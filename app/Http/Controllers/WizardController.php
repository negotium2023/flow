<?php

namespace App\Http\Controllers;

use App\Area;
use App\Division;
use App\HelperFunction;
use App\Office;
use App\OfficeUser;
use App\Region;
use App\Wizard;
use App\Forms;
use Illuminate\Http\Request;

class WizardController extends Controller
{
    private $helper;
    public function __construct()
    {
        $this->middleware('auth');
        $this->helper = new HelperFunction();
    }
    public function FADetails()
    {
        $forms = Forms::with('sections.form_section_input')
            ->where('id', 3)
            ->get();

        $input_types = array();
        $final_collection = array();

        foreach ($forms[0]->sections as $section){
            $input_types = [
                'id' => $section->id,
                'name' => $section->name,
                'inputs' => [],
                "open" => false,
            ];


            foreach ($section->form_section_input as $input){
                $input_type_id = $this->helper->formatToTableColumnName($input);
                $data = app($input["input_type"]."Data")
                    ->where($input_type_id, $input["id"])
                    ->where('user_id', auth()->id())
                    ->where('client_id', 0)
                    ->latest()
                    ->first(['id', 'data']);

                if (isset($data) && !request()->edit){
                    return response()->json(['is_display' => false]);
                }

                if (isset($data) && request()->edit){
                    array_push($input_types["inputs"], [
                        'id' => $input->id,
                        'label' => $input->name,
                        'input_type' => $input->input_type,
                        'data' => $data->data??null,
                        'data_id' => $data->id??null
                    ]);
                }else{
                    array_push($input_types["inputs"], [
                        'id' => $input->id,
                        'label' => $input->name,
                        'input_type' => $input->input_type,
                        'data' => "",
                        'data_id' => ""
                    ]);
                }

                if (!request()->edit){
                    array_push($input_types["inputs"], [
                        'id' => $input->id,
                        'label' => $input->name,
                        'input_type' => $input->input_type
                    ]);
                }


            }
            array_push($final_collection,$input_types);
        }

        return response()->json(['forms' => $final_collection??[], 'is_display'=>true]);
    }

    public function isWizardDismissed()
    {
        $status = $this->helper->wizardStatus()["status"];

        if($status){
            return response()->json([
                'dismiss' => $status,
            ]);
        }

        $regions = Region::get(['id', 'name']);
        $areas = Area::get(['id', 'name']);
        $divisions = Division::get(['id', 'name']);
        $offices = Office::where('id', $this->helper->wizardStatus()["office"]->office_id)->get(['id', 'name']);

        return response()->json([
            'dismiss' => $status,
            'sub_users' => $this->helper->wizardStatus()["users"],
            'default_user_count' => $this->helper->wizardStatus()["registered_users"],
            'office_id' => $this->helper->wizardStatus()["office"]->office_id,
            'region_drop_down' => $regions,
            'area_drop_down' => $areas,
            'divisions_drop_down' => $divisions,
            'offices_drop_down' => $offices,
        ]);
    }

    public function dismissWizard(Request $request)
    {
        $wizard = new Wizard();
        $wizard->dismiss = $request->dismiss;
        $wizard->office_id = $request->office_id;
        $wizard->save();

        return response()->json(['dismiss' => $wizard->dismiss ? false : true, 'office_id' => $wizard->office_id]);
    }

    public function storeFADetails(Request $request)
    {

        foreach ($request->data as $input) {

            $input_type_id = $this->helper->formatToTableColumnName($input);

            $input_class = $input['input_type'].'Data';

            $input_data = !$request->edit ||  !isset($input["id"]) ? new $input_class : app($input["input_type"]."Data")->find($input["id"]);
            $input_data["data"] = $input["data"];
            $input_data[$input_type_id] = !$request->edit ? $input["id"] : (isset($input_data[$input_type_id])?$input_data[$input_type_id]:$input["input_type_id"]);
            $input_data["client_id"] = 0;
            $input_data["user_id"] = auth()->id();
            $input_data["duration"] = 120;
            $input_data->save();
        }

        return response()->json(['wizard_status' => $this->helper->wizardStatus()["status"]]);
    }
}
