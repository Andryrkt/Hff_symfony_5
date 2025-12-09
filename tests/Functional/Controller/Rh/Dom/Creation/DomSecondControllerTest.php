<?php

namespace App\Tests\Functional\Controller\Rh\Dom\Creation;

use App\Tests\BaseTestCase;
use App\Entity\Hf\Rh\Dom\Rmq;
use App\Dto\Hf\Rh\Dom\FirstFormDto;
use App\DataFixtures\Hf\Rh\dom\RmqFixtures;
use App\Repository\Hf\Rh\Dom\DomRepository;
use App\DataFixtures\Admin\PersonnelUser\UserFixtures;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\DataFixtures\Admin\PersonnelUser\PersonnelFixtures;

class DomSecondControllerTest extends BaseTestCase
{
    private $referenceRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->referenceRepository = $this->loadTestFixtures([
            PersonnelFixtures::class, // Assure que le personnel '9999' existe
            UserFixtures::class,      // Assure que l'utilisateur de test existe et est lié au personnel
            RmqFixtures::class,           // Assure que les entités Rmq (STD, 50) existent
        ])->getReferenceRepository();

        // DEBUG: Check if Rmq 'STD' is found
        $em = static::getContainer()->get('doctrine')->getManager();
        $rmqStd = $em->getRepository(Rmq::class)->findOneBy(['description' => 'STD']);
        if ($rmqStd) {
            echo "\nDEBUG: Rmq 'STD' found in test setUp!";
        } else {
            echo "\nDEBUG: Rmq 'STD' NOT found in test setUp!";
        }
    }

    public function testSubmitSecondFormSuccessfully(): void
    {
        // 1. Récupérer un utilisateur de test via les fixtures (il a la permission RH_ORDRE_MISSION_CREATE)
        $testUser = $this->referenceRepository->getReference('user_u1');

        // 2. Simuler les données de session du premier formulaire
        $firstFormDto = new FirstFormDto();
        // Remplissez le DTO avec des données valides pour le test
        $firstFormDto->salarier = 'PERMANENT';
        // $firstFormDto->typeMission = '';
        // $firstFormDto->categorie = '';
        $firstFormDto->matricule = '9999'; // Garanti par TestPersonnelFixtures
        $firstFormDto->nom = 'TEST';
        $firstFormDto->prenom = 'TEST';
        $firstFormDto->cin = null;


        $session = static::getContainer()->get('session');
        $session->set('dom_first_form_data', $firstFormDto);
        $session->save();

        // 3. Accéder au second formulaire
        $crawler = $this->client->request('GET', '/rh/ordre-de-mission/dom-second-form');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h3', 'Nouvelle demande d\'ordre de mission'); // A adapter au titre réel

        // 4. Créer un fichier de test pour l'upload
        $testPdfPath = sys_get_temp_dir() . '/test.pdf';
        file_put_contents($testPdfPath, 'dummy pdf content');
        $uploadedFile = new UploadedFile($testPdfPath, 'test.pdf', 'application/pdf', null, true);

        // 5. Soumettre le formulaire avec des données valides
        $form = $crawler->selectButton('Enregistrer')->form([
            'second_form[motifDeplacement]' => 'Test motif de déplacement',
            'second_form[client]' => 'Test Client',
            'second_form[lieuIntervention]' => 'Test Lieu',
            'second_form[pieceJoint01]' => $uploadedFile,
            // ... Remplissez les autres champs nécessaires
        ]);

        $this->client->submit($form);

        // 6. Vérifier les assertions
        // Vérifier la redirection vers la liste des DOMs
        self::assertResponseRedirects('/rh/ordre-de-mission/liste'); // Adaptez l'URL si nécessaire
        $crawler = $this->client->followRedirect();

        // Vérifier le message de succès
        self::assertSelectorExists('.alert-success');
        self::assertSelectorTextContains('.alert-success', 'La demande d\'ordre de mission a été créée avec succès.');

        // Vérifier en base de données que l'ordre de mission a été créé
        /** @var DomRepository $domRepository */
        $domRepository = static::getContainer()->get(DomRepository::class);
        $dom = $domRepository->findOneBy(['motifDeplacement' => 'Test motif de déplacement']);
        self::assertNotNull($dom);

        // Nettoyage du fichier temporaire
        unlink($testPdfPath);
        // La suppression des entités de la BDD est gérée automatiquement par LiipTestFixturesBundle
    }
}
