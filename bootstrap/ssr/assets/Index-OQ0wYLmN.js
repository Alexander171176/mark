import { mergeProps, unref, useSSRContext, ref, watch, computed, withCtx, createVNode, createBlock, openBlock, toDisplayString, createCommentVNode, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderComponent } from "vue/server-renderer";
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolSubscriptionPlan/Select/BulkActionSelect.vue");
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
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option value="sortAsc">${ssrInterpolate(unref(t)("sortNumber"))} ↑</option><option value="sortDesc">${ssrInterpolate(unref(t)("sortNumber"))} ↓</option><option disabled>────────────────</option><option value="titleAsc">${ssrInterpolate(unref(t)("title"))} A→Z</option><option value="titleDesc">${ssrInterpolate(unref(t)("title"))} Z→A</option><option disabled>────────────────</option><option value="publishedAtDesc">${ssrInterpolate(unref(t)("publishedAt"))} ↓</option><option value="publishedAtAsc">${ssrInterpolate(unref(t)("publishedAt"))} ↑</option><option disabled>────────────────</option><option value="availabilityNowFirst">${ssrInterpolate(unref(t)("availability"))}</option><option value="availableFromAsc">${ssrInterpolate(unref(t)("shortStarted"))} ↑</option><option value="availableFromDesc">${ssrInterpolate(unref(t)("shortStarted"))} ↓</option><option value="availableUntilAsc">${ssrInterpolate(unref(t)("shortExpires"))} ↑</option><option value="availableUntilDesc">${ssrInterpolate(unref(t)("shortExpires"))} ↓</option><option disabled>────────────────</option><option value="priceDesc">${ssrInterpolate(unref(t)("price"))} ↓</option><option value="priceAsc">${ssrInterpolate(unref(t)("price"))} ↑</option><option disabled>────────────────</option><option value="trialDaysDesc">${ssrInterpolate(unref(t)("trial"))} ↓</option><option value="trialDaysAsc">${ssrInterpolate(unref(t)("trial"))} ↑</option><option disabled>────────────────</option><option value="billingPeriodAsc">${ssrInterpolate(unref(t)("billingPeriod"))} A→Z</option><option value="billingPeriodDesc">${ssrInterpolate(unref(t)("billingPeriod"))} Z→A</option><option disabled>────────────────</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolSubscriptionPlan/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "SubscriptionPlanTable",
  __ssrInlineRender: true,
  props: {
    subscriptionPlans: {
      type: Array,
      default: () => []
    },
    selectedPlans: {
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
    const localPlans = ref([]);
    watch(
      () => props.subscriptionPlans,
      (plans) => {
        localPlans.value = Array.isArray(plans) ? plans.map((plan) => ({ ...plan })) : [];
      },
      {
        immediate: true,
        deep: true
      }
    );
    const allSelected = computed(() => {
      return localPlans.value.length > 0 && localPlans.value.every(
        (plan) => props.selectedPlans.includes(plan.id)
      );
    });
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localPlans.value.map((plan) => plan.id)
      );
    };
    const getPlanTitle = (plan) => {
      var _a;
      return ((_a = plan == null ? void 0 : plan.translation) == null ? void 0 : _a.title) || `ID: ${plan == null ? void 0 : plan.id}`;
    };
    const getPlanSubtitle = (plan) => {
      var _a;
      return ((_a = plan == null ? void 0 : plan.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getPrimaryImage = (plan) => {
      var _a;
      return (plan == null ? void 0 : plan.primary_image) || ((_a = plan == null ? void 0 : plan.images) == null ? void 0 : _a[0]) || null;
    };
    const imageSrc = (plan) => {
      const image = getPrimaryImage(plan);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/school/school_subscription_plan_images/default-image.png";
    };
    const imageAlt = (plan) => {
      const image = getPrimaryImage(plan);
      return (image == null ? void 0 : image.alt) || getPlanTitle(plan) || t("defaultImageTitle");
    };
    const formatDate = (date) => {
      if (!date) {
        return "—";
      }
      const value = new Date(date);
      if (Number.isNaN(value.getTime())) {
        return "—";
      }
      return value.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "short",
        day: "numeric"
      });
    };
    const billingPeriodLabel = (period) => {
      const map = {
        day: "days",
        week: "weeks",
        month: "months",
        year: "years"
      };
      const key = map[String(period || "").toLowerCase()];
      return key ? t(key) : "—";
    };
    const periodLabel = (plan) => {
      return `${billingPeriodLabel(plan.billing_period)}: ${plan.interval ?? "—"}`;
    };
    const priceLabel = (plan) => {
      var _a;
      const price = plan.price ?? "0.00";
      const code = ((_a = plan.currency) == null ? void 0 : _a.code) || "";
      return `${price} ${code}`.trim();
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative overflow-hidden rounded-sm border border-slate-300 bg-white shadow-lg dark:border-slate-600 dark:bg-slate-700" }, _attrs))}><div class="flex flex-col gap-2 border-b border-slate-300 px-3 py-2 dark:border-slate-600 sm:flex-row sm:items-center sm:justify-between"><div class="text-xs font-semibold text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedPlans.length)}</div>`);
      if (localPlans.value.length) {
        _push(`<label class="inline-flex cursor-pointer items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="rounded border-slate-400"${ssrIncludeBooleanAttr(allSelected.value) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localPlans.value.length) {
        _push(`<div class="overflow-x-auto"><table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 py-3 w-px"><svg class="w-4 h-4 fill-current text-slate-800 dark:text-slate-200" viewBox="0 0 24 24"><path d="M12.707,2.293a1,1,0,0,0-1.414,0l-5,5A1,1,0,0,0,7.707,8.707L12,4.414l4.293,4.293a1,1,0,0,0,1.414-1.414Z"></path><path d="M16.293,15.293,12,19.586,7.707,15.293a1,1,0,0,0-1.414,1.414l5,5a1,1,0,0,0,1.414,0l5-5a1,1,0,0,0-1.414-1.414Z"></path></svg></th><th class="px-2 py-3 w-px text-center font-medium">${ssrInterpolate(unref(t)("id"))}</th><th class="px-2 py-3 whitespace-nowrap"><div class="flex justify-center"${ssrRenderAttr("title", unref(t)("image"))}><svg class="w-6 h-6 fill-current shrink-0" viewBox="0 0 512 512"><path d="M0 96C0 60.7 28.7 32 64 32l384 0c35.3 0 64 28.7 64 64l0 320c0 35.3-28.7 64-64 64L64 480c-35.3 0-64-28.7-64-64L0 96zM323.8 202.5c-4.5-6.6-11.9-10.5-19.8-10.5s-15.4 3.9-19.8 10.5l-87 127.6L170.7 297c-4.6-5.7-11.5-9-18.7-9s-14.2 3.3-18.7 9l-64 80c-5.8 7.2-6.9 17.1-2.9 25.4s12.4 13.6 21.6 13.6l96 0 32 0 208 0c8.9 0 17.1-4.9 21.2-12.8s3.6-17.4-1.4-24.7l-120-176zM112 192a48 48 0 1 0 0-96 48 48 0 1 0 0 96z"></path></svg></div></th><th class="px-2 py-3 w-px"><div class="font-medium text-left">${ssrInterpolate(unref(t)("subscriptionPlan"))}</div></th><th class="px-2 py-3 whitespace-nowrap text-center font-medium">${ssrInterpolate(unref(t)("period"))}</th><th class="px-2 py-3 whitespace-nowrap text-center font-medium">${ssrInterpolate(unref(t)("price"))}</th><th class="px-2 py-3 whitespace-nowrap text-center"${ssrRenderAttr("title", unref(t)("availability"))}><svg class="w-4 h-4 mx-auto" viewBox="0 0 24 24"><path class="fill-current text-violet-700 dark:text-violet-300" d="M22,13a1,1,0,0,1,0-2h1.949A12.006,12.006,0,0,0,13,.051V2a1,1,0,0,1-2,0V.051A12.006,12.006,0,0,0,.051,11H2a1,1,0,0,1,0,2H.051A12.006,12.006,0,0,0,11,23.949V22a1,1,0,0,1,2,0v1.949A12.006,12.006,0,0,0,23.949,13Zm-6,0H12a1,1,0,0,1-.832-.445l-4-6a1,1,0,1,1,1.664-1.11L12.535,11H16a1,1,0,0,1,0,2Z"></path></svg></th><th class="px-2 py-3 whitespace-nowrap text-end font-semibold">${ssrInterpolate(unref(t)("actions"))}</th><th class="px-2 py-3 whitespace-nowrap text-center"><input type="checkbox" class="rounded border-slate-400"${ssrIncludeBooleanAttr(allSelected.value) ? " checked" : ""}></th></tr></thead>`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localPlans.value,
          "onUpdate:modelValue": ($event) => localPlans.value = $event,
          tag: "tbody",
          "item-key": "id",
          handle: ".handle",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: plan }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"${_scopeId}><td class="px-2 py-1 text-center cursor-move handle"${_scopeId}><svg class="h-4 w-4 text-slate-500" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></td><td class="px-2 py-3 whitespace-nowrap text-center"${_scopeId}><div${ssrRenderAttr("title", `Sort: ${plan.sort ?? "—"}`)}${_scopeId}>${ssrInterpolate(plan.id)}</div></td><td class="px-2 py-3"${_scopeId}><div class="flex justify-center"${_scopeId}><img${ssrRenderAttr("src", imageSrc(plan))}${ssrRenderAttr("alt", imageAlt(plan))} class="h-9 w-12 rounded-sm object-cover"${_scopeId}></div></td><td class="px-2 py-3"${_scopeId}><div class="w-fit flex flex-col space-y-1"${_scopeId}><div class="text-sm text-blue-700 dark:text-blue-200"${ssrRenderAttr("title", getPlanTitle(plan))}${_scopeId}>${ssrInterpolate(getPlanTitle(plan))}</div><div class="text-[11px] text-slate-500 dark:text-slate-300"${ssrRenderAttr("title", plan.slug)}${_scopeId}>${ssrInterpolate(plan.slug || "—")}</div>`);
              if (getPlanSubtitle(plan)) {
                _push2(`<div class="text-[11px] text-slate-600 dark:text-slate-300"${ssrRenderAttr("title", getPlanSubtitle(plan))}${_scopeId}>${ssrInterpolate(getPlanSubtitle(plan))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-xs flex flex-col justify-center items-center"${_scopeId}><div${_scopeId}>${ssrInterpolate(periodLabel(plan))}</div><div class="text-fuchsia-700 dark:text-fuchsia-300"${ssrRenderAttr("title", unref(t)("trial"))}${_scopeId}>${ssrInterpolate(plan.trial_days ?? 0)} ${ssrInterpolate(unref(t)("days"))}</div><div class="text-[10px] text-slate-500 dark:text-slate-300"${ssrRenderAttr("title", unref(t)("autoRenew"))}${_scopeId}>${ssrInterpolate(unref(t)("autoRenew"))}: ${ssrInterpolate(plan.auto_renew ? unref(t)("yes") : unref(t)("no"))}</div></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-xs flex flex-col items-center justify-center"${_scopeId}><div class="text-emerald-700 dark:text-emerald-300"${_scopeId}>${ssrInterpolate(priceLabel(plan))}</div><div class="text-yellow-700 dark:text-yellow-300"${ssrRenderAttr("title", unref(t)("publishedAt"))}${_scopeId}>${ssrInterpolate(formatDate(plan.published_at))}</div></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="text-[10px] space-y-1"${_scopeId}><div${_scopeId}><span class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("shortStarted"))}: </span><span class="text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(formatDate(plan.available_from))}</span></div><div${_scopeId}><span class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("shortExpires"))}: </span><span class="text-sky-700 dark:text-sky-300"${_scopeId}>${ssrInterpolate(formatDate(plan.available_until))}</span></div></div></td><td class="px-2 py-3 whitespace-nowrap"${_scopeId}><div class="flex justify-end gap-2"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                "is-active": plan.activity,
                title: plan.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", plan)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolSubscriptionPlans.edit", {
                  schoolSubscriptionPlan: plan.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => emit("delete", plan)
              }, null, _parent2, _scopeId));
              _push2(`</div></td><td class="px-2 py-3 whitespace-nowrap text-center"${_scopeId}><input type="checkbox" class="rounded border-slate-400"${ssrIncludeBooleanAttr(__props.selectedPlans.includes(plan.id)) ? " checked" : ""}${_scopeId}></td></tr>`);
            } else {
              return [
                createVNode("tr", { class: "text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800" }, [
                  createVNode("td", { class: "px-2 py-1 text-center cursor-move handle" }, [
                    (openBlock(), createBlock("svg", {
                      class: "h-4 w-4 text-slate-500",
                      fill: "currentColor",
                      viewBox: "0 0 20 20"
                    }, [
                      createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                    ]))
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap text-center" }, [
                    createVNode("div", {
                      title: `Sort: ${plan.sort ?? "—"}`
                    }, toDisplayString(plan.id), 9, ["title"])
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("div", { class: "flex justify-center" }, [
                      createVNode("img", {
                        src: imageSrc(plan),
                        alt: imageAlt(plan),
                        class: "h-9 w-12 rounded-sm object-cover"
                      }, null, 8, ["src", "alt"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3" }, [
                    createVNode("div", { class: "w-fit flex flex-col space-y-1" }, [
                      createVNode("div", {
                        class: "text-sm text-blue-700 dark:text-blue-200",
                        title: getPlanTitle(plan)
                      }, toDisplayString(getPlanTitle(plan)), 9, ["title"]),
                      createVNode("div", {
                        class: "text-[11px] text-slate-500 dark:text-slate-300",
                        title: plan.slug
                      }, toDisplayString(plan.slug || "—"), 9, ["title"]),
                      getPlanSubtitle(plan) ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "text-[11px] text-slate-600 dark:text-slate-300",
                        title: getPlanSubtitle(plan)
                      }, toDisplayString(getPlanSubtitle(plan)), 9, ["title"])) : createCommentVNode("", true)
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-xs flex flex-col justify-center items-center" }, [
                      createVNode("div", null, toDisplayString(periodLabel(plan)), 1),
                      createVNode("div", {
                        class: "text-fuchsia-700 dark:text-fuchsia-300",
                        title: unref(t)("trial")
                      }, toDisplayString(plan.trial_days ?? 0) + " " + toDisplayString(unref(t)("days")), 9, ["title"]),
                      createVNode("div", {
                        class: "text-[10px] text-slate-500 dark:text-slate-300",
                        title: unref(t)("autoRenew")
                      }, toDisplayString(unref(t)("autoRenew")) + ": " + toDisplayString(plan.auto_renew ? unref(t)("yes") : unref(t)("no")), 9, ["title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-xs flex flex-col items-center justify-center" }, [
                      createVNode("div", { class: "text-emerald-700 dark:text-emerald-300" }, toDisplayString(priceLabel(plan)), 1),
                      createVNode("div", {
                        class: "text-yellow-700 dark:text-yellow-300",
                        title: unref(t)("publishedAt")
                      }, toDisplayString(formatDate(plan.published_at)), 9, ["title"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "text-[10px] space-y-1" }, [
                      createVNode("div", null, [
                        createVNode("span", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("shortStarted")) + ": ", 1),
                        createVNode("span", { class: "text-teal-700 dark:text-teal-300" }, toDisplayString(formatDate(plan.available_from)), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("shortExpires")) + ": ", 1),
                        createVNode("span", { class: "text-sky-700 dark:text-sky-300" }, toDisplayString(formatDate(plan.available_until)), 1)
                      ])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap" }, [
                    createVNode("div", { class: "flex justify-end gap-2" }, [
                      createVNode(_sfc_main$5, {
                        "is-active": plan.activity,
                        title: plan.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", plan)
                      }, null, 8, ["is-active", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolSubscriptionPlans.edit", {
                          schoolSubscriptionPlan: plan.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        onDelete: ($event) => emit("delete", plan)
                      }, null, 8, ["onDelete"])
                    ])
                  ]),
                  createVNode("td", { class: "px-2 py-3 whitespace-nowrap text-center" }, [
                    createVNode("input", {
                      type: "checkbox",
                      class: "rounded border-slate-400",
                      checked: __props.selectedPlans.includes(plan.id),
                      onChange: ($event) => emit("toggle-select", plan.id)
                    }, null, 40, ["checked", "onChange"])
                  ])
                ])
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</table></div>`);
      } else {
        _push(`<div class="p-5 text-center text-sm font-semibold text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolSubscriptionPlan/Table/SubscriptionPlanTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "SubscriptionPlanCardGrid",
  __ssrInlineRender: true,
  props: {
    subscriptionPlans: {
      type: Array,
      default: () => []
    },
    selectedPlans: {
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
    const localPlans = ref([]);
    watch(
      () => props.subscriptionPlans,
      (plans) => {
        localPlans.value = Array.isArray(plans) ? plans.map((plan) => ({ ...plan })) : [];
      },
      {
        immediate: true,
        deep: true
      }
    );
    const allSelected = computed(() => {
      return localPlans.value.length > 0 && localPlans.value.every(
        (plan) => props.selectedPlans.includes(plan.id)
      );
    });
    const handleDragEnd = () => {
      emit(
        "update-sort-order",
        localPlans.value.map((plan) => plan.id)
      );
    };
    const getPlanTitle = (plan) => {
      var _a;
      return ((_a = plan == null ? void 0 : plan.translation) == null ? void 0 : _a.title) || `ID: ${plan == null ? void 0 : plan.id}`;
    };
    const getPlanSubtitle = (plan) => {
      var _a;
      return ((_a = plan == null ? void 0 : plan.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getPlanShort = (plan) => {
      var _a;
      return ((_a = plan == null ? void 0 : plan.translation) == null ? void 0 : _a.short) || "";
    };
    const getPrimaryImage = (plan) => {
      var _a;
      return (plan == null ? void 0 : plan.primary_image) || ((_a = plan == null ? void 0 : plan.images) == null ? void 0 : _a[0]) || null;
    };
    const imageSrc = (plan) => {
      const image = getPrimaryImage(plan);
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.thumb_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || "/storage/school/school_subscription_plan_images/default-image.png";
    };
    const imageAlt = (plan) => {
      const image = getPrimaryImage(plan);
      return (image == null ? void 0 : image.alt) || getPlanTitle(plan) || t("defaultImageTitle");
    };
    const formatDate = (date) => {
      if (!date) {
        return "—";
      }
      const value = new Date(date);
      if (Number.isNaN(value.getTime())) {
        return "—";
      }
      return value.toLocaleDateString("ru-RU", {
        year: "numeric",
        month: "short",
        day: "numeric"
      });
    };
    const billingPeriodLabel = (period) => {
      const map = {
        day: "days",
        week: "weeks",
        month: "months",
        year: "years"
      };
      const key = map[String(period || "").toLowerCase()];
      return key ? t(key) : "—";
    };
    const periodLabel = (plan) => {
      return `${billingPeriodLabel(plan.billing_period)}: ${plan.interval ?? "—"}`;
    };
    const priceLabel = (plan) => {
      var _a;
      const price = plan.price ?? "0.00";
      const code = ((_a = plan.currency) == null ? void 0 : _a.code) || "";
      return `${price} ${code}`.trim();
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-400 bg-white shadow-lg dark:border-slate-500 dark:bg-slate-700" }, _attrs))}><div class="flex flex-col gap-2 border-b border-slate-400 px-3 py-2 dark:border-slate-500 sm:flex-row sm:items-center sm:justify-between"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedPlans.length)}</div>`);
      if (localPlans.value.length) {
        _push(`<label class="inline-flex cursor-pointer items-center gap-2 text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="rounded border-slate-400"${ssrIncludeBooleanAttr(allSelected.value) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (localPlans.value.length) {
        _push(`<div class="p-3">`);
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: localPlans.value,
          "onUpdate:modelValue": ($event) => localPlans.value = $event,
          tag: "div",
          "item-key": "id",
          handle: ".drag-handle",
          class: "grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4",
          onEnd: handleDragEnd
        }, {
          item: withCtx(({ element: plan }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<article class="relative flex h-full flex-col overflow-hidden rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80"${_scopeId}><header class="flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-2"${_scopeId}><button type="button" class="drag-handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100"${ssrRenderAttr("title", unref(t)("dragDrop"))}${_scopeId}><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"${_scopeId}><path d="M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z"${_scopeId}></path></svg></button><div class="rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100"${ssrRenderAttr("title", `Sort: ${plan.sort ?? "—"}`)}${_scopeId}> ID: ${ssrInterpolate(plan.id)}</div></div><div class="flex items-center space-x-2"${_scopeId}><span class="rounded-sm border border-gray-400 bg-teal-100 px-1.5 py-0.5 text-[10px] font-bold text-teal-700 dark:bg-teal-900/50 dark:text-teal-300"${ssrRenderAttr("title", unref(t)("price"))}${_scopeId}>${ssrInterpolate(priceLabel(plan))}</span><input type="checkbox" class="rounded border-slate-400"${ssrIncludeBooleanAttr(__props.selectedPlans.includes(plan.id)) ? " checked" : ""}${_scopeId}></div></header><div class="relative h-32 w-full bg-slate-200 dark:bg-slate-900"${_scopeId}><img${ssrRenderAttr("src", imageSrc(plan))}${ssrRenderAttr("alt", imageAlt(plan))} class="h-full w-full object-cover"${_scopeId}>`);
              if (plan.images_count > 1) {
                _push2(`<div class="absolute right-1 top-1 rounded-sm border border-gray-400 bg-slate-900/70 px-1.5 py-0.5 text-[9px] font-semibold text-white"${ssrRenderAttr("title", unref(t)("images"))}${_scopeId}>${ssrInterpolate(plan.images_count)}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div><div class="flex flex-1 flex-col px-3 py-2"${_scopeId}><div class="line-clamp-2 text-center text-sm font-semibold text-blue-700 dark:text-blue-200"${ssrRenderAttr("title", getPlanSubtitle(plan) || getPlanTitle(plan))}${_scopeId}>${ssrInterpolate(getPlanTitle(plan))}</div><div class="text-center text-[9px] text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(plan.slug || "—")}</div>`);
              if (getPlanSubtitle(plan)) {
                _push2(`<div class="text-center text-[11px] font-semibold text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(getPlanSubtitle(plan))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              if (getPlanShort(plan)) {
                _push2(`<div class="my-1 border border-dashed border-gray-400 px-1 py-1 text-center text-[11px] text-gray-500 dark:text-gray-400"${_scopeId}>${ssrInterpolate(getPlanShort(plan))}</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="mt-1 flex flex-wrap justify-center gap-1 text-[10px] font-semibold"${_scopeId}><span class="rounded-sm border border-gray-400 bg-teal-100 dark:bg-teal-900 px-2 py-0.5 text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(unref(t)("period"))}: ${ssrInterpolate(periodLabel(plan))}</span><span class="rounded-sm border border-gray-400 bg-fuchsia-100 dark:bg-fuchsia-900 px-2 py-0.5 text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(unref(t)("trial"))}: ${ssrInterpolate(plan.trial_days ?? 0)} ${ssrInterpolate(unref(t)("days"))}</span><span class="rounded-sm border border-gray-400 bg-cyan-100 dark:bg-cyan-900 px-2 py-0.5 text-cyan-700 dark:text-cyan-300"${_scopeId}>${ssrInterpolate(unref(t)("autoRenew"))}: ${ssrInterpolate(plan.auto_renew ? unref(t)("yes") : unref(t)("no"))}</span></div><div class="mt-2 text-center text-[11px]"${_scopeId}><div class="font-semibold"${_scopeId}><span class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("publishedAt"))}: </span><span class="text-yellow-700 dark:text-yellow-300"${_scopeId}>${ssrInterpolate(formatDate(plan.published_at))}</span></div><div${_scopeId}><span class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("shortStarted"))}: </span><span class="font-semibold text-teal-700 dark:text-teal-300"${_scopeId}>${ssrInterpolate(formatDate(plan.available_from))}</span></div><div${_scopeId}><span class="text-slate-700 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("shortExpires"))}: </span><span class="font-semibold text-sky-700 dark:text-sky-300"${_scopeId}>${ssrInterpolate(formatDate(plan.available_until))}</span></div></div></div><footer class="flex items-center justify-center border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500"${_scopeId}><div class="flex items-center space-x-1"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                "is-active": plan.activity,
                title: plan.activity ? unref(t)("enabled") : unref(t)("disabled"),
                onToggleActivity: ($event) => emit("toggle-activity", plan)
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$6, {
                href: _ctx.route("admin.schoolSubscriptionPlans.edit", {
                  schoolSubscriptionPlan: plan.id
                })
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$7, {
                onDelete: ($event) => emit("delete", plan)
              }, null, _parent2, _scopeId));
              _push2(`</div></footer></article>`);
            } else {
              return [
                createVNode("article", { class: "relative flex h-full flex-col overflow-hidden rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80" }, [
                  createVNode("header", { class: "flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("button", {
                        type: "button",
                        class: "drag-handle cursor-move text-slate-400 hover:text-slate-700 dark:hover:text-slate-100",
                        title: unref(t)("dragDrop")
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "h-4 w-4",
                          fill: "currentColor",
                          viewBox: "0 0 20 20"
                        }, [
                          createVNode("path", { d: "M7 4h2v2H7V4zm4 0h2v2h-2V4zM7 8h2v2H7V8zm4 0h2v2h-2V8zM7 12h2v2H7v-2zm4 0h2v2h-2v-2z" })
                        ]))
                      ], 8, ["title"]),
                      createVNode("div", {
                        class: "rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100",
                        title: `Sort: ${plan.sort ?? "—"}`
                      }, " ID: " + toDisplayString(plan.id), 9, ["title"])
                    ]),
                    createVNode("div", { class: "flex items-center space-x-2" }, [
                      createVNode("span", {
                        class: "rounded-sm border border-gray-400 bg-teal-100 px-1.5 py-0.5 text-[10px] font-bold text-teal-700 dark:bg-teal-900/50 dark:text-teal-300",
                        title: unref(t)("price")
                      }, toDisplayString(priceLabel(plan)), 9, ["title"]),
                      createVNode("input", {
                        type: "checkbox",
                        class: "rounded border-slate-400",
                        checked: __props.selectedPlans.includes(plan.id),
                        onChange: ($event) => emit("toggle-select", plan.id)
                      }, null, 40, ["checked", "onChange"])
                    ])
                  ]),
                  createVNode("div", { class: "relative h-32 w-full bg-slate-200 dark:bg-slate-900" }, [
                    createVNode("img", {
                      src: imageSrc(plan),
                      alt: imageAlt(plan),
                      class: "h-full w-full object-cover"
                    }, null, 8, ["src", "alt"]),
                    plan.images_count > 1 ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "absolute right-1 top-1 rounded-sm border border-gray-400 bg-slate-900/70 px-1.5 py-0.5 text-[9px] font-semibold text-white",
                      title: unref(t)("images")
                    }, toDisplayString(plan.images_count), 9, ["title"])) : createCommentVNode("", true)
                  ]),
                  createVNode("div", { class: "flex flex-1 flex-col px-3 py-2" }, [
                    createVNode("div", {
                      class: "line-clamp-2 text-center text-sm font-semibold text-blue-700 dark:text-blue-200",
                      title: getPlanSubtitle(plan) || getPlanTitle(plan)
                    }, toDisplayString(getPlanTitle(plan)), 9, ["title"]),
                    createVNode("div", { class: "text-center text-[9px] text-slate-500 dark:text-slate-300" }, toDisplayString(plan.slug || "—"), 1),
                    getPlanSubtitle(plan) ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "text-center text-[11px] font-semibold text-gray-700 dark:text-gray-300"
                    }, toDisplayString(getPlanSubtitle(plan)), 1)) : createCommentVNode("", true),
                    getPlanShort(plan) ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "my-1 border border-dashed border-gray-400 px-1 py-1 text-center text-[11px] text-gray-500 dark:text-gray-400"
                    }, toDisplayString(getPlanShort(plan)), 1)) : createCommentVNode("", true),
                    createVNode("div", { class: "mt-1 flex flex-wrap justify-center gap-1 text-[10px] font-semibold" }, [
                      createVNode("span", { class: "rounded-sm border border-gray-400 bg-teal-100 dark:bg-teal-900 px-2 py-0.5 text-teal-700 dark:text-teal-300" }, toDisplayString(unref(t)("period")) + ": " + toDisplayString(periodLabel(plan)), 1),
                      createVNode("span", { class: "rounded-sm border border-gray-400 bg-fuchsia-100 dark:bg-fuchsia-900 px-2 py-0.5 text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(unref(t)("trial")) + ": " + toDisplayString(plan.trial_days ?? 0) + " " + toDisplayString(unref(t)("days")), 1),
                      createVNode("span", { class: "rounded-sm border border-gray-400 bg-cyan-100 dark:bg-cyan-900 px-2 py-0.5 text-cyan-700 dark:text-cyan-300" }, toDisplayString(unref(t)("autoRenew")) + ": " + toDisplayString(plan.auto_renew ? unref(t)("yes") : unref(t)("no")), 1)
                    ]),
                    createVNode("div", { class: "mt-2 text-center text-[11px]" }, [
                      createVNode("div", { class: "font-semibold" }, [
                        createVNode("span", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("publishedAt")) + ": ", 1),
                        createVNode("span", { class: "text-yellow-700 dark:text-yellow-300" }, toDisplayString(formatDate(plan.published_at)), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("shortStarted")) + ": ", 1),
                        createVNode("span", { class: "font-semibold text-teal-700 dark:text-teal-300" }, toDisplayString(formatDate(plan.available_from)), 1)
                      ]),
                      createVNode("div", null, [
                        createVNode("span", { class: "text-slate-700 dark:text-slate-300" }, toDisplayString(unref(t)("shortExpires")) + ": ", 1),
                        createVNode("span", { class: "font-semibold text-sky-700 dark:text-sky-300" }, toDisplayString(formatDate(plan.available_until)), 1)
                      ])
                    ])
                  ]),
                  createVNode("footer", { class: "flex items-center justify-center border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500" }, [
                    createVNode("div", { class: "flex items-center space-x-1" }, [
                      createVNode(_sfc_main$5, {
                        "is-active": plan.activity,
                        title: plan.activity ? unref(t)("enabled") : unref(t)("disabled"),
                        onToggleActivity: ($event) => emit("toggle-activity", plan)
                      }, null, 8, ["is-active", "title", "onToggleActivity"]),
                      createVNode(_sfc_main$6, {
                        href: _ctx.route("admin.schoolSubscriptionPlans.edit", {
                          schoolSubscriptionPlan: plan.id
                        })
                      }, null, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        onDelete: ($event) => emit("delete", plan)
                      }, null, 8, ["onDelete"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/School/SchoolSubscriptionPlan/View/SubscriptionPlanCardGrid.vue");
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
    adminSchoolSubscriptionPlansProcessingMode: {
      type: String,
      default: "frontend"
    },
    useServerProcessing: {
      type: Boolean,
      default: false
    },
    subscriptionPlans: {
      type: [Array, Object],
      default: () => []
    },
    plansCount: {
      type: Number,
      default: 0
    },
    adminSchoolSubscriptionPlansPerPage: {
      type: Number,
      default: 10
    },
    adminSchoolSubscriptionPlansDefaultSort: {
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
    currencies: {
      type: Array,
      default: () => []
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
      localStorage.getItem("admin_view_mode_subscription_plans") || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem(
        "admin_view_mode_subscription_plans",
        value
      );
    });
    const plansList = computed(() => {
      var _a;
      if (Array.isArray(props.subscriptionPlans)) {
        return props.subscriptionPlans;
      }
      if (Array.isArray((_a = props.subscriptionPlans) == null ? void 0 : _a.data)) {
        return props.subscriptionPlans.data;
      }
      return [];
    });
    const localPlans = ref([]);
    watch(
      plansList,
      (newValue) => {
        localPlans.value = JSON.parse(
          JSON.stringify(newValue || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const itemsPerPage = ref(
      props.adminSchoolSubscriptionPlansPerPage || 10
    );
    watch(itemsPerPage, (newValue) => {
      router.put(
        route(
          "admin.settings.updateAdminCountSchoolSubscriptionPlans"
        ),
        {
          value: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.info(
              `Показ ${newValue} элементов на странице.`
            );
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
      props.sortParam || props.adminSchoolSubscriptionPlansDefaultSort || "idDesc"
    );
    const currentPage = ref(1);
    watch(sortParam, (newValue) => {
      currentPage.value = 1;
      router.put(
        route(
          "admin.settings.updateAdminSortSchoolSubscriptionPlans"
        ),
        {
          value: newValue
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            if (props.useServerProcessing) {
              router.get(
                window.location.pathname,
                {
                  ...Object.fromEntries(
                    new URLSearchParams(
                      window.location.search
                    )
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
            toast.info(
              "Сортировка успешно изменена"
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.value) || "Ошибка обновления сортировки."
            );
          }
        }
      );
    });
    const searchQuery = ref(
      props.search || ""
    );
    const stripHtml = (html = "") => {
      return String(html || "").replace(/<\/p>/gi, " ").replace(/<br\s*\/?>/gi, " ").replace(/<[^>]+>/g, "").replace(/&nbsp;/gi, " ").replace(/\s+/g, " ").trim();
    };
    const normalize = (value) => {
      return stripHtml(
        value ?? ""
      ).toString().trim().toLowerCase();
    };
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      if (!value) {
        return 0;
      }
      const time = new Date(value).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const getPlanTitle = (plan) => {
      var _a;
      return ((_a = plan == null ? void 0 : plan.translation) == null ? void 0 : _a.title) || `ID: ${plan == null ? void 0 : plan.id}`;
    };
    const getPlanSubtitle = (plan) => {
      var _a;
      return ((_a = plan == null ? void 0 : plan.translation) == null ? void 0 : _a.subtitle) || "";
    };
    const getPlanShort = (plan) => {
      var _a;
      return ((_a = plan == null ? void 0 : plan.translation) == null ? void 0 : _a.short) || "";
    };
    const getPlanDescription = (plan) => {
      var _a;
      return ((_a = plan == null ? void 0 : plan.translation) == null ? void 0 : _a.description) || "";
    };
    const isAvailableNow = (plan) => {
      const now = Date.now();
      const publishedAt = safeDate(
        plan == null ? void 0 : plan.published_at
      );
      const availableFrom = safeDate(
        plan == null ? void 0 : plan.available_from
      );
      const availableUntil = safeDate(
        plan == null ? void 0 : plan.available_until
      );
      return publishedAt > 0 && (!availableFrom || availableFrom <= now) && (!availableUntil || availableUntil >= now);
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringAsc = (field) => (a, b) => normalize(
      a == null ? void 0 : a[field]
    ).localeCompare(
      normalize(
        b == null ? void 0 : b[field]
      ),
      props.currentLocale
    ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byStringDesc = (field) => (a, b) => normalize(
      b == null ? void 0 : b[field]
    ).localeCompare(
      normalize(
        a == null ? void 0 : a[field]
      ),
      props.currentLocale
    ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortPlans = (items) => {
      const list = (items || []).slice();
      if (sortParam.value === "activity") {
        return list.filter(
          (plan) => Boolean(
            plan.activity
          )
        );
      }
      if (sortParam.value === "inactive") {
        return list.filter(
          (plan) => !plan.activity
        );
      }
      const sortMap = {
        idAsc: byNumberAsc("id"),
        idDesc: byNumberDesc("id"),
        sortAsc: byNumberAsc("sort"),
        sortDesc: byNumberDesc("sort"),
        titleAsc: (a, b) => normalize(
          getPlanTitle(a)
        ).localeCompare(
          normalize(
            getPlanTitle(b)
          ),
          props.currentLocale
        ) || safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        titleDesc: (a, b) => normalize(
          getPlanTitle(b)
        ).localeCompare(
          normalize(
            getPlanTitle(a)
          ),
          props.currentLocale
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        publishedAtAsc: byDateAsc(
          "published_at"
        ),
        publishedAtDesc: byDateDesc(
          "published_at"
        ),
        availabilityNowFirst: (a, b) => Number(
          isAvailableNow(b)
        ) - Number(
          isAvailableNow(a)
        ) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        availableFromAsc: byDateAsc(
          "available_from"
        ),
        availableFromDesc: byDateDesc(
          "available_from"
        ),
        availableUntilAsc: byDateAsc(
          "available_until"
        ),
        availableUntilDesc: byDateDesc(
          "available_until"
        ),
        priceAsc: byNumberAsc("price"),
        priceDesc: byNumberDesc("price"),
        trialDaysAsc: byNumberAsc(
          "trial_days"
        ),
        trialDaysDesc: byNumberDesc(
          "trial_days"
        ),
        billingPeriodAsc: byStringAsc(
          "billing_period"
        ),
        billingPeriodDesc: byStringDesc(
          "billing_period"
        ),
        activityAsc: byNumberAsc(
          "activity"
        ),
        activityDesc: byNumberDesc(
          "activity"
        ),
        autoRenewAsc: byNumberAsc(
          "auto_renew"
        ),
        autoRenewDesc: byNumberDesc(
          "auto_renew"
        ),
        imagesAsc: byNumberAsc(
          "images_count"
        ),
        imagesDesc: byNumberDesc(
          "images_count"
        ),
        createdAtAsc: byDateAsc(
          "created_at"
        ),
        createdAtDesc: byDateDesc(
          "created_at"
        ),
        updatedAtAsc: byDateAsc(
          "updated_at"
        ),
        updatedAtDesc: byDateDesc(
          "updated_at"
        )
      };
      return sortMap[sortParam.value] ? list.sort(
        sortMap[sortParam.value]
      ) : list;
    };
    const filteredPlans = computed(() => {
      let filtered = localPlans.value || [];
      const query = normalize(
        searchQuery.value
      );
      if (!query) {
        return sortPlans(
          filtered
        );
      }
      filtered = filtered.filter(
        (plan) => {
          var _a, _b, _c;
          const values = [
            /**
             * Основная сущность.
             */
            plan == null ? void 0 : plan.id,
            plan == null ? void 0 : plan.slug,
            plan == null ? void 0 : plan.billing_period,
            plan == null ? void 0 : plan.provider,
            plan == null ? void 0 : plan.provider_ref,
            plan == null ? void 0 : plan.price,
            /**
             * Current locale translation.
             */
            getPlanTitle(plan),
            getPlanSubtitle(plan),
            getPlanShort(plan),
            getPlanDescription(plan),
            /**
             * Currency.
             */
            (_a = plan == null ? void 0 : plan.currency) == null ? void 0 : _a.code,
            (_b = plan == null ? void 0 : plan.currency) == null ? void 0 : _b.name,
            (_c = plan == null ? void 0 : plan.currency) == null ? void 0 : _c.symbol
          ];
          return values.some(
            (value) => normalize(
              value
            ).includes(
              query
            )
          );
        }
      );
      return sortPlans(
        filtered
      );
    });
    const paginatedPlans = computed(() => {
      const per = Number(
        itemsPerPage.value || 10
      );
      const start = (currentPage.value - 1) * per;
      return filteredPlans.value.slice(
        start,
        start + per
      );
    });
    const displayedPlans = computed(() => {
      return props.useServerProcessing ? plansList.value : paginatedPlans.value;
    });
    watch(
      [
        itemsPerPage,
        searchQuery
      ],
      () => {
        currentPage.value = 1;
      }
    );
    const showConfirmDeleteModal = ref(false);
    const planToDeleteId = ref(null);
    const planToDeleteTitle = ref("");
    const confirmDelete = (planOrId, title = null) => {
      if (typeof planOrId === "object") {
        planToDeleteId.value = planOrId.id;
        planToDeleteTitle.value = title || getPlanTitle(
          planOrId
        );
      } else {
        planToDeleteId.value = planOrId;
        planToDeleteTitle.value = title || `ID: ${planOrId}`;
      }
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      planToDeleteId.value = null;
      planToDeleteTitle.value = "";
    };
    const deletePlan = () => {
      if (planToDeleteId.value === null) {
        return;
      }
      const idToDelete = planToDeleteId.value;
      const titleToDelete = planToDeleteTitle.value;
      router.delete(
        route(
          "admin.schoolSubscriptionPlans.destroy",
          {
            schoolSubscriptionPlan: idToDelete
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => {
            toast.success(
              `Тариф "${titleToDelete || `ID: ${idToDelete}`}" удалён.`
            );
          },
          onError: (errors) => {
            const errorKey = Object.keys(
              errors || {}
            )[0];
            const errorMsg = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[errorKey]) || "Произошла ошибка при удалении.";
            toast.error(
              `${errorMsg} (Тариф: ${titleToDelete || `ID: ${idToDelete}`})`
            );
          },
          onFinish: () => {
            closeModal();
          }
        }
      );
    };
    const patchPlan = (planId, payload) => {
      const index = localPlans.value.findIndex(
        (plan) => Number(plan.id) === Number(planId)
      );
      if (index === -1) {
        return;
      }
      localPlans.value[index] = {
        ...localPlans.value[index],
        ...payload
      };
    };
    const selectedPlans = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedPlans.value.map(
        (plan) => plan.id
      );
      if (checked) {
        selectedPlans.value = [
          .../* @__PURE__ */ new Set([
            ...selectedPlans.value,
            ...ids
          ])
        ];
        return;
      }
      selectedPlans.value = selectedPlans.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectPlan = (id) => {
      const index = selectedPlans.value.indexOf(
        id
      );
      if (index > -1) {
        selectedPlans.value.splice(
          index,
          1
        );
        return;
      }
      selectedPlans.value.push(id);
    };
    const handleSortOrderUpdate = (orderedIds) => {
      const startSort = (currentPage.value - 1) * itemsPerPage.value;
      const items = orderedIds.map(
        (id, index) => ({
          id,
          sort: startSort + index + 1
        })
      );
      if (!items.length) {
        return;
      }
      router.put(
        route(
          "admin.actions.schoolSubscriptionPlans.updateSortBulk"
        ),
        {
          items
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            toast.success(
              "Порядок тарифов успешно обновлён."
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.message) || (errors == null ? void 0 : errors.general) || "Не удалось обновить порядок тарифов."
            );
            router.reload({
              only: [
                "subscriptionPlans"
              ],
              preserveScroll: true
            });
          }
        }
      );
    };
    const bulkToggleActivity = (newActivity) => {
      if (!selectedPlans.value.length) {
        toast.warning(
          "Выберите тарифы для активации/деактивации."
        );
        return;
      }
      const idsToUpdate = [
        ...selectedPlans.value
      ];
      router.put(
        route(
          "admin.actions.schoolSubscriptionPlans.bulkUpdateActivity"
        ),
        {
          ids: idsToUpdate,
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            idsToUpdate.forEach(
              (id) => patchPlan(
                id,
                {
                  activity: newActivity
                }
              )
            );
            selectedPlans.value = [];
            toast.success(
              "Активность выбранных тарифов обновлена."
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
        toggleAll({
          checked: true
        });
      } else if (action === "deselectAll") {
        toggleAll({
          checked: false
        });
      } else if (action === "activate") {
        bulkToggleActivity(
          true
        );
      } else if (action === "deactivate") {
        bulkToggleActivity(
          false
        );
      }
      event.target.value = "";
    };
    const toggleActivity = (plan) => {
      const newActivity = !plan.activity;
      const planTitle = getPlanTitle(plan);
      const actionText = newActivity ? t("activated") : t("deactivated");
      router.put(
        route(
          "admin.actions.schoolSubscriptionPlans.updateActivity",
          {
            schoolSubscriptionPlan: plan.id
          }
        ),
        {
          activity: newActivity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            patchPlan(
              plan.id,
              {
                activity: newActivity
              }
            );
            plan.activity = newActivity;
            toast.success(
              `Тариф "${planTitle}" ${actionText}.`
            );
          },
          onError: (errors) => {
            toast.error(
              (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || `Ошибка изменения активности для тарифа "${planTitle}".`
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("subscriptionPlans")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("subscriptionPlans"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("subscriptionPlans")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("subscriptionPlans")), 1)
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
              href: _ctx.route("admin.schoolSubscriptionPlans.create")
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
                  _push3(` ${ssrInterpolate(unref(t)("addSubscriptionPlan"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addSubscriptionPlan")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$9, {
              "setting-key": "adminSchoolSubscriptionPlansProcessingMode",
              mode: __props.adminSchoolSubscriptionPlansProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.plansCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.plansCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.plansCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.plansCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3 gap-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountSchoolSubscriptionPlans"
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
            if (__props.plansCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.plansCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.plansCount), 1)
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
            if (__props.plansCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredPlans.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.subscriptionPlans }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                "subscription-plans": displayedPlans.value,
                "selected-plans": selectedPlans.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectPlan,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                "subscription-plans": displayedPlans.value,
                "selected-plans": selectedPlans.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onUpdateSortOrder: handleSortOrderUpdate,
                onToggleSelect: toggleSelectPlan,
                onToggleAll: toggleAll
              }, null, _parent2, _scopeId));
            }
            if (__props.plansCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredPlans.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.subscriptionPlans }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$i, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deletePlan,
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
                      href: _ctx.route("admin.schoolSubscriptionPlans.create")
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
                        createTextVNode(" " + toDisplayString(unref(t)("addSubscriptionPlan")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$9, {
                      "setting-key": "adminSchoolSubscriptionPlansProcessingMode",
                      mode: __props.adminSchoolSubscriptionPlansProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.plansCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.plansCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.plansCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.plansCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3 gap-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$c, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$d, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountSchoolSubscriptionPlans"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (value) => sortParam.value = value
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.plansCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.plansCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$4, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.plansCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredPlans.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.subscriptionPlans
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    "subscription-plans": displayedPlans.value,
                    "selected-plans": selectedPlans.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectPlan,
                    onToggleAll: toggleAll
                  }, null, 8, ["subscription-plans", "selected-plans"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    "subscription-plans": displayedPlans.value,
                    "selected-plans": selectedPlans.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onUpdateSortOrder: handleSortOrderUpdate,
                    onToggleSelect: toggleSelectPlan,
                    onToggleAll: toggleAll
                  }, null, 8, ["subscription-plans", "selected-plans"])),
                  __props.plansCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredPlans.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.subscriptionPlans
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deletePlan,
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolSubscriptionPlans/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
