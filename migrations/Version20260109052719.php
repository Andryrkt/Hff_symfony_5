<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260109052719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE badm ALTER COLUMN nom_image NVARCHAR(255)');
        $this->addSql('ALTER TABLE badm ALTER COLUMN nom_fichier NVARCHAR(255)');
        $this->addSql('ALTER TABLE historique_operation_document DROP CONSTRAINT FK_7B383AB03D8FE0CE');
        $this->addSql('DROP INDEX IDX_7B383AB03D8FE0CE ON historique_operation_document');
        $this->addSql('sp_rename \'historique_operation_document.typeOperationId\', \'type_operation_id\', \'COLUMN\'');
        $this->addSql('ALTER TABLE historique_operation_document ADD CONSTRAINT FK_7B383AB0C3EF8F86 FOREIGN KEY (type_operation_id) REFERENCES type_operation (id)');
        $this->addSql('CREATE INDEX IDX_7B383AB0C3EF8F86 ON historique_operation_document (type_operation_id)');
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
        $this->addSql('ALTER TABLE historique_operation_document DROP CONSTRAINT FK_7B383AB0C3EF8F86');
        $this->addSql('DROP INDEX IDX_7B383AB0C3EF8F86 ON historique_operation_document');
        $this->addSql('sp_rename \'historique_operation_document.type_operation_id\', \'typeOperationId\', \'COLUMN\'');
        $this->addSql('ALTER TABLE historique_operation_document ADD CONSTRAINT FK_7B383AB03D8FE0CE FOREIGN KEY (typeOperationId) REFERENCES type_operation (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_7B383AB03D8FE0CE ON historique_operation_document (typeOperationId)');
        $this->addSql('ALTER TABLE badm ALTER COLUMN nom_image NVARCHAR(50)');
        $this->addSql('ALTER TABLE badm ALTER COLUMN nom_fichier NVARCHAR(50)');
    }
}
