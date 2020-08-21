<?php

namespace App\Services;

use Illuminate\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ConnectWise
{
    /**
     * @var string
     */
    private $baseUrl = 'https://cw.company.com/v4_6_release/apis/3.0/';

    /**
     * @var Repository|mixed
     */
    private $clientId;

    /**
     * @var string
     */
    private $authToken;

    /**
     * ConnectWise constructor.
     */
    public function __construct()
    {
        $this->clientId = config('cw.client_id');
        $this->authToken = $this->initializeAuthToken();
    }

    /**
     * @param $email
     *
     * @return bool
     */
    public function isEmailOnList($email)
    {
        $emailList = Arr::pluck($this->cachedResponse(), 'communicationItems.0.value');

        if (!in_array($email, $emailList)) {
            return false;
        }

        return true;
    }

    /**
     * @param $email
     *
     * @return mixed
     */
    public function getNameAndCompany($email)
    {
        foreach ($this->cachedResponse() as $key => $result) {
            if (isset($result['communicationItems'])) {
                if ($result['communicationItems'][0]['value'] === $email) {
                    return $result;
                }
            }
        }
    }

    /**
     * @return string
     */
    private function initializeAuthToken()
    {
        $companyId = config('cw.company_id');
        $publicKey = config('cw.public_key');
        $privateKey = config('cw.private_key');

        return 'Basic ' . base64_encode("{$companyId}+{$publicKey}:{$privateKey}");
    }

    /**
     * @return array|bool
     */
    private function getCompanyContacts()
    {
        $response = Http::withHeaders([
            'Authorization' => $this->authToken,
            'clientId' => $this->clientId,
        ])->get($this->baseUrl . '/company/contacts');

        if (!$response->successful()) {
            logger($response->body());
        }

        return $response->json();
    }

    /**
     * @return mixed
     */
    private function cachedResponse()
    {
        return Cache::remember('company_contacts', 300, function () {
           return $this->getCompanyContacts();
        });
    }
}