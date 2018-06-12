<?php

namespace App\Http\Controllers\Front;

use App\Classes\Front\Ryanair;
use App\Classes\Front\Wizzair;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AjaxTicketsController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function ajaxFlightGeneration(Request $request)
    {
        DB::table('flights')->truncate();
        $wizz = new Wizzair();
        $ryanair = new Ryanair();

        if($wizz->generateCheap() && $ryanair->generateCheap()){
            echo 'done';
        }else{
            echo 'error';
        }

    }


    public function ajaxFareFinder(Request $request){
        $fromID = $request->input('departure_code');
        $toID = $request->input('destination_code');

        if($fromID === null){
            return json_encode(array(
                'app_message' => 'Fill the departure, please!',
                'app_message_type' => 'danger'
            ));
        }elseif ($toID === null){
//            select cheap tickets from this airport like from budapest, but not from budapest

            DB::table('flights')->truncate();
            $ryanair = new Ryanair();
            $wizz = new Wizzair();

            $ryanair->setSettings(array(
                'departureAirportIataCode' => $fromID,
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

            $wizz->setSettings(                [
                'dateRange' => 1,
                'departureStation' => $fromID,
                'destinationCategory' => 0,
                'from' => null,
                'priceRange' => 6,
                'to' => null,
                'travelDuration' => 1,
            ]);

            if( $wizz->generateCheap() && $ryanair->generateCheap()) {

                return json_encode(array(
                    'app_message' => 'Flights synced!',
                    'app_message_type' => 'success',
                    'result' => HomeController::displayFlightsTable(true),
                ));
            } else {
                return json_encode(array(
                    'app_message' => 'Sync failed!',
                    'app_message_type' => 'danger',
                ));
            }
        }else{
//           TODO: select tickets from ariport 1 to airport 2
        }
    }

}
