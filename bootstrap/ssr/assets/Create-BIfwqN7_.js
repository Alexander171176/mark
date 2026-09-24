import { computed, ref, watch, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, createCommentVNode, Fragment, renderList, withDirectives, vModelSelect, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$g } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$b } from "./MetatagsButton-CvAlOk1D.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$6 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$c } from "./MarketProductPriceInput-BXoog2u-.js";
import { _ as _sfc_main$d } from "./MarketProductMeasureInput-CLzWFBJh.js";
import { _ as _sfc_main$5 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$7 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$4 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$a } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$9 } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$8 } from "./TranslationTabs-czH7YSpu.js";
import { _ as _sfc_main$f } from "./MultiImageUpload-Bg2ahSyk.js";
import { _ as _sfc_main$e } from "./MultiImagePresetUpload-Dhpb09Vk.js";
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
import "vue-advanced-cropper";
import "./vendor-V_Tb0Wa1.js";
const _sfc_main = {
  __name: "Create",
  __ssrInlineRender: true,
  props: {
    imageProcessorEnabled: {
      type: Boolean,
      default: true
    },
    imagePreset: {
      type: Object,
      default: null
    },
    currentLocale: {
      type: String,
      default: ""
    },
    availableLocales: {
      type: Array,
      default: () => []
    },
    products: {
      type: [Array, Object],
      default: () => []
    },
    currencies: {
      type: [Array, Object],
      default: () => []
    },
    attributes: {
      type: [Array, Object],
      default: () => []
    },
    selectedProductId: {
      type: [Number, String, null],
      default: null
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    function resourceList(value) {
      if (Array.isArray(value)) {
        return value;
      }
      if (Array.isArray(value == null ? void 0 : value.data)) {
        return value.data;
      }
      return [];
    }
    function translationTitle(item) {
      var _a;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || (item == null ? void 0 : item.code) || `ID: ${item == null ? void 0 : item.id}`;
    }
    function makeTranslation() {
      return {
        title: "",
        subtitle: "",
        short: "",
        description: "",
        meta_title: "",
        meta_keywords: "",
        meta_desc: ""
      };
    }
    function nullableNumber(value, digits = 2) {
      if (value === "" || value === null || typeof value === "undefined") {
        return null;
      }
      const number = Number(value);
      return Number.isFinite(number) ? Number(number.toFixed(digits)) : null;
    }
    function truncateText(text, maxLength, addEllipsis = false) {
      if (!text) {
        return "";
      }
      const value = String(text);
      if (value.length <= maxLength) {
        return value;
      }
      const lastSpaceIndex = value.lastIndexOf(" ", maxLength);
      const truncated = lastSpaceIndex === -1 ? value.substring(0, maxLength) : value.substring(0, lastSpaceIndex);
      return addEllipsis ? `${truncated}...` : truncated;
    }
    const productList = computed(() => resourceList(props.products));
    const currencyList = computed(() => resourceList(props.currencies));
    const attributeList = computed(() => resourceList(props.attributes));
    const productOptions = computed(() => {
      return productList.value.map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`
      }));
    });
    const currencyOptions = computed(() => {
      return currencyList.value.map((item) => ({
        ...item,
        label: `[${item.code}] ${item.name || item.title || item.code}${item.symbol ? ` — ${item.symbol}` : ""}`
      }));
    });
    const attributeOptions = computed(() => {
      return attributeList.value.map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`
      }));
    });
    const defaultLocale = props.currentLocale || "ru";
    const activeLocale = ref(defaultLocale);
    const selectedAttributeValues = ref({});
    const newImages = ref([]);
    const form = useForm({
      market_product_id: props.selectedProductId ? Number(props.selectedProductId) : null,
      currency_id: null,
      code: "",
      sku: "",
      vendor_code: "",
      barcode: "",
      price: null,
      old_price: null,
      purchase_price: null,
      wholesale_price: null,
      wholesale_min_quantity: null,
      quantity: 0,
      in_stock: false,
      weight: null,
      length: null,
      width: null,
      height: null,
      sort: 0,
      activity: false,
      status: "draft",
      moderation_status: 0,
      moderated_by: null,
      moderated_at: null,
      moderation_note: null,
      published_at: "",
      show_from_at: "",
      show_to_at: "",
      values: [],
      images: [],
      translations: {
        [defaultLocale]: makeTranslation()
      }
    });
    const selectedProduct = computed({
      get() {
        if (!form.market_product_id) {
          return null;
        }
        return productOptions.value.find(
          (item) => Number(item.id) === Number(form.market_product_id)
        ) || null;
      },
      set(value) {
        form.market_product_id = (value == null ? void 0 : value.id) ? Number(value.id) : null;
      }
    });
    const selectedCurrency = computed({
      get() {
        if (!form.currency_id) {
          return null;
        }
        return currencyOptions.value.find(
          (item) => Number(item.id) === Number(form.currency_id)
        ) || null;
      },
      set(value) {
        form.currency_id = (value == null ? void 0 : value.id) ? Number(value.id) : null;
      }
    });
    function attributeValues(attribute) {
      var _a;
      if (Array.isArray(attribute == null ? void 0 : attribute.values)) {
        return attribute.values;
      }
      if (Array.isArray((_a = attribute == null ? void 0 : attribute.values) == null ? void 0 : _a.data)) {
        return attribute.values.data;
      }
      return [];
    }
    function valueOptions(attribute) {
      return attributeValues(attribute).map((value) => ({
        ...value,
        label: `[ID: ${value.id}] ${translationTitle(value)}`
      }));
    }
    function syncVariantValues() {
      form.values = attributeOptions.value.map((attribute, index) => {
        const selectedValue = selectedAttributeValues.value[attribute.id];
        if (!(selectedValue == null ? void 0 : selectedValue.id)) {
          return null;
        }
        return {
          market_attribute_id: Number(attribute.id),
          market_attribute_value_id: Number(selectedValue.id),
          sort: index
        };
      }).filter(Boolean);
    }
    function updateAttributeValue(attribute, value) {
      selectedAttributeValues.value = {
        ...selectedAttributeValues.value,
        [attribute.id]: value || null
      };
      syncVariantValues();
    }
    function clearVariantValues() {
      selectedAttributeValues.value = {};
      form.values = [];
    }
    watch(
      () => form.market_product_id,
      (newValue, oldValue) => {
        if (oldValue && Number(newValue) !== Number(oldValue)) {
          clearVariantValues();
        }
      }
    );
    const currentTranslation = computed(() => {
      if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation();
      }
      return form.translations[activeLocale.value];
    });
    function getTranslationError(key) {
      return form.errors[`translations.${activeLocale.value}.${key}`];
    }
    function handleCodeInputFocus() {
      if (!form.code && currentTranslation.value.title) {
        form.code = transliterate(
          currentTranslation.value.title.toLowerCase()
        );
      }
    }
    function generateMetaFields() {
      const translation = currentTranslation.value;
      if (translation.title && !translation.meta_title) {
        translation.meta_title = truncateText(
          translation.title,
          255
        );
      }
      if (!translation.meta_keywords && translation.short) {
        let text = String(translation.short).replace(/(<([^>]+)>)/gi, "");
        text = text.replace(/[.,!?;:()[\]{}"'«»]/g, "");
        const words = text.split(/\s+/).filter((word) => word && word.length >= 3).map((word) => word.toLowerCase()).filter(
          (value, index, values) => values.indexOf(value) === index
        );
        translation.meta_keywords = truncateText(
          words.join(", "),
          255
        );
      }
      if (translation.short && !translation.meta_desc) {
        const description = String(translation.short).replace(/(<([^>]+)>)/gi, "");
        translation.meta_desc = truncateText(
          description,
          255,
          true
        );
      }
    }
    const selectedProductCurrency = computed(() => {
      var _a, _b, _c;
      if (!((_a = selectedProduct.value) == null ? void 0 : _a.currency_id)) {
        return ((_b = selectedProduct.value) == null ? void 0 : _b.currency) || null;
      }
      return currencyOptions.value.find(
        (item) => Number(item.id) === Number(selectedProduct.value.currency_id)
      ) || ((_c = selectedProduct.value) == null ? void 0 : _c.currency) || null;
    });
    const effectiveCurrencyPreview = computed(() => {
      return selectedCurrency.value || selectedProductCurrency.value;
    });
    const productPrice = computed(
      () => {
        var _a;
        return ((_a = selectedProduct.value) == null ? void 0 : _a.price) ?? null;
      }
    );
    const productOldPrice = computed(
      () => {
        var _a;
        return ((_a = selectedProduct.value) == null ? void 0 : _a.old_price) ?? null;
      }
    );
    const productPurchasePrice = computed(
      () => {
        var _a;
        return ((_a = selectedProduct.value) == null ? void 0 : _a.purchase_price) ?? null;
      }
    );
    const productWholesalePrice = computed(
      () => {
        var _a;
        return ((_a = selectedProduct.value) == null ? void 0 : _a.wholesale_price) ?? null;
      }
    );
    const productWholesaleMinQuantity = computed(
      () => {
        var _a;
        return ((_a = selectedProduct.value) == null ? void 0 : _a.wholesale_min_quantity) ?? null;
      }
    );
    const productWeight = computed(
      () => {
        var _a;
        return ((_a = selectedProduct.value) == null ? void 0 : _a.weight) ?? null;
      }
    );
    const productLength = computed(
      () => {
        var _a;
        return ((_a = selectedProduct.value) == null ? void 0 : _a.length) ?? null;
      }
    );
    const productWidth = computed(
      () => {
        var _a;
        return ((_a = selectedProduct.value) == null ? void 0 : _a.width) ?? null;
      }
    );
    const productHeight = computed(
      () => {
        var _a;
        return ((_a = selectedProduct.value) == null ? void 0 : _a.height) ?? null;
      }
    );
    const galleryPreset = computed(() => props.imagePreset);
    function handleNewImagesUpdate(images) {
      const normalizedImages = (images || []).map(
        (image, index) => ({
          ...image,
          order: index
        })
      );
      newImages.value = normalizedImages;
      form.images = normalizedImages;
    }
    function submitForm() {
      syncVariantValues();
      form.transform((data) => {
        const transformed = {
          ...data,
          market_product_id: data.market_product_id ? Number(data.market_product_id) : null,
          currency_id: data.currency_id ? Number(data.currency_id) : null,
          price: nullableNumber(data.price, 2),
          old_price: nullableNumber(data.old_price, 2),
          purchase_price: nullableNumber(
            data.purchase_price,
            2
          ),
          wholesale_price: nullableNumber(
            data.wholesale_price,
            2
          ),
          wholesale_min_quantity: data.wholesale_min_quantity === "" || data.wholesale_min_quantity === null ? null : Number(data.wholesale_min_quantity),
          quantity: Number(data.quantity || 0),
          in_stock: data.in_stock ? 1 : 0,
          weight: nullableNumber(data.weight, 3),
          length: nullableNumber(data.length, 2),
          width: nullableNumber(data.width, 2),
          height: nullableNumber(data.height, 2),
          sort: Number(data.sort || 0),
          activity: data.activity ? 1 : 0,
          moderation_status: Number(
            data.moderation_status || 0
          ),
          published_at: data.published_at || null,
          show_from_at: data.show_from_at || null,
          show_to_at: data.show_to_at || null,
          values: data.values.map((item, index) => ({
            market_attribute_id: Number(
              item.market_attribute_id
            ),
            market_attribute_value_id: Number(
              item.market_attribute_value_id
            ),
            sort: index
          }))
        };
        delete transformed.images;
        newImages.value.forEach((image, index) => {
          if (!image.file) {
            return;
          }
          transformed[`images[${index}][file]`] = image.file;
          transformed[`images[${index}][order]`] = image.order ?? index;
          transformed[`images[${index}][alt]`] = image.alt ?? "";
          transformed[`images[${index}][caption]`] = image.caption ?? "";
        });
        return transformed;
      });
      form.post(
        route("admin.marketProductVariants.store"),
        {
          forceFormData: true,
          errorBag: "createMarketProductVariant",
          preserveScroll: true,
          onSuccess: () => {
            toast.success(
              "Вариант товара успешно создан."
            );
            newImages.value = [];
            form.images = [];
          },
          onError: (errors) => {
            console.error(
              "Ошибка создания варианта товара:",
              errors
            );
            const firstKey = Object.keys(errors || {})[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || "Проверьте правильность заполнения полей."
            );
          }
        }
      );
    }
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("addMarketProductVariant")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addMarketProductVariant"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addMarketProductVariant")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("addMarketProductVariant")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b, _c, _d;
          if (_push2) {
            _push2(`<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.marketProductVariants.index")
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
            _push2(`</div><form${_scopeId}><div class="mb-4 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-col"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "activity",
              modelValue: unref(form).activity,
              "onUpdate:modelValue": ($event) => unref(form).activity = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "activity",
              text: unref(t)("activity"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
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
              modelValue: unref(form).sort,
              "onUpdate:modelValue": ($event) => unref(form).sort = $event,
              modelModifiers: { number: true },
              type: "number",
              min: "0",
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2 lg:mt-0",
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "code",
              value: unref(t)("code")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "code",
              modelValue: unref(form).code,
              "onUpdate:modelValue": ($event) => unref(form).code = $event,
              type: "text",
              maxlength: "255",
              onFocus: handleCodeInputFocus
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.code
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "sku",
              value: unref(t)("sku")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "sku",
              modelValue: unref(form).sku,
              "onUpdate:modelValue": ($event) => unref(form).sku = $event,
              type: "text",
              maxlength: "100"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.sku
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "vendor_code",
              value: unref(t)("vendorCode")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "vendor_code",
              modelValue: unref(form).vendor_code,
              "onUpdate:modelValue": ($event) => unref(form).vendor_code = $event,
              type: "text",
              maxlength: "100"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.vendor_code
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "barcode",
              value: unref(t)("barcode")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "barcode",
              modelValue: unref(form).barcode,
              "onUpdate:modelValue": ($event) => unref(form).barcode = $event,
              type: "text",
              maxlength: "100"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.barcode
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-4 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "market_product_id" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("marketProduct"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("marketProduct")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              modelValue: selectedProduct.value,
              "onUpdate:modelValue": ($event) => selectedProduct.value = $event,
              options: productOptions.value,
              label: "label",
              "track-by": "id",
              searchable: true,
              "allow-empty": false,
              "show-labels": false,
              placeholder: "Выберите родительский товар"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.market_product_id
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (selectedProduct.value) {
              _push2(`<div class="mb-5 rounded-sm border border-cyan-300 dark:border-cyan-700 bg-cyan-50 dark:bg-cyan-900/20 p-3"${_scopeId}><div class="text-sm font-semibold text-cyan-800 dark:text-cyan-200"${_scopeId}>${ssrInterpolate(translationTitle(selectedProduct.value))}</div><div class="mt-2 grid grid-cols-1 gap-2 text-xs text-slate-600 dark:text-slate-300 md:grid-cols-2 lg:grid-cols-4"${_scopeId}><div${_scopeId}>${ssrInterpolate(unref(t)("price"))}: ${ssrInterpolate(productPrice.value ?? "—")}</div><div${_scopeId}>${ssrInterpolate(unref(t)("currency"))}: ${ssrInterpolate(((_a = selectedProductCurrency.value) == null ? void 0 : _a.code) || "—")}</div><div${_scopeId}>${ssrInterpolate(unref(t)("quantity"))}: ${ssrInterpolate(selectedProduct.value.quantity ?? 0)}</div><div${_scopeId}>${ssrInterpolate(unref(t)("variants"))}: ${ssrInterpolate(selectedProduct.value.variants_count ?? 0)}</div></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              modelValue: activeLocale.value,
              "onUpdate:modelValue": ($event) => activeLocale.value = $event,
              translations: unref(form).translations,
              "available-locales": __props.availableLocales,
              "make-translation": makeTranslation,
              "onUpdate:translations": ($event) => unref(form).translations = $event,
              onRemoved: ($event) => unref(toast).warning(unref(t)("translationRemoved")),
              onAdded: ($event) => unref(toast).success(unref(t)("localeAdded"))
            }, null, _parent2, _scopeId));
            _push2(`<div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: `title-${activeLocale.value}`
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("title"))} [${ssrInterpolate(activeLocale.value.toUpperCase())}] `);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("title")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: `title-${activeLocale.value}`,
              modelValue: currentTranslation.value.title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
              type: "text",
              required: "",
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getTranslationError("title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: `subtitle-${activeLocale.value}`,
              value: `${unref(t)("subtitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: `subtitle-${activeLocale.value}`,
              modelValue: currentTranslation.value.subtitle,
              "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
              type: "text",
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getTranslationError("subtitle")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: `short-${activeLocale.value}`,
              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: `short-${activeLocale.value}`,
              modelValue: currentTranslation.value.short,
              "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
              type: "text",
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getTranslationError("short")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: `description-${activeLocale.value}`,
              value: `${unref(t)("description")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: `description-${activeLocale.value}`,
              modelValue: currentTranslation.value.description,
              "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getTranslationError("description")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mt-5 border-t border-slate-300 dark:border-slate-600 pt-4"${_scopeId}><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: `meta-title-${activeLocale.value}`,
              value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: `meta-title-${activeLocale.value}`,
              modelValue: currentTranslation.value.meta_title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
              type: "text",
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getTranslationError("meta_title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: `meta-keywords-${activeLocale.value}`,
              value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: `meta-keywords-${activeLocale.value}`,
              modelValue: currentTranslation.value.meta_keywords,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
              type: "text",
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getTranslationError("meta_keywords")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: `meta-desc-${activeLocale.value}`,
              value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              id: `meta-desc-${activeLocale.value}`,
              modelValue: currentTranslation.value.meta_desc,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getTranslationError("meta_desc")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex justify-end mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$b, { onClick: generateMetaFields }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("generateMetaTags"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("generateMetaTags")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></div></div><div class="mb-5 rounded-sm border border-fuchsia-300 dark:border-fuchsia-700 bg-white dark:bg-slate-800 p-3"${_scopeId}><div class="mb-3 text-sm font-semibold text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(unref(t)("attributes"))} <span class="text-red-500 dark:text-red-300"${_scopeId}>*</span></div>`);
            if (attributeOptions.value.length) {
              _push2(`<div class="grid grid-cols-1 gap-4 md:grid-cols-2"${_scopeId}><!--[-->`);
              ssrRenderList(attributeOptions.value, (attribute) => {
                _push2(`<div class="rounded-sm border border-slate-300 dark:border-slate-600 p-3"${_scopeId}>`);
                _push2(ssrRenderComponent(_sfc_main$5, {
                  for: `attribute-${attribute.id}`,
                  value: attribute.label
                }, null, _parent2, _scopeId));
                _push2(ssrRenderComponent(unref(VueMultiselect), {
                  "model-value": selectedAttributeValues.value[attribute.id] || null,
                  options: valueOptions(attribute),
                  label: "label",
                  "track-by": "id",
                  searchable: true,
                  "allow-empty": true,
                  "show-labels": false,
                  placeholder: "Выберите значение",
                  "onUpdate:modelValue": ($event) => updateAttributeValue(attribute, $event)
                }, null, _parent2, _scopeId));
                _push2(ssrRenderComponent(_sfc_main$4, {
                  class: "mt-2",
                  message: unref(form).errors[`values.${attributeOptions.value.findIndex((item) => item.id === attribute.id)}.market_attribute_value_id`]
                }, null, _parent2, _scopeId));
                _push2(`</div>`);
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<div class="text-sm text-slate-500 dark:text-slate-300"${_scopeId}> Нет характеристик, разрешённых для формирования вариантов. </div>`);
            }
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.values
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-4 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "currency_id",
              value: unref(t)("currency")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              modelValue: selectedCurrency.value,
              "onUpdate:modelValue": ($event) => selectedCurrency.value = $event,
              options: currencyOptions.value,
              label: "label",
              "track-by": "id",
              searchable: true,
              "allow-empty": true,
              "show-labels": false,
              placeholder: "Оставьте пустым для наследования валюты товара"
            }, null, _parent2, _scopeId));
            _push2(`<div class="mt-1 text-xs text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("currencyDesc"))}: ${ssrInterpolate(((_b = selectedProductCurrency.value) == null ? void 0 : _b.code) || "не выбрана")}. </div>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.currency_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-4 rounded-sm border border-emerald-300 dark:border-emerald-700 bg-white dark:bg-slate-800 p-3"${_scopeId}><div class="mb-3 text-sm font-semibold text-emerald-700 dark:text-emerald-300"${_scopeId}>${ssrInterpolate(unref(t)("optionPrices"))}</div><div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "old_price",
              value: unref(t)("compareAtPrice")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$c, {
              id: "old_price",
              modelValue: unref(form).old_price,
              "onUpdate:modelValue": ($event) => unref(form).old_price = $event,
              currency: effectiveCurrencyPreview.value,
              "fraction-digits": 2,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(`<div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("inherited"))}: ${ssrInterpolate(productOldPrice.value ?? "—")}</div>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.old_price
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "price",
              value: unref(t)("price")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$c, {
              id: "price",
              modelValue: unref(form).price,
              "onUpdate:modelValue": ($event) => unref(form).price = $event,
              currency: effectiveCurrencyPreview.value,
              "fraction-digits": 2,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(`<div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("inherited"))}: ${ssrInterpolate(productPrice.value ?? "—")}</div>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.price
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "purchase_price",
              value: unref(t)("purchasePrice")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$c, {
              id: "purchase_price",
              modelValue: unref(form).purchase_price,
              "onUpdate:modelValue": ($event) => unref(form).purchase_price = $event,
              currency: effectiveCurrencyPreview.value,
              "fraction-digits": 2,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(`<div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("inherited"))}: ${ssrInterpolate(productPurchasePrice.value ?? "—")}</div>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.purchase_price
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 gap-4 md:grid-cols-2"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "wholesale_price",
              value: unref(t)("wholesalePrice")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$c, {
              id: "wholesale_price",
              modelValue: unref(form).wholesale_price,
              "onUpdate:modelValue": ($event) => unref(form).wholesale_price = $event,
              currency: effectiveCurrencyPreview.value,
              "fraction-digits": 2,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(`<div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("inherited"))}: ${ssrInterpolate(productWholesalePrice.value ?? "—")}</div>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.wholesale_price
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "wholesale_min_quantity",
              value: unref(t)("wholesaleMinQuantity")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "wholesale_min_quantity",
              modelValue: unref(form).wholesale_min_quantity,
              "onUpdate:modelValue": ($event) => unref(form).wholesale_min_quantity = $event,
              modelModifiers: { number: true },
              type: "number",
              min: "1",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(`<div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("inherited"))}: ${ssrInterpolate(productWholesaleMinQuantity.value ?? "—")}</div>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.wholesale_min_quantity
            }, null, _parent2, _scopeId));
            _push2(`</div></div></div><div class="mb-4 grid grid-cols-1 gap-4 md:grid-cols-2"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "quantity",
              value: unref(t)("quantity")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "quantity",
              modelValue: unref(form).quantity,
              "onUpdate:modelValue": ($event) => unref(form).quantity = $event,
              modelModifiers: { number: true },
              type: "number",
              min: "0",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.quantity
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col justify-end"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "in_stock",
              modelValue: unref(form).in_stock,
              "onUpdate:modelValue": ($event) => unref(form).in_stock = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "in_stock",
              text: unref(t)("inStock"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.in_stock
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-4 rounded-sm border border-violet-300 dark:border-violet-700 bg-white dark:bg-slate-800 p-3"${_scopeId}><div class="mb-3 text-sm font-semibold text-violet-700 dark:text-violet-300"${_scopeId}>${ssrInterpolate(unref(t)("variantDimensions"))}</div><div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "weight",
              value: unref(t)("weight")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              id: "weight",
              modelValue: unref(form).weight,
              "onUpdate:modelValue": ($event) => unref(form).weight = $event,
              "fraction-digits": 3,
              unit: "кг"
            }, null, _parent2, _scopeId));
            _push2(`<div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("inherited"))}: ${ssrInterpolate(productWeight.value ?? "—")}</div>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.weight
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "length",
              value: unref(t)("length")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              id: "length",
              modelValue: unref(form).length,
              "onUpdate:modelValue": ($event) => unref(form).length = $event,
              "fraction-digits": 2,
              unit: "см"
            }, null, _parent2, _scopeId));
            _push2(`<div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("inherited"))}: ${ssrInterpolate(productLength.value ?? "—")}</div>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.length
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "width",
              value: unref(t)("width")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              id: "width",
              modelValue: unref(form).width,
              "onUpdate:modelValue": ($event) => unref(form).width = $event,
              "fraction-digits": 2,
              unit: "см"
            }, null, _parent2, _scopeId));
            _push2(`<div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("inherited"))}: ${ssrInterpolate(productWidth.value ?? "—")}</div>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.width
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "height",
              value: unref(t)("height")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              id: "height",
              modelValue: unref(form).height,
              "onUpdate:modelValue": ($event) => unref(form).height = $event,
              "fraction-digits": 2,
              unit: "см"
            }, null, _parent2, _scopeId));
            _push2(`<div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("inherited"))}: ${ssrInterpolate(productHeight.value ?? "—")}</div>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.height
            }, null, _parent2, _scopeId));
            _push2(`</div></div></div><div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "status",
              value: unref(t)("status")
            }, null, _parent2, _scopeId));
            _push2(`<select id="status" class="w-full px-2 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"${_scopeId}><option value="draft"${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, "draft") : ssrLooseEqual(unref(form).status, "draft")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="published"${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, "published") : ssrLooseEqual(unref(form).status, "published")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="archived"${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, "archived") : ssrLooseEqual(unref(form).status, "archived")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("statusArchived"))}</option></select>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.status
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "published_at",
              value: unref(t)("publishedAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "published_at",
              modelValue: unref(form).published_at,
              "onUpdate:modelValue": ($event) => unref(form).published_at = $event,
              type: "datetime-local"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.published_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "show_from_at",
              value: unref(t)("showFromAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "show_from_at",
              modelValue: unref(form).show_from_at,
              "onUpdate:modelValue": ($event) => unref(form).show_from_at = $event,
              type: "datetime-local"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.show_from_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "show_to_at",
              value: unref(t)("showToAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "show_to_at",
              modelValue: unref(form).show_to_at,
              "onUpdate:modelValue": ($event) => unref(form).show_to_at = $event,
              type: "datetime-local"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.show_to_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mt-4"${_scopeId}>`);
            if (__props.imageProcessorEnabled) {
              _push2(ssrRenderComponent(_sfc_main$e, {
                images: newImages.value,
                preset: galleryPreset.value,
                "onUpdate:images": handleNewImagesUpdate
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$f, {
                images: newImages.value,
                "onUpdate:images": handleNewImagesUpdate
              }, null, _parent2, _scopeId));
            }
            if (newImages.value.length) {
              _push2(`<div class="text-xs text-slate-600 dark:text-slate-300 mt-2"${_scopeId}>${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(newImages.value.length)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.images
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex items-center justify-center mt-6"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.marketProductVariants.index")
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
            _push2(ssrRenderComponent(_sfc_main$g, {
              class: ["ms-4 mb-0", { "opacity-25": unref(form).processing }],
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
                      href: _ctx.route("admin.marketProductVariants.index")
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
                    onSubmit: withModifiers(submitForm, ["prevent"])
                  }, [
                    createVNode("div", { class: "mb-4 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                      createVNode("div", { class: "flex flex-col" }, [
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode(_sfc_main$2, {
                            id: "activity",
                            modelValue: unref(form).activity,
                            "onUpdate:modelValue": ($event) => unref(form).activity = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            for: "activity",
                            text: unref(t)("activity"),
                            class: "text-sm h-8 flex items-center"
                          }, null, 8, ["text"])
                        ]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
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
                          modelValue: unref(form).sort,
                          "onUpdate:modelValue": ($event) => unref(form).sort = $event,
                          modelModifiers: { number: true },
                          type: "number",
                          min: "0",
                          class: "w-full lg:w-28"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2 lg:mt-0",
                          message: unref(form).errors.sort
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-4 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4" }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$5, {
                          for: "code",
                          value: unref(t)("code")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$7, {
                          id: "code",
                          modelValue: unref(form).code,
                          "onUpdate:modelValue": ($event) => unref(form).code = $event,
                          type: "text",
                          maxlength: "255",
                          onFocus: handleCodeInputFocus
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          message: unref(form).errors.code
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$5, {
                          for: "sku",
                          value: unref(t)("sku")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$7, {
                          id: "sku",
                          modelValue: unref(form).sku,
                          "onUpdate:modelValue": ($event) => unref(form).sku = $event,
                          type: "text",
                          maxlength: "100"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          message: unref(form).errors.sku
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$5, {
                          for: "vendor_code",
                          value: unref(t)("vendorCode")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$7, {
                          id: "vendor_code",
                          modelValue: unref(form).vendor_code,
                          "onUpdate:modelValue": ($event) => unref(form).vendor_code = $event,
                          type: "text",
                          maxlength: "100"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          message: unref(form).errors.vendor_code
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$5, {
                          for: "barcode",
                          value: unref(t)("barcode")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$7, {
                          id: "barcode",
                          modelValue: unref(form).barcode,
                          "onUpdate:modelValue": ($event) => unref(form).barcode = $event,
                          type: "text",
                          maxlength: "100"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          message: unref(form).errors.barcode
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-4 flex flex-col items-start" }, [
                      createVNode(_sfc_main$5, { for: "market_product_id" }, {
                        default: withCtx(() => [
                          createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                          createTextVNode(" " + toDisplayString(unref(t)("marketProduct")), 1)
                        ]),
                        _: 1
                      }),
                      createVNode(unref(VueMultiselect), {
                        modelValue: selectedProduct.value,
                        "onUpdate:modelValue": ($event) => selectedProduct.value = $event,
                        options: productOptions.value,
                        label: "label",
                        "track-by": "id",
                        searchable: true,
                        "allow-empty": false,
                        "show-labels": false,
                        placeholder: "Выберите родительский товар"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "options"]),
                      createVNode(_sfc_main$4, {
                        class: "mt-2",
                        message: unref(form).errors.market_product_id
                      }, null, 8, ["message"])
                    ]),
                    selectedProduct.value ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "mb-5 rounded-sm border border-cyan-300 dark:border-cyan-700 bg-cyan-50 dark:bg-cyan-900/20 p-3"
                    }, [
                      createVNode("div", { class: "text-sm font-semibold text-cyan-800 dark:text-cyan-200" }, toDisplayString(translationTitle(selectedProduct.value)), 1),
                      createVNode("div", { class: "mt-2 grid grid-cols-1 gap-2 text-xs text-slate-600 dark:text-slate-300 md:grid-cols-2 lg:grid-cols-4" }, [
                        createVNode("div", null, toDisplayString(unref(t)("price")) + ": " + toDisplayString(productPrice.value ?? "—"), 1),
                        createVNode("div", null, toDisplayString(unref(t)("currency")) + ": " + toDisplayString(((_c = selectedProductCurrency.value) == null ? void 0 : _c.code) || "—"), 1),
                        createVNode("div", null, toDisplayString(unref(t)("quantity")) + ": " + toDisplayString(selectedProduct.value.quantity ?? 0), 1),
                        createVNode("div", null, toDisplayString(unref(t)("variants")) + ": " + toDisplayString(selectedProduct.value.variants_count ?? 0), 1)
                      ])
                    ])) : createCommentVNode("", true),
                    createVNode("div", { class: "my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm" }, [
                      createVNode(_sfc_main$8, {
                        modelValue: activeLocale.value,
                        "onUpdate:modelValue": ($event) => activeLocale.value = $event,
                        translations: unref(form).translations,
                        "available-locales": __props.availableLocales,
                        "make-translation": makeTranslation,
                        "onUpdate:translations": ($event) => unref(form).translations = $event,
                        onRemoved: ($event) => unref(toast).warning(unref(t)("translationRemoved")),
                        onAdded: ($event) => unref(toast).success(unref(t)("localeAdded"))
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "translations", "available-locales", "onUpdate:translations", "onRemoved", "onAdded"]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, {
                          for: `title-${activeLocale.value}`
                        }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("title")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                          ]),
                          _: 1
                        }, 8, ["for"]),
                        createVNode(_sfc_main$7, {
                          id: `title-${activeLocale.value}`,
                          modelValue: currentTranslation.value.title,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
                          type: "text",
                          required: "",
                          maxlength: "255"
                        }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: getTranslationError("title")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, {
                          for: `subtitle-${activeLocale.value}`,
                          value: `${unref(t)("subtitle")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["for", "value"]),
                        createVNode(_sfc_main$7, {
                          id: `subtitle-${activeLocale.value}`,
                          modelValue: currentTranslation.value.subtitle,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
                          type: "text",
                          maxlength: "255"
                        }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: getTranslationError("subtitle")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, {
                          for: `short-${activeLocale.value}`,
                          value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["for", "value"]),
                        createVNode(_sfc_main$7, {
                          id: `short-${activeLocale.value}`,
                          modelValue: currentTranslation.value.short,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
                          type: "text",
                          maxlength: "255"
                        }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: getTranslationError("short")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, {
                          for: `description-${activeLocale.value}`,
                          value: `${unref(t)("description")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["for", "value"]),
                        createVNode(_sfc_main$9, {
                          id: `description-${activeLocale.value}`,
                          modelValue: currentTranslation.value.description,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event
                        }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: getTranslationError("description")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mt-5 border-t border-slate-300 dark:border-slate-600 pt-4" }, [
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: `meta-title-${activeLocale.value}`,
                            value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["for", "value"]),
                          createVNode(_sfc_main$7, {
                            id: `meta-title-${activeLocale.value}`,
                            modelValue: currentTranslation.value.meta_title,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
                            type: "text",
                            maxlength: "255"
                          }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getTranslationError("meta_title")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: `meta-keywords-${activeLocale.value}`,
                            value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["for", "value"]),
                          createVNode(_sfc_main$7, {
                            id: `meta-keywords-${activeLocale.value}`,
                            modelValue: currentTranslation.value.meta_keywords,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
                            type: "text",
                            maxlength: "255"
                          }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getTranslationError("meta_keywords")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: `meta-desc-${activeLocale.value}`,
                            value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["for", "value"]),
                          createVNode(_sfc_main$a, {
                            id: `meta-desc-${activeLocale.value}`,
                            modelValue: currentTranslation.value.meta_desc,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
                            class: "w-full"
                          }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getTranslationError("meta_desc")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex justify-end mt-4" }, [
                          createVNode(_sfc_main$b, {
                            onClick: withModifiers(generateMetaFields, ["prevent"])
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("generateMetaTags")), 1)
                            ]),
                            _: 1
                          })
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "mb-5 rounded-sm border border-fuchsia-300 dark:border-fuchsia-700 bg-white dark:bg-slate-800 p-3" }, [
                      createVNode("div", { class: "mb-3 text-sm font-semibold text-fuchsia-700 dark:text-fuchsia-300" }, [
                        createTextVNode(toDisplayString(unref(t)("attributes")) + " ", 1),
                        createVNode("span", { class: "text-red-500 dark:text-red-300" }, "*")
                      ]),
                      attributeOptions.value.length ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "grid grid-cols-1 gap-4 md:grid-cols-2"
                      }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(attributeOptions.value, (attribute) => {
                          return openBlock(), createBlock("div", {
                            key: attribute.id,
                            class: "rounded-sm border border-slate-300 dark:border-slate-600 p-3"
                          }, [
                            createVNode(_sfc_main$5, {
                              for: `attribute-${attribute.id}`,
                              value: attribute.label
                            }, null, 8, ["for", "value"]),
                            createVNode(unref(VueMultiselect), {
                              "model-value": selectedAttributeValues.value[attribute.id] || null,
                              options: valueOptions(attribute),
                              label: "label",
                              "track-by": "id",
                              searchable: true,
                              "allow-empty": true,
                              "show-labels": false,
                              placeholder: "Выберите значение",
                              "onUpdate:modelValue": ($event) => updateAttributeValue(attribute, $event)
                            }, null, 8, ["model-value", "options", "onUpdate:modelValue"]),
                            createVNode(_sfc_main$4, {
                              class: "mt-2",
                              message: unref(form).errors[`values.${attributeOptions.value.findIndex((item) => item.id === attribute.id)}.market_attribute_value_id`]
                            }, null, 8, ["message"])
                          ]);
                        }), 128))
                      ])) : (openBlock(), createBlock("div", {
                        key: 1,
                        class: "text-sm text-slate-500 dark:text-slate-300"
                      }, " Нет характеристик, разрешённых для формирования вариантов. ")),
                      createVNode(_sfc_main$4, {
                        class: "mt-2",
                        message: unref(form).errors.values
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "mb-4 flex flex-col items-start" }, [
                      createVNode(_sfc_main$5, {
                        for: "currency_id",
                        value: unref(t)("currency")
                      }, null, 8, ["value"]),
                      createVNode(unref(VueMultiselect), {
                        modelValue: selectedCurrency.value,
                        "onUpdate:modelValue": ($event) => selectedCurrency.value = $event,
                        options: currencyOptions.value,
                        label: "label",
                        "track-by": "id",
                        searchable: true,
                        "allow-empty": true,
                        "show-labels": false,
                        placeholder: "Оставьте пустым для наследования валюты товара"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "options"]),
                      createVNode("div", { class: "mt-1 text-xs text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("currencyDesc")) + ": " + toDisplayString(((_d = selectedProductCurrency.value) == null ? void 0 : _d.code) || "не выбрана") + ". ", 1),
                      createVNode(_sfc_main$4, {
                        class: "mt-2",
                        message: unref(form).errors.currency_id
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "mb-4 rounded-sm border border-emerald-300 dark:border-emerald-700 bg-white dark:bg-slate-800 p-3" }, [
                      createVNode("div", { class: "mb-3 text-sm font-semibold text-emerald-700 dark:text-emerald-300" }, toDisplayString(unref(t)("optionPrices")), 1),
                      createVNode("div", { class: "grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3" }, [
                        createVNode("div", null, [
                          createVNode(_sfc_main$5, {
                            for: "old_price",
                            value: unref(t)("compareAtPrice")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$c, {
                            id: "old_price",
                            modelValue: unref(form).old_price,
                            "onUpdate:modelValue": ($event) => unref(form).old_price = $event,
                            currency: effectiveCurrencyPreview.value,
                            "fraction-digits": 2,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "currency"]),
                          createVNode("div", { class: "mt-1 text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("inherited")) + ": " + toDisplayString(productOldPrice.value ?? "—"), 1),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.old_price
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", null, [
                          createVNode(_sfc_main$5, {
                            for: "price",
                            value: unref(t)("price")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$c, {
                            id: "price",
                            modelValue: unref(form).price,
                            "onUpdate:modelValue": ($event) => unref(form).price = $event,
                            currency: effectiveCurrencyPreview.value,
                            "fraction-digits": 2,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "currency"]),
                          createVNode("div", { class: "mt-1 text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("inherited")) + ": " + toDisplayString(productPrice.value ?? "—"), 1),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.price
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", null, [
                          createVNode(_sfc_main$5, {
                            for: "purchase_price",
                            value: unref(t)("purchasePrice")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$c, {
                            id: "purchase_price",
                            modelValue: unref(form).purchase_price,
                            "onUpdate:modelValue": ($event) => unref(form).purchase_price = $event,
                            currency: effectiveCurrencyPreview.value,
                            "fraction-digits": 2,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "currency"]),
                          createVNode("div", { class: "mt-1 text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("inherited")) + ": " + toDisplayString(productPurchasePrice.value ?? "—"), 1),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.purchase_price
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "grid grid-cols-1 gap-4 md:grid-cols-2" }, [
                        createVNode("div", null, [
                          createVNode(_sfc_main$5, {
                            for: "wholesale_price",
                            value: unref(t)("wholesalePrice")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$c, {
                            id: "wholesale_price",
                            modelValue: unref(form).wholesale_price,
                            "onUpdate:modelValue": ($event) => unref(form).wholesale_price = $event,
                            currency: effectiveCurrencyPreview.value,
                            "fraction-digits": 2,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "currency"]),
                          createVNode("div", { class: "mt-1 text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("inherited")) + ": " + toDisplayString(productWholesalePrice.value ?? "—"), 1),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.wholesale_price
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", null, [
                          createVNode(_sfc_main$5, {
                            for: "wholesale_min_quantity",
                            value: unref(t)("wholesaleMinQuantity")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$6, {
                            id: "wholesale_min_quantity",
                            modelValue: unref(form).wholesale_min_quantity,
                            "onUpdate:modelValue": ($event) => unref(form).wholesale_min_quantity = $event,
                            modelModifiers: { number: true },
                            type: "number",
                            min: "1",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode("div", { class: "mt-1 text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("inherited")) + ": " + toDisplayString(productWholesaleMinQuantity.value ?? "—"), 1),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.wholesale_min_quantity
                          }, null, 8, ["message"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "mb-4 grid grid-cols-1 gap-4 md:grid-cols-2" }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$5, {
                          for: "quantity",
                          value: unref(t)("quantity")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$6, {
                          id: "quantity",
                          modelValue: unref(form).quantity,
                          "onUpdate:modelValue": ($event) => unref(form).quantity = $event,
                          modelModifiers: { number: true },
                          type: "number",
                          min: "0",
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          message: unref(form).errors.quantity
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col justify-end" }, [
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode(_sfc_main$2, {
                            id: "in_stock",
                            modelValue: unref(form).in_stock,
                            "onUpdate:modelValue": ($event) => unref(form).in_stock = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            for: "in_stock",
                            text: unref(t)("inStock"),
                            class: "text-sm h-8 flex items-center"
                          }, null, 8, ["text"])
                        ]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.in_stock
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-4 rounded-sm border border-violet-300 dark:border-violet-700 bg-white dark:bg-slate-800 p-3" }, [
                      createVNode("div", { class: "mb-3 text-sm font-semibold text-violet-700 dark:text-violet-300" }, toDisplayString(unref(t)("variantDimensions")), 1),
                      createVNode("div", { class: "grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4" }, [
                        createVNode("div", null, [
                          createVNode(_sfc_main$5, {
                            for: "weight",
                            value: unref(t)("weight")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$d, {
                            id: "weight",
                            modelValue: unref(form).weight,
                            "onUpdate:modelValue": ($event) => unref(form).weight = $event,
                            "fraction-digits": 3,
                            unit: "кг"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode("div", { class: "mt-1 text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("inherited")) + ": " + toDisplayString(productWeight.value ?? "—"), 1),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.weight
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", null, [
                          createVNode(_sfc_main$5, {
                            for: "length",
                            value: unref(t)("length")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$d, {
                            id: "length",
                            modelValue: unref(form).length,
                            "onUpdate:modelValue": ($event) => unref(form).length = $event,
                            "fraction-digits": 2,
                            unit: "см"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode("div", { class: "mt-1 text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("inherited")) + ": " + toDisplayString(productLength.value ?? "—"), 1),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.length
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", null, [
                          createVNode(_sfc_main$5, {
                            for: "width",
                            value: unref(t)("width")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$d, {
                            id: "width",
                            modelValue: unref(form).width,
                            "onUpdate:modelValue": ($event) => unref(form).width = $event,
                            "fraction-digits": 2,
                            unit: "см"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode("div", { class: "mt-1 text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("inherited")) + ": " + toDisplayString(productWidth.value ?? "—"), 1),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.width
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", null, [
                          createVNode(_sfc_main$5, {
                            for: "height",
                            value: unref(t)("height")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$d, {
                            id: "height",
                            modelValue: unref(form).height,
                            "onUpdate:modelValue": ($event) => unref(form).height = $event,
                            "fraction-digits": 2,
                            unit: "см"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode("div", { class: "mt-1 text-[10px] text-slate-500 dark:text-slate-300" }, toDisplayString(unref(t)("inherited")) + ": " + toDisplayString(productHeight.value ?? "—"), 1),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.height
                          }, null, 8, ["message"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, {
                          for: "status",
                          value: unref(t)("status")
                        }, null, 8, ["value"]),
                        withDirectives(createVNode("select", {
                          id: "status",
                          "onUpdate:modelValue": ($event) => unref(form).status = $event,
                          class: "w-full px-2 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"
                        }, [
                          createVNode("option", { value: "draft" }, toDisplayString(unref(t)("statusDraft")), 1),
                          createVNode("option", { value: "published" }, toDisplayString(unref(t)("statusPublished")), 1),
                          createVNode("option", { value: "archived" }, toDisplayString(unref(t)("statusArchived")), 1)
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).status]
                        ]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.status
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, {
                          for: "published_at",
                          value: unref(t)("publishedAt")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$7, {
                          id: "published_at",
                          modelValue: unref(form).published_at,
                          "onUpdate:modelValue": ($event) => unref(form).published_at = $event,
                          type: "datetime-local"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.published_at
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, {
                          for: "show_from_at",
                          value: unref(t)("showFromAt")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$7, {
                          id: "show_from_at",
                          modelValue: unref(form).show_from_at,
                          "onUpdate:modelValue": ($event) => unref(form).show_from_at = $event,
                          type: "datetime-local"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.show_from_at
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, {
                          for: "show_to_at",
                          value: unref(t)("showToAt")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$7, {
                          id: "show_to_at",
                          modelValue: unref(form).show_to_at,
                          "onUpdate:modelValue": ($event) => unref(form).show_to_at = $event,
                          type: "datetime-local"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.show_to_at
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mt-4" }, [
                      __props.imageProcessorEnabled ? (openBlock(), createBlock(_sfc_main$e, {
                        key: 0,
                        images: newImages.value,
                        preset: galleryPreset.value,
                        "onUpdate:images": handleNewImagesUpdate
                      }, null, 8, ["images", "preset"])) : (openBlock(), createBlock(_sfc_main$f, {
                        key: 1,
                        images: newImages.value,
                        "onUpdate:images": handleNewImagesUpdate
                      }, null, 8, ["images"])),
                      newImages.value.length ? (openBlock(), createBlock("div", {
                        key: 2,
                        class: "text-xs text-slate-600 dark:text-slate-300 mt-2"
                      }, toDisplayString(unref(t)("images")) + ": " + toDisplayString(newImages.value.length), 1)) : createCommentVNode("", true),
                      createVNode(_sfc_main$4, {
                        class: "mt-2",
                        message: unref(form).errors.images
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center mt-6" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.marketProductVariants.index")
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
                      createVNode(_sfc_main$g, {
                        class: ["ms-4 mb-0", { "opacity-25": unref(form).processing }],
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketProductVariants/Create.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
