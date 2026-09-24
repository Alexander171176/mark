import { computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, createCommentVNode, withModifiers, Fragment, renderList, withDirectives, vModelCheckbox, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderClass, ssrRenderList, ssrIncludeBooleanAttr, ssrLooseContain } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$6 } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$2 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$4 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$3 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$5 } from "./TinyEditor-D1VhnqFH.js";
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
    item: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    var _a, _b, _c, _d;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const stripHtml = (html = "") => {
      return (html || "").replace(/<\/p>/gi, " ").replace(/<br\s*\/?>/gi, " ").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/&amp;/gi, "&").replace(/&quot;/gi, '"').replace(/&#039;/gi, "'").replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/\s+/g, " ").trim();
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
    const formatDateTime = (value) => {
      if (!value) return "—";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return String(value);
      }
      return date.toLocaleString("ru-RU");
    };
    const questionTypeLabel = (type) => {
      const map = {
        single_choice: t("questionTypeSingleChoice"),
        multiple_choice: t("questionTypeMultipleChoice"),
        true_false: t("questionTypeTrueFalse"),
        open_text: t("questionTypeOpenText")
      };
      return map[type] || type || "—";
    };
    const attemptStatusLabel = (status) => {
      const map = {
        in_progress: t("setStatusInProgress"),
        completed: t("setStatusCompleted"),
        graded: t("setStatusGraded")
      };
      return map[status] || status || "—";
    };
    const attemptStatusClass = (status) => {
      if (status === "graded") {
        return "text-emerald-700 dark:text-emerald-200";
      }
      if (status === "completed") {
        return "text-sky-800 dark:text-sky-200";
      }
      if (status === "in_progress") {
        return "text-amber-800 dark:text-amber-200";
      }
      return "text-slate-500 dark:text-slate-300";
    };
    const attempt = computed(
      () => {
        var _a2;
        return ((_a2 = props.item) == null ? void 0 : _a2.attempt) || null;
      }
    );
    const student = computed(
      () => {
        var _a2, _b2;
        return ((_b2 = (_a2 = props.item) == null ? void 0 : _a2.attempt) == null ? void 0 : _b2.user) || null;
      }
    );
    const quiz = computed(
      () => {
        var _a2, _b2;
        return ((_b2 = (_a2 = props.item) == null ? void 0 : _a2.attempt) == null ? void 0 : _b2.quiz) || null;
      }
    );
    const quizTitle = computed(
      () => {
        var _a2, _b2, _c2;
        return ((_b2 = (_a2 = quiz.value) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || ((_c2 = quiz.value) == null ? void 0 : _c2.slug) || "—";
      }
    );
    const question = computed(
      () => {
        var _a2;
        return ((_a2 = props.item) == null ? void 0 : _a2.question) || null;
      }
    );
    const questionType = computed(
      () => {
        var _a2;
        return ((_a2 = question.value) == null ? void 0 : _a2.question_type) || null;
      }
    );
    const questionText = computed(
      () => {
        var _a2, _b2;
        return ((_b2 = (_a2 = question.value) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.question_text) || "";
      }
    );
    const questionExplanation = computed(
      () => {
        var _a2, _b2;
        return ((_b2 = (_a2 = question.value) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.explanation) || "";
      }
    );
    const questionPoints = computed(
      () => {
        var _a2;
        return ((_a2 = question.value) == null ? void 0 : _a2.points) ?? null;
      }
    );
    const statusLabel = computed(
      () => {
        var _a2;
        return attemptStatusLabel(
          (_a2 = attempt.value) == null ? void 0 : _a2.status
        );
      }
    );
    const statusClass = computed(
      () => {
        var _a2;
        return attemptStatusClass(
          (_a2 = attempt.value) == null ? void 0 : _a2.status
        );
      }
    );
    const questionTypeLabelValue = computed(
      () => questionTypeLabel(
        questionType.value
      )
    );
    const maxScoreReadonly = computed(() => {
      var _a2;
      const points = Number(
        questionPoints.value
      );
      if (Number.isFinite(points)) {
        return points;
      }
      const maxScore = Number(
        (_a2 = props.item) == null ? void 0 : _a2.max_score
      );
      return Number.isFinite(maxScore) ? maxScore : null;
    });
    const isOpenText = computed(
      () => questionType.value === "open_text"
    );
    const isMultipleChoice = computed(
      () => questionType.value === "multiple_choice"
    );
    const isSingleChoice = computed(
      () => [
        "single_choice",
        "true_false"
      ].includes(
        questionType.value
      )
    );
    const selectedAnswerSingle = computed(
      () => {
        var _a2;
        return ((_a2 = props.item) == null ? void 0 : _a2.selected_answer) || null;
      }
    );
    const selectedAnswersMultiple = computed(() => {
      var _a2;
      return Array.isArray(
        (_a2 = props.item) == null ? void 0 : _a2.selected_answers
      ) ? props.item.selected_answers : [];
    });
    const selectedAnswerIds = computed(() => {
      var _a2;
      return Array.isArray(
        (_a2 = props.item) == null ? void 0 : _a2.selected_answer_ids
      ) ? props.item.selected_answer_ids : [];
    });
    const freeTextAnswer = computed(
      () => {
        var _a2;
        return ((_a2 = props.item) == null ? void 0 : _a2.free_text_answer) || "";
      }
    );
    const form = useForm({
      _method: "PUT",
      is_correct: Boolean(
        (_a = props.item) == null ? void 0 : _a.is_correct
      ),
      score: normalizeToEmptyString(
        (_b = props.item) == null ? void 0 : _b.score
      ),
      max_score: normalizeToEmptyString(
        (_c = props.item) == null ? void 0 : _c.max_score
      ),
      reviewer_comment: ((_d = props.item) == null ? void 0 : _d.reviewer_comment) || ""
    });
    const setScoreMax = () => {
      if (maxScoreReadonly.value === null) {
        return;
      }
      form.score = String(
        maxScoreReadonly.value
      );
      toast.info(
        `${t("score")}: ${t("setMax")}`
      );
    };
    const setScoreZero = () => {
      form.score = "0";
      toast.info(
        `${t("score")}: 0`
      );
    };
    const submitForm = () => {
      form.transform((data) => {
        const payload = {
          _method: "PUT",
          score: toNumberOrNull(
            data.score
          ),
          max_score: toNumberOrNull(
            data.max_score
          ) ?? maxScoreReadonly.value,
          reviewer_comment: (data.reviewer_comment || "").toString().trim() || null
        };
        if (isOpenText.value) {
          payload.is_correct = Boolean(
            data.is_correct
          );
        }
        Object.keys(payload).forEach((key) => {
          if (payload[key] === null) {
            delete payload[key];
          }
        });
        return payload;
      });
      form.post(
        route(
          "admin.schoolQuizAttemptItems.update",
          {
            schoolQuizAttemptItem: props.item.id
          }
        ),
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Проверка ответа успешно обновлена."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка обновления ответа попытки:",
              errors
            );
            const firstKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || "Проверьте поля формы."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editQuizAttemptItem")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editQuizAttemptItem"))} [ID: ${ssrInterpolate(__props.item.id)}] `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editQuizAttemptItem")) + " [ID: " + toDisplayString(__props.item.id) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editQuizAttemptItem")) + " [ID: " + toDisplayString(__props.item.id) + "] ", 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a2, _b2, _c2, _d2, _e, _f, _g, _h, _i, _j, _k, _l, _m, _n, _o, _p;
          if (_push2) {
            _push2(`<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto"${_scopeId}><div class="px-4 pt-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolQuizAttemptItems.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z" })
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
            _push2(`</div><form class="pt-3 w-full"${_scopeId}><div class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40"${_scopeId}><div class="text-center text-md font-semibold opacity-80 text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("context"))}</div><div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-3 text-sm"${_scopeId}><div class="p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20"${_scopeId}><div class="font-semibold opacity-80 text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("student"))}</div><div class="mt-1"${_scopeId}><div class="font-semibold text-slate-700 dark:text-slate-300"${_scopeId}> ID: ${ssrInterpolate(((_a2 = attempt.value) == null ? void 0 : _a2.user_id) ?? ((_b2 = student.value) == null ? void 0 : _b2.id) ?? "—")}</div>`);
            if (student.value) {
              _push2(`<!--[--><div${_scopeId}><span class="font-semibold text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("name"))}: </span><span class="font-semibold text-indigo-700 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(student.value.name || "—")}</span></div><div${_scopeId}><span class="font-semibold text-slate-700 dark:text-slate-300"${_scopeId}> Email: </span><span class="font-semibold text-indigo-700 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(student.value.email || "—")}</span></div><!--]-->`);
            } else {
              _push2(`<div class="p-5 text-center text-slate-700 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            }
            _push2(`</div></div><div class="p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20"${_scopeId}><div class="font-semibold opacity-80 text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("quiz"))}</div><div class="mt-1"${_scopeId}><div class="font-semibold text-slate-700 dark:text-slate-300"${_scopeId}> ID: ${ssrInterpolate(((_c2 = quiz.value) == null ? void 0 : _c2.id) ?? ((_d2 = attempt.value) == null ? void 0 : _d2.school_quiz_id) ?? "—")}</div><div${_scopeId}><span class="font-semibold text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("title"))}: </span><span class="font-semibold text-indigo-700 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(quizTitle.value)}</span></div></div></div><div class="p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20"${_scopeId}><div class="font-semibold opacity-80 text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("attempt"))}</div><div class="mt-1"${_scopeId}><div class="flex flex-wrap justify-between items-center"${_scopeId}><div class="font-semibold text-slate-700 dark:text-slate-300"${_scopeId}> Item ID: ${ssrInterpolate(__props.item.id)}</div><div class="font-semibold text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("attempt"))} ID: ${ssrInterpolate(((_e = attempt.value) == null ? void 0 : _e.id) ?? __props.item.school_quiz_attempt_id ?? "—")}</div></div><div class="font-semibold text-indigo-700 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(unref(t)("attemptNumber"))} ${ssrInterpolate(((_f = attempt.value) == null ? void 0 : _f.attempt_number) ?? "—")}</div><div${_scopeId}><span class="font-semibold text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("status"))}: </span><span class="${ssrRenderClass([statusClass.value, "font-semibold"])}"${_scopeId}>${ssrInterpolate(statusLabel.value)}</span></div><div class="flex flex-col items-start mt-1 text-xs opacity-75"${_scopeId}><div${_scopeId}><span class="font-semibold text-slate-900 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("createdAt"))}: </span><span class="font-semibold text-blue-700 dark:text-blue-300"${_scopeId}>${ssrInterpolate(formatDateTime(__props.item.created_at))}</span></div><div${_scopeId}><span class="font-semibold text-slate-900 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("updatedAt"))}: </span><span class="font-semibold text-blue-700 dark:text-blue-300"${_scopeId}>${ssrInterpolate(formatDateTime(__props.item.updated_at))}</span></div></div></div></div></div><div class="mt-4 p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20"${_scopeId}><div class="text-center font-semibold opacity-80 text-gray-900 dark:text-gray-100 text-md"${_scopeId}>${ssrInterpolate(unref(t)("quizQuestion"))}</div><div class="text-sm font-semibold text-slate-700 dark:text-slate-300"${_scopeId}> ID: ${ssrInterpolate(((_g = question.value) == null ? void 0 : _g.id) ?? __props.item.school_quiz_question_id ?? "—")}</div><div class="mt-2 whitespace-pre-wrap leading-relaxed text-sm font-semibold text-amber-800 dark:text-amber-200"${_scopeId}>${ssrInterpolate(stripHtml(questionText.value) || "—")}</div>`);
            if (questionExplanation.value) {
              _push2(`<div class="mt-2 text-xs text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(stripHtml(questionExplanation.value))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mt-2 text-sm flex flex-row items-center justify-start"${_scopeId}><div${_scopeId}><span class="font-semibold text-slate-900 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("type"))}: </span><span class="font-semibold text-teal-800 dark:text-teal-200"${_scopeId}>${ssrInterpolate(questionTypeLabelValue.value)}</span></div><div class="mx-2"${_scopeId}>|</div><div${_scopeId}><span class="font-semibold text-slate-900 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("maxScore"))}: </span><span class="font-semibold text-teal-800 dark:text-teal-200"${_scopeId}>${ssrInterpolate(maxScoreReadonly.value ?? __props.item.max_score ?? "—")}</span></div></div></div></div><div class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40 text-md"${_scopeId}><div class="text-center text-md font-semibold opacity-80 text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("studentAnswer"))}</div>`);
            if (isSingleChoice.value) {
              _push2(`<div class="mt-3 p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20 text-sm"${_scopeId}><div class="font-semibold opacity-80 text-amber-800 dark:text-amber-200"${_scopeId}>${ssrInterpolate(unref(t)("selectedOneAnswer"))}</div><div class="mt-1 flex flex-row items-center justify-start"${_scopeId}>`);
              if (selectedAnswerSingle.value) {
                _push2(`<!--[--><div class="font-semibold text-slate-700 dark:text-slate-300"${_scopeId}> ID: ${ssrInterpolate(selectedAnswerSingle.value.id)}</div><div class="mx-2"${_scopeId}>|</div><div${_scopeId}><span class="font-semibold text-slate-900 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("text"))}: </span><span class="font-semibold text-teal-800 dark:text-teal-200"${_scopeId}>${ssrInterpolate(stripHtml(
                  (_h = selectedAnswerSingle.value.translation) == null ? void 0 : _h.text
                ) || "—")}</span></div>`);
                if (selectedAnswerSingle.value.is_correct) {
                  _push2(`<span class="ml-2"${_scopeId}> ✅ </span>`);
                } else {
                  _push2(`<span class="ml-2"${_scopeId}> ❌ </span>`);
                }
                _push2(`<!--]-->`);
              } else {
                _push2(`<!--[--> — <!--]-->`);
              }
              _push2(`</div></div>`);
            } else if (isMultipleChoice.value) {
              _push2(`<div class="mt-3 p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20 text-sm"${_scopeId}><div class="font-semibold opacity-80 text-amber-800 dark:text-amber-200"${_scopeId}>${ssrInterpolate(unref(t)("selectedSeveralAnswers"))}</div>`);
              if (selectedAnswersMultiple.value.length) {
                _push2(`<ul class="mt-2 list-disc pl-5"${_scopeId}><!--[-->`);
                ssrRenderList(selectedAnswersMultiple.value, (answer) => {
                  var _a3;
                  _push2(`<li class="flex flex-row items-center justify-start"${_scopeId}><span class="font-semibold text-slate-700 dark:text-slate-300"${_scopeId}> ID: ${ssrInterpolate(answer.id)} — ${ssrInterpolate(stripHtml(
                    (_a3 = answer.translation) == null ? void 0 : _a3.text
                  ) || "—")}</span>`);
                  if (answer.is_correct) {
                    _push2(`<span class="ml-2"${_scopeId}> ✅ </span>`);
                  } else {
                    _push2(`<span class="ml-2"${_scopeId}> ❌ </span>`);
                  }
                  _push2(`</li>`);
                });
                _push2(`<!--]--></ul>`);
              } else {
                _push2(`<div class="mt-2 text-sm opacity-80 font-semibold text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(selectedAnswerIds.value.length ? selectedAnswerIds.value.join(", ") : unref(t)("noData"))}</div>`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<div class="mt-3 p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20 text-sm"${_scopeId}><div class="font-semibold opacity-80 text-slate-800 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("selectedTextAnswer"))}</div><div class="mt-2 p-3 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/40 rounded-sm"${_scopeId}><div class="whitespace-pre-wrap font-semibold text-violet-800 dark:text-violet-200"${_scopeId}>${ssrInterpolate(stripHtml(freeTextAnswer.value) || "—")}</div></div></div>`);
            }
            _push2(`</div><div class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40"${_scopeId}><div class="text-center text-md font-semibold opacity-80 text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("checkInstructor"))}</div><div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              for: "is_correct",
              class: "mb-1"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("isCorrect"))} `);
                  if (!isOpenText.value) {
                    _push3(`<span class="ml-2 text-xs opacity-70"${_scopeId2}> (${ssrInterpolate(unref(t)("auto"))}) </span>`);
                  } else {
                    _push3(`<!---->`);
                  }
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("isCorrect")) + " ", 1),
                    !isOpenText.value ? (openBlock(), createBlock("span", {
                      key: 0,
                      class: "ml-2 text-xs opacity-70"
                    }, " (" + toDisplayString(unref(t)("auto")) + ") ", 1)) : createCommentVNode("", true)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<label class="flex items-center gap-2 text-sm"${_scopeId}><input id="is_correct" type="checkbox"${ssrIncludeBooleanAttr(Array.isArray(unref(form).is_correct) ? ssrLooseContain(unref(form).is_correct, null) : unref(form).is_correct) ? " checked" : ""} class="rounded border-slate-400"${ssrIncludeBooleanAttr(!isOpenText.value) ? " disabled" : ""}${_scopeId}><span class="text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(form).is_correct ? unref(t)("yes") : unref(t)("no"))}</span></label>`);
            if (!isOpenText.value) {
              _push2(`<div class="mt-1 text-xs opacity-70 text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("auto"))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.is_correct
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              for: "score",
              class: "mb-1"
            }, {
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
              max: maxScoreReadonly.value ?? void 0,
              modelValue: unref(form).score,
              "onUpdate:modelValue": ($event) => unref(form).score = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(`<div class="flex gap-2 mt-2"${_scopeId}><button type="button" class="text-xs px-2 py-1 border border-slate-400 rounded-sm hover:bg-slate-100 dark:hover:bg-slate-700 text-gray-900 dark:text-gray-100 font-semibold"${ssrIncludeBooleanAttr(maxScoreReadonly.value === null) ? " disabled" : ""}${_scopeId}>${ssrInterpolate(unref(t)("setMax"))}</button><button type="button" class="text-xs px-2 py-1 border border-slate-400 rounded-sm hover:bg-slate-100 dark:hover:bg-slate-700 text-gray-900 dark:text-gray-100 font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("setZero"))}</button></div>`);
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.score
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { class: "mb-1" }, {
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
            _push2(`<div class="w-full py-0.5 px-2 text-sm border border-slate-400 rounded-sm bg-slate-100 dark:bg-slate-800 font-semibold text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(maxScoreReadonly.value ?? __props.item.max_score ?? "—")}</div>`);
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.max_score
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mt-4 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              for: "reviewer_comment",
              class: "mb-1"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("reviewerComment"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("reviewerComment")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "reviewer_comment",
              modelValue: unref(form).reviewer_comment,
              "onUpdate:modelValue": ($event) => unref(form).reviewer_comment = $event,
              height: 260
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.reviewer_comment
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.server
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="flex items-center justify-center gap-3 pb-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolQuizAttemptItems.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z" })
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
              class: { "opacity-25": unref(form).processing },
              disabled: unref(form).processing
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("save") || unref(t)("update"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("save") || unref(t)("update")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></form></div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "px-4 pt-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-2" }, [
                    createVNode(_sfc_main$1, {
                      href: _ctx.route("admin.schoolQuizAttemptItems.index")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z" })
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
                    class: "pt-3 w-full"
                  }, [
                    createVNode("div", { class: "mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40" }, [
                      createVNode("div", { class: "text-center text-md font-semibold opacity-80 text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("context")), 1),
                      createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-3 gap-4 mt-3 text-sm" }, [
                        createVNode("div", { class: "p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20" }, [
                          createVNode("div", { class: "font-semibold opacity-80 text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("student")), 1),
                          createVNode("div", { class: "mt-1" }, [
                            createVNode("div", { class: "font-semibold text-slate-700 dark:text-slate-300" }, " ID: " + toDisplayString(((_i = attempt.value) == null ? void 0 : _i.user_id) ?? ((_j = student.value) == null ? void 0 : _j.id) ?? "—"), 1),
                            student.value ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                              createVNode("div", null, [
                                createVNode("span", { class: "font-semibold text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("name")) + ": ", 1),
                                createVNode("span", { class: "font-semibold text-indigo-700 dark:text-indigo-300" }, toDisplayString(student.value.name || "—"), 1)
                              ]),
                              createVNode("div", null, [
                                createVNode("span", { class: "font-semibold text-slate-700 dark:text-slate-300" }, " Email: "),
                                createVNode("span", { class: "font-semibold text-indigo-700 dark:text-indigo-300" }, toDisplayString(student.value.email || "—"), 1)
                              ])
                            ], 64)) : (openBlock(), createBlock("div", {
                              key: 1,
                              class: "p-5 text-center text-slate-700 dark:text-slate-100"
                            }, toDisplayString(unref(t)("noData")), 1))
                          ])
                        ]),
                        createVNode("div", { class: "p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20" }, [
                          createVNode("div", { class: "font-semibold opacity-80 text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("quiz")), 1),
                          createVNode("div", { class: "mt-1" }, [
                            createVNode("div", { class: "font-semibold text-slate-700 dark:text-slate-300" }, " ID: " + toDisplayString(((_k = quiz.value) == null ? void 0 : _k.id) ?? ((_l = attempt.value) == null ? void 0 : _l.school_quiz_id) ?? "—"), 1),
                            createVNode("div", null, [
                              createVNode("span", { class: "font-semibold text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("title")) + ": ", 1),
                              createVNode("span", { class: "font-semibold text-indigo-700 dark:text-indigo-300" }, toDisplayString(quizTitle.value), 1)
                            ])
                          ])
                        ]),
                        createVNode("div", { class: "p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20" }, [
                          createVNode("div", { class: "font-semibold opacity-80 text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("attempt")), 1),
                          createVNode("div", { class: "mt-1" }, [
                            createVNode("div", { class: "flex flex-wrap justify-between items-center" }, [
                              createVNode("div", { class: "font-semibold text-slate-700 dark:text-slate-300" }, " Item ID: " + toDisplayString(__props.item.id), 1),
                              createVNode("div", { class: "font-semibold text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("attempt")) + " ID: " + toDisplayString(((_m = attempt.value) == null ? void 0 : _m.id) ?? __props.item.school_quiz_attempt_id ?? "—"), 1)
                            ]),
                            createVNode("div", { class: "font-semibold text-indigo-700 dark:text-indigo-300" }, toDisplayString(unref(t)("attemptNumber")) + " " + toDisplayString(((_n = attempt.value) == null ? void 0 : _n.attempt_number) ?? "—"), 1),
                            createVNode("div", null, [
                              createVNode("span", { class: "font-semibold text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("status")) + ": ", 1),
                              createVNode("span", {
                                class: ["font-semibold", statusClass.value]
                              }, toDisplayString(statusLabel.value), 3)
                            ]),
                            createVNode("div", { class: "flex flex-col items-start mt-1 text-xs opacity-75" }, [
                              createVNode("div", null, [
                                createVNode("span", { class: "font-semibold text-slate-900 dark:text-slate-100" }, toDisplayString(unref(t)("createdAt")) + ": ", 1),
                                createVNode("span", { class: "font-semibold text-blue-700 dark:text-blue-300" }, toDisplayString(formatDateTime(__props.item.created_at)), 1)
                              ]),
                              createVNode("div", null, [
                                createVNode("span", { class: "font-semibold text-slate-900 dark:text-slate-100" }, toDisplayString(unref(t)("updatedAt")) + ": ", 1),
                                createVNode("span", { class: "font-semibold text-blue-700 dark:text-blue-300" }, toDisplayString(formatDateTime(__props.item.updated_at)), 1)
                              ])
                            ])
                          ])
                        ])
                      ]),
                      createVNode("div", { class: "mt-4 p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20" }, [
                        createVNode("div", { class: "text-center font-semibold opacity-80 text-gray-900 dark:text-gray-100 text-md" }, toDisplayString(unref(t)("quizQuestion")), 1),
                        createVNode("div", { class: "text-sm font-semibold text-slate-700 dark:text-slate-300" }, " ID: " + toDisplayString(((_o = question.value) == null ? void 0 : _o.id) ?? __props.item.school_quiz_question_id ?? "—"), 1),
                        createVNode("div", { class: "mt-2 whitespace-pre-wrap leading-relaxed text-sm font-semibold text-amber-800 dark:text-amber-200" }, toDisplayString(stripHtml(questionText.value) || "—"), 1),
                        questionExplanation.value ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "mt-2 text-xs text-slate-600 dark:text-slate-300"
                        }, toDisplayString(stripHtml(questionExplanation.value)), 1)) : createCommentVNode("", true),
                        createVNode("div", { class: "mt-2 text-sm flex flex-row items-center justify-start" }, [
                          createVNode("div", null, [
                            createVNode("span", { class: "font-semibold text-slate-900 dark:text-slate-100" }, toDisplayString(unref(t)("type")) + ": ", 1),
                            createVNode("span", { class: "font-semibold text-teal-800 dark:text-teal-200" }, toDisplayString(questionTypeLabelValue.value), 1)
                          ]),
                          createVNode("div", { class: "mx-2" }, "|"),
                          createVNode("div", null, [
                            createVNode("span", { class: "font-semibold text-slate-900 dark:text-slate-100" }, toDisplayString(unref(t)("maxScore")) + ": ", 1),
                            createVNode("span", { class: "font-semibold text-teal-800 dark:text-teal-200" }, toDisplayString(maxScoreReadonly.value ?? __props.item.max_score ?? "—"), 1)
                          ])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40 text-md" }, [
                      createVNode("div", { class: "text-center text-md font-semibold opacity-80 text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("studentAnswer")), 1),
                      isSingleChoice.value ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "mt-3 p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20 text-sm"
                      }, [
                        createVNode("div", { class: "font-semibold opacity-80 text-amber-800 dark:text-amber-200" }, toDisplayString(unref(t)("selectedOneAnswer")), 1),
                        createVNode("div", { class: "mt-1 flex flex-row items-center justify-start" }, [
                          selectedAnswerSingle.value ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                            createVNode("div", { class: "font-semibold text-slate-700 dark:text-slate-300" }, " ID: " + toDisplayString(selectedAnswerSingle.value.id), 1),
                            createVNode("div", { class: "mx-2" }, "|"),
                            createVNode("div", null, [
                              createVNode("span", { class: "font-semibold text-slate-900 dark:text-slate-100" }, toDisplayString(unref(t)("text")) + ": ", 1),
                              createVNode("span", { class: "font-semibold text-teal-800 dark:text-teal-200" }, toDisplayString(stripHtml(
                                (_p = selectedAnswerSingle.value.translation) == null ? void 0 : _p.text
                              ) || "—"), 1)
                            ]),
                            selectedAnswerSingle.value.is_correct ? (openBlock(), createBlock("span", {
                              key: 0,
                              class: "ml-2"
                            }, " ✅ ")) : (openBlock(), createBlock("span", {
                              key: 1,
                              class: "ml-2"
                            }, " ❌ "))
                          ], 64)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                            createTextVNode(" — ")
                          ], 64))
                        ])
                      ])) : isMultipleChoice.value ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "mt-3 p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20 text-sm"
                      }, [
                        createVNode("div", { class: "font-semibold opacity-80 text-amber-800 dark:text-amber-200" }, toDisplayString(unref(t)("selectedSeveralAnswers")), 1),
                        selectedAnswersMultiple.value.length ? (openBlock(), createBlock("ul", {
                          key: 0,
                          class: "mt-2 list-disc pl-5"
                        }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(selectedAnswersMultiple.value, (answer) => {
                            var _a3;
                            return openBlock(), createBlock("li", {
                              key: answer.id,
                              class: "flex flex-row items-center justify-start"
                            }, [
                              createVNode("span", { class: "font-semibold text-slate-700 dark:text-slate-300" }, " ID: " + toDisplayString(answer.id) + " — " + toDisplayString(stripHtml(
                                (_a3 = answer.translation) == null ? void 0 : _a3.text
                              ) || "—"), 1),
                              answer.is_correct ? (openBlock(), createBlock("span", {
                                key: 0,
                                class: "ml-2"
                              }, " ✅ ")) : (openBlock(), createBlock("span", {
                                key: 1,
                                class: "ml-2"
                              }, " ❌ "))
                            ]);
                          }), 128))
                        ])) : (openBlock(), createBlock("div", {
                          key: 1,
                          class: "mt-2 text-sm opacity-80 font-semibold text-slate-700 dark:text-slate-300"
                        }, toDisplayString(selectedAnswerIds.value.length ? selectedAnswerIds.value.join(", ") : unref(t)("noData")), 1))
                      ])) : (openBlock(), createBlock("div", {
                        key: 2,
                        class: "mt-3 p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20 text-sm"
                      }, [
                        createVNode("div", { class: "font-semibold opacity-80 text-slate-800 dark:text-slate-200" }, toDisplayString(unref(t)("selectedTextAnswer")), 1),
                        createVNode("div", { class: "mt-2 p-3 border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-900/40 rounded-sm" }, [
                          createVNode("div", { class: "whitespace-pre-wrap font-semibold text-violet-800 dark:text-violet-200" }, toDisplayString(stripHtml(freeTextAnswer.value) || "—"), 1)
                        ])
                      ]))
                    ]),
                    createVNode("div", { class: "mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40" }, [
                      createVNode("div", { class: "text-center text-md font-semibold opacity-80 text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("checkInstructor")), 1),
                      createVNode("div", { class: "mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4" }, [
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$2, {
                            for: "is_correct",
                            class: "mb-1"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("isCorrect")) + " ", 1),
                              !isOpenText.value ? (openBlock(), createBlock("span", {
                                key: 0,
                                class: "ml-2 text-xs opacity-70"
                              }, " (" + toDisplayString(unref(t)("auto")) + ") ", 1)) : createCommentVNode("", true)
                            ]),
                            _: 1
                          }),
                          createVNode("label", { class: "flex items-center gap-2 text-sm" }, [
                            withDirectives(createVNode("input", {
                              id: "is_correct",
                              type: "checkbox",
                              "onUpdate:modelValue": ($event) => unref(form).is_correct = $event,
                              class: "rounded border-slate-400",
                              disabled: !isOpenText.value
                            }, null, 8, ["onUpdate:modelValue", "disabled"]), [
                              [vModelCheckbox, unref(form).is_correct]
                            ]),
                            createVNode("span", { class: "text-gray-900 dark:text-gray-100" }, toDisplayString(unref(form).is_correct ? unref(t)("yes") : unref(t)("no")), 1)
                          ]),
                          !isOpenText.value ? (openBlock(), createBlock("div", {
                            key: 0,
                            class: "mt-1 text-xs opacity-70 text-gray-900 dark:text-gray-100"
                          }, toDisplayString(unref(t)("auto")), 1)) : createCommentVNode("", true),
                          createVNode(_sfc_main$3, {
                            class: "mt-2",
                            message: unref(form).errors.is_correct
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$2, {
                            for: "score",
                            class: "mb-1"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("score")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$4, {
                            id: "score",
                            type: "number",
                            min: "0",
                            max: maxScoreReadonly.value ?? void 0,
                            modelValue: unref(form).score,
                            "onUpdate:modelValue": ($event) => unref(form).score = $event,
                            class: "w-full"
                          }, null, 8, ["max", "modelValue", "onUpdate:modelValue"]),
                          createVNode("div", { class: "flex gap-2 mt-2" }, [
                            createVNode("button", {
                              type: "button",
                              class: "text-xs px-2 py-1 border border-slate-400 rounded-sm hover:bg-slate-100 dark:hover:bg-slate-700 text-gray-900 dark:text-gray-100 font-semibold",
                              disabled: maxScoreReadonly.value === null,
                              onClick: setScoreMax
                            }, toDisplayString(unref(t)("setMax")), 9, ["disabled"]),
                            createVNode("button", {
                              type: "button",
                              class: "text-xs px-2 py-1 border border-slate-400 rounded-sm hover:bg-slate-100 dark:hover:bg-slate-700 text-gray-900 dark:text-gray-100 font-semibold",
                              onClick: setScoreZero
                            }, toDisplayString(unref(t)("setZero")), 1)
                          ]),
                          createVNode(_sfc_main$3, {
                            class: "mt-2",
                            message: unref(form).errors.score
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col items-start" }, [
                          createVNode(_sfc_main$2, { class: "mb-1" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("maxScore")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode("div", { class: "w-full py-0.5 px-2 text-sm border border-slate-400 rounded-sm bg-slate-100 dark:bg-slate-800 font-semibold text-gray-900 dark:text-gray-100" }, toDisplayString(maxScoreReadonly.value ?? __props.item.max_score ?? "—"), 1),
                          createVNode(_sfc_main$3, {
                            class: "mt-2",
                            message: unref(form).errors.max_score
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mt-4 flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, {
                          for: "reviewer_comment",
                          class: "mb-1"
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("reviewerComment")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$5, {
                          id: "reviewer_comment",
                          modelValue: unref(form).reviewer_comment,
                          "onUpdate:modelValue": ($event) => unref(form).reviewer_comment = $event,
                          height: 260
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.reviewer_comment
                        }, null, 8, ["message"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.server
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center gap-3 pb-4" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.schoolQuizAttemptItems.index")
                      }, {
                        icon: withCtx(() => [
                          (openBlock(), createBlock("svg", {
                            class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                            viewBox: "0 0 16 16"
                          }, [
                            createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z" })
                          ]))
                        ]),
                        default: withCtx(() => [
                          createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                        ]),
                        _: 1
                      }, 8, ["href"]),
                      createVNode(_sfc_main$6, {
                        class: { "opacity-25": unref(form).processing },
                        disabled: unref(form).processing
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(unref(t)("save") || unref(t)("update")), 1)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolQuizAttemptItems/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
