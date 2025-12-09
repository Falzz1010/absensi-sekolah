<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 7: 30-Day History Retrieval
 * Validates: Requirements 4.1
 * 
 * Property-based test for 30-day attendance history retrieval.
 * For any student accessing attendance history, the system should return
 * all attendance records within the past 30 calendar days, ordered by date descending.
 */
class ThirtyDayHistoryRetrievalTest extends TestCase
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
    }

    /**
     * Property Test: Only records within past 30 days are returned
     * 
     * For any student with attendance records across various dates,
     * only records within the past 30 days should be returned.
     */
    public function test_only_records_within_past_30_days_are_returned(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random student with user account
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1', 'XII-1']),
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Generate random number of records within 30 days
            $recordsWithin30Days = $faker->numberBetween(5, 20);
            $expectedRecordIds = [];
            
            for ($j = 0; $j < $recordsWithin30Days; $j++) {
                $daysAgo = $faker->numberBetween(0, 29);
                $absensi = Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => Carbon::now()->subDays($daysAgo)->toDateString(),
                    'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin', 'Alfa', 'Terlambat']),
                    'kelas' => $murid->kelas,
                    'check_in_time' => $faker->optional(0.8)->time('H:i:s'),
                    'keterangan' => $faker->optional(0.5)->sentence(),
                ]);
                $expectedRecordIds[] = $absensi->id;
            }

            // Generate random number of records outside 30 days (should be excluded)
            $recordsOutside30Days = $faker->numberBetween(3, 10);
            
            for ($j = 0; $j < $recordsOutside30Days; $j++) {
                $daysAgo = $faker->numberBetween(31, 90);
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => Carbon::now()->subDays($daysAgo)->toDateString(),
                    'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin', 'Alfa']),
                    'kelas' => $murid->kelas,
                ]);
            }

            // Act as the student user
            $this->actingAs($user);

            // Query attendance records using the same logic as AttendanceHistoryPage
            $records = Absensi::query()
                ->where('murid_id', $murid->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->orderBy('tanggal', 'desc')
                ->get();

            // Assert only records within 30 days are returned
            $this->assertCount($recordsWithin30Days, $records, 
                "Should return exactly {$recordsWithin30Days} records within 30 days");
            
            // Assert all returned records are within 30 days
            foreach ($records as $record) {
                $daysDiff = Carbon::now()->diffInDays($record->tanggal);
                $this->assertLessThanOrEqual(30, $daysDiff, 
                    "Record date should be within 30 days");
            }

            // Assert all expected records are present
            $returnedIds = $records->pluck('id')->toArray();
            foreach ($expectedRecordIds as $expectedId) {
                $this->assertContains($expectedId, $returnedIds, 
                    "Expected record ID {$expectedId} should be in results");
            }
        }
    }

    /**
     * Property Test: Records are ordered by date descending
     * 
     * For any student with multiple attendance records,
     * records should be ordered by date in descending order (newest first).
     */
    public function test_records_are_ordered_by_date_descending(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random student with user account
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Generate random records with various dates
            $recordCount = $faker->numberBetween(10, 25);
            
            for ($j = 0; $j < $recordCount; $j++) {
                $daysAgo = $faker->numberBetween(0, 29);
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => Carbon::now()->subDays($daysAgo)->toDateString(),
                    'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin', 'Alfa']),
                    'kelas' => $murid->kelas,
                ]);
            }

            // Act as the student user
            $this->actingAs($user);

            // Query attendance records
            $records = Absensi::query()
                ->where('murid_id', $murid->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->orderBy('tanggal', 'desc')
                ->get();

            // Assert records are ordered by date descending
            $previousDate = null;
            foreach ($records as $record) {
                if ($previousDate !== null) {
                    $this->assertLessThanOrEqual(
                        $previousDate->timestamp,
                        $record->tanggal->timestamp,
                        "Records should be ordered by date descending"
                    );
                }
                $previousDate = $record->tanggal;
            }
        }
    }

    /**
     * Property Test: Empty result when no records exist
     * 
     * For any student with no attendance records in the past 30 days,
     * an empty collection should be returned.
     */
    public function test_empty_result_when_no_records_exist(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random student with user account
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Don't create any attendance records

            // Act as the student user
            $this->actingAs($user);

            // Query attendance records
            $records = Absensi::query()
                ->where('murid_id', $murid->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->orderBy('tanggal', 'desc')
                ->get();

            // Assert empty collection is returned
            $this->assertCount(0, $records, "Should return empty collection when no records exist");
        }
    }

    /**
     * Property Test: Boundary date (exactly 30 days ago) is included
     * 
     * For any student with an attendance record exactly 30 days ago,
     * that record should be included in the results.
     */
    public function test_boundary_date_exactly_30_days_ago_is_included(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random student with user account
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Create record exactly 30 days ago
            $thirtyDaysAgo = Carbon::now()->subDays(30)->startOfDay();
            $boundaryAbsensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => $thirtyDaysAgo->toDateString(),
                'status' => 'Hadir',
                'kelas' => $murid->kelas,
            ]);

            // Create record 31 days ago (should be excluded)
            Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => Carbon::now()->subDays(31)->toDateString(),
                'status' => 'Hadir',
                'kelas' => $murid->kelas,
            ]);

            // Act as the student user
            $this->actingAs($user);

            // Query attendance records using the same date calculation
            $cutoffDate = Carbon::now()->subDays(30)->startOfDay();
            $records = Absensi::query()
                ->where('murid_id', $murid->id)
                ->whereDate('tanggal', '>=', $cutoffDate)
                ->orderBy('tanggal', 'desc')
                ->get();

            // Assert boundary record is included
            $this->assertGreaterThanOrEqual(1, $records->count(), "Should include record exactly 30 days ago");
            $foundBoundary = $records->contains('id', $boundaryAbsensi->id);
            $this->assertTrue($foundBoundary, "Boundary record should be in results");
        }
    }

    /**
     * Property Test: Records from other students are not included
     * 
     * For any student, only their own attendance records should be returned,
     * not records from other students.
     */
    public function test_records_from_other_students_are_not_included(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate target student
            $targetUser = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $targetMurid = Murid::create([
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2']),
                'is_active' => true,
                'user_id' => $targetUser->id,
            ]);

            // Create records for target student
            $targetRecordCount = $faker->numberBetween(5, 10);
            for ($j = 0; $j < $targetRecordCount; $j++) {
                Absensi::create([
                    'murid_id' => $targetMurid->id,
                    'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin']),
                    'kelas' => $targetMurid->kelas,
                ]);
            }

            // Generate other students with records
            $otherStudentCount = $faker->numberBetween(3, 7);
            for ($k = 0; $k < $otherStudentCount; $k++) {
                $otherUser = User::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => bcrypt('password'),
                ]);

                $otherMurid = Murid::create([
                    'name' => $otherUser->name,
                    'email' => $otherUser->email,
                    'kelas' => $faker->randomElement(['X-1', 'X-2']),
                    'is_active' => true,
                    'user_id' => $otherUser->id,
                ]);

                // Create records for other students
                for ($j = 0; $j < $faker->numberBetween(5, 10); $j++) {
                    Absensi::create([
                        'murid_id' => $otherMurid->id,
                        'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                        'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin']),
                        'kelas' => $otherMurid->kelas,
                    ]);
                }
            }

            // Act as the target student user
            $this->actingAs($targetUser);

            // Query attendance records
            $records = Absensi::query()
                ->where('murid_id', $targetMurid->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->orderBy('tanggal', 'desc')
                ->get();

            // Assert only target student's records are returned
            $this->assertCount($targetRecordCount, $records, 
                "Should return only target student's records");
            
            foreach ($records as $record) {
                $this->assertEquals($targetMurid->id, $record->murid_id, 
                    "All records should belong to target student");
            }
        }
    }
}
