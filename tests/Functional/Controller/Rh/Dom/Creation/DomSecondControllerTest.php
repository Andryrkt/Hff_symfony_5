<?php

namespace App\Tests\Functional\Controller\Rh\Dom\Creation;


use App\Dto\Rh\Dom\FirstFormDto;
use App\Entity\Admin\PersonnelUser\User;
use App\Repository\Rh\Dom\DomRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\Admin\PersonnelUser\UserRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DomSecondControllerTest extends WebTestCase
{
    public function testSubmitSecondFormSuccessfully(): void
    {
        $client = static::createClient();

        // 1. Récupérer un utilisateur de test pour se connecter
        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneBy(['email' => 'test@hff.mg']); // Remplacez par un utilisateur de test existant

        if (!$testUser) {
            // Créez un utilisateur de test si nécessaire, ou assurez-vous qu'il existe
            // Ce code est un placeholder, adaptez-le à votre entité User
            $testUser = new User();
            $testUser->setEmail('test@hff.mg');
            $testUser->setRoles(['ROLE_USER', 'RH_ORDRE_MISSION_CREATE']);
            // $testUser->setPassword('password'); // Le mot de passe n'est pas utilisé par loginUser
            $userRepository->add($testUser, true);
        }

        $client->loginUser($testUser);

        // 2. Simuler les données de session du premier formulaire
        $firstFormDto = new FirstFormDto();
        // Remplissez le DTO avec des données valides pour le test
        $firstFormDto->salarier = 'PERMANENT';
        // $firstFormDto->typeMission = '';
        // $firstFormDto->categorie = '';
        $firstFormDto->matricule = '9999'; 
        $firstFormDto->nom = '9999'; 
        $firstFormDto->prenom = '9999'; 
        $firstFormDto->cin = null; 


        $session = static::getContainer()->get('session');
        $session->set('dom_first_form_data', $firstFormDto);
        $session->save();

        // 3. Accéder au second formulaire
        $crawler = $client->request('GET', '/rh/ordre-de-mission/dom-second-form');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Affichage du second formulaire de création de DOM'); // A adapter au titre réel

        // 4. Créer un fichier de test pour l'upload
        $testPdfPath = sys_get_temp_dir().'/test.pdf';
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
        
        $client->submit($form);

        // 6. Vérifier les assertions
        // Vérifier la redirection vers la liste des DOMs
        self::assertResponseRedirects('/rh/ordre-de-mission/liste'); // Adaptez l'URL si nécessaire
        $crawler = $client->followRedirect();

        // Vérifier le message de succès
        self::assertSelectorExists('.alert-success');
        self::assertSelectorTextContains('.alert-success', 'La demande d\'ordre de mission a été créée avec succès.');

        // Vérifier en base de données que l'ordre de mission a été créé
        /** @var DomRepository $domRepository */
        $domRepository = static::getContainer()->get(DomRepository::class);
        $dom = $domRepository->findOneBy(['motifDeplacement' => 'Test motif de déplacement']);
        self::assertNotNull($dom);

        // Nettoyage
        unlink($testPdfPath);
        // Vous pouvez ajouter ici la suppression du DOM créé si nécessaire
    }
}
