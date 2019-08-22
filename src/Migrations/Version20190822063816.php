<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190822063816 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE results ADD indicator_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E4144402854A FOREIGN KEY (indicator_id) REFERENCES indicators (id)');
        $this->addSql('CREATE INDEX IDX_9FA3E4144402854A ON results (indicator_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E4144402854A');
        $this->addSql('DROP INDEX IDX_9FA3E4144402854A ON results');
        $this->addSql('ALTER TABLE results DROP indicator_id');
    }
}
