import { mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}><label class="mb-1 block font-medium text-sm text-indigo-600 dark:text-sky-500">${ssrInterpolate(unref(t)("status"))}</label><select class="block w-full py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${ssrRenderAttr("value", __props.modelValue)}><option value="">${ssrInterpolate(unref(t)("select"))}</option><option value="draft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="published">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="archived">${ssrInterpolate(unref(t)("statusArchived"))}</option></select>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCourse/Select/SelectStatus.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "SelectAvailability",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: String, default: "" },
    errorMessage: { type: String, default: "" }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}><label class="mb-1 block font-medium text-sm text-indigo-600 dark:text-sky-500">${ssrInterpolate(unref(t)("availability"))}</label><select class="block w-full py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${ssrRenderAttr("value", __props.modelValue)}><option value="">${ssrInterpolate(unref(t)("select"))}</option><option value="unlisted">${ssrInterpolate(unref(t)("availabilityUnlisted"))}</option><option value="public">${ssrInterpolate(unref(t)("availabilityPublic"))}</option><option value="private">${ssrInterpolate(unref(t)("availabilityPrivate"))}</option></select>`);
      if (__props.errorMessage) {
        _push(`<p class="text-sm text-red-600 dark:text-orange-200">${ssrInterpolate(__props.errorMessage)}</p>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolCourse/Select/SelectAvailability.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$1 as _,
  _sfc_main as a
};
