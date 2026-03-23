<?php

namespace App\Tests\Unit\Service\Hf\Atelier\Dit\Soumission\Ors;

use App\Constants\Hf\Atelier\Dit\Soumission\Ors\StatutOrConstant;
use App\Entity\Admin\AgenceService\Agence;
use App\Entity\Admin\AgenceService\Service;
use App\Entity\Hf\Atelier\Dit\Dit;
use App\Model\Hf\Atelier\Dit\Soumission\Ors\OrsModel;
use App\Repository\Hf\Atelier\Dit\DitRepository;
use App\Service\Hf\Atelier\Dit\Soumission\Ors\OrsBlockingConditionService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class OrsBlockingConditionServiceTest extends TestCase
{
    /** @var OrsBlockingConditionService */
    private $service;

    /** @var LoggerInterface&MockObject */
    private $logger;

    /** @var OrsModel&MockObject */
    private $orsModel;

    /** @var DitRepository&MockObject */
    private $ditRepository;

    protected function setUp(): void
    {
        $this->logger        = $this->createMock(LoggerInterface::class);
        $this->orsModel      = $this->createMock(OrsModel::class);
        $this->ditRepository = $this->createMock(DitRepository::class);

        $this->service = new OrsBlockingConditionService(
            $this->logger,
            $this->orsModel,
            $this->ditRepository
        );
    }

    // -----------------------------------------------------------------------
    // checkBlockingConditionsDepuisListe – cas bloquants AVANT appel IPS
    // -----------------------------------------------------------------------

    /**
     * @test
     */
    public function blocage_si_numero_or_est_vide(): void
    {
        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '');

        $this->assertStringContainsString('numéro OR est manquant', $result);
    }

    /**
     * @test
     */
    public function blocage_si_numero_or_est_null(): void
    {
        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', null);

        $this->assertStringContainsString('numéro OR est manquant', $result);
    }

    // -----------------------------------------------------------------------
    // Cas bloquants retournés par IPS (OrsModel)
    // -----------------------------------------------------------------------

    /**
     * @test
     */
    public function blocage_si_info_ors_ips_est_vide(): void
    {
        $this->orsModel
            ->method('getInfoOrs')
            ->willReturn([]);

        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '12345');

        $this->assertStringContainsString("n'existe pas ou différent", $result);
    }

    /**
     * @test
     */
    public function blocage_si_une_intervention_sans_date_planning(): void
    {
        $this->orsModel
            ->method('getInfoOrs')
            ->willReturn([
                $this->buildInfoOrRow(['date_planning_existe' => 1]),
                $this->buildInfoOrRow(['date_planning_existe' => 0]), // ← pas de date planning
            ]);

        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '12345');

        $this->assertStringContainsString('non planifiées', $result);
    }

    /**
     * @test
     * @dataProvider positionsBloquantesProvider
     */
    public function blocage_si_position_or_est_bloquante(string $position): void
    {
        $this->orsModel
            ->method('getInfoOrs')
            ->willReturn([
                $this->buildInfoOrRow(['date_planning_existe' => 1, 'position' => $position]),
            ]);

        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '12345');

        $this->assertStringContainsString("parmis 'FC', 'FE', 'CP', 'ST'", $result);
    }

    /**
     * @return array<string, array<string>>
     */
    public function positionsBloquantesProvider(): array
    {
        return [
            'position FC' => ['FC'],
            'position FE' => ['FE'],
            'position CP' => ['CP'],
            'position ST' => ['ST'],
        ];
    }

    /**
     * @test
     */
    public function blocage_si_reference_client_est_vide(): void
    {
        $this->orsModel
            ->method('getInfoOrs')
            ->willReturn([
                $this->buildInfoOrRow(['date_planning_existe' => 1, 'reference_client' => '']),
            ]);

        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '12345');

        $this->assertStringContainsString('référence client est vide', $result);
    }

    /**
     * @test
     */
    public function blocage_si_numero_client_nexiste_pas(): void
    {
        $this->orsModel
            ->method('getInfoOrs')
            ->willReturn([
                $this->buildInfoOrRow([
                    'date_planning_existe' => 1,
                    'reference_client'     => 'REF-CLIENT',
                    'numero_client_existe' => 0, // ← client introuvable
                ]),
            ]);

        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '12345');

        $this->assertStringContainsString('client rattaché', $result);
    }

    // -----------------------------------------------------------------------
    // Cas bloquants après lecture de la DIT (intranet)
    // -----------------------------------------------------------------------

    /**
     * @test
     */
    public function blocage_si_id_materiel_different(): void
    {
        $this->orsModel
            ->method('getInfoOrs')
            ->willReturn([
                $this->buildInfoOrRow([
                    'id_materiel'           => 999,
                    'code_agence_debiteur'  => '01',
                    'code_service_debiteur' => 'APP',
                ]),
            ]);

        // idMateriel=888 différent de 999
        $dit = $this->buildDitMock(888, '', '01', 'APP');

        $this->ditRepository
            ->method('findOneBy')
            ->willReturn($dit);

        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '12345');

        $this->assertStringContainsString('materiel de', $result);
    }

    /**
     * @test
     * @dataProvider statutsBloquantsProvider
     */
    public function blocage_si_statut_or_est_deja_en_cours_de_validation(string $statut): void
    {
        $this->orsModel
            ->method('getInfoOrs')
            ->willReturn([
                $this->buildInfoOrRow([
                    'id_materiel'           => 100,
                    'code_agence_debiteur'  => '01',
                    'code_service_debiteur' => 'APP',
                ]),
            ]);

        $dit = $this->buildDitMock(100, $statut, '01', 'APP');

        $this->ditRepository
            ->method('findOneBy')
            ->willReturn($dit);

        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '12345');

        $this->assertStringContainsString('en cours de validation', $result);
    }

    /**
     * @return array<string, array<string>>
     */
    public function statutsBloquantsProvider(): array
    {
        return [
            'Soumis à validation'              => [StatutOrConstant::SOUMIS_A_VALIDATION],
            'Validé'                           => [StatutOrConstant::VALIDE],
            'Refusé'                           => [StatutOrConstant::REFUSE],
            'Modification demandée par client' => [StatutOrConstant::MODIFICATION_DEMANDE_PAR_CLIENT],
            'Modification demandée par CA'     => [StatutOrConstant::MODIFICATION_DEMANDE_PAR_CA],
            'Modification demandée par DT'     => [StatutOrConstant::MODIFICATION_DEMANDE_PAR_DT],
        ];
    }

    /**
     * @test
     */
    public function blocage_si_agence_debiteur_or_different_de_dit(): void
    {
        $this->orsModel
            ->method('getInfoOrs')
            ->willReturn([
                $this->buildInfoOrRow([
                    'id_materiel'           => 100,
                    'code_agence_debiteur'  => '80', // ← différent du DIT
                    'code_service_debiteur' => 'APP',
                ]),
            ]);

        // DIT avec agence '01', différente de '80'
        $dit = $this->buildDitMock(100, '', '01', 'APP');

        $this->ditRepository
            ->method('findOneBy')
            ->willReturn($dit);

        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '12345');

        $this->assertStringContainsString('agence et service debiteur', $result);
    }

    /**
     * @test
     */
    public function blocage_si_service_debiteur_different_pour_plusieurs_interventions(): void
    {
        $this->orsModel
            ->method('getInfoOrs')
            ->willReturn([
                $this->buildInfoOrRow([
                    'id_materiel'           => 100,
                    'code_agence_debiteur'  => '01',
                    'code_service_debiteur' => 'APP',
                ]),
                $this->buildInfoOrRow([
                    'id_materiel'           => 100,
                    'code_agence_debiteur'  => '80',  // ← agence différente sur 2e intervention
                    'code_service_debiteur' => 'MEC',
                ]),
            ]);

        $dit = $this->buildDitMock(100, '', '01', 'APP');

        $this->ditRepository
            ->method('findOneBy')
            ->willReturn($dit);

        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '12345');

        $this->assertStringContainsString('plusieurs service débiteur', $result);
    }

    /**
     * @test
     */
    public function blocage_si_premiere_soumission_et_date_planning_inferieure_a_aujourd_hui(): void
    {
        $datePasse = date('Y-m-d', strtotime('-1 day')); // hier

        $this->orsModel
            ->method('getInfoOrs')
            ->willReturn([
                $this->buildInfoOrRow([
                    'id_materiel'           => 100,
                    'code_agence_debiteur'  => '01',
                    'code_service_debiteur' => 'APP',
                    'date_planning'         => $datePasse,
                ]),
            ]);

        // statutOr vide = première soumission
        $dit = $this->buildDitMock(100, '', '01', 'APP');

        $this->ditRepository
            ->method('findOneBy')
            ->willReturn($dit);

        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '12345');

        $this->assertStringContainsString('date planning est inférieur', $result);
    }

    // -----------------------------------------------------------------------
    // Cas non bloquant : tout est valide → retourne null
    // -----------------------------------------------------------------------

    /**
     * @test
     */
    public function retourne_null_si_toutes_les_conditions_sont_valides(): void
    {
        $dateFuture = date('Y-m-d', strtotime('+7 days')); // dans 7 jours

        $this->orsModel
            ->method('getInfoOrs')
            ->willReturn([
                $this->buildInfoOrRow([
                    'date_planning_existe'  => 1,
                    'position'             => 'OU',
                    'reference_client'     => 'REF-CLIENT',
                    'numero_client_existe' => 1,
                    'id_materiel'          => 100,
                    'code_agence_debiteur' => '01',
                    'code_service_debiteur' => 'APP',
                    'date_planning'        => $dateFuture,
                ]),
            ]);

        // statutOr vide = première soumission valide avec date future
        $dit = $this->buildDitMock(100, '', '01', 'APP');

        $this->ditRepository
            ->method('findOneBy')
            ->willReturn($dit);

        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '12345');

        $this->assertNull($result);
    }

    /**
     * @test
     *
     * Vérifie que le blocage « date_planning < aujourd'hui » ne s'applique PAS
     * lors d'une re-soumission (statutOr non vide). Dans ce cas c'est le blocage
     * « en cours de validation » qui prend le dessus.
     */
    public function blocage_statut_or_prime_sur_blocage_date_planning_en_resoumission(): void
    {
        $datePasse = date('Y-m-d', strtotime('-1 day'));

        $this->orsModel
            ->method('getInfoOrs')
            ->willReturn([
                $this->buildInfoOrRow([
                    'id_materiel'           => 100,
                    'code_agence_debiteur'  => '01',
                    'code_service_debiteur' => 'APP',
                    'date_planning'         => $datePasse,
                ]),
            ]);

        // re-soumission avec statut bloquant
        $dit = $this->buildDitMock(100, StatutOrConstant::MODIFICATION_DEMANDE_PAR_CLIENT, '01', 'APP');

        $this->ditRepository
            ->method('findOneBy')
            ->willReturn($dit);

        $result = $this->service->checkBlockingConditionsDepuisListe('DIT-001', '12345');

        // Le blocage statut arrive AVANT le blocage date_planning dans le code
        $this->assertStringContainsString('en cours de validation', $result);
        $this->assertStringNotContainsString('date planning', $result);
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    /**
     * Construit un tableau de données IPS avec des valeurs par défaut valides,
     * surchargées par $overrides.
     *
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function buildInfoOrRow(array $overrides = []): array
    {
        return array_merge([
            'date_planning_existe'  => 1,
            'position'              => 'OU',
            'reference_client'      => 'REF-CLIENT',
            'numero_client_existe'  => 1,
            'id_materiel'           => 100,
            'code_agence_debiteur'  => '01',
            'code_service_debiteur' => 'APP',
            'date_planning'         => date('Y-m-d', strtotime('+7 days')),
        ], $overrides);
    }

    /**
     * Construit un mock de l'entité Dit avec les valeurs nécessaires.
     * Compatible PHP 7.4 (pas de paramètres nommés).
     *
     * @return Dit&MockObject
     */
    private function buildDitMock(
        int    $idMateriel,
        string $statutOr,
        string $codeAgence,
        string $codeService
    ): Dit {
        /** @var Agence&MockObject $agence */
        $agence = $this->createMock(Agence::class);
        $agence->method('getCode')->willReturn($codeAgence);

        /** @var Service&MockObject $service */
        $service = $this->createMock(Service::class);
        $service->method('getCode')->willReturn($codeService);

        /** @var Dit&MockObject $dit */
        $dit = $this->createMock(Dit::class);
        $dit->method('getIdMateriel')->willReturn($idMateriel);
        $dit->method('getStatutOr')->willReturn($statutOr);
        $dit->method('getAgenceDebiteurId')->willReturn($agence);
        $dit->method('getServiceDebiteur')->willReturn($service);

        return $dit;
    }
}
