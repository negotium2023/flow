<?php

namespace App\Http\Controllers;

use App\DismissTrial;
use App\Document;
use App\HelperFunction;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Mail\SendDocuments;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $documents = Document::where('client_id', null)->with('user');

        if ($request->has('q')) {
            $documents->where('name', 'LIKE', "%" . $request->input('q') . "%");
        }

        return view('documents.index')->with(['documents' => $documents->get()]);
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(StoreDocumentRequest $request)
    {
        if ($request->has('client')) {
            $office_subscription = new HelperFunction();

            $office = auth()->user()->offices()->latest()->first();

            $date_Diff = $office_subscription->officeSubscription($office->id)["date_difference"];

            $dismiss_trial = DismissTrial::where('office_id', $office->id)->latest()->first()->trial_dismissed ?? 0;
            $trial = (($date_Diff >= 0) && ($date_Diff <= 5) && ($office_subscription->officeSubscription($office->id)["subscription"]["product_package_id"] == 6) && ($dismiss_trial != 1)) ? true : false;

            $nr_allowed_documents = ($trial ? $office_subscription->officeSubscription(3)["subscription"]["nr_documents"] : 1000);

            $nr_documents = Document::where('client_id',$request->input('client'))->get()->count();

            if($trial && $nr_allowed_documents >= $nr_allowed_documents){
                return redirect(route('clients.documents', [$request->input('client'),$request->input('process_id'),$request->input('step_id')]))->with('flash_danger', 'Trial subscription only allows for the upload of '.$nr_allowed_documents.' documents.');
            }
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".".$file->getClientOriginalExtension();
            $stored = $file->storeAs('documents', $name);
        }

        $document = new Document;
        $document->name = $request->input('name');
        $document->file = $name;
        $document->user_id = auth()->id();

        if ($request->has('client')) {
            $document->client_id = $request->input('client');
        }

        $document->save();

        if ($request->has('client')) {
            return redirect(route('clients.documents', [$request->input('client'),$request->input('process_id'),$request->input('step_id'),0]))->with('flash_success', 'Document uploaded successfully');
        }

        return redirect(route('documents.index'))->with('flash_success', 'Document uploaded successfully');
    }

    public function show($id)
    {
        //
    }

    public function edit(Document $document)
    {
        return view('documents.edit')->with(['document'=> $document]);
    }

    public function update(UpdateDocumentRequest $request, Document $document)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = Carbon::now()->format('Y-m-d')."-".strtotime(Carbon::now()).".".$file->getClientOriginalExtension();
            $stored = $file->storeAs('documents', $name);
            $document->file = $name;
        }

        $document->name = $request->input('name');
        $document->user_id = auth()->id();

        if ($request->has('client')) {
            $document->client_id = $request->input('client');
        }

        $document->save();

        if ($request->has('client')) {
            return redirect(route('clients.documents', [$request->input('client'),$request->input('process_id'),$request->input('step_id'),0]))->with('flash_success', 'Document updated successfully');
        }

        return redirect(route('documents.index'))->with('flash_success', 'Document updated successfully');
    }

    public function destroy($id,$client_id,$process_id,$step_id)
    {
        Document::destroy($id);
        //File::delete($request->input('q'));
        if ($client_id != 0) {
            return redirect(route('clients.documents', [$client_id,$process_id,$step_id,0]))->with('flash_success', 'Document deleted successfully');
        }

        return redirect(route('documents.index'))->with('flash_success', 'Document deleted successfully');
    }

    public function send(Request $request)
    {
        $documents = Document::find($request->documentsID);
        $emailContent = [
            'subject' => $request->subject,
            'body' => $request->body
        ];
        $documents = $documents->map(function ($doc){
            if (file_exists(storage_path('app/documents/' . $doc->file))) {
                return storage_path('app/documents/' . $doc->file);
            } else if(file_exists(public_path('storage/documents/processed_applications'.$doc->file))) {
                return public_path('storage/documents/processed_applications'.$doc->file);
            }
        });

        Mail::to($request->emails)->send(new SendDocuments($documents, $emailContent));

        return response()->json(['request'=>$request->all()]);
    }
}
