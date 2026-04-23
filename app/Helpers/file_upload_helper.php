<?php

use CodeIgniter\HTTP\Files\UploadedFile;

if (!function_exists('thumbnailUploadPath')) {
    /**
     * Returns the non-public storage path for program thumbnails.
     */
    function thumbnailUploadPath(): string
    {
        helper('media');

        return writable_upload_path('thumbnails');
    }
}

if (!function_exists('handleThumbnailUpload')) {
    /**
     * Handles upload, delete, and cleanup of thumbnail files.
     *
     * @param UploadedFile|null $file
     * @param string|null $existingFileName
     * @param string|null $removeFlag
     * @param string|null $uploadPath
     * @return string|null New thumbnail filename or null
     */
    function handleThumbnailUpload(?UploadedFile $file, ?string $existingFileName = null, ?string $removeFlag = null, ?string $uploadPath = null): ?string
    {
        helper('filesystem', 'media');

        $uploadPath ??= ensure_writable_upload_path('thumbnails');

        // Remove if requested
        if ($removeFlag === '1' && $existingFileName && file_exists($uploadPath . DIRECTORY_SEPARATOR . $existingFileName)) {
            unlink($uploadPath . DIRECTORY_SEPARATOR . $existingFileName);
            return null;
        }

        // Handle new upload
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

            if (!in_array($file->getMimeType(), $allowedMimeTypes, true)) {
                throw new InvalidArgumentException('Only JPG, PNG, GIF, and WEBP thumbnails are allowed.');
            }

            // Remove old if exists
            if ($existingFileName && file_exists($uploadPath . DIRECTORY_SEPARATOR . $existingFileName)) {
                unlink($uploadPath . DIRECTORY_SEPARATOR . $existingFileName);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            return $newName;
        }

        // Keep existing
        return $existingFileName;
    }
}

if (!function_exists('removeThumbnailFile')) {
    /**
     * Removes a thumbnail file if it exists.
     *
     * @param string|null $fileName
     * @param string|null $uploadPath
     * @return bool
     */
    function removeThumbnailFile(?string $fileName, ?string $uploadPath = null): bool
    {
        $uploadPath ??= thumbnailUploadPath();

        if ($fileName && file_exists($uploadPath . DIRECTORY_SEPARATOR . $fileName)) {
            return unlink($uploadPath . DIRECTORY_SEPARATOR . $fileName);
        }

        return false;
    }
}
