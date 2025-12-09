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
 * Feature: student-attendance-portal, Property 5: Proof Document Association
 * Validates: Requirements 2.3
 * 
 * Property-based test for proof document association.
 * For any valid document upload with an absence submission, the file should
 * be stored securely and the file path should be correctly associated with
 * the corresponding attendance record.
 */
class ProofDocumentAssociationTest extends TestCase
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
     * Property Test: Proof documents are correctly associated with absence records
     * 
     * For any valid proof document, it should be stored and linked to the
     * correct attendance record.
     */
    public function test_proof_documents_are_associated_with_absence_records(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            // Create student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'is_active' => true,
            ]);

            // Create and store proof document
            $file = UploadedFile::fake()->image('proof.jpg')->size(2048);
            $date = now()->subDays(rand(0, 30))->toDateString();
            $proofPath = $this->fileService->storeAttendanceProof($file, $murid->id, $date);

            $this->assertNotNull($proofPath, 'Proof document should be stored');

            // Create absence record with proof
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => $date,
                'status' => $faker->randomElement(['Sakit', 'Izin']),
                'kelas' => $murid->kelas,
                'keterangan' => $faker->sentence,
                'proof_document' => $proofPath,
                'verification_status' => 'pending',
            ]);

            // Verify association
            $this->assertNotNull($absensi->proof_document, 'Absence should have proof document');
            $this->assertEquals($proofPath, $absensi->proof_document, 'Proof path should match');
            
            // Verify file exists in storage
            Storage::disk('private')->assertExists($proofPath);
            
            // Verify file can be retrieved
            $this->assertTrue($this->fileService->fileExists($proofPath));
        }
    }

    /**
     * Property Test: Proof documents are stored in correct directory structure
     * 
     * For any proof document, it should be stored in the correct directory
     * following the pattern: attendance-proofs/{student_id}/{date}/
     */
    public function test_proof_documents_follow_directory_structure(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            $studentId = rand(1, 1000);
            $date = now()->subDays(rand(0, 365))->toDateString();

            $file = UploadedFile::fake()->image('proof.jpg')->size(1024);
            $path = $this->fileService->storeAttendanceProof($file, $studentId, $date);

            $this->assertNotNull($path);
            $this->assertStringContainsString("attendance-proofs/{$studentId}/{$date}", $path);
            
            // Verify directory structure
            $expectedPrefix = "attendance-proofs/{$studentId}/{$date}/";
            $this->assertStringStartsWith($expectedPrefix, $path);
        }
    }

    /**
     * Property Test: Multiple proofs can be stored for different dates
     * 
     * For any student, multiple proof documents for different dates
     * should all be stored correctly.
     */
    public function test_multiple_proofs_for_different_dates(): void
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

            $proofPaths = [];
            $dates = [];

            // Create 3-5 absence records with proofs for different dates
            $numAbsences = rand(3, 5);
            for ($j = 0; $j < $numAbsences; $j++) {
                $date = now()->subDays($j * 7)->toDateString();
                $dates[] = $date;

                $file = UploadedFile::fake()->image("proof{$j}.jpg")->size(1024);
                $proofPath = $this->fileService->storeAttendanceProof($file, $murid->id, $date);
                $proofPaths[] = $proofPath;

                Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => $date,
                    'status' => 'Sakit',
                    'kelas' => $murid->kelas,
                    'proof_document' => $proofPath,
                    'verification_status' => 'pending',
                ]);
            }

            // Verify all proofs are stored
            foreach ($proofPaths as $path) {
                $this->assertNotNull($path);
                Storage::disk('private')->assertExists($path);
            }

            // Verify all absence records have correct proofs
            $absences = Absensi::where('murid_id', $murid->id)->get();
            $this->assertCount($numAbsences, $absences);

            foreach ($absences as $absence) {
                $this->assertNotNull($absence->proof_document);
                $this->assertContains($absence->proof_document, $proofPaths);
            }
        }
    }

    /**
     * Property Test: Absence records without proofs are allowed
     * 
     * For any absence record, proof_document can be null (for cases
     * where admin creates the record).
     */
    public function test_absence_records_without_proofs_are_allowed(): void
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

            // Create absence without proof
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => 'Alfa',
                'kelas' => $murid->kelas,
                'proof_document' => null,
            ]);

            $this->assertNull($absensi->proof_document);
            $this->assertFalse($absensi->hasProof());
        }
    }

    /**
     * Property Test: Proof document paths are unique
     * 
     * For any multiple uploads, each proof document should have a unique path.
     */
    public function test_proof_document_paths_are_unique(): void
    {
        $iterations = 100;
        $allPaths = [];

        for ($i = 0; $i < $iterations; $i++) {
            $studentId = 1;
            $date = now()->toDateString();

            $file = UploadedFile::fake()->image('proof.jpg')->size(1024);
            $path = $this->fileService->storeAttendanceProof($file, $studentId, $date);

            $this->assertNotNull($path);
            $this->assertNotContains($path, $allPaths, 'Each proof should have unique path');
            
            $allPaths[] = $path;
        }

        // Verify all paths are unique
        $this->assertEquals($iterations, count(array_unique($allPaths)));
    }

    /**
     * Property Test: hasProof() method works correctly
     * 
     * For any absence record, hasProof() should return true if proof_document
     * is not null, and false otherwise.
     */
    public function test_has_proof_method_works_correctly(): void
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

            // Test with proof
            $file = UploadedFile::fake()->image('proof.jpg')->size(1024);
            $proofPath = $this->fileService->storeAttendanceProof($file, $murid->id, now()->toDateString());

            $absensiWithProof = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->toDateString(),
                'status' => 'Sakit',
                'kelas' => $murid->kelas,
                'proof_document' => $proofPath,
            ]);

            $this->assertTrue($absensiWithProof->hasProof());

            // Test without proof
            $absensiWithoutProof = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => now()->subDay()->toDateString(),
                'status' => 'Alfa',
                'kelas' => $murid->kelas,
                'proof_document' => null,
            ]);

            $this->assertFalse($absensiWithoutProof->hasProof());
        }
    }
}
