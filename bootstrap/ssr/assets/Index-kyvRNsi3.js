import { ref, mergeProps, useSSRContext, computed, unref, reactive, watch, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode } from "vue";
import { ssrRenderAttrs, ssrRenderSlot, ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderClass, ssrRenderAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$9 } from "./DefaultButton-DMDyGacf.js";
import { useForm, router } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { _ as _sfc_main$7 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$8 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$6 } from "./RefreshRatesButton-D_vPWECI.js";
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
  __name: "PrimaryButtonExt",
  __ssrInlineRender: true,
  props: {
    href: {
      type: String,
      default: "submit"
    }
  },
  setup(__props) {
    const isPressed = ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        class: ["flex items-center btn px-2 py-0.5 bg-emerald-500 shadow-md text-white text-sm font-semibold transition-colors duration-300 ease-in-out hover:bg-emerald-600 focus:bg-emerald-600 focus:outline-none", { "ring-2 ring-emerald-500 ring-offset-2 ring-offset-white": isPressed.value }]
      }, _attrs))}><span>`);
      ssrRenderSlot(_ctx.$slots, "icon", {}, () => {
        _push(`<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path></svg>`);
      }, _push, _parent);
      _push(`</span><span class="hidden xs:block ml-2">`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</span></button>`);
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/PrimaryButtonExt.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "PrimaryButtonAdd",
  __ssrInlineRender: true,
  props: {
    href: {
      type: String,
      default: "submit"
    }
  },
  setup(__props) {
    const isPressed = ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        class: ["flex items-center btn px-2 py-0.5 bg-teal-500 shadow-md text-white text-sm font-semibold transition-colors duration-300 ease-in-out hover:bg-teal-600 focus:bg-teal-600 focus:outline-none", { "ring-2 ring-teal-500 ring-offset-2 ring-offset-white": isPressed.value }]
      }, _attrs))}><span>`);
      ssrRenderSlot(_ctx.$slots, "icon", {}, () => {
        _push(`<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path></svg>`);
      }, _push, _parent);
      _push(`</span><span class="hidden xs:block ml-2">`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</span></button>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/PrimaryButtonAdd.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "DeleteIcon",
  __ssrInlineRender: true,
  emits: ["click"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const isPressed = ref(false);
    const buttonClass = computed(() => [
      "flex items-center py-2 px-2 rounded",
      "border border-slate-300",
      "hover:border-rose-500",
      "dark:border-rose-300 dark:hover:border-rose-100",
      {
        "ring-2 ring-sky-600 ring-offset-2 ring-offset-white": isPressed.value
      }
    ]);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        class: buttonClass.value,
        title: unref(t)("delete")
      }, _attrs))}><svg class="w-4 h-4 fill-current text-rose-400 hover:text-rose-500 dark:text-red-300 dark:hover:text-red-100 shrink-0" viewBox="0 0 16 16"><path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z"></path></svg></button>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/DeleteIcon.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "InputNumberExt",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: [String, Number],
      default: ""
    },
    type: {
      type: String,
      default: "number"
    },
    inputmode: {
      type: String,
      default: "decimal"
    },
    step: {
      type: [String, Number],
      default: "any"
    },
    min: {
      type: [String, Number],
      default: void 0
    },
    max: {
      type: [String, Number],
      default: void 0
    }
  },
  emits: ["update:modelValue", "input", "blur"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const emit = __emit;
    const valueStr = computed({
      get: () => (props.modelValue ?? "") === null ? "" : String(props.modelValue ?? ""),
      set: (v) => {
        emit("update:modelValue", v);
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<input${ssrRenderAttrs(mergeProps({
        type: __props.type,
        class: "form-input py-0.5 font-semibold text-sm rounded-sm shadow-sm border-slate-500 focus:border-indigo-500 focus:ring-indigo-300 dark:bg-cyan-800 dark:text-slate-100",
        inputmode: __props.inputmode,
        step: __props.step,
        min: __props.min,
        max: __props.max,
        value: valueStr.value
      }, _attrs))}>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Finance/CurrencyRate/Input/InputNumberExt.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "CurrencyRatesTable",
  __ssrInlineRender: true,
  props: {
    currency: { type: Object, required: true },
    // {id, code, name}
    rates: { type: Array, required: true },
    // CurrencyRateResource[]
    currencies: { type: Array, required: true }
    // [{id, code, name}]
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const currenciesByCode = computed(() => {
      const map = /* @__PURE__ */ Object.create(null);
      for (const c of props.currencies) {
        map[String(c.code || "").toUpperCase()] = c;
      }
      return map;
    });
    function toRow(r) {
      const quote = (r == null ? void 0 : r.quote_currency) ?? { id: "", code: "", name: "" };
      const rateStr = (r == null ? void 0 : r.rate) !== void 0 && (r == null ? void 0 : r.rate) !== null && (r == null ? void 0 : r.rate) !== "" ? String(r.rate) : "";
      return {
        id: (r == null ? void 0 : r.id) ?? null,
        quote_currency_id: (quote == null ? void 0 : quote.id) ?? "",
        quote,
        rate: rateStr !== "" ? Number(rateStr) : null,
        provider: (r == null ? void 0 : r.provider) ?? "",
        fetched_at: (r == null ? void 0 : r.fetched_at) ?? null,
        _rate: rateStr,
        // редактируемая строка
        _provider: String((r == null ? void 0 : r.provider) ?? ""),
        // редактируемая строка
        _dirty: false,
        _saving: false,
        _error: null
      };
    }
    function emptyRow() {
      return toRow({
        id: null,
        quote_currency: { id: "", code: "", name: "" },
        rate: "",
        provider: "",
        fetched_at: null
      });
    }
    function toNumber(val) {
      if (val === "" || val === void 0 || val === null) return NaN;
      return Number(String(val).replace(",", "."));
    }
    function reloadRates() {
      router.reload({ only: ["rates"], preserveScroll: true });
    }
    const rows = reactive((props.rates || []).map(toRow));
    watch(
      () => props.rates,
      (newRates) => {
        rows.splice(0, rows.length, ...(newRates || []).map(toRow));
      },
      { deep: true }
    );
    const dirtyRows = computed(() => rows.filter((r) => r._dirty));
    function addRow() {
      rows.push(emptyRow());
    }
    function removeLocalRow(idx) {
      rows.splice(idx, 1);
    }
    function onQuoteCodeChanged(row) {
      var _a;
      const code = (((_a = row.quote) == null ? void 0 : _a.code) || "").trim().toUpperCase();
      const found = code ? currenciesByCode.value[code] : null;
      if (found) {
        row.quote_currency_id = found.id;
        row.quote = { id: found.id, code: found.code, name: found.name };
      } else {
        row.quote_currency_id = "";
        row.quote = { id: "", code, name: "" };
      }
      row._dirty = true;
    }
    function markDirty(row) {
      const oldRateNum = Number.isFinite(Number(row.rate)) ? Number(row.rate) : NaN;
      const newRateNum = toNumber(row._rate);
      const providerChanged = String(row._provider ?? "").trim() !== String(row.provider ?? "").trim();
      const rateChanged = Number.isFinite(oldRateNum) && Number.isFinite(newRateNum) && oldRateNum !== newRateNum || Number.isFinite(oldRateNum) && !Number.isFinite(newRateNum) || !Number.isFinite(oldRateNum) && Number.isFinite(newRateNum);
      row._dirty = rateChanged || providerChanged;
    }
    function deleteRow(idx, row) {
      if (!row.id) {
        removeLocalRow(idx);
        return;
      }
      row._saving = true;
      row._error = null;
      router.delete(
        route("admin.currencies.rates.destroy", { currency: props.currency.id, rate: row.id }),
        {
          preserveScroll: true,
          onSuccess: () => {
            toast.success("Запись курса удалена.");
            reloadRates();
          },
          onError: (errors) => {
            row._error = Object.values(errors)[0] || "Ошибка удаления курса.";
            toast.error(row._error);
          },
          onFinish: () => {
            row._saving = false;
          }
        }
      );
    }
    function saveBulk() {
      if (!dirtyRows.value.length) {
        toast.info("Нет изменений для сохранения.");
        return;
      }
      const prepared = dirtyRows.value.map((r) => ({
        quote_currency_id: r.quote_currency_id,
        rate: toNumber(r._rate),
        provider: r._provider || null,
        fetched_at: null
      }));
      if (prepared.some((p) => !p.quote_currency_id || !Number.isFinite(p.rate) || p.rate <= 0)) {
        toast.error("Обнаружены некорректные данные.");
        return;
      }
      const form = useForm({ rates: prepared });
      form.post(route("admin.currencies.rates.bulk", { currency: props.currency.id }), {
        preserveScroll: true,
        onSuccess: () => {
          toast.success("Все изменения сохранены.");
          reloadRates();
        },
        onError: (errors) => {
          toast.error(Object.values(errors)[0] || "Ошибка пакетного сохранения.");
        }
      });
    }
    function refreshFromProvider() {
      const form = useForm({});
      form.post(route("admin.currencies.rates.refresh", { currency: props.currency.id }), {
        preserveScroll: true,
        onSuccess: () => {
          toast.success("Курсы обновлены от провайдера.");
          reloadRates();
        },
        onError: (errors) => toast.error(Object.values(errors)[0] || "Ошибка обновления курсов.")
      });
    }
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(_attrs)}><div class="sm:flex sm:justify-between items-center mb-2"><div class="mb-1 flex items-center gap-2">`);
      _push(ssrRenderComponent(_sfc_main$4, { onClick: addRow }, {
        icon: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<svg class="w-4 h-4 fill-current text-slate-100" viewBox="0 0 16 16"${_scopeId}><path d="M7 0h2v16H7zM0 7h16v2H0z"${_scopeId}></path></svg>`);
          } else {
            return [
              (openBlock(), createBlock("svg", {
                class: "w-4 h-4 fill-current text-slate-100",
                viewBox: "0 0 16 16"
              }, [
                createVNode("path", { d: "M7 0h2v16H7zM0 7h16v2H0z" })
              ]))
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` ${ssrInterpolate(unref(t)("addRow"))}`);
          } else {
            return [
              createTextVNode(" " + toDisplayString(unref(t)("addRow")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="mb-1 flex items-center gap-2">`);
      _push(ssrRenderComponent(_sfc_main$6, { onClick: refreshFromProvider }, {
        icon: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 24 24"${_scopeId}><path d="M12 6V3L8 7l4 4V8a4 4 0 1 1-4 4H6a6 6 0 1 0 6-6z"${_scopeId}></path></svg>`);
          } else {
            return [
              (openBlock(), createBlock("svg", {
                class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                viewBox: "0 0 24 24"
              }, [
                createVNode("path", { d: "M12 6V3L8 7l4 4V8a4 4 0 1 1-4 4H6a6 6 0 1 0 6-6z" })
              ]))
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` ${ssrInterpolate(unref(t)("currencyRefreshRates"))}`);
          } else {
            return [
              createTextVNode(" " + toDisplayString(unref(t)("currencyRefreshRates")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div><div class="overflow-x-auto"><table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="font-medium px-2 first:pl-6 last:pr-6 py-3 whitespace-nowrap">${ssrInterpolate(unref(t)("code"))}</th><th class="font-medium px-2 first:pl-6 last:pr-6 py-3 whitespace-nowrap">${ssrInterpolate(unref(t)("name"))}</th><th class="font-medium px-2 first:pl-6 last:pr-6 py-3 whitespace-nowrap">${ssrInterpolate(unref(t)("rate"))}</th><th class="font-medium px-2 first:pl-6 last:pr-6 py-3 whitespace-nowrap">${ssrInterpolate(unref(t)("provider"))}</th><th class="font-medium px-2 first:pl-6 last:pr-6 py-3 whitespace-nowrap">${ssrInterpolate(unref(t)("updatedAt"))}</th><th class="font-medium px-2 first:pl-6 last:pr-6 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("actions"))}</th></tr></thead><tbody><!--[-->`);
      ssrRenderList(rows, (r, idx) => {
        var _a, _b;
        _push(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"><td class="px-2 first:pl-6 last:pr-6 py-1 whitespace-nowrap"><div class="flex items-center gap-2">`);
        if (!r.quote_currency_id) {
          _push(ssrRenderComponent(_sfc_main$7, {
            modelValue: r.quote.code,
            "onUpdate:modelValue": [($event) => r.quote.code = $event, ($event) => onQuoteCodeChanged(r)],
            class: "w-28 uppercase",
            placeholder: unref(t)("currencyCodeComment")
          }, null, _parent));
        } else {
          _push(`<span class="font-semibold">${ssrInterpolate(((_a = r.quote) == null ? void 0 : _a.code) || "—")}</span>`);
        }
        _push(`</div></td><td class="px-2 first:pl-6 last:pr-6 py-1 whitespace-nowrap">`);
        if ((_b = r.quote) == null ? void 0 : _b.name) {
          _push(`<span class="text-amber-600 dark:text-amber-400">${ssrInterpolate(r.quote.name)}</span>`);
        } else {
          _push(`<span class="text-slate-400 italic">${ssrInterpolate(unref(t)("selectCurrency"))}</span>`);
        }
        _push(`</td><td class="px-2 first:pl-6 last:pr-6 py-1 whitespace-nowrap">`);
        _push(ssrRenderComponent(_sfc_main$2, {
          modelValue: r._rate,
          "onUpdate:modelValue": ($event) => r._rate = $event,
          type: "number",
          inputmode: "decimal",
          step: 1e-8,
          min: 1e-8,
          onInput: ($event) => markDirty(r)
        }, null, _parent));
        _push(ssrRenderComponent(_sfc_main$8, {
          message: r._error,
          class: "mt-1"
        }, null, _parent));
        _push(`</td><td class="px-2 first:pl-6 last:pr-6 py-1 whitespace-nowrap">`);
        _push(ssrRenderComponent(_sfc_main$7, {
          modelValue: r._provider,
          "onUpdate:modelValue": ($event) => r._provider = $event,
          class: "w-36",
          onInput: ($event) => markDirty(r)
        }, null, _parent));
        _push(`</td><td class="px-2 first:pl-6 last:pr-6 py-1 whitespace-nowrap"><span class="text-violet-800 dark:text-violet-200">${ssrInterpolate(r.fetched_at ? new Date(r.fetched_at).toLocaleString() : "—")}</span></td><td class="px-3 py-2"><div class="flex items-center justify-center gap-2"><button type="button" class="${ssrRenderClass([{ "opacity-50 pointer-events-none": r._saving || !r._dirty }, "inline-flex items-center gap-1 p-2 rounded text-white bg-emerald-500 hover:bg-emerald-700 dark:bg-emerald-800 dark:hover:bg-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed"])}"${ssrRenderAttr("title", unref(t)("saveRate"))}><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"></path></svg></button>`);
        if (r.id) {
          _push(ssrRenderComponent(_sfc_main$3, {
            class: { "opacity-50 pointer-events-none": r._saving },
            onClick: ($event) => deleteRow(idx, r)
          }, {
            icon: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<svg class="w-4 h-4 fill-current text-rose-400 hover:text-rose-500 dark:text-red-300 dark:hover:text-red-100 shrink-0" viewBox="0 0 16 16"${_scopeId}><path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z"${_scopeId}></path></svg>`);
              } else {
                return [
                  (openBlock(), createBlock("svg", {
                    class: "w-4 h-4 fill-current text-rose-400 hover:text-rose-500 dark:text-red-300 dark:hover:text-red-100 shrink-0",
                    viewBox: "0 0 16 16"
                  }, [
                    createVNode("path", { d: "M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" })
                  ]))
                ];
              }
            }),
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(` ${ssrInterpolate(unref(t)("delete"))}`);
              } else {
                return [
                  createTextVNode(" " + toDisplayString(unref(t)("delete")), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
        } else {
          _push(ssrRenderComponent(_sfc_main$3, {
            onClick: ($event) => removeLocalRow(idx),
            href: "#"
          }, {
            icon: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 24 24"${_scopeId}><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" fill="none"${_scopeId}></path></svg>`);
              } else {
                return [
                  (openBlock(), createBlock("svg", {
                    class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                    viewBox: "0 0 24 24"
                  }, [
                    createVNode("path", {
                      d: "M18 6L6 18M6 6l12 12",
                      stroke: "currentColor",
                      "stroke-width": "2",
                      fill: "none"
                    })
                  ]))
                ];
              }
            }),
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(` ${ssrInterpolate(unref(t)("remove"))}`);
              } else {
                return [
                  createTextVNode(" " + toDisplayString(unref(t)("remove")), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
        }
        _push(`</div></td></tr>`);
      });
      _push(`<!--]-->`);
      if (!rows.length) {
        _push(`<tr><td colspan="6" class="px-3 py-6 text-center text-slate-500">${ssrInterpolate(unref(t)("noData"))}</td></tr>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</tbody></table></div><div class="flex items-center justify-center gap-2 mt-4">`);
      _push(ssrRenderComponent(_sfc_main$5, {
        class: ["ms-4 mb-0", { "opacity-50 pointer-events-none": !dirtyRows.value.length }],
        onClick: saveBulk
      }, {
        icon: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"${_scopeId}><path d="M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z"${_scopeId}></path></svg>`);
          } else {
            return [
              (openBlock(), createBlock("svg", {
                xmlns: "http://www.w3.org/2000/svg",
                class: "w-4 h-4",
                viewBox: "0 0 24 24",
                fill: "currentColor"
              }, [
                createVNode("path", { d: "M22.707,6.707,17.293,1.293A1,1,0,0,0,16.586,1H4A3,3,0,0,0,1,4V20a3,3,0,0,0,3,3H20a3,3,0,0,0,3-3V7.414A1,1,0,0,0,22.707,6.707ZM14.5,4h1a.5.5,0,0,1,.5.5v4a.5.5,0,0,1-.5.5h-1a.5.5,0,0,1-.5-.5v-4A.5.5,0,0,1,14.5,4ZM19,12.5v6a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-6a.5.5,0,0,1,.5-.5h13A.5.5,0,0,1,19,12.5Z" })
              ]))
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` ${ssrInterpolate(unref(t)("save"))}`);
          } else {
            return [
              createTextVNode(" " + toDisplayString(unref(t)("save")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Finance/CurrencyRate/Table/CurrencyRatesTable.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currency: { type: Object, required: true },
    rates: { type: [Array, Object], required: true },
    // ✅ важно
    currencies: { type: Array, required: true }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const ratesArray = computed(() => {
      var _a;
      return Array.isArray(props.rates) ? props.rates : ((_a = props.rates) == null ? void 0 : _a.data) ?? [];
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: `${unref(t)("currencyRates")} — ${__props.currency.code}`
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("currencyRates") + ": ")} <span class="font-semibold"${_scopeId2}>${ssrInterpolate(__props.currency.name)} (${ssrInterpolate(__props.currency.code)})</span>`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("currencyRates") + ": ") + " ", 1),
                    createVNode("span", { class: "font-semibold" }, toDisplayString(__props.currency.name) + " (" + toDisplayString(__props.currency.code) + ")", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("currencyRates") + ": ") + " ", 1),
                  createVNode("span", { class: "font-semibold" }, toDisplayString(__props.currency.name) + " (" + toDisplayString(__props.currency.code) + ")", 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              href: _ctx.route("admin.currencies.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`...`);
                } else {
                  return [
                    createTextVNode("...")
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("back"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              currency: __props.currency,
              rates: ratesArray.value,
              currencies: __props.currencies
            }, null, _parent2, _scopeId));
            _push2(`</div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-2" }, [
                    createVNode(_sfc_main$9, {
                      href: _ctx.route("admin.currencies.index")
                    }, {
                      icon: withCtx(() => [
                        createTextVNode("...")
                      ]),
                      default: withCtx(() => [
                        createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ]),
                  createVNode(_sfc_main$1, {
                    currency: __props.currency,
                    rates: ratesArray.value,
                    currencies: __props.currencies
                  }, null, 8, ["currency", "rates", "currencies"])
                ])
              ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Finance/CurrencyRates/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
