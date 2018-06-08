<?php
// Adjust memory limit and timeout
ini_set('memory_limit', '-1');
set_time_limit(0);

// Set Parameters: Node Domain, Start Date, and End Date
#$nodeDomain = "node01.public.learningregistry.net";
#$nodeDomain = "node02.public.learningregistry.net";
$nodeDomain = "goopen.public.learningregistry.net";
$startDate = "2018-01-01";
$endDate = "2018-05-31";
$file_name = $nodeDomain . '-' . $startDate . '-' . $endDate . '.csv';
$log_file = $nodeDomain . '-' . $startDate . '-' . $endDate . '.log';

file_put_contents($log_file, "--- Starting ".$nodeDomain." from ".$startDate." to ".$endDate ."\n", FILE_APPEND | LOCK_EX);

// Compose lr slice url based on above parameters
if (is_null($startDate))
    $startDate = date('Y-m-d');

if (is_null($endDate))
    $endDate = date('Y-m-d');

$lrUrl = "https://".$nodeDomain."/slice?from=".$startDate."&until=".$endDate;

$signers = array();
$submitters = array();
$owners = array();

do {

    file_put_contents($log_file, "Getting  ".$lrUrl."\n" , FILE_APPEND | LOCK_EX);

    // Get LR Resources based on initial slice URL
    $resources = file_get_contents($lrUrl);
    $resources = json_decode($resources);

    // Exit loop if no resources returned
    if (empty($resources->documents))
        break;

    // get publishers according to signers, submitters, and owners
    $publishers = getPublishers($resources->documents);
    if ($publishers){
        $signers = array_merge($signers,$publishers['signers']);
        $submitters = array_merge($submitters,$publishers['submitters']);
        $owners = array_merge($owners,$publishers['owners']);
    }

    // get resumption token for next batch of resources
    $resume_token = null;
    if (!empty($resources->resumption_token))
        $resume_token = $resources->resumption_token;

    $lrUrl = "https://".$nodeDomain."/slice?from=".$startDate."&until=".$endDate."&resumption_token=".$resume_token;

} while(!empty($resources->resumption_token));


$lists = array();

//default header column name
$lists[] = array("type","name","count");

$signers = getArrayCount($signers);
$signers = getCount($signers,"signer");
$lists = array_merge($lists,$signers);

$submitters = getArrayCount($submitters);
$submitters = getCount($submitters, "submitter");
$lists = array_merge($lists,$submitters);

$owners = getArrayCount($owners);
$owners = getCount($owners, "owner");
$lists = array_merge($lists,$owners);

// Save list of publishers into CSV
$download_url = exportPublisherstoCSV($lists, $file_name);

echo "Completed: <a href='" . $file_name . "'>" . $file_name . "</a>" ;

// Get Publishers and segregate it to signers, submitters, and owners
function getPublishers($resources){
    $publishers = array();
    $signers = array();
    $submitters = array();
    $owners = array();
    $ids = array();
    foreach ($resources as $resource){
        // don't count deleted resources
        if ($resource->resource_data_description->doc_type == "resource_data" && $resource->resource_data_description->payload_placement != "none") {
            $ids[] = $resource->resource_data_description->doc_ID;
            if (isset($resource->resource_data_description->identity->signer))
                $signers[] = $resource->resource_data_description->identity->signer;
            if (isset($resource->resource_data_description->identity->submitter))
                $submitters[] = $resource->resource_data_description->identity->submitter;
            if (isset($resource->resource_data_description->identity->owner))
                $owners[] = $resource->resource_data_description->identity->owner;
        }
    }
    $publishers = array("ids"=> $ids, "signers"=> $signers, "submitters" => $submitters, "owners" => $owners);
    return $publishers;
}

// Get Array Count Grouped By Signer, Submitter, Or Owner
function getArrayCount($arr){
    $arrs = array_count_values($arr);
    return $arrs;
}

// Display count as per key value pair
function displayCount($arr){
    foreach($arr as $key=>$value){
        echo $key . " (" . $value . ")<br/>";
    }
}

// Get count as per key value pair
function getCount($arr, $type){
    $stats = array();
    foreach($arr as $key=>$value){
        $stats[] = array($type,$key,$value);
    }
    return $stats;
}

// export Publishers information
function exportPublisherstoCSV($publishers, $file_name){
    #$cur_date = date("Y-m-d-h-i-s");
    $fp = fopen(dirname(__FILE__)."/".$file_name, "a+");
    $dl_url = dirname($_SERVER['PHP_SELF']);

    foreach($publishers as $publisher){
        fputcsv($fp, $publisher, ',', '"', "\0");
    }
    fclose($fp);

    $host = $_SERVER['SERVER_NAME'];
    $port = $_SERVER['SERVER_PORT'];
    $port = ( ($port=='80' ) || ($port=='443' ) ) ? '' : ':'.$port;
    $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME']
            : ('http'. (($_SERVER['SERVER_PORT'] == '443') ? 's' : ''));

    return sprintf('%s://%s%s%s/%s', $scheme, $host, $port, dirname($_SERVER['PHP_SELF']),$file_name);
}

// this was used to escape quotes in fputcsv function, but i cant seem to find any publishers with quotes
// if so, use  -- fputcsv($fp, array_map(encodeColumn, $publisher), ',', chr(0));
function encodeColumn($value) {
    return "\"$value\"";
}
?>
