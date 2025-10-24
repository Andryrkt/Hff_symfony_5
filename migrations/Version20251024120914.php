<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251024120914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_permission (user_id INT NOT NULL, permission_id INT NOT NULL, PRIMARY KEY (user_id, permission_id))');
        $this->addSql('CREATE INDEX IDX_472E5446A76ED395 ON user_permission (user_id)');
        $this->addSql('CREATE INDEX IDX_472E5446FED90CCA ON user_permission (permission_id)');
        $this->addSql('CREATE TABLE user_access_permission (user_access_id INT NOT NULL, permission_id INT NOT NULL, PRIMARY KEY (user_access_id, permission_id))');
        $this->addSql('CREATE INDEX IDX_3268B1004F0AEA2B ON user_access_permission (user_access_id)');
        $this->addSql('CREATE INDEX IDX_3268B100FED90CCA ON user_access_permission (permission_id)');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446A76ED395 FOREIGN KEY (user_id) REFERENCES [user] (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_permission ADD CONSTRAINT FK_472E5446FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_access_permission ADD CONSTRAINT FK_3268B1004F0AEA2B FOREIGN KEY (user_access_id) REFERENCES user_access (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_access_permission ADD CONSTRAINT FK_3268B100FED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT FK_633B30693E030ACD');
        $this->addSql('DROP INDEX IDX_633B30693E030ACD ON user_access');
        $this->addSql('ALTER TABLE user_access ADD all_agence BIT NOT NULL CONSTRAINT DF_633B3069_E0226228 DEFAULT 0');
        $this->addSql('ALTER TABLE user_access ADD all_service BIT NOT NULL CONSTRAINT DF_633B3069_7BA6CA9C DEFAULT 0');
        $this->addSql('ALTER TABLE user_access ADD created_at DATETIME2(6) NOT NULL CONSTRAINT DF_user_access_created_at DEFAULT GETDATE()');
        $this->addSql('ALTER TABLE user_access ADD updated_at DATETIME2(6)');
        $this->addSql('ALTER TABLE user_access DROP COLUMN application_id');
        $this->addSql('ALTER TABLE user_access DROP COLUMN access_type');
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
        $this->addSql('ALTER TABLE user_permission DROP CONSTRAINT FK_472E5446A76ED395');
        $this->addSql('ALTER TABLE user_permission DROP CONSTRAINT FK_472E5446FED90CCA');
        $this->addSql('ALTER TABLE user_access_permission DROP CONSTRAINT FK_3268B1004F0AEA2B');
        $this->addSql('ALTER TABLE user_access_permission DROP CONSTRAINT FK_3268B100FED90CCA');
        $this->addSql('DROP TABLE user_permission');
        $this->addSql('DROP TABLE user_access_permission');
        $this->addSql('ALTER TABLE user_access ADD application_id INT');
        $this->addSql('ALTER TABLE user_access ADD access_type NVARCHAR(30) NOT NULL');
        $this->addSql('ALTER TABLE user_access DROP COLUMN all_agence');
        $this->addSql('ALTER TABLE user_access DROP COLUMN all_service');
        $this->addSql('ALTER TABLE user_access DROP COLUMN created_at');
        $this->addSql('ALTER TABLE user_access DROP COLUMN updated_at');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT FK_633B30693E030ACD FOREIGN KEY (application_id) REFERENCES application (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_633B30693E030ACD ON user_access (application_id)');
    }
}
