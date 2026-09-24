import { mergeProps, unref, useSSRContext, ref, watch, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, computed, createTextVNode, withDirectives, Fragment, renderList, vModelSelect } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderClass, ssrIncludeBooleanAttr, ssrRenderComponent, ssrLooseContain, ssrLooseEqual, ssrRenderList } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$9 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$j } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$f } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$d, a as _sfc_main$g, b as _sfc_main$h } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$b } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$c, a as _sfc_main$i } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$e } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$a } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$6 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$7 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$8 } from "./DeleteIconButton-DLv2Mr1x.js";
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
const _sfc_main$5 = {
  __name: "BulkActionSelect",
  __ssrInlineRender: true,
  emits: [
    "change"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled> ────────────────── </option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled> ────────────────── </option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled> ────────────────── </option></select></div>`);
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Location/Select/BulkActionSelect.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: {
      type: String,
      default: "sortAsc"
    }
  },
  emits: [
    "update:sortParam"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc"> ID ↓ </option><option value="idAsc"> ID ↑ </option><option disabled> ───────────────── </option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9 </option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0 </option><option disabled> ───────────────── </option><option value="titleAsc">${ssrInterpolate(unref(t)("name"))} A→Z </option><option value="titleDesc">${ssrInterpolate(unref(t)("name"))} Z→A </option><option disabled> ───────────────── </option><option value="typeAsc">${ssrInterpolate(unref(t)("type"))} A→Z </option><option value="typeDesc">${ssrInterpolate(unref(t)("type"))} Z→A </option><option disabled> ───────────────── </option><option value="slugAsc"> Slug A→Z </option><option value="slugDesc"> Slug Z→A </option><option disabled> ───────────────── </option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Location/Sort/SortSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "DefaultLocationToggle",
  __ssrInlineRender: true,
  props: {
    isDefault: {
      type: Boolean,
      default: false
    },
    disabled: {
      type: Boolean,
      default: false
    },
    title: {
      type: String,
      default: ""
    }
  },
  emits: [
    "make-default"
  ],
  setup(__props, { emit: __emit }) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        type: "button",
        class: ["relative inline-flex h-5 w-9 shrink-0 rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none", [
          __props.isDefault ? "bg-amber-500" : "bg-slate-300 dark:bg-slate-600",
          __props.disabled ? "cursor-not-allowed opacity-70" : "cursor-pointer"
        ]],
        title: __props.title
      }, _attrs))}><span class="${ssrRenderClass([
        __props.isDefault ? "translate-x-4" : "translate-x-0",
        "pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow transition duration-200"
      ])}"></span></button>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/DefaultLocationToggle.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "LocationTable",
  __ssrInlineRender: true,
  props: {
    locations: {
      type: Array,
      default: () => []
    },
    selectedLocations: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "make-default",
    "update-sort",
    "update-sort-order",
    "delete",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localLocations = ref([]);
    watch(
      () => props.locations,
      (newVal) => {
        localLocations.value = JSON.parse(JSON.stringify(newVal || []));
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localLocations.value.map((location) => location.id)
      );
    };
    const locationTranslation = (location) => (location == null ? void 0 : location.translation) || {};
    const locationTitle = (location) => {
      var _a;
      return ((_a = locationTranslation(location)) == null ? void 0 : _a.title) || `ID: ${location == null ? void 0 : location.id}`;
    };
    const locationTitleIn = (location) => {
      var _a;
      return ((_a = locationTranslation(location)) == null ? void 0 : _a.title_in) || "";
    };
    const parentTitle = (location) => {
      var _a, _b;
      return ((_b = (_a = location == null ? void 0 : location.parent) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((location == null ? void 0 : location.parent_id) ? `ID: ${location.parent_id}` : "—");
    };
    const locationTypeTitle = (type) => {
      const types = {
        country: "country",
        region: "region",
        city: "city",
        district: "district"
      };
      return types[type] ? t(types[type]) : type || "—";
    };
    const coordinates = (location) => {
      if ((location == null ? void 0 : location.latitude) === null || (location == null ? void 0 : location.latitude) === void 0 || (location == null ? void 0 : location.longitude) === null || (location == null ? void 0 : location.longitude) === void 0) {
        return "—";
      }
      return `${location.latitude} / ${location.longitude}`;
    };
    const typeBadgeClass = (type) => {
      const classes = {
        country: "bg-indigo-100 text-indigo-700 border-indigo-300 dark:bg-indigo-900/40 dark:text-indigo-200",
        region: "bg-purple-100 text-purple-700 border-purple-300 dark:bg-purple-900/40 dark:text-purple-200",
        city: "bg-sky-100 text-sky-700 border-sky-300 dark:bg-sky-900/40 dark:text-sky-200",
        district: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-200"
      };
      return classes[type] || "bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-800 dark:text-gray-200";
    };
    const allSelected = () => {
      return !!localLocations.value.length && localLocations.value.every(
        (location) => props.selectedLocations.includes(location.id)
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedLocations.length)}</div>`);
      if (localLocations.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localLocations.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 whitespace-nowrap w-px"><div class="flex justify-center"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" height="24" width="24" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></div></th><th class="px-1 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("title"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("slug"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("type"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("parent"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("code"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("coordinates"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-center"> Timezone </div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 py-3 whitespace-nowrap w-px"><div class="text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localLocations.value,
          "onUpdate:modelValue": ($event) => localLocations.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: location }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 whitespace-nowrap text-center cursor-move handle"${_scopeId}><div class="flex justify-center"${ssrRenderAttr("title", `${unref(t)("sort")}: ${location.sort}`)}${_scopeId}><svg class="w-4 h-4 fill-current text-gray-500 dark:text-gray-300" viewBox="0 0 24 24"${_scopeId}><circle cx="9" cy="5" r="1.5"${_scopeId}></circle><circle cx="15" cy="5" r="1.5"${_scopeId}></circle><circle cx="9" cy="12" r="1.5"${_scopeId}></circle><circle cx="15" cy="12" r="1.5"${_scopeId}></circle><circle cx="9" cy="19" r="1.5"${_scopeId}></circle><circle cx="15" cy="19" r="1.5"${_scopeId}></circle></svg></div></td><td class="px-2 py-3 whitespace-nowrap w-px"${_scopeId}><div class="text-center text-blue-600 dark:text-blue-200"${ssrRenderAttr("title", `${unref(t)("sort")}: ${location.sort}`)}${_scopeId}>${ssrInterpolate(location.id)}</div></td><td class="px-2 py-3"${_scopeId}><div class="text-left text-sky-700 dark:text-sky-200"${ssrRenderAttr("title", locationTitleIn(location))}${_scopeId}>${ssrInterpolate(locationTitle(location))}</div></td><td class="px-2 py-3"${_scopeId}><div class="text-xs text-violet-700 dark:text-violet-200"${_scopeId}>${ssrInterpolate(location.slug)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${_scopeId}><span class="${ssrRenderClass([typeBadgeClass(location.type), "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${_scopeId}>${ssrInterpolate(locationTypeTitle(location.type))}</span></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs"${ssrRenderAttr(
                "title",
                location.parent_id ? `ID: ${location.parent_id}` : null
              )}${_scopeId}>${ssrInterpolate(parentTitle(location))}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-amber-600 dark:text-amber-200"${_scopeId}>${ssrInterpolate(location.code || "—")}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs font-semibold text-blue-600 dark:text-blue-200"${_scopeId}>${ssrInterpolate(coordinates(location))}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs font-semibold"${_scopeId}>${ssrInterpolate(location.timezone || "—")}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$3, {
                "is-default": location.is_default,
                title: location.is_default ? "Локация по умолчанию" : "Сделать локацией по умолчанию",
                onMakeDefault: ($event) => emit("make-default", location)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: location.activity,
                title: location.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", location)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.locations.edit", {
                  location: location.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onClick: ($event) => emit("delete", location)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(
                __props.selectedLocations.includes(
                  location.id
                )
              ) ? " checked" : ""}${_scopeId}></div></td></tr>`);
            } else {
              return [
                createVNode("tr", { class: "text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800" }, [
                  createVNode("td", { class: "px-2 py-1 whitespace-nowrap text-center cursor-move handle" }, [
                    createVNode("div", {
                      class: "flex justify-center",
                      title: `${unref(t)("sort")}: ${location.sort}`
                    }, [
                      (openBlock(), createBlock("svg", {
                        class: "w-4 h-4 fill-current text-gray-500 dark:text-gray-300",
                        viewBox: "0 0 24 24"
                      }, [
                        createVNode("circle", {
                          cx: "9",
                          cy: "5",
                          r: "1.5"
                        }),
                        createVNode("circle", {
                          cx: "15",
                          cy: "5",
                          r: "1.5"
                        }),
                        createVNode("circle", {
                          cx: "9",
                          cy: "12",
                          r: "1.5"
                        }),
                        createVNode("circle", {
                          cx: "15",
                          cy: "12",
                          r: "1.5"
                        }),
                        createVNode("circle", {
                          cx: "9",
                          cy: "19",
                          r: "1.5"
                        }),
                        createVNode("circle", {
                          cx: "15",
                          cy: "19",
                          r: "1.5"
                        })
                      ]))
                    ], 8, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap w-px" }, [
                    createVNode("div", {
                      class: "text-center text-blue-600 dark:text-blue-200",
                      title: `${unref(t)("sort")}: ${location.sort}`
                    }, toDisplayString(location.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("div", {
                      class: "text-left text-sky-700 dark:text-sky-200",
                      title: locationTitleIn(location)
                    }, toDisplayString(locationTitle(location)), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("div", { class: "text-xs text-violet-700 dark:text-violet-200" }, toDisplayString(location.slug), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("span", {
                        class: ["text-[10px] px-2 py-1 rounded-sm border font-semibold", typeBadgeClass(location.type)]
                      }, toDisplayString(locationTypeTitle(location.type)), 3)
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs",
                      title: location.parent_id ? `ID: ${location.parent_id}` : null
                    }, toDisplayString(parentTitle(location)), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-amber-600 dark:text-amber-200" }, toDisplayString(location.code || "—"), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs font-semibold text-blue-600 dark:text-blue-200" }, toDisplayString(coordinates(location)), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs font-semibold" }, toDisplayString(location.timezone || "—"), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-1" }, [
                      createVNode(_sfc_main$3, {
                        "is-default": location.is_default,
                        title: location.is_default ? "Локация по умолчанию" : "Сделать локацией по умолчанию",
                        onMakeDefault: ($event) => emit("make-default", location)
                      }, null, 8, ["is-default", "title", "onMakeDefault"]),
                      createVNode(_sfc_main$6, {
                        isActive: location.activity,
                        title: location.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", location)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.locations.edit", {
                          location: location.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onClick: ($event) => emit("delete", location)
                      }, null, 8, ["onClick"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedLocations.includes(
                          location.id
                        ),
                        onChange: ($event) => emit(
                          "toggle-select",
                          location.id
                        )
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Location/Table/LocationTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "LocationCardGrid",
  __ssrInlineRender: true,
  props: {
    locations: {
      type: Array,
      default: () => []
    },
    selectedLocations: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "make-default",
    "update-sort",
    "update-sort-order",
    "delete",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localLocations = ref([]);
    watch(
      () => props.locations,
      (newVal) => {
        localLocations.value = JSON.parse(JSON.stringify(newVal || []));
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localLocations.value.map((location) => location.id)
      );
    };
    const locationTranslation = (location) => (location == null ? void 0 : location.translation) || {};
    const locationTitle = (location) => {
      var _a;
      return ((_a = locationTranslation(location)) == null ? void 0 : _a.title) || `ID: ${location == null ? void 0 : location.id}`;
    };
    const locationTitleIn = (location) => {
      var _a;
      return ((_a = locationTranslation(location)) == null ? void 0 : _a.title_in) || "";
    };
    const locationTitleFrom = (location) => {
      var _a;
      return ((_a = locationTranslation(location)) == null ? void 0 : _a.title_from) || "";
    };
    const locationShort = (location) => {
      var _a;
      return ((_a = locationTranslation(location)) == null ? void 0 : _a.short) || "";
    };
    const parentTitle = (location) => {
      var _a, _b;
      return ((_b = (_a = location == null ? void 0 : location.parent) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((location == null ? void 0 : location.parent_id) ? `ID: ${location.parent_id}` : "—");
    };
    const truncateText = (text, maxLength = 100) => {
      if (!text) return "";
      return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + "…" : text;
    };
    const locationTypeTitle = (type) => {
      const types = {
        country: "country",
        region: "region",
        city: "city",
        district: "district"
      };
      return types[type] ? t(types[type]) : type || "—";
    };
    const coordinates = (location) => {
      if ((location == null ? void 0 : location.latitude) === null || (location == null ? void 0 : location.latitude) === void 0 || (location == null ? void 0 : location.longitude) === null || (location == null ? void 0 : location.longitude) === void 0) {
        return "";
      }
      return `${location.latitude} / ${location.longitude}`;
    };
    const typeBadgeClass = (type) => {
      const classes = {
        country: "bg-indigo-100 text-indigo-700 border-indigo-300 dark:bg-indigo-900/40 dark:text-indigo-200",
        region: "bg-purple-100 text-purple-700 border-purple-300 dark:bg-purple-900/40 dark:text-purple-200",
        city: "bg-sky-100 text-sky-700 border-sky-300 dark:bg-sky-900/40 dark:text-sky-200",
        district: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-200"
      };
      return classes[type] || "bg-gray-100 text-gray-700 border-gray-300 dark:bg-gray-800 dark:text-gray-200";
    };
    const allSelected = () => {
      return !!localLocations.value.length && localLocations.value.every(
        (location) => props.selectedLocations.includes(location.id)
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-400 dark:border-slate-500 bg-white dark:bg-slate-700 shadow-lg" }, _attrs))}><div class="flex items-center justify-between border-b border-slate-400 dark:border-slate-500 px-3 py-2"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedLocations.length)}</div>`);
      if (localLocations.value.length) {
        _push(`<label class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allSelected()) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localLocations.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localLocations.value,
          "onUpdate:modelValue": ($event) => localLocations.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd,
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: location }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<article class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100 cursor-move"${ssrRenderAttr("title", `${unref(t)("sort")}: ${location.sort ?? 0}`)}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `${unref(t)("sort")}: ${location.sort ?? 0}`)}${_scopeId}> ID: ${ssrInterpolate(location.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}><div class="${ssrRenderClass([typeBadgeClass(location.type), "rounded-sm border px-2 py-0.5 text-[10px] font-semibold"])}"${_scopeId}>${ssrInterpolate(locationTypeTitle(location.type))}</div><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedLocations.includes(location.id)) ? " checked" : ""}${_scopeId}></div></header><div class="flex flex-col flex-1 px-3 py-3 space-y-3"${_scopeId}><div class="text-center"${_scopeId}><div class="text-base font-semibold text-sky-700 dark:text-sky-200"${ssrRenderAttr("title", locationTitle(location))}${_scopeId}>${ssrInterpolate(locationTitle(location))}</div>`);
              if (location.slug) {
                _push2(`<div class="mt-1 text-[11px] text-violet-700 dark:text-violet-200"${_scopeId}> /${ssrInterpolate(location.slug)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (locationTitleIn(location) || locationTitleFrom(location)) {
                _push2(`<div class="text-center space-y-1"${_scopeId}>`);
                if (locationTitleIn(location)) {
                  _push2(`<div class="text-[11px] text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("in"))}: ${ssrInterpolate(locationTitleIn(location))}</div>`);
                } else {
                  _push2(`<!---->`);
                }
                if (locationTitleFrom(location)) {
                  _push2(`<div class="text-[11px] text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("from"))}: ${ssrInterpolate(locationTitleFrom(location))}</div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (locationShort(location)) {
                _push2(`<div class="font-semibold text-[12px] text-center text-teal-700 dark:text-teal-200"${ssrRenderAttr("title", locationShort(location))}${_scopeId}>${ssrInterpolate(truncateText(locationShort(location)))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="mt-auto text-[11px] text-slate-600 dark:text-slate-300 border-t border-dashed border-slate-300 dark:border-slate-600 pt-2 space-y-1"${_scopeId}><div class="flex justify-between gap-2"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("parent"))}:</span><span class="font-semibold text-right"${ssrRenderAttr(
                "title",
                location.parent_id ? `ID: ${location.parent_id}` : null
              )}${_scopeId}>${ssrInterpolate(parentTitle(location))}</span></div><div class="flex justify-between gap-2"${_scopeId}><span${_scopeId}>Code:</span><span class="font-semibold text-amber-600 dark:text-amber-200"${_scopeId}>${ssrInterpolate(location.code || "—")}</span></div>`);
              if (coordinates(location)) {
                _push2(`<div class="flex justify-between gap-2"${_scopeId}><span${_scopeId}>${ssrInterpolate(unref(t)("coordinates"))}:</span><span class="font-semibold text-right text-blue-600 dark:text-blue-200"${_scopeId}>${ssrInterpolate(coordinates(location))}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              if (location.timezone) {
                _push2(`<div class="flex justify-between gap-2"${_scopeId}><span${_scopeId}>Timezone:</span><span class="font-semibold text-right"${_scopeId}>${ssrInterpolate(location.timezone)}</span></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$3, {
                "is-default": location.is_default,
                title: location.is_default ? "Локация по умолчанию" : "Сделать локацией по умолчанию",
                onMakeDefault: ($event) => emit("make-default", location)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                isActive: location.activity,
                title: location.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", location)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                href: _ctx.route("admin.locations.edit", {
                  location: location.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                onClick: ($event) => emit("delete", location)
              }, null, _parent2, _scopeId));
              _push2(`</div></footer></article>`);
            } else {
              return [
                createVNode("article", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
                  createVNode("header", { class: "flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100 cursor-move",
                        title: `${unref(t)("sort")}: ${location.sort ?? 0}`
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
                        title: `${unref(t)("sort")}: ${location.sort ?? 0}`
                      }, " ID: " + toDisplayString(location.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("div", {
                        class: ["rounded-sm border px-2 py-0.5 text-[10px] font-semibold", typeBadgeClass(location.type)]
                      }, toDisplayString(locationTypeTitle(location.type)), 3),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedLocations.includes(location.id),
                        onChange: ($event) => emit("toggle-select", location.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-3 space-y-3" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("div", {
                        class: "text-base font-semibold text-sky-700 dark:text-sky-200",
                        title: locationTitle(location)
                      }, toDisplayString(locationTitle(location)), 9, ["title"]),
                      location.slug ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "mt-1 text-[11px] text-violet-700 dark:text-violet-200"
                      }, " /" + toDisplayString(location.slug), 1)) : createCommentVNode("", true)
                    ]),
                    locationTitleIn(location) || locationTitleFrom(location) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-center space-y-1"
                    }, [
                      locationTitleIn(location) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[11px] text-slate-600 dark:text-slate-300"
                      }, toDisplayString(unref(t)("in")) + ": " + toDisplayString(locationTitleIn(location)), 1)) : createCommentVNode("", true),
                      locationTitleFrom(location) ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "text-[11px] text-slate-600 dark:text-slate-300"
                      }, toDisplayString(unref(t)("from")) + ": " + toDisplayString(locationTitleFrom(location)), 1)) : createCommentVNode("", true)
                    ])) : createCommentVNode("", true),
                    locationShort(location) ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "font-semibold text-[12px] text-center text-teal-700 dark:text-teal-200",
                      title: locationShort(location)
                    }, toDisplayString(truncateText(locationShort(location))), 9, ["title"])) : createCommentVNode("", true),
                    createVNode("div", { class: "mt-auto text-[11px] text-slate-600 dark:text-slate-300 border-t border-dashed border-slate-300 dark:border-slate-600 pt-2 space-y-1" }, [
                      createVNode("div", { class: "flex justify-between gap-2" }, [
                        createVNode("span", null, toDisplayString(unref(t)("parent")) + ":", 1),
                        createVNode("span", {
                          class: "font-semibold text-right",
                          title: location.parent_id ? `ID: ${location.parent_id}` : null
                        }, toDisplayString(parentTitle(location)), 9, ["title"])
                      ]),
                      createVNode("div", { class: "flex justify-between gap-2" }, [
                        createVNode("span", null, "Code:"),
                        createVNode("span", { class: "font-semibold text-amber-600 dark:text-amber-200" }, toDisplayString(location.code || "—"), 1)
                      ]),
                      coordinates(location) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "flex justify-between gap-2"
                      }, [
                        createVNode("span", null, toDisplayString(unref(t)("coordinates")) + ":", 1),
                        createVNode("span", { class: "font-semibold text-right text-blue-600 dark:text-blue-200" }, toDisplayString(coordinates(location)), 1)
                      ])) : createCommentVNode("", true),
                      location.timezone ? (openBlock(), createBlock("div", {
                        key: 1,
                        class: "flex justify-between gap-2"
                      }, [
                        createVNode("span", null, "Timezone:"),
                        createVNode("span", { class: "font-semibold text-right" }, toDisplayString(location.timezone), 1)
                      ])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("footer", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$3, {
                        "is-default": location.is_default,
                        title: location.is_default ? "Локация по умолчанию" : "Сделать локацией по умолчанию",
                        onMakeDefault: ($event) => emit("make-default", location)
                      }, null, 8, ["is-default", "title", "onMakeDefault"]),
                      createVNode(_sfc_main$6, {
                        isActive: location.activity,
                        title: location.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", location)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$7, {
                        href: _ctx.route("admin.locations.edit", {
                          location: location.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        onClick: ($event) => emit("delete", location)
                      }, null, 8, ["onClick"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Location/View/LocationCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    locations: { type: [Array, Object], default: () => [] },
    locationsCount: { type: Number, default: 0 },
    useServerProcessing: { type: Boolean, default: false },
    adminSystemLocationsProcessingMode: {
      type: String,
      default: "frontend"
    },
    adminSystemLocationsPerPage: {
      type: Number,
      default: 6
    },
    adminSystemLocationsDefaultSort: {
      type: String,
      default: "idDesc"
    },
    adminSystemLocationsDefaultView: {
      type: String,
      default: "table"
    },
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    locationTypes: { type: Array, default: () => [] },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    typeFilter: { type: String, default: "" },
    activityFilter: {
      type: [String, Boolean, Number],
      default: null
    },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_locations") || props.adminSystemLocationsDefaultView || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_locations", value);
    });
    const locationsList = computed(() => {
      var _a;
      if (Array.isArray(props.locations)) return props.locations;
      if (Array.isArray((_a = props.locations) == null ? void 0 : _a.data)) return props.locations.data;
      return [];
    });
    const localLocations = ref([]);
    watch(
      locationsList,
      (newVal) => {
        localLocations.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const currentPage = ref(1);
    const itemsPerPage = ref(
      props.adminSystemLocationsPerPage || 6
    );
    watch(itemsPerPage, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminCountLocations"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(`Показ ${newVal} элементов на странице.`);
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления количества элементов."
            );
          }
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminSystemLocationsDefaultSort || "idDesc"
    );
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortLocations"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing) {
              reloadServerData({
                sort: newVal,
                page: void 0
              });
            }
            toast.info("Сортировка успешно изменена.");
          },
          onError: (errors) => {
            toast.error(
              errors.value || "Ошибка обновления сортировки."
            );
          }
        }
      );
    });
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (currentPage.value - 1) * itemsPerPage.value;
      const sortData = orderedIds.map(
        (id, index) => ({
          id,
          sort: startSort + index + 1
        })
      );
      router.put(
        route(
          "admin.actions.locations.updateSortBulk"
        ),
        {
          items: sortData
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            sortData.forEach(
              ({ id, sort }) => {
                patchLocalLocation(
                  id,
                  (location) => {
                    location.sort = sort;
                  }
                );
              }
            );
            toast.success(
              "Порядок локаций успешно обновлён."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка обновления сортировки локаций:",
              errors
            );
            toast.error(
              errors.general || errors.items || "Не удалось обновить порядок локаций."
            );
            router.reload({
              only: ["locations"],
              preserveScroll: true
            });
          }
        }
      );
    };
    const searchQuery = ref(props.search || "");
    const typeFilter = ref(props.typeFilter || "");
    const normalizeActivityFilter = (value) => {
      if (value === true || value === 1 || value === "1" || value === "true") {
        return "true";
      }
      if (value === false || value === 0 || value === "0" || value === "false") {
        return "false";
      }
      return "";
    };
    const activityFilter = ref(
      normalizeActivityFilter(props.activityFilter)
    );
    const reloadServerData = (overrides = {}) => {
      const currentQuery = Object.fromEntries(
        new URLSearchParams(window.location.search)
      );
      router.get(
        window.location.pathname,
        {
          ...currentQuery,
          search: searchQuery.value || void 0,
          sort: sortParam.value || void 0,
          type: typeFilter.value || void 0,
          activity: activityFilter.value || void 0,
          page: void 0,
          ...overrides
        },
        {
          preserveScroll: true,
          preserveState: false,
          replace: true
        }
      );
    };
    watch(typeFilter, () => {
      currentPage.value = 1;
      if (props.useServerProcessing) {
        reloadServerData();
      }
    });
    watch(activityFilter, () => {
      currentPage.value = 1;
      if (props.useServerProcessing) {
        reloadServerData();
      }
    });
    watch(
      [
        itemsPerPage,
        searchQuery,
        typeFilter,
        activityFilter
      ],
      () => {
        currentPage.value = 1;
      }
    );
    const makeDefaultLocation = (location) => {
      if (!(location == null ? void 0 : location.id)) return;
      if (location.is_default) {
        toast.info(
          "Эта локация уже установлена по умолчанию."
        );
        return;
      }
      const title = getLocationTitle(location);
      router.put(
        route(
          "admin.actions.locations.makeDefault",
          {
            location: location.id
          }
        ),
        {},
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localLocations.value = localLocations.value.map(
              (item) => ({
                ...item,
                is_default: item.id === location.id,
                activity: item.id === location.id ? true : item.activity
              })
            );
            toast.success(
              `Локация "${title}" установлена по умолчанию.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              errors.general || errors[errorKey] || `Не удалось установить локацию "${title}" по умолчанию.`
            );
          }
        }
      );
    };
    const locationTypeTitle = (type) => {
      const types = {
        country: "country",
        region: "region",
        city: "city",
        district: "district"
      };
      return types[type] ? t(types[type]) : type || "—";
    };
    const normalize = (value) => {
      return (value ?? "").toString().trim().toLowerCase();
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const getLocationTranslation = (location) => {
      return (location == null ? void 0 : location.translation) || {};
    };
    const getLocationTitle = (location) => {
      var _a;
      return ((_a = getLocationTranslation(location)) == null ? void 0 : _a.title) || `ID: ${location == null ? void 0 : location.id}`;
    };
    const getLocationTitleIn = (location) => {
      var _a;
      return ((_a = getLocationTranslation(location)) == null ? void 0 : _a.title_in) || "";
    };
    const getLocationTitleFrom = (location) => {
      var _a;
      return ((_a = getLocationTranslation(location)) == null ? void 0 : _a.title_from) || "";
    };
    const getLocationShort = (location) => {
      var _a;
      return ((_a = getLocationTranslation(location)) == null ? void 0 : _a.short) || "";
    };
    const getParentTitle = (location) => {
      var _a, _b;
      return ((_b = (_a = location == null ? void 0 : location.parent) == null ? void 0 : _a.translation) == null ? void 0 : _b.title) || ((location == null ? void 0 : location.parent_id) ? `ID: ${location.parent_id}` : "");
    };
    const patchLocalLocation = (locationId, callback) => {
      const location = localLocations.value.find(
        (item) => item.id === locationId
      );
      if (location) {
        callback(location);
      }
    };
    const byNumberAsc = (field) => (a, b) => {
      return safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    };
    const byNumberDesc = (field) => (a, b) => {
      return safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    };
    const sortLocations = (locations) => {
      const list = (locations || []).slice();
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        titleAsc: (a, b) => normalize(getLocationTitle(a)).localeCompare(
          normalize(getLocationTitle(b)),
          props.currentLocale
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        titleDesc: (a, b) => normalize(getLocationTitle(b)).localeCompare(
          normalize(getLocationTitle(a)),
          props.currentLocale
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        typeAsc: (a, b) => normalize(a == null ? void 0 : a.type).localeCompare(normalize(b == null ? void 0 : b.type)) || safeNumber(a == null ? void 0 : a.sort) - safeNumber(b == null ? void 0 : b.sort),
        typeDesc: (a, b) => normalize(b == null ? void 0 : b.type).localeCompare(normalize(a == null ? void 0 : a.type)) || safeNumber(a == null ? void 0 : a.sort) - safeNumber(b == null ? void 0 : b.sort),
        slugAsc: (a, b) => normalize(a == null ? void 0 : a.slug).localeCompare(normalize(b == null ? void 0 : b.slug)) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        slugDesc: (a, b) => normalize(b == null ? void 0 : b.slug).localeCompare(normalize(a == null ? void 0 : a.slug)) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id)
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredLocations = computed(() => {
      let filtered = (localLocations.value || []).slice();
      if (typeFilter.value) {
        filtered = filtered.filter(
          (location) => (location == null ? void 0 : location.type) === typeFilter.value
        );
      }
      if (activityFilter.value === "true") {
        filtered = filtered.filter(
          (location) => !!(location == null ? void 0 : location.activity)
        );
      }
      if (activityFilter.value === "false") {
        filtered = filtered.filter(
          (location) => !(location == null ? void 0 : location.activity)
        );
      }
      const query = normalize(searchQuery.value);
      if (query) {
        filtered = filtered.filter((location) => {
          const values = [
            location == null ? void 0 : location.id,
            location == null ? void 0 : location.parent_id,
            location == null ? void 0 : location.type,
            location == null ? void 0 : location.slug,
            location == null ? void 0 : location.code,
            location == null ? void 0 : location.latitude,
            location == null ? void 0 : location.longitude,
            location == null ? void 0 : location.timezone,
            location == null ? void 0 : location.sort,
            getLocationTitle(location),
            getLocationTitleIn(location),
            getLocationTitleFrom(location),
            getLocationShort(location),
            getParentTitle(location)
          ];
          return values.some(
            (value) => normalize(value).includes(query)
          );
        });
      }
      return sortLocations(filtered);
    });
    const paginatedLocations = computed(() => {
      const perPage = Number(itemsPerPage.value || 12);
      const start = (currentPage.value - 1) * perPage;
      return filteredLocations.value.slice(
        start,
        start + perPage
      );
    });
    const displayedLocations = computed(() => {
      return props.useServerProcessing ? locationsList.value : paginatedLocations.value;
    });
    const toggleActivity = (location) => {
      if (!(location == null ? void 0 : location.id)) return;
      const newActivity = !location.activity;
      const title = getLocationTitle(location);
      if (location.is_default && !newActivity) {
        toast.warning(
          "Локацию по умолчанию нельзя деактивировать."
        );
        return;
      }
      router.put(
        route(
          "admin.actions.locations.updateActivity",
          { location: location.id }
        ),
        { activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalLocation(
              location.id,
              (item) => {
                item.activity = newActivity;
              }
            );
            toast.success(
              newActivity ? `Локация "${title}" активирована.` : `Локация "${title}" деактивирована.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0];
            toast.error(
              errors.activity || errors.general || errors[errorKey] || `Ошибка изменения активности локации "${title}".`
            );
          }
        }
      );
    };
    const updateLocationSort = (location, sort) => {
      if (!(location == null ? void 0 : location.id)) return;
      const normalizedSort = Number(sort);
      if (!Number.isInteger(normalizedSort) || normalizedSort < 0) {
        toast.warning(
          "Значение сортировки должно быть целым числом от 0."
        );
        return;
      }
      if (Number(location.sort) === normalizedSort) {
        return;
      }
      const title = getLocationTitle(location);
      router.put(
        route(
          "admin.actions.locations.updateSort",
          { location: location.id }
        ),
        { sort: normalizedSort },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchLocalLocation(
              location.id,
              (item) => {
                item.sort = normalizedSort;
              }
            );
            toast.success(
              `Сортировка локации "${title}" обновлена.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0];
            toast.error(
              errors.sort || errors.general || errors[errorKey] || `Ошибка изменения сортировки локации "${title}".`
            );
          }
        }
      );
    };
    const showConfirmDeleteModal = ref(false);
    const locationToDeleteId = ref(null);
    const locationToDeleteTitle = ref("");
    const confirmDelete = (locationOrId, title = null) => {
      if (typeof locationOrId === "object") {
        locationToDeleteId.value = locationOrId.id;
        locationToDeleteTitle.value = title || getLocationTitle(locationOrId);
      } else {
        locationToDeleteId.value = locationOrId;
        locationToDeleteTitle.value = title || `ID: ${locationOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      locationToDeleteId.value = null;
      locationToDeleteTitle.value = "";
    };
    const deleteLocation = () => {
      if (locationToDeleteId.value === null) return;
      const idToDelete = locationToDeleteId.value;
      const titleToDelete = locationToDeleteTitle.value;
      router.delete(
        route(
          "admin.locations.destroy",
          { location: idToDelete }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedLocations.value = selectedLocations.value.filter(
              (id) => id !== idToDelete
            );
            toast.success(
              `Локация "${titleToDelete || "ID: " + idToDelete}" удалена.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0];
            const message = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
            toast.error(
              `${message} (Локация: ${titleToDelete || "ID: " + idToDelete})`
            );
          },
          onFinish: closeModal
        }
      );
    };
    const selectedLocations = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedLocations.value.map(
        (location) => location.id
      );
      if (checked) {
        selectedLocations.value = [
          .../* @__PURE__ */ new Set([
            ...selectedLocations.value,
            ...ids
          ])
        ];
        return;
      }
      selectedLocations.value = selectedLocations.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectLocation = (locationId) => {
      const index = selectedLocations.value.indexOf(locationId);
      if (index > -1) {
        selectedLocations.value.splice(index, 1);
        return;
      }
      selectedLocations.value.push(locationId);
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedLocations.value.length) {
        toast.warning(
          "Выберите локации для изменения активности."
        );
        return;
      }
      const idsToUpdate = [...selectedLocations.value];
      if (!newActivity) {
        const containsDefault = localLocations.value.some(
          (location) => idsToUpdate.includes(location.id) && location.is_default
        );
        if (containsDefault) {
          toast.warning(
            "Локацию по умолчанию нельзя деактивировать."
          );
          return;
        }
      }
      router.put(
        route(
          "admin.actions.locations.bulkUpdateActivity"
        ),
        {
          ids: idsToUpdate,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localLocations.value = localLocations.value.map(
              (location) => {
                if (!idsToUpdate.includes(location.id)) {
                  return location;
                }
                return {
                  ...location,
                  activity: newActivity
                };
              }
            );
            selectedLocations.value = [];
            toast.success(
              newActivity ? "Выбранные локации активированы." : "Выбранные локации деактивированы."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0];
            toast.error(
              errors.ids || errors.activity || errors.general || errors[errorKey] || "Ошибка массового изменения активности локаций."
            );
          }
        }
      );
    };
    const bulkDelete = () => {
      if (!selectedLocations.value.length) {
        toast.warning(
          "Выберите хотя бы одну локацию для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные локации?"
      )) {
        return;
      }
      router.delete(
        route(
          "admin.actions.locations.bulkDestroy"
        ),
        {
          data: {
            ids: [...selectedLocations.value]
          },
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            selectedLocations.value = [];
            toast.success(
              "Выбранные локации успешно удалены."
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0];
            toast.error(
              errors.general || errors[errorKey] || "Произошла ошибка при удалении локаций."
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
      } else if (action === "activate") {
        bulkToggleActivity(true);
      } else if (action === "deactivate") {
        bulkToggleActivity(false);
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("locations")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("locations"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("locations")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("locations")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              href: _ctx.route("admin.locations.create")
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
                  _push3(` ${ssrInterpolate(unref(t)("addLocation"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addLocation")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$a, {
              "setting-key": "adminSystemLocationsProcessingMode",
              mode: __props.adminSystemLocationsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.locationsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.locationsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.locationsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.locationsCount) {
              _push2(`<div class="grid grid-cols-1 sm:grid-cols-2 gap-2 my-3"${_scopeId}><select class="w-full py-1 px-2 text-sm border border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100 rounded-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"${_scopeId}><option value=""${ssrIncludeBooleanAttr(Array.isArray(typeFilter.value) ? ssrLooseContain(typeFilter.value, "") : ssrLooseEqual(typeFilter.value, "")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("allTypes"))}</option><!--[-->`);
              ssrRenderList(__props.locationTypes, (type) => {
                _push2(`<option${ssrRenderAttr("value", type)}${ssrIncludeBooleanAttr(Array.isArray(typeFilter.value) ? ssrLooseContain(typeFilter.value, type) : ssrLooseEqual(typeFilter.value, type)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(locationTypeTitle(type))}</option>`);
              });
              _push2(`<!--]--></select><select class="w-full py-1 px-2 text-sm border border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100 rounded-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"${_scopeId}><option value=""${ssrIncludeBooleanAttr(Array.isArray(activityFilter.value) ? ssrLooseContain(activityFilter.value, "") : ssrLooseEqual(activityFilter.value, "")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("activity"))}</option><option value="true"${ssrIncludeBooleanAttr(Array.isArray(activityFilter.value) ? ssrLooseContain(activityFilter.value, "true") : ssrLooseEqual(activityFilter.value, "true")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("active"))}</option><option value="false"${ssrIncludeBooleanAttr(Array.isArray(activityFilter.value) ? ssrLooseContain(activityFilter.value, "false") : ssrLooseEqual(activityFilter.value, "false")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("inactive"))}</option></select></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.locationsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountLocations"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$4, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": ($event) => sortParam.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.locationsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$f, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.locationsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.locationsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$5, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$g, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.locationsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredLocations.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.locations }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                locations: displayedLocations.value,
                "selected-locations": selectedLocations.value,
                onToggleActivity: toggleActivity,
                onMakeDefault: makeDefaultLocation,
                onUpdateSort: updateLocationSort,
                onUpdateSortOrder: handleSortOrderUpdate,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectLocation,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                locations: displayedLocations.value,
                "selected-locations": selectedLocations.value,
                onToggleActivity: toggleActivity,
                onMakeDefault: makeDefaultLocation,
                onUpdateSort: updateLocationSort,
                onUpdateSortOrder: handleSortOrderUpdate,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectLocation,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            }
            if (__props.locationsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$h, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredLocations.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$i, { pagination: __props.locations }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (!__props.locationsCount) {
              _push2(`<div class="py-8 text-center text-sm text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$j, {
              show: showConfirmDeleteModal.value,
              onClose: closeModal,
              onCancel: closeModal,
              onConfirm: deleteLocation,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$9, {
                      href: _ctx.route("admin.locations.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addLocation")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$a, {
                      "setting-key": "adminSystemLocationsProcessingMode",
                      mode: __props.adminSystemLocationsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.locationsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.locationsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.locationsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.locationsCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "grid grid-cols-1 sm:grid-cols-2 gap-2 my-3"
                  }, [
                    withDirectives(createVNode("select", {
                      "onUpdate:modelValue": ($event) => typeFilter.value = $event,
                      class: "w-full py-1 px-2 text-sm border border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100 rounded-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                    }, [
                      createVNode("option", { value: "" }, toDisplayString(unref(t)("allTypes")), 1),
                      (openBlock(true), createBlock(Fragment, null, renderList(__props.locationTypes, (type) => {
                        return openBlock(), createBlock("option", {
                          key: type,
                          value: type
                        }, toDisplayString(locationTypeTitle(type)), 9, ["value"]);
                      }), 128))
                    ], 8, ["onUpdate:modelValue"]), [
                      [vModelSelect, typeFilter.value]
                    ]),
                    withDirectives(createVNode("select", {
                      "onUpdate:modelValue": ($event) => activityFilter.value = $event,
                      class: "w-full py-1 px-2 text-sm border border-slate-400 dark:border-slate-600 bg-white dark:bg-slate-800 text-gray-900 dark:text-gray-100 rounded-sm focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
                    }, [
                      createVNode("option", { value: "" }, toDisplayString(unref(t)("activity")), 1),
                      createVNode("option", { value: "true" }, toDisplayString(unref(t)("active")), 1),
                      createVNode("option", { value: "false" }, toDisplayString(unref(t)("inactive")), 1)
                    ], 8, ["onUpdate:modelValue"]), [
                      [vModelSelect, activityFilter.value]
                    ])
                  ])) : createCommentVNode("", true),
                  __props.locationsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$d, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$e, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountLocations"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$4, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.locationsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$f, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.locationsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$5, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$g, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.locationsCount ? (openBlock(), createBlock("div", {
                    key: 5,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredLocations.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.locations
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    locations: displayedLocations.value,
                    "selected-locations": selectedLocations.value,
                    onToggleActivity: toggleActivity,
                    onMakeDefault: makeDefaultLocation,
                    onUpdateSort: updateLocationSort,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectLocation,
                    onToggleAll: toggleAll
                  }, null, 8, ["locations", "selected-locations"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 7,
                    locations: displayedLocations.value,
                    "selected-locations": selectedLocations.value,
                    onToggleActivity: toggleActivity,
                    onMakeDefault: makeDefaultLocation,
                    onUpdateSort: updateLocationSort,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectLocation,
                    onToggleAll: toggleAll
                  }, null, 8, ["locations", "selected-locations"])),
                  __props.locationsCount ? (openBlock(), createBlock("div", {
                    key: 8,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$h, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredLocations.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$i, {
                      key: 1,
                      pagination: __props.locations
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  !__props.locationsCount ? (openBlock(), createBlock("div", {
                    key: 9,
                    class: "py-8 text-center text-sm text-slate-600 dark:text-slate-300"
                  }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$j, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteLocation,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/Locations/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
