<?php

namespace Database\Seeders;

use App\Models\Admin\Constructor\HomePage\Component\ComponentSection;
use App\Models\Admin\Constructor\HomePage\Component\ComponentTab;
use App\Models\Admin\Constructor\HomePage\Component\ComponentTile;
use Illuminate\Database\Seeder;

class ComponentTileSeeder extends Seeder
{
    public function run(): void
    {
        $locales = ['en', 'ru', 'kk'];

        $sections = ComponentSection::whereIn('locale', $locales)->get()->keyBy('locale');

        $data = [
            'base' => [
                ['href'=>'/blocks/avatars',    'light'=>'avatar.svg',          'dark'=>'avatar-dark.svg',          'title'=>'Avatar Component'],
                ['href'=>'/blocks/buttons',    'light'=>'button.svg',          'dark'=>'button-dark.svg',          'title'=>'Button Component'],
                ['href'=>'/blocks/cards',      'light'=>'card.svg',            'dark'=>'card-dark.svg',            'title'=>'Card Component'],
                ['href'=>'/blocks/counters',   'light'=>'counter.svg',         'dark'=>'counter-dark.svg',         'title'=>'Counter Component'],
                ['href'=>'/blocks/forms',      'light'=>'form.svg',            'dark'=>'form-dark.svg',            'title'=>'Form Components'],
                ['href'=>'/blocks/icons',      'light'=>'icon.svg',            'dark'=>'icon-dark.svg',            'title'=>'Icon Component'],
                ['href'=>'/blocks/images',     'light'=>'image.svg',           'dark'=>'image-dark.svg',           'title'=>'Image Component'],
                ['href'=>'/blocks/typography', 'light'=>'typography.svg',      'dark'=>'typography-dark.svg',      'title'=>'Typography'],
                ['href'=>'/blocks/tags',       'light'=>'tags.svg',            'dark'=>'tags-dark.svg',            'title'=>'Tag Component'],
            ],
            'intermediate' => [
                ['href'=>'/blocks/modals',     'light'=>'modal.svg',           'dark'=>'modal-dark.svg',           'title'=>'Modal Component'],
                ['href'=>'/blocks/placeholder','light'=>'placeholder.svg',     'dark'=>'placeholder-dark.svg',     'title'=>'Placeholder Component'],
                ['href'=>'/blocks/table',      'light'=>'table.svg',           'dark'=>'table-dark.svg',           'title'=>'Table Component'],
                ['href'=>'/blocks/navtabs',    'light'=>'tabs.svg',            'dark'=>'tabs-dark.svg',            'title'=>'Tab Component'],
                ['href'=>'/blocks/video',      'light'=>'video.svg',           'dark'=>'video-dark.svg',           'title'=>'Video Component'],
                ['href'=>'/blocks/sections',   'light'=>'section.svg',         'dark'=>'section-dark.svg',         'title'=>'Section Component'],
            ],
            'blocks' => [
                ['href'=>'/blocks/navbar',     'light'=>'block-navbar.svg',    'dark'=>'block-navbar-dark.svg',    'title'=>'Navbar Blocks'],
                ['href'=>'/blocks/footer',     'light'=>'block-footer.svg',    'dark'=>'block-footer-dark.svg',    'title'=>'Footer Blocks'],
                ['href'=>'/blocks/features',   'light'=>'block-features.svg',  'dark'=>'block-features-dark.svg',  'title'=>'Feature Blocks'],
                ['href'=>'/blocks/content',    'light'=>'block-content.svg',   'dark'=>'block-content-dark.svg',   'title'=>'Content Blocks'],
                ['href'=>'/blocks/call',       'light'=>'block-cta.svg',       'dark'=>'block-cta-dark.svg',       'title'=>'CTA Blocks'],
                ['href'=>'/blocks/gallery',    'light'=>'block-gallery.svg',   'dark'=>'block-gallery-dark.svg',   'title'=>'Gallery Blocks'],
                ['href'=>'/blocks/team',       'light'=>'block-team.svg',      'dark'=>'block-team-dark.svg',      'title'=>'Team Blocks'],
                ['href'=>'/blocks/testimonials','light'=>'block-testimonials.svg','dark'=>'block-testimonials-dark.svg','title'=>'Testimonial Blocks'],
            ],
            'advanced' => [
                ['href'=>'/blocks/advanced/features',    'light'=>'features.svg',        'dark'=>'features-dark.svg',        'title'=>'Advanced Features'],
                ['href'=>'/blocks/advanced/content',     'light'=>'block-content.svg',   'dark'=>'block-content-dark.svg',   'title'=>'Advanced Content'],
                ['href'=>'/blocks/advanced/company',     'light'=>'company.svg',         'dark'=>'company-dark.svg',         'title'=>'Advanced Company'],
                ['href'=>'/blocks/advanced/clients',     'light'=>'clients.svg',         'dark'=>'clients-dark.svg',         'title'=>'Advanced Clients'],
                ['href'=>'/blocks/advanced/testimonials','light'=>'testimonials.svg',    'dark'=>'testimonials-dark.svg',    'title'=>'Advanced Testimonials'],
                ['href'=>'/blocks/advanced/blog',        'light'=>'blog.svg',            'dark'=>'blog-dark.svg',            'title'=>'Advanced Blog'],
                ['href'=>'/blocks/advanced/contact',     'light'=>'contact.svg',         'dark'=>'contact-dark.svg',         'title'=>'Advanced Contact'],
                ['href'=>'/blocks/advanced/faq',         'light'=>'faq.svg',             'dark'=>'faq-dark.svg',             'title'=>'Advanced FAQ'],
            ],
            'utilities' => [
                ['href'=>'/blocks/colors',     'light'=>'colors.svg',      'dark'=>'colors-dark.svg',      'title'=>'Theme Colors'],
                ['href'=>'/blocks/utilities',  'light'=>'utilities.svg',   'dark'=>'utilities-dark.svg',   'title'=>'Theme Utilities'],
                ['href'=>'/blocks/plugins',    'light'=>'plugins.svg',     'dark'=>'plugins-dark.svg',     'title'=>'Theme Plugins'],
            ],
        ];

        // каталог, где лежат исходные svg
        $assetsDir = resource_path('images/vulk/');

        foreach ($locales as $loc) {
            $section = $sections[$loc] ?? null;
            if (!$section) continue;

            foreach ($data as $slug => $tiles) {
                $tab = ComponentTab::firstWhere([
                    'section_id' => $section->id,
                    'slug'       => $slug,
                ]);

                if (!$tab) continue;

                $sort = 1;
                foreach ($tiles as $tile) {
                    $model = ComponentTile::updateOrCreate(
                        ['tab_id' => $tab->id, 'title' => $tile['title']],
                        [
                            'href'     => $tile['href'],
                            'sort'     => $sort++,
                            'activity' => true,
                            'light_alt'=> 'component icon',
                            'dark_alt' => 'component icon',
                        ]
                    );

                    $lightPath = $assetsDir.$tile['light'];
                    $darkPath  = $assetsDir.$tile['dark'];

                    if (is_file($lightPath)) {
                        $model->clearMediaCollection(ComponentTile::MEDIA_COLLECTION_LIGHT);
                        $model->addMedia($lightPath)->preservingOriginal()
                            ->toMediaCollection(ComponentTile::MEDIA_COLLECTION_LIGHT);
                    }

                    if (is_file($darkPath)) {
                        $model->clearMediaCollection(ComponentTile::MEDIA_COLLECTION_DARK);
                        $model->addMedia($darkPath)->preservingOriginal()
                            ->toMediaCollection(ComponentTile::MEDIA_COLLECTION_DARK);
                    }
                }
            }
        }
    }
}
