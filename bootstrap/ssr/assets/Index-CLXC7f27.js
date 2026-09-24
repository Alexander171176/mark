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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketTag/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>─────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>─────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option value="urlAsc">URL A→Z</option><option value="urlDesc">URL Z→A</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option disabled>─────────────────</option><option value="colorAsc">${ssrInterpolate(unref(t)("typeColor"))} 0→F</option><option value="colorDesc">${ssrInterpolate(unref(t)("typeColor"))} F→0</option><option disabled>─────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="statusDraft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="statusPublished">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="statusArchived">${ssrInterpolate(unref(t)("statusArchived"))}</option><option disabled>─────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="showFromAtDesc">${ssrInterpolate(unref(t)("showFromAt"))} ↓</option><option value="showFromAtAsc">${ssrInterpolate(unref(t)("showFromAt"))} ↑</option><option value="showToAtDesc">${ssrInterpolate(unref(t)("showToAt"))} ↓</option><option value="showToAtAsc">${ssrInterpolate(unref(t)("showToAt"))} ↑</option><option disabled>─────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>─────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketTag/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "TagTable",
  __ssrInlineRender: true,
  props: {
    tags: { type: Array, default: () => [] },
    selectedTags: { type: Array, default: () => [] },
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
    const localTags = ref([]);
    watch(
      () => props.tags,
      (newVal) => {
        localTags.value = JSON.parse(
          JSON.stringify(
            Array.isArray(newVal) ? newVal : []
          )
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
        localTags.value.map(
          (tag) => tag.id
        )
      );
    };
    const allSelected = () => {
      if (!localTags.value.length) {
        return false;
      }
      return localTags.value.every(
        (tag) => props.selectedTags.includes(
          tag.id
        )
      );
    };
    const tagTranslation = (tag) => {
      return (tag == null ? void 0 : tag.translation) || {};
    };
    const tagTitle = (tag) => {
      var _a;
      return ((_a = tagTranslation(tag)) == null ? void 0 : _a.title) || `ID: ${tag == null ? void 0 : tag.id}`;
    };
    const tagShort = (tag) => {
      var _a;
      return ((_a = tagTranslation(tag)) == null ? void 0 : _a.short) || "";
    };
    const tagPublicUrl = (tag) => {
      if (!(tag == null ? void 0 : tag.url)) {
        return "#";
      }
      return `/market/tags/${encodeURIComponent(tag.url)}`;
    };
    const ownerTitle = (tag) => {
      const owner = tag == null ? void 0 : tag.owner;
      if (!owner) {
        return t("noData");
      }
      const values = [
        owner.name,
        owner.email
      ].filter(Boolean);
      return values.length ? values.join(" — ") : t("noData");
    };
    const ownerAvatar = (tag) => {
      var _a;
      return ((_a = tag == null ? void 0 : tag.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const getSafeIcon = (icon) => {
      if (!icon || typeof icon !== "string") {
        return null;
      }
      const trimmed = icon.trim();
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
    const truncateText = (text, maxLength = 70) => {
      if (!text) {
        return "";
      }
      const value = String(text);
      return value.length > maxLength ? `${value.slice(0, maxLength).trimEnd()}…` : value;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedTags.length)}</div>`);
      if (localTags.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localTags.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-1 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("owner"))}</div></th><th class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("icon"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("typeColor"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("title"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-1 py-1 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localTags.value,
          "onUpdate:modelValue": ($event) => localTags.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: tag }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-1 py-1 text-center cursor-move handle w-px"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-1 py-1 whitespace-nowrap w-px"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${ssrRenderAttr("title", `[${tag.sort}] / ${formatDate(tag.published_at)}`)}${_scopeId}>${ssrInterpolate(tag.id)}</div></td><td class="px-1 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(tag))}${ssrRenderAttr("title", ownerTitle(tag))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("owner"))}${_scopeId}></div><div class="text-[10px] font-semibold text-center text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(ownerTitle(tag))}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${_scopeId}>`);
              if (getSafeIcon(tag.icon)) {
                _push2(`<div class="w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center"${_scopeId}>${getSafeIcon(tag.icon) ?? ""}</div>`);
              } else {
                _push2(`<span class="text-slate-400 dark:text-slate-300"${_scopeId}> — </span>`);
              }
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center items-center gap-1"${_scopeId}><span class="inline-block w-5 h-5 rounded-sm border border-slate-400" style="${ssrRenderStyle({ backgroundColor: tag.color || "transparent" })}"${_scopeId}></span><span class="text-xs text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(tag.color || "—")}</span></div></td><td class="px-1 py-1"${_scopeId}><div class="flex flex-col items-start space-y-1"${_scopeId}><a${ssrRenderAttr("href", tagPublicUrl(tag))} target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md" style="${ssrRenderStyle({ borderColor: tag.color || "transparent" })}"${ssrRenderAttr("title", tag.show_from_at ? `${unref(t)("show")}: ${tag.show_from_at} / ${tag.show_to_at || "—"}` : formatDate(tag.published_at))}${_scopeId}><span style="${ssrRenderStyle({ color: tag.color || "#666666" })}"${_scopeId}>${ssrInterpolate(truncateText(tagTitle(tag)))}</span></a><div class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(truncateText(tag.url, 70))}</div>`);
              if (tagShort(tag)) {
                _push2(`<div class="text-[10px] text-cyan-700 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(truncateText(tagShort(tag), 80))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-300"${_scopeId}>${ssrInterpolate(tag.views ?? 0)}</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-center justify-center space-y-1"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(tag.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", tag.moderation_note && tag.moderated_at ? `${tag.moderation_note} [${formatDate(tag.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(tag.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isAdmin: __props.isAdmin,
                status: (tag == null ? void 0 : tag.moderation_status) ?? 0,
                initialNote: (tag == null ? void 0 : tag.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emits("approve", tag, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(getStatusLabel(tag.status))}</div></div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: tag.activity,
                title: tag.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emits("toggle-activity", tag)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.marketTags.edit", { marketTag: tag.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emits("delete", tag)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedTags.includes(tag.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                      title: `[${tag.sort}] / ${formatDate(tag.published_at)}`
                    }, toDisplayString(tag.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(tag),
                        title: ownerTitle(tag),
                        class: "h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("owner")
                      }, null, 8, ["src", "title", "alt"])
                    ]),
                    createVNode("div", { class: "text-[10px] font-semibold text-center text-slate-700 dark:text-slate-300" }, toDisplayString(ownerTitle(tag)), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      getSafeIcon(tag.icon) ? (openBlock(), createBlock("div", {
                        key: 0,
                        innerHTML: getSafeIcon(tag.icon),
                        class: "w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center"
                      }, null, 8, ["innerHTML"])) : (openBlock(), createBlock("span", {
                        key: 1,
                        class: "text-slate-400 dark:text-slate-300"
                      }, " — "))
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center items-center gap-1" }, [
                      createVNode("span", {
                        class: "inline-block w-5 h-5 rounded-sm border border-slate-400",
                        style: { backgroundColor: tag.color || "transparent" }
                      }, null, 4),
                      createVNode("span", { class: "text-xs text-slate-700 dark:text-slate-300" }, toDisplayString(tag.color || "—"), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1" }, [
                    createVNode("div", { class: "flex flex-col items-start space-y-1" }, [
                      createVNode("a", {
                        href: tagPublicUrl(tag),
                        target: "_blank",
                        rel: "noopener noreferrer",
                        class: "text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md",
                        style: { borderColor: tag.color || "transparent" },
                        title: tag.show_from_at ? `${unref(t)("show")}: ${tag.show_from_at} / ${tag.show_to_at || "—"}` : formatDate(tag.published_at)
                      }, [
                        createVNode("span", {
                          style: { color: tag.color || "#666666" }
                        }, toDisplayString(truncateText(tagTitle(tag))), 5)
                      ], 12, ["href", "title"]),
                      createVNode("div", { class: "text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(truncateText(tag.url, 70)), 1),
                      tagShort(tag) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] text-cyan-700 dark:text-cyan-300"
                      }, toDisplayString(truncateText(tagShort(tag), 80)), 1)) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-blue-600 dark:text-blue-300" }, toDisplayString(tag.views ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-col items-center justify-center space-y-1" }, [
                      createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                        createVNode("span", {
                          class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(tag.moderation_status).class],
                          title: tag.moderation_note && tag.moderated_at ? `${tag.moderation_note} [${formatDate(tag.moderated_at)}]` : null
                        }, toDisplayString(moderationBadge(tag.moderation_status).text), 11, ["title"]),
                        createVNode(_sfc_main$5, {
                          isAdmin: __props.isAdmin,
                          status: (tag == null ? void 0 : tag.moderation_status) ?? 0,
                          initialNote: (tag == null ? void 0 : tag.moderation_note) || "",
                          mode: "toggle",
                          onSubmit: ({ status, note }) => emits("approve", tag, status, note)
                        }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                      ]),
                      createVNode("div", { class: "text-[10px] text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(getStatusLabel(tag.status)), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-1" }, [
                      createVNode(_sfc_main$6, {
                        isActive: tag.activity,
                        title: tag.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emits("toggle-activity", tag)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.marketTags.edit", { marketTag: tag.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emits("delete", tag)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedTags.includes(tag.id),
                        onChange: ($event) => emits("toggle-select", tag.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketTag/Table/TagTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "TagCardGrid",
  __ssrInlineRender: true,
  props: {
    tags: { type: Array, default: () => [] },
    selectedTags: { type: Array, default: () => [] },
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
    const localTags = ref([]);
    const openedOwnerBlocks = ref([]);
    watch(
      () => props.tags,
      (newVal) => {
        localTags.value = JSON.parse(
          JSON.stringify(
            Array.isArray(newVal) ? newVal : []
          )
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
        localTags.value.map(
          (tag) => tag.id
        )
      );
    };
    const allSelected = () => {
      if (!localTags.value.length) {
        return false;
      }
      return localTags.value.every(
        (tag) => props.selectedTags.includes(
          tag.id
        )
      );
    };
    const tagTranslation = (tag) => {
      return (tag == null ? void 0 : tag.translation) || {};
    };
    const tagTitle = (tag) => {
      var _a;
      return ((_a = tagTranslation(tag)) == null ? void 0 : _a.title) || `ID: ${tag == null ? void 0 : tag.id}`;
    };
    const tagShort = (tag) => {
      var _a;
      return ((_a = tagTranslation(tag)) == null ? void 0 : _a.short) || "";
    };
    const tagLocale = (tag) => {
      var _a;
      return ((_a = tagTranslation(tag)) == null ? void 0 : _a.locale) || "";
    };
    const tagPublicUrl = (tag) => {
      if (!(tag == null ? void 0 : tag.url)) {
        return "#";
      }
      return `/market/tags/${encodeURIComponent(tag.url)}`;
    };
    const ownerName = (tag) => {
      var _a;
      return ((_a = tag == null ? void 0 : tag.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (tag) => {
      var _a;
      return ((_a = tag == null ? void 0 : tag.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (tag) => {
      const owner = tag == null ? void 0 : tag.owner;
      if (!owner) {
        return t("noData");
      }
      const values = [
        owner.name,
        owner.email
      ].filter(Boolean);
      return values.length ? values.join(" — ") : t("noData");
    };
    const ownerAvatar = (tag) => {
      var _a;
      return ((_a = tag == null ? void 0 : tag.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
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
      return value.length > maxLength ? `${value.slice(0, maxLength).trimEnd()}…` : value;
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
    const isOwnerBlockOpen = (tagId) => {
      return openedOwnerBlocks.value.includes(
        tagId
      );
    };
    const toggleOwnerBlock = (tagId) => {
      if (isOwnerBlockOpen(
        tagId
      )) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
          (id) => id !== tagId
        );
        return;
      }
      openedOwnerBlocks.value.push(
        tagId
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedTags.length)}</div>`);
      if (localTags.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localTags.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localTags.value,
          "onUpdate:modelValue": ($event) => localTags.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd,
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: tag }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${tagLocale(tag)}] : [${tag.sort}]`)}${_scopeId}> ID: ${ssrInterpolate(tag.id)}</div><button type="button" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"${ssrRenderAttr("title", isOwnerBlockOpen(tag.id) ? unref(t)("hideOwner") : unref(t)("showOwner"))}${_scopeId}><svg class="${ssrRenderClass([{ "rotate-180": isOwnerBlockOpen(tag.id) }, "w-4 h-4 transition-transform duration-200"])}" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"${_scopeId}></path></svg></button></div><div class="flex items-center space-x-2"${_scopeId}><span class="${ssrRenderClass([moderationBadge(tag.moderation_status).class, "text-[10px] px-2 py-0.5 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", tag.moderation_note && tag.moderated_at ? `${tag.moderation_note} [${formatDate(tag.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(tag.moderation_status).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedTags.includes(tag.id)) ? " checked" : ""}${_scopeId}></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-2"${_scopeId}><div style="${ssrRenderStyle(isOwnerBlockOpen(tag.id) ? null : { display: "none" })}" class="flex flex-col items-center justify-center text-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(tag))}${ssrRenderAttr("title", ownerTitle(tag))} class="h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("owner"))}${_scopeId}><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerName(tag))}${_scopeId}>${ssrInterpolate(ownerName(tag))}</div>`);
              if (ownerEmail(tag)) {
                _push2(`<div class="text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerEmail(tag))}${_scopeId}>${ssrInterpolate(ownerEmail(tag))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (tag.show_from_at) {
                _push2(`<div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("show"))}: ${ssrInterpolate(tag.show_from_at)} / ${ssrInterpolate(tag.show_to_at || "—")}</div>`);
              } else {
                _push2(`<div class="text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(formatDate(tag.published_at))}</div>`);
              }
              _push2(`</div><div class="flex justify-center items-center"${_scopeId}><a${ssrRenderAttr("href", tagPublicUrl(tag))} target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md" style="${ssrRenderStyle({ borderColor: tag.color || "transparent" })}"${ssrRenderAttr("title", tag.show_from_at ? `${unref(t)("show")}: ${tag.show_from_at} / ${tag.show_to_at || "—"}` : formatDate(tag.published_at))}${_scopeId}><span style="${ssrRenderStyle({ color: tag.color || "#666666" })}"${_scopeId}>${ssrInterpolate(truncateText(tagTitle(tag)))}</span></a></div><div class="text-center text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(truncateText(tag.url, 90))}</div>`);
              if (tag.color) {
                _push2(`<div class="flex items-center justify-center gap-1 font-semibold text-xs text-slate-700 dark:text-slate-300"${_scopeId}><span class="inline-block w-4 h-4 rounded-sm border border-slate-400" style="${ssrRenderStyle({ backgroundColor: tag.color })}"${_scopeId}></span><span${_scopeId}>${ssrInterpolate(tag.color)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              if (tagShort(tag)) {
                _push2(`<div class="font-semibold text-[12px] text-center text-cyan-700 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(truncateText(tagShort(tag), 120))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="flex items-center justify-center gap-3 text-[11px] font-semibold text-slate-600 dark:text-slate-200"${_scopeId}><div class="flex items-center justify-center space-x-1"${ssrRenderAttr("title", unref(t)("views"))}${_scopeId}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"${_scopeId}><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"${_scopeId}></path></svg><span class="text-[12px] text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(tag.views ?? 0)}</span></div></div><div class="font-semibold text-center text-[11px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("status"))}: </span> ${ssrInterpolate(getStatusLabel(tag.status))}</div><div class="flex justify-center space-x-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(tag.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", tag.moderation_note && tag.moderated_at ? `${tag.moderation_note} [${formatDate(tag.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(tag.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isAdmin: __props.isAdmin,
                status: (tag == null ? void 0 : tag.moderation_status) ?? 0,
                initialNote: (tag == null ? void 0 : tag.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => emits("approve", tag, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: tag.activity,
                title: tag.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emits("toggle-activity", tag)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.marketTags.edit", { marketTag: tag.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onDelete: ($event) => emits("delete", tag)
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
                        title: `[${tagLocale(tag)}] : [${tag.sort}]`
                      }, " ID: " + toDisplayString(tag.id), 9, ["title"]),
                      createVNode("button", {
                        type: "button",
                        class: "text-slate-400 hover:text-blue-600 dark:hover:text-blue-300",
                        title: isOwnerBlockOpen(tag.id) ? unref(t)("hideOwner") : unref(t)("showOwner"),
                        onClick: withModifiers(($event) => toggleOwnerBlock(tag.id), ["prevent"])
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: ["w-4 h-4 transition-transform duration-200", { "rotate-180": isOwnerBlockOpen(tag.id) }],
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
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border font-semibold", moderationBadge(tag.moderation_status).class],
                        title: tag.moderation_note && tag.moderated_at ? `${tag.moderation_note} [${formatDate(tag.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(tag.moderation_status).text), 11, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedTags.includes(tag.id),
                        onChange: ($event) => emits("toggle-select", tag.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-2" }, [
                    withDirectives(createVNode("div", { class: "flex flex-col items-center justify-center text-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(tag),
                        title: ownerTitle(tag),
                        class: "h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("owner")
                      }, null, 8, ["src", "title", "alt"]),
                      createVNode("div", {
                        class: "mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1",
                        title: ownerName(tag)
                      }, toDisplayString(ownerName(tag)), 9, ["title"]),
                      ownerEmail(tag) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1",
                        title: ownerEmail(tag)
                      }, toDisplayString(ownerEmail(tag)), 9, ["title"])) : createCommentVNode("", true),
                      tag.show_from_at ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "text-center text-[10px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(unref(t)("show")) + ": " + toDisplayString(tag.show_from_at) + " / " + toDisplayString(tag.show_to_at || "—"), 1)) : (openBlock(), createBlock("div", {
                        key: 2,
                        class: "text-center text-[10px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(formatDate(tag.published_at)), 1))
                    ], 512), [
                      [vShow, isOwnerBlockOpen(tag.id)]
                    ]),
                    createVNode("div", { class: "flex justify-center items-center" }, [
                      createVNode("a", {
                        href: tagPublicUrl(tag),
                        target: "_blank",
                        rel: "noopener noreferrer",
                        class: "text-xs font-semibold text-center bg-white dark:bg-slate-600 w-fit border-2 px-2 py-0.5 rounded-md",
                        style: { borderColor: tag.color || "transparent" },
                        title: tag.show_from_at ? `${unref(t)("show")}: ${tag.show_from_at} / ${tag.show_to_at || "—"}` : formatDate(tag.published_at)
                      }, [
                        createVNode("span", {
                          style: { color: tag.color || "#666666" }
                        }, toDisplayString(truncateText(tagTitle(tag))), 5)
                      ], 12, ["href", "title"])
                    ]),
                    createVNode("div", { class: "text-center text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(truncateText(tag.url, 90)), 1),
                    tag.color ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex items-center justify-center gap-1 font-semibold text-xs text-slate-700 dark:text-slate-300"
                    }, [
                      createVNode("span", {
                        class: "inline-block w-4 h-4 rounded-sm border border-slate-400",
                        style: { backgroundColor: tag.color }
                      }, null, 4),
                      createVNode("span", null, toDisplayString(tag.color), 1)
                    ])) : createCommentVNode("", true),
                    tagShort(tag) ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "font-semibold text-[12px] text-center text-cyan-700 dark:text-cyan-300"
                    }, toDisplayString(truncateText(tagShort(tag), 120)), 1)) : createCommentVNode("", true),
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
                        createVNode("span", { class: "text-[12px] text-slate-700 dark:text-slate-200" }, toDisplayString(tag.views ?? 0), 1)
                      ], 8, ["title"])
                    ]),
                    createVNode("div", { class: "font-semibold text-center text-[11px] text-fuchsia-700 dark:text-fuchsia-300" }, [
                      createVNode("span", null, toDisplayString(unref(t)("status")) + ": ", 1),
                      createTextVNode(" " + toDisplayString(getStatusLabel(tag.status)), 1)
                    ]),
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(tag.moderation_status).class],
                        title: tag.moderation_note && tag.moderated_at ? `${tag.moderation_note} [${formatDate(tag.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(tag.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$5, {
                        isAdmin: __props.isAdmin,
                        status: (tag == null ? void 0 : tag.moderation_status) ?? 0,
                        initialNote: (tag == null ? void 0 : tag.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => emits("approve", tag, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$6, {
                        isActive: tag.activity,
                        title: tag.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emits("toggle-activity", tag)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.marketTags.edit", { marketTag: tag.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onDelete: ($event) => emits("delete", tag)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketTag/View/TagCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminMarketTagsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminMarketTagsPerPage: { type: Number, default: 10 },
    adminMarketTagsDefaultSort: { type: String, default: "idDesc" },
    tags: { type: [Array, Object], default: () => [] },
    tagsCount: { type: Number, default: 0 },
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
    const getTagTranslation = (tag) => (tag == null ? void 0 : tag.translation) || {};
    const getTagTitleValue = (tag) => {
      var _a;
      return ((_a = getTagTranslation(tag)) == null ? void 0 : _a.title) || "";
    };
    const getTagTitle = (tag) => getTagTitleValue(tag) || `ID: ${tag == null ? void 0 : tag.id}`;
    const getTagSubtitle = (tag) => {
      var _a;
      return ((_a = getTagTranslation(tag)) == null ? void 0 : _a.subtitle) || "";
    };
    const getTagShort = (tag) => {
      var _a;
      return ((_a = getTagTranslation(tag)) == null ? void 0 : _a.short) || "";
    };
    const getTagDescription = (tag) => {
      var _a;
      return ((_a = getTagTranslation(tag)) == null ? void 0 : _a.description) || "";
    };
    const getOwnerName = (tag) => {
      var _a;
      return ((_a = tag == null ? void 0 : tag.owner) == null ? void 0 : _a.name) || "";
    };
    const getOwnerEmail = (tag) => {
      var _a;
      return ((_a = tag == null ? void 0 : tag.owner) == null ? void 0 : _a.email) || "";
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
      localStorage.getItem("admin_view_mode_market_tags") || "cards"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_market_tags",
        value
      );
    });
    const itemsPerPage = ref(
      props.adminMarketTagsPerPage || 10
    );
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountMarketTags"),
        {
          value: newVal
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(
              `Показ ${newVal} тегов на странице.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления кол-ва тегов."
            );
          }
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminMarketTagsDefaultSort || "idDesc"
    );
    watch(sortParam, (newVal) => {
      router.put(
        route("admin.settings.updateAdminSortMarketTags"),
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
              "Сортировка тегов успешно изменена."
            );
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления сортировки тегов."
            );
          }
        }
      );
    });
    const localTags = ref([]);
    const tagsList = computed(() => {
      var _a, _b, _c, _d;
      if (Array.isArray(props.tags)) {
        return props.tags;
      }
      if (Array.isArray((_a = props.tags) == null ? void 0 : _a.data)) {
        return props.tags.data;
      }
      if (Array.isArray((_c = (_b = props.tags) == null ? void 0 : _b.data) == null ? void 0 : _c.data)) {
        return props.tags.data.data;
      }
      if (Array.isArray((_d = props.tags) == null ? void 0 : _d.resource)) {
        return props.tags.resource;
      }
      return [];
    });
    watch(
      tagsList,
      (newVal) => {
        localTags.value = JSON.parse(
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
    const tagToDeleteId = ref(null);
    const tagToDeleteTitle = ref("");
    const confirmDelete = (tagOrId, title = null) => {
      if (tagOrId && typeof tagOrId === "object") {
        tagToDeleteId.value = tagOrId.id;
        tagToDeleteTitle.value = title || getTagTitle(tagOrId);
      } else {
        tagToDeleteId.value = tagOrId;
        tagToDeleteTitle.value = title || `ID: ${tagOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      tagToDeleteId.value = null;
      tagToDeleteTitle.value = "";
    };
    const deleteTag = () => {
      if (tagToDeleteId.value === null) {
        return;
      }
      const idToDelete = tagToDeleteId.value;
      const titleToDelete = tagToDeleteTitle.value;
      router.delete(
        route(
          "admin.marketTags.destroy",
          {
            marketTag: idToDelete
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Тег "${titleToDelete || "ID: " + idToDelete}" удалён.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
            toast.error(
              `${errorMsg} (Тег: ${titleToDelete || "ID: " + idToDelete})`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    const patchLocalTag = (tagId, callback) => {
      const index = localTags.value.findIndex(
        (tag) => tag.id === tagId
      );
      if (index !== -1) {
        callback(
          localTags.value[index]
        );
      }
    };
    const toggleActivity = (tag) => {
      const newActivity = !tag.activity;
      const title = getTagTitle(tag);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.marketTags.updateActivity",
          {
            marketTag: tag.id
          }
        ),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalTag(
              tag.id,
              (node) => {
                node.activity = newActivity;
              }
            );
            toast.success(
              `Тег "${title}" ${actionText}.`
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
    const byIdDesc = (a, b) => {
      return safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byNumberAsc = (field) => (a, b) => {
      return safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || byIdDesc(a, b);
    };
    const byNumberDesc = (field) => (a, b) => {
      return safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || byIdDesc(a, b);
    };
    const byDateAsc = (field) => (a, b) => {
      return safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || byIdDesc(a, b);
    };
    const byDateDesc = (field) => (a, b) => {
      return safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || byIdDesc(a, b);
    };
    const byStringAsc = (valueResolver) => (a, b) => {
      return normalize(
        valueResolver(a)
      ).localeCompare(
        normalize(
          valueResolver(b)
        ),
        locale.value
      ) || byIdDesc(a, b);
    };
    const byStringDesc = (valueResolver) => (a, b) => {
      return normalize(
        valueResolver(b)
      ).localeCompare(
        normalize(
          valueResolver(a)
        ),
        locale.value
      ) || byIdDesc(a, b);
    };
    const filterWithIdDesc = (list, callback) => {
      return list.filter(callback).sort(byIdDesc);
    };
    const sortTags = (tags) => {
      const list = Array.isArray(tags) ? tags.slice() : [];
      if (sortParam.value === "activity") {
        return filterWithIdDesc(
          list,
          (tag) => !!tag.activity
        );
      }
      if (sortParam.value === "inactive") {
        return filterWithIdDesc(
          list,
          (tag) => !tag.activity
        );
      }
      if (sortParam.value === "statusDraft") {
        return filterWithIdDesc(
          list,
          (tag) => (tag == null ? void 0 : tag.status) === "draft"
        );
      }
      if (sortParam.value === "statusPublished") {
        return filterWithIdDesc(
          list,
          (tag) => (tag == null ? void 0 : tag.status) === "published"
        );
      }
      if (sortParam.value === "statusArchived") {
        return filterWithIdDesc(
          list,
          (tag) => (tag == null ? void 0 : tag.status) === "archived"
        );
      }
      if (sortParam.value === "moderationPending") {
        return filterWithIdDesc(
          list,
          (tag) => moderationNum(
            tag == null ? void 0 : tag.moderation_status
          ) === 0
        );
      }
      if (sortParam.value === "moderationApproved") {
        return filterWithIdDesc(
          list,
          (tag) => moderationNum(
            tag == null ? void 0 : tag.moderation_status
          ) === 1
        );
      }
      if (sortParam.value === "moderationRejected") {
        return filterWithIdDesc(
          list,
          (tag) => moderationNum(
            tag == null ? void 0 : tag.moderation_status
          ) === 2
        );
      }
      const sortMap = {
        idAsc: (a, b) => safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        idDesc: byIdDesc,
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        titleAsc: byStringAsc(
          getTagTitleValue
        ),
        titleDesc: byStringDesc(
          getTagTitleValue
        ),
        urlAsc: byStringAsc(
          (tag) => tag == null ? void 0 : tag.url
        ),
        urlDesc: byStringDesc(
          (tag) => tag == null ? void 0 : tag.url
        ),
        colorAsc: byStringAsc(
          (tag) => tag == null ? void 0 : tag.color
        ),
        colorDesc: byStringDesc(
          (tag) => tag == null ? void 0 : tag.color
        ),
        activityAsc: byNumberAsc(
          "activity"
        ),
        activityDesc: byNumberDesc(
          "activity"
        ),
        viewsAsc: byNumberAsc(
          "views"
        ),
        viewsDesc: byNumberDesc(
          "views"
        ),
        statusAsc: byStringAsc(
          (tag) => tag == null ? void 0 : tag.status
        ),
        statusDesc: byStringDesc(
          (tag) => tag == null ? void 0 : tag.status
        ),
        moderationStatusAsc: (a, b) => {
          return moderationNum(
            a == null ? void 0 : a.moderation_status
          ) - moderationNum(
            b == null ? void 0 : b.moderation_status
          ) || byIdDesc(a, b);
        },
        moderationStatusDesc: (a, b) => {
          return moderationNum(
            b == null ? void 0 : b.moderation_status
          ) - moderationNum(
            a == null ? void 0 : a.moderation_status
          ) || byIdDesc(a, b);
        },
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
      const sorter = sortMap[sortParam.value];
      return sorter ? list.sort(sorter) : list;
    };
    const filteredTags = computed(() => {
      let filtered = localTags.value || [];
      const query = normalize(
        searchQuery.value
      );
      if (!query) {
        return sortTags(
          filtered
        );
      }
      filtered = filtered.filter(
        (tag) => {
          const values = [
            tag == null ? void 0 : tag.url,
            tag == null ? void 0 : tag.icon,
            tag == null ? void 0 : tag.color,
            tag == null ? void 0 : tag.status,
            tag == null ? void 0 : tag.moderation_note,
            getTagTitleValue(tag),
            getTagSubtitle(tag),
            getTagShort(tag),
            getTagDescription(tag),
            getOwnerName(tag),
            getOwnerEmail(tag)
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
      return sortTags(
        filtered
      );
    });
    const paginatedTags = computed(() => {
      const perPage = Number(
        itemsPerPage.value || 10
      );
      const start = (currentPage.value - 1) * perPage;
      return filteredTags.value.slice(
        start,
        start + perPage
      );
    });
    const displayedTags = computed(() => {
      return props.useServerProcessing ? tagsList.value : paginatedTags.value;
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
    const selectedTags = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = Boolean(
        (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false
      );
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedTags.value.map(
        (tag) => tag.id
      );
      if (checked) {
        selectedTags.value = [
          .../* @__PURE__ */ new Set([
            ...selectedTags.value,
            ...ids
          ])
        ];
        return;
      }
      selectedTags.value = selectedTags.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectTag = (tagId) => {
      const index = selectedTags.value.indexOf(
        tagId
      );
      if (index > -1) {
        selectedTags.value.splice(
          index,
          1
        );
        return;
      }
      selectedTags.value.push(
        tagId
      );
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedTags.value.length) {
        toast.warning(
          "Выберите теги для активации/деактивации."
        );
        return;
      }
      const idsToUpdate = [
        ...selectedTags.value
      ];
      router.put(
        route(
          "admin.actions.marketTags.bulkUpdateActivity"
        ),
        {
          ids: idsToUpdate,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localTags.value = localTags.value.map(
              (tag) => {
                return idsToUpdate.includes(
                  tag.id
                ) ? {
                  ...tag,
                  activity: newActivity
                } : tag;
              }
            );
            selectedTags.value = [];
            toast.success(
              "Активность тегов массово обновлена."
            );
          },
          onError: (errors) => {
            const message = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности.";
            toast.error(
              message
            );
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedTags.value.length) {
        toast.warning(
          "Выберите хотя бы один тег для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные теги?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.marketTags.bulkDestroy"
        ),
        {
          data: {
            ids: selectedTags.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedTags.value = [];
            toast.success(
              "Массовое удаление тегов успешно завершено."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              errors[errorKey] || "Произошла ошибка при удалении тегов."
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
    const approveTag = (tag, status = 1, note = "") => {
      if (!(tag == null ? void 0 : tag.id)) {
        return;
      }
      const moderationStatus = Number(
        status ?? 0
      );
      router.put(
        route(
          "admin.actions.marketTags.approve",
          {
            marketTag: tag.id
          }
        ),
        {
          moderation_status: moderationStatus,
          moderation_note: note
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalTag(
              tag.id,
              (node) => {
                node.moderation_status = moderationStatus;
                node.is_pending = moderationStatus === 0;
                node.is_approved = moderationStatus === 1;
                node.is_rejected = moderationStatus === 2;
                node.moderation_note = note;
              }
            );
            toast.success(
              moderationStatus === 1 ? "Тег одобрен." : "Тег отклонён."
            );
          },
          onError: () => {
            toast.error(
              "Ошибка модерации тега."
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
          "admin.actions.marketTags.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Сортировка тегов обновлена."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка сортировки тегов:",
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
        title: unref(t)("marketTags")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("marketTags"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("marketTags")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("marketTags")), 1)
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
              href: _ctx.route("admin.marketTags.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addMarketTag"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addMarketTag")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              "setting-key": "adminMarketTagsProcessingMode",
              mode: __props.adminMarketTagsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.tagsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.tagsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.tagsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.tagsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountMarketTags"
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
            if (__props.tagsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$f, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.tagsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.tagsCount), 1)
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
            if (__props.tagsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredTags.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.tags }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                tags: displayedTags.value,
                "selected-tags": selectedTags.value,
                "is-admin": isAdmin.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectTag,
                onToggleAll: toggleAll,
                onApprove: approveTag
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                tags: displayedTags.value,
                "selected-tags": selectedTags.value,
                "is-admin": isAdmin.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectTag,
                onToggleAll: toggleAll,
                onApprove: approveTag
              }, null, _parent2, _scopeId));
            }
            if (__props.tagsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredTags.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.tags }, null, _parent2, _scopeId));
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
              onConfirm: deleteTag,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$9, {
                      href: _ctx.route("admin.marketTags.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addMarketTag")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$a, {
                      "setting-key": "adminMarketTagsProcessingMode",
                      mode: __props.adminMarketTagsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.tagsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.tagsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.tagsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.tagsCount ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountMarketTags"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (value) => sortParam.value = value
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.tagsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$f, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.tagsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$g, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.tagsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredTags.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.tags
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    tags: displayedTags.value,
                    "selected-tags": selectedTags.value,
                    "is-admin": isAdmin.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectTag,
                    onToggleAll: toggleAll,
                    onApprove: approveTag
                  }, null, 8, ["tags", "selected-tags", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    tags: displayedTags.value,
                    "selected-tags": selectedTags.value,
                    "is-admin": isAdmin.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectTag,
                    onToggleAll: toggleAll,
                    onApprove: approveTag
                  }, null, 8, ["tags", "selected-tags", "is-admin"])),
                  __props.tagsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredTags.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.tags
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$j, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteTag,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketTags/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
