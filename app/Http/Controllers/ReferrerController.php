<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReferrerRequest;
use App\Referrer;
use App\ReferrerType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReferrerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $referrers = Referrer::orderBy('first_name');

        if ($request->has('q') && $request->input('q') != '') {
            $referrers->where(function ($query) use ($request) {
                $query->where(DB::raw("CONCAT(first_name,' ',COALESCE(`last_name`,''))"), 'LIKE', "%" . $request->input('q') . "%")
                    ->orWhere('email', 'like', "%" . $request->input('q') . "%");
            });
        }

        return view('referrers.index')->with(['referrers'=>$referrers->get()]);
    }

    public function create()
    {
        $parameters = [
            'referrer_type' => ReferrerType::orderBy("id","asc")->pluck('description', 'id')->prepend('Referrer Type', '0')
        ];

        return view('referrers.create')->with($parameters);
    }

    public function store(StoreReferrerRequest $request)
    {
            $referrer = new Referrer;
            $referrer->referrer_type = $request->input('referrer_type');
            $referrer->first_name = $request->input('first_name');
            $referrer->last_name = $request->input('last_name');
            $referrer->email = $request->input('email');
            $referrer->contact = $request->input('contact');
            $referrer->uhy_referral = $request->input('uhy_referral');
            $referrer->uhy_firm_name = $request->has('uhy_firm_name') ? $request->input('uhy_firm_name') : '';
            $referrer->uhy_contact = $request->has('uhy_contact') ? $request->input('uhy_contact') : '';
            $referrer->save();

            if ($request->session()->pull('platform') === "desktop")
                return redirect(route('referrers.index'))->with('flash_success', 'Referrer captured successfully');
            else
                return redirect(route('referrers.create'))->with('flash_success', 'Referrer captured successfully');

    }

    public function show(Referrer $referrer)
    {
        return view('referrers.show')->with(['referrer'=>$referrer->load('clients')]);
    }

    public function edit(Referrer $referrer)
    {
        $parameters = [
            'referrer' => $referrer,
            'referrer_type' => ReferrerType::orderBy("id","asc")->pluck('description', 'id')->prepend('Referrer Type', '0')
        ];

        return view('referrers.edit')->with($parameters);
    }

    public function update(Referrer $referrer, Request $request)
    {
        $referrer->referrer_type = $request->input('referrer_type');
        $referrer->first_name = $request->input('first_name');
        $referrer->last_name = $request->input('last_name');
        $referrer->email = $request->input('email');
        $referrer->contact = $request->input('contact');
        $referrer->uhy_referral = $request->input('uhy_referral');
        $referrer->uhy_firm_name = $request->has('uhy_firm_name')?$request->input('uhy_firm_name'):'';
        $referrer->uhy_contact = $request->has('uhy_contact')?$request->input('uhy_contact'):'';
        $referrer->save();

        return redirect(route('referrers.show',$referrer))->with('flash_success', 'Referrer updated successfully');
    }

    public function destroy($id)
    {
        //
    }
}
