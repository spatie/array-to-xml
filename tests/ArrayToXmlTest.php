<?php

use Spatie\ArrayToXml\ArrayToXml;

class ArrayToXmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function it_can_convert_an_array_to_xml()
    {
        $array = [
            'Good guy' => [

                'name' => 'Luke Skywalker',
                'weapon' => 'Lightsaber'

            ],
            'Bad guy' => [

                'name' => 'Sauron',
                'weapon' => 'Evil Eye'

            ]
        ];

        $expectedXml = '<?xml version="1.0"?>
<root><Good_guy><name>Luke Skywalker</name><weapon>Lightsaber</weapon></Good_guy><Bad_guy><name>Sauron</name><weapon>Evil Eye</weapon></Bad_guy></root>' . PHP_EOL;

        $result = ArrayToXml::convert($array);

        $this->assertEquals($expectedXml, $result);
    }

    /**
     * @test
     */
    function it_can_handle_an_empty_array()
    {
        $array = [];

        $expectedXml = '<?xml version="1.0"?>
<root/>' . PHP_EOL;

        $result = ArrayToXml::convert($array);

        $this->assertEquals($expectedXml, $result);
    }

    /**
     * @test
     */
    function it_can_receive_name_for_the_root_element()
    {
        $rootElementName = 'helloyouluckpeople';

        $array = [];

        $expectedXml = '<?xml version="1.0"?>
<' . $rootElementName . '/>' . PHP_EOL;

        $result = ArrayToXml::convert($array, $rootElementName);

        $this->assertEquals($expectedXml, $result);
    }

}