<?php

use App\Http\Middleware\AdminMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Unit test AdminMiddleware::handle()
 * Decision: (!auth()->check() || !auth()->user()->isAdmin())
 */

test('middleware menolak (403) pengguna yang belum login', function () {
    // Arrange
    $middleware = new AdminMiddleware();
    $request    = Request::create('/admin/sanctions', 'GET');
    $next       = fn () => new Response('lolos');

    // Act & Assert
    expect(fn () => $middleware->handle($request, $next))
        ->toThrow(HttpException::class, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
});

test('middleware menolak (403) pengguna dengan role user', function () {
    // Arrange
    $this->actingAs(User::factory()->create(['role' => 'user']));
    $middleware = new AdminMiddleware();
    $request    = Request::create('/admin/sanctions', 'GET');
    $next       = fn () => new Response('lolos');

    // Act & Assert
    expect(fn () => $middleware->handle($request, $next))
        ->toThrow(HttpException::class);
});

test('middleware meneruskan request untuk pengguna dengan role admin', function () {
    // Arrange
    $this->actingAs(User::factory()->create(['role' => 'admin']));
    $middleware = new AdminMiddleware();
    $request    = Request::create('/admin/sanctions', 'GET');
    $next       = fn () => new Response('lolos', 200);

    // Act
    $response = $middleware->handle($request, $next);

    // Assert
    expect($response->getStatusCode())->toBe(200)
        ->and($response->getContent())->toBe('lolos');
});
