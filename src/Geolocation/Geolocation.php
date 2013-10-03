<?php

/**
 * Geolocation
 *
 * Get latitude/longitude or address using Google Maps API
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class Geolocation
{
	// API URL
	const API_URL = 'http://maps.googleapis.com/maps/api/geocode/json';

	/**
	 * Do call
	 *
	 * @param string $method
	 * @param array $headerOptions[optional]
	 * @return object
	 */
	protected static function doCall($method, $headerOptions = array())
	{
		// define url
		$url = self::API_URL . $method;

		// check if curl is available
		if(!function_exists('curl_init')) throw new GeolocationException('This method requires cURL (http://php.net/curl), it seems like the extension isn\'t installed.');

		// set options
		$options[CURLOPT_URL] = (string) $url;
		if(ini_get('open_basedir') == '' && ini_get('safe_mode' == 'Off')) $options[CURLOPT_FOLLOWLOCATION] = true;
		$options[CURLOPT_RETURNTRANSFER] = true;
		$options[CURLOPT_TIMEOUT] = 10;

		// any extra options provided?
		if($headerOptions !== null)
		{
			// loop the extra options
			foreach($headerOptions as $key => $value) $options[$key] = $value;
		}

		// init
		$curl = curl_init();

		// set options
		curl_setopt_array($curl, $options);

		// execute
		$response = curl_exec($curl);

		// fetch errors
		$errorNumber = curl_errno($curl);
		$errorMessage = curl_error($curl);

		// close
		curl_close($curl);

		// validate
		if($errorNumber != '') throw new GeolocationException($errorMessage);

		// redefine response as json decoded
		$response = json_decode($response);

		// return the content
		return $response->results;
	}
	
	/**
	 * Get address using latitude/longitude
	 *
	 * @param float $latitude
	 * @param float $longitude
	 * @return array(label, street, streetNumber, city, cityLocal, zip, country, countryLabel)
	 */
	public static function getAddress($latitude, $longitude)
	{
		// define result
		$results = self::doCall('?latlng=' . urlencode($latitude . ',' . $longitude) . '&sensor=false');

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
	 * @param string $street[optional]
	 * @param string $streetNumber[optional]
	 * @param string $city[optional]
	 * @param string $zip[optional]
	 * @param string $country[optional]
	 * @return array The latitude/longitude coordinates
	 */
	public static function getCoordinates($street = null, $streetNumber = null, $city = null, $zip = null, $country = null)
	{
		// init item
		$item = array();

		// add street
		if(!empty($street)) $item[] = $street;

		// add street number
		if(!empty($streetNumber)) $item[] = $streetNumber;

		// add city
		if(!empty($city)) $item[] = $city;

		// add zip
		if(!empty($zip)) $item[] = $zip;

		// add country
		if(!empty($country)) $item[] = $country;

		// define value
		$address = implode(' ', $item);

		// define result
		$results = self::doCall('?address=' . urlencode($address) . '&sensor=false');

		// return coordinates latitude/longitude
		return array(
			'latitude' => (float) $results[0]->geometry->location->lat,
			'longitude' => (float) $results[0]->geometry->location->lng
		);
	}
}


/**
 * Geolocation Exception
 *
 * @author Jeroen Desloovere <jeroen@siesqo.be>
 */
class GeolocationException extends Exception {}
