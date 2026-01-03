<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260110000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix admin users by ensuring they have ROLE_RESPONSABLE in addition to ROLE_ADMIN';
    }

    public function up(Schema $schema): void
    {
        // Update all users with ROLE_ADMIN to also have ROLE_RESPONSABLE
        // This is done in PHP instead of SQL to properly handle JSON encoding
        $connection = $this->connection;
        
        // Get all users with ROLE_ADMIN but without ROLE_RESPONSABLE
        $rows = $connection->fetchAllAssociative(
            "SELECT id, roles FROM user WHERE JSON_CONTAINS(roles, '\"ROLE_ADMIN\"') AND NOT JSON_CONTAINS(roles, '\"ROLE_RESPONSABLE\"')"
        );
        
        foreach ($rows as $row) {
            $roles = json_decode($row['roles'], true) ?: [];
            if (!in_array('ROLE_RESPONSABLE', $roles)) {
                $roles[] = 'ROLE_RESPONSABLE';
                $rolesJson = json_encode($roles);
                $connection->executeStatement(
                    'UPDATE user SET roles = ? WHERE id = ?',
                    [$rolesJson, $row['id']]
                );
            }
        }
    }

    public function down(Schema $schema): void
    {
        // This migration cannot be safely reverted, so we intentionally leave it empty
    }
}
