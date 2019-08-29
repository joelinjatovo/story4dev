<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190829103646 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE activities_files (id INT AUTO_INCREMENT NOT NULL, file_id INT NOT NULL, activity_id INT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_C5BC638A93CB796C (file_id), INDEX IDX_C5BC638A81C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reports_files (id INT AUTO_INCREMENT NOT NULL, file_id INT NOT NULL, activity_id INT NOT NULL, report_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_ADE32AB293CB796C (file_id), INDEX IDX_ADE32AB281C06096 (activity_id), INDEX IDX_ADE32AB24BD2A4C0 (report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activities_files ADD CONSTRAINT FK_C5BC638A93CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE activities_files ADD CONSTRAINT FK_C5BC638A81C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE reports_files ADD CONSTRAINT FK_ADE32AB293CB796C FOREIGN KEY (file_id) REFERENCES file (id)');
        $this->addSql('ALTER TABLE reports_files ADD CONSTRAINT FK_ADE32AB281C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE reports_files ADD CONSTRAINT FK_ADE32AB24BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE goals DROP INDEX UNIQ_C7241E2F1B48E3E1, ADD INDEX IDX_C7241E2F1B48E3E1 (iteration_id)');
        $this->addSql('ALTER TABLE iterations DROP FOREIGN KEY FK_78E67203667D1AFE');
        $this->addSql('DROP INDEX UNIQ_78E67203667D1AFE ON iterations');
        $this->addSql('ALTER TABLE iterations DROP goal_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE activities_files');
        $this->addSql('DROP TABLE reports_files');
        $this->addSql('ALTER TABLE goals DROP INDEX IDX_C7241E2F1B48E3E1, ADD UNIQUE INDEX UNIQ_C7241E2F1B48E3E1 (iteration_id)');
        $this->addSql('ALTER TABLE iterations ADD goal_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE iterations ADD CONSTRAINT FK_78E67203667D1AFE FOREIGN KEY (goal_id) REFERENCES goals (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_78E67203667D1AFE ON iterations (goal_id)');
    }
}
