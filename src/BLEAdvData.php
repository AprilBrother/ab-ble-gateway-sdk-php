<?php

namespace AprBrother;

use AprBrother\BLEAdvType;

class BLEAdvData {

    const MAC_ADDRESS_LEN = 6;

    public $records = array();
	public $rawData, $rssi, $macAddress, $advType;

    public function getRecord($type) {
        return $this->records[$type];
    }

}
