import { computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, withDirectives, Fragment, renderList, vModelSelect, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$6 } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$2 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$5 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$3 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$4 } from "./InputError-CLVdJ1nk.js";
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
    currentLocale: {
      type: String,
      default: ""
    },
    availableLocales: {
      type: Array,
      default: () => []
    },
    attempt: {
      type: Object,
      required: true
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
    }
  },
  setup(__props) {
    var _a, _b, _c, _d, _e, _f;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const normalizeDateTimeLocal = (value) => {
      if (!value) return "";
      const string = String(value);
      const match = string.match(/^(\d{4}-\d{2}-\d{2}T\d{2}:\d{2})/);
      return match ? match[1] : string;
    };
    const fromDatetimeLocal = (value) => {
      if (!value) return null;
      const date = new Date(value);
      return Number.isFinite(date.getTime()) ? date.toISOString() : null;
    };
    const normalizeToEmptyString = (value) => {
      return value === null || typeof value === "undefined" ? "" : value;
    };
    const toNumberOrNull = (value) => {
      if (value === "" || value === null || typeof value === "undefined") {
        return null;
      }
      const number = Number(value);
      return Number.isFinite(number) ? number : null;
    };
    const getTitle = (item) => {
      var _a2, _b2;
      return ((_a2 = item == null ? void 0 : item.translation) == null ? void 0 : _a2.title) || ((_b2 = item == null ? void 0 : item.translation) == null ? void 0 : _b2.name) || (item == null ? void 0 : item.slug) || null;
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
    const userLabel = computed(() => {
      var _a2, _b2;
      const user = (_a2 = props.attempt) == null ? void 0 : _a2.user;
      if (user == null ? void 0 : user.id) {
        return `[ID: ${user.id}] ${user.name || "—"}${user.email ? ` (${user.email})` : ""}`;
      }
      return ((_b2 = props.attempt) == null ? void 0 : _b2.user_id) ? `[ID: ${props.attempt.user_id}]` : "—";
    });
    const quizLabel = computed(() => {
      var _a2, _b2;
      const quiz = (_a2 = props.attempt) == null ? void 0 : _a2.quiz;
      if (quiz == null ? void 0 : quiz.id) {
        return `[ID: ${quiz.id}] ${getTitle(quiz) || "—"}`;
      }
      return ((_b2 = props.attempt) == null ? void 0 : _b2.school_quiz_id) ? `[ID: ${props.attempt.school_quiz_id}]` : "—";
    });
    const enrollmentLabel = computed(() => {
      var _a2, _b2, _c2;
      const enrollment = (_a2 = props.attempt) == null ? void 0 : _a2.enrollment;
      if (enrollment == null ? void 0 : enrollment.id) {
        const userPart = ((_b2 = enrollment.user) == null ? void 0 : _b2.name) ? ` — ${enrollment.user.name}` : "";
        const courseTitle = getTitle(enrollment.course);
        const coursePart = courseTitle ? ` / ${courseTitle}` : "";
        return `[ID: ${enrollment.id}]${userPart}${coursePart}`;
      }
      return ((_c2 = props.attempt) == null ? void 0 : _c2.school_enrollment_id) ? `[ID: ${props.attempt.school_enrollment_id}]` : "—";
    });
    const courseLabel = computed(() => {
      var _a2, _b2;
      const course = (_a2 = props.attempt) == null ? void 0 : _a2.course;
      if (course == null ? void 0 : course.id) {
        return `[ID: ${course.id}] ${getTitle(course) || "—"}`;
      }
      return ((_b2 = props.attempt) == null ? void 0 : _b2.school_course_id) ? `[ID: ${props.attempt.school_course_id}]` : "—";
    });
    const moduleLabel = computed(() => {
      var _a2, _b2;
      const module = (_a2 = props.attempt) == null ? void 0 : _a2.module;
      if (module == null ? void 0 : module.id) {
        return `[ID: ${module.id}] ${getTitle(module) || "—"}`;
      }
      return ((_b2 = props.attempt) == null ? void 0 : _b2.school_module_id) ? `[ID: ${props.attempt.school_module_id}]` : "—";
    });
    const lessonLabel = computed(() => {
      var _a2, _b2;
      const lesson = (_a2 = props.attempt) == null ? void 0 : _a2.lesson;
      if (lesson == null ? void 0 : lesson.id) {
        return `[ID: ${lesson.id}] ${getTitle(lesson) || "—"}`;
      }
      return ((_b2 = props.attempt) == null ? void 0 : _b2.school_lesson_id) ? `[ID: ${props.attempt.school_lesson_id}]` : "—";
    });
    const ipLabel = computed(
      () => {
        var _a2;
        return ((_a2 = props.attempt) == null ? void 0 : _a2.ip_address) || "—";
      }
    );
    const userAgentLabel = computed(
      () => {
        var _a2;
        return ((_a2 = props.attempt) == null ? void 0 : _a2.user_agent) || "—";
      }
    );
    const percentLabel = computed(() => {
      var _a2, _b2;
      if (((_a2 = props.attempt) == null ? void 0 : _a2.percent) === null || typeof ((_b2 = props.attempt) == null ? void 0 : _b2.percent) === "undefined") {
        return "—";
      }
      return `${props.attempt.percent}%`;
    });
    const form = useForm({
      _method: "PUT",
      status: ((_a = props.attempt) == null ? void 0 : _a.status) ?? "in_progress",
      score: normalizeToEmptyString(
        (_b = props.attempt) == null ? void 0 : _b.score
      ),
      max_score: normalizeToEmptyString(
        (_c = props.attempt) == null ? void 0 : _c.max_score
      ),
      started_at: normalizeDateTimeLocal(
        (_d = props.attempt) == null ? void 0 : _d.started_at
      ),
      finished_at: normalizeDateTimeLocal(
        (_e = props.attempt) == null ? void 0 : _e.finished_at
      ),
      duration_seconds: normalizeToEmptyString(
        (_f = props.attempt) == null ? void 0 : _f.duration_seconds
      )
    });
    const submitForm = () => {
      form.transform((data) => {
        const payload = {
          _method: "PUT",
          status: data.status,
          score: toNumberOrNull(
            data.score
          ),
          max_score: toNumberOrNull(
            data.max_score
          ),
          started_at: fromDatetimeLocal(
            data.started_at
          ),
          finished_at: fromDatetimeLocal(
            data.finished_at
          ),
          duration_seconds: toNumberOrNull(
            data.duration_seconds
          )
        };
        Object.keys(payload).forEach((key) => {
          if (payload[key] === null) {
            delete payload[key];
          }
        });
        return payload;
      });
      form.post(
        route("admin.schoolQuizAttempts.update", {
          schoolQuizAttempt: props.attempt.id
        }),
        {
          preserveScroll: true,
          onSuccess: () => {
            toast.success(
              "Попытка квиза успешно обновлена."
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || "Проверьте правильность заполнения полей."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editQuizAttempt")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editQuizAttempt"))} [ID: ${ssrInterpolate(__props.attempt.id)}] `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editQuizAttempt")) + " [ID: " + toDisplayString(__props.attempt.id) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editQuizAttempt")) + " [ID: " + toDisplayString(__props.attempt.id) + "] ", 1)
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
            _push2(`</div><div class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40"${_scopeId}><div class="text-left text-md font-semibold opacity-80 mb-3"${_scopeId}>${ssrInterpolate(unref(t)("context"))}</div><div class="grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm"${_scopeId}><div class="space-y-3"${_scopeId}><div${_scopeId}><span class="font-semibold text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("user"))}: </span><span class="text-slate-800 dark:text-slate-200 opacity-80"${_scopeId}>${ssrInterpolate(userLabel.value)}</span></div><div${_scopeId}><span class="font-semibold text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("quiz"))}: </span><span class="text-slate-800 dark:text-slate-200 opacity-80"${_scopeId}>${ssrInterpolate(quizLabel.value)}</span></div><div${_scopeId}><span class="font-semibold text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("enrollment"))}: </span><span class="text-slate-800 dark:text-slate-200 opacity-80"${_scopeId}>${ssrInterpolate(enrollmentLabel.value)}</span></div><div${_scopeId}><span class="font-semibold text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("attemptNumber"))}: </span><span class="text-slate-800 dark:text-slate-200 opacity-80"${_scopeId}>${ssrInterpolate(__props.attempt.attempt_number ?? "—")}</span></div><div${_scopeId}><span class="font-semibold text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("percent"))}: </span><span class="text-slate-800 dark:text-slate-200 opacity-80"${_scopeId}>${ssrInterpolate(percentLabel.value)}</span></div></div><div class="space-y-3"${_scopeId}><div${_scopeId}><span class="font-semibold text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("course"))}: </span><span class="text-slate-800 dark:text-slate-200 opacity-80"${_scopeId}>${ssrInterpolate(courseLabel.value)}</span></div><div${_scopeId}><span class="font-semibold text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("module"))}: </span><span class="text-slate-800 dark:text-slate-200 opacity-80"${_scopeId}>${ssrInterpolate(moduleLabel.value)}</span></div><div${_scopeId}><span class="font-semibold text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("lesson"))}: </span><span class="text-slate-800 dark:text-slate-200 opacity-80"${_scopeId}>${ssrInterpolate(lessonLabel.value)}</span></div><div${_scopeId}><span class="font-semibold text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("ipAddress"))}: </span><span class="text-slate-800 dark:text-slate-200 opacity-80"${_scopeId}>${ssrInterpolate(ipLabel.value)}</span></div><div${_scopeId}><span class="font-semibold text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("userAgent"))}: </span><div class="mt-1 text-slate-800 dark:text-slate-200 opacity-80 break-words whitespace-pre-wrap"${_scopeId}>${ssrInterpolate(userAgentLabel.value)}</div></div></div></div></div><form class="p-3 w-full space-y-4"${_scopeId}><div class="grid grid-cols-1 lg:grid-cols-3 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
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
            _push2(ssrRenderComponent(_sfc_main$3, {
              id: "duration_seconds",
              type: "number",
              min: "0",
              modelValue: unref(form).duration_seconds,
              "onUpdate:modelValue": ($event) => unref(form).duration_seconds = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
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
            _push2(ssrRenderComponent(_sfc_main$4, {
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
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.finished_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 lg:grid-cols-3 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
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
            _push2(ssrRenderComponent(_sfc_main$3, {
              id: "score",
              type: "number",
              min: "0",
              modelValue: unref(form).score,
              "onUpdate:modelValue": ($event) => unref(form).score = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
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
            _push2(ssrRenderComponent(_sfc_main$3, {
              id: "max_score",
              type: "number",
              min: "0",
              modelValue: unref(form).max_score,
              "onUpdate:modelValue": ($event) => unref(form).max_score = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.max_score
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "status" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("status"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("status")), 1)
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
            _push2(ssrRenderComponent(_sfc_main$4, {
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
                  createVNode("div", { class: "mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40" }, [
                    createVNode("div", { class: "text-left text-md font-semibold opacity-80 mb-3" }, toDisplayString(unref(t)("context")), 1),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 text-sm" }, [
                      createVNode("div", { class: "space-y-3" }, [
                        createVNode("div", null, [
                          createVNode("span", { class: "font-semibold text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("user")) + ": ", 1),
                          createVNode("span", { class: "text-slate-800 dark:text-slate-200 opacity-80" }, toDisplayString(userLabel.value), 1)
                        ]),
                        createVNode("div", null, [
                          createVNode("span", { class: "font-semibold text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("quiz")) + ": ", 1),
                          createVNode("span", { class: "text-slate-800 dark:text-slate-200 opacity-80" }, toDisplayString(quizLabel.value), 1)
                        ]),
                        createVNode("div", null, [
                          createVNode("span", { class: "font-semibold text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("enrollment")) + ": ", 1),
                          createVNode("span", { class: "text-slate-800 dark:text-slate-200 opacity-80" }, toDisplayString(enrollmentLabel.value), 1)
                        ]),
                        createVNode("div", null, [
                          createVNode("span", { class: "font-semibold text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("attemptNumber")) + ": ", 1),
                          createVNode("span", { class: "text-slate-800 dark:text-slate-200 opacity-80" }, toDisplayString(__props.attempt.attempt_number ?? "—"), 1)
                        ]),
                        createVNode("div", null, [
                          createVNode("span", { class: "font-semibold text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("percent")) + ": ", 1),
                          createVNode("span", { class: "text-slate-800 dark:text-slate-200 opacity-80" }, toDisplayString(percentLabel.value), 1)
                        ])
                      ]),
                      createVNode("div", { class: "space-y-3" }, [
                        createVNode("div", null, [
                          createVNode("span", { class: "font-semibold text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("course")) + ": ", 1),
                          createVNode("span", { class: "text-slate-800 dark:text-slate-200 opacity-80" }, toDisplayString(courseLabel.value), 1)
                        ]),
                        createVNode("div", null, [
                          createVNode("span", { class: "font-semibold text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("module")) + ": ", 1),
                          createVNode("span", { class: "text-slate-800 dark:text-slate-200 opacity-80" }, toDisplayString(moduleLabel.value), 1)
                        ]),
                        createVNode("div", null, [
                          createVNode("span", { class: "font-semibold text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("lesson")) + ": ", 1),
                          createVNode("span", { class: "text-slate-800 dark:text-slate-200 opacity-80" }, toDisplayString(lessonLabel.value), 1)
                        ]),
                        createVNode("div", null, [
                          createVNode("span", { class: "font-semibold text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("ipAddress")) + ": ", 1),
                          createVNode("span", { class: "text-slate-800 dark:text-slate-200 opacity-80" }, toDisplayString(ipLabel.value), 1)
                        ]),
                        createVNode("div", null, [
                          createVNode("span", { class: "font-semibold text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("userAgent")) + ": ", 1),
                          createVNode("div", { class: "mt-1 text-slate-800 dark:text-slate-200 opacity-80 break-words whitespace-pre-wrap" }, toDisplayString(userAgentLabel.value), 1)
                        ])
                      ])
                    ])
                  ]),
                  createVNode("form", {
                    onSubmit: withModifiers(submitForm, ["prevent"]),
                    class: "p-3 w-full space-y-4"
                  }, [
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-3 gap-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "duration_seconds" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("duration")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$3, {
                          id: "duration_seconds",
                          type: "number",
                          min: "0",
                          modelValue: unref(form).duration_seconds,
                          "onUpdate:modelValue": ($event) => unref(form).duration_seconds = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
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
                        createVNode(_sfc_main$4, {
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
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.finished_at
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-3 gap-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "score" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("score")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$3, {
                          id: "score",
                          type: "number",
                          min: "0",
                          modelValue: unref(form).score,
                          "onUpdate:modelValue": ($event) => unref(form).score = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
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
                        createVNode(_sfc_main$3, {
                          id: "max_score",
                          type: "number",
                          min: "0",
                          modelValue: unref(form).max_score,
                          "onUpdate:modelValue": ($event) => unref(form).max_score = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.max_score
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "status" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("status")), 1)
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
                        createVNode(_sfc_main$4, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolQuizAttempts/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
