<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211129183618 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE moto CHANGE seat_height seat_height VARCHAR(255) DEFAULT NULL, CHANGE ground_clearance ground_clearance VARCHAR(255) DEFAULT NULL, CHANGE fuel_capacity fuel_capacity VARCHAR(255) DEFAULT NULL, CHANGE oil_capacity oil_capacity VARCHAR(255) DEFAULT NULL, CHANGE weight weight VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE moto CHANGE seat_height seat_height INT DEFAULT NULL, CHANGE ground_clearance ground_clearance INT DEFAULT NULL, CHANGE fuel_capacity fuel_capacity INT DEFAULT NULL, CHANGE oil_capacity oil_capacity INT DEFAULT NULL, CHANGE weight weight INT DEFAULT NULL');
    }
}
