<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 6: Today's Attendance Display
 * Validates: Requirements 3.2, 3.3
 * 
 * Property-based test for today's attendance display.
 * For any student viewing their dashboard, if an attendance record exists for today,
 * it should be displayed with status, timestamp, and notes; otherwise,
 * "not yet recorded" should be shown.
 */
class TodayAttendanceDisplayTest extends TestCase
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
     * Property Test: Today's attendance is displayed when record exists
     * 
     * For any student with an attendance record for today,
     * the widget should display the status, timestamp, and notes.
     */
    public function test_todays_attendance_is_displayed_when_record_exists(): void
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

            // Generate random attendance record for today
            $status = $faker->randomElement(['Hadir', 'Sakit', 'Izin', 'Alfa', 'Terlambat']);
            $isLate = $status === 'Terlambat';
            $lateDuration = $isLate ? $faker->numberBetween(1, 60) : null;
            $keterangan = $faker->optional(0.7)->sentence();
            
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => $status,
                'kelas' => $murid->kelas,
                'check_in_time' => $faker->time('H:i:s'),
                'is_late' => $isLate,
                'late_duration' => $lateDuration,
                'keterangan' => $keterangan,
            ]);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayAttendanceWidget();
            $todayAttendance = $widget->getTodayAttendance();

            // Assert attendance record is returned
            $this->assertNotNull($todayAttendance, 'Today\'s attendance should be returned');
            $this->assertEquals($absensi->id, $todayAttendance->id);
            $this->assertEquals($status, $todayAttendance->status);
            $this->assertEquals($isLate, $todayAttendance->is_late);
            $this->assertEquals($lateDuration, $todayAttendance->late_duration);
            $this->assertEquals($keterangan, $todayAttendance->keterangan);
            $this->assertNotNull($todayAttendance->check_in_time);
        }
    }

    /**
     * Property Test: No attendance message is shown when record doesn't exist
     * 
     * For any student without an attendance record for today,
     * the widget should return null, indicating "not yet recorded".
     */
    public function test_no_attendance_message_when_record_doesnt_exist(): void
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

            // Don't create any attendance record for today

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayAttendanceWidget();
            $todayAttendance = $widget->getTodayAttendance();

            // Assert no attendance record is returned
            $this->assertNull($todayAttendance, 'No attendance should be returned when record doesn\'t exist');
        }
    }

    /**
     * Property Test: Late attendance displays tardiness duration
     * 
     * For any student with a late attendance record,
     * the widget should display the tardiness duration.
     */
    public function test_late_attendance_displays_tardiness_duration(): void
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

            // Generate random late attendance record
            $lateDuration = $faker->numberBetween(1, 120);
            
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => 'Terlambat',
                'kelas' => $murid->kelas,
                'check_in_time' => $faker->time('H:i:s'),
                'is_late' => true,
                'late_duration' => $lateDuration,
            ]);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayAttendanceWidget();
            $todayAttendance = $widget->getTodayAttendance();

            // Assert late attendance has tardiness duration
            $this->assertNotNull($todayAttendance, 'Late attendance should be returned');
            $this->assertTrue($todayAttendance->is_late);
            $this->assertEquals($lateDuration, $todayAttendance->late_duration);
            $this->assertEquals('Terlambat', $todayAttendance->status);
        }
    }

    /**
     * Property Test: Attendance with proof document displays link
     * 
     * For any student with an attendance record that has a proof document,
     * the widget should include the proof document path.
     */
    public function test_attendance_with_proof_document_displays_link(): void
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

            // Generate random attendance record with proof document
            $status = $faker->randomElement(['Sakit', 'Izin']);
            $proofDocument = 'attendance-proofs/' . $murid->id . '/' . now()->format('Y-m-d') . '/' . $faker->uuid . '.pdf';
            
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => $status,
                'kelas' => $murid->kelas,
                'proof_document' => $proofDocument,
                'verification_status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'keterangan' => $faker->sentence(),
            ]);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayAttendanceWidget();
            $todayAttendance = $widget->getTodayAttendance();

            // Assert attendance has proof document
            $this->assertNotNull($todayAttendance, 'Attendance with proof should be returned');
            $this->assertNotNull($todayAttendance->proof_document);
            $this->assertEquals($proofDocument, $todayAttendance->proof_document);
        }
    }

    /**
     * Property Test: Only today's attendance is returned
     * 
     * For any student with attendance records on multiple days,
     * only today's record should be returned by the widget.
     */
    public function test_only_todays_attendance_is_returned(): void
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

            // Create attendance records for past days
            for ($j = 1; $j <= 5; $j++) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->subDays($j)->toDateString(),
                    'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin']),
                    'kelas' => $murid->kelas,
                ]);
            }

            // Create today's attendance record
            $todayAbsensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => 'Hadir',
                'kelas' => $murid->kelas,
                'check_in_time' => $faker->time('H:i:s'),
            ]);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayAttendanceWidget();
            $todayAttendance = $widget->getTodayAttendance();

            // Assert only today's attendance is returned
            $this->assertNotNull($todayAttendance, 'Today\'s attendance should be returned');
            $this->assertEquals($todayAbsensi->id, $todayAttendance->id);
            $this->assertEquals(now()->toDateString(), $todayAttendance->tanggal->toDateString());
        }
    }

    /**
     * Property Test: Student without user account returns null
     * 
     * For any request where the authenticated user has no linked Murid record,
     * the widget should return null.
     */
    public function test_student_without_murid_record_returns_null(): void
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
            $widget = new \App\Filament\Student\Widgets\TodayAttendanceWidget();
            $todayAttendance = $widget->getTodayAttendance();

            // Assert null is returned
            $this->assertNull($todayAttendance, 'No attendance should be returned when user has no Murid record');
        }
    }

    /**
     * Property Test: Verification status is included for sick/permission records
     * 
     * For any student with a sick or permission attendance record,
     * the verification status should be included.
     */
    public function test_verification_status_is_included_for_sick_permission_records(): void
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

            // Generate random sick/permission attendance with verification status
            $status = $faker->randomElement(['Sakit', 'Izin']);
            $verificationStatus = $faker->randomElement(['pending', 'approved', 'rejected']);
            
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => $status,
                'kelas' => $murid->kelas,
                'verification_status' => $verificationStatus,
                'proof_document' => 'attendance-proofs/' . $murid->id . '/proof.pdf',
            ]);

            // Act as the student user
            $this->actingAs($user);

            // Access the widget
            $widget = new \App\Filament\Student\Widgets\TodayAttendanceWidget();
            $todayAttendance = $widget->getTodayAttendance();

            // Assert verification status is included
            $this->assertNotNull($todayAttendance, 'Attendance should be returned');
            $this->assertEquals($verificationStatus, $todayAttendance->verification_status);
        }
    }
}
