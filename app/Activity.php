<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Activity extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function actionable()
    {
        return $this->morphTo();
    }

    public function related_party()
    {
        return $this->morphTo();
    }

    public function getTypeName()
    {
        //activity type hook
        switch ($this->actionable_type) {
            case 'App\ActionableHeading':
                return 'heading';
                break;
            case 'App\ActionableSubheading':
                return 'subheading';
                break;
            case 'App\ActionableContent':
                return 'content';
                break;
            case 'App\ActionableText':
                return 'text';
                break;
            case 'App\ActionableTextarea':
                return 'textarea';
                break;
            case 'App\ActionablePercentage':
                return 'percentage';
                break;
            case 'App\ActionableInteger':
                return 'integer';
                break;
            case 'App\ActionableAmount':
                return 'amount';
                break;
            case 'App\ActionableVideoUpload':
                return 'videoupload';
                break;
            case 'App\ActionableImageUpload':
                return 'imageupload';
                break;
            case 'App\ActionableVideoYoutube':
                return 'videoyoutube';
                break;
            case 'App\ActionableTemplateEmail':
                return 'template_email';
                break;
            case 'App\ActionableDocumentEmail':
                return 'document_email';
                break;
            case 'App\ActionableDocument':
                return 'document';
                break;
            case 'App\ActionableDropdown':
                return 'dropdown';
                break;
            case 'App\ActionableDate':
                return 'date';
                break;
            case 'App\ActionableBoolean':
                return 'boolean';
                break;
            case 'App\ActionableNotification':
                return 'notification';
                break;
            case 'App\ActionableMultipleAttachment':
                return 'multiple_attachment';
                break;
            default:
                return 'error';
                break;
        }
    }

    public function getRelatedPartyTypeName()
    {
        //activity type hook
        switch ($this->actionable_type) {
            case 'App\RelatedPartyText':
                return 'text';
                break;
            case 'App\RelatedPartyTextarea':
                return 'textarea';
                break;
            case 'App\RelatedPartyTemplateEmail':
                return 'template_email';
                break;
            case 'App\RelatedPartyDocumentEmail':
                return 'document_email';
                break;
            case 'App\RelatedPartyDocument':
                return 'document';
                break;
            case 'App\RelatedPartyDropdown':
                return 'dropdown';
                break;
            case 'App\RelatedPartyDate':
                return 'date';
                break;
            case 'App\RelatedPartyBoolean':
                return 'boolean';
                break;
            case 'App\RelatedPartyNotification':
                return 'notification';
                break;
            case 'App\RelatedPartyMultipleAttachment':
                return 'multiple_attachment';
                break;
            default:
                return 'error';
                break;
        }
    }

    public function getTypeDisplayName()
    {
        //activity type hook
        switch ($this->actionable_type) {
            case 'App\ActionableText':
                return 'Free text';
                break;
            case 'App\ActionableTextarea':
                return 'Free textarea';
                break;
            case 'App\ActionablePercentage':
                return 'Percentage';
                break;
            case 'App\ActionableInteger':
                return 'Integer';
                break;
            case 'App\ActionableAmount':
                return 'Amount';
                break;
            case 'App\ActionableVideo':
                return 'Video';
                break;
            case 'App\ActionableImageUpload':
                return 'Image';
                break;
            case 'App\ActionableTemplateEmail':
                return 'Letter Email';
                break;
            case 'App\ActionableDocumentEmail':
                return 'Document Email';
                break;
            case 'App\ActionableDocument':
                return 'Document';
                break;
            case 'App\ActionableBoolean':
            case 'App\ActionableDropdown':
                return 'Dropdown';
                break;
            case 'App\ActionableDate':
                return 'Date';
                break;
            case 'App\ActionableNotification':
                return 'Notification';
                break;
            case 'App\ActionableMultipleAttachment':
                return 'Multiple Attachment';
                break;
            default:
                return 'error';
                break;
        }
    }

    public function getRelatedPartyTypeDisplayName()
    {
        //activity type hook
        switch ($this->actionable_type) {
            case 'App\RelatedPartyText':
                return 'Free text';
                break;
            case 'App\RelatedPartyTextarea':
                return 'Free textarea';
                break;
            case 'App\RelatedPartyTemplateEmail':
                return 'Letter Email';
                break;
            case 'App\RelatedPartyDocumentEmail':
                return 'Document Email';
                break;
            case 'App\RelatedPartyDocument':
                return 'Document';
                break;
            case 'App\RelatedPartyBoolean':
            case 'App\RelatedPartyDropdown':
                return 'Dropdown';
                break;
            case 'App\RelatedPartyDate':
                return 'Date';
                break;
            case 'App\RelatedPartyNotification':
                return 'Notification';
                break;
            case 'App\RelatedPartyMultipleAttachment':
                return 'Multiple Attachment';
                break;
            default:
                return 'error';
                break;
        }
    }

    public function dependant(){
        return $this->belongsTo(Activity::class,'dependant_activity_id');
    }

    public function step(){
        return $this->belongsTo('App\Step','step_id');
    }

    public function steps(){
        return $this->hasOne(Step::class,'process_id');
    }

    public function comments()
    {
        return $this->hasMany('App\ActivityComment', 'activity_id');
    }

    public function getActivityMirrorValue($activity_id,$client_id){

        $val = null;
        $values = array();

        $mirror_values = ActivityMirrorValue::where('activity_id',$activity_id)->get();

        foreach ($mirror_values as $mirror_value) {
            if($mirror_value->mirror_type == 'activity') {

                $activity = Activity::find($mirror_value->mirror_activity_id);

                switch ($activity->getTypeName()) {
                    case 'boolean':
                        $data = ActionableBooleanData::where('actionable_boolean_id', $activity->actionable_id)->where('client_id', $client_id)->first();

                        if ($data) {
                            $val = $data->data;
                            array_push($values,['type'=>'boolean','val'=>($data->data == 0 ? 'No' : 'Yes')]);
                        }
                        break;
                    case 'date':
                        $data = ActionableDateData::where('actionable_date_id', $activity->actionable_id)->where('client_id', $client_id)->first();

                        if ($data) {
                            $val = $data->data;
                            array_push($values,$data->data);
                        }
                        break;
                    case 'text':

                        $data = ActionableTextData::where('actionable_text_id', $activity->actionable_id)->where('client_id', $client_id)->first();

                        if ($data) {
                            $val = $data->data;
                            array_push($values,$data->data);
                        }
                        break;
                    case 'amount':

                        $data = ActionableAmountData::where('actionable_amount_id', $activity->actionable_id)->where('client_id', $client_id)->first();

                        if ($data) {
                            $val = $data->data;
                            array_push($values,$data->data);
                        }
                        break;
                    case 'percentage':

                        $data = ActionablePercentageData::where('actionable_percentage_id', $activity->actionable_id)->where('client_id', $client_id)->first();

                        if ($data) {
                            $val = $data->data;
                            array_push($values,$data->data);
                        }
                        break;
                    case 'integer':

                        $data = ActionableIntegerData::where('actionable_integer_id', $activity->actionable_id)->where('client_id', $client_id)->first();

                        if ($data) {
                            $val = $data->data;
                            array_push($values,$data->data);
                        }
                        break;
                    case 'textarea':
                        $data = ActionableTextareaData::where('actionable_textarea_id', $activity->actionable_id)->where('client_id', $client_id)->first();

                        if ($data) {
                            $val = $data->data;
                            array_push($values,$data->data);
                        }
                        break;
                    case 'dropdown':
                        $array = [];
                        $datas = ActionableDropdownData::where('actionable_dropdown_id', $activity->actionable_id)->where('client_id', $client_id)->get();

                        foreach ($datas as $data) {
                            $item = ActionableDropdownItem::where('id', $data->actionable_dropdown_item_id)->first();
                            $array[] = $item->name;
                        }
                        if(count($array) > 0) {
                            $val = $array;
                            array_push($values, $array);
                        }
                        break;
                    default:
                        //todo capture defaults
                        break;
                }
            }

            if($mirror_value->mirror_type == 'crm') {

                $input = FormSectionInputs::find($mirror_value->mirror_activity_id);

                if(Auth::check()) {
                    $office_users = OfficeUser::select('user_id')->where('office_id', Auth::user()->office()->id)->get();
                } else {
                    $office_users = OfficeUser::select('user_id')->where('office_id', Client::where('id',$client_id)->first()->office_id)->get();
                }

                //if($input->form->form_id === '2'){
                    switch ($input->getFormTypeName()) {
                        case 'boolean':
                                $data = FormInputBooleanData::where('form_input_boolean_id', $input->input_id)->where('client_id', $client_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, ['type' => 'boolean', 'val' => ($data->data == 0 ? 'No' : 'Yes')]);
                                }
                            break;
                        case 'date':
                                $data = FormInputDateData::where('form_input_date_id', $input->input_id)->where('client_id', $client_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, $data->data);
                                }
                            break;
                        case 'text':

                            $data = FormInputTextData::where('form_input_text_id', $input->input_id)->where('client_id', $client_id)->first();

                            if ($data) {
                                $val = $data->data;
                                array_push($values,$data->data);
                            }
                            break;
                        case 'amount':
                                $data = FormInputAmountData::where('form_input_amount_id', $input->input_id)->where('client_id', $client_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, $data->data);
                                }
                            break;
                        case 'percentage':
                                $data = FormInputPercentageData::where('form_input_percentage_id', $input->input_id)->where('client_id', $client_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, $data->data);
                                }
                            break;
                        case 'integer':
                                $data = FormInputIntegerData::where('form_input_integer_id', $input->input_id)->where('client_id', $client_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, $data->data);
                                }
                            break;
                        case 'textarea':
                                $data = FormInputTextareaData::where('form_input_textarea_id', $input->input_id)->where('client_id', $client_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, $data->data);
                                }
                            break;
                        case 'dropdown':
                                /*$array = [];
                                $datas = FormInputDropdownData::where('form_input_dropdown_id', $input->input_id)->where('client_id', $client_id)->first();

                                foreach ($datas as $data) {
                                    $item = FormInputDropdownItem::where('id', $data->form_input_dropdown_item_id)->first();
                                    $array[] = $item->name;
                                }
                                if (count($array) > 0) {
                                    $val = $array;
                                    array_push($values, $array);
                                }*/
                            break;
                        default:
                            //todo capture defaults
                            break;
                    }
                //}

                /*if($input->form->form_id === '3') {
                    switch ($input->getFormTypeName()) {
                        case 'boolean':

                            foreach ($office_users as $office_user) {
                                $data = FormInputBooleanData::where('form_input_boolean_id', $input->id)->where('user_id', $office_user->user_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, ['type' => 'boolean', 'val' => ($data->data == 0 ? 'No' : 'Yes')]);
                                }
                            }
                            break;
                        case 'date':

                            foreach ($office_users as $office_user) {
                                $data = FormInputDateData::where('form_input_date_id', $input->id)->where('user_id', $office_user->user_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, $data->data);
                                }
                            }
                            break;
                        case 'text':

                            foreach ($office_users as $office_user) {

                                $data = FormInputTextData::where('form_input_text_id', $input->id)->where('user_id', $office_user->user_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, $data->data);
                                }
                            }
                            break;
                        case 'amount':

                            foreach ($office_users as $office_user) {
                                $data = FormInputAmountData::where('form_input_amount_id', $input->id)->where('user_id', $office_user->user_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, $data->data);
                                }
                            }
                            break;
                        case 'percentage':

                            foreach ($office_users as $office_user) {
                                $data = FormInputPercentageData::where('form_input_percentage_id', $input->id)->where('user_id', $office_user->user_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, $data->data);
                                }
                            }
                            break;
                        case 'integer':

                            foreach ($office_users as $office_user) {
                                $data = FormInputIntegerData::where('form_input_integer_id', $input->id)->where('user_id', $office_user->user_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, $data->data);
                                }
                            }
                            break;
                        case 'textarea':

                            foreach ($office_users as $office_user) {
                                $data = FormInputTextareaData::where('form_input_textarea_id', $input->id)->where('user_id', $office_user->user_id)->first();

                                if ($data) {
                                    $val = $data->data;
                                    array_push($values, $data->data);
                                }
                            }
                            break;
                        case 'dropdown':

                            foreach ($office_users as $office_user) {
                                $array = [];
                                $datas = FormInputDropdownData::where('form_input_dropdown_id', $input->id)->where('user_id', $office_user->user_id)->get();

                                foreach ($datas as $data) {
                                    $item = FormInputDropdownItem::where('id', $data->form_input_dropdown_item_id)->first();
                                    $array[] = $item->name;
                                }
                                if (count($array) > 0) {
                                    $val = $array;
                                    array_push($values, $array);
                                }
                            }
                            break;
                        default:
                            //todo capture defaults
                            break;
                    }
                }*/
            }

            if($mirror_value->mirror_type == 'default' && $mirror_value->mirror_column != '' && $mirror_value->mirror_column != '0') {

                if($mirror_value->mirror_column == 'full_name'){
                    $data = Client::where('id',$client_id)->first();

                    if ($data) {
                        $val = $data['first_name'].' '.$data['last_name'];
                        array_push($values,$data['first_name'].' '.$data['last_name']);
                    }
                } else {
                    $data = Client::select($mirror_value->mirror_column)->where('id',$client_id)->first();

                    if ($data) {
                        $val = $data[$mirror_value->mirror_column];
                        array_push($values,$data[$mirror_value->mirror_column]);
                    }

                }
            }
        }

        return ['count'=>count($mirror_values),'val' => ($val != null ? $val : ''),'mirror_values'=>(count($values) > 0 ? $values : '')];
    }
}