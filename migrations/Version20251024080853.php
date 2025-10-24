<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251024080853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE permission (id INT IDENTITY NOT NULL, vignette_id INT, code NVARCHAR(50) NOT NULL, description NVARCHAR(255), created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_E04992AA7D16298B ON permission (vignette_id)');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AA7D16298B FOREIGN KEY (vignette_id) REFERENCES vignette (id)');
        $this->addSql('ALTER TABLE persmission DROP CONSTRAINT FK_53EA7C497D16298B');
        $this->addSql('DROP TABLE persmission');
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
        $this->addSql('CREATE TABLE persmission (id INT IDENTITY NOT NULL, vignette_id INT, code NVARCHAR(50) COLLATE French_CI_AS NOT NULL, description NVARCHAR(255) COLLATE French_CI_AS, PRIMARY KEY (id))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_53EA7C497D16298B ON persmission (vignette_id)');
        $this->addSql('ALTER TABLE persmission ADD CONSTRAINT FK_53EA7C497D16298B FOREIGN KEY (vignette_id) REFERENCES vignette (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE permission DROP CONSTRAINT FK_E04992AA7D16298B');
        $this->addSql('DROP TABLE permission');
    }
}
