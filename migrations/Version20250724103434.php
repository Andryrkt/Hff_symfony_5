<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724103434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dom_indemnite ADD dom_sous_type_document_id_id INT');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE64F1788B4 FOREIGN KEY (dom_sous_type_document_id_id) REFERENCES dom_sous_type_document (id)');
        $this->addSql('CREATE INDEX IDX_D1109AE64F1788B4 ON dom_indemnite (dom_sous_type_document_id_id)');
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
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE64F1788B4');
        $this->addSql('DROP INDEX IDX_D1109AE64F1788B4 ON dom_indemnite');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN dom_sous_type_document_id_id');
    }
}
