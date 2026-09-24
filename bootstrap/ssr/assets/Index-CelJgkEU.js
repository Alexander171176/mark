import { mergeProps, unref, useSSRContext, ref, computed, watch, resolveComponent, withCtx, createVNode, createTextVNode, toDisplayString, createBlock, createCommentVNode, openBlock } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderList, ssrRenderClass, ssrRenderStyle, ssrIncludeBooleanAttr, ssrRenderComponent } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { usePage, router } from "@inertiajs/vue3";
import draggable from "vuedraggable";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$8 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$i } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$e } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$c, a as _sfc_main$f, b as _sfc_main$g } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$a } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$b, a as _sfc_main$h } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$d } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$9 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$5 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$6 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$7 } from "./DeleteIconButton-DLv2Mr1x.js";
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
const _sfc_main$4 = {
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
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Cms/CmsPage/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>───────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>───────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option value="urlAsc">URL A→Z</option><option value="urlDesc">URL Z→A</option><option disabled>───────────────────</option><option value="levelAsc">${ssrInterpolate(unref(t)("level"))} 0→9</option><option value="levelDesc">${ssrInterpolate(unref(t)("level"))} 9→0</option><option value="parentAsc">${ssrInterpolate(unref(t)("parentPage"))} 0→9</option><option value="parentDesc">${ssrInterpolate(unref(t)("parentPage"))} 9→0</option><option disabled>───────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>───────────────────</option><option value="inMenuDesc">${ssrInterpolate(unref(t)("printInMenu"))} ON→OFF</option><option value="inMenuAsc">${ssrInterpolate(unref(t)("printInMenu"))} OFF→ON</option><option value="inMenu">${ssrInterpolate(unref(t)("showInMenu"))}</option><option value="notInMenu">${ssrInterpolate(unref(t)("notShowInMenu"))}</option><option disabled>───────────────────</option><option value="inFooterDesc">Footer ON→OFF</option><option value="inFooterAsc">Footer OFF→ON</option><option value="inFooter">${ssrInterpolate(unref(t)("showInFooter"))}</option><option value="notInFooter">${ssrInterpolate(unref(t)("notShowInFooter"))}</option><option disabled>───────────────────</option><option value="showContentDesc">HTML ON→OFF</option><option value="showContentAsc">HTML OFF→ON</option><option value="showContent">${ssrInterpolate(unref(t)("showHtml"))}</option><option value="notShowContent">${ssrInterpolate(unref(t)("notShowHtml"))}</option><option disabled>───────────────────</option><option value="showSeoDesc">SEO ON→OFF</option><option value="showSeoAsc">SEO OFF→ON</option><option value="showSeo">${ssrInterpolate(unref(t)("showSeo"))}</option><option value="notShowSeo">${ssrInterpolate(unref(t)("notShowSeo"))}</option><option disabled>───────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option disabled>───────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>───────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("status"))} A→Z</option><option value="statusDesc">${ssrInterpolate(unref(t)("status"))} Z→A</option><option value="statusDraft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="statusPublished">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="statusArchived">${ssrInterpolate(unref(t)("statusArchived"))}</option><option disabled>───────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option><option disabled>───────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Cms/CmsPage/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "PageCardGrid",
  __ssrInlineRender: true,
  props: {
    pages: { type: Array, default: () => [] },
    selectedPages: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
    "toggle-activity",
    "delete",
    "toggle-select",
    "toggle-all",
    "toggle-menu",
    "toggle-footer",
    "toggle-content",
    "toggle-seo"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const emit = __emit;
    const openedOwnerBlocks = ref([]);
    const isOwnerBlockOpen = (pageId) => {
      return openedOwnerBlocks.value.includes(pageId);
    };
    const getTitle = (page) => {
      var _a;
      return ((_a = page == null ? void 0 : page.translation) == null ? void 0 : _a.title) || `ID: ${page == null ? void 0 : page.id}`;
    };
    const getShort = (page) => {
      var _a;
      return ((_a = page == null ? void 0 : page.translation) == null ? void 0 : _a.short) || "";
    };
    const getDescription = (page) => {
      var _a;
      return ((_a = page == null ? void 0 : page.translation) == null ? void 0 : _a.description) || "";
    };
    const parentTitle = (page) => {
      var _a, _b;
      return ((_b = (_a = page == null ? void 0 : page.parent) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || t("noData");
    };
    const truncateText = (text, maxLength = 80) => {
      if (!text) {
        return "";
      }
      return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + "…" : text;
    };
    const getSafeIcon = (icon) => {
      if (!icon) {
        return null;
      }
      const trimmed = icon.trim();
      if (trimmed.startsWith("<svg") && trimmed.endsWith("</svg>")) {
        return trimmed;
      }
      return null;
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
    const ownerName = (page) => {
      var _a;
      return ((_a = page == null ? void 0 : page.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (page) => {
      var _a;
      return ((_a = page == null ? void 0 : page.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (page) => {
      const owner = page == null ? void 0 : page.owner;
      if (!owner) {
        return t("noData");
      }
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (page) => {
      var _a;
      return ((_a = page == null ? void 0 : page.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const badgeClass = (enabled) => {
      return enabled ? "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300" : "bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-700 dark:text-slate-300";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedPages.length)}</div>`);
      if (__props.pages.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (__props.pages.length) {
        _push(`<div class="p-3"><div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"><!--[-->`);
        ssrRenderList(__props.pages, (page) => {
          _push(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center space-x-2"><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `sort: ${page.sort}`)}> ID: ${ssrInterpolate(page.id)}</div><button type="button" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"${ssrRenderAttr("title", isOwnerBlockOpen(page.id) ? unref(t)("hideOwner") : unref(t)("showOwner"))}><svg class="${ssrRenderClass([{ "rotate-180": isOwnerBlockOpen(page.id) }, "w-4 h-4 transition-transform duration-200"])}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"></path></svg></button></div><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedPages.includes(page.id)) ? " checked" : ""}></header><div style="${ssrRenderStyle(isOwnerBlockOpen(page.id) ? null : { display: "none" })}" class="flex flex-col items-center justify-center text-center py-3"><img${ssrRenderAttr("src", ownerAvatar(page))}${ssrRenderAttr("title", ownerTitle(page))} class="h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("author"))}><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerName(page))}>${ssrInterpolate(ownerName(page))}</div>`);
          if (ownerEmail(page)) {
            _push(`<div class="text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerEmail(page))}>${ssrInterpolate(ownerEmail(page))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="flex flex-col flex-1 px-3 py-3 space-y-2"><div class="flex flex-wrap justify-center gap-1 font-semibold"><span class="text-[10px] px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("level"))}> L${ssrInterpolate(page.level ?? 1)}</span><span class="text-[10px] px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("parentPage"))}>${ssrInterpolate(page.parent_id ? parentTitle(page) : unref(t)("rootPage"))}</span><span class="text-[10px] px-2 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("children"))}>${ssrInterpolate(page.children_count ?? 0)}</span></div><div class="flex items-center justify-center text-center"><div class="flex items-center justify-center space-x-2 max-w-full"><div class="flex items-center justify-center shrink-0">`);
          if (getSafeIcon(page.icon)) {
            _push(`<div class="w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center">${getSafeIcon(page.icon) ?? ""}</div>`);
          } else {
            _push(`<svg class="w-4 h-4 text-slate-500 dark:text-slate-300" fill="currentColor" viewBox="0 0 16 16"><path d="M2 2a1 1 0 011-1h3.5L8 2.5 9.5 1H13a1 1 0 011 1v3.5L12.5 7 14 8.5V12a1 1 0 01-1 1H9.5L8 11.5 6.5 13H3a1 1 0 01-1-1V8.5L3.5 7 2 5.5V2z"></path></svg>`);
          }
          _push(`</div><a${ssrRenderAttr("href", page.url || "#")} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-blue-700 dark:text-blue-200 hover:text-amber-700 dark:hover:text-amber-200 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", getTitle(page))}>${ssrInterpolate(truncateText(getTitle(page)))}</a></div></div><div class="flex justify-center font-semibold text-[10px] text-slate-600 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("url"))}>${ssrInterpolate(page.url || "#")}</div>`);
          if (getShort(page)) {
            _push(`<div class="font-semibold text-[12px] text-center text-cyan-700 dark:text-cyan-300"${ssrRenderAttr("title", getShort(page))}>${ssrInterpolate(truncateText(getShort(page)))}</div>`);
          } else {
            _push(`<!---->`);
          }
          if (getDescription(page)) {
            _push(`<div class="text-[11px] text-center text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", getDescription(page))}>${ssrInterpolate(truncateText(getDescription(page), 100))}</div>`);
          } else {
            _push(`<!---->`);
          }
          if ((page.views ?? 0) > 0) {
            _push(`<div class="flex items-center justify-center space-x-1"><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975C.253 8.715 3.248 14 8 14s7.747-5.285 7.872-5.51a1 1 0 0 0 0-.98C15.747 7.285 12.752 2 8 2zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z"></path></svg><span class="text-[10px] text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("views"))}>${ssrInterpolate(page.views ?? 0)}</span></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="grid grid-cols-1 gap-0.5 text-[11px] text-slate-600 dark:text-slate-300"><div class="font-semibold text-center text-fuchsia-700 dark:text-fuchsia-300"><span>${ssrInterpolate(unref(t)("status"))}: </span> ${ssrInterpolate(getStatusLabel(page.status))}</div></div><div class="flex flex-wrap justify-center gap-1"><span class="${ssrRenderClass([badgeClass(page.in_menu), "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"> Menu </span><span class="${ssrRenderClass([badgeClass(page.in_footer), "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"> Footer </span><span class="${ssrRenderClass([badgeClass(page.show_content), "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"> HTML </span><span class="${ssrRenderClass([badgeClass(page.show_seo), "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"> SEO </span></div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center space-x-1"><button type="button" class="${ssrRenderClass([badgeClass(page.in_menu), "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", page.in_menu ? unref(t)("showInMenu") : unref(t)("notShowInMenu"))}> M </button><button type="button" class="${ssrRenderClass([badgeClass(page.in_footer), "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", page.in_footer ? unref(t)("showInFooter") : unref(t)("notShowInFooter"))}> F </button><button type="button" class="${ssrRenderClass([badgeClass(page.show_content), "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", page.show_content ? unref(t)("showHtml") : unref(t)("notShowHtml"))}> H </button><button type="button" class="${ssrRenderClass([badgeClass(page.show_seo), "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", page.show_seo ? unref(t)("showSeo") : unref(t)("notShowSeo"))}> S </button>`);
          _push(ssrRenderComponent(_sfc_main$5, {
            isActive: page.activity,
            title: page.activity ? unref(t)("enabled") : unref(t)("disabled"),
            onToggleActivity: ($event) => emit("toggle-activity", page)
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$6, {
            href: _ctx.route("admin.cmsPages.edit", {
              cmsPage: page.id
            })
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$7, {
            onClick: ($event) => emit("delete", page)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Cms/CmsPage/View/PageCardGrid.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = /* @__PURE__ */ Object.assign({
  name: "PageTreeDraggable"
}, {
  __name: "PageTreeDraggable",
  __ssrInlineRender: true,
  props: {
    page: { type: Object, required: true },
    level: { type: Number, default: 0 },
    selectedPages: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
    "toggle-activity",
    "delete",
    "toggle-select",
    "request-drag-end",
    "toggle-menu",
    "toggle-footer",
    "toggle-content",
    "toggle-seo"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const storageKey = computed(() => {
      return `admin.cms.pages.tree.expanded.${props.page.id}`;
    });
    const isExpanded = ref(true);
    const localChildren = ref([]);
    const readExpandedState = () => {
      const savedValue = localStorage.getItem(storageKey.value);
      if (savedValue === null) {
        return true;
      }
      return savedValue === "1";
    };
    isExpanded.value = readExpandedState();
    watch(
      () => props.page.id,
      () => {
        isExpanded.value = readExpandedState();
      }
    );
    watch(
      () => props.page.children,
      (children) => {
        localChildren.value = Array.isArray(children) ? [...children] : [];
      },
      {
        immediate: true,
        deep: true
      }
    );
    const getTitle = (page) => {
      var _a;
      return ((_a = page == null ? void 0 : page.translation) == null ? void 0 : _a.title) || `ID: ${page == null ? void 0 : page.id}`;
    };
    const getShort = (page) => {
      var _a;
      return ((_a = page == null ? void 0 : page.translation) == null ? void 0 : _a.short) || "";
    };
    const handleInnerDragEnd = (event) => {
      emit("request-drag-end", event);
    };
    const getSafeIcon = (icon) => {
      if (!icon) {
        return null;
      }
      const trimmed = icon.trim();
      if (trimmed.startsWith("<svg") && trimmed.endsWith("</svg>")) {
        return trimmed;
      }
      return null;
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
    const ownerTitle = (page) => {
      const owner = page == null ? void 0 : page.owner;
      if (!owner) {
        return t("noData");
      }
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const badgeClass = (enabled) => {
      return enabled ? "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300" : "bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-700 dark:text-slate-300";
    };
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c, _d;
      const _component_PageTreeDraggable = resolveComponent("PageTreeDraggable", true);
      _push(`<div${ssrRenderAttrs(_attrs)}><div class="cms-page-item mb-1" style="${ssrRenderStyle({ marginLeft: __props.level * 20 + "px" })}"><div class="flex items-center justify-between py-1 px-2 border border-gray-400 rounded-sm bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 transition duration-150 ease-in-out"><div class="flex items-center space-x-2 flex-grow min-w-0"><span class="drag-handle handle cursor-move mr-1 flex-shrink-0"${ssrRenderAttr("title", unref(t)("dragDrop"))}><svg viewBox="0 0 512 512" class="w-4 h-4"><path class="fill-current text-sky-500 dark:text-sky-200" d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8h32v96h-96v-32c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c9.2 9.2 22.9 11.9 34.9 6.9s19.8-16.6 19.8-29.6v-32h96v96h-32c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8h-32v-96h96v32c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-9.2-9.2-22.9-11.9-34.9-6.9s-19.8 16.6-19.8 29.6v32h-96v-96h32c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-64-64z"></path></svg></span>`);
      if ((_a = __props.page.children) == null ? void 0 : _a.length) {
        _push(`<button type="button" class="flex-shrink-0 text-slate-900 hover:text-red-500 dark:text-slate-100 dark:hover:text-red-200"${ssrRenderAttr("title", isExpanded.value ? unref(t)("collapse") : unref(t)("expand"))}><svg class="${ssrRenderClass([{ "rotate-90": isExpanded.value }, "w-5 h-5 transform transition-transform duration-150"])}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>`);
      } else {
        _push(`<span class="w-5 h-5 inline-block flex-shrink-0"></span>`);
      }
      _push(`<div class="w-8 font-semibold text-sm text-amber-600 dark:text-amber-200 mr-1 flex-shrink-0"${ssrRenderAttr("title", `[${unref(t)("sort")}: ${__props.page.sort}] ${unref(t)("level")}: ${__props.page.level} / ${getStatusLabel(__props.page.status)}`)}>${ssrInterpolate(__props.page.id)}</div>`);
      if ((_c = (_b = __props.page) == null ? void 0 : _b.owner) == null ? void 0 : _c.profile_photo_url) {
        _push(`<img${ssrRenderAttr("src", __props.page.owner.profile_photo_url)}${ssrRenderAttr("title", ownerTitle(__props.page))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("author"))}>`);
      } else {
        _push(`<!---->`);
      }
      if (getSafeIcon(__props.page.icon)) {
        _push(`<div class="pl-3 w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center">${getSafeIcon(__props.page.icon) ?? ""}</div>`);
      } else {
        _push(`<svg class="w-4 h-4 text-slate-500 dark:text-slate-300" fill="currentColor" viewBox="0 0 16 16"><path d="M2 2a1 1 0 011-1h3.5L8 2.5 9.5 1H13a1 1 0 011 1v3.5L12.5 7 14 8.5V12a1 1 0 01-1 1H9.5L8 11.5 6.5 13H3a1 1 0 01-1-1V8.5L3.5 7 2 5.5V2z"></path></svg>`);
      }
      _push(`<a${ssrRenderAttr("href", __props.page.url || "#")} target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-blue-700 dark:text-blue-200 hover:text-amber-700 dark:hover:text-amber-200 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", getShort(__props.page))}>${ssrInterpolate(getTitle(__props.page))}</a><span class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("level"))}> L${ssrInterpolate(__props.page.level ?? 1)}</span><span class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("children"))}>${ssrInterpolate(__props.page.children_count ?? ((_d = __props.page.children) == null ? void 0 : _d.length) ?? 0)}</span><span class="${ssrRenderClass([badgeClass(__props.page.in_menu), "shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", unref(t)("showInMenu"))}> Menu </span><span class="${ssrRenderClass([badgeClass(__props.page.in_footer), "shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", unref(t)("showInFooter"))}> Footer </span><span class="${ssrRenderClass([badgeClass(__props.page.show_content), "shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", unref(t)("showHtml"))}> HTML </span><span class="${ssrRenderClass([badgeClass(__props.page.show_seo), "shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", unref(t)("showSeo"))}> SEO </span><div class="flex items-center space-x-1 flex-shrink-0 ml-4"><div class="w-7 font-semibold text-[8px] text-slate-700 dark:text-slate-300"${ssrRenderAttr("title", unref(t)("url"))}>${ssrInterpolate(__props.page.url || "#")}</div></div></div><div class="flex items-center space-x-1 flex-shrink-0 ml-4"><div class="flex items-center gap-1"><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975C.253 8.715 3.248 14 8 14s7.747-5.285 7.872-5.51a1 1 0 0 0 0-.98C15.747 7.285 12.752 2 8 2zm0 10a4 4 0 110-8 4 4 0 010 8zm0-6a2 2 0 100 4 2 2 0 000-4z"></path></svg><div class="w-7 font-semibold text-[10px] text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("views"))}>${ssrInterpolate(__props.page.views ?? 0)}</div></div><button type="button" class="${ssrRenderClass([badgeClass(__props.page.in_menu), "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", __props.page.in_menu ? unref(t)("showInMenu") : unref(t)("notShowInMenu"))}> M </button><button type="button" class="${ssrRenderClass([badgeClass(__props.page.in_footer), "text-[10px] px-2.5 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", __props.page.in_footer ? unref(t)("showInFooter") : unref(t)("notShowInFooter"))}> F </button><button type="button" class="${ssrRenderClass([badgeClass(__props.page.show_content), "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", __props.page.show_content ? unref(t)("showHtml") : unref(t)("notShowHtml"))}> H </button><button type="button" class="${ssrRenderClass([badgeClass(__props.page.show_seo), "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", __props.page.show_seo ? unref(t)("showSeo") : unref(t)("notShowSeo"))}> S </button>`);
      _push(ssrRenderComponent(_sfc_main$5, {
        isActive: __props.page.activity,
        title: __props.page.activity ? unref(t)("enabled") : unref(t)("disabled"),
        onToggleActivity: ($event) => emit("toggle-activity", __props.page)
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$6, {
        href: _ctx.route("admin.cmsPages.edit", {
          cmsPage: __props.page.id
        })
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$7, {
        onClick: ($event) => emit("delete", __props.page)
      }, null, _parent));
      _push(`<div class="pl-1.5"><input type="checkbox" class="form-checkbox rounded-sm text-indigo-500 flex-shrink-0"${ssrIncludeBooleanAttr(__props.selectedPages.includes(__props.page.id)) ? " checked" : ""}></div></div></div></div><div style="${ssrRenderStyle(isExpanded.value && localChildren.value.length ? null : { display: "none" })}" class="children-container mt-1">`);
      _push(ssrRenderComponent(unref(draggable), {
        modelValue: localChildren.value,
        "onUpdate:modelValue": ($event) => localChildren.value = $event,
        tag: "div",
        "item-key": "id",
        handle: ".drag-handle",
        group: "cms-pages",
        class: "cms-page-tree-children",
        "data-parent-id": __props.page.id,
        onEnd: handleInnerDragEnd
      }, {
        item: withCtx(({ element: childPage }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_PageTreeDraggable, {
              page: childPage,
              level: __props.level + 1,
              "selected-pages": __props.selectedPages,
              "is-admin": __props.isAdmin,
              onToggleActivity: (payload) => emit("toggle-activity", payload),
              onDelete: (payload) => emit("delete", payload),
              onToggleSelect: (id) => emit("toggle-select", id),
              onRequestDragEnd: handleInnerDragEnd,
              onToggleMenu: (payload) => emit("toggle-menu", payload),
              onToggleFooter: (payload) => emit("toggle-footer", payload),
              onToggleContent: (payload) => emit("toggle-content", payload),
              onToggleSeo: (payload) => emit("toggle-seo", payload)
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_PageTreeDraggable, {
                page: childPage,
                level: __props.level + 1,
                "selected-pages": __props.selectedPages,
                "is-admin": __props.isAdmin,
                onToggleActivity: (payload) => emit("toggle-activity", payload),
                onDelete: (payload) => emit("delete", payload),
                onToggleSelect: (id) => emit("toggle-select", id),
                onRequestDragEnd: handleInnerDragEnd,
                onToggleMenu: (payload) => emit("toggle-menu", payload),
                onToggleFooter: (payload) => emit("toggle-footer", payload),
                onToggleContent: (payload) => emit("toggle-content", payload),
                onToggleSeo: (payload) => emit("toggle-seo", payload)
              }, null, 8, ["page", "level", "selected-pages", "is-admin", "onToggleActivity", "onDelete", "onToggleSelect", "onToggleMenu", "onToggleFooter", "onToggleContent", "onToggleSeo"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Cms/CmsPage/Tree/PageTreeDraggable.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminCmsPagesProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    pagesTree: { type: Array, default: () => [] },
    pages: { type: [Array, Object], default: () => [] },
    pagesCount: { type: Number, default: 0 },
    adminCmsPagesPerPage: { type: Number, default: 6 },
    adminCmsPagesDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t, locale } = useI18n();
    const toast = useToast();
    const inertiaPage = usePage();
    const props = __props;
    const isAdmin = computed(() => {
      var _a, _b, _c;
      const roles = ((_c = (_b = (_a = inertiaPage.props) == null ? void 0 : _a.auth) == null ? void 0 : _b.user) == null ? void 0 : _c.roles) || [];
      return roles.some((role) => (role == null ? void 0 : role.name) === "admin");
    });
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_cms_pages") || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_cms_pages",
        value
      );
    });
    const pagesList = computed(() => {
      var _a, _b, _c;
      if (Array.isArray(props.pages)) {
        return props.pages;
      }
      if (Array.isArray((_a = props.pages) == null ? void 0 : _a.data)) {
        return props.pages.data;
      }
      if (Array.isArray((_c = (_b = props.pages) == null ? void 0 : _b.data) == null ? void 0 : _c.data)) {
        return props.pages.data.data;
      }
      return [];
    });
    const localPagesTree = ref([]);
    const localPagesFlat = ref([]);
    watch(
      () => props.pagesTree,
      (value) => {
        localPagesTree.value = JSON.parse(
          JSON.stringify(value || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    watch(
      pagesList,
      (value) => {
        localPagesFlat.value = JSON.parse(
          JSON.stringify(value || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const itemsPerPage = ref(
      props.adminCmsPagesPerPage || 6
    );
    const currentPage = ref(1);
    watch(itemsPerPage, (newValue) => {
      router.put(
        route("admin.settings.updateAdminCountCmsPages"),
        {
          value: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(
              `Показ ${newValue} CMS страниц на странице.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления количества CMS страниц."
            );
          }
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminCmsPagesDefaultSort || "idDesc"
    );
    watch(
      () => props.sortParam,
      (value) => {
        if (value && value !== sortParam.value) {
          sortParam.value = value;
        }
      }
    );
    watch(sortParam, (newValue, oldValue) => {
      if (newValue === oldValue) {
        return;
      }
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortCmsPages"),
        {
          value: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing && viewMode.value !== "table") {
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
              "Сортировка CMS страниц успешно изменена."
            );
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления сортировки CMS страниц."
            );
          }
        }
      );
    });
    const searchQuery = ref(
      props.search || ""
    );
    watch(
      () => props.search,
      (value) => {
        const normalizedValue = value || "";
        if (normalizedValue !== searchQuery.value) {
          searchQuery.value = normalizedValue;
        }
      }
    );
    const normalize = (value) => {
      return String(value ?? "").trim().toLowerCase();
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(value || 0).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const getPageTitle = (cmsPage) => {
      var _a;
      return ((_a = cmsPage == null ? void 0 : cmsPage.translation) == null ? void 0 : _a.title) || `ID: ${cmsPage == null ? void 0 : cmsPage.id}`;
    };
    const getOwnerName = (cmsPage) => {
      var _a;
      return ((_a = cmsPage == null ? void 0 : cmsPage.owner) == null ? void 0 : _a.name) || "";
    };
    const getOwnerEmail = (cmsPage) => {
      var _a;
      return ((_a = cmsPage == null ? void 0 : cmsPage.owner) == null ? void 0 : _a.email) || "";
    };
    const byNumberAsc = (field) => (a, b) => {
      return safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    };
    const byNumberDesc = (field) => (a, b) => {
      return safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const byDateAsc = (field) => (a, b) => {
      return safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    };
    const byDateDesc = (field) => (a, b) => {
      return safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const sortPages = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") {
        return list.filter((item) => !!item.activity);
      }
      if (sortParam.value === "inactive") {
        return list.filter((item) => !item.activity);
      }
      if (sortParam.value === "inMenu") {
        return list.filter((item) => !!item.in_menu);
      }
      if (sortParam.value === "notInMenu") {
        return list.filter((item) => !item.in_menu);
      }
      if (sortParam.value === "inFooter") {
        return list.filter((item) => !!item.in_footer);
      }
      if (sortParam.value === "notInFooter") {
        return list.filter((item) => !item.in_footer);
      }
      if (sortParam.value === "showContent") {
        return list.filter((item) => !!item.show_content);
      }
      if (sortParam.value === "notShowContent") {
        return list.filter((item) => !item.show_content);
      }
      if (sortParam.value === "showSeo") {
        return list.filter((item) => !!item.show_seo);
      }
      if (sortParam.value === "notShowSeo") {
        return list.filter((item) => !item.show_seo);
      }
      if (sortParam.value === "statusDraft") {
        return list.filter(
          (item) => (item == null ? void 0 : item.status) === "draft"
        );
      }
      if (sortParam.value === "statusPublished") {
        return list.filter(
          (item) => (item == null ? void 0 : item.status) === "published"
        );
      }
      if (sortParam.value === "statusArchived") {
        return list.filter(
          (item) => (item == null ? void 0 : item.status) === "archived"
        );
      }
      const sortMap = {
        /** ID */
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        /** Sort */
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        /** Level */
        levelAsc: byNumberAsc("level"),
        levelDesc: byNumberDesc("level"),
        /** Parent */
        parentAsc: byNumberAsc("parent_id"),
        parentDesc: byNumberDesc("parent_id"),
        /** URL */
        urlAsc: (a, b) => normalize(a == null ? void 0 : a.url).localeCompare(
          normalize(b == null ? void 0 : b.url),
          locale.value
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        urlDesc: (a, b) => normalize(b == null ? void 0 : b.url).localeCompare(
          normalize(a == null ? void 0 : a.url),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        /** Title currentLocale */
        titleAsc: (a, b) => normalize(getPageTitle(a)).localeCompare(
          normalize(getPageTitle(b)),
          locale.value
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        titleDesc: (a, b) => normalize(getPageTitle(b)).localeCompare(
          normalize(getPageTitle(a)),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        /** Views */
        viewsAsc: byNumberAsc("views"),
        viewsDesc: byNumberDesc("views"),
        /** Children */
        childrenAsc: byNumberAsc("children_count"),
        childrenDesc: byNumberDesc("children_count"),
        /** Activity */
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        /** Menu */
        inMenuAsc: byNumberAsc("in_menu"),
        inMenuDesc: byNumberDesc("in_menu"),
        /** Footer */
        inFooterAsc: byNumberAsc("in_footer"),
        inFooterDesc: byNumberDesc("in_footer"),
        /** HTML */
        showContentAsc: byNumberAsc("show_content"),
        showContentDesc: byNumberDesc("show_content"),
        /** SEO */
        showSeoAsc: byNumberAsc("show_seo"),
        showSeoDesc: byNumberDesc("show_seo"),
        /** Owner name */
        ownerNameAsc: (a, b) => normalize(getOwnerName(a)).localeCompare(
          normalize(getOwnerName(b)),
          locale.value
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        ownerNameDesc: (a, b) => normalize(getOwnerName(b)).localeCompare(
          normalize(getOwnerName(a)),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        /** Owner email */
        ownerEmailAsc: (a, b) => normalize(getOwnerEmail(a)).localeCompare(
          normalize(getOwnerEmail(b)),
          locale.value
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        ownerEmailDesc: (a, b) => normalize(getOwnerEmail(b)).localeCompare(
          normalize(getOwnerEmail(a)),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        /** Status */
        statusAsc: (a, b) => normalize(a == null ? void 0 : a.status).localeCompare(
          normalize(b == null ? void 0 : b.status),
          locale.value
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        statusDesc: (a, b) => normalize(b == null ? void 0 : b.status).localeCompare(
          normalize(a == null ? void 0 : a.status),
          locale.value
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        /** Published */
        publishedAtAsc: byDateAsc("published_at"),
        publishedAtDesc: byDateDesc("published_at"),
        /** Created */
        createdAtAsc: byDateAsc("created_at"),
        createdAtDesc: byDateDesc("created_at"),
        dateAsc: byDateAsc("created_at"),
        dateDesc: byDateDesc("created_at"),
        /** Updated */
        updatedAtAsc: byDateAsc("updated_at"),
        updatedAtDesc: byDateDesc("updated_at")
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const pageSearchValues = (cmsPage) => {
      var _a, _b, _c, _d, _e, _f, _g;
      return [
        cmsPage == null ? void 0 : cmsPage.id,
        cmsPage == null ? void 0 : cmsPage.url,
        cmsPage == null ? void 0 : cmsPage.icon,
        cmsPage == null ? void 0 : cmsPage.views,
        cmsPage == null ? void 0 : cmsPage.status,
        (_a = cmsPage == null ? void 0 : cmsPage.translation) == null ? void 0 : _a.title,
        (_b = cmsPage == null ? void 0 : cmsPage.translation) == null ? void 0 : _b.short,
        (_c = cmsPage == null ? void 0 : cmsPage.translation) == null ? void 0 : _c.description,
        (_e = (_d = cmsPage == null ? void 0 : cmsPage.parent) == null ? void 0 : _d.translation) == null ? void 0 : _e.title,
        (_f = cmsPage == null ? void 0 : cmsPage.owner) == null ? void 0 : _f.name,
        (_g = cmsPage == null ? void 0 : cmsPage.owner) == null ? void 0 : _g.email
      ];
    };
    const filteredPages = computed(() => {
      const query = normalize(
        searchQuery.value
      );
      let filtered = localPagesFlat.value || [];
      if (query) {
        filtered = filtered.filter((cmsPage) => {
          return pageSearchValues(cmsPage).some(
            (value) => normalize(value).includes(query)
          );
        });
      }
      return sortPages(
        filtered
      );
    });
    const paginatedPages = computed(() => {
      const perPage = Number(
        itemsPerPage.value || 10
      );
      const start = (currentPage.value - 1) * perPage;
      return filteredPages.value.slice(
        start,
        start + perPage
      );
    });
    const displayedPages = computed(() => {
      return props.useServerProcessing ? pagesList.value : paginatedPages.value;
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
    const showConfirmDeleteModal = ref(false);
    const pageToDeleteId = ref(null);
    const pageToDeleteTitle = ref("");
    const confirmDelete = (pageOrId, title = null) => {
      if (pageOrId && typeof pageOrId === "object") {
        pageToDeleteId.value = pageOrId.id;
        pageToDeleteTitle.value = title || getPageTitle(pageOrId);
      } else {
        pageToDeleteId.value = pageOrId;
        pageToDeleteTitle.value = title || `ID: ${pageOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      pageToDeleteId.value = null;
      pageToDeleteTitle.value = "";
    };
    const deletePage = () => {
      if (pageToDeleteId.value === null) {
        return;
      }
      const idToDelete = pageToDeleteId.value;
      const titleToDelete = pageToDeleteTitle.value;
      router.delete(
        route(
          "admin.cmsPages.destroy",
          {
            cmsPage: idToDelete
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `CMS страница "${titleToDelete || "ID: " + idToDelete}" удалена.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            const errorMessage = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
            toast.error(
              `${errorMessage} (Страница: ${titleToDelete || "ID: " + idToDelete})`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    const patchPageInTree = (nodes, pageId, callback) => {
      var _a;
      for (const node of nodes) {
        if (node.id === pageId) {
          callback(node);
          return true;
        }
        if (((_a = node.children) == null ? void 0 : _a.length) && patchPageInTree(
          node.children,
          pageId,
          callback
        )) {
          return true;
        }
      }
      return false;
    };
    const patchPageInFlat = (pageId, callback) => {
      const index = localPagesFlat.value.findIndex(
        (cmsPage) => cmsPage.id === pageId
      );
      if (index !== -1) {
        callback(
          localPagesFlat.value[index]
        );
      }
    };
    const toggleActivity = (cmsPage) => {
      const newActivity = !cmsPage.activity;
      const title = getPageTitle(cmsPage);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.cmsPages.updateActivity",
          {
            cmsPage: cmsPage.id
          }
        ),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchPageInTree(
              localPagesTree.value,
              cmsPage.id,
              (node) => {
                node.activity = newActivity;
              }
            );
            patchPageInFlat(
              cmsPage.id,
              (node) => {
                node.activity = newActivity;
              }
            );
            cmsPage.activity = newActivity;
            toast.success(
              `CMS страница "${title}" ${actionText}.`
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
    const toggleBooleanFlag = (cmsPage, field, routeName, payloadKey, enabledText, disabledText) => {
      const newValue = !cmsPage[field];
      const title = getPageTitle(cmsPage);
      router.put(
        route(
          routeName,
          {
            cmsPage: cmsPage.id
          }
        ),
        {
          [payloadKey]: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchPageInTree(
              localPagesTree.value,
              cmsPage.id,
              (node) => {
                node[field] = newValue;
              }
            );
            patchPageInFlat(
              cmsPage.id,
              (node) => {
                node[field] = newValue;
              }
            );
            cmsPage[field] = newValue;
            toast.success(
              newValue ? `CMS страница "${title}" ${enabledText}.` : `CMS страница "${title}" ${disabledText}.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors[payloadKey] || errors.general || `Ошибка изменения настройки для "${title}".`
            );
          }
        }
      );
    };
    const toggleInMenu = (cmsPage) => {
      toggleBooleanFlag(
        cmsPage,
        "in_menu",
        "admin.actions.cmsPages.updateInMenu",
        "in_menu",
        "добавлена в меню",
        "скрыта из меню"
      );
    };
    const toggleInFooter = (cmsPage) => {
      toggleBooleanFlag(
        cmsPage,
        "in_footer",
        "admin.actions.cmsPages.updateInFooter",
        "in_footer",
        "добавлена в футер",
        "скрыта из футера"
      );
    };
    const toggleShowContent = (cmsPage) => {
      toggleBooleanFlag(
        cmsPage,
        "show_content",
        "admin.actions.cmsPages.updateShowContent",
        "show_content",
        "будет показывать свой HTML-контент",
        "не будет показывать свой HTML-контент"
      );
    };
    const toggleShowSeo = (cmsPage) => {
      toggleBooleanFlag(
        cmsPage,
        "show_seo",
        "admin.actions.cmsPages.updateShowSeo",
        "show_seo",
        "будет использовать своё SEO",
        "не будет использовать своё SEO"
      );
    };
    const handleDragEnd = () => {
      const changes = [];
      const updateSortAndCollectChanges = (nodes, parentId) => {
        nodes.forEach((node, index) => {
          var _a;
          let changed = false;
          if (node.sort !== index) {
            node.sort = index;
            changed = true;
          }
          if (node.parent_id !== parentId) {
            node.parent_id = parentId;
            changed = true;
          }
          if (changed) {
            changes.push({
              id: node.id,
              sort: node.sort,
              parent_id: parentId
            });
          }
          if ((_a = node.children) == null ? void 0 : _a.length) {
            updateSortAndCollectChanges(
              node.children,
              node.id
            );
          }
        });
      };
      updateSortAndCollectChanges(
        localPagesTree.value,
        null
      );
      if (!changes.length) {
        return;
      }
      router.put(
        route(
          "admin.actions.cmsPages.updateSortBulk"
        ),
        {
          items: changes
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Иерархия CMS страниц успешно обновлена."
            );
          },
          onError: (errors) => {
            toast.error(
              errors.message || "Ошибка обновления иерархии CMS страниц."
            );
            router.reload({
              only: [
                "pagesTree",
                "pages"
              ],
              preserveScroll: true
            });
          }
        }
      );
    };
    const selectedPages = ref([]);
    const getAllIds = (nodes) => {
      let ids = [];
      nodes.forEach((node) => {
        var _a;
        ids.push(
          node.id
        );
        if ((_a = node.children) == null ? void 0 : _a.length) {
          ids = ids.concat(
            getAllIds(
              node.children
            )
          );
        }
      });
      return ids;
    };
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = viewMode.value === "table" ? getAllIds(localPagesTree.value) : displayedPages.value.map(
        (cmsPage) => cmsPage.id
      );
      selectedPages.value = checked ? ids : [];
    };
    const toggleAllCards = ({
      ids,
      checked
    }) => {
      selectedPages.value = checked ? [...ids] : [];
    };
    const toggleSelectPage = (pageId) => {
      const index = selectedPages.value.indexOf(
        pageId
      );
      if (index > -1) {
        selectedPages.value.splice(
          index,
          1
        );
        return;
      }
      selectedPages.value.push(
        pageId
      );
    };
    const updateActivityByIds = (nodes, ids, activity) => {
      nodes.forEach((node) => {
        var _a;
        if (ids.includes(node.id)) {
          node.activity = activity;
        }
        if ((_a = node.children) == null ? void 0 : _a.length) {
          updateActivityByIds(
            node.children,
            ids,
            activity
          );
        }
      });
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedPages.value.length) {
        toast.warning(
          "Выберите CMS страницы для активации/деактивации."
        );
        return;
      }
      const idsToUpdate = [
        ...selectedPages.value
      ];
      router.put(
        route(
          "admin.actions.cmsPages.bulkUpdateActivity"
        ),
        {
          ids: idsToUpdate,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            updateActivityByIds(
              localPagesTree.value,
              idsToUpdate,
              newActivity
            );
            localPagesFlat.value = localPagesFlat.value.map(
              (item) => {
                return idsToUpdate.includes(
                  item.id
                ) ? {
                  ...item,
                  activity: newActivity
                } : item;
              }
            );
            selectedPages.value = [];
            toast.success(
              "Активность выбранных CMS страниц обновлена."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности."
            );
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedPages.value.length) {
        toast.warning(
          "Выберите хотя бы одну CMS страницу для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные CMS страницы?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.cmsPages.bulkDestroy"
        ),
        {
          data: {
            ids: selectedPages.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedPages.value = [];
            toast.success(
              "Выбранные CMS страницы успешно удалены."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              errors[errorKey] || "Ошибка при массовом удалении CMS страниц."
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
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("pages")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("pages"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("pages")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("pages")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto" data-v-d8e34f63${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" data-v-d8e34f63${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3" data-v-d8e34f63${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              href: _ctx.route("admin.cmsPages.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addPage"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addPage")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              "setting-key": "adminCmsPagesProcessingMode",
              mode: __props.adminCmsPagesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.pagesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.pagesCount && !__props.useServerProcessing && viewMode.value !== "table") {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.pagesCount && __props.useServerProcessing && viewMode.value !== "table") {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.pagesCount && viewMode.value !== "table") {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3" data-v-d8e34f63${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountCmsPages"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$3, {
                "sort-param": sortParam.value,
                "onUpdate:sortParam": ($event) => sortParam.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.pagesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3 mb-3" data-v-d8e34f63${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.pagesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.pagesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, {
                disabled: !selectedPages.value.length,
                onChange: handleBulkAction
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$f, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.pagesCount && viewMode.value !== "table") {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3" data-v-d8e34f63${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredPages.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.pages }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(`<div class="mt-2 border border-gray-400 bg-white dark:bg-slate-800" data-v-d8e34f63${_scopeId}>`);
              if (__props.pagesCount) {
                _push2(`<div class="flex justify-between items-center px-3 py-2 border-b border-gray-400 bg-gray-100 dark:bg-slate-900" data-v-d8e34f63${_scopeId}><div class="text-xs text-slate-600 dark:text-slate-200" data-v-d8e34f63${_scopeId}>${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(selectedPages.value.length)}</div><label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer" data-v-d8e34f63${_scopeId}><span data-v-d8e34f63${_scopeId}>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="form-checkbox rounded-sm text-indigo-500 ml-2"${ssrRenderAttr("title", unref(t)("selectAll"))} data-v-d8e34f63${_scopeId}></label></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(ssrRenderComponent(unref(draggable), {
                modelValue: localPagesTree.value,
                "onUpdate:modelValue": ($event) => localPagesTree.value = $event,
                "item-key": "id",
                group: "cms-pages",
                handle: ".drag-handle",
                class: "cms-page-tree-root p-1",
                onEnd: handleDragEnd
              }, {
                item: withCtx(({ element }, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(ssrRenderComponent(_sfc_main$1, {
                      page: element,
                      "selected-pages": selectedPages.value,
                      "is-admin": isAdmin.value,
                      onToggleActivity: toggleActivity,
                      onDelete: confirmDelete,
                      onToggleSelect: toggleSelectPage,
                      onRequestDragEnd: handleDragEnd,
                      onToggleMenu: toggleInMenu,
                      onToggleFooter: toggleInFooter,
                      onToggleContent: toggleShowContent,
                      onToggleSeo: toggleShowSeo
                    }, null, _parent3, _scopeId2));
                  } else {
                    return [
                      createVNode(_sfc_main$1, {
                        page: element,
                        "selected-pages": selectedPages.value,
                        "is-admin": isAdmin.value,
                        onToggleActivity: toggleActivity,
                        onDelete: confirmDelete,
                        onToggleSelect: toggleSelectPage,
                        onRequestDragEnd: handleDragEnd,
                        onToggleMenu: toggleInMenu,
                        onToggleFooter: toggleInFooter,
                        onToggleContent: toggleShowContent,
                        onToggleSeo: toggleShowSeo
                      }, null, 8, ["page", "selected-pages", "is-admin"])
                    ];
                  }
                }),
                footer: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    if (!localPagesTree.value.length) {
                      _push3(`<div class="p-4 text-center text-slate-900 dark:text-slate-100" data-v-d8e34f63${_scopeId2}>${ssrInterpolate(unref(t)("noData"))}</div>`);
                    } else {
                      _push3(`<!---->`);
                    }
                  } else {
                    return [
                      !localPagesTree.value.length ? (openBlock(), createBlock("div", {
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
                pages: displayedPages.value,
                "selected-pages": selectedPages.value,
                "is-admin": isAdmin.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectPage,
                onToggleAll: toggleAllCards,
                onToggleMenu: toggleInMenu,
                onToggleFooter: toggleInFooter,
                onToggleContent: toggleShowContent,
                onToggleSeo: toggleShowSeo
              }, null, _parent2, _scopeId));
            }
            if (__props.pagesCount && viewMode.value !== "table") {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3" data-v-d8e34f63${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredPages.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.pages }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$i, {
              show: showConfirmDeleteModal.value,
              "on-cancel": closeModal,
              "on-confirm": deletePage,
              "cancel-text": unref(t)("cancel"),
              "confirm-text": unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$8, {
                      href: _ctx.route("admin.cmsPages.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addPage")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$9, {
                      "setting-key": "adminCmsPagesProcessingMode",
                      mode: __props.adminCmsPagesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.pagesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.pagesCount && !__props.useServerProcessing && viewMode.value !== "table" ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.pagesCount && __props.useServerProcessing && viewMode.value !== "table" ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.pagesCount && viewMode.value !== "table" ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountCmsPages"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      "sort-param": sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sort-param", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.pagesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3 mb-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.pagesCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, {
                      disabled: !selectedPages.value.length,
                      onChange: handleBulkAction
                    }, null, 8, ["disabled"]),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.pagesCount && viewMode.value !== "table" ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredPages.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.pages
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock("div", {
                    key: 5,
                    class: "mt-2 border border-gray-400 bg-white dark:bg-slate-800"
                  }, [
                    __props.pagesCount ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex justify-between items-center px-3 py-2 border-b border-gray-400 bg-gray-100 dark:bg-slate-900"
                    }, [
                      createVNode("div", { class: "text-xs text-slate-600 dark:text-slate-200" }, toDisplayString(unref(t)("selected")) + ": " + toDisplayString(selectedPages.value.length), 1),
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
                      modelValue: localPagesTree.value,
                      "onUpdate:modelValue": ($event) => localPagesTree.value = $event,
                      "item-key": "id",
                      group: "cms-pages",
                      handle: ".drag-handle",
                      class: "cms-page-tree-root p-1",
                      onEnd: handleDragEnd
                    }, {
                      item: withCtx(({ element }) => [
                        createVNode(_sfc_main$1, {
                          page: element,
                          "selected-pages": selectedPages.value,
                          "is-admin": isAdmin.value,
                          onToggleActivity: toggleActivity,
                          onDelete: confirmDelete,
                          onToggleSelect: toggleSelectPage,
                          onRequestDragEnd: handleDragEnd,
                          onToggleMenu: toggleInMenu,
                          onToggleFooter: toggleInFooter,
                          onToggleContent: toggleShowContent,
                          onToggleSeo: toggleShowSeo
                        }, null, 8, ["page", "selected-pages", "is-admin"])
                      ]),
                      footer: withCtx(() => [
                        !localPagesTree.value.length ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "p-4 text-center text-slate-900 dark:text-slate-100"
                        }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true)
                      ]),
                      _: 1
                    }, 8, ["modelValue", "onUpdate:modelValue"])
                  ])) : (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    pages: displayedPages.value,
                    "selected-pages": selectedPages.value,
                    "is-admin": isAdmin.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectPage,
                    onToggleAll: toggleAllCards,
                    onToggleMenu: toggleInMenu,
                    onToggleFooter: toggleInFooter,
                    onToggleContent: toggleShowContent,
                    onToggleSeo: toggleShowSeo
                  }, null, 8, ["pages", "selected-pages", "is-admin"])),
                  __props.pagesCount && viewMode.value !== "table" ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredPages.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.pages
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                "on-cancel": closeModal,
                "on-confirm": deletePage,
                "cancel-text": unref(t)("cancel"),
                "confirm-text": unref(t)("yesDelete"),
                onClose: closeModal
              }, null, 8, ["show", "cancel-text", "confirm-text"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Cms/CmsPages/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-d8e34f63"]]);
export {
  Index as default
};
