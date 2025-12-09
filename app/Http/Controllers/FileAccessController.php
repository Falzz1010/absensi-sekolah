<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Murid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileAccessController extends Controller
{
    /**
     * Serve attendance proof file with authorization check
     *
     * @param Request $request
     * @param string $path
     * @return StreamedResponse|\Illuminate\Http\Response
     */
    public function serveAttendanceProof(Request $request, string $path)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(403, 'Anda harus login untuk mengakses file ini');
        }

        // Decode the path
        $filePath = base64_decode($path);

        // Check if file exists
        if (!Storage::disk('private')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Extract student ID from path (format: attendance-proofs/{student_id}/{date}/{filename})
        $pathParts = explode('/', $filePath);
        if (count($pathParts) < 3 || $pathParts[0] !== 'attendance-proofs') {
            abort(403, 'Akses tidak diizinkan');
        }

        $studentId = (int) $pathParts[1];

        // Get authenticated user
        $user = auth()->user();

        // Authorization check: student owns file OR user is admin
        $authorized = false;

        // Check if user is admin or guru
        if ($user->hasAnyRole(['admin', 'guru'])) {
            $authorized = true;
        }

        // Check if user is the student who owns the file
        if (!$authorized && $user->hasRole('murid')) {
            $murid = Murid::where('user_id', $user->id)->first();
            if ($murid && $murid->id === $studentId) {
                $authorized = true;
            }
        }

        // If not authorized, return 403
        if (!$authorized) {
            abort(403, 'Anda tidak memiliki akses ke file ini');
        }

        // Stream file content with appropriate headers
        $mimeType = Storage::disk('private')->mimeType($filePath);
        $fileSize = Storage::disk('private')->size($filePath);
        $fileName = basename($filePath);

        return Storage::disk('private')->response($filePath, $fileName, [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }

    /**
     * Serve profile photo with authorization check
     *
     * @param Request $request
     * @param string $path
     * @return StreamedResponse|\Illuminate\Http\Response
     */
    public function serveProfilePhoto(Request $request, string $path)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            abort(403, 'Anda harus login untuk mengakses file ini');
        }

        // Decode the path
        $filePath = base64_decode($path);

        // Check if file exists
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Extract student ID from path (format: profile-photos/{student_id}/{filename})
        $pathParts = explode('/', $filePath);
        if (count($pathParts) < 2 || $pathParts[0] !== 'profile-photos') {
            abort(403, 'Akses tidak diizinkan');
        }

        $studentId = (int) $pathParts[1];

        // Get authenticated user
        $user = auth()->user();

        // Authorization check: student owns file OR user is admin/guru
        $authorized = false;

        // Check if user is admin or guru
        if ($user->hasAnyRole(['admin', 'guru'])) {
            $authorized = true;
        }

        // Check if user is the student who owns the file
        if (!$authorized && $user->hasRole('murid')) {
            $murid = Murid::where('user_id', $user->id)->first();
            if ($murid && $murid->id === $studentId) {
                $authorized = true;
            }
        }

        // If not authorized, return 403
        if (!$authorized) {
            abort(403, 'Anda tidak memiliki akses ke file ini');
        }

        // Stream file content with appropriate headers
        $mimeType = Storage::disk('public')->mimeType($filePath);
        $fileSize = Storage::disk('public')->size($filePath);
        $fileName = basename($filePath);

        return Storage::disk('public')->response($filePath, $fileName, [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Content-Disposition' => 'inline; filename="' . $fileName . '"',
        ]);
    }
}
