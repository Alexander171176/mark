import { computed, mergeProps, useSSRContext } from "vue";
import { ssrRenderAttrs } from "vue/server-renderer";
const _sfc_main$2 = {
  __name: "LeftToggle",
  __ssrInlineRender: true,
  props: {
    isActive: Boolean,
    title: String
  },
  emits: ["toggle-left"],
  setup(__props, { emit: __emit }) {
    const buttonClass = computed(() => [
      "flex items-center"
    ]);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        class: buttonClass.value,
        title: __props.title
      }, _attrs))}>`);
      if (__props.isActive) {
        _push(`<svg class="w-6 h-6 fill-current text-amber-500 hover:text-slate-700 dark:hover:text-slate-900" stroke="currentColor" viewBox="0 0 24 24"><path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path><path class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`);
      } else {
        _push(`<svg class="w-6 h-6 fill-current text-gray-500 hover:text-rose-500 dark:hover:text-red-400" stroke="currentColor" viewBox="0 0 24 24"><path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path><path class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`);
      }
      _push(`</button>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/LeftToggle.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "MainToggle",
  __ssrInlineRender: true,
  props: {
    isActive: Boolean,
    title: String
  },
  emits: ["toggle-main"],
  setup(__props, { emit: __emit }) {
    const buttonClass = computed(() => [
      "flex items-center"
    ]);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        class: buttonClass.value,
        title: __props.title
      }, _attrs))}>`);
      if (__props.isActive) {
        _push(`<svg class="w-4 h-4 fill-current text-rose-500 hover:text-slate-700 dark:hover:text-slate-900" viewBox="0 0 12 12"><path d="M6 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2A6 6 0 1 1 6 0a6 6 0 0 1 0 12Z"></path></svg>`);
      } else {
        _push(`<svg class="w-4 h-4 fill-current text-gray-500 hover:text-rose-500 dark:hover:text-red-400" viewBox="0 0 12 12"><path d="M6 10a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2A6 6 0 1 1 6 0a6 6 0 0 1 0 12Z"></path></svg>`);
      }
      _push(`</button>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/MainToggle.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "RightToggle",
  __ssrInlineRender: true,
  props: {
    isActive: Boolean,
    title: String
  },
  emits: ["toggle-right"],
  setup(__props, { emit: __emit }) {
    const buttonClass = computed(() => [
      "flex items-center"
    ]);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        class: buttonClass.value,
        title: __props.title
      }, _attrs))}>`);
      if (__props.isActive) {
        _push(`<svg class="w-6 h-6 fill-current text-amber-500 hover:text-slate-700 dark:hover:text-slate-900" stroke="currentColor" viewBox="0 0 24 24"><path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path><path class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`);
      } else {
        _push(`<svg class="w-6 h-6 fill-current text-gray-500 hover:text-rose-500 dark:hover:text-red-400" stroke="currentColor" viewBox="0 0 24 24"><path class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path><path class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>`);
      }
      _push(`</button>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/RightToggle.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$2 as _,
  _sfc_main$1 as a,
  _sfc_main as b
};
