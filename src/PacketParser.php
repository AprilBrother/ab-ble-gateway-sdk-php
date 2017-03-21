<?php

namespace AprBrother;

use AprBrother\BLEAdvType;
use AprBrother\BLEAdvData;

class PacketParser {

    const PACKET_START          = 0xfe;

    const OFFSET_LENGTH         = 1;
    const OFFSET_ADV_TYPE       = 2;
    const OFFSET_MAC_ADDRESS    = 3;
    const OFFSET_RSSI           = 9;
    const OFFSET_ADV_DATA       = 10;

    /**
     * Parse packet from AB BLE Gateway
     * 
     * @link http://wiki.aprbrother.com/wiki/AB_BLE_Gateway_User_Guide#BLE
     * @return array
     */
    public static function parse($packet) {
        $lines  = $explode("\r\n", $packet);
        $meta   = new Stdclass();
        $advData = array();
        $tmp    = array_shift($lines);
        array_shift($lines);

        if(!empty($tmp)) {
            $meta = json_decode($tmp);
        }

        foreach($lines as $v) {
            if ($v[0] != self::PACKET_START) {
                continue;
            }
            $data = new BLEAdvData();
            $data->advType      = $v[OFFSET_ADV_TYPE];
            $data->rssi         = $v[OFFSET_RSSI];
            $data->macAddress   = substr($v, self::OFFSET_MAC_ADDRESS, BLEAdvData::MAC_ADDRESS_LEN);
            $data->rawData      = substr($v, self::OFFSET_ADV_DATA);
            $data->records       = self::parseAdvertisement($data->rawData);
            $advData[]          = $data;
        }

        return array($meta, $advData);
    }

    /**
     * Parse BLE advertisement data
     *
     * @return array
     */
    public static function parseAdvertisement($payload) {
        $total = strlen($payload);
        $records = array();
        for($i = 0; $i < $total;) {
            $len    = $payload[$i];
            $type   = $payload[$i + 1];
            if (!$type) {
                return $records;
            }
            $begin  = $i + 2;
            $end    = $begin + $len - 1;
            $data   = substr($payload, $begin, $end);
            $records[$type] = $data;
            $i += $len + 1;
        }
        return $records;
    }

}
