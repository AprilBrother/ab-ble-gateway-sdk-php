<?php
require __DIR__ . '/../vendor/autoload.php';

const TOPIC     = "your-topic";
const HOST      = "mqtt.bconimg.com";
const PORT      = 1883;
const MQTT_USER = "";
const MQTT_PASS = "";

use AprBrother\PacketParser4;

$username   = MQTT_USER;
$password   = MQTT_PASS;
$mqttclient = new Mosquitto\Client;
$mqttclient->onConnect(function() use ($mqttclient){
	echo "connect success \n";
	$mqttclient->subscribe(TOPIC, 0);
});

$mqttclient->onMessage(function($message) use($mqttclient){
    $parser = new AprBrother\PacketParser4();
    $data = msgpack_unpack($message->payload);
    $devices = $data['devices'];
    unset($data['devices']);

	echo "===== device data ====\n";
    print_r($data);
	foreach($devices as $v) {
        $adv = $parser->parse($v);
        echo "mac: $adv->macAddress rssi: $adv->rssi advertising data: ";
        echo $parser->hexString($adv->rawData);
        echo "\n";
	}
});
//$mqttclient->setCredentials($username, $password);
$mqttclient->connect(HOST, PORT, 60);
$mqttclient->loopforever();
