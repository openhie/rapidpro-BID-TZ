<?php
$contact_details=strtolower($_REQUEST["contact_details"]);
$contact_details=str_replace("delete","",$contact_details);
$phone=trim($contact_details);
$phone=str_replace("+255","",$phone);
$phone=trim($phone);
$phone_back=explode(" ",$phone);

if(count($phone_back)>1 or $phone_back=="") {
header('Content-Type: application/json');
echo '{"response":"Error:Invalid Delete Format.The format Should Be delete 07XXXXXXXX"}';
return false;
}
if(strlen($phone)!=10) {
header('Content-Type: application/json');
echo '{"response":"Error:Invalid Phone Number.The format Should Be delete 07XXXXXXXX"}';
return false;
}
//remove the first 0 and replace with %
$phone[0]='5';
$phone='%2B25'.$phone;
//get contact details
$url = "http://54.148.103.198:8001/api/v1/contacts.json?phone=$phone";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_HTTPHEADER, Array(
                                                   "Content-Type: application/json",
                                                   "Authorization: Token b9d090b6890b92bf43d254536c2b1a29308f32d4",
                                          ));
$output = curl_exec ($ch);
$results=json_decode($output,true);
$uuid=$results["results"][0]["uuid"];
if($uuid=="") {
echo '{"response":"Error:Phone Number Not Found"}';
return false;
}
//delete the contact
$url=$url = "http://54.148.103.198:8001/api/v1/contacts.json?uuid=$uuid";
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
$output = curl_exec ($ch);
if(curl_errno($ch))
echo '{"response":"An Error Occured While Deleting Contact,Try Later"}';
else
echo '{"response":"Contact Deleted Successfully"}';
curl_close ($ch);
?>
