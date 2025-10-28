<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251028083223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_access DROP CONSTRAINT FK_D5EFF384D725330D');
        $this->addSql('ALTER TABLE group_access DROP CONSTRAINT FK_D5EFF384ED5CA9E6');
        $this->addSql('ALTER TABLE group_access DROP CONSTRAINT FK_D5EFF3843E030ACD');
        $this->addSql('ALTER TABLE group_access DROP CONSTRAINT FK_D5EFF384FE54D947');
        $this->addSql('ALTER TABLE users_groups DROP CONSTRAINT FK_FF8AB7E0A76ED395');
        $this->addSql('ALTER TABLE users_groups DROP CONSTRAINT FK_FF8AB7E0FE54D947');
        $this->addSql('DROP TABLE group_access');
        $this->addSql('DROP TABLE users_groups');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT DF_633B3069_8B8E8428');
        $this->addSql('ALTER TABLE user_access ALTER COLUMN created_at DATETIME2(6) NOT NULL');
        $this->addSql('ALTER TABLE vignette ADD icon NVARCHAR(255)');
        $this->addSql('ALTER TABLE vignette ADD color NVARCHAR(255)');
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
        $this->addSql('CREATE TABLE group_access (id INT IDENTITY NOT NULL, group_id INT, application_id INT, agence_id INT, service_id INT, access_type NVARCHAR(30) COLLATE French_CI_AS NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D5EFF384FE54D947 ON group_access (group_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D5EFF3843E030ACD ON group_access (application_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D5EFF384D725330D ON group_access (agence_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_D5EFF384ED5CA9E6 ON group_access (service_id)');
        $this->addSql('CREATE TABLE users_groups (user_id INT NOT NULL, group_id INT NOT NULL, PRIMARY KEY (user_id, group_id))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_FF8AB7E0A76ED395 ON users_groups (user_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_FF8AB7E0FE54D947 ON users_groups (group_id)');
        $this->addSql('CREATE TABLE user_group (id INT IDENTITY NOT NULL, name NVARCHAR(100) COLLATE French_CI_AS NOT NULL, description NVARCHAR(255) COLLATE French_CI_AS, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE NONCLUSTERED INDEX UNIQ_8F02BF9D5E237E06 ON user_group (name) WHERE name IS NOT NULL');
        $this->addSql('ALTER TABLE group_access ADD CONSTRAINT FK_D5EFF384D725330D FOREIGN KEY (agence_id) REFERENCES agence (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE group_access ADD CONSTRAINT FK_D5EFF384ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE group_access ADD CONSTRAINT FK_D5EFF3843E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE group_access ADD CONSTRAINT FK_D5EFF384FE54D947 FOREIGN KEY (group_id) REFERENCES user_group (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0A76ED395 FOREIGN KEY (user_id) REFERENCES [user] (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES user_group (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vignette DROP COLUMN icon');
        $this->addSql('ALTER TABLE vignette DROP COLUMN color');
        $this->addSql('ALTER TABLE user_access ALTER COLUMN created_at DATETIME2(6) NOT NULL');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT DF_633B3069_8B8E8428 DEFAULT CURRENT_TIMESTAMP FOR created_at');
    }
}
