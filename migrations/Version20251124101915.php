<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124101915 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dom_categorie DROP CONSTRAINT FK_E7D3C153ED6251B5');
        $this->addSql('DROP INDEX IDX_E7D3C153ED6251B5 ON dom_categorie');
        $this->addSql('sp_rename \'dom_categorie.sous_type_document_id_id\', \'sous_type_document_id\', \'COLUMN\'');
        $this->addSql('ALTER TABLE dom_categorie ADD CONSTRAINT FK_E7D3C153CCEEF398 FOREIGN KEY (sous_type_document_id) REFERENCES dom_sous_type_document (id)');
        $this->addSql('CREATE INDEX IDX_E7D3C153CCEEF398 ON dom_categorie (sous_type_document_id)');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6ED6251B5');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6F6BD1646');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6DC09654E');
        $this->addSql('DROP INDEX IDX_D1109AE6ED6251B5 ON dom_indemnite');
        $this->addSql('DROP INDEX IDX_D1109AE6F6BD1646 ON dom_indemnite');
        $this->addSql('DROP INDEX IDX_D1109AE6DC09654E ON dom_indemnite');
        $this->addSql('ALTER TABLE dom_indemnite ADD siteId INT');
        $this->addSql('ALTER TABLE dom_indemnite ADD rmqId INT');
        $this->addSql('ALTER TABLE dom_indemnite ADD sousTypeDocumentId INT');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN sous_type_document_id_id');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN site_id');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN rmq_id');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE66973A4FD FOREIGN KEY (siteId) REFERENCES dom_site (id)');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6B22B3806 FOREIGN KEY (rmqId) REFERENCES dom_rmq (id)');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6B7E1F171 FOREIGN KEY (sousTypeDocumentId) REFERENCES dom_sous_type_document (id)');
        $this->addSql('CREATE INDEX IDX_D1109AE66973A4FD ON dom_indemnite (siteId)');
        $this->addSql('CREATE INDEX IDX_D1109AE6B22B3806 ON dom_indemnite (rmqId)');
        $this->addSql('CREATE INDEX IDX_D1109AE6B7E1F171 ON dom_indemnite (sousTypeDocumentId)');
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
        $this->addSql('ALTER TABLE dom_categorie DROP CONSTRAINT FK_E7D3C153CCEEF398');
        $this->addSql('DROP INDEX IDX_E7D3C153CCEEF398 ON dom_categorie');
        $this->addSql('sp_rename \'dom_categorie.sous_type_document_id\', \'sous_type_document_id_id\', \'COLUMN\'');
        $this->addSql('ALTER TABLE dom_categorie ADD CONSTRAINT FK_E7D3C153ED6251B5 FOREIGN KEY (sous_type_document_id_id) REFERENCES dom_sous_type_document (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_E7D3C153ED6251B5 ON dom_categorie (sous_type_document_id_id)');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE66973A4FD');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6B22B3806');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6B7E1F171');
        $this->addSql('DROP INDEX IDX_D1109AE66973A4FD ON dom_indemnite');
        $this->addSql('DROP INDEX IDX_D1109AE6B22B3806 ON dom_indemnite');
        $this->addSql('DROP INDEX IDX_D1109AE6B7E1F171 ON dom_indemnite');
        $this->addSql('ALTER TABLE dom_indemnite ADD sous_type_document_id_id INT');
        $this->addSql('ALTER TABLE dom_indemnite ADD site_id INT');
        $this->addSql('ALTER TABLE dom_indemnite ADD rmq_id INT');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN siteId');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN rmqId');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN sousTypeDocumentId');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6ED6251B5 FOREIGN KEY (sous_type_document_id_id) REFERENCES dom_sous_type_document (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6F6BD1646 FOREIGN KEY (site_id) REFERENCES dom_site (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6DC09654E FOREIGN KEY (rmq_id) REFERENCES dom_rmq (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE6ED6251B5 ON dom_indemnite (sous_type_document_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE6F6BD1646 ON dom_indemnite (site_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE6DC09654E ON dom_indemnite (rmq_id)');
    }
}
