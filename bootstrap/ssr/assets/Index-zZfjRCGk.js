import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, withDirectives, withModifiers, createTextVNode, vShow, Fragment, renderList, computed } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent, ssrRenderClass, ssrRenderStyle, ssrRenderList } from "vue/server-renderer";
import { usePage, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$f } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$p } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$l } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$j, a as _sfc_main$m, b as _sfc_main$n } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$h } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$i, a as _sfc_main$o } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$k } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$g } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$8, a as _sfc_main$9, b as _sfc_main$a } from "./RightToggle-r8SYzaEU.js";
import { _ as _sfc_main$5 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$7 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$6 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$e } from "./ModerationButton-D_ehimPY.js";
import { _ as _sfc_main$b, a as _sfc_main$c, b as _sfc_main$d } from "./HitButtonToggle-DYH18bkK.js";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value="">— ${ssrInterpolate(unref(t)("selectAction"))} —</option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>───────────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>───────────────────────</option><option value="left">${ssrInterpolate(unref(t)("left"))}: ON</option><option value="noLeft">${ssrInterpolate(unref(t)("left"))}: OFF</option><option value="main">${ssrInterpolate(unref(t)("main"))}: ON</option><option value="noMain">${ssrInterpolate(unref(t)("main"))}: OFF</option><option value="right">${ssrInterpolate(unref(t)("right"))}: ON</option><option value="noRight">${ssrInterpolate(unref(t)("right"))}: OFF</option><option disabled>───────────────────────</option><option value="isNewOn">${ssrInterpolate(unref(t)("sortIsNew"))}: ON</option><option value="isNewOff">${ssrInterpolate(unref(t)("sortIsNew"))}: OFF</option><option value="isHitOn">${ssrInterpolate(unref(t)("sortIsHit"))}: ON</option><option value="isHitOff">${ssrInterpolate(unref(t)("sortIsHit"))}: OFF</option><option value="isSaleOn">${ssrInterpolate(unref(t)("sortIsSale"))}: ON</option><option value="isSaleOff">${ssrInterpolate(unref(t)("sortIsSale"))}: OFF</option><option disabled>───────────────────────</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled>───────────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProductBundle/Select/BulkActionSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: {
      type: String,
      default: "idDesc"
    }
  },
  emits: [
    "update:sortParam"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex h-fit items-center justify-center my-2" }, _attrs))}><label for="sortParam" class="hidden text-sm font-semibold tracking-wider text-slate-600 dark:text-slate-100 sm:mr-2 lg:block">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="form-select w-64 rounded-sm border border-slate-400 bg-white px-3 py-0.5 text-gray-600 shadow-sm dark:border-slate-600 dark:bg-gray-200 dark:text-gray-900"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>────────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>────────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option value="urlAsc">URL A→Z</option><option value="urlDesc">URL Z→A</option><option disabled>────────────────────</option><option value="skuAsc">SKU A→Z</option><option value="skuDesc">SKU Z→A</option><option value="vendorCodeAsc">${ssrInterpolate(unref(t)("vendorCode"))} A→Z</option><option value="vendorCodeDesc">${ssrInterpolate(unref(t)("vendorCode"))} Z→A</option><option value="barcodeAsc">${ssrInterpolate(unref(t)("barcode"))} A→Z</option><option value="barcodeDesc">${ssrInterpolate(unref(t)("barcode"))} Z→A</option><option disabled>────────────────────</option><option value="companyAsc">${ssrInterpolate(unref(t)("marketCompany"))} A→Z</option><option value="companyDesc">${ssrInterpolate(unref(t)("marketCompany"))} Z→A</option><option disabled>────────────────────</option><option value="shopAsc">${ssrInterpolate(unref(t)("marketShop"))} A→Z</option><option value="shopDesc">${ssrInterpolate(unref(t)("marketShop"))} Z→A</option><option disabled>────────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option><option disabled>────────────────────</option><option value="calculatePriceDesc">${ssrInterpolate(unref(t)("automaticPrice"))} ON→OFF</option><option value="calculatePriceAsc">${ssrInterpolate(unref(t)("automaticPrice"))} OFF→ON</option><option value="calculatedPrice">${ssrInterpolate(unref(t)("automaticPrice"))}</option><option value="manualPrice">${ssrInterpolate(unref(t)("manualPrice"))}</option><option disabled>────────────────────</option><option value="priceDesc">${ssrInterpolate(unref(t)("price"))} 9→0</option><option value="priceAsc">${ssrInterpolate(unref(t)("price"))} 0→9</option><option value="oldPriceDesc">${ssrInterpolate(unref(t)("compareAtPrice"))} 9→0</option><option value="oldPriceAsc">${ssrInterpolate(unref(t)("compareAtPrice"))} 0→9</option><option value="purchasePriceDesc">${ssrInterpolate(unref(t)("purchasePrice"))} 9→0</option><option value="purchasePriceAsc">${ssrInterpolate(unref(t)("purchasePrice"))} 0→9</option><option value="wholesalePriceDesc">${ssrInterpolate(unref(t)("wholesalePrice"))} 9→0</option><option value="wholesalePriceAsc">${ssrInterpolate(unref(t)("wholesalePrice"))} 0→9</option><option value="wholesaleMinQuantityDesc">${ssrInterpolate(unref(t)("wholesaleMinQuantity"))} 9→0</option><option value="wholesaleMinQuantityAsc">${ssrInterpolate(unref(t)("wholesaleMinQuantity"))} 0→9</option><option disabled>────────────────────</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>────────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option value="likesDesc">${ssrInterpolate(unref(t)("likes"))} 9→0</option><option value="likesAsc">${ssrInterpolate(unref(t)("likes"))} 0→9</option><option value="ratingDesc">${ssrInterpolate(unref(t)("rating"))} 9→0</option><option value="ratingAsc">${ssrInterpolate(unref(t)("rating"))} 0→9</option><option value="ratingCountDesc">${ssrInterpolate(unref(t)("ratingCount"))} 9→0</option><option value="ratingCountAsc">${ssrInterpolate(unref(t)("ratingCount"))} 0→9</option><option disabled>────────────────────</option><option value="itemsDesc">${ssrInterpolate(unref(t)("checkbox"))} 9→0</option><option value="itemsAsc">${ssrInterpolate(unref(t)("checkbox"))} 0→9</option><option value="hasItems">${ssrInterpolate(unref(t)("yes"))}</option><option value="withoutItems">${ssrInterpolate(unref(t)("no"))}</option><option disabled>────────────────────</option><option value="newDesc">${ssrInterpolate(unref(t)("sortIsNew"))} ON→OFF</option><option value="newAsc">${ssrInterpolate(unref(t)("sortIsNew"))} OFF→ON</option><option value="new">${ssrInterpolate(unref(t)("sortIsNew"))}</option><option value="notNew">${ssrInterpolate(unref(t)("notNew"))}</option><option disabled>────────────────────</option><option value="hitDesc">${ssrInterpolate(unref(t)("sortIsHit"))} ON→OFF</option><option value="hitAsc">${ssrInterpolate(unref(t)("sortIsHit"))} OFF→ON</option><option value="hit">${ssrInterpolate(unref(t)("sortIsHit"))}</option><option value="notHit">${ssrInterpolate(unref(t)("notHit"))}</option><option disabled>────────────────────</option><option value="saleDesc">${ssrInterpolate(unref(t)("sortIsSale"))} ON→OFF</option><option value="saleAsc">${ssrInterpolate(unref(t)("sortIsSale"))} OFF→ON</option><option value="sale">${ssrInterpolate(unref(t)("sortIsSale"))}</option><option value="notSale">${ssrInterpolate(unref(t)("notSale"))}</option><option disabled>────────────────────</option><option value="leftDesc">${ssrInterpolate(unref(t)("inLeft"))} ON→OFF</option><option value="leftAsc">${ssrInterpolate(unref(t)("inLeft"))} OFF→ON</option><option value="left">${ssrInterpolate(unref(t)("inLeft"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("notLeft"))}</option><option disabled>────────────────────</option><option value="mainDesc">${ssrInterpolate(unref(t)("inMain"))} ON→OFF</option><option value="mainAsc">${ssrInterpolate(unref(t)("inMain"))} OFF→ON</option><option value="main">${ssrInterpolate(unref(t)("inMain"))}</option><option value="noMain">${ssrInterpolate(unref(t)("notMain"))}</option><option disabled>────────────────────</option><option value="rightDesc">${ssrInterpolate(unref(t)("inRight"))} ON→OFF</option><option value="rightAsc">${ssrInterpolate(unref(t)("inRight"))} OFF→ON</option><option value="right">${ssrInterpolate(unref(t)("inRight"))}</option><option value="noRight">${ssrInterpolate(unref(t)("notRight"))}</option><option disabled>────────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="statusDraft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="statusPublished">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="statusArchived">${ssrInterpolate(unref(t)("statusArchived"))}</option><option disabled>────────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>────────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="showFromAtDesc">${ssrInterpolate(unref(t)("show"))} — ${ssrInterpolate(unref(t)("shortStarted"))} ↓</option><option value="showFromAtAsc">${ssrInterpolate(unref(t)("show"))} — ${ssrInterpolate(unref(t)("shortStarted"))} ↑</option><option value="showToAtDesc">${ssrInterpolate(unref(t)("show"))} — ${ssrInterpolate(unref(t)("shortExpires"))} ↓</option><option value="showToAtAsc">${ssrInterpolate(unref(t)("show"))} — ${ssrInterpolate(unref(t)("shortExpires"))} ↑</option><option disabled>────────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>────────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProductBundle/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "BundleTable",
  __ssrInlineRender: true,
  props: {
    bundles: {
      type: Array,
      default: () => []
    },
    selectedBundles: {
      type: Array,
      default: () => []
    },
    allSelected: {
      type: Boolean,
      default: false
    },
    isAdmin: {
      type: Boolean,
      default: false
    }
  },
  emits: [
    "toggle-left",
    "toggle-main",
    "toggle-right",
    "toggle-is-new",
    "toggle-is-hit",
    "toggle-is-sale",
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
    const emit = __emit;
    const localBundles = ref([]);
    watch(
      () => props.bundles,
      (newValue) => {
        localBundles.value = JSON.parse(
          JSON.stringify(newValue || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localBundles.value.map((bundle) => bundle.id)
      );
    };
    const allBundlesSelected = () => {
      if (!localBundles.value.length) {
        return false;
      }
      return localBundles.value.every((bundle) => {
        return props.selectedBundles.includes(bundle.id);
      });
    };
    const bundleTranslation = (bundle) => {
      return (bundle == null ? void 0 : bundle.translation) || {};
    };
    const bundleTitle = (bundle) => {
      var _a;
      return ((_a = bundleTranslation(bundle)) == null ? void 0 : _a.title) || `ID: ${bundle == null ? void 0 : bundle.id}`;
    };
    const bundleSubtitle = (bundle) => {
      var _a;
      return ((_a = bundleTranslation(bundle)) == null ? void 0 : _a.subtitle) || "";
    };
    const bundleShort = (bundle) => {
      var _a;
      return ((_a = bundleTranslation(bundle)) == null ? void 0 : _a.short) || "";
    };
    const relationTitle = (relation) => {
      var _a;
      return ((_a = relation == null ? void 0 : relation.translation) == null ? void 0 : _a.title) || "";
    };
    const companyTitle = (bundle) => {
      var _a;
      return relationTitle(bundle == null ? void 0 : bundle.company) || ((_a = bundle == null ? void 0 : bundle.company) == null ? void 0 : _a.legal_name) || "";
    };
    const shopTitle = (bundle) => {
      return relationTitle(bundle == null ? void 0 : bundle.shop);
    };
    const getPrimaryImage = (bundle) => {
      if (!Array.isArray(bundle == null ? void 0 : bundle.images) || !bundle.images.length) {
        return null;
      }
      return [...bundle.images].sort((left, right) => {
        return Number((left == null ? void 0 : left.order) ?? 0) - Number((right == null ? void 0 : right.order) ?? 0);
      })[0];
    };
    const imageUrl = (bundle) => {
      const image = getPrimaryImage(bundle);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/market/market_product_bundle_images/default-image.png";
    };
    const imageAlt = (bundle) => {
      var _a;
      return ((_a = getPrimaryImage(bundle)) == null ? void 0 : _a.alt) || bundleTitle(bundle);
    };
    const imageTitle = (bundle) => {
      var _a;
      return ((_a = getPrimaryImage(bundle)) == null ? void 0 : _a.caption) || bundleTitle(bundle);
    };
    const ownerTitle = (bundle) => {
      const owner = bundle == null ? void 0 : bundle.owner;
      if (!owner) {
        return t("noData");
      }
      return `${owner.name || ""}${owner.email ? ` — ${owner.email}` : ""}`.trim();
    };
    const ownerAvatar = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const statusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const getStatusLabel = (status) => {
      return t(
        statusLabelKeyMap[status] || status || "no"
      );
    };
    const statusBadge = (status) => {
      if (status === "published") {
        return {
          text: getStatusLabel(status),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700"
        };
      }
      if (status === "archived") {
        return {
          text: getStatusLabel(status),
          class: "bg-slate-200 text-slate-700 border-slate-400 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600"
        };
      }
      return {
        text: getStatusLabel(status),
        class: "bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700"
      };
    };
    const moderationBadge = (status) => {
      const value = Number(status ?? 0);
      if (value === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300 dark:border-emerald-700"
        };
      }
      if (value === 2) {
        return {
          text: t("statusSelectRejected"),
          class: "bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300 dark:border-rose-700"
        };
      }
      return {
        text: t("underModeration"),
        class: "bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300 dark:border-amber-700"
      };
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const formatDate = (dateString) => {
      if (!dateString) {
        return "";
      }
      const date = new Date(dateString);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return date.toLocaleDateString(
        locale.value || "ru-RU",
        {
          year: "numeric",
          month: "long",
          day: "numeric"
        }
      );
    };
    const truncateText = (text, maxLength = 55) => {
      if (!text) {
        return "";
      }
      const value = String(text);
      return value.length > maxLength ? `${value.slice(0, maxLength).trimEnd()}…` : value;
    };
    const formatMoney = (value, currency) => {
      if (value === null || value === void 0 || value === "") {
        return "—";
      }
      const amount = safeNumber(value);
      const precision = Number.isFinite(
        Number(currency == null ? void 0 : currency.precision)
      ) ? Number(currency.precision) : 2;
      const thousandsSeparator = (currency == null ? void 0 : currency.thousands_sep) ?? " ";
      const decimalSeparator = (currency == null ? void 0 : currency.decimal_sep) ?? ".";
      const parts = amount.toFixed(precision).split(".");
      const integerPart = parts[0].replace(
        /\B(?=(\d{3})+(?!\d))/g,
        thousandsSeparator
      );
      const formattedAmount = precision > 0 ? `${integerPart}${decimalSeparator}${parts[1]}` : integerPart;
      const symbol = String(
        (currency == null ? void 0 : currency.symbol) || (currency == null ? void 0 : currency.code) || ""
      ).trim();
      if (!symbol) {
        return formattedAmount;
      }
      return (currency == null ? void 0 : currency.symbol_first) ? `${symbol}${formattedAmount}` : `${formattedAmount} ${symbol}`;
    };
    const formatRating = (bundle) => {
      return safeNumber(bundle == null ? void 0 : bundle.rating_avg).toFixed(1);
    };
    const effectivePrice = (bundle) => {
      return (bundle == null ? void 0 : bundle.effective_price) ?? (bundle == null ? void 0 : bundle.price) ?? 0;
    };
    const priceModeTitle = (bundle) => {
      return (bundle == null ? void 0 : bundle.calculate_price) ? "Цена рассчитывается автоматически по составу" : "Цена комплекта задаётся вручную";
    };
    const itemsCount = (bundle) => {
      if ((bundle == null ? void 0 : bundle.items_count) !== void 0) {
        return safeNumber(bundle.items_count);
      }
      return Array.isArray(bundle == null ? void 0 : bundle.items) ? bundle.items.length : 0;
    };
    const activeItemsCount = (bundle) => {
      if ((bundle == null ? void 0 : bundle.active_items_count) !== void 0) {
        return safeNumber(bundle.active_items_count);
      }
      if (Array.isArray(bundle == null ? void 0 : bundle.active_items)) {
        return bundle.active_items.length;
      }
      if (Array.isArray(bundle == null ? void 0 : bundle.items)) {
        return bundle.items.filter(
          (item) => Boolean(item == null ? void 0 : item.activity)
        ).length;
      }
      return 0;
    };
    const itemTitle = (item) => {
      var _a, _b, _c, _d;
      return ((_b = (_a = item == null ? void 0 : item.variant) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((_d = (_c = item == null ? void 0 : item.product) == null ? void 0 : _c.translation) == null ? void 0 : _d.title) || `ID: ${item == null ? void 0 : item.id}`;
    };
    const itemsTitle = (bundle) => {
      const items = Array.isArray(bundle == null ? void 0 : bundle.items) ? bundle.items : Array.isArray(bundle == null ? void 0 : bundle.active_items) ? bundle.active_items : [];
      if (!items.length) {
        return t("noData");
      }
      return items.map((item, index) => {
        const values = [
          `${index + 1}. ${itemTitle(item)}`,
          `× ${safeNumber(item == null ? void 0 : item.quantity) || 1}`
        ];
        if ((item == null ? void 0 : item.unit_price) !== null && (item == null ? void 0 : item.unit_price) !== void 0) {
          values.push(
            `Цена: ${formatMoney(
              item.unit_price,
              bundle == null ? void 0 : bundle.currency
            )}`
          );
        }
        if ((item == null ? void 0 : item.discount_type) && safeNumber(item == null ? void 0 : item.discount_value) > 0) {
          values.push(
            item.discount_type === "percent" ? `Скидка: ${item.discount_value}%` : `Скидка: ${formatMoney(
              item.discount_value,
              bundle == null ? void 0 : bundle.currency
            )}`
          );
        }
        return values.join(" ");
      }).join("\n");
    };
    const codesTitle = (bundle) => {
      return [
        (bundle == null ? void 0 : bundle.url) ? `URL: ${bundle.url}` : null,
        (bundle == null ? void 0 : bundle.sku) ? `SKU: ${bundle.sku}` : null,
        (bundle == null ? void 0 : bundle.vendor_code) ? `Артикул: ${bundle.vendor_code}` : null,
        (bundle == null ? void 0 : bundle.barcode) ? `Штрихкод: ${bundle.barcode}` : null
      ].filter(Boolean).join("\n");
    };
    const supplierTitle = (bundle) => {
      return [
        companyTitle(bundle) ? `Компания: ${companyTitle(bundle)}` : null,
        shopTitle(bundle) ? `Магазин: ${shopTitle(bundle)}` : null
      ].filter(Boolean).join("\n");
    };
    const publicationTitle = (bundle) => {
      return [
        `Sort: ${(bundle == null ? void 0 : bundle.sort) ?? 0}`,
        (bundle == null ? void 0 : bundle.published_at) ? `Публикация: ${formatDate(bundle.published_at)}` : null,
        (bundle == null ? void 0 : bundle.show_from_at) ? `Показ с: ${formatDate(bundle.show_from_at)}` : null,
        (bundle == null ? void 0 : bundle.show_to_at) ? `Показ до: ${formatDate(bundle.show_to_at)}` : null
      ].filter(Boolean).join("\n");
    };
    const bundlePublicUrl = (bundle) => {
      return `/market/bundles/${encodeURIComponent((bundle == null ? void 0 : bundle.url) || "")}`;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 shadow-lg" }, _attrs))}><div class="flex items-center justify-between border-b border-slate-400 dark:border-slate-500 px-3 py-2"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedBundles.length)}</div>`);
      if (localBundles.value.length) {
        _push(`<label class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allBundlesSelected() || __props.allSelected) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localBundles.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="border border-solid border-gray-300 dark:border-gray-700 bg-slate-200 dark:bg-cyan-900 text-xs uppercase"><tr><th class="w-px px-1 py-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-current text-slate-800 dark:text-slate-200" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="w-px whitespace-nowrap px-1 py-1"><div class="text-center font-semibold">${ssrInterpolate(unref(t)("id"))}</div></th><th class="w-px whitespace-nowrap px-1 py-1"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("owner"))}><svg class="h-6 w-6 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path><path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path><path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path><path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path><path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"></path></svg></div></th><th class="w-px px-1 py-1"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="h-6 w-6 shrink-0 fill-current" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32h384c35.3 0 64 28.7 64 64v320c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zm323.8 106.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4S78.8 416 88 416h336c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="min-w-64 px-2 py-1"><div class="text-left font-semibold">${ssrInterpolate(unref(t)("marketProductBundle"))}</div></th><th class="min-w-64 px-2 py-1"><div class="text-left font-semibold">${ssrInterpolate(unref(t)("supplier"))} / ${ssrInterpolate(unref(t)("compound"))}</div></th><th class="whitespace-nowrap px-2 py-1"><div class="text-right font-semibold">${ssrInterpolate(unref(t)("price"))}</div></th><th class="whitespace-nowrap px-2 py-1"><div class="text-center font-semibold">${ssrInterpolate(unref(t)("quantity"))}</div></th><th class="whitespace-nowrap px-2 py-1"><div class="text-right font-semibold">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="w-px whitespace-nowrap px-1 py-1 text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allBundlesSelected() || __props.allSelected) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localBundles.value,
          "onUpdate:modelValue": ($event) => localBundles.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: bundle }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="border-b-2 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="handle w-px cursor-move px-1 py-1 text-center"${_scopeId}><svg class="h-4 w-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="w-px whitespace-nowrap px-1 py-1"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-300"${ssrRenderAttr("title", publicationTitle(bundle))}${_scopeId}>${ssrInterpolate(bundle.id)}</div></td><td class="w-px px-1 py-1"${_scopeId}><div class="flex justify-center"${ssrRenderAttr("title", ownerTitle(bundle))}${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(bundle))}${ssrRenderAttr("alt", ownerTitle(bundle))} class="h-6 w-6 rounded-full border border-slate-300 object-cover dark:border-slate-500"${_scopeId}></div></td><td class="w-px px-1 py-1"${_scopeId}><a${ssrRenderAttr("href", bundlePublicUrl(bundle))} target="_blank" rel="noopener noreferrer" class="block"${_scopeId}><img${ssrRenderAttr("src", imageUrl(bundle))}${ssrRenderAttr("alt", imageAlt(bundle))}${ssrRenderAttr("title", imageTitle(bundle))} class="h-8 w-12 rounded border border-slate-300 object-cover dark:border-slate-500"${_scopeId}></a></td><td class="px-2 py-1 align-top"${_scopeId}><div class="space-y-1"${_scopeId}><a${ssrRenderAttr("href", bundlePublicUrl(bundle))} target="_blank" rel="noopener noreferrer" class="text-center text-xs font-semibold text-sky-700 hover:text-amber-700 hover:underline dark:text-sky-300 dark:hover:text-amber-300"${ssrRenderAttr("title", codesTitle(bundle))}${_scopeId}>${ssrInterpolate(bundleTitle(bundle))}</a><div class="mt-0.5 text-[10px] italic text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(truncateText(bundle.url, 70))}</div>`);
              if (bundleSubtitle(bundle)) {
                _push2(`<div class="text-xs text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(bundleSubtitle(bundle), 65))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (bundleShort(bundle)) {
                _push2(`<div class="text-[10px] font-normal text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(bundleShort(bundle), 90))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="mt-0.5 flex flex-wrap gap-1 text-[9px]"${_scopeId}>`);
              if (bundle.vendor_code) {
                _push2(`<span class="rounded-sm border border-slate-400 bg-violet-100 px-1 py-0.5 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300"${ssrRenderAttr("title", unref(t)("vendorCode"))}${_scopeId}>${ssrInterpolate(truncateText(bundle.vendor_code, 24))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (bundle.sku) {
                _push2(`<span class="rounded-sm border border-slate-400 bg-blue-100 px-1 py-0.5 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"${ssrRenderAttr("title", unref(t)("sku"))}${_scopeId}> SKU: ${ssrInterpolate(truncateText(bundle.sku, 24))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="mt-0.5 flex items-center gap-1 text-[9px]"${_scopeId}><svg class="h-5 w-5 shrink-0 fill-current" viewBox="0 0 512 512"${_scopeId}><path d="M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z"${_scopeId}></path></svg>`);
              if (bundle.barcode) {
                _push2(`<span class="rounded-sm border border-slate-400 bg-slate-200 px-1 py-0.5 text-slate-700 dark:bg-slate-800 dark:text-slate-300"${ssrRenderAttr("title", unref(t)("barcode"))}${_scopeId}>${ssrInterpolate(truncateText(bundle.barcode, 24))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div></td><td class="px-2 py-1 align-top"${_scopeId}><div${ssrRenderAttr("title", supplierTitle(bundle))}${_scopeId}>`);
              if (companyTitle(bundle)) {
                _push2(`<div class="font-semibold"${_scopeId}>${ssrInterpolate(truncateText(companyTitle(bundle), 38))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (shopTitle(bundle)) {
                _push2(`<div class="mt-1 font-normal text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(shopTitle(bundle), 38))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (!companyTitle(bundle) && !shopTitle(bundle)) {
                _push2(`<span class="font-normal text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div${ssrRenderAttr("title", itemsTitle(bundle))}${_scopeId}><div class="flex items-center gap-2"${_scopeId}><div class="rounded-full border bg-cyan-100 px-2 py-0.5 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(itemsCount(bundle))}</div><div class="mt-1 font-normal text-emerald-700 dark:text-emerald-300"${_scopeId}> [${ssrInterpolate(activeItemsCount(bundle))}] </div></div>`);
              if (Array.isArray(bundle.items)) {
                _push2(`<div class="mt-1 font-normal text-[9px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(bundle.items.slice(0, 2).map(itemTitle).join(", "), 70))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div${ssrRenderAttr("title", unref(t)("statistics"))} class="flex flex-col gap-1 font-semibold"${_scopeId}><div class="flex flex-row items-center gap-1"${ssrRenderAttr("title", unref(t)("rating"))}${_scopeId}><svg class="w-3 h-3 fill-current shrink-0 text-red-500 dark:text-red-400" viewBox="0 0 24 24"${_scopeId}><path d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"${_scopeId}></path></svg><span${_scopeId}>${ssrInterpolate(formatRating(bundle))} (${ssrInterpolate(safeNumber(bundle.rating_count))}) </span></div><div class="flex flex-row items-center gap-1"${ssrRenderAttr("title", unref(t)("views"))}${_scopeId}><svg class="w-3 h-3 fill-current shrink-0 text-blue-600 dark:text-blue-300" viewBox="0 0 16 16"${_scopeId}><path d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"${_scopeId}></path></svg><span${_scopeId}>${ssrInterpolate(safeNumber(bundle.views))}</span></div><div class="flex flex-row items-center gap-1"${ssrRenderAttr("title", unref(t)("likes"))}${_scopeId}><svg class="w-3 h-3 fill-current shrink-0 text-rose-400 dark:text-rose-300" viewBox="0 0 24 24"${_scopeId}><path d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"${_scopeId}></path><path d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"${_scopeId}></path></svg><span${_scopeId}>${ssrInterpolate(safeNumber(bundle.likes_count))}</span></div></div></td><td class="whitespace-nowrap px-2 py-1 text-right align-top"${ssrRenderAttr("title", priceModeTitle(bundle))}${_scopeId}><div class="text-sm font-bold text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(formatMoney(effectivePrice(bundle), bundle.currency))}</div>`);
              if (bundle.has_old_price) {
                _push2(`<div class="mt-1 font-normal text-slate-400 line-through"${_scopeId}>${ssrInterpolate(formatMoney(bundle.old_price, bundle.currency))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (safeNumber(bundle.saving_percent) > 0) {
                _push2(`<div class="mt-1 text-rose-600 dark:text-rose-300"${_scopeId}> −${ssrInterpolate(safeNumber(bundle.saving_percent).toFixed(0))}% </div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="${ssrRenderClass([
                bundle.calculate_price ? "bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300" : "bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300",
                "mt-1 rounded px-2 py-0.5 border border-gray-400 text-[10px] text-center font-normal"
              ])}"${_scopeId}>${ssrInterpolate(bundle.calculate_price ? unref(t)("automaticPrice") : unref(t)("manualPrice"))}</div></td><td class="whitespace-nowrap px-2 py-1 text-center align-top"${_scopeId}><div class="${ssrRenderClass([
                bundle.has_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300",
                "text-sm font-bold"
              ])}"${_scopeId}>${ssrInterpolate(safeNumber(bundle.available_quantity))}</div><div class="${ssrRenderClass([
                bundle.has_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-500 dark:text-rose-300",
                "mt-1 font-normal"
              ])}"${_scopeId}>${ssrInterpolate(bundle.has_stock ? unref(t)("inStock") : unref(t)("notAvailable"))}</div></td><td class="whitespace-nowrap px-2 py-1"${_scopeId}><div class="flex flex-col justify-center items-center gap-1"${_scopeId}><div class="flex flex-row items-center justify-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: bundle.activity,
                title: bundle.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit(
                  "toggle-activity",
                  bundle
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route(
                  "admin.marketProductBundles.edit",
                  {
                    marketProductBundle: bundle.id
                  }
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => emit("delete", bundle)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex flex-row items-center justify-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isActive: bundle.left,
                title: bundle.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => emit("toggle-left", bundle)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: bundle.main,
                title: bundle.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => emit("toggle-main", bundle)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                isActive: bundle.right,
                title: bundle.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => emit("toggle-right", bundle)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex flex-row items-center justify-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$b, {
                isActive: bundle.is_new,
                title: bundle.is_new ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleIsNew: ($event) => emit("toggle-is-new", bundle)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$c, {
                isActive: bundle.is_hit,
                title: bundle.is_hit ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleIsHit: ($event) => emit("toggle-is-hit", bundle)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$d, {
                isActive: bundle.is_sale,
                title: bundle.is_sale ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleIsSale: ($event) => emit("toggle-is-sale", bundle)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex flex-row items-center justify-center gap-1"${_scopeId}><div class="flex flex-col items-center justify-center gap-1"${_scopeId}><span class="px-2 py-1 text-[10px] font-semibold text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(statusBadge(bundle.status).text)}</span><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(bundle.moderation_status).class, "rounded-sm border px-2 py-1 text-[9px] font-semibold"])}"${ssrRenderAttr("title", bundle.moderation_note ? `${bundle.moderation_note}${bundle.moderated_at ? ` [${formatDate(
                bundle.moderated_at
              )}]` : ""}` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(bundle.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$e, {
                isAdmin: __props.isAdmin,
                status: (bundle == null ? void 0 : bundle.moderation_status) ?? 0,
                initialNote: (bundle == null ? void 0 : bundle.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emit(
                  "approve",
                  bundle,
                  status,
                  note
                )
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div></div></td><td class="w-px whitespace-nowrap px-2 py-2"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(
                __props.selectedBundles.includes(
                  bundle.id
                )
              ) ? " checked" : ""}${_scopeId}></div></td></tr>`);
            } else {
              return [
                createVNode("tr", { class: "border-b-2 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-cyan-800" }, [
                  createVNode("td", { class: "handle w-px cursor-move px-1 py-1 text-center" }, [
                    (openBlock(), createBlock("svg", {
                      class: "h-4 w-4 text-gray-500 dark:text-gray-300",
                      fill: "currentColor",
                      viewBox: "0 0 20 20"
                    }, [
                      createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                    ]))
                  ]),
                  createVNode("td", { class: "w-px whitespace-nowrap px-1 py-1" }, [
                    createVNode("div", {
                      class: "text-center text-blue-600 dark:text-blue-300",
                      title: publicationTitle(bundle)
                    }, toDisplayString(bundle.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "w-px px-1 py-1" }, [
                    createVNode("div", {
                      class: "flex justify-center",
                      title: ownerTitle(bundle)
                    }, [
                      createVNode("img", {
                        src: ownerAvatar(bundle),
                        alt: ownerTitle(bundle),
                        class: "h-6 w-6 rounded-full border border-slate-300 object-cover dark:border-slate-500"
                      }, null, 8, ["src", "alt"])
                    ], 8, ["title"])
                  ]),
                  createVNode("td", { class: "w-px px-1 py-1" }, [
                    createVNode("a", {
                      href: bundlePublicUrl(bundle),
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "block"
                    }, [
                      createVNode("img", {
                        src: imageUrl(bundle),
                        alt: imageAlt(bundle),
                        title: imageTitle(bundle),
                        class: "h-8 w-12 rounded border border-slate-300 object-cover dark:border-slate-500"
                      }, null, 8, ["src", "alt", "title"])
                    ], 8, ["href"])
                  ]),
                  createVNode("td", { class: "px-2 py-1 align-top" }, [
                    createVNode("div", { class: "space-y-1" }, [
                      createVNode("a", {
                        href: bundlePublicUrl(bundle),
                        target: "_blank",
                        rel: "noopener noreferrer",
                        class: "text-center text-xs font-semibold text-sky-700 hover:text-amber-700 hover:underline dark:text-sky-300 dark:hover:text-amber-300",
                        title: codesTitle(bundle)
                      }, toDisplayString(bundleTitle(bundle)), 9, ["href", "title"]),
                      createVNode("div", { class: "mt-0.5 text-[10px] italic text-slate-500 dark:text-slate-400" }, toDisplayString(truncateText(bundle.url, 70)), 1),
                      bundleSubtitle(bundle) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-xs text-slate-600 dark:text-slate-300"
                      }, toDisplayString(truncateText(bundleSubtitle(bundle), 65)), 1)) : createCommentVNode("", true),
                      bundleShort(bundle) ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "text-[10px] font-normal text-slate-500 dark:text-slate-300"
                      }, toDisplayString(truncateText(bundleShort(bundle), 90)), 1)) : createCommentVNode("", true),
                      createVNode("div", { class: "mt-0.5 flex flex-wrap gap-1 text-[9px]" }, [
                        bundle.vendor_code ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "rounded-sm border border-slate-400 bg-violet-100 px-1 py-0.5 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300",
                          title: unref(t)("vendorCode")
                        }, toDisplayString(truncateText(bundle.vendor_code, 24)), 9, ["title"])) : createCommentVNode("", true),
                        bundle.sku ? (openBlock(), createBlock("span", {
                          key: 1,
                          class: "rounded-sm border border-slate-400 bg-blue-100 px-1 py-0.5 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300",
                          title: unref(t)("sku")
                        }, " SKU: " + toDisplayString(truncateText(bundle.sku, 24)), 9, ["title"])) : createCommentVNode("", true)
                      ]),
                      createVNode("div", { class: "mt-0.5 flex items-center gap-1 text-[9px]" }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-5 w-5 shrink-0 fill-current",
                          viewBox: "0 0 512 512"
                        }, [
                          createVNode("path", { d: "M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z" })
                        ])),
                        bundle.barcode ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "rounded-sm border border-slate-400 bg-slate-200 px-1 py-0.5 text-slate-700 dark:bg-slate-800 dark:text-slate-300",
                          title: unref(t)("barcode")
                        }, toDisplayString(truncateText(bundle.barcode, 24)), 9, ["title"])) : createCommentVNode("", true)
                      ])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-1 align-top" }, [
                    createVNode("div", {
                      title: supplierTitle(bundle)
                    }, [
                      companyTitle(bundle) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "font-semibold"
                      }, toDisplayString(truncateText(companyTitle(bundle), 38)), 1)) : createCommentVNode("", true),
                      shopTitle(bundle) ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "mt-1 font-normal text-slate-500 dark:text-slate-300"
                      }, toDisplayString(truncateText(shopTitle(bundle), 38)), 1)) : createCommentVNode("", true),
                      !companyTitle(bundle) && !shopTitle(bundle) ? (openBlock(), createBlock("span", {
                        key: 2,
                        class: "font-normal text-slate-400"
                      }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true)
                    ], 8, ["title"]),
                    createVNode("div", {
                      title: itemsTitle(bundle)
                    }, [
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode("div", { class: "rounded-full border bg-cyan-100 px-2 py-0.5 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300" }, toDisplayString(itemsCount(bundle)), 1),
                        createVNode("div", { class: "mt-1 font-normal text-emerald-700 dark:text-emerald-300" }, " [" + toDisplayString(activeItemsCount(bundle)) + "] ", 1)
                      ]),
                      Array.isArray(bundle.items) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "mt-1 font-normal text-[9px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(truncateText(bundle.items.slice(0, 2).map(itemTitle).join(", "), 70)), 1)) : createCommentVNode("", true)
                    ], 8, ["title"]),
                    createVNode("div", {
                      title: unref(t)("statistics"),
                      class: "flex flex-col gap-1 font-semibold"
                    }, [
                      createVNode("div", {
                        class: "flex flex-row items-center gap-1",
                        title: unref(t)("rating")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-3 h-3 fill-current shrink-0 text-red-500 dark:text-red-400",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("path", { d: "M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z" })
                        ])),
                        createVNode("span", null, toDisplayString(formatRating(bundle)) + " (" + toDisplayString(safeNumber(bundle.rating_count)) + ") ", 1)
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "flex flex-row items-center gap-1",
                        title: unref(t)("views")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-3 h-3 fill-current shrink-0 text-blue-600 dark:text-blue-300",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" })
                        ])),
                        createVNode("span", null, toDisplayString(safeNumber(bundle.views)), 1)
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "flex flex-row items-center gap-1",
                        title: unref(t)("likes")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-3 h-3 fill-current shrink-0 text-rose-400 dark:text-rose-300",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("path", { d: "M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z" }),
                          createVNode("path", { d: "M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z" })
                        ])),
                        createVNode("span", null, toDisplayString(safeNumber(bundle.likes_count)), 1)
                      ], 8, ["title"])
                    ], 8, ["title"])
                  ]),
                  createVNode("td", {
                    class: "whitespace-nowrap px-2 py-1 text-right align-top",
                    title: priceModeTitle(bundle)
                  }, [
                    createVNode("div", { class: "text-sm font-bold text-teal-700 dark:text-teal-300" }, toDisplayString(formatMoney(effectivePrice(bundle), bundle.currency)), 1),
                    bundle.has_old_price ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "mt-1 font-normal text-slate-400 line-through"
                    }, toDisplayString(formatMoney(bundle.old_price, bundle.currency)), 1)) : createCommentVNode("", true),
                    safeNumber(bundle.saving_percent) > 0 ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "mt-1 text-rose-600 dark:text-rose-300"
                    }, " −" + toDisplayString(safeNumber(bundle.saving_percent).toFixed(0)) + "% ", 1)) : createCommentVNode("", true),
                    createVNode("div", {
                      class: [
                        "mt-1 rounded px-2 py-0.5 border border-gray-400 text-[10px] text-center font-normal",
                        bundle.calculate_price ? "bg-cyan-100 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300" : "bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300"
                      ]
                    }, toDisplayString(bundle.calculate_price ? unref(t)("automaticPrice") : unref(t)("manualPrice")), 3)
                  ], 8, ["title"]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-1 text-center align-top" }, [
                    createVNode("div", {
                      class: [
                        "text-sm font-bold",
                        bundle.has_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300"
                      ]
                    }, toDisplayString(safeNumber(bundle.available_quantity)), 3),
                    createVNode("div", {
                      class: [
                        "mt-1 font-normal",
                        bundle.has_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-500 dark:text-rose-300"
                      ]
                    }, toDisplayString(bundle.has_stock ? unref(t)("inStock") : unref(t)("notAvailable")), 3)
                  ]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-1" }, [
                    createVNode("div", { class: "flex flex-col justify-center items-center gap-1" }, [
                      createVNode("div", { class: "flex flex-row items-center justify-center gap-1" }, [
                        createVNode(_sfc_main$5, {
                          isActive: bundle.activity,
                          title: bundle.activity ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleActivity: ($event) => emit(
                            "toggle-activity",
                            bundle
                          )
                        }, null, 8, ["isActive", "title", "onToggleActivity"]),
                        createVNode(_sfc_main$6, {
                          href: _ctx.route(
                            "admin.marketProductBundles.edit",
                            {
                              marketProductBundle: bundle.id
                            }
                          )
                        }, null, 8, ["href"]),
                        createVNode(_sfc_main$7, {
                          onDelete: ($event) => emit("delete", bundle)
                        }, null, 8, ["onDelete"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center justify-center gap-1" }, [
                        createVNode(_sfc_main$8, {
                          isActive: bundle.left,
                          title: bundle.left ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleLeft: ($event) => emit("toggle-left", bundle)
                        }, null, 8, ["isActive", "title", "onToggleLeft"]),
                        createVNode(_sfc_main$9, {
                          isActive: bundle.main,
                          title: bundle.main ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleMain: ($event) => emit("toggle-main", bundle)
                        }, null, 8, ["isActive", "title", "onToggleMain"]),
                        createVNode(_sfc_main$a, {
                          isActive: bundle.right,
                          title: bundle.right ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleRight: ($event) => emit("toggle-right", bundle)
                        }, null, 8, ["isActive", "title", "onToggleRight"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center justify-center gap-1" }, [
                        createVNode(_sfc_main$b, {
                          isActive: bundle.is_new,
                          title: bundle.is_new ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleIsNew: ($event) => emit("toggle-is-new", bundle)
                        }, null, 8, ["isActive", "title", "onToggleIsNew"]),
                        createVNode(_sfc_main$c, {
                          isActive: bundle.is_hit,
                          title: bundle.is_hit ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleIsHit: ($event) => emit("toggle-is-hit", bundle)
                        }, null, 8, ["isActive", "title", "onToggleIsHit"]),
                        createVNode(_sfc_main$d, {
                          isActive: bundle.is_sale,
                          title: bundle.is_sale ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleIsSale: ($event) => emit("toggle-is-sale", bundle)
                        }, null, 8, ["isActive", "title", "onToggleIsSale"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center justify-center gap-1" }, [
                        createVNode("div", { class: "flex flex-col items-center justify-center gap-1" }, [
                          createVNode("span", { class: "px-2 py-1 text-[10px] font-semibold text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(statusBadge(bundle.status).text), 1),
                          createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                            createVNode("span", {
                              class: ["rounded-sm border px-2 py-1 text-[9px] font-semibold", moderationBadge(bundle.moderation_status).class],
                              title: bundle.moderation_note ? `${bundle.moderation_note}${bundle.moderated_at ? ` [${formatDate(
                                bundle.moderated_at
                              )}]` : ""}` : null
                            }, toDisplayString(moderationBadge(bundle.moderation_status).text), 11, ["title"]),
                            createVNode(_sfc_main$e, {
                              isAdmin: __props.isAdmin,
                              status: (bundle == null ? void 0 : bundle.moderation_status) ?? 0,
                              initialNote: (bundle == null ? void 0 : bundle.moderation_note) || "",
                              mode: "toggle",
                              onSubmit: ({ status, note }) => emit(
                                "approve",
                                bundle,
                                status,
                                note
                              )
                            }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                          ])
                        ])
                      ])
                    ])
                  ]),
                  createVNode("td", { class: "w-px whitespace-nowrap px-2 py-2" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedBundles.includes(
                          bundle.id
                        ),
                        onChange: ($event) => emit(
                          "toggle-select",
                          bundle.id
                        )
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
        _push(`<div class="p-8 text-center text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProductBundle/Table/BundleTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "BundleCardGrid",
  __ssrInlineRender: true,
  props: {
    bundles: {
      type: Array,
      default: () => []
    },
    selectedBundles: {
      type: Array,
      default: () => []
    },
    isAdmin: {
      type: Boolean,
      default: false
    }
  },
  emits: [
    "toggle-left",
    "toggle-main",
    "toggle-right",
    "toggle-is-new",
    "toggle-is-hit",
    "toggle-is-sale",
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
    const emit = __emit;
    const localBundles = ref([]);
    const openedInfoBlocks = ref([]);
    watch(
      () => props.bundles,
      (bundles) => {
        localBundles.value = JSON.parse(
          JSON.stringify(bundles || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localBundles.value.map((bundle) => bundle.id)
      );
    };
    const allSelected = () => {
      return localBundles.value.length > 0 && localBundles.value.every((bundle) => {
        return props.selectedBundles.includes(bundle.id);
      });
    };
    const bundleTranslation = (bundle) => {
      return (bundle == null ? void 0 : bundle.translation) || {};
    };
    const bundleTitle = (bundle) => {
      var _a;
      return ((_a = bundleTranslation(bundle)) == null ? void 0 : _a.title) || `ID: ${bundle == null ? void 0 : bundle.id}`;
    };
    const bundleSubtitle = (bundle) => {
      var _a;
      return ((_a = bundleTranslation(bundle)) == null ? void 0 : _a.subtitle) || "";
    };
    const bundleShort = (bundle) => {
      var _a;
      return ((_a = bundleTranslation(bundle)) == null ? void 0 : _a.short) || "";
    };
    const relationTitle = (relation) => {
      var _a;
      return ((_a = relation == null ? void 0 : relation.translation) == null ? void 0 : _a.title) || "";
    };
    const companyTitle = (bundle) => {
      var _a;
      return relationTitle(bundle == null ? void 0 : bundle.company) || ((_a = bundle == null ? void 0 : bundle.company) == null ? void 0 : _a.legal_name) || "";
    };
    const shopTitle = (bundle) => {
      return relationTitle(bundle == null ? void 0 : bundle.shop);
    };
    const ownerName = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (bundle) => {
      const owner = bundle == null ? void 0 : bundle.owner;
      if (!owner) {
        return t("noData");
      }
      return [
        owner.name,
        owner.email
      ].filter(Boolean).join(" — ");
    };
    const ownerAvatar = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const isInfoBlockOpen = (bundleId) => {
      return openedInfoBlocks.value.includes(bundleId);
    };
    const toggleInfoBlock = (bundleId) => {
      if (isInfoBlockOpen(bundleId)) {
        openedInfoBlocks.value = openedInfoBlocks.value.filter(
          (id) => id !== bundleId
        );
        return;
      }
      openedInfoBlocks.value.push(bundleId);
    };
    const primaryImage = (bundle) => {
      if (!Array.isArray(bundle == null ? void 0 : bundle.images) || !bundle.images.length) {
        return null;
      }
      return [...bundle.images].sort((left, right) => {
        return Number((left == null ? void 0 : left.order) ?? 0) - Number((right == null ? void 0 : right.order) ?? 0);
      })[0];
    };
    const imageUrl = (bundle) => {
      const image = primaryImage(bundle);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "/storage/market/market_product_bundle_images/default-image.png";
    };
    const imageAlt = (bundle) => {
      var _a;
      return ((_a = primaryImage(bundle)) == null ? void 0 : _a.alt) || bundleTitle(bundle);
    };
    const imageTitle = (bundle) => {
      var _a;
      return ((_a = primaryImage(bundle)) == null ? void 0 : _a.caption) || bundleTitle(bundle);
    };
    const bundleItems = (bundle) => {
      if (Array.isArray(bundle == null ? void 0 : bundle.items)) {
        return bundle.items;
      }
      if (Array.isArray(bundle == null ? void 0 : bundle.active_items)) {
        return bundle.active_items;
      }
      return [];
    };
    const itemTitle = (item) => {
      var _a, _b, _c, _d;
      return ((_b = (_a = item == null ? void 0 : item.variant) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((_d = (_c = item == null ? void 0 : item.product) == null ? void 0 : _c.translation) == null ? void 0 : _d.title) || `ID: ${item == null ? void 0 : item.id}`;
    };
    const itemsTooltip = (bundle) => {
      return bundleItems(bundle).map((item) => {
        return `${itemTitle(item)} × ${Number((item == null ? void 0 : item.quantity) || 1)}`;
      }).join("\n");
    };
    const statusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const statusLabel = (status) => {
      return t(
        statusLabelKeyMap[status] || status || "no"
      );
    };
    const moderationBadge = (status) => {
      const value = Number(status ?? 0);
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
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const formatDate = (value) => {
      if (!value) {
        return "";
      }
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return date.toLocaleDateString(
        locale.value || "ru-RU",
        {
          year: "numeric",
          month: "long",
          day: "numeric"
        }
      );
    };
    const truncateText = (value, maxLength = 80) => {
      if (!value) {
        return "";
      }
      const text = String(value);
      return text.length > maxLength ? `${text.slice(0, maxLength).trimEnd()}…` : text;
    };
    const formatMoney = (value, currency) => {
      if (value === null || value === void 0 || value === "") {
        return "—";
      }
      const amount = safeNumber(value);
      const precision = Number.isFinite(Number(currency == null ? void 0 : currency.precision)) ? Number(currency.precision) : 2;
      const thousandsSeparator = (currency == null ? void 0 : currency.thousands_sep) ?? " ";
      const decimalSeparator = (currency == null ? void 0 : currency.decimal_sep) ?? ".";
      const parts = amount.toFixed(precision).split(".");
      const integerPart = parts[0].replace(
        /\B(?=(\d{3})+(?!\d))/g,
        thousandsSeparator
      );
      const formatted = precision > 0 ? `${integerPart}${decimalSeparator}${parts[1]}` : integerPart;
      const symbol = String(
        (currency == null ? void 0 : currency.symbol) || (currency == null ? void 0 : currency.code) || ""
      ).trim();
      if (!symbol) {
        return formatted;
      }
      return (currency == null ? void 0 : currency.symbol_first) ? `${symbol}${formatted}` : `${formatted} ${symbol}`;
    };
    const effectivePrice = (bundle) => {
      return (bundle == null ? void 0 : bundle.effective_price) ?? (bundle == null ? void 0 : bundle.calculated_price) ?? (bundle == null ? void 0 : bundle.price) ?? 0;
    };
    const formatRating = (bundle) => {
      return safeNumber(bundle == null ? void 0 : bundle.rating_avg).toFixed(1);
    };
    const bundlePublicUrl = (bundle) => {
      return `/market/bundles/${encodeURIComponent(
        (bundle == null ? void 0 : bundle.url) || ""
      )}`;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-400 bg-white shadow-lg dark:border-slate-500 dark:bg-slate-700" }, _attrs))}><div class="flex items-center justify-between border-b border-slate-400 px-3 py-2 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedBundles.length)}</div>`);
      if (localBundles.value.length) {
        _push(`<label class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localBundles.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localBundles.value,
          "onUpdate:modelValue": ($event) => localBundles.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".handle",
          class: "grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: bundle }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<article class="relative flex h-full flex-col overflow-hidden rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80"${_scopeId}><header class="flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500"${_scopeId}><div class="flex items-center gap-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100"${ssrRenderAttr("title", `[${bundle.sort}] / ${formatDate(bundle.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(bundle.id)}</div><button type="button" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"${ssrRenderAttr("title", isInfoBlockOpen(bundle.id) ? unref(t)("hideOwner") : unref(t)("showOwner"))}${_scopeId}><svg class="${ssrRenderClass([{
                "rotate-180": isInfoBlockOpen(bundle.id)
              }, "h-4 w-4 transition-transform"])}" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"${_scopeId}></path></svg></button></div><div class="flex items-center gap-2"${_scopeId}><span class="${ssrRenderClass([moderationBadge(bundle.moderation_status).class, "rounded-sm border px-2 py-0.5 text-[10px] font-semibold"])}"${ssrRenderAttr("title", bundle.moderation_note || null)}${_scopeId}>${ssrInterpolate(moderationBadge(bundle.moderation_status).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedBundles.includes(bundle.id)) ? " checked" : ""}${_scopeId}></div></header><div style="${ssrRenderStyle(isInfoBlockOpen(bundle.id) ? null : { display: "none" })}" class="border-b border-dashed border-slate-300 px-3 py-2 text-center dark:border-slate-600"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(bundle))}${ssrRenderAttr("title", ownerTitle(bundle))}${ssrRenderAttr("alt", unref(t)("owner"))} class="mx-auto h-12 w-12 rounded-full border border-slate-300 object-cover dark:border-slate-600"${_scopeId}><div class="mt-1 truncate text-[11px] font-semibold text-slate-700 dark:text-slate-100"${_scopeId}>${ssrInterpolate(ownerName(bundle))}</div>`);
              if (ownerEmail(bundle)) {
                _push2(`<div class="truncate text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(ownerEmail(bundle))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(formatDate(bundle.show_from_at || bundle.published_at))} `);
              if (bundle.show_to_at) {
                _push2(`<span${_scopeId}> — ${ssrInterpolate(formatDate(bundle.show_to_at))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="mt-0.5 flex flex-col justify-center gap-1 text-[9px]"${_scopeId}>`);
              if (bundle.vendor_code) {
                _push2(`<div class="rounded-sm border border-slate-400 bg-violet-100 px-1 py-0.5 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300"${ssrRenderAttr("title", unref(t)("vendorCode"))}${_scopeId}>${ssrInterpolate(truncateText(bundle.vendor_code, 24))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (bundle.sku) {
                _push2(`<div class="rounded-sm border border-slate-400 bg-blue-100 px-1 py-0.5 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"${ssrRenderAttr("title", unref(t)("sku"))}${_scopeId}> SKU: ${ssrInterpolate(truncateText(bundle.sku, 24))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="mt-0.5 flex items-center justify-center gap-1 text-[9px]"${_scopeId}><svg class="h-5 w-5 shrink-0 fill-current" viewBox="0 0 512 512"${_scopeId}><path d="M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z"${_scopeId}></path></svg>`);
              if (bundle.barcode) {
                _push2(`<span class="rounded-sm border border-slate-400 bg-slate-200 px-1 py-0.5 text-slate-700 dark:bg-slate-800 dark:text-slate-300"${ssrRenderAttr("title", unref(t)("barcode"))}${_scopeId}>${ssrInterpolate(truncateText(bundle.barcode, 24))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div></div><div class="relative w-full overflow-hidden bg-slate-200 dark:bg-slate-900"${_scopeId}><img${ssrRenderAttr("src", imageUrl(bundle))}${ssrRenderAttr("alt", imageAlt(bundle))}${ssrRenderAttr("title", imageTitle(bundle))} class="aspect-[4/3] w-full object-cover"${_scopeId}><div class="absolute left-2 top-2 flex flex-wrap gap-1"${_scopeId}>`);
              if (bundle.is_new) {
                _push2(`<span class="rounded-sm border border-teal-700 bg-teal-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-teal-700 dark:border-teal-300 dark:bg-teal-800/90 dark:text-teal-300"${_scopeId}> NEW </span>`);
              } else {
                _push2(`<!---->`);
              }
              if (bundle.is_hit) {
                _push2(`<span class="rounded-sm border border-yellow-700 bg-yellow-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-yellow-700 dark:border-yellow-300 dark:bg-yellow-800/90 dark:text-yellow-300"${_scopeId}> HIT </span>`);
              } else {
                _push2(`<!---->`);
              }
              if (bundle.is_sale) {
                _push2(`<span class="rounded-sm border border-pink-700 bg-pink-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-pink-700 dark:border-pink-300 dark:bg-pink-800/90 dark:text-pink-300"${_scopeId}> SALE </span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (bundle.images_count) {
                _push2(`<div class="absolute bottom-2 right-2 rounded-sm bg-slate-900/70 px-1.5 py-0.5 text-[9px] font-semibold text-white"${_scopeId}>${ssrInterpolate(bundle.images_count)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="flex flex-1 flex-col space-y-1 p-2"${_scopeId}><a${ssrRenderAttr("href", bundlePublicUrl(bundle))} target="_blank" rel="noopener noreferrer" class="text-center text-xs font-semibold text-sky-700 hover:text-amber-700 hover:underline dark:text-sky-300 dark:hover:text-amber-300"${ssrRenderAttr("title", bundleShort(bundle) || bundleSubtitle(bundle) || bundleTitle(bundle))}${_scopeId}>${ssrInterpolate(truncateText(bundleTitle(bundle), 80))}</a>`);
              if (bundleSubtitle(bundle)) {
                _push2(`<div class="text-center text-[10px] font-semibold text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(bundleSubtitle(bundle), 80))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="truncate text-center text-[10px] italic text-slate-500 dark:text-slate-400"${ssrRenderAttr("title", bundle.url)}${_scopeId}>${ssrInterpolate(bundle.url)}</div><div class="text-center"${_scopeId}><div class="${ssrRenderClass([bundle.calculate_price ? "text-violet-600 dark:text-violet-300" : "text-blue-600 dark:text-blue-300", "text-[9px] font-semibold uppercase tracking-wide"])}"${_scopeId}>${ssrInterpolate(bundle.calculate_price ? unref(t)("automaticPrice") : unref(t)("manualPrice"))}</div><div class="flex items-center justify-center gap-2"${_scopeId}>`);
              if (bundle.has_old_price) {
                _push2(`<span class="text-xs text-slate-400 line-through"${_scopeId}>${ssrInterpolate(formatMoney(bundle.old_price, bundle.currency))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<span class="text-sm font-bold text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(formatMoney(effectivePrice(bundle), bundle.currency))}</span></div>`);
              if (safeNumber(bundle.saving_percent) > 0) {
                _push2(`<div class="text-[10px] font-semibold text-pink-600 dark:text-pink-300"${_scopeId}> −${ssrInterpolate(bundle.saving_percent)}% </div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="grid grid-cols-2 gap-1 text-center text-[10px]"${_scopeId}><div class="rounded-sm border border-fuchsia-300 bg-fuchsia-50 px-1 py-1 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-300"${ssrRenderAttr("title", itemsTooltip(bundle))}${_scopeId}><div class="font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("compound"))}</div><div${_scopeId}>${ssrInterpolate(bundle.items_count ?? bundleItems(bundle).length)} `);
              if (bundle.active_items_count !== void 0) {
                _push2(`<span class="text-emerald-600 dark:text-emerald-300"${_scopeId}> / ${ssrInterpolate(bundle.active_items_count)}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div><div class="${ssrRenderClass([bundle.has_stock ? "border-emerald-300 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300" : "border-rose-300 bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300", "rounded-sm border px-1 py-1"])}"${_scopeId}><div class="font-semibold"${_scopeId}>${ssrInterpolate(bundle.has_stock ? unref(t)("inStock") : unref(t)("outOfStock"))}</div><div${_scopeId}>${ssrInterpolate(bundle.available_quantity ?? 0)}</div></div></div>`);
              if (bundleItems(bundle).length) {
                _push2(`<div class="space-y-0.5 rounded-sm border border-slate-300 bg-white/60 p-1 dark:border-slate-600 dark:bg-slate-900/30"${_scopeId}><!--[-->`);
                ssrRenderList(bundleItems(bundle).slice(0, 3), (item) => {
                  _push2(`<div class="flex items-center justify-between gap-2 text-[9px] text-slate-600 dark:text-slate-300"${_scopeId}><span class="truncate"${ssrRenderAttr("title", itemTitle(item))}${_scopeId}>${ssrInterpolate(itemTitle(item))}</span><span class="shrink-0 font-semibold"${_scopeId}> × ${ssrInterpolate(item.quantity ?? 1)}</span></div>`);
                });
                _push2(`<!--]-->`);
                if (bundleItems(bundle).length > 3) {
                  _push2(`<div class="text-center text-[9px] text-slate-400"${_scopeId}> + ${ssrInterpolate(bundleItems(bundle).length - 3)}</div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (shopTitle(bundle) || companyTitle(bundle)) {
                _push2(`<div class="truncate text-center text-[10px] text-amber-700 dark:text-amber-300"${ssrRenderAttr("title", [companyTitle(bundle), shopTitle(bundle)].filter(Boolean).join(" / "))}${_scopeId}>${ssrInterpolate(shopTitle(bundle) || companyTitle(bundle))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (bundleShort(bundle)) {
                _push2(`<div class="line-clamp-3 text-center text-[11px] text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(bundleShort(bundle), 150))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="grid grid-cols-3 gap-1 text-center text-[10px] font-semibold"${_scopeId}><div class="flex flex-col items-center gap-1"${ssrRenderAttr("title", unref(t)("rating"))}${_scopeId}><svg class="w-3 h-3 fill-current shrink-0 text-red-500 dark:text-red-400" viewBox="0 0 24 24"${_scopeId}><path d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"${_scopeId}></path></svg><span${_scopeId}>${ssrInterpolate(formatRating(bundle))} (${ssrInterpolate(safeNumber(bundle.rating_count))}) </span></div><div class="flex flex-col items-center gap-1"${ssrRenderAttr("title", unref(t)("views"))}${_scopeId}><svg class="w-3 h-3 fill-current shrink-0 text-blue-600 dark:text-blue-300" viewBox="0 0 16 16"${_scopeId}><path d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"${_scopeId}></path></svg><span${_scopeId}>${ssrInterpolate(safeNumber(bundle.views))}</span></div><div class="flex flex-col items-center gap-1"${ssrRenderAttr("title", unref(t)("likes"))}${_scopeId}><svg class="w-3 h-3 fill-current shrink-0 text-rose-400 dark:text-rose-300" viewBox="0 0 24 24"${_scopeId}><path d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"${_scopeId}></path><path d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"${_scopeId}></path></svg><span${_scopeId}>${ssrInterpolate(safeNumber(bundle.likes_count))}</span></div></div><div class="text-center text-[11px] font-semibold text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(unref(t)("status"))}: ${ssrInterpolate(statusLabel(bundle.status))}</div><div class="flex justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(bundle.moderation_status).class, "rounded-sm border px-2 py-1 text-[10px] font-semibold"])}"${_scopeId}>${ssrInterpolate(moderationBadge(bundle.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$e, {
                isAdmin: __props.isAdmin,
                status: (bundle == null ? void 0 : bundle.moderation_status) ?? 0,
                initialNote: (bundle == null ? void 0 : bundle.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emit("approve", bundle, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><footer class="border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500"${_scopeId}><div class="flex flex-wrap items-center justify-center gap-2"${_scopeId}><div class="flex items-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isActive: bundle.left,
                title: bundle.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => emit("toggle-left", bundle)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: bundle.main,
                title: bundle.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => emit("toggle-main", bundle)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                isActive: bundle.right,
                title: bundle.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => emit("toggle-right", bundle)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex items-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$b, {
                isActive: bundle.is_new,
                title: bundle.is_new ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleIsNew: ($event) => emit("toggle-is-new", bundle)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$c, {
                isActive: bundle.is_hit,
                title: bundle.is_hit ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleIsHit: ($event) => emit("toggle-is-hit", bundle)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$d, {
                isActive: bundle.is_sale,
                title: bundle.is_sale ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleIsSale: ($event) => emit("toggle-is-sale", bundle)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex items-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: bundle.activity,
                title: bundle.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", bundle)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route(
                  "admin.marketProductBundles.edit",
                  {
                    marketProductBundle: bundle.id
                  }
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => emit("delete", bundle)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></footer></article>`);
            } else {
              return [
                createVNode("article", { class: "relative flex h-full flex-col overflow-hidden rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80" }, [
                  createVNode("header", { class: "flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center gap-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100",
                        title: unref(t)("dragDrop")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-4 w-4",
                          fill: "currentColor",
                          viewBox: "0 0 20 20"
                        }, [
                          createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                        ]))
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100",
                        title: `[${bundle.sort}] / ${formatDate(bundle.published_at)}`
                      }, " ID: " + toDisplayString(bundle.id), 9, ["title"]),
                      createVNode("button", {
                        type: "button",
                        class: "text-slate-400 hover:text-blue-600 dark:hover:text-blue-300",
                        title: isInfoBlockOpen(bundle.id) ? unref(t)("hideOwner") : unref(t)("showOwner"),
                        onClick: withModifiers(($event) => toggleInfoBlock(bundle.id), ["prevent"])
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: ["h-4 w-4 transition-transform", {
                            "rotate-180": isInfoBlockOpen(bundle.id)
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
                    createVNode("div", { class: "flex items-center gap-2" }, [
                      createVNode("span", {
                        class: ["rounded-sm border px-2 py-0.5 text-[10px] font-semibold", moderationBadge(bundle.moderation_status).class],
                        title: bundle.moderation_note || null
                      }, toDisplayString(moderationBadge(bundle.moderation_status).text), 11, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedBundles.includes(bundle.id),
                        onChange: ($event) => emit("toggle-select", bundle.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  withDirectives(createVNode("div", { class: "border-b border-dashed border-slate-300 px-3 py-2 text-center dark:border-slate-600" }, [
                    createVNode("img", {
                      src: ownerAvatar(bundle),
                      title: ownerTitle(bundle),
                      alt: unref(t)("owner"),
                      class: "mx-auto h-12 w-12 rounded-full border border-slate-300 object-cover dark:border-slate-600"
                    }, null, 8, ["src", "title", "alt"]),
                    createVNode("div", { class: "mt-1 truncate text-[11px] font-semibold text-slate-700 dark:text-slate-100" }, toDisplayString(ownerName(bundle)), 1),
                    ownerEmail(bundle) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "truncate text-[10px] text-slate-500 dark:text-slate-300"
                    }, toDisplayString(ownerEmail(bundle)), 1)) : createCommentVNode("", true),
                    createVNode("div", { class: "mt-1 text-[10px] text-slate-500 dark:text-slate-300" }, [
                      createTextVNode(toDisplayString(formatDate(bundle.show_from_at || bundle.published_at)) + " ", 1),
                      bundle.show_to_at ? (openBlock(), createBlock("span", { key: 0 }, " — " + toDisplayString(formatDate(bundle.show_to_at)), 1)) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "mt-0.5 flex flex-col justify-center gap-1 text-[9px]" }, [
                      bundle.vendor_code ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "rounded-sm border border-slate-400 bg-violet-100 px-1 py-0.5 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300",
                        title: unref(t)("vendorCode")
                      }, toDisplayString(truncateText(bundle.vendor_code, 24)), 9, ["title"])) : createCommentVNode("", true),
                      bundle.sku ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "rounded-sm border border-slate-400 bg-blue-100 px-1 py-0.5 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300",
                        title: unref(t)("sku")
                      }, " SKU: " + toDisplayString(truncateText(bundle.sku, 24)), 9, ["title"])) : createCommentVNode("", true),
                      createVNode("div", { class: "mt-0.5 flex items-center justify-center gap-1 text-[9px]" }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-5 w-5 shrink-0 fill-current",
                          viewBox: "0 0 512 512"
                        }, [
                          createVNode("path", { d: "M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z" })
                        ])),
                        bundle.barcode ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "rounded-sm border border-slate-400 bg-slate-200 px-1 py-0.5 text-slate-700 dark:bg-slate-800 dark:text-slate-300",
                          title: unref(t)("barcode")
                        }, toDisplayString(truncateText(bundle.barcode, 24)), 9, ["title"])) : createCommentVNode("", true)
                      ])
                    ])
                  ], 512), [
                    [vShow, isInfoBlockOpen(bundle.id)]
                  ]),
                  createVNode("div", { class: "relative w-full overflow-hidden bg-slate-200 dark:bg-slate-900" }, [
                    createVNode("img", {
                      src: imageUrl(bundle),
                      alt: imageAlt(bundle),
                      title: imageTitle(bundle),
                      class: "aspect-[4/3] w-full object-cover"
                    }, null, 8, ["src", "alt", "title"]),
                    createVNode("div", { class: "absolute left-2 top-2 flex flex-wrap gap-1" }, [
                      bundle.is_new ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "rounded-sm border border-teal-700 bg-teal-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-teal-700 dark:border-teal-300 dark:bg-teal-800/90 dark:text-teal-300"
                      }, " NEW ")) : createCommentVNode("", true),
                      bundle.is_hit ? (openBlock(), createBlock("span", {
                        key: 1,
                        class: "rounded-sm border border-yellow-700 bg-yellow-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-yellow-700 dark:border-yellow-300 dark:bg-yellow-800/90 dark:text-yellow-300"
                      }, " HIT ")) : createCommentVNode("", true),
                      bundle.is_sale ? (openBlock(), createBlock("span", {
                        key: 2,
                        class: "rounded-sm border border-pink-700 bg-pink-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-pink-700 dark:border-pink-300 dark:bg-pink-800/90 dark:text-pink-300"
                      }, " SALE ")) : createCommentVNode("", true)
                    ]),
                    bundle.images_count ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "absolute bottom-2 right-2 rounded-sm bg-slate-900/70 px-1.5 py-0.5 text-[9px] font-semibold text-white"
                    }, toDisplayString(bundle.images_count), 1)) : createCommentVNode("", true)
                  ]),
                  createVNode("div", { class: "flex flex-1 flex-col space-y-1 p-2" }, [
                    createVNode("a", {
                      href: bundlePublicUrl(bundle),
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-center text-xs font-semibold text-sky-700 hover:text-amber-700 hover:underline dark:text-sky-300 dark:hover:text-amber-300",
                      title: bundleShort(bundle) || bundleSubtitle(bundle) || bundleTitle(bundle)
                    }, toDisplayString(truncateText(bundleTitle(bundle), 80)), 9, ["href", "title"]),
                    bundleSubtitle(bundle) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-center text-[10px] font-semibold text-slate-700 dark:text-slate-300"
                    }, toDisplayString(truncateText(bundleSubtitle(bundle), 80)), 1)) : createCommentVNode("", true),
                    createVNode("div", {
                      class: "truncate text-center text-[10px] italic text-slate-500 dark:text-slate-400",
                      title: bundle.url
                    }, toDisplayString(bundle.url), 9, ["title"]),
                    createVNode("div", { class: "text-center" }, [
                      createVNode("div", {
                        class: ["text-[9px] font-semibold uppercase tracking-wide", bundle.calculate_price ? "text-violet-600 dark:text-violet-300" : "text-blue-600 dark:text-blue-300"]
                      }, toDisplayString(bundle.calculate_price ? unref(t)("automaticPrice") : unref(t)("manualPrice")), 3),
                      createVNode("div", { class: "flex items-center justify-center gap-2" }, [
                        bundle.has_old_price ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "text-xs text-slate-400 line-through"
                        }, toDisplayString(formatMoney(bundle.old_price, bundle.currency)), 1)) : createCommentVNode("", true),
                        createVNode("span", { class: "text-sm font-bold text-teal-700 dark:text-teal-300" }, toDisplayString(formatMoney(effectivePrice(bundle), bundle.currency)), 1)
                      ]),
                      safeNumber(bundle.saving_percent) > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] font-semibold text-pink-600 dark:text-pink-300"
                      }, " −" + toDisplayString(bundle.saving_percent) + "% ", 1)) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "grid grid-cols-2 gap-1 text-center text-[10px]" }, [
                      createVNode("div", {
                        class: "rounded-sm border border-fuchsia-300 bg-fuchsia-50 px-1 py-1 text-fuchsia-700 dark:bg-fuchsia-900/30 dark:text-fuchsia-300",
                        title: itemsTooltip(bundle)
                      }, [
                        createVNode("div", { class: "font-semibold" }, toDisplayString(unref(t)("compound")), 1),
                        createVNode("div", null, [
                          createTextVNode(toDisplayString(bundle.items_count ?? bundleItems(bundle).length) + " ", 1),
                          bundle.active_items_count !== void 0 ? (openBlock(), createBlock("span", {
                            key: 0,
                            class: "text-emerald-600 dark:text-emerald-300"
                          }, " / " + toDisplayString(bundle.active_items_count), 1)) : createCommentVNode("", true)
                        ])
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: ["rounded-sm border px-1 py-1", bundle.has_stock ? "border-emerald-300 bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300" : "border-rose-300 bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300"]
                      }, [
                        createVNode("div", { class: "font-semibold" }, toDisplayString(bundle.has_stock ? unref(t)("inStock") : unref(t)("outOfStock")), 1),
                        createVNode("div", null, toDisplayString(bundle.available_quantity ?? 0), 1)
                      ], 2)
                    ]),
                    bundleItems(bundle).length ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "space-y-0.5 rounded-sm border border-slate-300 bg-white/60 p-1 dark:border-slate-600 dark:bg-slate-900/30"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(bundleItems(bundle).slice(0, 3), (item) => {
                        return openBlock(), createBlock("div", {
                          key: item.id,
                          class: "flex items-center justify-between gap-2 text-[9px] text-slate-600 dark:text-slate-300"
                        }, [
                          createVNode("span", {
                            class: "truncate",
                            title: itemTitle(item)
                          }, toDisplayString(itemTitle(item)), 9, ["title"]),
                          createVNode("span", { class: "shrink-0 font-semibold" }, " × " + toDisplayString(item.quantity ?? 1), 1)
                        ]);
                      }), 128)),
                      bundleItems(bundle).length > 3 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-center text-[9px] text-slate-400"
                      }, " + " + toDisplayString(bundleItems(bundle).length - 3), 1)) : createCommentVNode("", true)
                    ])) : createCommentVNode("", true),
                    shopTitle(bundle) || companyTitle(bundle) ? (openBlock(), createBlock("div", {
                      key: 2,
                      class: "truncate text-center text-[10px] text-amber-700 dark:text-amber-300",
                      title: [companyTitle(bundle), shopTitle(bundle)].filter(Boolean).join(" / ")
                    }, toDisplayString(shopTitle(bundle) || companyTitle(bundle)), 9, ["title"])) : createCommentVNode("", true),
                    bundleShort(bundle) ? (openBlock(), createBlock("div", {
                      key: 3,
                      class: "line-clamp-3 text-center text-[11px] text-slate-600 dark:text-slate-300"
                    }, toDisplayString(truncateText(bundleShort(bundle), 150)), 1)) : createCommentVNode("", true),
                    createVNode("div", { class: "grid grid-cols-3 gap-1 text-center text-[10px] font-semibold" }, [
                      createVNode("div", {
                        class: "flex flex-col items-center gap-1",
                        title: unref(t)("rating")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-3 h-3 fill-current shrink-0 text-red-500 dark:text-red-400",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("path", { d: "M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z" })
                        ])),
                        createVNode("span", null, toDisplayString(formatRating(bundle)) + " (" + toDisplayString(safeNumber(bundle.rating_count)) + ") ", 1)
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "flex flex-col items-center gap-1",
                        title: unref(t)("views")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-3 h-3 fill-current shrink-0 text-blue-600 dark:text-blue-300",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" })
                        ])),
                        createVNode("span", null, toDisplayString(safeNumber(bundle.views)), 1)
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "flex flex-col items-center gap-1",
                        title: unref(t)("likes")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-3 h-3 fill-current shrink-0 text-rose-400 dark:text-rose-300",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("path", { d: "M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z" }),
                          createVNode("path", { d: "M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z" })
                        ])),
                        createVNode("span", null, toDisplayString(safeNumber(bundle.likes_count)), 1)
                      ], 8, ["title"])
                    ]),
                    createVNode("div", { class: "text-center text-[11px] font-semibold text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(unref(t)("status")) + ": " + toDisplayString(statusLabel(bundle.status)), 1),
                    createVNode("div", { class: "flex justify-center gap-1" }, [
                      createVNode("span", {
                        class: ["rounded-sm border px-2 py-1 text-[10px] font-semibold", moderationBadge(bundle.moderation_status).class]
                      }, toDisplayString(moderationBadge(bundle.moderation_status).text), 3),
                      createVNode(_sfc_main$e, {
                        isAdmin: __props.isAdmin,
                        status: (bundle == null ? void 0 : bundle.moderation_status) ?? 0,
                        initialNote: (bundle == null ? void 0 : bundle.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => emit("approve", bundle, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("footer", { class: "border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex flex-wrap items-center justify-center gap-2" }, [
                      createVNode("div", { class: "flex items-center gap-1" }, [
                        createVNode(_sfc_main$8, {
                          isActive: bundle.left,
                          title: bundle.left ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleLeft: ($event) => emit("toggle-left", bundle)
                        }, null, 8, ["isActive", "title", "onToggleLeft"]),
                        createVNode(_sfc_main$9, {
                          isActive: bundle.main,
                          title: bundle.main ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleMain: ($event) => emit("toggle-main", bundle)
                        }, null, 8, ["isActive", "title", "onToggleMain"]),
                        createVNode(_sfc_main$a, {
                          isActive: bundle.right,
                          title: bundle.right ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleRight: ($event) => emit("toggle-right", bundle)
                        }, null, 8, ["isActive", "title", "onToggleRight"])
                      ]),
                      createVNode("div", { class: "flex items-center gap-1" }, [
                        createVNode(_sfc_main$b, {
                          isActive: bundle.is_new,
                          title: bundle.is_new ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleIsNew: ($event) => emit("toggle-is-new", bundle)
                        }, null, 8, ["isActive", "title", "onToggleIsNew"]),
                        createVNode(_sfc_main$c, {
                          isActive: bundle.is_hit,
                          title: bundle.is_hit ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleIsHit: ($event) => emit("toggle-is-hit", bundle)
                        }, null, 8, ["isActive", "title", "onToggleIsHit"]),
                        createVNode(_sfc_main$d, {
                          isActive: bundle.is_sale,
                          title: bundle.is_sale ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleIsSale: ($event) => emit("toggle-is-sale", bundle)
                        }, null, 8, ["isActive", "title", "onToggleIsSale"])
                      ]),
                      createVNode("div", { class: "flex items-center gap-1" }, [
                        createVNode(_sfc_main$5, {
                          isActive: bundle.activity,
                          title: bundle.activity ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleActivity: ($event) => emit("toggle-activity", bundle)
                        }, null, 8, ["isActive", "title", "onToggleActivity"]),
                        createVNode(_sfc_main$6, {
                          href: _ctx.route(
                            "admin.marketProductBundles.edit",
                            {
                              marketProductBundle: bundle.id
                            }
                          )
                        }, null, 8, ["href"]),
                        createVNode(_sfc_main$7, {
                          onDelete: ($event) => emit("delete", bundle)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProductBundle/View/BundleCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: {
      type: String,
      default: ""
    },
    availableLocales: {
      type: Array,
      default: () => []
    },
    adminMarketProductBundlesProcessingMode: {
      type: String,
      default: "frontend"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    adminMarketProductBundlesPerPage: {
      type: Number,
      default: 6
    },
    adminMarketProductBundlesDefaultSort: {
      type: String,
      default: "idDesc"
    },
    bundles: {
      type: [Array, Object],
      default: () => []
    },
    bundlesCount: {
      type: Number,
      default: 0
    },
    sortParam: {
      type: String,
      default: ""
    },
    search: {
      type: String,
      default: ""
    },
    errors: {
      type: Object,
      default: () => ({})
    }
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
    const getBundleTranslation = (bundle) => {
      return (bundle == null ? void 0 : bundle.translation) || {};
    };
    const getBundleTitle = (bundle) => {
      var _a;
      return ((_a = getBundleTranslation(bundle)) == null ? void 0 : _a.title) || `ID: ${bundle == null ? void 0 : bundle.id}`;
    };
    const getBundleSubtitle = (bundle) => {
      var _a;
      return ((_a = getBundleTranslation(bundle)) == null ? void 0 : _a.subtitle) || "";
    };
    const getBundleShort = (bundle) => {
      var _a;
      return ((_a = getBundleTranslation(bundle)) == null ? void 0 : _a.short) || "";
    };
    const getBundleDescription = (bundle) => {
      var _a;
      return ((_a = getBundleTranslation(bundle)) == null ? void 0 : _a.description) || "";
    };
    const getCompanyTitle = (bundle) => {
      var _a, _b, _c;
      return ((_b = (_a = bundle == null ? void 0 : bundle.company) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((_c = bundle == null ? void 0 : bundle.company) == null ? void 0 : _c.legal_name) || "";
    };
    const getShopTitle = (bundle) => {
      var _a, _b;
      return ((_b = (_a = bundle == null ? void 0 : bundle.shop) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getOwnerName = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.owner) == null ? void 0 : _a.name) || "";
    };
    const getOwnerEmail = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.owner) == null ? void 0 : _a.email) || "";
    };
    const normalize = (value) => {
      return (value ?? "").toString().trim().toLowerCase();
    };
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
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_market_product_bundles") || "cards"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_market_product_bundles",
        value
      );
    });
    const itemsPerPage = ref(
      props.adminMarketProductBundlesPerPage || 6
    );
    watch(itemsPerPage, (newValue, oldValue) => {
      if (newValue === oldValue) {
        return;
      }
      router.put(
        route("admin.settings.updateAdminCountMarketProductBundles"),
        {
          value: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(
              `Показ ${newValue} комплектов товаров на странице.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.value) || "Ошибка обновления количества комплектов товаров."
            );
          }
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminMarketProductBundlesDefaultSort || "idDesc"
    );
    watch(sortParam, (newValue, oldValue) => {
      if (newValue === oldValue) {
        return;
      }
      router.put(
        route("admin.settings.updateAdminSortMarketProductBundles"),
        {
          value: newValue
        },
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
                  sort: newValue || void 0,
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
              "Сортировка комплектов товаров успешно изменена."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.value) || "Ошибка обновления сортировки комплектов товаров."
            );
          }
        }
      );
    });
    const bundlesList = computed(() => {
      var _a, _b, _c, _d;
      if (Array.isArray(props.bundles)) {
        return props.bundles;
      }
      if (Array.isArray((_a = props.bundles) == null ? void 0 : _a.data)) {
        return props.bundles.data;
      }
      if (Array.isArray((_c = (_b = props.bundles) == null ? void 0 : _b.data) == null ? void 0 : _c.data)) {
        return props.bundles.data.data;
      }
      if (Array.isArray((_d = props.bundles) == null ? void 0 : _d.resource)) {
        return props.bundles.resource;
      }
      return [];
    });
    const localBundles = ref([]);
    watch(
      bundlesList,
      (newValue) => {
        localBundles.value = JSON.parse(
          JSON.stringify(newValue || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const showConfirmDeleteModal = ref(false);
    const bundleToDeleteId = ref(null);
    const bundleToDeleteTitle = ref("");
    const confirmDelete = (productOrId, title = null) => {
      if (typeof productOrId === "object") {
        bundleToDeleteId.value = productOrId == null ? void 0 : productOrId.id;
        bundleToDeleteTitle.value = title || getBundleTitle(productOrId);
      } else {
        bundleToDeleteId.value = productOrId;
        bundleToDeleteTitle.value = title || `ID: ${productOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      bundleToDeleteId.value = null;
      bundleToDeleteTitle.value = "";
    };
    const deleteBundle = () => {
      if (bundleToDeleteId.value === null) {
        return;
      }
      const id = bundleToDeleteId.value;
      const title = bundleToDeleteTitle.value;
      router.delete(
        route(
          "admin.marketProductBundles.destroy",
          {
            marketProductBundle: id
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Комплект товаров "${title || `ID: ${id}`}" удалён.`
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0];
            const message = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[firstKey]) || "Ошибка при удалении комплекта товаров.";
            toast.error(
              `${message} (Комплект товаров: ${title || `ID: ${id}`})`
            );
          },
          onFinish: closeModal
        }
      );
    };
    const patchLocalBundle = (productId, callback) => {
      const index = localBundles.value.findIndex(
        (bundle) => Number(bundle.id) === Number(productId)
      );
      if (index === -1) {
        return;
      }
      callback(localBundles.value[index]);
    };
    const toggleActivity = (bundle) => {
      if (!(bundle == null ? void 0 : bundle.id)) {
        return;
      }
      const activity = !bundle.activity;
      const title = getBundleTitle(bundle);
      router.put(
        route(
          "admin.actions.marketProductBundles.updateActivity",
          {
            marketProductBundle: bundle.id
          }
        ),
        {
          activity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBundle(bundle.id, (item) => {
              item.activity = activity;
              item.is_active = activity;
            });
            toast.success(
              activity ? `Комплект товаров "${title}" активирован.` : `Комплект товаров "${title}" деактивирован.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности комплекта товаров "${title}".`
            );
          }
        }
      );
    };
    const toggleLeft = (bundle) => {
      if (!(bundle == null ? void 0 : bundle.id)) {
        return;
      }
      const left = !bundle.left;
      const title = getBundleTitle(bundle);
      router.put(
        route(
          "admin.actions.marketProductBundles.updateLeft",
          {
            marketProductBundle: bundle.id
          }
        ),
        {
          left
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBundle(bundle.id, (item) => {
              item.left = left;
            });
            toast.success(
              `Позиция left комплекта товаров "${title}" обновлена.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.left) || (errors == null ? void 0 : errors.general) || `Ошибка изменения left комплекта товаров "${title}".`
            );
          }
        }
      );
    };
    const toggleMain = (bundle) => {
      if (!(bundle == null ? void 0 : bundle.id)) {
        return;
      }
      const main = !bundle.main;
      const title = getBundleTitle(bundle);
      router.put(
        route(
          "admin.actions.marketProductBundles.updateMain",
          {
            marketProductBundle: bundle.id
          }
        ),
        {
          main
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBundle(bundle.id, (item) => {
              item.main = main;
            });
            toast.success(
              `Позиция main комплекта товаров "${title}" обновлена.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.main) || (errors == null ? void 0 : errors.general) || `Ошибка изменения main комплекта товаров "${title}".`
            );
          }
        }
      );
    };
    const toggleRight = (bundle) => {
      if (!(bundle == null ? void 0 : bundle.id)) {
        return;
      }
      const right = !bundle.right;
      const title = getBundleTitle(bundle);
      router.put(
        route(
          "admin.actions.marketProductBundles.updateRight",
          {
            marketProductBundle: bundle.id
          }
        ),
        {
          right
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBundle(bundle.id, (item) => {
              item.right = right;
            });
            toast.success(
              `Позиция right комплекта товаров "${title}" обновлена.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.right) || (errors == null ? void 0 : errors.general) || `Ошибка изменения right комплекта товаров "${title}".`
            );
          }
        }
      );
    };
    const approveBundle = (bundle, status = 1, note = "") => {
      if (!(bundle == null ? void 0 : bundle.id) || !isAdmin.value) {
        return;
      }
      router.put(
        route(
          "admin.actions.marketProductBundles.approve",
          {
            marketProductBundle: bundle.id
          }
        ),
        {
          moderation_status: status,
          moderation_note: note
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalBundle(bundle.id, (item) => {
              item.moderation_status = status;
              item.is_approved = status === 1;
              item.moderation_note = note;
            });
            toast.success(
              status === 1 ? "Комплект товаров одобрен." : "Комплект товаров отклонён."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.moderation_status) || (errors == null ? void 0 : errors.general) || "Ошибка модерации комплекта товаров."
            );
          }
        }
      );
    };
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const bundleMatchesSearch = (bundle, query) => {
      var _a, _b, _c, _d;
      if (!query) {
        return true;
      }
      const items = Array.isArray(bundle == null ? void 0 : bundle.items) ? bundle.items : [];
      const searchValues = [
        bundle == null ? void 0 : bundle.url,
        bundle == null ? void 0 : bundle.sku,
        bundle == null ? void 0 : bundle.vendor_code,
        bundle == null ? void 0 : bundle.barcode,
        bundle == null ? void 0 : bundle.status,
        bundle == null ? void 0 : bundle.moderation_note,
        getBundleTitle(bundle),
        getBundleSubtitle(bundle),
        getBundleShort(bundle),
        getBundleDescription(bundle),
        (_b = (_a = bundle == null ? void 0 : bundle.company) == null ? void 0 : _a.translation) == null ? void 0 : _b.title,
        (_d = (_c = bundle == null ? void 0 : bundle.shop) == null ? void 0 : _c.translation) == null ? void 0 : _d.title,
        getOwnerName(bundle),
        getOwnerEmail(bundle),
        ...items.flatMap((item) => {
          var _a2, _b2, _c2, _d2;
          return [
            (_b2 = (_a2 = item == null ? void 0 : item.product) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title,
            (_d2 = (_c2 = item == null ? void 0 : item.variant) == null ? void 0 : _c2.translation) == null ? void 0 : _d2.title
          ];
        })
      ];
      return searchValues.some((value) => {
        return normalize(value).includes(query);
      });
    };
    const filteredBundles = computed(() => {
      if (props.useServerProcessing) {
        return localBundles.value;
      }
      const query = normalize(searchQuery.value);
      return localBundles.value.filter(
        (bundle) => bundleMatchesSearch(bundle, query)
      );
    });
    const compareText = (left, right) => {
      return normalize(left).localeCompare(
        normalize(right),
        locale.value
      );
    };
    const compareByIdDesc = (left, right) => {
      return safeNumber(right == null ? void 0 : right.id) - safeNumber(left == null ? void 0 : left.id);
    };
    const filterByIdDesc = (list, predicate) => {
      return list.filter(predicate).sort(compareByIdDesc);
    };
    const byNumberAsc = (field) => {
      return (left, right) => {
        return safeNumber(left == null ? void 0 : left[field]) - safeNumber(right == null ? void 0 : right[field]) || compareByIdDesc(left, right);
      };
    };
    const byNumberDesc = (field) => {
      return (left, right) => {
        return safeNumber(right == null ? void 0 : right[field]) - safeNumber(left == null ? void 0 : left[field]) || compareByIdDesc(left, right);
      };
    };
    const byStringAsc = (field) => {
      return (left, right) => {
        return compareText(
          left == null ? void 0 : left[field],
          right == null ? void 0 : right[field]
        ) || compareByIdDesc(left, right);
      };
    };
    const byStringDesc = (field) => {
      return (left, right) => {
        return compareText(
          right == null ? void 0 : right[field],
          left == null ? void 0 : left[field]
        ) || compareByIdDesc(left, right);
      };
    };
    const byDateAsc = (field) => {
      return (left, right) => {
        return safeDate(left == null ? void 0 : left[field]) - safeDate(right == null ? void 0 : right[field]) || compareByIdDesc(left, right);
      };
    };
    const byDateDesc = (field) => {
      return (left, right) => {
        return safeDate(right == null ? void 0 : right[field]) - safeDate(left == null ? void 0 : left[field]) || compareByIdDesc(left, right);
      };
    };
    const sortedBundles = computed(() => {
      const list = [...filteredBundles.value];
      if (props.useServerProcessing) {
        return list;
      }
      const sort = sortParam.value;
      if (sort === "activity") {
        return filterByIdDesc(list, (bundle) => bundle.activity);
      }
      if (sort === "inactive") {
        return filterByIdDesc(list, (bundle) => !bundle.activity);
      }
      if (sort === "hasItems") {
        return filterByIdDesc(
          list,
          (bundle) => safeNumber(bundle.items_count) > 0
        );
      }
      if (sort === "withoutItems") {
        return filterByIdDesc(
          list,
          (bundle) => safeNumber(bundle.items_count) === 0
        );
      }
      if (sort === "calculatedPrice") {
        return filterByIdDesc(
          list,
          (bundle) => bundle.calculate_price
        );
      }
      if (sort === "manualPrice") {
        return filterByIdDesc(
          list,
          (bundle) => !bundle.calculate_price
        );
      }
      if (sort === "left") {
        return filterByIdDesc(list, (bundle) => bundle.left);
      }
      if (sort === "noLeft") {
        return filterByIdDesc(list, (bundle) => !bundle.left);
      }
      if (sort === "main") {
        return filterByIdDesc(list, (bundle) => bundle.main);
      }
      if (sort === "noMain") {
        return filterByIdDesc(list, (bundle) => !bundle.main);
      }
      if (sort === "right") {
        return filterByIdDesc(list, (bundle) => bundle.right);
      }
      if (sort === "noRight") {
        return filterByIdDesc(list, (bundle) => !bundle.right);
      }
      if (sort === "new") {
        return filterByIdDesc(list, (bundle) => bundle.is_new);
      }
      if (sort === "notNew") {
        return filterByIdDesc(list, (bundle) => !bundle.is_new);
      }
      if (sort === "hit") {
        return filterByIdDesc(list, (bundle) => bundle.is_hit);
      }
      if (sort === "notHit") {
        return filterByIdDesc(list, (bundle) => !bundle.is_hit);
      }
      if (sort === "sale") {
        return filterByIdDesc(list, (bundle) => bundle.is_sale);
      }
      if (sort === "notSale") {
        return filterByIdDesc(list, (bundle) => !bundle.is_sale);
      }
      if (sort === "statusDraft") {
        return filterByIdDesc(
          list,
          (bundle) => bundle.status === "draft"
        );
      }
      if (sort === "statusPublished") {
        return filterByIdDesc(
          list,
          (bundle) => bundle.status === "published"
        );
      }
      if (sort === "statusArchived") {
        return filterByIdDesc(
          list,
          (bundle) => bundle.status === "archived"
        );
      }
      if (sort === "moderationPending") {
        return filterByIdDesc(
          list,
          (bundle) => moderationNum(bundle.moderation_status) === 0
        );
      }
      if (sort === "moderationApproved") {
        return filterByIdDesc(
          list,
          (bundle) => moderationNum(bundle.moderation_status) === 1
        );
      }
      if (sort === "moderationRejected") {
        return filterByIdDesc(
          list,
          (bundle) => moderationNum(bundle.moderation_status) === 2
        );
      }
      const sortMap = {
        /** ID */
        idAsc: (left, right) => safeNumber(left == null ? void 0 : left.id) - safeNumber(right == null ? void 0 : right.id),
        idDesc: (left, right) => safeNumber(right == null ? void 0 : right.id) - safeNumber(left == null ? void 0 : left.id),
        /** Ручная сортировка */
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        /** Название комплекта */
        titleAsc: (left, right) => {
          return compareText(
            getBundleTitle(left),
            getBundleTitle(right)
          ) || compareByIdDesc(left, right);
        },
        titleDesc: (left, right) => {
          return compareText(
            getBundleTitle(right),
            getBundleTitle(left)
          ) || compareByIdDesc(left, right);
        },
        /** Основные коды */
        urlAsc: byStringAsc("url"),
        urlDesc: byStringDesc("url"),
        skuAsc: byStringAsc("sku"),
        skuDesc: byStringDesc("sku"),
        vendorCodeAsc: byStringAsc("vendor_code"),
        vendorCodeDesc: byStringDesc("vendor_code"),
        barcodeAsc: byStringAsc("barcode"),
        barcodeDesc: byStringDesc("barcode"),
        /** Компания */
        companyAsc: (left, right) => {
          return compareText(
            getCompanyTitle(left),
            getCompanyTitle(right)
          ) || compareByIdDesc(left, right);
        },
        companyDesc: (left, right) => {
          return compareText(
            getCompanyTitle(right),
            getCompanyTitle(left)
          ) || compareByIdDesc(left, right);
        },
        /** Магазин */
        shopAsc: (left, right) => {
          return compareText(
            getShopTitle(left),
            getShopTitle(right)
          ) || compareByIdDesc(left, right);
        },
        shopDesc: (left, right) => {
          return compareText(
            getShopTitle(right),
            getShopTitle(left)
          ) || compareByIdDesc(left, right);
        },
        /** Имя владельца */
        ownerNameAsc: (left, right) => {
          return compareText(
            getOwnerName(left),
            getOwnerName(right)
          ) || compareByIdDesc(left, right);
        },
        ownerNameDesc: (left, right) => {
          return compareText(
            getOwnerName(right),
            getOwnerName(left)
          ) || compareByIdDesc(left, right);
        },
        /** Email владельца */
        ownerEmailAsc: (left, right) => {
          return compareText(
            getOwnerEmail(left),
            getOwnerEmail(right)
          ) || compareByIdDesc(left, right);
        },
        ownerEmailDesc: (left, right) => {
          return compareText(
            getOwnerEmail(right),
            getOwnerEmail(left)
          ) || compareByIdDesc(left, right);
        },
        /** Режим формирования цены */
        calculatePriceAsc: byNumberAsc("calculate_price"),
        calculatePriceDesc: byNumberDesc("calculate_price"),
        /** Основная цена */
        priceAsc: byNumberAsc("price"),
        priceDesc: byNumberDesc("price"),
        /** Старая цена */
        oldPriceAsc: byNumberAsc("old_price"),
        oldPriceDesc: byNumberDesc("old_price"),
        /** Закупочная цена */
        purchasePriceAsc: byNumberAsc("purchase_price"),
        purchasePriceDesc: byNumberDesc("purchase_price"),
        /** Оптовая цена */
        wholesalePriceAsc: byNumberAsc("wholesale_price"),
        wholesalePriceDesc: byNumberDesc("wholesale_price"),
        /** Минимальное оптовое количество */
        wholesaleMinQuantityAsc: byNumberAsc("wholesale_min_quantity"),
        wholesaleMinQuantityDesc: byNumberDesc("wholesale_min_quantity"),
        /** Количество позиций */
        itemsAsc: byNumberAsc("items_count"),
        itemsDesc: byNumberDesc("items_count"),
        /** Количество изображений */
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        /** Просмотры */
        viewsAsc: byNumberAsc("views"),
        viewsDesc: byNumberDesc("views"),
        /** Лайки */
        likesAsc: byNumberAsc("likes_count"),
        likesDesc: byNumberDesc("likes_count"),
        /** Средний рейтинг */
        ratingAsc: byNumberAsc("rating_avg"),
        ratingDesc: byNumberDesc("rating_avg"),
        /** Количество оценок */
        ratingCountAsc: byNumberAsc("rating_count"),
        ratingCountDesc: byNumberDesc("rating_count"),
        /** Активность */
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        /** Новинка */
        newAsc: byNumberAsc("is_new"),
        newDesc: byNumberDesc("is_new"),
        /** Хит продаж */
        hitAsc: byNumberAsc("is_hit"),
        hitDesc: byNumberDesc("is_hit"),
        /** Распродажа */
        saleAsc: byNumberAsc("is_sale"),
        saleDesc: byNumberDesc("is_sale"),
        /** Левая рекламная позиция */
        leftAsc: byNumberAsc("left"),
        leftDesc: byNumberDesc("left"),
        /** Главная рекламная позиция */
        mainAsc: byNumberAsc("main"),
        mainDesc: byNumberDesc("main"),
        /** Правая рекламная позиция */
        rightAsc: byNumberAsc("right"),
        rightDesc: byNumberDesc("right"),
        /** Статус публикации */
        statusAsc: byStringAsc("status"),
        statusDesc: byStringDesc("status"),
        /** Статус модерации */
        moderationStatusAsc: byNumberAsc("moderation_status"),
        moderationStatusDesc: byNumberDesc("moderation_status"),
        /** Дата публикации */
        publishedAtAsc: byDateAsc("published_at"),
        publishedAtDesc: byDateDesc("published_at"),
        /** Начало показа */
        showFromAtAsc: byDateAsc("show_from_at"),
        showFromAtDesc: byDateDesc("show_from_at"),
        /** Окончание показа */
        showToAtAsc: byDateAsc("show_to_at"),
        showToAtDesc: byDateDesc("show_to_at"),
        /** Дата создания */
        createdAtAsc: byDateAsc("created_at"),
        createdAtDesc: byDateDesc("created_at"),
        /** Совместимость со старым параметром date */
        dateAsc: byDateAsc("created_at"),
        dateDesc: byDateDesc("created_at"),
        /** Дата обновления */
        updatedAtAsc: byDateAsc("updated_at"),
        updatedAtDesc: byDateDesc("updated_at")
      };
      const comparator = sortMap[sort];
      if (typeof comparator !== "function") {
        return list.sort((left, right) => {
          return safeNumber(left == null ? void 0 : left.sort) - safeNumber(right == null ? void 0 : right.sort) || compareByIdDesc(left, right);
        });
      }
      return list.sort(comparator);
    });
    const frontendPaginatedBundles = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      return sortedBundles.value.slice(
        start,
        start + itemsPerPage.value
      );
    });
    watch(
      [
        searchQuery,
        sortParam,
        itemsPerPage,
        viewMode
      ],
      () => {
        currentPage.value = 1;
      }
    );
    const displayedBundles = computed(() => {
      return props.useServerProcessing ? localBundles.value : frontendPaginatedBundles.value;
    });
    const selectedBundles = ref([]);
    const isSelected = (productId) => {
      return selectedBundles.value.includes(productId);
    };
    const toggleSelectBundle = (bundleOrId) => {
      const id = typeof bundleOrId === "object" ? bundleOrId == null ? void 0 : bundleOrId.id : bundleOrId;
      if (!id) {
        return;
      }
      if (isSelected(id)) {
        selectedBundles.value = selectedBundles.value.filter(
          (selectedId) => selectedId !== id
        );
        return;
      }
      selectedBundles.value.push(id);
    };
    const toggleAll = (eventOrValue) => {
      var _a;
      const checked = typeof eventOrValue === "boolean" ? eventOrValue : Boolean(((_a = eventOrValue == null ? void 0 : eventOrValue.target) == null ? void 0 : _a.checked) ?? (eventOrValue == null ? void 0 : eventOrValue.checked));
      if (!checked) {
        selectedBundles.value = [];
        return;
      }
      selectedBundles.value = displayedBundles.value.map((bundle) => bundle == null ? void 0 : bundle.id).filter(Boolean);
    };
    const allDisplayedSelected = computed(() => {
      const ids = displayedBundles.value.map((bundle) => bundle == null ? void 0 : bundle.id).filter(Boolean);
      return ids.length > 0 && ids.every((id) => selectedBundles.value.includes(id));
    });
    const bulkToggleActivity = (activity) => {
      if (!selectedBundles.value.length) {
        toast.warning(
          "Выберите хотя бы один комплект товаров."
        );
        return;
      }
      const ids = [...selectedBundles.value];
      router.put(
        route(
          "admin.actions.marketProductBundles.bulkUpdateActivity"
        ),
        {
          ids,
          activity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localBundles.value.forEach((bundle) => {
              if (ids.includes(bundle.id)) {
                bundle.activity = activity;
                bundle.is_active = activity;
              }
            });
            selectedBundles.value = [];
            toast.success(
              "Активность выбранных комплектов товаров обновлена."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового изменения активности."
            );
          }
        }
      );
    };
    const bulkToggleFlag = (field, value, routeName, message) => {
      if (!selectedBundles.value.length) {
        toast.warning("Выберите комплекты товаров для массового действия.");
        return;
      }
      const ids = [...selectedBundles.value];
      router.put(route(routeName), {
        ids,
        [field]: value
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          ids.forEach((id) => {
            patchLocalBundle(id, (bundle) => {
              bundle[field] = value;
            });
          });
          selectedBundles.value = [];
          toast.success(message);
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления комплектов товаров.");
        }
      });
    };
    const bulkDelete = () => {
      if (!selectedBundles.value.length) {
        toast.warning(
          "Выберите хотя бы один комплект товаров для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные комплекты товаров?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.marketProductBundles.bulkDestroy"
        ),
        {
          data: {
            ids: selectedBundles.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedBundles.value = [];
            toast.success(
              "Выбранные комплекты товаров успешно удалены."
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || "Ошибка массового удаления комплектов товаров."
            );
          }
        }
      );
    };
    const handleBulkAction = (event) => {
      var _a;
      const action = (_a = event == null ? void 0 : event.target) == null ? void 0 : _a.value;
      if (action === "selectAll") {
        toggleAll(true);
      } else if (action === "deselectAll") {
        toggleAll(false);
      } else if (action === "activate") {
        bulkToggleActivity(true);
      } else if (action === "deactivate") {
        bulkToggleActivity(false);
      } else if (action === "isNewOn") {
        bulkToggleFlag(
          "is_new",
          true,
          "admin.actions.marketProductBundles.bulkUpdateIsNew",
          "Комплекты товаров добавлены в новинки."
        );
      } else if (action === "isNewOff") {
        bulkToggleFlag(
          "is_new",
          false,
          "admin.actions.marketProductBundles.bulkUpdateIsNew",
          "Комплекты товаров убраны из новинок."
        );
      } else if (action === "isHitOn") {
        bulkToggleFlag(
          "is_hit",
          true,
          "admin.actions.marketProductBundles.bulkUpdateIsHit",
          "Комплекты товаров добавлены в рекомендуемые."
        );
      } else if (action === "isHitOff") {
        bulkToggleFlag(
          "is_hit",
          false,
          "admin.actions.marketProductBundles.bulkUpdateIsHit",
          "Комплекты товаров убраны из рекомендуемых."
        );
      } else if (action === "isSaleOn") {
        bulkToggleFlag(
          "is_sale",
          true,
          "admin.actions.marketProductBundles.bulkUpdateIsSale",
          "Комплекты товаров добавлены в распродажу."
        );
      } else if (action === "isSaleOff") {
        bulkToggleFlag(
          "is_sale",
          false,
          "admin.actions.marketProductBundles.bulkUpdateIsSale",
          "Комплекты товаров убраны из распродажи."
        );
      } else if (action === "left") {
        bulkToggleFlag(
          "left",
          true,
          "admin.actions.marketProductBundles.bulkUpdateLeft",
          "Комплекты товаров добавлены в левую колонку."
        );
      } else if (action === "noLeft") {
        bulkToggleFlag(
          "left",
          false,
          "admin.actions.marketProductBundles.bulkUpdateLeft",
          "Комплекты товаров убраны из левой колонки."
        );
      } else if (action === "main") {
        bulkToggleFlag(
          "main",
          true,
          "admin.actions.marketProductBundles.bulkUpdateMain",
          "Комплекты товаров добавлены в главный блок."
        );
      } else if (action === "noMain") {
        bulkToggleFlag(
          "main",
          false,
          "admin.actions.marketProductBundles.bulkUpdateMain",
          "Комплекты товаров убраны из главного блока."
        );
      } else if (action === "right") {
        bulkToggleFlag(
          "right",
          true,
          "admin.actions.marketProductBundles.bulkUpdateRight",
          "Комплекты товаров добавлены в правую колонку."
        );
      } else if (action === "noRight") {
        bulkToggleFlag(
          "right",
          false,
          "admin.actions.marketProductBundles.bulkUpdateRight",
          "Комплекты товаров убраны из правой колонки."
        );
      } else if (action === "delete") {
        bulkDelete();
      }
      if (event == null ? void 0 : event.target) {
        event.target.value = "";
      }
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
          "admin.actions.marketProductBundles.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            items.forEach((item) => {
              patchLocalBundle(item.id, (bundle) => {
                bundle.sort = item.sort;
              });
            });
            toast.success(
              "Сортировка комплектов товаров обновлена."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка сортировки комплектов товаров:",
              errors
            );
            toast.error(
              (errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.items) || "Ошибка обновления сортировки комплектов товаров."
            );
          }
        }
      );
    };
    const toggleBundleFlag = (bundle, field, routeName, enabledMessage, disabledMessage) => {
      if (!(bundle == null ? void 0 : bundle.id)) {
        return;
      }
      const newValue = !bundle[field];
      const title = getBundleTitle(bundle);
      router.put(route(routeName, { marketProductBundle: bundle.id }), {
        [field]: newValue
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          patchLocalBundle(bundle.id, (item) => {
            item[field] = newValue;
          });
          bundle[field] = newValue;
          toast.success(newValue ? `${enabledMessage} "${title}".` : `${disabledMessage} "${title}".`);
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || `Ошибка изменения флага комплекта товаров "${title}".`);
        }
      });
    };
    const toggleIsNew = (bundle) => {
      toggleBundleFlag(
        bundle,
        "is_new",
        "admin.actions.marketProductBundles.updateIsNew",
        "Комплект товаров добавлен в новинки",
        "Комплект товаров убран из новинок"
      );
    };
    const toggleIsHit = (bundle) => {
      toggleBundleFlag(
        bundle,
        "is_hit",
        "admin.actions.marketProductBundles.updateIsHit",
        "Комплект товаров добавлен в рекомендуемые",
        "Комплект товаров убран из рекомендуемых"
      );
    };
    const toggleIsSale = (bundle) => {
      toggleBundleFlag(
        bundle,
        "is_sale",
        "admin.actions.marketProductBundles.updateIsSale",
        "Комплект товаров добавлен в распродажу",
        "Комплект товаров убран из распродажи"
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("marketProductBundles")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("marketProductBundles"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("marketProductBundles")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("marketProductBundles")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="flex flex-col gap-3 mb-3 sm:flex-row sm:items-center sm:justify-between"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$f, {
              href: _ctx.route("admin.marketProductBundles.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addMarketProductBundle"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addMarketProductBundle")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$g, {
              "setting-key": "adminMarketProductBundlesProcessingMode",
              mode: __props.adminMarketProductBundlesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.bundlesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.bundlesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$h, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.bundlesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$i, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.bundlesCount) {
              _push2(`<div class="flex flex-col md:flex-row items-center justify-between gap-3 my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$j, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountMarketProductBundles"
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
            if (__props.bundlesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$l, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.bundlesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.bundlesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$m, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.bundlesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$n, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredBundles.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$o, { pagination: __props.bundles }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                bundles: displayedBundles.value,
                "selected-bundles": selectedBundles.value,
                "all-selected": allDisplayedSelected.value,
                "is-admin": isAdmin.value,
                onToggleSelect: toggleSelectBundle,
                onToggleAll: toggleAll,
                onToggleActivity: toggleActivity,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleIsNew: toggleIsNew,
                onToggleIsHit: toggleIsHit,
                onToggleIsSale: toggleIsSale,
                onApprove: approveBundle,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                bundles: displayedBundles.value,
                "selected-bundles": selectedBundles.value,
                "is-admin": isAdmin.value,
                onToggleSelect: toggleSelectBundle,
                onToggleAll: toggleAll,
                onToggleActivity: toggleActivity,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleIsNew: toggleIsNew,
                onToggleIsHit: toggleIsHit,
                onToggleIsSale: toggleIsSale,
                onApprove: approveBundle,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate
              }, null, _parent2, _scopeId));
            }
            if (!__props.bundlesCount) {
              _push2(`<div class="py-12 text-center text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else if (!__props.useServerProcessing && !displayedBundles.value.length) {
              _push2(`<div class="py-10 text-center text-slate-500 dark:text-slate-300"${_scopeId}> По вашему запросу комплекты товаров не найдены. </div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.bundlesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-4"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$n, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredBundles.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$o, { pagination: __props.bundles }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$p, {
              show: showConfirmDeleteModal.value,
              "on-cancel": closeModal,
              "on-confirm": deleteBundle,
              "cancel-text": unref(t)("cancel"),
              "confirm-text": unref(t)("yesDelete"),
              onClose: closeModal
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<p class="text-sm text-slate-700 dark:text-slate-200"${_scopeId2}> Удалить комплект товаров <strong${_scopeId2}>${ssrInterpolate(bundleToDeleteTitle.value)}</strong> ? </p>`);
                } else {
                  return [
                    createVNode("p", { class: "text-sm text-slate-700 dark:text-slate-200" }, [
                      createTextVNode(" Удалить комплект товаров "),
                      createVNode("strong", null, toDisplayString(bundleToDeleteTitle.value), 1),
                      createTextVNode(" ? ")
                    ])
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "flex flex-col gap-3 mb-3 sm:flex-row sm:items-center sm:justify-between" }, [
                    createVNode(_sfc_main$f, {
                      href: _ctx.route("admin.marketProductBundles.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addMarketProductBundle")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$g, {
                      "setting-key": "adminMarketProductBundlesProcessingMode",
                      mode: __props.adminMarketProductBundlesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.bundlesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.bundlesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.bundlesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$i, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.bundlesCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex flex-col md:flex-row items-center justify-between gap-3 my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$j, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$k, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountMarketProductBundles"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.bundlesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$l, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.bundlesCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$m, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.bundlesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$n, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredBundles.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$o, {
                      key: 1,
                      pagination: __props.bundles
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    bundles: displayedBundles.value,
                    "selected-bundles": selectedBundles.value,
                    "all-selected": allDisplayedSelected.value,
                    "is-admin": isAdmin.value,
                    onToggleSelect: toggleSelectBundle,
                    onToggleAll: toggleAll,
                    onToggleActivity: toggleActivity,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleIsNew: toggleIsNew,
                    onToggleIsHit: toggleIsHit,
                    onToggleIsSale: toggleIsSale,
                    onApprove: approveBundle,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate
                  }, null, 8, ["bundles", "selected-bundles", "all-selected", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    bundles: displayedBundles.value,
                    "selected-bundles": selectedBundles.value,
                    "is-admin": isAdmin.value,
                    onToggleSelect: toggleSelectBundle,
                    onToggleAll: toggleAll,
                    onToggleActivity: toggleActivity,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleIsNew: toggleIsNew,
                    onToggleIsHit: toggleIsHit,
                    onToggleIsSale: toggleIsSale,
                    onApprove: approveBundle,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate
                  }, null, 8, ["bundles", "selected-bundles", "is-admin"])),
                  !__props.bundlesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "py-12 text-center text-slate-500 dark:text-slate-300"
                  }, toDisplayString(unref(t)("noData")), 1)) : !__props.useServerProcessing && !displayedBundles.value.length ? (openBlock(), createBlock("div", {
                    key: 8,
                    class: "py-10 text-center text-slate-500 dark:text-slate-300"
                  }, " По вашему запросу комплекты товаров не найдены. ")) : createCommentVNode("", true),
                  __props.bundlesCount ? (openBlock(), createBlock("div", {
                    key: 9,
                    class: "flex justify-center items-center flex-col md:flex-row mt-4"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$n, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredBundles.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$o, {
                      key: 1,
                      pagination: __props.bundles
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$p, {
                show: showConfirmDeleteModal.value,
                "on-cancel": closeModal,
                "on-confirm": deleteBundle,
                "cancel-text": unref(t)("cancel"),
                "confirm-text": unref(t)("yesDelete"),
                onClose: closeModal
              }, {
                default: withCtx(() => [
                  createVNode("p", { class: "text-sm text-slate-700 dark:text-slate-200" }, [
                    createTextVNode(" Удалить комплект товаров "),
                    createVNode("strong", null, toDisplayString(bundleToDeleteTitle.value), 1),
                    createTextVNode(" ? ")
                  ])
                ]),
                _: 1
              }, 8, ["show", "cancel-text", "confirm-text"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketProductBundles/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
