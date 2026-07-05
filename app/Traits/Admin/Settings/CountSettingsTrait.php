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

    /** Обновление количества элементов в вариантах обработки изображений */
    public function updateAdminCountImagePresets(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminImagePresetsPerPage', 'site_settings.adminImagePresetsPerPage');
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

    /** Обновление количества элементов в валютах */
    public function updateAdminCountCurrencies(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminFinanceCurrenciesPerPage', 'site_settings.adminFinanceCurrenciesPerPage');
    }

    /** Обновление количества элементов в комментариях */
    public function updateAdminCountComments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminCommentsPerPage', 'site_settings.adminCommentsPerPage');
    }

    /** Обновление количества элементов в рубриках */
    public function updateAdminCountBlogRubrics(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogRubricsPerPage', 'site_settings.adminBlogRubricsPerPage');
    }

    /** Обновление количества элементов в статьях */
    public function updateAdminCountBlogArticles(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogArticlesPerPage', 'site_settings.adminBlogArticlesPerPage');
    }

    /** Обновление количества элементов в тегах */
    public function updateAdminCountBlogTags(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogTagsPerPage', 'site_settings.adminBlogTagsPerPage');
    }

    /** Обновление количества элементов в баннерах */
    public function updateAdminCountBlogBanners(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogBannersPerPage', 'site_settings.adminBlogBannersPerPage');
    }

    /** Обновление количества элементов в видео */
    public function updateAdminCountBlogVideos(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogVideosPerPage', 'site_settings.adminBlogVideosPerPage');
    }

    /** Обновление количества элементов в тегах обучения */
    public function updateAdminCountSchoolHashtags(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolHashtagsPerPage', 'site_settings.adminSchoolHashtagsPerPage');
    }

    /** Обновление количества элементов в преподователях */
    public function updateAdminCountSchoolInstructors(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolInstructorsPerPage', 'site_settings.adminSchoolInstructorsPerPage');
    }

    /** Обновление количества элементов в категориях курсов */
    public function updateAdminCountSchoolTracks(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolTracksPerPage', 'site_settings.adminSchoolTracksPerPage');
    }

    /** Обновление количества элементов в курсах */
    public function updateAdminCountSchoolCourses(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolCoursesPerPage', 'site_settings.adminSchoolCoursesPerPage');
    }

    /** Обновление количества элементов в модулях обучения */
    public function updateAdminCountSchoolModules(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolModulesPerPage', 'site_settings.adminSchoolModulesPerPage');
    }

    /** Обновление количества элементов в уроках обучения */
    public function updateAdminCountSchoolLessons(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolLessonsPerPage', 'site_settings.adminSchoolLessonsPerPage');
    }

    /** Обновление количества элементов в заданиях */
    public function updateAdminCountSchoolAssignments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolAssignmentsPerPage', 'site_settings.adminSchoolAssignmentsPerPage');
    }

    /** Обновление количества элементов в расписании потоков */
    public function updateAdminCountSchoolCourseSchedules(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolCourseSchedulesPerPage', 'site_settings.adminSchoolCourseSchedulesPerPage');
    }

    /** Обновление количества элементов в записях на потоки */
    public function updateAdminCountSchoolCohortEnrollments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolCohortEnrollmentsPerPage', 'site_settings.adminSchoolCohortEnrollmentsPerPage');
    }

    /** Обновление количества элементов в зачислениях на потоки */
    public function updateAdminCountSchoolEnrollments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolEnrollmentsPerPage', 'site_settings.adminSchoolEnrollmentsPerPage');
    }

    /** Обновление количества элементов в квизах */
    public function updateAdminCountSchoolQuizzes(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizzesPerPage', 'site_settings.adminSchoolQuizzesPerPage');
    }

    /** Обновление количества элементов в вопросах квиза */
    public function updateAdminCountSchoolQuizQuestions(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizQuestionsPerPage', 'site_settings.adminSchoolQuizQuestionsPerPage');
    }

    /** Обновление количества элементов в ответах квиза */
    public function updateAdminCountSchoolQuizAnswers(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizAnswersPerPage', 'site_settings.adminSchoolQuizAnswersPerPage');
    }

    /** Обновление количества элементов в прохождениях квиза */
    public function updateAdminCountSchoolQuizAttempts(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizAttemptsPerPage', 'site_settings.adminSchoolQuizAttemptsPerPage');
    }

    /** Обновление количества элементов в попытках ответа квиза */
    public function updateAdminCountSchoolQuizAttemptItems(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizAttemptItemsPerPage', 'site_settings.adminSchoolQuizAttemptItemsPerPage');
    }

    /** Обновление количества элементов в бандлах */
    public function updateAdminCountSchoolBundles(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolBundlesPerPage', 'site_settings.adminSchoolBundlesPerPage');
    }

    /** Обновление количества элементов в заказах школы */
    public function updateAdminCountSchoolOrders(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolOrdersPerPage', 'site_settings.adminSchoolOrdersPerPage');
    }

    /** Обновление количества элементов в прайсах курсов */
    public function updateAdminCountSchoolCoursePrices(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolCoursePricesPerPage', 'site_settings.adminSchoolCoursePricesPerPage');
    }

    /** Обновление количества элементов в прайсах наборов курсов */
    public function updateAdminCountSchoolBundlePrices(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolBundlePricesPerPage', 'site_settings.adminSchoolBundlePricesPerPage');
    }

    /** Обновление количества элементов в тарифных планах */
    public function updateAdminCountSchoolSubscriptionPlans(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolSubscriptionPlansPerPage', 'site_settings.adminSchoolSubscriptionPlansPerPage');
    }

    /** Обновление количества элементов в компаниях */
    public function updateAdminCountMarketCompanies(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketCompaniesPerPage', 'site_settings.adminMarketCompaniesPerPage');
    }

    /** Обновление количества элементов в магазинах */
    public function updateAdminCountMarketShops(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketShopsPerPage', 'site_settings.adminMarketShopsPerPage');
    }

    /** Обновление количества элементов в категориях товаров */
    public function updateAdminCountMarketCategories(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketCategoriesPerPage', 'site_settings.adminMarketCategoriesPerPage');
    }

    /** Обновление количества элементов в брендах */
    public function updateAdminCountMarketBrands(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketBrandsPerPage', 'site_settings.adminMarketBrandsPerPage');
    }

    /** Обновление количества элементов в тегах товаров */
    public function updateAdminCountMarketTags(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketTagsPerPage', 'site_settings.adminMarketTagsPerPage');
    }

    /** Обновление количества элементов в группах характеристик */
    public function updateAdminCountMarketAttributeGroups(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketAttributeGroupsPerPage', 'site_settings.adminMarketAttributeGroupsPerPage');
    }

    /** Обновление количества элементов в характеристиках */
    public function updateAdminCountMarketAttributes(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketAttributesPerPage', 'site_settings.adminMarketAttributesPerPage');
    }

    /** Обновление количества элементов в значениях характеристик */
    public function updateAdminCountMarketAttributeValues(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketAttributeValuesPerPage', 'site_settings.adminMarketAttributeValuesPerPage');
    }
}
