<script setup>
import {computed, onMounted, onUnmounted, ref} from 'vue';
import {Link, usePage} from '@inertiajs/vue3';
import {useI18n} from 'vue-i18n';
import {sidebarIcons} from '@/utils/sidebarIcons';

const page = usePage()

const adminSettings = computed(() => page.props?.adminSettings || {})

const props = defineProps({
    id: String,
    expanded: Boolean
});

// Реф для хранения состояния темного режима (true, если активен)
const isDarkMode = ref(false);
let observer;

// Функция для проверки наличия класса "dark" на <html>
const checkDarkMode = () => {
    isDarkMode.value = document.documentElement.classList.contains('dark');
};

// При монтировании компонента запускаем первоначальную проверку и устанавливаем MutationObserver
onMounted(() => {
    checkDarkMode();
    observer = new MutationObserver(checkDarkMode);
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
});

// При размонтировании отключаем наблюдатель
onUnmounted(() => {
    if (observer) observer.disconnect();
});

const colorText = computed(() => {
    return isDarkMode.value
        ? (adminSettings.value.AdminSidebarDarkText || 'text-slate-200')
        : (adminSettings.value.AdminSidebarLightText || 'text-slate-200');
});

const colorTextHover = computed(() => {
    return isDarkMode.value
        ? (adminSettings.value.AdminSidebarDarkHoverText || 'text-orange-300')
        : (adminSettings.value.AdminSidebarLightHoverText || 'text-orange-300');
});

const colorTextActive = computed(() => {
    return isDarkMode.value
        ? (adminSettings.value.AdminSidebarDarkActiveText || 'text-yellow-200')
        : (adminSettings.value.AdminSidebarLightActiveText || 'text-yellow-200');
});

const {t} = useI18n();
const {props: pageProps} = usePage();

const linkInfo = {
    admin: {label: t('adminPanel'), route: 'admin.index'},
    home: {label: t('home'), route: 'admin.home-page.index'},
    marketCompanies: {label: t('marketCompanies'), route: 'admin.marketCompanies.index'},
    marketStorefronts: {label: t('marketStorefronts'), route: 'admin.marketStorefronts.index'},
    currencies: {label: t('currencies'), route: 'admin.currencies.index'},
    subscriptionPlans: {label: t('subscriptionPlans'), route: 'admin.schoolSubscriptionPlans.index'},
    coursePrices: {label: t('coursePrices'), route: 'admin.schoolCoursePrices.index'},
    bundlePrices: {label: t('bundlePrices'), route: 'admin.schoolBundlePrices.index'},
    orders: {label: t('orders'), route: 'admin.schoolOrders.index'},
    apiTokens: {label: t('apiTokens'), route: 'api-tokens.index'},
    teamSettings: {label: t('teamSettings'), route: 'teams.show', params: {team: pageProps.auth.user.current_team}},
    instructors: {label: t('instructors'), route: 'admin.schoolInstructorProfiles.index'},
    hashtags: {label: t('hashtags'), route: 'admin.schoolHashtags.index'},
    learningCategories: {label: t('learningCategories'), route: 'admin.schoolTracks.index'},
    bundles: {label: t('bundles'), route: 'admin.schoolBundles.index'},
    courses: {label: t('courses'), route: 'admin.schoolCourses.index'},
    modules: {label: t('modules'), route: 'admin.schoolModules.index'},
    lessons: {label: t('lessons'), route: 'admin.schoolLessons.index'},
    assignments: {label: t('assignments'), route: 'admin.schoolAssignments.index'},
    courseSchedules: {label: t('courseSchedules'), route: 'admin.schoolCourseSchedules.index'},
    cohortEnrollments: {label: t('cohortEnrollments'), route: 'admin.schoolCohortEnrollments.index'},
    enrollments: {label: t('enrollments'), route: 'admin.schoolEnrollments.index'},
    quizzes: {label: t('quizzes'), route: 'admin.schoolQuizzes.index'},
    quizQuestions: {label: t('quizQuestions'), route: 'admin.schoolQuizQuestions.index'},
    quizAnswers: {label: t('quizAnswers'), route: 'admin.schoolQuizAnswers.index'},
    quizAttempts: {label: t('quizAttempts'), route: 'admin.schoolQuizAttempts.index'},
    quizAttemptItems: {label: t('quizAttemptItems'), route: 'admin.schoolQuizAttemptItems.index'},
    users: {label: t('users'), route: 'admin.users.index'},
    roles: {label: t('roles'), route: 'admin.roles.index'},
    permissions: {label: t('permissions'), route: 'admin.permissions.index'},
    rubrics: {label: t('rubrics'), route: 'admin.blogRubrics.index'},
    articles: {label: t('articles'), route: 'admin.blogArticles.index'},
    tags: {label: t('tags'), route: 'admin.blogTags.index'},
    comments: {label: t('comments'), route: 'admin.comments.index'},
    banners: {label: t('banners'), route: 'admin.blogBanners.index'},
    videos: {label: t('videos'), route: 'admin.blogVideos.index'},
    charts: {label: t('charts'), route: 'admin.charts.index'},
    settings: {label: t('settings'), route: 'admin.settings.index'},
    parameters: {label: t('parameters'), route: 'admin.parameters.index'},
    logs: {label: t('logs'), route: 'admin.logs.index'},
    phpinfo: {label: 'phpinfo', route: 'admin.phpinfo.index'},
    composer: {label: 'composer', route: 'admin.composer.index'},
    package: {label: 'package', route: 'admin.package.index'},
    env: {label: 'env', route: 'admin.env.index'},
    backups: {label: t('backups'), route: 'admin.backup.index'},
    files: {label: t('archive'), route: 'admin.files.index'},
    components: {label: t('components'), route: 'admin.components.index'},
    robot: {label: t('robot'), route: 'admin.robot.index'},
    sitemap: {label: 'sitemap.xml', route: 'admin.sitemap.index'},
    reports: {label: t('reports'), route: 'admin.reports.index'},
};

const link = computed(() => linkInfo[props.id]);

const svgContent = computed(() => sidebarIcons[props.id]);

const isActive = computed(() => {
    if (!link.value?.route) {
        return false
    }

    const currentRoute = route().current()

    if (!currentRoute) {
        return false
    }

    // Точное совпадение
    if (currentRoute === link.value.route) {
        return true
    }

    // Если ссылка ведёт на index, подсвечиваем также create/edit/show/update/destroy
    if (link.value.route.endsWith('.index')) {
        const baseRoute = link.value.route.replace(/\.index$/, '')

        return currentRoute.startsWith(`${baseRoute}.`)
    }

    return false
})

const classes = computed(() => {
    if (isActive.value) {
        return `flex items-center px-1 text-xs font-medium leading-3
        ${colorTextActive.value}
        hover:${colorTextActive.value}
        focus:${colorTextActive.value}
        focus:outline-none transition duration-150 ease-in-out`;
    }

    return `flex items-center px-1 text-xs font-medium leading-3
    ${colorText.value}
    hover:${colorTextHover.value}
    focus:${colorTextHover.value}
    focus:outline-none transition duration-150 ease-in-out`;
})

const containerClasses = computed(() => {
    return props.expanded ? 'mb-1' : 'mb-3';
});

const textClasses = computed(() => {
    return props.expanded ? 'ml-3 opacity-100' : 'ml-3 opacity-0 whitespace-nowrap overflow-hidden';
});
</script>

<template>
    <li class="mt-0" :class="containerClasses">
        <Link :href="route(link.route, link.params || {})" :class="classes">
            <span v-html="svgContent"></span>
            <span class="text-xs font-medium transition-opacity duration-200 max-w-full"
                  :class="textClasses">
                {{ link.label }}
            </span>
        </Link>
    </li>
</template>
