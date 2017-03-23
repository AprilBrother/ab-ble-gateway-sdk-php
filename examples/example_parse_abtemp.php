<?php

require __DIR__ . '/../vendor/autoload.php';

const TEMPERATURE_UUID = "B5B182C7EAB14988AA99B5C1517008D9";

$sampleFile = __DIR__ . '/sample_data.txt';

$parser = new AprBrother\PacketParser();
$cont = file_get_contents($sampleFile);
list($meta, $data) = $parser->parse($cont);

echo "===== meta ====\n";
print_r($meta);
echo "===== data ====\n";
foreach($data as $v) {
    $beacon = $parser->parseIbeacon($v);
    if (empty($beacon)) {
        continue;
    }
    $uuidString = $parser->hexString($beacon->uuid);

    if ($uuidString != TEMPERATURE_UUID) {
        // not temperature sensor
        continue;
    }

    // parse battery level and temperature
    // see http://wiki.aprbrother.com/wiki/ABTemp_Payload_Format
    $minor      = sprintf("%04X", $beacon->minor);
    $battery    = hexdec(substr($minor, 0, 2));
    $temperature = hexdec(substr($minor, 2, 4));
    echo "uuid: $uuidString major: $beacon->major minor: $beacon->minor battery: $battery temperature: $temperature\n";
}
echo "done\n";
