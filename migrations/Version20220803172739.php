<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220803172739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dealer_permission ADD permission_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE dealer_permission ADD CONSTRAINT FK_E4DFF4FAFED90CCA FOREIGN KEY (permission_id) REFERENCES permission (id)');
        $this->addSql('CREATE INDEX IDX_E4DFF4FAFED90CCA ON dealer_permission (permission_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dealer_permission DROP FOREIGN KEY FK_E4DFF4FAFED90CCA');
        $this->addSql('DROP INDEX IDX_E4DFF4FAFED90CCA ON dealer_permission');
        $this->addSql('ALTER TABLE dealer_permission DROP permission_id');
    }
}
