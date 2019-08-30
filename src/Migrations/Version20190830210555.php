<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190830210555 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE results CHANGE longitude longitude VARCHAR(255) DEFAULT NULL, CHANGE latitude latitude VARCHAR(255) DEFAULT NULL, CHANGE altitude altitude VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE reports CHANGE longitude longitude VARCHAR(255) DEFAULT NULL, CHANGE latitude latitude VARCHAR(255) DEFAULT NULL, CHANGE altitude altitude VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE reports CHANGE longitude longitude VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE latitude latitude VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE altitude altitude VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE results CHANGE longitude longitude VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE latitude latitude VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE altitude altitude VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
