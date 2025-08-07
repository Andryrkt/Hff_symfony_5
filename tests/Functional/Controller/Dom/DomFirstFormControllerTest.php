<?php
// tests/Functional/Controller/Dom/DomFirstFormControllerTest.php
namespace App\Tests\Functional\Controller\Dom;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;
use App\Entity\Dom\DomAgence;
use App\Entity\Dom\DomService;
use App\Entity\Dom\DomSousTypeDocument;

class DomFirstFormControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function testDomFirstPageLoadsSuccessfully(): void
    {
        // Arrange - Se connecter avec un utilisateur
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        // Act - Accéder à la page du formulaire
        $crawler = $this->client->request('GET', '/dom/first');

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Demande d\'Ordre de Mission');
        $this->assertSelectorExists('form[id="dom_form1"]');
        $this->assertSelectorExists('button[type="submit"]');
    }

    public function testFormSubmissionWithValidData(): void
    {
        // Arrange
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        // Créer les entités nécessaires
        $agence = $this->createTestAgence();
        $service = $this->createTestService();
        $sousType = $this->createTestSousTypeDocument();

        $crawler = $this->client->request('GET', '/dom/first');

        // Act - Soumettre le formulaire avec des données valides
        $form = $crawler->selectButton('Suivant')->form();

        // Trouver les noms exacts des champs du formulaire
        $formName = $form->getName();

        $form["{$formName}[emetteur][agenceEmetteur]"] = $agence->getId();
        $form["{$formName}[emetteur][serviceEmetteur]"] = $service->getId();
        $form["{$formName}[sousTypeDocument]"] = $sousType->getId();
        $form["{$formName}[salarie]"] = 'TEMPORAIRE';
        $form["{$formName}[nom]"] = 'Test';
        $form["{$formName}[prenom]"] = 'User';
        $form["{$formName}[cin]"] = '123456789';

        $this->client->submit($form);

        // Assert
        $this->assertResponseRedirects();

        // Vérifier que les données sont sauvegardées en session
        $session = $this->client->getRequest()->getSession();
        $this->assertTrue($session->has('dom_wizard_data'));

        $sessionData = $session->get('dom_wizard_data');
        $this->assertEquals('TEMPORAIRE', $sessionData['salarie']);
        $this->assertEquals('Test', $sessionData['nom']);
        $this->assertEquals('User', $sessionData['prenom']);
    }

    public function testFormSubmissionWithMissingRequiredFields(): void
    {
        // Arrange
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/dom/first');

        // Act - Soumettre le formulaire sans données requises
        $form = $crawler->selectButton('Suivant')->form();
        $this->client->submit($form);

        // Assert - Reste sur la même page avec des erreurs
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        // Vérifier les messages d'erreur
        $crawler = $this->client->getCrawler();
        $this->assertSelectorExists('.alert-danger');
    }

    public function testAjaxCategoriesEndpoint(): void
    {
        // Arrange
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $sousType = $this->createTestSousTypeDocument();
        $agence = $this->createTestAgence();

        // Act - Appel AJAX pour récupérer les catégories
        $this->client->request('GET', '/dom/categories', [
            'typeDoc' => $sousType->getId(),
            'agence' => $agence->getCodeAgence()
        ]);

        // Assert
        $this->assertResponseIsSuccessful();
        $this->assertJson($this->client->getResponse()->getContent());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
    }

    public function testAjaxCategoriesWithMissingParameters(): void
    {
        // Arrange
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        // Act - Appel AJAX sans paramètres requis
        $this->client->request('GET', '/dom/categories');

        // Assert
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertStringContainsString('manquants', $responseData['error']);
    }

    public function testRedirectionToStep2AfterSuccessfulSubmission(): void
    {
        // Arrange
        $user = $this->createTestUser();
        $this->client->loginUser($user);

        $agence = $this->createTestAgence();
        $service = $this->createTestService();
        $sousType = $this->createTestSousTypeDocument();

        // Soumettre d'abord le formulaire de l'étape 1
        $crawler = $this->client->request('GET', '/dom/first');
        $form = $crawler->selectButton('Suivant')->form();

        $formName = $form->getName();
        $form["{$formName}[emetteur][agenceEmetteur]"] = $agence->getId();
        $form["{$formName}[emetteur][serviceEmetteur]"] = $service->getId();
        $form["{$formName}[sousTypeDocument]"] = $sousType->getId();
        $form["{$formName}[salarie]"] = 'PERMANENT';
        $form["{$formName}[matricule]"] = '12345';

        $this->client->submit($form);

        // Act - Suivre la redirection vers l'étape 2
        $this->client->followRedirect();

        // Assert - Vérifier qu'on arrive bien sur l'étape 2
        $this->assertRouteSame('dom_step2');
        $this->assertSelectorTextContains('h3', 'Étape 2');
    }

    private function createTestUser(): User
    {
        $user = new User();
        $user->setUsername('test_user');
        $user->setEmail('test@example.com');
        $user->setFullname('Test User');
        $user->setRoles(['ROLE_USER']);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    private function createTestAgence(): DomAgence
    {
        $agence = new DomAgence();
        $agence->setCodeAgence('TEST001');
        $agence->setDescription('Agence Test');

        $this->entityManager->persist($agence);
        $this->entityManager->flush();

        return $agence;
    }

    private function createTestService(): DomService
    {
        $service = new DomService();
        $service->setCodeService('SRV001');
        $service->setDescription('Service Test');

        $this->entityManager->persist($service);
        $this->entityManager->flush();

        return $service;
    }

    private function createTestSousTypeDocument(): DomSousTypeDocument
    {
        $sousType = new DomSousTypeDocument();
        $sousType->setCodeSousType('MISSION');
        // Ajouter d'autres propriétés nécessaires

        $this->entityManager->persist($sousType);
        $this->entityManager->flush();

        return $sousType;
    }
}
