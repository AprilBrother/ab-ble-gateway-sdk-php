PHP SDK for AB BLE Gateway

## Installation

* Install composer
* Run 
```
composer require aprbrother/ab-ble-gateway=dev-master
```

## Usage

```
$parser = new AprBrother\PacketParser();
list($meta, $data) = $parser->parse($content);
```

