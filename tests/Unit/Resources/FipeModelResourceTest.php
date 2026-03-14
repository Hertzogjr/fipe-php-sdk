<?php

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Hertzogjr\FipePhpSdk\Model\DTOs\ModelsByMakePayloadDTO;
use Hertzogjr\FipePhpSdk\Model\DTOs\ModelsByYearPayloadDTO;
use Hertzogjr\FipePhpSdk\Model\DTOs\ModelYearsPayloadDTO;
use Hertzogjr\FipePhpSdk\Model\Entities\FipeModelEntity;
use Hertzogjr\FipePhpSdk\Model\Entities\FipeYearEntity;
use Hertzogjr\FipePhpSdk\Model\FipeModelException;
use Hertzogjr\FipePhpSdk\Model\FipeModelResource;
use Hertzogjr\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

describe('FipeModelResource::all()', function () {
    beforeEach(function () {
        $this->dto = new ModelsByMakePayloadDTO(
            makeCode: '59',
            referenceCode: '331',
            vehicleType: FipeVehicleTypeEnum::CAR,
        );
    });

    it('returns Modelos and Anos arrays on success', function () {
        $data = [
            'Modelos' => [
                ['Label' => 'Gol', 'Value' => '5941'],
            ],
            'Anos' => [
                ['Label' => '2020 Gasolina', 'Value' => '2020-1'],
            ],
        ];

        $resource = new FipeModelResource(
            mockClient([new Response(200, [], json_encode($data))])
        );

        $result = $resource->all($this->dto);

        expect($result)->toHaveKey('data')
            ->and($result['data'])->toHaveKeys(['Modelos', 'Anos'])
            ->and($result['data']['Modelos'][0])->toBeInstanceOf(FipeModelEntity::class)
            ->and($result['data']['Modelos'][0]->label)->toBe('Gol')
            ->and($result['data']['Anos'][0])->toBeInstanceOf(FipeYearEntity::class)
            ->and($result['data']['Anos'][0]->value)->toBe('2020-1');
    });

    it('throws fetchFailed on HTTP error', function () {
        $resource = new FipeModelResource(
            mockClient([new RequestException('connection error', new Request('POST', 'test'))])
        );

        expect(fn () => $resource->all($this->dto))
            ->toThrow(FipeModelException::class, '[Fipe Model Failed]');
    });

    it('throws invalidParameters when codigo is 2', function () {
        $resource = new FipeModelResource(
            mockClient([new Response(200, [], json_encode(['codigo' => '2']))])
        );

        expect(fn () => $resource->all($this->dto))
            ->toThrow(FipeModelException::class, 'Invalid parameters provided');
    });

    it('throws notFound when codigo is 0', function () {
        $resource = new FipeModelResource(
            mockClient([new Response(200, [], json_encode(['codigo' => '0']))])
        );

        expect(fn () => $resource->all($this->dto))
            ->toThrow(FipeModelException::class, 'Model not found');
    });
});

describe('FipeModelResource::byYear()', function () {
    beforeEach(function () {
        $this->dto = new ModelsByYearPayloadDTO(
            makeCode: '59',
            referenceCode: '331',
            vehicleType: FipeVehicleTypeEnum::CAR,
            yearCode: '2020-1',
        );
    });

    it('returns model array on success', function () {
        $data = [
            ['Label' => 'Gol', 'Value' => '5941'],
        ];

        $resource = new FipeModelResource(
            mockClient([new Response(200, [], json_encode($data))])
        );

        $result = $resource->byYear($this->dto);

        expect($result)->toHaveKey('data')
            ->and($result['data'][0])->toBeInstanceOf(FipeModelEntity::class)
            ->and($result['data'][0]->label)->toBe('Gol');
    });

    it('throws fetchFailed on HTTP error', function () {
        $resource = new FipeModelResource(
            mockClient([new RequestException('connection error', new Request('POST', 'test'))])
        );

        expect(fn () => $resource->byYear($this->dto))
            ->toThrow(FipeModelException::class, '[Fipe Model Failed]');
    });

    it('throws invalidParameters when codigo is 2', function () {
        $resource = new FipeModelResource(
            mockClient([new Response(200, [], json_encode(['codigo' => '2']))])
        );

        expect(fn () => $resource->byYear($this->dto))
            ->toThrow(FipeModelException::class, 'Invalid parameters provided');
    });

    it('throws notFound when codigo is 0', function () {
        $resource = new FipeModelResource(
            mockClient([new Response(200, [], json_encode(['codigo' => '0']))])
        );

        expect(fn () => $resource->byYear($this->dto))
            ->toThrow(FipeModelException::class, 'Model not found');
    });
});

describe('FipeModelResource::years()', function () {
    beforeEach(function () {
        $this->dto = new ModelYearsPayloadDTO(
            makeCode: '59',
            referenceCode: '331',
            vehicleType: FipeVehicleTypeEnum::CAR,
            modelCode: '5941',
        );
    });

    it('returns year array on success', function () {
        $data = [
            ['Label' => '2020 Gasolina', 'Value' => '2020-1'],
        ];

        $resource = new FipeModelResource(
            mockClient([new Response(200, [], json_encode($data))])
        );

        $result = $resource->years($this->dto);

        expect($result)->toHaveKey('data')
            ->and($result['data'][0])->toBeInstanceOf(FipeYearEntity::class)
            ->and($result['data'][0]->label)->toBe('2020 Gasolina');
    });

    it('throws fetchFailed on HTTP error', function () {
        $resource = new FipeModelResource(
            mockClient([new RequestException('connection error', new Request('POST', 'test'))])
        );

        expect(fn () => $resource->years($this->dto))
            ->toThrow(FipeModelException::class, '[Fipe Model Failed]');
    });

    it('throws invalidParameters when codigo is 2', function () {
        $resource = new FipeModelResource(
            mockClient([new Response(200, [], json_encode(['codigo' => '2']))])
        );

        expect(fn () => $resource->years($this->dto))
            ->toThrow(FipeModelException::class, 'Invalid parameters provided');
    });

    it('throws notFound when codigo is 0', function () {
        $resource = new FipeModelResource(
            mockClient([new Response(200, [], json_encode(['codigo' => '0']))])
        );

        expect(fn () => $resource->years($this->dto))
            ->toThrow(FipeModelException::class, 'Model not found');
    });
});
