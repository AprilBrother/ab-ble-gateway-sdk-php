<?php
require __DIR__ . '/../vendor/autoload.php';

use AprBrother\PacketParser;

// see http://wiki.aprbrother.com/wiki/ASensor_Packet_Formats
const LEN_ADV_SENSOR    = 27;

const OFFSET_MOTION     = 19;
const OFFSET_X          = 20;
const OFFSET_Y          = 21;
const OFFSET_Z          = 22;
const OFFSET_CMD        = 23;
const OFFSET_LMD        = 24;
const OFFSET_BATT       = 25;

$mqttclient = new Mosquitto\Client;
$mqttclient->onConnect(function() use ($mqttclient){
	echo "connect success \n";
	$mqttclient->subscribe('beacons',0);
});
$mqttclient->onMessage(function($message) use($mqttclient){
	echo "===== RAW message ====\n";
    echo PacketParser::hexString($message->payload);

	list($meta, $data) = PacketParser::parse($message->payload);

	echo "\n===== meta ====\n";
	print_r($meta);
	echo "===== data ====\n";
	foreach($data as $v) {
        $sensor = strlen($v->rawData) == LEN_ADV_SENSOR ? 1 : 0;
        if (!$sensor) {
            continue;
        }

        $raw    = $v->rawData;
        $move   = hexdec(bin2hex($raw[OFFSET_MOTION]));
        $x      = hexdec(bin2hex($raw[OFFSET_X]));
        $y      = hexdec(bin2hex($raw[OFFSET_Y]));
        $z      = hexdec(bin2hex($raw[OFFSET_Z]));
        $currentMotionDuration  = hexdec(bin2hex($raw[OFFSET_CMD]));
        $lastMotionDuration     = hexdec(bin2hex($raw[OFFSET_LMD]));
        $batt   = hexdec(bin2hex($raw[OFFSET_BATT]));
		echo "mac: $v->macAddress rssi: $v->rssi move: $move x: $x y: $y z: $z CMD: $currentMotionDuration LMD: $lastMotionDuration batt: $batt ";
		//echo PacketParser::hexString($v->rawData);
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
$mqttclient->connect('192.168.1.11',1883,60);
$mqttclient->loopforever();
