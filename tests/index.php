<?php

/**
 * Geolocation tests
 *
 * Get latitude/longitude or address using Google Maps API
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */

// require Geolocation
require_once '../src/Geolocation/Geolocation.php';

// define result
$result = Geolocation::getCoordinates('rijksweg', '29', 'wielsbeke', '8710', 'belgium');

// dump result
echo 'Address coordinates = ' . $result['latitude'] . ', ' . $result['longitude'];
