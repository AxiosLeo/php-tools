<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\Path;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class PathTest extends TestCase
{
    public function testJoin()
    {
        $this->assertEquals('/path/to', Path::join('/path/to/file.json', '..'));
        $this->assertEquals('/path/to/file.json', Path::join('/path/to/file.json', '.'));
        $this->assertEquals('/path/to/some/foo/bar', Path::join('/path/to/', 'some', './', 'foo', 'invalid', '..', 'bar'));
    }

    public function testSearch()
    {
        $dir   = \dirname(__DIR__);
        $files = Path::search($dir);
        $count = \count($files);
        $this->assertCount($count, Path::search($dir, '/\.(php|ini)$/'));
        $this->assertCount($count, Path::search($dir, ['php']));
        $this->assertCount(0, Path::search($dir, '/\.(xml|json)$/'));
        $this->assertCount(0, Path::search($dir, ['xml', 'json']));
    }
}
