<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Allowed file types for attendance proofs
     */
    private const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'application/pdf'];

    /**
     * Maximum file size in bytes (5MB)
     */
    private const MAX_SIZE = 5 * 1024 * 1024;

    /**
     * Validate uploaded file
     *
     * @param UploadedFile $file
     * @return array
     */
    public function validateFile(UploadedFile $file): array
    {
        $errors = [];

        // Check file type
        if (!in_array($file->getMimeType(), self::ALLOWED_TYPES)) {
            $errors[] = 'Tipe file tidak valid. Hanya JPEG, PNG, dan PDF yang diperbolehkan.';
        }

        // Check file size
        if ($file->getSize() > self::MAX_SIZE) {
            $errors[] = 'Ukuran file terlalu besar. Maksimal 5MB.';
        }

        // Check if file is valid
        if (!$file->isValid()) {
            $errors[] = 'File tidak valid atau rusak.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Store attendance proof file
     *
     * @param UploadedFile $file
     * @param int $studentId
     * @param string $date
     * @return string|null File path or null on failure
     */
    public function storeAttendanceProof(UploadedFile $file, int $studentId, string $date): ?string
    {
        // Validate file first
        $validation = $this->validateFile($file);
        if (!$validation['valid']) {
            return null;
        }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;

        // Create directory path
        $directory = "attendance-proofs/{$studentId}/{$date}";

        // Store file
        $path = $file->storeAs($directory, $filename, 'private');

        return $path;
    }

    /**
     * Store profile photo
     *
     * @param UploadedFile $file
     * @param int $studentId
     * @return string|null File path or null on failure
     */
    public function storeProfilePhoto(UploadedFile $file, int $studentId): ?string
    {
        // Validate file (only images for profile)
        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png'])) {
            return null;
        }

        if ($file->getSize() > 2 * 1024 * 1024) { // 2MB for profile photos
            return null;
        }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;

        // Create directory path
        $directory = "profile-photos/{$studentId}";

        // Store file
        $path = $file->storeAs($directory, $filename, 'public');

        return $path;
    }

    /**
     * Delete file
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function deleteFile(string $path, string $disk = 'private'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }

    /**
     * Get file URL
     *
     * @param string $path
     * @param string $disk
     * @return string|null
     */
    public function getFileUrl(string $path, string $disk = 'private'): ?string
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->url($path);
        }

        return null;
    }

    /**
     * Get secure URL for attendance proof file
     *
     * @param string $path
     * @return string
     */
    public function getSecureAttendanceProofUrl(string $path): string
    {
        $encodedPath = base64_encode($path);
        return route('files.attendance-proof', ['path' => $encodedPath]);
    }

    /**
     * Get secure URL for profile photo
     *
     * @param string $path
     * @return string
     */
    public function getSecureProfilePhotoUrl(string $path): string
    {
        $encodedPath = base64_encode($path);
        return route('files.profile-photo', ['path' => $encodedPath]);
    }

    /**
     * Check if file exists
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function fileExists(string $path, string $disk = 'private'): bool
    {
        return Storage::disk($disk)->exists($path);
    }
}
