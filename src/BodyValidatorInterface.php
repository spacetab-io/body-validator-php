<?php

declare(strict_types=1);

namespace Spacetab\BodyValidator;

interface BodyValidatorInterface
{
    public function validate(): iterable;
}
