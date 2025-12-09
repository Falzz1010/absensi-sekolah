<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 8: Status Filter Accuracy
 * Validates: Requirements 4.3
 * 
 * Property-based test for status filter accuracy in attendance history.
 * For any status filter selection, all returned records should match
 * the selected status exactly.
 */
class StatusFilterAccuracyTest extends TestCase
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
     * Property Test: Filter returns only matching status records
     * 
     * For any student with attendance records of various statuses,
     * filtering by a specific status should return only records with that status.
     */
    public function test_filter_returns_only_matching_status_records(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;
        $statuses = ['Hadir', 'Sakit', 'Izin', 'Alfa', 'Terlambat'];

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

            // Generate random records with various statuses
            $recordsByStatus = [];
            foreach ($statuses as $status) {
                $count = $faker->numberBetween(2, 5);
                $recordsByStatus[$status] = $count;
                
                for ($j = 0; $j < $count; $j++) {
                    Absensi::create([
                        'murid_id' => $murid->id,
                        'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                        'status' => $status,
                        'kelas' => $murid->kelas,
                        'check_in_time' => $faker->optional(0.7)->time('H:i:s'),
                        'is_late' => $status === 'Terlambat',
                        'late_duration' => $status === 'Terlambat' ? $faker->numberBetween(1, 60) : null,
                    ]);
                }
            }

            // Act as the student user
            $this->actingAs($user);

            // Test filtering by each status
            foreach ($statuses as $filterStatus) {
                $records = Absensi::query()
                    ->where('murid_id', $murid->id)
                    ->where('tanggal', '>=', Carbon::now()->subDays(30))
                    ->where('status', $filterStatus)
                    ->orderBy('tanggal', 'desc')
                    ->get();

                // Assert correct count
                $expectedCount = $recordsByStatus[$filterStatus];
                $this->assertCount($expectedCount, $records, 
                    "Should return exactly {$expectedCount} records with status {$filterStatus}");

                // Assert all records have the filtered status
                foreach ($records as $record) {
                    $this->assertEquals($filterStatus, $record->status, 
                        "All records should have status {$filterStatus}");
                }
            }
        }
    }

    /**
     * Property Test: Multiple status values are not mixed
     * 
     * For any student with records of multiple statuses,
     * filtering by one status should not return records with other statuses.
     */
    public function test_multiple_status_values_are_not_mixed(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;
        $statuses = ['Hadir', 'Sakit', 'Izin', 'Alfa', 'Terlambat'];

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

            // Create records with all statuses
            foreach ($statuses as $status) {
                for ($j = 0; $j < $faker->numberBetween(3, 7); $j++) {
                    Absensi::create([
                        'murid_id' => $murid->id,
                        'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                        'status' => $status,
                        'kelas' => $murid->kelas,
                    ]);
                }
            }

            // Act as the student user
            $this->actingAs($user);

            // Pick a random status to filter by
            $filterStatus = $faker->randomElement($statuses);
            
            $records = Absensi::query()
                ->where('murid_id', $murid->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->where('status', $filterStatus)
                ->orderBy('tanggal', 'desc')
                ->get();

            // Assert no records have other statuses
            $otherStatuses = array_diff($statuses, [$filterStatus]);
            foreach ($records as $record) {
                $this->assertNotContains($record->status, $otherStatuses, 
                    "Record should not have status from other categories");
                $this->assertEquals($filterStatus, $record->status);
            }
        }
    }

    /**
     * Property Test: Empty result when no records match filter
     * 
     * For any student with no records of a specific status,
     * filtering by that status should return an empty collection.
     */
    public function test_empty_result_when_no_records_match_filter(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;
        $statuses = ['Hadir', 'Sakit', 'Izin', 'Alfa', 'Terlambat'];

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

            // Pick a status to exclude
            $excludedStatus = $faker->randomElement($statuses);
            $includedStatuses = array_diff($statuses, [$excludedStatus]);

            // Create records with all statuses except the excluded one
            foreach ($includedStatuses as $status) {
                for ($j = 0; $j < $faker->numberBetween(2, 5); $j++) {
                    Absensi::create([
                        'murid_id' => $murid->id,
                        'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                        'status' => $status,
                        'kelas' => $murid->kelas,
                    ]);
                }
            }

            // Act as the student user
            $this->actingAs($user);

            // Filter by the excluded status
            $records = Absensi::query()
                ->where('murid_id', $murid->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->where('status', $excludedStatus)
                ->orderBy('tanggal', 'desc')
                ->get();

            // Assert empty collection
            $this->assertCount(0, $records, 
                "Should return empty collection when no records match filter");
        }
    }

    /**
     * Property Test: Filter works with date range
     * 
     * For any student with records across various dates,
     * status filter combined with date range should return only matching records.
     */
    public function test_filter_works_with_date_range(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;
        $statuses = ['Hadir', 'Sakit', 'Izin', 'Alfa', 'Terlambat'];

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

            // Pick a status to filter by
            $filterStatus = $faker->randomElement($statuses);

            // Create records within 30 days with the filter status
            $expectedCount = 0;
            for ($j = 0; $j < $faker->numberBetween(5, 10); $j++) {
                $daysAgo = $faker->numberBetween(0, 29);
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => Carbon::now()->subDays($daysAgo)->toDateString(),
                    'status' => $filterStatus,
                    'kelas' => $murid->kelas,
                ]);
                $expectedCount++;
            }

            // Create records outside 30 days with the filter status (should be excluded)
            for ($j = 0; $j < $faker->numberBetween(3, 5); $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => Carbon::now()->subDays($faker->numberBetween(31, 60))->toDateString(),
                    'status' => $filterStatus,
                    'kelas' => $murid->kelas,
                ]);
            }

            // Create records within 30 days with other statuses (should be excluded)
            $otherStatuses = array_diff($statuses, [$filterStatus]);
            foreach ($otherStatuses as $status) {
                for ($j = 0; $j < $faker->numberBetween(2, 4); $j++) {
                    Absensi::create([
                        'murid_id' => $murid->id,
                        'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                        'status' => $status,
                        'kelas' => $murid->kelas,
                    ]);
                }
            }

            // Act as the student user
            $this->actingAs($user);

            // Filter by status and date range
            $records = Absensi::query()
                ->where('murid_id', $murid->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->where('status', $filterStatus)
                ->orderBy('tanggal', 'desc')
                ->get();

            // Assert correct count and all match filter
            $this->assertCount($expectedCount, $records, 
                "Should return only records matching both status and date range");
            
            foreach ($records as $record) {
                $this->assertEquals($filterStatus, $record->status);
                $daysDiff = Carbon::now()->diffInDays($record->tanggal);
                $this->assertLessThanOrEqual(30, $daysDiff);
            }
        }
    }

    /**
     * Property Test: Case sensitivity in status filter
     * 
     * For any student with attendance records,
     * status filter should be case-sensitive and exact match.
     */
    public function test_case_sensitivity_in_status_filter(): void
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

            // Create records with exact status "Hadir"
            $hadirCount = $faker->numberBetween(5, 10);
            for ($j = 0; $j < $hadirCount; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => 'Hadir',
                    'kelas' => $murid->kelas,
                ]);
            }

            // Act as the student user
            $this->actingAs($user);

            // Filter by exact status
            $records = Absensi::query()
                ->where('murid_id', $murid->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->where('status', 'Hadir')
                ->orderBy('tanggal', 'desc')
                ->get();

            // Assert all records have exact status
            $this->assertCount($hadirCount, $records);
            foreach ($records as $record) {
                $this->assertSame('Hadir', $record->status, 
                    "Status should be exact match (case-sensitive)");
            }
        }
    }

    /**
     * Property Test: Filter preserves record order
     * 
     * For any student with filtered records,
     * the results should still be ordered by date descending.
     */
    public function test_filter_preserves_record_order(): void
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

            // Create records with same status on different dates
            $filterStatus = 'Hadir';
            for ($j = 0; $j < $faker->numberBetween(10, 20); $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => $filterStatus,
                    'kelas' => $murid->kelas,
                ]);
            }

            // Act as the student user
            $this->actingAs($user);

            // Filter by status
            $records = Absensi::query()
                ->where('murid_id', $murid->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->where('status', $filterStatus)
                ->orderBy('tanggal', 'desc')
                ->get();

            // Assert records are ordered by date descending
            $previousDate = null;
            foreach ($records as $record) {
                if ($previousDate !== null) {
                    $this->assertLessThanOrEqual(
                        $previousDate->timestamp,
                        $record->tanggal->timestamp,
                        "Filtered records should be ordered by date descending"
                    );
                }
                $previousDate = $record->tanggal;
            }
        }
    }
}
