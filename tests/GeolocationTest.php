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
use PHPUnit\Framework\TestCase;

/**
 * In this class we test all generic functions from Geolocation.
 *
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 */
class GeolocationTest extends TestCase
{
    /** @var Geolocation */
    private $api;

    public function setUp(): void
    {
        $this->api = new Geolocation();
    }

    /**
     * Test getting latitude/longitude coordinates from address.
     */
    public function testGettingLatitudeAndLongitudeFromAddress(): void
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

        $this->assertEquals(51.037249600000003, $result->getLatitude());
        $this->assertEquals(3.7094974999999999, $result->getLongitude());
    }

    /**
     * Test getting address from latitude and longitude coordinates.
     */
    public function testGetAddressFromLatitudeAndLongitude(): void
    {
        $latitude = 51.0363935;
        $longitude = 3.7121008;

        $result = $this->api->getAddress(
            $latitude,
            $longitude
        );

        $this->assertEquals('Pr. Clementinalaan 114-140, 9000 Gent, Belgium', $result->getLabel());
    }

    /**
     * Test getting latitude/longitude coordinates from address.
     */
    public function testGettingLatitudeAndLongitudeFromAddressWithoutHTTPS(): void
    {
        $this->api = new Geolocation(null, false);
        $this->testGettingLatitudeAndLongitudeFromAddress();
    }

    /**
     * Test getting address from latitude and longitude coordinates.
     */
    public function testGetAddressFromLatitudeAndLongitudeWithoutHTTPS(): void
    {
        $this->api = new Geolocation(null, false);
        $this->testGetAddressFromLatitudeAndLongitude();
    }
}
