import { mergeProps, unref, useSSRContext, computed, ref, watch, resolveComponent, withCtx, createVNode, createTextVNode, toDisplayString, createBlock, createCommentVNode, openBlock } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderList, ssrRenderClass, ssrRenderStyle, ssrIncludeBooleanAttr, ssrRenderComponent } from "vue/server-renderer";
import { usePage, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import draggable from "vuedraggable";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$a } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$k } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$g } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$e, a as _sfc_main$h, b as _sfc_main$i } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$c } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$d, a as _sfc_main$j } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$f } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$b } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$6 } from "./ModerationButton-D_ehimPY.js";
import { _ as _sfc_main$7 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$8 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$9 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
import "./LocaleSelectOption-BeLdazeX.js";
import "./ResponsiveNavLink-gtte0z5g.js";
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
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: "flex flex-col sm:flex-row items-center",
        title: unref(t)("bulkActions")
      }, _attrs))}><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketCategory/Select/BulkActionSelect.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: String
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>─────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>─────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option value="urlAsc">URL A→Z</option><option value="urlDesc">URL Z→A</option><option disabled>─────────────────</option><option value="levelAsc">${ssrInterpolate(unref(t)("level"))} 0→9</option><option value="levelDesc">${ssrInterpolate(unref(t)("level"))} 9→0</option><option value="parentAsc">${ssrInterpolate(unref(t)("parentCategory"))} 0→9</option><option value="parentDesc">${ssrInterpolate(unref(t)("parentCategory"))} 9→0</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="inMenuDesc">${ssrInterpolate(unref(t)("printInMenu"))} ON→OFF</option><option value="inMenuAsc">${ssrInterpolate(unref(t)("printInMenu"))} OFF→ON</option><option value="inMenu">${ssrInterpolate(unref(t)("printInMenu"))}</option><option value="notInMenu">${ssrInterpolate(unref(t)("notPrintInMenu"))}</option><option disabled>─────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option disabled>─────────────────</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>─────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="statusDraft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="statusPublished">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="statusArchived">${ssrInterpolate(unref(t)("statusArchived"))}</option><option disabled>─────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>─────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketCategory/Sort/SortSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "MenuToggle",
  __ssrInlineRender: true,
  props: {
    isEnabled: Boolean,
    title: String
  },
  emits: ["toggle-menu"],
  setup(__props, { emit: __emit }) {
    const buttonClass = computed(() => [
      "flex items-center"
    ]);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        class: buttonClass.value,
        title: __props.title,
        type: "button"
      }, _attrs))}>`);
      if (__props.isEnabled) {
        _push(`<svg class="w-5 h-5 fill-current text-emerald-400 hover:text-gray-500 dark:hover:text-white" viewBox="0 0 448 512"><path d="M16 132h416c8.8 0 16-7.2 16-16V76c0-8.8-7.2-16-16-16H16C7.2 60 0 67.2 0 76v40c0 8.8 7.2 16 16 16zm0 160h416c8.8 0 16-7.2 16-16v-40c0-8.8-7.2-16-16-16H16c-8.8 0-16 7.2-16 16v40c0 8.8 7.2 16 16 16zm0 160h416c8.8 0 16-7.2 16-16v-40c0-8.8-7.2-16-16-16H16c-8.8 0-16 7.2-16 16v40c0 8.8 7.2 16 16 16z"></path></svg>`);
      } else {
        _push(`<svg class="w-5 h-5 fill-current text-gray-300 hover:text-emerald-400 dark:hover:text-emerald-300" viewBox="0 0 448 512"><path d="M16 132h416c8.8 0 16-7.2 16-16V76c0-8.8-7.2-16-16-16H16C7.2 60 0 67.2 0 76v40c0 8.8 7.2 16 16 16zm0 160h416c8.8 0 16-7.2 16-16v-40c0-8.8-7.2-16-16-16H16c-8.8 0-16 7.2-16 16v40c0 8.8 7.2 16 16 16zm0 160h416c8.8 0 16-7.2 16-16v-40c0-8.8-7.2-16-16-16H16c-8.8 0-16 7.2-16 16v40c0 8.8 7.2 16 16 16z"></path></svg>`);
      }
      _push(`</button>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/MenuToggle.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "CategoryCardGrid",
  __ssrInlineRender: true,
  props: {
    categories: { type: Array, default: () => [] },
    selectedCategories: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
    "toggle-activity",
    "delete",
    "toggle-select",
    "toggle-all",
    "approve",
    "toggle-menu"
  ],
  setup(__props, { emit: __emit }) {
    const { t, locale } = useI18n();
    const emit = __emit;
    const openedOwnerBlocks = ref([]);
    const isOwnerBlockOpen = (categoryId) => {
      return openedOwnerBlocks.value.includes(categoryId);
    };
    const getTranslation = (category) => {
      return (category == null ? void 0 : category.translation) || {};
    };
    const getTitle = (category) => {
      return getTranslation(category).title || `ID: ${category == null ? void 0 : category.id}`;
    };
    const getShort = (category) => {
      return getTranslation(category).short || "";
    };
    const getDescription = (category) => {
      return getTranslation(category).description || "";
    };
    const getParentTitle = (category) => {
      var _a, _b;
      if (!(category == null ? void 0 : category.parent_id)) {
        return t("rootCategory");
      }
      return ((_b = (_a = category == null ? void 0 : category.parent) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || t("noData");
    };
    const truncateText = (text, maxLength = 80) => {
      if (!text) {
        return "";
      }
      const value = String(text);
      return value.length > maxLength ? `${value.slice(0, maxLength).trimEnd()}…` : value;
    };
    const getPrimaryImage = (category) => {
      const images = Array.isArray(category == null ? void 0 : category.images) ? category.images : [];
      if (!images.length) {
        return null;
      }
      return [...images].sort((a, b) => {
        return Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0);
      })[0];
    };
    const getPrimaryImageUrl = (category) => {
      const image = getPrimaryImage(category);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.url) || "";
    };
    const getSafeIcon = (icon) => {
      if (typeof icon !== "string") {
        return "";
      }
      const value = icon.trim();
      if (value.startsWith("<svg") && value.endsWith("</svg>")) {
        return value;
      }
      return "";
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
    const dateLocale = computed(() => {
      const locales = {
        ru: "ru-RU",
        en: "en-US",
        kk: "kk-KZ",
        kz: "kk-KZ"
      };
      return locales[locale.value] || locale.value || "ru-RU";
    });
    const formatDate = (value) => {
      if (!value) {
        return "";
      }
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return new Intl.DateTimeFormat(
        dateLocale.value,
        {
          year: "numeric",
          month: "long",
          day: "numeric"
        }
      ).format(date);
    };
    const getOwnerName = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const getOwnerEmail = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.owner) == null ? void 0 : _a.email) || "";
    };
    const getOwnerTitle = (category) => {
      const owner = category == null ? void 0 : category.owner;
      if (!owner) {
        return t("noData");
      }
      const name = owner.name || "";
      const email = owner.email || "";
      return email ? `${name} — ${email}`.trim() : name || t("noData");
    };
    const getOwnerAvatar = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
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
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedCategories.length)}</div>`);
      if (__props.categories.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (__props.categories.length) {
        _push(`<div class="p-3"><div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"><!--[-->`);
        ssrRenderList(__props.categories, (category) => {
          var _a, _b;
          _push(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center space-x-2"><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `${unref(t)("sort")}: ${category.sort}`)}> ID: ${ssrInterpolate(category.id)}</div><button type="button" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"${ssrRenderAttr("title", isOwnerBlockOpen(category.id) ? unref(t)("hideOwner") : unref(t)("showOwner"))}><svg class="${ssrRenderClass([{ "rotate-180": isOwnerBlockOpen(category.id) }, "w-4 h-4 transition-transform duration-200"])}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"></path></svg></button></div><div class="flex items-center space-x-2"><span class="${ssrRenderClass([moderationBadge(category.moderation_status).class, "text-[10px] px-2 py-0.5 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", category.moderation_note && category.moderated_at ? `${category.moderation_note} [${formatDate(category.moderated_at)}]` : null)}>${ssrInterpolate(moderationBadge(category.moderation_status).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedCategories.includes(category.id)) ? " checked" : ""}></div></header><div style="${ssrRenderStyle(isOwnerBlockOpen(category.id) ? null : { display: "none" })}" class="flex flex-col items-center justify-center text-center py-3"><img${ssrRenderAttr("src", getOwnerAvatar(category))}${ssrRenderAttr("title", getOwnerTitle(category))}${ssrRenderAttr("alt", unref(t)("author"))} class="h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", getOwnerName(category))}>${ssrInterpolate(getOwnerName(category))}</div>`);
          if (getOwnerEmail(category)) {
            _push(`<div class="text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1"${ssrRenderAttr("title", getOwnerEmail(category))}>${ssrInterpolate(getOwnerEmail(category))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="relative w-full bg-slate-200 dark:bg-slate-900">`);
          if (getPrimaryImageUrl(category)) {
            _push(`<img${ssrRenderAttr("src", getPrimaryImageUrl(category))}${ssrRenderAttr("alt", ((_a = getPrimaryImage(category)) == null ? void 0 : _a.alt) || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", ((_b = getPrimaryImage(category)) == null ? void 0 : _b.caption) || unref(t)("image"))} class="h-32 w-full object-cover">`);
          } else {
            _push(`<img src="/storage/market/market_category_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))}${ssrRenderAttr("title", unref(t)("defaultImageTitle"))} class="h-32 w-full object-cover">`);
          }
          _push(`</div><div class="flex flex-col flex-1 px-3 py-2 space-y-2"><div class="flex items-center justify-center text-center"><div class="flex items-center justify-center space-x-2 max-w-full"><div class="flex items-center justify-center shrink-0">`);
          if (getSafeIcon(category.icon)) {
            _push(`<div class="w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center">${getSafeIcon(category.icon) ?? ""}</div>`);
          } else {
            _push(`<svg class="w-4 h-4 text-slate-500 dark:text-slate-300" fill="currentColor" viewBox="0 0 16 16"><path d="M2 2a1 1 0 011-1h3.5L8 2.5 9.5 1H13a1 1 0 011 1v3.5L12.5 7 14 8.5V12a1 1 0 01-1 1H9.5L8 11.5 6.5 13H3a1 1 0 01-1-1V8.5L3.5 7 2 5.5V2z"></path></svg>`);
          }
          _push(`</div><a${ssrRenderAttr("href", `/market/categories/${encodeURIComponent(category.url)}`)} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-sky-700 dark:text-sky-200 hover:text-amber-700 dark:hover:text-amber-200 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", getTitle(category))}>${ssrInterpolate(truncateText(getTitle(category)))}</a></div></div>`);
          if (getShort(category)) {
            _push(`<div class="font-semibold text-[12px] text-center text-cyan-700 dark:text-cyan-300"${ssrRenderAttr("title", getShort(category))}>${ssrInterpolate(truncateText(getShort(category)))}</div>`);
          } else {
            _push(`<!---->`);
          }
          if (getDescription(category)) {
            _push(`<div class="text-[11px] text-center text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", getDescription(category))}>${ssrInterpolate(truncateText(getDescription(category), 100))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="flex flex-wrap justify-center gap-1"><span class="text-[10px] px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("level"))}> L${ssrInterpolate(category.level ?? 1)}</span><span class="text-[10px] px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("parentCategory"))}>${ssrInterpolate(getParentTitle(category))}</span><span class="text-[10px] px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("children"))}>${ssrInterpolate(category.children_count ?? 0)}</span></div>`);
          if ((category.views ?? 0) > 0) {
            _push(`<div class="flex items-center justify-center space-x-1"><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975C.253 8.715 3.248 14 8 14s7.747-5.285 7.872-5.51a1 1 0 0 0 0-.98C15.747 7.285 12.752 2 8 2zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z"></path></svg><span class="text-[10px] text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("views"))}>${ssrInterpolate(category.views ?? 0)}</span></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="grid grid-cols-1 gap-0.5 text-[11px] text-slate-600 dark:text-slate-300"><div class="font-semibold text-center text-fuchsia-700 dark:text-fuchsia-300"><span>${ssrInterpolate(unref(t)("status"))}: </span> ${ssrInterpolate(getStatusLabel(category.status))}</div></div><div class="flex justify-center gap-1"><span class="${ssrRenderClass([moderationBadge(category.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", category.moderation_note && category.moderated_at ? `${category.moderation_note} [${formatDate(category.moderated_at)}]` : null)}>${ssrInterpolate(moderationBadge(category.moderation_status).text)}</span>`);
          _push(ssrRenderComponent(_sfc_main$6, {
            isAdmin: __props.isAdmin,
            status: category.moderation_status ?? 0,
            initialNote: category.moderation_note || "",
            mode: "toggle",
            onSubmit: ({ status, note }) => emit("approve", category, status, note)
          }, null, _parent));
          _push(`</div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center space-x-1">`);
          _push(ssrRenderComponent(_sfc_main$3, {
            "is-enabled": category.in_menu,
            title: category.in_menu ? unref(t)("showInMenu") : unref(t)("notShowInMenu"),
            onToggleMenu: ($event) => emit("toggle-menu", category)
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$7, {
            isActive: category.activity,
            title: category.activity ? unref(t)("enabled") : unref(t)("disabled"),
            onToggleActivity: ($event) => emit("toggle-activity", category)
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$8, {
            href: _ctx.route("admin.marketCategories.edit", {
              marketCategory: category.id
            })
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$9, {
            onClick: ($event) => emit("delete", category)
          }, null, _parent));
          _push(`</div></footer></div>`);
        });
        _push(`<!--]--></div></div>`);
      } else {
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketCategory/View/CategoryCardGrid.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ Object.assign({
  name: "CategoryTreeDraggable"
}, {
  __name: "CategoryTreeDraggable",
  __ssrInlineRender: true,
  props: {
    category: { type: Object, required: true },
    level: { type: Number, default: 0 },
    selectedCategories: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
    "toggle-activity",
    "delete",
    "toggle-select",
    "request-drag-end",
    "approve",
    "toggle-menu"
  ],
  setup(__props, { emit: __emit }) {
    const { t, locale } = useI18n();
    const props = __props;
    const emit = __emit;
    const storageKey = computed(() => {
      return `admin.market.categories.tree.expanded.${props.category.id}`;
    });
    const readExpandedState = () => {
      const savedValue = localStorage.getItem(storageKey.value);
      if (savedValue === null) {
        return true;
      }
      return savedValue === "1";
    };
    const isExpanded = ref(readExpandedState());
    watch(
      () => props.category.id,
      () => {
        isExpanded.value = readExpandedState();
      }
    );
    const children = computed(() => {
      return Array.isArray(props.category.children) ? props.category.children : [];
    });
    const hasChildren = computed(() => {
      return children.value.length > 0;
    });
    const translation = computed(() => {
      var _a;
      return ((_a = props.category) == null ? void 0 : _a.translation) || {};
    });
    const title = computed(() => {
      return translation.value.title || `ID: ${props.category.id}`;
    });
    const short = computed(() => {
      return translation.value.short || "";
    });
    const owner = computed(() => {
      var _a;
      return ((_a = props.category) == null ? void 0 : _a.owner) || null;
    });
    const ownerTitle = computed(() => {
      if (!owner.value) {
        return t("noData");
      }
      const name = owner.value.name || "";
      const email = owner.value.email || "";
      return email ? `${name} — ${email}`.trim() : name || t("noData");
    });
    const primaryImage = computed(() => {
      var _a;
      const images = Array.isArray((_a = props.category) == null ? void 0 : _a.images) ? props.category.images : [];
      if (!images.length) {
        return null;
      }
      return [...images].sort((a, b) => {
        return Number((a == null ? void 0 : a.order) ?? 0) - Number((b == null ? void 0 : b.order) ?? 0);
      })[0];
    });
    const primaryImageUrl = computed(() => {
      var _a, _b, _c;
      return ((_a = primaryImage.value) == null ? void 0 : _a.webp_url) || ((_b = primaryImage.value) == null ? void 0 : _b.thumb_url) || ((_c = primaryImage.value) == null ? void 0 : _c.url) || "";
    });
    const safeIcon = computed(() => {
      var _a;
      const icon = (_a = props.category) == null ? void 0 : _a.icon;
      if (typeof icon !== "string") {
        return "";
      }
      const value = icon.trim();
      if (value.startsWith("<svg") && value.endsWith("</svg>")) {
        return value;
      }
      return "";
    });
    const handleDragEnd = () => {
      emit("request-drag-end");
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
    const dateLocale = computed(() => {
      const locales = {
        ru: "ru-RU",
        en: "en-US",
        kk: "kk-KZ",
        kz: "kk-KZ"
      };
      return locales[locale.value] || locale.value || "ru-RU";
    });
    const formatDate = (value) => {
      if (!value) {
        return "";
      }
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return new Intl.DateTimeFormat(
        dateLocale.value,
        {
          year: "numeric",
          month: "long",
          day: "numeric"
        }
      ).format(date);
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
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c;
      const _component_CategoryTreeDraggable = resolveComponent("CategoryTreeDraggable", true);
      _push(`<div${ssrRenderAttrs(_attrs)}><div class="category-item mb-1" style="${ssrRenderStyle({ marginLeft: `${__props.level * 20}px` })}"><div class="flex items-center justify-between py-1 px-2 border border-gray-400 rounded-sm bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 transition duration-150 ease-in-out"><div class="flex items-center space-x-2 flex-grow min-w-0"><span class="drag-handle cursor-move mr-1 flex-shrink-0"${ssrRenderAttr("title", unref(t)("dragDrop"))}><svg viewBox="0 0 512 512" class="w-4 h-4"><path class="fill-current text-sky-500 dark:text-sky-200" d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8h32v96h-96v-32c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c9.2 9.2 22.9 11.9 34.9 6.9s19.8-16.6 19.8-29.6v-32h96v96h-32c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8h-32v-96h96v32c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-9.2-9.2-22.9-11.9-34.9-6.9s-19.8 16.6-19.8 29.6v32h-96v-96h32c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-64-64z"></path></svg></span>`);
      if (hasChildren.value) {
        _push(`<button type="button" class="flex-shrink-0 text-slate-900 hover:text-red-500 dark:text-slate-100 dark:hover:text-red-200"${ssrRenderAttr("title", isExpanded.value ? unref(t)("collapse") : unref(t)("expand"))}><svg class="${ssrRenderClass([{ "rotate-90": isExpanded.value }, "w-5 h-5 transform transition-transform duration-150"])}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>`);
      } else {
        _push(`<span class="w-5 h-5 inline-block flex-shrink-0"></span>`);
      }
      _push(`<div class="w-8 font-semibold text-sm text-amber-600 dark:text-amber-200 mr-1 flex-shrink-0"${ssrRenderAttr("title", `[${unref(t)("sort")}: ${__props.category.sort}] ${unref(t)("level")}: ${__props.category.level} / ${getStatusLabel(__props.category.status)}`)}>${ssrInterpolate(__props.category.id)}</div>`);
      if ((_a = owner.value) == null ? void 0 : _a.profile_photo_url) {
        _push(`<img${ssrRenderAttr("src", owner.value.profile_photo_url)}${ssrRenderAttr("title", ownerTitle.value)}${ssrRenderAttr("alt", unref(t)("author"))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600">`);
      } else {
        _push(`<!---->`);
      }
      if (safeIcon.value) {
        _push(`<div class="pl-3 w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center">${safeIcon.value ?? ""}</div>`);
      } else {
        _push(`<svg class="w-4 h-4 text-slate-500 dark:text-slate-300" fill="currentColor" viewBox="0 0 16 16"><path d="M2 2a1 1 0 011-1h3.5L8 2.5 9.5 1H13a1 1 0 011 1v3.5L12.5 7 14 8.5V12a1 1 0 01-1 1H9.5L8 11.5 6.5 13H3a1 1 0 01-1-1V8.5L3.5 7 2 5.5V2z"></path></svg>`);
      }
      _push(`<a${ssrRenderAttr("href", `/market/categories/${encodeURIComponent(__props.category.url)}`)} target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-sky-700 dark:text-sky-200 hover:text-amber-700 dark:hover:text-amber-200 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", short.value)}>${ssrInterpolate(title.value)}</a><span class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("level"))}> L${ssrInterpolate(__props.category.level ?? 1)}</span><span class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("children"))}>${ssrInterpolate(__props.category.children_count ?? children.value.length)}</span></div><div class="flex items-center space-x-1 flex-shrink-0 ml-4"><div class="flex items-center gap-1"><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975C.253 8.715 3.248 14 8 14s7.747-5.285 7.872-5.51a1 1 0 0 0 0-.98C15.747 7.285 12.752 2 8 2zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z"></path></svg><div class="w-7 font-semibold text-[10px] text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("views"))}>${ssrInterpolate(__props.category.views ?? 0)}</div></div><div class="flex justify-center">`);
      if (primaryImageUrl.value) {
        _push(`<img${ssrRenderAttr("src", primaryImageUrl.value)}${ssrRenderAttr("alt", ((_b = primaryImage.value) == null ? void 0 : _b.alt) || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", ((_c = primaryImage.value) == null ? void 0 : _c.caption) || unref(t)("image"))} class="h-6 w-8 object-cover rounded-sm border border-slate-400 dark:border-slate-200 p-0.5">`);
      } else {
        _push(`<div class="h-6 w-8 flex items-center justify-center rounded-sm border border-dashed border-slate-400 dark:border-slate-200 text-[9px] text-slate-500 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("noImage"))}> — </div>`);
      }
      _push(`</div><span class="${ssrRenderClass([moderationBadge(__props.category.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", __props.category.moderation_note && __props.category.moderated_at ? `${__props.category.moderation_note} [${formatDate(__props.category.moderated_at)}]` : null)}>${ssrInterpolate(moderationBadge(__props.category.moderation_status).text)}</span>`);
      _push(ssrRenderComponent(_sfc_main$6, {
        isAdmin: __props.isAdmin,
        status: __props.category.moderation_status ?? 0,
        initialNote: __props.category.moderation_note || "",
        mode: "toggle",
        onSubmit: ({ status, note }) => emit("approve", __props.category, status, note)
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$3, {
        "is-enabled": __props.category.in_menu,
        title: __props.category.in_menu ? unref(t)("showInMenu") : unref(t)("notShowInMenu"),
        onToggleMenu: ($event) => emit("toggle-menu", __props.category)
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$7, {
        isActive: __props.category.activity,
        title: __props.category.activity ? unref(t)("enabled") : unref(t)("disabled"),
        onToggleActivity: ($event) => emit("toggle-activity", __props.category)
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$8, {
        href: _ctx.route("admin.marketCategories.edit", {
          marketCategory: __props.category.id
        })
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$9, {
        onClick: ($event) => emit("delete", __props.category)
      }, null, _parent));
      _push(`<div class="pl-1.5"><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedCategories.includes(__props.category.id)) ? " checked" : ""} class="form-checkbox rounded-sm text-indigo-500 flex-shrink-0"></div></div></div></div><div style="${ssrRenderStyle(isExpanded.value && hasChildren.value ? null : { display: "none" })}" class="children-container mt-1">`);
      _push(ssrRenderComponent(unref(draggable), {
        list: children.value,
        tag: "div",
        "item-key": "id",
        handle: ".drag-handle",
        group: "market-categories",
        class: "category-tree-children",
        "data-parent-id": __props.category.id,
        onEnd: handleDragEnd
      }, {
        item: withCtx(({ element: childCategory }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_CategoryTreeDraggable, {
              category: childCategory,
              level: __props.level + 1,
              "selected-categories": __props.selectedCategories,
              "is-admin": __props.isAdmin,
              onToggleActivity: ($event) => emit("toggle-activity", $event),
              onDelete: ($event) => emit("delete", $event),
              onToggleSelect: ($event) => emit("toggle-select", $event),
              onRequestDragEnd: handleDragEnd,
              onApprove: (category, status, note) => emit("approve", category, status, note),
              onToggleMenu: ($event) => emit("toggle-menu", $event)
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_CategoryTreeDraggable, {
                category: childCategory,
                level: __props.level + 1,
                "selected-categories": __props.selectedCategories,
                "is-admin": __props.isAdmin,
                onToggleActivity: ($event) => emit("toggle-activity", $event),
                onDelete: ($event) => emit("delete", $event),
                onToggleSelect: ($event) => emit("toggle-select", $event),
                onRequestDragEnd: handleDragEnd,
                onApprove: (category, status, note) => emit("approve", category, status, note),
                onToggleMenu: ($event) => emit("toggle-menu", $event)
              }, null, 8, ["category", "level", "selected-categories", "is-admin", "onToggleActivity", "onDelete", "onToggleSelect", "onApprove", "onToggleMenu"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div>`);
    };
  }
});
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketCategory/Tree/CategoryTreeDraggable.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminMarketCategoriesProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    categoriesTree: { type: Array, default: () => [] },
    categories: { type: [Array, Object], default: () => [] },
    categoriesCount: { type: Number, default: 0 },
    adminMarketCategoriesPerPage: { type: Number, default: 6 },
    adminMarketCategoriesDefaultSort: { type: String, default: "idDesc" },
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
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_market_categories") || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_market_categories", value);
    });
    const categoriesList = computed(() => {
      var _a, _b, _c;
      if (Array.isArray(props.categories)) {
        return props.categories;
      }
      if (Array.isArray((_a = props.categories) == null ? void 0 : _a.data)) {
        return props.categories.data;
      }
      if (Array.isArray((_c = (_b = props.categories) == null ? void 0 : _b.data) == null ? void 0 : _c.data)) {
        return props.categories.data.data;
      }
      return [];
    });
    const localCategoriesTree = ref([]);
    const localCategoriesFlat = ref([]);
    watch(
      () => props.categoriesTree,
      (categories) => {
        localCategoriesTree.value = JSON.parse(
          JSON.stringify(categories || [])
        );
      },
      { immediate: true, deep: true }
    );
    watch(
      categoriesList,
      (categories) => {
        localCategoriesFlat.value = JSON.parse(
          JSON.stringify(categories || [])
        );
      },
      { immediate: true, deep: true }
    );
    const itemsPerPage = ref(
      props.adminMarketCategoriesPerPage || 6
    );
    const sortParam = ref(
      props.sortParam || props.adminMarketCategoriesDefaultSort || "idDesc"
    );
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const selectedCategories = ref([]);
    watch(itemsPerPage, (value) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminCountMarketCategories"),
        { value },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(`Показ ${value} категорий на странице.`);
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления количества категорий."
            );
          }
        }
      );
    });
    watch(sortParam, (value) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortMarketCategories"),
        { value },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing && viewMode.value !== "table") {
              router.get(
                window.location.pathname,
                {
                  ...Object.fromEntries(
                    new URLSearchParams(window.location.search)
                  ),
                  sort: value || void 0,
                  page: void 0
                },
                {
                  preserveScroll: true,
                  preserveState: false,
                  replace: true
                }
              );
            }
            toast.info("Сортировка категорий успешно изменена.");
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления сортировки категорий."
            );
          }
        }
      );
    });
    watch([itemsPerPage, searchQuery, sortParam], () => {
      currentPage.value = 1;
    });
    const normalize = (value) => {
      return String(value ?? "").trim().toLowerCase();
    };
    const numberValue = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const dateValue = (value) => {
      if (!value) return 0;
      const timestamp = new Date(value).getTime();
      return Number.isNaN(timestamp) ? 0 : timestamp;
    };
    const compareIdDesc = (a, b) => {
      return numberValue(b == null ? void 0 : b.id) - numberValue(a == null ? void 0 : a.id);
    };
    const compareNumber = (aValue, bValue, direction, a, b) => {
      const result = (numberValue(aValue) - numberValue(bValue)) * direction;
      return result || compareIdDesc(a, b);
    };
    const compareText = (aValue, bValue, direction, a, b) => {
      const result = normalize(aValue).localeCompare(
        normalize(bValue),
        locale.value || void 0
      ) * direction;
      return result || compareIdDesc(a, b);
    };
    const compareDate = (aValue, bValue, direction, a, b) => {
      const result = (dateValue(aValue) - dateValue(bValue)) * direction;
      return result || compareIdDesc(a, b);
    };
    const filterBy = (items, callback) => {
      return items.filter(callback).sort(compareIdDesc);
    };
    const getTranslation = (category) => {
      return (category == null ? void 0 : category.translation) || {};
    };
    const getTitle = (category) => {
      return getTranslation(category).title || `ID: ${category == null ? void 0 : category.id}`;
    };
    const getTranslationTitle = (category) => {
      return getTranslation(category).title || "";
    };
    const getParentTranslation = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.parent) == null ? void 0 : _a.translation) || {};
    };
    const getOwnerName = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.owner) == null ? void 0 : _a.name) || "";
    };
    const getOwnerEmail = (category) => {
      var _a;
      return ((_a = category == null ? void 0 : category.owner) == null ? void 0 : _a.email) || "";
    };
    const sortCategories = (categories) => {
      const items = Array.isArray(categories) ? [...categories] : [];
      if (sortParam.value === "activity") {
        return filterBy(items, (category) => category.activity);
      }
      if (sortParam.value === "inactive") {
        return filterBy(items, (category) => !category.activity);
      }
      if (sortParam.value === "inMenu") {
        return filterBy(items, (category) => category.in_menu);
      }
      if (sortParam.value === "notInMenu") {
        return filterBy(items, (category) => !category.in_menu);
      }
      if (sortParam.value === "statusDraft") {
        return filterBy(
          items,
          (category) => category.status === "draft"
        );
      }
      if (sortParam.value === "statusPublished") {
        return filterBy(
          items,
          (category) => category.status === "published"
        );
      }
      if (sortParam.value === "statusArchived") {
        return filterBy(
          items,
          (category) => category.status === "archived"
        );
      }
      if (sortParam.value === "moderationPending") {
        return filterBy(
          items,
          (category) => numberValue(category.moderation_status) === 0
        );
      }
      if (sortParam.value === "moderationApproved") {
        return filterBy(
          items,
          (category) => numberValue(category.moderation_status) === 1
        );
      }
      if (sortParam.value === "moderationRejected") {
        return filterBy(
          items,
          (category) => numberValue(category.moderation_status) === 2
        );
      }
      const sortMap = {
        idAsc: (a, b) => numberValue(a.id) - numberValue(b.id),
        idDesc: compareIdDesc,
        sortAsc: (a, b) => compareNumber(a.sort, b.sort, 1, a, b),
        sortDesc: (a, b) => compareNumber(a.sort, b.sort, -1, a, b),
        levelAsc: (a, b) => compareNumber(a.level, b.level, 1, a, b),
        levelDesc: (a, b) => compareNumber(a.level, b.level, -1, a, b),
        parentAsc: (a, b) => compareNumber(a.parent_id, b.parent_id, 1, a, b),
        parentDesc: (a, b) => compareNumber(a.parent_id, b.parent_id, -1, a, b),
        urlAsc: (a, b) => compareText(a.url, b.url, 1, a, b),
        urlDesc: (a, b) => compareText(a.url, b.url, -1, a, b),
        titleAsc: (a, b) => compareText(
          getTranslationTitle(a),
          getTranslationTitle(b),
          1,
          a,
          b
        ),
        titleDesc: (a, b) => compareText(
          getTranslationTitle(a),
          getTranslationTitle(b),
          -1,
          a,
          b
        ),
        viewsAsc: (a, b) => compareNumber(a.views, b.views, 1, a, b),
        viewsDesc: (a, b) => compareNumber(a.views, b.views, -1, a, b),
        imagesAsc: (a, b) => compareNumber(
          a.images_count,
          b.images_count,
          1,
          a,
          b
        ),
        imagesDesc: (a, b) => compareNumber(
          a.images_count,
          b.images_count,
          -1,
          a,
          b
        ),
        childrenAsc: (a, b) => compareNumber(
          a.children_count,
          b.children_count,
          1,
          a,
          b
        ),
        childrenDesc: (a, b) => compareNumber(
          a.children_count,
          b.children_count,
          -1,
          a,
          b
        ),
        activityAsc: (a, b) => compareNumber(a.activity, b.activity, 1, a, b),
        activityDesc: (a, b) => compareNumber(a.activity, b.activity, -1, a, b),
        inMenuAsc: (a, b) => compareNumber(a.in_menu, b.in_menu, 1, a, b),
        inMenuDesc: (a, b) => compareNumber(a.in_menu, b.in_menu, -1, a, b),
        moderationStatusAsc: (a, b) => compareNumber(
          a.moderation_status,
          b.moderation_status,
          1,
          a,
          b
        ),
        moderationStatusDesc: (a, b) => compareNumber(
          a.moderation_status,
          b.moderation_status,
          -1,
          a,
          b
        ),
        ownerNameAsc: (a, b) => compareText(
          getOwnerName(a),
          getOwnerName(b),
          1,
          a,
          b
        ),
        ownerNameDesc: (a, b) => compareText(
          getOwnerName(a),
          getOwnerName(b),
          -1,
          a,
          b
        ),
        ownerEmailAsc: (a, b) => compareText(
          getOwnerEmail(a),
          getOwnerEmail(b),
          1,
          a,
          b
        ),
        ownerEmailDesc: (a, b) => compareText(
          getOwnerEmail(a),
          getOwnerEmail(b),
          -1,
          a,
          b
        ),
        statusAsc: (a, b) => compareText(a.status, b.status, 1, a, b),
        statusDesc: (a, b) => compareText(a.status, b.status, -1, a, b),
        publishedAtAsc: (a, b) => compareDate(
          a.published_at,
          b.published_at,
          1,
          a,
          b
        ),
        publishedAtDesc: (a, b) => compareDate(
          a.published_at,
          b.published_at,
          -1,
          a,
          b
        ),
        createdAtAsc: (a, b) => compareDate(
          a.created_at,
          b.created_at,
          1,
          a,
          b
        ),
        createdAtDesc: (a, b) => compareDate(
          a.created_at,
          b.created_at,
          -1,
          a,
          b
        ),
        dateAsc: (a, b) => compareDate(
          a.created_at,
          b.created_at,
          1,
          a,
          b
        ),
        dateDesc: (a, b) => compareDate(
          a.created_at,
          b.created_at,
          -1,
          a,
          b
        ),
        updatedAtAsc: (a, b) => compareDate(
          a.updated_at,
          b.updated_at,
          1,
          a,
          b
        ),
        updatedAtDesc: (a, b) => compareDate(
          a.updated_at,
          b.updated_at,
          -1,
          a,
          b
        )
      };
      const comparator = sortMap[sortParam.value];
      return comparator ? items.sort(comparator) : items;
    };
    const filteredCategories = computed(() => {
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortCategories(localCategoriesFlat.value);
      }
      const categories = localCategoriesFlat.value.filter((category) => {
        var _a, _b;
        const translation = getTranslation(category);
        const parentTranslation = getParentTranslation(category);
        const values = [
          category.url,
          category.icon,
          category.status,
          category.moderation_note,
          translation.title,
          translation.subtitle,
          translation.short,
          translation.description,
          parentTranslation.title,
          (_a = category.owner) == null ? void 0 : _a.name,
          (_b = category.owner) == null ? void 0 : _b.email
        ];
        return values.some((value) => {
          return normalize(value).includes(query);
        });
      });
      return sortCategories(categories);
    });
    const paginatedCategories = computed(() => {
      const perPage = numberValue(itemsPerPage.value) || 10;
      const start = (currentPage.value - 1) * perPage;
      return filteredCategories.value.slice(
        start,
        start + perPage
      );
    });
    const displayedCategories = computed(() => {
      return props.useServerProcessing ? categoriesList.value : paginatedCategories.value;
    });
    const showConfirmDeleteModal = ref(false);
    const categoryToDeleteId = ref(null);
    const categoryToDeleteTitle = ref("");
    const confirmDelete = (categoryOrId, title = null) => {
      if (categoryOrId && typeof categoryOrId === "object") {
        categoryToDeleteId.value = categoryOrId.id;
        categoryToDeleteTitle.value = title || getTitle(categoryOrId);
      } else {
        categoryToDeleteId.value = categoryOrId;
        categoryToDeleteTitle.value = title || `ID: ${categoryOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      categoryToDeleteId.value = null;
      categoryToDeleteTitle.value = "";
    };
    const deleteCategory = () => {
      if (categoryToDeleteId.value === null) {
        return;
      }
      const categoryId = categoryToDeleteId.value;
      const categoryTitle = categoryToDeleteTitle.value;
      router.delete(
        route(
          "admin.marketCategories.destroy",
          { marketCategory: categoryId }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Категория "${categoryTitle || `ID: ${categoryId}`}" удалена.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0];
            const message = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
            toast.error(
              `${message} (Категория: ${categoryTitle || `ID: ${categoryId}`})`
            );
          },
          onFinish: closeModal
        }
      );
    };
    const patchCategoryInTree = (nodes, categoryId, callback) => {
      var _a;
      const items = Array.isArray(nodes) ? nodes : [];
      for (const node of items) {
        if (node.id === categoryId) {
          callback(node);
          return true;
        }
        if (((_a = node.children) == null ? void 0 : _a.length) && patchCategoryInTree(
          node.children,
          categoryId,
          callback
        )) {
          return true;
        }
      }
      return false;
    };
    const patchCategoryInFlat = (categoryId, callback) => {
      const category = localCategoriesFlat.value.find(
        (item) => item.id === categoryId
      );
      if (category) {
        callback(category);
      }
    };
    const toggleActivity = (category) => {
      const activity = !category.activity;
      const title = getTitle(category);
      router.put(
        route(
          "admin.actions.marketCategories.updateActivity",
          { marketCategory: category.id }
        ),
        { activity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchCategoryInTree(
              localCategoriesTree.value,
              category.id,
              (node) => {
                node.activity = activity;
              }
            );
            patchCategoryInFlat(
              category.id,
              (node) => {
                node.activity = activity;
              }
            );
            category.activity = activity;
            toast.success(
              activity ? `Категория "${title}" активирована.` : `Категория "${title}" деактивирована.`
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
    const toggleInMenu = (category) => {
      const inMenu = !category.in_menu;
      const title = getTitle(category);
      router.put(
        route(
          "admin.actions.marketCategories.updateInMenu",
          { marketCategory: category.id }
        ),
        { in_menu: inMenu },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchCategoryInTree(
              localCategoriesTree.value,
              category.id,
              (node) => {
                node.in_menu = inMenu;
              }
            );
            patchCategoryInFlat(
              category.id,
              (node) => {
                node.in_menu = inMenu;
              }
            );
            category.in_menu = inMenu;
            toast.success(
              inMenu ? `Категория "${title}" добавлена в меню.` : `Категория "${title}" скрыта из меню.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors.in_menu || errors.general || `Ошибка изменения показа в меню для "${title}".`
            );
          }
        }
      );
    };
    const setModerationState = (category, status, note) => {
      const moderationStatus = numberValue(status);
      category.moderation_status = moderationStatus;
      category.is_pending = moderationStatus === 0;
      category.is_approved = moderationStatus === 1;
      category.is_rejected = moderationStatus === 2;
      category.moderation_note = note;
    };
    const approveCategory = (category, status = 1, note = "") => {
      if (!(category == null ? void 0 : category.id)) {
        return;
      }
      const moderationStatus = numberValue(status);
      router.put(
        route(
          "admin.actions.marketCategories.approve",
          { marketCategory: category.id }
        ),
        {
          moderation_status: moderationStatus,
          moderation_note: note
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchCategoryInTree(
              localCategoriesTree.value,
              category.id,
              (node) => {
                setModerationState(
                  node,
                  moderationStatus,
                  note
                );
              }
            );
            patchCategoryInFlat(
              category.id,
              (node) => {
                setModerationState(
                  node,
                  moderationStatus,
                  note
                );
              }
            );
            setModerationState(
              category,
              moderationStatus,
              note
            );
            if (moderationStatus === 1) {
              toast.success("Категория одобрена.");
            } else if (moderationStatus === 2) {
              toast.success("Категория отклонена.");
            } else {
              toast.success("Категория отправлена на модерацию.");
            }
          },
          onError: () => {
            toast.error("Ошибка модерации категории.");
          }
        }
      );
    };
    const collectTreeItems = (nodes, parentId = null, items = []) => {
      const categories = Array.isArray(nodes) ? nodes : [];
      categories.forEach((node, index) => {
        var _a;
        const normalizedParentId = parentId ?? null;
        node.sort = index;
        node.parent_id = normalizedParentId;
        items.push({
          id: node.id,
          sort: index,
          parent_id: normalizedParentId
        });
        patchCategoryInFlat(
          node.id,
          (category) => {
            category.sort = index;
            category.parent_id = normalizedParentId;
          }
        );
        if ((_a = node.children) == null ? void 0 : _a.length) {
          collectTreeItems(
            node.children,
            node.id,
            items
          );
        }
      });
      return items;
    };
    const handleDragEnd = () => {
      const items = collectTreeItems(
        localCategoriesTree.value
      );
      if (!items.length) {
        return;
      }
      router.put(
        route(
          "admin.actions.marketCategories.updateSortBulk"
        ),
        { items },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Иерархия категорий успешно обновлена."
            );
          },
          onError: (errors) => {
            toast.error(
              errors.message || errors.general || "Ошибка обновления иерархии категорий."
            );
            router.reload({
              only: [
                "categoriesTree",
                "categories"
              ],
              preserveScroll: true
            });
          }
        }
      );
    };
    const getAllTreeIds = (nodes) => {
      const items = Array.isArray(nodes) ? nodes : [];
      const ids = [];
      items.forEach((node) => {
        var _a;
        ids.push(node.id);
        if ((_a = node.children) == null ? void 0 : _a.length) {
          ids.push(...getAllTreeIds(node.children));
        }
      });
      return ids;
    };
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = viewMode.value === "table" ? getAllTreeIds(localCategoriesTree.value) : displayedCategories.value.map(
        (category) => category.id
      );
      selectedCategories.value = checked ? [...ids] : [];
    };
    const toggleAllCards = ({ ids, checked }) => {
      selectedCategories.value = checked ? [...ids] : [];
    };
    const toggleSelectCategory = (categoryId) => {
      const index = selectedCategories.value.indexOf(categoryId);
      if (index !== -1) {
        selectedCategories.value.splice(index, 1);
        return;
      }
      selectedCategories.value.push(categoryId);
    };
    const updateTreeActivity = (nodes, ids, activity) => {
      const items = Array.isArray(nodes) ? nodes : [];
      items.forEach((node) => {
        var _a;
        if (ids.includes(node.id)) {
          node.activity = activity;
        }
        if ((_a = node.children) == null ? void 0 : _a.length) {
          updateTreeActivity(
            node.children,
            ids,
            activity
          );
        }
      });
    };
    const bulkToggleActivity = (activity) => {
      if (!selectedCategories.value.length) {
        toast.warning(
          "Выберите категории для активации/деактивации."
        );
        return;
      }
      const ids = [...selectedCategories.value];
      router.put(
        route(
          "admin.actions.marketCategories.bulkUpdateActivity"
        ),
        {
          ids,
          activity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            updateTreeActivity(
              localCategoriesTree.value,
              ids,
              activity
            );
            localCategoriesFlat.value = localCategoriesFlat.value.map((category) => {
              if (!ids.includes(category.id)) {
                return category;
              }
              return {
                ...category,
                activity
              };
            });
            selectedCategories.value = [];
            toast.success(
              "Активность выбранных категорий обновлена."
            );
          },
          onError: (errors) => {
            toast.error(
              errors.ids || errors.activity || errors.general || "Ошибка массового обновления активности."
            );
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedCategories.value.length) {
        toast.warning(
          "Выберите хотя бы одну категорию для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные категории?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.marketCategories.bulkDestroy"
        ),
        {
          data: {
            ids: [...selectedCategories.value]
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedCategories.value = [];
            toast.success(
              "Выбранные категории успешно удалены."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0];
            toast.error(
              errors[errorKey] || "Ошибка при массовом удалении категорий."
            );
          }
        }
      );
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
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("marketCategories")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("marketCategories"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("marketCategories")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("marketCategories")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto" data-v-ab435f88${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" data-v-ab435f88${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3" data-v-ab435f88${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$a, {
              href: _ctx.route("admin.marketCategories.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addMarketCategory"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addMarketCategory")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$b, {
              "setting-key": "adminMarketCategoriesProcessingMode",
              mode: __props.adminMarketCategoriesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.categoriesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.categoriesCount && !__props.useServerProcessing && viewMode.value !== "table") {
              _push2(ssrRenderComponent(_sfc_main$c, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.categoriesCount && __props.useServerProcessing && viewMode.value !== "table") {
              _push2(ssrRenderComponent(_sfc_main$d, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.categoriesCount && viewMode.value !== "table") {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3" data-v-ab435f88${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$f, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountMarketCategories"
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
            if (__props.categoriesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3 mb-3" data-v-ab435f88${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$g, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.categoriesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.categoriesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$5, {
                disabled: !selectedCategories.value.length,
                onChange: handleBulkAction
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$h, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.categoriesCount && viewMode.value !== "table") {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3" data-v-ab435f88${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$i, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredCategories.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$j, { pagination: __props.categories }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(`<div class="mt-2 border border-gray-400 bg-white dark:bg-slate-800" data-v-ab435f88${_scopeId}>`);
              if (__props.categoriesCount) {
                _push2(`<div class="flex justify-between items-center px-3 py-2 border-b border-gray-400 bg-gray-100 dark:bg-slate-900" data-v-ab435f88${_scopeId}><div class="text-xs text-slate-600 dark:text-slate-200" data-v-ab435f88${_scopeId}>${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(selectedCategories.value.length)}</div><label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer" data-v-ab435f88${_scopeId}><span data-v-ab435f88${_scopeId}>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="form-checkbox rounded-sm text-indigo-500 ml-2"${ssrRenderAttr("title", unref(t)("selectAll"))} data-v-ab435f88${_scopeId}></label></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(ssrRenderComponent(unref(draggable), {
                modelValue: localCategoriesTree.value,
                "onUpdate:modelValue": ($event) => localCategoriesTree.value = $event,
                "item-key": "id",
                group: "market-categories",
                handle: ".drag-handle",
                class: "category-tree-root p-1",
                onEnd: handleDragEnd
              }, {
                item: withCtx(({ element }, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(ssrRenderComponent(_sfc_main$1, {
                      category: element,
                      "selected-categories": selectedCategories.value,
                      "is-admin": isAdmin.value,
                      onToggleActivity: toggleActivity,
                      onDelete: confirmDelete,
                      onToggleSelect: toggleSelectCategory,
                      onApprove: approveCategory,
                      onRequestDragEnd: handleDragEnd,
                      onToggleMenu: toggleInMenu
                    }, null, _parent3, _scopeId2));
                  } else {
                    return [
                      createVNode(_sfc_main$1, {
                        category: element,
                        "selected-categories": selectedCategories.value,
                        "is-admin": isAdmin.value,
                        onToggleActivity: toggleActivity,
                        onDelete: confirmDelete,
                        onToggleSelect: toggleSelectCategory,
                        onApprove: approveCategory,
                        onRequestDragEnd: handleDragEnd,
                        onToggleMenu: toggleInMenu
                      }, null, 8, ["category", "selected-categories", "is-admin"])
                    ];
                  }
                }),
                footer: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    if (!localCategoriesTree.value.length) {
                      _push3(`<div class="p-4 text-center text-slate-900 dark:text-slate-100" data-v-ab435f88${_scopeId2}>${ssrInterpolate(unref(t)("noData"))}</div>`);
                    } else {
                      _push3(`<!---->`);
                    }
                  } else {
                    return [
                      !localCategoriesTree.value.length ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "p-4 text-center text-slate-900 dark:text-slate-100"
                      }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(ssrRenderComponent(_sfc_main$2, {
                categories: displayedCategories.value,
                "selected-categories": selectedCategories.value,
                "is-admin": isAdmin.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectCategory,
                onToggleAll: toggleAllCards,
                onApprove: approveCategory,
                onToggleMenu: toggleInMenu
              }, null, _parent2, _scopeId));
            }
            if (__props.categoriesCount && viewMode.value !== "table") {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3" data-v-ab435f88${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$i, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredCategories.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$j, { pagination: __props.categories }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$k, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteCategory,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$a, {
                      href: _ctx.route("admin.marketCategories.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addMarketCategory")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$b, {
                      "setting-key": "adminMarketCategoriesProcessingMode",
                      mode: __props.adminMarketCategoriesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.categoriesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.categoriesCount && !__props.useServerProcessing && viewMode.value !== "table" ? (openBlock(), createBlock(_sfc_main$c, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.categoriesCount && __props.useServerProcessing && viewMode.value !== "table" ? (openBlock(), createBlock(_sfc_main$d, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.categoriesCount && viewMode.value !== "table" ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$f, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountMarketCategories"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$4, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.categoriesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3 mb-3"
                  }, [
                    createVNode(_sfc_main$g, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.categoriesCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$5, {
                      disabled: !selectedCategories.value.length,
                      onChange: handleBulkAction
                    }, null, 8, ["disabled"]),
                    createVNode(_sfc_main$h, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.categoriesCount && viewMode.value !== "table" ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$i, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredCategories.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$j, {
                      key: 1,
                      pagination: __props.categories
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock("div", {
                    key: 5,
                    class: "mt-2 border border-gray-400 bg-white dark:bg-slate-800"
                  }, [
                    __props.categoriesCount ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex justify-between items-center px-3 py-2 border-b border-gray-400 bg-gray-100 dark:bg-slate-900"
                    }, [
                      createVNode("div", { class: "text-xs text-slate-600 dark:text-slate-200" }, toDisplayString(unref(t)("selected")) + ": " + toDisplayString(selectedCategories.value.length), 1),
                      createVNode("label", { class: "flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer" }, [
                        createVNode("span", null, toDisplayString(unref(t)("selectAll")), 1),
                        createVNode("input", {
                          type: "checkbox",
                          class: "form-checkbox rounded-sm text-indigo-500 ml-2",
                          title: unref(t)("selectAll"),
                          onChange: toggleAll
                        }, null, 40, ["title"])
                      ])
                    ])) : createCommentVNode("", true),
                    createVNode(unref(draggable), {
                      modelValue: localCategoriesTree.value,
                      "onUpdate:modelValue": ($event) => localCategoriesTree.value = $event,
                      "item-key": "id",
                      group: "market-categories",
                      handle: ".drag-handle",
                      class: "category-tree-root p-1",
                      onEnd: handleDragEnd
                    }, {
                      item: withCtx(({ element }) => [
                        createVNode(_sfc_main$1, {
                          category: element,
                          "selected-categories": selectedCategories.value,
                          "is-admin": isAdmin.value,
                          onToggleActivity: toggleActivity,
                          onDelete: confirmDelete,
                          onToggleSelect: toggleSelectCategory,
                          onApprove: approveCategory,
                          onRequestDragEnd: handleDragEnd,
                          onToggleMenu: toggleInMenu
                        }, null, 8, ["category", "selected-categories", "is-admin"])
                      ]),
                      footer: withCtx(() => [
                        !localCategoriesTree.value.length ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "p-4 text-center text-slate-900 dark:text-slate-100"
                        }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true)
                      ]),
                      _: 1
                    }, 8, ["modelValue", "onUpdate:modelValue"])
                  ])) : (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    categories: displayedCategories.value,
                    "selected-categories": selectedCategories.value,
                    "is-admin": isAdmin.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectCategory,
                    onToggleAll: toggleAllCards,
                    onApprove: approveCategory,
                    onToggleMenu: toggleInMenu
                  }, null, 8, ["categories", "selected-categories", "is-admin"])),
                  __props.categoriesCount && viewMode.value !== "table" ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$i, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredCategories.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$j, {
                      key: 1,
                      pagination: __props.categories
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$k, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteCategory,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketCategories/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-ab435f88"]]);
export {
  Index as default
};
