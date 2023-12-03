<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231202035224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE keyword (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE keyword_announcement (keyword_id INT NOT NULL, announcement_id INT NOT NULL, INDEX IDX_A67EA576115D4552 (keyword_id), INDEX IDX_A67EA576913AEA17 (announcement_id), PRIMARY KEY(keyword_id, announcement_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE keyword_announcement ADD CONSTRAINT FK_A67EA576115D4552 FOREIGN KEY (keyword_id) REFERENCES keyword (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE keyword_announcement ADD CONSTRAINT FK_A67EA576913AEA17 FOREIGN KEY (announcement_id) REFERENCES announcement (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE keyword_announcement DROP FOREIGN KEY FK_A67EA576115D4552');
        $this->addSql('ALTER TABLE keyword_announcement DROP FOREIGN KEY FK_A67EA576913AEA17');
        $this->addSql('DROP TABLE keyword');
        $this->addSql('DROP TABLE keyword_announcement');
    }
}
