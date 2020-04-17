<?php

declare(strict_types=1);

namespace Spacetab\BodyValidator\Combinator;

use Amp\Promise;
use HarmonyIO\Validation\Rule\Combinator\All;
use HarmonyIO\Validation\Rule\Rule;
use function HarmonyIO\Validation\succeed;

final class Sometimes implements Rule
{
    /**
     * @var \HarmonyIO\Validation\Rule\Rule[]
     */
    private array $rules;

    public function __construct(Rule ...$rules)
    {
        $this->rules = $rules;
    }

    public function validate($value): Promise
    {
        if (is_null($value)) {
            return succeed();
        }

        return (new All(...$this->rules))
            ->validate($value);
    }
}
