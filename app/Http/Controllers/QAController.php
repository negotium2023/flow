<?php

namespace App\Http\Controllers;

use App\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QAController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getQACount(Request $request){
        $query = DB::select(DB::raw("SELECT a.*,b.seen_at FROM `notifications` a join `user_notifications` b on b.notification_id = a.id  WHERE a.type = '2' and b.user_id = ".Auth()->id()." and b.notify = '1' and b.seen_at is null"));
        $count = count($query);

        $data['count'] = $count;
        $data['notifications'] = array();

        $notifications = DB::select(DB::raw("SELECT a.*,b.seen_at FROM `notifications` a join `user_notifications` b on b.notification_id = a.id  WHERE a.type = '2' and b.user_id = ".Auth()->id()." and b.seen_at is null order by created_at DESC limit 5"));
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

    public function getQAs(Request $request){
        $notifications = $request->user()->notifications->where('type','2')->take(5);

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

    public function markAllQANotifications()
    {
        UserNotification::whereHas('notification',function($q){
            $q->where('type','2');
        })->where('user_id',Auth()->id())->update(['notify' => 0]);

        return response()->json(['success' => 'success']);
    }
}
