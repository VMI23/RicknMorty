<?php

namespace RickAndMorty;

use Carbon\Carbon;

class Cache
{
    public static function remember(string $key, string $data, int $ttl): void
    {
        $cachedFile = self::getCacheFilePath($key);
        $expiresAt = Carbon::now()->addSeconds($ttl);

        $cachedData = [
            'expires_at' => $expiresAt,
            'data' => $data
        ];

        file_put_contents($cachedFile, json_encode($cachedData));
    }

    private static function getCacheFilePath(string $key): string
    {
        return '../cache/' . $key . '.json';
    }

    public static function get(string $key)
    {
        $cachedFile = self::getCacheFilePath($key);

        if (self::has($key)) {
            $content = json_decode(file_get_contents($cachedFile));
            return $content->data;
        }

        return null;
    }

    public static function has($key): bool
    {
        $cachedFile = self::getCacheFilePath($key);

        if (file_exists($cachedFile)) {
            $content = json_decode(file_get_contents($cachedFile));
            $expiresAt = Carbon::parse($content->expires_at);
            return Carbon::now()->lte($expiresAt);
        }

        return false;
    }

    public static function forget(string $key): void
    {
        $cachedFile = self::getCacheFilePath($key);

        if (file_exists($cachedFile)) {
            unlink($cachedFile);
        }
    }
}