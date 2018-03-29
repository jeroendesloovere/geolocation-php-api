<?php

namespace JeroenDesloovere\Geolocation;

class Exception extends \Exception
{
    public static function curlNotInstalled(): Exception
    {
        return new self(
            'This method requires cURL (http://php.net/curl), it seems like the extension isn\'t installed.'
        );
    }

    public static function noAddressFoundForCoordinates($latitude, $longitude): Exception
    {
        return new self(
            'No address found for coordinates.
             With latitude: "' . $latitude . '" and longitude "' . $longitude . '".'
        );
    }

    public static function noCoordinatesFoundforAddress(array $addressData): Exception
    {
        return new self(
            'No coordinates found for address.
             With address: "' . implode(', ', $addressData) . '".'
        );
    }

    public static function overQueryLimit(): Exception
    {
        return new self(
            'You have exceeded your daily request quota for this API.
             We recommend registering for a key at the Google Developers Console:
             https://console.developers.google.com/apis/credentials?project=_'
        );
    }
}
