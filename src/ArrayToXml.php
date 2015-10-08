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
     * @var \DOMDocument
     */
    protected $document;

    /**
     * Set to enable replacing space with underscore.
     *
     * @var bool
     */
    protected $replaceSpacesByUnderScoresInKeyNames = true;

    /**
     * Construct a new instance.
     *
     * @param string[] $array
     * @param string   $rootElementName
     * @param bool     $replaceSpacesByUnderScoresInKeyNames
     *
     * @throws DOMException
     */
    public function __construct(array $array, $rootElementName = '', $replaceSpacesByUnderScoresInKeyNames = true)
    {
        $this->document = new DOMDocument();
        $this->replaceSpacesByUnderScoresInKeyNames = $replaceSpacesByUnderScoresInKeyNames;

        if ($this->isArrayAllKeySequential($array) && ! empty($array)) {
            throw new DOMException("Invalid Character Error");
        }

        $root = $this->document->createElement($rootElementName == '' ? 'root' : $rootElementName);

        $this->document->appendChild($root);

        $this->convertElement($root, $array);
    }

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
        $converter = new static($array, $rootElementName, $replaceSpacesByUnderScoresInKeyNames);

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
     * Parse individual element.
     *
     * @param \DOMElement     $element
     * @param string|string[] $value
     */
    private function convertElement(DOMElement $element, $value)
    {
        $sequential = $this->isArrayAllKeySequential($value);

        if (! is_array($value)) {
            $element->nodeValue = htmlspecialchars($value);

            return;
        }

        foreach ($value as $key => $data) {
            if (! $sequential) {
                $this->addNode($element, $key, $data);
            } elseif (is_array($data)) {
                $this->addCollectionNode($element, $data);
            } else {
                $this->addSequentualNode($element, $data);
            }
        }
    }

    /**
     * Add node.
     *
     * @param \DOMElement     $element
     * @param string          $key
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
     * @param \DOMElement     $element
     * @param string|string[] $value
     *
     * @internal param string $key
     */
    protected function addCollectionNode(DOMElement $element, $value)
    {
        if ($element->childNodes->length == 0) {
            $this->convertElement($element, $value);

            return;
        }

        $child = $element->cloneNode();
        $element->parentNode->appendChild($child);
        $this->convertElement($child, $value);
    }

    /**
     * Add sequential node.
     *
     * @param \DOMElement     $element
     * @param string|string[] $value
     *
     * @internal param string $key
     */
    protected function addSequentualNode(DOMElement $element, $value)
    {
        if (empty($element->nodeValue)) {
            $element->nodeValue = htmlspecialchars($value);

            return;
        }

        $child = $element->cloneNode();
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

        return array_unique(array_map("is_int", array_keys($value))) === array(true);
    }
}
