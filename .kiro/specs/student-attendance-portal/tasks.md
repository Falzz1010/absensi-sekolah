# Implementation Plan: Student Attendance Portal

- [x] 1. Database schema extensions

  - [x] 1.1 Create migration for murids table extensions


    - Add `photo` (string, nullable) field
    - Add `qr_code_id` (foreignId, nullable) field
    - Add `user_id` (foreignId, nullable) field with foreign key constraint
    - _Requirements: 7.1, 7.2, 9.2_
  
  - [x] 1.2 Create migration for absensis table extensions


    - Add `proof_document` (string, nullable) field
    - Add `verification_status` (enum: pending, approved, rejected, nullable) field
    - Add `verified_by` (foreignId, nullable) field
    - Add `verified_at` (timestamp, nullable) field
    - Add `check_in_time` (time, nullable) field
    - Add `is_late` (boolean, default false) field
    - Add `late_duration` (integer, nullable) field for minutes
    - Add indexes on `murid_id` and `tanggal` for performance
    - _Requirements: 2.3, 2.4, 3.4, 6.1_
  
  - [x] 1.3 Create student_notifications table migration



    - Create table with fields: id, murid_id, type, title, message, data (json), read_at, timestamps
    - Add foreign key constraint on murid_id
    - Add index on murid_id and read_at
    - _Requirements: 6.1, 6.2, 6.5_
  
  - [x] 1.4 Write property test for database schema integrity





    - **Property: Schema Constraints**
    - **Validates: Requirements 9.2**
    - Test that foreign key constraints are properly enforced
    - Test that nullable fields accept null values
    - Test that enum fields only accept valid values
-

- [x] 2. Create Student Panel infrastructure



  - [x] 2.1 Create StudentPanelProvider


    - Configure panel with path `/student`
    - Set up authentication middleware for student role
    - Configure mobile-responsive theme
    - Set up navigation structure
    - Enable database notifications
    - _Requirements: 8.1, 9.1_
  
  - [x] 2.2 Create student role and permissions


    - Add 'student' role to Spatie permissions
    - Create seeder to assign student role to Murid-linked users
    - Configure role-based panel access
    - _Requirements: 9.1, 9.2_
  
  - [x] 2.3 Link Murid records to User accounts


    - Create migration to populate user_id in murids table
    - Create command to generate User accounts for existing Murids
    - Set default password and require password change on first login
    - _Requirements: 9.1_
  
  - [x] 2.4 Write unit tests for panel configuration


    - Test panel path is correctly set
    - Test authentication middleware is applied
    - Test student role can access panel
    - Test non-student roles cannot access panel
    - _Requirements: 9.1, 9.2_

- [x] 3. Implement QR Scanner functionality





  - [x] 3.1 Create QrScanPage Filament page


    - Create page class with camera interface
    - Add JavaScript for html5-qrcode library integration
    - Implement scan result handling
    - Add error display components
    - _Requirements: 1.1_
  
  - [x] 3.2 Extend QrScanController for student authentication


    - Add Sanctum token authentication
    - Validate student is authenticated and active
    - Add time window validation logic
    - Implement duplicate check-in prevention
    - Calculate and set is_late and late_duration fields
    - _Requirements: 1.2, 1.4, 1.5, 3.4_
  
  - [x] 3.3 Write property test for QR scan attendance creation


    - **Property 1: QR Scan Creates Attendance Record**
    - **Validates: Requirements 1.2**
    - Generate random valid QR codes and student IDs
    - Verify attendance record is created with correct status and timestamp
  
  - [x] 3.4 Write property test for invalid QR rejection


    - **Property 2: Invalid QR Rejection**
    - **Validates: Requirements 1.3**
    - Generate random invalid QR codes
    - Verify scan is rejected and no record is created
  
  - [x] 3.5 Write property test for duplicate check-in prevention


    - **Property 3: Duplicate Check-in Prevention**
    - **Validates: Requirements 1.5**
    - Generate random students with existing attendance
    - Verify second scan is rejected and existing record unchanged
  
  - [x] 3.6 Add QR scan error handling


    - Implement error messages for invalid codes
    - Implement error messages for time window violations
    - Implement error messages for duplicate scans
    - Add camera permission error handling
    - _Requirements: 1.3, 1.4, 1.5_

- [x] 4. Implement absence submission with proof upload



  - [x] 4.1 Create AbsenceSubmissionPage Filament page


    - Create form with absence type selector (Sakit/Izin)
    - Add date selector field
    - Add reason textarea field
    - Add file upload field with validation
    - Implement form submission handler
    - _Requirements: 2.1, 2.2_
  
  - [x] 4.2 Implement file upload service


    - Create FileUploadService class
    - Implement file type validation (JPEG, PNG, PDF)
    - Implement file size validation (max 5MB)
    - Implement secure file storage in `storage/app/attendance-proofs/{student_id}/{date}/`
    - Generate unique filenames to prevent conflicts
    - _Requirements: 2.2, 2.3_
  
  - [x] 4.3 Write property test for file upload validation


    - **Property 4: File Upload Validation**
    - **Validates: Requirements 2.2**
    - Generate random files with various types and sizes
    - Verify only valid files are accepted
  
  - [x] 4.4 Write property test for proof document association


    - **Property 5: Proof Document Association**
    - **Validates: Requirements 2.3**
    - Generate random valid documents
    - Verify files are stored and correctly linked to attendance records
  
  - [x] 4.5 Create absence record with pending verification status

    - Implement Absensi creation with verification_status = 'pending'
    - Store proof_document path in database
    - Send notification to administrators for verification
    - _Requirements: 2.4_
  
  - [x] 4.6 Write property test for absence submission status


    - **Property: Absence Verification Status**
    - **Validates: Requirements 2.4**
    - Generate random absence submissions with proof
    - Verify status is set to 'pending verification'

- [x] 5. Checkpoint - Ensure all tests pass





  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Implement Student Dashboard


  - [x] 6.1 Create StudentDashboard page



    - Create dashboard page class
    - Add today's attendance status widget
    - Add quick action buttons (QR scan, submit absence)
    - Add attendance summary statistics widget
    - Add today's schedule widget
    - _Requirements: 3.1, 5.1, 10.1_
  
  - [x] 6.2 Create TodayAttendanceWidget





    - Display today's attendance status prominently
    - Show status, timestamp, and notes if record exists
    - Show "not yet recorded" with check-in button if no record
    - Display tardiness duration if late
    - Display proof document link if available
    - _Requirements: 3.2, 3.3, 3.4, 3.5_
  
  - [x] 6.2.1 Write property test for today's attendance display


    - **Property 6: Today's Attendance Display**
    - **Validates: Requirements 3.2, 3.3**
    - Generate random attendance records for today
    - Verify correct display based on record existence
  
  - [x] 6.3 Create AttendanceSummaryWidget



    - Calculate counts for each status type (present, sick, permission, absent, late)
    - Display counts for past 30 days only
    - Highlight counts exceeding school policy thresholds
    - Display count of pending verifications
    - Handle case when no data is available
    - _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_
  
  - [x] 6.3.1 Write property test for summary statistics accuracy


    - **Property 9: Summary Statistics Accuracy**
    - **Validates: Requirements 5.1, 5.2**
    - Generate random attendance records across various dates
    - Verify counts match actual records in past 30 days
  
  - [x] 6.5 Create TodayScheduleWidget








    - Query Jadwal records for student's class and current day
    - Display subject name, teacher name, time slot, room number
    - Highlight current class as "in progress" based on time
    - Display "no classes" message when schedule is empty
    - Display holiday name when today is a holiday
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_
  
  - [x] 6.5.1 Write property test for schedule display





    - **Property 13: Schedule Display for Current Day**
    - **Validates: Requirements 10.1, 10.2**
    - Generate random schedules for various classes and days
    - Verify correct schedule is displayed for student's class and current day
  
  - [x] 6.5.2 Write property test for current class highlighting



    - **Property 14: Current Class Highlighting**
    - **Validates: Requirements 10.3**
    - Generate random schedules and test with various current times
    - Verify correct class is highlighted as "in progress"

- [x] 7. Implement Attendance History page





  - [x] 7.1 Create AttendanceHistoryPage with Filament Table


    - Create page with table component
    - Configure table columns: date, status, time, notes
    - Set default date range to past 30 days
    - Add status filter (Hadir, Sakit, Izin, Alfa, Terlambat)
    - Add date range filter
    - Implement detail view modal for individual records
    - _Requirements: 4.1, 4.2, 4.3, 4.4_
  
  - [x] 7.2 Write property test for 30-day history retrieval


    - **Property 7: 30-Day History Retrieval**
    - **Validates: Requirements 4.1**
    - Generate random attendance records across various dates
    - Verify only records within past 30 days are returned
  
  - [x] 7.3 Write property test for status filter accuracy


    - **Property 8: Status Filter Accuracy**
    - **Validates: Requirements 4.3**
    - Generate random records with various statuses
    - Verify filtering returns only matching records
  
  - [x] 7.4 Implement data access authorization


    - Add query scope to filter by authenticated student's murid_id
    - Verify students can only see their own records
    - _Requirements: 9.2_
  
  - [x] 7.5 Write property test for data access authorization


    - **Property 11: Data Access Authorization**
    - **Validates: Requirements 9.2**
    - Generate random student IDs
    - Verify each student sees only their own attendance data

- [x] 8. Implement notification system













  - [x] 8.1 Create StudentNotification model and relationships


    - Create Eloquent model for student_notifications table
    - Add relationship to Murid model
    - Implement query scopes for unread notifications
    - _Requirements: 6.1, 6.2_
  
  - [x] 8.2 Create notification service


    - Create NotificationService class
    - Implement method to create late arrival notifications
    - Implement method to create verification status notifications
    - Include date, time, and tardiness duration in notification data
    - _Requirements: 6.1, 6.2_
  
  - [x] 8.3 Write property test for late notification delivery


    - **Property 10: Late Notification Delivery**
    - **Validates: Requirements 6.1, 6.2**
    - Generate random late attendance records
    - Verify notifications are created with correct data
  
  - [x] 8.4 Integrate notifications into dashboard


    - Add notification bell icon to panel header
    - Display unread notifications prominently
    - Implement notification dismissal (mark as read)
    - Display notifications in reverse chronological order
    - _Requirements: 6.3, 6.4, 6.5_
  
  - [x] 8.5 Write property test for notification ordering


    - **Property: Notification Ordering**
    - **Validates: Requirements 6.5**
    - Generate random notifications with various timestamps
    - Verify they are displayed in reverse chronological order
  
  - [x] 8.6 Trigger notifications from attendance events




    - Update QrScanController to create notification when is_late is true
    - Update absence verification to create notification on status change
    - _Requirements: 6.1_

- [x] 9. Checkpoint - Ensure all tests pass





  - Ensure all tests pass, ask the user if questions arise.

- [x] 10. Implement Profile page




  - [x] 10.1 Create StudentProfilePage


    - Create page with profile information display
    - Show name, photo, NIS, class, homeroom teacher (read-only)
    - Add profile photo upload form
    - Add today's schedule section
    - _Requirements: 7.1, 7.4, 7.5_
  
  - [x] 10.2 Implement profile photo upload


    - Create form with file upload field
    - Validate file type (JPEG, PNG) and size (max 2MB)
    - Store photo in `storage/app/profile-photos/{student_id}/`
    - Update murids.photo field with file path
    - Display confirmation message on success
    - _Requirements: 7.2, 7.3_
  
  - [x] 10.3 Write property test for profile photo validation


    - **Property: Profile Photo Validation**
    - **Validates: Requirements 7.2**
    - Generate random files with various types and sizes
    - Verify only valid photos are accepted
  
  - [x] 10.4 Write property test for profile photo update


    - **Property: Profile Photo Update**
    - **Validates: Requirements 7.3**
    - Generate random valid photos
    - Verify photo is stored and database is updated correctly

- [x] 11. Implement file access security





  - [x] 11.1 Create FileAccessController


    - Create controller to serve attendance proof files
    - Implement authorization check (student owns file or user is admin)
    - Return 403 for unauthorized access attempts
    - Stream file content with appropriate headers
    - _Requirements: 9.3_
  
  - [x] 11.2 Write property test for file access restriction


    - **Property 12: File Access Restriction**
    - **Validates: Requirements 9.3**
    - Generate random students and proof documents
    - Verify only authorized users can access files
  
  - [x] 11.3 Update file upload to use secure URLs


    - Generate signed URLs for file access
    - Update proof document links to use FileAccessController
    - Set URL expiration time (e.g., 1 hour)
    - _Requirements: 9.3_

- [x] 12. Mobile optimization and responsive design





  - [x] 12.1 Implement responsive layouts


    - Add Tailwind responsive classes to all pages
    - Test layouts on various screen sizes (320px to 768px)
    - Ensure touch-friendly button sizes (minimum 44x44px)
    - Optimize table display for mobile (card view)
    - _Requirements: 8.1, 8.4, 8.5_
  

  - [x] 12.2 Optimize QR scanner for mobile

    - Test camera access on mobile browsers
    - Add fallback for browsers without camera support
    - Optimize scanner UI for small screens
    - _Requirements: 8.2_
  

  - [x] 12.3 Optimize file upload for mobile

    - Configure file input to allow camera/gallery selection
    - Add image compression for mobile uploads
    - Test upload flow on iOS and Android
    - _Requirements: 8.3_
  
  - [x] 12.4 Write unit tests for responsive behavior


    - Test that layouts adapt to different viewport sizes
    - Test that touch targets meet minimum size requirements
    - Test that mobile-specific features are available
    - _Requirements: 8.1, 8.5_

- [ ] 13. Security and session management
  - [ ] 13.1 Configure session timeout
    - Set session lifetime in config
    - Implement automatic logout on session expiration
    - Redirect to login page with appropriate message
    - _Requirements: 9.4_
  
  - [ ] 13.2 Implement secure logout
    - Create logout action that clears session data
    - Invalidate Sanctum tokens on logout
    - Redirect to login page
    - _Requirements: 9.5_
  
  - [ ] 13.3 Write property test for logout session clearing
    - **Property: Logout Session Clearing**
    - **Validates: Requirements 9.5**
    - Generate random authenticated sessions
    - Verify session data is cleared after logout
  
  - [ ] 13.4 Write security tests
    - Test SQL injection prevention
    - Test XSS prevention in user inputs
    - Test CSRF token validation
    - Test authorization for all endpoints
    - _Requirements: 9.1, 9.2, 9.3_

- [ ] 14. Error handling and user feedback
  - [ ] 14.1 Implement comprehensive error messages
    - Add user-friendly error messages for all validation failures
    - Add error messages for network failures with retry options
    - Add error messages for permission denials
    - Implement toast notifications for success/error feedback
    - _Requirements: 1.3, 1.4, 1.5, 2.5_
  
  - [ ] 14.2 Add loading states
    - Add loading spinners for async operations
    - Add skeleton loaders for data fetching
    - Disable buttons during processing to prevent double-submission
    - _Requirements: All_
  
  - [ ] 14.3 Write unit tests for error handling
    - Test that appropriate error messages are displayed
    - Test that retry mechanisms work correctly
    - Test that loading states are properly managed
    - _Requirements: All_

- [ ] 15. Performance optimization
  - [ ] 15.1 Add database indexes
    - Verify indexes on absensis (murid_id, tanggal)
    - Add index on student_notifications (murid_id, read_at)
    - Add index on murids (user_id)
    - _Requirements: Performance_
  
  - [ ] 15.2 Implement query optimization
    - Use eager loading for relationships (murid, kelas, guru)
    - Add pagination to attendance history (25 records per page)
    - Cache today's schedule queries (5 minute TTL)
    - _Requirements: Performance_
  
  - [ ] 15.3 Optimize file uploads
    - Implement image compression for photos
    - Add client-side file size validation before upload
    - Use chunked uploads for large files
    - _Requirements: 2.2, 7.2_
  
  - [ ] 15.4 Write performance tests
    - Test query performance with large datasets
    - Test page load times
    - Test file upload performance
    - _Requirements: Performance_

- [ ] 16. Final integration and testing
  - [ ] 16.1 Create seed data for testing
    - Create seeder for student users with various attendance patterns
    - Create seeder for QR codes linked to students
    - Create seeder for schedules and notifications
    - _Requirements: All_
  
  - [ ] 16.2 Write end-to-end integration tests
    - Test complete QR scan flow from camera to attendance creation
    - Test complete absence submission flow with file upload
    - Test authentication and authorization flow
    - Test notification delivery flow
    - _Requirements: All_
  
  - [ ] 16.3 Manual testing checklist
    - Test on multiple browsers (Chrome, Firefox, Safari, Edge)
    - Test on multiple devices (desktop, tablet, mobile)
    - Test with various network conditions
    - Test accessibility with screen readers
    - _Requirements: All_

- [ ] 17. Final Checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

- [ ] 18. Documentation and deployment preparation
  - [ ] 18.1 Create user documentation
    - Write student user guide for portal features
    - Create FAQ document for common issues
    - Document QR scanning process with screenshots
    - _Requirements: All_
  
  - [ ] 18.2 Create technical documentation
    - Document API endpoints for mobile app integration
    - Document database schema changes
    - Document configuration requirements
    - Create deployment checklist
    - _Requirements: All_
  
  - [ ] 18.3 Prepare deployment
    - Create migration rollback plan
    - Test migrations on staging environment
    - Prepare production environment configuration
    - Create backup and restore procedures
    - _Requirements: All_
