<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;

class Media extends BaseController
{
    public function upload(string $collection, string $fileName): ResponseInterface
    {
        $collection = basename($collection);
        $fileName = basename($fileName);

        if (
            $collection === ''
            || $fileName === ''
            || str_starts_with($fileName, '.')
            || !preg_match('/^[A-Za-z0-9_-]+$/', $collection)
        ) {
            throw PageNotFoundException::forPageNotFound();
        }

        $basePath = writable_upload_path($collection);
        $path = writable_upload_path($collection, $fileName);
        $realBasePath = realpath($basePath);
        $realPath = realpath($path);

        if (
            $realBasePath === false
            || $realPath === false
            || !str_starts_with($realPath, $realBasePath . DIRECTORY_SEPARATOR)
            || !is_file($realPath)
        ) {
            if (in_array($collection, ['thumbnails', 'images'], true)) {
                return redirect()->to(image_placeholder_url());
            }

            throw PageNotFoundException::forPageNotFound();
        }

        $mimeType = mime_content_type($realPath) ?: 'application/octet-stream';

        return $this->response
            ->setContentType($mimeType)
            ->setHeader('Content-Length', (string) filesize($realPath))
            ->setBody(file_get_contents($realPath));
    }
}
