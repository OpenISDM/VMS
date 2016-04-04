<?php
use App\Utils\ArrayUtil;

class ArrayUtiltest extends TestCase
{
    public function testCombinedArray()
    {
        $data = [
            [
                'id' => 1,
                'a' => 'aOO',
                'b' => 'bOO',
                'y' => 'y00',
                'z' => 'z00',
            ],
            [
                'id' => 1,
                'a' => 'aOO',
                'b' => 'bOO',
                'y' => 'y11',
                'z' => 'z11',
            ],
            [
                'id' => 1,
                'a' => 'aOO',
                'b' => 'bOO',
                'y' => 'y22',
                'z' => 'z22',
            ],
            [
                'id' => 2,
                'a' => 'a01',
                'b' => 'b01',
                'y' => 'y33',
                'z' => 'z33',
            ],
            [
                'id' => 2,
                'a' => 'a01',
                'b' => 'b01',
                'y' => 'y44',
                'z' => 'z44',
            ],
        ];

        $expected = [
            1 => [
                'id' => 1,
                'a' => 'aOO',
                'b' => 'bOO',
                'qoo' => [
                    [
                        'y' => 'y00',
                        'z' => 'z00'
                    ],
                    [
                        'y' => 'y11',
                        'z' => 'z11'
                    ],
                    [
                        'y' => 'y22',
                        'z' => 'z22'
                    ]
                ]
            ],
            2 => [
                'id' => 2,
                'a' => 'a01',
                'b' => 'b01',
                'qoo' => [
                    [
                        'y' => 'y33',
                        'z' => 'z33'
                    ],
                    [
                        'y' => 'y44',
                        'z' => 'z44'
                    ]
                ]
            ]
        ];

        $actual = ArrayUtil::combinedArray($data, 'id', 'qoo', ['y', 'z']);

        $this->assertEquals($expected, $actual);
    }
}
