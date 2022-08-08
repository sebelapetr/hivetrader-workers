<?php

namespace App\Model\Services\Connectors;

use Contributte\Http\Curl\CurlClient;
use Curl\Curl;

class NovikoConnector
{
    private string $username;
    private string $password;

    public function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function getConnection(): Curl
    {
        $curl = new Curl();
        $curl->setBasicAuthentication($this->username, $this->password);
        $curl->setUrl("https://www.noviko-online.cz:8081/");
        return $curl;
    }
}