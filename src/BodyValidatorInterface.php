<?php

declare(strict_types=1);

namespace Spacetab\BodyValidator;

interface BodyValidatorInterface
{
    /**
     * @return iterable<string, \HarmonyIO\Validation\Rule\Rule>
     */
    public function validate(): iterable;
}
