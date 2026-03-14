# FIPE PHP SDK

SDK não oficial para a [API FIPE](https://veiculos.fipe.org.br/api/veiculos/), que fornece a tabela de preços de referência de veículos do mercado brasileiro.

## Considerações importantes

> **A FIPE não disponibiliza uma API pública oficial.** O endpoint utilizado por este SDK é o mesmo consumido pelo site da FIPE internamente, sem qualquer garantia de estabilidade, autenticação ou termos de uso formais. Utilize com responsabilidade.

- **Limite de requisições:** O servidor da FIPE bloqueia IPs que realizam muitas requisições em um curto período de tempo. Se você pretende fazer um volume grande de consultas, considere implementar um proxy ou cache para distribuir e reduzir as chamadas.
- **Sem SLA:** Como não é uma API pública, a FIPE pode alterar ou remover os endpoints a qualquer momento sem aviso prévio.

## Requisitos

- PHP 8.4+
- Composer

## Instalação

```bash
composer require hertzogjr/fipe-php-sdk
```

## Uso

Todos os recursos são acessados através do `FipeClient`:

```php
use Hertzogjr\\FipePhpSdk\FipeClient;

$client = new FipeClient();
```

---

### Tabelas de Referência

Retorna todas as tabelas de referência mensais disponíveis na API FIPE.

```php
$result = $client->referenceTable()->all();

// $result['data'] → FipeReferenceTableEntity[]
foreach ($result['data'] as $table) {
    echo $table->code;  // ex: "319"
    echo $table->month; // ex: "janeiro/2025"
}
```

---

### Marcas

Retorna as marcas disponíveis para um tipo de veículo e tabela de referência.

```php
use Hertzogjr\\FipePhpSdk\Make\DTOs\MakesByVehicleTypeDTO;
use Hertzogjr\\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

$result = $client->make()->byVehicleType(
    new MakesByVehicleTypeDTO(
        fipeVehicleType: FipeVehicleTypeEnum::CAR,
        referenceTableCode: '319',
    )
);

// $result['data'] → FipeMakeEntity[]
foreach ($result['data'] as $make) {
    echo $make->label; // ex: "Fiat"
    echo $make->value; // ex: "21" (código da marca)
}
```

**Tipos de veículo disponíveis:**

| Enum                          | Descrição  |
|-------------------------------|------------|
| `FipeVehicleTypeEnum::CAR`        | Carro      |
| `FipeVehicleTypeEnum::MOTORCYCLE` | Moto       |
| `FipeVehicleTypeEnum::TRUCK`      | Caminhão   |

---

### Modelos

#### Todos os modelos de uma marca

Retorna os modelos e os anos disponíveis para uma marca.

```php
use Hertzogjr\\FipePhpSdk\Model\DTOs\ModelsByMakePayloadDTO;

$result = $client->model()->all(
    new ModelsByMakePayloadDTO(
        makeCode: '21',
        referenceCode: '319',
        vehicleType: FipeVehicleTypeEnum::CAR,
    )
);

// $result['data']['Modelos'] → FipeModelEntity[]
// $result['data']['Anos']    → FipeYearEntity[]
foreach ($result['data']['Modelos'] as $model) {
    echo $model->label; // ex: "Uno"
    echo $model->value; // ex: "5941"
}
```

#### Anos de um modelo

Retorna os anos disponíveis para um modelo específico.

```php
use Hertzogjr\\FipePhpSdk\Model\DTOs\ModelYearsPayloadDTO;

$result = $client->model()->years(
    new ModelYearsPayloadDTO(
        makeCode: '21',
        referenceCode: '319',
        vehicleType: FipeVehicleTypeEnum::CAR,
        modelCode: '5941',
    )
);

// $result['data'] → FipeYearEntity[]
foreach ($result['data'] as $year) {
    echo $year->label; // ex: "2021 Gasolina"
    echo $year->value; // ex: "2021-1" (anoModelo-codigoCombustivel)
}
```

#### Modelos por ano

Retorna os modelos de uma marca filtrados por um ano e tipo de combustível específicos.

```php
use Hertzogjr\\FipePhpSdk\Model\DTOs\ModelsByYearPayloadDTO;

$result = $client->model()->byYear(
    new ModelsByYearPayloadDTO(
        makeCode: '21',
        referenceCode: '319',
        vehicleType: FipeVehicleTypeEnum::CAR,
        yearCode: '2021-1', // formato: "anoModelo-codigoCombustivel"
    )
);

// $result['data'] → FipeModelEntity[]
```

---

### Veículo

Retorna os dados completos de precificação FIPE para um veículo específico.

```php
use Hertzogjr\\FipePhpSdk\Vehicle\DTOs\VehiclePayloadDTO;

$result = $client->vehicle()->get(
    new VehiclePayloadDTO(
        referenceCode: '319',
        makeCode: '21',
        modelCode: '5941',
        vehicleType: FipeVehicleTypeEnum::CAR,
        yearCode: '2021-1', // formato: "anoModelo-codigoCombustivel"
    )
);

// $result['data'] → FipeVehicleEntity
$vehicle = $result['data'];
echo $vehicle->make;             // ex: "Fiat"
echo $vehicle->model;            // ex: "Uno"
echo $vehicle->modelYear;        // ex: 2021
echo $vehicle->value;            // ex: "R$ 45.123,00"
echo $vehicle->fipeCode;         // ex: "021004-9"
echo $vehicle->fuel;             // ex: "Gasolina"
echo $vehicle->fuelAbbreviation; // ex: "G"
echo $vehicle->referenceMonth;   // ex: "janeiro/2025"
echo $vehicle->consultationDate; // ex: "segunda-feira, 13 de janeiro de 2025 12:34:56"
```

---

### Tratamento de Exceções

Cada domínio possui sua própria exception com construtores nomeados:

```php
use Hertzogjr\\FipePhpSdk\ReferenceTable\FipeReferenceTableException;
use Hertzogjr\\FipePhpSdk\Make\FipeMakeException;
use Hertzogjr\\FipePhpSdk\Model\FipeModelException;
use Hertzogjr\\FipePhpSdk\Vehicle\FipeVehicleException;

try {
    $result = $client->vehicle()->get($payload);
} catch (FipeVehicleException $e) {
    // Lançado em falha de conexão, parâmetros inválidos ou veículo não encontrado
    echo $e->getMessage();
}
```

---

## Desenvolvimento

```bash
composer install       # Instalar dependências
composer run test      # Executar testes com Pest
composer run format    # Formatar código com Laravel Pint
```

## Contribuições

Contribuições são bem-vindas! Sinta-se à vontade para abrir issues, sugerir melhorias ou enviar pull requests.

## Licença

MIT
