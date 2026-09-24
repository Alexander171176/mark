import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, computed, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$8 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$a } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$b, a as _sfc_main$h } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$c, a as _sfc_main$f, b as _sfc_main$g } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$d } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$e } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$i } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$9 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$5 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$6 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$7 } from "./DeleteIconButton-DLv2Mr1x.js";
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
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: String
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option value="sort">${ssrInterpolate(unref(t)("sortNumber"))}</option><option disabled>─────────────</option><option value="category">${ssrInterpolate(unref(t)("category"))}</option><option value="type">${ssrInterpolate(unref(t)("type"))}</option><option value="option">${ssrInterpolate(unref(t)("parameter"))}</option><option disabled>─────────────</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Parameters/Sort/SortSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "BulkActionSelect",
  __ssrInlineRender: true,
  emits: ["change"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Parameters/Select/BulkActionSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "ParameterTable",
  __ssrInlineRender: true,
  props: {
    settings: {
      type: Array,
      default: () => []
    },
    selectedSettings: {
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
    const localSettings = ref([]);
    watch(
      () => props.settings,
      (newVal) => {
        localSettings.value = JSON.parse(
          JSON.stringify(newVal || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      const newOrderIds = localSettings.value.map(
        (setting) => setting.id
      );
      emits(
        "update-sort-order",
        newOrderIds
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedSettings.length)}</div>`);
      if (localSettings.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localSettings.value.length) {
        _push(`<table class="table-auto w-full text-sm"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px"><div class="font-medium text-center text-slate-800 dark:text-slate-200">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"${ssrRenderAttr("title", unref(t)("parameter"))}><svg class="shrink-0 h-5 w-5" viewBox="0 0 24 24"><path class="fill-current text-blue-600" d="M10,7H2A1,1,0,0,1,1,6V2A1,1,0,0,1,2,1h8a1,1,0,0,1,1,1V6A1,1,0,0,1,10,7Z"></path><path class="fill-current text-blue-600" d="M10,23H2a1,1,0,0,1-1-1V18a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1v4A1,1,0,0,1,10,23Z"></path><rect class="fill-current text-sky-500" x="5" y="8" width="2" height="8"></rect><path class="fill-current text-sky-500" d="M19,7H17V5H12V3h6a1,1,0,0,1,1,1Z"></path><path class="fill-current text-sky-500" d="M18,21H12V19h5V17h2v3A1,1,0,0,1,18,21Z"></path><path class="fill-current text-violet-500" d="M18,16a1,1,0,0,1-.515-.143l-5-3a1,1,0,0,1,0-1.714l5-3a1,1,0,0,1,1.03,0l5,3a1,1,0,0,1,0,1.714l-5,3A1,1,0,0,1,18,16Z"></path></svg></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"${ssrRenderAttr("title", unref(t)("value"))}><svg class="shrink-0 h-5 w-5" viewBox="0 0 24 24"><path class="fill-current text-sky-500" d="M23.57,10.005l-2.907-.415c-.197-.71-.476-1.385-.831-2.014l1.76-2.348c.148-.199,.129-.478-.047-.653l-2.121-2.121c-.176-.176-.456-.194-.653-.047l-2.348,1.76c-.628-.356-1.303-.634-2.014-.831l-.415-2.907c-.035-.247-.246-.43-.495-.43h-3c-.249,0-.46,.183-.495,.43l-.415,2.907c-.71,.197-1.385,.476-2.014,.831l-2.348-1.76c-.197-.147-.478-.129-.653,.047l-2.121,2.121c-.176,.176-.195,.454-.047,.653l1.76,2.348c-.356,.628-.634,1.303-.831,2.014l-2.907,.415c-.247,.035-.43,.246-.43,.495v3c0,.249,.183,.46,.43,.495l2.907,.415c.197,.71,.476,1.385,.831,2.014l-1.76,2.348c-.148,.199-.129,.478,.047,.653l2.121,2.121c.097,.097,.225,.146,.354,.146,.105,0,.211-.033,.3-.1l2.348-1.76c.628,.356,1.303,.634,2.014,.831l.415,2.907c.035,.247,.246,.43,.495,.43h3c.249,0,.46-.183,.495-.43l.415-2.907c.71-.197,1.385-.476,2.014-.831l2.348,1.76c.089,.066,.194,.1,.3,.1,.129,0,.257-.05,.354-.146l2.121-2.121c.176-.176,.195-.454,.047-.653l-1.76-2.348c.356-.628,.634-1.303,.831-2.014l2.907-.415c.247-.035,.43-.246,.43-.495v-3c0-.249-.183-.46-.43-.495Zm-11.57,5.995c-2.209,0-4-1.791-4-4s1.791-4,4-4,4,1.791,4,4-1.791,4-4,4Z"></path></svg></th><th class="flex justify-center px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"${ssrRenderAttr("title", unref(t)("category"))}><svg class="shrink-0 h-5 w-5" viewBox="0 0 24 24"><path class="fill-current text-blue-600" d="M23.746,16.564l-1.62-.915-8.9,5.028a2.5,2.5,0,0,1-2.459,0l-8.9-5.029-1.62.915a.5.5,0,0,0,0,.872l11.5,6.5a.5.5,0,0,0,.492,0l11.5-6.5a.5.5,0,0,0,0-.872Z"></path><path class="fill-current text-blue-600" d="M23.746,11.564l-1.62-.915-8.9,5.028a2.5,2.5,0,0,1-2.459,0l-8.9-5.029-1.62.915a.5.5,0,0,0,0,.872l11.5,6.5a.5.5,0,0,0,.492,0l11.5-6.5a.5.5,0,0,0,0-.872Z"></path><path class="fill-current text-sky-500" d="M23.746,6.564l-11.5-6.5a.507.507,0,0,0-.492,0l-11.5,6.5a.5.5,0,0,0,0,.872l11.5,6.5a.5.5,0,0,0,.492,0l11.5-6.5a.5.5,0,0,0,0-.872Z"></path></svg></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("actions"))}</div></th><th><input type="checkbox"></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localSettings.value,
          "onUpdate:modelValue": ($event) => localSettings.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: setting }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"${_scopeId}><div class="text-center text-slate-800 dark:text-slate-200"${ssrRenderAttr("title", setting.sort)}${_scopeId}>${ssrInterpolate(setting.id)}</div></td><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"${_scopeId}><div class="text-left text-sm font-semibold text-orange-400 dark:text-orange-200"${ssrRenderAttr("title", setting.type)}${_scopeId}>${ssrInterpolate(setting.option)}</div></td><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"${_scopeId}><div class="text-left text-sm font-semibold text-teal-500 dark:text-teal-200"${ssrRenderAttr("title", setting.value)}${_scopeId}>${ssrInterpolate(setting.value)}</div></td><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"${_scopeId}><div class="text-center py-0.5 px-2 badge bg-blue-500 rounded-sm text-xs text-slate-100"${ssrRenderAttr("title", setting.description)}${_scopeId}>${ssrInterpolate(setting.category)}</div></td><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"${_scopeId}><div class="flex justify-center space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: setting.activity,
                onToggleActivity: ($event) => emits(
                  "toggle-activity",
                  setting
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route(
                  "admin.parameters.edit",
                  {
                    parameter: setting.id
                  }
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => emits(
                  "delete",
                  setting.id
                )
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(
                __props.selectedSettings.includes(
                  setting.id
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
                  createVNode("td", { class: "px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-slate-800 dark:text-slate-200",
                      title: setting.sort
                    }, toDisplayString(setting.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-left text-sm font-semibold text-orange-400 dark:text-orange-200",
                      title: setting.type
                    }, toDisplayString(setting.option), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-left text-sm font-semibold text-teal-500 dark:text-teal-200",
                      title: setting.value
                    }, toDisplayString(setting.value), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center py-0.5 px-2 badge bg-blue-500 rounded-sm text-xs text-slate-100",
                      title: setting.description
                    }, toDisplayString(setting.category), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: setting.activity,
                        onToggleActivity: ($event) => emits(
                          "toggle-activity",
                          setting
                        )
                      }, null, 8, ["isActive", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route(
                          "admin.parameters.edit",
                          {
                            parameter: setting.id
                          }
                        )
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        onDelete: ($event) => emits(
                          "delete",
                          setting.id
                        )
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", null, [
                    createVNode("input", {
                      type: "checkbox",
                      checked: __props.selectedSettings.includes(
                        setting.id
                      ),
                      onChange: ($event) => emits(
                        "toggle-select",
                        setting.id
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Parameters/Table/ParameterTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "ParameterCardGrid",
  __ssrInlineRender: true,
  props: {
    settings: {
      type: Array,
      default: () => []
    },
    selectedSettings: {
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
    const localSettings = ref([]);
    watch(
      () => props.settings,
      (newVal) => {
        localSettings.value = JSON.parse(
          JSON.stringify(newVal || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      const newOrderIds = localSettings.value.map(
        (setting) => setting.id
      );
      emits(
        "update-sort-order",
        newOrderIds
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedSettings.length)}</div>`);
      if (localSettings.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localSettings.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localSettings.value,
          "onUpdate:modelValue": ($event) => localSettings.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd,
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: setting }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<article class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/80 dark:bg-slate-800/90 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100 cursor-move"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><span class="text-[11px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", setting.sort)}${_scopeId}> ID: ${ssrInterpolate(setting.id)}</span></div><div class="text-[10px] px-2 py-0.5 rounded-sm bg-blue-500 text-slate-50 font-semibold"${ssrRenderAttr("title", setting.description)}${_scopeId}>${ssrInterpolate(setting.category)}</div></header><div class="flex-1 px-3 py-3 space-y-2 text-center"${_scopeId}><div class="text-[13px] font-semibold text-orange-500 dark:text-orange-200 line-clamp-2"${ssrRenderAttr("title", setting.option)}${_scopeId}>${ssrInterpolate(setting.option)}</div><div class="text-[13px] font-semibold text-teal-600 dark:text-teal-200 break-all"${ssrRenderAttr("title", setting.value)}${_scopeId}>${ssrInterpolate(setting.value)}</div>`);
              if (setting.type) {
                _push2(`<div class="text-[11px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("type"))}: ${ssrInterpolate(setting.type)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><footer class="px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center justify-between space-x-2"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: setting.activity,
                title: setting.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emits(
                  "toggle-activity",
                  setting
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route(
                  "admin.parameters.edit",
                  {
                    parameter: setting.id
                  }
                )
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => emits(
                  "delete",
                  setting.id
                )
              }, null, _parent2, _scopeId));
              _push2(`</div><div${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(
                __props.selectedSettings.includes(
                  setting.id
                )
              ) ? " checked" : ""}${_scopeId}></div></div></footer></article>`);
            } else {
              return [
                createVNode("article", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/80 dark:bg-slate-800/90 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
                  createVNode("header", { class: "flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100 cursor-move",
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
                      createVNode("span", {
                        class: "text-[11px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100",
                        title: setting.sort
                      }, " ID: " + toDisplayString(setting.id), 9, ["title"])
                    ]),
                    createVNode("div", {
                      class: "text-[10px] px-2 py-0.5 rounded-sm bg-blue-500 text-slate-50 font-semibold",
                      title: setting.description
                    }, toDisplayString(setting.category), 9, ["title"])
                  ]),
                  createVNode("div", { class: "flex-1 px-3 py-3 space-y-2 text-center" }, [
                    createVNode("div", {
                      class: "text-[13px] font-semibold text-orange-500 dark:text-orange-200 line-clamp-2",
                      title: setting.option
                    }, toDisplayString(setting.option), 9, ["title"]),
                    createVNode("div", {
                      class: "text-[13px] font-semibold text-teal-600 dark:text-teal-200 break-all",
                      title: setting.value
                    }, toDisplayString(setting.value), 9, ["title"]),
                    setting.type ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-[11px] text-slate-500 dark:text-slate-300"
                    }, toDisplayString(unref(t)("type")) + ": " + toDisplayString(setting.type), 1)) : createCommentVNode("", true)
                  ]),
                  createVNode("footer", { class: "px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center justify-between space-x-2" }, [
                      createVNode("div", { class: "flex items-center space-x-2" }, [
                        createVNode(_sfc_main$5, {
                          isActive: setting.activity,
                          title: setting.activity ? unref(t)("enabled") : unref(t)("disabled"),
                          onToggleActivity: ($event) => emits(
                            "toggle-activity",
                            setting
                          )
                        }, null, 8, ["isActive", "title", "onToggleActivity"]),
                        createVNode(_sfc_main$6, {
                          href: _ctx.route(
                            "admin.parameters.edit",
                            {
                              parameter: setting.id
                            }
                          )
                        }, null, 8, ["href"]),
                        createVNode(_sfc_main$7, {
                          onDelete: ($event) => emits(
                            "delete",
                            setting.id
                          )
                        }, null, 8, ["onDelete"])
                      ]),
                      createVNode("div", null, [
                        createVNode("input", {
                          type: "checkbox",
                          checked: __props.selectedSettings.includes(
                            setting.id
                          ),
                          onChange: ($event) => emits(
                            "toggle-select",
                            setting.id
                          )
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Parameters/View/ParameterCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    adminSystemSettingsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    settings: { type: [Array, Object], default: () => [] },
    settingsCount: { type: Number, default: 0 },
    adminSystemSettingsPerPage: { type: Number, default: 10 },
    adminSystemSettingsDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(localStorage.getItem("admin_view_mode_parameters") || "table");
    watch(viewMode, (val) => {
      localStorage.setItem("admin_view_mode_parameters", val);
    });
    const settingsList = computed(() => {
      var _a;
      if (Array.isArray(props.settings)) return props.settings;
      if (Array.isArray((_a = props.settings) == null ? void 0 : _a.data)) return props.settings.data;
      return [];
    });
    const localSettings = ref([]);
    watch(
      settingsList,
      (newVal) => {
        localSettings.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const itemsPerPage = ref(props.adminSystemSettingsPerPage || 10);
    watch(itemsPerPage, (newVal) => {
      router.put(route("admin.settings.updateAdminCountSettings"), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
      });
    });
    const sortParam = ref(
      props.sortParam || props.adminSystemSettingsDefaultSort || "idDesc"
    );
    const currentPage = ref(1);
    const searchQuery = ref(props.search || "");
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(route("admin.settings.updateAdminSortSettings"), { value: newVal }, {
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
      });
    });
    const normalize = (value) => (value ?? "").toString().trim().toLowerCase();
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringAsc = (field) => (a, b) => normalize(a == null ? void 0 : a[field]).localeCompare(normalize(b == null ? void 0 : b[field]), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byStringDesc = (field) => (a, b) => normalize(b == null ? void 0 : b[field]).localeCompare(normalize(a == null ? void 0 : a[field]), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortSettings = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") return list.filter((item) => !!item.activity);
      if (sortParam.value === "inactive") return list.filter((item) => !item.activity);
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sort: byNumberAsc("sort"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        category: byStringAsc("category"),
        categoryAsc: byStringAsc("category"),
        categoryDesc: byStringDesc("category"),
        type: byStringAsc("type"),
        typeAsc: byStringAsc("type"),
        typeDesc: byStringDesc("type"),
        option: byStringAsc("option"),
        optionAsc: byStringAsc("option"),
        optionDesc: byStringDesc("option"),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity")
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredSettings = computed(() => {
      let filtered = localSettings.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortSettings(filtered);
      }
      filtered = filtered.filter((setting) => {
        const values = [
          setting == null ? void 0 : setting.id,
          setting == null ? void 0 : setting.sort,
          setting == null ? void 0 : setting.type,
          setting == null ? void 0 : setting.option,
          setting == null ? void 0 : setting.value,
          setting == null ? void 0 : setting.constant,
          setting == null ? void 0 : setting.category,
          setting == null ? void 0 : setting.description
        ];
        return values.some((value) => normalize(value).includes(query));
      });
      return sortSettings(filtered);
    });
    const paginatedSettings = computed(() => {
      const per = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * per;
      return filteredSettings.value.slice(start, start + per);
    });
    const displayedSettings = computed(() => {
      return props.useServerProcessing ? settingsList.value : paginatedSettings.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const showConfirmDeleteModal = ref(false);
    const settingToDeleteId = ref(null);
    const confirmDelete = (id) => {
      settingToDeleteId.value = id;
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      settingToDeleteId.value = null;
    };
    const deleteSetting = () => {
      if (settingToDeleteId.value === null) return;
      const idToDelete = settingToDeleteId.value;
      router.delete(
        route("admin.parameters.destroy", {
          parameter: idToDelete
        }),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Параметр ID ${idToDelete} успешно удалён.`
            );
          },
          onError: (errors) => {
            const errorMsg = errors.general || "Ошибка удаления параметра.";
            toast.error(
              `${errorMsg} (ID: ${idToDelete})`
            );
          },
          onFinish: () => closeModal()
        }
      );
    };
    const patchSetting = (settingId, payload) => {
      const index = localSettings.value.findIndex((setting) => setting.id === settingId);
      if (index !== -1) {
        localSettings.value[index] = {
          ...localSettings.value[index],
          ...payload
        };
      }
    };
    const toggleActivity = (setting) => {
      const newActivity = !setting.activity;
      router.put(route("admin.actions.settings.updateActivity", { setting: setting.id }), {
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: (page) => {
          patchSetting(setting.id, { activity: newActivity });
          setting.activity = newActivity;
          if (page.props.flash.success) {
            toast.success(page.props.flash.success);
          } else if (page.props.flash.warning) {
            toast.warning(page.props.flash.warning);
          } else if (page.props.flash.error || page.props.flash.general) {
            toast.error(page.props.flash.error || page.props.flash.general);
          } else {
            toast.info(`Изменение активности параметра "${setting.option}" выполнено.`);
          }
        },
        onError: (errors) => {
          toast.error(errors.activity || errors.general || `Ошибка изменения активности параметра "${setting.option}".`);
        }
      });
    };
    const selectedSettings = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedSettings.value.map((setting) => setting.id);
      if (checked) {
        selectedSettings.value = [.../* @__PURE__ */ new Set([...selectedSettings.value, ...ids])];
      } else {
        selectedSettings.value = selectedSettings.value.filter((id) => !ids.includes(id));
      }
    };
    const toggleSelectSetting = (settingId) => {
      const index = selectedSettings.value.indexOf(settingId);
      if (index > -1) {
        selectedSettings.value.splice(index, 1);
      } else {
        selectedSettings.value.push(settingId);
      }
    };
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (currentPage.value - 1) * itemsPerPage.value;
      const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1
      }));
      router.put(route("admin.actions.settings.updateSortBulk"), {
        settings: sortData
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.success("Порядок параметров успешно обновлён."),
        onError: (errors) => {
          console.error("Ошибка обновления сортировки:", errors);
          toast.error(errors.general || errors.settings || "Не удалось обновить порядок параметров.");
          router.reload({
            only: ["settings"],
            preserveScroll: true
          });
        }
      });
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedSettings.value.length) {
        toast.warning("Выберите параметры для активации/деактивации");
        return;
      }
      const idsToUpdate = [...selectedSettings.value];
      router.put(route("admin.actions.settings.bulkUpdateActivity"), {
        ids: idsToUpdate,
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          idsToUpdate.forEach((id) => patchSetting(id, { activity: newActivity }));
          selectedSettings.value = [];
          toast.success("Активность параметров массово обновлена");
        },
        onError: (errors) => {
          const msg = (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Не удалось массово обновить активность параметров";
          toast.error(msg);
        }
      });
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") {
        toggleAll({ checked: true });
      } else if (action === "deselectAll") {
        toggleAll({ checked: false });
      } else if (action === "activate") {
        bulkToggleActivity(true);
      } else if (action === "deactivate") {
        bulkToggleActivity(false);
      }
      event.target.value = "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("parametersHeader")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("parametersHeader"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("parametersHeader")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("parametersHeader")), 1)
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
              href: _ctx.route("admin.parameters.create")
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
                  _push3(` ${ssrInterpolate(unref(t)("addParameter"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addParameter")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              "setting-key": "adminSystemSettingsProcessingMode",
              mode: __props.adminSystemSettingsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.settingsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.settingsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByParameter")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.settingsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.settingsCount) {
              _push2(`<div class="flex items-center justify-between flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSettings"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$4, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (val) => sortParam.value = val
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.settingsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.settingsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.settingsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$3, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$f, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.settingsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredSettings.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.settings }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                settings: displayedSettings.value,
                "selected-settings": selectedSettings.value,
                onToggleActivity: toggleActivity,
                onUpdateSortOrder: handleSortOrderUpdate,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectSetting,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                settings: displayedSettings.value,
                "selected-settings": selectedSettings.value,
                onToggleActivity: toggleActivity,
                onUpdateSortOrder: handleSortOrderUpdate,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectSetting,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            }
            if (__props.settingsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredSettings.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.settings }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$i, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteSetting,
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
                      href: _ctx.route("admin.parameters.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addParameter")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$9, {
                      "setting-key": "adminSystemSettingsProcessingMode",
                      mode: __props.adminSystemSettingsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.settingsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.settingsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByParameter")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.settingsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.settingsCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex items-center justify-between flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$d, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSettings"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$4, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.settingsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.settingsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$3, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.settingsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredSettings.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.settings
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    settings: displayedSettings.value,
                    "selected-settings": selectedSettings.value,
                    onToggleActivity: toggleActivity,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectSetting,
                    onToggleAll: toggleAll
                  }, null, 8, ["settings", "selected-settings"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    settings: displayedSettings.value,
                    "selected-settings": selectedSettings.value,
                    onToggleActivity: toggleActivity,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectSetting,
                    onToggleAll: toggleAll
                  }, null, 8, ["settings", "selected-settings"])),
                  __props.settingsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredSettings.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.settings
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteSetting,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/Parameters/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
