<?php

namespace Tests\Feature;

use App\Models\Absensi;
use App\Models\Murid;
use App\Models\StudentNotification;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property 10: Late Notification Delivery
 * Validates: Requirements 6.1, 6.2
 * 
 * Property-based test for late notification delivery.
 * For any student marked as late, a notification should be created and delivered
 * to that student's account with the correct date, time, and tardiness duration.
 */
class LateNotificationDeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected NotificationService $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable broadcasting for tests
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\QrCodeScanned::class,
            \App\Events\AbsensiCreated::class,
            \App\Events\AbsensiUpdated::class,
        ]);
        
        $this->notificationService = new NotificationService();
    }

    /**
     * Property Test: Late arrival notification is created with correct data
     * 
     * For any late attendance record, a notification should be created
     * with the correct murid_id, date, time, and tardiness duration.
     */
    public function test_late_arrival_notification_is_created_with_correct_data(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1', 'XII-1']),
                'is_active' => true,
            ]);

            // Generate random late attendance record
            $lateDuration = $faker->numberBetween(1, 120); // 1 to 120 minutes late
            $checkInTime = $faker->time('H:i:s');
            $tanggal = $faker->dateTimeBetween('-30 days', 'now');

            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => $tanggal,
                'status' => 'Terlambat',
                'kelas' => $murid->kelas,
                'is_late' => true,
                'late_duration' => $lateDuration,
                'check_in_time' => $checkInTime,
            ]);

            // Create notification using the service
            $notification = $this->notificationService->createLateArrivalNotification($absensi);

            // Assert notification was created
            $this->assertNotNull($notification);
            $this->assertInstanceOf(StudentNotification::class, $notification);

            // Assert notification has correct murid_id
            $this->assertEquals($murid->id, $notification->murid_id);

            // Assert notification type is correct
            $this->assertEquals('late_arrival', $notification->type);

            // Assert notification has title and message
            $this->assertNotEmpty($notification->title);
            $this->assertNotEmpty($notification->message);

            // Assert notification data contains required fields
            $this->assertIsArray($notification->data);
            $this->assertArrayHasKey('absensi_id', $notification->data);
            $this->assertArrayHasKey('date', $notification->data);
            $this->assertArrayHasKey('check_in_time', $notification->data);
            $this->assertArrayHasKey('late_duration', $notification->data);

            // Assert notification data values are correct
            $this->assertEquals($absensi->id, $notification->data['absensi_id']);
            $this->assertEquals($absensi->tanggal->toDateString(), \Carbon\Carbon::parse($notification->data['date'])->toDateString());
            $this->assertEquals($lateDuration, $notification->data['late_duration']);

            // Assert notification message contains the late duration
            $this->assertStringContainsString((string)$lateDuration, $notification->message);

            // Assert notification is unread by default
            $this->assertTrue($notification->isUnread());
            $this->assertNull($notification->read_at);

            // Assert notification can be retrieved from database
            $savedNotification = StudentNotification::find($notification->id);
            $this->assertNotNull($savedNotification);
            $this->assertEquals($notification->id, $savedNotification->id);
        }
    }

    /**
     * Property Test: Notification is associated with correct student
     * 
     * For any late attendance record, the notification should be
     * retrievable through the student's notifications relationship.
     */
    public function test_notification_is_associated_with_correct_student(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $faker->randomElement(['X-1', 'X-2', 'XI-1']),
                'is_active' => true,
            ]);

            // Generate random late attendance record
            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => $faker->dateTimeBetween('-30 days', 'now'),
                'status' => 'Terlambat',
                'kelas' => $murid->kelas,
                'is_late' => true,
                'late_duration' => $faker->numberBetween(1, 60),
                'check_in_time' => $faker->time('H:i:s'),
            ]);

            // Create notification
            $notification = $this->notificationService->createLateArrivalNotification($absensi);

            // Assert notification can be retrieved through student relationship
            $studentNotifications = $murid->notifications;
            $this->assertCount(1, $studentNotifications);
            $this->assertEquals($notification->id, $studentNotifications->first()->id);

            // Assert notification's murid relationship points back to correct student
            $this->assertEquals($murid->id, $notification->murid->id);
            $this->assertEquals($murid->name, $notification->murid->name);
        }
    }

    /**
     * Property Test: Multiple late arrivals create multiple notifications
     * 
     * For any student with multiple late attendance records,
     * each should generate a separate notification.
     */
    public function test_multiple_late_arrivals_create_multiple_notifications(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 20;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $faker->randomElement(['X-1', 'X-2']),
                'is_active' => true,
            ]);

            // Generate random number of late attendance records (2-5)
            $lateCount = $faker->numberBetween(2, 5);
            $notifications = [];

            for ($j = 0; $j < $lateCount; $j++) {
                $absensi = Absensi::create([
                    'murid_id' => $murid->id,
                    'tanggal' => $faker->dateTimeBetween('-30 days', 'now'),
                    'status' => 'Terlambat',
                    'kelas' => $murid->kelas,
                    'is_late' => true,
                    'late_duration' => $faker->numberBetween(1, 60),
                    'check_in_time' => $faker->time('H:i:s'),
                ]);

                $notification = $this->notificationService->createLateArrivalNotification($absensi);
                $notifications[] = $notification;
            }

            // Assert correct number of notifications were created
            $this->assertCount($lateCount, $notifications);

            // Assert all notifications belong to the same student
            foreach ($notifications as $notification) {
                $this->assertEquals($murid->id, $notification->murid_id);
            }

            // Assert student has correct number of notifications
            $studentNotifications = $murid->notifications;
            $this->assertCount($lateCount, $studentNotifications);
        }
    }

    /**
     * Property Test: Notification data is correctly formatted
     * 
     * For any late attendance record, the notification data should
     * contain properly formatted date and time information.
     */
    public function test_notification_data_is_correctly_formatted(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $faker->randomElement(['X-1', 'XI-1']),
                'is_active' => true,
            ]);

            // Generate random late attendance record
            $lateDuration = $faker->numberBetween(1, 90);
            $checkInTime = sprintf('%02d:%02d:00', $faker->numberBetween(7, 8), $faker->numberBetween(0, 59));
            $tanggal = now()->subDays($faker->numberBetween(0, 30));

            $absensi = Absensi::create([
                'murid_id' => $murid->id,
                'tanggal' => $tanggal,
                'status' => 'Terlambat',
                'kelas' => $murid->kelas,
                'is_late' => true,
                'late_duration' => $lateDuration,
                'check_in_time' => $checkInTime,
            ]);

            // Create notification
            $notification = $this->notificationService->createLateArrivalNotification($absensi);

            // Assert date format in data
            $this->assertIsString($notification->data['date']);
            $this->assertEquals($absensi->tanggal->toDateString(), \Carbon\Carbon::parse($notification->data['date'])->toDateString());

            // Assert check_in_time format in data (should be H:i format)
            $this->assertIsString($notification->data['check_in_time']);
            $this->assertMatchesRegularExpression('/^\d{2}:\d{2}$/', $notification->data['check_in_time']);

            // Assert late_duration is an integer
            $this->assertIsInt($notification->data['late_duration']);
            $this->assertEquals($lateDuration, $notification->data['late_duration']);

            // Assert absensi_id is an integer
            $this->assertIsInt($notification->data['absensi_id']);
            $this->assertEquals($absensi->id, $notification->data['absensi_id']);
        }
    }
}
