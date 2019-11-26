<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191126103418 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //$this->addSql('ALTER TABLE activities_favorites RENAME INDEX idx_c8cf3ed7a76ed395 TO IDX_2AF3E33A76ED395');
        //$this->addSql('ALTER TABLE activities_favorites RENAME INDEX idx_c8cf3ed781c06096 TO IDX_2AF3E3381C06096');
        $this->addSql('ALTER TABLE downloads ADD type VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE files ADD type VARCHAR(100) DEFAULT NULL');
        //$this->addSql('ALTER TABLE indicators_favorites RENAME INDEX idx_627d80aa76ed395 TO IDX_D6ED007A76ED395');
        //$this->addSql('ALTER TABLE indicators_favorites RENAME INDEX idx_627d80a4402854a TO IDX_D6ED0074402854A');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activities_favorites RENAME INDEX idx_2af3e3381c06096 TO IDX_C8CF3ED781C06096');
        $this->addSql('ALTER TABLE activities_favorites RENAME INDEX idx_2af3e33a76ed395 TO IDX_C8CF3ED7A76ED395');
        $this->addSql('ALTER TABLE downloads DROP type');
        $this->addSql('ALTER TABLE files DROP type');
        $this->addSql('ALTER TABLE indicators_favorites RENAME INDEX idx_d6ed0074402854a TO IDX_627D80A4402854A');
        $this->addSql('ALTER TABLE indicators_favorites RENAME INDEX idx_d6ed007a76ed395 TO IDX_627D80AA76ED395');
    }
}
