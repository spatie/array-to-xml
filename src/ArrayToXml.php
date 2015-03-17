<?php

namespace Spatie\ArrayToXml;

use SimpleXMLElement;

class ArrayToXml
{
    /**
     * Convert the given array to an xml string
     *
     * @param $array
     * @param string $rootElementName
     * @param null $xml
     * @return string
     */
    public static function convert($array, $rootElementName = '', $xml = null)
    {
        if ($xml === null) {
            $xml = new SimpleXMLElement(self::getRootElement($rootElementName));
        }

        foreach ($array as $key => $value) {
            $key = str_replace(' ', '_', $key);

            if (is_array($value)) {
                self::convert($value, $key, $xml->addChild($key));
            } else {
                $xml->addChild($key, $value);
            }
        }

        return $xml->asXML();
    }

    /**
     * Get the root element for the given name
     *
     * @param $name
     * @return string
     */
    private static function getRootElement($name)
    {
        return '<'.($name == '' ? 'root' : $name).'/>';
    }
}
