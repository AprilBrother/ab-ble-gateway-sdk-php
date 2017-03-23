<?php

namespace AprBrother;

use AprBrother\BLEAdvType;
use AprBrother\BLEAdvData;

class PacketParser {

    const PACKET_START          = "fe";

    const OFFSET_LENGTH         = 1;
    const OFFSET_ADV_TYPE       = 2;
    const OFFSET_MAC_ADDRESS    = 3;
    const OFFSET_RSSI           = 9;
    const OFFSET_ADV_DATA       = 10;

    const OFFSET_UUID           = 4;
    const OFFSET_MAJOR          = 20;
    const OFFSET_MINOR          = 22;
    const OFFSET_MEASURED_POWER = 24;

    const LEN_ADV_IBEACON       = 30;
    const LEN_UUID              = 16;
    const LEN_MAJOR             = 2;
    const LEN_MINOR             = 2;

    const PREFIX_ADV_IBEACON    = "0201061AFF4C000215";

    /**
     * Parse packet from AB BLE Gateway
     * 
     * @link http://wiki.aprbrother.com/wiki/AB_BLE_Gateway_User_Guide#BLE
     * @return array
     */
    public static function parse($packet) {
        $lines  = explode("\r\n", $packet);
        $meta   = (object)null;
        $advData = array();
        $tmp    = array_shift($lines);
        array_shift($lines);

        if(!empty($tmp)) {
            $meta = json_decode($tmp);
        }

        foreach($lines as $v) {
            if(empty($v)) {
                continue;
            }
            if ($v[0] != hex2bin(self::PACKET_START)) {
                continue;
            }
            $data = new BLEAdvData();
            $data->advType      = ord($v[self::OFFSET_ADV_TYPE]);
            $data->rssi         = ord($v[self::OFFSET_RSSI]) - 255;
            $data->macAddress   = strtoupper(bin2hex(substr($v, self::OFFSET_MAC_ADDRESS, BLEAdvData::MAC_ADDRESS_LEN)));
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
            $len    = ord($payload[$i]);
            $type   = ord($payload[$i + 1]);
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

    public static function hexString($value) {
        $len    = strlen($value);
        $i      = 0;
        $hex    = "";
        do {
            $hex .= sprintf("%02X", ord($value{$i}));
            $i++;
        } while ($i < $len);
        return $hex;
    }

    /**
     * @return bool
     */
    public static function isIbeacon(BLEAdvData $adv) {
        if (strlen($adv->rawData) < self::LEN_ADV_IBEACON) {
            return false;
        }

        $hexString = self::hexString($adv->rawData);
        $prefixLen = strlen(self::PREFIX_ADV_IBEACON);
        if(substr($hexString, 0, $prefixLen) == self::PREFIX_ADV_IBEACON) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @TODO
     *
     * @return bool
     */
    public static function isEddystoneUid(BLEAdvData $adv) {
        return false;
    }

    /**
     * @TODO
     * @return bool
     */
    public static function isEddystoneUrl(BLEAdvData $adv) {
        return false;
    }

    /**
     * @TODO
     * @return bool
     */
    public static function isEddystoneTlmEncrypted(BLEAdvData $adv) {
        return false;
    }

    /**
     * @TODO
     * @return bool
     */
    public static function isEddystoneTlmUnencrypted(BLEAdvData $adv) {
        return false;
    }

    /**
     * 
     * @return Ibeacon object or null
     */
    public static function parseIbeacon(BLEAdvData $adv) {
        if (!self::isIbeacon($adv)) {
            return null;    
        }

        $beacon = new Beacon\Ibeacon();
        $beacon->macAddress = $adv->macAddress;
        $beacon->rssi       = $adv->rssi;
        $manufactureData    = $adv->getRecord(BLEAdvType::MANUFACTURER_SPECIFIC_DATA);
        $beacon->uuid       = substr($manufactureData, self::OFFSET_UUID, self::LEN_UUID);
        $major              = substr($manufactureData, self::OFFSET_MAJOR, self::LEN_MAJOR);
        $beacon->major      = hexdec(bin2hex($major));
        $minor              = substr($manufactureData, self::OFFSET_MINOR, self::LEN_MINOR);
        $beacon->minor      = hexdec(bin2hex($minor));
        $beacon->measuredPower = ord($manufactureData[self::OFFSET_MEASURED_POWER]) - 255;

        return $beacon;
    }

}
