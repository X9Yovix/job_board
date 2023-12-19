<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231123002215 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, state_id INT NOT NULL, name VARCHAR(255) NOT NULL, state_code VARCHAR(255) DEFAULT NULL, country_code VARCHAR(255) DEFAULT NULL, latitude NUMERIC(10, 8) NOT NULL, longitude NUMERIC(11, 8) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', flag SMALLINT DEFAULT NULL, wiki_data_id VARCHAR(255) DEFAULT NULL, INDEX IDX_2D5B0234F92F3E70 (country_id), INDEX IDX_2D5B02345D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE country (id INT AUTO_INCREMENT NOT NULL, regions_id INT NOT NULL, subregions_id INT NOT NULL, name VARCHAR(100) NOT NULL, iso3 VARCHAR(100) DEFAULT NULL, numeric_code VARCHAR(100) DEFAULT NULL, iso2 VARCHAR(100) DEFAULT NULL, phonecode VARCHAR(255) DEFAULT NULL, capital VARCHAR(255) DEFAULT NULL, currency VARCHAR(255) DEFAULT NULL, currency_name VARCHAR(255) DEFAULT NULL, currency_symbol VARCHAR(255) DEFAULT NULL, tld VARCHAR(255) DEFAULT NULL, native VARCHAR(255) DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, subregion VARCHAR(255) DEFAULT NULL, nationality VARCHAR(255) DEFAULT NULL, timezones LONGTEXT DEFAULT NULL, translations LONGTEXT DEFAULT NULL, latitude NUMERIC(10, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, emoji VARCHAR(191) DEFAULT NULL, emoji_u VARCHAR(191) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', flag SMALLINT DEFAULT NULL, wiki_data_id VARCHAR(255) DEFAULT NULL, INDEX IDX_5373C966FCE83E5F (regions_id), INDEX IDX_5373C966A379D9FC (subregions_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, translations LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', flag SMALLINT DEFAULT NULL, wiki_data_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE state (id INT AUTO_INCREMENT NOT NULL, country_id INT NOT NULL, name VARCHAR(255) NOT NULL, country_code VARCHAR(255) DEFAULT NULL, fips_code VARCHAR(255) DEFAULT NULL, iso2 VARCHAR(255) DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, latitude NUMERIC(10, 8) DEFAULT NULL, longitude NUMERIC(11, 8) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', flag SMALLINT DEFAULT NULL, wiki_data_id VARCHAR(255) DEFAULT NULL, INDEX IDX_A393D2FBF92F3E70 (country_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subregions (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, name VARCHAR(255) NOT NULL, translations LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', flag SMALLINT DEFAULT NULL, wiki_data_id VARCHAR(255) DEFAULT NULL, INDEX IDX_E905D13C98260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234F92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B02345D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C966FCE83E5F FOREIGN KEY (regions_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE country ADD CONSTRAINT FK_5373C966A379D9FC FOREIGN KEY (subregions_id) REFERENCES subregions (id)');
        $this->addSql('ALTER TABLE state ADD CONSTRAINT FK_A393D2FBF92F3E70 FOREIGN KEY (country_id) REFERENCES country (id)');
        $this->addSql('ALTER TABLE subregions ADD CONSTRAINT FK_E905D13C98260155 FOREIGN KEY (region_id) REFERENCES region (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234F92F3E70');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B02345D83CC1');
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C966FCE83E5F');
        $this->addSql('ALTER TABLE country DROP FOREIGN KEY FK_5373C966A379D9FC');
        $this->addSql('ALTER TABLE state DROP FOREIGN KEY FK_A393D2FBF92F3E70');
        $this->addSql('ALTER TABLE subregions DROP FOREIGN KEY FK_E905D13C98260155');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE country');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE state');
        $this->addSql('DROP TABLE subregions');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
