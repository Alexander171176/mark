import { computed, watch, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, withDirectives, Fragment, renderList, vModelSelect, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { useToast } from "vue-toastification";
import { useI18n } from "vue-i18n";
import { useForm } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$9 } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$8 } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$5 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$4 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$7 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$6 } from "./InputError-CLVdJ1nk.js";
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
const _sfc_main = {
  __name: "Edit",
  __ssrInlineRender: true,
  props: {
    preset: {
      type: Object,
      required: true
    }
  },
  setup(__props) {
    var _a;
    const toast = useToast();
    const { t } = useI18n();
    const props = __props;
    const presetData = ((_a = props.preset) == null ? void 0 : _a.data) ?? props.preset;
    const shapeOptions = [
      { value: "rectangle", label: t("shapeRectangle") },
      { value: "square", label: t("shapeSquare") },
      { value: "circle", label: t("shapeCircle") }
    ];
    const form = useForm({
      key: presetData.key || "",
      description: presetData.description || "",
      shape: presetData.shape || "rectangle",
      width: presetData.width ?? 1200,
      height: presetData.height ?? 800,
      image_rotation_enabled: !!presetData.image_rotation_enabled,
      crop_rotation_enabled: !!presetData.crop_rotation_enabled,
      max_file_size_kb: presetData.max_file_size_kb ?? 2048,
      keep_original: !!presetData.keep_original,
      sort: presetData.sort ?? 0
    });
    const isSingleSizeShape = computed(() => {
      return ["square", "circle"].includes(form.shape);
    });
    const maxFileSizeMb = computed(() => {
      return form.max_file_size_kb ? (Number(form.max_file_size_kb) / 1024).toFixed(2) : "0.00";
    });
    const resolutionPreview = computed(() => {
      return `${form.width || 0}×${form.height || 0}`;
    });
    watch(
      () => form.shape,
      (shape) => {
        if (["square", "circle"].includes(shape)) {
          form.height = form.width;
          form.crop_rotation_enabled = false;
        }
      }
    );
    watch(
      () => form.width,
      (width) => {
        if (isSingleSizeShape.value) {
          form.height = width;
        }
      }
    );
    const handleKeyInput = (event) => {
      form.key = event.target.value.toLowerCase().replace(/[^a-z0-9_\-\s]/g, "").replace(/[\s\-]+/g, "_").replace(/_+/g, "_").replace(/^_+|_+$/g, "");
    };
    const submitForm = () => {
      form.transform((data) => ({
        key: data.key,
        description: data.description,
        shape: data.shape,
        width: data.width,
        height: ["square", "circle"].includes(data.shape) ? data.width : data.height,
        image_rotation_enabled: data.image_rotation_enabled ? 1 : 0,
        crop_rotation_enabled: data.shape === "rectangle" && data.crop_rotation_enabled ? 1 : 0,
        max_file_size_kb: data.max_file_size_kb,
        keep_original: data.keep_original ? 1 : 0,
        sort: data.sort
      }));
      form.put(route("admin.imagePresets.update", {
        imagePreset: presetData.id
      }), {
        preserveScroll: true,
        onSuccess: () => {
          toast.success("Пресет обработки изображений успешно обновлён.");
        },
        onError: (errors) => {
          const firstError = errors[Object.keys(errors)[0]];
          toast.error(firstError || "Проверьте правильность заполнения полей.");
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editImagePreset")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editImagePreset"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editImagePreset")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editImagePreset")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.imagePresets.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                    ]))
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("back"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div><form class="p-3 w-full"${_scopeId}><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-col sm:flex-row gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).image_rotation_enabled,
              "onUpdate:modelValue": ($event) => unref(form).image_rotation_enabled = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "image_rotation_enabled",
              text: unref(t)("imageRotationEnabled")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).crop_rotation_enabled,
              "onUpdate:modelValue": ($event) => unref(form).crop_rotation_enabled = $event,
              disabled: isSingleSizeShape.value
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "crop_rotation_enabled",
              text: unref(t)("cropRotationEnabled")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).keep_original,
              "onUpdate:modelValue": ($event) => unref(form).keep_original = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "keep_original",
              text: unref(t)("keepOriginal")
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "sort",
              value: unref(t)("sort"),
              class: "text-sm"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "sort",
              type: "number",
              modelValue: unref(form).sort,
              "onUpdate:modelValue": ($event) => unref(form).sort = $event,
              min: "0",
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2 lg:mt-0",
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 grid grid-cols-1 lg:grid-cols-2 gap-3"${_scopeId}><div class="flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "key" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}> * </span> ${ssrInterpolate(unref(t)("key"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                    createTextVNode(" " + toDisplayString(unref(t)("key")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<div class="text-sm text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate(unref(form).key.length)} / 100 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "key",
              type: "text",
              modelValue: unref(form).key,
              "onUpdate:modelValue": ($event) => unref(form).key = $event,
              onInput: handleKeyInput,
              required: "",
              maxlength: "100",
              pattern: "[a-z0-9_]+",
              title: unref(t)("onlyText")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.key
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "shape" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("shape"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("shape")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<select id="shape" required class="w-full form-select rounded-sm py-0.5 bg-white dark:bg-cyan-800 text-sm text-slate-700 dark:text-slate-100 border border-slate-400 dark:border-slate-600"${_scopeId}><!--[-->`);
            ssrRenderList(shapeOptions, (option) => {
              _push2(`<option${ssrRenderAttr("value", option.value)}${ssrIncludeBooleanAttr(Array.isArray(unref(form).shape) ? ssrLooseContain(unref(form).shape, option.value) : ssrLooseEqual(unref(form).shape, option.value)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(option.label)}</option>`);
            });
            _push2(`<!--]--></select>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.shape
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "description",
              value: unref(t)("description")
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-sm text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate(unref(form).description.length)} / 500 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              id: "description",
              modelValue: unref(form).description,
              "onUpdate:modelValue": ($event) => unref(form).description = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.description
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 grid grid-cols-1 lg:grid-cols-3 gap-4"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "width",
              value: isSingleSizeShape.value ? unref(t)("size") : unref(t)("width")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "width",
              type: "number",
              modelValue: unref(form).width,
              "onUpdate:modelValue": ($event) => unref(form).width = $event,
              min: "1",
              max: "10000"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.width
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (!isSingleSizeShape.value) {
              _push2(`<div${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                for: "height",
                value: unref(t)("height")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$5, {
                id: "height",
                type: "number",
                modelValue: unref(form).height,
                "onUpdate:modelValue": ($event) => unref(form).height = $event,
                min: "1",
                max: "10000"
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                class: "mt-2",
                message: unref(form).errors.height
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "max_file_size_kb",
              value: unref(t)("maxFileSizeKb")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "max_file_size_kb",
              type: "number",
              modelValue: unref(form).max_file_size_kb,
              "onUpdate:modelValue": ($event) => unref(form).max_file_size_kb = $event,
              min: "128",
              max: "51200"
            }, null, _parent2, _scopeId));
            _push2(`<div class="mt-1 text-xs text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(maxFileSizeMb.value)} MB </div>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.max_file_size_kb
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 p-3 rounded-sm border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800"${_scopeId}><div class="text-sm font-semibold text-slate-700 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("view"))}</div><div class="mt-2 text-sm text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("shape"))}: <span class="font-semibold"${_scopeId}>${ssrInterpolate(unref(form).shape)}</span></div><div class="text-sm text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("typeSize"))}: <span class="font-semibold"${_scopeId}>${ssrInterpolate(resolutionPreview.value)}</span></div><div class="text-sm text-slate-600 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("file"))}: <span class="font-semibold"${_scopeId}>${ssrInterpolate(unref(form).max_file_size_kb)} KB / ${ssrInterpolate(maxFileSizeMb.value)} MB </span></div></div><div class="flex items-center justify-center mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.imagePresets.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                    ]))
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("back"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              class: ["ms-4", { "opacity-25": unref(form).processing }],
              disabled: unref(form).processing
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("save"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("save")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></form></div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-2" }, [
                    createVNode(_sfc_main$1, {
                      href: _ctx.route("admin.imagePresets.index")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                        ]))
                      ]),
                      default: withCtx(() => [
                        createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ]),
                  createVNode("form", {
                    onSubmit: withModifiers(submitForm, ["prevent"]),
                    class: "p-3 w-full"
                  }, [
                    createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                      createVNode("div", { class: "flex flex-col sm:flex-row gap-4" }, [
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode(_sfc_main$2, {
                            modelValue: unref(form).image_rotation_enabled,
                            "onUpdate:modelValue": ($event) => unref(form).image_rotation_enabled = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            for: "image_rotation_enabled",
                            text: unref(t)("imageRotationEnabled")
                          }, null, 8, ["text"])
                        ]),
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode(_sfc_main$2, {
                            modelValue: unref(form).crop_rotation_enabled,
                            "onUpdate:modelValue": ($event) => unref(form).crop_rotation_enabled = $event,
                            disabled: isSingleSizeShape.value
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "disabled"]),
                          createVNode(_sfc_main$3, {
                            for: "crop_rotation_enabled",
                            text: unref(t)("cropRotationEnabled")
                          }, null, 8, ["text"])
                        ]),
                        createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                          createVNode(_sfc_main$2, {
                            modelValue: unref(form).keep_original,
                            "onUpdate:modelValue": ($event) => unref(form).keep_original = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            for: "keep_original",
                            text: unref(t)("keepOriginal")
                          }, null, 8, ["text"])
                        ])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$4, {
                          for: "sort",
                          value: unref(t)("sort"),
                          class: "text-sm"
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$5, {
                          id: "sort",
                          type: "number",
                          modelValue: unref(form).sort,
                          "onUpdate:modelValue": ($event) => unref(form).sort = $event,
                          min: "0",
                          class: "w-full lg:w-28"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2 lg:mt-0",
                          message: unref(form).errors.sort
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 grid grid-cols-1 lg:grid-cols-2 gap-3" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode("div", { class: "flex justify-between w-full" }, [
                          createVNode(_sfc_main$4, { for: "key" }, {
                            default: withCtx(() => [
                              createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                              createTextVNode(" " + toDisplayString(unref(t)("key")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode("div", { class: "text-sm text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString(unref(form).key.length) + " / 100 " + toDisplayString(unref(t)("characters")), 1)
                        ]),
                        createVNode(_sfc_main$7, {
                          id: "key",
                          type: "text",
                          modelValue: unref(form).key,
                          "onUpdate:modelValue": ($event) => unref(form).key = $event,
                          onInput: handleKeyInput,
                          required: "",
                          maxlength: "100",
                          pattern: "[a-z0-9_]+",
                          title: unref(t)("onlyText")
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "title"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.key
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, { for: "shape" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("shape")), 1)
                          ]),
                          _: 1
                        }),
                        withDirectives(createVNode("select", {
                          id: "shape",
                          "onUpdate:modelValue": ($event) => unref(form).shape = $event,
                          required: "",
                          class: "w-full form-select rounded-sm py-0.5 bg-white dark:bg-cyan-800 text-sm text-slate-700 dark:text-slate-100 border border-slate-400 dark:border-slate-600"
                        }, [
                          (openBlock(), createBlock(Fragment, null, renderList(shapeOptions, (option) => {
                            return createVNode("option", {
                              key: option.value,
                              value: option.value
                            }, toDisplayString(option.label), 9, ["value"]);
                          }), 64))
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).shape]
                        ]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.shape
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                      createVNode("div", { class: "flex justify-between w-full" }, [
                        createVNode(_sfc_main$4, {
                          for: "description",
                          value: unref(t)("description")
                        }, null, 8, ["value"]),
                        createVNode("div", { class: "text-sm text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString(unref(form).description.length) + " / 500 " + toDisplayString(unref(t)("characters")), 1)
                      ]),
                      createVNode(_sfc_main$8, {
                        id: "description",
                        modelValue: unref(form).description,
                        "onUpdate:modelValue": ($event) => unref(form).description = $event,
                        class: "w-full"
                      }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                      createVNode(_sfc_main$6, {
                        class: "mt-2",
                        message: unref(form).errors.description
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "mb-3 grid grid-cols-1 lg:grid-cols-3 gap-4" }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "width",
                          value: isSingleSizeShape.value ? unref(t)("size") : unref(t)("width")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$5, {
                          id: "width",
                          type: "number",
                          modelValue: unref(form).width,
                          "onUpdate:modelValue": ($event) => unref(form).width = $event,
                          min: "1",
                          max: "10000"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.width
                        }, null, 8, ["message"])
                      ]),
                      !isSingleSizeShape.value ? (openBlock(), createBlock("div", { key: 0 }, [
                        createVNode(_sfc_main$4, {
                          for: "height",
                          value: unref(t)("height")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$5, {
                          id: "height",
                          type: "number",
                          modelValue: unref(form).height,
                          "onUpdate:modelValue": ($event) => unref(form).height = $event,
                          min: "1",
                          max: "10000"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.height
                        }, null, 8, ["message"])
                      ])) : createCommentVNode("", true),
                      createVNode("div", null, [
                        createVNode(_sfc_main$4, {
                          for: "max_file_size_kb",
                          value: unref(t)("maxFileSizeKb")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$5, {
                          id: "max_file_size_kb",
                          type: "number",
                          modelValue: unref(form).max_file_size_kb,
                          "onUpdate:modelValue": ($event) => unref(form).max_file_size_kb = $event,
                          min: "128",
                          max: "51200"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode("div", { class: "mt-1 text-xs text-slate-500 dark:text-slate-300" }, toDisplayString(maxFileSizeMb.value) + " MB ", 1),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: unref(form).errors.max_file_size_kb
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 p-3 rounded-sm border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800" }, [
                      createVNode("div", { class: "text-sm font-semibold text-slate-700 dark:text-slate-100" }, toDisplayString(unref(t)("view")), 1),
                      createVNode("div", { class: "mt-2 text-sm text-slate-600 dark:text-slate-300" }, [
                        createTextVNode(toDisplayString(unref(t)("shape")) + ": ", 1),
                        createVNode("span", { class: "font-semibold" }, toDisplayString(unref(form).shape), 1)
                      ]),
                      createVNode("div", { class: "text-sm text-slate-600 dark:text-slate-300" }, [
                        createTextVNode(toDisplayString(unref(t)("typeSize")) + ": ", 1),
                        createVNode("span", { class: "font-semibold" }, toDisplayString(resolutionPreview.value), 1)
                      ]),
                      createVNode("div", { class: "text-sm text-slate-600 dark:text-slate-300" }, [
                        createTextVNode(toDisplayString(unref(t)("file")) + ": ", 1),
                        createVNode("span", { class: "font-semibold" }, toDisplayString(unref(form).max_file_size_kb) + " KB / " + toDisplayString(maxFileSizeMb.value) + " MB ", 1)
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center mt-4" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.imagePresets.index")
                      }, {
                        icon: withCtx(() => [
                          (openBlock(), createBlock("svg", {
                            class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                            viewBox: "0 0 16 16"
                          }, [
                            createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                          ]))
                        ]),
                        default: withCtx(() => [
                          createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                        ]),
                        _: 1
                      }, 8, ["href"]),
                      createVNode(_sfc_main$9, {
                        class: ["ms-4", { "opacity-25": unref(form).processing }],
                        disabled: unref(form).processing
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(unref(t)("save")), 1)
                        ]),
                        _: 1
                      }, 8, ["class", "disabled"])
                    ])
                  ], 32)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/ImagePresets/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
