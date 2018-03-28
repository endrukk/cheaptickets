<?php
/**
 * Created by PhpStorm.
 * User: endre
 * Date: 2018. 02. 06.
 * Time: 21:37
 */

namespace App\Classes\Front;


use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public static function getCountry($country){
        if(!Country::where('name', $country)->first()){
            $insert = new Country();
            $insert->name = $country;
            $insert->save();
        }
        return Country::where('name', $country)->first()->id;
    }
}