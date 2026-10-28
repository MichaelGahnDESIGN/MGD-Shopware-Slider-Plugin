<?php

declare(strict_types=1);

namespace Mgd\Slider;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

/**
 * Plugin-Klasse MgdSlider - Pre-Alpha, nicht gegen eine echte Shopware-Instanz getestet.
 */
class MgdSlider extends Plugin
{
    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        // Daten behalten wenn der Kunde das so wählt
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('DROP TABLE IF EXISTS `mgd_slider`');
    }
}
