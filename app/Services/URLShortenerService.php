<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class URLShortenerService
{
    /**
     * Encodes a given URL into a shortened version.
     *
     * @param array $request The request data containing the 'url' to be shortened.
     * @return string The shortened URL.
     */
    public function encode(string $url): string {
        $code = Str::random(6);
        $shortenedUrl = config('app.url') . '/' . $code;

        Cache::put($code, $url);

        return $shortenedUrl;
    }

    /**
     * Decodes a given shortened URL to its original URL.
     *
     * @param string $shortUrl The shortened URL to be decoded.
     * @return string The original URL associated with the shortened URL.
     */
    public function decode(string $code): string {
        if (!Cache::has($code)) {
            throw new \Exception('Code not found');
        }

        return Cache::get($code);
    }
}