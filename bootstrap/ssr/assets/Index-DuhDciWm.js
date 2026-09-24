import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, withModifiers, withDirectives, createCommentVNode, vShow, createTextVNode, computed } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent, ssrRenderClass, ssrRenderStyle } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { usePage, router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$c } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$m } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$i } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$g, a as _sfc_main$j, b as _sfc_main$k } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$e } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$d } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$f, a as _sfc_main$l } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$h } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$5, a as _sfc_main$6, b as _sfc_main$7 } from "./RightToggle-r8SYzaEU.js";
import { _ as _sfc_main$9 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$b } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$a } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$8 } from "./ModerationButton-D_ehimPY.js";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option><option value="left">${ssrInterpolate(unref(t)("left"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("noLeft"))}</option><option disabled>──────────────────</option><option value="main">${ssrInterpolate(unref(t)("main"))}</option><option value="noMain">${ssrInterpolate(unref(t)("noMain"))}</option><option disabled>──────────────────</option><option value="right">${ssrInterpolate(unref(t)("right"))}</option><option value="noRight">${ssrInterpolate(unref(t)("noRight"))}</option><option disabled>──────────────────</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketCompany/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit mt-2 mb-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>───────────────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>───────────────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option disabled>───────────────────────────</option><option value="urlAsc">URL A→Z</option><option value="urlDesc">URL Z→A</option><option disabled>───────────────────────────</option><option value="legalNameAsc">${ssrInterpolate(unref(t)("legalName"))} A→Z</option><option value="legalNameDesc">${ssrInterpolate(unref(t)("legalName"))} Z→A</option><option value="companyTypeAsc">${ssrInterpolate(unref(t)("type"))} A→Z</option><option value="companyTypeDesc">${ssrInterpolate(unref(t)("type"))} Z→A</option><option value="cityAsc">${ssrInterpolate(unref(t)("city"))} A→Z</option><option value="cityDesc">${ssrInterpolate(unref(t)("city"))} Z→A</option><option disabled>───────────────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option><option disabled>───────────────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="showFromAtDesc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortStarted"))} ↓ </option><option value="showFromAtAsc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortStarted"))} ↑ </option><option value="showToAtDesc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortExpires"))} ↓ </option><option value="showToAtAsc">${ssrInterpolate(unref(t)("show"))} - ${ssrInterpolate(unref(t)("shortExpires"))} ↑ </option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>───────────────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option disabled>───────────────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>───────────────────────────</option><option value="leftDesc">${ssrInterpolate(unref(t)("inLeft"))} ON→OFF</option><option value="leftAsc">${ssrInterpolate(unref(t)("inLeft"))} OFF→ON</option><option value="left">${ssrInterpolate(unref(t)("inLeft"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("notLeft"))}</option><option disabled>───────────────────────────</option><option value="mainDesc">${ssrInterpolate(unref(t)("inMain"))} ON→OFF</option><option value="mainAsc">${ssrInterpolate(unref(t)("inMain"))} OFF→ON</option><option value="main">${ssrInterpolate(unref(t)("inMain"))}</option><option value="noMain">${ssrInterpolate(unref(t)("notMain"))}</option><option disabled>───────────────────────────</option><option value="rightDesc">${ssrInterpolate(unref(t)("inRight"))} ON→OFF</option><option value="rightAsc">${ssrInterpolate(unref(t)("inRight"))} OFF→ON</option><option value="right">${ssrInterpolate(unref(t)("inRight"))}</option><option value="noRight">${ssrInterpolate(unref(t)("notRight"))}</option><option disabled>───────────────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>───────────────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketCompany/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "CompanyTable",
  __ssrInlineRender: true,
  props: {
    companies: {
      type: Array,
      default: () => []
    },
    selectedCompanies: {
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
    const localCompanies = ref([]);
    watch(
      () => props.companies,
      (newVal) => {
        localCompanies.value = JSON.parse(
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
        localCompanies.value.map(
          (company) => company.id
        )
      );
    };
    const allSelected = () => {
      return !!localCompanies.value.length && localCompanies.value.every(
        (company) => props.selectedCompanies.includes(
          company.id
        )
      );
    };
    const companyTitle = (company) => {
      var _a;
      return ((_a = company == null ? void 0 : company.translation) == null ? void 0 : _a.title) || (company == null ? void 0 : company.legal_name) || `ID: ${company == null ? void 0 : company.id}`;
    };
    const ownerTitle = (company) => {
      const owner = company == null ? void 0 : company.owner;
      if (!owner) {
        return t("noData");
      }
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (company) => {
      var _a;
      return ((_a = company == null ? void 0 : company.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const logoUrl = (company) => {
      if (!(company == null ? void 0 : company.logo)) {
        return "/storage/market/companies/logos/default-image-light.png";
      }
      return company.logo.startsWith("/storage/") ? company.logo : `/storage/${company.logo}`;
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
    const formatDate = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(dateStr);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return date.toLocaleDateString(
        "ru-RU",
        {
          year: "numeric",
          month: "long",
          day: "numeric"
        }
      );
    };
    const truncateText = (text, maxLength = 40) => {
      if (!text) {
        return "";
      }
      return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + "…" : text;
    };
    const companyTypeLabelKeyMap = {
      company: "marketCompanyTypeCompany",
      entrepreneur: "marketCompanyTypeEntrepreneur",
      individual: "marketCompanyTypeIndividual"
    };
    const companyTypeLabel = (type) => {
      return t(
        companyTypeLabelKeyMap[type] || "marketCompanyTypeCompany"
      );
    };
    const vatLabel = (company) => {
      if (!(company == null ? void 0 : company.vat_enabled)) {
        return t("marketCompanyWithoutVat");
      }
      if (company == null ? void 0 : company.vat_rate) {
        return `${t("marketCompanyVat")} ${company.vat_rate}%`;
      }
      return t("marketCompanyWithVat");
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedCompanies.length)}</div>`);
      if (localCompanies.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localCompanies.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-1 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("owner"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 24 24"><path d="M3,7H1V2A1,1,0,0,1,2,1H7V3H3Z"></path><path d="M23,7H21V3H17V1h5a1,1,0,0,1,1,1Z"></path><path d="M7,23H2a1,1,0,0,1-1-1V17H3v4H7Z"></path><path d="M22,23H17V21h4V17h2v5A1,1,0,0,1,22,23Z"></path><path d="M18.242,18.03l-2.727-.681a1,1,0,0,1-.744-.806l-.249-1.491A6.792,6.792,0,0,0,17,10V9A5,5,0,0,0,7,9v1a6.792,6.792,0,0,0,2.478,5.052l-.249,1.491a1,1,0,0,1-.743.806l-2.728.681A1,1,0,0,0,6,20H18a1,1,0,0,0,.242-1.97Z"></path></svg></div></th><th class="px-1 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("title"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-left"> БИН/ИИН </div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("contacts"))} / ${ssrInterpolate(unref(t)("city"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("show"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-1 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-1 py-1 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localCompanies.value,
          "onUpdate:modelValue": ($event) => localCompanies.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: company }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-1 py-1 text-center cursor-move handle w-px"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-1 py-1 whitespace-nowrap w-px"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${ssrRenderAttr("title", `[${company.sort}] / ${formatDate(company.published_at)}`)}${_scopeId}>${ssrInterpolate(company.id)}</div></td><td class="px-1 py-3"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(company))}${ssrRenderAttr("title", ownerTitle(company))}${ssrRenderAttr("alt", unref(t)("owner"))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${_scopeId}></div></td><td class="px-1 py-3"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", logoUrl(company))}${ssrRenderAttr("alt", companyTitle(company))}${ssrRenderAttr("title", companyTitle(company))} class="h-8 w-12 object-cover rounded-sm border border-slate-300 dark:border-slate-600"${_scopeId}></div></td><td class="px-1 py-3"${_scopeId}><div class="text-left"${_scopeId}><a${ssrRenderAttr("href", `/market/companies/${encodeURIComponent(company.url)}`)} target="_blank" rel="noopener noreferrer" class="text-sky-700 dark:text-sky-200 text-xs hover:underline hover:text-amber-700 dark:hover:text-amber-200"${ssrRenderAttr("title", company.show_from_at ? `${unref(t)("show")}: ${company.show_from_at} / ${company.show_to_at}` : `${formatDate(company.published_at)}`)}${_scopeId}>${ssrInterpolate(truncateText(companyTitle(company), 70))}</a><div class="text-[11px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(company.legal_name, 70))}</div><div class="text-[10px] text-slate-400 dark:text-slate-400"${_scopeId}>${ssrInterpolate(companyTypeLabel(company.company_type))} / ${ssrInterpolate(vatLabel(company))}</div><div class="text-[10px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(getStatusLabel(company.status))}</div></div></td><td class="px-1 py-3 whitespace-nowrap"${_scopeId}><div class="text-left text-xs text-indigo-700 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(company.bin_iin || "—")}</div></td><td class="px-1 py-3 whitespace-nowrap flex flex-col items-center justify-center gap-3"${_scopeId}><div class="text-left text-xs"${_scopeId}><div class="text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(company.phone || "—")}</div><div class="text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(company.email || "")}</div></div><div class="text-left text-xs"${_scopeId}><div${_scopeId}>${ssrInterpolate(company.city || "—")}</div><div class="text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(company.region || "")}</div></div></td><td class="px-1 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-300"${_scopeId}>${ssrInterpolate(company.views)}</div></td><td class="px-1 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: company.left,
                title: company.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => _ctx.$emit("toggle-left", company)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: company.main,
                title: company.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => _ctx.$emit("toggle-main", company)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: company.right,
                title: company.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => _ctx.$emit("toggle-right", company)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-3 whitespace-nowrap"${_scopeId}><div class="flex items-center justify-center gap-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(company.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", company.moderation_note && company.moderated_at ? `${company.moderation_note} [${formatDate(company.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(company.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isAdmin: __props.isAdmin,
                status: (company == null ? void 0 : company.moderation_status) ?? 0,
                initialNote: (company == null ? void 0 : company.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => _ctx.$emit("approve", company, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: company.activity,
                title: company.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", company)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.marketCompanies.edit", { marketCompany: company.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => _ctx.$emit("delete", company)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-1 py-1 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedCompanies.includes(company.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                      title: `[${company.sort}] / ${formatDate(company.published_at)}`
                    }, toDisplayString(company.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-1 py-3" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(company),
                        title: ownerTitle(company),
                        alt: unref(t)("owner"),
                        class: "h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"
                      }, null, 8, ["src", "title", "alt"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-3" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: logoUrl(company),
                        alt: companyTitle(company),
                        title: companyTitle(company),
                        class: "h-8 w-12 object-cover rounded-sm border border-slate-300 dark:border-slate-600"
                      }, null, 8, ["src", "alt", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-3" }, [
                    createVNode("div", { class: "text-left" }, [
                      createVNode("a", {
                        href: `/market/companies/${encodeURIComponent(company.url)}`,
                        target: "_blank",
                        rel: "noopener noreferrer",
                        class: "text-sky-700 dark:text-sky-200 text-xs hover:underline hover:text-amber-700 dark:hover:text-amber-200",
                        title: company.show_from_at ? `${unref(t)("show")}: ${company.show_from_at} / ${company.show_to_at}` : `${formatDate(company.published_at)}`
                      }, toDisplayString(truncateText(companyTitle(company), 70)), 9, ["href", "title"]),
                      createVNode("div", { class: "text-[11px] text-slate-500 dark:text-slate-300" }, toDisplayString(truncateText(company.legal_name, 70)), 1),
                      createVNode("div", { class: "text-[10px] text-slate-400 dark:text-slate-400" }, toDisplayString(companyTypeLabel(company.company_type)) + " / " + toDisplayString(vatLabel(company)), 1),
                      createVNode("div", { class: "text-[10px] text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(getStatusLabel(company.status)), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-left text-xs text-indigo-700 dark:text-indigo-300" }, toDisplayString(company.bin_iin || "—"), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-3 whitespace-nowrap flex flex-col items-center justify-center gap-3" }, [
                    createVNode("div", { class: "text-left text-xs" }, [
                      createVNode("div", { class: "text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(company.phone || "—"), 1),
                      createVNode("div", { class: "text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(company.email || ""), 1)
                    ]),
                    createVNode("div", { class: "text-left text-xs" }, [
                      createVNode("div", null, toDisplayString(company.city || "—"), 1),
                      createVNode("div", { class: "text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(company.region || ""), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-blue-600 dark:text-blue-300" }, toDisplayString(company.views), 1)
                  ]),
                  createVNode("td", { class: "px-1 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: company.left,
                        title: company.left ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleLeft: ($event) => _ctx.$emit("toggle-left", company)
                      }, null, 8, ["isActive", "title", "onToggleLeft"]),
                      createVNode(_sfc_main$6, {
                        isActive: company.main,
                        title: company.main ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleMain: ($event) => _ctx.$emit("toggle-main", company)
                      }, null, 8, ["isActive", "title", "onToggleMain"]),
                      createVNode(_sfc_main$7, {
                        isActive: company.right,
                        title: company.right ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleRight: ($event) => _ctx.$emit("toggle-right", company)
                      }, null, 8, ["isActive", "title", "onToggleRight"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(company.moderation_status).class],
                        title: company.moderation_note && company.moderated_at ? `${company.moderation_note} [${formatDate(company.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(company.moderation_status).text), 11, ["title"]),
                      createVNode(_sfc_main$8, {
                        isAdmin: __props.isAdmin,
                        status: (company == null ? void 0 : company.moderation_status) ?? 0,
                        initialNote: (company == null ? void 0 : company.moderation_note) || "",
                        mode: "toggle",
                        onSubmit: ({ status, note }) => _ctx.$emit("approve", company, status, note)
                      }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-1" }, [
                      createVNode(_sfc_main$9, {
                        isActive: company.activity,
                        title: company.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", company)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.marketCompanies.edit", { marketCompany: company.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => _ctx.$emit("delete", company)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-1 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedCompanies.includes(company.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", company.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketCompany/Table/CompanyTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "CompanyCardGrid",
  __ssrInlineRender: true,
  props: {
    companies: {
      type: Array,
      default: () => []
    },
    selectedCompanies: {
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
    const localCompanies = ref([]);
    watch(
      () => props.companies,
      (newVal) => {
        localCompanies.value = JSON.parse(
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
        localCompanies.value.map(
          (company) => company.id
        )
      );
    };
    const allSelected = () => {
      return !!localCompanies.value.length && localCompanies.value.every(
        (company) => props.selectedCompanies.includes(
          company.id
        )
      );
    };
    const companyTitle = (company) => {
      var _a;
      return ((_a = company == null ? void 0 : company.translation) == null ? void 0 : _a.title) || (company == null ? void 0 : company.legal_name) || `ID: ${company == null ? void 0 : company.id}`;
    };
    const companyShort = (company) => {
      var _a;
      return ((_a = company == null ? void 0 : company.translation) == null ? void 0 : _a.short) || "";
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
    const ownerName = (company) => {
      var _a;
      return ((_a = company == null ? void 0 : company.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (company) => {
      var _a;
      return ((_a = company == null ? void 0 : company.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (company) => {
      const owner = company == null ? void 0 : company.owner;
      if (!owner) {
        return t("noData");
      }
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const ownerAvatar = (company) => {
      var _a;
      return ((_a = company == null ? void 0 : company.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const logoUrl = (company) => {
      if (!(company == null ? void 0 : company.logo)) {
        return "/storage/market/companies/logos/default-image-light.png";
      }
      return company.logo.startsWith("/storage/") ? company.logo : `/storage/${company.logo}`;
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
        "ru-RU",
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
      return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + "…" : text;
    };
    const companyTypeLabelKeyMap = {
      company: "marketCompanyTypeCompany",
      entrepreneur: "marketCompanyTypeEntrepreneur",
      individual: "marketCompanyTypeIndividual"
    };
    const companyTypeLabel = (type) => {
      return t(
        companyTypeLabelKeyMap[type] || "marketCompanyTypeCompany"
      );
    };
    const vatLabel = (company) => {
      if (!(company == null ? void 0 : company.vat_enabled)) {
        return t("marketCompanyWithoutVat");
      }
      if (company == null ? void 0 : company.vat_rate) {
        return `${t("marketCompanyVat")} ${company.vat_rate}%`;
      }
      return t("marketCompanyWithVat");
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
    const openedOwnerBlocks = ref([]);
    const isOwnerBlockOpen = (companyId) => {
      return openedOwnerBlocks.value.includes(
        companyId
      );
    };
    const toggleOwnerBlock = (companyId) => {
      if (isOwnerBlockOpen(
        companyId
      )) {
        openedOwnerBlocks.value = openedOwnerBlocks.value.filter(
          (id) => id !== companyId
        );
        return;
      }
      openedOwnerBlocks.value.push(
        companyId
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedCompanies.length)}</div>`);
      if (localCompanies.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localCompanies.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localCompanies.value,
          "onUpdate:modelValue": ($event) => localCompanies.value = $event,
          "item-key": "id",
          handle: ".handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: company }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${company.sort}] / ${formatDate(company.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(company.id)}</div><button type="button" class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-300"${ssrRenderAttr("title", isOwnerBlockOpen(company.id) ? unref(t)("hideOwner") : unref(t)("showOwner"))}${_scopeId}><svg class="${ssrRenderClass([{ "rotate-180": isOwnerBlockOpen(company.id) }, "w-4 h-4 transition-transform duration-200"])}" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.17l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"${_scopeId}></path></svg></button></div><div class="flex items-center space-x-2"${_scopeId}><span class="${ssrRenderClass([moderationBadge(company.moderation_status).class, "text-[10px] px-2 py-0.5 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", company.moderation_note && company.moderated_at ? `${company.moderation_note} [${formatDate(company.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(company.moderation_status).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedCompanies.includes(company.id)) ? " checked" : ""}${_scopeId}></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-1"${_scopeId}><div style="${ssrRenderStyle(isOwnerBlockOpen(company.id) ? null : { display: "none" })}" class="flex flex-col items-center justify-center text-center"${_scopeId}><img${ssrRenderAttr("src", ownerAvatar(company))}${ssrRenderAttr("title", ownerTitle(company))}${ssrRenderAttr("alt", unref(t)("owner"))} class="h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"${_scopeId}><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerName(company))}${_scopeId}>${ssrInterpolate(ownerName(company))}</div>`);
              if (ownerEmail(company)) {
                _push2(`<div class="text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerEmail(company))}${_scopeId}>${ssrInterpolate(ownerEmail(company))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (company.show_from_at) {
                _push2(`<div class="flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("show"))}: ${ssrInterpolate(company.show_from_at)} / ${ssrInterpolate(company.show_to_at)}</div>`);
              } else {
                _push2(`<div class="flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(formatDate(company.published_at))}</div>`);
              }
              _push2(`</div><div class="relative w-full bg-slate-200 dark:bg-slate-900"${_scopeId}><img${ssrRenderAttr("src", logoUrl(company))}${ssrRenderAttr("alt", companyTitle(company))}${ssrRenderAttr("title", companyTitle(company))} class="h-32 w-full object-cover"${_scopeId}></div><a${ssrRenderAttr("href", `/market/companies/${encodeURIComponent(company.url)}`)} target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-sky-700 dark:text-sky-200 hover:underline hover:text-amber-700 dark:hover:text-amber-200 line-clamp-2 text-center"${_scopeId}>${ssrInterpolate(truncateText(companyTitle(company), 90))}</a><div class="text-center text-[11px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(truncateText(company.legal_name, 90))}</div><div class="flex justify-center flex-wrap gap-1 text-[10px]"${_scopeId}><span class="px-2 py-0.5 rounded-sm border bg-indigo-100 text-indigo-700 border-indigo-300 dark:bg-indigo-900/40 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(companyTypeLabel(company.company_type))}</span><span class="px-2 py-0.5 rounded-sm border bg-cyan-100 text-cyan-700 border-cyan-300 dark:bg-cyan-900/40 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(vatLabel(company))}</span></div><div class="font-semibold text-[12px] text-center text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(truncateText(companyShort(company), 120))}</div><div class="grid grid-cols-1 gap-0.5 text-[11px] text-slate-600 dark:text-slate-300"${_scopeId}><div class="text-center"${_scopeId}><span class="font-semibold"${_scopeId}>БИН/ИИН:</span> ${ssrInterpolate(company.bin_iin || "—")}</div><div class="text-center"${_scopeId}><span class="font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("city"))}: </span> ${ssrInterpolate(company.city || "—")} `);
              if (company.region) {
                _push2(`<span${_scopeId}> / ${ssrInterpolate(company.region)}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="text-center"${_scopeId}><span class="font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("contacts"))}: </span> ${ssrInterpolate(company.phone || "—")}</div>`);
              if (company.email) {
                _push2(`<div class="text-center line-clamp-1"${ssrRenderAttr("title", company.email)}${_scopeId}>${ssrInterpolate(company.email)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="font-semibold text-center my-1 text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("status"))}: </span> ${ssrInterpolate(getStatusLabel(company.status))}</div></div><div class="flex items-center justify-center gap-3 text-[11px] font-semibold text-slate-600 dark:text-slate-200"${_scopeId}><div class="flex items-center justify-center space-x-1"${ssrRenderAttr("title", unref(t)("views"))}${_scopeId}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"${_scopeId}><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"${_scopeId}></path></svg><span class="text-[12px] text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(company.views ?? 0)}</span></div><div class="flex justify-center space-x-1"${_scopeId}><span class="${ssrRenderClass([moderationBadge(company.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", company.moderation_note && company.moderated_at ? `${company.moderation_note} [${formatDate(company.moderated_at)}]` : null)}${_scopeId}>${ssrInterpolate(moderationBadge(company.moderation_status).text)}</span>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isAdmin: __props.isAdmin,
                status: (company == null ? void 0 : company.moderation_status) ?? 0,
                initialNote: (company == null ? void 0 : company.moderation_note) || "",
                mode: "toggle",
                onSubmit: ({ status, note }) => _ctx.$emit("approve", company, status, note)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div><div class="flex items-center justify-between px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: company.left,
                title: company.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => _ctx.$emit("toggle-left", company)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: company.main,
                title: company.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => _ctx.$emit("toggle-main", company)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: company.right,
                title: company.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => _ctx.$emit("toggle-right", company)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                isActive: company.activity,
                title: company.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", company)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.marketCompanies.edit", { marketCompany: company.id })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                onDelete: ($event) => _ctx.$emit("delete", company)
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
                        title: `[${company.sort}] / ${formatDate(company.published_at)}`
                      }, " ID: " + toDisplayString(company.id), 9, ["title"]),
                      createVNode("button", {
                        type: "button",
                        class: "text-slate-400 hover:text-blue-600 dark:hover:text-blue-300",
                        title: isOwnerBlockOpen(company.id) ? unref(t)("hideOwner") : unref(t)("showOwner"),
                        onClick: withModifiers(($event) => toggleOwnerBlock(company.id), ["prevent"])
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: ["w-4 h-4 transition-transform duration-200", { "rotate-180": isOwnerBlockOpen(company.id) }],
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
                        class: ["text-[10px] px-2 py-0.5 rounded-sm border font-semibold", moderationBadge(company.moderation_status).class],
                        title: company.moderation_note && company.moderated_at ? `${company.moderation_note} [${formatDate(company.moderated_at)}]` : null
                      }, toDisplayString(moderationBadge(company.moderation_status).text), 11, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedCompanies.includes(company.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", company.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-1" }, [
                    withDirectives(createVNode("div", { class: "flex flex-col items-center justify-center text-center" }, [
                      createVNode("img", {
                        src: ownerAvatar(company),
                        title: ownerTitle(company),
                        alt: unref(t)("owner"),
                        class: "h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"
                      }, null, 8, ["src", "title", "alt"]),
                      createVNode("div", {
                        class: "mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1",
                        title: ownerName(company)
                      }, toDisplayString(ownerName(company)), 9, ["title"]),
                      ownerEmail(company) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1",
                        title: ownerEmail(company)
                      }, toDisplayString(ownerEmail(company)), 9, ["title"])) : createCommentVNode("", true),
                      company.show_from_at ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(unref(t)("show")) + ": " + toDisplayString(company.show_from_at) + " / " + toDisplayString(company.show_to_at), 1)) : (openBlock(), createBlock("div", {
                        key: 2,
                        class: "flex flex-col items-center justify-center text-center text-[10px] text-slate-500 dark:text-slate-300"
                      }, toDisplayString(formatDate(company.published_at)), 1))
                    ], 512), [
                      [vShow, isOwnerBlockOpen(company.id)]
                    ]),
                    createVNode("div", { class: "relative w-full bg-slate-200 dark:bg-slate-900" }, [
                      createVNode("img", {
                        src: logoUrl(company),
                        alt: companyTitle(company),
                        title: companyTitle(company),
                        class: "h-32 w-full object-cover"
                      }, null, 8, ["src", "alt", "title"])
                    ]),
                    createVNode("a", {
                      href: `/market/companies/${encodeURIComponent(company.url)}`,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-xs font-semibold text-sky-700 dark:text-sky-200 hover:underline hover:text-amber-700 dark:hover:text-amber-200 line-clamp-2 text-center"
                    }, toDisplayString(truncateText(companyTitle(company), 90)), 9, ["href"]),
                    createVNode("div", { class: "text-center text-[11px] text-slate-500 dark:text-slate-300" }, toDisplayString(truncateText(company.legal_name, 90)), 1),
                    createVNode("div", { class: "flex justify-center flex-wrap gap-1 text-[10px]" }, [
                      createVNode("span", { class: "px-2 py-0.5 rounded-sm border bg-indigo-100 text-indigo-700 border-indigo-300 dark:bg-indigo-900/40 dark:text-indigo-300" }, toDisplayString(companyTypeLabel(company.company_type)), 1),
                      createVNode("span", { class: "px-2 py-0.5 rounded-sm border bg-cyan-100 text-cyan-700 border-cyan-300 dark:bg-cyan-900/40 dark:text-cyan-300" }, toDisplayString(vatLabel(company)), 1)
                    ]),
                    createVNode("div", { class: "font-semibold text-[12px] text-center text-teal-700 dark:text-teal-300" }, toDisplayString(truncateText(companyShort(company), 120)), 1),
                    createVNode("div", { class: "grid grid-cols-1 gap-0.5 text-[11px] text-slate-600 dark:text-slate-300" }, [
                      createVNode("div", { class: "text-center" }, [
                        createVNode("span", { class: "font-semibold" }, "БИН/ИИН:"),
                        createTextVNode(" " + toDisplayString(company.bin_iin || "—"), 1)
                      ]),
                      createVNode("div", { class: "text-center" }, [
                        createVNode("span", { class: "font-semibold" }, toDisplayString(unref(t)("city")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(company.city || "—") + " ", 1),
                        company.region ? (openBlock(), createBlock("span", { key: 0 }, " / " + toDisplayString(company.region), 1)) : createCommentVNode("", true)
                      ]),
                      createVNode("div", { class: "text-center" }, [
                        createVNode("span", { class: "font-semibold" }, toDisplayString(unref(t)("contacts")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(company.phone || "—"), 1)
                      ]),
                      company.email ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-center line-clamp-1",
                        title: company.email
                      }, toDisplayString(company.email), 9, ["title"])) : createCommentVNode("", true),
                      createVNode("div", { class: "font-semibold text-center my-1 text-fuchsia-700 dark:text-fuchsia-300" }, [
                        createVNode("span", null, toDisplayString(unref(t)("status")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(getStatusLabel(company.status)), 1)
                      ])
                    ]),
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
                        createVNode("span", { class: "text-[12px] text-slate-700 dark:text-slate-200" }, toDisplayString(company.views ?? 0), 1)
                      ], 8, ["title"]),
                      createVNode("div", { class: "flex justify-center space-x-1" }, [
                        createVNode("span", {
                          class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", moderationBadge(company.moderation_status).class],
                          title: company.moderation_note && company.moderated_at ? `${company.moderation_note} [${formatDate(company.moderated_at)}]` : null
                        }, toDisplayString(moderationBadge(company.moderation_status).text), 11, ["title"]),
                        createVNode(_sfc_main$8, {
                          isAdmin: __props.isAdmin,
                          status: (company == null ? void 0 : company.moderation_status) ?? 0,
                          initialNote: (company == null ? void 0 : company.moderation_note) || "",
                          mode: "toggle",
                          onSubmit: ({ status, note }) => _ctx.$emit("approve", company, status, note)
                        }, null, 8, ["isAdmin", "status", "initialNote", "onSubmit"])
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-between px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: company.left,
                        title: company.left ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleLeft: ($event) => _ctx.$emit("toggle-left", company)
                      }, null, 8, ["isActive", "title", "onToggleLeft"]),
                      createVNode(_sfc_main$6, {
                        isActive: company.main,
                        title: company.main ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleMain: ($event) => _ctx.$emit("toggle-main", company)
                      }, null, 8, ["isActive", "title", "onToggleMain"]),
                      createVNode(_sfc_main$7, {
                        isActive: company.right,
                        title: company.right ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleRight: ($event) => _ctx.$emit("toggle-right", company)
                      }, null, 8, ["isActive", "title", "onToggleRight"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$9, {
                        isActive: company.activity,
                        title: company.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", company)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.marketCompanies.edit", { marketCompany: company.id })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        onDelete: ($event) => _ctx.$emit("delete", company)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketCompany/View/CompanyCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminMarketCompaniesProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminMarketCompaniesPerPage: { type: Number, default: 6 },
    adminMarketCompaniesDefaultSort: { type: String, default: "idDesc" },
    companies: { type: [Array, Object], default: () => [] },
    companiesCount: { type: Number, default: 0 },
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
    const getCompanyTitle = (company) => {
      var _a;
      return ((_a = company == null ? void 0 : company.translation) == null ? void 0 : _a.title) || (company == null ? void 0 : company.legal_name) || `ID: ${company == null ? void 0 : company.id}`;
    };
    const normalize = (value) => {
      return (value ?? "").toString().trim().toLowerCase();
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      if (!value) return 0;
      const timestamp = new Date(value).getTime();
      return Number.isFinite(timestamp) ? timestamp : 0;
    };
    const moderationNum = (value) => {
      return safeNumber(value);
    };
    const compareStrings = (a, b) => {
      return normalize(a).localeCompare(
        normalize(b),
        locale.value
      );
    };
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_market_companies") || "cards"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_market_companies",
        value
      );
    });
    const itemsPerPage = ref(
      props.adminMarketCompaniesPerPage || 6
    );
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountMarketCompanies"),
        {
          value: newVal
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(
              `Показ ${newVal} компаний на странице.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления кол-ва компаний."
            );
          }
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminMarketCompaniesDefaultSort || "idDesc"
    );
    watch(sortParam, (newVal) => {
      router.put(
        route("admin.settings.updateAdminSortMarketCompanies"),
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
              "Сортировка компаний успешно изменена"
            );
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления сортировки компаний."
            );
          }
        }
      );
    });
    const localCompanies = ref([]);
    const companiesList = computed(() => {
      var _a, _b, _c, _d;
      if (Array.isArray(props.companies)) {
        return props.companies;
      }
      if (Array.isArray((_a = props.companies) == null ? void 0 : _a.data)) {
        return props.companies.data;
      }
      if (Array.isArray((_c = (_b = props.companies) == null ? void 0 : _b.data) == null ? void 0 : _c.data)) {
        return props.companies.data.data;
      }
      if (Array.isArray((_d = props.companies) == null ? void 0 : _d.resource)) {
        return props.companies.resource;
      }
      return [];
    });
    watch(
      companiesList,
      (newVal) => {
        localCompanies.value = JSON.parse(
          JSON.stringify(newVal || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const showConfirmDeleteModal = ref(false);
    const companyToDeleteId = ref(null);
    const companyToDeleteTitle = ref("");
    const confirmDelete = (companyOrId, title = null) => {
      if (typeof companyOrId === "object") {
        companyToDeleteId.value = companyOrId.id;
        companyToDeleteTitle.value = title || getCompanyTitle(companyOrId);
      } else {
        companyToDeleteId.value = companyOrId;
        companyToDeleteTitle.value = title || `ID: ${companyOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      companyToDeleteId.value = null;
      companyToDeleteTitle.value = "";
    };
    const deleteCompany = () => {
      if (companyToDeleteId.value === null) {
        return;
      }
      const idToDelete = companyToDeleteId.value;
      const titleToDelete = companyToDeleteTitle.value;
      router.delete(
        route(
          "admin.marketCompanies.destroy",
          {
            marketCompany: idToDelete
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Компания "${titleToDelete || "ID: " + idToDelete}" удалена.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(errors)[0];
            const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
            toast.error(
              `${errorMsg} (Компания: ${titleToDelete || "ID: " + idToDelete})`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    const patchLocalCompany = (companyId, callback) => {
      const index = localCompanies.value.findIndex(
        (company) => company.id === companyId
      );
      if (index !== -1) {
        callback(
          localCompanies.value[index]
        );
      }
    };
    const toggleActivity = (company) => {
      const newActivity = !company.activity;
      const title = getCompanyTitle(company);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.marketCompanies.updateActivity",
          {
            marketCompany: company.id
          }
        ),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalCompany(
              company.id,
              (node) => {
                node.activity = newActivity;
              }
            );
            toast.success(
              `Компания "${title}" ${actionText}.`
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
    const toggleLeft = (company) => {
      const newLeft = !company.left;
      const title = getCompanyTitle(company);
      router.put(
        route(
          "admin.actions.marketCompanies.updateLeft",
          {
            marketCompany: company.id
          }
        ),
        {
          left: newLeft
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalCompany(
              company.id,
              (node) => {
                node.left = newLeft;
              }
            );
            toast.success(
              `Позиция left для компании "${title}" обновлена.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors.left || errors.general || `Ошибка изменения left для "${title}".`
            );
          }
        }
      );
    };
    const toggleMain = (company) => {
      const newMain = !company.main;
      const title = getCompanyTitle(company);
      router.put(
        route(
          "admin.actions.marketCompanies.updateMain",
          {
            marketCompany: company.id
          }
        ),
        {
          main: newMain
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalCompany(
              company.id,
              (node) => {
                node.main = newMain;
              }
            );
            toast.success(
              `Позиция main для компании "${title}" обновлена.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors.main || errors.general || `Ошибка изменения main для "${title}".`
            );
          }
        }
      );
    };
    const toggleRight = (company) => {
      const newRight = !company.right;
      const title = getCompanyTitle(company);
      router.put(
        route(
          "admin.actions.marketCompanies.updateRight",
          {
            marketCompany: company.id
          }
        ),
        {
          right: newRight
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalCompany(
              company.id,
              (node) => {
                node.right = newRight;
              }
            );
            toast.success(
              `Позиция right для компании "${title}" обновлена.`
            );
          },
          onError: (errors) => {
            toast.error(
              errors.right || errors.general || `Ошибка изменения right для "${title}".`
            );
          }
        }
      );
    };
    const searchQuery = ref(
      props.search || ""
    );
    const currentPage = ref(1);
    const sortCompanies = (companies) => {
      const list = (companies || []).slice();
      switch (sortParam.value) {
        case "ownerNameAsc":
          return list.sort(
            (a, b) => {
              var _a, _b;
              return compareStrings(
                (_a = a == null ? void 0 : a.owner) == null ? void 0 : _a.name,
                (_b = b == null ? void 0 : b.owner) == null ? void 0 : _b.name
              );
            }
          );
        case "ownerNameDesc":
          return list.sort(
            (a, b) => {
              var _a, _b;
              return compareStrings(
                (_a = b == null ? void 0 : b.owner) == null ? void 0 : _a.name,
                (_b = a == null ? void 0 : a.owner) == null ? void 0 : _b.name
              );
            }
          );
        case "ownerEmailAsc":
          return list.sort(
            (a, b) => {
              var _a, _b;
              return compareStrings(
                (_a = a == null ? void 0 : a.owner) == null ? void 0 : _a.email,
                (_b = b == null ? void 0 : b.owner) == null ? void 0 : _b.email
              );
            }
          );
        case "ownerEmailDesc":
          return list.sort(
            (a, b) => {
              var _a, _b;
              return compareStrings(
                (_a = b == null ? void 0 : b.owner) == null ? void 0 : _a.email,
                (_b = a == null ? void 0 : a.owner) == null ? void 0 : _b.email
              );
            }
          );
        case "idAsc":
          return list.sort(
            (a, b) => safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id)
          );
        case "idDesc":
          return list.sort(
            (a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id)
          );
        case "sortAsc":
          return list.sort(
            (a, b) => safeNumber(a == null ? void 0 : a.sort) - safeNumber(b == null ? void 0 : b.sort) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id)
          );
        case "sortDesc":
          return list.sort(
            (a, b) => safeNumber(b == null ? void 0 : b.sort) - safeNumber(a == null ? void 0 : a.sort) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id)
          );
        case "titleAsc":
          return list.sort(
            (a, b) => compareStrings(
              getCompanyTitle(a),
              getCompanyTitle(b)
            )
          );
        case "titleDesc":
          return list.sort(
            (a, b) => compareStrings(
              getCompanyTitle(b),
              getCompanyTitle(a)
            )
          );
        case "urlAsc":
          return list.sort(
            (a, b) => compareStrings(
              a == null ? void 0 : a.url,
              b == null ? void 0 : b.url
            )
          );
        case "urlDesc":
          return list.sort(
            (a, b) => compareStrings(
              b == null ? void 0 : b.url,
              a == null ? void 0 : a.url
            )
          );
        case "legalNameAsc":
          return list.sort(
            (a, b) => compareStrings(
              a == null ? void 0 : a.legal_name,
              b == null ? void 0 : b.legal_name
            )
          );
        case "legalNameDesc":
          return list.sort(
            (a, b) => compareStrings(
              b == null ? void 0 : b.legal_name,
              a == null ? void 0 : a.legal_name
            )
          );
        case "companyTypeAsc":
          return list.sort(
            (a, b) => compareStrings(
              a == null ? void 0 : a.company_type,
              b == null ? void 0 : b.company_type
            )
          );
        case "companyTypeDesc":
          return list.sort(
            (a, b) => compareStrings(
              b == null ? void 0 : b.company_type,
              a == null ? void 0 : a.company_type
            )
          );
        case "cityAsc":
          return list.sort(
            (a, b) => compareStrings(
              a == null ? void 0 : a.city,
              b == null ? void 0 : b.city
            )
          );
        case "cityDesc":
          return list.sort(
            (a, b) => compareStrings(
              b == null ? void 0 : b.city,
              a == null ? void 0 : a.city
            )
          );
        case "views":
        case "viewsDesc":
          return list.sort(
            (a, b) => safeNumber(b == null ? void 0 : b.views) - safeNumber(a == null ? void 0 : a.views)
          );
        case "viewsAsc":
          return list.sort(
            (a, b) => safeNumber(a == null ? void 0 : a.views) - safeNumber(b == null ? void 0 : b.views)
          );
        case "publishedAtAsc":
          return list.sort(
            (a, b) => safeDate(a == null ? void 0 : a.published_at) - safeDate(b == null ? void 0 : b.published_at)
          );
        case "publishedAtDesc":
          return list.sort(
            (a, b) => safeDate(b == null ? void 0 : b.published_at) - safeDate(a == null ? void 0 : a.published_at)
          );
        case "showFromAtAsc":
          return list.sort(
            (a, b) => safeDate(a == null ? void 0 : a.show_from_at) - safeDate(b == null ? void 0 : b.show_from_at)
          );
        case "showFromAtDesc":
          return list.sort(
            (a, b) => safeDate(b == null ? void 0 : b.show_from_at) - safeDate(a == null ? void 0 : a.show_from_at)
          );
        case "showToAtAsc":
          return list.sort(
            (a, b) => safeDate(a == null ? void 0 : a.show_to_at) - safeDate(b == null ? void 0 : b.show_to_at)
          );
        case "showToAtDesc":
          return list.sort(
            (a, b) => safeDate(b == null ? void 0 : b.show_to_at) - safeDate(a == null ? void 0 : a.show_to_at)
          );
        case "createdAtAsc":
        case "dateAsc":
          return list.sort(
            (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at)
          );
        case "createdAtDesc":
        case "dateDesc":
          return list.sort(
            (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at)
          );
        case "updatedAtAsc":
          return list.sort(
            (a, b) => safeDate(a == null ? void 0 : a.updated_at) - safeDate(b == null ? void 0 : b.updated_at)
          );
        case "updatedAtDesc":
          return list.sort(
            (a, b) => safeDate(b == null ? void 0 : b.updated_at) - safeDate(a == null ? void 0 : a.updated_at)
          );
        case "activityAsc":
          return list.sort(
            (a, b) => Number(!!(a == null ? void 0 : a.activity)) - Number(!!(b == null ? void 0 : b.activity))
          );
        case "activityDesc":
          return list.sort(
            (a, b) => Number(!!(b == null ? void 0 : b.activity)) - Number(!!(a == null ? void 0 : a.activity))
          );
        case "activity":
          return list.filter(
            (company) => !!(company == null ? void 0 : company.activity)
          );
        case "inactive":
          return list.filter(
            (company) => !(company == null ? void 0 : company.activity)
          );
        case "leftAsc":
          return list.sort(
            (a, b) => Number(!!(a == null ? void 0 : a.left)) - Number(!!(b == null ? void 0 : b.left))
          );
        case "leftDesc":
          return list.sort(
            (a, b) => Number(!!(b == null ? void 0 : b.left)) - Number(!!(a == null ? void 0 : a.left))
          );
        case "left":
          return list.filter(
            (company) => !!(company == null ? void 0 : company.left)
          );
        case "noLeft":
          return list.filter(
            (company) => !(company == null ? void 0 : company.left)
          );
        case "mainAsc":
          return list.sort(
            (a, b) => Number(!!(a == null ? void 0 : a.main)) - Number(!!(b == null ? void 0 : b.main))
          );
        case "mainDesc":
          return list.sort(
            (a, b) => Number(!!(b == null ? void 0 : b.main)) - Number(!!(a == null ? void 0 : a.main))
          );
        case "main":
          return list.filter(
            (company) => !!(company == null ? void 0 : company.main)
          );
        case "noMain":
          return list.filter(
            (company) => !(company == null ? void 0 : company.main)
          );
        case "rightAsc":
          return list.sort(
            (a, b) => Number(!!(a == null ? void 0 : a.right)) - Number(!!(b == null ? void 0 : b.right))
          );
        case "rightDesc":
          return list.sort(
            (a, b) => Number(!!(b == null ? void 0 : b.right)) - Number(!!(a == null ? void 0 : a.right))
          );
        case "right":
          return list.filter(
            (company) => !!(company == null ? void 0 : company.right)
          );
        case "noRight":
          return list.filter(
            (company) => !(company == null ? void 0 : company.right)
          );
        case "moderationStatusAsc":
          return list.sort(
            (a, b) => moderationNum(a == null ? void 0 : a.moderation_status) - moderationNum(b == null ? void 0 : b.moderation_status)
          );
        case "moderationStatusDesc":
          return list.sort(
            (a, b) => moderationNum(b == null ? void 0 : b.moderation_status) - moderationNum(a == null ? void 0 : a.moderation_status)
          );
        case "moderationPending":
          return list.filter(
            (company) => moderationNum(
              company == null ? void 0 : company.moderation_status
            ) === 0
          );
        case "moderationApproved":
          return list.filter(
            (company) => moderationNum(
              company == null ? void 0 : company.moderation_status
            ) === 1
          );
        case "moderationRejected":
          return list.filter(
            (company) => moderationNum(
              company == null ? void 0 : company.moderation_status
            ) === 2
          );
        default:
          return list;
      }
    };
    const filteredCompanies = computed(() => {
      const query = normalize(
        searchQuery.value
      );
      let filtered = localCompanies.value || [];
      if (!query) {
        return sortCompanies(
          filtered
        );
      }
      filtered = filtered.filter((company) => {
        var _a, _b, _c, _d, _e;
        const values = [
          (_a = company == null ? void 0 : company.translation) == null ? void 0 : _a.title,
          (_b = company == null ? void 0 : company.translation) == null ? void 0 : _b.short,
          (_c = company == null ? void 0 : company.translation) == null ? void 0 : _c.description,
          company == null ? void 0 : company.url,
          company == null ? void 0 : company.legal_name,
          company == null ? void 0 : company.bin_iin,
          company == null ? void 0 : company.email,
          company == null ? void 0 : company.phone,
          company == null ? void 0 : company.city,
          (_d = company == null ? void 0 : company.owner) == null ? void 0 : _d.name,
          (_e = company == null ? void 0 : company.owner) == null ? void 0 : _e.email
        ];
        return values.some(
          (value) => normalize(value).includes(query)
        );
      });
      return sortCompanies(
        filtered
      );
    });
    const paginatedCompanies = computed(() => {
      const perPage = Number(
        itemsPerPage.value || 10
      );
      const start = (currentPage.value - 1) * perPage;
      return filteredCompanies.value.slice(
        start,
        start + perPage
      );
    });
    const displayedCompanies = computed(() => {
      return props.useServerProcessing ? companiesList.value : paginatedCompanies.value;
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
    const selectedCompanies = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedCompanies.value.map(
        (company) => company.id
      );
      if (checked) {
        selectedCompanies.value = [
          .../* @__PURE__ */ new Set([
            ...selectedCompanies.value,
            ...ids
          ])
        ];
        return;
      }
      selectedCompanies.value = selectedCompanies.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectCompany = (companyId) => {
      const index = selectedCompanies.value.indexOf(
        companyId
      );
      if (index > -1) {
        selectedCompanies.value.splice(
          index,
          1
        );
        return;
      }
      selectedCompanies.value.push(
        companyId
      );
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedCompanies.value.length) {
        toast.warning(
          "Выберите компании для активации/деактивации"
        );
        return;
      }
      const idsToUpdate = [
        ...selectedCompanies.value
      ];
      router.put(
        route(
          "admin.actions.marketCompanies.bulkUpdateActivity"
        ),
        {
          ids: idsToUpdate,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localCompanies.value = localCompanies.value.map(
              (company) => {
                if (idsToUpdate.includes(
                  company.id
                )) {
                  return {
                    ...company,
                    activity: newActivity
                  };
                }
                return company;
              }
            );
            selectedCompanies.value = [];
            toast.success(
              "Активность компаний массово обновлена"
            );
          },
          onError: (errors) => {
            const msg = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности";
            toast.error(msg);
          }
        }
      );
    };
    const bulkToggleFlag = (field, newValue, routeName, successMessage) => {
      if (!selectedCompanies.value.length) {
        toast.warning(
          "Выберите компании для массового действия"
        );
        return;
      }
      const idsToUpdate = [
        ...selectedCompanies.value
      ];
      router.put(
        route(routeName),
        {
          ids: idsToUpdate,
          [field]: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localCompanies.value = localCompanies.value.map(
              (company) => {
                if (idsToUpdate.includes(
                  company.id
                )) {
                  return {
                    ...company,
                    [field]: newValue
                  };
                }
                return company;
              }
            );
            selectedCompanies.value = [];
            toast.success(
              successMessage
            );
          },
          onError: (errors) => {
            const msg = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления";
            toast.error(msg);
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedCompanies.value.length) {
        toast.warning(
          "Выберите хотя бы одну компанию для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные компании?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.marketCompanies.bulkDestroy"
        ),
        {
          data: {
            ids: selectedCompanies.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedCompanies.value = [];
            toast.success(
              "Массовое удаление компаний успешно завершено."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(errors)[0];
            toast.error(
              errors[errorKey] || "Произошла ошибка при удалении компаний."
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
      } else if (action === "left") {
        bulkToggleFlag(
          "left",
          true,
          "admin.actions.marketCompanies.bulkUpdateLeft",
          "Компании добавлены в левую колонку"
        );
      } else if (action === "noLeft") {
        bulkToggleFlag(
          "left",
          false,
          "admin.actions.marketCompanies.bulkUpdateLeft",
          "Компании убраны из левой колонки"
        );
      } else if (action === "main") {
        bulkToggleFlag(
          "main",
          true,
          "admin.actions.marketCompanies.bulkUpdateMain",
          "Компании добавлены в главный блок"
        );
      } else if (action === "noMain") {
        bulkToggleFlag(
          "main",
          false,
          "admin.actions.marketCompanies.bulkUpdateMain",
          "Компании убраны из главного блока"
        );
      } else if (action === "right") {
        bulkToggleFlag(
          "right",
          true,
          "admin.actions.marketCompanies.bulkUpdateRight",
          "Компании добавлены в правую колонку"
        );
      } else if (action === "noRight") {
        bulkToggleFlag(
          "right",
          false,
          "admin.actions.marketCompanies.bulkUpdateRight",
          "Компании убраны из правой колонки"
        );
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const approveCompany = (company, status = 1, note = "") => {
      if (!(company == null ? void 0 : company.id)) {
        return;
      }
      router.put(
        route(
          "admin.actions.marketCompanies.approve",
          {
            marketCompany: company.id
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
            patchLocalCompany(
              company.id,
              (node) => {
                node.moderation_status = status;
                node.is_approved = status === 1;
                node.moderation_note = note;
              }
            );
            toast.success(
              status === 1 ? "Компания одобрена" : "Компания отклонена"
            );
          },
          onError: () => {
            toast.error(
              "Ошибка модерации компании"
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
          "admin.actions.marketCompanies.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            items.forEach((item) => {
              patchLocalCompany(
                item.id,
                (company) => {
                  company.sort = item.sort;
                }
              );
            });
            toast.success(
              "Сортировка компаний обновлена"
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка сортировки компаний:",
              errors
            );
            toast.error(
              errors.message || "Ошибка обновления сортировки"
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("marketCompanies")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("marketCompanies"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("marketCompanies")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("marketCompanies")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$c, {
              href: _ctx.route("admin.marketCompanies.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addMarketCompany"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addMarketCompany")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              "setting-key": "adminMarketCompaniesProcessingMode",
              mode: __props.adminMarketCompaniesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.companiesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.companiesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$e, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.companiesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$f, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.companiesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountMarketCompanies"
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
            if (__props.companiesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$i, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.companiesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.companiesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$j, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.companiesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredCompanies.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.companies }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                companies: displayedCompanies.value,
                "selected-companies": selectedCompanies.value,
                "is-admin": isAdmin.value,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectCompany,
                onToggleAll: toggleAll,
                onApprove: approveCompany
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                companies: displayedCompanies.value,
                "selected-companies": selectedCompanies.value,
                "is-admin": isAdmin.value,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectCompany,
                onToggleAll: toggleAll,
                onApprove: approveCompany
              }, null, _parent2, _scopeId));
            }
            if (__props.companiesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredCompanies.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.companies }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$m, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteCompany,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$c, {
                      href: _ctx.route("admin.marketCompanies.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addMarketCompany")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$d, {
                      "setting-key": "adminMarketCompaniesProcessingMode",
                      mode: __props.adminMarketCompaniesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.companiesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.companiesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.companiesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$f, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.companiesCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountMarketCompanies"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (value) => sortParam.value = value
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.companiesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$i, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.companiesCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$j, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.companiesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredCompanies.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.companies
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    companies: displayedCompanies.value,
                    "selected-companies": selectedCompanies.value,
                    "is-admin": isAdmin.value,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectCompany,
                    onToggleAll: toggleAll,
                    onApprove: approveCompany
                  }, null, 8, ["companies", "selected-companies", "is-admin"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    companies: displayedCompanies.value,
                    "selected-companies": selectedCompanies.value,
                    "is-admin": isAdmin.value,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectCompany,
                    onToggleAll: toggleAll,
                    onApprove: approveCompany
                  }, null, 8, ["companies", "selected-companies", "is-admin"])),
                  __props.companiesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredCompanies.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.companies
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$m, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteCompany,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketCompanies/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
