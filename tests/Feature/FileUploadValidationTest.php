<?php

namespace Tests\Feature;

use App\Services\FileUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 4: File Upload Validation
 * Validates: Requirements 2.2
 * 
 * Property-based test for file upload validation.
 * For any file upload attempt, if the file type is not in the allowed list
 * (JPEG, PNG, PDF) or size exceeds 5MB, the upload should be rejected
 * with a specific error message.
 */
class FileUploadValidationTest extends TestCase
{
    use RefreshDatabase;

    private FileUploadService $fileService;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('private');
        Storage::fake('public');
        
        $this->fileService = new FileUploadService();
    }

    /**
     * Property Test: Valid file types are accepted
     * 
     * For any file with valid type (JPEG, PNG, PDF) and size <= 5MB,
     * validation should pass.
     */
    public function test_valid_file_types_are_accepted(): void
    {
        $validTypes = [
            ['image/jpeg', 'jpg'],
            ['image/png', 'png'],
            ['application/pdf', 'pdf'],
        ];

        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            foreach ($validTypes as [$mimeType, $extension]) {
                // Create file with valid size (1MB to 4MB)
                $sizeKB = rand(1024, 4096);
                $file = UploadedFile::fake()->create("document.{$extension}", $sizeKB, $mimeType);

                $validation = $this->fileService->validateFile($file);

                $this->assertTrue($validation['valid'], "File type {$mimeType} should be valid");
                $this->assertEmpty($validation['errors'], "No errors should be present for valid file");
            }
        }
    }

    /**
     * Property Test: Invalid file types are rejected
     * 
     * For any file with invalid type, validation should fail
     * with appropriate error message.
     */
    public function test_invalid_file_types_are_rejected(): void
    {
        $invalidTypes = [
            ['text/plain', 'txt'],
            ['application/zip', 'zip'],
            ['video/mp4', 'mp4'],
            ['audio/mpeg', 'mp3'],
            ['application/msword', 'doc'],
            ['application/vnd.ms-excel', 'xls'],
        ];

        $iterations = 30;

        for ($i = 0; $i < $iterations; $i++) {
            foreach ($invalidTypes as [$mimeType, $extension]) {
                // Create file with valid size but invalid type
                $file = UploadedFile::fake()->create("document.{$extension}", 1024, $mimeType);

                $validation = $this->fileService->validateFile($file);

                $this->assertFalse($validation['valid'], "File type {$mimeType} should be invalid");
                $this->assertNotEmpty($validation['errors'], "Errors should be present for invalid file type");
                $this->assertStringContainsString('Tipe file tidak valid', implode(' ', $validation['errors']));
            }
        }
    }

    /**
     * Property Test: Files exceeding size limit are rejected
     * 
     * For any file larger than 5MB, validation should fail
     * with appropriate error message.
     */
    public function test_oversized_files_are_rejected(): void
    {
        $validTypes = [
            ['image/jpeg', 'jpg'],
            ['image/png', 'png'],
            ['application/pdf', 'pdf'],
        ];

        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            foreach ($validTypes as [$mimeType, $extension]) {
                // Create file larger than 5MB (5121KB to 10000KB)
                $sizeKB = rand(5121, 10000);
                $file = UploadedFile::fake()->create("document.{$extension}", $sizeKB, $mimeType);

                $validation = $this->fileService->validateFile($file);

                $this->assertFalse($validation['valid'], "File size {$sizeKB}KB should be invalid");
                $this->assertNotEmpty($validation['errors'], "Errors should be present for oversized file");
                $this->assertStringContainsString('Ukuran file terlalu besar', implode(' ', $validation['errors']));
            }
        }
    }

    /**
     * Property Test: Files at size boundary are handled correctly
     * 
     * For any file exactly at or just below 5MB, validation should pass.
     * For any file just above 5MB, validation should fail.
     */
    public function test_size_boundary_is_enforced_correctly(): void
    {
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Test just below limit (5119KB = ~4.99MB)
            $fileBelowLimit = UploadedFile::fake()->create('document.jpg', 5119, 'image/jpeg');
            $validationBelow = $this->fileService->validateFile($fileBelowLimit);
            $this->assertTrue($validationBelow['valid'], 'File just below 5MB should be valid');

            // Test at limit (5120KB = exactly 5MB)
            $fileAtLimit = UploadedFile::fake()->create('document.jpg', 5120, 'image/jpeg');
            $validationAt = $this->fileService->validateFile($fileAtLimit);
            $this->assertTrue($validationAt['valid'], 'File at exactly 5MB should be valid');

            // Test just above limit (5121KB = ~5.001MB)
            $fileAboveLimit = UploadedFile::fake()->create('document.jpg', 5121, 'image/jpeg');
            $validationAbove = $this->fileService->validateFile($fileAboveLimit);
            $this->assertFalse($validationAbove['valid'], 'File just above 5MB should be invalid');
        }
    }

    /**
     * Property Test: Multiple validation errors are reported
     * 
     * For any file with both invalid type and size, both errors
     * should be reported.
     */
    public function test_multiple_validation_errors_are_reported(): void
    {
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Create file with invalid type AND oversized
            $file = UploadedFile::fake()->create('document.txt', 6000, 'text/plain');

            $validation = $this->fileService->validateFile($file);

            $this->assertFalse($validation['valid'], 'File with multiple issues should be invalid');
            $this->assertGreaterThanOrEqual(2, count($validation['errors']), 'Multiple errors should be reported');
            
            $errorsString = implode(' ', $validation['errors']);
            $this->assertStringContainsString('Tipe file tidak valid', $errorsString);
            $this->assertStringContainsString('Ukuran file terlalu besar', $errorsString);
        }
    }

    /**
     * Property Test: File storage works for valid files
     * 
     * For any valid file, storage should succeed and return a path.
     */
    public function test_valid_files_can_be_stored(): void
    {
        $iterations = 30;

        for ($i = 0; $i < $iterations; $i++) {
            $studentId = rand(1, 1000);
            $date = now()->subDays(rand(0, 30))->toDateString();

            // Create valid file
            $file = UploadedFile::fake()->image('proof.jpg', 800, 600)->size(2048);

            $path = $this->fileService->storeAttendanceProof($file, $studentId, $date);

            $this->assertNotNull($path, 'Valid file should be stored successfully');
            $this->assertStringContainsString("attendance-proofs/{$studentId}/{$date}", $path);
            Storage::disk('private')->assertExists($path);
        }
    }

    /**
     * Property Test: Invalid files cannot be stored
     * 
     * For any invalid file, storage should fail and return null.
     */
    public function test_invalid_files_cannot_be_stored(): void
    {
        $iterations = 30;

        for ($i = 0; $i < $iterations; $i++) {
            $studentId = rand(1, 1000);
            $date = now()->toDateString();

            // Create invalid file (wrong type)
            $file = UploadedFile::fake()->create('document.txt', 1024, 'text/plain');

            $path = $this->fileService->storeAttendanceProof($file, $studentId, $date);

            $this->assertNull($path, 'Invalid file should not be stored');
        }
    }

    /**
     * Property Test: Stored files have unique names
     * 
     * For any multiple file uploads, each should have a unique filename.
     */
    public function test_stored_files_have_unique_names(): void
    {
        $iterations = 50;
        $storedPaths = [];

        for ($i = 0; $i < $iterations; $i++) {
            $studentId = 1;
            $date = now()->toDateString();

            $file = UploadedFile::fake()->image('proof.jpg')->size(1024);
            $path = $this->fileService->storeAttendanceProof($file, $studentId, $date);

            $this->assertNotNull($path);
            $this->assertNotContains($path, $storedPaths, 'Each file should have a unique path');
            
            $storedPaths[] = $path;
        }

        // Verify all paths are unique
        $this->assertEquals($iterations, count(array_unique($storedPaths)));
    }
}
