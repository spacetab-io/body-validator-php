<?php

declare(strict_types=1);

namespace Spacetab\BodyValidator;

use Spacetab\Obelix;
use Amp\Promise;
use HarmonyIO\Validation\Result\Error;
use HarmonyIO\Validation\Result\Result;
use function Amp\call;

final class BodyValidator
{
    private Obelix\Dot $body;
    private BodyValidatorInterface $validator;

    /**
     * BodyValidator constructor.
     *
     * @param array<mixed> $body
     * @param \Spacetab\BodyValidator\BodyValidatorInterface $validator
     */
    public function __construct(array $body, BodyValidatorInterface $validator)
    {
        $this->body      = new Obelix\Dot($body);
        $this->validator = $validator;
    }

    /**
     * @return Promise<ResultSet>
     */
    public function verify(): Promise
    {
        // @phpstan-ignore-next-line
        return call(function () {
            $promises = [];
            /** @var \HarmonyIO\Validation\Rule\Rule $rule */
            foreach ($this->validator->validate() as $path => $rule) {
                foreach ($this->body->get($path)->getMap() as $key => $value) {
                    $promises[$key] = $rule->validate($value);
                }
            }

            $results = yield $promises;

            return new ResultSet(
                $this->isValid($results),
                $this->collectMistakes($results)
            );
        });
    }

    /**
     * @param array<string, Result> $results
     * @return array<string, array<Error>>
     */
    private function collectMistakes(array $results): array
    {
        $errors = [];
        $unique = [];
        foreach ($results as $key => $result) {
            /** @var Error $error */
            foreach ($result->getErrors() as $error) {
                if (isset($errors[$key])) {
                    if (!isset($unique[$error->getMessage()])) {
                        $errors[$key] = array_merge($errors[$key], [$error]);
                    }
                } else {
                    $errors[$key][] = $error;
                    $unique[$error->getMessage()] = true;
                }
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
