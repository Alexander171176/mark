import { mergeProps, unref, useSSRContext, computed, watch, onMounted, onUnmounted, ref, withCtx, createTextVNode, toDisplayString, createVNode, createBlock, createCommentVNode, openBlock } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrIncludeBooleanAttr, ssrRenderList, ssrRenderClass, ssrRenderComponent, ssrRenderTeleport, ssrRenderStyle } from "vue/server-renderer";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import axios from "axios";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$i } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$c, a as _sfc_main$f, b as _sfc_main$g } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$b, a as _sfc_main$h } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$d } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$a } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$e } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$9 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$7 } from "./ActivityToggle-B1-nFMYK.js";
import { _ as _sfc_main$8 } from "./DeleteIconButton-DLv2Mr1x.js";
import { _ as _sfc_main$6 } from "./ModerationButton-D_ehimPY.js";
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
const _sfc_main$5 = {
  __name: "BulkActionSelect",
  __ssrInlineRender: true,
  emits: ["change"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-col sm:flex-row items-center" }, _attrs))}><label class="block mb-2 sm:mb-0 sm:mr-2 font-semibold text-sm text-slate-700 dark:text-slate-300">${ssrInterpolate(unref(t)("bulkActions"))}</label><select class="w-auto px-3 py-0.5 form-select rounded-sm shadow-sm bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600"><option value="">— ${ssrInterpolate(unref(t)("selectAction"))} —</option><option value="selectAll">${ssrInterpolate(unref(t)("selectAll"))}</option><option value="deselectAll">${ssrInterpolate(unref(t)("deselectAll"))}</option><option disabled>──────────────────</option><option value="activate">${ssrInterpolate(unref(t)("activate"))}</option><option value="deactivate">${ssrInterpolate(unref(t)("deactivate"))}</option><option disabled>──────────────────</option><option value="delete">${ssrInterpolate(unref(t)("deleteSelected"))}</option><option disabled>──────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Review/Select/BulkActionSelect.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: {
      type: String,
      default: "idDesc"
    }
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">ID ↓</option><option value="idAsc">ID ↑</option><option disabled>─────────────────</option><option value="authorNameAsc">${ssrInterpolate(unref(t)("user"))} A→Z</option><option value="authorNameDesc">${ssrInterpolate(unref(t)("user"))} Z→A</option><option value="authorEmailAsc">Email A→Z</option><option value="authorEmailDesc">Email Z→A</option><option disabled>─────────────────</option><option value="ratingDesc">${ssrInterpolate(unref(t)("rating"))} 5→1</option><option value="ratingAsc">${ssrInterpolate(unref(t)("rating"))} 1→5</option><option value="likesDesc">${ssrInterpolate(unref(t)("likes"))} 9→0</option><option value="likesAsc">${ssrInterpolate(unref(t)("likes"))} 0→9</option><option value="imagesDesc">${ssrInterpolate(unref(t)("images"))} 9→0</option><option value="imagesAsc">${ssrInterpolate(unref(t)("images"))} 0→9</option><option disabled>─────────────────</option><option value="commentAsc">${ssrInterpolate(unref(t)("reviews"))} A→Z</option><option value="commentDesc">${ssrInterpolate(unref(t)("reviews"))} Z→A</option><option value="reviewableTypeAsc">${ssrInterpolate(unref(t)("type"))} A→Z</option><option value="reviewableTypeDesc">${ssrInterpolate(unref(t)("type"))} Z→A</option><option disabled>─────────────────</option><option value="verifiedDesc">${ssrInterpolate(unref(t)("verified"))} ON→OFF</option><option value="verifiedAsc">${ssrInterpolate(unref(t)("verified"))} OFF→ON</option><option value="verified">${ssrInterpolate(unref(t)("verified"))}</option><option value="notVerified">${ssrInterpolate(unref(t)("notVerified"))}</option><option disabled>─────────────────</option><option value="replyDesc">${ssrInterpolate(unref(t)("reply"))} ON→OFF</option><option value="replyAsc">${ssrInterpolate(unref(t)("reply"))} OFF→ON</option><option value="hasReply">${ssrInterpolate(unref(t)("hasReply"))}</option><option value="noReply">${ssrInterpolate(unref(t)("noReply"))}</option><option disabled>─────────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────────</option><option value="activityDesc">${ssrInterpolate(unref(t)("activity"))} ON→OFF</option><option value="activityAsc">${ssrInterpolate(unref(t)("activity"))} OFF→ON</option><option value="activity">${ssrInterpolate(unref(t)("active"))}</option><option value="inactive">${ssrInterpolate(unref(t)("inactive"))}</option><option disabled>─────────────────</option><option value="moderationPending">${ssrInterpolate(unref(t)("underModeration"))}</option><option value="moderationApproved">${ssrInterpolate(unref(t)("statusSelectApproved"))}</option><option value="moderationRejected">${ssrInterpolate(unref(t)("statusSelectRejected"))}</option><option value="moderationStatusAsc">${ssrInterpolate(unref(t)("moderationStatus"))} 0→2</option><option value="moderationStatusDesc">${ssrInterpolate(unref(t)("moderationStatus"))} 2→0</option><option disabled>─────────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Review/Sort/SortSelect.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "ReviewTable",
  __ssrInlineRender: true,
  props: {
    reviews: { type: Array, default: () => [] },
    selectedReviews: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
    "toggle-activity",
    "delete",
    "toggle-select",
    "toggle-all",
    "view-details",
    "approve-review"
  ],
  setup(__props, { emit: __emit }) {
    const { t, locale } = useI18n();
    const props = __props;
    const emit = __emit;
    const displayedIds = computed(() => {
      return props.reviews.map((review) => review.id);
    });
    const allDisplayedSelected = computed(() => {
      return displayedIds.value.length > 0 && displayedIds.value.every((id) => props.selectedReviews.includes(id));
    });
    const formatDate = (value) => {
      if (!value) return "—";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return "—";
      return new Intl.DateTimeFormat(locale.value || "ru-RU", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
      }).format(date);
    };
    const truncate = (value, maxLength = 160) => {
      const text = String(value || "").trim();
      if (!text) return "—";
      if (text.length <= maxLength) return text;
      return `${text.slice(0, maxLength)}…`;
    };
    const reviewableType = (review) => {
      var _a;
      return ((_a = review == null ? void 0 : review.reviewable) == null ? void 0 : _a.type) || (review == null ? void 0 : review.reviewable_type) || "—";
    };
    const reviewableId = (review) => {
      var _a;
      return ((_a = review == null ? void 0 : review.reviewable) == null ? void 0 : _a.id) ?? (review == null ? void 0 : review.reviewable_id) ?? "—";
    };
    const reviewableTitle = (review) => {
      var _a;
      return ((_a = review == null ? void 0 : review.reviewable) == null ? void 0 : _a.title) || "—";
    };
    const reviewableArticle = (review) => {
      var _a, _b;
      return ((_a = review == null ? void 0 : review.reviewable) == null ? void 0 : _a.sku) || ((_b = review == null ? void 0 : review.reviewable) == null ? void 0 : _b.vendor_code) || "";
    };
    const reviewableBarcode = (review) => {
      var _a;
      return ((_a = review == null ? void 0 : review.reviewable) == null ? void 0 : _a.barcode) || "";
    };
    const hasReply = (review) => {
      return Boolean(
        (review == null ? void 0 : review.has_reply) || String((review == null ? void 0 : review.reply) || "").trim()
      );
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
    const verifiedBadgeClass = (verified) => {
      return verified ? "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300" : "bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-700 dark:text-slate-300";
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedReviews.length)}</div>`);
      if (__props.reviews.length) {
        _push(`<label class="flex items-center text-xs text-slate-600 dark:text-slate-200 cursor-pointer"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allDisplayedSelected.value) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div><div class="overflow-x-auto">`);
      if (__props.reviews.length) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px"><div class="font-medium text-center">${ssrInterpolate(unref(t)("id"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-left">${ssrInterpolate(unref(t)("owner"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 min-w-72"><div class="font-medium text-left">${ssrInterpolate(unref(t)("review"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 min-w-52"><div class="font-medium text-center">${ssrInterpolate(unref(t)("object"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("status"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-medium text-center">${ssrInterpolate(unref(t)("actions"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap w-px"><div class="font-semibold text-center"><input type="checkbox"${ssrIncludeBooleanAttr(allDisplayedSelected.value) ? " checked" : ""}></div></th></tr></thead><tbody><!--[-->`);
        ssrRenderList(__props.reviews, (review) => {
          var _a, _b;
          _push(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"><div class="text-center"${ssrRenderAttr("title", `${formatDate(review.created_at)} / ${formatDate(review.updated_at)}`)}>${ssrInterpolate(review.id)}</div></td><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"><div class="flex flex-col"><div class="font-semibold text-blue-700 dark:text-blue-300">${ssrInterpolate(((_a = review.author) == null ? void 0 : _a.name) || "—")}</div>`);
          if ((_b = review.author) == null ? void 0 : _b.email) {
            _push(`<div class="text-[10px] text-gray-500 dark:text-gray-300 break-all font-semibold">${ssrInterpolate(review.author.email)}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="text-xs text-slate-500 dark:text-slate-300"> ID: ${ssrInterpolate(review.user_id)}</div></div></td><td class="px-2 first:pl-5 last:pr-5 py-2"><button type="button" class="block w-full text-left"${ssrRenderAttr("title", review.comment || "")}><span class="flex flex-wrap items-center gap-2 mb-1"><span class="inline-flex items-center rounded-sm border border-amber-300 bg-amber-100 px-2 py-0 text-xs font-bold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300"> ★ ${ssrInterpolate(Number(review.rating || 0))}/5 </span><span class="${ssrRenderClass([verifiedBadgeClass(review.verified), "inline-flex items-center rounded-sm border px-2 py-0.5 text-[10px] font-semibold"])}">${ssrInterpolate(review.verified ? unref(t)("verified") : unref(t)("notVerified"))}</span><span class="text-[10px] text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("likes"))}: ${ssrInterpolate(Number(review.likes || 0))}</span><span class="text-[10px] text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(Number(review.images_count || 0))}</span></span><div class="text-xs text-slate-600 dark:text-slate-200 whitespace-normal break-words">${ssrInterpolate(truncate(review.comment))}</div><div class="${ssrRenderClass([hasReply(review) ? "text-emerald-700 dark:text-emerald-300" : "text-slate-400 dark:text-slate-400", "mt-1 text-[10px] font-semibold"])}">${ssrInterpolate(hasReply(review) ? unref(t)("hasReply") : unref(t)("noReply"))}</div></button></td><td class="px-2 first:pl-5 last:pr-5 py-2"><div class="flex flex-wrap items-center justify-center gap-1 text-xs font-semibold text-violet-700 dark:text-violet-300"><span>${ssrInterpolate(reviewableType(review))}</span><span>${ssrInterpolate(unref(t)("id"))}: ${ssrInterpolate(reviewableId(review))}</span></div><div class="mt-1 text-center text-xs text-fuchsia-700 dark:text-fuchsia-300 whitespace-normal break-words line-clamp-2"${ssrRenderAttr("title", reviewableTitle(review))}>${ssrInterpolate(reviewableTitle(review))}</div>`);
          if (reviewableArticle(review)) {
            _push(`<div class="mt-1 text-center text-[10px] text-slate-500 dark:text-slate-300"> SKU: ${ssrInterpolate(reviewableArticle(review))}</div>`);
          } else {
            _push(`<!---->`);
          }
          if (reviewableBarcode(review)) {
            _push(`<div class="mt-0.5 text-center text-[10px] text-slate-400 dark:text-slate-400">${ssrInterpolate(reviewableBarcode(review))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</td><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"><div class="flex items-center justify-center gap-1"><span class="${ssrRenderClass([moderationBadge(review.moderation_status).class, "text-[10px] px-2 py-1 rounded-sm border font-semibold"])}"${ssrRenderAttr("title", review.moderation_note ? `${review.moderation_note}${review.moderated_at ? ` [${formatDate(review.moderated_at)}]` : ""}` : null)}>${ssrInterpolate(moderationBadge(review.moderation_status).text)}</span>`);
          _push(ssrRenderComponent(_sfc_main$6, {
            isAdmin: __props.isAdmin,
            status: review.moderation_status ?? 0,
            initialNote: review.moderation_note || "",
            mode: "toggle",
            onSubmit: ({ status, note }) => emit("approve-review", review, status, note)
          }, null, _parent));
          _push(`</div></td><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"><div class="flex items-center justify-center gap-2"><button type="button"${ssrRenderAttr("title", unref(t)("view"))} class="flex items-center py-1 px-0 rounded border border-slate-300 hover:border-blue-500 dark:border-blue-300 dark:hover:border-blue-100"><svg class="w-4 h-4 shrink-0 fill-current text-blue-500 mx-1" viewBox="0 0 16 16"><path d="M5 9h11v2H5V9zM0 9h3v2H0V9zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z"></path></svg></button>`);
          _push(ssrRenderComponent(_sfc_main$7, {
            isActive: review.activity,
            title: review.activity ? unref(t)("enabled") : unref(t)("disabled"),
            onToggleActivity: ($event) => emit("toggle-activity", review)
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$8, {
            title: unref(t)("delete"),
            onDelete: ($event) => emit("delete", review.id)
          }, null, _parent));
          _push(`</div></td><td class="px-2 first:pl-5 last:pr-5 py-2 whitespace-nowrap"><div class="text-center"><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedReviews.includes(review.id)) ? " checked" : ""}></div></td></tr>`);
        });
        _push(`<!--]--></tbody></table>`);
      } else {
        _push(`<div class="py-6 text-center text-sm text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Review/Table/ReviewTable.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "ReviewCardGrid",
  __ssrInlineRender: true,
  props: {
    reviews: { type: Array, default: () => [] },
    selectedReviews: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false }
  },
  emits: [
    "toggle-activity",
    "delete",
    "toggle-select",
    "toggle-all",
    "view-details",
    "approve-review"
  ],
  setup(__props, { emit: __emit }) {
    const { t, locale } = useI18n();
    const props = __props;
    const emit = __emit;
    const displayedIds = computed(() => {
      return props.reviews.map((review) => review.id);
    });
    const allDisplayedSelected = computed(() => {
      return displayedIds.value.length > 0 && displayedIds.value.every((id) => props.selectedReviews.includes(id));
    });
    const formatDate = (value) => {
      if (!value) return "—";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) return "—";
      return new Intl.DateTimeFormat(locale.value || "ru-RU", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
      }).format(date);
    };
    const authorName = (review) => {
      var _a;
      return ((_a = review == null ? void 0 : review.author) == null ? void 0 : _a.name) || "—";
    };
    const authorEmail = (review) => {
      var _a;
      return ((_a = review == null ? void 0 : review.author) == null ? void 0 : _a.email) || "";
    };
    const reviewableType = (review) => {
      var _a;
      return ((_a = review == null ? void 0 : review.reviewable) == null ? void 0 : _a.type) || (review == null ? void 0 : review.reviewable_type) || "—";
    };
    const reviewableId = (review) => {
      var _a;
      return ((_a = review == null ? void 0 : review.reviewable) == null ? void 0 : _a.id) ?? (review == null ? void 0 : review.reviewable_id) ?? "—";
    };
    const reviewableTitle = (review) => {
      var _a;
      return ((_a = review == null ? void 0 : review.reviewable) == null ? void 0 : _a.title) || "—";
    };
    const reviewableArticle = (review) => {
      var _a, _b;
      return ((_a = review == null ? void 0 : review.reviewable) == null ? void 0 : _a.sku) || ((_b = review == null ? void 0 : review.reviewable) == null ? void 0 : _b.vendor_code) || "";
    };
    const reviewableBarcode = (review) => {
      var _a;
      return ((_a = review == null ? void 0 : review.reviewable) == null ? void 0 : _a.barcode) || "";
    };
    const reviewText = (review) => {
      return (review == null ? void 0 : review.comment) || (review == null ? void 0 : review.advantages) || (review == null ? void 0 : review.disadvantages) || "—";
    };
    const hasReply = (review) => {
      return Boolean(
        (review == null ? void 0 : review.has_reply) || String((review == null ? void 0 : review.reply) || "").trim()
      );
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
    const verifiedBadge = (verified) => {
      return verified ? {
        text: t("verified"),
        class: "bg-sky-100 text-sky-700 border-sky-300 dark:bg-sky-900/40 dark:text-sky-300"
      } : {
        text: t("notVerified"),
        class: "bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-700 dark:text-slate-300"
      };
    };
    const normalizedRating = (review) => {
      return Math.min(
        5,
        Math.max(
          0,
          Number((review == null ? void 0 : review.rating) || 0)
        )
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative rounded-sm border border-slate-400 bg-white shadow-lg dark:border-slate-500 dark:bg-slate-700" }, _attrs))}><div class="flex items-center justify-between border-b border-slate-400 px-3 py-2 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("selected"))}: ${ssrInterpolate(__props.selectedReviews.length)}</div>`);
      if (__props.reviews.length) {
        _push(`<label class="flex cursor-pointer items-center text-xs text-slate-600 dark:text-slate-200"><span>${ssrInterpolate(unref(t)("selectAll"))}</span><input type="checkbox" class="mx-2"${ssrIncludeBooleanAttr(allDisplayedSelected.value) ? " checked" : ""}></label>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (__props.reviews.length) {
        _push(`<div class="p-3"><div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4"><!--[-->`);
        ssrRenderList(__props.reviews, (review) => {
          _push(`<article class="relative flex h-full flex-col rounded-md border border-slate-400 bg-slate-50/70 shadow-sm transition-shadow duration-150 hover:shadow-md dark:border-slate-500 dark:bg-slate-800/80"><header class="flex items-center justify-between border-b border-dashed border-slate-400 px-2 py-1 dark:border-slate-500"><div class="rounded-sm border border-gray-400 bg-slate-200 px-1.5 py-0.5 text-[10px] font-semibold text-slate-800 dark:bg-slate-700 dark:text-blue-100"${ssrRenderAttr("title", `${formatDate(review.created_at)} / ${formatDate(review.updated_at)}`)}> ID: ${ssrInterpolate(review.id)}</div><input type="checkbox"${ssrIncludeBooleanAttr(__props.selectedReviews.includes(review.id)) ? " checked" : ""}></header><div class="flex flex-1 flex-col space-y-2 px-3 py-2"><div class="flex flex-col items-center justify-center"><div class="break-words text-center font-semibold text-blue-700 dark:text-blue-300">${ssrInterpolate(authorName(review))}</div>`);
          if (authorEmail(review)) {
            _push(`<div class="break-all text-center text-[10px] font-semibold text-gray-500 dark:text-gray-300">${ssrInterpolate(authorEmail(review))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="text-xs font-semibold text-slate-700 dark:text-slate-300"> ID: ${ssrInterpolate(review.user_id)}</div></div><div class="flex items-center justify-center gap-1"${ssrRenderAttr("title", `${unref(t)("rating")}: ${normalizedRating(review)}`)}><!--[-->`);
          ssrRenderList(5, (star) => {
            _push(`<span class="${ssrRenderClass([star <= normalizedRating(review) ? "text-amber-500" : "text-slate-300 dark:text-slate-600", "text-base leading-none"])}"> ★ </span>`);
          });
          _push(`<!--]--><span class="ml-1 text-xs font-semibold text-slate-600 dark:text-slate-300">${ssrInterpolate(normalizedRating(review))}/5 </span></div><button type="button" class="block w-full text-left"${ssrRenderAttr("title", reviewText(review))}><span class="line-clamp-4 text-xs font-semibold text-slate-600 dark:text-slate-200">${ssrInterpolate(reviewText(review))}</span></button><div class="space-y-0.5 text-center"><div class="flex flex-wrap items-center justify-center gap-2 text-xs font-semibold text-indigo-700 dark:text-indigo-300"><span>${ssrInterpolate(reviewableType(review))}</span><span>${ssrInterpolate(unref(t)("id"))}: ${ssrInterpolate(reviewableId(review))}</span></div><div class="line-clamp-2 text-xs font-semibold text-fuchsia-700 dark:text-fuchsia-300"${ssrRenderAttr("title", reviewableTitle(review))}>${ssrInterpolate(reviewableTitle(review))}</div>`);
          if (reviewableArticle(review)) {
            _push(`<div class="text-[10px] font-semibold text-slate-500 dark:text-slate-300"> SKU: ${ssrInterpolate(reviewableArticle(review))}</div>`);
          } else {
            _push(`<!---->`);
          }
          if (reviewableBarcode(review)) {
            _push(`<div class="text-[10px] text-slate-400 dark:text-slate-400">${ssrInterpolate(reviewableBarcode(review))}</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div><div class="flex flex-wrap items-center justify-center gap-1"><span class="${ssrRenderClass([verifiedBadge(review.verified).class, "rounded-sm border px-2 py-1 text-[10px] font-semibold"])}">${ssrInterpolate(verifiedBadge(review.verified).text)}</span><span class="rounded-sm border border-violet-300 bg-violet-100 px-2 py-1 text-[10px] font-semibold text-violet-700 dark:bg-violet-900/40 dark:text-violet-300">${ssrInterpolate(unref(t)("likes"))}: ${ssrInterpolate(Number(review.likes || 0))}</span><span class="rounded-sm border border-cyan-300 bg-cyan-100 px-2 py-1 text-[10px] font-semibold text-cyan-700 dark:bg-cyan-900/40 dark:text-cyan-300">${ssrInterpolate(unref(t)("images"))}: ${ssrInterpolate(Number(review.images_count || 0))}</span></div><div class="flex justify-center"><span class="${ssrRenderClass([hasReply(review) ? "border-emerald-300 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300" : "border-slate-300 bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300", "rounded-sm border px-2 py-1 text-[10px] font-semibold"])}">${ssrInterpolate(hasReply(review) ? unref(t)("hasReply") : unref(t)("noReply"))}</span></div><div class="flex items-center justify-center"><span class="text-[10px] font-semibold text-slate-600 dark:text-slate-400"${ssrRenderAttr("title", formatDate(review.created_at))}>${ssrInterpolate(formatDate(review.updated_at || review.created_at))}</span></div><div class="flex justify-center pt-1"><div class="flex items-center justify-center gap-1"><span class="${ssrRenderClass([moderationBadge(review.moderation_status).class, "rounded-sm border px-2 py-1 text-[10px] font-semibold"])}"${ssrRenderAttr("title", review.moderation_note ? `${review.moderation_note}${review.moderated_at ? ` [${formatDate(review.moderated_at)}]` : ""}` : null)}>${ssrInterpolate(moderationBadge(review.moderation_status).text)}</span>`);
          _push(ssrRenderComponent(_sfc_main$6, {
            isAdmin: __props.isAdmin,
            status: review.moderation_status ?? 0,
            initialNote: review.moderation_note || "",
            mode: "toggle",
            onSubmit: ({ status, note }) => emit("approve-review", review, status, note)
          }, null, _parent));
          _push(`</div></div></div><footer class="border-t border-dashed border-slate-400 px-3 py-2 dark:border-slate-500"><div class="flex items-center justify-between gap-2"><button type="button" class="flex items-center rounded border border-slate-300 px-0 py-1 hover:border-blue-500 dark:border-blue-300 dark:hover:border-blue-100"${ssrRenderAttr("title", unref(t)("view"))}><svg class="mx-1 h-4 w-4 shrink-0 fill-current text-blue-500" viewBox="0 0 16 16"><path d="M5 9h11v2H5V9zM0 9h3v2H0v-2zm5 4h6v2H5v-2zm-5 0h3v2H0v-2zm5-8h7v2H5V5zM0 5h3v2H0V5zm5-4h11v2H5V1zM0 1h3v2H0V1z"></path></svg></button><div class="flex items-center gap-1">`);
          _push(ssrRenderComponent(_sfc_main$7, {
            isActive: review.activity,
            title: review.activity ? unref(t)("enabled") : unref(t)("disabled"),
            onToggleActivity: ($event) => emit("toggle-activity", review)
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$8, {
            title: unref(t)("delete"),
            onDelete: ($event) => emit("delete", review.id)
          }, null, _parent));
          _push(`</div></div></footer></article>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Review/View/ReviewCardGrid.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "ReviewDetailsModal",
  __ssrInlineRender: true,
  props: {
    show: {
      type: Boolean,
      default: false
    },
    review: {
      type: Object,
      default: null
    }
  },
  emits: ["close"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const closeModal = () => {
      emit("close");
    };
    const closeOnEscape = (event) => {
      if (event.key === "Escape" && props.show) {
        closeModal();
      }
    };
    watch(
      () => props.show,
      (show) => {
        document.body.style.overflow = show ? "hidden" : "";
      }
    );
    onMounted(() => {
      document.addEventListener("keydown", closeOnEscape);
    });
    onUnmounted(() => {
      document.removeEventListener("keydown", closeOnEscape);
      document.body.style.overflow = "";
    });
    const formatDate = (dateString) => {
      if (!dateString) return "—";
      const date = new Date(dateString);
      if (Number.isNaN(date.getTime())) {
        return "—";
      }
      return new Intl.DateTimeFormat("ru-RU", {
        day: "numeric",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
      }).format(date);
    };
    const reviewableType = computed(() => {
      var _a, _b, _c;
      return ((_b = (_a = props.review) == null ? void 0 : _a.reviewable) == null ? void 0 : _b.type) || ((_c = props.review) == null ? void 0 : _c.reviewable_type) || "—";
    });
    const reviewableTitle = computed(() => {
      var _a, _b, _c, _d, _e, _f;
      return ((_b = (_a = props.review) == null ? void 0 : _a.reviewable) == null ? void 0 : _b.title) || ((_d = (_c = props.review) == null ? void 0 : _c.reviewable) == null ? void 0 : _d.name) || ((_f = (_e = props.review) == null ? void 0 : _e.reviewable) == null ? void 0 : _f.url) || "—";
    });
    const images = computed(() => {
      var _a;
      return Array.isArray((_a = props.review) == null ? void 0 : _a.images) ? props.review.images : [];
    });
    const hasReply = computed(() => {
      var _a, _b;
      return Boolean(
        ((_a = props.review) == null ? void 0 : _a.has_reply) || String(((_b = props.review) == null ? void 0 : _b.reply) || "").trim()
      );
    });
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
    const activityBadge = (activity) => {
      return activity ? {
        text: t("active"),
        class: "bg-emerald-100 text-emerald-700 border-emerald-300 dark:bg-emerald-900/40 dark:text-emerald-300"
      } : {
        text: t("inactive"),
        class: "bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-700 dark:text-slate-300"
      };
    };
    const verifiedBadge = (verified) => {
      return verified ? {
        text: t("verified"),
        class: "bg-blue-100 text-blue-700 border-blue-300 dark:bg-blue-900/40 dark:text-blue-300"
      } : {
        text: t("notVerified"),
        class: "bg-slate-100 text-slate-600 border-slate-300 dark:bg-slate-700 dark:text-slate-300"
      };
    };
    const imageUrl = (image) => {
      return (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.url) || (image == null ? void 0 : image.thumb_url) || "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      ssrRenderTeleport(_push, (_push2) => {
        var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l, _m, _n;
        _push2(`<div style="${ssrRenderStyle(__props.show ? null : { display: "none" })}" class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto px-3 py-6" scroll-region role="dialog" aria-modal="true"><div style="${ssrRenderStyle(__props.show ? null : { display: "none" })}" class="fixed inset-0 transform transition-all"><div class="absolute inset-0 bg-slate-900/50"></div></div><div style="${ssrRenderStyle(__props.show ? null : { display: "none" })}" class="relative w-full max-w-4xl max-h-[90vh] overflow-y-auto rounded-lg border border-gray-400 bg-slate-100 dark:bg-slate-900 shadow-xl transform transition-all"><button type="button" class="absolute top-1 right-1 z-10 rounded-sm p-1 text-gray-400 hover:text-red-500 dark:text-gray-300 dark:hover:text-red-300"${ssrRenderAttr("title", unref(t)("close"))}><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg></button><div class="px-4 py-3 sm:px-6"><h3 class="pr-8 pb-2 text-center text-base font-semibold text-sky-700 dark:text-sky-300 border-b border-dashed border-slate-400">${ssrInterpolate(unref(t)("reviewDetails"))} — ${ssrInterpolate(unref(t)("id"))}: ${ssrInterpolate((_a = __props.review) == null ? void 0 : _a.id)}</h3>`);
        if (__props.review) {
          _push2(`<!--[--><div class="my-4 flex flex-wrap items-center justify-center gap-2"><span class="${ssrRenderClass([moderationBadge(__props.review.moderation_status).class, "rounded-sm border px-3 py-1 text-xs font-semibold"])}"${ssrRenderAttr("title", __props.review.moderation_note ? `${__props.review.moderation_note}${__props.review.moderated_at ? ` [${formatDate(__props.review.moderated_at)}]` : ""}` : null)}>${ssrInterpolate(moderationBadge(__props.review.moderation_status).text)}</span><span class="${ssrRenderClass([activityBadge(__props.review.activity).class, "rounded-sm border px-3 py-1 text-xs font-semibold"])}">${ssrInterpolate(activityBadge(__props.review.activity).text)}</span><span class="${ssrRenderClass([verifiedBadge(__props.review.verified).class, "rounded-sm border px-3 py-1 text-xs font-semibold"])}">${ssrInterpolate(verifiedBadge(__props.review.verified).text)}</span><span class="rounded-sm border border-amber-300 bg-amber-100 px-3 py-1 text-xs font-bold text-amber-700 dark:bg-amber-900/40 dark:text-amber-300"> ★ ${ssrInterpolate(Number(__props.review.rating || 0))}/5 </span></div><div class="grid grid-cols-1 gap-3 md:grid-cols-2"><section class="rounded-sm border border-slate-300 bg-white p-3 dark:border-slate-600 dark:bg-slate-800"><h4 class="mb-2 text-sm font-semibold text-slate-900 dark:text-slate-100">${ssrInterpolate(unref(t)("author"))}</h4><div class="space-y-1 text-sm"><div><span class="font-semibold">${ssrInterpolate(unref(t)("id"))}: </span> ${ssrInterpolate(((_b = __props.review.author) == null ? void 0 : _b.id) || __props.review.user_id || "—")}</div><div><span class="font-semibold">${ssrInterpolate(unref(t)("name"))}: </span><span class="text-blue-700 dark:text-blue-300">${ssrInterpolate(((_c = __props.review.author) == null ? void 0 : _c.name) || "—")}</span></div><div><span class="font-semibold">Email:</span><span class="break-all">${ssrInterpolate(((_d = __props.review.author) == null ? void 0 : _d.email) || "—")}</span></div></div></section><section class="rounded-sm border border-slate-300 bg-white p-3 dark:border-slate-600 dark:bg-slate-800"><h4 class="mb-2 text-sm font-semibold text-slate-900 dark:text-slate-100">${ssrInterpolate(unref(t)("statistics"))}</h4><div class="grid grid-cols-2 gap-2 text-sm"><div><span class="font-semibold">${ssrInterpolate(unref(t)("likes"))}: </span> ${ssrInterpolate(Number(__props.review.likes || 0))}</div><div><span class="font-semibold">${ssrInterpolate(unref(t)("images"))}: </span> ${ssrInterpolate(Number(__props.review.images_count ?? images.value.length))}</div><div><span class="font-semibold">${ssrInterpolate(unref(t)("reply"))}: </span> ${ssrInterpolate(hasReply.value ? unref(t)("yes") : unref(t)("no"))}</div><div><span class="font-semibold">${ssrInterpolate(unref(t)("rating"))}: </span> ${ssrInterpolate(Number(__props.review.rating || 0))}</div></div></section></div><section class="mt-3 rounded-sm border border-slate-300 bg-white p-3 dark:border-slate-600 dark:bg-slate-800"><h4 class="mb-2 text-sm font-semibold text-slate-900 dark:text-slate-100">${ssrInterpolate(unref(t)("object"))}</h4><div class="flex flex-wrap items-center gap-2 text-sm font-semibold"><span class="text-slate-600 dark:text-slate-300"> [${ssrInterpolate(unref(t)("id"))}: ${ssrInterpolate(((_e = __props.review.reviewable) == null ? void 0 : _e.id) || __props.review.reviewable_id)}] </span><span class="text-indigo-700 dark:text-indigo-300"> [${ssrInterpolate(reviewableType.value)}] </span><span class="text-fuchsia-700 dark:text-fuchsia-300">${ssrInterpolate(reviewableTitle.value)}</span></div>`);
          if (((_f = __props.review.reviewable) == null ? void 0 : _f.sku) || ((_g = __props.review.reviewable) == null ? void 0 : _g.code) || ((_h = __props.review.reviewable) == null ? void 0 : _h.url) || ((_i = __props.review.reviewable) == null ? void 0 : _i.slug)) {
            _push2(`<div class="mt-2 grid grid-cols-1 gap-1 text-xs text-slate-500 dark:text-slate-300 sm:grid-cols-2">`);
            if ((_j = __props.review.reviewable) == null ? void 0 : _j.sku) {
              _push2(`<div> SKU: ${ssrInterpolate(__props.review.reviewable.sku)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if ((_k = __props.review.reviewable) == null ? void 0 : _k.code) {
              _push2(`<div>${ssrInterpolate(unref(t)("code"))}: ${ssrInterpolate(__props.review.reviewable.code)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if ((_l = __props.review.reviewable) == null ? void 0 : _l.url) {
              _push2(`<div class="break-all"> URL: ${ssrInterpolate(__props.review.reviewable.url)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if ((_m = __props.review.reviewable) == null ? void 0 : _m.slug) {
              _push2(`<div class="break-all"> Slug: ${ssrInterpolate(__props.review.reviewable.slug)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</section><div class="mt-3 grid grid-cols-1 gap-3 lg:grid-cols-2">`);
          if (__props.review.advantages) {
            _push2(`<section class="rounded-sm border border-emerald-300 bg-emerald-50 p-3 dark:border-emerald-700 dark:bg-emerald-950/30"><h4 class="mb-1 text-sm font-semibold text-emerald-700 dark:text-emerald-300">${ssrInterpolate(unref(t)("advantages"))}</h4><p class="whitespace-pre-wrap break-words text-sm text-slate-700 dark:text-slate-200">${ssrInterpolate(__props.review.advantages)}</p></section>`);
          } else {
            _push2(`<!---->`);
          }
          if (__props.review.disadvantages) {
            _push2(`<section class="rounded-sm border border-rose-300 bg-rose-50 p-3 dark:border-rose-700 dark:bg-rose-950/30"><h4 class="mb-1 text-sm font-semibold text-rose-700 dark:text-rose-300">${ssrInterpolate(unref(t)("disadvantages"))}</h4><p class="whitespace-pre-wrap break-words text-sm text-slate-700 dark:text-slate-200">${ssrInterpolate(__props.review.disadvantages)}</p></section>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`</div><section class="mt-3 rounded-sm border border-amber-300 bg-amber-50 p-3 dark:border-amber-700 dark:bg-amber-950/30"><h4 class="mb-1 text-sm font-semibold text-amber-700 dark:text-amber-300">${ssrInterpolate(unref(t)("review"))}</h4><p class="whitespace-pre-wrap break-words text-sm font-semibold text-slate-700 dark:text-slate-200">${ssrInterpolate(__props.review.comment || "—")}</p></section>`);
          if (hasReply.value) {
            _push2(`<section class="mt-3 rounded-sm border border-blue-300 bg-blue-50 p-3 dark:border-blue-700 dark:bg-blue-950/30"><h4 class="mb-1 text-sm font-semibold text-blue-700 dark:text-blue-300">${ssrInterpolate(unref(t)("reply"))}</h4><p class="whitespace-pre-wrap break-words text-sm text-slate-700 dark:text-slate-200">${ssrInterpolate(__props.review.reply)}</p><div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-300">`);
            if (__props.review.replier) {
              _push2(`<span>${ssrInterpolate(__props.review.replier.name || "—")} [ID: ${ssrInterpolate(__props.review.replier.id)}] </span>`);
            } else {
              _push2(`<!---->`);
            }
            if ((_n = __props.review.replier) == null ? void 0 : _n.email) {
              _push2(`<span>${ssrInterpolate(__props.review.replier.email)}</span>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.review.replied_at) {
              _push2(`<span>${ssrInterpolate(formatDate(__props.review.replied_at))}</span>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></section>`);
          } else {
            _push2(`<!---->`);
          }
          if (images.value.length) {
            _push2(`<section class="mt-3 rounded-sm border border-slate-300 bg-white p-3 dark:border-slate-600 dark:bg-slate-800"><h4 class="mb-3 text-sm font-semibold text-slate-900 dark:text-slate-100">${ssrInterpolate(unref(t)("reviewImages"))}: ${ssrInterpolate(images.value.length)}</h4><div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3"><!--[-->`);
            ssrRenderList(images.value, (image) => {
              _push2(`<a${ssrRenderAttr("href", imageUrl(image))} target="_blank" rel="noopener noreferrer" class="group overflow-hidden rounded-sm border border-slate-300 dark:border-slate-600"><img${ssrRenderAttr("src", imageUrl(image))}${ssrRenderAttr("alt", image.alt || "")} class="h-44 w-full object-cover transition-transform duration-200 group-hover:scale-105">`);
              if (image.caption || image.alt) {
                _push2(`<div class="space-y-1 p-2 text-xs text-slate-600 dark:text-slate-300">`);
                if (image.caption) {
                  _push2(`<div>${ssrInterpolate(image.caption)}</div>`);
                } else {
                  _push2(`<!---->`);
                }
                if (image.alt) {
                  _push2(`<div class="break-words text-[10px]"> Alt: ${ssrInterpolate(image.alt)}</div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`</div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</a>`);
            });
            _push2(`<!--]--></div></section>`);
          } else {
            _push2(`<!---->`);
          }
          if (__props.review.moderation_note || __props.review.moderator || __props.review.moderated_at) {
            _push2(`<section class="mt-3 rounded-sm border border-slate-300 bg-white p-3 dark:border-slate-600 dark:bg-slate-800"><h4 class="mb-2 text-sm font-semibold text-slate-900 dark:text-slate-100">${ssrInterpolate(unref(t)("moderation"))}</h4><div class="space-y-1 text-sm">`);
            if (__props.review.moderation_note) {
              _push2(`<div><span class="font-semibold">${ssrInterpolate(unref(t)("moderationNote"))}: </span><span class="whitespace-pre-wrap break-words">${ssrInterpolate(__props.review.moderation_note)}</span></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.review.moderator) {
              _push2(`<div><span class="font-semibold">${ssrInterpolate(unref(t)("moderator"))}: </span> ${ssrInterpolate(__props.review.moderator.name || "—")} [ID: ${ssrInterpolate(__props.review.moderator.id)}] `);
              if (__props.review.moderator.email) {
                _push2(`<span> — ${ssrInterpolate(__props.review.moderator.email)}</span>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.review.moderated_at) {
              _push2(`<div><span class="font-semibold">${ssrInterpolate(unref(t)("moderatedAt"))}: </span> ${ssrInterpolate(formatDate(__props.review.moderated_at))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></section>`);
          } else {
            _push2(`<!---->`);
          }
          _push2(`<div class="mt-3 flex flex-col gap-1 border-t border-dashed border-slate-400 pt-3 text-xs italic text-slate-600 dark:text-slate-400 sm:flex-row sm:justify-between"><div><span class="font-semibold not-italic">${ssrInterpolate(unref(t)("createdAt"))}: </span> ${ssrInterpolate(formatDate(__props.review.created_at))}</div><div><span class="font-semibold not-italic">${ssrInterpolate(unref(t)("updatedAt"))}: </span> ${ssrInterpolate(formatDate(__props.review.updated_at))}</div></div><!--]-->`);
        } else {
          _push2(`<div class="py-8 text-center text-sm text-slate-500 dark:text-slate-300">${ssrInterpolate(unref(t)("noData"))}</div>`);
        }
        _push2(`<div class="mt-4 flex justify-center"><button type="button" class="flex items-center gap-1 rounded-sm bg-slate-600 px-3 py-1 text-sm font-semibold text-white hover:bg-slate-700 dark:bg-slate-500 dark:hover:bg-slate-400"><svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg><span>${ssrInterpolate(unref(t)("close"))}</span></button></div></div></div></div>`);
      }, "body", false, _parent);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Review/Modal/ReviewDetailsModal.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    reviews: { type: [Array, Object], default: () => [] },
    reviewsCount: { type: Number, default: 0 },
    adminReviewsProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    adminReviewsPerPage: { type: Number, default: 10 },
    adminReviewsDefaultSort: { type: String, default: "idDesc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" },
    currentLocale: { type: String, default: "" },
    availableLocales: { type: Array, default: () => [] },
    isAdmin: { type: Boolean, default: false },
    error: { type: String, default: "" },
    errors: { type: Object, default: () => ({}) }
  },
  setup(__props) {
    const { t, locale } = useI18n();
    const toast = useToast();
    const props = __props;
    const isAdmin = computed(() => Boolean(props.isAdmin));
    const viewMode = ref(
      localStorage.getItem("admin_view_mode_reviews") || "table"
    );
    watch(viewMode, (value) => {
      localStorage.setItem("admin_view_mode_reviews", value);
    });
    const reviewsList = computed(() => {
      var _a;
      if (Array.isArray(props.reviews)) return props.reviews;
      if (Array.isArray((_a = props.reviews) == null ? void 0 : _a.data)) return props.reviews.data;
      return [];
    });
    const localReviews = ref([]);
    watch(
      reviewsList,
      (reviews) => {
        localReviews.value = JSON.parse(
          JSON.stringify(reviews || [])
        );
      },
      {
        immediate: true,
        deep: true
      }
    );
    const patchLocal = (id, patch) => {
      const index = localReviews.value.findIndex(
        (review) => Number(review.id) === Number(id)
      );
      if (index === -1) return;
      localReviews.value[index] = {
        ...localReviews.value[index],
        ...patch
      };
    };
    const itemsPerPage = ref(
      Number(props.adminReviewsPerPage || 10)
    );
    watch(itemsPerPage, (value) => {
      router.put(
        route("admin.settings.updateAdminCountReviews"),
        { value },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => toast.info(
            `Показ ${value} элементов на странице.`
          ),
          onError: (errors) => toast.error(
            (errors == null ? void 0 : errors.value) || "Ошибка обновления количества элементов."
          )
        }
      );
    });
    const currentPage = ref(1);
    const sortParam = ref(
      props.sortParam || props.adminReviewsDefaultSort || "idDesc"
    );
    watch(sortParam, (value) => {
      currentPage.value = 1;
      router.put(
        route("admin.settings.updateAdminSortReviews"),
        { value },
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
                  sort: value || void 0,
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
              "Сортировка успешно изменена."
            );
          },
          onError: (errors) => toast.error(
            (errors == null ? void 0 : errors.value) || "Ошибка обновления сортировки."
          )
        }
      );
    });
    const searchQuery = ref(props.search || "");
    watch(
      [
        itemsPerPage,
        searchQuery
      ],
      () => {
        currentPage.value = 1;
      }
    );
    const normalize = (value) => (value ?? "").toString().trim().toLowerCase();
    const searchWords = (value) => {
      return String(value || "").split(/[\s:#№,"'«»(){}\[\].!?/\\|;+=*&^%$@<>`~_-]+/u).map((word) => normalize(word)).filter((word) => word.length >= 2);
    };
    const numberValue = (value, fallback = 0) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : fallback;
    };
    const safeDate = (value) => {
      const time = new Date(value || 0).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const reviewableType = (review) => {
      var _a;
      return ((_a = review == null ? void 0 : review.reviewable) == null ? void 0 : _a.type) || (review == null ? void 0 : review.reviewable_type) || "";
    };
    const reviewableTitle = (review) => {
      var _a, _b, _c;
      return ((_a = review == null ? void 0 : review.reviewable) == null ? void 0 : _a.title) || ((_b = review == null ? void 0 : review.reviewable) == null ? void 0 : _b.name) || ((_c = review == null ? void 0 : review.reviewable) == null ? void 0 : _c.url) || "";
    };
    const hasReply = (review) => {
      return Boolean(
        (review == null ? void 0 : review.has_reply) || normalize(review == null ? void 0 : review.reply)
      );
    };
    const reviewSearchValues = (review) => {
      var _a, _b, _c, _d, _e, _f, _g, _h, _i, _j, _k, _l, _m;
      return [
        /** Сам отзыв */
        review == null ? void 0 : review.reviewable_type,
        reviewableType(review),
        review == null ? void 0 : review.advantages,
        review == null ? void 0 : review.disadvantages,
        review == null ? void 0 : review.comment,
        review == null ? void 0 : review.reply,
        review == null ? void 0 : review.moderation_note,
        /** Связанные пользователи */
        (_a = review == null ? void 0 : review.author) == null ? void 0 : _a.name,
        (_b = review == null ? void 0 : review.author) == null ? void 0 : _b.email,
        (_c = review == null ? void 0 : review.replier) == null ? void 0 : _c.name,
        (_d = review == null ? void 0 : review.replier) == null ? void 0 : _d.email,
        (_e = review == null ? void 0 : review.moderator) == null ? void 0 : _e.name,
        (_f = review == null ? void 0 : review.moderator) == null ? void 0 : _f.email,
        /** Полиморфная сущность */
        reviewableTitle(review),
        (_g = review == null ? void 0 : review.reviewable) == null ? void 0 : _g.url,
        (_h = review == null ? void 0 : review.reviewable) == null ? void 0 : _h.sku,
        /**
         * Эти поля могут присутствовать у MarketProduct.
         * Если Resource их не передаёт, значения просто undefined.
         */
        (_i = review == null ? void 0 : review.reviewable) == null ? void 0 : _i.vendor_code,
        (_j = review == null ? void 0 : review.reviewable) == null ? void 0 : _j.barcode,
        /**
         * Переводимые поля reviewable.
         * title уже нормализован в reviewable.title.
         * Остальные поля учитываются, если Resource их передаёт.
         */
        (_k = review == null ? void 0 : review.reviewable) == null ? void 0 : _k.subtitle,
        (_l = review == null ? void 0 : review.reviewable) == null ? void 0 : _l.short,
        (_m = review == null ? void 0 : review.reviewable) == null ? void 0 : _m.description
      ];
    };
    const sortReviews = (reviews) => {
      const list = [...reviews || []];
      if (sortParam.value === "activity") {
        return list.filter(
          (review) => Boolean(review.activity)
        );
      }
      if (sortParam.value === "inactive") {
        return list.filter(
          (review) => !review.activity
        );
      }
      if (sortParam.value === "verified") {
        return list.filter(
          (review) => Boolean(review.verified)
        );
      }
      if (sortParam.value === "notVerified") {
        return list.filter(
          (review) => !review.verified
        );
      }
      if (sortParam.value === "hasReply") {
        return list.filter(
          (review) => hasReply(review)
        );
      }
      if (sortParam.value === "noReply") {
        return list.filter(
          (review) => !hasReply(review)
        );
      }
      if (sortParam.value === "moderationPending") {
        return list.filter(
          (review) => numberValue(
            review.moderation_status
          ) === 0
        );
      }
      if (sortParam.value === "moderationApproved") {
        return list.filter(
          (review) => numberValue(
            review.moderation_status
          ) === 1
        );
      }
      if (sortParam.value === "moderationRejected") {
        return list.filter(
          (review) => numberValue(
            review.moderation_status
          ) === 2
        );
      }
      const sortMap = {
        idAsc: (a, b) => numberValue(a.id) - numberValue(b.id),
        idDesc: (a, b) => numberValue(b.id) - numberValue(a.id),
        authorNameAsc: (a, b) => {
          var _a, _b;
          return normalize((_a = a == null ? void 0 : a.author) == null ? void 0 : _a.name).localeCompare(
            normalize((_b = b == null ? void 0 : b.author) == null ? void 0 : _b.name),
            locale.value
          );
        },
        authorNameDesc: (a, b) => {
          var _a, _b;
          return normalize((_a = b == null ? void 0 : b.author) == null ? void 0 : _a.name).localeCompare(
            normalize((_b = a == null ? void 0 : a.author) == null ? void 0 : _b.name),
            locale.value
          );
        },
        authorEmailAsc: (a, b) => {
          var _a, _b;
          return normalize((_a = a == null ? void 0 : a.author) == null ? void 0 : _a.email).localeCompare(
            normalize((_b = b == null ? void 0 : b.author) == null ? void 0 : _b.email),
            locale.value
          );
        },
        authorEmailDesc: (a, b) => {
          var _a, _b;
          return normalize((_a = b == null ? void 0 : b.author) == null ? void 0 : _a.email).localeCompare(
            normalize((_b = a == null ? void 0 : a.author) == null ? void 0 : _b.email),
            locale.value
          );
        },
        ratingAsc: (a, b) => numberValue(a.rating) - numberValue(b.rating),
        ratingDesc: (a, b) => numberValue(b.rating) - numberValue(a.rating),
        likesAsc: (a, b) => numberValue(a.likes) - numberValue(b.likes),
        likesDesc: (a, b) => numberValue(b.likes) - numberValue(a.likes),
        imagesAsc: (a, b) => numberValue(a.images_count) - numberValue(b.images_count),
        imagesDesc: (a, b) => numberValue(b.images_count) - numberValue(a.images_count),
        commentAsc: (a, b) => normalize(a.comment).localeCompare(
          normalize(b.comment),
          locale.value
        ),
        commentDesc: (a, b) => normalize(b.comment).localeCompare(
          normalize(a.comment),
          locale.value
        ),
        reviewableTypeAsc: (a, b) => normalize(reviewableType(a)).localeCompare(
          normalize(reviewableType(b)),
          locale.value
        ),
        reviewableTypeDesc: (a, b) => normalize(reviewableType(b)).localeCompare(
          normalize(reviewableType(a)),
          locale.value
        ),
        verifiedAsc: (a, b) => Number(Boolean(a.verified)) - Number(Boolean(b.verified)),
        verifiedDesc: (a, b) => Number(Boolean(b.verified)) - Number(Boolean(a.verified)),
        replyAsc: (a, b) => Number(hasReply(a)) - Number(hasReply(b)),
        replyDesc: (a, b) => Number(hasReply(b)) - Number(hasReply(a)),
        createdAtAsc: (a, b) => safeDate(a.created_at) - safeDate(b.created_at),
        createdAtDesc: (a, b) => safeDate(b.created_at) - safeDate(a.created_at),
        updatedAtAsc: (a, b) => safeDate(a.updated_at) - safeDate(b.updated_at),
        updatedAtDesc: (a, b) => safeDate(b.updated_at) - safeDate(a.updated_at),
        activityAsc: (a, b) => Number(Boolean(a.activity)) - Number(Boolean(b.activity)),
        activityDesc: (a, b) => Number(Boolean(b.activity)) - Number(Boolean(a.activity)),
        moderationStatusAsc: (a, b) => numberValue(a.moderation_status) - numberValue(b.moderation_status),
        moderationStatusDesc: (a, b) => numberValue(b.moderation_status) - numberValue(a.moderation_status)
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const filteredReviews = computed(() => {
      const words = searchWords(searchQuery.value);
      if (!words.length) {
        return sortReviews(localReviews.value);
      }
      const filtered = localReviews.value.filter(
        (review) => {
          const values = reviewSearchValues(review).map((value) => normalize(value)).filter(Boolean);
          return words.every(
            (word) => values.some(
              (value) => value.includes(word)
            )
          );
        }
      );
      return sortReviews(filtered);
    });
    const paginatedReviews = computed(() => {
      const perPage = Number(
        itemsPerPage.value || 10
      );
      const start = (currentPage.value - 1) * perPage;
      return filteredReviews.value.slice(
        start,
        start + perPage
      );
    });
    const displayedReviews = computed(() => {
      return props.useServerProcessing ? reviewsList.value : paginatedReviews.value;
    });
    const selectedReviews = ref([]);
    const toggleAll = (payload) => {
      var _a;
      const checked = (payload == null ? void 0 : payload.checked) ?? ((_a = payload == null ? void 0 : payload.target) == null ? void 0 : _a.checked) ?? false;
      const ids = (payload == null ? void 0 : payload.ids) ?? displayedReviews.value.map(
        (review) => review.id
      );
      if (checked) {
        selectedReviews.value = [
          .../* @__PURE__ */ new Set([
            ...selectedReviews.value,
            ...ids
          ])
        ];
        return;
      }
      selectedReviews.value = selectedReviews.value.filter(
        (id) => !ids.includes(id)
      );
    };
    const toggleSelectReview = (reviewId) => {
      const index = selectedReviews.value.indexOf(
        reviewId
      );
      if (index > -1) {
        selectedReviews.value.splice(
          index,
          1
        );
        return;
      }
      selectedReviews.value.push(
        reviewId
      );
    };
    const bulkToggleActivity = (activity) => {
      if (!selectedReviews.value.length) {
        toast.warning(
          "Выберите отзывы для изменения активности."
        );
        return;
      }
      const ids = [
        ...selectedReviews.value
      ];
      router.put(
        route(
          "admin.actions.reviews.bulkUpdateActivity"
        ),
        {
          ids,
          activity
        },
        {
          preserveScroll: true,
          preserveState: true,
          onSuccess: () => {
            localReviews.value = localReviews.value.map(
              (review) => ids.includes(review.id) ? {
                ...review,
                activity
              } : review
            );
            selectedReviews.value = [];
            toast.success(
              "Активность отзывов массово обновлена."
            );
          },
          onError: (errors) => toast.error(
            (errors == null ? void 0 : errors.ids) || (errors == null ? void 0 : errors.activity) || (errors == null ? void 0 : errors.general) || "Не удалось массово обновить активность отзывов."
          )
        }
      );
    };
    const bulkDelete = async () => {
      var _a, _b, _c, _d;
      if (!selectedReviews.value.length) {
        toast.warning(
          "Выберите хотя бы один отзыв для удаления."
        );
        return;
      }
      if (!confirm(
        "Вы уверены, что хотите удалить выбранные отзывы?"
      )) {
        return;
      }
      const ids = [
        ...selectedReviews.value
      ];
      try {
        const response = await axios.delete(
          route(
            "admin.actions.reviews.bulkDestroy"
          ),
          {
            data: {
              ids
            }
          }
        );
        if (!((_a = response == null ? void 0 : response.data) == null ? void 0 : _a.success)) {
          toast.error(
            ((_b = response == null ? void 0 : response.data) == null ? void 0 : _b.message) || "Произошла ошибка при удалении отзывов."
          );
          return;
        }
        localReviews.value = localReviews.value.filter(
          (review) => !ids.includes(review.id)
        );
        selectedReviews.value = [];
        toast.success(
          response.data.message || "Массовое удаление отзывов успешно завершено."
        );
      } catch (error) {
        console.error(
          "Ошибка массового удаления отзывов:",
          error
        );
        toast.error(
          ((_d = (_c = error == null ? void 0 : error.response) == null ? void 0 : _c.data) == null ? void 0 : _d.message) || "Произошла ошибка при удалении отзывов."
        );
      }
    };
    const handleBulkAction = (event) => {
      const action = event.target.value;
      if (action === "selectAll") {
        toggleAll({
          checked: true
        });
      }
      if (action === "deselectAll") {
        toggleAll({
          checked: false
        });
      }
      if (action === "activate") {
        bulkToggleActivity(true);
      }
      if (action === "deactivate") {
        bulkToggleActivity(false);
      }
      if (action === "delete") {
        bulkDelete();
      }
      event.target.value = "";
    };
    const showReviewDetailsModal = ref(false);
    const reviewDetails = ref(null);
    const viewReviewDetails = (review) => {
      reviewDetails.value = review;
      showReviewDetailsModal.value = true;
    };
    const closeReviewDetailsModal = () => {
      showReviewDetailsModal.value = false;
      reviewDetails.value = null;
    };
    const showConfirmDeleteModal = ref(false);
    const reviewToDeleteId = ref(null);
    const confirmDelete = (id) => {
      reviewToDeleteId.value = id;
      showConfirmDeleteModal.value = true;
    };
    const closeDeleteModal = () => {
      showConfirmDeleteModal.value = false;
      reviewToDeleteId.value = null;
    };
    const deleteReview = () => {
      if (reviewToDeleteId.value === null) {
        return;
      }
      const id = reviewToDeleteId.value;
      router.delete(
        route(
          "admin.reviews.destroy",
          {
            review: id
          }
        ),
        {
          preserveScroll: true,
          preserveState: false,
          onSuccess: () => toast.success(
            `Отзыв "ID: ${id}" удалён.`
          ),
          onError: (errors) => {
            const firstKey = Object.keys(
              errors || {}
            )[0];
            const message = (errors == null ? void 0 : errors.general) || (errors == null ? void 0 : errors[firstKey]) || "Произошла ошибка при удалении.";
            toast.error(
              `${message} (Отзыв: ID: ${id})`
            );
          },
          onFinish: closeDeleteModal
        }
      );
    };
    const toggleActivity = async (review) => {
      var _a, _b;
      const activity = !review.activity;
      try {
        const response = await axios.put(
          route(
            "admin.actions.reviews.updateActivity",
            {
              review: review.id
            }
          ),
          {
            activity
          }
        );
        patchLocal(
          review.id,
          {
            activity: Boolean(
              response.data.activity
            )
          }
        );
        toast.success(
          response.data.message || "Активность отзыва обновлена."
        );
      } catch (error) {
        console.error(
          "Ошибка изменения активности отзыва:",
          error
        );
        toast.error(
          ((_b = (_a = error == null ? void 0 : error.response) == null ? void 0 : _a.data) == null ? void 0 : _b.message) || "Ошибка при изменении активности отзыва."
        );
      }
    };
    const approveReview = async (review, status = 1, note = "") => {
      var _a, _b, _c, _d, _e, _f, _g, _h, _i;
      if (!isAdmin.value) {
        toast.error(
          "Модерация доступна только администратору."
        );
        return;
      }
      if (!(review == null ? void 0 : review.id)) return;
      try {
        const response = await axios.put(
          route(
            "admin.actions.reviews.approve",
            {
              review: review.id
            }
          ),
          {
            moderation_status: status,
            moderation_note: note
          }
        );
        const resource = (_a = response.data) == null ? void 0 : _a.review;
        const data = (resource == null ? void 0 : resource.data) || resource;
        patchLocal(
          review.id,
          data ? {
            moderation_status: data.moderation_status,
            moderation_note: data.moderation_note,
            moderated_by: data.moderated_by,
            moderated_at: data.moderated_at,
            moderator: data.moderator,
            is_pending: data.is_pending,
            is_approved: data.is_approved,
            is_rejected: data.is_rejected
          } : {
            moderation_status: status,
            moderation_note: note,
            is_pending: Number(status) === 0,
            is_approved: Number(status) === 1,
            is_rejected: Number(status) === 2
          }
        );
        toast.success(
          ((_b = response.data) == null ? void 0 : _b.message) || "Статус модерации обновлён."
        );
      } catch (error) {
        if (((_c = error == null ? void 0 : error.response) == null ? void 0 : _c.status) === 403) {
          toast.error(
            "Доступ запрещён: модерировать отзывы может только администратор."
          );
          return;
        }
        const validationMessage = (_g = (_f = (_e = (_d = error == null ? void 0 : error.response) == null ? void 0 : _d.data) == null ? void 0 : _e.errors) == null ? void 0 : _f.moderation_note) == null ? void 0 : _g[0];
        toast.error(
          validationMessage || ((_i = (_h = error == null ? void 0 : error.response) == null ? void 0 : _h.data) == null ? void 0 : _i.message) || "Ошибка при обновлении модерации отзыва."
        );
        console.error(
          "Ошибка модерации отзыва:",
          error
        );
      }
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("reviews")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("reviews"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("reviews")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("reviews")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-end sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$9, {
              "setting-key": "adminReviewsProcessingMode",
              mode: __props.adminReviewsProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.reviewsCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.reviewsCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$a, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("search")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.reviewsCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$b, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("search")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.reviewsCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$c, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$d, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountReviews"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$4, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": ($event) => sortParam.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.reviewsCount) {
              _push2(`<div class="flex flex-col lg:flex-row items-center justify-between gap-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$e, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.reviewsCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.reviewsCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$5, { onChange: handleBulkAction }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$f, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.reviewsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredReviews.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.reviews }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$3, {
                isAdmin: isAdmin.value,
                reviews: displayedReviews.value,
                "selected-reviews": selectedReviews.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectReview,
                onToggleAll: toggleAll,
                onViewDetails: viewReviewDetails,
                onApproveReview: approveReview
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$2, {
                isAdmin: isAdmin.value,
                reviews: displayedReviews.value,
                "selected-reviews": selectedReviews.value,
                onToggleActivity: toggleActivity,
                onDelete: confirmDelete,
                onToggleSelect: toggleSelectReview,
                onToggleAll: toggleAll,
                onViewDetails: viewReviewDetails,
                onApproveReview: approveReview
              }, null, _parent2, _scopeId));
            }
            _push2(ssrRenderComponent(_sfc_main$1, {
              show: showReviewDetailsModal.value,
              review: reviewDetails.value,
              onClose: closeReviewDetailsModal
            }, null, _parent2, _scopeId));
            if (__props.reviewsCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$g, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredReviews.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$h, { pagination: __props.reviews }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (!__props.reviewsCount && !props.error) {
              _push2(`<div class="py-8 text-center text-sm font-semibold text-slate-500 dark:text-slate-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (props.error) {
              _push2(`<div class="mt-3 text-sm font-semibold text-rose-700 dark:text-rose-300"${_scopeId}>${ssrInterpolate(props.error)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$i, {
              show: showConfirmDeleteModal.value,
              onCancel: closeDeleteModal,
              onConfirm: deleteReview,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeDeleteModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-end sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$9, {
                      "setting-key": "adminReviewsProcessingMode",
                      mode: __props.adminReviewsProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.reviewsCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.reviewsCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("search")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.reviewsCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$b, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("search")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.reviewsCount ? (openBlock(), createBlock("div", {
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
                      "update-route": "admin.settings.updateAdminCountReviews"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$4, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": ($event) => sortParam.value = $event
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.reviewsCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex flex-col lg:flex-row items-center justify-between gap-3"
                  }, [
                    createVNode(_sfc_main$e, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.reviewsCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$5, { onChange: handleBulkAction }),
                    createVNode(_sfc_main$f, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.reviewsCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredReviews.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.reviews
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$3, {
                    key: 5,
                    isAdmin: isAdmin.value,
                    reviews: displayedReviews.value,
                    "selected-reviews": selectedReviews.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectReview,
                    onToggleAll: toggleAll,
                    onViewDetails: viewReviewDetails,
                    onApproveReview: approveReview
                  }, null, 8, ["isAdmin", "reviews", "selected-reviews"])) : (openBlock(), createBlock(_sfc_main$2, {
                    key: 6,
                    isAdmin: isAdmin.value,
                    reviews: displayedReviews.value,
                    "selected-reviews": selectedReviews.value,
                    onToggleActivity: toggleActivity,
                    onDelete: confirmDelete,
                    onToggleSelect: toggleSelectReview,
                    onToggleAll: toggleAll,
                    onViewDetails: viewReviewDetails,
                    onApproveReview: approveReview
                  }, null, 8, ["isAdmin", "reviews", "selected-reviews"])),
                  createVNode(_sfc_main$1, {
                    show: showReviewDetailsModal.value,
                    review: reviewDetails.value,
                    onClose: closeReviewDetailsModal
                  }, null, 8, ["show", "review"]),
                  __props.reviewsCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$g, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredReviews.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$h, {
                      key: 1,
                      pagination: __props.reviews
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  !__props.reviewsCount && !props.error ? (openBlock(), createBlock("div", {
                    key: 8,
                    class: "py-8 text-center text-sm font-semibold text-slate-500 dark:text-slate-300"
                  }, toDisplayString(unref(t)("noData")), 1)) : createCommentVNode("", true),
                  props.error ? (openBlock(), createBlock("div", {
                    key: 9,
                    class: "mt-3 text-sm font-semibold text-rose-700 dark:text-rose-300"
                  }, toDisplayString(props.error), 1)) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$i, {
                show: showConfirmDeleteModal.value,
                onCancel: closeDeleteModal,
                onConfirm: deleteReview,
                cancelText: unref(t)("cancel"),
                confirmText: unref(t)("yesDelete"),
                onClose: closeDeleteModal
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Reviews/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
