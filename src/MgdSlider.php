<?php

declare(strict_types=1);

namespace Mgd\Slider;

use Doctrine\DBAL\Connection;
use Mgd\Slider\Service\InstallPing;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * Plugin-Klasse MgdSlider
 */
class MgdSlider extends Plugin
{
    // Install-Meldung bei install / activate / update (Free-Lizenz)
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $this->pingInstall();
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);
        $this->pingInstall();
    }

    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);
        $this->pingInstall();
    }

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

    // Lifecycle darf nie scheitern > alles abfangen, Dienste von Hand bauen (Plugin-Container noch nicht da)
    private function pingInstall(): void
    {
        try {
            $ping = new InstallPing($this->container->get(SystemConfigService::class), $this->container->get(Connection::class));
            $ping->ping(true);
        } catch (\Throwable) {
        }
    }
}
