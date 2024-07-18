<?php

namespace Tests\Integration;

use Auth;
use DB;
use Http;
use Illuminate\Database\Console\Migrations\StatusCommand as MigrationStatusCommand;
use Illuminate\Mail\Message;
use Laravel\Horizon\Console\StatusCommand as HorizonStatusCommand;
use Mail;
use Tests\TestCase;

class EnvironmentRunningTest extends TestCase
{
    public function test_that_homepage_is_available(): void
    {
        $this->assertTrue(Http::get('frank:8000')->successful());
    }

    public function test_that_redis_has_started(): void
    {
        // Assert that we get a response from redis. Redis doesn't have a http interface, so an error is the best confirmation we have.
        $this->expectExceptionMessage('cURL error 52: Empty reply from server');
        Http::get('redis:6379')->body();
    }

    public function test_that_database_has_started(): void
    {
        DB::getPdo();

        // Assert that getting db connection doesn't throw an exception
        $this->addToAssertionCount(1);
    }

    public function test_that_migrations_ran_successfully(): void
    {
        $this->artisan(MigrationStatusCommand::class)->assertSuccessful();
    }

    public function test_that_horizon_has_started(): void
    {
        $this->artisan(HorizonStatusCommand::class)->assertSuccessful();
    }

    public function test_that_horizon_interface_is_available(): void
    {
        $this->get(config('horizon.path'))->assertSuccessful();
    }

    public function test_that_reverb_server_is_up(): void
    {
        $this->assertTrue(Http::get('http://reverb:6001')->notFound());
    }

    public function test_that_mailpit_interface_is_up(): void
    {
        $this->assertTrue(Http::get('http://mailpit:8025')->successful());
    }

    public function test_that_mail_can_be_sent(): void
    {
        Mail::raw('test', static fn (Message $message) => $message->to('test@example.com'));

        // Assert that sending mail doesn't throw an exception
        $this->addToAssertionCount(1);
    }
}
