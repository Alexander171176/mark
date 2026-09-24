import { ref, computed, mergeProps, withCtx, createTextVNode, toDisplayString, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderComponent, ssrRenderClass, ssrInterpolate } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$2 } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$1 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$3 } from "./InputError-CLVdJ1nk.js";
const _sfc_main = {
  __name: "EditImageFileInput",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: [File, null],
      default: null
    },
    initialPreview: {
      type: String,
      default: null
    },
    label: {
      type: String,
      default: null
    },
    buttonText: {
      type: String,
      default: null
    },
    emptyText: {
      type: String,
      default: null
    },
    error: {
      type: String,
      default: ""
    },
    accept: {
      type: String,
      default: "image/png,image/jpeg,image/webp"
    },
    previewClass: {
      type: String,
      default: "h-24 w-36 object-cover rounded-sm border border-slate-400"
    },
    placeholderClass: {
      type: String,
      default: "h-24 w-36 flex items-center justify-center rounded-sm border border-dashed border-slate-400 text-xs text-slate-500"
    }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const fileInput = ref(null);
    const newPreview = ref(null);
    const labelText = computed(() => props.label ?? t("image"));
    const buttonLabel = computed(() => props.buttonText ?? t("selectImage"));
    const emptyLabel = computed(() => props.emptyText ?? t("noImage"));
    const preview = computed(() => {
      return newPreview.value || props.initialPreview || null;
    });
    const selectFile = () => {
      var _a;
      (_a = fileInput.value) == null ? void 0 : _a.click();
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-center" }, _attrs))}><input type="file" class="hidden"${ssrRenderAttr("accept", __props.accept)}>`);
      _push(ssrRenderComponent(_sfc_main$1, { value: labelText.value }, null, _parent));
      _push(`<div class="mt-2">`);
      if (preview.value) {
        _push(`<img${ssrRenderAttr("src", preview.value)}${ssrRenderAttr("alt", labelText.value)} class="${ssrRenderClass(__props.previewClass)}">`);
      } else {
        _push(`<div class="${ssrRenderClass(__props.placeholderClass)}">${ssrInterpolate(emptyLabel.value)}</div>`);
      }
      _push(`</div>`);
      _push(ssrRenderComponent(_sfc_main$2, {
        class: "mt-2 text-xs py-1 rounded-sm",
        type: "button",
        onClick: selectFile
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(buttonLabel.value)}`);
          } else {
            return [
              createTextVNode(toDisplayString(buttonLabel.value), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      if (newPreview.value) {
        _push(`<button type="button" class="flex flex-row items-center justify-center gap-1 font-semibold mt-2 text-sm text-red-700 dark:text-red-300 hover:underline"><svg class="w-4 h-4 fill-current text-red-700 dark:text-red-300 shrink-0 mr-2" viewBox="0 0 352 512"><path d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"></path></svg> ${ssrInterpolate(unref(t)("delete"))}</button>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$3, {
        class: "mt-2",
        message: __props.error
      }, null, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/File/EditImageFileInput.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
