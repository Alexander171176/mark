import { ref, computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, Fragment, renderList, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$e } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$4 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$7 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$6 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$b } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$5 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$8 } from "./InputDecimal-25_UZ0Jr.js";
import { _ as _sfc_main$d } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$a } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$c } from "./MetatagsButton-CvAlOk1D.js";
import { _ as _sfc_main$f } from "./MultiImageUpload-Bg2ahSyk.js";
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
  __name: "Create",
  __ssrInlineRender: true,
  props: {
    users: {
      type: Array,
      default: () => []
    },
    targetLocale: {
      type: String,
      default: "ru"
    },
    availableLocales: {
      type: Array,
      default: () => ["ru", "en", "kk"]
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const selectedInstructorProfile = ref(null);
    const userOptions = computed(
      () => props.users.map((user) => ({
        id: user.id,
        label: user.name || `#${user.id}`
      }))
    );
    const makeTranslation = () => ({
      title: "",
      short: "",
      bio: "",
      meta_title: "",
      meta_keywords: "",
      meta_desc: ""
    });
    const defaultLocale = props.targetLocale || "ru";
    const activeLocale = ref(defaultLocale);
    const form = useForm({
      activity: true,
      sort: 0,
      slug: "",
      user_id: null,
      experience_years: 0,
      hourly_rate: "",
      rating_count: 0,
      rating_avg: "",
      social_links: {},
      images: [],
      translations: {
        [defaultLocale]: makeTranslation()
      }
    });
    const currentTranslation = computed(() => {
      if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation();
      }
      return form.translations[activeLocale.value];
    });
    const getError = (key) => {
      return form.errors[`translations.${activeLocale.value}.${key}`];
    };
    const newImages = ref([]);
    const handleNewImagesUpdate = (images) => {
      newImages.value = images;
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
    const socialRows = ref([]);
    const addSocialRow = () => {
      socialRows.value.push({
        platform: "",
        url: ""
      });
    };
    const removeSocialRow = (idx) => {
      socialRows.value.splice(idx, 1);
    };
    const buildSocialLinks = () => {
      const links = {};
      socialRows.value.forEach((row) => {
        const platform = (row.platform || "").trim();
        const url = (row.url || "").trim();
        if (!platform || !url) return;
        links[platform] = url;
      });
      return links;
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
    const submit = () => {
      const socialLinks = buildSocialLinks();
      form.transform((data) => {
        var _a;
        const toNum = (val, digits = 2) => {
          if (val === "" || val === null || typeof val === "undefined") {
            return null;
          }
          const n = Number(val);
          return Number.isFinite(n) ? Number(n.toFixed(digits)) : null;
        };
        let rating = toNum(data.rating_avg, 2);
        if (rating !== null) {
          if (rating < 0) rating = 0;
          if (rating > 5) rating = 5;
        }
        return {
          ...data,
          user_id: ((_a = selectedInstructorProfile.value) == null ? void 0 : _a.id) ?? null,
          activity: data.activity ? 1 : 0,
          hourly_rate: toNum(data.hourly_rate, 2),
          rating_avg: rating,
          social_links: socialLinks,
          images: newImages.value.map((img) => ({
            file: img.file,
            order: img.order ?? 0,
            alt: img.alt ?? "",
            caption: img.caption ?? ""
          }))
        };
      });
      form.post(route("admin.schoolInstructorProfiles.store"), {
        errorBag: "createSchoolInstructorProfile",
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
          toast.success("Инструктор успешно создан!");
        },
        onError: (errors) => {
          console.error("Не удалось отправить форму:", errors);
          const firstKey = Object.keys(errors || {})[0];
          toast.error(errors[firstKey] || "Проверьте корректность полей.");
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("createInstructor")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("createInstructor"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("createInstructor")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("createInstructor")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolInstructorProfiles.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
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
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}><div class="h-8 flex items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "sort",
              value: unref(t)("sort"),
              class: "text-sm"
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "sort",
              type: "number",
              modelValue: unref(form).sort,
              "onUpdate:modelValue": ($event) => unref(form).sort = $event,
              modelModifiers: { number: true },
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2 lg:mt-0",
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
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
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "slug",
              type: "text",
              modelValue: unref(form).slug,
              "onUpdate:modelValue": ($event) => unref(form).slug = $event,
              autocomplete: "off",
              class: "w-full",
              onFocus: handleSlugFocus,
              required: ""
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.slug
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "user_id",
              value: unref(t)("instructor"),
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "user_id",
              modelValue: selectedInstructorProfile.value,
              "onUpdate:modelValue": ($event) => selectedInstructorProfile.value = $event,
              options: userOptions.value,
              multiple: false,
              "close-on-select": true,
              placeholder: unref(t)("select"),
              label: "label",
              "track-by": "id",
              class: "min-w-[240px] w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.user_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "experience_years" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("experienceYears"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("experienceYears")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "experience_years",
              type: "number",
              min: "0",
              modelValue: unref(form).experience_years,
              "onUpdate:modelValue": ($event) => unref(form).experience_years = $event,
              modelModifiers: { number: true },
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.experience_years
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "hourly_rate" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("hourlyRate"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("hourlyRate")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "hourly_rate",
              modelValue: unref(form).hourly_rate,
              "onUpdate:modelValue": ($event) => unref(form).hourly_rate = $event,
              min: 0,
              step: 0.01,
              "fraction-digits": 2,
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.hourly_rate
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}><div class="h-8 flex items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "rating_count",
              value: unref(t)("ratingCount"),
              class: "text-sm"
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "rating_count",
              type: "number",
              min: "0",
              modelValue: unref(form).rating_count,
              "onUpdate:modelValue": ($event) => unref(form).rating_count = $event,
              modelModifiers: { number: true },
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2 lg:mt-0",
              message: unref(form).errors.rating_count
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}><div class="h-8 flex items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "rating_avg",
              value: unref(t)("ratingAvg"),
              class: "text-sm"
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "rating_avg",
              modelValue: unref(form).rating_avg,
              "onUpdate:modelValue": ($event) => unref(form).rating_avg = $event,
              min: 0,
              max: 5,
              step: 0.01,
              "fraction-digits": 2,
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2 lg:mt-0",
              message: unref(form).errors.rating_avg
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm"${_scopeId}>`);
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
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "title",
              type: "text",
              modelValue: currentTranslation.value.title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
              required: "",
              autocomplete: "off"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "short",
              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.short || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$a, {
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
              for: "bio",
              value: `${unref(t)("bio")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$b, {
              modelValue: currentTranslation.value.bio,
              "onUpdate:modelValue": ($event) => currentTranslation.value.bio = $event,
              height: 500
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("bio")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "meta_title",
              value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_title || "").length)} / 160 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "meta_title",
              type: "text",
              modelValue: currentTranslation.value.meta_title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
              maxlength: "160",
              autocomplete: "meta_title"
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
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_keywords || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "meta_keywords",
              type: "text",
              modelValue: currentTranslation.value.meta_keywords,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
              maxlength: "255",
              autocomplete: "meta_keywords"
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
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_desc || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$a, {
              modelValue: currentTranslation.value.meta_desc,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
              maxlength: "255",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("meta_desc")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex justify-end mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$c, { onClick: generateMetaFields }, {
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
            _push2(`</div></div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "social",
              value: unref(t)("socialLinks")
            }, null, _parent2, _scopeId));
            _push2(`<div class="w-full space-y-2"${_scopeId}><!--[-->`);
            ssrRenderList(socialRows.value, (row, idx) => {
              _push2(`<div class="flex flex-col lg:flex-row gap-2 items-start lg:items-center"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$7, {
                id: `social_platform_${idx}`,
                modelValue: row.platform,
                "onUpdate:modelValue": ($event) => row.platform = $event,
                placeholder: unref(t)("platform"),
                class: "w-full lg:w-1/3",
                autocomplete: "off"
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                id: `social_url_${idx}`,
                modelValue: row.url,
                "onUpdate:modelValue": ($event) => row.url = $event,
                placeholder: unref(t)("link") || "https://...",
                class: "w-full lg:flex-1",
                autocomplete: "off"
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$d, {
                title: unref(t)("delete"),
                onClick: ($event) => removeSocialRow(idx)
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            });
            _push2(`<!--]-->`);
            _push2(ssrRenderComponent(_sfc_main$e, {
              type: "button",
              class: "mt-2 float-end",
              onClick: addSocialRow
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100" viewBox="0 0 24 24"${_scopeId2}><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100",
                      viewBox: "0 0 24 24"
                    }, [
                      createVNode("path", {
                        d: "M12 5v14M5 12h14",
                        stroke: "currentColor",
                        "stroke-width": "2",
                        "stroke-linecap": "round"
                      })
                    ]))
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("addLink"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addLink")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.social_links
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$f, { "onUpdate:images": handleNewImagesUpdate }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="flex items-center justify-center mt-4 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolInstructorProfiles.index")
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
            _push2(ssrRenderComponent(_sfc_main$e, {
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
              createVNode("div", { class: "px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-2" }, [
                    createVNode(_sfc_main$1, {
                      href: _ctx.route("admin.schoolInstructorProfiles.index")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                        ]))
                      ]),
                      default: withCtx(() => [
                        createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ]),
                  createVNode("form", {
                    onSubmit: withModifiers(submit, ["prevent"]),
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
                          }, null, 8, ["text"])
                        ]),
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode("div", { class: "h-8 flex items-center" }, [
                            createVNode(_sfc_main$4, {
                              for: "sort",
                              value: unref(t)("sort"),
                              class: "text-sm"
                            }, null, 8, ["value"])
                          ]),
                          createVNode(_sfc_main$5, {
                            id: "sort",
                            type: "number",
                            modelValue: unref(form).sort,
                            "onUpdate:modelValue": ($event) => unref(form).sort = $event,
                            modelModifiers: { number: true },
                            class: "w-full lg:w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2 lg:mt-0",
                            message: unref(form).errors.sort
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, { for: "slug" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("slug")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$7, {
                          id: "slug",
                          type: "text",
                          modelValue: unref(form).slug,
                          "onUpdate:modelValue": ($event) => unref(form).slug = $event,
                          autocomplete: "off",
                          class: "w-full",
                          onFocus: handleSlugFocus,
                          required: ""
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.slug
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "user_id",
                          value: unref(t)("instructor"),
                          class: "mb-1"
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "user_id",
                          modelValue: selectedInstructorProfile.value,
                          "onUpdate:modelValue": ($event) => selectedInstructorProfile.value = $event,
                          options: userOptions.value,
                          multiple: false,
                          "close-on-select": true,
                          placeholder: unref(t)("select"),
                          label: "label",
                          "track-by": "id",
                          class: "min-w-[240px] w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "placeholder"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.user_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, { for: "experience_years" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("experienceYears")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "experience_years",
                            type: "number",
                            min: "0",
                            modelValue: unref(form).experience_years,
                            "onUpdate:modelValue": ($event) => unref(form).experience_years = $event,
                            modelModifiers: { number: true },
                            class: "w-full lg:w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2",
                            message: unref(form).errors.experience_years
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, { for: "hourly_rate" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("hourlyRate")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$8, {
                            id: "hourly_rate",
                            modelValue: unref(form).hourly_rate,
                            "onUpdate:modelValue": ($event) => unref(form).hourly_rate = $event,
                            min: 0,
                            step: 0.01,
                            "fraction-digits": 2,
                            class: "w-full lg:w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2",
                            message: unref(form).errors.hourly_rate
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode("div", { class: "h-8 flex items-center" }, [
                            createVNode(_sfc_main$4, {
                              for: "rating_count",
                              value: unref(t)("ratingCount"),
                              class: "text-sm"
                            }, null, 8, ["value"])
                          ]),
                          createVNode(_sfc_main$5, {
                            id: "rating_count",
                            type: "number",
                            min: "0",
                            modelValue: unref(form).rating_count,
                            "onUpdate:modelValue": ($event) => unref(form).rating_count = $event,
                            modelModifiers: { number: true },
                            class: "w-full lg:w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2 lg:mt-0",
                            message: unref(form).errors.rating_count
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode("div", { class: "h-8 flex items-center" }, [
                            createVNode(_sfc_main$4, {
                              for: "rating_avg",
                              value: unref(t)("ratingAvg"),
                              class: "text-sm"
                            }, null, 8, ["value"])
                          ]),
                          createVNode(_sfc_main$8, {
                            id: "rating_avg",
                            modelValue: unref(form).rating_avg,
                            "onUpdate:modelValue": ($event) => unref(form).rating_avg = $event,
                            min: 0,
                            max: 5,
                            step: 0.01,
                            "fraction-digits": 2,
                            class: "w-full lg:w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2 lg:mt-0",
                            message: unref(form).errors.rating_avg
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm" }, [
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
                          createVNode(_sfc_main$4, { for: "title" }, {
                            default: withCtx(() => [
                              createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                              createTextVNode(" " + toDisplayString(unref(t)("title")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$7, {
                            id: "title",
                            type: "text",
                            modelValue: currentTranslation.value.title,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
                            required: "",
                            autocomplete: "off"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2",
                            message: getError("title")
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
                          createVNode(_sfc_main$a, {
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
                            for: "bio",
                            value: `${unref(t)("bio")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$b, {
                            modelValue: currentTranslation.value.bio,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.bio = $event,
                            height: 500
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2",
                            message: getError("bio")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode("div", { class: "flex justify-between w-full" }, [
                            createVNode(_sfc_main$4, {
                              for: "meta_title",
                              value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
                            }, null, 8, ["value"]),
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_title || "").length) + " / 160 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$7, {
                            id: "meta_title",
                            type: "text",
                            modelValue: currentTranslation.value.meta_title,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
                            maxlength: "160",
                            autocomplete: "meta_title"
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
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_keywords || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$7, {
                            id: "meta_keywords",
                            type: "text",
                            modelValue: currentTranslation.value.meta_keywords,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
                            maxlength: "255",
                            autocomplete: "meta_keywords"
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
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_desc || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$a, {
                            modelValue: currentTranslation.value.meta_desc,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
                            maxlength: "255",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2",
                            message: getError("meta_desc")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex justify-end mt-4" }, [
                          createVNode(_sfc_main$c, {
                            onClick: withModifiers(generateMetaFields, ["prevent"])
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
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "social",
                          value: unref(t)("socialLinks")
                        }, null, 8, ["value"]),
                        createVNode("div", { class: "w-full space-y-2" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(socialRows.value, (row, idx) => {
                            return openBlock(), createBlock("div", {
                              key: idx,
                              class: "flex flex-col lg:flex-row gap-2 items-start lg:items-center"
                            }, [
                              createVNode(_sfc_main$7, {
                                id: `social_platform_${idx}`,
                                modelValue: row.platform,
                                "onUpdate:modelValue": ($event) => row.platform = $event,
                                placeholder: unref(t)("platform"),
                                class: "w-full lg:w-1/3",
                                autocomplete: "off"
                              }, null, 8, ["id", "modelValue", "onUpdate:modelValue", "placeholder"]),
                              createVNode(_sfc_main$7, {
                                id: `social_url_${idx}`,
                                modelValue: row.url,
                                "onUpdate:modelValue": ($event) => row.url = $event,
                                placeholder: unref(t)("link") || "https://...",
                                class: "w-full lg:flex-1",
                                autocomplete: "off"
                              }, null, 8, ["id", "modelValue", "onUpdate:modelValue", "placeholder"]),
                              createVNode(_sfc_main$d, {
                                title: unref(t)("delete"),
                                onClick: ($event) => removeSocialRow(idx)
                              }, null, 8, ["title", "onClick"])
                            ]);
                          }), 128)),
                          createVNode(_sfc_main$e, {
                            type: "button",
                            class: "mt-2 float-end",
                            onClick: addSocialRow
                          }, {
                            icon: withCtx(() => [
                              (openBlock(), createBlock("svg", {
                                class: "w-4 h-4 fill-current text-slate-100",
                                viewBox: "0 0 24 24"
                              }, [
                                createVNode("path", {
                                  d: "M12 5v14M5 12h14",
                                  stroke: "currentColor",
                                  "stroke-width": "2",
                                  "stroke-linecap": "round"
                                })
                              ]))
                            ]),
                            default: withCtx(() => [
                              createTextVNode(" " + toDisplayString(unref(t)("addLink")), 1)
                            ]),
                            _: 1
                          })
                        ]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.social_links
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mt-4" }, [
                        createVNode(_sfc_main$f, { "onUpdate:images": handleNewImagesUpdate })
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center mt-4 gap-3" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.schoolInstructorProfiles.index")
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
                      createVNode(_sfc_main$e, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolInstructorProfiles/Create.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
