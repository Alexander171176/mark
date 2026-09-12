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
     * @return RedirectResponse
     */
    protected function countSetting(
        UpdateCountSettingRequest $request,
        string $optionKey
    ): RedirectResponse {
        return $this->updateSettingAndRedirect(
            $request,
            $optionKey,
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
            'adminSystemSettingsPerPage');
    }

    /** Обновление количества элементов в вариантах обработки изображений */
    public function updateAdminCountImagePresets(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminImagePresetsPerPage');
    }

    /** Обновление количества элементов в локациях */
    public function updateAdminCountLocations(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSystemLocationsPerPage');
    }

    /** Обновление количества элементов в пользователях */
    public function updateAdminCountUsers(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSystemUsersPerPage');
    }

    /** Обновление количества элементов в ролях */
    public function updateAdminCountRoles(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSystemRolesPerPage');
    }

    /** Обновление количества элементов в разрешениях */
    public function updateAdminCountPermissions(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSystemPermissionsPerPage');
    }

    /** Обновление количества элементов в валютах */
    public function updateAdminCountCurrencies(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminFinanceCurrenciesPerPage');
    }

    /** Обновление количества элементов в комментариях */
    public function updateAdminCountComments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminCommentsPerPage');
    }

    /** Обновление количества элементов в отзывах */
    public function updateAdminCountReviews(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminReviewsPerPage');
    }

    /** Обновление количества элементов в рубриках */
    public function updateAdminCountBlogRubrics(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogRubricsPerPage');
    }

    /** Обновление количества элементов в статьях */
    public function updateAdminCountBlogArticles(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogArticlesPerPage');
    }

    /** Обновление количества элементов в тегах */
    public function updateAdminCountBlogTags(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogTagsPerPage');
    }

    /** Обновление количества элементов в баннерах */
    public function updateAdminCountBlogBanners(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogBannersPerPage');
    }

    /** Обновление количества элементов в видео */
    public function updateAdminCountBlogVideos(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminBlogVideosPerPage');
    }

    /** Обновление количества элементов в тегах обучения */
    public function updateAdminCountSchoolHashtags(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolHashtagsPerPage');
    }

    /** Обновление количества элементов в преподователях */
    public function updateAdminCountSchoolInstructors(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolInstructorsPerPage');
    }

    /** Обновление количества элементов в категориях курсов */
    public function updateAdminCountSchoolTracks(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolTracksPerPage');
    }

    /** Обновление количества элементов в курсах */
    public function updateAdminCountSchoolCourses(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolCoursesPerPage');
    }

    /** Обновление количества элементов в модулях обучения */
    public function updateAdminCountSchoolModules(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolModulesPerPage');
    }

    /** Обновление количества элементов в уроках обучения */
    public function updateAdminCountSchoolLessons(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolLessonsPerPage');
    }

    /** Обновление количества элементов в заданиях */
    public function updateAdminCountSchoolAssignments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolAssignmentsPerPage');
    }

    /** Обновление количества элементов в расписании потоков */
    public function updateAdminCountSchoolCourseSchedules(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolCourseSchedulesPerPage');
    }

    /** Обновление количества элементов в записях на потоки */
    public function updateAdminCountSchoolCohortEnrollments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolCohortEnrollmentsPerPage');
    }

    /** Обновление количества элементов в зачислениях на потоки */
    public function updateAdminCountSchoolEnrollments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolEnrollmentsPerPage');
    }

    /** Обновление количества элементов в квизах */
    public function updateAdminCountSchoolQuizzes(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizzesPerPage');
    }

    /** Обновление количества элементов в вопросах квиза */
    public function updateAdminCountSchoolQuizQuestions(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizQuestionsPerPage');
    }

    /** Обновление количества элементов в ответах квиза */
    public function updateAdminCountSchoolQuizAnswers(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizAnswersPerPage');
    }

    /** Обновление количества элементов в прохождениях квиза */
    public function updateAdminCountSchoolQuizAttempts(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizAttemptsPerPage');
    }

    /** Обновление количества элементов в попытках ответа квиза */
    public function updateAdminCountSchoolQuizAttemptItems(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolQuizAttemptItemsPerPage');
    }

    /** Обновление количества элементов в бандлах */
    public function updateAdminCountSchoolBundles(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolBundlesPerPage');
    }

    /** Обновление количества элементов в заказах школы */
    public function updateAdminCountSchoolOrders(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolOrdersPerPage');
    }

    /** Обновление количества элементов в прайсах курсов */
    public function updateAdminCountSchoolCoursePrices(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolCoursePricesPerPage');
    }

    /** Обновление количества элементов в прайсах наборов курсов */
    public function updateAdminCountSchoolBundlePrices(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolBundlePricesPerPage');
    }

    /** Обновление количества элементов в тарифных планах */
    public function updateAdminCountSchoolSubscriptionPlans(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminSchoolSubscriptionPlansPerPage');
    }

    /** Обновление количества элементов в CMS страниц */
    public function updateAdminCountCmsPages(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminCmsPagesPerPage');
    }

    /** Обновление количества элементов в компаниях */
    public function updateAdminCountMarketCompanies(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketCompaniesPerPage');
    }

    /** Обновление количества элементов в магазинах */
    public function updateAdminCountMarketShops(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketShopsPerPage');
    }

    /** Обновление количества элементов в категориях товаров */
    public function updateAdminCountMarketCategories(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketCategoriesPerPage');
    }

    /** Обновление количества элементов в товарах */
    public function updateAdminCountMarketProducts(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketProductsPerPage');
    }

    /** Обновление количества элементов в комплектах товаров */
    public function updateAdminCountMarketProductBundles(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketProductBundlesPerPage');
    }

    /** Обновление количества элементов в вариантах товаров */
    public function updateAdminCountMarketProductVariants(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketProductVariantsPerPage');
    }

    /** Обновление количества элементов в брендах */
    public function updateAdminCountMarketBrands(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketBrandsPerPage');
    }

    /** Обновление количества элементов в тегах товаров */
    public function updateAdminCountMarketTags(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketTagsPerPage');
    }

    /** Обновление количества элементов в группах характеристик */
    public function updateAdminCountMarketAttributeGroups(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketAttributeGroupsPerPage');
    }

    /** Обновление количества элементов в характеристиках */
    public function updateAdminCountMarketAttributes(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketAttributesPerPage');
    }

    /** Обновление количества элементов в значениях характеристик */
    public function updateAdminCountMarketAttributeValues(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'adminMarketAttributeValuesPerPage');
    }
}
