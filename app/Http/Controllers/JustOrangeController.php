<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\MetaSetting;
use Illuminate\Http\Request;
use App\Interfaces\MovieServiceInterface;

class JustOrangeController extends Controller
{
    protected $service;
    
    public function __construct(MovieServiceInterface $service)
    {
        $this->service = $service;
        
    }
    public function index(Request $request): \Inertia\Response
    {
        
        
        
        return Inertia::render('justorange-default');
    }
    public function movieDetail(Request $request):\Inertia\Response
    {
        $bookId = $request->bookId;
        if(!$bookId)
        {
            abort(404);
        }
        $prop['bookId'] = $bookId;
        $data['prop'] = $prop;
        return Inertia::render('detail',$data);
    }

    public function moviePlay(Request $request):\Inertia\Response
    {
        $prop['bookId'] = $request->bookId;
        $prop['episode'] = $request->episode;
        $data['prop'] = $prop;
        return Inertia::render('video' ,$data );
    }
    public function blankPage(Request $request)
    {
        $data['meta'] = MetaSetting::find($request->id);
        return view('blank',$data);
    }

    public function ComingSoonService(Request $request)
    {
        $service = $request->service;
        $data['service'] = $service;
        return Inertia::render('coming-soon',$data);
    }
}
