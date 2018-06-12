<?php
/**
 * Created by PhpStorm.
 * User: endre
 * Date: 2018. 02. 19.
 * Time: 20:31
 */

namespace App\Classes\Front;


use App\Classes\Front\Flight;
use Illuminate\Http\Request;

class Ryanair
{

    private $settings = array();
    private $api = 'https://api.ryanair.com/farefinder/3/roundTripFares';
    private $result = "";

    public function _construct()
    {
        $this->middleware('auth');
    }
    
    private function serialize()
    {
        $return = '?';
        foreach ($this->settings as $key => $value) {
            $return .= $key . '=' . $value . '&';
        }
        return trim($return, '&');
    }

    /**
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }

    /**
     * @param array $settings
     */
    public function setSettings($settings)
    {
        $this->settings = $settings;
    }


    public function getResources()
    {
        $this->result = json_decode(file_get_contents($this->api . $this->serialize()));
    }

    public function generateCheap()
    {

        if($this->settings === array()){
            $this->setSettings(array(
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
                'priceValueTo' => '999999',
            ));
        }

        $this->result = json_decode(file_get_contents($this->api . $this->serialize()));

        foreach ($this->result->fares as $fare) {


            $flight = new Flight();
            $flight->id_from = Airport::getAirport($fare->outbound->departureAirport->iataCode);

            $flight->id_to = Airport::getAirport($fare->outbound->arrivalAirport->iataCode);

            $flight->price = $fare->outbound->price->value + $fare->inbound->price->value;
            $flight->date = date("Y-m-d H:i:s", strtotime($fare->outbound->departureDate));

            /*find difference between go there and come back ==> it's in secconds so convert to days*/
            $flight->length = intval(
                (strtotime($fare->inbound->departureDate)
                    - strtotime($fare->outbound->departureDate))
                / (60 * 60 * 24)
            );

            $flight->id_company = Company::getCompany('Ryanair');

            if (!Flight::where('id_from', '=', $flight->id_from)
                ->where('id_to', '=', $flight->id_to)
                ->where('date', '=', $flight->date)
                ->where('length', '=', $flight->length)
                ->where('id_company', '=', $flight->id_company)
                ->first()
            ) {
                $flight->save();
            }
        }


//        $this->ryanairToCheap('BUD');

        return true;
    }

    public function ryanairToCheap($from, $when = false, $intervall = false)
    {
        $settings = array(
            'departureAirportIataCode' => $from,
            'language' => 'hu',
            'limit' => '999',
            'market' => 'hu-hu',
            'offset' => '0',
            'outboundDepartureDateFrom' => date('Y-m-d'),
            'outboundDepartureDateTo' => date('Y-m-d', strtotime("+10 month")),
            'priceValueTo' => '999999',
        );
        if($from != 'BUD'){
//            change the price, because it's converted to eur
            $settings['priceValueTo'] = '20';
        }
        if($when){
            $settings['outboundDepartureDateFrom'] = date($when);
            $settings['outboundDepartureDateTo'] = date($when);
            if($intervall){
                $settings['outboundDepartureDateFrom'] = date("Y-m-d", strtotime("-". $intervall ." day", $when));;
                $settings['outboundDepartureDateTo'] = date("Y-m-d", strtotime("+". $intervall ." day", $when));;
            }
        }
        $this->setSettings($settings);



        $this->result = json_decode(file_get_contents('https://api.ryanair.com/farefinder/3/oneWayFares' . $this->serialize()));

        foreach ($this->result->fares as $fare) {


            $flight = new Flight;
            $flight->id_from = Airport::getAirport($fare->outbound->departureAirport->iataCode);

            $flight->id_to = Airport::getAirport($fare->outbound->arrivalAirport->iataCode);

            $flight->price = $fare->outbound->price->value;
            $flight->date = date("Y-m-d H:i:s", strtotime($fare->outbound->departureDate));
            $flight->length = 0;

            $flight->id_company = Company::getCompany('Ryanair');

            if (!Flight::where('id_from', '=', $flight->id_from)
                ->where('id_to', '=', $flight->id_to)
                ->where('date', '=', $flight->date)
                ->where('id_company', '=', $flight->id_company)
                ->first()
            ) {
                $flight->save();
            }
        }
    }

}