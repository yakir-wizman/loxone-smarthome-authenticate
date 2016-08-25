<?php
$address = "127.0.0.1";
echo("Trying to login @ {$address}..\r\n");
$apiKeyObj 	= json_decode(file_get_contents("http://{$address}/jdev/cfg/apiKey?"));
			
if(isset($apiKeyObj->{'LL'}->{'value'})) {
	$valueJSObj = explode(',', str_replace(array('\'', '{', '}', ' '), '', $apiKeyObj->{'LL'}->{'value'}));
	$parseKey	= explode(':', $valueJSObj[2]);
	$apiKey		= $parseKey[1];

	$password 	= hash_hmac('sha1', 'admin:admin', authToken($apiKey));

	$loginReq 	= json_decode(file_get_contents("http://{$address}/jdev/sps/LoxAPPversion3?auth={$password}&user=admin"));
	$statusCode	= isset($loginReq->{'LL'}->{'Code'}) ? $loginReq->{'LL'}->{'Code'} : '401';

	if(strstr($statusCode, '200')) {
		echo("Logged in successfully!\r\n");
	} else {
		echo("Failed!\r\n");
	}
} else {
	echo("Host has failed to respond!\r\n");
}

function authToken($str) {
	$str_split	= substr(chunk_split($str, 2, ','), 0, -1); 
	$comma_split = explode(',', $str_split);
	$str = '';
	foreach($comma_split as $key => $value)
		$str.= unicode_decode('\u00'.$value);
	return($str);
}

function replace_unicode_escape_sequence($match) {
	return(mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE'));
}

function unicode_decode($str) {
	return(preg_replace_callback('/\\\\u([0-9a-f]{4})/i', 'replace_unicode_escape_sequence', $str));
}
?>
