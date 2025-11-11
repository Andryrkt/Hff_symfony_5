<?php

namespace App\Tests\Controller\Rh\Dom;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DomSecondControllerTest extends WebTestCase
{
    public function testSecondFormSubmissionWithNullableData()
    {
        $client = static::createClient();

        // 1. Login as a user with the required role
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('test@example.com'); // Change to a valid user email
        $client->loginUser($testUser);

        // 2. Simulate the session data from the first form
        $session = $client->getContainer()->get('session');
        $firstFormDto = new \App\Dto\Rh\Dom\FirstFormDto();
        // Set any necessary properties on $firstFormDto
        $session->set('dom_first_form_data', $firstFormDto);
        $session->save();

        // 3. Submit the second form with nullable data
        $crawler = $client->request('GET', '/rh/ordre-de-mission/dom-second-form');
        $form = $crawler->selectButton('Créer la demande')->form();

        $client->submit($form, [
            'second_form[debiteur]' => null,
            'second_form[agenceUser]' => null,
            'second_form[serviceUser]' => null,
            'second_form[dateDemande]' => null,
            'second_form[typeMission]' => null,
            'second_form[categorie]' => null,
            'second_form[site]' => null,
            'second_form[matricule]' => null,
            'second_form[nom]' => null,
            'second_form[prenom]' => null,
            'second_form[cin]' => null,
            'second_form[salarier]' => null,
            'second_form[rmq]' => null,
            'second_form[dateHeureMission]' => null,
            'second_form[nombreJour]' => null,
            'second_form[motifDeplacement]' => null,
            'second_form[pieceJustificatif]' => null,
            'second_form[client]' => null,
            'second_form[fiche]' => null,
            'second_form[lieuIntervention]' => null,
            'second_form[vehiculeSociete]' => null,
            'second_form[numVehicule]' => null,
            'second_form[idemnityDepl]' => null,
            'second_form[totalIndemniteDeplacement]' => null,
            'second_form[devis]' => null,
            'second_form[supplementJournaliere]' => null,
            'second_form[indemniteForfaitaire]' => null,
            'second_form[totalIndemniteForfaitaire]' => null,
            'second_form[motifAutresDepense1]' => null,
            'second_form[autresDepense1]' => null,
            'second_form[motifAutresDepense2]' => null,
            'second_form[autresDepense2]' => null,
            'second_form[motifAutresDepense3]' => null,
            'second_form[autresDepense3]' => null,
            'second_form[totalAutresDepenses]' => null,
            'second_form[totalGeneralPayer]' => null,
            'second_form[modePayement]' => null,
            'second_form[mode]' => null,
            'second_form[pieceJoint01]' => null,
            'second_form[pieceJoint02]' => null,
        ]);

        // 4. Assert the response
        $this->assertResponseRedirects('/rh/ordre-de-mission/dom-first-form');
    }
}
