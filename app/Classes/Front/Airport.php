<?php
/**
 * Created by PhpStorm.
 * User: endre
 * Date: 2018. 02. 13.
 * Time: 21:56
 */

namespace App\Classes\Front;


use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    public static function getAirport($airport){
        return Airport::where('code', $airport)->first()->id;
    }
}