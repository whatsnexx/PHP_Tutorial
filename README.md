Send Events Using PHP
==============

Describes how to send an event through the whatsnexx TicketBus from PHP code.
 
Getting Started
------------------
The Whatsnexx <b>ticketbus</b> handles REST and SOAP Web Service request to send events. In this example, we will demonstrate how to send and event as a RESTful service request to the Whatsnexx <b>TicketBusService</b> using PHP. You will need following to send a request to the <b>ticketbus</b> service.

1. <b>Account Id:</b> Provided by Whatsnexx.
2. <b>Username:</b> Provided by Whatsnexx.
2. <b>Password:</b> Provided by Whatsnexx.
3. <b>TermName:</b> This is the name of the event that is to be triggered by the send event.
4. <b>SubjectCode:</b> The unique identifier for your [subject](). This usually represents <b>who</b> you would like to send the event to.
5. <b>SubjectTypeId:</b> A unique identitfier for the subject type. The subject type defines the context under which events are sent.
6. <b>ExecutionEnvironment:</b> Specifies the Whatsnexx environment you are sending the event request. A <b>Constellation</b> must exist in the chosen environment for the event to be triggered. The available Environments are: Test, Stage, and Production.
7. <b>Attributes:</b> A list of attributes that are used by the event.


Steps
----------------
###1.  Construct the RESTful URI.  
<b>"https:// ticketbus.whatsnexx.com/Rest/TicketBusService.svc/SendEvent/ {accountId}/{subjectTypeId}/{termName}/{executionEnvironment}/{subjectCode}".</b>

```php
/*configuration*/
$api_base_service_url = 'https://ticketbus.whatsnexx.com/Rest/TicketBusService.svc';
 
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
$api_service_uri = getServiceUrl($api_base_service_url,'SendEvent','{accountId}','{subjectTypeId}','TestEvent','Stage','testing123');   
```
###2. Define the Event Attributes

A whatsnexx ticket may have a number of attributes defined. The <b>Attribute</b>s are defined in an XML format. The xmlns ( [namespace](http://www.w3schools.com/tags/att_html_xmlns.asp) ) '<i>http://schemas.whatsnexx.com/v1/tbx/</i>' must be specified in the <b>AttributeList</b> tag.

```xml
<AttributeList xmlns="http://schemas.whatsnexx.com/v1/tbx/">
 
	<Attribute><Name>Attribute1</Name><Value>Value1</Value></Attribute>
	<Attribute><Name>Attribute2</Name><Value>Value2</Value></Attribute>
	<Attribute><Name>Attribute3</Name><Value>Value3</Value></Attribute>

 </AttributeList>
```

###3. Setup and execute HTTP request using [cURL](http://php.net/manual/en/book.curl.php) libraries.
######Note: $authorization is added to the request header after it is encoded.
```php
$session = curl_init($api_service_uri);
$authorization=base64_encode('{username}:{password}');

curl_setopt($session, CURLOPT_HEADER, 1);
curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($session, CURLOPT_POST, 1);
curl_setopt($session, CURLOPT_HTTPHEADER, array('Authorization:' . $authorization,'Content-Type: text/xml'));
curl_setopt($session, CURLOPT_POSTFIELDS, $xml);
curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);



$response = curl_exec($session);
curl_close($session);
```
###4. Check response.
```php
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
```
Getting Help
-----------
[Whatsnexx Full Documentation]() Available Soon  


*****
[Top](https://github.com/paulsmelser/PHP-Send-Event/blob/master/README.md#send-events-using-php)
