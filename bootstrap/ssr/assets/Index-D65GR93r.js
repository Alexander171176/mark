import { mergeProps, unref, useSSRContext, ref, watch, computed, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, createTextVNode, withModifiers, withDirectives, vShow } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent, ssrRenderClass, ssrRenderStyle } from "vue/server-renderer";
import { Link, usePage, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$g } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$q } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$m } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$k, a as _sfc_main$n, b as _sfc_main$o } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$i } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$j, a as _sfc_main$p } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$l } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$h } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$6, a as _sfc_main$7, b as _sfc_main$8 } from "./RightToggle-r8SYzaEU.js";
import { _ as _sfc_main$d } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$f } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$e } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$c } from "./ModerationButton-D_ehimPY.js";
import { _ as _sfc_main$9, a as _sfc_main$a, b as _sfc_main$b } from "./HitButtonToggle-DYH18bkK.js";
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
const _sfc_main$5 = {
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
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProduct/Select/BulkActionSelect.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: {
      type: String,
      default: "idDesc"
    }
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit mt-2 mb-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>────────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>────────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option disabled>────────────────────</option><option value="urlAsc">URL A→Z</option><option value="urlDesc">URL Z→A</option><option disabled>────────────────────</option><option value="skuAsc">SKU A→Z</option><option value="skuDesc">SKU Z→A</option><option disabled>────────────────────</option><option value="vendorCodeAsc">${ssrInterpolate(unref(t)("vendorCode"))} A→Z</option><option value="vendorCodeDesc">${ssrInterpolate(unref(t)("vendorCode"))} Z→A</option><option disabled>────────────────────</option><option value="barcodeAsc">${ssrInterpolate(unref(t)("barcode"))} A→Z</option><option value="barcodeDesc">${ssrInterpolate(unref(t)("barcode"))} Z→A</option><option disabled>────────────────────</option><option value="companyAsc">${ssrInterpolate(unref(t)("marketCompany"))} A→Z</option><option value="companyDesc">${ssrInterpolate(unref(t)("marketCompany"))} Z→A</option><option disabled>────────────────────</option><option value="shopAsc">${ssrInterpolate(unref(t)("marketShop"))} A→Z</option><option value="shopDesc">${ssrInterpolate(unref(t)("marketShop"))} Z→A</option><option disabled>────────────────────</option><option value="brandAsc">${ssrInterpolate(unref(t)("brand"))} A→Z</option><option value="brandDesc">${ssrInterpolate(unref(t)("brand"))} Z→A</option><option disabled>────────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option><option disabled>────────────────────</option><option value="priceDesc">${ssrInterpolate(unref(t)("price"))} 9→0</option><option value="priceAsc">${ssrInterpolate(unref(t)("price"))} 0→9</option><option disabled>────────────────────</option><option value="oldPriceDesc">${ssrInterpolate(unref(t)("compareAtPrice"))} 9→0</option><option value="oldPriceAsc">${ssrInterpolate(unref(t)("compareAtPrice"))} 0→9</option><option disabled>────────────────────</option><option value="purchasePriceDesc">${ssrInterpolate(unref(t)("purchasePrice"))} 9→0</option><option value="purchasePriceAsc">${ssrInterpolate(unref(t)("purchasePrice"))} 0→9</option><option disabled>────────────────────</option><option value="wholesalePriceDesc">${ssrInterpolate(unref(t)("wholesalePrice"))} 9→0</option><option value="wholesalePriceAsc">${ssrInterpolate(unref(t)("wholesalePrice"))} 0→9</option><option disabled>────────────────────</option><option value="quantityDesc">${ssrInterpolate(unref(t)("quantity"))} 9→0</option><option value="quantityAsc">${ssrInterpolate(unref(t)("quantity"))} 0→9</option><option disabled>────────────────────</option><option value="inStockDesc">${ssrInterpolate(unref(t)("inStock"))} ON→OFF</option><option value="inStockAsc">${ssrInterpolate(unref(t)("inStock"))} OFF→ON</option><option value="inStock">${ssrInterpolate(unref(t)("inStock"))}</option><option value="outOfStock">${ssrInterpolate(unref(t)("outOfStock"))}</option><option disabled>────────────────────</option><option value="weightDesc">${ssrInterpolate(unref(t)("weight"))} 9→0</option><option value="weightAsc">${ssrInterpolate(unref(t)("weight"))} 0→9</option><option value="lengthDesc">${ssrInterpolate(unref(t)("length"))} 9→0</option><option value="lengthAsc">${ssrInterpolate(unref(t)("length"))} 0→9</option><option value="widthDesc">${ssrInterpolate(unref(t)("width"))} 9→0</option><option value="widthAsc">${ssrInterpolate(unref(t)("width"))} 0→9</option><option value="heightDesc">${ssrInterpolate(unref(t)("height"))} 9→0</option><option value="heightAsc">${ssrInterpolate(unref(t)("height"))} 0→9</option><option disabled>────────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option value="likesDesc">${ssrInterpolate(unref(t)("likes"))} 9→0</option><option value="likesAsc">${ssrInterpolate(unref(t)("likes"))} 0→9</option><option disabled>────────────────────</option><option value="ratingDesc">${ssrInterpolate(unref(t)("rating"))} 9→0</option><option value="ratingAsc">${ssrInterpolate(unref(t)("rating"))} 0→9</option><option value="ratingCountDesc">${ssrInterpolate(unref(t)("ratingCount"))} 9→0</option><option value="ratingCountAsc">${ssrInterpolate(unref(t)("ratingCount"))} 0→9</option><option disabled>────────────────────</option><option value="reviewsDesc">${ssrInterpolate(unref(t)("reviews"))} 9→0</option><option value="reviewsAsc">${ssrInterpolate(unref(t)("reviews"))} 0→9</option><option disabled>────────────────────</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>────────────────────</option><option value="categoriesDesc">${ssrInterpolate(unref(t)("categories"))} 9→0</option><option value="categoriesAsc">${ssrInterpolate(unref(t)("categories"))} 0→9</option><option disabled>────────────────────</option><option value="tagsDesc">${ssrInterpolate(unref(t)("tags"))} 9→0</option><option value="tagsAsc">${ssrInterpolate(unref(t)("tags"))} 0→9</option><option disabled>────────────────────</option><option value="attributesDesc">${ssrInterpolate(unref(t)("attributes"))} 9→0</option><option value="attributesAsc">${ssrInterpolate(unref(t)("attributes"))} 0→9</option><option disabled>────────────────────</option><option value="variantsDesc">${ssrInterpolate(unref(t)("marketProductVariants"))} 9→0</option><option value="variantsAsc">${ssrInterpolate(unref(t)("marketProductVariants"))} 0→9</option><option value="hasVariants">${ssrInterpolate(unref(t)("withVariants"))}</option><option value="withoutVariants">${ssrInterpolate(unref(t)("withoutVariants"))}</option><option disabled>────────────────────</option><option value="relatedProductsDesc">${ssrInterpolate(unref(t)("relatedProducts"))} 9→0</option><option value="relatedProductsAsc">${ssrInterpolate(unref(t)("relatedProducts"))} 0→9</option><option disabled>────────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>────────────────────</option><option value="newDesc">${ssrInterpolate(unref(t)("sortIsNew"))} ON→OFF</option><option value="newAsc">${ssrInterpolate(unref(t)("sortIsNew"))} OFF→ON</option><option value="new">${ssrInterpolate(unref(t)("sortIsNew"))}</option><option value="notNew">${ssrInterpolate(unref(t)("notNew"))}</option><option disabled>────────────────────</option><option value="hitDesc">${ssrInterpolate(unref(t)("sortIsHit"))} ON→OFF</option><option value="hitAsc">${ssrInterpolate(unref(t)("sortIsHit"))} OFF→ON</option><option value="hit">${ssrInterpolate(unref(t)("sortIsHit"))}</option><option value="notHit">${ssrInterpolate(unref(t)("notHit"))}</option><option disabled>────────────────────</option><option value="saleDesc">${ssrInterpolate(unref(t)("sortIsSale"))} ON→OFF</option><option value="saleAsc">${ssrInterpolate(unref(t)("sortIsSale"))} OFF→ON</option><option value="sale">${ssrInterpolate(unref(t)("sortIsSale"))}</option><option value="notSale">${ssrInterpolate(unref(t)("notSale"))}</option><option disabled>────────────────────</option><option value="leftDesc">${ssrInterpolate(unref(t)("inLeft"))} ON→OFF</option><option value="leftAsc">${ssrInterpolate(unref(t)("inLeft"))} OFF→ON</option><option value="left">${ssrInterpolate(unref(t)("inLeft"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("notLeft"))}</option><option disabled>────────────────────</option><option value="mainDesc">${ssrInterpolate(unref(t)("inMain"))} ON→OFF</option><option value="mainAsc">${ssrInterpolate(unref(t)("inMain"))} OFF→ON</option><option value="main">${ssrInterpolate(unref(t)("inMain"))}</option><option value="noMain">${ssrInterpolate(unref(t)("notMain"))}</option><option disabled>────────────────────</option><option value="rightDesc">${ssrInterpolate(unref(t)("inRight"))} ON→OFF</option><option value="rightAsc">${ssrInterpolate(unref(t)("inRight"))} OFF→ON</option><option value="right">${ssrInterpolate(unref(t)("inRight"))}</option><option value="noRight">${ssrInterpolate(unref(t)("notRight"))}</option><option disabled>────────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="statusDraft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="statusPublished">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="statusArchived">${ssrInterpolate(unref(t)("statusArchived"))}</option><option disabled>────────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>────────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option disabled>────────────────────</option><option value="showFromAtDesc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortStarted"))} ↓</option><option value="showFromAtAsc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortStarted"))} ↑</option><option value="showToAtDesc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortExpires"))} ↓</option><option value="showToAtAsc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortExpires"))} ↑</option><option disabled>────────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>────────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProduct/Sort/SortSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "ProductTable",
  __ssrInlineRender: true,
  props: {
    products: {
      type: Array,
      default: () => []
    },
    selectedProducts: {
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
    const localProducts = ref([]);
    watch(
      () => props.products,
      (newValue) => {
        localProducts.value = JSON.parse(JSON.stringify(newValue || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localProducts.value.map((product) => product.id)
      );
    };
    const allProductsSelected = computed(() => {
      if (!localProducts.value.length) {
        return false;
      }
      return localProducts.value.every((product) => {
        return props.selectedProducts.includes(product.id);
      });
    });
    const productTranslation = (product) => {
      return (product == null ? void 0 : product.translation) || {};
    };
    const productTitle = (product) => {
      var _a;
      return ((_a = productTranslation(product)) == null ? void 0 : _a.title) || `ID: ${product == null ? void 0 : product.id}`;
    };
    const productSubtitle = (product) => {
      var _a;
      return ((_a = productTranslation(product)) == null ? void 0 : _a.subtitle) || "";
    };
    const productShort = (product) => {
      var _a;
      return ((_a = productTranslation(product)) == null ? void 0 : _a.short) || "";
    };
    const relationTitle = (relation) => {
      var _a;
      return ((_a = relation == null ? void 0 : relation.translation) == null ? void 0 : _a.title) || "";
    };
    const companyTitle = (product) => {
      var _a;
      return relationTitle(product == null ? void 0 : product.company) || ((_a = product == null ? void 0 : product.company) == null ? void 0 : _a.legal_name) || "";
    };
    const shopTitle = (product) => {
      return relationTitle(product == null ? void 0 : product.shop);
    };
    const brandTitle = (product) => {
      return relationTitle(product == null ? void 0 : product.brand);
    };
    const categoryTitle = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.translation) == null ? void 0 : _a.title) || `ID: ${category == null ? void 0 : category.id}`;
    };
    const getPrimaryImage = (product) => {
      if (!Array.isArray(product == null ? void 0 : product.images) || !product.images.length) {
        return null;
      }
      return [...product.images].sort((left, right) => {
        return Number((left == null ? void 0 : left.order) ?? 0) - Number((right == null ? void 0 : right.order) ?? 0);
      })[0];
    };
    const imageUrl = (product) => {
      const image = getPrimaryImage(product);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/market/market_product_images/default-image.png";
    };
    const imageAlt = (product) => {
      const image = getPrimaryImage(product);
      return (image == null ? void 0 : image.alt) || productTitle(product);
    };
    const imageTitle = (product) => {
      const image = getPrimaryImage(product);
      return (image == null ? void 0 : image.caption) || productTitle(product);
    };
    const ownerTitle = (product) => {
      const owner = product == null ? void 0 : product.owner;
      if (!owner) {
        return t("noData");
      }
      return `${owner.name || ""}${owner.email ? ` — ${owner.email}` : ""}`.trim();
    };
    const ownerAvatar = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
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
      return date.toLocaleDateString(locale.value || "ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    const truncateText = (text, maxLength = 50) => {
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
      const precision = Number.isFinite(Number(currency == null ? void 0 : currency.precision)) ? Number(currency.precision) : 2;
      const thousandsSep = (currency == null ? void 0 : currency.thousands_sep) ?? " ";
      const decimalSep = (currency == null ? void 0 : currency.decimal_sep) ?? ".";
      const parts = amount.toFixed(precision).split(".");
      const integerPart = parts[0].replace(
        /\B(?=(\d{3})+(?!\d))/g,
        thousandsSep
      );
      const formattedAmount = precision > 0 ? `${integerPart}${decimalSep}${parts[1]}` : integerPart;
      const symbol = String(
        (currency == null ? void 0 : currency.symbol) || (currency == null ? void 0 : currency.code) || ""
      ).trim();
      if (!symbol) {
        return formattedAmount;
      }
      return (currency == null ? void 0 : currency.symbol_first) ? `${symbol}${formattedAmount}` : `${formattedAmount} ${symbol}`;
    };
    const formatRating = (product) => {
      return safeNumber(product == null ? void 0 : product.rating_avg).toFixed(1);
    };
    const productPublicUrl = (product) => {
      return `/market/products/${encodeURIComponent((product == null ? void 0 : product.url) || "")}`;
    };
    const categoriesTitle = (product) => {
      if (!Array.isArray(product == null ? void 0 : product.categories) || !product.categories.length) {
        return "";
      }
      return product.categories.map((category) => categoryTitle(category)).filter(Boolean).join(", ");
    };
    const mainCategory = (product) => {
      if (!Array.isArray(product == null ? void 0 : product.categories)) {
        return null;
      }
      return product.categories.find((category) => {
        var _a;
        return Boolean((_a = category == null ? void 0 : category.pivot) == null ? void 0 : _a.main);
      }) || product.categories[0] || null;
    };
    const mainCategoryTitle = (product) => {
      const category = mainCategory(product);
      return category ? categoryTitle(category) : "";
    };
    const attributeTitle = (item) => {
      var _a, _b;
      return ((_b = (_a = item == null ? void 0 : item.attribute) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || `ID: ${item == null ? void 0 : item.market_attribute_id}`;
    };
    const attributeReferenceValueTitle = (item) => {
      var _a, _b;
      return ((_b = (_a = item == null ? void 0 : item.attribute_value) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const attributeValueText = (item) => {
      const referenceTitle = attributeReferenceValueTitle(item);
      if (referenceTitle) {
        return referenceTitle;
      }
      if ((item == null ? void 0 : item.value_string) !== null && (item == null ? void 0 : item.value_string) !== void 0 && item.value_string !== "") {
        return String(item.value_string);
      }
      if ((item == null ? void 0 : item.value_number) !== null && (item == null ? void 0 : item.value_number) !== void 0 && item.value_number !== "") {
        return String(item.value_number);
      }
      if ((item == null ? void 0 : item.value_boolean) === true) {
        return t("yes");
      }
      if ((item == null ? void 0 : item.value_boolean) === false) {
        return t("no");
      }
      if (item == null ? void 0 : item.value_date) {
        return String(item.value_date);
      }
      return "";
    };
    const attributesTitle = (product) => {
      if (!Array.isArray(product == null ? void 0 : product.attribute_values) || !product.attribute_values.length) {
        return "";
      }
      return product.attribute_values.map((item) => {
        const attribute = attributeTitle(item);
        const value = attributeValueText(item);
        const unit = value && (item == null ? void 0 : item.unit) ? ` ${item.unit}` : "";
        return value ? `${attribute}: ${value}${unit}` : attribute;
      }).filter(Boolean).join("\n");
    };
    const supplierTitle = (product) => {
      const values = [];
      if (companyTitle(product)) {
        values.push(`Компания: ${companyTitle(product)}`);
      }
      if (brandTitle(product)) {
        values.push(`Бренд: ${brandTitle(product)}`);
      }
      return values.join("\n");
    };
    const publicationTitle = (product) => {
      const values = [`Sort: ${(product == null ? void 0 : product.sort) ?? 0}`];
      if (product == null ? void 0 : product.published_at) {
        values.push(`Публикация: ${formatDate(product.published_at)}`);
      }
      if (product == null ? void 0 : product.show_from_at) {
        values.push(`Показ с: ${formatDate(product.show_from_at)}`);
      }
      if (product == null ? void 0 : product.show_to_at) {
        values.push(`Показ до: ${formatDate(product.show_to_at)}`);
      }
      return values.join("\n");
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-200 bg-white shadow-lg dark:border-slate-600 dark:bg-slate-700" }, _attrs))}><div class="flex items-center justify-between border-b border-slate-400 px-3 py-2 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedProducts.length)}</div>`);
      if (localProducts.value.length) {
        _push(`<label class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(Boolean(allProductsSelected.value || __props.allSelected)) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localProducts.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="border border-solid border-gray-300 bg-slate-200 text-xs uppercase dark:border-gray-700 dark:bg-cyan-900"><tr><th class="w-px px-1 py-3"><svg class="h-4 w-4 fill-current text-slate-800 dark:text-slate-200" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="w-px whitespace-nowrap px-1 py-3"><div class="text-center font-semibold">${ssrInterpolate(unref(t)("id"))}</div></th><th class="w-px whitespace-nowrap px-1 py-3"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("owner"))}><svg class="h-6 w-6 shrink-0 fill-current" viewBox="0 0 24 24"><path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path><path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path><path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path><path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path><path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"></path></svg></div></th><th class="w-px px-1 py-3"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="h-6 w-6 shrink-0 fill-current" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32h384c35.3 0 64 28.7 64 64v320c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zm323.8 106.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4S78.8 416 88 416h336c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="min-w-64 px-2 py-3"><div class="text-left font-semibold">${ssrInterpolate(unref(t)("marketProduct"))}</div></th><th class="whitespace-nowrap px-2 py-3"><div class="text-right font-semibold">${ssrInterpolate(unref(t)("price"))}</div></th><th class="whitespace-nowrap px-2 py-3"><div class="text-center font-semibold">${ssrInterpolate(unref(t)("quantity"))}</div></th><th class="whitespace-nowrap px-2 py-3"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("ratingAvg"))}><svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24"><path class="fill-current text-red-400 dark:text-red-300" d="M12.746,1.464l3.11,6.3L22.81,8.776a.831.831,0,0,1,.461,1.418l-5.033,4.9,1.188,6.926a.832.832,0,0,1-1.207.877L12,19.632,5.78,22.9a.833.833,0,0,1-1.207-.878L5.761,15.1l-5.033-4.9a.831.831,0,0,1,.461-1.418L8.143,7.765l3.11-6.3A.833.833,0,0,1,12.746,1.464Z"></path></svg></div></th><th class="whitespace-nowrap px-2 py-3"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("reviews"))}><svg class="h-4 w-4 shrink-0 fill-current" viewBox="0 0 512 512"><path class="fill-current text-sky-700 dark:text-sky-300" d="M256 32C114.62 32 0 125.12 0 240c0 49.56 21.41 95.01 57.02 130.74C44.46 421.05 2.7 465.97 2.2 466.5A7.995 7.995 0 0 0 8 480c66.26 0 115.99-31.75 140.6-51.38C181.29 440.93 217.59 448 256 448c141.38 0 256-93.12 256-208S397.38 32 256 32z"></path></svg></div></th><th class="whitespace-nowrap px-2 py-3"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="h-4 w-4 shrink-0 fill-current" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="whitespace-nowrap px-2 py-3"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("likes"))}><svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24"><path class="fill-current text-red-400 dark:text-red-300" d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path><path class="fill-current text-red-400 dark:text-red-300" d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path></svg></div></th><th class="whitespace-nowrap px-2 py-3"><div class="text-center font-medium">${ssrInterpolate(unref(t)("show"))}</div></th><th class="whitespace-nowrap px-2 py-3"><div class="text-right font-semibold">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="w-px whitespace-nowrap px-1 py-1 text-center"><input type="checkbox"${ssrIncludeBooleanAttr(Boolean(allProductsSelected.value || __props.allSelected)) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localProducts.value,
          "onUpdate:modelValue": ($event) => localProducts.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: product }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="border-b-2 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="handle w-px cursor-move px-1 py-1 text-center"${_scopeId}><svg class="h-4 w-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="w-px whitespace-nowrap px-1 py-1"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${ssrRenderAttr("title", publicationTitle(product))}${_scopeId}>${ssrInterpolate(product.id)}</div></td><td class="w-px px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(product))}${ssrRenderAttr("title", ownerTitle(product))}${ssrRenderAttr("alt", unref(t)("owner"))} class="h-7 w-7 rounded-full border border-slate-300 object-cover dark:border-slate-600"${_scopeId}></div></td><td class="w-16 px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", imageUrl(product))}${ssrRenderAttr("alt", imageAlt(product))}${ssrRenderAttr("title", imageTitle(product))} class="h-10 w-14 rounded-sm border border-slate-300 object-cover dark:border-slate-600"${_scopeId}></div>`);
              if (product.images_count) {
                _push2(`<div class="mt-0.5 text-center text-[9px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(product.images_count)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</td><td class="px-2 py-1"${_scopeId}><div class="text-left"${_scopeId}><a${ssrRenderAttr("href", productPublicUrl(product))} target="_blank" rel="noopener noreferrer" class="text-xs text-sky-700 hover:text-amber-700 hover:underline dark:text-sky-300 dark:hover:text-amber-300"${ssrRenderAttr("title", productShort(product) || productSubtitle(product) || productTitle(product))}${_scopeId}>${ssrInterpolate(truncateText(productTitle(product), 80))}</a>`);
              if (productSubtitle(product)) {
                _push2(`<div class="text-[10px] text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(productSubtitle(product), 75))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="mt-0.5 text-[10px] italic text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(truncateText(product.url, 70))}</div>`);
              if (mainCategoryTitle(product)) {
                _push2(`<div class="mt-1 flex items-center gap-1 text-[10px] text-indigo-700 dark:text-indigo-300"${ssrRenderAttr("title", categoriesTitle(product))}${_scopeId}><svg class="h-3 w-3 shrink-0 fill-current" viewBox="0 0 512 512"${_scopeId}><path d="M144 132h352c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h352c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h352c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z"${_scopeId}></path></svg> ${ssrInterpolate(truncateText(mainCategoryTitle(product), 55))} `);
                if (product.categories_count) {
                  _push2(`<span class="text-slate-400"${_scopeId}> (${ssrInterpolate(product.categories_count)}) </span>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (product.attribute_values_count) {
                _push2(`<div class="mt-0.5 flex items-center gap-1 text-[9px] text-fuchsia-700 dark:text-fuchsia-300"${ssrRenderAttr("title", attributesTitle(product))}${_scopeId}>${ssrInterpolate(unref(t)("attributes"))}: ${ssrInterpolate(product.attribute_values_count)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<a${ssrRenderAttr("href", _ctx.route("admin.marketProductVariants.index", {
                market_product_id: product.id
              }))} class="mt-0.5 flex items-center gap-1 text-[10px] text-fuchsia-700 hover:text-fuchsia-900 hover:underline dark:text-fuchsia-300 dark:hover:text-fuchsia-100"${ssrRenderAttr("title", unref(t)("marketProductVariants"))}${_scopeId}>${ssrInterpolate(unref(t)("marketProductVariants"))}: ${ssrInterpolate(product.variants_count ?? 0)} `);
              if (product.available_variants_count) {
                _push2(`<span class="text-emerald-600 dark:text-emerald-300"${_scopeId}> (${ssrInterpolate(product.available_variants_count)}) </span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</a>`);
              if (shopTitle(product)) {
                _push2(`<div class="mt-1 flex items-center gap-1 text-[10px] text-amber-700 dark:text-amber-300"${ssrRenderAttr("title", supplierTitle(product))}${_scopeId}><span class="truncate"${_scopeId}>${ssrInterpolate(truncateText(shopTitle(product), 42))}</span></div>`);
              } else {
                _push2(`<div class="mt-1 text-[10px] text-slate-400"${ssrRenderAttr("title", supplierTitle(product))}${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
              }
              if (product.barcode) {
                _push2(`<div class="mt-0.5 flex items-center gap-1 text-[9px]"${_scopeId}><span class="rounded-sm border border-slate-400 bg-slate-200 px-1 py-0.5 text-slate-700 dark:bg-slate-800 dark:text-slate-300"${ssrRenderAttr("title", unref(t)("barcode"))}${_scopeId}>${ssrInterpolate(truncateText(product.barcode, 24))}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="mt-0.5 flex flex-wrap gap-1 text-[9px]"${_scopeId}>`);
              if (product.vendor_code) {
                _push2(`<span class="rounded-sm border border-slate-400 bg-violet-100 px-1 py-0.5 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300"${ssrRenderAttr("title", unref(t)("vendorCode"))}${_scopeId}>${ssrInterpolate(truncateText(product.vendor_code, 24))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (product.sku) {
                _push2(`<span class="rounded-sm border border-slate-400 bg-blue-100 px-1 py-0.5 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"${ssrRenderAttr("title", unref(t)("sku"))}${_scopeId}> SKU: ${ssrInterpolate(truncateText(product.sku, 24))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div></td><td class="whitespace-nowrap px-2 py-1"${_scopeId}><div class="text-right"${_scopeId}><div class="text-sm font-bold text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(formatMoney(product.price, product.currency))}</div>`);
              if (product.old_price) {
                _push2(`<div class="text-sm text-slate-400 line-through"${_scopeId}>${ssrInterpolate(formatMoney(product.old_price, product.currency))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (product.wholesale_price) {
                _push2(`<div class="mt-0.5 text-[10px] text-blue-700 dark:text-blue-300"${ssrRenderAttr("title", unref(t)("wholesalePrice"))}${_scopeId}>${ssrInterpolate(formatMoney(product.wholesale_price, product.currency))} `);
                if (product.wholesale_min_quantity) {
                  _push2(`<span${_scopeId}> × ${ssrInterpolate(product.wholesale_min_quantity)}</span>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></td><td class="whitespace-nowrap px-2 py-1"${_scopeId}><div class="text-center"${_scopeId}><div class="${ssrRenderClass([product.in_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300", "text-sm font-bold"])}"${_scopeId}>${ssrInterpolate(product.quantity)}</div><span class="${ssrRenderClass([product.in_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300", "text-[9px]"])}"${_scopeId}>${ssrInterpolate(product.in_stock ? unref(t)("inStock") : unref(t)("outOfStock"))}</span></div></td><td class="whitespace-nowrap px-2 py-3"${_scopeId}><div class="text-center text-xs text-rose-500 dark:text-rose-300"${ssrRenderAttr("title", unref(t)("rating"))}${_scopeId}>${ssrInterpolate(formatRating(product))}</div></td><td class="whitespace-nowrap px-2 py-1"${_scopeId}><div class="text-center text-xs text-sky-700 dark:text-sky-300"${ssrRenderAttr("title", unref(t)("reviews"))}${_scopeId}>${ssrInterpolate(product.reviews_count ?? product.rating_count ?? 0)}</div></td><td class="whitespace-nowrap px-2 py-1"${_scopeId}><div class="text-center text-xs text-blue-700 dark:text-blue-300"${ssrRenderAttr("title", unref(t)("views"))}${_scopeId}>${ssrInterpolate(product.views ?? 0)}</div></td><td class="whitespace-nowrap px-2 py-1"${_scopeId}><div class="text-center text-xs"${ssrRenderAttr("title", unref(t)("likes"))}${_scopeId}>${ssrInterpolate(product.likes_count ?? 0)}</div></td><td class="whitespace-nowrap px-2 py-1"${_scopeId}><div class="flex flex-col items-center justify-center gap-1"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: product.left,
                title: product.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => emit("toggle-left", product)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: product.main,
                title: product.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => emit("toggle-main", product)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                isActive: product.right,
                title: product.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => emit("toggle-right", product)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex items-center justify-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: product.is_new,
                title: product.is_new ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleIsNew: ($event) => emit("toggle-is-new", product)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                isActive: product.is_hit,
                title: product.is_hit ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleIsHit: ($event) => emit("toggle-is-hit", product)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                isActive: product.is_sale,
                title: product.is_sale ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleIsSale: ($event) => emit("toggle-is-sale", product)
              }, null, _parent2, _scopeId));
              _push2(`</div><span class="px-2 py-1 text-[10px] font-semibold text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(statusBadge(product.status).text)}</span><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(product.moderation_status).class, "rounded-sm border px-2 py-1 text-[9px] font-semibold"])}"${ssrRenderAttr("title", product.moderation_note ? `${product.moderation_note}${product.moderated_at ? ` [${formatDate(product.moderated_at)}]` : ""}` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(product.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$c, {
                isAdmin: __props.isAdmin,
                status: product.moderation_status ?? 0,
                initialNote: product.moderation_note || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emit(
                  "approve",
                  product,
                  status,
                  note
                )
              }, null, _parent2, _scopeId));
              _push2(`</div></div></td><td class="whitespace-nowrap px-2 py-1"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$d, {
                isActive: product.activity,
                title: product.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", product)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$e, {
                href: _ctx.route("admin.marketProducts.edit", {
                  marketProduct: product.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$f, {
                onDelete: ($event) => emit("delete", product)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="w-px whitespace-nowrap px-1 py-1"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedProducts.includes(product.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                      class: "text-center text-blue-600 dark:text-blue-200",
                      title: publicationTitle(product)
                    }, toDisplayString(product.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "w-px px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(product),
                        title: ownerTitle(product),
                        alt: unref(t)("owner"),
                        class: "h-7 w-7 rounded-full border border-slate-300 object-cover dark:border-slate-600"
                      }, null, 8, ["src", "title", "alt"])
                    ])
                  ]),
                  createVNode("td", { class: "w-16 px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: imageUrl(product),
                        alt: imageAlt(product),
                        title: imageTitle(product),
                        class: "h-10 w-14 rounded-sm border border-slate-300 object-cover dark:border-slate-600"
                      }, null, 8, ["src", "alt", "title"])
                    ]),
                    product.images_count ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "mt-0.5 text-center text-[9px] text-slate-500 dark:text-slate-300"
                    }, toDisplayString(product.images_count), 1)) : createCommentVNode("", true)
                  ]),
                  createVNode("td", { class: "px-2 py-1" }, [
                    createVNode("div", { class: "text-left" }, [
                      createVNode("a", {
                        href: productPublicUrl(product),
                        target: "_blank",
                        rel: "noopener noreferrer",
                        class: "text-xs text-sky-700 hover:text-amber-700 hover:underline dark:text-sky-300 dark:hover:text-amber-300",
                        title: productShort(product) || productSubtitle(product) || productTitle(product)
                      }, toDisplayString(truncateText(productTitle(product), 80)), 9, ["href", "title"]),
                      productSubtitle(product) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] text-slate-700 dark:text-slate-300"
                      }, toDisplayString(truncateText(productSubtitle(product), 75)), 1)) : createCommentVNode("", true),
                      createVNode("div", { class: "mt-0.5 text-[10px] italic text-slate-500 dark:text-slate-400" }, toDisplayString(truncateText(product.url, 70)), 1),
                      mainCategoryTitle(product) ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "mt-1 flex items-center gap-1 text-[10px] text-indigo-700 dark:text-indigo-300",
                        title: categoriesTitle(product)
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-3 w-3 shrink-0 fill-current",
                          viewBox: "0 0 512 512"
                        }, [
                          createVNode("path", { d: "M144 132h352c8.837 0 16-7.163 16-16V76c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h352c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16zm0 160h352c8.837 0 16-7.163 16-16v-40c0-8.837-7.163-16-16-16H144c-8.837 0-16 7.163-16 16v40c0 8.837 7.163 16 16 16z" })
                        ])),
                        createTextVNode(" " + toDisplayString(truncateText(mainCategoryTitle(product), 55)) + " ", 1),
                        product.categories_count ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "text-slate-400"
                        }, " (" + toDisplayString(product.categories_count) + ") ", 1)) : createCommentVNode("", true)
                      ], 8, ["title"])) : createCommentVNode("", true),
                      product.attribute_values_count ? (openBlock(), createBlock("div", {
                        key: 2,
                        class: "mt-0.5 flex items-center gap-1 text-[9px] text-fuchsia-700 dark:text-fuchsia-300",
                        title: attributesTitle(product)
                      }, toDisplayString(unref(t)("attributes")) + ": " + toDisplayString(product.attribute_values_count), 9, ["title"])) : createCommentVNode("", true),
                      createVNode("a", {
                        href: _ctx.route("admin.marketProductVariants.index", {
                          market_product_id: product.id
                        }),
                        class: "mt-0.5 flex items-center gap-1 text-[10px] text-fuchsia-700 hover:text-fuchsia-900 hover:underline dark:text-fuchsia-300 dark:hover:text-fuchsia-100",
                        title: unref(t)("marketProductVariants")
                      }, [
                        createTextVNode(toDisplayString(unref(t)("marketProductVariants")) + ": " + toDisplayString(product.variants_count ?? 0) + " ", 1),
                        product.available_variants_count ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "text-emerald-600 dark:text-emerald-300"
                        }, " (" + toDisplayString(product.available_variants_count) + ") ", 1)) : createCommentVNode("", true)
                      ], 8, ["href", "title"]),
                      shopTitle(product) ? (openBlock(), createBlock("div", {
                        key: 3,
                        class: "mt-1 flex items-center gap-1 text-[10px] text-amber-700 dark:text-amber-300",
                        title: supplierTitle(product)
                      }, [
                        createVNode("span", { class: "truncate" }, toDisplayString(truncateText(shopTitle(product), 42)), 1)
                      ], 8, ["title"])) : (openBlock(), createBlock("div", {
                        key: 4,
                        class: "mt-1 text-[10px] text-slate-400",
                        title: supplierTitle(product)
                      }, toDisplayString(unref(t)("noData")), 9, ["title"])),
                      product.barcode ? (openBlock(), createBlock("div", {
                        key: 5,
                        class: "mt-0.5 flex items-center gap-1 text-[9px]"
                      }, [
                        createVNode("span", {
                          class: "rounded-sm border border-slate-400 bg-slate-200 px-1 py-0.5 text-slate-700 dark:bg-slate-800 dark:text-slate-300",
                          title: unref(t)("barcode")
                        }, toDisplayString(truncateText(product.barcode, 24)), 9, ["title"])
                      ])) : createCommentVNode("", true),
                      createVNode("div", { class: "mt-0.5 flex flex-wrap gap-1 text-[9px]" }, [
                        product.vendor_code ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "rounded-sm border border-slate-400 bg-violet-100 px-1 py-0.5 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300",
                          title: unref(t)("vendorCode")
                        }, toDisplayString(truncateText(product.vendor_code, 24)), 9, ["title"])) : createCommentVNode("", true),
                        product.sku ? (openBlock(), createBlock("span", {
                          key: 1,
                          class: "rounded-sm border border-slate-400 bg-blue-100 px-1 py-0.5 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300",
                          title: unref(t)("sku")
                        }, " SKU: " + toDisplayString(truncateText(product.sku, 24)), 9, ["title"])) : createCommentVNode("", true)
                      ])
                    ])
                  ]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-1" }, [
                    createVNode("div", { class: "text-right" }, [
                      createVNode("div", { class: "text-sm font-bold text-teal-700 dark:text-teal-300" }, toDisplayString(formatMoney(product.price, product.currency)), 1),
                      product.old_price ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-sm text-slate-400 line-through"
                      }, toDisplayString(formatMoney(product.old_price, product.currency)), 1)) : createCommentVNode("", true),
                      product.wholesale_price ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "mt-0.5 text-[10px] text-blue-700 dark:text-blue-300",
                        title: unref(t)("wholesalePrice")
                      }, [
                        createTextVNode(toDisplayString(formatMoney(product.wholesale_price, product.currency)) + " ", 1),
                        product.wholesale_min_quantity ? (openBlock(), createBlock("span", { key: 0 }, " × " + toDisplayString(product.wholesale_min_quantity), 1)) : createCommentVNode("", true)
                      ], 8, ["title"])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-1" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("div", {
                        class: ["text-sm font-bold", product.in_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300"]
                      }, toDisplayString(product.quantity), 3),
                      createVNode("span", {
                        class: ["text-[9px]", product.in_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300"]
                      }, toDisplayString(product.in_stock ? unref(t)("inStock") : unref(t)("outOfStock")), 3)
                    ])
                  ]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-3" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-rose-500 dark:text-rose-300",
                      title: unref(t)("rating")
                    }, toDisplayString(formatRating(product)), 9, ["title"])
                  ]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-1" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-sky-700 dark:text-sky-300",
                      title: unref(t)("reviews")
                    }, toDisplayString(product.reviews_count ?? product.rating_count ?? 0), 9, ["title"])
                  ]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-1" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-blue-700 dark:text-blue-300",
                      title: unref(t)("views")
                    }, toDisplayString(product.views ?? 0), 9, ["title"])
                  ]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-1" }, [
                    createVNode("div", {
                      class: "text-center text-xs",
                      title: unref(t)("likes")
                    }, toDisplayString(product.likes_count ?? 0), 9, ["title"])
                  ]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-1" }, [
                    createVNode("div", { class: "flex flex-col items-center justify-center gap-1" }, [
                      createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                        createVNode(_sfc_main$6, {
                          isActive: product.left,
                          title: product.left ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleLeft: ($event) => emit("toggle-left", product)
                        }, null, 8, ["isActive", "title", "onToggleLeft"]),
                        createVNode(_sfc_main$7, {
                          isActive: product.main,
                          title: product.main ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleMain: ($event) => emit("toggle-main", product)
                        }, null, 8, ["isActive", "title", "onToggleMain"]),
                        createVNode(_sfc_main$8, {
                          isActive: product.right,
                          title: product.right ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleRight: ($event) => emit("toggle-right", product)
                        }, null, 8, ["isActive", "title", "onToggleRight"])
                      ]),
                      createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                        createVNode(_sfc_main$9, {
                          isActive: product.is_new,
                          title: product.is_new ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleIsNew: ($event) => emit("toggle-is-new", product)
                        }, null, 8, ["isActive", "title", "onToggleIsNew"]),
                        createVNode(_sfc_main$a, {
                          isActive: product.is_hit,
                          title: product.is_hit ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleIsHit: ($event) => emit("toggle-is-hit", product)
                        }, null, 8, ["isActive", "title", "onToggleIsHit"]),
                        createVNode(_sfc_main$b, {
                          isActive: product.is_sale,
                          title: product.is_sale ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleIsSale: ($event) => emit("toggle-is-sale", product)
                        }, null, 8, ["isActive", "title", "onToggleIsSale"])
                      ]),
                      createVNode("span", { class: "px-2 py-1 text-[10px] font-semibold text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(statusBadge(product.status).text), 1),
                      createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                        createVNode("span", {
                          class: ["rounded-sm border px-2 py-1 text-[9px] font-semibold", moderationBadge(product.moderation_status).class],
                          title: product.moderation_note ? `${product.moderation_note}${product.moderated_at ? ` [${formatDate(product.moderated_at)}]` : ""}` : null
                        }, toDisplayString(moderationBadge(product.moderation_status).text), 11, ["title"]),
                        createVNode(_sfc_main$c, {
                          isAdmin: __props.isAdmin,
                          status: product.moderation_status ?? 0,
                          initialNote: product.moderation_note || "",
                          mode: "toggle",
                          onSubmit: ({ status, note }) => emit(
                            "approve",
                            product,
                            status,
                            note
                          )
                        }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                      ])
                    ])
                  ]),
                  createVNode("td", { class: "whitespace-nowrap px-2 py-1" }, [
                    createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                      createVNode(_sfc_main$d, {
                        isActive: product.activity,
                        title: product.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", product)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$e, {
                        href: _ctx.route("admin.marketProducts.edit", {
                          marketProduct: product.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$f, {
                        onDelete: ($event) => emit("delete", product)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "w-px whitespace-nowrap px-1 py-1" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedProducts.includes(product.id),
                        onChange: ($event) => emit("toggle-select", product.id)
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
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProduct/Table/ProductTable.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "AddButton",
  __ssrInlineRender: true,
  props: {
    href: {
      type: String,
      required: true
    }
  },
  setup(__props) {
    ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(Link), mergeProps({
        href: __props.href,
        class: "flex items-center btn px-2 py-0.5 text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-800 focus:text-blue-800 dark:hover:text-blue-600 dark:focus:text-blue-600 transition-colors duration-300 ease-in-out"
      }, _attrs), {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<span${_scopeId}><svg class="w-4 h-4 fill-current opacity-80 shrink-0" viewBox="0 0 16 16"${_scopeId}><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"${_scopeId}></path></svg></span>`);
          } else {
            return [
              createVNode("span", null, [
                (openBlock(), createBlock("svg", {
                  class: "w-4 h-4 fill-current opacity-80 shrink-0",
                  viewBox: "0 0 16 16"
                }, [
                  createVNode("path", { d: "M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" })
                ]))
              ])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/AddButton.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "ProductCardGrid",
  __ssrInlineRender: true,
  props: {
    products: {
      type: Array,
      default: () => []
    },
    selectedProducts: {
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
    const localProducts = ref([]);
    const openedOwnerBlocks = ref([]);
    watch(
      () => props.products,
      (newValue) => {
        localProducts.value = JSON.parse(JSON.stringify(newValue || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localProducts.value.map((product) => product.id)
      );
    };
    const allSelected = computed(() => {
      if (!localProducts.value.length) {
        return false;
      }
      return localProducts.value.every((product) => {
        return props.selectedProducts.includes(product.id);
      });
    });
    const productTranslation = (product) => {
      return (product == null ? void 0 : product.translation) || {};
    };
    const productTitle = (product) => {
      var _a;
      return ((_a = productTranslation(product)) == null ? void 0 : _a.title) || `ID: ${product == null ? void 0 : product.id}`;
    };
    const productSubtitle = (product) => {
      var _a;
      return ((_a = productTranslation(product)) == null ? void 0 : _a.subtitle) || "";
    };
    const productShort = (product) => {
      var _a;
      return ((_a = productTranslation(product)) == null ? void 0 : _a.short) || "";
    };
    const relationTitle = (relation) => {
      var _a;
      return ((_a = relation == null ? void 0 : relation.translation) == null ? void 0 : _a.title) || "";
    };
    const companyTitle = (product) => {
      var _a;
      return relationTitle(product == null ? void 0 : product.company) || ((_a = product == null ? void 0 : product.company) == null ? void 0 : _a.legal_name) || "";
    };
    const shopTitle = (product) => {
      return relationTitle(product == null ? void 0 : product.shop);
    };
    const brandTitle = (product) => {
      return relationTitle(product == null ? void 0 : product.brand);
    };
    const categoryTitle = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.translation) == null ? void 0 : _a.title) || `ID: ${category == null ? void 0 : category.id}`;
    };
    const ownerName = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (product) => {
      const owner = product == null ? void 0 : product.owner;
      if (!owner) {
        return t("noData");
      }
      return `${owner.name || ""}${owner.email ? ` — ${owner.email}` : ""}`.trim();
    };
    const ownerAvatar = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const isOwnerBlockOpen = (productId) => {
      return openedOwnerBlocks.value.includes(productId);
    };
    const toggleOwnerBlock = (productId) => {
      if (isOwnerBlockOpen(productId)) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
          (id) => id !== productId
        );
        return;
      }
      openedOwnerBlocks.value.push(productId);
    };
    const getPrimaryImage = (product) => {
      if (!Array.isArray(product == null ? void 0 : product.images) || !product.images.length) {
        return null;
      }
      return [...product.images].sort((left, right) => {
        return Number((left == null ? void 0 : left.order) ?? 0) - Number((right == null ? void 0 : right.order) ?? 0);
      })[0];
    };
    const imageUrl = (product) => {
      const image = getPrimaryImage(product);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/market/market_product_images/default-image.png";
    };
    const imageAlt = (product) => {
      const image = getPrimaryImage(product);
      return (image == null ? void 0 : image.alt) || productTitle(product);
    };
    const imageTitle = (product) => {
      const image = getPrimaryImage(product);
      return (image == null ? void 0 : image.caption) || productTitle(product);
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
    const truncateText = (text, maxLength = 80) => {
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
      const precision = Number.isFinite(Number(currency == null ? void 0 : currency.precision)) ? Number(currency.precision) : 2;
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
    const formatRating = (product) => {
      return safeNumber(product == null ? void 0 : product.rating_avg).toFixed(1);
    };
    const mainCategory = (product) => {
      const categories = Array.isArray(product == null ? void 0 : product.categories) ? product.categories : [];
      return categories.find((category) => {
        var _a;
        return Boolean((_a = category == null ? void 0 : category.pivot) == null ? void 0 : _a.main);
      }) || categories[0] || null;
    };
    const mainCategoryTitle = (product) => {
      const category = mainCategory(product);
      return category ? categoryTitle(category) : "";
    };
    const categoriesTitle = (product) => {
      const categories = Array.isArray(product == null ? void 0 : product.categories) ? product.categories : [];
      return categories.map((category) => categoryTitle(category)).filter(Boolean).join(", ");
    };
    const attributeTitle = (item) => {
      var _a, _b;
      return ((_b = (_a = item == null ? void 0 : item.attribute) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || `ID: ${item == null ? void 0 : item.market_attribute_id}`;
    };
    const attributeReferenceValueTitle = (item) => {
      var _a, _b;
      return ((_b = (_a = item == null ? void 0 : item.attribute_value) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const attributeValueText = (item) => {
      const referenceTitle = attributeReferenceValueTitle(item);
      if (referenceTitle) {
        return referenceTitle;
      }
      if ((item == null ? void 0 : item.value_string) !== null && (item == null ? void 0 : item.value_string) !== void 0 && item.value_string !== "") {
        return String(item.value_string);
      }
      if ((item == null ? void 0 : item.value_number) !== null && (item == null ? void 0 : item.value_number) !== void 0 && item.value_number !== "") {
        return String(item.value_number);
      }
      if ((item == null ? void 0 : item.value_boolean) === true) {
        return t("yes");
      }
      if ((item == null ? void 0 : item.value_boolean) === false) {
        return t("no");
      }
      if (item == null ? void 0 : item.value_date) {
        return String(item.value_date);
      }
      if (Array.isArray(item == null ? void 0 : item.value_json) || (item == null ? void 0 : item.value_json) && typeof item.value_json === "object") {
        return JSON.stringify(item.value_json);
      }
      return "";
    };
    const attributesTooltip = (product) => {
      const attributes = Array.isArray(product == null ? void 0 : product.attribute_values) ? product.attribute_values : [];
      return attributes.map((item) => {
        const title = attributeTitle(item);
        const value = attributeValueText(item);
        const unit = value && (item == null ? void 0 : item.unit) ? ` ${item.unit}` : "";
        return value ? `${title}: ${value}${unit}` : title;
      }).filter(Boolean).join("\n");
    };
    const supplierTitle = (product) => {
      const values = [];
      if (companyTitle(product)) {
        values.push(`Компания: ${companyTitle(product)}`);
      }
      if (brandTitle(product)) {
        values.push(`Бренд: ${brandTitle(product)}`);
      }
      return values.join("\n");
    };
    const productPublicUrl = (product) => {
      return `/market/products/${encodeURIComponent((product == null ? void 0 : product.url) || "")}`;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-400 bg-white shadow-lg dark:border-slate-500 dark:bg-slate-700" }, _attrs))}><div class="flex items-center justify-between border-b border-slate-400 px-3 py-2 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedProducts.length)}</div>`);
      if (localProducts.value.length) {
        _push(`<label class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(Boolean(allSelected.value)) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localProducts.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localProducts.value,
          "onUpdate:modelValue": ($event) => localProducts.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".handle",
          class: "grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: product }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex h-full flex-col rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80"${_scopeId}><header class="flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100"${ssrRenderAttr("title", `[${product.sort}] / ${formatDate(product.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(product.id)}</div><button type="button" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"${ssrRenderAttr("title", isOwnerBlockOpen(product.id) ? unref(t)("hideOwner") : unref(t)("showOwner"))}${_scopeId}><svg class="${ssrRenderClass([{ "rotate-180": isOwnerBlockOpen(product.id) }, "h-4 w-4 transition-transform duration-200"])}" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"${_scopeId}></path></svg></button></div><div class="flex items-center space-x-2"${_scopeId}><span class="${ssrRenderClass([moderationBadge(product.moderation_status).class, "rounded-sm border px-2 py-0.5 text-[10px] font-semibold"])}"${ssrRenderAttr("title", product.moderation_note && product.moderated_at ? `${product.moderation_note} [${formatDate(product.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(product.moderation_status).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedProducts.includes(product.id)) ? " checked" : ""}${_scopeId}></div></header><div class="flex flex-1 flex-col"${_scopeId}><div style="${ssrRenderStyle(isOwnerBlockOpen(product.id) ? null : { display: "none" })}" class="flex flex-col items-center justify-center text-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(product))}${ssrRenderAttr("title", ownerTitle(product))}${ssrRenderAttr("alt", unref(t)("owner"))} class="h-12 w-12 rounded-full border border-slate-300 object-cover dark:border-slate-600"${_scopeId}><div class="mt-1 line-clamp-1 text-[11px] font-semibold leading-tight text-slate-700 dark:text-slate-100"${ssrRenderAttr("title", ownerName(product))}${_scopeId}>${ssrInterpolate(ownerName(product))}</div>`);
              if (ownerEmail(product)) {
                _push2(`<div class="line-clamp-1 text-[10px] leading-tight text-slate-500 dark:text-slate-300"${ssrRenderAttr("title", ownerEmail(product))}${_scopeId}>${ssrInterpolate(ownerEmail(product))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (product.show_from_at) {
                _push2(`<div class="mt-1 flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("show"))}: ${ssrInterpolate(formatDate(product.show_from_at))} / ${ssrInterpolate(formatDate(product.show_to_at))}</div>`);
              } else {
                _push2(`<div class="mt-1 text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(formatDate(product.published_at))}</div>`);
              }
              _push2(`</div><div class="relative w-full overflow-hidden bg-slate-200 dark:bg-slate-900"${_scopeId}><img${ssrRenderAttr("src", imageUrl(product))}${ssrRenderAttr("alt", imageAlt(product))}${ssrRenderAttr("title", imageTitle(product))} class="h-auto w-full object-cover"${_scopeId}><div class="absolute left-2 top-2 flex flex-wrap gap-1"${_scopeId}>`);
              if (product.is_new) {
                _push2(`<span${ssrRenderAttr("title", unref(t)("sortIsNew"))} class="rounded-sm border border-teal-700 bg-teal-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-teal-700 dark:border-teal-300 dark:bg-teal-800/90 dark:text-teal-300"${_scopeId}> NEW </span>`);
              } else {
                _push2(`<!---->`);
              }
              if (product.is_hit) {
                _push2(`<span${ssrRenderAttr("title", unref(t)("sortIsHit"))} class="rounded-sm border border-yellow-700 bg-yellow-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-yellow-700 dark:border-yellow-300 dark:bg-yellow-800/90 dark:text-yellow-300"${_scopeId}> HIT </span>`);
              } else {
                _push2(`<!---->`);
              }
              if (product.is_sale) {
                _push2(`<span${ssrRenderAttr("title", unref(t)("sortIsSale"))} class="rounded-sm border border-pink-700 bg-pink-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-pink-700 dark:border-pink-300 dark:bg-pink-800/90 dark:text-pink-300"${_scopeId}> SALE </span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (product.images_count) {
                _push2(`<div class="absolute bottom-2 right-2 rounded-sm bg-slate-900/70 px-1.5 py-0.5 text-[9px] font-semibold text-white"${ssrRenderAttr("title", unref(t)("images"))}${_scopeId}>${ssrInterpolate(product.images_count)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="space-y-1"${_scopeId}>`);
              if (product.barcode) {
                _push2(`<div class="mt-0.5 flex items-center justify-center gap-1 text-[9px]"${_scopeId}><span class="rounded-sm border border-slate-400 bg-slate-200 px-1 py-0.5 text-slate-700 dark:bg-slate-800 dark:text-slate-300"${ssrRenderAttr("title", unref(t)("barcode"))}${_scopeId}>${ssrInterpolate(truncateText(product.barcode, 24))}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="mt-0.5 flex flex-wrap items-center justify-center gap-1 text-[9px]"${_scopeId}>`);
              if (product.vendor_code) {
                _push2(`<span class="rounded-sm border border-slate-400 bg-violet-100 px-1 py-0.5 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300"${ssrRenderAttr("title", unref(t)("vendorCode"))}${_scopeId}>${ssrInterpolate(truncateText(product.vendor_code, 24))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (product.sku) {
                _push2(`<span class="rounded-sm border border-slate-400 bg-blue-100 px-1 py-0.5 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300"${ssrRenderAttr("title", unref(t)("sku"))}${_scopeId}> SKU: ${ssrInterpolate(truncateText(product.sku, 24))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><a${ssrRenderAttr("href", productPublicUrl(product))} target="_blank" rel="noopener noreferrer" class="flex items-center justify-center px-3 text-xs font-semibold text-sky-700 hover:text-amber-700 hover:underline dark:text-sky-300 dark:hover:text-amber-300"${ssrRenderAttr("title", productShort(product) || productSubtitle(product) || productTitle(product))}${_scopeId}>${ssrInterpolate(truncateText(productTitle(product), 80))}</a>`);
              if (productSubtitle(product)) {
                _push2(`<div class="flex items-center justify-center text-[10px] font-semibold text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(productSubtitle(product), 75))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="line-clamp-1 text-center text-[10px] italic text-slate-500 dark:text-slate-400"${ssrRenderAttr("title", product.url)}${_scopeId}>${ssrInterpolate(truncateText(product.url, 90))}</div><div class="flex items-center justify-center space-x-2 px-2 font-semibold"${_scopeId}>`);
              if (product.old_price) {
                _push2(`<div class="text-sm text-slate-400 line-through"${_scopeId}>${ssrInterpolate(formatMoney(product.old_price, product.currency))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="text-sm font-bold text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(formatMoney(product.price, product.currency))}</div></div>`);
              if (product.wholesale_price) {
                _push2(`<div class="mt-0.5 text-center text-[10px] font-semibold text-blue-700 dark:text-blue-300"${ssrRenderAttr("title", unref(t)("wholesalePrice"))}${_scopeId}>${ssrInterpolate(formatMoney(product.wholesale_price, product.currency))} `);
                if (product.wholesale_min_quantity) {
                  _push2(`<span${_scopeId}> × ${ssrInterpolate(product.wholesale_min_quantity)}</span>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="flex items-center justify-center space-x-1 text-[11px] font-semibold"${_scopeId}><span class="${ssrRenderClass([product.in_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300", "text-[10px]"])}"${_scopeId}>${ssrInterpolate(product.in_stock ? unref(t)("inStock") : unref(t)("outOfStock"))}</span><div class="${ssrRenderClass([product.in_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300", "text-sm font-bold"])}"${_scopeId}>${ssrInterpolate(product.quantity)}</div></div>`);
              if (shopTitle(product)) {
                _push2(`<div class="mt-1 flex items-center justify-center gap-1 text-[10px] text-amber-700 dark:text-amber-300"${ssrRenderAttr("title", supplierTitle(product))}${_scopeId}><span class="truncate"${_scopeId}>${ssrInterpolate(truncateText(shopTitle(product), 42))}</span></div>`);
              } else {
                _push2(`<div class="mt-1 flex items-center justify-center text-[10px] text-slate-400"${ssrRenderAttr("title", supplierTitle(product))}${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
              }
              if (productShort(product)) {
                _push2(`<div class="line-clamp-3 text-center text-[12px] font-semibold text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(truncateText(productShort(product), 160))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="grid grid-cols-2 gap-1 px-1 text-center text-[10px] font-semibold"${_scopeId}><div class="rounded-sm border border-gray-400 bg-indigo-100 px-1 py-1 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300"${ssrRenderAttr("title", unref(t)("tags"))}${_scopeId}><div${_scopeId}>${ssrInterpolate(unref(t)("tags"))}</div><div${_scopeId}>${ssrInterpolate(product.tags_count ?? 0)}</div></div><div class="rounded-sm border border-gray-400 bg-cyan-100 px-1 py-1 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300"${ssrRenderAttr("title", attributesTooltip(product))}${_scopeId}><div${_scopeId}>${ssrInterpolate(unref(t)("attributes"))}</div><div${_scopeId}>${ssrInterpolate(product.attribute_values_count ?? 0)}</div></div></div>`);
              if (mainCategoryTitle(product)) {
                _push2(`<div class="mt-1 flex items-center justify-center gap-1 text-[10px] text-indigo-700 dark:text-indigo-300"${ssrRenderAttr("title", categoriesTitle(product))}${_scopeId}>${ssrInterpolate(truncateText(mainCategoryTitle(product), 55))} `);
                if (product.categories_count) {
                  _push2(`<span class="text-slate-400"${_scopeId}> (${ssrInterpolate(product.categories_count)}) </span>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="grid grid-cols-5 gap-1 text-center text-[10px] font-semibold"${_scopeId}><div class="px-1 py-1"${ssrRenderAttr("title", unref(t)("rating"))}${_scopeId}><div class="text-red-500 dark:text-red-400"${_scopeId}>★</div><span class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(formatRating(product))}</span></div><div class="px-1 py-1"${ssrRenderAttr("title", unref(t)("reviews"))}${_scopeId}><div class="text-sky-500 dark:text-sky-300"${_scopeId}>●</div><span class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(product.reviews_count ?? 0)}</span></div><div class="px-1 py-1"${ssrRenderAttr("title", unref(t)("likes"))}${_scopeId}><div class="text-rose-400 dark:text-rose-300"${_scopeId}>♥</div><span class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(product.likes_count ?? 0)}</span></div><div class="px-1 py-1"${ssrRenderAttr("title", unref(t)("views"))}${_scopeId}><div class="text-blue-600 dark:text-blue-300"${_scopeId}>◉</div><span class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(product.views ?? 0)}</span></div><a${ssrRenderAttr("href", _ctx.route("admin.marketProductVariants.index", {
                market_product_id: product.id
              }))} class="flex flex-col items-center justify-center px-1 py-1 text-fuchsia-700 transition dark:text-fuchsia-300"${ssrRenderAttr("title", unref(t)("marketProductVariants"))}${_scopeId}><span${_scopeId}>▦</span><span class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(product.variants_count ?? 0)} `);
              if (product.available_variants_count) {
                _push2(`<span class="text-emerald-600 dark:text-emerald-300"${_scopeId}> / ${ssrInterpolate(product.available_variants_count)}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</span></a></div><div class="text-center text-[11px] font-semibold text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("status"))}: </span> ${ssrInterpolate(getStatusLabel(product.status))}</div><div class="flex justify-center space-x-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(product.moderation_status).class, "rounded-sm border px-2 py-1 text-[10px] font-semibold"])}"${ssrRenderAttr("title", product.moderation_note && product.moderated_at ? `${product.moderation_note} [${formatDate(product.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(product.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$c, {
                isAdmin: __props.isAdmin,
                status: product.moderation_status ?? 0,
                initialNote: product.moderation_note || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emit(
                  "approve",
                  product,
                  status,
                  note
                )
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div><div class="flex items-center justify-center border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500"${_scopeId}><div class="flex flex-wrap items-center justify-center gap-1"${_scopeId}><div class="flex flex-row items-center space-x-7"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: product.left,
                title: product.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => emit("toggle-left", product)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: product.main,
                title: product.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => emit("toggle-main", product)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                isActive: product.right,
                title: product.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => emit("toggle-right", product)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex flex-row items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: product.is_new,
                title: product.is_new ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleIsNew: ($event) => emit("toggle-is-new", product)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                isActive: product.is_hit,
                title: product.is_hit ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleIsHit: ($event) => emit("toggle-is-hit", product)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                isActive: product.is_sale,
                title: product.is_sale ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleIsSale: ($event) => emit("toggle-is-sale", product)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$d, {
                isActive: product.activity,
                title: product.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", product)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$e, {
                href: _ctx.route("admin.marketProducts.edit", {
                  marketProduct: product.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$2, {
                href: _ctx.route("admin.marketProductVariants.create", {
                  market_product_id: product.id
                }),
                title: unref(t)("addMarketProductVariant"),
                class: "py-1"
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$f, {
                onDelete: ($event) => emit("delete", product)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div></div>`);
            } else {
              return [
                createVNode("div", { class: "relative flex h-full flex-col rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80" }, [
                  createVNode("header", { class: "flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500" }, [
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
                        class: "rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100",
                        title: `[${product.sort}] / ${formatDate(product.published_at)}`
                      }, " ID: " + toDisplayString(product.id), 9, ["title"]),
                      createVNode("button", {
                        type: "button",
                        class: "text-slate-400 hover:text-blue-600 dark:hover:text-blue-300",
                        title: isOwnerBlockOpen(product.id) ? unref(t)("hideOwner") : unref(t)("showOwner"),
                        onClick: withModifiers(($event) => toggleOwnerBlock(product.id), ["prevent"])
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: ["h-4 w-4 transition-transform duration-200", { "rotate-180": isOwnerBlockOpen(product.id) }],
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
                        class: ["rounded-sm border px-2 py-0.5 text-[10px] font-semibold", moderationBadge(product.moderation_status).class],
                        title: product.moderation_note && product.moderated_at ? `${product.moderation_note} [${formatDate(product.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(product.moderation_status).text), 11, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedProducts.includes(product.id),
                        onChange: ($event) => emit("toggle-select", product.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-1 flex-col" }, [
                    withDirectives(createVNode("div", { class: "flex flex-col items-center justify-center text-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(product),
                        title: ownerTitle(product),
                        alt: unref(t)("owner"),
                        class: "h-12 w-12 rounded-full border border-slate-300 object-cover dark:border-slate-600"
                      }, null, 8, ["src", "title", "alt"]),
                      createVNode("div", {
                        class: "mt-1 line-clamp-1 text-[11px] font-semibold leading-tight text-slate-700 dark:text-slate-100",
                        title: ownerName(product)
                      }, toDisplayString(ownerName(product)), 9, ["title"]),
                      ownerEmail(product) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "line-clamp-1 text-[10px] leading-tight text-slate-500 dark:text-slate-300",
                        title: ownerEmail(product)
                      }, toDisplayString(ownerEmail(product)), 9, ["title"])) : createCommentVNode("", true),
                      product.show_from_at ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "mt-1 flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(unref(t)("show")) + ": " + toDisplayString(formatDate(product.show_from_at)) + " / " + toDisplayString(formatDate(product.show_to_at)), 1)) : (openBlock(), createBlock("div", {
                        key: 2,
                        class: "mt-1 text-center text-[10px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(formatDate(product.published_at)), 1))
                    ], 512), [
                      [vShow, isOwnerBlockOpen(product.id)]
                    ]),
                    createVNode("div", { class: "relative w-full overflow-hidden bg-slate-200 dark:bg-slate-900" }, [
                      createVNode("img", {
                        src: imageUrl(product),
                        alt: imageAlt(product),
                        title: imageTitle(product),
                        class: "h-auto w-full object-cover"
                      }, null, 8, ["src", "alt", "title"]),
                      createVNode("div", { class: "absolute left-2 top-2 flex flex-wrap gap-1" }, [
                        product.is_new ? (openBlock(), createBlock("span", {
                          key: 0,
                          title: unref(t)("sortIsNew"),
                          class: "rounded-sm border border-teal-700 bg-teal-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-teal-700 dark:border-teal-300 dark:bg-teal-800/90 dark:text-teal-300"
                        }, " NEW ", 8, ["title"])) : createCommentVNode("", true),
                        product.is_hit ? (openBlock(), createBlock("span", {
                          key: 1,
                          title: unref(t)("sortIsHit"),
                          class: "rounded-sm border border-yellow-700 bg-yellow-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-yellow-700 dark:border-yellow-300 dark:bg-yellow-800/90 dark:text-yellow-300"
                        }, " HIT ", 8, ["title"])) : createCommentVNode("", true),
                        product.is_sale ? (openBlock(), createBlock("span", {
                          key: 2,
                          title: unref(t)("sortIsSale"),
                          class: "rounded-sm border border-pink-700 bg-pink-200/95 px-1.5 py-0.5 text-[9px] font-semibold text-pink-700 dark:border-pink-300 dark:bg-pink-800/90 dark:text-pink-300"
                        }, " SALE ", 8, ["title"])) : createCommentVNode("", true)
                      ]),
                      product.images_count ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "absolute bottom-2 right-2 rounded-sm bg-slate-900/70 px-1.5 py-0.5 text-[9px] font-semibold text-white",
                        title: unref(t)("images")
                      }, toDisplayString(product.images_count), 9, ["title"])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "space-y-1" }, [
                      product.barcode ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "mt-0.5 flex items-center justify-center gap-1 text-[9px]"
                      }, [
                        createVNode("span", {
                          class: "rounded-sm border border-slate-400 bg-slate-200 px-1 py-0.5 text-slate-700 dark:bg-slate-800 dark:text-slate-300",
                          title: unref(t)("barcode")
                        }, toDisplayString(truncateText(product.barcode, 24)), 9, ["title"])
                      ])) : createCommentVNode("", true),
                      createVNode("div", { class: "mt-0.5 flex flex-wrap items-center justify-center gap-1 text-[9px]" }, [
                        product.vendor_code ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "rounded-sm border border-slate-400 bg-violet-100 px-1 py-0.5 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300",
                          title: unref(t)("vendorCode")
                        }, toDisplayString(truncateText(product.vendor_code, 24)), 9, ["title"])) : createCommentVNode("", true),
                        product.sku ? (openBlock(), createBlock("span", {
                          key: 1,
                          class: "rounded-sm border border-slate-400 bg-blue-100 px-1 py-0.5 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300",
                          title: unref(t)("sku")
                        }, " SKU: " + toDisplayString(truncateText(product.sku, 24)), 9, ["title"])) : createCommentVNode("", true)
                      ]),
                      createVNode("a", {
                        href: productPublicUrl(product),
                        target: "_blank",
                        rel: "noopener noreferrer",
                        class: "flex items-center justify-center px-3 text-xs font-semibold text-sky-700 hover:text-amber-700 hover:underline dark:text-sky-300 dark:hover:text-amber-300",
                        title: productShort(product) || productSubtitle(product) || productTitle(product)
                      }, toDisplayString(truncateText(productTitle(product), 80)), 9, ["href", "title"]),
                      productSubtitle(product) ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "flex items-center justify-center text-[10px] font-semibold text-slate-700 dark:text-slate-300"
                      }, toDisplayString(truncateText(productSubtitle(product), 75)), 1)) : createCommentVNode("", true),
                      createVNode("div", {
                        class: "line-clamp-1 text-center text-[10px] italic text-slate-500 dark:text-slate-400",
                        title: product.url
                      }, toDisplayString(truncateText(product.url, 90)), 9, ["title"]),
                      createVNode("div", { class: "flex items-center justify-center space-x-2 px-2 font-semibold" }, [
                        product.old_price ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-sm text-slate-400 line-through"
                        }, toDisplayString(formatMoney(product.old_price, product.currency)), 1)) : createCommentVNode("", true),
                        createVNode("div", { class: "text-sm font-bold text-teal-700 dark:text-teal-300" }, toDisplayString(formatMoney(product.price, product.currency)), 1)
                      ]),
                      product.wholesale_price ? (openBlock(), createBlock("div", {
                        key: 2,
                        class: "mt-0.5 text-center text-[10px] font-semibold text-blue-700 dark:text-blue-300",
                        title: unref(t)("wholesalePrice")
                      }, [
                        createTextVNode(toDisplayString(formatMoney(product.wholesale_price, product.currency)) + " ", 1),
                        product.wholesale_min_quantity ? (openBlock(), createBlock("span", { key: 0 }, " × " + toDisplayString(product.wholesale_min_quantity), 1)) : createCommentVNode("", true)
                      ], 8, ["title"])) : createCommentVNode("", true),
                      createVNode("div", { class: "flex items-center justify-center space-x-1 text-[11px] font-semibold" }, [
                        createVNode("span", {
                          class: ["text-[10px]", product.in_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300"]
                        }, toDisplayString(product.in_stock ? unref(t)("inStock") : unref(t)("outOfStock")), 3),
                        createVNode("div", {
                          class: ["text-sm font-bold", product.in_stock ? "text-amber-600 dark:text-amber-300" : "text-rose-600 dark:text-rose-300"]
                        }, toDisplayString(product.quantity), 3)
                      ]),
                      shopTitle(product) ? (openBlock(), createBlock("div", {
                        key: 3,
                        class: "mt-1 flex items-center justify-center gap-1 text-[10px] text-amber-700 dark:text-amber-300",
                        title: supplierTitle(product)
                      }, [
                        createVNode("span", { class: "truncate" }, toDisplayString(truncateText(shopTitle(product), 42)), 1)
                      ], 8, ["title"])) : (openBlock(), createBlock("div", {
                        key: 4,
                        class: "mt-1 flex items-center justify-center text-[10px] text-slate-400",
                        title: supplierTitle(product)
                      }, toDisplayString(unref(t)("noData")), 9, ["title"])),
                      productShort(product) ? (openBlock(), createBlock("div", {
                        key: 5,
                        class: "line-clamp-3 text-center text-[12px] font-semibold text-teal-700 dark:text-teal-300"
                      }, toDisplayString(truncateText(productShort(product), 160)), 1)) : createCommentVNode("", true),
                      createVNode("div", { class: "grid grid-cols-2 gap-1 px-1 text-center text-[10px] font-semibold" }, [
                        createVNode("div", {
                          class: "rounded-sm border border-gray-400 bg-indigo-100 px-1 py-1 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300",
                          title: unref(t)("tags")
                        }, [
                          createVNode("div", null, toDisplayString(unref(t)("tags")), 1),
                          createVNode("div", null, toDisplayString(product.tags_count ?? 0), 1)
                        ], 8, ["title"]),
                        createVNode("div", {
                          class: "rounded-sm border border-gray-400 bg-cyan-100 px-1 py-1 text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300",
                          title: attributesTooltip(product)
                        }, [
                          createVNode("div", null, toDisplayString(unref(t)("attributes")), 1),
                          createVNode("div", null, toDisplayString(product.attribute_values_count ?? 0), 1)
                        ], 8, ["title"])
                      ]),
                      mainCategoryTitle(product) ? (openBlock(), createBlock("div", {
                        key: 6,
                        class: "mt-1 flex items-center justify-center gap-1 text-[10px] text-indigo-700 dark:text-indigo-300",
                        title: categoriesTitle(product)
                      }, [
                        createTextVNode(toDisplayString(truncateText(mainCategoryTitle(product), 55)) + " ", 1),
                        product.categories_count ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "text-slate-400"
                        }, " (" + toDisplayString(product.categories_count) + ") ", 1)) : createCommentVNode("", true)
                      ], 8, ["title"])) : createCommentVNode("", true),
                      createVNode("div", { class: "grid grid-cols-5 gap-1 text-center text-[10px] font-semibold" }, [
                        createVNode("div", {
                          class: "px-1 py-1",
                          title: unref(t)("rating")
                        }, [
                          createVNode("div", { class: "text-red-500 dark:text-red-400" }, "★"),
                          createVNode("span", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(formatRating(product)), 1)
                        ], 8, ["title"]),
                        createVNode("div", {
                          class: "px-1 py-1",
                          title: unref(t)("reviews")
                        }, [
                          createVNode("div", { class: "text-sky-500 dark:text-sky-300" }, "●"),
                          createVNode("span", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(product.reviews_count ?? 0), 1)
                        ], 8, ["title"]),
                        createVNode("div", {
                          class: "px-1 py-1",
                          title: unref(t)("likes")
                        }, [
                          createVNode("div", { class: "text-rose-400 dark:text-rose-300" }, "♥"),
                          createVNode("span", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(product.likes_count ?? 0), 1)
                        ], 8, ["title"]),
                        createVNode("div", {
                          class: "px-1 py-1",
                          title: unref(t)("views")
                        }, [
                          createVNode("div", { class: "text-blue-600 dark:text-blue-300" }, "◉"),
                          createVNode("span", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(product.views ?? 0), 1)
                        ], 8, ["title"]),
                        createVNode("a", {
                          href: _ctx.route("admin.marketProductVariants.index", {
                            market_product_id: product.id
                          }),
                          class: "flex flex-col items-center justify-center px-1 py-1 text-fuchsia-700 transition dark:text-fuchsia-300",
                          title: unref(t)("marketProductVariants")
                        }, [
                          createVNode("span", null, "▦"),
                          createVNode("span", { class: "text-slate-700 dark:text-slate-300" }, [
                            createTextVNode(toDisplayString(product.variants_count ?? 0) + " ", 1),
                            product.available_variants_count ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: "text-emerald-600 dark:text-emerald-300"
                            }, " / " + toDisplayString(product.available_variants_count), 1)) : createCommentVNode("", true)
                          ])
                        ], 8, ["href", "title"])
                      ]),
                      createVNode("div", { class: "text-center text-[11px] font-semibold text-fuchsia-700 dark:text-fuchsia-300" }, [
                        createVNode("span", null, toDisplayString(unref(t)("status")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(getStatusLabel(product.status)), 1)
                      ]),
                      createVNode("div", { class: "flex justify-center space-x-1" }, [
                        createVNode("span", {
                          class: ["rounded-sm border px-2 py-1 text-[10px] font-semibold", moderationBadge(product.moderation_status).class],
                          title: product.moderation_note && product.moderated_at ? `${product.moderation_note} [${formatDate(product.moderated_at)}]` : null
                        }, toDisplayString(moderationBadge(product.moderation_status).text), 11, ["title"]),
                        createVNode(_sfc_main$c, {
                          isAdmin: __props.isAdmin,
                          status: product.moderation_status ?? 0,
                          initialNote: product.moderation_note || "",
                          mode: "toggle",
                          onSubmit: ({ status, note }) => emit(
                            "approve",
                            product,
                            status,
                            note
                          )
                        }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-center border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex flex-wrap items-center justify-center gap-1" }, [
                      createVNode("div", { class: "flex flex-row items-center space-x-7" }, [
                        createVNode("div", { class: "flex items-center space-x-1" }, [
                          createVNode(_sfc_main$6, {
                            isActive: product.left,
                            title: product.left ? unref(t)("enabled") : unref(t)("disabled"),
                            onToggleLeft: ($event) => emit("toggle-left", product)
                          }, null, 8, ["isActive", "title", "onToggleLeft"]),
                          createVNode(_sfc_main$7, {
                            isActive: product.main,
                            title: product.main ? unref(t)("enabled") : unref(t)("disabled"),
                            onToggleMain: ($event) => emit("toggle-main", product)
                          }, null, 8, ["isActive", "title", "onToggleMain"]),
                          createVNode(_sfc_main$8, {
                            isActive: product.right,
                            title: product.right ? unref(t)("enabled") : unref(t)("disabled"),
                            onToggleRight: ($event) => emit("toggle-right", product)
                          }, null, 8, ["isActive", "title", "onToggleRight"])
                        ]),
                        createVNode("div", { class: "flex flex-row items-center space-x-1" }, [
                          createVNode(_sfc_main$9, {
                            isActive: product.is_new,
                            title: product.is_new ? unref(t)("enabled") : unref(t)("disabled"),
                            onToggleIsNew: ($event) => emit("toggle-is-new", product)
                          }, null, 8, ["isActive", "title", "onToggleIsNew"]),
                          createVNode(_sfc_main$a, {
                            isActive: product.is_hit,
                            title: product.is_hit ? unref(t)("enabled") : unref(t)("disabled"),
                            onToggleIsHit: ($event) => emit("toggle-is-hit", product)
                          }, null, 8, ["isActive", "title", "onToggleIsHit"]),
                          createVNode(_sfc_main$b, {
                            isActive: product.is_sale,
                            title: product.is_sale ? unref(t)("enabled") : unref(t)("disabled"),
                            onToggleIsSale: ($event) => emit("toggle-is-sale", product)
                          }, null, 8, ["isActive", "title", "onToggleIsSale"])
                        ])
                      ]),
                      createVNode("div", { class: "flex items-center space-x-1" }, [
                        createVNode(_sfc_main$d, {
                          isActive: product.activity,
                          title: product.activity ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleActivity: ($event) => emit("toggle-activity", product)
                        }, null, 8, ["isActive", "title", "onToggleActivity"]),
                        createVNode(_sfc_main$e, {
                          href: _ctx.route("admin.marketProducts.edit", {
                            marketProduct: product.id
                          })
                        }, null, 8, ["href"]),
                        createVNode(_sfc_main$2, {
                          href: _ctx.route("admin.marketProductVariants.create", {
                            market_product_id: product.id
                          }),
                          title: unref(t)("addMarketProductVariant"),
                          class: "py-1"
                        }, null, 8, ["href", "title"]),
                        createVNode(_sfc_main$f, {
                          onDelete: ($event) => emit("delete", product)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProduct/View/ProductCardGrid.vue");
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
    adminMarketProductsProcessingMode: {
      type: String,
      default: "frontend"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    adminMarketProductsPerPage: {
      type: Number,
      default: 6
    },
    adminMarketProductsDefaultSort: {
      type: String,
      default: "idDesc"
    },
    products: {
      type: [Array, Object],
      default: () => []
    },
    productsCount: {
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
    const getProductTranslation = (product) => (product == null ? void 0 : product.translation) || {};
    const getProductTitle = (product) => {
      var _a;
      return ((_a = getProductTranslation(product)) == null ? void 0 : _a.title) || `ID: ${product == null ? void 0 : product.id}`;
    };
    const getProductSortTitle = (product) => {
      var _a;
      return ((_a = getProductTranslation(product)) == null ? void 0 : _a.title) || "";
    };
    const getProductSubtitle = (product) => {
      var _a;
      return ((_a = getProductTranslation(product)) == null ? void 0 : _a.subtitle) || "";
    };
    const getProductShort = (product) => {
      var _a;
      return ((_a = getProductTranslation(product)) == null ? void 0 : _a.short) || "";
    };
    const getProductDescription = (product) => {
      var _a;
      return ((_a = getProductTranslation(product)) == null ? void 0 : _a.description) || "";
    };
    const getCompanyTitle = (product) => {
      var _a, _b;
      return ((_b = (_a = product == null ? void 0 : product.company) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getShopTitle = (product) => {
      var _a, _b;
      return ((_b = (_a = product == null ? void 0 : product.shop) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getBrandTitle = (product) => {
      var _a, _b;
      return ((_b = (_a = product == null ? void 0 : product.brand) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getOwnerName = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.owner) == null ? void 0 : _a.name) || "";
    };
    const getOwnerEmail = (product) => {
      var _a;
      return ((_a = product == null ? void 0 : product.owner) == null ? void 0 : _a.email) || "";
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
    const byIdDesc = (a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_market_products") || "cards"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_market_products",
        value
      );
    });
    const itemsPerPage = ref(
      props.adminMarketProductsPerPage || 6
    );
    watch(itemsPerPage, (newValue, oldValue) => {
      if (newValue === oldValue) {
        return;
      }
      router.put(
        route("admin.settings.updateAdminCountMarketProducts"),
        {
          value: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(
              `Показ ${newValue} товаров на странице.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.value) || "Ошибка обновления количества товаров."
            );
          }
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminMarketProductsDefaultSort || "idDesc"
    );
    watch(sortParam, (newValue, oldValue) => {
      if (newValue === oldValue) {
        return;
      }
      router.put(
        route("admin.settings.updateAdminSortMarketProducts"),
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
              "Сортировка товаров успешно изменена."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.value) || "Ошибка обновления сортировки товаров."
            );
          }
        }
      );
    });
    const productsList = computed(() => {
      var _a, _b, _c, _d;
      if (Array.isArray(props.products)) {
        return props.products;
      }
      if (Array.isArray((_a = props.products) == null ? void 0 : _a.data)) {
        return props.products.data;
      }
      if (Array.isArray((_c = (_b = props.products) == null ? void 0 : _b.data) == null ? void 0 : _c.data)) {
        return props.products.data.data;
      }
      if (Array.isArray((_d = props.products) == null ? void 0 : _d.resource)) {
        return props.products.resource;
      }
      return [];
    });
    const localProducts = ref([]);
    watch(
      productsList,
      (newValue) => {
        localProducts.value = JSON.parse(
          JSON.stringify(newValue || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const showConfirmDeleteModal = ref(false);
    const productToDeleteId = ref(null);
    const productToDeleteTitle = ref("");
    const confirmDelete = (productOrId, title = null) => {
      if (typeof productOrId === "object") {
        productToDeleteId.value = productOrId == null ? void 0 : productOrId.id;
        productToDeleteTitle.value = title || getProductTitle(productOrId);
      } else {
        productToDeleteId.value = productOrId;
        productToDeleteTitle.value = title || `ID: ${productOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      productToDeleteId.value = null;
      productToDeleteTitle.value = "";
    };
    const deleteProduct = () => {
      if (productToDeleteId.value === null) {
        return;
      }
      const id = productToDeleteId.value;
      const title = productToDeleteTitle.value;
      router.delete(
        route(
          "admin.marketProducts.destroy",
          {
            marketProduct: id
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Товар "${title || `ID: ${id}`}" удалён.`
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0];
            const message = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[firstKey]) || "Ошибка при удалении товара.";
            toast.error(
              `${message} (Товар: ${title || `ID: ${id}`})`
            );
          },
          onFinish: closeModal
        }
      );
    };
    const patchLocalProduct = (productId, callback) => {
      const index = localProducts.value.findIndex(
        (product) => Number(product.id) === Number(productId)
      );
      if (index === -1) {
        return;
      }
      callback(localProducts.value[index]);
    };
    const toggleActivity = (product) => {
      if (!(product == null ? void 0 : product.id)) {
        return;
      }
      const activity = !product.activity;
      const title = getProductTitle(product);
      router.put(
        route(
          "admin.actions.marketProducts.updateActivity",
          {
            marketProduct: product.id
          }
        ),
        {
          activity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalProduct(product.id, (item) => {
              item.activity = activity;
              item.is_active = activity;
            });
            toast.success(
              activity ? `Товар "${title}" активирован.` : `Товар "${title}" деактивирован.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности товара "${title}".`
            );
          }
        }
      );
    };
    const toggleLeft = (product) => {
      if (!(product == null ? void 0 : product.id)) {
        return;
      }
      const left = !product.left;
      const title = getProductTitle(product);
      router.put(
        route(
          "admin.actions.marketProducts.updateLeft",
          {
            marketProduct: product.id
          }
        ),
        {
          left
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalProduct(product.id, (item) => {
              item.left = left;
            });
            toast.success(
              `Позиция left товара "${title}" обновлена.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.left) || (errors == null ? void 0 : errors.general) || `Ошибка изменения left товара "${title}".`
            );
          }
        }
      );
    };
    const toggleMain = (product) => {
      if (!(product == null ? void 0 : product.id)) {
        return;
      }
      const main = !product.main;
      const title = getProductTitle(product);
      router.put(
        route(
          "admin.actions.marketProducts.updateMain",
          {
            marketProduct: product.id
          }
        ),
        {
          main
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalProduct(product.id, (item) => {
              item.main = main;
            });
            toast.success(
              `Позиция main товара "${title}" обновлена.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.main) || (errors == null ? void 0 : errors.general) || `Ошибка изменения main товара "${title}".`
            );
          }
        }
      );
    };
    const toggleRight = (product) => {
      if (!(product == null ? void 0 : product.id)) {
        return;
      }
      const right = !product.right;
      const title = getProductTitle(product);
      router.put(
        route(
          "admin.actions.marketProducts.updateRight",
          {
            marketProduct: product.id
          }
        ),
        {
          right
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalProduct(product.id, (item) => {
              item.right = right;
            });
            toast.success(
              `Позиция right товара "${title}" обновлена.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.right) || (errors == null ? void 0 : errors.general) || `Ошибка изменения right товара "${title}".`
            );
          }
        }
      );
    };
    const approveProduct = (product, status = 1, note = "") => {
      if (!(product == null ? void 0 : product.id) || !isAdmin.value) {
        return;
      }
      router.put(
        route(
          "admin.actions.marketProducts.approve",
          {
            marketProduct: product.id
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
            patchLocalProduct(product.id, (item) => {
              item.moderation_status = status;
              item.is_pending = status === 0;
              item.is_approved = status === 1;
              item.is_rejected = status === 2;
              item.moderation_note = note;
            });
            toast.success(
              status === 1 ? "Товар одобрен." : "Товар отклонён."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.moderation_status) || (errors == null ? void 0 : errors.general) || "Ошибка модерации товара."
            );
          }
        }
      );
    };
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const productMatchesSearch = (product, query) => {
      var _a;
      if (!query) return true;
      const searchValues = [
        product == null ? void 0 : product.url,
        product == null ? void 0 : product.sku,
        product == null ? void 0 : product.vendor_code,
        product == null ? void 0 : product.barcode,
        product == null ? void 0 : product.status,
        product == null ? void 0 : product.moderation_note,
        (_a = getProductTranslation(product)) == null ? void 0 : _a.title,
        getProductSubtitle(product),
        getProductShort(product),
        getProductDescription(product),
        getCompanyTitle(product),
        getShopTitle(product),
        getBrandTitle(product),
        getOwnerName(product),
        getOwnerEmail(product)
      ];
      return searchValues.some((value) => normalize(value).includes(query));
    };
    const filteredProducts = computed(() => {
      if (props.useServerProcessing) {
        return localProducts.value;
      }
      const query = normalize(searchQuery.value);
      return localProducts.value.filter(
        (product) => productMatchesSearch(product, query)
      );
    });
    const compareText = (left, right) => {
      return normalize(left).localeCompare(
        normalize(right),
        locale.value
      );
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringAsc = (field) => (a, b) => compareText(a == null ? void 0 : a[field], b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringDesc = (field) => (a, b) => compareText(b == null ? void 0 : b[field], a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortedProducts = computed(() => {
      let list = [...filteredProducts.value];
      const filteredByIdDesc = (predicate) => list.filter(predicate).sort(byIdDesc);
      if (sortParam.value === "activity") return filteredByIdDesc((product) => Boolean(product.activity));
      if (sortParam.value === "inactive") return filteredByIdDesc((product) => !product.activity);
      if (sortParam.value === "inStock") return filteredByIdDesc((product) => Boolean(product.in_stock));
      if (sortParam.value === "outOfStock") return filteredByIdDesc((product) => !product.in_stock);
      if (sortParam.value === "left") return filteredByIdDesc((product) => Boolean(product.left));
      if (sortParam.value === "noLeft") return filteredByIdDesc((product) => !product.left);
      if (sortParam.value === "main") return filteredByIdDesc((product) => Boolean(product.main));
      if (sortParam.value === "noMain") return filteredByIdDesc((product) => !product.main);
      if (sortParam.value === "right") return filteredByIdDesc((product) => Boolean(product.right));
      if (sortParam.value === "noRight") return filteredByIdDesc((product) => !product.right);
      if (sortParam.value === "new") return filteredByIdDesc((product) => Boolean(product.is_new));
      if (sortParam.value === "notNew") return filteredByIdDesc((product) => !product.is_new);
      if (sortParam.value === "hit") return filteredByIdDesc((product) => Boolean(product.is_hit));
      if (sortParam.value === "notHit") return filteredByIdDesc((product) => !product.is_hit);
      if (sortParam.value === "sale") return filteredByIdDesc((product) => Boolean(product.is_sale));
      if (sortParam.value === "notSale") return filteredByIdDesc((product) => !product.is_sale);
      if (sortParam.value === "hasVariants") {
        return filteredByIdDesc((product) => safeNumber(product.variants_count) > 0);
      }
      if (sortParam.value === "withoutVariants") {
        return filteredByIdDesc((product) => safeNumber(product.variants_count) === 0);
      }
      if (sortParam.value === "statusDraft") return filteredByIdDesc((product) => product.status === "draft");
      if (sortParam.value === "statusPublished") return filteredByIdDesc((product) => product.status === "published");
      if (sortParam.value === "statusArchived") return filteredByIdDesc((product) => product.status === "archived");
      if (sortParam.value === "moderationPending") {
        return filteredByIdDesc((product) => moderationNum(product.moderation_status) === 0);
      }
      if (sortParam.value === "moderationApproved") {
        return filteredByIdDesc((product) => moderationNum(product.moderation_status) === 1);
      }
      if (sortParam.value === "moderationRejected") {
        return filteredByIdDesc((product) => moderationNum(product.moderation_status) === 2);
      }
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        titleAsc: (a, b) => compareText(getProductSortTitle(a), getProductSortTitle(b)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        titleDesc: (a, b) => compareText(getProductSortTitle(b), getProductSortTitle(a)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        urlAsc: byStringAsc("url"),
        urlDesc: byStringDesc("url"),
        skuAsc: byStringAsc("sku"),
        skuDesc: byStringDesc("sku"),
        vendorCodeAsc: byStringAsc("vendor_code"),
        vendorCodeDesc: byStringDesc("vendor_code"),
        barcodeAsc: byStringAsc("barcode"),
        barcodeDesc: byStringDesc("barcode"),
        priceAsc: byNumberAsc("price"),
        priceDesc: byNumberDesc("price"),
        oldPriceAsc: byNumberAsc("old_price"),
        oldPriceDesc: byNumberDesc("old_price"),
        purchasePriceAsc: byNumberAsc("purchase_price"),
        purchasePriceDesc: byNumberDesc("purchase_price"),
        wholesalePriceAsc: byNumberAsc("wholesale_price"),
        wholesalePriceDesc: byNumberDesc("wholesale_price"),
        quantityAsc: byNumberAsc("quantity"),
        quantityDesc: byNumberDesc("quantity"),
        inStockAsc: byNumberAsc("in_stock"),
        inStockDesc: byNumberDesc("in_stock"),
        weightAsc: byNumberAsc("weight"),
        weightDesc: byNumberDesc("weight"),
        lengthAsc: byNumberAsc("length"),
        lengthDesc: byNumberDesc("length"),
        widthAsc: byNumberAsc("width"),
        widthDesc: byNumberDesc("width"),
        heightAsc: byNumberAsc("height"),
        heightDesc: byNumberDesc("height"),
        companyAsc: (a, b) => compareText(getCompanyTitle(a), getCompanyTitle(b)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        companyDesc: (a, b) => compareText(getCompanyTitle(b), getCompanyTitle(a)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        shopAsc: (a, b) => compareText(getShopTitle(a), getShopTitle(b)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        shopDesc: (a, b) => compareText(getShopTitle(b), getShopTitle(a)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        brandAsc: (a, b) => compareText(getBrandTitle(a), getBrandTitle(b)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        brandDesc: (a, b) => compareText(getBrandTitle(b), getBrandTitle(a)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        ownerNameAsc: (a, b) => compareText(getOwnerName(a), getOwnerName(b)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        ownerNameDesc: (a, b) => compareText(getOwnerName(b), getOwnerName(a)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        ownerEmailAsc: (a, b) => compareText(getOwnerEmail(a), getOwnerEmail(b)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        ownerEmailDesc: (a, b) => compareText(getOwnerEmail(b), getOwnerEmail(a)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        viewsAsc: byNumberAsc("views"),
        viewsDesc: byNumberDesc("views"),
        likesAsc: byNumberAsc("likes_count"),
        likesDesc: byNumberDesc("likes_count"),
        ratingAsc: byNumberAsc("rating_avg"),
        ratingDesc: byNumberDesc("rating_avg"),
        ratingCountAsc: byNumberAsc("rating_count"),
        ratingCountDesc: byNumberDesc("rating_count"),
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        categoriesAsc: byNumberAsc("categories_count"),
        categoriesDesc: byNumberDesc("categories_count"),
        tagsAsc: byNumberAsc("tags_count"),
        tagsDesc: byNumberDesc("tags_count"),
        attributesAsc: byNumberAsc("attribute_values_count"),
        attributesDesc: byNumberDesc("attribute_values_count"),
        variantsAsc: byNumberAsc("variants_count"),
        variantsDesc: byNumberDesc("variants_count"),
        reviewsAsc: byNumberAsc("reviews_count"),
        reviewsDesc: byNumberDesc("reviews_count"),
        relatedProductsAsc: byNumberAsc("related_products_count"),
        relatedProductsDesc: byNumberDesc("related_products_count"),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        leftAsc: byNumberAsc("left"),
        leftDesc: byNumberDesc("left"),
        mainAsc: byNumberAsc("main"),
        mainDesc: byNumberDesc("main"),
        rightAsc: byNumberAsc("right"),
        rightDesc: byNumberDesc("right"),
        newAsc: (a, b) => Number(Boolean(a == null ? void 0 : a.is_new)) - Number(Boolean(b == null ? void 0 : b.is_new)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        newDesc: (a, b) => Number(Boolean(b == null ? void 0 : b.is_new)) - Number(Boolean(a == null ? void 0 : a.is_new)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        hitAsc: (a, b) => Number(Boolean(a == null ? void 0 : a.is_hit)) - Number(Boolean(b == null ? void 0 : b.is_hit)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        hitDesc: (a, b) => Number(Boolean(b == null ? void 0 : b.is_hit)) - Number(Boolean(a == null ? void 0 : a.is_hit)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        saleAsc: (a, b) => Number(Boolean(a == null ? void 0 : a.is_sale)) - Number(Boolean(b == null ? void 0 : b.is_sale)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        saleDesc: (a, b) => Number(Boolean(b == null ? void 0 : b.is_sale)) - Number(Boolean(a == null ? void 0 : a.is_sale)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        statusAsc: byStringAsc("status"),
        statusDesc: byStringDesc("status"),
        moderationStatusAsc: byNumberAsc("moderation_status"),
        moderationStatusDesc: byNumberDesc("moderation_status"),
        publishedAtAsc: byDateAsc("published_at"),
        publishedAtDesc: byDateDesc("published_at"),
        showFromAtAsc: byDateAsc("show_from_at"),
        showFromAtDesc: byDateDesc("show_from_at"),
        showToAtAsc: byDateAsc("show_to_at"),
        showToAtDesc: byDateDesc("show_to_at"),
        createdAtAsc: byDateAsc("created_at"),
        createdAtDesc: byDateDesc("created_at"),
        dateAsc: byDateAsc("created_at"),
        dateDesc: byDateDesc("created_at"),
        updatedAtAsc: byDateAsc("updated_at"),
        updatedAtDesc: byDateDesc("updated_at")
      };
      const sorter = sortMap[sortParam.value] || sortMap.idDesc;
      return list.sort(sorter);
    });
    const frontendPaginatedProducts = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      return sortedProducts.value.slice(
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
    const displayedProducts = computed(() => {
      return props.useServerProcessing ? localProducts.value : frontendPaginatedProducts.value;
    });
    const selectedProducts = ref([]);
    const isSelected = (productId) => {
      return selectedProducts.value.includes(productId);
    };
    const toggleSelectProduct = (productOrId) => {
      const id = typeof productOrId === "object" ? productOrId == null ? void 0 : productOrId.id : productOrId;
      if (!id) {
        return;
      }
      if (isSelected(id)) {
        selectedProducts.value = selectedProducts.value.filter(
          (selectedId) => selectedId !== id
        );
        return;
      }
      selectedProducts.value.push(id);
    };
    const toggleAll = (eventOrValue) => {
      var _a;
      const checked = typeof eventOrValue === "boolean" ? eventOrValue : Boolean(((_a = eventOrValue == null ? void 0 : eventOrValue.target) == null ? void 0 : _a.checked) ?? (eventOrValue == null ? void 0 : eventOrValue.checked));
      if (!checked) {
        selectedProducts.value = [];
        return;
      }
      selectedProducts.value = displayedProducts.value.map((product) => product == null ? void 0 : product.id).filter(Boolean);
    };
    const allDisplayedSelected = computed(() => {
      const ids = displayedProducts.value.map((product) => product == null ? void 0 : product.id).filter(Boolean);
      return ids.length > 0 && ids.every((id) => selectedProducts.value.includes(id));
    });
    const bulkToggleActivity = (activity) => {
      if (!selectedProducts.value.length) {
        toast.warning(
          "Выберите хотя бы один товар."
        );
        return;
      }
      const ids = [...selectedProducts.value];
      router.put(
        route(
          "admin.actions.marketProducts.bulkUpdateActivity"
        ),
        {
          ids,
          activity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localProducts.value.forEach((product) => {
              if (ids.includes(product.id)) {
                product.activity = activity;
                product.is_active = activity;
              }
            });
            selectedProducts.value = [];
            toast.success(
              "Активность выбранных товаров обновлена."
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
      if (!selectedProducts.value.length) {
        toast.warning("Выберите товары для массового действия.");
        return;
      }
      const ids = [...selectedProducts.value];
      router.put(route(routeName), {
        ids,
        [field]: value
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          ids.forEach((id) => {
            patchLocalProduct(id, (product) => {
              product[field] = value;
            });
          });
          selectedProducts.value = [];
          toast.success(message);
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления товаров.");
        }
      });
    };
    const bulkDelete = () => {
      if (!selectedProducts.value.length) {
        toast.warning(
          "Выберите хотя бы один товар для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные товары?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.marketProducts.bulkDestroy"
        ),
        {
          data: {
            ids: selectedProducts.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedProducts.value = [];
            toast.success(
              "Выбранные товары успешно удалены."
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || "Ошибка массового удаления товаров."
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
          "admin.actions.marketProducts.bulkUpdateIsNew",
          "Товары добавлены в новинки."
        );
      } else if (action === "isNewOff") {
        bulkToggleFlag(
          "is_new",
          false,
          "admin.actions.marketProducts.bulkUpdateIsNew",
          "Товары убраны из новинок."
        );
      } else if (action === "isHitOn") {
        bulkToggleFlag(
          "is_hit",
          true,
          "admin.actions.marketProducts.bulkUpdateIsHit",
          "Товары добавлены в рекомендуемые."
        );
      } else if (action === "isHitOff") {
        bulkToggleFlag(
          "is_hit",
          false,
          "admin.actions.marketProducts.bulkUpdateIsHit",
          "Товары убраны из рекомендуемых."
        );
      } else if (action === "isSaleOn") {
        bulkToggleFlag(
          "is_sale",
          true,
          "admin.actions.marketProducts.bulkUpdateIsSale",
          "Товары добавлены в распродажу."
        );
      } else if (action === "isSaleOff") {
        bulkToggleFlag(
          "is_sale",
          false,
          "admin.actions.marketProducts.bulkUpdateIsSale",
          "Товары убраны из распродажи."
        );
      } else if (action === "left") {
        bulkToggleFlag(
          "left",
          true,
          "admin.actions.marketProducts.bulkUpdateLeft",
          "Товары добавлены в левую колонку."
        );
      } else if (action === "noLeft") {
        bulkToggleFlag(
          "left",
          false,
          "admin.actions.marketProducts.bulkUpdateLeft",
          "Товары убраны из левой колонки."
        );
      } else if (action === "main") {
        bulkToggleFlag(
          "main",
          true,
          "admin.actions.marketProducts.bulkUpdateMain",
          "Товары добавлены в главный блок."
        );
      } else if (action === "noMain") {
        bulkToggleFlag(
          "main",
          false,
          "admin.actions.marketProducts.bulkUpdateMain",
          "Товары убраны из главного блока."
        );
      } else if (action === "right") {
        bulkToggleFlag(
          "right",
          true,
          "admin.actions.marketProducts.bulkUpdateRight",
          "Товары добавлены в правую колонку."
        );
      } else if (action === "noRight") {
        bulkToggleFlag(
          "right",
          false,
          "admin.actions.marketProducts.bulkUpdateRight",
          "Товары убраны из правой колонки."
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
          "admin.actions.marketProducts.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            items.forEach((item) => {
              patchLocalProduct(item.id, (product) => {
                product.sort = item.sort;
              });
            });
            toast.success(
              "Сортировка товаров обновлена."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка сортировки товаров:",
              errors
            );
            toast.error(
              (errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.items) || "Ошибка обновления сортировки товаров."
            );
          }
        }
      );
    };
    const toggleProductFlag = (product, field, routeName, enabledMessage, disabledMessage) => {
      if (!(product == null ? void 0 : product.id)) {
        return;
      }
      const newValue = !product[field];
      const title = getProductTitle(product);
      router.put(route(routeName, { marketProduct: product.id }), {
        [field]: newValue
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          patchLocalProduct(product.id, (item) => {
            item[field] = newValue;
          });
          product[field] = newValue;
          toast.success(newValue ? `${enabledMessage} "${title}".` : `${disabledMessage} "${title}".`);
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || `Ошибка изменения флага товара "${title}".`);
        }
      });
    };
    const toggleIsNew = (product) => {
      toggleProductFlag(
        product,
        "is_new",
        "admin.actions.marketProducts.updateIsNew",
        "Товар добавлен в новинки",
        "Товар убран из новинок"
      );
    };
    const toggleIsHit = (product) => {
      toggleProductFlag(
        product,
        "is_hit",
        "admin.actions.marketProducts.updateIsHit",
        "Товар добавлен в рекомендуемые",
        "Товар убран из рекомендуемых"
      );
    };
    const toggleIsSale = (product) => {
      toggleProductFlag(
        product,
        "is_sale",
        "admin.actions.marketProducts.updateIsSale",
        "Товар добавлен в распродажу",
        "Товар убран из распродажи"
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("marketProducts")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("marketProducts"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("marketProducts")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("marketProducts")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="flex flex-col gap-3 mb-3 sm:flex-row sm:items-center sm:justify-between"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$g, {
              href: _ctx.route("admin.marketProducts.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addMarketProduct"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addMarketProduct")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$h, {
              "setting-key": "adminMarketProductsProcessingMode",
              mode: __props.adminMarketProductsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.productsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.productsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$i, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.productsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$j, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.productsCount) {
              _push2(`<div class="flex flex-col md:flex-row items-center justify-between gap-3 my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountMarketProducts"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$4, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": ($event) => sortParam.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.productsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$m, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.productsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.productsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$5, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$n, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.productsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$o, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredProducts.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$p, { pagination: __props.products }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$3, {
                products: displayedProducts.value,
                "selected-products": selectedProducts.value,
                "all-selected": allDisplayedSelected.value,
                "is-admin": isAdmin.value,
                onToggleSelect: toggleSelectProduct,
                onToggleAll: toggleAll,
                onToggleActivity: toggleActivity,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleIsNew: toggleIsNew,
                onToggleIsHit: toggleIsHit,
                onToggleIsSale: toggleIsSale,
                onApprove: approveProduct,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                products: displayedProducts.value,
                "selected-products": selectedProducts.value,
                "is-admin": isAdmin.value,
                onToggleSelect: toggleSelectProduct,
                onToggleAll: toggleAll,
                onToggleActivity: toggleActivity,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleIsNew: toggleIsNew,
                onToggleIsHit: toggleIsHit,
                onToggleIsSale: toggleIsSale,
                onApprove: approveProduct,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate
              }, null, _parent2, _scopeId));
            }
            if (!__props.productsCount) {
              _push2(`<div class="py-12 text-center text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else if (!__props.useServerProcessing && !displayedProducts.value.length) {
              _push2(`<div class="py-10 text-center text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.productsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-4"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$o, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredProducts.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$p, { pagination: __props.products }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$q, {
              show: showConfirmDeleteModal.value,
              "on-cancel": closeModal,
              "on-confirm": deleteProduct,
              "cancel-text": unref(t)("cancel"),
              "confirm-text": unref(t)("yesDelete"),
              onClose: closeModal
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<p class="text-sm text-slate-700 dark:text-slate-200"${_scopeId2}>${ssrInterpolate(unref(t)("delete"))} <strong${_scopeId2}>${ssrInterpolate(productToDeleteTitle.value)}</strong> ? </p>`);
                } else {
                  return [
                    createVNode("p", { class: "text-sm text-slate-700 dark:text-slate-200" }, [
                      createTextVNode(toDisplayString(unref(t)("delete")) + " ", 1),
                      createVNode("strong", null, toDisplayString(productToDeleteTitle.value), 1),
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
                    createVNode(_sfc_main$g, {
                      href: _ctx.route("admin.marketProducts.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addMarketProduct")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$h, {
                      "setting-key": "adminMarketProductsProcessingMode",
                      mode: __props.adminMarketProductsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.productsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.productsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$i, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.productsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$j, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.productsCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex flex-col md:flex-row items-center justify-between gap-3 my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountMarketProducts"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$4, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.productsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$m, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.productsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$5, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$n, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.productsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$o, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredProducts.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$p, {
                      key: 1,
                      pagination: __props.products
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$3, {
                    key: 5,
                    products: displayedProducts.value,
                    "selected-products": selectedProducts.value,
                    "all-selected": allDisplayedSelected.value,
                    "is-admin": isAdmin.value,
                    onToggleSelect: toggleSelectProduct,
                    onToggleAll: toggleAll,
                    onToggleActivity: toggleActivity,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleIsNew: toggleIsNew,
                    onToggleIsHit: toggleIsHit,
                    onToggleIsSale: toggleIsSale,
                    onApprove: approveProduct,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate
                  }, null, 8, ["products", "selected-products", "all-selected", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    products: displayedProducts.value,
                    "selected-products": selectedProducts.value,
                    "is-admin": isAdmin.value,
                    onToggleSelect: toggleSelectProduct,
                    onToggleAll: toggleAll,
                    onToggleActivity: toggleActivity,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleIsNew: toggleIsNew,
                    onToggleIsHit: toggleIsHit,
                    onToggleIsSale: toggleIsSale,
                    onApprove: approveProduct,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate
                  }, null, 8, ["products", "selected-products", "is-admin"])),
                  !__props.productsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "py-12 text-center text-slate-500 dark:text-slate-300"
                  }, toDisplayString(unref(t)("noData")), 1)) : !__props.useServerProcessing && !displayedProducts.value.length ? (openBlock(), createBlock("div", {
                    key: 8,
                    class: "py-10 text-center text-slate-500 dark:text-slate-300"
                  }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true),
                  __props.productsCount ? (openBlock(), createBlock("div", {
                    key: 9,
                    class: "flex justify-center items-center flex-col md:flex-row mt-4"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$o, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredProducts.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$p, {
                      key: 1,
                      pagination: __props.products
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$q, {
                show: showConfirmDeleteModal.value,
                "on-cancel": closeModal,
                "on-confirm": deleteProduct,
                "cancel-text": unref(t)("cancel"),
                "confirm-text": unref(t)("yesDelete"),
                onClose: closeModal
              }, {
                default: withCtx(() => [
                  createVNode("p", { class: "text-sm text-slate-700 dark:text-slate-200" }, [
                    createTextVNode(toDisplayString(unref(t)("delete")) + " ", 1),
                    createVNode("strong", null, toDisplayString(productToDeleteTitle.value), 1),
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketProducts/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
