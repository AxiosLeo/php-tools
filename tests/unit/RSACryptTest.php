<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class RSACryptTest extends TestCase
{
    public function testRSACrypt1()
    {
        $rsa = new \axios\tools\RSACrypt();
        $rsa->create();
        $data       = 'test';
        $encryptStr = $rsa->encryptByPrivateKey($data);
        $this->assertEquals($data, $rsa->decryptByPublicKey($encryptStr));
    }

    public function testRSACrypt2()
    {
        $rsa = new \axios\tools\RSACrypt();
        $rsa->create();
        $data       = 'test';
        $encryptStr = $rsa->encryptByPublicKey($data);
        $this->assertEquals($data, $rsa->decryptByPrivateKey($encryptStr));
    }
}
