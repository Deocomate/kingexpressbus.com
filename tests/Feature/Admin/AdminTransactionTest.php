<?php

use App\Http\Middleware\AdminDatabaseTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

it('rolls back all writes when an exception is thrown mid-transaction on a POST request', function () {
    $middleware = new AdminDatabaseTransaction();
    $request = Request::create('/test', 'POST');

    try {
        $middleware->handle($request, function () {
            DB::table('users')->insert([
                'name' => 'Write 1',
                'email' => 'write1@example.com',
                'password' => bcrypt('pw'),
                'role' => 'customer',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            throw new \RuntimeException('Simulated failure after first write');
        });
    } catch (\Throwable) {
        // Expected: middleware rolls back and re-throws
    }

    // Write 1 must not survive — transaction was rolled back
    expect(DB::table('users')->where('email', 'write1@example.com')->exists())->toBeFalse();
});

it('does not wrap GET requests in a transaction', function () {
    $middleware = new AdminDatabaseTransaction();
    $request = Request::create('/test', 'GET');
    $called = false;

    $response = $middleware->handle($request, function () use (&$called) {
        $called = true;

        return response('ok');
    });

    expect($called)->toBeTrue();
    expect($response->getContent())->toBe('ok');
});

it('commits writes when no exception is thrown on POST', function () {
    $middleware = new AdminDatabaseTransaction();
    $request = Request::create('/test', 'POST');

    $middleware->handle($request, function () {
        DB::table('users')->insert([
            'name' => 'Committed',
            'email' => 'committed@example.com',
            'password' => bcrypt('pw'),
            'role' => 'customer',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response('ok', 200);
    });

    expect(DB::table('users')->where('email', 'committed@example.com')->exists())->toBeTrue();
});
