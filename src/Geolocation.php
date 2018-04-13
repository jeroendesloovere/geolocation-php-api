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
    const API_URL = 'maps.googleapis.com/maps/api/geocode/json';

    /** @var string */
    private $apiKey;

    /** @var bool */
    private $https;

    public function __construct(string $apiKey = null, bool $https = true)
    {
        $this->https = $https;

        if ($apiKey !== null) {
            $this->apiKey = $apiKey;
            $this->https = true;
        }
    }

    private function createUrl(array $parameters): string
    {
        // define url
        $url = ($this->https ? 'https://' : 'http://') . self::API_URL . '?';

        // add every parameter to the url
        foreach ($parameters as $key => $value) {
            $url .= $key . '=' . urlencode($value) . '&';
        }

        // trim last &
        $url = trim($url, '&');

        if ($this->apiKey) {
            $url .= '&key=' . $this->apiKey;
        }

        return $url;
    }

    /**
     * Do call
     *
     * @param  array $parameters
     * @return mixed
     * @throws Exception
     */
    protected function doCall(array $parameters = array())
    {
        // check if curl is available
        if (!function_exists('curl_init')) {
            throw Exception::CurlNotInstalled();
        }

        // init curl
        $curl = curl_init();

        // set options
        curl_setopt($curl, CURLOPT_URL, $this->createUrl($parameters));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        if (ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) {
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        }

        // execute
        $response = curl_exec($curl);

        // fetch errors
        $errorNumber = curl_errno($curl);
        $errorMessage = curl_error($curl);

        // close curl
        curl_close($curl);

        // we have errors
        if ($errorNumber != '') {
            throw new Exception($errorMessage);
        }

        // redefine response as json decoded
        $response = json_decode($response);

        // API returns with an error
        if (isset($response->error_message)) {
            throw new Exception($response->error_message);
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
    public function getAddress(float $latitude, float $longitude): Address
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
    public function getAddresses(float $latitude, float $longitude): ?array
    {
        // init results
        $addresses = [];

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
        $items = [];
        $variables = [$street, $streetNumber, $city, $zip, $country];
        foreach ($variables as $variable) {
            if (empty($variable)) {
                continue;
            }

            $items[] = $variable;
        }

        $results = $this->doCall(array(
            'address' => implode(' ', $items),
            'sensor' => 'false'
        ));

        if (!array_key_exists(0, $results)) {
            throw Exception::noCoordinatesFoundforAddress($variables);
        }

        return new Coordinates(
            $results[0]->geometry->location->lat,
            $results[0]->geometry->location->lng
        );
    }
}
