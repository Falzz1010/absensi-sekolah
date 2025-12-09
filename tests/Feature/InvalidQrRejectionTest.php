<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\QrCode;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 2: Invalid QR Rejection
 * Validates: Requirements 1.3
 * 
 * Property-based test for invalid QR code rejection.
 * For any invalid or inactive QR code, when scanned by any student,
 * the system should reject the scan and no attendance record should be created.
 */
class InvalidQrRejectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable broadcasting for tests
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\QrCodeScanned::class,
            \App\Events\AbsensiCreated::class,
            \App\Events\AbsensiUpdated::class,
        ]);
        
        // Seed settings for time windows
        Setting::updateOrCreate(
            ['key' => 'check_in_start'],
            [
                'value' => '06:00:00',
                'type' => 'time',
                'group' => 'absensi',
                'label' => 'Check-in Start Time',
                'description' => 'Start time for check-in window',
            ]
        );
        
        Setting::updateOrCreate(
            ['key' => 'check_in_end'],
            [
                'value' => '08:00:00',
                'type' => 'time',
                'group' => 'absensi',
                'label' => 'Check-in End Time',
                'description' => 'End time for check-in window',
            ]
        );
    }

    /**
     * Property Test: Invalid QR codes are rejected
     * 
     * For any non-existent QR code, the scan should be rejected
     * and no attendance record should be created.
     */
    public function test_nonexistent_qr_codes_are_rejected(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        // Mock time to be within valid window
        $this->travelTo(now()->setTime(7, 0, 0));

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random non-existent QR code
            $invalidCode = $faker->unique()->uuid;

            // Count attendance records before scan
            $beforeCount = Absensi::count();

            // Attempt to scan invalid QR code
            $response = $this->postJson('/api/qr-scan', [
                'code' => $invalidCode,
            ]);

            // Assert response indicates failure
            $response->assertStatus(404);
            $response->assertJson([
                'success' => false,
            ]);

            // Assert no attendance record was created
            $afterCount = Absensi::count();
            $this->assertEquals($beforeCount, $afterCount, 'No attendance record should be created for invalid QR code');
        }

        $this->travelBack();
    }

    /**
     * Property Test: Inactive QR codes are rejected
     * 
     * For any QR code marked as inactive, the scan should be rejected
     * and no attendance record should be created.
     */
    public function test_inactive_qr_codes_are_rejected(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        // Mock time to be within valid window
        $this->travelTo(now()->setTime(7, 0, 0));

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random inactive QR code
            $qrCode = QrCode::create([
                'nama' => $faker->words(3, true),
                'tipe' => $faker->randomElement(['global', 'kelas']),
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'code' => $faker->unique()->uuid,
                'is_active' => false, // Inactive
            ]);

            // Create a student linked to this QR code
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $qrCode->kelas,
                'is_active' => true,
                'qr_code_id' => $qrCode->id,
            ]);

            // Count attendance records before scan
            $beforeCount = Absensi::count();

            // Attempt to scan inactive QR code
            $response = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert response indicates failure
            $response->assertStatus(404);
            $response->assertJson([
                'success' => false,
                'message' => 'QR Code tidak valid atau tidak aktif',
            ]);

            // Assert no attendance record was created
            $afterCount = Absensi::count();
            $this->assertEquals($beforeCount, $afterCount, 'No attendance record should be created for inactive QR code');
        }

        $this->travelBack();
    }

    /**
     * Property Test: Expired QR codes are rejected
     * 
     * For any QR code outside its validity period, the scan should be rejected
     * and no attendance record should be created.
     */
    public function test_expired_qr_codes_are_rejected(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        // Mock time to be within valid window
        $this->travelTo(now()->setTime(7, 0, 0));

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random expired QR code (expired yesterday)
            $qrCode = QrCode::create([
                'nama' => $faker->words(3, true),
                'tipe' => 'kelas',
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'code' => $faker->unique()->uuid,
                'is_active' => true,
                'berlaku_dari' => now()->subDays(30),
                'berlaku_sampai' => now()->subDays(1), // Expired yesterday
            ]);

            // Create a student linked to this QR code
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $qrCode->kelas,
                'is_active' => true,
                'qr_code_id' => $qrCode->id,
            ]);

            // Count attendance records before scan
            $beforeCount = Absensi::count();

            // Attempt to scan expired QR code
            $response = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert response indicates failure
            $response->assertStatus(400);
            $response->assertJson([
                'success' => false,
                'message' => 'QR Code sudah tidak aktif',
            ]);

            // Assert no attendance record was created
            $afterCount = Absensi::count();
            $this->assertEquals($beforeCount, $afterCount, 'No attendance record should be created for expired QR code');
        }

        $this->travelBack();
    }

    /**
     * Property Test: QR codes not yet valid are rejected
     * 
     * For any QR code before its validity period, the scan should be rejected
     * and no attendance record should be created.
     */
    public function test_not_yet_valid_qr_codes_are_rejected(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        // Mock time to be within valid window
        $this->travelTo(now()->setTime(7, 0, 0));

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random QR code that's not yet valid (starts tomorrow)
            $qrCode = QrCode::create([
                'nama' => $faker->words(3, true),
                'tipe' => 'kelas',
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'code' => $faker->unique()->uuid,
                'is_active' => true,
                'berlaku_dari' => now()->addDays(1), // Starts tomorrow
                'berlaku_sampai' => now()->addDays(30),
            ]);

            // Create a student linked to this QR code
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $qrCode->kelas,
                'is_active' => true,
                'qr_code_id' => $qrCode->id,
            ]);

            // Count attendance records before scan
            $beforeCount = Absensi::count();

            // Attempt to scan not-yet-valid QR code
            $response = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert response indicates failure
            $response->assertStatus(400);
            $response->assertJson([
                'success' => false,
                'message' => 'QR Code sudah tidak aktif',
            ]);

            // Assert no attendance record was created
            $afterCount = Absensi::count();
            $this->assertEquals($beforeCount, $afterCount, 'No attendance record should be created for not-yet-valid QR code');
        }

        $this->travelBack();
    }

    /**
     * Property Test: QR codes without linked students are rejected
     * 
     * For any QR code that has no student linked to it, the scan should be rejected
     * and no attendance record should be created.
     */
    public function test_qr_codes_without_linked_students_are_rejected(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        // Mock time to be within valid window
        $this->travelTo(now()->setTime(7, 0, 0));

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random valid QR code but with no linked student
            $qrCode = QrCode::create([
                'nama' => $faker->words(3, true),
                'tipe' => 'kelas',
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'code' => $faker->unique()->uuid,
                'is_active' => true,
            ]);

            // Don't create a student linked to this QR code

            // Count attendance records before scan
            $beforeCount = Absensi::count();

            // Attempt to scan QR code without linked student
            $response = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert response indicates failure
            $response->assertStatus(404);
            $response->assertJson([
                'success' => false,
                'message' => 'Murid tidak ditemukan',
            ]);

            // Assert no attendance record was created
            $afterCount = Absensi::count();
            $this->assertEquals($beforeCount, $afterCount, 'No attendance record should be created when no student is linked');
        }

        $this->travelBack();
    }

    /**
     * Property Test: QR codes with inactive students are rejected
     * 
     * For any QR code linked to an inactive student, the scan should be rejected
     * and no attendance record should be created.
     */
    public function test_qr_codes_with_inactive_students_are_rejected(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        // Mock time to be within valid window
        $this->travelTo(now()->setTime(7, 0, 0));

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random valid QR code
            $qrCode = QrCode::create([
                'nama' => $faker->words(3, true),
                'tipe' => 'kelas',
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'code' => $faker->unique()->uuid,
                'is_active' => true,
            ]);

            // Create an inactive student linked to this QR code
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $qrCode->kelas,
                'is_active' => false, // Inactive student
                'qr_code_id' => $qrCode->id,
            ]);

            // Count attendance records before scan
            $beforeCount = Absensi::count();

            // Attempt to scan QR code with inactive student
            $response = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert response indicates failure
            $response->assertStatus(404);
            $response->assertJson([
                'success' => false,
                'message' => 'Murid tidak ditemukan',
            ]);

            // Assert no attendance record was created
            $afterCount = Absensi::count();
            $this->assertEquals($beforeCount, $afterCount, 'No attendance record should be created for inactive student');
        }

        $this->travelBack();
    }
}
