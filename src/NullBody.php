<?php

declare(strict_types=1);

namespace Spacetab\BodyValidator;

final class NullBody implements BodyValidatorInterface
{
    public function validate(): iterable
    {
        return [];
    }
}
