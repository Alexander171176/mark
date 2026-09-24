import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, Fragment, renderList, createTextVNode, withDirectives, withModifiers, vShow, computed, vModelSelect } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent, ssrRenderList, ssrRenderStyle, ssrRenderClass, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { usePage, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$9 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$j } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$f } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$d, a as _sfc_main$g, b as _sfc_main$h } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$b } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$c, a as _sfc_main$i } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$e } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$a } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$6 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$8 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$7 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$5 } from "./ModerationButton-D_ehimPY.js";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value="">— ${ssrInterpolate(unref(t)("selectAction"))} —</option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>───────────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>───────────────────────</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled>───────────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProductVariant/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-64 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>────────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>────────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option disabled>────────────────────</option><option value="productTitleAsc">${ssrInterpolate(unref(t)("marketProduct"))} A→Z</option><option value="productTitleDesc">${ssrInterpolate(unref(t)("marketProduct"))} Z→A</option><option disabled>────────────────────</option><option value="codeAsc">${ssrInterpolate(unref(t)("code"))} A→Z</option><option value="codeDesc">${ssrInterpolate(unref(t)("code"))} Z→A</option><option value="skuAsc">SKU A→Z</option><option value="skuDesc">SKU Z→A</option><option value="vendorCodeAsc">${ssrInterpolate(unref(t)("vendorCode"))} A→Z</option><option value="vendorCodeDesc">${ssrInterpolate(unref(t)("vendorCode"))} Z→A</option><option value="barcodeAsc">${ssrInterpolate(unref(t)("barcode"))} A→Z</option><option value="barcodeDesc">${ssrInterpolate(unref(t)("barcode"))} Z→A</option><option disabled>────────────────────</option><option value="priceDesc">${ssrInterpolate(unref(t)("price"))} 9→0</option><option value="priceAsc">${ssrInterpolate(unref(t)("price"))} 0→9</option><option value="oldPriceDesc">${ssrInterpolate(unref(t)("compareAtPrice"))} 9→0</option><option value="oldPriceAsc">${ssrInterpolate(unref(t)("compareAtPrice"))} 0→9</option><option value="purchasePriceDesc">${ssrInterpolate(unref(t)("purchasePrice"))} 9→0</option><option value="purchasePriceAsc">${ssrInterpolate(unref(t)("purchasePrice"))} 0→9</option><option value="wholesalePriceDesc">${ssrInterpolate(unref(t)("wholesalePrice"))} 9→0</option><option value="wholesalePriceAsc">${ssrInterpolate(unref(t)("wholesalePrice"))} 0→9</option><option value="wholesaleMinQuantityDesc">${ssrInterpolate(unref(t)("wholesaleMinQuantity"))} 9→0</option><option value="wholesaleMinQuantityAsc">${ssrInterpolate(unref(t)("wholesaleMinQuantity"))} 0→9</option><option disabled>────────────────────</option><option value="quantityDesc">${ssrInterpolate(unref(t)("quantity"))} 9→0</option><option value="quantityAsc">${ssrInterpolate(unref(t)("quantity"))} 0→9</option><option disabled>───────────────────</option><option value="inStockDesc">${ssrInterpolate(unref(t)("inStock"))} ON→OFF</option><option value="inStockAsc">${ssrInterpolate(unref(t)("inStock"))} OFF→ON</option><option value="inStock">${ssrInterpolate(unref(t)("inStock"))}</option><option value="outOfStock">${ssrInterpolate(unref(t)("outOfStock"))}</option><option disabled>────────────────────</option><option value="weightDesc">${ssrInterpolate(unref(t)("weight"))} 9→0</option><option value="weightAsc">${ssrInterpolate(unref(t)("weight"))} 0→9</option><option value="lengthDesc">${ssrInterpolate(unref(t)("length"))} 9→0</option><option value="lengthAsc">${ssrInterpolate(unref(t)("length"))} 0→9</option><option value="widthDesc">${ssrInterpolate(unref(t)("width"))} 9→0</option><option value="widthAsc">${ssrInterpolate(unref(t)("width"))} 0→9</option><option value="heightDesc">${ssrInterpolate(unref(t)("height"))} 9→0</option><option value="heightAsc">${ssrInterpolate(unref(t)("height"))} 0→9</option><option disabled>────────────────────</option><option value="defaultDesc">${ssrInterpolate(unref(t)("defaultVariant"))} ON→OFF</option><option value="defaultAsc">${ssrInterpolate(unref(t)("defaultVariant"))} OFF→ON</option><option value="default">${ssrInterpolate(unref(t)("defaultVariant"))}</option><option value="notDefault">${ssrInterpolate(unref(t)("notDefaultVariant"))}</option><option disabled>────────────────────</option><option value="valuesDesc">${ssrInterpolate(unref(t)("attributes"))} 9→0</option><option value="valuesAsc">${ssrInterpolate(unref(t)("attributes"))} 0→9</option><option disabled>────────────────────</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>────────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>───────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="statusDraft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="statusPublished">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="statusArchived">${ssrInterpolate(unref(t)("statusArchived"))}</option><option disabled>───────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>───────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="showFromAtDesc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortStarted"))} ↓</option><option value="showFromAtAsc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortStarted"))} ↑</option><option value="showToAtDesc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortExpires"))} ↓</option><option value="showToAtAsc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortExpires"))} ↑</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>───────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProductVariant/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "VariantTable",
  __ssrInlineRender: true,
  props: {
    variants: {
      type: Array,
      default: () => []
    },
    selectedVariants: {
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
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all",
    "approve",
    "make-default"
  ],
  setup(__props, { emit: __emit }) {
    const { t, locale } = useI18n();
    const props = __props;
    const emit = __emit;
    const localVariants = ref([]);
    watch(
      () => props.variants,
      (newValue) => {
        localVariants.value = JSON.parse(JSON.stringify(newValue || []));
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localVariants.value.map((variant) => variant.id)
      );
    };
    const normalizedSelectedIds = () => {
      return props.selectedVariants.map((id) => Number(id));
    };
    const isSelected = (variantId) => {
      return normalizedSelectedIds().includes(Number(variantId));
    };
    const allVariantsSelected = () => {
      if (!localVariants.value.length) {
        return false;
      }
      return localVariants.value.every((variant) => isSelected(variant.id));
    };
    const variantTranslation = (variant) => {
      return (variant == null ? void 0 : variant.translation) || {};
    };
    const variantTitle = (variant) => {
      var _a;
      return (variant == null ? void 0 : variant.display_title) || ((_a = variantTranslation(variant)) == null ? void 0 : _a.title) || (variant == null ? void 0 : variant.code) || (variant == null ? void 0 : variant.sku) || `ID: ${variant == null ? void 0 : variant.id}`;
    };
    const variantSubtitle = (variant) => {
      var _a;
      return ((_a = variantTranslation(variant)) == null ? void 0 : _a.subtitle) || "";
    };
    const variantShort = (variant) => {
      var _a;
      return ((_a = variantTranslation(variant)) == null ? void 0 : _a.short) || "";
    };
    const productTitle = (variant) => {
      var _a, _b;
      return ((_b = (_a = variant == null ? void 0 : variant.product) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || `ID: ${variant == null ? void 0 : variant.market_product_id}`;
    };
    const getPrimaryImage = (variant) => {
      if (!Array.isArray(variant == null ? void 0 : variant.images) || !variant.images.length) {
        return null;
      }
      return [...variant.images].sort((left, right) => {
        var _a, _b;
        const leftOrder = Number(((_a = left == null ? void 0 : left.pivot) == null ? void 0 : _a.order) ?? (left == null ? void 0 : left.order) ?? 0);
        const rightOrder = Number(((_b = right == null ? void 0 : right.pivot) == null ? void 0 : _b.order) ?? (right == null ? void 0 : right.order) ?? 0);
        return leftOrder - rightOrder;
      })[0];
    };
    const imageUrl = (variant) => {
      const image = getPrimaryImage(variant);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/market/market_product_variant_images/default-image.png";
    };
    const imageAlt = (variant) => {
      var _a;
      return ((_a = getPrimaryImage(variant)) == null ? void 0 : _a.alt) || variantTitle(variant);
    };
    const imageTitle = (variant) => {
      var _a;
      return ((_a = getPrimaryImage(variant)) == null ? void 0 : _a.caption) || variantTitle(variant);
    };
    const variantValues = (variant) => {
      return Array.isArray(variant == null ? void 0 : variant.values) ? variant.values : [];
    };
    const valueTitle = (item) => {
      var _a, _b, _c;
      return (item == null ? void 0 : item.display_value) || ((_b = (_a = item == null ? void 0 : item.attribute_value) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((_c = item == null ? void 0 : item.attribute_value) == null ? void 0 : _c.code) || "";
    };
    const attributeColor = (item) => {
      var _a, _b;
      return ((_a = item == null ? void 0 : item.attribute_value) == null ? void 0 : _a.color) || ((_b = item == null ? void 0 : item.attribute) == null ? void 0 : _b.color) || null;
    };
    const valuesTooltip = (variant) => {
      return variantValues(variant).map((item) => valueTitle(item)).filter(Boolean).join("\n");
    };
    const effectiveCurrency = (variant) => {
      var _a;
      return (variant == null ? void 0 : variant.effective_currency) || (variant == null ? void 0 : variant.currency) || ((_a = variant == null ? void 0 : variant.product) == null ? void 0 : _a.currency) || null;
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
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
      const integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);
      const formattedAmount = precision > 0 ? `${integerPart}${decimalSeparator}${parts[1]}` : integerPart;
      const symbol = String((currency == null ? void 0 : currency.symbol) || (currency == null ? void 0 : currency.code) || "").trim();
      if (!symbol) {
        return formattedAmount;
      }
      return (currency == null ? void 0 : currency.symbol_first) ? `${symbol}${formattedAmount}` : `${formattedAmount} ${symbol}`;
    };
    const statusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const getStatusLabel = (status) => {
      return t(statusLabelKeyMap[status] || status || "no");
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
    const formatDate = (dateString) => {
      if (!dateString) {
        return "";
      }
      const date = new Date(dateString);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return date.toLocaleDateString(locale.value || "ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    const truncateText = (text, maxLength = 60) => {
      if (!text) {
        return "";
      }
      const value = String(text);
      return value.length > maxLength ? `${value.slice(0, maxLength).trimEnd()}…` : value;
    };
    const publicationTitle = (variant) => {
      const values = [
        `Sort: ${(variant == null ? void 0 : variant.sort) ?? 0}`
      ];
      if (variant == null ? void 0 : variant.published_at) {
        values.push(`${t("publishedAt")}: ${formatDate(variant.published_at)}`);
      }
      if (variant == null ? void 0 : variant.show_from_at) {
        values.push(`${t("showFromAt")}: ${formatDate(variant.show_from_at)}`);
      }
      if (variant == null ? void 0 : variant.show_to_at) {
        values.push(`${t("showToAt")}: ${formatDate(variant.show_to_at)}`);
      }
      return values.join("\n");
    };
    const dimensionsTitle = (variant) => {
      return [
        `${t("weight")}: ${(variant == null ? void 0 : variant.effective_weight) ?? "—"} kg`,
        `${t("length")}: ${(variant == null ? void 0 : variant.effective_length) ?? "—"} mm`,
        `${t("width")}: ${(variant == null ? void 0 : variant.effective_width) ?? "—"} mm`,
        `${t("height")}: ${(variant == null ? void 0 : variant.effective_height) ?? "—"} mm`
      ].join("\n");
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 shadow-lg" }, _attrs))}><div class="flex items-center justify-between border-b border-slate-400 dark:border-slate-500 px-3 py-2"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedVariants.length)}</div>`);
      if (localVariants.value.length) {
        _push(`<label class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allVariantsSelected() || __props.allSelected) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localVariants.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="border border-solid border-gray-300 dark:border-gray-700 bg-slate-200 dark:bg-cyan-900 text-xs uppercase"><tr><th class="w-px px-1 py-3"><svg class="h-4 w-4 fill-current text-slate-800 dark:text-slate-200" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="w-px whitespace-nowrap px-1 py-3">${ssrInterpolate(unref(t)("id"))}</th><th class="w-px px-1 py-3"><div class="flex justify-center" title="Изображение"><svg class="h-6 w-6 shrink-0 fill-current" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32h384c35.3 0 64 28.7 64 64v320c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zm323.8 106.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4S78.8 416 88 416h336c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="min-w-72 px-2 py-3 text-left">${ssrInterpolate(unref(t)("marketProductVariant"))}</th><th class="min-w-52 px-2 py-3 text-left">${ssrInterpolate(unref(t)("attributes"))}</th><th class="whitespace-nowrap px-2 py-3 text-right">${ssrInterpolate(unref(t)("price"))}</th><th class="whitespace-nowrap px-2 py-3 text-center">${ssrInterpolate(unref(t)("quantity"))}</th><th class="whitespace-nowrap px-2 py-3 text-center">${ssrInterpolate(unref(t)("actions"))}</th><th class="w-px whitespace-nowrap px-1 py-1 text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allVariantsSelected() || __props.allSelected) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localVariants.value,
          "onUpdate:modelValue": ($event) => localVariants.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: variant }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="border-b-2 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="handle w-px cursor-move px-1 py-1 text-center"${_scopeId}><svg class="h-4 w-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="w-px whitespace-nowrap px-1 py-1 text-center text-blue-600 dark:text-blue-200"${ssrRenderAttr("title", publicationTitle(variant))}${_scopeId}>${ssrInterpolate(variant.id)}</td><td class="w-16 px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", imageUrl(variant))}${ssrRenderAttr("alt", imageAlt(variant))}${ssrRenderAttr("title", imageTitle(variant))} class="h-10 w-14 rounded-sm border border-slate-300 object-cover dark:border-slate-600"${_scopeId}></div>`);
              if (variant.images_count) {
                _push2(`<div class="mt-0.5 text-center text-[9px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(variant.images_count)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</td><td class="px-2 py-1"${_scopeId}><div class="text-left"${_scopeId}><div class="text-[10px] font-semibold uppercase text-blue-600 dark:text-blue-300"${ssrRenderAttr("title", productTitle(variant))}${_scopeId}>${ssrInterpolate(truncateText(productTitle(variant), 75))}</div><div class="mt-0.5 text-xs font-semibold text-sky-700 dark:text-sky-300"${ssrRenderAttr("title", variantShort(variant) || variantSubtitle(variant) || variantTitle(variant))}${_scopeId}>${ssrInterpolate(truncateText(variantTitle(variant), 85))}</div>`);
              if (variantSubtitle(variant)) {
                _push2(`<div class="mt-0.5 text-[10px] text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(variantSubtitle(variant), 75))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="mt-1 flex flex-wrap gap-1 text-[9px]"${_scopeId}>`);
              if (variant.code) {
                _push2(`<div class="rounded-sm border border-slate-400 bg-slate-100 px-1 py-0.5 dark:bg-slate-800"${ssrRenderAttr("title", unref(t)("code"))}${_scopeId}>${ssrInterpolate(truncateText(variant.code, 25))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.barcode) {
                _push2(`<div class="mt-0.5 flex items-center gap-1 text-[9px]"${_scopeId}><svg class="h-5 w-5 shrink-0 fill-current" viewBox="0 0 512 512"${_scopeId}><path d="M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z"${_scopeId}></path></svg>`);
                if (variant.barcode) {
                  _push2(`<span class="rounded-sm border border-slate-400 bg-slate-200 px-1 py-0.5 text-slate-700 dark:bg-slate-800 dark:text-slate-300"${ssrRenderAttr("title", unref(t)("barcode"))}${_scopeId}>${ssrInterpolate(truncateText(variant.barcode, 24))}</span>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.sku) {
                _push2(`<div class="rounded-sm border border-blue-400 bg-blue-100 px-1 py-0.5 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"${ssrRenderAttr("title", unref(t)("sku"))}${_scopeId}> SKU: ${ssrInterpolate(truncateText(variant.sku, 50))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.vendor_code) {
                _push2(`<div class="rounded-sm border border-violet-400 bg-violet-100 px-1 py-0.5 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300"${ssrRenderAttr("title", unref(t)("vendorCode"))}${_scopeId}>${ssrInterpolate(truncateText(variant.vendor_code, 25))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="mt-1 flex flex-wrap gap-1 text-[10px]"${_scopeId}>`);
              if (variant.has_own_price) {
                _push2(`<span class="rounded-sm px-1 py-0.5 text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(unref(t)("ownPrice"))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.has_own_currency) {
                _push2(`<span class="rounded-sm border border-cyan-300 bg-cyan-100 px-1 py-0.5 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(unref(t)("ownCurrency"))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.has_own_dimensions) {
                _push2(`<span class="rounded-sm border border-orange-300 bg-orange-100 px-1 py-0.5 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300"${ssrRenderAttr("title", dimensionsTitle(variant))}${_scopeId}>${ssrInterpolate(unref(t)("ownDimensions"))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div></td><td class="px-2 py-1"${_scopeId}>`);
              if (variantValues(variant).length) {
                _push2(`<div class="flex max-w-64 flex-wrap gap-1"${ssrRenderAttr("title", valuesTooltip(variant))}${_scopeId}><!--[-->`);
                ssrRenderList(variantValues(variant), (item) => {
                  _push2(`<span class="inline-flex max-w-full items-center rounded-sm border border-slate-300 bg-white px-1.5 py-0.5 text-[9px] text-slate-700 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"${_scopeId}>`);
                  if (attributeColor(item)) {
                    _push2(`<span class="mr-1 h-2.5 w-2.5 shrink-0 rounded-full border border-slate-400" style="${ssrRenderStyle({ backgroundColor: attributeColor(item) })}"${_scopeId}></span>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  _push2(`<span class="truncate"${_scopeId}>${ssrInterpolate(valueTitle(item))}</span></span>`);
                });
                _push2(`<!--]--></div>`);
              } else {
                _push2(`<div class="text-center text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
              }
              _push2(`<div class="mt-1 text-[9px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("attributes"))}: ${ssrInterpolate(variant.values_count ?? variantValues(variant).length)}</div></td><td class="whitespace-nowrap px-2 py-1"${_scopeId}><div class="text-right"${_scopeId}><div class="text-sm font-bold text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(formatMoney(variant.effective_price, effectiveCurrency(variant)))}</div>`);
              if (variant.effective_old_price) {
                _push2(`<div class="text-sm text-slate-400 line-through"${_scopeId}>${ssrInterpolate(formatMoney(variant.effective_old_price, effectiveCurrency(variant)))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.effective_wholesale_price) {
                _push2(`<div class="mt-0.5 text-[10px] text-blue-700 dark:text-blue-300"${ssrRenderAttr("title", unref(t)("wholesalePrice"))}${_scopeId}>${ssrInterpolate(formatMoney(variant.effective_wholesale_price, effectiveCurrency(variant)))} `);
                if (variant.effective_wholesale_min_quantity) {
                  _push2(`<span${_scopeId}> × ${ssrInterpolate(variant.effective_wholesale_min_quantity)}</span>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></td><td class="whitespace-nowrap px-2 py-1 text-center"${_scopeId}><div class="${ssrRenderClass([variant.in_stock && variant.has_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300", "text-sm font-bold"])}"${_scopeId}>${ssrInterpolate(variant.quantity ?? 0)}</div><div class="${ssrRenderClass([variant.in_stock && variant.has_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300", "text-[9px]"])}"${_scopeId}>${ssrInterpolate(variant.in_stock && variant.has_stock ? unref(t)("inStock") : unref(t)("outOfStock"))}</div></td><td class="whitespace-nowrap px-2 py-1"${_scopeId}><div class="whitespace-nowrap px-2 py-1"${_scopeId}><button type="button" class="${ssrRenderClass([variant.is_default ? "cursor-default border-amber-400 bg-violet-100 text-amber-700 dark:bg-violet-900/40 dark:text-amber-300" : "border-slate-400 bg-white text-slate-500 hover:border-amber-400 hover:text-amber-600 dark:bg-slate-800 dark:text-slate-300", "inline-flex items-center gap-1 rounded-sm border px-2 py-1 text-[9px] font-semibold transition"])}"${ssrIncludeBooleanAttr(variant.is_default) ? " disabled" : ""}${_scopeId}><svg class="h-3.5 w-3.5 fill-current" viewBox="0 0 24 24"${_scopeId}><path d="m12 2.5 2.93 5.94 6.56.95-4.75 4.63 1.12 6.54L12 17.48l-5.86 3.08 1.12-6.54-4.75-4.63 6.56-.95L12 2.5Z"${_scopeId}></path></svg> ${ssrInterpolate(variant.is_default ? unref(t)("defaultVariant") : unref(t)("makePrimary"))}</button><div class="flex flex-col items-center justify-center gap-1"${_scopeId}><span class="rounded-sm px-2 py-1 text-[9px] font-semibold"${_scopeId}>${ssrInterpolate(statusBadge(variant.status).text)}</span><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(variant.moderation_status).class, "rounded-sm border px-2 py-1 text-[9px] font-semibold"])}"${ssrRenderAttr("title", variant.moderation_note && variant.moderated_at ? `${variant.moderation_note} [${formatDate(variant.moderated_at)}]` : variant.moderation_note || null)}${_scopeId}>${ssrInterpolate(moderationBadge(variant.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isAdmin: __props.isAdmin,
                status: (variant == null ? void 0 : variant.moderation_status) ?? 0,
                initialNote: (variant == null ? void 0 : variant.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emit("approve", variant, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div><div class="flex items-center justify-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: variant.activity,
                title: variant.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", variant)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.marketProductVariants.edit", { marketProductVariant: variant.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emit("delete", variant)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="w-px whitespace-nowrap px-1 py-1 text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(isSelected(variant.id)) ? " checked" : ""}${_scopeId}></td></tr>`);
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
                  createVNode("td", {
                    class: "w-px whitespace-nowrap px-1 py-1 text-center text-blue-600 dark:text-blue-200",
                    title: publicationTitle(variant)
                  }, toDisplayString(variant.id), 9, ["title"]),
                  createVNode("td", { class: "w-16 px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: imageUrl(variant),
                        alt: imageAlt(variant),
                        title: imageTitle(variant),
                        class: "h-10 w-14 rounded-sm border border-slate-300 object-cover dark:border-slate-600"
                      }, null, 8, ["src", "alt", "title"])
                    ]),
                    variant.images_count ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "mt-0.5 text-center text-[9px] text-slate-500 dark:text-slate-300"
                    }, toDisplayString(variant.images_count), 1)) : createCommentVNode("", true)
                  ]),
                  createVNode("td", { class: "px-2 py-1" }, [
                    createVNode("div", { class: "text-left" }, [
                      createVNode("div", {
                        class: "text-[10px] font-semibold uppercase text-blue-600 dark:text-blue-300",
                        title: productTitle(variant)
                      }, toDisplayString(truncateText(productTitle(variant), 75)), 9, ["title"]),
                      createVNode("div", {
                        class: "mt-0.5 text-xs font-semibold text-sky-700 dark:text-sky-300",
                        title: variantShort(variant) || variantSubtitle(variant) || variantTitle(variant)
                      }, toDisplayString(truncateText(variantTitle(variant), 85)), 9, ["title"]),
                      variantSubtitle(variant) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "mt-0.5 text-[10px] text-slate-600 dark:text-slate-300"
                      }, toDisplayString(truncateText(variantSubtitle(variant), 75)), 1)) : createCommentVNode("", true),
                      createVNode("div", { class: "mt-1 flex flex-wrap gap-1 text-[9px]" }, [
                        variant.code ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "rounded-sm border border-slate-400 bg-slate-100 px-1 py-0.5 dark:bg-slate-800",
                          title: unref(t)("code")
                        }, toDisplayString(truncateText(variant.code, 25)), 9, ["title"])) : createCommentVNode("", true),
                        variant.barcode ? (openBlock(), createBlock("div", {
                          key: 1,
                          class: "mt-0.5 flex items-center gap-1 text-[9px]"
                        }, [
                          (openBlock(), createBlock("svg", {
                            class: "h-5 w-5 shrink-0 fill-current",
                            viewBox: "0 0 512 512"
                          }, [
                            createVNode("path", { d: "M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z" })
                          ])),
                          variant.barcode ? (openBlock(), createBlock("span", {
                            key: 0,
                            class: "rounded-sm border border-slate-400 bg-slate-200 px-1 py-0.5 text-slate-700 dark:bg-slate-800 dark:text-slate-300",
                            title: unref(t)("barcode")
                          }, toDisplayString(truncateText(variant.barcode, 24)), 9, ["title"])) : createCommentVNode("", true)
                        ])) : createCommentVNode("", true),
                        variant.sku ? (openBlock(), createBlock("div", {
                          key: 2,
                          class: "rounded-sm border border-blue-400 bg-blue-100 px-1 py-0.5 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300",
                          title: unref(t)("sku")
                        }, " SKU: " + toDisplayString(truncateText(variant.sku, 50)), 9, ["title"])) : createCommentVNode("", true),
                        variant.vendor_code ? (openBlock(), createBlock("div", {
                          key: 3,
                          class: "rounded-sm border border-violet-400 bg-violet-100 px-1 py-0.5 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300",
                          title: unref(t)("vendorCode")
                        }, toDisplayString(truncateText(variant.vendor_code, 25)), 9, ["title"])) : createCommentVNode("", true)
                      ]),
                      createVNode("div", { class: "mt-1 flex flex-wrap gap-1 text-[10px]" }, [
                        variant.has_own_price ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "rounded-sm px-1 py-0.5 text-fuchsia-700 dark:text-fuchsia-300"
                        }, toDisplayString(unref(t)("ownPrice")), 1)) : createCommentVNode("", true),
                        variant.has_own_currency ? (openBlock(), createBlock("span", {
                          key: 1,
                          class: "rounded-sm border border-cyan-300 bg-cyan-100 px-1 py-0.5 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300"
                        }, toDisplayString(unref(t)("ownCurrency")), 1)) : createCommentVNode("", true),
                        variant.has_own_dimensions ? (openBlock(), createBlock("span", {
                          key: 2,
                          class: "rounded-sm border border-orange-300 bg-orange-100 px-1 py-0.5 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300",
                          title: dimensionsTitle(variant)
                        }, toDisplayString(unref(t)("ownDimensions")), 9, ["title"])) : createCommentVNode("", true)
                      ])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-1" }, [
                    variantValues(variant).length ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex max-w-64 flex-wrap gap-1",
                      title: valuesTooltip(variant)
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(variantValues(variant), (item) => {
                        return openBlock(), createBlock("span", {
                          key: item.id,
                          class: "inline-flex max-w-full items-center rounded-sm border border-slate-300 bg-white px-1.5 py-0.5 text-[9px] text-slate-700 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"
                        }, [
                          attributeColor(item) ? (openBlock(), createBlock("span", {
                            key: 0,
                            class: "mr-1 h-2.5 w-2.5 shrink-0 rounded-full border border-slate-400",
                            style: { backgroundColor: attributeColor(item) }
                          }, null, 4)) : createCommentVNode("", true),
                          createVNode("span", { class: "truncate" }, toDisplayString(valueTitle(item)), 1)
                        ]);
                      }), 128))
                    ], 8, ["title"])) : (openBlock(), createBlock("div", {
                      key: 1,
                      class: "text-center text-slate-400"
                    }, toDisplayString(unref(t)("noData")), 1)),
                    createVNode("div", { class: "mt-1 text-[9px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("attributes")) + ": " + toDisplayString(variant.values_count ?? variantValues(variant).length), 1)
                  ]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-1" }, [
                    createVNode("div", { class: "text-right" }, [
                      createVNode("div", { class: "text-sm font-bold text-teal-700 dark:text-teal-300" }, toDisplayString(formatMoney(variant.effective_price, effectiveCurrency(variant))), 1),
                      variant.effective_old_price ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-sm text-slate-400 line-through"
                      }, toDisplayString(formatMoney(variant.effective_old_price, effectiveCurrency(variant))), 1)) : createCommentVNode("", true),
                      variant.effective_wholesale_price ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "mt-0.5 text-[10px] text-blue-700 dark:text-blue-300",
                        title: unref(t)("wholesalePrice")
                      }, [
                        createTextVNode(toDisplayString(formatMoney(variant.effective_wholesale_price, effectiveCurrency(variant))) + " ", 1),
                        variant.effective_wholesale_min_quantity ? (openBlock(), createBlock("span", { key: 0 }, " × " + toDisplayString(variant.effective_wholesale_min_quantity), 1)) : createCommentVNode("", true)
                      ], 8, ["title"])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-1 text-center" }, [
                    createVNode("div", {
                      class: ["text-sm font-bold", variant.in_stock && variant.has_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300"]
                    }, toDisplayString(variant.quantity ?? 0), 3),
                    createVNode("div", {
                      class: ["text-[9px]", variant.in_stock && variant.has_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300"]
                    }, toDisplayString(variant.in_stock && variant.has_stock ? unref(t)("inStock") : unref(t)("outOfStock")), 3)
                  ]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-1" }, [
                    createVNode("div", { class: "whitespace-nowrap px-2 py-1" }, [
                      createVNode("button", {
                        type: "button",
                        class: ["inline-flex items-center gap-1 rounded-sm border px-2 py-1 text-[9px] font-semibold transition", variant.is_default ? "cursor-default border-amber-400 bg-violet-100 text-amber-700 dark:bg-violet-900/40 dark:text-amber-300" : "border-slate-400 bg-white text-slate-500 hover:border-amber-400 hover:text-amber-600 dark:bg-slate-800 dark:text-slate-300"],
                        disabled: variant.is_default,
                        onClick: ($event) => emit("make-default", variant)
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-3.5 w-3.5 fill-current",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("path", { d: "m12 2.5 2.93 5.94 6.56.95-4.75 4.63 1.12 6.54L12 17.48l-5.86 3.08 1.12-6.54-4.75-4.63 6.56-.95L12 2.5Z" })
                        ])),
                        createTextVNode(" " + toDisplayString(variant.is_default ? unref(t)("defaultVariant") : unref(t)("makePrimary")), 1)
                      ], 10, ["disabled", "onClick"]),
                      createVNode("div", { class: "flex flex-col items-center justify-center gap-1" }, [
                        createVNode("span", { class: "rounded-sm px-2 py-1 text-[9px] font-semibold" }, toDisplayString(statusBadge(variant.status).text), 1),
                        createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                          createVNode("span", {
                            class: ["rounded-sm border px-2 py-1 text-[9px] font-semibold", moderationBadge(variant.moderation_status).class],
                            title: variant.moderation_note && variant.moderated_at ? `${variant.moderation_note} [${formatDate(variant.moderated_at)}]` : variant.moderation_note || null
                          }, toDisplayString(moderationBadge(variant.moderation_status).text), 11, ["title"]),
                          createVNode(_sfc_main$5, {
                            isAdmin: __props.isAdmin,
                            status: (variant == null ? void 0 : variant.moderation_status) ?? 0,
                            initialNote: (variant == null ? void 0 : variant.moderation_note) || "",
                            mode: "toggle",
                            onSubmit: ({ status, note }) => emit("approve", variant, status, note)
                          }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                      createVNode(_sfc_main$6, {
                        isActive: variant.activity,
                        title: variant.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", variant)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.marketProductVariants.edit", { marketProductVariant: variant.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emit("delete", variant)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "w-px whitespace-nowrap px-1 py-1 text-center" }, [
                    createVNode("input", {
                      type: "checkbox",
                      checked: isSelected(variant.id),
                      onChange: ($event) => emit("toggle-select", variant.id)
                    }, null, 40, ["checked", "onChange"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProductVariant/Table/VariantTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "VariantCardGrid",
  __ssrInlineRender: true,
  props: {
    variants: {
      type: Array,
      default: () => []
    },
    selectedVariants: {
      type: Array,
      default: () => []
    },
    isAdmin: {
      type: Boolean,
      default: false
    }
  },
  emits: [
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all",
    "approve",
    "make-default"
  ],
  setup(__props, { emit: __emit }) {
    const { t, locale } = useI18n();
    const props = __props;
    const emits = __emit;
    const localVariants = ref([]);
    const openedDetails = ref([]);
    watch(
      () => props.variants,
      (newValue) => {
        localVariants.value = JSON.parse(JSON.stringify(newValue || []));
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      emits("update-sort-order", localVariants.value.map((variant) => variant.id));
    };
    const normalizedSelectedIds = () => {
      return props.selectedVariants.map((id) => Number(id));
    };
    const isSelected = (variantId) => {
      return normalizedSelectedIds().includes(Number(variantId));
    };
    const allSelected = () => {
      return localVariants.value.length > 0 && localVariants.value.every((variant) => isSelected(variant.id));
    };
    const variantTranslation = (variant) => {
      return (variant == null ? void 0 : variant.translation) || {};
    };
    const variantTitle = (variant) => {
      var _a;
      return (variant == null ? void 0 : variant.display_title) || ((_a = variantTranslation(variant)) == null ? void 0 : _a.title) || (variant == null ? void 0 : variant.code) || (variant == null ? void 0 : variant.sku) || `ID: ${variant == null ? void 0 : variant.id}`;
    };
    const variantSubtitle = (variant) => {
      var _a;
      return ((_a = variantTranslation(variant)) == null ? void 0 : _a.subtitle) || "";
    };
    const variantShort = (variant) => {
      var _a;
      return ((_a = variantTranslation(variant)) == null ? void 0 : _a.short) || "";
    };
    const productTitle = (variant) => {
      var _a, _b;
      return ((_b = (_a = variant == null ? void 0 : variant.product) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || `ID: ${variant == null ? void 0 : variant.market_product_id}`;
    };
    const isDetailsOpen = (variantId) => {
      return openedDetails.value.includes(Number(variantId));
    };
    const toggleDetails = (variantId) => {
      const id = Number(variantId);
      if (isDetailsOpen(id)) {
        openedDetails.value = openedDetails.value.filter((itemId) => itemId !== id);
        return;
      }
      openedDetails.value.push(id);
    };
    const getPrimaryImage = (variant) => {
      if (!Array.isArray(variant == null ? void 0 : variant.images) || !variant.images.length) {
        return null;
      }
      return [...variant.images].sort((left, right) => {
        var _a, _b;
        const leftOrder = Number(((_a = left == null ? void 0 : left.pivot) == null ? void 0 : _a.order) ?? (left == null ? void 0 : left.order) ?? 0);
        const rightOrder = Number(((_b = right == null ? void 0 : right.pivot) == null ? void 0 : _b.order) ?? (right == null ? void 0 : right.order) ?? 0);
        return leftOrder - rightOrder;
      })[0];
    };
    const imageUrl = (variant) => {
      const image = getPrimaryImage(variant);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "/storage/market/market_product_variant_images/default-image.png";
    };
    const imageAlt = (variant) => {
      var _a;
      return ((_a = getPrimaryImage(variant)) == null ? void 0 : _a.alt) || variantTitle(variant);
    };
    const imageTitle = (variant) => {
      var _a;
      return ((_a = getPrimaryImage(variant)) == null ? void 0 : _a.caption) || variantTitle(variant);
    };
    const variantValues = (variant) => {
      return Array.isArray(variant == null ? void 0 : variant.values) ? variant.values : [];
    };
    const valueTitle = (item) => {
      var _a, _b, _c;
      return (item == null ? void 0 : item.display_value) || ((_b = (_a = item == null ? void 0 : item.attribute_value) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((_c = item == null ? void 0 : item.attribute_value) == null ? void 0 : _c.code) || "";
    };
    const attributeColor = (item) => {
      var _a, _b;
      return ((_a = item == null ? void 0 : item.attribute_value) == null ? void 0 : _a.color) || ((_b = item == null ? void 0 : item.attribute) == null ? void 0 : _b.color) || null;
    };
    const valuesTooltip = (variant) => {
      return variantValues(variant).map((item) => valueTitle(item)).filter(Boolean).join("\n");
    };
    const effectiveCurrency = (variant) => {
      var _a;
      return (variant == null ? void 0 : variant.effective_currency) || (variant == null ? void 0 : variant.currency) || ((_a = variant == null ? void 0 : variant.product) == null ? void 0 : _a.currency) || null;
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
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
      const integerPart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandsSeparator);
      const formattedAmount = precision > 0 ? `${integerPart}${decimalSeparator}${parts[1]}` : integerPart;
      const symbol = String((currency == null ? void 0 : currency.symbol) || (currency == null ? void 0 : currency.code) || "").trim();
      if (!symbol) {
        return formattedAmount;
      }
      return (currency == null ? void 0 : currency.symbol_first) ? `${symbol}${formattedAmount}` : `${formattedAmount} ${symbol}`;
    };
    const statusLabelKeyMap = {
      draft: "statusDraft",
      published: "statusPublished",
      archived: "statusArchived"
    };
    const getStatusLabel = (status) => {
      return t(statusLabelKeyMap[status] || status || "no");
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
    const formatDate = (dateString) => {
      if (!dateString) {
        return "";
      }
      const date = new Date(dateString);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return date.toLocaleDateString(locale.value || "ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    const truncateText = (text, maxLength = 110) => {
      if (!text) {
        return "";
      }
      const value = String(text);
      return value.length > maxLength ? `${value.slice(0, maxLength).trimEnd()}…` : value;
    };
    const formatDimension = (value, unit = "") => {
      if (value === null || value === void 0 || value === "") {
        return "—";
      }
      return `${value}${unit ? ` ${unit}` : ""}`;
    };
    const publicationTitle = (variant) => {
      const values = [];
      if (variant == null ? void 0 : variant.published_at) {
        values.push(`${t("publishedAt")}: ${formatDate(variant.published_at)}`);
      }
      if (variant == null ? void 0 : variant.show_from_at) {
        values.push(`${t("showFromAt")}: ${formatDate(variant.show_from_at)}`);
      }
      if (variant == null ? void 0 : variant.show_to_at) {
        values.push(`${t("showToAt")}: ${formatDate(variant.show_to_at)}`);
      }
      return values.join("\n");
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-700 shadow-lg" }, _attrs))}><div class="flex items-center justify-between border-b border-slate-400 dark:border-slate-500 px-3 py-2"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedVariants.length)}</div>`);
      if (localVariants.value.length) {
        _push(`<label class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localVariants.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localVariants.value,
          "onUpdate:modelValue": ($event) => localVariants.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".handle",
          class: "grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: variant }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<article class="relative flex h-full flex-col rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm transition-shadow duration-150 hover:shadow-md"${_scopeId}><header class="flex items-center justify-between border-b border-dashed border-slate-400 dark:border-slate-500 px-2 py-1"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `sort: ${variant.sort}`)}${_scopeId}> ID: ${ssrInterpolate(variant.id)}</div><button type="button" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"${ssrRenderAttr("title", isDetailsOpen(variant.id) ? unref(t)("hideDetails") : unref(t)("showDetails"))}${_scopeId}><svg class="${ssrRenderClass([{ "rotate-180": isDetailsOpen(variant.id) }, "h-4 w-4 transition-transform duration-200"])}" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"${_scopeId}></path></svg></button></div><div class="flex items-center space-x-2"${_scopeId}><div class="${ssrRenderClass([moderationBadge(variant.moderation_status).class, "rounded-sm border px-2 py-0.5 text-[10px] font-semibold"])}"${_scopeId}>${ssrInterpolate(moderationBadge(variant.moderation_status).text)}</div><input type="checkbox"${ssrIncludeBooleanAttr(isSelected(variant.id)) ? " checked" : ""}${_scopeId}></div></header><div style="${ssrRenderStyle(isDetailsOpen(variant.id) ? null : { display: "none" })}" class="border-b border-dashed border-slate-300 bg-slate-100/80 px-2 py-2 text-[10px] text-slate-600 dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-300"${_scopeId}><div class="grid grid-cols-1 gap-1 sm:grid-cols-2"${_scopeId}>`);
              if (variant.code) {
                _push2(`<div class="rounded-sm border border-slate-400 bg-white px-1.5 py-1 dark:bg-slate-800"${ssrRenderAttr("title", unref(t)("code"))}${_scopeId}><span class="font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("code"))}:</span> ${ssrInterpolate(truncateText(variant.code, 50))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.sku) {
                _push2(`<div class="rounded-sm border border-blue-400 bg-blue-100 px-1.5 py-1 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"${ssrRenderAttr("title", unref(t)("sku"))}${_scopeId}><span class="font-semibold"${_scopeId}>SKU:</span> ${ssrInterpolate(truncateText(variant.sku, 50))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.vendor_code) {
                _push2(`<div class="rounded-sm border border-violet-400 bg-violet-100 px-1.5 py-1 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300"${ssrRenderAttr("title", unref(t)("vendorCode"))}${_scopeId}><span class="font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("vendorCode"))}:</span> ${ssrInterpolate(truncateText(variant.vendor_code, 50))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.barcode) {
                _push2(`<div class="flex items-center gap-1 rounded-sm border border-slate-400 bg-slate-200 px-1.5 py-1 text-slate-700 dark:bg-slate-800 dark:text-slate-300"${ssrRenderAttr("title", unref(t)("barcode"))}${_scopeId}><svg class="h-5 w-5 shrink-0 fill-current" viewBox="0 0 512 512"${_scopeId}><path d="M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z"${_scopeId}></path></svg><span class="min-w-0"${_scopeId}><span class="font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("barcode"))}:</span> ${ssrInterpolate(truncateText(variant.barcode, 50))}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (!variant.code && !variant.sku && !variant.vendor_code && !variant.barcode) {
                _push2(`<div class="text-center text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="relative w-full overflow-hidden bg-slate-200 dark:bg-slate-900"${_scopeId}><img${ssrRenderAttr("src", imageUrl(variant))}${ssrRenderAttr("alt", imageAlt(variant))}${ssrRenderAttr("title", imageTitle(variant))} class="aspect-[4/3] w-full object-cover"${_scopeId}><div class="absolute left-2 top-2 flex flex-wrap gap-1"${_scopeId}>`);
              if (variant.has_own_price) {
                _push2(`<span class="rounded-sm border border-violet-300 bg-violet-100/95 px-1.5 py-0.5 text-[9px] font-semibold text-violet-700 dark:border-violet-700 dark:bg-violet-900/80 dark:text-violet-300"${_scopeId}>${ssrInterpolate(unref(t)("ownPrice"))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.has_own_currency) {
                _push2(`<span class="rounded-sm border border-cyan-300 bg-cyan-100/95 px-1.5 py-0.5 text-[9px] font-semibold text-cyan-700 dark:border-cyan-700 dark:bg-cyan-900/80 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(unref(t)("ownCurrency"))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.has_own_dimensions) {
                _push2(`<span class="rounded-sm border border-orange-300 bg-orange-100/95 px-1.5 py-0.5 text-[9px] font-semibold text-orange-700 dark:border-orange-700 dark:bg-orange-900/80 dark:text-orange-300"${_scopeId}>${ssrInterpolate(unref(t)("ownDimensions"))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (variant.images_count) {
                _push2(`<div class="absolute bottom-2 right-2 rounded-sm bg-black/65 px-1.5 py-0.5 text-[10px] font-semibold text-white"${_scopeId}>${ssrInterpolate(variant.images_count)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="flex flex-1 flex-col px-3 py-2"${_scopeId}><div class="mb-1 text-center text-[10px] font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-300"${ssrRenderAttr("title", productTitle(variant))}${_scopeId}>${ssrInterpolate(truncateText(productTitle(variant), 70))}</div><h3 class="text-center text-xs font-semibold text-sky-700 dark:text-sky-300"${ssrRenderAttr("title", variantTitle(variant))}${_scopeId}>${ssrInterpolate(variantTitle(variant))}</h3>`);
              if (variantSubtitle(variant)) {
                _push2(`<div class="mt-0.5 line-clamp-1 text-center text-[10px] text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", variantSubtitle(variant))}${_scopeId}>${ssrInterpolate(variantSubtitle(variant))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (variantShort(variant)) {
                _push2(`<p class="my-2 line-clamp-3 text-center text-[11px] leading-4 text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", variantShort(variant))}${_scopeId}>${ssrInterpolate(truncateText(variantShort(variant)))}</p>`);
              } else {
                _push2(`<!---->`);
              }
              if (variantValues(variant).length) {
                _push2(`<div class="mt-2 flex flex-wrap justify-center gap-1"${ssrRenderAttr("title", valuesTooltip(variant))}${_scopeId}><!--[-->`);
                ssrRenderList(variantValues(variant), (item) => {
                  _push2(`<span class="inline-flex max-w-full items-center rounded-sm border border-slate-300 bg-white px-1.5 py-0.5 text-[10px] text-slate-700 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"${_scopeId}>`);
                  if (attributeColor(item)) {
                    _push2(`<span class="mr-1 h-2.5 w-2.5 shrink-0 rounded-full border border-slate-400" style="${ssrRenderStyle({ backgroundColor: attributeColor(item) })}"${_scopeId}></span>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  _push2(`<span class="truncate"${_scopeId}>${ssrInterpolate(valueTitle(item))}</span></span>`);
                });
                _push2(`<!--]--></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="mt-1 text-center"${_scopeId}><div class="text-base font-bold text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(formatMoney(variant.effective_price, effectiveCurrency(variant)))}</div>`);
              if (variant.effective_old_price) {
                _push2(`<div class="text-sm text-slate-400 line-through"${_scopeId}>${ssrInterpolate(formatMoney(variant.effective_old_price, effectiveCurrency(variant)))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.effective_wholesale_price) {
                _push2(`<div class="mt-0.5 text-[10px] font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("wholesalePrice"))}: <span class="text-indigo-600 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(formatMoney(variant.effective_wholesale_price, effectiveCurrency(variant)))}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="mt-2 flex items-center justify-center gap-2 text-[11px] font-semibold"${_scopeId}><span class="${ssrRenderClass(variant.in_stock && variant.has_stock ? "text-emerald-700 dark:text-emerald-300" : "text-rose-700 dark:text-rose-300")}"${_scopeId}>${ssrInterpolate(variant.in_stock && variant.has_stock ? unref(t)("inStock") : unref(t)("outOfStock"))}</span><span class="text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("quantity"))}: ${ssrInterpolate(variant.quantity ?? 0)}</span></div><div style="${ssrRenderStyle(isDetailsOpen(variant.id) ? null : { display: "none" })}" class="mt-3 space-y-2 border-t border-dashed border-slate-300 pt-2 text-[10px] text-slate-600 dark:border-slate-600 dark:text-slate-300"${_scopeId}><div class="grid grid-cols-2 gap-1"${_scopeId}><div${_scopeId}>${ssrInterpolate(unref(t)("weight"))}: ${ssrInterpolate(formatDimension(variant.effective_weight, "kg"))}</div><div${_scopeId}>${ssrInterpolate(unref(t)("length"))}: ${ssrInterpolate(formatDimension(variant.effective_length, "mm"))}</div><div${_scopeId}>${ssrInterpolate(unref(t)("width"))}: ${ssrInterpolate(formatDimension(variant.effective_width, "mm"))}</div><div${_scopeId}>${ssrInterpolate(unref(t)("height"))}: ${ssrInterpolate(formatDimension(variant.effective_height, "mm"))}</div></div>`);
              if (variant.show_from_at || variant.show_to_at || variant.published_at) {
                _push2(`<div class="text-center"${ssrRenderAttr("title", publicationTitle(variant))}${_scopeId}>`);
                if (variant.published_at) {
                  _push2(`<div${_scopeId}>${ssrInterpolate(unref(t)("publishedAt"))}: ${ssrInterpolate(formatDate(variant.published_at))}</div>`);
                } else {
                  _push2(`<!---->`);
                }
                if (variant.show_from_at) {
                  _push2(`<div${_scopeId}>${ssrInterpolate(unref(t)("showFromAt"))}: ${ssrInterpolate(formatDate(variant.show_from_at))}</div>`);
                } else {
                  _push2(`<!---->`);
                }
                if (variant.show_to_at) {
                  _push2(`<div${_scopeId}>${ssrInterpolate(unref(t)("showToAt"))}: ${ssrInterpolate(formatDate(variant.show_to_at))}</div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (variant.moderator) {
                _push2(`<div class="text-center"${_scopeId}>${ssrInterpolate(unref(t)("moderator"))}: ${ssrInterpolate(variant.moderator.name || variant.moderator.email || `ID: ${variant.moderated_by}`)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="flex justify-center gap-3"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(variant.images_count ?? 0)}</span><span${_scopeId}>${ssrInterpolate(unref(t)("attributes"))}: ${ssrInterpolate(variant.values_count ?? variantValues(variant).length)}</span></div></div><div class="flex justify-center items-center my-1"${_scopeId}>`);
              if (variant.is_default) {
                _push2(`<span class="rounded-sm text-wrap font-semibold text-[10px] text-yellow-700 dark:text-yellow-200"${_scopeId}>${ssrInterpolate(unref(t)("basicProductVariant"))}</span>`);
              } else {
                _push2(`<span class="text-[9px] text-slate-400"${_scopeId}> — </span>`);
              }
              _push2(`</div><div class="mt-auto"${_scopeId}><div class="text-center text-[11px] font-semibold text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(unref(t)("status"))}: ${ssrInterpolate(getStatusLabel(variant.status))}</div><div class="mt-2 flex items-center justify-center space-x-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(variant.moderation_status).class, "rounded-sm border px-2 py-1 text-[10px] font-semibold"])}"${ssrRenderAttr("title", variant.moderation_note && variant.moderated_at ? `${variant.moderation_note} [${formatDate(variant.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(variant.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isAdmin: __props.isAdmin,
                status: (variant == null ? void 0 : variant.moderation_status) ?? 0,
                initialNote: (variant == null ? void 0 : variant.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emits("approve", variant, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div><footer class="flex items-center justify-center border-t border-dashed border-slate-400 dark:border-slate-500 px-3 py-2"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}><button type="button" class="${ssrRenderClass([variant.is_default ? "cursor-default border-amber-400 bg-violet-100 text-amber-600 dark:bg-violet-900/40 dark:text-amber-300" : "border-slate-400 bg-white text-slate-400 hover:border-amber-400 hover:text-amber-500 dark:bg-slate-800", "inline-flex h-7 w-7 items-center justify-center rounded-sm border transition"])}"${ssrIncludeBooleanAttr(variant.is_default) ? " disabled" : ""}${ssrRenderAttr("title", variant.is_default ? unref(t)("defaultVariant") : unref(t)("makePrimary"))}${_scopeId}><svg class="h-4 w-4 fill-current" viewBox="0 0 24 24"${_scopeId}><path d="m12 2.5 2.93 5.94 6.56.95-4.75 4.63 1.12 6.54L12 17.48l-5.86 3.08 1.12-6.54-4.75-4.63 6.56-.95L12 2.5Z"${_scopeId}></path></svg></button>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: variant.activity,
                title: variant.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emits("toggle-activity", variant)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route(
                  "admin.marketProductVariants.edit",
                  { marketProductVariant: variant.id }
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emits("delete", variant)
              }, null, _parent2, _scopeId));
              _push2(`</div></footer></article>`);
            } else {
              return [
                createVNode("article", { class: "relative flex h-full flex-col rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm transition-shadow duration-150 hover:shadow-md" }, [
                  createVNode("header", { class: "flex items-center justify-between border-b border-dashed border-slate-400 dark:border-slate-500 px-2 py-1" }, [
                    createVNode("div", { class: "flex items-center space-x-2" }, [
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
                        class: "rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:text-blue-100",
                        title: `sort: ${variant.sort}`
                      }, " ID: " + toDisplayString(variant.id), 9, ["title"]),
                      createVNode("button", {
                        type: "button",
                        class: "text-slate-400 hover:text-blue-600 dark:hover:text-blue-300",
                        title: isDetailsOpen(variant.id) ? unref(t)("hideDetails") : unref(t)("showDetails"),
                        onClick: withModifiers(($event) => toggleDetails(variant.id), ["prevent"])
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: ["h-4 w-4 transition-transform duration-200", { "rotate-180": isDetailsOpen(variant.id) }],
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
                      createVNode("div", {
                        class: ["rounded-sm border px-2 py-0.5 text-[10px] font-semibold", moderationBadge(variant.moderation_status).class]
                      }, toDisplayString(moderationBadge(variant.moderation_status).text), 3),
                      createVNode("input", {
                        type: "checkbox",
                        checked: isSelected(variant.id),
                        onChange: ($event) => emits("toggle-select", variant.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  withDirectives(createVNode("div", { class: "border-b border-dashed border-slate-300 bg-slate-100/80 px-2 py-2 text-[10px] text-slate-600 dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-300" }, [
                    createVNode("div", { class: "grid grid-cols-1 gap-1 sm:grid-cols-2" }, [
                      variant.code ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "rounded-sm border border-slate-400 bg-white px-1.5 py-1 dark:bg-slate-800",
                        title: unref(t)("code")
                      }, [
                        createVNode("span", { class: "font-semibold" }, toDisplayString(unref(t)("code")) + ":", 1),
                        createTextVNode(" " + toDisplayString(truncateText(variant.code, 50)), 1)
                      ], 8, ["title"])) : createCommentVNode("", true),
                      variant.sku ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "rounded-sm border border-blue-400 bg-blue-100 px-1.5 py-1 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300",
                        title: unref(t)("sku")
                      }, [
                        createVNode("span", { class: "font-semibold" }, "SKU:"),
                        createTextVNode(" " + toDisplayString(truncateText(variant.sku, 50)), 1)
                      ], 8, ["title"])) : createCommentVNode("", true),
                      variant.vendor_code ? (openBlock(), createBlock("div", {
                        key: 2,
                        class: "rounded-sm border border-violet-400 bg-violet-100 px-1.5 py-1 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300",
                        title: unref(t)("vendorCode")
                      }, [
                        createVNode("span", { class: "font-semibold" }, toDisplayString(unref(t)("vendorCode")) + ":", 1),
                        createTextVNode(" " + toDisplayString(truncateText(variant.vendor_code, 50)), 1)
                      ], 8, ["title"])) : createCommentVNode("", true),
                      variant.barcode ? (openBlock(), createBlock("div", {
                        key: 3,
                        class: "flex items-center gap-1 rounded-sm border border-slate-400 bg-slate-200 px-1.5 py-1 text-slate-700 dark:bg-slate-800 dark:text-slate-300",
                        title: unref(t)("barcode")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-5 w-5 shrink-0 fill-current",
                          viewBox: "0 0 512 512"
                        }, [
                          createVNode("path", { d: "M0 448V64h18v384H0zm26.857-.273V64H36v383.727h-9.143zm27.143 0V64h8.857v383.727H54zm44.857 0V64h8.857v383.727h-8.857zm36 0V64h17.714v383.727h-17.714zm44.857 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm18 0V64h8.857v383.727h-8.857zm35.715 0V64h18v383.727h-18zm44.857 0V64h18v383.727h-18zm35.999 0V64h18.001v383.727h-18.001zm36.001 0V64h18.001v383.727h-18.001zm26.857 0V64h18v383.727h-18zm45.143 0V64h26.857v383.727h-26.857zm35.714 0V64h9.143v383.727H476zm18 .273V64h18v384h-18z" })
                        ])),
                        createVNode("span", { class: "min-w-0" }, [
                          createVNode("span", { class: "font-semibold" }, toDisplayString(unref(t)("barcode")) + ":", 1),
                          createTextVNode(" " + toDisplayString(truncateText(variant.barcode, 50)), 1)
                        ])
                      ], 8, ["title"])) : createCommentVNode("", true)
                    ]),
                    !variant.code && !variant.sku && !variant.vendor_code && !variant.barcode ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-center text-slate-400"
                    }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true)
                  ], 512), [
                    [vShow, isDetailsOpen(variant.id)]
                  ]),
                  createVNode("div", { class: "relative w-full overflow-hidden bg-slate-200 dark:bg-slate-900" }, [
                    createVNode("img", {
                      src: imageUrl(variant),
                      alt: imageAlt(variant),
                      title: imageTitle(variant),
                      class: "aspect-[4/3] w-full object-cover"
                    }, null, 8, ["src", "alt", "title"]),
                    createVNode("div", { class: "absolute left-2 top-2 flex flex-wrap gap-1" }, [
                      variant.has_own_price ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "rounded-sm border border-violet-300 bg-violet-100/95 px-1.5 py-0.5 text-[9px] font-semibold text-violet-700 dark:border-violet-700 dark:bg-violet-900/80 dark:text-violet-300"
                      }, toDisplayString(unref(t)("ownPrice")), 1)) : createCommentVNode("", true),
                      variant.has_own_currency ? (openBlock(), createBlock("span", {
                        key: 1,
                        class: "rounded-sm border border-cyan-300 bg-cyan-100/95 px-1.5 py-0.5 text-[9px] font-semibold text-cyan-700 dark:border-cyan-700 dark:bg-cyan-900/80 dark:text-cyan-300"
                      }, toDisplayString(unref(t)("ownCurrency")), 1)) : createCommentVNode("", true),
                      variant.has_own_dimensions ? (openBlock(), createBlock("span", {
                        key: 2,
                        class: "rounded-sm border border-orange-300 bg-orange-100/95 px-1.5 py-0.5 text-[9px] font-semibold text-orange-700 dark:border-orange-700 dark:bg-orange-900/80 dark:text-orange-300"
                      }, toDisplayString(unref(t)("ownDimensions")), 1)) : createCommentVNode("", true)
                    ]),
                    variant.images_count ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "absolute bottom-2 right-2 rounded-sm bg-black/65 px-1.5 py-0.5 text-[10px] font-semibold text-white"
                    }, toDisplayString(variant.images_count), 1)) : createCommentVNode("", true)
                  ]),
                  createVNode("div", { class: "flex flex-1 flex-col px-3 py-2" }, [
                    createVNode("div", {
                      class: "mb-1 text-center text-[10px] font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-300",
                      title: productTitle(variant)
                    }, toDisplayString(truncateText(productTitle(variant), 70)), 9, ["title"]),
                    createVNode("h3", {
                      class: "text-center text-xs font-semibold text-sky-700 dark:text-sky-300",
                      title: variantTitle(variant)
                    }, toDisplayString(variantTitle(variant)), 9, ["title"]),
                    variantSubtitle(variant) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "mt-0.5 line-clamp-1 text-center text-[10px] text-slate-600 dark:text-slate-300",
                      title: variantSubtitle(variant)
                    }, toDisplayString(variantSubtitle(variant)), 9, ["title"])) : createCommentVNode("", true),
                    variantShort(variant) ? (openBlock(), createBlock("p", {
                      key: 1,
                      class: "my-2 line-clamp-3 text-center text-[11px] leading-4 text-slate-600 dark:text-slate-300",
                      title: variantShort(variant)
                    }, toDisplayString(truncateText(variantShort(variant))), 9, ["title"])) : createCommentVNode("", true),
                    variantValues(variant).length ? (openBlock(), createBlock("div", {
                      key: 2,
                      class: "mt-2 flex flex-wrap justify-center gap-1",
                      title: valuesTooltip(variant)
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(variantValues(variant), (item) => {
                        return openBlock(), createBlock("span", {
                          key: item.id,
                          class: "inline-flex max-w-full items-center rounded-sm border border-slate-300 bg-white px-1.5 py-0.5 text-[10px] text-slate-700 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200"
                        }, [
                          attributeColor(item) ? (openBlock(), createBlock("span", {
                            key: 0,
                            class: "mr-1 h-2.5 w-2.5 shrink-0 rounded-full border border-slate-400",
                            style: { backgroundColor: attributeColor(item) }
                          }, null, 4)) : createCommentVNode("", true),
                          createVNode("span", { class: "truncate" }, toDisplayString(valueTitle(item)), 1)
                        ]);
                      }), 128))
                    ], 8, ["title"])) : createCommentVNode("", true),
                    createVNode("div", { class: "mt-1 text-center" }, [
                      createVNode("div", { class: "text-base font-bold text-teal-700 dark:text-teal-300" }, toDisplayString(formatMoney(variant.effective_price, effectiveCurrency(variant))), 1),
                      variant.effective_old_price ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-sm text-slate-400 line-through"
                      }, toDisplayString(formatMoney(variant.effective_old_price, effectiveCurrency(variant))), 1)) : createCommentVNode("", true),
                      variant.effective_wholesale_price ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "mt-0.5 text-[10px] font-semibold"
                      }, [
                        createTextVNode(toDisplayString(unref(t)("wholesalePrice")) + ": ", 1),
                        createVNode("span", { class: "text-indigo-600 dark:text-indigo-300" }, toDisplayString(formatMoney(variant.effective_wholesale_price, effectiveCurrency(variant))), 1)
                      ])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "mt-2 flex items-center justify-center gap-2 text-[11px] font-semibold" }, [
                      createVNode("span", {
                        class: variant.in_stock && variant.has_stock ? "text-emerald-700 dark:text-emerald-300" : "text-rose-700 dark:text-rose-300"
                      }, toDisplayString(variant.in_stock && variant.has_stock ? unref(t)("inStock") : unref(t)("outOfStock")), 3),
                      createVNode("span", { class: "text-slate-600 dark:text-slate-300" }, toDisplayString(unref(t)("quantity")) + ": " + toDisplayString(variant.quantity ?? 0), 1)
                    ]),
                    withDirectives(createVNode("div", { class: "mt-3 space-y-2 border-t border-dashed border-slate-300 pt-2 text-[10px] text-slate-600 dark:border-slate-600 dark:text-slate-300" }, [
                      createVNode("div", { class: "grid grid-cols-2 gap-1" }, [
                        createVNode("div", null, toDisplayString(unref(t)("weight")) + ": " + toDisplayString(formatDimension(variant.effective_weight, "kg")), 1),
                        createVNode("div", null, toDisplayString(unref(t)("length")) + ": " + toDisplayString(formatDimension(variant.effective_length, "mm")), 1),
                        createVNode("div", null, toDisplayString(unref(t)("width")) + ": " + toDisplayString(formatDimension(variant.effective_width, "mm")), 1),
                        createVNode("div", null, toDisplayString(unref(t)("height")) + ": " + toDisplayString(formatDimension(variant.effective_height, "mm")), 1)
                      ]),
                      variant.show_from_at || variant.show_to_at || variant.published_at ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-center",
                        title: publicationTitle(variant)
                      }, [
                        variant.published_at ? (openBlock(), createBlock("div", { key: 0 }, toDisplayString(unref(t)("publishedAt")) + ": " + toDisplayString(formatDate(variant.published_at)), 1)) : createCommentVNode("", true),
                        variant.show_from_at ? (openBlock(), createBlock("div", { key: 1 }, toDisplayString(unref(t)("showFromAt")) + ": " + toDisplayString(formatDate(variant.show_from_at)), 1)) : createCommentVNode("", true),
                        variant.show_to_at ? (openBlock(), createBlock("div", { key: 2 }, toDisplayString(unref(t)("showToAt")) + ": " + toDisplayString(formatDate(variant.show_to_at)), 1)) : createCommentVNode("", true)
                      ], 8, ["title"])) : createCommentVNode("", true),
                      variant.moderator ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "text-center"
                      }, toDisplayString(unref(t)("moderator")) + ": " + toDisplayString(variant.moderator.name || variant.moderator.email || `ID: ${variant.moderated_by}`), 1)) : createCommentVNode("", true),
                      createVNode("div", { class: "flex justify-center gap-3" }, [
                        createVNode("span", null, toDisplayString(unref(t)("images")) + ": " + toDisplayString(variant.images_count ?? 0), 1),
                        createVNode("span", null, toDisplayString(unref(t)("attributes")) + ": " + toDisplayString(variant.values_count ?? variantValues(variant).length), 1)
                      ])
                    ], 512), [
                      [vShow, isDetailsOpen(variant.id)]
                    ]),
                    createVNode("div", { class: "flex justify-center items-center my-1" }, [
                      variant.is_default ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "rounded-sm text-wrap font-semibold text-[10px] text-yellow-700 dark:text-yellow-200"
                      }, toDisplayString(unref(t)("basicProductVariant")), 1)) : (openBlock(), createBlock("span", {
                        key: 1,
                        class: "text-[9px] text-slate-400"
                      }, " — "))
                    ]),
                    createVNode("div", { class: "mt-auto" }, [
                      createVNode("div", { class: "text-center text-[11px] font-semibold text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(unref(t)("status")) + ": " + toDisplayString(getStatusLabel(variant.status)), 1),
                      createVNode("div", { class: "mt-2 flex items-center justify-center space-x-1" }, [
                        createVNode("span", {
                          class: ["rounded-sm border px-2 py-1 text-[10px] font-semibold", moderationBadge(variant.moderation_status).class],
                          title: variant.moderation_note && variant.moderated_at ? `${variant.moderation_note} [${formatDate(variant.moderated_at)}]` : null
                        }, toDisplayString(moderationBadge(variant.moderation_status).text), 11, ["title"]),
                        createVNode(_sfc_main$5, {
                          isAdmin: __props.isAdmin,
                          status: (variant == null ? void 0 : variant.moderation_status) ?? 0,
                          initialNote: (variant == null ? void 0 : variant.moderation_note) || "",
                          mode: "toggle",
                          onSubmit: ({ status, note }) => emits("approve", variant, status, note)
                        }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                      ])
                    ])
                  ]),
                  createVNode("footer", { class: "flex items-center justify-center border-t border-dashed border-slate-400 dark:border-slate-500 px-3 py-2" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode("button", {
                        type: "button",
                        class: ["inline-flex h-7 w-7 items-center justify-center rounded-sm border transition", variant.is_default ? "cursor-default border-amber-400 bg-violet-100 text-amber-600 dark:bg-violet-900/40 dark:text-amber-300" : "border-slate-400 bg-white text-slate-400 hover:border-amber-400 hover:text-amber-500 dark:bg-slate-800"],
                        disabled: variant.is_default,
                        title: variant.is_default ? unref(t)("defaultVariant") : unref(t)("makePrimary"),
                        onClick: ($event) => emits("make-default", variant)
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-4 w-4 fill-current",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("path", { d: "m12 2.5 2.93 5.94 6.56.95-4.75 4.63 1.12 6.54L12 17.48l-5.86 3.08 1.12-6.54-4.75-4.63 6.56-.95L12 2.5Z" })
                        ]))
                      ], 10, ["disabled", "title", "onClick"]),
                      createVNode(_sfc_main$6, {
                        isActive: variant.activity,
                        title: variant.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emits("toggle-activity", variant)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route(
                          "admin.marketProductVariants.edit",
                          { marketProductVariant: variant.id }
                        )
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emits("delete", variant)
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
        _push(`<div class="px-3 py-10 text-center text-sm text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProductVariant/View/VariantCardGrid.vue");
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
    adminMarketProductVariantsProcessingMode: {
      type: String,
      default: "frontend"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    adminMarketProductVariantsPerPage: {
      type: Number,
      default: 10
    },
    adminMarketProductVariantsDefaultSort: {
      type: String,
      default: "idDesc"
    },
    variants: {
      type: [Array, Object],
      default: () => []
    },
    variantsCount: {
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
    filters: {
      type: Object,
      default: () => ({})
    },
    products: {
      type: Array,
      default: () => []
    },
    currentProductId: {
      type: Number,
      default: null
    },
    error: {
      type: String,
      default: ""
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  setup(__props) {
    var _a;
    const { t, locale } = useI18n();
    const toast = useToast();
    const page = usePage();
    const props = __props;
    const isAdmin = computed(() => {
      var _a2, _b, _c;
      const roles = ((_c = (_b = (_a2 = page.props) == null ? void 0 : _a2.auth) == null ? void 0 : _b.user) == null ? void 0 : _c.roles) || [];
      return roles.some((role) => (role == null ? void 0 : role.name) === "admin");
    });
    const getVariantTranslation = (variant) => {
      return (variant == null ? void 0 : variant.translation) || {};
    };
    const getVariantTitle = (variant) => {
      var _a2;
      return (variant == null ? void 0 : variant.display_title) || ((_a2 = getVariantTranslation(variant)) == null ? void 0 : _a2.title) || (variant == null ? void 0 : variant.code) || (variant == null ? void 0 : variant.sku) || `ID: ${variant == null ? void 0 : variant.id}`;
    };
    const getProductTitle = (variant) => {
      var _a2, _b;
      return ((_b = (_a2 = variant == null ? void 0 : variant.product) == null ? void 0 : _a2.translation) == null ? void 0 : _b.title) || `ID: ${variant == null ? void 0 : variant.market_product_id}`;
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
    const selectedProductId = ref(
      props.currentProductId ?? ((_a = props.filters) == null ? void 0 : _a.market_product_id) ?? null
    );
    const productOptions = computed(() => props.products || []);
    const productOptionLabel = (product) => {
      if (!product) {
        return "";
      }
      const title = product.title || product.code || product.sku || `ID: ${product.id}`;
      const variantsCount = Number(product.variants_count || 0);
      return `[ID: ${product.id}] ${title} — ${t("variants")}: ${variantsCount}`;
    };
    const reloadWithProductFilter = () => {
      const params = {
        ...Object.fromEntries(new URLSearchParams(window.location.search)),
        page: void 0
      };
      if (selectedProductId.value) {
        params.market_product_id = selectedProductId.value;
      } else {
        delete params.market_product_id;
      }
      router.get(
        route("admin.marketProductVariants.index"),
        params,
        {
          preserveScroll: true,
          preserveState: false,
          replace: true
        }
      );
    };
    const handleProductFilterChange = () => {
      selectedVariants.value = [];
      currentPage.value = 1;
      reloadWithProductFilter();
    };
    const viewMode = ref(localStorage.getItem("admin_view_mode_market_product_variants") || "cards");
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_market_product_variants", value);
    });
    const itemsPerPage = ref(props.adminMarketProductVariantsPerPage || 10);
    watch(itemsPerPage, (newValue, oldValue) => {
      if (newValue === oldValue) {
        return;
      }
      router.put(
        route("admin.settings.updateAdminCountMarketProductVariants"),
        {
          value: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(`Показ ${newValue} вариантов на странице.`);
          },
          onError: (errors) => {
            toast.error((errors == null ? void 0 : errors.value) || "Ошибка обновления количества вариантов.");
          }
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminMarketProductVariantsDefaultSort || "idDesc");
    watch(sortParam, (newValue, oldValue) => {
      if (newValue === oldValue) {
        return;
      }
      router.put(
        route("admin.settings.updateAdminSortMarketProductVariants"),
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
                  ...Object.fromEntries(new URLSearchParams(window.location.search)),
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
            toast.info("Сортировка вариантов товаров успешно изменена.");
          },
          onError: (errors) => {
            toast.error((errors == null ? void 0 : errors.value) || "Ошибка обновления сортировки вариантов товаров.");
          }
        }
      );
    });
    const variantsList = computed(() => {
      var _a2, _b, _c, _d;
      if (Array.isArray(props.variants)) {
        return props.variants;
      }
      if (Array.isArray((_a2 = props.variants) == null ? void 0 : _a2.data)) {
        return props.variants.data;
      }
      if (Array.isArray((_c = (_b = props.variants) == null ? void 0 : _b.data) == null ? void 0 : _c.data)) {
        return props.variants.data.data;
      }
      if (Array.isArray((_d = props.variants) == null ? void 0 : _d.resource)) {
        return props.variants.resource;
      }
      return [];
    });
    const localVariants = ref([]);
    watch(
      variantsList,
      (newValue) => {
        localVariants.value = JSON.parse(JSON.stringify(newValue || []));
      },
      {
        immediate: true,
        deep: true
      }
    );
    const showConfirmDeleteModal = ref(false);
    const variantToDeleteId = ref(null);
    const variantToDeleteTitle = ref("");
    const confirmDelete = (variantOrId, title = null) => {
      if (typeof variantOrId === "object") {
        variantToDeleteId.value = variantOrId == null ? void 0 : variantOrId.id;
        variantToDeleteTitle.value = title || getVariantTitle(variantOrId);
      } else {
        variantToDeleteId.value = variantOrId;
        variantToDeleteTitle.value = title || `ID: ${variantOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      variantToDeleteId.value = null;
      variantToDeleteTitle.value = "";
    };
    const deleteVariant = () => {
      if (variantToDeleteId.value === null) {
        return;
      }
      const id = variantToDeleteId.value;
      const title = variantToDeleteTitle.value;
      router.delete(
        route("admin.marketProductVariants.destroy", {
          marketProductVariant: id
        }),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(`Вариант товара "${title || `ID: ${id}`}" удалён.`);
          },
          onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0];
            const message = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[firstKey]) || "Ошибка при удалении варианта товара.";
            toast.error(`${message} (Вариант: ${title || `ID: ${id}`})`);
          },
          onFinish: closeModal
        }
      );
    };
    const patchLocalVariant = (variantId, callback) => {
      const index = localVariants.value.findIndex((variant) => Number(variant.id) === Number(variantId));
      if (index === -1) {
        return;
      }
      callback(localVariants.value[index]);
    };
    const toggleActivity = (variant) => {
      if (!(variant == null ? void 0 : variant.id)) {
        return;
      }
      const activity = !variant.activity;
      const title = getVariantTitle(variant);
      router.put(
        route("admin.actions.marketProductVariants.updateActivity", {
          marketProductVariant: variant.id
        }),
        {
          activity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalVariant(variant.id, (item) => {
              item.activity = activity;
              item.is_active = activity;
            });
            toast.success(
              activity ? `Вариант товара "${title}" активирован.` : `Вариант товара "${title}" деактивирован.`
            );
          },
          onError: (errors) => {
            toast.error((errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности варианта "${title}".`);
          }
        }
      );
    };
    const approveVariant = (variant, status = 1, note = "") => {
      if (!(variant == null ? void 0 : variant.id) || !isAdmin.value) {
        return;
      }
      router.put(
        route("admin.actions.marketProductVariants.approve", {
          marketProductVariant: variant.id
        }),
        {
          moderation_status: status,
          moderation_note: note
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalVariant(variant.id, (item) => {
              item.moderation_status = status;
              item.is_pending = Number(status) === 0;
              item.is_approved = Number(status) === 1;
              item.is_rejected = Number(status) === 2;
              item.moderation_note = note;
            });
            if (Number(status) === 1) {
              toast.success("Вариант товара одобрен.");
            } else if (Number(status) === 2) {
              toast.success("Вариант товара отклонён.");
            } else {
              toast.success("Вариант товара возвращён на модерацию.");
            }
          },
          onError: (errors) => {
            toast.error((errors == null ? void 0 : errors.moderation_status) || (errors == null ? void 0 : errors.general) || "Ошибка модерации варианта товара.");
          }
        }
      );
    };
    const makeDefaultVariant = (variant) => {
      if (!(variant == null ? void 0 : variant.id) || variant.is_default) {
        return;
      }
      router.put(
        route("admin.actions.marketProductVariants.makeDefault", {
          marketProductVariant: variant.id
        }),
        {},
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localVariants.value.forEach((item) => {
              if (Number(item.market_product_id) !== Number(variant.market_product_id)) {
                return;
              }
              item.is_default = Number(item.id) === Number(variant.id);
            });
            toast.success(
              `Вариант «${getVariantTitle(variant)}» назначен основным.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.general) || "Ошибка назначения основного варианта товара."
            );
          }
        }
      );
    };
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const variantMatchesSearch = (variant, query) => {
      var _a2, _b, _c, _d;
      if (!query) {
        return true;
      }
      const translation = getVariantTranslation(variant);
      const searchValues = [
        variant == null ? void 0 : variant.code,
        variant == null ? void 0 : variant.sku,
        variant == null ? void 0 : variant.vendor_code,
        variant == null ? void 0 : variant.barcode,
        variant == null ? void 0 : variant.status,
        variant == null ? void 0 : variant.moderation_note,
        translation == null ? void 0 : translation.title,
        translation == null ? void 0 : translation.subtitle,
        translation == null ? void 0 : translation.short,
        translation == null ? void 0 : translation.description,
        (_b = (_a2 = variant == null ? void 0 : variant.product) == null ? void 0 : _a2.translation) == null ? void 0 : _b.title,
        (_c = variant == null ? void 0 : variant.moderator) == null ? void 0 : _c.name,
        (_d = variant == null ? void 0 : variant.moderator) == null ? void 0 : _d.email
      ];
      return searchValues.some((value) => normalize(value).includes(query));
    };
    const filteredVariants = computed(() => {
      if (props.useServerProcessing) {
        return localVariants.value;
      }
      const query = normalize(searchQuery.value);
      return localVariants.value.filter((variant) => variantMatchesSearch(variant, query));
    });
    const compareText = (left, right) => {
      return normalize(left).localeCompare(normalize(right), locale.value);
    };
    const byNumberAsc = (field) => (a, b) => {
      return safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byNumberDesc = (field) => (a, b) => {
      return safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byStringAsc = (field) => (a, b) => {
      return compareText(a == null ? void 0 : a[field], b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byStringDesc = (field) => (a, b) => {
      return compareText(b == null ? void 0 : b[field], a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byDateAsc = (field) => (a, b) => {
      return safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byDateDesc = (field) => (a, b) => {
      return safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const sortedVariants = computed(() => {
      const list = [...filteredVariants.value];
      if (sortParam.value === "activity") {
        return list.filter((variant) => Boolean(variant.activity)).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "inactive") {
        return list.filter((variant) => !variant.activity).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "inStock") {
        return list.filter((variant) => {
          return Boolean(variant.in_stock) && safeNumber(variant.quantity) > 0;
        }).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "outOfStock") {
        return list.filter((variant) => {
          return !variant.in_stock || safeNumber(variant.quantity) <= 0;
        }).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "default") {
        return list.filter((variant) => Boolean(variant.is_default)).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "notDefault") {
        return list.filter((variant) => !variant.is_default).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "statusDraft") {
        return list.filter((variant) => {
          return variant.status === "draft";
        }).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "statusPublished") {
        return list.filter((variant) => {
          return variant.status === "published";
        }).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "statusArchived") {
        return list.filter((variant) => {
          return variant.status === "archived";
        }).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "moderationPending") {
        return list.filter((variant) => {
          return moderationNum(variant.moderation_status) === 0;
        }).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "moderationApproved") {
        return list.filter((variant) => {
          return moderationNum(variant.moderation_status) === 1;
        }).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "moderationRejected") {
        return list.filter((variant) => {
          return moderationNum(variant.moderation_status) === 2;
        }).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      const sortMap = {
        /** ID и ручная сортировка */
        idAsc: (a, b) => safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        idDesc: (a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        /** Название варианта */
        titleAsc: (a, b) => {
          return compareText(
            getVariantTitle(a),
            getVariantTitle(b)
          ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
        },
        titleDesc: (a, b) => {
          return compareText(
            getVariantTitle(b),
            getVariantTitle(a)
          ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
        },
        /** Название родительского товара */
        productTitleAsc: (a, b) => {
          return compareText(
            getProductTitle(a),
            getProductTitle(b)
          ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
        },
        productTitleDesc: (a, b) => {
          return compareText(
            getProductTitle(b),
            getProductTitle(a)
          ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
        },
        /** Торговые коды */
        codeAsc: byStringAsc("code"),
        codeDesc: byStringDesc("code"),
        skuAsc: byStringAsc("sku"),
        skuDesc: byStringDesc("sku"),
        vendorCodeAsc: byStringAsc("vendor_code"),
        vendorCodeDesc: byStringDesc("vendor_code"),
        barcodeAsc: byStringAsc("barcode"),
        barcodeDesc: byStringDesc("barcode"),
        /**
         * Эффективные цены.
         *
         * Используется собственное значение варианта
         * либо унаследованное значение товара.
         */
        priceAsc: byNumberAsc("effective_price"),
        priceDesc: byNumberDesc("effective_price"),
        oldPriceAsc: byNumberAsc("effective_old_price"),
        oldPriceDesc: byNumberDesc("effective_old_price"),
        purchasePriceAsc: byNumberAsc(
          "effective_purchase_price"
        ),
        purchasePriceDesc: byNumberDesc(
          "effective_purchase_price"
        ),
        wholesalePriceAsc: byNumberAsc(
          "effective_wholesale_price"
        ),
        wholesalePriceDesc: byNumberDesc(
          "effective_wholesale_price"
        ),
        wholesaleMinQuantityAsc: byNumberAsc(
          "effective_wholesale_min_quantity"
        ),
        wholesaleMinQuantityDesc: byNumberDesc(
          "effective_wholesale_min_quantity"
        ),
        /** Остаток и наличие */
        quantityAsc: byNumberAsc("quantity"),
        quantityDesc: byNumberDesc("quantity"),
        inStockAsc: byNumberAsc("in_stock"),
        inStockDesc: byNumberDesc("in_stock"),
        /**
         * Эффективные физические параметры.
         *
         * Используются собственные значения варианта
         * либо унаследованные значения товара.
         */
        weightAsc: byNumberAsc("effective_weight"),
        weightDesc: byNumberDesc("effective_weight"),
        lengthAsc: byNumberAsc("effective_length"),
        lengthDesc: byNumberDesc("effective_length"),
        widthAsc: byNumberAsc("effective_width"),
        widthDesc: byNumberDesc("effective_width"),
        heightAsc: byNumberAsc("effective_height"),
        heightDesc: byNumberDesc("effective_height"),
        /** Основной вариант */
        defaultAsc: byNumberAsc("is_default"),
        defaultDesc: byNumberDesc("is_default"),
        /** Количество связей */
        valuesAsc: byNumberAsc("values_count"),
        valuesDesc: byNumberDesc("values_count"),
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        /** Активность */
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        /** Статус публикации */
        statusAsc: byStringAsc("status"),
        statusDesc: byStringDesc("status"),
        /** Статус модерации */
        moderationStatusAsc: byNumberAsc(
          "moderation_status"
        ),
        moderationStatusDesc: byNumberDesc(
          "moderation_status"
        ),
        /** Даты */
        publishedAtAsc: byDateAsc("published_at"),
        publishedAtDesc: byDateDesc("published_at"),
        showFromAtAsc: byDateAsc("show_from_at"),
        showFromAtDesc: byDateDesc("show_from_at"),
        showToAtAsc: byDateAsc("show_to_at"),
        showToAtDesc: byDateDesc("show_to_at"),
        createdAtAsc: byDateAsc("created_at"),
        createdAtDesc: byDateDesc("created_at"),
        updatedAtAsc: byDateAsc("updated_at"),
        updatedAtDesc: byDateDesc("updated_at")
      };
      const sorter = sortMap[sortParam.value] || sortMap.idDesc;
      return list.sort(sorter);
    });
    const frontendPaginatedVariants = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      return sortedVariants.value.slice(start, start + itemsPerPage.value);
    });
    watch(
      [searchQuery, sortParam, itemsPerPage, viewMode],
      () => {
        currentPage.value = 1;
      }
    );
    const displayedVariants = computed(() => {
      return props.useServerProcessing ? localVariants.value : frontendPaginatedVariants.value;
    });
    const selectedVariants = ref([]);
    const isSelected = (variantId) => {
      return selectedVariants.value.includes(variantId);
    };
    const toggleSelectVariant = (variantOrId) => {
      const id = typeof variantOrId === "object" ? variantOrId == null ? void 0 : variantOrId.id : variantOrId;
      if (!id) {
        return;
      }
      if (isSelected(id)) {
        selectedVariants.value = selectedVariants.value.filter((selectedId) => selectedId !== id);
        return;
      }
      selectedVariants.value.push(id);
    };
    const toggleAll = (eventOrValue) => {
      var _a2;
      const checked = typeof eventOrValue === "boolean" ? eventOrValue : Boolean(((_a2 = eventOrValue == null ? void 0 : eventOrValue.target) == null ? void 0 : _a2.checked) ?? (eventOrValue == null ? void 0 : eventOrValue.checked));
      if (!checked) {
        selectedVariants.value = [];
        return;
      }
      selectedVariants.value = displayedVariants.value.map((variant) => variant == null ? void 0 : variant.id).filter(Boolean);
    };
    const allDisplayedSelected = computed(() => {
      const ids = displayedVariants.value.map((variant) => variant == null ? void 0 : variant.id).filter(Boolean);
      return ids.length > 0 && ids.every((id) => selectedVariants.value.includes(id));
    });
    const bulkToggleActivity = (activity) => {
      if (!selectedVariants.value.length) {
        toast.warning("Выберите хотя бы один вариант товара.");
        return;
      }
      const ids = [...selectedVariants.value];
      router.put(
        route("admin.actions.marketProductVariants.bulkUpdateActivity"),
        {
          ids,
          activity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localVariants.value.forEach((variant) => {
              if (ids.includes(variant.id)) {
                variant.activity = activity;
                variant.is_active = activity;
              }
            });
            selectedVariants.value = [];
            toast.success("Активность выбранных вариантов товаров обновлена.");
          },
          onError: (errors) => {
            toast.error((errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового изменения активности.");
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedVariants.value.length) {
        toast.warning("Выберите хотя бы один вариант товара для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные варианты товаров?")) {
        return;
      }
      router.delete(
        route("admin.actions.marketProductVariants.bulkDestroy"),
        {
          data: {
            ids: selectedVariants.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedVariants.value = [];
            toast.success("Выбранные варианты товаров успешно удалены.");
          },
          onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0];
            toast.error((errors == null ? void 0 : errors[firstKey]) || "Ошибка массового удаления вариантов товаров.");
          }
        }
      );
    };
    const handleBulkAction = (event) => {
      var _a2;
      const action = (_a2 = event == null ? void 0 : event.target) == null ? void 0 : _a2.value;
      if (action === "selectAll") {
        toggleAll(true);
      } else if (action === "deselectAll") {
        toggleAll(false);
      } else if (action === "activate") {
        bulkToggleActivity(true);
      } else if (action === "deactivate") {
        bulkToggleActivity(false);
      } else if (action === "delete") {
        bulkDelete();
      }
      if (event == null ? void 0 : event.target) {
        event.target.value = "";
      }
    };
    const handleSortOrderUpdate = (newOrderIds) => {
      const items = (newOrderIds || []).map((id, index) => ({
        id,
        sort: index
      }));
      if (!items.length) {
        return;
      }
      router.put(
        route("admin.actions.marketProductVariants.updateSortBulk"),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            items.forEach((item) => {
              patchLocalVariant(item.id, (variant) => {
                variant.sort = item.sort;
              });
            });
            toast.success("Сортировка вариантов товаров обновлена.");
          },
          onError: (errors) => {
            console.error("Ошибка сортировки вариантов товаров:", errors);
            toast.error((errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.items) || "Ошибка обновления сортировки вариантов товаров.");
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("marketProductVariants")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("marketProductVariants"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("marketProductVariants")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("marketProductVariants")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="flex flex-col gap-3 mb-3 sm:flex-row sm:items-center sm:justify-between"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              href: _ctx.route("admin.marketProductVariants.create", selectedProductId.value ? { market_product_id: selectedProductId.value } : {})
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addMarketProductVariant"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addMarketProductVariant")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              "setting-key": "adminMarketProductVariantsProcessingMode",
              mode: __props.adminMarketProductVariantsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.variantsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.error) {
              _push2(`<div class="mb-3 rounded-sm border border-red-400 bg-red-100 px-3 py-2 text-sm text-red-700 dark:border-red-700 dark:bg-red-900/40 dark:text-red-200"${_scopeId}>${ssrInterpolate(__props.error)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.variantsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mb-3"${_scopeId}><select class="w-full px-2 py-1 text-xs bg-slate-200 dark:bg-cyan-900 dark:text-slate-100 border border-slate-400 dark:border-slate-600 rounded-sm"${_scopeId}><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray(selectedProductId.value) ? ssrLooseContain(selectedProductId.value, null) : ssrLooseEqual(selectedProductId.value, null)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("allProducts"))}</option><!--[-->`);
            ssrRenderList(productOptions.value, (product) => {
              _push2(`<option${ssrRenderAttr("value", product.id)}${ssrIncludeBooleanAttr(Array.isArray(selectedProductId.value) ? ssrLooseContain(selectedProductId.value, product.id) : ssrLooseEqual(selectedProductId.value, product.id)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(productOptionLabel(product))}</option>`);
            });
            _push2(`<!--]--></select></div>`);
            if (__props.variantsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.variantsCount) {
              _push2(`<div class="flex flex-col md:flex-row items-center justify-between gap-3 my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountMarketProductVariants"
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
            if (__props.variantsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$f, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.variantsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.variantsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$g, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.variantsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredVariants.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.variants }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                variants: displayedVariants.value,
                "selected-variants": selectedVariants.value,
                "all-selected": allDisplayedSelected.value,
                "is-admin": isAdmin.value,
                onToggleSelect: toggleSelectVariant,
                onToggleAll: toggleAll,
                onToggleActivity: toggleActivity,
                onApprove: approveVariant,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onMakeDefault: makeDefaultVariant
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                variants: displayedVariants.value,
                "selected-variants": selectedVariants.value,
                "is-admin": isAdmin.value,
                onToggleSelect: toggleSelectVariant,
                onToggleAll: toggleAll,
                onToggleActivity: toggleActivity,
                onApprove: approveVariant,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onMakeDefault: makeDefaultVariant
              }, null, _parent2, _scopeId));
            }
            if (!__props.variantsCount) {
              _push2(`<div class="py-12 text-center text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else if (!__props.useServerProcessing && !displayedVariants.value.length) {
              _push2(`<div class="py-10 text-center text-slate-500 dark:text-slate-300"${_scopeId}> По вашему запросу варианты товаров не найдены. </div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.variantsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-4"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredVariants.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.variants }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$j, {
              show: showConfirmDeleteModal.value,
              "on-cancel": closeModal,
              "on-confirm": deleteVariant,
              "cancel-text": unref(t)("cancel"),
              "confirm-text": unref(t)("yesDelete"),
              onClose: closeModal
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<p class="text-sm text-slate-700 dark:text-slate-200"${_scopeId2}> Удалить вариант товара <strong${_scopeId2}>${ssrInterpolate(variantToDeleteTitle.value)}</strong> ? </p>`);
                } else {
                  return [
                    createVNode("p", { class: "text-sm text-slate-700 dark:text-slate-200" }, [
                      createTextVNode(" Удалить вариант товара "),
                      createVNode("strong", null, toDisplayString(variantToDeleteTitle.value), 1),
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
                    createVNode(_sfc_main$9, {
                      href: _ctx.route("admin.marketProductVariants.create", selectedProductId.value ? { market_product_id: selectedProductId.value } : {})
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addMarketProductVariant")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$a, {
                      "setting-key": "adminMarketProductVariantsProcessingMode",
                      mode: __props.adminMarketProductVariantsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.variantsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.error ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "mb-3 rounded-sm border border-red-400 bg-red-100 px-3 py-2 text-sm text-red-700 dark:border-red-700 dark:bg-red-900/40 dark:text-red-200"
                  }, toDisplayString(__props.error), 1)) : createCommentVNode("", true),
                  __props.variantsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  createVNode("div", { class: "mb-3" }, [
                    withDirectives(createVNode("select", {
                      "onUpdate:modelValue": ($event) => selectedProductId.value = $event,
                      class: "w-full px-2 py-1 text-xs bg-slate-200 dark:bg-cyan-900 dark:text-slate-100 border border-slate-400 dark:border-slate-600 rounded-sm",
                      onChange: handleProductFilterChange
                    }, [
                      createVNode("option", { value: null }, toDisplayString(unref(t)("allProducts")), 1),
                      (openBlock(true), createBlock(Fragment, null, renderList(productOptions.value, (product) => {
                        return openBlock(), createBlock("option", {
                          key: product.id,
                          value: product.id
                        }, toDisplayString(productOptionLabel(product)), 9, ["value"]);
                      }), 128))
                    ], 40, ["onUpdate:modelValue"]), [
                      [
                        vModelSelect,
                        selectedProductId.value,
                        void 0,
                        { number: true }
                      ]
                    ])
                  ]),
                  __props.variantsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                    key: 2,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.variantsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col md:flex-row items-center justify-between gap-3 my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$d, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$e, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountMarketProductVariants"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.variantsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$f, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.variantsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$g, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.variantsCount ? (openBlock(), createBlock("div", {
                    key: 5,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredVariants.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.variants
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    variants: displayedVariants.value,
                    "selected-variants": selectedVariants.value,
                    "all-selected": allDisplayedSelected.value,
                    "is-admin": isAdmin.value,
                    onToggleSelect: toggleSelectVariant,
                    onToggleAll: toggleAll,
                    onToggleActivity: toggleActivity,
                    onApprove: approveVariant,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onMakeDefault: makeDefaultVariant
                  }, null, 8, ["variants", "selected-variants", "all-selected", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 7,
                    variants: displayedVariants.value,
                    "selected-variants": selectedVariants.value,
                    "is-admin": isAdmin.value,
                    onToggleSelect: toggleSelectVariant,
                    onToggleAll: toggleAll,
                    onToggleActivity: toggleActivity,
                    onApprove: approveVariant,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onMakeDefault: makeDefaultVariant
                  }, null, 8, ["variants", "selected-variants", "is-admin"])),
                  !__props.variantsCount ? (openBlock(), createBlock("div", {
                    key: 8,
                    class: "py-12 text-center text-slate-500 dark:text-slate-300"
                  }, toDisplayString(unref(t)("noData")), 1)) : !__props.useServerProcessing && !displayedVariants.value.length ? (openBlock(), createBlock("div", {
                    key: 9,
                    class: "py-10 text-center text-slate-500 dark:text-slate-300"
                  }, " По вашему запросу варианты товаров не найдены. ")) : createCommentVNode("", true),
                  __props.variantsCount ? (openBlock(), createBlock("div", {
                    key: 10,
                    class: "flex justify-center items-center flex-col md:flex-row mt-4"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredVariants.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.variants
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$j, {
                show: showConfirmDeleteModal.value,
                "on-cancel": closeModal,
                "on-confirm": deleteVariant,
                "cancel-text": unref(t)("cancel"),
                "confirm-text": unref(t)("yesDelete"),
                onClose: closeModal
              }, {
                default: withCtx(() => [
                  createVNode("p", { class: "text-sm text-slate-700 dark:text-slate-200" }, [
                    createTextVNode(" Удалить вариант товара "),
                    createVNode("strong", null, toDisplayString(variantToDeleteTitle.value), 1),
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketProductVariants/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
