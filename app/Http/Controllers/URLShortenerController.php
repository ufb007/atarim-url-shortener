<?php

namespace App\Http\Controllers;

use App\Http\Requests\DecodeRequest;
use App\Http\Requests\EncodeRequest;
use App\Services\URLShortenerService;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class URLShortenerController extends Controller
{
    public function __construct(protected URLShortenerService $urlShortenerService)
    {
        //
    }

    /**
     * Redirects to the URL associated with the given shortened URL.
     *
     * @param string $short_url
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function redirect(string $code) {
        try {
            return redirect($this->urlShortenerService->decode($code));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Encodes a given URL into a shortened version and returns it as a JSON response.
     *
     * @param EncodeRequest $request The request object containing the URL to be shortened.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the shortened URL.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function encode(EncodeRequest $request) {
        try {
            return response()->json([
                "short_url" => $this->urlShortenerService->encode($request->url)
            ], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Decodes a given shortened URL and returns it as a JSON response.
     *
     * @param DecodeRequest $request The request object containing the shortened URL to be decoded.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the decoded URL.
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function decode(DecodeRequest $request) {
        try {
            $code = $request->code;
            
            return response()->json([
                "url" => $this->urlShortenerService->decode($code)
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
