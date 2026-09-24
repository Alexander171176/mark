import { ref, computed, watch, mergeProps, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderComponent, ssrRenderClass, ssrInterpolate } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$2 } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$1 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$3 } from "./InputError-CLVdJ1nk.js";
const _sfc_main = {
  __name: "ImageFileInput",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: [File, String, null],
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
  emits: ["update:modelValue", "clear"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const fileInput = ref(null);
    const preview = ref(null);
    const labelText = computed(() => props.label ?? t("image"));
    const buttonLabel = computed(() => props.buttonText ?? t("selectImage"));
    const emptyLabel = computed(() => props.emptyText ?? t("noImage"));
    watch(
      () => props.modelValue,
      (value) => {
        if (typeof value === "string" && value) {
          preview.value = value;
          return;
        }
        if (!value) {
          preview.value = null;
        }
      },
      { immediate: true }
    );
    const selectFile = () => {
      var _a;
      (_a = fileInput.value) == null ? void 0 : _a.click();
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-center" }, _attrs))}><input type="file" class="hidden"${ssrRenderAttr("accept", __props.accept)}>`);
      _push(ssrRenderComponent(_sfc_main$1, { value: labelText.value }, null, _parent));
      _push(`<div class="mt-1">`);
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
        icon: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 384 512"${_scopeId}><path d="M384 121.941V128H256V0h6.059a24 24 0 0 1 16.97 7.029l97.941 97.941a24.002 24.002 0 0 1 7.03 16.971zM248 160c-13.2 0-24-10.8-24-24V0H24C10.745 0 0 10.745 0 24v464c0 13.255 10.745 24 24 24h336c13.255 0 24-10.745 24-24V160H248zm-135.455 16c26.51 0 48 21.49 48 48s-21.49 48-48 48-48-21.49-48-48 21.491-48 48-48zm208 240h-256l.485-48.485L104.545 328c4.686-4.686 11.799-4.201 16.485.485L160.545 368 264.06 264.485c4.686-4.686 12.284-4.686 16.971 0L320.545 304v112z"${_scopeId}></path></svg>`);
          } else {
            return [
              (openBlock(), createBlock("svg", {
                class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                viewBox: "0 0 384 512"
              }, [
                createVNode("path", { d: "M384 121.941V128H256V0h6.059a24 24 0 0 1 16.97 7.029l97.941 97.941a24.002 24.002 0 0 1 7.03 16.971zM248 160c-13.2 0-24-10.8-24-24V0H24C10.745 0 0 10.745 0 24v464c0 13.255 10.745 24 24 24h336c13.255 0 24-10.745 24-24V160H248zm-135.455 16c26.51 0 48 21.49 48 48s-21.49 48-48 48-48-21.49-48-48 21.491-48 48-48zm208 240h-256l.485-48.485L104.545 328c4.686-4.686 11.799-4.201 16.485.485L160.545 368 264.06 264.485c4.686-4.686 12.284-4.686 16.971 0L320.545 304v112z" })
              ]))
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` ${ssrInterpolate(buttonLabel.value)}`);
          } else {
            return [
              createTextVNode(" " + toDisplayString(buttonLabel.value), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      if (preview.value) {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/File/ImageFileInput.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
