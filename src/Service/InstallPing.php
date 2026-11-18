<?php

declare(strict_types=1);

namespace Mgd\Slider\Service;

use Doctrine\DBAL\Connection;
use Mgd\Slider\Install\InstallClient;
use Mgd\Slider\Install\Platform\Shopware as ShopwareInstall;
use Shopware\Core\System\SystemConfig\SystemConfigService;

/**
 * Install-Meldung an theme.michael-gahn.de (Free-Lizenz) - max 1x am Tag, darf nie was kaputt machen
 */
final class InstallPing
{
    public const PRODUCT = 'mgd-slider-shopware';
    // Zustand vom Install-Client (Schlüssel, ID, letzter Ping)
    public const STATE_KEY = 'MgdSlider.config.installState';
    // Schalter in den Plugin-Einstellungen, ab Werk an
    public const SWITCH_KEY = 'MgdSlider.config.installReporting';

    public function __construct(
        private readonly SystemConfigService $config,
        private readonly Connection $connection,
    ) {
    }

    /** true = gemeldet. Fehler werden still geschluckt */
    public function ping(bool $force = false): bool
    {
        try {
            $ic = $this->client();
            if ($ic === null) {
                return false;
            }
            $this->syncSwitch($ic);

            return $ic->ping($force);
        } catch (\Throwable) {
            return false;
        }
    }

    public function client(): ?InstallClient
    {
        $domain = $this->domain();
        if ($domain === '') {
            return null;
        }

        return ShopwareInstall::client($this->config, self::STATE_KEY, self::PRODUCT, self::version(), $domain);
    }

    // Schalter aus Einstellungen > Client (aus = Eintrag auf Plattform wird gelöscht)
    private function syncSwitch(InstallClient $ic): void
    {
        $on = $this->config->get(self::SWITCH_KEY);
        $on = $on === null ? true : (bool) $on;
        if ($on !== $ic->isEnabled()) {
            $ic->setEnabled($on);
        }
    }

    // erste Storefront-Domain, sonst APP_URL
    private function domain(): string
    {
        try {
            $url = $this->connection->fetchOne(
                'SELECT d.url FROM sales_channel_domain d INNER JOIN sales_channel s ON s.id = d.sales_channel_id
                 WHERE s.active = 1 AND s.type_id = UNHEX(:type) ORDER BY d.created_at LIMIT 1',
                ['type' => '8a243080f92e4c719546314b577cf82b'],
            );
        } catch (\Throwable) {
            $url = false;
        }
        if (!is_string($url) || $url === '') {
            $url = (string) ($_SERVER['APP_URL'] ?? $_ENV['APP_URL'] ?? getenv('APP_URL') ?: '');
        }

        return strtolower((string) parse_url($url, PHP_URL_HOST));
    }

    // Version aus composer.json vom Plugin
    public static function version(): string
    {
        $raw = @file_get_contents(__DIR__ . '/../../composer.json');
        $data = is_string($raw) ? json_decode($raw, true) : null;

        return is_array($data) && is_string($data['version'] ?? null) ? $data['version'] : '0.0.0';
    }
}
