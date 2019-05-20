<?php

namespace App\Providers;

use Carbon\Carbon;
use App\Models\LogUserActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class LogsUserActivityProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if(Auth::check()){
            if(Auth::User()->can("access_panel") && Schema::hasTable('logs_user_activity')){
                DB::listen(
                function ($sql) {
                    // $ sql es un objeto con las propiedades:
                    // sql: la consulta
                    // enlaces: las variables de consulta sql
                    // hora: el tiempo de ejecución para la consulta
                    // connectionName: el nombre de la conexión
            
                    // Para guardar las consultas ejecutadas en el archivo:
                    // Procesar el sql y los enlaces:
                    if (strpos($sql->sql, 'logs_user_activity') !== false || strpos($sql->sql, 'authentication_log') !== false) {
                        return;
                    }
                    foreach ($sql->bindings as $i => $binding) {
                        if ($binding instanceof \DateTime) {
                            $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');
                        } else {
                            if (is_string($binding)) {
                                $sql->bindings[$i] = "'$binding'";
                            }
                        }
                    }
                    // Se extrae la consulta
                    $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);
                    $query = vsprintf($query, $sql->bindings);
                    $type_query = $this->search_type_query($query);
                    //Se verifica si no es un "select" y se inserta en el log
                    if($type_query != "SELECT"){
                        $table_name = get_table_name($query);
                        LogUserActivity::create([
                            'user_id' => Auth::user()->id, 
                            'ip_address' => get_client_ip(), 
                            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                            'current_date_b' => Carbon::now(),
                            'type_query' => $type_query,
                            'table_name' => $table_name,
                            'sql_query' => $query,
                            'current_url' => url()->current()
                        ]);
                    }
                });
            }
        }
    }

    /**
     * Buscando tipo de consulta
     */
    private function search_type_query($query){
        if(strpos($query, 'insert') !== false){
            return "INSERT";
        }elseif(strpos($query, 'update') !== false){
            return "UPDATE";
        }elseif(strpos($query, 'delete') !== false){
            return "DELETE";
        }else{
            return "SELECT";
        }
    }
}
