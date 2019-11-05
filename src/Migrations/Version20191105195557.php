<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191105195557 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE5166D1F9C');
        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE5166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4F675F31B');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4F675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_contributions DROP FOREIGN KEY FK_A2A889F8166D1F9C');
        $this->addSql('ALTER TABLE project_contributions DROP FOREIGN KEY FK_A2A889F8A76ED395');
        $this->addSql('ALTER TABLE project_contributions ADD CONSTRAINT FK_A2A889F8166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_contributions ADD CONSTRAINT FK_A2A889F8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE indicators DROP FOREIGN KEY FK_49B719A081C06096');
        $this->addSql('ALTER TABLE indicators DROP FOREIGN KEY FK_49B719A0F8BD700D');
        $this->addSql('ALTER TABLE indicators ADD CONSTRAINT FK_49B719A081C06096 FOREIGN KEY (activity_id) REFERENCES activities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE indicators ADD CONSTRAINT FK_49B719A0F8BD700D FOREIGN KEY (unit_id) REFERENCES units (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA74581C06096');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA74581C06096 FOREIGN KEY (activity_id) REFERENCES activities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_contributions DROP FOREIGN KEY FK_77EBD73A81C06096');
        $this->addSql('ALTER TABLE activity_contributions DROP FOREIGN KEY FK_77EBD73AA76ED395');
        $this->addSql('ALTER TABLE activity_contributions ADD CONSTRAINT FK_77EBD73A81C06096 FOREIGN KEY (activity_id) REFERENCES activities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_contributions ADD CONSTRAINT FK_77EBD73AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activities_files DROP FOREIGN KEY FK_C5BC638A81C06096');
        $this->addSql('ALTER TABLE activities_files DROP FOREIGN KEY FK_C5BC638A93CB796C');
        $this->addSql('ALTER TABLE activities_files CHANGE file_id file_id INT DEFAULT NULL, CHANGE activity_id activity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE activities_files ADD CONSTRAINT FK_C5BC638A81C06096 FOREIGN KEY (activity_id) REFERENCES activities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activities_files ADD CONSTRAINT FK_C5BC638A93CB796C FOREIGN KEY (file_id) REFERENCES files (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE downloads DROP FOREIGN KEY FK_4B73A4B593CB796C');
        $this->addSql('ALTER TABLE downloads DROP FOREIGN KEY FK_4B73A4B5A76ED395');
        $this->addSql('ALTER TABLE downloads CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B593CB796C FOREIGN KEY (file_id) REFERENCES files (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE files_tags DROP FOREIGN KEY FK_E874914793CB796C');
        $this->addSql('ALTER TABLE files_tags DROP FOREIGN KEY FK_E8749147BAD26311');
        $this->addSql('ALTER TABLE files_tags ADD CONSTRAINT FK_E874914793CB796C FOREIGN KEY (file_id) REFERENCES files (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE files_tags ADD CONSTRAINT FK_E8749147BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE goals DROP FOREIGN KEY FK_C7241E2F1B48E3E1');
        $this->addSql('ALTER TABLE goals DROP FOREIGN KEY FK_C7241E2F4402854A');
        $this->addSql('ALTER TABLE goals ADD CONSTRAINT FK_C7241E2F1B48E3E1 FOREIGN KEY (iteration_id) REFERENCES iterations (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE goals ADD CONSTRAINT FK_C7241E2F4402854A FOREIGN KEY (indicator_id) REFERENCES indicators (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE iterations DROP FOREIGN KEY FK_78E67203166D1F9C');
        $this->addSql('ALTER TABLE iterations DROP FOREIGN KEY FK_78E67203F675F31B');
        $this->addSql('ALTER TABLE iterations ADD CONSTRAINT FK_78E67203166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE iterations ADD CONSTRAINT FK_78E67203F675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reports_files DROP FOREIGN KEY FK_ADE32AB24BD2A4C0');
        $this->addSql('ALTER TABLE reports_files DROP FOREIGN KEY FK_ADE32AB281C06096');
        $this->addSql('ALTER TABLE reports_files DROP FOREIGN KEY FK_ADE32AB293CB796C');
        $this->addSql('ALTER TABLE reports_files CHANGE file_id file_id INT DEFAULT NULL, CHANGE report_id report_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE reports_files ADD CONSTRAINT FK_ADE32AB24BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reports_files ADD CONSTRAINT FK_ADE32AB281C06096 FOREIGN KEY (activity_id) REFERENCES activities (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reports_files ADD CONSTRAINT FK_ADE32AB293CB796C FOREIGN KEY (file_id) REFERENCES files (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E4144402854A');
        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E4144BD2A4C0');
        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E414F675F31B');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E4144402854A FOREIGN KEY (indicator_id) REFERENCES indicators (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E4144BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E414F675F31B FOREIGN KEY (author_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tags DROP FOREIGN KEY FK_6FBC9426166D1F9C');
        $this->addSql('ALTER TABLE tags ADD CONSTRAINT FK_6FBC9426166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE activities DROP FOREIGN KEY FK_B5F1AFE5166D1F9C');
        $this->addSql('ALTER TABLE activities ADD CONSTRAINT FK_B5F1AFE5166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE activities_files DROP FOREIGN KEY FK_C5BC638A93CB796C');
        $this->addSql('ALTER TABLE activities_files DROP FOREIGN KEY FK_C5BC638A81C06096');
        $this->addSql('ALTER TABLE activities_files CHANGE file_id file_id INT NOT NULL, CHANGE activity_id activity_id INT NOT NULL');
        $this->addSql('ALTER TABLE activities_files ADD CONSTRAINT FK_C5BC638A93CB796C FOREIGN KEY (file_id) REFERENCES files (id)');
        $this->addSql('ALTER TABLE activities_files ADD CONSTRAINT FK_C5BC638A81C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE activity_contributions DROP FOREIGN KEY FK_77EBD73AA76ED395');
        $this->addSql('ALTER TABLE activity_contributions DROP FOREIGN KEY FK_77EBD73A81C06096');
        $this->addSql('ALTER TABLE activity_contributions ADD CONSTRAINT FK_77EBD73AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE activity_contributions ADD CONSTRAINT FK_77EBD73A81C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE downloads DROP FOREIGN KEY FK_4B73A4B5A76ED395');
        $this->addSql('ALTER TABLE downloads DROP FOREIGN KEY FK_4B73A4B593CB796C');
        $this->addSql('ALTER TABLE downloads CHANGE user_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B5A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE downloads ADD CONSTRAINT FK_4B73A4B593CB796C FOREIGN KEY (file_id) REFERENCES files (id)');
        $this->addSql('ALTER TABLE files_tags DROP FOREIGN KEY FK_E874914793CB796C');
        $this->addSql('ALTER TABLE files_tags DROP FOREIGN KEY FK_E8749147BAD26311');
        $this->addSql('ALTER TABLE files_tags ADD CONSTRAINT FK_E874914793CB796C FOREIGN KEY (file_id) REFERENCES files (id)');
        $this->addSql('ALTER TABLE files_tags ADD CONSTRAINT FK_E8749147BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id)');
        $this->addSql('ALTER TABLE goals DROP FOREIGN KEY FK_C7241E2F4402854A');
        $this->addSql('ALTER TABLE goals DROP FOREIGN KEY FK_C7241E2F1B48E3E1');
        $this->addSql('ALTER TABLE goals ADD CONSTRAINT FK_C7241E2F4402854A FOREIGN KEY (indicator_id) REFERENCES indicators (id)');
        $this->addSql('ALTER TABLE goals ADD CONSTRAINT FK_C7241E2F1B48E3E1 FOREIGN KEY (iteration_id) REFERENCES iterations (id)');
        $this->addSql('ALTER TABLE indicators DROP FOREIGN KEY FK_49B719A081C06096');
        $this->addSql('ALTER TABLE indicators DROP FOREIGN KEY FK_49B719A0F8BD700D');
        $this->addSql('ALTER TABLE indicators ADD CONSTRAINT FK_49B719A081C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE indicators ADD CONSTRAINT FK_49B719A0F8BD700D FOREIGN KEY (unit_id) REFERENCES units (id)');
        $this->addSql('ALTER TABLE iterations DROP FOREIGN KEY FK_78E67203F675F31B');
        $this->addSql('ALTER TABLE iterations DROP FOREIGN KEY FK_78E67203166D1F9C');
        $this->addSql('ALTER TABLE iterations ADD CONSTRAINT FK_78E67203F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE iterations ADD CONSTRAINT FK_78E67203166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE project_contributions DROP FOREIGN KEY FK_A2A889F8A76ED395');
        $this->addSql('ALTER TABLE project_contributions DROP FOREIGN KEY FK_A2A889F8166D1F9C');
        $this->addSql('ALTER TABLE project_contributions ADD CONSTRAINT FK_A2A889F8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE project_contributions ADD CONSTRAINT FK_A2A889F8166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
        $this->addSql('ALTER TABLE projects DROP FOREIGN KEY FK_5C93B3A4F675F31B');
        $this->addSql('ALTER TABLE projects ADD CONSTRAINT FK_5C93B3A4F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA74581C06096');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA74581C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE reports_files DROP FOREIGN KEY FK_ADE32AB293CB796C');
        $this->addSql('ALTER TABLE reports_files DROP FOREIGN KEY FK_ADE32AB281C06096');
        $this->addSql('ALTER TABLE reports_files DROP FOREIGN KEY FK_ADE32AB24BD2A4C0');
        $this->addSql('ALTER TABLE reports_files CHANGE file_id file_id INT NOT NULL, CHANGE report_id report_id INT NOT NULL');
        $this->addSql('ALTER TABLE reports_files ADD CONSTRAINT FK_ADE32AB293CB796C FOREIGN KEY (file_id) REFERENCES files (id)');
        $this->addSql('ALTER TABLE reports_files ADD CONSTRAINT FK_ADE32AB281C06096 FOREIGN KEY (activity_id) REFERENCES activities (id)');
        $this->addSql('ALTER TABLE reports_files ADD CONSTRAINT FK_ADE32AB24BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E414F675F31B');
        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E4144BD2A4C0');
        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E4144402854A');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E414F675F31B FOREIGN KEY (author_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E4144BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E4144402854A FOREIGN KEY (indicator_id) REFERENCES indicators (id)');
        $this->addSql('ALTER TABLE tags DROP FOREIGN KEY FK_6FBC9426166D1F9C');
        $this->addSql('ALTER TABLE tags ADD CONSTRAINT FK_6FBC9426166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id)');
    }
}
