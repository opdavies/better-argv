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
                    if (!$this->isNextArg($argsArray, $i)) {
                        return [];
                    }

                    return $this->addNextValue(
                        $currentArg,
                        $this->getNextArg($argsArray, $i)
                    );
                }
            );
    }

    private function addNextValue(
        string $currentArg,
        string $nextArg
    ): array {
        if (!$this->isArgumentOrOption($nextArg)) {
            return [$currentArg => $nextArg];
        }

        if ($this->isArgumentOrOption($currentArg)) {
            return [$currentArg => true];
        }

        return [$nextArg => true];
    }

    private function getNextArg(array $argsArray, int $currentPosition): string
    {
        return $argsArray[$currentPosition + 1];
    }

    private function isNextArg(array $argsArray, int $currentPosition): bool
    {
        return array_key_exists($currentPosition + 1, $argsArray);
    }
}
