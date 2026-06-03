import { publicMenuIcons } from '@/utils/publicMenuIcons'

export const publicDropdownMenus = (t) => [
    {
        key: 'blog',
        title: t('blog'),
        links: [
            {
                label: t('rubrics'),
                route: 'public.blogRubrics.index',
                active: ['public.blogRubrics.*', 'public.blogTags.*'],
                icon: publicMenuIcons.rubrics,
            },
            {
                label: t('articles'),
                route: 'public.blogArticles.index',
                active: 'public.blogArticles.*',
                icon: publicMenuIcons.articles,
            },
            {
                label: t('videos'),
                route: 'public.blogVideos.index',
                active: 'public.blogVideos.*',
                icon: publicMenuIcons.videos,
            },
        ],
    },
    {
        key: 'school',
        title: t('school'),
        links: [
            {
                label: t('instructors'),
                route: 'public.instructors.index',
                active: 'public.instructors.*',
                icon: publicMenuIcons.instructors,
            },
            {
                label: t('tracks'),
                route: 'public.tracks.index',
                active: 'public.tracks.*',
                icon: publicMenuIcons.tracks,
            },
            {
                label: t('courses'),
                route: 'public.courses.index',
                active: 'public.courses.*',
                icon: publicMenuIcons.courses,
            },
            {
                label: t('modules'),
                route: 'public.modules.index',
                active: 'public.modules.*',
                icon: publicMenuIcons.modules,
            },
            {
                label: t('lessons'),
                route: 'public.lessons.index',
                active: 'public.lessons.*',
                icon: publicMenuIcons.lessons,
            },
        ],
    },
]
