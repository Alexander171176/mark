import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, Fragment, renderList, createCommentVNode, computed, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent, ssrRenderList, ssrRenderClass } from "vue/server-renderer";
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
import draggable from "vuedraggable";
import { _ as _sfc_main$5, a as _sfc_main$6, b as _sfc_main$7 } from "./RightToggle-r8SYzaEU.js";
import { _ as _sfc_main$9 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$b } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$a } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$8 } from "./ModerationButton-D_ehimPY.js";
import { _ as _sfc_main$d } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$f, a as _sfc_main$l } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$h } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogArticle/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit mt-2 mb-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>────────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>────────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option disabled>────────────────────</option><option value="urlAsc">URL A→Z</option><option value="urlDesc">URL Z→A</option><option disabled>────────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option><option disabled>────────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="showFromAtDesc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortStarted"))} ↓ </option><option value="showFromAtAsc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortStarted"))} ↑ </option><option value="showToAtDesc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortExpires"))} ↓ </option><option value="showToAtAsc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortExpires"))} ↑ </option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>────────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option value="likesDesc">${ssrInterpolate(unref(t)("likes"))} 9→0</option><option value="likesAsc">${ssrInterpolate(unref(t)("likes"))} 0→9</option><option value="commentsDesc">${ssrInterpolate(unref(t)("comments"))} 9→0</option><option value="commentsAsc">${ssrInterpolate(unref(t)("comments"))} 0→9</option><option disabled>────────────────────</option><option value="rubricsDesc">${ssrInterpolate(unref(t)("rubrics"))} 9→0</option><option value="rubricsAsc">${ssrInterpolate(unref(t)("rubrics"))} 0→9</option><option value="tagsDesc">${ssrInterpolate(unref(t)("tags"))} 9→0</option><option value="tagsAsc">${ssrInterpolate(unref(t)("tags"))} 0→9</option><option value="videosDesc">${ssrInterpolate(unref(t)("videos"))} 9→0</option><option value="videosAsc">${ssrInterpolate(unref(t)("videos"))} 0→9</option><option disabled>────────────────────</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option value="relatedArticlesDesc">${ssrInterpolate(unref(t)("relatedArticles"))} 9→0</option><option value="relatedArticlesAsc">${ssrInterpolate(unref(t)("relatedArticles"))} 0→9</option><option disabled>────────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>────────────────────</option><option value="leftDesc">${ssrInterpolate(unref(t)("inLeft"))} ON→OFF</option><option value="leftAsc">${ssrInterpolate(unref(t)("inLeft"))} OFF→ON</option><option value="left">${ssrInterpolate(unref(t)("inLeft"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("notLeft"))}</option><option disabled>────────────────────</option><option value="mainDesc">${ssrInterpolate(unref(t)("inMain"))} ON→OFF</option><option value="mainAsc">${ssrInterpolate(unref(t)("inMain"))} OFF→ON</option><option value="main">${ssrInterpolate(unref(t)("inMain"))}</option><option value="noMain">${ssrInterpolate(unref(t)("notMain"))}</option><option disabled>────────────────────</option><option value="rightDesc">${ssrInterpolate(unref(t)("inRight"))} ON→OFF</option><option value="rightAsc">${ssrInterpolate(unref(t)("inRight"))} OFF→ON</option><option value="right">${ssrInterpolate(unref(t)("inRight"))}</option><option value="noRight">${ssrInterpolate(unref(t)("notRight"))}</option><option disabled>────────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>────────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogArticle/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "ArticleTable",
  __ssrInlineRender: true,
  props: {
    articles: { type: Array, default: () => [] },
    selectedArticles: { type: Array, default: () => [] },
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
    const localArticles = ref([]);
    watch(
      () => props.articles,
      (newVal) => {
        localArticles.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits("update-sort-order", localArticles.value.map((article) => article.id));
    };
    const allSelected = () => {
      return localArticles.value.length && localArticles.value.every((article) => props.selectedArticles.includes(article.id));
    };
    const articleTranslation = (article) => (article == null ? void 0 : article.translation) || {};
    const articleTitle = (article) => {
      var _a;
      return ((_a = articleTranslation(article)) == null ? void 0 : _a.title) || `ID: ${article == null ? void 0 : article.id}`;
    };
    const rubricTitle = (rubric) => {
      var _a;
      return (rubric == null ? void 0 : rubric.title) || ((_a = rubric == null ? void 0 : rubric.translation) == null ? void 0 : _a.title) || `ID: ${rubric == null ? void 0 : rubric.id}`;
    };
    const getPrimaryImage = (article) => {
      if (article.images && article.images.length) {
        return [...article.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0];
      }
      return null;
    };
    const imageUrl = (article) => {
      const image = getPrimaryImage(article);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/blog/blog_article_images/default-image.png";
    };
    const imageAlt = (article) => {
      const image = getPrimaryImage(article);
      return (image == null ? void 0 : image.alt) || t("defaultImageAlt");
    };
    const imageTitle = (article) => {
      const image = getPrimaryImage(article);
      return (image == null ? void 0 : image.caption) || t("postImage");
    };
    const ownerTitle = (article) => {
      const owner = article == null ? void 0 : article.owner;
      if (!owner) return t("noData");
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (article) => {
      var _a;
      return ((_a = article == null ? void 0 : article.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
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
    const truncateText = (text, maxLength = 30) => {
      if (!text) return "";
      return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + "…" : text;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedArticles.length)}</div>`);
      if (localArticles.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localArticles.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-1 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("owner"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24"><path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path><path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path><path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path><path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path><path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"></path></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("title"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("rubrics"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><circle class="fill-current text-cyan-600" cx="16" cy="8" r="8"></circle><circle class="fill-current text-cyan-400" cx="8" cy="16" r="8"></circle></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("likes"))}><svg class="h-7 w-7 fill-current" viewBox="0 0 32 32"><path class="fill-current text-red-600 dark:text-red-300" d="M22.682 11.318A4.485 4.485 0 0019.5 10a4.377 4.377 0 00-3.5 1.707A4.383 4.383 0 0012.5 10a4.5 4.5 0 00-3.182 7.682L16 24l6.682-6.318a4.5 4.5 0 000-6.364zm-1.4 4.933L16 21.247l-5.285-5A2.5 2.5 0 0112.5 12c1.437 0 2.312.681 3.5 2.625C17.187 12.681 18.062 12 19.5 12a2.5 2.5 0 011.785 4.251h-.003z"></path></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("show"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-1 py-1 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localArticles.value,
          "onUpdate:modelValue": ($event) => localArticles.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: article }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-1 py-1 text-center cursor-move handle w-px"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-1 py-1 whitespace-nowrap w-px"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${ssrRenderAttr("title", `[${article.sort}] / ${formatDate(article.published_at)}`)}${_scopeId}>${ssrInterpolate(article.id)}</div></td><td class="px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(article))}${ssrRenderAttr("title", ownerTitle(article))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("author"))}${_scopeId}></div></td><td class="px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", imageUrl(article))}${ssrRenderAttr("alt", imageAlt(article))}${ssrRenderAttr("title", imageTitle(article))} class="h-6 w-8 object-cover rounded-sm"${_scopeId}></div></td><td class="px-1 py-1"${_scopeId}><div class="text-left"${_scopeId}><a${ssrRenderAttr("href", `/blog/articles/${encodeURIComponent(article.url)}`)} class="text-sky-700 dark:text-sky-200 text-xs hover:underline hover:text-amber-700 dark:hover:text-amber-200" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", article.show_from_at ? `${unref(t)("show")}: ${article.show_from_at} / ${article.show_to_at}` : `${formatDate(article.published_at)}`)}${_scopeId}>${ssrInterpolate(truncateText(articleTitle(article), 90))}</a></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-left"${_scopeId}><!--[-->`);
              ssrRenderList(article.rubrics, (rubric) => {
                _push2(`<span${ssrRenderAttr("title", rubricTitle(rubric))} class="py-0.5 px-1.5 mr-0.5 badge bg-indigo-500 dark:bg-indigo-200 rounded-sm text-xs text-slate-100 dark:text-slate-900"${_scopeId}>${ssrInterpolate(rubric.id)}</span>`);
              });
              _push2(`<!--]--></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}>${ssrInterpolate(article.views)}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}>${ssrInterpolate(article.likes_count)}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: article.left,
                onToggleLeft: ($event) => _ctx.$emit("toggle-left", article),
                title: article.left ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: article.main,
                onToggleMain: ($event) => _ctx.$emit("toggle-main", article),
                title: article.main ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: article.right,
                onToggleRight: ($event) => _ctx.$emit("toggle-right", article),
                title: article.right ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(article.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", article.moderation_note && article.moderated_at ? `${article.moderation_note} [${formatDate(article.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(article.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isAdmin: __props.isAdmin,
                status: (article == null ? void 0 : article.moderation_status) ?? 0,
                initialNote: (article == null ? void 0 : article.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => _ctx.$emit("approve", article, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: article.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", article),
                title: article.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.blogArticles.edit", { blogArticle: article.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => _ctx.$emit("delete", article)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedArticles.includes(article.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                    createVNode("div", {
                      class: "text-center text-blue-600 dark:text-blue-200",
                      title: `[${article.sort}] / ${formatDate(article.published_at)}`
                    }, toDisplayString(article.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(article),
                        title: ownerTitle(article),
                        class: "h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("author")
                      }, null, 8, ["src", "title", "alt"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: imageUrl(article),
                        alt: imageAlt(article),
                        title: imageTitle(article),
                        class: "h-6 w-8 object-cover rounded-sm"
                      }, null, 8, ["src", "alt", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "text-left" }, [
                      createVNode("a", {
                        href: `/blog/articles/${encodeURIComponent(article.url)}`,
                        class: "text-sky-700 dark:text-sky-200 text-xs hover:underline hover:text-amber-700 dark:hover:text-amber-200",
                        target: "_blank",
                        rel: "noopener noreferrer",
                        title: article.show_from_at ? `${unref(t)("show")}: ${article.show_from_at} / ${article.show_to_at}` : `${formatDate(article.published_at)}`
                      }, toDisplayString(truncateText(articleTitle(article), 90)), 9, ["href", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-left" }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(article.rubrics, (rubric) => {
                        return openBlock(), createBlock("span", {
                          key: rubric.id,
                          title: rubricTitle(rubric),
                          class: "py-0.5 px-1.5 mr-0.5 badge bg-indigo-500 dark:bg-indigo-200 rounded-sm text-xs text-slate-100 dark:text-slate-900"
                        }, toDisplayString(rubric.id), 9, ["title"]);
                      }), 128))
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, toDisplayString(article.views), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, toDisplayString(article.likes_count), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: article.left,
                        onToggleLeft: ($event) => _ctx.$emit("toggle-left", article),
                        title: article.left ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleLeft", "title"]),
                      createVNode(_sfc_main$6, {
                        isActive: article.main,
                        onToggleMain: ($event) => _ctx.$emit("toggle-main", article),
                        title: article.main ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleMain", "title"]),
                      createVNode(_sfc_main$7, {
                        isActive: article.right,
                        onToggleRight: ($event) => _ctx.$emit("toggle-right", article),
                        title: article.right ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleRight", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(article.moderation_status).class],
                        title: article.moderation_note && article.moderated_at ? `${article.moderation_note} [${formatDate(article.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(article.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$8, {
                        isAdmin: __props.isAdmin,
                        status: (article == null ? void 0 : article.moderation_status) ?? 0,
                        initialNote: (article == null ? void 0 : article.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => _ctx.$emit("approve", article, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode(_sfc_main$9, {
                        isActive: article.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", article),
                        title: article.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.blogArticles.edit", { blogArticle: article.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => _ctx.$emit("delete", article)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedArticles.includes(article.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", article.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogArticle/Table/ArticleTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "ArticleCardGrid",
  __ssrInlineRender: true,
  props: {
    articles: { type: Array, default: () => [] },
    selectedArticles: { type: Array, default: () => [] },
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
    const localArticles = ref([]);
    watch(
      () => props.articles,
      (newVal) => {
        localArticles.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits("update-sort-order", localArticles.value.map((article) => article.id));
    };
    const allSelected = () => {
      return localArticles.value.length && localArticles.value.every((article) => props.selectedArticles.includes(article.id));
    };
    const articleTranslation = (article) => (article == null ? void 0 : article.translation) || {};
    const articleTitle = (article) => {
      var _a;
      return ((_a = articleTranslation(article)) == null ? void 0 : _a.title) || `ID: ${article == null ? void 0 : article.id}`;
    };
    const articleShort = (article) => {
      var _a;
      return ((_a = articleTranslation(article)) == null ? void 0 : _a.short) || "";
    };
    const rubricTitle = (rubric) => {
      var _a;
      return (rubric == null ? void 0 : rubric.title) || ((_a = rubric == null ? void 0 : rubric.translation) == null ? void 0 : _a.title) || `ID: ${rubric == null ? void 0 : rubric.id}`;
    };
    const getPrimaryImage = (article) => {
      if (article.images && article.images.length) {
        return [...article.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0];
      }
      return null;
    };
    const imageUrl = (article) => {
      const image = getPrimaryImage(article);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/blog/blog_article_images/default-image.png";
    };
    const imageAlt = (article) => {
      const image = getPrimaryImage(article);
      return (image == null ? void 0 : image.alt) || t("defaultImageAlt");
    };
    const imageTitle = (article) => {
      const image = getPrimaryImage(article);
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
    const truncateText = (text, maxLength = 80) => {
      if (!text) return "";
      return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + "…" : text;
    };
    const ownerName = (article) => {
      var _a;
      return ((_a = article == null ? void 0 : article.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (article) => {
      var _a;
      return ((_a = article == null ? void 0 : article.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (article) => {
      const owner = article == null ? void 0 : article.owner;
      if (!owner) return t("noData");
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (article) => {
      var _a;
      return ((_a = article == null ? void 0 : article.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
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
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedArticles.length)}</div>`);
      if (localArticles.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localArticles.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localArticles.value,
          "onUpdate:modelValue": ($event) => localArticles.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd,
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: article }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${article.sort}] / ${formatDate(article.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(article.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}><span class="${ssrRenderClass([moderationBadge(article.moderation_status).class, "text-[10px] px-2 py-0.5 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", article.moderation_note && article.moderated_at ? `${article.moderation_note} [${formatDate(article.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(article.moderation_status).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedArticles.includes(article.id)) ? " checked" : ""}${_scopeId}></div></header><div class="relative w-full bg-slate-200 dark:bg-slate-900"${_scopeId}><img${ssrRenderAttr("src", imageUrl(article))}${ssrRenderAttr("alt", imageAlt(article))}${ssrRenderAttr("title", imageTitle(article))} class="h-32 w-full object-cover"${_scopeId}></div><div class="flex flex-col flex-1 px-3 py-2 space-y-2"${_scopeId}><div class="flex flex-col items-center justify-center text-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(article))}${ssrRenderAttr("title", ownerTitle(article))} class="h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("author"))}${_scopeId}><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerName(article))}${_scopeId}>${ssrInterpolate(ownerName(article))}</div>`);
              if (ownerEmail(article)) {
                _push2(`<div class="text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerEmail(article))}${_scopeId}>${ssrInterpolate(ownerEmail(article))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (article.show_from_at) {
                _push2(`<div class="flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("show"))}: ${ssrInterpolate(article.show_from_at)} / ${ssrInterpolate(article.show_to_at)}</div>`);
              } else {
                _push2(`<div class="flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(formatDate(article.published_at))}</div>`);
              }
              _push2(`<a${ssrRenderAttr("href", `/blog/articles/${encodeURIComponent(article.url)}`)} target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-sky-700 dark:text-sky-200 hover:underline hover:text-amber-700 dark:hover:text-amber-200 line-clamp-2 text-center"${_scopeId}>${ssrInterpolate(truncateText(articleTitle(article), 90))}</a><div class="flex items-center justify-center gap-3 text-[11px] font-semibold text-slate-600 dark:text-slate-200"${_scopeId}>`);
              if ((article.views ?? 0) > 0) {
                _push2(`<div class="flex items-center justify-center space-x-1"${ssrRenderAttr("title", unref(t)("views"))}${_scopeId}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"${_scopeId}><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"${_scopeId}></path></svg><span class="text-[12px] text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(article.views)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              if ((article.likes_count ?? 0) > 0) {
                _push2(`<div class="flex items-center justify-center space-x-1"${ssrRenderAttr("title", unref(t)("likes"))}${_scopeId}><svg class="h-7 w-7 fill-current" viewBox="0 0 32 32"${_scopeId}><path class="fill-current text-red-600 dark:text-red-300" d="M22.682 11.318A4.485 4.485 0 0019.5 10a4.377 4.377 0 00-3.5 1.707A4.383 4.383 0 0012.5 10a4.5 4.5 0 00-3.182 7.682L16 24l6.682-6.318a4.5 4.5 0 000-6.364zm-1.4 4.933L16 21.247l-5.285-5A2.5 2.5 0 0112.5 12c1.437 0 2.312.681 3.5 2.625C17.187 12.681 18.062 12 19.5 12a2.5 2.5 0 011.785 4.251h-.003z"${_scopeId}></path></svg><span class="text-[12px] text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(article.likes_count)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="font-semibold text-[12px] text-center text-teal-700 dark:text-teal-200"${_scopeId}>${ssrInterpolate(truncateText(articleShort(article), 120))}</div><div class="flex flex-col justify-center"${_scopeId}><div class="text-center text-[11px] font-medium text-slate-600 dark:text-slate-300 mb-0.5"${_scopeId}>${ssrInterpolate(unref(t)("rubrics"))}: </div>`);
              if (article.rubrics && article.rubrics.length) {
                _push2(`<div class="flex justify-center flex-wrap gap-1"${_scopeId}><!--[-->`);
                ssrRenderList(article.rubrics, (rubric) => {
                  _push2(`<span${ssrRenderAttr("title", rubricTitle(rubric))} class="py-0.5 px-1.5 badge bg-indigo-500 dark:bg-indigo-200 rounded-sm text-[10px] text-slate-100 dark:text-slate-900"${_scopeId}>${ssrInterpolate(rubric.id)}</span>`);
                });
                _push2(`<!--]--></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="flex justify-center space-x-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(article.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", article.moderation_note && article.moderated_at ? `${article.moderation_note} [${formatDate(article.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(article.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isAdmin: __props.isAdmin,
                status: (article == null ? void 0 : article.moderation_status) ?? 0,
                initialNote: (article == null ? void 0 : article.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => _ctx.$emit("approve", article, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="flex items-center justify-between px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: article.left,
                onToggleLeft: ($event) => _ctx.$emit("toggle-left", article),
                title: article.left ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: article.main,
                onToggleMain: ($event) => _ctx.$emit("toggle-main", article),
                title: article.main ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: article.right,
                onToggleRight: ($event) => _ctx.$emit("toggle-right", article),
                title: article.right ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: article.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", article),
                title: article.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.blogArticles.edit", { blogArticle: article.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => _ctx.$emit("delete", article)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div>`);
            } else {
              return [
                createVNode("div", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
                  createVNode("header", { class: "flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100",
                        title: unref(t)("dragDrop")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4",
                          fill: "currentColor",
                          viewBox: "0 0 20 20"
                        }, [
                          createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                        ]))
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100",
                        title: `[${article.sort}] / ${formatDate(article.published_at)}`
                      }, " ID: " + toDisplayString(article.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border font-semibold", moderationBadge(article.moderation_status).class],
                        title: article.moderation_note && article.moderated_at ? `${article.moderation_note} [${formatDate(article.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(article.moderation_status).text), 11, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedArticles.includes(article.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", article.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "relative w-full bg-slate-200 dark:bg-slate-900" }, [
                    createVNode("img", {
                      src: imageUrl(article),
                      alt: imageAlt(article),
                      title: imageTitle(article),
                      class: "h-32 w-full object-cover"
                    }, null, 8, ["src", "alt", "title"])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-2" }, [
                    createVNode("div", { class: "flex flex-col items-center justify-center text-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(article),
                        title: ownerTitle(article),
                        class: "h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("author")
                      }, null, 8, ["src", "title", "alt"]),
                      createVNode("div", {
                        class: "mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1",
                        title: ownerName(article)
                      }, toDisplayString(ownerName(article)), 9, ["title"]),
                      ownerEmail(article) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1",
                        title: ownerEmail(article)
                      }, toDisplayString(ownerEmail(article)), 9, ["title"])) : createCommentVNode("", true)
                    ]),
                    article.show_from_at ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"
                    }, toDisplayString(unref(t)("show")) + ": " + toDisplayString(article.show_from_at) + " / " + toDisplayString(article.show_to_at), 1)) : (openBlock(), createBlock("div", {
                      key: 1,
                      class: "flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"
                    }, toDisplayString(formatDate(article.published_at)), 1)),
                    createVNode("a", {
                      href: `/blog/articles/${encodeURIComponent(article.url)}`,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-xs font-semibold text-sky-700 dark:text-sky-200 hover:underline hover:text-amber-700 dark:hover:text-amber-200 line-clamp-2 text-center"
                    }, toDisplayString(truncateText(articleTitle(article), 90)), 9, ["href"]),
                    createVNode("div", { class: "flex items-center justify-center gap-3 text-[11px] font-semibold text-slate-600 dark:text-slate-200" }, [
                      (article.views ?? 0) > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "flex items-center justify-center space-x-1",
                        title: unref(t)("views")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current shrink-0",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", {
                            class: "fill-current text-blue-600 dark:text-blue-300",
                            d: "M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                          })
                        ])),
                        createVNode("span", { class: "text-[12px] text-slate-700 dark:text-slate-200" }, toDisplayString(article.views), 1)
                      ], 8, ["title"])) : createCommentVNode("", true),
                      (article.likes_count ?? 0) > 0 ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "flex items-center justify-center space-x-1",
                        title: unref(t)("likes")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-7 w-7 fill-current",
                          viewBox: "0 0 32 32"
                        }, [
                          createVNode("path", {
                            class: "fill-current text-red-600 dark:text-red-300",
                            d: "M22.682 11.318A4.485 4.485 0 0019.5 10a4.377 4.377 0 00-3.5 1.707A4.383 4.383 0 0012.5 10a4.5 4.5 0 00-3.182 7.682L16 24l6.682-6.318a4.5 4.5 0 000-6.364zm-1.4 4.933L16 21.247l-5.285-5A2.5 2.5 0 0112.5 12c1.437 0 2.312.681 3.5 2.625C17.187 12.681 18.062 12 19.5 12a2.5 2.5 0 011.785 4.251h-.003z"
                          })
                        ])),
                        createVNode("span", { class: "text-[12px] text-slate-700 dark:text-slate-200" }, toDisplayString(article.likes_count), 1)
                      ], 8, ["title"])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "font-semibold text-[12px] text-center text-teal-700 dark:text-teal-200" }, toDisplayString(truncateText(articleShort(article), 120)), 1),
                    createVNode("div", { class: "flex flex-col justify-center" }, [
                      createVNode("div", { class: "text-center text-[11px] font-medium text-slate-600 dark:text-slate-300 mb-0.5" }, toDisplayString(unref(t)("rubrics")) + ": ", 1),
                      article.rubrics && article.rubrics.length ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "flex justify-center flex-wrap gap-1"
                      }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(article.rubrics, (rubric) => {
                          return openBlock(), createBlock("span", {
                            key: rubric.id,
                            title: rubricTitle(rubric),
                            class: "py-0.5 px-1.5 badge bg-indigo-500 dark:bg-indigo-200 rounded-sm text-[10px] text-slate-100 dark:text-slate-900"
                          }, toDisplayString(rubric.id), 9, ["title"]);
                        }), 128))
                      ])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(article.moderation_status).class],
                        title: article.moderation_note && article.moderated_at ? `${article.moderation_note} [${formatDate(article.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(article.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$8, {
                        isAdmin: __props.isAdmin,
                        status: (article == null ? void 0 : article.moderation_status) ?? 0,
                        initialNote: (article == null ? void 0 : article.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => _ctx.$emit("approve", article, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-between px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: article.left,
                        onToggleLeft: ($event) => _ctx.$emit("toggle-left", article),
                        title: article.left ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleLeft", "title"]),
                      createVNode(_sfc_main$6, {
                        isActive: article.main,
                        onToggleMain: ($event) => _ctx.$emit("toggle-main", article),
                        title: article.main ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleMain", "title"]),
                      createVNode(_sfc_main$7, {
                        isActive: article.right,
                        onToggleRight: ($event) => _ctx.$emit("toggle-right", article),
                        title: article.right ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleRight", "title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$9, {
                        isActive: article.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", article),
                        title: article.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.blogArticles.edit", { blogArticle: article.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => _ctx.$emit("delete", article)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogArticle/View/ArticleCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminBlogArticlesProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminBlogArticlesPerPage: { type: Number, default: 6 },
    adminBlogArticlesDefaultSort: { type: String, default: "idDesc" },
    articles: { type: [Array, Object], default: () => [] },
    articlesCount: { type: Number, default: 0 },
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
    const getArticleTranslation = (article) => (article == null ? void 0 : article.translation) || {};
    const getArticleTitle = (article) => {
      var _a;
      return ((_a = getArticleTranslation(article)) == null ? void 0 : _a.title) || `ID: ${article == null ? void 0 : article.id}`;
    };
    const getArticleShort = (article) => {
      var _a;
      return ((_a = getArticleTranslation(article)) == null ? void 0 : _a.short) || "";
    };
    const getArticleDescription = (article) => {
      var _a;
      return ((_a = getArticleTranslation(article)) == null ? void 0 : _a.description) || "";
    };
    const normalize = (value) => (value ?? "").toString().trim().toLowerCase();
    const moderationNum = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const viewMode = ref(localStorage.getItem("admin_view_mode_articles") || "cards");
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_articles", value);
    });
    const itemsPerPage = ref(props.adminBlogArticlesPerPage || 6);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountBlogArticles"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminBlogArticlesDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      router.put(
        route("admin.settings.updateAdminSortBlogArticles"),
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
    const localArticles = ref([]);
    const articlesList = computed(() => {
      var _a;
      if (Array.isArray(props.articles)) {
        return props.articles;
      }
      if (Array.isArray((_a = props.articles) == null ? void 0 : _a.data)) {
        return props.articles.data;
      }
      return [];
    });
    watch(
      articlesList,
      (newVal) => {
        localArticles.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const showConfirmDeleteModal = ref(false);
    const articleToDeleteId = ref(null);
    const articleToDeleteTitle = ref("");
    const confirmDelete = (articleOrId, title = null) => {
      if (typeof articleOrId === "object") {
        articleToDeleteId.value = articleOrId.id;
        articleToDeleteTitle.value = title || getArticleTitle(articleOrId);
      } else {
        articleToDeleteId.value = articleOrId;
        articleToDeleteTitle.value = title || `ID: ${articleOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      articleToDeleteId.value = null;
      articleToDeleteTitle.value = "";
    };
    const deleteArticle = () => {
      if (articleToDeleteId.value === null) return;
      const idToDelete = articleToDeleteId.value;
      const titleToDelete = articleToDeleteTitle.value;
      router.delete(route("admin.blogArticles.destroy", { blogArticle: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          toast.success(`Статья "${titleToDelete || "ID: " + idToDelete}" удалена.`);
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors)[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Статья: ${titleToDelete || "ID: " + idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    const patchLocalArticle = (articleId, callback) => {
      const index = localArticles.value.findIndex((article) => article.id === articleId);
      if (index !== -1) {
        callback(localArticles.value[index]);
      }
    };
    const toggleActivity = (article) => {
      const newActivity = !article.activity;
      const title = getArticleTitle(article);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route("admin.actions.blogArticles.updateActivity", { blogArticle: article.id }),
        { activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalArticle(article.id, (node) => {
              node.activity = newActivity;
            });
            toast.success(`Статья "${title}" ${actionText}.`);
          },
          onError: (errors) => {
            toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`);
          }
        }
      );
    };
    const toggleLeft = (article) => {
      const newLeft = !article.left;
      const title = getArticleTitle(article);
      router.put(
        route("admin.actions.blogArticles.updateLeft", { blogArticle: article.id }),
        { left: newLeft },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalArticle(article.id, (node) => {
              node.left = newLeft;
            });
            toast.success(`Позиция left для статьи "${title}" обновлена.`);
          },
          onError: (errors) => {
            toast.error(errors.left || errors.general || `Ошибка изменения left для "${title}".`);
          }
        }
      );
    };
    const toggleMain = (article) => {
      const newMain = !article.main;
      const title = getArticleTitle(article);
      router.put(
        route("admin.actions.blogArticles.updateMain", { blogArticle: article.id }),
        { main: newMain },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalArticle(article.id, (node) => {
              node.main = newMain;
            });
            toast.success(`Позиция main для статьи "${title}" обновлена.`);
          },
          onError: (errors) => {
            toast.error(errors.main || errors.general || `Ошибка изменения main для "${title}".`);
          }
        }
      );
    };
    const toggleRight = (article) => {
      const newRight = !article.right;
      const title = getArticleTitle(article);
      router.put(
        route("admin.actions.blogArticles.updateRight", { blogArticle: article.id }),
        { right: newRight },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalArticle(article.id, (node) => {
              node.right = newRight;
            });
            toast.success(`Позиция right для статьи "${title}" обновлена.`);
          },
          onError: (errors) => {
            toast.error(errors.right || errors.general || `Ошибка изменения right для "${title}".`);
          }
        }
      );
    };
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const sortArticles = (articles) => {
      const list = (articles || []).slice();
      if (sortParam.value === "ownerNameAsc") return list.sort((a, b) => {
        var _a, _b;
        return normalize((_a = a == null ? void 0 : a.owner) == null ? void 0 : _a.name).localeCompare(normalize((_b = b == null ? void 0 : b.owner) == null ? void 0 : _b.name), locale.value);
      });
      if (sortParam.value === "ownerNameDesc") return list.sort((a, b) => {
        var _a, _b;
        return normalize((_a = b == null ? void 0 : b.owner) == null ? void 0 : _a.name).localeCompare(normalize((_b = a == null ? void 0 : a.owner) == null ? void 0 : _b.name), locale.value);
      });
      if (sortParam.value === "ownerEmailAsc") return list.sort((a, b) => {
        var _a, _b;
        return normalize((_a = a == null ? void 0 : a.owner) == null ? void 0 : _a.email).localeCompare(normalize((_b = b == null ? void 0 : b.owner) == null ? void 0 : _b.email), locale.value);
      });
      if (sortParam.value === "ownerEmailDesc") return list.sort((a, b) => {
        var _a, _b;
        return normalize((_a = b == null ? void 0 : b.owner) == null ? void 0 : _a.email).localeCompare(normalize((_b = a == null ? void 0 : a.owner) == null ? void 0 : _b.email), locale.value);
      });
      if (sortParam.value === "idAsc") return list.sort((a, b) => (a.id ?? 0) - (b.id ?? 0));
      if (sortParam.value === "idDesc") return list.sort((a, b) => (b.id ?? 0) - (a.id ?? 0));
      if (sortParam.value === "sortAsc") return list.sort((a, b) => (a.sort ?? 0) - (b.sort ?? 0));
      if (sortParam.value === "sortDesc") return list.sort((a, b) => (b.sort ?? 0) - (a.sort ?? 0));
      if (sortParam.value === "titleAsc") return list.sort((a, b) => normalize(getArticleTitle(a)).localeCompare(normalize(getArticleTitle(b)), locale.value));
      if (sortParam.value === "titleDesc") return list.sort((a, b) => normalize(getArticleTitle(b)).localeCompare(normalize(getArticleTitle(a)), locale.value));
      if (sortParam.value === "activity") return list.filter((article) => !!article.activity);
      if (sortParam.value === "inactive") return list.filter((article) => !article.activity);
      if (sortParam.value === "left") return list.filter((article) => !!article.left);
      if (sortParam.value === "noLeft") return list.filter((article) => !article.left);
      if (sortParam.value === "main") return list.filter((article) => !!article.main);
      if (sortParam.value === "noMain") return list.filter((article) => !article.main);
      if (sortParam.value === "right") return list.filter((article) => !!article.right);
      if (sortParam.value === "noRight") return list.filter((article) => !article.right);
      if (sortParam.value === "publishedAtDesc") return list.sort((a, b) => new Date(b.published_at || 0) - new Date(a.published_at || 0));
      if (sortParam.value === "publishedAtAsc") return list.sort((a, b) => new Date(a.published_at || 0) - new Date(b.published_at || 0));
      if (sortParam.value === "views" || sortParam.value === "viewsDesc") return list.sort((a, b) => (b.views ?? 0) - (a.views ?? 0));
      if (sortParam.value === "viewsAsc") return list.sort((a, b) => (a.views ?? 0) - (b.views ?? 0));
      if (sortParam.value === "likes" || sortParam.value === "likesDesc") return list.sort((a, b) => (b.likes_count ?? 0) - (a.likes_count ?? 0));
      if (sortParam.value === "likesAsc") return list.sort((a, b) => (a.likes_count ?? 0) - (b.likes_count ?? 0));
      if (sortParam.value === "commentsDesc") return list.sort((a, b) => (b.comments_count ?? 0) - (a.comments_count ?? 0));
      if (sortParam.value === "commentsAsc") return list.sort((a, b) => (a.comments_count ?? 0) - (b.comments_count ?? 0));
      if (sortParam.value === "moderationPending") return list.filter((article) => moderationNum(article == null ? void 0 : article.moderation_status) === 0);
      if (sortParam.value === "moderationApproved") return list.filter((article) => moderationNum(article == null ? void 0 : article.moderation_status) === 1);
      if (sortParam.value === "moderationRejected") return list.filter((article) => moderationNum(article == null ? void 0 : article.moderation_status) === 2);
      if (sortParam.value === "moderationStatusAsc") return list.sort((a, b) => moderationNum(a == null ? void 0 : a.moderation_status) - moderationNum(b == null ? void 0 : b.moderation_status));
      if (sortParam.value === "moderationStatusDesc") return list.sort((a, b) => moderationNum(b == null ? void 0 : b.moderation_status) - moderationNum(a == null ? void 0 : a.moderation_status));
      return list;
    };
    const filteredArticles = computed(() => {
      let filtered = localArticles.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortArticles(filtered);
      }
      filtered = filtered.filter((article) => {
        var _a, _b;
        const title = normalize(getArticleTitle(article));
        const short = normalize(getArticleShort(article));
        const description = normalize(getArticleDescription(article));
        const url = normalize(article == null ? void 0 : article.url);
        const ownerName = normalize((_a = article == null ? void 0 : article.owner) == null ? void 0 : _a.name);
        const ownerEmail = normalize((_b = article == null ? void 0 : article.owner) == null ? void 0 : _b.email);
        return title.includes(query) || short.includes(query) || description.includes(query) || url.includes(query) || ownerName.includes(query) || ownerEmail.includes(query);
      });
      return sortArticles(filtered);
    });
    const paginatedArticles = computed(() => {
      const perPage = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * perPage;
      return filteredArticles.value.slice(start, start + perPage);
    });
    const displayedArticles = computed(() => {
      return props.useServerProcessing ? articlesList.value : paginatedArticles.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const selectedArticles = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedArticles.value.map((article) => article.id);
      if (checked) {
        selectedArticles.value = [.../* @__PURE__ */ new Set([...selectedArticles.value, ...ids])];
      } else {
        selectedArticles.value = selectedArticles.value.filter((id) => !ids.includes(id));
      }
    };
    const toggleSelectArticle = (articleId) => {
      const index = selectedArticles.value.indexOf(articleId);
      if (index > -1) {
        selectedArticles.value.splice(index, 1);
      } else {
        selectedArticles.value.push(articleId);
      }
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedArticles.value.length) {
        toast.warning("Выберите статьи для активации/деактивации");
        return;
      }
      const idsToUpdate = [...selectedArticles.value];
      router.put(
        route("admin.actions.blogArticles.bulkUpdateActivity"),
        { ids: idsToUpdate, activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localArticles.value = localArticles.value.map((article) => {
              if (idsToUpdate.includes(article.id)) {
                return { ...article, activity: newActivity };
              }
              return article;
            });
            selectedArticles.value = [];
            toast.success("Активность статей массово обновлена");
          },
          onError: (errors) => {
            const msg = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности";
            toast.error(msg);
          }
        }
      );
    };
    const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
      if (!selectedArticles.value.length) {
        toast.warning("Выберите статьи для массового действия");
        return;
      }
      const idsToUpdate = [...selectedArticles.value];
      router.put(
        route(routeName),
        { ids: idsToUpdate, [field]: newValue },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localArticles.value = localArticles.value.map((article) => {
              if (idsToUpdate.includes(article.id)) {
                return { ...article, [field]: newValue };
              }
              return article;
            });
            selectedArticles.value = [];
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
      if (!selectedArticles.value.length) {
        toast.warning("Выберите хотя бы одну статью для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные статьи?")) return;
      router.delete(route("admin.actions.blogArticles.bulkDestroy"), {
        data: { ids: selectedArticles.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          selectedArticles.value = [];
          toast.success("Массовое удаление статей успешно завершено.");
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors)[0];
          toast.error(errors[errorKey] || "Произошла ошибка при удалении статей.");
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
        bulkToggleFlag("left", true, "admin.actions.blogArticles.bulkUpdateLeft", "Статьи добавлены в левую колонку");
      } else if (action === "noLeft") {
        bulkToggleFlag("left", false, "admin.actions.blogArticles.bulkUpdateLeft", "Статьи убраны из левой колонки");
      } else if (action === "main") {
        bulkToggleFlag("main", true, "admin.actions.blogArticles.bulkUpdateMain", "Статьи добавлены в главный блок");
      } else if (action === "noMain") {
        bulkToggleFlag("main", false, "admin.actions.blogArticles.bulkUpdateMain", "Статьи убраны из главного блока");
      } else if (action === "right") {
        bulkToggleFlag("right", true, "admin.actions.blogArticles.bulkUpdateRight", "Статьи добавлены в правую колонку");
      } else if (action === "noRight") {
        bulkToggleFlag("right", false, "admin.actions.blogArticles.bulkUpdateRight", "Статьи убраны из правой колонке");
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const approveArticle = (article, status = 1, note = "") => {
      if (!(article == null ? void 0 : article.id)) return;
      router.put(
        route("admin.actions.blogArticles.approve", { blogArticle: article.id }),
        { moderation_status: status, moderation_note: note },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalArticle(article.id, (node) => {
              node.moderation_status = status;
              node.is_approved = status === 1;
              node.moderation_note = note;
            });
            toast.success(status === 1 ? "Статья одобрена" : "Статья отклонена");
          },
          onError: () => toast.error("Ошибка модерации статьи")
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
        route("admin.actions.blogArticles.updateSortBulk"),
        { items },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.success("Сортировка статей обновлена"),
          onError: (errors) => {
            console.error("Ошибка сортировки статей:", errors);
            toast.error(errors.message || "Ошибка обновления сортировки");
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("articles")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("articles"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("articles")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("articles")), 1)
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
              href: _ctx.route("admin.blogArticles.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addArticle"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addArticle")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              "setting-key": "adminBlogArticlesProcessingMode",
              mode: __props.adminBlogArticlesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.articlesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.articlesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$e, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.articlesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$f, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.articlesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountBlogArticles"
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
            if (__props.articlesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$i, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.articlesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.articlesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              if (__props.articlesCount) {
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
            if (__props.articlesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredArticles.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.articles }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                articles: displayedArticles.value,
                "selected-articles": selectedArticles.value,
                "is-admin": isAdmin.value,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectArticle,
                onToggleAll: toggleAll,
                onApprove: approveArticle
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                articles: displayedArticles.value,
                "selected-articles": selectedArticles.value,
                "is-admin": isAdmin.value,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectArticle,
                onToggleAll: toggleAll,
                onApprove: approveArticle
              }, null, _parent2, _scopeId));
            }
            if (__props.articlesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredArticles.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.articles }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$m, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteArticle,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$c, {
                      href: _ctx.route("admin.blogArticles.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addArticle")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$d, {
                      "setting-key": "adminBlogArticlesProcessingMode",
                      mode: __props.adminBlogArticlesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.articlesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.articlesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.articlesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$f, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.articlesCount ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountBlogArticles"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.articlesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$i, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.articlesCount), 1)
                      ]),
                      _: 1
                    }),
                    __props.articlesCount ? (openBlock(), createBlock(_sfc_main$4, {
                      key: 0,
                      onChange: handleBulkAction
                    })) : createCommentVNode("", true),
                    createVNode(_sfc_main$j, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.articlesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredArticles.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.articles
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    articles: displayedArticles.value,
                    "selected-articles": selectedArticles.value,
                    "is-admin": isAdmin.value,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectArticle,
                    onToggleAll: toggleAll,
                    onApprove: approveArticle
                  }, null, 8, ["articles", "selected-articles", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    articles: displayedArticles.value,
                    "selected-articles": selectedArticles.value,
                    "is-admin": isAdmin.value,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectArticle,
                    onToggleAll: toggleAll,
                    onApprove: approveArticle
                  }, null, 8, ["articles", "selected-articles", "is-admin"])),
                  __props.articlesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredArticles.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.articles
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$m, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteArticle,
                cancelText: unref(t)("cancel"),
                confirmText: unref(t)("yesDelete"),
                onClose: closeModal
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Blog/BlogArticles/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
