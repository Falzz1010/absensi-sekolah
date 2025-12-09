# Design Document: Student Attendance Portal

## Overview

The Student Attendance Portal is a dedicated interface within the existing Laravel Filament-based school attendance system that provides students with self-service capabilities for managing their attendance. The portal will be implemented as a separate Filament panel with restricted access and student-specific features.

The system leverages the existing database schema (Murid, Absensi, QrCode, Jadwal models) and extends functionality to support student-facing features including QR code scanning, document uploads, attendance history viewing, and profile management.

**Technology Stack:**
- Laravel 11.x with Filament 3.x
- Existing database schema (SQLite/MySQL)
- Laravel Sanctum for API authentication
- Laravel Storage for file management
- Laravel Broadcasting for real-time notifications
- Responsive design for mobile devices

## Architecture

### High-Level Architecture

```
┌─────────────────────────────────────────────────────────┐
│                   Student Portal Panel                   │
│                    (Filament Panel)                      │
├─────────────────────────────────────────────────────────┤
│  Dashboard  │  QR Scan  │  History  │  Profile          │
└─────────────────────────────────────────────────────────┘
                          │
                          ├─── Authentication Layer
                          │    (Student Role Check)
                          │
┌─────────────────────────────────────────────────────────┐
│              Application Services Layer                  │
├─────────────────────────────────────────────────────────┤
│  AttendanceService  │  QrScanService  │  FileService    │
└─────────────────────────────────────────────────────────┘
                          │
┌─────────────────────────────────────────────────────────┐
│                   Data Layer                             │
├─────────────────────────────────────────────────────────┤
│  Murid  │  Absensi  │  QrCode  │  Jadwal  │  User      │
└─────────────────────────────────────────────────────────┘
```

### Panel Structure

The system will create a new Filament panel (`student`) separate from the existing admin panel:

- **Admin Panel** (`/admin`): Existing administrative interface
- **Student Panel** (`/student`): New student-facing interface

This separation ensures:
- Clear role-based access control
- Different navigation and features per role
- Independent styling and branding if needed
- Security isolation between admin and student functions

## Components and Interfaces

### 1. Student Panel Provider

**Purpose:** Configure the student-specific Filament panel

**Key Responsibilities:**
- Define panel path (`/student`)
- Configure authentication for student role
- Register student-specific pages and widgets
- Set up mobile-responsive theme
- Configure navigation structure

### 2. Student Dashboard Page

**Purpose:** Main landing page showing today's attendance and quick actions

**Components:**
- Today's attendance status widget
- Quick QR scan button
- Attendance summary statistics (30-day)
- Today's class schedule widget
- Recent notifications

### 3. QR Scanner Page

**Purpose:** Enable students to scan QR codes for self check-in

**Components:**
- Camera interface (using browser WebRTC API)
- QR code decoder (using JavaScript library like `html5-qrcode`)
- Scan result display
- Error handling and validation feedback

**API Integration:**
- Extends existing `/api/qr-scan` endpoint
- Adds student authentication via Sanctum token
- Validates scan time windows
- Prevents duplicate check-ins

### 4. Attendance History Page

**Purpose:** Display student's attendance records

**Components:**
- Filament Table with filters
- Date range selector (default: 30 days)
- Status filter (Hadir, Sakit, Izin, Alfa, Terlambat)
- Detail view modal for individual records
- Export functionality (optional)

### 5. Absence Submission Form

**Purpose:** Allow students to submit absence documentation

**Components:**
- Filament Form with file upload
- Absence type selector (Sakit/Izin)
- Date selector
- Reason text area
- File upload field (photos/PDFs)
- Submission confirmation

**File Handling:**
- Store in `storage/app/attendance-proofs/{student_id}/{date}/`
- Validate file types: JPEG, PNG, PDF
- Maximum file size: 5MB
- Generate unique filenames to prevent conflicts

### 6. Profile Page

**Purpose:** Display and update student profile information

**Components:**
- Profile photo upload
- Read-only student information (name, NIS, class)
- Homeroom teacher information
- Today's schedule display
- Change password form (optional)

### 7. Notification System

**Purpose:** Alert students of attendance events

**Implementation:**
- Use Laravel's database notifications
- Display in Filament notification panel
- Real-time updates via Laravel Echo (optional)
- Notification types:
  - Late arrival notification
  - Absence verification status
  - Schedule changes

## Data Models

### Extended Murid Model

```php
// Additional fields needed (via migration)
- photo: string (nullable) - profile photo path
- qr_code_id: foreignId (nullable) - link to personal QR code
- user_id: foreignId (nullable) - link to User account

// Additional relationships
- user(): belongsTo(User)
- qrCode(): belongsTo(QrCode)
```

### Extended Absensi Model

```php
// Additional fields needed (via migration)
- proof_document: string (nullable) - path to uploaded proof
- verification_status: enum (pending, approved, rejected) - for absences with proof
- verified_by: foreignId (nullable) - admin who verified
- verified_at: timestamp (nullable)
- check_in_time: time (nullable) - exact check-in time
- is_late: boolean (default false)
- late_duration: integer (nullable) - minutes late

// Additional methods
- isLate(): bool
- getLateDuration(): int
- hasProof(): bool
```

### New StudentNotification Model

```php
// Fields
- id: bigint
- murid_id: foreignId
- type: string (late_arrival, verification_update, schedule_change)
- title: string
- message: text
- data: json (nullable) - additional data
- read_at: timestamp (nullable)
- created_at: timestamp
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: QR Scan Creates Attendance Record
*For any* valid QR code and authenticated student, when the QR code is scanned within valid time windows, an attendance record with status "present" should be created with the current timestamp.
**Validates: Requirements 1.2**

### Property 2: Invalid QR Rejection
*For any* invalid or inactive QR code, when scanned by any student, the system should reject the scan and no attendance record should be created.
**Validates: Requirements 1.3**

### Property 3: Duplicate Check-in Prevention
*For any* student who has already checked in for the current session, attempting to scan again should be rejected and the existing record should remain unchanged.
**Validates: Requirements 1.5**

### Property 4: File Upload Validation
*For any* file upload attempt, if the file type is not in the allowed list (JPEG, PNG, PDF) or size exceeds 5MB, the upload should be rejected with a specific error message.
**Validates: Requirements 2.2**

### Property 5: Proof Document Association
*For any* valid document upload with an absence submission, the file should be stored securely and the file path should be correctly associated with the corresponding attendance record.
**Validates: Requirements 2.3**

### Property 6: Today's Attendance Display
*For any* student viewing their dashboard, if an attendance record exists for today, it should be displayed with status, timestamp, and notes; otherwise, "not yet recorded" should be shown.
**Validates: Requirements 3.1, 3.2, 3.3**

### Property 7: 30-Day History Retrieval
*For any* student accessing attendance history, the system should return all attendance records within the past 30 calendar days, ordered by date descending.
**Validates: Requirements 4.1**

### Property 8: Status Filter Accuracy
*For any* status filter selection in attendance history, all returned records should match the selected status exactly.
**Validates: Requirements 4.3**

### Property 9: Summary Statistics Accuracy
*For any* student viewing attendance summary, the counts for each status type should equal the actual number of records with that status in the past 30 days.
**Validates: Requirements 5.1, 5.2**

### Property 10: Late Notification Delivery
*For any* student marked as late, a notification should be created and delivered to that student's account with the correct date, time, and tardiness duration.
**Validates: Requirements 6.1, 6.2**

### Property 11: Data Access Authorization
*For any* student accessing attendance data, the system should return only records where the murid_id matches the authenticated student's ID.
**Validates: Requirements 9.2**

### Property 12: File Access Restriction
*For any* uploaded proof document, access should be restricted such that only the student who uploaded it and authorized administrators can retrieve the file.
**Validates: Requirements 9.3**

### Property 13: Schedule Display for Current Day
*For any* student viewing today's schedule, the system should return all Jadwal records matching the student's class and the current day of week.
**Validates: Requirements 10.1, 10.2**

### Property 14: Current Class Highlighting
*For any* schedule display where the current time falls within a class period's time range, that class should be marked as "in progress".
**Validates: Requirements 10.3**

## Error Handling

### QR Scanning Errors
- **Invalid QR Code**: Display "QR Code tidak valid" with retry option
- **Inactive QR Code**: Display "QR Code sudah tidak aktif"
- **Outside Time Window**: Display "Scan hanya dapat dilakukan pada jam [time range]"
- **Duplicate Scan**: Display "Anda sudah melakukan check-in hari ini"
- **Camera Permission Denied**: Display instructions to enable camera access
- **Network Error**: Display "Koneksi gagal, silakan coba lagi" with retry button

### File Upload Errors
- **Invalid File Type**: Display "Hanya file JPEG, PNG, atau PDF yang diperbolehkan"
- **File Too Large**: Display "Ukuran file maksimal 5MB"
- **Upload Failed**: Display "Upload gagal, silakan coba lagi" with retry option
- **Storage Full**: Display "Penyimpanan penuh, hubungi administrator"

### Authentication Errors
- **Session Expired**: Automatically redirect to login with message
- **Unauthorized Access**: Display "Anda tidak memiliki akses" and redirect
- **Invalid Credentials**: Display "Email atau password salah"

### Data Retrieval Errors
- **No Records Found**: Display friendly "Belum ada data" message
- **Database Error**: Display "Terjadi kesalahan, silakan coba lagi"
- **Network Timeout**: Display "Koneksi timeout" with retry option

## Testing Strategy

### Unit Testing

Unit tests will verify specific examples and edge cases:

- **QR Code Validation**: Test valid/invalid code formats
- **File Upload Validation**: Test various file types and sizes
- **Date Range Calculations**: Test 30-day window edge cases
- **Time Window Validation**: Test boundary conditions for check-in times
- **Authorization Checks**: Test access control for different user roles
- **Notification Creation**: Test notification generation for various events

### Property-Based Testing

Property-based tests will verify universal properties using **Pest PHP** with the **pest-plugin-faker** for data generation. Each test will run a minimum of 100 iterations.

**Property Test Examples:**

1. **QR Scan Attendance Creation** (Property 1)
   - Generate random valid QR codes and student IDs
   - Verify attendance record is created with correct data
   - Tag: `**Feature: student-attendance-portal, Property 1: QR Scan Creates Attendance Record**`

2. **Duplicate Prevention** (Property 3)
   - Generate random student with existing attendance
   - Verify second scan is rejected
   - Tag: `**Feature: student-attendance-portal, Property 3: Duplicate Check-in Prevention**`

3. **File Validation** (Property 4)
   - Generate random file types and sizes
   - Verify only valid files are accepted
   - Tag: `**Feature: student-attendance-portal, Property 4: File Upload Validation**`

4. **Data Isolation** (Property 11)
   - Generate random student IDs
   - Verify each student sees only their own data
   - Tag: `**Feature: student-attendance-portal, Property 11: Data Access Authorization**`

### Integration Testing

- **End-to-End QR Scan Flow**: From camera access to attendance creation
- **File Upload Flow**: From file selection to storage and database update
- **Authentication Flow**: Login to dashboard access
- **Notification Flow**: Event trigger to notification display

### Mobile Testing

- **Responsive Layout**: Test on various screen sizes (320px to 768px width)
- **Touch Interactions**: Test button sizes and touch targets
- **Camera Access**: Test on iOS Safari and Android Chrome
- **File Upload**: Test camera/gallery selection on mobile devices
- **Performance**: Test load times on mobile networks

### Security Testing

- **Authorization**: Verify students cannot access other students' data
- **File Access**: Verify direct URL access to files is blocked
- **SQL Injection**: Test input sanitization
- **XSS Prevention**: Test output escaping
- **CSRF Protection**: Verify token validation

## Implementation Notes

### Database Migrations Required

1. Add fields to `murids` table: `photo`, `qr_code_id`, `user_id`
2. Add fields to `absensis` table: `proof_document`, `verification_status`, `verified_by`, `verified_at`, `check_in_time`, `is_late`, `late_duration`
3. Create `student_notifications` table

### Configuration

- Add `STUDENT_PANEL_PATH=/student` to `.env`
- Configure file storage disk for attendance proofs
- Set up Sanctum for API authentication
- Configure camera permissions in browser

### Third-Party Libraries

- `html5-qrcode`: For QR code scanning in browser
- `intervention/image`: For image processing and optimization
- `spatie/laravel-permission`: Already installed for role management

### Performance Considerations

- Index `absensis` table on `murid_id` and `tanggal`
- Cache today's schedule queries
- Lazy load attendance history with pagination
- Optimize image uploads with compression
- Use eager loading for relationships

### Mobile Optimization

- Use Filament's responsive utilities
- Implement touch-friendly button sizes (minimum 44x44px)
- Optimize images for mobile bandwidth
- Use progressive web app (PWA) features for offline capability
- Implement pull-to-refresh for data updates
