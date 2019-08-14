<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190814113515 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE activities (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, project_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, budget DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_B5F1AFE5F675F31B (author_id), INDEX IDX_B5F1AFE5166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE goals (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, indicator_id INT DEFAULT NULL, iteration_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_C7241E2FF675F31B (author_id), INDEX IDX_C7241E2F4402854A (indicator_id), INDEX IDX_C7241E2F1B48E3E1 (iteration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indicators (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, activity_id INT DEFAULT NULL, unit_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_49B719A0F675F31B (author_id), INDEX IDX_49B719A081C06096 (activity_id), INDEX IDX_49B719A0F8BD700D (unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE iterations (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, project_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_78E67203F675F31B (author_id), INDEX IDX_78E67203166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metas (id INT AUTO_INCREMENT NOT NULL, meta_key VARCHAR(255) NOT NULL, meta_value VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, meta_type VARCHAR(255) NOT NULL, object_id INT DEFAULT NULL, INDEX IDX_4D6AF93C232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE options (id INT AUTO_INCREMENT NOT NULL, option_key VARCHAR(255) NOT NULL, option_value VARCHAR(255) NOT NULL, autoload TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, option_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE periodicities (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, delay INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_2F071C5CF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, periodicity_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, budget DOUBLE PRECISION DEFAULT NULL, start_at DATETIME DEFAULT NULL, end_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_5C93B3A4F675F31B (author_id), INDEX IDX_5C93B3A433E79D0D (periodicity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reports (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, project_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, synced_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_F11FA745F675F31B (author_id), INDEX IDX_F11FA745166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE results (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, report_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_9FA3E414F675F31B (author_id), INDEX IDX_9FA3E4144BD2A4C0 (report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE units (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, label VARCHAR(10) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_E9B07449F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, full_name VARCHAR(255) DEFAULT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE5F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE5166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE goals ADD CONSTRAINT FK_C7241E2FF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE goals ADD CONSTRAINT FK_C7241E2F4402854A FOREIGN KEY (indicator_id) REFERENCES indicators (id)');
        $this->addSql('ALTER TABLE goals ADD CONSTRAINT FK_C7241E2F1B48E3E1 FOREIGN KEY (iteration_id) REFERENCES iterations (id)');
        $this->addSql('ALTER TABLE indicators ADD CONSTRAINT FK_49B719A0F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE indicators ADD CONSTRAINT FK_49B719A081C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE indicators ADD CONSTRAINT FK_49B719A0F8BD700D FOREIGN KEY (unit_id) REFERENCES units (id)');
        $this->addSql('ALTER TABLE iterations ADD CONSTRAINT FK_78E67203F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE iterations ADD CONSTRAINT FK_78E67203166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE periodicities ADD CONSTRAINT FK_2F071C5CF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A433E79D0D FOREIGN KEY (periodicity_id) REFERENCES periodicities (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E414F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E4144BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE units ADD CONSTRAINT FK_E9B07449F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE indicators DROP FOREIGN KEY FK_49B719A081C06096');
        $this->addSql('ALTER TABLE goals DROP FOREIGN KEY FK_C7241E2F4402854A');
        $this->addSql('ALTER TABLE goals DROP FOREIGN KEY FK_C7241E2F1B48E3E1');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A433E79D0D');
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE5166D1F9C');
        $this->addSql('ALTER TABLE iterations DROP FOREIGN KEY FK_78E67203166D1F9C');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745166D1F9C');
        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E4144BD2A4C0');
        $this->addSql('ALTER TABLE indicators DROP FOREIGN KEY FK_49B719A0F8BD700D');
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE5F675F31B');
        $this->addSql('ALTER TABLE goals DROP FOREIGN KEY FK_C7241E2FF675F31B');
        $this->addSql('ALTER TABLE indicators DROP FOREIGN KEY FK_49B719A0F675F31B');
        $this->addSql('ALTER TABLE iterations DROP FOREIGN KEY FK_78E67203F675F31B');
        $this->addSql('ALTER TABLE periodicities DROP FOREIGN KEY FK_2F071C5CF675F31B');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4F675F31B');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745F675F31B');
        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E414F675F31B');
        $this->addSql('ALTER TABLE units DROP FOREIGN KEY FK_E9B07449F675F31B');
        $this->addSql('DROP TABLE activities');
        $this->addSql('DROP TABLE goals');
        $this->addSql('DROP TABLE indicators');
        $this->addSql('DROP TABLE iterations');
        $this->addSql('DROP TABLE metas');
        $this->addSql('DROP TABLE options');
        $this->addSql('DROP TABLE periodicities');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE reports');
        $this->addSql('DROP TABLE results');
        $this->addSql('DROP TABLE units');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE ext_log_entries');
    }
}
