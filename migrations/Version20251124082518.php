<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251124082518 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6BB1E4E52');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE68A3C7387');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE67D134260');
        $this->addSql('DROP INDEX IDX_D1109AE6BB1E4E52 ON dom_indemnite');
        $this->addSql('DROP INDEX IDX_D1109AE68A3C7387 ON dom_indemnite');
        $this->addSql('DROP INDEX IDX_D1109AE67D134260 ON dom_indemnite');
        $this->addSql('ALTER TABLE dom_indemnite ADD site_id INT');
        $this->addSql('ALTER TABLE dom_indemnite ADD categorie_id INT');
        $this->addSql('ALTER TABLE dom_indemnite ADD rmq_id INT');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN site_id_id');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN categorie_id_id');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN rmq_id_id');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6F6BD1646 FOREIGN KEY (site_id) REFERENCES dom_site (id)');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6BCF5E72D FOREIGN KEY (categorie_id) REFERENCES dom_categorie (id)');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6DC09654E FOREIGN KEY (rmq_id) REFERENCES dom_rmq (id)');
        $this->addSql('CREATE INDEX IDX_D1109AE6F6BD1646 ON dom_indemnite (site_id)');
        $this->addSql('CREATE INDEX IDX_D1109AE6BCF5E72D ON dom_indemnite (categorie_id)');
        $this->addSql('CREATE INDEX IDX_D1109AE6DC09654E ON dom_indemnite (rmq_id)');
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
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6F6BD1646');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6BCF5E72D');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6DC09654E');
        $this->addSql('DROP INDEX IDX_D1109AE6F6BD1646 ON dom_indemnite');
        $this->addSql('DROP INDEX IDX_D1109AE6BCF5E72D ON dom_indemnite');
        $this->addSql('DROP INDEX IDX_D1109AE6DC09654E ON dom_indemnite');
        $this->addSql('ALTER TABLE dom_indemnite ADD site_id_id INT');
        $this->addSql('ALTER TABLE dom_indemnite ADD categorie_id_id INT');
        $this->addSql('ALTER TABLE dom_indemnite ADD rmq_id_id INT');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN site_id');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN categorie_id');
        $this->addSql('ALTER TABLE dom_indemnite DROP COLUMN rmq_id');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6BB1E4E52 FOREIGN KEY (site_id_id) REFERENCES dom_site (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE68A3C7387 FOREIGN KEY (categorie_id_id) REFERENCES dom_categorie (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE67D134260 FOREIGN KEY (rmq_id_id) REFERENCES dom_rmq (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE6BB1E4E52 ON dom_indemnite (site_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE68A3C7387 ON dom_indemnite (categorie_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D1109AE67D134260 ON dom_indemnite (rmq_id_id)');
    }
}
