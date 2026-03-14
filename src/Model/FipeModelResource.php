<?php

namespace Junior\FipePhpSdk\Model;

use GuzzleHttp\Client;
use Junior\FipePhpSdk\Model\DTOs\ModelsByMakePayloadDTO;
use Junior\FipePhpSdk\Model\DTOs\ModelsByYearPayloadDTO;
use Junior\FipePhpSdk\Model\DTOs\ModelYearsPayloadDTO;
use Junior\FipePhpSdk\Model\Entities\FipeModelEntity;
use Junior\FipePhpSdk\Model\Entities\FipeYearEntity;

final class FipeModelResource
{
    const string MODELS_URI = 'https://veiculos.fipe.org.br/api/veiculos/ConsultarModelos';

    const string MODELS_BY_YEAR_URI = 'https://veiculos.fipe.org.br/api/veiculos/ConsultarModelosAtravesDoAno';

    const string MODEL_YEARS_URI = 'https://veiculos.fipe.org.br/api/veiculos/ConsultarAnoModelo';

    public function __construct(
        public readonly Client $client,
    ) {}

    /**
     * Get the models of a make by its reference table, vehicle type and make code.
     *
     * @return array{data: array{Modelos: FipeModelEntity[], Anos: FipeYearEntity[]}}
     *
     * @throws FipeModelException
     */
    public function all(ModelsByMakePayloadDTO $payload): array
    {
        try {
            $response = $this->client->post(self::MODELS_URI, [
                'form_params' => [
                    'codigoTipoVeiculo' => $payload->vehicleType->value,
                    'codigoTabelaReferencia' => $payload->referenceCode,
                    'codigoMarca' => $payload->makeCode,
                ],
            ]);
        } catch (\Exception $e) {
            throw FipeModelException::fetchFailed($e);
        }

        $responsePayload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (($responsePayload['codigo'] ?? null) === '2') {
            throw FipeModelException::invalidParameters();
        }

        if ((string) ($responsePayload['codigo'] ?? null) === '0') {
            throw FipeModelException::notFound();
        }

        $models = array_map(
            static fn (array $referenceTable) => FipeModelEntity::fromArray($referenceTable),
            $responsePayload['Modelos']
        );

        $years = array_map(
            static fn (array $referenceTable) => FipeYearEntity::fromArray($referenceTable),
            $responsePayload['Anos']
        );

        return [
            'data' => [
                'Modelos' => $models,
                'Anos' => $years,
            ],
        ];
    }

    /**
     * Get the models of a make by its reference table, vehicle type, fuel type and year.
     *
     * @return array{data: FipeModelEntity[]}
     *
     * @throws FipeModelException
     */
    public function byYear(ModelsByYearPayloadDTO $payload): array
    {
        try {
            $response = $this->client->post(self::MODELS_BY_YEAR_URI, [
                'form_params' => [
                    'codigoTipoVeiculo' => $payload->vehicleType->value,
                    'codigoTabelaReferencia' => $payload->referenceCode,
                    'codigoMarca' => $payload->makeCode,
                    'ano' => $payload->yearCode,
                    'codigoTipoCombustivel' => $payload->fuelTypeCode,
                    'anoModelo' => $payload->modelYear,
                ],
            ]);
        } catch (\Exception $e) {
            throw FipeModelException::fetchFailed($e);
        }

        $responsePayload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (($responsePayload['codigo'] ?? null) === '2') {
            throw FipeModelException::invalidParameters();
        }

        if ((string) ($responsePayload['codigo'] ?? null) === '0') {
            throw FipeModelException::notFound();
        }

        $models = array_map(
            static fn (array $referenceTable) => FipeModelEntity::fromArray($referenceTable),
            $responsePayload
        );

        return [
            'data' => $models,
        ];
    }

    /**
     * Get the years of a model by its make, reference table and vehicle type.
     *
     * @return array{data: FipeYearEntity[]}
     *
     * @throws FipeModelException
     */
    public function years(ModelYearsPayloadDTO $payload): array
    {
        try {
            $response = $this->client->post(self::MODEL_YEARS_URI, [
                'form_params' => [
                    'codigoTipoVeiculo' => $payload->vehicleType->value,
                    'codigoTabelaReferencia' => $payload->referenceCode,
                    'codigoMarca' => $payload->makeCode,
                    'codigoModelo' => $payload->modelCode,
                ],
            ]);
        } catch (\Exception $e) {
            throw FipeModelException::fetchFailed($e);
        }

        $responsePayload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (($responsePayload['codigo'] ?? null) === '2') {
            throw FipeModelException::invalidParameters();
        }

        if ((string) ($responsePayload['codigo'] ?? null) === '0') {
            throw FipeModelException::notFound();
        }

        $years = array_map(
            static fn (array $model) => FipeYearEntity::fromArray($model),
            $responsePayload
        );

        return [
            'data' => $years,
        ];
    }
}
