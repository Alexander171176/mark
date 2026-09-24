import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, withModifiers, withDirectives, vShow, createTextVNode, computed } from "vue";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketAttributeValue/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>─────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>─────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option disabled>─────────────────</option><option value="attributeTitleAsc">${ssrInterpolate(unref(t)("attribute"))} A→Z</option><option value="attributeTitleDesc">${ssrInterpolate(unref(t)("attribute"))} Z→A</option><option disabled>─────────────────</option><option value="codeAsc">${ssrInterpolate(unref(t)("code"))} A→Z</option><option value="codeDesc">${ssrInterpolate(unref(t)("code"))} Z→A</option><option disabled>─────────────────</option><option value="colorAsc">${ssrInterpolate(unref(t)("typeColor"))} 0→F</option><option value="colorDesc">${ssrInterpolate(unref(t)("typeColor"))} F→0</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="statusDraft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="statusPublished">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="statusArchived">${ssrInterpolate(unref(t)("statusArchived"))}</option><option disabled>─────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="showFromAtDesc">${ssrInterpolate(unref(t)("showFromAt"))} ↓</option><option value="showFromAtAsc">${ssrInterpolate(unref(t)("showFromAt"))} ↑</option><option value="showToAtDesc">${ssrInterpolate(unref(t)("showToAt"))} ↓</option><option value="showToAtAsc">${ssrInterpolate(unref(t)("showToAt"))} ↑</option><option disabled>─────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketAttributeValue/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "AttributeValueTable",
  __ssrInlineRender: true,
  props: {
    values: { type: Array, default: () => [] },
    selectedValues: { type: Array, default: () => [] },
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
    const localValues = ref([]);
    watch(
      () => props.values,
      (newVal) => {
        localValues.value = JSON.parse(
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
        localValues.value.map(
          (value) => value.id
        )
      );
    };
    const allSelected = () => {
      return localValues.value.length > 0 && localValues.value.every(
        (value) => props.selectedValues.includes(
          value.id
        )
      );
    };
    const valueTranslation = (value) => {
      return (value == null ? void 0 : value.translation) || {};
    };
    const valueTitle = (value) => {
      var _a;
      return ((_a = valueTranslation(value)) == null ? void 0 : _a.title) || `ID: ${value == null ? void 0 : value.id}`;
    };
    const valueShort = (value) => {
      var _a;
      return ((_a = valueTranslation(value)) == null ? void 0 : _a.short) || "";
    };
    const attributeTranslation = (value) => {
      var _a;
      return ((_a = value == null ? void 0 : value.attribute) == null ? void 0 : _a.translation) || {};
    };
    const attributeTitle = (value) => {
      var _a, _b;
      return ((_a = attributeTranslation(value)) == null ? void 0 : _a.title) || ((_b = value == null ? void 0 : value.attribute) == null ? void 0 : _b.code) || "—";
    };
    const attributeInfo = (value) => {
      const attribute = value == null ? void 0 : value.attribute;
      if (!attribute) {
        return "—";
      }
      return [
        attribute.code,
        attribute.type,
        attribute.unit
      ].filter(Boolean).join(" / ") || "—";
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
    const moderationBadge = (status) => {
      const moderationStatus = Number(status ?? 0);
      if (moderationStatus === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (moderationStatus === 2) {
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
    const truncateText = (text, maxLength = 70) => {
      if (text === null || text === void 0 || text === "") {
        return "";
      }
      const value = String(text);
      return value.length > maxLength ? `${value.slice(0, maxLength).trimEnd()}…` : value;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedValues.length)}</div>`);
      if (localValues.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localValues.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-1 py-3 w-px"></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("attribute"))}</div></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("icon"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("value"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("code"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-1 py-1 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localValues.value,
          "onUpdate:modelValue": ($event) => localValues.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: value }, _push2, _parent2, _scopeId) => {
            var _a, _b;
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-1 py-1 text-center cursor-move handle w-px"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-1 py-1 whitespace-nowrap w-px"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${ssrRenderAttr("title", `[${value.sort}] / ${formatDate(value.published_at)}`)}${_scopeId}>${ssrInterpolate(value.id)}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-center gap-1"${_scopeId}><div class="text-xs text-center text-gray-700 dark:text-gray-200"${_scopeId}>${ssrInterpolate(truncateText(attributeTitle(value), 55))}</div><div class="text-[10px] text-slate-500 dark:text-slate-300"${ssrRenderAttr("title", attributeInfo(value))}${_scopeId}>${ssrInterpolate(attributeInfo(value))}</div>`);
              if ((_a = value == null ? void 0 : value.attribute) == null ? void 0 : _a.type) {
                _push2(`<div class="text-[10px] px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-500"${_scopeId}>${ssrInterpolate(getTypeLabel(value.attribute.type))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${_scopeId}>`);
              if (getSafeIcon(value.icon)) {
                _push2(`<div class="w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center"${_scopeId}>${getSafeIcon(value.icon) ?? ""}</div>`);
              } else {
                _push2(`<span class="text-slate-400 dark:text-slate-300"${_scopeId}> — </span>`);
              }
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-center space-y-1"${_scopeId}><div class="text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md" style="${ssrRenderStyle({ borderColor: value.color || "transparent" })}"${_scopeId}><span style="${ssrRenderStyle({ color: value.color || "#666666" })}"${_scopeId}>${ssrInterpolate(truncateText(valueTitle(value)))}</span></div>`);
              if (valueShort(value)) {
                _push2(`<div class="italic text-center text-xs text-slate-500 dark:text-slate-400"${ssrRenderAttr("title", valueShort(value))}${_scopeId}>${ssrInterpolate(truncateText(valueShort(value), 80))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(value.code || "—")}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-center justify-center space-y-1"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(value.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", value.moderation_note && value.moderated_at ? `${value.moderation_note} [${formatDate(value.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(value.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isAdmin: __props.isAdmin,
                status: (value == null ? void 0 : value.moderation_status) ?? 0,
                initialNote: (value == null ? void 0 : value.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emits("approve", value, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(getStatusLabel(value.status))}</div></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: value.activity,
                onToggleActivity: ($event) => emits("toggle-activity", value)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.marketAttributeValues.edit", {
                  marketAttributeValue: value.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emits("delete", value)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedValues.includes(value.id)) ? " checked" : ""}${_scopeId}></td></tr>`);
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
                      title: `[${value.sort}] / ${formatDate(value.published_at)}`
                    }, toDisplayString(value.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-col items-center gap-1" }, [
                      createVNode("div", { class: "text-xs text-center text-gray-700 dark:text-gray-200" }, toDisplayString(truncateText(attributeTitle(value), 55)), 1),
                      createVNode("div", {
                        class: "text-[10px] text-slate-500 dark:text-slate-300",
                        title: attributeInfo(value)
                      }, toDisplayString(attributeInfo(value)), 9, ["title"]),
                      ((_b = value == null ? void 0 : value.attribute) == null ? void 0 : _b.type) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-500"
                      }, toDisplayString(getTypeLabel(value.attribute.type)), 1)) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      getSafeIcon(value.icon) ? (openBlock(), createBlock("div", {
                        key: 0,
                        innerHTML: getSafeIcon(value.icon),
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
                        style: { borderColor: value.color || "transparent" }
                      }, [
                        createVNode("span", {
                          style: { color: value.color || "#666666" }
                        }, toDisplayString(truncateText(valueTitle(value))), 5)
                      ], 4),
                      valueShort(value) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "italic text-center text-xs text-slate-500 dark:text-slate-400",
                        title: valueShort(value)
                      }, toDisplayString(truncateText(valueShort(value), 80)), 9, ["title"])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs text-slate-700 dark:text-slate-200" }, toDisplayString(value.code || "—"), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-col items-center justify-center space-y-1" }, [
                      createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                        createVNode("span", {
                          class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(value.moderation_status).class],
                          title: value.moderation_note && value.moderated_at ? `${value.moderation_note} [${formatDate(value.moderated_at)}]` : null
                        }, toDisplayString(moderationBadge(value.moderation_status).text), 11, ["title"]),
                        createVNode(_sfc_main$5, {
                          isAdmin: __props.isAdmin,
                          status: (value == null ? void 0 : value.moderation_status) ?? 0,
                          initialNote: (value == null ? void 0 : value.moderation_note) || "",
                          mode: "toggle",
                          onSubmit: ({ status, note }) => emits("approve", value, status, note)
                        }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                      ]),
                      createVNode("div", { class: "text-[10px] text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(getStatusLabel(value.status)), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-1" }, [
                      createVNode(_sfc_main$6, {
                        isActive: value.activity,
                        onToggleActivity: ($event) => emits("toggle-activity", value)
                      }, null, 8, ["isActive", "onToggleActivity"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.marketAttributeValues.edit", {
                          marketAttributeValue: value.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emits("delete", value)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 text-center" }, [
                    createVNode("input", {
                      type: "checkbox",
                      checked: __props.selectedValues.includes(value.id),
                      onChange: ($event) => emits("toggle-select", value.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketAttributeValue/Table/AttributeValueTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "AttributeValueCardGrid",
  __ssrInlineRender: true,
  props: {
    values: { type: Array, default: () => [] },
    selectedValues: { type: Array, default: () => [] },
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
    const localValues = ref([]);
    const openedAttributeBlocks = ref([]);
    watch(
      () => props.values,
      (newVal) => {
        localValues.value = JSON.parse(
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
        localValues.value.map(
          (value) => value.id
        )
      );
    };
    const allSelected = () => {
      return localValues.value.length > 0 && localValues.value.every(
        (value) => props.selectedValues.includes(
          value.id
        )
      );
    };
    const valueTranslation = (value) => {
      return (value == null ? void 0 : value.translation) || {};
    };
    const valueTitle = (value) => {
      var _a;
      return ((_a = valueTranslation(value)) == null ? void 0 : _a.title) || `ID: ${value == null ? void 0 : value.id}`;
    };
    const valueShort = (value) => {
      var _a;
      return ((_a = valueTranslation(value)) == null ? void 0 : _a.short) || "";
    };
    const valueDescription = (value) => {
      var _a;
      return ((_a = valueTranslation(value)) == null ? void 0 : _a.description) || "";
    };
    const attributeTranslation = (value) => {
      var _a;
      return ((_a = value == null ? void 0 : value.attribute) == null ? void 0 : _a.translation) || {};
    };
    const attributeTitle = (value) => {
      var _a, _b;
      return ((_a = attributeTranslation(value)) == null ? void 0 : _a.title) || ((_b = value == null ? void 0 : value.attribute) == null ? void 0 : _b.code) || "—";
    };
    const attributeInfo = (value) => {
      const attribute = value == null ? void 0 : value.attribute;
      if (!attribute) {
        return "—";
      }
      return [
        attribute.code,
        attribute.type ? getTypeLabel(
          attribute.type
        ) : null,
        attribute.unit
      ].filter(Boolean).join(" / ") || "—";
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
    const moderationBadge = (status) => {
      const moderationStatus = Number(status ?? 0);
      if (moderationStatus === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (moderationStatus === 2) {
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
    const truncateText = (text, maxLength = 80) => {
      if (text === null || text === void 0 || text === "") {
        return "";
      }
      const value = String(text);
      return value.length > maxLength ? `${value.slice(0, maxLength).trimEnd()}…` : value;
    };
    const isAttributeBlockOpen = (valueId) => {
      return openedAttributeBlocks.value.includes(
        valueId
      );
    };
    const toggleAttributeBlock = (valueId) => {
      if (isAttributeBlockOpen(
        valueId
      )) {
        openedAttributeBlocks.value = openedAttributeBlocks.value.filter(
          (id) => id !== valueId
        );
        return;
      }
      openedAttributeBlocks.value.push(
        valueId
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedValues.length)}</div>`);
      if (localValues.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localValues.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localValues.value,
          "onUpdate:modelValue": ($event) => localValues.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd,
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: value }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${value.sort}] / ${formatDate(value.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(value.id)}</div><button type="button" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"${ssrRenderAttr("title", isAttributeBlockOpen(value.id) ? unref(t)("hide") : unref(t)("show"))}${_scopeId}><svg class="${ssrRenderClass([{ "rotate-180": isAttributeBlockOpen(value.id) }, "w-4 h-4 transition-transform duration-200"])}" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"${_scopeId}></path></svg></button></div><div class="flex items-center space-x-2"${_scopeId}><span class="${ssrRenderClass([moderationBadge(value.moderation_status).class, "text-[10px] px-2 py-0.5 rounded-sm border font-semibold"])}"${_scopeId}>${ssrInterpolate(moderationBadge(value.moderation_status).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedValues.includes(value.id)) ? " checked" : ""}${_scopeId}></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-2"${_scopeId}><div style="${ssrRenderStyle(isAttributeBlockOpen(value.id) ? null : { display: "none" })}" class="p-2 rounded-sm border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-center"${_scopeId}><div class="text-[11px] font-semibold text-slate-700 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("attribute"))}</div><div class="text-[12px] font-semibold text-blue-700 dark:text-blue-300"${ssrRenderAttr("title", attributeTitle(value))}${_scopeId}>${ssrInterpolate(truncateText(attributeTitle(value), 90))}</div><div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"${ssrRenderAttr("title", attributeInfo(value))}${_scopeId}>${ssrInterpolate(attributeInfo(value))}</div></div><div class="flex justify-center items-center"${_scopeId}><div class="text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md" style="${ssrRenderStyle({ borderColor: value.color || "transparent" })}"${_scopeId}><span style="${ssrRenderStyle({ color: value.color || "#666666" })}"${_scopeId}>${ssrInterpolate(truncateText(valueTitle(value)))}</span></div></div><div class="flex justify-center"${_scopeId}>`);
              if (getSafeIcon(value.icon)) {
                _push2(`<div class="w-8 h-8 text-slate-700 dark:text-slate-100 flex items-center justify-center"${_scopeId}>${getSafeIcon(value.icon) ?? ""}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (value.color) {
                _push2(`<div class="flex items-center justify-center gap-1 font-semibold text-xs text-slate-700 dark:text-slate-300"${_scopeId}><span class="inline-block w-4 h-4 rounded-sm border border-slate-400" style="${ssrRenderStyle({ backgroundColor: value.color })}"${_scopeId}></span><span${_scopeId}>${ssrInterpolate(value.color)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="text-center text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("code"))}: ${ssrInterpolate(truncateText(value.code, 90) || "—")}</div><div class="text-center text-[11px] font-semibold text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("attribute"))}: ${ssrInterpolate(truncateText(attributeTitle(value), 80))}</div>`);
              if (valueShort(value)) {
                _push2(`<div class="font-semibold text-[12px] text-center text-cyan-700 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(truncateText(valueShort(value), 120))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (valueDescription(value)) {
                _push2(`<div class="text-[11px] text-center text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(valueDescription(value), 140))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (value.show_from_at) {
                _push2(`<div class="text-center text-[10px] text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("show"))}: ${ssrInterpolate(formatDate(value.show_from_at))} / ${ssrInterpolate(formatDate(value.show_to_at) || "—")}</div>`);
              } else {
                _push2(`<div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(formatDate(value.published_at))}</div>`);
              }
              _push2(`<div class="font-semibold text-center text-[11px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("status"))}: </span> ${ssrInterpolate(getStatusLabel(value.status))}</div><div class="flex justify-center space-x-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(value.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", value.moderation_note && value.moderated_at ? `${value.moderation_note} [${formatDate(value.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(value.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isAdmin: __props.isAdmin,
                status: (value == null ? void 0 : value.moderation_status) ?? 0,
                initialNote: (value == null ? void 0 : value.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emits("approve", value, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: value.activity,
                onToggleActivity: ($event) => emits("toggle-activity", value),
                title: value.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.marketAttributeValues.edit", {
                  marketAttributeValue: value.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emits("delete", value)
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
                        title: `[${value.sort}] / ${formatDate(value.published_at)}`
                      }, " ID: " + toDisplayString(value.id), 9, ["title"]),
                      createVNode("button", {
                        type: "button",
                        class: "text-slate-400 hover:text-blue-600 dark:hover:text-blue-300",
                        title: isAttributeBlockOpen(value.id) ? unref(t)("hide") : unref(t)("show"),
                        onClick: withModifiers(($event) => toggleAttributeBlock(value.id), ["prevent"])
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: ["w-4 h-4 transition-transform duration-200", { "rotate-180": isAttributeBlockOpen(value.id) }],
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
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border font-semibold", moderationBadge(value.moderation_status).class]
                      }, toDisplayString(moderationBadge(value.moderation_status).text), 3),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedValues.includes(value.id),
                        onChange: ($event) => emits("toggle-select", value.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-2" }, [
                    withDirectives(createVNode("div", { class: "p-2 rounded-sm border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-center" }, [
                      createVNode("div", { class: "text-[11px] font-semibold text-slate-700 dark:text-slate-100" }, toDisplayString(unref(t)("attribute")), 1),
                      createVNode("div", {
                        class: "text-[12px] font-semibold text-blue-700 dark:text-blue-300",
                        title: attributeTitle(value)
                      }, toDisplayString(truncateText(attributeTitle(value), 90)), 9, ["title"]),
                      createVNode("div", {
                        class: "mt-1 text-[10px] text-slate-500 dark:text-slate-300",
                        title: attributeInfo(value)
                      }, toDisplayString(attributeInfo(value)), 9, ["title"])
                    ], 512), [
                      [vShow, isAttributeBlockOpen(value.id)]
                    ]),
                    createVNode("div", { class: "flex justify-center items-center" }, [
                      createVNode("div", {
                        class: "text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md",
                        style: { borderColor: value.color || "transparent" }
                      }, [
                        createVNode("span", {
                          style: { color: value.color || "#666666" }
                        }, toDisplayString(truncateText(valueTitle(value))), 5)
                      ], 4)
                    ]),
                    createVNode("div", { class: "flex justify-center" }, [
                      getSafeIcon(value.icon) ? (openBlock(), createBlock("div", {
                        key: 0,
                        innerHTML: getSafeIcon(value.icon),
                        class: "w-8 h-8 text-slate-700 dark:text-slate-100 flex items-center justify-center"
                      }, null, 8, ["innerHTML"])) : createCommentVNode("", true)
                    ]),
                    value.color ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex items-center justify-center gap-1 font-semibold text-xs text-slate-700 dark:text-slate-300"
                    }, [
                      createVNode("span", {
                        class: "inline-block w-4 h-4 rounded-sm border border-slate-400",
                        style: { backgroundColor: value.color }
                      }, null, 4),
                      createVNode("span", null, toDisplayString(value.color), 1)
                    ])) : createCommentVNode("", true),
                    createVNode("div", { class: "text-center text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(unref(t)("code")) + ": " + toDisplayString(truncateText(value.code, 90) || "—"), 1),
                    createVNode("div", { class: "text-center text-[11px] font-semibold text-gray-700 dark:text-gray-300" }, toDisplayString(unref(t)("attribute")) + ": " + toDisplayString(truncateText(attributeTitle(value), 80)), 1),
                    valueShort(value) ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "font-semibold text-[12px] text-center text-cyan-700 dark:text-cyan-300"
                    }, toDisplayString(truncateText(valueShort(value), 120)), 1)) : createCommentVNode("", true),
                    valueDescription(value) ? (openBlock(), createBlock("div", {
                      key: 2,
                      class: "text-[11px] text-center text-slate-500 dark:text-slate-300"
                    }, toDisplayString(truncateText(valueDescription(value), 140)), 1)) : createCommentVNode("", true),
                    value.show_from_at ? (openBlock(), createBlock("div", {
                      key: 3,
                      class: "text-center text-[10px] text-gray-700 dark:text-gray-300"
                    }, toDisplayString(unref(t)("show")) + ": " + toDisplayString(formatDate(value.show_from_at)) + " / " + toDisplayString(formatDate(value.show_to_at) || "—"), 1)) : (openBlock(), createBlock("div", {
                      key: 4,
                      class: "text-center text-[10px] text-slate-500 dark:text-slate-300"
                    }, toDisplayString(formatDate(value.published_at)), 1)),
                    createVNode("div", { class: "font-semibold text-center text-[11px] text-fuchsia-700 dark:text-fuchsia-300" }, [
                      createVNode("span", null, toDisplayString(unref(t)("status")) + ": ", 1),
                      createTextVNode(" " + toDisplayString(getStatusLabel(value.status)), 1)
                    ]),
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(value.moderation_status).class],
                        title: value.moderation_note && value.moderated_at ? `${value.moderation_note} [${formatDate(value.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(value.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$5, {
                        isAdmin: __props.isAdmin,
                        status: (value == null ? void 0 : value.moderation_status) ?? 0,
                        initialNote: (value == null ? void 0 : value.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => emits("approve", value, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$6, {
                        isActive: value.activity,
                        onToggleActivity: ($event) => emits("toggle-activity", value),
                        title: value.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.marketAttributeValues.edit", {
                          marketAttributeValue: value.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emits("delete", value)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketAttributeValue/View/AttributeValueCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminMarketAttributeValuesProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminMarketAttributeValuesPerPage: { type: Number, default: 10 },
    adminMarketAttributeValuesDefaultSort: { type: String, default: "idDesc" },
    values: { type: [Array, Object], default: () => [] },
    valuesCount: { type: Number, default: 0 },
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
    const getValueTranslation = (value) => (value == null ? void 0 : value.translation) || {};
    const getValueTranslationTitle = (value) => {
      var _a;
      return ((_a = getValueTranslation(value)) == null ? void 0 : _a.title) || "";
    };
    const getValueTitle = (value) => {
      return getValueTranslationTitle(value) || `ID: ${value == null ? void 0 : value.id}`;
    };
    const getValueSubtitle = (value) => {
      var _a;
      return ((_a = getValueTranslation(value)) == null ? void 0 : _a.subtitle) || "";
    };
    const getValueShort = (value) => {
      var _a;
      return ((_a = getValueTranslation(value)) == null ? void 0 : _a.short) || "";
    };
    const getValueDescription = (value) => {
      var _a;
      return ((_a = getValueTranslation(value)) == null ? void 0 : _a.description) || "";
    };
    const getAttributeTranslation = (value) => {
      var _a;
      return ((_a = value == null ? void 0 : value.attribute) == null ? void 0 : _a.translation) || {};
    };
    const getAttributeTranslationTitle = (value) => {
      var _a;
      return ((_a = getAttributeTranslation(value)) == null ? void 0 : _a.title) || "";
    };
    const getAttributeSubtitle = (value) => {
      var _a;
      return ((_a = getAttributeTranslation(value)) == null ? void 0 : _a.subtitle) || "";
    };
    const getAttributeShort = (value) => {
      var _a;
      return ((_a = getAttributeTranslation(value)) == null ? void 0 : _a.short) || "";
    };
    const getAttributeDescription = (value) => {
      var _a;
      return ((_a = getAttributeTranslation(value)) == null ? void 0 : _a.description) || "";
    };
    const getModeratorName = (value) => {
      var _a;
      return ((_a = value == null ? void 0 : value.moderator) == null ? void 0 : _a.name) || "";
    };
    const getModeratorEmail = (value) => {
      var _a;
      return ((_a = value == null ? void 0 : value.moderator) == null ? void 0 : _a.email) || "";
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
      localStorage.getItem("admin_view_mode_market_attribute_values") || "cards"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_market_attribute_values",
        value
      );
    });
    const itemsPerPage = ref(
      props.adminMarketAttributeValuesPerPage || 10
    );
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountMarketAttributeValues"),
        {
          value: newVal
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(
              `Показ ${newVal} значений характеристик на странице.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления кол-ва значений."
            );
          }
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminMarketAttributeValuesDefaultSort || "idDesc"
    );
    watch(sortParam, (newVal) => {
      router.put(
        route("admin.settings.updateAdminSortMarketAttributeValues"),
        {
          value: newVal
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
              "Сортировка значений характеристик успешно изменена."
            );
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления сортировки значений."
            );
          }
        }
      );
    });
    const localValues = ref([]);
    const valuesList = computed(() => {
      var _a, _b, _c, _d;
      if (Array.isArray(props.values)) {
        return props.values;
      }
      if (Array.isArray((_a = props.values) == null ? void 0 : _a.data)) {
        return props.values.data;
      }
      if (Array.isArray((_c = (_b = props.values) == null ? void 0 : _b.data) == null ? void 0 : _c.data)) {
        return props.values.data.data;
      }
      if (Array.isArray((_d = props.values) == null ? void 0 : _d.resource)) {
        return props.values.resource;
      }
      return [];
    });
    watch(
      valuesList,
      (newVal) => {
        localValues.value = JSON.parse(
          JSON.stringify(
            newVal || []
          )
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const showConfirmDeleteModal = ref(false);
    const valueToDeleteId = ref(null);
    const valueToDeleteTitle = ref("");
    const confirmDelete = (valueOrId, title = null) => {
      if (valueOrId && typeof valueOrId === "object") {
        valueToDeleteId.value = valueOrId.id;
        valueToDeleteTitle.value = title || getValueTitle(valueOrId);
      } else {
        valueToDeleteId.value = valueOrId;
        valueToDeleteTitle.value = title || `ID: ${valueOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      valueToDeleteId.value = null;
      valueToDeleteTitle.value = "";
    };
    const deleteValue = () => {
      if (valueToDeleteId.value === null) {
        return;
      }
      const idToDelete = valueToDeleteId.value;
      const titleToDelete = valueToDeleteTitle.value;
      router.delete(
        route(
          "admin.marketAttributeValues.destroy",
          {
            marketAttributeValue: idToDelete
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Значение характеристики "${titleToDelete || "ID: " + idToDelete}" удалено.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
            toast.error(
              `${errorMsg} (Значение: ${titleToDelete || "ID: " + idToDelete})`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    const patchLocalValue = (valueId, callback) => {
      const index = localValues.value.findIndex(
        (value) => value.id === valueId
      );
      if (index !== -1) {
        callback(
          localValues.value[index]
        );
      }
    };
    const toggleActivity = (value) => {
      const newActivity = !value.activity;
      const title = getValueTitle(value);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.marketAttributeValues.updateActivity",
          {
            marketAttributeValue: value.id
          }
        ),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalValue(
              value.id,
              (node) => {
                node.activity = newActivity;
                node.is_active = newActivity;
              }
            );
            toast.success(
              `Значение характеристики "${title}" ${actionText}.`
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
    const searchQuery = ref(
      props.search || ""
    );
    const currentPage = ref(1);
    const byNumberAsc = (field) => (a, b) => {
      return safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byNumberDesc = (field) => (a, b) => {
      return safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byDateAsc = (field) => (a, b) => {
      return safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byDateDesc = (field) => (a, b) => {
      return safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byStringAsc = (getter) => (a, b) => {
      return normalize(
        getter(a)
      ).localeCompare(
        normalize(
          getter(b)
        ),
        locale.value
      ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byStringDesc = (getter) => (a, b) => {
      return normalize(
        getter(b)
      ).localeCompare(
        normalize(
          getter(a)
        ),
        locale.value
      ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byIdDesc = (a, b) => {
      return safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const sortValues = (values) => {
      const list = (values || []).slice();
      if (sortParam.value === "activity") {
        return list.filter(
          (value) => value == null ? void 0 : value.activity
        ).sort(byIdDesc);
      }
      if (sortParam.value === "inactive") {
        return list.filter(
          (value) => !(value == null ? void 0 : value.activity)
        ).sort(byIdDesc);
      }
      if (sortParam.value === "statusDraft") {
        return list.filter(
          (value) => (value == null ? void 0 : value.status) === "draft"
        ).sort(byIdDesc);
      }
      if (sortParam.value === "statusPublished") {
        return list.filter(
          (value) => (value == null ? void 0 : value.status) === "published"
        ).sort(byIdDesc);
      }
      if (sortParam.value === "statusArchived") {
        return list.filter(
          (value) => (value == null ? void 0 : value.status) === "archived"
        ).sort(byIdDesc);
      }
      if (sortParam.value === "moderationPending") {
        return list.filter(
          (value) => moderationNum(
            value == null ? void 0 : value.moderation_status
          ) === 0
        ).sort(byIdDesc);
      }
      if (sortParam.value === "moderationApproved") {
        return list.filter(
          (value) => moderationNum(
            value == null ? void 0 : value.moderation_status
          ) === 1
        ).sort(byIdDesc);
      }
      if (sortParam.value === "moderationRejected") {
        return list.filter(
          (value) => moderationNum(
            value == null ? void 0 : value.moderation_status
          ) === 2
        ).sort(byIdDesc);
      }
      const sortMap = {
        idAsc: (a, b) => safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        idDesc: byIdDesc,
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        /**
         * Используем именно translation.title.
         * Fallback `ID: ...` не должен влиять
         * на сортировку.
         */
        titleAsc: byStringAsc(
          getValueTranslationTitle
        ),
        titleDesc: byStringDesc(
          getValueTranslationTitle
        ),
        /**
         * Backend сортирует по
         * market_attribute_translations.title,
         * поэтому code fallback здесь не используем.
         */
        attributeTitleAsc: byStringAsc(
          getAttributeTranslationTitle
        ),
        attributeTitleDesc: byStringDesc(
          getAttributeTranslationTitle
        ),
        codeAsc: byStringAsc(
          (value) => (value == null ? void 0 : value.code) || ""
        ),
        codeDesc: byStringDesc(
          (value) => (value == null ? void 0 : value.code) || ""
        ),
        colorAsc: byStringAsc(
          (value) => (value == null ? void 0 : value.color) || ""
        ),
        colorDesc: byStringDesc(
          (value) => (value == null ? void 0 : value.color) || ""
        ),
        activityAsc: byNumberAsc(
          "activity"
        ),
        activityDesc: byNumberDesc(
          "activity"
        ),
        statusAsc: byStringAsc(
          (value) => (value == null ? void 0 : value.status) || ""
        ),
        statusDesc: byStringDesc(
          (value) => (value == null ? void 0 : value.status) || ""
        ),
        moderationStatusAsc: byNumberAsc(
          "moderation_status"
        ),
        moderationStatusDesc: byNumberDesc(
          "moderation_status"
        ),
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
      return sortMap[sortParam.value] ? list.sort(
        sortMap[sortParam.value]
      ) : list;
    };
    const filteredValues = computed(() => {
      let filtered = localValues.value || [];
      const query = normalize(
        searchQuery.value
      );
      if (!query) {
        return sortValues(
          filtered
        );
      }
      filtered = filtered.filter(
        (value) => {
          var _a, _b, _c;
          const searchableValues = [
            /** MarketAttributeValue */
            value == null ? void 0 : value.code,
            value == null ? void 0 : value.color,
            value == null ? void 0 : value.status,
            value == null ? void 0 : value.moderation_note,
            /** MarketAttributeValue translation */
            getValueTranslationTitle(
              value
            ),
            getValueSubtitle(
              value
            ),
            getValueShort(
              value
            ),
            getValueDescription(
              value
            ),
            /** MarketAttribute */
            (_a = value == null ? void 0 : value.attribute) == null ? void 0 : _a.code,
            (_b = value == null ? void 0 : value.attribute) == null ? void 0 : _b.type,
            (_c = value == null ? void 0 : value.attribute) == null ? void 0 : _c.unit,
            /** MarketAttribute translation */
            getAttributeTranslationTitle(
              value
            ),
            getAttributeSubtitle(
              value
            ),
            getAttributeShort(
              value
            ),
            getAttributeDescription(
              value
            ),
            /** Moderator */
            getModeratorName(
              value
            ),
            getModeratorEmail(
              value
            )
          ];
          return searchableValues.some(
            (item) => normalize(
              item
            ).includes(
              query
            )
          );
        }
      );
      return sortValues(
        filtered
      );
    });
    const paginatedValues = computed(() => {
      const perPage = Number(
        itemsPerPage.value || 10
      );
      const start = (currentPage.value - 1) * perPage;
      return filteredValues.value.slice(
        start,
        start + perPage
      );
    });
    const displayedValues = computed(() => {
      return props.useServerProcessing ? valuesList.value : paginatedValues.value;
    });
    watch(
      [
        itemsPerPage,
        searchQuery
      ],
      () => {
        currentPage.value = 1;
      }
    );
    const selectedValues = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = Boolean(
        (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false
      );
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedValues.value.map(
        (value) => value.id
      );
      if (checked) {
        selectedValues.value = [
          .../* @__PURE__ */ new Set([
            ...selectedValues.value,
            ...ids
          ])
        ];
        return;
      }
      selectedValues.value = selectedValues.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectValue = (valueId) => {
      const index = selectedValues.value.indexOf(
        valueId
      );
      if (index > -1) {
        selectedValues.value.splice(
          index,
          1
        );
        return;
      }
      selectedValues.value.push(
        valueId
      );
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedValues.value.length) {
        toast.warning(
          "Выберите значения характеристик для активации/деактивации."
        );
        return;
      }
      const idsToUpdate = [
        ...selectedValues.value
      ];
      router.put(
        route(
          "admin.actions.marketAttributeValues.bulkUpdateActivity"
        ),
        {
          ids: idsToUpdate,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localValues.value = localValues.value.map(
              (value) => {
                if (!idsToUpdate.includes(
                  value.id
                )) {
                  return value;
                }
                return {
                  ...value,
                  activity: newActivity,
                  is_active: newActivity
                };
              }
            );
            selectedValues.value = [];
            toast.success(
              "Активность значений характеристик массово обновлена."
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
      if (!selectedValues.value.length) {
        toast.warning(
          "Выберите хотя бы одно значение характеристики для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные значения характеристик?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.marketAttributeValues.bulkDestroy"
        ),
        {
          data: {
            ids: selectedValues.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedValues.value = [];
            toast.success(
              "Массовое удаление значений характеристик успешно завершено."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              errors[errorKey] || "Произошла ошибка при удалении значений характеристик."
            );
          }
        }
      );
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") {
        toggleAll({
          checked: true
        });
      } else if (action === "deselectAll") {
        toggleAll({
          checked: false
        });
      } else if (action === "activate") {
        bulkToggleActivity(
          true
        );
      } else if (action === "deactivate") {
        bulkToggleActivity(
          false
        );
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const approveValue = (value, status = 1, note = "") => {
      if (!(value == null ? void 0 : value.id)) {
        return;
      }
      router.put(
        route(
          "admin.actions.marketAttributeValues.approve",
          {
            marketAttributeValue: value.id
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
            patchLocalValue(
              value.id,
              (node) => {
                node.moderation_status = status;
                node.is_pending = status === 0;
                node.is_approved = status === 1;
                node.is_rejected = status === 2;
                node.moderation_note = note;
              }
            );
            toast.success(
              status === 1 ? "Значение характеристики одобрено." : "Значение характеристики отклонено."
            );
          },
          onError: () => {
            toast.error(
              "Ошибка модерации значения характеристики."
            );
          }
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
      if (!items.length) {
        return;
      }
      router.put(
        route(
          "admin.actions.marketAttributeValues.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Сортировка значений характеристик обновлена."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка сортировки значений характеристик:",
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
        title: unref(t)("marketAttributeValues")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("marketAttributeValues"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("marketAttributeValues")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("marketAttributeValues")), 1)
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
              href: _ctx.route("admin.marketAttributeValues.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addMarketAttributeValue"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addMarketAttributeValue")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              "setting-key": "adminMarketAttributeValuesProcessingMode",
              mode: __props.adminMarketAttributeValuesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.valuesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.valuesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.valuesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.valuesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountMarketAttributeValues"
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
            if (__props.valuesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$f, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.valuesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.valuesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              if (__props.valuesCount) {
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
            if (__props.valuesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredValues.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.values }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                values: displayedValues.value,
                "selected-values": selectedValues.value,
                "is-admin": isAdmin.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectValue,
                onToggleAll: toggleAll,
                onApprove: approveValue
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                values: displayedValues.value,
                "selected-values": selectedValues.value,
                "is-admin": isAdmin.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectValue,
                onToggleAll: toggleAll,
                onApprove: approveValue
              }, null, _parent2, _scopeId));
            }
            if (__props.valuesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredValues.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.values }, null, _parent2, _scopeId));
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
              onConfirm: deleteValue,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$9, {
                      href: _ctx.route("admin.marketAttributeValues.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addMarketAttributeValue")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$a, {
                      "setting-key": "adminMarketAttributeValuesProcessingMode",
                      mode: __props.adminMarketAttributeValuesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.valuesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.valuesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.valuesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.valuesCount ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountMarketAttributeValues"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.valuesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$f, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.valuesCount), 1)
                      ]),
                      _: 1
                    }),
                    __props.valuesCount ? (openBlock(), createBlock(_sfc_main$4, {
                      key: 0,
                      onChange: handleBulkAction
                    })) : createCommentVNode("", true),
                    createVNode(_sfc_main$g, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.valuesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredValues.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.values
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    values: displayedValues.value,
                    "selected-values": selectedValues.value,
                    "is-admin": isAdmin.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectValue,
                    onToggleAll: toggleAll,
                    onApprove: approveValue
                  }, null, 8, ["values", "selected-values", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    values: displayedValues.value,
                    "selected-values": selectedValues.value,
                    "is-admin": isAdmin.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectValue,
                    onToggleAll: toggleAll,
                    onApprove: approveValue
                  }, null, 8, ["values", "selected-values", "is-admin"])),
                  __props.valuesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredValues.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.values
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$j, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteValue,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketAttributeValues/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
