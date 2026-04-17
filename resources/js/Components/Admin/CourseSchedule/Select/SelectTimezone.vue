<script setup>
import { computed, onMounted } from 'vue'
import { useI18n } from 'vue-i18n'
import LabelInput from '@/Components/Admin/Input/LabelInput.vue'
import InputError from '@/Components/Admin/Input/InputError.vue'

const { t } = useI18n()

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    errorMessage: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['update:modelValue'])

/**
 * 🔹 Кастомный список таймзон:
 * value — то, что пишем в БД,
 * label — то, что показываем пользователю (с UTC смещением).
 */
const fallbackTimezones = [
    // Универсальный
    { value: 'UTC', label: '(UTC+0) UTC' },

    // Европа
    { value: 'Europe/London',     label: '(UTC+0) Europe/London' },
    { value: 'Europe/Berlin',     label: '(UTC+1) Europe/Berlin' },
    { value: 'Europe/Paris',      label: '(UTC+1) Europe/Paris' },
    { value: 'Europe/Madrid',     label: '(UTC+1) Europe/Madrid' },
    { value: 'Europe/Rome',       label: '(UTC+1) Europe/Rome' },
    { value: 'Europe/Amsterdam',  label: '(UTC+1) Europe/Amsterdam' },
    { value: 'Europe/Brussels',   label: '(UTC+1) Europe/Brussels' },
    { value: 'Europe/Vienna',     label: '(UTC+1) Europe/Vienna' },
    { value: 'Europe/Prague',     label: '(UTC+1) Europe/Prague' },
    { value: 'Europe/Warsaw',     label: '(UTC+1) Europe/Warsaw' },
    { value: 'Europe/Budapest',   label: '(UTC+1) Europe/Budapest' },
    { value: 'Europe/Athens',     label: '(UTC+2) Europe/Athens' },
    { value: 'Europe/Helsinki',   label: '(UTC+2) Europe/Helsinki' },
    { value: 'Europe/Oslo',       label: '(UTC+1) Europe/Oslo' },
    { value: 'Europe/Stockholm',  label: '(UTC+1) Europe/Stockholm' },
    { value: 'Europe/Copenhagen', label: '(UTC+1) Europe/Copenhagen' },
    { value: 'Europe/Dublin',     label: '(UTC+0) Europe/Dublin' },
    { value: 'Europe/Zurich',     label: '(UTC+1) Europe/Zurich' },
    { value: 'Europe/Moscow',     label: '(UTC+3) Europe/Moscow' },
    { value: 'Europe/Kaliningrad',label: '(UTC+2) Europe/Kaliningrad' },
    { value: 'Europe/Minsk',      label: '(UTC+3) Europe/Minsk' },
    { value: 'Europe/Samara',     label: '(UTC+4) Europe/Samara' },
    { value: 'Europe/Volgograd',  label: '(UTC+3) Europe/Volgograd' },

    // Азия (включая Казахстан)
    { value: 'Asia/Almaty',       label: '(UTC+6) Asia/Almaty' },
    { value: 'Asia/Aqtau',        label: '(UTC+5) Asia/Aqtau' },
    { value: 'Asia/Aqtobe',       label: '(UTC+5) Asia/Aqtobe' },
    { value: 'Asia/Atyrau',       label: '(UTC+5) Asia/Atyrau' },
    { value: 'Asia/Oral',         label: '(UTC+5) Asia/Oral' },
    { value: 'Asia/Ashgabat',     label: '(UTC+5) Asia/Ashgabat' },
    { value: 'Asia/Tashkent',     label: '(UTC+5) Asia/Tashkent' },
    { value: 'Asia/Bishkek',      label: '(UTC+6) Asia/Bishkek' },
    { value: 'Asia/Omsk',         label: '(UTC+6) Asia/Omsk' },
    { value: 'Asia/Novosibirsk',  label: '(UTC+7) Asia/Novosibirsk' },
    { value: 'Asia/Krasnoyarsk',  label: '(UTC+7) Asia/Krasnoyarsk' },
    { value: 'Asia/Irkutsk',      label: '(UTC+8) Asia/Irkutsk' },
    { value: 'Asia/Yakutsk',      label: '(UTC+9) Asia/Yakutsk' },
    { value: 'Asia/Vladivostok',  label: '(UTC+10) Asia/Vladivostok' },
    { value: 'Asia/Magadan',      label: '(UTC+11) Asia/Magadan' },
    { value: 'Asia/Kamchatka',    label: '(UTC+12) Asia/Kamchatka' },
    { value: 'Asia/Tokyo',        label: '(UTC+9) Asia/Tokyo' },
    { value: 'Asia/Seoul',        label: '(UTC+9) Asia/Seoul' },
    { value: 'Asia/Shanghai',     label: '(UTC+8) Asia/Shanghai' },
    { value: 'Asia/Hong_Kong',    label: '(UTC+8) Asia/Hong_Kong' },
    { value: 'Asia/Singapore',    label: '(UTC+8) Asia/Singapore' },
    { value: 'Asia/Dubai',        label: '(UTC+4) Asia/Dubai' },
    { value: 'Asia/Tehran',       label: '(UTC+3:30) Asia/Tehran' },
    { value: 'Asia/Jerusalem',    label: '(UTC+2) Asia/Jerusalem' },
    { value: 'Asia/Colombo',      label: '(UTC+5:30) Asia/Colombo' },
    { value: 'Asia/Kolkata',      label: '(UTC+5:30) Asia/Kolkata' },
    { value: 'Asia/Bangkok',      label: '(UTC+7) Asia/Bangkok' },
    { value: 'Asia/Ho_Chi_Minh',  label: '(UTC+7) Asia/Ho_Chi_Minh' },

    // Америка
    { value: 'America/New_York',  label: '(UTC-5) America/New_York' },
    { value: 'America/Chicago',   label: '(UTC-6) America/Chicago' },
    { value: 'America/Denver',    label: '(UTC-7) America/Denver' },
    { value: 'America/Los_Angeles', label: '(UTC-8) America/Los_Angeles' },
    { value: 'America/Toronto',   label: '(UTC-5) America/Toronto' },
    { value: 'America/Vancouver', label: '(UTC-8) America/Vancouver' },
    { value: 'America/Mexico_City', label: '(UTC-6) America/Mexico_City' },
    { value: 'America/Sao_Paulo', label: '(UTC-3) America/Sao_Paulo' },
    { value: 'America/Bogota',    label: '(UTC-5) America/Bogota' },
    { value: 'America/Lima',      label: '(UTC-5) America/Lima' },
    { value: 'America/Argentina/Buenos_Aires', label: '(UTC-3) America/Argentina/Buenos_Aires' },

    // Африка
    { value: 'Africa/Cairo',        label: '(UTC+2) Africa/Cairo' },
    { value: 'Africa/Johannesburg', label: '(UTC+2) Africa/Johannesburg' },
    { value: 'Africa/Lagos',        label: '(UTC+1) Africa/Lagos' },
    { value: 'Africa/Nairobi',      label: '(UTC+3) Africa/Nairobi' },
    { value: 'Africa/Casablanca',   label: '(UTC+0) Africa/Casablanca' },

    // Австралия / Океания
    { value: 'Australia/Sydney',   label: '(UTC+11) Australia/Sydney' },
    { value: 'Australia/Melbourne',label: '(UTC+11) Australia/Melbourne' },
    { value: 'Australia/Brisbane', label: '(UTC+10) Australia/Brisbane' },
    { value: 'Australia/Perth',    label: '(UTC+8) Australia/Perth' },
    { value: 'Pacific/Auckland',   label: '(UTC+13) Pacific/Auckland' },
    { value: 'Pacific/Fiji',       label: '(UTC+12) Pacific/Fiji' },
]

// просто наш список
const timezones = computed(() => fallbackTimezones)

// 🔹 Авто-установка таймзоны по браузеру (только если modelValue пустой)
onMounted(() => {
    if (props.modelValue) {
        // В режиме редактирования / если уже выбрано — не лезем.
        return
    }

    let browserTz = null

    try {
        if (typeof Intl !== 'undefined' && Intl.DateTimeFormat) {
            browserTz = Intl.DateTimeFormat().resolvedOptions().timeZone || null
        }
    } catch (e) {
        browserTz = null
    }

    const values = fallbackTimezones.map(tz => tz.value)

    let initialTz = 'Europe/Moscow' // дефолт по умолчанию

    if (browserTz && values.includes(browserTz)) {
        initialTz = browserTz
    }

    emit('update:modelValue', initialTz)
})

const handleChange = (event) => {
    emit('update:modelValue', event.target.value || '')
}
</script>

<template>
    <div class="flex flex-col items-start w-full">
        <LabelInput
            for="timezone"
            :value="t('timezone')"
        />

        <select
            id="timezone"
            class="block w-full py-0.5 border-slate-500 text-md
                   focus:border-indigo-500 focus:ring-indigo-300
                   rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"
            :value="modelValue"
            @change="handleChange"
        >
            <!-- Плейсхолдер -->
            <option value="">
                {{ t('select') }}
            </option>

            <!-- Список таймзон -->
            <option
                v-for="tz in timezones"
                :key="tz.value"
                :value="tz.value"
            >
                {{ tz.label }}
            </option>
        </select>

        <InputError class="mt-2" :message="errorMessage" />
    </div>
</template>
