<?php

use App\Http\Controllers\Admin\AdminSanctionController;
use App\Models\User;
use App\Models\UserSanction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Unit Test: AdminSanctionController (role Admin)
|--------------------------------------------------------------------------
| Test Double yang dipakai:
|  - Fake  : SQLite in-memory (phpunit.xml) menggantikan PostgreSQL Supabase
|  - Dummy : objek Request kosong untuk parameter yang tidak dipakai
|  - Stub  : User palsu dengan isAdmin()/update() yang jawabannya ditentukan
|  - Mock  : facade DB, memverifikasi beginTransaction/commit/rollBack
|  - Spy   : facade Auth, merekam pemanggilan Auth::id() lalu diverifikasi
*/

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create([
        'name' => 'Admin Sistem', 'email' => 'admin@tiket.com', 'role' => 'admin',
    ]);
    $this->actingAs($this->admin);
    $this->controller = new AdminSanctionController();
});

function makeSuporter(string $name, string $email, bool $banned = false): User
{
    return User::factory()->create([
        'name' => $name, 'email' => $email, 'role' => 'user', 'is_banned' => $banned,
    ]);
}

function makeSanction(User $user, int $bannedBy, string $type = 'blacklist', string $reason = 'Pelanggaran', string $status = 'active'): UserSanction
{
    return UserSanction::create([
        'user_id'       => $user->id,
        'sanction_type' => $type,
        'reason'        => $reason,
        'status'        => $status,
        'banned_by'     => $bannedBy,
    ]);
}

function suporterNames($view): array
{
    $names = $view->getData()['users']->pluck('name')->all();
    sort($names);

    return $names;
}

// ============================================================
// index(): TC-01..TC-04 pada analisis Pre-Lab (Statement & Branch)
// ============================================================

describe('index()', function () {
    beforeEach(function () {
        makeSuporter('Budi Santoso', 'budi@tiket.com', true);
        makeSuporter('Siti Aminah', 'siti@tiket.com');
        makeSuporter('Budi Hartono', 'hartono@tiket.com');
    });

    test('TC-01: filter status=banned + search=budi hanya menampilkan suporter ter-banned bernama budi', function () {
        // Arrange
        $request = Request::create('/admin/sanctions', 'GET', ['status' => 'banned', 'search' => 'budi']);

        // Act
        $view = $this->controller->index($request);

        // Assert
        expect($view->name())->toBe('admin.sanctions.index')
            ->and(suporterNames($view))->toBe(['Budi Santoso']);
    });

    test('TC-02: filter status=active hanya menampilkan suporter yang tidak di-banned', function () {
        // Arrange
        $request = Request::create('/admin/sanctions', 'GET', ['status' => 'active']);

        // Act
        $view = $this->controller->index($request);

        // Assert
        expect(suporterNames($view))->toBe(['Budi Hartono', 'Siti Aminah']);
    });

    test('TC-03: nilai status tidak dikenal diabaikan sehingga semua suporter tampil', function () {
        // Arrange
        $request = Request::create('/admin/sanctions', 'GET', ['status' => 'suspended']);

        // Act
        $view = $this->controller->index($request);

        // Assert
        expect(suporterNames($view))->toBe(['Budi Hartono', 'Budi Santoso', 'Siti Aminah']);
    });

    test('TC-04: tanpa parameter menampilkan semua suporter dan akun admin tidak ikut', function () {
        // Arrange
        $request = Request::create('/admin/sanctions', 'GET');

        // Act
        $view = $this->controller->index($request);

        // Assert
        expect(suporterNames($view))->toBe(['Budi Hartono', 'Budi Santoso', 'Siti Aminah'])
            ->and(suporterNames($view))->not->toContain('Admin Sistem');
    });

    test('pencarian juga mencocokkan email (orWhere)', function () {
        // Arrange
        $request = Request::create('/admin/sanctions', 'GET', ['search' => 'siti@']);

        // Act
        $view = $this->controller->index($request);

        // Assert
        expect(suporterNames($view))->toBe(['Siti Aminah']);
    });

    test('statistik dihitung benar', function () {
        // Arrange
        $request = Request::create('/admin/sanctions', 'GET');

        // Act
        $stats = $this->controller->index($request)->getData()['stats'];

        // Assert
        expect($stats)->toBe([
            'total_users' => 3, 'banned_users' => 1, 'active_users' => 2, 'total_sanctions' => 0,
        ]);
    });
});

// ============================================================
// ban()
// ============================================================

describe('ban()', function () {
    test('[Dummy + Stub] menolak pemberian sanksi kepada akun Administrator', function () {
        // Arrange
        $dummyRequest = new Request();                                   // Dummy: tidak pernah dipakai
        $stubTarget   = Mockery::mock(User::class)->makePartial();      // Stub: isAdmin() dipaksa true
        $stubTarget->allows('isAdmin')->andReturn(true);

        // Act
        $this->controller->ban($dummyRequest, $stubTarget);

        // Assert
        expect(session('error'))->toContain('Tidak dapat memberikan sanksi kepada akun Administrator')
            ->and(UserSanction::count())->toBe(0);
    });

    test('[Mock] sanksi valid dijalankan dalam satu transaksi yang di-commit', function () {
        // Arrange
        DB::shouldReceive('beginTransaction')->once();                   // Mock: harapan interaksi
        DB::shouldReceive('commit')->once();
        DB::shouldReceive('rollBack')->never();
        $target  = makeSuporter('Budi Santoso', 'budi@tiket.com');
        $request = Request::create('/', 'POST', [
            'sanction_type' => 'blacklist', 'reason' => 'Membawa flare ke stadion',
        ]);

        // Act
        $this->controller->ban($request, $target);

        // Assert
        $target->refresh();
        expect($target->is_banned)->toBeTrue()
            ->and($target->ban_reason)->toBe('Membawa flare ke stadion')
            ->and($target->banned_at)->not->toBeNull()
            ->and(session('success'))->toContain('Sanksi berhasil diberikan kepada suporter Budi Santoso')
            ->and(UserSanction::where('user_id', $target->id)->where('status', 'active')->count())->toBe(1)
            ->and(UserSanction::first()->banned_by)->toBe($this->admin->id);
    });

    test('[Stub] kegagalan saat update user membatalkan transaksi (rollback)', function () {
        // Arrange
        $real = makeSuporter('Budi Santoso', 'budi@tiket.com');
        $stubUser = Mockery::mock(User::class)->makePartial();           // Stub: update() selalu gagal
        $stubUser->setRawAttributes($real->getAttributes());
        $stubUser->allows('update')->andThrow(new Exception('Simulasi DB error'));
        $request = Request::create('/', 'POST', [
            'sanction_type' => 'suspend', 'reason' => 'Berkelahi di tribun',
        ]);

        // Act
        $this->controller->ban($request, $stubUser);

        // Assert: riwayat sanksi yang sempat dibuat ikut dibatalkan
        expect(session('error'))->toBe('Gagal memberikan sanksi: Simulasi DB error')
            ->and(UserSanction::count())->toBe(0)
            ->and($real->fresh()->is_banned)->toBeFalse();
    });

    test('input tidak valid ditolak dan tidak ada data tersimpan', function (array $input) {
        // Arrange
        $target  = makeSuporter('Budi Santoso', 'budi@tiket.com');
        $request = Request::create('/', 'POST', $input);

        // Act & Assert
        expect(fn () => $this->controller->ban($request, $target))->toThrow(ValidationException::class);
        expect(UserSanction::count())->toBe(0)
            ->and($target->fresh()->is_banned)->toBeFalse();
    })->with([
        'jenis sanksi tidak dikenal' => [['sanction_type' => 'kick', 'reason' => 'Alasan cukup panjang']],
        'alasan kosong'              => [['sanction_type' => 'warning', 'reason' => '']],
        'alasan kurang dari 5 huruf' => [['sanction_type' => 'warning', 'reason' => 'abcd']],
    ]);
});

// ============================================================
// revoke()
// ============================================================

describe('revoke()', function () {
    test('mencabut seluruh sanksi aktif dan memulihkan akun suporter', function () {
        // Arrange (Fake: SQLite in-memory)
        $target = makeSuporter('Budi Santoso', 'budi@tiket.com', true);
        makeSanction($target, $this->admin->id, 'warning', 'Pelanggaran 1');
        makeSanction($target, $this->admin->id, 'blacklist', 'Pelanggaran 2');
        $request = Request::create('/', 'POST', ['revocation_reason' => 'Sudah menyampaikan permohonan maaf']);

        // Act
        $this->controller->revoke($request, $target);

        // Assert
        $target->refresh();
        expect($target->is_banned)->toBeFalse()
            ->and($target->ban_reason)->toBeNull()
            ->and($target->banned_at)->toBeNull()
            ->and(UserSanction::where('status', 'active')->count())->toBe(0)
            ->and(UserSanction::where('status', 'revoked')->count())->toBe(2)
            ->and(session('success'))->toContain('berhasil dicabut');
    });

    test('[Spy] revoke mencatat admin pencabut lewat Auth::id() tepat satu kali', function () {
        // Arrange
        $target = makeSuporter('Budi Santoso', 'budi@tiket.com', true);
        makeSanction($target, $this->admin->id);
        $spyAuth = Auth::spy();                                          // Spy: merekam semua panggilan
        $spyAuth->allows('id')->andReturn($this->admin->id);
        $request = Request::create('/', 'POST', ['revocation_reason' => 'Banding diterima']);

        // Act
        $this->controller->revoke($request, $target);

        // Assert
        Auth::shouldHaveReceived('id')->once();
        $sanksi = UserSanction::first();
        expect($sanksi->revoked_by)->toBe($this->admin->id)
            ->and($sanksi->revocation_reason)->toBe('Banding diterima');
    });

    test('alasan pencabutan tidak valid ditolak', function (string $reason) {
        // Arrange
        $target  = makeSuporter('Budi Santoso', 'budi@tiket.com', true);
        $request = Request::create('/', 'POST', ['revocation_reason' => $reason]);

        // Act & Assert
        expect(fn () => $this->controller->revoke($request, $target))->toThrow(ValidationException::class);
        expect($target->fresh()->is_banned)->toBeTrue();
    })->with(['', 'abc']);

    test('[Stub] kegagalan saat memulihkan user membatalkan pencabutan sanksi (rollback)', function () {
        // Arrange
        $real = makeSuporter('Budi Santoso', 'budi@tiket.com', true);
        makeSanction($real, $this->admin->id);
        $stubUser = Mockery::mock(User::class)->makePartial();
        $stubUser->setRawAttributes($real->getAttributes());
        $stubUser->allows('update')->andThrow(new Exception('Simulasi DB error'));
        $request = Request::create('/', 'POST', ['revocation_reason' => 'Banding diterima']);

        // Act
        $this->controller->revoke($request, $stubUser);

        // Assert
        expect(session('error'))->toBe('Gagal mencabut sanksi: Simulasi DB error')
            ->and(UserSanction::where('status', 'active')->count())->toBe(1);
    });
});

// ============================================================
// show()
// ============================================================

test('show() mengirim suporter beserta relasi riwayat sanksi ke view', function () {
    // Arrange
    $target = makeSuporter('Budi Santoso', 'budi@tiket.com', true);
    makeSanction($target, $this->admin->id, 'warning');

    // Act
    $view = $this->controller->show($target);

    // Assert
    expect($view->name())->toBe('admin.sanctions.show')
        ->and($view->getData()['user']->relationLoaded('sanctions'))->toBeTrue()
        ->and($view->getData()['user']->relationLoaded('orders'))->toBeTrue()
        ->and($view->getData()['user']->sanctions)->toHaveCount(1);
});
