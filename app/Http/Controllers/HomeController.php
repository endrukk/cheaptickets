<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {

        $data['flightsTableData'] = $this->displayFlightsTable(true);
        $data['flightsTableHeaders'] = false;
        if(isset($data['flightsTableData'][0])){
            $row = get_object_vars($data['flightsTableData'][0]);
            foreach ($row as $key => $value){
                $data['flightsTableHeaders'][] = $key;
            }
        }
//        die(var_dump($data['flightsTableData']));
        return view('dashboard',$data);
    }

    /**
     * Show the application home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = array(
            'flightsTableData' => $this->displayFlightsTable(true),
        );

        return view('home',$data);
    }

    private function displayFlightsTable($today = false)
    {

        $sql = "select 
                ct_from.name as 'from_country',
                c_from.name as  'from_city',
                a_from.name as  'from_airport',
                
                ct_to.name as 'to_country',
                c_to.name as  'to_city',
                a_to.name as  'to_airport',
                
                f.`date`  as  'date',
                f.`length` as  'length',
                f.price as  'price',
                cm.`name` as  'company'
                
                from flights f
                inner join airports a_from on f.id_from = a_from.id
                inner join airports a_to on f.id_to = a_to.id
                inner join cities c_from on a_from.id_city = c_from.id
                inner join cities c_to on a_to.id_city = c_to.id
                inner join countries ct_from on c_from.id_country = ct_from.id
                inner join countries ct_to on c_to.id_country = ct_to.id
                inner join companies cm on f.id_company = cm.id ";
        if($today){
            $sql .= " where f.created_at >= '" . date('Y-m-d', time()) . "' ";
        }
        $sql .= " order by f.price asc;";

        return DB::select($sql);
    }
}
