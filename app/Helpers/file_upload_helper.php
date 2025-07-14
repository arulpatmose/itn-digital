<?php

use CodeIgniter\HTTP\Files\UploadedFile;

if (!function_exists('handleThumbnailUpload')) {
    /**
     * Handles upload, delete, and cleanup of thumbnail files.
     *
     * @param UploadedFile|null $file
     * @param string|null $existingFileName
     * @param string|null $removeFlag
     * @param string $uploadPath
     * @return string|null New thumbnail filename or null
     */
    function handleThumbnailUpload(?UploadedFile $file, ?string $existingFileName = null, ?string $removeFlag = null, string $uploadPath = 'uploads/thumbnails'): ?string
    {
        helper('filesystem');

        // Remove if requested
        if ($removeFlag === '1' && $existingFileName && file_exists("$uploadPath/$existingFileName")) {
            unlink("$uploadPath/$existingFileName");
            return null;
        }

        // Handle new upload
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Remove old if exists
            if ($existingFileName && file_exists("$uploadPath/$existingFileName")) {
                unlink("$uploadPath/$existingFileName");
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
     * @param string $uploadPath
     * @return bool
     */
    function removeThumbnailFile(?string $fileName, string $uploadPath = 'uploads/thumbnails'): bool
    {
        if ($fileName && file_exists("$uploadPath/$fileName")) {
            return unlink("$uploadPath/$fileName");
        }

        return false;
    }
}
