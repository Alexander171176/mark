<?php

namespace Database\Seeders;

use App\Models\Admin\School\SchoolCoursePrice\SchoolCoursePrice;
use Illuminate\Database\Seeder;

class SchoolCoursePriceSeeder extends Seeder
{
    public function run(): void
    {
        $id = 1;

        for ($courseId = 1; $courseId <= 84; $courseId++) {
            $basePrice = 49 + ($courseId * 2);

            SchoolCoursePrice::updateOrCreate(
                ['id' => $id],
                [
                    'school_course_id' => $courseId,
                    'currency_id' => 1,

                    'price' => $basePrice,
                    'sale_price' => $courseId % 3 === 0
                        ? $basePrice - 10
                        : null,
                    'compare_at_price' => $courseId % 3 === 0
                        ? $basePrice + 20
                        : null,

                    'starts_at' => now()->subDays(30),
                    'ends_at' => null,

                    'activity' => true,
                    'sort' => $id,

                    'meta' => [
                        'source' => 'seeder',
                        'demo' => true,
                        'price_type' => 'course',
                    ],
                ]
            );

            $id++;
        }
    }
}
