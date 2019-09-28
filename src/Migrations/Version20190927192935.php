<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190927192935 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE units ADD project_id INT DEFAULT NULL, ADD main TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE units ADD CONSTRAINT FK_E9B07449166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('CREATE INDEX IDX_E9B07449166D1F9C ON units (project_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE units DROP FOREIGN KEY FK_E9B07449166D1F9C');
        $this->addSql('DROP INDEX IDX_E9B07449166D1F9C ON units');
        $this->addSql('ALTER TABLE units DROP project_id, DROP main');
    }
}
