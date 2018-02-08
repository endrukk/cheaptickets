<?php

namespace App\Http\Controllers\Front;

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
        $request =[
            'departureStation' => 'BUD',
            'destinationCategory' => 0,
            'dateRange' => 0,
            'from' => null,
            'to' => null,
            'travelDuration' => 2,
            'priceRange' => 0
        ];
        $headers =[
            ':authority' => 'be.wizzair.com',
            ':referer' => 'https://wizzair.com/hu-hu/jaratok/utazastervezo',
            ':method' => 'POST',
            ':path' => '/7.8.6/Api/search/inspirationalFlights',
            ':scheme' => 'https',
            'accept' => 'application/json, text/plain, */*',
            'accept-encoding' => 'gzip, deflate, br',
            'accept-language' => 'hu-HU,hu;q=0.9,en-US;q=0.8,en;q=0.7',
            'content-length' => '120',
            'content-type' => 'application/json',
        ];
        $url = 'https://be.wizzair.com/7.8.6/Api/search/inspirationalFlights';

        echo '<pre>';

        $client = new Client();

        $client = $client->post($url, [
            RequestOptions::HEADERS => $headers,
            RequestOptions::JSON => $request
        ]);
        die(var_dump(
           $client->
        ));


        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, ($request));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);

//execute post
        print $response;
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
