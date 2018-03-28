<?php
/**
 * Created by PhpStorm.
 * User: endre
 * Date: 2018. 02. 19.
 * Time: 20:31
 */

namespace App\Classes\Front;


use App\Classes\External\Scraper;
use Illuminate\Http\Request;

class Wizzair
{


    public function _construct()
    {
        $this->middleware('auth');
    }

    public function generateCheap(){
        $departure_date = date('Y-m-d');
        $return_date = date('Y-m-d', strtotime("+10 month"));

        $wizzair = new Scraper();
        $wizzair->cacheOff();
        $wizzair->verboseOff();

        $wizzair->setAdults(1);
        $wizzair->setCookieFileName(tempnam(sys_get_temp_dir(), 'wizzaircookie.'));

        $api_detected = $wizzair->detect_api_version();
//        if($api_detected)
//            echo "Detected api version: {$wizzair->getApiVersion()}\n";

        $wizzair->setDepartureDate($departure_date);
        $wizzair->setReturnDate($return_date);

        try {
            $flights = $wizzair->getTrips(6);

            foreach ($flights['inspirationalFlights'] as $fare){
                $flight = new Flight;
                $flight->id_from = Airport::getAirport($fare['departureStation']);

                $flight->id_to = Airport::getAirport($fare['arrivalStation']);

                $flight->price = $fare['returnPrice'] + $fare['price'];
                $flight->date = date("Y-m-d H:i:s", strtotime($fare['departureDate']));

                /*find difference between go there and come back ==> it's in secconds so convert to days*/
                $flight->length = intval(
                    (strtotime($fare['returnDepartureDate'])
                        - strtotime($fare['departureDate']))
                    / (60 * 60 * 24)
                );

                $flight->id_company = Company::getCompany('Wizzair');

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

            return true;

        }catch(\Exception $e)
        {
            return false;
            echo "An Error ocurred: ", $e->getMessage(), ". You may want to try changing search parameters.";
            echo "\nConnection Info: ";
            var_export($wizzair->getInfo());
            exit(1);
        }

    }
}