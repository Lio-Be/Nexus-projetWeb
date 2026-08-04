# InscriptionModel — Clé composite expliquée

> La table `inscriptions` est particulière : elle n'a pas de colonne `id` classique.
> Elle utilise une **clé composite** — deux colonnes combinées forment l'identifiant unique.

---

## 1. Pourquoi une clé composite ?

Dans une table classique, chaque ligne a un `id` unique :

```
formations
-----------
id_formation | titre
1            | PHP avancé
2            | Unity 3D
```

Dans `inscriptions`, un étudiant peut s'inscrire à plusieurs sessions,
et une session peut avoir plusieurs étudiants inscrits.
Ce qui identifie **une** inscription de façon unique, c'est la combinaison :
**"cet étudiant" + "cette session"**.

```
inscriptions
-----------------------------------
id_compte | id_session | etat_parcours | date_inscription
3         | 5          | Inscrit        | 2026-01-15 10:00:00
3         | 8          | En_cours       | 2026-02-01 14:30:00
7         | 5          | Inscrit        | 2026-01-20 09:15:00
```

- Le compte 3 est inscrit aux sessions 5 et 8 → 2 lignes différentes ✅
- La session 5 a les comptes 3 et 7 inscrits → 2 lignes différentes ✅
- Le couple (3, 5) est unique → impossible d'avoir deux fois la même ligne ✅

> Il ne peut pas y avoir deux fois `id_compte = 3` ET `id_session = 5`.
> C'est la base de données qui l'interdit au niveau de la contrainte de clé primaire.

---

## 2. Le problème avec CI4 et les clés composites

CodeIgniter 4 est conçu pour les tables avec **un seul** `id` en clé primaire.
Ses méthodes automatiques (`find()`, `update()`, `delete()`) ne fonctionnent pas
avec une clé composite.

```php
// ❌ Ça ne fonctionne PAS pour les clés composites
$this->inscriptionModel->find(3);           // CI4 cherche id = 3 → inutile ici
$this->inscriptionModel->delete(3);         // CI4 supprime où id = 3 → mauvais
$this->inscriptionModel->update(3, $data);  // CI4 met à jour où id = 3 → mauvais
```

### La solution dans NEXUS : méthodes custom

Le modèle déclare `id_compte` comme clé primaire (pour satisfaire CI4),
mais toutes les opérations réelles passent par des méthodes qui utilisent
**les deux colonnes** :

```php
// Dans InscriptionModel.php
protected $primaryKey = 'id_compte'; // déclaration obligatoire pour CI4

// ✅ Méthode custom pour update
public function updateByKeys(int $id_compte, int $id_session, array $data): void
{
    $this->db->table($this->table)
             ->where('id_compte', $id_compte)
             ->where('id_session', $id_session)
             ->update($data);
}

// ✅ Méthode custom pour delete
public function deleteByKeys(int $id_compte, int $id_session): void
{
    $this->db->table($this->table)
             ->where('id_compte', $id_compte)
             ->where('id_session', $id_session)
             ->delete();
}
```

---

## 3. Toutes les méthodes du modèle expliquées

### `getAllWithFormation()` — liste complète pour l'admin

```php
public function getAllWithFormation(): array
{
    return $this->db->query("
        SELECT i.*, c.nom, c.prenom, c.email,
               s.date_debut, s.date_fin, s.place_max, s.statut,
               f.titre
        FROM inscriptions i
        JOIN comptes    c ON i.id_compte   = c.id_compte
        JOIN sessions   s ON i.id_session  = s.id_session
        JOIN formations f ON s.id_formation = f.id_formation
        ORDER BY i.date_inscription DESC
    ")->getResultArray();
}
```

Utilisée par : `Admin\InscriptionsController::index()`
Retourne : toutes les inscriptions avec le nom de l'étudiant, la session et la formation

---

### `getByCompte(int $id_compte)` — inscriptions d'un étudiant

```php
public function getByCompte(int $id_compte): array
{
    return $this->db->query("
        SELECT i.*, s.date_debut, s.date_fin, s.statut, f.titre
        FROM inscriptions i
        JOIN sessions   s ON i.id_session   = s.id_session
        JOIN formations f ON s.id_formation = f.id_formation
        WHERE i.id_compte = $id_compte
        ORDER BY i.date_inscription DESC
    ")->getResultArray();
}
```

Utilisée par : `Etudiant\Dashboard::mesInscriptions()` et `Formateur\Dashboard::mesInscriptions()`
Retourne : les inscriptions d'un seul compte, avec les détails de session et formation

---

### `getWithDetails(int $id_compte, int $id_session)` — une inscription précise

```php
public function getWithDetails(int $id_compte, int $id_session): ?array
{
    return $this->db->query("
        SELECT i.*, c.nom, c.prenom, c.email,
               s.date_debut, s.date_fin, s.place_max, s.statut,
               f.titre
        FROM inscriptions i
        JOIN comptes    c ON i.id_compte   = c.id_compte
        JOIN sessions   s ON i.id_session  = s.id_session
        JOIN formations f ON s.id_formation = f.id_formation
        WHERE i.id_compte = $id_compte AND i.id_session = $id_session
    ")->getRowArray();
}
```

Utilisée par : `Admin\InscriptionsController::edit()` et `approuverPaiement()`
Retourne : **une seule ligne** (ou null si introuvable) — `getRowArray()` au lieu de `getResultArray()`

---

### `isAlreadyInscrit(int $id_compte, int $id_session)` — vérification doublon

```php
public function isAlreadyInscrit(int $id_compte, int $id_session): bool
{
    $result = $this->db->query("
        SELECT COUNT(*) as count
        FROM inscriptions
        WHERE id_compte = $id_compte AND id_session = $id_session
    ")->getRowArray();

    return $result['count'] > 0;
}
```

Utilisée par : `Etudiant\Dashboard::inscrire()` avant d'insérer
Retourne : `true` si l'inscription existe déjà, `false` sinon

Exemple d'utilisation dans le contrôleur :
```php
if ($this->inscriptionModel->isAlreadyInscrit($id_compte, $id_session)) {
    session()->setFlashdata('error', 'Vous êtes déjà inscrit à cette session.');
    return redirect()->to(site_url('etudiant/catalogue'));
}
```

---

### `countInscriptions(int $id_session)` — places prises

```php
public function countInscriptions(int $id_session): int
{
    $result = $this->db->query("
        SELECT COUNT(*) as count
        FROM inscriptions
        WHERE id_session = $id_session
    ")->getRowArray();

    return (int) $result['count'];
}
```

Utilisée par : `Etudiant\Dashboard::inscrire()` pour vérifier s'il reste des places
Retourne : le nombre d'inscrits pour une session

Exemple d'utilisation dans le contrôleur :
```php
$session = $this->sessionModel->find($id_session);

if ($this->inscriptionModel->countInscriptions($id_session) >= $session['place_max']) {
    session()->setFlashdata('error', 'Il n\'y a plus de places disponibles.');
    return redirect()->to(site_url('etudiant/catalogue'));
}
```

---

### `updateByKeys(int $id_compte, int $id_session, array $data)` — mise à jour

```php
public function updateByKeys(int $id_compte, int $id_session, array $data): void
{
    $this->db->table($this->table)
             ->where('id_compte', $id_compte)
             ->where('id_session', $id_session)
             ->update($data);
}
```

Utilisée pour : envoyer un paiement, approuver, refuser
Le paramètre `$data` contient uniquement les colonnes à modifier :

```php
// Étudiant envoie son paiement
$this->inscriptionModel->updateByKeys($id_compte, $id_session, [
    'date_envoi_paiement' => date('Y-m-d H:i:s'),
]);

// Admin approuve le paiement
$this->inscriptionModel->updateByKeys($id_compte, $id_session, [
    'date_validation_paiement' => date('Y-m-d H:i:s'),
]);

// Admin refuse le paiement
$this->inscriptionModel->updateByKeys($id_compte, $id_session, [
    'date_envoi_paiement'  => null,
    'raison_refus_paiement' => $this->request->getPost('raison'),
]);
```

---

### `deleteByKeys(int $id_compte, int $id_session)` — suppression

```php
public function deleteByKeys(int $id_compte, int $id_session): void
{
    $this->db->table($this->table)
             ->where('id_compte', $id_compte)
             ->where('id_session', $id_session)
             ->delete();
}
```

Utilisée par : `Admin\InscriptionsController::delete()`
Supprime **exactement une ligne** identifiée par les deux clés

---

## 4. Le flux d'une inscription de A à Z

```
1. Étudiant clique "S'inscrire" sur la session n°5
         ↓
2. POST etudiant/inscrire/5 → Etudiant\Dashboard::inscrire(5)
         ↓
3. isAlreadyInscrit(3, 5) ?  → false  ✅ (pas encore inscrit)
   countInscriptions(5) < place_max ? → true ✅ (places disponibles)
         ↓
4. INSERT INTO inscriptions (id_compte, id_session, date_inscription, etat_parcours)
               VALUES (3, 5, '2026-05-21 10:00:00', 'Inscrit')
         ↓
5. Étudiant voit la session dans "Mes inscriptions"
   date_envoi_paiement     = NULL  (pas encore payé)
   date_validation_paiement = NULL  (pas encore validé)
         ↓
6. Étudiant entre son IBAN → updateByKeys(3, 5, ['date_envoi_paiement' => now()])
         ↓
7. Admin voit "En attente" → approuve → updateByKeys(3, 5, ['date_validation_paiement' => now()])
         ↓
8. Inscription finalisée
```

---

## 5. Structure de la table en base de données

```sql
CREATE TABLE inscriptions (
    id_compte                  INT NOT NULL,
    id_session                 INT NOT NULL,
    etat_parcours              ENUM('Inscrit','En_cours','Termine','Abandonne') DEFAULT 'Inscrit',
    note_admin                 TEXT NULL,
    date_inscription           DATETIME NOT NULL,
    date_envoi_paiement        DATETIME NULL DEFAULT NULL,
    date_validation_paiement   DATETIME NULL DEFAULT NULL,
    raison_refus_paiement      TEXT NULL DEFAULT NULL,

    -- Clé primaire composite : la combinaison (id_compte, id_session) doit être unique
    PRIMARY KEY (id_compte, id_session),

    FOREIGN KEY (id_compte)  REFERENCES comptes(id_compte),
    FOREIGN KEY (id_session) REFERENCES sessions(id_session)
);
```

`DEFAULT NULL` sur les dates de paiement : elles sont vides à l'inscription
et remplies progressivement selon le flux de paiement.
