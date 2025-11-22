<?php
namespace App\Service;

use App\Repository\SettingRepository;
use Symfony\Contracts\Cache\CacheInterface;

class SettingsService
{
    public function __construct(
        private SettingRepository $repo,
        private CacheInterface $cache,
    ) {}

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->getAll()[$name] ?? $default;
    }

    public function getAll(): array
    {
        return $this->cache->get('app.settings', function () {
            $settings = [];
            foreach ($this->repo->findAll() as $setting) {
                $settings[$setting->getName()] = $setting->getValue();
            }
            return $settings;
        });
    }

    public function updateSettings(array $data): void
    {
        foreach ($data as $name => $value) {
            $setting = $this->repo->findOneBy(['name' => $name]);
            if ($setting) {
                $setting->setValue($value);
            }
        }
        $this->repo->flush();
        $this->cache->delete('app.settings');
    }

    public function increment(string $name, int $amount = 1): void
    {
        $setting = $this->repo->findOneBy(['name' => $name]);
        if (!$setting) {
            throw new \InvalidArgumentException("Setting '{$name}' not found.");
        }

        $currentValue = (int) $setting->getValue();
        $setting->setValue($currentValue + $amount);

        $this->repo->flush();
        $this->cache->delete('app.settings');
    }

    public function refresh(): void
    {
        $this->cache->delete('app.settings');
    }
}