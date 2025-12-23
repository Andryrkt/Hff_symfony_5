<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251219130935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE casier (id INT IDENTITY NOT NULL, agence_rattacher_id INT, statut_demande_id INT, nom NVARCHAR(20) NOT NULL, numero NVARCHAR(15) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), createdBy INT, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_3FDF285CB56EBC1 ON casier (agence_rattacher_id)');
        $this->addSql('CREATE INDEX IDX_3FDF285F5225311 ON casier (statut_demande_id)');
        $this->addSql('CREATE INDEX IDX_3FDF285D3564642 ON casier (createdBy)');
        $this->addSql('ALTER TABLE casier ADD CONSTRAINT FK_3FDF285CB56EBC1 FOREIGN KEY (agence_rattacher_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE casier ADD CONSTRAINT FK_3FDF285F5225311 FOREIGN KEY (statut_demande_id) REFERENCES statut_demande (id)');
        $this->addSql('ALTER TABLE casier ADD CONSTRAINT FK_3FDF285D3564642 FOREIGN KEY (createdBy) REFERENCES [user] (id)');
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
        $this->addSql('ALTER TABLE casier DROP CONSTRAINT FK_3FDF285CB56EBC1');
        $this->addSql('ALTER TABLE casier DROP CONSTRAINT FK_3FDF285F5225311');
        $this->addSql('ALTER TABLE casier DROP CONSTRAINT FK_3FDF285D3564642');
        $this->addSql('DROP TABLE casier');
    }
}
