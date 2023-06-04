<?php

namespace App\TokenStore;

use App\ClientHelper;
use Illuminate\Database\Eloquent\Model;

class MicrosoftTokenCache extends Model
{
    public function storeTokens($accessToken, $user) {
        session([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'tokenExpires' => $accessToken->getExpires(),
            'userName' => $user->getDisplayName(),
            'userEmail' => null !== $user->getMail() ? $user->getMail() : $user->getUserPrincipalName(),
            'userID' => $user->getID(),
            'userPrincipalName' => $user->getUserPrincipalName()
        ]);
    }

    public function clearTokens() {
        session()->forget('accessToken');
        session()->forget('refreshToken');
        session()->forget('tokenExpires');
        session()->forget('userName');
        session()->forget('userEmail');
    }

    // <GetAccessTokenSnippet>
    public function getAccessToken() {
        // Check if tokens exist
        if (empty(session('accessToken')) ||
            empty(session('refreshToken')) ||
            empty(session('tokenExpires'))) {
            return '';
        }

        // Check if token is expired
        //Get current time + 5 minutes (to allow for time differences)
        $now = time() + 600;
        if (session('tokenExpires') <= $now) {
            // Token is expired (or very close to it)
            // so let's refresh

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

            try {
                $newToken = $oauthClient->getAccessToken('refresh_token', [
                    'refresh_token' => session('refreshToken')
                ]);

                // Store the new values
                $this->updateTokens($newToken);

                return $newToken->getToken();
            }
            catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return '';
            }
        }

        // Token is still valid, just return it
        return session('accessToken');
    }
    // </GetAccessTokenSnippet>

    // <UpdateTokensSnippet>
    public function updateTokens($accessToken) {
        session([
            'accessToken' => $accessToken->getToken(),
            'refreshToken' => $accessToken->getRefreshToken(),
            'tokenExpires' => $accessToken->getExpires()
        ]);
    }
    // </UpdateTokensSnippet>

    public function getUserID() {
        if (empty(session('userID'))){
            return '';
        }

        return session('userID');
    }

    public function getUserPrincipalName() {
        if (empty(session('userPrincipalName'))){
            return '';
        }

        return session('userPrincipalName');
    }
}
