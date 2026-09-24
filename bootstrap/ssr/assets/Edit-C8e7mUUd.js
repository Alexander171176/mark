import { computed, ref, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, withDirectives, vModelSelect, vModelText, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderAttr } from "vue/server-renderer";
import { useToast } from "vue-toastification";
import { useI18n } from "vue-i18n";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import { useForm } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$f } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$c } from "./MetatagsButton-CvAlOk1D.js";
import { _ as _sfc_main$b } from "./ClearMetaButton-zYsecbj9.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$9 } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$5 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$4 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$8 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$6 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$a } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$7 } from "./TranslationTabs-czH7YSpu.js";
import { _ as _sfc_main$d } from "./GeoCoordinatesInput-Dyk4dYRv.js";
import { _ as _sfc_main$e } from "./EditImageFileInput-DmYXxrrC.js";
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
    company: {
      type: Object,
      required: true
    },
    currentLocale: {
      type: String,
      default: ""
    },
    availableLocales: {
      type: Array,
      default: () => []
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  setup(__props) {
    var _a, _b, _c, _d, _e;
    const toast = useToast();
    const { t } = useI18n();
    const props = __props;
    const companyData = computed(() => {
      var _a2;
      return ((_a2 = props.company) == null ? void 0 : _a2.data) ?? props.company;
    });
    const pageTitle = computed(() => {
      var _a2;
      return currentTranslation.value.title || ((_a2 = companyData.value.translation) == null ? void 0 : _a2.title) || `ID: ${companyData.value.id}`;
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
    const defaultLocale = props.currentLocale || "ru";
    const activeLocale = ref(defaultLocale);
    const storageUrl = (path) => {
      if (!path) return null;
      if (path instanceof File || String(path).startsWith("http") || String(path).startsWith("/storage/")) {
        return path;
      }
      return `/storage/${path}`;
    };
    const buildTranslations = () => {
      const result = {};
      (companyData.value.translations || []).forEach((translation) => {
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
      if (!result[defaultLocale]) {
        result[defaultLocale] = makeTranslation();
      }
      return result;
    };
    const form = useForm({
      _method: "PUT",
      user_id: companyData.value.user_id || null,
      url: companyData.value.url || "",
      company_type: companyData.value.company_type || "company",
      bin_iin: companyData.value.bin_iin || "",
      legal_name: companyData.value.legal_name || "",
      director_name: companyData.value.director_name || "",
      email: companyData.value.email || "",
      phone: companyData.value.phone || "",
      website: companyData.value.website || "",
      logo: null,
      signature: null,
      stamp: null,
      country: companyData.value.country || "Казахстан",
      region: companyData.value.region || "",
      city: companyData.value.city || "",
      legal_address: companyData.value.legal_address || "",
      actual_address: companyData.value.actual_address || "",
      latitude: companyData.value.latitude || "",
      longitude: companyData.value.longitude || "",
      bank_name: companyData.value.bank_name || "",
      bank_account: companyData.value.bank_account || "",
      bank_account_secondary: companyData.value.bank_account_secondary || "",
      bank_bik: companyData.value.bank_bik || "",
      bank_iban: companyData.value.bank_iban || "",
      vat_enabled: Boolean(companyData.value.vat_enabled),
      vat_rate: companyData.value.vat_rate || "",
      social_links: {
        instagram: ((_a = companyData.value.social_links) == null ? void 0 : _a.instagram) || "",
        whatsapp: ((_b = companyData.value.social_links) == null ? void 0 : _b.whatsapp) || "",
        telegram: ((_c = companyData.value.social_links) == null ? void 0 : _c.telegram) || "",
        facebook: ((_d = companyData.value.social_links) == null ? void 0 : _d.facebook) || "",
        youtube: ((_e = companyData.value.social_links) == null ? void 0 : _e.youtube) || ""
      },
      sort: companyData.value.sort ?? 0,
      activity: Boolean(companyData.value.activity),
      left: Boolean(companyData.value.left),
      main: Boolean(companyData.value.main),
      right: Boolean(companyData.value.right),
      status: companyData.value.status || "draft",
      moderation_status: companyData.value.moderation_status ?? 0,
      moderation_note: companyData.value.moderation_note || "",
      published_at: companyData.value.published_at || "",
      show_from_at: companyData.value.show_from_at || "",
      show_to_at: companyData.value.show_to_at || "",
      views: companyData.value.views || 0,
      translations: buildTranslations()
    });
    const logoPreview = computed(() => storageUrl(companyData.value.logo));
    const signaturePreview = computed(() => storageUrl(companyData.value.signature));
    const stampPreview = computed(() => storageUrl(companyData.value.stamp));
    const currentTranslation = computed(() => {
      if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation();
      }
      return form.translations[activeLocale.value];
    });
    const getError = (key) => {
      return form.errors[`translations.${activeLocale.value}.${key}`];
    };
    const handleUrlInputFocus = () => {
      if (!form.url && currentTranslation.value.title) {
        form.url = transliterate(currentTranslation.value.title.toLowerCase());
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
        const descText = String(translation.short).replace(/(<([^>]+)>)/gi, "");
        translation.meta_desc = truncateText(descText, 200, true);
      }
    };
    const fileOrNull = (value) => {
      return value instanceof File ? value : null;
    };
    const submitForm = () => {
      form.transform((data) => ({
        ...data,
        activity: data.activity ? 1 : 0,
        left: data.left ? 1 : 0,
        main: data.main ? 1 : 0,
        right: data.right ? 1 : 0,
        vat_enabled: data.vat_enabled ? 1 : 0,
        vat_rate: data.vat_enabled && data.vat_rate !== "" ? data.vat_rate : null,
        logo: fileOrNull(data.logo),
        signature: fileOrNull(data.signature),
        stamp: fileOrNull(data.stamp),
        social_links: Object.fromEntries(
          Object.entries(data.social_links || {}).filter(([, value]) => String(value || "").trim() !== "")
        )
      }));
      form.post(route("admin.marketCompanies.update", { marketCompany: companyData.value.id }), {
        forceFormData: true,
        errorBag: "updateMarketCompany",
        preserveScroll: true,
        onSuccess: () => toast.success("Компания успешно обновлена!"),
        onError: (errors) => {
          console.error("Не удалось обновить компанию:", errors);
          const firstError = errors == null ? void 0 : errors[Object.keys(errors)[0]];
          toast.error(firstError || "Пожалуйста, проверьте правильность заполнения полей.");
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editMarketCompany")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editMarketCompany"))}: ${ssrInterpolate(pageTitle.value)} [ID: ${ssrInterpolate(companyData.value.id)}] `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editMarketCompany")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(companyData.value.id) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editMarketCompany")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(companyData.value.id) + "] ", 1)
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
              href: _ctx.route("admin.marketCompanies.index")
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
            _push2(`</div><form class="p-3 w-full"${_scopeId}><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).activity,
              "onUpdate:modelValue": ($event) => unref(form).activity = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "activity",
              text: unref(t)("activity"),
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
              type: "number",
              modelValue: unref(form).sort,
              "onUpdate:modelValue": ($event) => unref(form).sort = $event,
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2 lg:mt-0",
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
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
              modelValue: unref(form).right,
              "onUpdate:modelValue": ($event) => unref(form).right = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "right",
              text: unref(t)("right"),
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
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "subtitle",
              value: `${unref(t)("subtitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "subtitle",
              type: "text",
              modelValue: currentTranslation.value.subtitle,
              "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("subtitle")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "short",
              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.short || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
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
              height: 400
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("description")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "meta_title",
              value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
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
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "meta_keywords",
              value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
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
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "meta_desc",
              value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
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
            _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
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
              type: "text",
              modelValue: unref(form).url,
              "onUpdate:modelValue": ($event) => unref(form).url = $event,
              required: "",
              onFocus: handleUrlInputFocus
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.url
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "company_type" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("companyType"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("companyType")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<select id="company_type" class="w-full px-2 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"${_scopeId}><option value="company"${ssrIncludeBooleanAttr(Array.isArray(unref(form).company_type) ? ssrLooseContain(unref(form).company_type, "company") : ssrLooseEqual(unref(form).company_type, "company")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("company"))}</option><option value="entrepreneur"${ssrIncludeBooleanAttr(Array.isArray(unref(form).company_type) ? ssrLooseContain(unref(form).company_type, "entrepreneur") : ssrLooseEqual(unref(form).company_type, "entrepreneur")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("soleProprietor"))}</option><option value="individual"${ssrIncludeBooleanAttr(Array.isArray(unref(form).company_type) ? ssrLooseContain(unref(form).company_type, "individual") : ssrLooseEqual(unref(form).company_type, "individual")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("individual"))}</option></select>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.company_type
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "status",
              value: unref(t)("status")
            }, null, _parent2, _scopeId));
            _push2(`<select id="status" class="w-full px-2 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"${_scopeId}><option value="draft"${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, "draft") : ssrLooseEqual(unref(form).status, "draft")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="published"${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, "published") : ssrLooseEqual(unref(form).status, "published")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="archived"${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, "archived") : ssrLooseEqual(unref(form).status, "archived")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("statusArchived"))}</option></select>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.status
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "legal_name",
              value: unref(t)("legalName")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "legal_name",
              type: "text",
              modelValue: unref(form).legal_name,
              "onUpdate:modelValue": ($event) => unref(form).legal_name = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.legal_name
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "director_name",
              value: unref(t)("director")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "director_name",
              type: "text",
              modelValue: unref(form).director_name,
              "onUpdate:modelValue": ($event) => unref(form).director_name = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.director_name
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "bin_iin",
              value: unref(t)("billingTaxId")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "bin_iin",
              type: "text",
              modelValue: unref(form).bin_iin,
              "onUpdate:modelValue": ($event) => unref(form).bin_iin = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.bin_iin
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "website",
              value: unref(t)("site")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "website",
              type: "url",
              modelValue: unref(form).website,
              "onUpdate:modelValue": ($event) => unref(form).website = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.website
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "email",
              value: "Email"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "email",
              type: "email",
              modelValue: unref(form).email,
              "onUpdate:modelValue": ($event) => unref(form).email = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.email
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "phone",
              value: unref(t)("phone")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "phone",
              type: "text",
              modelValue: unref(form).phone,
              "onUpdate:modelValue": ($event) => unref(form).phone = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.phone
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "country",
              value: unref(t)("country")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "country",
              type: "text",
              modelValue: unref(form).country,
              "onUpdate:modelValue": ($event) => unref(form).country = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.country
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "region",
              value: unref(t)("region")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "region",
              type: "text",
              modelValue: unref(form).region,
              "onUpdate:modelValue": ($event) => unref(form).region = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.region
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "city",
              value: unref(t)("city")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "city",
              type: "text",
              modelValue: unref(form).city,
              "onUpdate:modelValue": ($event) => unref(form).city = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.city
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start lg:col-span-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "legal_address",
              value: unref(t)("addressLegal")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "legal_address",
              type: "text",
              modelValue: unref(form).legal_address,
              "onUpdate:modelValue": ($event) => unref(form).legal_address = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.legal_address
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start lg:col-span-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "actual_address",
              value: unref(t)("addressActual")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "actual_address",
              type: "text",
              modelValue: unref(form).actual_address,
              "onUpdate:modelValue": ($event) => unref(form).actual_address = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.actual_address
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$d, {
              latitude: unref(form).latitude,
              "onUpdate:latitude": ($event) => unref(form).latitude = $event,
              longitude: unref(form).longitude,
              "onUpdate:longitude": ($event) => unref(form).longitude = $event,
              "latitude-error": unref(form).errors.latitude,
              "longitude-error": unref(form).errors.longitude
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "bank_name",
              value: unref(t)("bank")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "bank_name",
              type: "text",
              modelValue: unref(form).bank_name,
              "onUpdate:modelValue": ($event) => unref(form).bank_name = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.bank_name
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "bank_bik",
              value: "БИК"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "bank_bik",
              type: "text",
              modelValue: unref(form).bank_bik,
              "onUpdate:modelValue": ($event) => unref(form).bank_bik = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.bank_bik
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "bank_account",
              value: `${unref(t)("bankAccount")} №1`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "bank_account",
              type: "text",
              modelValue: unref(form).bank_account,
              "onUpdate:modelValue": ($event) => unref(form).bank_account = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.bank_account
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "bank_account_secondary",
              value: `${unref(t)("bankAccount")} №2`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "bank_account_secondary",
              type: "text",
              modelValue: unref(form).bank_account_secondary,
              "onUpdate:modelValue": ($event) => unref(form).bank_account_secondary = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.bank_account_secondary
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start lg:col-span-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "bank_iban",
              value: "IBAN"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "bank_iban",
              type: "text",
              modelValue: unref(form).bank_iban,
              "onUpdate:modelValue": ($event) => unref(form).bank_iban = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.bank_iban
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-4 flex flex-col lg:flex-row gap-4 items-center justify-between"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).vat_enabled,
              "onUpdate:modelValue": ($event) => unref(form).vat_enabled = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "vat_enabled",
              text: unref(t)("vat"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "vat_rate",
              value: unref(t)("vatRate"),
              class: "text-sm h-8 flex items-center justify-end w-full"
            }, null, _parent2, _scopeId));
            _push2(`<input id="vat_rate" type="number" step="0.01"${ssrRenderAttr("value", unref(form).vat_rate)}${ssrIncludeBooleanAttr(!unref(form).vat_enabled) ? " disabled" : ""} class="w-full px-2 py-0.5 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 disabled:opacity-60"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.vat_rate
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$e, {
              modelValue: unref(form).logo,
              "onUpdate:modelValue": ($event) => unref(form).logo = $event,
              "initial-preview": logoPreview.value,
              label: unref(t)("companyLogo"),
              "button-text": unref(t)("selectLogo"),
              "empty-text": unref(t)("noImage"),
              accept: "image/png,image/jpeg,image/webp",
              error: unref(form).errors.logo,
              "preview-class": "h-24 w-36 object-cover rounded-sm\n                                           border border-slate-400"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$e, {
              modelValue: unref(form).signature,
              "onUpdate:modelValue": ($event) => unref(form).signature = $event,
              "initial-preview": signaturePreview.value,
              label: unref(t)("signaturePng"),
              "button-text": unref(t)("selectSignature"),
              "empty-text": unref(t)("noSignature"),
              accept: "image/png",
              error: unref(form).errors.signature,
              "preview-class": "h-24 w-36 object-contain rounded-sm\n                                           border border-slate-400 bg-white"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$e, {
              modelValue: unref(form).stamp,
              "onUpdate:modelValue": ($event) => unref(form).stamp = $event,
              "initial-preview": stampPreview.value,
              label: unref(t)("stampPng"),
              "button-text": unref(t)("selectStamp"),
              "empty-text": unref(t)("noStamp"),
              accept: "image/png",
              error: unref(form).errors.stamp,
              "preview-class": "h-24 w-36 object-contain rounded-sm\n                                           border border-slate-400 bg-white"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "published_at",
              value: unref(t)("publishedAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "published_at",
              type: "date",
              modelValue: unref(form).published_at,
              "onUpdate:modelValue": ($event) => unref(form).published_at = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.published_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "show_from_at",
              value: unref(t)("showFromAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "show_from_at",
              type: "datetime-local",
              modelValue: unref(form).show_from_at,
              "onUpdate:modelValue": ($event) => unref(form).show_from_at = $event
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
              type: "datetime-local",
              modelValue: unref(form).show_to_at,
              "onUpdate:modelValue": ($event) => unref(form).show_to_at = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.show_to_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="flex items-center justify-center mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.marketCompanies.index")
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
            _push2(ssrRenderComponent(_sfc_main$f, {
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
                      href: _ctx.route("admin.marketCompanies.index")
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
                          type: "number",
                          modelValue: unref(form).sort,
                          "onUpdate:modelValue": ($event) => unref(form).sort = $event,
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
                          maxlength: "255"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("title")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "subtitle",
                          value: `${unref(t)("subtitle")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "subtitle",
                          type: "text",
                          modelValue: currentTranslation.value.subtitle,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
                          maxlength: "255"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("subtitle")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode("div", { class: "flex justify-between w-full" }, [
                          createVNode(_sfc_main$4, {
                            for: "short",
                            value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.short || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
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
                          height: 400
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("description")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "meta_title",
                          value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["value"]),
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
                        createVNode(_sfc_main$4, {
                          for: "meta_keywords",
                          value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["value"]),
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
                        createVNode(_sfc_main$4, {
                          for: "meta_desc",
                          value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["value"]),
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
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, { for: "url" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" URL ")
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$8, {
                          id: "url",
                          type: "text",
                          modelValue: unref(form).url,
                          "onUpdate:modelValue": ($event) => unref(form).url = $event,
                          required: "",
                          onFocus: handleUrlInputFocus
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.url
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, { for: "company_type" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("companyType")), 1)
                          ]),
                          _: 1
                        }),
                        withDirectives(createVNode("select", {
                          id: "company_type",
                          "onUpdate:modelValue": ($event) => unref(form).company_type = $event,
                          class: "w-full px-2 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"
                        }, [
                          createVNode("option", { value: "company" }, toDisplayString(unref(t)("company")), 1),
                          createVNode("option", { value: "entrepreneur" }, toDisplayString(unref(t)("soleProprietor")), 1),
                          createVNode("option", { value: "individual" }, toDisplayString(unref(t)("individual")), 1)
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).company_type]
                        ]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.company_type
                        }, null, 8, ["message"])
                      ]),
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
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "legal_name",
                          value: unref(t)("legalName")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "legal_name",
                          type: "text",
                          modelValue: unref(form).legal_name,
                          "onUpdate:modelValue": ($event) => unref(form).legal_name = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.legal_name
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "director_name",
                          value: unref(t)("director")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "director_name",
                          type: "text",
                          modelValue: unref(form).director_name,
                          "onUpdate:modelValue": ($event) => unref(form).director_name = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.director_name
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "bin_iin",
                          value: unref(t)("billingTaxId")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "bin_iin",
                          type: "text",
                          modelValue: unref(form).bin_iin,
                          "onUpdate:modelValue": ($event) => unref(form).bin_iin = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.bin_iin
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "website",
                          value: unref(t)("site")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "website",
                          type: "url",
                          modelValue: unref(form).website,
                          "onUpdate:modelValue": ($event) => unref(form).website = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.website
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "email",
                          value: "Email"
                        }),
                        createVNode(_sfc_main$8, {
                          id: "email",
                          type: "email",
                          modelValue: unref(form).email,
                          "onUpdate:modelValue": ($event) => unref(form).email = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.email
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "phone",
                          value: unref(t)("phone")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "phone",
                          type: "text",
                          modelValue: unref(form).phone,
                          "onUpdate:modelValue": ($event) => unref(form).phone = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.phone
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "country",
                          value: unref(t)("country")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "country",
                          type: "text",
                          modelValue: unref(form).country,
                          "onUpdate:modelValue": ($event) => unref(form).country = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.country
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "region",
                          value: unref(t)("region")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "region",
                          type: "text",
                          modelValue: unref(form).region,
                          "onUpdate:modelValue": ($event) => unref(form).region = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.region
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "city",
                          value: unref(t)("city")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "city",
                          type: "text",
                          modelValue: unref(form).city,
                          "onUpdate:modelValue": ($event) => unref(form).city = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.city
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start lg:col-span-3" }, [
                        createVNode(_sfc_main$4, {
                          for: "legal_address",
                          value: unref(t)("addressLegal")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "legal_address",
                          type: "text",
                          modelValue: unref(form).legal_address,
                          "onUpdate:modelValue": ($event) => unref(form).legal_address = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.legal_address
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start lg:col-span-3" }, [
                        createVNode(_sfc_main$4, {
                          for: "actual_address",
                          value: unref(t)("addressActual")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "actual_address",
                          type: "text",
                          modelValue: unref(form).actual_address,
                          "onUpdate:modelValue": ($event) => unref(form).actual_address = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.actual_address
                        }, null, 8, ["message"])
                      ]),
                      createVNode(_sfc_main$d, {
                        latitude: unref(form).latitude,
                        "onUpdate:latitude": ($event) => unref(form).latitude = $event,
                        longitude: unref(form).longitude,
                        "onUpdate:longitude": ($event) => unref(form).longitude = $event,
                        "latitude-error": unref(form).errors.latitude,
                        "longitude-error": unref(form).errors.longitude
                      }, null, 8, ["latitude", "onUpdate:latitude", "longitude", "onUpdate:longitude", "latitude-error", "longitude-error"])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "bank_name",
                          value: unref(t)("bank")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "bank_name",
                          type: "text",
                          modelValue: unref(form).bank_name,
                          "onUpdate:modelValue": ($event) => unref(form).bank_name = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.bank_name
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "bank_bik",
                          value: "БИК"
                        }),
                        createVNode(_sfc_main$8, {
                          id: "bank_bik",
                          type: "text",
                          modelValue: unref(form).bank_bik,
                          "onUpdate:modelValue": ($event) => unref(form).bank_bik = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.bank_bik
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "bank_account",
                          value: `${unref(t)("bankAccount")} №1`
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "bank_account",
                          type: "text",
                          modelValue: unref(form).bank_account,
                          "onUpdate:modelValue": ($event) => unref(form).bank_account = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.bank_account
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "bank_account_secondary",
                          value: `${unref(t)("bankAccount")} №2`
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "bank_account_secondary",
                          type: "text",
                          modelValue: unref(form).bank_account_secondary,
                          "onUpdate:modelValue": ($event) => unref(form).bank_account_secondary = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.bank_account_secondary
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start lg:col-span-2" }, [
                        createVNode(_sfc_main$4, {
                          for: "bank_iban",
                          value: "IBAN"
                        }),
                        createVNode(_sfc_main$8, {
                          id: "bank_iban",
                          type: "text",
                          modelValue: unref(form).bank_iban,
                          "onUpdate:modelValue": ($event) => unref(form).bank_iban = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.bank_iban
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-4 flex flex-col lg:flex-row gap-4 items-center justify-between" }, [
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          modelValue: unref(form).vat_enabled,
                          "onUpdate:modelValue": ($event) => unref(form).vat_enabled = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "vat_enabled",
                          text: unref(t)("vat"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$4, {
                          for: "vat_rate",
                          value: unref(t)("vatRate"),
                          class: "text-sm h-8 flex items-center justify-end w-full"
                        }, null, 8, ["value"]),
                        withDirectives(createVNode("input", {
                          id: "vat_rate",
                          type: "number",
                          step: "0.01",
                          "onUpdate:modelValue": ($event) => unref(form).vat_rate = $event,
                          disabled: !unref(form).vat_enabled,
                          class: "w-full px-2 py-0.5 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 disabled:opacity-60"
                        }, null, 8, ["onUpdate:modelValue", "disabled"]), [
                          [vModelText, unref(form).vat_rate]
                        ]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.vat_rate
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4" }, [
                      createVNode(_sfc_main$e, {
                        modelValue: unref(form).logo,
                        "onUpdate:modelValue": ($event) => unref(form).logo = $event,
                        "initial-preview": logoPreview.value,
                        label: unref(t)("companyLogo"),
                        "button-text": unref(t)("selectLogo"),
                        "empty-text": unref(t)("noImage"),
                        accept: "image/png,image/jpeg,image/webp",
                        error: unref(form).errors.logo,
                        "preview-class": "h-24 w-36 object-cover rounded-sm\n                                           border border-slate-400"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "initial-preview", "label", "button-text", "empty-text", "error"]),
                      createVNode(_sfc_main$e, {
                        modelValue: unref(form).signature,
                        "onUpdate:modelValue": ($event) => unref(form).signature = $event,
                        "initial-preview": signaturePreview.value,
                        label: unref(t)("signaturePng"),
                        "button-text": unref(t)("selectSignature"),
                        "empty-text": unref(t)("noSignature"),
                        accept: "image/png",
                        error: unref(form).errors.signature,
                        "preview-class": "h-24 w-36 object-contain rounded-sm\n                                           border border-slate-400 bg-white"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "initial-preview", "label", "button-text", "empty-text", "error"]),
                      createVNode(_sfc_main$e, {
                        modelValue: unref(form).stamp,
                        "onUpdate:modelValue": ($event) => unref(form).stamp = $event,
                        "initial-preview": stampPreview.value,
                        label: unref(t)("stampPng"),
                        "button-text": unref(t)("selectStamp"),
                        "empty-text": unref(t)("noStamp"),
                        accept: "image/png",
                        error: unref(form).errors.stamp,
                        "preview-class": "h-24 w-36 object-contain rounded-sm\n                                           border border-slate-400 bg-white"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "initial-preview", "label", "button-text", "empty-text", "error"])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "published_at",
                          value: unref(t)("publishedAt")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "published_at",
                          type: "date",
                          modelValue: unref(form).published_at,
                          "onUpdate:modelValue": ($event) => unref(form).published_at = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.published_at
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "show_from_at",
                          value: unref(t)("showFromAt")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$8, {
                          id: "show_from_at",
                          type: "datetime-local",
                          modelValue: unref(form).show_from_at,
                          "onUpdate:modelValue": ($event) => unref(form).show_from_at = $event
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
                          type: "datetime-local",
                          modelValue: unref(form).show_to_at,
                          "onUpdate:modelValue": ($event) => unref(form).show_to_at = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.show_to_at
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center mt-4" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.marketCompanies.index")
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
                      createVNode(_sfc_main$f, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketCompanies/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
