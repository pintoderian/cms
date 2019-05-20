<?php

namespace App\Http\Controllers\Core;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RolesController extends Controller
{
    public function __construct(){
        $this->middleware('permission:view_roles')->only('index');
        $this->middleware('permission:add_roles')->only(['store']);
        $this->middleware('permission:edit_roles')->only(['update']);
        $this->middleware('permission:delete_roles')->only('destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = Role::select('id', 'name')
        ->where(function($query) use($request){
            $query->where('name', '!=', 'admin');
            $texto =  isset($request->texto) ? $request->texto : null;
            if($texto!=null){
                $query->where('name', 'like', '%' . $texto . '%');
            }
            //Cuando tenga la opción modular se habilita
            /*if(isset($request->modulo) && $request->modulo != null){
                $query->whereHas('modules',function($q) use($request){
                    $q->where('module_id', $request->modulo);
                });
            }*/
        });
        return $roles->vuePaginate();
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
            'name' => 'min:3|max:40|required|unique:roles,name',
            //'idmodulo' => 'required'
        ]);
        $rol = new Role;
        $rol->name = $request->name;
        $rol->save();
        //$rol->modules()->attach($request->idmodulo);
        return $rol->id;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $rol = Role::with("permissions")->findOrFail($id);
        $rol_perms = [];
        foreach ($rol->permissions as $key => $perm) {
            $rol_perms[] = $perm->name;
        }
        $permissions = Permission::select('name AS text', 'name AS value')->get();
        return [
            'data' => $rol_perms,
            'permisos' => $permissions
        ];
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
            'name' => 'min:3|max:40|required|unique:roles,name,'.$id,
        ]);
        $rol = Role::findOrFail($id);
        $rol->name = $request->name;
        $rol->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rol = Role::findOrFail($id);
        $rol->delete();
    }
    /**
     * Actualiza los permisos de un Rol
     */
    public function change_permissions(Request $request, $id){
        $rol = Role::findOrFail($id);
        if($rol->name == 'admin') {
            $rol->syncPermissions(Permission::all());
        }else{
            $perms = isset($request->perms["result"]["data"]) ? $request->perms["result"]["data"] : [];
            $rol->syncPermissions($perms);
        }
    }
}
