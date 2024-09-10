<?php

/**
 * This example shows how to enable a feature for a certain key and to check if it actually has been enabled.
 * To create a sample key, you can simply access this example file within your browser adding parameter "?keyCreate"
 * 
 * 
 * In this scenario we will try to figure out if our customer has a certain feature (in this example feature 1) enabled, which we will refer on as "pro" 
 */


require_once "../loader.php";

use Cryptolens_PHP_Client\Cryptolens;
use Cryptolens_PHP_Client\Key;

$c = new Cryptolens("YOUR_TOKEN", 12345, Cryptolens::CRYPTOLENS_OUTPUT_PHP);
$k = new Key($c);

function check_pro_enabled($key, $k){
    $key = $k->get_key($key);

    if($key != false){
        $key = json_decode($key["licenseKey"], true);
        if($key["F1"] == true){
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

if($_GET["action"] == "checkPro"){
    if(isset($_GET["key"])){
        if(check_pro_enabled($_GET["key"], $k)){
            echo "Feature 1 enabled for key " . $_GET["key"];
        } else {
            echo "Failed to check for enabled Feature 1 on key " . $_GET["key"];
        }
    }
}


if($_GET["action"] == "exampleCheckPro"){
    if(check_pro_enabled("EXAMPLE_KEY", $k)){
        echo "Feature 1 enabled for key " . "EXAMPLE_KEY";
    } else {
        echo "Failed to check for enabled Feature 1 on key " . "EXAMPLE_KEY";
    }
}
