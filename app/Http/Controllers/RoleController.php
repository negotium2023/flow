<?php

namespace App\Http\Controllers;

use App\Permission;
use App\PermissionRole;
use App\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $roles = Role::orderBy('display_name')->with('permissions');

        if ($request->has('q') && $request->input('q') != '') {
            $roles->where('display_name', 'like', "%" . $request->input('q') . "%");
        }

        return view('roles.index')->with(['roles' => $roles->get(), 'permissions' => Permission::all()]);
    }

    public function create()
    {
        return view('roles.create')->with(['permissions' => Permission::all()]);
    }

    public function store(Request $request)
    {
        $role = new Role;
        $role->name = $request->input('name');
        $role->display_name = $request->input('name');
        $role->description = 'User created role';
        $role->save();

        foreach ($request->input('permission') as $permission_input) {
            $permission_role = new PermissionRole;
            $permission_role->permission_id = $permission_input;
            $permission_role->role_id = $role->id;
            $permission_role->save();
        }

        return redirect(route('roles.index'))->with('flash_success', 'Role created successfully.');
    }

    public function update(Request $request)
    {
        PermissionRole::truncate();

        foreach ($request->input('permission') as $role_key => $role_input){
            foreach ($role_input as $permission_input){
                $permission_role = new PermissionRole;
                $permission_role->permission_id = $permission_input;
                $permission_role->role_id = $role_key;
                $permission_role->save();
            }
        }

        return redirect(route('roles.index'))->with('flash_success', 'Roles updated successfully.');
    }

    public function destroy(Role $role)
    {
        PermissionRole::where('role_id',$role->id)->delete();

        $role->delete();

        return redirect(route('roles.index'))->with('flash_success', 'Role removed successfully.');
    }
}
