import { mergeProps, unref, useSSRContext, ref, watch, computed, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, Fragment, renderList, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent, ssrRenderList } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { router } from "@inertiajs/vue3";
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
import draggable from "vuedraggable";
import { _ as _sfc_main$6 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$7 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$5 } from "./ActivityToggle-B1-nFMYK.js";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolBundle/Select/BulkActionSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: String
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>────────────────</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} 0→9</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} 9→0</option><option disabled>────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option value="slugAsc">Slug A→Z</option><option value="slugDesc">Slug Z→A</option><option disabled>────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>────────────────</option><option value="viewsDesc">${ssrInterpolate(unref(t)("views"))} 9→0</option><option value="viewsAsc">${ssrInterpolate(unref(t)("views"))} 0→9</option><option value="likesDesc">${ssrInterpolate(unref(t)("likes"))} 9→0</option><option value="likesAsc">${ssrInterpolate(unref(t)("likes"))} 0→9</option><option disabled>────────────────</option><option value="coursesDesc">${ssrInterpolate(unref(t)("courses"))} 9→0</option><option value="coursesAsc">${ssrInterpolate(unref(t)("courses"))} 0→9</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option value="pricesDesc">${ssrInterpolate(unref(t)("prices"))} 9→0</option><option value="pricesAsc">${ssrInterpolate(unref(t)("prices"))} 0→9</option><option value="orderItemsDesc">${ssrInterpolate(unref(t)("orders"))} 9→0</option><option value="orderItemsAsc">${ssrInterpolate(unref(t)("orders"))} 0→9</option><option disabled>────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolBundle/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "BundleTable",
  __ssrInlineRender: true,
  props: {
    bundles: {
      type: Array,
      default: () => []
    },
    selectedBundles: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localBundles = ref([]);
    watch(
      () => props.bundles,
      (newValue) => {
        localBundles.value = JSON.parse(JSON.stringify(newValue || []));
      },
      { immediate: true, deep: true }
    );
    const selectedCount = computed(() => props.selectedBundles.length);
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localBundles.value.map((bundle) => bundle.id)
      );
    };
    const getBundleTitle = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.translation) == null ? void 0 : _a.title) || `ID: ${(bundle == null ? void 0 : bundle.id) ?? "—"}`;
    };
    const getBundleSubtitle = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getCourseTitle = (course) => {
      var _a;
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.title) || (course == null ? void 0 : course.slug) || "";
    };
    const getCourses = (bundle) => {
      return Array.isArray(bundle == null ? void 0 : bundle.courses) ? bundle.courses : [];
    };
    const getCoursesCount = (bundle) => {
      if (typeof (bundle == null ? void 0 : bundle.courses_count) === "number") {
        return bundle.courses_count;
      }
      return getCourses(bundle).length;
    };
    const getCourseTitles = (bundle) => {
      return getCourses(bundle).map(getCourseTitle).filter(Boolean);
    };
    const getCourseTitlesTooltip = (bundle) => {
      return getCourseTitles(bundle).join("\n");
    };
    const getPrimaryImage = (bundle) => {
      if (bundle == null ? void 0 : bundle.primary_image) {
        return bundle.primary_image;
      }
      if (Array.isArray(bundle == null ? void 0 : bundle.images) && bundle.images.length) {
        return bundle.images[0];
      }
      return null;
    };
    const imageUrl = (bundle) => {
      const image = getPrimaryImage(bundle);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/school/school_bundle_images/default-image.png";
    };
    const imageAlt = (bundle) => {
      const image = getPrimaryImage(bundle);
      return (image == null ? void 0 : image.alt) || getBundleTitle(bundle) || t("defaultImageAlt");
    };
    const imageTitle = (bundle) => {
      const image = getPrimaryImage(bundle);
      return (image == null ? void 0 : image.caption) || getBundleTitle(bundle) || t("defaultImageTitle");
    };
    const formatDate = (value) => {
      if (!value) return "—";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return "—";
      }
      return date.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "long",
        day: "numeric"
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-200 dark:border-slate-600"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(selectedCount.value)}</div>`);
      if (localBundles.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(localBundles.value.length && localBundles.value.every((bundle) => __props.selectedBundles.includes(bundle.id))) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (localBundles.value.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><svg class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-2 py-3 w-px"><div class="font-medium text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("bundle"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("courses"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("views"))}><svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16"><path class="fill-current text-blue-600 dark:text-blue-300" d="M8 2C3.246 2 .251 7.29.127 7.515a.998.998 0 0 0 .002.975c.07.125 1.044 1.801 2.695 3.274C4.738 13.582 6.283 14 8 14c4.706 0 7.743-5.284 7.872-5.507a1 1 0 0 0 0-.98A13.292 13.292 0 0 0 8 2zm0 10a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-6a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("likes"))}><svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24"><path class="fill-current text-red-400 dark:text-red-300" d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path><path class="fill-current text-red-400 dark:text-red-300" d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"></path></svg></div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-center">${ssrInterpolate(unref(t)("prices"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 py-3 whitespace-nowrap"><div class="text-center"><input type="checkbox"${ssrIncludeBooleanAttr(localBundles.value.length && localBundles.value.every((bundle) => __props.selectedBundles.includes(bundle.id))) ? " checked" : ""}></div></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "tbody",
          modelValue: localBundles.value,
          "onUpdate:modelValue": ($event) => localBundles.value = $event,
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: bundle }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="w-4 h-4 text-gray-500 dark:text-gray-300" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-slate-800 dark:text-blue-200"${ssrRenderAttr("title", `[${bundle.sort}] ${formatDate(bundle.published_at)}`)}${_scopeId}>${ssrInterpolate(bundle.id)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", imageUrl(bundle))}${ssrRenderAttr("alt", imageAlt(bundle))}${ssrRenderAttr("title", imageTitle(bundle))} class="h-6 w-8 object-cover rounded-xs"${_scopeId}></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><a${ssrRenderAttr("href", `/school-bundles/${encodeURIComponent(bundle.slug)}`)} class="text-xs text-sky-600 dark:text-sky-200 hover:underline" target="_blank" rel="noopener noreferrer"${ssrRenderAttr("title", getBundleSubtitle(bundle) || getBundleTitle(bundle))}${_scopeId}>${ssrInterpolate(getBundleTitle(bundle))}</a>`);
              if (getBundleSubtitle(bundle)) {
                _push2(`<div class="text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1"${ssrRenderAttr("title", getBundleSubtitle(bundle))}${_scopeId}>${ssrInterpolate(getBundleSubtitle(bundle))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</td><td class="px-2 py-3"${_scopeId}><div class="flex flex-col items-center gap-1"${_scopeId}><div class="text-center text-xs text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(getCoursesCount(bundle))}</div>`);
              if (getCourseTitles(bundle).length) {
                _push2(`<div class="text-[11px] leading-snug text-left text-teal-700 dark:text-teal-300"${ssrRenderAttr("title", getCourseTitlesTooltip(bundle))}${_scopeId}><ul class="space-y-0.5"${_scopeId}><!--[-->`);
                ssrRenderList(getCourseTitles(bundle), (title, index) => {
                  _push2(`<li class="relative pl-2"${_scopeId}><span class="absolute left-0 text-teal-500"${_scopeId}>•</span> ${ssrInterpolate(title)}</li>`);
                });
                _push2(`<!--]--></ul></div>`);
              } else {
                _push2(`<div class="text-[11px] leading-4 text-slate-400 text-center"${ssrRenderAttr("title", unref(t)("noData"))}${_scopeId}> — </div>`);
              }
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-blue-700 dark:text-blue-300"${_scopeId}>${ssrInterpolate(bundle.views ?? 0)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-red-700 dark:text-red-300"${_scopeId}>${ssrInterpolate(bundle.likes ?? 0)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center text-xs text-emerald-700 dark:text-emerald-300"${_scopeId}>${ssrInterpolate(bundle.prices_count ?? 0)}</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-end space-x-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: bundle.activity,
                title: bundle.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", bundle)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolBundles.edit", {
                  schoolBundle: bundle.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                title: unref(t)("delete"),
                onDelete: ($event) => emit("delete", bundle)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-center"${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedBundles.includes(bundle.id)) ? " checked" : ""}${_scopeId}></div></td></tr>`);
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
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", {
                      class: "text-center text-xs text-slate-800 dark:text-blue-200",
                      title: `[${bundle.sort}] ${formatDate(bundle.published_at)}`
                    }, toDisplayString(bundle.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: imageUrl(bundle),
                        alt: imageAlt(bundle),
                        title: imageTitle(bundle),
                        class: "h-6 w-8 object-cover rounded-xs"
                      }, null, 8, ["src", "alt", "title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("a", {
                      href: `/school-bundles/${encodeURIComponent(bundle.slug)}`,
                      class: "text-xs text-sky-600 dark:text-sky-200 hover:underline",
                      target: "_blank",
                      rel: "noopener noreferrer",
                      title: getBundleSubtitle(bundle) || getBundleTitle(bundle)
                    }, toDisplayString(getBundleTitle(bundle)), 9, ["href", "title"]),
                    getBundleSubtitle(bundle) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-[11px] text-slate-500 dark:text-slate-300 line-clamp-1",
                      title: getBundleSubtitle(bundle)
                    }, toDisplayString(getBundleSubtitle(bundle)), 9, ["title"])) : createCommentVNode("", true)
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("div", { class: "flex flex-col items-center gap-1" }, [
                      createVNode("div", { class: "text-center text-xs text-slate-700 dark:text-slate-300" }, toDisplayString(getCoursesCount(bundle)), 1),
                      getCourseTitles(bundle).length ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[11px] leading-snug text-left text-teal-700 dark:text-teal-300",
                        title: getCourseTitlesTooltip(bundle)
                      }, [
                        createVNode("ul", { class: "space-y-0.5" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(getCourseTitles(bundle), (title, index) => {
                            return openBlock(), createBlock("li", {
                              key: index,
                              class: "relative pl-2"
                            }, [
                              createVNode("span", { class: "absolute left-0 text-teal-500" }, "•"),
                              createTextVNode(" " + toDisplayString(title), 1)
                            ]);
                          }), 128))
                        ])
                      ], 8, ["title"])) : (openBlock(), createBlock("div", {
                        key: 1,
                        class: "text-[11px] leading-4 text-slate-400 text-center",
                        title: unref(t)("noData")
                      }, " — ", 8, ["title"]))
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs text-blue-700 dark:text-blue-300" }, toDisplayString(bundle.views ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs text-red-700 dark:text-red-300" }, toDisplayString(bundle.likes ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center text-xs text-emerald-700 dark:text-emerald-300" }, toDisplayString(bundle.prices_count ?? 0), 1)
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end space-x-2" }, [
                      createVNode(_sfc_main$5, {
                        isActive: bundle.activity,
                        title: bundle.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", bundle)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolBundles.edit", {
                          schoolBundle: bundle.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        title: unref(t)("delete"),
                        onDelete: ($event) => emit("delete", bundle)
                      }, null, 8, ["title", "onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-center" }, [
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedBundles.includes(bundle.id),
                        onChange: ($event) => emit("toggle-select", bundle.id)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolBundle/Table/BundleTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "BundleCardGrid",
  __ssrInlineRender: true,
  props: {
    bundles: {
      type: Array,
      default: () => []
    },
    selectedBundles: {
      type: Array,
      default: () => []
    }
  },
  emits: [
    "toggle-activity",
    "delete",
    "update-sort-order",
    "toggle-select",
    "toggle-all"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const localBundles = ref([]);
    watch(
      () => props.bundles,
      (newValue) => {
        localBundles.value = JSON.parse(JSON.stringify(newValue || []));
      },
      { immediate: true, deep: true }
    );
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localBundles.value.map((bundle) => bundle.id)
      );
    };
    const getBundleTitle = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.translation) == null ? void 0 : _a.title) || `ID: ${(bundle == null ? void 0 : bundle.id) ?? "—"}`;
    };
    const getBundleSubtitle = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getCourseTitle = (course) => {
      var _a;
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.title) || (course == null ? void 0 : course.slug) || "";
    };
    const getCourses = (bundle) => {
      return Array.isArray(bundle == null ? void 0 : bundle.courses) ? bundle.courses : [];
    };
    const getCoursesCount = (bundle) => {
      if (typeof (bundle == null ? void 0 : bundle.courses_count) === "number") {
        return bundle.courses_count;
      }
      return getCourses(bundle).length;
    };
    const getCourseTitles = (bundle) => {
      return getCourses(bundle).map(getCourseTitle).filter(Boolean);
    };
    const getCourseTitlesTooltip = (bundle) => {
      return getCourseTitles(bundle).join("\n");
    };
    const getPrimaryImage = (bundle) => {
      if (bundle == null ? void 0 : bundle.primary_image) {
        return bundle.primary_image;
      }
      if (Array.isArray(bundle == null ? void 0 : bundle.images) && bundle.images.length) {
        return bundle.images[0];
      }
      return null;
    };
    const imageUrl = (bundle) => {
      const image = getPrimaryImage(bundle);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/school/school_bundle_images/default-image.png";
    };
    const imageAlt = (bundle) => {
      const image = getPrimaryImage(bundle);
      return (image == null ? void 0 : image.alt) || getBundleTitle(bundle) || t("defaultImageAlt");
    };
    const imageTitle = (bundle) => {
      const image = getPrimaryImage(bundle);
      return (image == null ? void 0 : image.caption) || getBundleTitle(bundle) || t("defaultImageTitle");
    };
    const formatDate = (value) => {
      if (!value) return "—";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return "—";
      }
      return date.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "short",
        day: "numeric"
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedBundles.length)}</div>`);
      if (localBundles.value.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(localBundles.value.length && localBundles.value.every((bundle) => __props.selectedBundles.includes(bundle.id))) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localBundles.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          tag: "div",
          modelValue: localBundles.value,
          "onUpdate:modelValue": ($event) => localBundles.value = $event,
          "item-key": "id",
          handle: ".drag-handle",
          class: "grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: bundle }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<article class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"${_scopeId}><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100"${ssrRenderAttr("title", `[${bundle.sort}] ${formatDate(bundle.published_at)}`)}${_scopeId}> ID: ${ssrInterpolate(bundle.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}><span class="text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300"${ssrRenderAttr("title", unref(t)("courses"))}${_scopeId}>${ssrInterpolate(unref(t)("courses"))}: ${ssrInterpolate(getCoursesCount(bundle))}</span><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedBundles.includes(bundle.id)) ? " checked" : ""}${_scopeId}></div></header><div class="relative w-full h-32 bg-slate-200 dark:bg-slate-900"${_scopeId}><img${ssrRenderAttr("src", imageUrl(bundle))}${ssrRenderAttr("alt", imageAlt(bundle))}${ssrRenderAttr("title", imageTitle(bundle))} class="w-full h-full object-cover"${_scopeId}></div><div class="flex flex-col flex-1 px-3 py-2 space-y-1"${_scopeId}><a${ssrRenderAttr("href", `/school-bundles/${encodeURIComponent(bundle.slug)}`)} target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-sky-700 dark:text-sky-300 hover:underline line-clamp-2 text-center"${ssrRenderAttr("title", getBundleSubtitle(bundle) || getBundleTitle(bundle))}${_scopeId}>${ssrInterpolate(getBundleTitle(bundle))}</a>`);
              if (getBundleSubtitle(bundle)) {
                _push2(`<div class="text-[11px] text-slate-600 dark:text-slate-200 text-center font-semibold line-clamp-2"${ssrRenderAttr("title", getBundleSubtitle(bundle))}${_scopeId}>${ssrInterpolate(getBundleSubtitle(bundle))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (getCourseTitles(bundle).length) {
                _push2(`<div class="py-1 font-semibold text-[11px] text-left text-slate-900 dark:text-slate-100 border border-dashed border-slate-400"${ssrRenderAttr("title", getCourseTitlesTooltip(bundle))}${_scopeId}><div class="mb-1 text-[13px] text-center font-semibold text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(unref(t)("courses"))}: </div><ul class="space-y-0.5 max-h-20 overflow-auto pr-1"${_scopeId}><!--[-->`);
                ssrRenderList(getCourseTitles(bundle), (title, index) => {
                  _push2(`<li class="leading-snug pl-2 relative"${_scopeId}><span class="absolute left-0 text-red-400"${_scopeId}>•</span> ${ssrInterpolate(title)}</li>`);
                });
                _push2(`<!--]--></ul></div>`);
              } else {
                _push2(`<div class="mt-1 text-[11px] text-center text-slate-400"${ssrRenderAttr("title", unref(t)("noData"))}${_scopeId}>${ssrInterpolate(unref(t)("courses"))}: — </div>`);
              }
              _push2(`<div class="flex flex-wrap justify-center gap-3 mt-4 text-[11px] text-slate-900 dark:text-slate-200"${_scopeId}><div${_scopeId}><span class="font-semibold text-gray-500 dark:text-gray-400"${_scopeId}>${ssrInterpolate(unref(t)("views"))}: </span><span class="font-semibold text-blue-700 dark:text-blue-300"${_scopeId}>${ssrInterpolate(bundle.views ?? 0)}</span></div><div${_scopeId}><span class="font-semibold text-gray-500 dark:text-gray-400"${_scopeId}>${ssrInterpolate(unref(t)("likes"))}: </span><span class="font-semibold text-red-700 dark:text-red-300"${_scopeId}>${ssrInterpolate(bundle.likes ?? 0)}</span></div><div${_scopeId}><span class="font-semibold text-gray-500 dark:text-gray-400"${_scopeId}>${ssrInterpolate(unref(t)("prices"))}: </span><span class="font-semibold text-emerald-700 dark:text-emerald-300"${_scopeId}>${ssrInterpolate(bundle.prices_count ?? 0)}</span></div></div></div><footer class="flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                isActive: bundle.activity,
                title: bundle.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", bundle)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolBundles.edit", {
                  schoolBundle: bundle.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                title: unref(t)("delete"),
                onDelete: ($event) => emit("delete", bundle)
              }, null, _parent2, _scopeId));
              _push2(`</div></footer></article>`);
            } else {
              return [
                createVNode("article", { class: "relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150" }, [
                  createVNode("header", { class: "flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "drag-handle text-slate-400 hover:text-slate-700 dark:hover:text-slate-100",
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
                      createVNode("div", {
                        class: "text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100",
                        title: `[${bundle.sort}] ${formatDate(bundle.published_at)}`
                      }, " ID: " + toDisplayString(bundle.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("span", {
                        class: "text-[10px] px-1.5 py-0.5 rounded-sm border border-gray-400 bg-sky-100 dark:bg-sky-900/50 text-sky-700 dark:text-sky-300",
                        title: unref(t)("courses")
                      }, toDisplayString(unref(t)("courses")) + ": " + toDisplayString(getCoursesCount(bundle)), 9, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        checked: __props.selectedBundles.includes(bundle.id),
                        onChange: ($event) => emit("toggle-select", bundle.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "relative w-full h-32 bg-slate-200 dark:bg-slate-900" }, [
                    createVNode("img", {
                      src: imageUrl(bundle),
                      alt: imageAlt(bundle),
                      title: imageTitle(bundle),
                      class: "w-full h-full object-cover"
                    }, null, 8, ["src", "alt", "title"])
                  ]),
                  createVNode("div", { class: "flex flex-col flex-1 px-3 py-2 space-y-1" }, [
                    createVNode("a", {
                      href: `/school-bundles/${encodeURIComponent(bundle.slug)}`,
                      target: "_blank",
                      rel: "noopener noreferrer",
                      class: "text-sm font-semibold text-sky-700 dark:text-sky-300 hover:underline line-clamp-2 text-center",
                      title: getBundleSubtitle(bundle) || getBundleTitle(bundle)
                    }, toDisplayString(getBundleTitle(bundle)), 9, ["href", "title"]),
                    getBundleSubtitle(bundle) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-[11px] text-slate-600 dark:text-slate-200 text-center font-semibold line-clamp-2",
                      title: getBundleSubtitle(bundle)
                    }, toDisplayString(getBundleSubtitle(bundle)), 9, ["title"])) : createCommentVNode("", true),
                    getCourseTitles(bundle).length ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "py-1 font-semibold text-[11px] text-left text-slate-900 dark:text-slate-100 border border-dashed border-slate-400",
                      title: getCourseTitlesTooltip(bundle)
                    }, [
                      createVNode("div", { class: "mb-1 text-[13px] text-center font-semibold text-teal-700 dark:text-teal-300" }, toDisplayString(unref(t)("courses")) + ": ", 1),
                      createVNode("ul", { class: "space-y-0.5 max-h-20 overflow-auto pr-1" }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(getCourseTitles(bundle), (title, index) => {
                          return openBlock(), createBlock("li", {
                            key: index,
                            class: "leading-snug pl-2 relative"
                          }, [
                            createVNode("span", { class: "absolute left-0 text-red-400" }, "•"),
                            createTextVNode(" " + toDisplayString(title), 1)
                          ]);
                        }), 128))
                      ])
                    ], 8, ["title"])) : (openBlock(), createBlock("div", {
                      key: 2,
                      class: "mt-1 text-[11px] text-center text-slate-400",
                      title: unref(t)("noData")
                    }, toDisplayString(unref(t)("courses")) + ": — ", 9, ["title"])),
                    createVNode("div", { class: "flex flex-wrap justify-center gap-3 mt-4 text-[11px] text-slate-900 dark:text-slate-200" }, [
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold text-gray-500 dark:text-gray-400" }, toDisplayString(unref(t)("views")) + ": ", 1),
                        createVNode("span", { class: "font-semibold text-blue-700 dark:text-blue-300" }, toDisplayString(bundle.views ?? 0), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold text-gray-500 dark:text-gray-400" }, toDisplayString(unref(t)("likes")) + ": ", 1),
                        createVNode("span", { class: "font-semibold text-red-700 dark:text-red-300" }, toDisplayString(bundle.likes ?? 0), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "font-semibold text-gray-500 dark:text-gray-400" }, toDisplayString(unref(t)("prices")) + ": ", 1),
                        createVNode("span", { class: "font-semibold text-emerald-700 dark:text-emerald-300" }, toDisplayString(bundle.prices_count ?? 0), 1)
                      ])
                    ])
                  ]),
                  createVNode("footer", { class: "flex items-center justify-center px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        isActive: bundle.activity,
                        title: bundle.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", bundle)
                      }, null, 8, ["isActive", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolBundles.edit", {
                          schoolBundle: bundle.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        title: unref(t)("delete"),
                        onDelete: ($event) => emit("delete", bundle)
                      }, null, 8, ["title", "onDelete"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolBundle/View/BundleCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    currentLocale: {
      type: String,
      default: ""
    },
    availableLocales: {
      type: Array,
      default: () => []
    },
    adminSchoolBundlesProcessingMode: {
      type: String,
      default: "frontend"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    bundles: {
      type: [Array, Object],
      default: () => []
    },
    bundlesCount: {
      type: Number,
      default: 0
    },
    adminSchoolBundlesPerPage: {
      type: Number,
      default: 6
    },
    adminSchoolBundlesDefaultSort: {
      type: String,
      default: "idDesc"
    },
    sortParam: {
      type: String,
      default: ""
    },
    search: {
      type: String,
      default: ""
    },
    errors: {
      type: Object,
      default: () => ({})
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_bundles") || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_bundles", value);
    });
    const bundlesList = computed(() => {
      var _a;
      if (Array.isArray(props.bundles)) return props.bundles;
      if (Array.isArray((_a = props.bundles) == null ? void 0 : _a.data)) return props.bundles.data;
      return [];
    });
    const localBundles = ref([]);
    watch(
      bundlesList,
      (newValue) => {
        localBundles.value = JSON.parse(JSON.stringify(newValue || []));
      },
      { immediate: true, deep: true }
    );
    const itemsPerPage = ref(
      props.adminSchoolBundlesPerPage || 6
    );
    watch(itemsPerPage, (newValue) => {
      router.put(
        route("admin.settings.updateAdminCountSchoolBundles"),
        { value: newValue },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(`Показ ${newValue} элементов на странице.`);
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.value) || "Ошибка обновления кол-ва элементов."
            );
          }
        }
      );
    });
    const sortParam = ref(
      props.sortParam || props.adminSchoolBundlesDefaultSort || "idDesc"
    );
    watch(sortParam, (newValue) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortSchoolBundles"),
        { value: newValue },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing) {
              router.get(
                window.location.pathname,
                {
                  ...Object.fromEntries(
                    new URLSearchParams(window.location.search)
                  ),
                  sort: newValue || void 0,
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
            toast.error(
              (errors == null ? void 0 : errors.value) || "Ошибка обновления сортировки."
            );
          }
        }
      );
    });
    const searchQuery = ref(props.search || "");
    const currentPage = ref(1);
    const stripHtml = (value = "") => {
      if (value === null || typeof value === "undefined") return "";
      const html = typeof value === "string" ? value : JSON.stringify(value);
      return html.replace(/<\/p>/gi, " ").replace(/<br\s*\/?>/gi, " ").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/&amp;/gi, "&").replace(/&quot;/gi, '"').replace(/&#039;/gi, "'").replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/\s+/g, " ").trim();
    };
    const normalize = (value) => {
      return stripHtml(value).toString().trim().toLowerCase();
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(value || 0).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const getBundleTitle = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.translation) == null ? void 0 : _a.title) || `ID: ${(bundle == null ? void 0 : bundle.id) ?? "—"}`;
    };
    const getBundleSubtitle = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getBundleShort = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.translation) == null ? void 0 : _a.short) || "";
    };
    const getBundleDescription = (bundle) => {
      var _a;
      return ((_a = bundle == null ? void 0 : bundle.translation) == null ? void 0 : _a.description) || "";
    };
    const getBundleSlug = (bundle) => {
      return (bundle == null ? void 0 : bundle.slug) || "";
    };
    const getCourseTitle = (course) => {
      var _a;
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.title) || "";
    };
    const getCoursesText = (bundle) => {
      const courses = Array.isArray(bundle == null ? void 0 : bundle.courses) ? bundle.courses : [];
      return courses.map(getCourseTitle).filter(Boolean).join(" ");
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortBundles = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") {
        return list.filter((item) => !!item.activity);
      }
      if (sortParam.value === "inactive") {
        return list.filter((item) => !item.activity);
      }
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        titleAsc: (a, b) => normalize(getBundleTitle(a)).localeCompare(
          normalize(getBundleTitle(b)),
          props.currentLocale
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        titleDesc: (a, b) => normalize(getBundleTitle(b)).localeCompare(
          normalize(getBundleTitle(a)),
          props.currentLocale
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        slugAsc: (a, b) => normalize(getBundleSlug(a)).localeCompare(
          normalize(getBundleSlug(b)),
          props.currentLocale
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        slugDesc: (a, b) => normalize(getBundleSlug(b)).localeCompare(
          normalize(getBundleSlug(a)),
          props.currentLocale
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        activityAsc: byNumberAsc("activity"),
        activityDesc: byNumberDesc("activity"),
        viewsAsc: byNumberAsc("views"),
        viewsDesc: byNumberDesc("views"),
        likesAsc: byNumberAsc("likes"),
        likesDesc: byNumberDesc("likes"),
        coursesAsc: byNumberAsc("courses_count"),
        coursesDesc: byNumberDesc("courses_count"),
        imagesAsc: byNumberAsc("images_count"),
        imagesDesc: byNumberDesc("images_count"),
        pricesAsc: byNumberAsc("prices_count"),
        pricesDesc: byNumberDesc("prices_count"),
        orderItemsAsc: byNumberAsc("order_items_count"),
        orderItemsDesc: byNumberDesc("order_items_count"),
        publishedAtAsc: byDateAsc("published_at"),
        publishedAtDesc: byDateDesc("published_at"),
        dateAsc: byDateAsc("published_at"),
        dateDesc: byDateDesc("published_at"),
        createdAtAsc: byDateAsc("created_at"),
        createdAtDesc: byDateDesc("created_at"),
        updatedAtAsc: byDateAsc("updated_at"),
        updatedAtDesc: byDateDesc("updated_at")
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredBundles = computed(() => {
      let filtered = localBundles.value || [];
      const query = normalize(searchQuery.value);
      if (!query) {
        return sortBundles(filtered);
      }
      filtered = filtered.filter((bundle) => {
        const values = [
          bundle.id,
          getBundleSlug(bundle),
          getBundleTitle(bundle),
          getBundleSubtitle(bundle),
          getBundleShort(bundle),
          getBundleDescription(bundle),
          getCoursesText(bundle),
          bundle.views,
          bundle.likes,
          bundle.courses_count,
          bundle.images_count,
          bundle.prices_count,
          bundle.order_items_count
        ];
        return values.some(
          (value) => normalize(value).includes(query)
        );
      });
      return sortBundles(filtered);
    });
    const paginatedBundles = computed(() => {
      const per = Number(itemsPerPage.value || 6);
      const start = (currentPage.value - 1) * per;
      return filteredBundles.value.slice(
        start,
        start + per
      );
    });
    const displayedBundles = computed(() => {
      return props.useServerProcessing ? bundlesList.value : paginatedBundles.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const showConfirmDeleteModal = ref(false);
    const bundleToDelete = ref(null);
    const confirmDelete = (bundle) => {
      bundleToDelete.value = bundle;
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      bundleToDelete.value = null;
    };
    const deleteBundle = () => {
      var _a;
      if (!((_a = bundleToDelete.value) == null ? void 0 : _a.id)) return;
      const idToDelete = bundleToDelete.value.id;
      const titleToDelete = getBundleTitle(bundleToDelete.value);
      router.delete(
        route("admin.schoolBundles.destroy", {
          schoolBundle: idToDelete
        }),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Набор курсов "${titleToDelete}" удалён.`
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0];
            const errorMessage = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[firstKey]) || "Произошла ошибка при удалении.";
            toast.error(
              `${errorMessage} (Набор: ${titleToDelete})`
            );
          },
          onFinish: closeModal
        }
      );
    };
    const patchBundle = (bundleId, payload) => {
      const index = localBundles.value.findIndex(
        (bundle) => bundle.id === bundleId
      );
      if (index === -1) return;
      localBundles.value[index] = {
        ...localBundles.value[index],
        ...payload
      };
    };
    const selectedBundles = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedBundles.value.map((bundle) => bundle.id);
      if (checked) {
        selectedBundles.value = [
          .../* @__PURE__ */ new Set([
            ...selectedBundles.value,
            ...ids
          ])
        ];
        return;
      }
      selectedBundles.value = selectedBundles.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectBundle = (id) => {
      const index = selectedBundles.value.indexOf(id);
      if (index > -1) {
        selectedBundles.value.splice(index, 1);
        return;
      }
      selectedBundles.value.push(id);
    };
    const handleSortOrderUpdate = (orderedIds) => {
      const pageOffset = props.useServerProcessing ? 0 : (currentPage.value - 1) * Number(itemsPerPage.value || 6);
      const items = orderedIds.map((id, index) => ({
        id,
        sort: pageOffset + index + 1
      }));
      if (!items.length) return;
      router.put(
        route("admin.actions.schoolBundles.updateSortBulk"),
        { items },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Порядок наборов курсов успешно обновлён."
            );
          },
          onError: (errors) => {
            console.error(
              "Ошибка обновления сортировки наборов курсов:",
              errors
            );
            toast.error(
              (errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок наборов курсов."
            );
            router.reload({
              only: ["bundles"],
              preserveScroll: true
            });
          }
        }
      );
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedBundles.value.length) {
        toast.warning(
          "Выберите наборы курсов для активации/деактивации."
        );
        return;
      }
      const idsToUpdate = [
        ...selectedBundles.value
      ];
      router.put(
        route("admin.actions.schoolBundles.bulkUpdateActivity"),
        {
          ids: idsToUpdate,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            idsToUpdate.forEach((id) => {
              patchBundle(id, {
                activity: newActivity
              });
            });
            selectedBundles.value = [];
            toast.success(
              "Активность выбранных наборов курсов обновлена."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Ошибка массового обновления активности."
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
      }
      event.target.value = "";
    };
    const toggleActivity = (bundle) => {
      const newActivity = !bundle.activity;
      const bundleTitle = getBundleTitle(bundle);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route("admin.actions.schoolBundles.updateActivity", {
          schoolBundle: bundle.id
        }),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchBundle(bundle.id, {
              activity: newActivity
            });
            toast.success(
              `Набор курсов "${bundleTitle}" ${actionText}.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для набора "${bundleTitle}".`
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("bundles")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("bundles"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("bundles")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("bundles")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$8, {
              href: _ctx.route("admin.schoolBundles.create")
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
                  _push3(` ${ssrInterpolate(unref(t)("addBundle"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addBundle")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              "setting-key": "adminSchoolBundlesProcessingMode",
              mode: __props.adminSchoolBundlesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.bundlesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.bundlesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.bundlesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.bundlesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolBundles"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$3, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (value) => sortParam.value = value
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.bundlesCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.bundlesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.bundlesCount), 1)
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
            if (__props.bundlesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredBundles.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.bundles }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                bundles: displayedBundles.value,
                "selected-bundles": selectedBundles.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectBundle,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                bundles: displayedBundles.value,
                "selected-bundles": selectedBundles.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectBundle,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            }
            if (__props.bundlesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredBundles.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.bundles }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$i, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteBundle,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$8, {
                      href: _ctx.route("admin.schoolBundles.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addBundle")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$9, {
                      "setting-key": "adminSchoolBundlesProcessingMode",
                      mode: __props.adminSchoolBundlesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.bundlesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.bundlesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.bundlesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.bundlesCount ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountSchoolBundles"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (value) => sortParam.value = value
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.bundlesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.bundlesCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.bundlesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredBundles.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.bundles
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    bundles: displayedBundles.value,
                    "selected-bundles": selectedBundles.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectBundle,
                    onToggleAll: toggleAll
                  }, null, 8, ["bundles", "selected-bundles"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    bundles: displayedBundles.value,
                    "selected-bundles": selectedBundles.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectBundle,
                    onToggleAll: toggleAll
                  }, null, 8, ["bundles", "selected-bundles"])),
                  __props.bundlesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredBundles.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.bundles
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteBundle,
                cancelText: unref(t)("cancel"),
                confirmText: unref(t)("yesDelete"),
                onClose: closeModal
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolBundles/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
