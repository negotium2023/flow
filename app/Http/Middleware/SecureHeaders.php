<?php

namespace App\Http\Middleware;

use Closure;

class SecureHeaders
{
    // Enumerate headers which you do not want in your application's responses.
    // Great starting point would be to go check out @Scott_Helme's:
    // https://securityheaders.com/
    private $unwantedHeaderList = [
        'X-Powered-By',
        'Server',
    ];
    public function handle($request, Closure $next)
    {
        $this->removeUnwantedHeaders($this->unwantedHeaderList);
        $response = $next($request);
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Access-Control-Allow-Origin', '*'); // This is for demo purposes, please remove on production
        /*$response->headers->set('Content-Security-Policy', "style-src 'self' https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/fonts/fontawesome-webfont.woff2?v=4.7.0 https://www.google.com/recaptcha/api.js? https://www.google.com/recaptcha/api2/anchor?ar=1&k=6LeChlcUAAAAALZ2XqJkkLECgOOuXqQhaFU5T3ev&co=aHR0cHM6Ly9zcGVjaWFsaXNlZG9wZXJhdGlvbnMuYmxhY2tib2FyZGJzLmNvbTo0NDM.&hl=en&v=v1565591531251&size=normal&cb=tqealyuv78e5 https://www.google.com/recaptcha/api2/webworker.js?hl=en&v=v1565591531251 https://www.google.com/js/bg/pWmgTbzrV74Bm6PUhVaf0UNg58JyytbJ0QQZlIXNEVM.js https://www.google.com/recaptcha/api2/bframe?hl=en&v=v1565591531251&k=6LeChlcUAAAAALZ2XqJkkLECgOOuXqQhaFU5T3ev&cb=6dgudupeg22d https://fonts.googleapis.com/css?family=Pacifico https://code.jquery.com/jquery-3.2.1.slim.min.js https://www.gstatic.com/recaptcha/api2/v1565591531251/recaptcha__en.js https://www.gstatic.com/recaptcha/api2/v1565591531251/styles__ltr.css https://www.gstatic.com/recaptcha/api2/logo_48.png https://fonts.gstatic.com/s/roboto/v18/KFOmCnqEu92Fr1Mu4mxK.woff2"); // Clearly, you will be more elaborate here.*/
        return $response;
    }
    private function removeUnwantedHeaders($headerList)
    {
        foreach ($headerList as $header)
            header_remove($header);
    }

}
