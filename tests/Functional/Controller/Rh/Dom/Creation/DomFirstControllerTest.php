<?php

namespace App\Tests\Functional\Controller\Rh\Dom\Creation;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Admin\PersonnelUser\User;
use App\Repository\Admin\PersonnelUser\UserRepository;
use App\DataFixtures\Hf\Rh\Dom\RmqFixtures;
use App\DataFixtures\Hf\Rh\Dom\CategorieFixtures;
use App\DataFixtures\Hf\Rh\Dom\SousTypeDocumentFixtures;

class DomFirstControllerTest extends WebTestCase
{
    protected $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        // On n'utilise pas loadTestFixtures pour ne pas purger la base de données existante.
        // On s'appuie sur les données déjà présentes.
    }

    private function getAuthorizedUser(): User
    {
        // On doit accéder au conteneur via static::getContainer() ou $this->client->getContainer()
        // Mais dans un test fonctionnel, le conteneur est accessible après createClient
        $container = $this->client->getContainer();
        $userRepository = $container->get(UserRepository::class);

        // Fallback: prendre le premier utilisateur de la base
        $user = $userRepository->findOneBy([]);

        if (!$user) {
            throw new \RuntimeException('Aucun utilisateur trouvé en base de données.');
        }

        return $user;
    }

    public function testAccessDeniedAnonymous(): void
    {
        $this->client->request('GET', '/rh/ordre-de-mission/dom-first-form');
        self::assertResponseRedirects('/login');
    }

    public function testDisplayFirstForm(): void
    {
        $user = $this->getAuthorizedUser();
        $this->client->loginUser($user);

        $this->client->request('GET', '/rh/ordre-de-mission/dom-first-form');

        if ($this->client->getResponse()->getStatusCode() === 403) {
            self::markTestSkipped('L\'utilisateur trouvé n\'a pas la permission RH_ORDRE_MISSION_CREATE.');
        }

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('form');
        // Vérification de la présence d'un titre (h3 ou autre)
        // self::assertSelectorTextContains('h3', 'Ordre de mission'); 
    }

    public function testSubmitFirstFormSuccessfully(): void
    {
        $user = $this->getAuthorizedUser();
        $this->client->loginUser($user);

        $crawler = $this->client->request('GET', '/rh/ordre-de-mission/dom-first-form');

        if ($this->client->getResponse()->getStatusCode() === 403) {
            self::markTestSkipped('L\'utilisateur trouvé n\'a pas la permission RH_ORDRE_MISSION_CREATE.');
        }

        $form = $crawler->selectButton('Suivant')->form();

        // Remplissage du formulaire
        $form['first_form[nom]'] = 'TestNom';
        $form['first_form[prenoms]'] = 'TestPrenoms';
        $form['first_form[cin]'] = '123456789';
        $form['first_form[salarier]'] = 'PERMANENT';

        // Soumission
        $this->client->submit($form);

        self::assertResponseRedirects('/rh/ordre-de-mission/dom-second-form');

        // Vérification de la session
        $session = $this->client->getContainer()->get('session');
        self::assertNotNull($session->get('dom_first_form_data'));
    }
}
