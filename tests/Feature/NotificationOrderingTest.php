<?php

namespace Tests\Feature;

use App\Models\Murid;
use App\Models\StudentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature: student-attendance-portal, Property: Notification Ordering
 * Validates: Requirements 6.5
 * 
 * Property-based test for notification ordering.
 * For any set of notifications with various timestamps, they should be
 * displayed in reverse chronological order (newest first).
 */
class NotificationOrderingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable broadcasting for tests
        \Illuminate\Support\Facades\Event::fake([
            \App\Events\QrCodeScanned::class,
            \App\Events\AbsensiCreated::class,
            \App\Events\AbsensiUpdated::class,
        ]);
    }

    /**
     * Property Test: Notifications are ordered by created_at descending
     * 
     * For any set of notifications with random timestamps,
     * they should be retrieved in reverse chronological order.
     */
    public function test_notifications_are_ordered_by_created_at_descending(): void
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

            // Generate random number of notifications (3-10)
            $notificationCount = $faker->numberBetween(3, 10);
            $timestamps = [];

            for ($j = 0; $j < $notificationCount; $j++) {
                // Generate random timestamp within the last 30 days
                $timestamp = $faker->dateTimeBetween('-30 days', 'now');
                $timestamps[] = $timestamp;

                $notification = new StudentNotification([
                    'murid_id' => $murid->id,
                    'type' => $faker->randomElement(['late_arrival', 'verification_update', 'schedule_change']),
                    'title' => $faker->sentence(3),
                    'message' => $faker->sentence(10),
                    'data' => [
                        'test_data' => $faker->word,
                    ],
                ]);
                
                $notification->created_at = $timestamp;
                $notification->updated_at = $timestamp;
                $notification->save();
            }

            // Sort timestamps in descending order (newest first)
            usort($timestamps, function ($a, $b) {
                return $b <=> $a;
            });

            // Retrieve notifications
            $notifications = StudentNotification::where('murid_id', $murid->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Assert correct number of notifications
            $this->assertCount($notificationCount, $notifications);

            // Assert notifications are in reverse chronological order
            for ($k = 0; $k < $notificationCount; $k++) {
                $expectedTimestamp = \Carbon\Carbon::instance($timestamps[$k]);
                $actualTimestamp = $notifications[$k]->created_at;

                $this->assertEquals(
                    $expectedTimestamp->timestamp,
                    $actualTimestamp->timestamp,
                    "Notification at index {$k} should have timestamp {$expectedTimestamp} but has {$actualTimestamp}"
                );
            }

            // Assert each notification is newer than or equal to the next one
            for ($k = 0; $k < $notificationCount - 1; $k++) {
                $this->assertGreaterThanOrEqual(
                    $notifications[$k + 1]->created_at->timestamp,
                    $notifications[$k]->created_at->timestamp,
                    "Notification at index {$k} should be newer than notification at index " . ($k + 1)
                );
            }
        }
    }

    /**
     * Property Test: Unread notifications maintain chronological order
     * 
     * For any set of unread notifications, they should be ordered
     * by created_at descending regardless of read status.
     */
    public function test_unread_notifications_maintain_chronological_order(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 50;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $faker->randomElement(['X-1', 'X-2']),
                'is_active' => true,
            ]);

            // Generate random number of notifications (5-10)
            $notificationCount = $faker->numberBetween(5, 10);
            $unreadTimestamps = [];

            for ($j = 0; $j < $notificationCount; $j++) {
                $timestamp = $faker->dateTimeBetween('-30 days', 'now');
                
                // Randomly mark some as read
                $readAt = $faker->boolean(50) ? null : $faker->dateTimeBetween($timestamp, 'now');

                $notification = new StudentNotification([
                    'murid_id' => $murid->id,
                    'type' => 'late_arrival',
                    'title' => $faker->sentence(3),
                    'message' => $faker->sentence(10),
                    'data' => [],
                    'read_at' => $readAt,
                ]);
                
                $notification->created_at = $timestamp;
                $notification->updated_at = $timestamp;
                $notification->save();

                if ($readAt === null) {
                    $unreadTimestamps[] = $timestamp;
                }
            }

            // Sort unread timestamps in descending order
            usort($unreadTimestamps, function ($a, $b) {
                return $b <=> $a;
            });

            // Retrieve unread notifications
            $unreadNotifications = StudentNotification::where('murid_id', $murid->id)
                ->unread()
                ->orderBy('created_at', 'desc')
                ->get();

            // Assert correct number of unread notifications
            $this->assertCount(count($unreadTimestamps), $unreadNotifications);

            // Assert unread notifications are in reverse chronological order
            for ($k = 0; $k < count($unreadTimestamps) - 1; $k++) {
                $this->assertGreaterThanOrEqual(
                    $unreadNotifications[$k + 1]->created_at->timestamp,
                    $unreadNotifications[$k]->created_at->timestamp,
                    "Unread notification at index {$k} should be newer than notification at index " . ($k + 1)
                );
            }
        }
    }

    /**
     * Property Test: Mixed read/unread notifications maintain order
     * 
     * For any set of notifications with mixed read statuses,
     * when retrieved together they should maintain chronological order.
     */
    public function test_mixed_read_unread_notifications_maintain_order(): void
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

            // Generate random number of notifications (5-15)
            $notificationCount = $faker->numberBetween(5, 15);

            for ($j = 0; $j < $notificationCount; $j++) {
                $timestamp = $faker->dateTimeBetween('-30 days', 'now');
                
                // Randomly mark some as read (60% chance of being read)
                $readAt = $faker->boolean(60) ? $faker->dateTimeBetween($timestamp, 'now') : null;

                $notification = new StudentNotification([
                    'murid_id' => $murid->id,
                    'type' => $faker->randomElement(['late_arrival', 'verification_update']),
                    'title' => $faker->sentence(3),
                    'message' => $faker->sentence(10),
                    'data' => [],
                    'read_at' => $readAt,
                ]);
                
                $notification->created_at = $timestamp;
                $notification->updated_at = $timestamp;
                $notification->save();
            }

            // Retrieve all notifications
            $notifications = StudentNotification::where('murid_id', $murid->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Assert correct number of notifications
            $this->assertCount($notificationCount, $notifications);

            // Assert all notifications are in reverse chronological order
            // regardless of read status
            for ($k = 0; $k < $notificationCount - 1; $k++) {
                $this->assertGreaterThanOrEqual(
                    $notifications[$k + 1]->created_at->timestamp,
                    $notifications[$k]->created_at->timestamp,
                    "Notification at index {$k} (read: " . ($notifications[$k]->read_at ? 'yes' : 'no') . 
                    ") should be newer than notification at index " . ($k + 1) . 
                    " (read: " . ($notifications[$k + 1]->read_at ? 'yes' : 'no') . ")"
                );
            }
        }
    }

    /**
     * Property Test: Notifications with same timestamp maintain stable order
     * 
     * For any notifications created at the same timestamp,
     * they should maintain a consistent order (by ID).
     */
    public function test_notifications_with_same_timestamp_maintain_stable_order(): void
    {
        $faker = \Faker\Factory::create();
        $iterations = 30;

        for ($i = 0; $i < $iterations; $i++) {
            // Generate random student
            $murid = Murid::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'kelas' => $faker->randomElement(['X-1', 'X-2']),
                'is_active' => true,
            ]);

            // Generate random number of notifications with same timestamp (3-7)
            $notificationCount = $faker->numberBetween(3, 7);
            $timestamp = $faker->dateTimeBetween('-30 days', 'now');
            $ids = [];

            for ($j = 0; $j < $notificationCount; $j++) {
                $notification = new StudentNotification([
                    'murid_id' => $murid->id,
                    'type' => 'late_arrival',
                    'title' => $faker->sentence(3),
                    'message' => $faker->sentence(10),
                    'data' => [],
                ]);
                
                $notification->created_at = $timestamp;
                $notification->updated_at = $timestamp;
                $notification->save();
                
                $ids[] = $notification->id;
            }

            // Retrieve notifications
            $notifications = StudentNotification::where('murid_id', $murid->id)
                ->orderBy('created_at', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            // Assert correct number of notifications
            $this->assertCount($notificationCount, $notifications);

            // Assert all notifications have the same timestamp
            foreach ($notifications as $notification) {
                $this->assertEquals(
                    \Carbon\Carbon::instance($timestamp)->timestamp,
                    $notification->created_at->timestamp
                );
            }

            // Assert notifications are ordered by ID descending (newest ID first)
            $retrievedIds = $notifications->pluck('id')->toArray();
            rsort($ids); // Sort IDs in descending order
            $this->assertEquals($ids, $retrievedIds);
        }
    }
}
