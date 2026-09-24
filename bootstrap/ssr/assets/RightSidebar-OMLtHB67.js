import { computed, ref, onMounted, resolveComponent, mergeProps, unref, withCtx, createVNode, toDisplayString, createTextVNode, useSSRContext, onBeforeUnmount, watch, createBlock, createCommentVNode, openBlock, Transition, Fragment, renderList, withModifiers } from "vue";
import { ssrRenderAttrs, ssrRenderStyle, ssrRenderClass, ssrInterpolate, ssrRenderComponent, ssrRenderList, ssrRenderAttr } from "vue/server-renderer";
import { Link, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as unwrapList, a as unwrap, B as BannersSidebar, b as _sfc_main$6 } from "./SectionBanners-Cdgmr0Bw.js";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
const STORAGE_KEY = "rubricAccordionOpen";
const _sfc_main$5 = /* @__PURE__ */ Object.assign({
  name: "RubricTreeItem"
}, {
  __name: "RubricTreeItem",
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
    const props = __props;
    const { t } = useI18n();
    const hasChildren = computed(
      () => {
        var _a;
        return Array.isArray((_a = props.item) == null ? void 0 : _a.children) && props.item.children.length > 0;
      }
    );
    const hasSvgIcon = computed(() => {
      var _a;
      const icon = (_a = props.item) == null ? void 0 : _a.icon;
      if (!icon) {
        return false;
      }
      return /^\s*<svg[\s\S]*<\/svg>\s*$/i.test(
        String(icon)
      );
    });
    const rubricUrl = computed(
      () => {
        var _a;
        return route(
          "public.blogRubrics.show",
          {
            url: (_a = props.item) == null ? void 0 : _a.url
          }
        );
      }
    );
    const isOpen = ref(false);
    const getStoredOpenIds = () => {
      try {
        const raw = localStorage.getItem(STORAGE_KEY);
        if (!raw) {
          return [];
        }
        const ids = JSON.parse(raw);
        return Array.isArray(ids) ? ids : [];
      } catch {
        return [];
      }
    };
    const loadState = () => {
      const openIds = getStoredOpenIds();
      isOpen.value = openIds.includes(props.item.id);
    };
    onMounted(() => {
      if (hasChildren.value) {
        loadState();
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_RubricTreeItem = resolveComponent("RubricTreeItem", true);
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-full" }, _attrs))}><div class="flex items-center justify-between gap-0.5 my-0.5 rounded-sm transition hover:bg-slate-200 dark:hover:bg-slate-800" style="${ssrRenderStyle({
        paddingLeft: `${__props.depth * 16}px`
      })}"><div class="flex min-w-0 flex-1 items-center gap-2 pr-2 py-1">`);
      if (hasChildren.value) {
        _push(`<button type="button" class="flex min-w-0 flex-1 items-center justify-start gap-1 text-left"><svg class="${ssrRenderClass([{
          "rotate-90": isOpen.value
        }, "h-3.5 w-3.5 shrink-0 text-gray-500 dark:text-gray-400 transition-transform duration-200"])}" viewBox="0 0 320 512" fill="currentColor"><path d="M96 96l128 160L96 416z"></path></svg><span class="truncate text-xs font-semibold text-gray-700 dark:text-gray-300">${ssrInterpolate(__props.item.title)}</span></button>`);
      } else {
        _push(ssrRenderComponent(unref(Link), {
          href: rubricUrl.value,
          class: "min-w-0 flex-1"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="truncate text-xs font-semibold text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(__props.item.title)}</span>`);
            } else {
              return [
                createVNode("span", { class: "truncate text-xs font-semibold text-gray-700 dark:text-gray-300" }, toDisplayString(__props.item.title), 1)
              ];
            }
          }),
          _: 1
        }, _parent));
      }
      if (hasSvgIcon.value) {
        _push(`<span class="flex h-4 w-4 items-center justify-center shrink-0">${__props.item.icon ?? ""}</span>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (hasChildren.value) {
        _push(ssrRenderComponent(unref(Link), {
          href: rubricUrl.value,
          class: "mr-2 shrink-0 rounded-sm px-2 py-1 text-[10px] font-semibold text-indigo-700 dark:text-indigo-300 hover:text-slate-100 hover:bg-indigo-500 dark:hover:bg-indigo-500",
          title: unref(t)("openLink")
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(` → `);
            } else {
              return [
                createTextVNode(" → ")
              ];
            }
          }),
          _: 1
        }, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (hasChildren.value && isOpen.value) {
        _push(`<div class="overflow-hidden"><!--[-->`);
        ssrRenderList(__props.item.children, (child) => {
          _push(ssrRenderComponent(_component_RubricTreeItem, {
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
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogRubric/RubricTreeItem.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "RubricsSidebar",
  __ssrInlineRender: true,
  props: {
    rubrics: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      if (__props.rubrics.length) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-full space-y-1" }, _attrs))}><!--[-->`);
        ssrRenderList(__props.rubrics, (rubric) => {
          _push(ssrRenderComponent(_sfc_main$5, {
            key: rubric.id,
            item: rubric,
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
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogRubric/RubricsSidebar.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "ArticlesSidebar",
  __ssrInlineRender: true,
  props: {
    articles: {
      type: [Array, Object],
      default: () => []
    },
    intervalMs: {
      type: Number,
      default: 4200
    },
    pauseOnHover: {
      type: Boolean,
      default: true
    },
    pauseOnHidden: {
      type: Boolean,
      default: true
    }
  },
  setup(__props) {
    const props = __props;
    const list = computed(
      () => unwrapList(props.articles)
    );
    const getArticleTitle = (article) => {
      var _a;
      const item = unwrap(article);
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || "";
    };
    const getArticleUrl = (article) => {
      const item = unwrap(article);
      return route(
        "public.blogArticles.show",
        {
          url: item == null ? void 0 : item.url
        }
      );
    };
    const articleImages = (article) => {
      var _a;
      const item = unwrap(article);
      const imagesRaw = Array.isArray(item == null ? void 0 : item.images) ? item.images : ((_a = item == null ? void 0 : item.images) == null ? void 0 : _a.data) ?? [];
      return (Array.isArray(imagesRaw) ? imagesRaw : []).slice().sort(
        (a, b) => Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0)
      ).map((image, index) => {
        const src = (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || (image == null ? void 0 : image.src) || (image == null ? void 0 : image.path) || (image == null ? void 0 : image.image) || "";
        return {
          id: (image == null ? void 0 : image.id) ?? `${src || "image"}-${(image == null ? void 0 : image.order) ?? index}`,
          src,
          alt: (image == null ? void 0 : image.alt) || getArticleTitle(article),
          title: (image == null ? void 0 : image.title) || (image == null ? void 0 : image.alt) || getArticleTitle(article),
          order: Number(
            (image == null ? void 0 : image.order) ?? 0
          )
        };
      }).filter((image) => Boolean(image.src));
    };
    const currentByArticle = ref({});
    const hoveredByArticle = ref({});
    let timer = null;
    const getCurrent = (articleId) => {
      var _a;
      return Number(
        ((_a = currentByArticle.value) == null ? void 0 : _a[articleId]) ?? 0
      );
    };
    const setCurrent = (articleId, index, total) => {
      const count = Number(total) || 0;
      if (count <= 1) {
        currentByArticle.value = {
          ...currentByArticle.value,
          [articleId]: 0
        };
        return;
      }
      const value = Number(index);
      const safeIndex = Number.isFinite(value) ? Math.min(
        Math.max(0, value),
        count - 1
      ) : 0;
      currentByArticle.value = {
        ...currentByArticle.value,
        [articleId]: safeIndex
      };
    };
    const canRun = () => {
      return !(props.pauseOnHidden && typeof document !== "undefined" && document.hidden);
    };
    const tick = () => {
      var _a;
      if (!canRun()) {
        return;
      }
      const nextState = {
        ...currentByArticle.value
      };
      for (const articleItem of list.value) {
        const article = unwrap(articleItem);
        const id = article == null ? void 0 : article.id;
        if (!id) {
          continue;
        }
        const images = articleImages(articleItem);
        if (images.length <= 1) {
          continue;
        }
        if (props.pauseOnHover && ((_a = hoveredByArticle.value) == null ? void 0 : _a[id])) {
          continue;
        }
        const current = Number(nextState[id] ?? 0);
        nextState[id] = (current + 1) % images.length;
      }
      currentByArticle.value = nextState;
    };
    const stop = () => {
      if (!timer) {
        return;
      }
      clearInterval(timer);
      timer = null;
    };
    const start = () => {
      stop();
      const hasAnySlider = list.value.some(
        (article) => articleImages(article).length > 1
      );
      if (!hasAnySlider) {
        return;
      }
      timer = setInterval(
        tick,
        Math.max(
          1500,
          Number(props.intervalMs) || 4200
        )
      );
    };
    const resetState = () => {
      var _a;
      const initialState = {};
      for (const article of list.value) {
        const id = (_a = unwrap(article)) == null ? void 0 : _a.id;
        if (id) {
          initialState[id] = 0;
        }
      }
      currentByArticle.value = initialState;
    };
    const onVisibilityChange = () => {
      start();
    };
    onMounted(() => {
      resetState();
      start();
      if (props.pauseOnHidden && typeof document !== "undefined") {
        document.addEventListener(
          "visibilitychange",
          onVisibilityChange
        );
      }
    });
    onBeforeUnmount(() => {
      stop();
      if (props.pauseOnHidden && typeof document !== "undefined") {
        document.removeEventListener(
          "visibilitychange",
          onVisibilityChange
        );
      }
    });
    watch(
      () => [
        list.value.length,
        props.intervalMs
      ],
      () => {
        resetState();
        start();
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      if (list.value.length) {
        _push(`<div${ssrRenderAttrs(_attrs)} data-v-7c1d3626><!--[-->`);
        ssrRenderList(list.value, (article) => {
          _push(`<div data-v-7c1d3626><div class="mb-4" data-v-7c1d3626>`);
          _push(ssrRenderComponent(unref(Link), {
            href: getArticleUrl(article),
            class: "flex gap-2"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              var _a, _b, _c;
              if (_push2) {
                if (articleImages(article).length > 0) {
                  _push2(`<div class="post-image relative overflow-hidden rounded-md bg-slate-100 dark:bg-slate-900 w-auto h-[64px] shrink-0" data-v-7c1d3626${_scopeId}><img class="w-full h-full object-cover"${ssrRenderAttr(
                    "src",
                    (_a = articleImages(article)[getCurrent(
                      unref(unwrap)(article).id
                    )]) == null ? void 0 : _a.src
                  )}${ssrRenderAttr(
                    "alt",
                    (_b = articleImages(article)[getCurrent(
                      unref(unwrap)(article).id
                    )]) == null ? void 0 : _b.alt
                  )}${ssrRenderAttr(
                    "title",
                    (_c = articleImages(article)[getCurrent(
                      unref(unwrap)(article).id
                    )]) == null ? void 0 : _c.title
                  )} loading="lazy" data-v-7c1d3626${_scopeId}>`);
                  if (articleImages(article).length > 1) {
                    _push2(`<div class="absolute left-0 right-0 bottom-0 px-1 pb-1" data-v-7c1d3626${_scopeId}><div class="flex items-center justify-center gap-1" data-v-7c1d3626${_scopeId}><!--[-->`);
                    ssrRenderList(articleImages(article), (image, index) => {
                      _push2(`<button type="button" class="${ssrRenderClass([
                        index === getCurrent(
                          unref(unwrap)(article).id
                        ) ? "bg-orange-400 shadow ring-1 ring-black/40" : "bg-white/60 hover:bg-orange-400/80",
                        "h-1.5 w-1.5 rounded-full transition-all"
                      ])}"${ssrRenderAttr("aria-label", `image ${index + 1}`)}${ssrRenderAttr("title", image.title)} data-v-7c1d3626${_scopeId}></button>`);
                    });
                    _push2(`<!--]--></div></div>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  _push2(`</div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`<h3 class="title" data-v-7c1d3626${_scopeId}><span class="font-semibold text-xs text-gray-700 dark:text-gray-300 hover:text-indigo-600" data-v-7c1d3626${_scopeId}>${ssrInterpolate(getArticleTitle(article))}</span></h3>`);
              } else {
                return [
                  articleImages(article).length > 0 ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "post-image relative overflow-hidden rounded-md bg-slate-100 dark:bg-slate-900 w-auto h-[64px] shrink-0",
                    onMouseenter: ($event) => hoveredByArticle.value = {
                      ...hoveredByArticle.value,
                      [unref(unwrap)(article).id]: true
                    },
                    onMouseleave: ($event) => hoveredByArticle.value = {
                      ...hoveredByArticle.value,
                      [unref(unwrap)(article).id]: false
                    }
                  }, [
                    createVNode(Transition, {
                      name: "imgfx",
                      mode: "out-in"
                    }, {
                      default: withCtx(() => {
                        var _a2, _b2, _c2, _d;
                        return [
                          (openBlock(), createBlock("img", {
                            key: (_a2 = articleImages(article)[getCurrent(
                              unref(unwrap)(article).id
                            )]) == null ? void 0 : _a2.id,
                            class: "w-full h-full object-cover",
                            src: (_b2 = articleImages(article)[getCurrent(
                              unref(unwrap)(article).id
                            )]) == null ? void 0 : _b2.src,
                            alt: (_c2 = articleImages(article)[getCurrent(
                              unref(unwrap)(article).id
                            )]) == null ? void 0 : _c2.alt,
                            title: (_d = articleImages(article)[getCurrent(
                              unref(unwrap)(article).id
                            )]) == null ? void 0 : _d.title,
                            loading: "lazy"
                          }, null, 8, ["src", "alt", "title"]))
                        ];
                      }),
                      _: 2
                    }, 1024),
                    articleImages(article).length > 1 ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "absolute left-0 right-0 bottom-0 px-1 pb-1"
                    }, [
                      createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(articleImages(article), (image, index) => {
                          return openBlock(), createBlock("button", {
                            key: image.id,
                            type: "button",
                            class: [
                              "h-1.5 w-1.5 rounded-full transition-all",
                              index === getCurrent(
                                unref(unwrap)(article).id
                              ) ? "bg-orange-400 shadow ring-1 ring-black/40" : "bg-white/60 hover:bg-orange-400/80"
                            ],
                            "aria-label": `image ${index + 1}`,
                            title: image.title,
                            onClick: withModifiers(($event) => setCurrent(
                              unref(unwrap)(article).id,
                              index,
                              articleImages(article).length
                            ), ["prevent", "stop"])
                          }, null, 10, ["aria-label", "title", "onClick"]);
                        }), 128))
                      ])
                    ])) : createCommentVNode("", true)
                  ], 40, ["onMouseenter", "onMouseleave"])) : createCommentVNode("", true),
                  createVNode("h3", { class: "title" }, [
                    createVNode("span", { class: "font-semibold text-xs text-gray-700 dark:text-gray-300 hover:text-indigo-600" }, toDisplayString(getArticleTitle(article)), 1)
                  ])
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div></div>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogArticle/ArticlesSidebar.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const ArticlesSidebar = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["__scopeId", "data-v-7c1d3626"]]);
const _sfc_main$2 = {
  __name: "LeftSidebar",
  __ssrInlineRender: true,
  props: {
    rubricTree: {
      type: Array,
      default: () => []
    },
    collapsed: {
      type: Boolean,
      default: false
    }
  },
  emits: [
    "collapsed"
  ],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const { t } = useI18n();
    const page = usePage();
    const leftArticles = computed(
      () => page.props.leftArticles ?? []
    );
    const leftBanners = computed(
      () => page.props.leftBanners ?? []
    );
    const leftVideos = computed(
      () => page.props.leftVideos ?? []
    );
    const isCollapsed = computed(
      () => props.collapsed
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative" }, _attrs))}><div class="flex items-center justify-center mb-2"><button type="button" class="focus:outline-none"${ssrRenderAttr("title", unref(t)("toggleSidebar"))}>`);
      if (isCollapsed.value) {
        _push(`<svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="currentColor" viewBox="0 0 448 512"><path d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"></path></svg>`);
      } else {
        _push(`<svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="currentColor" viewBox="0 0 448 512"><path d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"></path></svg>`);
      }
      _push(`</button></div><div style="${ssrRenderStyle(!isCollapsed.value ? null : { display: "none" })}" class="flex flex-col gap-4">`);
      _push(ssrRenderComponent(_sfc_main$4, {
        rubrics: props.rubricTree
      }, null, _parent));
      _push(ssrRenderComponent(ArticlesSidebar, { articles: leftArticles.value }, null, _parent));
      _push(ssrRenderComponent(BannersSidebar, { banners: leftBanners.value }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$6, { videos: leftVideos.value }, null, _parent));
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Partials/LeftSidebar.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "TagsSidebar",
  __ssrInlineRender: true,
  props: {
    tags: {
      type: [Array, Object],
      default: () => []
    }
  },
  setup(__props) {
    const props = __props;
    const list = computed(
      () => unwrapList(props.tags)
    );
    const getTagName = (tag) => {
      var _a;
      const item = unwrap(tag);
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.name) || "";
    };
    const getTagUrl = (tag) => {
      const item = unwrap(tag);
      return route(
        "public.blogTags.show",
        {
          slug: item == null ? void 0 : item.slug
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      if (list.value.length) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-wrap items-center gap-2" }, _attrs))}><!--[-->`);
        ssrRenderList(list.value, (tag) => {
          _push(ssrRenderComponent(unref(Link), {
            key: unref(unwrap)(tag).id,
            href: getTagUrl(tag),
            class: "flex items-center justify-start gap-2 px-3 py-1 transition rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 dark:hover:text-slate-300 border border-gray-400 dark:border-gray-400 hover:bg-slate-200 dark:hover:bg-slate-800"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(getTagName(tag))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(getTagName(tag)), 1)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogTag/TagsSidebar.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "RightSidebar",
  __ssrInlineRender: true,
  props: {
    collapsed: {
      type: Boolean,
      default: false
    }
  },
  emits: [
    "collapsed"
  ],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const { t } = useI18n();
    const page = usePage();
    const tags = computed(
      () => page.props.tags ?? []
    );
    const rightArticles = computed(
      () => page.props.rightArticles ?? []
    );
    const rightBanners = computed(
      () => page.props.rightBanners ?? []
    );
    const rightVideos = computed(
      () => page.props.rightVideos ?? []
    );
    const isCollapsed = computed(
      () => props.collapsed
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative" }, _attrs))}><div class="flex items-center justify-center mb-2"><button type="button" class="focus:outline-none"${ssrRenderAttr("title", unref(t)("toggleSidebar"))}>`);
      if (isCollapsed.value) {
        _push(`<svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="currentColor" viewBox="0 0 448 512"><path d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"></path></svg>`);
      } else {
        _push(`<svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="currentColor" viewBox="0 0 448 512"><path d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"></path></svg>`);
      }
      _push(`</button></div><div style="${ssrRenderStyle(!isCollapsed.value ? null : { display: "none" })}" class="flex flex-col gap-4">`);
      _push(ssrRenderComponent(_sfc_main$1, { tags: tags.value }, null, _parent));
      _push(ssrRenderComponent(ArticlesSidebar, { articles: rightArticles.value }, null, _parent));
      _push(ssrRenderComponent(BannersSidebar, { banners: rightBanners.value }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$6, { videos: rightVideos.value }, null, _parent));
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Partials/RightSidebar.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$2 as _,
  _sfc_main as a
};
