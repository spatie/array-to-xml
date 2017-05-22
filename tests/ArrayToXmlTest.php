<?php

use Spatie\ArrayToXml\ArrayToXml;

class ArrayToXmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var array
     */
    protected $testArray;

    public function setUp()
    {
        $this->testArray = [
            'Good guy' => [

                'name' => 'Luke Skywalker',
                'weapon' => 'Lightsaber',

            ],
            'Bad guy' => [
                'name' => 'Sauron',
                'weapon' => 'Evil Eye',

            ],
        ];
    }

    /**
     * @test
     */
    public function it_can_convert_an_array_to_xml()
    {
        $expectedXml = '<?xml version="1.0"?>
<root><Good_guy><name>Luke Skywalker</name><weapon>Lightsaber</weapon></Good_guy><Bad_guy><name>Sauron</name><weapon>Evil Eye</weapon></Bad_guy></root>'.PHP_EOL;

        $result = ArrayToXml::convert($this->testArray);

        $this->assertEquals($expectedXml, $result);
    }

    /**
     * @test
     */
    public function it_can_handle_an_empty_array()
    {
        $array = [];

        $expectedXml = '<?xml version="1.0"?>
<root/>'.PHP_EOL;

        $result = ArrayToXml::convert($array);

        $this->assertEquals($expectedXml, $result);
    }

    /**
     * @test
     */
    public function it_can_receive_name_for_the_root_element()
    {
        $rootElementName = 'helloyouluckpeople';

        $array = [];

        $expectedXml = '<?xml version="1.0"?>
<'.$rootElementName.'/>'.PHP_EOL;

        $result = ArrayToXml::convert($array, $rootElementName);

        $this->assertEquals($expectedXml, $result);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_converting_an_array_with_no_keys()
    {
        $this->setExpectedException('DOMException');

        ArrayToXml::convert(['one', 'two', 'three']);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_converting_an_array_with_invalid_characters_key_names()
    {
        $this->setExpectedException('DOMException');

        echo ArrayToXml::convert(['tom & jerry' => 'cartoon characters'], '', false);
    }

    /**
     * @test
     */
    public function it_will_raise_an_exception_when_spaces_should_not_be_replaced_and_a_key_contains_a_space()
    {
        $this->setExpectedException('DOMException');

        ArrayToXml::convert($this->testArray, '', false);
    }

    /**
     * @test
     */
    public function it_can_handle_values_as_basic_collection()
    {
        $array = ['user' => ['een', 'twee', 'drie']];

        $expectedXml = '<?xml version="1.0"?>
<root><user>een</user><user>twee</user><user>drie</user></root>'.PHP_EOL;

        $result = ArrayToXml::convert($array);

        $this->assertEquals($expectedXml, $result);
    }

    /**
     * @test
     */
    public function it_can_handle_values_as_collection()
    {
        $array = [
            'user' => [
                [
                    'name' => 'een',
                    'age' => 10,
                ],
                [
                    'name' => 'twee',
                    'age' => 12,
                ],
            ],
        ];

        $expectedXml = '<?xml version="1.0"?>
<root><user><name>een</name><age>10</age></user><user><name>twee</name><age>12</age></user></root>'.PHP_EOL;

        $result = ArrayToXml::convert($array);

        $this->assertEquals($expectedXml, $result);
    }

    /**
     * @test
     */
    public function it_will_raise_an_exception_when_value_contains_mixed_sequential_array()
    {
        $this->setExpectedException('DOMException');

        $array = [
            'user' => [
                [
                    'name' => 'een',
                    'age' => 10,
                ],
                'twee' => [
                    'name' => 'twee',
                    'age' => 12,
                ],
            ],
        ];

        ArrayToXml::convert($array);
    }

    /**
     * @test
     */
    public function it_can_handle_values_with_special_characters()
    {
        $array = ['name' => 'this & that'];

        $expectedXml = '<?xml version="1.0"?>
<root><name>this &amp; that</name></root>'.PHP_EOL;

        $result = ArrayToXml::convert($array);

        $this->assertEquals($expectedXml, $result);
    }

    /**
     * @test
     */
    public function it_can_group_by_values_when_values_are_in_a_numeric_array()
    {
        $array = ['user' => ['foo', 'bar']];

        $expectedXml = '<?xml version="1.0"?>
<root><user>foo</user><user>bar</user></root>'.PHP_EOL;

        $result = ArrayToXml::convert($array);

        $this->assertEquals($expectedXml, $result);
    }

    /**
     * @test
     */
    public function it_can_convert_attributes_to_xml()
    {
        $expectedXml = '<?xml version="1.0"?>
<root><Good_guy nameType="1"><name>Luke Skywalker</name><weapon>Lightsaber</weapon></Good_guy><Bad_guy><name>Sauron</name><weapon>Evil Eye</weapon></Bad_guy></root>'.PHP_EOL;
        $withAttributes = $this->testArray;
        $withAttributes['Good guy']['_attributes'] = ['nameType' => 1];
        $result = ArrayToXml::convert($withAttributes);

        $this->assertEquals($expectedXml, $result);
    }

    /**
     * @test
     */
    public function and_attributes_also_can_be_set_in_simplexmlelement_style()
    {
        $expectedXml = '<?xml version="1.0"?>
<root><Good_guy nameType="1"><name>Luke Skywalker</name><weapon>Lightsaber</weapon></Good_guy><Bad_guy><name>Sauron</name><weapon>Evil Eye</weapon></Bad_guy></root>'.PHP_EOL;
        $withAttributes = $this->testArray;
        $withAttributes['Good guy']['@attributes'] = ['nameType' => 1];
        $result = ArrayToXml::convert($withAttributes);

        $this->assertEquals($expectedXml, $result);
    }

    /**
     * @test
     */
    public function it_can_handle_values_set_with_attributes_with_special_characters()
    {
        $expectedXml = '<?xml version="1.0"?>
<root><movie><title category="SF">STAR WARS</title></movie><movie><title category="Children">tom &amp; jerry</title></movie></root>'.PHP_EOL;

        $array = [
            'movie' => [
                [
                    'title' => [
                        '_attributes' => ['category' => 'SF'],
                        '_value' => 'STAR WARS',
                    ],
                ],
                [
                    'title' => [
                        '_attributes' => ['category' => 'Children'],
                        '_value' => 'tom & jerry',
                    ],
                ],
            ],
        ];

        $result = ArrayToXml::convert($array);

        $this->assertEquals($expectedXml, $result);
    }

    /**
     * @test
     */
    public function and_value_also_can_be_set_in_simplexmlelement_style()
    {
        $expectedXml = '<?xml version="1.0"?>
<root><movie><title category="SF">STAR WARS</title></movie><movie><title category="Children">tom &amp; jerry</title></movie></root>'.PHP_EOL;

        $array = [
            'movie' => [
                [
                    'title' => [
                        '@attributes' => ['category' => 'SF'],
                        '@value' => 'STAR WARS',
                    ],
                ],
                [
                    'title' => [
                        '@attributes' => ['category' => 'Children'],
                        '@value' => 'tom & jerry',
                    ],
                ],
            ],
        ];

        $result = ArrayToXml::convert($array);

        $this->assertEquals($expectedXml, $result);
    }
}
