<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Inertia\Inertia;
use App\Models\Referral;
use App\Models\Rekening;
use App\Models\MetaSetting;
use Illuminate\Http\Request;
use App\Interfaces\MovieServiceInterface;

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
        $prop['referralCode'] = request()->get('ref', null);
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

    public function referralPage()
    {
        $auth = auth()->user();

        $user = User::find($auth->id);
        $prop['referralCode'] = $user->name;
        $setting = json_decode(\Illuminate\Support\Facades\Storage::get('settings.json'), true);
        $prop['referralLink'] = url('/plan?ref=') . $user->name;
        $prop['referrals'] = Referral::where('user_id', $user->id)->with('referredUser')->get();
        $prop['totalCommission'] = $user->totalCommission();
        $prop['availableCommission'] = $user->availableCommission();
        $prop['withdrawnCommission'] = $user->withdrawnCommission();
        $prop['totalReferrals'] = $user->referrals()->count();
        $prop['user_id'] = $user->id;
        $data['prop'] = $prop;
        return Inertia::render('referral', $data);
    }

    public function requestWithdrawal(Request $request)
    {
        $setting = json_decode(file_get_contents(storage_path('app/settings.json')) ,true);
        $phone = $request->phone;
        $user_id = $request->user_id;
        $nama_bank = $request->bank_name;
        $account_number = $request->account_number;
        $holder_name = $request->holder_name;
        $user = User::find($user_id);
        $message = "
-- ".$setting['site_name']." --\n
*REQUEST WITHDRAWAL REFERRAL COMMISSION*\n\n
- Akun    : ".$user->name."\n
- License : ".$user->license_key."\n
- Phone   : ".$phone."\n
- Jml. Penarikan : Rp ".str_replace(',','.',number_format($user->availableCommission()))."\n\n
- Pembayaran ke : ".$account_number. " ( ". $nama_bank. " ) A/n ".$holder_name."\n
- Tanggal : ".date('D,d-m-Y H:i')."\n
";
        $send = \App\Helpers\Helper::whatsappSend($message,$setting['no_whatsapp_admin']);
         Referral::where('user_id', $user_id)->update(['status' => 'withdrawn']);

        return response()->json(['success' => true]);
    }
}
