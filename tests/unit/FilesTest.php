<?php

declare(strict_types=1);

namespace axios\tools\tests\unit;

use axios\tools\Files;
use axios\tools\Path;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
class FilesTest extends TestCase
{
    public function testCopy()
    {
        Files::copy(__DIR__, Path::join(__DIR__, '../../runtime/tests_unit/'));
        $this->assertTrue(true); // No throw exception
    }
}
