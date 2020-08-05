<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200805080253 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admins_documents DROP FOREIGN KEY admins_documents_ibfk_1');
        $this->addSql('ALTER TABLE admins_documents DROP FOREIGN KEY admins_documents_ibfk_2');
        $this->addSql('ALTER TABLE admins_documents ADD CONSTRAINT FK_AB946800642B8210 FOREIGN KEY (admin_id) REFERENCES admin (admin_id)');
        $this->addSql('ALTER TABLE admins_documents ADD CONSTRAINT FK_AB946800C33F7837 FOREIGN KEY (document_id) REFERENCES document (document_id)');
        $this->addSql('ALTER TABLE document CHANGE cinr cinr VARCHAR(3) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX cnp ON document (CNP)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admins_documents DROP FOREIGN KEY FK_AB946800642B8210');
        $this->addSql('ALTER TABLE admins_documents DROP FOREIGN KEY FK_AB946800C33F7837');
        $this->addSql('ALTER TABLE admins_documents ADD CONSTRAINT admins_documents_ibfk_1 FOREIGN KEY (admin_id) REFERENCES admin (admin_id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE admins_documents ADD CONSTRAINT admins_documents_ibfk_2 FOREIGN KEY (document_id) REFERENCES document (document_id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('DROP INDEX cnp ON document');
        $this->addSql('ALTER TABLE document CHANGE cinr cinr VARCHAR(6) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
