<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200724181412 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admins_documents ADD CONSTRAINT FK_AB946800642B8210 FOREIGN KEY (admin_id) REFERENCES admin (admin_id)');
        $this->addSql('ALTER TABLE admins_documents ADD CONSTRAINT FK_AB946800C33F7837 FOREIGN KEY (document_id) REFERENCES document (document_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admins_documents DROP FOREIGN KEY FK_AB946800642B8210');
        $this->addSql('ALTER TABLE admins_documents DROP FOREIGN KEY FK_AB946800C33F7837');
    }
}
