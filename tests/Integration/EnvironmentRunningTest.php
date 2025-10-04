<?php

declare(strict_types=1);

use Illuminate\Database\Console\Migrations\StatusCommand as MigrationStatusCommand;
use Illuminate\Mail\Message;
use Laravel\Horizon\Console\StatusCommand as HorizonStatusCommand;

test('that homepage is available', function (): void {
    $response = Http::get('frank:8000');
    expect($response->successful())->toBeTrue();
});

test('that redis has started', function (): void {
    // Assert that we get a response from redis. Redis doesn't have a http interface, so an error is the best confirmation we have.
    $this->expectExceptionMessage('cURL error 52: Empty reply from server');
    Http::get('redis:6379')->body();
});

test('that database has started', function (): void {
    DB::getPdo();

    // Assert that getting db connection doesn't throw an exception
    $this->addToAssertionCount(1);
});

test('that migrations ran successfully', function (): void {
    $this->artisan(MigrationStatusCommand::class)->assertSuccessful();
});

test('that horizon has started', function (): void {
    $this->artisan(HorizonStatusCommand::class)->assertSuccessful();
});

test('that horizon interface is available', function (): void {
    $this->get(config('horizon.path'))->assertSuccessful();
});

test('that reverb server is up', function (): void {
    expect(Http::get('http://reverb:6001')->notFound())->toBeTrue();
});

test('that mailpit interface is up', function (): void {
    expect(Http::get('http://mailpit:8025')->successful())->toBeTrue();
});

test('that mail can be sent', function (): void {
    Mail::raw('test', static fn (Message $message) => $message->to('test@example.com'));

    // Assert that sending mail doesn't throw an exception
    $this->addToAssertionCount(1);
});
