<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260105055757 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE badm (id INT IDENTITY NOT NULL, casier_emetteur_id INT, casier_destinataire_id INT, type_mouvement_id INT, statut_demande_id INT, agence_emetteur_id INT, service_emetteur_id INT, agence_debiteur_id INT, service_debiteur_id INT, numero_badm NVARCHAR(11) NOT NULL, id_materiel INT NOT NULL, motif_materiel NVARCHAR(100), etat_achat NVARCHAR(10) NOT NULL, date_mise_location DATE NOT NULL, cout_acquisition DOUBLE PRECISION NOT NULL, amortissement DOUBLE PRECISION NOT NULL, valeur_net_comptable DOUBLE PRECISION NOT NULL, nom_client NVARCHAR(50), modalite_paiement NVARCHAR(20), prix_vente_ht DOUBLE PRECISION NOT NULL, motif_mise_rebut NVARCHAR(100), heure_machine INT NOT NULL, km_machine INT NOT NULL, num_parc NVARCHAR(15), nom_image NVARCHAR(50), nom_fichier NVARCHAR(50), created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), createdBy INT, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX IDX_CBECF2C5D2A061C3 ON badm (casier_emetteur_id)');
        $this->addSql('CREATE INDEX IDX_CBECF2C57CF643D4 ON badm (casier_destinataire_id)');
        $this->addSql('CREATE INDEX IDX_CBECF2C56B927827 ON badm (type_mouvement_id)');
        $this->addSql('CREATE INDEX IDX_CBECF2C5F5225311 ON badm (statut_demande_id)');
        $this->addSql('CREATE INDEX IDX_CBECF2C56F410E1C ON badm (agence_emetteur_id)');
        $this->addSql('CREATE INDEX IDX_CBECF2C5B5730820 ON badm (service_emetteur_id)');
        $this->addSql('CREATE INDEX IDX_CBECF2C53EC7D81B ON badm (agence_debiteur_id)');
        $this->addSql('CREATE INDEX IDX_CBECF2C5E4F5DE27 ON badm (service_debiteur_id)');
        $this->addSql('CREATE INDEX IDX_CBECF2C5D3564642 ON badm (createdBy)');
        $this->addSql('CREATE TABLE type_mouvement (id INT IDENTITY NOT NULL, code_mouvement NVARCHAR(3) NOT NULL, description NVARCHAR(50) NOT NULL, created_at DATETIME2(6) NOT NULL, updated_at DATETIME2(6), PRIMARY KEY (id))');
        $this->addSql('ALTER TABLE badm ADD CONSTRAINT FK_CBECF2C5D2A061C3 FOREIGN KEY (casier_emetteur_id) REFERENCES casier (id)');
        $this->addSql('ALTER TABLE badm ADD CONSTRAINT FK_CBECF2C57CF643D4 FOREIGN KEY (casier_destinataire_id) REFERENCES casier (id)');
        $this->addSql('ALTER TABLE badm ADD CONSTRAINT FK_CBECF2C56B927827 FOREIGN KEY (type_mouvement_id) REFERENCES type_mouvement (id)');
        $this->addSql('ALTER TABLE badm ADD CONSTRAINT FK_CBECF2C5F5225311 FOREIGN KEY (statut_demande_id) REFERENCES statut_demande (id)');
        $this->addSql('ALTER TABLE badm ADD CONSTRAINT FK_CBECF2C56F410E1C FOREIGN KEY (agence_emetteur_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE badm ADD CONSTRAINT FK_CBECF2C5B5730820 FOREIGN KEY (service_emetteur_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE badm ADD CONSTRAINT FK_CBECF2C53EC7D81B FOREIGN KEY (agence_debiteur_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE badm ADD CONSTRAINT FK_CBECF2C5E4F5DE27 FOREIGN KEY (service_debiteur_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE badm ADD CONSTRAINT FK_CBECF2C5D3564642 FOREIGN KEY (createdBy) REFERENCES [user] (id)');
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
        $this->addSql('ALTER TABLE badm DROP CONSTRAINT FK_CBECF2C5D2A061C3');
        $this->addSql('ALTER TABLE badm DROP CONSTRAINT FK_CBECF2C57CF643D4');
        $this->addSql('ALTER TABLE badm DROP CONSTRAINT FK_CBECF2C56B927827');
        $this->addSql('ALTER TABLE badm DROP CONSTRAINT FK_CBECF2C5F5225311');
        $this->addSql('ALTER TABLE badm DROP CONSTRAINT FK_CBECF2C56F410E1C');
        $this->addSql('ALTER TABLE badm DROP CONSTRAINT FK_CBECF2C5B5730820');
        $this->addSql('ALTER TABLE badm DROP CONSTRAINT FK_CBECF2C53EC7D81B');
        $this->addSql('ALTER TABLE badm DROP CONSTRAINT FK_CBECF2C5E4F5DE27');
        $this->addSql('ALTER TABLE badm DROP CONSTRAINT FK_CBECF2C5D3564642');
        $this->addSql('DROP TABLE badm');
        $this->addSql('DROP TABLE type_mouvement');
    }
}
