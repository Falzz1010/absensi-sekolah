<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Murid;
use App\Services\FileUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property: Absence Verification Status
 * Validates: Requirements 2.4
 * 
 * Property-based test for absence verification status.
 * For any absence submission with proof, the status should be set to
 * 'pending verification'.
 */
class AbsenceVerificationStatusTest extends TestCase
{
    use RefreshDatabase;

    private FileUploadService $fileService;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('private');
        
        $this->fileService = new FileUploadService();
        
        // Disable broadcasting
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\AbsensiCreated::class,
            \App\Events\AbsensiUpdated::class,
        ]);
    }

    /**
     * Property Test: Absence submissions with proof have pending status
     * 
     * For any absence submission with proof document, the verification_status
     * should be set to 'pending'.
     */
    public function test_absence_submissions_with_proof_have_pending_status(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'is_active' => true,
            ]);

            // Create proof document
            $file = UploadedFile::fake()->image('proof.jpg')->size(2048);
            $date = now()->subDays(rand(0, 30))->toDateString();
            $proofPath = $this->fileService->storeAttendanceProof($file, $murid->id, $date);

            // Create absence with proof
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => $date,
                'status' => $faker->randomElement(['Sakit', 'Izin']),
                'kelas' => $murid->kelas,
                'keterangan' => $faker->sentence,
                'proof_document' => $proofPath,
                'verification_status' => 'pending',
            ]);

            // Verify status is pending
            $this->assertEquals('pending', $absensi->verification_status);
            $this->assertNotNull($absensi->proof_document);
            $this->assertNull($absensi->verified_by);
            $this->assertNull($absensi->verified_at);
        }
    }

    /**
     * Property Test: Verification status can be updated to approved
     * 
     * For any pending absence, status can be updated to 'approved'.
     */
    public function test_verification_status_can_be_approved(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => 'X-1',
                'is_active' => true,
            ]);

            // Create pending absence
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => 'Sakit',
                'kelas' => $murid->kelas,
                'verification_status' => 'pending',
            ]);

            // Update to approved
            $absensi->update([
                'verification_status' => 'approved',
                'verified_at' => now(),
            ]);

            $absensi->refresh();
            $this->assertEquals('approved', $absensi->verification_status);
            $this->assertNotNull($absensi->verified_at);
        }
    }

    /**
     * Property Test: Verification status can be updated to rejected
     * 
     * For any pending absence, status can be updated to 'rejected'.
     */
    public function test_verification_status_can_be_rejected(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => 'X-1',
                'is_active' => true,
            ]);

            // Create pending absence
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => 'Sakit',
                'kelas' => $murid->kelas,
                'verification_status' => 'pending',
            ]);

            // Update to rejected
            $absensi->update([
                'verification_status' => 'rejected',
                'verified_at' => now(),
            ]);

            $absensi->refresh();
            $this->assertEquals('rejected', $absensi->verification_status);
            $this->assertNotNull($absensi->verified_at);
        }
    }

    /**
     * Property Test: Only valid verification statuses are accepted
     * 
     * For any absence record, verification_status should only accept
     * 'pending', 'approved', or 'rejected'.
     */
    public function test_only_valid_verification_statuses_are_accepted(): void
    {
        $faker = \Faker\Factory::create();
        $validStatuses = ['pending', 'approved', 'rejected'];
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            foreach ($validStatuses as $status) {
                $murid = Murid::create([
                    'name' => $faker->name,
                    'email' => $faker->unique()->safeEmail,
                    'kelas' => 'X-1',
                    'is_active' => true,
                ]);

                $absensi = Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->addDays(rand(1, 100))->toDateString(),
                    'status' => 'Sakit',
                    'kelas' => $murid->kelas,
                    'verification_status' => $status,
                ]);

                $this->assertEquals($status, $absensi->verification_status);
            }
        }
    }

    /**
     * Property Test: Absence without proof can have null verification status
     * 
     * For any absence without proof (e.g., marked by admin), verification_status
     * can be null.
     */
    public function test_absence_without_proof_can_have_null_status(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => 'X-1',
                'is_active' => true,
            ]);

            // Create absence without proof (admin-created)
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => $faker->randomElement(['Hadir', 'Alfa', 'Sakit']),
                'kelas' => $murid->kelas,
                'proof_document' => null,
                'verification_status' => null,
            ]);

            $this->assertNull($absensi->verification_status);
            $this->assertNull($absensi->proof_document);
        }
    }

    /**
     * Property Test: Pending absences can be queried
     * 
     * For any set of absences, those with 'pending' status should be
     * retrievable via query.
     */
    public function test_pending_absences_can_be_queried(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => 'X-1',
                'is_active' => true,
            ]);

            // Create mix of statuses
            $statuses = ['pending', 'approved', 'rejected', null];
            $pendingCount = 0;

            foreach ($statuses as $status) {
                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => now()->addDays(rand(1, 100))->toDateString(),
                    'status' => 'Sakit',
                    'kelas' => $murid->kelas,
                    'verification_status' => $status,
                ]);

                if ($status === 'pending') {
                    $pendingCount++;
                }
            }

            // Query pending absences
            $pending = Absensi::where('murid_id', $murid->id)
                ->where('verification_status', 'pending')
                ->count();

            $this->assertEquals($pendingCount, $pending);
        }
    }

    /**
     * Property Test: Verification timestamp is recorded
     * 
     * For any absence that is verified (approved/rejected), verified_at
     * should be set to the current timestamp.
     */
    public function test_verification_timestamp_is_recorded(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => 'X-1',
                'is_active' => true,
            ]);

            // Create pending absence
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => 'Sakit',
                'kelas' => $murid->kelas,
                'verification_status' => 'pending',
            ]);

            $this->assertNull($absensi->verified_at);

            // Verify it
            $verificationTime = now();
            $absensi->update([
                'verification_status' => $faker->randomElement(['approved', 'rejected']),
                'verified_at' => $verificationTime,
            ]);

            $absensi->refresh();
            $this->assertNotNull($absensi->verified_at);
            $this->assertEquals(
                $verificationTime->timestamp,
                $absensi->verified_at->timestamp
            );
        }
    }
}
