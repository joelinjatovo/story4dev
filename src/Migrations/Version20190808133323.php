<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190808133323 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, budget DOUBLE PRECISION DEFAULT NULL, INDEX IDX_AC74095A166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE goal (id INT AUTO_INCREMENT NOT NULL, indicator_id INT DEFAULT NULL, iteration_id INT DEFAULT NULL, value DOUBLE PRECISION NOT NULL, INDEX IDX_FCDCEB2E4402854A (indicator_id), INDEX IDX_FCDCEB2E1B48E3E1 (iteration_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE indicator (id INT AUTO_INCREMENT NOT NULL, activity_id INT DEFAULT NULL, unit_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, INDEX IDX_D1349DB381C06096 (activity_id), INDEX IDX_D1349DB3F8BD700D (unit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE iteration (id INT AUTO_INCREMENT NOT NULL, project_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, INDEX IDX_EED1D11D166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE periodicity (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, delay INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, periodicity_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, budget DOUBLE PRECISION DEFAULT NULL, start_at DATETIME DEFAULT NULL, end_at DATETIME DEFAULT NULL, INDEX IDX_2FB3D0EE33E79D0D (periodicity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE unit (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, label VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095A166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE goal ADD CONSTRAINT FK_FCDCEB2E4402854A FOREIGN KEY (indicator_id) REFERENCES indicator (id)');
        $this->addSql('ALTER TABLE goal ADD CONSTRAINT FK_FCDCEB2E1B48E3E1 FOREIGN KEY (iteration_id) REFERENCES iteration (id)');
        $this->addSql('ALTER TABLE indicator ADD CONSTRAINT FK_D1349DB381C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE indicator ADD CONSTRAINT FK_D1349DB3F8BD700D FOREIGN KEY (unit_id) REFERENCES unit (id)');
        $this->addSql('ALTER TABLE iteration ADD CONSTRAINT FK_EED1D11D166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE33E79D0D FOREIGN KEY (periodicity_id) REFERENCES periodicity (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE indicator DROP FOREIGN KEY FK_D1349DB381C06096');
        $this->addSql('ALTER TABLE goal DROP FOREIGN KEY FK_FCDCEB2E4402854A');
        $this->addSql('ALTER TABLE goal DROP FOREIGN KEY FK_FCDCEB2E1B48E3E1');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE33E79D0D');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095A166D1F9C');
        $this->addSql('ALTER TABLE iteration DROP FOREIGN KEY FK_EED1D11D166D1F9C');
        $this->addSql('ALTER TABLE indicator DROP FOREIGN KEY FK_D1349DB3F8BD700D');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE goal');
        $this->addSql('DROP TABLE indicator');
        $this->addSql('DROP TABLE iteration');
        $this->addSql('DROP TABLE periodicity');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE unit');
        $this->addSql('DROP TABLE user');
    }
}
