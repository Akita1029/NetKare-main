<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220328194542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dealer DROP FOREIGN KEY FK_17A33902BF396750');
        $this->addSql('CREATE TABLE `admin` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_880E0D76E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE school DROP FOREIGN KEY FK_F99EDABB7E3C61F9');
        $this->addSql('ALTER TABLE dealer ADD email VARCHAR(180) NOT NULL, ADD password VARCHAR(255) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE school ADD CONSTRAINT FK_F99EDABB7E3C61F9 FOREIGN KEY (owner_id) REFERENCES dealer (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17A33902E7927C74 ON dealer (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, roles JSON NOT NULL, password VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, is_verified TINYINT(1) NOT NULL, discr VARCHAR(255) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP INDEX UNIQ_17A33902E7927C74 ON dealer');
        $this->addSql('ALTER TABLE school DROP FOREIGN KEY FK_F99EDABB7E3C61F9');
        $this->addSql('ALTER TABLE dealer DROP email, DROP password, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE dealer ADD CONSTRAINT FK_17A33902BF396750 FOREIGN KEY (id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17A33902E7927C74 ON dealer (email)');
    }
}
