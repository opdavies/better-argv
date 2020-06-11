<?php

declare(strict_types=1);

namespace Opdavies\BetterArgv;

use Illuminate\Support\Collection;

final class Argv
{
    private $args = [];

    public static function createFromString(string $args): self
    {
        return new static($args);
    }

    public function getAll(): Collection
    {
        return $this->args;
    }

    public function get(string $argName)
    {
        return $this->args[$argName] ?? null;
    }

    final private function __construct(string $args)
    {
        $this->args = $this->parseAndFormatArgs($args);
    }

    private function isArgument(string $string): bool
    {
        return (bool)preg_match('/^-\w+/', $string) ?? false;
    }

    private function isOption(string $string): bool
    {
        return (bool)preg_match('/^--\w+/', $string) ?? false;
    }

    private function isArgumentOrOption(string $string): bool
    {
        return $this->isArgument($string) || $this->isOption($string);
    }

    private function parseAndFormatArgs(string $args): Collection
    {
        $argsArray = explode(' ', $args);

        return (new Collection($argsArray))
            ->mapWithKeys(
                function (string $currentArg, int $i) use (
                    $argsArray
                ): array {
                    $isNextArg = array_key_exists($i + 1, $argsArray);

                    if (!$isNextArg) {
                        return [];
                    }

                    $nextArg = $argsArray[$i + 1];

                    if (!$this->isArgumentOrOption($nextArg)) {
                      return [$currentArg => $nextArg];
                    }

                    if ($this->isArgumentOrOption($currentArg)) {
                        return [$currentArg => true];
                    }

                    return [$nextArg => true];
                }
            );
    }
}
