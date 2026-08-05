<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Teste la fonction generate_structured_com() du payement_helper.
 *
 * C'est un test unitaire pur : aucune connexion à la base de données,
 * aucun seeder, aucun DatabaseTestTrait. La fonction est mathématique,
 * elle n'a besoin que de ses deux arguments entiers.
 *
 * @internal
 */
final class GenerateStructuredComTest extends CIUnitTestCase
{
    /**
     * Charge le helper une seule fois pour toute la classe, avant le premier test.
     * On étend le setUpBeforeClass du parent qui charge déjà 'url' et 'test'.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        helper('payement');
    }

    // ---------------------------------------------------------------
    // Tests
    // ---------------------------------------------------------------

    /**
     * Test 1 — Cas normal : la clé de contrôle est le reste de la division par 97.
     *
     * Valeurs choisies : id_compte=1, id_session=1
     *   reference = "001" . "0000001" = "0010000001"  → entier : 10 000 001
     *   97 × 103 092 = 9 999 924
     *   10 000 001 − 9 999 924 = 77  → check = 77
     *   full = "001000000177"
     *   résultat : +++001/0000/00177+++
     */
    public function testCasNormal(): void
    {
        // ARRANGE
        $idCompte  = 1;
        $idSession = 1;

        // ACT
        $result = generate_structured_com($idCompte, $idSession);

        // ASSERT — format correct et clé de contrôle = 77
        $this->assertSame('+++001/0000/00177+++', $result);
    }

    /**
     * Test 2 — Cas particulier : reste = 0 → clé devient 97, jamais "00".
     *
     * Valeurs choisies : id_compte=1, id_session=21
     *   reference = "001" . "0000021" = "0010000021"  → entier : 10 000 021
     *   10^7 mod 97 = 76, donc id_session = 97 − 76 = 21 rend la valeur divisible par 97
     *   97 × 103 093 = 10 000 021  → reste = 0  → règle spéciale : check = 97
     *   full = "001000002197"
     *   résultat : +++001/0000/02197+++
     */
    public function testCleControleNeJamaisEtre00(): void
    {
        // ARRANGE — paire choisie mathématiquement pour que 10 000 021 % 97 === 0
        $idCompte  = 1;
        $idSession = 21;

        // ACT
        $result = generate_structured_com($idCompte, $idSession);

        // ASSERT — la clé affichée doit être "97", pas "00"
        $this->assertSame('+++001/0000/02197+++', $result);
        $this->assertStringNotContainsString('/02100+++', $result); // garde-fou explicite
    }
}
