import { ref, mergeProps, useSSRContext, computed, watch, onBeforeUnmount, unref, withCtx, createBlock, openBlock, createVNode, toDisplayString } from "vue";
import { ssrRenderAttrs, ssrRenderSlot, ssrRenderTeleport, ssrInterpolate, ssrRenderComponent, ssrRenderList, ssrRenderAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$6 } from "./LabelInput-C61CQHdu.js";
import { CircleStencil, RectangleStencil, Cropper } from "vue-advanced-cropper";
import "./vendor-V_Tb0Wa1.js";
import { _ as _sfc_main$5 } from "./PrimaryButton-B3InEAXg.js";
const _sfc_main$4 = {
  __name: "SecondaryButton",
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
        class: ["flex items-center btn px-3 py-0.5 bg-violet-500 shadow-md text-white text-sm font-semibold transition-colors duration-300 ease-in-out hover:bg-violet-600 focus:bg-violet-600 focus:outline-none", { "ring-2 ring-violet-500 ring-offset-2 ring-offset-white": isPressed.value }]
      }, _attrs))}><span>`);
      ssrRenderSlot(_ctx.$slots, "icon", {}, () => {
        _push(`<svg class="w-4 h-4 fill-current text-slate-100" viewBox="0 0 16 16"><path d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"></path></svg>`);
      }, _push, _parent);
      _push(`</span></button>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/SecondaryButton.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "WarningButton",
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
        class: ["flex items-center btn px-2 py-0.5 bg-rose-500 shadow-md text-white text-sm font-semibold transition-colors duration-300 ease-in-out hover:bg-rose-600 focus:bg-rose-600 focus:outline-none", { "ring-2 ring-rose-500 ring-offset-2 ring-offset-white": isPressed.value }]
      }, _attrs))}><span>`);
      ssrRenderSlot(_ctx.$slots, "icon", {}, () => {
        _push(`<svg class="w-4 h-4 fill-current text-slate-100" viewBox="0 0 16 16"><path d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"></path></svg>`);
      }, _push, _parent);
      _push(`</span><span class="hidden xs:block ml-2">`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</span></button>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/WarningButton.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "GreenButton",
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
        class: ["flex items-center btn px-3 py-0.5 bg-emerald-500 shadow-md text-white text-sm font-semibold transition-colors duration-300 ease-in-out hover:bg-emerald-600 focus:bg-emerald-600 focus:outline-none", { "ring-2 ring-emerald-500 ring-offset-2 ring-offset-white": isPressed.value }]
      }, _attrs))}><span>`);
      ssrRenderSlot(_ctx.$slots, "icon", {}, () => {
        _push(`<svg class="w-4 h-4 fill-current text-slate-100" viewBox="0 0 16 16"><path d="M14.3 2.3L5 11.6 1.7 8.3c-.4-.4-1-.4-1.4 0-.4.4-.4 1 0 1.4l4 4c.2.2.4.3.7.3.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4-.4-.4-1-.4-1.4 0z"></path></svg>`);
      }, _push, _parent);
      _push(`</span></button>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Buttons/GreenButton.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "ImageEditorModal",
  __ssrInlineRender: true,
  props: {
    show: {
      type: Boolean,
      default: false
    },
    file: {
      type: [File, null],
      default: null
    },
    preset: {
      type: Object,
      required: true
    }
  },
  emits: [
    "close",
    "save"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const cropper = ref(null);
    const imageSrc = ref(null);
    const imageRotation = ref(0);
    const cropRotation = ref(0);
    const shape = computed(() => {
      var _a;
      return ((_a = props.preset) == null ? void 0 : _a.shape) || "rectangle";
    });
    const isCircle = computed(() => {
      return shape.value === "circle";
    });
    const isRectangle = computed(() => {
      return shape.value === "rectangle";
    });
    const isFixedShape = computed(() => {
      return ["square", "circle"].includes(shape.value);
    });
    const targetWidth = computed(() => {
      var _a;
      return Number(((_a = props.preset) == null ? void 0 : _a.width) || 1);
    });
    const targetHeight = computed(() => {
      var _a;
      return Number(((_a = props.preset) == null ? void 0 : _a.height) || 1);
    });
    const rotatedTargetWidth = computed(() => {
      if (!isRectangle.value) {
        return targetWidth.value;
      }
      return cropRotation.value === 90 ? targetHeight.value : targetWidth.value;
    });
    const rotatedTargetHeight = computed(() => {
      if (!isRectangle.value) {
        return targetHeight.value;
      }
      return cropRotation.value === 90 ? targetWidth.value : targetHeight.value;
    });
    const aspectRatio = computed(() => {
      if (isFixedShape.value) {
        return 1;
      }
      return rotatedTargetWidth.value / rotatedTargetHeight.value;
    });
    const stencilComponent = computed(() => {
      return isCircle.value ? CircleStencil : RectangleStencil;
    });
    const stencilProps = computed(() => {
      return {
        aspectRatio: aspectRatio.value,
        movable: true,
        resizable: !isFixedShape.value,
        scalable: true
      };
    });
    const canRotateImage = computed(() => {
      var _a;
      return Boolean((_a = props.preset) == null ? void 0 : _a.image_rotation_enabled);
    });
    const canRotateCropFrame = computed(() => {
      var _a;
      return Boolean((_a = props.preset) == null ? void 0 : _a.crop_rotation_enabled) && isRectangle.value;
    });
    const cropperKey = computed(() => {
      return [
        shape.value,
        cropRotation.value,
        rotatedTargetWidth.value,
        rotatedTargetHeight.value
      ].join("-");
    });
    const resultFileName = computed(() => {
      var _a;
      const key = ((_a = props.preset) == null ? void 0 : _a.key) || "image";
      return `${key}.webp`;
    });
    const normalizeAngle = (value) => {
      return (value % 360 + 360) % 360;
    };
    const resetState = () => {
      imageRotation.value = 0;
      cropRotation.value = 0;
    };
    const revokeImageUrl = () => {
      if (imageSrc.value) {
        URL.revokeObjectURL(imageSrc.value);
        imageSrc.value = null;
      }
    };
    watch(
      () => props.file,
      (file) => {
        resetState();
        revokeImageUrl();
        if (!file) {
          return;
        }
        imageSrc.value = URL.createObjectURL(file);
      },
      { immediate: true }
    );
    onBeforeUnmount(() => {
      revokeImageUrl();
    });
    const rotateImageLeft = () => {
      var _a, _b;
      if (!canRotateImage.value) {
        return;
      }
      imageRotation.value = normalizeAngle(imageRotation.value - 90);
      (_b = (_a = cropper.value) == null ? void 0 : _a.rotate) == null ? void 0 : _b.call(_a, -90);
    };
    const rotateImageRight = () => {
      var _a, _b;
      if (!canRotateImage.value) {
        return;
      }
      imageRotation.value = normalizeAngle(imageRotation.value + 90);
      (_b = (_a = cropper.value) == null ? void 0 : _a.rotate) == null ? void 0 : _b.call(_a, 90);
    };
    const rotateCropFrame = () => {
      if (!canRotateCropFrame.value) {
        return;
      }
      cropRotation.value = cropRotation.value === 0 ? 90 : 0;
    };
    const saveImage = () => {
      var _a, _b;
      const result = (_b = (_a = cropper.value) == null ? void 0 : _a.getResult) == null ? void 0 : _b.call(_a);
      if (!(result == null ? void 0 : result.canvas)) {
        return;
      }
      const canvas = document.createElement("canvas");
      canvas.width = rotatedTargetWidth.value;
      canvas.height = rotatedTargetHeight.value;
      const context = canvas.getContext("2d");
      context.drawImage(
        result.canvas,
        0,
        0,
        canvas.width,
        canvas.height
      );
      canvas.toBlob(
        (blob) => {
          if (!blob) {
            return;
          }
          const file = new File(
            [blob],
            resultFileName.value,
            {
              type: "image/webp"
            }
          );
          emit("save", {
            blob,
            file,
            coordinates: result.coordinates,
            preset_key: props.preset.key,
            shape: shape.value,
            width: rotatedTargetWidth.value,
            height: rotatedTargetHeight.value,
            image_rotation: imageRotation.value,
            crop_rotation: cropRotation.value
          });
        },
        "image/webp",
        0.85
      );
    };
    const closeModal = () => {
      emit("close");
    };
    return (_ctx, _push, _parent, _attrs) => {
      ssrRenderTeleport(_push, (_push2) => {
        if (__props.show) {
          _push2(`<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-2 sm:p-4"><div class="w-full max-w-3xl max-h-[95vh] flex flex-col rounded-sm bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 shadow-xl overflow-hidden"><div class="shrink-0 flex items-center justify-between px-4 py-1 border-b border-slate-300 dark:border-slate-600"><div><h3 class="text-base sm:text-lg font-semibold text-slate-800 dark:text-slate-100">${ssrInterpolate(unref(t)("imageEditor"))}</h3><div class="text-xs text-slate-500 dark:text-slate-300">${ssrInterpolate(__props.preset.key)} — ${ssrInterpolate(rotatedTargetWidth.value)}×${ssrInterpolate(rotatedTargetHeight.value)}</div></div><button type="button" class="text-slate-500 hover:text-red-500"> ✕ </button></div><div class="flex-1 overflow-y-auto p-3 sm:p-4">`);
          if (imageSrc.value) {
            _push2(`<div class="h-[45vh] min-h-[260px] max-h-[520px] bg-slate-100 dark:bg-slate-900 border border-slate-300 dark:border-slate-600">`);
            _push2(ssrRenderComponent(unref(Cropper), {
              key: cropperKey.value,
              ref_key: "cropper",
              ref: cropper,
              class: "h-full",
              src: imageSrc.value,
              "stencil-component": stencilComponent.value,
              "stencil-props": stencilProps.value
            }, null, _parent));
            _push2(`</div>`);
          } else {
            _push2(`<div class="h-64 flex items-center justify-center text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("noImage"))}</div>`);
          }
          _push2(`<div class="mt-3 flex flex-wrap items-center justify-center gap-2">`);
          _push2(ssrRenderComponent(_sfc_main$4, {
            type: "button",
            class: "py-2.5 rounded-sm",
            title: unref(t)("rotateImageLeft"),
            disabled: !canRotateImage.value,
            onClick: rotateImageLeft
          }, {
            icon: withCtx((_, _push3, _parent2, _scopeId) => {
              if (_push3) {
                _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0" viewBox="0 0 448 512"${_scopeId}><path d="M8.309 189.836L184.313 37.851C199.719 24.546 224 35.347 224 56.015v80.053c160.629 1.839 288 34.032 288 186.258 0 61.441-39.581 122.309-83.333 154.132-13.653 9.931-33.111-2.533-28.077-18.631 45.344-145.012-21.507-183.51-176.59-185.742V360c0 20.7-24.3 31.453-39.687 18.164l-176.004-152c-11.071-9.562-11.086-26.753 0-36.328z"${_scopeId}></path></svg>`);
              } else {
                return [
                  (openBlock(), createBlock("svg", {
                    class: "w-4 h-4 fill-current text-slate-100 shrink-0",
                    viewBox: "0 0 448 512"
                  }, [
                    createVNode("path", { d: "M8.309 189.836L184.313 37.851C199.719 24.546 224 35.347 224 56.015v80.053c160.629 1.839 288 34.032 288 186.258 0 61.441-39.581 122.309-83.333 154.132-13.653 9.931-33.111-2.533-28.077-18.631 45.344-145.012-21.507-183.51-176.59-185.742V360c0 20.7-24.3 31.453-39.687 18.164l-176.004-152c-11.071-9.562-11.086-26.753 0-36.328z" })
                  ]))
                ];
              }
            }),
            _: 1
          }, _parent));
          _push2(ssrRenderComponent(_sfc_main$2, {
            type: "button",
            class: "py-2.5 rounded-sm",
            title: unref(t)("rotateCropFrame"),
            disabled: !canRotateCropFrame.value,
            onClick: rotateCropFrame
          }, {
            icon: withCtx((_, _push3, _parent2, _scopeId) => {
              if (_push3) {
                _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0" viewBox="0 0 576 512"${_scopeId}><path d="M561.938 158.06L417.94 14.092C387.926-15.922 336 5.097 336 48.032v57.198c-42.45 1.88-84.03 6.55-120.76 17.99-35.17 10.95-63.07 27.58-82.91 49.42C108.22 199.2 96 232.6 96 271.94c0 61.697 33.178 112.455 84.87 144.76 37.546 23.508 85.248-12.651 71.02-55.74-15.515-47.119-17.156-70.923 84.11-78.76V336c0 42.993 51.968 63.913 81.94 33.94l143.998-144c18.75-18.74 18.75-49.14 0-67.88zM384 336V232.16C255.309 234.082 166.492 255.35 206.31 376 176.79 357.55 144 324.08 144 271.94c0-109.334 129.14-118.947 240-119.85V48l144 144-144 144zm24.74 84.493a82.658 82.658 0 0 0 20.974-9.303c7.976-4.952 18.286.826 18.286 10.214V464c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V112c0-26.51 21.49-48 48-48h132c6.627 0 12 5.373 12 12v4.486c0 4.917-2.987 9.369-7.569 11.152-13.702 5.331-26.396 11.537-38.05 18.585a12.138 12.138 0 0 1-6.28 1.777H54a6 6 0 0 0-6 6v340a6 6 0 0 0 6 6h340a6 6 0 0 0 6-6v-25.966c0-5.37 3.579-10.059 8.74-11.541z"${_scopeId}></path></svg>`);
              } else {
                return [
                  (openBlock(), createBlock("svg", {
                    class: "w-4 h-4 fill-current text-slate-100 shrink-0",
                    viewBox: "0 0 576 512"
                  }, [
                    createVNode("path", { d: "M561.938 158.06L417.94 14.092C387.926-15.922 336 5.097 336 48.032v57.198c-42.45 1.88-84.03 6.55-120.76 17.99-35.17 10.95-63.07 27.58-82.91 49.42C108.22 199.2 96 232.6 96 271.94c0 61.697 33.178 112.455 84.87 144.76 37.546 23.508 85.248-12.651 71.02-55.74-15.515-47.119-17.156-70.923 84.11-78.76V336c0 42.993 51.968 63.913 81.94 33.94l143.998-144c18.75-18.74 18.75-49.14 0-67.88zM384 336V232.16C255.309 234.082 166.492 255.35 206.31 376 176.79 357.55 144 324.08 144 271.94c0-109.334 129.14-118.947 240-119.85V48l144 144-144 144zm24.74 84.493a82.658 82.658 0 0 0 20.974-9.303c7.976-4.952 18.286.826 18.286 10.214V464c0 26.51-21.49 48-48 48H48c-26.51 0-48-21.49-48-48V112c0-26.51 21.49-48 48-48h132c6.627 0 12 5.373 12 12v4.486c0 4.917-2.987 9.369-7.569 11.152-13.702 5.331-26.396 11.537-38.05 18.585a12.138 12.138 0 0 1-6.28 1.777H54a6 6 0 0 0-6 6v340a6 6 0 0 0 6 6h340a6 6 0 0 0 6-6v-25.966c0-5.37 3.579-10.059 8.74-11.541z" })
                  ]))
                ];
              }
            }),
            _: 1
          }, _parent));
          _push2(ssrRenderComponent(_sfc_main$4, {
            type: "button",
            class: "py-2.5 rounded-sm",
            title: unref(t)("rotateImageRight"),
            disabled: !canRotateImage.value,
            onClick: rotateImageRight
          }, {
            icon: withCtx((_, _push3, _parent2, _scopeId) => {
              if (_push3) {
                _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0" viewBox="0 0 448 512"${_scopeId}><path d="M503.691 189.836L327.687 37.851C312.281 24.546 288 35.347 288 56.015v80.053C127.371 137.907 0 170.1 0 322.326c0 61.441 39.581 122.309 83.333 154.132 13.653 9.931 33.111-2.533 28.077-18.631C66.066 312.814 132.917 274.316 288 272.085V360c0 20.7 24.3 31.453 39.687 18.164l176.004-152c11.071-9.562 11.086-26.753 0-36.328z"${_scopeId}></path></svg>`);
              } else {
                return [
                  (openBlock(), createBlock("svg", {
                    class: "w-4 h-4 fill-current text-slate-100 shrink-0",
                    viewBox: "0 0 448 512"
                  }, [
                    createVNode("path", { d: "M503.691 189.836L327.687 37.851C312.281 24.546 288 35.347 288 56.015v80.053C127.371 137.907 0 170.1 0 322.326c0 61.441 39.581 122.309 83.333 154.132 13.653 9.931 33.111-2.533 28.077-18.631C66.066 312.814 132.917 274.316 288 272.085V360c0 20.7 24.3 31.453 39.687 18.164l176.004-152c11.071-9.562 11.086-26.753 0-36.328z" })
                  ]))
                ];
              }
            }),
            _: 1
          }, _parent));
          _push2(`</div></div><div class="shrink-0 flex items-center justify-center gap-3 px-3 sm:px-4 py-3 border-t border-slate-300 dark:border-slate-600">`);
          _push2(ssrRenderComponent(_sfc_main$3, {
            type: "button",
            class: "text-sm py-0.5 rounded-sm",
            onClick: closeModal
          }, {
            icon: withCtx((_, _push3, _parent2, _scopeId) => {
              if (_push3) {
                _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0" viewBox="0 0 512 512"${_scopeId}><path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm101.8-262.2L295.6 256l62.2 62.2c4.7 4.7 4.7 12.3 0 17l-22.6 22.6c-4.7 4.7-12.3 4.7-17 0L256 295.6l-62.2 62.2c-4.7 4.7-12.3 4.7-17 0l-22.6-22.6c-4.7-4.7-4.7-12.3 0-17l62.2-62.2-62.2-62.2c-4.7-4.7-4.7-12.3 0-17l22.6-22.6c4.7-4.7 12.3-4.7 17 0l62.2 62.2 62.2-62.2c4.7-4.7 12.3-4.7 17 0l22.6 22.6c4.7 4.7 4.7 12.3 0 17z"${_scopeId}></path></svg>`);
              } else {
                return [
                  (openBlock(), createBlock("svg", {
                    class: "w-4 h-4 fill-current text-slate-100 shrink-0",
                    viewBox: "0 0 512 512"
                  }, [
                    createVNode("path", { d: "M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm101.8-262.2L295.6 256l62.2 62.2c4.7 4.7 4.7 12.3 0 17l-22.6 22.6c-4.7 4.7-12.3 4.7-17 0L256 295.6l-62.2 62.2c-4.7 4.7-12.3 4.7-17 0l-22.6-22.6c-4.7-4.7-4.7-12.3 0-17l62.2-62.2-62.2-62.2c-4.7-4.7-4.7-12.3 0-17l22.6-22.6c4.7-4.7 12.3-4.7 17 0l62.2 62.2 62.2-62.2c4.7-4.7 12.3-4.7 17 0l22.6 22.6c4.7 4.7 4.7 12.3 0 17z" })
                  ]))
                ];
              }
            }),
            default: withCtx((_, _push3, _parent2, _scopeId) => {
              if (_push3) {
                _push3(`<span${_scopeId}>${ssrInterpolate(unref(t)("cancel"))}</span>`);
              } else {
                return [
                  createVNode("span", null, toDisplayString(unref(t)("cancel")), 1)
                ];
              }
            }),
            _: 1
          }, _parent));
          _push2(ssrRenderComponent(_sfc_main$5, {
            type: "button",
            class: "text-sm py-0.5 rounded-sm",
            onClick: saveImage
          }, {
            icon: withCtx((_, _push3, _parent2, _scopeId) => {
              if (_push3) {
                _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0" viewBox="0 0 448 512"${_scopeId}><path d="M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM272 80v80H144V80h128zm122 352H54a6 6 0 0 1-6-6V86a6 6 0 0 1 6-6h42v104c0 13.255 10.745 24 24 24h176c13.255 0 24-10.745 24-24V83.882l78.243 78.243a6 6 0 0 1 1.757 4.243V426a6 6 0 0 1-6 6zM224 232c-48.523 0-88 39.477-88 88s39.477 88 88 88 88-39.477 88-88-39.477-88-88-88zm0 128c-22.056 0-40-17.944-40-40s17.944-40 40-40 40 17.944 40 40-17.944 40-40 40z"${_scopeId}></path></svg>`);
              } else {
                return [
                  (openBlock(), createBlock("svg", {
                    class: "w-4 h-4 fill-current text-slate-100 shrink-0",
                    viewBox: "0 0 448 512"
                  }, [
                    createVNode("path", { d: "M433.941 129.941l-83.882-83.882A48 48 0 0 0 316.118 32H48C21.49 32 0 53.49 0 80v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V163.882a48 48 0 0 0-14.059-33.941zM272 80v80H144V80h128zm122 352H54a6 6 0 0 1-6-6V86a6 6 0 0 1 6-6h42v104c0 13.255 10.745 24 24 24h176c13.255 0 24-10.745 24-24V83.882l78.243 78.243a6 6 0 0 1 1.757 4.243V426a6 6 0 0 1-6 6zM224 232c-48.523 0-88 39.477-88 88s39.477 88 88 88 88-39.477 88-88-39.477-88-88-88zm0 128c-22.056 0-40-17.944-40-40s17.944-40 40-40 40 17.944 40 40-17.944 40-40 40z" })
                  ]))
                ];
              }
            }),
            default: withCtx((_, _push3, _parent2, _scopeId) => {
              if (_push3) {
                _push3(`<span${_scopeId}>${ssrInterpolate(unref(t)("save"))}</span>`);
              } else {
                return [
                  createVNode("span", null, toDisplayString(unref(t)("save")), 1)
                ];
              }
            }),
            _: 1
          }, _parent));
          _push2(`</div><div class="mb-3 mx-3 grid grid-cols-1 sm:grid-cols-3 gap-3"><div class="p-2 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-xs sm:text-sm text-slate-700 dark:text-slate-100"><div class="font-semibold">${ssrInterpolate(unref(t)("shape"))}</div><div>${ssrInterpolate(shape.value)}</div></div><div class="p-2 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-xs sm:text-sm text-slate-700 dark:text-slate-100"><div class="font-semibold">${ssrInterpolate(unref(t)("rotation"))}</div><div>${ssrInterpolate(imageRotation.value)}° </div></div><div class="p-2 rounded-sm border border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-xs sm:text-sm text-slate-700 dark:text-slate-100"><div class="font-semibold">${ssrInterpolate(unref(t)("typeSize"))}</div><div>${ssrInterpolate(rotatedTargetWidth.value)}×${ssrInterpolate(rotatedTargetHeight.value)}</div></div></div></div></div>`);
        } else {
          _push2(`<!---->`);
        }
      }, "body", false, _parent);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Image/ImageEditorModal.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "MultiImagePresetUpload",
  __ssrInlineRender: true,
  props: {
    preset: {
      type: Object,
      required: true
    }
  },
  emits: ["update:images"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const emit = __emit;
    ref(null);
    const previewImages = ref([]);
    const editorVisible = ref(false);
    const editingFile = ref(null);
    const handleEditorSave = (payload) => {
      const file = payload.file;
      const url = URL.createObjectURL(file);
      previewImages.value.push({
        file,
        url,
        order: previewImages.value.length + 1,
        alt: "",
        caption: ""
      });
      editorVisible.value = false;
      editingFile.value = null;
      updateImages();
    };
    const updateImages = () => {
      emit(
        "update:images",
        previewImages.value.map((image) => ({
          file: image.file,
          order: image.order,
          alt: image.alt,
          caption: image.caption
        }))
      );
    };
    const closeEditor = () => {
      editorVisible.value = false;
      editingFile.value = null;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "multi-image-preset-upload mt-3" }, _attrs))}>`);
      _push(ssrRenderComponent(_sfc_main$6, {
        value: unref(t)("uploadNewImages")
      }, null, _parent));
      _push(`<input type="file" accept="image/png,image/jpeg,image/webp" class="block w-full text-md text-gray-700 dark:text-gray-100 file:mr-4 file:py-0.5 file:px-2 file:border-0 file:text-sm file:font-semibold file:bg-violet-600 file:text-white hover:file:bg-violet-700">`);
      if (previewImages.value.length) {
        _push(`<div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"><!--[-->`);
        ssrRenderList(previewImages.value, (image, index) => {
          _push(`<div class="relative border border-slate-500 rounded-sm py-0.5 px-2"><img${ssrRenderAttr("src", image.url)}${ssrRenderAttr("alt", unref(t)("view"))} class="h-40 w-full object-cover"><input${ssrRenderAttr("value", image.order)}${ssrRenderAttr("placeholder", unref(t)("sort"))} class="w-full my-2 py-0.5 px-2 text-sm font-semibold border border-slate-500 rounded dark:bg-cyan-800 dark:text-slate-100"><input${ssrRenderAttr("value", image.alt)}${ssrRenderAttr("placeholder", unref(t)("seoAltImage"))} class="w-full my-2 py-0.5 px-2 text-sm font-semibold border border-slate-500 rounded dark:bg-cyan-800 dark:text-slate-100"><input${ssrRenderAttr("value", image.caption)}${ssrRenderAttr("placeholder", unref(t)("seoTitleImage"))} class="w-full mb-2 py-0.5 px-2 text-sm font-semibold border border-slate-500 rounded dark:bg-cyan-800 dark:text-slate-100"><button type="button" class="absolute top-2 right-2 bg-rose-500 hover:bg-rose-700 text-white rounded-sm p-1"><svg class="w-4 h-4 shrink-0 fill-current opacity-80" viewBox="0 0 16 16"><path d="M8 0C3.6 0 0 3.6 0 8s3.6 8 8 8 8-3.6 8-8-3.6-8-8-8zm3.5 10.1l-1.4 1.4L8 9.4l-2.1 2.1-1.4-1.4L6.6 8 4.5 5.9l1.4-1.4L8 6.6l2.1-2.1 1.4 1.4L9.4 8l2.1 2.1z"></path></svg></button></div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$1, {
        show: editorVisible.value,
        file: editingFile.value,
        preset: __props.preset,
        onSave: handleEditorSave,
        onClose: closeEditor
      }, null, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Image/MultiImagePresetUpload.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _,
  _sfc_main$1 as a
};
