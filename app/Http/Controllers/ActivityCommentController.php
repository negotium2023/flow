<?php

namespace App\Http\Controllers;

use App\ActivityComment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Activity;

class ActivityCommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function addComment(Request $request){

        $comment = new ActivityComment();
        $comment->client_id = $request->input('clientid');
        $comment->activity_id = $request->input('activityid');
        $comment->user_id = Auth::id();
        $comment->comment = $request->input('comment');

            $comment->private = $request->input('privatec');

        $comment->save();


        $response = array(
            'status' => 'success',
            'msg' => 'Setting created successfully',
        );

        return response()->json($response);
    }

    public function showComment(Request $request)
    {
        $comments = ActivityComment::where('client_id',$request->input('clientid'))
            ->where('activity_id',$request->input('activityid'))
            ->orderBy('created_at','desc')
            ->get();

        $data = $comments->map(function ($comment){
                $avatar = User::select('avatar')->where('id',$comment->user_id)->first();
                if ($comment->private == 1 && $comment->user_id == Auth::id()){
                    return [
                        'id' => $comment->id,
                        'activity_id' => $comment->activity_id,
                        'client_id' => $comment->client_id,
                        'comment' => $comment->comment,
                        'user' => $comment->user_id,
                        'date' => Carbon::parse($comment->created_at)->format('Y-m-d H:i:s'),
                        'avatar' => $avatar->avatar,
                        'privatec' => $comment->private
                    ];
                }

                if($comment->private == 0 || $comment->private == null){
                    return [
                        'id' => $comment->id,
                        'activity_id' => $comment->activity_id,
                        'client_id' => $comment->client_id,
                        'comment' => $comment->comment,
                        'user' => $comment->user_id,
                        'date' => Carbon::parse($comment->created_at)->format('Y-m-d H:i:s'),
                        'avatar' => $avatar->avatar,
                        'privatec' => $comment->private
                    ];
                }
            });

        /**
         * variable is not used
         * $activity = Activity::where('id',$request->input('activityid'))->first();
         * $data = array();
         **/

        return response()->json($data);
    }

    public function editComment(Request $request)
    {
        $comments = ActivityComment::where('id',$request->input('commentid'))->get();

        $data = $comments->map(function ($comment){
            return [
                'id' => $comment->id,
                'activity_id' => $comment->activity_id,
                'client_id' => $comment->client_id,
                'comment' => $comment->comment,
                'user' => $comment->user_id,
                'date' => Carbon::parse($comment->created_at)->format('Y-m-d'),
                'privatec' => $comment->private
            ];
        });

        return response()->json($data);
    }

    public function updateComment(Request $request)
    {
        $comment = ActivityComment::find($request->input('commentid'));
        $comment->comment = $request->input('comment');
        $comment->private = $request->input('privatec');
        $comment->save();

        return response()->json('success');
    }

    public function destroyComment(Request $request){
        ActivityComment::destroy($request->input('commentid'));
        return response()->json('success');
    }
}
