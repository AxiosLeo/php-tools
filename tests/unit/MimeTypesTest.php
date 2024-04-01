<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\MimeTypes;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class MimeTypesTest extends TestCase
{
    public function testGetExtension()
    {
        $mime = new MimeTypes();
        $this->assertEquals('html', $mime->getExtension('text/html'));
        $this->assertEquals('html', $mime->getExtension('application/html', true));
        $this->assertEquals('js', $mime->getExtension('application/javascript'));
        $this->assertEquals('json', $mime->getExtension('application/json'));
        $this->assertEquals('jpg', $mime->getExtension('image/jpeg'));
    }

    public function testGetMime()
    {
        $mime = new MimeTypes();
        $this->assertEquals('text/html', $mime->getMime('html'));
        $this->assertEquals('application/javascript', $mime->getMime('js'));
        $this->assertEquals('application/json', $mime->getMime('json'));
        $this->assertEquals('image/jpeg', $mime->getMime('jpeg'));
    }
}
