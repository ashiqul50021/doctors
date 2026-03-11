<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class ImageService
{
    /**
     * Upload and compress image to public folder
     *
     * @param UploadedFile $file
     * @param string $folder - folder name inside public/uploads/
     * @param int $quality - compression quality (1-100)
     * @param int|null $maxWidth - max width to resize
     * @return string - relative path from public/
     */
    public static function upload(UploadedFile $file, string $folder, int $quality = 80, ?int $maxWidth = 1200): string
    {
        // Increase memory limit for image processing
        ini_set('memory_limit', '512M');

        // Create directory if not exists
        $uploadPath = public_path("uploads/{$folder}");
        if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true) && !is_dir($uploadPath)) {
            throw new RuntimeException("Unable to create upload directory: {$uploadPath}");
        }

        if (!is_writable($uploadPath)) {
            throw new RuntimeException("Upload directory is not writable: {$uploadPath}");
        }

        // Generate unique filename
        $filename = Str::uuid() . '.webp';
        $fullPath = "{$uploadPath}/{$filename}";

        // Get image info
        $imageInfo = getimagesize($file->getPathname());
        $mimeType = $imageInfo['mime'] ?? '';

        if (!$imageInfo || !$mimeType) {
            return self::moveOriginal($file, $uploadPath, $folder);
        }

        // Create image resource based on type
        $sourceImage = match ($mimeType) {
            'image/jpeg', 'image/jpg' => imagecreatefromjpeg($file->getPathname()),
            'image/png' => imagecreatefrompng($file->getPathname()),
            'image/gif' => imagecreatefromgif($file->getPathname()),
            'image/webp' => imagecreatefromwebp($file->getPathname()),
            default => null,
        };

        if (!$sourceImage) {
            return self::moveOriginal($file, $uploadPath, $folder);
        }

        // Get original dimensions
        $origWidth = imagesx($sourceImage);
        $origHeight = imagesy($sourceImage);

        // Calculate new dimensions if resize needed
        if ($maxWidth && $origWidth > $maxWidth) {
            $ratio = $maxWidth / $origWidth;
            $newWidth = $maxWidth;
            $newHeight = (int) ($origHeight * $ratio);

            // Create resized image
            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

            // Preserve transparency for PNG
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);

            imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            imagedestroy($sourceImage);
            $sourceImage = $resizedImage;
        }

        // Save as WebP with compression
        $saved = function_exists('imagewebp') && imagewebp($sourceImage, $fullPath, $quality);
        imagedestroy($sourceImage);

        if (!$saved || !file_exists($fullPath) || filesize($fullPath) === 0) {
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }

            return self::moveOriginal($file, $uploadPath, $folder);
        }

        return "uploads/{$folder}/{$filename}";
    }

    /**
     * Delete image from public folder
     *
     * @param string|null $path - relative path from public/
     * @return bool
     */
    public static function delete(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        $fullPath = public_path($path);
        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }

    protected static function moveOriginal(UploadedFile $file, string $uploadPath, string $folder): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg');
        $filename = Str::uuid() . '.' . $extension;
        $file->move($uploadPath, $filename);

        return "uploads/{$folder}/{$filename}";
    }
}
