<?php

namespace AprBrother;

class BLEAdvType {

	const FLAGS                                = 0x01; /**< Flags for discoverability. */
	const SERVICE_16BIT_UUID_MORE_AVAILABLE    = 0x02; /**< Partial list of 16 bit service UUIDs. */
	const SERVICE_16BIT_UUID_COMPLETE          = 0x03; /**< Complete list of 16 bit service UUIDs. */
	const SERVICE_32BIT_UUID_MORE_AVAILABLE    = 0x04; /**< Partial list of 32 bit service UUIDs. */
	const SERVICE_32BIT_UUID_COMPLETE          = 0x05; /**< Complete list of 32 bit service UUIDs. */
	const SERVICE_128BIT_UUID_MORE_AVAILABLE   = 0x06; /**< Partial list of 128 bit service UUIDs. */
	const SERVICE_128BIT_UUID_COMPLETE         = 0x07; /**< Complete list of 128 bit service UUIDs. */
	const SHORT_LOCAL_NAME                     = 0x08; /**< Short local device name. */
	const COMPLETE_LOCAL_NAME                  = 0x09; /**< Complete local device name. */
	const TX_POWER_LEVEL                       = 0x0A; /**< Transmit power level. */
	const CLASS_OF_DEVICE                      = 0x0D; /**< Class of device. */
	const SIMPLE_PAIRING_HASH_C                = 0x0E; /**< Simple Pairing Hash C. */
	const SIMPLE_PAIRING_RANDOMIZER_R          = 0x0F; /**< Simple Pairing Randomizer R. */
	const SECURITY_MANAGER_TK_VALUE            = 0x10; /**< Security Manager TK Value. */
	const SECURITY_MANAGER_OOB_FLAGS           = 0x11; /**< Security Manager Out Of Band Flags. */
	const SLAVE_CONNECTION_INTERVAL_RANGE      = 0x12; /**< Slave Connection Interval Range. */
	const SOLICITED_SERVICE_UUIDS_16BIT        = 0x14; /**< List of 16-bit Service Solicitation UUIDs. */
	const SOLICITED_SERVICE_UUIDS_128BIT       = 0x15; /**< List of 128-bit Service Solicitation UUIDs. */
	const SERVICE_DATA                         = 0x16; /**< Service Data - 16-bit UUID. */
	const PUBLIC_TARGET_ADDRESS                = 0x17; /**< Public Target Address. */
	const RANDOM_TARGET_ADDRESS                = 0x18; /**< Random Target Address. */
	const APPEARANCE                           = 0x19; /**< Appearance. */
	const ADVERTISING_INTERVAL                 = 0x1A; /**< Advertising Interval. */
	const LE_BLUETOOTH_DEVICE_ADDRESS          = 0x1B; /**< LE Bluetooth Device Address. */
	const LE_ROLE                              = 0x1C; /**< LE Role. */
	const SIMPLE_PAIRING_HASH_C256             = 0x1D; /**< Simple Pairing Hash C-256. */
	const SIMPLE_PAIRING_RANDOMIZER_R256       = 0x1E; /**< Simple Pairing Randomizer R-256. */
	const SERVICE_DATA_32BIT_UUID              = 0x20; /**< Service Data - 32-bit UUID. */
	const SERVICE_DATA_128BIT_UUID             = 0x21; /**< Service Data - 128-bit UUID. */
	const URI                                  = 0x24; /**< URI */
	const INFORMATION_3D_DATA                  = 0x3D; /**< 3D Information Data. */
	const MANUFACTURER_SPECIFIC_DATA           = 0xFF; /**< Manufacturer Specific Data. */

}
