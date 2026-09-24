import { ref, computed, watch, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, withDirectives, Fragment, renderList, vModelSelect, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$e } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$c } from "./MetatagsButton-CvAlOk1D.js";
import { _ as _sfc_main$b } from "./ClearMetaButton-zYsecbj9.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$4 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$8 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$5 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$6 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$9 } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$a } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$7 } from "./TranslationTabs-czH7YSpu.js";
import { _ as _sfc_main$d } from "./GeoCoordinatesInput-Dyk4dYRv.js";
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
    location: { type: Object, required: true },
    parents: { type: Array, default: () => [] },
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    locationTypes: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    var _a, _b;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const makeTranslation = () => ({
      title: "",
      title_in: "",
      title_from: "",
      short: "",
      description: "",
      meta_title: "",
      meta_keywords: "",
      meta_desc: ""
    });
    const defaultLocale = props.currentLocale || props.availableLocales[0] || ((_b = (_a = props.location.translations) == null ? void 0 : _a[0]) == null ? void 0 : _b.locale) || "ru";
    const buildTranslations = () => {
      const result = {};
      (props.location.translations || []).forEach((translation) => {
        result[translation.locale] = {
          title: translation.title || "",
          title_in: translation.title_in || "",
          title_from: translation.title_from || "",
          short: translation.short || "",
          description: translation.description || "",
          meta_title: translation.meta_title || "",
          meta_keywords: translation.meta_keywords || "",
          meta_desc: translation.meta_desc || ""
        };
      });
      if (!Object.keys(result).length) {
        result[defaultLocale] = makeTranslation();
      }
      if (!result[defaultLocale]) {
        result[defaultLocale] = makeTranslation();
      }
      return result;
    };
    const activeLocale = ref(defaultLocale);
    const form = useForm({
      parent_id: props.location.parent_id ?? null,
      type: props.location.type ?? "city",
      slug: props.location.slug ?? "",
      code: props.location.code ?? "",
      latitude: props.location.latitude ?? "",
      longitude: props.location.longitude ?? "",
      timezone: props.location.timezone ?? "",
      activity: Boolean(props.location.activity ?? false),
      is_default: Boolean(props.location.is_default ?? false),
      sort: props.location.sort ?? 0,
      translations: buildTranslations()
    });
    const currentTranslation = computed(() => {
      if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation();
      }
      return form.translations[activeLocale.value];
    });
    const pageTitle = computed(() => {
      var _a2, _b2;
      return currentTranslation.value.title || ((_b2 = (_a2 = props.location.translations) == null ? void 0 : _a2[0]) == null ? void 0 : _b2.title) || `ID: ${props.location.id}`;
    });
    const getError = (key) => {
      return form.errors[`translations.${activeLocale.value}.${key}`];
    };
    watch(
      () => form.is_default,
      (isDefault) => {
        if (isDefault) {
          form.activity = true;
          return;
        }
        if (props.location.is_default) {
          form.is_default = true;
          toast.warning(
            "Нельзя снять статус локации по умолчанию. Сначала назначьте другую локацию."
          );
        }
      }
    );
    watch(
      () => form.activity,
      (activity) => {
        if (!activity && form.is_default) {
          form.activity = true;
          toast.warning("Локация по умолчанию должна быть активна.");
        }
      }
    );
    const locationTypeTitle = (type) => {
      const types = {
        country: "country",
        region: "region",
        city: "city",
        district: "district"
      };
      return types[type] ? t(types[type]) : type || "—";
    };
    const parentTranslation = (location) => {
      const translations = (location == null ? void 0 : location.translations) || [];
      return translations.find((item) => item.locale === activeLocale.value) || translations.find((item) => item.locale === "ru") || translations[0] || null;
    };
    const parentTitle = (location) => {
      var _a2;
      return ((_a2 = parentTranslation(location)) == null ? void 0 : _a2.title) || `ID: ${location.id}`;
    };
    function buildParentOptions(locations, parentId = null, level = 0) {
      let result = [];
      (locations || []).filter((location) => location.parent_id === parentId).sort((a, b) => (a.sort || 0) - (b.sort || 0)).forEach((location) => {
        result.push({
          id: location.id,
          title: `${"— ".repeat(level)}${parentTitle(location)}`
        });
        result = result.concat(
          buildParentOptions(locations, location.id, level + 1)
        );
      });
      return result;
    }
    const parentOptions = computed(() => {
      return buildParentOptions(props.parents || []);
    });
    const handleSlugInputFocus = () => {
      if (!form.slug && currentTranslation.value.title) {
        form.slug = transliterate(
          currentTranslation.value.title.toLowerCase()
        );
      }
    };
    const truncateText = (text, maxLength, addEllipsis = false) => {
      if (!text) return "";
      const str = String(text);
      if (str.length <= maxLength) return str;
      const lastSpaceIndex = str.lastIndexOf(" ", maxLength);
      const truncated = lastSpaceIndex === -1 ? str.substring(0, maxLength) : str.substring(0, lastSpaceIndex);
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
        const text = String(translation.short).replace(/(<([^>]+)>)/gi, "");
        translation.meta_desc = truncateText(text, 200, true);
      }
    };
    const submitForm = () => {
      form.transform((data) => ({
        ...data,
        parent_id: data.parent_id || null,
        activity: data.activity ? 1 : 0,
        is_default: data.is_default ? 1 : 0
      }));
      form.put(
        route("admin.locations.update", {
          location: props.location.id
        }),
        {
          errorBag: "editLocation",
          preserveScroll: true,
          onSuccess: () => {
            toast.success("Локация успешно обновлена!");
          },
          onError: (errors) => {
            console.error("Не удалось обновить локацию:", errors);
            const firstError = errors == null ? void 0 : errors[Object.keys(errors)[0]];
            toast.error(
              firstError || "Пожалуйста, проверьте правильность заполнения полей."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editLocation")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editLocation"))}: ${ssrInterpolate(pageTitle.value)} [ID: ${ssrInterpolate(__props.location.id)}] `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editLocation")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(__props.location.id) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editLocation")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(__props.location.id) + "] ", 1)
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
              href: _ctx.route("admin.locations.index")
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
            _push2(`</div><form class="p-3 w-full"${_scopeId}><div class="mb-4 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).activity,
              "onUpdate:modelValue": ($event) => unref(form).activity = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "activity",
              text: unref(t)("activity"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).is_default,
              "onUpdate:modelValue": ($event) => unref(form).is_default = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "is_default",
              text: unref(t)("default"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "sort",
              value: unref(t)("sort"),
              class: "text-sm"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "sort",
              type: "number",
              min: "0",
              modelValue: unref(form).sort,
              "onUpdate:modelValue": ($event) => unref(form).sort = $event,
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "type" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("type"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("type")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<select id="type" required class="block w-full py-0.5 border-slate-500 font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${_scopeId}><!--[-->`);
            ssrRenderList(__props.locationTypes, (type) => {
              _push2(`<option${ssrRenderAttr("value", type)}${ssrIncludeBooleanAttr(Array.isArray(unref(form).type) ? ssrLooseContain(unref(form).type, type) : ssrLooseEqual(unref(form).type, type)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(locationTypeTitle(type))}</option>`);
            });
            _push2(`<!--]--></select>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.type
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "parent_id",
              value: unref(t)("parent")
            }, null, _parent2, _scopeId));
            _push2(`<select id="parent_id" class="block w-full py-0.5 border-slate-500 font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${_scopeId}><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray(unref(form).parent_id) ? ssrLooseContain(unref(form).parent_id, null) : ssrLooseEqual(unref(form).parent_id, null)) ? " selected" : ""}${_scopeId}>—</option><!--[-->`);
            ssrRenderList(parentOptions.value, (parent) => {
              _push2(`<option${ssrRenderAttr("value", parent.id)}${ssrIncludeBooleanAttr(Array.isArray(unref(form).parent_id) ? ssrLooseContain(unref(form).parent_id, parent.id) : ssrLooseEqual(unref(form).parent_id, parent.id)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(parent.title)}</option>`);
            });
            _push2(`<!--]--></select>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.parent_id
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              modelValue: activeLocale.value,
              "onUpdate:modelValue": ($event) => activeLocale.value = $event,
              translations: unref(form).translations,
              "available-locales": __props.availableLocales,
              "make-translation": makeTranslation,
              "onUpdate:translations": ($event) => unref(form).translations = $event,
              onRemoved: ($event) => unref(toast).warning("Перевод удалён."),
              onAdded: ($event) => unref(toast).success("Локаль добавлена.")
            }, null, _parent2, _scopeId));
            _push2(`<div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "title" }, {
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
              id: "title",
              type: "text",
              modelValue: currentTranslation.value.title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
              required: "",
              autocomplete: "title"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "title_in",
              value: `${unref(t)("titleIn")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "title_in",
              type: "text",
              modelValue: currentTranslation.value.title_in,
              "onUpdate:modelValue": ($event) => currentTranslation.value.title_in = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("title_in")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "title_from",
              value: `${unref(t)("titleFrom")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "title_from",
              type: "text",
              modelValue: currentTranslation.value.title_from,
              "onUpdate:modelValue": ($event) => currentTranslation.value.title_from = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("title_from")
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "short",
              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-xs text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.short || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
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
              for: "description",
              value: `${unref(t)("description")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              modelValue: currentTranslation.value.description,
              "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
              height: 500
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("description")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "meta_title",
              value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-xs text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_title || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "meta_title",
              type: "text",
              modelValue: currentTranslation.value.meta_title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("meta_title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "meta_keywords",
              value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-xs text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_keywords || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "meta_keywords",
              type: "text",
              modelValue: currentTranslation.value.meta_keywords,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("meta_keywords")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "meta_desc",
              value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-xs text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_desc || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
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
            _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "slug" }, {
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
              required: "",
              autocomplete: "slug",
              onFocus: handleSlugInputFocus
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.slug
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "code",
              value: unref(t)("code")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "code",
              type: "text",
              modelValue: unref(form).code,
              "onUpdate:modelValue": ($event) => unref(form).code = $event,
              autocomplete: "code"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.code
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-4 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "timezone",
              value: unref(t)("timezone")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "timezone",
              type: "text",
              modelValue: unref(form).timezone,
              "onUpdate:modelValue": ($event) => unref(form).timezone = $event,
              placeholder: "Asia/Almaty",
              autocomplete: "timezone"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.timezone
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$d, {
              latitude: unref(form).latitude,
              "onUpdate:latitude": ($event) => unref(form).latitude = $event,
              longitude: unref(form).longitude,
              "onUpdate:longitude": ($event) => unref(form).longitude = $event,
              "latitude-error": unref(form).errors.latitude,
              "longitude-error": unref(form).errors.longitude,
              "wrapper-class": "mb-4 flex justify-center"
            }, null, _parent2, _scopeId));
            _push2(`<div class="flex items-center justify-center mt-6"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.locations.index")
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
            _push2(ssrRenderComponent(_sfc_main$e, {
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
                      href: _ctx.route("admin.locations.index")
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
                    onSubmit: withModifiers(submitForm, ["prevent"]),
                    class: "p-3 w-full"
                  }, [
                    createVNode("div", { class: "mb-4 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          modelValue: unref(form).activity,
                          "onUpdate:modelValue": ($event) => unref(form).activity = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "activity",
                          text: unref(t)("activity"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          modelValue: unref(form).is_default,
                          "onUpdate:modelValue": ($event) => unref(form).is_default = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "is_default",
                          text: unref(t)("default"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        createVNode(_sfc_main$4, {
                          for: "sort",
                          value: unref(t)("sort"),
                          class: "text-sm"
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$5, {
                          id: "sort",
                          type: "number",
                          min: "0",
                          modelValue: unref(form).sort,
                          "onUpdate:modelValue": ($event) => unref(form).sort = $event,
                          class: "w-full lg:w-28"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          message: unref(form).errors.sort
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, { for: "type" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("type")), 1)
                          ]),
                          _: 1
                        }),
                        withDirectives(createVNode("select", {
                          id: "type",
                          "onUpdate:modelValue": ($event) => unref(form).type = $event,
                          required: "",
                          class: "block w-full py-0.5 border-slate-500 font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"
                        }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(__props.locationTypes, (type) => {
                            return openBlock(), createBlock("option", {
                              key: type,
                              value: type
                            }, toDisplayString(locationTypeTitle(type)), 9, ["value"]);
                          }), 128))
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).type]
                        ]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.type
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "parent_id",
                          value: unref(t)("parent")
                        }, null, 8, ["value"]),
                        withDirectives(createVNode("select", {
                          id: "parent_id",
                          "onUpdate:modelValue": ($event) => unref(form).parent_id = $event,
                          class: "block w-full py-0.5 border-slate-500 font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"
                        }, [
                          createVNode("option", { value: null }, "—"),
                          (openBlock(true), createBlock(Fragment, null, renderList(parentOptions.value, (parent) => {
                            return openBlock(), createBlock("option", {
                              key: parent.id,
                              value: parent.id
                            }, toDisplayString(parent.title), 9, ["value"]);
                          }), 128))
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).parent_id]
                        ]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.parent_id
                        }, null, 8, ["message"])
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
                        onRemoved: ($event) => unref(toast).warning("Перевод удалён."),
                        onAdded: ($event) => unref(toast).success("Локаль добавлена.")
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "translations", "available-locales", "onUpdate:translations", "onRemoved", "onAdded"]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, { for: "title" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("title")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$8, {
                          id: "title",
                          type: "text",
                          modelValue: currentTranslation.value.title,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
                          required: "",
                          autocomplete: "title"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("title")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "title_in",
                            value: `${unref(t)("titleIn")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$8, {
                            id: "title_in",
                            type: "text",
                            modelValue: currentTranslation.value.title_in,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.title_in = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2",
                            message: getError("title_in")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "title_from",
                            value: `${unref(t)("titleFrom")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$8, {
                            id: "title_from",
                            type: "text",
                            modelValue: currentTranslation.value.title_from,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.title_from = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2",
                            message: getError("title_from")
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode("div", { class: "flex justify-between w-full" }, [
                          createVNode(_sfc_main$4, {
                            for: "short",
                            value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode("div", { class: "text-xs text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.short || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                        ]),
                        createVNode(_sfc_main$9, {
                          modelValue: currentTranslation.value.short,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("short")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "description",
                          value: `${unref(t)("description")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$a, {
                          modelValue: currentTranslation.value.description,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
                          height: 500
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("description")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode("div", { class: "flex justify-between w-full" }, [
                          createVNode(_sfc_main$4, {
                            for: "meta_title",
                            value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode("div", { class: "text-xs text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_title || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                        ]),
                        createVNode(_sfc_main$8, {
                          id: "meta_title",
                          type: "text",
                          modelValue: currentTranslation.value.meta_title,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
                          maxlength: "255"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("meta_title")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode("div", { class: "flex justify-between w-full" }, [
                          createVNode(_sfc_main$4, {
                            for: "meta_keywords",
                            value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode("div", { class: "text-xs text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_keywords || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                        ]),
                        createVNode(_sfc_main$8, {
                          id: "meta_keywords",
                          type: "text",
                          modelValue: currentTranslation.value.meta_keywords,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
                          maxlength: "255"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("meta_keywords")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode("div", { class: "flex justify-between w-full" }, [
                          createVNode(_sfc_main$4, {
                            for: "meta_desc",
                            value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode("div", { class: "text-xs text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_desc || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                        ]),
                        createVNode(_sfc_main$9, {
                          modelValue: currentTranslation.value.meta_desc,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
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
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, { for: "slug" }, {
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
                          required: "",
                          autocomplete: "slug",
                          onFocus: handleSlugInputFocus
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.slug
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "code",
                          value: unref(t)("code")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "code",
                          type: "text",
                          modelValue: unref(form).code,
                          "onUpdate:modelValue": ($event) => unref(form).code = $event,
                          autocomplete: "code"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.code
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-4 flex flex-col items-start" }, [
                      createVNode(_sfc_main$4, {
                        for: "timezone",
                        value: unref(t)("timezone")
                      }, null, 8, ["value"]),
                      createVNode(_sfc_main$8, {
                        id: "timezone",
                        type: "text",
                        modelValue: unref(form).timezone,
                        "onUpdate:modelValue": ($event) => unref(form).timezone = $event,
                        placeholder: "Asia/Almaty",
                        autocomplete: "timezone"
                      }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                      createVNode(_sfc_main$6, {
                        class: "mt-2",
                        message: unref(form).errors.timezone
                      }, null, 8, ["message"])
                    ]),
                    createVNode(_sfc_main$d, {
                      latitude: unref(form).latitude,
                      "onUpdate:latitude": ($event) => unref(form).latitude = $event,
                      longitude: unref(form).longitude,
                      "onUpdate:longitude": ($event) => unref(form).longitude = $event,
                      "latitude-error": unref(form).errors.latitude,
                      "longitude-error": unref(form).errors.longitude,
                      "wrapper-class": "mb-4 flex justify-center"
                    }, null, 8, ["latitude", "onUpdate:latitude", "longitude", "onUpdate:longitude", "latitude-error", "longitude-error"]),
                    createVNode("div", { class: "flex items-center justify-center mt-6" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.locations.index")
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
                      createVNode(_sfc_main$e, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/Locations/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
