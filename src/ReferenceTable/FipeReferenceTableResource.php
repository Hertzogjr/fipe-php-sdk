<?php

namespace Junior\FipePhpSdk\FipeReferenceTable;

use GuzzleHttp\Client;
use Junior\FipePhpSdk\FipeReferenceTable\Entities\FipeReferenceTableEntity;

class FipeReferenceTableResource
{
    public const string REFERENCE_TABLE_URI = 'https://veiculos.fipe.org.br/api/veiculos/ConsultarTabelaDeReferencia';
    public function __construct(
        public Client $client,
    ) {}

    /**
     * Get the reference tables from the FIPE API.
     *
     * @return array{data: FipeReferenceTableEntity[]}
     *
     * @throws FipeReferenceTableException
     */
    public function getReferenceTables(): array
    {
        try{
            $response = $this->client->post(self::REFERENCE_TABLE_URI);
        } catch (\Exception $e) {
            throw FipeReferenceTableException::fetchFailed($e);
        }

        $responsePayload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        $referenceTables = array_map(
            static fn (array $referenceTable) => FipeReferenceTableEntity::fromArray($referenceTable),
            $responsePayload
        );

        return [
            'data' => $referenceTables,
        ];
    }

}