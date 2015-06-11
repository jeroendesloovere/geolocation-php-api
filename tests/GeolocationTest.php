<?php

namespace JeroenDesloovere\Geolocation\tests;

// required to load
require_once __DIR__ . '/../vendor/autoload.php';

/*
 * This file is part of the Geolocation PHP Class from Jeroen Desloovere.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 */

use JeroenDesloovere\Geolocation\Geolocation;

/**
 * In this class we test all generic functions from Geolocation.
 *
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 */
class GeolocationTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->api = new Geolocation();
    }

    /**
     * Test getting latitude/longitude coordinates from address.
     */
    public function testGettingLatitudeAndLongitudeFromAddress()
    {
        $street = 'Koningin Maria Hendrikaplein';
        $streetNumber = '1';
        $city = 'Gent';
        $zip = '1';
        $country = 'belgium';
        
        $result = $this->api->getCoordinates(
            $street,
            $streetNumber,
            $city,
            $zip,
            $country
        );

        $this->assertEquals(51.037249600000003, $result['latitude']);
        $this->assertEquals(3.7094974999999999, $result['longitude']);
    }

    /**
     * Test getting address from latitude and longitude coordinates.
     */
    public function testGetAddressFromLatitudeAndLongitude()
    {
        $latitude = 51.0363935;
        $longitude = 3.7121008;

        $result = $this->api->getAddress(
            $latitude,
            $longitude
        );

        $this->assertEquals('Prinses Clementinalaan 114-140, 9000 Gent, Belgium', $result['label']);
        $this->assertEquals('array', gettype($result['components']));
    }
}
