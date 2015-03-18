<?php

namespace Spatie\ArrayToXml;

use DOMDocument;

class ArrayToXml {

    /**
     * Convert the given array to an xml string
     * 
     * @param string[] $array
     * @param string $rootElementName
     * @return type
     */
    public static function convert(array $array, $rootElementName = '') {
        $DOMDocument = new DOMDocument();
        $root = $DOMDocument->createElement($rootElementName == '' ? 'root' : $rootElementName);
        foreach ($array as $key => $value) {
            $root->appendChild(self::convertElement($value, $key, $DOMDocument));
        }

        $DOMDocument->appendChild($root);
        return $DOMDocument->saveXML();
    }

    /**
     * Parse individual element
     * 
     * @param string|string[] $value
     * @param string $key
     * @param \DOMDocument $DOMDocument
     * @return \DOMElement
     */
    private static function convertElement($value, $key, DOMDocument $DOMDocument) {
        $key = str_replace(' ', '_', $key);
        $element = $DOMDocument->createElement($key);
        if (is_array($value)) {
            foreach ($value as $key => $value) {
                $element->appendChild(self::convertElement($value, $key, $DOMDocument));
            }
        } else {
            $element->nodeValue = $value;
        }
        return $element;
    }

}
