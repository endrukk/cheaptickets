<?php
/**
 * Created by PhpStorm.
 * User: endre
 * Date: 2018. 02. 06.
 * Time: 21:37
 */

namespace App\Classes\Front;


use Illuminate\Database\Eloquent\Model;

class City extends Model
{

    public static function getCity($city){
        if(!City::where('name', $city)->first()){
            $insert = new City();
            $insert->name = $city;
            $insert->save();
        }
        return City::where('name', $city)->first()->id;
    }

}