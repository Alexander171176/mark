import { mergeProps, unref, useSSRContext, withCtx, createTextVNode, toDisplayString, computed, ref, watch } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderComponent, ssrRenderList, ssrGetDynamicModelProps, ssrRenderClass } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$6 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$7 } from "./InputError-CLVdJ1nk.js";
import VueMultiselect from "vue-multiselect";
const _sfc_main$5 = {
  __name: "SelectStatus",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: String, default: "" },
    errorMessage: { type: String, default: "" }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}><label class="mb-1 block font-medium text-sm text-indigo-600 dark:text-sky-500">${ssrInterpolate(unref(t)("status"))}</label><select class="block w-full py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${ssrRenderAttr("value", __props.modelValue)}><option value="">${ssrInterpolate(unref(t)("select"))}</option><option value="draft">${ssrInterpolate(unref(t)("statusDraft"))}</option><option value="published">${ssrInterpolate(unref(t)("statusPublished"))}</option><option value="archived">${ssrInterpolate(unref(t)("statusArchived"))}</option></select>`);
      if (__props.errorMessage) {
        _push(`<p class="text-sm text-red-600 dark:text-orange-200">${ssrInterpolate(__props.errorMessage)}</p>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolLesson/Select/SelectStatus.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "SelectAvailability",
  __ssrInlineRender: true,
  props: {
    modelValue: { type: String, default: "" },
    errorMessage: { type: String, default: "" }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}><label class="mb-1 block font-medium text-sm text-indigo-600 dark:text-sky-500">${ssrInterpolate(unref(t)("availability"))}</label><select class="block w-full py-0.5 border-slate-500 text-md focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100"${ssrRenderAttr("value", __props.modelValue)}><option value="">${ssrInterpolate(unref(t)("select"))}</option><option value="unlisted">${ssrInterpolate(unref(t)("availabilityUnlisted"))}</option><option value="public">${ssrInterpolate(unref(t)("availabilityPublic"))}</option><option value="private">${ssrInterpolate(unref(t)("availabilityPrivate"))}</option></select>`);
      if (__props.errorMessage) {
        _push(`<p class="text-sm text-red-600 dark:text-orange-200">${ssrInterpolate(__props.errorMessage)}</p>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolLesson/Select/SelectAvailability.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "SelectAccessType",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: String,
      default: "free"
    },
    errorMessage: {
      type: String,
      default: ""
    }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const options = [
      { value: "free", label: "free" },
      { value: "paid", label: "paid" },
      { value: "bonus", label: "bonus" }
    ];
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}>`);
      _push(ssrRenderComponent(_sfc_main$6, { for: "access_type" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("accessType"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("accessType")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<select id="access_type" class="mt-1 py-0.5 border-slate-500 rounded-sm shadow-sm font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300 dark:bg-cyan-800 dark:text-slate-100"${ssrRenderAttr("value", __props.modelValue)}><!--[-->`);
      ssrRenderList(options, (opt) => {
        _push(`<option${ssrRenderAttr("value", opt.value)}>${ssrInterpolate(unref(t)(opt.label) || opt.label)}</option>`);
      });
      _push(`<!--]--></select>`);
      _push(ssrRenderComponent(_sfc_main$7, {
        class: "mt-1",
        message: __props.errorMessage
      }, null, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolLesson/Select/SelectAccessType.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "SelectPreviewMode",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: String,
      default: "none"
    },
    errorMessage: {
      type: String,
      default: ""
    }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const options = [
      { value: "none", label: "previewNone" },
      { value: "full", label: "previewFull" },
      { value: "percent", label: "previewPercent" },
      { value: "duration", label: "previewDuration" },
      { value: "chars", label: "previewChars" }
    ];
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col items-start" }, _attrs))}>`);
      _push(ssrRenderComponent(_sfc_main$6, { for: "preview_mode" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("previewMode"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("previewMode")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<select id="preview_mode" class="mt-1 py-0.5 border-slate-500 rounded-sm shadow-sm font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300 dark:bg-cyan-800 dark:text-slate-100"${ssrRenderAttr("value", __props.modelValue)}><!--[-->`);
      ssrRenderList(options, (opt) => {
        _push(`<option${ssrRenderAttr("value", opt.value)}>${ssrInterpolate(unref(t)(opt.label) || opt.value)}</option>`);
      });
      _push(`<!--]--></select>`);
      _push(ssrRenderComponent(_sfc_main$7, {
        class: "mt-1",
        message: __props.errorMessage
      }, null, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolLesson/Select/SelectPreviewMode.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "ContentIdInput",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: [Number, String],
      default: ""
    },
    id: {
      type: String,
      default: "content_id"
    },
    min: {
      type: Number,
      default: 0
    }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const emit = __emit;
    const internalValue = computed({
      get() {
        if (props.modelValue === null || typeof props.modelValue === "undefined") {
          return "";
        }
        return props.modelValue;
      },
      set(val) {
        if (val === "" || val === null) {
          emit("update:modelValue", "");
          return;
        }
        const num = Number(val);
        if (Number.isNaN(num)) {
          emit("update:modelValue", "");
        } else {
          emit("update:modelValue", num);
        }
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      let _temp0;
      _push(`<input${ssrRenderAttrs((_temp0 = mergeProps({ class: "px-2 py-0.5 rounded-sm" }, _ctx.$attrs, {
        id: __props.id,
        type: "number",
        min: __props.min,
        value: internalValue.value
      }, _attrs), mergeProps(_temp0, ssrGetDynamicModelProps(_temp0, internalValue.value))))}>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolLesson/Input/ContentIdInput.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "LessonContentSelect",
  __ssrInlineRender: true,
  props: {
    contentType: {
      type: String,
      default: null
    },
    contentId: {
      type: [Number, String, null],
      default: null
    },
    articles: {
      type: Array,
      default: () => []
    },
    videos: {
      type: Array,
      default: () => []
    },
    errorType: {
      type: String,
      default: ""
    },
    errorId: {
      type: String,
      default: ""
    }
  },
  emits: [
    "update:contentType",
    "update:contentId"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const CONTENT_TYPE_MAP = {
      article: "App\\Models\\Admin\\Blog\\BlogArticle\\BlogArticle",
      video: "App\\Models\\Admin\\Blog\\BlogVideo\\BlogVideo"
    };
    const detectType = (contentType) => {
      if (contentType === CONTENT_TYPE_MAP.article) {
        return "article";
      }
      if (contentType === CONTENT_TYPE_MAP.video) {
        return "video";
      }
      return null;
    };
    const articleOptions = computed(() => {
      return (props.articles || []).map(
        (article) => {
          var _a;
          return {
            id: article.id,
            label: `[ID: ${article.id}] ${((_a = article == null ? void 0 : article.translation) == null ? void 0 : _a.title) || (article == null ? void 0 : article.url) || `Article #${article.id}`}`
          };
        }
      );
    });
    const videoOptions = computed(() => {
      return (props.videos || []).map(
        (video) => {
          var _a;
          return {
            id: video.id,
            label: `[ID: ${video.id}] ${((_a = video == null ? void 0 : video.translation) == null ? void 0 : _a.title) || (video == null ? void 0 : video.url) || `Video #${video.id}`}`
          };
        }
      );
    });
    const activeOptions = computed(() => {
      if (localType.value === "article") {
        return articleOptions.value;
      }
      if (localType.value === "video") {
        return videoOptions.value;
      }
      return [];
    });
    const localType = ref(
      detectType(
        props.contentType
      )
    );
    const selectedItem = ref(null);
    const cachedSelectedIds = ref({
      article: null,
      video: null
    });
    const findOption = (type, id) => {
      if (!type || id === null || id === "" || typeof id === "undefined") {
        return null;
      }
      const options = type === "article" ? articleOptions.value : videoOptions.value;
      return options.find(
        (item) => Number(item.id) === Number(id)
      ) || null;
    };
    watch(
      [
        () => props.contentType,
        () => props.contentId,
        articleOptions,
        videoOptions
      ],
      () => {
        const type = detectType(
          props.contentType
        );
        localType.value = type;
        if (!type) {
          selectedItem.value = null;
          return;
        }
        const found = findOption(
          type,
          props.contentId
        );
        selectedItem.value = found;
        cachedSelectedIds.value[type] = (found == null ? void 0 : found.id) ?? (props.contentId ? Number(props.contentId) : null);
      },
      {
        immediate: true
      }
    );
    watch(
      selectedItem,
      (item) => {
        if (localType.value) {
          cachedSelectedIds.value[localType.value] = item ? Number(item.id) : null;
        }
        emit(
          "update:contentId",
          item ? Number(item.id) : null
        );
      }
    );
    const contentIdProxy = computed({
      get() {
        return props.contentId ?? "";
      },
      set(value) {
        if (value === "" || value === null) {
          selectedItem.value = null;
          emit(
            "update:contentId",
            null
          );
          return;
        }
        const id = Number(value);
        if (!Number.isFinite(id)) {
          selectedItem.value = null;
          emit(
            "update:contentId",
            null
          );
          return;
        }
        selectedItem.value = findOption(
          localType.value,
          id
        );
        if (localType.value) {
          cachedSelectedIds.value[localType.value] = id;
        }
        emit(
          "update:contentId",
          id
        );
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "mb-3 flex w-full flex-col items-start border-y border-dashed border-slate-500 py-3" }, _attrs))}><div class="mb-2 w-full text-sm font-semibold text-gray-900 dark:text-gray-200">${ssrInterpolate(unref(t)("relatedContent"))}</div><div class="grid w-full grid-cols-1 gap-4 md:grid-cols-2"><div class="flex flex-col items-start">`);
      _push(ssrRenderComponent(_sfc_main$6, { for: "content_type" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("contentType"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("contentType")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<div class="mt-1 flex flex-wrap gap-3"><button type="button" class="${ssrRenderClass([
        localType.value === "article" ? "border-emerald-700 bg-emerald-600 text-white" : "border-slate-300 bg-slate-100 text-slate-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100",
        "rounded border px-3 py-1 text-sm"
      ])}">${ssrInterpolate(unref(t)("article"))}</button><button type="button" class="${ssrRenderClass([
        localType.value === "video" ? "border-emerald-700 bg-emerald-600 text-white" : "border-slate-300 bg-slate-100 text-slate-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100",
        "rounded border px-3 py-1 text-sm"
      ])}">${ssrInterpolate(unref(t)("video"))}</button><button type="button" class="${ssrRenderClass([
        !localType.value ? "border-red-700 bg-red-500 text-white" : "border-slate-300 bg-slate-200 text-slate-800 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200",
        "rounded border px-2 py-0.5 text-xs"
      ])}">${ssrInterpolate(unref(t)("reset"))}</button></div>`);
      _push(ssrRenderComponent(_sfc_main$7, {
        class: "mt-1",
        message: __props.errorType
      }, null, _parent));
      _push(`</div><div class="flex w-full flex-col items-start">`);
      _push(ssrRenderComponent(_sfc_main$6, { for: "content_id" }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("contentId"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("contentId")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      if (localType.value) {
        _push(ssrRenderComponent(unref(VueMultiselect), {
          id: "content_id_select",
          modelValue: selectedItem.value,
          "onUpdate:modelValue": ($event) => selectedItem.value = $event,
          options: activeOptions.value,
          multiple: false,
          "close-on-select": true,
          "clear-on-select": false,
          "preserve-search": true,
          placeholder: unref(t)("select"),
          label: "label",
          "track-by": "id",
          class: "mb-2 w-full"
        }, null, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="flex w-full items-center gap-2">`);
      _push(ssrRenderComponent(_sfc_main$1, {
        id: "content_id",
        modelValue: contentIdProxy.value,
        "onUpdate:modelValue": ($event) => contentIdProxy.value = $event,
        min: 0,
        class: "w-32"
      }, null, _parent));
      _push(`<span class="text-xs font-semibold text-slate-600 dark:text-slate-400">${ssrInterpolate(unref(t)("orEnterIdManually"))}</span></div>`);
      _push(ssrRenderComponent(_sfc_main$7, {
        class: "mt-1",
        message: __props.errorId
      }, null, _parent));
      _push(`</div></div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolLesson/Block/LessonContentSelect.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$5 as _,
  _sfc_main$4 as a,
  _sfc_main$3 as b,
  _sfc_main$2 as c,
  _sfc_main as d
};
