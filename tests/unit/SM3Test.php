<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\HMac;
use axios\tools\Path;
use axios\tools\SM3;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class SM3Test extends TestCase
{
    private ?SM3 $sm3;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sm3 = new SM3();
    }

    public function testEncodeWithEmptyString()
    {
        $this->sm3->encode('');
        $this->assertEquals(hex2bin('1ab21d8355cfa17f8e61194831e81a8f22bec8c728fefb747ed035eb5082aa2b'), $this->sm3->getBinary());
        $this->assertEquals('1ab21d8355cfa17f8e61194831e81a8f22bec8c728fefb747ed035eb5082aa2b', $this->sm3->getHex());
        $this->assertEquals('GrIdg1XPoX+OYRlIMegajyK+yMco/vt0ftA161CCqis=', $this->sm3->getBase64());
    }

    public function testEncode()
    {
        $this->sm3->encode('test');
        $this->assertEquals(hex2bin('55e12e91650d2fec56ec74e1d3e4ddbfce2ef3a65890c2a19ecf88a307e76a23'), $this->sm3->getBinary());
        $this->assertEquals('55e12e91650d2fec56ec74e1d3e4ddbfce2ef3a65890c2a19ecf88a307e76a23', $this->sm3->getHex());
        $this->assertEquals('VeEukWUNL+xW7HTh0+Tdv84u86ZYkMKhns+IowfnaiM=', $this->sm3->getBase64());
    }

    public function testEncodeWithFile()
    {
        $filepath = Path::join(__DIR__, '../../test.tmp');
        $this->write($filepath, 'test', 'w');
        $this->sm3->encodeFile($filepath);
        $this->assertEquals(hex2bin('55e12e91650d2fec56ec74e1d3e4ddbfce2ef3a65890c2a19ecf88a307e76a23'), $this->sm3->getBinary());
        $this->assertEquals('55e12e91650d2fec56ec74e1d3e4ddbfce2ef3a65890c2a19ecf88a307e76a23', $this->sm3->getHex());
        $this->assertEquals('VeEukWUNL+xW7HTh0+Tdv84u86ZYkMKhns+IowfnaiM=', $this->sm3->getBase64());
    }

    public function testEncodeWithHMac()
    {
        $hmac = new HMac();
        $hmac->registerAlgorithm('sm3', function ($str) {
            $sm3 = new SM3();
            $sm3->encode($str);

            return $sm3->getHex();
        });
        $this->assertEquals(
            '71e9db0344cd62427ccb824234214e14a0a54fe80adfb46bd12453270961dd5b',
            $hmac->count('sm3', '', 'secret')
        );
        $this->assertEquals(
            hex2bin('71e9db0344cd62427ccb824234214e14a0a54fe80adfb46bd12453270961dd5b'),
            $hmac->count('sm3', '', 'secret', true)
        );
        $this->assertEquals(
            '96042a28529e7a438af81eece5b293e0699f481fd372c08c5ac01b8dc4b81856',
            $hmac->count('sm3', 'abc', 'secret')
        );
        $this->assertEquals(
            hex2bin('96042a28529e7a438af81eece5b293e0699f481fd372c08c5ac01b8dc4b81856'),
            $hmac->count('sm3', 'abc', 'secret', true)
        );
        $this->assertEquals(
            '8e4bd77d8a10526fae772bb6014dfaed0335491e1cdfa92d3aca1481ae5d9a83',
            $hmac->count('sm3', str_repeat('abc', 1000), 'secret')
        );
        $this->assertEquals(
            hex2bin('8e4bd77d8a10526fae772bb6014dfaed0335491e1cdfa92d3aca1481ae5d9a83'),
            $hmac->count('sm3', str_repeat('abc', 1000), 'secret', true)
        );
    }

    private function write(string $filename, string $text, string $mode): void
    {
        if (!file_exists(\dirname($filename))) {
            @mkdir(\dirname($filename), 0755, true);
        }
        $fp = fopen($filename, $mode);
        if (flock($fp, LOCK_EX)) {
            fwrite($fp, $text);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }
}
