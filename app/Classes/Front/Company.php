<?php
/**
 * Created by PhpStorm.
 * User: endre
 * Date: 2018. 02. 06.
 * Time: 21:37
 */

namespace App\Classes\Front;


use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    public static function getCompany($company){
        if(!Company::where('name', $company)->first()){
            $insert = new Company();
            $insert->name = $company;
            $insert->save();
        }
        return Company::where('name', $company)->first()->id;
    }
}