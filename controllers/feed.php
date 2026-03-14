<?php

error_reporting(0);
ini_set('display_errors', 0);

$cacheFile= '../cache/threats.json';
$cacheTime= 3600;
$maxCacheSize = 5 * 1024 * 1024; /* Because the threats.json file is getting bigger and bigger
                                    (currently 14.86MB) and errors in the API retrieving
                                    started to occur such as stuck in pending status. */
if (!is_dir('../cache')) {
    mkdir('../cache', 0777, true);
}
$data= [];
$cacheValid = false;

/*if (file_exists($cacheFile) && (time()- filemtime($cacheFile)< $cacheTime))*/
if (file_exists($cacheFile)) {
    $fileSize = filesize($cacheFile);;
    $fileAge = time() - filemtime($cacheFile);
    if ($fileAge < $cacheTime && $fileSize < $maxCacheSize) {
        $rawData = file_get_contents($cacheFile);
        $decoded = json_decode($rawData, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $data= $decoded;
            $cacheValid = true;
        }
    }
}

    if (!$cacheValid) {

    $api_url= "https://cve.circl.lu/api/last";

    $ch= curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'CyberLens_StudentProject');
    curl_setopt($ch, CURLOPT_TIMEOUT, 15); //15 seconds timeout
    // to prevent what I faced recently (hanging on "loading API" expecting it to work)

    $response = curl_exec($ch);
    $httpCode= curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if($httpCode==200 && $response){
        $decoded_new = json_decode($response, true);

        if(json_last_error() === JSON_ERROR_NONE && is_array($decoded_new)){
            file_put_contents($cacheFile, $response);
            $data= $decoded_new;
        }
    }

    elseif (file_exists($cacheFile)){
            $data= json_decode(file_get_contents($cacheFile), true);
        }
}

$clean_feed=[];

if(is_array($data)) {
    $sliced_data = array_slice($data, 0, 5);
    foreach ($sliced_data as $item) {
        $id = 'Unknown ID';
        $date = 'Recent'; //date("Y-m-d");
        $summary = 'No details available.';

       /* if(!empty($item['cveMetadata']['cveId'])) {
            $id = $item['cveMetadata']['cveId'];
        } elseif (!empty($item['id'])) {
            $id = $item['id'];
        }

        if(!empty($item['cveMetadata']['datePublished'])) {
            $date = substr($item['cveMetadata']['datePublished'], 0, 10);
        } elseif (!empty($item['Published'])) {
            $date = substr($item['Published'], 0, 10);
        } elseif (!empty($item['last-modified'])) {
            $date = substr($item['last-modified'], 0, 10);
        }

        if (!empty($item['containers']['cna']['descriptions'][0]['value'])) {
            $summary = $item['containers']['cna']['descriptions'][0]['value'];
        } elseif (!empty($item['summary'])) {
            $summary = $item['summary'];
        } elseif (!empty($item['details'])) {
            $summary = $item['details'];
        } */

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