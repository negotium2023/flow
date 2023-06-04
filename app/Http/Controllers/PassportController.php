<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PassportController extends Controller
{
    public function clients()
    {
        return view('passport.clients');
    }

    public function authorizedClients()
    {
        return view('passport.authorizedclients');
    }

    public function personalAccessTokens()
    {
        return view('passport.personalaccesstokens');
    }
}
