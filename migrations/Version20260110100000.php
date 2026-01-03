<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Rename 'order' column to 'sort_order' to avoid reserved keyword conflict
 */
final class Version20260110100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename order column to sort_order in advertisement table to avoid SQL reserved keyword';
    }

    public function up(Schema $schema): void
    {
        // Check if the 'order' column exists before trying to rename it
        $columns = $this->connection->fetchAllAssociative("SHOW COLUMNS FROM advertisement");
        $columnNames = array_column($columns, 'Field');
        
        if (in_array('order', $columnNames)) {
            $this->addSql('ALTER TABLE advertisement CHANGE `order` sort_order INT NOT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE advertisement CHANGE sort_order `order` INT NOT NULL');
    }
}
