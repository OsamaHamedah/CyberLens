<?php

error_reporting(0);
ini_set('display_errors', 0);

$cacheFile= '../cache/threats.json';
$cacheTime= 3600;

if (!is_dir('../cache')) {
    mkdir('../cache', 0777, true);
}
$data= [];

if (file_exists($cacheFile) && (time()- filemtime($cacheFile)< $cacheTime)) {
    $raw_data = file_get_contents($cacheFile);
    $data= json_decode($raw_data, true);
}
else{
    $api_url= "https://cve.circl.lu/api/last";

    $ch= curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'CyberLens_StudentProject');

    $response = curl_exec($ch);
    $httpCode= curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($httpCode==200 && $response){
        file_put_contents($cacheFile, $response);
        $data= json_decode($response, true);
    }
    /*else{
        if(file_exists($cacheFile)){
            $data= json_decode(file_get_contents($cacheFile), true);
        }
        else{
            $data=[];
        }
    }*/
}
$clean_feed=[];

if(is_array($data)) {
    $sliced_data = array_slice($data, 0, 5);
    foreach ($sliced_data as $item) {
        $id = 'Unknown ID';
        $date = 'Recent';
        $summary = 'No details available.';

        if (isset($item['cveMetadata']['cveId'])) {
            $id = $item['cveMetadata']['cveId'];
            // Extract Date
            if (isset($item['cveMetadata']['datePublished'])) {
                $date = substr($item['cveMetadata']['datePublished'], 0, 10);
            }
            // Extract Summary (It's deep inside the structure)
            if (isset($item['containers']['cna']['descriptions'][0]['value'])) {
                $summary = $item['containers']['cna']['descriptions'][0]['value'];
            }
        }
        elseif (isset($item['id'])) {
            $id = $item['id'];

            if(isset($item['aliases'][0])) {
                $id = $item['aliases'][0];
            }

            if (isset($item['published'])) {
                $date = substr($item['published'], 0, 10);

            }

            if (isset($item['summary'])) {
                $summary = $item['summary'];
            }
            elseif (isset($item['details'])) {
                $summary = $item['details'];
            }
        }

        $clean_feed[] = [
            'id' => $id,
            'Modified' => $date,
            'summary' => $summary
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($clean_feed);
//$top5= is_array($data) ? array_slice($data, 0, 5) : [];