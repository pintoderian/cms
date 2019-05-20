<?php

namespace App\Http\Controllers\Core;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UsersController extends Controller
{
    public function __construct(){
        $this->middleware('permission:view_users')->only('index');
        $this->middleware('permission:add_users')->only(['store']);
        $this->middleware('permission:edit_users')->only(['update']);
        $this->middleware('permission:delete_users')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $texto =  isset($request->texto) ? ucwords(strtolower($request->texto)) : null;
        $users = User::with("roles", "permissions")->where(function($query) use($request){
            $query->where('id', '!=', 1);
            $texto =  isset($request->texto) ? $request->texto : null;
            if($texto!=null){
                $query->where('name', 'like', '%' . $texto . '%');
            }
            if(isset($request->rol) && $request->rol != null){
                $query->whereHas('roles',function($q) use($request){
                    $q->where('role_id', $request->rol);
                });
            }
        })->orderBy('id', 'ASC')->paginate(10);
        return $users;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|max:255|unique:users',
            'roles' => 'required',
            'username' => 'required|unique:users',
            'description' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);
        $user = User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'description' => $request->description,
        ]);
        $user->assignRole($request->roles);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'username' => 'required|unique:users,username,'.$id,
            'password' => 'sometimes|nullable|min:6|confirmed',
        ]);
        $user = User::findOrFail($id);
        $user->update([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->email,
            'description' => $request->description
        ]);
        if(isset($request->password)):
            $user->password =  bcrypt($request->password);
            $user->save();
        endif;
        $user->syncRoles($request->roles);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $ban = $user->ban();
        $ban->isPermanent();
    }
    /**
     * Función para crear usuario administrador desde el seeder
     */
    public function create_admin(){
        $user = User::firstOrCreate([
            'username' => 'admin',
        ],[
            'name' => 'Administrator',
            'email'=> 'root@codegea.com',
            'password' => bcrypt('admin'),
            'description' => 'Root del sistema' 
        ]);
        $user->assignRole("admin");
        return $user;
    }
    /**
     * Función para banear o desbanear usuarios
     */
    public function ban(Request $request){
        $request->validate([
            "type" => "required",
            "id" => "required"
        ]);
        $user = User::findOrFail($request->id);
        if($request->type == "ban"){
            $user->ban([
                'expired_at' => $request->date,
                'comment' => $request->comment,
            ]);
        }else{
            $user->unban();
        }
    }
    /**
    * Usuarios select
    */
    public function select_users(){
        $users = User::select('id','id as value','name as text')->get();
        return $users;
    }
}
