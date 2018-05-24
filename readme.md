PHP SDK for AB BLE Gateway

## Installation

* Install composer
* Run 
```
composer require aprbrother/ab-ble-gateway=dev-master
```

## Usage

### Gateway V4 ###

```
$parser = new AprBrother\PacketParser4();
$data = $parser->parse($content);
var_dump($data);
```

### Gateway V2 or V3 ###

```
$parser = new AprBrother\PacketParser();
list($meta, $data) = $parser->parse($content);
```

