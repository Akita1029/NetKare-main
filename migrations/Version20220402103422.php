<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220402103422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE yearbook_student_video (id INT AUTO_INCREMENT NOT NULL, yearbook_id INT NOT NULL, student_id INT NOT NULL, youtube_video_id VARCHAR(255) NOT NULL, INDEX IDX_B375DF05F7857D5B (yearbook_id), INDEX IDX_B375DF05CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE yearbook_student_video ADD CONSTRAINT FK_B375DF05F7857D5B FOREIGN KEY (yearbook_id) REFERENCES yearbook (id)');
        $this->addSql('ALTER TABLE yearbook_student_video ADD CONSTRAINT FK_B375DF05CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE yearbook_student_video');
    }
}
