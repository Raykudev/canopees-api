<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261027161634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fondateur (id INT AUTO_INCREMENT NOT NULL, prenom VARCHAR(100) NOT NULL, role VARCHAR(100) NOT NULL, bio LONGTEXT NOT NULL, photo VARCHAR(255) NOT NULL, photo_alt VARCHAR(255) NOT NULL, avatar VARCHAR(255) NOT NULL, ordre INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE public_cible (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(100) NOT NULL, photo VARCHAR(255) NOT NULL, photo_alt VARCHAR(255) NOT NULL, icone VARCHAR(255) NOT NULL, ordre INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE valeur (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(100) NOT NULL, texte LONGTEXT NOT NULL, ordre INT NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE section DROP icone');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE fondateur');
        $this->addSql('DROP TABLE public_cible');
        $this->addSql('DROP TABLE valeur');
        $this->addSql('ALTER TABLE section ADD icone VARCHAR(255) DEFAULT NULL');
    }
}
