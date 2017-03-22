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
    print_r($v);
}
echo "done\n";
