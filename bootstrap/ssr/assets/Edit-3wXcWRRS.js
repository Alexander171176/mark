import { ref, computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$9 } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$8 } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$6 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$5 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$4 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$7 } from "./TranslationTabs-czH7YSpu.js";
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
    answer: {
      type: Object,
      required: true
    },
    quizzes: {
      type: Array,
      default: () => []
    },
    questions: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    var _a, _b;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const makeTranslation = () => ({
      text: "",
      explanation: ""
    });
    const defaultLocale = props.currentLocale || ((_b = (_a = props.answer) == null ? void 0 : _a.translation) == null ? void 0 : _b.locale) || props.availableLocales[0] || "ru";
    const buildTranslations = () => {
      const result = {};
      (props.answer.translations || []).forEach(
        (translation) => {
          result[translation.locale] = {
            text: translation.text || "",
            explanation: translation.explanation || ""
          };
        }
      );
      if (!Object.keys(result).length) {
        result[defaultLocale] = makeTranslation();
      }
      if (!result[defaultLocale]) {
        result[defaultLocale] = makeTranslation();
      }
      return result;
    };
    const activeLocale = ref(
      defaultLocale
    );
    const form = useForm({
      _method: "PUT",
      school_quiz_id: props.answer.school_quiz_id ?? null,
      school_quiz_question_id: props.answer.school_quiz_question_id ?? null,
      is_correct: Boolean(
        props.answer.is_correct
      ),
      weight: props.answer.weight ?? 0,
      sort: props.answer.sort ?? 0,
      activity: Boolean(
        props.answer.activity
      ),
      translations: buildTranslations()
    });
    const currentTranslation = computed(() => {
      if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation();
      }
      return form.translations[activeLocale.value];
    });
    const pageTitle = computed(() => {
      var _a2, _b2;
      return stripHtml(
        currentTranslation.value.text || ((_b2 = (_a2 = props.answer) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.text) || `ID: ${props.answer.id}`
      );
    });
    const getError = (key) => form.errors[`translations.${activeLocale.value}.${key}`];
    const stripHtml = (html = "") => {
      return String(html || "").replace(/<\/p>/gi, " ").replace(/<br\s*\/?>/gi, " ").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/\s+/g, " ").trim();
    };
    const shortText = (html, limit = 120) => {
      const clean = stripHtml(html);
      return clean.length > limit ? `${clean.slice(0, limit)}…` : clean;
    };
    const dynamicOptionsLimit = (items) => ((items == null ? void 0 : items.length) || 0) + 10;
    const quizOptions = computed(
      () => props.quizzes ?? []
    );
    const questionOptions = computed(() => {
      const items = props.questions ?? [];
      const filtered = form.school_quiz_id ? items.filter(
        (question) => Number(
          question.school_quiz_id
        ) === Number(
          form.school_quiz_id
        )
      ) : items;
      return [...filtered].sort(
        (a, b) => (a.sort ?? 0) - (b.sort ?? 0) || (a.id ?? 0) - (b.id ?? 0)
      );
    });
    const quizOptionLabel = (quiz) => {
      var _a2, _b2, _c, _d, _e, _f, _g;
      if (!quiz) {
        return "";
      }
      const idPart = `[ID: ${quiz.id}]`;
      const title = ((_a2 = quiz == null ? void 0 : quiz.translation) == null ? void 0 : _a2.title) || (quiz == null ? void 0 : quiz.slug) || `#${quiz.id}`;
      const context = [
        ((_c = (_b2 = quiz == null ? void 0 : quiz.course) == null ? void 0 : _b2.translation) == null ? void 0 : _c.title) ? `Курс: ${quiz.course.translation.title}` : null,
        ((_e = (_d = quiz == null ? void 0 : quiz.module) == null ? void 0 : _d.translation) == null ? void 0 : _e.title) ? `Модуль: ${quiz.module.translation.title}` : null,
        ((_g = (_f = quiz == null ? void 0 : quiz.lesson) == null ? void 0 : _f.translation) == null ? void 0 : _g.title) ? `Урок: ${quiz.lesson.translation.title}` : null
      ].filter(Boolean).join(" / ");
      return context ? `${idPart} ${title} — ${context}` : `${idPart} ${title}`;
    };
    const questionOptionLabel = (question) => {
      var _a2, _b2, _c;
      if (!question) {
        return "";
      }
      const idPart = `[ID: ${question.id}]`;
      const questionText = ((_a2 = question == null ? void 0 : question.translation) == null ? void 0 : _a2.question_text) ? shortText(
        question.translation.question_text
      ) : `#${question.id}`;
      const quizTitle = (_c = (_b2 = quizOptions.value.find(
        (quiz) => Number(quiz.id) === Number(
          question.school_quiz_id
        )
      )) == null ? void 0 : _b2.translation) == null ? void 0 : _c.title;
      const quizPart = quizTitle ? `Quiz: ${quizTitle}` : question.school_quiz_id ? `Quiz ID: ${question.school_quiz_id}` : null;
      return quizPart ? `${idPart} ${questionText} — ${quizPart}` : `${idPart} ${questionText}`;
    };
    const selectedQuiz = computed({
      get: () => quizOptions.value.find(
        (quiz) => Number(quiz.id) === Number(
          form.school_quiz_id
        )
      ) || null,
      set: (quiz) => {
        var _a2;
        const quizId = (quiz == null ? void 0 : quiz.id) ?? null;
        if (form.school_quiz_question_id && Number(quizId) !== Number(
          (_a2 = selectedQuestion.value) == null ? void 0 : _a2.school_quiz_id
        )) {
          form.school_quiz_question_id = null;
        }
        form.school_quiz_id = quizId;
      }
    });
    const selectedQuestion = computed({
      get: () => (props.questions ?? []).find(
        (question) => Number(question.id) === Number(
          form.school_quiz_question_id
        )
      ) || null,
      set: (question) => {
        form.school_quiz_question_id = (question == null ? void 0 : question.id) ?? null;
        if (question == null ? void 0 : question.school_quiz_id) {
          form.school_quiz_id = question.school_quiz_id;
        }
      }
    });
    const submitForm = () => {
      form.transform((data) => ({
        ...data,
        school_quiz_id: data.school_quiz_id || null,
        school_quiz_question_id: data.school_quiz_question_id || null,
        activity: data.activity ? 1 : 0,
        is_correct: data.is_correct ? 1 : 0,
        weight: data.weight === "" || data.weight === null ? 0 : Number(data.weight),
        sort: data.sort === "" || data.sort === null ? 0 : Number(data.sort)
      }));
      form.post(
        route(
          "admin.schoolQuizAnswers.update",
          {
            schoolQuizAnswer: props.answer.id
          }
        ),
        {
          errorBag: "editSchoolQuizAnswer",
          preserveScroll: true,
          onSuccess: () => {
            toast.success(
              "Ответ квиза успешно обновлён!"
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка обновления ответа квиза:",
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
        title: unref(t)("editQuizAnswer")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editQuizAnswer"))}: ${ssrInterpolate(pageTitle.value)} [ID: ${ssrInterpolate(__props.answer.id)}] `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editQuizAnswer")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(__props.answer.id) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editQuizAnswer")) + ": " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(__props.answer.id) + "] ", 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolQuizAnswers.index")
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
            _push2(`</div><form class="p-3 w-full"${_scopeId}><div class="pb-12"${_scopeId}><div class="mb-4 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
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
              modelValue: unref(form).is_correct,
              "onUpdate:modelValue": ($event) => unref(form).is_correct = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "is_correct",
              text: unref(t)("isCorrect"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.is_correct
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "weight",
              value: unref(t)("points")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "weight",
              modelValue: unref(form).weight,
              "onUpdate:modelValue": ($event) => unref(form).weight = $event,
              type: "number",
              min: "0",
              max: "100",
              class: "w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.weight
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "sort",
              value: unref(t)("sort")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "sort",
              modelValue: unref(form).sort,
              "onUpdate:modelValue": ($event) => unref(form).sort = $event,
              type: "number",
              min: "0",
              class: "w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-4 flex flex-col items-start w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "school_quiz_id",
              value: unref(t)("quiz"),
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "school_quiz_id",
              modelValue: selectedQuiz.value,
              "onUpdate:modelValue": ($event) => selectedQuiz.value = $event,
              options: quizOptions.value,
              "options-limit": dynamicOptionsLimit(quizOptions.value),
              multiple: false,
              "close-on-select": true,
              "clear-on-select": false,
              "preserve-search": true,
              "allow-empty": true,
              placeholder: unref(t)("select"),
              "track-by": "id",
              "custom-label": quizOptionLabel,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.school_quiz_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-4 flex flex-col items-start w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "school_quiz_question_id",
              value: unref(t)("quizQuestion"),
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "school_quiz_question_id",
              modelValue: selectedQuestion.value,
              "onUpdate:modelValue": ($event) => selectedQuestion.value = $event,
              options: questionOptions.value,
              "options-limit": dynamicOptionsLimit(questionOptions.value),
              multiple: false,
              "close-on-select": true,
              "clear-on-select": false,
              "preserve-search": true,
              "allow-empty": true,
              placeholder: unref(t)("select"),
              "track-by": "id",
              "custom-label": questionOptionLabel,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.school_quiz_question_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              modelValue: activeLocale.value,
              "onUpdate:modelValue": ($event) => activeLocale.value = $event,
              translations: unref(form).translations,
              "available-locales": __props.availableLocales,
              "make-translation": makeTranslation,
              "onUpdate:translations": ($event) => unref(form).translations = $event,
              onRemoved: ($event) => unref(toast).warning("Перевод удалён."),
              onAdded: ($event) => unref(toast).success("Локаль добавлена.")
            }, null, _parent2, _scopeId));
            _push2(`<div class="mb-4 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, { for: "text" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}> * </span> ${ssrInterpolate(unref(t)("answer"))} [${ssrInterpolate(activeLocale.value.toUpperCase())}] `);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                    createTextVNode(" " + toDisplayString(unref(t)("answer")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "text",
              modelValue: currentTranslation.value.text,
              "onUpdate:modelValue": ($event) => currentTranslation.value.text = $event,
              height: 250
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("text")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-4 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              for: "explanation",
              value: `${unref(t)("explanation")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "explanation",
              modelValue: currentTranslation.value.explanation,
              "onUpdate:modelValue": ($event) => currentTranslation.value.explanation = $event,
              height: 220
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: getError("explanation")
            }, null, _parent2, _scopeId));
            _push2(`</div></div></div><div class="flex items-center justify-center gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolQuizAnswers.index")
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
            _push2(ssrRenderComponent(_sfc_main$9, {
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
              createVNode("div", { class: "px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-4" }, [
                    createVNode(_sfc_main$1, {
                      href: _ctx.route("admin.schoolQuizAnswers.index")
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
                    class: "p-3 w-full",
                    onSubmit: withModifiers(submitForm, ["prevent"])
                  }, [
                    createVNode("div", { class: "pb-12" }, [
                      createVNode("div", { class: "mb-4 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between" }, [
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
                            modelValue: unref(form).is_correct,
                            "onUpdate:modelValue": ($event) => unref(form).is_correct = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            for: "is_correct",
                            text: unref(t)("isCorrect"),
                            class: "text-sm h-8 flex items-center"
                          }, null, 8, ["text"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.is_correct
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode(_sfc_main$5, {
                            for: "weight",
                            value: unref(t)("points")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$6, {
                            id: "weight",
                            modelValue: unref(form).weight,
                            "onUpdate:modelValue": ($event) => unref(form).weight = $event,
                            type: "number",
                            min: "0",
                            max: "100",
                            class: "w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.weight
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode(_sfc_main$5, {
                            for: "sort",
                            value: unref(t)("sort")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$6, {
                            id: "sort",
                            modelValue: unref(form).sort,
                            "onUpdate:modelValue": ($event) => unref(form).sort = $event,
                            type: "number",
                            min: "0",
                            class: "w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            message: unref(form).errors.sort
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-4 flex flex-col items-start w-full" }, [
                        createVNode(_sfc_main$5, {
                          for: "school_quiz_id",
                          value: unref(t)("quiz"),
                          class: "mb-1"
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "school_quiz_id",
                          modelValue: selectedQuiz.value,
                          "onUpdate:modelValue": ($event) => selectedQuiz.value = $event,
                          options: quizOptions.value,
                          "options-limit": dynamicOptionsLimit(quizOptions.value),
                          multiple: false,
                          "close-on-select": true,
                          "clear-on-select": false,
                          "preserve-search": true,
                          "allow-empty": true,
                          placeholder: unref(t)("select"),
                          "track-by": "id",
                          "custom-label": quizOptionLabel,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.school_quiz_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-4 flex flex-col items-start w-full" }, [
                        createVNode(_sfc_main$5, {
                          for: "school_quiz_question_id",
                          value: unref(t)("quizQuestion"),
                          class: "mb-1"
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "school_quiz_question_id",
                          modelValue: selectedQuestion.value,
                          "onUpdate:modelValue": ($event) => selectedQuestion.value = $event,
                          options: questionOptions.value,
                          "options-limit": dynamicOptionsLimit(questionOptions.value),
                          multiple: false,
                          "close-on-select": true,
                          "clear-on-select": false,
                          "preserve-search": true,
                          "allow-empty": true,
                          placeholder: unref(t)("select"),
                          "track-by": "id",
                          "custom-label": questionOptionLabel,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.school_quiz_question_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm" }, [
                        createVNode(_sfc_main$7, {
                          modelValue: activeLocale.value,
                          "onUpdate:modelValue": ($event) => activeLocale.value = $event,
                          translations: unref(form).translations,
                          "available-locales": __props.availableLocales,
                          "make-translation": makeTranslation,
                          "onUpdate:translations": ($event) => unref(form).translations = $event,
                          onRemoved: ($event) => unref(toast).warning("Перевод удалён."),
                          onAdded: ($event) => unref(toast).success("Локаль добавлена.")
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "translations", "available-locales", "onUpdate:translations", "onRemoved", "onAdded"]),
                        createVNode("div", { class: "mb-4 flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, { for: "text" }, {
                            default: withCtx(() => [
                              createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                              createTextVNode(" " + toDisplayString(unref(t)("answer")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$8, {
                            id: "text",
                            modelValue: currentTranslation.value.text,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.text = $event,
                            height: 250
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getError("text")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-4 flex flex-col items-start" }, [
                          createVNode(_sfc_main$5, {
                            for: "explanation",
                            value: `${unref(t)("explanation")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$8, {
                            id: "explanation",
                            modelValue: currentTranslation.value.explanation,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.explanation = $event,
                            height: 220
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$4, {
                            class: "mt-2",
                            message: getError("explanation")
                          }, null, 8, ["message"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center gap-3" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.schoolQuizAnswers.index")
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
                      createVNode(_sfc_main$9, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolQuizAnswers/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
