import { computed, ref, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, withDirectives, vModelSelect, createCommentVNode, Fragment, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$l } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$c } from "./MetatagsButton-CvAlOk1D.js";
import { _ as _sfc_main$b } from "./ClearMetaButton-zYsecbj9.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$5 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$d } from "./MarketProductPriceInput-BXoog2u-.js";
import { _ as _sfc_main$e } from "./MarketProductMeasureInput-CLzWFBJh.js";
import { _ as _sfc_main$f, a as _sfc_main$g } from "./MarketProductRelatedProductsField-B_rQVmSc.js";
import { _ as _sfc_main$4 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$8 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$6 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$9 } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$a } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$7 } from "./TranslationTabs-czH7YSpu.js";
import { _ as _sfc_main$j } from "./MultiImageEdit-BZZHL0n1.js";
import { _ as _sfc_main$k } from "./MultiImageUpload-Bg2ahSyk.js";
import { _ as _sfc_main$h } from "./MultiImagePresetEdit-aX3-dCXJ.js";
import { _ as _sfc_main$i } from "./MultiImagePresetUpload-Dhpb09Vk.js";
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
import "./InputDecimalExt-B0XQGDJe.js";
import "vue-advanced-cropper";
import "./vendor-V_Tb0Wa1.js";
const _sfc_main = {
  __name: "Edit",
  __ssrInlineRender: true,
  props: {
    product: { type: Object, required: true },
    imageProcessorEnabled: { type: Boolean, default: true },
    imagePreset: { type: Object, default: null },
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    companies: { type: [Array, Object], default: () => [] },
    shops: { type: [Array, Object], default: () => [] },
    brands: { type: [Array, Object], default: () => [] },
    currencies: { type: [Array, Object], default: () => [] },
    categories: { type: [Array, Object], default: () => [] },
    tags: { type: [Array, Object], default: () => [] },
    attributes: { type: [Array, Object], default: () => [] },
    relatedProducts: { type: [Array, Object], default: () => [] },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const resourceList = (value) => {
      if (Array.isArray(value)) {
        return value;
      }
      if (Array.isArray(value == null ? void 0 : value.data)) {
        return value.data;
      }
      return [];
    };
    const product = computed(() => {
      var _a;
      return ((_a = props.product) == null ? void 0 : _a.data) || props.product || {};
    });
    const companyList = computed(() => resourceList(props.companies));
    const shopList = computed(() => resourceList(props.shops));
    const brandList = computed(() => resourceList(props.brands));
    const currencyList = computed(() => resourceList(props.currencies));
    const categoryList = computed(() => resourceList(props.categories));
    const tagList = computed(() => resourceList(props.tags));
    const attributeList = computed(() => resourceList(props.attributes));
    const relatedProductList = computed(() => resourceList(props.relatedProducts));
    const makeTranslation = () => ({
      title: "",
      subtitle: "",
      short: "",
      description: "",
      meta_title: "",
      meta_keywords: "",
      meta_desc: ""
    });
    const normalizeTranslations = (translations) => {
      const result = {};
      resourceList(translations).forEach((translation) => {
        if (!(translation == null ? void 0 : translation.locale)) {
          return;
        }
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
      if (!Object.keys(result).length) {
        result[props.currentLocale || "ru"] = makeTranslation();
      }
      return result;
    };
    const initialTranslations = normalizeTranslations(product.value.translations);
    const defaultLocale = initialTranslations[props.currentLocale] ? props.currentLocale : Object.keys(initialTranslations)[0] || "ru";
    const activeLocale = ref(defaultLocale);
    const currentTranslation = computed(() => {
      if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation();
      }
      return form.translations[activeLocale.value];
    });
    const getError = (key) => {
      return form.errors[`translations.${activeLocale.value}.${key}`];
    };
    const normalizeNullableInput = (value) => {
      return value === null || value === void 0 ? "" : value;
    };
    const toDateTimeLocal = (value) => {
      if (!value) {
        return "";
      }
      const stringValue = String(value);
      if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/.test(stringValue)) {
        return stringValue.slice(0, 16);
      }
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return stringValue.slice(0, 16);
      }
      const pad = (number) => String(number).padStart(2, "0");
      return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
    };
    const initialCategories = resourceList(product.value.categories).map(
      (category, index) => {
        var _a, _b;
        return {
          id: category.id,
          main: Boolean((_a = category == null ? void 0 : category.pivot) == null ? void 0 : _a.main),
          order: Number(((_b = category == null ? void 0 : category.pivot) == null ? void 0 : _b.order) ?? index)
        };
      }
    );
    const initialTags = resourceList(product.value.tags).map(
      (tag, index) => {
        var _a;
        return {
          id: tag.id,
          order: Number(((_a = tag == null ? void 0 : tag.pivot) == null ? void 0 : _a.order) ?? index)
        };
      }
    );
    const initialRelatedProducts = resourceList(product.value.related_products).map((item, index) => {
      var _a, _b, _c;
      return {
        id: item.id,
        type: ((_a = item == null ? void 0 : item.pivot) == null ? void 0 : _a.type) ?? (item == null ? void 0 : item.type) ?? "related",
        order: Number(((_b = item == null ? void 0 : item.pivot) == null ? void 0 : _b.order) ?? (item == null ? void 0 : item.order) ?? index),
        activity: Boolean(((_c = item == null ? void 0 : item.pivot) == null ? void 0 : _c.activity) ?? (item == null ? void 0 : item.activity) ?? true)
      };
    });
    const initialAttributeValues = resourceList(product.value.attribute_values).map((item, index) => {
      var _a, _b;
      return {
        id: (item == null ? void 0 : item.id) ?? null,
        market_attribute_id: (item == null ? void 0 : item.market_attribute_id) ?? ((_a = item == null ? void 0 : item.attribute) == null ? void 0 : _a.id) ?? null,
        market_attribute_value_id: (item == null ? void 0 : item.market_attribute_value_id) ?? ((_b = item == null ? void 0 : item.attribute_value) == null ? void 0 : _b.id) ?? null,
        value_string: (item == null ? void 0 : item.value_string) ?? null,
        value_number: (item == null ? void 0 : item.value_number) ?? null,
        value_boolean: (item == null ? void 0 : item.value_boolean) === null || typeof (item == null ? void 0 : item.value_boolean) === "undefined" ? null : Boolean(item.value_boolean),
        value_date: (item == null ? void 0 : item.value_date) ?? null,
        value_json: (item == null ? void 0 : item.value_json) ?? null,
        unit: (item == null ? void 0 : item.unit) ?? null,
        order: Number((item == null ? void 0 : item.order) ?? index),
        activity: typeof (item == null ? void 0 : item.activity) === "undefined" ? true : Boolean(item.activity)
      };
    });
    const existingImages = ref(
      resourceList(product.value.images).map((image, index) => {
        var _a;
        return {
          id: Number(image.id),
          url: image.webp_url || image.image_url || image.thumb_url || image.url || "",
          order: Number(image.order ?? ((_a = image.pivot) == null ? void 0 : _a.order) ?? index),
          alt: image.alt || "",
          caption: image.caption || ""
        };
      }).filter((image) => image.id && image.url).sort((a, b) => a.order - b.order)
    );
    const newImages = ref([]);
    const form = useForm({
      user_id: product.value.user_id ?? null,
      market_company_id: product.value.market_company_id ?? null,
      market_shop_id: product.value.market_shop_id ?? null,
      market_brand_id: product.value.market_brand_id ?? null,
      currency_id: product.value.currency_id ?? null,
      url: product.value.url || "",
      sku: product.value.sku || "",
      vendor_code: product.value.vendor_code || "",
      barcode: product.value.barcode || "",
      price: normalizeNullableInput(product.value.price),
      old_price: normalizeNullableInput(product.value.old_price),
      purchase_price: normalizeNullableInput(product.value.purchase_price),
      wholesale_price: normalizeNullableInput(product.value.wholesale_price),
      wholesale_min_quantity: normalizeNullableInput(
        product.value.wholesale_min_quantity
      ),
      quantity: Number(product.value.quantity ?? 0),
      in_stock: Boolean(product.value.in_stock),
      weight: normalizeNullableInput(product.value.weight),
      length: normalizeNullableInput(product.value.length),
      width: normalizeNullableInput(product.value.width),
      height: normalizeNullableInput(product.value.height),
      sort: Number(product.value.sort ?? 0),
      activity: Boolean(product.value.activity),
      left: Boolean(product.value.left),
      main: Boolean(product.value.main),
      right: Boolean(product.value.right),
      is_new: Boolean(product.value.is_new),
      is_hit: Boolean(product.value.is_hit),
      is_sale: Boolean(product.value.is_sale),
      status: product.value.status || "draft",
      published_at: toDateTimeLocal(product.value.published_at),
      show_from_at: toDateTimeLocal(product.value.show_from_at),
      show_to_at: toDateTimeLocal(product.value.show_to_at),
      views: Number(product.value.views ?? 0),
      likes_count: Number(product.value.likes_count ?? 0),
      rating_avg: Number(product.value.rating_avg ?? 0),
      rating_count: Number(product.value.rating_count ?? 0),
      categories: initialCategories,
      tags: initialTags,
      related_products: initialRelatedProducts,
      attribute_values: initialAttributeValues,
      images: [],
      deletedImages: [],
      translations: initialTranslations
    });
    const translationTitle = (item) => {
      var _a;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || (item == null ? void 0 : item.legal_name) || (item == null ? void 0 : item.name) || (item == null ? void 0 : item.url) || `ID: ${item == null ? void 0 : item.id}`;
    };
    const companyOptions = computed(() => {
      return companyList.value.map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`
      }));
    });
    const filteredShopList = computed(() => {
      if (!form.market_company_id) {
        return shopList.value;
      }
      return shopList.value.filter((item) => {
        return Number(item == null ? void 0 : item.market_company_id) === Number(form.market_company_id);
      });
    });
    const shopOptions = computed(() => {
      return filteredShopList.value.map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`
      }));
    });
    const brandOptions = computed(() => {
      return brandList.value.map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`
      }));
    });
    const currencyOptions = computed(() => {
      return currencyList.value.map((item) => ({
        ...item,
        label: `[${item.code}] ${item.name}${item.symbol ? ` — ${item.symbol}` : ""}`
      }));
    });
    const categoryOptions = computed(() => {
      return categoryList.value.map((item) => {
        const level = Number((item == null ? void 0 : item.level) || 1);
        const prefix = level > 1 ? "— ".repeat(level - 1) : "";
        return {
          ...item,
          label: `[ID: ${item.id}] ${prefix}${translationTitle(item)}`
        };
      });
    });
    const tagOptions = computed(() => {
      return tagList.value.map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`
      }));
    });
    const dynamicOptionsLimit = (items) => {
      return Array.isArray(items) ? items.length + 10 : 10;
    };
    const selectedCompany = computed({
      get: () => {
        return companyOptions.value.find((item) => {
          return Number(item.id) === Number(form.market_company_id);
        }) || null;
      },
      set: (value) => {
        const nextCompanyId = (value == null ? void 0 : value.id) ?? null;
        form.market_company_id = nextCompanyId;
        const currentShop = shopList.value.find((item) => {
          return Number(item.id) === Number(form.market_shop_id);
        });
        if (currentShop && Number(currentShop.market_company_id) !== Number(nextCompanyId)) {
          form.market_shop_id = null;
        }
      }
    });
    const selectedShop = computed({
      get: () => {
        return shopOptions.value.find((item) => {
          return Number(item.id) === Number(form.market_shop_id);
        }) || null;
      },
      set: (value) => {
        form.market_shop_id = (value == null ? void 0 : value.id) ?? null;
        if (value == null ? void 0 : value.market_company_id) {
          form.market_company_id = Number(value.market_company_id);
        }
      }
    });
    const selectedBrand = computed({
      get: () => {
        return brandOptions.value.find((item) => {
          return Number(item.id) === Number(form.market_brand_id);
        }) || null;
      },
      set: (value) => {
        form.market_brand_id = (value == null ? void 0 : value.id) ?? null;
      }
    });
    const selectedCurrency = computed({
      get: () => {
        return currencyOptions.value.find((item) => {
          return Number(item.id) === Number(form.currency_id);
        }) || null;
      },
      set: (value) => {
        form.currency_id = (value == null ? void 0 : value.id) ?? null;
      }
    });
    const selectedCategories = computed({
      get: () => {
        const selectedIds = new Set(
          (form.categories || []).map((item) => Number(item == null ? void 0 : item.id))
        );
        return categoryOptions.value.filter((item) => {
          return selectedIds.has(Number(item.id));
        });
      },
      set: (values) => {
        var _a, _b;
        const previousMainId = (_a = (form.categories || []).find(
          (item) => Boolean(item == null ? void 0 : item.main)
        )) == null ? void 0 : _a.id;
        const nextValues = Array.isArray(values) ? values : [];
        const mainId = nextValues.some((item) => {
          return Number(item.id) === Number(previousMainId);
        }) ? previousMainId : ((_b = nextValues[0]) == null ? void 0 : _b.id) ?? null;
        form.categories = nextValues.map((item, index) => ({
          id: item.id,
          main: Number(item.id) === Number(mainId),
          order: index
        }));
      }
    });
    const selectedMainCategory = computed({
      get: () => {
        var _a;
        const currentMainId = (_a = (form.categories || []).find(
          (item) => Boolean(item == null ? void 0 : item.main)
        )) == null ? void 0 : _a.id;
        return selectedCategories.value.find((item) => {
          return Number(item.id) === Number(currentMainId);
        }) || selectedCategories.value[0] || null;
      },
      set: (value) => {
        const mainId = (value == null ? void 0 : value.id) ?? null;
        form.categories = (form.categories || []).map((item, index) => ({
          ...item,
          main: Number(item.id) === Number(mainId),
          order: index
        }));
      }
    });
    const syncCategories = () => {
      var _a;
      const mainId = ((_a = selectedMainCategory.value) == null ? void 0 : _a.id) ?? null;
      form.categories = selectedCategories.value.map((item, index) => ({
        id: item.id,
        main: Number(item.id) === Number(mainId),
        order: index
      }));
    };
    const selectedTags = computed({
      get: () => {
        const selectedIds = new Set(
          (form.tags || []).map((item) => Number(item == null ? void 0 : item.id))
        );
        return tagOptions.value.filter((item) => {
          return selectedIds.has(Number(item.id));
        });
      },
      set: (values) => {
        form.tags = (Array.isArray(values) ? values : []).map(
          (item, index) => ({
            id: item.id,
            order: index
          })
        );
      }
    });
    const galleryPreset = computed(() => {
      return props.imagePreset || {
        key: "rectangle_large",
        shape: "rectangle",
        width: 1200,
        height: 800,
        image_rotation_enabled: true,
        crop_rotation_enabled: true
      };
    });
    const handleExistingImagesUpdate = (images) => {
      existingImages.value = (images || []).map((image, index) => ({
        ...image,
        order: index
      }));
    };
    const handleDeleteExistingImage = (deletedId) => {
      if (!deletedId) {
        return;
      }
      const numericId = Number(deletedId);
      if (!form.deletedImages.some((id) => Number(id) === numericId)) {
        form.deletedImages.push(numericId);
      }
      existingImages.value = existingImages.value.filter((image) => Number(image.id) !== numericId).map((image, index) => ({
        ...image,
        order: index
      }));
    };
    const handleNewImagesUpdate = (images) => {
      newImages.value = (images || []).map((image, index) => ({
        ...image,
        order: index
      }));
    };
    const handleUrlInputFocus = () => {
      if (!form.url && currentTranslation.value.title) {
        form.url = transliterate(
          currentTranslation.value.title.toLowerCase()
        );
      }
    };
    const truncateText = (text, maxLength, addEllipsis = false) => {
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
    };
    const clearMetaFields = () => {
      const translation = currentTranslation.value;
      translation.meta_title = "";
      translation.meta_keywords = "";
      translation.meta_desc = "";
    };
    const generateMetaFields = () => {
      const translation = currentTranslation.value;
      if (translation.title && !translation.meta_title) {
        translation.meta_title = truncateText(translation.title, 255);
      }
      if (!translation.meta_keywords && translation.short) {
        let text = String(translation.short).replace(/(<([^>]+)>)/gi, "");
        text = text.replace(/[.,!?;:()[\]{}"'«»]/g, "");
        const words = text.split(/\s+/).filter((word) => word && word.length >= 3).map((word) => word.toLowerCase()).filter((value, index, self) => self.indexOf(value) === index);
        translation.meta_keywords = truncateText(words.join(", "), 255);
      }
      if (translation.short && !translation.meta_desc) {
        const description = String(translation.short).replace(/(<([^>]+)>)/gi, "");
        translation.meta_desc = truncateText(description, 255, true);
      }
    };
    const nullableNumber = (value, digits = 2) => {
      if (value === "" || value === null || typeof value === "undefined") {
        return null;
      }
      const number = Number(value);
      return Number.isFinite(number) ? Number(number.toFixed(digits)) : null;
    };
    const submitForm = () => {
      syncCategories();
      form.transform((data) => {
        const transformed = {
          ...data,
          _method: "put",
          price: nullableNumber(data.price, 2) ?? 0,
          old_price: nullableNumber(data.old_price, 2),
          purchase_price: nullableNumber(data.purchase_price, 2),
          wholesale_price: nullableNumber(data.wholesale_price, 2),
          wholesale_min_quantity: data.wholesale_min_quantity === "" || data.wholesale_min_quantity === null ? null : Number(data.wholesale_min_quantity),
          quantity: Number(data.quantity || 0),
          weight: nullableNumber(data.weight, 3),
          length: nullableNumber(data.length, 2),
          width: nullableNumber(data.width, 2),
          height: nullableNumber(data.height, 2),
          sort: Number(data.sort || 0),
          views: Number(data.views || 0),
          likes_count: Number(data.likes_count || 0),
          rating_avg: nullableNumber(data.rating_avg, 2) ?? 0,
          rating_count: Number(data.rating_count || 0),
          activity: data.activity ? 1 : 0,
          in_stock: data.in_stock ? 1 : 0,
          left: data.left ? 1 : 0,
          main: data.main ? 1 : 0,
          right: data.right ? 1 : 0,
          is_new: data.is_new ? 1 : 0,
          is_hit: data.is_hit ? 1 : 0,
          is_sale: data.is_sale ? 1 : 0,
          categories: data.categories,
          tags: data.tags,
          related_products: data.related_products,
          attribute_values: (data.attribute_values || []).map(
            (item, index) => ({
              ...item,
              id: (item == null ? void 0 : item.id) ?? null,
              value_string: (item == null ? void 0 : item.value_string) === "" ? null : (item == null ? void 0 : item.value_string) ?? null,
              value_number: nullableNumber(item == null ? void 0 : item.value_number, 4),
              value_boolean: (item == null ? void 0 : item.value_boolean) === "" || typeof (item == null ? void 0 : item.value_boolean) === "undefined" ? null : item.value_boolean,
              value_date: (item == null ? void 0 : item.value_date) || null,
              value_json: (item == null ? void 0 : item.value_json) ?? null,
              unit: (item == null ? void 0 : item.unit) || null,
              order: index,
              activity: (item == null ? void 0 : item.activity) ? 1 : 0
            })
          )
        };
        delete transformed.images;
        delete transformed.deletedImages;
        let imageIndex = 0;
        existingImages.value.forEach((image, index) => {
          transformed[`images[${imageIndex}][id]`] = image.id;
          transformed[`images[${imageIndex}][order]`] = Number(image.order ?? index);
          transformed[`images[${imageIndex}][alt]`] = image.alt ?? "";
          transformed[`images[${imageIndex}][caption]`] = image.caption ?? "";
          imageIndex++;
        });
        newImages.value.forEach((image, index) => {
          if (!image.file) {
            return;
          }
          transformed[`images[${imageIndex}][file]`] = image.file;
          transformed[`images[${imageIndex}][order]`] = Number(
            image.order ?? existingImages.value.length + index
          );
          transformed[`images[${imageIndex}][alt]`] = image.alt ?? "";
          transformed[`images[${imageIndex}][caption]`] = image.caption ?? "";
          imageIndex++;
        });
        form.deletedImages.forEach((id, index) => {
          transformed[`deletedImages[${index}]`] = id;
        });
        return transformed;
      });
      form.post(
        route("admin.marketProducts.update", {
          marketProduct: product.value.id
        }),
        {
          forceFormData: true,
          errorBag: "updateMarketProduct",
          preserveScroll: true,
          onSuccess: () => {
            toast.success("Товар успешно обновлён!");
            newImages.value = [];
            form.deletedImages = [];
          },
          onError: (errors) => {
            console.error("Ошибка обновления товара:", errors);
            const firstKey = Object.keys(errors || {})[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || "Проверьте правильность заполнения полей."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editMarketProduct")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editMarketProduct"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editMarketProduct")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editMarketProduct")), 1)
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
              href: _ctx.route("admin.marketProducts.index")
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
            _push2(`</div><form class="p-3 w-full" enctype="multipart/form-data"${_scopeId}><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
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
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
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
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "sort",
              value: unref(t)("sort"),
              class: "text-sm"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "sort",
              modelValue: unref(form).sort,
              "onUpdate:modelValue": ($event) => unref(form).sort = $event,
              modelModifiers: { number: true },
              type: "number",
              min: "0",
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2 lg:mt-0",
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "left",
              modelValue: unref(form).left,
              "onUpdate:modelValue": ($event) => unref(form).left = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "left",
              text: unref(t)("left"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "main",
              modelValue: unref(form).main,
              "onUpdate:modelValue": ($event) => unref(form).main = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "main",
              text: unref(t)("main"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "right",
              modelValue: unref(form).right,
              "onUpdate:modelValue": ($event) => unref(form).right = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "right",
              text: unref(t)("right"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "is_new",
              modelValue: unref(form).is_new,
              "onUpdate:modelValue": ($event) => unref(form).is_new = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "is_new",
              text: unref(t)("sortIsNew"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "is_hit",
              modelValue: unref(form).is_hit,
              "onUpdate:modelValue": ($event) => unref(form).is_hit = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "is_hit",
              text: unref(t)("sortIsHit"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "is_sale",
              modelValue: unref(form).is_sale,
              "onUpdate:modelValue": ($event) => unref(form).is_sale = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "is_sale",
              text: unref(t)("sortIsSale"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
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
            _push2(ssrRenderComponent(_sfc_main$4, {
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
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: `title-${activeLocale.value}`,
              modelValue: currentTranslation.value.title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
              type: "text",
              required: "",
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: `subtitle-${activeLocale.value}`,
              value: `${unref(t)("subtitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: `subtitle-${activeLocale.value}`,
              modelValue: currentTranslation.value.subtitle,
              "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
              type: "text",
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("subtitle")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: `short-${activeLocale.value}`,
              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.short || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: `short-${activeLocale.value}`,
              modelValue: currentTranslation.value.short,
              "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("short")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: `description-${activeLocale.value}`,
              value: `${unref(t)("description")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              modelValue: currentTranslation.value.description,
              "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
              height: 400
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("description")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: `meta-title-${activeLocale.value}`,
              value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: `meta-title-${activeLocale.value}`,
              modelValue: currentTranslation.value.meta_title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
              type: "text",
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("meta_title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: `meta-keywords-${activeLocale.value}`,
              value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: `meta-keywords-${activeLocale.value}`,
              modelValue: currentTranslation.value.meta_keywords,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
              type: "text",
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("meta_keywords")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: `meta-desc-${activeLocale.value}`,
              value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: `meta-desc-${activeLocale.value}`,
              modelValue: currentTranslation.value.meta_desc,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("meta_desc")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex justify-end gap-2 mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$b, { onClick: clearMetaFields }, {
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
            _push2(ssrRenderComponent(_sfc_main$c, { onClick: generateMetaFields }, {
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
            _push2(`</div></div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "url" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> URL `);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" URL ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "url",
              modelValue: unref(form).url,
              "onUpdate:modelValue": ($event) => unref(form).url = $event,
              type: "text",
              required: "",
              maxlength: "500",
              autocomplete: "url",
              onFocus: handleUrlInputFocus
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.url
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "vendor_code",
              value: unref(t)("vendorCode")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "vendor_code",
              modelValue: unref(form).vendor_code,
              "onUpdate:modelValue": ($event) => unref(form).vendor_code = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.vendor_code
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "sku",
              value: unref(t)("sku")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "sku",
              modelValue: unref(form).sku,
              "onUpdate:modelValue": ($event) => unref(form).sku = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.sku
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "barcode",
              value: unref(t)("barcode")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "barcode",
              modelValue: unref(form).barcode,
              "onUpdate:modelValue": ($event) => unref(form).barcode = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.barcode
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "market_company_id",
              value: unref(t)("marketCompany")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              modelValue: selectedCompany.value,
              "onUpdate:modelValue": ($event) => selectedCompany.value = $event,
              options: companyOptions.value,
              label: "label",
              "track-by": "id",
              searchable: true,
              "allow-empty": true,
              "show-labels": false,
              placeholder: "Выберите компанию"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.market_company_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "market_shop_id",
              value: unref(t)("marketShop")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              modelValue: selectedShop.value,
              "onUpdate:modelValue": ($event) => selectedShop.value = $event,
              options: shopOptions.value,
              label: "label",
              "track-by": "id",
              searchable: true,
              "allow-empty": true,
              "show-labels": false,
              placeholder: "Выберите магазин"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.market_shop_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "market_brand_id",
              value: unref(t)("brand")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              modelValue: selectedBrand.value,
              "onUpdate:modelValue": ($event) => selectedBrand.value = $event,
              options: brandOptions.value,
              label: "label",
              "track-by": "id",
              searchable: true,
              "allow-empty": true,
              "show-labels": false,
              placeholder: "Выберите бренд"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.market_brand_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "categories",
              value: unref(t)("categories")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              modelValue: selectedCategories.value,
              "onUpdate:modelValue": ($event) => selectedCategories.value = $event,
              options: categoryOptions.value,
              label: "label",
              "track-by": "id",
              multiple: true,
              "close-on-select": false,
              searchable: true,
              "show-labels": false,
              "options-limit": dynamicOptionsLimit(categoryOptions.value),
              placeholder: "Выберите категории"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.categories
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "category",
              value: unref(t)("mainCategory")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              modelValue: selectedMainCategory.value,
              "onUpdate:modelValue": ($event) => selectedMainCategory.value = $event,
              options: selectedCategories.value,
              label: "label",
              "track-by": "id",
              searchable: true,
              "allow-empty": true,
              "show-labels": false,
              placeholder: "Выберите основную категорию"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "tags",
              value: unref(t)("tags")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              modelValue: selectedTags.value,
              "onUpdate:modelValue": ($event) => selectedTags.value = $event,
              options: tagOptions.value,
              label: "label",
              "track-by": "id",
              multiple: true,
              "close-on-select": false,
              searchable: true,
              "show-labels": false,
              "options-limit": dynamicOptionsLimit(tagOptions.value),
              placeholder: "Выберите теги"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.tags
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
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
              placeholder: "Выберите валюту"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.currency_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col gap-4 lg:flex-row lg:justify-between"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "old_price",
              value: unref(t)("compareAtPrice")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              id: "old_price",
              modelValue: unref(form).old_price,
              "onUpdate:modelValue": ($event) => unref(form).old_price = $event,
              currency: selectedCurrency.value,
              "fraction-digits": 2,
              class: "w-full lg:w-36"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.old_price
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "price",
              value: unref(t)("price")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              id: "price",
              modelValue: unref(form).price,
              "onUpdate:modelValue": ($event) => unref(form).price = $event,
              currency: selectedCurrency.value,
              "fraction-digits": 2,
              class: "w-full lg:w-36"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.price
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "purchase_price",
              value: unref(t)("purchasePrice")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              id: "purchase_price",
              modelValue: unref(form).purchase_price,
              "onUpdate:modelValue": ($event) => unref(form).purchase_price = $event,
              currency: selectedCurrency.value,
              "fraction-digits": 2,
              class: "w-full lg:w-36"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.purchase_price
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col gap-4 lg:flex-row lg:justify-center"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "wholesale_price",
              value: unref(t)("wholesalePrice")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              id: "wholesale_price",
              modelValue: unref(form).wholesale_price,
              "onUpdate:modelValue": ($event) => unref(form).wholesale_price = $event,
              currency: selectedCurrency.value,
              "fraction-digits": 2,
              class: "w-full lg:w-36"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.wholesale_price
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "wholesale_min_quantity",
              value: unref(t)("wholesaleMinQuantity")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "wholesale_min_quantity",
              modelValue: unref(form).wholesale_min_quantity,
              "onUpdate:modelValue": ($event) => unref(form).wholesale_min_quantity = $event,
              modelModifiers: { number: true },
              type: "number",
              min: "0",
              class: "w-full lg:w-36"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.wholesale_min_quantity
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col gap-4 lg:flex-row lg:justify-center"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "quantity",
              value: unref(t)("quantity")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "quantity",
              modelValue: unref(form).quantity,
              "onUpdate:modelValue": ($event) => unref(form).quantity = $event,
              modelModifiers: { number: true },
              type: "number",
              min: "0",
              class: "w-full lg:w-36"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.quantity
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "weight",
              value: unref(t)("weight")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: "weight",
              modelValue: unref(form).weight,
              "onUpdate:modelValue": ($event) => unref(form).weight = $event,
              "fraction-digits": 3,
              unit: "кг",
              class: "w-full lg:w-36"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.weight
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col gap-4 lg:flex-row lg:justify-between"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "length",
              value: unref(t)("length")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: "length",
              modelValue: unref(form).length,
              "onUpdate:modelValue": ($event) => unref(form).length = $event,
              "fraction-digits": 2,
              unit: "см"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.length
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "width",
              value: unref(t)("width")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: "width",
              modelValue: unref(form).width,
              "onUpdate:modelValue": ($event) => unref(form).width = $event,
              "fraction-digits": 2,
              unit: "см"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.width
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "height",
              value: unref(t)("height")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: "height",
              modelValue: unref(form).height,
              "onUpdate:modelValue": ($event) => unref(form).height = $event,
              "fraction-digits": 2,
              unit: "см"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.height
            }, null, _parent2, _scopeId));
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$f, {
              modelValue: unref(form).attribute_values,
              "onUpdate:modelValue": ($event) => unref(form).attribute_values = $event,
              attributes: attributeList.value,
              errors: unref(form).errors
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$g, {
              modelValue: unref(form).related_products,
              "onUpdate:modelValue": ($event) => unref(form).related_products = $event,
              products: relatedProductList.value,
              errors: unref(form).errors,
              "exclude-product-id": product.value.id
            }, null, _parent2, _scopeId));
            _push2(`<div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "status",
              value: unref(t)("status")
            }, null, _parent2, _scopeId));
            _push2(`<select id="status" class="w-full px-2 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"${_scopeId}><option value="draft"${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, "draft") : ssrLooseEqual(unref(form).status, "draft")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="published"${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, "published") : ssrLooseEqual(unref(form).status, "published")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="archived"${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, "archived") : ssrLooseEqual(unref(form).status, "archived")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("statusArchived"))}</option></select>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.status
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "published_at",
              value: unref(t)("publishedAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "published_at",
              modelValue: unref(form).published_at,
              "onUpdate:modelValue": ($event) => unref(form).published_at = $event,
              type: "datetime-local"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.published_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "show_from_at",
              value: unref(t)("showFromAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "show_from_at",
              modelValue: unref(form).show_from_at,
              "onUpdate:modelValue": ($event) => unref(form).show_from_at = $event,
              type: "datetime-local"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.show_from_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "show_to_at",
              value: unref(t)("showToAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "show_to_at",
              modelValue: unref(form).show_to_at,
              "onUpdate:modelValue": ($event) => unref(form).show_to_at = $event,
              type: "datetime-local"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.show_to_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mt-4"${_scopeId}>`);
            if (__props.imageProcessorEnabled) {
              _push2(`<!--[-->`);
              _push2(ssrRenderComponent(_sfc_main$h, {
                images: existingImages.value,
                preset: galleryPreset.value,
                "onUpdate:images": handleExistingImagesUpdate,
                "onDelete:image": handleDeleteExistingImage
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$i, {
                preset: galleryPreset.value,
                "onUpdate:images": handleNewImagesUpdate
              }, null, _parent2, _scopeId));
              _push2(`<!--]-->`);
            } else {
              _push2(`<!--[-->`);
              _push2(ssrRenderComponent(_sfc_main$j, {
                images: existingImages.value,
                "onUpdate:images": handleExistingImagesUpdate,
                "onDelete:image": handleDeleteExistingImage
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$k, { "onUpdate:images": handleNewImagesUpdate }, null, _parent2, _scopeId));
              _push2(`<!--]-->`);
            }
            if (newImages.value.length) {
              _push2(`<div class="mt-2 text-xs text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(newImages.value.length)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.images
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex items-center justify-center mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.marketProducts.index")
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
            _push2(ssrRenderComponent(_sfc_main$l, {
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
                      href: _ctx.route("admin.marketProducts.index")
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
                    class: "p-3 w-full",
                    enctype: "multipart/form-data",
                    onSubmit: withModifiers(submitForm, ["prevent"])
                  }, [
                    createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
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
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$4, {
                          for: "sort",
                          value: unref(t)("sort"),
                          class: "text-sm"
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$5, {
                          id: "sort",
                          modelValue: unref(form).sort,
                          "onUpdate:modelValue": ($event) => unref(form).sort = $event,
                          modelModifiers: { number: true },
                          type: "number",
                          min: "0",
                          class: "w-full lg:w-28"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2 lg:mt-0",
                          message: unref(form).errors.sort
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "left",
                          modelValue: unref(form).left,
                          "onUpdate:modelValue": ($event) => unref(form).left = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "left",
                          text: unref(t)("left"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "main",
                          modelValue: unref(form).main,
                          "onUpdate:modelValue": ($event) => unref(form).main = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "main",
                          text: unref(t)("main"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "right",
                          modelValue: unref(form).right,
                          "onUpdate:modelValue": ($event) => unref(form).right = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "right",
                          text: unref(t)("right"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "is_new",
                          modelValue: unref(form).is_new,
                          "onUpdate:modelValue": ($event) => unref(form).is_new = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "is_new",
                          text: unref(t)("sortIsNew"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "is_hit",
                          modelValue: unref(form).is_hit,
                          "onUpdate:modelValue": ($event) => unref(form).is_hit = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "is_hit",
                          text: unref(t)("sortIsHit"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "is_sale",
                          modelValue: unref(form).is_sale,
                          "onUpdate:modelValue": ($event) => unref(form).is_sale = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "is_sale",
                          text: unref(t)("sortIsSale"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ])
                    ]),
                    createVNode("div", { class: "my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm" }, [
                      createVNode(_sfc_main$7, {
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
                        createVNode(_sfc_main$4, {
                          for: `title-${activeLocale.value}`
                        }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("title")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                          ]),
                          _: 1
                        }, 8, ["for"]),
                        createVNode(_sfc_main$8, {
                          id: `title-${activeLocale.value}`,
                          modelValue: currentTranslation.value.title,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
                          type: "text",
                          required: "",
                          maxlength: "255"
                        }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("title")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: `subtitle-${activeLocale.value}`,
                          value: `${unref(t)("subtitle")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["for", "value"]),
                        createVNode(_sfc_main$8, {
                          id: `subtitle-${activeLocale.value}`,
                          modelValue: currentTranslation.value.subtitle,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
                          type: "text",
                          maxlength: "255"
                        }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("subtitle")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode("div", { class: "flex justify-between w-full" }, [
                          createVNode(_sfc_main$4, {
                            for: `short-${activeLocale.value}`,
                            value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["for", "value"]),
                          createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.short || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                        ]),
                        createVNode(_sfc_main$9, {
                          id: `short-${activeLocale.value}`,
                          modelValue: currentTranslation.value.short,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
                          class: "w-full"
                        }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("short")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: `description-${activeLocale.value}`,
                          value: `${unref(t)("description")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["for", "value"]),
                        createVNode(_sfc_main$a, {
                          modelValue: currentTranslation.value.description,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
                          height: 400
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("description")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: `meta-title-${activeLocale.value}`,
                          value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["for", "value"]),
                        createVNode(_sfc_main$8, {
                          id: `meta-title-${activeLocale.value}`,
                          modelValue: currentTranslation.value.meta_title,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
                          type: "text",
                          maxlength: "255"
                        }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("meta_title")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: `meta-keywords-${activeLocale.value}`,
                          value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["for", "value"]),
                        createVNode(_sfc_main$8, {
                          id: `meta-keywords-${activeLocale.value}`,
                          modelValue: currentTranslation.value.meta_keywords,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
                          type: "text",
                          maxlength: "255"
                        }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("meta_keywords")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: `meta-desc-${activeLocale.value}`,
                          value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["for", "value"]),
                        createVNode(_sfc_main$9, {
                          id: `meta-desc-${activeLocale.value}`,
                          modelValue: currentTranslation.value.meta_desc,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
                          class: "w-full"
                        }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("meta_desc")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex justify-end gap-2 mt-4" }, [
                        createVNode(_sfc_main$b, {
                          onClick: withModifiers(clearMetaFields, ["prevent"])
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("clearMetaFields")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$c, {
                          onClick: withModifiers(generateMetaFields, ["prevent"])
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("generateMetaTags")), 1)
                          ]),
                          _: 1
                        })
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                      createVNode(_sfc_main$4, { for: "url" }, {
                        default: withCtx(() => [
                          createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                          createTextVNode(" URL ")
                        ]),
                        _: 1
                      }),
                      createVNode(_sfc_main$8, {
                        id: "url",
                        modelValue: unref(form).url,
                        "onUpdate:modelValue": ($event) => unref(form).url = $event,
                        type: "text",
                        required: "",
                        maxlength: "500",
                        autocomplete: "url",
                        onFocus: handleUrlInputFocus
                      }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                      createVNode(_sfc_main$6, {
                        class: "mt-2",
                        message: unref(form).errors.url
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "mb-3 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3" }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "vendor_code",
                          value: unref(t)("vendorCode")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "vendor_code",
                          modelValue: unref(form).vendor_code,
                          "onUpdate:modelValue": ($event) => unref(form).vendor_code = $event,
                          type: "text"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.vendor_code
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "sku",
                          value: unref(t)("sku")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "sku",
                          modelValue: unref(form).sku,
                          "onUpdate:modelValue": ($event) => unref(form).sku = $event,
                          type: "text"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.sku
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "barcode",
                          value: unref(t)("barcode")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "barcode",
                          modelValue: unref(form).barcode,
                          "onUpdate:modelValue": ($event) => unref(form).barcode = $event,
                          type: "text"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.barcode
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                      createVNode(_sfc_main$4, {
                        for: "market_company_id",
                        value: unref(t)("marketCompany")
                      }, null, 8, ["value"]),
                      createVNode(unref(VueMultiselect), {
                        modelValue: selectedCompany.value,
                        "onUpdate:modelValue": ($event) => selectedCompany.value = $event,
                        options: companyOptions.value,
                        label: "label",
                        "track-by": "id",
                        searchable: true,
                        "allow-empty": true,
                        "show-labels": false,
                        placeholder: "Выберите компанию"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "options"]),
                      createVNode(_sfc_main$6, {
                        message: unref(form).errors.market_company_id
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                      createVNode(_sfc_main$4, {
                        for: "market_shop_id",
                        value: unref(t)("marketShop")
                      }, null, 8, ["value"]),
                      createVNode(unref(VueMultiselect), {
                        modelValue: selectedShop.value,
                        "onUpdate:modelValue": ($event) => selectedShop.value = $event,
                        options: shopOptions.value,
                        label: "label",
                        "track-by": "id",
                        searchable: true,
                        "allow-empty": true,
                        "show-labels": false,
                        placeholder: "Выберите магазин"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "options"]),
                      createVNode(_sfc_main$6, {
                        message: unref(form).errors.market_shop_id
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                      createVNode(_sfc_main$4, {
                        for: "market_brand_id",
                        value: unref(t)("brand")
                      }, null, 8, ["value"]),
                      createVNode(unref(VueMultiselect), {
                        modelValue: selectedBrand.value,
                        "onUpdate:modelValue": ($event) => selectedBrand.value = $event,
                        options: brandOptions.value,
                        label: "label",
                        "track-by": "id",
                        searchable: true,
                        "allow-empty": true,
                        "show-labels": false,
                        placeholder: "Выберите бренд"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "options"]),
                      createVNode(_sfc_main$6, {
                        message: unref(form).errors.market_brand_id
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                      createVNode(_sfc_main$4, {
                        for: "categories",
                        value: unref(t)("categories")
                      }, null, 8, ["value"]),
                      createVNode(unref(VueMultiselect), {
                        modelValue: selectedCategories.value,
                        "onUpdate:modelValue": ($event) => selectedCategories.value = $event,
                        options: categoryOptions.value,
                        label: "label",
                        "track-by": "id",
                        multiple: true,
                        "close-on-select": false,
                        searchable: true,
                        "show-labels": false,
                        "options-limit": dynamicOptionsLimit(categoryOptions.value),
                        placeholder: "Выберите категории"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit"]),
                      createVNode(_sfc_main$6, {
                        message: unref(form).errors.categories
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                      createVNode(_sfc_main$4, {
                        for: "category",
                        value: unref(t)("mainCategory")
                      }, null, 8, ["value"]),
                      createVNode(unref(VueMultiselect), {
                        modelValue: selectedMainCategory.value,
                        "onUpdate:modelValue": ($event) => selectedMainCategory.value = $event,
                        options: selectedCategories.value,
                        label: "label",
                        "track-by": "id",
                        searchable: true,
                        "allow-empty": true,
                        "show-labels": false,
                        placeholder: "Выберите основную категорию"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "options"])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                      createVNode(_sfc_main$4, {
                        for: "tags",
                        value: unref(t)("tags")
                      }, null, 8, ["value"]),
                      createVNode(unref(VueMultiselect), {
                        modelValue: selectedTags.value,
                        "onUpdate:modelValue": ($event) => selectedTags.value = $event,
                        options: tagOptions.value,
                        label: "label",
                        "track-by": "id",
                        multiple: true,
                        "close-on-select": false,
                        searchable: true,
                        "show-labels": false,
                        "options-limit": dynamicOptionsLimit(tagOptions.value),
                        placeholder: "Выберите теги"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit"]),
                      createVNode(_sfc_main$6, {
                        message: unref(form).errors.tags
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                      createVNode(_sfc_main$4, {
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
                        placeholder: "Выберите валюту"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "options"]),
                      createVNode(_sfc_main$6, {
                        message: unref(form).errors.currency_id
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col gap-4 lg:flex-row lg:justify-between" }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "old_price",
                          value: unref(t)("compareAtPrice")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$d, {
                          id: "old_price",
                          modelValue: unref(form).old_price,
                          "onUpdate:modelValue": ($event) => unref(form).old_price = $event,
                          currency: selectedCurrency.value,
                          "fraction-digits": 2,
                          class: "w-full lg:w-36"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "currency"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.old_price
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "price",
                          value: unref(t)("price")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$d, {
                          id: "price",
                          modelValue: unref(form).price,
                          "onUpdate:modelValue": ($event) => unref(form).price = $event,
                          currency: selectedCurrency.value,
                          "fraction-digits": 2,
                          class: "w-full lg:w-36"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "currency"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.price
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "purchase_price",
                          value: unref(t)("purchasePrice")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$d, {
                          id: "purchase_price",
                          modelValue: unref(form).purchase_price,
                          "onUpdate:modelValue": ($event) => unref(form).purchase_price = $event,
                          currency: selectedCurrency.value,
                          "fraction-digits": 2,
                          class: "w-full lg:w-36"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "currency"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.purchase_price
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col gap-4 lg:flex-row lg:justify-center" }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "wholesale_price",
                          value: unref(t)("wholesalePrice")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$d, {
                          id: "wholesale_price",
                          modelValue: unref(form).wholesale_price,
                          "onUpdate:modelValue": ($event) => unref(form).wholesale_price = $event,
                          currency: selectedCurrency.value,
                          "fraction-digits": 2,
                          class: "w-full lg:w-36"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "currency"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.wholesale_price
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "wholesale_min_quantity",
                          value: unref(t)("wholesaleMinQuantity")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$5, {
                          id: "wholesale_min_quantity",
                          modelValue: unref(form).wholesale_min_quantity,
                          "onUpdate:modelValue": ($event) => unref(form).wholesale_min_quantity = $event,
                          modelModifiers: { number: true },
                          type: "number",
                          min: "0",
                          class: "w-full lg:w-36"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.wholesale_min_quantity
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col gap-4 lg:flex-row lg:justify-center" }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "quantity",
                          value: unref(t)("quantity")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$5, {
                          id: "quantity",
                          modelValue: unref(form).quantity,
                          "onUpdate:modelValue": ($event) => unref(form).quantity = $event,
                          modelModifiers: { number: true },
                          type: "number",
                          min: "0",
                          class: "w-full lg:w-36"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.quantity
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "weight",
                          value: unref(t)("weight")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$e, {
                          id: "weight",
                          modelValue: unref(form).weight,
                          "onUpdate:modelValue": ($event) => unref(form).weight = $event,
                          "fraction-digits": 3,
                          unit: "кг",
                          class: "w-full lg:w-36"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.weight
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col gap-4 lg:flex-row lg:justify-between" }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "length",
                          value: unref(t)("length")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$e, {
                          id: "length",
                          modelValue: unref(form).length,
                          "onUpdate:modelValue": ($event) => unref(form).length = $event,
                          "fraction-digits": 2,
                          unit: "см"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.length
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "width",
                          value: unref(t)("width")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$e, {
                          id: "width",
                          modelValue: unref(form).width,
                          "onUpdate:modelValue": ($event) => unref(form).width = $event,
                          "fraction-digits": 2,
                          unit: "см"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.width
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "height",
                          value: unref(t)("height")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$e, {
                          id: "height",
                          modelValue: unref(form).height,
                          "onUpdate:modelValue": ($event) => unref(form).height = $event,
                          "fraction-digits": 2,
                          unit: "см"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.height
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode(_sfc_main$f, {
                      modelValue: unref(form).attribute_values,
                      "onUpdate:modelValue": ($event) => unref(form).attribute_values = $event,
                      attributes: attributeList.value,
                      errors: unref(form).errors
                    }, null, 8, ["modelValue", "onUpdate:modelValue", "attributes", "errors"]),
                    createVNode(_sfc_main$g, {
                      modelValue: unref(form).related_products,
                      "onUpdate:modelValue": ($event) => unref(form).related_products = $event,
                      products: relatedProductList.value,
                      errors: unref(form).errors,
                      "exclude-product-id": product.value.id
                    }, null, 8, ["modelValue", "onUpdate:modelValue", "products", "errors", "exclude-product-id"]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-3 mb-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
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
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.status
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "published_at",
                          value: unref(t)("publishedAt")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "published_at",
                          modelValue: unref(form).published_at,
                          "onUpdate:modelValue": ($event) => unref(form).published_at = $event,
                          type: "datetime-local"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.published_at
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "show_from_at",
                          value: unref(t)("showFromAt")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "show_from_at",
                          modelValue: unref(form).show_from_at,
                          "onUpdate:modelValue": ($event) => unref(form).show_from_at = $event,
                          type: "datetime-local"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.show_from_at
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "show_to_at",
                          value: unref(t)("showToAt")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "show_to_at",
                          modelValue: unref(form).show_to_at,
                          "onUpdate:modelValue": ($event) => unref(form).show_to_at = $event,
                          type: "datetime-local"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.show_to_at
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mt-4" }, [
                      __props.imageProcessorEnabled ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                        createVNode(_sfc_main$h, {
                          images: existingImages.value,
                          preset: galleryPreset.value,
                          "onUpdate:images": handleExistingImagesUpdate,
                          "onDelete:image": handleDeleteExistingImage
                        }, null, 8, ["images", "preset"]),
                        createVNode(_sfc_main$i, {
                          preset: galleryPreset.value,
                          "onUpdate:images": handleNewImagesUpdate
                        }, null, 8, ["preset"])
                      ], 64)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                        createVNode(_sfc_main$j, {
                          images: existingImages.value,
                          "onUpdate:images": handleExistingImagesUpdate,
                          "onDelete:image": handleDeleteExistingImage
                        }, null, 8, ["images"]),
                        createVNode(_sfc_main$k, { "onUpdate:images": handleNewImagesUpdate })
                      ], 64)),
                      newImages.value.length ? (openBlock(), createBlock("div", {
                        key: 2,
                        class: "mt-2 text-xs text-slate-600 dark:text-slate-300"
                      }, toDisplayString(unref(t)("images")) + ": " + toDisplayString(newImages.value.length), 1)) : createCommentVNode("", true),
                      createVNode(_sfc_main$6, {
                        class: "mt-2",
                        message: unref(form).errors.images
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center mt-4" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.marketProducts.index")
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
                      createVNode(_sfc_main$l, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketProducts/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
