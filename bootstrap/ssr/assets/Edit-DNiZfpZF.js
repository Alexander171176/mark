import { ref, computed, watch, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$h } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$d } from "./ClearMetaButton-zYsecbj9.js";
import { _ as _sfc_main$e } from "./MetatagsButton-CvAlOk1D.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$c } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$a } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$6 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$5 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$9 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$4 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$g } from "./MultiImageUpload-Bg2ahSyk.js";
import { _ as _sfc_main$f } from "./MultiImageEdit-BZZHL0n1.js";
import { _ as _sfc_main$b } from "./TranslationTabs-czH7YSpu.js";
import { _ as _sfc_main$7, a as _sfc_main$8 } from "./SelectTimezone-PeGlSYxK.js";
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
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    schedule: { type: Object, required: true },
    courses: { type: Array, default: () => [] },
    instructors: { type: Array, default: () => [] }
  },
  setup(__props) {
    var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l, _m, _n, _o, _p, _q, _r, _s, _t, _u;
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
    const resolveDefaultLocale = () => {
      var _a2, _b2, _c2;
      return props.currentLocale || ((_b2 = (_a2 = props.schedule) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.locale) || ((_c2 = props.availableLocales) == null ? void 0 : _c2[0]) || "ru";
    };
    const buildTranslations = () => {
      var _a2;
      const result = {};
      (((_a2 = props.schedule) == null ? void 0 : _a2.translations) || []).forEach((translation) => {
        if (!(translation == null ? void 0 : translation.locale)) return;
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
      const locale = resolveDefaultLocale();
      if (!result[locale]) {
        result[locale] = makeTranslation();
      }
      return result;
    };
    const activeLocale = ref(resolveDefaultLocale());
    const form = useForm({
      _method: "PUT",
      school_course_id: ((_a = props.schedule) == null ? void 0 : _a.school_course_id) ?? ((_c = (_b = props.schedule) == null ? void 0 : _b.course) == null ? void 0 : _c.id) ?? null,
      school_instructor_profile_id: ((_d = props.schedule) == null ? void 0 : _d.school_instructor_profile_id) ?? ((_f = (_e = props.schedule) == null ? void 0 : _e.instructor) == null ? void 0 : _f.id) ?? null,
      activity: Boolean((_g = props.schedule) == null ? void 0 : _g.activity),
      sort: ((_h = props.schedule) == null ? void 0 : _h.sort) ?? 0,
      slug: ((_i = props.schedule) == null ? void 0 : _i.slug) ?? "",
      /**
       * Resource уже возвращает значения специально
       * для input[type="datetime-local"]:
       *
       * Y-m-dTH:i
       *
       * Поэтому new Date() здесь не нужен.
       */
      starts_at: ((_j = props.schedule) == null ? void 0 : _j.starts_at) ?? "",
      ends_at: ((_k = props.schedule) == null ? void 0 : _k.ends_at) ?? "",
      enroll_starts_at: ((_l = props.schedule) == null ? void 0 : _l.enroll_starts_at) ?? "",
      enroll_ends_at: ((_m = props.schedule) == null ? void 0 : _m.enroll_ends_at) ?? "",
      capacity: ((_n = props.schedule) == null ? void 0 : _n.capacity) ?? 0,
      is_online: Boolean((_o = props.schedule) == null ? void 0 : _o.is_online),
      location: ((_p = props.schedule) == null ? void 0 : _p.location) ?? "",
      meeting_url: ((_q = props.schedule) == null ? void 0 : _q.meeting_url) ?? "",
      timezone: ((_r = props.schedule) == null ? void 0 : _r.timezone) ?? "",
      status: ((_s = props.schedule) == null ? void 0 : _s.status) ?? "draft",
      notes: ((_t = props.schedule) == null ? void 0 : _t.notes) ?? "",
      translations: buildTranslations(),
      deletedImages: []
    });
    const currentTranslation = computed(() => {
      if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation();
      }
      return form.translations[activeLocale.value];
    });
    const pageTitle = computed(
      () => {
        var _a2, _b2, _c2;
        return currentTranslation.value.title || ((_b2 = (_a2 = props.schedule) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || `ID: ${(_c2 = props.schedule) == null ? void 0 : _c2.id}`;
      }
    );
    const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`];
    watch(
      () => props.currentLocale,
      (locale) => {
        if (!locale) return;
        activeLocale.value = locale;
        if (!form.translations[locale]) {
          form.translations[locale] = makeTranslation();
        }
      }
    );
    const dynamicOptionsLimit = (items) => Array.isArray(items) ? items.length + 10 : 10;
    const getCourseTitle = (item) => {
      var _a2;
      return ((_a2 = item == null ? void 0 : item.translation) == null ? void 0 : _a2.title) || (item == null ? void 0 : item.title) || (item == null ? void 0 : item.slug) || `#${(item == null ? void 0 : item.id) ?? ""}`;
    };
    const getInstructorTitle = (item) => {
      var _a2, _b2, _c2;
      return ((_a2 = item == null ? void 0 : item.translation) == null ? void 0 : _a2.title) || ((_b2 = item == null ? void 0 : item.translation) == null ? void 0 : _b2.public_name) || (item == null ? void 0 : item.public_name) || (item == null ? void 0 : item.title) || ((_c2 = item == null ? void 0 : item.user) == null ? void 0 : _c2.name) || `#${(item == null ? void 0 : item.id) ?? ""}`;
    };
    const courseOptions = computed(
      () => (props.courses || []).map((item) => ({
        id: item.id,
        label: `[ID: ${item.id}] ${getCourseTitle(item)}`
      }))
    );
    const instructorOptions = computed(
      () => (props.instructors || []).map((item) => {
        var _a2, _b2;
        const title = getInstructorTitle(item);
        const userName = ((_a2 = item == null ? void 0 : item.user) == null ? void 0 : _a2.name) || ((_b2 = item == null ? void 0 : item.user) == null ? void 0 : _b2.email) || "";
        return {
          id: item.id,
          label: userName && userName !== title ? `[ID: ${item.id}] ${title} — ${userName}` : `[ID: ${item.id}] ${title}`
        };
      })
    );
    const selectedCourse = ref(null);
    const selectedInstructor = ref(null);
    watch(
      courseOptions,
      (options) => {
        selectedCourse.value = options.find(
          (item) => Number(item.id) === Number(form.school_course_id)
        ) || null;
      },
      { immediate: true }
    );
    watch(
      instructorOptions,
      (options) => {
        selectedInstructor.value = options.find(
          (item) => Number(item.id) === Number(form.school_instructor_profile_id)
        ) || null;
      },
      { immediate: true }
    );
    watch(selectedCourse, (value) => {
      form.school_course_id = (value == null ? void 0 : value.id) !== void 0 && (value == null ? void 0 : value.id) !== null ? Number(value.id) : null;
    });
    watch(selectedInstructor, (value) => {
      form.school_instructor_profile_id = (value == null ? void 0 : value.id) !== void 0 && (value == null ? void 0 : value.id) !== null ? Number(value.id) : null;
    });
    const resolveImageUrl = (image) => (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || null;
    const existingImages = ref(
      (((_u = props.schedule) == null ? void 0 : _u.images) || []).filter((image) => Boolean(resolveImageUrl(image))).map((image) => {
        var _a2;
        return {
          id: image.id,
          url: resolveImageUrl(image),
          order: image.order ?? ((_a2 = image.pivot) == null ? void 0 : _a2.order) ?? 0,
          alt: image.alt || "",
          caption: image.caption || ""
        };
      })
    );
    const newImages = ref([]);
    const handleExistingImagesUpdate = (images) => {
      existingImages.value = Array.isArray(images) ? images : [];
    };
    const handleDeleteExistingImage = (deletedId) => {
      if (!form.deletedImages.includes(deletedId)) {
        form.deletedImages.push(deletedId);
      }
      existingImages.value = existingImages.value.filter(
        (image) => Number(image.id) !== Number(deletedId)
      );
    };
    const handleNewImagesUpdate = (images) => {
      newImages.value = Array.isArray(images) ? images : [];
    };
    const handleSlugFocus = () => {
      if (!form.slug && currentTranslation.value.title) {
        form.slug = transliterate(
          currentTranslation.value.title.toLowerCase()
        );
      }
    };
    const truncateText = (text, maxLength, addEllipsis = false) => {
      if (!text) return "";
      const value = String(text);
      if (value.length <= maxLength) {
        return value;
      }
      const lastSpaceIndex = value.lastIndexOf(
        " ",
        maxLength
      );
      const truncated = lastSpaceIndex === -1 ? value.substring(
        0,
        maxLength
      ) : value.substring(
        0,
        lastSpaceIndex
      );
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
        translation.meta_title = truncateText(
          translation.title,
          160
        );
      }
      if (!translation.meta_keywords && translation.short) {
        let text = String(
          translation.short
        ).replace(
          /(<([^>]+)>)/gi,
          ""
        );
        text = text.replace(
          /[.,!?;:()[\]{}"'«»]/g,
          ""
        );
        const words = text.split(/\s+/).filter(
          (word) => word && word.length >= 3
        ).map(
          (word) => word.toLowerCase()
        ).filter(
          (value, index, self) => self.indexOf(value) === index
        );
        translation.meta_keywords = truncateText(
          words.join(", "),
          255
        );
      }
      if (translation.short && !translation.meta_desc) {
        const description = String(
          translation.short
        ).replace(
          /(<([^>]+)>)/gi,
          ""
        );
        translation.meta_desc = truncateText(
          description,
          255,
          true
        );
      }
    };
    const nullableId = (value) => {
      if (value === "" || value === null || value === void 0) {
        return null;
      }
      const number = Number(value);
      return Number.isFinite(number) ? number : null;
    };
    const submitForm = () => {
      form.transform((data) => {
        const transformed = {
          ...data,
          /**
           * Единственный источник истины —
           * form IDs, а не selected UI objects.
           */
          school_course_id: nullableId(
            data.school_course_id
          ),
          school_instructor_profile_id: nullableId(
            data.school_instructor_profile_id
          ),
          activity: data.activity ? 1 : 0,
          is_online: data.is_online ? 1 : 0,
          sort: data.sort === "" || data.sort === null ? 0 : Number(data.sort),
          capacity: data.capacity === "" || data.capacity === null ? 0 : Number(data.capacity)
        };
        delete transformed.images;
        delete transformed.deletedImages;
        let index = 0;
        existingImages.value.forEach(
          (image) => {
            transformed[`images[${index}][id]`] = image.id;
            transformed[`images[${index}][order]`] = image.order ?? 0;
            transformed[`images[${index}][alt]`] = image.alt ?? "";
            transformed[`images[${index}][caption]`] = image.caption ?? "";
            index++;
          }
        );
        newImages.value.forEach(
          (image) => {
            transformed[`images[${index}][file]`] = image.file;
            transformed[`images[${index}][order]`] = image.order ?? 0;
            transformed[`images[${index}][alt]`] = image.alt ?? "";
            transformed[`images[${index}][caption]`] = image.caption ?? "";
            index++;
          }
        );
        form.deletedImages.forEach(
          (id, deletedIndex) => {
            transformed[`deletedImages[${deletedIndex}]`] = id;
          }
        );
        return transformed;
      });
      form.post(
        route(
          "admin.schoolCourseSchedules.update",
          {
            schoolCourseSchedule: props.schedule.id
          }
        ),
        {
          errorBag: "editSchoolCourseSchedule",
          preserveScroll: true,
          forceFormData: true,
          onSuccess: () => {
            toast.success(
              "Расписание успешно обновлено!"
            );
            newImages.value = [];
            form.deletedImages = [];
          },
          onError: (errors) => {
            console.error(
              "Ошибка обновления расписания:",
              errors
            );
            const firstKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || "Проверьте корректность полей."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editSchedule")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editSchedule"))}: ${ssrInterpolate(pageTitle.value)} [ID: ${ssrInterpolate(props.schedule.id)}] `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editSchedule")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(props.schedule.id) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editSchedule")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(props.schedule.id) + "] ", 1)
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
              href: _ctx.route("admin.schoolCourseSchedules.index")
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
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).is_online,
              "onUpdate:modelValue": ($event) => unref(form).is_online = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "is_online",
              text: unref(t)("online"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.is_online
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
            _push2(`</div></div><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              modelValue: unref(form).status,
              "onUpdate:modelValue": ($event) => unref(form).status = $event,
              errorMessage: unref(form).errors.status
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              modelValue: unref(form).timezone,
              "onUpdate:modelValue": ($event) => unref(form).timezone = $event,
              errorMessage: unref(form).errors.timezone
            }, null, _parent2, _scopeId));
            _push2(`<div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "capacity",
              value: unref(t)("capacity")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "capacity",
              type: "number",
              min: "0",
              modelValue: unref(form).capacity,
              "onUpdate:modelValue": ($event) => unref(form).capacity = $event,
              modelModifiers: { number: true },
              class: "w-full lg:w-36"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.capacity
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "starts_at",
              value: unref(t)("scheduleStartsAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "starts_at",
              type: "datetime-local",
              modelValue: unref(form).starts_at,
              "onUpdate:modelValue": ($event) => unref(form).starts_at = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.starts_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "ends_at",
              value: unref(t)("scheduleEndsAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "ends_at",
              type: "datetime-local",
              modelValue: unref(form).ends_at,
              "onUpdate:modelValue": ($event) => unref(form).ends_at = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.ends_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "enroll_starts_at",
              value: unref(t)("scheduleEnrollStartsAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "enroll_starts_at",
              type: "datetime-local",
              modelValue: unref(form).enroll_starts_at,
              "onUpdate:modelValue": ($event) => unref(form).enroll_starts_at = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.enroll_starts_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "enroll_ends_at",
              value: unref(t)("scheduleEnrollEndsAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "enroll_ends_at",
              type: "datetime-local",
              modelValue: unref(form).enroll_ends_at,
              "onUpdate:modelValue": ($event) => unref(form).enroll_ends_at = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.enroll_ends_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "location",
              value: unref(t)("location")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "location",
              type: "text",
              modelValue: unref(form).location,
              "onUpdate:modelValue": ($event) => unref(form).location = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.location
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "meeting_url",
              value: unref(t)("meetingUrl")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "meeting_url",
              type: "url",
              modelValue: unref(form).meeting_url,
              "onUpdate:modelValue": ($event) => unref(form).meeting_url = $event,
              placeholder: "https://...",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.meeting_url
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "school_course_id",
              class: "mb-1"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("course"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("course")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "school_course_id",
              modelValue: selectedCourse.value,
              "onUpdate:modelValue": ($event) => selectedCourse.value = $event,
              options: courseOptions.value,
              "options-limit": dynamicOptionsLimit(courseOptions.value),
              multiple: false,
              "close-on-select": true,
              "clear-on-select": false,
              "preserve-search": true,
              "allow-empty": true,
              placeholder: unref(t)("select"),
              label: "label",
              "track-by": "id",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.school_course_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "school_instructor_profile_id",
              value: unref(t)("instructor"),
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "school_instructor_profile_id",
              modelValue: selectedInstructor.value,
              "onUpdate:modelValue": ($event) => selectedInstructor.value = $event,
              options: instructorOptions.value,
              "options-limit": dynamicOptionsLimit(instructorOptions.value),
              multiple: false,
              "close-on-select": true,
              "clear-on-select": false,
              "preserve-search": true,
              "allow-empty": true,
              placeholder: unref(t)("select"),
              label: "label",
              "track-by": "id",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.school_instructor_profile_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
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
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "slug",
              type: "text",
              modelValue: unref(form).slug,
              "onUpdate:modelValue": ($event) => unref(form).slug = $event,
              class: "w-full",
              autocomplete: "slug",
              onFocus: handleSlugFocus,
              required: ""
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.slug
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "notes",
              value: unref(t)("notes")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              id: "notes",
              modelValue: unref(form).notes,
              "onUpdate:modelValue": ($event) => unref(form).notes = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.notes
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
            _push2(ssrRenderComponent(_sfc_main$5, { for: "title" }, {
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
              required: "",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "subtitle",
              value: `${unref(t)("subtitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.subtitle || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$a, {
              id: "subtitle",
              modelValue: currentTranslation.value.subtitle,
              "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("subtitle")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "short",
              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.short || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$a, {
              id: "short",
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
            _push2(ssrRenderComponent(_sfc_main$c, {
              modelValue: currentTranslation.value.description,
              "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
              height: 500
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("description")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "meta_title",
              value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_title || "").length)} / 160 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "meta_title",
              type: "text",
              modelValue: currentTranslation.value.meta_title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
              maxlength: "160",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("meta_title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "meta_keywords",
              value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_keywords || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              id: "meta_keywords",
              type: "text",
              modelValue: currentTranslation.value.meta_keywords,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
              maxlength: "255",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("meta_keywords")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "meta_desc",
              value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_desc || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$a, {
              id: "meta_desc",
              modelValue: currentTranslation.value.meta_desc,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
              maxlength: "255",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("meta_desc")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex justify-end gap-2 mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$d, { onClick: clearMetaFields }, {
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
            _push2(ssrRenderComponent(_sfc_main$e, { onClick: generateMetaFields }, {
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
            _push2(ssrRenderComponent(_sfc_main$f, {
              images: existingImages.value,
              "onUpdate:images": handleExistingImagesUpdate,
              onDeleteImage: handleDeleteExistingImage
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$g, {
              images: newImages.value,
              "onUpdate:images": [($event) => newImages.value = $event, handleNewImagesUpdate]
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex items-center justify-center mt-4 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolCourseSchedules.index")
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
            _push2(ssrRenderComponent(_sfc_main$h, {
              type: "submit",
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
            _push2(`</div></div></form></div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-2" }, [
                    createVNode(_sfc_main$1, {
                      href: _ctx.route("admin.schoolCourseSchedules.index")
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
                          createVNode(_sfc_main$2, {
                            modelValue: unref(form).is_online,
                            "onUpdate:modelValue": ($event) => unref(form).is_online = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            for: "is_online",
                            text: unref(t)("online"),
                            class: "text-sm h-8 flex items-center"
                          }, null, 8, ["text"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.is_online
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
                      createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                        createVNode(_sfc_main$7, {
                          modelValue: unref(form).status,
                          "onUpdate:modelValue": ($event) => unref(form).status = $event,
                          errorMessage: unref(form).errors.status
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "errorMessage"]),
                        createVNode(_sfc_main$8, {
                          modelValue: unref(form).timezone,
                          "onUpdate:modelValue": ($event) => unref(form).timezone = $event,
                          errorMessage: unref(form).errors.timezone
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "errorMessage"]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "capacity",
                            value: unref(t)("capacity")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$6, {
                            id: "capacity",
                            type: "number",
                            min: "0",
                            modelValue: unref(form).capacity,
                            "onUpdate:modelValue": ($event) => unref(form).capacity = $event,
                            modelModifiers: { number: true },
                            class: "w-full lg:w-36"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.capacity
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "starts_at",
                            value: unref(t)("scheduleStartsAt")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$9, {
                            id: "starts_at",
                            type: "datetime-local",
                            modelValue: unref(form).starts_at,
                            "onUpdate:modelValue": ($event) => unref(form).starts_at = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.starts_at
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "ends_at",
                            value: unref(t)("scheduleEndsAt")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$9, {
                            id: "ends_at",
                            type: "datetime-local",
                            modelValue: unref(form).ends_at,
                            "onUpdate:modelValue": ($event) => unref(form).ends_at = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.ends_at
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "enroll_starts_at",
                            value: unref(t)("scheduleEnrollStartsAt")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$9, {
                            id: "enroll_starts_at",
                            type: "datetime-local",
                            modelValue: unref(form).enroll_starts_at,
                            "onUpdate:modelValue": ($event) => unref(form).enroll_starts_at = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.enroll_starts_at
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "enroll_ends_at",
                            value: unref(t)("scheduleEnrollEndsAt")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$9, {
                            id: "enroll_ends_at",
                            type: "datetime-local",
                            modelValue: unref(form).enroll_ends_at,
                            "onUpdate:modelValue": ($event) => unref(form).enroll_ends_at = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.enroll_ends_at
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 grid grid-cols-1 lg:grid-cols-2 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "location",
                            value: unref(t)("location")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$9, {
                            id: "location",
                            type: "text",
                            modelValue: unref(form).location,
                            "onUpdate:modelValue": ($event) => unref(form).location = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.location
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "meeting_url",
                            value: unref(t)("meetingUrl")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$9, {
                            id: "meeting_url",
                            type: "url",
                            modelValue: unref(form).meeting_url,
                            "onUpdate:modelValue": ($event) => unref(form).meeting_url = $event,
                            placeholder: "https://...",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.meeting_url
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, {
                          for: "school_course_id",
                          class: "mb-1"
                        }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("course")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(unref(VueMultiselect), {
                          id: "school_course_id",
                          modelValue: selectedCourse.value,
                          "onUpdate:modelValue": ($event) => selectedCourse.value = $event,
                          options: courseOptions.value,
                          "options-limit": dynamicOptionsLimit(courseOptions.value),
                          multiple: false,
                          "close-on-select": true,
                          "clear-on-select": false,
                          "preserve-search": true,
                          "allow-empty": true,
                          placeholder: unref(t)("select"),
                          label: "label",
                          "track-by": "id",
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.school_course_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, {
                          for: "school_instructor_profile_id",
                          value: unref(t)("instructor"),
                          class: "mb-1"
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "school_instructor_profile_id",
                          modelValue: selectedInstructor.value,
                          "onUpdate:modelValue": ($event) => selectedInstructor.value = $event,
                          options: instructorOptions.value,
                          "options-limit": dynamicOptionsLimit(instructorOptions.value),
                          multiple: false,
                          "close-on-select": true,
                          "clear-on-select": false,
                          "preserve-search": true,
                          "allow-empty": true,
                          placeholder: unref(t)("select"),
                          label: "label",
                          "track-by": "id",
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.school_instructor_profile_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, { for: "slug" }, {
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
                          class: "w-full",
                          autocomplete: "slug",
                          onFocus: handleSlugFocus,
                          required: ""
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.slug
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$5, {
                          for: "notes",
                          value: unref(t)("notes")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$a, {
                          id: "notes",
                          modelValue: unref(form).notes,
                          "onUpdate:modelValue": ($event) => unref(form).notes = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.notes
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
                            createVNode(_sfc_main$5, { for: "title" }, {
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
                            required: "",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getError("title")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode("div", { class: "flex justify-between w-full" }, [
                            createVNode(_sfc_main$5, {
                              for: "subtitle",
                              value: `${unref(t)("subtitle")} [${activeLocale.value.toUpperCase()}]`
                            }, null, 8, ["value"]),
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.subtitle || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$a, {
                            id: "subtitle",
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
                          createVNode("div", { class: "flex justify-between w-full" }, [
                            createVNode(_sfc_main$5, {
                              for: "short",
                              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
                            }, null, 8, ["value"]),
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.short || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$a, {
                            id: "short",
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
                          createVNode(_sfc_main$c, {
                            modelValue: currentTranslation.value.description,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
                            height: 500
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getError("description")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode("div", { class: "flex justify-between w-full" }, [
                            createVNode(_sfc_main$5, {
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
                            maxlength: "160",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getError("meta_title")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode("div", { class: "flex justify-between w-full" }, [
                            createVNode(_sfc_main$5, {
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
                            maxlength: "255",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getError("meta_keywords")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode("div", { class: "flex justify-between w-full" }, [
                            createVNode(_sfc_main$5, {
                              for: "meta_desc",
                              value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
                            }, null, 8, ["value"]),
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_desc || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$a, {
                            id: "meta_desc",
                            modelValue: currentTranslation.value.meta_desc,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
                            maxlength: "255",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getError("meta_desc")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex justify-end gap-2 mt-4" }, [
                          createVNode(_sfc_main$d, {
                            onClick: withModifiers(clearMetaFields, ["prevent"])
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("clearMetaFields")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$e, {
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
                        createVNode(_sfc_main$f, {
                          images: existingImages.value,
                          "onUpdate:images": handleExistingImagesUpdate,
                          onDeleteImage: handleDeleteExistingImage
                        }, null, 8, ["images"])
                      ]),
                      createVNode("div", { class: "mt-4" }, [
                        createVNode(_sfc_main$g, {
                          images: newImages.value,
                          "onUpdate:images": [($event) => newImages.value = $event, handleNewImagesUpdate]
                        }, null, 8, ["images", "onUpdate:images"])
                      ]),
                      createVNode("div", { class: "flex items-center justify-center mt-4 gap-3" }, [
                        createVNode(_sfc_main$1, {
                          href: _ctx.route("admin.schoolCourseSchedules.index")
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
                        createVNode(_sfc_main$h, {
                          type: "submit",
                          disabled: unref(form).processing
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("save")), 1)
                          ]),
                          _: 1
                        }, 8, ["disabled"])
                      ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolCourseSchedules/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
