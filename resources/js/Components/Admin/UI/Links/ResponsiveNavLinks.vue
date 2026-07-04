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
        <!-- Админские маршруты магазина -->
        <ResponsiveNavLink
            :href="route('admin.marketCompanies.index')"
            :active="route().current('admin.marketCompanies.*')">
            {{ t('marketCompanies') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.marketShops.index')"
            :active="route().current('admin.marketShops.*')">
            {{ t('marketShops') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.marketCategories.index')"
            :active="route().current('admin.marketCategories.*')">
            {{ t('marketCategories') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.marketBrands.index')"
            :active="route().current('admin.marketBrands.*')">
            {{ t('marketBrands') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.marketTags.index')"
            :active="route().current('admin.marketTags.*')">
            {{ t('marketTags') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.marketAttributeGroups.index')"
            :active="route().current('admin.marketAttributeGroups.*')">
            {{ t('marketAttributeGroups') }}
        </ResponsiveNavLink>
        <!-- Админский маршрут валюты -->
        <ResponsiveNavLink
            :href="route('admin.currencies.index')"
            :active="route().current('admin.currencies.*')">
            {{ t('currencies') }}
        </ResponsiveNavLink>
        <!-- Админские маршруты онлайн школы -->
        <ResponsiveNavLink
            :href="route('admin.schoolOrders.index')"
            :active="route().current('admin.schoolOrders.*')">
            {{ t('orders') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolSubscriptionPlans.index')"
            :active="route().current('admin.schoolSubscriptionPlans.*')">
            {{ t('subscriptionPlans') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolCoursePrices.index')"
            :active="route().current('admin.schoolCoursePrices.*')">
            {{ t('coursePrices') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolBundlePrices.index')"
            :active="route().current('admin.schoolBundlePrices.*')">
            {{ t('bundlePrices') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolInstructorProfiles.index')"
            :active="route().current('admin.schoolInstructorProfiles.*')">
            {{ t('instructors') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolHashtags.index')"
            :active="route().current('admin.schoolHashtags.*')">
            {{ t('hashtags') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolTracks.index')"
            :active="route().current('admin.schoolTracks.*')">
            {{ t('learningCategories') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolBundles.index')"
            :active="route().current('admin.schoolBundles.*')">
            {{ t('bundles') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolCourses.index')"
            :active="route().current('admin.schoolCourses.*')">
            {{ t('courses') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolModules.index')"
            :active="route().current('admin.schoolModules.*')">
            {{ t('modules') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolLessons.index')"
            :active="route().current('admin.schoolLessons.*')">
            {{ t('lessons') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolAssignments.index')"
            :active="route().current('admin.schoolAssignments.*')">
            {{ t('assignments') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolCourseSchedules.index')"
            :active="route().current('admin.schoolCourseSchedules.*')">
            {{ t('courseSchedules') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolCohortEnrollments.index')"
            :active="route().current('admin.schoolCohortEnrollments.*')">
            {{ t('cohortEnrollments') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolEnrollments.index')"
            :active="route().current('admin.schoolEnrollments.*')">
            {{ t('enrollments') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolQuizzes.index')"
            :active="route().current('admin.schoolQuizzes.*')">
            {{ t('quizzes') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolQuizQuestions.index')"
            :active="route().current('admin.schoolQuizQuestions.*')">
            {{ t('quizQuestions') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolQuizAnswers.index')"
            :active="route().current('admin.schoolQuizAnswers.*')">
            {{ t('quizAnswers') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolQuizAttempts.index')"
            :active="route().current('admin.schoolQuizAttempts.*')">
            {{ t('quizAttempts') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.schoolQuizAttemptItems.index')"
            :active="route().current('admin.schoolQuizAttemptItems.*')">
            {{ t('quizAttemptItems') }}
        </ResponsiveNavLink>
        <!-- Админские маршруты блога -->
        <ResponsiveNavLink
            :href="route('admin.blogRubrics.index')"
            :active="route().current('admin.blogRubrics.*')">
            {{ t('rubrics') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.blogArticles.index')"
            :active="route().current('admin.blogArticles.*')">
            {{ t('articles') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.blogTags.index')"
            :active="route().current('admin.blogTags.*')">
            {{ t('tags') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.blogBanners.index')"
            :active="route().current('admin.blogBanners.*')">
            {{ t('banners') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.blogVideos.index')"
            :active="route().current('admin.blogVideos.*')">
            {{ t('videos') }}
        </ResponsiveNavLink>
        <!-- Админский маршрут комментариев -->
        <ResponsiveNavLink
            :href="route('admin.comments.index')"
            :active="route().current('admin.comments.*')">
            {{ t('comments') }}
        </ResponsiveNavLink>
        <!-- Админский маршрут графиков -->
        <ResponsiveNavLink
            :href="route('admin.charts.index')"
            :active="route().current('admin.charts.*')">
            {{ t('charts') }}
        </ResponsiveNavLink>
        <!-- Админские маршруты системы -->
        <ResponsiveNavLink
            :href="route('admin.users.index')"
            :active="route().current('admin.users.*')">
            {{ t('users') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.roles.index')"
            :active="route().current('admin.roles.*')">
            {{ t('roles') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.permissions.index')"
            :active="route().current('admin.permissions.*')">
            {{ t('permissions') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.settings.index')"
            :active="route().current('admin.settings.*')">
            {{ t('settings') }}
        </ResponsiveNavLink>
        <ResponsiveNavLink
            :href="route('admin.parameters.index')"
            :active="route().current('admin.parameters.*')">
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
        <ResponsiveNavLink
            :href="route('admin.imagePresets.index')"
            :active="route().current('admin.imagePresets.index')">
            {{ t('imagePresets') }}
        </ResponsiveNavLink>
    </div>
</template>
