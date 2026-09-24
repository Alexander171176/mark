import { ref, onMounted, onUnmounted, computed, mergeProps, unref, useSSRContext, withCtx, renderSlot, onBeforeUnmount, nextTick } from "vue";
import { ssrRenderAttrs, ssrRenderSlot, ssrRenderStyle, ssrRenderClass, ssrRenderAttr, ssrRenderComponent, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { Link, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
const _sfc_main$2 = {
  __name: "Dropdown",
  __ssrInlineRender: true,
  props: {
    align: {
      type: String,
      default: "right"
    },
    width: {
      type: String,
      default: "48"
    },
    contentClasses: {
      type: Array,
      default: () => ["py-0", "bg-white"]
    }
  },
  setup(__props) {
    const props = __props;
    let open = ref(false);
    const closeOnEscape = (e) => {
      if (open.value && e.key === "Escape") {
        open.value = false;
      }
    };
    onMounted(() => document.addEventListener("keydown", closeOnEscape));
    onUnmounted(() => document.removeEventListener("keydown", closeOnEscape));
    const widthClass = computed(() => {
      return {
        48: "w-48"
      }[props.width.toString()];
    });
    const alignmentClasses = computed(() => {
      if (props.align === "left") {
        return "ltr:origin-top-left rtl:origin-top-right start-0";
      }
      if (props.align === "right") {
        return "ltr:origin-top-right rtl:origin-top-left end-0";
      }
      return "origin-top";
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative" }, _attrs))}><div>`);
      ssrRenderSlot(_ctx.$slots, "trigger", {}, null, _push, _parent);
      _push(`</div><div style="${ssrRenderStyle(unref(open) ? null : { display: "none" })}" class="fixed inset-0 z-40"></div><div style="${ssrRenderStyle([
        unref(open) ? null : { display: "none" },
        { "display": "none" }
      ])}" class="${ssrRenderClass([[widthClass.value, alignmentClasses.value], "absolute z-50 mt-2 rounded-md shadow-lg"])}"><div class="${ssrRenderClass([__props.contentClasses, "w-fit dark:bg-slate-900 dark:border dark:border-gray-100 rounded-md ring-1 ring-black ring-opacity-5"])}">`);
      ssrRenderSlot(_ctx.$slots, "content", {}, null, _push, _parent);
      _push(`</div></div></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Base/Dropdown.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "DropdownLink",
  __ssrInlineRender: true,
  props: {
    href: String,
    as: String
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(_attrs)}>`);
      if (__props.as == "button") {
        _push(`<button type="submit" class="block w-full px-4 py-2 text-start text-md leading-5 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:text-slate-700 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">`);
        ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
        _push(`</button>`);
      } else if (__props.as == "a") {
        _push(`<a${ssrRenderAttr("href", __props.href)} class="block w-full px-4 py-2 text-md leading-5 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:text-slate-700 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">`);
        ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
        _push(`</a>`);
      } else {
        _push(ssrRenderComponent(unref(Link), {
          href: __props.href,
          class: "block w-full px-4 py-2 text-md leading-5 text-gray-700 dark:text-slate-300 hover:bg-gray-100 dark:hover:text-slate-700 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              ssrRenderSlot(_ctx.$slots, "default", {}, null, _push2, _parent2, _scopeId);
            } else {
              return [
                renderSlot(_ctx.$slots, "default")
              ];
            }
          }),
          _: 3
        }, _parent));
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Base/DropdownLink.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "LocaleSelectOption",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: String,
      default: ""
    },
    locales: {
      type: Array,
      default: () => []
    },
    placement: {
      type: String,
      default: "bottom-end"
    }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const page = usePage();
    const props = __props;
    const isOpen = ref(false);
    const dropdownRef = ref(null);
    const tooltipSide = ref("left");
    const localeTitles = {
      ru: "Русский",
      en: "English",
      kk: "Қазақ",
      zh: "中国人"
    };
    const getLocaleTitle = (code) => {
      const normalized = String(code || "").toLowerCase();
      return localeTitles[normalized] || normalized.toUpperCase();
    };
    const localesList = computed(() => {
      const sourceLocales = props.locales.length ? props.locales : page.props.availableLocales;
      if (!Array.isArray(sourceLocales) || !sourceLocales.length) {
        return [];
      }
      return sourceLocales.map((code) => String(code).trim().toLowerCase()).filter(Boolean).map((code) => ({
        code,
        flag: `/storage/flags/${code}.svg`,
        title: getLocaleTitle(code)
      }));
    });
    const selectedLocale = computed(
      () => localesList.value.find(
        (loc) => loc.code === String(props.modelValue || "").toLowerCase()
      ) || null
    );
    const dropdownClass = computed(() => {
      if (props.placement === "top-end") {
        return "absolute right-0 bottom-full mb-2 origin-bottom-right";
      }
      return "absolute right-0 top-full mt-2 origin-top-right";
    });
    const tooltipItemClass = computed(() => {
      if (tooltipSide.value === "right") {
        return "left-full ml-2";
      }
      return "right-full mr-2";
    });
    const updateTooltipSide = async () => {
      await nextTick();
      if (!dropdownRef.value) return;
      const rect = dropdownRef.value.getBoundingClientRect();
      const viewportWidth = window.innerWidth;
      const tooltipApproxWidth = 96;
      const freeRight = viewportWidth - rect.right;
      const freeLeft = rect.left;
      if (freeRight >= tooltipApproxWidth) {
        tooltipSide.value = "right";
        return;
      }
      if (freeLeft >= tooltipApproxWidth) {
        tooltipSide.value = "left";
        return;
      }
      tooltipSide.value = "left";
    };
    const closeDropdown = () => {
      isOpen.value = false;
    };
    const handleClickOutside = (event) => {
      if (!dropdownRef.value) return;
      if (!dropdownRef.value.contains(event.target)) {
        closeDropdown();
      }
    };
    const handleEscape = (event) => {
      if (event.key === "Escape") {
        closeDropdown();
      }
    };
    const handleResize = async () => {
      if (isOpen.value) {
        await updateTooltipSide();
      }
    };
    onMounted(() => {
      document.addEventListener("click", handleClickOutside);
      document.addEventListener("keydown", handleEscape);
      window.addEventListener("resize", handleResize);
    });
    onBeforeUnmount(() => {
      document.removeEventListener("click", handleClickOutside);
      document.removeEventListener("keydown", handleEscape);
      window.removeEventListener("resize", handleResize);
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        ref_key: "dropdownRef",
        ref: dropdownRef,
        class: "relative border border-gray-300 dark:border-gray-600"
      }, _attrs))}><div class="group relative"><button type="button" class="flex items-center justify-center w-10 h-8 rounded-md hover:bg-slate-200 dark:hover:bg-slate-700 transition"${ssrRenderAttr("aria-expanded", isOpen.value)}${ssrRenderAttr("aria-label", selectedLocale.value ? selectedLocale.value.title : unref(t)("selectLocale"))}>`);
      if (selectedLocale.value) {
        _push(`<img${ssrRenderAttr("src", selectedLocale.value.flag)}${ssrRenderAttr("alt", selectedLocale.value.code)} class="w-8 aspect-[4/3] object-cover rounded-sm">`);
      } else {
        _push(`<!---->`);
      }
      _push(`</button>`);
      if (selectedLocale.value) {
        _push(`<div class="pointer-events-none absolute left-1/2 top-full z-[60] mt-2 -translate-x-1/2 whitespace-nowrap rounded-md bg-slate-900 px-2 py-1 text-[11px] font-semibold text-white opacity-0 shadow-md transition duration-200 group-hover:opacity-100 dark:bg-slate-100 dark:text-slate-900">${ssrInterpolate(selectedLocale.value.title)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (isOpen.value) {
        _push(`<div class="${ssrRenderClass([dropdownClass.value, "z-50 rounded-md border border-slate-200 bg-white shadow-lg dark:border-slate-700 dark:bg-slate-900 flex flex-col gap-1"])}"><!--[-->`);
        ssrRenderList(localesList.value, (loc) => {
          _push(`<div class="group/item relative"><button type="button" class="${ssrRenderClass([String(loc.code).toLowerCase() === String(__props.modelValue || "").toLowerCase() ? "ring-2 ring-blue-500" : "", "flex items-center justify-center w-10 h-8 rounded-md hover:bg-slate-100 dark:hover:bg-slate-800 transition"])}"${ssrRenderAttr("aria-label", loc.title)}><img${ssrRenderAttr("src", loc.flag)}${ssrRenderAttr("alt", loc.code)} class="w-8 aspect-[4/3] object-cover rounded-sm"></button><div class="${ssrRenderClass([tooltipItemClass.value, "pointer-events-none absolute top-1/2 z-[60] -translate-y-1/2 whitespace-nowrap rounded-md bg-slate-900 px-2 py-1 text-[11px] font-semibold text-white opacity-0 shadow-md transition duration-200 group-hover/item:opacity-100 dark:bg-slate-100 dark:text-slate-900"])}">${ssrInterpolate(loc.title)}</div></div>`);
        });
        _push(`<!--]--></div>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Select/LocaleSelectOption.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$2 as _,
  _sfc_main$1 as a,
  _sfc_main as b
};
