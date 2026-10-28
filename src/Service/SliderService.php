<?php

declare(strict_types=1);

namespace Mgd\Slider\Service;

use Doctrine\DBAL\Connection;
use Mgd\Slider\Exception\SliderLimitReachedException;
use MGD\Slider\Config;
use MGD\Slider\Limits;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * Slider anlegen / laden. Direkt per DBAL auf mgd_slider (ungetestet, Pre-Alpha).
 */
class SliderService
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function count(): int
    {
        return (int) $this->connection->fetchOne('SELECT COUNT(*) FROM `mgd_slider`');
    }

    /**
     * Limit wird HIER geprüft, nicht nur im Admin
     *
     * @param array<string,mixed> $rawConfig
     * @return string id als Hex
     */
    public function create(string $name, array $rawConfig = []): string
    {
        if (!Limits::canCreate($this->count())) {
            throw new SliderLimitReachedException();
        }

        $id = Uuid::randomHex();
        $this->connection->insert('mgd_slider', [
            'id' => Uuid::fromHexToBytes($id),
            'name' => Config::text($name, 255) ?: 'Slider',
            'config' => json_encode(Config::normalize($rawConfig), JSON_THROW_ON_ERROR),
            'created_at' => $this->now(),
        ]);

        return $id;
    }

    /** @param array<string,mixed> $rawConfig */
    public function update(string $id, string $name, array $rawConfig): void
    {
        if (!Uuid::isValid($id)) {
            return;
        }

        $this->connection->update('mgd_slider', [
            'name' => Config::text($name, 255) ?: 'Slider',
            'config' => json_encode(Config::normalize($rawConfig), JSON_THROW_ON_ERROR),
            'updated_at' => $this->now(),
        ], ['id' => Uuid::fromHexToBytes($id)]);
    }

    public function delete(string $id): void
    {
        if (!Uuid::isValid($id)) {
            return;
        }

        $this->connection->delete('mgd_slider', ['id' => Uuid::fromHexToBytes($id)]);
    }

    /** @return array{id:string,name:string,config:array<string,mixed>}|null */
    public function get(string $id): ?array
    {
        if (!Uuid::isValid($id)) {
            return null;
        }

        $row = $this->connection->fetchAssociative(
            'SELECT `id`, `name`, `config` FROM `mgd_slider` WHERE `id` = :id',
            ['id' => Uuid::fromHexToBytes($id)]
        );

        return $row ? $this->hydrate($row) : null;
    }

    /** @return list<array{id:string,name:string,config:array<string,mixed>}> */
    public function list(): array
    {
        $rows = $this->connection->fetchAllAssociative('SELECT `id`, `name`, `config` FROM `mgd_slider` ORDER BY `created_at` ASC');

        return array_map(fn (array $row): array => $this->hydrate($row), $rows);
    }

    /**
     * @param array<string,mixed> $row
     * @return array{id:string,name:string,config:array<string,mixed>}
     */
    private function hydrate(array $row): array
    {
        $raw = json_decode((string) $row['config'], true);

        return [
            'id' => Uuid::fromBytesToHex($row['id']),
            'name' => (string) $row['name'],
            'config' => Config::normalize(is_array($raw) ? $raw : []),
        ];
    }

    private function now(): string
    {
        return (new \DateTimeImmutable())->format('Y-m-d H:i:s.v');
    }
}
