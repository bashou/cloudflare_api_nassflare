<?php

require_once('init.php');

function http_fetch_url($url, $postdata, $timeout = 10, $maxredirs = 10)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
	  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	  curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_MAXREDIRS, $maxredirs);
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

// Test data
$data = http_build_query(array(
   'email' => $user['cloudflare'],
   'tkn' => $token['cloudflare'],
   'a'  => 'cache_lvl',
   'z' => 'anyday-lovely.com',
// Values : agg or basic 
   'v' => 'basic'
));

if (($content = http_fetch_url($url['cloudflare'], $data, 10)) === FALSE) {
    die("Une erreur est survenue");
} else {
    $obj = json_decode($content,true);

	if($obj['result'] == "success")
	{
		var_dump($obj['response']);
    echo "-- Domain ".$obj['request']['z']." : Changement effectu&eacute;\n";

	}else{
		echo $obj['msg'];
	}

}

?>