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
   'a'  => 'stats',
   'interval' => 20
));

if (($content = http_fetch_url($url['cloudflare'], $data, 10)) === FALSE) {
    die("Une erreur est survenue");
} else {
    $obj = json_decode($content,true);

	if($obj['result'] == "success")
	{
		//var_dump($obj['response']['result']);
		
		// Lecture des informations
		echo "- Statistiques : \n";
		echo "-- Période : ".date('d/m/Y H:i:s',($obj['response']['result']['timeZero']/1000))." -> ".date('d/m/Y H:i:s',($obj['response']['result']['timeEnd']/1000))."\n";
		
		echo "\n";
		
		foreach($obj['response']['result']['objs'] as $obj_trap)
		{
			echo "-- Date de mise en cache des elements : ".date('d/m/Y H:i:s',($obj_trap['cachedServerTime']/1000))."\n";
			echo "-- Date d'expiration du cache des elements : ".date('d/m/Y H:i:s',($obj_trap['cachedExpryTime']/1000))."\n";

			echo "-- Visiteurs :\n";
			echo "--- Pages vues :\n";
			echo "---- Trafic Regulier : ".$obj_trap['trafficBreakdown']['pageviews']['regular']."\n";
			echo "---- Menaces : ".$obj_trap['trafficBreakdown']['pageviews']['threat']."\n";
			echo "---- Crawlers : ".$obj_trap['trafficBreakdown']['pageviews']['crawler']."\n";
			
			echo "--- Hits :\n";
			echo "---- Trafic Regulier : ".$obj_trap['trafficBreakdown']['uniques']['regular']."\n";
			echo "---- Menaces : ".$obj_trap['trafficBreakdown']['uniques']['threat']."\n";
			echo "---- Crawlers : ".$obj_trap['trafficBreakdown']['uniques']['crawler']."\n";
			
			echo "--- Taux de cache :\n";
			echo "---- Volume : ".number_format(($obj_trap['bandwidthServed']['cloudflare']*100/($obj_trap['bandwidthServed']['user'] + $obj_trap['bandwidthServed']['cloudflare'])),2)." %\n";
			echo "---- Hits : ".number_format(($obj_trap['requestsServed']['cloudflare']*100/($obj_trap['requestsServed']['user'] + $obj_trap['requestsServed']['cloudflare'])),2)." %\n";

			echo "\n";
			
			echo "--- Administratif :\n";
			echo "---- Compte pro : ".$obj_trap['pro_zone']."\n";
			echo "---- Mode developpement : ".$obj_trap['dev_mode']."\n";
			echo "---- IP V6 : ".$obj_trap['ipv46']."\n";
			echo "---- Niveau de cache : ".$obj_trap['cache_lvl']."\n";
			
			
		}
		
		
	}else{
		echo $obj['msg'];
	}

}

?>