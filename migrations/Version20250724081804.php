<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724081804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'ajout de categorie, site, rmq, idemnite pour DOM';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dom_categorie (id INT IDENTITY NOT NULL, dom_sous_type_document_id_id INT, desctiption NVARCHAR(60) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_E7D3C1534F1788B4 ON dom_categorie (dom_sous_type_document_id_id)');
        $this->addSql('CREATE TABLE dom_indemnite (id INT IDENTITY NOT NULL, dom_site_id_id INT, dom_categorie_id_id INT, dom_rmq_id_id INT, montant DOUBLE PRECISION NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_D1109AE6686614BE ON dom_indemnite (dom_site_id_id)');
        $this->addSql('CREATE INDEX IDX_D1109AE6335F19AB ON dom_indemnite (dom_categorie_id_id)');
        $this->addSql('CREATE INDEX IDX_D1109AE6D3BD5507 ON dom_indemnite (dom_rmq_id_id)');
        $this->addSql('CREATE TABLE dom_rmq (id INT IDENTITY NOT NULL, description NVARCHAR(5) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE dom_site (id INT IDENTITY NOT NULL, nom_zone NVARCHAR(50) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE dom_categorie ADD CONSTRAINT FK_E7D3C1534F1788B4 FOREIGN KEY (dom_sous_type_document_id_id) REFERENCES dom_sous_type_document (id)');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6686614BE FOREIGN KEY (dom_site_id_id) REFERENCES dom_site (id)');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6335F19AB FOREIGN KEY (dom_categorie_id_id) REFERENCES dom_categorie (id)');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6D3BD5507 FOREIGN KEY (dom_rmq_id_id) REFERENCES dom_rmq (id)');
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
        $this->addSql('ALTER TABLE dom_categorie DROP CONSTRAINT FK_E7D3C1534F1788B4');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6686614BE');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6335F19AB');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6D3BD5507');
        $this->addSql('DROP TABLE dom_categorie');
        $this->addSql('DROP TABLE dom_indemnite');
        $this->addSql('DROP TABLE dom_rmq');
        $this->addSql('DROP TABLE dom_site');
    }
}
