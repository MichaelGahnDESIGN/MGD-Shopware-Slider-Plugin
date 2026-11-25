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
        $this->pingKit($installContext);
    }

    public function activate(ActivateContext $activateContext): void
    {
        parent::activate($activateContext);
        $this->pingKit($activateContext);
    }

    public function update(UpdateContext $updateContext): void
    {
        parent::update($updateContext);
        $this->pingKit($updateContext);
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

    // Meldung bei install / activate / update ueber den Baukasten, darf nie scheitern
    private function pingKit(object $context): void
    {
        try {
            ShopwareKit::forLifecycle($context, $this->container->get(SystemConfigService::class), 'mgd-slider-shopware')?->installClient()?->ping(true);
        } catch (\Throwable) {
        }
    }
}
