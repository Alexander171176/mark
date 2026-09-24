import { computed, mergeProps, unref, withCtx, createVNode, toDisplayString, createBlock, openBlock, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderList, ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { V as VideoPlayer } from "./SectionBanners-Cdgmr0Bw.js";
import { _ as _sfc_main$1 } from "./EntityStats-0c7h3PEr.js";
const _sfc_main = {
  __name: "VideoGrid",
  __ssrInlineRender: true,
  props: {
    videos: {
      type: Array,
      default: () => []
    },
    cols: {
      type: Number,
      default: 3
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const getGridClass = () => {
      switch (props.cols) {
        case 1:
          return "grid-cols-1";
        case 2:
          return "grid-cols-1 sm:grid-cols-2";
        case 3:
          return "grid-cols-1 sm:grid-cols-2 xl:grid-cols-3";
        case 4:
          return "grid-cols-1 sm:grid-cols-2 xl:grid-cols-4";
        default:
          return "grid-cols-1 sm:grid-cols-2 xl:grid-cols-3";
      }
    };
    const normalizedVideos = computed(
      () => Array.isArray(props.videos) ? props.videos : []
    );
    const videoTitle = (video) => {
      var _a;
      return ((_a = video == null ? void 0 : video.translation) == null ? void 0 : _a.title) || "";
    };
    const videoShort = (video) => {
      var _a;
      return ((_a = video == null ? void 0 : video.translation) == null ? void 0 : _a.short) || "";
    };
    const videoAuthorName = (video) => {
      var _a, _b;
      return ((_a = video == null ? void 0 : video.translation) == null ? void 0 : _a.pseudonym) || ((_b = video == null ? void 0 : video.owner) == null ? void 0 : _b.name) || "";
    };
    const videoShowRoute = (video) => route(
      "public.blogVideos.show",
      {
        url: (video == null ? void 0 : video.url) || ""
      }
    );
    const videoImages = (video) => Array.isArray(video == null ? void 0 : video.images) ? video.images : [];
    const imageUrl = (image) => (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
    const videoThumbnail = (video) => {
      const image = videoImages(video)[0];
      return imageUrl(
        image
      );
    };
    const videoPublishedAt = (video) => (video == null ? void 0 : video.published_at) || (video == null ? void 0 : video.created_at) || "";
    const schemaDuration = (seconds) => {
      const value = Number(seconds);
      if (!Number.isFinite(value) || value <= 0) {
        return "";
      }
      const total = Math.floor(value);
      const hours = Math.floor(
        total / 3600
      );
      const minutes = Math.floor(
        total % 3600 / 60
      );
      const secs = total % 60;
      return [
        "PT",
        hours ? `${hours}H` : "",
        minutes ? `${minutes}M` : "",
        secs || !hours && !minutes ? `${secs}S` : ""
      ].join("");
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: [
          "grid gap-4",
          getGridClass()
        ],
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", normalizedVideos.value.length)}><!--[-->`);
      ssrRenderList(normalizedVideos.value, (video, index) => {
        var _a, _b;
        _push(`<div itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="group flex h-full flex-col overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-900"><meta itemprop="position"${ssrRenderAttr("content", index + 1)}><article itemprop="item" itemscope itemtype="https://schema.org/VideoObject" class="flex h-full flex-col"><meta itemprop="url"${ssrRenderAttr("content", videoShowRoute(video))}><meta itemprop="name"${ssrRenderAttr("content", videoTitle(video))}>`);
        if (videoShort(video)) {
          _push(`<meta itemprop="description"${ssrRenderAttr("content", videoShort(video))}>`);
        } else {
          _push(`<!---->`);
        }
        if (videoThumbnail(video)) {
          _push(`<meta itemprop="thumbnailUrl"${ssrRenderAttr("content", videoThumbnail(video))}>`);
        } else {
          _push(`<!---->`);
        }
        if (videoPublishedAt(video)) {
          _push(`<meta itemprop="uploadDate"${ssrRenderAttr("content", videoPublishedAt(video))}>`);
        } else {
          _push(`<!---->`);
        }
        if (schemaDuration(video == null ? void 0 : video.duration)) {
          _push(`<meta itemprop="duration"${ssrRenderAttr("content", schemaDuration(video == null ? void 0 : video.duration))}>`);
        } else {
          _push(`<!---->`);
        }
        if (videoAuthorName(video)) {
          _push(`<div itemprop="author" itemscope itemtype="https://schema.org/Person"><meta itemprop="name"${ssrRenderAttr("content", videoAuthorName(video))}></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter"><meta itemprop="interactionType" content="https://schema.org/WatchAction"><meta itemprop="userInteractionCount"${ssrRenderAttr("content", Number((video == null ? void 0 : video.views) || 0))}></div>`);
        if ((video == null ? void 0 : video.likes_count) !== void 0) {
          _push(`<div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter"><meta itemprop="interactionType" content="https://schema.org/LikeAction"><meta itemprop="userInteractionCount"${ssrRenderAttr("content", Number((video == null ? void 0 : video.likes_count) || 0))}></div>`);
        } else {
          _push(`<!---->`);
        }
        if ((video == null ? void 0 : video.comments_count) !== void 0) {
          _push(`<div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter"><meta itemprop="interactionType" content="https://schema.org/CommentAction"><meta itemprop="userInteractionCount"${ssrRenderAttr("content", Number((video == null ? void 0 : video.comments_count) || 0))}></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="p-4 pb-0">`);
        _push(ssrRenderComponent(VideoPlayer, { video }, null, _parent));
        _push(`</div><div class="flex flex-1 flex-col p-4"><div class="flex items-center justify-center text-center">`);
        _push(ssrRenderComponent(unref(Link), {
          href: videoShowRoute(video),
          class: "inline-flex items-center gap-1"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-lg font-semibold text-slate-900/85 text-center group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75"${_scopeId}>${ssrInterpolate(videoTitle(video))}</span>`);
            } else {
              return [
                createVNode("span", { class: "text-lg font-semibold text-slate-900/85 text-center group-hover:opacity-75 dark:text-slate-100/85 dark:group-hover:opacity-75" }, toDisplayString(videoTitle(video)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div>`);
        if (videoShort(video)) {
          _push(`<div class="mt-3 line-clamp-3 text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(videoShort(video))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (video == null ? void 0 : video.owner) {
          _push(`<div class="mt-4 flex items-center justify-center gap-2">`);
          if ((_a = video.owner) == null ? void 0 : _a.profile_photo_url) {
            _push(`<img${ssrRenderAttr("src", video.owner.profile_photo_url)}${ssrRenderAttr("alt", video.owner.name)} loading="lazy" class="h-6 w-6 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700">`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="min-w-0 text-xs font-semibold text-slate-700/85 dark:text-slate-300/85">${ssrInterpolate((_b = video.owner) == null ? void 0 : _b.name)}</div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex items-center justify-center">`);
        _push(ssrRenderComponent(_sfc_main$1, {
          views: video.views || 0,
          "likes-count": video.likes_count || 0,
          "already-liked": video.already_liked || false,
          "route-name": "public.blogVideos.like",
          "route-params": { id: video.id },
          "show-likes-button": true,
          compact: ""
        }, null, _parent));
        _push(`</div><div class="mt-auto pt-4">`);
        _push(ssrRenderComponent(unref(Link), {
          href: videoShowRoute(video),
          class: "flex w-full items-center justify-center gap-2 rounded-sm px-3 py-2 btn-default"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="text-sm font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("readMore"))}</span><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"${_scopeId}><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 0 1 .02-1.06L10.94 10 7.23 6.29a.75.75 0 1 1 1.06-1.06l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06.02Z" clip-rule="evenodd"${_scopeId}></path></svg>`);
            } else {
              return [
                createVNode("span", { class: "text-sm font-semibold" }, toDisplayString(unref(t)("readMore")), 1),
                (openBlock(), createBlock("svg", {
                  class: "h-4 w-4",
                  viewBox: "0 0 20 20",
                  fill: "currentColor",
                  "aria-hidden": "true"
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogVideo/VideoGrid.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
