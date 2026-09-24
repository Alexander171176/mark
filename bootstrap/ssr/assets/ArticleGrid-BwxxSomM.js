import { computed, mergeProps, unref, withCtx, createVNode, toDisplayString, createBlock, openBlock, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderList, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { U as UniversalImageSlider } from "./UniversalImageSlider-Cu2Xndcn.js";
import { _ as _sfc_main$1 } from "./EntityStats-0c7h3PEr.js";
const _sfc_main = {
  __name: "ArticleGrid",
  __ssrInlineRender: true,
  props: {
    articles: { type: Array, default: () => [] },
    cols: { type: Number, default: 2 }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const gridClass = computed(() => {
      switch (props.cols) {
        case 4:
          return "grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4";
        case 3:
          return "grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3";
        case 2:
        default:
          return "grid grid-cols-1 gap-4 sm:grid-cols-2";
      }
    });
    const normalizedArticles = computed(() => {
      return Array.isArray(props.articles) ? props.articles : [];
    });
    const getTitle = (article) => {
      var _a;
      return ((_a = article == null ? void 0 : article.translation) == null ? void 0 : _a.title) || "";
    };
    const getShort = (article) => {
      var _a;
      return ((_a = article == null ? void 0 : article.translation) == null ? void 0 : _a.short) || "";
    };
    const getAuthorName = (article) => {
      var _a, _b;
      return ((_a = article == null ? void 0 : article.translation) == null ? void 0 : _a.pseudonym) || ((_b = article == null ? void 0 : article.owner) == null ? void 0 : _b.name) || "";
    };
    const getShowRoute = (article) => {
      return route("public.blogArticles.show", { url: article.url });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: gridClass.value,
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", normalizedArticles.value.length)}><!--[-->`);
      ssrRenderList(normalizedArticles.value, (article, index) => {
        var _a;
        _push(`<div itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="group flex h-full flex-col overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900"><meta itemprop="position"${ssrRenderAttr("content", index + 1)}><article itemprop="item" itemscope itemtype="https://schema.org/BlogPosting"${ssrRenderAttr("itemid", getShowRoute(article))} class="contents"><meta itemprop="url"${ssrRenderAttr("content", getShowRoute(article))}><meta itemprop="headline"${ssrRenderAttr("content", getTitle(article))}>`);
        if (getShort(article)) {
          _push(`<meta itemprop="description"${ssrRenderAttr("content", getShort(article))}>`);
        } else {
          _push(`<!---->`);
        }
        if (article.published_at) {
          _push(`<meta itemprop="datePublished"${ssrRenderAttr("content", article.published_at)}>`);
        } else {
          _push(`<!---->`);
        }
        if (getAuthorName(article)) {
          _push(`<div itemprop="author" itemscope itemtype="https://schema.org/Person" class="contents"><meta itemprop="name"${ssrRenderAttr("content", getAuthorName(article))}></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter" class="contents"><link itemprop="interactionType" href="https://schema.org/ViewAction"><meta itemprop="userInteractionCount"${ssrRenderAttr("content", article.views || 0)}></div><div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter" class="contents"><link itemprop="interactionType" href="https://schema.org/LikeAction"><meta itemprop="userInteractionCount"${ssrRenderAttr("content", article.likes_count || 0)}></div>`);
        _push(ssrRenderComponent(unref(Link), {
          href: getShowRoute(article)
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(UniversalImageSlider, {
                entity: article,
                "height-class": "h-48",
                "rounded-class": "",
                "wrapper-class": "",
                "img-class": "w-full h-full object-cover transition\n                               duration-300 group-hover:scale-105"
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(UniversalImageSlider, {
                  entity: article,
                  "height-class": "h-48",
                  "rounded-class": "",
                  "wrapper-class": "",
                  "img-class": "w-full h-full object-cover transition\n                               duration-300 group-hover:scale-105"
                }, null, 8, ["entity"])
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`<div class="flex flex-1 flex-col p-4"><div class="flex items-center justify-center text-center">`);
        _push(ssrRenderComponent(unref(Link), {
          href: getShowRoute(article),
          class: "inline-flex items-center gap-1"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span itemprop="headline" class="text-lg font-semibold text-slate-900/85 text-center group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"${_scopeId}>${ssrInterpolate(getTitle(article))}</span>`);
            } else {
              return [
                createVNode("span", {
                  itemprop: "headline",
                  class: "text-lg font-semibold text-slate-900/85 text-center group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"
                }, toDisplayString(getTitle(article)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div>`);
        if (getShort(article)) {
          _push(`<div itemprop="description" class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(getShort(article))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if ((article == null ? void 0 : article.owner) || getAuthorName(article)) {
          _push(`<div class="mt-4 flex items-center justify-center gap-2">`);
          if ((_a = article.owner) == null ? void 0 : _a.profile_photo_url) {
            _push(`<img${ssrRenderAttr("src", article.owner.profile_photo_url)}${ssrRenderAttr("alt", getAuthorName(article))} loading="lazy" class="h-6 w-6 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700">`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85">${ssrInterpolate(getAuthorName(article))}</div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex items-center justify-center">`);
        _push(ssrRenderComponent(_sfc_main$1, {
          views: article.views || 0,
          "likes-count": article.likes_count || 0,
          "already-liked": article.already_liked || false,
          "route-name": "public.blogArticles.like",
          "route-params": { id: article.id },
          "show-likes-button": true,
          compact: ""
        }, null, _parent));
        _push(`</div><div class="mt-auto pt-4">`);
        _push(ssrRenderComponent(unref(Link), {
          href: getShowRoute(article),
          class: "flex w-full items-center justify-center gap-2 rounded-sm px-3 py-2 btn-default"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-sm font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("readMore"))}</span><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"${_scopeId}><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z" clip-rule="evenodd"${_scopeId}></path></svg>`);
            } else {
              return [
                createVNode("span", { class: "text-sm font-semibold" }, toDisplayString(unref(t)("readMore")), 1),
                (openBlock(), createBlock("svg", {
                  class: "h-4 w-4",
                  viewBox: "0 0 20 20",
                  fill: "currentColor"
                }, [
                  createVNode("path", {
                    "fill-rule": "evenodd",
                    d: "M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z",
                    "clip-rule": "evenodd"
                  })
                ]))
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div></div></article></div>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogArticle/ArticleGrid.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
