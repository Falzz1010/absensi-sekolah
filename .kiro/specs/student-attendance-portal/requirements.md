# Requirements Document

## Introduction

This document specifies the requirements for a Student Attendance Portal that enables students (Murid) to manage their daily attendance activities. The system allows students to check in via QR code scanning, submit documentation for absences, view their attendance history, and manage their personal profile. This portal provides students with transparency and control over their attendance records while maintaining integration with the existing school attendance management system.

## Glossary

- **Student Portal**: The web interface accessible to students for managing their attendance
- **Attendance System**: The existing school-wide attendance management system
- **QR Scanner**: The component that reads QR codes for attendance check-in
- **Attendance Record**: A single entry documenting a student's presence, absence, or tardiness
- **Proof Document**: Digital evidence (photo/file) supporting an absence claim
- **Self Check-in**: Student-initiated attendance marking via QR code scan
- **Attendance Status**: The state of attendance (present, sick, permission, absent, late)
- **Personal Dashboard**: The main view showing student's attendance information

## Requirements

### Requirement 1

**User Story:** As a student, I want to perform self check-in by scanning a QR code, so that I can mark my attendance quickly and independently.

#### Acceptance Criteria

1. WHEN a student accesses the QR scanner interface THEN the Student Portal SHALL display a camera view for QR code scanning
2. WHEN a valid QR code is scanned THEN the Student Portal SHALL create an attendance record with status "present" and current timestamp
3. WHEN an invalid QR code is scanned THEN the Student Portal SHALL display an error message and prevent attendance creation
4. WHEN a student attempts to scan outside valid time windows THEN the Student Portal SHALL reject the scan and display the valid time range
5. WHEN a student has already checked in for the current session THEN the Student Portal SHALL prevent duplicate check-in and display existing record

### Requirement 2

**User Story:** As a student, I want to upload proof documents when I am sick or need permission, so that my absence can be properly documented and verified.

#### Acceptance Criteria

1. WHEN a student selects absence type "sick" or "permission" THEN the Student Portal SHALL display a file upload interface
2. WHEN a student uploads a photo document THEN the Student Portal SHALL validate file type (JPEG, PNG, PDF) and size (maximum 5MB)
3. WHEN a valid document is uploaded THEN the Student Portal SHALL store the file securely and associate it with the absence record
4. WHEN a student submits an absence with proof THEN the Student Portal SHALL create an attendance record with status "pending verification"
5. WHEN file upload fails validation THEN the Student Portal SHALL display specific error messages indicating the validation failure reason

### Requirement 3

**User Story:** As a student, I want to view my attendance for today, so that I can verify my current attendance status.

#### Acceptance Criteria

1. WHEN a student accesses the personal dashboard THEN the Student Portal SHALL display today's attendance status prominently
2. WHEN today's attendance exists THEN the Student Portal SHALL show status, timestamp, and any associated notes
3. WHEN today's attendance does not exist THEN the Student Portal SHALL display "not yet recorded" with option to check in
4. WHEN today's attendance is marked as late THEN the Student Portal SHALL display the tardiness duration
5. WHEN today's attendance has uploaded proof THEN the Student Portal SHALL display a link to view the proof document

### Requirement 4

**User Story:** As a student, I want to view my attendance history for the past 30 days, so that I can track my attendance patterns over time.

#### Acceptance Criteria

1. WHEN a student accesses attendance history THEN the Student Portal SHALL display records for the past 30 calendar days
2. WHEN displaying history THEN the Student Portal SHALL show date, status, time, and notes for each record
3. WHEN a student filters by status THEN the Student Portal SHALL display only records matching the selected status
4. WHEN a student views a specific record THEN the Student Portal SHALL display full details including any uploaded proof
5. WHEN no attendance records exist for a date THEN the Student Portal SHALL display that date as "no record"

### Requirement 5

**User Story:** As a student, I want to see a summary of my absences, so that I can understand my overall attendance performance.

#### Acceptance Criteria

1. WHEN a student views the attendance summary THEN the Student Portal SHALL display total counts for each status type (present, sick, permission, absent, late)
2. WHEN calculating summary statistics THEN the Student Portal SHALL include only the past 30 days
3. WHEN displaying absence counts THEN the Student Portal SHALL highlight counts that exceed school policy thresholds
4. WHEN a student has pending verifications THEN the Student Portal SHALL display the count of unverified absences
5. WHEN summary data is unavailable THEN the Student Portal SHALL display zero counts with appropriate messaging

### Requirement 6

**User Story:** As a student, I want to receive notifications when I am marked as late, so that I am aware of my attendance status immediately.

#### Acceptance Criteria

1. WHEN a student is marked as late THEN the Student Portal SHALL send a real-time notification to the student's account
2. WHEN a notification is sent THEN the Student Portal SHALL include the date, time, and tardiness duration
3. WHEN a student views notifications THEN the Student Portal SHALL display unread notifications prominently
4. WHEN a student dismisses a notification THEN the Student Portal SHALL mark it as read and remove prominence
5. WHEN multiple notifications exist THEN the Student Portal SHALL display them in reverse chronological order

### Requirement 7

**User Story:** As a student, I want to view and update my profile information, so that my personal details are accurate in the system.

#### Acceptance Criteria

1. WHEN a student accesses their profile THEN the Student Portal SHALL display name, photo, class, and homeroom teacher
2. WHEN a student uploads a profile photo THEN the Student Portal SHALL validate file type (JPEG, PNG) and size (maximum 2MB)
3. WHEN a valid photo is uploaded THEN the Student Portal SHALL update the profile photo and display confirmation
4. WHEN a student views their class information THEN the Student Portal SHALL display current class name and homeroom teacher name
5. WHEN a student views today's schedule THEN the Student Portal SHALL display subject names and time slots for the current day

### Requirement 8

**User Story:** As a student, I want the portal to work on mobile devices, so that I can access it from my smartphone.

#### Acceptance Criteria

1. WHEN a student accesses the portal on a mobile device THEN the Student Portal SHALL display a responsive layout optimized for small screens
2. WHEN using the QR scanner on mobile THEN the Student Portal SHALL access the device camera with appropriate permissions
3. WHEN uploading files on mobile THEN the Student Portal SHALL allow selection from camera or gallery
4. WHEN viewing attendance history on mobile THEN the Student Portal SHALL display data in a mobile-friendly format
5. WHEN navigating on mobile THEN the Student Portal SHALL provide touch-friendly interface elements with adequate spacing

### Requirement 9

**User Story:** As a student, I want my data to be secure, so that only I can access my attendance information.

#### Acceptance Criteria

1. WHEN a student logs in THEN the Student Portal SHALL authenticate using secure credentials
2. WHEN a student accesses attendance data THEN the Student Portal SHALL verify the student can only view their own records
3. WHEN a student uploads documents THEN the Student Portal SHALL store files with access restricted to authorized users only
4. WHEN a session expires THEN the Student Portal SHALL automatically log out the student and require re-authentication
5. WHEN a student logs out THEN the Student Portal SHALL clear session data and redirect to login page

### Requirement 10

**User Story:** As a student, I want to see my schedule for today, so that I know which classes I have and when.

#### Acceptance Criteria

1. WHEN a student views today's schedule THEN the Student Portal SHALL display all scheduled classes for the current day
2. WHEN displaying schedule THEN the Student Portal SHALL show subject name, teacher name, time slot, and room number
3. WHEN the current time falls within a class period THEN the Student Portal SHALL highlight that class as "in progress"
4. WHEN no classes are scheduled for today THEN the Student Portal SHALL display a message indicating no classes
5. WHEN today is a holiday THEN the Student Portal SHALL display the holiday name and indicate no classes
