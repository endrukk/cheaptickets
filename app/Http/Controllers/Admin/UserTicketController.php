<?php

namespace App\Http\Controllers\Admin;

use App\Classes\Admin\UserTicket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserTicketController extends Controller
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

    public function dashboard()
    {

        $userTicket = new UserTicket(Auth::user()->id);

        $data['myCheapFlights'] = $userTicket->getTickets();


        return view('layouts/admin/my_cheap_flights', $data);
    }

    public function create(Request $request)
    {
        echo '<pre>';
        $formData = ($request->input('data'));
        die(var_dump($formData));
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
