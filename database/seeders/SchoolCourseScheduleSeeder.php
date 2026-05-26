<?php

namespace Database\Seeders;

use App\Models\Admin\School\CourseSchedule\SchoolCourseSchedule;
use Illuminate\Database\Seeder;

class SchoolCourseScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $schedules = [
            [
                'id' => 1,
                'school_course_id' => 1,
                'school_instructor_profile_id' => 2,
                'slug' => 'winter-flow-1',
                'starts_at' => now()->startOfYear()->addDays(10),
                'ends_at' => now()->startOfYear()->addDays(40),
                'enroll_starts_at' => now()->startOfYear()->subDays(20),
                'enroll_ends_at' => now()->startOfYear()->addDays(7),
                'capacity' => 25,
                'is_online' => true,
                'timezone' => 'Asia/Almaty',
                'views' => 850,
            ],
            [
                'id' => 2,
                'school_course_id' => 2,
                'school_instructor_profile_id' => 3,
                'slug' => 'winter-flow-2',
                'starts_at' => now()->startOfYear()->addDays(45),
                'ends_at' => now()->startOfYear()->addDays(75),
                'enroll_starts_at' => now()->startOfYear()->addDays(10),
                'enroll_ends_at' => now()->startOfYear()->addDays(42),
                'capacity' => 30,
                'is_online' => true,
                'timezone' => 'Asia/Almaty',
                'views' => 920,
            ],
            [
                'id' => 3,
                'school_course_id' => 3,
                'school_instructor_profile_id' => 4,
                'slug' => 'spring-flow-1',
                'starts_at' => now()->month(3)->day(10),
                'ends_at' => now()->month(4)->day(10),
                'enroll_starts_at' => now()->month(2)->day(10),
                'enroll_ends_at' => now()->month(3)->day(7),
                'capacity' => 35,
                'is_online' => true,
                'timezone' => 'Asia/Almaty',
                'views' => 1100,
            ],
            [
                'id' => 4,
                'school_course_id' => 4,
                'school_instructor_profile_id' => 5,
                'slug' => 'spring-flow-2',
                'starts_at' => now()->month(4)->day(15),
                'ends_at' => now()->month(5)->day(15),
                'enroll_starts_at' => now()->month(3)->day(15),
                'enroll_ends_at' => now()->month(4)->day(12),
                'capacity' => 30,
                'is_online' => true,
                'timezone' => 'Asia/Almaty',
                'views' => 980,
            ],
            [
                'id' => 5,
                'school_course_id' => 5,
                'school_instructor_profile_id' => 6,
                'slug' => 'summer-flow-1',
                'starts_at' => now()->month(6)->day(10),
                'ends_at' => now()->month(7)->day(10),
                'enroll_starts_at' => now()->month(5)->day(10),
                'enroll_ends_at' => now()->month(6)->day(7),
                'capacity' => 40,
                'is_online' => true,
                'timezone' => 'Asia/Almaty',
                'views' => 1250,
            ],
            [
                'id' => 6,
                'school_course_id' => 6,
                'school_instructor_profile_id' => 7,
                'slug' => 'summer-flow-2',
                'starts_at' => now()->month(7)->day(20),
                'ends_at' => now()->month(8)->day(20),
                'enroll_starts_at' => now()->month(6)->day(20),
                'enroll_ends_at' => now()->month(7)->day(17),
                'capacity' => 35,
                'is_online' => true,
                'timezone' => 'Asia/Almaty',
                'views' => 1180,
            ],
            [
                'id' => 7,
                'school_course_id' => 7,
                'school_instructor_profile_id' => 2,
                'slug' => 'autumn-flow-1',
                'starts_at' => now()->month(9)->day(10),
                'ends_at' => now()->month(10)->day(10),
                'enroll_starts_at' => now()->month(8)->day(10),
                'enroll_ends_at' => now()->month(9)->day(7),
                'capacity' => 45,
                'is_online' => true,
                'timezone' => 'Asia/Almaty',
                'views' => 1400,
            ],
            [
                'id' => 8,
                'school_course_id' => 8,
                'school_instructor_profile_id' => 3,
                'slug' => 'autumn-flow-2',
                'starts_at' => now()->month(10)->day(20),
                'ends_at' => now()->month(11)->day(20),
                'enroll_starts_at' => now()->month(9)->day(20),
                'enroll_ends_at' => now()->month(10)->day(17),
                'capacity' => 40,
                'is_online' => false,
                'location' => 'Astana IT Hub',
                'timezone' => 'Asia/Almaty',
                'views' => 1320,
            ],
        ];

        foreach ($schedules as $item) {
            SchoolCourseSchedule::updateOrCreate(
                ['id' => $item['id']],
                [
                    'school_course_id' => $item['school_course_id'],
                    'school_instructor_profile_id' => $item['school_instructor_profile_id'],
                    'sort' => $item['id'],
                    'activity' => true,
                    'slug' => $item['slug'],

                    'starts_at' => $item['starts_at'],
                    'ends_at' => $item['ends_at'],
                    'enroll_starts_at' => $item['enroll_starts_at'],
                    'enroll_ends_at' => $item['enroll_ends_at'],

                    'capacity' => $item['capacity'],
                    'is_online' => $item['is_online'],
                    'location' => $item['location'] ?? null,
                    'meeting_url' => $item['is_online']
                        ? 'https://meet.example.com/' . $item['slug']
                        : null,
                    'timezone' => $item['timezone'],

                    'status' => 'published',
                    'views' => $item['views'],
                    'notes' => 'Демо-поток онлайн-школы для тестирования расписания.',
                ]
            );
        }
    }
}
