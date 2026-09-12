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
     * @return RedirectResponse
     */
    protected function sortSetting(
        UpdateSortRequest $request,
        string $optionKey
    ): RedirectResponse {
        return $this->updateSettingAndRedirect(
            $request,
            $optionKey,
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
            'adminSystemSettingsDefaultSort');
    }

    /** Обновляет сортировку элементов в вариантах обработки изображений */
    public function updateAdminSortImagePresets(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminImagePresetsDefaultSort');
    }

    /** Обновляет сортировку элементов в локациях */
    public function updateAdminSortLocations(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSystemLocationsDefaultSort');
    }

    /** Обновляет сортировку элементов в пользователях */
    public function updateAdminSortUsers(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSystemUsersDefaultSort');
    }

    /** Обновляет сортировку элементов в ролях */
    public function updateAdminSortRoles(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSystemRolesDefaultSort');
    }

    /** Обновляет сортировку элементов в разрешениях */
    public function updateAdminSortPermissions(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSystemPermissionsDefaultSort');
    }

    /** Обновляет сортировку элементов в валютах */
    public function updateAdminSortCurrencies(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminFinanceCurrenciesDefaultSort');
    }

    /** Обновляет сортировку элементов в комментариях */
    public function updateAdminSortComments(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminCommentsDefaultSort');
    }

    /** Обновляет сортировку элементов в отзывах */
    public function updateAdminSortReviews(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminReviewsDefaultSort');
    }

    /** Обновляет сортировку элементов в рубриках */
    public function updateAdminSortBlogRubrics(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminBlogRubricsDefaultSort');
    }

    /** Обновляет сортировку элементов в статьях */
    public function updateAdminSortBlogArticles(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminBlogArticlesDefaultSort');
    }

    /** Обновляет сортировку элементов в тегах */
    public function updateAdminSortBlogTags(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminBlogTagsDefaultSort');
    }

    /** Обновляет сортировку элементов в баннерах */
    public function updateAdminSortBlogBanners(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminBlogBannersDefaultSort');
    }

    /** Обновляет сортировку элементов в видео */
    public function updateAdminSortBlogVideos(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminBlogVideosDefaultSort');
    }

    /** Обновляет сортировку элементов в категориях курсов */
    public function updateAdminSortSchoolHashtags(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolHashtagsDefaultSort');
    }

    /** Обновляет сортировку элементов в преподавателях */
    public function updateAdminSortSchoolInstructors(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolInstructorsDefaultSort');
    }

    /** Обновляет сортировку элементов в категориях курсов */
    public function updateAdminSortSchoolTracks(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolTracksDefaultSort');
    }

    /** Обновляет сортировку элементов в курсах */
    public function updateAdminSortSchoolCourses(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolCoursesDefaultSort');
    }

    /** Обновляет сортировку элементов в модулях обучения */
    public function updateAdminSortSchoolModules(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolModulesDefaultSort');
    }

    /** Обновляет сортировку элементов в уроках */
    public function updateAdminSortSchoolLessons(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolLessonsDefaultSort');
    }

    /** Обновляет сортировку элементов в заданиях */
    public function updateAdminSortSchoolAssignments(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolAssignmentsDefaultSort');
    }

    /** Обновляет сортировку элементов в расписании потоков */
    public function updateAdminSortSchoolCourseSchedules(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolCourseSchedulesDefaultSort');
    }

    /** Обновляет сортировку элементов в записях на потоки */
    public function updateAdminSortSchoolCohortEnrollments(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolCohortEnrollmentsDefaultSort');
    }

    /** Обновляет сортировку элементов в зачислениях на потоки */
    public function updateAdminSortSchoolEnrollments(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolEnrollmentsDefaultSort');
    }

    /** Обновляет сортировку элементов в квизах */
    public function updateAdminSortSchoolQuizzes(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolQuizzesDefaultSort');
    }

    /** Обновляет сортировку элементов в вопросах квиза */
    public function updateAdminSortSchoolQuizQuestions(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolQuizQuestionsDefaultSort');
    }

    /** Обновляет сортировку элементов в ответах квиза */
    public function updateAdminSortSchoolQuizAnswers(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolQuizAnswersDefaultSort');
    }

    /** Обновляет сортировку элементов в прохождениях квиза */
    public function updateAdminSortSchoolQuizAttempts(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolQuizAttemptsDefaultSort');
    }

    /** Обновляет сортировку элементов в попытках ответа квиза */
    public function updateAdminSortSchoolQuizAttemptItems(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolQuizAttemptItemsDefaultSort');
    }

    /** Обновляет сортировку элементов в бандлах */
    public function updateAdminSortSchoolBundles(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolBundlesDefaultSort');
    }

    /** Обновляет сортировку элементов в заказах школы */
    public function updateAdminSortSchoolOrders(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolOrdersDefaultSort');
    }

    /** Обновляет сортировку элементов в прайсах курсов */
    public function updateAdminSortSchoolCoursePrices(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolCoursePricesDefaultSort');
    }

    /** Обновляет сортировку элементов в прайсах наборов курсов */
    public function updateAdminSortSchoolBundlePrices(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolBundlePricesDefaultSort');
    }

    /** Обновляет сортировку элементов в тарифных планах */
    public function updateAdminSortSchoolSubscriptionPlans(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminSchoolSubscriptionPlansDefaultSort');
    }

    /** Обновляет сортировку элементов в CMS страниц */
    public function updateAdminSortCmsPages(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminCmsPagesDefaultSort');
    }

    /** Обновляет сортировку элементов в компаниях */
    public function updateAdminSortMarketCompanies(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketCompaniesDefaultSort');
    }

    /** Обновляет сортировку элементов в магазинах */
    public function updateAdminSortMarketShops(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketShopsDefaultSort');
    }

    /** Обновляет сортировку элементов в категориях товаров */
    public function updateAdminSortMarketCategories(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketCategoriesDefaultSort');
    }

    /** Обновляет сортировку элементов в товарах */
    public function updateAdminSortMarketProducts(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketProductsDefaultSort');
    }

    /** Обновляет сортировку элементов в комплектах товаров */
    public function updateAdminSortMarketProductBundles(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketProductBundlesDefaultSort');
    }

    /** Обновляет сортировку элементов в вариантах товаров */
    public function updateAdminSortMarketProductVariants(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketProductVariantsDefaultSort');
    }

    /** Обновляет сортировку элементов в брендах */
    public function updateAdminSortMarketBrands(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketBrandsDefaultSort');
    }

    /** Обновляет сортировку элементов в тегах товаров */
    public function updateAdminSortMarketTags(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketTagsDefaultSort');
    }

    /** Обновляет сортировку элементов в группах характеристик */
    public function updateAdminSortMarketAttributeGroups(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketAttributeGroupsDefaultSort');
    }

    /** Обновляет сортировку элементов в характеристиках */
    public function updateAdminSortMarketAttributes(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketAttributesDefaultSort');
    }

    /** Обновляет сортировку элементов в значениях характеристик */
    public function updateAdminSortMarketAttributeValues(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'adminMarketAttributeValuesDefaultSort');
    }
}
