<?php
require __DIR__ . '/../vendor/autoload.php';

const TOPIC = "beacons";
const HOST = "192.168.31.75";
const PORT = 1883;

const APLANT_UUID = "B5B182C7EAB14988AA99B5C1517008D9";

// mac address for aplant sensor
$aplantMac = array(
    "123B6A1A5117",
    "123B6A1A751D",
);
$aplantMac = array_flip($aplantMac);

use AprBrother\PacketParser;

$mqttclient = new Mosquitto\Client;
$mqttclient->onConnect(function() use ($mqttclient){
	echo "connect success \n";
	$mqttclient->subscribe(TOPIC, 0);
});
$mqttclient->onMessage(function($message) use($mqttclient){
    $parser = new AprBrother\PacketParser();
    list($meta, $data) = $parser->parse($message->payload);

    echo "===== meta ====\n";
    print_r($meta);
    echo "===== data ====\n";
    foreach($data as $v) {
        $beacon = $parser->parseIbeacon($v);
        if (empty($beacon)) {
            continue;
        }
        $uuidString = $parser->hexString($beacon->uuid);
        if ($uuidString != APLANT_UUID) {
            // not aplant sensor
            continue;
        }

        $mac = $GLOBALS['aplantMac'];
        if (!isset($mac[$beacon->macAddress])) {
            continue;
        }

        // parse battery level and soil moisture
        // see http://wiki.aprbrother.com/wiki/APlant_Payload_Format
        $minor      = sprintf("%04X", $beacon->minor);
        $moisture    = hexdec(substr($minor, 0, 2));
        $temperature = hexdec(substr($minor, 2, 4));
        echo "mac $beacon->macAddress $beacon->major minor: $beacon->minor moisture: $moisture temperature: $temperature\n";
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
