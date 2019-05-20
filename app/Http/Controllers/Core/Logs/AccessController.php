<?php

namespace App\Http\Controllers\Core\Logs;

use Illuminate\Http\Request;
use App\Models\AuthenticationLogs;
use App\Http\Controllers\Controller;

class AccessController extends Controller
{
    public function user_access(Request $request){
        $user_id = 0;
        if(isset($_GET['user']) && $_GET['user'] != 0){
            $user_id = $_GET['user'];
        }

        $fecha = '';
        if(isset($_GET['date']) && $_GET['date'] != ''){
            $fecha = $_GET['date'];
        }

        $lista = AuthenticationLogs::with('user:id,name')->where(function($query) use($user_id, $fecha){
            if($user_id!=0){
                $query->where('authenticatable_id', $user_id);
            }
            if($fecha!=''){
                $query->whereRaw("DATE(login_at) = '{$fecha}'");
            }
        })->orderBy('login_at', 'DESC')->paginate(10);
        return $lista;
    }
}
