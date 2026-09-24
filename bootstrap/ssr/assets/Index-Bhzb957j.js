import { mergeProps, unref, useSSRContext, ref, watch, computed, withCtx, createTextVNode, toDisplayString, createVNode, createBlock, createCommentVNode, openBlock } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderList, ssrRenderClass, ssrRenderComponent } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$8 } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$9, a as _sfc_main$f } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$c } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$a, a as _sfc_main$d, b as _sfc_main$e } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$b } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$g } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$7 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$5 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$6 } from "./DeleteIconButton-DLv2Mr1x.js";
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
const _sfc_main$4 = {
  __name: "BulkActionSelect",
  __ssrInlineRender: true,
  emits: ["change"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="correct:1">${ssrInterpolate(unref(t)("setCorrect"))}</option><option value="correct:0">${ssrInterpolate(unref(t)("setWrong"))}</option><option disabled>──────────────────</option><option value="delete">${ssrInterpolate(unref(t)("delete"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizAttemptItem/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>──────────────</option><option value="attemptIdDesc">${ssrInterpolate(unref(t)("attempt"))} ID ↓</option><option value="attemptIdAsc">${ssrInterpolate(unref(t)("attempt"))} ID ↑</option><option value="questionIdDesc">${ssrInterpolate(unref(t)("question"))} ID ↓</option><option value="questionIdAsc">${ssrInterpolate(unref(t)("question"))} ID ↑</option><option disabled>──────────────</option><option value="scoreAsc">${ssrInterpolate(unref(t)("score"))} ↑</option><option value="scoreDesc">${ssrInterpolate(unref(t)("score"))} ↓</option><option value="maxScoreAsc">${ssrInterpolate(unref(t)("maxScore"))} ↑</option><option value="maxScoreDesc">${ssrInterpolate(unref(t)("maxScore"))} ↓</option><option disabled>──────────────</option><option value="correctFirst">${ssrInterpolate(unref(t)("isCorrect"))}</option><option value="wrongFirst">${ssrInterpolate(unref(t)("no"))}</option><option disabled>──────────────</option><option value="questionTextAsc">${ssrInterpolate(unref(t)("question"))} ↑</option><option value="questionTextDesc">${ssrInterpolate(unref(t)("question"))} ↓</option><option disabled>──────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>──────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizAttemptItem/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "QuizAttemptItemTable",
  __ssrInlineRender: true,
  props: {
    items: {
      type: Array,
      default: () => []
    },
    selectedItems: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "delete",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localItems = ref([]);
    watch(
      () => props.items,
      (newValue) => {
        localItems.value = JSON.parse(JSON.stringify(newValue || []));
      },
      { immediate: true, deep: true }
    );
    const stripHtml = (html = "") => {
      return (html || "").replace(/<\/p>/gi, " ").replace(/<br\s*\/?>/gi, " ").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/&amp;/gi, "&").replace(/&quot;/gi, '"').replace(/&#039;/gi, "'").replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/\s+/g, " ").trim();
    };
    const shortText = (value, limit = 140) => {
      const clean = stripHtml(value);
      return clean.length > limit ? clean.slice(0, limit) + "…" : clean;
    };
    const getQuestionText = (item) => {
      var _a, _b;
      return ((_b = (_a = item == null ? void 0 : item.question) == null ? void 0 : _a.translation) == null ? void 0 : _b.question_text) || "";
    };
    const getQuizTitle = (item) => {
      var _a, _b, _c, _d, _e;
      return ((_c = (_b = (_a = item == null ? void 0 : item.attempt) == null ? void 0 : _a.quiz) == null ? void 0 : _b.translation) == null ? void 0 : _c.title) || ((_e = (_d = item == null ? void 0 : item.attempt) == null ? void 0 : _d.quiz) == null ? void 0 : _e.slug) || "—";
    };
    const formatAnswerShort = (item) => {
      var _a, _b;
      if (item.free_text_answer) {
        return shortText(item.free_text_answer, 180);
      }
      if ((_b = (_a = item.selected_answer) == null ? void 0 : _a.translation) == null ? void 0 : _b.text) {
        return shortText(
          item.selected_answer.translation.text,
          180
        );
      }
      if (Array.isArray(item.selected_answers) && item.selected_answers.length) {
        return item.selected_answers.map(
          (answer) => {
            var _a2;
            return shortText(
              ((_a2 = answer == null ? void 0 : answer.translation) == null ? void 0 : _a2.text) || `#${answer.id}`,
              60
            );
          }
        ).join(", ");
      }
      if (item.selected_answer_id) {
        return `#${item.selected_answer_id}`;
      }
      if (Array.isArray(item.selected_answer_ids) && item.selected_answer_ids.length) {
        return item.selected_answer_ids.join(", ");
      }
      return "—";
    };
    const formatBool = (value) => {
      return value ? t("yes") : t("no");
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
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="overflow-x-auto">`);
      if (localItems.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><div class="font-medium text-sm text-left">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-medium text-sm text-center">${ssrInterpolate(unref(t)("attempt"))}</div></th><th class="px-2 py-3"><div class="font-medium text-sm text-left">${ssrInterpolate(unref(t)("content"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-medium text-sm text-center">${ssrInterpolate(unref(t)("isCorrect"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-medium text-sm text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 py-3 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(localItems.value.length && localItems.value.every((item) => __props.selectedItems.includes(item.id))) ? " checked" : ""}></th></tr></thead><tbody><!--[-->`);
        ssrRenderList(localItems.value, (item) => {
          var _a, _b, _c, _d, _e, _f, _g;
          _push(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"><td class="px-2 py-3 w-px text-center"><div class="text-xs text-slate-800 dark:text-blue-200">${ssrInterpolate(item.id)}</div></td><td class="px-2 py-3 whitespace-nowrap"><div class="flex flex-col items-center"><span class="text-[11px] text-slate-500 dark:text-slate-200"> ID: ${ssrInterpolate(item.school_quiz_attempt_id)} `);
          if ((_a = item.attempt) == null ? void 0 : _a.attempt_number) {
            _push(`<span class="text-indigo-700 dark:text-indigo-300"> · ${ssrInterpolate(unref(t)("attemptNumber"))} ${ssrInterpolate(item.attempt.attempt_number)}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</span>`);
          if ((_b = item.attempt) == null ? void 0 : _b.user) {
            _push(`<div class="flex flex-col items-center justify-center text-[11px] text-blue-700 dark:text-blue-300">${ssrInterpolate(item.attempt.user.name || "—")} `);
            if (item.attempt.user.email) {
              _push(`<span class="text-slate-500 dark:text-slate-200"> (${ssrInterpolate(item.attempt.user.email)}) </span>`);
            } else {
              _push(`<!---->`);
            }
            _push(`</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></td><td class="px-2 py-3"><div class="flex flex-col gap-1">`);
          if ((_c = item.attempt) == null ? void 0 : _c.quiz) {
            _push(`<div class="text-[11px] text-slate-500 dark:text-slate-200 text-left"${ssrRenderAttr("title", getQuizTitle(item))}> [ID: ${ssrInterpolate(item.attempt.quiz.id)}] ${ssrInterpolate(unref(t)("quiz"))}: <span class="text-rose-700 dark:text-rose-300">${ssrInterpolate(getQuizTitle(item))}</span><span class="text-xs text-amber-800 dark:text-amber-200"${ssrRenderAttr("title", `${unref(t)("passScore")} / ${unref(t)("maxScore")}`)}> — ${ssrInterpolate(item.score ?? "—")} / ${ssrInterpolate(item.max_score ?? "—")}</span></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="text-[11px]"><span class="font-semibold text-slate-500 dark:text-slate-300"> [ID: ${ssrInterpolate(item.school_quiz_question_id)}] ${ssrInterpolate(unref(t)("quizQuestion"))}: </span>`);
          if ((_d = item.question) == null ? void 0 : _d.question_type) {
            _push(`<span class="text-orange-600 dark:text-orange-200"> · ${ssrInterpolate(questionTypeLabel(item.question.question_type))}</span>`);
          } else {
            _push(`<!---->`);
          }
          if (((_e = item.question) == null ? void 0 : _e.points) !== null && ((_f = item.question) == null ? void 0 : _f.points) !== void 0) {
            _push(`<span class="text-amber-700 dark:text-amber-300"> · ${ssrInterpolate(unref(t)("points"))}: ${ssrInterpolate(item.question.points)}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="text-[12px] text-fuchsia-800 dark:text-fuchsia-200"${ssrRenderAttr("title", stripHtml(getQuestionText(item)))}>${ssrInterpolate(shortText(getQuestionText(item)) || "—")}</div><div class="pt-1 border-t border-dashed border-slate-600/70 dark:border-slate-300/70"><div class="text-[11px] text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("answer"))}: </div><div class="text-[12px] text-teal-700 dark:text-teal-300"${ssrRenderAttr("title", stripHtml(formatAnswerShort(item)))}>${ssrInterpolate(formatAnswerShort(item))}</div></div>`);
          if (item.reviewer_comment) {
            _push(`<div class="mt-1 line-clamp-2 text-[11px] text-slate-600 dark:text-slate-400"${ssrRenderAttr("title", stripHtml(item.reviewer_comment))}>${ssrInterpolate(unref(t)("comment"))}: ${ssrInterpolate(shortText(item.reviewer_comment, 120))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></td><td class="px-2 py-3 flex flex-col items-center justify-center gap-1">`);
          if ((_g = item.attempt) == null ? void 0 : _g.status) {
            _push(`<span class="${ssrRenderClass([attemptStatusClass(item.attempt.status), "text-[11px]"])}">${ssrInterpolate(attemptStatusLabel(item.attempt.status))}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="${ssrRenderClass([item.is_correct ? "border-green-500 bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-200" : "border-rose-500 bg-rose-50 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200", "text-center text-xs font-semibold px-2 py-0.5 rounded-sm border"])}">${ssrInterpolate(formatBool(item.is_correct))}</div></td><td class="px-2 py-3 whitespace-nowrap"><div class="flex justify-end space-x-2">`);
          _push(ssrRenderComponent(_sfc_main$5, {
            href: _ctx.route("admin.schoolQuizAttemptItems.edit", {
              schoolQuizAttemptItem: item.id
            })
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$6, {
            title: unref(t)("delete"),
            onDelete: ($event) => emit("delete", item)
          }, null, _parent));
          _push(`</div></td><td class="px-2 py-3 whitespace-nowrap text-center"><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedItems.includes(item.id)) ? " checked" : ""}></td></tr>`);
        });
        _push(`<!--]--></tbody></table>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizAttemptItem/Table/QuizAttemptItemTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "QuizAttemptItemCardGrid",
  __ssrInlineRender: true,
  props: {
    items: {
      type: Array,
      default: () => []
    },
    selectedItems: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "delete",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localItems = ref([]);
    watch(
      () => props.items,
      (newValue) => {
        localItems.value = JSON.parse(JSON.stringify(newValue || []));
      },
      { immediate: true, deep: true }
    );
    const stripHtml = (html = "") => {
      return (html || "").replace(/<\/p>/gi, " ").replace(/<br\s*\/?>/gi, " ").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/&amp;/gi, "&").replace(/&quot;/gi, '"').replace(/&#039;/gi, "'").replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/\s+/g, " ").trim();
    };
    const shortText = (value, limit = 120) => {
      const clean = stripHtml(value);
      return clean.length > limit ? clean.slice(0, limit) + "…" : clean;
    };
    const getQuestionText = (item) => {
      var _a, _b;
      return ((_b = (_a = item == null ? void 0 : item.question) == null ? void 0 : _a.translation) == null ? void 0 : _b.question_text) || "";
    };
    const getQuizTitle = (item) => {
      var _a, _b, _c, _d, _e;
      return ((_c = (_b = (_a = item == null ? void 0 : item.attempt) == null ? void 0 : _a.quiz) == null ? void 0 : _b.translation) == null ? void 0 : _c.title) || ((_e = (_d = item == null ? void 0 : item.attempt) == null ? void 0 : _d.quiz) == null ? void 0 : _e.slug) || "—";
    };
    const formatAnswerShort = (item) => {
      var _a, _b;
      if (item.free_text_answer) {
        return shortText(item.free_text_answer, 160);
      }
      if ((_b = (_a = item.selected_answer) == null ? void 0 : _a.translation) == null ? void 0 : _b.text) {
        return shortText(
          item.selected_answer.translation.text,
          160
        );
      }
      if (Array.isArray(item.selected_answers) && item.selected_answers.length) {
        return item.selected_answers.map(
          (answer) => {
            var _a2;
            return shortText(
              ((_a2 = answer == null ? void 0 : answer.translation) == null ? void 0 : _a2.text) || `#${answer.id}`,
              60
            );
          }
        ).join(", ");
      }
      if (item.selected_answer_id) {
        return `#${item.selected_answer_id}`;
      }
      if (Array.isArray(item.selected_answer_ids) && item.selected_answer_ids.length) {
        return item.selected_answer_ids.join(", ");
      }
      return "—";
    };
    const formatBool = (value) => {
      return value ? t("yes") : t("no");
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
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedItems.length)}</div>`);
      if (localItems.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="rounded-sm border-slate-400 mx-2"${ssrIncludeBooleanAttr(localItems.value.length && localItems.value.every((item) => __props.selectedItems.includes(item.id))) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localItems.value.length) {
        _push(`<div class="p-3"><div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"><!--[-->`);
        ssrRenderList(localItems.value, (item) => {
          var _a, _b, _c, _d, _e, _f, _g;
          _push(`<article class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center gap-2"><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"> ID: ${ssrInterpolate(item.id)}</div>`);
          if ((_a = item.attempt) == null ? void 0 : _a.status) {
            _push(`<span class="${ssrRenderClass([attemptStatusClass(item.attempt.status), "text-[11px] font-semibold"])}">${ssrInterpolate(attemptStatusLabel(item.attempt.status))}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="flex items-center gap-2"><span class="${ssrRenderClass([item.is_correct ? "border-green-500 bg-green-50 text-green-800 dark:bg-green-900/30 dark:text-green-200" : "border-rose-500 bg-rose-50 text-rose-800 dark:bg-rose-900/30 dark:text-rose-200", "inline-flex items-center px-2 py-0.5 rounded-sm border text-[11px] font-semibold"])}"${ssrRenderAttr("title", unref(t)("isCorrect"))}>${ssrInterpolate(formatBool(item.is_correct))}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedItems.includes(item.id)) ? " checked" : ""}></div></header><div class="flex flex-col flex-1 px-3 py-2"><div class="flex flex-col items-center text-[11px] font-semibold">`);
          if ((_b = item.attempt) == null ? void 0 : _b.quiz) {
            _push(`<div class="w-full pb-1 text-[11px] text-center text-slate-500 dark:text-slate-200 border-b border-dotted border-slate-700/70 dark:border-slate-300/70"${ssrRenderAttr("title", getQuizTitle(item))}> [ID: ${ssrInterpolate(item.attempt.quiz.id)}] ${ssrInterpolate(unref(t)("quiz"))} — <div class="text-rose-700 dark:text-rose-300">${ssrInterpolate(getQuizTitle(item))}</div><div class="text-center text-amber-800 dark:text-amber-200"${ssrRenderAttr("title", `${unref(t)("passScore")} / ${unref(t)("maxScore")}`)}>${ssrInterpolate(item.score ?? "—")} / ${ssrInterpolate(item.max_score ?? "—")}</div></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="pt-1 text-slate-500 dark:text-slate-300"> [ID: ${ssrInterpolate(item.school_quiz_question_id)}] ${ssrInterpolate(unref(t)("quizQuestion"))}: </div><div class="pb-1 font-semibold text-[12px] text-fuchsia-800 dark:text-fuchsia-200 border-b border-dotted border-slate-700/70 dark:border-slate-300/70"${ssrRenderAttr("title", stripHtml(getQuestionText(item)))}>${ssrInterpolate(shortText(getQuestionText(item)) || "—")}</div>`);
          if ((_c = item.question) == null ? void 0 : _c.question_type) {
            _push(`<div class="py-1 text-orange-600 dark:text-orange-200">${ssrInterpolate(questionTypeLabel(item.question.question_type))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="w-full font-semibold py-1 border-t border-b border-dotted border-slate-700/70 dark:border-slate-300/70"><div class="text-[12px] text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("answer"))}: </div><div class="text-[11px] text-teal-700 dark:text-teal-300"${ssrRenderAttr("title", stripHtml(formatAnswerShort(item)))}>${ssrInterpolate(formatAnswerShort(item))}</div>`);
          if (((_d = item.question) == null ? void 0 : _d.points) !== null && ((_e = item.question) == null ? void 0 : _e.points) !== void 0) {
            _push(`<div class="text-amber-700 dark:text-amber-300">${ssrInterpolate(unref(t)("points"))}: ${ssrInterpolate(item.question.points)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="py-1 flex flex-col items-center gap-0.5"><div class="text-[11px] text-slate-500 dark:text-slate-200"> ID: ${ssrInterpolate(item.school_quiz_attempt_id)} `);
          if ((_f = item.attempt) == null ? void 0 : _f.attempt_number) {
            _push(`<span class="text-indigo-700 dark:text-indigo-300"> · ${ssrInterpolate(unref(t)("attemptNumber"))} ${ssrInterpolate(item.attempt.attempt_number)}</span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
          if ((_g = item.attempt) == null ? void 0 : _g.user) {
            _push(`<div class="flex flex-col items-center justify-center text-[11px] text-blue-700 dark:text-blue-300">${ssrInterpolate(item.attempt.user.name || "—")} `);
            if (item.attempt.user.email) {
              _push(`<span class="text-slate-500 dark:text-slate-200"> (${ssrInterpolate(item.attempt.user.email)}) </span>`);
            } else {
              _push(`<!---->`);
            }
            _push(`</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
          if (item.reviewer_comment) {
            _push(`<div class="pt-1 text-[11px] font-semibold text-slate-600 dark:text-slate-400 border-t border-dotted border-slate-700/70 dark:border-slate-300/70"${ssrRenderAttr("title", stripHtml(item.reviewer_comment))}>${ssrInterpolate(unref(t)("comment"))}: ${ssrInterpolate(shortText(item.reviewer_comment, 100))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center space-x-1">`);
          _push(ssrRenderComponent(_sfc_main$5, {
            href: _ctx.route("admin.schoolQuizAttemptItems.edit", {
              schoolQuizAttemptItem: item.id
            })
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$6, {
            title: unref(t)("delete"),
            onDelete: ($event) => emit("delete", item)
          }, null, _parent));
          _push(`</div></footer></article>`);
        });
        _push(`<!--]--></div></div>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuizAttemptItem/View/QuizAttemptItemCardGrid.vue");
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
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    adminSchoolQuizAttemptItemsProcessingMode: {
      type: String,
      default: "frontend"
    },
    items: {
      type: [Array, Object],
      default: () => []
    },
    itemsCount: {
      type: Number,
      default: 0
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    adminSchoolQuizAttemptItemsPerPage: {
      type: Number,
      default: 20
    },
    adminSchoolQuizAttemptItemsDefaultSort: {
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
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_quiz_attempt_items") || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_quiz_attempt_items", value);
    });
    const itemsList = computed(() => {
      var _a;
      if (Array.isArray(props.items)) return props.items;
      if (Array.isArray((_a = props.items) == null ? void 0 : _a.data)) return props.items.data;
      return [];
    });
    const localItems = ref([]);
    watch(
      itemsList,
      (newValue) => {
        localItems.value = JSON.parse(JSON.stringify(newValue || []));
      },
      { immediate: true, deep: true }
    );
    const itemsPerPage = ref(
      props.adminSchoolQuizAttemptItemsPerPage ?? 20
    );
    watch(itemsPerPage, (newValue) => {
      router.put(
        route("admin.settings.updateAdminCountSchoolQuizAttemptItems"),
        { value: newValue },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(`Показ ${newValue} элементов на странице.`);
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
      props.sortParam || props.adminSchoolQuizAttemptItemsDefaultSort || "idDesc"
    );
    watch(sortParam, (newValue) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortSchoolQuizAttemptItems"),
        { value: newValue },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing) {
              router.get(
                window.location.pathname,
                {
                  ...Object.fromEntries(
                    new URLSearchParams(window.location.search)
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
            toast.info("Сортировка успешно изменена");
          },
          onError: (errors) => {
            const firstError = errors ? Object.values(errors)[0] : null;
            toast.error(
              firstError || "Ошибка обновления параметра сортировки."
            );
          }
        }
      );
    });
    const currentPage = ref(1);
    const searchQuery = ref(props.search || "");
    const stripHtml = (value = "") => {
      if (value === null || typeof value === "undefined") return "";
      const html = typeof value === "string" ? value : JSON.stringify(value);
      return html.replace(/<\/p>/gi, " ").replace(/<br\s*\/?>/gi, " ").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/&amp;/gi, "&").replace(/&quot;/gi, '"').replace(/&#039;/gi, "'").replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/\s+/g, " ").trim();
    };
    const normalize = (value) => {
      return stripHtml(value).toString().trim().toLowerCase();
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(value || 0).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const getQuestionText = (item) => {
      var _a, _b;
      return ((_b = (_a = item == null ? void 0 : item.question) == null ? void 0 : _a.translation) == null ? void 0 : _b.question_text) || "";
    };
    const getQuestionExplanation = (item) => {
      var _a, _b;
      return ((_b = (_a = item == null ? void 0 : item.question) == null ? void 0 : _a.translation) == null ? void 0 : _b.explanation) || "";
    };
    const getSelectedAnswerText = (item) => {
      var _a, _b;
      return ((_b = (_a = item == null ? void 0 : item.selected_answer) == null ? void 0 : _a.translation) == null ? void 0 : _b.text) || "";
    };
    const getSelectedAnswerExplanation = (item) => {
      var _a, _b;
      return ((_b = (_a = item == null ? void 0 : item.selected_answer) == null ? void 0 : _a.translation) == null ? void 0 : _b.explanation) || "";
    };
    const getQuizTitle = (item) => {
      var _a, _b, _c, _d, _e;
      return ((_c = (_b = (_a = item == null ? void 0 : item.attempt) == null ? void 0 : _a.quiz) == null ? void 0 : _b.translation) == null ? void 0 : _c.title) || ((_e = (_d = item == null ? void 0 : item.attempt) == null ? void 0 : _d.quiz) == null ? void 0 : _e.slug) || "";
    };
    const itemTitle = (item) => {
      if (!item) return "";
      return getQuestionText(item) ? stripHtml(getQuestionText(item)) : `ID: ${item.id}`;
    };
    const sortItems = (items) => {
      const list = (items || []).slice();
      const sortMap = {
        idAsc: (a, b) => safeNumber(a.id) - safeNumber(b.id),
        idDesc: (a, b) => safeNumber(b.id) - safeNumber(a.id),
        attemptIdAsc: (a, b) => safeNumber(a.school_quiz_attempt_id) - safeNumber(b.school_quiz_attempt_id),
        attemptIdDesc: (a, b) => safeNumber(b.school_quiz_attempt_id) - safeNumber(a.school_quiz_attempt_id),
        questionIdAsc: (a, b) => safeNumber(a.school_quiz_question_id) - safeNumber(b.school_quiz_question_id),
        questionIdDesc: (a, b) => safeNumber(b.school_quiz_question_id) - safeNumber(a.school_quiz_question_id),
        scoreAsc: (a, b) => safeNumber(a.score) - safeNumber(b.score),
        scoreDesc: (a, b) => safeNumber(b.score) - safeNumber(a.score),
        maxScoreAsc: (a, b) => safeNumber(a.max_score) - safeNumber(b.max_score),
        maxScoreDesc: (a, b) => safeNumber(b.max_score) - safeNumber(a.max_score),
        correctFirst: (a, b) => Number(!!b.is_correct) - Number(!!a.is_correct),
        wrongFirst: (a, b) => Number(!!a.is_correct) - Number(!!b.is_correct),
        questionTextAsc: (a, b) => normalize(getQuestionText(a)).localeCompare(
          normalize(getQuestionText(b)),
          props.currentLocale
        ),
        questionTextDesc: (a, b) => normalize(getQuestionText(b)).localeCompare(
          normalize(getQuestionText(a)),
          props.currentLocale
        ),
        createdAtAsc: (a, b) => safeDate(a.created_at) - safeDate(b.created_at),
        createdAtDesc: (a, b) => safeDate(b.created_at) - safeDate(a.created_at),
        updatedAtAsc: (a, b) => safeDate(a.updated_at) - safeDate(b.updated_at),
        updatedAtDesc: (a, b) => safeDate(b.updated_at) - safeDate(a.updated_at)
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredItems = computed(() => {
      let filtered = localItems.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortItems(filtered);
      }
      filtered = filtered.filter((item) => {
        var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k;
        const selectedAnswersText = Array.isArray(item.selected_answers) ? item.selected_answers.map((answer) => {
          var _a2;
          return ((_a2 = answer == null ? void 0 : answer.translation) == null ? void 0 : _a2.text) || "";
        }).join(" ") : "";
        const values = [
          item.id,
          item.school_quiz_attempt_id,
          item.school_quiz_question_id,
          item.selected_answer_id,
          item.selected_answer_ids ? JSON.stringify(item.selected_answer_ids) : "",
          item.free_text_answer,
          item.reviewer_comment,
          item.score,
          item.max_score,
          getQuestionText(item),
          getQuestionExplanation(item),
          (_a = item.question) == null ? void 0 : _a.question_type,
          getSelectedAnswerText(item),
          getSelectedAnswerExplanation(item),
          selectedAnswersText,
          (_b = item.attempt) == null ? void 0 : _b.status,
          (_c = item.attempt) == null ? void 0 : _c.attempt_number,
          (_e = (_d = item.attempt) == null ? void 0 : _d.user) == null ? void 0 : _e.name,
          (_g = (_f = item.attempt) == null ? void 0 : _f.user) == null ? void 0 : _g.email,
          getQuizTitle(item),
          (_i = (_h = item.attempt) == null ? void 0 : _h.quiz) == null ? void 0 : _i.slug,
          (_k = (_j = item.attempt) == null ? void 0 : _j.quiz) == null ? void 0 : _k.id
        ];
        return values.some(
          (value) => normalize(value).includes(query)
        );
      });
      return sortItems(filtered);
    });
    const paginatedItems = computed(() => {
      const per = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * per;
      return filteredItems.value.slice(start, start + per);
    });
    const displayedItems = computed(() => {
      return props.useServerProcessing ? itemsList.value : paginatedItems.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const showConfirmDeleteModal = ref(false);
    const itemToDelete = ref(null);
    const confirmDelete = (item) => {
      itemToDelete.value = item;
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      itemToDelete.value = null;
    };
    const deleteItem = () => {
      var _a;
      if (!((_a = itemToDelete.value) == null ? void 0 : _a.id)) return;
      const idToDelete = itemToDelete.value.id;
      const titleToDelete = itemTitle(itemToDelete.value);
      router.delete(
        route("admin.schoolQuizAttemptItems.destroy", {
          schoolQuizAttemptItem: idToDelete
        }),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Ответ "${titleToDelete || `ID: ${idToDelete}`}" удалён.`
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0];
            const errorMessage = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[firstKey]) || "Произошла ошибка при удалении.";
            toast.error(
              `${errorMessage} (Ответ: ${titleToDelete || `ID: ${idToDelete}`})`
            );
          },
          onFinish: closeModal
        }
      );
    };
    const selectedItems = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedItems.value.map((item) => item.id);
      if (checked) {
        selectedItems.value = [
          .../* @__PURE__ */ new Set([
            ...selectedItems.value,
            ...ids
          ])
        ];
        return;
      }
      selectedItems.value = selectedItems.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectItem = (id) => {
      const index = selectedItems.value.indexOf(id);
      if (index > -1) {
        selectedItems.value.splice(index, 1);
        return;
      }
      selectedItems.value.push(id);
    };
    const patchItem = (itemId, payload) => {
      const index = localItems.value.findIndex(
        (item) => item.id === itemId
      );
      if (index === -1) return;
      localItems.value[index] = {
        ...localItems.value[index],
        ...payload
      };
    };
    const bulkUpdateCorrect = (isCorrect) => {
      if (!selectedItems.value.length) {
        toast.warning("Выберите ответы.");
        return;
      }
      const idsToUpdate = [...selectedItems.value];
      router.put(
        route("admin.actions.schoolQuizAttemptItems.bulkUpdateCorrect"),
        {
          ids: idsToUpdate,
          is_correct: isCorrect
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            idsToUpdate.forEach((id) => {
              patchItem(id, {
                is_correct: isCorrect
              });
            });
            selectedItems.value = [];
            toast.success(
              "Правильность выбранных текстовых ответов обновлена."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.is_correct) || (errors == null ? void 0 : errors.general) || "Не удалось массово обновить правильность."
            );
          }
        }
      );
    };
    const bulkDestroy = () => {
      var _a;
      if (!selectedItems.value.length) {
        toast.warning("Выберите ответы для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные ответы?")) {
        return;
      }
      router.delete(
        route("admin.actions.schoolQuizAttemptItems.bulkDestroy"),
        {
          data: {
            ids: selectedItems.value,
            ...((_a = props.filters) == null ? void 0 : _a.school_quiz_attempt_id) ? {
              school_quiz_attempt_id: props.filters.school_quiz_attempt_id
            } : {}
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedItems.value = [];
            toast.success(
              "Выбранные ответы успешно удалены."
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || "Ошибка массового удаления ответов."
            );
          }
        }
      );
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") {
        toggleAll({ checked: true });
      } else if (action === "deselectAll") {
        toggleAll({ checked: false });
      } else if (action === "correct:1") {
        bulkUpdateCorrect(true);
      } else if (action === "correct:0") {
        bulkUpdateCorrect(false);
      } else if (action === "delete") {
        bulkDestroy();
      }
      event.target.value = "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("quizAttemptItems")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("quizAttemptItems"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("quizAttemptItems")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("quizAttemptItems")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-end sm:items-center mb-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              "setting-key": "adminSchoolQuizAttemptItemsProcessingMode",
              mode: __props.adminSchoolQuizAttemptItemsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.itemsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.itemsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("search")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.itemsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$9, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.itemsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$a, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$b, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolQuizAttemptItems"
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
            if (__props.itemsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$c, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.itemsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.itemsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$d, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.itemsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mb-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredItems.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$f, { pagination: __props.items }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                items: displayedItems.value,
                "selected-items": selectedItems.value,
                onToggleSelect: toggleSelectItem,
                onToggleAll: toggleAll,
                onDelete: confirmDelete
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                items: displayedItems.value,
                "selected-items": selectedItems.value,
                onToggleSelect: toggleSelectItem,
                onToggleAll: toggleAll,
                onDelete: confirmDelete
              }, null, _parent2, _scopeId));
            }
            if (__props.itemsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredItems.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$f, { pagination: __props.items }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$g, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteItem,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-end sm:items-center mb-3" }, [
                    createVNode(_sfc_main$7, {
                      "setting-key": "adminSchoolQuizAttemptItemsProcessingMode",
                      mode: __props.adminSchoolQuizAttemptItemsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.itemsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.itemsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$8, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("search")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.itemsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$9, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.itemsCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$b, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolQuizAttemptItems"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (value) => sortParam.value = value
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.itemsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    createVNode(_sfc_main$c, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.itemsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$d, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.itemsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mb-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredItems.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$f, {
                      key: 1,
                      pagination: __props.items
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    items: displayedItems.value,
                    "selected-items": selectedItems.value,
                    onToggleSelect: toggleSelectItem,
                    onToggleAll: toggleAll,
                    onDelete: confirmDelete
                  }, null, 8, ["items", "selected-items"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    items: displayedItems.value,
                    "selected-items": selectedItems.value,
                    onToggleSelect: toggleSelectItem,
                    onToggleAll: toggleAll,
                    onDelete: confirmDelete
                  }, null, 8, ["items", "selected-items"])),
                  __props.itemsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredItems.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$f, {
                      key: 1,
                      pagination: __props.items
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$g, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteItem,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolQuizAttemptItems/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
