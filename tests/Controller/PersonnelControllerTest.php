<?php

namespace App\Test\Controller\Admin\PersonnelUser;

use App\Entity\Admin\PersonnelUser\Personnel;
use App\Repository\Admin\PersonnelUser\PersonnelRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PersonnelControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private PersonnelRepository $repository;
    private string $path = '/admin/personnel/user/personnel/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = (static::getContainer()->get('doctrine'))->getRepository(Personnel::class);

        foreach ($this->repository->findAll() as $object) {
            $this->repository->remove($object, true);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Personnel index');

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
            'personnel[nom]' => 'Testing',
            'personnel[prenoms]' => 'Testing',
            'personnel[matricule]' => 'Testing',
            'personnel[societe]' => 'Testing',
            'personnel[createdAt]' => 'Testing',
            'personnel[updatedAt]' => 'Testing',
            'personnel[agenceServiceIrium]' => 'Testing',
            'personnel[users]' => 'Testing',
        ]);

        self::assertResponseRedirects('/admin/personnel/user/personnel/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Personnel();
        $fixture->setNom('My Title');
        $fixture->setPrenoms('My Title');
        $fixture->setMatricule('My Title');
        $fixture->setSociete('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setAgenceServiceIrium('My Title');
        $fixture->setUsers('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Personnel');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Personnel();
        $fixture->setNom('My Title');
        $fixture->setPrenoms('My Title');
        $fixture->setMatricule('My Title');
        $fixture->setSociete('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setAgenceServiceIrium('My Title');
        $fixture->setUsers('My Title');

        $this->repository->add($fixture, true);

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'personnel[nom]' => 'Something New',
            'personnel[prenoms]' => 'Something New',
            'personnel[matricule]' => 'Something New',
            'personnel[societe]' => 'Something New',
            'personnel[createdAt]' => 'Something New',
            'personnel[updatedAt]' => 'Something New',
            'personnel[agenceServiceIrium]' => 'Something New',
            'personnel[users]' => 'Something New',
        ]);

        self::assertResponseRedirects('/admin/personnel/user/personnel/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getNom());
        self::assertSame('Something New', $fixture[0]->getPrenoms());
        self::assertSame('Something New', $fixture[0]->getMatricule());
        self::assertSame('Something New', $fixture[0]->getSociete());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getAgenceServiceIrium());
        self::assertSame('Something New', $fixture[0]->getUsers());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Personnel();
        $fixture->setNom('My Title');
        $fixture->setPrenoms('My Title');
        $fixture->setMatricule('My Title');
        $fixture->setSociete('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setAgenceServiceIrium('My Title');
        $fixture->setUsers('My Title');

        $this->repository->add($fixture, true);

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/admin/personnel/user/personnel/');
    }
}
