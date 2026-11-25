<?php

use App\Models\User;
use App\Models\UserSanction;

/*
|--------------------------------------------------------------------------
| Integration/Feature Test: alur HTTP fitur Sanksi (role Admin)
|--------------------------------------------------------------------------
| Menguji route + middleware auth/admin + controller + database (Fake SQLite).
*/

beforeEach(function () {
    $this->admin   = User::factory()->create(['name' => 'Admin Sistem', 'email' => 'admin@tiket.com', 'role' => 'admin']);
    $this->suporter = User::factory()->create(['name' => 'Budi Santoso', 'email' => 'budi@tiket.com', 'role' => 'user']);
});

// ---------- Otorisasi (positif & negatif) ----------

test('tamu diarahkan ke halaman login saat membuka halaman sanksi', function () {
    // Act & Assert
    $this->get('/admin/sanctions')->assertRedirect('/login');
});

test('user biasa mendapat 403 pada seluruh endpoint sanksi admin', function () {
    // Arrange
    $this->actingAs($this->suporter);

    // Act & Assert
    $this->get('/admin/sanctions')->assertForbidden();
    $this->get("/admin/sanctions/{$this->suporter->id}")->assertForbidden();
    $this->post("/admin/sanctions/{$this->suporter->id}/ban", ['sanction_type' => 'warning', 'reason' => 'Alasan valid'])->assertForbidden();
    $this->post("/admin/sanctions/{$this->suporter->id}/revoke", ['revocation_reason' => 'Alasan valid'])->assertForbidden();
    expect(UserSanction::count())->toBe(0);
});

test('admin dapat membuka daftar suporter dengan filter', function () {
    // Arrange
    $this->actingAs($this->admin);

    // Act
    $response = $this->get('/admin/sanctions?status=active&search=budi');

    // Assert
    $response->assertOk()
        ->assertViewIs('admin.sanctions.index')
        ->assertViewHas('stats');
    expect($response->viewData('users')->pluck('name')->all())->toBe(['Budi Santoso']);
});

test('admin dapat membuka detail suporter', function () {
    // Arrange
    $this->actingAs($this->admin);

    // Act & Assert
    $this->get("/admin/sanctions/{$this->suporter->id}")
        ->assertOk()
        ->assertViewIs('admin.sanctions.show');
});

// ---------- Alur lengkap ban -> revoke ----------

test('admin memberi sanksi lalu mencabutnya', function () {
    // Arrange
    $this->actingAs($this->admin);

    // Act 1: ban
    $this->post("/admin/sanctions/{$this->suporter->id}/ban", [
        'sanction_type' => 'blacklist', 'reason' => 'Melempar botol ke lapangan',
    ])->assertRedirect()->assertSessionHas('success');

    // Assert 1
    expect($this->suporter->fresh()->is_banned)->toBeTrue();
    $this->assertDatabaseHas('user_sanctions', [
        'user_id' => $this->suporter->id, 'sanction_type' => 'blacklist',
        'status' => 'active', 'banned_by' => $this->admin->id,
    ]);

    // Act 2: revoke
    $this->post("/admin/sanctions/{$this->suporter->id}/revoke", [
        'revocation_reason' => 'Banding diterima',
    ])->assertRedirect()->assertSessionHas('success');

    // Assert 2
    expect($this->suporter->fresh()->is_banned)->toBeFalse();
    $this->assertDatabaseHas('user_sanctions', [
        'user_id' => $this->suporter->id, 'status' => 'revoked', 'revoked_by' => $this->admin->id,
    ]);
});

test('admin tidak dapat memberi sanksi kepada sesama admin', function () {
    // Arrange
    $this->actingAs($this->admin);
    $adminLain = User::factory()->create(['role' => 'admin']);

    // Act
    $response = $this->post("/admin/sanctions/{$adminLain->id}/ban", [
        'sanction_type' => 'blacklist', 'reason' => 'Alasan valid',
    ]);

    // Assert
    $response->assertRedirect()->assertSessionHas('error');
    expect(UserSanction::count())->toBe(0)
        ->and($adminLain->fresh()->is_banned)->toBeFalse();
});

test('validasi gagal pada ban dan revoke mengembalikan error form', function () {
    // Arrange
    $this->actingAs($this->admin);

    // Act & Assert
    $this->post("/admin/sanctions/{$this->suporter->id}/ban", ['sanction_type' => 'kick', 'reason' => ''])
        ->assertSessionHasErrors(['sanction_type', 'reason']);
    $this->post("/admin/sanctions/{$this->suporter->id}/revoke", ['revocation_reason' => 'abc'])
        ->assertSessionHasErrors(['revocation_reason']);
});
