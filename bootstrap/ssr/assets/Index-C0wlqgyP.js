import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, withModifiers, withDirectives, vShow, createTextVNode, computed } from "vue";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketBrand/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>─────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>─────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option value="urlAsc">URL A→Z</option><option value="urlDesc">URL Z→A</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="leftDesc">${ssrInterpolate(unref(t)("left"))} ON→OFF</option><option value="leftAsc">${ssrInterpolate(unref(t)("left"))} OFF→ON</option><option value="left">${ssrInterpolate(unref(t)("left"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("notLeft"))}</option><option disabled>─────────────────</option><option value="mainDesc">${ssrInterpolate(unref(t)("main"))} ON→OFF</option><option value="mainAsc">${ssrInterpolate(unref(t)("main"))} OFF→ON</option><option value="main">${ssrInterpolate(unref(t)("main"))}</option><option value="noMain">${ssrInterpolate(unref(t)("notMain"))}</option><option disabled>─────────────────</option><option value="rightDesc">${ssrInterpolate(unref(t)("right"))} ON→OFF</option><option value="rightAsc">${ssrInterpolate(unref(t)("right"))} OFF→ON</option><option value="right">${ssrInterpolate(unref(t)("right"))}</option><option value="noRight">${ssrInterpolate(unref(t)("notRight"))}</option><option disabled>─────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option disabled>─────────────────</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>─────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="statusDraft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="statusPublished">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="statusArchived">${ssrInterpolate(unref(t)("statusArchived"))}</option><option disabled>─────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="showFromAtDesc">${ssrInterpolate(unref(t)("showFromAt"))} ↓</option><option value="showFromAtAsc">${ssrInterpolate(unref(t)("showFromAt"))} ↑</option><option value="showToAtDesc">${ssrInterpolate(unref(t)("showToAt"))} ↓</option><option value="showToAtAsc">${ssrInterpolate(unref(t)("showToAt"))} ↑</option><option disabled>─────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>─────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option><option disabled>─────────────────</option><option value="websiteAsc">${ssrInterpolate(unref(t)("site"))} A→Z</option><option value="websiteDesc">${ssrInterpolate(unref(t)("site"))} Z→A</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketBrand/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "BrandTable",
  __ssrInlineRender: true,
  props: {
    brands: { type: Array, default: () => [] },
    selectedBrands: { type: Array, default: () => [] },
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
    const localBrands = ref([]);
    watch(
      () => props.brands,
      (newVal) => {
        localBrands.value = JSON.parse(
          JSON.stringify(newVal || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      emits(
        "update-sort-order",
        localBrands.value.map((brand) => brand.id)
      );
    };
    const allSelected = () => {
      return Boolean(
        localBrands.value.length && localBrands.value.every(
          (brand) => props.selectedBrands.includes(
            brand.id
          )
        )
      );
    };
    const brandTranslation = (brand) => (brand == null ? void 0 : brand.translation) || {};
    const brandTitle = (brand) => {
      var _a;
      return ((_a = brandTranslation(brand)) == null ? void 0 : _a.title) || `ID: ${brand == null ? void 0 : brand.id}`;
    };
    const brandShort = (brand) => {
      var _a;
      return ((_a = brandTranslation(brand)) == null ? void 0 : _a.short) || "";
    };
    const statusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const getStatusLabel = (status) => t(
      statusLabelKeyMap[status] || status || "no"
    );
    const ownerTitle = (brand) => {
      const owner = brand == null ? void 0 : brand.owner;
      if (!owner) {
        return t("noData");
      }
      return [
        owner.name || "",
        owner.email || ""
      ].filter(Boolean).join(" — ");
    };
    const ownerAvatar = (brand) => {
      var _a;
      return ((_a = brand == null ? void 0 : brand.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const logoUrl = (brand) => {
      const logo = brand == null ? void 0 : brand.logo;
      if (!logo) {
        return "/storage/market/market_brands/logos/default-image-light.png";
      }
      const value = String(logo);
      if (value.startsWith("http://") || value.startsWith("https://") || value.startsWith("/storage/")) {
        return value;
      }
      return `/storage/${value}`;
    };
    const getPrimaryImage = (brand) => {
      const images = Array.isArray(
        brand == null ? void 0 : brand.images
      ) ? brand.images : [];
      if (!images.length) {
        return null;
      }
      return [...images].sort(
        (a, b) => Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0)
      )[0];
    };
    const imageUrl = (brand) => {
      const image = getPrimaryImage(brand);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/market/market_brand_images/default-image.png";
    };
    const imageAlt = (brand) => {
      const image = getPrimaryImage(brand);
      return (image == null ? void 0 : image.alt) || brandTitle(brand);
    };
    const imageTitle = (brand) => {
      const image = getPrimaryImage(brand);
      return (image == null ? void 0 : image.caption) || brandTitle(brand);
    };
    const brandPublicUrl = (brand) => {
      if (!(brand == null ? void 0 : brand.url)) {
        return "#";
      }
      return `/market/brands/${encodeURIComponent(
        String(brand.url)
      )}`;
    };
    const moderationBadge = (status) => {
      const value = Number(
        status ?? 0
      );
      if (value === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (value === 2) {
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
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(
        date.getTime()
      )) {
        return "";
      }
      return date.toLocaleDateString(
        locale.value || void 0,
        {
          year: "numeric",
          month: "long",
          day: "numeric"
        }
      );
    };
    const truncateText = (text, maxLength = 50) => {
      if (!text) {
        return "";
      }
      const value = String(text);
      return value.length > maxLength ? value.slice(0, maxLength).trimEnd() + "…" : value;
    };
    const toggleSelect = (brandId) => {
      emits(
        "toggle-select",
        brandId
      );
    };
    const emitToggleLeft = (brand) => {
      emits(
        "toggle-left",
        brand
      );
    };
    const emitToggleMain = (brand) => {
      emits(
        "toggle-main",
        brand
      );
    };
    const emitToggleRight = (brand) => {
      emits(
        "toggle-right",
        brand
      );
    };
    const emitToggleActivity = (brand) => {
      emits(
        "toggle-activity",
        brand
      );
    };
    const emitDelete = (brand) => {
      emits(
        "delete",
        brand
      );
    };
    const emitApprove = (brand, status, note) => {
      emits(
        "approve",
        brand,
        status,
        note
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedBrands.length)}</div>`);
      if (localBrands.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localBrands.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-1 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("owner"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24"><path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path><path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path><path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path><path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path><path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"></path></svg></div></th><th class="px-1 py-3"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-1 py-3"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("logo"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M274.835 12.646l25.516 62.393c4.213 10.301 16.671 14.349 26.134 8.492l57.316-35.479c15.49-9.588 34.808 4.447 30.475 22.142l-16.03 65.475c-2.647 10.81 5.053 21.408 16.152 22.231l67.224 4.987c18.167 1.348 25.546 24.057 11.641 35.826L441.81 242.26c-8.495 7.19-8.495 20.289 0 27.479l51.454 43.548c13.906 11.769 6.527 34.478-11.641 35.826l-67.224 4.987c-11.099.823-18.799 11.421-16.152 22.231l16.03 65.475c4.332 17.695-14.986 31.73-30.475 22.142l-57.316-35.479c-9.463-5.858-21.922-1.81-26.134 8.492l-25.516 62.393c-6.896 16.862-30.774 16.862-37.67 0l-25.516-62.393c-4.213-10.301-16.671-14.349-26.134-8.492l-57.317 35.479c-15.49 9.588-34.808-4.447-30.475-22.142l16.03-65.475c2.647-10.81-5.053-21.408-16.152-22.231l-67.224-4.987c-18.167-1.348-25.546-24.057-11.641-35.826L70.19 269.74c8.495-7.19 8.495-20.289 0-27.479l-51.454-43.548c-13.906-11.769-6.527-34.478 11.641-35.826l67.224-4.987c11.099-.823 18.799-11.421 16.152-22.231l-16.03-65.475c-4.332-17.695 14.986-31.73 30.475-22.142l57.317 35.479c9.463 5.858 21.921 1.81 26.134-8.492l25.516-62.393c6.896-16.861 30.774-16.861 37.67 0zM392 256c0-74.991-61.01-136-136-136-74.991 0-136 61.009-136 136s61.009 136 136 136c74.99 0 136-61.009 136-136zm-32 0c0 57.346-46.654 104-104 104s-104-46.654-104-104 46.654-104 104-104 104 46.654 104 104z"></path></svg></div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("brand"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("site"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("show"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-1 py-1 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localBrands.value,
          "onUpdate:modelValue": ($event) => localBrands.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: brand }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-1 py-1 text-center cursor-move handle w-px"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-1 py-1 whitespace-nowrap w-px"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${ssrRenderAttr("title", `[${brand.sort}] / ${formatDate(brand.published_at)}`)}${_scopeId}>${ssrInterpolate(brand.id)}</div></td><td class="px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(brand))}${ssrRenderAttr("title", ownerTitle(brand))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("owner"))}${_scopeId}></div></td><td class="px-1 py-1 w-12"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", imageUrl(brand))}${ssrRenderAttr("alt", imageAlt(brand))}${ssrRenderAttr("title", imageTitle(brand))} class="h-8 w-12 object-cover rounded-sm border border-slate-300 dark:border-slate-600"${_scopeId}></div></td><td class="px-1 py-1 w-12"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", logoUrl(brand))}${ssrRenderAttr("alt", brandTitle(brand))}${ssrRenderAttr("title", brandTitle(brand))} class="h-8 w-12 object-cover rounded-sm border border-slate-300 dark:border-slate-600"${_scopeId}></div></td><td class="px-1 py-1"${_scopeId}><div class="text-left"${_scopeId}><a${ssrRenderAttr("href", brandPublicUrl(brand))} class="text-blue-700 dark:text-blue-300 text-sm hover:underline hover:text-amber-700 dark:hover:text-amber-200" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", brand.show_from_at ? `${unref(t)("show")}: ${brand.show_from_at} / ${brand.show_to_at}` : formatDate(brand.published_at))}${_scopeId}>${ssrInterpolate(truncateText(brandTitle(brand), 70))}</a><div class="text-[11px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(brand.url, 70))}</div><div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(getStatusLabel(brand.status))}</div>`);
              if (brandShort(brand)) {
                _push2(`<div class="text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(brandShort(brand), 80))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-left text-xs"${_scopeId}>`);
              if (brand.website) {
                _push2(`<a${ssrRenderAttr("href", brand.website)} target="_blank" rel="noopener noreferrer" class="text-amber-700 dark:text-amber-300 hover:underline"${_scopeId}>${ssrInterpolate(truncateText(brand.website, 45))}</a>`);
              } else {
                _push2(`<span class="text-slate-400 dark:text-slate-300"${_scopeId}> — </span>`);
              }
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-300"${_scopeId}>${ssrInterpolate(brand.views ?? 0)}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: Boolean(brand.left),
                title: brand.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => emitToggleLeft(brand)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: Boolean(brand.main),
                title: brand.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => emitToggleMain(brand)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: Boolean(brand.right),
                title: brand.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => emitToggleRight(brand)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(brand.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", brand.moderation_note && brand.moderated_at ? `${brand.moderation_note} [${formatDate(brand.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(brand.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isAdmin: __props.isAdmin,
                status: (brand == null ? void 0 : brand.moderation_status) ?? 0,
                initialNote: (brand == null ? void 0 : brand.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emitApprove(brand, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: Boolean(brand.activity),
                title: brand.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emitToggleActivity(brand)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route(
                  "admin.marketBrands.edit",
                  { marketBrand: brand.id }
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => emitDelete(brand)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedBrands.includes(brand.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                      title: `[${brand.sort}] / ${formatDate(brand.published_at)}`
                    }, toDisplayString(brand.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(brand),
                        title: ownerTitle(brand),
                        class: "h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("owner")
                      }, null, 8, ["src", "title", "alt"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 w-12" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: imageUrl(brand),
                        alt: imageAlt(brand),
                        title: imageTitle(brand),
                        class: "h-8 w-12 object-cover rounded-sm border border-slate-300 dark:border-slate-600"
                      }, null, 8, ["src", "alt", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 w-12" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: logoUrl(brand),
                        alt: brandTitle(brand),
                        title: brandTitle(brand),
                        class: "h-8 w-12 object-cover rounded-sm border border-slate-300 dark:border-slate-600"
                      }, null, 8, ["src", "alt", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "text-left" }, [
                      createVNode("a", {
                        href: brandPublicUrl(brand),
                        class: "text-blue-700 dark:text-blue-300 text-sm hover:underline hover:text-amber-700 dark:hover:text-amber-200",
                        target: "_blank",
                        rel: "noopener noreferrer",
                        title: brand.show_from_at ? `${unref(t)("show")}: ${brand.show_from_at} / ${brand.show_to_at}` : formatDate(brand.published_at)
                      }, toDisplayString(truncateText(brandTitle(brand), 70)), 9, ["href", "title"]),
                      createVNode("div", { class: "text-[11px] text-slate-500 dark:text-slate-300" }, toDisplayString(truncateText(brand.url, 70)), 1),
                      createVNode("div", { class: "text-[10px] text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(getStatusLabel(brand.status)), 1),
                      brandShort(brand) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(truncateText(brandShort(brand), 80)), 1)) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-left text-xs" }, [
                      brand.website ? (openBlock(), createBlock("a", {
                        key: 0,
                        href: brand.website,
                        target: "_blank",
                        rel: "noopener noreferrer",
                        class: "text-amber-700 dark:text-amber-300 hover:underline"
                      }, toDisplayString(truncateText(brand.website, 45)), 9, ["href"])) : (openBlock(), createBlock("span", {
                        key: 1,
                        class: "text-slate-400 dark:text-slate-300"
                      }, " — "))
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-blue-600 dark:text-blue-300" }, toDisplayString(brand.views ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: Boolean(brand.left),
                        title: brand.left ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleLeft: ($event) => emitToggleLeft(brand)
                      }, null, 8, ["isActive", "title", "onToggleLeft"]),
                      createVNode(_sfc_main$6, {
                        isActive: Boolean(brand.main),
                        title: brand.main ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleMain: ($event) => emitToggleMain(brand)
                      }, null, 8, ["isActive", "title", "onToggleMain"]),
                      createVNode(_sfc_main$7, {
                        isActive: Boolean(brand.right),
                        title: brand.right ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleRight: ($event) => emitToggleRight(brand)
                      }, null, 8, ["isActive", "title", "onToggleRight"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(brand.moderation_status).class],
                        title: brand.moderation_note && brand.moderated_at ? `${brand.moderation_note} [${formatDate(brand.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(brand.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$8, {
                        isAdmin: __props.isAdmin,
                        status: (brand == null ? void 0 : brand.moderation_status) ?? 0,
                        initialNote: (brand == null ? void 0 : brand.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => emitApprove(brand, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode(_sfc_main$9, {
                        isActive: Boolean(brand.activity),
                        title: brand.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emitToggleActivity(brand)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route(
                          "admin.marketBrands.edit",
                          { marketBrand: brand.id }
                        )
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => emitDelete(brand)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedBrands.includes(brand.id),
                        onChange: ($event) => toggleSelect(brand.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketBrand/Table/BrandTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "BrandCardGrid",
  __ssrInlineRender: true,
  props: {
    brands: { type: Array, default: () => [] },
    selectedBrands: { type: Array, default: () => [] },
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
    const localBrands = ref([]);
    const openedOwnerBlocks = ref([]);
    watch(
      () => props.brands,
      (newVal) => {
        localBrands.value = JSON.parse(
          JSON.stringify(newVal || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      emits(
        "update-sort-order",
        localBrands.value.map(
          (brand) => brand.id
        )
      );
    };
    const allSelected = () => {
      return Boolean(
        localBrands.value.length && localBrands.value.every(
          (brand) => props.selectedBrands.includes(
            brand.id
          )
        )
      );
    };
    const brandTranslation = (brand) => (brand == null ? void 0 : brand.translation) || {};
    const brandTitle = (brand) => {
      var _a;
      return ((_a = brandTranslation(brand)) == null ? void 0 : _a.title) || `ID: ${brand == null ? void 0 : brand.id}`;
    };
    const brandShort = (brand) => {
      var _a;
      return ((_a = brandTranslation(brand)) == null ? void 0 : _a.short) || "";
    };
    const statusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const getStatusLabel = (status) => t(
      statusLabelKeyMap[status] || status || "no"
    );
    const ownerName = (brand) => {
      var _a;
      return ((_a = brand == null ? void 0 : brand.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (brand) => {
      var _a;
      return ((_a = brand == null ? void 0 : brand.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (brand) => {
      const owner = brand == null ? void 0 : brand.owner;
      if (!owner) {
        return t("noData");
      }
      return [
        owner.name || "",
        owner.email || ""
      ].filter(Boolean).join(" — ");
    };
    const ownerAvatar = (brand) => {
      var _a;
      return ((_a = brand == null ? void 0 : brand.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const logoUrl = (brand) => {
      const logo = brand == null ? void 0 : brand.logo;
      if (!logo) {
        return "/storage/market/market_brands/logos/default-image-light.png";
      }
      const value = String(logo);
      if (value.startsWith("http://") || value.startsWith("https://") || value.startsWith("/storage/")) {
        return value;
      }
      return `/storage/${value}`;
    };
    const getPrimaryImage = (brand) => {
      const images = Array.isArray(
        brand == null ? void 0 : brand.images
      ) ? brand.images : [];
      if (!images.length) {
        return null;
      }
      return [...images].sort(
        (a, b) => Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0)
      )[0];
    };
    const imageUrl = (brand) => {
      const image = getPrimaryImage(brand);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/market/market_brand_images/default-image.png";
    };
    const imageAlt = (brand) => {
      const image = getPrimaryImage(brand);
      return (image == null ? void 0 : image.alt) || brandTitle(brand);
    };
    const imageTitle = (brand) => {
      const image = getPrimaryImage(brand);
      return (image == null ? void 0 : image.caption) || brandTitle(brand);
    };
    const brandPublicUrl = (brand) => {
      if (!(brand == null ? void 0 : brand.url)) {
        return "#";
      }
      return `/market/brands/${encodeURIComponent(
        String(brand.url)
      )}`;
    };
    const formatDate = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(
        date.getTime()
      )) {
        return "";
      }
      return date.toLocaleDateString(
        locale.value || void 0,
        {
          year: "numeric",
          month: "long",
          day: "numeric"
        }
      );
    };
    const truncateText = (text, maxLength = 80) => {
      if (!text) {
        return "";
      }
      const value = String(text);
      return value.length > maxLength ? value.slice(0, maxLength).trimEnd() + "…" : value;
    };
    const moderationBadge = (status) => {
      const value = Number(
        status ?? 0
      );
      if (value === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (value === 2) {
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
    const isOwnerBlockOpen = (brandId) => openedOwnerBlocks.value.includes(
      brandId
    );
    const toggleOwnerBlock = (brandId) => {
      if (isOwnerBlockOpen(
        brandId
      )) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
          (id) => id !== brandId
        );
        return;
      }
      openedOwnerBlocks.value.push(
        brandId
      );
    };
    const toggleSelect = (brandId) => {
      emits(
        "toggle-select",
        brandId
      );
    };
    const emitToggleLeft = (brand) => {
      emits(
        "toggle-left",
        brand
      );
    };
    const emitToggleMain = (brand) => {
      emits(
        "toggle-main",
        brand
      );
    };
    const emitToggleRight = (brand) => {
      emits(
        "toggle-right",
        brand
      );
    };
    const emitToggleActivity = (brand) => {
      emits(
        "toggle-activity",
        brand
      );
    };
    const emitDelete = (brand) => {
      emits(
        "delete",
        brand
      );
    };
    const emitApprove = (brand, status, note) => {
      emits(
        "approve",
        brand,
        status,
        note
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedBrands.length)}</div>`);
      if (localBrands.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localBrands.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localBrands.value,
          "onUpdate:modelValue": ($event) => localBrands.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd,
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: brand }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${brand.sort}] / ${formatDate(brand.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(brand.id)}</div><button type="button" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"${ssrRenderAttr("title", isOwnerBlockOpen(brand.id) ? unref(t)("hideOwner") : unref(t)("showOwner"))}${_scopeId}><svg class="${ssrRenderClass([{
                "rotate-180": isOwnerBlockOpen(brand.id)
              }, "w-4 h-4 transition-transform duration-200"])}" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"${_scopeId}></path></svg></button></div><div class="flex items-center space-x-2"${_scopeId}><span class="${ssrRenderClass([moderationBadge(brand.moderation_status).class, "text-[10px] px-2 py-0.5 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", brand.moderation_note && brand.moderated_at ? `${brand.moderation_note} [${formatDate(brand.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(brand.moderation_status).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedBrands.includes(brand.id)) ? " checked" : ""}${_scopeId}></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-1"${_scopeId}><div style="${ssrRenderStyle(isOwnerBlockOpen(brand.id) ? null : { display: "none" })}" class="flex flex-col items-center justify-center text-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(brand))}${ssrRenderAttr("title", ownerTitle(brand))} class="h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("owner"))}${_scopeId}><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerName(brand))}${_scopeId}>${ssrInterpolate(ownerName(brand))}</div>`);
              if (ownerEmail(brand)) {
                _push2(`<div class="text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerEmail(brand))}${_scopeId}>${ssrInterpolate(ownerEmail(brand))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (brand.show_from_at) {
                _push2(`<div class="flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("show"))}: ${ssrInterpolate(brand.show_from_at)} / ${ssrInterpolate(brand.show_to_at)}</div>`);
              } else {
                _push2(`<div class="flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(formatDate(brand.published_at))}</div>`);
              }
              _push2(`</div><div class="relative w-full bg-slate-200 dark:bg-slate-900"${_scopeId}><img${ssrRenderAttr("src", imageUrl(brand))}${ssrRenderAttr("alt", imageAlt(brand))}${ssrRenderAttr("title", imageTitle(brand))} class="h-32 w-full object-cover"${_scopeId}></div><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", logoUrl(brand))}${ssrRenderAttr("alt", brandTitle(brand))}${ssrRenderAttr("title", brandTitle(brand))} class="h-12 w-20 object-cover rounded-sm border border-slate-300 dark:border-slate-600"${_scopeId}></div><a${ssrRenderAttr("href", brandPublicUrl(brand))} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold hover:underline text-blue-700 dark:text-blue-300 hover:text-amber-700 dark:hover:text-amber-200 line-clamp-2 text-center"${_scopeId}>${ssrInterpolate(truncateText(brandTitle(brand), 90))}</a><div class="text-center text-[11px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(brand.url, 90))}</div>`);
              if (brand.website) {
                _push2(`<a${ssrRenderAttr("href", brand.website)} target="_blank" rel="noopener noreferrer" class="text-center text-[11px] text-amber-700 dark:text-amber-300 hover:underline line-clamp-1"${ssrRenderAttr("title", brand.website)}${_scopeId}>${ssrInterpolate(truncateText(brand.website, 90))}</a>`);
              } else {
                _push2(`<!---->`);
              }
              if (brandShort(brand)) {
                _push2(`<div class="font-semibold text-[12px] text-center text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(truncateText(brandShort(brand), 120))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="flex items-center justify-center gap-3 text-[11px] font-semibold text-slate-600 dark:text-slate-200"${_scopeId}><div class="flex items-center justify-center space-x-1"${ssrRenderAttr("title", unref(t)("views"))}${_scopeId}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"${_scopeId}><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"${_scopeId}></path></svg><span class="text-[12px] text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(brand.views ?? 0)}</span></div><div class="flex items-center justify-center space-x-1"${ssrRenderAttr("title", unref(t)("images"))}${_scopeId}><span class="text-[11px]"${_scopeId}>${ssrInterpolate(unref(t)("images"))}: </span><span class="text-[12px] text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(brand.images_count ?? 0)}</span></div></div><div class="grid grid-cols-1 gap-0.5 text-[11px] text-slate-600 dark:text-slate-300"${_scopeId}><div class="font-semibold text-center text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("status"))}: </span> ${ssrInterpolate(getStatusLabel(brand.status))}</div></div><div class="flex justify-center space-x-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(brand.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", brand.moderation_note && brand.moderated_at ? `${brand.moderation_note} [${formatDate(brand.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(brand.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isAdmin: __props.isAdmin,
                status: (brand == null ? void 0 : brand.moderation_status) ?? 0,
                initialNote: (brand == null ? void 0 : brand.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emitApprove(brand, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="flex items-center justify-between px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: Boolean(brand.left),
                title: brand.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => emitToggleLeft(brand)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: Boolean(brand.main),
                title: brand.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => emitToggleMain(brand)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: Boolean(brand.right),
                title: brand.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => emitToggleRight(brand)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: Boolean(brand.activity),
                title: brand.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emitToggleActivity(brand)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route(
                  "admin.marketBrands.edit",
                  { marketBrand: brand.id }
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => emitDelete(brand)
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
                        title: `[${brand.sort}] / ${formatDate(brand.published_at)}`
                      }, " ID: " + toDisplayString(brand.id), 9, ["title"]),
                      createVNode("button", {
                        type: "button",
                        class: "text-slate-400 hover:text-blue-600 dark:hover:text-blue-300",
                        title: isOwnerBlockOpen(brand.id) ? unref(t)("hideOwner") : unref(t)("showOwner"),
                        onClick: withModifiers(($event) => toggleOwnerBlock(brand.id), ["prevent"])
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: ["w-4 h-4 transition-transform duration-200", {
                            "rotate-180": isOwnerBlockOpen(brand.id)
                          }],
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
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border font-semibold", moderationBadge(brand.moderation_status).class],
                        title: brand.moderation_note && brand.moderated_at ? `${brand.moderation_note} [${formatDate(brand.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(brand.moderation_status).text), 11, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedBrands.includes(brand.id),
                        onChange: ($event) => toggleSelect(brand.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-1" }, [
                    withDirectives(createVNode("div", { class: "flex flex-col items-center justify-center text-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(brand),
                        title: ownerTitle(brand),
                        class: "h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("owner")
                      }, null, 8, ["src", "title", "alt"]),
                      createVNode("div", {
                        class: "mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1",
                        title: ownerName(brand)
                      }, toDisplayString(ownerName(brand)), 9, ["title"]),
                      ownerEmail(brand) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1",
                        title: ownerEmail(brand)
                      }, toDisplayString(ownerEmail(brand)), 9, ["title"])) : createCommentVNode("", true),
                      brand.show_from_at ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(unref(t)("show")) + ": " + toDisplayString(brand.show_from_at) + " / " + toDisplayString(brand.show_to_at), 1)) : (openBlock(), createBlock("div", {
                        key: 2,
                        class: "flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(formatDate(brand.published_at)), 1))
                    ], 512), [
                      [vShow, isOwnerBlockOpen(brand.id)]
                    ]),
                    createVNode("div", { class: "relative w-full bg-slate-200 dark:bg-slate-900" }, [
                      createVNode("img", {
                        src: imageUrl(brand),
                        alt: imageAlt(brand),
                        title: imageTitle(brand),
                        class: "h-32 w-full object-cover"
                      }, null, 8, ["src", "alt", "title"])
                    ]),
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: logoUrl(brand),
                        alt: brandTitle(brand),
                        title: brandTitle(brand),
                        class: "h-12 w-20 object-cover rounded-sm border border-slate-300 dark:border-slate-600"
                      }, null, 8, ["src", "alt", "title"])
                    ]),
                    createVNode("a", {
                      href: brandPublicUrl(brand),
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-sm font-semibold hover:underline text-blue-700 dark:text-blue-300 hover:text-amber-700 dark:hover:text-amber-200 line-clamp-2 text-center"
                    }, toDisplayString(truncateText(brandTitle(brand), 90)), 9, ["href"]),
                    createVNode("div", { class: "text-center text-[11px] text-slate-500 dark:text-slate-300" }, toDisplayString(truncateText(brand.url, 90)), 1),
                    brand.website ? (openBlock(), createBlock("a", {
                      key: 0,
                      href: brand.website,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-center text-[11px] text-amber-700 dark:text-amber-300 hover:underline line-clamp-1",
                      title: brand.website
                    }, toDisplayString(truncateText(brand.website, 90)), 9, ["href", "title"])) : createCommentVNode("", true),
                    brandShort(brand) ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "font-semibold text-[12px] text-center text-teal-700 dark:text-teal-300"
                    }, toDisplayString(truncateText(brandShort(brand), 120)), 1)) : createCommentVNode("", true),
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
                        createVNode("span", { class: "text-[12px] text-slate-700 dark:text-slate-200" }, toDisplayString(brand.views ?? 0), 1)
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "flex items-center justify-center space-x-1",
                        title: unref(t)("images")
                      }, [
                        createVNode("span", { class: "text-[11px]" }, toDisplayString(unref(t)("images")) + ": ", 1),
                        createVNode("span", { class: "text-[12px] text-slate-700 dark:text-slate-200" }, toDisplayString(brand.images_count ?? 0), 1)
                      ], 8, ["title"])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 gap-0.5 text-[11px] text-slate-600 dark:text-slate-300" }, [
                      createVNode("div", { class: "font-semibold text-center text-fuchsia-700 dark:text-fuchsia-300" }, [
                        createVNode("span", null, toDisplayString(unref(t)("status")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(getStatusLabel(brand.status)), 1)
                      ])
                    ]),
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(brand.moderation_status).class],
                        title: brand.moderation_note && brand.moderated_at ? `${brand.moderation_note} [${formatDate(brand.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(brand.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$8, {
                        isAdmin: __props.isAdmin,
                        status: (brand == null ? void 0 : brand.moderation_status) ?? 0,
                        initialNote: (brand == null ? void 0 : brand.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => emitApprove(brand, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-between px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: Boolean(brand.left),
                        title: brand.left ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleLeft: ($event) => emitToggleLeft(brand)
                      }, null, 8, ["isActive", "title", "onToggleLeft"]),
                      createVNode(_sfc_main$6, {
                        isActive: Boolean(brand.main),
                        title: brand.main ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleMain: ($event) => emitToggleMain(brand)
                      }, null, 8, ["isActive", "title", "onToggleMain"]),
                      createVNode(_sfc_main$7, {
                        isActive: Boolean(brand.right),
                        title: brand.right ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleRight: ($event) => emitToggleRight(brand)
                      }, null, 8, ["isActive", "title", "onToggleRight"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$9, {
                        isActive: Boolean(brand.activity),
                        title: brand.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emitToggleActivity(brand)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route(
                          "admin.marketBrands.edit",
                          { marketBrand: brand.id }
                        )
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => emitDelete(brand)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketBrand/View/BrandCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminMarketBrandsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminMarketBrandsPerPage: { type: Number, default: 6 },
    adminMarketBrandsDefaultSort: { type: String, default: "idDesc" },
    brands: { type: [Array, Object], default: () => [] },
    brandsCount: { type: Number, default: 0 },
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
    const getBrandTranslation = (brand) => (brand == null ? void 0 : brand.translation) || {};
    const getBrandTitle = (brand) => {
      var _a;
      return ((_a = getBrandTranslation(brand)) == null ? void 0 : _a.title) || `ID: ${brand == null ? void 0 : brand.id}`;
    };
    const getOwnerName = (brand) => {
      var _a;
      return ((_a = brand == null ? void 0 : brand.owner) == null ? void 0 : _a.name) || "";
    };
    const getOwnerEmail = (brand) => {
      var _a;
      return ((_a = brand == null ? void 0 : brand.owner) == null ? void 0 : _a.email) || "";
    };
    const normalize = (value) => (value ?? "").toString().trim().toLowerCase();
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(value || 0).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const moderationNum = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const byIdDesc = (a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || byIdDesc(a, b);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || byIdDesc(a, b);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || byIdDesc(a, b);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || byIdDesc(a, b);
    const byStringAsc = (getter) => (a, b) => normalize(getter(a)).localeCompare(
      normalize(getter(b)),
      locale.value
    ) || byIdDesc(a, b);
    const byStringDesc = (getter) => (a, b) => normalize(getter(b)).localeCompare(
      normalize(getter(a)),
      locale.value
    ) || byIdDesc(a, b);
    const filterWithIdDesc = (list, callback) => list.filter(callback).sort(byIdDesc);
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_market_brands") || "cards"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_market_brands",
        value
      );
    });
    const itemsPerPage = ref(
      props.adminMarketBrandsPerPage || 6
    );
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountMarketBrands"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(
              `Показ ${newVal} брендов на странице.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления кол-ва брендов."
            );
          }
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminMarketBrandsDefaultSort || "idDesc"
    );
    watch(sortParam, (newVal) => {
      router.put(
        route("admin.settings.updateAdminSortMarketBrands"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing) {
              router.get(
                window.location.pathname,
                {
                  ...Object.fromEntries(
                    new URLSearchParams(
                      window.location.search
                    )
                  ),
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
            toast.info(
              "Сортировка брендов успешно изменена."
            );
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления сортировки брендов."
            );
          }
        }
      );
    });
    const brandsList = computed(() => {
      var _a, _b, _c, _d;
      if (Array.isArray(props.brands)) {
        return props.brands;
      }
      if (Array.isArray((_a = props.brands) == null ? void 0 : _a.data)) {
        return props.brands.data;
      }
      if (Array.isArray((_c = (_b = props.brands) == null ? void 0 : _b.data) == null ? void 0 : _c.data)) {
        return props.brands.data.data;
      }
      if (Array.isArray((_d = props.brands) == null ? void 0 : _d.resource)) {
        return props.brands.resource;
      }
      return [];
    });
    const localBrands = ref([]);
    watch(
      brandsList,
      (newVal) => {
        localBrands.value = JSON.parse(
          JSON.stringify(newVal || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const showConfirmDeleteModal = ref(false);
    const brandToDeleteId = ref(null);
    const brandToDeleteTitle = ref("");
    const confirmDelete = (brandOrId, title = null) => {
      if (brandOrId && typeof brandOrId === "object") {
        brandToDeleteId.value = brandOrId.id;
        brandToDeleteTitle.value = title || getBrandTitle(brandOrId);
      } else {
        brandToDeleteId.value = brandOrId;
        brandToDeleteTitle.value = title || `ID: ${brandOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      brandToDeleteId.value = null;
      brandToDeleteTitle.value = "";
    };
    const deleteBrand = () => {
      if (brandToDeleteId.value === null) {
        return;
      }
      const idToDelete = brandToDeleteId.value;
      const titleToDelete = brandToDeleteTitle.value;
      router.delete(
        route(
          "admin.marketBrands.destroy",
          { marketBrand: idToDelete }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Бренд "${titleToDelete || `ID: ${idToDelete}`}" удалён.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
            toast.error(
              `${errorMsg} (Бренд: ${titleToDelete || `ID: ${idToDelete}`})`
            );
          },
          onFinish: closeModal
        }
      );
    };
    const patchLocalBrand = (brandId, callback) => {
      const index = localBrands.value.findIndex(
        (brand) => brand.id === brandId
      );
      if (index !== -1) {
        callback(localBrands.value[index]);
      }
    };
    const toggleActivity = (brand) => {
      const newActivity = !brand.activity;
      const title = getBrandTitle(brand);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.marketBrands.updateActivity",
          { marketBrand: brand.id }
        ),
        { activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBrand(
              brand.id,
              (node) => {
                node.activity = newActivity;
              }
            );
            toast.success(
              `Бренд "${title}" ${actionText}.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors.activity || errors.general || `Ошибка изменения активности для "${title}".`
            );
          }
        }
      );
    };
    const toggleLeft = (brand) => {
      const newLeft = !brand.left;
      const title = getBrandTitle(brand);
      router.put(
        route(
          "admin.actions.marketBrands.updateLeft",
          { marketBrand: brand.id }
        ),
        { left: newLeft },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBrand(
              brand.id,
              (node) => {
                node.left = newLeft;
              }
            );
            toast.success(
              `Позиция left для бренда "${title}" обновлена.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors.left || errors.general || `Ошибка изменения left для "${title}".`
            );
          }
        }
      );
    };
    const toggleMain = (brand) => {
      const newMain = !brand.main;
      const title = getBrandTitle(brand);
      router.put(
        route(
          "admin.actions.marketBrands.updateMain",
          { marketBrand: brand.id }
        ),
        { main: newMain },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBrand(
              brand.id,
              (node) => {
                node.main = newMain;
              }
            );
            toast.success(
              `Позиция main для бренда "${title}" обновлена.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors.main || errors.general || `Ошибка изменения main для "${title}".`
            );
          }
        }
      );
    };
    const toggleRight = (brand) => {
      const newRight = !brand.right;
      const title = getBrandTitle(brand);
      router.put(
        route(
          "admin.actions.marketBrands.updateRight",
          { marketBrand: brand.id }
        ),
        { right: newRight },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBrand(
              brand.id,
              (node) => {
                node.right = newRight;
              }
            );
            toast.success(
              `Позиция right для бренда "${title}" обновлена.`
            );
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
    const sortBrands = (brands) => {
      const list = (brands || []).slice();
      if (sortParam.value === "activity") {
        return filterWithIdDesc(
          list,
          (brand) => Boolean(brand == null ? void 0 : brand.activity)
        );
      }
      if (sortParam.value === "inactive") {
        return filterWithIdDesc(
          list,
          (brand) => !(brand == null ? void 0 : brand.activity)
        );
      }
      if (sortParam.value === "left") {
        return filterWithIdDesc(
          list,
          (brand) => Boolean(brand == null ? void 0 : brand.left)
        );
      }
      if (sortParam.value === "noLeft") {
        return filterWithIdDesc(
          list,
          (brand) => !(brand == null ? void 0 : brand.left)
        );
      }
      if (sortParam.value === "main") {
        return filterWithIdDesc(
          list,
          (brand) => Boolean(brand == null ? void 0 : brand.main)
        );
      }
      if (sortParam.value === "noMain") {
        return filterWithIdDesc(
          list,
          (brand) => !(brand == null ? void 0 : brand.main)
        );
      }
      if (sortParam.value === "right") {
        return filterWithIdDesc(
          list,
          (brand) => Boolean(brand == null ? void 0 : brand.right)
        );
      }
      if (sortParam.value === "noRight") {
        return filterWithIdDesc(
          list,
          (brand) => !(brand == null ? void 0 : brand.right)
        );
      }
      if (sortParam.value === "statusDraft") {
        return filterWithIdDesc(
          list,
          (brand) => (brand == null ? void 0 : brand.status) === "draft"
        );
      }
      if (sortParam.value === "statusPublished") {
        return filterWithIdDesc(
          list,
          (brand) => (brand == null ? void 0 : brand.status) === "published"
        );
      }
      if (sortParam.value === "statusArchived") {
        return filterWithIdDesc(
          list,
          (brand) => (brand == null ? void 0 : brand.status) === "archived"
        );
      }
      if (sortParam.value === "moderationPending") {
        return filterWithIdDesc(
          list,
          (brand) => moderationNum(
            brand == null ? void 0 : brand.moderation_status
          ) === 0
        );
      }
      if (sortParam.value === "moderationApproved") {
        return filterWithIdDesc(
          list,
          (brand) => moderationNum(
            brand == null ? void 0 : brand.moderation_status
          ) === 1
        );
      }
      if (sortParam.value === "moderationRejected") {
        return filterWithIdDesc(
          list,
          (brand) => moderationNum(
            brand == null ? void 0 : brand.moderation_status
          ) === 2
        );
      }
      const sortMap = {
        /* ID */
        idAsc: (a, b) => safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        idDesc: byIdDesc,
        /* Порядок */
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        /* Название */
        titleAsc: byStringAsc(
          getBrandTitle
        ),
        titleDesc: byStringDesc(
          getBrandTitle
        ),
        /* URL */
        urlAsc: byStringAsc(
          (brand) => brand == null ? void 0 : brand.url
        ),
        urlDesc: byStringDesc(
          (brand) => brand == null ? void 0 : brand.url
        ),
        /* Website */
        websiteAsc: byStringAsc(
          (brand) => brand == null ? void 0 : brand.website
        ),
        websiteDesc: byStringDesc(
          (brand) => brand == null ? void 0 : brand.website
        ),
        /* Активность */
        activityAsc: byNumberAsc(
          "activity"
        ),
        activityDesc: byNumberDesc(
          "activity"
        ),
        /* Левая зона */
        leftAsc: byNumberAsc(
          "left"
        ),
        leftDesc: byNumberDesc(
          "left"
        ),
        /* Главная зона */
        mainAsc: byNumberAsc(
          "main"
        ),
        mainDesc: byNumberDesc(
          "main"
        ),
        /* Правая зона */
        rightAsc: byNumberAsc(
          "right"
        ),
        rightDesc: byNumberDesc(
          "right"
        ),
        /* Просмотры */
        viewsAsc: byNumberAsc(
          "views"
        ),
        viewsDesc: byNumberDesc(
          "views"
        ),
        /* Изображения */
        imagesAsc: byNumberAsc(
          "images_count"
        ),
        imagesDesc: byNumberDesc(
          "images_count"
        ),
        /* Статус */
        statusAsc: byStringAsc(
          (brand) => brand == null ? void 0 : brand.status
        ),
        statusDesc: byStringDesc(
          (brand) => brand == null ? void 0 : brand.status
        ),
        /* Модерация */
        moderationStatusAsc: (a, b) => moderationNum(
          a == null ? void 0 : a.moderation_status
        ) - moderationNum(
          b == null ? void 0 : b.moderation_status
        ) || byIdDesc(a, b),
        moderationStatusDesc: (a, b) => moderationNum(
          b == null ? void 0 : b.moderation_status
        ) - moderationNum(
          a == null ? void 0 : a.moderation_status
        ) || byIdDesc(a, b),
        /* Владелец */
        ownerNameAsc: byStringAsc(
          getOwnerName
        ),
        ownerNameDesc: byStringDesc(
          getOwnerName
        ),
        ownerEmailAsc: byStringAsc(
          getOwnerEmail
        ),
        ownerEmailDesc: byStringDesc(
          getOwnerEmail
        ),
        /* Даты */
        publishedAtAsc: byDateAsc(
          "published_at"
        ),
        publishedAtDesc: byDateDesc(
          "published_at"
        ),
        showFromAtAsc: byDateAsc(
          "show_from_at"
        ),
        showFromAtDesc: byDateDesc(
          "show_from_at"
        ),
        showToAtAsc: byDateAsc(
          "show_to_at"
        ),
        showToAtDesc: byDateDesc(
          "show_to_at"
        ),
        createdAtAsc: byDateAsc(
          "created_at"
        ),
        createdAtDesc: byDateDesc(
          "created_at"
        ),
        dateAsc: byDateAsc(
          "created_at"
        ),
        dateDesc: byDateDesc(
          "created_at"
        ),
        updatedAtAsc: byDateAsc(
          "updated_at"
        ),
        updatedAtDesc: byDateDesc(
          "updated_at"
        )
      };
      const comparator = sortMap[sortParam.value];
      return comparator ? list.sort(comparator) : list.sort(byIdDesc);
    };
    const filteredBrands = computed(() => {
      const query = normalize(
        searchQuery.value
      );
      let filtered = localBrands.value || [];
      if (query) {
        filtered = filtered.filter((brand) => {
          const translation = getBrandTranslation(brand);
          const values = [
            brand == null ? void 0 : brand.url,
            brand == null ? void 0 : brand.website,
            brand == null ? void 0 : brand.icon,
            brand == null ? void 0 : brand.status,
            brand == null ? void 0 : brand.moderation_note,
            translation == null ? void 0 : translation.title,
            translation == null ? void 0 : translation.subtitle,
            translation == null ? void 0 : translation.short,
            translation == null ? void 0 : translation.description,
            getOwnerName(brand),
            getOwnerEmail(brand)
          ];
          return values.some(
            (value) => normalize(value).includes(query)
          );
        });
      }
      return sortBrands(filtered);
    });
    const paginatedBrands = computed(() => {
      const perPage = Number(
        itemsPerPage.value || 10
      );
      const start = (currentPage.value - 1) * perPage;
      return filteredBrands.value.slice(
        start,
        start + perPage
      );
    });
    const displayedBrands = computed(() => {
      return props.useServerProcessing ? brandsList.value : paginatedBrands.value;
    });
    watch(
      [
        itemsPerPage,
        searchQuery,
        sortParam
      ],
      () => {
        currentPage.value = 1;
      }
    );
    const selectedBrands = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = Boolean(
        (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false
      );
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedBrands.value.map(
        (brand) => brand.id
      );
      if (checked) {
        selectedBrands.value = [
          .../* @__PURE__ */ new Set([
            ...selectedBrands.value,
            ...ids
          ])
        ];
      } else {
        selectedBrands.value = selectedBrands.value.filter(
          (id) => !ids.includes(id)
        );
      }
    };
    const toggleSelectBrand = (brandId) => {
      const index = selectedBrands.value.indexOf(
        brandId
      );
      if (index > -1) {
        selectedBrands.value.splice(
          index,
          1
        );
      } else {
        selectedBrands.value.push(
          brandId
        );
      }
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedBrands.value.length) {
        toast.warning(
          "Выберите бренды для активации/деактивации."
        );
        return;
      }
      const idsToUpdate = [
        ...selectedBrands.value
      ];
      router.put(
        route(
          "admin.actions.marketBrands.bulkUpdateActivity"
        ),
        {
          ids: idsToUpdate,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localBrands.value = localBrands.value.map(
              (brand) => idsToUpdate.includes(
                brand.id
              ) ? {
                ...brand,
                activity: newActivity
              } : brand
            );
            selectedBrands.value = [];
            toast.success(
              "Активность брендов массово обновлена."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности."
            );
          }
        }
      );
    };
    const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
      if (!selectedBrands.value.length) {
        toast.warning(
          "Выберите бренды для массового действия."
        );
        return;
      }
      const idsToUpdate = [
        ...selectedBrands.value
      ];
      router.put(
        route(routeName),
        {
          ids: idsToUpdate,
          [field]: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localBrands.value = localBrands.value.map(
              (brand) => idsToUpdate.includes(
                brand.id
              ) ? {
                ...brand,
                [field]: newValue
              } : brand
            );
            selectedBrands.value = [];
            toast.success(
              successMessage
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления."
            );
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedBrands.value.length) {
        toast.warning(
          "Выберите хотя бы один бренд для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные бренды?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.marketBrands.bulkDestroy"
        ),
        {
          data: {
            ids: selectedBrands.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedBrands.value = [];
            toast.success(
              "Массовое удаление брендов успешно завершено."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              errors[errorKey] || "Произошла ошибка при удалении брендов."
            );
          }
        }
      );
    };
    const handleBulkAction = (event) => {
      var _a;
      const action = ((_a = event == null ? void 0 : event.target) == null ? void 0 : _a.value) || "";
      switch (action) {
        case "selectAll":
          toggleAll({
            checked: true,
            ids: displayedBrands.value.map(
              (brand) => brand.id
            )
          });
          break;
        case "deselectAll":
          toggleAll({
            checked: false,
            ids: displayedBrands.value.map(
              (brand) => brand.id
            )
          });
          break;
        case "activate":
          bulkToggleActivity(true);
          break;
        case "deactivate":
          bulkToggleActivity(false);
          break;
        case "left":
          bulkToggleFlag(
            "left",
            true,
            "admin.actions.marketBrands.bulkUpdateLeft",
            "Бренды добавлены в левую колонку."
          );
          break;
        case "noLeft":
          bulkToggleFlag(
            "left",
            false,
            "admin.actions.marketBrands.bulkUpdateLeft",
            "Бренды убраны из левой колонки."
          );
          break;
        case "main":
          bulkToggleFlag(
            "main",
            true,
            "admin.actions.marketBrands.bulkUpdateMain",
            "Бренды добавлены в главный блок."
          );
          break;
        case "noMain":
          bulkToggleFlag(
            "main",
            false,
            "admin.actions.marketBrands.bulkUpdateMain",
            "Бренды убраны из главного блока."
          );
          break;
        case "right":
          bulkToggleFlag(
            "right",
            true,
            "admin.actions.marketBrands.bulkUpdateRight",
            "Бренды добавлены в правую колонку."
          );
          break;
        case "noRight":
          bulkToggleFlag(
            "right",
            false,
            "admin.actions.marketBrands.bulkUpdateRight",
            "Бренды убраны из правой колонки."
          );
          break;
        case "delete":
          bulkDelete();
          break;
      }
      if (event == null ? void 0 : event.target) {
        event.target.value = "";
      }
    };
    const approveBrand = (brand, status = 1, note = "") => {
      if (!(brand == null ? void 0 : brand.id)) {
        return;
      }
      router.put(
        route(
          "admin.actions.marketBrands.approve",
          { marketBrand: brand.id }
        ),
        {
          moderation_status: status,
          moderation_note: note
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBrand(
              brand.id,
              (node) => {
                node.moderation_status = status;
                node.is_pending = status === 0;
                node.is_approved = status === 1;
                node.is_rejected = status === 2;
                node.moderation_note = note;
              }
            );
            toast.success(
              status === 1 ? "Бренд одобрен." : status === 2 ? "Бренд отклонён." : "Бренд отправлен на модерацию."
            );
          },
          onError: () => {
            toast.error(
              "Ошибка модерации бренда."
            );
          }
        }
      );
    };
    const handleSortOrderUpdate = (newOrderIds) => {
      const items = (newOrderIds || []).map(
        (id, index) => ({
          id,
          sort: index
        })
      );
      if (!items.length) {
        return;
      }
      router.put(
        route(
          "admin.actions.marketBrands.updateSortBulk"
        ),
        { items },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            items.forEach((item) => {
              patchLocalBrand(
                item.id,
                (brand) => {
                  brand.sort = item.sort;
                }
              );
            });
            toast.success(
              "Сортировка брендов обновлена."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка сортировки брендов:",
              errors
            );
            toast.error(
              errors.message || errors.general || "Ошибка обновления сортировки."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("marketBrands")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("marketBrands"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("marketBrands")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("marketBrands")), 1)
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
              href: _ctx.route("admin.marketBrands.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addMarketBrand"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addMarketBrand")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              "setting-key": "adminMarketBrandsProcessingMode",
              mode: __props.adminMarketBrandsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.brandsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.brandsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$e, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.brandsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$f, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.brandsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountMarketBrands"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$3, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": ($event) => sortParam.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.brandsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$i, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.brandsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.brandsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$j, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.brandsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredBrands.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.brands }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                brands: displayedBrands.value,
                "selected-brands": selectedBrands.value,
                "is-admin": isAdmin.value,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectBrand,
                onToggleAll: toggleAll,
                onApprove: approveBrand
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                brands: displayedBrands.value,
                "selected-brands": selectedBrands.value,
                "is-admin": isAdmin.value,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectBrand,
                onToggleAll: toggleAll,
                onApprove: approveBrand
              }, null, _parent2, _scopeId));
            }
            if (__props.brandsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredBrands.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.brands }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$m, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteBrand,
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
                      href: _ctx.route("admin.marketBrands.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addMarketBrand")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$d, {
                      "setting-key": "adminMarketBrandsProcessingMode",
                      mode: __props.adminMarketBrandsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.brandsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.brandsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.brandsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$f, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.brandsCount ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountMarketBrands"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.brandsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$i, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.brandsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$j, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.brandsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredBrands.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.brands
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    brands: displayedBrands.value,
                    "selected-brands": selectedBrands.value,
                    "is-admin": isAdmin.value,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectBrand,
                    onToggleAll: toggleAll,
                    onApprove: approveBrand
                  }, null, 8, ["brands", "selected-brands", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    brands: displayedBrands.value,
                    "selected-brands": selectedBrands.value,
                    "is-admin": isAdmin.value,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectBrand,
                    onToggleAll: toggleAll,
                    onApprove: approveBrand
                  }, null, 8, ["brands", "selected-brands", "is-admin"])),
                  __props.brandsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredBrands.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.brands
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$m, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteBrand,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketBrands/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
