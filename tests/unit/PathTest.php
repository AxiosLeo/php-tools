<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\Path;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class PathTest extends TestCase
{
    public function testJoin()
    {
        $is_win = \PHP_SHLIB_SUFFIX === 'dll';

        $this->assertEquals(
            realpath(__DIR__ . '/../../') . \DIRECTORY_SEPARATOR . 'test.json',
            Path::join(__DIR__, '../../test.json')
        );

        $this->assertEquals(
            $is_win ? '\a\b\c' : '/a/b/c',
            Path::join('/a/', 'b/c')
        );

        $this->assertEquals(
            $is_win ? '\a\b' : '/a/b',
            Path::join('/a/', './', 'b/c', '../')
        );

        $this->assertEquals(
            $is_win ? 'a\b\c\d.php' : 'a/b/c/d.php',
            Path::join('a/', './', 'b/c', 'd.php')
        );
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
