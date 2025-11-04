<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104062319 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rh_dom_categorie DROP CONSTRAINT FK_A0F2ACF1DC09654E');
        $this->addSql('DROP TABLE rh_dom_categorie');
        $this->addSql('ALTER TABLE dom_categorie ADD rmq_id INT');
        $this->addSql('ALTER TABLE dom_categorie ADD CONSTRAINT FK_E7D3C153DC09654E FOREIGN KEY (rmq_id) REFERENCES dom_rmq (id)');
        $this->addSql('CREATE INDEX IDX_E7D3C153DC09654E ON dom_categorie (rmq_id)');
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
        $this->addSql('CREATE TABLE rh_dom_categorie (id INT IDENTITY NOT NULL, rmq_id INT, PRIMARY KEY (id))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_A0F2ACF1DC09654E ON rh_dom_categorie (rmq_id)');
        $this->addSql('ALTER TABLE rh_dom_categorie ADD CONSTRAINT FK_A0F2ACF1DC09654E FOREIGN KEY (rmq_id) REFERENCES dom_rmq (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_categorie DROP CONSTRAINT FK_E7D3C153DC09654E');
        $this->addSql('DROP INDEX IDX_E7D3C153DC09654E ON dom_categorie');
        $this->addSql('ALTER TABLE dom_categorie DROP COLUMN rmq_id');
    }
}
