<?php

declare(strict_types=1);

namespace Spacetab\Tests\BodyValidator;

use Amp\PHPUnit\AsyncTestCase;
use Spacetab\BodyValidator\NullBody;

class NullBodyTest extends AsyncTestCase
{
    public function testNullableBody()
    {
        $body = new NullBody();

        $this->assertSame([], $body->validate());
    }
}
