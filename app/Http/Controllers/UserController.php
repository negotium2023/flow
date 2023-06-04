<?php

namespace App\Http\Controllers;

use App\Area;
use App\AreaUser;
use App\Config;
use App\Division;
use App\DivisionUser;
use App\Http\Requests\PasswordRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Office;
use App\OfficeUser;
use App\Region;
use App\RegionUser;
use App\Role;
use App\RoleUser;
use App\User;
use App\UserNotification;
use App\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['update','getNotifications','getUsers', 'deActivate', 'reActivated','getNotificationsCount','getqausers']);
        $this->middleware('auth:api')->only(['getNotifications','getUsers', 'deActivate', 'reActivated']);
    }

    public function index(Request $request)
    {
        if(Auth::user()->is('mmanager')) {
            $users = User::with('divisions', 'regions', 'areas', 'offices', 'roles')->withTrashed();
        } else {
            $offices = OfficeUser::select('office_id')->where('user_id',Auth::id())->get();

            $users = User::with(['divisions', 'regions', 'areas', 'offices', 'roles'])->withTrashed()->whereHas('offices',function ($q) use ($offices){
                $q->whereIn('id',collect($offices)->toArray());
            });
        }

        if ($request->has('q') && $request->input('q') != '') {
            $users->where(function ($query) use ($request) {
                $query->where('first_name', 'like', "%" . $request->input('q') . "%")
                    ->orWhere('last_name', 'like', "%" . $request->input('q') . "%")
                    ->orWhere('email', 'like', "%" . $request->input('q') . "%");
            });
        }

        if ($request->has('f') && $request->input('f') != '') {
            $users->where(function ($query) use ($request) {
                $query->orWhereDate('last_login_at', '>=', $request->input('f'))
                    ->orWhereDate('created_at', '>=', $request->input('f'));
            });
        }

        if ($request->has('t') && $request->input('t') != '') {
            $users->where(function ($query) use ($request) {
                $query->orWhereDate('last_login_at', '<=', $request->input('t'))
                    ->orWhereDate('created_at', '<=', $request->input('t'));
            });
        }

        $parameters = [
            'users' => $users->get()
        ];

        return view('users.index')->with($parameters);
    }

    public function create()
    {
        $parameters = [
            'roles' => (Auth::user()->is('manager') ? Role::orderBy('name')->get()->pluck('display_name', 'id') : Role::orderBy('name')->where('name','!=','manager')->get()->pluck('display_name', 'id')),
            'divisions' => (Auth::user()->is('manager') ? Division::orderBy('name')->get()->pluck('name', 'id') : Division::orderBy('name')->whereIn('id',collect(DivisionUser::select('division_id')->where('user_id',Auth::id())->get())->toArray())->get()->pluck('name', 'id')),
            'regions' => (Auth::user()->is('manager') ? Region::orderBy('name')->get()->pluck('name', 'id') : Region::orderBy('name')->whereIn('id',collect(RegionUser::select('region_id')->where('user_id',Auth::id())->get())->toArray())->get()->pluck('name', 'id')),
            'areas' => (Auth::user()->is('manager') ? Area::orderBy('name')->get()->pluck('name', 'id') : Area::orderBy('name')->whereIn('id',collect(AreaUser::select('area_id')->where('user_id',Auth::id())->get())->toArray())->get()->pluck('name','id')),
            'offices' => (Auth::user()->is('manager') ? Office::orderBy('name')->get()->pluck('name', 'id') : Office::orderBy('name')->whereIn('id',collect(OfficeUser::select('office_id')->where('user_id',Auth::id())->get())->toArray())->get()->pluck('name', 'id')),
        ];

        return view('users.create')->with($parameters);
    }

    public function store(StoreUserRequest $request)
    {
        $password = str_random(8);

        $user = new User;
        if ($user->countUsersPerOffice() >= \auth()->user()->sub_users){
            return redirect()->back()->with('flash_danger', 'You have reached the maximum amount of users allowed in your package');
        }
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->verified = '1';
        $user->password = bcrypt($password);

//        if ($request->hasFile('avatar')) {
//
//            $request->file('avatar')->store('avatars');
//            $image = Image::make('../storage/app/avatars/' . $request->file('avatar')->hashName());
//            $image->resize(200, 200);
//            $image->save();
//
//            $user->avatar = $request->file('avatar')->hashName();
//        }

        $user->save();

        User::where('id',$user->id)->update([
            'hash_first_name' => DB::raw("AES_ENCRYPT('".$request->input('first_name')."','Qwfe345dgfdg')"),
            'hash_last_name' => DB::raw("AES_ENCRYPT('".$request->input('last_name')."','Qwfe345dgfdg')")
        ]);

        if($request->has('role')) {
            if ($request->input('role')) {
                $role_array = [];
                foreach ($request->input('role') as $role_input) {
                    array_push($role_array, [
                        'user_id' => $user->id,
                        'role_id' => $role_input
                    ]);
                }
                RoleUser::insert($role_array);
            }
        } else {
            RoleUser::insert(['user_id'=>$user->id,'role_id'=>14]);
        }

        if ($request->input('division')) {
            $division_array = [];
            foreach ($request->input('division') as $division_input) {
                array_push($division_array, [
                    'user_id' => $user->id,
                    'division_id' => $division_input
                ]);
            }
            DivisionUser::insert($division_array);
        }

        if ($request->input('region')) {
            $region_array = [];
            foreach ($request->input('region') as $region_input) {
                array_push($region_array, [
                    'user_id' => $user->id,
                    'region_id' => $region_input
                ]);
            }
            RegionUser::insert($region_array);
        }

        if ($request->input('area')) {
            $area_array = [];
            foreach ($request->input('area') as $area_input) {
                array_push($area_array, [
                    'user_id' => $user->id,
                    'area_id' => $area_input
                ]);
            }
            AreaUser::insert($area_array);
        }

        if ($request->input('office')) {
            $office_array = [];
            foreach ($request->input('office') as $office_input) {
                array_push($office_array, [
                    'user_id' => $user->id,
                    'office_id' => $office_input
                ]);
            }
            OfficeUser::insert($office_array);
        }

        //Send notification email to user
        Mail::to($user->email)->send(new WelcomeMail($user, $password));

        if (!$request->wizard){
            return redirect(route('users.index'))->with('flash_success', 'User captured successfully')->with('flash_info', 'Password is: ' . $password);
        }else{
            return response()->json(['user_count'=>$user->countUsersPerOffice()]);
        }

    }

    public function edit(User $user)
    {
        $user->load('divisions', 'regions', 'areas', 'offices', 'roles');

        $parameters = [
            'user' => $user,
            'user_roles' => $user->roles->pluck('id'),
            'user_divisions' => $user->divisions->pluck('id'),
            'user_regions' => $user->regions->pluck('id'),
            'user_areas' => $user->areas->pluck('id'),
            'user_offices' => $user->offices->pluck('id'),
            'roles' => (Auth::user()->is('manager') ? Role::orderBy('name')->get()->pluck('display_name', 'id') : Role::orderBy('name')->where('name','!=','manager')->get()->pluck('display_name', 'id')),
            'divisions' => (Auth::user()->is('manager') ? Division::orderBy('name')->get()->pluck('name', 'id') : Division::orderBy('name')->whereIn('id',collect(DivisionUser::select('division_id')->where('user_id',Auth::id())->get())->toArray())->get()->pluck('name', 'id')),
            'regions' => (Auth::user()->is('manager') ? Region::orderBy('name')->get()->pluck('name', 'id') : Region::orderBy('name')->whereIn('id',collect(RegionUser::select('region_id')->where('user_id',Auth::id())->get())->toArray())->get()->pluck('name', 'id')),
            'areas' => (Auth::user()->is('manager') ? Area::orderBy('name')->get()->pluck('name', 'id') : Area::orderBy('name')->whereIn('id',collect(AreaUser::select('area_id')->where('user_id',Auth::id())->get())->toArray())->get()->pluck('name','id')),
            'offices' => (Auth::user()->is('manager') ? Office::orderBy('name')->get()->pluck('name', 'id') : Office::orderBy('name')->whereIn('id',collect(OfficeUser::select('office_id')->where('user_id',Auth::id())->get())->toArray())->get()->pluck('name', 'id')),
        ];

        return view('users.edit')->with($parameters);
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');

//        if ($request->hasFile('avatar')) {
//
//            $request->file('avatar')->store('avatars');
//            $image = Image::make('../storage/app/avatars/' . $request->file('avatar')->hashName());
//            $image->resize(200, 200);
//            $image->save();
//
//            $user->avatar = $request->file('avatar')->hashName();
//        }

        $user->save();

        User::where('id',$user->id)->update([
            'hash_first_name' => DB::raw("AES_ENCRYPT('".$request->input('first_name')."','Qwfe345dgfdg')"),
            'hash_last_name' => DB::raw("AES_ENCRYPT('".$request->input('last_name')."','Qwfe345dgfdg')")
        ]);

        RoleUser::where('user_id', $user->id)->delete();
        if ($request->input('role')) {
            $role_array = [];
            foreach ($request->input('role') as $role_input) {
                array_push($role_array, [
                    'user_id' => $user->id,
                    'role_id' => $role_input
                ]);
            }
            RoleUser::insert($role_array);
        }

        DivisionUser::where('user_id', $user->id)->delete();
        if ($request->input('division')) {
            $division_array = [];
            foreach ($request->input('division') as $division_input) {
                array_push($division_array, [
                    'user_id' => $user->id,
                    'division_id' => $division_input
                ]);
            }
            DB::table('division_user')->insert($division_array);
        }

        RegionUser::where('user_id', $user->id)->delete();
        if ($request->input('region')) {
            $region_array = [];
            foreach ($request->input('region') as $region_input) {
                array_push($region_array, [
                    'user_id' => $user->id,
                    'region_id' => $region_input
                ]);
            }
            DB::table('region_user')->insert($region_array);
        }

        AreaUser::where('user_id', $user->id)->delete();
        if ($request->input('area')) {
            $area_array = [];
            foreach ($request->input('area') as $area_input) {
                array_push($area_array, [
                    'user_id' => $user->id,
                    'area_id' => $area_input
                ]);
            }
            DB::table('area_user')->insert($area_array);
        }

        OfficeUser::where('user_id', $user->id)->delete();
        if ($request->input('office')) {
            $office_array = [];
            foreach ($request->input('office') as $office_input) {
                array_push($office_array, [
                    'user_id' => $user->id,
                    'office_id' => $office_input
                ]);
            }
            DB::table('office_user')->insert($office_array);
        }

        return redirect(route('users.index'))->with('flash_success', 'User updated successfully');
    }

    public function profile(User $user)
    {

        if (!isset($user->id)) {
            $user = auth()->user();
        }

        $roles = $user->roles()->get()->map(function ($role){
            return $role->display_name;
        });

        return view('auth.profile')->with(['user' => $user, 'roles' => $roles]);
    }

    public function settings()
    {
        return view('auth.settings');
    }

    public function handlePassword(PasswordRequest $request)
    {
        //https://www.5balloons.info/setting-up-change-password-with-laravel-authentication/
        if (!(Hash::check($request->get('old_password'), auth()->user()->password))) {
            return redirect()->back()->with("flash_danger", "Incorrect password.");
        }

        $user = auth()->user();
        $user->password = bcrypt($request->input('password'));
        $user->save();

        return redirect()->back()->with('flash_success', 'Password updated successfully.');
    }

    public function handleProfile(ProfileRequest $request)
    {
        $path = DB::select(DB::raw("select absolute_path from configs"));
        $user = auth()->user();
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');

        if ($request->hasFile('avatar')) {
            $targetPath = $path;
            $request->file('avatar')->store('avatars');
            $image = Image::make($path[0]->absolute_path.'/storage/app/avatars/' . $request->file('avatar')->hashName());
            if ($image->height() == $image->width()) {
                $image->resize(200, 200);
            } else {
                $image->crop(200, 200);
            }
            $image->save();

            $user->avatar = $request->file('avatar')->hashName();
        }

        $user->save();

        return redirect()->back();
    }

    public function handleNotifications(Request $request){
        $user = auth()->user();
        if($request->has('notification_emails')) {
            $user->notification_emails = '1';
        } else {
            $user->notification_emails = '0';
        }
        if($request->has('message_emails')) {
            $user->message_emails = '1';
        } else {
            $user->message_emails = '0';
        }
        $user->save();

        return redirect()->back();
    }

    public function getNotifications(Request $request)
    {
        $notifications = $request->user()->notifications->where('type','!=','2')->orderBy('type','desc')->take(5);

        $data = [];
        foreach ($notifications as $notification) {
            array_push($data, [
                'id' => $notification->id,
                'name' => $notification->name,
                'link' => $notification->link,
                'created' => $notification->created_at->diffForHumans(),
                'type' => $notification->type
            ]);
        }

        usort($data, function ($item1, $item2) {
            return $item2['type'] <=> $item1['type'];
        });

        return $data;
    }

    public function getNotificationsCount(Request $request)
    {
        $query = DB::select(DB::raw("SELECT a.*,b.seen_at FROM `notifications` a join `user_notifications` b on b.notification_id = a.id  WHERE a.type != '2' and b.user_id = ".Auth()->id()." and b.notify = '1' and b.seen_at is null"));
        $count = count($query);

        $data['count'] = $count;
        $data['notifications'] = array();

        $notifications = DB::select(DB::raw("SELECT a.*,b.seen_at FROM `notifications` a join `user_notifications` b on b.notification_id = a.id  WHERE a.type != '2' and b.user_id = ".Auth()->id()." and b.seen_at is null order by created_at DESC limit 5"));
        foreach ($notifications as $notification) {
            array_push($data['notifications'], [
                'id' => $notification->id,
                'name' => $notification->name,
                'link' => $notification->link,
                'type' => $notification->type,
                'created' => Carbon::parse($notification->created_at)->diffForHumans()
            ]);
        };

        usort($data['notifications'], function ($item1, $item2) {
            return $item2['type'] <=> $item1['type'];
        });

        return $data;
    }

    public function markAllNotifications()
    {
        UserNotification::whereHas('notification',function($q){
            $q->where('type','!=','2');
        })->where('user_id',Auth()->id())->update(['notify' => 0]);

        return response()->json(['success' => 'success']);
    }

    public function readNotifications(Request $request)
    {
        UserNotification::where('notification_id',$request->id)->where('user_id',Auth()->id())->update(['seen_at' => now()]);

        return response()->json(['success' => 'success']);
    }

    public function readNotificationsHistory(Request $request)
    {
        $notification = UserNotification::find($request->id);
        $notification->seen_at = now();
        $notification->save();

        return response()->json(['success' => 'success']);
    }

    public function getUsers()
    {
        return User::select('id',DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->pluck('full_name','id');
    }

    public function deActivate(User $user)
    {
        if($user->id == auth()->id()){
            return redirect(route('users.index'))->with('flash_info', 'Not allowed to de-activate you own account');
        }

        $user->deleted_at = Carbon::now();
        $user->save();

        return redirect(route('users.index'))->with('flash_success', 'User de-activated successfully');
    }

    public function activateuser($user)
    {
        User::withTrashed()->where('id','=',$user)->restore();

        return redirect(route('users.index'))->with('flash_success', 'User activated successfully');
    }

    public function verifyPassword($password){

        $data = array();

        $user = User::where('id',Auth::id())->first();

        if(password_verify($password, $user->password)){
            $data["result"] = 1;
        } else {
            $data["result"] = 0;
        }

        return response()->json($data);
    }

    public function getqausers(Request $request,$client){

        $client = Client::where('id',$client)->first();

        if($client->consultant_id != null) {
            $qauser = User::whereHas('roles', function ($q) {
                $q->where('name', 'qa');
            })->orderBy('first_name')->get();

            $data = array();

            foreach ($qauser as $user) {
                $data[$user->id] = User::select('id', DB::raw("CONCAT(CAST(AES_DECRYPT(`hash_first_name`, 'Qwfe345dgfdg') AS CHAR(50)),' ',CAST(AES_DECRYPT(`hash_last_name`, 'Qwfe345dgfdg') AS CHAR(50))) as full_name"))->where('id', $user->id)->first()->full_name;
            }

            return response()->json((isset($data) ? $data : array()));
        } else {
            return response()->json(['message'=>'not assigned']);
        }
    }
    
}
