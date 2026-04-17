<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import WelcomeCard from '@/Components/Admin/Welcome/WelcomeCard.vue'

defineProps({
    t: { type: Function, required: true },
})

const page = usePage()

const description = (key) =>
    page.props.laravelLang?.admin?.welcome?.education?.[key]?.description ?? ''

const descriptions = computed(() => ({
    instructors: description('instructors'),
    hashtags: description('hashtags'),
    learningCategories: description('learningCategories'),
    bundles: description('bundles'),
    courses: description('courses'),
    modules: description('modules'),
    lessons: description('lessons'),
    assignments: description('assignments'),
    courseSchedules: description('courseSchedules'),
    cohortEnrollments: description('cohortEnrollments'),
    enrollments: description('enrollments'),
    quizzes: description('quizzes'),
    quizQuestions: description('quizQuestions'),
    quizAnswers: description('quizAnswers'),
    quizAttempts: description('quizAttempts'),
    quizAttemptItems: description('quizAttemptItems'),
}))
</script>

<template>
    <div
        class="p-4 bg-gray-200 dark:bg-slate-700 bg-opacity-95 dark:bg-opacity-95
               grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5"
    >
        <WelcomeCard
            :href="route('admin.instructorProfiles.index')"
            :title="t('instructors')"
            :description="descriptions.instructors"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path>
                    <path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path>
                    <path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path>
                    <path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path>
                    <path d="M12 6a3 3 0 110-6 3 3 0 010 6zm2 18h-4a1 1 0 01-1-1v-6H6v-6a3 3 0 013-3h6a3 3 0 013 3v6h-3v6a1 1 0 01-1 1z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.hashtags.index')"
            :title="t('hashtags')"
            :description="descriptions.hashtags"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M19,0H5A3,3,0,0,0,2,3V23.5a.5.5,0,0,0,.767.423L12,18.092l9.233,5.831A.493.493,0,0,0,21.5,24a.5.5,0,0,0,.5-.5V3A3,3,0,0,0,19,0Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.learningCategories.index')"
            :title="t('learningCategories')"
            :description="descriptions.learningCategories"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M3,7C1.346,7,0,5.654,0,4s1.346-3,3-3s3,1.346,3,3S4.654,7,3,7z"></path>
                    <path d="M3,19c-1.654,0-3-1.346-3-3s1.346-3,3-3s3,1.346,3,3S4.654,19,3,19z"></path>
                    <path d="M23,5H9C8.448,5,8,4.552,8,4s0.448-1,1-1h14c0.552,0,1,0.448,1,1S23.552,5,23,5z"></path>
                    <path d="M23,11H9c-0.552,0-1-0.448-1-1s0.448-1,1-1h14c0.552,0,1,0.448,1,1S23.552,11,23,11z"></path>
                    <path d="M23,17H9c-0.552,0-1-0.448-1-1s0.448-1,1-1h14c0.552,0,1,0.448,1,1S23.552,17,23,17z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.bundles.index')"
            :title="t('bundles')"
            :description="descriptions.bundles"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <circle cx="18.5" cy="8.5" r="5.5"></circle>
                    <rect x="5" y="14" width="9" height="9" rx="1"></rect>
                    <path d="M10.932,9.752a.5.5,0,0,0,0-.5l-4.5-8a.52.52,0,0,0-.872,0l-4.5,8A.5.5,0,0,0,1.5,10h9A.5.5,0,0,0,10.932,9.752Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.courses.index')"
            :title="t('courses')"
            :description="descriptions.courses"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.modules.index')"
            :title="t('modules')"
            :description="descriptions.modules"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <rect x="1" y="1" width="10" height="10" rx="2"></rect>
                    <path d="M23.428,4.618,19.381.572h0a1.957,1.957,0,0,0-2.762,0L12.572,4.618a1.959,1.959,0,0,0,0,2.764l4.047,4.047a1.957,1.957,0,0,0,2.762,0l4.047-4.046A1.959,1.959,0,0,0,23.428,4.618Z"></path>
                    <rect x="13" y="13" width="10" height="10" rx="2"></rect>
                    <rect x="1" y="13" width="10" height="10" rx="2"></rect>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.lessons.index')"
            :title="t('lessons')"
            :description="descriptions.lessons"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M23.58.424A1,1,0,0,0,22.819.13C8.791.862,3.609,13.358,3.559,13.484a1,1,0,0,0,.22,1.08l5.657,5.657a1,1,0,0,0,1.085.218c.125-.051,12.554-5.291,13.348-19.253A1,1,0,0,0,23.58.424Zm-8.166,10.99a2,2,0,1,1,0-2.828A2,2,0,0,1,15.414,11.414Z"></path>
                    <path d="M1.113,18.844a2.844,2.844,0,1,1,4.022,4.022C4.024,23.977,0,24,0,24S0,19.954,1.113,18.844Z"></path>
                    <path d="M10.357,2.341A8.911,8.911,0,0,0,2.522,4.825a9.084,9.084,0,0,0-1.384,1.8,1,1,0,0,0,.155,1.215l1.989,1.99A26.623,26.623,0,0,1,10.357,2.341Z"></path>
                    <path d="M21.659,13.643a8.911,8.911,0,0,1-2.484,7.835,9.084,9.084,0,0,1-1.8,1.384,1,1,0,0,1-1.215-.155l-1.99-1.989A26.623,26.623,0,0,0,21.659,13.643Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.assignments.index')"
            :title="t('assignments')"
            :description="descriptions.assignments"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M15,18v2H9v-2H1v5c0,0.552,0.448,1,1,1h20c0.552,0,1-0.448,1-1v-5H15z"></path>
                    <path d="M23,4h-6V1c0-0.552-0.448-1-1-1H8C7.448,0,7,0.448,7,1v3H1C0.448,4,0,4.448,0,5v10c0,0.552,0.448,1,1,1h8v-3 h6v3h8c0.552,0,1-0.448,1-1V5C24,4.448,23.552,4,23,4z M15,4H9V2h6V4z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.courseSchedules.index')"
            :title="t('courseSchedules')"
            :description="descriptions.courseSchedules"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M21,2H3C1.346,2,0,3.346,0,5v14c0,1.654,1.346,3,3,3H21c1.654,0,3-1.346,3-3V5c0-1.654-1.346-3-3-3ZM12,20H3c-.552,0-1-.449-1-1V5c0-.551,.448-1,1-1H12V20Zm9-3.5c0,.276-.224,.5-.5,.5h-5c-.276,0-.5-.224-.5-.5v-1c0-.276,.224-.5,.5-.5h5c.276,0,.5,.224,.5,.5v1Zm0-4c0,.276-.224,.5-.5,.5h-5c-.276,0-.5-.224-.5-.5v-1c0-.276,.224-.5,.5-.5h5c.276,0,.5,.224,.5,.5v1Zm0-4c0,.276-.224,.5-.5,.5h-5c-.276,0-.5-.224-.5-.5v-1c0-.276,.224-.5,.5-.5h5c.276,0,.5,.224,.5,.5v1Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.cohortEnrollments.index')"
            :title="t('cohortEnrollments')"
            :description="descriptions.cohortEnrollments"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M22,23H2c-.553,0-1-.448-1-1s.447-1,1-1H22c.553,0,1,.448,1,1s-.447,1-1,1Z"></path>
                    <path d="M18.414,8l2-2c.378-.378,.586-.88,.586-1.414s-.208-1.037-.586-1.414l-1.586-1.586c-.779-.78-2.049-.78-2.828,0l-2,2,4.414,4.414Z"></path>
                    <path d="M12.586,5l-7.293,7.293c-.096,.096-.171,.21-.222,.335l-2,5c-.148,.372-.062,.796,.222,1.079,.191,.191,.447,.293,.707,.293,.125,0,.251-.023,.371-.071l5-2c.126-.05,.24-.126,.336-.222l7.293-7.293-4.414-4.414Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.enrollments.index')"
            :title="t('enrollments')"
            :description="descriptions.enrollments"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M22.641,4.232c-.427-.354-1.056-.296-1.409,.128L11.933,15.519l-.433-.433-1.286,1.543,1.078,1.078c.188,.188,.442,.293,.707,.293,.015,0,.03,0,.045,0,.281-.013,.543-.143,.724-.359L22.769,5.64c.354-.424,.296-1.055-.128-1.408Z"></path>
                    <path d="M6,18c-.265,0-.52-.105-.707-.293L1.293,13.707c-.391-.391-.391-1.023,0-1.414s1.023-.391,1.414,0l3.226,3.226L15.231,4.36c.354-.424,.983-.481,1.409-.128,.424,.354,.481,.984,.128,1.408L6.769,17.64c-.181,.216-.442,.346-.724,.359-.015,0-.03,0-.045,0Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.quizzes.index')"
            :title="t('quizzes')"
            :description="descriptions.quizzes"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M20,1H4c-1.654,0-3,1.346-3,3V20c0,1.654,1.346,3,3,3H20c1.654,0,3-1.346,3-3V4c0-1.654-1.346-3-3-3ZM12,19.5c-.69,0-1.25-.56-1.25-1.25s.56-1.25,1.25-1.25,1.25,.56,1.25,1.25-.56,1.25-1.25,1.25Zm2.688-7.198c-1.444,1.224-1.688,1.667-1.688,2.198,0,.553-.448,1-1,1s-1-.447-1-1c0-1.434,.807-2.379,2.395-3.724,.447-.38,1.844-1.72,1.025-3.046-.532-.86-2.515-.984-4.162-.256-.505,.22-1.096-.005-1.319-.511-.223-.505,.005-1.096,.51-1.319,2.329-1.028,5.462-.924,6.672,1.035,1.103,1.784,.554,3.938-1.433,5.622Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.quizQuestions.index')"
            :title="t('quizQuestions')"
            :description="descriptions.quizQuestions"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm0,19.5c-.69,0-1.25-.56-1.25-1.25s.56-1.25,1.25-1.25,1.25,.56,1.25,1.25-.56,1.25-1.25,1.25Zm2.688-7.198c-1.444,1.224-1.688,1.667-1.688,2.198,0,.553-.447,1-1,1s-1-.447-1-1c0-1.434,.807-2.379,2.395-3.724,.447-.38,1.844-1.72,1.024-3.046-.532-.861-2.517-.984-4.162-.256-.507,.223-1.096-.006-1.319-.511-.223-.505,.006-1.096,.511-1.319,2.33-1.03,5.463-.924,6.672,1.035,1.103,1.784,.554,3.938-1.433,5.622Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.quizAnswers.index')"
            :title="t('quizAnswers')"
            :description="descriptions.quizAnswers"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M12,0C5.383,0,0,5.383,0,12s5.383,12,12,12,12-5.383,12-12S18.617,0,12,0Zm1,17c0,.553-.448,1-1,1s-1-.447-1-1v-6c0-.553,.448-1,1-1s1,.447,1,1v6Zm-1-9c-.69,0-1.25-.56-1.25-1.25s.56-1.25,1.25-1.25,1.25,.56,1.25,1.25-.56,1.25-1.25,1.25Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.quizAttempts.index')"
            :title="t('quizAttempts')"
            :description="descriptions.quizAttempts"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M22,15a1,1,0,0,1,0-2h1.949a11.941,11.941,0,0,0-2.794-6.741L19.778,7.636a1,1,0,1,1-1.414-1.414l1.377-1.377A11.941,11.941,0,0,0,13,2.051V4a1,1,0,0,1-2,0V2.051A12.006,12.006,0,0,0,.051,13H2a1,1,0,0,1,0,2H.044a12.025,12.025,0,0,0,2.7,6.637l.3.363H20.956l.3-.363A12.025,12.025,0,0,0,23.956,15Zm-8.586.414a2,2,0,0,1-2.828,0c-.6-.6-4.552-6.468-5-7.135a.5.5,0,0,1,.694-.694c.667.448,6.533,4.4,7.135,5A2,2,0,0,1,13.414,15.414Z"></path>
                </svg>
            </template>
        </WelcomeCard>
        <WelcomeCard
            :href="route('admin.quizAttemptItems.index')"
            :title="t('quizAttemptItems')"
            :description="descriptions.quizAttemptItems"
        >
            <template #icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="0.5"
                     class="w-5 h-5 stroke-gray-900 dark:stroke-gray-100">
                    <path d="M12,15.75l3.882-1.941-3.435-1.717a1,1,0,0,0-.894,0L8.118,13.809Z"></path>
                    <path d="M13,17.486v5.382l3.447-1.724A1,1,0,0,0,17,20.25V15.486Z"></path>
                    <path d="M11,17.486l-4-2V20.25a1,1,0,0,0,.553.894L11,22.868Z"></path>
                    <path d="M6,5.014,9.881,3.073,6.447,1.356a1,1,0,0,0-.9,0L2.118,3.073Z"></path>
                    <path d="M7,6.75v5.382l3.447-1.724A1,1,0,0,0,11,9.514V4.75Z"></path>
                    <path d="M5,6.75l-4-2V9.514a1,1,0,0,0,.552.894L5,12.132Z"></path>
                    <path d="M18,5.014l3.882-1.941L18.447,1.356a1,1,0,0,0-.894,0L14.118,3.073Z"></path>
                    <path d="M19,6.75v5.382l3.447-1.724A1,1,0,0,0,23,9.514V4.75Z"></path>
                    <path d="M17,6.75l-4-2V9.514a1,1,0,0,0,.553.894L17,12.132Z"></path>
                </svg>
            </template>
        </WelcomeCard>
    </div>
</template>
