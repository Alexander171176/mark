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
import { _ as _sfc_main$5, a as _sfc_main$6, b as _sfc_main$7 } from "./RightToggle-r8SYzaEU.js";
import { _ as _sfc_main$9 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$b } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$a } from "./IconEdit-Bw90OQvk.js";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogBanner/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>─────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} (0 → 9)</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} (9 → 0)</option><option disabled>─────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} (A → Z)</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} (Z → A)</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} (A → Z)</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} (Z → A)</option><option disabled>─────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} (A → Z)</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} (Z → A)</option><option disabled>─────────────────</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} (9 → 0)</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} (0 → 9)</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="leftDesc">${ssrInterpolate(unref(t)("inLeft"))} ON→OFF</option><option value="leftAsc">${ssrInterpolate(unref(t)("inLeft"))} OFF→ON</option><option value="left">${ssrInterpolate(unref(t)("inLeft"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("notLeft"))}</option><option disabled>─────────────────</option><option value="mainDesc">${ssrInterpolate(unref(t)("inMain"))} ON→OFF</option><option value="mainAsc">${ssrInterpolate(unref(t)("inMain"))} OFF→ON</option><option value="main">${ssrInterpolate(unref(t)("inMain"))}</option><option value="noMain">${ssrInterpolate(unref(t)("notMain"))}</option><option disabled>─────────────────</option><option value="rightDesc">${ssrInterpolate(unref(t)("inRight"))} ON→OFF</option><option value="rightAsc">${ssrInterpolate(unref(t)("inRight"))} OFF→ON</option><option value="right">${ssrInterpolate(unref(t)("inRight"))}</option><option value="noRight">${ssrInterpolate(unref(t)("notRight"))}</option><option disabled>─────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogBanner/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "BannerTable",
  __ssrInlineRender: true,
  props: {
    banners: { type: Array, default: () => [] },
    selectedBanners: { type: Array, default: () => [] },
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
    const localBanners = ref([]);
    watch(
      () => props.banners,
      (newVal) => {
        localBanners.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits("update-sort-order", localBanners.value.map((banner) => banner.id));
    };
    const allSelected = () => {
      return localBanners.value.length && localBanners.value.every((banner) => props.selectedBanners.includes(banner.id));
    };
    const bannerTranslation = (banner) => (banner == null ? void 0 : banner.translation) || {};
    const bannerTitle = (banner) => {
      var _a;
      return ((_a = bannerTranslation(banner)) == null ? void 0 : _a.title) || `ID: ${banner == null ? void 0 : banner.id}`;
    };
    const bannerLink = (banner) => {
      var _a;
      return ((_a = bannerTranslation(banner)) == null ? void 0 : _a.link) || "";
    };
    const bannerLocale = (banner) => {
      var _a;
      return ((_a = bannerTranslation(banner)) == null ? void 0 : _a.locale) || "";
    };
    const getPrimaryImage = (banner) => {
      if (banner.images && banner.images.length) {
        return [...banner.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0];
      }
      return null;
    };
    const imageUrl = (banner) => {
      const image = getPrimaryImage(banner);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || (banner == null ? void 0 : banner.cover_webp_url) || (banner == null ? void 0 : banner.cover_thumb_url) || (banner == null ? void 0 : banner.cover_image_url) || "/storage/blog/blog_banner_images/default-image.png";
    };
    const imageAlt = (banner) => {
      var _a;
      return ((_a = getPrimaryImage(banner)) == null ? void 0 : _a.alt) || t("defaultImageAlt");
    };
    const imageTitle = (banner) => {
      var _a;
      return ((_a = getPrimaryImage(banner)) == null ? void 0 : _a.caption) || t("postImage");
    };
    const ownerTitle = (banner) => {
      const owner = banner == null ? void 0 : banner.owner;
      if (!owner) return t("noData");
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (banner) => {
      var _a;
      return ((_a = banner == null ? void 0 : banner.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedBanners.length)}</div>`);
      if (localBanners.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localBanners.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-1 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("owner"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24"><path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path><path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path><path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path><path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path><path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"></path></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("localization"))}><svg class="w-8 h-8 fill-current shrink-0" viewBox="0 0 640 512"><path d="M0 128C0 92.7 28.7 64 64 64l192 0 48 0 16 0 256 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64l-256 0-16 0-48 0L64 448c-35.3 0-64-28.7-64-64L0 128zm320 0l0 256 256 0 0-256-256 0zM178.3 175.9c-3.2-7.2-10.4-11.9-18.3-11.9s-15.1 4.7-18.3 11.9l-64 144c-4.5 10.1 .1 21.9 10.2 26.4s21.9-.1 26.4-10.2l8.9-20.1 73.6 0 8.9 20.1c4.5 10.1 16.3 14.6 26.4 10.2s14.6-16.3 10.2-26.4l-64-144zM160 233.2L179 276l-38 0 19-42.8zM448 164c11 0 20 9 20 20l0 4 44 0 16 0c11 0 20 9 20 20s-9 20-20 20l-2 0-1.6 4.5c-8.9 24.4-22.4 46.6-39.6 65.4c.9 .6 1.8 1.1 2.7 1.6l18.9 11.3c9.5 5.7 12.5 18 6.9 27.4s-18 12.5-27.4 6.9l-18.9-11.3c-4.5-2.7-8.8-5.5-13.1-8.5c-10.6 7.5-21.9 14-34 19.4l-3.6 1.6c-10.1 4.5-21.9-.1-26.4-10.2s.1-21.9 10.2-26.4l3.6-1.6c6.4-2.9 12.6-6.1 18.5-9.8l-12.2-12.2c-7.8-7.8-7.8-20.5 0-28.3s20.5-7.8 28.3 0l14.6 14.6 .5 .5c12.4-13.1 22.5-28.3 29.8-45L448 228l-72 0c-11 0-20-9-20-20s9-20 20-20l52 0 0-4c0-11 9-20 20-20z"></path></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("title"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("url"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("show"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-1 py-3 first:pl-12 last:pr-12 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-1 py-1 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localBanners.value,
          "onUpdate:modelValue": ($event) => localBanners.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: banner }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-1 py-1 text-center cursor-move handle w-px"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-1 py-1 whitespace-nowrap w-px"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${ssrRenderAttr("title", `[${banner.sort}]`)}${_scopeId}>${ssrInterpolate(banner.id)}</div></td><td class="px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(banner))}${ssrRenderAttr("title", ownerTitle(banner))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("author"))}${_scopeId}></div></td><td class="px-1 py-1 whitespace-nowrap w-px"${_scopeId}><div class="${ssrRenderClass([banner.activity ? "bg-blue-500 text-white" : "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100", "text-xs px-1.5 py-0.5 rounded-sm border border-gray-400 uppercase"])}"${ssrRenderAttr("title", unref(t)("localization"))}${_scopeId}>${ssrInterpolate(bannerLocale(banner).toUpperCase())}</div></td><td class="px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", imageUrl(banner))}${ssrRenderAttr("alt", imageAlt(banner))}${ssrRenderAttr("title", imageTitle(banner))} class="h-6 w-9 object-cover rounded-sm"${_scopeId}></div></td><td class="px-1 py-1"${_scopeId}><div class="text-left text-sky-700 dark:text-sky-200"${_scopeId}>${ssrInterpolate(bannerTitle(banner))}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-left"${_scopeId}>`);
              if (bannerLink(banner)) {
                _push2(`<a${ssrRenderAttr("href", bannerLink(banner))} class="text-violet-600 dark:text-violet-200 hover:underline hover:text-violet-800 dark:hover:text-violet-50" target="_blank" rel="noopener noreferrer"${_scopeId}>${ssrInterpolate(bannerLink(banner))}</a>`);
              } else {
                _push2(`<span class="text-slate-400"${_scopeId}> — </span>`);
              }
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: banner.left,
                onToggleLeft: ($event) => _ctx.$emit("toggle-left", banner),
                title: banner.left ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: banner.main,
                onToggleMain: ($event) => _ctx.$emit("toggle-main", banner),
                title: banner.main ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: banner.right,
                onToggleRight: ($event) => _ctx.$emit("toggle-right", banner),
                title: banner.right ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(banner.moderation_status).class, "text-[10px] px-1 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", banner.moderation_note && banner.moderated_at ? `${banner.moderation_note} [${formatDate(banner.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(banner.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isAdmin: __props.isAdmin,
                status: (banner == null ? void 0 : banner.moderation_status) ?? 0,
                initialNote: (banner == null ? void 0 : banner.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => _ctx.$emit("approve", banner, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: banner.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", banner),
                title: banner.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.blogBanners.edit", { blogBanner: banner.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => _ctx.$emit("delete", banner)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedBanners.includes(banner.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                      title: `[${banner.sort}]`
                    }, toDisplayString(banner.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(banner),
                        title: ownerTitle(banner),
                        class: "h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("author")
                      }, null, 8, ["src", "title", "alt"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap w-px" }, [
                    createVNode("div", {
                      class: ["text-xs px-1.5 py-0.5 rounded-sm border border-gray-400 uppercase", banner.activity ? "bg-blue-500 text-white" : "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100"],
                      title: unref(t)("localization")
                    }, toDisplayString(bannerLocale(banner).toUpperCase()), 11, ["title"])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: imageUrl(banner),
                        alt: imageAlt(banner),
                        title: imageTitle(banner),
                        class: "h-6 w-9 object-cover rounded-sm"
                      }, null, 8, ["src", "alt", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "text-left text-sky-700 dark:text-sky-200" }, toDisplayString(bannerTitle(banner)), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-left" }, [
                      bannerLink(banner) ? (openBlock(), createBlock("a", {
                        key: 0,
                        href: bannerLink(banner),
                        class: "text-violet-600 dark:text-violet-200 hover:underline hover:text-violet-800 dark:hover:text-violet-50",
                        target: "_blank",
                        rel: "noopener noreferrer"
                      }, toDisplayString(bannerLink(banner)), 9, ["href"])) : (openBlock(), createBlock("span", {
                        key: 1,
                        class: "text-slate-400"
                      }, " — "))
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: banner.left,
                        onToggleLeft: ($event) => _ctx.$emit("toggle-left", banner),
                        title: banner.left ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleLeft", "title"]),
                      createVNode(_sfc_main$6, {
                        isActive: banner.main,
                        onToggleMain: ($event) => _ctx.$emit("toggle-main", banner),
                        title: banner.main ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleMain", "title"]),
                      createVNode(_sfc_main$7, {
                        isActive: banner.right,
                        onToggleRight: ($event) => _ctx.$emit("toggle-right", banner),
                        title: banner.right ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleRight", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-1 py-1 rounded-sm border font-semibold", moderationBadge(banner.moderation_status).class],
                        title: banner.moderation_note && banner.moderated_at ? `${banner.moderation_note} [${formatDate(banner.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(banner.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$8, {
                        isAdmin: __props.isAdmin,
                        status: (banner == null ? void 0 : banner.moderation_status) ?? 0,
                        initialNote: (banner == null ? void 0 : banner.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => _ctx.$emit("approve", banner, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-2" }, [
                      createVNode(_sfc_main$9, {
                        isActive: banner.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", banner),
                        title: banner.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.blogBanners.edit", { blogBanner: banner.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => _ctx.$emit("delete", banner)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedBanners.includes(banner.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", banner.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogBanner/Table/BannerTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "BannerCardGrid",
  __ssrInlineRender: true,
  props: {
    banners: { type: Array, default: () => [] },
    selectedBanners: { type: Array, default: () => [] },
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
    const localBanners = ref([]);
    watch(
      () => props.banners,
      (newVal) => {
        localBanners.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits("update-sort-order", localBanners.value.map((banner) => banner.id));
    };
    const allSelected = () => {
      return localBanners.value.length && localBanners.value.every((banner) => props.selectedBanners.includes(banner.id));
    };
    const bannerTranslation = (banner) => (banner == null ? void 0 : banner.translation) || {};
    const bannerTitle = (banner) => {
      var _a;
      return ((_a = bannerTranslation(banner)) == null ? void 0 : _a.title) || `ID: ${banner == null ? void 0 : banner.id}`;
    };
    const bannerShort = (banner) => {
      var _a;
      return ((_a = bannerTranslation(banner)) == null ? void 0 : _a.short) || "";
    };
    const bannerLink = (banner) => {
      var _a;
      return ((_a = bannerTranslation(banner)) == null ? void 0 : _a.link) || "";
    };
    const bannerLocale = (banner) => {
      var _a;
      return ((_a = bannerTranslation(banner)) == null ? void 0 : _a.locale) || "";
    };
    const truncateText = (text, maxLength = 90) => {
      if (!text) return "";
      return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + "…" : text;
    };
    const getPrimaryImage = (banner) => {
      if (banner.images && banner.images.length) {
        return [...banner.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0];
      }
      return null;
    };
    const imageUrl = (banner) => {
      const image = getPrimaryImage(banner);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || (banner == null ? void 0 : banner.cover_webp_url) || (banner == null ? void 0 : banner.cover_thumb_url) || (banner == null ? void 0 : banner.cover_image_url) || "/storage/blog/blog_banner_images/default-image.png";
    };
    const imageAlt = (banner) => {
      var _a;
      return ((_a = getPrimaryImage(banner)) == null ? void 0 : _a.alt) || t("defaultImageAlt");
    };
    const imageTitle = (banner) => {
      var _a;
      return ((_a = getPrimaryImage(banner)) == null ? void 0 : _a.caption) || t("postImage");
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
    const ownerName = (banner) => {
      var _a;
      return ((_a = banner == null ? void 0 : banner.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (banner) => {
      var _a;
      return ((_a = banner == null ? void 0 : banner.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (banner) => {
      const owner = banner == null ? void 0 : banner.owner;
      if (!owner) return t("noData");
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (banner) => {
      var _a;
      return ((_a = banner == null ? void 0 : banner.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedBanners.length)}</div>`);
      if (localBanners.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localBanners.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localBanners.value,
          "onUpdate:modelValue": ($event) => localBanners.value = $event,
          tag: "div",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          "item-key": "id",
          onEnd: handleDragEnd,
          handle: ".handle"
        }, {
          item: withCtx(({ element: banner }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-500 dark:text-slate-300 hover:text-slate-800 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" viewBox="0 0 20 20" fill="currentColor"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${banner.sort}]`)}${_scopeId}> ID: ${ssrInterpolate(banner.id)}</div><div class="${ssrRenderClass([banner.activity ? "bg-blue-500 text-white" : "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100", "text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 uppercase"])}"${ssrRenderAttr("title", unref(t)("localization"))}${_scopeId}>${ssrInterpolate(bannerLocale(banner).toUpperCase())}</div></div><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedBanners.includes(banner.id)) ? " checked" : ""}${_scopeId}></header><div class="relative w-full bg-slate-200 dark:bg-slate-900"${_scopeId}><img${ssrRenderAttr("src", imageUrl(banner))}${ssrRenderAttr("alt", imageAlt(banner))}${ssrRenderAttr("title", imageTitle(banner))} class="h-32 w-full object-cover"${_scopeId}></div><div class="flex flex-col flex-1 px-3 py-2 space-y-2"${_scopeId}><div class="text-center"${_scopeId}><div class="text-[14px] font-semibold text-sky-700 dark:text-sky-200 line-clamp-2"${ssrRenderAttr("title", bannerTitle(banner))}${_scopeId}>${ssrInterpolate(bannerTitle(banner))}</div></div>`);
              if (bannerShort(banner)) {
                _push2(`<div class="text-center text-xs font-semibold text-slate-600 dark:text-slate-300 line-clamp-2"${ssrRenderAttr("title", bannerShort(banner))}${_scopeId}>${ssrInterpolate(truncateText(bannerShort(banner)))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="text-center min-h-[1rem]"${_scopeId}>`);
              if (bannerLink(banner)) {
                _push2(`<a${ssrRenderAttr("href", bannerLink(banner))} target="_blank" rel="noopener noreferrer" class="font-semibold text-xs text-violet-600 dark:text-violet-200 hover:text-violet-800 dark:hover:text-violet-50 hover:underline break-all"${_scopeId}>${ssrInterpolate(bannerLink(banner))}</a>`);
              } else {
                _push2(`<span class="text-xs text-slate-400"${_scopeId}> — </span>`);
              }
              _push2(`</div><div class="flex flex-col items-center justify-center text-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(banner))}${ssrRenderAttr("title", ownerTitle(banner))} class="h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("author"))}${_scopeId}><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerName(banner))}${_scopeId}>${ssrInterpolate(ownerName(banner))}</div>`);
              if (ownerEmail(banner)) {
                _push2(`<div class="text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerEmail(banner))}${_scopeId}>${ssrInterpolate(ownerEmail(banner))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="flex justify-center space-x-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(banner.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", banner.moderation_note && banner.moderated_at ? `${banner.moderation_note} [${formatDate(banner.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(banner.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isAdmin: __props.isAdmin,
                status: (banner == null ? void 0 : banner.moderation_status) ?? 0,
                initialNote: (banner == null ? void 0 : banner.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => _ctx.$emit("approve", banner, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center justify-between space-x-2"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: banner.left,
                onToggleLeft: ($event) => _ctx.$emit("toggle-left", banner),
                title: banner.left ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: banner.main,
                onToggleMain: ($event) => _ctx.$emit("toggle-main", banner),
                title: banner.main ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: banner.right,
                onToggleRight: ($event) => _ctx.$emit("toggle-right", banner),
                title: banner.right ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: banner.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", banner),
                title: banner.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.blogBanners.edit", { blogBanner: banner.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => _ctx.$emit("delete", banner)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div></div>`);
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
                        title: `[${banner.sort}]`
                      }, " ID: " + toDisplayString(banner.id), 9, ["title"]),
                      createVNode("div", {
                        class: ["text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 uppercase", banner.activity ? "bg-blue-500 text-white" : "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100"],
                        title: unref(t)("localization")
                      }, toDisplayString(bannerLocale(banner).toUpperCase()), 11, ["title"])
                    ]),
                    createVNode("input", {
                      type: "checkbox",
                      checked: __props.selectedBanners.includes(banner.id),
                      onChange: ($event) => _ctx.$emit("toggle-select", banner.id)
                    }, null, 40, ["checked", "onChange"])
                  ]),
                  createVNode("div", { class: "relative w-full bg-slate-200 dark:bg-slate-900" }, [
                    createVNode("img", {
                      src: imageUrl(banner),
                      alt: imageAlt(banner),
                      title: imageTitle(banner),
                      class: "h-32 w-full object-cover"
                    }, null, 8, ["src", "alt", "title"])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-2" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("div", {
                        class: "text-[14px] font-semibold text-sky-700 dark:text-sky-200 line-clamp-2",
                        title: bannerTitle(banner)
                      }, toDisplayString(bannerTitle(banner)), 9, ["title"])
                    ]),
                    bannerShort(banner) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-center text-xs font-semibold text-slate-600 dark:text-slate-300 line-clamp-2",
                      title: bannerShort(banner)
                    }, toDisplayString(truncateText(bannerShort(banner))), 9, ["title"])) : createCommentVNode("", true),
                    createVNode("div", { class: "text-center min-h-[1rem]" }, [
                      bannerLink(banner) ? (openBlock(), createBlock("a", {
                        key: 0,
                        href: bannerLink(banner),
                        target: "_blank",
                        rel: "noopener noreferrer",
                        class: "font-semibold text-xs text-violet-600 dark:text-violet-200 hover:text-violet-800 dark:hover:text-violet-50 hover:underline break-all"
                      }, toDisplayString(bannerLink(banner)), 9, ["href"])) : (openBlock(), createBlock("span", {
                        key: 1,
                        class: "text-xs text-slate-400"
                      }, " — "))
                    ]),
                    createVNode("div", { class: "flex flex-col items-center justify-center text-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(banner),
                        title: ownerTitle(banner),
                        class: "h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("author")
                      }, null, 8, ["src", "title", "alt"]),
                      createVNode("div", {
                        class: "mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1",
                        title: ownerName(banner)
                      }, toDisplayString(ownerName(banner)), 9, ["title"]),
                      ownerEmail(banner) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1",
                        title: ownerEmail(banner)
                      }, toDisplayString(ownerEmail(banner)), 9, ["title"])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(banner.moderation_status).class],
                        title: banner.moderation_note && banner.moderated_at ? `${banner.moderation_note} [${formatDate(banner.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(banner.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$8, {
                        isAdmin: __props.isAdmin,
                        status: (banner == null ? void 0 : banner.moderation_status) ?? 0,
                        initialNote: (banner == null ? void 0 : banner.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => _ctx.$emit("approve", banner, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("div", { class: "px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center justify-between space-x-2" }, [
                      createVNode("div", { class: "flex items-center space-x-1" }, [
                        createVNode(_sfc_main$5, {
                          isActive: banner.left,
                          onToggleLeft: ($event) => _ctx.$emit("toggle-left", banner),
                          title: banner.left ? unref(t)("enabled") : unref(t)("disabled")
                        }, null, 8, ["isActive", "onToggleLeft", "title"]),
                        createVNode(_sfc_main$6, {
                          isActive: banner.main,
                          onToggleMain: ($event) => _ctx.$emit("toggle-main", banner),
                          title: banner.main ? unref(t)("enabled") : unref(t)("disabled")
                        }, null, 8, ["isActive", "onToggleMain", "title"]),
                        createVNode(_sfc_main$7, {
                          isActive: banner.right,
                          onToggleRight: ($event) => _ctx.$emit("toggle-right", banner),
                          title: banner.right ? unref(t)("enabled") : unref(t)("disabled")
                        }, null, 8, ["isActive", "onToggleRight", "title"])
                      ]),
                      createVNode("div", { class: "flex items-center space-x-1" }, [
                        createVNode(_sfc_main$9, {
                          isActive: banner.activity,
                          onToggleActivity: ($event) => _ctx.$emit("toggle-activity", banner),
                          title: banner.activity ? unref(t)("enabled") : unref(t)("disabled")
                        }, null, 8, ["isActive", "onToggleActivity", "title"]),
                        createVNode(_sfc_main$a, {
                          href: _ctx.route("admin.blogBanners.edit", { blogBanner: banner.id })
                        }, null, 8, ["href"]),
                        createVNode(_sfc_main$b, {
                          onDelete: ($event) => _ctx.$emit("delete", banner)
                        }, null, 8, ["onDelete"])
                      ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogBanner/View/BannerCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    banners: { type: [Array, Object], default: () => [] },
    bannersCount: { type: Number, default: 0 },
    adminBlogBannersProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminBlogBannersPerPage: { type: Number, default: 20 },
    adminBlogBannersDefaultSort: { type: String, default: "idDesc" },
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
    const getBannerTranslation = (banner) => (banner == null ? void 0 : banner.translation) || {};
    const getBannerTitle = (banner) => {
      var _a;
      return ((_a = getBannerTranslation(banner)) == null ? void 0 : _a.title) || `ID: ${banner == null ? void 0 : banner.id}`;
    };
    const getBannerShort = (banner) => {
      var _a;
      return ((_a = getBannerTranslation(banner)) == null ? void 0 : _a.short) || "";
    };
    const getBannerLink = (banner) => {
      var _a;
      return ((_a = getBannerTranslation(banner)) == null ? void 0 : _a.link) || "";
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
    const viewMode = ref(localStorage.getItem("admin_view_mode_banners") || "cards");
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_banners", value);
    });
    const itemsPerPage = ref(props.adminBlogBannersPerPage || 20);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountBlogBanners"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminBlogBannersDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortBlogBanners"),
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
    const bannersList = computed(() => {
      var _a;
      if (Array.isArray(props.banners)) return props.banners;
      if (Array.isArray((_a = props.banners) == null ? void 0 : _a.data)) return props.banners.data;
      return [];
    });
    const localBanners = ref([]);
    watch(
      bannersList,
      (newVal) => {
        localBanners.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const showConfirmDeleteModal = ref(false);
    const bannerToDeleteId = ref(null);
    const bannerToDeleteTitle = ref("");
    const confirmDelete = (bannerOrId, title = null) => {
      if (typeof bannerOrId === "object") {
        bannerToDeleteId.value = bannerOrId.id;
        bannerToDeleteTitle.value = title || getBannerTitle(bannerOrId);
      } else {
        bannerToDeleteId.value = bannerOrId;
        bannerToDeleteTitle.value = title || `ID: ${bannerOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      bannerToDeleteId.value = null;
      bannerToDeleteTitle.value = "";
    };
    const deleteBanner = () => {
      if (bannerToDeleteId.value === null) return;
      const idToDelete = bannerToDeleteId.value;
      const titleToDelete = bannerToDeleteTitle.value;
      router.delete(route("admin.blogBanners.destroy", { blogBanner: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          toast.success(`Баннер "${titleToDelete || "ID: " + idToDelete}" удалён.`);
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Баннер: ${titleToDelete || "ID: " + idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    const patchLocalBanner = (bannerId, callback) => {
      const index = localBanners.value.findIndex((banner) => banner.id === bannerId);
      if (index !== -1) {
        callback(localBanners.value[index]);
      }
    };
    const toggleActivity = (banner) => {
      const newActivity = !banner.activity;
      const title = getBannerTitle(banner);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route("admin.actions.blogBanners.updateActivity", { blogBanner: banner.id }),
        { activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBanner(banner.id, (node) => {
              node.activity = newActivity;
            });
            toast.success(`Баннер "${title}" ${actionText}.`);
          },
          onError: (errors) => {
            toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`);
          }
        }
      );
    };
    const toggleLeft = (banner) => {
      const newLeft = !banner.left;
      const title = getBannerTitle(banner);
      router.put(
        route("admin.actions.blogBanners.updateLeft", { blogBanner: banner.id }),
        { left: newLeft },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBanner(banner.id, (node) => {
              node.left = newLeft;
            });
            toast.success(`Позиция left для баннера "${title}" обновлена.`);
          },
          onError: (errors) => {
            toast.error(errors.left || errors.general || `Ошибка изменения left для "${title}".`);
          }
        }
      );
    };
    const toggleMain = (banner) => {
      const newMain = !banner.main;
      const title = getBannerTitle(banner);
      router.put(
        route("admin.actions.blogBanners.updateMain", { blogBanner: banner.id }),
        { main: newMain },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBanner(banner.id, (node) => {
              node.main = newMain;
            });
            toast.success(`Позиция main для баннера "${title}" обновлена.`);
          },
          onError: (errors) => {
            toast.error(errors.main || errors.general || `Ошибка изменения main для "${title}".`);
          }
        }
      );
    };
    const toggleRight = (banner) => {
      const newRight = !banner.right;
      const title = getBannerTitle(banner);
      router.put(
        route("admin.actions.blogBanners.updateRight", { blogBanner: banner.id }),
        { right: newRight },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBanner(banner.id, (node) => {
              node.right = newRight;
            });
            toast.success(`Позиция right для баннера "${title}" обновлена.`);
          },
          onError: (errors) => {
            toast.error(errors.right || errors.general || `Ошибка изменения right для "${title}".`);
          }
        }
      );
    };
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const sortBanners = (banners) => {
      const list = (banners || []).slice();
      if (sortParam.value === "activity") return list.filter((banner) => !!banner.activity);
      if (sortParam.value === "inactive") return list.filter((banner) => !banner.activity);
      if (sortParam.value === "left") return list.filter((banner) => !!banner.left);
      if (sortParam.value === "noLeft") return list.filter((banner) => !banner.left);
      if (sortParam.value === "main") return list.filter((banner) => !!banner.main);
      if (sortParam.value === "noMain") return list.filter((banner) => !banner.main);
      if (sortParam.value === "right") return list.filter((banner) => !!banner.right);
      if (sortParam.value === "noRight") return list.filter((banner) => !banner.right);
      if (sortParam.value === "moderationPending") return list.filter((banner) => moderationNum(banner == null ? void 0 : banner.moderation_status) === 0);
      if (sortParam.value === "moderationApproved") return list.filter((banner) => moderationNum(banner == null ? void 0 : banner.moderation_status) === 1);
      if (sortParam.value === "moderationRejected") return list.filter((banner) => moderationNum(banner == null ? void 0 : banner.moderation_status) === 2);
      const sortMap = {
        idAsc: (a, b) => (a.id ?? 0) - (b.id ?? 0),
        idDesc: (a, b) => (b.id ?? 0) - (a.id ?? 0),
        sortAsc: (a, b) => (a.sort ?? 0) - (b.sort ?? 0),
        sortDesc: (a, b) => (b.sort ?? 0) - (a.sort ?? 0),
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
        },
        titleAsc: (a, b) => normalize(getBannerTitle(a)).localeCompare(normalize(getBannerTitle(b)), locale.value),
        titleDesc: (a, b) => normalize(getBannerTitle(b)).localeCompare(normalize(getBannerTitle(a)), locale.value),
        imagesAsc: (a, b) => (a.images_count ?? 0) - (b.images_count ?? 0),
        imagesDesc: (a, b) => (b.images_count ?? 0) - (a.images_count ?? 0),
        activityAsc: (a, b) => Number(a.activity) - Number(b.activity),
        activityDesc: (a, b) => Number(b.activity) - Number(a.activity),
        leftAsc: (a, b) => Number(a.left) - Number(b.left),
        leftDesc: (a, b) => Number(b.left) - Number(a.left),
        mainAsc: (a, b) => Number(a.main) - Number(b.main),
        mainDesc: (a, b) => Number(b.main) - Number(a.main),
        rightAsc: (a, b) => Number(a.right) - Number(b.right),
        rightDesc: (a, b) => Number(b.right) - Number(a.right),
        createdAtAsc: (a, b) => safeDate(a.created_at) - safeDate(b.created_at),
        createdAtDesc: (a, b) => safeDate(b.created_at) - safeDate(a.created_at),
        updatedAtAsc: (a, b) => safeDate(a.updated_at) - safeDate(b.updated_at),
        updatedAtDesc: (a, b) => safeDate(b.updated_at) - safeDate(a.updated_at),
        moderationStatusAsc: (a, b) => moderationNum(a == null ? void 0 : a.moderation_status) - moderationNum(b == null ? void 0 : b.moderation_status),
        moderationStatusDesc: (a, b) => moderationNum(b == null ? void 0 : b.moderation_status) - moderationNum(a == null ? void 0 : a.moderation_status)
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredBanners = computed(() => {
      let filtered = localBanners.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortBanners(filtered);
      }
      filtered = filtered.filter((banner) => {
        var _a, _b, _c, _d;
        const values = [
          banner == null ? void 0 : banner.id,
          banner == null ? void 0 : banner.comment,
          banner == null ? void 0 : banner.moderation_note,
          getBannerTitle(banner),
          getBannerShort(banner),
          getBannerLink(banner),
          (_a = banner == null ? void 0 : banner.owner) == null ? void 0 : _a.name,
          (_b = banner == null ? void 0 : banner.owner) == null ? void 0 : _b.email,
          (_c = banner == null ? void 0 : banner.moderator) == null ? void 0 : _c.name,
          (_d = banner == null ? void 0 : banner.moderator) == null ? void 0 : _d.email
        ];
        return values.some((value) => normalize(value).includes(query));
      });
      return sortBanners(filtered);
    });
    const paginatedBanners = computed(() => {
      const perPage = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * perPage;
      return filteredBanners.value.slice(start, start + perPage);
    });
    const displayedBanners = computed(() => {
      return props.useServerProcessing ? bannersList.value : paginatedBanners.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const selectedBanners = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedBanners.value.map((banner) => banner.id);
      if (checked) {
        selectedBanners.value = [.../* @__PURE__ */ new Set([...selectedBanners.value, ...ids])];
      } else {
        selectedBanners.value = selectedBanners.value.filter((id) => !ids.includes(id));
      }
    };
    const toggleSelectBanner = (bannerId) => {
      const index = selectedBanners.value.indexOf(bannerId);
      if (index > -1) {
        selectedBanners.value.splice(index, 1);
      } else {
        selectedBanners.value.push(bannerId);
      }
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedBanners.value.length) {
        toast.warning("Выберите баннеры для активации/деактивации");
        return;
      }
      const idsToUpdate = [...selectedBanners.value];
      router.put(
        route("admin.actions.blogBanners.bulkUpdateActivity"),
        { ids: idsToUpdate, activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localBanners.value = localBanners.value.map((banner) => {
              return idsToUpdate.includes(banner.id) ? { ...banner, activity: newActivity } : banner;
            });
            selectedBanners.value = [];
            toast.success("Активность баннеров массово обновлена");
          },
          onError: (errors) => {
            const msg = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности";
            toast.error(msg);
          }
        }
      );
    };
    const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
      if (!selectedBanners.value.length) {
        toast.warning("Выберите баннеры для массового действия");
        return;
      }
      const idsToUpdate = [...selectedBanners.value];
      router.put(
        route(routeName),
        { ids: idsToUpdate, [field]: newValue },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localBanners.value = localBanners.value.map((banner) => {
              return idsToUpdate.includes(banner.id) ? { ...banner, [field]: newValue } : banner;
            });
            selectedBanners.value = [];
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
      if (!selectedBanners.value.length) {
        toast.warning("Выберите хотя бы один баннер для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные баннеры?")) return;
      router.delete(route("admin.actions.blogBanners.bulkDestroy"), {
        data: { ids: selectedBanners.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          selectedBanners.value = [];
          toast.success("Массовое удаление баннеров успешно завершено.");
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          toast.error(errors[errorKey] || "Произошла ошибка при удалении баннеров.");
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
        bulkToggleFlag("left", true, "admin.actions.blogBanners.bulkUpdateLeft", "Баннеры добавлены в левую колонку");
      } else if (action === "noLeft") {
        bulkToggleFlag("left", false, "admin.actions.blogBanners.bulkUpdateLeft", "Баннеры убраны из левой колонки");
      } else if (action === "main") {
        bulkToggleFlag("main", true, "admin.actions.blogBanners.bulkUpdateMain", "Баннеры добавлены в главный блок");
      } else if (action === "noMain") {
        bulkToggleFlag("main", false, "admin.actions.blogBanners.bulkUpdateMain", "Баннеры убраны из главного блока");
      } else if (action === "right") {
        bulkToggleFlag("right", true, "admin.actions.blogBanners.bulkUpdateRight", "Баннеры добавлены в правую колонку");
      } else if (action === "noRight") {
        bulkToggleFlag("right", false, "admin.actions.blogBanners.bulkUpdateRight", "Баннеры убраны из правой колонки");
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const approveBanner = (banner, status = 1, note = "") => {
      if (!(banner == null ? void 0 : banner.id)) return;
      router.put(
        route("admin.actions.blogBanners.approve", { blogBanner: banner.id }),
        { moderation_status: status, moderation_note: note },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBanner(banner.id, (node) => {
              node.moderation_status = status;
              node.is_approved = status === 1;
              node.moderation_note = note;
            });
            toast.success(status === 1 ? "Баннер одобрен" : "Баннер отклонён");
          },
          onError: () => toast.error("Ошибка модерации баннера")
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
        route("admin.actions.blogBanners.updateSortBulk"),
        { items },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.success("Сортировка баннеров обновлена"),
          onError: (errors) => {
            console.error("Ошибка сортировки баннеров:", errors);
            toast.error(errors.message || "Ошибка обновления сортировки");
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("banners")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("banners"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("banners")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("banners")), 1)
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
              href: _ctx.route("admin.blogBanners.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addBanner"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addBanner")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              "setting-key": "adminBlogBannersProcessingMode",
              mode: __props.adminBlogBannersProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.bannersCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.bannersCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$e, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.bannersCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$f, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.bannersCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountBlogBanners"
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
            if (__props.bannersCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$i, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.bannersCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.bannersCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              if (__props.bannersCount) {
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
            if (__props.bannersCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredBanners.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.banners }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                banners: displayedBanners.value,
                "selected-banners": selectedBanners.value,
                "is-admin": isAdmin.value,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectBanner,
                onToggleAll: toggleAll,
                onApprove: approveBanner
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                banners: displayedBanners.value,
                "selected-banners": selectedBanners.value,
                "is-admin": isAdmin.value,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectBanner,
                onToggleAll: toggleAll,
                onApprove: approveBanner
              }, null, _parent2, _scopeId));
            }
            if (__props.bannersCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredBanners.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.banners }, null, _parent2, _scopeId));
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
              onConfirm: deleteBanner,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$c, {
                      href: _ctx.route("admin.blogBanners.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addBanner")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$d, {
                      "setting-key": "adminBlogBannersProcessingMode",
                      mode: __props.adminBlogBannersProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.bannersCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.bannersCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.bannersCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$f, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.bannersCount ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountBlogBanners"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.bannersCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$i, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.bannersCount), 1)
                      ]),
                      _: 1
                    }),
                    __props.bannersCount ? (openBlock(), createBlock(_sfc_main$4, {
                      key: 0,
                      onChange: handleBulkAction
                    })) : createCommentVNode("", true),
                    createVNode(_sfc_main$j, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.bannersCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredBanners.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.banners
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    banners: displayedBanners.value,
                    "selected-banners": selectedBanners.value,
                    "is-admin": isAdmin.value,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectBanner,
                    onToggleAll: toggleAll,
                    onApprove: approveBanner
                  }, null, 8, ["banners", "selected-banners", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    banners: displayedBanners.value,
                    "selected-banners": selectedBanners.value,
                    "is-admin": isAdmin.value,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectBanner,
                    onToggleAll: toggleAll,
                    onApprove: approveBanner
                  }, null, 8, ["banners", "selected-banners", "is-admin"])),
                  __props.bannersCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredBanners.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.banners
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$m, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteBanner,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Blog/BlogBanners/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
