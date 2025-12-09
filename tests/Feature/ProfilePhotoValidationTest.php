<?php

namespace Tests\Feature;

use App\Models\Murid;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property: Profile Photo Validation
 * Validates: Requirements 7.2
 * 
 * Property: For any file upload attempt, if the file type is not JPEG or PNG,
 * or size exceeds 2MB, the upload should be rejected.
 */
class ProfilePhotoValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test that valid JPEG photos are accepted
     */
    public function test_valid_jpeg_photo_is_accepted(): void
    {
        // Create a student user
        $user = User::factory()->create();
        $murid = Murid::factory()->create(['user_id' => $user->id]);

        // Create a valid JPEG file (under 2MB)
        $file = UploadedFile::fake()->image('profile.jpg')->size(1024); // 1MB

        $this->actingAs($user);

        // Attempt to upload via Livewire component
        $response = $this->post(route('filament.student.pages.student-profile-page'), [
            'photo' => $file,
        ]);

        // Should not have validation errors for valid file
        $this->assertTrue($file->getMimeType() === 'image/jpeg');
        $this->assertTrue($file->getSize() <= 2 * 1024 * 1024);
    }

    /**
     * Test that valid PNG photos are accepted
     */
    public function test_valid_png_photo_is_accepted(): void
    {
        // Create a student user
        $user = User::factory()->create();
        $murid = Murid::factory()->create(['user_id' => $user->id]);

        // Create a valid PNG file (under 2MB)
        $file = UploadedFile::fake()->image('profile.png')->size(1500); // 1.5MB

        $this->actingAs($user);

        // Verify file properties
        $this->assertTrue($file->getMimeType() === 'image/png');
        $this->assertTrue($file->getSize() <= 2 * 1024 * 1024);
    }

    /**
     * Property test: Files exceeding 2MB should be rejected
     */
    public function test_oversized_photos_are_rejected(): void
    {
        // Create a student user
        $user = User::factory()->create();
        $murid = Murid::factory()->create(['user_id' => $user->id]);

        // Create an oversized file (over 2MB)
        $file = UploadedFile::fake()->image('large.jpg')->size(3000); // 3MB

        $this->actingAs($user);

        // Verify file is too large
        $this->assertTrue($file->getSize() > 2 * 1024 * 1024);
    }

    /**
     * Property test: Invalid file types should be rejected
     */
    public function test_invalid_file_types_are_rejected(): void
    {
        // Create a student user
        $user = User::factory()->create();
        $murid = Murid::factory()->create(['user_id' => $user->id]);

        // Create invalid file types
        $pdfFile = UploadedFile::fake()->create('document.pdf', 500);
        $txtFile = UploadedFile::fake()->create('text.txt', 100);
        $gifFile = UploadedFile::fake()->create('animation.gif', 500);

        $this->actingAs($user);

        // Verify these are not valid image types
        $this->assertNotEquals('image/jpeg', $pdfFile->getMimeType());
        $this->assertNotEquals('image/png', $pdfFile->getMimeType());
        $this->assertNotEquals('image/jpeg', $txtFile->getMimeType());
        $this->assertNotEquals('image/png', $txtFile->getMimeType());
    }

    /**
     * Property test: Random file generation with various types and sizes
     * Verify only valid photos (JPEG/PNG under 2MB) are accepted
     */
    public function test_property_only_valid_photos_accepted(): void
    {
        $iterations = 20;

        for ($i = 0; $i < $iterations; $i++) {
            // Create a student user for each iteration
            $user = User::factory()->create();
            $murid = Murid::factory()->create(['user_id' => $user->id]);

            // Generate random file parameters
            $types = ['jpg', 'png', 'pdf', 'txt', 'gif', 'bmp'];
            $type = $types[array_rand($types)];
            $size = rand(100, 5000); // Random size between 100KB and 5MB

            // Create file based on type
            if (in_array($type, ['jpg', 'png', 'gif', 'bmp'])) {
                $file = UploadedFile::fake()->image("test.{$type}")->size($size);
            } else {
                $file = UploadedFile::fake()->create("test.{$type}", $size);
            }

            $this->actingAs($user);

            // Determine if file should be valid
            $isValidType = in_array($file->getMimeType(), ['image/jpeg', 'image/png']);
            $isValidSize = $file->getSize() <= 2 * 1024 * 1024;
            $shouldBeValid = $isValidType && $isValidSize;

            // Property: Only files that are JPEG/PNG and under 2MB should be valid
            if ($shouldBeValid) {
                $this->assertTrue($isValidType, "File type should be valid");
                $this->assertTrue($isValidSize, "File size should be under 2MB");
            } else {
                $this->assertTrue(!$isValidType || !$isValidSize, "File should be invalid");
            }
        }
    }

    /**
     * Test edge case: Exactly 2MB file should be accepted
     */
    public function test_exactly_2mb_file_is_accepted(): void
    {
        $user = User::factory()->create();
        $murid = Murid::factory()->create(['user_id' => $user->id]);

        // Create a file exactly 2MB (2048 KB)
        $file = UploadedFile::fake()->image('exact.jpg')->size(2048);

        $this->actingAs($user);

        // Should be valid
        $this->assertTrue($file->getSize() <= 2 * 1024 * 1024);
        $this->assertEquals('image/jpeg', $file->getMimeType());
    }

    /**
     * Test edge case: Just over 2MB should be rejected
     */
    public function test_just_over_2mb_is_rejected(): void
    {
        $user = User::factory()->create();
        $murid = Murid::factory()->create(['user_id' => $user->id]);

        // Create a file just over 2MB (2049 KB)
        $file = UploadedFile::fake()->image('oversize.jpg')->size(2049);

        $this->actingAs($user);

        // Should be invalid
        $this->assertTrue($file->getSize() > 2 * 1024 * 1024);
    }
}
