<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260103160000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add image column to category table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category ADD image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE category DROP image');
    }
}
