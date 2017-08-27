<?php

use PHPUnit\Framework\TestCase;
use Spatie\ArrayToXml\ArrayToXml;
use Spatie\Snapshots\MatchesSnapshots;

class ArrayToXmlTest extends TestCase
{
    use MatchesSnapshots;

    /** @test array */
    protected $testArray = [];

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

    /** @test */
    public function it_can_convert_an_array_to_xml()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert($this->testArray));
    }

    /** @test */
    public function it_can_handle_an_empty_array()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert([]));
    }

    /** @test */
    public function it_can_receive_name_for_the_root_element()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert([], 'helloyouluckpeople'));
    }

    /** @test */
    public function it_can_receive_name_from_array_for_the_root_element()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert([], [
            'rootElementName' => 'helloyouluckpeople',
        ]));
    }

    /** @test */
    public function it_can_convert_attributes_to_xml_for_the_root_element()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert([], [
            '_attributes' => [
                'xmlns' => 'https://github.com/spatie/array-to-xml',
            ],
        ]));
    }

    /** @test */
    public function and_root_element_attributes_can_also_be_set_in_simplexmlelement_style()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert([], [
            '@attributes' => [
                'xmlns' => 'https://github.com/spatie/array-to-xml',
            ],
        ]));
    }

    /** @test */
    public function it_throws_an_exception_when_converting_an_array_with_no_keys()
    {
        $this->expectException('DOMException');

        ArrayToXml::convert(['one', 'two', 'three']);
    }

    /** @test */
    public function it_throws_an_exception_when_converting_an_array_with_invalid_characters_key_names()
    {
        $this->expectException('DOMException');

        echo ArrayToXml::convert(['tom & jerry' => 'cartoon characters'], '', false);
    }

    /** @test */
    public function it_will_raise_an_exception_when_spaces_should_not_be_replaced_and_a_key_contains_a_space()
    {
        $this->expectException('DOMException');

        ArrayToXml::convert($this->testArray, '', false);
    }

    /** @test */
    public function it_can_handle_values_as_basic_collection()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert([
            'user' => ['one', 'two', 'three'],
        ]));
    }

    /** @test */
    public function it_accepts_an_xml_encoding_type()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert([], '', false, 'UTF-8'));
    }

    /** @test */
    public function it_accepts_an_xml_version()
    {
        $this->assertMatchesSnapshot(ArrayToXml::convert([], '', false, null, '1.1'));
    }

    /** @test */
    public function it_can_handle_values_as_collection()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert([
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
        ]));
    }

    /** @test */
    public function it_will_raise_an_exception_when_value_contains_mixed_sequential_array()
    {
        $this->expectException('DOMException');

        ArrayToXml::convert([
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
        ]);
    }

    /** @test */
    public function it_can_handle_values_with_special_characters()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert(['name' => 'this & that']));
    }

    /**
     * @test
     */
    public function it_can_group_by_values_when_values_are_in_a_numeric_array()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert(['user' => ['foo', 'bar']]));
    }

    /** @test */
    public function it_can_convert_attributes_to_xml()
    {
        $withAttributes = $this->testArray;

        $withAttributes['Good guy']['_attributes'] = ['nameType' => 1];

        $this->assertMatchesXmlSnapshot(ArrayToXml::convert($withAttributes));
    }

    /** @test */
    public function it_can_handle_attributes_as_collection()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert([
            'user' => [
                [
                    '_attributes' => [
                        'name' => 'een',
                        'age' => 10,
                    ]
                ],
                [
                    '_attributes' => [
                        'name' => 'twee',
                        'age' => 12,
                    ]
                ],
            ],
        ]));
    }

    /** @test */
    public function and_attributes_also_can_be_set_in_simplexmlelement_style()
    {
        $withAttributes = $this->testArray;

        $withAttributes['Good guy']['@attributes'] = ['nameType' => 1];

        $this->assertMatchesXmlSnapshot(ArrayToXml::convert($withAttributes));
    }

    /** @test */
    public function it_can_handle_values_set_with_attributes_with_special_characters()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert([
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
        ]));
    }

    /** @test */
    public function and_value_also_can_be_set_in_simplexmlelement_style()
    {
        $this->assertMatchesXmlSnapshot(ArrayToXml::convert([
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
        ]));
    }
}
