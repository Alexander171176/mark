<?php

namespace App\Traits\Settings;

use App\Http\Requests\Admin\System\UpdateCountSettingRequest;
use Illuminate\Http\RedirectResponse;

trait CountSettingsTrait
{
    /**
     * Тип значения для всех count-настроек.
     */
    private string $countSettingType = 'number';

    /**
     * Категория всех count-настроек.
     */
    private string $countSettingCategory = 'admin';

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
    private function countSetting(
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

    /**
     * Обновление количества элементов в настройках
     */
    public function updateAdminCountSettings(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountSettings', 'site_settings.AdminCountSettings');
    }

    /**
     * Обновление количества элементов в категориях
     */
    public function updateAdminCountCategories(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountCategories', 'site_settings.AdminCountCategories');
    }

    /**
     * Обновление количества элементов в рубриках
     */
    public function updateAdminCountRubrics(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountRubrics', 'site_settings.AdminCountRubrics');
    }

    /**
     * Обновление количества элементов в статьях
     */
    public function updateAdminCountArticles(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountArticles', 'site_settings.AdminCountArticles');
    }

    /**
     * Обновление количества элементов в тегах
     */
    public function updateAdminCountTags(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountTags', 'site_settings.AdminCountTags');
    }

    /**
     * Обновление количества элементов в комментариях
     */
    public function updateAdminCountComments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountComments', 'site_settings.AdminCountComments');
    }

    /**
     * Обновление количества элементов в баннерах
     */
    public function updateAdminCountBanners(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountBanners', 'site_settings.AdminCountBanners');
    }

    /**
     * Обновление количества элементов в видео
     */
    public function updateAdminCountVideos(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountVideos', 'site_settings.AdminCountVideos');
    }

    /**
     * Обновление количества элементов в преподователях
     */
    public function updateAdminCountInstructors(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountInstructors', 'site_settings.AdminCountInstructors');
    }

    /**
     * Обновление количества элементов в категориях курсов
     */
    public function updateAdminCountLearningCategories(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountLearningCategories', 'site_settings.AdminCountLearningCategories');
    }

    /**
     * Обновление количества элементов в тегах обучения
     */
    public function updateAdminCountHashtags(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountHashtags', 'site_settings.AdminCountHashtags');
    }

    /**
     * Обновление количества элементов в курсах
     */
    public function updateAdminCountLearningCourses(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountLearningCourses', 'site_settings.AdminCountLearningCourses');
    }

    /**
     * Обновление количества элементов в модулях обучения
     */
    public function updateAdminCountLearningModules(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountLearningModules', 'site_settings.AdminCountLearningModules');
    }

    /**
     * Обновление количества элементов в уроках обучения
     */
    public function updateAdminCountLearningLessons(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountLearningLessons', 'site_settings.AdminCountLearningLessons');
    }

    /**
     * Обновление количества элементов в заданиях
     */
    public function updateAdminCountAssignments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountAssignments', 'site_settings.AdminCountAssignments');
    }

    /**
     * Обновление количества элементов в расписании потоков
     */
    public function updateAdminCountCourseSchedules(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountCourseSchedules', 'site_settings.AdminCountCourseSchedules');
    }

    /**
     * Обновление количества элементов в записях на потоки
     */
    public function updateAdminCountCohortEnrollments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountCohortEnrollments', 'site_settings.AdminCountCohortEnrollments');
    }

    /**
     * Обновление количества элементов в зачислениях на потоки
     */
    public function updateAdminCountEnrollments(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountEnrollments', 'site_settings.AdminCountEnrollments');
    }

    /**
     * Обновление количества элементов в квизах
     */
    public function updateAdminCountQuizzes(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountQuizzes', 'site_settings.AdminCountQuizzes');
    }

    /**
     * Обновление количества элементов в вопросах квиза
     */
    public function updateAdminCountQuizQuestions(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountQuizQuestions', 'site_settings.AdminCountQuizQuestions');
    }

    /**
     * Обновление количества элементов в ответах квиза
     */
    public function updateAdminCountQuizAnswers(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountQuizAnswers', 'site_settings.AdminCountQuizAnswers');
    }

    /**
     * Обновление количества элементов в прохождениях квиза
     */
    public function updateAdminCountQuizAttempts(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountQuizAttempts', 'site_settings.AdminCountQuizAttempts');
    }

    /**
     * Обновление количества элементов в ответах на квизы
     */
    public function updateAdminCountQuizAttemptItems(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountQuizAttemptItems', 'site_settings.AdminCountQuizAttemptItems');
    }

    /**
     * Обновление количества элементов в бандлах
     */
    public function updateAdminCountBundles(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountBundles', 'site_settings.AdminCountBundles');
    }

    /**
     * Обновление количества элементов в валютах
     */
    public function updateAdminCountCurrencies(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountCurrencies', 'site_settings.AdminCountCurrencies');
    }

    /**
     * Обновление количества элементов в заказах
     */
    public function updateAdminCountOrders(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountOrders', 'site_settings.AdminCountOrders');
    }

    /**
     * Обновление количества элементов в прайсах курсов
     */
    public function updateAdminCountCoursePrices(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountCoursePrices', 'site_settings.AdminCountCoursePrices');
    }

    /**
     * Обновление количества элементов в прайсах наборов курсов
     */
    public function updateAdminCountBundlePrices(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountBundlePrices', 'site_settings.AdminCountBundlePrices');
    }

    /**
     * Обновление количества элементов в тарифных планах
     */
    public function updateAdminCountSubscriptionPlans(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountSubscriptionPlans', 'site_settings.AdminCountSubscriptionPlans');
    }

    /**
     * Обновление количества элементов в пользователях
     */
    public function updateAdminCountUsers(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountUsers', 'site_settings.AdminCountUsers');
    }

    /**
     * Обновление количества элементов в ролях
     */
    public function updateAdminCountRoles(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountRoles', 'site_settings.AdminCountRoles');
    }

    /**
     * Обновление количества элементов в разрешениях
     */
    public function updateAdminCountPermissions(UpdateCountSettingRequest $request): RedirectResponse
    {
        return $this->countSetting($request,
            'AdminCountPermissions', 'site_settings.AdminCountPermissions');
    }
}
