import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, computed, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent, ssrRenderClass } from "vue/server-renderer";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogTag/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>─────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>─────────────────</option><option value="nameAsc">${ssrInterpolate(unref(t)("name"))} A→Z</option><option value="nameDesc">${ssrInterpolate(unref(t)("name"))} Z→A</option><option value="slugAsc">Slug A→Z</option><option value="slugDesc">Slug Z→A</option><option disabled>─────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option value="articlesDesc">${ssrInterpolate(unref(t)("articles"))} 9→0</option><option value="articlesAsc">${ssrInterpolate(unref(t)("articles"))} 0→9</option><option disabled>─────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>─────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogTag/Sort/SortSelect.vue");
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
    "edit",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all",
    "approve"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const localTags = ref([]);
    watch(
      () => props.tags,
      (newVal) => {
        localTags.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      const newOrderIds = localTags.value.map((tag) => tag.id);
      emits("update-sort-order", newOrderIds);
    };
    const tagTranslation = (tag) => (tag == null ? void 0 : tag.translation) || {};
    const tagName = (tag) => {
      var _a;
      return ((_a = tagTranslation(tag)) == null ? void 0 : _a.name) || `ID: ${tag == null ? void 0 : tag.id}`;
    };
    const tagLocale = (tag) => {
      var _a;
      return ((_a = tagTranslation(tag)) == null ? void 0 : _a.locale) || "";
    };
    const getSafeIcon = (icon) => {
      if (!icon) return null;
      const trimmed = icon.trim();
      if (trimmed.startsWith("<svg") && trimmed.endsWith("</svg>")) {
        return trimmed;
      }
      return null;
    };
    const formatDate = (dateStr) => {
      if (!dateStr) return "";
      const date = new Date(dateStr);
      if (isNaN(date)) return "";
      return date.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    const ownerTitle = (tag) => {
      const owner = tag == null ? void 0 : tag.owner;
      if (!owner) return t("noData");
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (tag) => {
      var _a;
      return ((_a = tag == null ? void 0 : tag.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedTags.length)}</div>`);
      if (localTags.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (__props.tags.length > 0) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-1 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("owner"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24"><path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path><path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path><path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path><path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path><path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"></path></svg></div></th><th class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("localization"))}><svg class="w-8 h-8 fill-current shrink-0" viewBox="0 0 640 512"><path d="M0 128C0 92.7 28.7 64 64 64l192 0 48 0 16 0 256 0c35.3 0 64 28.7 64 64l0 256c0 35.3-28.7 64-64 64l-256 0-16 0-48 0L64 448c-35.3 0-64-28.7-64-64L0 128zm320 0l0 256 256 0 0-256-256 0zM178.3 175.9c-3.2-7.2-10.4-11.9-18.3-11.9s-15.1 4.7-18.3 11.9l-64 144c-4.5 10.1 .1 21.9 10.2 26.4s21.9-.1 26.4-10.2l8.9-20.1 73.6 0 8.9 20.1c4.5 10.1 16.3 14.6 26.4 10.2s14.6-16.3 10.2-26.4l-64-144zM160 233.2L179 276l-38 0 19-42.8zM448 164c11 0 20 9 20 20l0 4 44 0 16 0c11 0 20 9 20 20s-9 20-20 20l-2 0-1.6 4.5c-8.9 24.4-22.4 46.6-39.6 65.4c.9 .6 1.8 1.1 2.7 1.6l18.9 11.3c9.5 5.7 12.5 18 6.9 27.4s-18 12.5-27.4 6.9l-18.9-11.3c-4.5-2.7-8.8-5.5-13.1-8.5c-10.6 7.5-21.9 14-34 19.4l-3.6 1.6c-10.1 4.5-21.9-.1-26.4-10.2s.1-21.9 10.2-26.4l3.6-1.6c6.4-2.9 12.6-6.1 18.5-9.8l-12.2-12.2c-7.8-7.8-7.8-20.5 0-28.3s20.5-7.8 28.3 0l14.6 14.6 .5 .5c12.4-13.1 22.5-28.3 29.8-45L448 228l-72 0c-11 0-20-9-20-20s9-20 20-20l52 0 0-4c0-11 9-20 20-20z"></path></svg></div></th><th class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("icon"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("name"))}</div></th><th class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px"><div class="text-center font-medium"><input type="checkbox"${ssrIncludeBooleanAttr(localTags.value.length && localTags.value.every((tag) => __props.selectedTags.includes(tag.id))) ? " checked" : ""}></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localTags.value,
          "onUpdate:modelValue": ($event) => localTags.value = $event,
          onEnd: handleDragEnd,
          itemKey: "id",
          handle: ".handle"
        }, {
          item: withCtx(({ element: tag }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-1 py-3 text-center cursor-move handle w-px"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${_scopeId}>${ssrInterpolate(tag.id)}</div></td><td class="first:pl-11 last:pr-11 py-1"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(tag))}${ssrRenderAttr("title", ownerTitle(tag))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("author"))}${_scopeId}></div></td><td class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px"${_scopeId}><div class="${ssrRenderClass([tag.activity ? "bg-blue-500 text-white" : "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100", "text-xs px-1.5 py-0.5 rounded-sm border border-gray-400 uppercase"])}"${ssrRenderAttr("title", unref(t)("localization"))}${_scopeId}>${ssrInterpolate(tagLocale(tag).toUpperCase())}</div></td><td class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px"${_scopeId}>`);
              if (getSafeIcon(tag.icon)) {
                _push2(`<div class="w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center"${_scopeId}>${getSafeIcon(tag.icon) ?? ""}</div>`);
              } else {
                _push2(`<div class="flex justify-center items-center h-full text-slate-400"${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16"${_scopeId}><path d="M8 8a3 3 0 100-6 3 3 0 000 6zm2-3a2 2 0 11-4 0 2 2 0 014 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"${_scopeId}></path></svg></div>`);
              }
              _push2(`</td><td class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap"${_scopeId}><div class="text-left"${_scopeId}><a${ssrRenderAttr("href", `/blog/tags/${encodeURIComponent(tag.slug)}`)} class="text-sky-700 dark:text-sky-200 hover:underline hover:text-amber-700 dark:hover:text-amber-200" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", tagName(tag))}${_scopeId}>${ssrInterpolate(tagName(tag))}</a></div></td><td class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}>${ssrInterpolate(tag.views)}</div></td><td class="px-1 first:pl-11 last:pr-11 py-1 whitespace-nowrap"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(tag.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", tag.moderation_note && tag.moderated_at ? `${tag.moderation_note} [${formatDate(tag.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(tag.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isAdmin: __props.isAdmin,
                status: (tag == null ? void 0 : tag.moderation_status) ?? 0,
                initialNote: (tag == null ? void 0 : tag.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => _ctx.$emit("approve", tag, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: tag.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", tag),
                title: tag.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.blogTags.edit", { blogTag: tag.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onClick: ($event) => _ctx.$emit("delete", tag)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedTags.includes(tag.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
            } else {
              return [
                createVNode("tr", { class: "text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800" }, [
                  createVNode("td", { class: "px-1 py-3 text-center cursor-move handle w-px" }, [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 text-gray-500 dark:text-gray-300",
                      fill: "currentColor",
                      viewBox: "0 0 20 20"
                    }, [
                      createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                    ]))
                  ]),
                  createVNode("td", { class: "px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px" }, [
                    createVNode("div", { class: "text-center text-blue-600 dark:text-blue-200" }, toDisplayString(tag.id), 1)
                  ]),
                  createVNode("td", { class: "first:pl-11 last:pr-11 py-1" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(tag),
                        title: ownerTitle(tag),
                        class: "h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("author")
                      }, null, 8, ["src", "title", "alt"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px" }, [
                    createVNode("div", {
                      class: ["text-xs px-1.5 py-0.5 rounded-sm border border-gray-400 uppercase", tag.activity ? "bg-blue-500 text-white" : "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100"],
                      title: unref(t)("localization")
                    }, toDisplayString(tagLocale(tag).toUpperCase()), 11, ["title"])
                  ]),
                  createVNode("td", { class: "px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap w-px" }, [
                    getSafeIcon(tag.icon) ? (openBlock(), createBlock("div", {
                      key: 0,
                      innerHTML: getSafeIcon(tag.icon),
                      class: "w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center"
                    }, null, 8, ["innerHTML"])) : (openBlock(), createBlock("div", {
                      key: 1,
                      class: "flex justify-center items-center h-full text-slate-400"
                    }, [
                      (openBlock(), createBlock("svg", {
                        class: "w-4 h-4",
                        fill: "currentColor",
                        viewBox: "0 0 16 16"
                      }, [
                        createVNode("path", { d: "M8 8a3 3 0 100-6 3 3 0 000 6zm2-3a2 2 0 11-4 0 2 2 0 014 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" })
                      ]))
                    ]))
                  ]),
                  createVNode("td", { class: "px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-left" }, [
                      createVNode("a", {
                        href: `/blog/tags/${encodeURIComponent(tag.slug)}`,
                        class: "text-sky-700 dark:text-sky-200 hover:underline hover:text-amber-700 dark:hover:text-amber-200",
                        target: "_blank",
                        rel: "noopener noreferrer",
                        title: tagName(tag)
                      }, toDisplayString(tagName(tag)), 9, ["href", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, toDisplayString(tag.views), 1)
                  ]),
                  createVNode("td", { class: "px-1 first:pl-11 last:pr-11 py-1 whitespace-nowrap" }, [
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
                        onSubmit: ({ status, note }) => _ctx.$emit("approve", tag, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-2" }, [
                      createVNode(_sfc_main$6, {
                        isActive: tag.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", tag),
                        title: tag.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.blogTags.edit", { blogTag: tag.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onClick: ($event) => _ctx.$emit("delete", tag)
                      }, null, 8, ["onClick"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-3 first:pl-8 last:pr-8 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedTags.includes(tag.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", tag.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogTag/Table/TagTable.vue");
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
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const localTags = ref([]);
    watch(
      () => props.tags,
      (newVal) => {
        localTags.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      const newOrderIds = localTags.value.map((tag) => tag.id);
      emits("update-sort-order", newOrderIds);
    };
    const tagTranslation = (tag) => (tag == null ? void 0 : tag.translation) || {};
    const tagName = (tag) => {
      var _a;
      return ((_a = tagTranslation(tag)) == null ? void 0 : _a.name) || `ID: ${tag == null ? void 0 : tag.id}`;
    };
    const tagShort = (tag) => {
      var _a;
      return ((_a = tagTranslation(tag)) == null ? void 0 : _a.short) || "";
    };
    const tagLocale = (tag) => {
      var _a;
      return ((_a = tagTranslation(tag)) == null ? void 0 : _a.locale) || "";
    };
    const getSafeIcon = (icon) => {
      if (!icon) return null;
      const trimmed = icon.trim();
      if (trimmed.startsWith("<svg") && trimmed.endsWith("</svg>")) {
        return trimmed;
      }
      return null;
    };
    const formatDate = (dateStr) => {
      if (!dateStr) return "";
      const date = new Date(dateStr);
      if (isNaN(date)) return "";
      return date.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    const truncateText = (text, maxLength = 80) => {
      if (!text) return "";
      return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + "…" : text;
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
      if (!owner) return t("noData");
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (tag) => {
      var _a;
      return ((_a = tag == null ? void 0 : tag.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedTags.length)}</div>`);
      if (localTags.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(localTags.value.length && localTags.value.every((tag) => __props.selectedTags.includes(tag.id))) ? " checked" : ""}></label>`);
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
          onEnd: handleDragEnd,
          handle: ".drag-handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: tag }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="drag-handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${tagLocale(tag)}] : [${tag.sort}]`)}${_scopeId}> ID: ${ssrInterpolate(tag.id)}</div><div class="${ssrRenderClass([tag.activity ? "bg-blue-500 text-white" : "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100", "text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 uppercase"])}"${ssrRenderAttr("title", unref(t)("localization"))}${_scopeId}>${ssrInterpolate(tagLocale(tag).toUpperCase())}</div></div><div class="flex items-center space-x-2"${_scopeId}>`);
              if ((tag.views ?? 0) > 0) {
                _push2(`<div class="flex items-center space-x-1"${_scopeId}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"${_scopeId}><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"${_scopeId}></path></svg><span class="text-[10px] text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("views"))}${_scopeId}>${ssrInterpolate(tag.views ?? 0)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedTags.includes(tag.id)) ? " checked" : ""}${_scopeId}></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-2"${_scopeId}><div class="flex flex-col items-center justify-center text-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(tag))}${ssrRenderAttr("title", ownerTitle(tag))} class="h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("author"))}${_scopeId}><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerName(tag))}${_scopeId}>${ssrInterpolate(ownerName(tag))}</div>`);
              if (ownerEmail(tag)) {
                _push2(`<div class="text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerEmail(tag))}${_scopeId}>${ssrInterpolate(ownerEmail(tag))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="flex items-center justify-center text-center"${_scopeId}><div class="flex items-center justify-center space-x-2 max-w-full"${_scopeId}><div class="flex items-center justify-center shrink-0"${_scopeId}>`);
              if (getSafeIcon(tag.icon)) {
                _push2(`<div class="w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center"${_scopeId}>${getSafeIcon(tag.icon) ?? ""}</div>`);
              } else {
                _push2(`<svg class="w-4 h-4 text-slate-500 dark:text-slate-300" fill="currentColor" viewBox="0 0 16 16"${_scopeId}><path d="M8 8a3 3 0 100-6 3 3 0 000 6zm2-3a2 2 0 11-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"${_scopeId}></path></svg>`);
              }
              _push2(`</div><a${ssrRenderAttr("href", `/blog/tags/${encodeURIComponent(tag.slug)}`)} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-sky-700 dark:text-sky-200 hover:text-amber-700 dark:hover:text-amber-200 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", tagName(tag))}${_scopeId}>${ssrInterpolate(tagName(tag))}</a></div></div><div class="font-semibold text-[12px] text-center text-teal-700 dark:text-teal-200"${_scopeId}>${ssrInterpolate(truncateText(tagShort(tag)))}</div><div class="flex justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(tag.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", tag.moderation_note && tag.moderated_at ? `${tag.moderation_note} [${formatDate(tag.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(tag.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isAdmin: __props.isAdmin,
                status: (tag == null ? void 0 : tag.moderation_status) ?? 0,
                initialNote: (tag == null ? void 0 : tag.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => _ctx.$emit("approve", tag, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: tag.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", tag),
                title: tag.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.blogTags.edit", { blogTag: tag.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onClick: ($event) => _ctx.$emit("delete", tag)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div>`);
            } else {
              return [
                createVNode("div", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
                  createVNode("header", { class: "flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "drag-handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100",
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
                      createVNode("div", {
                        class: ["text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 uppercase", tag.activity ? "bg-blue-500 text-white" : "bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100"],
                        title: unref(t)("localization")
                      }, toDisplayString(tagLocale(tag).toUpperCase()), 11, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      (tag.views ?? 0) > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "flex items-center space-x-1"
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
                        createVNode("span", {
                          class: "text-[10px] text-slate-700 dark:text-slate-200",
                          title: unref(t)("views")
                        }, toDisplayString(tag.views ?? 0), 9, ["title"])
                      ])) : createCommentVNode("", true),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedTags.includes(tag.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", tag.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-2" }, [
                    createVNode("div", { class: "flex flex-col items-center justify-center text-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(tag),
                        title: ownerTitle(tag),
                        class: "h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600",
                        alt: unref(t)("author")
                      }, null, 8, ["src", "title", "alt"]),
                      createVNode("div", {
                        class: "mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1",
                        title: ownerName(tag)
                      }, toDisplayString(ownerName(tag)), 9, ["title"]),
                      ownerEmail(tag) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1",
                        title: ownerEmail(tag)
                      }, toDisplayString(ownerEmail(tag)), 9, ["title"])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "flex items-center justify-center text-center" }, [
                      createVNode("div", { class: "flex items-center justify-center space-x-2 max-w-full" }, [
                        createVNode("div", { class: "flex items-center justify-center shrink-0" }, [
                          getSafeIcon(tag.icon) ? (openBlock(), createBlock("div", {
                            key: 0,
                            innerHTML: getSafeIcon(tag.icon),
                            class: "w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center"
                          }, null, 8, ["innerHTML"])) : (openBlock(), createBlock("svg", {
                            key: 1,
                            class: "w-4 h-4 text-slate-500 dark:text-slate-300",
                            fill: "currentColor",
                            viewBox: "0 0 16 16"
                          }, [
                            createVNode("path", { d: "M8 8a3 3 0 100-6 3 3 0 000 6zm2-3a2 2 0 11-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z" })
                          ]))
                        ]),
                        createVNode("a", {
                          href: `/blog/tags/${encodeURIComponent(tag.slug)}`,
                          target: "_blank",
                          rel: "noopener noreferrer",
                          class: "text-sm font-semibold text-sky-700 dark:text-sky-200 hover:text-amber-700 dark:hover:text-amber-200 hover:underline line-clamp-2 text-center",
                          title: tagName(tag)
                        }, toDisplayString(tagName(tag)), 9, ["href", "title"])
                      ])
                    ]),
                    createVNode("div", { class: "font-semibold text-[12px] text-center text-teal-700 dark:text-teal-200" }, toDisplayString(truncateText(tagShort(tag))), 1),
                    createVNode("div", { class: "flex justify-center gap-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(tag.moderation_status).class],
                        title: tag.moderation_note && tag.moderated_at ? `${tag.moderation_note} [${formatDate(tag.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(tag.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$5, {
                        isAdmin: __props.isAdmin,
                        status: (tag == null ? void 0 : tag.moderation_status) ?? 0,
                        initialNote: (tag == null ? void 0 : tag.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => _ctx.$emit("approve", tag, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$6, {
                        isActive: tag.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", tag),
                        title: tag.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.blogTags.edit", { blogTag: tag.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onClick: ($event) => _ctx.$emit("delete", tag)
                      }, null, 8, ["onClick"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogTag/View/TagCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    tags: { type: [Array, Object], default: () => [] },
    tagsCount: { type: Number, default: 0 },
    adminBlogTagsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminBlogTagsPerPage: { type: Number, default: 20 },
    adminBlogTagsDefaultSort: { type: String, default: "idDesc" },
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
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
    const getTagName = (tag) => {
      var _a;
      return ((_a = getTagTranslation(tag)) == null ? void 0 : _a.name) || `ID: ${tag == null ? void 0 : tag.id}`;
    };
    const getTagShort = (tag) => {
      var _a;
      return ((_a = getTagTranslation(tag)) == null ? void 0 : _a.short) || "";
    };
    const getTagDescription = (tag) => {
      var _a;
      return ((_a = getTagTranslation(tag)) == null ? void 0 : _a.description) || "";
    };
    const normalize = (value) => (value ?? "").toString().trim().toLowerCase();
    const moderationNum = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(value || 0).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const viewMode = ref(localStorage.getItem("admin_view_mode_tags") || "cards");
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_tags", value);
    });
    const itemsPerPage = ref(props.adminBlogTagsPerPage || 20);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountBlogTags"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminBlogTagsDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortBlogTags"),
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
            toast.error(errors.value || "Ошибка обновления сортировки.");
          }
        }
      );
    });
    const tagsList = computed(() => {
      var _a;
      if (Array.isArray(props.tags)) return props.tags;
      if (Array.isArray((_a = props.tags) == null ? void 0 : _a.data)) return props.tags.data;
      return [];
    });
    const localTags = ref([]);
    watch(
      tagsList,
      (newVal) => {
        localTags.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const showConfirmDeleteModal = ref(false);
    const tagToDeleteId = ref(null);
    const tagToDeleteName = ref("");
    const confirmDelete = (tagOrId, name = null) => {
      if (typeof tagOrId === "object") {
        tagToDeleteId.value = tagOrId.id;
        tagToDeleteName.value = name || getTagName(tagOrId);
      } else {
        tagToDeleteId.value = tagOrId;
        tagToDeleteName.value = name || `ID: ${tagOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      tagToDeleteId.value = null;
      tagToDeleteName.value = "";
    };
    const deleteTag = () => {
      if (tagToDeleteId.value === null) return;
      const idToDelete = tagToDeleteId.value;
      const nameToDelete = tagToDeleteName.value;
      router.delete(route("admin.blogTags.destroy", { blogTag: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Тег "${nameToDelete || "ID: " + idToDelete}" удалён.`),
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Тег: ${nameToDelete || "ID: " + idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    const patchLocalTag = (tagId, callback) => {
      const index = localTags.value.findIndex((tag) => tag.id === tagId);
      if (index !== -1) {
        callback(localTags.value[index]);
      }
    };
    const toggleActivity = (tag) => {
      const newActivity = !tag.activity;
      const name = getTagName(tag);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route("admin.actions.blogTags.updateActivity", { blogTag: tag.id }),
        { activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalTag(tag.id, (node) => {
              node.activity = newActivity;
            });
            toast.success(`Тег "${name}" ${actionText}.`);
          },
          onError: (errors) => {
            toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${name}".`);
          }
        }
      );
    };
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const sortTags = (tags) => {
      const list = (tags || []).slice();
      if (sortParam.value === "activity") return list.filter((tag) => !!tag.activity);
      if (sortParam.value === "inactive") return list.filter((tag) => !tag.activity);
      if (sortParam.value === "moderationPending") return list.filter((tag) => moderationNum(tag == null ? void 0 : tag.moderation_status) === 0);
      if (sortParam.value === "moderationApproved") return list.filter((tag) => moderationNum(tag == null ? void 0 : tag.moderation_status) === 1);
      if (sortParam.value === "moderationRejected") return list.filter((tag) => moderationNum(tag == null ? void 0 : tag.moderation_status) === 2);
      const sortMap = {
        idAsc: (a, b) => (a.id ?? 0) - (b.id ?? 0),
        idDesc: (a, b) => (b.id ?? 0) - (a.id ?? 0),
        sortAsc: (a, b) => (a.sort ?? 0) - (b.sort ?? 0),
        sortDesc: (a, b) => (b.sort ?? 0) - (a.sort ?? 0),
        nameAsc: (a, b) => normalize(getTagName(a)).localeCompare(normalize(getTagName(b)), locale.value),
        nameDesc: (a, b) => normalize(getTagName(b)).localeCompare(normalize(getTagName(a)), locale.value),
        slugAsc: (a, b) => normalize(a == null ? void 0 : a.slug).localeCompare(normalize(b == null ? void 0 : b.slug), locale.value),
        slugDesc: (a, b) => normalize(b == null ? void 0 : b.slug).localeCompare(normalize(a == null ? void 0 : a.slug), locale.value),
        activityAsc: (a, b) => Number(a.activity) - Number(b.activity),
        activityDesc: (a, b) => Number(b.activity) - Number(a.activity),
        viewsAsc: (a, b) => (a.views ?? 0) - (b.views ?? 0),
        viewsDesc: (a, b) => (b.views ?? 0) - (a.views ?? 0),
        articlesAsc: (a, b) => (a.articles_count ?? 0) - (b.articles_count ?? 0),
        articlesDesc: (a, b) => (b.articles_count ?? 0) - (a.articles_count ?? 0),
        createdAtAsc: (a, b) => safeDate(a.created_at) - safeDate(b.created_at),
        createdAtDesc: (a, b) => safeDate(b.created_at) - safeDate(a.created_at),
        updatedAtAsc: (a, b) => safeDate(a.updated_at) - safeDate(b.updated_at),
        updatedAtDesc: (a, b) => safeDate(b.updated_at) - safeDate(a.updated_at),
        moderationStatusAsc: (a, b) => moderationNum(a == null ? void 0 : a.moderation_status) - moderationNum(b == null ? void 0 : b.moderation_status),
        moderationStatusDesc: (a, b) => moderationNum(b == null ? void 0 : b.moderation_status) - moderationNum(a == null ? void 0 : a.moderation_status),
        ownerNameAsc: (a, b) => {
          var _a, _b;
          return normalize((_a = a == null ? void 0 : a.owner) == null ? void 0 : _a.name).localeCompare(normalize((_b = b == null ? void 0 : b.owner) == null ? void 0 : _b.name), locale.value);
        },
        ownerNameDesc: (a, b) => {
          var _a, _b;
          return normalize((_a = b == null ? void 0 : b.owner) == null ? void 0 : _a.name).localeCompare(normalize((_b = a == null ? void 0 : a.owner) == null ? void 0 : _b.name), locale.value);
        },
        ownerEmailAsc: (a, b) => {
          var _a, _b;
          return normalize((_a = a == null ? void 0 : a.owner) == null ? void 0 : _a.email).localeCompare(normalize((_b = b == null ? void 0 : b.owner) == null ? void 0 : _b.email), locale.value);
        },
        ownerEmailDesc: (a, b) => {
          var _a, _b;
          return normalize((_a = b == null ? void 0 : b.owner) == null ? void 0 : _a.email).localeCompare(normalize((_b = a == null ? void 0 : a.owner) == null ? void 0 : _b.email), locale.value);
        }
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredTags = computed(() => {
      let filtered = localTags.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortTags(filtered);
      }
      filtered = filtered.filter((tag) => {
        var _a, _b, _c, _d;
        const values = [
          tag == null ? void 0 : tag.id,
          tag == null ? void 0 : tag.slug,
          tag == null ? void 0 : tag.icon,
          tag == null ? void 0 : tag.views,
          tag == null ? void 0 : tag.moderation_note,
          getTagName(tag),
          getTagShort(tag),
          getTagDescription(tag),
          (_a = tag == null ? void 0 : tag.owner) == null ? void 0 : _a.name,
          (_b = tag == null ? void 0 : tag.owner) == null ? void 0 : _b.email,
          (_c = tag == null ? void 0 : tag.moderator) == null ? void 0 : _c.name,
          (_d = tag == null ? void 0 : tag.moderator) == null ? void 0 : _d.email
        ];
        return values.some((value) => normalize(value).includes(query));
      });
      return sortTags(filtered);
    });
    const paginatedTags = computed(() => {
      const perPage = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * perPage;
      return filteredTags.value.slice(start, start + perPage);
    });
    const displayedTags = computed(() => {
      return props.useServerProcessing ? tagsList.value : paginatedTags.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const selectedTags = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedTags.value.map((tag) => tag.id);
      if (checked) {
        selectedTags.value = [.../* @__PURE__ */ new Set([...selectedTags.value, ...ids])];
      } else {
        selectedTags.value = selectedTags.value.filter((id) => !ids.includes(id));
      }
    };
    const toggleSelectTag = (tagId) => {
      const index = selectedTags.value.indexOf(tagId);
      if (index > -1) {
        selectedTags.value.splice(index, 1);
      } else {
        selectedTags.value.push(tagId);
      }
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedTags.value.length) {
        toast.warning("Выберите теги для активации/деактивации");
        return;
      }
      const idsToUpdate = [...selectedTags.value];
      router.put(
        route("admin.actions.blogTags.bulkUpdateActivity"),
        { ids: idsToUpdate, activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localTags.value = localTags.value.map((tag) => {
              if (idsToUpdate.includes(tag.id)) {
                return { ...tag, activity: newActivity };
              }
              return tag;
            });
            selectedTags.value = [];
            toast.success("Активность тегов массово обновлена");
          },
          onError: (errors) => {
            const msg = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности";
            toast.error(msg);
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedTags.value.length) {
        toast.warning("Выберите хотя бы один тег для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные теги?")) return;
      router.delete(route("admin.actions.blogTags.bulkDestroy"), {
        data: { ids: selectedTags.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          selectedTags.value = [];
          toast.success("Массовое удаление тегов успешно завершено.");
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          toast.error(errors[errorKey] || "Произошла ошибка при удалении тегов.");
        }
      });
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
    const approveTag = (tag, status = 1, note = "") => {
      if (!(tag == null ? void 0 : tag.id)) return;
      router.put(
        route("admin.actions.blogTags.approve", { blogTag: tag.id }),
        { moderation_status: status, moderation_note: note },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalTag(tag.id, (node) => {
              node.moderation_status = status;
              node.is_approved = status === 1;
              node.moderation_note = note;
            });
            toast.success(status === 1 ? "Тег одобрен" : "Тег отклонён");
          },
          onError: () => toast.error("Ошибка модерации тега")
        }
      );
    };
    const handleSortOrderUpdate = (newOrderIds) => {
      const items = newOrderIds.map((id, index) => ({
        id,
        sort: index
      }));
      if (!items.length) return;
      router.put(
        route("admin.actions.blogTags.updateSortBulk"),
        { items },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.success("Сортировка тегов обновлена"),
          onError: (errors) => {
            console.error("Ошибка сортировки тегов:", errors);
            toast.error(errors.message || "Ошибка обновления сортировки");
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("tags")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("tags"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("tags")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("tags")), 1)
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
              href: _ctx.route("admin.blogTags.create")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16"${_scopeId2}><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current opacity-50 shrink-0",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" })
                    ]))
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("addTag"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addTag")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              "setting-key": "adminBlogTagsProcessingMode",
              mode: __props.adminBlogTagsProcessingMode,
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
                  "update-route": "admin.settings.updateAdminCountBlogTags"
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
              if (__props.tagsCount) {
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
                      href: _ctx.route("admin.blogTags.create")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current opacity-50 shrink-0",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" })
                        ]))
                      ]),
                      default: withCtx(() => [
                        createTextVNode(" " + toDisplayString(unref(t)("addTag")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$a, {
                      "setting-key": "adminBlogTagsProcessingMode",
                      mode: __props.adminBlogTagsProcessingMode,
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
                      "update-route": "admin.settings.updateAdminCountBlogTags"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
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
                    __props.tagsCount ? (openBlock(), createBlock(_sfc_main$4, {
                      key: 0,
                      onChange: handleBulkAction
                    })) : createCommentVNode("", true),
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Blog/BlogTags/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
