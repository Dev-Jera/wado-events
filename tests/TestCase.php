<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Safety rail: never allow the test suite to hit a persistent local/prod DB.
        $connection = DB::connection()->getDriverName();
        $database = (string) config('database.connections.' . config('database.default') . '.database');
        if ($connection !== 'sqlite' || $database !== ':memory:') {
            throw new RuntimeException(sprintf(
                'Unsafe test DB configuration detected (driver=%s, database=%s). Refusing to run tests.',
                $connection,
                $database
            ));
        }

        $this->withoutVite();
    }
}
