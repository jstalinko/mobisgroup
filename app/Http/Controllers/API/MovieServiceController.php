<?php

namespace App\Http\Controllers\API;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Interfaces\MovieServiceInterface;

class MovieServiceController extends Controller
{
    protected $service;

    public function __construct(MovieServiceInterface $service)
    {
        $this->service = $service;
    }

    public function send_response(array|Object $data, int $status = 200, bool $success = true)
    {
        return response()->json([
            'success' => $success,
            'data' => $data,
            'status' => $status,
            'timestamp' => now()->toDateTimeString(),
        ], $status, [], JSON_PRETTY_PRINT);
    }

    public function getTheater(Request $request)
    {
        $data = $this->service->getTheater($request);
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }

    public function getBootData()
    {
        return response()->json($this->service->getBootData());
    }

    public function getRecommendMovie(Request $request)
    {
        $data = $this->service->getRecommendMovie($request);
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }

    public function getCategory(Request $request)
    {
        $data = $this->service->getCategory();
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
    public function getChapterDetail(Request $request)
    {
        $bookId = $request->bookId;
        $data = $this->service->getChapterDetail($bookId);
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
    public function getTheaterDetail(Request $request)
    {
        $bookId = $request->bookId;

        $data = $this->service->getTheaterDetail($bookId);
        return response()->json($data, 200, [], JSON_PRETTY_PRINT);
    }
    public function getPlayVideo(Request $request)
    {
        $bookId = $request->bookId;
        $episode = ($request->episode - 1);
        $quality = $request->quality ?? 720;

        // Redis Cache Key
        $cacheKey = "dramabox:play:{$bookId}:{$episode}:{$quality}";
        $cacheTTL = 600; // 10 menit

        // Cek dulu apakah ada di cache
        if (Redis::exists($cacheKey)) {
            $cached = json_decode(Redis::get($cacheKey), true);

            return response()->json([
                'success' => true,
                'message' => 'cached',
                'data'    => $cached
            ], 200);
        }

        // Jika belum ada di cache → panggil API
        $client = new \GuzzleHttp\Client([
            'proxy'  => \App\Helpers\Dramabox::getProxies(),
            'verify' => false
        ]);

        $headers = [
            'accept' => 'application/json, text/plain, */*',
            'accept-language' => 'en-CN,en;q=0.9,jv-CN;q=0.8,jv;q=0.7,id-ID;q=0.6,id;q=0.5,en-GB;q=0.4,en-US;q=0.3',
            'priority' => 'u=1, i',
            'referer' => 'https://drama.udinchan.moe/watch/41000121122/0',
            'sec-ch-ua' => '"Chromium";v="142", "Google Chrome";v="142", "Not_A Brand";v="99"',
            'sec-ch-ua-mobile' => '?1',
            'sec-ch-ua-platform' => 'Android',
            'sec-fetch-dest' => 'empty',
            'sec-fetch-mode' => 'cors',
            'sec-fetch-site' => 'same-origin',
            'user-agent' => 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Mobile Safari/537.36'
        ];

        try {
            $resp = $client->get(
                'https://drama.udinchan.moe/api/dramabox/stream?bookid=' . $bookId . '&episode=' . $episode . '&quality=' . $quality,
                ['headers' => $headers]
            );
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'API error: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }

        $resp = json_decode($resp->getBody()->getContents(), true);

        if (isset($resp['episode'])) {
            // simpan di redis
            Redis::setex($cacheKey, $cacheTTL, json_encode($resp));

            return response()->json([
                'success' => true,
                'message' => 'success',
                'data'    => $resp
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'failed',
                'data'    => null
            ], 200);
        }
    }
}
