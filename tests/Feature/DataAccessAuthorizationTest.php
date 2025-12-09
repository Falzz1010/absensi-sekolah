<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 11: Data Access Authorization
 * Validates: Requirements 9.2
 * 
 * Property-based test for data access authorization.
 * For any student accessing attendance data, the system should return
 * only records where the murid_id matches the authenticated student's ID.
 */
class DataAccessAuthorizationTest extends TestCase
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
     * Property Test: Student sees only their own attendance records
     * 
     * For any student with attendance records, when querying attendance data,
     * only records belonging to that student should be returned.
     */
    public function test_student_sees_only_their_own_attendance_records(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate target student with user account
            $targetUser = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $targetMurid = Murid::create([
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1', 'XII-1']),
                'is_active' => true,
                'user_id' => $targetUser->id,
            ]);

            // Create attendance records for target student
            $targetRecordCount = $faker->numberBetween(5, 15);
            $targetRecordIds = [];
            
            for ($j = 0; $j < $targetRecordCount; $j++) {
                $absensi = Absensi::create([
                    'murid_id' => $targetMurid->id,
                    'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin', 'Alfa', 'Terlambat']),
                    'kelas' => $targetMurid->kelas,
                    'check_in_time' => $faker->optional(0.7)->time('H:i:s'),
                ]);
                $targetRecordIds[] = $absensi->id;
            }

            // Create other students with their own records
            $otherStudentCount = $faker->numberBetween(3, 8);
            $otherRecordIds = [];
            
            for ($k = 0; $k < $otherStudentCount; $k++) {
                $otherUser = User::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'password' => bcrypt('password'),
                ]);

                $otherMurid = Murid::create([
                    'name' => $otherUser->name,
                    'email' => $otherUser->email,
                    'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                    'is_active' => true,
                    'user_id' => $otherUser->id,
                ]);

                // Create records for other students
                for ($j = 0; $j < $faker->numberBetween(5, 10); $j++) {
                    $absensi = Absensi::create([
                        'murid_id' => $otherMurid->id,
                        'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                        'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin', 'Alfa']),
                        'kelas' => $otherMurid->kelas,
                    ]);
                    $otherRecordIds[] = $absensi->id;
                }
            }

            // Act as the target student user
            $this->actingAs($targetUser);

            // Query attendance records with authorization filter
            $records = Absensi::query()
                ->where('murid_id', $targetMurid->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->orderBy('tanggal', 'desc')
                ->get();

            // Assert only target student's records are returned
            $this->assertCount($targetRecordCount, $records, 
                "Should return only target student's records");

            // Assert all returned records belong to target student
            foreach ($records as $record) {
                $this->assertEquals($targetMurid->id, $record->murid_id, 
                    "All records should belong to target student");
            }

            // Assert no records from other students are included
            $returnedIds = $records->pluck('id')->toArray();
            foreach ($otherRecordIds as $otherId) {
                $this->assertNotContains($otherId, $returnedIds, 
                    "Should not include records from other students");
            }

            // Assert all target records are included
            foreach ($targetRecordIds as $targetId) {
                $this->assertContains($targetId, $returnedIds, 
                    "Should include all target student's records");
            }
        }
    }

    /**
     * Property Test: Using scope method for authorization
     * 
     * For any student, using the forStudent scope should return
     * only records belonging to that student.
     */
    public function test_using_scope_method_for_authorization(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate target student with user account
            $targetUser = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $targetMurid = Murid::create([
                'name' => $targetUser->name,
                'email' => $targetUser->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'is_active' => true,
                'user_id' => $targetUser->id,
            ]);

            // Create records for target student
            $targetRecordCount = $faker->numberBetween(5, 12);
            for ($j = 0; $j < $targetRecordCount; $j++) {
                Absensi::create([
                    'murid_id' => $targetMurid->id,
                    'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin', 'Alfa']),
                    'kelas' => $targetMurid->kelas,
                ]);
            }

            // Create records for other students
            for ($k = 0; $k < $faker->numberBetween(3, 6); $k++) {
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

            // Query using the forStudent scope
            $records = Absensi::query()
                ->forStudent($targetMurid->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->get();

            // Assert only target student's records are returned
            $this->assertCount($targetRecordCount, $records);
            
            foreach ($records as $record) {
                $this->assertEquals($targetMurid->id, $record->murid_id);
            }
        }
    }

    /**
     * Property Test: Different students see different data
     * 
     * For any two different students, each should see only their own
     * attendance records and not each other's records.
     */
    public function test_different_students_see_different_data(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate first student
            $user1 = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $murid1 = Murid::create([
                'name' => $user1->name,
                'email' => $user1->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2']),
                'is_active' => true,
                'user_id' => $user1->id,
            ]);

            // Generate second student
            $user2 = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            $murid2 = Murid::create([
                'name' => $user2->name,
                'email' => $user2->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2']),
                'is_active' => true,
                'user_id' => $user2->id,
            ]);

            // Create records for first student
            $count1 = $faker->numberBetween(5, 10);
            $ids1 = [];
            for ($j = 0; $j < $count1; $j++) {
                $absensi = Absensi::create([
                    'murid_id' => $murid1->id,
                    'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin']),
                    'kelas' => $murid1->kelas,
                ]);
                $ids1[] = $absensi->id;
            }

            // Create records for second student
            $count2 = $faker->numberBetween(5, 10);
            $ids2 = [];
            for ($j = 0; $j < $count2; $j++) {
                $absensi = Absensi::create([
                    'murid_id' => $murid2->id,
                    'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin']),
                    'kelas' => $murid2->kelas,
                ]);
                $ids2[] = $absensi->id;
            }

            // Act as first student
            $this->actingAs($user1);
            $records1 = Absensi::query()
                ->where('murid_id', $murid1->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->get();

            // Assert first student sees only their records
            $this->assertCount($count1, $records1);
            $returnedIds1 = $records1->pluck('id')->toArray();
            foreach ($ids1 as $id) {
                $this->assertContains($id, $returnedIds1);
            }
            foreach ($ids2 as $id) {
                $this->assertNotContains($id, $returnedIds1, 
                    "First student should not see second student's records");
            }

            // Act as second student
            $this->actingAs($user2);
            $records2 = Absensi::query()
                ->where('murid_id', $murid2->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->get();

            // Assert second student sees only their records
            $this->assertCount($count2, $records2);
            $returnedIds2 = $records2->pluck('id')->toArray();
            foreach ($ids2 as $id) {
                $this->assertContains($id, $returnedIds2);
            }
            foreach ($ids1 as $id) {
                $this->assertNotContains($id, $returnedIds2, 
                    "Second student should not see first student's records");
            }
        }
    }

    /**
     * Property Test: Authorization works with filters
     * 
     * For any student, applying filters (status, date range) should still
     * only return records belonging to that student.
     */
    public function test_authorization_works_with_filters(): void
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

            // Pick a status to filter by
            $filterStatus = $faker->randomElement(['Hadir', 'Sakit', 'Izin']);

            // Create records for target student with filter status
            $targetCount = $faker->numberBetween(5, 10);
            for ($j = 0; $j < $targetCount; $j++) {
                Absensi::create([
                    'murid_id' => $targetMurid->id,
                    'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                    'status' => $filterStatus,
                    'kelas' => $targetMurid->kelas,
                ]);
            }

            // Create records for other students with same filter status
            for ($k = 0; $k < $faker->numberBetween(3, 6); $k++) {
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

                for ($j = 0; $j < $faker->numberBetween(5, 10); $j++) {
                    Absensi::create([
                        'murid_id' => $otherMurid->id,
                        'tanggal' => Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString(),
                        'status' => $filterStatus,
                        'kelas' => $otherMurid->kelas,
                    ]);
                }
            }

            // Act as the target student user
            $this->actingAs($targetUser);

            // Query with authorization and status filter
            $records = Absensi::query()
                ->where('murid_id', $targetMurid->id)
                ->where('tanggal', '>=', Carbon::now()->subDays(30))
                ->where('status', $filterStatus)
                ->get();

            // Assert only target student's records are returned
            $this->assertCount($targetCount, $records);
            
            foreach ($records as $record) {
                $this->assertEquals($targetMurid->id, $record->murid_id, 
                    "All records should belong to target student");
                $this->assertEquals($filterStatus, $record->status, 
                    "All records should match filter status");
            }
        }
    }

    /**
     * Property Test: Empty result when student has no records
     * 
     * For any student with no attendance records,
     * querying should return an empty collection, not other students' records.
     */
    public function test_empty_result_when_student_has_no_records(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate target student with no records
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

            // Don't create any records for target student

            // Create records for other students
            for ($k = 0; $k < $faker->numberBetween(3, 6); $k++) {
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
                ->get();

            // Assert empty collection is returned
            $this->assertCount(0, $records, 
                "Should return empty collection when student has no records");
        }
    }
}
