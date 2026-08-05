<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\Mock\MockSecurity;
use Config\Services;

/**
 * Test fonctionnel de Formateur\Dashboard::modifierProfil().
 *
 * Structure identique à EtudiantModifierProfilTest, mais la route est
 * /formateur/profil/modifier et le filtre vérifie role === 'Formateur'.
 * Confirme que les deux implémentations dupliquées se comportent de la même façon.
 *
 * @internal
 */
final class FormateurModifierProfilTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

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

        $mock = new MockSecurity(config('Security'));
        Services::injectMock('security', $mock);
        $this->csrfToken = $mock->getHash();

        $this->originalEmail = 'formateur.test.' . uniqid() . '@nexus.dev';

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
     * Cas valide : données correctes → le profil formateur est mis à jour en BDD.
     */
    public function testModifierProfilAvecDonneesValides(): void
    {
        // ARRANGE — session simulant un formateur connecté
        $session = [
            'isLoggedIn' => true,
            'role'       => 'Formateur',
            'id_compte'  => $this->testIdCompte,
        ];

        // ACT
        $response = $this->withSession($session)->post('formateur/profil/modifier', [
            'csrf_test_name' => $this->csrfToken,
            'nom'            => 'NouveauNom',
            'prenom'         => 'NouveauPrenom',
            'email'          => $this->originalEmail,
        ]);

        // ASSERT — redirection vers profil formateur
        $response->assertRedirectTo(site_url('formateur/profil'));

        // ASSERT — mise à jour confirmée en BDD
        $this->seeInDatabase('comptes', [
            'id_compte' => $this->testIdCompte,
            'nom'       => 'NouveauNom',
        ]);
    }

    /**
     * Cas invalide : email mal formé → validation échoue, BDD non modifiée.
     */
    public function testModifierProfilAvecEmailMalForme(): void
    {
        // ARRANGE
        $session = [
            'isLoggedIn' => true,
            'role'       => 'Formateur',
            'id_compte'  => $this->testIdCompte,
        ];

        // ACT — POST avec email syntaxiquement invalide
        $response = $this->withSession($session)->post('formateur/profil/modifier', [
            'csrf_test_name' => $this->csrfToken,
            'nom'            => 'NouveauNom',
            'prenom'         => 'NouveauPrenom',
            'email'          => 'pas-un-email', // invalide → règle 'valid_email'
        ]);

        // ASSERT — redirection (flashdata 'errors' présent côté session)
        $response->assertRedirectTo(site_url('formateur/profil'));

        // ASSERT — la BDD n'a pas été modifiée
        $this->seeInDatabase('comptes', [
            'id_compte' => $this->testIdCompte,
            'nom'       => 'AncienNom',
        ]);
    }
}
