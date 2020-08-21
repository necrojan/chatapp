<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class Recaptcha
{
    /**
     * @var string
     */
    private $baseUrl = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function submit(array $data)
    {
        $response = Http::asForm()->post($this->baseUrl, $data);

        return json_decode($response, true);
    }
}