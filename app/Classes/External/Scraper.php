<?php
/**
 * Project: WizzairScraper
 *
 * @author Amado Martinez <amado@projectivemotion.com>
 */

namespace App\Classes\External;


use App\Classes\Front\Flight;
use projectivemotion\PhpScraperTools\CacheScraper;

class Scraper extends CacheScraper
{
    const default_api_version = '7.8.15';
    protected $protocol =   'https';
    protected $domain   =   'be.wizzair.com';
    protected $api_route   =   'https://wizzair.com/static/metadata.json';
    protected $api_url   =   '';

    protected $return_date      =   '';
    protected $departure_date   =   '';
    protected $api_version;

    /**
     * Age 14+
     * @var integer
     */
    protected $adults = 0;

    /**
     * Age 2-14
     * @var integer
     */
    protected $children = 0;

    /**
     * Age 0-2
     * @var integer
     */
    protected $infants = 0;

    public function init()
    {
        $this->setApiVersion(self::default_api_version);
        parent::init();
    }

    public function setApiVersion($api_version)
    {
        $this->api_version = $api_version;
    }

    public function getApiVersion()
    {
        return $this->api_version;
    }

    public function detect_api_version()
    {
        $apiJson = false;
        try{
            $apiJson = json_decode(file_get_contents($this->api_route));
        }catch (\Exception $e){
         /*TODO create an exception and log them somewhere*/
        }
        if($apiJson && isset($apiJson->apiUrl)){
            $pattern = "#https://{$this->domain}/([0-9\\.]*?)/Api#";
            if(preg_match($pattern, $apiJson->apiUrl, $matches)){
                $this->setApiVersion($matches[1]);
                return true;
            }
            $this->setApiUrl($apiJson->apiUrl);
            /*TODO set this to db fo further use*/

        }
        /*TODO if not json, notify by email*/
        return false;
    }


    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function setAdults($adults)
    {
        $this->adults = $adults;
    }

    public function setInfants($infants)
    {
        $this->infants = $infants;
    }

    public function setDepartureDate($departure_date)
    {
        $this->departure_date = strtotime($departure_date);
    }

    public function setReturnDate($return_date)
    {
        $this->return_date = strtotime($return_date);
    }

    public function getDepartureDate($format    =   'd/m/Y')
    {
        return date($format, $this->departure_date);
    }

    public function getReturnDate($format   =   'd/m/Y')
    {
        return date($format, $this->return_date);
    }

    public function getAdults()
    {
        return $this->adults;
    }

    public function getChildren()
    {
        return $this->children;
    }

    public function getInfants()
    {
        return $this->infants;
    }

    public function makeRequest($origin, $destination)
    {
        $params = ['flightList' => [
            ['departureStation' => $origin,
                'arrivalStation'    => $destination,
                'departureDate' =>  $this->getDepartureDate('Y-m-d')
            ],
            [
                'departureStation'  =>  $destination,
                'arrivalStation'    =>  $origin,
                'departureDate' => $this->getReturnDate('Y-m-d')
            ]],
            'wdc' => "true",
            'adultCount'    => $this->getAdults(),
            'childCount'    =>  $this->getChildren(),
            'infantCount'   =>  $this->getInfants()
        ];
        $array_source    =   $this->cache_get('/' . $this->getApiVersion() . '/Api/search/search', json_encode($params) , true );
        $result =   json_decode($array_source , true);

        if(!$result)
        {
            if($array_source == "")
            {
                throw new \Exception('The server returned a blank result.');
            }
            throw new \Exception($array_source);
        }

        return $result;
    }
    public function makeSearchRequest($settings, $priceRange)
    {
        if($settings === array()){
            $params =
                [
                    'dateRange' => 1,
                    'departureStation' => "BUD",
                    'destinationCategory' => 0,
                    'from' => null,
                    'priceRange' => $priceRange,
                    'to' => null,
                    'travelDuration' => 1,
                ];

        } else {
            $params = $settings;
        }

        $array_source    =   $this->cache_get('/' . $this->getApiVersion() . '/Api/search/inspirationalFlights', json_encode($params) , true );
        $result =   json_decode($array_source , true);

        if(!$result)
        {
            if($array_source == "")
            {
                throw new \Exception('The server returned a blank result.');
            }
            throw new \Exception($array_source);
        }

        return $result;
    }

    public function isRoundTrip()
    {
        return $this->getReturnDate() != '';
    }

    public function getFlights($origin, $destination)
    {
        // initialize cookies.
        $home = $this->cache_get('/');
        $html_source = $this->makeRequest($origin, $destination);

        return $html_source;
    }

    /**
     * var priceRange   from 1 to 6
     *                  1 - cheapest flights
     *                  6 - from cheapest to mst expensive
     */
    public function getTrips($settings = array(), $priceRange = 1)
    {
        // initialize cookies.
        $home = $this->cache_get('/');
        $html_source = $this->makeSearchRequest($settings,$priceRange);

        return $html_source;
    }

    /**
     * @return string
     */
    public function getApiUrl()
    {
        return $this->api_url;
    }

    /**
     * @param string $api_url
     */
    public function setApiUrl($api_url)
    {
        $this->api_url = $api_url;
    }
}
