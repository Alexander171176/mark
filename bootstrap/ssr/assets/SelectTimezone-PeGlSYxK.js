import { mergeProps, unref, useSSRContext, computed, onMounted } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrRenderList } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$2 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$3 } from "./InputError-CLVdJ1nk.js";
const _sfc_main$1 = {
  __name: "SelectStatus",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: String, default: "" },
    errorMessage: { type: String, default: "" }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}><label class="mb-1 block font-medium text-sm text-indigo-600 dark:text-sky-500">${ssrInterpolate(unref(t)("status"))}</label><select class="block w-fit py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${ssrRenderAttr("value", __props.modelValue)}><option value="">${ssrInterpolate(unref(t)("select"))}</option><option value="draft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="published">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="archived">${ssrInterpolate(unref(t)("statusArchived"))}</option></select>`);
      if (__props.errorMessage) {
        _push(`<p class="text-sm text-red-600 dark:text-orange-200">${ssrInterpolate(__props.errorMessage)}</p>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCourseSchedule/Select/SelectStatus.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "SelectTimezone",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: String,
      default: ""
    },
    errorMessage: {
      type: String,
      default: ""
    }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const fallbackTimezones = [
      // Универсальный
      { value: "UTC", label: "(UTC+0) UTC" },
      // Европа
      { value: "Europe/London", label: "(UTC+0) Europe/London" },
      { value: "Europe/Berlin", label: "(UTC+1) Europe/Berlin" },
      { value: "Europe/Paris", label: "(UTC+1) Europe/Paris" },
      { value: "Europe/Madrid", label: "(UTC+1) Europe/Madrid" },
      { value: "Europe/Rome", label: "(UTC+1) Europe/Rome" },
      { value: "Europe/Amsterdam", label: "(UTC+1) Europe/Amsterdam" },
      { value: "Europe/Brussels", label: "(UTC+1) Europe/Brussels" },
      { value: "Europe/Vienna", label: "(UTC+1) Europe/Vienna" },
      { value: "Europe/Prague", label: "(UTC+1) Europe/Prague" },
      { value: "Europe/Warsaw", label: "(UTC+1) Europe/Warsaw" },
      { value: "Europe/Budapest", label: "(UTC+1) Europe/Budapest" },
      { value: "Europe/Athens", label: "(UTC+2) Europe/Athens" },
      { value: "Europe/Helsinki", label: "(UTC+2) Europe/Helsinki" },
      { value: "Europe/Oslo", label: "(UTC+1) Europe/Oslo" },
      { value: "Europe/Stockholm", label: "(UTC+1) Europe/Stockholm" },
      { value: "Europe/Copenhagen", label: "(UTC+1) Europe/Copenhagen" },
      { value: "Europe/Dublin", label: "(UTC+0) Europe/Dublin" },
      { value: "Europe/Zurich", label: "(UTC+1) Europe/Zurich" },
      { value: "Europe/Moscow", label: "(UTC+3) Europe/Moscow" },
      { value: "Europe/Kaliningrad", label: "(UTC+2) Europe/Kaliningrad" },
      { value: "Europe/Minsk", label: "(UTC+3) Europe/Minsk" },
      { value: "Europe/Samara", label: "(UTC+4) Europe/Samara" },
      { value: "Europe/Volgograd", label: "(UTC+3) Europe/Volgograd" },
      // Азия (включая Казахстан)
      { value: "Asia/Almaty", label: "(UTC+6) Asia/Almaty" },
      { value: "Asia/Aqtau", label: "(UTC+5) Asia/Aqtau" },
      { value: "Asia/Aqtobe", label: "(UTC+5) Asia/Aqtobe" },
      { value: "Asia/Atyrau", label: "(UTC+5) Asia/Atyrau" },
      { value: "Asia/Oral", label: "(UTC+5) Asia/Oral" },
      { value: "Asia/Ashgabat", label: "(UTC+5) Asia/Ashgabat" },
      { value: "Asia/Tashkent", label: "(UTC+5) Asia/Tashkent" },
      { value: "Asia/Bishkek", label: "(UTC+6) Asia/Bishkek" },
      { value: "Asia/Omsk", label: "(UTC+6) Asia/Omsk" },
      { value: "Asia/Novosibirsk", label: "(UTC+7) Asia/Novosibirsk" },
      { value: "Asia/Krasnoyarsk", label: "(UTC+7) Asia/Krasnoyarsk" },
      { value: "Asia/Irkutsk", label: "(UTC+8) Asia/Irkutsk" },
      { value: "Asia/Yakutsk", label: "(UTC+9) Asia/Yakutsk" },
      { value: "Asia/Vladivostok", label: "(UTC+10) Asia/Vladivostok" },
      { value: "Asia/Magadan", label: "(UTC+11) Asia/Magadan" },
      { value: "Asia/Kamchatka", label: "(UTC+12) Asia/Kamchatka" },
      { value: "Asia/Tokyo", label: "(UTC+9) Asia/Tokyo" },
      { value: "Asia/Seoul", label: "(UTC+9) Asia/Seoul" },
      { value: "Asia/Shanghai", label: "(UTC+8) Asia/Shanghai" },
      { value: "Asia/Hong_Kong", label: "(UTC+8) Asia/Hong_Kong" },
      { value: "Asia/Singapore", label: "(UTC+8) Asia/Singapore" },
      { value: "Asia/Dubai", label: "(UTC+4) Asia/Dubai" },
      { value: "Asia/Tehran", label: "(UTC+3:30) Asia/Tehran" },
      { value: "Asia/Jerusalem", label: "(UTC+2) Asia/Jerusalem" },
      { value: "Asia/Colombo", label: "(UTC+5:30) Asia/Colombo" },
      { value: "Asia/Kolkata", label: "(UTC+5:30) Asia/Kolkata" },
      { value: "Asia/Bangkok", label: "(UTC+7) Asia/Bangkok" },
      { value: "Asia/Ho_Chi_Minh", label: "(UTC+7) Asia/Ho_Chi_Minh" },
      // Америка
      { value: "America/New_York", label: "(UTC-5) America/New_York" },
      { value: "America/Chicago", label: "(UTC-6) America/Chicago" },
      { value: "America/Denver", label: "(UTC-7) America/Denver" },
      { value: "America/Los_Angeles", label: "(UTC-8) America/Los_Angeles" },
      { value: "America/Toronto", label: "(UTC-5) America/Toronto" },
      { value: "America/Vancouver", label: "(UTC-8) America/Vancouver" },
      { value: "America/Mexico_City", label: "(UTC-6) America/Mexico_City" },
      { value: "America/Sao_Paulo", label: "(UTC-3) America/Sao_Paulo" },
      { value: "America/Bogota", label: "(UTC-5) America/Bogota" },
      { value: "America/Lima", label: "(UTC-5) America/Lima" },
      { value: "America/Argentina/Buenos_Aires", label: "(UTC-3) America/Argentina/Buenos_Aires" },
      // Африка
      { value: "Africa/Cairo", label: "(UTC+2) Africa/Cairo" },
      { value: "Africa/Johannesburg", label: "(UTC+2) Africa/Johannesburg" },
      { value: "Africa/Lagos", label: "(UTC+1) Africa/Lagos" },
      { value: "Africa/Nairobi", label: "(UTC+3) Africa/Nairobi" },
      { value: "Africa/Casablanca", label: "(UTC+0) Africa/Casablanca" },
      // Австралия / Океания
      { value: "Australia/Sydney", label: "(UTC+11) Australia/Sydney" },
      { value: "Australia/Melbourne", label: "(UTC+11) Australia/Melbourne" },
      { value: "Australia/Brisbane", label: "(UTC+10) Australia/Brisbane" },
      { value: "Australia/Perth", label: "(UTC+8) Australia/Perth" },
      { value: "Pacific/Auckland", label: "(UTC+13) Pacific/Auckland" },
      { value: "Pacific/Fiji", label: "(UTC+12) Pacific/Fiji" }
    ];
    const timezones = computed(() => fallbackTimezones);
    onMounted(() => {
      if (props.modelValue) {
        return;
      }
      let browserTz = null;
      try {
        if (typeof Intl !== "undefined" && Intl.DateTimeFormat) {
          browserTz = Intl.DateTimeFormat().resolvedOptions().timeZone || null;
        }
      } catch (e) {
        browserTz = null;
      }
      const values = fallbackTimezones.map((tz) => tz.value);
      let initialTz = "Europe/Moscow";
      if (browserTz && values.includes(browserTz)) {
        initialTz = browserTz;
      }
      emit("update:modelValue", initialTz);
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start w-full" }, _attrs))}>`);
      _push(ssrRenderComponent(_sfc_main$2, {
        for: "timezone",
        value: unref(t)("timezone")
      }, null, _parent));
      _push(`<select id="timezone" class="block w-full py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${ssrRenderAttr("value", __props.modelValue)}><option value="">${ssrInterpolate(unref(t)("select"))}</option><!--[-->`);
      ssrRenderList(timezones.value, (tz) => {
        _push(`<option${ssrRenderAttr("value", tz.value)}>${ssrInterpolate(tz.label)}</option>`);
      });
      _push(`<!--]--></select>`);
      _push(ssrRenderComponent(_sfc_main$3, {
        class: "mt-2",
        message: __props.errorMessage
      }, null, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCourseSchedule/Select/SelectTimezone.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$1 as _,
  _sfc_main as a
};
