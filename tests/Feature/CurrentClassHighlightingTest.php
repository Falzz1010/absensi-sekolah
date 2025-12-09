<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\Murid;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 14: Current Class Highlighting
 * Validates: Requirements 10.3
 * 
 * Property-based test for current class highlighting.
 * For any schedule display where the current time falls within a class period's time range,
 * that class should be marked as "in progress".
 */
class CurrentClassHighlightingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable broadcasting for tests
        \Illuminate\Support\Facades\Event::fake();
    }

    /**
     * Property Test: Current class is highlighted when time falls within class period
     * 
     * For any schedule where the current time is between jam_mulai and jam_selesai,
     * the getCurrentClass method should return that specific class.
     */
    public function test_current_class_is_highlighted_when_time_falls_within_class_period(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        $dayNames = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        for ($i = 0; $i < $iterations; $i++) {
            // Clear database for each iteration
            \Illuminate\Support\Facades\DB::table('jadwals')->delete();
            \Illuminate\Support\Facades\DB::table('gurus')->delete();
            \Illuminate\Support\Facades\DB::table('murids')->delete();
            \Illuminate\Support\Facades\DB::table('users')->delete();

            // Generate random student with user account
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $kelas = $faker->randomElement(['X-1', 'X-2', 'XI-1', 'XI-2', 'XII-1']);
            
            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $kelas,
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Get current day in Indonesian
            $currentDay = $dayNames[now()->format('l')];

            // Create multiple schedules for today
            $scheduleCount = $faker->numberBetween(3, 6);
            $schedules = [];

            for ($j = 0; $j < $scheduleCount; $j++) {
                $guru = Guru::create([
                    'name' => $faker->name,
                    'mata_pelajaran' => $faker->randomElement(['Matematika', 'Bahasa Indonesia', 'Fisika', 'Kimia', 'Biologi']),
                    'kelas' => $kelas,
                ]);

                $startHour = 7 + $j;
                $endHour = $startHour + 1;

                $schedule = Jadwal::create([
                    'guru_id' => $guru->id,
                    'kelas' => $kelas,
                    'mata_pelajaran' => $guru->mata_pelajaran,
                    'hari' => $currentDay,
                    'jam_mulai' => sprintf('%02d:00:00', $startHour),
                    'jam_selesai' => sprintf('%02d:00:00', $endHour),
                ]);

                $schedules[] = $schedule;
            }

            // Pick a random schedule to be the "current" one
            $currentScheduleIndex = $faker->numberBetween(0, $scheduleCount - 1);
            $currentSchedule = $schedules[$currentScheduleIndex];

            // Mock the current time to be within the selected schedule
            $startTime = Carbon::parse($currentSchedule->jam_mulai);
            $endTime = Carbon::parse($currentSchedule->jam_selesai);
            $mockCurrentTime = $startTime->copy()->addMinutes($faker->numberBetween(1, 59));

            // Use Carbon::setTestNow to mock the current time
            Carbon::setTestNow($mockCurrentTime);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayScheduleWidget();
            $currentClass = $widget->getCurrentClass();

            // Assert the correct class is identified as current
            $this->assertNotNull($currentClass, 'Current class should be identified');
            $this->assertEquals($currentSchedule->id, $currentClass->id, 'Should return the schedule that matches current time');
            $this->assertEquals($currentSchedule->mata_pelajaran, $currentClass->mata_pelajaran);
            $this->assertEquals($kelas, $currentClass->kelas);

            // Reset Carbon test time
            Carbon::setTestNow();
        }
    }

    /**
     * Property Test: No class is highlighted when time is outside all class periods
     * 
     * For any time that falls outside all scheduled class periods,
     * the getCurrentClass method should return null.
     */
    public function test_no_class_is_highlighted_when_time_is_outside_all_class_periods(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        $dayNames = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        for ($i = 0; $i < $iterations; $i++) {
            // Clear database for each iteration
            \Illuminate\Support\Facades\DB::table('jadwals')->delete();
            \Illuminate\Support\Facades\DB::table('gurus')->delete();
            \Illuminate\Support\Facades\DB::table('murids')->delete();
            \Illuminate\Support\Facades\DB::table('users')->delete();

            // Generate random student with user account
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $kelas = $faker->randomElement(['X-1', 'X-2', 'XI-1']);
            
            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $kelas,
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Get current day in Indonesian
            $currentDay = $dayNames[now()->format('l')];

            // Create schedules for today (8:00-12:00)
            $guru = Guru::create([
                'name' => $faker->name,
                'mata_pelajaran' => $faker->randomElement(['Matematika', 'Fisika']),
                'kelas' => $kelas,
            ]);

            Jadwal::create([
                'guru_id' => $guru->id,
                'kelas' => $kelas,
                'mata_pelajaran' => $guru->mata_pelajaran,
                'hari' => $currentDay,
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '12:00:00',
            ]);

            // Mock current time to be outside class hours (either before or after)
            $outsideTime = $faker->randomElement([
                Carbon::today()->setTime(6, 30, 0), // Before classes
                Carbon::today()->setTime(14, 30, 0), // After classes
            ]);

            Carbon::setTestNow($outsideTime);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayScheduleWidget();
            $currentClass = $widget->getCurrentClass();

            // Assert no class is identified as current
            $this->assertNull($currentClass, 'No class should be highlighted when time is outside all class periods');

            // Reset Carbon test time
            Carbon::setTestNow();
        }
    }

    /**
     * Property Test: Correct class is highlighted when multiple classes exist
     * 
     * For any schedule with multiple consecutive classes,
     * only the class matching the current time should be highlighted.
     */
    public function test_correct_class_is_highlighted_when_multiple_classes_exist(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        $dayNames = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        for ($i = 0; $i < $iterations; $i++) {
            // Clear database for each iteration
            \Illuminate\Support\Facades\DB::table('jadwals')->delete();
            \Illuminate\Support\Facades\DB::table('gurus')->delete();
            \Illuminate\Support\Facades\DB::table('murids')->delete();
            \Illuminate\Support\Facades\DB::table('users')->delete();

            // Generate random student with user account
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $kelas = $faker->randomElement(['X-1', 'X-2', 'XI-1']);
            
            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $kelas,
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Get current day in Indonesian
            $currentDay = $dayNames[now()->format('l')];

            // Create exactly 3 consecutive classes
            $class1Guru = Guru::create([
                'name' => $faker->name,
                'mata_pelajaran' => 'Matematika',
                'kelas' => $kelas,
            ]);

            $class1 = Jadwal::create([
                'guru_id' => $class1Guru->id,
                'kelas' => $kelas,
                'mata_pelajaran' => 'Matematika',
                'hari' => $currentDay,
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '09:00:00',
            ]);

            $class2Guru = Guru::create([
                'name' => $faker->name,
                'mata_pelajaran' => 'Fisika',
                'kelas' => $kelas,
            ]);

            $class2 = Jadwal::create([
                'guru_id' => $class2Guru->id,
                'kelas' => $kelas,
                'mata_pelajaran' => 'Fisika',
                'hari' => $currentDay,
                'jam_mulai' => '09:00:00',
                'jam_selesai' => '10:00:00',
            ]);

            $class3Guru = Guru::create([
                'name' => $faker->name,
                'mata_pelajaran' => 'Kimia',
                'kelas' => $kelas,
            ]);

            $class3 = Jadwal::create([
                'guru_id' => $class3Guru->id,
                'kelas' => $kelas,
                'mata_pelajaran' => 'Kimia',
                'hari' => $currentDay,
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '11:00:00',
            ]);

            // Mock current time to be in the middle class (class2)
            $mockCurrentTime = Carbon::today()->setTime(9, 30, 0);
            Carbon::setTestNow($mockCurrentTime);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayScheduleWidget();
            $currentClass = $widget->getCurrentClass();

            // Assert only class2 is identified as current
            $this->assertNotNull($currentClass, 'Current class should be identified');
            $this->assertEquals($class2->id, $currentClass->id, 'Should return class2 which matches current time');
            $this->assertEquals('Fisika', $currentClass->mata_pelajaran);
            $this->assertNotEquals($class1->id, $currentClass->id, 'Should not return class1');
            $this->assertNotEquals($class3->id, $currentClass->id, 'Should not return class3');

            // Reset Carbon test time
            Carbon::setTestNow();
        }
    }

    /**
     * Property Test: Class at exact start time is highlighted
     * 
     * For any class where the current time equals the start time,
     * that class should be highlighted.
     */
    public function test_class_at_exact_start_time_is_highlighted(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        $dayNames = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        for ($i = 0; $i < $iterations; $i++) {
            // Clear database for each iteration
            \Illuminate\Support\Facades\DB::table('jadwals')->delete();
            \Illuminate\Support\Facades\DB::table('gurus')->delete();
            \Illuminate\Support\Facades\DB::table('murids')->delete();
            \Illuminate\Support\Facades\DB::table('users')->delete();

            // Generate random student with user account
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $kelas = $faker->randomElement(['X-1', 'X-2', 'XI-1']);
            
            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $kelas,
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Get current day in Indonesian
            $currentDay = $dayNames[now()->format('l')];

            // Create a schedule
            $guru = Guru::create([
                'name' => $faker->name,
                'mata_pelajaran' => $faker->randomElement(['Matematika', 'Fisika']),
                'kelas' => $kelas,
            ]);

            $startHour = $faker->numberBetween(8, 14);
            $schedule = Jadwal::create([
                'guru_id' => $guru->id,
                'kelas' => $kelas,
                'mata_pelajaran' => $guru->mata_pelajaran,
                'hari' => $currentDay,
                'jam_mulai' => sprintf('%02d:00:00', $startHour),
                'jam_selesai' => sprintf('%02d:00:00', $startHour + 1),
            ]);

            // Mock current time to be exactly at start time
            $mockCurrentTime = Carbon::today()->setTime($startHour, 0, 0);
            Carbon::setTestNow($mockCurrentTime);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayScheduleWidget();
            $currentClass = $widget->getCurrentClass();

            // Assert class is highlighted at exact start time
            $this->assertNotNull($currentClass, 'Class should be highlighted at exact start time');
            $this->assertEquals($schedule->id, $currentClass->id);

            // Reset Carbon test time
            Carbon::setTestNow();
        }
    }

    /**
     * Property Test: Class at exact end time is highlighted
     * 
     * For any class where the current time equals the end time,
     * that class should still be highlighted.
     */
    public function test_class_at_exact_end_time_is_highlighted(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        $dayNames = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        for ($i = 0; $i < $iterations; $i++) {
            // Clear database for each iteration
            \Illuminate\Support\Facades\DB::table('jadwals')->delete();
            \Illuminate\Support\Facades\DB::table('gurus')->delete();
            \Illuminate\Support\Facades\DB::table('murids')->delete();
            \Illuminate\Support\Facades\DB::table('users')->delete();

            // Generate random student with user account
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $kelas = $faker->randomElement(['X-1', 'X-2', 'XI-1']);
            
            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $kelas,
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Get current day in Indonesian
            $currentDay = $dayNames[now()->format('l')];

            // Create a schedule
            $guru = Guru::create([
                'name' => $faker->name,
                'mata_pelajaran' => $faker->randomElement(['Matematika', 'Fisika']),
                'kelas' => $kelas,
            ]);

            $startHour = $faker->numberBetween(8, 14);
            $endHour = $startHour + 1;
            $schedule = Jadwal::create([
                'guru_id' => $guru->id,
                'kelas' => $kelas,
                'mata_pelajaran' => $guru->mata_pelajaran,
                'hari' => $currentDay,
                'jam_mulai' => sprintf('%02d:00:00', $startHour),
                'jam_selesai' => sprintf('%02d:00:00', $endHour),
            ]);

            // Mock current time to be exactly at end time
            $mockCurrentTime = Carbon::today()->setTime($endHour, 0, 0);
            Carbon::setTestNow($mockCurrentTime);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayScheduleWidget();
            $currentClass = $widget->getCurrentClass();

            // Assert class is highlighted at exact end time
            $this->assertNotNull($currentClass, 'Class should be highlighted at exact end time');
            $this->assertEquals($schedule->id, $currentClass->id);

            // Reset Carbon test time
            Carbon::setTestNow();
        }
    }

    /**
     * Property Test: No class highlighted for different day
     * 
     * For any schedule on a different day than today,
     * getCurrentClass should return null.
     */
    public function test_no_class_highlighted_for_different_day(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        $dayNames = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];

        for ($i = 0; $i < $iterations; $i++) {
            // Clear database for each iteration
            \Illuminate\Support\Facades\DB::table('jadwals')->delete();
            \Illuminate\Support\Facades\DB::table('gurus')->delete();
            \Illuminate\Support\Facades\DB::table('murids')->delete();
            \Illuminate\Support\Facades\DB::table('users')->delete();

            // Generate random student with user account
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $kelas = $faker->randomElement(['X-1', 'X-2', 'XI-1']);
            
            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $kelas,
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Get current day and a different day
            $currentDay = $dayNames[now()->format('l')];
            $otherDays = array_diff($dayNames, [$currentDay]);
            $differentDay = $faker->randomElement(array_values($otherDays));

            // Create schedule for a different day
            $guru = Guru::create([
                'name' => $faker->name,
                'mata_pelajaran' => $faker->randomElement(['Matematika', 'Fisika']),
                'kelas' => $kelas,
            ]);

            Jadwal::create([
                'guru_id' => $guru->id,
                'kelas' => $kelas,
                'mata_pelajaran' => $guru->mata_pelajaran,
                'hari' => $differentDay,
                'jam_mulai' => '09:00:00',
                'jam_selesai' => '10:00:00',
            ]);

            // Mock current time to be within the schedule time (but wrong day)
            $mockCurrentTime = Carbon::today()->setTime(9, 30, 0);
            Carbon::setTestNow($mockCurrentTime);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayScheduleWidget();
            $currentClass = $widget->getCurrentClass();

            // Assert no class is highlighted for different day
            $this->assertNull($currentClass, 'No class should be highlighted when schedule is for a different day');

            // Reset Carbon test time
            Carbon::setTestNow();
        }
    }
}
