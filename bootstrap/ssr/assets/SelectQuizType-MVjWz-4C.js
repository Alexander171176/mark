import { mergeProps, withCtx, unref, createTextVNode, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderList } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$2 } from "./InputError-CLVdJ1nk.js";
const _sfc_main = {
  __name: "SelectQuizType",
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
    const quizTypeOptions = [
      { value: "", labelKey: "notSelected" },
      // placeholder
      { value: "graded", labelKey: "quizTypeGraded" },
      { value: "practice", labelKey: "quizTypePractice" }
    ];
    const getOptionLabel = (option) => {
      return t(option.labelKey);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}>`);
      _push(ssrRenderComponent(_sfc_main$1, { for: "quiz_type" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("type"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("type")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<select id="quiz_type" class="block w-full py-0.5 border-slate-500 text-md rounded-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-300 dark:bg-cyan-800 dark:text-slate-100"${ssrRenderAttr("value", __props.modelValue)}><!--[-->`);
      ssrRenderList(quizTypeOptions, (opt) => {
        _push(`<option${ssrRenderAttr("value", opt.value)}>${ssrInterpolate(getOptionLabel(opt))}</option>`);
      });
      _push(`<!--]--></select>`);
      if (__props.errorMessage) {
        _push(ssrRenderComponent(_sfc_main$2, {
          class: "mt-1",
          message: __props.errorMessage
        }, null, _parent));
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuiz/Select/SelectQuizType.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
