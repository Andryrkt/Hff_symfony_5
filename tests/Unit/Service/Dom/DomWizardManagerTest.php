<?php
// tests/Unit/Service/Dom/DomWizardManagerTest.php
namespace App\Tests\Unit\Service\Dom;

use App\Service\Dom\DomWizardManager;
use App\Dto\Dom\DomFirstFormData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

class DomWizardManagerTest extends TestCase
{
    private DomWizardManager $wizardManager;
    private SessionInterface $session;
    private RequestStack $requestStack;

    protected function setUp(): void
    {
        $this->session = $this->createMock(SessionInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);

        // Mock de la request avec session
        $request = $this->createMock(Request::class);
        $request->method('getSession')->willReturn($this->session);
        $this->requestStack->method('getSession')->willReturn($this->session);

        $this->wizardManager = new DomWizardManager($this->requestStack);
    }

    public function testSaveStep1DataSuccess(): void
    {
        // Arrange
        $dto = $this->createMockDto();

        $expectedData = [
            'agenceEmetteur' => 1,
            'serviceEmetteur' => 2,
            'sousTypeDocument' => 3,
            'salarie' => 'PERMANENT',
            'categorie' => 4,
            'matriculeNom' => 5,
            'matricule' => '12345',
            'nom' => null,
            'prenom' => null,
            'cin' => null,
            'user_id' => 6,
            'timestamp' => time()
        ];

        $this->session
            ->expects($this->once())
            ->method('set')
            ->with('dom_wizard_data', $this->callback(function ($data) use ($expectedData) {
                // Vérifier que les données sauvegardées sont correctes (sauf timestamp)
                unset($data['timestamp'], $expectedData['timestamp']);
                return $data === $expectedData;
            }));

        // Act
        $this->wizardManager->saveStep1Data($dto);

        // Assert - Le mock vérifie que set() a été appelé
        $this->assertTrue(true);
    }

    public function testHasStep1DataReturnsTrueWhenDataExists(): void
    {
        // Arrange
        $this->session
            ->method('has')
            ->with('dom_wizard_data')
            ->willReturn(true);

        $this->session
            ->method('get')
            ->with('dom_wizard_data')
            ->willReturn(['some' => 'data']);

        // Act & Assert
        $this->assertTrue($this->wizardManager->hasStep1Data());
    }

    public function testHasStep1DataReturnsFalseWhenNoData(): void
    {
        // Arrange
        $this->session
            ->method('has')
            ->with('dom_wizard_data')
            ->willReturn(false);

        // Act & Assert
        $this->assertFalse($this->wizardManager->hasStep1Data());
    }

    public function testGetStep1DataArrayReturnsNullWhenNoData(): void
    {
        // Arrange
        $this->session
            ->method('has')
            ->with('dom_wizard_data')
            ->willReturn(false);

        // Act & Assert
        $this->assertNull($this->wizardManager->getStep1DataArray());
    }

    public function testGetStep1DataArrayReturnsDataWhenExists(): void
    {
        // Arrange
        $expectedData = ['test' => 'data', 'timestamp' => time()];

        $this->session
            ->method('has')
            ->with('dom_wizard_data')
            ->willReturn(true);

        $this->session
            ->method('get')
            ->with('dom_wizard_data')
            ->willReturn($expectedData);

        // Act
        $result = $this->wizardManager->getStep1DataArray();

        // Assert
        $this->assertEquals($expectedData, $result);
    }

    public function testGetStep1DataArrayClearsExpiredData(): void
    {
        // Arrange - Données expirées (plus de 1h)
        $expiredData = ['test' => 'data', 'timestamp' => time() - 3700];

        $this->session
            ->method('has')
            ->with('dom_wizard_data')
            ->willReturn(true);

        $this->session
            ->method('get')
            ->with('dom_wizard_data')
            ->willReturn($expiredData);

        $this->session
            ->expects($this->once())
            ->method('remove')
            ->with('dom_wizard_data');

        // Act
        $result = $this->wizardManager->getStep1DataArray();

        // Assert
        $this->assertNull($result);
    }

    public function testClearRemovesSessionData(): void
    {
        // Arrange
        $this->session
            ->expects($this->once())
            ->method('remove')
            ->with('dom_wizard_data');

        // Act
        $this->wizardManager->clear();

        // Assert - Le mock vérifie que remove() a été appelé
        $this->assertTrue(true);
    }

    /**
     * @dataProvider transitionDataProvider
     */
    public function testValidateTransition(string $currentStep, string $targetStep, bool $expected): void
    {
        // Act
        $result = $this->wizardManager->validateTransition($currentStep, $targetStep);

        // Assert
        $this->assertEquals($expected, $result);
    }

    public function transitionDataProvider(): array
    {
        return [
            // Valid transitions
            ['step1', 'step2', true],
            ['step2', 'step3', true],
            ['step2', 'step1', true],
            ['step3', 'confirm', true],
            ['step3', 'step2', true],

            // Invalid transitions
            ['step1', 'step3', false],
            ['step1', 'confirm', false],
            ['step2', 'confirm', false],
            ['step3', 'step1', false],
            ['invalid', 'step1', false],
            ['step1', 'invalid', false],
        ];
    }

    private function createMockDto(): DomFirstFormData
    {
        $dto = $this->createMock(DomFirstFormData::class);

        // Mock des entités avec des IDs
        $agence = $this->createMock(\App\Entity\Dom\DomAgence::class);
        $agence->method('getId')->willReturn(1);

        $service = $this->createMock(\App\Entity\Dom\DomService::class);
        $service->method('getId')->willReturn(2);

        $sousType = $this->createMock(\App\Entity\Dom\DomSousTypeDocument::class);
        $sousType->method('getId')->willReturn(3);

        $categorie = $this->createMock(\App\Entity\Dom\DomCategorie::class);
        $categorie->method('getId')->willReturn(4);

        $personnel = $this->createMock(\App\Entity\Personnel::class);
        $personnel->method('getId')->willReturn(5);

        $user = $this->createMock(\App\Entity\User::class);
        $user->method('getId')->willReturn(6);

        $emetteur = $this->createMock(\App\Dto\Dom\DomEmetteurData::class);
        $emetteur->method('getAgenceEmetteur')->willReturn($agence);
        $emetteur->method('getServiceEmetteur')->willReturn($service);

        // Configuration du DTO
        $dto->method('getEmetteur')->willReturn($emetteur);
        $dto->method('getSousTypeDocument')->willReturn($sousType);
        $dto->method('getSalarie')->willReturn('PERMANENT');
        $dto->method('getCategorie')->willReturn($categorie);
        $dto->method('getMatriculeNom')->willReturn($personnel);
        $dto->method('getMatricule')->willReturn('12345');
        $dto->method('getNom')->willReturn(null);
        $dto->method('getPrenom')->willReturn(null);
        $dto->method('getCin')->willReturn(null);
        $dto->method('getUser')->willReturn($user);

        return $dto;
    }
}
