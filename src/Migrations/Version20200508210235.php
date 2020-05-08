<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200508210235 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE categories (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(100) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_project (project_parent INT NOT NULL, project_child INT NOT NULL, INDEX IDX_B9ADDC8B482E9439 (project_parent), INDEX IDX_B9ADDC8B51CBC4B6 (project_child), PRIMARY KEY(project_parent, project_child)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_project ADD CONSTRAINT FK_B9ADDC8B482E9439 FOREIGN KEY (project_parent) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_project ADD CONSTRAINT FK_B9ADDC8B51CBC4B6 FOREIGN KEY (project_child) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activities ADD category_id INT DEFAULT NULL, CHANGE address_id address_id INT DEFAULT NULL, CHANGE author_id author_id INT DEFAULT NULL, CHANGE project_id project_id INT DEFAULT NULL, CHANGE parent_id parent_id INT DEFAULT NULL, CHANGE budget budget DOUBLE PRECISION DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL, CHANGE contact_email contact_email VARCHAR(255) DEFAULT NULL, CHANGE contact_phone contact_phone VARCHAR(255) DEFAULT NULL, CHANGE contact_address contact_address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE512469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_B5F1AFE512469DE2 ON activities (category_id)');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4727ACA70');
        $this->addSql('DROP INDEX FK_5C93B3A4727ACA70 ON projects');
        $this->addSql('ALTER TABLE projects DROP parent_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE512469DE2');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE project_project');
        $this->addSql('DROP INDEX IDX_B5F1AFE512469DE2 ON activities');
        $this->addSql('ALTER TABLE activities DROP category_id');
        $this->addSql('ALTER TABLE projects ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4727ACA70 FOREIGN KEY (parent_id) REFERENCES projects (id)');
        $this->addSql('CREATE INDEX FK_5C93B3A4727ACA70 ON projects (parent_id)');
    }
}
