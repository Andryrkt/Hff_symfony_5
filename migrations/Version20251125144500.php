<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251125144500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename type_document_id to typeDocumentId';
    }

    public function up(Schema $schema): void
    {
        // Rename column for SQL Server
        $this->addSql("EXEC sp_rename 'user_access.type_document_id', 'typeDocumentId', 'COLUMN'");
    }

    public function down(Schema $schema): void
    {
        // Revert rename
        $this->addSql("EXEC sp_rename 'user_access.typeDocumentId', 'type_document_id', 'COLUMN'");
    }
}
