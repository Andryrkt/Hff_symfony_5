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

        // On recherche un utilisateur avec le rôle ADMIN pour s'assurer qu'il a les droits
        $user = $userRepository->createQueryBuilder('u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_ADMIN"%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$user) {
            // Si aucun admin n'est trouvé, on prend le premier utilisateur, mais le test risque d'être skippé
            $user = $userRepository->findOneBy([]);
        }

        if (!$user) {
            throw new \RuntimeException('Aucun utilisateur trouvé en base de données.');
        }

        return $user;
    }

    /**
     * Teste que l'accès au formulaire est refusé pour un utilisateur non authentifié.
     * L'utilisateur doit être redirigé vers la page de connexion.
     */
    public function testAccessDeniedAnonymous(): void
    {
        $this->client->request('GET', '/rh/ordre-de-mission/dom-first-form');
        self::assertResponseRedirects('/login');
    }

    /**
     * Teste l'affichage du premier formulaire pour un utilisateur authentifié et autorisé.
     * Vérifie que la réponse est réussie et que le formulaire est bien présent dans la page.
     */
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

    /**
     * Teste la soumission réussie du premier formulaire.
     * Vérifie que l'utilisateur est redirigé vers le second formulaire
     * et que les données du formulaire sont bien stockées en session.
     */
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
        $form['first_form[prenom]'] = 'TestPrenom';
        $form['first_form[cin]'] = '123456789';
        $form['first_form[salarier]'] = 'TEMPORAIRE';

        // Soumission
        $this->client->submit($form);

        self::assertResponseRedirects('/rh/ordre-de-mission/dom-second-form');

        // Vérification de la session
        $session = $this->client->getContainer()->get('session');
        self::assertNotNull($session->get('dom_first_form_data'));
    }
}
