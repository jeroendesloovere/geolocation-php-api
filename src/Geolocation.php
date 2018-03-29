<?php

namespace JeroenDesloovere\Geolocation;
use JeroenDesloovere\Geolocation\Result\Address;
use JeroenDesloovere\Geolocation\Result\Coordinates;

/**
 * Geolocation
 *
 * Get latitude/longitude or address using Google Maps API
 *
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 */
class Geolocation
{
    // API URL
    const API_URL = 'http://maps.googleapis.com/maps/api/geocode/json';

    /**
     * Do call
     *
     * @param  array $parameters
     * @return object
     * @throws Exception
     */
    protected function doCall($parameters = array())
    {
        // check if curl is available
        if (!function_exists('curl_init')) {
            throw Exception::CurlNotInstalled();
        }

        // define url
        $url = self::API_URL . '?';

        // add every parameter to the url
        foreach ($parameters as $key => $value) $url .= $key . '=' . urlencode($value) . '&';

        // trim last &
        $url = trim($url, '&');

        // init curl
        $curl = curl_init();

        // set options
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        // execute
        $response = curl_exec($curl);

        // fetch errors
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        // close curl
        curl_close($curl);

        // we have errors
        if ($errorNumber != '') throw new Exception($errorMessage);

        // redefine response as json decoded
        $response = json_decode($response);

        if ($response->status === 'OVER_QUERY_LIMIT') {
            throw Exception::overQueryLimit();
        }

        // return the content
        return $response->results;
    }

    /**
     * Get address using latitude/longitude
     *
     * @param  float $latitude
     * @param  float $longitude
     * @return Address
     * @throws Exception
     */
    public function getAddress($latitude, $longitude): Address
    {
        $addressSuggestions = $this->getAddresses($latitude, $longitude);

        if (count($addressSuggestions) == 0) {
            throw Exception::noAddressFoundForCoordinates($latitude, $longitude);
        }

        return $addressSuggestions[0];
    }

    /**
     * Get possible addresses using latitude/longitude
     *
     * @param  float $latitude
     * @param  float $longitude
     * @return array
     * @throws Exception
     */
    public function getAddresses($latitude, $longitude)
    {
        // init results
        $addresses = array();

        // define result
        $addressSuggestions = $this->doCall(array(
            'latlng' => $latitude . ',' . $longitude,
            'sensor' => 'false'
        ));

        // loop addresses
        foreach ($addressSuggestions as $key => $addressSuggestion) {
            $addresses[$key] = Address::createFromGoogleResult($addressSuggestion);
        }

        return $addresses;
    }

    /**
     * Get coordinates latitude/longitude
     *
     * @param  null|string $street
     * @param  null|string $streetNumber
     * @param  null|string $city
     * @param  null|string $zip
     * @param  null|string $country
     * @return Coordinates
     * @throws Exception
     */
    public function getCoordinates(
        ?string $street = null,
        ?string $streetNumber = null,
        ?string $city = null,
        ?string $zip = null,
        ?string $country = null
    ): Coordinates {
        // init item
        $item = array();

        // add street
        if (!empty($street)) $item[] = $street;

        // add street number
        if (!empty($streetNumber)) $item[] = $streetNumber;

        // add city
        if (!empty($city)) $item[] = $city;

        // add zip
        if (!empty($zip)) $item[] = $zip;

        // add country
        if (!empty($country)) $item[] = $country;

        // define value
        $address = implode(' ', $item);

        // define result
        $results = $this->doCall(array(
            'address' => $address,
            'sensor' => 'false'
        ));

        if (!array_key_exists(0, $results)) {
            throw Exception::noCoordinatesFoundforAddress([$street, $streetNumber, $city, $zip, $country]);
        }

        return new Coordinates(
            $results[0]->geometry->location->lat,
            $results[0]->geometry->location->lng
        );
    }
}
