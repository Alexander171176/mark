import { mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$3 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$4 } from "./InputError-CLVdJ1nk.js";
const _sfc_main$2 = {
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
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolAssignment/Select/SelectStatus.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "SelectVisibility",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: String, default: "" },
    errorMessage: { type: String, default: "" }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}><label class="mb-1 block font-medium text-sm text-indigo-600 dark:text-sky-500">${ssrInterpolate(unref(t)("visibility"))}</label><select class="block w-full py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${ssrRenderAttr("value", __props.modelValue)}><option value="">${ssrInterpolate(unref(t)("select"))}</option><option value="public">${ssrInterpolate(unref(t)("assignmentGeneral"))}</option><option value="enrolled">${ssrInterpolate(unref(t)("assignmentTest"))}</option><option value="private">${ssrInterpolate(unref(t)("assignmentThematic"))}</option></select>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolAssignment/Select/SelectVisibility.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "SelectGradingType",
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
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}>`);
      _push(ssrRenderComponent(_sfc_main$3, {
        for: "grading_type",
        value: unref(t)("gradingType")
      }, null, _parent));
      _push(`<select id="grading_type"${ssrRenderAttr("value", __props.modelValue)} class="block w-full py-0.5 text-md rounded-sm border-slate-500 focus:border-indigo-500 focus:ring-indigo-300 shadow-sm dark:bg-cyan-800 dark:text-slate-100"><option value="">${ssrInterpolate(unref(t)("select"))}</option><option value="manual">${ssrInterpolate(unref(t)("gradingManual"))}</option><option value="auto">${ssrInterpolate(unref(t)("gradingAuto"))}</option></select>`);
      _push(ssrRenderComponent(_sfc_main$4, {
        class: "mt-1",
        message: __props.errorMessage
      }, null, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolAssignment/Select/SelectGradingType.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$2 as _,
  _sfc_main$1 as a,
  _sfc_main as b
};
