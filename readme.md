PHP SDK for AB BLE Gateway

## Installation

* Install composer
* Run 
```
composer require aprbrother/ab-ble-gateway=dev-master
```

For BLE Gateway V4, you should install msgpack extension also

`pecl install msgpack`

## Usage

### Gateway V4 ###

```
$parser     = new AprBrother\PacketParser4();
$meta       = $parser->parse($content);
$rawData    = $meta['devices'];
$data       = [];
unset($meta['devices']);
foreach($rawData as $v) {
    $data[] = $parser->parse($v);
}
```

### Gateway V2 or V3 ###

```
$parser = new AprBrother\PacketParser();
list($meta, $data) = $parser->parse($content);
```

