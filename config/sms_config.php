<?php
/**
 * SMS Gateway Configuration
 * 
 * Update the IP address here when your phone's IP changes.
 * This is the only file you need to modify.
 */

// Get your phone's IP from the Simple SMS Gateway app
// Example: http://192.168.68.200:8080/send-sms
define('SMS_GATEWAY_IP', '192.168.68.200');
define('SMS_GATEWAY_PORT', '8080');

// Full gateway URL (don't modify this)
define('GATEWAY_URL', 'http://' . SMS_GATEWAY_IP . ':' . SMS_GATEWAY_PORT . '/send-sms');
