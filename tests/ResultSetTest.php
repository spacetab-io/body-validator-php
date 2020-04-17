<?php

declare(strict_types=1);

namespace Spacetab\Tests\BodyValidator;

use Amp\PHPUnit\AsyncTestCase;
use Spacetab\BodyValidator\ResultSet;

class ResultSetTest extends AsyncTestCase
{
    public function testObjectFilling()
    {
        $errors = ['foo.bar' => [1, 2, 3]];
        $result = new ResultSet(true, $errors);

        $this->assertTrue($result->isValid());
        $this->assertSame($errors, $result->getErrors());
    }
}
