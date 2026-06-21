<?php

namespace App\Traits\Admin\Settings;

use App\Http\Requests\Admin\System\UpdateCountSettingRequest;
use Illuminate\Http\RedirectResponse;

trait CountSettingsTrait
{
    /**
     * Тип значения для всех count-настроек.
     */
    protected string $countSettingType = 'number';

    /**
     * Категория всех count-настроек.
     */
    protected string $countSettingCategory = 'admin';

    /**
     * Сообщение об успешном обновлении.
     */
    private string $countSuccessMessage = 'Количество элементов на странице успешно обновлено.';

    /**
     * Сообщение об ошибке обновления.
     */
    private string $countErrorMessage = 'Ошибка обновления настройки количества элементов.';

    /**
     * Универсальный обработчик для обновления count-настроек.
     *
     * @param UpdateCountSettingRequest $request
     * @param string $optionKey Ключ опции в таблице settings
     * @param string $configKey Ключ в конфигурации site_settings
     * @return RedirectResponse
     */
    protected function countSetting(
        UpdateCountSettingRequest $request,
        string $optionKey,
        string $configKey
    ): RedirectResponse {
        return $this->updateSettingAndRedirect(
            $request,
            $optionKey,
            $configKey,
            $this->countSettingType,
            $this->countSettingCategory,
            $this->countSuccessMessage,
            $this->countErrorMessage
        );
    }

    /** Обновление количества элементов в настройках */
    public function updateAdminCountSettings(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSystemSettingsPerPage', 'site_settings.adminSystemSettingsPerPage');
    }

    /** Обновление количества элементов в рубриках */
    public function updateAdminCountRubrics(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogRubricsPerPage', 'site_settings.adminBlogRubricsPerPage');
    }

    /** Обновление количества элементов в статьях */
    public function updateAdminCountArticles(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogArticlesPerPage', 'site_settings.adminBlogArticlesPerPage');
    }

    /** Обновление количества элементов в тегах */
    public function updateAdminCountTags(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogTagsPerPage', 'site_settings.adminBlogTagsPerPage');
    }

    /** Обновление количества элементов в баннерах */
    public function updateAdminCountBanners(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogBannersPerPage', 'site_settings.adminBlogBannersPerPage');
    }

    /** Обновление количества элементов в видео */
    public function updateAdminCountVideos(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogVideosPerPage', 'site_settings.adminBlogVideosPerPage');
    }

    /** Обновление количества элементов в комментариях */
    public function updateAdminCountComments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminCommentsPerPage', 'site_settings.adminCommentsPerPage');
    }

    /** Обновление количества элементов в валютах */
    public function updateAdminCountCurrencies(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminFinanceCurrenciesPerPage', 'site_settings.adminFinanceCurrenciesPerPage');
    }

    /** Обновление количества элементов в тегах обучения */
    public function updateAdminCountHashtags(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolHashtagsPerPage', 'site_settings.adminSchoolHashtagsPerPage');
    }

    /** Обновление количества элементов в преподователях */
    public function updateAdminCountInstructors(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolInstructorsPerPage', 'site_settings.adminSchoolInstructorsPerPage');
    }

    /** Обновление количества элементов в категориях курсов */
    public function updateAdminCountTracks(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolTracksPerPage', 'site_settings.adminSchoolTracksPerPage');
    }

    /** Обновление количества элементов в курсах */
    public function updateAdminCountCourses(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolCoursesPerPage', 'site_settings.adminSchoolCoursesPerPage');
    }

    /** Обновление количества элементов в модулях обучения */
    public function updateAdminCountModules(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolModulesPerPage', 'site_settings.adminSchoolModulesPerPage');
    }

    /** Обновление количества элементов в уроках обучения */
    public function updateAdminCountLessons(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolLessonsPerPage', 'site_settings.adminSchoolLessonsPerPage');
    }

    /** Обновление количества элементов в заданиях */
    public function updateAdminCountAssignments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolAssignmentsPerPage', 'site_settings.adminSchoolAssignmentsPerPage');
    }

    /** Обновление количества элементов в расписании потоков */
    public function updateAdminCountCourseSchedules(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolCourseSchedulesPerPage', 'site_settings.adminSchoolCourseSchedulesPerPage');
    }

    /** Обновление количества элементов в записях на потоки */
    public function updateAdminCountCohortEnrollments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolCohortEnrollmentsPerPage', 'site_settings.adminSchoolCohortEnrollmentsPerPage');
    }

    /** Обновление количества элементов в зачислениях на потоки */
    public function updateAdminCountEnrollments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolEnrollmentsPerPage', 'site_settings.adminSchoolEnrollmentsPerPage');
    }

    /** Обновление количества элементов в квизах */
    public function updateAdminCountQuizzes(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizzesPerPage', 'site_settings.adminSchoolQuizzesPerPage');
    }

    /** Обновление количества элементов в вопросах квиза */
    public function updateAdminCountQuizQuestions(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizQuestionsPerPage', 'site_settings.adminSchoolQuizQuestionsPerPage');
    }

    /** Обновление количества элементов в ответах квиза */
    public function updateAdminCountQuizAnswers(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizAnswersPerPage', 'site_settings.adminSchoolQuizAnswersPerPage');
    }

    /** Обновление количества элементов в прохождениях квиза */
    public function updateAdminCountQuizAttempts(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizAttemptsPerPage', 'site_settings.adminSchoolQuizAttemptsPerPage');
    }

    /** Обновление количества элементов в попытках ответа квиза */
    public function updateAdminCountQuizAttemptItems(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizAttemptItemsPerPage', 'site_settings.adminSchoolQuizAttemptItemsPerPage');
    }

    /** Обновление количества элементов в бандлах */
    public function updateAdminCountBundles(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolBundlesPerPage', 'site_settings.adminSchoolBundlesPerPage');
    }

    /** Обновление количества элементов в заказах школы */
    public function updateAdminCountOrders(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolOrdersPerPage', 'site_settings.adminSchoolOrdersPerPage');
    }

    /** Обновление количества элементов в прайсах курсов */
    public function updateAdminCountCoursePrices(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolCoursePricesPerPage', 'site_settings.adminSchoolCoursePricesPerPage');
    }

    /** Обновление количества элементов в прайсах наборов курсов */
    public function updateAdminCountBundlePrices(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolBundlePricesPerPage', 'site_settings.adminSchoolBundlePricesPerPage');
    }

    /** Обновление количества элементов в тарифных планах */
    public function updateAdminCountSubscriptionPlans(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolSubscriptionPlansPerPage', 'site_settings.adminSchoolSubscriptionPlansPerPage');
    }

    /** Обновление количества элементов в пользователях */
    public function updateAdminCountUsers(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSystemUsersPerPage', 'site_settings.adminSystemUsersPerPage');
    }

    /** Обновление количества элементов в ролях */
    public function updateAdminCountRoles(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSystemRolesPerPage', 'site_settings.adminSystemRolesPerPage');
    }

    /** Обновление количества элементов в разрешениях */
    public function updateAdminCountPermissions(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSystemPermissionsPerPage', 'site_settings.adminSystemPermissionsPerPage');
    }

    /** Обновление количества элементов в компаниях */
    public function updateAdminCountCompanies(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketCompaniesPerPage', 'site_settings.adminMarketCompaniesPerPage');
    }

    /** Обновление количества элементов в магазинах */
    public function updateAdminCountShops(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketShopsPerPage', 'site_settings.adminMarketShopsPerPage');
    }

}
