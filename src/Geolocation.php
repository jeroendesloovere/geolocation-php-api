<?php

namespace JeroenDesloovere\Geolocation;

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
     * @return object
     * @param  array  $parameters
     */
    protected static function doCall($parameters = array())
    {
        // check if curl is available
        if (!function_exists('curl_init')) {
            // throw error
            throw new GeolocationException('This method requires cURL (http://php.net/curl), it seems like the extension isn\'t installed.');
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
        if ($errorNumber != '') throw new GeolocationException($errorMessage);

        // redefine response as json decoded
        $response = json_decode($response);

        // return the content
        return $response->results;
    }

    /**
     * Get address using latitude/longitude
     *
     * @return array(label, street, streetNumber, city, cityLocal, zip, country, countryLabel)
     * @param  float        $latitude
     * @param  float        $longitude
     */
    public static function getAddress($latitude, $longitude)
    {
        // define result
        $results = self::doCall(array(
            'latlng' => $latitude . ',' . $longitude,
            'sensor' => 'false'
        ));

        // return address
        return array(
            'label' => (string) $results[0]->formatted_address,
            'street' => (string) $results[0]->address_components[1]->short_name,
            'streetNumber' => (string) $results[0]->address_components[0]->short_name,
            'city' => (string) $results[0]->address_components[3]->short_name,
            'cityLocal' => (string) $results[0]->address_components[2]->short_name,
            'zip' => (string) $results[0]->address_components[7]->short_name,
            'country' => (string) $results[0]->address_components[6]->short_name,
            'countryLabel' => (string) $results[0]->address_components[6]->long_name
        );
    }

    /**
     * Get coordinates latitude/longitude
     *
     * @return array  The latitude/longitude coordinates
     * @param  string $street[optional]
     * @param  string $streetNumber[optional]
     * @param  string $city[optional]
     * @param  string $zip[optional]
     * @param  string $country[optional]
     */
    public static function getCoordinates($street = null, $streetNumber = null, $city = null, $zip = null, $country = null)
    {
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
        $results = self::doCall(array(
            'address' => $address,
            'sensor' => 'false'
        ));

        // return coordinates latitude/longitude
        return array(
            'latitude' => array_key_exists(0, $results) ? (float) $results[0]->geometry->location->lat : null,
            'longitude' => array_key_exists(0, $results) ? (float) $results[0]->geometry->location->lng : null
        );
    }
}


/**
 * Geolocation Exception
 *
 * @author Jeroen Desloovere <info@jeroendesloovere.be>
 */
class GeolocationException extends \Exception {}
