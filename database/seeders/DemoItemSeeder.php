<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Demo\DemoGroup;
use App\Models\Admin\Constructor\HomePage\Demo\DemoItem;
use App\Models\Admin\Constructor\HomePage\Demo\DemoSection;
use Illuminate\Database\Seeder;
use Spatie\MediaLibrary\HasMedia;

class DemoItemSeeder extends Seeder
{
    public function run(): void
    {
        // Зависимости
        if (DemoSection::count() === 0) {
            $this->call(DemoSectionSeeder::class);
        }
        if (DemoGroup::count() === 0) {
            $this->call(DemoGroupSeeder::class);
        }

        // Карта: slug группы -> список элементов
        $itemsByGroup = [
            'landing' => [
                ['href'=>'http://localhost:3000/landing/landing-1',  'img'=>'landing-1',  'category'=>'Marketing',     'title'=>'Landing 1'],
                ['href'=>'http://localhost:3000/landing/landing-2',  'img'=>'landing-2',  'category'=>'Marketing',     'title'=>'Landing 2'],
                ['href'=>'http://localhost:3000/landing/landing-3',  'img'=>'landing-3',  'category'=>'Startup',       'title'=>'Landing 3'],
                ['href'=>'http://localhost:3000/landing/landing-4',  'img'=>'landing-4',  'category'=>'Startup',       'title'=>'Landing 4'],
                ['href'=>'http://localhost:3000/landing/landing-5',  'img'=>'landing-5',  'category'=>'Support',       'title'=>'Landing 5'],
                ['href'=>'http://localhost:3000/landing/landing-6',  'img'=>'landing-6',  'category'=>'Support',       'title'=>'Landing 6'],
                ['href'=>'http://localhost:3000/landing/landing-7',  'img'=>'landing-7',  'category'=>'Meetings',      'title'=>'Landing 7'],
                ['href'=>'http://localhost:3000/landing/landing-8',  'img'=>'landing-8',  'category'=>'Meetings',      'title'=>'Landing 8'],
                ['href'=>'http://localhost:3000/landing/landing-9',  'img'=>'landing-9',  'category'=>'Mentoring',     'title'=>'Landing 9'],
                ['href'=>'http://localhost:3000/landing/landing-10', 'img'=>'landing-10', 'category'=>'Mentoring',     'title'=>'Landing 10'],
                ['href'=>'http://localhost:3000/landing/landing-11', 'img'=>'landing-11', 'category'=>'Banking',       'title'=>'Landing 11'],
                ['href'=>'http://localhost:3000/landing/landing-12', 'img'=>'landing-12', 'category'=>'Banking',       'title'=>'Landing 12'],
                ['href'=>'http://localhost:3000/landing/landing-13', 'img'=>'landing-13', 'category'=>'Startup',       'title'=>'Landing 13'],
                ['href'=>'http://localhost:3000/landing/landing-14', 'img'=>'landing-14', 'category'=>'Creators',      'title'=>'Landing 14'],
                ['href'=>'http://localhost:3000/landing/landing-15', 'img'=>'landing-15', 'category'=>'Creators',      'title'=>'Landing 15'],
                ['href'=>'http://localhost:3000/landing/landing-16', 'img'=>'landing-16', 'category'=>'Commerce',      'title'=>'Landing 16'],
                ['href'=>'http://localhost:3000/landing/landing-17', 'img'=>'landing-17', 'category'=>'Commerce',      'title'=>'Landing 17'],
                ['href'=>'http://localhost:3000/landing/landing-18', 'img'=>'landing-18', 'category'=>'Commerce',      'title'=>'Landing 18'],
                ['href'=>'http://localhost:3000/landing/landing-19', 'img'=>'landing-19', 'category'=>'Development',   'title'=>'Landing 19'],
                ['href'=>'http://localhost:3000/landing/landing-20', 'img'=>'landing-20', 'category'=>'Development',   'title'=>'Landing 20'],
                ['href'=>'http://localhost:3000/landing/landing-21', 'img'=>'landing-21', 'category'=>'Conference',    'title'=>'Landing 21'],
                ['href'=>'http://localhost:3000/landing/landing-22', 'img'=>'landing-22', 'category'=>'Conference',    'title'=>'Landing 22'],
                ['href'=>'http://localhost:3000/landing/landing-23', 'img'=>'landing-23', 'category'=>'IT Business',   'title'=>'Landing 23'],
                ['href'=>'http://localhost:3000/landing/landing-24', 'img'=>'landing-24', 'category'=>'IT Business',   'title'=>'Landing 24'],
                ['href'=>'http://localhost:3000/landing/landing-25', 'img'=>'landing-25', 'category'=>'IT Business',   'title'=>'Landing 25'],
                ['href'=>'http://localhost:3000/landing/landing-26', 'img'=>'landing-26', 'category'=>'Insurance',     'title'=>'Landing 26'],
                ['href'=>'http://localhost:3000/landing/landing-27', 'img'=>'landing-27', 'category'=>'Insurance',     'title'=>'Landing 27'],
                ['href'=>'http://localhost:3000/landing/landing-28', 'img'=>'landing-28', 'category'=>'IT Jobs',       'title'=>'Landing 28'],
                ['href'=>'http://localhost:3000/landing/landing-29', 'img'=>'landing-29', 'category'=>'IT Jobs',       'title'=>'Landing 29'],
                ['href'=>'http://localhost:3000/landing/landing-30', 'img'=>'landing-30', 'category'=>'IT Jobs',       'title'=>'Landing 30'],
                ['href'=>'http://localhost:3000/landing/landing-31', 'img'=>'landing-31', 'category'=>'NFT Marketplace','title'=>'Landing 31'],
                ['href'=>'http://localhost:3000/landing/landing-32', 'img'=>'landing-32', 'category'=>'NFT Marketplace','title'=>'Landing 32'],
                ['href'=>'http://localhost:3000/landing/landing-33', 'img'=>'landing-33', 'category'=>'NFT Marketplace','title'=>'Landing 33'],
                ['href'=>'http://localhost:3000/landing/landing-34', 'img'=>'landing-34', 'category'=>'Mobile App',    'title'=>'Landing 34'],
                ['href'=>'http://localhost:3000/landing/landing-35', 'img'=>'landing-35', 'category'=>'Mobile App',    'title'=>'Landing 35'],
                ['href'=>'http://localhost:3000/landing/landing-36', 'img'=>'landing-36', 'category'=>'Car Rental',    'title'=>'Landing 36'],
                ['href'=>'http://localhost:3000/landing/landing-37', 'img'=>'landing-37', 'category'=>'Car Rental',    'title'=>'Landing 37'],
                ['href'=>'http://localhost:3000/landing/landing-38', 'img'=>'landing-38', 'category'=>'Car Rental',    'title'=>'Landing 38'],
            ],
            'about' => [
                ['href'=>'http://localhost:3000/about/about-1', 'img'=>'about-1', 'category'=>'Startup', 'title'=>'About 1'],
                ['href'=>'http://localhost:3000/about/about-2', 'img'=>'about-2', 'category'=>'Startup', 'title'=>'About 2'],
                ['href'=>'http://localhost:3000/about/about-3', 'img'=>'about-3', 'category'=>'Agency',  'title'=>'About 3'],
                ['href'=>'http://localhost:3000/about/about-4', 'img'=>'about-4', 'category'=>'Agency',  'title'=>'About 4'],
                ['href'=>'http://localhost:3000/about/about-5', 'img'=>'about-5', 'category'=>'Agency',  'title'=>'About 5'],
                ['href'=>'http://localhost:3000/about/about-6', 'img'=>'about-6', 'category'=>'Agency',  'title'=>'About 6'],
            ],
            'pricing' => [
                ['href'=>'http://localhost:3000/pricing/pricing-1','img'=>'pricing-1','category'=>'Pricing','title'=>'Scroll Pricing'],
                ['href'=>'http://localhost:3000/pricing/pricing-2','img'=>'pricing-2','category'=>'Pricing','title'=>'Solo Pricing'],
                ['href'=>'http://localhost:3000/pricing/pricing-3','img'=>'pricing-3','category'=>'Pricing','title'=>'Long Pricing'],
                ['href'=>'http://localhost:3000/pricing/pricing-4','img'=>'pricing-4','category'=>'Pricing','title'=>'Split Pricing'],
                ['href'=>'http://localhost:3000/pricing/pricing-5','img'=>'pricing-5','category'=>'Pricing','title'=>'Block Pricing'],
                ['href'=>'http://localhost:3000/pricing/pricing-6','img'=>'pricing-6','category'=>'Pricing','title'=>'Tabbed Pricing'],
                ['href'=>'http://localhost:3000/pricing/pricing-7','img'=>'pricing-7','category'=>'Pricing','title'=>'Compact Pricing'],
                ['href'=>'http://localhost:3000/pricing/pricing-8','img'=>'pricing-8','category'=>'Pricing','title'=>'Slide Pricing'],
                ['href'=>'http://localhost:3000/pricing/pricing-9','img'=>'pricing-9','category'=>'Pricing','title'=>'Solo Pricing'],
            ],
            'blog' => [
                ['href'=>'http://localhost:3000/blog/posts-1', 'img'=>'posts-1', 'category'=>'Blog', 'title'=>'Posts 1'],
                ['href'=>'http://localhost:3000/blog/posts-2', 'img'=>'posts-2', 'category'=>'Blog', 'title'=>'Posts 2'],
                ['href'=>'http://localhost:3000/blog/posts-3', 'img'=>'posts-3', 'category'=>'Blog', 'title'=>'Posts 3'],
                ['href'=>'http://localhost:3000/blog/posts-4', 'img'=>'posts-4', 'category'=>'Blog', 'title'=>'Posts 4'],
                ['href'=>'http://localhost:3000/blog/posts-5', 'img'=>'posts-5', 'category'=>'Blog', 'title'=>'Posts 5'],
                ['href'=>'http://localhost:3000/blog/posts-6', 'img'=>'posts-6', 'category'=>'Blog', 'title'=>'Posts 6'],
                ['href'=>'http://localhost:3000/blog/posts-7', 'img'=>'posts-7', 'category'=>'Blog', 'title'=>'Posts 7'],
                ['href'=>'http://localhost:3000/blog/posts-8', 'img'=>'posts-8', 'category'=>'Blog', 'title'=>'Posts 8'],
                ['href'=>'http://localhost:3000/blog/posts-9', 'img'=>'posts-9', 'category'=>'Blog', 'title'=>'Posts 9'],
                ['href'=>'http://localhost:3000/blog/posts-10','img'=>'posts-10','category'=>'Blog','title'=>'Posts 10'],
                ['href'=>'http://localhost:3000/blog/posts-11','img'=>'posts-1', 'category'=>'Blog','title'=>'Posts 11'],
                ['href'=>'http://localhost:3000/blog/posts-12','img'=>'posts-2', 'category'=>'Blog','title'=>'Posts 12'],
            ],
            'subpages' => [
                ['href'=>'http://localhost:3000/profile',                                        'img'=>'profile-overview',     'category'=>'Profile',     'title'=>'Overview'],
                ['href'=>'http://localhost:3000/profile/projects',                               'img'=>'profile-projects',     'category'=>'Profile',     'title'=>'Projects'],
                ['href'=>'http://localhost:3000/profile/network',                                'img'=>'profile-network',      'category'=>'Profile',     'title'=>'Network'],
                ['href'=>'http://localhost:3000/subpages/jobs/backend-developer',                'img'=>'job-details',          'category'=>'Jobs',        'title'=>'Job Details'],
                ['href'=>'http://localhost:3000/subpages/nfts/nft-list',                         'img'=>'nft-list',             'category'=>'Marketplace', 'title'=>'NFT List'],
                ['href'=>'http://localhost:3000/subpages/nfts/the-smoking-eagle',                'img'=>'nft-details',          'category'=>'Marketplace', 'title'=>'NFT Details'],
                ['href'=>'http://localhost:3000/subpages/help',                                  'img'=>'help-center-home',     'category'=>'Support',     'title'=>'Help Center'],
                ['href'=>'http://localhost:3000/subpages/help/category/project-module-category', 'img'=>'help-center-category', 'category'=>'Support',     'title'=>'Help Category'],
                ['href'=>'http://localhost:3000/subpages/help/category/article/managing-tasks',  'img'=>'help-center-article',  'category'=>'Support',     'title'=>'Help Article'],
                ['href'=>'http://localhost:3000/subpages/contact',                               'img'=>'contact-1',            'category'=>'Contact',     'title'=>'Contact 1'],
                ['href'=>'http://localhost:3000/subpages/contact/contact-2',                     'img'=>'contact-2',            'category'=>'Contact',     'title'=>'Contact 2'],
                ['href'=>'http://localhost:3000/subpages/contact/contact-3',                     'img'=>'contact-3',            'category'=>'Contact',     'title'=>'Contact 3'],
                ['href'=>'http://localhost:3000/subpages/terms',                                 'img'=>'terms-1',              'category'=>'ToS',         'title'=>'Terms of Service'],
                ['href'=>'http://localhost:3000/subpages/error',                                 'img'=>'404-2',                'category'=>'Error',       'title'=>'404 v2'],
                ['href'=>'http://localhost:3000/',                                               'img'=>'404-1',                'category'=>'Error',       'title'=>'404 v1'],
            ],
            'auth' => [
                ['href'=>'http://localhost:3000/auth/login-1',  'img'=>'login-1',  'category'=>'Auth', 'title'=>'Login 1'],
                ['href'=>'http://localhost:3000/auth/login-2',  'img'=>'login-2',  'category'=>'Auth', 'title'=>'Login 2'],
                ['href'=>'http://localhost:3000/auth/login-3',  'img'=>'login-3',  'category'=>'Auth', 'title'=>'Login 3'],
                ['href'=>'http://localhost:3000/auth/signup-1', 'img'=>'signup-1', 'category'=>'Auth', 'title'=>'Signup 1'],
                ['href'=>'http://localhost:3000/auth/signup-2', 'img'=>'signup-2', 'category'=>'Auth', 'title'=>'Signup 2'],
                ['href'=>'http://localhost:3000/auth/signup-3', 'img'=>'signup-3', 'category'=>'Auth', 'title'=>'Signup 3'],
            ],
            'layouts' => [
                ['href'=>'http://localhost:3000/master/minimal-demo', 'img'=>'layout-minimal', 'category'=>'Master Layouts', 'title'=>'Minimal'],
                ['href'=>'http://localhost:3000/master/default',      'img'=>'layout-default', 'category'=>'Master Layouts', 'title'=>'Default Nav'],
                ['href'=>'http://localhost:3000/master/layout-a-demo','img'=>'layout-a-demo',  'category'=>'Master Layouts', 'title'=>'Nav A'],
            ],
        ];

        // Для каждой секции (локали) найдём её группы и создадим элементы
        $groups = DemoGroup::with('section')->get();
        foreach ($groups as $group) {
            $list = $itemsByGroup[$group->slug] ?? [];
            foreach ($list as $i => $it) {
                $item = DemoItem::updateOrCreate(
                    ['group_id' => $group->id, 'href' => $it['href']],
                    [
                        'title'    => $it['title'],
                        'category' => $it['category'] ?? null,
                        'alt'      => 'Demo screenshot',
                        'sort'     => $i + 1,
                        'activity' => true,
                    ]
                );

                $lightPath = public_path("images/vulk/{$it['img']}.png");
                $darkPath  = public_path("images/vulk/{$it['img']}-dark.png");

                $this->attachSingle($item, DemoItem::MEDIA_COLLECTION_LIGHT, $lightPath);
                $this->attachSingle($item, DemoItem::MEDIA_COLLECTION_DARK,  $darkPath);
            }
        }
    }

    private function attachSingle(HasMedia $model, string $collection, ?string $path): void
    {
        if (!$path || !file_exists($path)) return;

        $model->clearMediaCollection($collection);
        $model->addMedia($path)
            ->preservingOriginal()
            ->toMediaCollection($collection);
    }
}
