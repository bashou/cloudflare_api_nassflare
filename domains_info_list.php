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
   'a'  => 'zone_load_multi'
));

if (($content = http_fetch_url($url['cloudflare'], $data, 10)) === FALSE) {
    die("Une erreur est survenue");
} else {
    $obj = json_decode($content,true);

	if($obj['result'] == "success")
	{
		var_dump($obj['response']);

    echo "-- Domaines : \n";
    echo "Vous avez ".$obj['response']['zones']['count']." domaines configurés dans Cloudflare.\n";

    foreach($obj['response']['zones']['objs'] as $domains)
    {
      echo "--- ".$domains['display_name']."(Status : ".$domains['zone_status_class'].", User ID : ".$domains['user_id'].", Zone ID : ".$domains['zone_id'].", Plan : ".$domains['props']['plan'].")\n";
      echo "---- Options disponibles : \n";
      foreach($domains['allow'] as $features)
      {
        echo $features." ";
      }
      echo "\n\n";
    }


	}else{
		echo $obj['msg'];
	}

}

?>