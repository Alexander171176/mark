import { computed, ref, watch, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, createCommentVNode, Fragment, renderList, withDirectives, vModelSelect, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrIncludeBooleanAttr, ssrRenderAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$i } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$c } from "./MetatagsButton-CvAlOk1D.js";
import { _ as _sfc_main$b } from "./ClearMetaButton-zYsecbj9.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$5 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$d } from "./MarketProductPriceInput-BXoog2u-.js";
import { _ as _sfc_main$4 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$8 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$6 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$9 } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$a } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$7 } from "./TranslationTabs-czH7YSpu.js";
import { _ as _sfc_main$g } from "./MultiImageEdit-BZZHL0n1.js";
import { _ as _sfc_main$h } from "./MultiImageUpload-Bg2ahSyk.js";
import { _ as _sfc_main$e } from "./MultiImagePresetEdit-aX3-dCXJ.js";
import { _ as _sfc_main$f } from "./MultiImagePresetUpload-Dhpb09Vk.js";
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
  __name: "Edit",
  __ssrInlineRender: true,
  props: {
    bundle: {
      type: Object,
      required: true
    },
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
    companies: {
      type: [Array, Object],
      default: () => []
    },
    shops: {
      type: [Array, Object],
      default: () => []
    },
    currencies: {
      type: [Array, Object],
      default: () => []
    },
    products: {
      type: [Array, Object],
      default: () => []
    },
    variants: {
      type: [Array, Object],
      default: () => []
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
    const resourceList = (value) => {
      if (Array.isArray(value)) {
        return value;
      }
      if (Array.isArray(value == null ? void 0 : value.data)) {
        return value.data;
      }
      return [];
    };
    const bundle = computed(() => {
      var _a;
      return ((_a = props.bundle) == null ? void 0 : _a.data) || props.bundle || {};
    });
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
    const normalizeNullableInput = (value) => {
      return value === null || typeof value === "undefined" ? "" : value;
    };
    const toDateTimeLocal = (value) => {
      if (!value) {
        return "";
      }
      const stringValue = String(value);
      if (/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/.test(
        stringValue
      )) {
        return stringValue;
      }
      const date = new Date(stringValue);
      if (Number.isNaN(date.getTime())) {
        return stringValue.slice(0, 16);
      }
      const pad = (number) => {
        return String(number).padStart(2, "0");
      };
      return [
        `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}`,
        `${pad(date.getHours())}:${pad(date.getMinutes())}`
      ].join("T");
    };
    const makeBundleItem = (sort = 0) => ({
      id: null,
      market_product_id: null,
      market_product_variant_id: null,
      quantity: 1,
      unit_price: null,
      discount_type: null,
      discount_value: null,
      sort,
      activity: true
    });
    const normalizeItems = (items) => {
      const normalized = resourceList(items).map((item, index) => ({
        id: (item == null ? void 0 : item.id) ? Number(item.id) : null,
        market_product_id: (item == null ? void 0 : item.market_product_id) ? Number(item.market_product_id) : null,
        market_product_variant_id: (item == null ? void 0 : item.market_product_variant_id) ? Number(item.market_product_variant_id) : null,
        quantity: Math.max(
          1,
          Number((item == null ? void 0 : item.quantity) || 1)
        ),
        unit_price: normalizeNullableInput(
          item == null ? void 0 : item.unit_price
        ),
        discount_type: (item == null ? void 0 : item.discount_type) || null,
        discount_value: normalizeNullableInput(
          item == null ? void 0 : item.discount_value
        ),
        sort: Number((item == null ? void 0 : item.sort) ?? index),
        activity: Boolean(
          (item == null ? void 0 : item.activity) ?? true
        )
      })).sort((first, second) => {
        if (first.sort !== second.sort) {
          return first.sort - second.sort;
        }
        return Number(first.id || 0) - Number(second.id || 0);
      }).map((item, index) => ({
        ...item,
        sort: index
      }));
      while (normalized.length < 2) {
        normalized.push(
          makeBundleItem(normalized.length)
        );
      }
      return normalized;
    };
    const initialTranslations = normalizeTranslations(
      bundle.value.translations
    );
    const defaultLocale = initialTranslations[props.currentLocale] ? props.currentLocale : Object.keys(initialTranslations)[0] || "ru";
    const activeLocale = ref(defaultLocale);
    const newImages = ref([]);
    const existingImages = ref(
      resourceList(bundle.value.images).filter((image) => {
        return image.url || image.webp_url || image.image_url || image.thumb_url;
      }).map((image, index) => {
        var _a;
        return {
          id: Number(image.id),
          url: image.webp_url || image.image_url || image.thumb_url || image.url,
          webp_url: image.webp_url || null,
          thumb_url: image.thumb_url || null,
          order: Number(
            image.order ?? ((_a = image.pivot) == null ? void 0 : _a.order) ?? index
          ),
          alt: image.alt || "",
          caption: image.caption || ""
        };
      }).sort((first, second) => {
        return first.order - second.order;
      })
    );
    const form = useForm({
      user_id: bundle.value.user_id ? Number(bundle.value.user_id) : null,
      market_company_id: bundle.value.market_company_id ? Number(bundle.value.market_company_id) : null,
      market_shop_id: bundle.value.market_shop_id ? Number(bundle.value.market_shop_id) : null,
      currency_id: bundle.value.currency_id ? Number(bundle.value.currency_id) : null,
      url: bundle.value.url || "",
      sku: bundle.value.sku || "",
      vendor_code: bundle.value.vendor_code || "",
      barcode: bundle.value.barcode || "",
      calculate_price: Boolean(
        bundle.value.calculate_price
      ),
      price: normalizeNullableInput(
        bundle.value.price
      ),
      old_price: normalizeNullableInput(
        bundle.value.old_price
      ),
      purchase_price: normalizeNullableInput(
        bundle.value.purchase_price
      ),
      wholesale_price: normalizeNullableInput(
        bundle.value.wholesale_price
      ),
      wholesale_min_quantity: normalizeNullableInput(
        bundle.value.wholesale_min_quantity
      ),
      sort: Number(bundle.value.sort || 0),
      activity: Boolean(bundle.value.activity),
      left: Boolean(bundle.value.left),
      main: Boolean(bundle.value.main),
      right: Boolean(bundle.value.right),
      is_new: Boolean(bundle.value.is_new),
      is_hit: Boolean(bundle.value.is_hit),
      is_sale: Boolean(bundle.value.is_sale),
      status: bundle.value.status || "draft",
      published_at: toDateTimeLocal(
        bundle.value.published_at
      ),
      show_from_at: toDateTimeLocal(
        bundle.value.show_from_at
      ),
      show_to_at: toDateTimeLocal(
        bundle.value.show_to_at
      ),
      views: Number(bundle.value.views || 0),
      likes_count: Number(
        bundle.value.likes_count || 0
      ),
      rating_avg: Number(
        bundle.value.rating_avg || 0
      ),
      rating_count: Number(
        bundle.value.rating_count || 0
      ),
      items: normalizeItems(
        bundle.value.items || bundle.value.active_items
      ),
      images: [],
      deletedImages: [],
      translations: initialTranslations
    });
    const currentTranslation = computed(() => {
      if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation();
      }
      return form.translations[activeLocale.value];
    });
    const getTranslationError = (key) => {
      return form.errors[`translations.${activeLocale.value}.${key}`];
    };
    const getItemError = (index, key) => {
      return form.errors[`items.${index}.${key}`];
    };
    const companyList = computed(() => {
      return resourceList(props.companies);
    });
    const shopList = computed(() => {
      return resourceList(props.shops);
    });
    const currencyList = computed(() => {
      return resourceList(props.currencies);
    });
    const productList = computed(() => {
      return resourceList(props.products);
    });
    const variantList = computed(() => {
      return resourceList(props.variants);
    });
    const translationTitle = (item) => {
      var _a;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || (item == null ? void 0 : item.code) || (item == null ? void 0 : item.sku) || (item == null ? void 0 : item.legal_name) || (item == null ? void 0 : item.name) || (item == null ? void 0 : item.url) || `ID: ${item == null ? void 0 : item.id}`;
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
        return Number(item.market_company_id) === Number(form.market_company_id);
      });
    });
    const shopOptions = computed(() => {
      return filteredShopList.value.map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`
      }));
    });
    const currencyOptions = computed(() => {
      return currencyList.value.map((item) => ({
        ...item,
        label: `[${item.code}] ${item.name}` + (item.symbol ? ` — ${item.symbol}` : "")
      }));
    });
    const selectedCompany = computed({
      get: () => {
        return companyOptions.value.find((item) => {
          return Number(item.id) === Number(form.market_company_id);
        }) || null;
      },
      set: (value) => {
        const previousCompanyId = form.market_company_id;
        form.market_company_id = (value == null ? void 0 : value.id) ?? null;
        if (Number(previousCompanyId) !== Number(form.market_company_id) && form.market_shop_id) {
          const selectedShopValue = shopList.value.find((item) => {
            return Number(item.id) === Number(form.market_shop_id);
          });
          if (selectedShopValue && Number(selectedShopValue.market_company_id) !== Number(form.market_company_id)) {
            form.market_shop_id = null;
          }
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
        if ((value == null ? void 0 : value.market_company_id) && Number(form.market_company_id) !== Number(value.market_company_id)) {
          form.market_company_id = Number(value.market_company_id);
        }
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
    const productOptions = computed(() => {
      return productList.value.map((item) => ({
        ...item,
        label: [
          `[ID: ${item.id}]`,
          translationTitle(item),
          item.sku ? `SKU: ${item.sku}` : ""
        ].filter(Boolean).join(" — ")
      }));
    });
    const selectedProduct = (item) => {
      return productOptions.value.find((product) => {
        return Number(product.id) === Number(item.market_product_id);
      }) || null;
    };
    const variantsForItem = (item) => {
      if (!item.market_product_id) {
        return [];
      }
      const product = selectedProduct(item);
      const nestedVariants = resourceList(
        product == null ? void 0 : product.variants
      );
      const source = nestedVariants.length ? nestedVariants : variantList.value.filter((variant) => {
        return Number(variant.market_product_id) === Number(item.market_product_id);
      });
      return source.map((variant) => ({
        ...variant,
        label: [
          variant.is_default ? "★" : "",
          `[ID: ${variant.id}]`,
          translationTitle(variant),
          variant.sku ? `SKU: ${variant.sku}` : ""
        ].filter(Boolean).join(" — ")
      }));
    };
    const selectedVariant = (item) => {
      return variantsForItem(item).find((variant) => {
        return Number(variant.id) === Number(
          item.market_product_variant_id
        );
      }) || null;
    };
    const selectProduct = (index, product) => {
      const item = form.items[index];
      item.market_product_id = (product == null ? void 0 : product.id) ?? null;
      item.market_product_variant_id = null;
    };
    const selectVariant = (index, variant) => {
      form.items[index].market_product_variant_id = (variant == null ? void 0 : variant.id) ?? null;
    };
    const addItem = () => {
      form.items.push(
        makeBundleItem(form.items.length)
      );
    };
    const removeItem = (index) => {
      if (form.items.length <= 2) {
        toast.warning(
          "Комплект должен содержать минимум две позиции."
        );
        return;
      }
      form.items.splice(index, 1);
      normalizeItemsSort();
    };
    const moveItem = (index, direction) => {
      const targetIndex = index + direction;
      if (targetIndex < 0 || targetIndex >= form.items.length) {
        return;
      }
      const currentItem = form.items[index];
      form.items[index] = form.items[targetIndex];
      form.items[targetIndex] = currentItem;
      normalizeItemsSort();
    };
    const normalizeItemsSort = () => {
      form.items.forEach((item, index) => {
        item.sort = index;
      });
    };
    watch(
      () => form.calculate_price,
      (value) => {
        if (value && form.price === "") {
          form.price = 0;
        }
      }
    );
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
      const imageId = Number(deletedId);
      if (!imageId) {
        return;
      }
      if (!form.deletedImages.some((id) => {
        return Number(id) === imageId;
      })) {
        form.deletedImages.push(imageId);
      }
      existingImages.value = existingImages.value.filter((image) => {
        return Number(image.id) !== imageId;
      }).map((image, index) => ({
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
      const suffix = addEllipsis ? "…" : "";
      return value.slice(0, maxLength - suffix.length).trimEnd() + suffix;
    };
    const clearMetaFields = () => {
      const translation = currentTranslation.value;
      translation.meta_title = "";
      translation.meta_keywords = "";
      translation.meta_desc = "";
    };
    const generateMetatags = () => {
      const translation = currentTranslation.value;
      if (translation.title && !translation.meta_title) {
        translation.meta_title = truncateText(
          translation.title,
          255
        );
      }
      if (translation.title && !translation.meta_keywords) {
        const words = translation.title.split(/\s+/).filter((word) => {
          return word && word.length >= 3;
        }).map((word) => {
          return word.toLowerCase();
        }).filter((value, index, self) => {
          return self.indexOf(value) === index;
        });
        translation.meta_keywords = truncateText(
          words.join(", "),
          255
        );
      }
      if (translation.short && !translation.meta_desc) {
        const description = String(
          translation.short
        ).replace(/(<([^>]+)>)/gi, "");
        translation.meta_desc = truncateText(
          description,
          255,
          true
        );
      }
    };
    const nullableNumber = (value, digits = 2) => {
      if (value === "" || value === null || typeof value === "undefined") {
        return null;
      }
      const number = Number(value);
      return Number.isFinite(number) ? Number(number.toFixed(digits)) : null;
    };
    const normalizedItems = () => {
      return form.items.map((item, index) => ({
        id: item.id ? Number(item.id) : null,
        market_product_id: item.market_product_id ? Number(item.market_product_id) : null,
        market_product_variant_id: item.market_product_variant_id ? Number(
          item.market_product_variant_id
        ) : null,
        quantity: Math.max(
          1,
          Number(item.quantity || 1)
        ),
        unit_price: nullableNumber(
          item.unit_price,
          2
        ),
        discount_type: item.discount_type || null,
        discount_value: item.discount_type ? nullableNumber(
          item.discount_value,
          2
        ) : null,
        sort: index,
        activity: item.activity ? 1 : 0
      }));
    };
    const submitForm = () => {
      normalizeItemsSort();
      form.transform((data) => {
        const transformed = {
          ...data,
          /** Laravel принимает multipart PUT через spoofing */
          _method: "put",
          /** Внешние ключи */
          market_company_id: data.market_company_id ? Number(data.market_company_id) : null,
          market_shop_id: data.market_shop_id ? Number(data.market_shop_id) : null,
          currency_id: data.currency_id ? Number(data.currency_id) : null,
          /** Режим формирования цены */
          calculate_price: data.calculate_price ? 1 : 0,
          /** Цены */
          price: nullableNumber(data.price, 2) ?? 0,
          old_price: nullableNumber(data.old_price, 2),
          purchase_price: nullableNumber(
            data.purchase_price,
            2
          ),
          wholesale_price: nullableNumber(
            data.wholesale_price,
            2
          ),
          wholesale_min_quantity: data.wholesale_min_quantity === "" || data.wholesale_min_quantity === null || typeof data.wholesale_min_quantity === "undefined" ? null : Number(
            data.wholesale_min_quantity
          ),
          /** Отображение */
          sort: Number(data.sort || 0),
          activity: data.activity ? 1 : 0,
          left: data.left ? 1 : 0,
          main: data.main ? 1 : 0,
          right: data.right ? 1 : 0,
          /** Маркетинговые признаки */
          is_new: data.is_new ? 1 : 0,
          is_hit: data.is_hit ? 1 : 0,
          is_sale: data.is_sale ? 1 : 0,
          /** Статистика */
          views: Number(data.views || 0),
          likes_count: Number(data.likes_count || 0),
          rating_avg: nullableNumber(
            data.rating_avg,
            2
          ) ?? 0,
          rating_count: Number(data.rating_count || 0),
          /** Состав комплекта */
          items: normalizedItems()
        };
        delete transformed.images;
        delete transformed.deletedImages;
        let imageIndex = 0;
        existingImages.value.forEach(
          (image, index) => {
            transformed[`images[${imageIndex}][id]`] = Number(image.id);
            transformed[`images[${imageIndex}][order]`] = Number(
              image.order ?? index
            );
            transformed[`images[${imageIndex}][alt]`] = image.alt || "";
            transformed[`images[${imageIndex}][caption]`] = image.caption || "";
            imageIndex += 1;
          }
        );
        newImages.value.forEach(
          (image, index) => {
            if (!image.file) {
              return;
            }
            transformed[`images[${imageIndex}][file]`] = image.file;
            transformed[`images[${imageIndex}][order]`] = Number(
              image.order ?? existingImages.value.length + index
            );
            transformed[`images[${imageIndex}][alt]`] = image.alt || "";
            transformed[`images[${imageIndex}][caption]`] = image.caption || "";
            imageIndex += 1;
          }
        );
        const deletedImageIds = [
          ...new Set(
            form.deletedImages.map((id) => Number(id)).filter((id) => id > 0)
          )
        ];
        deletedImageIds.forEach((id, index) => {
          transformed[`deletedImages[${index}]`] = id;
        });
        return transformed;
      });
      form.post(
        route(
          "admin.marketProductBundles.update",
          {
            marketProductBundle: bundle.value.id
          }
        ),
        {
          forceFormData: true,
          errorBag: "updateMarketProductBundle",
          preserveScroll: true,
          onSuccess: () => {
            toast.success(
              "Комплект товаров успешно обновлён!"
            );
            newImages.value = [];
            form.deletedImages = [];
          },
          onError: (errors) => {
            console.error(
              "Ошибка обновления комплекта:",
              errors
            );
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
        title: unref(t)("editMarketProductBundle")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editMarketProductBundle"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editMarketProductBundle")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editMarketProductBundle")), 1)
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
              href: _ctx.route("admin.marketProductBundles.index")
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
            _push2(`</div><form class="p-3 w-full" enctype="multipart/form-data"${_scopeId}><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-3"${_scopeId}><div class="flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "activity",
              modelValue: unref(form).activity,
              "onUpdate:modelValue": ($event) => unref(form).activity = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "activity",
              text: unref(t)("activity")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "sort",
              value: unref(t)("sort")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "sort",
              modelValue: unref(form).sort,
              "onUpdate:modelValue": ($event) => unref(form).sort = $event,
              modelModifiers: { number: true },
              type: "number",
              min: "0",
              class: "w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 grid grid-cols-1 sm:grid-cols-3 gap-4"${_scopeId}><div class="flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "left",
              modelValue: unref(form).left,
              "onUpdate:modelValue": ($event) => unref(form).left = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "left",
              text: unref(t)("left")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "main",
              modelValue: unref(form).main,
              "onUpdate:modelValue": ($event) => unref(form).main = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "main",
              text: unref(t)("main")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "right",
              modelValue: unref(form).right,
              "onUpdate:modelValue": ($event) => unref(form).right = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "right",
              text: unref(t)("right")
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 grid grid-cols-1 sm:grid-cols-3 gap-4"${_scopeId}><div class="flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "is_new",
              modelValue: unref(form).is_new,
              "onUpdate:modelValue": ($event) => unref(form).is_new = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "is_new",
              text: unref(t)("sortIsNew")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "is_hit",
              modelValue: unref(form).is_hit,
              "onUpdate:modelValue": ($event) => unref(form).is_hit = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "is_hit",
              text: unref(t)("sortIsHit")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "is_sale",
              modelValue: unref(form).is_sale,
              "onUpdate:modelValue": ($event) => unref(form).is_sale = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "is_sale",
              text: unref(t)("sortIsSale")
            }, null, _parent2, _scopeId));
            _push2(`</div></div><section class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "market_company_id",
              value: "Компания"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "market_company_id",
              modelValue: selectedCompany.value,
              "onUpdate:modelValue": ($event) => selectedCompany.value = $event,
              options: companyOptions.value,
              label: "label",
              "track-by": "id",
              "allow-empty": true,
              searchable: true,
              placeholder: "Выберите компанию"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.market_company_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "market_shop_id",
              value: "Магазин"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "market_shop_id",
              modelValue: selectedShop.value,
              "onUpdate:modelValue": ($event) => selectedShop.value = $event,
              options: shopOptions.value,
              label: "label",
              "track-by": "id",
              "allow-empty": true,
              searchable: true,
              placeholder: "Выберите магазин"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.market_shop_id
            }, null, _parent2, _scopeId));
            _push2(`</div></section><section class="my-3 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm"${_scopeId}>`);
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
            _push2(`<div class="mb-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: `title-${activeLocale.value}`
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("title"))} [${ssrInterpolate(activeLocale.value.toUpperCase())}] `);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500" }, "*"),
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
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: getTranslationError("title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3"${_scopeId}>`);
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
              message: getTranslationError("subtitle")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3"${_scopeId}><div class="flex justify-between gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: `short-${activeLocale.value}`,
              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<span class="text-xs text-slate-500"${_scopeId}>${ssrInterpolate((currentTranslation.value.short || "").length)} / 255 </span></div>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: `short-${activeLocale.value}`,
              modelValue: currentTranslation.value.short,
              "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: getTranslationError("short")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3"${_scopeId}>`);
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
              message: getTranslationError("description")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3"${_scopeId}>`);
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
              message: getTranslationError("meta_title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3"${_scopeId}>`);
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
              message: getTranslationError("meta_keywords")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3"${_scopeId}>`);
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
              message: getTranslationError("meta_desc")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex justify-end gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$b, {
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
            _push2(ssrRenderComponent(_sfc_main$c, {
              type: "button",
              onClick: generateMetatags
            }, {
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
            _push2(`</div></section><div class="mb-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "url" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500"${_scopeId2}>*</span> URL `);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500" }, "*"),
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
              maxlength: "500",
              onFocus: handleUrlInputFocus
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.url
            }, null, _parent2, _scopeId));
            _push2(`</div><section class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "vendor_code",
              value: "Артикул поставщика"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "vendor_code",
              modelValue: unref(form).vendor_code,
              "onUpdate:modelValue": ($event) => unref(form).vendor_code = $event,
              type: "text",
              maxlength: "100"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.vendor_code
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "sku",
              value: "SKU"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "sku",
              modelValue: unref(form).sku,
              "onUpdate:modelValue": ($event) => unref(form).sku = $event,
              type: "text",
              maxlength: "100"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.sku
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "barcode",
              value: "Штрихкод"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "barcode",
              modelValue: unref(form).barcode,
              "onUpdate:modelValue": ($event) => unref(form).barcode = $event,
              type: "text",
              maxlength: "100"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.barcode
            }, null, _parent2, _scopeId));
            _push2(`</div></section><section class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3"${_scopeId}><div class="flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              id: "calculate_price",
              modelValue: unref(form).calculate_price,
              "onUpdate:modelValue": ($event) => unref(form).calculate_price = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "calculate_price",
              text: "Рассчитывать цену автоматически"
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "currency_id",
              value: unref(t)("currency")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "currency_id",
              modelValue: selectedCurrency.value,
              "onUpdate:modelValue": ($event) => selectedCurrency.value = $event,
              options: currencyOptions.value,
              label: "label",
              "track-by": "id",
              "allow-empty": true,
              searchable: true,
              placeholder: "Выберите валюту"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.currency_id
            }, null, _parent2, _scopeId));
            _push2(`</div></section><section class="mb-3 p-4 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm"${_scopeId}><h3 class="mb-3 text-lg font-semibold text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("price"))}</h3><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-3"${_scopeId}><div${_scopeId}>`);
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
              disabled: unref(form).calculate_price
            }, null, _parent2, _scopeId));
            if (unref(form).calculate_price) {
              _push2(`<p class="mt-1 text-xs text-slate-500"${_scopeId}>${ssrInterpolate(unref(t)("calculatedPositions"))}</p>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.price
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "old_price",
              value: unref(t)("compareAtPrice")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              id: "old_price",
              modelValue: unref(form).old_price,
              "onUpdate:modelValue": ($event) => unref(form).old_price = $event,
              currency: selectedCurrency.value,
              "fraction-digits": 2
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.old_price
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
              "fraction-digits": 2
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.purchase_price
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "wholesale_price",
              value: unref(t)("wholesalePrice")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              id: "wholesale_price",
              modelValue: unref(form).wholesale_price,
              "onUpdate:modelValue": ($event) => unref(form).wholesale_price = $event,
              currency: selectedCurrency.value,
              "fraction-digits": 2
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
              min: "1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.wholesale_min_quantity
            }, null, _parent2, _scopeId));
            _push2(`</div></div></section><section class="mb-5 p-4 border border-blue-300 dark:border-blue-600 bg-blue-50/50 dark:bg-slate-800 rounded-sm"${_scopeId}><div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4"${_scopeId}><div${_scopeId}><h3 class="text-center text-lg font-semibold text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("kitComposition"))}</h3><p class="text-sm text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("addTwoItems"))}</p></div><button type="button" class="flex items-center gap-3 px-2 py-0.5 rounded-sm bg-cyan-600 hover:bg-cyan-700 text-white text-sm"${_scopeId}><svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16"${_scopeId}><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"${_scopeId}></path></svg><span${_scopeId}>${ssrInterpolate(unref(t)("addPosition"))}</span></button></div>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mb-3",
              message: unref(form).errors.items
            }, null, _parent2, _scopeId));
            _push2(`<!--[-->`);
            ssrRenderList(unref(form).items, (item, index) => {
              _push2(`<div class="mb-4 p-4 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-sm"${_scopeId}><div class="flex items-center justify-between gap-3 mb-3"${_scopeId}><h4 class="font-semibold text-slate-700 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("position"))} № ${ssrInterpolate(index + 1)}</h4><div class="flex items-center gap-2"${_scopeId}><button type="button" class="px-2 py-0 border border-slate-400 rounded-sm"${ssrIncludeBooleanAttr(index === 0) ? " disabled" : ""}${_scopeId}> ↑ </button><button type="button" class="px-2 py-0 border border-slate-400 rounded-sm"${ssrIncludeBooleanAttr(index === unref(form).items.length - 1) ? " disabled" : ""}${_scopeId}> ↓ </button><button type="button" class="flex items-center gap-3 px-3 py-1 rounded-sm bg-red-500 hover:bg-red-700 text-white"${_scopeId}><svg class="w-3 h-3 fill-current shrink-0" viewBox="0 0 16 16"${_scopeId}><path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z"${_scopeId}></path></svg><span class="text-xs"${_scopeId}>${ssrInterpolate(unref(t)("delete"))}</span></button></div></div><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-3"${_scopeId}><div class="flex items-center gap-2 pt-6"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$2, {
                id: `item-activity-${index}`,
                modelValue: item.activity,
                "onUpdate:modelValue": ($event) => item.activity = $event
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$3, {
                for: `item-activity-${index}`,
                text: unref(t)("activity")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                message: getItemError(index, "activity")
              }, null, _parent2, _scopeId));
              _push2(`</div><div${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                value: unref(t)("sort")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$5, {
                modelValue: item.sort,
                "onUpdate:modelValue": ($event) => item.sort = $event,
                modelModifiers: { number: true },
                type: "number",
                min: "0",
                disabled: ""
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                message: getItemError(index, "sort")
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="mb-3"${_scopeId}><div${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<span class="text-red-500"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("marketProduct"))}`);
                  } else {
                    return [
                      createVNode("span", { class: "text-red-500" }, "*"),
                      createTextVNode(" " + toDisplayString(unref(t)("marketProduct")), 1)
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(unref(VueMultiselect), {
                "model-value": selectedProduct(item),
                options: productOptions.value,
                label: "label",
                "track-by": "id",
                "allow-empty": true,
                searchable: true,
                placeholder: "Выберите товар",
                "onUpdate:modelValue": ($event) => selectProduct(index, $event)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                message: getItemError(index, "market_product_id")
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="mb-3"${_scopeId}><div${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                value: unref(t)("marketProductVariant")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(unref(VueMultiselect), {
                "model-value": selectedVariant(item),
                options: variantsForItem(item),
                label: "label",
                "track-by": "id",
                "allow-empty": true,
                searchable: true,
                disabled: !item.market_product_id,
                placeholder: unref(t)("withoutFixingIOption"),
                "onUpdate:modelValue": ($event) => selectVariant(index, $event)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                message: getItemError(index, "market_product_variant_id")
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3"${_scopeId}><div${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                value: unref(t)("yourUnitPrice")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$d, {
                modelValue: item.unit_price,
                "onUpdate:modelValue": ($event) => item.unit_price = $event,
                currency: selectedCurrency.value,
                "fraction-digits": 2
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                message: getItemError(index, "unit_price")
              }, null, _parent2, _scopeId));
              _push2(`</div><div${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                value: unref(t)("quantity")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$5, {
                modelValue: item.quantity,
                "onUpdate:modelValue": ($event) => item.quantity = $event,
                modelModifiers: { number: true },
                type: "number",
                min: "1"
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                message: getItemError(index, "quantity")
              }, null, _parent2, _scopeId));
              _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3"${_scopeId}><div${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                value: unref(t)("discountType")
              }, null, _parent2, _scopeId));
              _push2(`<select class="w-full px-2 py-0.5 form-select rounded-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"${_scopeId}><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray(item.discount_type) ? ssrLooseContain(item.discount_type, null) : ssrLooseEqual(item.discount_type, null)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("discountNone"))}</option><option value="fixed"${ssrIncludeBooleanAttr(Array.isArray(item.discount_type) ? ssrLooseContain(item.discount_type, "fixed") : ssrLooseEqual(item.discount_type, "fixed")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("discountFixed"))}</option><option value="percent"${ssrIncludeBooleanAttr(Array.isArray(item.discount_type) ? ssrLooseContain(item.discount_type, "percent") : ssrLooseEqual(item.discount_type, "percent")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("discountPercent"))}</option></select>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                message: getItemError(index, "discount_type")
              }, null, _parent2, _scopeId));
              _push2(`</div><div${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                value: unref(t)("discountAmount")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$5, {
                modelValue: item.discount_value,
                "onUpdate:modelValue": ($event) => item.discount_value = $event,
                modelModifiers: { number: true },
                type: "number",
                min: "0",
                max: item.discount_type === "percent" ? 100 : void 0,
                disabled: !item.discount_type
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                message: getItemError(index, "discount_value")
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div>`);
            });
            _push2(`<!--]--></section><section class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "status",
              value: unref(t)("status")
            }, null, _parent2, _scopeId));
            _push2(`<select id="status" class="w-full px-2 py-1 form-select rounded-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"${_scopeId}><option value="draft"${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, "draft") : ssrLooseEqual(unref(form).status, "draft")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="published"${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, "published") : ssrLooseEqual(unref(form).status, "published")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="archived"${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, "archived") : ssrLooseEqual(unref(form).status, "archived")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("statusArchived"))}</option></select>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.status
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
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
              message: unref(form).errors.published_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
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
              message: unref(form).errors.show_from_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
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
              message: unref(form).errors.show_to_at
            }, null, _parent2, _scopeId));
            _push2(`</div></section><section class="mt-5"${_scopeId}>`);
            if (__props.imageProcessorEnabled) {
              _push2(`<!--[-->`);
              _push2(ssrRenderComponent(_sfc_main$e, {
                images: existingImages.value,
                preset: galleryPreset.value,
                "onUpdate:images": handleExistingImagesUpdate,
                "onDelete:image": handleDeleteExistingImage
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$f, {
                images: newImages.value,
                preset: galleryPreset.value,
                "onUpdate:images": handleNewImagesUpdate
              }, null, _parent2, _scopeId));
              _push2(`<!--]-->`);
            } else {
              _push2(`<!--[-->`);
              _push2(ssrRenderComponent(_sfc_main$g, {
                images: existingImages.value,
                "onUpdate:images": handleExistingImagesUpdate,
                "onDelete:image": handleDeleteExistingImage
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$h, {
                images: newImages.value,
                "onUpdate:images": handleNewImagesUpdate
              }, null, _parent2, _scopeId));
              _push2(`<!--]-->`);
            }
            if (newImages.value.length) {
              _push2(`<div class="mt-2 text-xs text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(newImages.value.length)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.images
            }, null, _parent2, _scopeId));
            _push2(`</section><div class="flex items-center justify-center mt-6"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.marketProductBundles.index")
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
            _push2(ssrRenderComponent(_sfc_main$i, {
              class: ["ms-4 mb-0", {
                "opacity-25": unref(form).processing
              }],
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
                      href: _ctx.route("admin.marketProductBundles.index")
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
                    createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-3" }, [
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "activity",
                          modelValue: unref(form).activity,
                          "onUpdate:modelValue": ($event) => unref(form).activity = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "activity",
                          text: unref(t)("activity")
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode(_sfc_main$4, {
                          for: "sort",
                          value: unref(t)("sort")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$5, {
                          id: "sort",
                          modelValue: unref(form).sort,
                          "onUpdate:modelValue": ($event) => unref(form).sort = $event,
                          modelModifiers: { number: true },
                          type: "number",
                          min: "0",
                          class: "w-28"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.sort
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 grid grid-cols-1 sm:grid-cols-3 gap-4" }, [
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "left",
                          modelValue: unref(form).left,
                          "onUpdate:modelValue": ($event) => unref(form).left = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "left",
                          text: unref(t)("left")
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "main",
                          modelValue: unref(form).main,
                          "onUpdate:modelValue": ($event) => unref(form).main = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "main",
                          text: unref(t)("main")
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "right",
                          modelValue: unref(form).right,
                          "onUpdate:modelValue": ($event) => unref(form).right = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "right",
                          text: unref(t)("right")
                        }, null, 8, ["text"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 grid grid-cols-1 sm:grid-cols-3 gap-4" }, [
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "is_new",
                          modelValue: unref(form).is_new,
                          "onUpdate:modelValue": ($event) => unref(form).is_new = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "is_new",
                          text: unref(t)("sortIsNew")
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "is_hit",
                          modelValue: unref(form).is_hit,
                          "onUpdate:modelValue": ($event) => unref(form).is_hit = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "is_hit",
                          text: unref(t)("sortIsHit")
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "is_sale",
                          modelValue: unref(form).is_sale,
                          "onUpdate:modelValue": ($event) => unref(form).is_sale = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "is_sale",
                          text: unref(t)("sortIsSale")
                        }, null, 8, ["text"])
                      ])
                    ]),
                    createVNode("section", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3" }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "market_company_id",
                          value: "Компания"
                        }),
                        createVNode(unref(VueMultiselect), {
                          id: "market_company_id",
                          modelValue: selectedCompany.value,
                          "onUpdate:modelValue": ($event) => selectedCompany.value = $event,
                          options: companyOptions.value,
                          label: "label",
                          "track-by": "id",
                          "allow-empty": true,
                          searchable: true,
                          placeholder: "Выберите компанию"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.market_company_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "market_shop_id",
                          value: "Магазин"
                        }),
                        createVNode(unref(VueMultiselect), {
                          id: "market_shop_id",
                          modelValue: selectedShop.value,
                          "onUpdate:modelValue": ($event) => selectedShop.value = $event,
                          options: shopOptions.value,
                          label: "label",
                          "track-by": "id",
                          "allow-empty": true,
                          searchable: true,
                          placeholder: "Выберите магазин"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.market_shop_id
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("section", { class: "my-3 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm" }, [
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
                      createVNode("div", { class: "mb-3" }, [
                        createVNode(_sfc_main$4, {
                          for: `title-${activeLocale.value}`
                        }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("title")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                          ]),
                          _: 1
                        }, 8, ["for"]),
                        createVNode(_sfc_main$8, {
                          id: `title-${activeLocale.value}`,
                          modelValue: currentTranslation.value.title,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
                          type: "text",
                          maxlength: "255"
                        }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: getTranslationError("title")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3" }, [
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
                          message: getTranslationError("subtitle")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3" }, [
                        createVNode("div", { class: "flex justify-between gap-3" }, [
                          createVNode(_sfc_main$4, {
                            for: `short-${activeLocale.value}`,
                            value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["for", "value"]),
                          createVNode("span", { class: "text-xs text-slate-500" }, toDisplayString((currentTranslation.value.short || "").length) + " / 255 ", 1)
                        ]),
                        createVNode(_sfc_main$9, {
                          id: `short-${activeLocale.value}`,
                          modelValue: currentTranslation.value.short,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
                          class: "w-full"
                        }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: getTranslationError("short")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3" }, [
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
                          message: getTranslationError("description")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3" }, [
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
                          message: getTranslationError("meta_title")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3" }, [
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
                          message: getTranslationError("meta_keywords")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3" }, [
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
                          message: getTranslationError("meta_desc")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex justify-end gap-2" }, [
                        createVNode(_sfc_main$b, {
                          type: "button",
                          onClick: clearMetaFields
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("clearMetaFields")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$c, {
                          type: "button",
                          onClick: generateMetatags
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("generateMetaTags")), 1)
                          ]),
                          _: 1
                        })
                      ])
                    ]),
                    createVNode("div", { class: "mb-3" }, [
                      createVNode(_sfc_main$4, { for: "url" }, {
                        default: withCtx(() => [
                          createVNode("span", { class: "text-red-500" }, "*"),
                          createTextVNode(" URL ")
                        ]),
                        _: 1
                      }),
                      createVNode(_sfc_main$8, {
                        id: "url",
                        modelValue: unref(form).url,
                        "onUpdate:modelValue": ($event) => unref(form).url = $event,
                        type: "text",
                        maxlength: "500",
                        onFocus: handleUrlInputFocus
                      }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                      createVNode(_sfc_main$6, {
                        message: unref(form).errors.url
                      }, null, 8, ["message"])
                    ]),
                    createVNode("section", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3" }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "vendor_code",
                          value: "Артикул поставщика"
                        }),
                        createVNode(_sfc_main$8, {
                          id: "vendor_code",
                          modelValue: unref(form).vendor_code,
                          "onUpdate:modelValue": ($event) => unref(form).vendor_code = $event,
                          type: "text",
                          maxlength: "100"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.vendor_code
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "sku",
                          value: "SKU"
                        }),
                        createVNode(_sfc_main$8, {
                          id: "sku",
                          modelValue: unref(form).sku,
                          "onUpdate:modelValue": ($event) => unref(form).sku = $event,
                          type: "text",
                          maxlength: "100"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.sku
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "barcode",
                          value: "Штрихкод"
                        }),
                        createVNode(_sfc_main$8, {
                          id: "barcode",
                          modelValue: unref(form).barcode,
                          "onUpdate:modelValue": ($event) => unref(form).barcode = $event,
                          type: "text",
                          maxlength: "100"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.barcode
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("section", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3" }, [
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          id: "calculate_price",
                          modelValue: unref(form).calculate_price,
                          "onUpdate:modelValue": ($event) => unref(form).calculate_price = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "calculate_price",
                          text: "Рассчитывать цену автоматически"
                        })
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "currency_id",
                          value: unref(t)("currency")
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "currency_id",
                          modelValue: selectedCurrency.value,
                          "onUpdate:modelValue": ($event) => selectedCurrency.value = $event,
                          options: currencyOptions.value,
                          label: "label",
                          "track-by": "id",
                          "allow-empty": true,
                          searchable: true,
                          placeholder: "Выберите валюту"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.currency_id
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("section", { class: "mb-3 p-4 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm" }, [
                      createVNode("h3", { class: "mb-3 text-lg font-semibold text-slate-800 dark:text-slate-100" }, toDisplayString(unref(t)("price")), 1),
                      createVNode("div", { class: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-3" }, [
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
                            disabled: unref(form).calculate_price
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "currency", "disabled"]),
                          unref(form).calculate_price ? (openBlock(), createBlock("p", {
                            key: 0,
                            class: "mt-1 text-xs text-slate-500"
                          }, toDisplayString(unref(t)("calculatedPositions")), 1)) : createCommentVNode("", true),
                          createVNode(_sfc_main$6, {
                            message: unref(form).errors.price
                          }, null, 8, ["message"])
                        ]),
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
                            "fraction-digits": 2
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "currency"]),
                          createVNode(_sfc_main$6, {
                            message: unref(form).errors.old_price
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
                            "fraction-digits": 2
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "currency"]),
                          createVNode(_sfc_main$6, {
                            message: unref(form).errors.purchase_price
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "grid grid-cols-1 md:grid-cols-2 gap-4 mb-3" }, [
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
                            "fraction-digits": 2
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
                            min: "1"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            message: unref(form).errors.wholesale_min_quantity
                          }, null, 8, ["message"])
                        ])
                      ])
                    ]),
                    createVNode("section", { class: "mb-5 p-4 border border-blue-300 dark:border-blue-600 bg-blue-50/50 dark:bg-slate-800 rounded-sm" }, [
                      createVNode("div", { class: "flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4" }, [
                        createVNode("div", null, [
                          createVNode("h3", { class: "text-center text-lg font-semibold text-slate-800 dark:text-slate-100" }, toDisplayString(unref(t)("kitComposition")), 1),
                          createVNode("p", { class: "text-sm text-slate-600 dark:text-slate-300" }, toDisplayString(unref(t)("addTwoItems")), 1)
                        ]),
                        createVNode("button", {
                          type: "button",
                          class: "flex items-center gap-3 px-2 py-0.5 rounded-sm bg-cyan-600 hover:bg-cyan-700 text-white text-sm",
                          onClick: addItem
                        }, [
                          (openBlock(), createBlock("svg", {
                            class: "w-4 h-4 fill-current opacity-50 shrink-0",
                            viewBox: "0 0 16 16"
                          }, [
                            createVNode("path", { d: "M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" })
                          ])),
                          createVNode("span", null, toDisplayString(unref(t)("addPosition")), 1)
                        ])
                      ]),
                      createVNode(_sfc_main$6, {
                        class: "mb-3",
                        message: unref(form).errors.items
                      }, null, 8, ["message"]),
                      (openBlock(true), createBlock(Fragment, null, renderList(unref(form).items, (item, index) => {
                        return openBlock(), createBlock("div", {
                          key: `bundle-item-${index}`,
                          class: "mb-4 p-4 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-700 rounded-sm"
                        }, [
                          createVNode("div", { class: "flex items-center justify-between gap-3 mb-3" }, [
                            createVNode("h4", { class: "font-semibold text-slate-700 dark:text-slate-100" }, toDisplayString(unref(t)("position")) + " № " + toDisplayString(index + 1), 1),
                            createVNode("div", { class: "flex items-center gap-2" }, [
                              createVNode("button", {
                                type: "button",
                                class: "px-2 py-0 border border-slate-400 rounded-sm",
                                disabled: index === 0,
                                onClick: ($event) => moveItem(index, -1)
                              }, " ↑ ", 8, ["disabled", "onClick"]),
                              createVNode("button", {
                                type: "button",
                                class: "px-2 py-0 border border-slate-400 rounded-sm",
                                disabled: index === unref(form).items.length - 1,
                                onClick: ($event) => moveItem(index, 1)
                              }, " ↓ ", 8, ["disabled", "onClick"]),
                              createVNode("button", {
                                type: "button",
                                class: "flex items-center gap-3 px-3 py-1 rounded-sm bg-red-500 hover:bg-red-700 text-white",
                                onClick: ($event) => removeItem(index)
                              }, [
                                (openBlock(), createBlock("svg", {
                                  class: "w-3 h-3 fill-current shrink-0",
                                  viewBox: "0 0 16 16"
                                }, [
                                  createVNode("path", { d: "M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" })
                                ])),
                                createVNode("span", { class: "text-xs" }, toDisplayString(unref(t)("delete")), 1)
                              ], 8, ["onClick"])
                            ])
                          ]),
                          createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-3" }, [
                            createVNode("div", { class: "flex items-center gap-2 pt-6" }, [
                              createVNode(_sfc_main$2, {
                                id: `item-activity-${index}`,
                                modelValue: item.activity,
                                "onUpdate:modelValue": ($event) => item.activity = $event
                              }, null, 8, ["id", "modelValue", "onUpdate:modelValue"]),
                              createVNode(_sfc_main$3, {
                                for: `item-activity-${index}`,
                                text: unref(t)("activity")
                              }, null, 8, ["for", "text"]),
                              createVNode(_sfc_main$6, {
                                message: getItemError(index, "activity")
                              }, null, 8, ["message"])
                            ]),
                            createVNode("div", null, [
                              createVNode(_sfc_main$4, {
                                value: unref(t)("sort")
                              }, null, 8, ["value"]),
                              createVNode(_sfc_main$5, {
                                modelValue: item.sort,
                                "onUpdate:modelValue": ($event) => item.sort = $event,
                                modelModifiers: { number: true },
                                type: "number",
                                min: "0",
                                disabled: ""
                              }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                              createVNode(_sfc_main$6, {
                                message: getItemError(index, "sort")
                              }, null, 8, ["message"])
                            ])
                          ]),
                          createVNode("div", { class: "mb-3" }, [
                            createVNode("div", null, [
                              createVNode(_sfc_main$4, null, {
                                default: withCtx(() => [
                                  createVNode("span", { class: "text-red-500" }, "*"),
                                  createTextVNode(" " + toDisplayString(unref(t)("marketProduct")), 1)
                                ]),
                                _: 1
                              }),
                              createVNode(unref(VueMultiselect), {
                                "model-value": selectedProduct(item),
                                options: productOptions.value,
                                label: "label",
                                "track-by": "id",
                                "allow-empty": true,
                                searchable: true,
                                placeholder: "Выберите товар",
                                "onUpdate:modelValue": ($event) => selectProduct(index, $event)
                              }, null, 8, ["model-value", "options", "onUpdate:modelValue"]),
                              createVNode(_sfc_main$6, {
                                message: getItemError(index, "market_product_id")
                              }, null, 8, ["message"])
                            ])
                          ]),
                          createVNode("div", { class: "mb-3" }, [
                            createVNode("div", null, [
                              createVNode(_sfc_main$4, {
                                value: unref(t)("marketProductVariant")
                              }, null, 8, ["value"]),
                              createVNode(unref(VueMultiselect), {
                                "model-value": selectedVariant(item),
                                options: variantsForItem(item),
                                label: "label",
                                "track-by": "id",
                                "allow-empty": true,
                                searchable: true,
                                disabled: !item.market_product_id,
                                placeholder: unref(t)("withoutFixingIOption"),
                                "onUpdate:modelValue": ($event) => selectVariant(index, $event)
                              }, null, 8, ["model-value", "options", "disabled", "placeholder", "onUpdate:modelValue"]),
                              createVNode(_sfc_main$6, {
                                message: getItemError(index, "market_product_variant_id")
                              }, null, 8, ["message"])
                            ])
                          ]),
                          createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3" }, [
                            createVNode("div", null, [
                              createVNode(_sfc_main$4, {
                                value: unref(t)("yourUnitPrice")
                              }, null, 8, ["value"]),
                              createVNode(_sfc_main$d, {
                                modelValue: item.unit_price,
                                "onUpdate:modelValue": ($event) => item.unit_price = $event,
                                currency: selectedCurrency.value,
                                "fraction-digits": 2
                              }, null, 8, ["modelValue", "onUpdate:modelValue", "currency"]),
                              createVNode(_sfc_main$6, {
                                message: getItemError(index, "unit_price")
                              }, null, 8, ["message"])
                            ]),
                            createVNode("div", null, [
                              createVNode(_sfc_main$4, {
                                value: unref(t)("quantity")
                              }, null, 8, ["value"]),
                              createVNode(_sfc_main$5, {
                                modelValue: item.quantity,
                                "onUpdate:modelValue": ($event) => item.quantity = $event,
                                modelModifiers: { number: true },
                                type: "number",
                                min: "1"
                              }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                              createVNode(_sfc_main$6, {
                                message: getItemError(index, "quantity")
                              }, null, 8, ["message"])
                            ])
                          ]),
                          createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-3 mb-3" }, [
                            createVNode("div", null, [
                              createVNode(_sfc_main$4, {
                                value: unref(t)("discountType")
                              }, null, 8, ["value"]),
                              withDirectives(createVNode("select", {
                                "onUpdate:modelValue": ($event) => item.discount_type = $event,
                                class: "w-full px-2 py-0.5 form-select rounded-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"
                              }, [
                                createVNode("option", { value: null }, toDisplayString(unref(t)("discountNone")), 1),
                                createVNode("option", { value: "fixed" }, toDisplayString(unref(t)("discountFixed")), 1),
                                createVNode("option", { value: "percent" }, toDisplayString(unref(t)("discountPercent")), 1)
                              ], 8, ["onUpdate:modelValue"]), [
                                [vModelSelect, item.discount_type]
                              ]),
                              createVNode(_sfc_main$6, {
                                message: getItemError(index, "discount_type")
                              }, null, 8, ["message"])
                            ]),
                            createVNode("div", null, [
                              createVNode(_sfc_main$4, {
                                value: unref(t)("discountAmount")
                              }, null, 8, ["value"]),
                              createVNode(_sfc_main$5, {
                                modelValue: item.discount_value,
                                "onUpdate:modelValue": ($event) => item.discount_value = $event,
                                modelModifiers: { number: true },
                                type: "number",
                                min: "0",
                                max: item.discount_type === "percent" ? 100 : void 0,
                                disabled: !item.discount_type
                              }, null, 8, ["modelValue", "onUpdate:modelValue", "max", "disabled"]),
                              createVNode(_sfc_main$6, {
                                message: getItemError(index, "discount_value")
                              }, null, 8, ["message"])
                            ])
                          ])
                        ]);
                      }), 128))
                    ]),
                    createVNode("section", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4" }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "status",
                          value: unref(t)("status")
                        }, null, 8, ["value"]),
                        withDirectives(createVNode("select", {
                          id: "status",
                          "onUpdate:modelValue": ($event) => unref(form).status = $event,
                          class: "w-full px-2 py-1 form-select rounded-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"
                        }, [
                          createVNode("option", { value: "draft" }, toDisplayString(unref(t)("statusDraft")), 1),
                          createVNode("option", { value: "published" }, toDisplayString(unref(t)("statusPublished")), 1),
                          createVNode("option", { value: "archived" }, toDisplayString(unref(t)("statusArchived")), 1)
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).status]
                        ]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.status
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
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
                          message: unref(form).errors.published_at
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
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
                          message: unref(form).errors.show_from_at
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", null, [
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
                          message: unref(form).errors.show_to_at
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("section", { class: "mt-5" }, [
                      __props.imageProcessorEnabled ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                        createVNode(_sfc_main$e, {
                          images: existingImages.value,
                          preset: galleryPreset.value,
                          "onUpdate:images": handleExistingImagesUpdate,
                          "onDelete:image": handleDeleteExistingImage
                        }, null, 8, ["images", "preset"]),
                        createVNode(_sfc_main$f, {
                          images: newImages.value,
                          preset: galleryPreset.value,
                          "onUpdate:images": handleNewImagesUpdate
                        }, null, 8, ["images", "preset"])
                      ], 64)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                        createVNode(_sfc_main$g, {
                          images: existingImages.value,
                          "onUpdate:images": handleExistingImagesUpdate,
                          "onDelete:image": handleDeleteExistingImage
                        }, null, 8, ["images"]),
                        createVNode(_sfc_main$h, {
                          images: newImages.value,
                          "onUpdate:images": handleNewImagesUpdate
                        }, null, 8, ["images"])
                      ], 64)),
                      newImages.value.length ? (openBlock(), createBlock("div", {
                        key: 2,
                        class: "mt-2 text-xs text-slate-600 dark:text-slate-300"
                      }, toDisplayString(unref(t)("images")) + ": " + toDisplayString(newImages.value.length), 1)) : createCommentVNode("", true),
                      createVNode(_sfc_main$6, {
                        message: unref(form).errors.images
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center mt-6" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.marketProductBundles.index")
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
                      createVNode(_sfc_main$i, {
                        class: ["ms-4 mb-0", {
                          "opacity-25": unref(form).processing
                        }],
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketProductBundles/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
