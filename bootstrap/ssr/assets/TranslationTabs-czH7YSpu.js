import { ref, computed, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderClass, ssrInterpolate, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
const _sfc_main = {
  __name: "TranslationTabs",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: String,
      required: true
    },
    translations: {
      type: Object,
      required: true
    },
    availableLocales: {
      type: Array,
      default: () => ["ru", "en", "kk", "zh"]
    },
    makeTranslation: {
      type: Function,
      required: true
    },
    minTranslations: {
      type: Number,
      default: 1
    }
  },
  emits: [
    "update:modelValue",
    "update:translations",
    "removed",
    "added"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const localeToAdd = ref("");
    const translationLocales = computed(() => Object.keys(props.translations || {}));
    const availableLocalesForAdd = computed(() => {
      return props.availableLocales.filter((locale) => !translationLocales.value.includes(locale));
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col lg:flex-row lg:items-center justify-between gap-3 mb-4" }, _attrs))}><div class="flex flex-wrap gap-2"><!--[-->`);
      ssrRenderList(translationLocales.value, (locale) => {
        _push(`<button type="button" class="${ssrRenderClass([__props.modelValue === locale ? "bg-blue-500 text-white border-blue-500" : "bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-100 border-slate-300 dark:border-slate-500", "px-2 py-0.5 text-sm rounded-sm border"])}">${ssrInterpolate(locale.toUpperCase())}</button>`);
      });
      _push(`<!--]--></div><div class="flex items-center gap-2"><select class="w-auto pl-3 pr-7 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"><option value=""${ssrIncludeBooleanAttr(Array.isArray(localeToAdd.value) ? ssrLooseContain(localeToAdd.value, "") : ssrLooseEqual(localeToAdd.value, "")) ? " selected" : ""}>${ssrInterpolate(unref(t)("addLocale"))}</option><!--[-->`);
      ssrRenderList(availableLocalesForAdd.value, (locale) => {
        _push(`<option${ssrRenderAttr("value", locale)}${ssrIncludeBooleanAttr(Array.isArray(localeToAdd.value) ? ssrLooseContain(localeToAdd.value, locale) : ssrLooseEqual(localeToAdd.value, locale)) ? " selected" : ""}>${ssrInterpolate(locale.toUpperCase())}</option>`);
      });
      _push(`<!--]--></select><button type="button" class="px-3 py-1 text-sm rounded-sm bg-blue-500 text-white hover:bg-blue-600 disabled:opacity-50"${ssrIncludeBooleanAttr(!localeToAdd.value) ? " disabled" : ""}> + </button><button type="button" class="px-3 py-1 text-sm rounded-sm bg-yellow-400 text-white hover:bg-yellow-500 disabled:opacity-50"${ssrIncludeBooleanAttr(translationLocales.value.length <= __props.minTranslations) ? " disabled" : ""}> − </button></div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Locale/TranslationTabs.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
