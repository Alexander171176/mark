import { computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$a } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$6 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$8 } from "./InputMoney-C7PGtYdB.js";
import { _ as _sfc_main$5 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$9 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$4 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$7 } from "./SelectEntity-DUeUgJcQ.js";
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
const _sfc_main = {
  __name: "Edit",
  __ssrInlineRender: true,
  props: {
    currentLocale: {
      type: String,
      default: ""
    },
    price: {
      type: Object,
      required: true
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
    var _a, _b;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const isoToLocalInput = (iso) => {
      if (!iso) {
        return "";
      }
      const date = new Date(iso);
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      const pad = (number) => String(number).padStart(2, "0");
      return [
        `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`,
        `${pad(date.getHours())}:${pad(date.getMinutes())}`
      ].join("T");
    };
    const form = useForm({
      _method: "PUT",
      school_bundle_id: props.price.school_bundle_id ?? ((_a = props.price.bundle) == null ? void 0 : _a.id) ?? null,
      currency_id: props.price.currency_id ?? ((_b = props.price.currency) == null ? void 0 : _b.id) ?? null,
      price: props.price.price ?? "",
      sale_price: props.price.sale_price ?? "",
      compare_at_price: props.price.compare_at_price ?? "",
      starts_at: isoToLocalInput(
        props.price.starts_at
      ),
      ends_at: isoToLocalInput(
        props.price.ends_at
      ),
      activity: Boolean(
        props.price.activity
      ),
      sort: props.price.sort ?? 0,
      meta: props.price.meta ?? null
    });
    const getBundleTitle = (bundle) => {
      var _a2;
      return ((_a2 = bundle == null ? void 0 : bundle.translation) == null ? void 0 : _a2.title) || (bundle == null ? void 0 : bundle.slug) || `#${bundle == null ? void 0 : bundle.id}`;
    };
    const bundleOptions = computed(
      () => props.bundles.map((bundle) => ({
        id: bundle.id,
        label: [
          `[ID: ${bundle.id}]`,
          getBundleTitle(bundle),
          bundle.slug ? `(${bundle.slug})` : null
        ].filter(Boolean).join(" ")
      }))
    );
    const currencyOptions = computed(
      () => props.currencies.map((currency) => ({
        id: currency.id,
        label: [
          currency.code || `#${currency.id}`,
          currency.name ? `— ${currency.name}` : null,
          currency.symbol ? `(${currency.symbol})` : null
        ].filter(Boolean).join(" ")
      }))
    );
    const pageTitle = computed(() => {
      return props.price.bundle ? getBundleTitle(props.price.bundle) : `ID: ${props.price.id}`;
    });
    const toNullableId = (value) => {
      if (value === null || value === void 0 || value === "") {
        return null;
      }
      const number = Number(value);
      return Number.isFinite(number) ? number : null;
    };
    const toMoneyString = (value) => {
      if (value === null || value === void 0) {
        return null;
      }
      const stringValue = String(value).trim();
      if (stringValue === "") {
        return null;
      }
      return stringValue.replace(",", ".");
    };
    const toDateTimeStringOrNull = (value) => {
      if (!value) {
        return null;
      }
      return String(value);
    };
    const toSort = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const submitForm = () => {
      form.transform((data) => ({
        ...data,
        school_bundle_id: toNullableId(data.school_bundle_id),
        currency_id: toNullableId(data.currency_id),
        price: toMoneyString(data.price),
        sale_price: toMoneyString(data.sale_price),
        compare_at_price: toMoneyString(data.compare_at_price),
        starts_at: toDateTimeStringOrNull(data.starts_at),
        ends_at: toDateTimeStringOrNull(data.ends_at),
        activity: Boolean(data.activity),
        sort: toSort(data.sort),
        meta: data.meta
      }));
      form.post(route("admin.schoolBundlePrices.update", {
        schoolBundlePrice: props.price.id
      }), {
        preserveScroll: true,
        onSuccess: () => {
          toast.success("Цена набора курсов успешно обновлена.");
        },
        onError: (errors) => {
          const firstKey = Object.keys(errors || {})[0];
          toast.error(
            (errors == null ? void 0 : errors[firstKey]) || "Проверьте правильность заполнения полей."
          );
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editBundlePrice")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editBundlePrice"))} - ${ssrInterpolate(pageTitle.value)} [ID: ${ssrInterpolate(__props.price.id)}] `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editBundlePrice")) + " - " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(__props.price.id) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editBundlePrice")) + " - " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(__props.price.id) + "] ", 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolBundlePrices.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                    ]))
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
            _push2(`</div><form class="p-3 w-full space-y-4"${_scopeId}><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).activity,
              "onUpdate:modelValue": ($event) => unref(form).activity = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "activity",
              text: unref(t)("activity"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2 lg:mt-0",
              message: unref(form).errors.activity
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}><div class="h-8 flex items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "sort",
              value: unref(t)("sort"),
              class: "text-sm"
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "sort",
              modelValue: unref(form).sort,
              "onUpdate:modelValue": ($event) => unref(form).sort = $event,
              type: "number",
              min: "0",
              autocomplete: "sort",
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2 lg:mt-0",
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-2 gap-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "school_bundle_id",
              modelValue: unref(form).school_bundle_id,
              "onUpdate:modelValue": ($event) => unref(form).school_bundle_id = $event,
              label: unref(t)("bundle"),
              required: true,
              options: bundleOptions.value,
              "error-message": unref(form).errors.school_bundle_id,
              placeholder: unref(t)("select")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "currency_id",
              modelValue: unref(form).currency_id,
              "onUpdate:modelValue": ($event) => unref(form).currency_id = $event,
              label: unref(t)("currency"),
              required: true,
              options: currencyOptions.value,
              "error-message": unref(form).errors.currency_id,
              placeholder: unref(t)("select")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="grid grid-cols-1 lg:grid-cols-3 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "price" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("price"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("price")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "price",
              modelValue: unref(form).price,
              "onUpdate:modelValue": ($event) => unref(form).price = $event,
              min: 0,
              step: 0.01,
              "fraction-digits": 2,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.price
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "sale_price" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("salePrice"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("salePrice")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "sale_price",
              modelValue: unref(form).sale_price,
              "onUpdate:modelValue": ($event) => unref(form).sale_price = $event,
              min: 0,
              step: 0.01,
              "fraction-digits": 2,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.sale_price
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "compare_at_price" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("compareAtPrice"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("compareAtPrice")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "compare_at_price",
              modelValue: unref(form).compare_at_price,
              "onUpdate:modelValue": ($event) => unref(form).compare_at_price = $event,
              min: 0,
              step: 0.01,
              "fraction-digits": 2,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.compare_at_price
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-2 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "starts_at" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("priceStartsAt"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("priceStartsAt")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "starts_at",
              modelValue: unref(form).starts_at,
              "onUpdate:modelValue": ($event) => unref(form).starts_at = $event,
              type: "datetime-local",
              autocomplete: "off",
              class: "w-full max-w-xs"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.starts_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start lg:items-end"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "ends_at" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("priceEndsAt"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("priceEndsAt")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "ends_at",
              modelValue: unref(form).ends_at,
              "onUpdate:modelValue": ($event) => unref(form).ends_at = $event,
              type: "datetime-local",
              autocomplete: "off",
              class: "w-full max-w-xs"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.ends_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="flex items-center justify-center mt-4 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolBundlePrices.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                    ]))
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
            _push2(ssrRenderComponent(_sfc_main$a, {
              class: ["mb-0", { "opacity-25": unref(form).processing }],
              disabled: unref(form).processing
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100" viewBox="0 0 16 16"${_scopeId2}><path d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z" })
                    ]))
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("save"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("save")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></form></div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-2" }, [
                    createVNode(_sfc_main$1, {
                      href: _ctx.route("admin.schoolBundlePrices.index")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                        ]))
                      ]),
                      default: withCtx(() => [
                        createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ]),
                  createVNode("form", {
                    class: "p-3 w-full space-y-4",
                    onSubmit: withModifiers(submitForm, ["prevent"])
                  }, [
                    createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          modelValue: unref(form).activity,
                          "onUpdate:modelValue": ($event) => unref(form).activity = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "activity",
                          text: unref(t)("activity"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2 lg:mt-0",
                          message: unref(form).errors.activity
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode("div", { class: "h-8 flex items-center" }, [
                          createVNode(_sfc_main$5, {
                            for: "sort",
                            value: unref(t)("sort"),
                            class: "text-sm"
                          }, null, 8, ["value"])
                        ]),
                        createVNode(_sfc_main$6, {
                          id: "sort",
                          modelValue: unref(form).sort,
                          "onUpdate:modelValue": ($event) => unref(form).sort = $event,
                          type: "number",
                          min: "0",
                          autocomplete: "sort",
                          class: "w-full lg:w-28"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2 lg:mt-0",
                          message: unref(form).errors.sort
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4" }, [
                      createVNode(_sfc_main$7, {
                        id: "school_bundle_id",
                        modelValue: unref(form).school_bundle_id,
                        "onUpdate:modelValue": ($event) => unref(form).school_bundle_id = $event,
                        label: unref(t)("bundle"),
                        required: true,
                        options: bundleOptions.value,
                        "error-message": unref(form).errors.school_bundle_id,
                        placeholder: unref(t)("select")
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "label", "options", "error-message", "placeholder"]),
                      createVNode(_sfc_main$7, {
                        id: "currency_id",
                        modelValue: unref(form).currency_id,
                        "onUpdate:modelValue": ($event) => unref(form).currency_id = $event,
                        label: unref(t)("currency"),
                        required: true,
                        options: currencyOptions.value,
                        "error-message": unref(form).errors.currency_id,
                        placeholder: unref(t)("select")
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "label", "options", "error-message", "placeholder"])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-3 gap-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, { for: "price" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("price")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$8, {
                          id: "price",
                          modelValue: unref(form).price,
                          "onUpdate:modelValue": ($event) => unref(form).price = $event,
                          min: 0,
                          step: 0.01,
                          "fraction-digits": 2,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.price
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, { for: "sale_price" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("salePrice")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$8, {
                          id: "sale_price",
                          modelValue: unref(form).sale_price,
                          "onUpdate:modelValue": ($event) => unref(form).sale_price = $event,
                          min: 0,
                          step: 0.01,
                          "fraction-digits": 2,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.sale_price
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, { for: "compare_at_price" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("compareAtPrice")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$8, {
                          id: "compare_at_price",
                          modelValue: unref(form).compare_at_price,
                          "onUpdate:modelValue": ($event) => unref(form).compare_at_price = $event,
                          min: 0,
                          step: 0.01,
                          "fraction-digits": 2,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.compare_at_price
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, { for: "starts_at" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("priceStartsAt")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$9, {
                          id: "starts_at",
                          modelValue: unref(form).starts_at,
                          "onUpdate:modelValue": ($event) => unref(form).starts_at = $event,
                          type: "datetime-local",
                          autocomplete: "off",
                          class: "w-full max-w-xs"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.starts_at
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start lg:items-end" }, [
                        createVNode(_sfc_main$5, { for: "ends_at" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("priceEndsAt")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$9, {
                          id: "ends_at",
                          modelValue: unref(form).ends_at,
                          "onUpdate:modelValue": ($event) => unref(form).ends_at = $event,
                          type: "datetime-local",
                          autocomplete: "off",
                          class: "w-full max-w-xs"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.ends_at
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center mt-4 gap-3" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.schoolBundlePrices.index")
                      }, {
                        icon: withCtx(() => [
                          (openBlock(), createBlock("svg", {
                            class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                            viewBox: "0 0 16 16"
                          }, [
                            createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                          ]))
                        ]),
                        default: withCtx(() => [
                          createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                        ]),
                        _: 1
                      }, 8, ["href"]),
                      createVNode(_sfc_main$a, {
                        class: ["mb-0", { "opacity-25": unref(form).processing }],
                        disabled: unref(form).processing
                      }, {
                        icon: withCtx(() => [
                          (openBlock(), createBlock("svg", {
                            class: "w-4 h-4 fill-current text-slate-100",
                            viewBox: "0 0 16 16"
                          }, [
                            createVNode("path", { d: "M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z" })
                          ]))
                        ]),
                        default: withCtx(() => [
                          createTextVNode(" " + toDisplayString(unref(t)("save")), 1)
                        ]),
                        _: 1
                      }, 8, ["class", "disabled"])
                    ])
                  ], 32)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolBundlePrices/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
