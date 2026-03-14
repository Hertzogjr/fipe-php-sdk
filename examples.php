<?php

require_once __DIR__.'/vendor/autoload.php';

use Hertzogjr\FipePhpSdk\FipeClient;
use Hertzogjr\FipePhpSdk\Make\DTOs\MakesByVehicleTypeDTO;
use Hertzogjr\FipePhpSdk\Model\DTOs\ModelsByMakePayloadDTO;
use Hertzogjr\FipePhpSdk\Model\DTOs\ModelsByYearPayloadDTO;
use Hertzogjr\FipePhpSdk\Model\DTOs\ModelYearsPayloadDTO;
use Hertzogjr\FipePhpSdk\Vehicle\DTOs\VehiclePayloadDTO;
use Hertzogjr\FipePhpSdk\Vehicle\Enums\FipeVehicleTypeEnum;

$client = new FipeClient;

// -------------------------------------------------------------------------
// 1. Reference Tables
//    Fetch the list of monthly reference tables available in the FIPE API.
// -------------------------------------------------------------------------
$referenceTables = $client->referenceTable()->all();

// $referenceTables['data'] is an array of FipeReferenceTableEntity
$latestTable = $referenceTables['data'][0];
$referenceCode = $latestTable->code; // e.g. "319"

echo "Latest reference table: {$latestTable->month} (code: {$referenceCode})\n";

// -------------------------------------------------------------------------
// 2. Makes
//    Fetch car makes available for a given reference table.
// -------------------------------------------------------------------------
$makes = $client->make()->byVehicleType(
    new MakesByVehicleTypeDTO(
        fipeVehicleType: FipeVehicleTypeEnum::CAR,
        referenceTableCode: $referenceCode,
    )
);

// $makes['data'] is an array of FipeMakeEntity
$firstMake = $makes['data'][0];
$makeCode = $firstMake->value; // e.g. "1"

echo "First make: {$firstMake->label} (code: {$makeCode})\n";

// -------------------------------------------------------------------------
// 3. Models by make
//    Fetch all models and available years for a given make.
// -------------------------------------------------------------------------
$models = $client->model()->all(
    new ModelsByMakePayloadDTO(
        makeCode: $makeCode,
        referenceCode: $referenceCode,
        vehicleType: FipeVehicleTypeEnum::CAR,
    )
);

// $models['data']['Modelos'] → FipeModelEntity[]
// $models['data']['Anos']    → FipeYearEntity[]
$firstModel = $models['data']['Modelos'][0];
$firstYear = $models['data']['Anos'][0];
$modelCode = $firstModel->value; // e.g. "5941"
$yearCode = $firstYear->value;  // e.g. "2021-1" (year-fuelCode)

echo "First model: {$firstModel->label} (code: {$modelCode})\n";
echo "First year: {$firstYear->label} (code: {$yearCode})\n";

// -------------------------------------------------------------------------
// 4. Model years
//    Fetch the available years for a specific model.
// -------------------------------------------------------------------------
$years = $client->model()->years(
    new ModelYearsPayloadDTO(
        makeCode: $makeCode,
        referenceCode: $referenceCode,
        vehicleType: FipeVehicleTypeEnum::CAR,
        modelCode: $modelCode,
    )
);

// $years['data'] is an array of FipeYearEntity
// Use the first year from this model-specific list for subsequent calls
$yearCode = $years['data'][0]->value;
foreach ($years['data'] as $year) {
    echo "Year: {$year->label} (code: {$year->value})\n";
}

// -------------------------------------------------------------------------
// 5. Models by year
//    Fetch all models of a make filtered by a specific year/fuel combination.
// -------------------------------------------------------------------------
$modelsByYear = $client->model()->byYear(
    new ModelsByYearPayloadDTO(
        makeCode: $makeCode,
        referenceCode: $referenceCode,
        vehicleType: FipeVehicleTypeEnum::CAR,
        yearCode: $yearCode,
    )
);

// $modelsByYear['data'] is an array of FipeModelEntity
echo "Models for year {$yearCode}: ".count($modelsByYear['data'])."\n";

// -------------------------------------------------------------------------
// 6. Vehicle price
//    Fetch the full FIPE pricing data for a specific vehicle.
// -------------------------------------------------------------------------
$vehicle = $client->vehicle()->get(
    new VehiclePayloadDTO(
        referenceCode: $referenceCode,
        makeCode: $makeCode,
        modelCode: $modelCode,
        vehicleType: FipeVehicleTypeEnum::CAR,
        yearCode: $yearCode, // format: "2021-1"
    )
);

// $vehicle['data'] is a FipeVehicleEntity
$v = $vehicle['data'];
echo "Vehicle: {$v->make} {$v->model} {$v->modelYear}\n";
echo "FIPE code: {$v->fipeCode}\n";
echo "Price: {$v->value}\n";
echo "Fuel: {$v->fuel}\n";
echo "Reference month: {$v->referenceMonth}\n";
