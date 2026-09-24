import { mergeProps, unref, useSSRContext, computed, ref, watch, resolveComponent, withCtx, createVNode, createTextVNode, toDisplayString, createBlock, openBlock, createSlots, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderStyle, ssrRenderAttr, ssrRenderClass, ssrRenderComponent, ssrIncludeBooleanAttr, ssrRenderList } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { usePage, router } from "@inertiajs/vue3";
import draggable from "vuedraggable";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$a } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$k } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$g } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$e, a as _sfc_main$h, b as _sfc_main$i } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$c } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$d, a as _sfc_main$j } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$f } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$b } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$5 } from "./ModerationButton-D_ehimPY.js";
import { _ as _sfc_main$6 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$8 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$9 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$7 } from "./CloneIconButton-BfVfDOWt.js";
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
      _push(`<div${ssrRenderAttrs(mergeProps({
        class: "flex flex-col sm:flex-row items-center",
        title: unref(t)("bulkActions")
      }, _attrs))}><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value=""> — ${ssrInterpolate(unref(t)("selectAction"))} — </option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogRubric/Select/BulkActionSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = /* @__PURE__ */ Object.assign({ name: "RubricTreeDraggable" }, {
  __name: "RubricTreeDraggable",
  __ssrInlineRender: true,
  props: {
    rubric: { type: Object, required: true },
    level: { type: Number, default: 0 },
    selectedRubrics: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
    "toggle-activity",
    "delete",
    "clone",
    "toggle-select",
    "request-drag-end",
    "approve"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emits = __emit;
    const storageKey = computed(() => {
      return `admin.blog.rubrics.tree.expanded.${props.rubric.id}`;
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
      () => props.rubric.id,
      () => {
        isExpanded.value = readExpandedState();
      }
    );
    watch(
      () => props.rubric.children,
      (children) => {
        localChildren.value = Array.isArray(children) ? [...children] : [];
      },
      { immediate: true, deep: true }
    );
    watch(() => props.selectedRubrics, () => {
    }, { deep: true });
    const getRubricTranslation = (rubric) => {
      return (rubric == null ? void 0 : rubric.translation) || {};
    };
    const getRubricTitle = (rubric) => {
      var _a;
      return ((_a = getRubricTranslation(rubric)) == null ? void 0 : _a.title) || `ID: ${rubric == null ? void 0 : rubric.id}`;
    };
    const getRubricShort = (rubric) => {
      var _a;
      return ((_a = getRubricTranslation(rubric)) == null ? void 0 : _a.short) || "";
    };
    const handleInnerDragEnd = (event) => {
      emits("request-drag-end", event);
    };
    const getPrimaryImage = (rubric) => {
      if ((rubric == null ? void 0 : rubric.images) && rubric.images.length) {
        return [...rubric.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0];
      }
      return null;
    };
    const getSafeIcon = (icon) => {
      if (!icon) return null;
      const trimmed = icon.trim();
      if (trimmed.startsWith("<svg") && trimmed.endsWith("</svg>")) {
        return trimmed;
      }
      return null;
    };
    const formatDate = (dateStr) => {
      if (!dateStr) return "";
      const d = new Date(dateStr);
      if (isNaN(d)) return "";
      return d.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    const ownerTitle = (rubric) => {
      const owner = rubric == null ? void 0 : rubric.owner;
      if (!owner) return t("noData");
      return `${owner.name || ""}${owner.email ? " — " + owner.email : ""}`.trim();
    };
    const moderationBadge = (status) => {
      const value = Number(status ?? 0);
      if (value === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (value === 2) {
        return {
          text: t("statusSelectRejected"),
          class: "bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300"
        };
      }
      return {
        text: t("underModeration"),
        class: "bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300"
      };
    };
    return (_ctx, _push, _parent, _attrs) => {
      var _a, _b, _c, _d, _e, _f, _g, _h;
      const _component_RubricTreeDraggable = resolveComponent("RubricTreeDraggable", true);
      _push(`<div${ssrRenderAttrs(_attrs)}><div class="category-item mb-1" style="${ssrRenderStyle({ marginLeft: __props.level * 20 + "px" })}"><div class="flex items-center justify-between py-1 px-2 border border-gray-400 rounded-sm bg-white dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 transition duration-150 ease-in-out"><div class="flex items-center space-x-2 flex-grow min-w-0"><span class="handle cursor-move mr-1 flex-shrink-0"${ssrRenderAttr("title", unref(t)("dragDrop"))}><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="w-4 h-4"><path class="fill-current text-sky-500 dark:text-sky-200" d="M278.6 9.4c-12.5-12.5-32.8-12.5-45.3 0l-64 64c-9.2 9.2-11.9 22.9-6.9 34.9s16.6 19.8 29.6 19.8l32 0 0 96-96 0 0-32c0-12.9-7.8-24.6-19.8-29.6s-25.7-2.2-34.9 6.9l-64 64c-12.5 12.5-12.5 32.8 0 45.3l64 64c9.2 9.2 22.9 11.9 34.9 6.9s19.8-16.6 19.8-29.6l0-32 96 0 0 96-32 0c-12.9 0-24.6 7.8-29.6 19.8s-2.2 25.7 6.9 34.9l64 64c12.5 12.5 32.8 12.5 45.3 0l64-64c9.2-9.2 11.9-22.9 6.9-34.9s-16.6-19.8-29.6-19.8l-32 0 0-96 96 0 0 32c0 12.9 7.8 24.6 19.8 29.6s25.7 2.2 34.9-6.9l64-64c12.5-12.5 12.5-32.8 0-45.3l-64-64c-9.2-9.2-22.9-11.9-34.9-6.9s-19.8 16.6-19.8 29.6l0 32-96 0 0-96 32 0c12.9 0 24.6-7.8 29.6-19.8s2.2-25.7-6.9-34.9l-64-64z"></path></svg></span>`);
      if (__props.rubric.children && __props.rubric.children.length) {
        _push(`<button${ssrRenderAttr("title", isExpanded.value ? unref(t)("collapse") : unref(t)("expand"))} class="flex-shrink-0 text-slate-900 hover:text-red-500 dark:text-slate-100 dark:hover:text-red-200"><svg class="${ssrRenderClass([{ "rotate-90": isExpanded.value }, "w-5 h-5 transform transition-transform duration-150"])}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>`);
      } else {
        _push(`<span class="w-4 h-4 inline-block flex-shrink-0"></span>`);
      }
      _push(`<div class="w-8 font-semibold text-sm text-amber-600 dark:text-amber-200 mr-1 flex-shrink-0"${ssrRenderAttr("title", `[${__props.rubric.sort}]`)}>${ssrInterpolate(__props.rubric.id)}</div>`);
      if ((_b = (_a = __props.rubric) == null ? void 0 : _a.owner) == null ? void 0 : _b.profile_photo_url) {
        _push(`<img${ssrRenderAttr("src", __props.rubric.owner.profile_photo_url)}${ssrRenderAttr("title", ownerTitle(__props.rubric))} class="h-6 w-6 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("author"))}>`);
      } else {
        _push(`<!---->`);
      }
      if (getSafeIcon(__props.rubric.icon)) {
        _push(`<div class="pl-3 w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center">${getSafeIcon(__props.rubric.icon) ?? ""}</div>`);
      } else {
        _push(`<svg class="w-4 h-4 text-slate-500 dark:text-slate-300" fill="currentColor" viewBox="0 0 16 16"><path d="M8 8a3 3 0 100-6 3 3 0 000 6zm2-3a2 2 0 11-4 0 2 2 0 014 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"></path></svg>`);
      }
      _push(`<a${ssrRenderAttr("href", `/blog/rubrics/${encodeURIComponent(__props.rubric.url)}`)} target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-sky-700 dark:text-sky-200 hover:text-amber-700 dark:hover:text-amber-200 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", getRubricShort(__props.rubric))}>${ssrInterpolate(getRubricTitle(__props.rubric))}</a><span class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", `${unref(t)("articles")}: ${__props.rubric.articles_count ?? 0}`)}>${ssrInterpolate(__props.rubric.articles_count ?? 0)}</span></div><div class="flex items-center space-x-1 flex-shrink-0 ml-4"><div class="flex items-center gap-1"><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg><div class="w-7 font-semibold text-[10px] text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("views"))}>${ssrInterpolate(__props.rubric.views ?? 0)}</div></div><div class="flex justify-center">`);
      if (__props.rubric.images && __props.rubric.images.length) {
        _push(`<img${ssrRenderAttr("src", ((_c = getPrimaryImage(__props.rubric)) == null ? void 0 : _c.webp_url) || ((_d = getPrimaryImage(__props.rubric)) == null ? void 0 : _d.url))}${ssrRenderAttr("alt", ((_e = getPrimaryImage(__props.rubric)) == null ? void 0 : _e.alt) || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", ((_f = getPrimaryImage(__props.rubric)) == null ? void 0 : _f.caption) || unref(t)("postImage"))} class="h-6 w-8 object-cover rounded-sm border border-slate-400 dark:border-slate-200 p-0.5">`);
      } else {
        _push(`<img src="/storage/blog/blog_rubric_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-6 w-8 object-cover rounded-sm border border-slate-400 dark:border-slate-200 p-0.5">`);
      }
      _push(`</div><div class="flex justify-center"><span class="${ssrRenderClass([moderationBadge(__props.rubric.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", __props.rubric.moderation_note && __props.rubric.moderated_at ? `${__props.rubric.moderation_note} [${formatDate(__props.rubric.moderated_at)}]` : null)}>${ssrInterpolate(moderationBadge(__props.rubric.moderation_status).text)}</span></div>`);
      _push(ssrRenderComponent(_sfc_main$5, {
        isAdmin: __props.isAdmin,
        status: ((_g = __props.rubric) == null ? void 0 : _g.moderation_status) ?? 0,
        initialNote: ((_h = __props.rubric) == null ? void 0 : _h.moderation_note) || "",
        mode: "toggle",
        onSubmit: ({ status, note }) => _ctx.$emit("approve", __props.rubric, status, note)
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$6, {
        isActive: __props.rubric.activity,
        onToggleActivity: ($event) => _ctx.$emit("toggle-activity", __props.rubric),
        title: __props.rubric.activity ? unref(t)("enabled") : unref(t)("disabled")
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$7, {
        onClone: ($event) => _ctx.$emit("clone", __props.rubric)
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$8, {
        href: _ctx.route("admin.blogRubrics.edit", { blogRubric: __props.rubric.id })
      }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$9, {
        onClick: ($event) => _ctx.$emit("delete", __props.rubric)
      }, null, _parent));
      _push(`<div class="pl-1.5"><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedRubrics.includes(__props.rubric.id)) ? " checked" : ""} class="form-checkbox rounded-sm text-indigo-500 flex-shrink-0"></div></div></div></div><div style="${ssrRenderStyle(isExpanded.value && localChildren.value.length ? null : { display: "none" })}" class="children-container mt-1">`);
      _push(ssrRenderComponent(unref(draggable), {
        modelValue: localChildren.value,
        "onUpdate:modelValue": ($event) => localChildren.value = $event,
        tag: "div",
        "item-key": "id",
        handle: ".handle",
        group: "rubrics",
        onEnd: handleInnerDragEnd,
        class: "category-tree-children",
        "data-parent-id": __props.rubric.id
      }, {
        item: withCtx(({ element: childRubric }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_component_RubricTreeDraggable, {
              rubric: childRubric,
              level: __props.level + 1,
              "selected-rubrics": __props.selectedRubrics,
              "is-admin": __props.isAdmin,
              onToggleActivity: (p) => _ctx.$emit("toggle-activity", p),
              onDelete: (p) => _ctx.$emit("delete", p),
              onClone: (p) => _ctx.$emit("clone", p),
              onToggleSelect: (id) => _ctx.$emit("toggle-select", id),
              onRequestDragEnd: handleInnerDragEnd,
              onApprove: (rub, status, note) => _ctx.$emit("approve", rub, status, note)
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_component_RubricTreeDraggable, {
                rubric: childRubric,
                level: __props.level + 1,
                "selected-rubrics": __props.selectedRubrics,
                "is-admin": __props.isAdmin,
                onToggleActivity: (p) => _ctx.$emit("toggle-activity", p),
                onDelete: (p) => _ctx.$emit("delete", p),
                onClone: (p) => _ctx.$emit("clone", p),
                onToggleSelect: (id) => _ctx.$emit("toggle-select", id),
                onRequestDragEnd: handleInnerDragEnd,
                onApprove: (rub, status, note) => _ctx.$emit("approve", rub, status, note)
              }, null, 8, ["rubric", "level", "selected-rubrics", "is-admin", "onToggleActivity", "onDelete", "onClone", "onToggleSelect", "onApprove"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogRubric/Tree/RubricTreeDraggable.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "RubricCardGrid",
  __ssrInlineRender: true,
  props: {
    rubrics: { type: Array, default: () => [] },
    selectedRubrics: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
    "toggle-activity",
    "delete",
    "clone",
    "toggle-select",
    "toggle-all",
    "approve"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const getTranslation = (rubric) => (rubric == null ? void 0 : rubric.translation) || {};
    const getTitle = (rubric) => {
      var _a;
      return ((_a = getTranslation(rubric)) == null ? void 0 : _a.title) || `ID: ${rubric == null ? void 0 : rubric.id}`;
    };
    const getShort = (rubric) => {
      var _a;
      return ((_a = getTranslation(rubric)) == null ? void 0 : _a.short) || "";
    };
    const getPrimaryImage = (rubric) => {
      if ((rubric == null ? void 0 : rubric.images) && rubric.images.length) {
        return [...rubric.images].sort((a, b) => (a.order ?? 0) - (b.order ?? 0))[0];
      }
      return null;
    };
    const getSafeIcon = (icon) => {
      if (!icon) return null;
      const trimmed = icon.trim();
      if (trimmed.startsWith("<svg") && trimmed.endsWith("</svg>")) {
        return trimmed;
      }
      return null;
    };
    const formatDate = (dateStr) => {
      if (!dateStr) return "";
      const d = new Date(dateStr);
      if (isNaN(d)) return "";
      return d.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    const truncateText = (text, maxLength = 80) => {
      if (!text) return "";
      return text.length > maxLength ? text.slice(0, maxLength).trimEnd() + "…" : text;
    };
    const ownerName = (rubric) => {
      var _a;
      return ((_a = rubric == null ? void 0 : rubric.owner) == null ? void 0 : _a.name) || t("noData");
    };
    const ownerEmail = (rubric) => {
      var _a;
      return ((_a = rubric == null ? void 0 : rubric.owner) == null ? void 0 : _a.email) || "";
    };
    const ownerTitle = (rubric) => {
      const o = rubric == null ? void 0 : rubric.owner;
      if (!o) return t("noData");
      return `${o.name || ""}${o.email ? " — " + o.email : ""}`.trim();
    };
    const ownerAvatar = (rubric) => {
      var _a;
      return ((_a = rubric == null ? void 0 : rubric.owner) == null ? void 0 : _a.profile_photo_url) || "/storage/profile-photos/default-image.png";
    };
    const moderationBadge = (status) => {
      const s = Number(status ?? 0);
      if (s === 1) {
        return {
          text: t("statusSelectApproved"),
          class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
        };
      }
      if (s === 2) {
        return {
          text: t("statusSelectRejected"),
          class: "bg-rose-100 text-rose-700 border-rose-300 dark:bg-rose-900/40 dark:text-rose-300"
        };
      }
      return {
        text: t("underModeration"),
        class: "bg-amber-100 text-amber-800 border-amber-300 dark:bg-amber-900/40 dark:text-amber-300"
      };
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedRubrics.length)}</div>`);
      if (props.rubrics.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (__props.rubrics.length) {
        _push(`<div class="p-3"><div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"><!--[-->`);
        ssrRenderList(__props.rubrics, (rubric) => {
          var _a, _b, _c, _d;
          _push(`<div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"><div class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center space-x-2"><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${rubric.sort}]`)}> ID: ${ssrInterpolate(rubric.id)}</div></div><div class="flex items-center space-x-2"><span class="${ssrRenderClass([moderationBadge(rubric.moderation_status).class, "text-[10px] px-2 py-0.5 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", rubric.moderation_note && rubric.moderated_at ? `${rubric.moderation_note} [${formatDate(rubric.moderated_at)}]` : null)}>${ssrInterpolate(moderationBadge(rubric.moderation_status).text)}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedRubrics.includes(rubric.id)) ? " checked" : ""}></div></header><div class="relative w-full bg-slate-200 dark:bg-slate-900">`);
          if (rubric.images && rubric.images.length) {
            _push(`<img${ssrRenderAttr("src", ((_a = getPrimaryImage(rubric)) == null ? void 0 : _a.webp_url) || ((_b = getPrimaryImage(rubric)) == null ? void 0 : _b.url))}${ssrRenderAttr("alt", ((_c = getPrimaryImage(rubric)) == null ? void 0 : _c.alt) || unref(t)("defaultImageAlt"))}${ssrRenderAttr("title", ((_d = getPrimaryImage(rubric)) == null ? void 0 : _d.caption) || unref(t)("postImage"))} class="h-32 w-full object-cover">`);
          } else {
            _push(`<img src="/storage/blog/blog_rubric_images/default-image.png"${ssrRenderAttr("alt", unref(t)("defaultImageTitle"))} class="h-32 w-full object-cover">`);
          }
          _push(`</div><div class="flex flex-col flex-1 px-3 py-2 space-y-2"><div class="flex flex-col items-center justify-center text-center"><img${ssrRenderAttr("src", ownerAvatar(rubric))}${ssrRenderAttr("title", ownerTitle(rubric))} class="h-12 w-12 rounded-full object-cover border border-slate-300 dark:border-slate-600"${ssrRenderAttr("alt", unref(t)("author"))}><div class="mt-1 text-[11px] font-semibold text-slate-700 dark:text-slate-100 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerName(rubric))}>${ssrInterpolate(ownerName(rubric))}</div>`);
          if (ownerEmail(rubric)) {
            _push(`<div class="text-[10px] text-slate-500 dark:text-slate-300 leading-tight line-clamp-1"${ssrRenderAttr("title", ownerEmail(rubric))}>${ssrInterpolate(ownerEmail(rubric))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="flex items-center justify-center text-center"><div class="flex items-center justify-center space-x-2 max-w-full"><div class="flex items-center justify-center shrink-0">`);
          if (getSafeIcon(rubric.icon)) {
            _push(`<div class="w-6 h-6 text-slate-700 dark:text-slate-100 flex items-center justify-center">${getSafeIcon(rubric.icon) ?? ""}</div>`);
          } else {
            _push(`<svg class="w-4 h-4 text-slate-500 dark:text-slate-300" fill="currentColor" viewBox="0 0 16 16"><path d="M8 8a3 3 0 100-6 3 3 0 000 6zm2-3a2 2 0 11-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"></path></svg>`);
          }
          _push(`</div><a${ssrRenderAttr("href", `/blog/rubrics/${encodeURIComponent(rubric.url)}`)} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-sky-700 dark:text-sky-200 hover:text-amber-700 dark:hover:text-amber-200 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", getTitle(rubric))}>${ssrInterpolate(truncateText(getTitle(rubric)))}</a><span class="shrink-0 text-[10px] px-1.5 py-0.5 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", `${unref(t)("articles")}: ${rubric.articles_count ?? 0}`)}>${ssrInterpolate(rubric.articles_count ?? 0)}</span></div></div><div class="font-semibold text-[12px] text-center text-teal-700 dark:text-teal-200">${ssrInterpolate(truncateText(getShort(rubric)))}</div>`);
          if ((rubric.views ?? 0) > 0) {
            _push(`<div class="flex items-center justify-center space-x-1"><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg><span class="text-[10px] text-slate-700 dark:text-slate-200"${ssrRenderAttr("title", unref(t)("views"))}>${ssrInterpolate(rubric.views ?? 0)}</span></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="flex justify-center gap-1"><span class="${ssrRenderClass([moderationBadge(rubric.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", rubric.moderation_note && rubric.moderated_at ? `${rubric.moderation_note} [${formatDate(rubric.moderated_at)}]` : null)}>${ssrInterpolate(moderationBadge(rubric.moderation_status).text)}</span>`);
          _push(ssrRenderComponent(_sfc_main$5, {
            isAdmin: __props.isAdmin,
            status: (rubric == null ? void 0 : rubric.moderation_status) ?? 0,
            initialNote: (rubric == null ? void 0 : rubric.moderation_note) || "",
            mode: "toggle",
            onSubmit: ({ status, note }) => _ctx.$emit("approve", rubric, status, note)
          }, null, _parent));
          _push(`</div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center space-x-1">`);
          _push(ssrRenderComponent(_sfc_main$6, {
            isActive: rubric.activity,
            onToggleActivity: ($event) => _ctx.$emit("toggle-activity", rubric),
            title: rubric.activity ? unref(t)("enabled") : unref(t)("disabled")
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$7, {
            onClone: ($event) => _ctx.$emit("clone", rubric)
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$8, {
            href: _ctx.route(
              "admin.blogRubrics.edit",
              { blogRubric: rubric.id }
            )
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$9, {
            onClick: ($event) => _ctx.$emit("delete", rubric)
          }, null, _parent));
          _push(`</div></footer></div></div>`);
        });
        _push(`<!--]--></div></div>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogRubric/View/RubricCardGrid.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-56 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>─────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>─────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option value="urlAsc">URL A→Z</option><option value="urlDesc">URL Z→A</option><option disabled>─────────────────</option><option value="levelAsc">${ssrInterpolate(unref(t)("level"))} 0→9</option><option value="levelDesc">${ssrInterpolate(unref(t)("level"))} 9→0</option><option value="parentAsc">${ssrInterpolate(unref(t)("parentRubric"))} 0→9</option><option value="parentDesc">${ssrInterpolate(unref(t)("parentRubric"))} 9→0</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="inMenuDesc">${ssrInterpolate(unref(t)("printInMenu"))} ON→OFF</option><option value="inMenuAsc">${ssrInterpolate(unref(t)("printInMenu"))} OFF→ON</option><option value="inMenu">${ssrInterpolate(unref(t)("printInMenu"))}</option><option value="notInMenu">${ssrInterpolate(unref(t)("notPrintInMenu"))}</option><option disabled>─────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option disabled>─────────────────</option><option value="articlesDesc">${ssrInterpolate(unref(t)("articles"))} 9→0</option><option value="articlesAsc">${ssrInterpolate(unref(t)("articles"))} 0→9</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>─────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>─────────────────</option><option value="ownerNameAsc">${ssrInterpolate(unref(t)("owner"))} A→Z</option><option value="ownerNameDesc">${ssrInterpolate(unref(t)("owner"))} Z→A</option><option value="ownerEmailAsc">${ssrInterpolate(unref(t)("ownerEmail"))} A→Z</option><option value="ownerEmailDesc">${ssrInterpolate(unref(t)("ownerEmail"))} Z→A</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Blog/BlogRubric/Sort/SortSelect.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    adminBlogRubricsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    rubricsTree: { type: Array, default: () => [] },
    rubrics: { type: [Array, Object], default: () => [] },
    rubricsCount: { type: Number, default: 0 },
    adminBlogRubricsPerPage: { type: Number, default: 6 },
    adminBlogRubricsDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const page = usePage();
    const props = __props;
    const isAdmin = computed(() => {
      var _a, _b, _c;
      const roles = ((_c = (_b = (_a = page.props) == null ? void 0 : _a.auth) == null ? void 0 : _b.user) == null ? void 0 : _c.roles) || [];
      return roles.some((role) => (role == null ? void 0 : role.name) === "admin");
    });
    const viewMode = ref(localStorage.getItem("admin_view_mode_blog_rubrics") || "table");
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_blog_rubrics", value);
    });
    const rubricsList = computed(() => {
      var _a;
      if (Array.isArray(props.rubrics)) {
        return props.rubrics;
      }
      if (Array.isArray((_a = props.rubrics) == null ? void 0 : _a.data)) {
        return props.rubrics.data;
      }
      return [];
    });
    const localRubricsTree = ref([]);
    const localRubricsFlat = ref([]);
    watch(
      () => props.rubricsTree,
      (newVal) => {
        localRubricsTree.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    watch(
      rubricsList,
      (newVal) => {
        localRubricsFlat.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const itemsPerPage = ref(props.adminBlogRubricsPerPage || 6);
    watch(itemsPerPage, (newVal) => {
      router.put(
        route("admin.settings.updateAdminCountBlogRubrics"),
        { value: newVal },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
          onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
        }
      );
    });
    const sortParam = ref(props.sortParam || props.adminBlogRubricsDefaultSort || "idDesc");
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortBlogRubrics"),
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
    const moderationNum = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const getRubricTranslation = (rubric) => {
      return (rubric == null ? void 0 : rubric.translation) || {};
    };
    const getRubricTitle = (rubric) => {
      var _a;
      return ((_a = getRubricTranslation(rubric)) == null ? void 0 : _a.title) || `ID: ${rubric == null ? void 0 : rubric.id}`;
    };
    const getRubricShort = (rubric) => {
      var _a;
      return ((_a = getRubricTranslation(rubric)) == null ? void 0 : _a.short) || "";
    };
    const getRubricDescription = (rubric) => {
      var _a;
      return ((_a = getRubricTranslation(rubric)) == null ? void 0 : _a.description) || "";
    };
    const getParentTitle = (rubric) => {
      var _a;
      return ((_a = rubric == null ? void 0 : rubric.parent) == null ? void 0 : _a.title) || "";
    };
    const getOwnerName = (rubric) => {
      var _a;
      return ((_a = rubric == null ? void 0 : rubric.owner) == null ? void 0 : _a.name) || "";
    };
    const getOwnerEmail = (rubric) => {
      var _a;
      return ((_a = rubric == null ? void 0 : rubric.owner) == null ? void 0 : _a.email) || "";
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortRubrics = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") return list.filter((item) => !!item.activity);
      if (sortParam.value === "inactive") return list.filter((item) => !item.activity);
      if (sortParam.value === "inMenu") return list.filter((item) => !!item.in_menu);
      if (sortParam.value === "notInMenu") return list.filter((item) => !item.in_menu);
      if (sortParam.value === "moderationPending") {
        return list.filter((item) => moderationNum(item == null ? void 0 : item.moderation_status) === 0);
      }
      if (sortParam.value === "moderationApproved") {
        return list.filter((item) => moderationNum(item == null ? void 0 : item.moderation_status) === 1);
      }
      if (sortParam.value === "moderationRejected") {
        return list.filter((item) => moderationNum(item == null ? void 0 : item.moderation_status) === 2);
      }
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        levelAsc: byNumberAsc("level"),
        levelDesc: byNumberDesc("level"),
        parentAsc: byNumberAsc("parent_id"),
        parentDesc: byNumberDesc("parent_id"),
        urlAsc: (a, b) => normalize(a == null ? void 0 : a.url).localeCompare(normalize(b == null ? void 0 : b.url), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        urlDesc: (a, b) => normalize(b == null ? void 0 : b.url).localeCompare(normalize(a == null ? void 0 : a.url), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        titleAsc: (a, b) => normalize(getRubricTitle(a)).localeCompare(normalize(getRubricTitle(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        titleDesc: (a, b) => normalize(getRubricTitle(b)).localeCompare(normalize(getRubricTitle(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        viewsAsc: byNumberAsc("views"),
        viewsDesc: byNumberDesc("views"),
        articlesAsc: byNumberAsc("articles_count"),
        articlesDesc: byNumberDesc("articles_count"),
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        inMenuAsc: byNumberAsc("in_menu"),
        inMenuDesc: byNumberDesc("in_menu"),
        moderationStatusAsc: (a, b) => moderationNum(a == null ? void 0 : a.moderation_status) - moderationNum(b == null ? void 0 : b.moderation_status) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        moderationStatusDesc: (a, b) => moderationNum(b == null ? void 0 : b.moderation_status) - moderationNum(a == null ? void 0 : a.moderation_status) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        ownerNameAsc: (a, b) => normalize(getOwnerName(a)).localeCompare(normalize(getOwnerName(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        ownerNameDesc: (a, b) => normalize(getOwnerName(b)).localeCompare(normalize(getOwnerName(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        ownerEmailAsc: (a, b) => normalize(getOwnerEmail(a)).localeCompare(normalize(getOwnerEmail(b)), props.currentLocale) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        ownerEmailDesc: (a, b) => normalize(getOwnerEmail(b)).localeCompare(normalize(getOwnerEmail(a)), props.currentLocale) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        createdAtAsc: byDateAsc("created_at"),
        createdAtDesc: byDateDesc("created_at"),
        updatedAtAsc: byDateAsc("updated_at"),
        updatedAtDesc: byDateDesc("updated_at")
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredRubrics = computed(() => {
      let filtered = localRubricsFlat.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortRubrics(filtered);
      }
      filtered = filtered.filter((rubric) => {
        var _a, _b;
        const values = [
          rubric == null ? void 0 : rubric.id,
          rubric == null ? void 0 : rubric.url,
          rubric == null ? void 0 : rubric.icon,
          rubric == null ? void 0 : rubric.views,
          rubric == null ? void 0 : rubric.moderation_note,
          getRubricTitle(rubric),
          getRubricShort(rubric),
          getRubricDescription(rubric),
          getParentTitle(rubric),
          getOwnerName(rubric),
          getOwnerEmail(rubric),
          (_a = rubric == null ? void 0 : rubric.moderator) == null ? void 0 : _a.name,
          (_b = rubric == null ? void 0 : rubric.moderator) == null ? void 0 : _b.email
        ];
        return values.some((value) => normalize(value).includes(query));
      });
      return sortRubrics(filtered);
    });
    const paginatedRubrics = computed(() => {
      const perPage = Number(itemsPerPage.value || 10);
      const start = (currentPage.value - 1) * perPage;
      return filteredRubrics.value.slice(start, start + perPage);
    });
    const displayedRubrics = computed(() => {
      return props.useServerProcessing ? rubricsList.value : paginatedRubrics.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const showConfirmDeleteModal = ref(false);
    const rubricToDeleteId = ref(null);
    const rubricToDeleteTitle = ref("");
    const confirmDelete = (rubricOrId, title = null) => {
      if (typeof rubricOrId === "object") {
        rubricToDeleteId.value = rubricOrId.id;
        rubricToDeleteTitle.value = title || getRubricTitle(rubricOrId);
      } else {
        rubricToDeleteId.value = rubricOrId;
        rubricToDeleteTitle.value = title || `ID: ${rubricOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      rubricToDeleteId.value = null;
      rubricToDeleteTitle.value = "";
    };
    const deleteRubric = () => {
      if (rubricToDeleteId.value === null) return;
      const idToDelete = rubricToDeleteId.value;
      const titleToDelete = rubricToDeleteTitle.value;
      router.delete(route("admin.blogRubrics.destroy", { blogRubric: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => toast.success(`Рубрика "${titleToDelete || "ID: " + idToDelete}" удалена.`),
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Рубрика: ${titleToDelete || "ID: " + idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    const patchRubricInTree = (nodes, rubricId, callback) => {
      var _a;
      for (const node of nodes) {
        if (node.id === rubricId) {
          callback(node);
          return true;
        }
        if (((_a = node.children) == null ? void 0 : _a.length) && patchRubricInTree(node.children, rubricId, callback)) {
          return true;
        }
      }
      return false;
    };
    const patchRubricInFlat = (rubricId, callback) => {
      const index = localRubricsFlat.value.findIndex((rubric) => rubric.id === rubricId);
      if (index !== -1) {
        callback(localRubricsFlat.value[index]);
      }
    };
    const toggleActivity = (rubric) => {
      const newActivity = !rubric.activity;
      const title = getRubricTitle(rubric);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route("admin.actions.blogRubrics.updateActivity", { blogRubric: rubric.id }),
        { activity: newActivity },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchRubricInTree(localRubricsTree.value, rubric.id, (node) => {
              node.activity = newActivity;
            });
            patchRubricInFlat(rubric.id, (node) => {
              node.activity = newActivity;
            });
            rubric.activity = newActivity;
            toast.success(`Рубрика "${title}" ${actionText}.`);
          },
          onError: (errors) => {
            toast.error(errors.activity || errors.general || `Ошибка изменения активности для "${title}".`);
          }
        }
      );
    };
    const cloneRubric = (rubric) => {
      const rubricId = rubric == null ? void 0 : rubric.id;
      const rubricTitle = getRubricTitle(rubric);
      if (!rubricId) {
        toast.error("Не удалось определить рубрику для клонирования.");
        return;
      }
      if (!confirm(`Вы уверены, что хотите клонировать рубрику "${rubricTitle}"?`)) return;
      router.post(
        route("admin.actions.blogRubrics.clone", { blogRubric: rubricId }),
        {},
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => toast.success(`Рубрика "${rubricTitle}" успешно клонирована.`),
          onError: (errors) => {
            const errorKey = Object.keys(errors || {})[0];
            toast.error(errors[errorKey] || `Ошибка клонирования рубрики "${rubricTitle}".`);
          }
        }
      );
    };
    const approveRubric = (rubric, status = 1, note = "") => {
      if (!(rubric == null ? void 0 : rubric.id)) return;
      router.put(
        route("admin.actions.blogRubrics.approve", { blogRubric: rubric.id }),
        {
          moderation_status: status,
          moderation_note: note
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchRubricInTree(localRubricsTree.value, rubric.id, (node) => {
              node.moderation_status = status;
              node.is_approved = status === 1;
              node.moderation_note = note;
            });
            patchRubricInFlat(rubric.id, (node) => {
              node.moderation_status = status;
              node.is_approved = status === 1;
              node.moderation_note = note;
            });
            toast.success(status === 1 ? "Рубрика одобрена" : "Рубрика отклонена");
          },
          onError: () => toast.error("Ошибка модерации рубрики")
        }
      );
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
      updateSortAndCollectChanges(localRubricsTree.value, null);
      if (!changes.length) return;
      router.put(
        route("admin.actions.blogRubrics.updateSortBulk"),
        { items: changes },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.success("Иерархия рубрик успешно обновлена."),
          onError: (errors) => {
            console.error("Ошибка обновления сортировки:", errors);
            toast.error(errors.message || "Ошибка обновления иерархии рубрик.");
            router.reload({
              only: ["rubricsTree", "rubrics"],
              preserveScroll: true
            });
          }
        }
      );
    };
    const selectedRubrics = ref([]);
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
      const ids = viewMode.value === "table" ? getAllIds(localRubricsTree.value) : displayedRubrics.value.map((rubric) => rubric.id);
      selectedRubrics.value = checked ? ids : [];
    };
    const toggleAllCards = ({ ids, checked }) => {
      selectedRubrics.value = checked ? [...ids] : [];
    };
    const toggleSelectRubric = (rubricId) => {
      const index = selectedRubrics.value.indexOf(rubricId);
      if (index > -1) {
        selectedRubrics.value.splice(index, 1);
      } else {
        selectedRubrics.value.push(rubricId);
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
      if (!selectedRubrics.value.length) {
        toast.warning("Выберите рубрики для активации/деактивации.");
        return;
      }
      const idsToUpdate = [...selectedRubrics.value];
      router.put(route("admin.actions.blogRubrics.bulkUpdateActivity"), {
        ids: idsToUpdate,
        activity: newActivity
      }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          updateActivityByIds(localRubricsTree.value, idsToUpdate, newActivity);
          localRubricsFlat.value = localRubricsFlat.value.map((item) => {
            return idsToUpdate.includes(item.id) ? { ...item, activity: newActivity } : item;
          });
          selectedRubrics.value = [];
          toast.success("Активность выбранных рубрик обновлена.");
        },
        onError: (errors) => {
          toast.error((errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности.");
        }
      });
    };
    const bulkDelete = () => {
      if (!selectedRubrics.value.length) {
        toast.warning("Выберите хотя бы одну рубрику для удаления.");
        return;
      }
      if (!confirm("Вы уверены, что хотите удалить выбранные рубрики?")) return;
      router.delete(route("admin.actions.blogRubrics.bulkDestroy"), {
        data: { ids: selectedRubrics.value },
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          selectedRubrics.value = [];
          toast.success("Выбранные рубрики успешно удалены.");
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          toast.error(errors[errorKey] || "Ошибка при массовом удалении рубрик.");
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
      } else if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("rubrics")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("rubrics"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("rubrics")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("rubrics")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto" data-v-e0879b5f${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" data-v-e0879b5f${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3" data-v-e0879b5f${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$a, {
              href: _ctx.route("admin.blogRubrics.create")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16" data-v-e0879b5f${_scopeId2}><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" data-v-e0879b5f${_scopeId2}></path></svg>`);
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
                  _push3(` ${ssrInterpolate(unref(t)("addRubric"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addRubric")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$b, {
              "setting-key": "adminBlogRubricsProcessingMode",
              mode: __props.adminBlogRubricsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.rubricsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.rubricsCount && viewMode.value !== "table" && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$c, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.rubricsCount && viewMode.value !== "table" && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$d, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.rubricsCount && viewMode.value !== "table") {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3" data-v-e0879b5f${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$f, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountBlogRubrics"
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
            if (__props.rubricsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3 mb-3" data-v-e0879b5f${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$g, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.rubricsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.rubricsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$h, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.rubricsCount && viewMode.value !== "table") {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3" data-v-e0879b5f${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$i, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredRubrics.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$j, { pagination: __props.rubrics }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(`<div class="mt-2 border border-gray-400 bg-white dark:bg-slate-800" data-v-e0879b5f${_scopeId}>`);
              if (__props.rubricsCount) {
                _push2(`<div class="flex justify-between items-center px-3 py-2 border-b border-gray-400 bg-gray-100 dark:bg-slate-900" data-v-e0879b5f${_scopeId}><div class="text-xs text-slate-600 dark:text-slate-200" data-v-e0879b5f${_scopeId}>${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(selectedRubrics.value.length)}</div><label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer" data-v-e0879b5f${_scopeId}><span data-v-e0879b5f${_scopeId}>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="form-checkbox rounded-sm text-indigo-500 ml-2"${ssrRenderAttr("title", unref(t)("selectAll"))} data-v-e0879b5f${_scopeId}></label></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(ssrRenderComponent(unref(draggable), {
                modelValue: localRubricsTree.value,
                "onUpdate:modelValue": ($event) => localRubricsTree.value = $event,
                tag: "div",
                "item-key": "id",
                handle: ".handle",
                group: "rubrics",
                onEnd: handleDragEnd,
                class: "category-tree-root p-1",
                "data-parent-id": null
              }, createSlots({
                item: withCtx(({ element: rubric }, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(ssrRenderComponent(_sfc_main$3, {
                      rubric,
                      level: 0,
                      "selected-rubrics": selectedRubrics.value,
                      "is-admin": isAdmin.value,
                      onToggleActivity: toggleActivity,
                      onDelete: confirmDelete,
                      onClone: cloneRubric,
                      onToggleSelect: toggleSelectRubric,
                      onRequestDragEnd: handleDragEnd,
                      onApprove: approveRubric
                    }, null, _parent3, _scopeId2));
                  } else {
                    return [
                      createVNode(_sfc_main$3, {
                        rubric,
                        level: 0,
                        "selected-rubrics": selectedRubrics.value,
                        "is-admin": isAdmin.value,
                        onToggleActivity: toggleActivity,
                        onDelete: confirmDelete,
                        onClone: cloneRubric,
                        onToggleSelect: toggleSelectRubric,
                        onRequestDragEnd: handleDragEnd,
                        onApprove: approveRubric
                      }, null, 8, ["rubric", "selected-rubrics", "is-admin"])
                    ];
                  }
                }),
                _: 2
              }, [
                localRubricsTree.value.length === 0 && __props.rubricsCount > 0 ? {
                  name: "header",
                  fn: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<div class="p-4 text-center text-slate-500 dark:text-slate-400" data-v-e0879b5f${_scopeId2}>${ssrInterpolate(unref(t)("loading"))}</div>`);
                    } else {
                      return [
                        createVNode("div", { class: "p-4 text-center text-slate-500 dark:text-slate-400" }, toDisplayString(unref(t)("loading")), 1)
                      ];
                    }
                  }),
                  key: "0"
                } : void 0,
                localRubricsTree.value.length === 0 && __props.rubricsCount === 0 ? {
                  name: "footer",
                  fn: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`<div class="p-4 text-center text-slate-900 dark:text-slate-100" data-v-e0879b5f${_scopeId2}>${ssrInterpolate(unref(t)("noData"))}</div>`);
                    } else {
                      return [
                        createVNode("div", { class: "p-4 text-center text-slate-900 dark:text-slate-100" }, toDisplayString(unref(t)("noData")), 1)
                      ];
                    }
                  }),
                  key: "1"
                } : void 0
              ]), _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(ssrRenderComponent(_sfc_main$2, {
                rubrics: displayedRubrics.value,
                selectedRubrics: selectedRubrics.value,
                isAdmin: isAdmin.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onClone: cloneRubric,
                onToggleSelect: toggleSelectRubric,
                onToggleAll: toggleAllCards,
                onApprove: approveRubric
              }, null, _parent2, _scopeId));
            }
            if (__props.rubricsCount && viewMode.value !== "table") {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3" data-v-e0879b5f${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$i, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredRubrics.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$j, { pagination: __props.rubrics }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$k, {
              show: showConfirmDeleteModal.value,
              onClose: closeModal,
              onCancel: closeModal,
              onConfirm: deleteRubric,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete")
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$a, {
                      href: _ctx.route("admin.blogRubrics.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addRubric")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$b, {
                      "setting-key": "adminBlogRubricsProcessingMode",
                      mode: __props.adminBlogRubricsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.rubricsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.rubricsCount && viewMode.value !== "table" && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.rubricsCount && viewMode.value !== "table" && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$d, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.rubricsCount && viewMode.value !== "table" ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$f, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountBlogRubrics"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$1, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.rubricsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3 mb-3"
                  }, [
                    createVNode(_sfc_main$g, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.rubricsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$h, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.rubricsCount && viewMode.value !== "table" ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$i, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredRubrics.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$j, {
                      key: 1,
                      pagination: __props.rubrics
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock("div", {
                    key: 5,
                    class: "mt-2 border border-gray-400 bg-white dark:bg-slate-800"
                  }, [
                    __props.rubricsCount ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex justify-between items-center px-3 py-2 border-b border-gray-400 bg-gray-100 dark:bg-slate-900"
                    }, [
                      createVNode("div", { class: "text-xs text-slate-600 dark:text-slate-200" }, toDisplayString(unref(t)("selected")) + ": " + toDisplayString(selectedRubrics.value.length), 1),
                      createVNode("label", { class: "flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer" }, [
                        createVNode("span", null, toDisplayString(unref(t)("selectAll")), 1),
                        createVNode("input", {
                          type: "checkbox",
                          onChange: toggleAll,
                          class: "form-checkbox rounded-sm text-indigo-500 ml-2",
                          title: unref(t)("selectAll")
                        }, null, 40, ["title"])
                      ])
                    ])) : createCommentVNode("", true),
                    createVNode(unref(draggable), {
                      modelValue: localRubricsTree.value,
                      "onUpdate:modelValue": ($event) => localRubricsTree.value = $event,
                      tag: "div",
                      "item-key": "id",
                      handle: ".handle",
                      group: "rubrics",
                      onEnd: handleDragEnd,
                      class: "category-tree-root p-1",
                      "data-parent-id": null
                    }, createSlots({
                      item: withCtx(({ element: rubric }) => [
                        createVNode(_sfc_main$3, {
                          rubric,
                          level: 0,
                          "selected-rubrics": selectedRubrics.value,
                          "is-admin": isAdmin.value,
                          onToggleActivity: toggleActivity,
                          onDelete: confirmDelete,
                          onClone: cloneRubric,
                          onToggleSelect: toggleSelectRubric,
                          onRequestDragEnd: handleDragEnd,
                          onApprove: approveRubric
                        }, null, 8, ["rubric", "selected-rubrics", "is-admin"])
                      ]),
                      _: 2
                    }, [
                      localRubricsTree.value.length === 0 && __props.rubricsCount > 0 ? {
                        name: "header",
                        fn: withCtx(() => [
                          createVNode("div", { class: "p-4 text-center text-slate-500 dark:text-slate-400" }, toDisplayString(unref(t)("loading")), 1)
                        ]),
                        key: "0"
                      } : void 0,
                      localRubricsTree.value.length === 0 && __props.rubricsCount === 0 ? {
                        name: "footer",
                        fn: withCtx(() => [
                          createVNode("div", { class: "p-4 text-center text-slate-900 dark:text-slate-100" }, toDisplayString(unref(t)("noData")), 1)
                        ]),
                        key: "1"
                      } : void 0
                    ]), 1032, ["modelValue", "onUpdate:modelValue"])
                  ])) : (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    rubrics: displayedRubrics.value,
                    selectedRubrics: selectedRubrics.value,
                    isAdmin: isAdmin.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onClone: cloneRubric,
                    onToggleSelect: toggleSelectRubric,
                    onToggleAll: toggleAllCards,
                    onApprove: approveRubric
                  }, null, 8, ["rubrics", "selectedRubrics", "isAdmin"])),
                  __props.rubricsCount && viewMode.value !== "table" ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$i, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredRubrics.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$j, {
                      key: 1,
                      pagination: __props.rubrics
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$k, {
                show: showConfirmDeleteModal.value,
                onClose: closeModal,
                onCancel: closeModal,
                onConfirm: deleteRubric,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Blog/BlogRubrics/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-e0879b5f"]]);
export {
  Index as default
};
