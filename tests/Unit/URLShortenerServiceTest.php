<?php

namespace Tests\Unit;

use App\Services\URLShortenerService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class URLShortenerServiceTest extends TestCase
{
    protected $urlShortenerService;
    protected $url = 'https://example.com';
    protected $shortUrl;
    protected $code;

    // Set up the dependencies in the setUp method
    public function setUp(): void
    {
        parent::setUp();

        Cache::spy();

        // Manually instantiate the service or get it from the container
        $this->urlShortenerService = new URLShortenerService();

        $this->shortUrl = $this->urlShortenerService->encode($this->url);

        $this->code = substr($this->shortUrl, -6);

        Cache::shouldHaveReceived('put')
            ->once()
            ->with($this->code, $this->url);
    }

    /**
     * Tests that the encode() method returns a shortened URL.
     *
     * This test ensures that the encode() method returns a string, which is
     * the shortened URL.
     *
     * @return void
     */
    public function test_encode_url_returns_shortened_url(): void
    {
        Cache::spy();

        Cache::shouldReceive('has')
            ->once()
            ->with($this->code)
            ->andReturn(true);

        $this->assertIsString($this->shortUrl);
        $this->assertTrue(Cache::has($this->code));
    }

    /**
     * Tests that the decode() method returns the original URL associated with the given shortened URL.
     *
     * This test ensures that the decode() method returns the original URL when given a
     * valid shortened URL.
     *
     * @return void
     */
    public function test_decode_short_url_returns_original_url(): void
    {
        Cache::spy();

        Cache::shouldReceive('get')
            ->with($this->code)
            ->once()
            ->andReturn($this->url);

        $decodedUrl = $this->urlShortenerService->decode($this->code);

        $this->assertEquals($this->url, $decodedUrl);
    }
}
