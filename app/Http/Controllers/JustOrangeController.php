<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Inertia\Inertia;
use App\Models\MetaSetting;
use Illuminate\Http\Request;
use App\Interfaces\MovieServiceInterface;
use App\Models\Rekening;

class JustOrangeController extends Controller
{
    protected $service;
    
   
    public function index(Request $request): \Inertia\Response
    {
        // dd(session('errors'));
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
    public function search(Request $request)
    {
        $query= $request->get('query');
        if(!$query) return abort(404);
        $prop['query'] = $query;
        $data['prop'] = $prop;
        return Inertia::render('search',$data);
    }
    public function plan()
    {
        $setting = json_decode(\Illuminate\Support\Facades\Storage::get('settings.json'), true);
        $formatPhone = function($number){
            if(substr($number,0,1) == '0'){
                $number = '62'.substr($number,1);
            }
            return preg_replace('/[^0-9]/', '', $number);
        };
        $prop['plans'] = Plan::where('active', true)->get();
        $prop['checkout_url'] = "https://wa.me/".$formatPhone($setting['no_whatsapp_admin'])."?text=Saya%20ingin%20berlangganan%20plan%20";
        $prop['paymentMethod'] = Rekening::where('active', true)->get()->groupBy('type'); 
        $data['prop'] = $prop;
        return Inertia::render('plan',$data);
    }

    public function ComingSoonService(Request $request)
    {
        $service = $request->service;
        $data['service'] = $service;
        return Inertia::render('coming-soon',$data);
    }

    public function limitDevices()
    {
        $data['limitReached'] = true;
        $data['maxDevices'] = auth()->user()->activeSubscription->plan->max_devices;
        $data['devices'] = auth()->user()->userDevices()->where('revoked', false)->get();
        return Inertia::render('limit-devices',$data);
    }
    public function blockedPage()
    {
        return Inertia::render('blocked');
    }
    public function stopPage()
    {
        return Inertia::render('alert', ['message' => 'Anda tidak diizinkan mengakses layanan ini. Silakan hubungi dukungan pelanggan untuk informasi lebih lanjut.']);
    }
    public function invoicePage(Request $request)
    {
        $prop['invoice'] = $request->invoice;
        $prop['order'] = \App\Models\Order::where('invoice',$request->invoice)->first();
        $prop['rekening'] = Rekening::where('active', true)->where('type',$prop['order']->payment_method)->get();
        $data['prop'] = $prop;
        return Inertia::render('invoice',$data);
    }
}
