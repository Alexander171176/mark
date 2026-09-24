import { ref, computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, withDirectives, vModelSelect, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$g } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$c } from "./ClearMetaButton-zYsecbj9.js";
import { _ as _sfc_main$d } from "./MetatagsButton-CvAlOk1D.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$5 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$8 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$6 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$7 } from "./InputMoney-C7PGtYdB.js";
import { _ as _sfc_main$4 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$a } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$b } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$f } from "./MultiImageUpload-Bg2ahSyk.js";
import { _ as _sfc_main$e } from "./MultiImageEdit-BZZHL0n1.js";
import { _ as _sfc_main$9 } from "./TranslationTabs-czH7YSpu.js";
/* empty css                                                                      */
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
    subscriptionPlan: { type: Object, required: true },
    currencies: { type: Array, default: () => [] },
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] }
  },
  setup(__props) {
    var _a, _b;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const makeTranslation = () => ({
      title: "",
      subtitle: "",
      short: "",
      description: "",
      meta_title: "",
      meta_keywords: "",
      meta_desc: ""
    });
    const buildTranslations = () => {
      var _a2;
      const result = {};
      (props.subscriptionPlan.translations || []).forEach((translation) => {
        result[translation.locale] = {
          title: translation.title || "",
          subtitle: translation.subtitle || "",
          short: translation.short || "",
          description: translation.description || "",
          meta_title: translation.meta_title || "",
          meta_keywords: translation.meta_keywords || "",
          meta_desc: translation.meta_desc || ""
        };
      });
      const defaultLocale2 = props.currentLocale || ((_a2 = props.subscriptionPlan.translation) == null ? void 0 : _a2.locale) || props.availableLocales[0] || "ru";
      if (!Object.keys(result).length) {
        result[defaultLocale2] = makeTranslation();
      }
      if (!result[defaultLocale2]) {
        result[defaultLocale2] = makeTranslation();
      }
      return result;
    };
    const defaultLocale = props.currentLocale || ((_a = props.subscriptionPlan.translation) == null ? void 0 : _a.locale) || props.availableLocales[0] || "ru";
    const activeLocale = ref(defaultLocale);
    const formatDate = (value) => {
      if (!value) return "";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return "";
      return date.toISOString().split("T")[0];
    };
    const stringifyJson = (value) => {
      if (!value) return "";
      if (typeof value === "string") {
        return value;
      }
      try {
        return JSON.stringify(value, null, 2);
      } catch (e) {
        return "";
      }
    };
    const form = useForm({
      _method: "PUT",
      activity: Boolean(props.subscriptionPlan.activity),
      sort: props.subscriptionPlan.sort ?? 0,
      slug: props.subscriptionPlan.slug ?? "",
      published_at: formatDate(props.subscriptionPlan.published_at),
      available_from: formatDate(props.subscriptionPlan.available_from),
      available_until: formatDate(props.subscriptionPlan.available_until),
      billing_period: props.subscriptionPlan.billing_period ?? "month",
      interval: props.subscriptionPlan.interval ?? 1,
      currency_id: props.subscriptionPlan.currency_id ?? ((_b = props.subscriptionPlan.currency) == null ? void 0 : _b.id) ?? null,
      price: props.subscriptionPlan.price ?? "",
      trial_days: props.subscriptionPlan.trial_days ?? "",
      auto_renew: Boolean(props.subscriptionPlan.auto_renew),
      provider: props.subscriptionPlan.provider ?? "",
      provider_ref: props.subscriptionPlan.provider_ref ?? "",
      provider_payload: stringifyJson(props.subscriptionPlan.provider_payload),
      config: stringifyJson(props.subscriptionPlan.config),
      translations: buildTranslations(),
      deletedImages: []
    });
    const currentTranslation = computed(() => {
      if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation();
      }
      return form.translations[activeLocale.value];
    });
    const pageTitle = computed(() => {
      var _a2;
      return currentTranslation.value.title || ((_a2 = props.subscriptionPlan.translation) == null ? void 0 : _a2.title) || `ID: ${props.subscriptionPlan.id}`;
    });
    const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`];
    const dynamicOptionsLimit = (items) => {
      if (!items) return 10;
      return items.length + 10;
    };
    const currencyOptions = computed(
      () => (props.currencies || []).map((currency) => {
        const code = currency.code || `#${currency.id}`;
        const name = currency.name ? ` — ${currency.name}` : "";
        const symbol = currency.symbol ? ` (${currency.symbol})` : "";
        return {
          id: currency.id,
          label: `${code}${symbol}${name}`
        };
      })
    );
    const selectedCurrency = computed({
      get: () => currencyOptions.value.find(
        (item) => Number(item.id) === Number(form.currency_id)
      ) || null,
      set: (value) => {
        form.currency_id = (value == null ? void 0 : value.id) ?? null;
      }
    });
    const existingImages = ref(
      (props.subscriptionPlan.images || []).filter((image) => image.webp_url || image.url || image.image_url).map((image) => ({
        id: image.id,
        url: image.webp_url || image.url || image.image_url,
        order: image.order || 0,
        alt: image.alt || "",
        caption: image.caption || ""
      }))
    );
    const newImages = ref([]);
    const handleExistingImagesUpdate = (images) => {
      existingImages.value = images || [];
    };
    const handleDeleteExistingImage = (deletedId) => {
      if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId);
      }
      existingImages.value = existingImages.value.filter(
        (image) => image.id !== deletedId
      );
    };
    const handleNewImagesUpdate = (images) => {
      newImages.value = images || [];
    };
    const handleSlugFocus = () => {
      if (!form.slug && currentTranslation.value.title) {
        form.slug = transliterate(currentTranslation.value.title.toLowerCase());
      }
    };
    const truncateText = (text, maxLength, addEllipsis = false) => {
      if (!text) return "";
      if (text.length <= maxLength) return text;
      const cut = text.substr(0, text.lastIndexOf(" ", maxLength));
      return addEllipsis ? `${cut}...` : cut;
    };
    const stripHtml = (value) => {
      return String(value || "").replace(/(<([^>]+)>)/gi, "");
    };
    const clearMetaFields = () => {
      currentTranslation.value.meta_title = "";
      currentTranslation.value.meta_keywords = "";
      currentTranslation.value.meta_desc = "";
    };
    const generateMetaFields = () => {
      const translation = currentTranslation.value;
      if (translation.title && !translation.meta_title) {
        translation.meta_title = truncateText(translation.title, 160);
      }
      if (translation.short && !translation.meta_keywords) {
        let text = stripHtml(translation.short);
        text = text.replace(/[.,!?;:()[\]{}"'«»]/g, "");
        const words = text.split(/\s+/).filter((word) => word && word.length >= 3).map((word) => word.toLowerCase()).filter((value, index, self) => self.indexOf(value) === index);
        translation.meta_keywords = truncateText(words.join(", "), 255);
      }
      if (translation.short && !translation.meta_desc) {
        translation.meta_desc = truncateText(stripHtml(translation.short), 255, true);
      }
    };
    const toMoneyString = (value) => {
      if (value === null || typeof value === "undefined") return null;
      if (typeof value === "string") {
        const prepared = value.trim();
        if (prepared === "") return null;
        return prepared.replace(",", ".");
      }
      return String(value);
    };
    const toJsonOrNull = (value) => {
      if (value === "" || value === null || typeof value === "undefined") return null;
      if (typeof value === "object") return value;
      if (typeof value === "string") {
        const prepared = value.trim();
        if (!prepared) return null;
        try {
          return JSON.parse(prepared);
        } catch (e) {
          return prepared;
        }
      }
      return value;
    };
    const toDateOrNull = (value) => {
      return value ? String(value) : null;
    };
    const submitForm = () => {
      form.transform((data) => {
        const transformed = {
          ...data,
          activity: data.activity ? 1 : 0,
          auto_renew: data.auto_renew ? 1 : 0,
          sort: data.sort === "" || data.sort === null ? 0 : Number(data.sort),
          interval: data.interval === "" || data.interval === null ? 1 : Number(data.interval),
          trial_days: data.trial_days === "" || data.trial_days === null ? null : Number(data.trial_days),
          currency_id: data.currency_id === "" || data.currency_id === null ? null : Number(data.currency_id),
          published_at: toDateOrNull(data.published_at),
          available_from: toDateOrNull(data.available_from),
          available_until: toDateOrNull(data.available_until),
          price: toMoneyString(data.price),
          provider_payload: toJsonOrNull(data.provider_payload),
          config: toJsonOrNull(data.config)
        };
        delete transformed.images;
        delete transformed.deletedImages;
        let index = 0;
        existingImages.value.forEach((image) => {
          transformed[`images[${index}][id]`] = image.id;
          transformed[`images[${index}][order]`] = image.order ?? 0;
          transformed[`images[${index}][alt]`] = image.alt ?? "";
          transformed[`images[${index}][caption]`] = image.caption ?? "";
          index++;
        });
        newImages.value.forEach((image) => {
          transformed[`images[${index}][file]`] = image.file;
          transformed[`images[${index}][order]`] = image.order ?? 0;
          transformed[`images[${index}][alt]`] = image.alt ?? "";
          transformed[`images[${index}][caption]`] = image.caption ?? "";
          index++;
        });
        form.deletedImages.forEach((id, deletedIndex) => {
          transformed[`deletedImages[${deletedIndex}]`] = id;
        });
        return transformed;
      });
      form.post(route("admin.schoolSubscriptionPlans.update", {
        schoolSubscriptionPlan: props.subscriptionPlan.id
      }), {
        errorBag: "editSchoolSubscriptionPlan",
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
          toast.success("Тарифный план успешно обновлён!");
          newImages.value = [];
          form.deletedImages = [];
        },
        onError: (errors) => {
          console.error("Ошибка обновления тарифного плана:", errors);
          const firstKey = Object.keys(errors || {})[0];
          toast.error(errors[firstKey] || "Проверьте корректность полей.");
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editSubscriptionPlan")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editSubscriptionPlan"))}: ${ssrInterpolate(pageTitle.value)} [ID: ${ssrInterpolate(props.subscriptionPlan.id)}] `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editSubscriptionPlan")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(props.subscriptionPlan.id) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editSubscriptionPlan")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(props.subscriptionPlan.id) + "] ", 1)
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
              href: _ctx.route("admin.schoolSubscriptionPlans.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z" })
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
            _push2(`</div><form enctype="multipart/form-data" class="p-3 w-full"${_scopeId}><div class="pb-12"${_scopeId}><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
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
              message: unref(form).errors.activity
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "sort",
              value: unref(t)("sort"),
              class: "text-sm"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "sort",
              type: "number",
              min: "0",
              modelValue: unref(form).sort,
              "onUpdate:modelValue": ($event) => unref(form).sort = $event,
              modelModifiers: { number: true },
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 grid grid-cols-1 lg:grid-cols-4 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "currency_id" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}> * </span> ${ssrInterpolate(unref(t)("currency"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                    createTextVNode(" " + toDisplayString(unref(t)("currency")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "currency_id",
              modelValue: selectedCurrency.value,
              "onUpdate:modelValue": ($event) => selectedCurrency.value = $event,
              options: currencyOptions.value,
              "options-limit": dynamicOptionsLimit(currencyOptions.value),
              multiple: false,
              "close-on-select": true,
              "allow-empty": true,
              placeholder: unref(t)("select"),
              label: "label",
              "track-by": "id",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.currency_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "price" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}> * </span> ${ssrInterpolate(unref(t)("price"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                    createTextVNode(" " + toDisplayString(unref(t)("price")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
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
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "trial_days",
              value: unref(t)("trialDays")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "trial_days",
              type: "number",
              min: "0",
              modelValue: unref(form).trial_days,
              "onUpdate:modelValue": ($event) => unref(form).trial_days = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.trial_days
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2 mt-2 lg:mt-5"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).auto_renew,
              "onUpdate:modelValue": ($event) => unref(form).auto_renew = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "auto_renew",
              text: unref(t)("autoRenew"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.auto_renew
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "billing_period" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}> * </span> ${ssrInterpolate(unref(t)("billingPeriod"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                    createTextVNode(" " + toDisplayString(unref(t)("billingPeriod")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<select id="billing_period" class="block w-full py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${_scopeId}><option value="day"${ssrIncludeBooleanAttr(Array.isArray(unref(form).billing_period) ? ssrLooseContain(unref(form).billing_period, "day") : ssrLooseEqual(unref(form).billing_period, "day")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("days"))}</option><option value="week"${ssrIncludeBooleanAttr(Array.isArray(unref(form).billing_period) ? ssrLooseContain(unref(form).billing_period, "week") : ssrLooseEqual(unref(form).billing_period, "week")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("weeks"))}</option><option value="month"${ssrIncludeBooleanAttr(Array.isArray(unref(form).billing_period) ? ssrLooseContain(unref(form).billing_period, "month") : ssrLooseEqual(unref(form).billing_period, "month")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("months"))}</option><option value="year"${ssrIncludeBooleanAttr(Array.isArray(unref(form).billing_period) ? ssrLooseContain(unref(form).billing_period, "year") : ssrLooseEqual(unref(form).billing_period, "year")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("years"))}</option></select>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.billing_period
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "interval" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}> * </span> ${ssrInterpolate(unref(t)("intervalPeriod"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                    createTextVNode(" " + toDisplayString(unref(t)("intervalPeriod")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "interval",
              type: "number",
              min: "1",
              modelValue: unref(form).interval,
              "onUpdate:modelValue": ($event) => unref(form).interval = $event,
              modelModifiers: { number: true },
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.interval
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 grid grid-cols-1 lg:grid-cols-3 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "published_at",
              value: unref(t)("publishedAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "published_at",
              type: "date",
              modelValue: unref(form).published_at,
              "onUpdate:modelValue": ($event) => unref(form).published_at = $event,
              class: "w-full max-w-xs"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.published_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "available_from",
              value: unref(t)("shortStarted")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "available_from",
              type: "date",
              modelValue: unref(form).available_from,
              "onUpdate:modelValue": ($event) => unref(form).available_from = $event,
              class: "w-full max-w-xs"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.available_from
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "available_until",
              value: unref(t)("shortExpires")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "available_until",
              type: "date",
              modelValue: unref(form).available_until,
              "onUpdate:modelValue": ($event) => unref(form).available_until = $event,
              class: "w-full max-w-xs"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.available_until
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "slug" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("slug"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("slug")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "slug",
              type: "text",
              modelValue: unref(form).slug,
              "onUpdate:modelValue": ($event) => unref(form).slug = $event,
              autocomplete: "off",
              class: "w-full",
              required: "",
              onFocus: handleSlugFocus
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.slug
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-4 rounded-sm border border-slate-300 p-3 dark:border-slate-600"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              modelValue: activeLocale.value,
              "onUpdate:modelValue": ($event) => activeLocale.value = $event,
              translations: unref(form).translations,
              "available-locales": __props.availableLocales,
              "make-translation": makeTranslation,
              "onUpdate:translations": ($event) => unref(form).translations = $event,
              onRemoved: ($event) => unref(toast).warning("Перевод удалён."),
              onAdded: ($event) => unref(toast).success("Локаль добавлена.")
            }, null, _parent2, _scopeId));
            _push2(`<div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "title" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}> * </span> ${ssrInterpolate(unref(t)("title"))} [${ssrInterpolate(activeLocale.value.toUpperCase())}] `);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                    createTextVNode(" " + toDisplayString(unref(t)("title")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.title || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "title",
              type: "text",
              modelValue: currentTranslation.value.title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
              maxlength: "255",
              required: "",
              autocomplete: "off"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "subtitle",
              value: `${unref(t)("subtitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              modelValue: currentTranslation.value.subtitle,
              "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("subtitle")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "short",
              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              modelValue: currentTranslation.value.short,
              "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("short")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "description",
              value: `${unref(t)("description")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$b, {
              modelValue: currentTranslation.value.description,
              "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
              height: 500
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("description")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mt-4 border-t border-dashed border-slate-400 pt-4"${_scopeId}><div class="mb-3 flex items-center justify-end gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$c, {
              type: "button",
              onClick: clearMetaFields
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("clearMetaFields"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("clearMetaFields")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              type: "button",
              onClick: generateMetaFields
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-600 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M13 7h2v6a1 1 0 01-1 1H4v2l-4-3 4-3v2h9V7zM3 9H1V3a1 1 0 011-1h10V0l4 3-4 3V4H3v5z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-600 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M13 7h2v6a1 1 0 01-1 1H4v2l-4-3 4-3v2h9V7zM3 9H1V3a1 1 0 011-1h10V0l4 3-4 3V4H3v5z" })
                    ]))
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("generateMetaTags"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("generateMetaTags")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "meta_title",
              value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "meta_title",
              type: "text",
              modelValue: currentTranslation.value.meta_title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
              maxlength: "255",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("meta_title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "meta_keywords",
              value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              modelValue: currentTranslation.value.meta_keywords,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("meta_keywords")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "meta_desc",
              value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              modelValue: currentTranslation.value.meta_desc,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("meta_desc")
            }, null, _parent2, _scopeId));
            _push2(`</div></div></div><div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "provider",
              value: unref(t)("provider")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "provider",
              type: "text",
              modelValue: unref(form).provider,
              "onUpdate:modelValue": ($event) => unref(form).provider = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.provider
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "provider_ref",
              value: unref(t)("providerRef")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "provider_ref",
              type: "text",
              modelValue: unref(form).provider_ref,
              "onUpdate:modelValue": ($event) => unref(form).provider_ref = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.provider_ref
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "provider_payload",
              value: unref(t)("providerPayload")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              id: "provider_payload",
              modelValue: unref(form).provider_payload,
              "onUpdate:modelValue": ($event) => unref(form).provider_payload = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.provider_payload
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "config",
              value: unref(t)("config")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              id: "config",
              modelValue: unref(form).config,
              "onUpdate:modelValue": ($event) => unref(form).config = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.config
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$e, {
              images: existingImages.value,
              "onUpdate:images": handleExistingImagesUpdate,
              onDeleteImage: handleDeleteExistingImage
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$f, { "onUpdate:images": handleNewImagesUpdate }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.images
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="flex items-center justify-center gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolSubscriptionPlans.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z" })
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
            _push2(ssrRenderComponent(_sfc_main$g, {
              type: "submit",
              class: { "opacity-25": unref(form).processing },
              disabled: unref(form).processing
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("save"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("save")), 1)
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
                      href: _ctx.route("admin.schoolSubscriptionPlans.index")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z" })
                        ]))
                      ]),
                      default: withCtx(() => [
                        createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ]),
                  createVNode("form", {
                    onSubmit: withModifiers(submitForm, ["prevent"]),
                    enctype: "multipart/form-data",
                    class: "p-3 w-full"
                  }, [
                    createVNode("div", { class: "pb-12" }, [
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
                            message: unref(form).errors.activity
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode(_sfc_main$5, {
                            for: "sort",
                            value: unref(t)("sort"),
                            class: "text-sm"
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$6, {
                            id: "sort",
                            type: "number",
                            min: "0",
                            modelValue: unref(form).sort,
                            "onUpdate:modelValue": ($event) => unref(form).sort = $event,
                            modelModifiers: { number: true },
                            class: "w-full lg:w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.sort
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 grid grid-cols-1 lg:grid-cols-4 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, { for: "currency_id" }, {
                            default: withCtx(() => [
                              createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                              createTextVNode(" " + toDisplayString(unref(t)("currency")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(unref(VueMultiselect), {
                            id: "currency_id",
                            modelValue: selectedCurrency.value,
                            "onUpdate:modelValue": ($event) => selectedCurrency.value = $event,
                            options: currencyOptions.value,
                            "options-limit": dynamicOptionsLimit(currencyOptions.value),
                            multiple: false,
                            "close-on-select": true,
                            "allow-empty": true,
                            placeholder: unref(t)("select"),
                            label: "label",
                            "track-by": "id",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: unref(form).errors.currency_id
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, { for: "price" }, {
                            default: withCtx(() => [
                              createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                              createTextVNode(" " + toDisplayString(unref(t)("price")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$7, {
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
                          createVNode(_sfc_main$5, {
                            for: "trial_days",
                            value: unref(t)("trialDays")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$6, {
                            id: "trial_days",
                            type: "number",
                            min: "0",
                            modelValue: unref(form).trial_days,
                            "onUpdate:modelValue": ($event) => unref(form).trial_days = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: unref(form).errors.trial_days
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-row items-center gap-2 mt-2 lg:mt-5" }, [
                          createVNode(_sfc_main$2, {
                            modelValue: unref(form).auto_renew,
                            "onUpdate:modelValue": ($event) => unref(form).auto_renew = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            for: "auto_renew",
                            text: unref(t)("autoRenew"),
                            class: "text-sm h-8 flex items-center"
                          }, null, 8, ["text"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.auto_renew
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, { for: "billing_period" }, {
                            default: withCtx(() => [
                              createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                              createTextVNode(" " + toDisplayString(unref(t)("billingPeriod")), 1)
                            ]),
                            _: 1
                          }),
                          withDirectives(createVNode("select", {
                            id: "billing_period",
                            "onUpdate:modelValue": ($event) => unref(form).billing_period = $event,
                            class: "block w-full py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"
                          }, [
                            createVNode("option", { value: "day" }, toDisplayString(unref(t)("days")), 1),
                            createVNode("option", { value: "week" }, toDisplayString(unref(t)("weeks")), 1),
                            createVNode("option", { value: "month" }, toDisplayString(unref(t)("months")), 1),
                            createVNode("option", { value: "year" }, toDisplayString(unref(t)("years")), 1)
                          ], 8, ["onUpdate:modelValue"]), [
                            [vModelSelect, unref(form).billing_period]
                          ]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: unref(form).errors.billing_period
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, { for: "interval" }, {
                            default: withCtx(() => [
                              createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                              createTextVNode(" " + toDisplayString(unref(t)("intervalPeriod")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$6, {
                            id: "interval",
                            type: "number",
                            min: "1",
                            modelValue: unref(form).interval,
                            "onUpdate:modelValue": ($event) => unref(form).interval = $event,
                            modelModifiers: { number: true },
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: unref(form).errors.interval
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 grid grid-cols-1 lg:grid-cols-3 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "published_at",
                            value: unref(t)("publishedAt")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$8, {
                            id: "published_at",
                            type: "date",
                            modelValue: unref(form).published_at,
                            "onUpdate:modelValue": ($event) => unref(form).published_at = $event,
                            class: "w-full max-w-xs"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: unref(form).errors.published_at
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "available_from",
                            value: unref(t)("shortStarted")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$8, {
                            id: "available_from",
                            type: "date",
                            modelValue: unref(form).available_from,
                            "onUpdate:modelValue": ($event) => unref(form).available_from = $event,
                            class: "w-full max-w-xs"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: unref(form).errors.available_from
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "available_until",
                            value: unref(t)("shortExpires")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$8, {
                            id: "available_until",
                            type: "date",
                            modelValue: unref(form).available_until,
                            "onUpdate:modelValue": ($event) => unref(form).available_until = $event,
                            class: "w-full max-w-xs"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: unref(form).errors.available_until
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, { for: "slug" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("slug")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$8, {
                          id: "slug",
                          type: "text",
                          modelValue: unref(form).slug,
                          "onUpdate:modelValue": ($event) => unref(form).slug = $event,
                          autocomplete: "off",
                          class: "w-full",
                          required: "",
                          onFocus: handleSlugFocus
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.slug
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-4 rounded-sm border border-slate-300 p-3 dark:border-slate-600" }, [
                        createVNode(_sfc_main$9, {
                          modelValue: activeLocale.value,
                          "onUpdate:modelValue": ($event) => activeLocale.value = $event,
                          translations: unref(form).translations,
                          "available-locales": __props.availableLocales,
                          "make-translation": makeTranslation,
                          "onUpdate:translations": ($event) => unref(form).translations = $event,
                          onRemoved: ($event) => unref(toast).warning("Перевод удалён."),
                          onAdded: ($event) => unref(toast).success("Локаль добавлена.")
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "translations", "available-locales", "onUpdate:translations", "onRemoved", "onAdded"]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode("div", { class: "flex justify-between w-full" }, [
                            createVNode(_sfc_main$5, { for: "title" }, {
                              default: withCtx(() => [
                                createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                                createTextVNode(" " + toDisplayString(unref(t)("title")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                              ]),
                              _: 1
                            }),
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.title || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$8, {
                            id: "title",
                            type: "text",
                            modelValue: currentTranslation.value.title,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
                            maxlength: "255",
                            required: "",
                            autocomplete: "off"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getError("title")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "subtitle",
                            value: `${unref(t)("subtitle")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$a, {
                            modelValue: currentTranslation.value.subtitle,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getError("subtitle")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "short",
                            value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$a, {
                            modelValue: currentTranslation.value.short,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getError("short")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "description",
                            value: `${unref(t)("description")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$b, {
                            modelValue: currentTranslation.value.description,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
                            height: 500
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getError("description")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mt-4 border-t border-dashed border-slate-400 pt-4" }, [
                          createVNode("div", { class: "mb-3 flex items-center justify-end gap-2" }, [
                            createVNode(_sfc_main$c, {
                              type: "button",
                              onClick: clearMetaFields
                            }, {
                              default: withCtx(() => [
                                createTextVNode(toDisplayString(unref(t)("clearMetaFields")), 1)
                              ]),
                              _: 1
                            }),
                            createVNode(_sfc_main$d, {
                              type: "button",
                              onClick: generateMetaFields
                            }, {
                              icon: withCtx(() => [
                                (openBlock(), createBlock("svg", {
                                  class: "w-4 h-4 fill-current text-slate-600 shrink-0 mr-2",
                                  viewBox: "0 0 16 16"
                                }, [
                                  createVNode("path", { d: "M13 7h2v6a1 1 0 01-1 1H4v2l-4-3 4-3v2h9V7zM3 9H1V3a1 1 0 011-1h10V0l4 3-4 3V4H3v5z" })
                                ]))
                              ]),
                              default: withCtx(() => [
                                createTextVNode(" " + toDisplayString(unref(t)("generateMetaTags")), 1)
                              ]),
                              _: 1
                            })
                          ]),
                          createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                            createVNode(_sfc_main$5, {
                              for: "meta_title",
                              value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
                            }, null, 8, ["value"]),
                            createVNode(_sfc_main$8, {
                              id: "meta_title",
                              type: "text",
                              modelValue: currentTranslation.value.meta_title,
                              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
                              maxlength: "255",
                              class: "w-full"
                            }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                            createVNode(_sfc_main$4, {
                              class: "mt-2",
                              message: getError("meta_title")
                            }, null, 8, ["message"])
                          ]),
                          createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                            createVNode(_sfc_main$5, {
                              for: "meta_keywords",
                              value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
                            }, null, 8, ["value"]),
                            createVNode(_sfc_main$a, {
                              modelValue: currentTranslation.value.meta_keywords,
                              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
                              class: "w-full"
                            }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                            createVNode(_sfc_main$4, {
                              class: "mt-2",
                              message: getError("meta_keywords")
                            }, null, 8, ["message"])
                          ]),
                          createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                            createVNode(_sfc_main$5, {
                              for: "meta_desc",
                              value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
                            }, null, 8, ["value"]),
                            createVNode(_sfc_main$a, {
                              modelValue: currentTranslation.value.meta_desc,
                              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
                              class: "w-full"
                            }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                            createVNode(_sfc_main$4, {
                              class: "mt-2",
                              message: getError("meta_desc")
                            }, null, 8, ["message"])
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "provider",
                            value: unref(t)("provider")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$8, {
                            id: "provider",
                            type: "text",
                            modelValue: unref(form).provider,
                            "onUpdate:modelValue": ($event) => unref(form).provider = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: unref(form).errors.provider
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "provider_ref",
                            value: unref(t)("providerRef")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$8, {
                            id: "provider_ref",
                            type: "text",
                            modelValue: unref(form).provider_ref,
                            "onUpdate:modelValue": ($event) => unref(form).provider_ref = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: unref(form).errors.provider_ref
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "provider_payload",
                            value: unref(t)("providerPayload")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$a, {
                            id: "provider_payload",
                            modelValue: unref(form).provider_payload,
                            "onUpdate:modelValue": ($event) => unref(form).provider_payload = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: unref(form).errors.provider_payload
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "config",
                            value: unref(t)("config")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$a, {
                            id: "config",
                            modelValue: unref(form).config,
                            "onUpdate:modelValue": ($event) => unref(form).config = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: unref(form).errors.config
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mt-4" }, [
                        createVNode(_sfc_main$e, {
                          images: existingImages.value,
                          "onUpdate:images": handleExistingImagesUpdate,
                          onDeleteImage: handleDeleteExistingImage
                        }, null, 8, ["images"])
                      ]),
                      createVNode("div", { class: "mt-4" }, [
                        createVNode(_sfc_main$f, { "onUpdate:images": handleNewImagesUpdate }),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.images
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center gap-3" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.schoolSubscriptionPlans.index")
                      }, {
                        icon: withCtx(() => [
                          (openBlock(), createBlock("svg", {
                            class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                            viewBox: "0 0 16 16"
                          }, [
                            createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z" })
                          ]))
                        ]),
                        default: withCtx(() => [
                          createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                        ]),
                        _: 1
                      }, 8, ["href"]),
                      createVNode(_sfc_main$g, {
                        type: "submit",
                        class: { "opacity-25": unref(form).processing },
                        disabled: unref(form).processing
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(unref(t)("save")), 1)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolSubscriptionPlans/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
