<?php

namespace App\Services;

use App\Support\AdminImagePresets;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

class AdminImageUploadService
{
    public function store(UploadedFile $file, string $preset): string
    {
        $spec = AdminImagePresets::get($preset);
        $folder = $spec['disk_folder'];

        $path = $file->store($folder, 'public');

        return '/storage/'.$path;
    }

    /**
     * @return array<string, mixed>
     */
    public function fileRules(string $preset, bool $required = false): array
    {
        $spec = AdminImagePresets::get($preset);
        $mimes = implode(',', $spec['mimes']);

        $rules = [
            $required ? 'required' : 'nullable',
            'image',
            'mimes:'.$mimes,
            'max:'.$spec['max_kb'],
            new \App\Rules\AdminImageSpec($preset),
        ];

        return ['image_file' => $rules];
    }

    public function urlRules(bool $required = false): array
    {
        return [
            'image' => [
                $required ? 'required' : 'nullable',
                'string',
                'max:500',
                'regex:#^(/storage/|https://)#i',
            ],
        ];
    }

    /**
     * Require image on create when no URL provided.
     *
     * @param  array<string, mixed>  $data
     */
    public function requireImageOrUpload(array $data, bool $hasFile, string $field = 'image'): void
    {
        if (empty($data[$field]) && ! $hasFile) {
            throw ValidationException::withMessages([
                $field => 'Upload an image or provide an image URL.',
            ]);
        }
    }
}
