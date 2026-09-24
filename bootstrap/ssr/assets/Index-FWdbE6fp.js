import { mergeProps, unref, useSSRContext, ref, watch, computed, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$a } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$b, a as _sfc_main$h } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$8 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$e } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$c, a as _sfc_main$f, b as _sfc_main$g } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$d } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$i } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$9 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$5 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$6 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$7 } from "./DeleteIconButton-DLv2Mr1x.js";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolBundlePrice/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} ↑</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} ↓</option><option disabled>───────────────────</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>───────────────────</option><option value="bundleTitleAsc">${ssrInterpolate(unref(t)("bundles"))} ↑</option><option value="bundleTitleDesc">${ssrInterpolate(unref(t)("bundles"))} ↓</option><option value="currencyCodeAsc">${ssrInterpolate(unref(t)("currency"))} ↑</option><option value="currencyCodeDesc">${ssrInterpolate(unref(t)("currency"))} ↓</option><option disabled>───────────────────</option><option value="effectivePriceAsc">${ssrInterpolate(unref(t)("sortByEffectivePrice"))} ↑</option><option value="effectivePriceDesc">${ssrInterpolate(unref(t)("sortByEffectivePrice"))} ↓</option><option value="priceAsc">${ssrInterpolate(unref(t)("sortByPrice"))} ↑</option><option value="priceDesc">${ssrInterpolate(unref(t)("sortByPrice"))} ↓</option><option value="salePriceAsc">${ssrInterpolate(unref(t)("sortBySalePrice"))} ↑</option><option value="salePriceDesc">${ssrInterpolate(unref(t)("sortBySalePrice"))} ↓</option><option value="compareAtPriceAsc">${ssrInterpolate(unref(t)("sortByCompareAtPrice"))} ↑</option><option value="compareAtPriceDesc">${ssrInterpolate(unref(t)("sortByCompareAtPrice"))} ↓</option><option value="discountPercentAsc">${ssrInterpolate(unref(t)("sortByDiscountPercent"))} ↑</option><option value="discountPercentDesc">${ssrInterpolate(unref(t)("sortByDiscountPercent"))} ↓</option><option disabled>───────────────────</option><option value="startsAtAsc">${ssrInterpolate(unref(t)("priceStartsAt"))} ↑</option><option value="startsAtDesc">${ssrInterpolate(unref(t)("priceStartsAt"))} ↓</option><option value="endsAtAsc">${ssrInterpolate(unref(t)("priceEndsAt"))} ↑</option><option value="endsAtDesc">${ssrInterpolate(unref(t)("priceEndsAt"))} ↓</option><option disabled>───────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolBundlePrice/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "BundlePriceTable",
  __ssrInlineRender: true,
  props: {
    prices: {
      type: Array,
      default: () => []
    },
    selectedPrices: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localPrices = ref([]);
    watch(
      () => props.prices,
      (newValue) => {
        localPrices.value = JSON.parse(
          JSON.stringify(newValue || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const allSelected = computed(() => {
      return localPrices.value.length > 0 && localPrices.value.every(
        (price) => props.selectedPrices.includes(
          price.id
        )
      );
    });
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localPrices.value.map(
          (price) => price.id
        )
      );
    };
    const getBundleTitle = (price) => {
      var _a, _b;
      return ((_b = (_a = price == null ? void 0 : price.bundle) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || `ID: ${(price == null ? void 0 : price.school_bundle_id) || "—"}`;
    };
    const getBundleSubtitle = (price) => {
      var _a, _b;
      return ((_b = (_a = price == null ? void 0 : price.bundle) == null ? void 0 : _a.translation) == null ? void 0 : _b.subtitle) || "";
    };
    const getBundleSlug = (price) => {
      var _a;
      return ((_a = price == null ? void 0 : price.bundle) == null ? void 0 : _a.slug) || "";
    };
    const getCurrencyCode = (price) => {
      var _a;
      return ((_a = price == null ? void 0 : price.currency) == null ? void 0 : _a.code) || "—";
    };
    const getCurrencyName = (price) => {
      var _a;
      return ((_a = price == null ? void 0 : price.currency) == null ? void 0 : _a.name) || "";
    };
    const getCurrencySymbol = (price) => {
      var _a, _b;
      return ((_a = price == null ? void 0 : price.currency) == null ? void 0 : _a.symbol) || ((_b = price == null ? void 0 : price.currency) == null ? void 0 : _b.code) || "";
    };
    const money = (value) => {
      if (value === null || value === void 0 || value === "") {
        return "—";
      }
      const number = Number(value);
      if (!Number.isFinite(number)) {
        return "—";
      }
      return number.toLocaleString(
        "ru-RU",
        {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        }
      );
    };
    const dateShort = (iso) => {
      if (!iso) {
        return "—";
      }
      const date = new Date(iso);
      if (Number.isNaN(
        date.getTime()
      )) {
        return "—";
      }
      return date.toLocaleDateString(
        "ru-RU",
        {
          year: "numeric",
          month: "short",
          day: "numeric"
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedPrices.length)}</div>`);
      if (localPrices.value.length) {
        _push(`<label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="rounded border-slate-400"${ssrIncludeBooleanAttr(allSelected.value) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localPrices.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><svg class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-2 py-3 w-px text-center font-medium">${ssrInterpolate(unref(t)("id"))}</th><th class="px-2 py-3 whitespace-nowrap text-left font-semibold">${ssrInterpolate(unref(t)("bundle"))}</th><th class="px-2 py-3 whitespace-nowrap text-center"${ssrRenderAttr("title", unref(t)("currency"))}>${ssrInterpolate(unref(t)("currency"))}</th><th class="px-2 py-3 whitespace-nowrap text-center font-semibold">${ssrInterpolate(unref(t)("price"))}</th><th class="px-2 py-3 whitespace-nowrap text-center font-semibold">${ssrInterpolate(unref(t)("salePrice"))}</th><th class="px-2 py-3 whitespace-nowrap text-center"${ssrRenderAttr("title", unref(t)("periodValidityPrice"))}>${ssrInterpolate(unref(t)("period"))}</th><th class="px-2 py-3 whitespace-nowrap text-end font-semibold">${ssrInterpolate(unref(t)("actions"))}</th><th class="px-2 py-3 whitespace-nowrap text-center"><input type="checkbox" class="rounded border-slate-400"${ssrIncludeBooleanAttr(allSelected.value) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localPrices.value,
          "onUpdate:modelValue": ($event) => localPrices.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".drag-handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: price }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center"${_scopeId}><button type="button" class="drag-handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button></td><td class="px-2 py-3 whitespace-nowrap text-center"${_scopeId}><div class="text-xs text-slate-800 dark:text-blue-200"${ssrRenderAttr("title", `${unref(t)("sort")}: ${price.sort ?? 0}`)}${_scopeId}>${ssrInterpolate(price.id)}</div></td><td class="px-2 py-3"${_scopeId}><div class="flex flex-col"${_scopeId}><span class="text-xs text-sky-700 dark:text-sky-200"${ssrRenderAttr("title", getBundleTitle(price))}${_scopeId}>${ssrInterpolate(getBundleTitle(price))}</span>`);
              if (getBundleSubtitle(price)) {
                _push2(`<span class="text-[10px] text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(getBundleSubtitle(price))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<span class="text-[10px] text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(getBundleSlug(price) || "—")}</span></div></td><td class="px-2 py-3 whitespace-nowrap text-center"${_scopeId}><div class="font-semibold text-teal-700 dark:text-teal-300"${ssrRenderAttr("title", getCurrencyName(price))}${_scopeId}>${ssrInterpolate(getCurrencyCode(price))}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-center"${_scopeId}><div class="flex items-baseline justify-center gap-1"${_scopeId}><span class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(money(price.price))}</span>`);
              if (money(price.price) !== "—") {
                _push2(`<span class="text-[12px] opacity-70 text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(getCurrencySymbol(price))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (Number(price.compare_at_price) > 0) {
                _push2(`<div class="flex items-baseline justify-center gap-1"${_scopeId}><span class="text-xs line-through opacity-75 text-gray-500 dark:text-gray-400"${_scopeId}>${ssrInterpolate(money(price.compare_at_price))}</span><span class="text-[10px] opacity-70 text-gray-500 dark:text-gray-400"${_scopeId}>${ssrInterpolate(getCurrencySymbol(price))}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><div class="text-orange-700 dark:text-orange-300"${_scopeId}>${ssrInterpolate(money(price.effective_price))} `);
              if (money(price.effective_price) !== "—") {
                _push2(`<span class="text-[12px] opacity-70 text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(getCurrencySymbol(price))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (price.has_discount && price.discount_percent) {
                _push2(`<div class="text-[11px] text-rose-600 dark:text-rose-300"${_scopeId}> -${ssrInterpolate(price.discount_percent)}% </div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-xs text-center"${_scopeId}><div class="text-blue-700 dark:text-blue-300"${_scopeId}>${ssrInterpolate(dateShort(price.starts_at))}</div><div class="text-slate-400"${_scopeId}> — </div><div class="text-blue-700 dark:text-blue-300"${_scopeId}>${ssrInterpolate(dateShort(price.ends_at))}</div></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex items-center justify-end space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                "is-active": price.activity,
                title: price.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", price)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolBundlePrices.edit", {
                  schoolBundlePrice: price.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => emit("delete", price)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap text-center"${_scopeId}><input type="checkbox" class="rounded border-slate-400"${ssrIncludeBooleanAttr(__props.selectedPrices.includes(price.id)) ? " checked" : ""}${_scopeId}></td></tr>`);
            } else {
              return [
                createVNode("tr", { class: "text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800" }, [
                  createVNode("td", { class: "px-2 py-1 text-center" }, [
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
                    ], 8, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap text-center" }, [
                    createVNode("div", {
                      class: "text-xs text-slate-800 dark:text-blue-200",
                      title: `${unref(t)("sort")}: ${price.sort ?? 0}`
                    }, toDisplayString(price.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("div", { class: "flex flex-col" }, [
                      createVNode("span", {
                        class: "text-xs text-sky-700 dark:text-sky-200",
                        title: getBundleTitle(price)
                      }, toDisplayString(getBundleTitle(price)), 9, ["title"]),
                      getBundleSubtitle(price) ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "text-[10px] text-slate-600 dark:text-slate-300"
                      }, toDisplayString(getBundleSubtitle(price)), 1)) : createCommentVNode("", true),
                      createVNode("span", { class: "text-[10px] text-slate-500 dark:text-slate-400" }, toDisplayString(getBundleSlug(price) || "—"), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap text-center" }, [
                    createVNode("div", {
                      class: "font-semibold text-teal-700 dark:text-teal-300",
                      title: getCurrencyName(price)
                    }, toDisplayString(getCurrencyCode(price)), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-col items-center" }, [
                      createVNode("div", { class: "flex items-baseline justify-center gap-1" }, [
                        createVNode("span", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(money(price.price)), 1),
                        money(price.price) !== "—" ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "text-[12px] opacity-70 text-gray-700 dark:text-gray-300"
                        }, toDisplayString(getCurrencySymbol(price)), 1)) : createCommentVNode("", true)
                      ]),
                      Number(price.compare_at_price) > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "flex items-baseline justify-center gap-1"
                      }, [
                        createVNode("span", { class: "text-xs line-through opacity-75 text-gray-500 dark:text-gray-400" }, toDisplayString(money(price.compare_at_price)), 1),
                        createVNode("span", { class: "text-[10px] opacity-70 text-gray-500 dark:text-gray-400" }, toDisplayString(getCurrencySymbol(price)), 1)
                      ])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("div", { class: "text-orange-700 dark:text-orange-300" }, [
                        createTextVNode(toDisplayString(money(price.effective_price)) + " ", 1),
                        money(price.effective_price) !== "—" ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "text-[12px] opacity-70 text-gray-700 dark:text-gray-300"
                        }, toDisplayString(getCurrencySymbol(price)), 1)) : createCommentVNode("", true)
                      ]),
                      price.has_discount && price.discount_percent ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[11px] text-rose-600 dark:text-rose-300"
                      }, " -" + toDisplayString(price.discount_percent) + "% ", 1)) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-xs text-center" }, [
                      createVNode("div", { class: "text-blue-700 dark:text-blue-300" }, toDisplayString(dateShort(price.starts_at)), 1),
                      createVNode("div", { class: "text-slate-400" }, " — "),
                      createVNode("div", { class: "text-blue-700 dark:text-blue-300" }, toDisplayString(dateShort(price.ends_at)), 1)
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex items-center justify-end space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        "is-active": price.activity,
                        title: price.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", price)
                      }, null, 8, ["is-active", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolBundlePrices.edit", {
                          schoolBundlePrice: price.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        onDelete: ($event) => emit("delete", price)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap text-center" }, [
                    createVNode("input", {
                      type: "checkbox",
                      class: "rounded border-slate-400",
                      checked: __props.selectedPrices.includes(price.id),
                      onChange: ($event) => emit("toggle-select", price.id)
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
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolBundlePrice/Table/BundlePriceTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "BundlePriceCardGrid",
  __ssrInlineRender: true,
  props: {
    prices: {
      type: Array,
      default: () => []
    },
    selectedPrices: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localPrices = ref([]);
    watch(
      () => props.prices,
      (newValue) => {
        localPrices.value = JSON.parse(
          JSON.stringify(newValue || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const allSelected = computed(() => {
      return localPrices.value.length > 0 && localPrices.value.every(
        (price) => props.selectedPrices.includes(
          price.id
        )
      );
    });
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localPrices.value.map(
          (price) => price.id
        )
      );
    };
    const getBundleTitle = (price) => {
      var _a, _b;
      return ((_b = (_a = price == null ? void 0 : price.bundle) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || `ID: ${(price == null ? void 0 : price.school_bundle_id) || "—"}`;
    };
    const getBundleSubtitle = (price) => {
      var _a, _b;
      return ((_b = (_a = price == null ? void 0 : price.bundle) == null ? void 0 : _a.translation) == null ? void 0 : _b.subtitle) || "";
    };
    const getBundleShort = (price) => {
      var _a, _b;
      return ((_b = (_a = price == null ? void 0 : price.bundle) == null ? void 0 : _a.translation) == null ? void 0 : _b.short) || "";
    };
    const getBundleSlug = (price) => {
      var _a;
      return ((_a = price == null ? void 0 : price.bundle) == null ? void 0 : _a.slug) || "";
    };
    const getCurrencyCode = (price) => {
      var _a;
      return ((_a = price == null ? void 0 : price.currency) == null ? void 0 : _a.code) || "—";
    };
    const getCurrencyName = (price) => {
      var _a;
      return ((_a = price == null ? void 0 : price.currency) == null ? void 0 : _a.name) || "";
    };
    const getCurrencySymbol = (price) => {
      var _a, _b;
      return ((_a = price == null ? void 0 : price.currency) == null ? void 0 : _a.symbol) || ((_b = price == null ? void 0 : price.currency) == null ? void 0 : _b.code) || "";
    };
    const money = (value) => {
      if (value === null || value === void 0 || value === "") {
        return "—";
      }
      const number = Number(value);
      if (!Number.isFinite(number)) {
        return "—";
      }
      return number.toLocaleString(
        "ru-RU",
        {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        }
      );
    };
    const dateShort = (iso) => {
      if (!iso) {
        return "—";
      }
      const date = new Date(iso);
      if (Number.isNaN(
        date.getTime()
      )) {
        return "—";
      }
      return date.toLocaleDateString(
        "ru-RU",
        {
          year: "numeric",
          month: "short",
          day: "numeric"
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedPrices.length)}</div>`);
      if (localPrices.value.length) {
        _push(`<label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="rounded border-slate-400"${ssrIncludeBooleanAttr(allSelected.value) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localPrices.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localPrices.value,
          "onUpdate:modelValue": ($event) => localPrices.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".drag-handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: price }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<article class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="drag-handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `${unref(t)("sort")}: ${price.sort ?? 0}`)}${_scopeId}> ID: ${ssrInterpolate(price.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}><span class="font-semibold text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300"${ssrRenderAttr("title", getCurrencyName(price) || unref(t)("currency"))}${_scopeId}>${ssrInterpolate(getCurrencyCode(price))}</span><input type="checkbox" class="rounded border-slate-400"${ssrIncludeBooleanAttr(__props.selectedPrices.includes(price.id)) ? " checked" : ""}${_scopeId}></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-2"${_scopeId}><div class="text-xs font-semibold text-center text-sky-700 dark:text-sky-200"${ssrRenderAttr("title", getBundleTitle(price))}${_scopeId}>${ssrInterpolate(getBundleTitle(price))} `);
              if (getBundleSubtitle(price)) {
                _push2(`<span class="block text-[10px] font-normal text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(getBundleSubtitle(price))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              if (getBundleSlug(price)) {
                _push2(`<span class="block text-[10px] font-normal text-gray-500 dark:text-gray-400"${_scopeId}>${ssrInterpolate(getBundleSlug(price))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (getBundleShort(price)) {
                _push2(`<div class="border border-dashed border-slate-300 dark:border-slate-600 px-2 py-1 text-[10px] text-center text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(getBundleShort(price))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="text-[12px] font-semibold text-teal-700 dark:text-teal-300 text-center"${_scopeId}>${ssrInterpolate(getCurrencyName(price))} <span class="text-slate-400"${_scopeId}> — </span> ${ssrInterpolate(getCurrencyCode(price))}</div><div class="space-y-1 text-[11px] font-semibold"${_scopeId}>`);
              if (Number(price.compare_at_price) > 0) {
                _push2(`<div class="text-center"${_scopeId}><span class="text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("compareAtPrice"))}: </span><span class="ml-1 line-through opacity-75 text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(money(price.compare_at_price))}</span><span class="ml-1 text-[10px] opacity-70 text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(getCurrencySymbol(price))}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="text-center"${_scopeId}><span class="text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("price"))}: </span><span class="ml-1 text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(money(price.price))}</span><span class="ml-1 text-[10px] opacity-70 text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(getCurrencySymbol(price))}</span></div><div class="text-center"${_scopeId}><span class="text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("salePrice"))}: </span><span class="ml-1 text-orange-700 dark:text-orange-300"${_scopeId}>${ssrInterpolate(money(price.effective_price))}</span><span class="ml-1 text-[10px] opacity-70 text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(getCurrencySymbol(price))}</span>`);
              if (price.has_discount && price.discount_percent) {
                _push2(`<span class="ml-2 text-rose-600 dark:text-rose-300"${_scopeId}> -${ssrInterpolate(price.discount_percent)}% </span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (price.discount_amount !== null && price.discount_amount !== void 0 && Number(price.discount_amount) > 0) {
                _push2(`<div class="text-center text-[10px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(unref(t)("discount"))}: ${ssrInterpolate(money(price.discount_amount))} ${ssrInterpolate(getCurrencySymbol(price))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="text-[9px] text-center font-semibold"${_scopeId}><div${_scopeId}><span class="text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("priceStartsAt"))}: </span><span class="text-sky-700 dark:text-sky-300"${_scopeId}>${ssrInterpolate(dateShort(price.starts_at))}</span></div><div${_scopeId}><span class="text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("priceEndsAt"))}: </span><span class="text-sky-700 dark:text-sky-300"${_scopeId}>${ssrInterpolate(dateShort(price.ends_at))}</span></div></div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                "is-active": price.activity,
                title: price.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", price)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolBundlePrices.edit", {
                  schoolBundlePrice: price.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => emit("delete", price)
              }, null, _parent2, _scopeId));
              _push2(`</div></footer></article>`);
            } else {
              return [
                createVNode("article", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
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
                        title: `${unref(t)("sort")}: ${price.sort ?? 0}`
                      }, " ID: " + toDisplayString(price.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("span", {
                        class: "font-semibold text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 bg-teal-100 dark:bg-teal-900/50 text-teal-700 dark:text-teal-300",
                        title: getCurrencyName(price) || unref(t)("currency")
                      }, toDisplayString(getCurrencyCode(price)), 9, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        class: "rounded border-slate-400",
                        checked: __props.selectedPrices.includes(price.id),
                        onChange: ($event) => emit("toggle-select", price.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-2" }, [
                    createVNode("div", {
                      class: "text-xs font-semibold text-center text-sky-700 dark:text-sky-200",
                      title: getBundleTitle(price)
                    }, [
                      createTextVNode(toDisplayString(getBundleTitle(price)) + " ", 1),
                      getBundleSubtitle(price) ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "block text-[10px] font-normal text-slate-600 dark:text-slate-300"
                      }, toDisplayString(getBundleSubtitle(price)), 1)) : createCommentVNode("", true),
                      getBundleSlug(price) ? (openBlock(), createBlock("span", {
                        key: 1,
                        class: "block text-[10px] font-normal text-gray-500 dark:text-gray-400"
                      }, toDisplayString(getBundleSlug(price)), 1)) : createCommentVNode("", true)
                    ], 8, ["title"]),
                    getBundleShort(price) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "border border-dashed border-slate-300 dark:border-slate-600 px-2 py-1 text-[10px] text-center text-slate-600 dark:text-slate-300"
                    }, toDisplayString(getBundleShort(price)), 1)) : createCommentVNode("", true),
                    createVNode("div", { class: "text-[12px] font-semibold text-teal-700 dark:text-teal-300 text-center" }, [
                      createTextVNode(toDisplayString(getCurrencyName(price)) + " ", 1),
                      createVNode("span", { class: "text-slate-400" }, " — "),
                      createTextVNode(" " + toDisplayString(getCurrencyCode(price)), 1)
                    ]),
                    createVNode("div", { class: "space-y-1 text-[11px] font-semibold" }, [
                      Number(price.compare_at_price) > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-center"
                      }, [
                        createVNode("span", { class: "text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("compareAtPrice")) + ": ", 1),
                        createVNode("span", { class: "ml-1 line-through opacity-75 text-slate-700 dark:text-slate-300" }, toDisplayString(money(price.compare_at_price)), 1),
                        createVNode("span", { class: "ml-1 text-[10px] opacity-70 text-gray-700 dark:text-gray-300" }, toDisplayString(getCurrencySymbol(price)), 1)
                      ])) : createCommentVNode("", true),
                      createVNode("div", { class: "text-center" }, [
                        createVNode("span", { class: "text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("price")) + ": ", 1),
                        createVNode("span", { class: "ml-1 text-slate-700 dark:text-slate-300" }, toDisplayString(money(price.price)), 1),
                        createVNode("span", { class: "ml-1 text-[10px] opacity-70 text-gray-700 dark:text-gray-300" }, toDisplayString(getCurrencySymbol(price)), 1)
                      ]),
                      createVNode("div", { class: "text-center" }, [
                        createVNode("span", { class: "text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("salePrice")) + ": ", 1),
                        createVNode("span", { class: "ml-1 text-orange-700 dark:text-orange-300" }, toDisplayString(money(price.effective_price)), 1),
                        createVNode("span", { class: "ml-1 text-[10px] opacity-70 text-gray-700 dark:text-gray-300" }, toDisplayString(getCurrencySymbol(price)), 1),
                        price.has_discount && price.discount_percent ? (openBlock(), createBlock("span", {
                          key: 0,
                          class: "ml-2 text-rose-600 dark:text-rose-300"
                        }, " -" + toDisplayString(price.discount_percent) + "% ", 1)) : createCommentVNode("", true)
                      ]),
                      price.discount_amount !== null && price.discount_amount !== void 0 && Number(price.discount_amount) > 0 ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "text-center text-[10px] text-fuchsia-700 dark:text-fuchsia-300"
                      }, toDisplayString(unref(t)("discount")) + ": " + toDisplayString(money(price.discount_amount)) + " " + toDisplayString(getCurrencySymbol(price)), 1)) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "text-[9px] text-center font-semibold" }, [
                      createVNode("div", null, [
                        createVNode("span", { class: "text-slate-700 dark:text-slate-200" }, toDisplayString(unref(t)("priceStartsAt")) + ": ", 1),
                        createVNode("span", { class: "text-sky-700 dark:text-sky-300" }, toDisplayString(dateShort(price.starts_at)), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "text-slate-700 dark:text-slate-200" }, toDisplayString(unref(t)("priceEndsAt")) + ": ", 1),
                        createVNode("span", { class: "text-sky-700 dark:text-sky-300" }, toDisplayString(dateShort(price.ends_at)), 1)
                      ])
                    ])
                  ]),
                  createVNode("footer", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        "is-active": price.activity,
                        title: price.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", price)
                      }, null, 8, ["is-active", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolBundlePrices.edit", {
                          schoolBundlePrice: price.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        onDelete: ($event) => emit("delete", price)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolBundlePrice/View/BundlePriceCardGrid.vue");
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
    adminSchoolBundlePricesProcessingMode: {
      type: String,
      default: "frontend"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    prices: {
      type: [Array, Object],
      default: () => []
    },
    pricesCount: {
      type: Number,
      default: 0
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    adminSchoolBundlePricesPerPage: {
      type: Number,
      default: 10
    },
    adminSchoolBundlePricesDefaultSort: {
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
    bundles: {
      type: Array,
      default: () => []
    },
    currencies: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    var _a;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_bundle_prices") || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_bundle_prices",
        value
      );
    });
    const pricesList = computed(() => {
      var _a2;
      if (Array.isArray(props.prices)) {
        return props.prices;
      }
      if (Array.isArray((_a2 = props.prices) == null ? void 0 : _a2.data)) {
        return props.prices.data;
      }
      return [];
    });
    const localPrices = ref([]);
    watch(
      pricesList,
      (newValue) => {
        localPrices.value = JSON.parse(
          JSON.stringify(newValue || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const itemsPerPage = ref(
      props.adminSchoolBundlePricesPerPage || 10
    );
    watch(itemsPerPage, (newValue) => {
      router.put(
        route(
          "admin.settings.updateAdminCountSchoolBundlePrices"
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
      props.sortParam || props.adminSchoolBundlePricesDefaultSort || "idDesc"
    );
    const currentPage = ref(1);
    watch(sortParam, (newValue) => {
      currentPage.value = 1;
      router.put(
        route(
          "admin.settings.updateAdminSortSchoolBundlePrices"
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
    const stripHtml = (html = "") => {
      return String(html || "").replace(/<\/p>/gi, " ").replace(/<br\s*\/?>/gi, " ").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/\s+/g, " ").trim();
    };
    const normalize = (value) => {
      return stripHtml(
        value ?? ""
      ).toString().trim().toLowerCase();
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      if (!value) {
        return 0;
      }
      const time = new Date(value).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const getBundleTitle = (price) => {
      var _a2, _b;
      return ((_b = (_a2 = price == null ? void 0 : price.bundle) == null ? void 0 : _a2.translation) == null ? void 0 : _b.title) || `ID: ${(price == null ? void 0 : price.school_bundle_id) || ""}`;
    };
    const getBundleSubtitle = (price) => {
      var _a2, _b;
      return ((_b = (_a2 = price == null ? void 0 : price.bundle) == null ? void 0 : _a2.translation) == null ? void 0 : _b.subtitle) || "";
    };
    const getBundleShort = (price) => {
      var _a2, _b;
      return ((_b = (_a2 = price == null ? void 0 : price.bundle) == null ? void 0 : _a2.translation) == null ? void 0 : _b.short) || "";
    };
    const getBundleDescription = (price) => {
      var _a2, _b;
      return ((_b = (_a2 = price == null ? void 0 : price.bundle) == null ? void 0 : _a2.translation) == null ? void 0 : _b.description) || "";
    };
    const getCurrencyCode = (price) => {
      var _a2;
      return ((_a2 = price == null ? void 0 : price.currency) == null ? void 0 : _a2.code) || "";
    };
    const getCurrencyName = (price) => {
      var _a2;
      return ((_a2 = price == null ? void 0 : price.currency) == null ? void 0 : _a2.name) || "";
    };
    const getCurrencySymbol = (price) => {
      var _a2;
      return ((_a2 = price == null ? void 0 : price.currency) == null ? void 0 : _a2.symbol) || "";
    };
    const getDeleteTitle = (price) => {
      const bundle = getBundleTitle(price);
      const currency = getCurrencyCode(price);
      const effectivePrice = (price == null ? void 0 : price.effective_price) !== null && (price == null ? void 0 : price.effective_price) !== void 0 ? String(
        price.effective_price
      ) : "";
      return [
        bundle,
        currency,
        effectivePrice
      ].filter(Boolean).join(" • ");
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortPrices = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") {
        return list.filter(
          (price) => Boolean(
            price.activity
          )
        );
      }
      if (sortParam.value === "inactive") {
        return list.filter(
          (price) => !price.activity
        );
      }
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        activityAsc: byNumberAsc(
          "activity"
        ),
        activityDesc: byNumberDesc(
          "activity"
        ),
        bundleTitleAsc: (a, b) => normalize(
          getBundleTitle(a)
        ).localeCompare(
          normalize(
            getBundleTitle(b)
          ),
          props.currentLocale
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        bundleTitleDesc: (a, b) => normalize(
          getBundleTitle(b)
        ).localeCompare(
          normalize(
            getBundleTitle(a)
          ),
          props.currentLocale
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        currencyCodeAsc: (a, b) => normalize(
          getCurrencyCode(a)
        ).localeCompare(
          normalize(
            getCurrencyCode(b)
          ),
          props.currentLocale
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        currencyCodeDesc: (a, b) => normalize(
          getCurrencyCode(b)
        ).localeCompare(
          normalize(
            getCurrencyCode(a)
          ),
          props.currentLocale
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        effectivePriceAsc: byNumberAsc(
          "effective_price"
        ),
        effectivePriceDesc: byNumberDesc(
          "effective_price"
        ),
        priceAsc: byNumberAsc(
          "price"
        ),
        priceDesc: byNumberDesc(
          "price"
        ),
        salePriceAsc: byNumberAsc(
          "sale_price"
        ),
        salePriceDesc: byNumberDesc(
          "sale_price"
        ),
        compareAtPriceAsc: byNumberAsc(
          "compare_at_price"
        ),
        compareAtPriceDesc: byNumberDesc(
          "compare_at_price"
        ),
        discountPercentAsc: byNumberAsc(
          "discount_percent"
        ),
        discountPercentDesc: byNumberDesc(
          "discount_percent"
        ),
        startsAtAsc: byDateAsc(
          "starts_at"
        ),
        startsAtDesc: byDateDesc(
          "starts_at"
        ),
        endsAtAsc: byDateAsc(
          "ends_at"
        ),
        endsAtDesc: byDateDesc(
          "ends_at"
        ),
        createdAtAsc: byDateAsc(
          "created_at"
        ),
        createdAtDesc: byDateDesc(
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
    const filteredPrices = computed(() => {
      let filtered = localPrices.value || [];
      const query = normalize(
        searchQuery.value
      );
      if (!query) {
        return sortPrices(
          filtered
        );
      }
      filtered = filtered.filter(
        (price) => {
          const values = [
            /**
             * Price.
             */
            price == null ? void 0 : price.price,
            price == null ? void 0 : price.sale_price,
            price == null ? void 0 : price.compare_at_price,
            /**
             * Bundle current locale.
             */
            getBundleTitle(price),
            getBundleSubtitle(price),
            getBundleShort(price),
            getBundleDescription(price),
            /**
             * Currency.
             */
            getCurrencyCode(price),
            getCurrencyName(price),
            getCurrencySymbol(price)
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
      return sortPrices(
        filtered
      );
    });
    const paginatedPrices = computed(() => {
      const per = Number(
        itemsPerPage.value || 10
      );
      const start = (currentPage.value - 1) * per;
      return filteredPrices.value.slice(
        start,
        start + per
      );
    });
    const displayedPrices = computed(() => {
      return props.useServerProcessing ? pricesList.value : paginatedPrices.value;
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
    const priceToDelete = ref(null);
    const confirmDelete = (priceOrId) => {
      if (typeof priceOrId === "object") {
        priceToDelete.value = priceOrId;
      } else {
        priceToDelete.value = displayedPrices.value.find(
          (price) => Number(price.id) === Number(priceOrId)
        ) || {
          id: priceOrId
        };
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      priceToDelete.value = null;
    };
    const deleteBundlePrice = () => {
      var _a2;
      if (!((_a2 = priceToDelete.value) == null ? void 0 : _a2.id)) {
        return;
      }
      const idToDelete = priceToDelete.value.id;
      const titleToDelete = getDeleteTitle(
        priceToDelete.value
      );
      router.delete(
        route(
          "admin.schoolBundlePrices.destroy",
          {
            schoolBundlePrice: idToDelete
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Цена набора "${titleToDelete || `ID: ${idToDelete}`}" удалена.`
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(
              errors || {}
            )[0];
            const errorMsg = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[firstKey]) || "Ошибка при удалении цены набора.";
            toast.error(
              `${errorMsg} ID: ${idToDelete}`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    const patchPrice = (priceId, payload) => {
      const index = localPrices.value.findIndex(
        (price) => Number(price.id) === Number(priceId)
      );
      if (index === -1) {
        return;
      }
      localPrices.value[index] = {
        ...localPrices.value[index],
        ...payload
      };
    };
    const selectedPrices = ref([]);
    const toggleAll = (payload) => {
      var _a2;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a2 = payload == null ? void 0 : payload.target) == null ? void 0 : _a2.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedPrices.value.map(
        (price) => price.id
      );
      if (checked) {
        selectedPrices.value = [
          .../* @__PURE__ */ new Set([
            ...selectedPrices.value,
            ...ids
          ])
        ];
        return;
      }
      selectedPrices.value = selectedPrices.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectPrice = (id) => {
      const index = selectedPrices.value.indexOf(
        id
      );
      if (index > -1) {
        selectedPrices.value.splice(
          index,
          1
        );
        return;
      }
      selectedPrices.value.push(
        id
      );
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedPrices.value.length) {
        toast.warning(
          "Выберите цены наборов для активации/деактивации"
        );
        return;
      }
      const idsToUpdate = [
        ...selectedPrices.value
      ];
      router.put(
        route(
          "admin.actions.schoolBundlePrices.bulkUpdateActivity"
        ),
        {
          ids: idsToUpdate,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            idsToUpdate.forEach(
              (id) => patchPrice(
                id,
                {
                  activity: newActivity
                }
              )
            );
            selectedPrices.value = [];
            toast.success(
              "Активность цен наборов массово обновлена"
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Не удалось массово обновить активность"
            );
          }
        }
      );
    };
    const bulkDestroy = () => {
      if (!selectedPrices.value.length) {
        toast.warning(
          "Выберите цены наборов для удаления"
        );
        return;
      }
      router.delete(
        route(
          "admin.actions.schoolBundlePrices.bulkDestroy"
        ),
        {
          data: {
            ids: selectedPrices.value
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedPrices.value = [];
            toast.success(
              "Выбранные цены наборов удалены"
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.general) || "Не удалось массово удалить цены наборов"
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
        bulkDestroy();
      }
      event.target.value = "";
    };
    const toggleActivity = (price) => {
      const newActivity = !price.activity;
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.schoolBundlePrices.updateActivity",
          {
            schoolBundlePrice: price.id
          }
        ),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchPrice(
              price.id,
              {
                activity: newActivity
              }
            );
            price.activity = newActivity;
            toast.success(
              `Цена набора "${getDeleteTitle(price) || `ID: ${price.id}`}" ${actionText}.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности ID: ${price.id}`
            );
          }
        }
      );
    };
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (currentPage.value - 1) * itemsPerPage.value;
      const items = orderedIds.map(
        (id, index) => ({
          id,
          sort: startSort + index + 1
        })
      );
      if (!items.length) {
        return;
      }
      router.put(
        route(
          "admin.actions.schoolBundlePrices.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Порядок цен наборов успешно обновлён."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка обновления сортировки цен наборов:",
              errors
            );
            toast.error(
              (errors == null ? void 0 : errors.items) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок цен наборов."
            );
            router.reload({
              only: [
                "prices"
              ],
              preserveScroll: true
            });
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("bundlePrices")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("bundlePrices"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("bundlePrices")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("bundlePrices")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              href: _ctx.route("admin.schoolBundlePrices.create")
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
                  _push3(` ${ssrInterpolate(unref(t)("addBundlePrice"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addBundlePrice")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              "setting-key": "adminSchoolBundlePricesProcessingMode",
              mode: __props.adminSchoolBundlePricesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.pricesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.pricesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("search")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.pricesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.pricesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolBundlePrices"
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
            if (__props.pricesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.pricesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.pricesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$f, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.pricesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mb-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredPrices.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.prices }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                prices: displayedPrices.value,
                "selected-prices": selectedPrices.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectPrice,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                prices: displayedPrices.value,
                "selected-prices": selectedPrices.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectPrice,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            }
            if (__props.pricesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredPrices.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.prices }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$i, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteBundlePrice,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$8, {
                      href: _ctx.route("admin.schoolBundlePrices.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addBundlePrice")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$9, {
                      "setting-key": "adminSchoolBundlePricesProcessingMode",
                      mode: __props.adminSchoolBundlePricesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.pricesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.pricesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("search")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.pricesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.pricesCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$d, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolBundlePrices"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (value) => sortParam.value = value
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.pricesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.pricesCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.pricesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mb-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredPrices.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.prices
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    prices: displayedPrices.value,
                    "selected-prices": selectedPrices.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectPrice,
                    onToggleAll: toggleAll
                  }, null, 8, ["prices", "selected-prices"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    prices: displayedPrices.value,
                    "selected-prices": selectedPrices.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectPrice,
                    onToggleAll: toggleAll
                  }, null, 8, ["prices", "selected-prices"])),
                  __props.pricesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredPrices.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.prices
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteBundlePrice,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolBundlePrices/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
