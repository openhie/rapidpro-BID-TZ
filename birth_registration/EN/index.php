<?php
error_log(print_r($_REQUEST,true));
$success = false;
$_REQUEST["birth_details"]=strtolower($_REQUEST["birth_details"]);
$_REQUEST["birth_details"]=str_replace("birth","",$_REQUEST["birth_details"]);
$_REQUEST["birth_details"]=trim($_REQUEST["birth_details"]);
//check if this request is comming from the app
$birth_details=$_REQUEST["birth_details"];
$birth_details=explode(" ",$birth_details);
$k=0;
foreach($birth_details as $birth) {
        $k++;
        if(substr($birth,0,2)=="tz" and ($k==count($birth_details) or $k==(count($birth_details)-1))) {
                $_REQUEST["orgcode"]=strtoupper($birth);
                $_REQUEST["weight"]=$birth_details[count($birth_details)-1];
                unset($birth_details[count($birth_details)-1]);
                unset($birth_details[count($birth_details)-1]);
                $birth_details=implode(" ",$birth_details);
                $_REQUEST["birth_details"]=$birth_details;
                break;
                }
        }
//end of checking requests comming from the app
$client_uuid = uuid_create();
require_once('config.php');
require_once('lib/PixSubmitNewBirthRegistration.php');
if (! isset($pix_url)) {
    error_log( "Config \$pix_url not set in config.php");
echo "Config \$pix_url not set in config.php";
} else if (! isset($pix_client_id)) {
    error_log("Config \$pix_client_id not set in config.php");
echo "Config \$pix_client_id not set in config.php";
} else {
    $pix = new PixSumbitNewBirthRegistration($pix_url,$pix_client_id,$client_uuid);
    $pix->cert_private_key = 'privateKey.pem';
    $pix->cert_client = 'clientCert.pem';
    $pix->pass = 'client';
    $pix->ua ='Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.A.B.C Safari/525.13';

    $submission = $pix->create_submission($_REQUEST);

    if ($submission) {
        error_log( "Sending $submission");
        if (($pix->send($submission))) {
            error_log( "Sent:\n$submission\n{$pix->output}");
        } else {
            error_log( "Could not send:\n$submission");
        }
        if (strpos($pix->output,'acceptAckCode') > 0) {
            $success= true;
        }
    } else {
        error_log( "Invalid submission");
    }

} 

header('Content-Type: application/json');
if ($success) {
    echo '{ 
		"response":"Birth For '.$_REQUEST["birth_details"].' From '.$_REQUEST["orgname"].' Village, Succesfully Registered",
		"error":" ",
		"client_uuid":"'.$client_uuid.'"
	  }'; 
}

