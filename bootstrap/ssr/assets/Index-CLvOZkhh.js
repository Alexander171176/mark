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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketAttributeGroup/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>─────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>─────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="codeAsc">${ssrInterpolate(unref(t)("code"))} A→Z</option><option value="codeDesc">${ssrInterpolate(unref(t)("code"))} Z→A</option><option disabled>─────────────────</option><option value="attributesCountDesc">${ssrInterpolate(unref(t)("attributes"))} 9→0</option><option value="attributesCountAsc">${ssrInterpolate(unref(t)("attributes"))} 0→9</option><option disabled>─────────────────</option><option value="colorAsc">${ssrInterpolate(unref(t)("typeColor"))} 0→F</option><option value="colorDesc">${ssrInterpolate(unref(t)("typeColor"))} F→0</option><option disabled>─────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="statusDraft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="statusPublished">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="statusArchived">${ssrInterpolate(unref(t)("statusArchived"))}</option><option disabled>─────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="showFromAtDesc">${ssrInterpolate(unref(t)("showFromAt"))} ↓</option><option value="showFromAtAsc">${ssrInterpolate(unref(t)("showFromAt"))} ↑</option><option value="showToAtDesc">${ssrInterpolate(unref(t)("showToAt"))} ↓</option><option value="showToAtAsc">${ssrInterpolate(unref(t)("showToAt"))} ↑</option><option disabled>─────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>─────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketAttributeGroup/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "AttributeGroupTable",
  __ssrInlineRender: true,
  props: {
    groups: { type: Array, default: () => [] },
    selectedGroups: { type: Array, default: () => [] },
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
    const localGroups = ref([]);
    watch(
      () => props.groups,
      (newVal) => {
        localGroups.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits(
        "update-sort-order",
        localGroups.value.map((group) => group.id)
      );
    };
    const allSelected = () => {
      return localGroups.value.length > 0 && localGroups.value.every((group) => props.selectedGroups.includes(group.id));
    };
    const groupTranslation = (group) => (group == null ? void 0 : group.translation) || {};
    const groupTitle = (group) => {
      var _a;
      return ((_a = groupTranslation(group)) == null ? void 0 : _a.title) || `ID: ${group == null ? void 0 : group.id}`;
    };
    const groupShort = (group) => {
      var _a;
      return ((_a = groupTranslation(group)) == null ? void 0 : _a.short) || "";
    };
    const ownerTitle = (group) => {
      const owner = group == null ? void 0 : group.owner;
      if (!owner) return t("noData");
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (group) => {
      var _a;
      return ((_a = group == null ? void 0 : group.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const getSafeIcon = (icon) => {
      if (typeof icon !== "string") return null;
      const trimmed = icon.trim();
      if (!trimmed) return null;
      return trimmed.startsWith("<svg") && trimmed.endsWith("</svg>") ? trimmed : null;
    };
    const getStatusLabel = (status) => {
      const map = {
        draft: "statusDraft",
        published: "statusPublished",
        archived: "statusArchived"
      };
      return t(map[status] || status || "no");
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
      if (!dateStr) return "";
      const date = new Date(dateStr);
      if (Number.isNaN(date.getTime())) return "";
      return date.toLocaleDateString(locale.value || void 0, {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    const truncateText = (text, maxLength = 70) => {
      if (!text) return "";
      const value = String(text);
      return value.length > maxLength ? value.slice(0, maxLength).trimEnd() + "…" : value;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedGroups.length)}</div>`);
      if (localGroups.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localGroups.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-1 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("owner"))}</div></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("icon"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("title"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("description"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("attributes"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-1 py-1 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localGroups.value,
          "onUpdate:modelValue": ($event) => localGroups.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: group }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-1 py-1 text-center cursor-move handle w-px"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-1 py-1 whitespace-nowrap w-px"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${ssrRenderAttr("title", `[${group.sort}] / ${formatDate(group.published_at)}`)}${_scopeId}>${ssrInterpolate(group.id)}</div></td><td class="px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(group))}${ssrRenderAttr("title", ownerTitle(group))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("owner"))}${_scopeId}></div><div class="text-[10px] font-semibold text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(ownerTitle(group))}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${_scopeId}>`);
              if (getSafeIcon(group.icon)) {
                _push2(`<div class="w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center"${_scopeId}>${getSafeIcon(group.icon) ?? ""}</div>`);
              } else {
                _push2(`<span class="text-slate-400 dark:text-slate-300"${_scopeId}> — </span>`);
              }
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-center space-y-1"${_scopeId}><div class="text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md" style="${ssrRenderStyle({ borderColor: group.color || "transparent" })}"${ssrRenderAttr("title", group.show_from_at ? `${unref(t)("show")}: ${group.show_from_at} / ${group.show_to_at || "—"}` : formatDate(group.published_at))}${_scopeId}><span style="${ssrRenderStyle({ color: group.color || "#666666" })}"${_scopeId}>${ssrInterpolate(truncateText(groupTitle(group)))}</span></div><div class="italic text-center text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(group.code)}</div><div class="flex justify-center items-center gap-1"${ssrRenderAttr("title", unref(t)("typeColor"))}${_scopeId}><span class="inline-block w-5 h-5 rounded-sm border border-slate-400" style="${ssrRenderStyle({ backgroundColor: group.color || "transparent" })}"${_scopeId}></span><span class="text-xs text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(group.color || "—")}</span></div></div></td><td class="px-1 py-1"${_scopeId}><div class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(truncateText(groupShort(group), 80))}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-300"${_scopeId}>${ssrInterpolate(group.attributes_count ?? 0)}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-center justify-center space-y-1"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(group.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", group.moderation_note && group.moderated_at ? `${group.moderation_note} [${formatDate(group.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(group.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isAdmin: __props.isAdmin,
                status: (group == null ? void 0 : group.moderation_status) ?? 0,
                initialNote: (group == null ? void 0 : group.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emits("approve", group, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(getStatusLabel(group.status))}</div></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: group.activity,
                title: group.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emits("toggle-activity", group)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.marketAttributeGroups.edit", {
                  marketAttributeGroup: group.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emits("delete", group)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedGroups.includes(group.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                      title: `[${group.sort}] / ${formatDate(group.published_at)}`
                    }, toDisplayString(group.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(group),
                        title: ownerTitle(group),
                        class: "h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("owner")
                      }, null, 8, ["src", "title", "alt"])
                    ]),
                    createVNode("div", { class: "text-[10px] font-semibold text-center text-slate-700 dark:text-slate-300" }, toDisplayString(ownerTitle(group)), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      getSafeIcon(group.icon) ? (openBlock(), createBlock("div", {
                        key: 0,
                        innerHTML: getSafeIcon(group.icon),
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
                        style: { borderColor: group.color || "transparent" },
                        title: group.show_from_at ? `${unref(t)("show")}: ${group.show_from_at} / ${group.show_to_at || "—"}` : formatDate(group.published_at)
                      }, [
                        createVNode("span", {
                          style: { color: group.color || "#666666" }
                        }, toDisplayString(truncateText(groupTitle(group))), 5)
                      ], 12, ["title"]),
                      createVNode("div", { class: "italic text-center text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(group.code), 1),
                      createVNode("div", {
                        class: "flex justify-center items-center gap-1",
                        title: unref(t)("typeColor")
                      }, [
                        createVNode("span", {
                          class: "inline-block w-5 h-5 rounded-sm border border-slate-400",
                          style: { backgroundColor: group.color || "transparent" }
                        }, null, 4),
                        createVNode("span", { class: "text-xs text-slate-700 dark:text-slate-300" }, toDisplayString(group.color || "—"), 1)
                      ], 8, ["title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(truncateText(groupShort(group), 80)), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-blue-600 dark:text-blue-300" }, toDisplayString(group.attributes_count ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-col items-center justify-center space-y-1" }, [
                      createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                        createVNode("span", {
                          class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(group.moderation_status).class],
                          title: group.moderation_note && group.moderated_at ? `${group.moderation_note} [${formatDate(group.moderated_at)}]` : null
                        }, toDisplayString(moderationBadge(group.moderation_status).text), 11, ["title"]),
                        createVNode(_sfc_main$5, {
                          isAdmin: __props.isAdmin,
                          status: (group == null ? void 0 : group.moderation_status) ?? 0,
                          initialNote: (group == null ? void 0 : group.moderation_note) || "",
                          mode: "toggle",
                          onSubmit: ({ status, note }) => emits("approve", group, status, note)
                        }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                      ]),
                      createVNode("div", { class: "text-[10px] text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(getStatusLabel(group.status)), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-1" }, [
                      createVNode(_sfc_main$6, {
                        isActive: group.activity,
                        title: group.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emits("toggle-activity", group)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.marketAttributeGroups.edit", {
                          marketAttributeGroup: group.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emits("delete", group)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedGroups.includes(group.id),
                        onChange: ($event) => emits("toggle-select", group.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketAttributeGroup/Table/AttributeGroupTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "AttributeGroupCardGrid",
  __ssrInlineRender: true,
  props: {
    groups: { type: Array, default: () => [] },
    selectedGroups: { type: Array, default: () => [] },
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
    const localGroups = ref([]);
    const openedOwnerBlocks = ref([]);
    watch(
      () => props.groups,
      (newVal) => {
        localGroups.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits(
        "update-sort-order",
        localGroups.value.map((group) => group.id)
      );
    };
    const allSelected = () => {
      return localGroups.value.length > 0 && localGroups.value.every((group) => props.selectedGroups.includes(group.id));
    };
    const groupTranslation = (group) => (group == null ? void 0 : group.translation) || {};
    const groupTitle = (group) => {
      var _a;
      return ((_a = groupTranslation(group)) == null ? void 0 : _a.title) || `ID: ${group == null ? void 0 : group.id}`;
    };
    const groupShort = (group) => {
      var _a;
      return ((_a = groupTranslation(group)) == null ? void 0 : _a.short) || "";
    };
    const groupLocale = (group) => {
      var _a;
      return ((_a = groupTranslation(group)) == null ? void 0 : _a.locale) || "";
    };
    const ownerName = (group) => {
      var _a;
      return ((_a = group == null ? void 0 : group.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (group) => {
      var _a;
      return ((_a = group == null ? void 0 : group.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (group) => {
      const owner = group == null ? void 0 : group.owner;
      if (!owner) return t("noData");
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (group) => {
      var _a;
      return ((_a = group == null ? void 0 : group.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const getSafeIcon = (icon) => {
      if (typeof icon !== "string") return null;
      const trimmed = icon.trim();
      if (!trimmed) return null;
      return trimmed.startsWith("<svg") && trimmed.endsWith("</svg>") ? trimmed : null;
    };
    const getStatusLabel = (status) => {
      const map = {
        draft: "statusDraft",
        published: "statusPublished",
        archived: "statusArchived"
      };
      return t(map[status] || status || "no");
    };
    const formatDate = (dateStr) => {
      if (!dateStr) return "";
      const date = new Date(dateStr);
      if (Number.isNaN(date.getTime())) return "";
      return date.toLocaleDateString(locale.value || void 0, {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    const truncateText = (text, maxLength = 80) => {
      if (!text) return "";
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
    const isOwnerBlockOpen = (groupId) => {
      return openedOwnerBlocks.value.includes(groupId);
    };
    const toggleOwnerBlock = (groupId) => {
      if (isOwnerBlockOpen(groupId)) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
          (id) => id !== groupId
        );
        return;
      }
      openedOwnerBlocks.value.push(groupId);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedGroups.length)}</div>`);
      if (localGroups.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localGroups.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localGroups.value,
          "onUpdate:modelValue": ($event) => localGroups.value = $event,
          "item-key": "id",
          handle: ".handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: group }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${groupLocale(group)}] : [${group.sort}]`)}${_scopeId}> ID: ${ssrInterpolate(group.id)}</div><button type="button" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"${ssrRenderAttr("title", isOwnerBlockOpen(group.id) ? unref(t)("hideOwner") : unref(t)("showOwner"))}${_scopeId}><svg class="${ssrRenderClass([{ "rotate-180": isOwnerBlockOpen(group.id) }, "w-4 h-4 transition-transform duration-200"])}" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"${_scopeId}></path></svg></button></div><div class="flex items-center space-x-2"${_scopeId}><span class="${ssrRenderClass([moderationBadge(group.moderation_status).class, "text-[10px] px-2 py-0.5 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", group.moderation_note && group.moderated_at ? `${group.moderation_note} [${formatDate(group.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(group.moderation_status).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedGroups.includes(group.id)) ? " checked" : ""}${_scopeId}></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-2"${_scopeId}><div style="${ssrRenderStyle(isOwnerBlockOpen(group.id) ? null : { display: "none" })}" class="flex flex-col items-center justify-center text-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(group))}${ssrRenderAttr("title", ownerTitle(group))} class="h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("owner"))}${_scopeId}><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerName(group))}${_scopeId}>${ssrInterpolate(ownerName(group))}</div>`);
              if (ownerEmail(group)) {
                _push2(`<div class="text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerEmail(group))}${_scopeId}>${ssrInterpolate(ownerEmail(group))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (group.show_from_at) {
                _push2(`<div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("show"))}: ${ssrInterpolate(group.show_from_at)} / ${ssrInterpolate(group.show_to_at || "—")}</div>`);
              } else {
                _push2(`<div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(formatDate(group.published_at))}</div>`);
              }
              _push2(`</div><div class="flex justify-center items-center"${_scopeId}><div class="text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md" style="${ssrRenderStyle({ borderColor: group.color || "transparent" })}"${ssrRenderAttr("title", group.show_from_at ? `${unref(t)("show")}: ${group.show_from_at} / ${group.show_to_at || "—"}` : formatDate(group.published_at))}${_scopeId}><span style="${ssrRenderStyle({ color: group.color || "#666666" })}"${_scopeId}>${ssrInterpolate(truncateText(groupTitle(group)))}</span></div></div><div class="flex justify-center"${_scopeId}>`);
              if (getSafeIcon(group.icon)) {
                _push2(`<div class="w-8 h-8 text-slate-700 dark:text-slate-100 flex items-center justify-center"${_scopeId}>${getSafeIcon(group.icon) ?? ""}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="text-center text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("code"))}: ${ssrInterpolate(truncateText(group.code, 90))}</div>`);
              if (group.color) {
                _push2(`<div class="flex items-center justify-center gap-1 font-semibold text-xs text-slate-700 dark:text-slate-300"${_scopeId}><span class="inline-block w-4 h-4 rounded-sm border border-slate-400" style="${ssrRenderStyle({ backgroundColor: group.color })}"${_scopeId}></span><span${_scopeId}>${ssrInterpolate(group.color)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              if (groupShort(group)) {
                _push2(`<div class="font-semibold text-[12px] text-center text-cyan-700 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(truncateText(groupShort(group), 120))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="flex items-center justify-center gap-3 text-[11px] font-semibold text-slate-600 dark:text-slate-200"${_scopeId}><div class="flex items-center justify-center space-x-1"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("attributes"))}: </span><span class="text-[12px] text-blue-600 dark:text-blue-300"${_scopeId}>${ssrInterpolate(group.attributes_count ?? 0)}</span></div></div><div class="font-semibold text-center text-[11px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("status"))}: </span> ${ssrInterpolate(getStatusLabel(group.status))}</div><div class="flex justify-center space-x-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(group.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", group.moderation_note && group.moderated_at ? `${group.moderation_note} [${formatDate(group.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(group.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isAdmin: __props.isAdmin,
                status: (group == null ? void 0 : group.moderation_status) ?? 0,
                initialNote: (group == null ? void 0 : group.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emits("approve", group, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: group.activity,
                title: group.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emits("toggle-activity", group)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.marketAttributeGroups.edit", {
                  marketAttributeGroup: group.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emits("delete", group)
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
                        title: `[${groupLocale(group)}] : [${group.sort}]`
                      }, " ID: " + toDisplayString(group.id), 9, ["title"]),
                      createVNode("button", {
                        type: "button",
                        class: "text-slate-400 hover:text-blue-600 dark:hover:text-blue-300",
                        title: isOwnerBlockOpen(group.id) ? unref(t)("hideOwner") : unref(t)("showOwner"),
                        onClick: withModifiers(($event) => toggleOwnerBlock(group.id), ["prevent"])
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: ["w-4 h-4 transition-transform duration-200", { "rotate-180": isOwnerBlockOpen(group.id) }],
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
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border font-semibold", moderationBadge(group.moderation_status).class],
                        title: group.moderation_note && group.moderated_at ? `${group.moderation_note} [${formatDate(group.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(group.moderation_status).text), 11, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedGroups.includes(group.id),
                        onChange: ($event) => emits("toggle-select", group.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-2" }, [
                    withDirectives(createVNode("div", { class: "flex flex-col items-center justify-center text-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(group),
                        title: ownerTitle(group),
                        class: "h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("owner")
                      }, null, 8, ["src", "title", "alt"]),
                      createVNode("div", {
                        class: "mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1",
                        title: ownerName(group)
                      }, toDisplayString(ownerName(group)), 9, ["title"]),
                      ownerEmail(group) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1",
                        title: ownerEmail(group)
                      }, toDisplayString(ownerEmail(group)), 9, ["title"])) : createCommentVNode("", true),
                      group.show_from_at ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "text-center text-[10px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(unref(t)("show")) + ": " + toDisplayString(group.show_from_at) + " / " + toDisplayString(group.show_to_at || "—"), 1)) : (openBlock(), createBlock("div", {
                        key: 2,
                        class: "text-center text-[10px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(formatDate(group.published_at)), 1))
                    ], 512), [
                      [vShow, isOwnerBlockOpen(group.id)]
                    ]),
                    createVNode("div", { class: "flex justify-center items-center" }, [
                      createVNode("div", {
                        class: "text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md",
                        style: { borderColor: group.color || "transparent" },
                        title: group.show_from_at ? `${unref(t)("show")}: ${group.show_from_at} / ${group.show_to_at || "—"}` : formatDate(group.published_at)
                      }, [
                        createVNode("span", {
                          style: { color: group.color || "#666666" }
                        }, toDisplayString(truncateText(groupTitle(group))), 5)
                      ], 12, ["title"])
                    ]),
                    createVNode("div", { class: "flex justify-center" }, [
                      getSafeIcon(group.icon) ? (openBlock(), createBlock("div", {
                        key: 0,
                        innerHTML: getSafeIcon(group.icon),
                        class: "w-8 h-8 text-slate-700 dark:text-slate-100 flex items-center justify-center"
                      }, null, 8, ["innerHTML"])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "text-center text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(unref(t)("code")) + ": " + toDisplayString(truncateText(group.code, 90)), 1),
                    group.color ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex items-center justify-center gap-1 font-semibold text-xs text-slate-700 dark:text-slate-300"
                    }, [
                      createVNode("span", {
                        class: "inline-block w-4 h-4 rounded-sm border border-slate-400",
                        style: { backgroundColor: group.color }
                      }, null, 4),
                      createVNode("span", null, toDisplayString(group.color), 1)
                    ])) : createCommentVNode("", true),
                    groupShort(group) ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "font-semibold text-[12px] text-center text-cyan-700 dark:text-cyan-300"
                    }, toDisplayString(truncateText(groupShort(group), 120)), 1)) : createCommentVNode("", true),
                    createVNode("div", { class: "flex items-center justify-center gap-3 text-[11px] font-semibold text-slate-600 dark:text-slate-200" }, [
                      createVNode("div", { class: "flex items-center justify-center space-x-1" }, [
                        createVNode("span", null, toDisplayString(unref(t)("attributes")) + ": ", 1),
                        createVNode("span", { class: "text-[12px] text-blue-600 dark:text-blue-300" }, toDisplayString(group.attributes_count ?? 0), 1)
                      ])
                    ]),
                    createVNode("div", { class: "font-semibold text-center text-[11px] text-fuchsia-700 dark:text-fuchsia-300" }, [
                      createVNode("span", null, toDisplayString(unref(t)("status")) + ": ", 1),
                      createTextVNode(" " + toDisplayString(getStatusLabel(group.status)), 1)
                    ]),
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(group.moderation_status).class],
                        title: group.moderation_note && group.moderated_at ? `${group.moderation_note} [${formatDate(group.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(group.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$5, {
                        isAdmin: __props.isAdmin,
                        status: (group == null ? void 0 : group.moderation_status) ?? 0,
                        initialNote: (group == null ? void 0 : group.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => emits("approve", group, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$6, {
                        isActive: group.activity,
                        title: group.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emits("toggle-activity", group)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.marketAttributeGroups.edit", {
                          marketAttributeGroup: group.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emits("delete", group)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketAttributeGroup/View/AttributeGroupCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminMarketAttributeGroupsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminMarketAttributeGroupsPerPage: { type: Number, default: 10 },
    adminMarketAttributeGroupsDefaultSort: { type: String, default: "idDesc" },
    groups: { type: [Array, Object], default: () => [] },
    groupsCount: { type: Number, default: 0 },
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
    const getGroupTranslation = (group) => (group == null ? void 0 : group.translation) || {};
    const getGroupTitle = (group) => {
      var _a;
      return ((_a = getGroupTranslation(group)) == null ? void 0 : _a.title) || `ID: ${group == null ? void 0 : group.id}`;
    };
    const getGroupSubtitle = (group) => {
      var _a;
      return ((_a = getGroupTranslation(group)) == null ? void 0 : _a.subtitle) || "";
    };
    const getGroupShort = (group) => {
      var _a;
      return ((_a = getGroupTranslation(group)) == null ? void 0 : _a.short) || "";
    };
    const getOwnerName = (group) => {
      var _a;
      return ((_a = group == null ? void 0 : group.owner) == null ? void 0 : _a.name) || "";
    };
    const getOwnerEmail = (group) => {
      var _a;
      return ((_a = group == null ? void 0 : group.owner) == null ? void 0 : _a.email) || "";
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
      localStorage.getItem(
        "admin_view_mode_market_attribute_groups"
      ) || "cards"
    );
    watch(
      viewMode,
      (value) => {
        localStorage.setItem(
          "admin_view_mode_market_attribute_groups",
          value
        );
      }
    );
    const itemsPerPage = ref(
      props.adminMarketAttributeGroupsPerPage || 10
    );
    watch(
      itemsPerPage,
      (newVal) => {
        router.put(
          route(
            "admin.settings.updateAdminCountMarketAttributeGroups"
          ),
          {
            value: newVal
          },
          {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
              toast.info(
                `Показ ${newVal} групп характеристик на странице.`
              );
            },
            onError: (errors) => {
              toast.error(
                errors.value || "Ошибка обновления кол-ва групп характеристик."
              );
            }
          }
        );
      }
    );
    const sortParam = ref(
      props.sortParam || props.adminMarketAttributeGroupsDefaultSort || "idDesc"
    );
    watch(
      sortParam,
      (newVal) => {
        router.put(
          route(
            "admin.settings.updateAdminSortMarketAttributeGroups"
          ),
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
                "Сортировка групп характеристик успешно изменена."
              );
            },
            onError: (errors) => {
              toast.error(
                errors.value || "Ошибка обновления сортировки групп характеристик."
              );
            }
          }
        );
      }
    );
    const localGroups = ref([]);
    const groupsList = computed(() => {
      var _a, _b, _c, _d;
      if (Array.isArray(props.groups)) {
        return props.groups;
      }
      if (Array.isArray((_a = props.groups) == null ? void 0 : _a.data)) {
        return props.groups.data;
      }
      if (Array.isArray((_c = (_b = props.groups) == null ? void 0 : _b.data) == null ? void 0 : _c.data)) {
        return props.groups.data.data;
      }
      if (Array.isArray((_d = props.groups) == null ? void 0 : _d.resource)) {
        return props.groups.resource;
      }
      return [];
    });
    watch(
      groupsList,
      (newVal) => {
        localGroups.value = JSON.parse(
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
    const groupToDeleteId = ref(null);
    const groupToDeleteTitle = ref("");
    const confirmDelete = (groupOrId, title = null) => {
      if (typeof groupOrId === "object" && groupOrId !== null) {
        groupToDeleteId.value = groupOrId.id;
        groupToDeleteTitle.value = title || getGroupTitle(
          groupOrId
        );
      } else {
        groupToDeleteId.value = groupOrId;
        groupToDeleteTitle.value = title || `ID: ${groupOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      groupToDeleteId.value = null;
      groupToDeleteTitle.value = "";
    };
    const deleteGroup = () => {
      if (groupToDeleteId.value === null) {
        return;
      }
      const idToDelete = groupToDeleteId.value;
      const titleToDelete = groupToDeleteTitle.value;
      router.delete(
        route(
          "admin.marketAttributeGroups.destroy",
          {
            marketAttributeGroup: idToDelete
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Группа характеристик "${titleToDelete || "ID: " + idToDelete}" удалена.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
            toast.error(
              `${errorMsg} (Группа: ${titleToDelete || "ID: " + idToDelete})`
            );
          },
          onFinish: () => closeModal()
        }
      );
    };
    const patchLocalGroup = (groupId, callback) => {
      const index = localGroups.value.findIndex(
        (group) => group.id === groupId
      );
      if (index !== -1) {
        callback(
          localGroups.value[index]
        );
      }
    };
    const toggleActivity = (group) => {
      const newActivity = !group.activity;
      const title = getGroupTitle(group);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.marketAttributeGroups.updateActivity",
          {
            marketAttributeGroup: group.id
          }
        ),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalGroup(
              group.id,
              (node) => {
                node.activity = newActivity;
              }
            );
            toast.success(
              `Группа характеристик "${title}" ${actionText}.`
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
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortGroups = (groups) => {
      const list = (groups || []).slice();
      if (sortParam.value === "activity") {
        return list.filter(
          (group) => !!group.activity
        );
      }
      if (sortParam.value === "inactive") {
        return list.filter(
          (group) => !group.activity
        );
      }
      if (sortParam.value === "statusDraft") {
        return list.filter(
          (group) => (group == null ? void 0 : group.status) === "draft"
        );
      }
      if (sortParam.value === "statusPublished") {
        return list.filter(
          (group) => (group == null ? void 0 : group.status) === "published"
        );
      }
      if (sortParam.value === "statusArchived") {
        return list.filter(
          (group) => (group == null ? void 0 : group.status) === "archived"
        );
      }
      if (sortParam.value === "moderationPending") {
        return list.filter(
          (group) => moderationNum(
            group == null ? void 0 : group.moderation_status
          ) === 0
        );
      }
      if (sortParam.value === "moderationApproved") {
        return list.filter(
          (group) => moderationNum(
            group == null ? void 0 : group.moderation_status
          ) === 1
        );
      }
      if (sortParam.value === "moderationRejected") {
        return list.filter(
          (group) => moderationNum(
            group == null ? void 0 : group.moderation_status
          ) === 2
        );
      }
      const sortMap = {
        /** ID */
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        /** Sort */
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        /** Title */
        titleAsc: (a, b) => normalize(
          getGroupTitle(a)
        ).localeCompare(
          normalize(
            getGroupTitle(b)
          ),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        titleDesc: (a, b) => normalize(
          getGroupTitle(b)
        ).localeCompare(
          normalize(
            getGroupTitle(a)
          ),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        /** Code */
        codeAsc: (a, b) => normalize(
          a == null ? void 0 : a.code
        ).localeCompare(
          normalize(
            b == null ? void 0 : b.code
          ),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        codeDesc: (a, b) => normalize(
          b == null ? void 0 : b.code
        ).localeCompare(
          normalize(
            a == null ? void 0 : a.code
          ),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        /** Color */
        colorAsc: (a, b) => normalize(
          a == null ? void 0 : a.color
        ).localeCompare(
          normalize(
            b == null ? void 0 : b.color
          ),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        colorDesc: (a, b) => normalize(
          b == null ? void 0 : b.color
        ).localeCompare(
          normalize(
            a == null ? void 0 : a.color
          ),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        /** Attributes count */
        attributesCountAsc: byNumberAsc(
          "attributes_count"
        ),
        attributesCountDesc: byNumberDesc(
          "attributes_count"
        ),
        /** Status */
        statusAsc: (a, b) => normalize(
          a == null ? void 0 : a.status
        ).localeCompare(
          normalize(
            b == null ? void 0 : b.status
          ),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        statusDesc: (a, b) => normalize(
          b == null ? void 0 : b.status
        ).localeCompare(
          normalize(
            a == null ? void 0 : a.status
          ),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        /** Activity */
        activityAsc: byNumberAsc(
          "activity"
        ),
        activityDesc: byNumberDesc(
          "activity"
        ),
        /** Moderation */
        moderationStatusAsc: (a, b) => moderationNum(
          a == null ? void 0 : a.moderation_status
        ) - moderationNum(
          b == null ? void 0 : b.moderation_status
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        moderationStatusDesc: (a, b) => moderationNum(
          b == null ? void 0 : b.moderation_status
        ) - moderationNum(
          a == null ? void 0 : a.moderation_status
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        /** Owner name */
        ownerNameAsc: (a, b) => normalize(
          getOwnerName(a)
        ).localeCompare(
          normalize(
            getOwnerName(b)
          ),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        ownerNameDesc: (a, b) => normalize(
          getOwnerName(b)
        ).localeCompare(
          normalize(
            getOwnerName(a)
          ),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        /** Owner email */
        ownerEmailAsc: (a, b) => normalize(
          getOwnerEmail(a)
        ).localeCompare(
          normalize(
            getOwnerEmail(b)
          ),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        ownerEmailDesc: (a, b) => normalize(
          getOwnerEmail(b)
        ).localeCompare(
          normalize(
            getOwnerEmail(a)
          ),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        /** Dates */
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
    const filteredGroups = computed(() => {
      let filtered = localGroups.value || [];
      const query = normalize(
        searchQuery.value
      );
      if (!query) {
        return sortGroups(
          filtered
        );
      }
      filtered = filtered.filter(
        (group) => {
          const values = [
            group == null ? void 0 : group.code,
            group == null ? void 0 : group.icon,
            group == null ? void 0 : group.color,
            group == null ? void 0 : group.status,
            group == null ? void 0 : group.moderation_note,
            getGroupTitle(
              group
            ),
            getGroupSubtitle(
              group
            ),
            getGroupShort(
              group
            ),
            getOwnerName(
              group
            ),
            getOwnerEmail(
              group
            )
          ];
          return values.some(
            (value) => normalize(
              value
            ).includes(
              query
            )
          );
        }
      );
      return sortGroups(
        filtered
      );
    });
    const paginatedGroups = computed(() => {
      const perPage = Number(
        itemsPerPage.value || 10
      );
      const start = (currentPage.value - 1) * perPage;
      return filteredGroups.value.slice(
        start,
        start + perPage
      );
    });
    const displayedGroups = computed(() => {
      return props.useServerProcessing ? groupsList.value : paginatedGroups.value;
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
    const selectedGroups = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedGroups.value.map(
        (group) => group.id
      );
      if (checked) {
        selectedGroups.value = [
          .../* @__PURE__ */ new Set([
            ...selectedGroups.value,
            ...ids
          ])
        ];
      } else {
        selectedGroups.value = selectedGroups.value.filter(
          (id) => !ids.includes(id)
        );
      }
    };
    const toggleSelectGroup = (groupId) => {
      const index = selectedGroups.value.indexOf(
        groupId
      );
      if (index > -1) {
        selectedGroups.value.splice(
          index,
          1
        );
      } else {
        selectedGroups.value.push(
          groupId
        );
      }
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedGroups.value.length) {
        toast.warning(
          "Выберите группы характеристик для активации/деактивации."
        );
        return;
      }
      const idsToUpdate = [
        ...selectedGroups.value
      ];
      router.put(
        route(
          "admin.actions.marketAttributeGroups.bulkUpdateActivity"
        ),
        {
          ids: idsToUpdate,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localGroups.value = localGroups.value.map(
              (group) => {
                return idsToUpdate.includes(
                  group.id
                ) ? {
                  ...group,
                  activity: newActivity
                } : group;
              }
            );
            selectedGroups.value = [];
            toast.success(
              "Активность групп характеристик массово обновлена."
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
      if (!selectedGroups.value.length) {
        toast.warning(
          "Выберите хотя бы одну группу характеристик для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные группы характеристик?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.marketAttributeGroups.bulkDestroy"
        ),
        {
          data: {
            ids: selectedGroups.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedGroups.value = [];
            toast.success(
              "Массовое удаление групп характеристик успешно завершено."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              errors[errorKey] || "Произошла ошибка при удалении групп характеристик."
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
    const approveGroup = (group, status = 1, note = "") => {
      if (!(group == null ? void 0 : group.id)) {
        return;
      }
      router.put(
        route(
          "admin.actions.marketAttributeGroups.approve",
          {
            marketAttributeGroup: group.id
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
            patchLocalGroup(
              group.id,
              (node) => {
                node.moderation_status = status;
                node.is_approved = status === 1;
                node.moderation_note = note;
              }
            );
            toast.success(
              status === 1 ? "Группа характеристик одобрена." : "Группа характеристик отклонена."
            );
          },
          onError: () => {
            toast.error(
              "Ошибка модерации группы характеристик."
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
          "admin.actions.marketAttributeGroups.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Сортировка групп характеристик обновлена."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка сортировки групп характеристик:",
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
        title: unref(t)("marketAttributeGroups")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("marketAttributeGroups"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("marketAttributeGroups")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("marketAttributeGroups")), 1)
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
              href: _ctx.route("admin.marketAttributeGroups.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addMarketAttributeGroup"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addMarketAttributeGroup")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              "setting-key": "adminMarketAttributeGroupsProcessingMode",
              mode: __props.adminMarketAttributeGroupsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.groupsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.groupsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.groupsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.groupsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountMarketAttributeGroups"
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
            if (__props.groupsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$f, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.groupsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.groupsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              if (__props.groupsCount) {
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
            if (__props.groupsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredGroups.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.groups }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                groups: displayedGroups.value,
                "selected-groups": selectedGroups.value,
                "is-admin": isAdmin.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectGroup,
                onToggleAll: toggleAll,
                onApprove: approveGroup
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                groups: displayedGroups.value,
                "selected-groups": selectedGroups.value,
                "is-admin": isAdmin.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectGroup,
                onToggleAll: toggleAll,
                onApprove: approveGroup
              }, null, _parent2, _scopeId));
            }
            if (__props.groupsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredGroups.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.groups }, null, _parent2, _scopeId));
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
              onConfirm: deleteGroup,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$9, {
                      href: _ctx.route("admin.marketAttributeGroups.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addMarketAttributeGroup")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$a, {
                      "setting-key": "adminMarketAttributeGroupsProcessingMode",
                      mode: __props.adminMarketAttributeGroupsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.groupsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.groupsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.groupsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.groupsCount ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountMarketAttributeGroups"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.groupsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$f, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.groupsCount), 1)
                      ]),
                      _: 1
                    }),
                    __props.groupsCount ? (openBlock(), createBlock(_sfc_main$4, {
                      key: 0,
                      onChange: handleBulkAction
                    })) : createCommentVNode("", true),
                    createVNode(_sfc_main$g, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.groupsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredGroups.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.groups
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    groups: displayedGroups.value,
                    "selected-groups": selectedGroups.value,
                    "is-admin": isAdmin.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectGroup,
                    onToggleAll: toggleAll,
                    onApprove: approveGroup
                  }, null, 8, ["groups", "selected-groups", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    groups: displayedGroups.value,
                    "selected-groups": selectedGroups.value,
                    "is-admin": isAdmin.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectGroup,
                    onToggleAll: toggleAll,
                    onApprove: approveGroup
                  }, null, 8, ["groups", "selected-groups", "is-admin"])),
                  __props.groupsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredGroups.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.groups
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$j, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteGroup,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketAttributeGroups/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
