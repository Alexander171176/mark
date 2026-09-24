import { computed, mergeProps, unref, withCtx, createVNode, toDisplayString, createBlock, openBlock, useSSRContext, ref, onMounted, watch, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderList, ssrRenderComponent, ssrInterpolate, ssrRenderClass } from "vue/server-renderer";
import { Link, usePage, Head, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as useSmoothScrollTo } from "./useSmoothScrollTo-g9G3kyDv.js";
import { _ as _sfc_main$3 } from "./DefaultLayout-6zxGxPtK.js";
import { _ as _sfc_main$4, a as _sfc_main$c, P as Progress } from "./Progress-CyfOKcqP.js";
import { _ as _sfc_main$5, a as _sfc_main$b } from "./RightSidebar-OMLtHB67.js";
import { _ as _sfc_main$6 } from "./EntityPageToolbar-DT32FtSd.js";
import { _ as _sfc_main$8, a as _sfc_main$9, b as _sfc_main$d } from "./PublicAdminBottomPanel-BhLEQMoJ.js";
import { _ as _sfc_main$7 } from "./VideoGrid-lyRFayP0.js";
import { V as VideoPlayer, _ as _sfc_main$a, S as SectionBanners } from "./SectionBanners-Cdgmr0Bw.js";
import { _ as _sfc_main$2 } from "./EntityStats-0c7h3PEr.js";
import "axios";
import "@inertiajs/inertia";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "./LocaleSelectOption-BeLdazeX.js";
import "./Checkbox-CgE3PSwb.js";
import "./TextInput-CCxUFX3K.js";
import "./InputLabel-Ds0Eo91B.js";
import "./PrimaryButton-D7EZDGT_.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
import "./ViewModeToggle-DMCnQ0wo.js";
import "./ProcessingModeSwitcher-BJvzFf6_.js";
import "./LikeButtonEntity-ZC4HMEAO.js";
const _sfc_main$1 = {
  __name: "VideoRows",
  __ssrInlineRender: true,
  props: {
    videos: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
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
    const videoThumbnail = (video) => imageUrl(
      videoImages(video)[0]
    );
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
        class: "space-y-4",
        itemscope: "",
        itemtype: "https://schema.org/ItemList"
      }, _attrs))}><meta itemprop="numberOfItems"${ssrRenderAttr("content", normalizedVideos.value.length)}><!--[-->`);
      ssrRenderList(normalizedVideos.value, (video, index) => {
        var _a, _b;
        _push(`<div itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="group overflow-hidden rounded-md border border-gray-200 bg-white shadow-sm transition hover:shadow-md dark:border-gray-700 dark:bg-gray-900"><meta itemprop="position"${ssrRenderAttr("content", index + 1)}><article itemprop="item" itemscope itemtype="https://schema.org/VideoObject"><meta itemprop="url"${ssrRenderAttr("content", videoShowRoute(video))}><meta itemprop="name"${ssrRenderAttr("content", videoTitle(video))}>`);
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
        _push(`<div class="flex flex-col xl:flex-row gap-3 p-3"><div class="w-full xl:w-96 shrink-0">`);
        _push(ssrRenderComponent(VideoPlayer, { video }, null, _parent));
        _push(`</div><div class="min-w-0 flex-1 flex flex-col justify-around"><div class="flex items-start justify-between gap-3">`);
        _push(ssrRenderComponent(unref(Link), {
          href: videoShowRoute(video),
          class: "min-w-0 inline-flex items-center gap-2"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75"${_scopeId}>${ssrInterpolate(videoTitle(video))}</span>`);
            } else {
              return [
                createVNode("span", { class: "truncate text-lg font-semibold text-slate-900/85 dark:text-slate-100/85 group-hover:opacity-75" }, toDisplayString(videoTitle(video)), 1)
              ];
            }
          }),
          _: 2
        }, _parent));
        _push(`</div>`);
        if (videoShort(video)) {
          _push(`<div class="mt-2 line-clamp-2 text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(videoShort(video))}</div>`);
        } else {
          _push(`<!---->`);
        }
        if (video == null ? void 0 : video.owner) {
          _push(`<div class="mt-3 flex items-center gap-2">`);
          if ((_a = video.owner) == null ? void 0 : _a.profile_photo_url) {
            _push(`<img${ssrRenderAttr("src", video.owner.profile_photo_url)}${ssrRenderAttr("alt", video.owner.name)} loading="lazy" class="h-6 w-6 rounded-full object-cover ring-1 ring-gray-200 dark:ring-gray-700">`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="text-xs font-semibold text-slate-700/85 dark:text-slate-300/85 truncate">${ssrInterpolate((_b = video.owner) == null ? void 0 : _b.name)}</div></div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<div class="mt-3 flex items-center justify-between gap-3">`);
        _push(ssrRenderComponent(_sfc_main$2, {
          views: video.views || 0,
          "likes-count": video.likes_count || 0,
          "already-liked": video.already_liked || false,
          "route-name": "public.blogVideos.like",
          "route-params": { id: video.id },
          "show-likes-button": true,
          compact: ""
        }, null, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: videoShowRoute(video),
          class: "flex items-center justify-center gap-2 rounded-sm px-3 py-1 btn-default"
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
        _push(`</div></div></div></article></div>`);
      });
      _push(`<!--]--></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/BlogVideo/VideoRows.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const VIEW_KEY = "public_blog_videos_view";
const LEFT_SIDEBAR_KEY = "public_left_sidebar_collapsed";
const RIGHT_SIDEBAR_KEY = "public_right_sidebar_collapsed";
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    locale: {
      type: String,
      default: ""
    },
    seo: {
      type: Object,
      default: () => ({
        title: "",
        keywords: "",
        description: ""
      })
    },
    publicBlogVideosProcessingMode: {
      type: String,
      default: "server"
    },
    /**
     * Единственный default сортировки
     * приходит из PublicSettingsService
     * через backend-контроллер.
     */
    publicBlogVideosDefaultSort: {
      type: String,
      default: "sortAsc"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: ""
    },
    canLogin: {
      type: Boolean,
      default: false
    },
    canRegister: {
      type: Boolean,
      default: false
    },
    rubricTree: {
      type: Array,
      default: () => []
    },
    videos: {
      type: [Array, Object],
      default: () => []
    },
    videosCount: {
      type: Number,
      default: 0
    },
    videosFound: {
      type: Number,
      default: 0
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    mainVideos: {
      type: [Array, Object],
      default: () => []
    },
    mainBanners: {
      type: [Array, Object],
      default: () => []
    }
  },
  setup(__props) {
    var _a, _b, _c;
    const {
      t,
      locale: i18nLocale
    } = useI18n();
    const page = usePage();
    const props = __props;
    const normalizeList = (value) => {
      if (Array.isArray(value)) {
        return value;
      }
      if (Array.isArray(value == null ? void 0 : value.data)) {
        return value.data;
      }
      return [];
    };
    const normalizeText = (value) => String(value ?? "").trim().toLocaleLowerCase();
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = value ? Date.parse(value) : 0;
      return Number.isFinite(time) ? time : 0;
    };
    const resolveContentLocale = () => {
      var _a2;
      const backendLocale = String(
        props.locale || ""
      ).trim();
      if (backendLocale) {
        return backendLocale;
      }
      const vueLocale = String(
        i18nLocale.value || ""
      ).trim();
      if (vueLocale) {
        return vueLocale;
      }
      if (typeof document !== "undefined" && ((_a2 = document.documentElement) == null ? void 0 : _a2.lang)) {
        return String(
          document.documentElement.lang
        ).trim();
      }
      return "und";
    };
    const normalizeOgLocale = (locale) => {
      const value = String(locale || "").trim().replace("_", "-");
      if (!value) {
        return "und";
      }
      try {
        const normalized = new Intl.Locale(value).maximize();
        const language = normalized.language;
        const region = normalized.region;
        if (language && region) {
          return `${language}_${region}`;
        }
        return normalized.toString().replace("-", "_");
      } catch {
        return value.replace("-", "_");
      }
    };
    const siteSettings = computed(
      () => {
        var _a2;
        return ((_a2 = page.props) == null ? void 0 : _a2.siteSettings) ?? {};
      }
    );
    const isAdmin = computed(
      () => {
        var _a2;
        return ((_a2 = page.props) == null ? void 0 : _a2.isAdmin) === true;
      }
    );
    const rubricTree = computed(
      () => Array.isArray(props.rubricTree) ? props.rubricTree : []
    );
    const videosData = computed(
      () => normalizeList(
        props.videos
      )
    );
    const mainVideos = computed(
      () => normalizeList(
        props.mainVideos
      )
    );
    const mainBanners = computed(
      () => normalizeList(
        props.mainBanners
      )
    );
    const getVideoTitle = (video) => {
      var _a2;
      return ((_a2 = video == null ? void 0 : video.translation) == null ? void 0 : _a2.title) || "";
    };
    const getVideoShort = (video) => {
      var _a2;
      return ((_a2 = video == null ? void 0 : video.translation) == null ? void 0 : _a2.short) || "";
    };
    const getVideoPseudonym = (video) => {
      var _a2;
      return ((_a2 = video == null ? void 0 : video.translation) == null ? void 0 : _a2.pseudonym) || "";
    };
    const getVideoDuration = (video) => safeNumber(
      video == null ? void 0 : video.duration
    );
    const getVideoImages = (video) => normalizeList(
      video == null ? void 0 : video.images
    );
    const getImageUrl = (image) => (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
    const seoTitle = computed(
      () => {
        var _a2;
        return ((_a2 = props.seo) == null ? void 0 : _a2.title) || t("videos");
      }
    );
    const seoDescription = computed(
      () => {
        var _a2;
        return ((_a2 = props.seo) == null ? void 0 : _a2.description) || "";
      }
    );
    const seoKeywords = computed(
      () => {
        var _a2;
        return ((_a2 = props.seo) == null ? void 0 : _a2.keywords) || "";
      }
    );
    const contentLocale = computed(
      () => resolveContentLocale()
    );
    const ogLocale = computed(
      () => normalizeOgLocale(
        contentLocale.value
      )
    );
    const canonicalUrl = computed(
      () => String(
        route(
          "public.blogVideos.index"
        )
      )
    );
    const dcSubject = computed(
      () => seoKeywords.value || seoTitle.value
    );
    const seoPreview = computed(() => {
      for (const video of videosData.value) {
        const image = getVideoImages(video)[0];
        const url = getImageUrl(image);
        if (url) {
          return {
            url,
            alt: (image == null ? void 0 : image.alt) || getVideoTitle(video) || seoTitle.value
          };
        }
      }
      return {
        url: "",
        alt: ""
      };
    });
    const seoImage = computed(
      () => seoPreview.value.url
    );
    const seoImageAlt = computed(
      () => seoPreview.value.alt
    );
    const q = ref(
      String(
        ((_a = props.filters) == null ? void 0 : _a.q) ?? ""
      )
    );
    const DEFAULT_SORT = computed(
      () => String(
        props.publicBlogVideosDefaultSort || "sortAsc"
      )
    );
    const sort = ref(
      String(
        ((_b = props.filters) == null ? void 0 : _b.sort) ?? DEFAULT_SORT.value
      )
    );
    const perPage = computed(() => {
      var _a2;
      const value = Number(
        (_a2 = props.filters) == null ? void 0 : _a2.per_page
      );
      return Number.isFinite(value) && value > 0 ? value : 12;
    });
    const videoSortOptions = [
      {
        value: "sortAsc",
        label: `${t("sortNumber")} 0→9`
      },
      {
        value: "sortDesc",
        label: `${t("sortNumber")} 9→0`
      },
      {
        value: "titleAsc",
        label: `${t("title")} A→Z`
      },
      {
        value: "titleDesc",
        label: `${t("title")} Z→A`
      },
      {
        value: "viewsDesc",
        label: `${t("views")} 9→0`
      },
      {
        value: "viewsAsc",
        label: `${t("views")} 0→9`
      },
      {
        value: "likesDesc",
        label: `${t("likes")} 9→0`
      },
      {
        value: "likesAsc",
        label: `${t("likes")} 0→9`
      },
      {
        value: "commentsDesc",
        label: `${t("comments")} 9→0`
      },
      {
        value: "commentsAsc",
        label: `${t("comments")} 0→9`
      },
      {
        value: "durationDesc",
        label: `${t("duration")} 9→0`
      },
      {
        value: "durationAsc",
        label: `${t("duration")} 0→9`
      },
      {
        value: "publishedAtDesc",
        label: `${t("publishedAt")} ↓`
      },
      {
        value: "publishedAtAsc",
        label: `${t("publishedAt")} ↑`
      }
    ];
    const viewMode = ref(
      String(
        ((_c = props.filters) == null ? void 0 : _c.view) || "grid"
      )
    );
    onMounted(() => {
      try {
        const storedView = localStorage.getItem(
          VIEW_KEY
        );
        if (storedView === "grid" || storedView === "rows") {
          viewMode.value = storedView;
        }
      } catch {
      }
    });
    watch(
      viewMode,
      (value) => {
        try {
          localStorage.setItem(
            VIEW_KEY,
            value
          );
        } catch {
        }
      }
    );
    const filteredVideos = computed(() => {
      if (props.useServerProcessing) {
        return videosData.value;
      }
      const query = normalizeText(
        q.value
      );
      if (!query) {
        return videosData.value;
      }
      return videosData.value.filter(
        (video) => {
          var _a2;
          return [
            video == null ? void 0 : video.id,
            getVideoTitle(video),
            getVideoShort(video),
            getVideoPseudonym(video),
            video == null ? void 0 : video.url,
            video == null ? void 0 : video.source_type,
            video == null ? void 0 : video.external_video_id,
            (_a2 = video == null ? void 0 : video.owner) == null ? void 0 : _a2.name
          ].some(
            (value) => normalizeText(value).includes(query)
          );
        }
      );
    });
    const compareText = (a, b) => String(a ?? "").localeCompare(
      String(b ?? ""),
      contentLocale.value,
      {
        sensitivity: "base"
      }
    );
    const compareNumber = (a, b) => safeNumber(a) - safeNumber(b);
    const compareIdDesc = (a, b) => compareNumber(
      b == null ? void 0 : b.id,
      a == null ? void 0 : a.id
    );
    const sortedVideos = computed(() => {
      if (props.useServerProcessing) {
        return filteredVideos.value;
      }
      const list = [
        ...filteredVideos.value
      ];
      switch (sort.value) {
        case "sortAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.sort,
              b == null ? void 0 : b.sort
            ) || compareNumber(
              a == null ? void 0 : a.id,
              b == null ? void 0 : b.id
            )
          );
          break;
        case "sortDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.sort,
              a == null ? void 0 : a.sort
            ) || compareNumber(
              b == null ? void 0 : b.id,
              a == null ? void 0 : a.id
            )
          );
          break;
        case "titleAsc":
          list.sort(
            (a, b) => compareText(
              getVideoTitle(a),
              getVideoTitle(b)
            ) || compareIdDesc(
              a,
              b
            )
          );
          break;
        case "titleDesc":
          list.sort(
            (a, b) => compareText(
              getVideoTitle(b),
              getVideoTitle(a)
            ) || compareIdDesc(
              a,
              b
            )
          );
          break;
        case "viewsAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.views,
              b == null ? void 0 : b.views
            ) || compareIdDesc(
              a,
              b
            )
          );
          break;
        case "viewsDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.views,
              a == null ? void 0 : a.views
            ) || compareIdDesc(
              a,
              b
            )
          );
          break;
        case "likesAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.likes_count,
              b == null ? void 0 : b.likes_count
            ) || compareIdDesc(
              a,
              b
            )
          );
          break;
        case "likesDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.likes_count,
              a == null ? void 0 : a.likes_count
            ) || compareIdDesc(
              a,
              b
            )
          );
          break;
        case "commentsAsc":
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.comments_count,
              b == null ? void 0 : b.comments_count
            ) || compareIdDesc(
              a,
              b
            )
          );
          break;
        case "commentsDesc":
          list.sort(
            (a, b) => compareNumber(
              b == null ? void 0 : b.comments_count,
              a == null ? void 0 : a.comments_count
            ) || compareIdDesc(
              a,
              b
            )
          );
          break;
        case "durationAsc":
          list.sort(
            (a, b) => getVideoDuration(a) - getVideoDuration(b) || compareIdDesc(
              a,
              b
            )
          );
          break;
        case "durationDesc":
          list.sort(
            (a, b) => getVideoDuration(b) - getVideoDuration(a) || compareIdDesc(
              a,
              b
            )
          );
          break;
        case "publishedAtAsc":
          list.sort(
            (a, b) => safeDate(
              (a == null ? void 0 : a.published_at) || (a == null ? void 0 : a.created_at)
            ) - safeDate(
              (b == null ? void 0 : b.published_at) || (b == null ? void 0 : b.created_at)
            ) || compareIdDesc(
              a,
              b
            )
          );
          break;
        case "publishedAtDesc":
          list.sort(
            (a, b) => safeDate(
              (b == null ? void 0 : b.published_at) || (b == null ? void 0 : b.created_at)
            ) - safeDate(
              (a == null ? void 0 : a.published_at) || (a == null ? void 0 : a.created_at)
            ) || compareIdDesc(
              a,
              b
            )
          );
          break;
        default:
          list.sort(
            (a, b) => compareNumber(
              a == null ? void 0 : a.sort,
              b == null ? void 0 : b.sort
            ) || compareNumber(
              a == null ? void 0 : a.id,
              b == null ? void 0 : b.id
            )
          );
          break;
      }
      return list;
    });
    const frontendCurrentPage = ref(1);
    const {
      targetRef: scrollTarget,
      scrollToTarget
    } = useSmoothScrollTo({
      offset: 80,
      duration: 1200
    });
    watch(
      [
        q,
        sort
      ],
      () => {
        if (!props.useServerProcessing) {
          frontendCurrentPage.value = 1;
        }
      }
    );
    watch(
      frontendCurrentPage,
      () => {
        if (!props.useServerProcessing) {
          scrollToTarget();
        }
      }
    );
    const effectiveVideosFound = computed(
      () => props.useServerProcessing ? safeNumber(
        props.videosFound
      ) : sortedVideos.value.length
    );
    const frontendPaginatedVideos = computed(() => {
      if (props.useServerProcessing) {
        return videosData.value;
      }
      const start = (frontendCurrentPage.value - 1) * perPage.value;
      return sortedVideos.value.slice(
        start,
        start + perPage.value
      );
    });
    const displayedVideos = computed(
      () => props.useServerProcessing ? videosData.value : frontendPaginatedVideos.value
    );
    const currentPage = computed(
      () => {
        var _a2, _b2, _c2;
        return Number(
          ((_b2 = (_a2 = props.videos) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.current_page) ?? ((_c2 = props.videos) == null ? void 0 : _c2.current_page) ?? 1
        ) || 1;
      }
    );
    const lastPage = computed(
      () => {
        var _a2, _b2, _c2;
        return Number(
          ((_b2 = (_a2 = props.videos) == null ? void 0 : _a2.meta) == null ? void 0 : _b2.last_page) ?? ((_c2 = props.videos) == null ? void 0 : _c2.last_page) ?? 1
        ) || 1;
      }
    );
    const reloadVideos = (pageNumber = 1) => {
      if (!props.useServerProcessing) {
        return;
      }
      router.get(
        canonicalUrl.value,
        {
          q: q.value || void 0,
          sort: sort.value || void 0,
          page: pageNumber
        },
        {
          preserveState: true,
          replace: true,
          preserveScroll: true
        }
      );
    };
    const goToPage = (pageNumber) => {
      const target = Math.min(
        Math.max(
          1,
          Number(pageNumber) || 1
        ),
        lastPage.value
      );
      reloadVideos(
        target
      );
    };
    const goPrev = () => {
      if (currentPage.value > 1) {
        goToPage(
          currentPage.value - 1
        );
      }
    };
    const goNext = () => {
      if (currentPage.value < lastPage.value) {
        goToPage(
          currentPage.value + 1
        );
      }
    };
    const applyFilters = () => {
      if (props.useServerProcessing) {
        reloadVideos(1);
      } else {
        frontendCurrentPage.value = 1;
      }
    };
    const resetFilters = () => {
      q.value = "";
      sort.value = DEFAULT_SORT.value;
      if (props.useServerProcessing) {
        reloadVideos(1);
      } else {
        frontendCurrentPage.value = 1;
      }
    };
    const settingEnabled = (value, defaultValue = true) => {
      if (value === void 0 || value === null || value === "") {
        return defaultValue;
      }
      if (typeof value === "boolean") {
        return value;
      }
      return String(value) === "true";
    };
    const showLeft = computed(
      () => {
        var _a2;
        return settingEnabled(
          (_a2 = siteSettings.value) == null ? void 0 : _a2.ViewLeftColumn,
          true
        );
      }
    );
    const showRight = computed(
      () => {
        var _a2;
        return settingEnabled(
          (_a2 = siteSettings.value) == null ? void 0 : _a2.ViewRightColumn,
          true
        );
      }
    );
    const leftCollapsed = ref(true);
    const rightCollapsed = ref(true);
    const readStoredBoolean = (key, fallback = true) => {
      try {
        const value = localStorage.getItem(
          key
        );
        return value === null ? fallback : value === "true";
      } catch {
        return fallback;
      }
    };
    const writeStoredBoolean = (key, value) => {
      try {
        localStorage.setItem(
          key,
          String(
            Boolean(value)
          )
        );
      } catch {
      }
    };
    onMounted(() => {
      leftCollapsed.value = readStoredBoolean(
        LEFT_SIDEBAR_KEY,
        true
      );
      rightCollapsed.value = readStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        true
      );
    });
    const setLeftCollapsed = (value) => {
      leftCollapsed.value = Boolean(value);
      writeStoredBoolean(
        LEFT_SIDEBAR_KEY,
        leftCollapsed.value
      );
    };
    const setRightCollapsed = (value) => {
      rightCollapsed.value = Boolean(value);
      writeStoredBoolean(
        RIGHT_SIDEBAR_KEY,
        rightCollapsed.value
      );
    };
    const videoGridCols = computed(() => {
      const leftExpanded = showLeft.value && !leftCollapsed.value;
      const rightExpanded = showRight.value && !rightCollapsed.value;
      if (leftExpanded && rightExpanded) {
        return 2;
      }
      if (leftExpanded || rightExpanded) {
        return 3;
      }
      return 4;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), null, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<title${_scopeId}>
            ${ssrInterpolate(seoTitle.value)}
        </title><meta name="title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta name="description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoKeywords.value) {
              _push2(`<meta name="keywords"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="robots" content="index, follow, max-image-preview:large"${_scopeId}><link rel="canonical"${ssrRenderAttr("href", canonicalUrl.value)}${_scopeId}><meta property="og:type" content="website"${_scopeId}><meta property="og:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta property="og:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta property="og:url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta property="og:locale"${ssrRenderAttr("content", ogLocale.value)}${_scopeId}>`);
            if (seoImage.value) {
              _push2(`<meta property="og:image"${ssrRenderAttr("content", seoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value) {
              _push2(`<meta property="og:image:alt"${ssrRenderAttr("content", seoImageAlt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="twitter:card"${ssrRenderAttr(
              "content",
              seoImage.value ? "summary_large_image" : "summary"
            )}${_scopeId}><meta name="twitter:title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta name="twitter:description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value) {
              _push2(`<meta name="twitter:image"${ssrRenderAttr("content", seoImage.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoImage.value) {
              _push2(`<meta name="twitter:image:alt"${ssrRenderAttr("content", seoImageAlt.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.title"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta name="DC.description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (dcSubject.value) {
              _push2(`<meta name="DC.subject"${ssrRenderAttr("content", dcSubject.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta name="DC.language"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}><meta name="DC.identifier"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta name="DC.type" content="Collection"${_scopeId}><meta name="DC.format" content="text/html"${_scopeId}>`);
          } else {
            return [
              createVNode("title", null, "\n            " + toDisplayString(seoTitle.value) + "\n        ", 1),
              createVNode("meta", {
                name: "title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 0,
                name: "description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoKeywords.value ? (openBlock(), createBlock("meta", {
                key: 1,
                name: "keywords",
                content: seoKeywords.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "robots",
                content: "index, follow, max-image-preview:large"
              }),
              createVNode("link", {
                rel: "canonical",
                href: canonicalUrl.value
              }, null, 8, ["href"]),
              createVNode("meta", {
                property: "og:type",
                content: "website"
              }),
              createVNode("meta", {
                property: "og:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 2,
                property: "og:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                property: "og:url",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                property: "og:locale",
                content: ogLocale.value
              }, null, 8, ["content"]),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 3,
                property: "og:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 4,
                property: "og:image:alt",
                content: seoImageAlt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "twitter:card",
                content: seoImage.value ? "summary_large_image" : "summary"
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "twitter:title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 5,
                name: "twitter:description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 6,
                name: "twitter:image",
                content: seoImage.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              seoImage.value ? (openBlock(), createBlock("meta", {
                key: 7,
                name: "twitter:image:alt",
                content: seoImageAlt.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.title",
                content: seoTitle.value
              }, null, 8, ["content"]),
              seoDescription.value ? (openBlock(), createBlock("meta", {
                key: 8,
                name: "DC.description",
                content: seoDescription.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              dcSubject.value ? (openBlock(), createBlock("meta", {
                key: 9,
                name: "DC.subject",
                content: dcSubject.value
              }, null, 8, ["content"])) : createCommentVNode("", true),
              createVNode("meta", {
                name: "DC.language",
                content: contentLocale.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.identifier",
                content: canonicalUrl.value
              }, null, 8, ["content"]),
              createVNode("meta", {
                name: "DC.type",
                content: "Collection"
              }),
              createVNode("meta", {
                name: "DC.format",
                content: "text/html"
              })
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$3, {
        title: __props.title,
        "can-login": __props.canLogin,
        "can-register": __props.canRegister
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$4, null, null, _parent2, _scopeId));
            _push2(`<main class="min-h-screen px-1 lg:px-6 max-w-full"${_scopeId}><div class="mx-auto tracking-wider pt-20 lg:pt-44"${_scopeId}><div class="ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500"${_scopeId}>`);
            if (showLeft.value) {
              _push2(`<aside class="${ssrRenderClass([
                leftCollapsed.value ? "lg:w-6" : "lg:w-72",
                "shrink-0 transition-all duration-300 overflow-hidden"
              ])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                "rubric-tree": rubricTree.value,
                collapsed: leftCollapsed.value,
                onCollapsed: setLeftCollapsed
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<article itemscope itemtype="https://schema.org/CollectionPage"${ssrRenderAttr("itemid", canonicalUrl.value)} class="w-full pb-6 slate-1 min-w-0"${_scopeId}><meta itemprop="name"${ssrRenderAttr("content", seoTitle.value)}${_scopeId}>`);
            if (seoDescription.value) {
              _push2(`<meta itemprop="description"${ssrRenderAttr("content", seoDescription.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            if (seoKeywords.value) {
              _push2(`<meta itemprop="keywords"${ssrRenderAttr("content", seoKeywords.value)}${_scopeId}>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<meta itemprop="url"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="inLanguage"${ssrRenderAttr("content", contentLocale.value)}${_scopeId}><div class="mx-auto max-w-6xl"${_scopeId}><nav class="text-sm" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"${_scopeId}><ol class="flex flex-wrap items-center font-semibold"${_scopeId}><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              itemprop: "item",
              href: _ctx.route("home"),
              class: "breadcrumb-link hover:underline"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span itemprop="name"${_scopeId2}>${ssrInterpolate(unref(t)("home"))}</span>`);
                } else {
                  return [
                    createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("home")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<meta itemprop="position" content="1"${_scopeId}></li><li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem" class="flex items-center" aria-current="page"${_scopeId}><span class="mx-2 breadcrumbs"${_scopeId}> / </span><span itemprop="name" class="breadcrumbs"${_scopeId}>${ssrInterpolate(unref(t)("videos"))}</span><meta itemprop="item"${ssrRenderAttr("content", canonicalUrl.value)}${_scopeId}><meta itemprop="position" content="2"${_scopeId}></li></ol></nav><div class="my-3 flex flex-wrap items-center justify-center gap-2 title"${_scopeId}><svg class="h-5 w-5" fill="currentColor" viewBox="0 0 576 512" aria-hidden="true"${_scopeId}><path d="M336.2 64H47.8C21.4 64 0 85.4 0 111.8v288.4C0 426.6 21.4 448 47.8 448h288.4c26.4 0 47.8-21.4 47.8-47.8V111.8c0-26.4-21.4-47.8-47.8-47.8zm189.4 37.7L416 177.3v157.4l109.6 75.5c21.2 14.6 50.4-.3 50.4-25.8V127.5c0-25.4-29.1-40.4-50.4-25.8z"${_scopeId}></path></svg><h1 itemprop="name" class="text-2xl font-bold"${_scopeId}>${ssrInterpolate(unref(t)("videos"))}</h1></div>`);
            if (seoDescription.value) {
              _push2(`<div itemprop="description" class="my-1 text-sm subtitle text-center"${_scopeId}>${ssrInterpolate(seoDescription.value)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$6, {
              modelValue: q.value,
              "onUpdate:modelValue": ($event) => q.value = $event,
              "view-mode": viewMode.value,
              "onUpdate:viewMode": ($event) => viewMode.value = $event,
              "sort-value": sort.value,
              "onUpdate:sortValue": ($event) => sort.value = $event,
              found: effectiveVideosFound.value,
              "sort-options": videoSortOptions,
              "default-sort": DEFAULT_SORT.value,
              "found-label": unref(t)("videos"),
              "search-placeholder": unref(t)("searchByName"),
              onSubmit: applyFilters,
              onReset: resetFilters
            }, null, _parent2, _scopeId));
            _push2(`<div${_scopeId}></div>`);
            if (displayedVideos.value.length === 0) {
              _push2(`<div class="mt-6 text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<div${_scopeId}>`);
              if (viewMode.value === "grid") {
                _push2(ssrRenderComponent(_sfc_main$7, {
                  itemprop: "mainEntity",
                  videos: displayedVideos.value,
                  cols: videoGridCols.value
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$1, {
                  itemprop: "mainEntity",
                  videos: displayedVideos.value
                }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            }
            if (__props.useServerProcessing && lastPage.value > 1) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                "current-page": currentPage.value,
                "last-page": lastPage.value,
                found: __props.videosFound,
                onPrev: goPrev,
                onNext: goNext,
                onGo: goToPage
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (!__props.useServerProcessing && effectiveVideosFound.value > perPage.value) {
              _push2(ssrRenderComponent(_sfc_main$9, {
                currentPage: frontendCurrentPage.value,
                "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                "items-per-page": perPage.value,
                "total-items": effectiveVideosFound.value
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$a, { videos: mainVideos.value }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(SectionBanners, { banners: mainBanners.value }, null, _parent2, _scopeId));
            _push2(`</div></article>`);
            if (showRight.value) {
              _push2(`<aside class="${ssrRenderClass([
                rightCollapsed.value ? "lg:w-6" : "lg:w-72",
                "shrink-0 transition-all duration-300 overflow-hidden"
              ])}"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$b, {
                collapsed: rightCollapsed.value,
                onCollapsed: setRightCollapsed
              }, null, _parent2, _scopeId));
              _push2(`</aside>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div></main>`);
            _push2(ssrRenderComponent(_sfc_main$c, null, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(Progress, null, null, _parent2, _scopeId));
            if (isAdmin.value) {
              _push2(ssrRenderComponent(_sfc_main$d, {
                "setting-key": "publicBlogVideosProcessingMode",
                mode: __props.publicBlogVideosProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.videosCount
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode(_sfc_main$4),
              createVNode("main", { class: "min-h-screen px-1 lg:px-6 max-w-full" }, [
                createVNode("div", { class: "mx-auto tracking-wider pt-20 lg:pt-44" }, [
                  createVNode("div", { class: "ext-color w-full min-w-0 py-3 px-1 flex flex-col lg:flex-row gap-4 rounded-3xl border-2 border-slate-300 dark:border-slate-500" }, [
                    showLeft.value ? (openBlock(), createBlock("aside", {
                      key: 0,
                      class: [
                        "shrink-0 transition-all duration-300 overflow-hidden",
                        leftCollapsed.value ? "lg:w-6" : "lg:w-72"
                      ]
                    }, [
                      createVNode(_sfc_main$5, {
                        "rubric-tree": rubricTree.value,
                        collapsed: leftCollapsed.value,
                        onCollapsed: setLeftCollapsed
                      }, null, 8, ["rubric-tree", "collapsed"])
                    ], 2)) : createCommentVNode("", true),
                    createVNode("article", {
                      itemscope: "",
                      itemtype: "https://schema.org/CollectionPage",
                      itemid: canonicalUrl.value,
                      class: "w-full pb-6 slate-1 min-w-0"
                    }, [
                      createVNode("meta", {
                        itemprop: "name",
                        content: seoTitle.value
                      }, null, 8, ["content"]),
                      seoDescription.value ? (openBlock(), createBlock("meta", {
                        key: 0,
                        itemprop: "description",
                        content: seoDescription.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
                      seoKeywords.value ? (openBlock(), createBlock("meta", {
                        key: 1,
                        itemprop: "keywords",
                        content: seoKeywords.value
                      }, null, 8, ["content"])) : createCommentVNode("", true),
                      createVNode("meta", {
                        itemprop: "url",
                        content: canonicalUrl.value
                      }, null, 8, ["content"]),
                      createVNode("meta", {
                        itemprop: "inLanguage",
                        content: contentLocale.value
                      }, null, 8, ["content"]),
                      createVNode("div", { class: "mx-auto max-w-6xl" }, [
                        createVNode("nav", {
                          class: "text-sm",
                          "aria-label": "Breadcrumb",
                          itemscope: "",
                          itemtype: "https://schema.org/BreadcrumbList"
                        }, [
                          createVNode("ol", { class: "flex flex-wrap items-center font-semibold" }, [
                            createVNode("li", {
                              itemprop: "itemListElement",
                              itemscope: "",
                              itemtype: "https://schema.org/ListItem",
                              class: "flex items-center"
                            }, [
                              createVNode(unref(Link), {
                                itemprop: "item",
                                href: _ctx.route("home"),
                                class: "breadcrumb-link hover:underline"
                              }, {
                                default: withCtx(() => [
                                  createVNode("span", { itemprop: "name" }, toDisplayString(unref(t)("home")), 1)
                                ]),
                                _: 1
                              }, 8, ["href"]),
                              createVNode("meta", {
                                itemprop: "position",
                                content: "1"
                              })
                            ]),
                            createVNode("li", {
                              itemprop: "itemListElement",
                              itemscope: "",
                              itemtype: "https://schema.org/ListItem",
                              class: "flex items-center",
                              "aria-current": "page"
                            }, [
                              createVNode("span", { class: "mx-2 breadcrumbs" }, " / "),
                              createVNode("span", {
                                itemprop: "name",
                                class: "breadcrumbs"
                              }, toDisplayString(unref(t)("videos")), 1),
                              createVNode("meta", {
                                itemprop: "item",
                                content: canonicalUrl.value
                              }, null, 8, ["content"]),
                              createVNode("meta", {
                                itemprop: "position",
                                content: "2"
                              })
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "my-3 flex flex-wrap items-center justify-center gap-2 title" }, [
                          (openBlock(), createBlock("svg", {
                            class: "h-5 w-5",
                            fill: "currentColor",
                            viewBox: "0 0 576 512",
                            "aria-hidden": "true"
                          }, [
                            createVNode("path", { d: "M336.2 64H47.8C21.4 64 0 85.4 0 111.8v288.4C0 426.6 21.4 448 47.8 448h288.4c26.4 0 47.8-21.4 47.8-47.8V111.8c0-26.4-21.4-47.8-47.8-47.8zm189.4 37.7L416 177.3v157.4l109.6 75.5c21.2 14.6 50.4-.3 50.4-25.8V127.5c0-25.4-29.1-40.4-50.4-25.8z" })
                          ])),
                          createVNode("h1", {
                            itemprop: "name",
                            class: "text-2xl font-bold"
                          }, toDisplayString(unref(t)("videos")), 1)
                        ]),
                        seoDescription.value ? (openBlock(), createBlock("div", {
                          key: 0,
                          itemprop: "description",
                          class: "my-1 text-sm subtitle text-center"
                        }, toDisplayString(seoDescription.value), 1)) : createCommentVNode("", true),
                        createVNode(_sfc_main$6, {
                          modelValue: q.value,
                          "onUpdate:modelValue": ($event) => q.value = $event,
                          "view-mode": viewMode.value,
                          "onUpdate:viewMode": ($event) => viewMode.value = $event,
                          "sort-value": sort.value,
                          "onUpdate:sortValue": ($event) => sort.value = $event,
                          found: effectiveVideosFound.value,
                          "sort-options": videoSortOptions,
                          "default-sort": DEFAULT_SORT.value,
                          "found-label": unref(t)("videos"),
                          "search-placeholder": unref(t)("searchByName"),
                          onSubmit: applyFilters,
                          onReset: resetFilters
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "view-mode", "onUpdate:viewMode", "sort-value", "onUpdate:sortValue", "found", "default-sort", "found-label", "search-placeholder"]),
                        createVNode("div", {
                          ref_key: "scrollTarget",
                          ref: scrollTarget
                        }, null, 512),
                        displayedVideos.value.length === 0 ? (openBlock(), createBlock("div", {
                          key: 1,
                          class: "mt-6 text-center text-slate-700 dark:text-slate-300"
                        }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", { key: 2 }, [
                          viewMode.value === "grid" ? (openBlock(), createBlock(_sfc_main$7, {
                            key: 0,
                            itemprop: "mainEntity",
                            videos: displayedVideos.value,
                            cols: videoGridCols.value
                          }, null, 8, ["videos", "cols"])) : (openBlock(), createBlock(_sfc_main$1, {
                            key: 1,
                            itemprop: "mainEntity",
                            videos: displayedVideos.value
                          }, null, 8, ["videos"]))
                        ])),
                        __props.useServerProcessing && lastPage.value > 1 ? (openBlock(), createBlock(_sfc_main$8, {
                          key: 3,
                          "current-page": currentPage.value,
                          "last-page": lastPage.value,
                          found: __props.videosFound,
                          onPrev: goPrev,
                          onNext: goNext,
                          onGo: goToPage
                        }, null, 8, ["current-page", "last-page", "found"])) : createCommentVNode("", true),
                        !__props.useServerProcessing && effectiveVideosFound.value > perPage.value ? (openBlock(), createBlock(_sfc_main$9, {
                          key: 4,
                          currentPage: frontendCurrentPage.value,
                          "onUpdate:currentPage": ($event) => frontendCurrentPage.value = $event,
                          "items-per-page": perPage.value,
                          "total-items": effectiveVideosFound.value
                        }, null, 8, ["currentPage", "onUpdate:currentPage", "items-per-page", "total-items"])) : createCommentVNode("", true),
                        createVNode(_sfc_main$a, { videos: mainVideos.value }, null, 8, ["videos"]),
                        createVNode(SectionBanners, { banners: mainBanners.value }, null, 8, ["banners"])
                      ])
                    ], 8, ["itemid"]),
                    showRight.value ? (openBlock(), createBlock("aside", {
                      key: 1,
                      class: [
                        "shrink-0 transition-all duration-300 overflow-hidden",
                        rightCollapsed.value ? "lg:w-6" : "lg:w-72"
                      ]
                    }, [
                      createVNode(_sfc_main$b, {
                        collapsed: rightCollapsed.value,
                        onCollapsed: setRightCollapsed
                      }, null, 8, ["collapsed"])
                    ], 2)) : createCommentVNode("", true)
                  ])
                ])
              ]),
              createVNode(_sfc_main$c),
              createVNode(Progress),
              isAdmin.value ? (openBlock(), createBlock(_sfc_main$d, {
                key: 0,
                "setting-key": "publicBlogVideosProcessingMode",
                mode: __props.publicBlogVideosProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.videosCount
              }, null, 8, ["mode", "use-server-processing", "total"])) : createCommentVNode("", true)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Public/Default/Blog/BlogVideos/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
