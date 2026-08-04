# MVC — Modèle, Contrôleur, Vue

> Explication simple avec des exemples tirés du projet NEXUS.

---

## L'idée de base

Imagine que tu commandes une pizza :

| Rôle | Dans la pizzeria | Dans NEXUS |
|------|-----------------|------------|
| **Contrôleur** | Le serveur | Reçoit ta demande, coordonne |
| **Modèle** | La cuisine | Va chercher/modifier les données en BDD |
| **Vue** | L'assiette | Affiche le résultat à l'écran |

Le serveur **ne cuisine pas** et ne présente pas lui-même l'assiette.
La cuisine **ne parle pas** au client.
L'assiette **ne sait pas** comment la pizza a été faite.

---

## Le Modèle — il parle à la base de données

**Fichier : `app/Models/InscriptionModel.php`**

Le modèle contient toutes les requêtes SQL. Il ne sait pas qui a demandé les données ni comment elles seront affichées. Son seul rôle : **lire et écrire en BDD**.

```php
// Exemple : récupérer toutes les inscriptions d'un étudiant
public function getByCompte(int $id_compte): array
{
    return $this->db->query("
        SELECT i.*, s.date_debut, s.date_fin, f.titre
        FROM inscriptions i
        JOIN sessions s   ON i.id_session = s.id_session
        JOIN formations f ON s.id_formation = f.id_formation
        WHERE i.id_compte = $id_compte
    ")->getResultArray();
}
```

Ce que fait ce code :
- Il reçoit un `id_compte` (ex: 3)
- Il fait un `SELECT` en joinant 3 tables
- Il retourne un tableau PHP avec les résultats

> Le modèle ne sait pas si c'est un étudiant ou un formateur qui a demandé,
> et il ne sait pas comment les données seront affichées.

---

## Le Contrôleur — il orchestre

**Fichier : `app/Controllers/Etudiant/Dashboard.php`**

Le contrôleur reçoit la requête HTTP (clic sur un bouton, accès à une URL),
appelle le modèle pour obtenir les données, puis envoie ces données à la vue.

```php
// Exemple : l'étudiant clique sur "S'inscrire" à une session
public function inscrire(int $id_session)
{
    $id_compte = (int) session()->get('id_compte');

    // 1. Vérifier si déjà inscrit (appel modèle)
    if ($this->inscriptionModel->isAlreadyInscrit($id_compte, $id_session)) {
        session()->setFlashdata('error', 'Vous êtes déjà inscrit à cette session.');
        return redirect()->to(site_url('etudiant/catalogue'));
    }

    // 2. Vérifier les places disponibles (appel modèle)
    $session = $this->sessionModel->find($id_session);
    if ($this->inscriptionModel->countInscriptions($id_session) >= $session['place_max']) {
        session()->setFlashdata('error', 'Il n\'y a plus de places disponibles.');
        return redirect()->to(site_url('etudiant/catalogue'));
    }

    // 3. Insérer l'inscription (appel modèle)
    $this->inscriptionModel->insert([
        'id_compte'        => $id_compte,
        'id_session'       => $id_session,
        'date_inscription' => date('Y-m-d H:i:s'),
        'etat_parcours'    => 'Inscrit',
    ]);

    // 4. Rediriger vers la vue "mes inscriptions"
    session()->setFlashdata('success', 'Inscription réussie !');
    return redirect()->to(site_url('etudiant/mes-inscriptions'));
}
```

Ce que fait ce code :
1. Récupère l'id de l'étudiant connecté (depuis la session)
2. Pose des questions au modèle (déjà inscrit ? places dispo ?)
3. Demande au modèle d'insérer l'inscription
4. Redirige l'utilisateur (pas d'affichage direct — c'est la vue qui affiche)

> Le contrôleur ne fait **jamais** de SQL directement,
> et il ne contient **jamais** de HTML.

---

## La Vue — elle affiche

**Fichier : `app/Views/etudiant/catalogue.php`** (extrait)

La vue reçoit les données du contrôleur et les affiche en HTML.
Elle ne fait aucun calcul complexe, aucune requête SQL.

```php
// Le contrôleur lui a passé $sessions — elle boucle dessus et affiche
<?php foreach ($sessions as $s): ?>
<tr>
    <td><?= esc($s['titre']) ?></td>
    <td><?= esc($s['date_debut']) ?></td>
    <td>
        <?php if ($s['deja_inscrit']): ?>
            <span class="badge bg-secondary">Déjà inscrit</span>
        <?php else: ?>
            <form method="POST" action="<?= site_url($base_route . '/inscrire/' . $s['id_session']) ?>">
                <button type="submit" class="btn btn-sm btn-nexus">S'inscrire</button>
            </form>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
```

Ce que fait ce code :
- Boucle sur le tableau `$sessions` fourni par le contrôleur
- Affiche chaque session en HTML
- Affiche un badge "Déjà inscrit" ou un bouton selon les données

> La vue ne fait **jamais** de requête SQL.
> Elle utilise `esc()` pour sécuriser l'affichage (anti-XSS).

---

## Le flux complet — exemple concret

Quand un étudiant va sur `etudiant/catalogue` :

```
1. Le navigateur envoie GET /etudiant/catalogue
         ↓
2. Routes.php → Etudiant\Dashboard::catalogue()
         ↓
3. Le contrôleur appelle SessionModel::getSessionsOuvertes($id_compte)
         ↓
4. Le modèle fait le SELECT en BDD et retourne un tableau PHP
         ↓
5. Le contrôleur passe ce tableau à la vue :
   return view('etudiant/catalogue', ['sessions' => $sessions]);
         ↓
6. La vue boucle sur $sessions et génère le HTML
         ↓
7. Le navigateur affiche la page
```

---

## Résumé en une phrase chacun

| Composant | Résumé |
|-----------|--------|
| **Modèle** | "Je parle à la BDD, je retourne des données" |
| **Contrôleur** | "Je reçois les requêtes, j'appelle le modèle, j'envoie à la vue" |
| **Vue** | "Je reçois des données et je les affiche en HTML" |
