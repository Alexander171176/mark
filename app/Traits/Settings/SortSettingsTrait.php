<?php

namespace App\Traits\Settings;

use App\Http\Requests\Admin\System\UpdateSortRequest;
use Illuminate\Http\RedirectResponse;

trait SortSettingsTrait
{
    // Публичные методы для сортировки (принимают общий UpdateSortRequest)

    /**
     * Тип значения для всех sort-настроек.
     */
    private string $sortSettingType = 'string';

    /**
     * Категория всех sort-настроек.
     */
    private string $sortSettingCategory = 'admin';

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
    private function sortSetting(
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

    /**
     * Обновляет сортировку элементов в настройках
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortSettings(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortSettings', 'site_settings.AdminSortSettings');
    }

    /**
     * Обновляет сортировку элементов в категориях
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortCategories(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortCategories', 'site_settings.AdminSortCategories');
    }

    /**
     * Обновляет сортировку элементов в рубриках
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortRubrics(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortRubrics', 'site_settings.AdminSortRubrics');
    }

    /**
     * Обновляет сортировку элементов в статьях
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortArticles(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortArticles', 'site_settings.AdminSortArticles');
    }

    /**
     * Обновляет сортировку элементов в тегах
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortTags(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortTags', 'site_settings.AdminSortTags');
    }

    /**
     * Обновляет сортировку элементов в комментариях
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortComments(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortComments', 'site_settings.AdminSortComments');
    }

    /**
     * Обновляет сортировку элементов в баннерах
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortBanners(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortBanners', 'site_settings.AdminSortBanners');
    }

    /**
     * Обновляет сортировку элементов в видео
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortVideos(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortVideos', 'site_settings.AdminSortVideos');
    }

    /**
     * Обновляет сортировку элементов в преподавателях
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortInstructors(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortInstructors', 'site_settings.AdminSortInstructors');
    }

    /**
     * Обновляет сортировку элементов в категориях курсов
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortLearningCategories(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortLearningCategories', 'site_settings.AdminSortLearningCategories');
    }

    /**
     * Обновляет сортировку элементов в категориях курсов
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortHashtags(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortHashtags', 'site_settings.AdminSortHashtags');
    }

    /**
     * Обновляет сортировку элементов в курсах
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortLearningCourses(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortLearningCourses', 'site_settings.AdminSortLearningCourses');
    }

    /**
     * Обновляет сортировку элементов в модулях обучения
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortLearningModules(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortLearningModules', 'site_settings.AdminSortLearningModules');
    }

    /**
     * Обновляет сортировку элементов в уроках
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortLearningLessons(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortLearningLessons', 'site_settings.AdminSortLearningLessons');
    }

    /**
     * Обновляет сортировку элементов в заданиях
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortAssignments(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortAssignments', 'site_settings.AdminSortAssignments');
    }

    /**
     * Обновляет сортировку элементов в расписании потоков
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortCourseSchedules(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortCourseSchedules', 'site_settings.AdminSortCourseSchedules');
    }

    /**
     * Обновляет сортировку элементов в записях на потоки
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortCohortEnrollments(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortCohortEnrollments', 'site_settings.AdminSortCohortEnrollments');
    }

    /**
     * Обновляет сортировку элементов в зачислениях на потоки
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortEnrollments(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortEnrollments', 'site_settings.AdminSortEnrollments');
    }

    /**
     * Обновляет сортировку элементов в квизах
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortQuizzes(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortQuizzes', 'site_settings.AdminSortQuizzes');
    }

    /**
     * Обновляет сортировку элементов в вопросах квиза
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortQuizQuestions(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortQuizQuestions', 'site_settings.AdminSortQuizQuestions');
    }

    /**
     * Обновляет сортировку элементов в ответах квиза
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortQuizAnswers(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortQuizAnswers', 'site_settings.AdminSortQuizAnswers');
    }

    /**
     * Обновляет сортировку элементов в прохождениях квиза
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortQuizAttempts(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortQuizAttempts', 'site_settings.AdminSortQuizAttempts');
    }

    /**
     * Обновляет сортировку элементов в ответах квиза
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortQuizAttemptItems(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortQuizAttemptItems', 'site_settings.AdminSortQuizAttemptItems');
    }

    /**
     * Обновляет сортировку элементов в бандлах
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortBundles(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortBundles', 'site_settings.AdminSortBundles');
    }

    /**
     * Обновляет сортировку элементов в валютах
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortCurrencies(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortCurrencies', 'site_settings.AdminSortCurrencies');
    }

    /**
     * Обновляет сортировку элементов в заказах
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortOrders(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortOrders', 'site_settings.AdminSortOrders');
    }

    /**
     * Обновляет сортировку элементов в прайсах курсов
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortCoursePrices(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortCoursePrices', 'site_settings.AdminSortCoursePrices');
    }

    /**
     * Обновляет сортировку элементов в прайсах наборов курсов
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortBundlePrices(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortBundlePrices', 'site_settings.AdminSortBundlePrices');
    }

    /**
     * Обновляет сортировку элементов в тарифных планах
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortSubscriptionPlans(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortSubscriptionPlans', 'site_settings.AdminSortSubscriptionPlans');
    }

    /**
     * Обновляет сортировку элементов в пользователях
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortUsers(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortUsers', 'site_settings.AdminSortUsers');
    }

    /**
     * Обновляет сортировку элементов в ролях
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortRoles(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortRoles', 'site_settings.AdminSortRoles');
    }

    /**
     * Обновляет сортировку элементов в разрешениях
     *
     * @param UpdateSortRequest $request
     * @return RedirectResponse
     */
    public function updateAdminSortPermissions(UpdateSortRequest $request): RedirectResponse
    {
        return $this->sortSetting($request,
            'AdminSortPermissions', 'site_settings.AdminSortPermissions');
    }
}
