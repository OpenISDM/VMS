<?php

use App\Utils\StringUtil;

class StirngUtilTest extends TestCase
{
    public function testHighlightKeyword()
    {
        $this->assertEquals('<strong>Hel</strong>lo world, PHP!',
            StringUtil::highlightKeyword('Hel', 'Hello world, PHP!'));
    }
}
