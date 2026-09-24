import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, withModifiers, withDirectives, createCommentVNode, vShow, createTextVNode, computed } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent, ssrRenderStyle, ssrRenderClass } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { usePage, router } from "@inertiajs/vue3";
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
import { _ as _sfc_main$7 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$8 } from "./DeleteIconButton-DLv2Mr1x.js";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketAttribute/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>─────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>─────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option disabled>─────────────────</option><option value="groupTitleAsc">${ssrInterpolate(unref(t)("group"))} A→Z</option><option value="groupTitleDesc">${ssrInterpolate(unref(t)("group"))} Z→A</option><option disabled>─────────────────</option><option value="codeAsc">${ssrInterpolate(unref(t)("code"))} A→Z</option><option value="codeDesc">${ssrInterpolate(unref(t)("code"))} Z→A</option><option disabled>─────────────────</option><option value="colorAsc">${ssrInterpolate(unref(t)("typeColor"))} 0→F</option><option value="colorDesc">${ssrInterpolate(unref(t)("typeColor"))} F→0</option><option disabled>─────────────────</option><option value="typeAsc">${ssrInterpolate(unref(t)("type"))} A→Z</option><option value="typeDesc">${ssrInterpolate(unref(t)("type"))} Z→A</option><option value="unitAsc">${ssrInterpolate(unref(t)("unit"))} A→Z</option><option value="unitDesc">${ssrInterpolate(unref(t)("unit"))} Z→A</option><option disabled>─────────────────</option><option value="valuesCountDesc">${ssrInterpolate(unref(t)("values"))} 9→0</option><option value="valuesCountAsc">${ssrInterpolate(unref(t)("values"))} 0→9</option><option disabled>─────────────────</option><option value="requiredDesc">${ssrInterpolate(unref(t)("required"))} ON→OFF</option><option value="requiredAsc">${ssrInterpolate(unref(t)("required"))} OFF→ON</option><option value="required">${ssrInterpolate(unref(t)("required"))}</option><option value="notRequired">${ssrInterpolate(unref(t)("notRequired"))}</option><option disabled>─────────────────</option><option value="filterableDesc">${ssrInterpolate(unref(t)("showFilter"))} ON→OFF</option><option value="filterableAsc">${ssrInterpolate(unref(t)("showFilter"))} OFF→ON</option><option value="filterable">${ssrInterpolate(unref(t)("showFilter"))}</option><option value="notFilterable">${ssrInterpolate(unref(t)("notFilterable"))}</option><option disabled>─────────────────</option><option value="useForVariantsDesc">${ssrInterpolate(unref(t)("variants"))} ON→OFF</option><option value="useForVariantsAsc">${ssrInterpolate(unref(t)("variants"))} OFF→ON</option><option value="useForVariants">${ssrInterpolate(unref(t)("variants"))}</option><option value="notForVariants">${ssrInterpolate(unref(t)("notForVariants"))}</option><option disabled>─────────────────</option><option value="visibleDesc">${ssrInterpolate(unref(t)("show"))} ON→OFF</option><option value="visibleAsc">${ssrInterpolate(unref(t)("show"))} OFF→ON</option><option value="visible">${ssrInterpolate(unref(t)("show"))}</option><option value="hidden">${ssrInterpolate(unref(t)("availabilityUnlisted"))}</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="statusDraft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="statusPublished">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="statusArchived">${ssrInterpolate(unref(t)("statusArchived"))}</option><option disabled>─────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="showFromAtDesc">${ssrInterpolate(unref(t)("showFromAt"))} ↓</option><option value="showFromAtAsc">${ssrInterpolate(unref(t)("showFromAt"))} ↑</option><option value="showToAtDesc">${ssrInterpolate(unref(t)("showToAt"))} ↓</option><option value="showToAtAsc">${ssrInterpolate(unref(t)("showToAt"))} ↑</option><option disabled>─────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>─────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketAttribute/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "AttributeTable",
  __ssrInlineRender: true,
  props: {
    attributes: { type: Array, default: () => [] },
    selectedAttributes: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
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
    const localAttributes = ref([]);
    watch(
      () => props.attributes,
      (newVal) => {
        localAttributes.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits(
        "update-sort-order",
        localAttributes.value.map((attribute) => attribute.id)
      );
    };
    const allSelected = () => {
      return Boolean(
        localAttributes.value.length && localAttributes.value.every(
          (attribute) => props.selectedAttributes.includes(attribute.id)
        )
      );
    };
    const attributeTranslation = (attribute) => (attribute == null ? void 0 : attribute.translation) || {};
    const attributeTitle = (attribute) => {
      var _a;
      return ((_a = attributeTranslation(attribute)) == null ? void 0 : _a.title) || `ID: ${attribute == null ? void 0 : attribute.id}`;
    };
    const attributeShort = (attribute) => {
      var _a;
      return ((_a = attributeTranslation(attribute)) == null ? void 0 : _a.short) || "";
    };
    const groupTranslation = (attribute) => {
      var _a;
      return ((_a = attribute == null ? void 0 : attribute.group) == null ? void 0 : _a.translation) || {};
    };
    const groupTitle = (attribute) => {
      var _a, _b;
      return ((_a = groupTranslation(attribute)) == null ? void 0 : _a.title) || ((_b = attribute == null ? void 0 : attribute.group) == null ? void 0 : _b.code) || "—";
    };
    const ownerTitle = (attribute) => {
      const owner = attribute == null ? void 0 : attribute.owner;
      if (!owner) {
        return t("noData");
      }
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (attribute) => {
      var _a;
      return ((_a = attribute == null ? void 0 : attribute.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const getSafeIcon = (icon) => {
      if (typeof icon !== "string") {
        return null;
      }
      const trimmed = icon.trim();
      if (!trimmed) {
        return null;
      }
      return trimmed.startsWith("<svg") && trimmed.endsWith("</svg>") ? trimmed : null;
    };
    const getStatusLabel = (status) => {
      const map = {
        draft: "statusDraft",
        published: "statusPublished",
        archived: "statusArchived"
      };
      return t(
        map[status] || status || "no"
      );
    };
    const getTypeLabel = (type) => {
      const map = {
        string: "string",
        text: "text",
        integer: "integer",
        decimal: "float",
        boolean: "boolean",
        date: "date",
        datetime: "datetime",
        select: "typeSelect",
        multiselect: "multiselect"
      };
      return t(
        map[type] || type || "noData"
      );
    };
    const booleanBadge = (value, label) => {
      return {
        text: value ? label : "—",
        class: value ? "bg-cyan-100 text-cyan-700 border-cyan-300 dark:bg-cyan-900/40 dark:text-cyan-300" : "bg-slate-100 text-slate-500 border-slate-300 dark:bg-slate-700 dark:text-slate-400"
      };
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
    const formatDate = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(date.getTime())) {
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
    const truncateText = (text, maxLength = 70) => {
      if (text === null || text === void 0 || text === "") {
        return "";
      }
      const value = String(text);
      return value.length > maxLength ? value.slice(0, maxLength).trimEnd() + "…" : value;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedAttributes.length)}</div>`);
      if (localAttributes.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localAttributes.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-1 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("owner"))}</div></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("icon"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("title"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center"${ssrRenderAttr("title", unref(t)("values"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 512 512"><path d="M395.198 256c3.461-10.526 18.796-21.28 36.265-32.425 16.625-10.605 35.467-22.626 50.341-38.862 17.458-19.054 25.944-40.175 25.944-64.567 0-60.562-50.702-88.146-97.81-88.146-42.491 0-76.378 22.016-94.432 50.447-4.654 7.329-2.592 17.036 4.623 21.865l30.328 20.296c7.032 4.706 16.46 3.084 21.63-3.614 8.022-10.394 18.818-18.225 31.667-18.225 19.387 0 26.266 12.901 26.266 23.948 0 36.159-119.437 57.023-119.437 160.024 0 6.654.561 13.014 1.415 19.331 1.076 7.964 7.834 13.928 15.87 13.928H496c8.837 0 16-7.163 16-16v-32c0-8.837-7.163-16-16-16H395.198zM272 416c8.837 0 16 7.163 16 16v32c0 8.837-7.163 16-16 16h-62.399a16 16 0 0 1-13.541-7.478l-45.701-72.615c-2.297-3.352-4.422-6.969-6.195-10.209-1.65 3.244-3.647 6.937-5.874 10.582l-44.712 72.147a15.999 15.999 0 0 1-13.6 7.572H16c-8.837 0-16-7.163-16-16v-32c0-8.837 7.163-16 16-16h26.325l56.552-82.709L46.111 256H16c-8.837 0-16-7.163-16-16v-32c0-8.837 7.163-16 16-16h68.806a16 16 0 0 1 13.645 7.644l39.882 65.126c2.072 3.523 4.053 7.171 5.727 10.37 1.777-3.244 3.92-6.954 6.237-10.537l40.332-65.035a16 16 0 0 1 13.598-7.567H272c8.837 0 16 7.163 16 16v32c0 8.837-7.163 16-16 16h-27.979l-52.69 75.671L249.974 416H272z"></path></svg></div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("type"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("settings"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-1 py-1 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localAttributes.value,
          "onUpdate:modelValue": ($event) => localAttributes.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: attribute }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-1 py-1 text-center cursor-move handle w-px"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-1 py-1 whitespace-nowrap w-px"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${ssrRenderAttr("title", `[${attribute.sort}] / ${formatDate(attribute.published_at)}`)}${_scopeId}>${ssrInterpolate(attribute.id)}</div></td><td class="px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(attribute))}${ssrRenderAttr("title", ownerTitle(attribute))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("owner"))}${_scopeId}></div><div class="text-[10px] font-semibold text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(ownerTitle(attribute))}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${_scopeId}>`);
              if (getSafeIcon(attribute.icon)) {
                _push2(`<div class="w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center"${_scopeId}>${getSafeIcon(attribute.icon) ?? ""}</div>`);
              } else {
                _push2(`<span class="text-slate-400 dark:text-slate-300"${_scopeId}> — </span>`);
              }
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-center space-y-1"${_scopeId}><div class="text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md" style="${ssrRenderStyle({ borderColor: attribute.color || "transparent" })}"${_scopeId}><span style="${ssrRenderStyle({ color: attribute.color || "#666666" })}"${_scopeId}>${ssrInterpolate(truncateText(attributeTitle(attribute)))}</span></div><div class="italic text-center text-xs text-slate-500 dark:text-slate-400"${ssrRenderAttr("title", truncateText(attributeShort(attribute), 80))}${_scopeId}>${ssrInterpolate(attribute.code)}</div><div class="text-center text-xs text-gray-700 dark:text-gray-300"${ssrRenderAttr("title", unref(t)("group"))}${_scopeId}>${ssrInterpolate(truncateText(groupTitle(attribute), 45))}</div></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-300"${_scopeId}>${ssrInterpolate(attribute.values_count ?? 0)}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-center gap-1"${_scopeId}><span class="text-xs px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-500"${_scopeId}>${ssrInterpolate(getTypeLabel(attribute.type))}</span><span class="text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(attribute.unit || "—")}</span></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-center gap-1"${_scopeId}><span class="${ssrRenderClass([booleanBadge(attribute.visible, unref(t)("visibleCard")).class, "text-[10px] px-2 py-0.5 rounded-sm border"])}"${_scopeId}>${ssrInterpolate(booleanBadge(attribute.visible, unref(t)("visibleCard")).text)}</span><span class="${ssrRenderClass([booleanBadge(attribute.filterable, unref(t)("showFilter")).class, "text-[10px] px-2 py-0.5 rounded-sm border"])}"${_scopeId}>${ssrInterpolate(booleanBadge(attribute.filterable, unref(t)("showFilter")).text)}</span><span class="${ssrRenderClass([booleanBadge(attribute.required, unref(t)("required")).class, "text-[10px] px-2 py-0.5 rounded-sm border"])}"${_scopeId}>${ssrInterpolate(booleanBadge(attribute.required, unref(t)("required")).text)}</span></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-center justify-center space-y-1"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(attribute.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", attribute.moderation_note && attribute.moderated_at ? `${attribute.moderation_note} [${formatDate(attribute.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(attribute.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isAdmin: __props.isAdmin,
                status: (attribute == null ? void 0 : attribute.moderation_status) ?? 0,
                initialNote: (attribute == null ? void 0 : attribute.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emits("approve", attribute, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(getStatusLabel(attribute.status))}</div></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: attribute.activity,
                onToggleActivity: ($event) => emits("toggle-activity", attribute)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.marketAttributes.edit", {
                  marketAttribute: attribute.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emits("delete", attribute)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedAttributes.includes(attribute.id)) ? " checked" : ""}${_scopeId}></td></tr>`);
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
                      title: `[${attribute.sort}] / ${formatDate(attribute.published_at)}`
                    }, toDisplayString(attribute.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(attribute),
                        title: ownerTitle(attribute),
                        class: "h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("owner")
                      }, null, 8, ["src", "title", "alt"])
                    ]),
                    createVNode("div", { class: "text-[10px] font-semibold text-center text-slate-700 dark:text-slate-300" }, toDisplayString(ownerTitle(attribute)), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      getSafeIcon(attribute.icon) ? (openBlock(), createBlock("div", {
                        key: 0,
                        innerHTML: getSafeIcon(attribute.icon),
                        class: "w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center"
                      }, null, 8, ["innerHTML"])) : (openBlock(), createBlock("span", {
                        key: 1,
                        class: "text-slate-400 dark:text-slate-300"
                      }, " — "))
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-col items-center space-y-1" }, [
                      createVNode("div", {
                        class: "text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md",
                        style: { borderColor: attribute.color || "transparent" }
                      }, [
                        createVNode("span", {
                          style: { color: attribute.color || "#666666" }
                        }, toDisplayString(truncateText(attributeTitle(attribute))), 5)
                      ], 4),
                      createVNode("div", {
                        class: "italic text-center text-xs text-slate-500 dark:text-slate-400",
                        title: truncateText(attributeShort(attribute), 80)
                      }, toDisplayString(attribute.code), 9, ["title"]),
                      createVNode("div", {
                        class: "text-center text-xs text-gray-700 dark:text-gray-300",
                        title: unref(t)("group")
                      }, toDisplayString(truncateText(groupTitle(attribute), 45)), 9, ["title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-blue-600 dark:text-blue-300" }, toDisplayString(attribute.values_count ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-col items-center gap-1" }, [
                      createVNode("span", { class: "text-xs px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-500" }, toDisplayString(getTypeLabel(attribute.type)), 1),
                      createVNode("span", { class: "text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(attribute.unit || "—"), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-col items-center gap-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border", booleanBadge(attribute.visible, unref(t)("visibleCard")).class]
                      }, toDisplayString(booleanBadge(attribute.visible, unref(t)("visibleCard")).text), 3),
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border", booleanBadge(attribute.filterable, unref(t)("showFilter")).class]
                      }, toDisplayString(booleanBadge(attribute.filterable, unref(t)("showFilter")).text), 3),
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border", booleanBadge(attribute.required, unref(t)("required")).class]
                      }, toDisplayString(booleanBadge(attribute.required, unref(t)("required")).text), 3)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-col items-center justify-center space-y-1" }, [
                      createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                        createVNode("span", {
                          class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(attribute.moderation_status).class],
                          title: attribute.moderation_note && attribute.moderated_at ? `${attribute.moderation_note} [${formatDate(attribute.moderated_at)}]` : null
                        }, toDisplayString(moderationBadge(attribute.moderation_status).text), 11, ["title"]),
                        createVNode(_sfc_main$5, {
                          isAdmin: __props.isAdmin,
                          status: (attribute == null ? void 0 : attribute.moderation_status) ?? 0,
                          initialNote: (attribute == null ? void 0 : attribute.moderation_note) || "",
                          mode: "toggle",
                          onSubmit: ({ status, note }) => emits("approve", attribute, status, note)
                        }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                      ]),
                      createVNode("div", { class: "text-[10px] text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(getStatusLabel(attribute.status)), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-1" }, [
                      createVNode(_sfc_main$6, {
                        isActive: attribute.activity,
                        onToggleActivity: ($event) => emits("toggle-activity", attribute)
                      }, null, 8, ["isActive", "onToggleActivity"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.marketAttributes.edit", {
                          marketAttribute: attribute.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emits("delete", attribute)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 text-center" }, [
                    createVNode("input", {
                      type: "checkbox",
                      checked: __props.selectedAttributes.includes(attribute.id),
                      onChange: ($event) => emits("toggle-select", attribute.id)
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
        _push(`<div class="p-5 text-center text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketAttribute/Table/AttributeTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "AttributeCardGrid",
  __ssrInlineRender: true,
  props: {
    attributes: { type: Array, default: () => [] },
    selectedAttributes: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
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
    const localAttributes = ref([]);
    const openedOwnerBlocks = ref([]);
    watch(
      () => props.attributes,
      (newVal) => {
        localAttributes.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits(
        "update-sort-order",
        localAttributes.value.map((attribute) => attribute.id)
      );
    };
    const allSelected = () => {
      return Boolean(
        localAttributes.value.length && localAttributes.value.every(
          (attribute) => props.selectedAttributes.includes(attribute.id)
        )
      );
    };
    const attributeTranslation = (attribute) => (attribute == null ? void 0 : attribute.translation) || {};
    const attributeTitle = (attribute) => {
      var _a;
      return ((_a = attributeTranslation(attribute)) == null ? void 0 : _a.title) || `ID: ${attribute == null ? void 0 : attribute.id}`;
    };
    const attributeShort = (attribute) => {
      var _a;
      return ((_a = attributeTranslation(attribute)) == null ? void 0 : _a.short) || "";
    };
    const groupTranslation = (attribute) => {
      var _a;
      return ((_a = attribute == null ? void 0 : attribute.group) == null ? void 0 : _a.translation) || {};
    };
    const groupTitle = (attribute) => {
      var _a, _b;
      return ((_a = groupTranslation(attribute)) == null ? void 0 : _a.title) || ((_b = attribute == null ? void 0 : attribute.group) == null ? void 0 : _b.code) || "—";
    };
    const ownerName = (attribute) => {
      var _a;
      return ((_a = attribute == null ? void 0 : attribute.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (attribute) => {
      var _a;
      return ((_a = attribute == null ? void 0 : attribute.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (attribute) => {
      const owner = attribute == null ? void 0 : attribute.owner;
      if (!owner) {
        return t("noData");
      }
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (attribute) => {
      var _a;
      return ((_a = attribute == null ? void 0 : attribute.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const getSafeIcon = (icon) => {
      if (typeof icon !== "string") {
        return null;
      }
      const trimmed = icon.trim();
      if (!trimmed) {
        return null;
      }
      return trimmed.startsWith("<svg") && trimmed.endsWith("</svg>") ? trimmed : null;
    };
    const getStatusLabel = (status) => {
      const map = {
        draft: "statusDraft",
        published: "statusPublished",
        archived: "statusArchived"
      };
      return t(
        map[status] || status || "no"
      );
    };
    const formatDate = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(date.getTime())) {
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
      if (text === null || text === void 0 || text === "") {
        return "";
      }
      const value = String(text);
      return value.length > maxLength ? value.slice(0, maxLength).trimEnd() + "…" : value;
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
    const getTypeLabel = (type) => {
      const map = {
        string: "string",
        text: "text",
        integer: "integer",
        decimal: "float",
        boolean: "boolean",
        date: "date",
        datetime: "datetime",
        select: "typeSelect",
        multiselect: "multiselect"
      };
      return t(
        map[type] || type || "noData"
      );
    };
    const booleanBadge = (value, label) => {
      return {
        text: value ? label : "—",
        class: value ? "bg-cyan-100 text-cyan-700 border-cyan-300 dark:bg-cyan-900/40 dark:text-cyan-300" : "bg-slate-100 text-slate-500 border-slate-300 dark:bg-slate-700 dark:text-slate-400"
      };
    };
    const isOwnerBlockOpen = (attributeId) => openedOwnerBlocks.value.includes(attributeId);
    const toggleOwnerBlock = (attributeId) => {
      if (isOwnerBlockOpen(attributeId)) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
          (id) => id !== attributeId
        );
        return;
      }
      openedOwnerBlocks.value.push(attributeId);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedAttributes.length)}</div>`);
      if (localAttributes.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localAttributes.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localAttributes.value,
          "onUpdate:modelValue": ($event) => localAttributes.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd,
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: attribute }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${attribute.sort}] / ${formatDate(attribute.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(attribute.id)}</div><button type="button" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"${ssrRenderAttr("title", isOwnerBlockOpen(attribute.id) ? unref(t)("hideOwner") : unref(t)("showOwner"))}${_scopeId}><svg class="${ssrRenderClass([{ "rotate-180": isOwnerBlockOpen(attribute.id) }, "w-4 h-4 transition-transform duration-200"])}" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"${_scopeId}></path></svg></button></div><div class="flex items-center space-x-2"${_scopeId}><span class="${ssrRenderClass([moderationBadge(attribute.moderation_status).class, "text-[10px] px-2 py-0.5 rounded-sm border font-semibold"])}"${_scopeId}>${ssrInterpolate(moderationBadge(attribute.moderation_status).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedAttributes.includes(attribute.id)) ? " checked" : ""}${_scopeId}></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-2"${_scopeId}><div style="${ssrRenderStyle(isOwnerBlockOpen(attribute.id) ? null : { display: "none" })}" class="flex flex-col items-center justify-center text-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(attribute))}${ssrRenderAttr("title", ownerTitle(attribute))} class="h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("owner"))}${_scopeId}><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerName(attribute))}${_scopeId}>${ssrInterpolate(ownerName(attribute))}</div>`);
              if (ownerEmail(attribute)) {
                _push2(`<div class="text-[10px] text-slate-500 dark:text-slate-400 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerEmail(attribute))}${_scopeId}>${ssrInterpolate(ownerEmail(attribute))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (attribute.show_from_at) {
                _push2(`<div class="mt-1 text-center text-[10px] text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("show"))}: ${ssrInterpolate(formatDate(attribute.show_from_at))} / ${ssrInterpolate(formatDate(attribute.show_to_at))}</div>`);
              } else {
                _push2(`<div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(formatDate(attribute.published_at))}</div>`);
              }
              _push2(`</div><div class="flex justify-center items-center"${_scopeId}><div class="text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md" style="${ssrRenderStyle({ borderColor: attribute.color || "transparent" })}"${_scopeId}><span style="${ssrRenderStyle({ color: attribute.color || "#666666" })}"${_scopeId}>${ssrInterpolate(truncateText(attributeTitle(attribute)))}</span></div></div><div class="flex justify-center"${_scopeId}>`);
              if (getSafeIcon(attribute.icon)) {
                _push2(`<div class="w-8 h-8 text-slate-700 dark:text-slate-100 flex items-center justify-center"${_scopeId}>${getSafeIcon(attribute.icon) ?? ""}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (attribute.color) {
                _push2(`<div class="flex items-center justify-center gap-1 font-semibold text-xs text-slate-700 dark:text-slate-300"${_scopeId}><span class="inline-block w-4 h-4 rounded-sm border border-slate-400" style="${ssrRenderStyle({ backgroundColor: attribute.color })}"${_scopeId}></span><span${_scopeId}>${ssrInterpolate(attribute.color)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="text-center text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("code"))}: ${ssrInterpolate(truncateText(attribute.code, 90))}</div><div class="flex items-center justify-center gap-2 text-[11px] font-semibold text-slate-600 dark:text-slate-200"${_scopeId}><span class="px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-500"${_scopeId}>${ssrInterpolate(getTypeLabel(attribute.type))}</span><span class="px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-500"${_scopeId}>${ssrInterpolate(attribute.unit || "—")}</span></div><div class="flex items-center justify-center gap-3 text-[11px] font-semibold text-slate-600 dark:text-slate-200"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("values"))}:</span><span class="text-[12px] text-blue-600 dark:text-blue-300"${_scopeId}>${ssrInterpolate(attribute.values_count ?? 0)}</span></div><div class="text-center text-[11px] font-semibold text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("group"))}: ${ssrInterpolate(truncateText(groupTitle(attribute), 80))}</div>`);
              if (attributeShort(attribute)) {
                _push2(`<div class="font-semibold text-[12px] text-center text-cyan-700 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(truncateText(attributeShort(attribute), 120))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="flex flex-wrap justify-center gap-1 font-semibold"${_scopeId}><span class="${ssrRenderClass([booleanBadge(attribute.visible, unref(t)("visibleCard")).class, "text-[10px] px-2 py-0.5 rounded-sm border"])}"${_scopeId}>${ssrInterpolate(booleanBadge(attribute.visible, unref(t)("visibleCard")).text)}</span><span class="${ssrRenderClass([booleanBadge(attribute.filterable, unref(t)("showFilter")).class, "text-[10px] px-2 py-0.5 rounded-sm border"])}"${_scopeId}>${ssrInterpolate(booleanBadge(attribute.filterable, unref(t)("showFilter")).text)}</span><span class="${ssrRenderClass([booleanBadge(attribute.required, unref(t)("required")).class, "text-[10px] px-2 py-0.5 rounded-sm border"])}"${_scopeId}>${ssrInterpolate(booleanBadge(attribute.required, unref(t)("required")).text)}</span></div><div class="font-semibold text-center text-[11px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("status"))}: </span> ${ssrInterpolate(getStatusLabel(attribute.status))}</div><div class="flex justify-center space-x-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(attribute.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", attribute.moderation_note && attribute.moderated_at ? `${attribute.moderation_note} [${formatDate(attribute.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(attribute.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isAdmin: __props.isAdmin,
                status: (attribute == null ? void 0 : attribute.moderation_status) ?? 0,
                initialNote: (attribute == null ? void 0 : attribute.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emits("approve", attribute, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: attribute.activity,
                onToggleActivity: ($event) => emits("toggle-activity", attribute),
                title: attribute.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.marketAttributes.edit", {
                  marketAttribute: attribute.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emits("delete", attribute)
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
                        title: `[${attribute.sort}] / ${formatDate(attribute.published_at)}`
                      }, " ID: " + toDisplayString(attribute.id), 9, ["title"]),
                      createVNode("button", {
                        type: "button",
                        class: "text-slate-400 hover:text-blue-600 dark:hover:text-blue-300",
                        title: isOwnerBlockOpen(attribute.id) ? unref(t)("hideOwner") : unref(t)("showOwner"),
                        onClick: withModifiers(($event) => toggleOwnerBlock(attribute.id), ["prevent"])
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: ["w-4 h-4 transition-transform duration-200", { "rotate-180": isOwnerBlockOpen(attribute.id) }],
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
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border font-semibold", moderationBadge(attribute.moderation_status).class]
                      }, toDisplayString(moderationBadge(attribute.moderation_status).text), 3),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedAttributes.includes(attribute.id),
                        onChange: ($event) => emits("toggle-select", attribute.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-2" }, [
                    withDirectives(createVNode("div", { class: "flex flex-col items-center justify-center text-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(attribute),
                        title: ownerTitle(attribute),
                        class: "h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("owner")
                      }, null, 8, ["src", "title", "alt"]),
                      createVNode("div", {
                        class: "mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1",
                        title: ownerName(attribute)
                      }, toDisplayString(ownerName(attribute)), 9, ["title"]),
                      ownerEmail(attribute) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] text-slate-500 dark:text-slate-400 leading-tight line-clamp-1",
                        title: ownerEmail(attribute)
                      }, toDisplayString(ownerEmail(attribute)), 9, ["title"])) : createCommentVNode("", true),
                      attribute.show_from_at ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "mt-1 text-center text-[10px] text-gray-700 dark:text-gray-300"
                      }, toDisplayString(unref(t)("show")) + ": " + toDisplayString(formatDate(attribute.show_from_at)) + " / " + toDisplayString(formatDate(attribute.show_to_at)), 1)) : (openBlock(), createBlock("div", {
                        key: 2,
                        class: "text-center text-[10px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(formatDate(attribute.published_at)), 1))
                    ], 512), [
                      [vShow, isOwnerBlockOpen(attribute.id)]
                    ]),
                    createVNode("div", { class: "flex justify-center items-center" }, [
                      createVNode("div", {
                        class: "text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md",
                        style: { borderColor: attribute.color || "transparent" }
                      }, [
                        createVNode("span", {
                          style: { color: attribute.color || "#666666" }
                        }, toDisplayString(truncateText(attributeTitle(attribute))), 5)
                      ], 4)
                    ]),
                    createVNode("div", { class: "flex justify-center" }, [
                      getSafeIcon(attribute.icon) ? (openBlock(), createBlock("div", {
                        key: 0,
                        innerHTML: getSafeIcon(attribute.icon),
                        class: "w-8 h-8 text-slate-700 dark:text-slate-100 flex items-center justify-center"
                      }, null, 8, ["innerHTML"])) : createCommentVNode("", true)
                    ]),
                    attribute.color ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex items-center justify-center gap-1 font-semibold text-xs text-slate-700 dark:text-slate-300"
                    }, [
                      createVNode("span", {
                        class: "inline-block w-4 h-4 rounded-sm border border-slate-400",
                        style: { backgroundColor: attribute.color }
                      }, null, 4),
                      createVNode("span", null, toDisplayString(attribute.color), 1)
                    ])) : createCommentVNode("", true),
                    createVNode("div", { class: "text-center text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(unref(t)("code")) + ": " + toDisplayString(truncateText(attribute.code, 90)), 1),
                    createVNode("div", { class: "flex items-center justify-center gap-2 text-[11px] font-semibold text-slate-600 dark:text-slate-200" }, [
                      createVNode("span", { class: "px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-500" }, toDisplayString(getTypeLabel(attribute.type)), 1),
                      createVNode("span", { class: "px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-500" }, toDisplayString(attribute.unit || "—"), 1)
                    ]),
                    createVNode("div", { class: "flex items-center justify-center gap-3 text-[11px] font-semibold text-slate-600 dark:text-slate-200" }, [
                      createVNode("span", null, toDisplayString(unref(t)("values")) + ":", 1),
                      createVNode("span", { class: "text-[12px] text-blue-600 dark:text-blue-300" }, toDisplayString(attribute.values_count ?? 0), 1)
                    ]),
                    createVNode("div", { class: "text-center text-[11px] font-semibold text-gray-700 dark:text-gray-300" }, toDisplayString(unref(t)("group")) + ": " + toDisplayString(truncateText(groupTitle(attribute), 80)), 1),
                    attributeShort(attribute) ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "font-semibold text-[12px] text-center text-cyan-700 dark:text-cyan-300"
                    }, toDisplayString(truncateText(attributeShort(attribute), 120)), 1)) : createCommentVNode("", true),
                    createVNode("div", { class: "flex flex-wrap justify-center gap-1 font-semibold" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border", booleanBadge(attribute.visible, unref(t)("visibleCard")).class]
                      }, toDisplayString(booleanBadge(attribute.visible, unref(t)("visibleCard")).text), 3),
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border", booleanBadge(attribute.filterable, unref(t)("showFilter")).class]
                      }, toDisplayString(booleanBadge(attribute.filterable, unref(t)("showFilter")).text), 3),
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border", booleanBadge(attribute.required, unref(t)("required")).class]
                      }, toDisplayString(booleanBadge(attribute.required, unref(t)("required")).text), 3)
                    ]),
                    createVNode("div", { class: "font-semibold text-center text-[11px] text-fuchsia-700 dark:text-fuchsia-300" }, [
                      createVNode("span", null, toDisplayString(unref(t)("status")) + ": ", 1),
                      createTextVNode(" " + toDisplayString(getStatusLabel(attribute.status)), 1)
                    ]),
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(attribute.moderation_status).class],
                        title: attribute.moderation_note && attribute.moderated_at ? `${attribute.moderation_note} [${formatDate(attribute.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(attribute.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$5, {
                        isAdmin: __props.isAdmin,
                        status: (attribute == null ? void 0 : attribute.moderation_status) ?? 0,
                        initialNote: (attribute == null ? void 0 : attribute.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => emits("approve", attribute, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$6, {
                        isActive: attribute.activity,
                        onToggleActivity: ($event) => emits("toggle-activity", attribute),
                        title: attribute.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.marketAttributes.edit", {
                          marketAttribute: attribute.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emits("delete", attribute)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketAttribute/View/AttributeCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminMarketAttributesProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminMarketAttributesPerPage: { type: Number, default: 10 },
    adminMarketAttributesDefaultSort: { type: String, default: "idDesc" },
    attributes: { type: [Array, Object], default: () => [] },
    attributesCount: { type: Number, default: 0 },
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
    const getAttributeTranslation = (attribute) => (attribute == null ? void 0 : attribute.translation) || {};
    const getAttributeTranslationTitle = (attribute) => {
      var _a;
      return ((_a = getAttributeTranslation(attribute)) == null ? void 0 : _a.title) || "";
    };
    const getAttributeTitle = (attribute) => getAttributeTranslationTitle(attribute) || `ID: ${attribute == null ? void 0 : attribute.id}`;
    const getAttributeSubtitle = (attribute) => {
      var _a;
      return ((_a = getAttributeTranslation(attribute)) == null ? void 0 : _a.subtitle) || "";
    };
    const getAttributeShort = (attribute) => {
      var _a;
      return ((_a = getAttributeTranslation(attribute)) == null ? void 0 : _a.short) || "";
    };
    const getAttributeDescription = (attribute) => {
      var _a;
      return ((_a = getAttributeTranslation(attribute)) == null ? void 0 : _a.description) || "";
    };
    const getGroupTranslation = (attribute) => {
      var _a;
      return ((_a = attribute == null ? void 0 : attribute.group) == null ? void 0 : _a.translation) || {};
    };
    const getGroupTitle = (attribute) => {
      var _a;
      return ((_a = getGroupTranslation(attribute)) == null ? void 0 : _a.title) || "";
    };
    const getGroupSubtitle = (attribute) => {
      var _a;
      return ((_a = getGroupTranslation(attribute)) == null ? void 0 : _a.subtitle) || "";
    };
    const getGroupShort = (attribute) => {
      var _a;
      return ((_a = getGroupTranslation(attribute)) == null ? void 0 : _a.short) || "";
    };
    const getOwnerName = (attribute) => {
      var _a;
      return ((_a = attribute == null ? void 0 : attribute.owner) == null ? void 0 : _a.name) || "";
    };
    const getOwnerEmail = (attribute) => {
      var _a;
      return ((_a = attribute == null ? void 0 : attribute.owner) == null ? void 0 : _a.email) || "";
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
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_market_attributes") || "cards"
    );
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_market_attributes", value);
    });
    const itemsPerPage = ref(props.adminMarketAttributesPerPage || 10);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountMarketAttributes"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} характеристик на странице.`),
          onError: (errors) => toast.error(
            errors.value || "Ошибка обновления кол-ва характеристик."
          )
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminMarketAttributesDefaultSort || "idDesc"
    );
    watch(sortParam, (newVal) => {
      router.put(
        route("admin.settings.updateAdminSortMarketAttributes"),
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
                    new URLSearchParams(window.location.search)
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
            toast.info("Сортировка характеристик успешно изменена.");
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления сортировки характеристик."
            );
          }
        }
      );
    });
    const localAttributes = ref([]);
    const attributesList = computed(() => {
      var _a, _b, _c, _d;
      if (Array.isArray(props.attributes)) return props.attributes;
      if (Array.isArray((_a = props.attributes) == null ? void 0 : _a.data)) return props.attributes.data;
      if (Array.isArray((_c = (_b = props.attributes) == null ? void 0 : _b.data) == null ? void 0 : _c.data)) return props.attributes.data.data;
      if (Array.isArray((_d = props.attributes) == null ? void 0 : _d.resource)) return props.attributes.resource;
      return [];
    });
    watch(
      attributesList,
      (newVal) => {
        localAttributes.value = JSON.parse(
          JSON.stringify(newVal || [])
        );
      },
      { immediate: true, deep: true }
    );
    const showConfirmDeleteModal = ref(false);
    const attributeToDeleteId = ref(null);
    const attributeToDeleteTitle = ref("");
    const confirmDelete = (attributeOrId, title = null) => {
      if (typeof attributeOrId === "object") {
        attributeToDeleteId.value = attributeOrId.id;
        attributeToDeleteTitle.value = title || getAttributeTitle(attributeOrId);
      } else {
        attributeToDeleteId.value = attributeOrId;
        attributeToDeleteTitle.value = title || `ID: ${attributeOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      attributeToDeleteId.value = null;
      attributeToDeleteTitle.value = "";
    };
    const deleteAttribute = () => {
      if (attributeToDeleteId.value === null) return;
      const idToDelete = attributeToDeleteId.value;
      const titleToDelete = attributeToDeleteTitle.value;
      router.delete(
        route("admin.marketAttributes.destroy", {
          marketAttribute: idToDelete
        }),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Характеристика "${titleToDelete || "ID: " + idToDelete}" удалена.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0];
            const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
            toast.error(
              `${errorMsg} (Характеристика: ${titleToDelete || "ID: " + idToDelete})`
            );
          },
          onFinish: () => closeModal()
        }
      );
    };
    const patchLocalAttribute = (attributeId, callback) => {
      const index = localAttributes.value.findIndex(
        (attribute) => attribute.id === attributeId
      );
      if (index !== -1) {
        callback(localAttributes.value[index]);
      }
    };
    const toggleActivity = (attribute) => {
      const newActivity = !attribute.activity;
      const title = getAttributeTitle(attribute);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route("admin.actions.marketAttributes.updateActivity", {
          marketAttribute: attribute.id
        }),
        { activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalAttribute(attribute.id, (node) => {
              node.activity = newActivity;
            });
            toast.success(
              `Характеристика "${title}" ${actionText}.`
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
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringAsc = (getter) => (a, b) => normalize(getter(a)).localeCompare(
      normalize(getter(b)),
      locale.value
    ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringDesc = (getter) => (a, b) => normalize(getter(b)).localeCompare(
      normalize(getter(a)),
      locale.value
    ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortAttributes = (attributes) => {
      const list = (attributes || []).slice();
      if (sortParam.value === "activity") {
        return list.filter((attribute) => !!attribute.activity).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "inactive") {
        return list.filter((attribute) => !attribute.activity).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "required") {
        return list.filter((attribute) => !!attribute.required).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "notRequired") {
        return list.filter((attribute) => !attribute.required).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "filterable") {
        return list.filter((attribute) => !!attribute.filterable).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "notFilterable") {
        return list.filter((attribute) => !attribute.filterable).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "useForVariants") {
        return list.filter((attribute) => !!attribute.use_for_variants).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "notForVariants") {
        return list.filter((attribute) => !attribute.use_for_variants).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "visible") {
        return list.filter((attribute) => !!attribute.visible).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "hidden") {
        return list.filter((attribute) => !attribute.visible).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "statusDraft") {
        return list.filter((attribute) => (attribute == null ? void 0 : attribute.status) === "draft").sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "statusPublished") {
        return list.filter((attribute) => (attribute == null ? void 0 : attribute.status) === "published").sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "statusArchived") {
        return list.filter((attribute) => (attribute == null ? void 0 : attribute.status) === "archived").sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "moderationPending") {
        return list.filter(
          (attribute) => moderationNum(attribute == null ? void 0 : attribute.moderation_status) === 0
        ).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "moderationApproved") {
        return list.filter(
          (attribute) => moderationNum(attribute == null ? void 0 : attribute.moderation_status) === 1
        ).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      if (sortParam.value === "moderationRejected") {
        return list.filter(
          (attribute) => moderationNum(attribute == null ? void 0 : attribute.moderation_status) === 2
        ).sort((a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id));
      }
      const sortMap = {
        idAsc: (a, b) => safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        idDesc: (a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        /**
         * Для сортировки используем raw translation title,
         * а не display fallback "ID: ...".
         */
        titleAsc: byStringAsc(
          getAttributeTranslationTitle
        ),
        titleDesc: byStringDesc(
          getAttributeTranslationTitle
        ),
        groupTitleAsc: byStringAsc(
          getGroupTitle
        ),
        groupTitleDesc: byStringDesc(
          getGroupTitle
        ),
        codeAsc: byStringAsc(
          (attribute) => attribute == null ? void 0 : attribute.code
        ),
        codeDesc: byStringDesc(
          (attribute) => attribute == null ? void 0 : attribute.code
        ),
        colorAsc: byStringAsc(
          (attribute) => attribute == null ? void 0 : attribute.color
        ),
        colorDesc: byStringDesc(
          (attribute) => attribute == null ? void 0 : attribute.color
        ),
        typeAsc: byStringAsc(
          (attribute) => attribute == null ? void 0 : attribute.type
        ),
        typeDesc: byStringDesc(
          (attribute) => attribute == null ? void 0 : attribute.type
        ),
        unitAsc: byStringAsc(
          (attribute) => attribute == null ? void 0 : attribute.unit
        ),
        unitDesc: byStringDesc(
          (attribute) => attribute == null ? void 0 : attribute.unit
        ),
        valuesCountAsc: byNumberAsc("values_count"),
        valuesCountDesc: byNumberDesc("values_count"),
        requiredAsc: byNumberAsc("required"),
        requiredDesc: byNumberDesc("required"),
        filterableAsc: byNumberAsc("filterable"),
        filterableDesc: byNumberDesc("filterable"),
        useForVariantsAsc: byNumberAsc("use_for_variants"),
        useForVariantsDesc: byNumberDesc("use_for_variants"),
        visibleAsc: byNumberAsc("visible"),
        visibleDesc: byNumberDesc("visible"),
        statusAsc: byStringAsc(
          (attribute) => attribute == null ? void 0 : attribute.status
        ),
        statusDesc: byStringDesc(
          (attribute) => attribute == null ? void 0 : attribute.status
        ),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        moderationStatusAsc: (a, b) => moderationNum(a == null ? void 0 : a.moderation_status) - moderationNum(b == null ? void 0 : b.moderation_status) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        moderationStatusDesc: (a, b) => moderationNum(b == null ? void 0 : b.moderation_status) - moderationNum(a == null ? void 0 : a.moderation_status) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
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
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredAttributes = computed(() => {
      let filtered = localAttributes.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortAttributes(filtered);
      }
      filtered = filtered.filter((attribute) => {
        const values = [
          attribute == null ? void 0 : attribute.code,
          attribute == null ? void 0 : attribute.icon,
          attribute == null ? void 0 : attribute.color,
          attribute == null ? void 0 : attribute.type,
          attribute == null ? void 0 : attribute.unit,
          attribute == null ? void 0 : attribute.status,
          attribute == null ? void 0 : attribute.moderation_note,
          getAttributeTranslationTitle(attribute),
          getAttributeSubtitle(attribute),
          getAttributeShort(attribute),
          getAttributeDescription(attribute),
          getGroupTitle(attribute),
          getGroupSubtitle(attribute),
          getGroupShort(attribute),
          getOwnerName(attribute),
          getOwnerEmail(attribute)
        ];
        return values.some(
          (value) => normalize(value).includes(query)
        );
      });
      return sortAttributes(filtered);
    });
    const paginatedAttributes = computed(() => {
      const perPage = Number(
        itemsPerPage.value || 10
      );
      const start = (currentPage.value - 1) * perPage;
      return filteredAttributes.value.slice(
        start,
        start + perPage
      );
    });
    const displayedAttributes = computed(() => {
      return props.useServerProcessing ? attributesList.value : paginatedAttributes.value;
    });
    watch(
      [itemsPerPage, searchQuery],
      () => {
        currentPage.value = 1;
      }
    );
    const selectedAttributes = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = Boolean(
        (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false
      );
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedAttributes.value.map(
        (attribute) => attribute.id
      );
      if (checked) {
        selectedAttributes.value = [
          .../* @__PURE__ */ new Set([
            ...selectedAttributes.value,
            ...ids
          ])
        ];
      } else {
        selectedAttributes.value = selectedAttributes.value.filter(
          (id) => !ids.includes(id)
        );
      }
    };
    const toggleSelectAttribute = (attributeId) => {
      const index = selectedAttributes.value.indexOf(
        attributeId
      );
      if (index > -1) {
        selectedAttributes.value.splice(
          index,
          1
        );
      } else {
        selectedAttributes.value.push(
          attributeId
        );
      }
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedAttributes.value.length) {
        toast.warning(
          "Выберите характеристики для активации/деактивации."
        );
        return;
      }
      const idsToUpdate = [
        ...selectedAttributes.value
      ];
      router.put(
        route(
          "admin.actions.marketAttributes.bulkUpdateActivity"
        ),
        {
          ids: idsToUpdate,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localAttributes.value = localAttributes.value.map(
              (attribute) => {
                return idsToUpdate.includes(
                  attribute.id
                ) ? {
                  ...attribute,
                  activity: newActivity
                } : attribute;
              }
            );
            selectedAttributes.value = [];
            toast.success(
              "Активность характеристик массово обновлена."
            );
          },
          onError: (errors) => {
            const msg = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности.";
            toast.error(msg);
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedAttributes.value.length) {
        toast.warning(
          "Выберите хотя бы одну характеристику для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные характеристики?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.marketAttributes.bulkDestroy"
        ),
        {
          data: {
            ids: selectedAttributes.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedAttributes.value = [];
            toast.success(
              "Массовое удаление характеристик успешно завершено."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0];
            toast.error(
              errors[errorKey] || "Произошла ошибка при удалении характеристик."
            );
          }
        }
      );
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") {
        toggleAll({
          target: {
            checked: true
          }
        });
      } else if (action === "deselectAll") {
        toggleAll({
          target: {
            checked: false
          }
        });
      } else if (action === "activate") {
        bulkToggleActivity(true);
      } else if (action === "deactivate") {
        bulkToggleActivity(false);
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const approveAttribute = (attribute, status = 1, note = "") => {
      if (!(attribute == null ? void 0 : attribute.id)) return;
      router.put(
        route(
          "admin.actions.marketAttributes.approve",
          {
            marketAttribute: attribute.id
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
            patchLocalAttribute(
              attribute.id,
              (node) => {
                node.moderation_status = status;
                node.is_approved = status === 1;
                node.moderation_note = note;
              }
            );
            toast.success(
              status === 1 ? "Характеристика одобрена." : "Характеристика отклонена."
            );
          },
          onError: () => toast.error(
            "Ошибка модерации характеристики."
          )
        }
      );
    };
    const handleSortOrderUpdate = (newOrderIds) => {
      const items = newOrderIds.map(
        (id, index) => ({
          id,
          sort: index
        })
      );
      if (!items.length) return;
      router.put(
        route(
          "admin.actions.marketAttributes.updateSortBulk"
        ),
        { items },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.success(
            "Сортировка характеристик обновлена."
          ),
          onError: (errors) => {
            console.error(
              "Ошибка сортировки характеристик:",
              errors
            );
            toast.error(
              errors.message || "Ошибка обновления сортировки."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("marketAttributes")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("marketAttributes"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("marketAttributes")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("marketAttributes")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              href: _ctx.route("admin.marketAttributes.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addMarketAttribute"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addMarketAttribute")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              "setting-key": "adminMarketAttributesProcessingMode",
              mode: __props.adminMarketAttributesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.attributesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.attributesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.attributesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.attributesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountMarketAttributes"
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
            if (__props.attributesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$f, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.attributesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.attributesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              if (__props.attributesCount) {
                _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              _push2(ssrRenderComponent(_sfc_main$g, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.attributesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredAttributes.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.attributes }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                attributes: displayedAttributes.value,
                "selected-attributes": selectedAttributes.value,
                "is-admin": isAdmin.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectAttribute,
                onToggleAll: toggleAll,
                onApprove: approveAttribute
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                attributes: displayedAttributes.value,
                "selected-attributes": selectedAttributes.value,
                "is-admin": isAdmin.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectAttribute,
                onToggleAll: toggleAll,
                onApprove: approveAttribute
              }, null, _parent2, _scopeId));
            }
            if (__props.attributesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredAttributes.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.attributes }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$j, {
              show: showConfirmDeleteModal.value,
              onClose: closeModal,
              onCancel: closeModal,
              onConfirm: deleteAttribute,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$9, {
                      href: _ctx.route("admin.marketAttributes.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addMarketAttribute")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$a, {
                      "setting-key": "adminMarketAttributesProcessingMode",
                      mode: __props.adminMarketAttributesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.attributesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.attributesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.attributesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.attributesCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$d, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$e, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountMarketAttributes"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.attributesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$f, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.attributesCount), 1)
                      ]),
                      _: 1
                    }),
                    __props.attributesCount ? (openBlock(), createBlock(_sfc_main$4, {
                      key: 0,
                      onChange: handleBulkAction
                    })) : createCommentVNode("", true),
                    createVNode(_sfc_main$g, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.attributesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredAttributes.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.attributes
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    attributes: displayedAttributes.value,
                    "selected-attributes": selectedAttributes.value,
                    "is-admin": isAdmin.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectAttribute,
                    onToggleAll: toggleAll,
                    onApprove: approveAttribute
                  }, null, 8, ["attributes", "selected-attributes", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    attributes: displayedAttributes.value,
                    "selected-attributes": selectedAttributes.value,
                    "is-admin": isAdmin.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectAttribute,
                    onToggleAll: toggleAll,
                    onApprove: approveAttribute
                  }, null, 8, ["attributes", "selected-attributes", "is-admin"])),
                  __props.attributesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredAttributes.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.attributes
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$j, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteAttribute,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketAttributes/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
