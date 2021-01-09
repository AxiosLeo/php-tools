<?php

declare (strict_types = 1);

namespace axios\tools\tests\unit;

use axios\tools\HMac;
use PHPUnit\Framework\TestCase;

class HMacTest extends TestCase
{
    public function testHMacSM3()
    {
        $hmac = new HMac();
        $hmac->registerAlgorithm('md5_sha256', function ($data) {
            return hash('sha256', md5($data));
        });

        $res = $hmac->count('md5_sha256', 'source', 'secret', false);
        $this->assertEquals("d6a810f338113f9a41995c3c052a834d51ada5f10aab89aa5ae302b2a21db0c2", $res);

        $res = $hmac->count('md5_sha256', 'source', 'secret', true);
        $this->assertEquals("d6a810f338113f9a41995c3c052a834d51ada5f10aab89aa5ae302b2a21db0c2", bin2hex($res));
    }
}