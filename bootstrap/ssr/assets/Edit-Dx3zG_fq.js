import { computed, ref, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, withDirectives, Fragment, renderList, vModelSelect, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderList, ssrRenderAttr } from "vue/server-renderer";
import { useToast } from "vue-toastification";
import { useI18n } from "vue-i18n";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import { useForm } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$i } from "./PrimaryButton-B3InEAXg.js";
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
import { _ as _sfc_main$d } from "./EditImageFileInput-DmYXxrrC.js";
import { _ as _sfc_main$e } from "./MultiImagePresetEdit-aX3-dCXJ.js";
import { _ as _sfc_main$f } from "./MultiImagePresetUpload-Dhpb09Vk.js";
import { _ as _sfc_main$g } from "./MultiImageEdit-BZZHL0n1.js";
import { _ as _sfc_main$h } from "./MultiImageUpload-Bg2ahSyk.js";
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
    imageProcessorEnabled: { type: Boolean, default: true },
    shop: { type: Object, required: true },
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    companies: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    var _a, _b, _c, _d, _e, _f;
    const toast = useToast();
    const { t } = useI18n();
    const props = __props;
    const shopData = computed(() => {
      var _a2;
      return ((_a2 = props.shop) == null ? void 0 : _a2.data) ?? props.shop;
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
    const defaultLocale = props.currentLocale || ((_a = shopData.value.translation) == null ? void 0 : _a.locale) || props.availableLocales[0] || "ru";
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
      (shopData.value.translations || []).forEach((translation) => {
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
      user_id: shopData.value.user_id || null,
      market_company_id: shopData.value.market_company_id || "",
      url: shopData.value.url || "",
      email: shopData.value.email || "",
      phone: shopData.value.phone || "",
      logo: null,
      social_links: {
        instagram: ((_b = shopData.value.social_links) == null ? void 0 : _b.instagram) || "",
        whatsapp: ((_c = shopData.value.social_links) == null ? void 0 : _c.whatsapp) || "",
        telegram: ((_d = shopData.value.social_links) == null ? void 0 : _d.telegram) || "",
        facebook: ((_e = shopData.value.social_links) == null ? void 0 : _e.facebook) || "",
        youtube: ((_f = shopData.value.social_links) == null ? void 0 : _f.youtube) || ""
      },
      sort: shopData.value.sort ?? 0,
      activity: Boolean(shopData.value.activity),
      left: Boolean(shopData.value.left),
      main: Boolean(shopData.value.main),
      right: Boolean(shopData.value.right),
      status: shopData.value.status || "draft",
      moderation_status: shopData.value.moderation_status ?? 0,
      moderation_note: shopData.value.moderation_note || "",
      published_at: shopData.value.published_at || "",
      show_from_at: shopData.value.show_from_at || "",
      show_to_at: shopData.value.show_to_at || "",
      translations: buildTranslations(),
      deletedImages: []
    });
    const logoPreview = computed(() => storageUrl(shopData.value.logo));
    const currentTranslation = computed(() => {
      if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation();
      }
      return form.translations[activeLocale.value];
    });
    const pageTitle = computed(() => {
      var _a2;
      return currentTranslation.value.title || ((_a2 = shopData.value.translation) == null ? void 0 : _a2.title) || `ID: ${shopData.value.id}`;
    });
    const getError = (key) => {
      return form.errors[`translations.${activeLocale.value}.${key}`];
    };
    const getCompanyTitle = (company) => {
      var _a2;
      return ((_a2 = company == null ? void 0 : company.translation) == null ? void 0 : _a2.title) || (company == null ? void 0 : company.legal_name) || `ID: ${company == null ? void 0 : company.id}`;
    };
    const galleryPreset = {
      key: "rectangle_large",
      shape: "rectangle",
      width: 1200,
      height: 800,
      image_rotation_enabled: true,
      crop_rotation_enabled: true
    };
    const existingImages = ref(
      (shopData.value.images || []).filter((image) => image.webp_url || image.thumb_url || image.url).map((image) => ({
        id: image.id,
        url: image.webp_url || image.thumb_url || image.url,
        order: image.order ?? 0,
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
      existingImages.value = existingImages.value.filter((img) => img.id !== deletedId);
    };
    const handleNewImagesUpdate = (images) => {
      newImages.value = images || [];
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
      form.transform((data) => {
        const transformed = {
          ...data,
          activity: data.activity ? 1 : 0,
          left: data.left ? 1 : 0,
          main: data.main ? 1 : 0,
          right: data.right ? 1 : 0,
          logo: fileOrNull(data.logo),
          social_links: Object.fromEntries(
            Object.entries(data.social_links || {}).filter(([, value]) => String(value || "").trim() !== "")
          )
        };
        delete transformed.images;
        delete transformed.deletedImages;
        let i = 0;
        existingImages.value.forEach((img) => {
          transformed[`images[${i}][id]`] = img.id;
          transformed[`images[${i}][order]`] = img.order ?? 0;
          transformed[`images[${i}][alt]`] = img.alt ?? "";
          transformed[`images[${i}][caption]`] = img.caption ?? "";
          if (img.file instanceof File) {
            transformed[`images[${i}][file]`] = img.file;
          }
          i++;
        });
        newImages.value.forEach((img) => {
          if (img.file) {
            transformed[`images[${i}][file]`] = img.file;
            transformed[`images[${i}][order]`] = img.order ?? 0;
            transformed[`images[${i}][alt]`] = img.alt ?? "";
            transformed[`images[${i}][caption]`] = img.caption ?? "";
            i++;
          }
        });
        form.deletedImages.forEach((id, index) => {
          transformed[`deletedImages[${index}]`] = id;
        });
        return transformed;
      });
      form.post(route("admin.marketShops.update", { marketShop: shopData.value.id }), {
        forceFormData: true,
        errorBag: "updateMarketShop",
        preserveScroll: true,
        onSuccess: () => {
          toast.success("Магазин успешно обновлён!");
          newImages.value = [];
          form.deletedImages = [];
        },
        onError: (errors) => {
          console.error("Не удалось обновить магазин:", errors);
          const firstError = errors == null ? void 0 : errors[Object.keys(errors)[0]];
          toast.error(firstError || "Пожалуйста, проверьте правильность заполнения полей.");
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editMarketShop")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editMarketShop"))}: ${ssrInterpolate(pageTitle.value)} [ID: ${ssrInterpolate(shopData.value.id)}] `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editMarketShop")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(shopData.value.id) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editMarketShop")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(shopData.value.id) + "] ", 1)
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
              href: _ctx.route("admin.marketShops.index")
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
            _push2(`</div><form enctype="multipart/form-data" class="p-3 w-full"${_scopeId}><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
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
            _push2(ssrRenderComponent(_sfc_main$4, { for: "market_company_id" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("company"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("company")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<select id="market_company_id" required class="w-full px-2 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"${_scopeId}><option value=""${ssrIncludeBooleanAttr(Array.isArray(unref(form).market_company_id) ? ssrLooseContain(unref(form).market_company_id, "") : ssrLooseEqual(unref(form).market_company_id, "")) ? " selected" : ""}${_scopeId}>— ${ssrInterpolate(unref(t)("select"))} —</option><!--[-->`);
            ssrRenderList(__props.companies, (company) => {
              _push2(`<option${ssrRenderAttr("value", company.id)}${ssrIncludeBooleanAttr(Array.isArray(unref(form).market_company_id) ? ssrLooseContain(unref(form).market_company_id, company.id) : ssrLooseEqual(unref(form).market_company_id, company.id)) ? " selected" : ""}${_scopeId}> [ID:${ssrInterpolate(company.id)}] ${ssrInterpolate(getCompanyTitle(company))}</option>`);
            });
            _push2(`<!--]--></select>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.market_company_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
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
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$d, {
              modelValue: unref(form).logo,
              "onUpdate:modelValue": ($event) => unref(form).logo = $event,
              "initial-preview": logoPreview.value,
              label: unref(t)("logo"),
              "button-text": unref(t)("selectLogo"),
              "empty-text": unref(t)("noImage"),
              accept: "image/png,image/jpeg,image/webp",
              error: unref(form).errors.logo,
              "preview-class": "h-24 w-36 object-cover rounded-sm\n                                           border border-slate-400"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm"${_scopeId}><div class="text-sm font-semibold text-slate-700 dark:text-slate-200 mb-3"${_scopeId}>${ssrInterpolate(unref(t)("socialLinks"))}</div><div class="grid grid-cols-1 lg:grid-cols-3 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "instagram",
              value: "Instagram"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "instagram",
              type: "url",
              modelValue: unref(form).social_links.instagram,
              "onUpdate:modelValue": ($event) => unref(form).social_links.instagram = $event
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "whatsapp",
              value: "WhatsApp"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "whatsapp",
              type: "url",
              modelValue: unref(form).social_links.whatsapp,
              "onUpdate:modelValue": ($event) => unref(form).social_links.whatsapp = $event
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "telegram",
              value: "Telegram"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "telegram",
              type: "url",
              modelValue: unref(form).social_links.telegram,
              "onUpdate:modelValue": ($event) => unref(form).social_links.telegram = $event
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "facebook",
              value: "Facebook"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "facebook",
              type: "url",
              modelValue: unref(form).social_links.facebook,
              "onUpdate:modelValue": ($event) => unref(form).social_links.facebook = $event
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "youtube",
              value: "YouTube"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "youtube",
              type: "url",
              modelValue: unref(form).social_links.youtube,
              "onUpdate:modelValue": ($event) => unref(form).social_links.youtube = $event
            }, null, _parent2, _scopeId));
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.social_links
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
            _push2(`</div></div><div class="mt-4"${_scopeId}>`);
            if (__props.imageProcessorEnabled) {
              _push2(`<!--[-->`);
              _push2(ssrRenderComponent(_sfc_main$e, {
                images: existingImages.value,
                preset: galleryPreset,
                "onUpdate:images": handleExistingImagesUpdate,
                "onDelete:image": handleDeleteExistingImage
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$f, {
                preset: galleryPreset,
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
              _push2(ssrRenderComponent(_sfc_main$h, { "onUpdate:images": handleNewImagesUpdate }, null, _parent2, _scopeId));
              _push2(`<!--]-->`);
            }
            if (newImages.value.length) {
              _push2(`<div class="text-xs text-slate-600 dark:text-slate-300 mt-2"${_scopeId}>${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(newImages.value.length)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="flex items-center justify-center mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.marketShops.index")
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
                      href: _ctx.route("admin.marketShops.index")
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
                    enctype: "multipart/form-data",
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
                        createVNode(_sfc_main$4, { for: "market_company_id" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("company")), 1)
                          ]),
                          _: 1
                        }),
                        withDirectives(createVNode("select", {
                          id: "market_company_id",
                          "onUpdate:modelValue": ($event) => unref(form).market_company_id = $event,
                          required: "",
                          class: "w-full px-2 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600 border border-slate-400 dark:border-slate-600"
                        }, [
                          createVNode("option", { value: "" }, "— " + toDisplayString(unref(t)("select")) + " —", 1),
                          (openBlock(true), createBlock(Fragment, null, renderList(__props.companies, (company) => {
                            return openBlock(), createBlock("option", {
                              key: company.id,
                              value: company.id
                            }, " [ID:" + toDisplayString(company.id) + "] " + toDisplayString(getCompanyTitle(company)), 9, ["value"]);
                          }), 128))
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).market_company_id]
                        ]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.market_company_id
                        }, null, 8, ["message"])
                      ]),
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
                      ]),
                      createVNode(_sfc_main$d, {
                        modelValue: unref(form).logo,
                        "onUpdate:modelValue": ($event) => unref(form).logo = $event,
                        "initial-preview": logoPreview.value,
                        label: unref(t)("logo"),
                        "button-text": unref(t)("selectLogo"),
                        "empty-text": unref(t)("noImage"),
                        accept: "image/png,image/jpeg,image/webp",
                        error: unref(form).errors.logo,
                        "preview-class": "h-24 w-36 object-cover rounded-sm\n                                           border border-slate-400"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "initial-preview", "label", "button-text", "empty-text", "error"])
                    ]),
                    createVNode("div", { class: "my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm" }, [
                      createVNode("div", { class: "text-sm font-semibold text-slate-700 dark:text-slate-200 mb-3" }, toDisplayString(unref(t)("socialLinks")), 1),
                      createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-3 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "instagram",
                            value: "Instagram"
                          }),
                          createVNode(_sfc_main$8, {
                            id: "instagram",
                            type: "url",
                            modelValue: unref(form).social_links.instagram,
                            "onUpdate:modelValue": ($event) => unref(form).social_links.instagram = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "whatsapp",
                            value: "WhatsApp"
                          }),
                          createVNode(_sfc_main$8, {
                            id: "whatsapp",
                            type: "url",
                            modelValue: unref(form).social_links.whatsapp,
                            "onUpdate:modelValue": ($event) => unref(form).social_links.whatsapp = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "telegram",
                            value: "Telegram"
                          }),
                          createVNode(_sfc_main$8, {
                            id: "telegram",
                            type: "url",
                            modelValue: unref(form).social_links.telegram,
                            "onUpdate:modelValue": ($event) => unref(form).social_links.telegram = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "facebook",
                            value: "Facebook"
                          }),
                          createVNode(_sfc_main$8, {
                            id: "facebook",
                            type: "url",
                            modelValue: unref(form).social_links.facebook,
                            "onUpdate:modelValue": ($event) => unref(form).social_links.facebook = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "youtube",
                            value: "YouTube"
                          }),
                          createVNode(_sfc_main$8, {
                            id: "youtube",
                            type: "url",
                            modelValue: unref(form).social_links.youtube,
                            "onUpdate:modelValue": ($event) => unref(form).social_links.youtube = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"])
                        ])
                      ]),
                      createVNode(_sfc_main$6, {
                        class: "mt-2",
                        message: unref(form).errors.social_links
                      }, null, 8, ["message"])
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
                    createVNode("div", { class: "mt-4" }, [
                      __props.imageProcessorEnabled ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                        createVNode(_sfc_main$e, {
                          images: existingImages.value,
                          preset: galleryPreset,
                          "onUpdate:images": handleExistingImagesUpdate,
                          "onDelete:image": handleDeleteExistingImage
                        }, null, 8, ["images"]),
                        createVNode(_sfc_main$f, {
                          preset: galleryPreset,
                          "onUpdate:images": handleNewImagesUpdate
                        })
                      ], 64)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                        createVNode(_sfc_main$g, {
                          images: existingImages.value,
                          "onUpdate:images": handleExistingImagesUpdate,
                          "onDelete:image": handleDeleteExistingImage
                        }, null, 8, ["images"]),
                        createVNode(_sfc_main$h, { "onUpdate:images": handleNewImagesUpdate })
                      ], 64)),
                      newImages.value.length ? (openBlock(), createBlock("div", {
                        key: 2,
                        class: "text-xs text-slate-600 dark:text-slate-300 mt-2"
                      }, toDisplayString(unref(t)("images")) + ": " + toDisplayString(newImages.value.length), 1)) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "flex items-center justify-center mt-4" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.marketShops.index")
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Market/MarketShops/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
