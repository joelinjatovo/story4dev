<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191001095830 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activity_contributions DROP FOREIGN KEY FK_77EBD73A232D562B');
        $this->addSql('DROP INDEX IDX_77EBD73A232D562B ON activity_contributions');
        $this->addSql('ALTER TABLE activity_contributions CHANGE object_id activity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE activity_contributions ADD CONSTRAINT FK_77EBD73A81C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('CREATE INDEX IDX_77EBD73A81C06096 ON activity_contributions (activity_id)');
        $this->addSql('ALTER TABLE project_contributions DROP FOREIGN KEY FK_A2A889F8232D562B');
        $this->addSql('DROP INDEX IDX_A2A889F8232D562B ON project_contributions');
        $this->addSql('ALTER TABLE project_contributions CHANGE object_id project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project_contributions ADD CONSTRAINT FK_A2A889F8166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('CREATE INDEX IDX_A2A889F8166D1F9C ON project_contributions (project_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activity_contributions DROP FOREIGN KEY FK_77EBD73A81C06096');
        $this->addSql('DROP INDEX IDX_77EBD73A81C06096 ON activity_contributions');
        $this->addSql('ALTER TABLE activity_contributions CHANGE activity_id object_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE activity_contributions ADD CONSTRAINT FK_77EBD73A232D562B FOREIGN KEY (object_id) REFERENCES activities (id)');
        $this->addSql('CREATE INDEX IDX_77EBD73A232D562B ON activity_contributions (object_id)');
        $this->addSql('ALTER TABLE project_contributions DROP FOREIGN KEY FK_A2A889F8166D1F9C');
        $this->addSql('DROP INDEX IDX_A2A889F8166D1F9C ON project_contributions');
        $this->addSql('ALTER TABLE project_contributions CHANGE project_id object_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE project_contributions ADD CONSTRAINT FK_A2A889F8232D562B FOREIGN KEY (object_id) REFERENCES projects (id)');
        $this->addSql('CREATE INDEX IDX_A2A889F8232D562B ON project_contributions (object_id)');
    }
}
