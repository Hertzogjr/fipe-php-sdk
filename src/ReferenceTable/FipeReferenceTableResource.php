<?php

namespace Hertzogjr\FipePhpSdk\ReferenceTable;

use GuzzleHttp\Client;
use Hertzogjr\FipePhpSdk\ReferenceTable\Entities\FipeReferenceTableEntity;

final class FipeReferenceTableResource
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
    public function all(): array
    {
        try {
            $response = $this->client->post(self::REFERENCE_TABLE_URI);
        } catch (\Exception $e) {
            throw FipeReferenceTableException::fetchFailed($e);
        }

        $responsePayload = json_decode($response->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);

        if ((string) ($responsePayload['codigo'] ?? null) === '0') {
            throw FipeReferenceTableException::notFound();
        }

        $referenceTables = array_map(
            static fn (array $referenceTable) => FipeReferenceTableEntity::fromArray($referenceTable),
            $responsePayload
        );

        return [
            'data' => $referenceTables,
        ];
    }
}
