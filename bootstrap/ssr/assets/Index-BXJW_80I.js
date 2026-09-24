import { ref, watch, mergeProps, unref, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, useSSRContext, computed, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderComponent, ssrRenderAttr, ssrRenderClass } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$6, a as _sfc_main$8, b as _sfc_main$9 } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$5 } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$7 } from "./CountTable-p8tyXGUL.js";
import draggable from "vuedraggable";
import { _ as _sfc_main$4 } from "./IconEdit-Bw90OQvk.js";
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
const _sfc_main$3 = {
  __name: "ImagePresetTable",
  __ssrInlineRender: true,
  props: {
    presets: { type: Array, default: () => [] }
  },
  emits: ["update-sort-order"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const localPresets = ref([]);
    watch(
      () => props.presets,
      (newVal) => localPresets.value = JSON.parse(JSON.stringify(newVal || [])),
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits("update-sort-order", localPresets.value.map((preset) => preset.id));
    };
    const shapeLabel = (shape) => {
      const map = {
        rectangle: "shapeRectangle",
        square: "shapeSquare",
        circle: "shapeCircle"
      };
      return map[shape] ? t(map[shape]) : shape;
    };
    const shapeBadgeClass = (shape) => {
      const classes = {
        rectangle: "bg-blue-600 dark:bg-blue-700 text-white",
        square: "bg-purple-600 dark:bg-purple-700 text-white",
        circle: "bg-pink-600 dark:bg-pink-700 text-white"
      };
      return classes[shape] || "bg-gray-500 text-white";
    };
    const rotationBadgeClass = (type) => {
      const classes = {
        image: "bg-sky-600 dark:bg-sky-700 text-white",
        crop: "bg-cyan-600 dark:bg-cyan-700 text-white"
      };
      return classes[type] || "bg-gray-500 text-white";
    };
    const originalBadgeClass = () => "bg-slate-500 dark:bg-slate-900 text-white";
    const booleanLabel = (value) => value ? t("yes") : t("no");
    const aspectRatio = (preset) => {
      const width = Number(preset.width);
      const height = Number(preset.height);
      return width && height ? (width / height).toFixed(2) : null;
    };
    const maxFileSizeMb = (preset) => {
      return (Number(preset.max_file_size_kb || 0) / 1024).toFixed(2);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="overflow-x-auto">`);
      if (localPresets.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm font-semibold uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-2 py-3 w-px text-center">${ssrInterpolate(unref(t)("id"))}</th><th class="px-2 py-3 whitespace-nowrap text-left">${ssrInterpolate(unref(t)("key"))}</th><th class="px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("shape"))}</th><th class="px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("typeSize"))}</th><th class="px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("file"))}</th><th class="px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("turn"))}</th><th class="px-2 py-3 whitespace-nowrap text-center">${ssrInterpolate(unref(t)("originalShort"))}</th><th class="px-2 py-3 whitespace-nowrap text-end">${ssrInterpolate(unref(t)("actions"))}</th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localPresets.value,
          "onUpdate:modelValue": ($event) => localPresets.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: preset }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 whitespace-nowrap text-center"${_scopeId}><span class="text-xs text-slate-900 dark:text-slate-100"${ssrRenderAttr("title", `sort: ${preset.sort ?? "—"}`)}${_scopeId}>${ssrInterpolate(preset.id)}</span></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><span class="text-amber-600 dark:text-amber-200"${ssrRenderAttr("title", preset.description || "—")}${_scopeId}>${ssrInterpolate(preset.key)}</span></td><td class="px-2 py-3 whitespace-nowrap text-center"${_scopeId}><span class="${ssrRenderClass([shapeBadgeClass(preset.shape), "block w-full py-0.5 px-2 rounded-sm text-xs font-semibold"])}"${_scopeId}>${ssrInterpolate(shapeLabel(preset.shape))}</span></td><td class="px-2 py-3 whitespace-nowrap text-center"${_scopeId}><div class="text-sky-700 dark:text-sky-300"${_scopeId}>${ssrInterpolate(preset.resolution)}</div>`);
              if (aspectRatio(preset)) {
                _push2(`<div class="text-xs text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(aspectRatio(preset))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</td><td class="px-2 py-3 whitespace-nowrap text-center"${_scopeId}><div class="text-indigo-700 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(maxFileSizeMb(preset))} MB </div><div class="text-xs text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(preset.max_file_size_kb)} KB </div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex flex-col items-stretch gap-1"${_scopeId}><span class="${ssrRenderClass([rotationBadgeClass("image"), "block w-full py-0.5 px-2 rounded-sm text-[10px] font-semibold text-center"])}"${_scopeId}>${ssrInterpolate(unref(t)("photo"))}: ${ssrInterpolate(booleanLabel(preset.image_rotation_enabled))}</span><span class="${ssrRenderClass([rotationBadgeClass("crop"), "block w-full py-0.5 px-2 rounded-sm text-[10px] font-semibold text-center"])}"${_scopeId}>${ssrInterpolate(unref(t)("photoFrames"))}: ${ssrInterpolate(booleanLabel(preset.crop_rotation_enabled))}</span></div></td><td class="px-2 py-3 whitespace-nowrap text-center"${_scopeId}><span class="${ssrRenderClass([originalBadgeClass(), "block w-full py-0.5 px-2 rounded-sm text-[10px] font-semibold text-center"])}"${_scopeId}>${ssrInterpolate(unref(t)("originalShort"))}: ${ssrInterpolate(booleanLabel(preset.keep_original))}</span></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-end"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                href: _ctx.route("admin.imagePresets.edit", preset.id)
              }, null, _parent2, _scopeId));
              _push2(`</div></td></tr>`);
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
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap text-center" }, [
                    createVNode("span", {
                      class: "text-xs text-slate-900 dark:text-slate-100",
                      title: `sort: ${preset.sort ?? "—"}`
                    }, toDisplayString(preset.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("span", {
                      class: "text-amber-600 dark:text-amber-200",
                      title: preset.description || "—"
                    }, toDisplayString(preset.key), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap text-center" }, [
                    createVNode("span", {
                      class: ["block w-full py-0.5 px-2 rounded-sm text-xs font-semibold", shapeBadgeClass(preset.shape)]
                    }, toDisplayString(shapeLabel(preset.shape)), 3)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap text-center" }, [
                    createVNode("div", { class: "text-sky-700 dark:text-sky-300" }, toDisplayString(preset.resolution), 1),
                    aspectRatio(preset) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-xs text-slate-500 dark:text-slate-300"
                    }, toDisplayString(aspectRatio(preset)), 1)) : createCommentVNode("", true)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap text-center" }, [
                    createVNode("div", { class: "text-indigo-700 dark:text-indigo-300" }, toDisplayString(maxFileSizeMb(preset)) + " MB ", 1),
                    createVNode("div", { class: "text-xs text-slate-500 dark:text-slate-300" }, toDisplayString(preset.max_file_size_kb) + " KB ", 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex flex-col items-stretch gap-1" }, [
                      createVNode("span", {
                        class: ["block w-full py-0.5 px-2 rounded-sm text-[10px] font-semibold text-center", rotationBadgeClass("image")]
                      }, toDisplayString(unref(t)("photo")) + ": " + toDisplayString(booleanLabel(preset.image_rotation_enabled)), 3),
                      createVNode("span", {
                        class: ["block w-full py-0.5 px-2 rounded-sm text-[10px] font-semibold text-center", rotationBadgeClass("crop")]
                      }, toDisplayString(unref(t)("photoFrames")) + ": " + toDisplayString(booleanLabel(preset.crop_rotation_enabled)), 3)
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap text-center" }, [
                    createVNode("span", {
                      class: ["block w-full py-0.5 px-2 rounded-sm text-[10px] font-semibold text-center", originalBadgeClass()]
                    }, toDisplayString(unref(t)("originalShort")) + ": " + toDisplayString(booleanLabel(preset.keep_original)), 3)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end" }, [
                      createVNode(_sfc_main$4, {
                        href: _ctx.route("admin.imagePresets.edit", preset.id)
                      }, null, 8, ["href"])
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
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/ImagePreset/Table/ImagePresetTable.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "ImagePresetCardGrid",
  __ssrInlineRender: true,
  props: {
    presets: { type: Array, default: () => [] }
  },
  emits: ["update-sort-order"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const localPresets = ref([]);
    watch(
      () => props.presets,
      (newVal) => localPresets.value = JSON.parse(JSON.stringify(newVal || [])),
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emits("update-sort-order", localPresets.value.map((preset) => preset.id));
    };
    const shapeLabel = (shape) => {
      const map = {
        rectangle: "shapeRectangle",
        square: "shapeSquare",
        circle: "shapeCircle"
      };
      return map[shape] ? t(map[shape]) : shape;
    };
    const shapeBadgeClass = (shape) => {
      const classes = {
        rectangle: "bg-blue-600 dark:bg-blue-700 text-white",
        square: "bg-purple-600 dark:bg-purple-700 text-white",
        circle: "bg-pink-600 dark:bg-pink-700 text-white"
      };
      return classes[shape] || "bg-gray-500 text-white";
    };
    const booleanLabel = (value) => value ? t("yes") : t("no");
    const originalBadgeClass = () => "bg-slate-500 dark:bg-slate-900 text-white";
    const aspectRatio = (preset) => {
      const width = Number(preset.width);
      const height = Number(preset.height);
      return width && height ? (width / height).toFixed(2) : null;
    };
    const maxFileSizeMb = (preset) => {
      return (Number(preset.max_file_size_kb || 0) / 1024).toFixed(2);
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}>`);
      if (localPresets.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localPresets.value,
          "onUpdate:modelValue": ($event) => localPresets.value = $event,
          "item-key": "id",
          handle: ".drag-handle",
          onEnd: handleDragEnd,
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
        }, {
          item: withCtx(({ element: preset }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<article class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center gap-2"${_scopeId}><button type="button" class="drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100 cursor-move"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><span class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200"${ssrRenderAttr("title", `sort: ${preset.sort ?? "—"}`)}${_scopeId}> ID: ${ssrInterpolate(preset.id)}</span></div><span class="${ssrRenderClass([shapeBadgeClass(preset.shape), "block w-fit py-0.5 px-2 rounded-sm text-xs font-semibold"])}"${_scopeId}>${ssrInterpolate(shapeLabel(preset.shape))}</span></header><div class="flex flex-col flex-1 px-3 py-3 space-y-3"${_scopeId}><div class="text-center font-semibold text-amber-600 dark:text-amber-200"${ssrRenderAttr("title", preset.description || "—")}${_scopeId}>${ssrInterpolate(preset.key)}</div><div class="text-xs text-center font-semibold text-slate-600 dark:text-slate-300 line-clamp-2 min-h-[32px]"${ssrRenderAttr("title", preset.description)}${_scopeId}>${ssrInterpolate(preset.description || "—")}</div><div class="grid grid-cols-2 gap-2 text-xs text-slate-700 dark:text-slate-100"${_scopeId}><div class="rounded-sm p-2 text-center border border-slate-300 dark:border-slate-600"${_scopeId}><div class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("ratio"))}</div><div class="font-semibold text-sm text-sky-700 dark:text-sky-300"${_scopeId}>${ssrInterpolate(preset.resolution)}</div>`);
              if (aspectRatio(preset)) {
                _push2(`<div class="text-xs text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(aspectRatio(preset))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="rounded-sm border border-slate-300 dark:border-slate-600 p-2 text-center"${_scopeId}><div class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("file"))}</div><div class="font-semibold text-sm text-indigo-700 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(maxFileSizeMb(preset))} MB </div><div class="text-[10px] text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(preset.max_file_size_kb)} KB </div></div><div class="rounded-sm border border-slate-300 dark:border-slate-600 p-2 text-center"${_scopeId}><div class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("photo"))}</div><div class="${ssrRenderClass([preset.image_rotation_enabled ? "text-teal-700 dark:text-teal-300" : "text-red-700 dark:text-red-300", "font-semibold text-sm"])}"${_scopeId}>${ssrInterpolate(booleanLabel(preset.image_rotation_enabled))}</div></div><div class="rounded-sm border border-slate-300 dark:border-slate-600 p-2 text-center"${_scopeId}><div class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("photoFrames"))}</div><div class="${ssrRenderClass([preset.crop_rotation_enabled ? "text-teal-700 dark:text-teal-300" : "text-red-700 dark:text-red-300", "font-semibold text-sm"])}"${_scopeId}>${ssrInterpolate(booleanLabel(preset.crop_rotation_enabled))}</div></div></div><div class="text-center text-xs"${_scopeId}><span class="${ssrRenderClass([originalBadgeClass(), "block w-full py-0.5 px-2 rounded-sm text-[10px] font-semibold text-center"])}"${ssrRenderAttr("title", unref(t)("keepOriginal"))}${_scopeId}>${ssrInterpolate(unref(t)("originalShort"))}: ${ssrInterpolate(booleanLabel(preset.keep_original))}</span></div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                href: _ctx.route("admin.imagePresets.edit", preset.id)
              }, null, _parent2, _scopeId));
              _push2(`</footer></article>`);
            } else {
              return [
                createVNode("article", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
                  createVNode("header", { class: "flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center gap-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100 cursor-move",
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
                        class: "text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-slate-200",
                        title: `sort: ${preset.sort ?? "—"}`
                      }, " ID: " + toDisplayString(preset.id), 9, ["title"])
                    ]),
                    createVNode("span", {
                      class: ["block w-fit py-0.5 px-2 rounded-sm text-xs font-semibold", shapeBadgeClass(preset.shape)]
                    }, toDisplayString(shapeLabel(preset.shape)), 3)
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-3 space-y-3" }, [
                    createVNode("div", {
                      class: "text-center font-semibold text-amber-600 dark:text-amber-200",
                      title: preset.description || "—"
                    }, toDisplayString(preset.key), 9, ["title"]),
                    createVNode("div", {
                      class: "text-xs text-center font-semibold text-slate-600 dark:text-slate-300 line-clamp-2 min-h-[32px]",
                      title: preset.description
                    }, toDisplayString(preset.description || "—"), 9, ["title"]),
                    createVNode("div", { class: "grid grid-cols-2 gap-2 text-xs text-slate-700 dark:text-slate-100" }, [
                      createVNode("div", { class: "rounded-sm p-2 text-center border border-slate-300 dark:border-slate-600" }, [
                        createVNode("div", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("ratio")), 1),
                        createVNode("div", { class: "font-semibold text-sm text-sky-700 dark:text-sky-300" }, toDisplayString(preset.resolution), 1),
                        aspectRatio(preset) ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-xs text-slate-700 dark:text-slate-300"
                        }, toDisplayString(aspectRatio(preset)), 1)) : createCommentVNode("", true)
                      ]),
                      createVNode("div", { class: "rounded-sm border border-slate-300 dark:border-slate-600 p-2 text-center" }, [
                        createVNode("div", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("file")), 1),
                        createVNode("div", { class: "font-semibold text-sm text-indigo-700 dark:text-indigo-300" }, toDisplayString(maxFileSizeMb(preset)) + " MB ", 1),
                        createVNode("div", { class: "text-[10px] text-slate-500 dark:text-slate-400" }, toDisplayString(preset.max_file_size_kb) + " KB ", 1)
                      ]),
                      createVNode("div", { class: "rounded-sm border border-slate-300 dark:border-slate-600 p-2 text-center" }, [
                        createVNode("div", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("photo")), 1),
                        createVNode("div", {
                          class: ["font-semibold text-sm", preset.image_rotation_enabled ? "text-teal-700 dark:text-teal-300" : "text-red-700 dark:text-red-300"]
                        }, toDisplayString(booleanLabel(preset.image_rotation_enabled)), 3)
                      ]),
                      createVNode("div", { class: "rounded-sm border border-slate-300 dark:border-slate-600 p-2 text-center" }, [
                        createVNode("div", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("photoFrames")), 1),
                        createVNode("div", {
                          class: ["font-semibold text-sm", preset.crop_rotation_enabled ? "text-teal-700 dark:text-teal-300" : "text-red-700 dark:text-red-300"]
                        }, toDisplayString(booleanLabel(preset.crop_rotation_enabled)), 3)
                      ])
                    ]),
                    createVNode("div", { class: "text-center text-xs" }, [
                      createVNode("span", {
                        class: ["block w-full py-0.5 px-2 rounded-sm text-[10px] font-semibold text-center", originalBadgeClass()],
                        title: unref(t)("keepOriginal")
                      }, toDisplayString(unref(t)("originalShort")) + ": " + toDisplayString(booleanLabel(preset.keep_original)), 11, ["title"])
                    ])
                  ]),
                  createVNode("footer", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode(_sfc_main$4, {
                      href: _ctx.route("admin.imagePresets.edit", preset.id)
                    }, null, 8, ["href"])
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
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/ImagePreset/View/ImagePresetCardGrid.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: String
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit mt-2 mb-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>────────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9 </option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0 </option><option disabled>────────────────────</option><option value="keyAsc">${ssrInterpolate(unref(t)("key"))} A→Z </option><option value="keyDesc">${ssrInterpolate(unref(t)("key"))} Z→A </option><option disabled>────────────────────</option><option value="widthAsc">${ssrInterpolate(unref(t)("width"))} 0→9 </option><option value="widthDesc">${ssrInterpolate(unref(t)("width"))} 9→0 </option><option value="heightAsc">${ssrInterpolate(unref(t)("height"))} 0→9 </option><option value="heightDesc">${ssrInterpolate(unref(t)("height"))} 9→0 </option><option disabled>────────────────────</option><option value="sizeAsc">${ssrInterpolate(unref(t)("fileSize"))} 0→9 </option><option value="sizeDesc">${ssrInterpolate(unref(t)("fileSize"))} 9→0 </option><option disabled>────────────────────</option><option value="shapeAsc">${ssrInterpolate(unref(t)("shape"))} A→Z </option><option value="shapeDesc">${ssrInterpolate(unref(t)("shape"))} Z→A </option><option disabled>────────────────────</option><option value="allowRotate">${ssrInterpolate(unref(t)("allowRotateShort"))} ${ssrInterpolate(unref(t)("image"))} ON </option><option value="noAllowRotate">${ssrInterpolate(unref(t)("allowRotateShort"))} ${ssrInterpolate(unref(t)("image"))} OFF </option><option value="allowCropRotate">${ssrInterpolate(unref(t)("allowRotateShort"))} ${ssrInterpolate(unref(t)("photoFrames"))} ON </option><option value="noCropRotate">${ssrInterpolate(unref(t)("allowRotateShort"))} ${ssrInterpolate(unref(t)("photoFrames"))} OFF </option><option disabled>────────────────────</option><option value="keepOriginal">${ssrInterpolate(unref(t)("keepOriginal"))} - ${ssrInterpolate(unref(t)("yes"))}</option><option value="noKeepOriginal">${ssrInterpolate(unref(t)("keepOriginal"))} - ${ssrInterpolate(unref(t)("no"))}</option><option disabled>────────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓ </option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑ </option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓ </option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑ </option><option disabled>────────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/ImagePreset/Sort/SortSelect.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    presets: { type: [Array, Object], default: () => [] },
    presetsCount: { type: Number, default: 0 },
    adminImagePresetsPerPage: { type: Number, default: 6 },
    adminImagePresetsDefaultSort: { type: String, default: "idDesc" },
    error: { type: String, default: "" }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const allPresets = computed(() => {
      var _a;
      if (Array.isArray(props.presets)) return props.presets;
      return ((_a = props.presets) == null ? void 0 : _a.data) || [];
    });
    const viewMode = ref(localStorage.getItem("admin_image_presets_view_mode") || "table");
    watch(viewMode, (value) => {
      localStorage.setItem("admin_image_presets_view_mode", value);
    });
    const itemsPerPage = ref(props.adminImagePresetsPerPage || 6);
    watch(
      () => props.adminImagePresetsPerPage,
      (value) => itemsPerPage.value = value || 6
    );
    watch(itemsPerPage, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminCountImagePresets"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error(
            (errors == null ? void 0 : errors.value) || "Ошибка обновления количества элементов."
          )
        }
      );
    });
    const sortParam = ref(props.adminImagePresetsDefaultSort || "idDesc");
    watch(
      () => props.adminImagePresetsDefaultSort,
      (value) => sortParam.value = value || "idDesc"
    );
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortImagePresets"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info("Сортировка успешно изменена"),
          onError: (errors) => toast.error(
            (errors == null ? void 0 : errors.value) || "Ошибка обновления сортировки."
          )
        }
      );
    });
    const searchQuery = ref("");
    const currentPage = ref(1);
    watch(searchQuery, () => currentPage.value = 1);
    const sortPresets = (presets) => {
      const value = sortParam.value;
      const list = presets.slice();
      if (value === "idAsc") return list.sort((a, b) => a.id - b.id);
      if (value === "idDesc") return list.sort((a, b) => b.id - a.id);
      if (value === "sortAsc") {
        return list.sort((a, b) => a.sort !== b.sort ? a.sort - b.sort : a.id - b.id);
      }
      if (value === "sortDesc") {
        return list.sort((a, b) => a.sort !== b.sort ? b.sort - a.sort : b.id - a.id);
      }
      if (value === "keyAsc") {
        return list.sort((a, b) => (a.key || "").localeCompare(b.key || ""));
      }
      if (value === "keyDesc") {
        return list.sort((a, b) => (b.key || "").localeCompare(a.key || ""));
      }
      if (value === "widthAsc") return list.sort((a, b) => a.width - b.width);
      if (value === "widthDesc") return list.sort((a, b) => b.width - a.width);
      if (value === "heightAsc") return list.sort((a, b) => a.height - b.height);
      if (value === "heightDesc") return list.sort((a, b) => b.height - a.height);
      if (value === "sizeAsc") {
        return list.sort((a, b) => a.max_file_size_kb - b.max_file_size_kb);
      }
      if (value === "sizeDesc") {
        return list.sort((a, b) => b.max_file_size_kb - a.max_file_size_kb);
      }
      if (value === "shapeAsc") {
        return list.sort((a, b) => (a.shape || "").localeCompare(b.shape || ""));
      }
      if (value === "shapeDesc") {
        return list.sort((a, b) => (b.shape || "").localeCompare(a.shape || ""));
      }
      if (value === "allowRotate") return list.filter((p) => p.image_rotation_enabled);
      if (value === "noAllowRotate") return list.filter((p) => !p.image_rotation_enabled);
      if (value === "allowCropRotate") return list.filter((p) => p.crop_rotation_enabled);
      if (value === "noCropRotate") return list.filter((p) => !p.crop_rotation_enabled);
      if (value === "keepOriginal") return list.filter((p) => p.keep_original);
      if (value === "noKeepOriginal") return list.filter((p) => !p.keep_original);
      if (value === "createdAtAsc") {
        return list.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      }
      if (value === "createdAtDesc") {
        return list.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      }
      if (value === "updatedAtAsc") {
        return list.sort((a, b) => new Date(a.updated_at) - new Date(b.updated_at));
      }
      if (value === "updatedAtDesc") {
        return list.sort((a, b) => new Date(b.updated_at) - new Date(a.updated_at));
      }
      return list;
    };
    const filteredPresets = computed(() => {
      let filtered = allPresets.value;
      if (searchQuery.value) {
        const query = searchQuery.value.trim().toLowerCase();
        filtered = filtered.filter(
          (preset) => (preset.key || "").toLowerCase().includes(query) || (preset.description || "").toLowerCase().includes(query) || (preset.shape || "").toLowerCase().includes(query) || (preset.resolution || "").toLowerCase().includes(query)
        );
      }
      return sortPresets(filtered);
    });
    const paginatedPresets = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value;
      return filteredPresets.value.slice(start, start + itemsPerPage.value);
    });
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (currentPage.value - 1) * itemsPerPage.value;
      const sortData = orderedIds.map((id, index) => ({
        id,
        sort: startSort + index + 1
      }));
      router.put(
        route("admin.actions.imagePresets.updateSortBulk"),
        { presets: sortData },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.success("Порядок пресетов успешно обновлён."),
          onError: (errors) => {
            console.error("Ошибка обновления сортировки пресетов:", errors);
            toast.error(
              (errors == null ? void 0 : errors.presets) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок пресетов."
            );
            router.reload({
              only: ["presets"],
              preserveScroll: true
            });
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("imagePresets")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("imagePresets"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("imagePresets")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("imagePresets")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}>`);
            if (__props.error) {
              _push2(`<div class="mb-3 p-3 text-sm text-red-700 bg-red-100 border border-red-300 rounded-sm"${_scopeId}>${ssrInterpolate(__props.error)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.presetsCount) {
              _push2(ssrRenderComponent(_sfc_main$5, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: `${unref(t)("search")}...`
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.presetsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between mb-3 gap-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$6, {
                "items-per-page": itemsPerPage.value,
                "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$1, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": ($event) => sortParam.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.presetsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$7, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.presetsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.presetsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$8, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.presetsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                "current-page": currentPage.value,
                "items-per-page": itemsPerPage.value,
                "total-items": filteredPresets.value.length,
                "onUpdate:currentPage": ($event) => currentPage.value = $event,
                "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$3, {
                presets: paginatedPresets.value,
                onUpdateSortOrder: handleSortOrderUpdate
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$2, {
                presets: paginatedPresets.value,
                onUpdateSortOrder: handleSortOrderUpdate
              }, null, _parent2, _scopeId));
            }
            if (__props.presetsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$9, {
                "current-page": currentPage.value,
                "items-per-page": itemsPerPage.value,
                "total-items": filteredPresets.value.length,
                "onUpdate:currentPage": ($event) => currentPage.value = $event,
                "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  __props.error ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "mb-3 p-3 text-sm text-red-700 bg-red-100 border border-red-300 rounded-sm"
                  }, toDisplayString(__props.error), 1)) : createCommentVNode("", true),
                  __props.presetsCount ? (openBlock(), createBlock(_sfc_main$5, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: `${unref(t)("search")}...`
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.presetsCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex flex-col lg:flex-row items-center justify-between mb-3 gap-1"
                  }, [
                    createVNode(_sfc_main$6, {
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"]),
                    createVNode(_sfc_main$1, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.presetsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$7, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.presetsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$8, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.presetsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row"
                  }, [
                    createVNode(_sfc_main$9, {
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredPresets.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage", "onUpdate:itemsPerPage"])
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$3, {
                    key: 5,
                    presets: paginatedPresets.value,
                    onUpdateSortOrder: handleSortOrderUpdate
                  }, null, 8, ["presets"])) : (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    presets: paginatedPresets.value,
                    onUpdateSortOrder: handleSortOrderUpdate
                  }, null, 8, ["presets"])),
                  __props.presetsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    createVNode(_sfc_main$9, {
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredPresets.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage", "onUpdate:itemsPerPage"])
                  ])) : createCommentVNode("", true)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/ImagePresets/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
