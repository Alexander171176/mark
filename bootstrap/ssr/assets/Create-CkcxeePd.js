import { ref, computed, watch, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$f } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$d } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$c } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$5 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$4 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$8 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$6 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$e } from "./MultiImageUpload-Bg2ahSyk.js";
import { _ as _sfc_main$b } from "./TranslationTabs-czH7YSpu.js";
import { _ as _sfc_main$7, a as _sfc_main$9, b as _sfc_main$a } from "./SelectGradingType-CoEcogCT.js";
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
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    courses: { type: Array, default: () => [] },
    modules: { type: Array, default: () => [] },
    lessons: { type: Array, default: () => [] },
    instructors: { type: Array, default: () => [] }
  },
  setup(__props) {
    var _a;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const makeTranslation = () => ({
      title: "",
      subtitle: "",
      short: "",
      description: "",
      instructions: ""
    });
    const defaultLocale = props.currentLocale || ((_a = props.availableLocales) == null ? void 0 : _a[0]) || "ru";
    const activeLocale = ref(defaultLocale);
    const form = useForm({
      school_course_id: null,
      school_module_id: null,
      school_lesson_id: null,
      school_instructor_profile_id: null,
      activity: true,
      left: false,
      main: false,
      right: false,
      sort: 0,
      slug: "",
      status: "draft",
      visibility: "enrolled",
      attempts_limit: 0,
      grading_type: "manual",
      max_score: 100,
      published_at: "",
      due_at: "",
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
    const dynamicOptionsLimit = (items) => {
      return Array.isArray(items) ? items.length + 10 : 10;
    };
    const getTranslationForLocale = (entity) => {
      if (!entity) return null;
      if (Array.isArray(entity.translations)) {
        const exact = entity.translations.find(
          (translation) => (translation == null ? void 0 : translation.locale) === activeLocale.value
        );
        if (exact) return exact;
      }
      return entity.translation || null;
    };
    const getEntityTitle = (entity) => {
      var _a2;
      return ((_a2 = getTranslationForLocale(entity)) == null ? void 0 : _a2.title) || (entity == null ? void 0 : entity.slug) || `#${(entity == null ? void 0 : entity.id) ?? ""}`;
    };
    const findOptionById = (options, id) => {
      if (id === null || id === void 0 || id === "") {
        return null;
      }
      return options.find(
        (option) => Number(option.id) === Number(id)
      ) || null;
    };
    const courseOptions = computed(() => {
      return (props.courses || []).map((course) => ({
        id: course.id,
        label: `[ID: ${course.id}] ${getEntityTitle(course)}`
      }));
    });
    const moduleOptions = computed(() => {
      return (props.modules || []).map((module) => {
        var _a2;
        const moduleTitle = getEntityTitle(module);
        const courseTitle = (module == null ? void 0 : module.course) ? getEntityTitle(module.course) : "";
        return {
          id: module.id,
          course_id: module.school_course_id ?? ((_a2 = module.course) == null ? void 0 : _a2.id) ?? null,
          label: courseTitle ? `[ID: ${module.id}] [${courseTitle}] ${moduleTitle}` : `[ID: ${module.id}] ${moduleTitle}`
        };
      });
    });
    const lessonOptions = computed(() => {
      return (props.lessons || []).map((lesson) => {
        const lessonTitle = getEntityTitle(lesson);
        const module = (lesson == null ? void 0 : lesson.module) || null;
        const course = (module == null ? void 0 : module.course) || (lesson == null ? void 0 : lesson.course) || null;
        const moduleTitle = module ? getEntityTitle(module) : "";
        const courseTitle = course ? getEntityTitle(course) : "";
        let label = lessonTitle;
        if (courseTitle && moduleTitle) {
          label = `[${courseTitle}] [${moduleTitle}] ${lessonTitle}`;
        } else if (moduleTitle) {
          label = `[${moduleTitle}] ${lessonTitle}`;
        } else if (courseTitle) {
          label = `[${courseTitle}] ${lessonTitle}`;
        }
        return {
          id: lesson.id,
          module_id: lesson.school_module_id ?? (module == null ? void 0 : module.id) ?? null,
          course_id: (course == null ? void 0 : course.id) ?? (module == null ? void 0 : module.school_course_id) ?? null,
          label: `[ID: ${lesson.id}] ${label}`
        };
      });
    });
    const instructorOptions = computed(() => {
      return (props.instructors || []).map((instructor) => {
        var _a2, _b;
        const profileTitle = getEntityTitle(instructor);
        const userName = ((_a2 = instructor == null ? void 0 : instructor.user) == null ? void 0 : _a2.name) || ((_b = instructor == null ? void 0 : instructor.user) == null ? void 0 : _b.email) || "";
        return {
          id: instructor.id,
          label: userName && userName !== profileTitle ? `[ID: ${instructor.id}] ${profileTitle} — ${userName}` : `[ID: ${instructor.id}] ${profileTitle}`
        };
      });
    });
    const selectedCourse = ref(null);
    const selectedModule = ref(null);
    const selectedLesson = ref(null);
    const selectedInstructor = ref(null);
    const syncSelectedOptions = () => {
      selectedCourse.value = findOptionById(
        courseOptions.value,
        form.school_course_id
      );
      selectedModule.value = findOptionById(
        moduleOptions.value,
        form.school_module_id
      );
      selectedLesson.value = findOptionById(
        lessonOptions.value,
        form.school_lesson_id
      );
      selectedInstructor.value = findOptionById(
        instructorOptions.value,
        form.school_instructor_profile_id
      );
    };
    watch(
      [courseOptions, moduleOptions, lessonOptions, instructorOptions],
      syncSelectedOptions,
      { immediate: true }
    );
    watch(selectedCourse, (value) => {
      form.school_course_id = (value == null ? void 0 : value.id) ?? null;
    });
    watch(selectedModule, (value) => {
      form.school_module_id = (value == null ? void 0 : value.id) ?? null;
      if (!(value == null ? void 0 : value.course_id)) return;
      form.school_course_id = value.course_id;
      selectedCourse.value = findOptionById(
        courseOptions.value,
        value.course_id
      );
    });
    watch(selectedLesson, (value) => {
      form.school_lesson_id = (value == null ? void 0 : value.id) ?? null;
      if (value == null ? void 0 : value.module_id) {
        form.school_module_id = value.module_id;
        selectedModule.value = findOptionById(
          moduleOptions.value,
          value.module_id
        );
      }
      if (value == null ? void 0 : value.course_id) {
        form.school_course_id = value.course_id;
        selectedCourse.value = findOptionById(
          courseOptions.value,
          value.course_id
        );
      }
    });
    watch(selectedInstructor, (value) => {
      form.school_instructor_profile_id = (value == null ? void 0 : value.id) ?? null;
    });
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
      form.transform((data) => {
        const transformed = {
          ...data,
          school_course_id: data.school_course_id ?? null,
          school_module_id: data.school_module_id ?? null,
          school_lesson_id: data.school_lesson_id ?? null,
          school_instructor_profile_id: data.school_instructor_profile_id ?? null,
          activity: data.activity ? 1 : 0,
          left: data.left ? 1 : 0,
          main: data.main ? 1 : 0,
          right: data.right ? 1 : 0,
          sort: data.sort === "" || data.sort === null ? 0 : Number(data.sort),
          attempts_limit: data.attempts_limit === "" || data.attempts_limit === null ? 0 : Number(data.attempts_limit),
          max_score: data.max_score === "" || data.max_score === null ? 100 : Number(data.max_score)
        };
        newImages.value.forEach((image, index) => {
          transformed[`images[${index}][file]`] = image.file;
          transformed[`images[${index}][order]`] = image.order ?? 0;
          transformed[`images[${index}][alt]`] = image.alt ?? "";
          transformed[`images[${index}][caption]`] = image.caption ?? "";
        });
        return transformed;
      });
      form.post(
        route("admin.schoolAssignments.store"),
        {
          errorBag: "createSchoolAssignment",
          preserveScroll: true,
          forceFormData: true,
          onSuccess: () => {
            toast.success("Задание успешно создано!");
          },
          onError: (errors) => {
            console.error(
              "Ошибка создания задания:",
              errors
            );
            const firstKey = Object.keys(errors || {})[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || "Проверьте корректность полей."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("createAssignment")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("createAssignment"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("createAssignment")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("createAssignment")), 1)
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
              href: _ctx.route("admin.schoolAssignments.index")
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
              modelValue: unref(form).status,
              "onUpdate:modelValue": ($event) => unref(form).status = $event,
              errorMessage: unref(form).errors.status
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
              autocomplete: "published_at",
              class: "w-full max-w-56"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-1 sm:mt-0",
              message: unref(form).errors.published_at
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              modelValue: unref(form).visibility,
              "onUpdate:modelValue": ($event) => unref(form).visibility = $event,
              errorMessage: unref(form).errors.visibility
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "attempts_limit",
              value: unref(t)("attemptsLimit")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "attempts_limit",
              type: "number",
              min: "0",
              modelValue: unref(form).attempts_limit,
              "onUpdate:modelValue": ($event) => unref(form).attempts_limit = $event,
              autocomplete: "attempts_limit",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-1",
              message: unref(form).errors.attempts_limit
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            _push2(ssrRenderComponent(_sfc_main$a, {
              modelValue: unref(form).grading_type,
              "onUpdate:modelValue": ($event) => unref(form).grading_type = $event,
              errorMessage: unref(form).errors.grading_type
            }, null, _parent2, _scopeId));
            _push2(`<div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "max_score",
              value: unref(t)("maxScore")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "max_score",
              type: "number",
              min: "0",
              modelValue: unref(form).max_score,
              "onUpdate:modelValue": ($event) => unref(form).max_score = $event,
              autocomplete: "max_score",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-1",
              message: unref(form).errors.max_score
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "due_at",
              value: unref(t)("dueAt")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "due_at",
              type: "datetime-local",
              modelValue: unref(form).due_at,
              "onUpdate:modelValue": ($event) => unref(form).due_at = $event,
              autocomplete: "due_at",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-1",
              message: unref(form).errors.due_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "instructor",
              value: unref(t)("instructor"),
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "instructor",
              modelValue: selectedInstructor.value,
              "onUpdate:modelValue": ($event) => selectedInstructor.value = $event,
              options: instructorOptions.value,
              "options-limit": dynamicOptionsLimit(instructorOptions.value),
              multiple: false,
              "close-on-select": true,
              "clear-on-select": false,
              "preserve-search": true,
              placeholder: unref(t)("select"),
              label: "label",
              "track-by": "id",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.school_instructor_profile_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "course",
              value: `${unref(t)("course")} (${unref(t)("autoCorrect")})`,
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "course",
              modelValue: selectedCourse.value,
              "onUpdate:modelValue": ($event) => selectedCourse.value = $event,
              options: courseOptions.value,
              "options-limit": dynamicOptionsLimit(courseOptions.value),
              multiple: false,
              "close-on-select": true,
              "clear-on-select": false,
              "preserve-search": true,
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
              for: "module",
              value: `${unref(t)("module")} (${unref(t)("autoCorrect")})`,
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "module",
              modelValue: selectedModule.value,
              "onUpdate:modelValue": ($event) => selectedModule.value = $event,
              options: moduleOptions.value,
              "options-limit": dynamicOptionsLimit(moduleOptions.value),
              multiple: false,
              "close-on-select": true,
              "clear-on-select": false,
              "preserve-search": true,
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
              for: "lesson",
              value: unref(t)("lesson"),
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "lesson",
              modelValue: selectedLesson.value,
              "onUpdate:modelValue": ($event) => selectedLesson.value = $event,
              options: lessonOptions.value,
              "options-limit": dynamicOptionsLimit(lessonOptions.value),
              multiple: false,
              "close-on-select": true,
              "clear-on-select": false,
              "preserve-search": true,
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
              onFocus: handleSlugFocus
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
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
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.title || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "title",
              type: "text",
              modelValue: currentTranslation.value.title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
              maxlength: "255",
              required: ""
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
            _push2(ssrRenderComponent(_sfc_main$c, {
              modelValue: currentTranslation.value.subtitle,
              "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("subtitle")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "short",
              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$c, {
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
            _push2(ssrRenderComponent(_sfc_main$d, {
              modelValue: currentTranslation.value.description,
              "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
              height: 500
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("description")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "instructions",
              value: `${unref(t)("instructions")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              modelValue: currentTranslation.value.instructions,
              "onUpdate:modelValue": ($event) => currentTranslation.value.instructions = $event,
              height: 400
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("instructions")
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$e, { "onUpdate:images": handleNewImagesUpdate }, null, _parent2, _scopeId));
            if (newImages.value.length) {
              _push2(`<div class="text-xs text-slate-600 dark:text-slate-300 mt-2"${_scopeId}>${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(newImages.value.length)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="flex items-center justify-center gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolAssignments.index")
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
            _push2(ssrRenderComponent(_sfc_main$f, {
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
              createVNode("div", { class: "px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-2" }, [
                    createVNode(_sfc_main$1, {
                      href: _ctx.route("admin.schoolAssignments.index")
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
                          modelValue: unref(form).status,
                          "onUpdate:modelValue": ($event) => unref(form).status = $event,
                          errorMessage: unref(form).errors.status
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
                            autocomplete: "published_at",
                            class: "w-full max-w-56"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-1 sm:mt-0",
                            message: unref(form).errors.published_at
                          }, null, 8, ["message"])
                        ]),
                        createVNode(_sfc_main$9, {
                          modelValue: unref(form).visibility,
                          "onUpdate:modelValue": ($event) => unref(form).visibility = $event,
                          errorMessage: unref(form).errors.visibility
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "errorMessage"])
                      ]),
                      createVNode("div", { class: "mb-3 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "attempts_limit",
                            value: unref(t)("attemptsLimit")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$5, {
                            id: "attempts_limit",
                            type: "number",
                            min: "0",
                            modelValue: unref(form).attempts_limit,
                            "onUpdate:modelValue": ($event) => unref(form).attempts_limit = $event,
                            autocomplete: "attempts_limit",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-1",
                            message: unref(form).errors.attempts_limit
                          }, null, 8, ["message"])
                        ]),
                        createVNode(_sfc_main$a, {
                          modelValue: unref(form).grading_type,
                          "onUpdate:modelValue": ($event) => unref(form).grading_type = $event,
                          errorMessage: unref(form).errors.grading_type
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "errorMessage"]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "max_score",
                            value: unref(t)("maxScore")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$5, {
                            id: "max_score",
                            type: "number",
                            min: "0",
                            modelValue: unref(form).max_score,
                            "onUpdate:modelValue": ($event) => unref(form).max_score = $event,
                            autocomplete: "max_score",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-1",
                            message: unref(form).errors.max_score
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "due_at",
                            value: unref(t)("dueAt")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$8, {
                            id: "due_at",
                            type: "datetime-local",
                            modelValue: unref(form).due_at,
                            "onUpdate:modelValue": ($event) => unref(form).due_at = $event,
                            autocomplete: "due_at",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-1",
                            message: unref(form).errors.due_at
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "instructor",
                          value: unref(t)("instructor"),
                          class: "mb-1"
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "instructor",
                          modelValue: selectedInstructor.value,
                          "onUpdate:modelValue": ($event) => selectedInstructor.value = $event,
                          options: instructorOptions.value,
                          "options-limit": dynamicOptionsLimit(instructorOptions.value),
                          multiple: false,
                          "close-on-select": true,
                          "clear-on-select": false,
                          "preserve-search": true,
                          placeholder: unref(t)("select"),
                          label: "label",
                          "track-by": "id",
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.school_instructor_profile_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "course",
                          value: `${unref(t)("course")} (${unref(t)("autoCorrect")})`,
                          class: "mb-1"
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "course",
                          modelValue: selectedCourse.value,
                          "onUpdate:modelValue": ($event) => selectedCourse.value = $event,
                          options: courseOptions.value,
                          "options-limit": dynamicOptionsLimit(courseOptions.value),
                          multiple: false,
                          "close-on-select": true,
                          "clear-on-select": false,
                          "preserve-search": true,
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
                          for: "module",
                          value: `${unref(t)("module")} (${unref(t)("autoCorrect")})`,
                          class: "mb-1"
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "module",
                          modelValue: selectedModule.value,
                          "onUpdate:modelValue": ($event) => selectedModule.value = $event,
                          options: moduleOptions.value,
                          "options-limit": dynamicOptionsLimit(moduleOptions.value),
                          multiple: false,
                          "close-on-select": true,
                          "clear-on-select": false,
                          "preserve-search": true,
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
                          for: "lesson",
                          value: unref(t)("lesson"),
                          class: "mb-1"
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "lesson",
                          modelValue: selectedLesson.value,
                          "onUpdate:modelValue": ($event) => selectedLesson.value = $event,
                          options: lessonOptions.value,
                          "options-limit": dynamicOptionsLimit(lessonOptions.value),
                          multiple: false,
                          "close-on-select": true,
                          "clear-on-select": false,
                          "preserve-search": true,
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
                          onFocus: handleSlugFocus
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
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
                            createVNode(_sfc_main$4, { for: "title" }, {
                              default: withCtx(() => [
                                createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                                createTextVNode(" " + toDisplayString(unref(t)("title")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                              ]),
                              _: 1
                            }),
                            createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.title || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                          ]),
                          createVNode(_sfc_main$8, {
                            id: "title",
                            type: "text",
                            modelValue: currentTranslation.value.title,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
                            maxlength: "255",
                            required: ""
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
                          createVNode(_sfc_main$c, {
                            modelValue: currentTranslation.value.subtitle,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.subtitle = $event,
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2",
                            message: getError("subtitle")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "short",
                            value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$c, {
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
                          createVNode(_sfc_main$d, {
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
                          createVNode(_sfc_main$4, {
                            for: "instructions",
                            value: `${unref(t)("instructions")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$d, {
                            modelValue: currentTranslation.value.instructions,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.instructions = $event,
                            height: 400
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2",
                            message: getError("instructions")
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mt-4" }, [
                        createVNode(_sfc_main$e, { "onUpdate:images": handleNewImagesUpdate }),
                        newImages.value.length ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-xs text-slate-600 dark:text-slate-300 mt-2"
                        }, toDisplayString(unref(t)("images")) + ": " + toDisplayString(newImages.value.length), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center gap-3" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.schoolAssignments.index")
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
                      createVNode(_sfc_main$f, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolAssignments/Create.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
