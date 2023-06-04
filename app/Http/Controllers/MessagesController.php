<?php
namespace App\Http\Controllers;
use App\Client;
use App\Config;
use App\RelatedParty;
use App\User;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
class MessagesController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show all of the message threads to the user.
     *
     * @return mixed
     */
    public function index()
    {
        // All threads, ignore deleted/archived participants
        // $threads = Thread::getAllLatest()->get();
        // All threads that user is participating in
         $threads = Thread::forUser(Auth::id())->latest('updated_at')->get();
        // All threads that user is participating in, with new messages
        // $threads = Thread::forUserWithNewMessages(Auth::id())->latest('updated_at')->get();

        $config = Config::first();

        $subject = $config->message_subject;

        if($subject == 0){
            return view('messenger.index-no-subject', compact('threads'));
        } else {
            return view('messenger.index-subject', compact('threads'));
        }
    }
    /**
     * Shows a message thread.
     *
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');
            return redirect()->route('messages');
        }
        // show current user in list if not a current participant
        // $users = User::whereNotIn('id', $thread->participantsUserIds())->get();
        // don't show the current user in list
        $userId = Auth::id();
        $users = User::whereNotIn('id', $thread->participantsUserIds($userId))->get();
        $thread->markAsRead($userId);

        $config = Config::first();

        $subject = $config->message_subject;

        if($subject == 0){
            return view('messenger.show-no-subject', compact('thread', 'users'));
        } else {
            return view('messenger.show', compact('thread', 'users'));
        }
    }
    /**
     * Creates a new message thread.
     *
     * @return mixed
     */
    public function create(Request $request)
    {
        $config = Config::first();

        $subject = $config->message_subject;

        $users = User::where('id', '!=', Auth::id())->get();

        $request->session()->forget('page_url');

        if($subject == 0){
            return view('messenger.create-no-subject', compact('users'));
        } else {
            return view('messenger.create', compact('users'));
        }
    }

    public function client_create(Request $request,$client_id,$process_id,$step_id)
    {


        $config = Config::first();

        $subject = $config->message_subject;

        $users = User::where('id', '!=', Auth::id())->get();

        //Add page url to session variable
        $request->session()->forget('page_url');
        $request->session()->put('page_url',route('clients.overview',[$client_id,$process_id,$step_id]));
        if($subject == 0){
            return response()->json(['subject' => 0]);
            //return view('messenger.create-no-subject', compact('users'));
        } else {
            return response()->json(['subject' => 1]);
            //return view('messenger.create', compact('users'));
        }
    }

    public function relatedparty_create(Request $request,$client_id,$related_party_id)
    {
        $config = Config::first();

        $subject = $config->message_subject;

        $users = User::where('id', '!=', Auth::id())->get();

        $related_party = RelatedParty::find($related_party_id);
        //Add page url to session variable
        $request->session()->forget('page_url');
        $request->session()->put('page_url',route('relatedparty.show',['client_id' => $related_party->client_id,'process_id' => $related_party->process_id,'step_id' => $related_party->step_id,'related_party_id'=>$related_party->id]));
        if($subject == 0){
            return view('messenger.create-no-subject', compact('users'));
        } else {
            return view('messenger.create', compact('users'));
        }
    }
    /**
     * Stores a new message thread.
     *
     * @return mixed
     */
    public function store(Request $request)
    {

        $request->session()->forget('email_template');

        $input = Input::all();
        if(isset($input['subject'])) {
            $thread = Thread::create([
                'subject' => $input['subject'],
            ]);
        } else {
            $thread = Thread::create([
                'subject' => '',
            ]);
        }
        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'body' => $input['message'],
        ]);
        // Sender
        Participant::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'last_read' => new Carbon,
        ]);
        // Recipients
        if (Input::has('recipients')) {
            $thread->addParticipant($input['recipients']);
        }

        //clear page from session
        return redirect()->route('messages');
    }
    /**
     * Adds a new message to a current thread.
     *
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        try {
            $thread = Thread::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            Session::flash('error_message', 'The thread with ID: ' . $id . ' was not found.');
            return redirect()->route('messages');
        }
        $thread->activateAllParticipants();
        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'body' => Input::get('message'),
        ]);
        // Add replier as a participant
        $participant = Participant::firstOrCreate([
            'thread_id' => $thread->id,
            'user_id' => Auth::id(),
        ]);
        $participant->last_read = new Carbon;
        $participant->save();
        // Recipients
        if (Input::has('recipients')) {
            $thread->addParticipant(Input::get('recipients'));
        }
        return redirect()->route('messages.show', $id);
    }

    public function getMessageCount(){
        $threads = Thread::getAllLatest()->get();

        $thread = 0;

        foreach ($threads as $result){
            $thread = $thread+$result->isUnread(Auth::id());
        }

        $data["count"] = $thread;

        return $data;
    }

    public function getMessages(){
        $query = DB::select(DB::raw("SELECT b.*,c.thread_id as tid,c.body AS body,c.created_at AS cdate FROM participants b INNER JOIN messages c ON b.thread_id = c.thread_id WHERE b.user_id = ".Auth::id()." AND c.id IN (SELECT MAX(id) FROM messages GROUP BY thread_id) ORDER BY b.created_at DESC limit 5"));

        //dd($query);

        $data = [];
        foreach ($query as $message) {
            $avatar = User::where('id',$message->user_id)->first();
            array_push($data, [
                'image' => storage_path('app/avatars/' . $avatar->avatar),
                'sender' => $avatar->first_name.' '.$avatar->last_name,
                'id' => $message->tid,
                'link' => route('messages.show',$message->tid),
                'body' => substr($message->body, 0, 30),
                'created' => Carbon::parse($message->cdate)->format('M d'),
                'type' => 'system'
            ]);
        }

        $query2 = DB::select(DB::raw("SELECT * FROM whatsapp_messages a WHERE ".Auth::id()." IN (SELECT DISTINCT(user_id) from whatsapp_messages) and id IN (SELECT MAX(id) FROM whatsapp_messages GROUP BY client_id)"));

        foreach ($query2 as $message) {
            $avatar = ($message->user_id != '' && $message->user_id != '0' ? User::where('id',$message->user_id)->first() : Client::where('id',$message->client_id)->first());
            array_push($data, [
                'image' => storage_path('app/avatars/' . $avatar->avatar),
                'sender' => $avatar->first_name.' '.$avatar->last_name,
                'id' => $message->id,
                'link' => route('messages.show',$message->id),
                'body' => substr($message->message, 0, 30),
                'created' => Carbon::parse($message->created_at)->format('M d'),
                'type' => 'whatsapp'
            ]);
        }

        return $data;
    }
}