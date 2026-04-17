<script setup>
import { useI18n } from 'vue-i18n';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';

const { t } = useI18n();
</script>

<template>
    <div class="pt-2 pb-24 space-y-1">
        <!-- Маршрут пользовательского дашборда остается без префикса -->
        <ResponsiveNavLink :href="route('dashboard')"
                           :active="route().current('dashboard')">
            {{ t('dashboard') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.index')"
                           :active="route().current('admin.index')">
            {{ t('adminPanel') }}
        </ResponsiveNavLink>
        <!-- Админские маршруты с префиксом -->
        <ResponsiveNavLink :href="route('admin.home-page.index')"
                           :active="route().current('admin.home-page.index')">
            {{ t('home') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.orders.index')"
                           :active="route().current('admin.orders.index')">
            {{ t('orders') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.subscriptionPlans.index')"
                           :active="route().current('admin.subscriptionPlans.index')">
            {{ t('subscriptionPlans') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.coursePrices.index')"
                           :active="route().current('admin.coursePrices.index')">
            {{ t('coursePrices') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.bundlePrices.index')"
                           :active="route().current('admin.bundlePrices.index')">
            {{ t('bundlePrices') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.currencies.index')"
                           :active="route().current('admin.currencies.index')">
            {{ t('currencies') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.instructorProfiles.index')"
                           :active="route().current('admin.instructorProfiles.index')">
            {{ t('instructors') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.hashtags.index')"
                           :active="route().current('admin.hashtags.index')">
            {{ t('hashtags') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.learningCategories.index')"
                           :active="route().current('admin.learningCategories.index')">
            {{ t('learningCategories') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.bundles.index')"
                           :active="route().current('admin.bundles.index')">
            {{ t('bundles') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.courses.index')"
                           :active="route().current('admin.courses.index')">
            {{ t('courses') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.modules.index')"
                           :active="route().current('admin.modules.index')">
            {{ t('modules') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.lessons.index')"
                           :active="route().current('admin.lessons.index')">
            {{ t('lessons') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.assignments.index')"
                           :active="route().current('admin.assignments.index')">
            {{ t('assignments') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.courseSchedules.index')"
                           :active="route().current('admin.courseSchedules.index')">
            {{ t('courseSchedules') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.cohortEnrollments.index')"
                           :active="route().current('admin.cohortEnrollments.index')">
            {{ t('cohortEnrollments') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.enrollments.index')"
                           :active="route().current('admin.enrollments.index')">
            {{ t('enrollments') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.quizzes.index')"
                           :active="route().current('admin.quizzes.index')">
            {{ t('quizzes') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.quizQuestions.index')"
                           :active="route().current('admin.quizQuestions.index')">
            {{ t('quizQuestions') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.quizAnswers.index')"
                           :active="route().current('admin.quizAnswers.index')">
            {{ t('quizAnswers') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.quizAttempts.index')"
                           :active="route().current('admin.quizAttempts.index')">
            {{ t('quizAttempts') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.quizAttemptItems.index')"
                           :active="route().current('admin.quizAttemptItems.index')">
            {{ t('quizAttemptItems') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.users.index')"
                           :active="route().current('admin.users.index')">
            {{ t('users') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.roles.index')"
                           :active="route().current('admin.roles.index')">
            {{ t('roles') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.permissions.index')"
                           :active="route().current('admin.permissions.index')">
            {{ t('permissions') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.rubrics.index')"
                           :active="route().current('admin.rubrics.index')">
            {{ t('rubrics') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.articles.index')"
                           :active="route().current('admin.articles.index')">
            {{ t('articles') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.tags.index')"
                           :active="route().current('admin.tags.index')">
            {{ t('tags') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.comments.index')"
                           :active="route().current('admin.comments.index')">
            {{ t('comments') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.banners.index')"
                           :active="route().current('admin.banners.index')">
            {{ t('banners') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.videos.index')"
                           :active="route().current('admin.videos.index')">
            {{ t('videos') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.charts.index')"
                           :active="route().current('admin.charts.index')">
            {{ t('charts') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.settings.index')"
                           :active="route().current('admin.settings.index')">
            {{ t('settings') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.parameters.index')"
                           :active="route().current('admin.parameters.index')">
            {{ t('parameters') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.logs.index')"
                           :active="route().current('admin.logs.index')">
            {{ t('logs') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.phpinfo.index')"
                           :active="route().current('admin.phpinfo.index')">
            phpinfo
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.composer.index')"
                           :active="route().current('admin.composer.index')">
            composer
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.package.index')"
                           :active="route().current('admin.package.index')">
            package
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.env.index')"
                           :active="route().current('admin.env.index')">
            env
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.backup.index')"
                           :active="route().current('admin.backup.index')">
            {{ t('backups') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.files.index')"
                           :active="route().current('admin.files.index')">
            {{ t('archive') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.components.index')"
                           :active="route().current('admin.components.index')">
            {{ t('components') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.robot.index')"
                           :active="route().current('admin.robot.index')">
            {{ t('robot') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.sitemap.index')"
                           :active="route().current('admin.sitemap.index')">
            sitemap.xml
        </ResponsiveNavLink>
        <ResponsiveNavLink :href="route('admin.reports.index')"
                           :active="route().current('admin.reports.index')">
            {{ t('reports') }}
        </ResponsiveNavLink>
    </div>
</template>
