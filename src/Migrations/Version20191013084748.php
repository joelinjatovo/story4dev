<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191013084748 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE files_tags (id INT AUTO_INCREMENT NOT NULL, file_id INT DEFAULT NULL, tag_id INT DEFAULT NULL, INDEX IDX_E874914793CB796C (file_id), INDEX IDX_E8749147BAD26311 (tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE files_tags ADD CONSTRAINT FK_E874914793CB796C FOREIGN KEY (file_id) REFERENCES files (id)');
        $this->addSql('ALTER TABLE files_tags ADD CONSTRAINT FK_E8749147BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id)');
        $this->addSql('ALTER TABLE downloads RENAME INDEX idx_781a8270a76ed395 TO IDX_4B73A4B5A76ED395');
        $this->addSql('ALTER TABLE downloads RENAME INDEX idx_781a827093cb796c TO IDX_4B73A4B593CB796C');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE files_tags DROP FOREIGN KEY FK_E8749147BAD26311');
        $this->addSql('DROP TABLE files_tags');
        $this->addSql('DROP TABLE tags');
        $this->addSql('ALTER TABLE downloads RENAME INDEX idx_4b73a4b593cb796c TO IDX_781A827093CB796C');
        $this->addSql('ALTER TABLE downloads RENAME INDEX idx_4b73a4b5a76ed395 TO IDX_781A8270A76ED395');
    }
}
