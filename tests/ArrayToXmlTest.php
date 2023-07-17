<?php

namespace Spatie\ArrayToXml\Test;

use DOMException;
use Exception;
use Spatie\ArrayToXml\ArrayToXml;
use function Spatie\Snapshots\assertMatchesSnapshot;
use function Spatie\Snapshots\assertMatchesXmlSnapshot;
use Spatie\Snapshots\MatchesSnapshots;

uses(MatchesSnapshots::class);

beforeEach(function () {
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
});

it('can convert an array to xml', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert($this->testArray));
});

it('can handle an empty array', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([]));
});

it('can receive name for the root element', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([], 'helloyouluckpeople'));
});

it('can receive name from array for the root element', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([], [
        'rootElementName' => 'helloyouluckpeople',
    ]));
});

it('can convert attributes to xml for the root element', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([], [
        '_attributes' => [
            'xmlns' => 'https://github.com/spatie/array-to-xml',
        ],
    ]));
});

test('and root element attributes can also be set in SimpleXMLElement style', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([], [
        '@attributes' => [
            'xmlns' => 'https://github.com/spatie/array-to-xml',
        ],
    ]));
});

it('throws an exception when converting an array with no keys', function () {
    ArrayToXml::convert(['one', 'two', 'three']);
})->throws(DOMException::class);

it('throws an exception when converting an array with invalid characters key names', function () {
    echo ArrayToXml::convert(['tom & jerry' => 'cartoon characters'], '', false);
})->throws(DOMException::class);

it('will raise an exception when spaces should not be replaced and a key contains a space', function () {
    ArrayToXml::convert($this->testArray, '', false);
})->throws(DOMException::class);

it('can handle values as basic collection', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([
        'user' => ['one', 'two', 'three'],
    ]));
});

it('can handle zero values in beginning of basic collection', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([
        'user' => ['0', '1', '0'],
    ]));
});

it('can handle values with namespace as basic collection', function () {
    $root = [
        'rootElementName' => 'root',
        '_attributes' => [
            'xmlns:ns1' => 'https://github.com/spatie/array-to-xml',
        ],
    ];

    assertMatchesXmlSnapshot(ArrayToXml::convert([
        'ns1:user' => ['one', 'two', 'three'],
    ], $root));
});

it('accepts an xml encoding type', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([], '', false, 'UTF-8'));
});

it('accepts an xml version', function () {
    assertMatchesSnapshot(ArrayToXml::convert([], '', false, null, '1.1'));
});

it('accepts an xml standalone value', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([], '', false, null, '1.0', [], false));
});

it('accepts an xml declaration value', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([], '', false, null, '1.0', [], null, false));
});

it('can handle values as collection', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([
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
});

it('can handle values with special characters', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert(['name' => 'this & that']));
});

it('can handle values with special control characters', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert(['name' => "i want tothis and \x03 that"]));
});

it('can group by values when values are in a numeric array', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert(['user' => ['foo', 'bar']]));
});

it('can convert attributes to xml', function () {
    $withAttributes = $this->testArray;
    $withAttributes['Good guy']['_attributes'] = ['nameType' => 1];

    assertMatchesXmlSnapshot(ArrayToXml::convert($withAttributes));
});

it('can handle attributes as collection', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([
        'user' => [
            [
                '_attributes' => [
                    'name' => 'een',
                    'age' => 10,
                ],
            ],
            [
                '_attributes' => [
                    'name' => 'twee',
                    'age' => 12,
                ],
            ],
        ],
    ]));
});

test('and attributes also can be set in SimpleXMLElement style', function () {
    $withAttributes = $this->testArray;
    $withAttributes['Good guy']['@attributes'] = ['nameType' => 1];

    assertMatchesXmlSnapshot(ArrayToXml::convert($withAttributes));
});

it('can handle values set with attributes with special characters', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([
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
});

test('and value also can be set in SimpleXMLElement style', function () {
    assertMatchesXmlSnapshot(ArrayToXml::convert([
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
});

it('can handle values set as cdata', function () {
    assertMatchesSnapshot(ArrayToXml::convert([
        'movie' => [
            [
                'title' => [
                    '_attributes' => ['category' => 'SF'],
                    '_cdata' => '<p>STAR WARS</p>',
                ],
            ],
            [
                'title' => [
                    '_attributes' => ['category' => 'Children'],
                    '_cdata' => '<p>tom & jerry</p>',
                ],
            ],
        ],
    ]));
});

test('and cdata values can also be set in SimpleXMLElement style', function () {
    assertMatchesSnapshot(ArrayToXml::convert([
        'movie' => [
            [
                'title' => [
                    '@attributes' => ['category' => 'SF'],
                    '@cdata' => '<p>STAR WARS</p>',
                ],
            ],
            [
                'title' => [
                    '@attributes' => ['category' => 'Children'],
                    '@cdata' => '<p>tom & jerry</p>',
                ],
            ],
        ],
    ]));
});

it('doesnt pollute attributes in collection and sequential nodes', function () {
    assertMatchesSnapshot(ArrayToXml::convert([
        'books' => [
            'book' => [
                ['name' => 'A', '@attributes' => ['z' => 1]],
                ['name' => 'B'],
                ['name' => 'C'],
            ],
        ],
    ]));
});

it('can convert array to dom', function () {
    $resultDom = (new ArrayToXml($this->testArray))->toDom();

    expect($resultDom->getElementsByTagName('name')->item(0)->nodeValue)->toBe('Luke Skywalker');
    expect($resultDom->getElementsByTagName('name')->item(1)->nodeValue)->toBe('Sauron');
    expect($resultDom->getElementsByTagName('weapon')->item(0)->nodeValue)->toBe('Lightsaber');
    expect($resultDom->getElementsByTagName('weapon')->item(1)->nodeValue)->toBe('Evil Eye');
});

it('can handle values set as mixed', function () {
    assertMatchesSnapshot(ArrayToXml::convert([
        'movie' => [
            [
                'title' => [
                    '@attributes' => ['category' => 'SF'],
                    '_mixed' => 'STAR WARS <xref ref-type="fig" rid="f1">Figure 1</xref>',
                ],
            ],
            [
                'title' => [
                    '@attributes' => ['category' => 'Action'],
                    '_mixed' => 'ROBOCOP <xref ref-type="fig" rid="f2">Figure 2</xref>',
                ],
            ],
        ],
    ]));
});

it('can handle closure values', function () {
    $users = [
        [
            'name' => 'one',
            'age' => 10,
        ],
        [
            'name' => 'two',
            'age' => 12,
        ],
    ];

    assertMatchesXmlSnapshot(ArrayToXml::convert([
        'users' => function () use ($users) {
            $new_users = [];
            foreach ($users as $user) {
                $new_users[] = array_merge(
                    $user,
                    [
                        'double_age' => $user['age'] * 2,
                    ]
                );
            }

            return $new_users;
        },
    ]));
});

test('and mixed values can also be set in SimpleXMLElement style', function () {
    assertMatchesSnapshot(ArrayToXml::convert([
        'movie' => [
            [
                'title' => [
                    '@attributes' => ['category' => 'SF'],
                    '@mixed' => 'STAR WARS <xref ref-type="fig" rid="f1">Figure 1</xref>',
                ],
            ],
            [
                'title' => [
                    '@attributes' => ['category' => 'Action'],
                    '@mixed' => 'ROBOCOP <xref ref-type="fig" rid="f2">Figure 2</xref>',
                ],
            ],
        ],
    ]));
});

it('can handle numeric keys', function () {
    assertMatchesSnapshot(ArrayToXml::convert([
        '__numeric' => [
            16 => [
                'parent' => 'aaa',
                'numLinks' => 3,
                'child' => [
                    16 => [
                        'parent' => 'abc',
                        'numLinks' => 3,
                    ],
                ],
            ],
            17 => [
                'parent' => 'bb',
                'numLinks' => 3,
                'child' => [
                    16 => [
                        'parent' => 'abb',
                        'numLinks' => 3,
                        'child' => [
                            16 => [
                                'parent' => 'abc',
                                'numLinks' => 3,
                            ],
                        ],
                    ],
                    17 => [
                        'parent' => 'acb',
                        'numLinks' => 3,
                    ],
                ],
            ],
        ],
    ]));
});

it('can handle custom keys', function () {
    assertMatchesSnapshot(ArrayToXml::convert([
        '__custom:custom-key:01' => [
            'parent' => 'aaa',
            'numLinks' => 3,
            'child' => [
                16 => [
                    'parent' => 'abc',
                    'numLinks' => 3,
                ],
            ],
        ],
        '__custom:custom-key:02' => [
            'parent' => 'bb',
            'numLinks' => 3,
            'child' => [
                '__custom:custom-subkey:01' => [
                    'parent' => 'abb',
                    'numLinks' => 3,
                    'child' => [
                        '__custom:custom-subsubkey:01' => [
                            'parent' => 'abc',
                            'numLinks' => 3,
                        ],
                    ],
                ],
                '__custom:custom-subkey:02' => [
                    'parent' => 'acb',
                    'numLinks' => 3,
                ],
            ],
        ],
    ]));
});

it('can handle custom keys containing colon character', function () {
    assertMatchesSnapshot(ArrayToXml::convert([
        '__custom:custom\:key:01' => [
            'parent' => 'aaa',
            'numLinks' => 3,
            'child' => [
                16 => [
                    'parent' => 'abc',
                    'numLinks' => 3,
                ],
            ],
        ],
        '__custom:custom\:key:02' => [
            'parent' => 'bb',
            'numLinks' => 3,
            'child' => [
                '__custom:custom\:subkey:01' => [
                    'parent' => 'abb',
                    'numLinks' => 3,
                    'child' => [
                        '__custom:custom\:subsubkey:01' => [
                            'parent' => 'abc',
                            'numLinks' => 3,
                        ],
                    ],
                ],
                '__custom:custom\:subkey:02' => [
                    'parent' => 'acb',
                    'numLinks' => 3,
                ],
            ],
        ],
    ]));
});

test('Setting invalid properties will result in an exception', function () {
    $xml2Array = new ArrayToXml($this->testArray);
    $xml2Array->setDomProperties(['foo' => 'bar']);
})->throws(Exception::class);

it('can set dom properties', function () {
    $xml2Array = new ArrayToXml($this->testArray);
    $xml2Array->setDomProperties([
        'formatOutput' => true,
        'version' => '1234567',
    ]);

    $dom = $xml2Array->toDom();
    expect($dom->formatOutput)->toBeTrue();
    expect($dom->version)->toBe('1234567');
});

it('can drop xml declaration', function () {
    $root = [
        'rootElementName' => 'soap:Envelope',
        '_attributes' => [
            'xmlns:soap' => 'http://www.w3.org/2003/05/soap-envelope/',
        ],
    ];
    $array = [
        'soap:Header' => [],
        'soap:Body' => [
            'soap:key' => 'soap:value',
        ],
    ];
    $arrayToXml = new ArrayToXml($array, $root);

    assertMatchesSnapshot($arrayToXml->dropXmlDeclaration()->toXml());
});

it('can convert an array with null value to xml', function () {
    $arr = [
        'test' => null,
    ];

    assertMatchesXmlSnapshot(ArrayToXml::convert($arr));
});

it('can add processing instructions', function () {
    $arrayToXml = new ArrayToXml($this->testArray);
    $arrayToXml->addProcessingInstruction('xml-stylesheet', 'type="text/xsl" href="base.xsl"');

    assertMatchesSnapshot($arrayToXml->toXml());
});
