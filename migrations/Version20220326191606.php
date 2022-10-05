<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220326191606 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_line_product_option (order_line_id INT NOT NULL, product_option_id INT NOT NULL, INDEX IDX_C34A4B8EBB01DC09 (order_line_id), INDEX IDX_C34A4B8EC964ABE2 (product_option_id), PRIMARY KEY(order_line_id, product_option_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE order_line_product_option ADD CONSTRAINT FK_C34A4B8EBB01DC09 FOREIGN KEY (order_line_id) REFERENCES order_line (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_line_product_option ADD CONSTRAINT FK_C34A4B8EC964ABE2 FOREIGN KEY (product_option_id) REFERENCES product_option (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE order_line ADD album_id INT NOT NULL, ADD product_id INT NOT NULL, ADD product_template_id INT DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, ADD laboratory_referance VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE11137ABCF FOREIGN KEY (album_id) REFERENCES album (id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE14584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_line ADD CONSTRAINT FK_9CE58EE1A9F591A7 FOREIGN KEY (product_template_id) REFERENCES product_template (id)');
        $this->addSql('CREATE INDEX IDX_9CE58EE11137ABCF ON order_line (album_id)');
        $this->addSql('CREATE INDEX IDX_9CE58EE14584665A ON order_line (product_id)');
        $this->addSql('CREATE INDEX IDX_9CE58EE1A9F591A7 ON order_line (product_template_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE order_line_product_option');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE11137ABCF');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE14584665A');
        $this->addSql('ALTER TABLE order_line DROP FOREIGN KEY FK_9CE58EE1A9F591A7');
        $this->addSql('DROP INDEX IDX_9CE58EE11137ABCF ON order_line');
        $this->addSql('DROP INDEX IDX_9CE58EE14584665A ON order_line');
        $this->addSql('DROP INDEX IDX_9CE58EE1A9F591A7 ON order_line');
        $this->addSql('ALTER TABLE order_line DROP album_id, DROP product_id, DROP product_template_id, DROP description, DROP laboratory_referance');
    }
}
