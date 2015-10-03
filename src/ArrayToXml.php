<?php

namespace Spatie\ArrayToXml;

use DOMDocument;

class ArrayToXml
{
    /**
     * Convert the given array to an xml string.
     *
     * @param string[] $array
     * @param string   $rootElementName
     * @param bool     $replaceSpacesByUnderScoresInKeyNames
     *
     * @return type
     */
    public static function convert(array $array, $rootElementName = '', $replaceSpacesByUnderScoresInKeyNames = true)
    {
        $domDocument = new DOMDocument();

        $root = $domDocument->createElement($rootElementName == '' ? 'root' : $rootElementName);

        foreach ($array as $key => $value) {
            $root->appendChild(self::convertElement($value, $key, $domDocument, $replaceSpacesByUnderScoresInKeyNames));
        }

        $domDocument->appendChild($root);

        return $domDocument->saveXML();
    }

    /**
     * Parse individual element.
     *
     * @param string|string[] $value
     * @param string          $key
     * @param \DOMDocument    $domDocument
     * @param $replaceSpacesByUnderScoresInKeyNames
     *
     * @return \DOMElement
     */
    private static function convertElement($value, $key, DOMDocument $domDocument, $replaceSpacesByUnderScoresInKeyNames)
    {
        if ($replaceSpacesByUnderScoresInKeyNames) {
            $key = str_replace(' ', '_', $key);
        }

        $element = $domDocument->createElement($key);
        if (is_array($value)) {
            foreach ($value as $key => $value) {
                $element->appendChild(self::convertElement($value, $key, $domDocument, $replaceSpacesByUnderScoresInKeyNames));
            }
        } else {
            $element->nodeValue = htmlspecialchars($value);
        }

        return $element;
    }
}
