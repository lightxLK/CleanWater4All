<?php
require 'vendor/autoload.php';

use Ibm\WatsonxAi\WatsonxAiV1;
use Ibm\WatsonxAi\WatsonxAiV1Models\GenerateParams;

header('Content-Type: application/json');

function loadConfig() {
    $config = parse_ini_file('config.ini');
    if ($config === false) {
        throw new Exception("Error reading configuration file");
    }
    return $config;
}

function predictWithWatsonx($data) {
    // Try to load from environment variables first
    $apiKey = getenv('WATSONX_API_KEY');
    $deploymentId = getenv('WATSONX_DEPLOYMENT_ID');
    $spaceGuid = getenv('WATSONX_SPACE_GUID');

    // If not found in environment variables, try loading from config file
    if (!$apiKey || !$deploymentId || !$spaceGuid) {
        $config = loadConfig();
        $apiKey = $config['WATSONX_API_KEY'];
        $deploymentId = $config['WATSONX_DEPLOYMENT_ID'];
        $spaceGuid = $config['WATSONX_SPACE_GUID'];
    }

    if (!$apiKey || !$deploymentId || !$spaceGuid) {
        throw new Exception("Missing required Watsonx credentials");
    }

    $watsonx = new WatsonxAiV1([
        'version' => '2023-05-29',
        'apikey' => $apiKey,
        'url' => 'https://us-south.ml.cloud.ibm.com', // Adjust if your region is different
    ]);

    $generateParams = new GenerateParams([
        'model_id' => $deploymentId,
        'input' => json_encode($data),
        'parameters' => [
            'temperature' => 0.7,
            'max_new_tokens' => 100,
        ],
        'project_id' => $spaceGuid,
    ]);

    $result = $watsonx->generate($generateParams);
    
    // Parse the result and return the prediction
    // This will depend on how your model returns results
    $prediction = json_decode($result->getResult(), true);
    
    return $prediction['potability'];  // Adjust based on your model's output
}

// Rest of the code remains the same