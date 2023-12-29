<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231229232351 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announcement_user (announcement_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A1A2DE15913AEA17 (announcement_id), INDEX IDX_A1A2DE15A76ED395 (user_id), PRIMARY KEY(announcement_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE announcement_user ADD CONSTRAINT FK_A1A2DE15913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE announcement_user ADD CONSTRAINT FK_A1A2DE15A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announcement_user DROP FOREIGN KEY FK_A1A2DE15913AEA17');
        $this->addSql('ALTER TABLE announcement_user DROP FOREIGN KEY FK_A1A2DE15A76ED395');
        $this->addSql('DROP TABLE announcement_user');
    }
}
