<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220404172505 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE yearbook_student_image ADD yearbook_id INT NOT NULL');
        $this->addSql('ALTER TABLE yearbook_student_image ADD CONSTRAINT FK_A8F0176F7857D5B FOREIGN KEY (yearbook_id) REFERENCES yearbook (id)');
        $this->addSql('CREATE INDEX IDX_A8F0176F7857D5B ON yearbook_student_image (yearbook_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE yearbook_student_image DROP FOREIGN KEY FK_A8F0176F7857D5B');
        $this->addSql('DROP INDEX IDX_A8F0176F7857D5B ON yearbook_student_image');
        $this->addSql('ALTER TABLE yearbook_student_image DROP yearbook_id');
    }
}
