<?php

namespace App\Http\Controllers\Front;

use App\Classes\Front\Ryanair;
use App\Classes\Front\Wizzair;
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

}
