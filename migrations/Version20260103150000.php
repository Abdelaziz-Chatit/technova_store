<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260103150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create advertisement table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE advertisement (
            id INT AUTO_INCREMENT NOT NULL,
            title VARCHAR(255) NOT NULL,
            description VARCHAR(500),
            image VARCHAR(255),
            link VARCHAR(500),
            `order` INT NOT NULL DEFAULT 0,
            is_active BOOLEAN NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL,
            updated_at DATETIME,
            PRIMARY KEY (id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS advertisement');
    }
}
