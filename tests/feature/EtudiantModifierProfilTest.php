<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\Mock\MockSecurity;
use Config\Services;

/**
 * Test fonctionnel de Etudiant\Dashboard::modifierProfil().
 *
 * Simule une vraie requête POST comme le ferait un navigateur, avec session,
 * filtre d'auth et base de données réels (nexus_test).
 * DatabaseTestTrait → prépare et nettoie le compte de test en BDD.
 * FeatureTestTrait  → envoie la requête HTTP via le router CI4.
 *
 * @internal
 */
final class EtudiantModifierProfilTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    // Pas de migrations dans ce projet — les tables existent dans nexus_test.
    protected $migrate = false;
    protected $refresh = false;
    protected $DBGroup = 'tests';

    private int    $testIdCompte  = 0;
    private string $originalEmail = '';
    private string $csrfToken     = '';

    // ---------------------------------------------------------------
    // Setup / Teardown
    // ---------------------------------------------------------------

    protected function setUp(): void
    {
        parent::setUp();

        // MockSecurity génère un hash réel (32 chars hex valides) via generateHash().
        // On le lit via getHash() — sans passer par $_COOKIE, que saveHashInCookie()
        // n'écrit jamais (elle passe par $response->setCookie(), pas par $_COOKIE).
        // Sans MockSecurity, un token arbitraire échoue le preg_match de isHashInCookie()
        // → Security génère un hash aléatoire inconnu → SecurityException.
        $mock = new MockSecurity(config('Security'));
        Services::injectMock('security', $mock);
        $this->csrfToken = $mock->getHash();

        // Arrange — créer un compte étudiant fictif avant chaque test.
        $this->originalEmail = 'etudiant.test.' . uniqid() . '@nexus.dev';

        $this->db->table('comptes')->insert([
            'nom'      => 'AncienNom',
            'prenom'   => 'AncienPrenom',
            'email'    => $this->originalEmail,
            'password' => password_hash('motdepasse123', PASSWORD_DEFAULT),
        ]);

        $this->testIdCompte = (int) $this->db->insertID();
    }

    protected function tearDown(): void
    {
        // Nettoyage — suppression du compte de test (pas de compte_roles ici).
        if ($this->testIdCompte > 0) {
            $this->db->table('comptes')
                ->where('id_compte', $this->testIdCompte)
                ->delete();
        }

        parent::tearDown();
    }

    // ---------------------------------------------------------------
    // Tests
    // ---------------------------------------------------------------

    /**
     * Cas valide : données correctes → le profil est mis à jour en base de données.
     *
     * Flux attendu :
     *   1. Le filtre EtudiantFilter laisse passer (session valide).
     *   2. La validation CI4 accepte les données.
     *   3. CompteModel::update() écrit les nouvelles valeurs.
     *   4. Le contrôleur redirige vers etudiant/profil avec message de succès.
     */
    public function testModifierProfilAvecDonneesValides(): void
    {
        // ARRANGE — session simulant un étudiant connecté avec son vrai id_compte
        $session = [
            'isLoggedIn' => true,
            'role'       => 'Etudiant',
            'id_compte'  => $this->testIdCompte,
        ];

        // ACT — POST vers la route protégée
        $response = $this->withSession($session)->post('etudiant/profil/modifier', [
            'csrf_test_name' => $this->csrfToken,
            'nom'            => 'NouveauNom',
            'prenom'         => 'NouveauPrenom',
            'email'          => $this->originalEmail, // même email → is_unique exclut ce compte
        ]);

        // ASSERT — redirection vers profil (comportement normal après succès)
        $response->assertRedirectTo(site_url('etudiant/profil'));

        // ASSERT — la base de données contient bien le nom mis à jour
        $this->seeInDatabase('comptes', [
            'id_compte' => $this->testIdCompte,
            'nom'       => 'NouveauNom',
        ]);
    }

    /**
     * Cas invalide : email vide → validation échoue, la base de données n'est pas modifiée.
     *
     * Flux attendu :
     *   1. Le filtre EtudiantFilter laisse passer.
     *   2. La validation échoue sur la règle 'required' de l'email.
     *   3. Le contrôleur stocke les erreurs en flashdata et redirige.
     *   4. CompteModel::update() n'est jamais appelé.
     */
    public function testModifierProfilAvecEmailVide(): void
    {
        // ARRANGE
        $session = [
            'isLoggedIn' => true,
            'role'       => 'Etudiant',
            'id_compte'  => $this->testIdCompte,
        ];

        // ACT — POST avec email délibérément vide
        $response = $this->withSession($session)->post('etudiant/profil/modifier', [
            'csrf_test_name' => $this->csrfToken,
            'nom'            => 'NouveauNom',
            'prenom'         => 'NouveauPrenom',
            'email'          => '', // invalide → doit déclencher l'erreur 'required'
        ]);

        // ASSERT — redirection (avec flashdata 'errors', pas 'success')
        $response->assertRedirectTo(site_url('etudiant/profil'));

        // ASSERT — le nom en base n'a PAS changé (update() n'a pas été appelé)
        $this->seeInDatabase('comptes', [
            'id_compte' => $this->testIdCompte,
            'nom'       => 'AncienNom',
        ]);
    }
}
