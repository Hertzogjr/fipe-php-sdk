<?php

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Junior\FipePhpSdk\ReferenceTable\Entities\FipeReferenceTableEntity;
use Junior\FipePhpSdk\ReferenceTable\FipeReferenceTableException;

describe('Reference table via FipeClient', function () {
    it('returns a list of reference tables with code and month', function () {
        $payload = [
            ['Codigo' => '331', 'Mes' => 'janeiro/2024'],
            ['Codigo' => '330', 'Mes' => 'dezembro/2023'],
            ['Codigo' => '329', 'Mes' => 'novembro/2023'],
        ];

        $client = mockFipeClient([new Response(200, [], json_encode($payload))]);

        $result = $client->referenceTable()->all();

        expect($result)->toHaveKey('data')
            ->and($result['data'])->toHaveCount(3)
            ->and($result['data'][0])->toBeInstanceOf(FipeReferenceTableEntity::class)
            ->and($result['data'][0]->code)->toBe('331')
            ->and($result['data'][0]->month)->toBe('janeiro/2024')
            ->and($result['data'][2]->code)->toBe('329');
    });

    it('throws when the API is unreachable', function () {
        $client = mockFipeClient([
            new RequestException('cURL error 6: Could not resolve host', new Request('POST', 'test')),
        ]);

        expect(fn () => $client->referenceTable()->all())
            ->toThrow(FipeReferenceTableException::class, '[Fipe Reference Table Failed]');
    });

    it('throws notFound when no reference tables are returned', function () {
        $client = mockFipeClient([new Response(200, [], json_encode(['codigo' => '0']))]);

        expect(fn () => $client->referenceTable()->all())
            ->toThrow(FipeReferenceTableException::class, 'Reference table not found');
    });
});
