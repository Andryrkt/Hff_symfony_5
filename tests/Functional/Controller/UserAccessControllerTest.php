<?php

namespace App\Test\Controller;

use App\Entity\Admin\PersonnelUser\UserAccess;
use App\Repository\Admin\PersonnelUser\UserAccessRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserAccessControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private UserAccessRepository $repository;
    private string $path = '/user/access/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(UserAccess::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('UserAccess index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'user_access[accessType]' => 'Testing',
            'user_access[users]' => 'Testing',
            'user_access[agence]' => 'Testing',
            'user_access[service]' => 'Testing',
            'user_access[application]' => 'Testing',
        ]);

        self::assertResponseRedirects('/user/access/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new UserAccess();
        $fixture->setAccessType('My Title');
        $fixture->setUsers(new \App\Entity\Admin\PersonnelUser\User());
        $fixture->setAgence(new \App\Entity\Admin\AgenceService\Agence());
        $fixture->setService(new \App\Entity\Admin\AgenceService\Service());
        $fixture->setApplication(new \App\Entity\Admin\ApplicationGroupe\Application());

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('UserAccess');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new UserAccess();
        $fixture->setAccessType('My Title');
        $fixture->setUsers(new \App\Entity\Admin\PersonnelUser\User());
        $fixture->setAgence(new \App\Entity\Admin\AgenceService\Agence());
        $fixture->setService(new \App\Entity\Admin\AgenceService\Service());
        $fixture->setApplication(new \App\Entity\Admin\ApplicationGroupe\Application());

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'user_access[accessType]' => 'Something New',
            'user_access[users]' => 'Something New',
            'user_access[agence]' => 'Something New',
            'user_access[service]' => 'Something New',
            'user_access[application]' => 'Something New',
        ]);

        self::assertResponseRedirects('/user/access/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getAccessType());
        self::assertSame('Something New', $fixture[0]->getUsers());
        self::assertSame('Something New', $fixture[0]->getAgence());
        self::assertSame('Something New', $fixture[0]->getService());
        self::assertSame('Something New', $fixture[0]->getApplication());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new UserAccess();
        $fixture->setAccessType('My Title');
        $fixture->setUsers(new \App\Entity\Admin\PersonnelUser\User());
        $fixture->setAgence(new \App\Entity\Admin\AgenceService\Agence());
        $fixture->setService(new \App\Entity\Admin\AgenceService\Service());
        $fixture->setApplication(new \App\Entity\Admin\ApplicationGroupe\Application());

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/user/access/');
    }
}
