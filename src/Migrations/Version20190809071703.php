<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190809071703 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE33E79D0D');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A166D1F9C');
        $this->addSql('CREATE TABLE activities (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, budget DOUBLE PRECISION DEFAULT NULL, INDEX IDX_B5F1AFE5166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE goals (id INT AUTO_INCREMENT NOT NULL, indicator_id INT DEFAULT NULL, iteration_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_C7241E2F4402854A (indicator_id), INDEX IDX_C7241E2F1B48E3E1 (iteration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indicators (id INT AUTO_INCREMENT NOT NULL, activity_id INT DEFAULT NULL, unit_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_49B719A081C06096 (activity_id), INDEX IDX_49B719A0F8BD700D (unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE iterations (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, INDEX IDX_78E67203166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE metas (id INT AUTO_INCREMENT NOT NULL, meta_key VARCHAR(255) NOT NULL, meta_value VARCHAR(255) NOT NULL, meta_type VARCHAR(255) NOT NULL, object_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE perioicities (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, delay INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE projects (id INT AUTO_INCREMENT NOT NULL, periodicity_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, budget DOUBLE PRECISION DEFAULT NULL, start_at DATETIME DEFAULT NULL, end_at DATETIME DEFAULT NULL, INDEX IDX_5C93B3A433E79D0D (periodicity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE units (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, label VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE5166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE goals ADD CONSTRAINT FK_C7241E2F4402854A FOREIGN KEY (indicator_id) REFERENCES indicators (id)');
        $this->addSql('ALTER TABLE goals ADD CONSTRAINT FK_C7241E2F1B48E3E1 FOREIGN KEY (iteration_id) REFERENCES iterations (id)');
        $this->addSql('ALTER TABLE indicators ADD CONSTRAINT FK_49B719A081C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE indicators ADD CONSTRAINT FK_49B719A0F8BD700D FOREIGN KEY (unit_id) REFERENCES units (id)');
        $this->addSql('ALTER TABLE iterations ADD CONSTRAINT FK_78E67203166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A433E79D0D FOREIGN KEY (periodicity_id) REFERENCES perioicities (id)');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE periodicity');
        $this->addSql('DROP TABLE project');
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
        $this->addSql('ALTER TABLE indicators DROP FOREIGN KEY FK_49B719A0F8BD700D');
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, budget DOUBLE PRECISION DEFAULT NULL, INDEX IDX_AC74095A166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE periodicity (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, delay INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, periodicity_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, description VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, budget DOUBLE PRECISION DEFAULT NULL, start_at DATETIME DEFAULT NULL, end_at DATETIME DEFAULT NULL, INDEX IDX_2FB3D0EE33E79D0D (periodicity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE33E79D0D FOREIGN KEY (periodicity_id) REFERENCES periodicity (id)');
        $this->addSql('DROP TABLE activities');
        $this->addSql('DROP TABLE goals');
        $this->addSql('DROP TABLE indicators');
        $this->addSql('DROP TABLE iterations');
        $this->addSql('DROP TABLE metas');
        $this->addSql('DROP TABLE perioicities');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE units');
        $this->addSql('DROP TABLE users');
    }
}
