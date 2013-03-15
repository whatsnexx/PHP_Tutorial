
<?php

//SEND EVENT EXAMPLE
error_reporting(E_ALL);

/*configuration*/
$api_base_service_url = 'https://localhost/TicketBusService/Rest/TicketBusService.svc/';
 
function getServiceUrl(
				$api_base_service_url,
				$api_method_name,
				$accountid,
				$subject_type_id,
				$term_name,
				$execution_environment,
				$subject_code)  {
	$url = $api_base_service_url.$api_method_name.'/'.$accountid.'/'.$subject_type_id.'/'.$term_name.'/'.$execution_environment.'/'.$subject_code;
	return ($url);
}


//Create service URL
$api_service_uri = getServiceUrl($api_base_service_url,'SendEvent','00000000-0000-0000-0000-000000000001','00000000-0000-0000-0000-000000000001','TestEvent','Stage','testing123');

//Build request attributes
$xml = '<AttributeList xmlns="http://schemas.whatsnexx.com/v1/tbx/">
		<Attribute><Name>EmailAddress</Name><Value>something@email.com</Value></Attribute>
		<Attribute><Name>FirstName</Name><Value>Paul</Value></Attribute>
		<Attribute><Name>LanguageCode</Name><Value>fr</Value></Attribute>
		<Attribute><Name>LastName</Name><Value>Smelser</Value></Attribute>
		</AttributeList>';


$session = curl_init($api_service_uri);
$authorization=base64_encode('00000000-0000-0000-0000-000000000001:MDAwMDAwMDAtMDAwMC0wMDAwLTAwMDAtMDAwMDAwMDAwMDAx');

curl_setopt($session, CURLOPT_HEADER, 1);
curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($session, CURLOPT_POST, 1);
curl_setopt($session, CURLOPT_HTTPHEADER, array('Authorization:' . $authorization,'Content-Type: text/xml'));
curl_setopt($session, CURLOPT_POSTFIELDS, $xml);
curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);



$response = curl_exec($session);
curl_close($session);


//Get HTTP Status code from the response
$status_code = array();
preg_match('/\d\d\d/', $response, $status_code);

// Check the HTTP Status code
switch( $status_code[0] ) {
	case 200:// Success
		break;
	case 503:
		die('Your call to Whatsnexx Web Services at ('.$api_service_uri.') failed and returned an HTTP status of 503. That means: Service unavailable. An internal problem prevented us from returning data to you.');
		break;
	case 403:
		die('Your call to Whatsnexx Web Services at ('.$api_service_uri.') failed and returned an HTTP status of 403. That means: Forbidden. You do not have permission to access this resource, or are over your rate limit.');
		break;
	case 400:
		// You may want to fall through here and read the specific XML error
		die('Your call to whatsnexx Web Services at ('.$api_service_uri.') failed and returned an HTTP status of 400. That means:  Bad request. The parameters passed to the service did not match as expected. The exact error is returned in the XML response.');
		break;
	case 404:
		// You may want to fall through here and read the specific XML error
		die('Your call to whatsnexx Web Services at ('.$api_service_uri.') failed and returned an HTTP status of 404. That means: Page not found. The URL is likely not formated correctly. More details follow: ('.$response.')');
		break;
	default:
		die('Your call to Whatsnexx Web Services at ('.$api_service_uri.') returned ('.$response.') an  HTTP status of:'. $status_code[0]);
}



// Get the XML from the response, bypassing the header
if (!($xml = strstr($response, '<?xml'))) {
	$xml = 'Value:';
}

// Output the XML
echo htmlspecialchars($xml, ENT_QUOTES);


?>