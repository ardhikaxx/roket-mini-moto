<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class FileUploadHelper
{
    /**
     * Upload a file directly without requiring storage:link.
     * Stores file in public/uploads/{folder} and storage/uploads/{folder}.
     *
     * @param UploadedFile $file
     * @param string $folder (e.g., 'products', 'users', 'stores', 'reports')
     * @return string relative path (e.g. 'products/products_172000000_abc123.jpg')
     */
    public static function upload(UploadedFile $file, string $folder): string
    {
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = $folder . '_' . time() . '_' . uniqid() . '.' . $ext;

        $publicDir = public_path('uploads/' . $folder);
        $storageDir = storage_path('uploads/' . $folder);

        if (!File::exists($publicDir)) {
            File::makeDirectory($publicDir, 0755, true, true);
        }
        if (!File::exists($storageDir)) {
            File::makeDirectory($storageDir, 0755, true, true);
        }

        // Move to public/uploads directory first for instant accessibility
        $file->move($publicDir, $filename);

        // Make a backup copy in storage/uploads
        $publicFile = $publicDir . '/' . $filename;
        if (File::exists($publicFile)) {
            File::copy($publicFile, $storageDir . '/' . $filename);
        }

        return $folder . '/' . $filename;
    }

    /**
     * Delete an uploaded file from all upload and storage locations.
     *
     * @param string|null $path
     * @return void
     */
    public static function delete(?string $path): void
    {
        if (empty($path)) {
            return;
        }

        // Normalize path
        $cleanPath = ltrim(str_replace(['public/', 'uploads/', 'storage/'], '', $path), '/');

        $possiblePaths = [
            public_path('uploads/' . $cleanPath),
            storage_path('uploads/' . $cleanPath),
            public_path('storage/' . $cleanPath),
            storage_path('app/public/' . $cleanPath),
            public_path($path),
            storage_path($path),
        ];

        foreach ($possiblePaths as $filePath) {
            if (File::exists($filePath) && !is_dir($filePath)) {
                @File::delete($filePath);
            }
        }
    }
}
