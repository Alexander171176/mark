import { computed, ref, resolveComponent, mergeProps, unref, withCtx, createBlock, createVNode, createCommentVNode, openBlock, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderStyle, ssrRenderClass, ssrRenderComponent, ssrRenderAttr, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { Link, usePage } from "@inertiajs/vue3";
const TREE_OPEN_KEY = "public_market_category_tree_open_ids";
const _sfc_main$4 = /* @__PURE__ */ Object.assign({
  name: "MarketCategoryTreeItem"
}, {
  __name: "MarketCategoryTreeItem",
  __ssrInlineRender: true,
  props: {
    item: {
      type: Object,
      required: true
    },
    depth: {
      type: Number,
      default: 0
    }
  },
  setup(__props) {
    var _a;
    const props = __props;
    const getStoredOpenIds = () => {
      try {
        const value = localStorage.getItem(TREE_OPEN_KEY);
        if (!value) {
          return [];
        }
        const ids = JSON.parse(value);
        return Array.isArray(ids) ? ids.map(Number) : [];
      } catch {
        return [];
      }
    };
    computed(() => {
      var _a2;
      return Number((_a2 = props.item) == null ? void 0 : _a2.id);
    });
    const isOpen = ref(
      getStoredOpenIds().includes(
        Number((_a = props.item) == null ? void 0 : _a.id)
      )
    );
    const children = computed(() => {
      var _a2;
      return Array.isArray((_a2 = props.item) == null ? void 0 : _a2.children) ? props.item.children : [];
    });
    const hasChildren = computed(() => {
      return children.value.length > 0;
    });
    const categoryLink = computed(() => {
      var _a2;
      return ((_a2 = props.item) == null ? void 0 : _a2.url) ? route(
        "public.marketCategories.show",
        { url: props.item.url }
      ) : "#";
    });
    const hasSvgIcon = computed(() => {
      var _a2;
      return typeof ((_a2 = props.item) == null ? void 0 : _a2.icon) === "string" && props.item.icon.trim().startsWith("<svg");
    });
    const itemPadding = computed(() => {
      return {
        paddingLeft: `${props.depth * 12}px`
      };
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_MarketCategoryTreeItem = resolveComponent("MarketCategoryTreeItem", true);
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-full" }, _attrs))}><div class="group flex items-center gap-1 rounded-sm transition hover:bg-slate-100 dark:hover:bg-slate-800" style="${ssrRenderStyle(itemPadding.value)}">`);
      if (hasChildren.value) {
        _push(`<button type="button" class="flex h-6 w-6 shrink-0 items-center justify-center text-slate-500 hover:text-indigo-600 dark:text-slate-400 dark:hover:text-indigo-400"><svg class="${ssrRenderClass([{ "rotate-90": isOpen.value }, "h-3 w-3 transition-transform duration-200"])}" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z" clip-rule="evenodd"></path></svg></button>`);
      } else {
        _push(`<span class="block h-6 w-6 shrink-0"></span>`);
      }
      _push(ssrRenderComponent(unref(Link), {
        href: categoryLink.value,
        class: "flex min-w-0 flex-1 items-center gap-2 rounded-sm px-1 py-2",
        title: __props.item.title
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            if (__props.item.thumbnail_url) {
              _push2(`<img${ssrRenderAttr("src", __props.item.thumbnail_url)}${ssrRenderAttr("alt", __props.item.title)} loading="lazy" class="h-6 w-6 shrink-0 rounded-sm object-cover"${_scopeId}>`);
            } else if (hasSvgIcon.value) {
              _push2(`<span class="flex h-5 w-5 shrink-0 items-center justify-center"${_scopeId}>${__props.item.icon ?? ""}</span>`);
            } else {
              _push2(`<svg class="h-4 w-4 shrink-0 text-slate-500 dark:text-slate-400" viewBox="0 0 24 24" fill="currentColor"${_scopeId}><path d="M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z"${_scopeId}></path></svg>`);
            }
            _push2(`<span class="min-w-0 flex-1 truncate text-sm font-semibold text-slate-700 group-hover:text-indigo-600 dark:text-slate-300 dark:group-hover:text-indigo-400"${_scopeId}>${ssrInterpolate(__props.item.title)}</span>`);
            if (hasChildren.value) {
              _push2(`<span class="shrink-0 text-[11px] font-semibold text-slate-400 dark:text-slate-500"${_scopeId}>${ssrInterpolate(children.value.length)}</span>`);
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              __props.item.thumbnail_url ? (openBlock(), createBlock("img", {
                key: 0,
                src: __props.item.thumbnail_url,
                alt: __props.item.title,
                loading: "lazy",
                class: "h-6 w-6 shrink-0 rounded-sm object-cover"
              }, null, 8, ["src", "alt"])) : hasSvgIcon.value ? (openBlock(), createBlock("span", {
                key: 1,
                class: "flex h-5 w-5 shrink-0 items-center justify-center",
                innerHTML: __props.item.icon
              }, null, 8, ["innerHTML"])) : (openBlock(), createBlock("svg", {
                key: 2,
                class: "h-4 w-4 shrink-0 text-slate-500 dark:text-slate-400",
                viewBox: "0 0 24 24",
                fill: "currentColor"
              }, [
                createVNode("path", { d: "M4 4h6v6H4V4Zm10 0h6v6h-6V4ZM4 14h6v6H4v-6Zm10 0h6v6h-6v-6Z" })
              ])),
              createVNode("span", { class: "min-w-0 flex-1 truncate text-sm font-semibold text-slate-700 group-hover:text-indigo-600 dark:text-slate-300 dark:group-hover:text-indigo-400" }, toDisplayString(__props.item.title), 1),
              hasChildren.value ? (openBlock(), createBlock("span", {
                key: 3,
                class: "shrink-0 text-[11px] font-semibold text-slate-400 dark:text-slate-500"
              }, toDisplayString(children.value.length), 1)) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div>`);
      if (isOpen.value && hasChildren.value) {
        _push(`<div class="space-y-1"><!--[-->`);
        ssrRenderList(children.value, (child) => {
          _push(ssrRenderComponent(_component_MarketCategoryTreeItem, {
            key: child.id,
            item: child,
            depth: __props.depth + 1
          }, null, _parent));
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
});
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketCategory/MarketCategoryTreeItem.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "CategoriesSidebar",
  __ssrInlineRender: true,
  props: {
    categories: { type: Array, default: () => [] }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      if (__props.categories.length) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-full space-y-1" }, _attrs))}><!--[-->`);
        ssrRenderList(__props.categories, (category) => {
          _push(ssrRenderComponent(_sfc_main$4, {
            key: category.id,
            item: category,
            depth: 0
          }, null, _parent));
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketCategory/CategoriesSidebar.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "LeftSidebarMarket",
  __ssrInlineRender: true,
  props: {
    categoryTree: { type: Array, default: () => [] },
    collapsed: { type: Boolean, default: false }
  },
  emits: ["collapsed"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const isCollapsed = computed(() => props.collapsed);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative" }, _attrs))}><div class="flex items-center justify-center mb-2"><button type="button" class="focus:outline-none"${ssrRenderAttr("title", unref(t)("toggleSidebar"))}>`);
      if (isCollapsed.value) {
        _push(`<svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="currentColor" viewBox="0 0 448 512"><path d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"></path></svg>`);
      } else {
        _push(`<svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="currentColor" viewBox="0 0 448 512"><path d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"></path></svg>`);
      }
      _push(`</button></div><div style="${ssrRenderStyle(!isCollapsed.value ? null : { display: "none" })}" class="flex flex-col gap-4">`);
      _push(ssrRenderComponent(_sfc_main$3, { categories: __props.categoryTree }, null, _parent));
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Partials/LeftSidebarMarket.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "TagsSidebar",
  __ssrInlineRender: true,
  props: {
    tags: { type: [Array, Object], default: () => [] }
  },
  setup(__props) {
    const props = __props;
    const tagsList = computed(() => {
      var _a;
      if (Array.isArray(props.tags)) {
        return props.tags;
      }
      if (Array.isArray((_a = props.tags) == null ? void 0 : _a.data)) {
        return props.tags.data;
      }
      return [];
    });
    const tagLink = (tag) => {
      return (tag == null ? void 0 : tag.url) ? route("public.marketTags.show", { url: tag.url }) : "#";
    };
    const getTagTitle = (tag) => {
      var _a, _b, _c;
      return (tag == null ? void 0 : tag.title) || ((_a = tag == null ? void 0 : tag.translation) == null ? void 0 : _a.title) || ((_c = (_b = tag == null ? void 0 : tag.translations) == null ? void 0 : _b[0]) == null ? void 0 : _c.title) || "";
    };
    const hasSvgIcon = (tag) => {
      return typeof (tag == null ? void 0 : tag.icon) === "string" && tag.icon.trim().startsWith("<svg");
    };
    return (_ctx, _push, _parent, _attrs) => {
      if (tagsList.value.length) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-wrap items-center gap-2" }, _attrs))}><!--[-->`);
        ssrRenderList(tagsList.value, (tag) => {
          _push(ssrRenderComponent(unref(Link), {
            key: tag.id,
            href: tagLink(tag),
            title: getTagTitle(tag),
            class: "flex items-center justify-start gap-1.5 rounded-md border border-gray-400 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-slate-200 dark:border-gray-400 dark:text-gray-300 dark:hover:bg-slate-800 dark:hover:text-slate-300"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                if (hasSvgIcon(tag)) {
                  _push2(`<span class="flex h-3.5 w-3.5 shrink-0 items-center justify-center"${_scopeId}>${tag.icon ?? ""}</span>`);
                } else if (tag.color) {
                  _push2(`<span class="h-2.5 w-2.5 shrink-0 rounded-full" style="${ssrRenderStyle({ backgroundColor: tag.color })}"${_scopeId}></span>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`<span${_scopeId}> #${ssrInterpolate(getTagTitle(tag))}</span>`);
              } else {
                return [
                  hasSvgIcon(tag) ? (openBlock(), createBlock("span", {
                    key: 0,
                    class: "flex h-3.5 w-3.5 shrink-0 items-center justify-center",
                    innerHTML: tag.icon
                  }, null, 8, ["innerHTML"])) : tag.color ? (openBlock(), createBlock("span", {
                    key: 1,
                    class: "h-2.5 w-2.5 shrink-0 rounded-full",
                    style: { backgroundColor: tag.color }
                  }, null, 4)) : createCommentVNode("", true),
                  createVNode("span", null, " #" + toDisplayString(getTagTitle(tag)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Market/MarketTag/TagsSidebar.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "RightSidebarMarket",
  __ssrInlineRender: true,
  props: {
    collapsed: { type: Boolean, default: false }
  },
  emits: ["collapsed"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const page = usePage();
    const props = __props;
    const tags = computed(() => page.props.tags ?? []);
    const isCollapsed = computed(() => props.collapsed);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative" }, _attrs))}><div class="flex items-center justify-center mb-2"><button type="button" class="focus:outline-none"${ssrRenderAttr("title", unref(t)("toggleSidebar"))}>`);
      if (isCollapsed.value) {
        _push(`<svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="currentColor" viewBox="0 0 448 512"><path d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"></path></svg>`);
      } else {
        _push(`<svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="currentColor" viewBox="0 0 448 512"><path d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"></path></svg>`);
      }
      _push(`</button></div><div style="${ssrRenderStyle(!isCollapsed.value ? null : { display: "none" })}" class="flex flex-col gap-4">`);
      _push(ssrRenderComponent(_sfc_main$1, { tags: tags.value }, null, _parent));
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Partials/RightSidebarMarket.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$2 as _,
  _sfc_main as a
};
