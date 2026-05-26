<?php

namespace App\Support;

use InvalidArgumentException;

class AdminImagePresets
{
    /**
     * @return array<string, array<string, mixed>>
     */
    public static function all(): array
    {
        static $presets = null;

        if ($presets !== null) {
            return $presets;
        }

        $presets = config('admin_images.presets');

        if (! is_array($presets) || $presets === []) {
            $path = config_path('admin_images.php');

            if (is_readable($path)) {
                /** @var array<string, mixed> $file */
                $file = require $path;
                $presets = $file['presets'] ?? [];
            }
        }

        return $presets = is_array($presets) ? $presets : [];
    }

    public static function get(string $preset): array
    {
        $presets = self::all();

        if (! isset($presets[$preset]) || ! is_array($presets[$preset])) {
            throw new InvalidArgumentException(
                "Unknown admin image preset: {$preset}. Ensure config/admin_images.php is deployed and run: php artisan config:clear"
            );
        }

        return $presets[$preset];
    }

    public static function bannerPresetForPlacement(?string $placement): string
    {
        return $placement === 'promo_strip' ? 'banner_promo_strip' : 'banner_home_hero';
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public static function bannerPresetsForJs(): array
    {
        return [
            'home_hero' => self::get('banner_home_hero'),
            'promo_strip' => self::get('banner_promo_strip'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function forAdminUi(string $preset): array
    {
        $spec = self::get($preset);

        return [
            'preset' => $preset,
            'label' => $spec['label'],
            'ratio_label' => $spec['ratio_label'],
            'ratio' => $spec['ratio'],
            'recommended' => $spec['recommended'],
            'min' => $spec['min'],
            'max' => $spec['max'],
            'max_kb' => $spec['max_kb'],
            'max_mb' => round($spec['max_kb'] / 1024, 1),
            'devices' => $spec['devices'],
            'tips' => $spec['tips'],
            'mimes' => $spec['mimes'],
        ];
    }
}
