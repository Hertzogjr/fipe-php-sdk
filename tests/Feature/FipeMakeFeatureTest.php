<?php

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Hertzogjr\FipePhpSdk\Make\DTOs\MakesByVehicleTypeDTO;
use Hertzogjr\FipePhpSdk\Make\Entities\FipeMakeEntity;
use Hertzogjr\FipePhpSdk\Make\FipeMakeException;
use Hertzogjr\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

describe('Makes via FipeClient', function () {
    beforeEach(function () {
        $this->dto = new MakesByVehicleTypeDTO(
            fipeVehicleType: FipeVehicleTypeEnum::CAR,
            referenceTableCode: '331',
        );
    });

    it('returns car makes for a given reference table', function () {
        $payload = [
            ['Label' => 'Acura', 'Value' => '1'],
            ['Label' => 'Agrale', 'Value' => '2'],
            ['Label' => 'Alfa Romeo', 'Value' => '3'],
        ];

        $client = mockFipeClient([new Response(200, [], json_encode($payload))]);

        $result = $client->make()->byVehicleType($this->dto);

        expect($result)->toHaveKey('data')
            ->and($result['data'])->toHaveCount(3)
            ->and($result['data'][0])->toBeInstanceOf(FipeMakeEntity::class)
            ->and($result['data'][0]->label)->toBe('Acura')
            ->and($result['data'][0]->value)->toBe('1');
    });

    it('returns motorcycle makes', function () {
        $dto = new MakesByVehicleTypeDTO(
            fipeVehicleType: FipeVehicleTypeEnum::MOTORCYCLE,
            referenceTableCode: '331',
        );

        $payload = [
            ['Label' => 'Honda', 'Value' => '15'],
            ['Label' => 'Yamaha', 'Value' => '25'],
        ];

        $client = mockFipeClient([new Response(200, [], json_encode($payload))]);

        $result = $client->make()->byVehicleType($dto);

        expect($result['data'])->toHaveCount(2)
            ->and($result['data'][1]->label)->toBe('Yamaha');
    });

    it('throws when the API is unreachable', function () {
        $client = mockFipeClient([
            new RequestException('cURL error 6: Could not resolve host', new Request('POST', 'test')),
        ]);

        expect(fn () => $client->make()->byVehicleType($this->dto))
            ->toThrow(FipeMakeException::class, '[Fipe Make Failed]');
    });

    it('throws invalidParameters when the vehicle type or reference code is invalid', function () {
        $client = mockFipeClient([new Response(200, [], json_encode(['codigo' => '2']))]);

        expect(fn () => $client->make()->byVehicleType($this->dto))
            ->toThrow(FipeMakeException::class, 'Invalid parameters provided');
    });

    it('throws notFound when no makes exist for the given parameters', function () {
        $client = mockFipeClient([new Response(200, [], json_encode(['codigo' => '0']))]);

        expect(fn () => $client->make()->byVehicleType($this->dto))
            ->toThrow(FipeMakeException::class, 'Make not found');
    });
});
