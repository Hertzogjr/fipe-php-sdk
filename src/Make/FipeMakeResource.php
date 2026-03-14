<?php

namespace Hertzogjr\FipePhpSdk\Make;

use GuzzleHttp\Client;
use Hertzogjr\FipePhpSdk\Make\DTOs\MakesByVehicleTypeDTO;
use Hertzogjr\FipePhpSdk\Make\Entities\FipeMakeEntity;

final class FipeMakeResource
{
    const string MAKES_URI = 'https://veiculos.fipe.org.br/api/veiculos/ConsultarMarcas';

    public function __construct(
        public Client $client
    ) {}

    /**
     * @return array{data: FipeMakeEntity[]}
     *
     * @throws FipeMakeException
     */
    public function byVehicleType(MakesByVehicleTypeDTO $makeByVehicleTypeDTO)
    {
        try {
            $response = $this->client->post(self::MAKES_URI, [
                'form_params' => [
                    'codigoTipoVeiculo' => $makeByVehicleTypeDTO->fipeVehicleType->value,
                    'codigoTabelaReferencia' => $makeByVehicleTypeDTO->referenceTableCode,
                ],
            ]);
        } catch (\Exception $e) {
            throw FipeMakeException::fetchFailed($e);
        }

        $responsePayload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if (($responsePayload['codigo'] ?? null) === '2') {
            throw FipeMakeException::invalidParameters();
        }

        if ((string) ($responsePayload['codigo'] ?? null) === '0') {
            throw FipeMakeException::notFound();
        }

        $makes = array_map(
            static fn (array $make) => FipeMakeEntity::fromArray($make),
            $responsePayload
        );

        return [
            'data' => $makes,
        ];
    }
}
