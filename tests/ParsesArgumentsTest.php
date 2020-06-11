<?php

namespace Opdavies\BetterArgv\Tests;

use Opdavies\BetterArgv\Argv;
use PHPUnit\Framework\TestCase;

final class ParsesArgumentsTest extends TestCase
{
    /**
     * @dataProvider providesArguments()
     */
    public function testThatItParsesAStringOfArguments(
        string $input,
        array $expected
    ): void {
        $args = Argv::createFromString($input);

        $this->assertSame($expected, $args->getAll()->toArray());
    }

    public function providesArguments(): array
    {
        return [
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
        ];
    }

    public function testThatItReturnsTheValueOfAnArgument(): void
    {
        $argv = Argv::createFromString('-t main --force');

        $this->assertSame('main', $argv->get('-t'));
        $this->assertTrue($argv->get('--force'));
    }

    public function testThatItAHandlesAnArgumentThatDoesNotExist(): void
    {
        $argv = Argv::createFromString('--this-exists');

        $this->assertNull($argv->get('--this-does-not-exist'));
    }
}
