<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\HariLibur;
use App\Models\Jadwal;
use App\Models\Murid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 13: Schedule Display for Current Day
 * Validates: Requirements 10.1, 10.2
 * 
 * Property-based test for schedule display.
 * For any student viewing today's schedule, the system should return all Jadwal records
 * matching the student's class and the current day of week.
 */
class ScheduleDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable broadcasting for tests
        \Illuminate\Support\Facades\Event::fake();
    }

    /**
     * Property Test: Correct schedule is displayed for student's class and current day
     * 
     * For any student with schedules for their class on the current day,
     * the widget should return all matching schedule records.
     */
    public function test_correct_schedule_is_displayed_for_students_class_and_current_day(): void
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
            \Illuminate\Support\Facades\DB::table('hari_liburs')->delete();
            // Generate random student with user account
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $kelas = $faker->randomElement(['X-1', 'X-2', 'XI-1', 'XI-2', 'XII-1', 'XII-2']);
            
            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $kelas,
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Get current day in Indonesian
            $currentDay = $dayNames[now()->format('l')];

            // Create random number of schedules for today
            $scheduleCount = $faker->numberBetween(1, 6);
            $createdSchedules = [];

            for ($j = 0; $j < $scheduleCount; $j++) {
                $guru = Guru::create([
                    'name' => $faker->name,
                    'mata_pelajaran' => $faker->randomElement(['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Fisika', 'Kimia']),
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

                $createdSchedules[] = $schedule;
            }

            // Create schedules for other days (should not be returned)
            $otherDays = array_diff($dayNames, [$currentDay]);
            $otherDay = $faker->randomElement(array_values($otherDays));
            
            $otherGuru = Guru::create([
                'name' => $faker->name,
                'mata_pelajaran' => $faker->randomElement(['Sejarah', 'Geografi']),
                'kelas' => $kelas,
            ]);

            Jadwal::create([
                'guru_id' => $otherGuru->id,
                'kelas' => $kelas,
                'mata_pelajaran' => $otherGuru->mata_pelajaran,
                'hari' => $otherDay,
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '09:00:00',
            ]);

            // Create schedules for other classes (should not be returned)
            $otherKelas = $faker->randomElement(['X-3', 'XI-3', 'XII-3']);
            $otherKelasGuru = Guru::create([
                'name' => $faker->name,
                'mata_pelajaran' => $faker->randomElement(['Biologi', 'Ekonomi']),
                'kelas' => $otherKelas,
            ]);

            Jadwal::create([
                'guru_id' => $otherKelasGuru->id,
                'kelas' => $otherKelas,
                'mata_pelajaran' => $otherKelasGuru->mata_pelajaran,
                'hari' => $currentDay,
                'jam_mulai' => '10:00:00',
                'jam_selesai' => '11:00:00',
            ]);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayScheduleWidget();
            $data = $widget->getTodaySchedule();

            // Assert correct schedules are returned
            $this->assertArrayHasKey('schedules', $data, 'Schedules key should exist');
            $schedules = $data['schedules'];
            
            $this->assertCount($scheduleCount, $schedules, 'Should return exactly the schedules for student\'s class and current day');

            // Verify each schedule matches the student's class and current day
            foreach ($schedules as $schedule) {
                $this->assertEquals($kelas, $schedule->kelas, 'Schedule should match student\'s class');
                $this->assertEquals($currentDay, $schedule->hari, 'Schedule should match current day');
                $this->assertNotNull($schedule->guru, 'Schedule should have guru relationship loaded');
            }

            // Verify schedules are ordered by start time
            $previousTime = null;
            foreach ($schedules as $schedule) {
                if ($previousTime !== null) {
                    $this->assertGreaterThanOrEqual(
                        $previousTime,
                        $schedule->jam_mulai,
                        'Schedules should be ordered by start time'
                    );
                }
                $previousTime = $schedule->jam_mulai;
            }
        }
    }

    /**
     * Property Test: No schedules message when schedule is empty
     * 
     * For any student with no schedules for the current day,
     * the widget should return an empty schedules array.
     */
    public function test_no_schedules_message_when_schedule_is_empty(): void
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
            \Illuminate\Support\Facades\DB::table('hari_liburs')->delete();
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

            // Don't create any schedules for today
            // Create schedules for other days only
            $currentDay = $dayNames[now()->format('l')];
            $otherDays = array_diff($dayNames, [$currentDay]);
            $otherDay = $faker->randomElement(array_values($otherDays));
            
            $guru = Guru::create([
                'name' => $faker->name,
                'mata_pelajaran' => $faker->randomElement(['Matematika', 'Fisika']),
                'kelas' => $kelas,
            ]);

            Jadwal::create([
                'guru_id' => $guru->id,
                'kelas' => $kelas,
                'mata_pelajaran' => $guru->mata_pelajaran,
                'hari' => $otherDay,
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '09:00:00',
            ]);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayScheduleWidget();
            $data = $widget->getTodaySchedule();

            // Assert empty schedules are returned
            $this->assertArrayHasKey('schedules', $data, 'Schedules key should exist');
            $this->assertCount(0, $data['schedules'], 'Should return empty schedules when no classes today');
        }
    }

    /**
     * Property Test: Holiday name is displayed when today is a holiday
     * 
     * For any day that is marked as a holiday,
     * the widget should return the holiday information instead of schedules.
     */
    public function test_holiday_name_is_displayed_when_today_is_holiday(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Clear database for each iteration
            \Illuminate\Support\Facades\DB::table('jadwals')->delete();
            \Illuminate\Support\Facades\DB::table('gurus')->delete();
            \Illuminate\Support\Facades\DB::table('murids')->delete();
            \Illuminate\Support\Facades\DB::table('users')->delete();
            \Illuminate\Support\Facades\DB::table('hari_liburs')->delete();
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

            // Create a holiday for today
            $holidayName = $faker->randomElement([
                'Hari Kemerdekaan',
                'Hari Raya Idul Fitri',
                'Tahun Baru',
                'Hari Pendidikan Nasional'
            ]);

            $holiday = HariLibur::create([
                'nama' => $holidayName,
                'tanggal' => now()->toDateString(),
                'keterangan' => $faker->sentence(),
                'jenis' => $faker->randomElement(['nasional', 'sekolah']),
            ]);

            // Create schedules (should not be returned because it's a holiday)
            $guru = Guru::create([
                'name' => $faker->name,
                'mata_pelajaran' => $faker->randomElement(['Matematika', 'Fisika']),
                'kelas' => $kelas,
            ]);

            $dayNames = [
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu',
            ];
            $currentDay = $dayNames[now()->format('l')];

            Jadwal::create([
                'guru_id' => $guru->id,
                'kelas' => $kelas,
                'mata_pelajaran' => $guru->mata_pelajaran,
                'hari' => $currentDay,
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '09:00:00',
            ]);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayScheduleWidget();
            $data = $widget->getTodaySchedule();

            // Assert holiday is returned instead of schedules
            $this->assertArrayHasKey('holiday', $data, 'Holiday key should exist');
            $this->assertArrayNotHasKey('schedules', $data, 'Schedules should not be returned on holidays');
            $this->assertEquals($holiday->id, $data['holiday']->id);
            $this->assertEquals($holidayName, $data['holiday']->nama);
        }
    }

    /**
     * Property Test: Schedule displays all required information
     * 
     * For any schedule record, the widget should display subject name,
     * teacher name, and time slot.
     */
    public function test_schedule_displays_all_required_information(): void
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
            \Illuminate\Support\Facades\DB::table('hari_liburs')->delete();
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

            // Create a schedule with all required information
            $guru = Guru::create([
                'name' => $faker->name,
                'mata_pelajaran' => $faker->randomElement(['Matematika', 'Bahasa Indonesia', 'Fisika']),
                'kelas' => $kelas,
            ]);

            $mataPelajaran = $faker->randomElement(['Matematika', 'Bahasa Indonesia', 'Fisika', 'Kimia']);
            $jamMulai = sprintf('%02d:00:00', $faker->numberBetween(7, 14));
            $jamSelesai = sprintf('%02d:00:00', $faker->numberBetween(8, 15));

            $schedule = Jadwal::create([
                'guru_id' => $guru->id,
                'kelas' => $kelas,
                'mata_pelajaran' => $mataPelajaran,
                'hari' => $currentDay,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
            ]);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayScheduleWidget();
            $data = $widget->getTodaySchedule();

            // Assert schedule has all required information
            $this->assertArrayHasKey('schedules', $data);
            $this->assertCount(1, $data['schedules']);
            
            $returnedSchedule = $data['schedules']->first();
            $this->assertEquals($mataPelajaran, $returnedSchedule->mata_pelajaran, 'Should have subject name');
            $this->assertNotNull($returnedSchedule->guru, 'Should have teacher relationship');
            $this->assertEquals($guru->name, $returnedSchedule->guru->name, 'Should have teacher name');
            $this->assertEquals($jamMulai, $returnedSchedule->jam_mulai, 'Should have start time');
            $this->assertEquals($jamSelesai, $returnedSchedule->jam_selesai, 'Should have end time');
        }
    }

    /**
     * Property Test: Student without murid record returns empty array
     * 
     * For any user without a linked Murid record,
     * the widget should return an empty array.
     */
    public function test_student_without_murid_record_returns_empty_array(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Clear database for each iteration
            \Illuminate\Support\Facades\DB::table('murids')->delete();
            \Illuminate\Support\Facades\DB::table('users')->delete();
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
            $widget = new \App\Filament\Student\Widgets\TodayScheduleWidget();
            $data = $widget->getTodaySchedule();

            // Assert empty array is returned
            $this->assertEquals([], $data, 'Should return empty array when user has no Murid record');
        }
    }
}
