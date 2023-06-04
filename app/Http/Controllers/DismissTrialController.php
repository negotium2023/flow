<?php

namespace App\Http\Controllers;

use App\ActionableAmountData;
use App\ActionableBooleanData;
use App\ActionableContentData;
use App\ActionableDateData;
use App\ActionableDocumentData;
use App\ActionableDocumentEmailData;
use App\ActionableDropdownData;
use App\ActionableHeadingData;
use App\ActionableImageUploadData;
use App\ActionableIntegerData;
use App\ActionableMultipleAttachmentData;
use App\ActionableNotificationData;
use App\ActionablePercentageData;
use App\ActionableSubheadingData;
use App\ActionableTemplateEmailData;
use App\ActionableTextareaData;
use App\ActionableTextData;
use App\ActionableVideoUploadData;
use App\ActionableVideoYoutubeData;
use App\Client;
use App\DismissTrial;
use App\FormInputAmountData;
use App\FormInputBooleanData;
use App\FormInputDropdownData;
use App\FormInputIntegerData;
use App\FormInputPercentageData;
use App\HelperFunction;
use App\OfficeUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DismissTrialController extends Controller
{
    private $office_subscription;

    public function __construct()
    {
        $this->office_subscription = new HelperFunction();
    }

    public function index()
    {
        $office = auth()->user()->offices()->latest()->first();

        //return $user_offices = OfficeUser::get()->unique('office_id')->values();

        //Is the user a financial advisor?
        $roles = auth()->user()->roles()->get()->map(function ($role){
            return $role->name;
        })->toArray();

        $role = in_array('Financial advisor', $roles)?true:false;
        $date_Diff = $this->office_subscription->officeSubscription($office->id)["date_difference"];

        $dismiss_trial = DismissTrial::where('office_id', $office->id)->latest()->first()->trial_dismissed??0;
        $trial = (($date_Diff >= 0) && ($date_Diff <= 5) && ($this->office_subscription->officeSubscription($office->id)["subscription"]["product_package_id"] == 6) && ($dismiss_trial != 1)) ? true : false;
        return response()->json([
            'role' => $role,
            'trial' => $trial,
            'expiry_date' => $this->office_subscription->officeSubscription($office->id)["subscription"]["expiry_date"],
            'nr_documents' => ($trial ? $this->office_subscription->officeSubscription($office->id)["subscription"]["nr_documents"] : 1000)
        ]);
    }

    public function store(Request $request)
    {
        $office = auth()->user()->offices()->latest()->first();
        $dismiss_trial = new DismissTrial();
        $dismiss_trial->office_id = $office->id;
        $dismiss_trial->trial_dismissed = $request->is_dismissed;
        $dismiss_trial->save();

        return response()->json(['status' => 'success']);

    }

    public function cancelSubscription($user){

        $office = OfficeUser::where('user_id',$user)->first();
//dd($office);
        $clients = Client::where('office_id',$office->office_id)->get();
//dd($clients);
        foreach ($clients as $client){
            //Delete Actionable Data
            ActionableAmountData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableBooleanData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableDropdownData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableContentData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableDateData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableDocumentData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableDocumentEmailData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableHeadingData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableImageUploadData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableIntegerData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableMultipleAttachmentData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableNotificationData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionablePercentageData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableSubheadingData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableTemplateEmailData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableTextareaData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableTextData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableVideoUploadData::where('client_id',$client->id)->where('user_id',$user)->delete();
            ActionableVideoYoutubeData::where('client_id',$client->id)->where('user_id',$user)->delete();

            //Delete Crm Data
            FormInputAmountData::where('client_id',$client->id)->where('user_id',$user)->delete();
            FormInputBooleanData::where('client_id',$client->id)->where('user_id',$user)->delete();
            FormInputIntegerData::where('client_id',$client->id)->where('user_id',$user)->delete();
            FormInputPercentageData::where('client_id',$client->id)->where('user_id',$user)->delete();
            FormInputDropdownData::where('client_id',$client->id)->where('user_id',$user)->delete();


            $client->forceDelete();
        }

    }
}
