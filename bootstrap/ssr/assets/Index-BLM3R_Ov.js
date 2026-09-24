import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, withModifiers, withDirectives, createCommentVNode, vShow, createTextVNode, computed } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent, ssrRenderClass, ssrRenderStyle } from "vue/server-renderer";
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
import { _ as _sfc_main$d } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$f, a as _sfc_main$l } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$h } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketShop/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit mt-2 mb-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>────────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>────────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option disabled>────────────────────</option><option value="urlAsc">URL A→Z</option><option value="urlDesc">URL Z→A</option><option disabled>────────────────────</option><option value="emailAsc">Email A→Z</option><option value="emailDesc">Email Z→A</option><option value="phoneAsc">${ssrInterpolate(unref(t)("phone"))} A→Z</option><option value="phoneDesc">${ssrInterpolate(unref(t)("phone"))} Z→A</option><option disabled>────────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option disabled>────────────────────</option><option value="statusDraft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="statusPublished">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="statusArchived">${ssrInterpolate(unref(t)("statusArchived"))}</option><option disabled>────────────────────</option><option value="companyLegalNameAsc">${ssrInterpolate(unref(t)("company"))} A→Z</option><option value="companyLegalNameDesc">${ssrInterpolate(unref(t)("company"))} Z→A</option><option disabled>────────────────────</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>────────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option disabled>────────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>────────────────────</option><option value="leftDesc">${ssrInterpolate(unref(t)("inLeft"))} ON→OFF</option><option value="leftAsc">${ssrInterpolate(unref(t)("inLeft"))} OFF→ON</option><option value="left">${ssrInterpolate(unref(t)("inLeft"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("notLeft"))}</option><option disabled>────────────────────</option><option value="mainDesc">${ssrInterpolate(unref(t)("inMain"))} ON→OFF</option><option value="mainAsc">${ssrInterpolate(unref(t)("inMain"))} OFF→ON</option><option value="main">${ssrInterpolate(unref(t)("inMain"))}</option><option value="noMain">${ssrInterpolate(unref(t)("notMain"))}</option><option disabled>────────────────────</option><option value="rightDesc">${ssrInterpolate(unref(t)("inRight"))} ON→OFF</option><option value="rightAsc">${ssrInterpolate(unref(t)("inRight"))} OFF→ON</option><option value="right">${ssrInterpolate(unref(t)("inRight"))}</option><option value="noRight">${ssrInterpolate(unref(t)("notRight"))}</option><option disabled>────────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option><option disabled>────────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>────────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="showFromAtDesc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortStarted"))} ↓ </option><option value="showFromAtAsc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortStarted"))} ↑ </option><option value="showToAtDesc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortExpires"))} ↓ </option><option value="showToAtAsc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortExpires"))} ↑ </option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>────────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketShop/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "ShopTable",
  __ssrInlineRender: true,
  props: {
    shops: { type: Array, default: () => [] },
    selectedShops: { type: Array, default: () => [] },
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
    const { t, locale } = useI18n();
    const props = __props;
    const emits = __emit;
    const localShops = ref([]);
    watch(
      () => props.shops,
      (newVal) => {
        localShops.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits(
        "update-sort-order",
        localShops.value.map((shop) => shop.id)
      );
    };
    const allSelected = () => {
      return localShops.value.length > 0 && localShops.value.every((shop) => props.selectedShops.includes(shop.id));
    };
    const shopTranslation = (shop) => (shop == null ? void 0 : shop.translation) || {};
    const shopTitle = (shop) => {
      var _a;
      return ((_a = shopTranslation(shop)) == null ? void 0 : _a.title) || `ID: ${shop == null ? void 0 : shop.id}`;
    };
    const companyTranslation = (shop) => {
      var _a;
      return ((_a = shop == null ? void 0 : shop.company) == null ? void 0 : _a.translation) || {};
    };
    const companyTitle = (shop) => {
      var _a, _b;
      return ((_a = companyTranslation(shop)) == null ? void 0 : _a.title) || ((_b = shop == null ? void 0 : shop.company) == null ? void 0 : _b.legal_name) || `Company ID: ${shop == null ? void 0 : shop.market_company_id}`;
    };
    const statusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const getStatusLabel = (status) => {
      return t(statusLabelKeyMap[status] || status || "no");
    };
    const ownerTitle = (shop) => {
      const owner = shop == null ? void 0 : shop.owner;
      if (!owner) {
        return t("noData");
      }
      return `${owner.name || ""}${owner.email ? ` — ${owner.email}` : ""}`.trim();
    };
    const ownerAvatar = (shop) => {
      var _a;
      return ((_a = shop == null ? void 0 : shop.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const logoUrl = (shop) => {
      const logo = shop == null ? void 0 : shop.logo;
      if (!logo) {
        return "/storage/market/market_shops/logos/default-image-light.png";
      }
      return logo.startsWith("/storage/") ? logo : `/storage/${logo}`;
    };
    const getPrimaryImage = (shop) => {
      const images = Array.isArray(shop == null ? void 0 : shop.images) ? shop.images : [];
      if (!images.length) {
        return null;
      }
      return [...images].sort(
        (a, b) => Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0)
      )[0];
    };
    const imageUrl = (shop) => {
      const image = getPrimaryImage(shop);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "/storage/market/market_shop_images/default-image.png";
    };
    const imageAlt = (shop) => {
      var _a;
      return ((_a = getPrimaryImage(shop)) == null ? void 0 : _a.alt) || shopTitle(shop);
    };
    const imageTitle = (shop) => {
      var _a;
      return ((_a = getPrimaryImage(shop)) == null ? void 0 : _a.caption) || shopTitle(shop);
    };
    const moderationBadge = (status) => {
      const normalizedStatus = Number(status ?? 0);
      if (normalizedStatus === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (normalizedStatus === 2) {
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
    const dateLocale = () => {
      const currentLocale = locale.value || "ru";
      const locales = {
        ru: "ru-RU",
        en: "en-US",
        kk: "kk-KZ",
        kz: "kk-KZ"
      };
      return locales[currentLocale] || currentLocale;
    };
    const formatDate = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return new Intl.DateTimeFormat(dateLocale(), {
        year: "numeric",
        month: "long",
        day: "numeric"
      }).format(date);
    };
    const formatDateTime = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return new Intl.DateTimeFormat(dateLocale(), {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit"
      }).format(date);
    };
    const showWindowTitle = (shop) => {
      if (!(shop == null ? void 0 : shop.show_from_at) && !(shop == null ? void 0 : shop.show_to_at)) {
        return formatDate(shop == null ? void 0 : shop.published_at);
      }
      const from = formatDateTime(shop == null ? void 0 : shop.show_from_at);
      const to = formatDateTime(shop == null ? void 0 : shop.show_to_at);
      return `${t("show")}: ${from || "—"} / ${to || "—"}`;
    };
    const truncateText = (text, maxLength = 50) => {
      const value = String(text ?? "");
      if (!value) {
        return "";
      }
      return value.length > maxLength ? `${value.slice(0, maxLength).trimEnd()}…` : value;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedShops.length)}</div>`);
      if (localShops.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localShops.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-1 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("owner"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24"><path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path><path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path><path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path><path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path><path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"></path></svg></div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("company"))}</div></th><th class="px-1 py-3"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-1 py-3"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("logo"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M274.835 12.646l25.516 62.393c4.213 10.301 16.671 14.349 26.134 8.492l57.316-35.479c15.49-9.588 34.808 4.447 30.475 22.142l-16.03 65.475c-2.647 10.81 5.053 21.408 16.152 22.231l67.224 4.987c18.167 1.348 25.546 24.057 11.641 35.826L441.81 242.26c-8.495 7.19-8.495 20.289 0 27.479l51.454 43.548c13.906 11.769 6.527 34.478-11.641 35.826l-67.224 4.987c-11.099.823-18.799 11.421-16.152 22.231l16.03 65.475c4.332 17.695-14.986 31.73-30.475 22.142l-57.316-35.479c-9.463-5.858-21.922-1.81-26.134 8.492l-25.516 62.393c-6.896 16.862-30.774 16.862-37.67 0l-25.516-62.393c-4.213-10.301-16.671-14.349-26.134-8.492l-57.317 35.479c-15.49 9.588-34.808-4.447-30.475-22.142l16.03-65.475c2.647-10.81-5.053-21.408-16.152-22.231l-67.224-4.987c-18.167-1.348-25.546-24.057-11.641-35.826L70.19 269.74c8.495-7.19 8.495-20.289 0-27.479l-51.454-43.548c-13.906-11.769-6.527-34.478 11.641-35.826l67.224-4.987c11.099-.823 18.799-11.421 16.152-22.231l-16.03-65.475c-4.332-17.695 14.986-31.73 30.475-22.142l57.317 35.479c9.463 5.858 21.921 1.81 26.134-8.492l25.516-62.393c6.896-16.861 30.774-16.861 37.67 0zM392 256c0-74.991-61.01-136-136-136-74.991 0-136 61.009-136 136s61.009 136 136 136c74.99 0 136-61.009 136-136zm-32 0c0 57.346-46.654 104-104 104s-104-46.654-104-104 46.654-104 104-104 104 46.654 104 104z"></path></svg></div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("store"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("contacts"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("show"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-1 py-1 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localShops.value,
          "onUpdate:modelValue": ($event) => localShops.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: shop }, _push2, _parent2, _scopeId) => {
            var _a, _b;
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-1 py-1 text-center cursor-move handle w-px"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-1 py-1 whitespace-nowrap w-px"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${ssrRenderAttr("title", `[${shop.sort}] / ${formatDate(shop.published_at)}`)}${_scopeId}>${ssrInterpolate(shop.id)}</div></td><td class="px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(shop))}${ssrRenderAttr("title", ownerTitle(shop))}${ssrRenderAttr("alt", unref(t)("owner"))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${_scopeId}></div></td><td class="px-1 py-1"${_scopeId}><div class="text-left text-xs"${_scopeId}><div class="text-indigo-700 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(truncateText(companyTitle(shop), 50))}</div><div class="text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText((_a = shop == null ? void 0 : shop.company) == null ? void 0 : _a.legal_name, 60))}</div></div></td><td class="px-1 py-1 w-12"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", imageUrl(shop))}${ssrRenderAttr("alt", imageAlt(shop))}${ssrRenderAttr("title", imageTitle(shop))} class="h-8 w-12 object-cover rounded-sm border border-slate-300 dark:border-slate-600"${_scopeId}></div></td><td class="px-1 py-1 w-12"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", logoUrl(shop))}${ssrRenderAttr("alt", shopTitle(shop))}${ssrRenderAttr("title", shopTitle(shop))} class="h-8 w-12 object-cover rounded-sm border border-slate-300 dark:border-slate-600"${_scopeId}></div></td><td class="px-1 py-1"${_scopeId}><div class="text-left"${_scopeId}><a${ssrRenderAttr("href", `/market/shops/${encodeURIComponent(shop.url || "")}`)} target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", showWindowTitle(shop))} class="text-blue-700 dark:text-blue-300 text-sm hover:underline hover:text-amber-700 dark:hover:text-amber-200"${_scopeId}>${ssrInterpolate(truncateText(shopTitle(shop), 70))}</a><div class="text-[11px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(shop.url, 70))}</div><div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(getStatusLabel(shop.status))}</div></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-left text-xs"${_scopeId}><div class="text-amber-700 dark:text-amber-300"${_scopeId}>${ssrInterpolate(shop.phone || "—")}</div><div class="text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(shop.email || "")}</div></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-300"${_scopeId}>${ssrInterpolate(shop.views ?? 0)}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: Boolean(shop.left),
                title: shop.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => emits("toggle-left", shop)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: Boolean(shop.main),
                title: shop.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => emits("toggle-main", shop)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: Boolean(shop.right),
                title: shop.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => emits("toggle-right", shop)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(shop.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", shop.moderation_note && shop.moderated_at ? `${shop.moderation_note} [${formatDateTime(shop.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(shop.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isAdmin: __props.isAdmin,
                status: (shop == null ? void 0 : shop.moderation_status) ?? 0,
                initialNote: (shop == null ? void 0 : shop.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emits("approve", shop, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: Boolean(shop.activity),
                title: shop.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emits("toggle-activity", shop)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.marketShops.edit", { marketShop: shop.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => emits("delete", shop)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedShops.includes(shop.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                      title: `[${shop.sort}] / ${formatDate(shop.published_at)}`
                    }, toDisplayString(shop.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(shop),
                        title: ownerTitle(shop),
                        alt: unref(t)("owner"),
                        class: "h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"
                      }, null, 8, ["src", "title", "alt"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "text-left text-xs" }, [
                      createVNode("div", { class: "text-indigo-700 dark:text-indigo-300" }, toDisplayString(truncateText(companyTitle(shop), 50)), 1),
                      createVNode("div", { class: "text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(truncateText((_b = shop == null ? void 0 : shop.company) == null ? void 0 : _b.legal_name, 60)), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 w-12" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: imageUrl(shop),
                        alt: imageAlt(shop),
                        title: imageTitle(shop),
                        class: "h-8 w-12 object-cover rounded-sm border border-slate-300 dark:border-slate-600"
                      }, null, 8, ["src", "alt", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 w-12" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: logoUrl(shop),
                        alt: shopTitle(shop),
                        title: shopTitle(shop),
                        class: "h-8 w-12 object-cover rounded-sm border border-slate-300 dark:border-slate-600"
                      }, null, 8, ["src", "alt", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "text-left" }, [
                      createVNode("a", {
                        href: `/market/shops/${encodeURIComponent(shop.url || "")}`,
                        target: "_blank",
                        rel: "noopener noreferrer",
                        title: showWindowTitle(shop),
                        class: "text-blue-700 dark:text-blue-300 text-sm hover:underline hover:text-amber-700 dark:hover:text-amber-200"
                      }, toDisplayString(truncateText(shopTitle(shop), 70)), 9, ["href", "title"]),
                      createVNode("div", { class: "text-[11px] text-slate-500 dark:text-slate-300" }, toDisplayString(truncateText(shop.url, 70)), 1),
                      createVNode("div", { class: "text-[10px] text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(getStatusLabel(shop.status)), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-left text-xs" }, [
                      createVNode("div", { class: "text-amber-700 dark:text-amber-300" }, toDisplayString(shop.phone || "—"), 1),
                      createVNode("div", { class: "text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(shop.email || ""), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-blue-600 dark:text-blue-300" }, toDisplayString(shop.views ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: Boolean(shop.left),
                        title: shop.left ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleLeft: ($event) => emits("toggle-left", shop)
                      }, null, 8, ["isActive", "title", "onToggleLeft"]),
                      createVNode(_sfc_main$6, {
                        isActive: Boolean(shop.main),
                        title: shop.main ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleMain: ($event) => emits("toggle-main", shop)
                      }, null, 8, ["isActive", "title", "onToggleMain"]),
                      createVNode(_sfc_main$7, {
                        isActive: Boolean(shop.right),
                        title: shop.right ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleRight: ($event) => emits("toggle-right", shop)
                      }, null, 8, ["isActive", "title", "onToggleRight"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(shop.moderation_status).class],
                        title: shop.moderation_note && shop.moderated_at ? `${shop.moderation_note} [${formatDateTime(shop.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(shop.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$8, {
                        isAdmin: __props.isAdmin,
                        status: (shop == null ? void 0 : shop.moderation_status) ?? 0,
                        initialNote: (shop == null ? void 0 : shop.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => emits("approve", shop, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode(_sfc_main$9, {
                        isActive: Boolean(shop.activity),
                        title: shop.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emits("toggle-activity", shop)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.marketShops.edit", { marketShop: shop.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => emits("delete", shop)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedShops.includes(shop.id),
                        onChange: ($event) => emits("toggle-select", shop.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketShop/Table/ShopTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "ShopCardGrid",
  __ssrInlineRender: true,
  props: {
    shops: { type: Array, default: () => [] },
    selectedShops: { type: Array, default: () => [] },
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
    const { t, locale } = useI18n();
    const props = __props;
    const emits = __emit;
    const localShops = ref([]);
    const openedOwnerBlocks = ref([]);
    watch(
      () => props.shops,
      (newVal) => {
        localShops.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits(
        "update-sort-order",
        localShops.value.map((shop) => shop.id)
      );
    };
    const allSelected = () => {
      return localShops.value.length > 0 && localShops.value.every((shop) => props.selectedShops.includes(shop.id));
    };
    const shopTranslation = (shop) => (shop == null ? void 0 : shop.translation) || {};
    const shopTitle = (shop) => {
      var _a;
      return ((_a = shopTranslation(shop)) == null ? void 0 : _a.title) || `ID: ${shop == null ? void 0 : shop.id}`;
    };
    const shopShort = (shop) => {
      var _a;
      return ((_a = shopTranslation(shop)) == null ? void 0 : _a.short) || "";
    };
    const companyTranslation = (shop) => {
      var _a;
      return ((_a = shop == null ? void 0 : shop.company) == null ? void 0 : _a.translation) || {};
    };
    const companyTitle = (shop) => {
      var _a, _b;
      return ((_a = companyTranslation(shop)) == null ? void 0 : _a.title) || ((_b = shop == null ? void 0 : shop.company) == null ? void 0 : _b.legal_name) || `Company ID: ${shop == null ? void 0 : shop.market_company_id}`;
    };
    const statusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const getStatusLabel = (status) => {
      return t(statusLabelKeyMap[status] || status || "no");
    };
    const ownerName = (shop) => {
      var _a;
      return ((_a = shop == null ? void 0 : shop.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (shop) => {
      var _a;
      return ((_a = shop == null ? void 0 : shop.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (shop) => {
      const owner = shop == null ? void 0 : shop.owner;
      if (!owner) {
        return t("noData");
      }
      return `${owner.name || ""}${owner.email ? ` — ${owner.email}` : ""}`.trim();
    };
    const ownerAvatar = (shop) => {
      var _a;
      return ((_a = shop == null ? void 0 : shop.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const logoUrl = (shop) => {
      const logo = shop == null ? void 0 : shop.logo;
      if (!logo) {
        return "/storage/market/market_shops/logos/default-image-light.png";
      }
      return logo.startsWith("/storage/") ? logo : `/storage/${logo}`;
    };
    const getPrimaryImage = (shop) => {
      const images = Array.isArray(shop == null ? void 0 : shop.images) ? shop.images : [];
      if (!images.length) {
        return null;
      }
      return [...images].sort(
        (a, b) => Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0)
      )[0];
    };
    const imageUrl = (shop) => {
      const image = getPrimaryImage(shop);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "/storage/market/market_shop_images/default-image.png";
    };
    const imageAlt = (shop) => {
      var _a;
      return ((_a = getPrimaryImage(shop)) == null ? void 0 : _a.alt) || shopTitle(shop);
    };
    const imageTitle = (shop) => {
      var _a;
      return ((_a = getPrimaryImage(shop)) == null ? void 0 : _a.caption) || shopTitle(shop);
    };
    const dateLocale = () => {
      const currentLocale = locale.value || "ru";
      const locales = {
        ru: "ru-RU",
        en: "en-US",
        kk: "kk-KZ",
        kz: "kk-KZ"
      };
      return locales[currentLocale] || currentLocale;
    };
    const formatDate = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return new Intl.DateTimeFormat(dateLocale(), {
        year: "numeric",
        month: "long",
        day: "numeric"
      }).format(date);
    };
    const formatDateTime = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return new Intl.DateTimeFormat(dateLocale(), {
        year: "numeric",
        month: "short",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit"
      }).format(date);
    };
    const showWindow = (shop) => {
      if (!(shop == null ? void 0 : shop.show_from_at) && !(shop == null ? void 0 : shop.show_to_at)) {
        return formatDate(shop == null ? void 0 : shop.published_at);
      }
      const from = formatDateTime(shop == null ? void 0 : shop.show_from_at);
      const to = formatDateTime(shop == null ? void 0 : shop.show_to_at);
      return `${t("show")}: ${from || "—"} / ${to || "—"}`;
    };
    const truncateText = (text, maxLength = 80) => {
      const value = String(text ?? "");
      if (!value) {
        return "";
      }
      return value.length > maxLength ? `${value.slice(0, maxLength).trimEnd()}…` : value;
    };
    const moderationBadge = (status) => {
      const normalizedStatus = Number(status ?? 0);
      if (normalizedStatus === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (normalizedStatus === 2) {
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
    const isOwnerBlockOpen = (shopId) => {
      return openedOwnerBlocks.value.includes(shopId);
    };
    const toggleOwnerBlock = (shopId) => {
      if (isOwnerBlockOpen(shopId)) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
          (id) => id !== shopId
        );
        return;
      }
      openedOwnerBlocks.value.push(shopId);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedShops.length)}</div>`);
      if (localShops.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localShops.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localShops.value,
          "onUpdate:modelValue": ($event) => localShops.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: shop }, _push2, _parent2, _scopeId) => {
            var _a, _b;
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${shop.sort}] / ${formatDate(shop.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(shop.id)}</div><button type="button" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"${ssrRenderAttr("title", isOwnerBlockOpen(shop.id) ? unref(t)("hideOwner") : unref(t)("showOwner"))}${_scopeId}><svg class="${ssrRenderClass([{ "rotate-180": isOwnerBlockOpen(shop.id) }, "w-4 h-4 transition-transform duration-200"])}" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"${_scopeId}></path></svg></button></div><div class="flex items-center space-x-2"${_scopeId}><span class="${ssrRenderClass([moderationBadge(shop.moderation_status).class, "text-[10px] px-2 py-0.5 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", shop.moderation_note && shop.moderated_at ? `${shop.moderation_note} [${formatDateTime(shop.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(shop.moderation_status).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedShops.includes(shop.id)) ? " checked" : ""}${_scopeId}></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-1"${_scopeId}><div style="${ssrRenderStyle(isOwnerBlockOpen(shop.id) ? null : { display: "none" })}" class="flex flex-col items-center justify-center text-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(shop))}${ssrRenderAttr("title", ownerTitle(shop))}${ssrRenderAttr("alt", unref(t)("owner"))} class="h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"${_scopeId}><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerName(shop))}${_scopeId}>${ssrInterpolate(ownerName(shop))}</div>`);
              if (ownerEmail(shop)) {
                _push2(`<div class="text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerEmail(shop))}${_scopeId}>${ssrInterpolate(ownerEmail(shop))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(showWindow(shop))}</div></div><div class="relative w-full bg-slate-200 dark:bg-slate-900"${_scopeId}><img${ssrRenderAttr("src", imageUrl(shop))}${ssrRenderAttr("alt", imageAlt(shop))}${ssrRenderAttr("title", imageTitle(shop))} class="h-32 w-full object-cover"${_scopeId}></div><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", logoUrl(shop))}${ssrRenderAttr("alt", shopTitle(shop))}${ssrRenderAttr("title", shopTitle(shop))} class="h-12 w-20 object-cover rounded-sm border border-slate-300 dark:border-slate-600"${_scopeId}></div><a${ssrRenderAttr("href", `/market/shops/${encodeURIComponent(shop.url || "")}`)} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-blue-700 dark:text-blue-300 hover:underline hover:text-amber-700 dark:hover:text-amber-200 line-clamp-2 text-center"${_scopeId}>${ssrInterpolate(truncateText(shopTitle(shop), 90))}</a><div class="text-center text-[11px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(shop.url, 90))}</div><div class="text-center text-[11px] font-semibold text-indigo-700 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(truncateText(companyTitle(shop), 90))}</div>`);
              if ((_a = shop == null ? void 0 : shop.company) == null ? void 0 : _a.legal_name) {
                _push2(`<div class="text-center text-[10px] text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(truncateText(shop.company.legal_name, 100))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (shopShort(shop)) {
                _push2(`<div class="font-semibold text-[12px] text-center text-sky-700 dark:text-sky-300"${_scopeId}>${ssrInterpolate(truncateText(shopShort(shop), 120))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="grid grid-cols-1 gap-0.5 text-[11px] text-slate-600 dark:text-slate-300"${_scopeId}><div class="text-center font-semibold"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("contacts"))}: </span><span class="text-amber-700 dark:text-amber-300"${_scopeId}>${ssrInterpolate(shop.phone || "—")}</span></div>`);
              if (shop.email) {
                _push2(`<div class="text-center line-clamp-1"${ssrRenderAttr("title", shop.email)}${_scopeId}>${ssrInterpolate(shop.email)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="flex items-center justify-center gap-3 text-[11px] font-semibold text-slate-600 dark:text-slate-200"${_scopeId}><div class="flex items-center justify-center space-x-1"${ssrRenderAttr("title", unref(t)("views"))}${_scopeId}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"${_scopeId}><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"${_scopeId}></path></svg><span class="text-[12px] text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(shop.views ?? 0)}</span></div><div class="flex items-center justify-center space-x-1"${ssrRenderAttr("title", unref(t)("images"))}${_scopeId}><span class="text-[11px]"${_scopeId}>${ssrInterpolate(unref(t)("images"))}: </span><span class="text-[12px] text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(shop.images_count ?? 0)}</span></div></div><div class="grid grid-cols-1 gap-0.5 text-[11px] text-slate-600 dark:text-slate-300"${_scopeId}><div class="font-semibold text-center text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("status"))}: </span> ${ssrInterpolate(getStatusLabel(shop.status))}</div></div><div class="flex justify-center space-x-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(shop.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", shop.moderation_note && shop.moderated_at ? `${shop.moderation_note} [${formatDateTime(shop.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(shop.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isAdmin: __props.isAdmin,
                status: (shop == null ? void 0 : shop.moderation_status) ?? 0,
                initialNote: (shop == null ? void 0 : shop.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emits("approve", shop, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="flex items-center justify-between px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: Boolean(shop.left),
                title: shop.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => emits("toggle-left", shop)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: Boolean(shop.main),
                title: shop.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => emits("toggle-main", shop)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: Boolean(shop.right),
                title: shop.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => emits("toggle-right", shop)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: Boolean(shop.activity),
                title: shop.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emits("toggle-activity", shop)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.marketShops.edit", { marketShop: shop.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => emits("delete", shop)
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
                        title: `[${shop.sort}] / ${formatDate(shop.published_at)}`
                      }, " ID: " + toDisplayString(shop.id), 9, ["title"]),
                      createVNode("button", {
                        type: "button",
                        class: "text-slate-400 hover:text-blue-600 dark:hover:text-blue-300",
                        title: isOwnerBlockOpen(shop.id) ? unref(t)("hideOwner") : unref(t)("showOwner"),
                        onClick: withModifiers(($event) => toggleOwnerBlock(shop.id), ["prevent"])
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: ["w-4 h-4 transition-transform duration-200", { "rotate-180": isOwnerBlockOpen(shop.id) }],
                          fill: "currentColor",
                          viewBox: "0 0 20 20"
                        }, [
                          createVNode("path", {
                            "fill-rule": "evenodd",
                            d: "M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z",
                            "clip-rule": "evenodd"
                          })
                        ], 2))
                      ], 8, ["title", "onClick"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border font-semibold", moderationBadge(shop.moderation_status).class],
                        title: shop.moderation_note && shop.moderated_at ? `${shop.moderation_note} [${formatDateTime(shop.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(shop.moderation_status).text), 11, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedShops.includes(shop.id),
                        onChange: ($event) => emits("toggle-select", shop.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-1" }, [
                    withDirectives(createVNode("div", { class: "flex flex-col items-center justify-center text-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(shop),
                        title: ownerTitle(shop),
                        alt: unref(t)("owner"),
                        class: "h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"
                      }, null, 8, ["src", "title", "alt"]),
                      createVNode("div", {
                        class: "mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1",
                        title: ownerName(shop)
                      }, toDisplayString(ownerName(shop)), 9, ["title"]),
                      ownerEmail(shop) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1",
                        title: ownerEmail(shop)
                      }, toDisplayString(ownerEmail(shop)), 9, ["title"])) : createCommentVNode("", true),
                      createVNode("div", { class: "flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(showWindow(shop)), 1)
                    ], 512), [
                      [vShow, isOwnerBlockOpen(shop.id)]
                    ]),
                    createVNode("div", { class: "relative w-full bg-slate-200 dark:bg-slate-900" }, [
                      createVNode("img", {
                        src: imageUrl(shop),
                        alt: imageAlt(shop),
                        title: imageTitle(shop),
                        class: "h-32 w-full object-cover"
                      }, null, 8, ["src", "alt", "title"])
                    ]),
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: logoUrl(shop),
                        alt: shopTitle(shop),
                        title: shopTitle(shop),
                        class: "h-12 w-20 object-cover rounded-sm border border-slate-300 dark:border-slate-600"
                      }, null, 8, ["src", "alt", "title"])
                    ]),
                    createVNode("a", {
                      href: `/market/shops/${encodeURIComponent(shop.url || "")}`,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-sm font-semibold text-blue-700 dark:text-blue-300 hover:underline hover:text-amber-700 dark:hover:text-amber-200 line-clamp-2 text-center"
                    }, toDisplayString(truncateText(shopTitle(shop), 90)), 9, ["href"]),
                    createVNode("div", { class: "text-center text-[11px] text-slate-500 dark:text-slate-300" }, toDisplayString(truncateText(shop.url, 90)), 1),
                    createVNode("div", { class: "text-center text-[11px] font-semibold text-indigo-700 dark:text-indigo-300" }, toDisplayString(truncateText(companyTitle(shop), 90)), 1),
                    ((_b = shop == null ? void 0 : shop.company) == null ? void 0 : _b.legal_name) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-center text-[10px] text-slate-500 dark:text-slate-400"
                    }, toDisplayString(truncateText(shop.company.legal_name, 100)), 1)) : createCommentVNode("", true),
                    shopShort(shop) ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "font-semibold text-[12px] text-center text-sky-700 dark:text-sky-300"
                    }, toDisplayString(truncateText(shopShort(shop), 120)), 1)) : createCommentVNode("", true),
                    createVNode("div", { class: "grid grid-cols-1 gap-0.5 text-[11px] text-slate-600 dark:text-slate-300" }, [
                      createVNode("div", { class: "text-center font-semibold" }, [
                        createVNode("span", null, toDisplayString(unref(t)("contacts")) + ": ", 1),
                        createVNode("span", { class: "text-amber-700 dark:text-amber-300" }, toDisplayString(shop.phone || "—"), 1)
                      ]),
                      shop.email ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-center line-clamp-1",
                        title: shop.email
                      }, toDisplayString(shop.email), 9, ["title"])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "flex items-center justify-center gap-3 text-[11px] font-semibold text-slate-600 dark:text-slate-200" }, [
                      createVNode("div", {
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
                        createVNode("span", { class: "text-[12px] text-slate-700 dark:text-slate-200" }, toDisplayString(shop.views ?? 0), 1)
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "flex items-center justify-center space-x-1",
                        title: unref(t)("images")
                      }, [
                        createVNode("span", { class: "text-[11px]" }, toDisplayString(unref(t)("images")) + ": ", 1),
                        createVNode("span", { class: "text-[12px] text-slate-700 dark:text-slate-200" }, toDisplayString(shop.images_count ?? 0), 1)
                      ], 8, ["title"])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 gap-0.5 text-[11px] text-slate-600 dark:text-slate-300" }, [
                      createVNode("div", { class: "font-semibold text-center text-fuchsia-700 dark:text-fuchsia-300" }, [
                        createVNode("span", null, toDisplayString(unref(t)("status")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(getStatusLabel(shop.status)), 1)
                      ])
                    ]),
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(shop.moderation_status).class],
                        title: shop.moderation_note && shop.moderated_at ? `${shop.moderation_note} [${formatDateTime(shop.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(shop.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$8, {
                        isAdmin: __props.isAdmin,
                        status: (shop == null ? void 0 : shop.moderation_status) ?? 0,
                        initialNote: (shop == null ? void 0 : shop.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => emits("approve", shop, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-between px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: Boolean(shop.left),
                        title: shop.left ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleLeft: ($event) => emits("toggle-left", shop)
                      }, null, 8, ["isActive", "title", "onToggleLeft"]),
                      createVNode(_sfc_main$6, {
                        isActive: Boolean(shop.main),
                        title: shop.main ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleMain: ($event) => emits("toggle-main", shop)
                      }, null, 8, ["isActive", "title", "onToggleMain"]),
                      createVNode(_sfc_main$7, {
                        isActive: Boolean(shop.right),
                        title: shop.right ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleRight: ($event) => emits("toggle-right", shop)
                      }, null, 8, ["isActive", "title", "onToggleRight"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$9, {
                        isActive: Boolean(shop.activity),
                        title: shop.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emits("toggle-activity", shop)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.marketShops.edit", { marketShop: shop.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => emits("delete", shop)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketShop/View/ShopCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminMarketShopsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminMarketShopsPerPage: { type: Number, default: 6 },
    adminMarketShopsDefaultSort: { type: String, default: "idDesc" },
    shops: { type: [Array, Object], default: () => [] },
    shopsCount: { type: Number, default: 0 },
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
    const getShopTranslation = (shop) => (shop == null ? void 0 : shop.translation) || {};
    const getShopTitle = (shop) => {
      var _a;
      return ((_a = getShopTranslation(shop)) == null ? void 0 : _a.title) || `ID: ${shop == null ? void 0 : shop.id}`;
    };
    const getShopTranslationTitle = (shop) => {
      var _a;
      return ((_a = getShopTranslation(shop)) == null ? void 0 : _a.title) || "";
    };
    const getCompanyTranslation = (shop) => {
      var _a;
      return ((_a = shop == null ? void 0 : shop.company) == null ? void 0 : _a.translation) || {};
    };
    const getCompanyTranslationTitle = (shop) => {
      var _a;
      return ((_a = getCompanyTranslation(shop)) == null ? void 0 : _a.title) || "";
    };
    const normalize = (value) => String(value ?? "").trim().toLowerCase();
    const numberValue = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const moderationNum = (value) => numberValue(value);
    const dateValue = (value) => {
      if (!value) return 0;
      const timestamp = new Date(value).getTime();
      return Number.isNaN(timestamp) ? 0 : timestamp;
    };
    const compareIdDesc = (a, b) => numberValue(b == null ? void 0 : b.id) - numberValue(a == null ? void 0 : a.id);
    const compareNumber = (aValue, bValue, direction, a, b) => {
      const result = (numberValue(aValue) - numberValue(bValue)) * direction;
      return result !== 0 ? result : compareIdDesc(a, b);
    };
    const compareText = (aValue, bValue, direction, a, b) => {
      const result = normalize(aValue).localeCompare(
        normalize(bValue),
        locale.value || void 0
      ) * direction;
      return result !== 0 ? result : compareIdDesc(a, b);
    };
    const compareDate = (aValue, bValue, direction, a, b) => {
      const result = (dateValue(aValue) - dateValue(bValue)) * direction;
      return result !== 0 ? result : compareIdDesc(a, b);
    };
    const filterBy = (list, callback) => {
      return list.filter(callback).sort(compareIdDesc);
    };
    const viewMode = ref(localStorage.getItem("admin_view_mode_market_shops") || "cards");
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_market_shops", value);
    });
    const itemsPerPage = ref(props.adminMarketShopsPerPage || 6);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountMarketShops"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} магазинов на странице.`),
          onError: (errors) => {
            toast.error(errors.value || "Ошибка обновления кол-ва магазинов.");
          }
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminMarketShopsDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      router.put(
        route("admin.settings.updateAdminSortMarketShops"),
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
            toast.info("Сортировка магазинов успешно изменена");
          },
          onError: (errors) => {
            toast.error(errors.value || "Ошибка обновления сортировки магазинов.");
          }
        }
      );
    });
    const localShops = ref([]);
    const shopsList = computed(() => {
      var _a, _b, _c, _d;
      if (Array.isArray(props.shops)) {
        return props.shops;
      }
      if (Array.isArray((_a = props.shops) == null ? void 0 : _a.data)) {
        return props.shops.data;
      }
      if (Array.isArray((_c = (_b = props.shops) == null ? void 0 : _b.data) == null ? void 0 : _c.data)) {
        return props.shops.data.data;
      }
      if (Array.isArray((_d = props.shops) == null ? void 0 : _d.resource)) {
        return props.shops.resource;
      }
      return [];
    });
    watch(
      shopsList,
      (newVal) => {
        localShops.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const showConfirmDeleteModal = ref(false);
    const shopToDeleteId = ref(null);
    const shopToDeleteTitle = ref("");
    const confirmDelete = (shopOrId, title = null) => {
      if (typeof shopOrId === "object") {
        shopToDeleteId.value = shopOrId.id;
        shopToDeleteTitle.value = title || getShopTitle(shopOrId);
      } else {
        shopToDeleteId.value = shopOrId;
        shopToDeleteTitle.value = title || `ID: ${shopOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      shopToDeleteId.value = null;
      shopToDeleteTitle.value = "";
    };
    const deleteShop = () => {
      if (shopToDeleteId.value === null) return;
      const idToDelete = shopToDeleteId.value;
      const titleToDelete = shopToDeleteTitle.value;
      router.delete(route("admin.marketShops.destroy", { marketShop: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          toast.success(`Магазин "${titleToDelete || "ID: " + idToDelete}" удалён.`);
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors)[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Магазин: ${titleToDelete || "ID: " + idToDelete})`);
        },
        onFinish: closeModal
      });
    };
    const patchLocalShop = (shopId, callback) => {
      const index = localShops.value.findIndex((shop) => shop.id === shopId);
      if (index !== -1) {
        callback(localShops.value[index]);
      }
    };
    const toggleActivity = (shop) => {
      const newActivity = !shop.activity;
      const title = getShopTitle(shop);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route("admin.actions.marketShops.updateActivity", { marketShop: shop.id }),
        { activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalShop(shop.id, (node) => {
              node.activity = newActivity;
            });
            toast.success(`Магазин "${title}" ${actionText}.`);
          },
          onError: (errors) => {
            toast.error(
              errors.activity || errors.general || `Ошибка изменения активности для "${title}".`
            );
          }
        }
      );
    };
    const toggleLeft = (shop) => {
      const newLeft = !shop.left;
      const title = getShopTitle(shop);
      router.put(
        route("admin.actions.marketShops.updateLeft", { marketShop: shop.id }),
        { left: newLeft },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalShop(shop.id, (node) => {
              node.left = newLeft;
            });
            toast.success(`Позиция left для магазина "${title}" обновлена.`);
          },
          onError: (errors) => {
            toast.error(
              errors.left || errors.general || `Ошибка изменения left для "${title}".`
            );
          }
        }
      );
    };
    const toggleMain = (shop) => {
      const newMain = !shop.main;
      const title = getShopTitle(shop);
      router.put(
        route("admin.actions.marketShops.updateMain", { marketShop: shop.id }),
        { main: newMain },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalShop(shop.id, (node) => {
              node.main = newMain;
            });
            toast.success(`Позиция main для магазина "${title}" обновлена.`);
          },
          onError: (errors) => {
            toast.error(
              errors.main || errors.general || `Ошибка изменения main для "${title}".`
            );
          }
        }
      );
    };
    const toggleRight = (shop) => {
      const newRight = !shop.right;
      const title = getShopTitle(shop);
      router.put(
        route("admin.actions.marketShops.updateRight", { marketShop: shop.id }),
        { right: newRight },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalShop(shop.id, (node) => {
              node.right = newRight;
            });
            toast.success(`Позиция right для магазина "${title}" обновлена.`);
          },
          onError: (errors) => {
            toast.error(
              errors.right || errors.general || `Ошибка изменения right для "${title}".`
            );
          }
        }
      );
    };
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const sortShops = (shops) => {
      const list = [...shops || []];
      const sort = sortParam.value;
      if (sort === "idAsc") {
        return list.sort((a, b) => numberValue(a == null ? void 0 : a.id) - numberValue(b == null ? void 0 : b.id));
      }
      if (sort === "idDesc") {
        return list.sort(compareIdDesc);
      }
      if (sort === "sortAsc") {
        return list.sort((a, b) => compareNumber(a == null ? void 0 : a.sort, b == null ? void 0 : b.sort, 1, a, b));
      }
      if (sort === "sortDesc") {
        return list.sort((a, b) => compareNumber(a == null ? void 0 : a.sort, b == null ? void 0 : b.sort, -1, a, b));
      }
      if (sort === "titleAsc") {
        return list.sort((a, b) => compareText(
          getShopTranslationTitle(a),
          getShopTranslationTitle(b),
          1,
          a,
          b
        ));
      }
      if (sort === "titleDesc") {
        return list.sort((a, b) => compareText(
          getShopTranslationTitle(a),
          getShopTranslationTitle(b),
          -1,
          a,
          b
        ));
      }
      if (sort === "urlAsc") {
        return list.sort((a, b) => compareText(a == null ? void 0 : a.url, b == null ? void 0 : b.url, 1, a, b));
      }
      if (sort === "urlDesc") {
        return list.sort((a, b) => compareText(a == null ? void 0 : a.url, b == null ? void 0 : b.url, -1, a, b));
      }
      if (sort === "emailAsc") {
        return list.sort((a, b) => compareText(a == null ? void 0 : a.email, b == null ? void 0 : b.email, 1, a, b));
      }
      if (sort === "emailDesc") {
        return list.sort((a, b) => compareText(a == null ? void 0 : a.email, b == null ? void 0 : b.email, -1, a, b));
      }
      if (sort === "phoneAsc") {
        return list.sort((a, b) => compareText(a == null ? void 0 : a.phone, b == null ? void 0 : b.phone, 1, a, b));
      }
      if (sort === "phoneDesc") {
        return list.sort((a, b) => compareText(a == null ? void 0 : a.phone, b == null ? void 0 : b.phone, -1, a, b));
      }
      if (sort === "statusAsc") {
        return list.sort((a, b) => compareText(a == null ? void 0 : a.status, b == null ? void 0 : b.status, 1, a, b));
      }
      if (sort === "statusDesc") {
        return list.sort((a, b) => compareText(a == null ? void 0 : a.status, b == null ? void 0 : b.status, -1, a, b));
      }
      if (sort === "statusDraft") {
        return filterBy(list, (shop) => (shop == null ? void 0 : shop.status) === "draft");
      }
      if (sort === "statusPublished") {
        return filterBy(list, (shop) => (shop == null ? void 0 : shop.status) === "published");
      }
      if (sort === "statusArchived") {
        return filterBy(list, (shop) => (shop == null ? void 0 : shop.status) === "archived");
      }
      if (sort === "publishedAtAsc") {
        return list.sort((a, b) => compareDate(
          a == null ? void 0 : a.published_at,
          b == null ? void 0 : b.published_at,
          1,
          a,
          b
        ));
      }
      if (sort === "publishedAtDesc") {
        return list.sort((a, b) => compareDate(
          a == null ? void 0 : a.published_at,
          b == null ? void 0 : b.published_at,
          -1,
          a,
          b
        ));
      }
      if (sort === "showFromAtAsc") {
        return list.sort((a, b) => compareDate(
          a == null ? void 0 : a.show_from_at,
          b == null ? void 0 : b.show_from_at,
          1,
          a,
          b
        ));
      }
      if (sort === "showFromAtDesc") {
        return list.sort((a, b) => compareDate(
          a == null ? void 0 : a.show_from_at,
          b == null ? void 0 : b.show_from_at,
          -1,
          a,
          b
        ));
      }
      if (sort === "showToAtAsc") {
        return list.sort((a, b) => compareDate(
          a == null ? void 0 : a.show_to_at,
          b == null ? void 0 : b.show_to_at,
          1,
          a,
          b
        ));
      }
      if (sort === "showToAtDesc") {
        return list.sort((a, b) => compareDate(
          a == null ? void 0 : a.show_to_at,
          b == null ? void 0 : b.show_to_at,
          -1,
          a,
          b
        ));
      }
      if (sort === "createdAtAsc" || sort === "dateAsc") {
        return list.sort((a, b) => compareDate(
          a == null ? void 0 : a.created_at,
          b == null ? void 0 : b.created_at,
          1,
          a,
          b
        ));
      }
      if (sort === "createdAtDesc" || sort === "dateDesc") {
        return list.sort((a, b) => compareDate(
          a == null ? void 0 : a.created_at,
          b == null ? void 0 : b.created_at,
          -1,
          a,
          b
        ));
      }
      if (sort === "updatedAtAsc") {
        return list.sort((a, b) => compareDate(
          a == null ? void 0 : a.updated_at,
          b == null ? void 0 : b.updated_at,
          1,
          a,
          b
        ));
      }
      if (sort === "updatedAtDesc") {
        return list.sort((a, b) => compareDate(
          a == null ? void 0 : a.updated_at,
          b == null ? void 0 : b.updated_at,
          -1,
          a,
          b
        ));
      }
      if (sort === "viewsAsc") {
        return list.sort((a, b) => compareNumber(a == null ? void 0 : a.views, b == null ? void 0 : b.views, 1, a, b));
      }
      if (sort === "viewsDesc" || sort === "views") {
        return list.sort((a, b) => compareNumber(a == null ? void 0 : a.views, b == null ? void 0 : b.views, -1, a, b));
      }
      if (sort === "imagesAsc") {
        return list.sort((a, b) => compareNumber(
          a == null ? void 0 : a.images_count,
          b == null ? void 0 : b.images_count,
          1,
          a,
          b
        ));
      }
      if (sort === "imagesDesc") {
        return list.sort((a, b) => compareNumber(
          a == null ? void 0 : a.images_count,
          b == null ? void 0 : b.images_count,
          -1,
          a,
          b
        ));
      }
      if (sort === "activityAsc") {
        return list.sort((a, b) => compareNumber(a == null ? void 0 : a.activity, b == null ? void 0 : b.activity, 1, a, b));
      }
      if (sort === "activityDesc") {
        return list.sort((a, b) => compareNumber(a == null ? void 0 : a.activity, b == null ? void 0 : b.activity, -1, a, b));
      }
      if (sort === "activity") {
        return filterBy(list, (shop) => Boolean(shop == null ? void 0 : shop.activity));
      }
      if (sort === "inactive") {
        return filterBy(list, (shop) => !(shop == null ? void 0 : shop.activity));
      }
      if (sort === "leftAsc") {
        return list.sort((a, b) => compareNumber(a == null ? void 0 : a.left, b == null ? void 0 : b.left, 1, a, b));
      }
      if (sort === "leftDesc") {
        return list.sort((a, b) => compareNumber(a == null ? void 0 : a.left, b == null ? void 0 : b.left, -1, a, b));
      }
      if (sort === "left") {
        return filterBy(list, (shop) => Boolean(shop == null ? void 0 : shop.left));
      }
      if (sort === "noLeft") {
        return filterBy(list, (shop) => !(shop == null ? void 0 : shop.left));
      }
      if (sort === "mainAsc") {
        return list.sort((a, b) => compareNumber(a == null ? void 0 : a.main, b == null ? void 0 : b.main, 1, a, b));
      }
      if (sort === "mainDesc") {
        return list.sort((a, b) => compareNumber(a == null ? void 0 : a.main, b == null ? void 0 : b.main, -1, a, b));
      }
      if (sort === "main") {
        return filterBy(list, (shop) => Boolean(shop == null ? void 0 : shop.main));
      }
      if (sort === "noMain") {
        return filterBy(list, (shop) => !(shop == null ? void 0 : shop.main));
      }
      if (sort === "rightAsc") {
        return list.sort((a, b) => compareNumber(a == null ? void 0 : a.right, b == null ? void 0 : b.right, 1, a, b));
      }
      if (sort === "rightDesc") {
        return list.sort((a, b) => compareNumber(a == null ? void 0 : a.right, b == null ? void 0 : b.right, -1, a, b));
      }
      if (sort === "right") {
        return filterBy(list, (shop) => Boolean(shop == null ? void 0 : shop.right));
      }
      if (sort === "noRight") {
        return filterBy(list, (shop) => !(shop == null ? void 0 : shop.right));
      }
      if (sort === "moderationStatusAsc") {
        return list.sort((a, b) => compareNumber(
          moderationNum(a == null ? void 0 : a.moderation_status),
          moderationNum(b == null ? void 0 : b.moderation_status),
          1,
          a,
          b
        ));
      }
      if (sort === "moderationStatusDesc") {
        return list.sort((a, b) => compareNumber(
          moderationNum(a == null ? void 0 : a.moderation_status),
          moderationNum(b == null ? void 0 : b.moderation_status),
          -1,
          a,
          b
        ));
      }
      if (sort === "moderationPending") {
        return filterBy(
          list,
          (shop) => moderationNum(shop == null ? void 0 : shop.moderation_status) === 0
        );
      }
      if (sort === "moderationApproved") {
        return filterBy(
          list,
          (shop) => moderationNum(shop == null ? void 0 : shop.moderation_status) === 1
        );
      }
      if (sort === "moderationRejected") {
        return filterBy(
          list,
          (shop) => moderationNum(shop == null ? void 0 : shop.moderation_status) === 2
        );
      }
      if (sort === "ownerNameAsc") {
        return list.sort((a, b) => {
          var _a, _b;
          return compareText(
            (_a = a == null ? void 0 : a.owner) == null ? void 0 : _a.name,
            (_b = b == null ? void 0 : b.owner) == null ? void 0 : _b.name,
            1,
            a,
            b
          );
        });
      }
      if (sort === "ownerNameDesc") {
        return list.sort((a, b) => {
          var _a, _b;
          return compareText(
            (_a = a == null ? void 0 : a.owner) == null ? void 0 : _a.name,
            (_b = b == null ? void 0 : b.owner) == null ? void 0 : _b.name,
            -1,
            a,
            b
          );
        });
      }
      if (sort === "ownerEmailAsc") {
        return list.sort((a, b) => {
          var _a, _b;
          return compareText(
            (_a = a == null ? void 0 : a.owner) == null ? void 0 : _a.email,
            (_b = b == null ? void 0 : b.owner) == null ? void 0 : _b.email,
            1,
            a,
            b
          );
        });
      }
      if (sort === "ownerEmailDesc") {
        return list.sort((a, b) => {
          var _a, _b;
          return compareText(
            (_a = a == null ? void 0 : a.owner) == null ? void 0 : _a.email,
            (_b = b == null ? void 0 : b.owner) == null ? void 0 : _b.email,
            -1,
            a,
            b
          );
        });
      }
      if (sort === "companyLegalNameAsc") {
        return list.sort((a, b) => {
          var _a, _b;
          return compareText(
            (_a = a == null ? void 0 : a.company) == null ? void 0 : _a.legal_name,
            (_b = b == null ? void 0 : b.company) == null ? void 0 : _b.legal_name,
            1,
            a,
            b
          );
        });
      }
      if (sort === "companyLegalNameDesc") {
        return list.sort((a, b) => {
          var _a, _b;
          return compareText(
            (_a = a == null ? void 0 : a.company) == null ? void 0 : _a.legal_name,
            (_b = b == null ? void 0 : b.company) == null ? void 0 : _b.legal_name,
            -1,
            a,
            b
          );
        });
      }
      return list;
    };
    const filteredShops = computed(() => {
      let filtered = localShops.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortShops(filtered);
      }
      filtered = filtered.filter((shop) => {
        var _a, _b;
        const translation = getShopTranslation(shop);
        const company = (shop == null ? void 0 : shop.company) || {};
        const companyTranslationTitle = getCompanyTranslationTitle(shop);
        const searchableValues = [
          shop == null ? void 0 : shop.url,
          shop == null ? void 0 : shop.email,
          shop == null ? void 0 : shop.phone,
          shop == null ? void 0 : shop.status,
          shop == null ? void 0 : shop.moderation_note,
          translation == null ? void 0 : translation.title,
          translation == null ? void 0 : translation.subtitle,
          translation == null ? void 0 : translation.short,
          translation == null ? void 0 : translation.description,
          (_a = shop == null ? void 0 : shop.owner) == null ? void 0 : _a.name,
          (_b = shop == null ? void 0 : shop.owner) == null ? void 0 : _b.email,
          company == null ? void 0 : company.url,
          company == null ? void 0 : company.legal_name,
          company == null ? void 0 : company.email,
          company == null ? void 0 : company.phone,
          companyTranslationTitle
        ];
        return searchableValues.some((value) => normalize(value).includes(query));
      });
      return sortShops(filtered);
    });
    const paginatedShops = computed(() => {
      const perPage = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * perPage;
      return filteredShops.value.slice(start, start + perPage);
    });
    const displayedShops = computed(() => {
      return props.useServerProcessing ? shopsList.value : paginatedShops.value;
    });
    watch([itemsPerPage, searchQuery, sortParam], () => {
      currentPage.value = 1;
    });
    const selectedShops = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = Boolean(
        (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false
      );
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedShops.value.map((shop) => shop.id);
      if (checked) {
        selectedShops.value = [
          .../* @__PURE__ */ new Set([...selectedShops.value, ...ids])
        ];
      } else {
        selectedShops.value = selectedShops.value.filter(
          (id) => !ids.includes(id)
        );
      }
    };
    const toggleSelectShop = (shopId) => {
      const index = selectedShops.value.indexOf(shopId);
      if (index > -1) {
        selectedShops.value.splice(index, 1);
      } else {
        selectedShops.value.push(shopId);
      }
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedShops.value.length) {
        toast.warning("Выберите магазины для активации/деактивации");
        return;
      }
      const idsToUpdate = [...selectedShops.value];
      const activity = Boolean(newActivity);
      router.put(
        route("admin.actions.marketShops.bulkUpdateActivity"),
        { ids: idsToUpdate, activity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localShops.value = localShops.value.map((shop) => {
              if (idsToUpdate.includes(shop.id)) {
                return { ...shop, activity };
              }
              return shop;
            });
            selectedShops.value = [];
            toast.success("Активность магазинов массово обновлена");
          },
          onError: (errors) => {
            const msg = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности";
            toast.error(msg);
          }
        }
      );
    };
    const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
      if (!selectedShops.value.length) {
        toast.warning("Выберите магазины для массового действия");
        return;
      }
      const idsToUpdate = [...selectedShops.value];
      const value = Boolean(newValue);
      router.put(
        route(routeName),
        { ids: idsToUpdate, [field]: value },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localShops.value = localShops.value.map((shop) => {
              if (idsToUpdate.includes(shop.id)) {
                return { ...shop, [field]: value };
              }
              return shop;
            });
            selectedShops.value = [];
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
      if (!selectedShops.value.length) {
        toast.warning("Выберите хотя бы один магазин для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные магазины?")) return;
      router.delete(route("admin.actions.marketShops.bulkDestroy"), {
        data: { ids: selectedShops.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          selectedShops.value = [];
          toast.success("Массовое удаление магазинов успешно завершено.");
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors)[0];
          toast.error(
            errors[errorKey] || "Произошла ошибка при удалении магазинов."
          );
        }
      });
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") {
        toggleAll({ checked: true });
      } else if (action === "deselectAll") {
        toggleAll({ checked: false });
      } else if (action === "activate") {
        bulkToggleActivity(true);
      } else if (action === "deactivate") {
        bulkToggleActivity(false);
      } else if (action === "left") {
        bulkToggleFlag(
          "left",
          true,
          "admin.actions.marketShops.bulkUpdateLeft",
          "Магазины добавлены в левую колонку"
        );
      } else if (action === "noLeft") {
        bulkToggleFlag(
          "left",
          false,
          "admin.actions.marketShops.bulkUpdateLeft",
          "Магазины убраны из левой колонки"
        );
      } else if (action === "main") {
        bulkToggleFlag(
          "main",
          true,
          "admin.actions.marketShops.bulkUpdateMain",
          "Магазины добавлены в главный блок"
        );
      } else if (action === "noMain") {
        bulkToggleFlag(
          "main",
          false,
          "admin.actions.marketShops.bulkUpdateMain",
          "Магазины убраны из главного блока"
        );
      } else if (action === "right") {
        bulkToggleFlag(
          "right",
          true,
          "admin.actions.marketShops.bulkUpdateRight",
          "Магазины добавлены в правую колонку"
        );
      } else if (action === "noRight") {
        bulkToggleFlag(
          "right",
          false,
          "admin.actions.marketShops.bulkUpdateRight",
          "Магазины убраны из правой колонки"
        );
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const approveShop = (shop, status = 1, note = "") => {
      if (!(shop == null ? void 0 : shop.id)) return;
      const moderationStatus = moderationNum(status);
      router.put(
        route("admin.actions.marketShops.approve", { marketShop: shop.id }),
        {
          moderation_status: moderationStatus,
          moderation_note: note
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalShop(shop.id, (node) => {
              node.moderation_status = moderationStatus;
              node.is_pending = moderationStatus === 0;
              node.is_approved = moderationStatus === 1;
              node.is_rejected = moderationStatus === 2;
              node.moderation_note = note;
            });
            toast.success(
              moderationStatus === 1 ? "Магазин одобрен" : "Магазин отклонён"
            );
          },
          onError: () => toast.error("Ошибка модерации магазина")
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
        route("admin.actions.marketShops.updateSortBulk"),
        { items },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            items.forEach((item) => {
              patchLocalShop(item.id, (shop) => {
                shop.sort = item.sort;
              });
            });
            toast.success("Сортировка магазинов обновлена");
          },
          onError: (errors) => {
            console.error("Ошибка сортировки магазинов:", errors);
            toast.error(errors.message || "Ошибка обновления сортировки");
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("marketShops")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("marketShops"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("marketShops")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("marketShops")), 1)
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
              href: _ctx.route("admin.marketShops.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addMarketShop"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addMarketShop")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              "setting-key": "adminMarketShopsProcessingMode",
              mode: __props.adminMarketShopsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.shopsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.shopsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$e, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.shopsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$f, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.shopsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountMarketShops"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$3, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (value) => sortParam.value = value
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.shopsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$i, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.shopsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.shopsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              if (__props.shopsCount) {
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
            if (__props.shopsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredShops.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.shops }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                shops: displayedShops.value,
                "selected-shops": selectedShops.value,
                "is-admin": isAdmin.value,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectShop,
                onToggleAll: toggleAll,
                onApprove: approveShop
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                shops: displayedShops.value,
                "selected-shops": selectedShops.value,
                "is-admin": isAdmin.value,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectShop,
                onToggleAll: toggleAll,
                onApprove: approveShop
              }, null, _parent2, _scopeId));
            }
            if (__props.shopsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredShops.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.shops }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$m, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteShop,
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
                      href: _ctx.route("admin.marketShops.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addMarketShop")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$d, {
                      "setting-key": "adminMarketShopsProcessingMode",
                      mode: __props.adminMarketShopsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.shopsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.shopsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.shopsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$f, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.shopsCount ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountMarketShops"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (value) => sortParam.value = value
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.shopsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$i, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.shopsCount), 1)
                      ]),
                      _: 1
                    }),
                    __props.shopsCount ? (openBlock(), createBlock(_sfc_main$4, {
                      key: 0,
                      onChange: handleBulkAction
                    })) : createCommentVNode("", true),
                    createVNode(_sfc_main$j, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.shopsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredShops.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.shops
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    shops: displayedShops.value,
                    "selected-shops": selectedShops.value,
                    "is-admin": isAdmin.value,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectShop,
                    onToggleAll: toggleAll,
                    onApprove: approveShop
                  }, null, 8, ["shops", "selected-shops", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    shops: displayedShops.value,
                    "selected-shops": selectedShops.value,
                    "is-admin": isAdmin.value,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectShop,
                    onToggleAll: toggleAll,
                    onApprove: approveShop
                  }, null, 8, ["shops", "selected-shops", "is-admin"])),
                  __props.shopsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredShops.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.shops
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$m, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteShop,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketShops/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
