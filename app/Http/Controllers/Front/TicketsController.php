<?php

namespace App\Http\Controllers\Front;

use App\Classes\External\Scraper;
use App\Classes\Front\City;
use App\Classes\Front\Company;
use App\Classes\Front\Country;
use App\Classes\Front\Flight;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Client;

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

        return redirect()->route('home');
    }

    private function serializeRyanair()
    {
        $return = '?';
        foreach ($this->ryanairSettings as $key => $value) {
            $return .= $key . '=' . $value . '&';
        }
        return trim($return, '&');
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

    public function generateRyanair(Request $request)
    {

        $this->setRyanairSettings(array(
            'departureAirportIataCode' => 'BUD',
            'durationFrom' => '03',
            'durationTo' => '07',
            'inboundDepartureDateFrom' => date('Y-m-d'),
            'inboundDepartureDateTo' => date('Y-m-d', strtotime("+3 month")),
            'language' => 'hu',
            'limit' => '999',
            'market' => 'hu-hu',
            'offset' => '0',
            'outboundDepartureDateFrom' => date('Y-m-d'),
            'outboundDepartureDateTo' => date('Y-m-d', strtotime("+3 month")),
            'priceValueTo' => '16000',
        ));

        $this->ryanairResult = json_decode(file_get_contents($this->ryanairApi . $this->serializeRyanair()));

        foreach ($this->ryanairResult->fares as $fare) {

            $flight = new Flight;
            $flight->id_departure = $this->getCity($fare->outbound->departureAirport->name);
            $flight->id_departure_country = $this->getCountry($fare->outbound->departureAirport->countryName);

            $flight->id_destination = $this->getCity($fare->outbound->arrivalAirport->name);
            $flight->id_destination_country = $this->getCountry($fare->outbound->arrivalAirport->countryName);

            $flight->price = $fare->outbound->price->value + $fare->inbound->price->value;
            $flight->date = date("Y-m-d H:i:s", strtotime($fare->outbound->departureDate));

            /*find difference between go there and come back ==> it's in secconds so convert to days*/
            $flight->length = intval(
                (strtotime($fare->inbound->departureDate)
                    - strtotime($fare->outbound->departureDate))
                / (60 * 60 * 24)
            );

            $flight->id_company = $this->getCompany('Ryanair');

            if (!Flight::where('id_departure', '=', $flight->id_departure)
                ->where('id_destination', '=', $flight->id_destination)
                ->where('date', '=', $flight->date)
                ->where('length', '=', $flight->length)
                ->where('id_company', '=', $flight->id_company)
                ->first()
            ) {
                $flight->save();
            }
        }


        $request->session()->put('app_message', 'Ryanair Synced!');
        $request->session()->put('app_message_type', 'success');

        return redirect()->route('home');
    }

    public function generateWizzair(Request $request){
        $origin = 'BUD';
        $destination = 'OPO';
        $departure_date = '2018-02-10';
        $return_date = '2018-03-20';

        $wizzair = new Scraper();
        $wizzair->cacheOff();
        $wizzair->verboseOff();

        $wizzair->setAdults(1);
        $wizzair->setCookieFileName(tempnam(sys_get_temp_dir(), 'wizzaircookie.'));

        $api_detected = $wizzair->detect_api_version();
        if($api_detected)
            echo "Detected api version: {$wizzair->getApiVersion()}\n";

        $wizzair->setDepartureDate($departure_date);
        $wizzair->setReturnDate($return_date);

        try {
            $flights = $wizzair->getTrips();
            echo json_encode($flights, JSON_PRETTY_PRINT);
        }catch(\Exception $e)
        {
            echo "An Error ocurred: ", $e->getMessage(), ". You may want to try changing search parameters.";
            echo "\nConnection Info: ";
            var_export($wizzair->getInfo());
            exit(1);
        }

    }

    public function getCity($city){
        if(!City::where('name', $city)->first()){
            $insert = new City();
            $insert->name = $city;
            $insert->save();
        }
        return City::where('name', $city)->first()->id;
    }

    public function getCountry($country){
        if(!Country::where('name', $country)->first()){
            $insert = new Country();
            $insert->name = $country;
            $insert->save();
        }
        return Country::where('name', $country)->first()->id;
    }

    public function getCompany($company){
        if(!Company::where('name', $company)->first()){
            $insert = new Company();
            $insert->name = $company;
            $insert->save();
        }
        return Company::where('name', $company)->first()->id;
    }

}
