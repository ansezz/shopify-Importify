<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OhMyBrew\ShopifyApp\Facades\ShopifyApp;


class ImporterController extends Controller
{
    protected $client;
    protected $shop;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();
        $this->shop = ShopifyApp::shop(\request()->get('shop'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $url = $request->get('url');
        $url = parse_url($url);
        $slug = $request->get('slug');
        $url = $url['host'] . '/products/' . $slug;

        $product = [];
        try {
            $res = $this->client->request('GET', $url . '.json');
        } catch (GuzzleException $e) {
            $res = null;
        }
        if ($res && $res->getStatusCode() === 200) {
            $product = $res->getBody();
            $product = json_decode($product, true);
        }

        $product['product']['vendor'] = $this->shop->shopify_domain;

        $images_sku = [];
        foreach ($product['product']['variants'] as &$variant) {
            $found = array_filter($product['product']['images'], function ($image) use ($variant) {
                return $variant['image_id'] === $image['id'];
            });
            unset($variant['image_id']);
            if ($found) {
                $images_sku[$variant['sku']] = reset($found)['src'];
            }
        }
        unset($variant);

        $request2 = $this->shop->api()->rest('POST', '/admin/products.json', $product);
        $result = $request2->body;


        if (count($product['product']['variants']) > 1) {
            $sku_images_ids = [];
            foreach ($images_sku as $sku => $src) {
                foreach ($result->product->images as $image) {
                    $path_parts = pathinfo($src);
                    if (strpos($image->src, $path_parts['filename']) !== false) {
                        $sku_images_ids[$sku] = $image->id;
                        break;
                    }
                }
            }

            $variants = collect($result->product->variants)->keyBy('sku');
            $new_variants = [];
            $variants->map(function ($item, $key) use ($sku_images_ids, &$new_variants) {
                if (isset($sku_images_ids[$key])) {
                    $new_variants[] = [
                        'id'       => $item->id,
                        'image_id' => $sku_images_ids[$key],
                    ];
                }
            });

            if (count($new_variants) > 0) {
                $request2 = $this->shop->api()->rest('PUT', '/admin/products/' . $result->product->id . '.json', [
                    'product' => [
                        'id'       => $result->product->id,
                        'variants' => $new_variants,
                    ],
                ]);

                $result = $request2->body;
            }
        }

        return response()->json($result);

    }
}
