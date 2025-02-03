<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class URLShortenerControllerTest extends TestCase
{
    protected string $url = 'https://example.com';
    protected string $baseUrl;
    protected string $apiEndpoint = '/api/v1/';
    protected string $code;
    protected $postEncodeResponse;
    protected string $shortUrl;

    public function setUp(): void
    {
        parent::setUp();

        $this->baseUrl = config('app.url');

        Cache::spy();

        $this->postEncodeResponse = $this->post($this->baseUrl . $this->apiEndpoint . 'encode', [
            'url' => $this->url,
        ]);

        $this->shortUrl = $this->postEncodeResponse->json('short_url');
        $this->code = substr($this->shortUrl, -6);
    }

    /**
     * Tests the creation of a shortened URL via the encode endpoint.
     *
     * This test verifies that a POST request to the encode endpoint
     * returns a JSON response with a status of HTTP_CREATED and a
     * 'short_url' field in the response structure. It also checks
     * that the URL is cached with the expected code as the key.
     */
    public function test_encode_create_short_url(): void
    {
        Cache::shouldHaveReceived('put')
            ->once()
            ->with($this->code, $this->url);

        $this->postEncodeResponse->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'short_url' => $this->shortUrl,
            ]);
    }

    /**
     * Tests the retrieval of the original URL via the decode endpoint.
     *
     * This test verifies that a POST request to the decode endpoint
     * with a valid code returns a JSON response with a status of HTTP_OK
     * and a 'url' field in the response structure with the original URL.
     */
    public function test_decode_get_original_url(): void
    {
        Cache::shouldReceive('has')
            ->with($this->code)
            ->once()
            ->andReturn(true);

        Cache::shouldReceive('get')
            ->with($this->code)
            ->once()
            ->andReturn($this->url);

        $response = $this->post($this->baseUrl . $this->apiEndpoint . 'decode', [
            'code' => $this->code,
        ]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'url' => $this->url,
            ]);
        
        $this->assertEquals($this->url, $response->json('url'));
    }

    /**
     * Tests the retrieval of the original URL via the decode endpoint with a non-existent code.
     *
     * This test verifies that a POST request to the decode endpoint
     * with a code that does not exist returns a JSON response with a status of HTTP_NOT_FOUND
     * and a 'error' field in the response structure with the value 'code not found'.
     */
    public function test_decode_get_original_url_not_found(): void
    {
        $code = 'Rt34GH';

        Cache::shouldReceive('has')
            ->with($code)
            ->once()
            ->andReturn(false);

        $response = $this->post($this->baseUrl . $this->apiEndpoint . 'decode', [
            'code' => $code,
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND)
            ->assertJson([
                'error' => 'code not found',
            ]);
    }

    /**
     * Tests the redirection of the shortened URL to the original URL.
     *
     * This test verifies that a GET request to the shortened URL
     * redirects to the original URL.
     */
    public function test_url_redirect_to_original_url(): void
    {
        Cache::shouldReceive('get')
            ->with($this->code)
            ->once()
            ->andReturn($this->url);

        $this->get($this->baseUrl . '/' . $this->code)
            ->assertRedirect($this->url);
    }
}
