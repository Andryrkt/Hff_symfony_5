<?php

namespace App\Test\Controller;

use App\Entity\AgenceService;
use App\Repository\AgenceServiceRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AgenceServiceControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private AgenceServiceRepository $repository;
    private string $path = '/agence/service/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(AgenceService::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('AgenceService index');

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
            'agence_service[code]' => 'Testing',
            'agence_service[responsable]' => 'Testing',
            'agence_service[createdAt]' => 'Testing',
            'agence_service[updatedAt]' => 'Testing',
            'agence_service[agence]' => 'Testing',
            'agence_service[service]' => 'Testing',
        ]);

        self::assertResponseRedirects('/agence/service/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new AgenceService();
        $fixture->setCode('My Title');
        $fixture->setResponsable('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setAgence('My Title');
        $fixture->setService('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('AgenceService');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new AgenceService();
        $fixture->setCode('My Title');
        $fixture->setResponsable('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setAgence('My Title');
        $fixture->setService('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'agence_service[code]' => 'Something New',
            'agence_service[responsable]' => 'Something New',
            'agence_service[createdAt]' => 'Something New',
            'agence_service[updatedAt]' => 'Something New',
            'agence_service[agence]' => 'Something New',
            'agence_service[service]' => 'Something New',
        ]);

        self::assertResponseRedirects('/agence/service/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCode());
        self::assertSame('Something New', $fixture[0]->getResponsable());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getAgence());
        self::assertSame('Something New', $fixture[0]->getService());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new AgenceService();
        $fixture->setCode('My Title');
        $fixture->setResponsable('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setAgence('My Title');
        $fixture->setService('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/agence/service/');
    }
}
