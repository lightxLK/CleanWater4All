<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $csvFile = fopen($file['tmp_name'], 'r');
        
        // Skip the header row
        fgetcsv($csvFile);
        
        $totalSamples = 0;
        $potableSamples = 0;
        
        while (($data = fgetcsv($csvFile)) !== FALSE) {
            $totalSamples++;
            if (end($data) == 1) {
                $potableSamples++;
            }
        }
        
        fclose($csvFile);
        
        $nonPotableSamples = $totalSamples - $potableSamples;
        $potabilityPercentage = ($potableSamples / $totalSamples) * 100;
        
        echo json_encode([
            'totalSamples' => $totalSamples,
            'potableSamples' => $potableSamples,
            'nonPotableSamples' => $nonPotableSamples,
            'potabilityPercentage' => $potabilityPercentage
        ]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'File upload failed']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}