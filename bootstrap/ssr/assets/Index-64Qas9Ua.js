import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, computed, createTextVNode, withDirectives, Fragment, renderList, vModelSelect } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrRenderClass, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderList } from "vue/server-renderer";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizAnswer/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-44 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>─────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>─────────────</option><option value="textAsc">${ssrInterpolate(unref(t)("answer"))} A→Z</option><option value="textDesc">${ssrInterpolate(unref(t)("answer"))} Z→A</option><option value="quizTitleAsc">${ssrInterpolate(unref(t)("quiz"))} A→Z</option><option value="quizTitleDesc">${ssrInterpolate(unref(t)("quiz"))} Z→A</option><option value="questionTextAsc">${ssrInterpolate(unref(t)("question"))} A→Z</option><option value="questionTextDesc">${ssrInterpolate(unref(t)("question"))} Z→A</option><option disabled>─────────────</option><option value="weightDesc">${ssrInterpolate(unref(t)("weight"))} 9→0</option><option value="weightAsc">${ssrInterpolate(unref(t)("weight"))} 0→9</option><option value="attemptItemsDesc">${ssrInterpolate(unref(t)("quizAttempts"))} 9→0</option><option value="attemptItemsAsc">${ssrInterpolate(unref(t)("quizAttempts"))} 0→9</option><option disabled>─────────────</option><option value="correct">${ssrInterpolate(unref(t)("isCorrect"))}</option><option value="incorrect">${ssrInterpolate(unref(t)("incorrect"))}</option><option value="correctDesc">${ssrInterpolate(unref(t)("isCorrect"))} ON→OFF</option><option value="correctAsc">${ssrInterpolate(unref(t)("isCorrect"))} OFF→ON</option><option disabled>─────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizAnswer/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "QuizAnswerTable",
  __ssrInlineRender: true,
  props: {
    answers: {
      type: Array,
      default: () => []
    },
    selectedAnswers: {
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
    const localAnswers = ref([]);
    watch(
      () => props.answers,
      (newValue) => {
        localAnswers.value = JSON.parse(
          JSON.stringify(newValue || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localAnswers.value.map(
          (answer) => answer.id
        )
      );
    };
    const stripHtml = (html) => {
      if (!html) {
        return "";
      }
      return String(html).replace(/<\/p>/gi, " ").replace(/<br\s*\/?>/gi, " ").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/&amp;/gi, "&").replace(/&quot;/gi, '"').replace(/&#039;/gi, "'").replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/\s+/g, " ").trim();
    };
    const shortText = (html, length = 120) => {
      const clean = stripHtml(html);
      return clean.length > length ? `${clean.slice(0, length)}…` : clean;
    };
    const getAnswerText = (answer) => {
      var _a;
      return ((_a = answer == null ? void 0 : answer.translation) == null ? void 0 : _a.text) || "";
    };
    const getQuizTitle = (answer) => {
      var _a, _b;
      return ((_b = (_a = answer == null ? void 0 : answer.quiz) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getQuestionText = (answer) => {
      var _a, _b;
      return ((_b = (_a = answer == null ? void 0 : answer.question) == null ? void 0 : _a.translation) == null ? void 0 : _b.question_text) || "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedAnswers.length)}</div>`);
      if (localAnswers.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localAnswers.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-xs uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px text-center"><svg class="w-4 h-4 opacity-60" viewBox="0 0 24 24"><path d="M12.707 2.293a1 1 0 0 0-1.414 0l-5 5A1 1 0 1 0 7.707 8.707L12 4.414l4.293 4.293a1 1 0 0 0 1.414-1.414l-5-5z"></path><path d="M16.293 15.293 12 19.586l-4.293-4.293a1 1 0 0 0-1.414 1.414l5 5a1 1 0 0 0 1.414 0l5-5a1 1 0 0 0-1.414-1.414z"></path></svg></th><th class="font-medium px-2 py-3 w-px text-center"> ID </th><th class="font-medium px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("quiz"))}</th><th class="font-medium px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("quizQuestion"))}</th><th class="font-medium px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("answer"))}</th><th class="font-medium px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("isCorrect"))}</th><th class="font-medium px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("points"))}</th><th class="font-medium px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("quizAttempts"))}</th><th class="font-medium px-2 py-3 whitespace-nowrap text-end">${ssrInterpolate(unref(t)("actions"))}</th><th class="px-2 py-3 whitespace-nowrap text-center"><input type="checkbox"></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localAnswers.value,
          "onUpdate:modelValue": ($event) => localAnswers.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: answer }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 text-center text-xs text-indigo-800 dark:text-indigo-200"${ssrRenderAttr("title", `sort: ${answer.sort ?? "—"}`)}${_scopeId}>${ssrInterpolate(answer.id)}</td><td class="px-2 py-3 text-center text-xs text-blue-600 dark:text-blue-300"${ssrRenderAttr("title", getQuizTitle(answer) || `Quiz ID: ${answer.school_quiz_id}`)}${_scopeId}>${ssrInterpolate(getQuizTitle(answer) || `Quiz ID: ${answer.school_quiz_id}`)}</td><td class="px-2 py-3 text-xs text-center font-semibold"${ssrRenderAttr("title", stripHtml(getQuestionText(answer)))}${_scopeId}>${ssrInterpolate(shortText(
                getQuestionText(answer)
              ) || `ID: ${answer.school_quiz_question_id}`)}</td><td class="px-2 py-3 text-xs text-slate-500 dark:text-slate-200 text-center"${ssrRenderAttr("title", stripHtml(getAnswerText(answer)))}${_scopeId}>${ssrInterpolate(shortText(
                getAnswerText(answer)
              ) || `ID: ${answer.id}`)}</td><td class="px-2 py-3 text-center text-xs"${_scopeId}><span class="${ssrRenderClass([
                "px-2 pb-0.5 rounded-sm text-[12px] font-semibold border-2 border-gray-300 dark:border-gray-400",
                answer.is_correct ? "bg-emerald-100 text-emerald-700 dark:bg-emerald-700/40 dark:text-emerald-100" : "bg-rose-100 text-rose-700 dark:bg-rose-700/40 dark:text-rose-100"
              ])}"${_scopeId}>${ssrInterpolate(answer.is_correct ? unref(t)("yes") : unref(t)("no"))}</span></td><td class="px-2 py-3 text-center text-xs text-amber-600 dark:text-amber-300"${_scopeId}>${ssrInterpolate(answer.weight ?? 0)}</td><td class="px-2 py-3 text-center text-xs text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(answer.attempt_items_count ?? 0)}</td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: answer.activity,
                title: answer.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", answer)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolQuizAnswers.edit", {
                  schoolQuizAnswer: answer.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                title: unref(t)("delete"),
                onDelete: ($event) => emit("delete", answer)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedAnswers.includes(answer.id)) ? " checked" : ""}${_scopeId}></td></tr>`);
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
                    class: "px-2 py-3 text-center text-xs text-indigo-800 dark:text-indigo-200",
                    title: `sort: ${answer.sort ?? "—"}`
                  }, toDisplayString(answer.id), 9, ["title"]),
                  createVNode("td", {
                    class: "px-2 py-3 text-center text-xs text-blue-600 dark:text-blue-300",
                    title: getQuizTitle(answer) || `Quiz ID: ${answer.school_quiz_id}`
                  }, toDisplayString(getQuizTitle(answer) || `Quiz ID: ${answer.school_quiz_id}`), 9, ["title"]),
                  createVNode("td", {
                    class: "px-2 py-3 text-xs text-center font-semibold",
                    title: stripHtml(getQuestionText(answer))
                  }, toDisplayString(shortText(
                    getQuestionText(answer)
                  ) || `ID: ${answer.school_quiz_question_id}`), 9, ["title"]),
                  createVNode("td", {
                    class: "px-2 py-3 text-xs text-slate-500 dark:text-slate-200 text-center",
                    title: stripHtml(getAnswerText(answer))
                  }, toDisplayString(shortText(
                    getAnswerText(answer)
                  ) || `ID: ${answer.id}`), 9, ["title"]),
                  createVNode("td", { class: "px-2 py-3 text-center text-xs" }, [
                    createVNode("span", {
                      class: [
                        "px-2 pb-0.5 rounded-sm text-[12px] font-semibold border-2 border-gray-300 dark:border-gray-400",
                        answer.is_correct ? "bg-emerald-100 text-emerald-700 dark:bg-emerald-700/40 dark:text-emerald-100" : "bg-rose-100 text-rose-700 dark:bg-rose-700/40 dark:text-rose-100"
                      ]
                    }, toDisplayString(answer.is_correct ? unref(t)("yes") : unref(t)("no")), 3)
                  ]),
                  createVNode("td", { class: "px-2 py-3 text-center text-xs text-amber-600 dark:text-amber-300" }, toDisplayString(answer.weight ?? 0), 1),
                  createVNode("td", { class: "px-2 py-3 text-center text-xs text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(answer.attempt_items_count ?? 0), 1),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: answer.activity,
                        title: answer.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", answer)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolQuizAnswers.edit", {
                          schoolQuizAnswer: answer.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        title: unref(t)("delete"),
                        onDelete: ($event) => emit("delete", answer)
                      }, null, 8, ["title", "onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 text-center" }, [
                    createVNode("input", {
                      type: "checkbox",
                      checked: __props.selectedAnswers.includes(answer.id),
                      onChange: ($event) => emit("toggle-select", answer.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizAnswer/Table/QuizAnswerTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "QuizAnswerCardGrid",
  __ssrInlineRender: true,
  props: {
    answers: {
      type: Array,
      default: () => []
    },
    selectedAnswers: {
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
    const localAnswers = ref([]);
    watch(
      () => props.answers,
      (newValue) => {
        localAnswers.value = JSON.parse(
          JSON.stringify(newValue || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localAnswers.value.map(
          (answer) => answer.id
        )
      );
    };
    const stripHtml = (html) => {
      if (!html) {
        return "";
      }
      return String(html).replace(/<\/p>/gi, " ").replace(/<br\s*\/?>/gi, " ").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/&amp;/gi, "&").replace(/&quot;/gi, '"').replace(/&#039;/gi, "'").replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/\s+/g, " ").trim();
    };
    const shortText = (html, length = 120) => {
      const clean = stripHtml(html);
      return clean.length > length ? `${clean.slice(0, length)}…` : clean;
    };
    const getAnswerText = (answer) => {
      var _a;
      return ((_a = answer == null ? void 0 : answer.translation) == null ? void 0 : _a.text) || "";
    };
    const getAnswerExplanation = (answer) => {
      var _a;
      return ((_a = answer == null ? void 0 : answer.translation) == null ? void 0 : _a.explanation) || "";
    };
    const getQuizTitle = (answer) => {
      var _a, _b;
      return ((_b = (_a = answer == null ? void 0 : answer.quiz) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || "";
    };
    const getQuestionText = (answer) => {
      var _a, _b;
      return ((_b = (_a = answer == null ? void 0 : answer.question) == null ? void 0 : _a.translation) == null ? void 0 : _b.question_text) || "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedAnswers.length)}</div>`);
      if (localAnswers.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localAnswers.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localAnswers.value,
          "onUpdate:modelValue": ($event) => localAnswers.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".drag-handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: answer }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<article class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-2 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `sort: ${answer.sort ?? "—"}`)}${_scopeId}> ID: ${ssrInterpolate(answer.id)}</div></div><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedAnswers.includes(answer.id)) ? " checked" : ""}${_scopeId}></header><div class="flex flex-col flex-1 px-3 py-2 space-y-2 text-[11px]"${_scopeId}><div class="text-[11px] text-center font-semibold text-blue-600 dark:text-blue-300"${ssrRenderAttr("title", getQuizTitle(answer) || `Quiz ID: ${answer.school_quiz_id}`)}${_scopeId}>${ssrInterpolate(getQuizTitle(answer) || `Quiz ID: ${answer.school_quiz_id}`)}</div><div class="text-xs text-slate-800 dark:text-slate-100 font-semibold border border-dashed border-slate-400 bg-slate-100/70 dark:bg-slate-900/40 rounded-sm px-2 py-1 min-h-[3rem]"${ssrRenderAttr("title", stripHtml(getQuestionText(answer)))}${_scopeId}>${ssrInterpolate(shortText(
                getQuestionText(answer)
              ) || `ID: ${answer.school_quiz_question_id}`)}</div><div class="text-[11px] text-center font-semibold text-indigo-700 dark:text-indigo-200"${ssrRenderAttr("title", stripHtml(getAnswerText(answer)))}${_scopeId}>${ssrInterpolate(shortText(
                getAnswerText(answer)
              ) || `ID: ${answer.id}`)}</div>`);
              if (getAnswerExplanation(answer)) {
                _push2(`<div class="text-[10px] text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", stripHtml(getAnswerExplanation(answer)))}${_scopeId}>${ssrInterpolate(shortText(
                  getAnswerExplanation(answer),
                  90
                ))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="flex flex-wrap justify-between gap-2 mt-1 text-[10px]"${_scopeId}><span class="${ssrRenderClass([
                "px-2 py-0.5 rounded-sm font-semibold border",
                answer.is_correct ? "border-emerald-500 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-200" : "border-rose-500 bg-rose-50 dark:bg-rose-900/40 text-rose-700 dark:text-rose-200"
              ])}"${_scopeId}>${ssrInterpolate(unref(t)("isCorrect"))}: ${ssrInterpolate(answer.is_correct ? unref(t)("yes") : unref(t)("no"))}</span><span class="px-2 py-0.5 rounded-sm bg-amber-100 dark:bg-amber-900 border border-gray-400 text-amber-700 dark:text-amber-300"${_scopeId}>${ssrInterpolate(unref(t)("points"))}: ${ssrInterpolate(answer.weight ?? 0)}</span></div><div class="flex justify-center text-[10px] text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(unref(t)("quizAttempts"))}: ${ssrInterpolate(answer.attempt_items_count ?? 0)}</div><div class="grid grid-cols-2 gap-1 text-[10px] text-center"${_scopeId}><span class="border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1 py-0.5"${_scopeId}>${ssrInterpolate(unref(t)("quiz"))} ID: ${ssrInterpolate(answer.school_quiz_id ?? "—")}</span><span class="border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1 py-0.5"${_scopeId}>${ssrInterpolate(unref(t)("question"))} ID: ${ssrInterpolate(answer.school_quiz_question_id ?? "—")}</span></div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: answer.activity,
                title: answer.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", answer)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolQuizAnswers.edit", {
                  schoolQuizAnswer: answer.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                title: unref(t)("delete"),
                onDelete: ($event) => emit("delete", answer)
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
                        title: `sort: ${answer.sort ?? "—"}`
                      }, " ID: " + toDisplayString(answer.id), 9, ["title"])
                    ]),
                    createVNode("input", {
                      type: "checkbox",
                      checked: __props.selectedAnswers.includes(answer.id),
                      onChange: ($event) => emit("toggle-select", answer.id)
                    }, null, 40, ["checked", "onChange"])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-2 text-[11px]" }, [
                    createVNode("div", {
                      class: "text-[11px] text-center font-semibold text-blue-600 dark:text-blue-300",
                      title: getQuizTitle(answer) || `Quiz ID: ${answer.school_quiz_id}`
                    }, toDisplayString(getQuizTitle(answer) || `Quiz ID: ${answer.school_quiz_id}`), 9, ["title"]),
                    createVNode("div", {
                      class: "text-xs text-slate-800 dark:text-slate-100 font-semibold border border-dashed border-slate-400 bg-slate-100/70 dark:bg-slate-900/40 rounded-sm px-2 py-1 min-h-[3rem]",
                      title: stripHtml(getQuestionText(answer))
                    }, toDisplayString(shortText(
                      getQuestionText(answer)
                    ) || `ID: ${answer.school_quiz_question_id}`), 9, ["title"]),
                    createVNode("div", {
                      class: "text-[11px] text-center font-semibold text-indigo-700 dark:text-indigo-200",
                      title: stripHtml(getAnswerText(answer))
                    }, toDisplayString(shortText(
                      getAnswerText(answer)
                    ) || `ID: ${answer.id}`), 9, ["title"]),
                    getAnswerExplanation(answer) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-[10px] text-slate-600 dark:text-slate-300",
                      title: stripHtml(getAnswerExplanation(answer))
                    }, toDisplayString(shortText(
                      getAnswerExplanation(answer),
                      90
                    )), 9, ["title"])) : createCommentVNode("", true),
                    createVNode("div", { class: "flex flex-wrap justify-between gap-2 mt-1 text-[10px]" }, [
                      createVNode("span", {
                        class: [
                          "px-2 py-0.5 rounded-sm font-semibold border",
                          answer.is_correct ? "border-emerald-500 bg-emerald-50 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-200" : "border-rose-500 bg-rose-50 dark:bg-rose-900/40 text-rose-700 dark:text-rose-200"
                        ]
                      }, toDisplayString(unref(t)("isCorrect")) + ": " + toDisplayString(answer.is_correct ? unref(t)("yes") : unref(t)("no")), 3),
                      createVNode("span", { class: "px-2 py-0.5 rounded-sm bg-amber-100 dark:bg-amber-900 border border-gray-400 text-amber-700 dark:text-amber-300" }, toDisplayString(unref(t)("points")) + ": " + toDisplayString(answer.weight ?? 0), 1)
                    ]),
                    createVNode("div", { class: "flex justify-center text-[10px] text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(unref(t)("quizAttempts")) + ": " + toDisplayString(answer.attempt_items_count ?? 0), 1),
                    createVNode("div", { class: "grid grid-cols-2 gap-1 text-[10px] text-center" }, [
                      createVNode("span", { class: "border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1 py-0.5" }, toDisplayString(unref(t)("quiz")) + " ID: " + toDisplayString(answer.school_quiz_id ?? "—"), 1),
                      createVNode("span", { class: "border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1 py-0.5" }, toDisplayString(unref(t)("question")) + " ID: " + toDisplayString(answer.school_quiz_question_id ?? "—"), 1)
                    ])
                  ]),
                  createVNode("footer", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: answer.activity,
                        title: answer.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", answer)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolQuizAnswers.edit", {
                          schoolQuizAnswer: answer.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        title: unref(t)("delete"),
                        onDelete: ($event) => emit("delete", answer)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizAnswer/View/QuizAnswerCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
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
    adminSchoolQuizAnswersProcessingMode: {
      type: String,
      default: "frontend"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    answers: {
      type: [Array, Object],
      default: () => []
    },
    answersCount: {
      type: Number,
      default: 0
    },
    adminSchoolQuizAnswersPerPage: {
      type: Number,
      default: 10
    },
    adminSchoolQuizAnswersDefaultSort: {
      type: String,
      default: "idDesc"
    },
    sortParam: {
      type: String,
      default: ""
    },
    search: {
      type: String,
      default: ""
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    quizzes: {
      type: Array,
      default: () => []
    },
    questions: {
      type: Array,
      default: () => []
    },
    currentQuizId: {
      type: Number,
      default: null
    },
    currentQuestionId: {
      type: Number,
      default: null
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  setup(__props) {
    var _a, _b;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_quiz_answers") || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_quiz_answers",
        value
      );
    });
    const answersList = computed(() => {
      var _a2;
      if (Array.isArray(props.answers)) {
        return props.answers;
      }
      if (Array.isArray((_a2 = props.answers) == null ? void 0 : _a2.data)) {
        return props.answers.data;
      }
      return [];
    });
    const localAnswers = ref([]);
    watch(
      answersList,
      (newValue) => {
        localAnswers.value = JSON.parse(
          JSON.stringify(newValue || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const selectedQuizId = ref(
      props.currentQuizId ?? ((_a = props.filters) == null ? void 0 : _a.school_quiz_id) ?? null
    );
    const selectedQuestionId = ref(
      props.currentQuestionId ?? ((_b = props.filters) == null ? void 0 : _b.school_quiz_question_id) ?? null
    );
    const quizOptions = computed(
      () => props.quizzes || []
    );
    const questionOptions = computed(
      () => props.questions || []
    );
    const stripHtml = (html = "") => {
      return (html || "").replace(/<\/p>/gi, " ").replace(/<br\s*\/?>/gi, " ").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/\s+/g, " ").trim();
    };
    const shortText = (html, limit = 100) => {
      const clean = stripHtml(
        html
      );
      return clean.length > limit ? `${clean.slice(0, limit)}…` : clean;
    };
    const getAnswerText = (answer) => {
      var _a2;
      return ((_a2 = answer == null ? void 0 : answer.translation) == null ? void 0 : _a2.text) || `ID: ${answer == null ? void 0 : answer.id}`;
    };
    const getAnswerExplanation = (answer) => {
      var _a2;
      return ((_a2 = answer == null ? void 0 : answer.translation) == null ? void 0 : _a2.explanation) || "";
    };
    const getQuizTitle = (answer) => {
      var _a2, _b2;
      return ((_b2 = (_a2 = answer == null ? void 0 : answer.quiz) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.title) || "";
    };
    const getQuestionText = (answer) => {
      var _a2, _b2;
      return ((_b2 = (_a2 = answer == null ? void 0 : answer.question) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.question_text) || "";
    };
    const getQuestionExplanation = (answer) => {
      var _a2, _b2;
      return ((_b2 = (_a2 = answer == null ? void 0 : answer.question) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.explanation) || "";
    };
    const quizOptionLabel = (quiz) => {
      var _a2, _b2, _c, _d, _e, _f, _g;
      if (!quiz) {
        return "";
      }
      const idPart = `[ID: ${quiz.id}]`;
      const titlePart = ((_a2 = quiz == null ? void 0 : quiz.translation) == null ? void 0 : _a2.title) || (quiz == null ? void 0 : quiz.slug) || `#${quiz.id}`;
      const context = [
        ((_c = (_b2 = quiz == null ? void 0 : quiz.course) == null ? void 0 : _b2.translation) == null ? void 0 : _c.title) ? `Курс: ${quiz.course.translation.title}` : null,
        ((_e = (_d = quiz == null ? void 0 : quiz.module) == null ? void 0 : _d.translation) == null ? void 0 : _e.title) ? `Модуль: ${quiz.module.translation.title}` : null,
        ((_g = (_f = quiz == null ? void 0 : quiz.lesson) == null ? void 0 : _f.translation) == null ? void 0 : _g.title) ? `Урок: ${quiz.lesson.translation.title}` : null
      ].filter(Boolean).join(" / ");
      return context ? `${idPart} ${titlePart} — ${context}` : `${idPart} ${titlePart}`;
    };
    const questionOptionLabel = (question) => {
      var _a2;
      if (!question) {
        return "";
      }
      const idPart = `[ID: ${question.id}]`;
      const textPart = ((_a2 = question == null ? void 0 : question.translation) == null ? void 0 : _a2.question_text) ? shortText(
        question.translation.question_text,
        120
      ) : `#${question.id}`;
      return question.school_quiz_id ? `${idPart} ${textPart} — Quiz ID: ${question.school_quiz_id}` : `${idPart} ${textPart}`;
    };
    const reloadWithFilters = () => {
      const params = {
        ...Object.fromEntries(
          new URLSearchParams(
            window.location.search
          )
        ),
        page: void 0
      };
      if (selectedQuizId.value) {
        params.school_quiz_id = selectedQuizId.value;
      } else {
        delete params.school_quiz_id;
      }
      if (selectedQuestionId.value) {
        params.school_quiz_question_id = selectedQuestionId.value;
      } else {
        delete params.school_quiz_question_id;
      }
      router.get(
        route(
          "admin.schoolQuizAnswers.index"
        ),
        params,
        {
          preserveScroll: true,
          preserveState: false,
          replace: true
        }
      );
    };
    const handleQuizFilterChange = () => {
      selectedQuestionId.value = null;
      reloadWithFilters();
    };
    const handleQuestionFilterChange = () => {
      reloadWithFilters();
    };
    const itemsPerPage = ref(
      props.adminSchoolQuizAnswersPerPage || 10
    );
    watch(itemsPerPage, (newValue) => {
      router.put(
        route(
          "admin.settings.updateAdminCountSchoolQuizAnswers"
        ),
        {
          value: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(
              `Показ ${newValue} элементов на странице.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.value) || "Ошибка обновления кол-ва элементов."
            );
          }
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminSchoolQuizAnswersDefaultSort || "idDesc"
    );
    const currentPage = ref(1);
    watch(sortParam, (newValue) => {
      currentPage.value = 1;
      router.put(
        route(
          "admin.settings.updateAdminSortSchoolQuizAnswers"
        ),
        {
          value: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing) {
              router.get(
                window.location.pathname,
                {
                  ...Object.fromEntries(
                    new URLSearchParams(
                      window.location.search
                    )
                  ),
                  sort: newValue || void 0,
                  page: void 0
                },
                {
                  preserveScroll: true,
                  preserveState: false,
                  replace: true
                }
              );
            }
            toast.info(
              "Сортировка успешно изменена"
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.value) || "Ошибка обновления сортировки."
            );
          }
        }
      );
    });
    const searchQuery = ref(
      props.search || ""
    );
    const normalize = (value) => {
      return stripHtml(
        value ?? ""
      ).toString().trim().toLowerCase();
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(
        value || 0
      ).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortAnswers = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") {
        return list.filter(
          (item) => Boolean(item.activity)
        );
      }
      if (sortParam.value === "inactive") {
        return list.filter(
          (item) => !item.activity
        );
      }
      if (sortParam.value === "correct") {
        return list.filter(
          (item) => Boolean(item.is_correct)
        );
      }
      if (sortParam.value === "incorrect") {
        return list.filter(
          (item) => !item.is_correct
        );
      }
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        textAsc: (a, b) => normalize(
          getAnswerText(a)
        ).localeCompare(
          normalize(
            getAnswerText(b)
          ),
          props.currentLocale
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        textDesc: (a, b) => normalize(
          getAnswerText(b)
        ).localeCompare(
          normalize(
            getAnswerText(a)
          ),
          props.currentLocale
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        quizTitleAsc: (a, b) => normalize(
          getQuizTitle(a)
        ).localeCompare(
          normalize(
            getQuizTitle(b)
          ),
          props.currentLocale
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        quizTitleDesc: (a, b) => normalize(
          getQuizTitle(b)
        ).localeCompare(
          normalize(
            getQuizTitle(a)
          ),
          props.currentLocale
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        questionTextAsc: (a, b) => normalize(
          getQuestionText(a)
        ).localeCompare(
          normalize(
            getQuestionText(b)
          ),
          props.currentLocale
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        questionTextDesc: (a, b) => normalize(
          getQuestionText(b)
        ).localeCompare(
          normalize(
            getQuestionText(a)
          ),
          props.currentLocale
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        weightAsc: byNumberAsc("weight"),
        weightDesc: byNumberDesc("weight"),
        attemptItemsAsc: byNumberAsc(
          "attempt_items_count"
        ),
        attemptItemsDesc: byNumberDesc(
          "attempt_items_count"
        ),
        correctAsc: byNumberAsc(
          "is_correct"
        ),
        correctDesc: byNumberDesc(
          "is_correct"
        ),
        activityAsc: byNumberAsc(
          "activity"
        ),
        activityDesc: byNumberDesc(
          "activity"
        ),
        createdAtAsc: byDateAsc(
          "created_at"
        ),
        createdAtDesc: byDateDesc(
          "created_at"
        ),
        updatedAtAsc: byDateAsc(
          "updated_at"
        ),
        updatedAtDesc: byDateDesc(
          "updated_at"
        )
      };
      return sortMap[sortParam.value] ? list.sort(
        sortMap[sortParam.value]
      ) : list;
    };
    const filteredAnswers = computed(() => {
      let filtered = localAnswers.value || [];
      if (selectedQuizId.value && !props.useServerProcessing) {
        filtered = filtered.filter(
          (answer) => Number(
            answer.school_quiz_id
          ) === Number(
            selectedQuizId.value
          )
        );
      }
      if (selectedQuestionId.value && !props.useServerProcessing) {
        filtered = filtered.filter(
          (answer) => Number(
            answer.school_quiz_question_id
          ) === Number(
            selectedQuestionId.value
          )
        );
      }
      const query = normalize(
        searchQuery.value
      );
      if (!query) {
        return sortAnswers(
          filtered
        );
      }
      filtered = filtered.filter(
        (answer) => {
          var _a2, _b2, _c, _d, _e;
          const values = [
            getAnswerText(answer),
            getAnswerExplanation(answer),
            getQuizTitle(answer),
            (_b2 = (_a2 = answer == null ? void 0 : answer.quiz) == null ? void 0 : _a2.translation) == null ? void 0 : _b2.short,
            (_d = (_c = answer == null ? void 0 : answer.quiz) == null ? void 0 : _c.translation) == null ? void 0 : _d.description,
            (_e = answer == null ? void 0 : answer.quiz) == null ? void 0 : _e.slug,
            getQuestionText(answer),
            getQuestionExplanation(answer)
          ];
          return values.some(
            (value) => normalize(
              value
            ).includes(
              query
            )
          );
        }
      );
      return sortAnswers(
        filtered
      );
    });
    const paginatedAnswers = computed(() => {
      const per = Number(
        itemsPerPage.value || 10
      );
      const start = (currentPage.value - 1) * per;
      return filteredAnswers.value.slice(
        start,
        start + per
      );
    });
    const displayedAnswers = computed(() => {
      return props.useServerProcessing ? answersList.value : paginatedAnswers.value;
    });
    watch(
      [
        itemsPerPage,
        searchQuery,
        selectedQuizId,
        selectedQuestionId
      ],
      () => {
        currentPage.value = 1;
      }
    );
    const showConfirmDeleteModal = ref(false);
    const answerToDeleteId = ref(null);
    const answerToDeleteTitle = ref("");
    const confirmDelete = (answerOrId, title = null) => {
      if (typeof answerOrId === "object") {
        answerToDeleteId.value = answerOrId.id;
        answerToDeleteTitle.value = title || stripHtml(
          getAnswerText(
            answerOrId
          )
        ) || `ID: ${answerOrId.id}`;
      } else {
        answerToDeleteId.value = answerOrId;
        answerToDeleteTitle.value = title || `ID: ${answerOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      answerToDeleteId.value = null;
      answerToDeleteTitle.value = "";
    };
    const deleteAnswer = () => {
      if (answerToDeleteId.value === null) {
        return;
      }
      const idToDelete = answerToDeleteId.value;
      const titleToDelete = answerToDeleteTitle.value;
      router.delete(
        route(
          "admin.schoolQuizAnswers.destroy",
          {
            schoolQuizAnswer: idToDelete
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Ответ "${titleToDelete || `ID: ${idToDelete}`}" удалён.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            const errorMsg = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[errorKey]) || "Произошла ошибка при удалении.";
            toast.error(
              `${errorMsg} (Ответ: ${titleToDelete || `ID: ${idToDelete}`})`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    const patchAnswer = (answerId, payload) => {
      const index = localAnswers.value.findIndex(
        (answer) => Number(answer.id) === Number(answerId)
      );
      if (index === -1) {
        return;
      }
      localAnswers.value[index] = {
        ...localAnswers.value[index],
        ...payload
      };
    };
    const selectedAnswers = ref([]);
    const toggleAll = (payload) => {
      var _a2;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a2 = payload == null ? void 0 : payload.target) == null ? void 0 : _a2.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedAnswers.value.map(
        (answer) => answer.id
      );
      if (checked) {
        selectedAnswers.value = [
          .../* @__PURE__ */ new Set([
            ...selectedAnswers.value,
            ...ids
          ])
        ];
        return;
      }
      selectedAnswers.value = selectedAnswers.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectAnswer = (id) => {
      const index = selectedAnswers.value.indexOf(
        id
      );
      if (index > -1) {
        selectedAnswers.value.splice(
          index,
          1
        );
        return;
      }
      selectedAnswers.value.push(
        id
      );
    };
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (currentPage.value - 1) * itemsPerPage.value;
      const items = orderedIds.map(
        (id, index) => ({
          id,
          sort: startSort + index + 1
        })
      );
      if (!items.length) {
        return;
      }
      router.put(
        route(
          "admin.actions.schoolQuizAnswers.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Порядок ответов успешно обновлён."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок ответов."
            );
            router.reload({
              only: [
                "answers"
              ],
              preserveScroll: true
            });
          }
        }
      );
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedAnswers.value.length) {
        toast.warning(
          "Выберите ответы для активации/деактивации."
        );
        return;
      }
      const idsToUpdate = [
        ...selectedAnswers.value
      ];
      router.put(
        route(
          "admin.actions.schoolQuizAnswers.bulkUpdateActivity"
        ),
        {
          ids: idsToUpdate,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            idsToUpdate.forEach(
              (id) => patchAnswer(
                id,
                {
                  activity: newActivity
                }
              )
            );
            selectedAnswers.value = [];
            toast.success(
              "Активность выбранных ответов обновлена."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности."
            );
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedAnswers.value.length) {
        toast.warning(
          "Выберите ответы для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные ответы?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.schoolQuizAnswers.bulkDestroy"
        ),
        {
          data: {
            ids: selectedAnswers.value,
            ...selectedQuizId.value ? {
              school_quiz_id: selectedQuizId.value
            } : {},
            ...selectedQuestionId.value ? {
              school_quiz_question_id: selectedQuestionId.value
            } : {}
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedAnswers.value = [];
            toast.success(
              "Выбранные ответы успешно удалены."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              (errors == null ? void 0 : errors[errorKey]) || "Ошибка массового удаления ответов."
            );
          }
        }
      );
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") {
        toggleAll({
          target: {
            checked: true
          }
        });
      } else if (action === "deselectAll") {
        toggleAll({
          target: {
            checked: false
          }
        });
      } else if (action === "activate") {
        bulkToggleActivity(
          true
        );
      } else if (action === "deactivate") {
        bulkToggleActivity(
          false
        );
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const toggleActivity = (answer) => {
      const newActivity = !answer.activity;
      const answerTitle = stripHtml(
        getAnswerText(
          answer
        )
      ) || `ID: ${answer.id}`;
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.schoolQuizAnswers.updateActivity",
          {
            schoolQuizAnswer: answer.id
          }
        ),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchAnswer(
              answer.id,
              {
                activity: newActivity
              }
            );
            answer.activity = newActivity;
            toast.success(
              `Ответ "${answerTitle}" ${actionText}.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для ответа "${answerTitle}".`
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("quizAnswers")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("quizAnswers"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("quizAnswers")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("quizAnswers")), 1)
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
              href: _ctx.route("admin.schoolQuizAnswers.create")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16"${_scopeId2}><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current opacity-50 shrink-0",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" })
                    ]))
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("addQuizAnswer"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addQuizAnswer")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              "setting-key": "adminSchoolQuizAnswersProcessingMode",
              mode: __props.adminSchoolQuizAnswersProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.answersCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.answersCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.answersCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mb-3 flex flex-col gap-3"${_scopeId}><select class="px-2 py-1 text-xs bg-slate-200 dark:bg-cyan-900"${_scopeId}><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray(selectedQuizId.value) ? ssrLooseContain(selectedQuizId.value, null) : ssrLooseEqual(selectedQuizId.value, null)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("allQuizzes"))}</option><!--[-->`);
            ssrRenderList(quizOptions.value, (quiz) => {
              _push2(`<option${ssrRenderAttr("value", quiz.id)}${ssrIncludeBooleanAttr(Array.isArray(selectedQuizId.value) ? ssrLooseContain(selectedQuizId.value, quiz.id) : ssrLooseEqual(selectedQuizId.value, quiz.id)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(quizOptionLabel(quiz))}</option>`);
            });
            _push2(`<!--]--></select><select class="px-2 py-1 text-xs bg-slate-200 dark:bg-cyan-900"${_scopeId}><option${ssrRenderAttr("value", null)}${ssrIncludeBooleanAttr(Array.isArray(selectedQuestionId.value) ? ssrLooseContain(selectedQuestionId.value, null) : ssrLooseEqual(selectedQuestionId.value, null)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("allQuestions"))}</option><!--[-->`);
            ssrRenderList(questionOptions.value, (question) => {
              _push2(`<option${ssrRenderAttr("value", question.id)}${ssrIncludeBooleanAttr(Array.isArray(selectedQuestionId.value) ? ssrLooseContain(selectedQuestionId.value, question.id) : ssrLooseEqual(selectedQuestionId.value, question.id)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(questionOptionLabel(question))}</option>`);
            });
            _push2(`<!--]--></select></div>`);
            if (__props.answersCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolQuizAnswers"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$3, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (value) => sortParam.value = value
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.answersCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.answersCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.answersCount), 1)
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
            if (__props.answersCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredAnswers.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.answers }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                answers: displayedAnswers.value,
                "selected-answers": selectedAnswers.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectAnswer,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                answers: displayedAnswers.value,
                "selected-answers": selectedAnswers.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectAnswer,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            }
            if (__props.answersCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredAnswers.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.answers }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$i, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteAnswer,
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
                      href: _ctx.route("admin.schoolQuizAnswers.create")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current opacity-50 shrink-0",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" })
                        ]))
                      ]),
                      default: withCtx(() => [
                        createTextVNode(" " + toDisplayString(unref(t)("addQuizAnswer")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$9, {
                      "setting-key": "adminSchoolQuizAnswersProcessingMode",
                      mode: __props.adminSchoolQuizAnswersProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.answersCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.answersCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.answersCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  createVNode("div", { class: "mb-3 flex flex-col gap-3" }, [
                    withDirectives(createVNode("select", {
                      "onUpdate:modelValue": ($event) => selectedQuizId.value = $event,
                      onChange: handleQuizFilterChange,
                      class: "px-2 py-1 text-xs bg-slate-200 dark:bg-cyan-900"
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
                    ]),
                    withDirectives(createVNode("select", {
                      "onUpdate:modelValue": ($event) => selectedQuestionId.value = $event,
                      onChange: handleQuestionFilterChange,
                      class: "px-2 py-1 text-xs bg-slate-200 dark:bg-cyan-900"
                    }, [
                      createVNode("option", { value: null }, toDisplayString(unref(t)("allQuestions")), 1),
                      (openBlock(true), createBlock(Fragment, null, renderList(questionOptions.value, (question) => {
                        return openBlock(), createBlock("option", {
                          key: question.id,
                          value: question.id
                        }, toDisplayString(questionOptionLabel(question)), 9, ["value"]);
                      }), 128))
                    ], 40, ["onUpdate:modelValue"]), [
                      [
                        vModelSelect,
                        selectedQuestionId.value,
                        void 0,
                        { number: true }
                      ]
                    ])
                  ]),
                  __props.answersCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$d, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolQuizAnswers"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (value) => sortParam.value = value
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.answersCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.answersCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.answersCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredAnswers.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.answers
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    answers: displayedAnswers.value,
                    "selected-answers": selectedAnswers.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectAnswer,
                    onToggleAll: toggleAll
                  }, null, 8, ["answers", "selected-answers"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    answers: displayedAnswers.value,
                    "selected-answers": selectedAnswers.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectAnswer,
                    onToggleAll: toggleAll
                  }, null, 8, ["answers", "selected-answers"])),
                  __props.answersCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredAnswers.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.answers
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteAnswer,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolQuizAnswers/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
