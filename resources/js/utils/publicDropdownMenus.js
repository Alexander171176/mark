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
                route: 'public.schoolInstructors.index',
                active: 'public.schoolInstructors.*',
                icon: publicMenuIcons.instructors,
            },
            {
                label: t('tracks'),
                route: 'public.schoolTracks.index',
                active: 'public.schoolTracks.*',
                icon: publicMenuIcons.tracks,
            },
            {
                label: t('courses'),
                route: 'public.schoolCourses.index',
                active: 'public.schoolCourses.*',
                icon: publicMenuIcons.courses,
            },
            {
                label: t('modules'),
                route: 'public.schoolModules.index',
                active: 'public.schoolModules.*',
                icon: publicMenuIcons.modules,
            },
            {
                label: t('lessons'),
                route: 'public.schoolLessons.index',
                active: 'public.schoolLessons.*',
                icon: publicMenuIcons.lessons,
            },
            {
                label: t('assignments'),
                route: 'public.schoolAssignments.index',
                active: 'public.schoolAssignments.*',
                icon: publicMenuIcons.assignments,
            },
        ],
    },
]
