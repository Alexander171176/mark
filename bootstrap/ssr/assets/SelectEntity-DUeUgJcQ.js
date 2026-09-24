import { mergeProps, withCtx, createBlock, createCommentVNode, createTextVNode, openBlock, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderList } from "vue/server-renderer";
import { _ as _sfc_main$1 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$2 } from "./InputError-CLVdJ1nk.js";
const _sfc_main = {
  __name: "SelectEntity",
  __ssrInlineRender: true,
  props: {
    id: {
      type: String,
      required: true
    },
    modelValue: {
      type: [Number, String, null],
      default: null
    },
    label: {
      type: String,
      default: ""
    },
    required: {
      type: Boolean,
      default: false
    },
    options: {
      type: Array,
      default: () => []
      // [{ id: 1, label: '...' }, ...]
    },
    errorMessage: {
      type: String,
      default: ""
    },
    placeholder: {
      type: String,
      default: ""
    },
    /**
     * Если true — первый option будет "не выбрано" (value = null)
     */
    nullable: {
      type: Boolean,
      default: false
    }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}>`);
      _push(ssrRenderComponent(_sfc_main$1, { for: __props.id }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            if (__props.required) {
              _push2(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId}>*</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(` ${ssrInterpolate(__props.label)}`);
          } else {
            return [
              __props.required ? (openBlock(), createBlock("span", {
                key: 0,
                class: "text-red-500 dark:text-red-300 font-semibold"
              }, "*")) : createCommentVNode("", true),
              createTextVNode(" " + toDisplayString(__props.label), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<select${ssrRenderAttr("id", __props.id)}${ssrRenderAttr("value", __props.modelValue ?? (__props.nullable ? null : ""))} class="w-full px-3 py-1 rounded-sm shadow-sm form-select bg-white dark:bg-cyan-800 text-xs font-semibold text-gray-600 dark:text-slate-100 border border-slate-400 dark:border-slate-600 whitespace-pre-line">`);
      if (__props.nullable) {
        _push(`<option${ssrRenderAttr("value", null)} class="text-xs whitespace-pre-line">${ssrInterpolate(__props.placeholder || "—")}</option>`);
      } else if (__props.placeholder) {
        _push(`<option value="" class="text-xs whitespace-pre-line">${ssrInterpolate(__props.placeholder)}</option>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<!--[-->`);
      ssrRenderList(__props.options, (opt) => {
        _push(`<option${ssrRenderAttr("value", opt.id)} class="text-xs whitespace-pre-line">${ssrInterpolate(opt.label)}</option>`);
      });
      _push(`<!--]--></select>`);
      if (__props.errorMessage) {
        _push(ssrRenderComponent(_sfc_main$2, {
          class: "mt-2",
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Select/SelectEntity.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
