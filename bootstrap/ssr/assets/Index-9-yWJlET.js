import { mergeProps, unref, useSSRContext, ref, watch, computed, withCtx, createTextVNode, toDisplayString, createVNode, createBlock, createCommentVNode, openBlock } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderList, ssrRenderClass, ssrRenderComponent } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$8 } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$9, a as _sfc_main$f } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$c } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$a, a as _sfc_main$d, b as _sfc_main$e } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$b } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$g } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$7 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$4 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$6 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$5 } from "./CloneIconButton-BfVfDOWt.js";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>──────────────────</option><option value="numberAsc">${ssrInterpolate(unref(t)("sortNumberAsc"))}</option><option value="numberDesc">${ssrInterpolate(unref(t)("sortNumberDesc"))}</option><option disabled>──────────────────</option><option value="createdDesc">${ssrInterpolate(unref(t)("sortNewestFirst"))}</option><option value="createdAsc">${ssrInterpolate(unref(t)("sortOldestFirst"))}</option><option disabled>──────────────────</option><option value="totalDesc">${ssrInterpolate(unref(t)("sortTotalDesc"))}</option><option value="totalAsc">${ssrInterpolate(unref(t)("sortTotalAsc"))}</option><option disabled>──────────────────</option><option value="buyerAsc">${ssrInterpolate(unref(t)("sortBuyerAsc"))}</option><option value="buyerDesc">${ssrInterpolate(unref(t)("sortBuyerDesc"))}</option><option disabled>──────────────────</option><option value="statusAsc">${ssrInterpolate(unref(t)("sortStatusAsc"))}</option><option value="statusDesc">${ssrInterpolate(unref(t)("sortStatusDesc"))}</option><option disabled>──────────────────</option><option value="paymentStatusAsc">${ssrInterpolate(unref(t)("sortPaymentStatusAsc"))}</option><option value="paymentStatusDesc">${ssrInterpolate(unref(t)("sortPaymentStatusDesc"))}</option><option disabled>──────────────────</option><option value="paidFirst">${ssrInterpolate(unref(t)("sortPaidFirst"))}</option><option value="paidLast">${ssrInterpolate(unref(t)("sortPaidLast"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolOrder/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "OrderTable",
  __ssrInlineRender: true,
  props: {
    orders: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "delete",
    "clone"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const emit = __emit;
    const paymentStatusLabelKeyMap = {
      pending: "statusPaidPending",
      paid: "statusPaid",
      succeeded: "statusPaid",
      failed: "statusPaidError",
      partial: "statusPaidPartial",
      partially_refunded: "statusPaidPartial",
      refunded: "statusPaidRefunded"
    };
    const orderStatusLabelKeyMap = {
      new: "statusOrderNew",
      processing: "statusOrderProcessing",
      completed: "statusOrderCompleted",
      cancelled: "statusOrderCancelled",
      refunded: "statusPaidRefunded"
    };
    const getPaymentStatusLabel = (status) => {
      if (!status) return "—";
      const key = paymentStatusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getOrderStatusLabel = (status) => {
      if (!status) return "—";
      const key = orderStatusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getPaymentStatusClasses = (status) => {
      switch (status) {
        case "paid":
        case "succeeded":
          return "border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100";
        case "pending":
          return "border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100";
        case "failed":
          return "border-rose-400 bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-100";
        case "partial":
        case "partially_refunded":
          return "border-violet-400 bg-violet-100 dark:bg-violet-900/40 text-violet-800 dark:text-violet-100";
        case "refunded":
          return "border-slate-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100";
        default:
          return "border-blue-400 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-100";
      }
    };
    const getOrderStatusClasses = (status) => {
      switch (status) {
        case "completed":
          return "border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100";
        case "processing":
          return "border-sky-400 bg-sky-100 dark:bg-sky-900/40 text-sky-800 dark:text-sky-100";
        case "new":
          return "border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100";
        case "cancelled":
        case "refunded":
          return "border-rose-400 bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-100";
        default:
          return "border-slate-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100";
      }
    };
    const formatDateTime = (value) => {
      if (!value) return "—";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return String(value);
      }
      return date.toLocaleString("ru-RU");
    };
    const formatMoney = (value, currency = "USD") => {
      if (value === null || typeof value === "undefined") {
        return "—";
      }
      const number = Number(value);
      if (Number.isNaN(number)) {
        return String(value);
      }
      return `${number.toFixed(2)} ${currency}`;
    };
    const getBuyerName = (order) => {
      var _a;
      return (order == null ? void 0 : order.buyer_name) || ((_a = order == null ? void 0 : order.user) == null ? void 0 : _a.name) || "—";
    };
    const getBuyerEmail = (order) => {
      var _a;
      return (order == null ? void 0 : order.buyer_email) || ((_a = order == null ? void 0 : order.user) == null ? void 0 : _a.email) || "";
    };
    const getBuyerId = (order) => {
      var _a;
      return ((_a = order == null ? void 0 : order.user) == null ? void 0 : _a.id) ?? (order == null ? void 0 : order.user_id) ?? null;
    };
    const getCourseTitle = (order) => {
      var _a, _b;
      return ((_b = (_a = order == null ? void 0 : order.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getScheduleTitle = (order) => {
      var _a, _b;
      return ((_b = (_a = order == null ? void 0 : order.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="overflow-x-auto">`);
      if (__props.orders.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-xs uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><div class="font-medium text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("numberOrder"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("buyer"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("course"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("amount"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("date"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("statuses"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th></tr></thead><tbody><!--[-->`);
        ssrRenderList(__props.orders, (order) => {
          _push(`<tr class="text-xs font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"><td class="px-2 py-3 whitespace-nowrap"><div class="text-left text-blue-600 dark:text-blue-200">${ssrInterpolate(order.id)}</div></td><td class="px-2 py-3 whitespace-nowrap text-amber-600 dark:text-amber-200"><div class="font-semibold">${ssrInterpolate(order.number || `#${order.id}`)}</div>`);
          if (order.external_id) {
            _push(`<div class="text-[10px] text-slate-500 dark:text-slate-300">${ssrInterpolate(order.external_id)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="text-[10px] text-blue-600 dark:text-blue-200">${ssrInterpolate(formatDateTime(order.updated_at || order.created_at))}</div></td><td class="px-2 py-3 whitespace-nowrap text-slate-800 dark:text-slate-100">`);
          if (getBuyerId(order)) {
            _push(`<div class="text-[10px] text-gray-500 dark:text-gray-300">${ssrInterpolate(unref(t)("buyer"))} ID: ${ssrInterpolate(getBuyerId(order))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="text-left font-medium">${ssrInterpolate(getBuyerName(order))}</div>`);
          if (getBuyerEmail(order)) {
            _push(`<div><span class="text-xs text-gray-500 dark:text-gray-300"> Email: </span><span class="ml-1">${ssrInterpolate(getBuyerEmail(order))}</span></div>`);
          } else {
            _push(`<!---->`);
          }
          if (order.buyer_phone) {
            _push(`<div class="mt-0.5"><span class="text-xs text-gray-500 dark:text-gray-300">${ssrInterpolate(unref(t)("phone"))}: </span><span class="ml-1">${ssrInterpolate(order.buyer_phone)}</span></div>`);
          } else {
            _push(`<!---->`);
          }
          if (!getBuyerEmail(order) && !order.buyer_phone) {
            _push(`<div> — </div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</td><td class="px-2 py-3">`);
          if (getCourseTitle(order)) {
            _push(`<div class="text-[11px] text-amber-700 dark:text-amber-200">${ssrInterpolate(getCourseTitle(order))}</div>`);
          } else {
            _push(`<!---->`);
          }
          if (getScheduleTitle(order)) {
            _push(`<div class="text-[11px] text-teal-700 dark:text-teal-200">${ssrInterpolate(getScheduleTitle(order))}</div>`);
          } else {
            _push(`<!---->`);
          }
          if (!getCourseTitle(order) && !getScheduleTitle(order)) {
            _push(`<div> — </div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</td><td class="px-2 py-3 text-rose-600 dark:text-rose-200"><div class="font-semibold">${ssrInterpolate(formatMoney(order.total, order.currency || "USD"))}</div><div class="text-[10px] text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("subtotal"))}: <span class="font-bold">${ssrInterpolate(formatMoney(order.subtotal, order.currency || "USD"))}</span></div></td><td class="px-2 py-3 text-blue-800 dark:text-blue-200"><div>${ssrInterpolate(formatDateTime(order.created_at))}</div>`);
          if (order.paid_at) {
            _push(`<div class="text-[10px] text-emerald-700 dark:text-emerald-300">${ssrInterpolate(unref(t)("paidAt"))}: ${ssrInterpolate(formatDateTime(order.paid_at))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</td><td class="px-2 py-3 whitespace-nowrap"><div class="flex flex-col gap-1"><span class="${ssrRenderClass([
            "inline-flex w-fit items-center rounded-sm px-2 py-0.5 text-[11px] font-semibold border",
            order.is_paid ? "border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100" : "border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100"
          ])}">${ssrInterpolate(order.is_paid ? unref(t)("isPaid") : unref(t)("notPaid"))}</span><span class="${ssrRenderClass([
            "inline-flex w-fit items-center rounded-sm px-2 py-0.5 text-[11px] font-semibold border",
            getPaymentStatusClasses(order.payment_status)
          ])}">${ssrInterpolate(getPaymentStatusLabel(order.payment_status))}</span><span class="${ssrRenderClass([
            "inline-flex w-fit items-center rounded-sm px-2 py-0.5 text-[11px] font-semibold border",
            getOrderStatusClasses(order.status)
          ])}">${ssrInterpolate(getOrderStatusLabel(order.status))}</span></div></td><td class="px-2 py-3 whitespace-nowrap"><div class="flex justify-end space-x-2">`);
          _push(ssrRenderComponent(_sfc_main$4, {
            href: _ctx.route("admin.schoolOrders.edit", {
              schoolOrder: order.id
            })
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$5, {
            title: unref(t)("clone"),
            onClone: ($event) => emit("clone", order)
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$6, {
            title: unref(t)("delete"),
            onDelete: ($event) => emit("delete", order)
          }, null, _parent));
          _push(`</div></td></tr>`);
        });
        _push(`<!--]--></tbody></table>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolOrder/Table/OrderTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "OrderCardGrid",
  __ssrInlineRender: true,
  props: {
    orders: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "clone",
    "delete"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const emit = __emit;
    const paymentStatusLabelKeyMap = {
      pending: "statusPaidPending",
      paid: "statusPaid",
      succeeded: "statusPaid",
      failed: "statusPaidError",
      partial: "statusPaidPartial",
      partially_refunded: "statusPaidPartial",
      refunded: "statusPaidRefunded"
    };
    const orderStatusLabelKeyMap = {
      new: "statusOrderNew",
      processing: "statusOrderProcessing",
      completed: "statusOrderCompleted",
      cancelled: "statusOrderCancelled",
      refunded: "statusPaidRefunded"
    };
    const getPaymentStatusLabel = (status) => {
      if (!status) return "—";
      const key = paymentStatusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getOrderStatusLabel = (status) => {
      if (!status) return "—";
      const key = orderStatusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getPaymentStatusClasses = (status) => {
      switch (status) {
        case "paid":
        case "succeeded":
          return "border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100";
        case "pending":
          return "border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100";
        case "failed":
          return "border-rose-400 bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-100";
        case "partial":
        case "partially_refunded":
          return "border-violet-400 bg-violet-100 dark:bg-violet-900/40 text-violet-800 dark:text-violet-100";
        case "refunded":
          return "border-slate-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100";
        default:
          return "border-blue-400 bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-100";
      }
    };
    const getOrderStatusClasses = (status) => {
      switch (status) {
        case "completed":
          return "border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100";
        case "processing":
          return "border-sky-400 bg-sky-100 dark:bg-sky-900/40 text-sky-800 dark:text-sky-100";
        case "new":
          return "border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100";
        case "cancelled":
        case "refunded":
          return "border-rose-400 bg-rose-100 dark:bg-rose-900/40 text-rose-800 dark:text-rose-100";
        default:
          return "border-slate-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-100";
      }
    };
    const formatDateTime = (value) => {
      if (!value) return "—";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return String(value);
      }
      return date.toLocaleString("ru-RU");
    };
    const formatMoney = (value, currency = "USD") => {
      if (value === null || typeof value === "undefined") {
        return "—";
      }
      const number = Number(value);
      if (Number.isNaN(number)) {
        return String(value);
      }
      return `${number.toFixed(2)} ${currency}`;
    };
    const getBuyerName = (order) => {
      var _a;
      return (order == null ? void 0 : order.buyer_name) || ((_a = order == null ? void 0 : order.user) == null ? void 0 : _a.name) || "—";
    };
    const getBuyerEmail = (order) => {
      var _a;
      return (order == null ? void 0 : order.buyer_email) || ((_a = order == null ? void 0 : order.user) == null ? void 0 : _a.email) || "";
    };
    const getBuyerId = (order) => {
      var _a;
      return ((_a = order == null ? void 0 : order.user) == null ? void 0 : _a.id) ?? (order == null ? void 0 : order.user_id) ?? null;
    };
    const getCourseTitle = (order) => {
      var _a, _b;
      return ((_b = (_a = order == null ? void 0 : order.course) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getScheduleTitle = (order) => {
      var _a, _b;
      return ((_b = (_a = order == null ? void 0 : order.schedule) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}>`);
      if (__props.orders.length) {
        _push(`<div class="p-3"><div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"><!--[-->`);
        ssrRenderList(__props.orders, (order) => {
          _push(`<article class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"><div class="w-full flex flex-row items-center justify-between gap-2"><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"> ID: ${ssrInterpolate(order.id)}</div><span class="${ssrRenderClass([
            "inline-flex items-center px-2 py-0.5 rounded-sm border text-[11px] font-semibold",
            getPaymentStatusClasses(order.payment_status)
          ])}"${ssrRenderAttr("title", unref(t)("statusPayment"))}>${ssrInterpolate(getPaymentStatusLabel(order.payment_status))}</span></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-2 text-sm text-slate-700 dark:text-slate-100"><div class="text-center"><div class="text-xs font-semibold text-amber-600 dark:text-amber-200">${ssrInterpolate(order.number || `#${order.id}`)}</div>`);
          if (order.external_id) {
            _push(`<div class="text-[10px] text-slate-500 dark:text-slate-300">${ssrInterpolate(order.external_id)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="mt-0.5 font-semibold text-[10px] text-blue-600 dark:text-blue-200">${ssrInterpolate(formatDateTime(order.updated_at || order.created_at))}</div></div><div class="text-center space-y-0.5"><div class="text-[12px] font-medium text-slate-900 dark:text-slate-50">${ssrInterpolate(getBuyerName(order))} `);
          if (getBuyerId(order)) {
            _push(`<span class="text-slate-500 dark:text-slate-400"> [ID: ${ssrInterpolate(getBuyerId(order))}] </span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
          if (order.buyer_phone) {
            _push(`<div class="text-[11px] text-slate-500 dark:text-slate-300">${ssrInterpolate(order.buyer_phone)}</div>`);
          } else {
            _push(`<!---->`);
          }
          if (getBuyerEmail(order)) {
            _push(`<div class="text-[11px] text-slate-500 dark:text-slate-300">${ssrInterpolate(getBuyerEmail(order))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="text-center space-y-0.5">`);
          if (getCourseTitle(order)) {
            _push(`<div class="text-[11px] font-semibold text-amber-700 dark:text-amber-200">${ssrInterpolate(getCourseTitle(order))}</div>`);
          } else {
            _push(`<!---->`);
          }
          if (getScheduleTitle(order)) {
            _push(`<div class="text-[11px] text-teal-700 dark:text-teal-200">${ssrInterpolate(getScheduleTitle(order))}</div>`);
          } else {
            _push(`<!---->`);
          }
          if (!getCourseTitle(order) && !getScheduleTitle(order)) {
            _push(`<div class="text-[11px] text-slate-400"> — </div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="flex justify-center text-xs font-semibold text-rose-600 dark:text-rose-200">${ssrInterpolate(formatMoney(order.total, order.currency || "USD"))}</div>`);
          if (order.paid_at) {
            _push(`<div class="text-center text-[10px] text-emerald-700 dark:text-emerald-300">${ssrInterpolate(unref(t)("paidAt"))}: ${ssrInterpolate(formatDateTime(order.paid_at))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="flex flex-wrap justify-center gap-2 mt-2 text-[11px] font-semibold"><span class="${ssrRenderClass([
            "px-2 py-0.5 rounded-sm border",
            getOrderStatusClasses(order.status)
          ])}"${ssrRenderAttr("title", unref(t)("statusOrder"))}>${ssrInterpolate(getOrderStatusLabel(order.status))}</span><span class="${ssrRenderClass([
            "px-2 py-0.5 rounded-sm border",
            order.is_paid ? "border-emerald-400 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-100" : "border-amber-400 bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-100"
          ])}"${ssrRenderAttr("title", unref(t)("statusPaid"))}>${ssrInterpolate(order.is_paid ? unref(t)("isPaid") : unref(t)("notPaid"))}</span></div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center space-x-3">`);
          _push(ssrRenderComponent(_sfc_main$4, {
            href: _ctx.route("admin.schoolOrders.edit", {
              schoolOrder: order.id
            })
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$5, {
            title: unref(t)("clone"),
            onClone: ($event) => emit("clone", order)
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$6, {
            title: unref(t)("delete"),
            onDelete: ($event) => emit("delete", order)
          }, null, _parent));
          _push(`</div></footer></article>`);
        });
        _push(`<!--]--></div></div>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolOrder/View/OrderCardGrid.vue");
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
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    adminSchoolOrdersProcessingMode: {
      type: String,
      default: "frontend"
    },
    orders: {
      type: [Array, Object],
      default: () => []
    },
    ordersCount: {
      type: Number,
      default: 0
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    adminSchoolOrdersPerPage: {
      type: Number,
      default: 10
    },
    adminSchoolOrdersDefaultSort: {
      type: String,
      default: "idDesc"
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
    var _a;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_orders") || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_orders",
        value
      );
    });
    const ordersList = computed(() => {
      var _a2;
      if (Array.isArray(props.orders)) {
        return props.orders;
      }
      if (Array.isArray((_a2 = props.orders) == null ? void 0 : _a2.data)) {
        return props.orders.data;
      }
      return [];
    });
    const localOrders = ref([]);
    watch(
      ordersList,
      (newValue) => {
        localOrders.value = JSON.parse(
          JSON.stringify(newValue || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const itemsPerPage = ref(
      props.adminSchoolOrdersPerPage || 10
    );
    watch(itemsPerPage, (newValue) => {
      router.put(
        route(
          "admin.settings.updateAdminCountSchoolOrders"
        ),
        {
          value: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(
              `Показ ${newValue} элементов на странице.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.value) || "Ошибка обновления кол-ва элементов."
            );
          }
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminSchoolOrdersDefaultSort || "idDesc"
    );
    const currentPage = ref(1);
    watch(sortParam, (newValue) => {
      currentPage.value = 1;
      router.put(
        route(
          "admin.settings.updateAdminSortSchoolOrders"
        ),
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
              "Сортировка успешно изменена"
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.value) || "Ошибка обновления сортировки."
            );
          }
        }
      );
    });
    const searchQuery = ref(
      props.search || ((_a = props.filters) == null ? void 0 : _a.search) || ""
    );
    const showConfirmDeleteModal = ref(false);
    const orderToDelete = ref(null);
    const normalize = (value) => {
      return (value ?? "").toString().trim().toLowerCase();
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(
        value || 0
      ).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const getBuyerName = (order) => {
      var _a2;
      return (order == null ? void 0 : order.buyer_name) || ((_a2 = order == null ? void 0 : order.user) == null ? void 0 : _a2.name) || "";
    };
    const getBuyerEmail = (order) => {
      var _a2;
      return (order == null ? void 0 : order.buyer_email) || ((_a2 = order == null ? void 0 : order.user) == null ? void 0 : _a2.email) || "";
    };
    const getCourseTitle = (order) => {
      var _a2, _b;
      return ((_b = (_a2 = order == null ? void 0 : order.course) == null ? void 0 : _a2.translation) == null ? void 0 : _b.title) || "";
    };
    const getScheduleTitle = (order) => {
      var _a2, _b;
      return ((_b = (_a2 = order == null ? void 0 : order.schedule) == null ? void 0 : _a2.translation) == null ? void 0 : _b.title) || "";
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringAsc = (field) => (a, b) => normalize(
      a == null ? void 0 : a[field]
    ).localeCompare(
      normalize(
        b == null ? void 0 : b[field]
      ),
      props.currentLocale
    ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byStringDesc = (field) => (a, b) => normalize(
      b == null ? void 0 : b[field]
    ).localeCompare(
      normalize(
        a == null ? void 0 : a[field]
      ),
      props.currentLocale
    ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortOrders = (items) => {
      const list = (items || []).slice();
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        numberAsc: byStringAsc("number"),
        numberDesc: byStringDesc("number"),
        createdAsc: byDateAsc("created_at"),
        createdDesc: byDateDesc("created_at"),
        date_asc: byDateAsc("created_at"),
        date_desc: byDateDesc("created_at"),
        totalAsc: byNumberAsc("total"),
        totalDesc: byNumberDesc("total"),
        total_asc: byNumberAsc("total"),
        total_desc: byNumberDesc("total"),
        paidAtAsc: byDateAsc("paid_at"),
        paidAtDesc: byDateDesc("paid_at"),
        paid_asc: byDateAsc("paid_at"),
        paid_desc: byDateDesc("paid_at"),
        buyerAsc: (a, b) => normalize(
          getBuyerName(a)
        ).localeCompare(
          normalize(
            getBuyerName(b)
          ),
          props.currentLocale
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        buyerDesc: (a, b) => normalize(
          getBuyerName(b)
        ).localeCompare(
          normalize(
            getBuyerName(a)
          ),
          props.currentLocale
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        statusAsc: byStringAsc("status"),
        statusDesc: byStringDesc("status"),
        paymentStatusAsc: byStringAsc(
          "payment_status"
        ),
        paymentStatusDesc: byStringDesc(
          "payment_status"
        ),
        paidFirst: (a, b) => Number(
          Boolean(
            b == null ? void 0 : b.is_paid
          )
        ) - Number(
          Boolean(
            a == null ? void 0 : a.is_paid
          )
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        paidLast: (a, b) => Number(
          Boolean(
            a == null ? void 0 : a.is_paid
          )
        ) - Number(
          Boolean(
            b == null ? void 0 : b.is_paid
          )
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id)
      };
      return sortMap[sortParam.value] ? list.sort(
        sortMap[sortParam.value]
      ) : list;
    };
    const filteredOrders = computed(() => {
      let filtered = localOrders.value || [];
      const query = normalize(
        searchQuery.value
      );
      if (!query) {
        return sortOrders(
          filtered
        );
      }
      filtered = filtered.filter(
        (order) => {
          const values = [
            order == null ? void 0 : order.id,
            order == null ? void 0 : order.number,
            order == null ? void 0 : order.status,
            order == null ? void 0 : order.payment_status,
            getBuyerName(order),
            getBuyerEmail(order),
            order == null ? void 0 : order.buyer_phone,
            getCourseTitle(order),
            getScheduleTitle(order),
            order == null ? void 0 : order.payment_method,
            order == null ? void 0 : order.payment_provider,
            order == null ? void 0 : order.payment_reference,
            order == null ? void 0 : order.external_id,
            order == null ? void 0 : order.client_ip,
            order == null ? void 0 : order.total,
            order == null ? void 0 : order.currency
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
      return sortOrders(
        filtered
      );
    });
    const paginatedOrders = computed(() => {
      const per = Number(
        itemsPerPage.value || 10
      );
      const start = (currentPage.value - 1) * per;
      return filteredOrders.value.slice(
        start,
        start + per
      );
    });
    const displayedOrders = computed(() => {
      return props.useServerProcessing ? ordersList.value : paginatedOrders.value;
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
    const confirmDelete = (order) => {
      orderToDelete.value = order;
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      orderToDelete.value = null;
    };
    const deleteOrder = () => {
      var _a2;
      if (!((_a2 = orderToDelete.value) == null ? void 0 : _a2.id)) {
        return;
      }
      const idToDelete = orderToDelete.value.id;
      const numberToDelete = orderToDelete.value.number || `ID: ${idToDelete}`;
      router.delete(
        route(
          "admin.schoolOrders.destroy",
          {
            schoolOrder: idToDelete
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Заказ "${numberToDelete}" удалён.`
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(
              errors || {}
            )[0];
            const errorMessage = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[firstKey]) || "Ошибка при удалении заказа.";
            toast.error(
              `${errorMessage} Заказ: ${numberToDelete}`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    const cloneOrder = (order) => {
      if (!(order == null ? void 0 : order.id)) {
        return;
      }
      router.post(
        route(
          "admin.actions.schoolOrders.clone",
          {
            schoolOrder: order.id
          }
        ),
        {},
        {
          preserveScroll: true,
          onSuccess: () => {
            toast.success(
              "Заказ успешно клонирован."
            );
          },
          onError: () => {
            toast.error(
              "Ошибка при клонировании заказа."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("orders")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("orders"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("orders")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("orders")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-end sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              "setting-key": "adminSchoolOrdersProcessingMode",
              mode: __props.adminSchoolOrdersProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.ordersCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.ordersCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("search")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.ordersCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$9, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.ordersCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$a, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$b, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolOrders"
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
            if (__props.ordersCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$c, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.ordersCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.ordersCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$d, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.ordersCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mb-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredOrders.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$f, { pagination: __props.orders }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                orders: displayedOrders.value,
                onClone: cloneOrder,
                onDelete: confirmDelete
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                orders: displayedOrders.value,
                onClone: cloneOrder,
                onDelete: confirmDelete
              }, null, _parent2, _scopeId));
            }
            if (__props.ordersCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredOrders.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$f, { pagination: __props.orders }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$g, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteOrder,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-end sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$7, {
                      "setting-key": "adminSchoolOrdersProcessingMode",
                      mode: __props.adminSchoolOrdersProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.ordersCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.ordersCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$8, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("search")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.ordersCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$9, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.ordersCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$b, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolOrders"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (value) => sortParam.value = value
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.ordersCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    createVNode(_sfc_main$c, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.ordersCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$d, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.ordersCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mb-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredOrders.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$f, {
                      key: 1,
                      pagination: __props.orders
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    orders: displayedOrders.value,
                    onClone: cloneOrder,
                    onDelete: confirmDelete
                  }, null, 8, ["orders"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    orders: displayedOrders.value,
                    onClone: cloneOrder,
                    onDelete: confirmDelete
                  }, null, 8, ["orders"])),
                  __props.ordersCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredOrders.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$f, {
                      key: 1,
                      pagination: __props.orders
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$g, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteOrder,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolOrders/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
