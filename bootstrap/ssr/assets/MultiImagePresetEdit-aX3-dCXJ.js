import { ref, watch, mergeProps, unref, withCtx, createVNode, withDirectives, vModelText, withModifiers, createBlock, openBlock, toDisplayString, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrRenderAttr, ssrInterpolate } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import draggable from "vuedraggable";
import { _ as _sfc_main$1 } from "./LabelInput-C61CQHdu.js";
import { a as _sfc_main$2 } from "./MultiImagePresetUpload-Dhpb09Vk.js";
const _sfc_main = {
  __name: "MultiImagePresetEdit",
  __ssrInlineRender: true,
  props: {
    images: {
      type: Array,
      default: () => []
    },
    preset: {
      type: Object,
      required: true
    }
  },
  emits: [
    "update:images",
    "delete-image",
    "delete:image"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localImages = ref([]);
    const fileInput = ref(null);
    const replaceIndex = ref(null);
    const editorVisible = ref(false);
    const editingFile = ref(null);
    watch(
      () => props.images,
      (images) => {
        localImages.value = images.map((image) => ({
          id: image.id,
          order: image.order || 0,
          url: image.url,
          alt: image.alt || "",
          caption: image.caption || "",
          file: image.file || null
        })).sort((a, b) => a.order - b.order);
      },
      { immediate: true }
    );
    const updateImages = () => {
      emit("update:images", localImages.value);
    };
    const updateOrder = () => {
      localImages.value.forEach((image, index) => {
        image.order = index + 1;
      });
      updateImages();
    };
    const removeImage = (index) => {
      const removedImage = localImages.value[index];
      if (removedImage == null ? void 0 : removedImage.id) {
        emit("delete-image", removedImage.id);
        emit("delete:image", removedImage.id);
      }
      localImages.value.splice(index, 1);
      updateOrder();
    };
    const selectReplaceFile = (index) => {
      var _a;
      replaceIndex.value = index;
      (_a = fileInput.value) == null ? void 0 : _a.click();
    };
    const handleEditorSave = (payload) => {
      var _a;
      if (replaceIndex.value === null) return;
      const image = localImages.value[replaceIndex.value];
      if (!image) return;
      if ((_a = image.url) == null ? void 0 : _a.startsWith("blob:")) {
        URL.revokeObjectURL(image.url);
      }
      image.file = payload.file;
      image.url = URL.createObjectURL(payload.file);
      editorVisible.value = false;
      editingFile.value = null;
      replaceIndex.value = null;
      updateImages();
    };
    const closeEditor = () => {
      editorVisible.value = false;
      editingFile.value = null;
      replaceIndex.value = null;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "multi-image-preset-edit" }, _attrs))}>`);
      _push(ssrRenderComponent(_sfc_main$1, {
        value: unref(t)("editImages")
      }, null, _parent));
      _push(`<input type="file" accept="image/png,image/jpeg,image/webp" class="hidden">`);
      if (localImages.value.length) {
        _push(`<div class="mt-2">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localImages.value,
          "onUpdate:modelValue": ($event) => localImages.value = $event,
          group: "images",
          "item-key": "id",
          onEnd: updateOrder,
          class: "grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4"
        }, {
          item: withCtx(({ element, index }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="relative border border-slate-500 rounded-sm py-0.5 px-2"${_scopeId}><img${ssrRenderAttr("src", element.url)}${ssrRenderAttr("alt", unref(t)("view"))} class="h-40 w-full object-cover"${_scopeId}><input${ssrRenderAttr("value", element.order)}${ssrRenderAttr("placeholder", unref(t)("sort"))} class="w-full my-2 py-0.5 px-2 text-sm font-semibold border border-slate-500 rounded dark:bg-cyan-800 dark:text-slate-100"${_scopeId}><input${ssrRenderAttr("value", element.alt)}${ssrRenderAttr("placeholder", unref(t)("seoAltImage"))} class="w-full my-2 py-0.5 px-2 text-sm font-semibold border border-slate-500 rounded dark:bg-cyan-800 dark:text-slate-100"${_scopeId}><input${ssrRenderAttr("value", element.caption)}${ssrRenderAttr("placeholder", unref(t)("seoTitleImage"))} class="w-full mb-2 py-0.5 px-2 text-sm font-semibold border border-slate-500 rounded dark:bg-cyan-800 dark:text-slate-100"${_scopeId}><div class="flex justify-between gap-2 mb-2"${_scopeId}><button type="button" class="w-full text-xs font-semibold px-2 py-1 rounded-sm bg-amber-300 hover:bg-amber-500 dark:bg-amber-400 dark:hover:bg-amber-600 text-slate-800 hover:text-slate-100 dark:text-slate-800 dark:hover:text-slate-100 flex items-center justify-center gap-1"${_scopeId}><svg class="w-3 h-3 fill-current" viewBox="0 0 512 512"${_scopeId}><path d="M8.309 189.836L184.313 37.851C199.719 24.546 224 35.347 224 56.015v80.053c160.629 1.839 288 34.032 288 186.258 0 61.441-39.581 122.309-83.333 154.132-13.653 9.931-33.111-2.533-28.077-18.631 45.344-145.012-21.507-183.51-176.59-185.742V360c0 20.7-24.3 31.453-39.687 18.164l-176.004-152c-11.071-9.562-11.086-26.753 0-36.328z"${_scopeId}></path></svg><span${_scopeId}>${ssrInterpolate(unref(t)("replace"))}</span></button><button type="button" class="w-full text-xs font-semibold px-2 py-1 rounded-sm bg-rose-500 hover:bg-rose-700 text-white flex items-center justify-center gap-1"${_scopeId}><svg class="w-3 h-3 fill-current" viewBox="0 0 448 512"${_scopeId}><path d="M0 84V56c0-13.3 10.7-24 24-24h112l9.4-18.7c4-8.2 12.3-13.3 21.4-13.3h114.3c9.1 0 17.4 5.1 21.5 13.3L312 32h112c13.3 0 24 10.7 24 24v28c0 6.6-5.4 12-12 12H12C5.4 96 0 90.6 0 84zm416 56v324c0 26.5-21.5 48-48 48H80c-26.5 0-48-21.5-48-48V140c0-6.6 5.4-12 12-12h360c6.6 0 12 5.4 12 12zm-272 68c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208z"${_scopeId}></path></svg><span${_scopeId}>${ssrInterpolate(unref(t)("delete"))}</span></button></div></div>`);
            } else {
              return [
                createVNode("div", { class: "relative border border-slate-500 rounded-sm py-0.5 px-2" }, [
                  createVNode("img", {
                    src: element.url,
                    alt: unref(t)("view"),
                    class: "h-40 w-full object-cover"
                  }, null, 8, ["src", "alt"]),
                  withDirectives(createVNode("input", {
                    "onUpdate:modelValue": ($event) => element.order = $event,
                    onInput: updateImages,
                    placeholder: unref(t)("sort"),
                    class: "w-full my-2 py-0.5 px-2 text-sm font-semibold border border-slate-500 rounded dark:bg-cyan-800 dark:text-slate-100"
                  }, null, 40, ["onUpdate:modelValue", "placeholder"]), [
                    [vModelText, element.order]
                  ]),
                  withDirectives(createVNode("input", {
                    "onUpdate:modelValue": ($event) => element.alt = $event,
                    onInput: updateImages,
                    placeholder: unref(t)("seoAltImage"),
                    class: "w-full my-2 py-0.5 px-2 text-sm font-semibold border border-slate-500 rounded dark:bg-cyan-800 dark:text-slate-100"
                  }, null, 40, ["onUpdate:modelValue", "placeholder"]), [
                    [vModelText, element.alt]
                  ]),
                  withDirectives(createVNode("input", {
                    "onUpdate:modelValue": ($event) => element.caption = $event,
                    onInput: updateImages,
                    placeholder: unref(t)("seoTitleImage"),
                    class: "w-full mb-2 py-0.5 px-2 text-sm font-semibold border border-slate-500 rounded dark:bg-cyan-800 dark:text-slate-100"
                  }, null, 40, ["onUpdate:modelValue", "placeholder"]), [
                    [vModelText, element.caption]
                  ]),
                  createVNode("div", { class: "flex justify-between gap-2 mb-2" }, [
                    createVNode("button", {
                      type: "button",
                      class: "w-full text-xs font-semibold px-2 py-1 rounded-sm bg-amber-300 hover:bg-amber-500 dark:bg-amber-400 dark:hover:bg-amber-600 text-slate-800 hover:text-slate-100 dark:text-slate-800 dark:hover:text-slate-100 flex items-center justify-center gap-1",
                      onClick: withModifiers(($event) => selectReplaceFile(index), ["prevent"])
                    }, [
                      (openBlock(), createBlock("svg", {
                        class: "w-3 h-3 fill-current",
                        viewBox: "0 0 512 512"
                      }, [
                        createVNode("path", { d: "M8.309 189.836L184.313 37.851C199.719 24.546 224 35.347 224 56.015v80.053c160.629 1.839 288 34.032 288 186.258 0 61.441-39.581 122.309-83.333 154.132-13.653 9.931-33.111-2.533-28.077-18.631 45.344-145.012-21.507-183.51-176.59-185.742V360c0 20.7-24.3 31.453-39.687 18.164l-176.004-152c-11.071-9.562-11.086-26.753 0-36.328z" })
                      ])),
                      createVNode("span", null, toDisplayString(unref(t)("replace")), 1)
                    ], 8, ["onClick"]),
                    createVNode("button", {
                      type: "button",
                      class: "w-full text-xs font-semibold px-2 py-1 rounded-sm bg-rose-500 hover:bg-rose-700 text-white flex items-center justify-center gap-1",
                      onClick: withModifiers(($event) => removeImage(index), ["prevent"])
                    }, [
                      (openBlock(), createBlock("svg", {
                        class: "w-3 h-3 fill-current",
                        viewBox: "0 0 448 512"
                      }, [
                        createVNode("path", { d: "M0 84V56c0-13.3 10.7-24 24-24h112l9.4-18.7c4-8.2 12.3-13.3 21.4-13.3h114.3c9.1 0 17.4 5.1 21.5 13.3L312 32h112c13.3 0 24 10.7 24 24v28c0 6.6-5.4 12-12 12H12C5.4 96 0 90.6 0 84zm416 56v324c0 26.5-21.5 48-48 48H80c-26.5 0-48-21.5-48-48V140c0-6.6 5.4-12 12-12h360c6.6 0 12 5.4 12 12zm-272 68c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208zm96 0c0-8.8-7.2-16-16-16s-16 7.2-16 16v224c0 8.8 7.2 16 16 16s16-7.2 16-16V208z" })
                      ])),
                      createVNode("span", null, toDisplayString(unref(t)("delete")), 1)
                    ], 8, ["onClick"])
                  ])
                ])
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div>`);
      } else {
        _push(`<div><p class="text-sm text-left text-slate-800 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</p></div>`);
      }
      _push(ssrRenderComponent(_sfc_main$2, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Image/MultiImagePresetEdit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
