<?php
require __DIR__ . '/../vendor/autoload.php';
$sampleFile = __DIR__ . '/sample_gateway4_data.txt';

use AprBrother\PacketParser4;

$parser = new AprBrother\PacketParser4();
$cont = file_get_contents($sampleFile);
$data = msgpack_unpack($cont);
$devices = $data['devices'];
unset($data['devices']);

echo "===== device data ====\n";
print_r($data);
foreach($devices as $v) {
    $adv = $parser->parse($v);
    echo $parser->hexString($adv->rawData);
    echo "\n";
}
