<?php

namespace App\Http\Controllers;

use http\Env\Response;
use Illuminate\Http\Request;
use \App\TokenStore\TokenCache;
use App\User;

class AuthController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function login(Request $request)
    {
        $http = new \GuzzleHttp\Client;

        try {
            $response = $http->post('http://attooh.test/oauth/token', [
               'form_params' => [
                   'grant_type' => 'password',
                   'client_id' => 3,
                   'client_secret' => 'dv7j87yxnK38CvSeMxSzUewvHiHfL62O1gCHqRop',
                   'username' => $request->username,
                   'password' => $request->password
               ]
            ]);

            return $response->getBody();
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            if($e->getCode() === 400) {
                return response()->json('Invalid Request. Please enter a username or a password.', $e->getCode());
            } else if($e->getCode() === 401) {
                return response()->json('Your credentials are incorrect. Please try again.', $e->getCode());
            }

            return response()->json('Something went wrong on the server.', $e->getCode());
        }
    }

    public function register(Request $request)
    {
        $request->validate([
           'first_name' => 'required|string|max:255',
           'last_name' => 'required|string|max:255',
           'email' => 'required|string|email|max:255|unique:users',
           'password' => 'required|string|email|min:6'
        ]);

        return User::create([
           'first_name' => $request->first_name,
           'last_name' => $request->last_name,
           'email' => $request->email,
           'password' => bcrypt($request->password)
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens(function($token, $key) {
           $token->revoke();
           // $token->delete();
        });

        return response()->json('Logged out successfully', 200);
    }

    public function signin()
    {
        if (session_status() == PHP_SESSION_NONE) {
          session_start();
        }

        // Initialize the OAuth client
        $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
          'clientId'                => env('OAUTH_APP_ID'),
          'clientSecret'            => env('OAUTH_APP_PASSWORD'),
          'redirectUri'             => env('OAUTH_REDIRECT_URI'),
          'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
          'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
          'urlResourceOwnerDetails' => '',
          'scopes'                  => env('OAUTH_SCOPES')
        ]);

        // Generate the auth URL
        $authorizationUrl = $oauthClient->getAuthorizationUrl();

        // Save client state so we can validate in response
        $_SESSION['oauth_state'] = $oauthClient->getState();

        // Redirect to authorization endpoint
        header('Location: '.$authorizationUrl);
        exit();
    }
    
    public function gettoken() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Authorization code should be in the "code" query param
        if (isset($_GET['code'])) {
            // Check that state matches
            if (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth_state'])) {
                exit('State provided in redirect does not match expected value.');
            }

            // Clear saved state
            unset($_SESSION['oauth_state']);

            // Initialize the OAuth client
            $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId' => env('OAUTH_APP_ID'),
                'clientSecret' => env('OAUTH_APP_PASSWORD'),
                'redirectUri' => env('OAUTH_REDIRECT_URI'),
                'urlAuthorize' => env('OAUTH_AUTHORITY') . env('OAUTH_AUTHORIZE_ENDPOINT'),
                'urlAccessToken' => env('OAUTH_AUTHORITY') . env('OAUTH_TOKEN_ENDPOINT'),
                'urlResourceOwnerDetails' => '',
                'scopes' => env('OAUTH_SCOPES')
            ]);

            try {
                // Make the token request
                $accessToken = $oauthClient->getAccessToken('authorization_code', [
                    'code' => $_GET['code']
                ]);

                // Save the access token and refresh tokens in session
                // This is for demo purposes only. A better method would
                // be to store the refresh token in a secured database
                $tokenCache = new TokenCache;
                $tokenCache->storeTokens($accessToken->getToken(), $accessToken->getRefreshToken(), $accessToken->getExpires());

                // Redirect back to mail page
                return redirect()->route('outlook.calendar');
            } catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                exit('ERROR getting tokens: ' . $e->getMessage());
            }
            exit();
        } elseif (isset($_GET['error'])) {
            exit('ERROR: ' . $_GET['error'] . ' - ' . $_GET['error_description']);
        }
    }

}
