<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231122035600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD first_name VARCHAR(255) NOT NULL, ADD last_name VARCHAR(255) NOT NULL, ADD birthday DATE NOT NULL, ADD gender VARCHAR(255) NOT NULL, ADD phone_number VARCHAR(255) NOT NULL, ADD country VARCHAR(255) NOT NULL, ADD state VARCHAR(255) NOT NULL, ADD city VARCHAR(255) NOT NULL, ADD job_title VARCHAR(255) NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP first_name, DROP last_name, DROP birthday, DROP gender, DROP phone_number, DROP country, DROP state, DROP city, DROP zip_code, DROP job_title');
    }
}
