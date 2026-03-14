<?php

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Hertzogjr\FipePhpSdk\ReferenceTable\Entities\FipeReferenceTableEntity;
use Hertzogjr\FipePhpSdk\ReferenceTable\FipeReferenceTableException;
use Hertzogjr\FipePhpSdk\ReferenceTable\FipeReferenceTableResource;

describe('FipeReferenceTableResource', function () {
    it('returns FipeReferenceTableEntity array on success', function () {
        $data = [
            ['Codigo' => '331', 'Mes' => 'janeiro/2024'],
            ['Codigo' => '330', 'Mes' => 'dezembro/2023'],
        ];

        $resource = new FipeReferenceTableResource(
            mockClient([new Response(200, [], json_encode($data))])
        );

        $result = $resource->all();

        expect($result)->toHaveKey('data')
            ->and($result['data'])->toHaveCount(2)
            ->and($result['data'][0])->toBeInstanceOf(FipeReferenceTableEntity::class)
            ->and($result['data'][0]->code)->toBe('331')
            ->and($result['data'][1]->code)->toBe('330');
    });

    it('throws fetchFailed on HTTP error', function () {
        $resource = new FipeReferenceTableResource(
            mockClient([new RequestException('connection error', new Request('POST', 'test'))])
        );

        expect(fn () => $resource->all())
            ->toThrow(FipeReferenceTableException::class, '[Fipe Reference Table Failed]');
    });

    it('throws notFound when codigo is 0', function () {
        $resource = new FipeReferenceTableResource(
            mockClient([new Response(200, [], json_encode(['codigo' => '0']))])
        );

        expect(fn () => $resource->all())
            ->toThrow(FipeReferenceTableException::class, 'Reference table not found');
    });
});
