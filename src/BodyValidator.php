<?php

declare(strict_types=1);

namespace Spacetab\BodyValidator;

use Adbar\Dot;
use Amp\Promise;
use HarmonyIO\Validation\Result\Error;
use HarmonyIO\Validation\Result\Result;
use function Amp\call;

final class BodyValidator
{
    private Dot $body;

    /**
     * @var \Spacetab\BodyValidator\BodyValidatorInterface
     */
    private BodyValidatorInterface $validator;

    /**
     * BodyValidator constructor.
     *
     * @param array $body
     * @param \Spacetab\BodyValidator\BodyValidatorInterface $validator
     */
    public function __construct(array $body, BodyValidatorInterface $validator)
    {
        $this->body      = new Dot($body);
        $this->validator = $validator;
    }

    /**
     * @return Promise<ResultSet>
     */
    public function verify(): Promise
    {
        return call(function () {
            $promises = [];
            foreach ($this->validator->validate() as $path => $rule) {
                /** @var \HarmonyIO\Validation\Rule\Rule $rule */
                $promises[$path] = $rule->validate(
                    $this->body->get($path)
                );
            }

            $results = yield $promises;

            return new ResultSet(
                $this->isValid($results),
                $this->collectMistakes($results)
            );
        });
    }

    /**
     * @param array $results
     * @return array<string, array<Error>>
     */
    private function collectMistakes(array $results): array
    {
        $errors = [];
        foreach ($results as $key => $result) {
            /** @var Error $error */
            foreach ($result->getErrors() as $error) {
                $errors[$key][] = $error;
            }
        }

        return $errors;
    }

    /**
     * @param array<Result> $results
     * @return bool
     */
    private function isValid(array $results)
    {
        foreach ($results as $result) {
            /** @var $result Result */
            if (!$result->isValid()) {
                return false;
            }
        }

        return true;
    }
}
