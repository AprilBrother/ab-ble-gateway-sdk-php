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

    // iBeacon
    const OFFSET_UUID           = 4;
    const OFFSET_MAJOR          = 20;
    const OFFSET_MINOR          = 22;
    const OFFSET_MEASURED_POWER = 24;

    // Eddystone UID
    const OFFSET_CALIBRATED_TX_POWER = 3;
    const OFFSET_NAMESPACE      = 4;
    const OFFSET_INSTANCE       = 14;

    // Eddystone URL

    const LEN_ADV_IBEACON       = 30;
    const LEN_ADV_EDDYSTONE_UID = 31;
    const LEN_ADV_MIN_EDDYSTONE_URL = 14;

    const LEN_UUID              = 16;
    const LEN_MAJOR             = 2;
    const LEN_MINOR             = 2;

    const LEN_NAMESPACE         = 10;
    const LEN_INSTANCE          = 6;

    const PREFIX_ADV_IBEACON        = "0201061AFF4C000215";
    const PREFIX_ADV_EDDYSTONE_UID  = "0201060303AAFE1716AAFE00";

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
     * Check is iBeacon
     *
     * @return bool
     */
    public static function isIbeacon(BLEAdvData $adv) {
        if (strlen($adv->rawData) < self::LEN_ADV_IBEACON) {
            return false;
        }

        $hexString = self::hexString($adv->rawData);
        $prefixLen = strlen(self::PREFIX_ADV_IBEACON);
        return (substr($hexString, 0, $prefixLen) == self::PREFIX_ADV_IBEACON);
    }

    /**
     * Check is eddystone UID
     *
     * @return bool
     */
    public static function isEddystoneUid(BLEAdvData $adv) {
        if (strlen($adv->rawData) != self::LEN_ADV_EDDYSTONE_UID) {
            return false;
        }
        $hexString = self::hexString($adv->rawData);
        $prefixLen = strlen(self::PREFIX_ADV_EDDYSTONE_UID);
        return (substr($hexString, 0, $prefixLen) == self::PREFIX_ADV_EDDYSTONE_UID);
    }

    /**
     * Check is eddystone URL
     *
     * @TODO
     * @return bool
     */
    public static function isEddystoneUrl(BLEAdvData $adv) {
        if (strlen($adv->rawData) < self::LEN_ADV_MIN_EDDYSTONE_URL) {
            return false;
        }
        $hexString = self::hexString($adv->rawData);
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
     * Parse adv for eddystone UID object
     * 
     * @return EddystoneUid object or null
     */
    public static function parseEddystoneUid(BLEAdvData $adv) {
        if (!self::isEddystoneUid($adv)) {
            return null;    
        }

        $beacon = new Beacon\EddystoneUid();
        $beacon->macAddress = $adv->macAddress;
        $beacon->rssi       = $adv->rssi;
        $serviceData        = $adv->getRecord(BLEAdvType::SERVICE_DATA);
        $beacon->calibratedTxPower = ord($serviceData[self::OFFSET_CALIBRATED_TX_POWER]);
        $beacon->namespace   = substr($serviceData, self::OFFSET_NAMESPACE, self::LEN_NAMESPACE);
        $beacon->instance   = substr($serviceData, self::OFFSET_INSTANCE, self::LEN_INSTANCE);

        return $beacon;
    }

    /**
     * Parse adv for iBeacon object
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
        $beacon->measuredPower = ord($manufactureData[self::OFFSET_MEASURED_POWER]) - 256;

        return $beacon;
    }

}
