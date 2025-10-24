<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251024065628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agence_service (agence_id INT NOT NULL, service_id INT NOT NULL, PRIMARY KEY (agence_id, service_id))');
        $this->addSql('CREATE INDEX IDX_DD69B2E6D725330D ON agence_service (agence_id)');
        $this->addSql('CREATE INDEX IDX_DD69B2E6ED5CA9E6 ON agence_service (service_id)');
        $this->addSql('CREATE TABLE application (id INT IDENTITY NOT NULL, name NVARCHAR(100) NOT NULL, description NVARCHAR(255), code NVARCHAR(10), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A45BDDC15E237E06 ON application (name) WHERE name IS NOT NULL');
        $this->addSql('CREATE TABLE dom_categorie (id INT IDENTITY NOT NULL, sous_type_document_id_id INT, description NVARCHAR(60), created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_E7D3C153ED6251B5 ON dom_categorie (sous_type_document_id_id)');
        $this->addSql('CREATE TABLE dom_indemnite (id INT IDENTITY NOT NULL, site_id_id INT, categorie_id_id INT, rmq_id_id INT, sous_type_document_id_id INT, montant DOUBLE PRECISION, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_D1109AE6BB1E4E52 ON dom_indemnite (site_id_id)');
        $this->addSql('CREATE INDEX IDX_D1109AE68A3C7387 ON dom_indemnite (categorie_id_id)');
        $this->addSql('CREATE INDEX IDX_D1109AE67D134260 ON dom_indemnite (rmq_id_id)');
        $this->addSql('CREATE INDEX IDX_D1109AE6ED6251B5 ON dom_indemnite (sous_type_document_id_id)');
        $this->addSql('CREATE TABLE dom_rmq (id INT IDENTITY NOT NULL, description NVARCHAR(5), created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE dom_site (id INT IDENTITY NOT NULL, nom_zone NVARCHAR(50), created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE group_access (id INT IDENTITY NOT NULL, group_id INT, application_id INT, agence_id INT, service_id INT, access_type NVARCHAR(30) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_D5EFF384FE54D947 ON group_access (group_id)');
        $this->addSql('CREATE INDEX IDX_D5EFF3843E030ACD ON group_access (application_id)');
        $this->addSql('CREATE INDEX IDX_D5EFF384D725330D ON group_access (agence_id)');
        $this->addSql('CREATE INDEX IDX_D5EFF384ED5CA9E6 ON group_access (service_id)');
        $this->addSql('CREATE TABLE users_groups (user_id INT NOT NULL, group_id INT NOT NULL, PRIMARY KEY (user_id, group_id))');
        $this->addSql('CREATE INDEX IDX_FF8AB7E0A76ED395 ON users_groups (user_id)');
        $this->addSql('CREATE INDEX IDX_FF8AB7E0FE54D947 ON users_groups (group_id)');
        $this->addSql('CREATE TABLE user_access (id INT IDENTITY NOT NULL, users_id INT, agence_id INT, service_id INT, application_id INT, access_type NVARCHAR(30) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_633B306967B3B43D ON user_access (users_id)');
        $this->addSql('CREATE INDEX IDX_633B3069D725330D ON user_access (agence_id)');
        $this->addSql('CREATE INDEX IDX_633B3069ED5CA9E6 ON user_access (service_id)');
        $this->addSql('CREATE INDEX IDX_633B30693E030ACD ON user_access (application_id)');
        $this->addSql('CREATE TABLE user_group (id INT IDENTITY NOT NULL, name NVARCHAR(100) NOT NULL, description NVARCHAR(255), PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8F02BF9D5E237E06 ON user_group (name) WHERE name IS NOT NULL');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT IDENTITY NOT NULL, body VARCHAR(MAX) NOT NULL, headers VARCHAR(MAX) NOT NULL, queue_name NVARCHAR(190) NOT NULL, created_at DATETIME2(6) NOT NULL, available_at DATETIME2(6) NOT NULL, delivered_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('ALTER TABLE agence_service ADD CONSTRAINT FK_DD69B2E6D725330D FOREIGN KEY (agence_id) REFERENCES agence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE agence_service ADD CONSTRAINT FK_DD69B2E6ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dom_categorie ADD CONSTRAINT FK_E7D3C153ED6251B5 FOREIGN KEY (sous_type_document_id_id) REFERENCES dom_sous_type_document (id)');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6BB1E4E52 FOREIGN KEY (site_id_id) REFERENCES dom_site (id)');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE68A3C7387 FOREIGN KEY (categorie_id_id) REFERENCES dom_categorie (id)');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE67D134260 FOREIGN KEY (rmq_id_id) REFERENCES dom_rmq (id)');
        $this->addSql('ALTER TABLE dom_indemnite ADD CONSTRAINT FK_D1109AE6ED6251B5 FOREIGN KEY (sous_type_document_id_id) REFERENCES dom_sous_type_document (id)');
        $this->addSql('ALTER TABLE group_access ADD CONSTRAINT FK_D5EFF384FE54D947 FOREIGN KEY (group_id) REFERENCES user_group (id)');
        $this->addSql('ALTER TABLE group_access ADD CONSTRAINT FK_D5EFF3843E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
        $this->addSql('ALTER TABLE group_access ADD CONSTRAINT FK_D5EFF384D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE group_access ADD CONSTRAINT FK_D5EFF384ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0A76ED395 FOREIGN KEY (user_id) REFERENCES [user] (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES user_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT FK_633B306967B3B43D FOREIGN KEY (users_id) REFERENCES [user] (id)');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT FK_633B3069D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT FK_633B3069ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE user_access ADD CONSTRAINT FK_633B30693E030ACD FOREIGN KEY (application_id) REFERENCES application (id)');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEEEC25C3C3');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE3AD3919B');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE5FD929EB');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE892F7BB3');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE65654707');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEEECD3392B');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE8C1EC44B');
        $this->addSql('ALTER TABLE demande_ordre_mission DROP CONSTRAINT FK_DB49BFEE3F5343F1');
        $this->addSql('DROP TABLE demande_ordre_mission');
        $this->addSql('ALTER TABLE dom_sous_type_document ALTER COLUMN code_sous_type NVARCHAR(50)');
        $this->addSql('DROP INDEX IDX_8D93D6491C109075 ON [user]');
        $this->addSql('ALTER TABLE [user] ALTER COLUMN roles VARCHAR(MAX) NOT NULL');
        $this->addSql('EXEC sp_addextendedproperty N\'MS_Description\', N\'(DC2Type:json)\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'user\', N\'COLUMN\', \'roles\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6491C109075 ON [user] (personnel_id) WHERE personnel_id IS NOT NULL');
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
        $this->addSql('CREATE TABLE demande_ordre_mission (id INT IDENTITY NOT NULL, dom_demandeur_id INT, dom_personnel_id INT, dom_sous_type_document_id INT, statut_demande_id_id INT, agence_emetteur_id_id INT, service_emetteur_id_id INT, agence_debiteur_id_id INT, service_debiteur_id_id INT, numero_ordre_mission NVARCHAR(11) COLLATE French_CI_AS NOT NULL, date_debut_mission DATETIME2(6), date_fin_mission DATETIME2(6), nombre_jours SMALLINT, motif_deplacement VARCHAR(MAX) COLLATE French_CI_AS, client NVARCHAR(100) COLLATE French_CI_AS, lieu_intervention NVARCHAR(100) COLLATE French_CI_AS, vehicule_societe NVARCHAR(3) COLLATE French_CI_AS, motif_autres_depense1 NVARCHAR(50) COLLATE French_CI_AS, montant_autres_depense1 DOUBLE PRECISION, motif_autres_depense2 NVARCHAR(50) COLLATE French_CI_AS, montant_autres_depense2 DOUBLE PRECISION, motif_autres_depense3 NVARCHAR(50) COLLATE French_CI_AS, montant_autres_depense3 DOUBLE PRECISION NOT NULL, indemnite_forfaitaire DOUBLE PRECISION, total_indemnite_forfaitaire DOUBLE PRECISION, total_general_payer DOUBLE PRECISION, mode_paiement NVARCHAR(50) COLLATE French_CI_AS, piece_jointe1 NVARCHAR(50) COLLATE French_CI_AS, piece_jointe2 NVARCHAR(50) COLLATE French_CI_AS, code_statut NVARCHAR(3) COLLATE French_CI_AS, numero_tel NVARCHAR(10) COLLATE French_CI_AS, devis NVARCHAR(3) COLLATE French_CI_AS, fiche NVARCHAR(20) COLLATE French_CI_AS, numero_vehicule NVARCHAR(20) COLLATE French_CI_AS, supplement_journalier DOUBLE PRECISION, indemnite_chantier DOUBLE PRECISION, PRIMARY KEY (id))');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEEECD3392B ON demande_ordre_mission (dom_demandeur_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEE65654707 ON demande_ordre_mission (dom_personnel_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEE8C1EC44B ON demande_ordre_mission (dom_sous_type_document_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEE3F5343F1 ON demande_ordre_mission (statut_demande_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEEEC25C3C3 ON demande_ordre_mission (agence_emetteur_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEE5FD929EB ON demande_ordre_mission (service_emetteur_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEE3AD3919B ON demande_ordre_mission (agence_debiteur_id_id)');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_DB49BFEE892F7BB3 ON demande_ordre_mission (service_debiteur_id_id)');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEEEC25C3C3 FOREIGN KEY (agence_emetteur_id_id) REFERENCES agence (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE3AD3919B FOREIGN KEY (agence_debiteur_id_id) REFERENCES agence (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE5FD929EB FOREIGN KEY (service_emetteur_id_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE892F7BB3 FOREIGN KEY (service_debiteur_id_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE65654707 FOREIGN KEY (dom_personnel_id) REFERENCES personnel (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEEECD3392B FOREIGN KEY (dom_demandeur_id) REFERENCES [user] (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE8C1EC44B FOREIGN KEY (dom_sous_type_document_id) REFERENCES dom_sous_type_document (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE demande_ordre_mission ADD CONSTRAINT FK_DB49BFEE3F5343F1 FOREIGN KEY (statut_demande_id_id) REFERENCES statut_demande (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE agence_service DROP CONSTRAINT FK_DD69B2E6D725330D');
        $this->addSql('ALTER TABLE agence_service DROP CONSTRAINT FK_DD69B2E6ED5CA9E6');
        $this->addSql('ALTER TABLE dom_categorie DROP CONSTRAINT FK_E7D3C153ED6251B5');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6BB1E4E52');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE68A3C7387');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE67D134260');
        $this->addSql('ALTER TABLE dom_indemnite DROP CONSTRAINT FK_D1109AE6ED6251B5');
        $this->addSql('ALTER TABLE group_access DROP CONSTRAINT FK_D5EFF384FE54D947');
        $this->addSql('ALTER TABLE group_access DROP CONSTRAINT FK_D5EFF3843E030ACD');
        $this->addSql('ALTER TABLE group_access DROP CONSTRAINT FK_D5EFF384D725330D');
        $this->addSql('ALTER TABLE group_access DROP CONSTRAINT FK_D5EFF384ED5CA9E6');
        $this->addSql('ALTER TABLE users_groups DROP CONSTRAINT FK_FF8AB7E0A76ED395');
        $this->addSql('ALTER TABLE users_groups DROP CONSTRAINT FK_FF8AB7E0FE54D947');
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT FK_633B306967B3B43D');
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT FK_633B3069D725330D');
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT FK_633B3069ED5CA9E6');
        $this->addSql('ALTER TABLE user_access DROP CONSTRAINT FK_633B30693E030ACD');
        $this->addSql('DROP TABLE agence_service');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE dom_categorie');
        $this->addSql('DROP TABLE dom_indemnite');
        $this->addSql('DROP TABLE dom_rmq');
        $this->addSql('DROP TABLE dom_site');
        $this->addSql('DROP TABLE group_access');
        $this->addSql('DROP TABLE users_groups');
        $this->addSql('DROP TABLE user_access');
        $this->addSql('DROP TABLE user_group');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP INDEX UNIQ_8D93D6491C109075 ON [user]');
        $this->addSql('ALTER TABLE [user] ALTER COLUMN roles NVARCHAR(255) NOT NULL');
        $this->addSql('EXEC sp_dropextendedproperty N\'MS_Description\', N\'SCHEMA\', \'dbo\', N\'TABLE\', \'user\', N\'COLUMN\', \'roles\'');
        $this->addSql('CREATE NONCLUSTERED INDEX IDX_8D93D6491C109075 ON [user] (personnel_id)');
        $this->addSql('ALTER TABLE dom_sous_type_document ALTER COLUMN code_sous_type NVARCHAR(50) NOT NULL');
    }
}
