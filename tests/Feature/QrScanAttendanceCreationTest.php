<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\QrCode;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 1: QR Scan Creates Attendance Record
 * Validates: Requirements 1.2
 * 
 * Property-based test for QR scan attendance creation.
 * For any valid QR code and authenticated student, when the QR code is scanned
 * within valid time windows, an attendance record with status "present" should
 * be created with the current timestamp.
 */
class QrScanAttendanceCreationTest extends TestCase
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
        
        Setting::updateOrCreate(
            ['key' => 'late_threshold'],
            [
                'value' => '07:30:00',
                'type' => 'time',
                'group' => 'absensi',
                'label' => 'Late Threshold',
                'description' => 'Time threshold for marking late',
            ]
        );
    }

    /**
     * Property Test: QR scan creates attendance record with correct data
     * 
     * For any valid QR code and active student, scanning should create
     * an attendance record with the correct status and timestamp.
     */
    public function test_qr_scan_creates_attendance_record_with_valid_data(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random valid QR code
            $qrCode = QrCode::create([
                'nama' => $faker->words(3, true),
                'tipe' => $faker->randomElement(['global', 'kelas']),
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1', 'XII-1']),
                'code' => $faker->unique()->uuid,
                'is_active' => true,
                'berlaku_dari' => now()->subDays(10),
                'berlaku_sampai' => now()->addDays(10),
            ]);

            // Generate random active student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $qrCode->kelas,
                'is_active' => true,
                'qr_code_id' => $qrCode->id,
            ]);

            // Mock current time to be within valid window (07:00)
            $this->travelTo(now()->setTime(7, 0, 0));

            // Scan QR code
            $response = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert response is successful
            $response->assertStatus(200);
            $response->assertJson([
                'success' => true,
            ]);

            // Get the created attendance record
            $absensi = Absensi::where('murid_id', $murid->id)
                ->whereDate('tanggal', now()->toDateString())
                ->first();

            // Assert attendance record was created
            $this->assertNotNull($absensi, 'Attendance record should be created');
            
            // Assert attendance has correct properties
            $this->assertEquals($murid->id, $absensi->murid_id);
            $this->assertEquals(now()->toDateString(), $absensi->tanggal->toDateString());
            $this->assertEquals('Hadir', $absensi->status);
            $this->assertEquals($murid->kelas, $absensi->kelas);
            $this->assertNotNull($absensi->check_in_time);
            $this->assertFalse($absensi->is_late);
            $this->assertNull($absensi->late_duration);

            // Travel back to present
            $this->travelBack();
        }
    }

    /**
     * Property Test: Late check-in is properly marked
     * 
     * For any student checking in after the late threshold,
     * the attendance should be marked as late with correct duration.
     */
    public function test_late_checkin_is_properly_marked(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random valid QR code
            $qrCode = QrCode::create([
                'nama' => $faker->words(3, true),
                'tipe' => 'kelas',
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'code' => $faker->unique()->uuid,
                'is_active' => true,
                'berlaku_dari' => now()->subDays(10),
                'berlaku_sampai' => now()->addDays(10),
            ]);

            // Generate random active student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $qrCode->kelas,
                'is_active' => true,
                'qr_code_id' => $qrCode->id,
            ]);

            // Mock current time to be after late threshold (07:31 to 07:59)
            $lateMinutes = $faker->numberBetween(1, 29);
            $this->travelTo(now()->setTime(7, 30, 0)->addMinutes($lateMinutes));

            // Scan QR code
            $response = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert response is successful
            $response->assertStatus(200);

            // Get the created attendance record
            $absensi = Absensi::where('murid_id', $murid->id)
                ->whereDate('tanggal', now()->toDateString())
                ->first();

            // Assert attendance record was created with late status
            $this->assertNotNull($absensi, 'Attendance record should be created');
            $this->assertEquals('Terlambat', $absensi->status);
            $this->assertTrue($absensi->is_late);

            // Assert late duration is calculated correctly
            $this->assertNotNull($absensi->late_duration);
            $this->assertGreaterThan(0, $absensi->late_duration);
            $this->assertEquals($lateMinutes, $absensi->late_duration);

            // Travel back to present
            $this->travelBack();
        }
    }

    /**
     * Property Test: On-time check-in is not marked as late
     * 
     * For any student checking in before the late threshold,
     * the attendance should not be marked as late.
     */
    public function test_ontime_checkin_is_not_marked_as_late(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random valid QR code
            $qrCode = QrCode::create([
                'nama' => $faker->words(3, true),
                'tipe' => 'kelas',
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'code' => $faker->unique()->uuid,
                'is_active' => true,
            ]);

            // Generate random active student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $qrCode->kelas,
                'is_active' => true,
                'qr_code_id' => $qrCode->id,
            ]);

            // Mock current time to be before late threshold (random between 06:00 and 07:29)
            $hour = $faker->numberBetween(6, 7);
            $minute = $hour == 7 ? $faker->numberBetween(0, 29) : $faker->numberBetween(0, 59);
            $this->travelTo(now()->setTime($hour, $minute, 0));

            // Scan QR code
            $response = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert response is successful
            $response->assertStatus(200);

            // Get the created attendance record
            $absensi = Absensi::where('murid_id', $murid->id)
                ->whereDate('tanggal', now()->toDateString())
                ->first();

            // Assert attendance record was created without late status
            $this->assertNotNull($absensi, 'Attendance record should be created');
            $this->assertEquals('Hadir', $absensi->status);
            $this->assertFalse($absensi->is_late);

            // Assert late duration is null
            $this->assertNull($absensi->late_duration);

            // Travel back to present
            $this->travelBack();
        }
    }

    /**
     * Property Test: Check-in time is recorded correctly
     * 
     * For any valid scan, the check_in_time should match the current time.
     */
    public function test_checkin_time_is_recorded_correctly(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random valid QR code
            $qrCode = QrCode::create([
                'nama' => $faker->words(3, true),
                'tipe' => 'kelas',
                'kelas' => $faker->randomElement(['X-1', 'X-2']),
                'code' => $faker->unique()->uuid,
                'is_active' => true,
            ]);

            // Generate random active student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $qrCode->kelas,
                'is_active' => true,
                'qr_code_id' => $qrCode->id,
            ]);

            // Mock random time within valid window
            $hour = $faker->numberBetween(6, 7);
            $minute = $faker->numberBetween(0, 59);
            $second = $faker->numberBetween(0, 59);
            $expectedTime = sprintf('%02d:%02d:%02d', $hour, $minute, $second);
            
            $this->travelTo(now()->setTime($hour, $minute, $second));

            // Scan QR code
            $response = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert response is successful
            $response->assertStatus(200);

            // Get the created attendance record
            $absensi = Absensi::where('murid_id', $murid->id)
                ->whereDate('tanggal', now()->toDateString())
                ->first();

            // Assert check-in time matches expected time
            $this->assertNotNull($absensi, 'Attendance record should be created');
            $this->assertNotNull($absensi->check_in_time);
            $this->assertEquals($expectedTime, $absensi->check_in_time);

            // Travel back to present
            $this->travelBack();
        }
    }
}
