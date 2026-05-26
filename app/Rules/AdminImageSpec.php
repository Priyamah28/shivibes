<?php

namespace App\Rules;

use App\Support\AdminImagePresets;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class AdminImageSpec implements ValidationRule
{
    public function __construct(
        private readonly string $preset,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            return;
        }

        $spec = AdminImagePresets::get($this->preset);

        $info = @getimagesize($value->getRealPath());

        if ($info === false) {
            $fail('The uploaded file is not a valid image.');

            return;
        }

        [$width, $height] = $info;
        $minW = (int) $spec['min']['width'];
        $minH = (int) $spec['min']['height'];
        $maxW = (int) $spec['max']['width'];
        $maxH = (int) $spec['max']['height'];
        $expectedRatio = (float) $spec['ratio'];
        $tolerance = (float) $spec['ratio_tolerance'];

        if ($width < $minW || $height < $minH) {
            $fail(sprintf(
                'Image is too small (%d×%d px). Minimum %d×%d px for %s.',
                $width,
                $height,
                $minW,
                $minH,
                $spec['label']
            ));

            return;
        }

        if ($width > $maxW || $height > $maxH) {
            $fail(sprintf(
                'Image is too large (%d×%d px). Maximum %d×%d px — resize before uploading.',
                $width,
                $height,
                $maxW,
                $maxH
            ));

            return;
        }

        $ratio = $width / $height;
        $delta = abs($ratio - $expectedRatio);

        if ($delta > $expectedRatio * $tolerance) {
            $fail(sprintf(
                'Image ratio is %.2f:1 but %s requires %s (recommended %d×%d px).',
                round($ratio, 2),
                $spec['label'],
                $spec['ratio_label'],
                $spec['recommended']['width'],
                $spec['recommended']['height']
            ));
        }
    }
}
