<?php

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Hertzogjr\FipePhpSdk\Make\DTOs\MakesByVehicleTypeDTO;
use Hertzogjr\FipePhpSdk\Make\Entities\FipeMakeEntity;
use Hertzogjr\FipePhpSdk\Make\FipeMakeException;
use Hertzogjr\FipePhpSdk\Make\FipeMakeResource;
use Hertzogjr\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

describe('FipeMakeResource', function () {
    beforeEach(function () {
        $this->dto = new MakesByVehicleTypeDTO(
            fipeVehicleType: FipeVehicleTypeEnum::CAR,
            referenceTableCode: '331',
        );
    });

    it('returns FipeMakeEntity array on success', function () {
        $data = [
            ['Label' => 'Volkswagen', 'Value' => '59'],
            ['Label' => 'Fiat', 'Value' => '21'],
        ];

        $resource = new FipeMakeResource(
            mockClient([new Response(200, [], json_encode($data))])
        );

        $result = $resource->byVehicleType($this->dto);

        expect($result)->toHaveKey('data')
            ->and($result['data'])->toHaveCount(2)
            ->and($result['data'][0])->toBeInstanceOf(FipeMakeEntity::class)
            ->and($result['data'][0]->label)->toBe('Volkswagen')
            ->and($result['data'][0]->value)->toBe('59');
    });

    it('throws fetchFailed on HTTP error', function () {
        $resource = new FipeMakeResource(
            mockClient([new RequestException('connection error', new Request('POST', 'test'))])
        );

        expect(fn () => $resource->byVehicleType($this->dto))
            ->toThrow(FipeMakeException::class, '[Fipe Make Failed]');
    });

    it('throws invalidParameters when codigo is 2', function () {
        $resource = new FipeMakeResource(
            mockClient([new Response(200, [], json_encode(['codigo' => '2']))])
        );

        expect(fn () => $resource->byVehicleType($this->dto))
            ->toThrow(FipeMakeException::class, 'Invalid parameters provided');
    });

    it('throws notFound when codigo is 0', function () {
        $resource = new FipeMakeResource(
            mockClient([new Response(200, [], json_encode(['codigo' => '0']))])
        );

        expect(fn () => $resource->byVehicleType($this->dto))
            ->toThrow(FipeMakeException::class, 'Make not found');
    });
});
