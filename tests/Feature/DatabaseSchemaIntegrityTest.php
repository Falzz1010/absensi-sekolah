<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\QrCode;
use App\Models\StudentNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property: Schema Constraints
 * Validates: Requirements 9.2
 * 
 * Property-based test for database schema integrity.
 * Tests that foreign key constraints are properly enforced,
 * nullable fields accept null values, and enum fields only accept valid values.
 */
class DatabaseSchemaIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable broadcasting for tests to avoid connection errors
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\AbsensiCreated::class,
            \App\Events\AbsensiUpdated::class,
        ]);
    }

    /**
     * Property Test: Foreign key constraints are properly enforced
     * 
     * For any attempt to create a record with an invalid foreign key,
     * the database should reject the operation.
     */
    public function test_foreign_key_constraints_are_enforced(): void
    {
        // Test 1: murids.user_id foreign key constraint
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        DB::table('murids')->insert([
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'kelas' => 'X-1',
            'is_active' => true,
            'user_id' => 99999, // Non-existent user_id
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_murids_qr_code_foreign_key_constraint(): void
    {
        // Test 2: murids.qr_code_id foreign key constraint
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        DB::table('murids')->insert([
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'kelas' => 'X-1',
            'is_active' => true,
            'qr_code_id' => 99999, // Non-existent qr_code_id
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_absensis_verified_by_foreign_key_constraint(): void
    {
        // Test 3: absensis.verified_by foreign key constraint
        $murid = Murid::create([
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'kelas' => 'X-1',
            'is_active' => true,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        
        DB::table('absensis')->insert([
            'murid_id' => $murid->id,
            'tanggal' => now()->toDateString(),
            'status' => 'Hadir',
            'kelas' => 'X-1',
            'verified_by' => 99999, // Non-existent user_id
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_student_notifications_murid_foreign_key_constraint(): void
    {
        // Test 4: student_notifications.murid_id foreign key constraint
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        DB::table('student_notifications')->insert([
            'murid_id' => 99999, // Non-existent murid_id
            'type' => 'late_arrival',
            'title' => 'Test Notification',
            'message' => 'Test message',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Property Test: Foreign key cascade behavior
     * 
     * For any record with a foreign key that has onDelete('cascade'),
     * deleting the parent should delete the child records.
     */
    public function test_foreign_key_cascade_on_delete(): void
    {
        // Create a murid
        $murid = Murid::create([
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'kelas' => 'X-1',
            'is_active' => true,
        ]);

        // Create notifications for the murid
        $notification = StudentNotification::create([
            'murid_id' => $murid->id,
            'type' => 'late_arrival',
            'title' => 'Test Notification',
            'message' => 'Test message',
        ]);

        $notificationId = $notification->id;

        // Delete the murid
        $murid->delete();

        // Verify the notification was also deleted (cascade)
        $this->assertDatabaseMissing('student_notifications', [
            'id' => $notificationId,
        ]);
    }

    /**
     * Property Test: Foreign key set null behavior
     * 
     * For any record with a foreign key that has onDelete('set null'),
     * deleting the parent should set the foreign key to null.
     */
    public function test_foreign_key_set_null_on_delete(): void
    {
        // Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create a murid linked to the user
        $murid = Murid::create([
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'kelas' => 'X-1',
            'is_active' => true,
            'user_id' => $user->id,
        ]);

        $muridId = $murid->id;

        // Delete the user
        $user->delete();

        // Verify the murid's user_id was set to null
        $this->assertDatabaseHas('murids', [
            'id' => $muridId,
            'user_id' => null,
        ]);
    }

    /**
     * Property Test: Nullable fields accept null values
     * 
     * For any nullable field in the schema, inserting null should succeed.
     */
    public function test_nullable_fields_accept_null_values(): void
    {
        // Test murids table nullable fields
        $murid = Murid::create([
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'kelas' => 'X-1',
            'is_active' => true,
            'photo' => null,
            'qr_code_id' => null,
            'user_id' => null,
        ]);

        $this->assertDatabaseHas('murids', [
            'id' => $murid->id,
            'photo' => null,
            'qr_code_id' => null,
            'user_id' => null,
        ]);

        // Test absensis table nullable fields
        $absensi = Absensi::create([
            'murid_id' => $murid->id,
            'tanggal' => now()->toDateString(),
            'status' => 'Hadir',
            'kelas' => 'X-1',
            'proof_document' => null,
            'verification_status' => null,
            'verified_by' => null,
            'verified_at' => null,
            'check_in_time' => null,
            'late_duration' => null,
        ]);

        $this->assertDatabaseHas('absensis', [
            'id' => $absensi->id,
            'proof_document' => null,
            'verification_status' => null,
            'verified_by' => null,
            'verified_at' => null,
            'check_in_time' => null,
            'late_duration' => null,
        ]);

        // Test student_notifications table nullable fields
        $notification = StudentNotification::create([
            'murid_id' => $murid->id,
            'type' => 'late_arrival',
            'title' => 'Test',
            'message' => 'Test message',
            'data' => null,
            'read_at' => null,
        ]);

        $this->assertDatabaseHas('student_notifications', [
            'id' => $notification->id,
            'data' => null,
            'read_at' => null,
        ]);
    }

    /**
     * Property Test: Enum fields only accept valid values
     * 
     * For any enum field, only predefined values should be accepted.
     */
    public function test_enum_fields_only_accept_valid_values(): void
    {
        $murid = Murid::create([
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'kelas' => 'X-1',
            'is_active' => true,
        ]);

        // Test valid enum values for verification_status
        $validStatuses = ['pending', 'approved', 'rejected'];
        
        foreach ($validStatuses as $status) {
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->addDays(rand(1, 100))->toDateString(),
                'status' => 'Hadir',
                'kelas' => 'X-1',
                'verification_status' => $status,
            ]);

            $this->assertDatabaseHas('absensis', [
                'id' => $absensi->id,
                'verification_status' => $status,
            ]);
        }
    }

    public function test_enum_fields_reject_invalid_values(): void
    {
        $murid = Murid::create([
            'name' => 'Test Student',
            'email' => 'test@example.com',
            'kelas' => 'X-1',
            'is_active' => true,
        ]);

        // Test invalid enum value for verification_status
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        DB::table('absensis')->insert([
            'murid_id' => $murid->id,
            'tanggal' => now()->toDateString(),
            'status' => 'Hadir',
            'kelas' => 'X-1',
            'verification_status' => 'invalid_status', // Invalid enum value
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Property Test: Multiple iterations with random data
     * 
     * Run multiple iterations to ensure schema constraints hold
     * across various random inputs.
     */
    public function test_schema_constraints_with_random_data(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            // Create random valid data
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1', 'XII-1']),
                'is_active' => $faker->boolean,
                'photo' => $faker->optional()->imageUrl(),
            ]);

            // Verify murid was created
            $this->assertDatabaseHas('murids', [
                'id' => $murid->id,
                'email' => $murid->email,
            ]);

            // Create random attendance record
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => $faker->date(),
                'status' => $faker->randomElement(['Hadir', 'Sakit', 'Izin', 'Alfa']),
                'kelas' => $murid->kelas,
                'is_late' => $faker->boolean,
                'late_duration' => $faker->optional()->numberBetween(1, 120),
                'verification_status' => $faker->optional()->randomElement(['pending', 'approved', 'rejected']),
            ]);

            // Verify absensi was created
            $this->assertDatabaseHas('absensis', [
                'id' => $absensi->id,
                'murid_id' => $murid->id,
            ]);

            // Create random notification
            $notification = StudentNotification::create([
                'murid_id' => $murid->id,
                'type' => $faker->randomElement(['late_arrival', 'verification_update', 'schedule_change']),
                'title' => $faker->sentence,
                'message' => $faker->paragraph,
                'data' => $faker->optional()->randomElements(['key' => 'value']),
            ]);

            // Verify notification was created
            $this->assertDatabaseHas('student_notifications', [
                'id' => $notification->id,
                'murid_id' => $murid->id,
            ]);
        }

        // Verify we created the expected number of records
        $this->assertEquals($iterations, Murid::count());
        $this->assertEquals($iterations, Absensi::count());
        $this->assertEquals($iterations, StudentNotification::count());
    }
}
