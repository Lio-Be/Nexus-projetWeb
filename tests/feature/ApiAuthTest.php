<?php

use App\Filters\ApiThrottleFilter;
use App\Models\ApiTokenModel;
use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\Mock\MockCache;
use CodeIgniter\Throttle\Throttler;
use Config\Services;

/**
 * Tests fonctionnels de l'API REST authentifiée :
 *   POST /api/login            → délivre un token
 *   GET  /api/mes-inscriptions → protégé par le filtre api-auth
 *
 * Base nexus_test : la table api_tokens est créée par les migrations de l'app.
 *
 * @internal
 */
final class ApiAuthTest extends CIUnitTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    // Les migrations de l'app créent api_tokens dans nexus_test (sans toucher aux autres tables).
    protected $migrate   = true;
    protected $refresh   = false;
    protected $namespace = 'App';
    protected $DBGroup   = 'tests';

    private const MOT_DE_PASSE = 'motdepasse123';

    private int    $testIdCompte = 0;
    private string $testEmail    = '';
    private int    $idSession    = 0;

    // ---------------------------------------------------------------
    // Setup / Teardown
    // ---------------------------------------------------------------

    protected function setUp(): void
    {
        parent::setUp();

        // Throttler sur un cache en mémoire : le compteur repart de zéro à chaque test.
        Services::injectMock('throttler', new Throttler(new MockCache()));

        $this->testEmail = 'api.test.' . uniqid() . '@nexus.dev';

        $this->db->table('comptes')->insert([
            'nom'      => 'Api',
            'prenom'   => 'Test',
            'email'    => $this->testEmail,
            'password' => password_hash(self::MOT_DE_PASSE, PASSWORD_DEFAULT),
        ]);
        $this->testIdCompte = (int) $this->db->insertID();

        $this->idSession = (int) $this->db->table('sessions')->select('id_session')->get()->getRow()->id_session;
    }

    protected function tearDown(): void
    {
        if ($this->testIdCompte > 0) {
            $this->db->table('api_tokens')->where('id_compte', $this->testIdCompte)->delete();
            $this->db->table('inscriptions')->where('id_compte', $this->testIdCompte)->delete();
            $this->db->table('comptes')->where('id_compte', $this->testIdCompte)->delete();
        }

        parent::tearDown();
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    private function login(string $password): \CodeIgniter\Test\TestResponse
    {
        return $this->withBodyFormat('json')->post('api/login', [
            'email'    => $this->testEmail,
            'password' => $password,
        ]);
    }

    private function tokenValide(): string
    {
        return (new ApiTokenModel())->creerToken($this->testIdCompte)['token'];
    }

    // ---------------------------------------------------------------
    // POST /api/login
    // ---------------------------------------------------------------

    /**
     * Identifiants corrects envoyés en JSON → 200 + token.
     * En base, seule l'empreinte SHA-256 est stockée, jamais le token en clair.
     */
    public function testLoginJsonValideRetourneUnTokenStockeHashe(): void
    {
        $response = $this->login(self::MOT_DE_PASSE);

        $response->assertStatus(200);
        $token = json_decode($response->getJSON(), true)['token'];
        $this->assertMatchesRegularExpression('/^[0-9a-f]{64}$/', $token);

        $this->seeInDatabase('api_tokens', [
            'id_compte'  => $this->testIdCompte,
            'token_hash' => hash('sha256', $token),
        ]);
        $this->dontSeeInDatabase('api_tokens', ['token_hash' => $token]);
    }

    /**
     * Mauvais mot de passe → 401 et aucun token créé.
     */
    public function testLoginMauvaisMotDePasseRetourne401(): void
    {
        $response = $this->login('mauvais');

        $response->assertStatus(401);
        $this->dontSeeInDatabase('api_tokens', ['id_compte' => $this->testIdCompte]);
    }

    /**
     * Au-delà de MAX_TENTATIVES requêtes rapprochées, le login est bloqué (429).
     */
    public function testLoginBloqueApresTropDeTentatives(): void
    {
        for ($i = 0; $i < ApiThrottleFilter::MAX_TENTATIVES; $i++) {
            $this->login('mauvais')->assertStatus(401);
        }

        $this->login('mauvais')->assertStatus(429);
    }

    // ---------------------------------------------------------------
    // GET /api/mes-inscriptions (protégé)
    // ---------------------------------------------------------------

    public function testMesInscriptionsSansTokenRetourne401(): void
    {
        $this->get('api/mes-inscriptions')->assertStatus(401);
    }

    public function testMesInscriptionsAvecTokenInconnuRetourne401(): void
    {
        $this->withHeaders(['Authorization' => 'Bearer ' . str_repeat('a', 64)])
            ->get('api/mes-inscriptions')
            ->assertStatus(401);
    }

    public function testMesInscriptionsAvecTokenExpireRetourne401(): void
    {
        $token = $this->tokenValide();
        $this->db->table('api_tokens')
            ->where('id_compte', $this->testIdCompte)
            ->update(['date_expiration' => date('Y-m-d H:i:s', strtotime('-1 minute'))]);

        $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->get('api/mes-inscriptions')
            ->assertStatus(401);
    }

    /**
     * Token valide → 200, et uniquement les inscriptions du compte du token.
     */
    public function testMesInscriptionsAvecTokenValideRetourneSesInscriptions(): void
    {
        $this->db->table('inscriptions')->insert([
            'id_compte'  => $this->testIdCompte,
            'id_session' => $this->idSession,
        ]);

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $this->tokenValide()])
            ->get('api/mes-inscriptions');

        $response->assertStatus(200);
        $data = json_decode($response->getJSON(), true)['data'];

        $this->assertCount(1, $data);
        $this->assertSame($this->testIdCompte, (int) $data[0]['id_compte']);
        $this->assertSame($this->idSession, (int) $data[0]['id_session']);
    }
}
