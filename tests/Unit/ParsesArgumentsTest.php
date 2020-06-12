<?php

declare(strict_types=1);

namespace Opdavies\BetterArgv\Tests;

use Opdavies\BetterArgv\Argv;

it('parses a string of arguments', function (
    string $input,
    array $expected
): void {
    $args = Argv::createFromString($input);

    $this->assertSame($expected, $args->getAll()->toArray());
})->with([
    'empty string' => [
        'input' => '',
        'expected' => [],
    ],
    'argument followed by an option' => [
        'input' => '-t main --force',
        'expected' => [
            '-t' => 'main',
            '--force' => true,
        ],
    ],
    'option followed by an argument' => [
        'input' => '--force -t main',
        'expected' => [
            '--force' => true,
            '-t' => 'main',
        ],
    ],
]);

it('returns the value of an argument', function (): void {
    $argv = Argv::createFromString('-t main --force');

    $this->assertSame('main', $argv->get('-t'));
    $this->assertTrue($argv->get('--force'));
});

it('returns a null value for arguments that do not exist', function (): void {
    $argv = Argv::createFromString('--this-exists');

    $this->assertNull($argv->get('--this-does-not-exist'));
});
