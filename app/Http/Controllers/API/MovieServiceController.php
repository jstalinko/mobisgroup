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
    private $pageNo;
    private $searchQuery;
    private $lang;

    protected $jdigital_apikey;
    public function __construct(Request $request)
    {
        $name = $request->route('service');
        $class = "App\\Services\\" . ucfirst(string: $name) . "Service";
        $this->service = app(class_exists($class) ? $class : \App\Services\DramaboxService::class);

        if ($request->page) {
            $this->pageNo = $request->page;
        }
        if ($request->query) {
            $this->searchQuery = $request->query;
        }
        if($request->lang)
        {
            $this->lang = $request->lang;
        }
        $this->jdigital_apikey = env('JDIGITAL_APIKEY');
    }

    public function send_response(array|Object $data, int $status = 200, bool $success = true)
    {
        return response()->json([
            'success' => $success,
            'data' => $data['data'],
            'status' => $status,
            'cached_at' => $data['cached_at'] ?? null,
            'timestamp' => now()->toDateTimeString(),
        ], $status, [], JSON_PRETTY_PRINT);
    }

    public function getTheater(Request $request) {
        $data= $this->service->getTheater($this->lang);
        if(isset($data['data']))
        {
            return $this->send_response($data , 200,true);
        }else{
            return $this->send_response([] , 500,false);

        }
    }
    public function getRecommend() {
        $data = $this->service->getRecommend($this->lang , $this->pageNo);
        if(isset($data['data']))
        {
            return $this->send_response($data , 200,true);
        }else{
            return $this->send_response([] , 500,false);

        }
    }
    public function getCategory() {}
    public function getTheaterDetail(Request $request)
    {
        $bookId = $request->bookId;
        if(!$bookId) return $this->send_response(['error' => 'Required bookId'],404,false);

        $data = $this->service->getTheaterDetail($this->lang,$bookId);
        if(isset($data['data']))
        {
            return $this->send_response($data , 200,true);
        }else{
            return $this->send_response([] , 500,false);

        }
    }

    public function getChapterDetail(Request $request)
    {
        $bookId = $request->bookId;
        if(!$bookId) return $this->send_response(['error' => 'Required bookId'],404,false);


        $data= $this->service->getChapterDetail($this->lang,$bookId);
        if(isset($data['data']))
        {
            return $this->send_response($data , 200,true);
        }else{
            return $this->send_response([] , 500,false);

        }
    }
    public function getSearch(Request $request)
    {
        $query = $request->input('query');
        if(!$query)
        {
            return $this->send_response(['error' => 'Required query or keyword for search'],404,false);
        }
        $data = $this->service->getSearch($this->lang,$query,$this->pageNo);
         if(isset($data['data']))
        {
            return $this->send_response($data , 200,true);
        }else{
            return $this->send_response([] , 500,false);

        }
    }
    public function getStream(Request $request)
    {
        $bookId = $request->bookId;
        if(!$bookId) return $this->send_response(['error' => 'Required bookId'],404,false);
        $data= $this->service->getStream($this->lang,$bookId);
        if(isset($data['data']))
        {
            return $this->send_response($data , 200,true);
        }else{
            return $this->send_response([] , 500,false);

        }
    }
}
