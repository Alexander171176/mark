import { ref, mergeProps, useSSRContext, useModel, onMounted, computed, watch, unref, createVNode, resolveDynamicComponent, withCtx, createTextVNode, toDisplayString, createBlock, createCommentVNode, openBlock, withDirectives, vModelText, Fragment, renderList } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderSlot, ssrGetDynamicModelProps, ssrRenderComponent, ssrRenderList, ssrRenderAttr, ssrRenderVNode } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { _ as _sfc_main$c } from "./InputError-CLVdJ1nk.js";
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
const _sfc_main$b = {
  __name: "IconSaveButton",
  __ssrInlineRender: true,
  props: {
    href: {
      type: String,
      default: "submit"
    }
  },
  setup(__props) {
    const isPressed = ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<button${ssrRenderAttrs(mergeProps({
        class: ["flex items-center btn mt-1 px-1 py-1 bg-teal-500 text-white text-md font-semibold rounded-md shadow-md transition-colors duration-300 ease-in-out hover:bg-teal-600 focus:bg-teal-600 focus:outline-none", { "ring-2 ring-teal-500 ring-offset-2 ring-offset-white": isPressed.value }]
      }, _attrs))}><svg class="w-6 h-6 fill-current text-slate-100" viewBox="0 0 24 24"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z"></path></svg></button>`);
    };
  }
};
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/IconSaveButton.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const _sfc_main$a = {
  __name: "InfoIconButton",
  __ssrInlineRender: true,
  emits: ["click"],
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<svg${ssrRenderAttrs(mergeProps({
        class: "w-6 h-6 shrink-0 fill-current text-blue-500 mx-1",
        viewBox: "0 0 16 16"
      }, _attrs))}><path d="M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z"></path></svg>`);
    };
  }
};
const _sfc_setup$a = _sfc_main$a.setup;
_sfc_main$a.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Setting/Button/InfoIconButton.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const _sfc_main$9 = {
  __name: "LabelInput",
  __ssrInlineRender: true,
  props: {
    value: {
      type: String
    }
  },
  setup(__props) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<label${ssrRenderAttrs(mergeProps({ class: "block font-medium text-sm text-indigo-600 dark:text-sky-500" }, _attrs))}>`);
      if (__props.value) {
        _push(`<span>${ssrInterpolate(__props.value)}</span>`);
      } else {
        _push(`<span>`);
        ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
        _push(`</span>`);
      }
      _push(`</label>`);
    };
  }
};
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Setting/Input/LabelInput.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const _sfc_main$8 = {
  __name: "InputText",
  __ssrInlineRender: true,
  props: {
    "modelValue": {
      type: String,
      required: true
    },
    "modelModifiers": {}
  },
  emits: ["update:modelValue"],
  setup(__props, { expose: __expose }) {
    const model = useModel(__props, "modelValue");
    const input = ref(null);
    onMounted(() => {
      if (input.value.hasAttribute("autofocus")) {
        input.value.focus();
      }
    });
    __expose({ focus: () => input.value.focus() });
    return (_ctx, _push, _parent, _attrs) => {
      let _temp0;
      _push(`<input${ssrRenderAttrs((_temp0 = mergeProps({
        class: "mt-1 block w-fit flex-grow px-2 py-1 bg-slate-100 dark:bg-cyan-800 border border-slate-500 dark:border-slate-100 rounded-md shadow-sm font-semibold text-sm focus:outline-none focus:border-indigo-500 focus:ring-indigo-300 dark:text-slate-100",
        ref_key: "input",
        ref: input
      }, _attrs), mergeProps(_temp0, ssrGetDynamicModelProps(_temp0, model.value))))}>`);
    };
  }
};
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Setting/Input/InputText.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const _sfc_main$7 = {
  __name: "SettingText",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: [String, Number],
      default: ""
    },
    id: {
      type: String,
      required: true
    }
  },
  emits: [
    "update:modelValue"
  ],
  setup(__props, { emit: __emit }) {
    const emit = __emit;
    const updateValue = (value) => {
      emit(
        "update:modelValue",
        value
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(_sfc_main$8, mergeProps({
        id: __props.id,
        "model-value": __props.modelValue,
        type: "text",
        autocomplete: "off",
        "onUpdate:modelValue": updateValue
      }, _attrs), null, _parent));
    };
  }
};
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Setting/Fields/SettingText.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const _sfc_main$6 = {
  __name: "SettingNumber",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: [String, Number],
      default: ""
    },
    id: {
      type: String,
      required: true
    }
  },
  emits: [
    "update:modelValue"
  ],
  setup(__props, { emit: __emit }) {
    const emit = __emit;
    const updateValue = (value) => {
      emit(
        "update:modelValue",
        value
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(_sfc_main$8, mergeProps({
        id: __props.id,
        "model-value": __props.modelValue,
        type: "number",
        autocomplete: "off",
        "onUpdate:modelValue": updateValue
      }, _attrs), null, _parent));
    };
  }
};
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Setting/Fields/SettingNumber.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const _sfc_main$5 = {
  __name: "SettingsCheckbox",
  __ssrInlineRender: true,
  props: {
    id: {
      type: String,
      required: true
    },
    modelValue: {
      type: String,
      default: "false"
    }
  },
  emits: ["update:modelValue"],
  setup(__props, { emit: __emit }) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<input${ssrRenderAttrs(mergeProps({
        type: "checkbox",
        id: __props.id,
        checked: __props.modelValue === "true",
        class: "mt-1 block w-6 h-6 bg-slate-100 dark:bg-cyan-800 border border-gray-500 dark:border-gray-100 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
      }, _attrs))}>`);
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Setting/Input/SettingsCheckbox.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "SettingCheckbox",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: String,
      default: "false"
    },
    id: {
      type: String,
      required: true
    }
  },
  emits: [
    "update:modelValue"
  ],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const emit = __emit;
    const checkboxValue = computed({
      get() {
        return props.modelValue === "true" ? "true" : "false";
      },
      set(value) {
        emit(
          "update:modelValue",
          value === "true" ? "true" : "false"
        );
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex items-center" }, _attrs))}>`);
      _push(ssrRenderComponent(_sfc_main$5, {
        id: __props.id,
        modelValue: checkboxValue.value,
        "onUpdate:modelValue": ($event) => checkboxValue.value = $event
      }, null, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Setting/Fields/SettingCheckbox.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "SettingSelect",
  __ssrInlineRender: true,
  props: {
    modelValue: {
      type: [String, Number],
      default: ""
    },
    id: {
      type: String,
      required: true
    },
    options: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "update:modelValue"
  ],
  setup(__props, { emit: __emit }) {
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<select${ssrRenderAttrs(mergeProps({
        id: __props.id,
        value: __props.modelValue,
        class: "block w-fit px-2 py-1 bg-slate-100 dark:bg-cyan-800 border border-slate-500 dark:border-slate-100 rounded-md shadow-sm font-semibold text-sm focus:outline-none focus:border-indigo-500 focus:ring-indigo-300 dark:text-slate-100"
      }, _attrs))}><!--[-->`);
      ssrRenderList(__props.options, (option) => {
        _push(`<option${ssrRenderAttr("value", option.value)}>${ssrInterpolate(option.label)}</option>`);
      });
      _push(`<!--]--></select>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Setting/Fields/SettingSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "SettingField",
  __ssrInlineRender: true,
  props: {
    setting: {
      type: Object,
      required: true
    }
  },
  emits: [
    "show-description"
  ],
  setup(__props, { emit: __emit }) {
    var _a;
    const { t } = useI18n();
    useToast();
    const props = __props;
    const emit = __emit;
    const settingType = computed(() => {
      var _a2;
      return String(
        ((_a2 = props.setting) == null ? void 0 : _a2.type) || "string"
      ).trim().toLowerCase();
    });
    const fieldId = computed(() => {
      return `setting-${props.setting.id}`;
    });
    const normalizeValue = (value) => {
      if (settingType.value === "checkbox") {
        return value === "true" ? "true" : "false";
      }
      return value ?? "";
    };
    const form = useForm({
      value: normalizeValue(
        (_a = props.setting) == null ? void 0 : _a.value
      )
    });
    watch(
      () => {
        var _a2;
        return (_a2 = props.setting) == null ? void 0 : _a2.value;
      },
      (newValue) => {
        form.value = normalizeValue(
          newValue
        );
      }
    );
    const isProcessingMode = computed(() => {
      var _a2;
      const option = String(
        ((_a2 = props.setting) == null ? void 0 : _a2.option) || ""
      );
      return option.endsWith(
        "ProcessingMode"
      );
    });
    const processingModeOptions = [
      {
        value: "frontend",
        label: "Frontend"
      },
      {
        value: "server",
        label: "Server"
      },
      {
        value: "auto",
        label: "Auto"
      }
    ];
    const fieldComponent = computed(() => {
      switch (settingType.value) {
        case "checkbox":
          return _sfc_main$4;
        case "number":
          return _sfc_main$6;
        case "string":
        default:
          return _sfc_main$7;
      }
    });
    const settingLabel = computed(() => {
      var _a2;
      return ((_a2 = props.setting) == null ? void 0 : _a2.option) || "";
    });
    const showDescription = () => {
      var _a2;
      emit(
        "show-description",
        ((_a2 = props.setting) == null ? void 0 : _a2.description) || ""
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<form${ssrRenderAttrs(mergeProps({ class: "border-dashed border-b border-slate-500 dark:border-slate-400 last:border-b-0" }, _attrs))}><div class="py-2 flex flex-col gap-4 xl:flex-row xl:items-center"><div class="w-full xl:w-1/3 xl:min-w-[320px]">`);
      _push(ssrRenderComponent(_sfc_main$9, {
        for: fieldId.value,
        value: settingLabel.value
      }, null, _parent));
      _push(`<div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 font-semibold text-xs text-slate-600 dark:text-slate-400"><span>${ssrInterpolate(__props.setting.category)}</span><span> · </span><span>${ssrInterpolate(__props.setting.type)}</span></div></div><div class="w-full flex items-center justify-between"><div class="w-full min-w-0 xl:flex-1">`);
      if (isProcessingMode.value) {
        _push(ssrRenderComponent(_sfc_main$3, {
          id: fieldId.value,
          modelValue: unref(form).value,
          "onUpdate:modelValue": ($event) => unref(form).value = $event,
          options: processingModeOptions
        }, null, _parent));
      } else {
        ssrRenderVNode(_push, createVNode(resolveDynamicComponent(fieldComponent.value), {
          id: fieldId.value,
          modelValue: unref(form).value,
          "onUpdate:modelValue": ($event) => unref(form).value = $event
        }, null), _parent);
      }
      _push(ssrRenderComponent(_sfc_main$c, {
        class: "mt-2",
        message: unref(form).errors.value
      }, null, _parent));
      _push(`</div><div class="w-full flex items-center justify-end gap-2 xl:w-auto xl:flex-shrink-0">`);
      _push(ssrRenderComponent(_sfc_main$b, {
        class: {
          "opacity-25": unref(form).processing
        },
        disabled: unref(form).processing
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("save"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("save")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      if (__props.setting.description) {
        _push(ssrRenderComponent(_sfc_main$a, { onClick: showDescription }, null, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div></div></form>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Setting/SettingField.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "Modal",
  __ssrInlineRender: true,
  props: {
    showModal: {
      type: Boolean,
      required: true
    },
    modalDescription: {
      type: String,
      required: true
    }
  },
  emits: ["toggleModal"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      if (__props.showModal) {
        _push(`<div${ssrRenderAttrs(mergeProps({
          class: "fixed inset-0 overflow-hidden flex items-center my-4 justify-center px-4 sm:px-6 z-50",
          role: "dialog",
          "aria-modal": "true"
        }, _attrs))}><div class="fixed inset-0 bg-slate-900 opacity-50 z-50"></div><div class="bg-white rounded shadow-lg overflow-auto max-w-lg w-full max-h-full dark:bg-gray-800 z-60"><div class="px-5 py-3 border-b border-slate-200 dark:border-gray-700"><div class="flex justify-between items-center"><svg class="w-6 h-6 shrink-0 fill-current text-indigo-500" viewBox="0 0 16 16"><path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm1 12H7V7h2v5zM8 6c-.6 0-1-.4-1-1s.4-1 1-1 1 .4 1 1-.4 1-1 1z"></path></svg><div class="text-xl font-semibold text-sky-800 dark:text-sky-400">${ssrInterpolate(unref(t)("description"))}</div><button class="text-slate-700 hover:text-rose-400 dark:text-slate-400 dark:hover:text-red-400"><svg class="w-4 h-4 fill-current"><path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z"></path></svg></button></div></div><div class="px-5 pt-4 pb-5"><div class="text-xl text-center font-semibold text-slate-800 dark:text-gray-200"><div class="space-y-2"><p>${ssrInterpolate(__props.modalDescription)}</p></div></div></div></div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Setting/Modal/Modal.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    settings: {
      type: [Array, Object],
      default: () => []
    },
    error: {
      type: String,
      default: ""
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const search = ref("");
    const showModal = ref(false);
    const modalDescription = ref("");
    const settingsList = computed(() => {
      var _a;
      if (Array.isArray(props.settings)) return props.settings;
      if (Array.isArray((_a = props.settings) == null ? void 0 : _a.data)) return props.settings.data;
      return [];
    });
    const filteredSettings = computed(() => {
      const term = search.value.trim().toLowerCase();
      if (!term) return settingsList.value;
      return settingsList.value.filter((setting) => {
        const haystack = [
          setting.option,
          setting.description,
          setting.category,
          setting.type,
          setting.value
        ].filter(Boolean).join(" ").toLowerCase();
        return haystack.includes(term);
      });
    });
    const resolveSection = (setting) => {
      const option = String((setting == null ? void 0 : setting.option) || "");
      if ((setting == null ? void 0 : setting.category) === "admin") return "Admin";
      if (option.startsWith("publicBlog")) return "Blog";
      if (option.startsWith("publicSchool")) return "School";
      if (option.startsWith("publicMarket")) return "Market";
      return "Общие";
    };
    const resolveSubgroup = (setting) => {
      const option = String((setting == null ? void 0 : setting.option) || "");
      if (option.startsWith("publicBlogRubrics")) return "Rubrics";
      if (option.startsWith("publicBlogArticles")) return "Articles";
      if (option.startsWith("publicBlogTags")) return "Tags";
      if (option.startsWith("publicBlogBanners")) return "Banners";
      if (option.startsWith("publicBlogVideos")) return "Videos";
      if (option.startsWith("publicSchoolHashtags")) return "Hashtags";
      if (option.startsWith("publicSchoolInstructors")) return "Instructors";
      if (option.startsWith("publicSchoolTracks")) return "Tracks";
      if (option.startsWith("publicSchoolCourseSchedules")) return "Course Schedules";
      if (option.startsWith("publicSchoolCourses")) return "Courses";
      if (option.startsWith("publicSchoolModules")) return "Modules";
      if (option.startsWith("publicSchoolLessons")) return "Lessons";
      if (option.startsWith("publicSchoolAssignments")) return "Assignments";
      if (option.startsWith("publicSchoolQuizAttemptItems")) return "Quiz Attempt Items";
      if (option.startsWith("publicSchoolQuizAttempts")) return "Quiz Attempts";
      if (option.startsWith("publicSchoolQuizzes")) return "Quizzes";
      if (option.startsWith("publicSchoolBundles")) return "Bundles";
      if (option.startsWith("publicMarketProductVariants")) return "Product Variants";
      if (option.startsWith("publicMarketProductBundles")) return "Product Bundles";
      if (option.startsWith("publicMarketProducts")) return "Products";
      if (option.startsWith("publicMarketCompanies")) return "Companies";
      if (option.startsWith("publicMarketShops")) return "Shops";
      if (option.startsWith("publicMarketCategories")) return "Categories";
      if (option.startsWith("publicMarketBrands")) return "Brands";
      if (option.startsWith("publicMarketTags")) return "Tags";
      return "Основные";
    };
    const groupedSettings = computed(() => {
      const groups = {};
      filteredSettings.value.forEach((setting) => {
        const section = resolveSection(setting);
        const subgroup = resolveSubgroup(setting);
        if (!groups[section]) groups[section] = {};
        if (!groups[section][subgroup]) groups[section][subgroup] = [];
        groups[section][subgroup].push(setting);
      });
      Object.values(groups).forEach((section) => {
        Object.values(section).forEach((settings) => {
          settings.sort((a, b) => {
            const sortDifference = (Number(a.sort) || 0) - (Number(b.sort) || 0);
            return sortDifference || b.id - a.id;
          });
        });
      });
      return groups;
    });
    const sectionOrder = [
      "Общие",
      "Blog",
      "School",
      "Market",
      "Admin"
    ];
    const subgroupOrder = {
      "Общие": [
        "Основные"
      ],
      Blog: [
        "Rubrics",
        "Articles",
        "Tags",
        "Banners",
        "Videos",
        "Основные"
      ],
      School: [
        "Hashtags",
        "Instructors",
        "Tracks",
        "Courses",
        "Modules",
        "Lessons",
        "Assignments",
        "Course Schedules",
        "Quizzes",
        "Quiz Attempts",
        "Quiz Attempt Items",
        "Bundles",
        "Основные"
      ],
      Market: [
        "Companies",
        "Shops",
        "Categories",
        "Brands",
        "Tags",
        "Products",
        "Product Variants",
        "Product Bundles",
        "Основные"
      ],
      Admin: [
        "Основные"
      ]
    };
    const sections = computed(() => {
      return sectionOrder.filter((section) => {
        return groupedSettings.value[section];
      });
    });
    const getSubgroups = (section) => {
      const available = groupedSettings.value[section] || {};
      return (subgroupOrder[section] || ["Основные"]).filter((subgroup) => {
        var _a;
        return (_a = available[subgroup]) == null ? void 0 : _a.length;
      });
    };
    const getSectionCount = (section) => {
      return Object.values(groupedSettings.value[section] || {}).reduce((total, settings) => total + settings.length, 0);
    };
    const openModal = (description = "") => {
      modalDescription.value = description || "";
      showModal.value = true;
    };
    const closeModal = () => {
      showModal.value = false;
      modalDescription.value = "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("siteSettingsTitle")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("siteSettingsTitle"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("siteSettingsTitle")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("siteSettingsTitle")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="w-full max-w-12xl mx-auto px-2 sm:px-4 lg:px-6 py-3"${_scopeId}><div class="p-3 sm:p-4 lg:p-6 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}>`);
            if (__props.error) {
              _push2(`<div class="mb-4 p-3 text-sm border border-red-300 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300"${_scopeId}>${ssrInterpolate(__props.error)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="px-3 py-3 mb-4 border border-gray-400 dark:border-gray-500"${_scopeId}><div class="relative w-full"${_scopeId}><input${ssrRenderAttr("value", search.value)} type="text"${ssrRenderAttr("placeholder", unref(t)("searchByParameter"))} class="w-full px-2 py-1 pr-8 border border-slate-300 rounded-xs bg-white dark:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-gray-300"${_scopeId}><svg class="absolute right-2 top-2 w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.42-1.42l4.58 4.58a1 1 0 01-1.42 1.42l-4.58-4.58zm-4.9 0a6 6 0 100-12 6 6 0 000 12z" clip-rule="evenodd"${_scopeId}></path></svg></div></div>`);
            if (sections.value.length) {
              _push2(`<div class="space-y-10"${_scopeId}><!--[-->`);
              ssrRenderList(sections.value, (section) => {
                _push2(`<section${_scopeId}><div class="mb-4 pb-2 border-b-2 border-cyan-500 dark:border-cyan-300"${_scopeId}><div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between"${_scopeId}><h2 class="text-lg sm:text-xl font-bold text-cyan-700 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(section)}</h2><div class="text-xs font-semibold text-cyan-700 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(unref(t)("settings"))}: ${ssrInterpolate(getSectionCount(section))}</div></div></div><div class="space-y-6"${_scopeId}><!--[-->`);
                ssrRenderList(getSubgroups(section), (subgroup) => {
                  _push2(`<div class="px-2 py-1 shadow-md shadow-slate-400 dark:shadow-slate-900 rounded border border-gray-400 dark:border-gray-500"${_scopeId}>`);
                  if (subgroup !== "Основные" || getSubgroups(section).length > 1) {
                    _push2(`<div class="mb-2 pb-1 border-b border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex flex-col gap-1 sm:flex-row items-center sm:justify-between"${_scopeId}><h3 class="text-sm sm:text-base font-semibold text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(subgroup)}</h3><div class="font-semibold text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("settings"))}: ${ssrInterpolate(groupedSettings.value[section][subgroup].length)}</div></div></div>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  _push2(`<!--[-->`);
                  ssrRenderList(groupedSettings.value[section][subgroup], (setting) => {
                    _push2(ssrRenderComponent(_sfc_main$2, {
                      key: setting.id,
                      setting,
                      onShowDescription: openModal
                    }, null, _parent2, _scopeId));
                  });
                  _push2(`<!--]--></div>`);
                });
                _push2(`<!--]--></div></section>`);
              });
              _push2(`<!--]--></div>`);
            } else if (!__props.error) {
              _push2(`<div class="py-8 px-3 text-center text-sm sm:text-base text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            if (showModal.value) {
              _push2(ssrRenderComponent(_sfc_main$1, {
                showModal: showModal.value,
                modalDescription: modalDescription.value,
                onToggleModal: closeModal
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
          } else {
            return [
              createVNode("div", { class: "w-full max-w-12xl mx-auto px-2 sm:px-4 lg:px-6 py-3" }, [
                createVNode("div", { class: "p-3 sm:p-4 lg:p-6 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  __props.error ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "mb-4 p-3 text-sm border border-red-300 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300"
                  }, toDisplayString(__props.error), 1)) : createCommentVNode("", true),
                  createVNode("div", { class: "px-3 py-3 mb-4 border border-gray-400 dark:border-gray-500" }, [
                    createVNode("div", { class: "relative w-full" }, [
                      withDirectives(createVNode("input", {
                        "onUpdate:modelValue": ($event) => search.value = $event,
                        type: "text",
                        placeholder: unref(t)("searchByParameter"),
                        class: "w-full px-2 py-1 pr-8 border border-slate-300 rounded-xs bg-white dark:bg-gray-800 text-sm font-semibold text-gray-700 dark:text-gray-300"
                      }, null, 8, ["onUpdate:modelValue", "placeholder"]), [
                        [vModelText, search.value]
                      ]),
                      (openBlock(), createBlock("svg", {
                        class: "absolute right-2 top-2 w-4 h-4 text-gray-400 dark:text-gray-500",
                        fill: "currentColor",
                        viewBox: "0 0 20 20"
                      }, [
                        createVNode("path", {
                          "fill-rule": "evenodd",
                          d: "M12.9 14.32a8 8 0 111.42-1.42l4.58 4.58a1 1 0 01-1.42 1.42l-4.58-4.58zm-4.9 0a6 6 0 100-12 6 6 0 000 12z",
                          "clip-rule": "evenodd"
                        })
                      ]))
                    ])
                  ]),
                  sections.value.length ? (openBlock(), createBlock("div", {
                    key: 1,
                    class: "space-y-10"
                  }, [
                    (openBlock(true), createBlock(Fragment, null, renderList(sections.value, (section) => {
                      return openBlock(), createBlock("section", { key: section }, [
                        createVNode("div", { class: "mb-4 pb-2 border-b-2 border-cyan-500 dark:border-cyan-300" }, [
                          createVNode("div", { class: "flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between" }, [
                            createVNode("h2", { class: "text-lg sm:text-xl font-bold text-cyan-700 dark:text-cyan-300" }, toDisplayString(section), 1),
                            createVNode("div", { class: "text-xs font-semibold text-cyan-700 dark:text-cyan-300" }, toDisplayString(unref(t)("settings")) + ": " + toDisplayString(getSectionCount(section)), 1)
                          ])
                        ]),
                        createVNode("div", { class: "space-y-6" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(getSubgroups(section), (subgroup) => {
                            return openBlock(), createBlock("div", {
                              key: subgroup,
                              class: "px-2 py-1 shadow-md shadow-slate-400 dark:shadow-slate-900 rounded border border-gray-400 dark:border-gray-500"
                            }, [
                              subgroup !== "Основные" || getSubgroups(section).length > 1 ? (openBlock(), createBlock("div", {
                                key: 0,
                                class: "mb-2 pb-1 border-b border-slate-400 dark:border-slate-500"
                              }, [
                                createVNode("div", { class: "flex flex-col gap-1 sm:flex-row items-center sm:justify-between" }, [
                                  createVNode("h3", { class: "text-sm sm:text-base font-semibold text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(subgroup), 1),
                                  createVNode("div", { class: "font-semibold text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(unref(t)("settings")) + ": " + toDisplayString(groupedSettings.value[section][subgroup].length), 1)
                                ])
                              ])) : createCommentVNode("", true),
                              (openBlock(true), createBlock(Fragment, null, renderList(groupedSettings.value[section][subgroup], (setting) => {
                                return openBlock(), createBlock(_sfc_main$2, {
                                  key: setting.id,
                                  setting,
                                  onShowDescription: openModal
                                }, null, 8, ["setting"]);
                              }), 128))
                            ]);
                          }), 128))
                        ])
                      ]);
                    }), 128))
                  ])) : !__props.error ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "py-8 px-3 text-center text-sm sm:text-base text-slate-500 dark:text-slate-300"
                  }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true)
                ])
              ]),
              showModal.value ? (openBlock(), createBlock(_sfc_main$1, {
                key: 0,
                showModal: showModal.value,
                modalDescription: modalDescription.value,
                onToggleModal: closeModal
              }, null, 8, ["showModal", "modalDescription"])) : createCommentVNode("", true)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/Settings/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
