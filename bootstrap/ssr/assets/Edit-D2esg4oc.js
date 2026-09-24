import { mergeProps, unref, useSSRContext, ref, computed, onMounted, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$3 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$i } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$e } from "./ClearMetaButton-zYsecbj9.js";
import { _ as _sfc_main$f } from "./MetatagsButton-CvAlOk1D.js";
import { _ as _sfc_main$5 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$4 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$d } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$c } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$7 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$a } from "./InputDecimalExt-B0XQGDJe.js";
import { _ as _sfc_main$6 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$9 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$8 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$h } from "./MultiImageUpload-Bg2ahSyk.js";
import { _ as _sfc_main$g } from "./MultiImageEdit-BZZHL0n1.js";
import { _ as _sfc_main$b } from "./TranslationTabs-czH7YSpu.js";
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
const _sfc_main$2 = {
  __name: "SelectStatus",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: String, default: "" },
    errorMessage: { type: String, default: "" }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}><label class="mb-1 block font-medium text-sm text-indigo-600 dark:text-sky-500">${ssrInterpolate(unref(t)("status"))}</label><select class="block w-full py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${ssrRenderAttr("value", __props.modelValue)}><option value="">${ssrInterpolate(unref(t)("select"))}</option><option value="draft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="published">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="archived">${ssrInterpolate(unref(t)("statusArchived"))}</option></select>`);
      if (__props.errorMessage) {
        _push(`<p class="text-sm text-red-600 dark:text-orange-200">${ssrInterpolate(__props.errorMessage)}</p>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolModule/Select/SelectStatus.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "SelectAvailability",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: String, default: "" },
    errorMessage: { type: String, default: "" }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}><label class="mb-1 block font-medium text-sm text-indigo-600 dark:text-sky-500">${ssrInterpolate(unref(t)("availability"))}</label><select class="block w-full py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${ssrRenderAttr("value", __props.modelValue)}><option value="">${ssrInterpolate(unref(t)("select"))}</option><option value="unlisted">${ssrInterpolate(unref(t)("availabilityUnlisted"))}</option><option value="public">${ssrInterpolate(unref(t)("availabilityPublic"))}</option><option value="private">${ssrInterpolate(unref(t)("availabilityPrivate"))}</option></select>`);
      if (__props.errorMessage) {
        _push(`<p class="text-sm text-red-600 dark:text-orange-200">${ssrInterpolate(__props.errorMessage)}</p>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolModule/Select/SelectAvailability.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Edit",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    module: { type: Object, required: true },
    courses: { type: Array, default: () => [] }
  },
  setup(__props) {
    var _a, _b;
    const toast = useToast();
    const { t } = useI18n();
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
      (props.module.translations || []).forEach((translation) => {
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
      const defaultLocale2 = props.currentLocale || ((_a2 = props.module.translation) == null ? void 0 : _a2.locale) || props.availableLocales[0] || "ru";
      if (!Object.keys(result).length) {
        result[defaultLocale2] = makeTranslation();
      }
      if (!result[defaultLocale2]) {
        result[defaultLocale2] = makeTranslation();
      }
      return result;
    };
    const defaultLocale = props.currentLocale || ((_a = props.module.translation) == null ? void 0 : _a.locale) || props.availableLocales[0] || "ru";
    const activeLocale = ref(defaultLocale);
    const form = useForm({
      _method: "PUT",
      school_course_id: props.module.school_course_id ?? ((_b = props.module.course) == null ? void 0 : _b.id) ?? null,
      sort: props.module.sort ?? 0,
      activity: Boolean(props.module.activity),
      slug: props.module.slug ?? "",
      difficulty: props.module.difficulty ?? 0,
      duration: Number(props.module.duration ?? 0),
      availability: props.module.availability ?? "public",
      status: props.module.status ?? "draft",
      published_at: props.module.published_at ?? "",
      lessons_count: props.module.lessons_count ?? 0,
      popularity: props.module.popularity ?? 0,
      rating_count: props.module.rating_count ?? 0,
      rating_avg: props.module.rating_avg ?? 0,
      views: props.module.views ?? 0,
      likes: props.module.likes ?? 0,
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
      return currentTranslation.value.title || ((_a2 = props.module.translation) == null ? void 0 : _a2.title) || props.module.title || `ID: ${props.module.id}`;
    });
    const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`];
    const formatDate = (dateStr) => {
      if (!dateStr) return "";
      const date = new Date(dateStr);
      if (isNaN(date.getTime())) return "";
      return date.toISOString().split("T")[0];
    };
    onMounted(() => {
      if (form.published_at) {
        form.published_at = formatDate(form.published_at);
      }
    });
    const dynamicOptionsLimit = (items) => {
      if (!items) return 10;
      return items.length + 10;
    };
    const courseOptions = computed(() => {
      return (props.courses || []).map((course) => {
        var _a2;
        return {
          id: course.id,
          label: `[ID: ${course.id}] ${((_a2 = course == null ? void 0 : course.translation) == null ? void 0 : _a2.title) || (course == null ? void 0 : course.slug) || `#${course.id}`}`
        };
      });
    });
    const selectedCourse = computed({
      get() {
        return courseOptions.value.find(
          (option) => Number(option.id) === Number(form.school_course_id)
        ) || null;
      },
      set(value) {
        form.school_course_id = (value == null ? void 0 : value.id) ?? null;
      }
    });
    const existingImages = ref(
      (props.module.images || []).filter((image) => image.webp_url || image.url || image.image_url).map((image) => ({
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
      existingImages.value = existingImages.value.filter((image) => image.id !== deletedId);
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
        translation.meta_title = truncateText(translation.title, 160);
      }
      if (!translation.meta_keywords && translation.short) {
        let text = String(translation.short).replace(/(<([^>]+)>)/gi, "");
        text = text.replace(/[.,!?;:()[\]{}"'«»]/g, "");
        const words = text.split(/\s+/).filter((word) => word && word.length >= 3).map((word) => word.toLowerCase()).filter((value, index, self) => self.indexOf(value) === index);
        translation.meta_keywords = truncateText(words.join(", "), 255);
      }
      if (translation.short && !translation.meta_desc) {
        const descText = String(translation.short).replace(/(<([^>]+)>)/gi, "");
        translation.meta_desc = truncateText(descText, 255, true);
      }
    };
    const submitForm = () => {
      form.transform((data) => {
        var _a2;
        const toNum = (val, digits = 2) => {
          if (val === "" || val === null || typeof val === "undefined") return null;
          const n = Number(val);
          return Number.isFinite(n) ? Number(n.toFixed(digits)) : null;
        };
        let difficulty = toNum(data.difficulty, 2);
        if (difficulty !== null) {
          if (difficulty < 0) difficulty = 0;
          if (difficulty > 5) difficulty = 5;
        }
        const transformed = {
          ...data,
          school_course_id: ((_a2 = selectedCourse.value) == null ? void 0 : _a2.id) ?? null,
          difficulty,
          duration: data.duration === "" || data.duration === null ? null : Number(data.duration),
          lessons_count: data.lessons_count === "" || data.lessons_count === null ? 0 : Number(data.lessons_count),
          popularity: data.popularity === "" || data.popularity === null ? 0 : Number(data.popularity),
          rating_count: data.rating_count === "" || data.rating_count === null ? 0 : Number(data.rating_count),
          rating_avg: data.rating_avg === "" || data.rating_avg === null ? 0 : Number(data.rating_avg),
          views: data.views === "" || data.views === null ? 0 : Number(data.views),
          likes: data.likes === "" || data.likes === null ? 0 : Number(data.likes),
          activity: data.activity ? 1 : 0
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
      form.post(route("admin.schoolModules.update", {
        schoolModule: props.module.id
      }), {
        errorBag: "editSchoolModule",
        preserveScroll: true,
        forceFormData: true,
        // Успешное обновление модуля
        onSuccess: () => {
          toast.success("Модуль успешно обновлён!");
          newImages.value = [];
          form.deletedImages = [];
        },
        // Обработка ошибок формы
        onError: (errors) => {
          console.error("Ошибка обновления модуля:", errors);
          const firstKey = Object.keys(errors || {})[0];
          toast.error(errors[firstKey] || "Проверьте корректность полей.");
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editModule")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editModule"))}: ${ssrInterpolate(pageTitle.value)} [ID: ${ssrInterpolate(props.module.id)}] `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editModule")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(props.module.id) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editModule")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(props.module.id) + "] ", 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$3, {
              href: _ctx.route("admin.schoolModules.index")
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
            _push2(`</div><form enctype="multipart/form-data" class="p-3 w-full"${_scopeId}><div class="pb-12"${_scopeId}><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              modelValue: unref(form).activity,
              "onUpdate:modelValue": ($event) => unref(form).activity = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "activity",
              text: unref(t)("activity"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              for: "sort",
              value: unref(t)("sort"),
              class: "text-sm"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "sort",
              type: "number",
              modelValue: unref(form).sort,
              "onUpdate:modelValue": ($event) => unref(form).sort = $event,
              modelModifiers: { number: true },
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).status,
              "onUpdate:modelValue": ($event) => unref(form).status = $event,
              errorMessage: unref(form).errors.status
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$1, {
              modelValue: unref(form).availability,
              "onUpdate:modelValue": ($event) => unref(form).availability = $event,
              errorMessage: unref(form).errors.availability
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              for: "published_at",
              value: unref(t)("publishedAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "published_at",
              type: "date",
              modelValue: unref(form).published_at,
              "onUpdate:modelValue": ($event) => unref(form).published_at = $event,
              class: "w-full max-w-56"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              message: unref(form).errors.published_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, { for: "difficulty" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("difficulty"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("difficulty")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              id: "difficulty",
              modelValue: unref(form).difficulty,
              "onUpdate:modelValue": ($event) => unref(form).difficulty = $event,
              min: 0,
              max: 5,
              step: 0.01,
              "fraction-digits": 2,
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              message: unref(form).errors.difficulty
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, { for: "duration" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("duration"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("duration")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "duration",
              type: "number",
              min: "0",
              modelValue: unref(form).duration,
              "onUpdate:modelValue": ($event) => unref(form).duration = $event,
              modelModifiers: { number: true },
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              message: unref(form).errors.duration
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              for: "school_course_id",
              value: unref(t)("course"),
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "school_course_id",
              modelValue: selectedCourse.value,
              "onUpdate:modelValue": ($event) => selectedCourse.value = $event,
              options: courseOptions.value,
              "options-limit": dynamicOptionsLimit(courseOptions.value),
              multiple: false,
              "close-on-select": true,
              "allow-empty": true,
              placeholder: unref(t)("select"),
              label: "label",
              "track-by": "id",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              class: "mt-2",
              message: unref(form).errors.school_course_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, { for: "slug" }, {
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
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "slug",
              type: "text",
              modelValue: unref(form).slug,
              "onUpdate:modelValue": ($event) => unref(form).slug = $event,
              required: "",
              autocomplete: "slug",
              onFocus: handleSlugFocus
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              class: "mt-2",
              message: unref(form).errors.slug
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$b, {
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
            _push2(ssrRenderComponent(_sfc_main$6, { for: "title" }, {
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
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.title || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "title",
              type: "text",
              modelValue: currentTranslation.value.title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
              maxlength: "255",
              required: ""
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              class: "mt-2",
              message: getError("title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              for: "subtitle",
              value: `${unref(t)("subtitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.subtitle || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$c, {
              modelValue: currentTranslation.value.subtitle,
              "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              class: "mt-2",
              message: getError("subtitle")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              for: "short",
              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.short || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$c, {
              modelValue: currentTranslation.value.short,
              "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              class: "mt-2",
              message: getError("short")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              for: "description",
              value: `${unref(t)("description")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              modelValue: currentTranslation.value.description,
              "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
              height: 500
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              class: "mt-2",
              message: getError("description")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              for: "meta_title",
              value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_title || "").length)} / 160 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "meta_title",
              type: "text",
              modelValue: currentTranslation.value.meta_title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
              maxlength: "160"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              class: "mt-2",
              message: getError("meta_title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              for: "meta_keywords",
              value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_keywords || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "meta_keywords",
              type: "text",
              modelValue: currentTranslation.value.meta_keywords,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
              maxlength: "255"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              class: "mt-2",
              message: getError("meta_keywords")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              for: "meta_desc",
              value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_desc || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$c, {
              modelValue: currentTranslation.value.meta_desc,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
              maxlength: "255",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              class: "mt-2",
              message: getError("meta_desc")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex justify-end gap-2 mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$e, { onClick: clearMetaFields }, {
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
            _push2(ssrRenderComponent(_sfc_main$f, { onClick: generateMetaFields }, {
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
            _push2(`</div></div><div class="mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$g, {
              images: existingImages.value,
              "onUpdate:images": handleExistingImagesUpdate,
              onDeleteImage: handleDeleteExistingImage
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$h, { "onUpdate:images": handleNewImagesUpdate }, null, _parent2, _scopeId));
            if (newImages.value.length) {
              _push2(`<div class="text-xs text-slate-600 dark:text-slate-300 mt-2"${_scopeId}>${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(newImages.value.length)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="flex items-center justify-center mt-4 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$3, {
              href: _ctx.route("admin.schoolModules.index")
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
              class: ["mb-0", { "opacity-25": unref(form).processing }],
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
                    createVNode(_sfc_main$3, {
                      href: _ctx.route("admin.schoolModules.index")
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
                    createVNode("div", { class: "pb-12" }, [
                      createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode(_sfc_main$4, {
                            modelValue: unref(form).activity,
                            "onUpdate:modelValue": ($event) => unref(form).activity = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$5, {
                            for: "activity",
                            text: unref(t)("activity"),
                            class: "text-sm h-8 flex items-center"
                          }, null, 8, ["text"])
                        ]),
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode(_sfc_main$6, {
                            for: "sort",
                            value: unref(t)("sort"),
                            class: "text-sm"
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$7, {
                            id: "sort",
                            type: "number",
                            modelValue: unref(form).sort,
                            "onUpdate:modelValue": ($event) => unref(form).sort = $event,
                            modelModifiers: { number: true },
                            class: "w-full lg:w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$8, {
                            message: unref(form).errors.sort
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                        createVNode(_sfc_main$2, {
                          modelValue: unref(form).status,
                          "onUpdate:modelValue": ($event) => unref(form).status = $event,
                          errorMessage: unref(form).errors.status
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "errorMessage"]),
                        createVNode(_sfc_main$1, {
                          modelValue: unref(form).availability,
                          "onUpdate:modelValue": ($event) => unref(form).availability = $event,
                          errorMessage: unref(form).errors.availability
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "errorMessage"])
                      ]),
                      createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$6, {
                            for: "published_at",
                            value: unref(t)("publishedAt")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$9, {
                            id: "published_at",
                            type: "date",
                            modelValue: unref(form).published_at,
                            "onUpdate:modelValue": ($event) => unref(form).published_at = $event,
                            class: "w-full max-w-56"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$8, {
                            message: unref(form).errors.published_at
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$6, { for: "difficulty" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("difficulty")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$a, {
                            id: "difficulty",
                            modelValue: unref(form).difficulty,
                            "onUpdate:modelValue": ($event) => unref(form).difficulty = $event,
                            min: 0,
                            max: 5,
                            step: 0.01,
                            "fraction-digits": 2,
                            class: "w-full lg:w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$8, {
                            message: unref(form).errors.difficulty
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$6, { for: "duration" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("duration")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$7, {
                            id: "duration",
                            type: "number",
                            min: "0",
                            modelValue: unref(form).duration,
                            "onUpdate:modelValue": ($event) => unref(form).duration = $event,
                            modelModifiers: { number: true },
                            class: "w-full lg:w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$8, {
                            message: unref(form).errors.duration
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$6, {
                          for: "school_course_id",
                          value: unref(t)("course"),
                          class: "mb-1"
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "school_course_id",
                          modelValue: selectedCourse.value,
                          "onUpdate:modelValue": ($event) => selectedCourse.value = $event,
                          options: courseOptions.value,
                          "options-limit": dynamicOptionsLimit(courseOptions.value),
                          multiple: false,
                          "close-on-select": true,
                          "allow-empty": true,
                          placeholder: unref(t)("select"),
                          label: "label",
                          "track-by": "id",
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                        createVNode(_sfc_main$8, {
                          class: "mt-2",
                          message: unref(form).errors.school_course_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$6, { for: "slug" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("slug")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$9, {
                          id: "slug",
                          type: "text",
                          modelValue: unref(form).slug,
                          "onUpdate:modelValue": ($event) => unref(form).slug = $event,
                          required: "",
                          autocomplete: "slug",
                          onFocus: handleSlugFocus
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$8, {
                          class: "mt-2",
                          message: unref(form).errors.slug
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm" }, [
                        createVNode(_sfc_main$b, {
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
                            createVNode(_sfc_main$6, { for: "title" }, {
                              default: withCtx(() => [
                                createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                                createTextVNode(" " + toDisplayString(unref(t)("title")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                              ]),
                              _: 1
                            }),
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.title || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$9, {
                            id: "title",
                            type: "text",
                            modelValue: currentTranslation.value.title,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
                            maxlength: "255",
                            required: ""
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$8, {
                            class: "mt-2",
                            message: getError("title")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode("div", { class: "flex justify-between w-full" }, [
                            createVNode(_sfc_main$6, {
                              for: "subtitle",
                              value: `${unref(t)("subtitle")} [${activeLocale.value.toUpperCase()}]`
                            }, null, 8, ["value"]),
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.subtitle || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$c, {
                            modelValue: currentTranslation.value.subtitle,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$8, {
                            class: "mt-2",
                            message: getError("subtitle")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode("div", { class: "flex justify-between w-full" }, [
                            createVNode(_sfc_main$6, {
                              for: "short",
                              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
                            }, null, 8, ["value"]),
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.short || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$c, {
                            modelValue: currentTranslation.value.short,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$8, {
                            class: "mt-2",
                            message: getError("short")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode(_sfc_main$6, {
                            for: "description",
                            value: `${unref(t)("description")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$d, {
                            modelValue: currentTranslation.value.description,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
                            height: 500
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$8, {
                            class: "mt-2",
                            message: getError("description")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode("div", { class: "flex justify-between w-full" }, [
                            createVNode(_sfc_main$6, {
                              for: "meta_title",
                              value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
                            }, null, 8, ["value"]),
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_title || "").length) + " / 160 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$9, {
                            id: "meta_title",
                            type: "text",
                            modelValue: currentTranslation.value.meta_title,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
                            maxlength: "160"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$8, {
                            class: "mt-2",
                            message: getError("meta_title")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode("div", { class: "flex justify-between w-full" }, [
                            createVNode(_sfc_main$6, {
                              for: "meta_keywords",
                              value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
                            }, null, 8, ["value"]),
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_keywords || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$9, {
                            id: "meta_keywords",
                            type: "text",
                            modelValue: currentTranslation.value.meta_keywords,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
                            maxlength: "255"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$8, {
                            class: "mt-2",
                            message: getError("meta_keywords")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode("div", { class: "flex justify-between w-full" }, [
                            createVNode(_sfc_main$6, {
                              for: "meta_desc",
                              value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
                            }, null, 8, ["value"]),
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_desc || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$c, {
                            modelValue: currentTranslation.value.meta_desc,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
                            maxlength: "255",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$8, {
                            class: "mt-2",
                            message: getError("meta_desc")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex justify-end gap-2 mt-4" }, [
                          createVNode(_sfc_main$e, {
                            onClick: withModifiers(clearMetaFields, ["prevent"])
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("clearMetaFields")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$f, {
                            onClick: withModifiers(generateMetaFields, ["prevent"])
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("generateMetaTags")), 1)
                            ]),
                            _: 1
                          })
                        ])
                      ]),
                      createVNode("div", { class: "mt-4" }, [
                        createVNode(_sfc_main$g, {
                          images: existingImages.value,
                          "onUpdate:images": handleExistingImagesUpdate,
                          onDeleteImage: handleDeleteExistingImage
                        }, null, 8, ["images"])
                      ]),
                      createVNode("div", { class: "mt-4" }, [
                        createVNode(_sfc_main$h, { "onUpdate:images": handleNewImagesUpdate }),
                        newImages.value.length ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-xs text-slate-600 dark:text-slate-300 mt-2"
                        }, toDisplayString(unref(t)("images")) + ": " + toDisplayString(newImages.value.length), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center mt-4 gap-3" }, [
                      createVNode(_sfc_main$3, {
                        href: _ctx.route("admin.schoolModules.index")
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
                        class: ["mb-0", { "opacity-25": unref(form).processing }],
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolModules/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
