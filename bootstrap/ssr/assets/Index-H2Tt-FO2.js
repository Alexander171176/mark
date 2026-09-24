import { mergeProps, useSSRContext, computed, ref, watch, unref, withCtx, createBlock, openBlock, createVNode, toDisplayString, createCommentVNode, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent, ssrRenderClass } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { Link, router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$a } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$d, a as _sfc_main$f, b as _sfc_main$g } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$h } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$c } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$e } from "./CountTable-p8tyXGUL.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$7 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$8 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$9 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$b } from "./RefreshRatesButton-D_vPWECI.js";
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
const _sfc_main$6 = {
  __name: "SetDefaultButton",
  __ssrInlineRender: true,
  props: {
    disabled: { type: Boolean, default: false },
    title: { type: String, default: "" }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        title: __props.title,
        disabled: __props.disabled,
        class: "inline-flex items-center py-0.5 px-2 rounded border border-slate-300 hover:border-yellow-500 dark:border-yellow-300 text-gray-500 dark:text-gray-300 hover:text-rose-500 dark:hover:text-rose-400 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:border-slate-300 disabled:text-red-500 disabled:dark:text-red-300 disabled:border-slate-300"
      }, _attrs))}><span class="text-lg">★</span></button>`);
    };
  }
};
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Finance/Currency/Buttons/SetDefaultButton.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const _sfc_main$5 = {
  __name: "InlineRateEditor",
  __ssrInlineRender: true,
  props: {
    /**
     * ✅ Режим 1 (таблица/карточки): передаём currency целиком
     * ожидание полей: id, is_default, activity, rate_vs_default, rate_at, rate_provider
     */
    currency: { type: Object, default: null },
    /**
     * ✅ Режим 2 (карточки/любой список): можно работать без currency
     * тогда обязательно id
     */
    id: { type: [Number, String, null], default: null },
    /**
     * Текущее значение курса (для режима без currency или для явного переопределения)
     * Если передан currency, но modelValue тоже передан — приоритет у modelValue.
     */
    modelValue: { type: [String, Number, null], default: null },
    /**
     * Активность/дефолт (для режима без currency)
     * Если передан currency — берём оттуда.
     */
    isDefault: { type: Boolean, default: false },
    activity: { type: Boolean, default: true },
    placeholder: { type: String, default: "" },
    step: { type: String, default: "0.00000001" },
    precision: { type: Number, default: 8 },
    min: { type: Number, default: 1e-8 }
  },
  emits: ["save"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    computed(() => {
      var _a;
      return ((_a = props.currency) == null ? void 0 : _a.id) ?? props.id ?? null;
    });
    const isBase = computed(() => {
      var _a;
      return !!(((_a = props.currency) == null ? void 0 : _a.is_default) ?? props.isDefault);
    });
    const isActive = computed(() => {
      var _a;
      return !!(((_a = props.currency) == null ? void 0 : _a.activity) ?? props.activity);
    });
    const rateValue = computed(() => {
      var _a;
      const v = props.modelValue ?? ((_a = props.currency) == null ? void 0 : _a.rate_vs_default);
      return v === void 0 ? null : v;
    });
    const rateAt = computed(() => {
      var _a;
      return ((_a = props.currency) == null ? void 0 : _a.rate_at) ?? null;
    });
    const rateAtLabel = computed(() => {
      if (!rateAt.value) return "";
      const d = new Date(rateAt.value);
      if (isNaN(d)) return String(rateAt.value);
      return d.toLocaleString("ru-RU", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit"
      });
    });
    const provider = computed(() => {
      var _a;
      return ((_a = props.currency) == null ? void 0 : _a.rate_provider) ?? null;
    });
    const local = ref("");
    const fmt = (val) => {
      if (val === null || val === void 0 || val === "") return "";
      const num = Number(String(val).replace(",", "."));
      if (!Number.isFinite(num)) return "";
      return num.toFixed(props.precision);
    };
    const initLocal = () => {
      if (isBase.value) {
        local.value = fmt(1);
        return;
      }
      local.value = fmt(rateValue.value);
    };
    initLocal();
    watch(
      () => props.currency,
      () => initLocal(),
      { deep: true }
    );
    watch(
      () => props.modelValue,
      () => initLocal()
    );
    const normalized = computed(() => {
      const raw = String(local.value ?? "").trim();
      if (!raw) return null;
      return raw.replace(/\s+/g, "").replace(",", ".");
    });
    const isDisabledInput = computed(() => isBase.value || !isActive.value);
    const isValid = computed(() => {
      if (isBase.value) return false;
      if (isDisabledInput.value) return false;
      if (normalized.value === null) return false;
      const v = Number(normalized.value);
      return Number.isFinite(v) && v > 0;
    });
    const busy = ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-fit flex flex-col items-center justify-center gap-1" }, _attrs))}>`);
      if (provider.value) {
        _push(`<div class="px-2 py-0 rounded-sm text-xs opacity-70 bg-slate-100 dark:bg-slate-900/60 border border-gray-400 text-slate-700 dark:text-slate-200">${ssrInterpolate(unref(t)("provider"))}: ${ssrInterpolate(provider.value)}</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="flex items-center gap-2"><input type="number"${ssrRenderAttr("value", local.value)}${ssrRenderAttr("step", __props.step)}${ssrRenderAttr("min", __props.min)}${ssrIncludeBooleanAttr(isDisabledInput.value || busy.value) ? " disabled" : ""}${ssrRenderAttr("placeholder", __props.placeholder || unref(t)("rateForOneBase"))}${ssrRenderAttr("title", rateAtLabel.value)} class="w-28 md:w-32 lg:w-36 px-2 py-0 rounded border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:opacity-60 disabled:cursor-not-allowed"><button type="button" class="inline-flex items-center gap-1 p-1 rounded text-white bg-emerald-500 hover:bg-emerald-700 dark:bg-emerald-800 dark:hover:bg-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed"${ssrIncludeBooleanAttr(!isValid.value || isBase.value || busy.value) ? " disabled" : ""}${ssrRenderAttr("title", isBase.value ? unref(t)("baseUnit") : unref(t)("saveRate"))}><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path></svg></button></div>`);
      if (rateAtLabel.value) {
        _push(`<div class="font-semibold text-[10px] text-sky-700 dark:text-sky-300">${ssrInterpolate(unref(t)("updatedAt"))}: ${ssrInterpolate(rateAtLabel.value)}</div>`);
      } else {
        _push(`<div class="font-semibold text-[10px] text-emerald-700 dark:text-emerald-300">${ssrInterpolate(unref(t)("baseUnit"))}</div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Finance/Currency/Buttons/InlineRateEditor.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "CurrencyTable",
  __ssrInlineRender: true,
  props: {
    currencies: { type: Array, default: () => [] },
    selectedCurrencies: { type: Array, default: () => [] }
  },
  emits: [
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all",
    "set-default",
    "save-rate"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const localCurrencies = ref([]);
    watch(
      () => props.currencies,
      (newVal) => {
        localCurrencies.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      const newOrderIds = localCurrencies.value.map((c) => c.id);
      emits("update-sort-order", newOrderIds);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedCurrencies.length)}</div>`);
      if (localCurrencies.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (__props.currencies.length > 0) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm font-semibold uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-2 py-3 w-px"><div class="font-medium text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("currency"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("code"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold flex justify-center">${ssrInterpolate(unref(t)("symbol"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("rate"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center"><input type="checkbox"></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localCurrencies.value,
          "onUpdate:modelValue": ($event) => localCurrencies.value = $event,
          onEnd: handleDragEnd,
          "item-key": "id",
          handle: ".handle"
        }, {
          item: withCtx(({ element: currency }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="${ssrRenderClass([currency.is_default ? "text-slate-700 dark:text-slate-300" : "text-slate-900 dark:text-slate-100", "font-semibold text-center text-xs"])}"${ssrRenderAttr("title", `sort: ${currency.sort ?? "—"}`)}${_scopeId}>${ssrInterpolate(currency.id)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div${ssrRenderAttr("title", currency.is_default ? unref(t)("mainCurrency") : "")} class="${ssrRenderClass([currency.is_default ? "text-amber-600 dark:text-amber-400" : "text-blue-800 dark:text-blue-200", "flex items-center space-x-1 text-left font-semibold"])}"${_scopeId}>`);
              if (currency.is_default) {
                _push2(`<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-red-500 dark:text-red-400" viewBox="0 0 20 20"${_scopeId}><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.387 2.462a1 1 0 00-.364 1.118l1.287 3.967c.3.921-.755 1.688-1.54 1.118l-3.387-2.462a1 1 0 00-1.176 0l-3.387 2.462c-.785.57-1.84-.197-1.54-1.118l1.287-3.967a1 1 0 00-.364-1.118L2.045 9.394c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.95-.69l1.286-3.967z"${_scopeId}></path></svg>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<span class="${ssrRenderClass(currency.is_default ? "mt-0.5" : "")}"${_scopeId}>${ssrInterpolate(currency.name)}</span>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("admin.currencies.rates.index", currency.id),
                title: unref(t)("currencyRates"),
                class: "inline-flex items-center justify-center p-1 rounded text-blue-500 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-100"
              }, {
                default: withCtx((_, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"${_scopeId2}><path d="M22,1H14a1,1,0,0,0-.707,1.707L16.586,6l-6.293,6.293a1,1,0,1,0,1.414,1.414L18,7.414l3.293,3.293A1,1,0,0,0,22,11a.987.987,0,0,0,.383-.076A1,1,0,0,0,23,10V2A1,1,0,0,0,22,1Z"${_scopeId2}></path><path d="M4,23H18a3,3,0,0,0,3-3V15a1,1,0,0,0-2,0v5a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V6A1,1,0,0,1,4,5H9A1,1,0,0,0,9,3H4A3,3,0,0,0,1,6V20A3,3,0,0,0,4,23Z"${_scopeId2}></path></svg>`);
                  } else {
                    return [
                      (openBlock(), createBlock("svg", {
                        xmlns: "http://www.w3.org/2000/svg",
                        class: "w-4 h-4",
                        viewBox: "0 0 24 24",
                        fill: "currentColor"
                      }, [
                        createVNode("path", { d: "M22,1H14a1,1,0,0,0-.707,1.707L16.586,6l-6.293,6.293a1,1,0,1,0,1.414,1.414L18,7.414l3.293,3.293A1,1,0,0,0,22,11a.987.987,0,0,0,.383-.076A1,1,0,0,0,23,10V2A1,1,0,0,0,22,1Z" }),
                        createVNode("path", { d: "M4,23H18a3,3,0,0,0,3-3V15a1,1,0,0,0-2,0v5a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V6A1,1,0,0,1,4,5H9A1,1,0,0,0,9,3H4A3,3,0,0,0,1,6V20A3,3,0,0,0,4,23Z" })
                      ]))
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="${ssrRenderClass([currency.is_default ? "text-teal-600 dark:text-teal-400" : "text-indigo-700 dark:text-indigo-300", "text-left font-semibold"])}"${_scopeId}>${ssrInterpolate(currency.code)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="${ssrRenderClass([currency.is_default ? "text-teal-600 dark:text-teal-400" : "text-indigo-800 dark:text-indigo-200", "text-left flex justify-center"])}"${_scopeId}>${ssrInterpolate(currency.symbol)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                currency,
                onSave: ($event) => _ctx.$emit("save-rate", $event)
              }, null, _parent2, _scopeId));
              _push2(`</td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                disabled: currency.is_default,
                title: currency.is_default ? unref(t)("mainCurrency") : unref(t)("makeMainCurrency"),
                onClick: ($event) => _ctx.$emit("set-default", currency)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: currency.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", currency),
                title: currency.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                href: _ctx.route("admin.currencies.edit", currency.id)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$9, {
                onClick: ($event) => _ctx.$emit("delete", currency.id, currency.name)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedCurrencies.includes(currency.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
            } else {
              return [
                createVNode("tr", { class: "text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800" }, [
                  createVNode("td", { class: "px-2 py-1 text-center cursor-move handle" }, [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 text-gray-500 dark:text-gray-300",
                      fill: "currentColor",
                      viewBox: "0 0 20 20"
                    }, [
                      createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                    ]))
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: ["font-semibold text-center text-xs", currency.is_default ? "text-slate-700 dark:text-slate-300" : "text-slate-900 dark:text-slate-100"],
                      title: `sort: ${currency.sort ?? "—"}`
                    }, toDisplayString(currency.id), 11, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: ["flex items-center space-x-1 text-left font-semibold", currency.is_default ? "text-amber-600 dark:text-amber-400" : "text-blue-800 dark:text-blue-200"],
                      title: currency.is_default ? unref(t)("mainCurrency") : ""
                    }, [
                      currency.is_default ? (openBlock(), createBlock("svg", {
                        key: 0,
                        xmlns: "http://www.w3.org/2000/svg",
                        class: "w-4 h-4 fill-current text-red-500 dark:text-red-400",
                        viewBox: "0 0 20 20"
                      }, [
                        createVNode("path", { d: "M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.967a1 1 0 00.95.69h4.18c.969 0 1.371 1.24.588 1.81l-3.387 2.462a1 1 0 00-.364 1.118l1.287 3.967c.3.921-.755 1.688-1.54 1.118l-3.387-2.462a1 1 0 00-1.176 0l-3.387 2.462c-.785.57-1.84-.197-1.54-1.118l1.287-3.967a1 1 0 00-.364-1.118L2.045 9.394c-.783-.57-.38-1.81.588-1.81h4.18a1 1 0 00.95-.69l1.286-3.967z" })
                      ])) : createCommentVNode("", true),
                      createVNode("span", {
                        class: currency.is_default ? "mt-0.5" : ""
                      }, toDisplayString(currency.name), 3),
                      createVNode(unref(Link), {
                        href: _ctx.route("admin.currencies.rates.index", currency.id),
                        title: unref(t)("currencyRates"),
                        class: "inline-flex items-center justify-center p-1 rounded text-blue-500 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-100"
                      }, {
                        default: withCtx(() => [
                          (openBlock(), createBlock("svg", {
                            xmlns: "http://www.w3.org/2000/svg",
                            class: "w-4 h-4",
                            viewBox: "0 0 24 24",
                            fill: "currentColor"
                          }, [
                            createVNode("path", { d: "M22,1H14a1,1,0,0,0-.707,1.707L16.586,6l-6.293,6.293a1,1,0,1,0,1.414,1.414L18,7.414l3.293,3.293A1,1,0,0,0,22,11a.987.987,0,0,0,.383-.076A1,1,0,0,0,23,10V2A1,1,0,0,0,22,1Z" }),
                            createVNode("path", { d: "M4,23H18a3,3,0,0,0,3-3V15a1,1,0,0,0-2,0v5a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V6A1,1,0,0,1,4,5H9A1,1,0,0,0,9,3H4A3,3,0,0,0,1,6V20A3,3,0,0,0,4,23Z" })
                          ]))
                        ]),
                        _: 2
                      }, 1032, ["href", "title"])
                    ], 10, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: ["text-left font-semibold", currency.is_default ? "text-teal-600 dark:text-teal-400" : "text-indigo-700 dark:text-indigo-300"]
                    }, toDisplayString(currency.code), 3)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: ["text-left flex justify-center", currency.is_default ? "text-teal-600 dark:text-teal-400" : "text-indigo-800 dark:text-indigo-200"]
                    }, toDisplayString(currency.symbol), 3)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode(_sfc_main$5, {
                      currency,
                      onSave: ($event) => _ctx.$emit("save-rate", $event)
                    }, null, 8, ["currency", "onSave"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-2" }, [
                      createVNode(_sfc_main$6, {
                        disabled: currency.is_default,
                        title: currency.is_default ? unref(t)("mainCurrency") : unref(t)("makeMainCurrency"),
                        onClick: ($event) => _ctx.$emit("set-default", currency)
                      }, null, 8, ["disabled", "title", "onClick"]),
                      createVNode(_sfc_main$7, {
                        isActive: currency.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", currency),
                        title: currency.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$8, {
                        href: _ctx.route("admin.currencies.edit", currency.id)
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$9, {
                        onClick: ($event) => _ctx.$emit("delete", currency.id, currency.name)
                      }, null, 8, ["onClick"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedCurrencies.includes(currency.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", currency.id)
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
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Finance/Currency/Table/CurrencyTable.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "CurrencyCardGrid",
  __ssrInlineRender: true,
  props: {
    currencies: { type: Array, default: () => [] },
    selectedCurrencies: { type: Array, default: () => [] }
  },
  emits: [
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all",
    "set-default",
    "save-rate"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const localCurrencies = ref([]);
    watch(
      () => props.currencies,
      (newVal) => {
        localCurrencies.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      const newOrderIds = localCurrencies.value.map((c) => c.id);
      emits("update-sort-order", newOrderIds);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedCurrencies.length)}</div>`);
      if (localCurrencies.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localCurrencies.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localCurrencies.value,
          "onUpdate:modelValue": ($event) => localCurrencies.value = $event,
          "item-key": "id",
          onEnd: handleDragEnd,
          handle: ".drag-handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: currency }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><div class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200"${ssrRenderAttr("title", `sort: ${currency.sort ?? "—"}`)}${_scopeId}> ID: ${ssrInterpolate(currency.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}><span class="${ssrRenderClass([currency.is_default ? "bg-teal-100 dark:bg-teal-900/60 text-teal-600 dark:text-teal-400" : "bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300", "text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400"])}"${ssrRenderAttr("title", unref(t)("code"))}${_scopeId}>${ssrInterpolate(currency.code || "—")}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedCurrencies.includes(currency.id)) ? " checked" : ""}${_scopeId}></div></div><div class="flex flex-col flex-1 px-3 py-2 space-y-1"${_scopeId}><div class="${ssrRenderClass([currency.is_default ? "text-amber-600 dark:text-amber-400" : "text-blue-800 dark:text-blue-200", "text-sm font-semibold text-center line-clamp-2"])}"${ssrRenderAttr("title", currency.name)}${_scopeId}>${ssrInterpolate(currency.name || "—")}</div><div class="flex flex-row items-center justify-center gap-1"${_scopeId}><div class="${ssrRenderClass([currency.is_default ? "text-teal-600 dark:text-teal-400" : "text-indigo-700 dark:text-indigo-300", "font-semibold text-[14px] text-center"])}"${_scopeId}>`);
              if (currency.symbol) {
                _push2(`<span${_scopeId}>${ssrInterpolate(currency.symbol)} • </span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<span${_scopeId}>${ssrInterpolate(currency.code || "—")}</span></div>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("admin.currencies.rates.index", currency.id),
                title: unref(t)("currencyRates"),
                class: "inline-flex items-center justify-center p-1 rounded text-blue-500 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-100"
              }, {
                default: withCtx((_, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"${_scopeId2}><path d="M22,1H14a1,1,0,0,0-.707,1.707L16.586,6l-6.293,6.293a1,1,0,1,0,1.414,1.414L18,7.414l3.293,3.293A1,1,0,0,0,22,11a.987.987,0,0,0,.383-.076A1,1,0,0,0,23,10V2A1,1,0,0,0,22,1Z"${_scopeId2}></path><path d="M4,23H18a3,3,0,0,0,3-3V15a1,1,0,0,0-2,0v5a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V6A1,1,0,0,1,4,5H9A1,1,0,0,0,9,3H4A3,3,0,0,0,1,6V20A3,3,0,0,0,4,23Z"${_scopeId2}></path></svg>`);
                  } else {
                    return [
                      (openBlock(), createBlock("svg", {
                        xmlns: "http://www.w3.org/2000/svg",
                        class: "w-4 h-4",
                        viewBox: "0 0 24 24",
                        fill: "currentColor"
                      }, [
                        createVNode("path", { d: "M22,1H14a1,1,0,0,0-.707,1.707L16.586,6l-6.293,6.293a1,1,0,1,0,1.414,1.414L18,7.414l3.293,3.293A1,1,0,0,0,22,11a.987.987,0,0,0,.383-.076A1,1,0,0,0,23,10V2A1,1,0,0,0,22,1Z" }),
                        createVNode("path", { d: "M4,23H18a3,3,0,0,0,3-3V15a1,1,0,0,0-2,0v5a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V6A1,1,0,0,1,4,5H9A1,1,0,0,0,9,3H4A3,3,0,0,0,1,6V20A3,3,0,0,0,4,23Z" })
                      ]))
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</div><div class="text-center text-[11px] mt-2 text-slate-900 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("rate"))}: <span class="font-semibold text-amber-600 dark:text-amber-400"${_scopeId}>${ssrInterpolate(currency.rate_vs_default ?? "—")}</span></div><div class="mt-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                currency,
                onSave: ($event) => _ctx.$emit("save-rate", $event)
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                disabled: currency.is_default,
                title: currency.is_default ? unref(t)("mainCurrency") : unref(t)("makeMainCurrency"),
                onClick: ($event) => _ctx.$emit("set-default", currency)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: currency.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", currency),
                title: currency.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                href: _ctx.route("admin.currencies.edit", currency.id)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$9, {
                onClick: ($event) => _ctx.$emit("delete", currency.id, currency.name)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div>`);
            } else {
              return [
                createVNode("div", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
                  createVNode("div", { class: "flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100",
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
                        class: "text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200",
                        title: `sort: ${currency.sort ?? "—"}`
                      }, " ID: " + toDisplayString(currency.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400", currency.is_default ? "bg-teal-100 dark:bg-teal-900/60 text-teal-600 dark:text-teal-400" : "bg-indigo-100 dark:bg-indigo-900/60 text-indigo-700 dark:text-indigo-300"],
                        title: unref(t)("code")
                      }, toDisplayString(currency.code || "—"), 11, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedCurrencies.includes(currency.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", currency.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-1" }, [
                    createVNode("div", {
                      class: ["text-sm font-semibold text-center line-clamp-2", currency.is_default ? "text-amber-600 dark:text-amber-400" : "text-blue-800 dark:text-blue-200"],
                      title: currency.name
                    }, toDisplayString(currency.name || "—"), 11, ["title"]),
                    createVNode("div", { class: "flex flex-row items-center justify-center gap-1" }, [
                      createVNode("div", {
                        class: ["font-semibold text-[14px] text-center", currency.is_default ? "text-teal-600 dark:text-teal-400" : "text-indigo-700 dark:text-indigo-300"]
                      }, [
                        currency.symbol ? (openBlock(), createBlock("span", { key: 0 }, toDisplayString(currency.symbol) + " • ", 1)) : createCommentVNode("", true),
                        createVNode("span", null, toDisplayString(currency.code || "—"), 1)
                      ], 2),
                      createVNode(unref(Link), {
                        href: _ctx.route("admin.currencies.rates.index", currency.id),
                        title: unref(t)("currencyRates"),
                        class: "inline-flex items-center justify-center p-1 rounded text-blue-500 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-100"
                      }, {
                        default: withCtx(() => [
                          (openBlock(), createBlock("svg", {
                            xmlns: "http://www.w3.org/2000/svg",
                            class: "w-4 h-4",
                            viewBox: "0 0 24 24",
                            fill: "currentColor"
                          }, [
                            createVNode("path", { d: "M22,1H14a1,1,0,0,0-.707,1.707L16.586,6l-6.293,6.293a1,1,0,1,0,1.414,1.414L18,7.414l3.293,3.293A1,1,0,0,0,22,11a.987.987,0,0,0,.383-.076A1,1,0,0,0,23,10V2A1,1,0,0,0,22,1Z" }),
                            createVNode("path", { d: "M4,23H18a3,3,0,0,0,3-3V15a1,1,0,0,0-2,0v5a1,1,0,0,1-1,1H4a1,1,0,0,1-1-1V6A1,1,0,0,1,4,5H9A1,1,0,0,0,9,3H4A3,3,0,0,0,1,6V20A3,3,0,0,0,4,23Z" })
                          ]))
                        ]),
                        _: 2
                      }, 1032, ["href", "title"])
                    ]),
                    createVNode("div", { class: "text-center text-[11px] mt-2 text-slate-900 dark:text-slate-200" }, [
                      createTextVNode(toDisplayString(unref(t)("rate")) + ": ", 1),
                      createVNode("span", { class: "font-semibold text-amber-600 dark:text-amber-400" }, toDisplayString(currency.rate_vs_default ?? "—"), 1)
                    ]),
                    createVNode("div", { class: "mt-2" }, [
                      createVNode(_sfc_main$5, {
                        currency,
                        onSave: ($event) => _ctx.$emit("save-rate", $event)
                      }, null, 8, ["currency", "onSave"])
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$6, {
                        disabled: currency.is_default,
                        title: currency.is_default ? unref(t)("mainCurrency") : unref(t)("makeMainCurrency"),
                        onClick: ($event) => _ctx.$emit("set-default", currency)
                      }, null, 8, ["disabled", "title", "onClick"]),
                      createVNode(_sfc_main$7, {
                        isActive: currency.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", currency),
                        title: currency.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$8, {
                        href: _ctx.route("admin.currencies.edit", currency.id)
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$9, {
                        onClick: ($event) => _ctx.$emit("delete", currency.id, currency.name)
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
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Finance/Currency/View/CurrencyCardGrid.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "BulkActionSelect",
  __ssrInlineRender: true,
  emits: ["change"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-500">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="" disabled selected>${ssrInterpolate(unref(t)("selectAction"))}</option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option></select></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Finance/Currency/Select/BulkActionSelect.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: String
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-44 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option value="sort">${ssrInterpolate(unref(t)("sortNumber"))}</option><option value="name">${ssrInterpolate(unref(t)("name"))}</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option></select></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Finance/Currency/Sort/SortSelect.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currencies: { type: Array, default: () => [] },
    currenciesCount: { type: Number, default: 0 },
    adminFinanceCurrenciesPerPage: { type: Number, default: 10 },
    adminFinanceCurrenciesDefaultSort: { type: String, default: "idDesc" }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(localStorage.getItem("admin_view_mode") || "table");
    watch(viewMode, (val) => localStorage.setItem("admin_view_mode", val));
    const itemsPerPage = ref(props.adminFinanceCurrenciesPerPage || 10);
    watch(itemsPerPage, (newVal) => {
      router.put(route("admin.settings.updateAdminCountCurrencies"), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error((errors == null ? void 0 : errors.value) || "Ошибка обновления кол-ва элементов.")
      });
    });
    const sortParam = ref(props.adminFinanceCurrenciesDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      router.put(route("admin.settings.updateAdminSortCurrencies"), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info("Сортировка успешно изменена"),
        onError: (errors) => toast.error((errors == null ? void 0 : errors.value) || "Ошибка обновления сортировки.")
      });
    });
    const showConfirmDeleteModal = ref(false);
    const currencyToDeleteId = ref(null);
    const currencyToDeleteName = ref("");
    const confirmDelete = (id, name) => {
      currencyToDeleteId.value = id;
      currencyToDeleteName.value = name;
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      currencyToDeleteId.value = null;
      currencyToDeleteName.value = "";
    };
    const deleteCurrency = () => {
      if (currencyToDeleteId.value === null) return;
      const idToDelete = currencyToDeleteId.value;
      const nameToDelete = currencyToDeleteName.value;
      router.delete(route("admin.currencies.destroy", { currency: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          closeModal();
          toast.success(`Валюта "${nameToDelete || "ID: " + idToDelete}" удалена.`);
        },
        onError: (errors) => {
          closeModal();
          const errorMsg = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[Object.keys(errors || {})[0]]) || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Валюта: ${nameToDelete || "ID: " + idToDelete})`);
          console.error("Ошибка удаления:", errors);
        },
        onFinish: () => {
          currencyToDeleteId.value = null;
          currencyToDeleteName.value = "";
        }
      });
    };
    const currentPage = ref(1);
    const searchQuery = ref("");
    const sortCurrencies = (currencies) => {
      const value = sortParam.value;
      const list = currencies.slice();
      if (value === "idAsc") return list.sort((a, b) => a.id - b.id);
      if (value === "idDesc") return list.sort((a, b) => b.id - a.id);
      if (value === "activity") return currencies.filter((c) => c.activity);
      if (value === "inactive") return currencies.filter((c) => !c.activity);
      return list.sort((a, b) => {
        const av = (a == null ? void 0 : a[value]) ?? "";
        const bv = (b == null ? void 0 : b[value]) ?? "";
        if (av < bv) return -1;
        if (av > bv) return 1;
        return 0;
      });
    };
    const filteredCurrencies = computed(() => {
      let filtered = props.currencies || [];
      if (searchQuery.value) {
        const q = searchQuery.value.toLowerCase();
        filtered = filtered.filter(
          (c) => (c.name || "").toLowerCase().includes(q) || (c.code || "").toLowerCase().includes(q)
        );
      }
      return sortCurrencies(filtered);
    });
    const paginatedCurrencies = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      return filteredCurrencies.value.slice(start, start + itemsPerPage.value);
    });
    watch(searchQuery, () => {
      currentPage.value = 1;
    });
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (currentPage.value - 1) * itemsPerPage.value;
      const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1
      }));
      router.put(
        route("admin.actions.currencies.updateSortBulk"),
        { currencies: sortData },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.success("Порядок валют успешно обновлён."),
          onError: (errors) => {
            console.error("Ошибка обновления сортировки валют:", errors);
            toast.error(
              (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors.currencies) || "Не удалось обновить порядок валют."
            );
            router.reload({ only: ["currencies"], preserveScroll: true });
          }
        }
      );
    };
    const selectedCurrencies = ref([]);
    const toggleAll = ({ ids, checked }) => {
      if (checked) selectedCurrencies.value = [...ids];
      else selectedCurrencies.value = [];
    };
    const toggleSelectCurrency = (id) => {
      const idx = selectedCurrencies.value.indexOf(id);
      if (idx > -1) selectedCurrencies.value.splice(idx, 1);
      else selectedCurrencies.value.push(id);
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedCurrencies.value.length) {
        toast.warning("Выберите валюты для активации/деактивации");
        return;
      }
      router.put(
        route("admin.actions.currencies.bulkUpdateActivity"),
        { ids: selectedCurrencies.value, activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success("Активность валют массово обновлена");
            const updatedIds = [...selectedCurrencies.value];
            selectedCurrencies.value = [];
            paginatedCurrencies.value.forEach((c) => {
              if (updatedIds.includes(c.id)) c.activity = newActivity;
            });
          },
          onError: (errors) => {
            const msg = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Не удалось массово обновить активность валют";
            toast.error(msg);
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedCurrencies.value.length) {
        toast.warning("Выберите хотя бы одну валюту для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные валюты?")) return;
      router.delete(route("admin.actions.currencies.bulkDestroy"), {
        data: { ids: selectedCurrencies.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          selectedCurrencies.value = [];
          toast.success("Массовое удаление валют успешно завершено.");
        },
        onError: (errors) => {
          console.error("Ошибка массового удаления:", errors);
          const key = Object.keys(errors || {})[0];
          toast.error((errors == null ? void 0 : errors[key]) || "Произошла ошибка при удалении валют.");
        }
      });
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") selectedCurrencies.value = paginatedCurrencies.value.map((c) => c.id);
      else if (action === "deselectAll") selectedCurrencies.value = [];
      else if (action === "activate") bulkToggleActivity(true);
      else if (action === "deactivate") bulkToggleActivity(false);
      else if (action === "delete") bulkDelete();
      event.target.value = "";
    };
    const toggleActivity = (currency) => {
      const newActivity = !currency.activity;
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route("admin.actions.currencies.updateActivity", { currency: currency.id }),
        { activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            currency.activity = newActivity;
            toast.success(`Валюта "${currency.name}" ${actionText}.`);
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для "${currency.name}".`
            );
          }
        }
      );
    };
    const currentBase = computed(() => (props.currencies || []).find((c) => c.is_default) || null);
    const refreshFromProvider = () => {
      if (!currentBase.value) {
        toast.error("Не выбрана основная валюта.");
        return;
      }
      if (!confirm(`Обновить курсы из провайдера для базы ${currentBase.value.code}?`)) return;
      router.post(
        route("admin.actions.currencies.refreshRates", { currency: currentBase.value.id }),
        {},
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => toast.success("Курсы обновлены из провайдера."),
          onError: (errors) => toast.error((errors == null ? void 0 : errors.general) || "Ошибка обновления курсов из провайдера.")
        }
      );
    };
    const setDefault = (currency) => {
      if (currency.is_default) return;
      router.put(
        route("admin.actions.currencies.setDefault", { currency: currency.id }),
        {},
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => toast.success(`Назначена основной: ${currency.name}`),
          onError: (errors) => toast.error((errors == null ? void 0 : errors.general) || "Не удалось назначить основную валюту.")
        }
      );
    };
    const saveRate = ({ id, value }) => {
      router.post(
        route("admin.actions.currencies.updateRate", { currency: id }),
        { rate: value, provider: "manual" },
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => toast.success("Курс обновлён"),
          onError: (errors) => toast.error((errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors.rate) || "Ошибка обновления курса")
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("currencies")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("currencies"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("currencies")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("currencies")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b;
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$a, {
              href: _ctx.route("admin.currencies.create")
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
                  _push3(` ${ssrInterpolate(unref(t)("addCurrency"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addCurrency")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$b, {
              title: `${unref(t)("currencyRefreshRates")} (${((_a = currentBase.value) == null ? void 0 : _a.code) || "—"})`,
              disabled: !currentBase.value,
              onClick: refreshFromProvider
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.currenciesCount) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.currenciesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between mb-3 gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$d, {
                "items-per-page": itemsPerPage.value,
                "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$1, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (val) => sortParam.value = val
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.currenciesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.currenciesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.currenciesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              if (__props.currenciesCount) {
                _push2(ssrRenderComponent(_sfc_main$2, { onChange: handleBulkAction }, null, _parent2, _scopeId));
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
            if (__props.currenciesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$g, {
                "current-page": currentPage.value,
                "items-per-page": itemsPerPage.value,
                "total-items": filteredCurrencies.value.length,
                "onUpdate:currentPage": ($event) => currentPage.value = $event,
                "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$4, {
                currencies: paginatedCurrencies.value,
                "selected-currencies": selectedCurrencies.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectCurrency,
                onToggleAll: toggleAll,
                onSetDefault: setDefault,
                onSaveRate: saveRate
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$3, {
                currencies: paginatedCurrencies.value,
                "selected-currencies": selectedCurrencies.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectCurrency,
                onToggleAll: toggleAll,
                onSetDefault: setDefault,
                onSaveRate: saveRate
              }, null, _parent2, _scopeId));
            }
            if (__props.currenciesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$g, {
                "current-page": currentPage.value,
                "items-per-page": itemsPerPage.value,
                "total-items": filteredCurrencies.value.length,
                "onUpdate:currentPage": ($event) => currentPage.value = $event,
                "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$h, {
              show: showConfirmDeleteModal.value,
              onClose: closeModal,
              onCancel: closeModal,
              onConfirm: deleteCurrency,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3" }, [
                    createVNode(_sfc_main$a, {
                      href: _ctx.route("admin.currencies.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addCurrency")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$b, {
                      title: `${unref(t)("currencyRefreshRates")} (${((_b = currentBase.value) == null ? void 0 : _b.code) || "—"})`,
                      disabled: !currentBase.value,
                      onClick: refreshFromProvider
                    }, null, 8, ["title", "disabled"])
                  ]),
                  __props.currenciesCount ? (openBlock(), createBlock(_sfc_main$c, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.currenciesCount ? (openBlock(), createBlock("div", {
                    key: 1,
                    class: "flex flex-col lg:flex-row items-center justify-between mb-3 gap-1"
                  }, [
                    createVNode(_sfc_main$d, {
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"]),
                    createVNode(_sfc_main$1, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.currenciesCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.currenciesCount), 1)
                      ]),
                      _: 1
                    }),
                    __props.currenciesCount ? (openBlock(), createBlock(_sfc_main$2, {
                      key: 0,
                      onChange: handleBulkAction
                    })) : createCommentVNode("", true),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.currenciesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex justify-center items-center flex-col md:flex-row"
                  }, [
                    createVNode(_sfc_main$g, {
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredCurrencies.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage", "onUpdate:itemsPerPage"])
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$4, {
                    key: 4,
                    currencies: paginatedCurrencies.value,
                    "selected-currencies": selectedCurrencies.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectCurrency,
                    onToggleAll: toggleAll,
                    onSetDefault: setDefault,
                    onSaveRate: saveRate
                  }, null, 8, ["currencies", "selected-currencies"])) : (openBlock(), createBlock(_sfc_main$3, {
                    key: 5,
                    currencies: paginatedCurrencies.value,
                    "selected-currencies": selectedCurrencies.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectCurrency,
                    onToggleAll: toggleAll,
                    onSetDefault: setDefault,
                    onSaveRate: saveRate
                  }, null, 8, ["currencies", "selected-currencies"])),
                  __props.currenciesCount ? (openBlock(), createBlock("div", {
                    key: 6,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    createVNode(_sfc_main$g, {
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredCurrencies.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage", "onUpdate:itemsPerPage"])
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$h, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteCurrency,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Finance/Currencies/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
