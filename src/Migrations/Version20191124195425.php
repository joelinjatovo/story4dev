<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191124195425 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE activities_favorites (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, activity_id INT DEFAULT NULL, INDEX IDX_C8CF3ED7A76ED395 (user_id), INDEX IDX_C8CF3ED781C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indicators_favorites (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, indicator_id INT DEFAULT NULL, INDEX IDX_627D80AA76ED395 (user_id), INDEX IDX_627D80A4402854A (indicator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activities_favorites ADD CONSTRAINT FK_C8CF3ED7A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activities_favorites ADD CONSTRAINT FK_C8CF3ED781C06096 FOREIGN KEY (activity_id) REFERENCES activities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE indicators_favorites ADD CONSTRAINT FK_627D80AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE indicators_favorites ADD CONSTRAINT FK_627D80A4402854A FOREIGN KEY (indicator_id) REFERENCES indicators (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE graphes RENAME INDEX idx_94505dc166d1f9c TO IDX_CDB8DA42166D1F9C');
        $this->addSql('ALTER TABLE graphes RENAME INDEX idx_94505dcf675f31b TO IDX_CDB8DA42F675F31B');
        $this->addSql('ALTER TABLE axes RENAME INDEX idx_6c6a1e2c99134837 TO IDX_29BAC9F699134837');
        $this->addSql('ALTER TABLE axes RENAME INDEX idx_6c6a1e2cf675f31b TO IDX_29BAC9F6F675F31B');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE activities_favorites');
        $this->addSql('DROP TABLE indicators_favorites');
        $this->addSql('ALTER TABLE axes RENAME INDEX idx_29bac9f6f675f31b TO IDX_6C6A1E2CF675F31B');
        $this->addSql('ALTER TABLE axes RENAME INDEX idx_29bac9f699134837 TO IDX_6C6A1E2C99134837');
        $this->addSql('ALTER TABLE graphes RENAME INDEX idx_cdb8da42f675f31b TO IDX_94505DCF675F31B');
        $this->addSql('ALTER TABLE graphes RENAME INDEX idx_cdb8da42166d1f9c TO IDX_94505DC166D1F9C');
    }
}
