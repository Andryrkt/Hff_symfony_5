<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250722111118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_ordre_mission ADD dom_demandeur_id INT');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD dom_personnel_id INT');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD dom_sous_type_document_id INT');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD date_debut_mission DATETIME2(6)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD date_fin_mission DATETIME2(6)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEEECD3392B FOREIGN KEY (dom_demandeur_id) REFERENCES [user] (id)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE65654707 FOREIGN KEY (dom_personnel_id) REFERENCES personnel (id)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE8C1EC44B FOREIGN KEY (dom_sous_type_document_id) REFERENCES dom_sous_type_document (id)');
        $this->addSql('CREATE INDEX IDX_DB49BFEEECD3392B ON demande_ordre_mission (dom_demandeur_id)');
        $this->addSql('CREATE INDEX IDX_DB49BFEE65654707 ON demande_ordre_mission (dom_personnel_id)');
        $this->addSql('CREATE INDEX IDX_DB49BFEE8C1EC44B ON demande_ordre_mission (dom_sous_type_document_id)');
        $this->addSql('ALTER TABLE dom_sous_type_document ADD created_at DATETIME2(6) NOT NULL');
        $this->addSql('ALTER TABLE dom_sous_type_document ADD updated_at DATETIME2(6)');
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
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEEECD3392B');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE65654707');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE8C1EC44B');
        $this->addSql('DROP INDEX IDX_DB49BFEEECD3392B ON demande_ordre_mission');
        $this->addSql('DROP INDEX IDX_DB49BFEE65654707 ON demande_ordre_mission');
        $this->addSql('DROP INDEX IDX_DB49BFEE8C1EC44B ON demande_ordre_mission');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN dom_demandeur_id');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN dom_personnel_id');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN dom_sous_type_document_id');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN date_debut_mission');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP COLUMN date_fin_mission');
        $this->addSql('ALTER TABLE dom_sous_type_document DROP COLUMN created_at');
        $this->addSql('ALTER TABLE dom_sous_type_document DROP COLUMN updated_at');
    }
}
