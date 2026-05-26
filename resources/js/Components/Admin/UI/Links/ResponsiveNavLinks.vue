<script setup>
import { useI18n } from 'vue-i18n';
import ResponsiveNavLink from '@/Components/Base/ResponsiveNavLink.vue';

const { t } = useI18n();
</script>

<template>
    <div class="pt-2 pb-24 space-y-1">
        <!-- Маршрут пользовательского дашборда остается без префикса -->
        <ResponsiveNavLink
            :href="route('dashboard')"
            :active="route().current('dashboard')">
            {{ t('dashboard') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.index')"
            :active="route().current('admin.index')">
            {{ t('adminPanel') }}
        </ResponsiveNavLink>
        <!-- Админские маршруты с префиксом -->
        <ResponsiveNavLink
            :href="route('admin.marketCompanies.index')"
            :active="route().current('admin.marketCompanies.index')">
            {{ t('marketCompanies') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.marketStorefronts.index')"
            :active="route().current('admin.marketStorefronts.index')">
            {{ t('marketStorefronts') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolOrders.index')"
            :active="route().current('admin.schoolOrders.index')">
            {{ t('orders') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.subscriptionPlans.index')"
            :active="route().current('admin.subscriptionPlans.index')">
            {{ t('subscriptionPlans') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.coursePrices.index')"
            :active="route().current('admin.coursePrices.index')">
            {{ t('coursePrices') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.bundlePrices.index')"
            :active="route().current('admin.bundlePrices.index')">
            {{ t('bundlePrices') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.currencies.index')"
            :active="route().current('admin.currencies.index')">
            {{ t('currencies') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolInstructorProfiles.index')"
            :active="route().current('admin.schoolInstructorProfiles.index')">
            {{ t('instructors') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolHashtags.index')"
            :active="route().current('admin.schoolHashtags.index')">
            {{ t('hashtags') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolTracks.index')"
            :active="route().current('admin.schoolTracks.index')">
            {{ t('learningCategories') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.bundles.index')"
            :active="route().current('admin.bundles.index')">
            {{ t('bundles') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolCourses.index')"
            :active="route().current('admin.schoolCourses.index')">
            {{ t('courses') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolModules.index')"
            :active="route().current('admin.schoolModules.index')">
            {{ t('modules') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolLessons.index')"
            :active="route().current('admin.schoolLessons.index')">
            {{ t('lessons') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolAssignments.index')"
            :active="route().current('admin.schoolAssignments.index')">
            {{ t('assignments') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolCourseSchedules.index')"
            :active="route().current('admin.schoolCourseSchedules.index')">
            {{ t('courseSchedules') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolCohortEnrollments.index')"
            :active="route().current('admin.schoolCohortEnrollments.index')">
            {{ t('cohortEnrollments') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolEnrollments.index')"
            :active="route().current('admin.schoolEnrollments.index')">
            {{ t('enrollments') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolQuizzes.index')"
            :active="route().current('admin.schoolQuizzes.index')">
            {{ t('quizzes') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolQuizQuestions.index')"
            :active="route().current('admin.schoolQuizQuestions.index')">
            {{ t('quizQuestions') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.quizAnswers.index')"
            :active="route().current('admin.quizAnswers.index')">
            {{ t('quizAnswers') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.quizAttempts.index')"
            :active="route().current('admin.quizAttempts.index')">
            {{ t('quizAttempts') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.quizAttemptItems.index')"
            :active="route().current('admin.quizAttemptItems.index')">
            {{ t('quizAttemptItems') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.users.index')"
            :active="route().current('admin.users.index')">
            {{ t('users') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.roles.index')"
            :active="route().current('admin.roles.index')">
            {{ t('roles') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.permissions.index')"
            :active="route().current('admin.permissions.index')">
            {{ t('permissions') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.blogRubrics.index')"
            :active="route().current('admin.blogRubrics.index')">
            {{ t('rubrics') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.blogArticles.index')"
            :active="route().current('admin.blogArticles.index')">
            {{ t('articles') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.blogTags.index')"
            :active="route().current('admin.blogTags.index')">
            {{ t('tags') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.comments.index')"
            :active="route().current('admin.comments.index')">
            {{ t('comments') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.blogBanners.index')"
            :active="route().current('admin.blogBanners.index')">
            {{ t('banners') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.blogVideos.index')"
            :active="route().current('admin.blogVideos.index')">
            {{ t('videos') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.charts.index')"
            :active="route().current('admin.charts.index')">
            {{ t('charts') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.settings.index')"
            :active="route().current('admin.settings.index')">
            {{ t('settings') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.parameters.index')"
            :active="route().current('admin.parameters.index')">
            {{ t('parameters') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.logs.index')"
            :active="route().current('admin.logs.index')">
            {{ t('logs') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.phpinfo.index')"
            :active="route().current('admin.phpinfo.index')">
            phpinfo
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.composer.index')"
            :active="route().current('admin.composer.index')">
            composer
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.package.index')"
            :active="route().current('admin.package.index')">
            package
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.env.index')"
            :active="route().current('admin.env.index')">
            env
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.backup.index')"
            :active="route().current('admin.backup.index')">
            {{ t('backups') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.files.index')"
            :active="route().current('admin.files.index')">
            {{ t('archive') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.components.index')"
            :active="route().current('admin.components.index')">
            {{ t('components') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.robot.index')"
            :active="route().current('admin.robot.index')">
            {{ t('robot') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.sitemap.index')"
            :active="route().current('admin.sitemap.index')">
            sitemap.xml
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.reports.index')"
            :active="route().current('admin.reports.index')">
            {{ t('reports') }}
        </ResponsiveNavLink>
    </div>
</template>
