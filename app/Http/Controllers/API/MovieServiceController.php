<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interfaces\MovieServiceInterface;
use Illuminate\Http\Request;

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
        ], $status,[],JSON_PRETTY_PRINT);
    }

    public function getTheater(Request $request)
    {
        $data = $this->service->getTheater($request);
        return response()->json($data,200,[],JSON_PRETTY_PRINT);
    }

    public function getBootData()
    {
        return response()->json($this->service->getBootData());
    }

    public function getRecommendMovie(Request $request)
    {
       $data = $this->service->getRecommendMovie($request);
        return response()->json($data,200,[],JSON_PRETTY_PRINT);
    }

    public function getCategory(Request $request)
    {
         $data = $this->service->getCategory();
        return response()->json($data,200,[],JSON_PRETTY_PRINT);
    }
    public function getChapterDetail(Request $request)
    {
        $bookId = $request->bookId;
        $data = $this->service->getChapterDetail($bookId);
        return response()->json($data,200,[],JSON_PRETTY_PRINT);
    }
    public function getTheaterDetail(Request $request)
    {
        $bookId = $request->bookId;
        
        $data= $this->service->getTheaterDetail($bookId);
        return response()->json($data,200,[],JSON_PRETTY_PRINT);
    }
}
