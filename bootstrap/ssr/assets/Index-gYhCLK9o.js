import { mergeProps, unref, useSSRContext, computed, ref, watch, resolveComponent, withCtx, createVNode, createTextVNode, toDisplayString, createBlock, createCommentVNode, openBlock } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderStyle, ssrRenderAttr, ssrRenderClass, ssrRenderComponent, ssrIncludeBooleanAttr, ssrRenderList } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
import draggable from "vuedraggable";
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
import { _ as _sfc_main$5 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$6 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$7 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
import "./LocaleSelectOption-BeLdazeX.js";
import "./ResponsiveNavLink-gtte0z5g.js";
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolTrack/Select/BulkActionSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = /* @__PURE__ */ Object.assign({ name: "TrackTreeItem" }, {
  __name: "TrackTreeItem",
  __ssrInlineRender: true,
  props: {
    track: {
      type: Object,
      required: true
    },
    level: {
      type: Number,
      default: 0
    },
    selectedTracks: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "delete",
    "toggle-select",
    "request-drag-end"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const storageKey = computed(() => {
      return `admin.school.tracks.tree.expanded.${props.track.id}`;
    });
    const isExpanded = ref(true);
    const localChildren = ref([]);
    const readExpandedState = () => {
      const savedValue = localStorage.getItem(storageKey.value);
      if (savedValue === null) {
        return true;
      }
      return savedValue === "1";
    };
    isExpanded.value = readExpandedState();
    watch(
      () => props.track.id,
      () => {
        isExpanded.value = readExpandedState();
      }
    );
    watch(
      () => props.track.children,
      (children) => {
        localChildren.value = Array.isArray(children) ? [...children] : [];
      },
      {
        immediate: true,
        deep: true
      }
    );
    const handleInnerDragEnd = (event) => {
      emit("request-drag-end", event);
    };
    const getPrimaryImage = (track) => {
      if ((track == null ? void 0 : track.images) && track.images.length) {
        return [...track.images].sort((a, b) => {
          var _a, _b;
          const aOrder = Number((a == null ? void 0 : a.order) ?? ((_a = a == null ? void 0 : a.pivot) == null ? void 0 : _a.order) ?? 0);
          const bOrder = Number((b == null ? void 0 : b.order) ?? ((_b = b == null ? void 0 : b.pivot) == null ? void 0 : _b.order) ?? 0);
          return aOrder - bOrder;
        })[0];
      }
      return null;
    };
    const trackName = (track) => {
      var _a;
      return ((_a = track == null ? void 0 : track.translation) == null ? void 0 : _a.name) || `ID: ${track == null ? void 0 : track.id}`;
    };
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c, _d;
      const _component_TrackTreeItem = resolveComponent("TrackTreeItem", true);
      _push(`<div${ssrRenderAttrs(_attrs)}><div class="category-item mb-1" style="${ssrRenderStyle({ marginLeft: __props.level * 20 + "px" })}"><div class="flex items-center justify-between py-1 px-2 border border-gray-400 rounded-sm bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 transition duration-150 ease-in-out"><div class="flex items-center space-x-2 flex-grow min-w-0"><span class="handle cursor-move mr-1 flex-shrink-0"${ssrRenderAttr("title", unref(t)("dragDrop"))}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-4 h-4"><path class="fill-current text-sky-500 dark:text-sky-200" d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8l32 0 0 96-96 0 0-32c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c9.2 9.2 22.9 11.9 34.9 6.9s19.8-16.6 19.8-29.6l0-32 96 0 0 96-32 0c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8l-32 0 0-96 96 0 0 32c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-9.2-9.2-22.9-11.9-34.9-6.9s-19.8 16.6-19.8 29.6l0 32-96 0 0-96 32 0c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-64-64z"></path></svg></span>`);
      if (__props.track.children && __props.track.children.length) {
        _push(`<button type="button"${ssrRenderAttr("title", isExpanded.value ? unref(t)("collapse") : unref(t)("expand"))} class="flex-shrink-0 text-slate-900 hover:text-red-500 dark:text-slate-100 dark:hover:text-red-200"><svg class="${ssrRenderClass([{ "rotate-90": isExpanded.value }, "w-5 h-5 transform transition-transform duration-150"])}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>`);
      } else {
        _push(`<span class="w-4 h-4 inline-block flex-shrink-0"></span>`);
      }
      _push(`<span class="w-8 font-semibold text-sm text-amber-600 dark:text-amber-200 mr-1 flex-shrink-0"${ssrRenderAttr("title", `ID: ${__props.track.id} / sort: ${__props.track.sort}`)}>${ssrInterpolate(__props.track.id)}</span><a${ssrRenderAttr("href", `/school/tracks/${encodeURIComponent(__props.track.slug)}`)} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-sky-700 dark:text-sky-200 hover:text-amber-700 dark:hover:text-amber-200 hover:underline line-clamp-2 text-center">${ssrInterpolate(trackName(__props.track))}</a>`);
      if (__props.track.courses_count > 0) {
        _push(`<span class="shrink-0 px-1.5 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 flex items-center justify-center gap-1"${ssrRenderAttr("title", `${unref(t)("courses")}: ${__props.track.courses_count ?? 0}`)}><svg class="h-4 w-4 text-sky-600/85 dark:text-sky-300/85" fill="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path></svg><span class="text-[10px] text-slate-700 dark:text-slate-200">${ssrInterpolate(__props.track.courses_count ?? 0)}</span></span>`);
      } else {
        _push(`<!---->`);
      }
      if (__props.track.children_count > 0) {
        _push(`<span class="shrink-0 px-1.5 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 flex items-center justify-center gap-1"${ssrRenderAttr("title", `${unref(t)("subheadings")}: ${__props.track.children_count ?? 0}`)}><span class="text-[10px] text-slate-700 dark:text-slate-200">${ssrInterpolate(__props.track.children_count ?? 0)}</span></span>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="flex items-center space-x-1 flex-shrink-0 ml-4">`);
      if (__props.track.views > 0) {
        _push(`<div class="flex items-center gap-1"><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg><div class="w-7 font-semibold text-[10px] text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("views"))}>${ssrInterpolate(__props.track.views ?? 0)}</div></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="flex justify-center">`);
      if (__props.track.images && __props.track.images.length) {
        _push(`<img${ssrRenderAttr("src", ((_a = getPrimaryImage(__props.track)) == null ? void 0 : _a.webp_url) || ((_b = getPrimaryImage(__props.track)) == null ? void 0 : _b.url))}${ssrRenderAttr("alt", ((_c = getPrimaryImage(__props.track)) == null ? void 0 : _c.alt) || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", ((_d = getPrimaryImage(__props.track)) == null ? void 0 : _d.caption) || unref(t)("postImage"))} class="h-6 w-8 object-cover rounded-sm border border-slate-400 dark:border-slate-200 p-0.5">`);
      } else {
        _push(`<img src="/storage/school/school_track_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-6 w-8 object-cover rounded-sm border border-slate-400 dark:border-slate-200 p-0.5">`);
      }
      _push(`</div>`);
      _push(ssrRenderComponent(_sfc_main$5, {
        isActive: __props.track.activity,
        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", __props.track),
        title: __props.track.activity ? unref(t)("enabled") : unref(t)("disabled")
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$6, {
        href: _ctx.route("admin.schoolTracks.edit", {
          schoolTrack: __props.track.id
        })
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$7, {
        onClick: ($event) => _ctx.$emit("delete", __props.track)
      }, null, _parent));
      _push(`<div class="pl-1.5"><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedTracks.includes(__props.track.id)) ? " checked" : ""} class="form-checkbox rounded-sm text-indigo-500 flex-shrink-0"></div></div></div></div><div style="${ssrRenderStyle(isExpanded.value && localChildren.value.length ? null : { display: "none" })}" class="children-container mt-1">`);
      _push(ssrRenderComponent(unref(draggable), {
        modelValue: localChildren.value,
        "onUpdate:modelValue": ($event) => localChildren.value = $event,
        tag: "div",
        "item-key": "id",
        handle: ".handle",
        group: "tracks",
        onEnd: handleInnerDragEnd,
        class: "category-tree-children",
        "data-parent-id": __props.track.id
      }, {
        item: withCtx(({ element: childTrack }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_TrackTreeItem, {
              track: childTrack,
              level: __props.level + 1,
              "selected-tracks": __props.selectedTracks,
              onToggleActivity: (item) => _ctx.$emit("toggle-activity", item),
              onDelete: (item) => _ctx.$emit("delete", item),
              onToggleSelect: (id) => _ctx.$emit("toggle-select", id),
              onRequestDragEnd: handleInnerDragEnd
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_TrackTreeItem, {
                track: childTrack,
                level: __props.level + 1,
                "selected-tracks": __props.selectedTracks,
                onToggleActivity: (item) => _ctx.$emit("toggle-activity", item),
                onDelete: (item) => _ctx.$emit("delete", item),
                onToggleSelect: (id) => _ctx.$emit("toggle-select", id),
                onRequestDragEnd: handleInnerDragEnd
              }, null, 8, ["track", "level", "selected-tracks", "onToggleActivity", "onDelete", "onToggleSelect"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div>`);
    };
  }
});
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolTrack/Tree/TrackTreeItem.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "TrackCardGrid",
  __ssrInlineRender: true,
  props: {
    tracks: {
      type: Array,
      default: () => []
    },
    selectedTracks: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "delete",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const getPrimaryImage = (track) => {
      if ((track == null ? void 0 : track.images) && track.images.length) {
        return [...track.images].sort((a, b) => {
          var _a, _b;
          const aOrder = Number((a == null ? void 0 : a.order) ?? ((_a = a == null ? void 0 : a.pivot) == null ? void 0 : _a.order) ?? 0);
          const bOrder = Number((b == null ? void 0 : b.order) ?? ((_b = b == null ? void 0 : b.pivot) == null ? void 0 : _b.order) ?? 0);
          return aOrder - bOrder;
        })[0];
      }
      return null;
    };
    const truncateText = (text, maxLength = 80) => {
      if (!text) return "";
      return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + "…" : text;
    };
    const parentName = (track) => {
      var _a, _b;
      return ((_b = (_a = track == null ? void 0 : track.parent) == null ? void 0 : _a.translation) == null ? void 0 : _b.name) || t("noData");
    };
    const parentTitle = (track) => {
      var _a;
      const parent = track == null ? void 0 : track.parent;
      if (!parent) {
        return t("noData");
      }
      const name = ((_a = parent == null ? void 0 : parent.translation) == null ? void 0 : _a.name) || "";
      return `${name}${parent.slug ? " — " + parent.slug : ""}`.trim();
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedTracks.length)}</div>`);
      if (__props.tracks.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (__props.tracks.length) {
        _push(`<div class="p-3"><div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"><!--[-->`);
        ssrRenderList(__props.tracks, (track) => {
          var _a, _b, _c, _d, _e, _f, _g;
          _push(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center space-x-2"><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `ID: ${track.id} / sort: ${track.sort}`)}> ID: ${ssrInterpolate(track.id)}</div></div><div class="flex items-center space-x-2">`);
          if ((track.views ?? 0) > 0) {
            _push(`<div class="flex items-center space-x-1"><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg><span class="text-[10px] text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("views"))}>${ssrInterpolate(track.views ?? 0)}</span></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedTracks.includes(track.id)) ? " checked" : ""}></div></header><div class="relative w-full bg-slate-200 dark:bg-slate-900">`);
          if (track.images && track.images.length) {
            _push(`<img${ssrRenderAttr("src", ((_a = getPrimaryImage(track)) == null ? void 0 : _a.webp_url) || ((_b = getPrimaryImage(track)) == null ? void 0 : _b.url))}${ssrRenderAttr("alt", ((_c = getPrimaryImage(track)) == null ? void 0 : _c.alt) || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", ((_d = getPrimaryImage(track)) == null ? void 0 : _d.caption) || unref(t)("postImage"))} class="h-32 w-full object-cover">`);
          } else {
            _push(`<img src="/storage/school/school_track_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-32 w-full object-cover">`);
          }
          _push(`</div><div class="flex flex-col flex-1 px-3 py-2 space-y-2"><div class="flex items-center justify-center text-center"><a${ssrRenderAttr("href", `/school/tracks/${encodeURIComponent(track.slug)}`)} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-sky-700 dark:text-sky-200 hover:text-amber-700 dark:hover:text-amber-200 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", ((_e = track.translation) == null ? void 0 : _e.name) || `ID: ${track.id}`)}>${ssrInterpolate(truncateText(((_f = track.translation) == null ? void 0 : _f.name) || `ID: ${track.id}`))}</a></div>`);
          if (track.parent) {
            _push(`<div class="flex items-center justify-center text-center"><div class="flex flex-col text-[11px] font-semibold leading-tight line-clamp-1"${ssrRenderAttr("title", parentTitle(track))}><span class="text-gray-500 dark:text-gray-400">${ssrInterpolate(unref(t)("parentCategory"))}: </span><span class="text-slate-800 dark:text-slate-200">${ssrInterpolate(parentName(track))}</span></div></div>`);
          } else {
            _push(`<!---->`);
          }
          if ((_g = track.translation) == null ? void 0 : _g.short) {
            _push(`<div class="font-semibold text-[12px] text-center text-teal-700 dark:text-teal-200">${ssrInterpolate(truncateText(track.translation.short))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="flex items-center justify-center gap-3 text-[11px] text-slate-600 dark:text-slate-300">`);
          if (track.children_count > 0) {
            _push(`<span class="px-2 py-1 rounded-sm border border-slate-500 dark:border-slate-400 flex items-center justify-center gap-1"${ssrRenderAttr("title", unref(t)("subheadings"))}><span class="font-semibold">${ssrInterpolate(track.children_count ?? 0)}</span></span>`);
          } else {
            _push(`<!---->`);
          }
          if (track.courses_count > 0) {
            _push(`<span class="px-2 py-1 rounded-sm border border-slate-500 dark:border-slate-400 flex items-center justify-center gap-1"${ssrRenderAttr("title", unref(t)("courses"))}><svg class="h-4 w-4 text-sky-600/85 dark:text-sky-300/85" fill="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"></path></svg><span class="font-semibold">${ssrInterpolate(track.courses_count ?? 0)}</span></span>`);
          } else {
            _push(`<!---->`);
          }
          if (track.likes_count > 0) {
            _push(`<span class="px-2 py-1 rounded-sm border border-slate-500 dark:border-slate-400 flex items-center justify-center gap-1"${ssrRenderAttr("title", unref(t)("likes"))}><span class="font-semibold">${ssrInterpolate(track.likes_count ?? 0)}</span></span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center justify-center space-x-1">`);
          _push(ssrRenderComponent(_sfc_main$5, {
            isActive: track.activity,
            onToggleActivity: ($event) => _ctx.$emit("toggle-activity", track),
            title: track.activity ? unref(t)("enabled") : unref(t)("disabled")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$6, {
            href: _ctx.route("admin.schoolTracks.edit", {
              schoolTrack: track.id
            })
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$7, {
            onClick: ($event) => _ctx.$emit("delete", track)
          }, null, _parent));
          _push(`</div></footer></div>`);
        });
        _push(`<!--]--></div></div>`);
      } else {
        _push(`<div class="p-4 text-center text-slate-500 dark:text-slate-400">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolTrack/View/TrackCardGrid.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} ↑</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} ↓</option><option disabled>─────────────────</option><option value="nameAsc">${ssrInterpolate(unref(t)("title"))} ↑</option><option value="nameDesc">${ssrInterpolate(unref(t)("title"))} ↓</option><option disabled>─────────────────</option><option value="slugAsc">Slug A→Z</option><option value="slugDesc">Slug Z→A</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option value="likesDesc">${ssrInterpolate(unref(t)("likes"))} 9→0</option><option value="likesAsc">${ssrInterpolate(unref(t)("likes"))} 0→9</option><option disabled>─────────────────</option><option value="parentAsc">${ssrInterpolate(unref(t)("parentCategory"))} 0→9</option><option value="parentDesc">${ssrInterpolate(unref(t)("parentCategory"))} 9→0</option><option value="parentNameAsc">${ssrInterpolate(unref(t)("parentCategory"))} A→Z</option><option value="parentNameDesc">${ssrInterpolate(unref(t)("parentCategory"))} Z→A</option><option value="childrenDesc">${ssrInterpolate(unref(t)("children"))} 9→0</option><option value="childrenAsc">${ssrInterpolate(unref(t)("children"))} 0→9</option><option disabled>─────────────────</option><option value="coursesDesc">${ssrInterpolate(unref(t)("courses"))} 9→0</option><option value="coursesAsc">${ssrInterpolate(unref(t)("courses"))} 0→9</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>─────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolTrack/Sort/SortSelect.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminSchoolTracksProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    tracksTree: { type: Array, default: () => [] },
    tracks: { type: [Array, Object], default: () => [] },
    tracksCount: { type: Number, default: 0 },
    adminSchoolTracksPerPage: { type: Number, default: 6 },
    adminSchoolTracksDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(localStorage.getItem("admin_view_mode_tracks") || "table");
    watch(viewMode, (val) => {
      localStorage.setItem("admin_view_mode_tracks", val);
    });
    const tracksList = computed(() => {
      var _a;
      if (Array.isArray(props.tracks)) {
        return props.tracks;
      }
      if (Array.isArray((_a = props.tracks) == null ? void 0 : _a.data)) {
        return props.tracks.data;
      }
      return [];
    });
    const localTracksTree = ref([]);
    watch(
      () => props.tracksTree,
      (newVal) => {
        localTracksTree.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const localTracksFlat = ref([]);
    watch(
      tracksList,
      (newVal) => {
        localTracksFlat.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const itemsPerPage = ref(props.adminSchoolTracksPerPage || 6);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountSchoolTracks"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminSchoolTracksDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortSchoolTracks"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing && viewMode.value !== "table") {
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
    const getTrackName = (track) => {
      var _a;
      return ((_a = track == null ? void 0 : track.translation) == null ? void 0 : _a.name) || `ID: ${track == null ? void 0 : track.id}`;
    };
    const getTrackShort = (track) => {
      var _a;
      return ((_a = track == null ? void 0 : track.translation) == null ? void 0 : _a.short) || "";
    };
    const getTrackDescription = (track) => {
      var _a;
      return ((_a = track == null ? void 0 : track.translation) == null ? void 0 : _a.description) || "";
    };
    const getTrackSlug = (track) => {
      return (track == null ? void 0 : track.slug) || "";
    };
    const getParentName = (track) => {
      var _a, _b;
      return ((_b = (_a = track == null ? void 0 : track.parent) == null ? void 0 : _a.translation) == null ? void 0 : _b.name) || "";
    };
    const getParentSortValue = (track) => {
      return getParentName(track) || (track == null ? void 0 : track.parent_id) || "";
    };
    const getNestedTitle = (item) => {
      var _a, _b, _c, _d, _e, _f;
      return (item == null ? void 0 : item.title) || (item == null ? void 0 : item.name) || ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || ((_b = item == null ? void 0 : item.translation) == null ? void 0 : _b.name) || ((_d = (_c = item == null ? void 0 : item.translations) == null ? void 0 : _c[0]) == null ? void 0 : _d.title) || ((_f = (_e = item == null ? void 0 : item.translations) == null ? void 0 : _e[0]) == null ? void 0 : _f.name) || "";
    };
    const getCoursesText = (track) => {
      const courses = Array.isArray(track == null ? void 0 : track.courses) ? track.courses : [];
      return courses.map(getNestedTitle).filter(Boolean).join(" ");
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortTracks = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") return list.filter((item) => !!item.activity);
      if (sortParam.value === "inactive") return list.filter((item) => !item.activity);
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        parentAsc: byNumberAsc("parent_id"),
        parentDesc: byNumberDesc("parent_id"),
        nameAsc: (a, b) => normalize(getTrackName(a)).localeCompare(normalize(getTrackName(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        nameDesc: (a, b) => normalize(getTrackName(b)).localeCompare(normalize(getTrackName(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        slugAsc: (a, b) => normalize(getTrackSlug(a)).localeCompare(normalize(getTrackSlug(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        slugDesc: (a, b) => normalize(getTrackSlug(b)).localeCompare(normalize(getTrackSlug(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        viewsAsc: byNumberAsc("views"),
        viewsDesc: byNumberDesc("views"),
        likesAsc: byNumberAsc("likes_count"),
        likesDesc: byNumberDesc("likes_count"),
        childrenAsc: byNumberAsc("children_count"),
        childrenDesc: byNumberDesc("children_count"),
        coursesAsc: byNumberAsc("courses_count"),
        coursesDesc: byNumberDesc("courses_count"),
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        dateAsc: (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        dateDesc: (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        createdAtAsc: (a, b) => safeDate(a == null ? void 0 : a.created_at) - safeDate(b == null ? void 0 : b.created_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        createdAtDesc: (a, b) => safeDate(b == null ? void 0 : b.created_at) - safeDate(a == null ? void 0 : a.created_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        updatedAtAsc: (a, b) => safeDate(a == null ? void 0 : a.updated_at) - safeDate(b == null ? void 0 : b.updated_at) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        updatedAtDesc: (a, b) => safeDate(b == null ? void 0 : b.updated_at) - safeDate(a == null ? void 0 : a.updated_at) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        parentNameAsc: (a, b) => normalize(getParentSortValue(a)).localeCompare(normalize(getParentSortValue(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        parentNameDesc: (a, b) => normalize(getParentSortValue(b)).localeCompare(normalize(getParentSortValue(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id)
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredTracks = computed(() => {
      let filtered = localTracksFlat.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortTracks(filtered);
      }
      filtered = filtered.filter((track) => {
        const name = normalize(getTrackName(track));
        const slug = normalize(getTrackSlug(track));
        const short = normalize(getTrackShort(track));
        const description = normalize(getTrackDescription(track));
        const parentName = normalize(getParentName(track));
        const courses = normalize(getCoursesText(track));
        return name.includes(query) || slug.includes(query) || short.includes(query) || description.includes(query) || parentName.includes(query) || courses.includes(query);
      });
      return sortTracks(filtered);
    });
    const paginatedTracks = computed(() => {
      const per = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * per;
      return filteredTracks.value.slice(start, start + per);
    });
    const displayedTracks = computed(() => {
      return props.useServerProcessing ? tracksList.value : paginatedTracks.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const showConfirmDeleteModal = ref(false);
    const trackToDeleteId = ref(null);
    const trackToDeleteName = ref("");
    const confirmDelete = (trackOrId, name = null) => {
      if (typeof trackOrId === "object") {
        trackToDeleteId.value = trackOrId.id;
        trackToDeleteName.value = name || getTrackName(trackOrId);
      } else {
        trackToDeleteId.value = trackOrId;
        trackToDeleteName.value = name || `ID: ${trackOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      trackToDeleteId.value = null;
      trackToDeleteName.value = "";
    };
    const deleteTrack = () => {
      if (trackToDeleteId.value === null) return;
      const idToDelete = trackToDeleteId.value;
      const nameToDelete = trackToDeleteName.value;
      router.delete(route("admin.schoolTracks.destroy", { schoolTrack: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Трек "${nameToDelete || "ID: " + idToDelete}" удалён.`),
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Трек: ${nameToDelete || "ID: " + idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    const patchTrackInTree = (nodes, trackId, callback) => {
      var _a;
      for (const node of nodes) {
        if (node.id === trackId) {
          callback(node);
          return true;
        }
        if (((_a = node.children) == null ? void 0 : _a.length) && patchTrackInTree(node.children, trackId, callback)) {
          return true;
        }
      }
      return false;
    };
    const patchTrackInFlat = (trackId, callback) => {
      const index = localTracksFlat.value.findIndex((track) => track.id === trackId);
      if (index !== -1) {
        callback(localTracksFlat.value[index]);
      }
    };
    const handleDragEnd = () => {
      const changes = [];
      const updateSortAndCollectChanges = (nodes, parentId) => {
        nodes.forEach((node, index) => {
          var _a;
          let changed = false;
          if (node.sort !== index) {
            node.sort = index;
            changed = true;
          }
          if (node.parent_id !== parentId) {
            node.parent_id = parentId;
            changed = true;
          }
          if (changed) {
            changes.push({
              id: node.id,
              sort: node.sort,
              parent_id: parentId
            });
          }
          if ((_a = node.children) == null ? void 0 : _a.length) {
            updateSortAndCollectChanges(node.children, node.id);
          }
        });
      };
      updateSortAndCollectChanges(localTracksTree.value, null);
      if (!changes.length) return;
      router.put(
        route("admin.actions.schoolTracks.updateSortBulk"),
        { items: changes },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.success("Иерархия треков успешно обновлена."),
          onError: (errors) => {
            console.error("Ошибка обновления сортировки:", errors);
            toast.error(errors.message || "Ошибка обновления иерархии треков.");
            router.reload({
              only: ["tracksTree", "tracks"],
              preserveScroll: true
            });
          }
        }
      );
    };
    const selectedTracks = ref([]);
    const getAllIds = (nodes) => {
      let ids = [];
      nodes.forEach((node) => {
        var _a;
        ids.push(node.id);
        if ((_a = node.children) == null ? void 0 : _a.length) {
          ids = ids.concat(getAllIds(node.children));
        }
      });
      return ids;
    };
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = viewMode.value === "table" ? getAllIds(localTracksTree.value) : displayedTracks.value.map((track) => track.id);
      selectedTracks.value = checked ? ids : [];
    };
    const toggleAllCards = ({ ids, checked }) => {
      selectedTracks.value = checked ? [...ids] : [];
    };
    const toggleSelectTrack = (trackId) => {
      const index = selectedTracks.value.indexOf(trackId);
      if (index > -1) {
        selectedTracks.value.splice(index, 1);
      } else {
        selectedTracks.value.push(trackId);
      }
    };
    const updateActivityByIds = (nodes, ids, activity) => {
      nodes.forEach((node) => {
        var _a;
        if (ids.includes(node.id)) {
          node.activity = activity;
        }
        if ((_a = node.children) == null ? void 0 : _a.length) {
          updateActivityByIds(node.children, ids, activity);
        }
      });
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedTracks.value.length) {
        toast.warning("Выберите треки для активации/деактивации.");
        return;
      }
      const idsToUpdate = [...selectedTracks.value];
      router.put(route("admin.actions.schoolTracks.bulkUpdateActivity"), {
        ids: idsToUpdate,
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          updateActivityByIds(localTracksTree.value, idsToUpdate, newActivity);
          localTracksFlat.value = localTracksFlat.value.map((item) => {
            return idsToUpdate.includes(item.id) ? { ...item, activity: newActivity } : item;
          });
          selectedTracks.value = [];
          toast.success("Активность выбранных треков обновлена.");
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности.");
        }
      });
    };
    const bulkDelete = () => {
      if (!selectedTracks.value.length) {
        toast.warning("Выберите хотя бы один трек для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные треки?")) return;
      router.delete(route("admin.actions.schoolTracks.bulkDestroy"), {
        data: { ids: selectedTracks.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          selectedTracks.value = [];
          toast.success("Выбранные треки успешно удалены.");
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          toast.error(errors[errorKey] || "Ошибка при массовом удалении треков.");
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
    const toggleActivity = (track) => {
      const newActivity = !track.activity;
      const trackName = getTrackName(track);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route("admin.actions.schoolTracks.updateActivity", { schoolTrack: track.id }),
        { activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchTrackInTree(localTracksTree.value, track.id, (node) => {
              node.activity = newActivity;
            });
            patchTrackInFlat(track.id, (node) => {
              node.activity = newActivity;
            });
            track.activity = newActivity;
            toast.success(`Трек "${trackName}" ${actionText}.`);
          },
          onError: (errors) => {
            toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${trackName}".`);
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("tracks")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("tracks"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("tracks")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("tracks")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto" data-v-ad3a839c${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" data-v-ad3a839c${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3" data-v-ad3a839c${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              href: _ctx.route("admin.schoolTracks.create")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addLearningCategory"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addLearningCategory")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            if (viewMode.value !== "table") {
              _push2(ssrRenderComponent(_sfc_main$9, {
                "setting-key": "adminSchoolTracksProcessingMode",
                mode: __props.adminSchoolTracksProcessingMode,
                "use-server-processing": __props.useServerProcessing,
                total: __props.tracksCount
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
            if (__props.tracksCount && viewMode.value !== "table" && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.tracksCount && viewMode.value !== "table" && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.tracksCount && viewMode.value !== "table") {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3" data-v-ad3a839c${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolTracks"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$1, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (val) => sortParam.value = val
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.tracksCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3 mb-3" data-v-ad3a839c${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.tracksCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.tracksCount), 1)
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
            if (__props.tracksCount && viewMode.value !== "table") {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3" data-v-ad3a839c${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredTracks.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.tracks }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(`<div class="border border-gray-400 bg-white dark:bg-slate-800" data-v-ad3a839c${_scopeId}>`);
              _push2(ssrRenderComponent(unref(draggable), {
                modelValue: localTracksTree.value,
                "onUpdate:modelValue": ($event) => localTracksTree.value = $event,
                tag: "div",
                "item-key": "id",
                handle: ".handle",
                group: "tracks",
                onEnd: handleDragEnd,
                class: "category-tree-root p-1",
                "data-parent-id": null
              }, {
                item: withCtx(({ element: track }, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(ssrRenderComponent(_sfc_main$3, {
                      track,
                      level: 0,
                      "selected-tracks": selectedTracks.value,
                      onToggleActivity: toggleActivity,
                      onDelete: confirmDelete,
                      onToggleSelect: toggleSelectTrack,
                      onRequestDragEnd: handleDragEnd
                    }, null, _parent3, _scopeId2));
                  } else {
                    return [
                      createVNode(_sfc_main$3, {
                        track,
                        level: 0,
                        "selected-tracks": selectedTracks.value,
                        onToggleActivity: toggleActivity,
                        onDelete: confirmDelete,
                        onToggleSelect: toggleSelectTrack,
                        onRequestDragEnd: handleDragEnd
                      }, null, 8, ["track", "selected-tracks"])
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(ssrRenderComponent(_sfc_main$2, {
                tracks: displayedTracks.value,
                "selected-tracks": selectedTracks.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectTrack,
                onToggleAll: toggleAllCards
              }, null, _parent2, _scopeId));
            }
            if (__props.tracksCount && viewMode.value !== "table") {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3" data-v-ad3a839c${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredTracks.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.tracks }, null, _parent2, _scopeId));
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
              onConfirm: deleteTrack,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$8, {
                      href: _ctx.route("admin.schoolTracks.create")
                    }, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("addLearningCategory")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    viewMode.value !== "table" ? (openBlock(), createBlock(_sfc_main$9, {
                      key: 0,
                      "setting-key": "adminSchoolTracksProcessingMode",
                      mode: __props.adminSchoolTracksProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.tracksCount
                    }, null, 8, ["mode", "use-server-processing", "total"])) : createCommentVNode("", true)
                  ]),
                  __props.tracksCount && viewMode.value !== "table" && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.tracksCount && viewMode.value !== "table" && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.tracksCount && viewMode.value !== "table" ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountSchoolTracks"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$1, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.tracksCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3 mb-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.tracksCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.tracksCount && viewMode.value !== "table" ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredTracks.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.tracks
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock("div", {
                    key: 5,
                    class: "border border-gray-400 bg-white dark:bg-slate-800"
                  }, [
                    createVNode(unref(draggable), {
                      modelValue: localTracksTree.value,
                      "onUpdate:modelValue": ($event) => localTracksTree.value = $event,
                      tag: "div",
                      "item-key": "id",
                      handle: ".handle",
                      group: "tracks",
                      onEnd: handleDragEnd,
                      class: "category-tree-root p-1",
                      "data-parent-id": null
                    }, {
                      item: withCtx(({ element: track }) => [
                        createVNode(_sfc_main$3, {
                          track,
                          level: 0,
                          "selected-tracks": selectedTracks.value,
                          onToggleActivity: toggleActivity,
                          onDelete: confirmDelete,
                          onToggleSelect: toggleSelectTrack,
                          onRequestDragEnd: handleDragEnd
                        }, null, 8, ["track", "selected-tracks"])
                      ]),
                      _: 1
                    }, 8, ["modelValue", "onUpdate:modelValue"])
                  ])) : (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    tracks: displayedTracks.value,
                    "selected-tracks": selectedTracks.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectTrack,
                    onToggleAll: toggleAllCards
                  }, null, 8, ["tracks", "selected-tracks"])),
                  __props.tracksCount && viewMode.value !== "table" ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredTracks.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.tracks
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteTrack,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolTracks/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-ad3a839c"]]);
export {
  Index as default
};
