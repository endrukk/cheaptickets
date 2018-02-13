<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestsController extends Controller
{



    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $airports = DB::table('airports')->get();;
        foreach ($airports as $port){
            $city = DB::table('wsh_co_airports')
                ->select('cityName')
                ->where('code', '=', $port->code)
                ->first();

            $id_city = DB::table('cities')
                ->select('id')
                ->where('name', '=', $city->cityName)
                ->first();

            echo 'UPDATE `airports` SET `id_city`= ' . $id_city->id . ' WHERE  `id`= ' . $port->id . ';<br />';
//var_dump($id_city);
        }
        die('<br />done');



    }
}
