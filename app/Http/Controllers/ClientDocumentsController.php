<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Forms;
use App\Client;
use App\FormSection;
use App\ActionableBoolean;
use App\ActionableBooleanData;
use App\ActionableDate;
use App\ActionableDateData;
use App\Document;
use App\ActionableDropdown;
use App\ActionableDropdownData;
use App\ActionableDropdownItem;
use App\ActionableText;
use App\ActionableTextData;
use App\Activity;
use App\Process;
use App\Step;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\ClientProcess;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\DB;

class ClientDocumentsController extends Controller
{
    public function __construct()
    {
        error_reporting(E_ALL ^ E_DEPRECATED);
        $this->middleware('auth')->except(['sendtemplate', 'sendnotification']);
        $this->middleware('auth:api')->only(['sendtemplate', 'sendnotification']);
    }

    public function generateFormDocument($client_id,$process_id){

        $client = Client::find($client_id);

        $client_form = ClientProcess::where('client_id',$client_id)->where('process_id',$process_id)->first();

        $template_file = Process::find($process_id);

        $formfields = Step::with(['activities.actionable.data'=>function ($q) use ($client_id){
            $q->where('client_id',$client_id)->orderBy('created_at','DESC');
        }])->where('process_id',$process_id)->get();

        if($client_form->signed == '1') {
            $user = User::withTrashed()->where('id',$client_form->signed_by)->first();
        }
        
        $templateProcessor = new TemplateProcessor(storage_path('app/templates/' . $template_file->document));
        $templateProcessor->setValue('date', date("Y/m/d"));
        
        $templateProcessor->setValue('client', ($client->company != null ? htmlentities($client->company) : $client->first_name . ' ' . $client->last_name));

        foreach($formfields as $sections) {
            if($sections->group > 0){
                $group = ($client->groupCompletedActivities($sections,$client->id) > 0 ? $client->groupCompletedActivities($sections,$client->id) :1);
                if($group > 0){
                    $a = preg_replace('/[0-9]+/', '',str_replace('/', '', str_replace(' ', '', str_replace('.', '', str_replace('-', '', strtolower($sections->name))))));
                    $templateProcessor->cloneBlock($a, $group, true, true);
                }

            }
            // dd($sections["activities"]);
            foreach ($sections["activities"] as $section) {
                    if (isset($section["actionable"]["data"]) && count($section["actionable"]["data"]) > 0) {
                        foreach ($section["actionable"]["data"] as $value) {

                            switch ($section['actionable_type']){
                                case 'App\ActionableDropdown':
                                    if($process_id == 16){
                                        $items = ActionableDropdownItem::where('actionable_dropdown_id', $value->actionable_dropdown_id)->get();
                                        foreach($items as $item) {
                                                $val = '';
                                                $data = ActionableDropdownData::where('actionable_dropdown_id',$item->actionable_dropdown_id)->where('client_id',$client_id)->first();
                                                // if($data){
                                                    if($data && $item->id == $data->actionable_dropdown_item_id){
                                                        $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"].'.'.str_replace(' ', '_', strtolower(str_replace(' ', '_', $item->name)));
                                                    
                                                            $val = $item->name;
                                                        $templateProcessor->setValue($var_name,$val);
                                                    } else {
                                                        $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"].'.'.str_replace(' ', '_', strtolower(str_replace(' ', '_', $item->name)));
                                                        $templateProcessor->setValue($var_name,'');
                                                    }
                                                // }
                                                }
                                    } else {
                                        if($sections['group'] > 0){
                                            $items = ActionableDropdownItem::where('actionable_dropdown_id',$value->actionable_dropdown_id)->limit(1)->get();
                                            if($items){
                                                $value_array = [];
                                                foreach ($items as $item) {
                                                    $data = ActionableDropdownData::where('client_id',$client_id)->where('actionable_dropdown_item_id', $item->id)->first();
                                                    if($data && $data["actionable_dropdown_item_id"] == $item->id){
                                                        array_push($value_array, $item['name']);
                                                    } else {
                                                        array_push($value_array, '');
                                                    }
                                                }
                                            }
                                            $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . strtolower(str_replace(' ', '_', $section["name"])).'#'.$section["grouping"];
                                        } else {
                                            if($section['multiple_selection'] == '0'){
                                                $val = '';
                                                $items = ActionableDropdownData::where('actionable_dropdown_id',$value->actionable_dropdown_id)->where('client_id',$client_id)->limit(1)->get();
                                                if($items){
                                                    foreach ($items as $item) {
                                                        $data = ActionableDropdownItem::find($item->actionable_dropdown_item_id);
                                                        
                                                        if(!empty($data)){
                                                            $val = $data->name;
                                                        }
                                                    }
                                                }
                                                $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                                // if($section["order"] == '3' && $sections->id == '78'){
                                                //     dd($var_name.'-'.$val);
                                                // }
                                                $templateProcessor->setValue($var_name,$val);
                                            } else {
                                                $items = ActionableDropdownItem::where('actionable_dropdown_id',$value->actionable_dropdown_id)->get();
                                                if($items){
                                                    if(!isset($value_array)){
                                                        $value_array = [];
                                                    }
                                                        foreach ($items as $item) {
                                                            $data = ActionableDropdownData::where('client_id',$client_id)->where('actionable_dropdown_item_id', $item->id)->first();
                                                            if($data && $data["actionable_dropdown_item_id"] == $item->id){
                                                                array_push($value_array, $item['name']);
                                                            }
                                                        }
                                                }
                                                $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                                $arr = implode('${newline}',$value_array);
                                                $templateProcessor->setValue($var_name,$arr);
                                                $new_line = new \PhpOffice\PhpWord\Element\PreserveText('</w:t><w:br/><w:t>');
                                                $templateProcessor->setComplexValue('newline', $new_line);
                                            }
                                        }
                                        
                                    }
                                    

                                    break;
                                case 'App\ActionableBoolean':
                                    if($sections['group'] > 0){
                                        $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . strtolower(str_replace(' ', '_', $section["name"])).'#'.$section["grouping"];
                                    } else {
                                        $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                    }
                                    $arr = $value->data;
                                    $templateProcessor->setValue($var_name,($arr == 1 ? 'Yes' : 'No'));
                                    break;
                                default:
                                    if($sections['group'] > 0){
                                        $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . strtolower(str_replace(' ', '_', $section["name"])).'#'.$section["grouping"];
                                    } else {
                                        $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                    }
                                    $arr = $value->data;
                                    $templateProcessor->setValue($var_name,$arr);
                                    // $templateProcessor->setValue($var_name,htmlspecialchars(str_replace('<p>','',str_replace('</p>','',$arr))));
                                    break;
                            }
                        }
                    } else {

                            switch ($section['actionable_type']){
                                case 'App\ActionableDropdown':
                                    if($process_id == 16){
                                        $items = ActionableDropdownItem::where('actionable_dropdown_id', $section->actionable_dropdown_id)->get();
                                        foreach($items as $item) {
                                                // array_push($value_array, '');
                                                $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"].'.'.str_replace(' ', '_', strtolower(str_replace(' ', '_', $item->name)));
                                                // $arr = implode(',',$value_array);
                                                $templateProcessor->setValue($var_name,'');                                            
                                        }
                                    } else {
                                            if($sections['group'] > 0){
                                                $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . strtolower(str_replace(' ', '_', $section["name"])).'#'.$section["grouping"];
                                            } else {
                                                $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                            }
                                            $templateProcessor->setValue($var_name,'');
                                        }
                                    break;
                                default:
                                    if($sections['group'] > 0){
                                        $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . strtolower(str_replace(' ', '_', $section["name"])).'#'.$section["grouping"];
                                    } else {
                                        $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                    }
                                    // $templateProcessor->setValue($var_name,'');
                                    break;
                            }
                        }
            }
        }
        
        if($client_form->signed == '1') {
            $templateProcessor->setValue('signature',$user->first_name.' '.$user->last_name);
            $templateProcessor->setValue('now',Carbon::parse($client_form->signed_date)->format('Y-m-d'));
        }else {
            $templateProcessor->setValue('signature','');
            $templateProcessor->setValue('now','');
        }
        //Create directory to store processed templates, for future reference or to check what was sent to the client
        $processed_template_path = 'processedforms/'.($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.str_replace(' ','_',$client->last_name))).'/';
        if (!File::exists(storage_path('app/forms/' . $processed_template_path))) {
            Storage::makeDirectory('forms/' . $processed_template_path);
        }
        $filename = explode('.',$template_file->document);

        $processed_template = $processed_template_path . DIRECTORY_SEPARATOR . str_replace(' ','_',$filename[0]). "_" . ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.str_replace(' ','_',$client->last_name))) . "_".str_replace(' ','',$template_file->name).".docx";
        if(File::exists(storage_path('app/forms/' . $processed_template))){
            Storage::delete('forms/' . $processed_template);
        }

        $processed_template_pdf = $processed_template_path . DIRECTORY_SEPARATOR . str_replace(' ','_',$filename[0]). "_" . ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.str_replace(' ','_',$client->last_name))) . "_".str_replace(' ','',$template_file->name).".pdf";

        if(File::exists(storage_path('app/forms/' . $processed_template_pdf))){
            Storage::delete('forms/' . $processed_template_pdf);
        }

        $templateProcessor->saveAs(storage_path('app/forms/' . $processed_template));
        
        shell_exec('libreoffice --headless --convert-to pdf '.storage_path('app/forms/' .$processed_template).' --outdir '.storage_path('app/forms/' . $processed_template_path));
        // return Storage::download('forms/' . $processed_template_pdf );

            /*$domPdfPath = base_path('vendor/dompdf/dompdf');
             \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
             \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

            //  //Load word file
             $Content = \PhpOffice\PhpWord\IOFactory::load(storage_path('app/forms/' . $processed_template));

            //  //Save it into PDF
             $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($Content,'PDF');
             $PDFWriter->save(storage_path('app/forms/' . $processed_template_pdf));*/

             $existing_doc = $this->isExistingClientDoc($client_id,$process_id);
             
            if(!$existing_doc){
                $document = new Document;
            } else {
                $document = Document::find($existing_doc);
            }

            $document->name = $template_file->name;
            $document->file = '/'.str_replace(' ','_',$filename[0]). "_" . ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.str_replace(' ','_',$client->last_name))) . "_".str_replace(' ','',$template_file->name).".pdf";
            $document->user_id = Auth::id();
            $document->client_id = $client_id;
            $document->form_process_id = $template_file->id;
            $document->save();
            // dd($existing_doc);
            return response()->json([
                'http_code' => 200
            ]);

    }

    public function signFormDocument($client_id,$process_id){

        $client = Client::find($client_id);

        $client_form = ClientProcess::where('client_id',$client_id)->where('process_id',$process_id)->first();

        $template_file = Process::find($process_id);

        $formfields = Step::with(['activities.actionable.data'=>function ($q) use ($client_id){
            $q->where('client_id',$client_id);
        }])->where('process_id',$process_id)->get();

        $client_form->signed = 1;
        $client_form->signed_by = Auth::id();
        $client_form->signed_date = now();
        $client_form->save();
        
        $user = User::withTrashed()->where('id',$client_form->signed_by)->first();
        
        $templateProcessor = new TemplateProcessor(storage_path('app/templates/' . $template_file->document));
        $templateProcessor->setValue('date', date("Y/m/d"));
        
        $templateProcessor->setValue('client', ($client->company != null ? htmlentities($client->company) : $client->first_name . ' ' . $client->last_name));

        foreach($formfields as $sections) {
            if($sections->group > 0){
                $group = ($client->groupCompletedActivities($sections,$client->id) > 0 ? $client->groupCompletedActivities($sections,$client->id) :1);
                if($group > 0){
                    $a = preg_replace('/[0-9]+/', '',str_replace('/', '', str_replace(' ', '', str_replace('.', '', str_replace('-', '', strtolower($sections->name))))));
                    $templateProcessor->cloneBlock($a, $group, true, true);
                }

            }
            
            foreach($formfields as $sections) {
                if($sections->group > 0){
                    $group = ($client->groupCompletedActivities($sections,$client->id) > 0 ? $client->groupCompletedActivities($sections,$client->id) :1);
                    if($group > 0){
                        $a = preg_replace('/[0-9]+/', '',str_replace('/', '', str_replace(' ', '', str_replace('.', '', str_replace('-', '', strtolower($sections->name))))));
                        $templateProcessor->cloneBlock($a, $group, true, true);
                    }
    
                }
                
                foreach ($sections["activities"] as $section) {
                        if (isset($section["actionable"]->data) && count($section["actionable"]->data) > 0) {
                            foreach ($section["actionable"]->data as $value) {
    
                                switch ($section['actionable_type']){
                                    case 'App\ActionableDropdown':
                                        if($process_id == 16){
                                            $var = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                            $items = ActionableDropdownItem::where('actionable_dropdown_id', $section->actionable_dropdown_id)->get();
                                            foreach($items as $item) {
                                                $value_array = [];
                                                $data = ActionableDropdownData::where('client_id',$client_id)->where('actionable_dropdown_item_id', $item->id)->first();
                                                if($data && $data["actionable_dropdown_item_id"] == $item->id){
                                                    array_push($value_array, $item['name']);
                                                    $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"].'.'.str_replace(' ', '_', strtolower(str_replace(' ', '_', $item->name)));
                                                    $arr = implode(',',$value_array);
                                                    $templateProcessor->setValue($var_name,$arr);
                                                } else {
                                                    array_push($value_array, '');
                                                    $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"].'.'.str_replace(' ', '_', strtolower(str_replace(' ', '_', $item->name)));
                                                    $arr = implode(',',$value_array);
                                                    $templateProcessor->setValue($var_name,$arr);
                                                }
                                                
                                            }
                                        } else {
                                            if($sections['group'] > 0){
                                                $items = ActionableDropdownItem::where('actionable_dropdown_id',$value->actionable_dropdown_id)->limit(1)->get();
                                                if($items){
                                                    $value_array = [];
                                                    foreach ($items as $item) {
                                                        $data = ActionableDropdownData::where('client_id',$client_id)->where('actionable_dropdown_item_id', $item->id)->first();
                                                        if($data && $data["actionable_dropdown_item_id"] == $item->id){
                                                            array_push($value_array, $item['name']);
                                                        } else {
                                                            array_push($value_array, '');
                                                        }
                                                    }
                                                }
                                                $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . strtolower(str_replace(' ', '_', $section["name"])).'#'.$section["grouping"];
                                            } else {
                                                if($section['multiple_selection'] == 0){
                                                        $val = '';
                                                    $items = ActionableDropdownItem::where('actionable_dropdown_id',$value->actionable_dropdown_id)->limit(1)->get();
                                                    if($items){
                                                        foreach ($items as $item) {
                                                            $data = ActionableDropdownData::where('client_id',$client_id)->where('actionable_dropdown_item_id', $item->id)->first();
                                                            if($data){
                                                                $val = $item['name'];
                                                            }
                                                        }
                                                    }
                                                    $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                                    $templateProcessor->setValue($var_name,$val);
                                                } else {
                                                    $items = ActionableDropdownItem::where('actionable_dropdown_id',$value->actionable_dropdown_id)->get();
                                                    if($items){
                                                        if(!isset($value_array)){
                                                            $value_array = [];
                                                        }
                                                            foreach ($items as $item) {
                                                                $data = ActionableDropdownData::where('client_id',$client_id)->where('actionable_dropdown_item_id', $item->id)->first();
                                                                if($data && $data["actionable_dropdown_item_id"] == $item->id){
                                                                    array_push($value_array, $item['name']);
                                                                }
                                                            }
                                                    }
                                                    $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                                    $arr = implode('${newline}',$value_array);
                                                    $templateProcessor->setValue($var_name,$arr);
                                                    $new_line = new \PhpOffice\PhpWord\Element\PreserveText('</w:t><w:br/><w:t>');
                                                    $templateProcessor->setComplexValue('newline', $new_line);
                                                }
                                            }
                                            
                                        }
                                        
    
                                        break;
                                    case 'App\ActionableBoolean':
                                        if($sections['group'] > 0){
                                            $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . strtolower(str_replace(' ', '_', $section["name"])).'#'.$section["grouping"];
                                        } else {
                                            $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                        }
                                        $arr = $value->data;
                                        $templateProcessor->setValue($var_name,($arr == 1 ? 'Yes' : 'No'));
                                        break;
                                    default:
                                        if($sections['group'] > 0){
                                            $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . strtolower(str_replace(' ', '_', $section["name"])).'#'.$section["grouping"];
                                        } else {
                                            $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                        }
                                        $arr = $value->data;
                                        $templateProcessor->setValue($var_name,htmlspecialchars(str_replace('<p>','',str_replace('</p>','',$arr))));
                                        break;
                                }
                            }
                        } else {
    
                                switch ($section['actionable_type']){
                                    case 'App\ActionableDropdown':
                                        if($sections['group'] > 0){
                                            $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . strtolower(str_replace(' ', '_', $section["name"])).'#'.$section["grouping"];
                                        } else {
                                            $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                        }
                                        // $templateProcessor->setValue($var_name,'');
                                        break;
                                    default:
                                        if($sections['group'] > 0){
                                            $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . strtolower(str_replace(' ', '_', $section["name"])).'#'.$section["grouping"];
                                        } else {
                                            $var_name = strtolower(str_replace(' ', '_', $sections->id)) . '.' . $section["order"];
                                        }
                                        // $templateProcessor->setValue($var_name,'');
                                        break;
                                }
                            }
                }
            }
    
            if($client_form->signed == '1') {
                $templateProcessor->setValue('signature',$user->first_name.' '.$user->last_name);
                $templateProcessor->setValue('now',Carbon::parse($client_form->signed_date)->format('Y-m-d'));
            }else {
                $templateProcessor->setValue('signature','');
                $templateProcessor->setValue('now','');
            }
        }
        
        //Create directory to store processed templates, for future reference or to check what was sent to the client
        $processed_template_path = 'processedforms/'.($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.str_replace(' ','_',$client->last_name))).'/';
        if (!File::exists(storage_path('app/forms/' . $processed_template_path))) {
            Storage::makeDirectory('forms/' . $processed_template_path);
        }
        $filename = explode('.',$template_file->document);

        $processed_template = $processed_template_path . DIRECTORY_SEPARATOR . str_replace(' ','_',$filename[0]). "_" . ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.str_replace(' ','_',$client->last_name))) . ".docx";
        if(File::exists(storage_path('app/forms/' . $processed_template))){
            Storage::delete('forms/' . $processed_template);
        }

        $processed_template_pdf = $processed_template_path . DIRECTORY_SEPARATOR . str_replace(' ','_',$filename[0]). "_" . ($client->company != null ? str_replace(' ','',preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->company)) : preg_replace('/[^a-zA-Z0-9_ -]/s','',$client->first_name.'_'.str_replace(' ','_',$client->last_name))) . ".pdf";

        if(File::exists(storage_path('app/forms/' . $processed_template_pdf))){
            Storage::delete('forms/' . $processed_template_pdf);
        }

        $templateProcessor->saveAs(storage_path('app/forms/' . $processed_template));
        
        shell_exec('libreoffice --headless --convert-to pdf '.storage_path('app/forms/' .$processed_template).' --outdir '.storage_path('app/forms/' . $processed_template_path));
        // return Storage::download('forms/' . $processed_template_pdf );

        /*$domPdfPath = base_path('vendor/dompdf/dompdf');
             \PhpOffice\PhpWord\Settings::setPdfRendererPath($domPdfPath);
             \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');

        //      //Load word file
             $Content = \PhpOffice\PhpWord\IOFactory::load(storage_path('app/forms/' . $processed_template));

        //      //Save it into PDF
             $PDFWriter = \PhpOffice\PhpWord\IOFactory::createWriter($Content,'PDF');
             $PDFWriter->save(storage_path('app/forms/' . $processed_template_pdf));*/

            return response()->json([
                'http_code' => 200
            ]);

    }

    function isExistingClientDoc($client_id,$process_id){
        $doc = Document::where('client_id',$client_id)->where('form_process_id',$process_id)->first();

        if($doc){
            return $doc["id"];
        } else {
            return false; 
        }
    }
}
