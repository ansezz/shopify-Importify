<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $url = $request->get('url');
        $url = strtok($url, '?');
        $page = $request->get('page');

        if (str_contains($url, '/collections/') && !str_contains($url, '/products/')) {
            $url .= '/products.json';
        } elseif (str_contains($url, '/products/')) {
            $url .= '.json';
        }

        $product = [];
        try {
            $res = $this->client->request('GET', $url, [
                'query' => ['page' => $page],
            ]);
        } catch (GuzzleException $e) {
            $res = null;
        }
        if ($res && $res->getStatusCode() === 200) {
            $product = $res->getBody();
            $product = json_decode($product, true);
        }

        return response()->json([
            'url'    => $url,
            'result' => $product,
        ]);
    }
}
