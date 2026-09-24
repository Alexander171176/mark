import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, computed, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrRenderStyle, ssrIncludeBooleanAttr } from "vue/server-renderer";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolHashtag/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>─────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>─────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────</option><option value="nameAsc">${ssrInterpolate(unref(t)("name"))} A→Z</option><option value="nameDesc">${ssrInterpolate(unref(t)("name"))} Z→A</option><option disabled>─────────────</option><option value="slugAsc">Slug A→Z</option><option value="slugDesc">Slug Z→A</option><option disabled>─────────────</option><option value="colorAsc">${ssrInterpolate(unref(t)("typeColor"))} A→Z</option><option value="colorDesc">${ssrInterpolate(unref(t)("typeColor"))} Z→A</option><option disabled>─────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option value="likesDesc">${ssrInterpolate(unref(t)("likes"))} 9→0</option><option value="likesAsc">${ssrInterpolate(unref(t)("likes"))} 0→9</option><option disabled>─────────────</option><option value="coursesDesc">${ssrInterpolate(unref(t)("courses"))} 9→0</option><option value="coursesAsc">${ssrInterpolate(unref(t)("courses"))} 0→9</option><option value="modulesDesc">${ssrInterpolate(unref(t)("modules"))} 9→0</option><option value="modulesAsc">${ssrInterpolate(unref(t)("modules"))} 0→9</option><option value="lessonsDesc">${ssrInterpolate(unref(t)("lessons"))} 9→0</option><option value="lessonsAsc">${ssrInterpolate(unref(t)("lessons"))} 0→9</option><option disabled>─────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolHashtag/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "HashtagTable",
  __ssrInlineRender: true,
  props: {
    hashtags: {
      type: Array,
      default: () => []
    },
    selectedHashtags: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "edit",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const localHashtags = ref([]);
    watch(
      () => props.hashtags,
      (newVal) => {
        localHashtags.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      const newOrderIds = localHashtags.value.map(
        (hashtag) => hashtag.id
      );
      emits("update-sort-order", newOrderIds);
    };
    const truncateText = (text, maxLength = 30) => {
      if (!text) return "";
      return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + "…" : text;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedHashtags.length)}</div>`);
      if (localHashtags.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (__props.hashtags.length > 0) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap w-px"><div class="font-medium text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"><div class="font-medium text-left">${ssrInterpolate(unref(t)("name"))}</div></th><th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("description"))}</div></th><th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("likes"))}><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" class="shrink-0 h-4 w-4"><path class="fill-current text-red-400 dark:text-red-300" d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path><path class="fill-current text-red-400 dark:text-red-300" d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path></svg></div></th><th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"><div class="text-center"><input type="checkbox"></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localHashtags.value,
          "onUpdate:modelValue": ($event) => localHashtags.value = $event,
          onEnd: handleDragEnd,
          "item-key": "id",
          handle: ".handle"
        }, {
          item: withCtx(({ element: hashtag }, _push2, _parent2, _scopeId) => {
            var _a, _b, _c, _d;
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"${_scopeId}><div class="text-center text-slate-800 dark:text-blue-200"${ssrRenderAttr("title", hashtag.sort)}${_scopeId}>${ssrInterpolate(hashtag.id)}</div></td><td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"${_scopeId}><a${ssrRenderAttr("href", `/hashtags/${encodeURIComponent(hashtag.slug)}`)} class="text-sky-600 dark:text-sky-200 hover:underline hover:text-sky-600 dark:hover:text-sky-200 px-2 py-0.5 rounded border-2" style="${ssrRenderStyle({ borderColor: hashtag.color || "#666666" })}" target="_blank" rel="noopener noreferrer"${_scopeId}>${ssrInterpolate(((_a = hashtag.translation) == null ? void 0 : _a.name) || `ID: ${hashtag.id}`)}</a></td><td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"${_scopeId}><div class="text-left text-xs text-teal-600 dark:text-teal-300"${_scopeId}>${ssrInterpolate(truncateText((_b = hashtag.translation) == null ? void 0 : _b.short, 30))}</div></td><td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}>${ssrInterpolate(hashtag.views)}</div></td><td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"${_scopeId}><div class="text-center text-red-400 dark:text-red-300"${_scopeId}>${ssrInterpolate(hashtag.likes)}</div></td><td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: hashtag.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", hashtag),
                title: hashtag.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolHashtags.edit", hashtag.id)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => _ctx.$emit("delete", hashtag.id)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedHashtags.includes(hashtag.id)) ? " checked" : ""}${_scopeId}></div></div></td></tr>`);
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
                  createVNode("td", { class: "px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-slate-800 dark:text-blue-200",
                      title: hashtag.sort
                    }, toDisplayString(hashtag.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap" }, [
                    createVNode("a", {
                      href: `/hashtags/${encodeURIComponent(hashtag.slug)}`,
                      class: "text-sky-600 dark:text-sky-200 hover:underline hover:text-sky-600 dark:hover:text-sky-200 px-2 py-0.5 rounded border-2",
                      style: { borderColor: hashtag.color || "#666666" },
                      target: "_blank",
                      rel: "noopener noreferrer"
                    }, toDisplayString(((_c = hashtag.translation) == null ? void 0 : _c.name) || `ID: ${hashtag.id}`), 13, ["href"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-left text-xs text-teal-600 dark:text-teal-300" }, toDisplayString(truncateText((_d = hashtag.translation) == null ? void 0 : _d.short, 30)), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, toDisplayString(hashtag.views), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-red-400 dark:text-red-300" }, toDisplayString(hashtag.likes), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: hashtag.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", hashtag),
                        title: hashtag.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolHashtags.edit", hashtag.id)
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        onDelete: ($event) => _ctx.$emit("delete", hashtag.id)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 first:pl-7 last:pr-7 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("div", { class: "text-center" }, [
                        createVNode("input", {
                          type: "checkbox",
                          checked: __props.selectedHashtags.includes(hashtag.id),
                          onChange: ($event) => _ctx.$emit("toggle-select", hashtag.id)
                        }, null, 40, ["checked", "onChange"])
                      ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolHashtag/Table/HashtagTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "HashtagCardGrid",
  __ssrInlineRender: true,
  props: {
    hashtags: {
      type: Array,
      default: () => []
    },
    selectedHashtags: {
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
    const emits = __emit;
    const localHashtags = ref([]);
    watch(
      () => props.hashtags,
      (newVal) => {
        localHashtags.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      const newOrderIds = localHashtags.value.map((hashtag) => hashtag.id);
      emits("update-sort-order", newOrderIds);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedHashtags.length)}</div>`);
      if (localHashtags.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localHashtags.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localHashtags.value,
          "onUpdate:modelValue": ($event) => localHashtags.value = $event,
          "item-key": "id",
          onEnd: handleDragEnd,
          handle: ".drag-handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: hashtag }, _push2, _parent2, _scopeId) => {
            var _a, _b, _c, _d;
            if (_push2) {
              _push2(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><div class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[sort: ${hashtag.sort ?? "-"}]`)}${_scopeId}> ID: ${ssrInterpolate(hashtag.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedHashtags.includes(hashtag.id)) ? " checked" : ""}${_scopeId}></div></div><div class="flex flex-col flex-1 px-3 py-2 space-y-2"${_scopeId}><div class="flex justify-center"${_scopeId}><a${ssrRenderAttr("href", `/hashtags/${encodeURIComponent(hashtag.slug)}`)} class="inline-flex items-center text-xs font-semibold text-sky-700 dark:text-sky-200 hover:underline px-2 py-0.5 rounded-full border-2 bg-white/70 dark:bg-slate-900/60" style="${ssrRenderStyle({ borderColor: hashtag.color || "#666666" })}" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", hashtag.slug)}${_scopeId}>${ssrInterpolate(((_a = hashtag.translation) == null ? void 0 : _a.name) || `ID: ${hashtag.id}`)}</a></div>`);
              if ((_b = hashtag.translation) == null ? void 0 : _b.short) {
                _push2(`<div class="text-[11px] text-slate-700 dark:text-slate-100 text-center line-clamp-3"${_scopeId}>${ssrInterpolate(hashtag.translation.short)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="pt-1 flex flex-wrap justify-center gap-3 font-semibold text-[11px] text-slate-900 dark:text-slate-200"${_scopeId}>`);
              if (hashtag.views > 0) {
                _push2(`<div${ssrRenderAttr("title", unref(t)("views"))} class="flex flex-row items-center"${_scopeId}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"${_scopeId}><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"${_scopeId}></path></svg><span class="ml-1"${_scopeId}>${ssrInterpolate(hashtag.views ?? 0)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              if (hashtag.likes > 0) {
                _push2(`<div${ssrRenderAttr("title", unref(t)("likes"))} class="flex flex-row items-center"${_scopeId}><svg xmlns="http://www.w3.org/2000/svg" height="24" width="24" viewBox="0 0 24 24" class="shrink-0 h-4 w-4"${_scopeId}><path class="fill-current text-red-400 dark:text-red-300" d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"${_scopeId}></path><path class="fill-current text-red-400 dark:text-red-300" d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"${_scopeId}></path></svg><span class="ml-1"${_scopeId}>${ssrInterpolate(hashtag.likes)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div><div class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: hashtag.activity,
                onToggleActivity: ($event) => _ctx.$emit("toggle-activity", hashtag),
                title: hashtag.activity ? unref(t)("enabled") : unref(t)("disabled")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolHashtags.edit", hashtag.id)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => _ctx.$emit("delete", hashtag.id)
              }, null, _parent2, _scopeId));
              _push2(`</div></div></div>`);
            } else {
              return [
                createVNode("div", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
                  createVNode("div", { class: "flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500" }, [
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
                        class: "text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100",
                        title: `[sort: ${hashtag.sort ?? "-"}]`
                      }, " ID: " + toDisplayString(hashtag.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedHashtags.includes(hashtag.id),
                        onChange: ($event) => _ctx.$emit("toggle-select", hashtag.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-2" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("a", {
                        href: `/hashtags/${encodeURIComponent(hashtag.slug)}`,
                        class: "inline-flex items-center text-xs font-semibold text-sky-700 dark:text-sky-200 hover:underline px-2 py-0.5 rounded-full border-2 bg-white/70 dark:bg-slate-900/60",
                        style: { borderColor: hashtag.color || "#666666" },
                        target: "_blank",
                        rel: "noopener noreferrer",
                        title: hashtag.slug
                      }, toDisplayString(((_c = hashtag.translation) == null ? void 0 : _c.name) || `ID: ${hashtag.id}`), 13, ["href", "title"])
                    ]),
                    ((_d = hashtag.translation) == null ? void 0 : _d.short) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-[11px] text-slate-700 dark:text-slate-100 text-center line-clamp-3"
                    }, toDisplayString(hashtag.translation.short), 1)) : createCommentVNode("", true),
                    createVNode("div", { class: "pt-1 flex flex-wrap justify-center gap-3 font-semibold text-[11px] text-slate-900 dark:text-slate-200" }, [
                      hashtag.views > 0 ? (openBlock(), createBlock("div", {
                        key: 0,
                        title: unref(t)("views"),
                        class: "flex flex-row items-center"
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current shrink-0",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", {
                            class: "fill-current text-blue-600 dark:text-blue-300",
                            d: "M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"
                          })
                        ])),
                        createVNode("span", { class: "ml-1" }, toDisplayString(hashtag.views ?? 0), 1)
                      ], 8, ["title"])) : createCommentVNode("", true),
                      hashtag.likes > 0 ? (openBlock(), createBlock("div", {
                        key: 1,
                        title: unref(t)("likes"),
                        class: "flex flex-row items-center"
                      }, [
                        (openBlock(), createBlock("svg", {
                          xmlns: "http://www.w3.org/2000/svg",
                          height: "24",
                          width: "24",
                          viewBox: "0 0 24 24",
                          class: "shrink-0 h-4 w-4"
                        }, [
                          createVNode("path", {
                            class: "fill-current text-red-400 dark:text-red-300",
                            d: "M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"
                          }),
                          createVNode("path", {
                            class: "fill-current text-red-400 dark:text-red-300",
                            d: "M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"
                          })
                        ])),
                        createVNode("span", { class: "ml-1" }, toDisplayString(hashtag.likes), 1)
                      ], 8, ["title"])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("div", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: hashtag.activity,
                        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", hashtag),
                        title: hashtag.activity ? unref(t)("enabled") : unref(t)("disabled")
                      }, null, 8, ["isActive", "onToggleActivity", "title"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolHashtags.edit", hashtag.id)
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        onDelete: ($event) => _ctx.$emit("delete", hashtag.id)
                      }, null, 8, ["onDelete"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolHashtag/View/HashtagCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminSchoolHashtagsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    hashtags: { type: [Array, Object], default: () => [] },
    hashtagsCount: { type: Number, default: 0 },
    adminSchoolHashtagsPerPage: { type: Number, default: 6 },
    adminSchoolHashtagsDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(localStorage.getItem("admin_view_mode_hashtags") || "table");
    watch(viewMode, (val) => {
      localStorage.setItem("admin_view_mode_hashtags", val);
    });
    const hashtagsList = computed(() => {
      var _a;
      if (Array.isArray(props.hashtags)) {
        return props.hashtags;
      }
      if (Array.isArray((_a = props.hashtags) == null ? void 0 : _a.data)) {
        return props.hashtags.data;
      }
      return [];
    });
    const localHashtags = ref([]);
    watch(
      hashtagsList,
      (newVal) => {
        localHashtags.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const itemsPerPage = ref(props.adminSchoolHashtagsPerPage || 6);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountSchoolHashtags"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminSchoolHashtagsDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortSchoolHashtags"),
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
          onError: (errors) => {
            toast.error(errors.value || "Ошибка обновления сортировки.");
          }
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
    const getHashtagName = (hashtag) => {
      var _a;
      return ((_a = hashtag == null ? void 0 : hashtag.translation) == null ? void 0 : _a.name) || `ID: ${hashtag == null ? void 0 : hashtag.id}`;
    };
    const getHashtagShort = (hashtag) => {
      var _a;
      return ((_a = hashtag == null ? void 0 : hashtag.translation) == null ? void 0 : _a.short) || "";
    };
    const getHashtagDescription = (hashtag) => {
      var _a;
      return ((_a = hashtag == null ? void 0 : hashtag.translation) == null ? void 0 : _a.description) || "";
    };
    const getHashtagSlug = (hashtag) => {
      return (hashtag == null ? void 0 : hashtag.slug) || "";
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringAsc = (field) => (a, b) => normalize(a == null ? void 0 : a[field]).localeCompare(normalize(b == null ? void 0 : b[field]), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byStringDesc = (field) => (a, b) => normalize(b == null ? void 0 : b[field]).localeCompare(normalize(a == null ? void 0 : a[field]), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortHashtags = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") return list.filter((item) => !!item.activity);
      if (sortParam.value === "inactive") return list.filter((item) => !item.activity);
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        nameAsc: (a, b) => normalize(getHashtagName(a)).localeCompare(normalize(getHashtagName(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        nameDesc: (a, b) => normalize(getHashtagName(b)).localeCompare(normalize(getHashtagName(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        slugAsc: (a, b) => normalize(getHashtagSlug(a)).localeCompare(normalize(getHashtagSlug(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        slugDesc: (a, b) => normalize(getHashtagSlug(b)).localeCompare(normalize(getHashtagSlug(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        colorAsc: byStringAsc("color"),
        colorDesc: byStringDesc("color"),
        viewsAsc: byNumberAsc("views"),
        viewsDesc: byNumberDesc("views"),
        likesAsc: byNumberAsc("likes"),
        likesDesc: byNumberDesc("likes"),
        coursesAsc: byNumberAsc("courses_count"),
        coursesDesc: byNumberDesc("courses_count"),
        modulesAsc: byNumberAsc("modules_count"),
        modulesDesc: byNumberDesc("modules_count"),
        lessonsAsc: byNumberAsc("lessons_count"),
        lessonsDesc: byNumberDesc("lessons_count"),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        dateAsc: (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        dateDesc: (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        createdAtAsc: (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        createdAtDesc: (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        updatedAtAsc: (a, b) => safeDate(a == null ? void 0 : a.updated_at) - safeDate(b == null ? void 0 : b.updated_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        updatedAtDesc: (a, b) => safeDate(b == null ? void 0 : b.updated_at) - safeDate(a == null ? void 0 : a.updated_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id)
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredHashtags = computed(() => {
      let filtered = localHashtags.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortHashtags(filtered);
      }
      filtered = filtered.filter((hashtag) => {
        const name = normalize(getHashtagName(hashtag));
        const slug = normalize(getHashtagSlug(hashtag));
        const short = normalize(getHashtagShort(hashtag));
        const description = normalize(getHashtagDescription(hashtag));
        const color = normalize(hashtag == null ? void 0 : hashtag.color);
        return name.includes(query) || slug.includes(query) || short.includes(query) || description.includes(query) || color.includes(query);
      });
      return sortHashtags(filtered);
    });
    const paginatedHashtags = computed(() => {
      const per = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * per;
      return filteredHashtags.value.slice(start, start + per);
    });
    const displayedHashtags = computed(() => {
      return props.useServerProcessing ? hashtagsList.value : paginatedHashtags.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const showConfirmDeleteModal = ref(false);
    const hashtagToDeleteId = ref(null);
    const hashtagToDeleteName = ref("");
    const confirmDelete = (hashtagOrId, name = null) => {
      if (typeof hashtagOrId === "object") {
        hashtagToDeleteId.value = hashtagOrId.id;
        hashtagToDeleteName.value = name || getHashtagName(hashtagOrId);
      } else {
        hashtagToDeleteId.value = hashtagOrId;
        hashtagToDeleteName.value = name || `ID: ${hashtagOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      hashtagToDeleteId.value = null;
      hashtagToDeleteName.value = "";
    };
    const deleteHashtag = () => {
      if (hashtagToDeleteId.value === null) return;
      const idToDelete = hashtagToDeleteId.value;
      const nameToDelete = hashtagToDeleteName.value;
      router.delete(route("admin.schoolHashtags.destroy", { schoolHashtag: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          toast.success(`Хештег "${nameToDelete || "ID: " + idToDelete}" удалён.`);
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Хештег: ${nameToDelete || "ID: " + idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    const patchHashtag = (hashtagId, payload) => {
      const index = localHashtags.value.findIndex((hashtag) => hashtag.id === hashtagId);
      if (index !== -1) {
        localHashtags.value[index] = {
          ...localHashtags.value[index],
          ...payload
        };
      }
    };
    const selectedHashtags = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedHashtags.value.map((hashtag) => hashtag.id);
      if (checked) {
        selectedHashtags.value = [.../* @__PURE__ */ new Set([...selectedHashtags.value, ...ids])];
      } else {
        selectedHashtags.value = selectedHashtags.value.filter((id) => !ids.includes(id));
      }
    };
    const toggleSelectHashtag = (id) => {
      const index = selectedHashtags.value.indexOf(id);
      if (index > -1) {
        selectedHashtags.value.splice(index, 1);
      } else {
        selectedHashtags.value.push(id);
      }
    };
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (currentPage.value - 1) * itemsPerPage.value;
      const items = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1
      }));
      if (!items.length) return;
      router.put(route("admin.actions.schoolHashtags.updateSortBulk"), { items }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success("Порядок хештегов успешно обновлён."),
        onError: (errors) => {
          console.error("Ошибка обновления сортировки хештегов:", errors);
          toast.error((errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок хештегов.");
          router.reload({
            only: ["hashtags"],
            preserveScroll: true
          });
        }
      });
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedHashtags.value.length) {
        toast.warning("Выберите хештеги для активации/деактивации.");
        return;
      }
      const idsToUpdate = [...selectedHashtags.value];
      router.put(route("admin.actions.schoolHashtags.bulkUpdateActivity"), {
        ids: idsToUpdate,
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          idsToUpdate.forEach((id) => patchHashtag(id, { activity: newActivity }));
          selectedHashtags.value = [];
          toast.success("Активность выбранных хештегов обновлена.");
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности.");
        }
      });
    };
    const bulkDelete = () => {
      if (!selectedHashtags.value.length) {
        toast.warning("Выберите хештеги для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные хештеги?")) return;
      router.delete(route("admin.actions.schoolHashtags.bulkDestroy"), {
        data: { ids: selectedHashtags.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          selectedHashtags.value = [];
          toast.success("Выбранные хештеги успешно удалены.");
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          toast.error(errors[errorKey] || "Ошибка массового удаления хештегов.");
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
    const toggleActivity = (hashtag) => {
      const newActivity = !hashtag.activity;
      const hashtagName = getHashtagName(hashtag);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(route("admin.actions.schoolHashtags.updateActivity", {
        schoolHashtag: hashtag.id
      }), {
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          patchHashtag(hashtag.id, { activity: newActivity });
          hashtag.activity = newActivity;
          toast.success(`Хештег "${hashtagName}" ${actionText}.`);
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для хештега "${hashtagName}".`);
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("hashtags")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("hashtags"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("hashtags")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("hashtags")), 1)
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
              href: _ctx.route("admin.schoolHashtags.create")
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
                  _push3(` ${ssrInterpolate(unref(t)("addLearningTag"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addLearningTag")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              "setting-key": "adminSchoolHashtagsProcessingMode",
              mode: __props.adminSchoolHashtagsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.hashtagsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.hashtagsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.hashtagsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.hashtagsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolHashtags"
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
            if (__props.hashtagsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.hashtagsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.hashtagsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              if (__props.hashtagsCount) {
                _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              _push2(ssrRenderComponent(_sfc_main$f, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.hashtagsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredHashtags.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.hashtags }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                hashtags: displayedHashtags.value,
                "selected-hashtags": selectedHashtags.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectHashtag,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                hashtags: displayedHashtags.value,
                "selected-hashtags": selectedHashtags.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectHashtag,
                onToggleAll: toggleAll,
                onUpdateSortOrder: handleSortOrderUpdate
              }, null, _parent2, _scopeId));
            }
            if (__props.hashtagsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredHashtags.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.hashtags }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$i, {
              show: showConfirmDeleteModal.value,
              onClose: closeModal,
              onCancel: closeModal,
              onConfirm: deleteHashtag,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$8, {
                      href: _ctx.route("admin.schoolHashtags.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addLearningTag")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$9, {
                      "setting-key": "adminSchoolHashtagsProcessingMode",
                      mode: __props.adminSchoolHashtagsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.hashtagsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.hashtagsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.hashtagsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.hashtagsCount ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountSchoolHashtags"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.hashtagsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.hashtagsCount), 1)
                      ]),
                      _: 1
                    }),
                    __props.hashtagsCount ? (openBlock(), createBlock(_sfc_main$4, {
                      key: 0,
                      onChange: handleBulkAction
                    })) : createCommentVNode("", true),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.hashtagsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredHashtags.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.hashtags
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    hashtags: displayedHashtags.value,
                    "selected-hashtags": selectedHashtags.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectHashtag,
                    onToggleAll: toggleAll
                  }, null, 8, ["hashtags", "selected-hashtags"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    hashtags: displayedHashtags.value,
                    "selected-hashtags": selectedHashtags.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectHashtag,
                    onToggleAll: toggleAll,
                    onUpdateSortOrder: handleSortOrderUpdate
                  }, null, 8, ["hashtags", "selected-hashtags"])),
                  __props.hashtagsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredHashtags.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.hashtags
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteHashtag,
                cancelText: unref(t)("cancel"),
                confirmText: unref(t)("yesDelete")
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolHashtags/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
