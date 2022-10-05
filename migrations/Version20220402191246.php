<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220402191246 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE operator (id INT AUTO_INCREMENT NOT NULL, yearbook_id INT NOT NULL, password VARCHAR(255) NOT NULL, INDEX IDX_D7A6A781F7857D5B (yearbook_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE operator_classroom (operator_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_8FA5CF5A584598A3 (operator_id), INDEX IDX_8FA5CF5A6278D5A8 (classroom_id), PRIMARY KEY(operator_id, classroom_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE operator ADD CONSTRAINT FK_D7A6A781F7857D5B FOREIGN KEY (yearbook_id) REFERENCES yearbook (id)');
        $this->addSql('ALTER TABLE operator_classroom ADD CONSTRAINT FK_8FA5CF5A584598A3 FOREIGN KEY (operator_id) REFERENCES operator (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE operator_classroom ADD CONSTRAINT FK_8FA5CF5A6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE operator_classroom DROP FOREIGN KEY FK_8FA5CF5A584598A3');
        $this->addSql('DROP TABLE operator');
        $this->addSql('DROP TABLE operator_classroom');
    }
}
