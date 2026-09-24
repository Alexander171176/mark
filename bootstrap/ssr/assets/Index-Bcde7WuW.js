import { mergeProps, unref, useSSRContext, onMounted, onUnmounted, computed, ref, watch, withCtx, createTextVNode, toDisplayString, createVNode, createBlock, createCommentVNode, openBlock } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderList, ssrRenderClass, ssrRenderComponent, ssrIncludeBooleanAttr, ssrRenderTeleport, ssrRenderStyle } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$i } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$c, a as _sfc_main$f, b as _sfc_main$g } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$b, a as _sfc_main$h } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$d } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$a } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$e } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$9 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$7 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$8 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$6 } from "./ModerationButton-D_ehimPY.js";
import "./LocaleSelectOption-BeLdazeX.js";
import "./ResponsiveNavLink-gtte0z5g.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "vuedraggable";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/Comment/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>─────────────────</option><option value="userNameAsc">${ssrInterpolate(unref(t)("user"))} A→Z</option><option value="userNameDesc">${ssrInterpolate(unref(t)("user"))} Z→A</option><option value="userEmailAsc">Email A→Z</option><option value="userEmailDesc">Email Z→A</option><option disabled>─────────────────</option><option value="contentAsc">${ssrInterpolate(unref(t)("comment"))} A→Z</option><option value="contentDesc">${ssrInterpolate(unref(t)("comment"))} Z→A</option><option value="typeAsc">${ssrInterpolate(unref(t)("type"))} A→Z</option><option value="typeDesc">${ssrInterpolate(unref(t)("type"))} Z→A</option><option value="commentableTitleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="commentableTitleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option disabled>─────────────────</option><option value="repliesDesc">${ssrInterpolate(unref(t)("answers"))} 9→0</option><option value="repliesAsc">${ssrInterpolate(unref(t)("answers"))} 0→9</option><option disabled>─────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/Comment/Sort/SortSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "CommentTable",
  __ssrInlineRender: true,
  props: {
    comments: { type: Array, default: () => [] },
    selectedComments: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
    "toggle-activity",
    "edit",
    "delete",
    "toggle-select",
    "toggle-all",
    "view-details",
    "approve-comment"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const formatDate = (dateString) => {
      if (!dateString) return "";
      const date = new Date(dateString);
      if (isNaN(date)) return "";
      return new Intl.DateTimeFormat("ru-RU", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
      }).format(date);
    };
    const moderationBadge = (status) => {
      const s = Number(status ?? 0);
      if (s === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (s === 2) {
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedComments.length)}</div>`);
      if (__props.comments.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (__props.comments.length > 0) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px"><div class="font-medium text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-left">${ssrInterpolate(unref(t)("name"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-left">${ssrInterpolate(unref(t)("comment"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("object"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center"><input type="checkbox"></div></th></tr></thead><tbody><!--[-->`);
        ssrRenderList(__props.comments, (comment) => {
          var _a, _b;
          _push(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"><div class="text-center">${ssrInterpolate(comment.id)}</div></td><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"><div class="flex flex-col"><div class="font-semibold text-blue-700 dark:text-blue-300">${ssrInterpolate(((_a = comment.user) == null ? void 0 : _a.name) || "—")}</div>`);
          if ((_b = comment.user) == null ? void 0 : _b.email) {
            _push(`<div class="text-[10px] text-gray-500 dark:text-gray-300 break-all font-semibold">${ssrInterpolate(comment.user.email)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="text-xs text-slate-500 dark:text-slate-300"> ID: ${ssrInterpolate(comment.user_id)}</div></div></td><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"><div class="text-xs text-left text-slate-600 dark:text-slate-200 cursor-pointer text-wrap"${ssrRenderAttr("title", formatDate(comment.updated_at))}>${ssrInterpolate(((comment == null ? void 0 : comment.content) || "").length > 160 ? (comment.content || "").slice(0, 160) + "…" : (comment == null ? void 0 : comment.content) || "")}</div></td><td class="px-2 first:pl-5 last:pr-5 py-1"><div class="flex flex-row items-center justify-center gap-1 text-xs font-semibold text-violet-700 dark:text-violet-300"><div>${ssrInterpolate(((comment == null ? void 0 : comment.commentable_type) || "").split("\\").pop())}</div><div>${ssrInterpolate(unref(t)("id"))}: ${ssrInterpolate(comment.commentable_id)}</div></div>`);
          if (comment.commentable_title) {
            _push(`<div class="text-xs text-fuchsia-700 dark:text-fuchsia-300 line-clamp-2">${ssrInterpolate(comment.commentable_title)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</td><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"><div class="flex items-center justify-center gap-1"><span class="${ssrRenderClass([moderationBadge(comment == null ? void 0 : comment.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", (comment == null ? void 0 : comment.moderation_note) && (comment == null ? void 0 : comment.moderated_at) ? `${comment.moderation_note} [${formatDate(comment.moderated_at)}]` : null)}>${ssrInterpolate(moderationBadge(comment == null ? void 0 : comment.moderation_status).text)}</span>`);
          _push(ssrRenderComponent(_sfc_main$6, {
            isAdmin: __props.isAdmin,
            status: (comment == null ? void 0 : comment.moderation_status) ?? 0,
            initialNote: (comment == null ? void 0 : comment.moderation_note) || "",
            mode: "toggle",
            onSubmit: ({ status, note }) => _ctx.$emit("approve-comment", comment, status, note)
          }, null, _parent));
          _push(`</div></td><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"><div class="flex justify-end space-x-2"><button${ssrRenderAttr("title", unref(t)("view"))} class="flex items-center py-1 px-0 rounded border border-slate-300 hover:border-blue-500 dark:border-blue-300 dark:hover:border-blue-100"><svg class="w-4 h-4 shrink-0 fill-current text-blue-500 mx-1" viewBox="0 0 16 16"><path d="M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z"></path></svg></button>`);
          _push(ssrRenderComponent(_sfc_main$7, {
            isActive: comment.activity,
            onToggleActivity: ($event) => _ctx.$emit("toggle-activity", comment),
            title: comment.activity ? unref(t)("enabled") : unref(t)("disabled")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$8, {
            onClick: ($event) => _ctx.$emit("delete", comment.id)
          }, null, _parent));
          _push(`</div></td><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"><div class="text-center"><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedComments.includes(comment.id)) ? " checked" : ""}></div></td></tr>`);
        });
        _push(`<!--]--></tbody></table>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/Comment/Table/CommentTable.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "CommentCardGrid",
  __ssrInlineRender: true,
  props: {
    comments: { type: Array, default: () => [] },
    selectedComments: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
    "toggle-activity",
    "edit",
    "delete",
    "toggle-select",
    "toggle-all",
    "view-details",
    "approve-comment"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const formatDate = (dateString) => {
      if (!dateString) return "";
      const date = new Date(dateString);
      if (isNaN(date)) return "";
      return new Intl.DateTimeFormat("ru-RU", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
      }).format(date);
    };
    const getShortType = (fullType) => {
      if (!fullType) return "";
      return fullType.split("\\").pop();
    };
    const moderationBadge = (status) => {
      const s = Number(status ?? 0);
      if (s === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (s === 2) {
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedComments.length)}</div>`);
      if (__props.comments.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (__props.comments.length) {
        _push(`<div class="p-3"><div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"><!--[-->`);
        ssrRenderList(__props.comments, (comment) => {
          var _a, _b;
          _push(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center space-x-2"><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"> ID: ${ssrInterpolate(comment.id)}</div></div><div class="flex items-center space-x-2"><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedComments.includes(comment.id)) ? " checked" : ""}></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-2"><div class="flex flex-col items-center justify-center"><div class="font-semibold text-blue-700 dark:text-blue-300 text-center break-words">${ssrInterpolate(((_a = comment.user) == null ? void 0 : _a.name) || "—")}</div>`);
          if ((_b = comment.user) == null ? void 0 : _b.email) {
            _push(`<div class="text-[10px] text-gray-500 dark:text-gray-300 break-all text-center font-semibold">${ssrInterpolate(comment.user.email)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="text-xs text-slate-700 dark:text-slate-300 font-semibold"> ID: ${ssrInterpolate(comment.user_id)}</div></div><div class="text-left"><div class="text-xs text-slate-600 dark:text-slate-200 font-semibold line-clamp-3 cursor-pointer"${ssrRenderAttr("title", comment.content)}>${ssrInterpolate(comment.content)}</div></div><div class="flex items-center justify-center space-x-2"><span class="font-semibold text-[10px] text-slate-600 dark:text-slate-400"${ssrRenderAttr("title", formatDate(comment.created_at))}>${ssrInterpolate(formatDate(comment.updated_at))}</span></div><div class="text-center space-y-0.5"><div class="flex items-center justify-center gap-2 text-sm font-semibold text-indigo-700 dark:text-indigo-300 cursor-pointer"><div>${ssrInterpolate(getShortType(comment.commentable_type))}</div><div>${ssrInterpolate(unref(t)("id"))}: ${ssrInterpolate(comment.commentable_id)}</div></div>`);
          if (comment.commentable_title) {
            _push(`<div class="text-xs text-fuchsia-700 dark:text-fuchsia-300 font-semibold line-clamp-2">${ssrInterpolate(comment.commentable_title)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="flex justify-center mt-1"><div class="flex items-center justify-center gap-1"><span class="${ssrRenderClass([moderationBadge(comment == null ? void 0 : comment.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", (comment == null ? void 0 : comment.moderation_note) && (comment == null ? void 0 : comment.moderated_at) ? `${comment.moderation_note} [${formatDate(comment.moderated_at)}]` : null)}>${ssrInterpolate(moderationBadge(comment == null ? void 0 : comment.moderation_status).text)}</span>`);
          _push(ssrRenderComponent(_sfc_main$6, {
            isAdmin: __props.isAdmin,
            status: (comment == null ? void 0 : comment.moderation_status) ?? 0,
            initialNote: (comment == null ? void 0 : comment.moderation_note) || "",
            mode: "toggle",
            onSubmit: ({ status, note }) => _ctx.$emit("approve-comment", comment, status, note)
          }, null, _parent));
          _push(`</div></div></div><div class="px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center justify-between space-x-2"><button${ssrRenderAttr("title", unref(t)("view"))} class="flex items-center py-1 px-0 rounded border border-slate-300 hover:border-blue-500 dark:border-blue-300 dark:hover:border-blue-100"><svg class="w-4 h-4 shrink-0 fill-current text-blue-500 mx-1" viewBox="0 0 16 16"><path d="M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z"></path></svg></button><div class="flex items-center space-x-1">`);
          _push(ssrRenderComponent(_sfc_main$7, {
            isActive: comment.activity,
            onToggleActivity: ($event) => _ctx.$emit("toggle-activity", comment),
            title: comment.activity ? unref(t)("enabled") : unref(t)("disabled")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$8, {
            onClick: ($event) => _ctx.$emit("delete", comment.id)
          }, null, _parent));
          _push(`</div></div></div></div>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/Comment/View/CommentCardGrid.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "CommentDetailsModal",
  __ssrInlineRender: true,
  props: {
    show: Boolean,
    comment: Object
    // Предполагается, что в этом объекте есть данные о пользователе, такие как user.name
  },
  emits: ["close"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const closeModal = () => {
      emits("close");
    };
    const closeOnEscape = (e) => {
      if (e.key === "Escape" && props.show) {
        closeModal();
      }
    };
    onMounted(() => document.addEventListener("keydown", closeOnEscape));
    onUnmounted(() => document.removeEventListener("keydown", closeOnEscape));
    const formatDate = (dateString) => {
      const date = new Date(dateString);
      return new Intl.DateTimeFormat("ru-RU", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
      }).format(date);
    };
    const moderationBadge = (status) => {
      const s = Number(status ?? 0);
      if (s === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (s === 2) {
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
      ssrRenderTeleport(_push, (_push2) => {
        var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l, _m;
        _push2(`<div style="${ssrRenderStyle(__props.show ? null : { display: "none" })}" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto" scroll-region><div style="${ssrRenderStyle(__props.show ? null : { display: "none" })}" class="fixed inset-0 transform transition-all"><div class="absolute inset-0 bg-slate-800 opacity-25"></div></div><div style="${ssrRenderStyle(__props.show ? null : { display: "none" })}" class="bg-slate-100 dark:bg-slate-900 border border-gray-400 rounded-lg shadow-xl transform transition-all max-w-lg w-full max-h-full sm:w-full sm:mx-auto relative overflow-y-auto"><button class="absolute top-0 right-1 m-1"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 hover:text-red-400 dark:text-gray-300 dark:hover:text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg></button><div class="px-3 py-1"><h3 class="text-center text-md font-semibold text-sky-700 dark:text-sky-300 pb-1 border-dashed border-b border-slate-400">${ssrInterpolate(unref(t)("commentDetails"))} - ${ssrInterpolate(unref(t)("id"))}: ${ssrInterpolate((_a = __props.comment) == null ? void 0 : _a.id)}</h3>`);
        if (__props.comment) {
          _push2(`<div class="my-2 flex flex-row items-center justify-center gap-2"><span class="font-semibold text-sm text-gray-900 dark:text-gray-100">${ssrInterpolate(unref(t)("status"))}: </span><span class="${ssrRenderClass([moderationBadge((_b = __props.comment) == null ? void 0 : _b.moderation_status).class, "text-xs px-3 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", ((_c = __props.comment) == null ? void 0 : _c.moderation_note) && ((_d = __props.comment) == null ? void 0 : _d.moderated_at) ? `${__props.comment.moderation_note} [${formatDate(__props.comment.moderated_at)}]` : null)}>${ssrInterpolate(moderationBadge((_e = __props.comment) == null ? void 0 : _e.moderation_status).text)}</span></div>`);
        } else {
          _push2(`<!---->`);
        }
        if (__props.comment) {
          _push2(`<div class="my-2 flex flex-row items-center justify-start gap-1"><span class="font-semibold text-sm text-gray-900 dark:text-gray-100">${ssrInterpolate(unref(t)("activity"))}: </span><p class="font-semibold text-sm text-rose-600 dark:text-rose-400">${ssrInterpolate(__props.comment.activity ? unref(t)("active") : unref(t)("inactive"))}</p></div>`);
        } else {
          _push2(`<!---->`);
        }
        if ((_f = __props.comment) == null ? void 0 : _f.user) {
          _push2(`<div class="my-2"><span class="font-semibold text-sm text-gray-900 dark:text-gray-100">${ssrInterpolate(unref(t)("userCommented"))}</span><div class="flex flex-col mt-1"><div class="flex flex-row items-center gap-1 text-sm font-semibold"><div class="text-slate-600 dark:text-slate-400"> [ID: ${ssrInterpolate(__props.comment.user_id)}] </div><div class="text-blue-700 dark:text-blue-300">${ssrInterpolate(((_g = __props.comment.user) == null ? void 0 : _g.name) || "—")}</div></div>`);
          if ((_h = __props.comment.user) == null ? void 0 : _h.email) {
            _push2(`<div class="ml-1 text-xs font-semibold text-gray-500 dark:text-gray-300 break-all">${ssrInterpolate(__props.comment.user.email)}</div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div></div>`);
        } else {
          _push2(`<!---->`);
        }
        if (__props.comment) {
          _push2(`<div class="my-2"><span class="font-semibold text-sm text-gray-900 dark:text-gray-100">${ssrInterpolate(unref(t)("comment"))} : </span><p class="font-semibold text-sm text-amber-700 dark:text-amber-300">${ssrInterpolate(__props.comment.content)}</p></div>`);
        } else {
          _push2(`<!---->`);
        }
        if ((_i = __props.comment) == null ? void 0 : _i.commentable_type) {
          _push2(`<div class="my-2"><div class="font-semibold text-sm text-gray-900 dark:text-gray-100">${ssrInterpolate(unref(t)("object"))} : </div><div class="flex flex-row items-center justify-start gap-1 text-sm font-semibold">`);
          if ((_j = __props.comment) == null ? void 0 : _j.commentable_id) {
            _push2(`<div class="text-slate-600 dark:text-slate-400"> [${ssrInterpolate(unref(t)("id"))}: ${ssrInterpolate(__props.comment.commentable_id)}] </div>`);
          } else {
            _push2(`<!---->`);
          }
          if ((_k = __props.comment) == null ? void 0 : _k.commentable_type) {
            _push2(`<div class="text-indigo-700 dark:text-indigo-300"> [${ssrInterpolate((((_l = __props.comment) == null ? void 0 : _l.commentable_type) || "").split("\\").pop())}] </div>`);
          } else {
            _push2(`<!---->`);
          }
          if ((_m = __props.comment) == null ? void 0 : _m.commentable_title) {
            _push2(`<div class="text-fuchsia-700 dark:text-fuchsia-300">${ssrInterpolate(__props.comment.commentable_title)}</div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div></div>`);
        } else {
          _push2(`<!---->`);
        }
        if (__props.comment) {
          _push2(`<div class="my-2 flex flex-row items-end justify-start gap-1"><span class="font-semibold text-sm text-gray-900 dark:text-gray-100">${ssrInterpolate(unref(t)("createdAt"))}: </span><p class="font-semibold text-xs italic text-slate-600 dark:text-slate-400">${ssrInterpolate(formatDate(__props.comment.created_at))}</p></div>`);
        } else {
          _push2(`<!---->`);
        }
        if (__props.comment) {
          _push2(`<div class="my-2 flex flex-row items-end justify-start gap-1"><span class="font-semibold text-sm text-gray-900 dark:text-gray-100">${ssrInterpolate(unref(t)("updatedAt"))}: </span><p class="font-semibold text-xs italic text-slate-600 dark:text-slate-400">${ssrInterpolate(formatDate(__props.comment.updated_at))}</p></div>`);
        } else {
          _push2(`<!---->`);
        }
        _push2(`<div class="my-1 sm:flex sm:flex-row-reverse space-x-2"><button type="button" class="flex justify-center items-center float-right rounded-xs border border-transparent shadow-sm px-2 py-0.5 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:w-auto sm:text-sm"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg><span class="ml-1">${ssrInterpolate(unref(t)("close"))}</span></button></div></div></div></div>`);
      }, "body", false, _parent);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/Comment/Modal/CommentDetailsModal.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    comments: { type: [Array, Object], default: () => [] },
    commentsCount: { type: Number, default: 0 },
    adminCommentsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminCommentsPerPage: { type: Number, default: 10 },
    adminCommentsDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    isAdmin: { type: Boolean, default: false },
    error: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t, locale } = useI18n();
    const toast = useToast();
    const props = __props;
    const isAdmin = computed(() => !!props.isAdmin);
    const viewMode = ref(localStorage.getItem("admin_view_mode_comments") || "table");
    watch(viewMode, (val) => {
      localStorage.setItem("admin_view_mode_comments", val);
    });
    const commentsList = computed(() => {
      var _a;
      if (Array.isArray(props.comments)) {
        return props.comments;
      }
      if (Array.isArray((_a = props.comments) == null ? void 0 : _a.data)) {
        return props.comments.data;
      }
      return [];
    });
    const localComments = ref([]);
    watch(
      commentsList,
      (newVal) => {
        localComments.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const patchLocal = (id, patch) => {
      const idx = localComments.value.findIndex((comment) => comment.id === id);
      if (idx !== -1) {
        localComments.value[idx] = {
          ...localComments.value[idx],
          ...patch
        };
      }
    };
    const itemsPerPage = ref(Number(props.adminCommentsPerPage || 10));
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountComments"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error((errors == null ? void 0 : errors.value) || "Ошибка обновления кол-ва элементов.")
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminCommentsDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortComments"),
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
            toast.info("Сортировка успешно изменена");
          },
          onError: (errors) => {
            toast.error((errors == null ? void 0 : errors.value) || "Ошибка обновления сортировки.");
          }
        }
      );
    });
    const currentPage = ref(1);
    const searchQuery = ref(props.search || "");
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const normalize = (value) => (value ?? "").toString().trim().toLowerCase();
    const moderationNum = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(value || 0).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const shortType = (fullType) => {
      return fullType ? fullType.split("\\").pop() : "";
    };
    const sortComments = (comments) => {
      const list = (comments || []).slice();
      if (sortParam.value === "activity") return list.filter((comment) => !!comment.activity);
      if (sortParam.value === "inactive") return list.filter((comment) => !comment.activity);
      if (sortParam.value === "moderationPending") return list.filter((comment) => moderationNum(comment == null ? void 0 : comment.moderation_status) === 0);
      if (sortParam.value === "moderationApproved") return list.filter((comment) => moderationNum(comment == null ? void 0 : comment.moderation_status) === 1);
      if (sortParam.value === "moderationRejected") return list.filter((comment) => moderationNum(comment == null ? void 0 : comment.moderation_status) === 2);
      const sortMap = {
        idAsc: (a, b) => (a.id ?? 0) - (b.id ?? 0),
        idDesc: (a, b) => (b.id ?? 0) - (a.id ?? 0),
        userNameAsc: (a, b) => {
          var _a, _b;
          return normalize((_a = a == null ? void 0 : a.user) == null ? void 0 : _a.name).localeCompare(normalize((_b = b == null ? void 0 : b.user) == null ? void 0 : _b.name), locale.value);
        },
        userNameDesc: (a, b) => {
          var _a, _b;
          return normalize((_a = b == null ? void 0 : b.user) == null ? void 0 : _a.name).localeCompare(normalize((_b = a == null ? void 0 : a.user) == null ? void 0 : _b.name), locale.value);
        },
        userEmailAsc: (a, b) => {
          var _a, _b;
          return normalize((_a = a == null ? void 0 : a.user) == null ? void 0 : _a.email).localeCompare(normalize((_b = b == null ? void 0 : b.user) == null ? void 0 : _b.email), locale.value);
        },
        userEmailDesc: (a, b) => {
          var _a, _b;
          return normalize((_a = b == null ? void 0 : b.user) == null ? void 0 : _a.email).localeCompare(normalize((_b = a == null ? void 0 : a.user) == null ? void 0 : _b.email), locale.value);
        },
        contentAsc: (a, b) => normalize(a == null ? void 0 : a.content).localeCompare(normalize(b == null ? void 0 : b.content), locale.value),
        contentDesc: (a, b) => normalize(b == null ? void 0 : b.content).localeCompare(normalize(a == null ? void 0 : a.content), locale.value),
        typeAsc: (a, b) => normalize(shortType(a == null ? void 0 : a.commentable_type)).localeCompare(normalize(shortType(b == null ? void 0 : b.commentable_type)), locale.value),
        typeDesc: (a, b) => normalize(shortType(b == null ? void 0 : b.commentable_type)).localeCompare(normalize(shortType(a == null ? void 0 : a.commentable_type)), locale.value),
        commentableTitleAsc: (a, b) => normalize(a == null ? void 0 : a.commentable_title).localeCompare(normalize(b == null ? void 0 : b.commentable_title), locale.value),
        commentableTitleDesc: (a, b) => normalize(b == null ? void 0 : b.commentable_title).localeCompare(normalize(a == null ? void 0 : a.commentable_title), locale.value),
        repliesAsc: (a, b) => (a.replies_count ?? 0) - (b.replies_count ?? 0),
        repliesDesc: (a, b) => (b.replies_count ?? 0) - (a.replies_count ?? 0),
        createdAtAsc: (a, b) => safeDate(a.created_at) - safeDate(b.created_at),
        createdAtDesc: (a, b) => safeDate(b.created_at) - safeDate(a.created_at),
        updatedAtAsc: (a, b) => safeDate(a.updated_at) - safeDate(b.updated_at),
        updatedAtDesc: (a, b) => safeDate(b.updated_at) - safeDate(a.updated_at),
        activityAsc: (a, b) => Number(a.activity) - Number(b.activity),
        activityDesc: (a, b) => Number(b.activity) - Number(a.activity),
        moderationStatusAsc: (a, b) => moderationNum(a == null ? void 0 : a.moderation_status) - moderationNum(b == null ? void 0 : b.moderation_status),
        moderationStatusDesc: (a, b) => moderationNum(b == null ? void 0 : b.moderation_status) - moderationNum(a == null ? void 0 : a.moderation_status)
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredComments = computed(() => {
      let filtered = localComments.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortComments(filtered);
      }
      filtered = filtered.filter((comment) => {
        var _a, _b, _c, _d, _e;
        const values = [
          comment == null ? void 0 : comment.id,
          comment == null ? void 0 : comment.content,
          comment == null ? void 0 : comment.moderation_note,
          comment == null ? void 0 : comment.commentable_type,
          shortType(comment == null ? void 0 : comment.commentable_type),
          comment == null ? void 0 : comment.commentable_title,
          (_a = comment == null ? void 0 : comment.user) == null ? void 0 : _a.name,
          (_b = comment == null ? void 0 : comment.user) == null ? void 0 : _b.email,
          (_c = comment == null ? void 0 : comment.moderator) == null ? void 0 : _c.name,
          (_d = comment == null ? void 0 : comment.moderator) == null ? void 0 : _d.email,
          (_e = comment == null ? void 0 : comment.parent) == null ? void 0 : _e.content
        ];
        return values.some((value) => normalize(value).includes(query));
      });
      return sortComments(filtered);
    });
    const paginatedComments = computed(() => {
      const perPage = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * perPage;
      return filteredComments.value.slice(start, start + perPage);
    });
    const displayedComments = computed(() => {
      return props.useServerProcessing ? commentsList.value : paginatedComments.value;
    });
    const selectedComments = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedComments.value.map((comment) => comment.id);
      if (checked) {
        selectedComments.value = [.../* @__PURE__ */ new Set([...selectedComments.value, ...ids])];
      } else {
        selectedComments.value = selectedComments.value.filter((id) => !ids.includes(id));
      }
    };
    const toggleSelectComment = (commentId) => {
      const index = selectedComments.value.indexOf(commentId);
      if (index > -1) {
        selectedComments.value.splice(index, 1);
      } else {
        selectedComments.value.push(commentId);
      }
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedComments.value.length) {
        toast.warning("Выберите комментарии для активации/деактивации");
        return;
      }
      const idsToUpdate = [...selectedComments.value];
      router.put(
        route("admin.actions.comments.bulkUpdateActivity"),
        { ids: idsToUpdate, activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localComments.value = localComments.value.map((comment) => {
              return idsToUpdate.includes(comment.id) ? { ...comment, activity: newActivity } : comment;
            });
            selectedComments.value = [];
            toast.success("Активность комментариев массово обновлена");
          },
          onError: (errors) => {
            const msg = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Не удалось массово обновить активность комментариев";
            toast.error(msg);
          }
        }
      );
    };
    const bulkDelete = async () => {
      var _a, _b;
      if (!selectedComments.value.length) {
        toast.warning("Выберите хотя бы один комментарий для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные комментарии?")) return;
      const ids = [...selectedComments.value];
      try {
        const res = await axios.delete(route("admin.actions.comments.bulkDestroy"), {
          data: { ids }
        });
        if ((_a = res == null ? void 0 : res.data) == null ? void 0 : _a.success) {
          localComments.value = localComments.value.filter((comment) => !ids.includes(comment.id));
          selectedComments.value = [];
          toast.success(res.data.message || "Массовое удаление комментариев успешно завершено.");
          return;
        }
        toast.error(((_b = res == null ? void 0 : res.data) == null ? void 0 : _b.message) || "Произошла ошибка при удалении комментариев.");
      } catch (error) {
        console.error("Ошибка массового удаления:", error);
        toast.error("Произошла ошибка при удалении комментариев.");
      }
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") {
        toggleAll({ target: { checked: true } });
      } else if (action === "deselectAll") {
        toggleAll({ target: { checked: false } });
      } else if (action === "activate") {
        bulkToggleActivity(true);
      } else if (action === "deactivate") {
        bulkToggleActivity(false);
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const showCommentDetailsModal = ref(false);
    const commentDetails = ref(null);
    const viewCommentDetails = (comment) => {
      commentDetails.value = comment;
      showCommentDetailsModal.value = true;
    };
    const closeCommentDetailsModal = () => {
      showCommentDetailsModal.value = false;
      commentDetails.value = null;
    };
    const showConfirmDeleteModal = ref(false);
    const commentToDeleteId = ref(null);
    const confirmDelete = (id) => {
      commentToDeleteId.value = id;
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      commentToDeleteId.value = null;
    };
    const deleteComment = () => {
      if (commentToDeleteId.value === null) return;
      const idToDelete = commentToDeleteId.value;
      router.delete(route("admin.comments.destroy", { comment: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          toast.success(`Комментарий "ID: ${idToDelete}" удален.`);
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          const errorMsg = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[errorKey]) || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Комментарий: ID: ${idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    const toggleActivity = (comment) => {
      const newActivity = !comment.activity;
      axios.put(route("admin.actions.comments.updateActivity", { comment: comment.id }), {
        activity: newActivity
      }).then((response) => {
        patchLocal(comment.id, { activity: response.data.activity });
        toast.success(response.data.message);
      }).catch((error) => {
        toast.error("Ошибка при изменении активности комментария.");
        console.error(error);
      });
    };
    const approveComment = (comment, status = 1, note = "") => {
      if (!isAdmin.value) {
        toast.error("Модерация доступна только администратору.");
        return;
      }
      if (!(comment == null ? void 0 : comment.id)) return;
      axios.put(
        route("admin.actions.comments.approve", { comment: comment.id }),
        {
          moderation_status: status,
          moderation_note: note
        }
      ).then((response) => {
        var _a;
        const resource = (_a = response.data) == null ? void 0 : _a.comment;
        const data = (resource == null ? void 0 : resource.data) ? resource.data : resource;
        if (data) {
          patchLocal(comment.id, {
            moderation_status: data.moderation_status,
            moderation_note: data.moderation_note,
            moderated_by: data.moderated_by,
            moderated_at: data.moderated_at
          });
        } else {
          patchLocal(comment.id, {
            moderation_status: status,
            moderation_note: note
          });
        }
        toast.success(response.data.message || "Статус модерации обновлён");
      }).catch((error) => {
        var _a;
        if (((_a = error == null ? void 0 : error.response) == null ? void 0 : _a.status) === 403) {
          toast.error("Доступ запрещён: только администратор может модерировать комментарии.");
          return;
        }
        toast.error("Ошибка при обновлении модерации комментария.");
        console.error(error);
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("comments")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("comments"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("comments")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("comments")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-end sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              "setting-key": "adminCommentsProcessingMode",
              mode: __props.adminCommentsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.commentsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.commentsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("search")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.commentsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("search")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.commentsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountComments"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$4, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (val) => sortParam.value = val
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.commentsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.commentsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.commentsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              if (__props.commentsCount) {
                _push2(ssrRenderComponent(_sfc_main$5, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              _push2(ssrRenderComponent(_sfc_main$f, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.commentsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredComments.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.comments }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$3, {
                isAdmin: isAdmin.value,
                comments: displayedComments.value,
                "selected-comments": selectedComments.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectComment,
                onToggleAll: toggleAll,
                onViewDetails: viewCommentDetails,
                onApproveComment: approveComment
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$2, {
                isAdmin: isAdmin.value,
                comments: displayedComments.value,
                "selected-comments": selectedComments.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectComment,
                onToggleAll: toggleAll,
                onViewDetails: viewCommentDetails,
                onApproveComment: approveComment
              }, null, _parent2, _scopeId));
            }
            _push2(ssrRenderComponent(_sfc_main$1, {
              show: showCommentDetailsModal.value,
              comment: commentDetails.value,
              onClose: closeCommentDetailsModal
            }, null, _parent2, _scopeId));
            if (__props.commentsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredComments.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.comments }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (props.error) {
              _push2(`<div class="mt-3 text-sm font-semibold text-rose-700 dark:text-rose-300"${_scopeId}>${ssrInterpolate(props.error)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$i, {
              show: showConfirmDeleteModal.value,
              onClose: closeModal,
              onCancel: closeModal,
              onConfirm: deleteComment,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-end sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$9, {
                      "setting-key": "adminCommentsProcessingMode",
                      mode: __props.adminCommentsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.commentsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.commentsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("search")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.commentsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("search")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.commentsCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$d, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountComments"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$4, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.commentsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.commentsCount), 1)
                      ]),
                      _: 1
                    }),
                    __props.commentsCount ? (openBlock(), createBlock(_sfc_main$5, {
                      key: 0,
                      onChange: handleBulkAction
                    })) : createCommentVNode("", true),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.commentsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredComments.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.comments
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$3, {
                    key: 5,
                    isAdmin: isAdmin.value,
                    comments: displayedComments.value,
                    "selected-comments": selectedComments.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectComment,
                    onToggleAll: toggleAll,
                    onViewDetails: viewCommentDetails,
                    onApproveComment: approveComment
                  }, null, 8, ["isAdmin", "comments", "selected-comments"])) : (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    isAdmin: isAdmin.value,
                    comments: displayedComments.value,
                    "selected-comments": selectedComments.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectComment,
                    onToggleAll: toggleAll,
                    onViewDetails: viewCommentDetails,
                    onApproveComment: approveComment
                  }, null, 8, ["isAdmin", "comments", "selected-comments"])),
                  createVNode(_sfc_main$1, {
                    show: showCommentDetailsModal.value,
                    comment: commentDetails.value,
                    onClose: closeCommentDetailsModal
                  }, null, 8, ["show", "comment"]),
                  __props.commentsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredComments.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.comments
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  props.error ? (openBlock(), createBlock("div", {
                    key: 8,
                    class: "mt-3 text-sm font-semibold text-rose-700 dark:text-rose-300"
                  }, toDisplayString(props.error), 1)) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteComment,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Blog/Comments/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
