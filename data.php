<?php
header('Content-Type: application/json');

function readCSV($filename) {
    $file = fopen($filename, 'r');
    $header = fgetcsv($file);
    $data = [];
    while (($row = fgetcsv($file)) !== FALSE) {
        $data[] = array_combine($header, $row);
    }
    fclose($file);
    return $data;
}

$data = readCSV('water_potability.csv');

$sums = [
    'ph' => 0,
    'Hardness' => 0,
    'Solids' => 0,
    'Chloramines' => 0,
    'Sulfate' => 0,
    'Conductivity' => 0,
    'Organic_carbon' => 0,
    'Trihalomethanes' => 0,
    'Turbidity' => 0
];

$count = count($data);

foreach ($data as $row) {
    foreach ($sums as $key => $value) {
        $sums[$key] += floatval($row[$key]);
    }
}

$averages = array_map(function($sum) use ($count) {
    return $sum / $count;
}, $sums);

echo json_encode([
    'ph_avg' => $averages['ph'],
    'hardness_avg' => $averages['Hardness'],
    'solids_avg' => $averages['Solids'],
    'chloramines_avg' => $averages['Chloramines'],
    'sulfate_avg' => $averages['Sulfate'],
    'conductivity_avg' => $averages['Conductivity'],
    'organic_carbon_avg' => $averages['Organic_carbon'],
    'trihalomethanes_avg' => $averages['Trihalomethanes'],
    'turbidity_avg' => $averages['Turbidity']
]);