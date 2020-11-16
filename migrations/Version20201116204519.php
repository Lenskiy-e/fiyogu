<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201116204519 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE testimonials (id INT AUTO_INCREMENT NOT NULL, user_to_id INT DEFAULT NULL, user_from_id INT DEFAULT NULL, text LONGTEXT DEFAULT NULL, rating INT NOT NULL, INDEX IDX_38311579D2F7B13D (user_to_id), INDEX IDX_3831157920C3C701 (user_from_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE testimonials ADD CONSTRAINT FK_38311579D2F7B13D FOREIGN KEY (user_to_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE testimonials ADD CONSTRAINT FK_3831157920C3C701 FOREIGN KEY (user_from_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD rating DOUBLE PRECISION NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE testimonials');
        $this->addSql('ALTER TABLE user DROP rating');
    }
}
