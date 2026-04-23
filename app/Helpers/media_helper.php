<?php

if (!function_exists('writable_upload_path')) {
    function writable_upload_path(?string $collection = null, ?string $fileName = null): string
    {
        $path = rtrim(WRITEPATH . 'uploads', DIRECTORY_SEPARATOR);

        if ($collection !== null && $collection !== '') {
            $path .= DIRECTORY_SEPARATOR . basename($collection);
        }

        if ($fileName !== null && $fileName !== '') {
            $path .= DIRECTORY_SEPARATOR . basename($fileName);
        }

        return $path;
    }
}

if (!function_exists('ensure_writable_upload_path')) {
    function ensure_writable_upload_path(string $collection): string
    {
        $path = writable_upload_path($collection);

        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        return $path;
    }
}

if (!function_exists('writable_upload_exists')) {
    function writable_upload_exists(string $collection, ?string $fileName): bool
    {
        if ($fileName === null || $fileName === '') {
            return false;
        }

        $basePath = writable_upload_path($collection);
        $path = writable_upload_path($collection, $fileName);
        $realBasePath = realpath($basePath);
        $realPath = realpath($path);

        return $realBasePath !== false
            && $realPath !== false
            && str_starts_with($realPath, $realBasePath . DIRECTORY_SEPARATOR)
            && is_file($realPath);
    }
}

if (!function_exists('writable_upload_url')) {
    function writable_upload_url(string $collection, ?string $fileName, ?string $fallbackUrl = null): string
    {
        if (!writable_upload_exists($collection, $fileName)) {
            return $fallbackUrl ?? '';
        }

        return base_url(
            'media/uploads/'
            . rawurlencode(basename($collection))
            . '/'
            . rawurlencode(basename($fileName))
        );
    }
}

if (!function_exists('writable_upload_route_url')) {
    function writable_upload_route_url(string $collection, ?string $fileName): string
    {
        if ($fileName === null || $fileName === '') {
            return '';
        }

        return base_url(
            'media/uploads/'
            . rawurlencode(basename($collection))
            . '/'
            . rawurlencode(basename($fileName))
        );
    }
}

if (!function_exists('image_placeholder_url')) {
    function image_placeholder_url(string $fileName = 'No-Image-Placeholder.svg'): string
    {
        return base_url('assets/media/placeholders/' . rawurlencode(basename($fileName)));
    }
}

if (!function_exists('program_thumbnail_url')) {
    function program_thumbnail_url(?string $fileName): string
    {
        return writable_upload_url('thumbnails', $fileName, image_placeholder_url());
    }
}
