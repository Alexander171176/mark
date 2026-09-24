import { mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderClass, ssrRenderAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
const _sfc_main = {
  __name: "ViewModeToggle",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: String, default: "grid" },
    gridValue: { type: String, default: "grid" },
    rowValue: { type: String, default: "row" },
    size: { type: String, default: "md" }
    // sm | md
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const buttonClass = (mode) => {
      const active = props.modelValue === mode;
      const sizeClass = props.size === "sm" ? "px-1.5 py-1.5" : "px-1.5 py-1.5";
      return [
        sizeClass,
        "transition rounded-sm border border-gray-400 dark:border-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800",
        active ? "bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300" : "bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400"
      ].join(" ");
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex items-center gap-2" }, _attrs))}><button type="button" class="${ssrRenderClass(buttonClass(__props.gridValue))}"${ssrRenderAttr("title", unref(t)("gridView"))}><svg class="h-4 w-4 hover:text-blue-500" fill="currentColor" viewBox="0 0 512 512"><path d="M296 32h192c13.255 0 24 10.745 24 24v160c0 13.255-10.745 24-24 24H296c-13.255 0-24-10.745-24-24V56c0-13.255 10.745-24 24-24zm-80 0H24C10.745 32 0 42.745 0 56v160c0 13.255 10.745 24 24 24h192c13.255 0 24-10.745 24-24V56c0-13.255-10.745-24-24-24zM0 296v160c0 13.255 10.745 24 24 24h192c13.255 0 24-10.745 24-24V296c0-13.255-10.745-24-24-24H24c-13.255 0-24 10.745-24 24zm296 184h192c13.255 0 24-10.745 24-24V296c0-13.255-10.745-24-24-24H296c-13.255 0-24 10.745-24 24v160c0 13.255 10.745 24 24 24z"></path></svg></button><button type="button" class="${ssrRenderClass(buttonClass(__props.rowValue))}"${ssrRenderAttr("title", unref(t)("horizontalView"))}><svg class="h-4 w-4 hover:text-blue-500" fill="currentColor" viewBox="0 0 512 512"><path d="M149.333 216v80c0 13.255-10.745 24-24 24H24c-13.255 0-24-10.745-24-24v-80c0-13.255 10.745-24 24-24h101.333c13.255 0 24 10.745 24 24zM0 376v80c0 13.255 10.745 24 24 24h101.333c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H24c-13.255 0-24 10.745-24 24zM125.333 32H24C10.745 32 0 42.745 0 56v80c0 13.255 10.745 24 24 24h101.333c13.255 0 24-10.745 24-24V56c0-13.255-10.745-24-24-24zm80 448H488c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24zm-24-424v80c0 13.255 10.745 24 24 24H488c13.255 0 24-10.745 24-24V56c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24zm24 264H488c13.255 0 24-10.745 24-24v-80c0-13.255-10.745-24-24-24H205.333c-13.255 0-24 10.745-24 24v80c0 13.255 10.745 24 24 24z"></path></svg></button></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Buttons/ViewModeToggle.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
