import { ref, computed, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderList, ssrRenderClass, ssrInterpolate, ssrIncludeBooleanAttr } from "vue/server-renderer";
import "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
const _sfc_main = {
  __name: "ProcessingModeSwitcher",
  __ssrInlineRender: true,
  props: {
    mode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    total: { type: Number, default: 0 },
    settingKey: { type: String, required: true }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const processing = ref(false);
    const modes = computed(() => [
      { value: "frontend", label: t("frontend") },
      { value: "auto", label: t("auto") },
      { value: "server", label: t("server") }
    ]);
    const actualMode = computed(() => props.useServerProcessing ? "S" : "F");
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: "inline-flex items-center gap-2 text-[11px] font-semibold",
        title: `${__props.mode} / ${actualMode.value} / ${__props.total}`
      }, _attrs))}><div class="inline-flex overflow-hidden rounded-sm border border-slate-400 bg-white shadow-sm dark:border-slate-300 dark:bg-slate-800"><!--[-->`);
      ssrRenderList(modes.value, (item) => {
        _push(`<button type="button"${ssrIncludeBooleanAttr(processing.value) ? " disabled" : ""} class="${ssrRenderClass([__props.mode === item.value ? "bg-blue-600 text-white border-2 border-blue-100 dark:border-blue-900" : "text-slate-600 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-700", "px-2.5 py-0.5 transition disabled:opacity-50"])}">${ssrInterpolate(item.label)}</button>`);
      });
      _push(`<!--]--></div><div class="inline-flex items-center gap-1 rounded-sm border border-slate-400 bg-white px-2.5 py-1 shadow-sm text-slate-600 dark:border-slate-300 dark:bg-slate-800 dark:text-slate-200"><span class="${ssrRenderClass([__props.useServerProcessing ? "text-blue-600 dark:text-blue-300" : "text-red-500 dark:text-red-200", "font-bold"])}">${ssrInterpolate(actualMode.value)}</span><span class="text-slate-700 dark:text-slate-300">/ ${ssrInterpolate(__props.total)}</span></div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Processing/ProcessingModeSwitcher.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
