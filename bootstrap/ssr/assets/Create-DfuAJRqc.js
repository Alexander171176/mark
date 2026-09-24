import { computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$7 } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$2 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$6 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$3 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$4 } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$5 } from "./ActivityCheckbox-BiU-rq6S.js";
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
    attempts: {
      type: Array,
      default: () => []
    },
    questions: {
      type: Array,
      default: () => []
    },
    answers: {
      type: Array,
      default: () => []
    },
    defaultAttemptId: {
      type: Number,
      default: null
    },
    defaultQuestionId: {
      type: Number,
      default: null
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const stripHtml = (html = "") => {
      return (html || "").replace(/<\/p>/gi, " ").replace(/<br\s*\/?>/gi, " ").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/&amp;/gi, "&").replace(/&quot;/gi, '"').replace(/&#039;/gi, "'").replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/\s+/g, " ").trim();
    };
    const shortText = (value, limit = 120) => {
      const clean = stripHtml(value);
      return clean.length > limit ? clean.slice(0, limit) + "…" : clean;
    };
    const toNumberOrNull = (value) => {
      if (value === "" || value === null || typeof value === "undefined") {
        return null;
      }
      const number = Number(value);
      return Number.isFinite(number) ? number : null;
    };
    const dynamicOptionsLimit = (items) => {
      const list = Array.isArray(items) ? items : [];
      return list.length + 10;
    };
    const findById = (items, id) => {
      if (!id) return null;
      return items.find(
        (item) => Number(item.id) === Number(id)
      ) || null;
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
    const attemptOptions = computed(() => {
      return (props.attempts || []).map((attempt) => {
        var _a, _b, _c;
        const user = attempt.user ? `${attempt.user.name || "—"}${attempt.user.email ? ` (${attempt.user.email})` : ""}` : `User ID: ${attempt.user_id || "—"}`;
        const quizTitle = ((_b = (_a = attempt.quiz) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((_c = attempt.quiz) == null ? void 0 : _c.slug) || `Quiz ID: ${attempt.school_quiz_id || "—"}`;
        return {
          ...attempt,
          label: `[ID: ${attempt.id}] ${user} — ${quizTitle} — ${t("attemptNumber")} ${attempt.attempt_number ?? "—"}`
        };
      });
    });
    const questionOptions = computed(() => {
      return (props.questions || []).map((question) => {
        var _a;
        return {
          ...question,
          label: `[ID: ${question.id}] ${shortText(
            ((_a = question.translation) == null ? void 0 : _a.question_text) || `#${question.id}`,
            140
          )} — ${questionTypeLabel(question.question_type)}`
        };
      });
    });
    const answerOptionsAll = computed(() => {
      return (props.answers || []).map((answer) => {
        var _a;
        return {
          ...answer,
          label: `[ID: ${answer.id}] ${shortText(
            ((_a = answer.translation) == null ? void 0 : _a.text) || `#${answer.id}`,
            120
          )}${answer.is_correct ? " ✅" : ""}`
        };
      });
    });
    const initialAttempt = findById(
      attemptOptions.value,
      props.defaultAttemptId
    );
    const initialQuestion = findById(
      questionOptions.value,
      props.defaultQuestionId
    );
    const form = useForm({
      school_quiz_attempt_id: (initialAttempt == null ? void 0 : initialAttempt.id) ?? props.defaultAttemptId ?? null,
      school_quiz_question_id: (initialQuestion == null ? void 0 : initialQuestion.id) ?? props.defaultQuestionId ?? null,
      selected_answer_id: null,
      selected_answer_ids: [],
      free_text_answer: "",
      is_correct: false,
      score: 0,
      max_score: (initialQuestion == null ? void 0 : initialQuestion.points) !== null && typeof (initialQuestion == null ? void 0 : initialQuestion.points) !== "undefined" ? Number(initialQuestion.points) : 0,
      reviewer_comment: ""
    });
    const selectedAttempt = computed({
      get: () => findById(
        attemptOptions.value,
        form.school_quiz_attempt_id
      ),
      set: (attempt) => {
        form.school_quiz_attempt_id = (attempt == null ? void 0 : attempt.id) ?? null;
      }
    });
    const selectedQuestion = computed({
      get: () => findById(
        questionOptions.value,
        form.school_quiz_question_id
      ),
      set: (question) => {
        form.school_quiz_question_id = (question == null ? void 0 : question.id) ?? null;
        form.selected_answer_id = null;
        form.selected_answer_ids = [];
        form.free_text_answer = "";
        form.is_correct = false;
        form.score = 0;
        const points = Number(
          question == null ? void 0 : question.points
        );
        form.max_score = Number.isFinite(points) ? points : 0;
      }
    });
    const questionType = computed(
      () => {
        var _a;
        return ((_a = selectedQuestion.value) == null ? void 0 : _a.question_type) || null;
      }
    );
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
    const maxScoreReadonly = computed(() => {
      var _a;
      const points = Number(
        (_a = selectedQuestion.value) == null ? void 0 : _a.points
      );
      return Number.isFinite(points) ? points : null;
    });
    const answerOptions = computed(() => {
      const questionId = form.school_quiz_question_id;
      if (!questionId) {
        return answerOptionsAll.value;
      }
      return answerOptionsAll.value.filter(
        (answer) => Number(answer.school_quiz_question_id) === Number(questionId)
      );
    });
    const selectedAnswer = computed({
      get: () => findById(
        answerOptions.value,
        form.selected_answer_id
      ),
      set: (answer) => {
        form.selected_answer_id = (answer == null ? void 0 : answer.id) ?? null;
        if (!answer) {
          form.is_correct = false;
          form.score = 0;
          return;
        }
        form.selected_answer_ids = [];
        form.free_text_answer = "";
        form.is_correct = Boolean(answer.is_correct);
        form.score = answer.is_correct ? maxScoreReadonly.value ?? form.max_score ?? 0 : 0;
      }
    });
    const selectedAnswers = computed({
      get: () => {
        const ids = Array.isArray(
          form.selected_answer_ids
        ) ? form.selected_answer_ids : [];
        return answerOptions.value.filter(
          (answer) => ids.some(
            (id) => Number(id) === Number(answer.id)
          )
        );
      },
      set: (answers) => {
        const list = Array.isArray(answers) ? answers : [];
        form.selected_answer_ids = list.map((answer) => answer.id);
        form.selected_answer_id = null;
        form.free_text_answer = "";
        if (!list.length) {
          form.is_correct = false;
          form.score = 0;
          return;
        }
        const hasWrong = list.some(
          (answer) => !answer.is_correct
        );
        form.is_correct = !hasWrong;
        form.score = form.is_correct ? maxScoreReadonly.value ?? form.max_score ?? 0 : 0;
      }
    });
    const setScoreMax = () => {
      if (maxScoreReadonly.value === null) {
        return;
      }
      form.score = maxScoreReadonly.value;
      form.max_score = maxScoreReadonly.value;
      toast.info(
        `${t("score")}: ${t("setMax")}`
      );
    };
    const setScoreZero = () => {
      form.score = 0;
      toast.info(
        `${t("score")}: 0`
      );
    };
    const submitForm = () => {
      form.transform((data) => {
        return {
          ...data,
          school_quiz_attempt_id: data.school_quiz_attempt_id ?? null,
          school_quiz_question_id: data.school_quiz_question_id ?? null,
          selected_answer_id: isSingleChoice.value ? data.selected_answer_id ?? null : null,
          selected_answer_ids: isMultipleChoice.value ? Array.isArray(
            data.selected_answer_ids
          ) ? data.selected_answer_ids : [] : null,
          free_text_answer: isOpenText.value ? data.free_text_answer || null : null,
          is_correct: Boolean(
            data.is_correct
          ),
          score: toNumberOrNull(
            data.score
          ) ?? 0,
          max_score: toNumberOrNull(
            data.max_score
          ) ?? maxScoreReadonly.value ?? 0,
          reviewer_comment: (data.reviewer_comment || "").toString().trim() || null
        };
      });
      form.post(
        route(
          "admin.schoolQuizAttemptItems.store"
        ),
        {
          preserveScroll: true,
          onSuccess: () => {
            toast.success(
              "Ответ попытки квиза успешно создан."
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || "Проверьте правильность заполнения полей."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("createQuizAttemptItem")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("createQuizAttemptItem"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("createQuizAttemptItem")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("createQuizAttemptItem")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          var _a, _b;
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
            _push2(`</div><form class="pt-3 w-full"${_scopeId}><div class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40"${_scopeId}><div class="text-center text-md font-semibold opacity-80 text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("context"))}</div><div class="flex flex-col items-start text-xs mb-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "question" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("quizQuestion"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("quizQuestion")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "question",
              modelValue: selectedQuestion.value,
              "onUpdate:modelValue": ($event) => selectedQuestion.value = $event,
              options: questionOptions.value,
              "options-limit": dynamicOptionsLimit(questionOptions.value),
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
              message: unref(form).errors.school_quiz_question_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start text-xs"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "attempt" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("attempt"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("attempt")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "attempt",
              modelValue: selectedAttempt.value,
              "onUpdate:modelValue": ($event) => selectedAttempt.value = $event,
              options: attemptOptions.value,
              "options-limit": dynamicOptionsLimit(attemptOptions.value),
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
              message: unref(form).errors.school_quiz_attempt_id
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (selectedQuestion.value) {
              _push2(`<div class="mt-4 p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20"${_scopeId}><div class="text-sm font-semibold text-slate-700 dark:text-slate-300"${_scopeId}> ID: ${ssrInterpolate(selectedQuestion.value.id)}</div><div class="mt-2 whitespace-pre-wrap leading-relaxed text-sm font-semibold text-amber-800 dark:text-amber-200"${_scopeId}>${ssrInterpolate(stripHtml(
                (_a = selectedQuestion.value.translation) == null ? void 0 : _a.question_text
              ) || "—")}</div><div class="mt-2 text-sm flex flex-row items-center justify-start"${_scopeId}><div${_scopeId}><span class="font-semibold text-slate-900 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("type"))}: </span><span class="font-semibold text-teal-800 dark:text-teal-200"${_scopeId}>${ssrInterpolate(questionTypeLabel(selectedQuestion.value.question_type))}</span></div><div class="mx-2"${_scopeId}>|</div><div${_scopeId}><span class="font-semibold text-slate-900 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("maxScore"))}: </span><span class="font-semibold text-teal-800 dark:text-teal-200"${_scopeId}>${ssrInterpolate(maxScoreReadonly.value ?? unref(form).max_score ?? "—")}</span></div></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40"${_scopeId}><div class="text-center text-md font-semibold opacity-80 text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("studentAnswer"))}</div>`);
            if (isSingleChoice.value) {
              _push2(`<div class="mt-4 flex flex-col items-start"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$2, { for: "selected_answer_id" }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("selectedOneAnswer"))}`);
                  } else {
                    return [
                      createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                      createTextVNode(" " + toDisplayString(unref(t)("selectedOneAnswer")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(unref(VueMultiselect), {
                id: "selected_answer_id",
                modelValue: selectedAnswer.value,
                "onUpdate:modelValue": ($event) => selectedAnswer.value = $event,
                options: answerOptions.value,
                "options-limit": dynamicOptionsLimit(answerOptions.value),
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
                message: unref(form).errors.selected_answer_id
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else if (isMultipleChoice.value) {
              _push2(`<div class="mt-4 flex flex-col items-start"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$2, { for: "selected_answer_ids" }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("selectedSeveralAnswers"))}`);
                  } else {
                    return [
                      createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                      createTextVNode(" " + toDisplayString(unref(t)("selectedSeveralAnswers")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(unref(VueMultiselect), {
                id: "selected_answer_ids",
                modelValue: selectedAnswers.value,
                "onUpdate:modelValue": ($event) => selectedAnswers.value = $event,
                options: answerOptions.value,
                "options-limit": dynamicOptionsLimit(answerOptions.value),
                multiple: true,
                "close-on-select": false,
                "clear-on-select": false,
                "preserve-search": true,
                placeholder: unref(t)("select"),
                label: "label",
                "track-by": "id",
                class: "w-full"
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$3, {
                class: "mt-2",
                message: unref(form).errors.selected_answer_ids
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else if (isOpenText.value) {
              _push2(`<div class="mt-4 flex flex-col items-start"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$2, { for: "free_text_answer" }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("selectedTextAnswer"))}`);
                  } else {
                    return [
                      createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                      createTextVNode(" " + toDisplayString(unref(t)("selectedTextAnswer")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, {
                id: "free_text_answer",
                modelValue: unref(form).free_text_answer,
                "onUpdate:modelValue": ($event) => unref(form).free_text_answer = $event,
                height: 260
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$3, {
                class: "mt-2",
                message: unref(form).errors.free_text_answer
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<div class="mt-3 p-3 text-center text-slate-700 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("select"))} ${ssrInterpolate(unref(t)("quizQuestion"))}</div>`);
            }
            _push2(`</div><div class="mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40"${_scopeId}><div class="text-center text-md font-semibold opacity-80 text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(t)("checkInstructor"))}</div><div class="mt-4 grid grid-cols-1 lg:grid-cols-3 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              for: "is_correct",
              class: "mb-1"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("isCorrect"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("isCorrect")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<label class="flex items-center gap-2 text-sm"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "is_correct",
              modelValue: unref(form).is_correct,
              "onUpdate:modelValue": ($event) => unref(form).is_correct = $event
            }, null, _parent2, _scopeId));
            _push2(`<span class="text-gray-900 dark:text-gray-100"${_scopeId}>${ssrInterpolate(unref(form).is_correct ? unref(t)("yes") : unref(t)("no"))}</span></label>`);
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
            _push2(ssrRenderComponent(_sfc_main$6, {
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
            _push2(ssrRenderComponent(_sfc_main$2, {
              for: "max_score",
              class: "mb-1"
            }, {
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
            _push2(ssrRenderComponent(_sfc_main$6, {
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
            _push2(ssrRenderComponent(_sfc_main$4, {
              id: "reviewer_comment",
              modelValue: unref(form).reviewer_comment,
              "onUpdate:modelValue": ($event) => unref(form).reviewer_comment = $event,
              height: 260
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.reviewer_comment
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
            _push2(ssrRenderComponent(_sfc_main$7, {
              class: { "opacity-25": unref(form).processing },
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
                      createVNode("div", { class: "flex flex-col items-start text-xs mb-2" }, [
                        createVNode(_sfc_main$2, { for: "question" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("quizQuestion")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(unref(VueMultiselect), {
                          id: "question",
                          modelValue: selectedQuestion.value,
                          "onUpdate:modelValue": ($event) => selectedQuestion.value = $event,
                          options: questionOptions.value,
                          "options-limit": dynamicOptionsLimit(questionOptions.value),
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
                          message: unref(form).errors.school_quiz_question_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start text-xs" }, [
                        createVNode(_sfc_main$2, { for: "attempt" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("attempt")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(unref(VueMultiselect), {
                          id: "attempt",
                          modelValue: selectedAttempt.value,
                          "onUpdate:modelValue": ($event) => selectedAttempt.value = $event,
                          options: attemptOptions.value,
                          "options-limit": dynamicOptionsLimit(attemptOptions.value),
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
                          message: unref(form).errors.school_quiz_attempt_id
                        }, null, 8, ["message"])
                      ]),
                      selectedQuestion.value ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "mt-4 p-2 border border-slate-300/70 dark:border-slate-200/30 bg-white/70 dark:bg-slate-900/20"
                      }, [
                        createVNode("div", { class: "text-sm font-semibold text-slate-700 dark:text-slate-300" }, " ID: " + toDisplayString(selectedQuestion.value.id), 1),
                        createVNode("div", { class: "mt-2 whitespace-pre-wrap leading-relaxed text-sm font-semibold text-amber-800 dark:text-amber-200" }, toDisplayString(stripHtml(
                          (_b = selectedQuestion.value.translation) == null ? void 0 : _b.question_text
                        ) || "—"), 1),
                        createVNode("div", { class: "mt-2 text-sm flex flex-row items-center justify-start" }, [
                          createVNode("div", null, [
                            createVNode("span", { class: "font-semibold text-slate-900 dark:text-slate-100" }, toDisplayString(unref(t)("type")) + ": ", 1),
                            createVNode("span", { class: "font-semibold text-teal-800 dark:text-teal-200" }, toDisplayString(questionTypeLabel(selectedQuestion.value.question_type)), 1)
                          ]),
                          createVNode("div", { class: "mx-2" }, "|"),
                          createVNode("div", null, [
                            createVNode("span", { class: "font-semibold text-slate-900 dark:text-slate-100" }, toDisplayString(unref(t)("maxScore")) + ": ", 1),
                            createVNode("span", { class: "font-semibold text-teal-800 dark:text-teal-200" }, toDisplayString(maxScoreReadonly.value ?? unref(form).max_score ?? "—"), 1)
                          ])
                        ])
                      ])) : createCommentVNode("", true)
                    ]),
                    createVNode("div", { class: "mb-4 p-3 border border-dashed border-slate-500 dark:border-slate-300 bg-white/60 dark:bg-slate-800/40" }, [
                      createVNode("div", { class: "text-center text-md font-semibold opacity-80 text-gray-900 dark:text-gray-100" }, toDisplayString(unref(t)("studentAnswer")), 1),
                      isSingleChoice.value ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "mt-4 flex flex-col items-start"
                      }, [
                        createVNode(_sfc_main$2, { for: "selected_answer_id" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("selectedOneAnswer")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(unref(VueMultiselect), {
                          id: "selected_answer_id",
                          modelValue: selectedAnswer.value,
                          "onUpdate:modelValue": ($event) => selectedAnswer.value = $event,
                          options: answerOptions.value,
                          "options-limit": dynamicOptionsLimit(answerOptions.value),
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
                          message: unref(form).errors.selected_answer_id
                        }, null, 8, ["message"])
                      ])) : isMultipleChoice.value ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "mt-4 flex flex-col items-start"
                      }, [
                        createVNode(_sfc_main$2, { for: "selected_answer_ids" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("selectedSeveralAnswers")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(unref(VueMultiselect), {
                          id: "selected_answer_ids",
                          modelValue: selectedAnswers.value,
                          "onUpdate:modelValue": ($event) => selectedAnswers.value = $event,
                          options: answerOptions.value,
                          "options-limit": dynamicOptionsLimit(answerOptions.value),
                          multiple: true,
                          "close-on-select": false,
                          "clear-on-select": false,
                          "preserve-search": true,
                          placeholder: unref(t)("select"),
                          label: "label",
                          "track-by": "id",
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.selected_answer_ids
                        }, null, 8, ["message"])
                      ])) : isOpenText.value ? (openBlock(), createBlock("div", {
                        key: 2,
                        class: "mt-4 flex flex-col items-start"
                      }, [
                        createVNode(_sfc_main$2, { for: "free_text_answer" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("selectedTextAnswer")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$4, {
                          id: "free_text_answer",
                          modelValue: unref(form).free_text_answer,
                          "onUpdate:modelValue": ($event) => unref(form).free_text_answer = $event,
                          height: 260
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.free_text_answer
                        }, null, 8, ["message"])
                      ])) : (openBlock(), createBlock("div", {
                        key: 3,
                        class: "mt-3 p-3 text-center text-slate-700 dark:text-slate-100"
                      }, toDisplayString(unref(t)("select")) + " " + toDisplayString(unref(t)("quizQuestion")), 1))
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
                              createTextVNode(toDisplayString(unref(t)("isCorrect")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode("label", { class: "flex items-center gap-2 text-sm" }, [
                            createVNode(_sfc_main$5, {
                              id: "is_correct",
                              modelValue: unref(form).is_correct,
                              "onUpdate:modelValue": ($event) => unref(form).is_correct = $event
                            }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                            createVNode("span", { class: "text-gray-900 dark:text-gray-100" }, toDisplayString(unref(form).is_correct ? unref(t)("yes") : unref(t)("no")), 1)
                          ]),
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
                          createVNode(_sfc_main$6, {
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
                          createVNode(_sfc_main$2, {
                            for: "max_score",
                            class: "mb-1"
                          }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("maxScore")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$6, {
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
                        createVNode(_sfc_main$4, {
                          id: "reviewer_comment",
                          modelValue: unref(form).reviewer_comment,
                          "onUpdate:modelValue": ($event) => unref(form).reviewer_comment = $event,
                          height: 260
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.reviewer_comment
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
                      createVNode(_sfc_main$7, {
                        class: { "opacity-25": unref(form).processing },
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolQuizAttemptItems/Create.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
