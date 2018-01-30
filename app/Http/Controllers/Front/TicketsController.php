<?php

namespace App\Http\Controllers\Front;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class TicketsController extends Controller
{

    private $ryanairSettings = array();
    private $ryanairApi = 'https://api.ryanair.com/farefinder/3/roundTripFares';
    private $ryanairResult = "";

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->setRyanairSettings(array(
            'departureAirportIataCode' => 'BUD',
            'durationFrom' => '03',
            'durationTo' => '07',
            'inboundDepartureDateFrom' => date('Y-m-d'),
            'inboundDepartureDateTo' => date('Y-m-d',strtotime("+3 month")),
            'language' => 'hu',
            'limit' => '999',
            'market' => 'hu-hu',
            'offset' => '0',
            'outboundDepartureDateFrom' => date('Y-m-d'),
            'outboundDepartureDateTo' => date('Y-m-d',strtotime("+3 month")),
            'priceValueTo' => '16000',
        ));
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
        $request->session()->put('app_message','You have successfully subscribed to Cheap flights!');
        $request->session()->put('app_message_type','success');

        return redirect()->route('home');
    }

    private function serializeRyanair()
    {
        $return = '?';
        foreach ($this->ryanairSettings as $key => $value){
            $return .= $key . '=' . $value . '&';
        }
        return trim($return,'&');
    }

    /**
     * @return array
     */
    public function getRyanairSettings()
    {
        return $this->ryanairSettings;
    }

    /**
     * @param array $ryanairSettings
     */
    public function setRyanairSettings($ryanairSettings)
    {
        $this->ryanairSettings = $ryanairSettings;
    }


    public function getRyanair()
    {
        $this->ryanairResult = json_decode(file_get_contents($this->ryanairApi . $this->serializeRyanair()));

    }

}
