## Example index

File | Descrption
------------- | -------------
example_parse.php | Parse all BLE advertisement data
example_parse_abtemp.php | Parse sensor data come from [ABTemp temperature sensor](http://wiki.aprbrother.com/wiki/ABTemp)
example_parse_asensor.php | Parse sensor data come from [ASensor](http://wiki.aprbrother.com/wiki/ASensor)
example_parse_aplant.php | Parse sensor data come from [APlant](http://wiki.aprbrother.com/wiki/APlant)
example_mqtt_client.php | Parse BLE data from MQTT Broker, [mosquitto extension](//pecl.php.net/mosquitto) is required
example_gateway4_mqtt.php | Parse BLE data from BLE Gateway 4, [mosquitto extension](//pecl.php.net/mosquitto) and msgpack extension are required

## How to run

```
cd ..
composer dump_autoload
php example_parse.php
```

For MQTT example, install mosquitto extension first

```
pecl install mosquitto
```
