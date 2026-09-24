import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, computed, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$e } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$f, a as _sfc_main$l } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$c } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$i } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$g, a as _sfc_main$j, b as _sfc_main$k } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$h } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$m } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$d } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$a } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$b } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$8 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$9 } from "./CloneIconButton-BfVfDOWt.js";
import { _ as _sfc_main$5, a as _sfc_main$6, b as _sfc_main$7 } from "./RightToggle-r8SYzaEU.js";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuiz/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} ↑</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} ↓</option><option disabled>──────────────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} ↑</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} ↓</option><option disabled>──────────────────────────</option><option value="slugAsc">Slug A→Z</option><option value="slugDesc">Slug Z→A</option><option disabled>──────────────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option disabled>──────────────────────────</option><option value="passScoreAsc">${ssrInterpolate(unref(t)("passScore"))} ↑</option><option value="passScoreDesc">${ssrInterpolate(unref(t)("passScore"))} ↓</option><option value="attemptsLimitAsc">${ssrInterpolate(unref(t)("attemptsLimit"))} ↑</option><option value="attemptsLimitDesc">${ssrInterpolate(unref(t)("attemptsLimit"))} ↓</option><option value="timeLimitAsc">${ssrInterpolate(unref(t)("limitMinutes"))} ↑</option><option value="timeLimitDesc">${ssrInterpolate(unref(t)("limitMinutes"))} ↓</option><option disabled>──────────────────────────</option><option value="questionsDesc">${ssrInterpolate(unref(t)("quizQuestions"))} 9→0</option><option value="questionsAsc">${ssrInterpolate(unref(t)("quizQuestions"))} 0→9</option><option value="attemptsDesc">${ssrInterpolate(unref(t)("quizAttempts"))} 9→0</option><option value="attemptsAsc">${ssrInterpolate(unref(t)("quizAttempts"))} 0→9</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>──────────────────────────</option><option value="graded">${ssrInterpolate(unref(t)("quizTypeGraded"))}</option><option value="practice">${ssrInterpolate(unref(t)("quizTypePractice"))}</option><option disabled>──────────────────────────</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>──────────────────────────</option><option value="left">${ssrInterpolate(unref(t)("inLeft"))}</option><option value="noLeft">${ssrInterpolate(unref(t)("notLeft"))}</option><option disabled>──────────────────────────</option><option value="main">${ssrInterpolate(unref(t)("inMain"))}</option><option value="noMain">${ssrInterpolate(unref(t)("notMain"))}</option><option disabled>──────────────────────────</option><option value="right">${ssrInterpolate(unref(t)("inRight"))}</option><option value="noRight">${ssrInterpolate(unref(t)("notRight"))}</option><option disabled>──────────────────────────</option><option value="typeAsc">${ssrInterpolate(unref(t)("type"))} A→Z</option><option value="typeDesc">${ssrInterpolate(unref(t)("type"))} Z→A</option><option disabled>──────────────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>──────────────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuiz/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "QuizTable",
  __ssrInlineRender: true,
  props: {
    quizzes: {
      type: Array,
      default: () => []
    },
    selectedQuizzes: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-left",
    "toggle-main",
    "toggle-right",
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all",
    "clone"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localQuizzes = ref([]);
    watch(
      () => props.quizzes,
      (newVal) => {
        localQuizzes.value = JSON.parse(
          JSON.stringify(newVal || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      const newOrderIds = localQuizzes.value.map(
        (quiz) => quiz.id
      );
      emit(
        "update-sort-order",
        newOrderIds
      );
    };
    const getQuizTitle = (quiz) => {
      var _a;
      return ((_a = quiz == null ? void 0 : quiz.translation) == null ? void 0 : _a.title) || `ID: ${quiz == null ? void 0 : quiz.id}`;
    };
    const getQuizShort = (quiz) => {
      var _a;
      return ((_a = quiz == null ? void 0 : quiz.translation) == null ? void 0 : _a.short) || "";
    };
    const getNestedTitle = (item) => {
      var _a, _b;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || ((_b = item == null ? void 0 : item.translation) == null ? void 0 : _b.name) || "";
    };
    const getPrimaryImage = (quiz) => {
      var _a;
      if (!((_a = quiz.images) == null ? void 0 : _a.length)) {
        return null;
      }
      return quiz.images[0];
    };
    const getImageUrl = (quiz) => {
      const image = getPrimaryImage(
        quiz
      );
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/school/school_quiz_images/default-image.png";
    };
    const getImageAlt = (quiz) => {
      const image = getPrimaryImage(
        quiz
      );
      return (image == null ? void 0 : image.alt) || getQuizTitle(quiz) || t("defaultImageAlt");
    };
    const formatDate = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(
        dateStr
      );
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return date.toLocaleDateString(
        "ru-RU",
        {
          year: "numeric",
          month: "long",
          day: "numeric"
        }
      );
    };
    const getQuizHierarchyTitle = (quiz) => {
      const parts = [
        getNestedTitle(
          quiz == null ? void 0 : quiz.course
        ),
        getNestedTitle(
          quiz == null ? void 0 : quiz.module
        ),
        getNestedTitle(
          quiz == null ? void 0 : quiz.lesson
        )
      ].filter(Boolean);
      return parts.length ? parts.join(" / ") : t("noHierarchyData");
    };
    const quizTypeLabelKeyMap = {
      graded: "quizTypeGraded",
      practice: "quizTypePractice"
    };
    const getQuizTypeLabel = (type) => {
      const key = quizTypeLabelKeyMap[type];
      return key ? t(key) : type || "—";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedQuizzes.length)}</div><label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label></div><div class="overflow-x-auto">`);
      if (__props.quizzes.length > 0) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-2 py-3 w-px"><div class="font-medium text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("title"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("context"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("parametersHeader"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("show"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center"><input type="checkbox"></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localQuizzes.value,
          "onUpdate:modelValue": ($event) => localQuizzes.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: quiz }, _push2, _parent2, _scopeId) => {
            var _a, _b;
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-slate-800 dark:text-blue-200"${ssrRenderAttr("title", `[${quiz.sort}] ${formatDate(quiz.published_at)}`)}${_scopeId}>${ssrInterpolate(quiz.id)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", getImageUrl(quiz))}${ssrRenderAttr("alt", getImageAlt(quiz))}${ssrRenderAttr("title", ((_a = getPrimaryImage(quiz)) == null ? void 0 : _a.caption) || getQuizTitle(quiz) || unref(t)("image"))} class="h-8 w-10 object-cover rounded-xs"${_scopeId}></div></td><td class="px-2 py-3"${_scopeId}><a${ssrRenderAttr("href", `/quizzes/${encodeURIComponent(quiz.slug || "")}`)} class="text-xs text-fuchsia-800 dark:text-fuchsia-200 hover:underline hover:text-sky-600 dark:hover:text-sky-200" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", getQuizShort(quiz) || getQuizTitle(quiz))}${_scopeId}>${ssrInterpolate(getQuizTitle(quiz))}</a>`);
              if (quiz.slug) {
                _push2(`<div class="text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(quiz.slug)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</td><td class="px-2 py-3"${_scopeId}><div class="text-xs text-teal-700 dark:text-teal-300"${ssrRenderAttr("title", getQuizHierarchyTitle(quiz))}${_scopeId}>${ssrInterpolate(getQuizHierarchyTitle(quiz))}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-[11px] leading-snug text-slate-700 dark:text-slate-100"${_scopeId}><div${_scopeId}><span class="font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("type"))}: </span><span class="text-fuchsia-800 dark:text-fuchsia-200"${_scopeId}>${ssrInterpolate(getQuizTypeLabel(quiz.type))}</span></div><div${_scopeId}><span class="font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("limitCount"))}: </span><span class="text-red-800 dark:text-red-200"${_scopeId}>${ssrInterpolate(quiz.attempts_limit ?? "—")}</span></div><div${_scopeId}><span class="font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("limitMinutes"))}: </span><span class="text-red-800 dark:text-red-200"${_scopeId}>${ssrInterpolate(quiz.time_limit_minutes ?? "—")}</span></div><div${_scopeId}><span class="font-semibold"${_scopeId}>${ssrInterpolate(unref(t)("passScore"))}: </span><span class="text-amber-800 dark:text-amber-200"${_scopeId}>${ssrInterpolate(quiz.pass_score ?? "—")}% </span></div><div class="pt-1 fle flex-col text-[10px] text-slate-500 dark:text-slate-300"${_scopeId}><div${_scopeId}>${ssrInterpolate(unref(t)("allQuestions"))}: ${ssrInterpolate(quiz.questions_count ?? 0)}, </div><div${_scopeId}>${ssrInterpolate(unref(t)("quizAttemptItems"))}: ${ssrInterpolate(quiz.attempts_count ?? 0)}</div></div></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: quiz.left,
                title: quiz.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => emit("toggle-left", quiz)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: quiz.main,
                title: quiz.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => emit("toggle-main", quiz)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: quiz.right,
                title: quiz.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => emit("toggle-right", quiz)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isActive: quiz.activity,
                title: quiz.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", quiz)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$9, {
                title: unref(t)("clone"),
                onClone: ($event) => emit("clone", quiz)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.schoolQuizzes.edit", {
                  schoolQuiz: quiz.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                title: unref(t)("delete"),
                onDelete: ($event) => emit("delete", quiz)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedQuizzes.includes(quiz.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-slate-800 dark:text-blue-200",
                      title: `[${quiz.sort}] ${formatDate(quiz.published_at)}`
                    }, toDisplayString(quiz.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: getImageUrl(quiz),
                        alt: getImageAlt(quiz),
                        title: ((_b = getPrimaryImage(quiz)) == null ? void 0 : _b.caption) || getQuizTitle(quiz) || unref(t)("image"),
                        class: "h-8 w-10 object-cover rounded-xs"
                      }, null, 8, ["src", "alt", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("a", {
                      href: `/quizzes/${encodeURIComponent(quiz.slug || "")}`,
                      class: "text-xs text-fuchsia-800 dark:text-fuchsia-200 hover:underline hover:text-sky-600 dark:hover:text-sky-200",
                      target: "_blank",
                      rel: "noopener noreferrer",
                      title: getQuizShort(quiz) || getQuizTitle(quiz)
                    }, toDisplayString(getQuizTitle(quiz)), 9, ["href", "title"]),
                    quiz.slug ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-[10px] text-slate-500 dark:text-slate-300"
                    }, toDisplayString(quiz.slug), 1)) : createCommentVNode("", true)
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("div", {
                      class: "text-xs text-teal-700 dark:text-teal-300",
                      title: getQuizHierarchyTitle(quiz)
                    }, toDisplayString(getQuizHierarchyTitle(quiz)), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-[11px] leading-snug text-slate-700 dark:text-slate-100" }, [
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold" }, toDisplayString(unref(t)("type")) + ": ", 1),
                        createVNode("span", { class: "text-fuchsia-800 dark:text-fuchsia-200" }, toDisplayString(getQuizTypeLabel(quiz.type)), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold" }, toDisplayString(unref(t)("limitCount")) + ": ", 1),
                        createVNode("span", { class: "text-red-800 dark:text-red-200" }, toDisplayString(quiz.attempts_limit ?? "—"), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold" }, toDisplayString(unref(t)("limitMinutes")) + ": ", 1),
                        createVNode("span", { class: "text-red-800 dark:text-red-200" }, toDisplayString(quiz.time_limit_minutes ?? "—"), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold" }, toDisplayString(unref(t)("passScore")) + ": ", 1),
                        createVNode("span", { class: "text-amber-800 dark:text-amber-200" }, toDisplayString(quiz.pass_score ?? "—") + "% ", 1)
                      ]),
                      createVNode("div", { class: "pt-1 fle flex-col text-[10px] text-slate-500 dark:text-slate-300" }, [
                        createVNode("div", null, toDisplayString(unref(t)("allQuestions")) + ": " + toDisplayString(quiz.questions_count ?? 0) + ", ", 1),
                        createVNode("div", null, toDisplayString(unref(t)("quizAttemptItems")) + ": " + toDisplayString(quiz.attempts_count ?? 0), 1)
                      ])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: quiz.left,
                        title: quiz.left ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleLeft: ($event) => emit("toggle-left", quiz)
                      }, null, 8, ["isActive", "title", "onToggleLeft"]),
                      createVNode(_sfc_main$6, {
                        isActive: quiz.main,
                        title: quiz.main ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleMain: ($event) => emit("toggle-main", quiz)
                      }, null, 8, ["isActive", "title", "onToggleMain"]),
                      createVNode(_sfc_main$7, {
                        isActive: quiz.right,
                        title: quiz.right ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleRight: ($event) => emit("toggle-right", quiz)
                      }, null, 8, ["isActive", "title", "onToggleRight"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-2" }, [
                      createVNode(_sfc_main$8, {
                        isActive: quiz.activity,
                        title: quiz.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", quiz)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$9, {
                        title: unref(t)("clone"),
                        onClone: ($event) => emit("clone", quiz)
                      }, null, 8, ["title", "onClone"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.schoolQuizzes.edit", {
                          schoolQuiz: quiz.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        title: unref(t)("delete"),
                        onDelete: ($event) => emit("delete", quiz)
                      }, null, 8, ["title", "onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedQuizzes.includes(quiz.id),
                        onChange: ($event) => emit("toggle-select", quiz.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuiz/Table/QuizTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "QuizCardGrid",
  __ssrInlineRender: true,
  props: {
    quizzes: {
      type: Array,
      default: () => []
    },
    selectedQuizzes: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-left",
    "toggle-main",
    "toggle-right",
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all",
    "clone"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localQuizzes = ref([]);
    watch(
      () => props.quizzes,
      (newVal) => {
        localQuizzes.value = JSON.parse(
          JSON.stringify(newVal || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      const newOrderIds = localQuizzes.value.map(
        (quiz) => quiz.id
      );
      emit(
        "update-sort-order",
        newOrderIds
      );
    };
    const getQuizTitle = (quiz) => {
      var _a;
      return ((_a = quiz == null ? void 0 : quiz.translation) == null ? void 0 : _a.title) || `ID: ${quiz == null ? void 0 : quiz.id}`;
    };
    const getQuizShort = (quiz) => {
      var _a;
      return ((_a = quiz == null ? void 0 : quiz.translation) == null ? void 0 : _a.short) || "";
    };
    const getNestedTitle = (item) => {
      var _a, _b;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || ((_b = item == null ? void 0 : item.translation) == null ? void 0 : _b.name) || "";
    };
    const getPrimaryImage = (quiz) => {
      var _a;
      if (!((_a = quiz.images) == null ? void 0 : _a.length)) {
        return null;
      }
      return quiz.images[0];
    };
    const getImageUrl = (quiz) => {
      const image = getPrimaryImage(
        quiz
      );
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/school/school_quiz_images/default-image.png";
    };
    const getImageAlt = (quiz) => {
      const image = getPrimaryImage(
        quiz
      );
      return (image == null ? void 0 : image.alt) || getQuizTitle(quiz) || t("defaultImageAlt");
    };
    const formatDate = (dateStr) => {
      if (!dateStr) {
        return "";
      }
      const date = new Date(
        dateStr
      );
      if (Number.isNaN(date.getTime())) {
        return "";
      }
      return date.toLocaleDateString(
        "ru-RU",
        {
          year: "numeric",
          month: "long",
          day: "numeric"
        }
      );
    };
    const getQuizHierarchyTitle = (quiz) => {
      const parts = [
        getNestedTitle(
          quiz == null ? void 0 : quiz.course
        ),
        getNestedTitle(
          quiz == null ? void 0 : quiz.module
        ),
        getNestedTitle(
          quiz == null ? void 0 : quiz.lesson
        )
      ].filter(Boolean);
      return parts.length ? parts.join(" / ") : t("noHierarchyData");
    };
    const quizTypeLabelKeyMap = {
      graded: "quizTypeGraded",
      practice: "quizTypePractice"
    };
    const getQuizTypeLabel = (type) => {
      const key = quizTypeLabelKeyMap[type];
      return key ? t(key) : type || "—";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedQuizzes.length)}</div><label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label></div>`);
      if (localQuizzes.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localQuizzes.value,
          "onUpdate:modelValue": ($event) => localQuizzes.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: quiz }, _push2, _parent2, _scopeId) => {
            var _a, _b;
            if (_push2) {
              _push2(`<article class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${quiz.sort}] ${formatDate(quiz.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(quiz.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}>`);
              if (quiz.type) {
                _push2(`<span class="text-[10px] px-1.5 py-0.5 rounded-sm font-semibold border border-gray-400 bg-fuchsia-100 dark:bg-fuchsia-900/40 text-fuchsia-800 dark:text-fuchsia-200"${ssrRenderAttr("title", unref(t)("type"))}${_scopeId}>${ssrInterpolate(getQuizTypeLabel(quiz.type))}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedQuizzes.includes(quiz.id)) ? " checked" : ""}${_scopeId}></div></header><div class="relative w-full h-40 bg-slate-200 dark:bg-slate-900"${_scopeId}><img${ssrRenderAttr("src", getImageUrl(quiz))}${ssrRenderAttr("alt", getImageAlt(quiz))}${ssrRenderAttr("title", ((_a = getPrimaryImage(quiz)) == null ? void 0 : _a.caption) || getQuizTitle(quiz) || unref(t)("image"))} class="w-full h-full object-cover"${_scopeId}></div><div class="flex flex-col flex-1 px-3 py-2 space-y-1"${_scopeId}><a${ssrRenderAttr("href", `/quizzes/${encodeURIComponent(quiz.slug || "")}`)} class="text-sm font-semibold text-fuchsia-800 dark:text-fuchsia-200 hover:underline text-center" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", getQuizShort(quiz) || getQuizTitle(quiz))}${_scopeId}>${ssrInterpolate(getQuizTitle(quiz))}</a><div class="text-[11px] text-slate-500 dark:text-slate-300"${ssrRenderAttr("title", getQuizHierarchyTitle(quiz))}${_scopeId}>${ssrInterpolate(getQuizHierarchyTitle(quiz))}</div><div class="text-[11px] text-slate-600 dark:text-slate-200 space-y-0.5"${_scopeId}><div class="font-semibold"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("limitCount"))}: </span><span class="text-red-800 dark:text-red-200"${_scopeId}>${ssrInterpolate(quiz.attempts_limit ?? "—")}</span></div><div class="font-semibold"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("limitMinutes"))}: </span><span class="text-red-800 dark:text-red-200"${_scopeId}>${ssrInterpolate(quiz.time_limit_minutes ?? "—")}</span></div><div class="font-semibold"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("passScore"))}: </span><span class="text-amber-800 dark:text-amber-200"${_scopeId}>${ssrInterpolate(quiz.pass_score ?? "—")}% </span></div><div class="grid grid-cols-2 gap-1 pt-1 text-[9px] text-center"${_scopeId}><span class="border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1 py-0.5"${_scopeId}>${ssrInterpolate(unref(t)("allQuestions"))}: ${ssrInterpolate(quiz.questions_count ?? 0)}</span><span class="border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1 py-0.5"${_scopeId}>${ssrInterpolate(unref(t)("quizAttemptItems"))}: ${ssrInterpolate(quiz.attempts_count ?? 0)}</span></div></div></div><footer class="flex items-center justify-between px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: quiz.left,
                title: quiz.left ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleLeft: ($event) => emit("toggle-left", quiz)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: quiz.main,
                title: quiz.main ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleMain: ($event) => emit("toggle-main", quiz)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                isActive: quiz.right,
                title: quiz.right ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleRight: ($event) => emit("toggle-right", quiz)
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$8, {
                isActive: quiz.activity,
                title: quiz.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", quiz)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$9, {
                title: unref(t)("clone"),
                onClone: ($event) => emit("clone", quiz)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$a, {
                href: _ctx.route("admin.schoolQuizzes.edit", {
                  schoolQuiz: quiz.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$b, {
                title: unref(t)("delete"),
                onDelete: ($event) => emit("delete", quiz)
              }, null, _parent2, _scopeId));
              _push2(`</div></footer></article>`);
            } else {
              return [
                createVNode("article", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
                  createVNode("header", { class: "flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100",
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
                        class: "text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100",
                        title: `[${quiz.sort}] ${formatDate(quiz.published_at)}`
                      }, " ID: " + toDisplayString(quiz.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      quiz.type ? (openBlock(), createBlock("span", {
                        key: 0,
                        class: "text-[10px] px-1.5 py-0.5 rounded-sm font-semibold border border-gray-400 bg-fuchsia-100 dark:bg-fuchsia-900/40 text-fuchsia-800 dark:text-fuchsia-200",
                        title: unref(t)("type")
                      }, toDisplayString(getQuizTypeLabel(quiz.type)), 9, ["title"])) : createCommentVNode("", true),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedQuizzes.includes(quiz.id),
                        onChange: ($event) => emit("toggle-select", quiz.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "relative w-full h-40 bg-slate-200 dark:bg-slate-900" }, [
                    createVNode("img", {
                      src: getImageUrl(quiz),
                      alt: getImageAlt(quiz),
                      title: ((_b = getPrimaryImage(quiz)) == null ? void 0 : _b.caption) || getQuizTitle(quiz) || unref(t)("image"),
                      class: "w-full h-full object-cover"
                    }, null, 8, ["src", "alt", "title"])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-1" }, [
                    createVNode("a", {
                      href: `/quizzes/${encodeURIComponent(quiz.slug || "")}`,
                      class: "text-sm font-semibold text-fuchsia-800 dark:text-fuchsia-200 hover:underline text-center",
                      target: "_blank",
                      rel: "noopener noreferrer",
                      title: getQuizShort(quiz) || getQuizTitle(quiz)
                    }, toDisplayString(getQuizTitle(quiz)), 9, ["href", "title"]),
                    createVNode("div", {
                      class: "text-[11px] text-slate-500 dark:text-slate-300",
                      title: getQuizHierarchyTitle(quiz)
                    }, toDisplayString(getQuizHierarchyTitle(quiz)), 9, ["title"]),
                    createVNode("div", { class: "text-[11px] text-slate-600 dark:text-slate-200 space-y-0.5" }, [
                      createVNode("div", { class: "font-semibold" }, [
                        createVNode("span", null, toDisplayString(unref(t)("limitCount")) + ": ", 1),
                        createVNode("span", { class: "text-red-800 dark:text-red-200" }, toDisplayString(quiz.attempts_limit ?? "—"), 1)
                      ]),
                      createVNode("div", { class: "font-semibold" }, [
                        createVNode("span", null, toDisplayString(unref(t)("limitMinutes")) + ": ", 1),
                        createVNode("span", { class: "text-red-800 dark:text-red-200" }, toDisplayString(quiz.time_limit_minutes ?? "—"), 1)
                      ]),
                      createVNode("div", { class: "font-semibold" }, [
                        createVNode("span", null, toDisplayString(unref(t)("passScore")) + ": ", 1),
                        createVNode("span", { class: "text-amber-800 dark:text-amber-200" }, toDisplayString(quiz.pass_score ?? "—") + "% ", 1)
                      ]),
                      createVNode("div", { class: "grid grid-cols-2 gap-1 pt-1 text-[9px] text-center" }, [
                        createVNode("span", { class: "border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1 py-0.5" }, toDisplayString(unref(t)("allQuestions")) + ": " + toDisplayString(quiz.questions_count ?? 0), 1),
                        createVNode("span", { class: "border border-dashed border-slate-300 dark:border-slate-600 rounded-sm px-1 py-0.5" }, toDisplayString(unref(t)("quizAttemptItems")) + ": " + toDisplayString(quiz.attempts_count ?? 0), 1)
                      ])
                    ])
                  ]),
                  createVNode("footer", { class: "flex items-center justify-between px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: quiz.left,
                        title: quiz.left ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleLeft: ($event) => emit("toggle-left", quiz)
                      }, null, 8, ["isActive", "title", "onToggleLeft"]),
                      createVNode(_sfc_main$6, {
                        isActive: quiz.main,
                        title: quiz.main ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleMain: ($event) => emit("toggle-main", quiz)
                      }, null, 8, ["isActive", "title", "onToggleMain"]),
                      createVNode(_sfc_main$7, {
                        isActive: quiz.right,
                        title: quiz.right ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleRight: ($event) => emit("toggle-right", quiz)
                      }, null, 8, ["isActive", "title", "onToggleRight"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$8, {
                        isActive: quiz.activity,
                        title: quiz.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", quiz)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$9, {
                        title: unref(t)("clone"),
                        onClone: ($event) => emit("clone", quiz)
                      }, null, 8, ["title", "onClone"]),
                      createVNode(_sfc_main$a, {
                        href: _ctx.route("admin.schoolQuizzes.edit", {
                          schoolQuiz: quiz.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$b, {
                        title: unref(t)("delete"),
                        onDelete: ($event) => emit("delete", quiz)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolQuiz/View/QuizCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminSchoolQuizzesProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    quizzes: { type: [Array, Object], default: () => [] },
    quizzesCount: { type: Number, default: 0 },
    adminSchoolQuizzesPerPage: { type: Number, default: 6 },
    adminSchoolQuizzesDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(localStorage.getItem("admin_view_mode_quizzes") || "table");
    watch(viewMode, (val) => {
      localStorage.setItem("admin_view_mode_quizzes", val);
    });
    const quizzesList = computed(() => {
      var _a;
      if (Array.isArray(props.quizzes)) return props.quizzes;
      if (Array.isArray((_a = props.quizzes) == null ? void 0 : _a.data)) return props.quizzes.data;
      return [];
    });
    const localQuizzes = ref([]);
    watch(
      quizzesList,
      (newVal) => {
        localQuizzes.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const itemsPerPage = ref(props.adminSchoolQuizzesPerPage || 6);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountSchoolQuizzes"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminSchoolQuizzesDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortSchoolQuizzes"),
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
    const getQuizTitle = (quiz) => {
      var _a;
      return ((_a = quiz == null ? void 0 : quiz.translation) == null ? void 0 : _a.title) || `ID: ${quiz == null ? void 0 : quiz.id}`;
    };
    const getQuizShort = (quiz) => {
      var _a;
      return ((_a = quiz == null ? void 0 : quiz.translation) == null ? void 0 : _a.short) || "";
    };
    const getQuizDescription = (quiz) => {
      var _a;
      return ((_a = quiz == null ? void 0 : quiz.translation) == null ? void 0 : _a.description) || "";
    };
    const getNestedTitle = (item) => {
      var _a, _b;
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || ((_b = item == null ? void 0 : item.translation) == null ? void 0 : _b.name) || "";
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringAsc = (field) => (a, b) => normalize(a == null ? void 0 : a[field]).localeCompare(normalize(b == null ? void 0 : b[field]), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byStringDesc = (field) => (a, b) => normalize(b == null ? void 0 : b[field]).localeCompare(normalize(a == null ? void 0 : a[field]), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortQuizzes = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") return list.filter((item) => !!item.activity);
      if (sortParam.value === "inactive") return list.filter((item) => !item.activity);
      if (sortParam.value === "left") return list.filter((item) => !!item.left);
      if (sortParam.value === "noLeft") return list.filter((item) => !item.left);
      if (sortParam.value === "main") return list.filter((item) => !!item.main);
      if (sortParam.value === "noMain") return list.filter((item) => !item.main);
      if (sortParam.value === "right") return list.filter((item) => !!item.right);
      if (sortParam.value === "noRight") return list.filter((item) => !item.right);
      if (sortParam.value === "graded") return list.filter((item) => item.type === "graded");
      if (sortParam.value === "practice") return list.filter((item) => item.type === "practice");
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        titleAsc: (a, b) => normalize(getQuizTitle(a)).localeCompare(normalize(getQuizTitle(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        titleDesc: (a, b) => normalize(getQuizTitle(b)).localeCompare(normalize(getQuizTitle(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        slugAsc: byStringAsc("slug"),
        slugDesc: byStringDesc("slug"),
        typeAsc: byStringAsc("type"),
        typeDesc: byStringDesc("type"),
        passScoreAsc: byNumberAsc("pass_score"),
        passScoreDesc: byNumberDesc("pass_score"),
        attemptsLimitAsc: byNumberAsc("attempts_limit"),
        attemptsLimitDesc: byNumberDesc("attempts_limit"),
        timeLimitAsc: byNumberAsc("time_limit_minutes"),
        timeLimitDesc: byNumberDesc("time_limit_minutes"),
        questionsAsc: byNumberAsc("questions_count"),
        questionsDesc: byNumberDesc("questions_count"),
        attemptsAsc: byNumberAsc("attempts_count"),
        attemptsDesc: byNumberDesc("attempts_count"),
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        publishedAtAsc: byDateAsc("published_at"),
        publishedAtDesc: byDateDesc("published_at"),
        createdAtAsc: byDateAsc("created_at"),
        createdAtDesc: byDateDesc("created_at"),
        updatedAtAsc: byDateAsc("updated_at"),
        updatedAtDesc: byDateDesc("updated_at"),
        courseTitleAsc: (a, b) => normalize(getNestedTitle(a == null ? void 0 : a.course)).localeCompare(normalize(getNestedTitle(b == null ? void 0 : b.course)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        courseTitleDesc: (a, b) => normalize(getNestedTitle(b == null ? void 0 : b.course)).localeCompare(normalize(getNestedTitle(a == null ? void 0 : a.course)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        moduleTitleAsc: (a, b) => normalize(getNestedTitle(a == null ? void 0 : a.module)).localeCompare(normalize(getNestedTitle(b == null ? void 0 : b.module)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        moduleTitleDesc: (a, b) => normalize(getNestedTitle(b == null ? void 0 : b.module)).localeCompare(normalize(getNestedTitle(a == null ? void 0 : a.module)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        lessonTitleAsc: (a, b) => normalize(getNestedTitle(a == null ? void 0 : a.lesson)).localeCompare(normalize(getNestedTitle(b == null ? void 0 : b.lesson)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        lessonTitleDesc: (a, b) => normalize(getNestedTitle(b == null ? void 0 : b.lesson)).localeCompare(normalize(getNestedTitle(a == null ? void 0 : a.lesson)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id)
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredQuizzes = computed(() => {
      let filtered = localQuizzes.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortQuizzes(filtered);
      }
      filtered = filtered.filter((quiz) => {
        var _a, _b, _c;
        const values = [
          getQuizTitle(quiz),
          getQuizShort(quiz),
          getQuizDescription(quiz),
          quiz == null ? void 0 : quiz.slug,
          quiz == null ? void 0 : quiz.type,
          getNestedTitle(quiz == null ? void 0 : quiz.course),
          (_a = quiz == null ? void 0 : quiz.course) == null ? void 0 : _a.slug,
          getNestedTitle(quiz == null ? void 0 : quiz.module),
          (_b = quiz == null ? void 0 : quiz.module) == null ? void 0 : _b.slug,
          getNestedTitle(quiz == null ? void 0 : quiz.lesson),
          (_c = quiz == null ? void 0 : quiz.lesson) == null ? void 0 : _c.slug
        ];
        return values.some((value) => normalize(value).includes(query));
      });
      return sortQuizzes(filtered);
    });
    const paginatedQuizzes = computed(() => {
      const per = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * per;
      return filteredQuizzes.value.slice(start, start + per);
    });
    const displayedQuizzes = computed(() => {
      return props.useServerProcessing ? quizzesList.value : paginatedQuizzes.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const showConfirmDeleteModal = ref(false);
    const quizToDeleteId = ref(null);
    const quizToDeleteTitle = ref("");
    const confirmDelete = (quizOrId, title = null) => {
      if (typeof quizOrId === "object") {
        quizToDeleteId.value = quizOrId.id;
        quizToDeleteTitle.value = title || getQuizTitle(quizOrId);
      } else {
        quizToDeleteId.value = quizOrId;
        quizToDeleteTitle.value = title || `ID: ${quizOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      quizToDeleteId.value = null;
      quizToDeleteTitle.value = "";
    };
    const deleteQuiz = () => {
      if (quizToDeleteId.value === null) return;
      const idToDelete = quizToDeleteId.value;
      const titleToDelete = quizToDeleteTitle.value;
      router.delete(route("admin.schoolQuizzes.destroy", {
        schoolQuiz: idToDelete
      }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Квиз "${titleToDelete || "ID: " + idToDelete}" удалён.`),
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Квиз: ${titleToDelete || "ID: " + idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    const patchQuiz = (quizId, payload) => {
      const index = localQuizzes.value.findIndex((quiz) => quiz.id === quizId);
      if (index !== -1) {
        localQuizzes.value[index] = {
          ...localQuizzes.value[index],
          ...payload
        };
      }
    };
    const selectedQuizzes = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedQuizzes.value.map((quiz) => quiz.id);
      if (checked) {
        selectedQuizzes.value = [.../* @__PURE__ */ new Set([...selectedQuizzes.value, ...ids])];
      } else {
        selectedQuizzes.value = selectedQuizzes.value.filter((id) => !ids.includes(id));
      }
    };
    const toggleSelectQuiz = (id) => {
      const index = selectedQuizzes.value.indexOf(id);
      if (index > -1) {
        selectedQuizzes.value.splice(index, 1);
      } else {
        selectedQuizzes.value.push(id);
      }
    };
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (currentPage.value - 1) * itemsPerPage.value;
      const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1
      }));
      if (!items.length) return;
      router.put(route("admin.actions.schoolQuizzes.updateSortBulk"), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success("Порядок квизов успешно обновлён."),
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок квизов.");
          router.reload({
            only: ["quizzes"],
            preserveScroll: true
          });
        }
      });
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedQuizzes.value.length) {
        toast.warning("Выберите квизы для активации/деактивации.");
        return;
      }
      const idsToUpdate = [...selectedQuizzes.value];
      router.put(route("admin.actions.schoolQuizzes.bulkUpdateActivity"), {
        ids: idsToUpdate,
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          idsToUpdate.forEach((id) => patchQuiz(id, { activity: newActivity }));
          selectedQuizzes.value = [];
          toast.success("Активность выбранных квизов обновлена.");
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности.");
        }
      });
    };
    const bulkToggleFlag = (field, routeName, value) => {
      if (!selectedQuizzes.value.length) {
        toast.warning("Выберите квизы.");
        return;
      }
      const idsToUpdate = [...selectedQuizzes.value];
      router.put(route(routeName), {
        ids: idsToUpdate,
        [field]: value
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          idsToUpdate.forEach((id) => patchQuiz(id, { [field]: value }));
          selectedQuizzes.value = [];
          toast.success("Выбранные квизы обновлены.");
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления.");
        }
      });
    };
    const bulkDelete = () => {
      if (!selectedQuizzes.value.length) {
        toast.warning("Выберите хотя бы один квиз для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные квизы?")) return;
      router.delete(route("admin.actions.schoolQuizzes.bulkDestroy"), {
        data: { ids: selectedQuizzes.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          selectedQuizzes.value = [];
          toast.success("Массовое удаление квизов успешно завершено.");
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          toast.error(errors[errorKey] || "Произошла ошибка при удалении квизов.");
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
      } else if (action === "leftOn") {
        bulkToggleFlag("left", "admin.actions.schoolQuizzes.bulkUpdateLeft", true);
      } else if (action === "leftOff") {
        bulkToggleFlag("left", "admin.actions.schoolQuizzes.bulkUpdateLeft", false);
      } else if (action === "mainOn") {
        bulkToggleFlag("main", "admin.actions.schoolQuizzes.bulkUpdateMain", true);
      } else if (action === "mainOff") {
        bulkToggleFlag("main", "admin.actions.schoolQuizzes.bulkUpdateMain", false);
      } else if (action === "rightOn") {
        bulkToggleFlag("right", "admin.actions.schoolQuizzes.bulkUpdateRight", true);
      } else if (action === "rightOff") {
        bulkToggleFlag("right", "admin.actions.schoolQuizzes.bulkUpdateRight", false);
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const toggleActivity = (quiz) => {
      const newActivity = !quiz.activity;
      const quizTitle = getQuizTitle(quiz);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(route("admin.actions.schoolQuizzes.updateActivity", {
        schoolQuiz: quiz.id
      }), {
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          patchQuiz(quiz.id, { activity: newActivity });
          quiz.activity = newActivity;
          toast.success(`Квиз "${quizTitle}" ${actionText}.`);
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для квиза "${quizTitle}".`);
        }
      });
    };
    const togglePlacement = (quiz, field, routeName) => {
      const newValue = !quiz[field];
      const quizTitle = getQuizTitle(quiz);
      router.put(route(routeName, {
        schoolQuiz: quiz.id
      }), {
        [field]: newValue
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          patchQuiz(quiz.id, { [field]: newValue });
          quiz[field] = newValue;
          toast.success(`Поле "${field}" для квиза "${quizTitle}" обновлено.`);
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors[field]) || (errors == null ? void 0 : errors.general) || `Ошибка обновления поля "${field}".`);
        }
      });
    };
    const toggleLeft = (quiz) => togglePlacement(quiz, "left", "admin.actions.schoolQuizzes.updateLeft");
    const toggleMain = (quiz) => togglePlacement(quiz, "main", "admin.actions.schoolQuizzes.updateMain");
    const toggleRight = (quiz) => togglePlacement(quiz, "right", "admin.actions.schoolQuizzes.updateRight");
    const cloneQuiz = (quiz) => {
      const quizId = quiz == null ? void 0 : quiz.id;
      const quizTitle = getQuizTitle(quiz);
      if (!quizId) {
        toast.error("Не удалось определить квиз для клонирования.");
        return;
      }
      if (!confirm(`Вы уверены, что хотите клонировать квиз "${quizTitle}"?`)) return;
      router.post(route("admin.actions.schoolQuizzes.clone", {
        schoolQuiz: quizId
      }), {}, {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Квиз "${quizTitle}" успешно клонирован.`),
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          toast.error(errors[errorKey] || `Ошибка клонирования квиза "${quizTitle}".`);
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("quizzes")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("quizzes"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("quizzes")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("quizzes")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$c, {
              href: _ctx.route("admin.schoolQuizzes.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addQuiz"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addQuiz")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$d, {
              "setting-key": "adminSchoolQuizzesProcessingMode",
              mode: __props.adminSchoolQuizzesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.quizzesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.quizzesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$e, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.quizzesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$f, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.quizzesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolQuizzes"
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
            if (__props.quizzesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$i, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.quizzesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.quizzesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$j, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.quizzesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredQuizzes.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.quizzes }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                quizzes: displayedQuizzes.value,
                "selected-quizzes": selectedQuizzes.value,
                onToggleActivity: toggleActivity,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onDelete: confirmDelete,
                onClone: cloneQuiz,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectQuiz,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                quizzes: displayedQuizzes.value,
                "selected-quizzes": selectedQuizzes.value,
                onToggleActivity: toggleActivity,
                onToggleLeft: toggleLeft,
                onToggleMain: toggleMain,
                onToggleRight: toggleRight,
                onDelete: confirmDelete,
                onClone: cloneQuiz,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectQuiz,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            }
            if (__props.quizzesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredQuizzes.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$l, { pagination: __props.quizzes }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$m, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteQuiz,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$c, {
                      href: _ctx.route("admin.schoolQuizzes.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addQuiz")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$d, {
                      "setting-key": "adminSchoolQuizzesProcessingMode",
                      mode: __props.adminSchoolQuizzesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.quizzesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.quizzesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.quizzesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$f, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.quizzesCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolQuizzes"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.quizzesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$i, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.quizzesCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$j, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.quizzesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredQuizzes.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.quizzes
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    quizzes: displayedQuizzes.value,
                    "selected-quizzes": selectedQuizzes.value,
                    onToggleActivity: toggleActivity,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onDelete: confirmDelete,
                    onClone: cloneQuiz,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectQuiz,
                    onToggleAll: toggleAll
                  }, null, 8, ["quizzes", "selected-quizzes"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    quizzes: displayedQuizzes.value,
                    "selected-quizzes": selectedQuizzes.value,
                    onToggleActivity: toggleActivity,
                    onToggleLeft: toggleLeft,
                    onToggleMain: toggleMain,
                    onToggleRight: toggleRight,
                    onDelete: confirmDelete,
                    onClone: cloneQuiz,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectQuiz,
                    onToggleAll: toggleAll
                  }, null, 8, ["quizzes", "selected-quizzes"])),
                  __props.quizzesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$k, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredQuizzes.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$l, {
                      key: 1,
                      pagination: __props.quizzes
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$m, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteQuiz,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolQuizzes/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
