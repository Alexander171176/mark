<script setup>
import { defineProps, defineEmits } from "vue";
import { useI18n } from "vue-i18n";

const { t } = useI18n();

const props = defineProps({
    modelValue: { type: String, default: '' },
    errorMessage: { type: String, default: '' }
});

const emit = defineEmits(["update:modelValue"]);

const updateValue = (e) => {
    emit("update:modelValue", e.target.value);
};
</script>

<template>
    <div class="flex flex-col items-start">
        <label class="mb-1 block font-medium text-sm text-indigo-600 dark:text-sky-500">
            {{ t("availability") }}
        </label>

        <select
            class="block w-full py-0.5 border-slate-500 text-md
                   focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm
                   dark:bg-cyan-800 dark:text-slate-100"
            :value="modelValue"
            @change="updateValue"
        >
            <option value="">{{ t("select") }}</option>
            <option value="unlisted">{{ t("availabilityUnlisted") }}</option>
            <option value="public">{{ t("availabilityPublic") }}</option>
            <option value="private">{{ t("availabilityPrivate") }}</option>
        </select>

        <p v-if="errorMessage" class="text-sm text-red-600 dark:text-orange-200">
            {{ errorMessage }}
        </p>
    </div>
</template>
