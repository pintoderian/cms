<?php

namespace App\Http\Controllers\Core\Logs;

use Illuminate\Http\Request;
use App\Models\LogUserActivity;
use App\Http\Controllers\Controller;

class UserActivityController extends Controller
{
    public function user_activity(Request $request){
        $user_id = 0;
        if($request->get("usuario") != 0){ $user_id = $request->get("usuario"); }
        $fecha = '';
        if($request->get("fecha") != '' && $request->get("fecha") != 'undefined'){ $fecha = $request->get("fecha"); }
        $type_query = ''; 
        if($request->get("type_query") != '' && $request->get("type_query") != 'undefined'){                $type_query = $request->get("type_query"); 
        }
        $table_name = '';
        if($request->get("table_name") != '' && $request->get("table_name") != 'undefined'){                $table_name = $request->get("table_name"); 
        }
        $lista = LogUserActivity::with('user:id,name')->where(function($query) use($user_id, $fecha, $type_query, $table_name){
            if($user_id!=0){
                $query->where('user_id', $user_id);
            }
            if($fecha!=''){
                $query->whereRaw("DATE(current_date_b) = '{$fecha}'");
            }
            if($type_query!=""){
                $query->where('type_query', $type_query);
            }
            if($table_name!=""){
                $query->where('table_name', $table_name);
            }
        })->orderBy('id', 'DESC')->paginate(10);
        return $lista;
    }

    public function select_tables(){
        $tempTables = [];
        $tables = LogUserActivity::select('table_name')->groupBy('table_name')->get();
        foreach ($tables as $key => $item) {
            $tempTables[$key]["value"] = $item->table_name;
            $tempTables[$key]["id"] = $item->table_name;
            $tempTables[$key]["text"] = $item->table_name;
        }
        return $tempTables;
    }
}
