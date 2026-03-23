<?php

namespace App\Tests\Functional\Controller\Rh\Dom\Creation;

use App\Dto\Hf\Rh\Dom\FirstFormDto;
use App\Entity\Admin\PersonnelUser\User;
use App\Entity\Hf\Rh\Dom\Categorie;
use App\Entity\Hf\Rh\Dom\Rmq;
use App\Entity\Hf\Rh\Dom\SousTypeDocument;
use App\Repository\Admin\PersonnelUser\UserRepository;
use App\Repository\Hf\Rh\Dom\DomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DomSecondControllerTest extends WebTestCase
{
    private $client;
    private ?EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get('doctrine.orm.entity_manager');
    }

    private function getAuthorizedUser(): User
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_ADMIN"%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) {
            throw new \RuntimeException('Aucun utilisateur admin trouvé en base de données pour le test.');
        }

        return $user;
    }

    public function testSubmitSecondFormSuccessfully(): void
    {
        $testUser = $this->getAuthorizedUser();
        $personnel = $testUser->getPersonnel();
        if (!$personnel) {
            self::markTestSkipped('Test user has no associated Personnel.');
        }

        // Fetch real entities from DB to get their IDs
        $typeMission = $this->em->getRepository(SousTypeDocument::class)->findOneBy(['codeSousType' => 'MISSION']);
        $categorie = $this->em->getRepository(Categorie::class)->findOneBy([]);

        if (!$typeMission || !$categorie) {
            self::markTestSkipped('Base data (TypeMission, Categorie) not found for the test.');
        }

        $firstFormDto = new FirstFormDto();
        $firstFormDto->salarier = 'PERMANENT';
        $firstFormDto->typeMissionId = $typeMission->getId();
        $firstFormDto->categorieId = $categorie->getId();
        $firstFormDto->matricule = $personnel->getMatricule();
        $firstFormDto->nom = $personnel->getNom();
        $firstFormDto->prenom = $personnel->getPrenoms();
        $firstFormDto->cin = '1234567890'; // Fake CIN for test
        $firstFormDto->agenceUser = 'AGENCE TEST';
        $firstFormDto->serviceUser = 'SERVICE TEST';

        $session = $this->client->getContainer()->get('session');
        $session->set('dom_first_form_data', $firstFormDto);
        $session->save();

        $this->client->loginUser($testUser);

        // 3. Access the second form
        $crawler = $this->client->request('GET', '/rh/ordre-de-mission/dom-second-form');
        self::assertResponseIsSuccessful();

        // 4. Submit the form with valid data
        $form = $crawler->selectButton('Enregistrer')->form();

        $formValues = [
            'second_form[motifDeplacement]' => 'Test motif de déplacement',
            'second_form[client]' => 'Test Client',
            'second_form[lieuIntervention]' => 'Test Lieu',
            'second_form[dateHeureMission][dateDebut]' => (new \DateTime())->format('Y-m-d'),
            'second_form[dateHeureMission][heureDebut]' => '08:00',
            'second_form[dateHeureMission][dateFin]' => (new \DateTime('+1 day'))->format('Y-m-d'),
            'second_form[dateHeureMission][heureFin]' => '17:00',
            'second_form[nombreJour]' => '2',
        ];

        $this->client->submit($form, $formValues);

        // 5. Assertions
        self::assertResponseRedirects('/hf/rh/dom/liste');
        $crawler = $this->client->followRedirect();

        self::assertSelectorExists('.alert-success');
        self::assertSelectorTextContains('.alert-success', 'La demande d\'ordre de mission a été créée avec succès.');

        $domRepository = static::getContainer()->get(DomRepository::class);
        $dom = $domRepository->findOneBy(['motifDeplacement' => 'Test motif de déplacement']);
        self::assertNotNull($dom);

        // Clean up the created entity
        $this->em->remove($dom);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->em->close();
        $this->em = null;
    }
}
