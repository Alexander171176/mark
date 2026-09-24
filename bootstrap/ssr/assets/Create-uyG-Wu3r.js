import { computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, withDirectives, Fragment, renderList, vModelSelect, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$6 } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$2 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$5 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$4 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$3 } from "./InputError-CLVdJ1nk.js";
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
    users: {
      type: Array,
      default: () => []
    },
    quizzes: {
      type: Array,
      default: () => []
    },
    enrollments: {
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
    },
    defaultUserId: {
      type: Number,
      default: null
    },
    defaultQuizId: {
      type: Number,
      default: null
    },
    defaultEnrollmentId: {
      type: Number,
      default: null
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const dynamicOptionsLimit = (items) => {
      const list = Array.isArray(items) ? items : [];
      return list.length + 10;
    };
    const fromDatetimeLocal = (value) => {
      if (!value) return null;
      const date = new Date(value);
      return Number.isFinite(date.getTime()) ? date.toISOString() : null;
    };
    const toNumberOrNull = (value) => {
      if (value === "" || value === null || typeof value === "undefined") {
        return null;
      }
      const number = Number(value);
      return Number.isFinite(number) ? number : null;
    };
    const getTitle = (item) => {
      var _a, _b;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || ((_b = item == null ? void 0 : item.translation) == null ? void 0 : _b.name) || (item == null ? void 0 : item.slug) || null;
    };
    const findById = (items, id) => {
      if (!id) return null;
      return items.find((item) => Number(item.id) === Number(id)) || null;
    };
    const statusOptions = computed(() => [
      {
        value: "in_progress",
        label: t("setStatusInProgress")
      },
      {
        value: "completed",
        label: t("setStatusCompleted")
      },
      {
        value: "graded",
        label: t("setStatusGraded")
      }
    ]);
    const userOptions = computed(
      () => (props.users || []).map((user) => ({
        id: user.id,
        name: user.name,
        email: user.email,
        label: `[ID: ${user.id}] ${user.name || "—"}${user.email ? ` (${user.email})` : ""}`
      }))
    );
    const quizOptions = computed(
      () => (props.quizzes || []).map((quiz) => {
        var _a, _b, _c;
        const context = [
          getTitle(quiz.course) ? `Курс: ${getTitle(quiz.course)}` : null,
          getTitle(quiz.module) ? `Модуль: ${getTitle(quiz.module)}` : null,
          getTitle(quiz.lesson) ? `Урок: ${getTitle(quiz.lesson)}` : null
        ].filter(Boolean).join(" / ");
        const title = getTitle(quiz) || `#${quiz.id}`;
        return {
          id: quiz.id,
          school_course_id: quiz.school_course_id ?? ((_a = quiz.course) == null ? void 0 : _a.id) ?? null,
          school_module_id: quiz.school_module_id ?? ((_b = quiz.module) == null ? void 0 : _b.id) ?? null,
          school_lesson_id: quiz.school_lesson_id ?? ((_c = quiz.lesson) == null ? void 0 : _c.id) ?? null,
          label: context ? `[ID: ${quiz.id}] ${title} — ${context}` : `[ID: ${quiz.id}] ${title}`
        };
      })
    );
    const courseOptions = computed(
      () => (props.courses || []).map((course) => ({
        id: course.id,
        label: `[ID: ${course.id}] ${getTitle(course) || `#${course.id}`}`
      }))
    );
    const moduleOptionsAll = computed(
      () => (props.modules || []).map((module) => {
        var _a;
        return {
          id: module.id,
          school_course_id: module.school_course_id ?? ((_a = module.course) == null ? void 0 : _a.id) ?? null,
          label: `[ID: ${module.id}] ${getTitle(module) || `#${module.id}`}`
        };
      })
    );
    const lessonOptionsAll = computed(
      () => (props.lessons || []).map((lesson) => {
        var _a;
        return {
          id: lesson.id,
          school_module_id: lesson.school_module_id ?? ((_a = lesson.module) == null ? void 0 : _a.id) ?? null,
          label: `[ID: ${lesson.id}] ${getTitle(lesson) || `#${lesson.id}`}`
        };
      })
    );
    const enrollmentOptionsAll = computed(
      () => (props.enrollments || []).map((enrollment) => {
        var _a, _b;
        const userTitle = ((_a = enrollment.user) == null ? void 0 : _a.name) ? `${enrollment.user.name}${enrollment.user.email ? ` (${enrollment.user.email})` : ""}` : `User ID: ${enrollment.user_id || "—"}`;
        const courseTitle = getTitle(enrollment.course) || `Course ID: ${enrollment.school_course_id || "—"}`;
        return {
          id: enrollment.id,
          user_id: enrollment.user_id ?? null,
          school_course_id: enrollment.school_course_id ?? ((_b = enrollment.course) == null ? void 0 : _b.id) ?? null,
          label: `[ID: ${enrollment.id}] ${userTitle} — ${courseTitle}`
        };
      })
    );
    const initialQuiz = findById(
      quizOptions.value,
      props.defaultQuizId
    );
    const initialEnrollment = findById(
      enrollmentOptionsAll.value,
      props.defaultEnrollmentId
    );
    const initialCourseId = (initialQuiz == null ? void 0 : initialQuiz.school_course_id) ?? (initialEnrollment == null ? void 0 : initialEnrollment.school_course_id) ?? null;
    const initialEnrollmentIsCompatible = !initialEnrollment || !initialCourseId || Number(initialEnrollment.school_course_id) === Number(initialCourseId);
    const form = useForm({
      user_id: props.defaultUserId ?? (initialEnrollment == null ? void 0 : initialEnrollment.user_id) ?? null,
      school_quiz_id: (initialQuiz == null ? void 0 : initialQuiz.id) ?? props.defaultQuizId ?? null,
      school_enrollment_id: initialEnrollmentIsCompatible ? (initialEnrollment == null ? void 0 : initialEnrollment.id) ?? null : null,
      school_course_id: initialCourseId,
      school_module_id: (initialQuiz == null ? void 0 : initialQuiz.school_module_id) ?? null,
      school_lesson_id: (initialQuiz == null ? void 0 : initialQuiz.school_lesson_id) ?? null,
      attempt_number: "",
      status: "in_progress",
      score: "",
      max_score: "",
      started_at: "",
      finished_at: "",
      duration_seconds: ""
    });
    const moduleOptions = computed(() => {
      if (!form.school_course_id) {
        return moduleOptionsAll.value;
      }
      return moduleOptionsAll.value.filter(
        (module) => Number(module.school_course_id) === Number(form.school_course_id)
      );
    });
    const lessonOptions = computed(() => {
      if (!form.school_module_id) {
        return lessonOptionsAll.value;
      }
      return lessonOptionsAll.value.filter(
        (lesson) => Number(lesson.school_module_id) === Number(form.school_module_id)
      );
    });
    const enrollmentOptions = computed(() => {
      let list = enrollmentOptionsAll.value;
      if (form.user_id) {
        list = list.filter(
          (enrollment) => Number(enrollment.user_id) === Number(form.user_id)
        );
      }
      if (form.school_course_id) {
        list = list.filter(
          (enrollment) => Number(enrollment.school_course_id) === Number(form.school_course_id)
        );
      }
      return list;
    });
    const selectedUser = computed({
      get: () => findById(userOptions.value, form.user_id),
      set: (user) => {
        form.user_id = (user == null ? void 0 : user.id) ?? null;
        if (!form.school_enrollment_id) return;
        const enrollment = findById(
          enrollmentOptionsAll.value,
          form.school_enrollment_id
        );
        if (!user || enrollment && Number(enrollment.user_id) !== Number(user.id)) {
          form.school_enrollment_id = null;
        }
      }
    });
    const selectedQuiz = computed({
      get: () => findById(quizOptions.value, form.school_quiz_id),
      set: (quiz) => {
        form.school_quiz_id = (quiz == null ? void 0 : quiz.id) ?? null;
        if (!quiz) {
          form.school_course_id = null;
          form.school_module_id = null;
          form.school_lesson_id = null;
          form.school_enrollment_id = null;
          return;
        }
        form.school_course_id = quiz.school_course_id ?? null;
        form.school_module_id = quiz.school_module_id ?? null;
        form.school_lesson_id = quiz.school_lesson_id ?? null;
        if (!form.school_enrollment_id || !form.school_course_id) return;
        const enrollment = findById(
          enrollmentOptionsAll.value,
          form.school_enrollment_id
        );
        if (enrollment && Number(enrollment.school_course_id) !== Number(form.school_course_id)) {
          form.school_enrollment_id = null;
        }
      }
    });
    const selectedCourse = computed({
      get: () => findById(courseOptions.value, form.school_course_id),
      set: (course) => {
        form.school_course_id = (course == null ? void 0 : course.id) ?? null;
        if (!course) {
          form.school_module_id = null;
          form.school_lesson_id = null;
          form.school_enrollment_id = null;
          return;
        }
        if (form.school_module_id) {
          const module = findById(
            moduleOptionsAll.value,
            form.school_module_id
          );
          if (module && Number(module.school_course_id) !== Number(course.id)) {
            form.school_module_id = null;
            form.school_lesson_id = null;
          }
        }
        if (form.school_enrollment_id) {
          const enrollment = findById(
            enrollmentOptionsAll.value,
            form.school_enrollment_id
          );
          if (enrollment && Number(enrollment.school_course_id) !== Number(course.id)) {
            form.school_enrollment_id = null;
          }
        }
      }
    });
    const selectedModule = computed({
      get: () => findById(moduleOptionsAll.value, form.school_module_id),
      set: (module) => {
        form.school_module_id = (module == null ? void 0 : module.id) ?? null;
        if (!module) {
          form.school_lesson_id = null;
          return;
        }
        if (module.school_course_id) {
          form.school_course_id = module.school_course_id;
          if (form.school_enrollment_id) {
            const enrollment = findById(
              enrollmentOptionsAll.value,
              form.school_enrollment_id
            );
            if (enrollment && Number(enrollment.school_course_id) !== Number(module.school_course_id)) {
              form.school_enrollment_id = null;
            }
          }
        }
        if (form.school_lesson_id) {
          const lesson = findById(
            lessonOptionsAll.value,
            form.school_lesson_id
          );
          if (lesson && Number(lesson.school_module_id) !== Number(module.id)) {
            form.school_lesson_id = null;
          }
        }
      }
    });
    const selectedLesson = computed({
      get: () => findById(lessonOptionsAll.value, form.school_lesson_id),
      set: (lesson) => {
        form.school_lesson_id = (lesson == null ? void 0 : lesson.id) ?? null;
        if (!(lesson == null ? void 0 : lesson.school_module_id)) return;
        const module = findById(
          moduleOptionsAll.value,
          lesson.school_module_id
        );
        if (!module) return;
        form.school_module_id = module.id;
        if (module.school_course_id) {
          form.school_course_id = module.school_course_id;
          if (form.school_enrollment_id) {
            const enrollment = findById(
              enrollmentOptionsAll.value,
              form.school_enrollment_id
            );
            if (enrollment && Number(enrollment.school_course_id) !== Number(module.school_course_id)) {
              form.school_enrollment_id = null;
            }
          }
        }
      }
    });
    const selectedEnrollment = computed({
      get: () => findById(enrollmentOptionsAll.value, form.school_enrollment_id),
      set: (enrollment) => {
        form.school_enrollment_id = (enrollment == null ? void 0 : enrollment.id) ?? null;
        if (!enrollment) return;
        if (enrollment.user_id) {
          form.user_id = enrollment.user_id;
        }
        if (!enrollment.school_course_id) return;
        const courseChanged = form.school_course_id && Number(form.school_course_id) !== Number(enrollment.school_course_id);
        form.school_course_id = enrollment.school_course_id;
        if (courseChanged && form.school_module_id) {
          const module = findById(
            moduleOptionsAll.value,
            form.school_module_id
          );
          if (module && Number(module.school_course_id) !== Number(enrollment.school_course_id)) {
            form.school_module_id = null;
            form.school_lesson_id = null;
          }
        }
      }
    });
    const submitForm = () => {
      form.transform((data) => ({
        ...data,
        user_id: data.user_id ?? null,
        school_quiz_id: data.school_quiz_id ?? null,
        school_enrollment_id: data.school_enrollment_id ?? null,
        school_course_id: data.school_course_id ?? null,
        school_module_id: data.school_module_id ?? null,
        school_lesson_id: data.school_lesson_id ?? null,
        attempt_number: toNumberOrNull(data.attempt_number),
        score: toNumberOrNull(data.score),
        max_score: toNumberOrNull(data.max_score),
        started_at: fromDatetimeLocal(data.started_at),
        finished_at: fromDatetimeLocal(data.finished_at),
        duration_seconds: toNumberOrNull(data.duration_seconds)
      }));
      form.post(route("admin.schoolQuizAttempts.store"), {
        preserveScroll: true,
        onSuccess: () => {
          toast.success("Попытка квиза успешно создана.");
        },
        onError: (errors) => {
          const firstKey = Object.keys(errors || {})[0];
          toast.error(
            (errors == null ? void 0 : errors[firstKey]) || "Проверьте правильность заполнения полей."
          );
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("createQuizAttempt")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("createQuizAttempt"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("createQuizAttempt")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("createQuizAttempt")), 1)
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
              href: _ctx.route("admin.schoolQuizAttempts.index")
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
            _push2(`</div><form class="p-3 w-full space-y-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "user" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("user"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("user")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "user",
              modelValue: selectedUser.value,
              "onUpdate:modelValue": ($event) => selectedUser.value = $event,
              options: userOptions.value,
              "options-limit": dynamicOptionsLimit(userOptions.value),
              multiple: false,
              "close-on-select": true,
              "clear-on-select": false,
              "preserve-search": true,
              placeholder: unref(t)("select"),
              label: "label",
              "track-by": "id",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.user_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "quiz" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("quiz"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("quiz")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "quiz",
              modelValue: selectedQuiz.value,
              "onUpdate:modelValue": ($event) => selectedQuiz.value = $event,
              options: quizOptions.value,
              "options-limit": dynamicOptionsLimit(quizOptions.value),
              multiple: false,
              "close-on-select": true,
              "clear-on-select": false,
              "preserve-search": true,
              placeholder: unref(t)("select"),
              label: "label",
              "track-by": "id",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.school_quiz_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40"${_scopeId}><div class="text-center text-md font-semibold opacity-80 mb-3"${_scopeId}>${ssrInterpolate(unref(t)("context"))}</div><div class="grid grid-cols-1 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "lesson" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("lesson"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("lesson")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
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
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.school_lesson_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "module" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("module"))} (${ssrInterpolate(unref(t)("autoCorrect"))}) `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("module")) + " (" + toDisplayString(unref(t)("autoCorrect")) + ") ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
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
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.school_module_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "course" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("course"))} (${ssrInterpolate(unref(t)("autoCorrect"))}) `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("course")) + " (" + toDisplayString(unref(t)("autoCorrect")) + ") ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
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
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.school_course_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "enrollment" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("enrollment"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("enrollment")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "enrollment",
              modelValue: selectedEnrollment.value,
              "onUpdate:modelValue": ($event) => selectedEnrollment.value = $event,
              options: enrollmentOptions.value,
              "options-limit": dynamicOptionsLimit(enrollmentOptions.value),
              multiple: false,
              "close-on-select": true,
              "clear-on-select": false,
              "preserve-search": true,
              placeholder: unref(t)("select"),
              label: "label",
              "track-by": "id",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.school_enrollment_id
            }, null, _parent2, _scopeId));
            _push2(`</div></div></div><div class="grid grid-cols-1 lg:grid-cols-3 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "duration_seconds" }, {
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
            _push2(ssrRenderComponent(_sfc_main$4, {
              id: "duration_seconds",
              type: "number",
              min: "0",
              modelValue: unref(form).duration_seconds,
              "onUpdate:modelValue": ($event) => unref(form).duration_seconds = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.duration_seconds
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "started_at" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("shortStarted"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("shortStarted")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "started_at",
              type: "datetime-local",
              modelValue: unref(form).started_at,
              "onUpdate:modelValue": ($event) => unref(form).started_at = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.started_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "finished_at" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("shortExpires"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("shortExpires")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "finished_at",
              type: "datetime-local",
              modelValue: unref(form).finished_at,
              "onUpdate:modelValue": ($event) => unref(form).finished_at = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.finished_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-4 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "attempt_number" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("attemptNumber"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("attemptNumber")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              id: "attempt_number",
              type: "number",
              min: "1",
              modelValue: unref(form).attempt_number,
              "onUpdate:modelValue": ($event) => unref(form).attempt_number = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.attempt_number
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "score" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("score"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("score")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              id: "score",
              type: "number",
              min: "0",
              modelValue: unref(form).score,
              "onUpdate:modelValue": ($event) => unref(form).score = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.score
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "max_score" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("maxScore"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("maxScore")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              id: "max_score",
              type: "number",
              min: "0",
              modelValue: unref(form).max_score,
              "onUpdate:modelValue": ($event) => unref(form).max_score = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.max_score
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "status" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("status"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("status")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<select id="status" class="w-full py-0.5 font-semibold text-sm border border-slate-500 rounded-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-300 dark:bg-cyan-800 dark:text-slate-100"${_scopeId}><!--[-->`);
            ssrRenderList(statusOptions.value, (option) => {
              _push2(`<option${ssrRenderAttr("value", option.value)}${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, option.value) : ssrLooseEqual(unref(form).status, option.value)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(option.label)}</option>`);
            });
            _push2(`<!--]--></select>`);
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.status
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="flex items-center justify-center gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolQuizAttempts.index")
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
            _push2(ssrRenderComponent(_sfc_main$6, {
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
                    createVNode(_sfc_main$1, {
                      href: _ctx.route("admin.schoolQuizAttempts.index")
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
                    class: "p-3 w-full space-y-4"
                  }, [
                    createVNode("div", { class: "flex flex-col items-start" }, [
                      createVNode(_sfc_main$2, { for: "user" }, {
                        default: withCtx(() => [
                          createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                          createTextVNode(" " + toDisplayString(unref(t)("user")), 1)
                        ]),
                        _: 1
                      }),
                      createVNode(unref(VueMultiselect), {
                        id: "user",
                        modelValue: selectedUser.value,
                        "onUpdate:modelValue": ($event) => selectedUser.value = $event,
                        options: userOptions.value,
                        "options-limit": dynamicOptionsLimit(userOptions.value),
                        multiple: false,
                        "close-on-select": true,
                        "clear-on-select": false,
                        "preserve-search": true,
                        placeholder: unref(t)("select"),
                        label: "label",
                        "track-by": "id",
                        class: "w-full"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                      createVNode(_sfc_main$3, {
                        class: "mt-2",
                        message: unref(form).errors.user_id
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "flex flex-col items-start" }, [
                      createVNode(_sfc_main$2, { for: "quiz" }, {
                        default: withCtx(() => [
                          createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                          createTextVNode(" " + toDisplayString(unref(t)("quiz")), 1)
                        ]),
                        _: 1
                      }),
                      createVNode(unref(VueMultiselect), {
                        id: "quiz",
                        modelValue: selectedQuiz.value,
                        "onUpdate:modelValue": ($event) => selectedQuiz.value = $event,
                        options: quizOptions.value,
                        "options-limit": dynamicOptionsLimit(quizOptions.value),
                        multiple: false,
                        "close-on-select": true,
                        "clear-on-select": false,
                        "preserve-search": true,
                        placeholder: unref(t)("select"),
                        label: "label",
                        "track-by": "id",
                        class: "w-full"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                      createVNode(_sfc_main$3, {
                        class: "mt-2",
                        message: unref(form).errors.school_quiz_id
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40" }, [
                      createVNode("div", { class: "text-center text-md font-semibold opacity-80 mb-3" }, toDisplayString(unref(t)("context")), 1),
                      createVNode("div", { class: "grid grid-cols-1 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$2, { for: "lesson" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("lesson")), 1)
                            ]),
                            _: 1
                          }),
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
                          createVNode(_sfc_main$3, {
                            class: "mt-2",
                            message: unref(form).errors.school_lesson_id
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$2, { for: "module" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("module")) + " (" + toDisplayString(unref(t)("autoCorrect")) + ") ", 1)
                            ]),
                            _: 1
                          }),
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
                          createVNode(_sfc_main$3, {
                            class: "mt-2",
                            message: unref(form).errors.school_module_id
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$2, { for: "course" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("course")) + " (" + toDisplayString(unref(t)("autoCorrect")) + ") ", 1)
                            ]),
                            _: 1
                          }),
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
                          createVNode(_sfc_main$3, {
                            class: "mt-2",
                            message: unref(form).errors.school_course_id
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$2, { for: "enrollment" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("enrollment")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(unref(VueMultiselect), {
                            id: "enrollment",
                            modelValue: selectedEnrollment.value,
                            "onUpdate:modelValue": ($event) => selectedEnrollment.value = $event,
                            options: enrollmentOptions.value,
                            "options-limit": dynamicOptionsLimit(enrollmentOptions.value),
                            multiple: false,
                            "close-on-select": true,
                            "clear-on-select": false,
                            "preserve-search": true,
                            placeholder: unref(t)("select"),
                            label: "label",
                            "track-by": "id",
                            class: "w-full"
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                          createVNode(_sfc_main$3, {
                            class: "mt-2",
                            message: unref(form).errors.school_enrollment_id
                          }, null, 8, ["message"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-3 gap-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "duration_seconds" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("duration")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$4, {
                          id: "duration_seconds",
                          type: "number",
                          min: "0",
                          modelValue: unref(form).duration_seconds,
                          "onUpdate:modelValue": ($event) => unref(form).duration_seconds = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.duration_seconds
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "started_at" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("shortStarted")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$5, {
                          id: "started_at",
                          type: "datetime-local",
                          modelValue: unref(form).started_at,
                          "onUpdate:modelValue": ($event) => unref(form).started_at = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.started_at
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "finished_at" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("shortExpires")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$5, {
                          id: "finished_at",
                          type: "datetime-local",
                          modelValue: unref(form).finished_at,
                          "onUpdate:modelValue": ($event) => unref(form).finished_at = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.finished_at
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-4 gap-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "attempt_number" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("attemptNumber")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$4, {
                          id: "attempt_number",
                          type: "number",
                          min: "1",
                          modelValue: unref(form).attempt_number,
                          "onUpdate:modelValue": ($event) => unref(form).attempt_number = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.attempt_number
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "score" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("score")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$4, {
                          id: "score",
                          type: "number",
                          min: "0",
                          modelValue: unref(form).score,
                          "onUpdate:modelValue": ($event) => unref(form).score = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.score
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "max_score" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("maxScore")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$4, {
                          id: "max_score",
                          type: "number",
                          min: "0",
                          modelValue: unref(form).max_score,
                          "onUpdate:modelValue": ($event) => unref(form).max_score = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.max_score
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "status" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("status")), 1)
                          ]),
                          _: 1
                        }),
                        withDirectives(createVNode("select", {
                          id: "status",
                          "onUpdate:modelValue": ($event) => unref(form).status = $event,
                          class: "w-full py-0.5 font-semibold text-sm border border-slate-500 rounded-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-300 dark:bg-cyan-800 dark:text-slate-100"
                        }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(statusOptions.value, (option) => {
                            return openBlock(), createBlock("option", {
                              key: option.value,
                              value: option.value
                            }, toDisplayString(option.label), 9, ["value"]);
                          }), 128))
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).status]
                        ]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.status
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center gap-3" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.schoolQuizAttempts.index")
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
                      createVNode(_sfc_main$6, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolQuizAttempts/Create.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
