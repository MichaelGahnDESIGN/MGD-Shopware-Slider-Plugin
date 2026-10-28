<?php

declare(strict_types=1);

namespace Mgd\Slider\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * Tabelle mgd_slider (ein Datensatz = ein Slider, Einstellungen als JSON)
 */
#[Package('framework')]
class Migration1767225600Slider extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1767225600;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<'SQL'
CREATE TABLE IF NOT EXISTS `mgd_slider` (
    `id` BINARY(16) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `config` JSON NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `json.mgd_slider.config` CHECK (JSON_VALID(`config`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL);
    }

    public function updateDestructive(Connection $connection): void
    {
        // nix
    }
}
