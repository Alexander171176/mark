import { ref, watch, computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, withDirectives, Fragment, renderList, vModelSelect, vModelText, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
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
import { _ as _sfc_main$5 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$4 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$6 } from "./InputError-CLVdJ1nk.js";
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
    quizzes: {
      type: Array,
      default: () => []
    },
    defaultQuizId: {
      type: Number,
      default: null
    }
  },
  setup(__props) {
    var _a;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const makeTranslation = () => ({
      question_text: "",
      explanation: ""
    });
    const defaultLocale = props.currentLocale || ((_a = props.availableLocales) == null ? void 0 : _a[0]) || "ru";
    const activeLocale = ref(
      defaultLocale
    );
    const form = useForm({
      school_quiz_id: props.defaultQuizId ?? null,
      sort: 0,
      question_type: "single_choice",
      points: 1,
      activity: true,
      meta_raw: "",
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
    const quizOptions = computed(
      () => props.quizzes ?? []
    );
    const dynamicOptionsLimit = computed(
      () => {
        return quizOptions.value.length + 10;
      }
    );
    const quizOptionLabel = (quiz) => {
      var _a2, _b, _c, _d, _e, _f, _g;
      if (!quiz) {
        return "";
      }
      const idPart = `[ID: ${quiz.id}]`;
      const titlePart = ((_a2 = quiz == null ? void 0 : quiz.translation) == null ? void 0 : _a2.title) || (quiz == null ? void 0 : quiz.slug) || `#${quiz.id}`;
      const context = [
        ((_c = (_b = quiz == null ? void 0 : quiz.lesson) == null ? void 0 : _b.translation) == null ? void 0 : _c.title) ? `Урок: ${quiz.lesson.translation.title}` : null,
        ((_e = (_d = quiz == null ? void 0 : quiz.module) == null ? void 0 : _d.translation) == null ? void 0 : _e.title) ? `Модуль: ${quiz.module.translation.title}` : null,
        ((_g = (_f = quiz == null ? void 0 : quiz.course) == null ? void 0 : _f.translation) == null ? void 0 : _g.title) ? `Курс: ${quiz.course.translation.title}` : null
      ].filter(Boolean).join(" / ");
      return context ? `${idPart} ${titlePart} — ${context}` : `${idPart} ${titlePart}`;
    };
    const selectedQuiz = computed({
      get: () => {
        if (!form.school_quiz_id) {
          return null;
        }
        return quizOptions.value.find(
          (quiz) => Number(quiz.id) === Number(
            form.school_quiz_id
          )
        ) || null;
      },
      set: (value) => {
        form.school_quiz_id = (value == null ? void 0 : value.id) ?? null;
      }
    });
    const questionTypeLabelKeyMap = {
      single_choice: "questionTypeSingleChoice",
      multiple_choice: "questionTypeMultipleChoice",
      true_false: "questionTypeTrueFalse",
      open_text: "questionTypeOpenText"
    };
    const questionTypeOptions = Object.entries(
      questionTypeLabelKeyMap
    ).map(
      ([value, labelKey]) => ({
        value,
        labelKey
      })
    );
    const parseMeta = (metaRaw) => {
      if (!metaRaw || !metaRaw.trim()) {
        return null;
      }
      try {
        return JSON.parse(
          metaRaw
        );
      } catch (error) {
        console.error(
          "Ошибка парсинга meta JSON:",
          error
        );
        return null;
      }
    };
    const submit = () => {
      form.transform(
        (data) => {
          const {
            meta_raw,
            ...rest
          } = data;
          return {
            ...rest,
            /**
             * ID уже является
             * источником истины.
             */
            school_quiz_id: data.school_quiz_id || null,
            activity: rest.activity ? 1 : 0,
            sort: rest.sort === "" || rest.sort === null ? 0 : Number(
              rest.sort
            ),
            points: rest.points === "" || rest.points === null ? 1 : Number(
              rest.points
            ),
            meta: parseMeta(
              meta_raw
            )
          };
        }
      );
      form.post(
        route(
          "admin.schoolQuizQuestions.store"
        ),
        {
          errorBag: "createSchoolQuizQuestion",
          preserveScroll: true,
          onSuccess: () => {
            toast.success(
              "Вопрос квиза успешно создан!"
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка создания вопроса квиза:",
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
        title: unref(t)("createQuizQuestion")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("createQuizQuestion"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("createQuizQuestion")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("createQuizQuestion")), 1)
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
              href: _ctx.route("admin.schoolQuizQuestions.index")
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
                                       c-.2 1.5.9 2.8 1.9 3.8
                                       1.4 1.4 3.1 2 4.9 2
                                       1.8 0 3.6-.7 4.9-2
                                       l2.2 2.2 .8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0\n                                       .7.7 1.2 1.7 1.4 2.7l2-.3\n                                       c-.2-1.5-.9-2.8-1.9-3.8\n                                       C10.1.4 5.7.4 2.9 3.1\n                                       L.7.9 0 7.3l6.4-.7-2.1-2.1z\n                                       M15.6 8.7l-6.4.7 2.1 2.1\n                                       c-1.9 1.9-5.1 1.9-7 0\n                                       -.7-.7-1.2-1.7-1.4-2.7l-2 .3\n                                       c-.2 1.5.9 2.8 1.9 3.8\n                                       1.4 1.4 3.1 2 4.9 2\n                                       1.8 0 3.6-.7 4.9-2\n                                       l2.2 2.2 .8-6.4z" })
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
            _push2(`</div><form class="p-3 w-full"${_scopeId}><div class="pb-12"${_scopeId}><div class="mb-4 flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
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
              for: "points",
              value: unref(t)("points")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "points",
              type: "number",
              min: "0",
              modelValue: unref(form).points,
              "onUpdate:modelValue": ($event) => unref(form).points = $event,
              autocomplete: "points",
              class: "w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.points
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
              min: "0",
              modelValue: unref(form).sort,
              "onUpdate:modelValue": ($event) => unref(form).sort = $event,
              autocomplete: "sort",
              class: "w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-4 flex flex-col items-start w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "school_quiz_id",
              value: unref(t)("quiz"),
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              id: "school_quiz_id",
              modelValue: selectedQuiz.value,
              "onUpdate:modelValue": ($event) => selectedQuiz.value = $event,
              options: quizOptions.value,
              "options-limit": dynamicOptionsLimit.value,
              multiple: false,
              "close-on-select": true,
              "clear-on-select": false,
              "preserve-search": true,
              placeholder: unref(t)("select"),
              "track-by": "id",
              "custom-label": quizOptionLabel,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.school_quiz_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-4 flex flex-col items-end"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "question_type",
              value: unref(t)("questionType")
            }, null, _parent2, _scopeId));
            _push2(`<select id="question_type" class="block w-fit py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${_scopeId}><!--[-->`);
            ssrRenderList(unref(questionTypeOptions), (opt) => {
              _push2(`<option${ssrRenderAttr("value", opt.value)}${ssrIncludeBooleanAttr(Array.isArray(unref(form).question_type) ? ssrLooseContain(unref(form).question_type, opt.value) : ssrLooseEqual(unref(form).question_type, opt.value)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)(opt.labelKey) || opt.value)}</option>`);
            });
            _push2(`<!--]--></select>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.question_type
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
            _push2(ssrRenderComponent(_sfc_main$4, { for: "question_text" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}> * </span> ${ssrInterpolate(unref(t)("question"))} [${ssrInterpolate(activeLocale.value.toUpperCase())}] `);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                    createTextVNode(" " + toDisplayString(unref(t)("question")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "question_text",
              modelValue: currentTranslation.value.question_text,
              "onUpdate:modelValue": ($event) => currentTranslation.value.question_text = $event,
              height: 350
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("question_text")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-4 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "explanation",
              value: `${unref(t)("explanation")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "explanation",
              modelValue: currentTranslation.value.explanation,
              "onUpdate:modelValue": ($event) => currentTranslation.value.explanation = $event,
              height: 250
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("explanation")
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-4 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "meta_raw",
              value: unref(t)("metaJson")
            }, null, _parent2, _scopeId));
            _push2(`<span class="text-xs text-slate-500 dark:text-slate-300 mt-1"${_scopeId}>${ssrInterpolate(unref(t)("metaJsonHint"))}</span></div><textarea id="meta_raw" rows="6" class="mt-1 block w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm rounded-md px-3 py-2 font-mono"${_scopeId}>${ssrInterpolate(unref(form).meta_raw)}</textarea>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.meta
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="flex items-center justify-center gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolQuizQuestions.index")
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
                                           c-.2 1.5.9 2.8 1.9 3.8
                                           1.4 1.4 3.1 2 4.9 2
                                           1.8 0 3.6-.7 4.9-2
                                           l2.2 2.2 .8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0\n                                           .7.7 1.2 1.7 1.4 2.7l2-.3\n                                           c-.2-1.5-.9-2.8-1.9-3.8\n                                           C10.1.4 5.7.4 2.9 3.1\n                                           L.7.9 0 7.3l6.4-.7-2.1-2.1z\n                                           M15.6 8.7l-6.4.7 2.1 2.1\n                                           c-1.9 1.9-5.1 1.9-7 0\n                                           -.7-.7-1.2-1.7-1.4-2.7l-2 .3\n                                           c-.2 1.5.9 2.8 1.9 3.8\n                                           1.4 1.4 3.1 2 4.9 2\n                                           1.8 0 3.6-.7 4.9-2\n                                           l2.2 2.2 .8-6.4z" })
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
              class: ["mb-0", {
                "opacity-25": unref(form).processing
              }],
              disabled: unref(form).processing
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100" viewBox="0 0 16 16"${_scopeId2}><path d="M14.3 2.3L5 11.6 1.7 8.3
                                           c-.4-.4-1-.4-1.4 0
                                           -.4.4-.4 1 0 1.4l4 4
                                           c.2.2.4.3.7.3
                                           .3 0 .5-.1.7-.3l10-10
                                           c.4-.4.4-1 0-1.4
                                           -.4-.4-1-.4-1.4 0z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M14.3 2.3L5 11.6 1.7 8.3\n                                           c-.4-.4-1-.4-1.4 0\n                                           -.4.4-.4 1 0 1.4l4 4\n                                           c.2.2.4.3.7.3\n                                           .3 0 .5-.1.7-.3l10-10\n                                           c.4-.4.4-1 0-1.4\n                                           -.4-.4-1-.4-1.4 0z" })
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
              createVNode("div", { class: "px-4 sm:px-6 lg:px-8 py-8 w-full max-w-6xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-4" }, [
                    createVNode(_sfc_main$1, {
                      href: _ctx.route("admin.schoolQuizQuestions.index")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0\n                                       .7.7 1.2 1.7 1.4 2.7l2-.3\n                                       c-.2-1.5-.9-2.8-1.9-3.8\n                                       C10.1.4 5.7.4 2.9 3.1\n                                       L.7.9 0 7.3l6.4-.7-2.1-2.1z\n                                       M15.6 8.7l-6.4.7 2.1 2.1\n                                       c-1.9 1.9-5.1 1.9-7 0\n                                       -.7-.7-1.2-1.7-1.4-2.7l-2 .3\n                                       c-.2 1.5.9 2.8 1.9 3.8\n                                       1.4 1.4 3.1 2 4.9 2\n                                       1.8 0 3.6-.7 4.9-2\n                                       l2.2 2.2 .8-6.4z" })
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
                    class: "p-3 w-full"
                  }, [
                    createVNode("div", { class: "pb-12" }, [
                      createVNode("div", { class: "mb-4 flex flex-col lg:flex-row gap-4 lg:items-center lg:justify-between" }, [
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
                            for: "points",
                            value: unref(t)("points")
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$5, {
                            id: "points",
                            type: "number",
                            min: "0",
                            modelValue: unref(form).points,
                            "onUpdate:modelValue": ($event) => unref(form).points = $event,
                            autocomplete: "points",
                            class: "w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            message: unref(form).errors.points
                          }, null, 8, ["message"])
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
                            min: "0",
                            modelValue: unref(form).sort,
                            "onUpdate:modelValue": ($event) => unref(form).sort = $event,
                            autocomplete: "sort",
                            class: "w-28"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            message: unref(form).errors.sort
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-4 flex flex-col items-start w-full" }, [
                        createVNode(_sfc_main$4, {
                          for: "school_quiz_id",
                          value: unref(t)("quiz"),
                          class: "mb-1"
                        }, null, 8, ["value"]),
                        createVNode(unref(VueMultiselect), {
                          id: "school_quiz_id",
                          modelValue: selectedQuiz.value,
                          "onUpdate:modelValue": ($event) => selectedQuiz.value = $event,
                          options: quizOptions.value,
                          "options-limit": dynamicOptionsLimit.value,
                          multiple: false,
                          "close-on-select": true,
                          "clear-on-select": false,
                          "preserve-search": true,
                          placeholder: unref(t)("select"),
                          "track-by": "id",
                          "custom-label": quizOptionLabel,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.school_quiz_id
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-4 flex flex-col items-end" }, [
                        createVNode(_sfc_main$4, {
                          for: "question_type",
                          value: unref(t)("questionType")
                        }, null, 8, ["value"]),
                        withDirectives(createVNode("select", {
                          id: "question_type",
                          "onUpdate:modelValue": ($event) => unref(form).question_type = $event,
                          class: "block w-fit py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"
                        }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(unref(questionTypeOptions), (opt) => {
                            return openBlock(), createBlock("option", {
                              key: opt.value,
                              value: opt.value
                            }, toDisplayString(unref(t)(opt.labelKey) || opt.value), 9, ["value"]);
                          }), 128))
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).question_type]
                        ]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.question_type
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
                          createVNode(_sfc_main$4, { for: "question_text" }, {
                            default: withCtx(() => [
                              createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                              createTextVNode(" " + toDisplayString(unref(t)("question")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$8, {
                            id: "question_text",
                            modelValue: currentTranslation.value.question_text,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.question_text = $event,
                            height: 350
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2",
                            message: getError("question_text")
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "mb-4 flex flex-col items-start" }, [
                          createVNode(_sfc_main$4, {
                            for: "explanation",
                            value: `${unref(t)("explanation")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode(_sfc_main$8, {
                            id: "explanation",
                            modelValue: currentTranslation.value.explanation,
                            "onUpdate:modelValue": ($event) => currentTranslation.value.explanation = $event,
                            height: 250
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$6, {
                            class: "mt-2",
                            message: getError("explanation")
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mb-4 flex flex-col items-start" }, [
                        createVNode("div", { class: "flex justify-between w-full" }, [
                          createVNode(_sfc_main$4, {
                            for: "meta_raw",
                            value: unref(t)("metaJson")
                          }, null, 8, ["value"]),
                          createVNode("span", { class: "text-xs text-slate-500 dark:text-slate-300 mt-1" }, toDisplayString(unref(t)("metaJsonHint")), 1)
                        ]),
                        withDirectives(createVNode("textarea", {
                          id: "meta_raw",
                          "onUpdate:modelValue": ($event) => unref(form).meta_raw = $event,
                          rows: "6",
                          class: "mt-1 block w-full border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-sm rounded-md px-3 py-2 font-mono"
                        }, null, 8, ["onUpdate:modelValue"]), [
                          [vModelText, unref(form).meta_raw]
                        ]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.meta
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center gap-3" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.schoolQuizQuestions.index")
                      }, {
                        icon: withCtx(() => [
                          (openBlock(), createBlock("svg", {
                            class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                            viewBox: "0 0 16 16"
                          }, [
                            createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0\n                                           .7.7 1.2 1.7 1.4 2.7l2-.3\n                                           c-.2-1.5-.9-2.8-1.9-3.8\n                                           C10.1.4 5.7.4 2.9 3.1\n                                           L.7.9 0 7.3l6.4-.7-2.1-2.1z\n                                           M15.6 8.7l-6.4.7 2.1 2.1\n                                           c-1.9 1.9-5.1 1.9-7 0\n                                           -.7-.7-1.2-1.7-1.4-2.7l-2 .3\n                                           c-.2 1.5.9 2.8 1.9 3.8\n                                           1.4 1.4 3.1 2 4.9 2\n                                           1.8 0 3.6-.7 4.9-2\n                                           l2.2 2.2 .8-6.4z" })
                          ]))
                        ]),
                        default: withCtx(() => [
                          createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                        ]),
                        _: 1
                      }, 8, ["href"]),
                      createVNode(_sfc_main$9, {
                        class: ["mb-0", {
                          "opacity-25": unref(form).processing
                        }],
                        disabled: unref(form).processing
                      }, {
                        icon: withCtx(() => [
                          (openBlock(), createBlock("svg", {
                            class: "w-4 h-4 fill-current text-slate-100",
                            viewBox: "0 0 16 16"
                          }, [
                            createVNode("path", { d: "M14.3 2.3L5 11.6 1.7 8.3\n                                           c-.4-.4-1-.4-1.4 0\n                                           -.4.4-.4 1 0 1.4l4 4\n                                           c.2.2.4.3.7.3\n                                           .3 0 .5-.1.7-.3l10-10\n                                           c.4-.4.4-1 0-1.4\n                                           -.4-.4-1-.4-1.4 0z" })
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolQuizQuestions/Create.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
