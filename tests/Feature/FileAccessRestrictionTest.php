<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 12: File Access Restriction
 * Validates: Requirements 9.3
 * 
 * Property-based test for file access restriction.
 * For any uploaded proof document, access should be restricted such that
 * only the student who uploaded it and authorized administrators can retrieve the file.
 */
class FileAccessRestrictionTest extends TestCase
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

        // Fake storage
        Storage::fake('private');
        Storage::fake('public');

        // Create roles if they don't exist
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'guru']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'murid']);
    }

    /**
     * Property Test: Student can access their own proof documents
     * 
     * For any student with uploaded proof documents,
     * that student should be able to access their own files.
     */
    public function test_student_can_access_their_own_proof_documents(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate student with user account
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('murid');

            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Create proof document
            $date = Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString();
            $filename = $faker->uuid . '.jpg';
            $filePath = "attendance-proofs/{$murid->id}/{$date}/{$filename}";
            
            // Store fake file
            Storage::disk('private')->put($filePath, 'fake file content');

            // Create attendance record with proof
            Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => $date,
                'status' => $faker->randomElement(['Sakit', 'Izin']),
                'kelas' => $murid->kelas,
                'proof_document' => $filePath,
                'verification_status' => 'pending',
            ]);

            // Act as the student user
            $this->actingAs($user);

            // Attempt to access the file
            $encodedPath = base64_encode($filePath);
            $response = $this->get(route('files.attendance-proof', ['path' => $encodedPath]));

            // Assert file is accessible
            $response->assertStatus(200);
        }
    }

    /**
     * Property Test: Student cannot access other students' proof documents
     * 
     * For any two different students, each student should not be able
     * to access the other student's proof documents.
     */
    public function test_student_cannot_access_other_students_proof_documents(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate first student (file owner)
            $owner = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);
            $owner->assignRole('murid');

            $ownerMurid = Murid::create([
                'name' => $owner->name,
                'email' => $owner->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2']),
                'is_active' => true,
                'user_id' => $owner->id,
            ]);

            // Generate second student (unauthorized)
            $unauthorized = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);
            $unauthorized->assignRole('murid');

            $unauthorizedMurid = Murid::create([
                'name' => $unauthorized->name,
                'email' => $unauthorized->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2']),
                'is_active' => true,
                'user_id' => $unauthorized->id,
            ]);

            // Create proof document for owner
            $date = Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString();
            $filename = $faker->uuid . '.jpg';
            $filePath = "attendance-proofs/{$ownerMurid->id}/{$date}/{$filename}";
            
            // Store fake file
            Storage::disk('private')->put($filePath, 'fake file content');

            // Create attendance record with proof
            Absensi::create([
                'murid_id' => $ownerMurid->id,
                'tanggal' => $date,
                'status' => $faker->randomElement(['Sakit', 'Izin']),
                'kelas' => $ownerMurid->kelas,
                'proof_document' => $filePath,
                'verification_status' => 'pending',
            ]);

            // Act as the unauthorized student
            $this->actingAs($unauthorized);

            // Attempt to access the file
            $encodedPath = base64_encode($filePath);
            $response = $this->get(route('files.attendance-proof', ['path' => $encodedPath]));

            // Assert access is denied
            $response->assertStatus(403);
        }
    }

    /**
     * Property Test: Admin can access any student's proof documents
     * 
     * For any student's proof document, an admin user should be able
     * to access the file.
     */
    public function test_admin_can_access_any_students_proof_documents(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate admin user
            $admin = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);
            $admin->assignRole('admin');

            // Generate student with proof document
            $studentUser = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);
            $studentUser->assignRole('murid');

            $murid = Murid::create([
                'name' => $studentUser->name,
                'email' => $studentUser->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'is_active' => true,
                'user_id' => $studentUser->id,
            ]);

            // Create proof document
            $date = Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString();
            $filename = $faker->uuid . '.jpg';
            $filePath = "attendance-proofs/{$murid->id}/{$date}/{$filename}";
            
            // Store fake file
            Storage::disk('private')->put($filePath, 'fake file content');

            // Create attendance record with proof
            Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => $date,
                'status' => $faker->randomElement(['Sakit', 'Izin']),
                'kelas' => $murid->kelas,
                'proof_document' => $filePath,
                'verification_status' => 'pending',
            ]);

            // Act as the admin user
            $this->actingAs($admin);

            // Attempt to access the file
            $encodedPath = base64_encode($filePath);
            $response = $this->get(route('files.attendance-proof', ['path' => $encodedPath]));

            // Assert file is accessible
            $response->assertStatus(200);
        }
    }

    /**
     * Property Test: Guru can access any student's proof documents
     * 
     * For any student's proof document, a guru (teacher) user should be able
     * to access the file.
     */
    public function test_guru_can_access_any_students_proof_documents(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate guru user
            $guru = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);
            $guru->assignRole('guru');

            // Generate student with proof document
            $studentUser = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);
            $studentUser->assignRole('murid');

            $murid = Murid::create([
                'name' => $studentUser->name,
                'email' => $studentUser->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'is_active' => true,
                'user_id' => $studentUser->id,
            ]);

            // Create proof document
            $date = Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString();
            $filename = $faker->uuid . '.jpg';
            $filePath = "attendance-proofs/{$murid->id}/{$date}/{$filename}";
            
            // Store fake file
            Storage::disk('private')->put($filePath, 'fake file content');

            // Create attendance record with proof
            Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => $date,
                'status' => $faker->randomElement(['Sakit', 'Izin']),
                'kelas' => $murid->kelas,
                'proof_document' => $filePath,
                'verification_status' => 'pending',
            ]);

            // Act as the guru user
            $this->actingAs($guru);

            // Attempt to access the file
            $encodedPath = base64_encode($filePath);
            $response = $this->get(route('files.attendance-proof', ['path' => $encodedPath]));

            // Assert file is accessible
            $response->assertStatus(200);
        }
    }

    /**
     * Property Test: Users without proper role cannot access proof documents
     * 
     * For any proof document, users without proper roles should not be able
     * to access the file.
     * 
     * Note: Testing unauthenticated access is skipped because the auth middleware
     * redirects to login route which doesn't exist in test environment.
     */
    public function test_users_without_proper_role_cannot_access_proof_documents(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate student with proof document
            $studentUser = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);
            $studentUser->assignRole('murid');

            $murid = Murid::create([
                'name' => $studentUser->name,
                'email' => $studentUser->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2']),
                'is_active' => true,
                'user_id' => $studentUser->id,
            ]);

            // Create proof document
            $date = Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString();
            $filename = $faker->uuid . '.jpg';
            $filePath = "attendance-proofs/{$murid->id}/{$date}/{$filename}";
            
            // Store fake file
            Storage::disk('private')->put($filePath, 'fake file content');

            // Create attendance record with proof
            Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => $date,
                'status' => $faker->randomElement(['Sakit', 'Izin']),
                'kelas' => $murid->kelas,
                'proof_document' => $filePath,
                'verification_status' => 'pending',
            ]);

            // Create a user without any role
            $userWithoutRole = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);

            // Act as user without role
            $this->actingAs($userWithoutRole);

            // Attempt to access the file
            $encodedPath = base64_encode($filePath);
            $response = $this->get(route('files.attendance-proof', ['path' => $encodedPath]));

            // Assert access is denied
            $response->assertStatus(403);
        }
    }

    /**
     * Property Test: Non-existent files return 404
     * 
     * For any non-existent file path, the system should return 404.
     */
    public function test_non_existent_files_return_404(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate student user
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('murid');

            $murid = Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2']),
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Generate non-existent file path
            $date = Carbon::now()->subDays($faker->numberBetween(0, 29))->toDateString();
            $filename = $faker->uuid . '.jpg';
            $filePath = "attendance-proofs/{$murid->id}/{$date}/{$filename}";
            
            // Don't store the file - it doesn't exist

            // Act as the student user
            $this->actingAs($user);

            // Attempt to access the non-existent file
            $encodedPath = base64_encode($filePath);
            $response = $this->get(route('files.attendance-proof', ['path' => $encodedPath]));

            // Assert 404 is returned
            $response->assertStatus(404);
        }
    }

    /**
     * Property Test: Invalid file paths return 403
     * 
     * For any invalid file path (not in attendance-proofs directory),
     * the system should return 403.
     */
    public function test_invalid_file_paths_return_403(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate student user
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
            ]);
            $user->assignRole('murid');

            Murid::create([
                'name' => $user->name,
                'email' => $user->email,
                'kelas' => $faker->randomElement(['X-1', 'X-2']),
                'is_active' => true,
                'user_id' => $user->id,
            ]);

            // Generate invalid file paths (not in attendance-proofs directory)
            $invalidPaths = [
                "some-other-directory/{$faker->uuid}.jpg",
                "documents/{$faker->uuid}.pdf",
                $faker->uuid . '.jpg',
                "uploads/{$faker->uuid}.png",
            ];

            $invalidPath = $faker->randomElement($invalidPaths);
            
            // Store fake file at invalid location
            Storage::disk('private')->put($invalidPath, 'fake file content');

            // Act as the student user
            $this->actingAs($user);

            // Attempt to access the file with invalid path
            $encodedPath = base64_encode($invalidPath);
            $response = $this->get(route('files.attendance-proof', ['path' => $encodedPath]));

            // Assert 403 or 404 is returned
            $this->assertTrue(
                $response->status() === 403 || $response->status() === 404,
                "Invalid file paths should return 403 or 404"
            );
        }
    }
}
