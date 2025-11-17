<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Interfaces\MovieServiceInterface;
use GuzzleHttp\Exception\RequestException;

class DramaboxService implements MovieServiceInterface
{

    protected $api_url;
    public function __construct(){
        $this->api_url = 'https://dracin.javaradigital.com/api/v1/dramabox';
    }
 public function http($url, $headers = [], $post = null)
{
    $cacheKey = 'http_cache_' . md5($url);
    
    // Cek cache Redis hanya untuk GET request
    if (empty($post)) {
        $cached = Redis::get($cacheKey);
        if ($cached) {
            return json_decode($cached, true);
        }
    }

    try {
        $client = new Client([
            'proxy'  => \App\Helpers\Dramabox::getProxies(),
            'verify' => false,
            'timeout' => 20,
        ]);

        $options = [
            'headers' => $headers
        ];

        if (!empty($post)) {
            // Jika array → form_params
            if (is_array($post)) {
                $options['form_params'] = $post;
            } else {
                // Jika string → dianggap JSON body
                $options['body'] = $post;
                $options['headers']['Content-Type'] = 'application/json';
            }

            // POST request
            $response = $client->post($url, $options);
        } else {
            // GET request
            $response = $client->get($url, $options);
        }

        $result = [
            'success' => true,
            'code'   => $response->getStatusCode(),
            'body'   => $response->getBody()->getContents()
        ];

        // Simpan ke Redis hanya untuk GET (expire 1 jam = 3600 detik)
        if (empty($post)) {
            Redis::setex($cacheKey, 3600*12, json_encode($result));
        }

        return $result;
        
    } catch (RequestException $e) {

        $error = [
            'success'  => false,
            'code'    => $e->getCode(),
            'message' => $e->getMessage(),
        ];

        // Cache error selama 5 menit (300 detik)
        if (empty($post)) {
            Redis::setex($cacheKey, 300, json_encode($error));
        }

        return $error;
    }
}

    public function getTheater(Request $request)
    {
         $req = $this->http($this->api_url.'/theaters');
         if($req['success'])
         {
            return json_decode($req['body'],true);
         }else{
            return [];
         }
    }

    public function getRecommendMovie(Request $request)
    {
        $page = $request->page ?? 1;
        $req = $this->http($this->api_url.'/recommend/'.$page);
        if($req['success'])
        {
            return json_decode($req['body'],true);
        }else{
            return [];
        }
    }

    public function getCategory()
    {
        $req = $this->http($this->api_url.'/categories');
        if($req['success'])
        {
            return json_decode($req['body'],true);
        }else{
            return [];
        }
    }

    public function getChapterDetail($bookId)
    {
        $req = $this->http($this->api_url.'/detail/recommend/'.$bookId);
        if($req['success'])
        {
            return json_decode($req['body'],true);
        }else{
            return [];
        }
    }
    public function getTheaterDetail($bookId)
    {
        
        $req = $this->http($this->api_url.'/detail/'.$bookId);
         if($req['success'])
        {
            return json_decode($req['body'],true);
        }else{
            return [];
        }
    }
    

    public function getBootData() {}
}
