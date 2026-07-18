<?php

namespace App\Traits\Admin\Settings;

use App\Http\Requests\Admin\System\UpdateSortRequest;
use Illuminate\Http\RedirectResponse;

trait SortSettingsTrait
{
    // Публичные методы для сортировки (принимают общий UpdateSortRequest)

    /**
     * Тип значения для всех sort-настроек.
     */
    protected string $sortSettingType = 'string';

    /**
     * Категория всех sort-настроек.
     */
    protected string $sortSettingCategory = 'admin';

    /**
     * Сообщение об успешном обновлении сортировки.
     */
    private string $sortSuccessMessage = 'Сортировка по умолчанию успешно обновлена.';

    /**
     * Сообщение об ошибке обновления сортировки.
     */
    private string $sortErrorMessage = 'Ошибка обновления настройки сортировки.';

    /**
     * Универсальный обработчик для обновления sort-настроек.
     *
     * @param UpdateSortRequest $request
     * @param string $optionKey Ключ опции в таблице settings
     * @param string $configKey Ключ в конфигурации site_settings
     * @return RedirectResponse
     */
    protected function sortSetting(
        UpdateSortRequest $request,
        string $optionKey,
        string $configKey
    ): RedirectResponse {
        return $this->updateSettingAndRedirect(
            $request,
            $optionKey,
            $configKey,
            $this->sortSettingType,
            $this->sortSettingCategory,
            $this->sortSuccessMessage,
            $this->sortErrorMessage
        );
    }

    /** Обновляет сортировку элементов в настройках */
    public function updateAdminSortSettings(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSystemSettingsDefaultSort', 'site_settings.adminSystemSettingsDefaultSort');
    }

    /** Обновляет сортировку элементов в вариантах обработки изображений */
    public function updateAdminSortImagePresets(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminImagePresetsDefaultSort', 'site_settings.adminImagePresetsDefaultSort');
    }

    /** Обновляет сортировку элементов в пользователях */
    public function updateAdminSortUsers(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSystemUsersDefaultSort', 'site_settings.adminSystemUsersDefaultSort');
    }

    /** Обновляет сортировку элементов в ролях */
    public function updateAdminSortRoles(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSystemRolesDefaultSort', 'site_settings.adminSystemRolesDefaultSort');
    }

    /** Обновляет сортировку элементов в разрешениях */
    public function updateAdminSortPermissions(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSystemPermissionsDefaultSort', 'site_settings.adminSystemPermissionsDefaultSort');
    }

    /** Обновляет сортировку элементов в валютах */
    public function updateAdminSortCurrencies(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminFinanceCurrenciesDefaultSort', 'site_settings.adminFinanceCurrenciesDefaultSort');
    }

    /** Обновляет сортировку элементов в комментариях */
    public function updateAdminSortComments(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminCommentsDefaultSort', 'site_settings.adminCommentsDefaultSort');
    }

    /** Обновляет сортировку элементов в отзывах */
    public function updateAdminSortReviews(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminReviewsDefaultSort', 'site_settings.adminReviewsDefaultSort');
    }

    /** Обновляет сортировку элементов в рубриках */
    public function updateAdminSortBlogRubrics(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminBlogRubricsDefaultSort', 'site_settings.adminBlogRubricsDefaultSort');
    }

    /** Обновляет сортировку элементов в статьях */
    public function updateAdminSortBlogArticles(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminBlogArticlesDefaultSort', 'site_settings.adminBlogArticlesDefaultSort');
    }

    /** Обновляет сортировку элементов в тегах */
    public function updateAdminSortBlogTags(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminBlogTagsDefaultSort', 'site_settings.adminBlogTagsDefaultSort');
    }

    /** Обновляет сортировку элементов в баннерах */
    public function updateAdminSortBlogBanners(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminBlogBannersDefaultSort', 'site_settings.adminBlogBannersDefaultSort');
    }

    /** Обновляет сортировку элементов в видео */
    public function updateAdminSortBlogVideos(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminBlogVideosDefaultSort', 'site_settings.adminBlogVideosDefaultSort');
    }

    /** Обновляет сортировку элементов в категориях курсов */
    public function updateAdminSortSchoolHashtags(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolHashtagsDefaultSort', 'site_settings.adminSchoolHashtagsDefaultSort');
    }

    /** Обновляет сортировку элементов в преподавателях */
    public function updateAdminSortSchoolInstructors(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolInstructorsDefaultSort', 'site_settings.adminSchoolInstructorsDefaultSort');
    }

    /** Обновляет сортировку элементов в категориях курсов */
    public function updateAdminSortSchoolTracks(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolTracksDefaultSort', 'site_settings.adminSchoolTracksDefaultSort');
    }

    /** Обновляет сортировку элементов в курсах */
    public function updateAdminSortSchoolCourses(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolCoursesDefaultSort', 'site_settings.adminSchoolCoursesDefaultSort');
    }

    /** Обновляет сортировку элементов в модулях обучения */
    public function updateAdminSortSchoolModules(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolModulesDefaultSort', 'site_settings.adminSchoolModulesDefaultSort');
    }

    /** Обновляет сортировку элементов в уроках */
    public function updateAdminSortSchoolLessons(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolLessonsDefaultSort', 'site_settings.adminSchoolLessonsDefaultSort');
    }

    /** Обновляет сортировку элементов в заданиях */
    public function updateAdminSortSchoolAssignments(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolAssignmentsDefaultSort', 'site_settings.adminSchoolAssignmentsDefaultSort');
    }

    /** Обновляет сортировку элементов в расписании потоков */
    public function updateAdminSortSchoolCourseSchedules(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolCourseSchedulesDefaultSort', 'site_settings.adminSchoolCourseSchedulesDefaultSort');
    }

    /** Обновляет сортировку элементов в записях на потоки */
    public function updateAdminSortSchoolCohortEnrollments(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolCohortEnrollmentsDefaultSort', 'site_settings.adminSchoolCohortEnrollmentsDefaultSort');
    }

    /** Обновляет сортировку элементов в зачислениях на потоки */
    public function updateAdminSortSchoolEnrollments(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolEnrollmentsDefaultSort', 'site_settings.adminSchoolEnrollmentsDefaultSort');
    }

    /** Обновляет сортировку элементов в квизах */
    public function updateAdminSortSchoolQuizzes(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolQuizzesDefaultSort', 'site_settings.adminSchoolQuizzesDefaultSort');
    }

    /** Обновляет сортировку элементов в вопросах квиза */
    public function updateAdminSortSchoolQuizQuestions(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolQuizQuestionsDefaultSort', 'site_settings.adminSchoolQuizQuestionsDefaultSort');
    }

    /** Обновляет сортировку элементов в ответах квиза */
    public function updateAdminSortSchoolQuizAnswers(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolQuizAnswersDefaultSort', 'site_settings.adminSchoolQuizAnswersDefaultSort');
    }

    /** Обновляет сортировку элементов в прохождениях квиза */
    public function updateAdminSortSchoolQuizAttempts(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolQuizAttemptsDefaultSort', 'site_settings.adminSchoolQuizAttemptsDefaultSort');
    }

    /** Обновляет сортировку элементов в попытках ответа квиза */
    public function updateAdminSortSchoolQuizAttemptItems(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolQuizAttemptItemsDefaultSort', 'site_settings.adminSchoolQuizAttemptItemsDefaultSort');
    }

    /** Обновляет сортировку элементов в бандлах */
    public function updateAdminSortSchoolBundles(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolBundlesDefaultSort', 'site_settings.adminSchoolBundlesDefaultSort');
    }

    /** Обновляет сортировку элементов в заказах школы */
    public function updateAdminSortSchoolOrders(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolOrdersDefaultSort', 'site_settings.adminSchoolOrdersDefaultSort');
    }

    /** Обновляет сортировку элементов в прайсах курсов */
    public function updateAdminSortSchoolCoursePrices(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolCoursePricesDefaultSort', 'site_settings.adminSchoolCoursePricesDefaultSort');
    }

    /** Обновляет сортировку элементов в прайсах наборов курсов */
    public function updateAdminSortSchoolBundlePrices(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolBundlePricesDefaultSort', 'site_settings.adminSchoolBundlePricesDefaultSort');
    }

    /** Обновляет сортировку элементов в тарифных планах */
    public function updateAdminSortSchoolSubscriptionPlans(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolSubscriptionPlansDefaultSort', 'site_settings.adminSchoolSubscriptionPlansDefaultSort');
    }

    /** Обновляет сортировку элементов в CMS страниц */
    public function updateAdminSortCmsPages(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminCmsPagesDefaultSort', 'site_settings.adminCmsPagesDefaultSort');
    }

    /** Обновляет сортировку элементов в компаниях */
    public function updateAdminSortMarketCompanies(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketCompaniesDefaultSort', 'site_settings.adminMarketCompaniesDefaultSort');
    }

    /** Обновляет сортировку элементов в магазинах */
    public function updateAdminSortMarketShops(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketShopsDefaultSort', 'site_settings.adminMarketShopsDefaultSort');
    }

    /** Обновляет сортировку элементов в категориях товаров */
    public function updateAdminSortMarketCategories(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketCategoriesDefaultSort', 'site_settings.adminMarketCategoriesDefaultSort');
    }

    /** Обновляет сортировку элементов в товарах */
    public function updateAdminSortMarketProducts(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketProductsDefaultSort', 'site_settings.adminMarketProductsDefaultSort');
    }

    /** Обновляет сортировку элементов в брендах */
    public function updateAdminSortMarketBrands(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketBrandsDefaultSort', 'site_settings.adminMarketBrandsDefaultSort');
    }

    /** Обновляет сортировку элементов в тегах товаров */
    public function updateAdminSortMarketTags(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketTagsDefaultSort', 'site_settings.adminMarketTagsDefaultSort');
    }

    /** Обновляет сортировку элементов в группах характеристик */
    public function updateAdminSortMarketAttributeGroups(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketAttributeGroupsDefaultSort', 'site_settings.adminMarketAttributeGroupsDefaultSort');
    }

    /** Обновляет сортировку элементов в характеристиках */
    public function updateAdminSortMarketAttributes(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketAttributesDefaultSort', 'site_settings.adminMarketAttributesDefaultSort');
    }

    /** Обновляет сортировку элементов в значениях характеристик */
    public function updateAdminSortMarketAttributeValues(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketAttributeValuesDefaultSort', 'site_settings.adminMarketAttributeValuesDefaultSort');
    }
}
