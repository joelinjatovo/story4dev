<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191122125121 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE axes (id INT AUTO_INCREMENT NOT NULL, graph_id INT DEFAULT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, start_at DATETIME DEFAULT NULL, end_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_6C6A1E2C99134837 (graph_id), INDEX IDX_6C6A1E2CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE graphes (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_94505DC166D1F9C (project_id), INDEX IDX_94505DCF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE axes ADD CONSTRAINT FK_6C6A1E2C99134837 FOREIGN KEY (graph_id) REFERENCES graphes (id)');
        $this->addSql('ALTER TABLE axes ADD CONSTRAINT FK_6C6A1E2CF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE graphes ADD CONSTRAINT FK_94505DC166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE graphes ADD CONSTRAINT FK_94505DCF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE axes DROP FOREIGN KEY FK_6C6A1E2C99134837');
        $this->addSql('DROP TABLE axes');
        $this->addSql('DROP TABLE graphes');
    }
}
