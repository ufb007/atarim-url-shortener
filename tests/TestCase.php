<?php

namespace Tests;

use App\Services\URLShortenerService;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    protected URLShortenerService $urlShortenerService;
    protected string $url = 'https://example.com';
    protected string $shortUrl;
    protected string $code;
    protected string $baseUrl;
    protected string $apiEndpoint = '/api/v1/';
    protected TestResponse $postEncodeResponse;

    public function setup(): void
    {
        parent::setUp();
        
        $this->urlShortenerService = new URLShortenerService();
    }
}
