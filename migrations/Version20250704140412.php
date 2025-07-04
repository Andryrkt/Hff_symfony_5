<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250704140412 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'relation entre agence et service MANY TO MANY';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agence_service (agence_id INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY (agence_id, service_id))');
        $this->addSql('CREATE INDEX IDX_DD69B2E6D725330D ON agence_service (agence_id)');
        $this->addSql('CREATE INDEX IDX_DD69B2E6ED5CA9E6 ON agence_service (service_id)');
        $this->addSql('ALTER TABLE agence_service ADD CONSTRAINT FK_DD69B2E6D725330D FOREIGN KEY (agence_id) REFERENCES agence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE agence_service ADD CONSTRAINT FK_DD69B2E6ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA db_accessadmin');
        $this->addSql('CREATE SCHEMA db_backupoperator');
        $this->addSql('CREATE SCHEMA db_datareader');
        $this->addSql('CREATE SCHEMA db_datawriter');
        $this->addSql('CREATE SCHEMA db_ddladmin');
        $this->addSql('CREATE SCHEMA db_denydatareader');
        $this->addSql('CREATE SCHEMA db_denydatawriter');
        $this->addSql('CREATE SCHEMA db_owner');
        $this->addSql('CREATE SCHEMA db_securityadmin');
        $this->addSql('CREATE SCHEMA dbo');
        $this->addSql('ALTER TABLE agence_service DROP CONSTRAINT FK_DD69B2E6D725330D');
        $this->addSql('ALTER TABLE agence_service DROP CONSTRAINT FK_DD69B2E6ED5CA9E6');
        $this->addSql('DROP TABLE agence_service');
    }
}
