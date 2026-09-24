import { ref, computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, createCommentVNode, withDirectives, vModelSelect, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { t as transliterate } from "./transliteration-4EFNajdD.js";
import { useForm } from "@inertiajs/vue3";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$e } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$c } from "./MetatagsButton-CvAlOk1D.js";
import { _ as _sfc_main$3 } from "./LabelCheckbox-NXEIsthM.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$a } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$5 } from "./InputNumber-BpLRbsGi.js";
import { _ as _sfc_main$4 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$7 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$6 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$b } from "./TinyEditor-D1VhnqFH.js";
import { _ as _sfc_main$9 } from "./TranslationTabs-czH7YSpu.js";
import { _ as _sfc_main$d } from "./MultiImageUpload-Bg2ahSyk.js";
import { _ as _sfc_main$8 } from "./VideoSourceFields-CKQNBs-M.js";
/* empty css                                                                      */
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
  __name: "Create",
  __ssrInlineRender: true,
  props: {
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    relatedVideos: { type: Array, default: () => [] },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const makeTranslation = () => ({
      title: "",
      short: "",
      description: "",
      pseudonym: "",
      meta_title: "",
      meta_keywords: "",
      meta_desc: ""
    });
    const defaultLocale = props.currentLocale || "ru";
    const activeLocale = ref(defaultLocale);
    const form = useForm({
      sort: 0,
      activity: false,
      is_private: false,
      left: false,
      main: false,
      right: false,
      url: "",
      published_at: "",
      show_from_at: "",
      show_to_at: "",
      duration: "",
      source_type: "local",
      embed_code: "",
      external_video_id: "",
      video_file: null,
      related_videos: [],
      images: [],
      translations: {
        [defaultLocale]: makeTranslation()
      }
    });
    const currentTranslation = computed(() => {
      if (!form.translations[activeLocale.value]) {
        form.translations[activeLocale.value] = makeTranslation();
      }
      return form.translations[activeLocale.value];
    });
    const getError = (key) => {
      return form.errors[`translations.${activeLocale.value}.${key}`];
    };
    const relatedVideoLabel = (video) => {
      var _a;
      return (video == null ? void 0 : video.title) || ((_a = video == null ? void 0 : video.translation) == null ? void 0 : _a.title) || `ID: ${video == null ? void 0 : video.id}`;
    };
    const handleUrlInputFocus = () => {
      if (!form.url && currentTranslation.value.title) {
        form.url = transliterate(currentTranslation.value.title.toLowerCase());
      }
    };
    const truncateText = (text, maxLength, addEllipsis = false) => {
      if (!text) return "";
      const str = String(text);
      if (str.length <= maxLength) return str;
      const lastSpaceIndex = str.lastIndexOf(" ", maxLength);
      const truncated = lastSpaceIndex === -1 ? str.substring(0, maxLength) : str.substring(0, lastSpaceIndex);
      return addEllipsis ? `${truncated}...` : truncated;
    };
    const generateMetaFields = () => {
      const translation = currentTranslation.value;
      if (translation.title && !translation.meta_title) {
        translation.meta_title = truncateText(translation.title, 255);
      }
      if (!translation.meta_keywords && translation.short) {
        let text = String(translation.short).replace(/(<([^>]+)>)/gi, "");
        text = text.replace(/[.,!?;:()[\]{}"'«»]/g, "");
        const words = text.split(/\s+/).filter((word) => word && word.length >= 3).map((word) => word.toLowerCase()).filter((value, index, self) => self.indexOf(value) === index);
        translation.meta_keywords = truncateText(words.join(", "), 255);
      }
      if (translation.short && !translation.meta_desc) {
        const descText = String(translation.short).replace(/(<([^>]+)>)/gi, "");
        translation.meta_desc = truncateText(descText, 200, true);
      }
    };
    const submitForm = () => {
      form.transform((data) => ({
        ...data,
        activity: data.activity ? 1 : 0,
        is_private: data.is_private ? 1 : 0,
        left: data.left ? 1 : 0,
        main: data.main ? 1 : 0,
        right: data.right ? 1 : 0,
        related_videos: (data.related_videos || []).map((video, index) => ({
          id: video.id,
          sort: index
        })),
        images: (data.images || []).map((image) => {
          if (image.file) {
            return {
              file: image.file,
              order: image.order,
              alt: image.alt,
              caption: image.caption
            };
          }
          if (image.id) {
            return {
              id: Number(image.id),
              order: image.order,
              alt: image.alt,
              caption: image.caption
            };
          }
          return null;
        }).filter(Boolean)
      }));
      form.post(route("admin.blogVideos.store"), {
        errorBag: "createBlogVideo",
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => toast.success("Видео успешно создано!"),
        onError: (errors) => {
          console.error("Не удалось отправить форму:", errors);
          const firstError = errors == null ? void 0 : errors[Object.keys(errors)[0]];
          toast.error(firstError || "Пожалуйста, проверьте правильность заполнения полей.");
        }
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("addVideo")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("addVideo"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("addVideo")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("addVideo")), 1)
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
              href: _ctx.route("admin.blogVideos.index")
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
            _push2(`</div><form enctype="multipart/form-data" class="p-3 w-full"${_scopeId}><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).activity,
              "onUpdate:modelValue": ($event) => unref(form).activity = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "activity",
              text: unref(t)("activity"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).is_private,
              "onUpdate:modelValue": ($event) => unref(form).is_private = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "is_private",
              text: unref(t)("isPrivate"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
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
              class: "w-full lg:w-28"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2 lg:mt-0",
              message: unref(form).errors.sort
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex justify-between flex-col lg:flex-row items-center gap-4"${_scopeId}><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).left,
              "onUpdate:modelValue": ($event) => unref(form).left = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "left",
              text: unref(t)("left"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).main,
              "onUpdate:modelValue": ($event) => unref(form).main = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "main",
              text: unref(t)("main"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-row items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).right,
              "onUpdate:modelValue": ($event) => unref(form).right = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "right",
              text: unref(t)("right"),
              class: "text-sm h-8 flex items-center"
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "url" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("url"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("url")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "url",
              type: "text",
              modelValue: unref(form).url,
              "onUpdate:modelValue": ($event) => unref(form).url = $event,
              required: "",
              autocomplete: "url",
              onFocus: handleUrlInputFocus
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.url
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col lg:flex-row sm:justify-between sm:space-x-4"${_scopeId}><div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "published_at",
              value: unref(t)("publishedAt"),
              class: "mb-1 lg:mb-0 lg:mr-2"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "published_at",
              type: "date",
              modelValue: unref(form).published_at,
              "onUpdate:modelValue": ($event) => unref(form).published_at = $event,
              autocomplete: "published_at",
              class: "w-full max-w-56"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-1 sm:mt-0",
              message: unref(form).errors.published_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "duration",
              value: unref(t)("duration"),
              class: "mb-1 lg:mb-0 lg:mr-2"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "duration",
              type: "number",
              modelValue: unref(form).duration,
              "onUpdate:modelValue": ($event) => unref(form).duration = $event,
              autocomplete: "duration",
              class: "w-full max-w-24"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-1 lg:mt-0",
              message: unref(form).errors.duration
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col lg:flex-row sm:justify-between sm:space-x-4"${_scopeId}><div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "show_from_at",
              value: unref(t)("showFromAt"),
              class: "mb-1 lg:mb-0 lg:mr-2"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "show_from_at",
              type: "datetime-local",
              modelValue: unref(form).show_from_at,
              "onUpdate:modelValue": ($event) => unref(form).show_from_at = $event,
              autocomplete: "show_from_at",
              class: "w-full max-w-56"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-1 sm:mt-0",
              message: unref(form).errors.show_from_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "show_to_at",
              value: unref(t)("showToAt"),
              class: "mb-1 lg:mb-0 lg:mr-2"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "show_to_at",
              type: "datetime-local",
              modelValue: unref(form).show_to_at,
              "onUpdate:modelValue": ($event) => unref(form).show_to_at = $event,
              autocomplete: "show_to_at",
              class: "w-full max-w-56"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-1 sm:mt-0",
              message: unref(form).errors.show_to_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-3 flex flex-col lg:flex-row lg:justify-between lg:space-x-4"${_scopeId}><div class="flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "source_type",
              value: unref(t)("sourceType"),
              class: "mb-1 lg:mb-0 lg:mr-2"
            }, null, _parent2, _scopeId));
            _push2(`<select id="source_type" class="form-select px-2 py-0.5 min-w-[12rem] font-semibold text-sm rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100 border-slate-500 focus:border-indigo-500 focus:ring-indigo-300"${_scopeId}><option value="local"${ssrIncludeBooleanAttr(Array.isArray(unref(form).source_type) ? ssrLooseContain(unref(form).source_type, "local") : ssrLooseEqual(unref(form).source_type, "local")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("local"))}</option><option value="youtube"${ssrIncludeBooleanAttr(Array.isArray(unref(form).source_type) ? ssrLooseContain(unref(form).source_type, "youtube") : ssrLooseEqual(unref(form).source_type, "youtube")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("youtube"))}</option><option value="vimeo"${ssrIncludeBooleanAttr(Array.isArray(unref(form).source_type) ? ssrLooseContain(unref(form).source_type, "vimeo") : ssrLooseEqual(unref(form).source_type, "vimeo")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("vimeo"))}</option><option value="code"${ssrIncludeBooleanAttr(Array.isArray(unref(form).source_type) ? ssrLooseContain(unref(form).source_type, "code") : ssrLooseEqual(unref(form).source_type, "code")) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)("code"))}</option></select>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-1 lg:mt-0",
              message: unref(form).errors.source_type
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              modelValue: unref(form).source_type,
              "onUpdate:modelValue": ($event) => unref(form).source_type = $event,
              "video-url": unref(form).video_url,
              "onUpdate:videoUrl": ($event) => unref(form).video_url = $event,
              "external-video-id": unref(form).external_video_id,
              "onUpdate:externalVideoId": ($event) => unref(form).external_video_id = $event,
              "video-file": unref(form).video_file,
              "onUpdate:videoFile": ($event) => unref(form).video_file = $event,
              "embed-code": unref(form).embed_code,
              "onUpdate:embedCode": ($event) => unref(form).embed_code = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.video_file
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.embed_code
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.external_video_id
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (unref(form).source_type === "code" && unref(form).embed_code) {
              _push2(`<div class="mt-4 mb-4"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                value: unref(t)("view"),
                class: "mb-1"
              }, null, _parent2, _scopeId));
              _push2(`<div class="border rounded p-4 bg-white dark:bg-slate-800"${_scopeId}>${unref(form).embed_code ?? ""}</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "related_videos",
              value: unref(t)("relatedVideos"),
              class: "mb-1"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              modelValue: unref(form).related_videos,
              "onUpdate:modelValue": ($event) => unref(form).related_videos = $event,
              options: __props.relatedVideos,
              multiple: true,
              "close-on-select": true,
              placeholder: unref(t)("select"),
              "custom-label": relatedVideoLabel,
              "track-by": "id"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: unref(form).errors.related_videos
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              modelValue: activeLocale.value,
              "onUpdate:modelValue": ($event) => activeLocale.value = $event,
              translations: unref(form).translations,
              "available-locales": __props.availableLocales,
              "make-translation": makeTranslation,
              "onUpdate:translations": ($event) => unref(form).translations = $event,
              onRemoved: ($event) => unref(toast).warning("Перевод удалён."),
              onAdded: ($event) => unref(toast).success("Локаль добавлена.")
            }, null, _parent2, _scopeId));
            _push2(`<div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "title" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}>*</span> ${ssrInterpolate(unref(t)("name"))} [${ssrInterpolate(activeLocale.value.toUpperCase())}] `);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                    createTextVNode(" " + toDisplayString(unref(t)("name")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "title",
              type: "text",
              modelValue: currentTranslation.value.title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
              maxlength: "255",
              required: "",
              autocomplete: "title"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "short",
              value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.short || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$a, {
              modelValue: currentTranslation.value.short,
              "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("short")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "description",
              value: `${unref(t)("description")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$b, {
              modelValue: currentTranslation.value.description,
              "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
              height: 500
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("description")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col lg:flex-row items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "pseudonym",
              value: `${unref(t)("nickname")} / ${unref(t)("author")} [${activeLocale.value.toUpperCase()}]`,
              class: "w-40 mb-1 lg:mb-0 lg:mr-2"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "pseudonym",
              type: "text",
              modelValue: currentTranslation.value.pseudonym,
              "onUpdate:modelValue": ($event) => currentTranslation.value.pseudonym = $event,
              autocomplete: "author",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-1 sm:mt-0",
              message: getError("pseudonym")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "meta_title",
              value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-xs text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_title || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "meta_title",
              type: "text",
              modelValue: currentTranslation.value.meta_title,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
              maxlength: "255",
              autocomplete: "meta_title"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("meta_title")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "meta_keywords",
              value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-xs text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_keywords || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "meta_keywords",
              type: "text",
              modelValue: currentTranslation.value.meta_keywords,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
              maxlength: "255",
              autocomplete: "meta_keywords"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("meta_keywords")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-3 flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, {
              for: "meta_desc",
              value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
            }, null, _parent2, _scopeId));
            _push2(`<div class="text-xs text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate((currentTranslation.value.meta_desc || "").length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$a, {
              modelValue: currentTranslation.value.meta_desc,
              "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: "mt-2",
              message: getError("meta_desc")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex justify-end mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$c, { onClick: generateMetaFields }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("generateMetaTags"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("generateMetaTags")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$d, {
              "onUpdate:images": ($event) => unref(form).images = $event
            }, null, _parent2, _scopeId));
            _push2(`<div class="flex items-center justify-center mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.blogVideos.index")
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
            _push2(ssrRenderComponent(_sfc_main$e, {
              class: ["ms-4 mb-0", { "opacity-25": unref(form).processing }],
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
                      href: _ctx.route("admin.blogVideos.index")
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
                    enctype: "multipart/form-data",
                    class: "p-3 w-full"
                  }, [
                    createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          modelValue: unref(form).activity,
                          "onUpdate:modelValue": ($event) => unref(form).activity = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "activity",
                          text: unref(t)("activity"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          modelValue: unref(form).is_private,
                          "onUpdate:modelValue": ($event) => unref(form).is_private = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "is_private",
                          text: unref(t)("isPrivate"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
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
                          class: "w-full lg:w-28"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2 lg:mt-0",
                          message: unref(form).errors.sort
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 flex justify-between flex-col lg:flex-row items-center gap-4" }, [
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          modelValue: unref(form).left,
                          "onUpdate:modelValue": ($event) => unref(form).left = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "left",
                          text: unref(t)("left"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          modelValue: unref(form).main,
                          "onUpdate:modelValue": ($event) => unref(form).main = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "main",
                          text: unref(t)("main"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ]),
                      createVNode("div", { class: "flex flex-row items-center gap-2" }, [
                        createVNode(_sfc_main$2, {
                          modelValue: unref(form).right,
                          "onUpdate:modelValue": ($event) => unref(form).right = $event
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          for: "right",
                          text: unref(t)("right"),
                          class: "text-sm h-8 flex items-center"
                        }, null, 8, ["text"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                      createVNode(_sfc_main$4, { for: "url" }, {
                        default: withCtx(() => [
                          createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                          createTextVNode(" " + toDisplayString(unref(t)("url")), 1)
                        ]),
                        _: 1
                      }),
                      createVNode(_sfc_main$7, {
                        id: "url",
                        type: "text",
                        modelValue: unref(form).url,
                        "onUpdate:modelValue": ($event) => unref(form).url = $event,
                        required: "",
                        autocomplete: "url",
                        onFocus: handleUrlInputFocus
                      }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                      createVNode(_sfc_main$6, {
                        class: "mt-2",
                        message: unref(form).errors.url
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col lg:flex-row sm:justify-between sm:space-x-4" }, [
                      createVNode("div", { class: "flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1" }, [
                        createVNode(_sfc_main$4, {
                          for: "published_at",
                          value: unref(t)("publishedAt"),
                          class: "mb-1 lg:mb-0 lg:mr-2"
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$7, {
                          id: "published_at",
                          type: "date",
                          modelValue: unref(form).published_at,
                          "onUpdate:modelValue": ($event) => unref(form).published_at = $event,
                          autocomplete: "published_at",
                          class: "w-full max-w-56"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-1 sm:mt-0",
                          message: unref(form).errors.published_at
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1" }, [
                        createVNode(_sfc_main$4, {
                          for: "duration",
                          value: unref(t)("duration"),
                          class: "mb-1 lg:mb-0 lg:mr-2"
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$5, {
                          id: "duration",
                          type: "number",
                          modelValue: unref(form).duration,
                          "onUpdate:modelValue": ($event) => unref(form).duration = $event,
                          autocomplete: "duration",
                          class: "w-full max-w-24"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-1 lg:mt-0",
                          message: unref(form).errors.duration
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col lg:flex-row sm:justify-between sm:space-x-4" }, [
                      createVNode("div", { class: "flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1" }, [
                        createVNode(_sfc_main$4, {
                          for: "show_from_at",
                          value: unref(t)("showFromAt"),
                          class: "mb-1 lg:mb-0 lg:mr-2"
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$7, {
                          id: "show_from_at",
                          type: "datetime-local",
                          modelValue: unref(form).show_from_at,
                          "onUpdate:modelValue": ($event) => unref(form).show_from_at = $event,
                          autocomplete: "show_from_at",
                          class: "w-full max-w-56"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-1 sm:mt-0",
                          message: unref(form).errors.show_from_at
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1" }, [
                        createVNode(_sfc_main$4, {
                          for: "show_to_at",
                          value: unref(t)("showToAt"),
                          class: "mb-1 lg:mb-0 lg:mr-2"
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$7, {
                          id: "show_to_at",
                          type: "datetime-local",
                          modelValue: unref(form).show_to_at,
                          "onUpdate:modelValue": ($event) => unref(form).show_to_at = $event,
                          autocomplete: "show_to_at",
                          class: "w-full max-w-56"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-1 sm:mt-0",
                          message: unref(form).errors.show_to_at
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-3 flex flex-col lg:flex-row lg:justify-between lg:space-x-4" }, [
                      createVNode("div", { class: "flex flex-col lg:flex-row items-center mb-2 lg:mb-0 flex-1" }, [
                        createVNode(_sfc_main$4, {
                          for: "source_type",
                          value: unref(t)("sourceType"),
                          class: "mb-1 lg:mb-0 lg:mr-2"
                        }, null, 8, ["value"]),
                        withDirectives(createVNode("select", {
                          id: "source_type",
                          "onUpdate:modelValue": ($event) => unref(form).source_type = $event,
                          class: "form-select px-2 py-0.5 min-w-[12rem] font-semibold text-sm rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100 border-slate-500 focus:border-indigo-500 focus:ring-indigo-300"
                        }, [
                          createVNode("option", { value: "local" }, toDisplayString(unref(t)("local")), 1),
                          createVNode("option", { value: "youtube" }, toDisplayString(unref(t)("youtube")), 1),
                          createVNode("option", { value: "vimeo" }, toDisplayString(unref(t)("vimeo")), 1),
                          createVNode("option", { value: "code" }, toDisplayString(unref(t)("code")), 1)
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).source_type]
                        ]),
                        createVNode(_sfc_main$6, {
                          class: "mt-1 lg:mt-0",
                          message: unref(form).errors.source_type
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "flex flex-col items-start" }, [
                      createVNode(_sfc_main$8, {
                        modelValue: unref(form).source_type,
                        "onUpdate:modelValue": ($event) => unref(form).source_type = $event,
                        "video-url": unref(form).video_url,
                        "onUpdate:videoUrl": ($event) => unref(form).video_url = $event,
                        "external-video-id": unref(form).external_video_id,
                        "onUpdate:externalVideoId": ($event) => unref(form).external_video_id = $event,
                        "video-file": unref(form).video_file,
                        "onUpdate:videoFile": ($event) => unref(form).video_file = $event,
                        "embed-code": unref(form).embed_code,
                        "onUpdate:embedCode": ($event) => unref(form).embed_code = $event
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "video-url", "onUpdate:videoUrl", "external-video-id", "onUpdate:externalVideoId", "video-file", "onUpdate:videoFile", "embed-code", "onUpdate:embedCode"]),
                      createVNode(_sfc_main$6, {
                        class: "mt-2",
                        message: unref(form).errors.video_file
                      }, null, 8, ["message"]),
                      createVNode(_sfc_main$6, {
                        class: "mt-2",
                        message: unref(form).errors.embed_code
                      }, null, 8, ["message"]),
                      createVNode(_sfc_main$6, {
                        class: "mt-2",
                        message: unref(form).errors.external_video_id
                      }, null, 8, ["message"])
                    ]),
                    unref(form).source_type === "code" && unref(form).embed_code ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "mt-4 mb-4"
                    }, [
                      createVNode(_sfc_main$4, {
                        value: unref(t)("view"),
                        class: "mb-1"
                      }, null, 8, ["value"]),
                      createVNode("div", {
                        innerHTML: unref(form).embed_code,
                        class: "border rounded p-4 bg-white dark:bg-slate-800"
                      }, null, 8, ["innerHTML"])
                    ])) : createCommentVNode("", true),
                    createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                      createVNode(_sfc_main$4, {
                        for: "related_videos",
                        value: unref(t)("relatedVideos"),
                        class: "mb-1"
                      }, null, 8, ["value"]),
                      createVNode(unref(VueMultiselect), {
                        modelValue: unref(form).related_videos,
                        "onUpdate:modelValue": ($event) => unref(form).related_videos = $event,
                        options: __props.relatedVideos,
                        multiple: true,
                        "close-on-select": true,
                        placeholder: unref(t)("select"),
                        "custom-label": relatedVideoLabel,
                        "track-by": "id"
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "placeholder"]),
                      createVNode(_sfc_main$6, {
                        class: "mt-2",
                        message: unref(form).errors.related_videos
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "my-5 p-3 border border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-800 rounded-sm" }, [
                      createVNode(_sfc_main$9, {
                        modelValue: activeLocale.value,
                        "onUpdate:modelValue": ($event) => activeLocale.value = $event,
                        translations: unref(form).translations,
                        "available-locales": __props.availableLocales,
                        "make-translation": makeTranslation,
                        "onUpdate:translations": ($event) => unref(form).translations = $event,
                        onRemoved: ($event) => unref(toast).warning("Перевод удалён."),
                        onAdded: ($event) => unref(toast).success("Локаль добавлена.")
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "translations", "available-locales", "onUpdate:translations", "onRemoved", "onAdded"]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, { for: "title" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, "*"),
                            createTextVNode(" " + toDisplayString(unref(t)("name")) + " [" + toDisplayString(activeLocale.value.toUpperCase()) + "] ", 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$7, {
                          id: "title",
                          type: "text",
                          modelValue: currentTranslation.value.title,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.title = $event,
                          maxlength: "255",
                          required: "",
                          autocomplete: "title"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("title")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode("div", { class: "flex justify-between w-full" }, [
                          createVNode(_sfc_main$4, {
                            for: "short",
                            value: `${unref(t)("shortDescription")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.short || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                        ]),
                        createVNode(_sfc_main$a, {
                          modelValue: currentTranslation.value.short,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.short = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("short")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode(_sfc_main$4, {
                          for: "description",
                          value: `${unref(t)("description")} [${activeLocale.value.toUpperCase()}]`
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$b, {
                          modelValue: currentTranslation.value.description,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.description = $event,
                          height: 500
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("description")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col lg:flex-row items-center" }, [
                        createVNode(_sfc_main$4, {
                          for: "pseudonym",
                          value: `${unref(t)("nickname")} / ${unref(t)("author")} [${activeLocale.value.toUpperCase()}]`,
                          class: "w-40 mb-1 lg:mb-0 lg:mr-2"
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$7, {
                          id: "pseudonym",
                          type: "text",
                          modelValue: currentTranslation.value.pseudonym,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.pseudonym = $event,
                          autocomplete: "author",
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-1 sm:mt-0",
                          message: getError("pseudonym")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode("div", { class: "flex justify-between w-full" }, [
                          createVNode(_sfc_main$4, {
                            for: "meta_title",
                            value: `${unref(t)("metaTitle")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode("div", { class: "text-xs text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_title || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                        ]),
                        createVNode(_sfc_main$7, {
                          id: "meta_title",
                          type: "text",
                          modelValue: currentTranslation.value.meta_title,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.meta_title = $event,
                          maxlength: "255",
                          autocomplete: "meta_title"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("meta_title")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode("div", { class: "flex justify-between w-full" }, [
                          createVNode(_sfc_main$4, {
                            for: "meta_keywords",
                            value: `${unref(t)("metaKeywords")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode("div", { class: "text-xs text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_keywords || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                        ]),
                        createVNode(_sfc_main$7, {
                          id: "meta_keywords",
                          type: "text",
                          modelValue: currentTranslation.value.meta_keywords,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.meta_keywords = $event,
                          maxlength: "255",
                          autocomplete: "meta_keywords"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("meta_keywords")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mb-3 flex flex-col items-start" }, [
                        createVNode("div", { class: "flex justify-between w-full" }, [
                          createVNode(_sfc_main$4, {
                            for: "meta_desc",
                            value: `${unref(t)("metaDescription")} [${activeLocale.value.toUpperCase()}]`
                          }, null, 8, ["value"]),
                          createVNode("div", { class: "text-xs text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString((currentTranslation.value.meta_desc || "").length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                        ]),
                        createVNode(_sfc_main$a, {
                          modelValue: currentTranslation.value.meta_desc,
                          "onUpdate:modelValue": ($event) => currentTranslation.value.meta_desc = $event,
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$6, {
                          class: "mt-2",
                          message: getError("meta_desc")
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex justify-end mt-4" }, [
                        createVNode(_sfc_main$c, {
                          onClick: withModifiers(generateMetaFields, ["prevent"])
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("generateMetaTags")), 1)
                          ]),
                          _: 1
                        })
                      ])
                    ]),
                    createVNode(_sfc_main$d, {
                      "onUpdate:images": ($event) => unref(form).images = $event
                    }, null, 8, ["onUpdate:images"]),
                    createVNode("div", { class: "flex items-center justify-center mt-4" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.blogVideos.index")
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
                      createVNode(_sfc_main$e, {
                        class: ["ms-4 mb-0", { "opacity-25": unref(form).processing }],
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Blog/BlogVideos/Create.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
