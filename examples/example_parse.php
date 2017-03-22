<?php

require __DIR__ . '/../vendor/autoload.php';
$sampleFile = __DIR__ . '/sample_data.txt';

$parser = new AprBrother\PacketParser();
$cont = file_get_contents($sampleFile);
list($meta, $data) = $parser->parse($cont);

echo "===== meta ====\n";
print_r($meta);
echo "===== data ====\n";
foreach($data as $v) {
    echo "mac: $v->macAddress rssi: $v->rssi adv:";
    $parser->printHexString($v->rawData);
    echo "\n";
}
echo "done\n";
