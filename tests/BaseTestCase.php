<?php
// tests/BaseTestCase.php

namespace App\Tests;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseTestCase extends WebTestCase
{
    protected $databaseTool;
    protected $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class);
    }

    protected function loadTestFixtures(array $fixtures = [])
    {
        return $this->databaseTool->get('default')->loadFixtures($fixtures);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}
