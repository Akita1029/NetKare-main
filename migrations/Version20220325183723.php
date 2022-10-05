<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220325183723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE school DROP FOREIGN KEY FK_F99EDABBBF396750');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43C32A47EE');
        $this->addSql('ALTER TABLE classroom DROP FOREIGN KEY FK_497D309DC32A47EE');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398C32A47EE');
        $this->addSql('ALTER TABLE `yearbook` DROP FOREIGN KEY FK_A5A825EDC32A47EE');
        $this->addSql('ALTER TABLE school ADD email VARCHAR(255) DEFAULT NULL, ADD password VARCHAR(255) DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE address address LONGTEXT DEFAULT NULL, CHANGE phone phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE classroom ADD CONSTRAINT FK_497D309DC32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE yearbook ADD CONSTRAINT FK_A5A825EDC32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE school DROP FOREIGN KEY FK_F99EDABBBF396750');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43C32A47EE');
        $this->addSql('ALTER TABLE classroom DROP FOREIGN KEY FK_497D309DC32A47EE');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398C32A47EE');
        $this->addSql('ALTER TABLE `yearbook` DROP FOREIGN KEY FK_A5A825EDC32A47EE');
        $this->addSql('ALTER TABLE school DROP email, DROP password, CHANGE id id INT NOT NULL, CHANGE address address LONGTEXT NOT NULL, CHANGE phone phone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE school ADD CONSTRAINT FK_F99EDABBBF396750 FOREIGN KEY (id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE classroom ADD CONSTRAINT FK_497D309DC32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE yearbook ADD CONSTRAINT FK_A5A825EDC32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
    }
}
