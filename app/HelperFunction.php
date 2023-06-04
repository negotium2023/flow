<?php


namespace App;


use Carbon\Carbon;

class HelperFunction
{
    public function array_find($needle, array $haystack, $column = null) {

        if(is_array($haystack[0]) === true) { // check for multidimentional array


            foreach (array_column($haystack, $column) as $key => $value) {
                if (strpos(strtolower($value), strtolower($needle)) !== false) {
                    return $key;
                }
            }

        } else {
            foreach ($haystack as $key => $value) { // for normal array
                if (strpos(strtolower($value), strtolower($needle)) !== false) {
                    return $key;
                }
            }
        }
        return false;
    }

    public function getPath($request)
    {
        if((strpos($request->headers->get('referer'),'reports') !== false) || (strpos($request->headers->get('referer'),'custom_report') !== false)) {
            $request->session()->put('path_route',$request->headers->get('referer'));
            $path = '1';
            $path_route = $request->session()->get('path_route');
        } else {
            $request->session()->forget('path_route');
            $path = '0';
            $path_route = '';
        }

        return [
            'path' => $path,
            'path_route' => $path_route
        ];

    }

    public function officeSubscription(int $office_id = 0)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://helpdesk.blackboardbs.com/api/packageinfo/'.$office_id);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $subscription = json_decode(curl_exec($ch),true);

        $expiry_date = (Carbon::parse($subscription["data"]["expiry_date"]?? '0000-00-00'));
        $date_Diff = $expiry_date->diffInDays(now());

        return [
            'subscription' => $subscription['data']??[],
            'date_difference' => $date_Diff,
            'expiry_date' => Carbon::parse($expiry_date)->format('Ymd')
        ];
    }

    public function wizardStatus()
    {
        $office_user = app('App\OfficeUser');
        $office_id = $office_user->whereUserId(auth()->id())->first();
        $is_wizard = $office_user->where('office_id', $office_id->office_id)->count()??0;
        $is_dissmissed = Wizard::whereOfficeId($office_id->office_id)->first();
        $dismiss = $is_dissmissed->dismiss??0;
        $roles = auth()->user()->roles()->get()->map(function($role){ return $role->name;})->toArray();
        $number_of_users = auth()->user()->sub_users??3;
        $status = (($is_wizard < $number_of_users) && !$dismiss && in_array('Financial advisor', $roles))?0:1;

        return [
            'status' => $status,
            'office' => $office_id,
            'users' => $number_of_users,
            'registered_users' => $is_wizard
        ];
    }

    public function formatToTableColumnName($input)
    {
        return  strtolower(
                preg_replace(
                    '/(?<!^)[A-Z]/',
                    '_$0',
                    str_replace('App\\','',$input['input_type'])
                )
            )."_id";
    }

    public function getFormIputData()
    {
        $forms = Forms::with('sections.form_section_input')
            ->where('id', 3)
            ->get();

        $input_types = array();
        $final_collection = array();
        $office_users = OfficeUser::where('office_id', auth()->user()->offices()->first()->id)
            ->get(['user_id'])->map(function($user){
                return $user->user_id;
            })->toArray();

        foreach ($forms[0]->sections as $section){
            $input_types = [
                'id' => $section->id,
                'name' => $section->name,
                'inputs' => []
            ];

            foreach ($section->form_section_input as $input){

                $input_type_id = $this->formatToTableColumnName($input);
                $data = app($input["input_type"]."Data")
                    ->where($input_type_id, $input["id"])
                    ->whereIn('user_id', $office_users)
                    ->where('client_id', 0)
                    ->orderBy('id', 'desc')
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
                        'data_id' => $data->id
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

        return $final_collection;

    }

}