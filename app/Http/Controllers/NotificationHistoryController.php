<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificationHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request){

        $query = "SELECT a.*,b.id as 'sid',b.seen_at FROM `notifications` a join `user_notifications` b on b.notification_id = a.id where a.type != '2' and b.user_id = ".Auth()->id()."";
        $collection = collect($query);
        //dd($notifications);

        //$notifications = DB::select(DB::raw("SELECT a.*,b.id as 'sid',b.seen_at FROM `notifications` a join `user_notifications` b on b.notification_id = a.id  WHERE b.user_id = ".Auth()->id()." order by a.created_at desc"));
        if ($request->has('p')) {
            $query .= " and a.name like '%".$request->input('p')."%'";
        } else {
            //$notifications = collect($query);
        }

        if ($request->has('f') && $request->input('f') != null) {
            $from = Carbon::parse($request->input('f'));
        } else {
            //$from = Carbon::now()->subWeek();
            $from = Carbon::createFromFormat('Y-m-d', '2010-01-01');
        }

        if ($request->has('t') && $request->input('t') != null) {
            $to = Carbon::parse($request->input('t'));
        } else {
            $to = Carbon::now();
        }

        $query .= " and a.created_at between '".$from."' and '".$to."' order by a.created_at desc";

        $notifications = DB::select(DB::raw($query));

        $parameters = [
            'notifications' => $notifications
        ];

        return view('notifications.index')->with($parameters);
    }
}
