<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Pest\Mutate\Contracts\MutationTestRunner;
use Pest\Mutate\Repositories\ConfigurationRepository;
use Pest\Mutate\Support\Configuration\CliConfiguration;
use Pest\Support\Container;
use Tests\TestCase;

Container::getInstance()->get(ConfigurationRepository::class)
    ->globalConfiguration()
    ->path('app/Models')
    ->min(80);

pest()->beforeEach(function (): void {
    $mutationTestRunner = Container::getInstance()->get(MutationTestRunner::class);

    if (PHP_OS_FAMILY === 'Windows' && $mutationTestRunner->isEnabled()) {
        $arguments = (new CliConfiguration)->fromArguments($_SERVER['argv']);

        $mutationTestRunner->setOriginalArguments([PHP_BINARY, ...$arguments]);
    }
})->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
