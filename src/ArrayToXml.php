<?php

namespace Spatie\ArrayToXml;

use DOMElement;
use DOMDocument;
use DOMException;

class ArrayToXml
{
    /**
     * The root DOM Document.
     *
     * @var DOMDocument
     */
    protected $document;

    /**
     * Set to enable replacing space with underscore.
     *
     * @var bool
     */
    protected $replaceSpacesByUnderScoresInKeyNames = true;

    /**
     * Prefix for the tags with numeric names.
     *
     * @var string
     */
    protected $numericTagNamePrefix = 'numeric_';

    /**
     * Construct a new instance.
     *
     * @param string[] $array
     * @param string|array $rootElement
     * @param bool $replaceSpacesByUnderScoresInKeyNames
     * @param string $xmlEncoding
     * @param string $xmlVersion
     * @param array $domProperties
     *
     * @throws DOMException
     */
    public function __construct(array $array, $rootElement = '', $replaceSpacesByUnderScoresInKeyNames = true, $xmlEncoding = null, $xmlVersion = '1.0', $domProperties = [])
    {
        $this->document = new DOMDocument($xmlVersion, $xmlEncoding);

        if (!empty($domProperties)) {
            $this->setDomProperties($domProperties);
        }

        $this->replaceSpacesByUnderScoresInKeyNames = $replaceSpacesByUnderScoresInKeyNames;

        if ($this->isArrayAllKeySequential($array) && ! empty($array)) {
            throw new DOMException('Invalid Character Error');
        }

        $root = $this->createRootElement($rootElement);

        $this->document->appendChild($root);

        $this->convertElement($root, $array);
    }

    public function setNumericTagNamePrefix(string $prefix)
    {
        $this->numericTagNamePrefix = $prefix;
    }

    /**
     * Convert the given array to an xml string.
     *
     * @param array $array
     * @param string $rootElement
     * @param bool $replaceSpacesByUnderScoresInKeyNames
     * @param null $xmlEncoding
     * @param string $xmlVersion
     * @param array $domProperties
     * @return string
     *
     * @throws DOMException
     */
    public static function convert(array $array, $rootElement = '', $replaceSpacesByUnderScoresInKeyNames = true, $xmlEncoding = null, $xmlVersion = '1.0', $domProperties = [])
    {
        $converter = new static($array, $rootElement, $replaceSpacesByUnderScoresInKeyNames, $xmlEncoding, $xmlVersion, $domProperties);

        return $converter->toXml();
    }

    /**
     * Return as XML.
     *
     * @return string
     */
    public function toXml()
    {
        return $this->document->saveXML();
    }

    /**
     * Return as DOM object.
     *
     * @return DOMDocument
     */
    public function toDom()
    {
        return $this->document;
    }

    /**
     * Sets dom properties on $this->document
     *
     * @param $domProperties
     * @throws \Exception
     */
    public function setDomProperties($domProperties) {
        foreach ($domProperties as $key => $value) {
            if (!property_exists($this->document, $key)) {
                throw new \Exception($key . ' is not a valid property of DOMDocument', 1561114534);
            }
            $this->document->{$key} = $value;
        }
    }

    /**
     * Parse individual element.
     *
     * @param DOMElement $element
     * @param string|string[] $value
     */
    private function convertElement(DOMElement $element, $value)
    {
        $sequential = $this->isArrayAllKeySequential($value);

        if (! is_array($value)) {
            $value = htmlspecialchars($value);

            $value = $this->removeControlCharacters($value);

            $element->nodeValue = $value;

            return;
        }

        foreach ($value as $key => $data) {
            if (! $sequential) {
                if (($key === '_attributes') || ($key === '@attributes')) {
                    $this->addAttributes($element, $data);
                } elseif ((($key === '_value') || ($key === '@value')) && is_string($data)) {
                    $element->nodeValue = htmlspecialchars($data);
                } elseif ((($key === '_cdata') || ($key === '@cdata')) && is_string($data)) {
                    $element->appendChild($this->document->createCDATASection($data));
                } elseif ((($key === '_mixed') || ($key === '@mixed')) && is_string($data)) {
                    $fragment = $this->document->createDocumentFragment();
                    $fragment->appendXML($data);
                    $element->appendChild($fragment);
                } elseif ($key === '__numeric') {
                    $this->addNumericNode($element, $data);
                } else {
                    $this->addNode($element, $key, $data);
                }
            } elseif (is_array($data)) {
                $this->addCollectionNode($element, $data);
            } else {
                $this->addSequentialNode($element, $data);
            }
        }
    }

    /**
     * Add node with numeric keys.
     *
     * @param DOMElement $element
     * @param string|string[] $value
     */
    protected function addNumericNode(DOMElement $element, $value)
    {
        foreach ($value as $key => $item) {
            $this->convertElement($element, [$this->numericTagNamePrefix.$key => $value]);
        }
    }

    /**
     * Add node.
     *
     * @param DOMElement $element
     * @param string $key
     * @param string|string[] $value
     */
    protected function addNode(DOMElement $element, $key, $value)
    {
        if ($this->replaceSpacesByUnderScoresInKeyNames) {
            $key = str_replace(' ', '_', $key);
        }

        $child = $this->document->createElement($key);
        $element->appendChild($child);
        $this->convertElement($child, $value);
    }

    /**
     * Add collection node.
     *
     * @param DOMElement $element
     * @param string|string[] $value
     *
     * @internal param string $key
     */
    protected function addCollectionNode(DOMElement $element, $value)
    {
        if ($element->childNodes->length === 0 && $element->attributes->length === 0) {
            $this->convertElement($element, $value);

            return;
        }

        $child = $this->document->createElement($element->tagName);
        $element->parentNode->appendChild($child);
        $this->convertElement($child, $value);
    }

    /**
     * Add sequential node.
     *
     * @param DOMElement $element
     * @param string|string[] $value
     *
     * @internal param string $key
     */
    protected function addSequentialNode(DOMElement $element, $value)
    {
        if (empty($element->nodeValue)) {
            $element->nodeValue = htmlspecialchars($value);

            return;
        }

        $child = new DOMElement($element->tagName);
        $child->nodeValue = htmlspecialchars($value);
        $element->parentNode->appendChild($child);
    }

    /**
     * Check if array are all sequential.
     *
     * @param array|string $value
     *
     * @return bool
     */
    protected function isArrayAllKeySequential($value)
    {
        if (! is_array($value)) {
            return false;
        }

        if (count($value) <= 0) {
            return true;
        }

        if (\key($value) === '__numeric') {
            return false;
        }

        return array_unique(array_map('is_int', array_keys($value))) === [true];
    }

    /**
     * Add attributes.
     *
     * @param DOMElement $element
     * @param string[] $data
     */
    protected function addAttributes($element, $data)
    {
        foreach ($data as $attrKey => $attrVal) {
            $element->setAttribute($attrKey, $attrVal);
        }
    }

    /**
     * Create the root element.
     *
     * @param  string|array $rootElement
     * @return DOMElement
     */
    protected function createRootElement($rootElement)
    {
        if (is_string($rootElement)) {
            $rootElementName = $rootElement ?: 'root';

            return $this->document->createElement($rootElementName);
        }

        $rootElementName = $rootElement['rootElementName'] ?? 'root';

        $element = $this->document->createElement($rootElementName);

        foreach ($rootElement as $key => $value) {
            if ($key !== '_attributes' && $key !== '@attributes') {
                continue;
            }

            $this->addAttributes($element, $rootElement[$key]);
        }

        return $element;
    }

    /**
     * @param $valuet
     * @return string
     */
    protected function removeControlCharacters($value)
    {
        return preg_replace('/[\x00-\x09\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
    }
}
