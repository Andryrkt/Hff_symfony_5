<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251104055832 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE rh_dom_categorie (id INT IDENTITY NOT NULL, rmq_id INT, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_A0F2ACF1DC09654E ON rh_dom_categorie (rmq_id)');
        $this->addSql('ALTER TABLE rh_dom_categorie ADD CONSTRAINT FK_A0F2ACF1DC09654E FOREIGN KEY (rmq_id) REFERENCES dom_rmq (id)');
        
        // Drop default constraint on created_at for SQL Server
        $this->addSql("
            DECLARE @sql nvarchar(max)
            SELECT @sql = 'ALTER TABLE user_access DROP CONSTRAINT ' + d.name
            FROM sys.tables t
            JOIN sys.default_constraints d ON d.parent_object_id = t.object_id
            JOIN sys.columns c ON c.object_id = t.object_id AND c.column_id = d.parent_column_id
            WHERE t.name = 'user_access' AND c.name = 'created_at'
            EXEC sp_executesql @sql
        ");
        $this->addSql('ALTER TABLE user_access ALTER COLUMN created_at DATETIME2(6) NOT NULL');
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
        $this->addSql('ALTER TABLE rh_dom_categorie DROP CONSTRAINT FK_A0F2ACF1DC09654E');
        $this->addSql('DROP TABLE rh_dom_categorie');
        $this->addSql('ALTER TABLE user_access ALTER COLUMN created_at DATETIME2(6) NOT NULL');
        $this->addSql('ALTER TABLE user_access ADD DEFAULT GETDATE() FOR created_at');
    }
}
