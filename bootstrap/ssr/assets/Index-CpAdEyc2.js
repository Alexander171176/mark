import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, computed, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent, ssrRenderClass } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { usePage, router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$c } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$m } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$i } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$g, a as _sfc_main$j, b as _sfc_main$k } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$e } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$f, a as _sfc_main$l } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$h } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$d } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$a } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$b } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$9 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$5, a as _sfc_main$6, b as _sfc_main$7 } from "./RightToggle-r8SYzaEU.js";
import { _ as _sfc_main$8 } from "./ModerationButton-D_ehimPY.js";
import "./LocaleSelectOption-BeLdazeX.js";
import "./ResponsiveNavLink-gtte0z5g.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "./ScrollButtons-2xyFJfJ4.js";
import "@fortawesome/vue-fontawesome";
import "@fortawesome/fontawesome-svg-core";
import "@fortawesome/free-solid-svg-icons";
import "@inertiajs/inertia";
import "vue-smooth-dnd";
const _sfc_main$4 = {
  __name: "BulkActionSelect",
  __ssrInlineRender: true,
  emits: ["change"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option><option value="left">${ssrInterpolate(unref(t)("left"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("noLeft"))}</option><option disabled>──────────────────</option><option value="main">${ssrInterpolate(unref(t)("main"))}</option><option value="noMain">${ssrInterpolate(unref(t)("noMain"))}</option><option disabled>──────────────────</option><option value="right">${ssrInterpolate(unref(t)("right"))}</option><option value="noRight">${ssrInterpolate(unref(t)("noRight"))}</option><option disabled>──────────────────</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogVideo/Select/BulkActionSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: String
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit mt-2 mb-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} (0 → 9)</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} (9 → 0)</option><option disabled>─────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} (A → Z)</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} (Z → A)</option><option disabled>─────────────────</option><option value="urlAsc">URL A→Z</option><option value="urlDesc">URL Z→A</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="public">${ssrInterpolate(unref(t)("public"))}</option><option value="private">${ssrInterpolate(unref(t)("private"))}</option><option value="privateDesc">${ssrInterpolate(unref(t)("private"))} ON→OFF</option><option value="privateAsc">${ssrInterpolate(unref(t)("private"))} OFF→ON</option><option disabled>─────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} (9 → 0)</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} (0 → 9)</option><option value="likesDesc">${ssrInterpolate(unref(t)("likes"))} (9 → 0)</option><option value="likesAsc">${ssrInterpolate(unref(t)("likes"))} (0 → 9)</option><option value="commentsDesc">${ssrInterpolate(unref(t)("comments"))} 9→0</option><option value="commentsAsc">${ssrInterpolate(unref(t)("comments"))} 0→9</option><option disabled>─────────────────</option><option value="sourceTypeAsc">${ssrInterpolate(unref(t)("source"))} A→Z</option><option value="sourceTypeDesc">${ssrInterpolate(unref(t)("source"))} Z→A</option><option value="durationDesc">${ssrInterpolate(unref(t)("duration"))} (9 → 0)</option><option value="durationAsc">${ssrInterpolate(unref(t)("duration"))} (0 → 9)</option><option disabled>─────────────────</option><option value="leftDesc">${ssrInterpolate(unref(t)("inLeft"))} ON→OFF</option><option value="leftAsc">${ssrInterpolate(unref(t)("inLeft"))} OFF→ON</option><option value="left">${ssrInterpolate(unref(t)("inLeft"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("notLeft"))}</option><option disabled>─────────────────</option><option value="mainDesc">${ssrInterpolate(unref(t)("inMain"))} ON→OFF</option><option value="mainAsc">${ssrInterpolate(unref(t)("inMain"))} OFF→ON</option><option value="main">${ssrInterpolate(unref(t)("inMain"))}</option><option value="noMain">${ssrInterpolate(unref(t)("notMain"))}</option><option disabled>─────────────────</option><option value="rightDesc">${ssrInterpolate(unref(t)("inRight"))} ON→OFF</option><option value="rightAsc">${ssrInterpolate(unref(t)("inRight"))} OFF→ON</option><option value="right">${ssrInterpolate(unref(t)("inRight"))}</option><option value="noRight">${ssrInterpolate(unref(t)("notRight"))}</option><option disabled>─────────────────</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option value="articlesDesc">${ssrInterpolate(unref(t)("articles"))} 9→0</option><option value="articlesAsc">${ssrInterpolate(unref(t)("articles"))} 0→9</option><option value="relatedVideosDesc">${ssrInterpolate(unref(t)("relatedVideos"))} 9→0</option><option value="relatedVideosAsc">${ssrInterpolate(unref(t)("relatedVideos"))} 0→9</option><option disabled>─────────────────</option><option value="showFromAtDesc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortStarted"))} ↓</option><option value="showFromAtAsc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortStarted"))} ↑</option><option value="showToAtDesc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortExpires"))} ↓</option><option value="showToAtAsc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortExpires"))} ↑</option><option disabled>─────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>─────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} (A → Z)</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} (Z → A)</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} (A → Z)</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} (Z → A)</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogVideo/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "VideoTable",
  __ssrInlineRender: true,
  props: {
    videos: { type: Array, default: () => [] },
    selectedVideos: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
    "toggle-left",
    "toggle-main",
    "toggle-right",
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all",
    "approve"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const localVideos = ref([]);
    watch(
      () => props.videos,
      (newVal) => {
        localVideos.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits("update-sort-order", localVideos.value.map((video) => video.id));
    };
    const allSelected = () => {
      return localVideos.value.length && localVideos.value.every((video) => props.selectedVideos.includes(video.id));
    };
    const videoTranslation = (video) => (video == null ? void 0 : video.translation) || {};
    const videoTitle = (video) => {
      var _a;
      return ((_a = videoTranslation(video)) == null ? void 0 : _a.title) || `ID: ${video == null ? void 0 : video.id}`;
    };
    const videoLocale = (video) => {
      var _a;
      return ((_a = videoTranslation(video)) == null ? void 0 : _a.locale) || "";
    };
    const getPrimaryImage = (video) => {
      if (video.images && video.images.length) {
        return [...video.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0];
      }
      return null;
    };
    const imageUrl = (video) => {
      const image = getPrimaryImage(video);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/blog/blog_video_images/default-image.png";
    };
    const imageAlt = (video) => {
      const image = getPrimaryImage(video);
      return (image == null ? void 0 : image.alt) || t("defaultImageAlt");
    };
    const imageTitle = (video) => {
      const image = getPrimaryImage(video);
      return (image == null ? void 0 : image.caption) || t("postImage");
    };
    const formatDate = (dateStr) => {
      if (!dateStr) return "";
      const date = new Date(dateStr);
      if (isNaN(date)) return "";
      return date.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    const ownerTitle = (video) => {
      const owner = video == null ? void 0 : video.owner;
      if (!owner) return t("noData");
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (video) => {
      var _a;
      return ((_a = video == null ? void 0 : video.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const moderationBadge = (status) => {
      const s = Number(status ?? 0);
      if (s === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (s === 2) {
        return {
          text: t("statusSelectRejected"),
          class: "bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300"
        };
      }
      return {
        text: t("underModeration"),
        class: "bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300"
      };
    };
    const privacyBadge = (isPrivate) => {
      if (isPrivate) {
        return {
          text: t("private"),
          class: "bg-fuchsia-100 text-fuchsia-800 border-fuchsia-300 dark:bg-fuchsia-900/40 dark:text-fuchsia-200"
        };
      }
      return {
        text: t("public"),
        class: "bg-sky-100 text-sky-800 border-sky-300 dark:bg-sky-900/40 dark:text-sky-200"
      };
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedVideos.length)}</div>`);
      if (localVideos.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localVideos.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-1 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("owner"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24"><path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path><path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path><path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path><path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path><path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"></path></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("localization"))}><svg class="w-8 h-8 fill-current shrink-0" viewBox="0 0 640 512"><path d="M0 128C0 92.7 28.7 64 64 64l192 0 48 0 16 0 256 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64l-256 0-16 0-48 0L64 448c-35.3 0-64-28.7-64-64L0 128zm320 0l0 256 256 0 0-256-256 0zM178.3 175.9c-3.2-7.2-10.4-11.9-18.3-11.9s-15.1 4.7-18.3 11.9l-64 144c-4.5 10.1 .1 21.9 10.2 26.4s21.9-.1 26.4-10.2l8.9-20.1 73.6 0 8.9 20.1c4.5 10.1 16.3 14.6 26.4 10.2s14.6-16.3 10.2-26.4l-64-144zM160 233.2L179 276l-38 0 19-42.8zM448 164c11 0 20 9 20 20l0 4 44 0 16 0c11 0 20 9 20 20s-9 20-20 20l-2 0-1.6 4.5c-8.9 24.4-22.4 46.6-39.6 65.4c.9 .6 1.8 1.1 2.7 1.6l18.9 11.3c9.5 5.7 12.5 18 6.9 27.4s-18 12.5-27.4 6.9l-18.9-11.3c-4.5-2.7-8.8-5.5-13.1-8.5c-10.6 7.5-21.9 14-34 19.4l-3.6 1.6c-10.1 4.5-21.9-.1-26.4-10.2s.1-21.9 10.2-26.4l3.6-1.6c6.4-2.9 12.6-6.1 18.5-9.8l-12.2-12.2c-7.8-7.8-7.8-20.5 0-28.3s20.5-7.8 28.3 0l14.6 14.6 .5 .5c12.4-13.1 22.5-28.3 29.8-45L448 228l-72 0c-11 0-20-9-20-20s9-20 20-20l52 0 0-4c0-11 9-20 20-20z"></path></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("title"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("type"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("likes"))}><svg class="h-8 w-8 fill-current" viewBox="0 0 32 32"><path d="M22.682 11.318A4.485 4.485 0 0019.5 10a4.377 4.377 0 00-3.5 1.707A4.383 4.383 0 0012.5 10a4.5 4.5 0 00-3.182 7.682L16 24l6.682-6.318a4.5 4.5 0 000-6.364zm-1.4 4.933L16 21.247l-5.285-5A2.5 2.5 0 0112.5 12c1.437 0 2.312.681 3.5 2.625C17.187 12.681 18.062 12 19.5 12a2.5 2.5 0 011.785 4.251h-.003z"></path></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("show"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px"><div class="text-center font-medium"><input type="checkbox"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localVideos.value,
          "onUpdate:modelValue": ($event) => localVideos.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: video }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-1 py-1 text-center cursor-move handle w-px"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-1 py-1 whitespace-nowrap w-px"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${_scopeId}>${ssrInterpolate(video.id)}</div></td><td class="px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(video))}${ssrRenderAttr("title", ownerTitle(video))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("author"))}${_scopeId}></div></td><td class="px-1 py-1 whitespace-nowrap w-px"${_scopeId}><div class="${ssrRenderClass([video.activity ? "bg-blue-500 text-white" : "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100", "text-xs px-1.5 py-0.5 rounded-sm border border-gray-400 uppercase"])}"${ssrRenderAttr("title", unref(t)("localization"))}${_scopeId}>${ssrInterpolate(videoLocale(video).toUpperCase())}</div></td><td class="px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", imageUrl(video))}${ssrRenderAttr("alt", imageAlt(video))}${ssrRenderAttr("title", imageTitle(video))} class="h-6 w-9 object-cover rounded-sm"${_scopeId}></div></td><td class="px-1 py-1"${_scopeId}><div class="text-left"${_scopeId}><a${ssrRenderAttr("href", `/blog/videos/${encodeURIComponent(video.url)}`)} class="text-sky-700 dark:text-sky-200 hover:underline hover:text-amber-700 dark:hover:text-amber-200" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", video.show_from_at ? `${unref(t)("show")}: ${video.show_from_at} / ${video.show_to_at}` : `${formatDate(video.published_at)}`)}${_scopeId}>${ssrInterpolate(videoTitle(video))}</a></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-center justify-center"${_scopeId}><span class="text-[11px] text-indigo-600 dark:text-indigo-200"${_scopeId}>${ssrInterpolate(video.source_type)}</span><span class="${ssrRenderClass([privacyBadge(video.is_private).class, "text-[10px] px-2 py-0 rounded-sm border font-semibold"])}"${_scopeId}>${ssrInterpolate(privacyBadge(video.is_private).text)}</span></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}>${ssrInterpolate(video.views)}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}>${ssrInterpolate(video.likes_count)}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: video.left,
                onToggleLeft: ($event) => _ctx.$emit("toggle-left", video),
                title: video.left ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: video.main,
                onToggleMain: ($event) => _ctx.$emit("toggle-main", video),
                title: video.main ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: video.right,
                onToggleRight: ($event) => _ctx.$emit("toggle-right", video),
                title: video.right ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(video.moderation_status).class, "text-[10px] px-1 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", video.moderation_note && video.moderated_at ? `${video.moderation_note} [${formatDate(video.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(video.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isAdmin: __props.isAdmin,
                status: (video == null ? void 0 : video.moderation_status) ?? 0,
                initialNote: (video == null ? void 0 : video.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => _ctx.$emit("approve", video, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: video.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", video),
                title: video.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.blogVideos.edit", { blogVideo: video.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => _ctx.$emit("delete", video)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedVideos.includes(video.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
            } else {
              return [
                createVNode("tr", { class: "text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800" }, [
                  createVNode("td", { class: "px-1 py-1 text-center cursor-move handle w-px" }, [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 text-gray-500 dark:text-gray-300",
                      fill: "currentColor",
                      viewBox: "0 0 20 20"
                    }, [
                      createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                    ]))
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap w-px" }, [
                    createVNode("div", { class: "text-center text-blue-600 dark:text-blue-200" }, toDisplayString(video.id), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(video),
                        title: ownerTitle(video),
                        class: "h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("author")
                      }, null, 8, ["src", "title", "alt"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap w-px" }, [
                    createVNode("div", {
                      class: ["text-xs px-1.5 py-0.5 rounded-sm border border-gray-400 uppercase", video.activity ? "bg-blue-500 text-white" : "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100"],
                      title: unref(t)("localization")
                    }, toDisplayString(videoLocale(video).toUpperCase()), 11, ["title"])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: imageUrl(video),
                        alt: imageAlt(video),
                        title: imageTitle(video),
                        class: "h-6 w-9 object-cover rounded-sm"
                      }, null, 8, ["src", "alt", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "text-left" }, [
                      createVNode("a", {
                        href: `/blog/videos/${encodeURIComponent(video.url)}`,
                        class: "text-sky-700 dark:text-sky-200 hover:underline hover:text-amber-700 dark:hover:text-amber-200",
                        target: "_blank",
                        rel: "noopener noreferrer",
                        title: video.show_from_at ? `${unref(t)("show")}: ${video.show_from_at} / ${video.show_to_at}` : `${formatDate(video.published_at)}`
                      }, toDisplayString(videoTitle(video)), 9, ["href", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-col items-center justify-center" }, [
                      createVNode("span", { class: "text-[11px] text-indigo-600 dark:text-indigo-200" }, toDisplayString(video.source_type), 1),
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-0 rounded-sm border font-semibold", privacyBadge(video.is_private).class]
                      }, toDisplayString(privacyBadge(video.is_private).text), 3)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, toDisplayString(video.views), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, toDisplayString(video.likes_count), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: video.left,
                        onToggleLeft: ($event) => _ctx.$emit("toggle-left", video),
                        title: video.left ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleLeft", "title"]),
                      createVNode(_sfc_main$6, {
                        isActive: video.main,
                        onToggleMain: ($event) => _ctx.$emit("toggle-main", video),
                        title: video.main ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleMain", "title"]),
                      createVNode(_sfc_main$7, {
                        isActive: video.right,
                        onToggleRight: ($event) => _ctx.$emit("toggle-right", video),
                        title: video.right ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleRight", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-1 py-1 rounded-sm border font-semibold", moderationBadge(video.moderation_status).class],
                        title: video.moderation_note && video.moderated_at ? `${video.moderation_note} [${formatDate(video.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(video.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$8, {
                        isAdmin: __props.isAdmin,
                        status: (video == null ? void 0 : video.moderation_status) ?? 0,
                        initialNote: (video == null ? void 0 : video.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => _ctx.$emit("approve", video, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-2" }, [
                      createVNode(_sfc_main$9, {
                        isActive: video.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", video),
                        title: video.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.blogVideos.edit", { blogVideo: video.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => _ctx.$emit("delete", video)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedVideos.includes(video.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", video.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ])
                ])
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</table>`);
      } else {
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogVideo/Table/VideoTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "VideoCardGrid",
  __ssrInlineRender: true,
  props: {
    videos: { type: Array, default: () => [] },
    selectedVideos: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
    "toggle-left",
    "toggle-main",
    "toggle-right",
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all",
    "approve"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const localVideos = ref([]);
    watch(
      () => props.videos,
      (newVal) => {
        localVideos.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits("update-sort-order", localVideos.value.map((video) => video.id));
    };
    const allSelected = () => {
      return localVideos.value.length && localVideos.value.every((video) => props.selectedVideos.includes(video.id));
    };
    const videoTranslation = (video) => (video == null ? void 0 : video.translation) || {};
    const videoTitle = (video) => {
      var _a;
      return ((_a = videoTranslation(video)) == null ? void 0 : _a.title) || `ID: ${video == null ? void 0 : video.id}`;
    };
    const videoShort = (video) => {
      var _a;
      return ((_a = videoTranslation(video)) == null ? void 0 : _a.short) || "";
    };
    const videoLocale = (video) => {
      var _a;
      return ((_a = videoTranslation(video)) == null ? void 0 : _a.locale) || "";
    };
    const formatDate = (dateStr) => {
      if (!dateStr) return "";
      const date = new Date(dateStr);
      if (isNaN(date)) return "";
      return date.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    const truncateText = (text, maxLength = 80) => {
      if (!text) return "";
      return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + "…" : text;
    };
    const ownerName = (video) => {
      var _a;
      return ((_a = video == null ? void 0 : video.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (video) => {
      var _a;
      return ((_a = video == null ? void 0 : video.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (video) => {
      const owner = video == null ? void 0 : video.owner;
      if (!owner) return t("noData");
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (video) => {
      var _a;
      return ((_a = video == null ? void 0 : video.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const moderationBadge = (status) => {
      const s = Number(status ?? 0);
      if (s === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (s === 2) {
        return {
          text: t("statusSelectRejected"),
          class: "bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300"
        };
      }
      return {
        text: t("underModeration"),
        class: "bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300"
      };
    };
    const privacyBadge = (isPrivate) => {
      if (isPrivate) {
        return {
          text: t("private"),
          class: "bg-fuchsia-100 text-fuchsia-800 border-fuchsia-300 dark:bg-fuchsia-900/40 dark:text-fuchsia-200"
        };
      }
      return {
        text: t("public"),
        class: "bg-sky-100 text-sky-800 border-sky-300 dark:bg-sky-900/40 dark:text-sky-200"
      };
    };
    const preview = (video) => {
      const type = video == null ? void 0 : video.source_type;
      if (type === "local") {
        return { type: "video", src: (video == null ? void 0 : video.video_url) || null };
      }
      if (type === "youtube" || type === "vimeo") {
        return { type: "iframe", src: (video == null ? void 0 : video.embed_url) || null };
      }
      if (type === "code") {
        return { type: "html", html: (video == null ? void 0 : video.embed_code) || "" };
      }
      return { type: "none" };
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedVideos.length)}</div>`);
      if (localVideos.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localVideos.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localVideos.value,
          "onUpdate:modelValue": ($event) => localVideos.value = $event,
          tag: "div",
          "item-key": "id",
          onEnd: handleDragEnd,
          handle: ".handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2"
        }, {
          item: withCtx(({ element: video }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${videoLocale(video)}] : [${video.sort}]`)}${_scopeId}> ID: ${ssrInterpolate(video.id)}</div><div class="${ssrRenderClass([video.activity ? "bg-blue-500 text-white" : "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100", "text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 uppercase"])}"${ssrRenderAttr("title", unref(t)("localization"))}${_scopeId}>${ssrInterpolate(videoLocale(video).toUpperCase())}</div></div><span class="${ssrRenderClass([privacyBadge(video.is_private).class, "text-[10px] px-2 py-0.5 rounded-sm border font-semibold"])}"${_scopeId}>${ssrInterpolate(privacyBadge(video.is_private).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedVideos.includes(video.id)) ? " checked" : ""}${_scopeId}></header><div class="relative w-full bg-slate-200 dark:bg-slate-900"${_scopeId}>`);
              if (preview(video).type === "video" && preview(video).src) {
                _push2(`<video class="h-32 w-full object-cover"${ssrRenderAttr("src", preview(video).src)} controls preload="metadata"${_scopeId}></video>`);
              } else if (preview(video).type === "iframe" && preview(video).src) {
                _push2(`<iframe class="h-64 w-full"${ssrRenderAttr("src", preview(video).src)} allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen loading="lazy"${_scopeId}></iframe>`);
              } else if (preview(video).type === "html" && preview(video).html) {
                _push2(`<div class="h-64 w-full overflow-hidden bg-white dark:bg-slate-800"${_scopeId}>${preview(video).html ?? ""}</div>`);
              } else {
                _push2(`<div class="h-64 w-full flex items-center justify-center text-xs font-semibold text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
              }
              _push2(`</div><div class="flex flex-col flex-1 px-3 py-2 gap-3"${_scopeId}><div class="text-center"${_scopeId}><a${ssrRenderAttr("href", `/blog/videos/${encodeURIComponent(video.url)}`)}${ssrRenderAttr("title", video.source_type)} target="_blank" rel="noopener noreferrer" class="text-lg font-semibold text-center text-sky-700 dark:text-sky-200 hover:underline hover:text-amber-700 dark:hover:text-amber-200 line-clamp-2"${_scopeId}>${ssrInterpolate(videoTitle(video))}</a></div><div class="flex items-center justify-between text-[11px] font-semibold text-slate-600 dark:text-slate-200"${_scopeId}>`);
              if ((video.views ?? 0) > 0) {
                _push2(`<span class="flex flex-row items-center"${ssrRenderAttr("title", unref(t)("views"))}${_scopeId}> 👁 ${ssrInterpolate(video.views)}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if ((video.likes_count ?? 0) > 0) {
                _push2(`<span${ssrRenderAttr("title", unref(t)("likes"))}${_scopeId}> ❤ ${ssrInterpolate(video.likes_count)}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="font-semibold text-[14px] text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(truncateText(videoShort(video)))}</div><div class="flex flex-row items-center justify-center text-center gap-1"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(video))}${ssrRenderAttr("title", ownerTitle(video))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("author"))}${_scopeId}><div class="flex flex-col items-center justify-center"${_scopeId}><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerName(video))}${_scopeId}>${ssrInterpolate(ownerName(video))}</div>`);
              if (ownerEmail(video)) {
                _push2(`<div class="text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerEmail(video))}${_scopeId}>${ssrInterpolate(ownerEmail(video))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div>`);
              if (video.show_from_at) {
                _push2(`<div class="flex flex-col items-center justify-center font-semibold text-center text-[12px] text-slate-500 dark:text-slate-300 border border-dashed border-gray-400 py-1"${_scopeId}>${ssrInterpolate(unref(t)("show"))}: ${ssrInterpolate(video.show_from_at)} / ${ssrInterpolate(video.show_to_at)}</div>`);
              } else {
                _push2(`<div class="flex flex-col items-center justify-center font-semibold text-center text-[12px] text-slate-500 dark:text-slate-300 border border-dashed border-gray-400 py-1"${_scopeId}>${ssrInterpolate(formatDate(video.published_at))}</div>`);
              }
              _push2(`<div class="flex justify-center space-x-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(video.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", video.moderation_note && video.moderated_at ? `${video.moderation_note} [${formatDate(video.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(video.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isAdmin: __props.isAdmin,
                status: (video == null ? void 0 : video.moderation_status) ?? 0,
                initialNote: (video == null ? void 0 : video.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => _ctx.$emit("approve", video, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="flex items-center justify-between px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: video.left,
                onToggleLeft: ($event) => _ctx.$emit("toggle-left", video),
                title: video.left ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: video.main,
                onToggleMain: ($event) => _ctx.$emit("toggle-main", video),
                title: video.main ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: video.right,
                onToggleRight: ($event) => _ctx.$emit("toggle-right", video),
                title: video.right ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: video.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", video),
                title: video.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.blogVideos.edit", { blogVideo: video.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => _ctx.$emit("delete", video)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div>`);
            } else {
              return [
                createVNode("div", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
                  createVNode("header", { class: "flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "handle cursor-move text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100",
                        title: unref(t)("dragDrop")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4",
                          viewBox: "0 0 20 20",
                          fill: "currentColor"
                        }, [
                          createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                        ]))
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100",
                        title: `[${videoLocale(video)}] : [${video.sort}]`
                      }, " ID: " + toDisplayString(video.id), 9, ["title"]),
                      createVNode("div", {
                        class: ["text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 uppercase", video.activity ? "bg-blue-500 text-white" : "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100"],
                        title: unref(t)("localization")
                      }, toDisplayString(videoLocale(video).toUpperCase()), 11, ["title"])
                    ]),
                    createVNode("span", {
                      class: ["text-[10px] px-2 py-0.5 rounded-sm border font-semibold", privacyBadge(video.is_private).class]
                    }, toDisplayString(privacyBadge(video.is_private).text), 3),
                    createVNode("input", {
                      type: "checkbox",
                      checked: __props.selectedVideos.includes(video.id),
                      onChange: ($event) => _ctx.$emit("toggle-select", video.id)
                    }, null, 40, ["checked", "onChange"])
                  ]),
                  createVNode("div", { class: "relative w-full bg-slate-200 dark:bg-slate-900" }, [
                    preview(video).type === "video" && preview(video).src ? (openBlock(), createBlock("video", {
                      key: 0,
                      class: "h-32 w-full object-cover",
                      src: preview(video).src,
                      controls: "",
                      preload: "metadata"
                    }, null, 8, ["src"])) : preview(video).type === "iframe" && preview(video).src ? (openBlock(), createBlock("iframe", {
                      key: 1,
                      class: "h-64 w-full",
                      src: preview(video).src,
                      allow: "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture",
                      allowfullscreen: "",
                      loading: "lazy"
                    }, null, 8, ["src"])) : preview(video).type === "html" && preview(video).html ? (openBlock(), createBlock("div", {
                      key: 2,
                      class: "h-64 w-full overflow-hidden bg-white dark:bg-slate-800",
                      innerHTML: preview(video).html
                    }, null, 8, ["innerHTML"])) : (openBlock(), createBlock("div", {
                      key: 3,
                      class: "h-64 w-full flex items-center justify-center text-xs font-semibold text-slate-700 dark:text-slate-200"
                    }, toDisplayString(unref(t)("noData")), 1))
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 gap-3" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("a", {
                        href: `/blog/videos/${encodeURIComponent(video.url)}`,
                        title: video.source_type,
                        target: "_blank",
                        rel: "noopener noreferrer",
                        class: "text-lg font-semibold text-center text-sky-700 dark:text-sky-200 hover:underline hover:text-amber-700 dark:hover:text-amber-200 line-clamp-2"
                      }, toDisplayString(videoTitle(video)), 9, ["href", "title"])
                    ]),
                    createVNode("div", { class: "flex items-center justify-between text-[11px] font-semibold text-slate-600 dark:text-slate-200" }, [
                      (video.views ?? 0) > 0 ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "flex flex-row items-center",
                        title: unref(t)("views")
                      }, " 👁 " + toDisplayString(video.views), 9, ["title"])) : createCommentVNode("", true),
                      (video.likes_count ?? 0) > 0 ? (openBlock(), createBlock("span", {
                        key: 1,
                        title: unref(t)("likes")
                      }, " ❤ " + toDisplayString(video.likes_count), 9, ["title"])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "font-semibold text-[14px] text-slate-700 dark:text-slate-200" }, toDisplayString(truncateText(videoShort(video))), 1),
                    createVNode("div", { class: "flex flex-row items-center justify-center text-center gap-1" }, [
                      createVNode("img", {
                        src: ownerAvatar(video),
                        title: ownerTitle(video),
                        class: "h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("author")
                      }, null, 8, ["src", "title", "alt"]),
                      createVNode("div", { class: "flex flex-col items-center justify-center" }, [
                        createVNode("div", {
                          class: "mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1",
                          title: ownerName(video)
                        }, toDisplayString(ownerName(video)), 9, ["title"]),
                        ownerEmail(video) ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1",
                          title: ownerEmail(video)
                        }, toDisplayString(ownerEmail(video)), 9, ["title"])) : createCommentVNode("", true)
                      ])
                    ]),
                    video.show_from_at ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex flex-col items-center justify-center font-semibold text-center text-[12px] text-slate-500 dark:text-slate-300 border border-dashed border-gray-400 py-1"
                    }, toDisplayString(unref(t)("show")) + ": " + toDisplayString(video.show_from_at) + " / " + toDisplayString(video.show_to_at), 1)) : (openBlock(), createBlock("div", {
                      key: 1,
                      class: "flex flex-col items-center justify-center font-semibold text-center text-[12px] text-slate-500 dark:text-slate-300 border border-dashed border-gray-400 py-1"
                    }, toDisplayString(formatDate(video.published_at)), 1)),
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(video.moderation_status).class],
                        title: video.moderation_note && video.moderated_at ? `${video.moderation_note} [${formatDate(video.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(video.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$8, {
                        isAdmin: __props.isAdmin,
                        status: (video == null ? void 0 : video.moderation_status) ?? 0,
                        initialNote: (video == null ? void 0 : video.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => _ctx.$emit("approve", video, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-between px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: video.left,
                        onToggleLeft: ($event) => _ctx.$emit("toggle-left", video),
                        title: video.left ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleLeft", "title"]),
                      createVNode(_sfc_main$6, {
                        isActive: video.main,
                        onToggleMain: ($event) => _ctx.$emit("toggle-main", video),
                        title: video.main ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleMain", "title"]),
                      createVNode(_sfc_main$7, {
                        isActive: video.right,
                        onToggleRight: ($event) => _ctx.$emit("toggle-right", video),
                        title: video.right ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleRight", "title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$9, {
                        isActive: video.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", video),
                        title: video.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.blogVideos.edit", { blogVideo: video.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => _ctx.$emit("delete", video)
                      }, null, 8, ["onDelete"])
                    ])
                  ])
                ])
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div>`);
      } else {
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogVideo/View/VideoCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    videos: { type: [Array, Object], default: () => [] },
    videosCount: { type: Number, default: 0 },
    adminBlogVideosProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminBlogVideosPerPage: { type: Number, default: 20 },
    adminBlogVideosDefaultSort: { type: String, default: "idDesc" },
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t, locale } = useI18n();
    const toast = useToast();
    const page = usePage();
    const props = __props;
    const isAdmin = computed(() => {
      var _a, _b, _c;
      const roles = ((_c = (_b = (_a = page.props) == null ? void 0 : _a.auth) == null ? void 0 : _b.user) == null ? void 0 : _c.roles) || [];
      return roles.some((role) => (role == null ? void 0 : role.name) === "admin");
    });
    const getVideoTranslation = (video) => (video == null ? void 0 : video.translation) || {};
    const getVideoTitle = (video) => {
      var _a;
      return ((_a = getVideoTranslation(video)) == null ? void 0 : _a.title) || `ID: ${video == null ? void 0 : video.id}`;
    };
    const getVideoShort = (video) => {
      var _a;
      return ((_a = getVideoTranslation(video)) == null ? void 0 : _a.short) || "";
    };
    const getVideoDescription = (video) => {
      var _a;
      return ((_a = getVideoTranslation(video)) == null ? void 0 : _a.description) || "";
    };
    const normalize = (value) => (value ?? "").toString().trim().toLowerCase();
    const moderationNum = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(value || 0).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const viewMode = ref(localStorage.getItem("admin_view_mode_videos") || "cards");
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_videos", value);
    });
    const itemsPerPage = ref(props.adminBlogVideosPerPage || 20);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountBlogVideos"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminBlogVideosDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortBlogVideos"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing) {
              router.get(
                window.location.pathname,
                {
                  ...Object.fromEntries(new URLSearchParams(window.location.search)),
                  sort: newVal || void 0,
                  page: void 0
                },
                {
                  preserveScroll: true,
                  preserveState: false,
                  replace: true
                }
              );
            }
            toast.info("Сортировка успешно изменена");
          },
          onError: (errors) => {
            toast.error(errors.value || "Ошибка обновления сортировки.");
          }
        }
      );
    });
    const videosList = computed(() => {
      var _a;
      if (Array.isArray(props.videos)) {
        return props.videos;
      }
      if (Array.isArray((_a = props.videos) == null ? void 0 : _a.data)) {
        return props.videos.data;
      }
      return [];
    });
    const localVideos = ref([]);
    watch(
      videosList,
      (newVal) => {
        localVideos.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const showConfirmDeleteModal = ref(false);
    const videoToDeleteId = ref(null);
    const videoToDeleteTitle = ref("");
    const confirmDelete = (videoOrId, title = null) => {
      if (typeof videoOrId === "object") {
        videoToDeleteId.value = videoOrId.id;
        videoToDeleteTitle.value = title || getVideoTitle(videoOrId);
      } else {
        videoToDeleteId.value = videoOrId;
        videoToDeleteTitle.value = title || `ID: ${videoOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      videoToDeleteId.value = null;
      videoToDeleteTitle.value = "";
    };
    const deleteVideo = () => {
      if (videoToDeleteId.value === null) return;
      const idToDelete = videoToDeleteId.value;
      const titleToDelete = videoToDeleteTitle.value;
      router.delete(route("admin.blogVideos.destroy", { blogVideo: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          toast.success(`Видео "${titleToDelete || "ID: " + idToDelete}" удалено.`);
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Видео: ${titleToDelete || "ID: " + idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    const patchLocalVideo = (videoId, callback) => {
      const index = localVideos.value.findIndex((video) => video.id === videoId);
      if (index !== -1) {
        callback(localVideos.value[index]);
      }
    };
    const toggleActivity = (video) => {
      const newActivity = !video.activity;
      const title = getVideoTitle(video);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route("admin.actions.blogVideos.updateActivity", { blogVideo: video.id }),
        { activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalVideo(video.id, (node) => {
              node.activity = newActivity;
            });
            toast.success(`Видео "${title}" ${actionText}.`);
          },
          onError: (errors) => {
            toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`);
          }
        }
      );
    };
    const toggleLeft = (video) => {
      const newLeft = !video.left;
      const title = getVideoTitle(video);
      router.put(
        route("admin.actions.blogVideos.updateLeft", { blogVideo: video.id }),
        { left: newLeft },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalVideo(video.id, (node) => {
              node.left = newLeft;
            });
            toast.success(`Позиция left для видео "${title}" обновлена.`);
          },
          onError: (errors) => {
            toast.error(errors.left || errors.general || `Ошибка изменения left для "${title}".`);
          }
        }
      );
    };
    const toggleMain = (video) => {
      const newMain = !video.main;
      const title = getVideoTitle(video);
      router.put(
        route("admin.actions.blogVideos.updateMain", { blogVideo: video.id }),
        { main: newMain },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalVideo(video.id, (node) => {
              node.main = newMain;
            });
            toast.success(`Позиция main для видео "${title}" обновлена.`);
          },
          onError: (errors) => {
            toast.error(errors.main || errors.general || `Ошибка изменения main для "${title}".`);
          }
        }
      );
    };
    const toggleRight = (video) => {
      const newRight = !video.right;
      const title = getVideoTitle(video);
      router.put(
        route("admin.actions.blogVideos.updateRight", { blogVideo: video.id }),
        { right: newRight },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalVideo(video.id, (node) => {
              node.right = newRight;
            });
            toast.success(`Позиция right для видео "${title}" обновлена.`);
          },
          onError: (errors) => {
            toast.error(errors.right || errors.general || `Ошибка изменения right для "${title}".`);
          }
        }
      );
    };
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const sortVideos = (videos) => {
      const list = (videos || []).slice();
      if (sortParam.value === "activity") return list.filter((video) => !!video.activity);
      if (sortParam.value === "inactive") return list.filter((video) => !video.activity);
      if (sortParam.value === "public") return list.filter((video) => !video.is_private);
      if (sortParam.value === "private") return list.filter((video) => !!video.is_private);
      if (sortParam.value === "left") return list.filter((video) => !!video.left);
      if (sortParam.value === "noLeft") return list.filter((video) => !video.left);
      if (sortParam.value === "main") return list.filter((video) => !!video.main);
      if (sortParam.value === "noMain") return list.filter((video) => !video.main);
      if (sortParam.value === "right") return list.filter((video) => !!video.right);
      if (sortParam.value === "noRight") return list.filter((video) => !video.right);
      if (sortParam.value === "moderationPending") return list.filter((video) => moderationNum(video == null ? void 0 : video.moderation_status) === 0);
      if (sortParam.value === "moderationApproved") return list.filter((video) => moderationNum(video == null ? void 0 : video.moderation_status) === 1);
      if (sortParam.value === "moderationRejected") return list.filter((video) => moderationNum(video == null ? void 0 : video.moderation_status) === 2);
      const sortMap = {
        idAsc: (a, b) => (a.id ?? 0) - (b.id ?? 0),
        idDesc: (a, b) => (b.id ?? 0) - (a.id ?? 0),
        sortAsc: (a, b) => (a.sort ?? 0) - (b.sort ?? 0),
        sortDesc: (a, b) => (b.sort ?? 0) - (a.sort ?? 0),
        titleAsc: (a, b) => normalize(getVideoTitle(a)).localeCompare(normalize(getVideoTitle(b)), locale.value),
        titleDesc: (a, b) => normalize(getVideoTitle(b)).localeCompare(normalize(getVideoTitle(a)), locale.value),
        urlAsc: (a, b) => normalize(a == null ? void 0 : a.url).localeCompare(normalize(b == null ? void 0 : b.url), locale.value),
        urlDesc: (a, b) => normalize(b == null ? void 0 : b.url).localeCompare(normalize(a == null ? void 0 : a.url), locale.value),
        activityAsc: (a, b) => Number(a.activity) - Number(b.activity),
        activityDesc: (a, b) => Number(b.activity) - Number(a.activity),
        privateAsc: (a, b) => Number(a.is_private) - Number(b.is_private),
        privateDesc: (a, b) => Number(b.is_private) - Number(a.is_private),
        viewsAsc: (a, b) => (a.views ?? 0) - (b.views ?? 0),
        viewsDesc: (a, b) => (b.views ?? 0) - (a.views ?? 0),
        likesAsc: (a, b) => (a.likes_count ?? 0) - (b.likes_count ?? 0),
        likesDesc: (a, b) => (b.likes_count ?? 0) - (a.likes_count ?? 0),
        commentsAsc: (a, b) => (a.comments_count ?? 0) - (b.comments_count ?? 0),
        commentsDesc: (a, b) => (b.comments_count ?? 0) - (a.comments_count ?? 0),
        sourceTypeAsc: (a, b) => normalize(a == null ? void 0 : a.source_type).localeCompare(normalize(b == null ? void 0 : b.source_type), locale.value),
        sourceTypeDesc: (a, b) => normalize(b == null ? void 0 : b.source_type).localeCompare(normalize(a == null ? void 0 : a.source_type), locale.value),
        durationAsc: (a, b) => (a.duration ?? 0) - (b.duration ?? 0),
        durationDesc: (a, b) => (b.duration ?? 0) - (a.duration ?? 0),
        leftAsc: (a, b) => Number(a.left) - Number(b.left),
        leftDesc: (a, b) => Number(b.left) - Number(a.left),
        mainAsc: (a, b) => Number(a.main) - Number(b.main),
        mainDesc: (a, b) => Number(b.main) - Number(a.main),
        rightAsc: (a, b) => Number(a.right) - Number(b.right),
        rightDesc: (a, b) => Number(b.right) - Number(a.right),
        imagesAsc: (a, b) => (a.images_count ?? 0) - (b.images_count ?? 0),
        imagesDesc: (a, b) => (b.images_count ?? 0) - (a.images_count ?? 0),
        articlesAsc: (a, b) => (a.articles_count ?? 0) - (b.articles_count ?? 0),
        articlesDesc: (a, b) => (b.articles_count ?? 0) - (a.articles_count ?? 0),
        relatedVideosAsc: (a, b) => (a.related_videos_count ?? 0) - (b.related_videos_count ?? 0),
        relatedVideosDesc: (a, b) => (b.related_videos_count ?? 0) - (a.related_videos_count ?? 0),
        showFromAtAsc: (a, b) => safeDate(a.show_from_at) - safeDate(b.show_from_at),
        showFromAtDesc: (a, b) => safeDate(b.show_from_at) - safeDate(a.show_from_at),
        showToAtAsc: (a, b) => safeDate(a.show_to_at) - safeDate(b.show_to_at),
        showToAtDesc: (a, b) => safeDate(b.show_to_at) - safeDate(a.show_to_at),
        publishedAtAsc: (a, b) => safeDate(a.published_at) - safeDate(b.published_at),
        publishedAtDesc: (a, b) => safeDate(b.published_at) - safeDate(a.published_at),
        createdAtAsc: (a, b) => safeDate(a.created_at) - safeDate(b.created_at),
        createdAtDesc: (a, b) => safeDate(b.created_at) - safeDate(a.created_at),
        updatedAtAsc: (a, b) => safeDate(a.updated_at) - safeDate(b.updated_at),
        updatedAtDesc: (a, b) => safeDate(b.updated_at) - safeDate(a.updated_at),
        moderationStatusAsc: (a, b) => moderationNum(a == null ? void 0 : a.moderation_status) - moderationNum(b == null ? void 0 : b.moderation_status),
        moderationStatusDesc: (a, b) => moderationNum(b == null ? void 0 : b.moderation_status) - moderationNum(a == null ? void 0 : a.moderation_status),
        ownerNameAsc: (a, b) => {
          var _a, _b;
          return normalize((_a = a == null ? void 0 : a.owner) == null ? void 0 : _a.name).localeCompare(normalize((_b = b == null ? void 0 : b.owner) == null ? void 0 : _b.name), locale.value);
        },
        ownerNameDesc: (a, b) => {
          var _a, _b;
          return normalize((_a = b == null ? void 0 : b.owner) == null ? void 0 : _a.name).localeCompare(normalize((_b = a == null ? void 0 : a.owner) == null ? void 0 : _b.name), locale.value);
        },
        ownerEmailAsc: (a, b) => {
          var _a, _b;
          return normalize((_a = a == null ? void 0 : a.owner) == null ? void 0 : _a.email).localeCompare(normalize((_b = b == null ? void 0 : b.owner) == null ? void 0 : _b.email), locale.value);
        },
        ownerEmailDesc: (a, b) => {
          var _a, _b;
          return normalize((_a = b == null ? void 0 : b.owner) == null ? void 0 : _a.email).localeCompare(normalize((_b = a == null ? void 0 : a.owner) == null ? void 0 : _b.email), locale.value);
        }
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredVideos = computed(() => {
      let filtered = localVideos.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortVideos(filtered);
      }
      filtered = filtered.filter((video) => {
        var _a, _b, _c, _d;
        const values = [
          video == null ? void 0 : video.id,
          video == null ? void 0 : video.url,
          video == null ? void 0 : video.external_video_id,
          video == null ? void 0 : video.source_type,
          video == null ? void 0 : video.embed_code,
          video == null ? void 0 : video.moderation_note,
          getVideoTitle(video),
          getVideoShort(video),
          getVideoDescription(video),
          (_a = video == null ? void 0 : video.owner) == null ? void 0 : _a.name,
          (_b = video == null ? void 0 : video.owner) == null ? void 0 : _b.email,
          (_c = video == null ? void 0 : video.moderator) == null ? void 0 : _c.name,
          (_d = video == null ? void 0 : video.moderator) == null ? void 0 : _d.email
        ];
        return values.some((value) => normalize(value).includes(query));
      });
      return sortVideos(filtered);
    });
    const paginatedVideos = computed(() => {
      const perPage = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * perPage;
      return filteredVideos.value.slice(start, start + perPage);
    });
    const displayedVideos = computed(() => {
      return props.useServerProcessing ? videosList.value : paginatedVideos.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const selectedVideos = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedVideos.value.map((video) => video.id);
      if (checked) {
        selectedVideos.value = [.../* @__PURE__ */ new Set([...selectedVideos.value, ...ids])];
      } else {
        selectedVideos.value = selectedVideos.value.filter((id) => !ids.includes(id));
      }
    };
    const toggleSelectVideo = (videoId) => {
      const index = selectedVideos.value.indexOf(videoId);
      if (index > -1) {
        selectedVideos.value.splice(index, 1);
      } else {
        selectedVideos.value.push(videoId);
      }
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedVideos.value.length) {
        toast.warning("Выберите видео для активации/деактивации");
        return;
      }
      const idsToUpdate = [...selectedVideos.value];
      router.put(
        route("admin.actions.blogVideos.bulkUpdateActivity"),
        { ids: idsToUpdate, activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localVideos.value = localVideos.value.map((video) => {
              return idsToUpdate.includes(video.id) ? { ...video, activity: newActivity } : video;
            });
            selectedVideos.value = [];
            toast.success("Активность видео массово обновлена");
          },
          onError: (errors) => {
            const msg = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности";
            toast.error(msg);
          }
        }
      );
    };
    const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
      if (!selectedVideos.value.length) {
        toast.warning("Выберите видео для массового действия");
        return;
      }
      const idsToUpdate = [...selectedVideos.value];
      router.put(
        route(routeName),
        { ids: idsToUpdate, [field]: newValue },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localVideos.value = localVideos.value.map((video) => {
              return idsToUpdate.includes(video.id) ? { ...video, [field]: newValue } : video;
            });
            selectedVideos.value = [];
            toast.success(successMessage);
          },
          onError: (errors) => {
            const msg = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления";
            toast.error(msg);
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedVideos.value.length) {
        toast.warning("Выберите хотя бы одно видео для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные видео?")) return;
      router.delete(route("admin.actions.blogVideos.bulkDestroy"), {
        data: { ids: selectedVideos.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          selectedVideos.value = [];
          toast.success("Массовое удаление видео успешно завершено.");
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          toast.error(errors[errorKey] || "Произошла ошибка при удалении видео.");
        }
      });
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") {
        toggleAll({ target: { checked: true } });
      } else if (action === "deselectAll") {
        toggleAll({ target: { checked: false } });
      } else if (action === "activate") {
        bulkToggleActivity(true);
      } else if (action === "deactivate") {
        bulkToggleActivity(false);
      } else if (action === "left") {
        bulkToggleFlag("left", true, "admin.actions.blogVideos.bulkUpdateLeft", "Видео добавлены в левую колонку");
      } else if (action === "noLeft") {
        bulkToggleFlag("left", false, "admin.actions.blogVideos.bulkUpdateLeft", "Видео убраны из левой колонки");
      } else if (action === "main") {
        bulkToggleFlag("main", true, "admin.actions.blogVideos.bulkUpdateMain", "Видео добавлены в главный блок");
      } else if (action === "noMain") {
        bulkToggleFlag("main", false, "admin.actions.blogVideos.bulkUpdateMain", "Видео убраны из главного блока");
      } else if (action === "right") {
        bulkToggleFlag("right", true, "admin.actions.blogVideos.bulkUpdateRight", "Видео добавлены в правую колонку");
      } else if (action === "noRight") {
        bulkToggleFlag("right", false, "admin.actions.blogVideos.bulkUpdateRight", "Видео убраны из правой колонки");
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const approveVideo = (video, status = 1, note = "") => {
      if (!(video == null ? void 0 : video.id)) return;
      router.put(
        route("admin.actions.blogVideos.approve", { blogVideo: video.id }),
        { moderation_status: status, moderation_note: note },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalVideo(video.id, (node) => {
              node.moderation_status = status;
              node.is_approved = status === 1;
              node.moderation_note = note;
            });
            toast.success(status === 1 ? "Видео одобрено" : "Видео отклонено");
          },
          onError: () => toast.error("Ошибка модерации видео")
        }
      );
    };
    const handleSortOrderUpdate = (newOrderIds) => {
      const items = newOrderIds.map((id, index) => ({
        id,
        sort: index
      }));
      if (!items.length) return;
      router.put(
        route("admin.actions.blogVideos.updateSortBulk"),
        { items },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.success("Сортировка видео обновлена"),
          onError: (errors) => {
            console.error("Ошибка сортировки видео:", errors);
            toast.error(errors.message || "Ошибка обновления сортировки");
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("videos")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("videos"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("videos")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("videos")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$c, {
              href: _ctx.route("admin.blogVideos.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addVideo"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addVideo")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              "setting-key": "adminBlogVideosProcessingMode",
              mode: __props.adminBlogVideosProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.videosCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.videosCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$e, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.videosCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$f, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.videosCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountBlogVideos"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$3, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (val) => sortParam.value = val
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.videosCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$i, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.videosCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.videosCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              if (__props.videosCount) {
                _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              _push2(ssrRenderComponent(_sfc_main$j, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.videosCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredVideos.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.videos }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                videos: displayedVideos.value,
                "selected-videos": selectedVideos.value,
                "is-admin": isAdmin.value,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectVideo,
                onToggleAll: toggleAll,
                onApprove: approveVideo
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                videos: displayedVideos.value,
                "selected-videos": selectedVideos.value,
                "is-admin": isAdmin.value,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectVideo,
                onToggleAll: toggleAll,
                onApprove: approveVideo
              }, null, _parent2, _scopeId));
            }
            if (__props.videosCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredVideos.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.videos }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$m, {
              show: showConfirmDeleteModal.value,
              onClose: closeModal,
              onCancel: closeModal,
              onConfirm: deleteVideo,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$c, {
                      href: _ctx.route("admin.blogVideos.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addVideo")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$d, {
                      "setting-key": "adminBlogVideosProcessingMode",
                      mode: __props.adminBlogVideosProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.videosCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.videosCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.videosCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$f, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.videosCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountBlogVideos"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.videosCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$i, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.videosCount), 1)
                      ]),
                      _: 1
                    }),
                    __props.videosCount ? (openBlock(), createBlock(_sfc_main$4, {
                      key: 0,
                      onChange: handleBulkAction
                    })) : createCommentVNode("", true),
                    createVNode(_sfc_main$j, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.videosCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredVideos.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.videos
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    videos: displayedVideos.value,
                    "selected-videos": selectedVideos.value,
                    "is-admin": isAdmin.value,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectVideo,
                    onToggleAll: toggleAll,
                    onApprove: approveVideo
                  }, null, 8, ["videos", "selected-videos", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    videos: displayedVideos.value,
                    "selected-videos": selectedVideos.value,
                    "is-admin": isAdmin.value,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectVideo,
                    onToggleAll: toggleAll,
                    onApprove: approveVideo
                  }, null, 8, ["videos", "selected-videos", "is-admin"])),
                  __props.videosCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredVideos.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.videos
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$m, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteVideo,
                cancelText: unref(t)("cancel"),
                confirmText: unref(t)("yesDelete")
              }, null, 8, ["show", "cancelText", "confirmText"])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Blog/BlogVideos/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
