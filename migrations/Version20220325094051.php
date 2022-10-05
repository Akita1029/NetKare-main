<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220325094051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE album (id INT AUTO_INCREMENT NOT NULL, school_id INT NOT NULL, main TINYINT(1) NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_39986E43C32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE album_photo (id INT AUTO_INCREMENT NOT NULL, album_id INT NOT NULL, image_id INT NOT NULL, student_id INT DEFAULT NULL, came TINYINT(1) NOT NULL, INDEX IDX_620FCE3E1137ABCF (album_id), UNIQUE INDEX UNIQ_620FCE3E3DA5256D (image_id), INDEX IDX_620FCE3ECB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE announcement (id INT AUTO_INCREMENT NOT NULL, image_filename VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE asset (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, size BIGINT NOT NULL, mime_type VARCHAR(255) NOT NULL, public TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE classroom (id INT AUTO_INCREMENT NOT NULL, school_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_497D309DC32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dealer (id INT NOT NULL, company_name VARCHAR(255) NOT NULL, authorized_person_name VARCHAR(255) NOT NULL, address LONGTEXT NOT NULL, phone VARCHAR(255) NOT NULL, phone_gsm VARCHAR(255) DEFAULT NULL, fax VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, asset_id INT NOT NULL, parent_id INT DEFAULT NULL, width SMALLINT NOT NULL, height SMALLINT NOT NULL, UNIQUE INDEX UNIQ_C53D045F5DA1941 (asset_id), INDEX IDX_C53D045F727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE memoir (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, receiver_id INT NOT NULL, text LONGTEXT NOT NULL, INDEX IDX_D4892627F624B39D (sender_id), INDEX IDX_D4892627CD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `order` (id INT AUTO_INCREMENT NOT NULL, school_id INT NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_F5299398C32A47EE (school_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_line (id INT AUTO_INCREMENT NOT NULL, parent_order_id INT NOT NULL, price NUMERIC(5, 2) NOT NULL, INDEX IDX_9CE58EE11252C1E9 (parent_order_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_line_classroom (order_line_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_910E1B5FBB01DC09 (order_line_id), INDEX IDX_910E1B5F6278D5A8 (classroom_id), PRIMARY KEY(order_line_id, classroom_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, category_id INT DEFAULT NULL, image_filename VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price NUMERIC(5, 2) NOT NULL, can_select_multiple_option TINYINT(1) NOT NULL, can_select_template TINYINT(1) NOT NULL, can_fill_laboratory_reference TINYINT(1) NOT NULL, knife_template_filename VARCHAR(255) NOT NULL, INDEX IDX_D34A04AD12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_field (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_FAA93E044584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_option (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, title VARCHAR(255) NOT NULL, price NUMERIC(5, 2) NOT NULL, INDEX IDX_38FA41144584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product_template (id INT AUTO_INCREMENT NOT NULL, product_id INT NOT NULL, title VARCHAR(255) NOT NULL, preview1_title VARCHAR(255) NOT NULL, preview1_image_filename VARCHAR(255) NOT NULL, preview2_title VARCHAR(255) DEFAULT NULL, preview2_image_filename VARCHAR(255) DEFAULT NULL, INDEX IDX_5CD075144584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE school (id INT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, logo_filename VARCHAR(255) DEFAULT NULL, address LONGTEXT NOT NULL, phone VARCHAR(255) NOT NULL, phone_gsm VARCHAR(255) DEFAULT NULL, fax VARCHAR(255) DEFAULT NULL, note LONGTEXT DEFAULT NULL, INDEX IDX_F99EDABB7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, classroom_id INT NOT NULL, school_number INT NOT NULL, name VARCHAR(255) NOT NULL, surname VARCHAR(255) NOT NULL, gender VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, INDEX IDX_B723AF336278D5A8 (classroom_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_custom_field (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_1260C86CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, discr VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE yearbook (id INT AUTO_INCREMENT NOT NULL, school_id INT NOT NULL, profile_album_id INT NOT NULL, starts_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ends_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', memoir VARCHAR(255) NOT NULL, image_upload TINYINT(1) NOT NULL, image_upload_limit INT NOT NULL, youtube TINYINT(1) NOT NULL, qr_prefix VARCHAR(255) DEFAULT NULL, INDEX IDX_A5A825EDC32A47EE (school_id), INDEX IDX_A5A825EDE85DCA7E (profile_album_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE yearbook_classroom (yearbook_id INT NOT NULL, classroom_id INT NOT NULL, INDEX IDX_62A0D05DF7857D5B (yearbook_id), INDEX IDX_62A0D05D6278D5A8 (classroom_id), PRIMARY KEY(yearbook_id, classroom_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE yearbook_album (yearbook_id INT NOT NULL, album_id INT NOT NULL, INDEX IDX_47378496F7857D5B (yearbook_id), INDEX IDX_473784961137ABCF (album_id), PRIMARY KEY(yearbook_id, album_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE album ADD CONSTRAINT FK_39986E43C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE album_photo ADD CONSTRAINT FK_620FCE3E1137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE album_photo ADD CONSTRAINT FK_620FCE3E3DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE album_photo ADD CONSTRAINT FK_620FCE3ECB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE classroom ADD CONSTRAINT FK_497D309DC32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE dealer ADD CONSTRAINT FK_17A33902BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F5DA1941 FOREIGN KEY (asset_id) REFERENCES asset (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F727ACA70 FOREIGN KEY (parent_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE memoir ADD CONSTRAINT FK_D4892627F624B39D FOREIGN KEY (sender_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE memoir ADD CONSTRAINT FK_D4892627CD53EDB6 FOREIGN KEY (receiver_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE `order` ADD CONSTRAINT FK_F5299398C32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE11252C1E9 FOREIGN KEY (parent_order_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE order_line_classroom ADD CONSTRAINT FK_910E1B5FBB01DC09 FOREIGN KEY (order_line_id) REFERENCES order_line (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_line_classroom ADD CONSTRAINT FK_910E1B5F6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE product_field ADD CONSTRAINT FK_FAA93E044584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_option ADD CONSTRAINT FK_38FA41144584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product_template ADD CONSTRAINT FK_5CD075144584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE school ADD CONSTRAINT FK_F99EDABB7E3C61F9 FOREIGN KEY (owner_id) REFERENCES dealer (id)');
        $this->addSql('ALTER TABLE school ADD CONSTRAINT FK_F99EDABBBF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF336278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id)');
        $this->addSql('ALTER TABLE student_custom_field ADD CONSTRAINT FK_1260C86CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE yearbook ADD CONSTRAINT FK_A5A825EDC32A47EE FOREIGN KEY (school_id) REFERENCES school (id)');
        $this->addSql('ALTER TABLE yearbook ADD CONSTRAINT FK_A5A825EDE85DCA7E FOREIGN KEY (profile_album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE yearbook_classroom ADD CONSTRAINT FK_62A0D05DF7857D5B FOREIGN KEY (yearbook_id) REFERENCES yearbook (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE yearbook_classroom ADD CONSTRAINT FK_62A0D05D6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE yearbook_album ADD CONSTRAINT FK_47378496F7857D5B FOREIGN KEY (yearbook_id) REFERENCES yearbook (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE yearbook_album ADD CONSTRAINT FK_473784961137ABCF FOREIGN KEY (album_id) REFERENCES album (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE album_photo DROP FOREIGN KEY FK_620FCE3E1137ABCF');
        $this->addSql('ALTER TABLE yearbook DROP FOREIGN KEY FK_A5A825EDE85DCA7E');
        $this->addSql('ALTER TABLE yearbook_album DROP FOREIGN KEY FK_473784961137ABCF');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F5DA1941');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD12469DE2');
        $this->addSql('ALTER TABLE order_line_classroom DROP FOREIGN KEY FK_910E1B5F6278D5A8');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF336278D5A8');
        $this->addSql('ALTER TABLE yearbook_classroom DROP FOREIGN KEY FK_62A0D05D6278D5A8');
        $this->addSql('ALTER TABLE school DROP FOREIGN KEY FK_F99EDABB7E3C61F9');
        $this->addSql('ALTER TABLE album_photo DROP FOREIGN KEY FK_620FCE3E3DA5256D');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F727ACA70');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE11252C1E9');
        $this->addSql('ALTER TABLE order_line_classroom DROP FOREIGN KEY FK_910E1B5FBB01DC09');
        $this->addSql('ALTER TABLE product_field DROP FOREIGN KEY FK_FAA93E044584665A');
        $this->addSql('ALTER TABLE product_option DROP FOREIGN KEY FK_38FA41144584665A');
        $this->addSql('ALTER TABLE product_template DROP FOREIGN KEY FK_5CD075144584665A');
        $this->addSql('ALTER TABLE album DROP FOREIGN KEY FK_39986E43C32A47EE');
        $this->addSql('ALTER TABLE classroom DROP FOREIGN KEY FK_497D309DC32A47EE');
        $this->addSql('ALTER TABLE `order` DROP FOREIGN KEY FK_F5299398C32A47EE');
        $this->addSql('ALTER TABLE yearbook DROP FOREIGN KEY FK_A5A825EDC32A47EE');
        $this->addSql('ALTER TABLE album_photo DROP FOREIGN KEY FK_620FCE3ECB944F1A');
        $this->addSql('ALTER TABLE memoir DROP FOREIGN KEY FK_D4892627F624B39D');
        $this->addSql('ALTER TABLE memoir DROP FOREIGN KEY FK_D4892627CD53EDB6');
        $this->addSql('ALTER TABLE student_custom_field DROP FOREIGN KEY FK_1260C86CB944F1A');
        $this->addSql('ALTER TABLE dealer DROP FOREIGN KEY FK_17A33902BF396750');
        $this->addSql('ALTER TABLE school DROP FOREIGN KEY FK_F99EDABBBF396750');
        $this->addSql('ALTER TABLE yearbook_classroom DROP FOREIGN KEY FK_62A0D05DF7857D5B');
        $this->addSql('ALTER TABLE yearbook_album DROP FOREIGN KEY FK_47378496F7857D5B');
        $this->addSql('DROP TABLE album');
        $this->addSql('DROP TABLE album_photo');
        $this->addSql('DROP TABLE announcement');
        $this->addSql('DROP TABLE asset');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE classroom');
        $this->addSql('DROP TABLE dealer');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE memoir');
        $this->addSql('DROP TABLE `order`');
        $this->addSql('DROP TABLE order_line');
        $this->addSql('DROP TABLE order_line_classroom');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE product_field');
        $this->addSql('DROP TABLE product_option');
        $this->addSql('DROP TABLE product_template');
        $this->addSql('DROP TABLE school');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE student_custom_field');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE yearbook');
        $this->addSql('DROP TABLE yearbook_classroom');
        $this->addSql('DROP TABLE yearbook_album');
    }
}
