<?php
require __DIR__ . '/../vendor/autoload.php';

const TOPIC = "beacons";
const HOST = "mqtt.bconimg.com";
const PORT = 1883;

use AprBrother\PacketParser;

$mqttclient = new Mosquitto\Client;
$mqttclient->onConnect(function() use ($mqttclient){
	echo "connect success \n";
	$mqttclient->subscribe(TOPIC, 0);
});
$mqttclient->onMessage(function($message) use($mqttclient){
	echo "===== RAW message ====\n";
    echo PacketParser::hexString($message->payload);

	list($meta, $data) = PacketParser::parse($message->payload);

	echo "\n===== meta ====\n";
	print_r($meta);
	echo "===== data ====\n";
	foreach($data as $v) {
		$iBeacon = (int)PacketParser::isIbeacon($v);
		echo "mac: $v->macAddress rssi: $v->rssi iBeacon: $iBeacon adv:";
		echo PacketParser::hexString($v->rawData);
		echo "\n";
	}
});
$mqttclient->onLog(function($level,$msg)use($mqttclient){
    /*
	echo "\n";
	echo $msg;
	echo "\n";
     */
});
$mqttclient->connect(HOST,PORT,60);
$mqttclient->loopforever();
