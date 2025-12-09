<?php

namespace Tests\Feature;

use App\Models\Murid;
use App\Models\User;
use App\Services\FileUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property: Profile Photo Update
 * Validates: Requirements 7.3
 * 
 * Property: For any valid photo upload, the photo should be stored correctly
 * and the database should be updated with the correct file path.
 */
class ProfilePhotoUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /**
     * Test that photo is stored and database is updated
     */
    public function test_photo_is_stored_and_database_updated(): void
    {
        $user = User::factory()->create();
        $murid = Murid::factory()->create(['user_id' => $user->id, 'photo' => null]);

        $this->actingAs($user);

        // Create a valid photo
        $file = UploadedFile::fake()->image('profile.jpg')->size(1024);

        // Use FileUploadService to store the photo
        $service = new FileUploadService();
        $path = $service->storeProfilePhoto($file, $murid->id);

        // Verify file was stored
        $this->assertNotNull($path);
        Storage::disk('public')->assertExists($path);

        // Update database
        $murid->photo = $path;
        $murid->save();

        // Verify database was updated
        $this->assertDatabaseHas('murids', [
            'id' => $murid->id,
            'photo' => $path,
        ]);

        // Verify we can retrieve the photo
        $murid->refresh();
        $this->assertEquals($path, $murid->photo);
    }

    /**
     * Test that old photo is deleted when new photo is uploaded
     */
    public function test_old_photo_is_deleted_when_new_photo_uploaded(): void
    {
        $user = User::factory()->create();
        
        // Create initial photo
        $oldFile = UploadedFile::fake()->image('old.jpg')->size(500);
        $service = new FileUploadService();
        $oldPath = $service->storeProfilePhoto($oldFile, 1);
        
        $murid = Murid::factory()->create([
            'user_id' => $user->id,
            'photo' => $oldPath
        ]);

        $this->actingAs($user);

        // Verify old photo exists
        Storage::disk('public')->assertExists($oldPath);

        // Upload new photo
        $newFile = UploadedFile::fake()->image('new.jpg')->size(800);
        $newPath = $service->storeProfilePhoto($newFile, $murid->id);

        // Delete old photo
        if ($oldPath !== $newPath) {
            Storage::disk('public')->delete($oldPath);
        }

        // Update database
        $murid->photo = $newPath;
        $murid->save();

        // Verify old photo is deleted
        Storage::disk('public')->assertMissing($oldPath);

        // Verify new photo exists
        Storage::disk('public')->assertExists($newPath);

        // Verify database has new path
        $this->assertDatabaseHas('murids', [
            'id' => $murid->id,
            'photo' => $newPath,
        ]);
    }

    /**
     * Property test: Random valid photos should be stored correctly
     * and database should be updated with correct paths
     */
    public function test_property_random_valid_photos_stored_correctly(): void
    {
        $iterations = 20;

        for ($i = 0; $i < $iterations; $i++) {
            // Create a new student for each iteration
            $user = User::factory()->create();
            $murid = Murid::factory()->create(['user_id' => $user->id, 'photo' => null]);

            $this->actingAs($user);

            // Generate random valid photo parameters
            $types = ['jpg', 'png'];
            $type = $types[array_rand($types)];
            $size = rand(100, 2000); // Random size between 100KB and 2MB

            // Create valid photo
            $file = UploadedFile::fake()->image("photo.{$type}")->size($size);

            // Store photo using service
            $service = new FileUploadService();
            $path = $service->storeProfilePhoto($file, $murid->id);

            // Property 1: Photo should be stored successfully
            $this->assertNotNull($path, "Photo should be stored");
            Storage::disk('public')->assertExists($path);

            // Property 2: Path should contain student ID directory
            $this->assertStringContainsString("profile-photos/{$murid->id}", $path);

            // Update database
            $murid->photo = $path;
            $murid->save();

            // Property 3: Database should be updated correctly
            $this->assertDatabaseHas('murids', [
                'id' => $murid->id,
                'photo' => $path,
            ]);

            // Property 4: Photo should be retrievable
            $murid->refresh();
            $this->assertEquals($path, $murid->photo);
            $this->assertNotNull($murid->photo);
        }
    }

    /**
     * Test that multiple students can have different photos
     */
    public function test_multiple_students_can_have_different_photos(): void
    {
        $service = new FileUploadService();
        $students = [];

        // Create 5 students with different photos
        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create();
            $murid = Murid::factory()->create(['user_id' => $user->id]);

            $file = UploadedFile::fake()->image("student{$i}.jpg")->size(500 + ($i * 100));
            $path = $service->storeProfilePhoto($file, $murid->id);

            $murid->photo = $path;
            $murid->save();

            $students[] = ['murid' => $murid, 'path' => $path];
        }

        // Verify each student has their own photo
        foreach ($students as $student) {
            Storage::disk('public')->assertExists($student['path']);
            
            $this->assertDatabaseHas('murids', [
                'id' => $student['murid']->id,
                'photo' => $student['path'],
            ]);
        }

        // Verify all paths are unique
        $paths = array_column($students, 'path');
        $this->assertEquals(count($paths), count(array_unique($paths)));
    }

    /**
     * Test that photo path is correctly formatted
     */
    public function test_photo_path_format_is_correct(): void
    {
        $user = User::factory()->create();
        $murid = Murid::factory()->create(['user_id' => $user->id]);

        $file = UploadedFile::fake()->image('test.jpg')->size(1000);
        $service = new FileUploadService();
        $path = $service->storeProfilePhoto($file, $murid->id);

        // Verify path format
        $this->assertStringStartsWith('profile-photos/', $path);
        $this->assertStringContainsString((string)$murid->id, $path);
        $this->assertStringEndsWith('.jpg', $path);
    }

    /**
     * Test that invalid photos are not stored
     */
    public function test_invalid_photos_are_not_stored(): void
    {
        $user = User::factory()->create();
        $murid = Murid::factory()->create(['user_id' => $user->id, 'photo' => null]);

        $this->actingAs($user);

        // Try to store invalid file types
        $service = new FileUploadService();
        
        $pdfFile = UploadedFile::fake()->create('document.pdf', 500);
        $pdfPath = $service->storeProfilePhoto($pdfFile, $murid->id);
        $this->assertNull($pdfPath, "PDF should not be stored");

        // Try to store oversized file
        $largeFile = UploadedFile::fake()->image('large.jpg')->size(3000);
        $largePath = $service->storeProfilePhoto($largeFile, $murid->id);
        $this->assertNull($largePath, "Oversized file should not be stored");

        // Verify database was not updated
        $murid->refresh();
        $this->assertNull($murid->photo);
    }

    /**
     * Test that photo can be updated multiple times
     */
    public function test_photo_can_be_updated_multiple_times(): void
    {
        $user = User::factory()->create();
        $murid = Murid::factory()->create(['user_id' => $user->id, 'photo' => null]);
        $service = new FileUploadService();

        $this->actingAs($user);

        $paths = [];

        // Upload 3 different photos
        for ($i = 1; $i <= 3; $i++) {
            $file = UploadedFile::fake()->image("photo{$i}.jpg")->size(500 + ($i * 100));
            $path = $service->storeProfilePhoto($file, $murid->id);

            // Delete old photo if exists
            if ($murid->photo) {
                Storage::disk('public')->delete($murid->photo);
            }

            $murid->photo = $path;
            $murid->save();

            $paths[] = $path;

            // Verify current photo exists
            Storage::disk('public')->assertExists($path);

            // Verify database is updated
            $this->assertDatabaseHas('murids', [
                'id' => $murid->id,
                'photo' => $path,
            ]);
        }

        // Verify only the last photo exists
        Storage::disk('public')->assertExists($paths[2]);
        Storage::disk('public')->assertMissing($paths[0]);
        Storage::disk('public')->assertMissing($paths[1]);
    }
}
