<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Junior\FipePhpSdk\FipeClient;

function mockClient(array $responses): Client
{
    return new Client(['handler' => HandlerStack::create(new MockHandler($responses))]);
}

function mockFipeClient(array $responses): FipeClient
{
    $fipeClient = new FipeClient;
    $fipeClient->client = mockClient($responses);

    return $fipeClient;
}

uses()->group('unit')->in('Unit');
uses()->group('feature')->in('Feature');
