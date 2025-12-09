<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 9: Summary Statistics Accuracy
 * Validates: Requirements 5.1, 5.2
 * 
 * Property-based test for attendance summary statistics accuracy.
 * For any student viewing attendance summary, the counts for each status type
 * should equal the actual number of records with that status in the past 30 days.
 */
class SummaryStatisticsAccuracyTest extends TestCase
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
     * Helper method to get stats from widget using reflection
     */
    protected function getWidgetStats(): array
    {
        $widget = new \App\Filament\Student\Widgets\AttendanceSummaryWidget();
        $reflection = new \ReflectionClass($widget);
        $method = $reflection->getMethod('getStats');
        $method->setAccessible(true);
        return $method->invoke($widget);
    }

    /**
     * Property Test: Summary counts match actual records in past 30 days
     * 
     * For any student with attendance records across various dates,
     * the summary statistics should accurately count records within the past 30 days.
     */
    public function test_summary_counts_match_actual_records_in_past_30_days(): void
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
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1', 'XII-1']),
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Generate random number of attendance records within past 30 days
            $recordsInRange = $faker->numberBetween(5, 20);
            $expectedPresent = 0;
            $expectedLate = 0;
            $expectedSick = 0;
            $expectedPermission = 0;
            $expectedAbsent = 0;
            $expectedPending = 0;

            for ($j = 0; $j < $recordsInRange; $j++) {
                $daysAgo = $faker->numberBetween(0, 29);
                $status = $faker->randomElement(['Hadir', 'Sakit', 'Izin', 'Alfa', 'Terlambat']);
                $isLate = $status === 'Terlambat';
                $verificationStatus = in_array($status, ['Sakit', 'Izin']) 
                    ? $faker->randomElement(['pending', 'approved', 'rejected', null])
                    : null;

                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($daysAgo)->toDateString(),
                    'status' => $status,
                    'kelas' => $murid->kelas,
                    'is_late' => $isLate,
                    'verification_status' => $verificationStatus,
                ]);

                // Count expected values
                if ($status === 'Hadir' && !$isLate) {
                    $expectedPresent++;
                } elseif ($isLate) {
                    $expectedLate++;
                } elseif ($status === 'Sakit') {
                    $expectedSick++;
                } elseif ($status === 'Izin') {
                    $expectedPermission++;
                } elseif ($status === 'Alfa') {
                    $expectedAbsent++;
                }

                if ($verificationStatus === 'pending') {
                    $expectedPending++;
                }
            }

            // Generate some records outside the 30-day window (should not be counted)
            $recordsOutsideRange = $faker->numberBetween(2, 5);
            for ($j = 0; $j < $recordsOutsideRange; $j++) {
                $daysAgo = $faker->numberBetween(31, 60);
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($daysAgo)->toDateString(),
                    'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin', 'Alfa']),
                    'kelas' => $murid->kelas,
                ]);
            }

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $stats = $this->getWidgetStats();

            // Assert counts match expected values
            $this->assertCount(6, $stats, 'Should have 6 stat cards');

            // Extract actual counts from stats
            $actualPresent = $stats[0]->getValue();
            $actualLate = $stats[1]->getValue();
            $actualSick = $stats[2]->getValue();
            $actualPermission = $stats[3]->getValue();
            $actualAbsent = $stats[4]->getValue();
            $actualPending = $stats[5]->getValue();

            // Assert all counts match
            $this->assertEquals($expectedPresent, $actualPresent, 'Present count should match');
            $this->assertEquals($expectedLate, $actualLate, 'Late count should match');
            $this->assertEquals($expectedSick, $actualSick, 'Sick count should match');
            $this->assertEquals($expectedPermission, $actualPermission, 'Permission count should match');
            $this->assertEquals($expectedAbsent, $actualAbsent, 'Absent count should match');
            $this->assertEquals($expectedPending, $actualPending, 'Pending count should match');
        }
    }

    /**
     * Property Test: Only records within past 30 days are counted
     * 
     * For any student with records both inside and outside the 30-day window,
     * only records within the window should be counted.
     */
    public function test_only_records_within_past_30_days_are_counted(): void
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

            // Create records within 30 days
            $recordsInRange = $faker->numberBetween(3, 10);
            for ($j = 0; $j < $recordsInRange; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => 'Hadir',
                    'kelas' => $murid->kelas,
                ]);
            }

            // Create records outside 30 days
            $recordsOutsideRange = $faker->numberBetween(5, 15);
            for ($j = 0; $j < $recordsOutsideRange; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($faker->numberBetween(31, 90))->toDateString(),
                    'status' => 'Hadir',
                    'kelas' => $murid->kelas,
                ]);
            }

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $stats = $this->getWidgetStats();

            // Get total count from all stats
            $totalCount = array_sum(array_map(fn($stat) => $stat->getValue(), $stats));

            // Assert total count equals records in range
            $this->assertEquals($recordsInRange, $totalCount, 'Total count should only include records within 30 days');
        }
    }

    /**
     * Property Test: Late count includes all late records regardless of status
     * 
     * For any student with late attendance records,
     * the late count should include all records where is_late is true.
     */
    public function test_late_count_includes_all_late_records(): void
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

            // Create random number of late records
            $expectedLateCount = $faker->numberBetween(1, 10);
            for ($j = 0; $j < $expectedLateCount; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => 'Terlambat',
                    'kelas' => $murid->kelas,
                    'is_late' => true,
                    'late_duration' => $faker->numberBetween(1, 60),
                ]);
            }

            // Create some on-time records
            $onTimeCount = $faker->numberBetween(5, 15);
            for ($j = 0; $j < $onTimeCount; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => 'Hadir',
                    'kelas' => $murid->kelas,
                    'is_late' => false,
                ]);
            }

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $stats = $this->getWidgetStats();

            // Get late count (second stat)
            $actualLateCount = $stats[1]->getValue();

            // Assert late count matches expected
            $this->assertEquals($expectedLateCount, $actualLateCount, 'Late count should match number of late records');
        }
    }

    /**
     * Property Test: Pending verification count is accurate
     * 
     * For any student with sick/permission records,
     * the pending count should match records with verification_status = 'pending'.
     */
    public function test_pending_verification_count_is_accurate(): void
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

            // Create random number of pending records
            $expectedPendingCount = $faker->numberBetween(1, 5);
            for ($j = 0; $j < $expectedPendingCount; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => $faker->randomElement(['Sakit', 'Izin']),
                    'kelas' => $murid->kelas,
                    'verification_status' => 'pending',
                ]);
            }

            // Create some approved/rejected records
            $verifiedCount = $faker->numberBetween(2, 8);
            for ($j = 0; $j < $verifiedCount; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => $faker->randomElement(['Sakit', 'Izin']),
                    'kelas' => $murid->kelas,
                    'verification_status' => $faker->randomElement(['approved', 'rejected']),
                ]);
            }

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $stats = $this->getWidgetStats();

            // Get pending count (sixth stat)
            $actualPendingCount = $stats[5]->getValue();

            // Assert pending count matches expected
            $this->assertEquals($expectedPendingCount, $actualPendingCount, 'Pending count should match number of pending records');
        }
    }

    /**
     * Property Test: Widget returns empty array when student has no Murid record
     * 
     * For any user without a linked Murid record,
     * the widget should return an empty stats array.
     */
    public function test_widget_returns_empty_array_when_no_murid_record(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random user without linked Murid
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            // Don't create a Murid record

            // Act as the user
            $this->actingAs($user);

            // Access the widget
            $stats = $this->getWidgetStats();

            // Assert empty array is returned
            $this->assertIsArray($stats);
            $this->assertEmpty($stats, 'Stats should be empty when user has no Murid record');
        }
    }

    /**
     * Property Test: Widget handles zero counts correctly
     * 
     * For any student with no attendance records,
     * all counts should be zero.
     */
    public function test_widget_handles_zero_counts_correctly(): void
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

            // Access the widget
            $stats = $this->getWidgetStats();

            // Assert all counts are zero
            $this->assertCount(6, $stats, 'Should have 6 stat cards');
            foreach ($stats as $stat) {
                $this->assertEquals(0, $stat->getValue(), 'All counts should be zero when no records exist');
            }
        }
    }

    /**
     * Property Test: Present count excludes late records
     * 
     * For any student with both present and late records,
     * the present count should only include records where is_late is false.
     */
    public function test_present_count_excludes_late_records(): void
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

            // Create on-time present records
            $expectedPresentCount = $faker->numberBetween(5, 15);
            for ($j = 0; $j < $expectedPresentCount; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => 'Hadir',
                    'kelas' => $murid->kelas,
                    'is_late' => false,
                ]);
            }

            // Create late records (should not be counted in present)
            $lateCount = $faker->numberBetween(1, 5);
            for ($j = 0; $j < $lateCount; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => 'Terlambat',
                    'kelas' => $murid->kelas,
                    'is_late' => true,
                ]);
            }

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $stats = $this->getWidgetStats();

            // Get present count (first stat)
            $actualPresentCount = $stats[0]->getValue();

            // Assert present count excludes late records
            $this->assertEquals($expectedPresentCount, $actualPresentCount, 'Present count should exclude late records');
        }
    }

    /**
     * Property Test: Stat colors change based on threshold values
     * 
     * For any student with counts exceeding thresholds,
     * the stat colors should reflect warning/danger states.
     */
    public function test_stat_colors_change_based_on_thresholds(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 20;

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

            // Create records that exceed thresholds
            $lateCount = $faker->numberBetween(4, 10); // > 3 should be warning
            for ($j = 0; $j < $lateCount; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($j)->toDateString(),
                    'status' => 'Terlambat',
                    'kelas' => $murid->kelas,
                    'is_late' => true,
                ]);
            }

            $sickCount = $faker->numberBetween(6, 12); // > 5 should be warning
            for ($j = 0; $j < $sickCount; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($j)->toDateString(),
                    'status' => 'Sakit',
                    'kelas' => $murid->kelas,
                ]);
            }

            $absentCount = $faker->numberBetween(4, 8); // > 3 should be danger
            for ($j = 0; $j < $absentCount; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($j)->toDateString(),
                    'status' => 'Alfa',
                    'kelas' => $murid->kelas,
                ]);
            }

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $stats = $this->getWidgetStats();

            // Assert late stat has warning color
            $this->assertEquals('warning', $stats[1]->getColor(), 'Late stat should have warning color when count > 3');

            // Assert sick stat has warning color
            $this->assertEquals('warning', $stats[2]->getColor(), 'Sick stat should have warning color when count > 5');

            // Assert absent stat has danger color
            $this->assertEquals('danger', $stats[4]->getColor(), 'Absent stat should have danger color when count > 3');
        }
    }
}
