<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\QrCode;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 3: Duplicate Check-in Prevention
 * Validates: Requirements 1.5
 * 
 * Property-based test for duplicate check-in prevention.
 * For any student who has already checked in for the current session,
 * attempting to scan again should be rejected and the existing record
 * should remain unchanged.
 */
class DuplicateCheckinPreventionTest extends TestCase
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
     * Property Test: Duplicate check-in is prevented
     * 
     * For any student with existing attendance for today,
     * a second scan attempt should be rejected.
     */
    public function test_duplicate_checkin_is_prevented(): void
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

            // Generate random active student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $qrCode->kelas,
                'is_active' => true,
                'qr_code_id' => $qrCode->id,
            ]);

            // Create existing attendance record for today
            $existingAbsensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => 'Hadir',
                'kelas' => $murid->kelas,
                'check_in_time' => '06:30:00',
                'is_late' => false,
            ]);

            $originalCheckInTime = $existingAbsensi->check_in_time;
            $originalStatus = $existingAbsensi->status;

            // Count attendance records before second scan
            $beforeCount = Absensi::count();

            // Attempt to scan again
            $response = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert response indicates failure
            $response->assertStatus(400);
            $response->assertJson([
                'success' => false,
            ]);
            $this->assertStringContainsString('Anda sudah melakukan check-in hari ini', $response->json('message'));

            // Assert no new attendance record was created
            $afterCount = Absensi::count();
            $this->assertEquals($beforeCount, $afterCount, 'No new attendance record should be created');

            // Assert existing record remains unchanged
            $existingAbsensi->refresh();
            $this->assertEquals($originalCheckInTime, $existingAbsensi->check_in_time, 'Check-in time should not change');
            $this->assertEquals($originalStatus, $existingAbsensi->status, 'Status should not change');
        }

        $this->travelBack();
    }

    /**
     * Property Test: Duplicate check-in preserves original record
     * 
     * For any student with existing attendance, attempting to check in again
     * should not modify the original attendance record in any way.
     */
    public function test_duplicate_checkin_preserves_original_record(): void
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

            // Generate random active student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $qrCode->kelas,
                'is_active' => true,
                'qr_code_id' => $qrCode->id,
            ]);

            // Create existing attendance record with random properties
            $isLate = $faker->boolean;
            $lateDuration = $isLate ? $faker->numberBetween(1, 60) : null;
            $checkInTime = $faker->time('H:i:s');
            
            $existingAbsensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => $isLate ? 'Terlambat' : 'Hadir',
                'kelas' => $murid->kelas,
                'check_in_time' => $checkInTime,
                'is_late' => $isLate,
                'late_duration' => $lateDuration,
            ]);

            // Store original values
            $originalId = $existingAbsensi->id;
            $originalCheckInTime = $existingAbsensi->check_in_time;
            $originalStatus = $existingAbsensi->status;
            $originalIsLate = $existingAbsensi->is_late;
            $originalLateDuration = $existingAbsensi->late_duration;
            $originalUpdatedAt = $existingAbsensi->updated_at;

            // Attempt to scan again
            $response = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert response indicates failure
            $response->assertStatus(400);

            // Assert existing record remains completely unchanged
            $existingAbsensi->refresh();
            $this->assertEquals($originalId, $existingAbsensi->id);
            $this->assertEquals($originalCheckInTime, $existingAbsensi->check_in_time);
            $this->assertEquals($originalStatus, $existingAbsensi->status);
            $this->assertEquals($originalIsLate, $existingAbsensi->is_late);
            $this->assertEquals($originalLateDuration, $existingAbsensi->late_duration);
            $this->assertEquals($originalUpdatedAt->timestamp, $existingAbsensi->updated_at->timestamp);
        }

        $this->travelBack();
    }

    /**
     * Property Test: Duplicate check-in works across different times
     * 
     * For any student who checked in earlier, attempting to check in again
     * at a different time should still be rejected.
     */
    public function test_duplicate_checkin_rejected_at_different_times(): void
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

            // First check-in at random early time (06:00 - 06:59)
            $firstCheckInHour = 6;
            $firstCheckInMinute = $faker->numberBetween(0, 59);
            $this->travelTo(now()->setTime($firstCheckInHour, $firstCheckInMinute, 0));

            $response1 = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            $response1->assertStatus(200);

            // Second check-in attempt at different time (07:00 - 07:59)
            $secondCheckInHour = 7;
            $secondCheckInMinute = $faker->numberBetween(0, 59);
            $this->travelTo(now()->setTime($secondCheckInHour, $secondCheckInMinute, 0));

            $response2 = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert second attempt is rejected
            $response2->assertStatus(400);
            $response2->assertJson([
                'success' => false,
            ]);

            // Assert only one attendance record exists
            $attendanceCount = Absensi::where('murid_id', $murid->id)
                ->whereDate('tanggal', now()->toDateString())
                ->count();
            $this->assertEquals(1, $attendanceCount, 'Only one attendance record should exist');

            $this->travelBack();
        }
    }

    /**
     * Property Test: Students can check in on different days
     * 
     * For any student who checked in yesterday, they should be able
     * to check in again today (not considered duplicate).
     */
    public function test_students_can_checkin_on_different_days(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

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

            // Generate random active student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $qrCode->kelas,
                'is_active' => true,
                'qr_code_id' => $qrCode->id,
            ]);

            // Create attendance record for yesterday
            $yesterdayAbsensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->subDay()->toDateString(),
                'status' => 'Hadir',
                'kelas' => $murid->kelas,
                'check_in_time' => '07:00:00',
                'is_late' => false,
            ]);

            // Attempt to check in today
            $response = $this->postJson('/api/qr-scan', [
                'code' => $qrCode->code,
            ]);

            // Assert today's check-in is successful
            $response->assertStatus(200);
            $response->assertJson([
                'success' => true,
            ]);

            // Assert two attendance records exist (yesterday and today)
            $attendanceCount = Absensi::where('murid_id', $murid->id)->count();
            $this->assertEquals(2, $attendanceCount, 'Two attendance records should exist (yesterday and today)');

            // Assert today's record exists
            $todayAbsensi = Absensi::where('murid_id', $murid->id)
                ->whereDate('tanggal', now()->toDateString())
                ->first();
            $this->assertNotNull($todayAbsensi, 'Today\'s attendance record should exist');
        }

        $this->travelBack();
    }
}
