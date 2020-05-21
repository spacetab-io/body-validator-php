<?php

declare(strict_types=1);

namespace Spacetab\BodyValidator;


final class ResultSet
{
    private bool  $valid;

    /**
     * @var array<string, array>
     */
    private array $errors;

    /**
     * ResultSet constructor.
     *
     * @param bool $valid
     * @param array<string, array> $errors
     */
    public function __construct(bool $valid, array $errors = [])
    {
        $this->valid  = $valid;
        $this->errors = $errors;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @return array<string, array>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
