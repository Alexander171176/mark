import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, computed, createTextVNode, withDirectives, Fragment, renderList, vModelSelect } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderList } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$a } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$b, a as _sfc_main$h } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$8 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$c, a as _sfc_main$f, b as _sfc_main$g } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$e } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$d } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$i } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$9 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$6 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$7 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$5 } from "./ActivityToggle-B1-nFMYK.js";
import "./LocaleSelectOption-BeLdazeX.js";
import "./ResponsiveNavLink-gtte0z5g.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "./ScrollButtons-2xyFJfJ4.js";
import "@fortawesome/vue-fontawesome";
import "@fortawesome/fontawesome-svg-core";
import "@fortawesome/free-solid-svg-icons";
import "@inertiajs/inertia";
import "vue-smooth-dnd";
const _sfc_main$4 = {
  __name: "BulkActionSelect",
  __ssrInlineRender: true,
  emits: ["change"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizQuestion/Select/BulkActionSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: String
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} ↑</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} ↓</option><option disabled>─────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────</option><option value="questionTextAsc">${ssrInterpolate(unref(t)("question"))} ↑</option><option value="questionTextDesc">${ssrInterpolate(unref(t)("question"))} ↓</option><option value="quizTitleAsc">${ssrInterpolate(unref(t)("quiz"))} ↑</option><option value="quizTitleDesc">${ssrInterpolate(unref(t)("quiz"))} ↓</option><option disabled>─────────────</option><option value="pointsAsc">${ssrInterpolate(unref(t)("points"))} ↑</option><option value="pointsDesc">${ssrInterpolate(unref(t)("points"))} ↓</option><option value="answersCountAsc">${ssrInterpolate(unref(t)("answers"))} ↑</option><option value="answersCountDesc">${ssrInterpolate(unref(t)("answers"))} ↓</option><option disabled>─────────────</option><option value="attemptItemsCountDesc">${ssrInterpolate(unref(t)("quizAttempts"))} 9→0</option><option value="attemptItemsCountAsc">${ssrInterpolate(unref(t)("quizAttempts"))} 0→9</option><option disabled>─────────────</option><option value="singleChoice">${ssrInterpolate(unref(t)("questionTypeSingleChoice"))}</option><option value="multipleChoice">${ssrInterpolate(unref(t)("questionTypeMultipleChoice"))}</option><option value="trueFalse">${ssrInterpolate(unref(t)("questionTypeTrueFalse"))}</option><option value="openText">${ssrInterpolate(unref(t)("questionTypeOpenText"))}</option><option disabled>─────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizQuestion/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "QuizQuestionTable",
  __ssrInlineRender: true,
  props: {
    questions: {
      type: Array,
      default: () => []
    },
    selectedQuestions: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localQuestions = ref([]);
    watch(
      () => props.questions,
      (newVal) => {
        localQuestions.value = JSON.parse(
          JSON.stringify(
            newVal || []
          )
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      const newOrderIds = localQuestions.value.map(
        (question) => question.id
      );
      emit(
        "update-sort-order",
        newOrderIds
      );
    };
    const getQuestionText = (question) => {
      var _a;
      return ((_a = question == null ? void 0 : question.translation) == null ? void 0 : _a.question_text) || `ID: ${question == null ? void 0 : question.id}`;
    };
    const getQuestionExplanation = (question) => {
      var _a;
      return ((_a = question == null ? void 0 : question.translation) == null ? void 0 : _a.explanation) || "";
    };
    const getQuizTitle = (question) => {
      var _a, _b, _c;
      return ((_b = (_a = question == null ? void 0 : question.quiz) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((_c = question == null ? void 0 : question.quiz) == null ? void 0 : _c.slug) || `#${question == null ? void 0 : question.school_quiz_id}`;
    };
    const stripHtml = (html) => {
      if (!html) {
        return "";
      }
      return html.replace(/<\/p>/gi, "\n").replace(/<br\s*\/?>/gi, "\n").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/&amp;/gi, "&").replace(/&quot;/gi, '"').replace(/&#039;/gi, "'").replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/\s+/g, " ").trim();
    };
    const shortText = (html, length = 120) => {
      const clean = stripHtml(html);
      return clean.length > length ? clean.slice(
        0,
        length
      ) + "…" : clean;
    };
    const getCorrectAnswersSummary = (question) => {
      var _a;
      if (!((_a = question == null ? void 0 : question.answers) == null ? void 0 : _a.length)) {
        return "—";
      }
      const correct = question.answers.filter(
        (answer) => Boolean(
          answer.is_correct
        )
      );
      if (!correct.length) {
        return "—";
      }
      const list = correct.map(
        (answer) => {
          var _a2;
          return ((_a2 = answer == null ? void 0 : answer.translation) == null ? void 0 : _a2.text) || "";
        }
      ).filter(Boolean);
      if (!list.length) {
        return "—";
      }
      const sliced = list.slice(0, 3).join(", ");
      return sliced + (list.length > 3 ? "…" : "");
    };
    const questionTypeLabelKeyMap = {
      single_choice: "questionTypeSingleChoice",
      multiple_choice: "questionTypeMultipleChoice",
      true_false: "questionTypeTrueFalse",
      open_text: "questionTypeOpenText"
    };
    const getQuestionTypeLabel = (type) => {
      if (!type) {
        return "—";
      }
      const key = questionTypeLabelKeyMap[type];
      return key ? t(key) : type;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedQuestions.length)}</div>`);
      if (localQuestions.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localQuestions.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-xs uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px text-center"><svg class="w-4 h-4 opacity-60" viewBox="0 0 24 24"><path d="M12.707 2.293a1 1 0 0 0-1.414 0l-5 5A1 1 0 1 0 7.707 8.707L12 4.414l4.293 4.293a1 1 0 0 0 1.414-1.414l-5-5z"></path><path d="M16.293 15.293 12 19.586l-4.293-4.293a1 1 0 0 0-1.414 1.414l5 5a1 1 0 0 0 1.414 0l5-5a1 1 0 0 0-1.414-1.414z"></path></svg></th><th class="font-medium px-2 py-3 w-px text-center"> ID </th><th class="font-medium px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("quiz"))}</th><th class="font-medium px-2 py-3 whitespace-nowrap text-left">${ssrInterpolate(unref(t)("question"))}</th><th class="font-medium px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("answers"))}</th><th class="font-medium px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("points"))}</th><th class="font-medium px-2 py-3 whitespace-nowrap text-end">${ssrInterpolate(unref(t)("actions"))}</th><th class="px-2 py-3 whitespace-nowrap text-center"><input type="checkbox"></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localQuestions.value,
          "onUpdate:modelValue": ($event) => localQuestions.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: question }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 text-center text-xs whitespace-nowrap"${ssrRenderAttr("title", `sort: ${question.sort ?? "—"}`)}${_scopeId}>${ssrInterpolate(question.id)}</td><td class="px-2 py-3 text-center text-xs text-blue-600 dark:text-blue-300"${ssrRenderAttr("title", getQuizTitle(question))}${_scopeId}>${ssrInterpolate(getQuizTitle(question))}</td><td class="px-2 py-3 text-xs min-w-72"${_scopeId}><div class="font-semibold text-slate-800 dark:text-slate-100"${ssrRenderAttr("title", shortText(
                getQuestionText(question),
                300
              ))}${_scopeId}>${ssrInterpolate(shortText(
                getQuestionText(
                  question
                )
              ))}</div>`);
              if (getQuestionExplanation(
                question
              )) {
                _push2(`<div class="mt-1 text-[10px] text-slate-500 dark:text-slate-300"${ssrRenderAttr("title", shortText(
                  getQuestionExplanation(
                    question
                  ),
                  300
                ))}${_scopeId}>${ssrInterpolate(shortText(
                  getQuestionExplanation(
                    question
                  ),
                  90
                ))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</td><td class="px-2 py-3 text-center text-xs"${_scopeId}><div class="text-fuchsia-700 dark:text-fuchsia-200"${_scopeId}>${ssrInterpolate(getQuestionTypeLabel(
                question.question_type
              ))}</div><div class="text-emerald-600 dark:text-emerald-300"${_scopeId}>${ssrInterpolate(getCorrectAnswersSummary(
                question
              ))}</div></td><td class="px-2 py-3 text-center text-xs text-amber-600 dark:text-amber-300"${_scopeId}>${ssrInterpolate(question.points ?? 0)}</td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: question.activity,
                title: question.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit(
                  "toggle-activity",
                  question
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route(
                  "admin.schoolQuizQuestions.edit",
                  {
                    schoolQuizQuestion: question.id
                  }
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                title: unref(t)("delete"),
                onDelete: ($event) => emit(
                  "delete",
                  question
                )
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(
                __props.selectedQuestions.includes(
                  question.id
                )
              ) ? " checked" : ""}${_scopeId}></td></tr>`);
            } else {
              return [
                createVNode("tr", { class: "text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800" }, [
                  createVNode("td", { class: "px-2 py-1 text-center cursor-move handle" }, [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 text-gray-500 dark:text-gray-300",
                      fill: "currentColor",
                      viewBox: "0 0 20 20"
                    }, [
                      createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                    ]))
                  ]),
                  createVNode("td", {
                    class: "px-2 py-3 text-center text-xs whitespace-nowrap",
                    title: `sort: ${question.sort ?? "—"}`
                  }, toDisplayString(question.id), 9, ["title"]),
                  createVNode("td", {
                    class: "px-2 py-3 text-center text-xs text-blue-600 dark:text-blue-300",
                    title: getQuizTitle(question)
                  }, toDisplayString(getQuizTitle(question)), 9, ["title"]),
                  createVNode("td", { class: "px-2 py-3 text-xs min-w-72" }, [
                    createVNode("div", {
                      class: "font-semibold text-slate-800 dark:text-slate-100",
                      title: shortText(
                        getQuestionText(question),
                        300
                      )
                    }, toDisplayString(shortText(
                      getQuestionText(
                        question
                      )
                    )), 9, ["title"]),
                    getQuestionExplanation(
                      question
                    ) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "mt-1 text-[10px] text-slate-500 dark:text-slate-300",
                      title: shortText(
                        getQuestionExplanation(
                          question
                        ),
                        300
                      )
                    }, toDisplayString(shortText(
                      getQuestionExplanation(
                        question
                      ),
                      90
                    )), 9, ["title"])) : createCommentVNode("", true)
                  ]),
                  createVNode("td", { class: "px-2 py-3 text-center text-xs" }, [
                    createVNode("div", { class: "text-fuchsia-700 dark:text-fuchsia-200" }, toDisplayString(getQuestionTypeLabel(
                      question.question_type
                    )), 1),
                    createVNode("div", { class: "text-emerald-600 dark:text-emerald-300" }, toDisplayString(getCorrectAnswersSummary(
                      question
                    )), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 text-center text-xs text-amber-600 dark:text-amber-300" }, toDisplayString(question.points ?? 0), 1),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: question.activity,
                        title: question.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit(
                          "toggle-activity",
                          question
                        )
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route(
                          "admin.schoolQuizQuestions.edit",
                          {
                            schoolQuizQuestion: question.id
                          }
                        )
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        title: unref(t)("delete"),
                        onDelete: ($event) => emit(
                          "delete",
                          question
                        )
                      }, null, 8, ["title", "onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 text-center" }, [
                    createVNode("input", {
                      type: "checkbox",
                      checked: __props.selectedQuestions.includes(
                        question.id
                      ),
                      onChange: ($event) => emit(
                        "toggle-select",
                        question.id
                      )
                    }, null, 40, ["checked", "onChange"])
                  ])
                ])
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</table>`);
      } else {
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizQuestion/Table/QuizQuestionTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "QuizQuestionCardGrid",
  __ssrInlineRender: true,
  props: {
    questions: {
      type: Array,
      default: () => []
    },
    selectedQuestions: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localQuestions = ref([]);
    watch(
      () => props.questions,
      (newVal) => {
        localQuestions.value = JSON.parse(
          JSON.stringify(
            newVal || []
          )
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      const newOrderIds = localQuestions.value.map(
        (question) => question.id
      );
      emit(
        "update-sort-order",
        newOrderIds
      );
    };
    const getQuestionText = (question) => {
      var _a;
      return ((_a = question == null ? void 0 : question.translation) == null ? void 0 : _a.question_text) || `ID: ${question == null ? void 0 : question.id}`;
    };
    const getQuestionExplanation = (question) => {
      var _a;
      return ((_a = question == null ? void 0 : question.translation) == null ? void 0 : _a.explanation) || "";
    };
    const getQuizTitle = (question) => {
      var _a, _b, _c;
      return ((_b = (_a = question == null ? void 0 : question.quiz) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((_c = question == null ? void 0 : question.quiz) == null ? void 0 : _c.slug) || `Quiz ID: ${question == null ? void 0 : question.school_quiz_id}`;
    };
    const stripHtml = (html) => {
      if (!html) {
        return "";
      }
      return html.replace(/<\/p>/gi, "\n").replace(/<br\s*\/?>/gi, "\n").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/&amp;/gi, "&").replace(/&quot;/gi, '"').replace(/&#039;/gi, "'").replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/\s+/g, " ").trim();
    };
    const shortText = (html, length = 120) => {
      const clean = stripHtml(html);
      return clean.length > length ? clean.slice(
        0,
        length
      ) + "…" : clean;
    };
    const getCorrectAnswersSummary = (question) => {
      var _a;
      if (!((_a = question == null ? void 0 : question.answers) == null ? void 0 : _a.length)) {
        return "—";
      }
      const correct = question.answers.filter(
        (answer) => Boolean(
          answer.is_correct
        )
      );
      if (!correct.length) {
        return "—";
      }
      const list = correct.map(
        (answer) => {
          var _a2;
          return ((_a2 = answer == null ? void 0 : answer.translation) == null ? void 0 : _a2.text) || "";
        }
      ).filter(Boolean);
      if (!list.length) {
        return "—";
      }
      const sliced = list.slice(0, 3).join("; ");
      return sliced + (list.length > 3 ? "…" : "");
    };
    const questionTypeLabelKeyMap = {
      single_choice: "questionTypeSingleChoice",
      multiple_choice: "questionTypeMultipleChoice",
      true_false: "questionTypeTrueFalse",
      open_text: "questionTypeOpenText"
    };
    const getQuestionTypeLabel = (type) => {
      if (!type) {
        return "—";
      }
      const key = questionTypeLabelKeyMap[type];
      return key ? t(key) : type;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedQuestions.length)}</div>`);
      if (localQuestions.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localQuestions.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localQuestions.value,
          "onUpdate:modelValue": ($event) => localQuestions.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".drag-handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: question }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<article class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-2 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `sort: ${question.sort ?? "—"}`)}${_scopeId}> ID: ${ssrInterpolate(question.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}><span class="text-[10px] px-1.5 py-0.5 rounded-sm font-semibold border border-gray-400 bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300"${ssrRenderAttr("title", unref(t)("points"))}${_scopeId}>${ssrInterpolate(unref(t)("points"))}: ${ssrInterpolate(question.points ?? 0)}</span><input type="checkbox"${ssrIncludeBooleanAttr(
                __props.selectedQuestions.includes(
                  question.id
                )
              ) ? " checked" : ""}${_scopeId}></div></header><div class="flex flex-col flex-1 px-3 py-2 space-y-2 text-[11px]"${_scopeId}><div class="text-[11px] text-center font-semibold text-blue-600 dark:text-blue-300"${ssrRenderAttr(
                "title",
                getQuizTitle(
                  question
                )
              )}${_scopeId}>${ssrInterpolate(getQuizTitle(
                question
              ))}</div><div class="text-xs text-slate-800 dark:text-slate-100 font-semibold border border-dashed border-slate-400 bg-slate-100/70 dark:bg-slate-900/40 rounded-sm px-2 py-1 min-h-[3rem]"${ssrRenderAttr(
                "title",
                shortText(
                  getQuestionText(
                    question
                  ),
                  300
                )
              )}${_scopeId}>${ssrInterpolate(shortText(
                getQuestionText(
                  question
                )
              ))}</div>`);
              if (getQuestionExplanation(
                question
              )) {
                _push2(`<div class="text-[10px] text-slate-600 dark:text-slate-300 font-semibold"${ssrRenderAttr(
                  "title",
                  shortText(
                    getQuestionExplanation(
                      question
                    ),
                    300
                  )
                )}${_scopeId}>${ssrInterpolate(shortText(
                  getQuestionExplanation(
                    question
                  ),
                  90
                ))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="text-center text-[10px] px-2 py-0.5 rounded-sm font-semibold border border-slate-400 bg-fuchsia-50 dark:bg-fuchsia-900/40 text-fuchsia-700 dark:text-fuchsia-200"${_scopeId}>${ssrInterpolate(getQuestionTypeLabel(
                question.question_type
              ))}</div><div class="flex flex-wrap justify-between gap-2 mt-1 text-[10px]"${_scopeId}><span class="text-[10px] px-2 py-0.5 rounded-sm border border-slate-400 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-200"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("answers"))}: </span><span class="font-semibold"${_scopeId}>${ssrInterpolate(getCorrectAnswersSummary(
                question
              ))}</span></span></div><div class="grid grid-cols-2 gap-1 text-[10px] text-center"${_scopeId}><span class="border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1"${_scopeId}>${ssrInterpolate(unref(t)("answers"))}: ${ssrInterpolate(question.answers_count ?? 0)}</span><span class="border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1"${_scopeId}>${ssrInterpolate(unref(t)("attemptsLimit"))}: ${ssrInterpolate(question.attempt_items_count ?? 0)}</span></div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: question.activity,
                title: question.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit(
                  "toggle-activity",
                  question
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route(
                  "admin.schoolQuizQuestions.edit",
                  {
                    schoolQuizQuestion: question.id
                  }
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                title: unref(t)("delete"),
                onDelete: ($event) => emit(
                  "delete",
                  question
                )
              }, null, _parent2, _scopeId));
              _push2(`</div></footer></article>`);
            } else {
              return [
                createVNode("article", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
                  createVNode("header", { class: "flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100",
                        title: unref(t)("dragDrop")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4",
                          fill: "currentColor",
                          viewBox: "0 0 20 20"
                        }, [
                          createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                        ]))
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "text-[10px] font-semibold px-2 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100",
                        title: `sort: ${question.sort ?? "—"}`
                      }, " ID: " + toDisplayString(question.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("span", {
                        class: "text-[10px] px-1.5 py-0.5 rounded-sm font-semibold border border-gray-400 bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-300",
                        title: unref(t)("points")
                      }, toDisplayString(unref(t)("points")) + ": " + toDisplayString(question.points ?? 0), 9, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedQuestions.includes(
                          question.id
                        ),
                        onChange: ($event) => emit(
                          "toggle-select",
                          question.id
                        )
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-2 text-[11px]" }, [
                    createVNode("div", {
                      class: "text-[11px] text-center font-semibold text-blue-600 dark:text-blue-300",
                      title: getQuizTitle(
                        question
                      )
                    }, toDisplayString(getQuizTitle(
                      question
                    )), 9, ["title"]),
                    createVNode("div", {
                      class: "text-xs text-slate-800 dark:text-slate-100 font-semibold border border-dashed border-slate-400 bg-slate-100/70 dark:bg-slate-900/40 rounded-sm px-2 py-1 min-h-[3rem]",
                      title: shortText(
                        getQuestionText(
                          question
                        ),
                        300
                      )
                    }, toDisplayString(shortText(
                      getQuestionText(
                        question
                      )
                    )), 9, ["title"]),
                    getQuestionExplanation(
                      question
                    ) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-[10px] text-slate-600 dark:text-slate-300 font-semibold",
                      title: shortText(
                        getQuestionExplanation(
                          question
                        ),
                        300
                      )
                    }, toDisplayString(shortText(
                      getQuestionExplanation(
                        question
                      ),
                      90
                    )), 9, ["title"])) : createCommentVNode("", true),
                    createVNode("div", { class: "text-center text-[10px] px-2 py-0.5 rounded-sm font-semibold border border-slate-400 bg-fuchsia-50 dark:bg-fuchsia-900/40 text-fuchsia-700 dark:text-fuchsia-200" }, toDisplayString(getQuestionTypeLabel(
                      question.question_type
                    )), 1),
                    createVNode("div", { class: "flex flex-wrap justify-between gap-2 mt-1 text-[10px]" }, [
                      createVNode("span", { class: "text-[10px] px-2 py-0.5 rounded-sm border border-slate-400 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-200" }, [
                        createVNode("span", null, toDisplayString(unref(t)("answers")) + ": ", 1),
                        createVNode("span", { class: "font-semibold" }, toDisplayString(getCorrectAnswersSummary(
                          question
                        )), 1)
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-2 gap-1 text-[10px] text-center" }, [
                      createVNode("span", { class: "border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1" }, toDisplayString(unref(t)("answers")) + ": " + toDisplayString(question.answers_count ?? 0), 1),
                      createVNode("span", { class: "border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1" }, toDisplayString(unref(t)("attemptsLimit")) + ": " + toDisplayString(question.attempt_items_count ?? 0), 1)
                    ])
                  ]),
                  createVNode("footer", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: question.activity,
                        title: question.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit(
                          "toggle-activity",
                          question
                        )
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route(
                          "admin.schoolQuizQuestions.edit",
                          {
                            schoolQuizQuestion: question.id
                          }
                        )
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        title: unref(t)("delete"),
                        onDelete: ($event) => emit(
                          "delete",
                          question
                        )
                      }, null, 8, ["title", "onDelete"])
                    ])
                  ])
                ])
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div>`);
      } else {
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizQuestion/View/QuizQuestionCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminSchoolQuizQuestionsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    questions: { type: [Array, Object], default: () => [] },
    questionsCount: { type: Number, default: 0 },
    adminSchoolQuizQuestionsPerPage: { type: Number, default: 10 },
    adminSchoolQuizQuestionsDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    filters: { type: Object, default: () => ({}) },
    quizzes: { type: Array, default: () => [] },
    currentQuizId: { type: Number, default: null },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    var _a;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(localStorage.getItem("admin_view_mode_quiz_questions") || "table");
    watch(viewMode, (val) => {
      localStorage.setItem("admin_view_mode_quiz_questions", val);
    });
    const questionsList = computed(() => {
      var _a2;
      if (Array.isArray(props.questions)) return props.questions;
      if (Array.isArray((_a2 = props.questions) == null ? void 0 : _a2.data)) return props.questions.data;
      return [];
    });
    const localQuestions = ref([]);
    watch(
      questionsList,
      (newVal) => {
        localQuestions.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const selectedQuizId = ref(props.currentQuizId ?? ((_a = props.filters) == null ? void 0 : _a.school_quiz_id) ?? null);
    const quizOptions = computed(() => props.quizzes || []);
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
    const handleQuizFilterChange = () => {
      const params = {
        ...Object.fromEntries(new URLSearchParams(window.location.search)),
        page: void 0
      };
      if (selectedQuizId.value) {
        params.school_quiz_id = selectedQuizId.value;
      } else {
        delete params.school_quiz_id;
      }
      router.get(route("admin.schoolQuizQuestions.index"), params, {
        preserveScroll: true,
        preserveState: false,
        replace: true
      });
    };
    const itemsPerPage = ref(props.adminSchoolQuizQuestionsPerPage || 10);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountSchoolQuizQuestions"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminSchoolQuizQuestionsDefaultSort || "idDesc"
    );
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortSchoolQuizQuestions"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing) {
              router.get(
                window.location.pathname,
                {
                  ...Object.fromEntries(new URLSearchParams(window.location.search)),
                  sort: newVal || void 0,
                  page: void 0
                },
                {
                  preserveScroll: true,
                  preserveState: false,
                  replace: true
                }
              );
            }
            toast.info("Сортировка успешно изменена");
          },
          onError: (errors) => toast.error(errors.value || "Ошибка обновления сортировки.")
        }
      );
    });
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const normalize = (value) => (value ?? "").toString().trim().toLowerCase();
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(value || 0).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const getQuestionText = (question) => {
      var _a2;
      return ((_a2 = question == null ? void 0 : question.translation) == null ? void 0 : _a2.question_text) || `ID: ${question == null ? void 0 : question.id}`;
    };
    const getQuestionExplanation = (question) => {
      var _a2;
      return ((_a2 = question == null ? void 0 : question.translation) == null ? void 0 : _a2.explanation) || "";
    };
    const getNestedTitle = (item) => {
      var _a2, _b;
      return ((_a2 = item == null ? void 0 : item.translation) == null ? void 0 : _a2.title) || ((_b = item == null ? void 0 : item.translation) == null ? void 0 : _b.name) || "";
    };
    const getQuizTitle = (question) => {
      return getNestedTitle(
        question == null ? void 0 : question.quiz
      );
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringAsc = (field) => (a, b) => normalize(a == null ? void 0 : a[field]).localeCompare(normalize(b == null ? void 0 : b[field]), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byStringDesc = (field) => (a, b) => normalize(b == null ? void 0 : b[field]).localeCompare(normalize(a == null ? void 0 : a[field]), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortQuestions = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") return list.filter((item) => !!item.activity);
      if (sortParam.value === "inactive") return list.filter((item) => !item.activity);
      if (sortParam.value === "singleChoice") {
        return list.filter((item) => item.question_type === "single_choice");
      }
      if (sortParam.value === "multipleChoice") {
        return list.filter((item) => item.question_type === "multiple_choice");
      }
      if (sortParam.value === "trueFalse") {
        return list.filter((item) => item.question_type === "true_false");
      }
      if (sortParam.value === "openText") {
        return list.filter((item) => item.question_type === "open_text");
      }
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        questionTextAsc: (a, b) => normalize(getQuestionText(a)).localeCompare(normalize(getQuestionText(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        questionTextDesc: (a, b) => normalize(getQuestionText(b)).localeCompare(normalize(getQuestionText(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        quizTitleAsc: (a, b) => normalize(getQuizTitle(a)).localeCompare(normalize(getQuizTitle(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        quizTitleDesc: (a, b) => normalize(getQuizTitle(b)).localeCompare(normalize(getQuizTitle(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        questionTypeAsc: byStringAsc("question_type"),
        questionTypeDesc: byStringDesc("question_type"),
        pointsAsc: byNumberAsc("points"),
        pointsDesc: byNumberDesc("points"),
        answersCountAsc: byNumberAsc("answers_count"),
        answersCountDesc: byNumberDesc("answers_count"),
        attemptItemsCountAsc: byNumberAsc("attempt_items_count"),
        attemptItemsCountDesc: byNumberDesc("attempt_items_count"),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        createdAtAsc: byDateAsc("created_at"),
        createdAtDesc: byDateDesc("created_at"),
        updatedAtAsc: byDateAsc("updated_at"),
        updatedAtDesc: byDateDesc("updated_at")
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredQuestions = computed(() => {
      let filtered = localQuestions.value || [];
      const query = normalize(searchQuery.value);
      if (selectedQuizId.value && !props.useServerProcessing) {
        filtered = filtered.filter(
          (question) => Number(question.school_quiz_id) === Number(selectedQuizId.value)
        );
      }
      if (!query) {
        return sortQuestions(filtered);
      }
      filtered = filtered.filter((question) => {
        var _a2;
        const values = [
          getQuestionText(question),
          getQuestionExplanation(question),
          question == null ? void 0 : question.question_type,
          getQuizTitle(question),
          (_a2 = question == null ? void 0 : question.quiz) == null ? void 0 : _a2.slug
        ];
        return values.some((value) => normalize(value).includes(query));
      });
      return sortQuestions(filtered);
    });
    const paginatedQuestions = computed(() => {
      const per = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * per;
      return filteredQuestions.value.slice(start, start + per);
    });
    const displayedQuestions = computed(() => {
      return props.useServerProcessing ? questionsList.value : paginatedQuestions.value;
    });
    watch([itemsPerPage, searchQuery, selectedQuizId], () => {
      currentPage.value = 1;
    });
    const showConfirmDeleteModal = ref(false);
    const questionToDeleteId = ref(null);
    const questionToDeleteTitle = ref("");
    const confirmDelete = (questionOrId, title = null) => {
      if (typeof questionOrId === "object") {
        questionToDeleteId.value = questionOrId.id;
        questionToDeleteTitle.value = title || getQuestionText(questionOrId);
      } else {
        questionToDeleteId.value = questionOrId;
        questionToDeleteTitle.value = title || `ID: ${questionOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      questionToDeleteId.value = null;
      questionToDeleteTitle.value = "";
    };
    const deleteQuestion = () => {
      if (questionToDeleteId.value === null) return;
      const idToDelete = questionToDeleteId.value;
      const titleToDelete = questionToDeleteTitle.value;
      router.delete(route("admin.schoolQuizQuestions.destroy", {
        schoolQuizQuestion: idToDelete
      }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          toast.success(`Вопрос "${titleToDelete || "ID: " + idToDelete}" удалён.`);
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Вопрос: ${titleToDelete || "ID: " + idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    const patchQuestion = (questionId, payload) => {
      const index = localQuestions.value.findIndex((question) => question.id === questionId);
      if (index !== -1) {
        localQuestions.value[index] = {
          ...localQuestions.value[index],
          ...payload
        };
      }
    };
    const selectedQuestions = ref([]);
    const toggleAll = (payload) => {
      var _a2;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a2 = payload == null ? void 0 : payload.target) == null ? void 0 : _a2.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedQuestions.value.map((question) => question.id);
      if (checked) {
        selectedQuestions.value = [.../* @__PURE__ */ new Set([...selectedQuestions.value, ...ids])];
      } else {
        selectedQuestions.value = selectedQuestions.value.filter((id) => !ids.includes(id));
      }
    };
    const toggleSelectQuestion = (id) => {
      const index = selectedQuestions.value.indexOf(id);
      if (index > -1) {
        selectedQuestions.value.splice(index, 1);
      } else {
        selectedQuestions.value.push(id);
      }
    };
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (currentPage.value - 1) * itemsPerPage.value;
      const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1
      }));
      if (!items.length) return;
      router.put(route("admin.actions.schoolQuizQuestions.updateSortBulk"), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success("Порядок вопросов успешно обновлён."),
        onError: (errors) => {
          console.error("Ошибка обновления сортировки вопросов:", errors);
          toast.error((errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок вопросов.");
          router.reload({
            only: ["questions"],
            preserveScroll: true
          });
        }
      });
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedQuestions.value.length) {
        toast.warning("Выберите вопросы для активации/деактивации.");
        return;
      }
      const idsToUpdate = [...selectedQuestions.value];
      router.put(route("admin.actions.schoolQuizQuestions.bulkUpdateActivity"), {
        ids: idsToUpdate,
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          idsToUpdate.forEach((id) => patchQuestion(id, { activity: newActivity }));
          selectedQuestions.value = [];
          toast.success("Активность выбранных вопросов обновлена.");
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности.");
        }
      });
    };
    const bulkDelete = () => {
      if (!selectedQuestions.value.length) {
        toast.warning("Выберите хотя бы один вопрос для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные вопросы?")) return;
      router.delete(route("admin.actions.schoolQuizQuestions.bulkDestroy"), {
        data: {
          ids: selectedQuestions.value,
          ...selectedQuizId.value ? { school_quiz_id: selectedQuizId.value } : {}
        },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          selectedQuestions.value = [];
          toast.success("Массовое удаление вопросов успешно завершено.");
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          toast.error(errors[errorKey] || "Произошла ошибка при удалении вопросов.");
        }
      });
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") {
        toggleAll({ target: { checked: true } });
      } else if (action === "deselectAll") {
        toggleAll({ target: { checked: false } });
      } else if (action === "activate") {
        bulkToggleActivity(true);
      } else if (action === "deactivate") {
        bulkToggleActivity(false);
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const toggleActivity = (question) => {
      const newActivity = !question.activity;
      const questionTitle = getQuestionText(question);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(route("admin.actions.schoolQuizQuestions.updateActivity", {
        schoolQuizQuestion: question.id
      }), {
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          patchQuestion(question.id, { activity: newActivity });
          question.activity = newActivity;
          toast.success(`Вопрос "${questionTitle}" ${actionText}.`);
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для вопроса "${questionTitle}".`);
        }
      });
    };
    const cloneQuestion = (question) => {
      router.post(route("admin.actions.schoolQuizQuestions.clone", {
        schoolQuizQuestion: question.id
      }), {}, {
        preserveScroll: true,
        onSuccess: () => toast.success("Вопрос успешно клонирован."),
        onError: () => toast.error("Ошибка при клонировании вопроса.")
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("quizQuestions")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("quizQuestions"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("quizQuestions")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("quizQuestions")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              href: _ctx.route("admin.schoolQuizQuestions.create", {
                ...selectedQuizId.value ? { school_quiz_id: selectedQuizId.value } : {}
              })
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addQuizQuestion"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addQuizQuestion")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              "setting-key": "adminSchoolQuizQuestionsProcessingMode",
              mode: __props.adminSchoolQuizQuestionsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.questionsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.questionsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.questionsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.quizzes.length) {
              _push2(`<div class="flex items-center justify-center gap-2 my-3"${_scopeId}><label for="school_quiz_id" class="text-sm font-semibold text-slate-700 dark:text-slate-200"${_scopeId}>${ssrInterpolate(unref(t)("quiz"))}</label><select id="school_quiz_id" class="w-full rounded-sm border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-xs font-semibold px-3 py-1"${_scopeId}><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray(selectedQuizId.value) ? ssrLooseContain(selectedQuizId.value, null) : ssrLooseEqual(selectedQuizId.value, null)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("allQuizzes"))}</option><!--[-->`);
              ssrRenderList(quizOptions.value, (quiz) => {
                _push2(`<option${ssrRenderAttr("value", quiz.id)}${ssrIncludeBooleanAttr(Array.isArray(selectedQuizId.value) ? ssrLooseContain(selectedQuizId.value, quiz.id) : ssrLooseEqual(selectedQuizId.value, quiz.id)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(quizOptionLabel(quiz))}</option>`);
              });
              _push2(`<!--]--></select></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.questionsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolQuizQuestions"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$3, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (val) => sortParam.value = val
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.questionsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.questionsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.questionsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$f, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.questionsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredQuestions.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.questions }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                questions: displayedQuestions.value,
                "selected-questions": selectedQuestions.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectQuestion,
                onToggleAll: toggleAll,
                onClone: cloneQuestion
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                questions: displayedQuestions.value,
                "selected-questions": selectedQuestions.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectQuestion,
                onToggleAll: toggleAll,
                onClone: cloneQuestion
              }, null, _parent2, _scopeId));
            }
            if (__props.questionsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredQuestions.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.questions }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$i, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteQuestion,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$8, {
                      href: _ctx.route("admin.schoolQuizQuestions.create", {
                        ...selectedQuizId.value ? { school_quiz_id: selectedQuizId.value } : {}
                      })
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addQuizQuestion")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$9, {
                      "setting-key": "adminSchoolQuizQuestionsProcessingMode",
                      mode: __props.adminSchoolQuizQuestionsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.questionsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.questionsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.questionsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.quizzes.length ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex items-center justify-center gap-2 my-3"
                  }, [
                    createVNode("label", {
                      for: "school_quiz_id",
                      class: "text-sm font-semibold text-slate-700 dark:text-slate-200"
                    }, toDisplayString(unref(t)("quiz")), 1),
                    withDirectives(createVNode("select", {
                      id: "school_quiz_id",
                      "onUpdate:modelValue": ($event) => selectedQuizId.value = $event,
                      onChange: handleQuizFilterChange,
                      class: "w-full rounded-sm border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 text-xs font-semibold px-3 py-1"
                    }, [
                      createVNode("option", { value: null }, toDisplayString(unref(t)("allQuizzes")), 1),
                      (openBlock(true), createBlock(Fragment, null, renderList(quizOptions.value, (quiz) => {
                        return openBlock(), createBlock("option", {
                          key: quiz.id,
                          value: quiz.id
                        }, toDisplayString(quizOptionLabel(quiz)), 9, ["value"]);
                      }), 128))
                    ], 40, ["onUpdate:modelValue"]), [
                      [
                        vModelSelect,
                        selectedQuizId.value,
                        void 0,
                        { number: true }
                      ]
                    ])
                  ])) : createCommentVNode("", true),
                  __props.questionsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$d, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolQuizQuestions"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.questionsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.questionsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.questionsCount ? (openBlock(), createBlock("div", {
                    key: 5,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredQuestions.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.questions
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    questions: displayedQuestions.value,
                    "selected-questions": selectedQuestions.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectQuestion,
                    onToggleAll: toggleAll,
                    onClone: cloneQuestion
                  }, null, 8, ["questions", "selected-questions"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 7,
                    questions: displayedQuestions.value,
                    "selected-questions": selectedQuestions.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectQuestion,
                    onToggleAll: toggleAll,
                    onClone: cloneQuestion
                  }, null, 8, ["questions", "selected-questions"])),
                  __props.questionsCount ? (openBlock(), createBlock("div", {
                    key: 8,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredQuestions.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.questions
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteQuestion,
                cancelText: unref(t)("cancel"),
                confirmText: unref(t)("yesDelete"),
                onClose: closeModal
              }, null, 8, ["show", "cancelText", "confirmText"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolQuizQuestions/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
