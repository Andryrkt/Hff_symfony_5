<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250624104616 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_access (id INT IDENTITY NOT NULL, users_id INT, agence_id INT, service_id INT, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_633B306967B3B43D ON user_access (users_id)');
        $this->addSql('CREATE INDEX IDX_633B3069D725330D ON user_access (agence_id)');
        $this->addSql('CREATE INDEX IDX_633B3069ED5CA9E6 ON user_access (service_id)');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT FK_633B306967B3B43D FOREIGN KEY (users_id) REFERENCES [user] (id)');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT FK_633B3069D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT FK_633B3069ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
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
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT FK_633B306967B3B43D');
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT FK_633B3069D725330D');
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT FK_633B3069ED5CA9E6');
        $this->addSql('DROP TABLE user_access');
    }
}
