import { ref, watch, computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$d } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$b } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$a } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$5 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$4 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$8 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$6 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$c } from "./MultiImageUpload-Bg2ahSyk.js";
import { _ as _sfc_main$9 } from "./TranslationTabs-czH7YSpu.js";
import { _ as _sfc_main$7 } from "./SelectQuizType-MVjWz-4C.js";
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
    currentLocale: {
      type: String,
      default: ""
    },
    availableLocales: {
      type: Array,
      default: () => []
    },
    courses: {
      type: Array,
      default: () => []
    },
    modules: {
      type: Array,
      default: () => []
    },
    lessons: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    var _a;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const makeTranslation = () => ({
      title: "",
      short: "",
      description: ""
    });
    const defaultLocale = props.currentLocale || ((_a = props.availableLocales) == null ? void 0 : _a[0]) || "ru";
    const activeLocale = ref(
      defaultLocale
    );
    const form = useForm({
      school_course_id: null,
      school_module_id: null,
      school_lesson_id: null,
      activity: true,
      left: false,
      main: false,
      right: false,
      sort: 0,
      slug: "",
      type: "graded",
      attempts_limit: 0,
      time_limit_minutes: "",
      pass_score: 70,
      published_at: "",
      images: [],
      translations: {
        [defaultLocale]: makeTranslation()
      }
    });
    watch(
      () => props.currentLocale,
      (locale) => {
        if (!locale) {
          return;
        }
        activeLocale.value = locale;
        if (!form.translations[locale]) {
          form.translations[locale] = makeTranslation();
        }
      }
    );
    const currentTranslation = computed(
      () => {
        if (!form.translations[activeLocale.value]) {
          form.translations[activeLocale.value] = makeTranslation();
        }
        return form.translations[activeLocale.value];
      }
    );
    const getError = (key) => {
      return form.errors[`translations.${activeLocale.value}.${key}`];
    };
    const dynamicOptionsLimit = (items) => {
      if (!items) {
        return 10;
      }
      return items.length + 10;
    };
    const courseOptions = computed(
      () => props.courses.map(
        (course) => {
          var _a2;
          return {
            id: course.id,
            label: `[ID: ${course.id}] ${((_a2 = course == null ? void 0 : course.translation) == null ? void 0 : _a2.title) || (course == null ? void 0 : course.slug) || `#${course.id}`}`
          };
        }
      )
    );
    const moduleOptions = computed(
      () => props.modules.map(
        (module) => {
          var _a2, _b, _c, _d;
          const moduleTitle = ((_a2 = module == null ? void 0 : module.translation) == null ? void 0 : _a2.title) || (module == null ? void 0 : module.slug) || `#${module.id}`;
          const courseTitle = ((_c = (_b = module == null ? void 0 : module.course) == null ? void 0 : _b.translation) == null ? void 0 : _c.title) || ((_d = module == null ? void 0 : module.course) == null ? void 0 : _d.slug) || "";
          return {
            id: module.id,
            label: courseTitle ? `[ID: ${module.id}] [${courseTitle}] ${moduleTitle}` : `[ID: ${module.id}] ${moduleTitle}`
          };
        }
      )
    );
    const lessonOptions = computed(
      () => props.lessons.map(
        (lesson) => {
          var _a2, _b, _c, _d, _e, _f, _g, _h, _i;
          const lessonTitle = ((_a2 = lesson == null ? void 0 : lesson.translation) == null ? void 0 : _a2.title) || (lesson == null ? void 0 : lesson.slug) || `#${lesson.id}`;
          const moduleTitle = ((_c = (_b = lesson == null ? void 0 : lesson.module) == null ? void 0 : _b.translation) == null ? void 0 : _c.title) || ((_d = lesson == null ? void 0 : lesson.module) == null ? void 0 : _d.slug) || "";
          const courseTitle = ((_g = (_f = (_e = lesson == null ? void 0 : lesson.module) == null ? void 0 : _e.course) == null ? void 0 : _f.translation) == null ? void 0 : _g.title) || ((_i = (_h = lesson == null ? void 0 : lesson.module) == null ? void 0 : _h.course) == null ? void 0 : _i.slug) || "";
          let label = lessonTitle;
          if (courseTitle && moduleTitle) {
            label = `[${courseTitle}] [${moduleTitle}] ${lessonTitle}`;
          } else if (moduleTitle) {
            label = `[${moduleTitle}] ${lessonTitle}`;
          }
          return {
            id: lesson.id,
            label: `[ID: ${lesson.id}] ${label}`
          };
        }
      )
    );
    const selectedCourse = computed({
      get: () => {
        if (!form.school_course_id) {
          return null;
        }
        return courseOptions.value.find(
          (course) => Number(course.id) === Number(
            form.school_course_id
          )
        ) || null;
      },
      set: (value) => {
        form.school_course_id = (value == null ? void 0 : value.id) ?? null;
      }
    });
    const selectedModule = computed({
      get: () => {
        if (!form.school_module_id) {
          return null;
        }
        return moduleOptions.value.find(
          (module) => Number(module.id) === Number(
            form.school_module_id
          )
        ) || null;
      },
      set: (value) => {
        form.school_module_id = (value == null ? void 0 : value.id) ?? null;
      }
    });
    const selectedLesson = computed({
      get: () => {
        if (!form.school_lesson_id) {
          return null;
        }
        return lessonOptions.value.find(
          (lesson) => Number(lesson.id) === Number(
            form.school_lesson_id
          )
        ) || null;
      },
      set: (value) => {
        form.school_lesson_id = (value == null ? void 0 : value.id) ?? null;
      }
    });
    const selectedModuleEntity = computed(
      () => {
        if (!form.school_module_id) {
          return null;
        }
        return props.modules.find(
          (module) => Number(module.id) === Number(
            form.school_module_id
          )
        ) || null;
      }
    );
    const selectedLessonEntity = computed(
      () => {
        if (!form.school_lesson_id) {
          return null;
        }
        return props.lessons.find(
          (lesson) => Number(lesson.id) === Number(
            form.school_lesson_id
          )
        ) || null;
      }
    );
    watch(
      () => form.school_lesson_id,
      (lessonId) => {
        var _a2, _b, _c, _d, _e;
        if (!lessonId) {
          return;
        }
        const lesson = selectedLessonEntity.value;
        if (!lesson) {
          return;
        }
        const moduleId = lesson.school_module_id ?? lesson.module_id ?? ((_a2 = lesson.module) == null ? void 0 : _a2.id) ?? null;
        if (moduleId) {
          const normalizedModuleId = Number(moduleId);
          if (Number(
            form.school_module_id
          ) !== normalizedModuleId) {
            const hadModule = Boolean(
              form.school_module_id
            );
            form.school_module_id = normalizedModuleId;
            if (hadModule) {
              toast.info(
                "Модуль автоматически подставлен из выбранного урока"
              );
            }
          }
        }
        const courseId = ((_b = lesson.module) == null ? void 0 : _b.school_course_id) ?? ((_c = lesson.module) == null ? void 0 : _c.course_id) ?? ((_e = (_d = lesson.module) == null ? void 0 : _d.course) == null ? void 0 : _e.id) ?? null;
        if (courseId) {
          const normalizedCourseId = Number(courseId);
          if (Number(
            form.school_course_id
          ) !== normalizedCourseId) {
            const hadCourse = Boolean(
              form.school_course_id
            );
            form.school_course_id = normalizedCourseId;
            if (hadCourse) {
              toast.info(
                "Курс автоматически подставлен из выбранного урока"
              );
            }
          }
        }
      }
    );
    watch(
      () => form.school_module_id,
      (moduleId) => {
        var _a2, _b;
        if (!moduleId) {
          if (form.school_lesson_id) {
            form.school_lesson_id = null;
          }
          return;
        }
        const module = selectedModuleEntity.value;
        if (!module) {
          return;
        }
        const courseId = module.school_course_id ?? module.course_id ?? ((_a2 = module.course) == null ? void 0 : _a2.id) ?? null;
        if (courseId) {
          const normalizedCourseId = Number(courseId);
          if (Number(
            form.school_course_id
          ) !== normalizedCourseId) {
            const hadCourse = Boolean(
              form.school_course_id
            );
            form.school_course_id = normalizedCourseId;
            if (hadCourse) {
              toast.info(
                "Курс автоматически подставлен из выбранного модуля"
              );
            }
          }
        }
        if (form.school_lesson_id) {
          const lesson = selectedLessonEntity.value;
          const lessonModuleId = (lesson == null ? void 0 : lesson.school_module_id) ?? (lesson == null ? void 0 : lesson.module_id) ?? ((_b = lesson == null ? void 0 : lesson.module) == null ? void 0 : _b.id) ?? null;
          if (lesson && lessonModuleId && Number(lessonModuleId) !== Number(moduleId)) {
            form.school_lesson_id = null;
            toast.info(
              "Урок сброшен, так как не относится к выбранному модулю"
            );
          }
        }
      }
    );
    watch(
      () => form.school_course_id,
      (courseId) => {
        var _a2, _b, _c, _d, _e;
        if (!courseId) {
          form.school_module_id = null;
          form.school_lesson_id = null;
          return;
        }
        if (form.school_module_id) {
          const module = selectedModuleEntity.value;
          const moduleCourseId = (module == null ? void 0 : module.school_course_id) ?? (module == null ? void 0 : module.course_id) ?? ((_a2 = module == null ? void 0 : module.course) == null ? void 0 : _a2.id) ?? null;
          if (moduleCourseId && Number(moduleCourseId) !== Number(courseId)) {
            form.school_module_id = null;
            form.school_lesson_id = null;
            toast.info(
              "Модуль и урок сброшены: они не относятся к выбранному курсу"
            );
            return;
          }
        }
        if (form.school_lesson_id) {
          const lesson = selectedLessonEntity.value;
          const lessonCourseId = ((_b = lesson == null ? void 0 : lesson.module) == null ? void 0 : _b.school_course_id) ?? ((_c = lesson == null ? void 0 : lesson.module) == null ? void 0 : _c.course_id) ?? ((_e = (_d = lesson == null ? void 0 : lesson.module) == null ? void 0 : _d.course) == null ? void 0 : _e.id) ?? null;
          if (lessonCourseId && Number(lessonCourseId) !== Number(courseId)) {
            form.school_lesson_id = null;
            toast.info(
              "Урок сброшен: он не относится к выбранному курсу"
            );
          }
        }
      }
    );
    const newImages = ref([]);
    const handleNewImagesUpdate = (images) => {
      newImages.value = images || [];
    };
    const handleSlugFocus = () => {
      if (!form.slug && currentTranslation.value.title) {
        form.slug = transliterate(
          currentTranslation.value.title.toLowerCase()
        );
      }
    };
    const submit = () => {
      form.transform(
        (data) => {
          const transformed = {
            ...data,
            school_course_id: data.school_course_id || null,
            school_module_id: data.school_module_id || null,
            school_lesson_id: data.school_lesson_id || null,
            attempts_limit: Number(
              data.attempts_limit || 0
            ),
            time_limit_minutes: data.time_limit_minutes === "" || data.time_limit_minutes === null ? null : Number(
              data.time_limit_minutes
            ),
            pass_score: Number(
              data.pass_score || 0
            ),
            sort: Number(
              data.sort || 0
            ),
            activity: data.activity ? 1 : 0,
            left: data.left ? 1 : 0,
            main: data.main ? 1 : 0,
            right: data.right ? 1 : 0
          };
          newImages.value.forEach(
            (image, index) => {
              transformed[`images[${index}][file]`] = image.file;
              transformed[`images[${index}][order]`] = image.order ?? 0;
              transformed[`images[${index}][alt]`] = image.alt ?? "";
              transformed[`images[${index}][caption]`] = image.caption ?? "";
            }
          );
          return transformed;
        }
      );
      form.post(
        route(
          "admin.schoolQuizzes.store"
        ),
        {
          errorBag: "createSchoolQuiz",
          preserveScroll: true,
          forceFormData: true,
          onSuccess: () => {
            toast.success(
              "Квиз успешно создан!"
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка создания квиза:",
              errors
            );
            const firstKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              errors[firstKey] || "Проверьте корректность полей."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("createQuiz")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("createQuiz"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("createQuiz")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("createQuiz")), 1)
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
              href: _ctx.route("admin.schoolQuizzes.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0
                                       .7.7 1.2 1.7 1.4 2.7l2-.3
                                       c-.2-1.5-.9-2.8-1.9-3.8
                                       C10.1.4 5.7.4 2.9 3.1
                                       L.7.9 0 7.3l6.4-.7-2.1-2.1z
                                       M15.6 8.7l-6.4.7 2.1 2.1
                                       c-1.9 1.9-5.1 1.9-7 0
                                       -.7-.7-1.2-1.7-1.4-2.7l-2 .3
                                       c.2 1.5.9 2.8 1.9 3.8
                                       1.4 1.4 3.1 2 4.9 2
                                       1.8 0 3.6-.7 4.9-2
                                       l2.2 2.2.8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0\n                                       .7.7 1.2 1.7 1.4 2.7l2-.3\n                                       c-.2-1.5-.9-2.8-1.9-3.8\n                                       C10.1.4 5.7.4 2.9 3.1\n                                       L.7.9 0 7.3l6.4-.7-2.1-2.1z\n                                       M15.6 8.7l-6.4.7 2.1 2.1\n                                       c-1.9 1.9-5.1 1.9-7 0\n                                       -.7-.7-1.2-1.7-1.4-2.7l-2 .3\n                                       c.2 1.5.9 2.8 1.9 3.8\n                                       1.4 1.4 3.1 2 4.9 2\n                                       1.8 0 3.6-.7 4.9-2\n                                       l2.2 2.2.8-6.4z" })
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
              modelModifiers: { number: true },
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
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
            _push2(`</div></div><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              modelValue: unref(form).type,
              "onUpdate:modelValue": ($event) => unref(form).type = $event,
              errorMessage: unref(form).errors.type
            }, null, _parent2, _scopeId));
            _push2(`<div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "published_at",
              value: unref(t)("publishedAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "published_at",
              type: "datetime-local",
              modelValue: unref(form).published_at,
              "onUpdate:modelValue": ($event) => unref(form).published_at = $event,
              class: "w-full max-w-56"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.published_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 grid grid-cols-1 md:grid-cols-3 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "attempts_limit",
              value: unref(t)("limitCount")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "attempts_limit",
              type: "number",
              min: "0",
              modelValue: unref(form).attempts_limit,
              "onUpdate:modelValue": ($event) => unref(form).attempts_limit = $event,
              modelModifiers: { number: true },
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.attempts_limit
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "time_limit_minutes",
              value: unref(t)("limitMinutes")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "time_limit_minutes",
              type: "number",
              min: "1",
              modelValue: unref(form).time_limit_minutes,
              "onUpdate:modelValue": ($event) => unref(form).time_limit_minutes = $event,
              modelModifiers: { number: true },
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.time_limit_minutes
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "pass_score",
              value: unref(t)("passScore")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "pass_score",
              type: "number",
              min: "0",
              max: "100",
              modelValue: unref(form).pass_score,
              "onUpdate:modelValue": ($event) => unref(form).pass_score = $event,
              modelModifiers: { number: true },
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.pass_score
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
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
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.school_course_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "school_module_id",
              value: unref(t)("module"),
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "school_module_id",
              modelValue: selectedModule.value,
              "onUpdate:modelValue": ($event) => selectedModule.value = $event,
              options: moduleOptions.value,
              "options-limit": dynamicOptionsLimit(moduleOptions.value),
              multiple: false,
              "close-on-select": true,
              "allow-empty": true,
              placeholder: unref(t)("select"),
              label: "label",
              "track-by": "id",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.school_module_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "school_lesson_id",
              value: unref(t)("lesson"),
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "school_lesson_id",
              modelValue: selectedLesson.value,
              "onUpdate:modelValue": ($event) => selectedLesson.value = $event,
              options: lessonOptions.value,
              "options-limit": dynamicOptionsLimit(lessonOptions.value),
              multiple: false,
              "close-on-select": true,
              "allow-empty": true,
              placeholder: unref(t)("select"),
              label: "label",
              "track-by": "id",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.school_lesson_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "slug" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}> * </span> ${ssrInterpolate(unref(t)("slug"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
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
              class: "w-full",
              autocomplete: "slug",
              onFocus: handleSlugFocus
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.slug
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
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
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}> * </span> ${ssrInterpolate(unref(t)("title"))} [${ssrInterpolate(activeLocale.value.toUpperCase())}] `);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
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
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "short",
              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              id: "short",
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
            _push2(ssrRenderComponent(_sfc_main$b, {
              modelValue: currentTranslation.value.description,
              "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
              height: 500
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("description")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$c, {
              images: newImages.value,
              "onUpdate:images": [($event) => newImages.value = $event, handleNewImagesUpdate]
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.images
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="flex items-center justify-center mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolQuizzes.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0
                                           .7.7 1.2 1.7 1.4 2.7l2-.3
                                           c-.2-1.5-.9-2.8-1.9-3.8
                                           C10.1.4 5.7.4 2.9 3.1
                                           L.7.9 0 7.3l6.4-.7-2.1-2.1z
                                           M15.6 8.7l-6.4.7 2.1 2.1
                                           c-1.9 1.9-5.1 1.9-7 0
                                           -.7-.7-1.2-1.7-1.4-2.7l-2 .3
                                           c.2 1.5.9 2.8 1.9 3.8
                                           1.4 1.4 3.1 2 4.9 2
                                           1.8 0 3.6-.7 4.9-2
                                           l2.2 2.2.8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0\n                                           .7.7 1.2 1.7 1.4 2.7l2-.3\n                                           c-.2-1.5-.9-2.8-1.9-3.8\n                                           C10.1.4 5.7.4 2.9 3.1\n                                           L.7.9 0 7.3l6.4-.7-2.1-2.1z\n                                           M15.6 8.7l-6.4.7 2.1 2.1\n                                           c-1.9 1.9-5.1 1.9-7 0\n                                           -.7-.7-1.2-1.7-1.4-2.7l-2 .3\n                                           c.2 1.5.9 2.8 1.9 3.8\n                                           1.4 1.4 3.1 2 4.9 2\n                                           1.8 0 3.6-.7 4.9-2\n                                           l2.2 2.2.8-6.4z" })
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
            _push2(ssrRenderComponent(_sfc_main$d, {
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
                      href: _ctx.route("admin.schoolQuizzes.index")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0\n                                       .7.7 1.2 1.7 1.4 2.7l2-.3\n                                       c-.2-1.5-.9-2.8-1.9-3.8\n                                       C10.1.4 5.7.4 2.9 3.1\n                                       L.7.9 0 7.3l6.4-.7-2.1-2.1z\n                                       M15.6 8.7l-6.4.7 2.1 2.1\n                                       c-1.9 1.9-5.1 1.9-7 0\n                                       -.7-.7-1.2-1.7-1.4-2.7l-2 .3\n                                       c.2 1.5.9 2.8 1.9 3.8\n                                       1.4 1.4 3.1 2 4.9 2\n                                       1.8 0 3.6-.7 4.9-2\n                                       l2.2 2.2.8-6.4z" })
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
                            modelModifiers: { number: true },
                            class: "w-full lg:w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
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
                      createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                        createVNode(_sfc_main$7, {
                          modelValue: unref(form).type,
                          "onUpdate:modelValue": ($event) => unref(form).type = $event,
                          errorMessage: unref(form).errors.type
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "errorMessage"]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "published_at",
                            value: unref(t)("publishedAt")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$8, {
                            id: "published_at",
                            type: "datetime-local",
                            modelValue: unref(form).published_at,
                            "onUpdate:modelValue": ($event) => unref(form).published_at = $event,
                            class: "w-full max-w-56"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            message: unref(form).errors.published_at
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 grid grid-cols-1 md:grid-cols-3 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "attempts_limit",
                            value: unref(t)("limitCount")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$5, {
                            id: "attempts_limit",
                            type: "number",
                            min: "0",
                            modelValue: unref(form).attempts_limit,
                            "onUpdate:modelValue": ($event) => unref(form).attempts_limit = $event,
                            modelModifiers: { number: true },
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            message: unref(form).errors.attempts_limit
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "time_limit_minutes",
                            value: unref(t)("limitMinutes")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$5, {
                            id: "time_limit_minutes",
                            type: "number",
                            min: "1",
                            modelValue: unref(form).time_limit_minutes,
                            "onUpdate:modelValue": ($event) => unref(form).time_limit_minutes = $event,
                            modelModifiers: { number: true },
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            message: unref(form).errors.time_limit_minutes
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "pass_score",
                            value: unref(t)("passScore")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$5, {
                            id: "pass_score",
                            type: "number",
                            min: "0",
                            max: "100",
                            modelValue: unref(form).pass_score,
                            "onUpdate:modelValue": ($event) => unref(form).pass_score = $event,
                            modelModifiers: { number: true },
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            message: unref(form).errors.pass_score
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
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
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.school_course_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "school_module_id",
                          value: unref(t)("module"),
                          class: "mb-1"
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "school_module_id",
                          modelValue: selectedModule.value,
                          "onUpdate:modelValue": ($event) => selectedModule.value = $event,
                          options: moduleOptions.value,
                          "options-limit": dynamicOptionsLimit(moduleOptions.value),
                          multiple: false,
                          "close-on-select": true,
                          "allow-empty": true,
                          placeholder: unref(t)("select"),
                          label: "label",
                          "track-by": "id",
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.school_module_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "school_lesson_id",
                          value: unref(t)("lesson"),
                          class: "mb-1"
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "school_lesson_id",
                          modelValue: selectedLesson.value,
                          "onUpdate:modelValue": ($event) => selectedLesson.value = $event,
                          options: lessonOptions.value,
                          "options-limit": dynamicOptionsLimit(lessonOptions.value),
                          multiple: false,
                          "close-on-select": true,
                          "allow-empty": true,
                          placeholder: unref(t)("select"),
                          label: "label",
                          "track-by": "id",
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.school_lesson_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, { for: "slug" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                            createTextVNode(" " + toDisplayString(unref(t)("slug")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$8, {
                          id: "slug",
                          type: "text",
                          modelValue: unref(form).slug,
                          "onUpdate:modelValue": ($event) => unref(form).slug = $event,
                          class: "w-full",
                          autocomplete: "slug",
                          onFocus: handleSlugFocus
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.slug
                        }, null, 8, ["message"])
                      ]),
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
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                            createTextVNode(" " + toDisplayString(unref(t)("title")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$8, {
                          id: "title",
                          type: "text",
                          modelValue: currentTranslation.value.title,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("title")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "short",
                          value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$a, {
                          id: "short",
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
                        createVNode(_sfc_main$b, {
                          modelValue: currentTranslation.value.description,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
                          height: 500
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("description")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3" }, [
                        createVNode(_sfc_main$c, {
                          images: newImages.value,
                          "onUpdate:images": [($event) => newImages.value = $event, handleNewImagesUpdate]
                        }, null, 8, ["images", "onUpdate:images"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.images
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center mt-4" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.schoolQuizzes.index")
                      }, {
                        icon: withCtx(() => [
                          (openBlock(), createBlock("svg", {
                            class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                            viewBox: "0 0 16 16"
                          }, [
                            createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0\n                                           .7.7 1.2 1.7 1.4 2.7l2-.3\n                                           c-.2-1.5-.9-2.8-1.9-3.8\n                                           C10.1.4 5.7.4 2.9 3.1\n                                           L.7.9 0 7.3l6.4-.7-2.1-2.1z\n                                           M15.6 8.7l-6.4.7 2.1 2.1\n                                           c-1.9 1.9-5.1 1.9-7 0\n                                           -.7-.7-1.2-1.7-1.4-2.7l-2 .3\n                                           c.2 1.5.9 2.8 1.9 3.8\n                                           1.4 1.4 3.1 2 4.9 2\n                                           1.8 0 3.6-.7 4.9-2\n                                           l2.2 2.2.8-6.4z" })
                          ]))
                        ]),
                        default: withCtx(() => [
                          createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                        ]),
                        _: 1
                      }, 8, ["href"]),
                      createVNode(_sfc_main$d, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolQuizzes/Create.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
