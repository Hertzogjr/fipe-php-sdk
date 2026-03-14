<?php

namespace Junior\FipePhpSdk;

use GuzzleHttp\Client;

class FipeClient
{
    public Client $client;

    public function __construct()
    {
        $this->client = new Client;
    }

    public function referenceTable(): ReferenceTable\FipeReferenceTableResource
    {
        return new ReferenceTable\FipeReferenceTableResource($this->client);
    }

    public function make(): Make\FipeMakeResource
    {
        return new Make\FipeMakeResource($this->client);
    }

    public function model(): Model\FipeModelResource
    {
        return new Model\FipeModelResource($this->client);
    }

    public function vehicle(): Vehicle\FipeVehicleResource
    {
        return new Vehicle\FipeVehicleResource($this->client);
    }
}
