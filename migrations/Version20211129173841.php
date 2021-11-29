<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211129173841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE moto (id INT AUTO_INCREMENT NOT NULL, brand_id INT NOT NULL, type_id INT NOT NULL, name VARCHAR(255) NOT NULL, price INT NOT NULL, cylinder SMALLINT DEFAULT NULL, engine_cooling VARCHAR(255) DEFAULT NULL, engine_distribution VARCHAR(255) DEFAULT NULL, volumetric_ratio VARCHAR(255) DEFAULT NULL, starter VARCHAR(255) DEFAULT NULL, max_torque VARCHAR(255) DEFAULT NULL, gear_number VARCHAR(255) DEFAULT NULL, clutch VARCHAR(255) DEFAULT NULL, final_drive VARCHAR(255) DEFAULT NULL, frame VARCHAR(255) DEFAULT NULL, caster_angle VARCHAR(255) DEFAULT NULL, caster_trail VARCHAR(255) DEFAULT NULL, wheelbase INT DEFAULT NULL, front_suspension VARCHAR(255) DEFAULT NULL, rear_suspension VARCHAR(255) DEFAULT NULL, front_brake VARCHAR(255) DEFAULT NULL, rear_brake VARCHAR(255) DEFAULT NULL, front_tire VARCHAR(255) DEFAULT NULL, back_tire VARCHAR(255) DEFAULT NULL, lxlx_h VARCHAR(255) DEFAULT NULL, seat_height INT DEFAULT NULL, ground_clearance INT DEFAULT NULL, fuel_capacity INT DEFAULT NULL, oil_capacity INT DEFAULT NULL, weight INT DEFAULT NULL, fuel_consumption VARCHAR(255) DEFAULT NULL, INDEX IDX_3DDDBCE444F5D008 (brand_id), INDEX IDX_3DDDBCE4C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE moto ADD CONSTRAINT FK_3DDDBCE444F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE moto ADD CONSTRAINT FK_3DDDBCE4C54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE moto DROP FOREIGN KEY FK_3DDDBCE444F5D008');
        $this->addSql('ALTER TABLE moto DROP FOREIGN KEY FK_3DDDBCE4C54C8C93');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE moto');
        $this->addSql('DROP TABLE type');
    }
}
