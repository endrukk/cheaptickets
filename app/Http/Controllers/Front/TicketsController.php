<?php

namespace App\Http\Controllers\Front;

use App\Classes\Front\Ryanair;
use App\Classes\Front\Wizzair;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TicketsController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function applyCheap(Request $request)
    {
        Auth::user()->cheap_flights = 1;
        Auth::user()->save();
        $request->session()->put('app_message', 'You have successfully subscribed to Cheap flights!');
        $request->session()->put('app_message_type', 'success');

        return redirect()->route('dashboard');
    }

    public function generateWizzair(Request $request)
    {
        $wizz = new Wizzair();
        if($wizz->generateCheap()) {
            $request->session()->put('app_message', 'Wizzair Synced!');
            $request->session()->put('app_message_type', 'success');
        }else{
            $request->session()->put('app_message', 'Wizzair failed to Sync!');
            $request->session()->put('app_message_type', 'danger');
        }

        return redirect()->route('dashboard');
    }
    public function generateRyanair(Request $request)
    {
        $ryanair = new Ryanair();
        if($ryanair->generateCheap()) {
            $request->session()->put('app_message', 'Ryanair Synced!');
            $request->session()->put('app_message_type', 'success');
        }else{
            $request->session()->put('app_message', 'Ryanair failed to Sync!');
            $request->session()->put('app_message_type', 'danger');
        }
        return redirect()->route('dashboard');
    }


    public function ajaxFareFinder(Request $request){
//        TODO: create here a flight checker:
//        check the cheaoest available prices for this airport for ryanair and wizzair
//        TODO extra: add easyjet scraper
    }
    public function ajaxAirportFinder(Request $request){
        $airport = $request->input('airport');
        $airports = DB::table('airports AS a')
            ->join('cities AS c', 'a.id_city', '=', 'c.id')
            ->join('countries AS cn', 'c.id_country', '=', 'cn.id')
            ->select('a.id', 'a.code', 'a.name AS airport', 'c.name AS city', 'cn.name AS country')
            ->where('a.code',  $airport )
            ->orWhere('a.name', 'like', '%' . $airport . '%')
            ->orWhere('c.name', 'like', '%' . $airport . '%')
            ->get();


        return json_encode($airports);
    }


}
