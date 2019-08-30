<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190830184633 extends AbstractMigration
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
        $this->addSql('CREATE TABLE contributions (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, roles JSON NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, contribution_type VARCHAR(255) NOT NULL, object_id INT DEFAULT NULL, INDEX IDX_76391EFEA76ED395 (user_id), INDEX IDX_76391EFE232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE goals (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, indicator_id INT DEFAULT NULL, iteration_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_C7241E2FF675F31B (author_id), INDEX IDX_C7241E2F4402854A (indicator_id), INDEX IDX_C7241E2F1B48E3E1 (iteration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE iterations (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, project_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_78E67203F675F31B (author_id), INDEX IDX_78E67203166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metas (id INT AUTO_INCREMENT NOT NULL, meta_key VARCHAR(255) NOT NULL, meta_value VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, meta_type VARCHAR(255) NOT NULL, object_id INT DEFAULT NULL, INDEX IDX_4D6AF93C232D562B (object_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE options (id INT AUTO_INCREMENT NOT NULL, option_key VARCHAR(255) NOT NULL, option_value VARCHAR(255) NOT NULL, autoload TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, option_type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reports_files (id INT AUTO_INCREMENT NOT NULL, file_id INT NOT NULL, activity_id INT DEFAULT NULL, report_id INT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_ADE32AB293CB796C (file_id), INDEX IDX_ADE32AB281C06096 (activity_id), INDEX IDX_ADE32AB24BD2A4C0 (report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE results (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, report_id INT DEFAULT NULL, indicator_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, value DOUBLE PRECISION NOT NULL, longitude VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, altitude VARCHAR(255) NOT NULL, location_title VARCHAR(255) DEFAULT NULL, INDEX IDX_9FA3E414F675F31B (author_id), INDEX IDX_9FA3E4144BD2A4C0 (report_id), INDEX IDX_9FA3E4144402854A (indicator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tokens (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL, INDEX IDX_AA5A118EA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, field, foreign_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), INDEX log_version_lookup_idx (object_id, object_class, version), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB ROW_FORMAT = DYNAMIC');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activities_files ADD CONSTRAINT FK_C5BC638A93CB796C FOREIGN KEY (file_id) REFERENCES files (id)');
        $this->addSql('ALTER TABLE activities_files ADD CONSTRAINT FK_C5BC638A81C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE contributions ADD CONSTRAINT FK_76391EFEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE goals ADD CONSTRAINT FK_C7241E2FF675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE goals ADD CONSTRAINT FK_C7241E2F4402854A FOREIGN KEY (indicator_id) REFERENCES indicators (id)');
        $this->addSql('ALTER TABLE goals ADD CONSTRAINT FK_C7241E2F1B48E3E1 FOREIGN KEY (iteration_id) REFERENCES iterations (id)');
        $this->addSql('ALTER TABLE iterations ADD CONSTRAINT FK_78E67203F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE iterations ADD CONSTRAINT FK_78E67203166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE reports_files ADD CONSTRAINT FK_ADE32AB293CB796C FOREIGN KEY (file_id) REFERENCES files (id)');
        $this->addSql('ALTER TABLE reports_files ADD CONSTRAINT FK_ADE32AB281C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE reports_files ADD CONSTRAINT FK_ADE32AB24BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E414F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E4144BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E4144402854A FOREIGN KEY (indicator_id) REFERENCES indicators (id)');
        $this->addSql('ALTER TABLE tokens ADD CONSTRAINT FK_AA5A118EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE activities ADD address_id INT DEFAULT NULL, ADD contact_email VARCHAR(255) DEFAULT NULL, ADD contact_phone VARCHAR(255) DEFAULT NULL, ADD contact_address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE5F5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B5F1AFE5F5B7AF75 ON activities (address_id)');
        $this->addSql('ALTER TABLE files ADD url VARCHAR(255) DEFAULT NULL, ADD is_external TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE projects ADD address_id INT DEFAULT NULL, ADD contact_email VARCHAR(255) DEFAULT NULL, ADD contact_phone VARCHAR(255) DEFAULT NULL, ADD contact_address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4F5B7AF75 FOREIGN KEY (address_id) REFERENCES addresses (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5C93B3A4F5B7AF75 ON projects (address_id)');
        $this->addSql('ALTER TABLE reports ADD longitude VARCHAR(255) NOT NULL, ADD latitude VARCHAR(255) NOT NULL, ADD altitude VARCHAR(255) NOT NULL, ADD location_title VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE goals DROP FOREIGN KEY FK_C7241E2F1B48E3E1');
        $this->addSql('DROP TABLE activities_files');
        $this->addSql('DROP TABLE contributions');
        $this->addSql('DROP TABLE goals');
        $this->addSql('DROP TABLE iterations');
        $this->addSql('DROP TABLE metas');
        $this->addSql('DROP TABLE options');
        $this->addSql('DROP TABLE reports_files');
        $this->addSql('DROP TABLE results');
        $this->addSql('DROP TABLE tokens');
        $this->addSql('DROP TABLE ext_translations');
        $this->addSql('DROP TABLE ext_log_entries');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE5F5B7AF75');
        $this->addSql('DROP INDEX UNIQ_B5F1AFE5F5B7AF75 ON activities');
        $this->addSql('ALTER TABLE activities DROP address_id, DROP contact_email, DROP contact_phone, DROP contact_address');
        $this->addSql('ALTER TABLE files DROP url, DROP is_external');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4F5B7AF75');
        $this->addSql('DROP INDEX UNIQ_5C93B3A4F5B7AF75 ON projects');
        $this->addSql('ALTER TABLE projects DROP address_id, DROP contact_email, DROP contact_phone, DROP contact_address');
        $this->addSql('ALTER TABLE reports DROP longitude, DROP latitude, DROP altitude, DROP location_title');
    }
}
