<?php

declare(strict_types=1);

namespace Mgd\Slider;

use Doctrine\DBAL\Connection;
use Mgd\Slider\Kit\Platform\Shopware\ShopwareKit;
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
    // Anmeldung bei install / activate / update, darf nie scheitern (Baukasten kann null sein)
    public function install(InstallContext $installContext): void
    {
        parent::install($installContext);
        $this->pingKit();
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);
        $this->pingKit();
    }

    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);
        $this->pingKit();
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        // Abmelden bei theme.michael-gahn.de (immer), Baukasten-Daten nur bei "Daten löschen"
        try {
            ShopwareKit::forUninstall($uninstallContext, $this->container->get(SystemConfigService::class), 'mgd-slider-shopware')?->uninstall($uninstallContext);
        } catch (\Throwable) {
        }

        // Daten behalten wenn der Kunde das so wählt
        if ($uninstallContext->keepUserData()) {
            return;
        }

        $connection = $this->container->get(Connection::class);
        $connection->executeStatement('DROP TABLE IF EXISTS `mgd_slider`');
    }

    // Kit aus dem Container, beim ersten Install gibt es ihn evtl. noch nicht > dann macht es die tägliche Aufgabe
    private function pingKit(): void
    {
        try {
            $kit = $this->container->has(ShopwareKit::class) ? $this->container->get(ShopwareKit::class) : null;
            if ($kit instanceof ShopwareKit) {
                $kit->installClient()?->ping(true);
            }
        } catch (\Throwable) {
        }
    }
}
