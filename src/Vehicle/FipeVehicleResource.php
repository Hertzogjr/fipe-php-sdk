<?php

namespace Junior\FipePhpSdk\Vehicle;

use GuzzleHttp\Client;
use Junior\FipePhpSdk\Vehicle\DTOs\VehiclePayloadDTO;
use Junior\FipePhpSdk\Vehicle\Entities\FipeVehicleEntity;

final class FipeVehicleResource
{
    const string VEHICLE_URI = 'https://veiculos.fipe.org.br/api/veiculos/ConsultarValorComTodosParametros';

    public function __construct(
        public readonly Client $client
    ) {}

    /**
     * Get the vehicle information from the FIPE API.
     *
     * @return array{data: FipeVehicleEntity}
     *
     * @throws FipeVehicleException
     */
    public function get(VehiclePayloadDTO $vehiclePayload): array
    {
        try {
            $response = $this->client->post(self::VEHICLE_URI, [
                'form_params' => [
                    'codigoTabelaReferencia' => $vehiclePayload->referenceCode,
                    'codigoMarca' => $vehiclePayload->makeCode,
                    'codigoModelo' => $vehiclePayload->modelCode,
                    'codigoTipoVeiculo' => $vehiclePayload->vehicleType->value,
                    'anoModelo' => $vehiclePayload->modelYear,
                    'codigoTipoCombustivel' => $vehiclePayload->fuelCode,
                    'tipoVeiculo' => $vehiclePayload->vehicleType->label(),
                    'tipoConsulta' => 'tradicional',
                ],
            ]);
        } catch (\Exception $e) {
            throw FipeVehicleException::fetchFailed($e);
        }

        $responsePayload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (($responsePayload['codigo'] ?? null) === '2') {
            throw FipeVehicleException::invalidParameters();
        }

        if ((string) ($responsePayload['codigo'] ?? null) === '0') {
            throw FipeVehicleException::notFound();
        }

        return [
            'data' => FipeVehicleEntity::fromArray($responsePayload),
        ];
    }
}
