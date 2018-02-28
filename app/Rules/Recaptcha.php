<?php

namespace App\Rules;

use GuzzleHttp\Client;

class Recaptcha
{
    public function passes($attribute, $value)
    {
        if (app()->runningUnitTests()) return true;

        $client = new Client([
            'base_uri' => config('services.recaptcha.url'),
            'timeout'  => 9.0,
            'verify' => false,
            'headers' => [ 'Content-Type' => 'application/x-www-form-urlencoded' ]
        ]);

        $vals = [
            'form_params' => [
                'secret' => config('services.recaptcha.secret_test'),
                'response' => $value,
                'remoteip' => request()->ip(),
            ]
        ];

        $response = $client->post('recaptcha/api/siteverify', $vals);
        $result = json_decode($response->getBody()->getContents());

        return $result->success;
    }
}