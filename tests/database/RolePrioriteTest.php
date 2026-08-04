<?php

use App\Models\UserModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Vérifie que UserModel::findByEmail() applique correctement la priorité des rôles.
 * Règle métier : Admin > Formateur > Etudiant
 *
 * Prérequis : la base nexus_test doit contenir les mêmes tables que nexus_formations,
 * avec les 3 lignes de référence dans la table `roles` (Admin, Formateur, Etudiant).
 *
 * @internal
 */
final class RolePrioriteTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    // Pas de migrations dans ce projet — les tables existent déjà dans nexus_test.
    protected $migrate = false;
    protected $refresh = false;

    // Groupe de connexion défini dans phpunit.xml.dist (MySQL nexus_test).
    protected $DBGroup = 'tests';

    // Stocke l'id_compte et l'email du compte créé pour le test en cours.
    private int    $testIdCompte = 0;
    private string $testEmail    = '';

    // ---------------------------------------------------------------
    // Helpers partagés (non des tests)
    // ---------------------------------------------------------------

    /**
     * Insère un compte fictif avec un email unique et mémorise son id_compte.
     */
    private function creerCompteTest(): void
    {
        // Email unique à chaque appel pour éviter tout conflit de contrainte UNIQUE.
        $this->testEmail = 'test.priorite.' . uniqid() . '@nexus.dev';

        $this->db->table('comptes')->insert([
            'nom'      => 'TestRole',
            'prenom'   => 'PHPUnit',
            'email'    => $this->testEmail,
            'password' => password_hash('motdepasse123', PASSWORD_DEFAULT),
        ]);

        $this->testIdCompte = (int) $this->db->insertID();
    }

    /**
     * Retourne l'id_role correspondant au libellé donné dans la table roles.
     * Lève une erreur si le rôle n'existe pas dans nexus_test.
     */
    private function getIdRole(string $libelle): int
    {
        $row = $this->db->table('roles')
            ->where('libelle', $libelle)
            ->get()
            ->getRow();

        $this->assertNotNull($row, "Le rôle '$libelle' est introuvable dans nexus_test.roles");

        return (int) $row->id_role;
    }

    /**
     * Assigne un ou plusieurs rôles au compte de test via compte_roles.
     *
     * @param list<string> $libelles
     */
    private function assignerRoles(array $libelles): void
    {
        foreach ($libelles as $libelle) {
            $this->db->table('compte_roles')->insert([
                'id_compte'        => $this->testIdCompte,
                'id_role'          => $this->getIdRole($libelle),
                'date_attribution' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Nettoyage après chaque test.
     * Ordre important : compte_roles doit être supprimé AVANT comptes (contrainte FK).
     */
    protected function tearDown(): void
    {
        if ($this->testIdCompte > 0) {
            // Supprimer d'abord la table enfant (référence FK vers comptes)
            $this->db->table('compte_roles')
                ->where('id_compte', $this->testIdCompte)
                ->delete();

            // Puis la table parente
            $this->db->table('comptes')
                ->where('id_compte', $this->testIdCompte)
                ->delete();

            $this->testIdCompte = 0;
        }

        parent::tearDown();
    }

    // ---------------------------------------------------------------
    // Tests
    // ---------------------------------------------------------------

    /**
     * Test 1 — Formateur doit être prioritaire sur Étudiant.
     *
     * Situation réelle : un étudiant est promu Formateur.
     * Il possède donc les deux rôles simultanément dans compte_roles.
     * findByEmail() doit retourner 'Formateur', jamais retomber sur 'Etudiant'.
     */
    public function testFormateurPrioritaireSurEtudiant(): void
    {
        // ARRANGE — créer un compte avec les rôles Formateur ET Etudiant
        $this->creerCompteTest();
        $this->assignerRoles(['Formateur', 'Etudiant']);

        // ACT — appeler la méthode ciblée, comme le fait Auth::attemptLogin()
        $user = (new UserModel())->findByEmail($this->testEmail);

        // ASSERT — Formateur doit l'emporter sur Etudiant
        $this->assertNotNull($user, 'findByEmail() ne doit pas retourner null');
        $this->assertSame('Formateur', $user['role']);
    }

    /**
     * Test 2 — Admin doit être prioritaire sur Formateur.
     *
     * Situation réelle : un formateur est promu administrateur.
     * Il possède donc les deux rôles simultanément dans compte_roles.
     * findByEmail() doit retourner 'Admin', jamais retomber sur 'Formateur'.
     */
    public function testAdminPrioritaireSurFormateur(): void
    {
        // ARRANGE — créer un compte avec les rôles Admin ET Formateur
        $this->creerCompteTest();
        $this->assignerRoles(['Admin', 'Formateur']);

        // ACT
        $user = (new UserModel())->findByEmail($this->testEmail);

        // ASSERT — Admin doit l'emporter sur Formateur
        $this->assertNotNull($user, 'findByEmail() ne doit pas retourner null');
        $this->assertSame('Admin', $user['role']);
    }
}
