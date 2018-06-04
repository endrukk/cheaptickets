<?php

namespace App\Classes\Admin;


use Illuminate\Database\Eloquent\Model;

class UserTicket extends Model
{
    protected $table = 'user_tickets';

    private $id_user;

    public function __construct($id_user)
    {
        $this->id_user = $id_user;
        parent::__construct();
    }

    /**
     * id from ==> airport id
     * id to ==> airport id
    */
    public static function getUserTickets($id_user = false, $id_from = false, $id_to = false){
        $where = array();
        if ($id_user !== false){
            $where['id_user'] = $id_user;
        }
        if ($id_from !== false){
            $where['id_from'] = $id_from;
        }
        if ($id_to !== false){
            $where['id_to'] = $id_to;
        }
        return UserTicket::where($where);
    }
    /**
     * id from ==> airport id
     * id to ==> airport id
    */
    public function getTickets( $id_from = false, $id_to = false){
        $where = array('id_user' => $this->id_user);
        if ($id_from !== false){
            $where['id_from'] = $id_from;
        }
        if ($id_to !== false){
            $where['id_to'] = $id_to;
        }
        return self::where($where)->get();
    }


}