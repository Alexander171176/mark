import { ref, watch, computed, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderComponent, ssrRenderList, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import VueMultiselect from "vue-multiselect";
import { _ as _sfc_main$6 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$7 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$4 } from "./InputDecimalExt-B0XQGDJe.js";
import { _ as _sfc_main$2 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$5 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$3 } from "./LabelInput-C61CQHdu.js";
const _sfc_main$1 = {
  __name: "MarketProductAttributesField",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: Array, default: () => [] },
    attributes: { type: [Array, Object], default: () => [] },
    errors: { type: Object, default: () => ({}) }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const resourceList = (value) => {
      if (Array.isArray(value)) {
        return value;
      }
      if (Array.isArray(value == null ? void 0 : value.data)) {
        return value.data;
      }
      return [];
    };
    const translationTitle = (item) => {
      var _a;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || (item == null ? void 0 : item.code) || `ID: ${item == null ? void 0 : item.id}`;
    };
    const normalizeNullableInput = (value) => {
      return value === null || typeof value === "undefined" ? "" : value;
    };
    const normalizeRow = (item = {}, order = 0) => {
      var _a, _b, _c;
      return {
        id: (item == null ? void 0 : item.id) ?? null,
        market_attribute_id: (item == null ? void 0 : item.market_attribute_id) ?? ((_a = item == null ? void 0 : item.attribute) == null ? void 0 : _a.id) ?? null,
        market_attribute_value_id: (item == null ? void 0 : item.market_attribute_value_id) ?? ((_b = item == null ? void 0 : item.attribute_value) == null ? void 0 : _b.id) ?? ((_c = item == null ? void 0 : item.attributeValue) == null ? void 0 : _c.id) ?? null,
        value_string: (item == null ? void 0 : item.value_string) ?? "",
        value_number: normalizeNullableInput(item == null ? void 0 : item.value_number),
        value_boolean: (item == null ? void 0 : item.value_boolean) === null || typeof (item == null ? void 0 : item.value_boolean) === "undefined" ? null : Boolean(item.value_boolean),
        value_date: (item == null ? void 0 : item.value_date) ? String(item.value_date).slice(0, 10) : "",
        value_json: (item == null ? void 0 : item.value_json) ?? null,
        unit: (item == null ? void 0 : item.unit) ?? "",
        order: Number((item == null ? void 0 : item.order) ?? order),
        activity: typeof (item == null ? void 0 : item.activity) === "undefined" ? true : Boolean(item.activity)
      };
    };
    const normalizeRows = (items) => {
      return resourceList(items).map((item, index) => {
        return normalizeRow(item, index);
      });
    };
    const rows = ref(normalizeRows(props.modelValue));
    let syncingFromParent = false;
    watch(
      () => props.modelValue,
      (value) => {
        const normalized = normalizeRows(value);
        if (JSON.stringify(normalized) === JSON.stringify(rows.value)) {
          return;
        }
        syncingFromParent = true;
        rows.value = normalized;
        syncingFromParent = false;
      },
      { deep: true }
    );
    watch(
      rows,
      (value) => {
        if (syncingFromParent) {
          return;
        }
        emit(
          "update:modelValue",
          value.map((item, index) => ({
            ...item,
            order: index
          }))
        );
      },
      { deep: true }
    );
    const attributeList = computed(() => resourceList(props.attributes));
    const attributeOptions = computed(() => {
      return attributeList.value.map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}` + (item.unit ? ` (${item.unit})` : "")
      }));
    });
    const selectedAttribute = (row) => {
      return attributeOptions.value.find((item) => {
        return Number(item.id) === Number(row.market_attribute_id);
      }) || null;
    };
    const attributeType = (row) => {
      var _a;
      return ((_a = selectedAttribute(row)) == null ? void 0 : _a.type) || "string";
    };
    const attributeValueOptions = (row) => {
      const attribute = selectedAttribute(row);
      if (!attribute) {
        return [];
      }
      return resourceList(attribute.values).map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}`
      }));
    };
    const selectedReferenceValue = (row) => {
      return attributeValueOptions(row).find((item) => {
        return Number(item.id) === Number(row.market_attribute_value_id);
      }) || null;
    };
    const updateAttribute = (row, value) => {
      row.market_attribute_id = (value == null ? void 0 : value.id) ?? null;
      row.market_attribute_value_id = null;
      row.value_string = "";
      row.value_number = "";
      row.value_boolean = null;
      row.value_date = "";
      row.value_json = null;
      row.unit = (value == null ? void 0 : value.unit) ?? "";
    };
    const updateReferenceValue = (row, value) => {
      row.market_attribute_value_id = (value == null ? void 0 : value.id) ?? null;
    };
    const rowError = (index, field = "") => {
      var _a;
      const key = field ? `attribute_values.${index}.${field}` : `attribute_values.${index}`;
      return ((_a = props.errors) == null ? void 0 : _a[key]) || null;
    };
    return (_ctx, _push, _parent, _attrs) => {
      var _a;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm" }, _attrs))}><div class="mb-3 flex items-center justify-between"><h3 class="text-base font-semibold text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("attributes"))}</h3><button type="button" class="flex items-center rounded-sm bg-blue-600 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-700"><svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16"><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"></path></svg><span class="hidden sm:inline-block ml-2">${ssrInterpolate(unref(t)("addMarketAttribute"))}</span></button></div>`);
      _push(ssrRenderComponent(_sfc_main$2, {
        class: "mb-3",
        message: (_a = __props.errors) == null ? void 0 : _a.attribute_values
      }, null, _parent));
      if (rows.value.length) {
        _push(`<div class="space-y-3"><!--[-->`);
        ssrRenderList(rows.value, (row, index) => {
          _push(`<div class="rounded-sm p-3 border border-slate-300 dark:border-slate-500"><div class="grid grid-cols-1 gap-3 lg:grid-cols-2"><div class="min-w-0">`);
          _push(ssrRenderComponent(_sfc_main$3, {
            for: `attribute-${index}`,
            value: unref(t)("marketAttribute")
          }, null, _parent));
          _push(ssrRenderComponent(unref(VueMultiselect), {
            id: `attribute-${index}`,
            "model-value": selectedAttribute(row),
            options: attributeOptions.value,
            label: "label",
            "track-by": "id",
            searchable: true,
            "allow-empty": true,
            "show-labels": false,
            placeholder: "Выберите характеристику",
            class: "w-full min-w-0",
            "onUpdate:modelValue": ($event) => updateAttribute(row, $event)
          }, null, _parent));
          _push(`</div>`);
          if (attributeValueOptions(row).length) {
            _push(`<div class="min-w-0">`);
            _push(ssrRenderComponent(_sfc_main$3, {
              for: `attribute-value-${index}`,
              value: unref(t)("marketAttributeValue")
            }, null, _parent));
            _push(ssrRenderComponent(unref(VueMultiselect), {
              id: `attribute-value-${index}`,
              "model-value": selectedReferenceValue(row),
              options: attributeValueOptions(row),
              label: "label",
              "track-by": "id",
              searchable: true,
              "allow-empty": true,
              "show-labels": false,
              placeholder: "Выберите значение",
              class: "w-full min-w-0",
              "onUpdate:modelValue": ($event) => updateReferenceValue(row, $event)
            }, null, _parent));
            _push(`</div>`);
          } else if (attributeType(row) === "number") {
            _push(`<div>`);
            _push(ssrRenderComponent(_sfc_main$3, {
              for: `attribute-number-${index}`,
              value: unref(t)("value")
            }, null, _parent));
            _push(ssrRenderComponent(_sfc_main$4, {
              id: `attribute-number-${index}`,
              modelValue: row.value_number,
              "onUpdate:modelValue": ($event) => row.value_number = $event,
              step: 1e-4,
              "fraction-digits": 4,
              class: "w-full"
            }, null, _parent));
            _push(`</div>`);
          } else if (attributeType(row) === "boolean") {
            _push(`<div>`);
            _push(ssrRenderComponent(_sfc_main$3, {
              for: `attribute-boolean-${index}`,
              value: unref(t)("value")
            }, null, _parent));
            _push(`<select${ssrRenderAttr("id", `attribute-boolean-${index}`)} class="w-full rounded-sm border border-slate-400 bg-white px-3 py-2 text-slate-700 dark:bg-slate-200 dark:text-slate-900"><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray(row.value_boolean) ? ssrLooseContain(row.value_boolean, null) : ssrLooseEqual(row.value_boolean, null)) ? " selected" : ""}>—</option><option${ssrRenderAttr("value", true)}${ssrIncludeBooleanAttr(Array.isArray(row.value_boolean) ? ssrLooseContain(row.value_boolean, true) : ssrLooseEqual(row.value_boolean, true)) ? " selected" : ""}>${ssrInterpolate(unref(t)("yes"))}</option><option${ssrRenderAttr("value", false)}${ssrIncludeBooleanAttr(Array.isArray(row.value_boolean) ? ssrLooseContain(row.value_boolean, false) : ssrLooseEqual(row.value_boolean, false)) ? " selected" : ""}>${ssrInterpolate(unref(t)("no"))}</option></select></div>`);
          } else if (attributeType(row) === "date") {
            _push(`<div>`);
            _push(ssrRenderComponent(_sfc_main$3, {
              for: `attribute-date-${index}`,
              value: unref(t)("value")
            }, null, _parent));
            _push(ssrRenderComponent(_sfc_main$5, {
              id: `attribute-date-${index}`,
              modelValue: row.value_date,
              "onUpdate:modelValue": ($event) => row.value_date = $event,
              type: "date",
              class: "w-full"
            }, null, _parent));
            _push(`</div>`);
          } else {
            _push(`<div>`);
            _push(ssrRenderComponent(_sfc_main$3, {
              for: `attribute-string-${index}`,
              value: unref(t)("value")
            }, null, _parent));
            _push(ssrRenderComponent(_sfc_main$5, {
              id: `attribute-string-${index}`,
              modelValue: row.value_string,
              "onUpdate:modelValue": ($event) => row.value_string = $event,
              type: "text"
            }, null, _parent));
            _push(`</div>`);
          }
          _push(`<div>`);
          _push(ssrRenderComponent(_sfc_main$3, {
            for: `attribute-unit-${index}`,
            value: unref(t)("unit")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$5, {
            id: `attribute-unit-${index}`,
            modelValue: row.unit,
            "onUpdate:modelValue": ($event) => row.unit = $event,
            type: "text"
          }, null, _parent));
          _push(`</div><div class="flex items-end justify-between gap-3"><div class="flex items-center gap-2">`);
          _push(ssrRenderComponent(_sfc_main$6, {
            id: `attribute-activity-${index}`,
            modelValue: row.activity,
            "onUpdate:modelValue": ($event) => row.activity = $event
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$7, {
            for: `attribute-activity-${index}`,
            text: unref(t)("activity"),
            class: "text-sm h-8 flex items-center"
          }, null, _parent));
          _push(`</div><button type="button" class="w-fit text-xs font-semibold px-3 py-1 rounded-sm bg-rose-500 hover:bg-rose-700 text-white flex items-center justify-center gap-1"><svg class="w-3 h-3 fill-current" viewBox="0 0 448 512"><path d="M0 84V56c0-13.3 10.7-24 24-24h112l9.4-18.7c4-8.2 12.3-13.3 21.4-13.3h114.3c9.1 0 17.4 5.1 21.5 13.3L312 32h112c13.3 0 24 10.7 24 24v28c0 6.6-5.4 12-12 12H12C5.4 96 0 90.6 0 84zm416 56v324c0 26.5-21.5 48-48 48H80c-26.5 0-48-21.5-48-48V140c0-6.6 5.4-12 12-12h360c6.6 0 12 5.4 12 12zm-272 68c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208z"></path></svg><span class="hidden sm:inline-block ml-2">${ssrInterpolate(unref(t)("delete"))}</span></button></div></div>`);
          _push(ssrRenderComponent(_sfc_main$2, {
            message: rowError(index)
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$2, {
            message: rowError(index, "market_attribute_id")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$2, {
            message: rowError(index, "market_attribute_value_id")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$2, {
            message: rowError(index, "value_string")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$2, {
            message: rowError(index, "value_number")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$2, {
            message: rowError(index, "value_boolean")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$2, {
            message: rowError(index, "value_date")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$2, {
            message: rowError(index, "value_json")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$2, {
            message: rowError(index, "unit")
          }, null, _parent));
          _push(`</div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<div class="py-4 text-center text-sm text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProduct/Attribute/MarketProductAttributesField.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "MarketProductRelatedProductsField",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: Array, default: () => [] },
    products: { type: [Array, Object], default: () => [] },
    errors: { type: Object, default: () => ({}) },
    excludeProductId: { type: [Number, String, null], default: null }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const resourceList = (value) => {
      if (Array.isArray(value)) {
        return value;
      }
      if (Array.isArray(value == null ? void 0 : value.data)) {
        return value.data;
      }
      return [];
    };
    const translationTitle = (item) => {
      var _a;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || (item == null ? void 0 : item.url) || `ID: ${item == null ? void 0 : item.id}`;
    };
    const productOptions = computed(() => {
      return resourceList(props.products).filter((item) => {
        return !props.excludeProductId || Number(item.id) !== Number(props.excludeProductId);
      }).map((item) => ({
        ...item,
        label: `[ID: ${item.id}] ${translationTitle(item)}` + (item.sku ? ` — SKU: ${item.sku}` : "")
      }));
    });
    const normalizeModelItem = (item, index = 0) => {
      var _a, _b, _c, _d;
      return {
        id: Number(
          (item == null ? void 0 : item.id) ?? (item == null ? void 0 : item.related_product_id) ?? ((_a = item == null ? void 0 : item.product) == null ? void 0 : _a.id)
        ),
        type: (item == null ? void 0 : item.type) ?? (item == null ? void 0 : item.relation_type) ?? ((_b = item == null ? void 0 : item.pivot) == null ? void 0 : _b.type) ?? "related",
        order: Number(
          (item == null ? void 0 : item.order) ?? (item == null ? void 0 : item.relation_order) ?? ((_c = item == null ? void 0 : item.pivot) == null ? void 0 : _c.order) ?? index
        ),
        activity: (item == null ? void 0 : item.activity) ?? (item == null ? void 0 : item.relation_activity) ?? ((_d = item == null ? void 0 : item.pivot) == null ? void 0 : _d.activity) ?? true
      };
    };
    const normalizedModelValue = computed(() => {
      return resourceList(props.modelValue).map((item, index) => normalizeModelItem(item, index)).filter((item) => Number.isFinite(item.id) && item.id > 0).sort((a, b) => a.order - b.order);
    });
    const selectedProducts = computed({
      get: () => {
        return normalizedModelValue.value.map((relation) => {
          const option = productOptions.value.find((product) => {
            return Number(product.id) === Number(relation.id);
          });
          if (!option) {
            return null;
          }
          return {
            ...option,
            relation_type: relation.type,
            relation_order: relation.order,
            relation_activity: Boolean(relation.activity)
          };
        }).filter(Boolean);
      },
      set: (items) => {
        emit(
          "update:modelValue",
          resourceList(items).map((item, index) => {
            const current = normalizedModelValue.value.find((relation) => {
              return Number(relation.id) === Number(item.id);
            });
            return {
              id: Number(item.id),
              type: (item == null ? void 0 : item.relation_type) ?? (current == null ? void 0 : current.type) ?? "related",
              order: index,
              activity: typeof (item == null ? void 0 : item.relation_activity) !== "undefined" ? Boolean(item.relation_activity) : (current == null ? void 0 : current.activity) ?? true
            };
          })
        );
      }
    });
    const relationTypeOptions = computed(() => [
      { value: "related", label: t("related") },
      { value: "similar", label: t("similar") },
      { value: "accessory", label: t("accessory") },
      { value: "analog", label: t("analog") }
    ]);
    const dynamicOptionsLimit = computed(() => {
      return productOptions.value.length + 10;
    });
    const itemError = (index, field = "") => {
      var _a;
      const key = field ? `related_products.${index}.${field}` : `related_products.${index}`;
      return ((_a = props.errors) == null ? void 0 : _a[key]) || null;
    };
    return (_ctx, _push, _parent, _attrs) => {
      var _a;
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm" }, _attrs))}><h3 class="mb-3 text-base font-semibold text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("relatedProducts"))}</h3>`);
      _push(ssrRenderComponent(_sfc_main$3, {
        for: "related-products",
        value: unref(t)("relatedProducts")
      }, null, _parent));
      _push(ssrRenderComponent(unref(VueMultiselect), {
        id: "related-products",
        modelValue: selectedProducts.value,
        "onUpdate:modelValue": ($event) => selectedProducts.value = $event,
        options: productOptions.value,
        label: "label",
        "track-by": "id",
        multiple: true,
        "close-on-select": false,
        searchable: true,
        "show-labels": false,
        "options-limit": dynamicOptionsLimit.value,
        placeholder: unref(t)("selectRelatedProducts"),
        class: "w-full min-w-0"
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$2, {
        class: "mt-2",
        message: (_a = __props.errors) == null ? void 0 : _a.related_products
      }, null, _parent));
      if (selectedProducts.value.length) {
        _push(`<div class="mt-3 space-y-2"><!--[-->`);
        ssrRenderList(selectedProducts.value, (product, index) => {
          _push(`<div class="flex flex-col gap-3 rounded-sm bg-slate-100 px-3 py-2 dark:bg-slate-900 lg:flex-row lg:items-center lg:justify-between"><span class="min-w-0 truncate text-sm font-semibold text-slate-700 dark:text-slate-100"${ssrRenderAttr("title", product.label)}>${ssrInterpolate(product.label)}</span><select${ssrRenderAttr("value", product.relation_type)} class="w-full rounded-sm border border-slate-400 bg-white px-2 py-1 text-sm text-slate-700 dark:bg-slate-200 dark:text-slate-900 lg:w-44"><!--[-->`);
          ssrRenderList(relationTypeOptions.value, (option) => {
            _push(`<option${ssrRenderAttr("value", option.value)}>${ssrInterpolate(option.label)}</option>`);
          });
          _push(`<!--]--></select>`);
          _push(ssrRenderComponent(_sfc_main$2, {
            message: itemError(index)
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$2, {
            message: itemError(index, "id")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$2, {
            message: itemError(index, "type")
          }, null, _parent));
          _push(`</div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Market/MarketProduct/Related/MarketProductRelatedProductsField.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$1 as _,
  _sfc_main as a
};
