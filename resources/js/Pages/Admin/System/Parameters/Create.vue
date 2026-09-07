<script setup>
/**
 * @version PulsarCMS 1.0
 * @author Александр Косолапов <kosolapov1976@gmail.com>
 */

import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
import { useForm } from '@inertiajs/vue3'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import TitlePage from '@/Components/Admin/UI/Headlines/TitlePage.vue'
import DefaultButton from '@/Components/Admin/UI/Buttons/DefaultButton.vue'
import PrimaryButton from '@/Components/Admin/UI/Buttons/PrimaryButton.vue'
import LabelInput from '@/Components/Admin/UI/Input/LabelInput.vue'
import InputText from '@/Components/Admin/UI/Input/InputText.vue'
import InputError from '@/Components/Admin/UI/Input/InputError.vue'
import MetaDescTextarea from '@/Components/Admin/UI/Textarea/MetaDescTextarea.vue'
import LabelCheckbox from '@/Components/Admin/UI/Checkbox/LabelCheckbox.vue'
import ActivityCheckbox from '@/Components/Admin/UI/Checkbox/ActivityCheckbox.vue'
import InputNumber from '@/Components/Admin/UI/Input/InputNumber.vue'
import TypeSelect from '@/Components/Admin/System/Parameters/Select/TypeSelect.vue'

import CategorySelect from '@/Components/Admin/System/Parameters/Select/CategorySelect.vue'

const toast = useToast()
const { t } = useI18n()

/**
 * Форма создания параметра.
 *
 * Поле type не передаём:
 * ParameterController::store()
 * принудительно устанавливает type = parameter.
 */
const form = useForm({
    sort: 0,
    type: '',
    option: '',
    value: '',
    constant: '',
    category: '',
    description: '',
    activity: false,
})

/**
 * Фильтрация поля option.
 * Разрешаем латиницу, цифры и дефис.
 */
const handleOptionInput = (event) => {
    const cleaned = event.target.value.replace(
        /[^A-Za-z0-9\-]/g,
        ''
    )

    form.option =
        cleaned.charAt(0).toUpperCase() + cleaned.slice(1)
}

/**
 * Преобразование camelCase в UPPER_CASE.
 */
const toUpperCaseWithUnderscore = (str) => {
    return str
        .replace(/([a-z0-9])([A-Z])/g, '$1_$2')
        .toUpperCase()
}

/**
 * Автоматическое заполнение constant.
 *
 * Не перезаписываем значение,
 * если пользователь уже заполнил поле вручную.
 */
const handleConstantFocus = () => {
    if (form.option && !form.constant) {
        form.constant = toUpperCaseWithUnderscore(
            form.option
        )
    }
}

/**
 * Создание параметра.
 */
const submitForm = () => {
    form
        .transform((data) => ({
            ...data,
            activity: data.activity ? 1 : 0,
        }))
        .post(
            route('admin.parameters.store'),
            {
                preserveScroll: true,

                onSuccess: () => {
                    toast.success(
                        'Параметр успешно создан!'
                    )
                },

                onError: (errors) => {
                    console.error(
                        'Ошибка при создании параметра:',
                        errors
                    )

                    const firstError =
                        errors[Object.keys(errors)[0]]

                    toast.error(
                        firstError
                        || 'Пожалуйста, проверьте правильность заполнения полей.'
                    )
                },
            }
        )
}
</script>

<template>
    <AdminLayout :title="t('createParameter')">

        <template #header>
            <TitlePage>
                {{ t('createParameter') }}
            </TitlePage>
        </template>

        <div
            class="px-4 sm:px-6 lg:px-8 py-8
                   w-full max-w-12xl mx-auto"
        >
            <div
                class="p-4 bg-slate-50 dark:bg-slate-700
                       border border-blue-400 dark:border-blue-200
                       shadow-lg shadow-gray-500 dark:shadow-slate-400
                       bg-opacity-95 dark:bg-opacity-95"
            >

                <!-- Верхняя панель -->
                <div
                    class="sm:flex sm:justify-between
                           sm:items-center mb-2"
                >
                    <DefaultButton
                        :href="route('admin.parameters.index')"
                    >
                        <template #icon>
                            <svg
                                class="w-4 h-4 fill-current
                                       text-slate-100 shrink-0 mr-2"
                                viewBox="0 0 16 16"
                            >
                                <path
                                    d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0
                                       .7.7 1.2 1.7 1.4 2.7l2-.3
                                       c-.2-1.5-.9-2.8-1.9-3.8
                                       C10.1.4 5.7.4 2.9 3.1L.7.9
                                       0 7.3l6.4-.7-2.1-2.1z
                                       M15.6 8.7l-6.4.7 2.1 2.1
                                       c-1.9 1.9-5.1 1.9-7 0
                                       -.7-.7-1.2-1.7-1.4-2.7
                                       l-2 .3c.2 1.5.9 2.8 1.9 3.8
                                       1.4 1.4 3.1 2 4.9 2
                                       1.8 0 3.6-.7 4.9-2
                                       l2.2 2.2.8-6.4z"
                                />
                            </svg>
                        </template>

                        {{ t('back') }}
                    </DefaultButton>
                </div>

                <form
                    @submit.prevent="submitForm"
                    class="p-3 w-full"
                >

                    <!-- Активность / сортировка -->
                    <div
                        class="mb-3 flex justify-between
                               flex-col lg:flex-row
                               items-center gap-3"
                    >
                        <div class="flex justify-between w-full">
                            <div class="flex flex-row items-center">
                                <ActivityCheckbox
                                    v-model="form.activity"
                                />

                                <LabelCheckbox
                                    for="activity"
                                    :text="t('activity')"
                                />
                            </div>
                        </div>

                        <div
                            class="flex flex-row
                                   items-center gap-2"
                        >
                            <div class="h-8 flex items-center">
                                <LabelInput
                                    for="sort"
                                    :value="t('sort')"
                                    class="text-sm"
                                />
                            </div>

                            <InputNumber
                                id="sort"
                                type="number"
                                v-model="form.sort"
                                autocomplete="sort"
                                class="w-full lg:w-28"
                            />

                            <InputError
                                class="mt-2 lg:mt-0"
                                :message="form.errors.sort"
                            />
                        </div>
                    </div>

                    <!-- Категория / тип -->
                    <div
                        class="mb-3 flex justify-between
           flex-col lg:flex-row
           items-center gap-3"
                    >
                        <div class="flex flex-row items-center">
                            <LabelInput
                                for="category"
                                :value="t('parameterCategory')"
                                class="w-full"
                            />

                            <CategorySelect
                                v-model="form.category"
                                :error="form.errors.category"
                            />
                        </div>

                        <div
                            class="flex flex-row
               items-center gap-2"
                        >
                            <LabelInput
                                for="type"
                                :value="t('type')"
                                class="mr-3"
                            />

                            <TypeSelect
                                v-model="form.type"
                                :error="form.errors.type"
                                class="w-full lg:w-64 mr-3"
                            />
                        </div>
                    </div>

                    <!-- Option -->
                    <div
                        class="mb-3 flex flex-col items-start"
                    >
                        <div
                            class="flex justify-between w-full"
                        >
                            <LabelInput
                                for="option"
                                :value="t('parameterName')"
                            />

                            <div
                                class="text-md text-gray-900
                                       dark:text-gray-400 mt-1"
                            >
                                {{ form.option.length }}
                                / 255 {{ t('characters') }}
                            </div>
                        </div>

                        <InputText
                            id="option"
                            type="text"
                            v-model="form.option"
                            @input="handleOptionInput"
                            required
                            maxlength="255"
                            autocomplete="option"
                            pattern="[A-Za-z0-9\-]+"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.option"
                        />
                    </div>

                    <!-- Constant -->
                    <div
                        class="mb-3 flex flex-col items-start"
                    >
                        <LabelInput
                            for="constant"
                            :value="t('parameterConstant')"
                        />

                        <InputText
                            id="constant"
                            type="text"
                            v-model="form.constant"
                            @focus="handleConstantFocus"
                            required
                            maxlength="255"
                            autocomplete="constant"
                            pattern="[A-Z][A-Z0-9_]*"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.constant"
                        />
                    </div>

                    <!-- Value -->
                    <div
                        class="mb-3 flex flex-col items-start"
                    >
                        <div
                            class="flex justify-between w-full"
                        >
                            <LabelInput
                                for="value"
                                :value="t('parameterValue')"
                            />

                            <div
                                class="text-md text-gray-900
                                       dark:text-gray-400 mt-1"
                            >
                                {{ form.value.length }}
                                / 255 {{ t('characters') }}
                            </div>
                        </div>

                        <InputText
                            id="value"
                            type="text"
                            v-model="form.value"
                            maxlength="255"
                            autocomplete="value"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.value"
                        />
                    </div>

                    <!-- Description -->
                    <div
                        class="mb-3 flex flex-col items-start"
                    >
                        <div
                            class="flex justify-between w-full"
                        >
                            <LabelInput
                                for="description"
                                :value="t('parameterDescription')"
                            />

                            <div
                                class="text-md text-gray-900
                                       dark:text-gray-400 mt-1"
                            >
                                {{ form.description.length }}
                                / 255 {{ t('characters') }}
                            </div>
                        </div>

                        <MetaDescTextarea
                            v-model="form.description"
                            class="w-full"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.description"
                        />
                    </div>

                    <!-- Кнопки -->
                    <div
                        class="flex items-center
                               justify-center mt-4"
                    >
                        <DefaultButton
                            :href="route('admin.parameters.index')"
                        >
                            <template #icon>
                                <svg
                                    class="w-4 h-4 fill-current
                                           text-slate-100
                                           shrink-0 mr-2"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M4.3 4.5c1.9-1.9
                                           5.1-1.9 7 0
                                           .7.7 1.2 1.7 1.4 2.7
                                           l2-.3c-.2-1.5-.9-2.8
                                           -1.9-3.8C10.1.4
                                           5.7.4 2.9 3.1L.7.9
                                           0 7.3l6.4-.7-2.1-2.1z"
                                    />
                                </svg>
                            </template>

                            {{ t('back') }}
                        </DefaultButton>

                        <PrimaryButton
                            class="ms-4"
                            :class="{
                                'opacity-25': form.processing
                            }"
                            :disabled="form.processing"
                        >
                            <template #icon>
                                <svg
                                    class="w-4 h-4
                                           fill-current text-slate-100"
                                    viewBox="0 0 16 16"
                                >
                                    <path
                                        d="M14.3 2.3L5 11.6
                                           1.7 8.3c-.4-.4-1-.4
                                           -1.4 0-.4.4-.4 1
                                           0 1.4l4 4c.2.2.4.3
                                           .7.3.3 0 .5-.1.7-.3
                                           l10-10c.4-.4.4-1
                                           0-1.4-.4-.4-1-.4-1.4 0z"
                                    />
                                </svg>
                            </template>

                            {{ t('save') }}
                        </PrimaryButton>
                    </div>

                </form>
            </div>
        </div>
    </AdminLayout>
</template>
