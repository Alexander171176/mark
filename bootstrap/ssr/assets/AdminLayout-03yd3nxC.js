import { mergeProps, withCtx, unref, createTextVNode, toDisplayString, useSSRContext, ref, createBlock, openBlock, createVNode, createCommentVNode, withModifiers, Fragment, renderList, computed, onMounted, onUnmounted, watch } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderList, ssrRenderClass, ssrRenderSlot, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { Link, router, usePage, Head } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { _ as _sfc_main$j, a as _sfc_main$k, b as _sfc_main$m } from "./LocaleSelectOption-BeLdazeX.js";
import { _ as _sfc_main$i, A as ApplicationMark } from "./ResponsiveNavLink-gtte0z5g.js";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$l } from "./ThemeToggle-DA16u1ft.js";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
import draggable from "vuedraggable";
import { s as sidebarIcons, D as DigitalClock, _ as _sfc_main$n, S as ScrollButtons } from "./ScrollButtons-2xyFJfJ4.js";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { fas } from "@fortawesome/free-solid-svg-icons";
import { Inertia } from "@inertiajs/inertia";
import { Container } from "vue-smooth-dnd";
const _sfc_main$h = {
  __name: "ResponsiveNavLinks",
  __ssrInlineRender: true,
  setup(__props) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "pt-2 pb-24 space-y-1" }, _attrs))}>`);
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("dashboard"),
        active: _ctx.route().current("dashboard")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("dashboard"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("dashboard")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.index"),
        active: _ctx.route().current("admin.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("adminPanel"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("adminPanel")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.cmsPages.index"),
        active: _ctx.route().current("admin.cmsPages.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("pages"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("pages")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.marketCompanies.index"),
        active: _ctx.route().current("admin.marketCompanies.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("marketCompanies"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("marketCompanies")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.marketShops.index"),
        active: _ctx.route().current("admin.marketShops.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("marketShops"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("marketShops")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.marketCategories.index"),
        active: _ctx.route().current("admin.marketCategories.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("marketCategories"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("marketCategories")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.marketProducts.index"),
        active: _ctx.route().current("admin.marketProducts.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("marketProducts"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("marketProducts")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.marketProductBundles.index"),
        active: _ctx.route().current("admin.marketProductBundles.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("marketProductBundles"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("marketProductBundles")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.marketProductVariants.index"),
        active: _ctx.route().current("admin.marketProductVariants.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("marketProductVariants"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("marketProductVariants")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.marketBrands.index"),
        active: _ctx.route().current("admin.marketBrands.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("marketBrands"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("marketBrands")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.marketTags.index"),
        active: _ctx.route().current("admin.marketTags.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("marketTags"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("marketTags")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.marketAttributeGroups.index"),
        active: _ctx.route().current("admin.marketAttributeGroups.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("marketAttributeGroups"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("marketAttributeGroups")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.marketAttributes.index"),
        active: _ctx.route().current("admin.marketAttributes.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("marketAttributes"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("marketAttributes")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.marketAttributeValues.index"),
        active: _ctx.route().current("admin.marketAttributeValues.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("marketAttributeValues"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("marketAttributeValues")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.currencies.index"),
        active: _ctx.route().current("admin.currencies.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("currencies"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("currencies")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolOrders.index"),
        active: _ctx.route().current("admin.schoolOrders.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("orders"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("orders")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolSubscriptionPlans.index"),
        active: _ctx.route().current("admin.schoolSubscriptionPlans.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("subscriptionPlans"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("subscriptionPlans")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolCoursePrices.index"),
        active: _ctx.route().current("admin.schoolCoursePrices.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("coursePrices"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("coursePrices")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolBundlePrices.index"),
        active: _ctx.route().current("admin.schoolBundlePrices.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("bundlePrices"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("bundlePrices")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolInstructorProfiles.index"),
        active: _ctx.route().current("admin.schoolInstructorProfiles.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("instructors"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("instructors")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolHashtags.index"),
        active: _ctx.route().current("admin.schoolHashtags.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("hashtags"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("hashtags")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolTracks.index"),
        active: _ctx.route().current("admin.schoolTracks.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("learningCategories"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("learningCategories")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolBundles.index"),
        active: _ctx.route().current("admin.schoolBundles.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("bundles"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("bundles")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolCourses.index"),
        active: _ctx.route().current("admin.schoolCourses.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("courses"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("courses")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolModules.index"),
        active: _ctx.route().current("admin.schoolModules.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("modules"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("modules")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolLessons.index"),
        active: _ctx.route().current("admin.schoolLessons.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("lessons"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("lessons")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolAssignments.index"),
        active: _ctx.route().current("admin.schoolAssignments.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("assignments"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("assignments")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolCourseSchedules.index"),
        active: _ctx.route().current("admin.schoolCourseSchedules.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("courseSchedules"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("courseSchedules")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolCohortEnrollments.index"),
        active: _ctx.route().current("admin.schoolCohortEnrollments.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("cohortEnrollments"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("cohortEnrollments")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolEnrollments.index"),
        active: _ctx.route().current("admin.schoolEnrollments.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("enrollments"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("enrollments")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolQuizzes.index"),
        active: _ctx.route().current("admin.schoolQuizzes.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("quizzes"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("quizzes")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolQuizQuestions.index"),
        active: _ctx.route().current("admin.schoolQuizQuestions.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("quizQuestions"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("quizQuestions")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolQuizAnswers.index"),
        active: _ctx.route().current("admin.schoolQuizAnswers.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("quizAnswers"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("quizAnswers")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolQuizAttempts.index"),
        active: _ctx.route().current("admin.schoolQuizAttempts.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("quizAttempts"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("quizAttempts")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.schoolQuizAttemptItems.index"),
        active: _ctx.route().current("admin.schoolQuizAttemptItems.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("quizAttemptItems"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("quizAttemptItems")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.blogRubrics.index"),
        active: _ctx.route().current("admin.blogRubrics.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("rubrics"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("rubrics")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.blogArticles.index"),
        active: _ctx.route().current("admin.blogArticles.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("articles"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("articles")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.blogTags.index"),
        active: _ctx.route().current("admin.blogTags.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("tags"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("tags")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.blogBanners.index"),
        active: _ctx.route().current("admin.blogBanners.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("banners"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("banners")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.blogVideos.index"),
        active: _ctx.route().current("admin.blogVideos.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("videos"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("videos")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.comments.index"),
        active: _ctx.route().current("admin.comments.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("comments"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("comments")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.reviews.index"),
        active: _ctx.route().current("admin.reviews.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("reviews"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("reviews")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.charts.index"),
        active: _ctx.route().current("admin.charts.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("charts"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("charts")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.analyticsVisitorLogs.index"),
        active: _ctx.route().current("admin.analyticsVisitorLogs.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("analyticsLogs"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("analyticsLogs")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.users.index"),
        active: _ctx.route().current("admin.users.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("users"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("users")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.roles.index"),
        active: _ctx.route().current("admin.roles.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("roles"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("roles")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.permissions.index"),
        active: _ctx.route().current("admin.permissions.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("permissions"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("permissions")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.settings.index"),
        active: _ctx.route().current("admin.settings.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("settings"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("settings")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.parameters.index"),
        active: _ctx.route().current("admin.parameters.*")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("parameters"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("parameters")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.logs.index"),
        active: _ctx.route().current("admin.logs.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("logs"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("logs")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.phpinfo.index"),
        active: _ctx.route().current("admin.phpinfo.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` phpinfo `);
          } else {
            return [
              createTextVNode(" phpinfo ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.composer.index"),
        active: _ctx.route().current("admin.composer.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` composer `);
          } else {
            return [
              createTextVNode(" composer ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.package.index"),
        active: _ctx.route().current("admin.package.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` package `);
          } else {
            return [
              createTextVNode(" package ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.env.index"),
        active: _ctx.route().current("admin.env.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` env `);
          } else {
            return [
              createTextVNode(" env ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.backup.index"),
        active: _ctx.route().current("admin.backup.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("backups"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("backups")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.files.index"),
        active: _ctx.route().current("admin.files.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("archive"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("archive")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.components.index"),
        active: _ctx.route().current("admin.components.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("components"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("components")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.robot.index"),
        active: _ctx.route().current("admin.robot.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("robot"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("robot")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.sitemap.index"),
        active: _ctx.route().current("admin.sitemap.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(` sitemap.xml `);
          } else {
            return [
              createTextVNode(" sitemap.xml ")
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.reports.index"),
        active: _ctx.route().current("admin.reports.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("reports"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("reports")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.imagePresets.index"),
        active: _ctx.route().current("admin.imagePresets.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("imagePresets"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("imagePresets")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.locations.index"),
        active: _ctx.route().current("admin.locations.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("locations"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("locations")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(ssrRenderComponent(_sfc_main$i, {
        href: _ctx.route("admin.localization.index"),
        active: _ctx.route().current("admin.localization.index")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("localization"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("localization")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup$h = _sfc_main$h.setup;
_sfc_main$h.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Links/ResponsiveNavLinks.vue");
  return _sfc_setup$h ? _sfc_setup$h(props, ctx) : void 0;
};
const _sfc_main$g = {
  __name: "TopPanel",
  __ssrInlineRender: true,
  setup(__props) {
    const { t } = useI18n();
    const isPanelOpen = ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(_attrs)} data-v-e5b6029f><button class="fixed top-0 right-3 z-50 hidden md:inline-block px-3 py-3 cursor-pointer" data-v-e5b6029f><svg class="w-6 h-6" viewBox="0 0 20 20" data-v-e5b6029f><circle fill="none" class="stroke-violet-500" cx="9.997" cy="10" r="3.31" data-v-e5b6029f></circle><path fill="none" class="stroke-violet-500" d="M18.488,12.285 L16.205,16.237 C15.322,15.496 14.185,15.281 13.303,15.791 C12.428,16.289 12.047,17.373 12.246,18.5 L7.735,18.5 C7.938,17.374 7.553,16.299 6.684,15.791 C5.801,15.27 4.655,15.492 3.773,16.237 L1.5,12.285 C2.573,11.871 3.317,10.999 3.317,9.991 C3.305,8.98 2.573,8.121 1.5,7.716 L3.765,3.784 C4.645,4.516 5.794,4.738 6.687,4.232 C7.555,3.722 7.939,2.637 7.735,1.5 L12.263,1.5 C12.072,2.637 12.441,3.71 13.314,4.22 C14.206,4.73 15.343,4.516 16.225,3.794 L18.487,7.714 C17.404,8.117 16.661,8.988 16.67,10.009 C16.672,11.018 17.415,11.88 18.488,12.285 L18.488,12.285 Z" data-v-e5b6029f></path></svg></button>`);
      if (isPanelOpen.value) {
        _push(`<div class="fixed top-0 left-0 right-0 border-b-2 border-slate-200 dark:border-slate-800 bg-slate-700 bg-opacity-90 dark:bg-opacity-90 shadow-md font-semibold text-center text-lg z-40 h-12 py-2 overflow-y-auto flex items-center justify-center space-x-4" data-v-e5b6029f>`);
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.parameters.index"),
          title: unref(t)("parameters"),
          class: {
            "active-link": _ctx.route().current("admin.parameters.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M15.9,18.45C17.25,18.45 18.35,17.35 18.35,16C18.35,14.65 17.25,13.55 15.9,13.55C14.54,13.55 13.45,14.65 13.45,16C13.45,17.35 14.54,18.45 15.9,18.45M21.1,16.68L22.58,17.84C22.71,17.95 22.75,18.13 22.66,18.29L21.26,20.71C21.17,20.86 21,20.92 20.83,20.86L19.09,20.16C18.73,20.44 18.33,20.67 17.91,20.85L17.64,22.7C17.62,22.87 17.47,23 17.3,23H14.5C14.32,23 14.18,22.87 14.15,22.7L13.89,20.85C13.46,20.67 13.07,20.44 12.71,20.16L10.96,20.86C10.81,20.92 10.62,20.86 10.54,20.71L9.14,18.29C9.05,18.13 9.09,17.95 9.22,17.84L10.7,16.68L10.65,16L10.7,15.31L9.22,14.16C9.09,14.05 9.05,13.86 9.14,13.71L10.54,11.29C10.62,11.13 10.81,11.07 10.96,11.13L12.71,11.84C13.07,11.56 13.46,11.32 13.89,11.15L14.15,9.29C14.18,9.13 14.32,9 14.5,9H17.3C17.47,9 17.62,9.13 17.64,9.29L17.91,11.15C18.33,11.32 18.73,11.56 19.09,11.84L20.83,11.13C21,11.07 21.17,11.13 21.26,11.29L22.66,13.71C22.75,13.86 22.71,14.05 22.58,14.16L21.1,15.31L21.15,16L21.1,16.68M6.69,8.07C7.56,8.07 8.26,7.37 8.26,6.5C8.26,5.63 7.56,4.92 6.69,4.92A1.58,1.58 0 0,0 5.11,6.5C5.11,7.37 5.82,8.07 6.69,8.07M10.03,6.94L11,7.68C11.07,7.75 11.09,7.87 11.03,7.97L10.13,9.53C10.08,9.63 9.96,9.67 9.86,9.63L8.74,9.18L8,9.62L7.81,10.81C7.79,10.92 7.7,11 7.59,11H5.79C5.67,11 5.58,10.92 5.56,10.81L5.4,9.62L4.64,9.18L3.5,9.63C3.41,9.67 3.3,9.63 3.24,9.53L2.34,7.97C2.28,7.87 2.31,7.75 2.39,7.68L3.34,6.94L3.31,6.5L3.34,6.06L2.39,5.32C2.31,5.25 2.28,5.13 2.34,5.03L3.24,3.47C3.3,3.37 3.41,3.33 3.5,3.37L4.63,3.82L5.4,3.38L5.56,2.19C5.58,2.08 5.67,2 5.79,2H7.59C7.7,2 7.79,2.08 7.81,2.19L8,3.38L8.74,3.82L9.86,3.37C9.96,3.33 10.08,3.37 10.13,3.47L11.03,5.03C11.09,5.13 11.07,5.25 11,5.32L10.03,6.06L10.06,6.5L10.03,6.94Z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M15.9,18.45C17.25,18.45 18.35,17.35 18.35,16C18.35,14.65 17.25,13.55 15.9,13.55C14.54,13.55 13.45,14.65 13.45,16C13.45,17.35 14.54,18.45 15.9,18.45M21.1,16.68L22.58,17.84C22.71,17.95 22.75,18.13 22.66,18.29L21.26,20.71C21.17,20.86 21,20.92 20.83,20.86L19.09,20.16C18.73,20.44 18.33,20.67 17.91,20.85L17.64,22.7C17.62,22.87 17.47,23 17.3,23H14.5C14.32,23 14.18,22.87 14.15,22.7L13.89,20.85C13.46,20.67 13.07,20.44 12.71,20.16L10.96,20.86C10.81,20.92 10.62,20.86 10.54,20.71L9.14,18.29C9.05,18.13 9.09,17.95 9.22,17.84L10.7,16.68L10.65,16L10.7,15.31L9.22,14.16C9.09,14.05 9.05,13.86 9.14,13.71L10.54,11.29C10.62,11.13 10.81,11.07 10.96,11.13L12.71,11.84C13.07,11.56 13.46,11.32 13.89,11.15L14.15,9.29C14.18,9.13 14.32,9 14.5,9H17.3C17.47,9 17.62,9.13 17.64,9.29L17.91,11.15C18.33,11.32 18.73,11.56 19.09,11.84L20.83,11.13C21,11.07 21.17,11.13 21.26,11.29L22.66,13.71C22.75,13.86 22.71,14.05 22.58,14.16L21.1,15.31L21.15,16L21.1,16.68M6.69,8.07C7.56,8.07 8.26,7.37 8.26,6.5C8.26,5.63 7.56,4.92 6.69,4.92A1.58,1.58 0 0,0 5.11,6.5C5.11,7.37 5.82,8.07 6.69,8.07M10.03,6.94L11,7.68C11.07,7.75 11.09,7.87 11.03,7.97L10.13,9.53C10.08,9.63 9.96,9.67 9.86,9.63L8.74,9.18L8,9.62L7.81,10.81C7.79,10.92 7.7,11 7.59,11H5.79C5.67,11 5.58,10.92 5.56,10.81L5.4,9.62L4.64,9.18L3.5,9.63C3.41,9.67 3.3,9.63 3.24,9.53L2.34,7.97C2.28,7.87 2.31,7.75 2.39,7.68L3.34,6.94L3.31,6.5L3.34,6.06L2.39,5.32C2.31,5.25 2.28,5.13 2.34,5.03L3.24,3.47C3.3,3.37 3.41,3.33 3.5,3.37L4.63,3.82L5.4,3.38L5.56,2.19C5.58,2.08 5.67,2 5.79,2H7.59C7.7,2 7.79,2.08 7.81,2.19L8,3.38L8.74,3.82L9.86,3.37C9.96,3.33 10.08,3.37 10.13,3.47L11.03,5.03C11.09,5.13 11.07,5.25 11,5.32L10.03,6.06L10.06,6.5L10.03,6.94Z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.logs.index"),
          title: unref(t)("logs"),
          class: {
            "active-link": _ctx.route().current("admin.logs.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M14,0H3A1,1,0,0,0,2,1V23a1,1,0,0,0,1,1H21a1,1,0,0,0,1-1V8H15a1,1,0,0,1-1-1ZM5.5,17h13a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,5.5,17Zm0-5h13a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,5.5,12Zm5-3h-5A.5.5,0,0,1,5,8.5v-1A.5.5,0,0,1,5.5,7h5a.5.5,0,0,1,.5.5v1A.5.5,0,0,1,10.5,9Z" data-v-e5b6029f${_scopeId}></path><polygon class="fill-current text-cyan-400" points="21.414 6 16 6 16 0.586 21.414 6" data-v-e5b6029f${_scopeId}></polygon></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M14,0H3A1,1,0,0,0,2,1V23a1,1,0,0,0,1,1H21a1,1,0,0,0,1-1V8H15a1,1,0,0,1-1-1ZM5.5,17h13a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,5.5,17Zm0-5h13a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,5.5,12Zm5-3h-5A.5.5,0,0,1,5,8.5v-1A.5.5,0,0,1,5.5,7h5a.5.5,0,0,1,.5.5v1A.5.5,0,0,1,10.5,9Z"
                  }),
                  createVNode("polygon", {
                    class: "fill-current text-cyan-400",
                    points: "21.414 6 16 6 16 0.586 21.414 6"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.phpinfo.index"),
          title: "phpinfo",
          class: {
            "active-link": _ctx.route().current("admin.phpinfo.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M20,1H4c-1.654,0-3,1.346-3,3V20c0,1.654,1.346,3,3,3H20c1.654,0,3-1.346,3-3V4c0-1.654-1.346-3-3-3Zm-7,16c0,.553-.448,1-1,1s-1-.447-1-1v-6c0-.553,.448-1,1-1s1,.447,1,1v6Zm-1-9c-.69,0-1.25-.56-1.25-1.25s.56-1.25,1.25-1.25,1.25,.56,1.25,1.25-.56,1.25-1.25,1.25Z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M20,1H4c-1.654,0-3,1.346-3,3V20c0,1.654,1.346,3,3,3H20c1.654,0,3-1.346,3-3V4c0-1.654-1.346-3-3-3Zm-7,16c0,.553-.448,1-1,1s-1-.447-1-1v-6c0-.553,.448-1,1-1s1,.447,1,1v6Zm-1-9c-.69,0-1.25-.56-1.25-1.25s.56-1.25,1.25-1.25,1.25,.56,1.25,1.25-.56,1.25-1.25,1.25Z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.composer.index"),
          title: "composer",
          class: {
            "active-link": _ctx.route().current("admin.composer.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M10,7H2A1,1,0,0,1,1,6V2A1,1,0,0,1,2,1h8a1,1,0,0,1,1,1V6A1,1,0,0,1,10,7Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="M10,23H2a1,1,0,0,1-1-1V18a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1v4A1,1,0,0,1,10,23Z" data-v-e5b6029f${_scopeId}></path><rect class="fill-current text-cyan-400" x="5" y="8" width="2" height="8" data-v-e5b6029f${_scopeId}></rect><path class="fill-current text-cyan-400" d="M19,7H17V5H12V3h6a1,1,0,0,1,1,1Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="M18,21H12V19h5V17h2v3A1,1,0,0,1,18,21Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="M18,16a1,1,0,0,1-.515-.143l-5-3a1,1,0,0,1,0-1.714l5-3a1,1,0,0,1,1.03,0l5,3a1,1,0,0,1,0,1.714l-5,3A1,1,0,0,1,18,16Z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M10,7H2A1,1,0,0,1,1,6V2A1,1,0,0,1,2,1h8a1,1,0,0,1,1,1V6A1,1,0,0,1,10,7Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M10,23H2a1,1,0,0,1-1-1V18a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1v4A1,1,0,0,1,10,23Z"
                  }),
                  createVNode("rect", {
                    class: "fill-current text-cyan-400",
                    x: "5",
                    y: "8",
                    width: "2",
                    height: "8"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M19,7H17V5H12V3h6a1,1,0,0,1,1,1Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M18,21H12V19h5V17h2v3A1,1,0,0,1,18,21Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M18,16a1,1,0,0,1-.515-.143l-5-3a1,1,0,0,1,0-1.714l5-3a1,1,0,0,1,1.03,0l5,3a1,1,0,0,1,0,1.714l-5,3A1,1,0,0,1,18,16Z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.package.index"),
          title: "package",
          class: {
            "active-link": _ctx.route().current("admin.package.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M10,7H2A1,1,0,0,1,1,6V2A1,1,0,0,1,2,1h8a1,1,0,0,1,1,1V6A1,1,0,0,1,10,7Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="M10,23H2a1,1,0,0,1-1-1V18a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1v4A1,1,0,0,1,10,23Z" data-v-e5b6029f${_scopeId}></path><rect class="fill-current text-cyan-400" x="5" y="8" width="2" height="8" data-v-e5b6029f${_scopeId}></rect><path class="fill-current text-cyan-400" d="M19,7H17V5H12V3h6a1,1,0,0,1,1,1Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="M18,21H12V19h5V17h2v3A1,1,0,0,1,18,21Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="M18,16a1,1,0,0,1-.515-.143l-5-3a1,1,0,0,1,0-1.714l5-3a1,1,0,0,1,1.03,0l5,3a1,1,0,0,1,0,1.714l-5,3A1,1,0,0,1,18,16Z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M10,7H2A1,1,0,0,1,1,6V2A1,1,0,0,1,2,1h8a1,1,0,0,1,1,1V6A1,1,0,0,1,10,7Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M10,23H2a1,1,0,0,1-1-1V18a1,1,0,0,1,1-1h8a1,1,0,0,1,1,1v4A1,1,0,0,1,10,23Z"
                  }),
                  createVNode("rect", {
                    class: "fill-current text-cyan-400",
                    x: "5",
                    y: "8",
                    width: "2",
                    height: "8"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M19,7H17V5H12V3h6a1,1,0,0,1,1,1Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M18,21H12V19h5V17h2v3A1,1,0,0,1,18,21Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M18,16a1,1,0,0,1-.515-.143l-5-3a1,1,0,0,1,0-1.714l5-3a1,1,0,0,1,1.03,0l5,3a1,1,0,0,1,0,1.714l-5,3A1,1,0,0,1,18,16Z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.env.index"),
          title: "env",
          class: {
            "active-link": _ctx.route().current("admin.env.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M18.293,.293L10.257,8.329C5.507,6.925,.49,10.125-.009,15.33c-.304,3.169,1.397,6.299,4.222,7.767,4.639,2.411,10.053-.058,11.458-4.808,.439-1.484,.439-3.062,0-4.546l2.036-2.036c.188-.188,.293-.442,.293-.707v-2c0-.552,.448-1,1-1h2c.265,0,.52-.105,.707-.293l2-2c.188-.188,.293-.442,.293-.707V1c0-.552-.448-1-1-1h-4c-.265,0-.52,.105-.707,.293ZM8,19c-1.657,0-3-1.343-3-3s1.343-3,3-3,3,1.343,3,3-1.343,3-3,3Z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M18.293,.293L10.257,8.329C5.507,6.925,.49,10.125-.009,15.33c-.304,3.169,1.397,6.299,4.222,7.767,4.639,2.411,10.053-.058,11.458-4.808,.439-1.484,.439-3.062,0-4.546l2.036-2.036c.188-.188,.293-.442,.293-.707v-2c0-.552,.448-1,1-1h2c.265,0,.52-.105,.707-.293l2-2c.188-.188,.293-.442,.293-.707V1c0-.552-.448-1-1-1h-4c-.265,0-.52,.105-.707,.293ZM8,19c-1.657,0-3-1.343-3-3s1.343-3,3-3,3,1.343,3,3-1.343,3-3,3Z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.backup.index"),
          title: unref(t)("backups"),
          class: {
            "active-link": _ctx.route().current("admin.backup.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M12,10C8.2,10,4.3,9.3,2,7.6V12c0,2.7,5.2,4,10,4s10-1.3,10-4V7.6C19.7,9.3,15.8,10,12,10z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="M12,18c-3.8,0-7.7-0.7-10-2.4V20c0,2.7,5.2,4,10,4s10-1.3,10-4v-4.4C19.7,17.3,15.8,18,12,18z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-600" d="M12,0C7.2,0,2,1.3,2,4s5.2,4,10,4s10-1.3,10-4S16.8,0,12,0z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M12,10C8.2,10,4.3,9.3,2,7.6V12c0,2.7,5.2,4,10,4s10-1.3,10-4V7.6C19.7,9.3,15.8,10,12,10z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M12,18c-3.8,0-7.7-0.7-10-2.4V20c0,2.7,5.2,4,10,4s10-1.3,10-4v-4.4C19.7,17.3,15.8,18,12,18z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-600",
                    d: "M12,0C7.2,0,2,1.3,2,4s5.2,4,10,4s10-1.3,10-4S16.8,0,12,0z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.files.index"),
          title: unref(t)("archive"),
          class: {
            "active-link": _ctx.route().current("admin.files.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M23.746,16.564l-1.62-.915-8.9,5.028a2.5,2.5,0,0,1-2.459,0l-8.9-5.029-1.62.915a.5.5,0,0,0,0,.872l11.5,6.5a.5.5,0,0,0,.492,0l11.5-6.5a.5.5,0,0,0,0-.872Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="M23.746,11.564l-1.62-.915-8.9,5.028a2.5,2.5,0,0,1-2.459,0l-8.9-5.029-1.62.915a.5.5,0,0,0,0,.872l11.5,6.5a.5.5,0,0,0,.492,0l11.5-6.5a.5.5,0,0,0,0-.872Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-600" d="M23.746,6.564l-11.5-6.5a.507.507,0,0,0-.492,0l-11.5,6.5a.5.5,0,0,0,0,.872l11.5,6.5a.5.5,0,0,0,.492,0l11.5-6.5a.5.5,0,0,0,0-.872Z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M23.746,16.564l-1.62-.915-8.9,5.028a2.5,2.5,0,0,1-2.459,0l-8.9-5.029-1.62.915a.5.5,0,0,0,0,.872l11.5,6.5a.5.5,0,0,0,.492,0l11.5-6.5a.5.5,0,0,0,0-.872Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M23.746,11.564l-1.62-.915-8.9,5.028a2.5,2.5,0,0,1-2.459,0l-8.9-5.029-1.62.915a.5.5,0,0,0,0,.872l11.5,6.5a.5.5,0,0,0,.492,0l11.5-6.5a.5.5,0,0,0,0-.872Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-600",
                    d: "M23.746,6.564l-11.5-6.5a.507.507,0,0,0-.492,0l-11.5,6.5a.5.5,0,0,0,0,.872l11.5,6.5a.5.5,0,0,0,.492,0l11.5-6.5a.5.5,0,0,0,0-.872Z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.components.index"),
          title: unref(t)("components"),
          class: {
            "active-link": _ctx.route().current("admin.components.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><circle class="fill-current text-cyan-600" cx="16" cy="8" r="8" data-v-e5b6029f${_scopeId}></circle><circle class="fill-current text-cyan-400" cx="8" cy="16" r="8" data-v-e5b6029f${_scopeId}></circle></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("circle", {
                    class: "fill-current text-cyan-600",
                    cx: "16",
                    cy: "8",
                    r: "8"
                  }),
                  createVNode("circle", {
                    class: "fill-current text-cyan-400",
                    cx: "8",
                    cy: "16",
                    r: "8"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.robot.index"),
          title: unref(t)("robot"),
          class: {
            "active-link": _ctx.route().current("admin.robot.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M17,23H4c-1.654,0-3-1.346-3-3V7c0-1.654,1.346-3,3-3h5c.552,0,1,.448,1,1s-.448,1-1,1H4c-.551,0-1,.449-1,1v13c0,.551,.449,1,1,1h13c.551,0,1-.449,1-1v-5c0-.552,.448-1,1-1s1,.448,1,1v5c0,1.654-1.346,3-3,3Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="M20.414,8l1.585-1.585c.378-.377,.587-.879,.587-1.414,0-.535-.208-1.037-.586-1.416l-1.585-1.585c-.377-.378-.879-.587-1.414-.587h0c-.534,0-1.037,.208-1.415,.586l-1.586,1.586,4.414,4.414Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-600" d="M14.586,5l-6.293,6.293c-.096,.096-.171,.21-.222,.335l-2,5c-.148,.372-.061,.796,.222,1.079,.191,.191,.447,.293,.707,.293,.125,0,.251-.023,.372-.071l5-2c.125-.05,.24-.126,.335-.222l6.293-6.293-4.414-4.414Z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M17,23H4c-1.654,0-3-1.346-3-3V7c0-1.654,1.346-3,3-3h5c.552,0,1,.448,1,1s-.448,1-1,1H4c-.551,0-1,.449-1,1v13c0,.551,.449,1,1,1h13c.551,0,1-.449,1-1v-5c0-.552,.448-1,1-1s1,.448,1,1v5c0,1.654-1.346,3-3,3Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M20.414,8l1.585-1.585c.378-.377,.587-.879,.587-1.414,0-.535-.208-1.037-.586-1.416l-1.585-1.585c-.377-.378-.879-.587-1.414-.587h0c-.534,0-1.037,.208-1.415,.586l-1.586,1.586,4.414,4.414Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-600",
                    d: "M14.586,5l-6.293,6.293c-.096,.096-.171,.21-.222,.335l-2,5c-.148,.372-.061,.796,.222,1.079,.191,.191,.447,.293,.707,.293,.125,0,.251-.023,.372-.071l5-2c.125-.05,.24-.126,.335-.222l6.293-6.293-4.414-4.414Z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.sitemap.index"),
          title: "sitemap.xml",
          class: {
            "active-link": _ctx.route().current("admin.sitemap.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M24,8V4H20V5H15.858A3.981,3.981,0,0,0,8.142,5H4V4H0V8H4V7H8.142a3.9,3.9,0,0,0,.247.692A10.007,10.007,0,0,0,2.055,16H1a1,1,0,0,0-1,1v4a1,1,0,0,0,1,1H5a1,1,0,0,0,1-1V17a1,1,0,0,0-1-1H4.069A8.015,8.015,0,0,1,9.775,9.321a3.985,3.985,0,0,0,4.45,0A8.015,8.015,0,0,1,19.931,16H19a1,1,0,0,0-1,1v4a1,1,0,0,0,1,1h4a1,1,0,0,0,1-1V17a1,1,0,0,0-1-1H21.945a10.007,10.007,0,0,0-6.334-8.308A3.9,3.9,0,0,0,15.858,7H20V8ZM12,4a2,2,0,1,1-2,2A2,2,0,0,1,12,4Z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M24,8V4H20V5H15.858A3.981,3.981,0,0,0,8.142,5H4V4H0V8H4V7H8.142a3.9,3.9,0,0,0,.247.692A10.007,10.007,0,0,0,2.055,16H1a1,1,0,0,0-1,1v4a1,1,0,0,0,1,1H5a1,1,0,0,0,1-1V17a1,1,0,0,0-1-1H4.069A8.015,8.015,0,0,1,9.775,9.321a3.985,3.985,0,0,0,4.45,0A8.015,8.015,0,0,1,19.931,16H19a1,1,0,0,0-1,1v4a1,1,0,0,0,1,1h4a1,1,0,0,0,1-1V17a1,1,0,0,0-1-1H21.945a10.007,10.007,0,0,0-6.334-8.308A3.9,3.9,0,0,0,15.858,7H20V8ZM12,4a2,2,0,1,1-2,2A2,2,0,0,1,12,4Z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.reports.index"),
          title: unref(t)("reports"),
          class: {
            "active-link": _ctx.route().current("admin.reports.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-600" d="M16 13v4H8v-4H0l3-9h18l3 9h-8Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="m23.72 12 .229.686A.984.984 0 0 1 24 13v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1v-8c0-.107.017-.213.051-.314L.28 12H8v4h8v-4H23.72ZM13 0v7h3l-4 5-4-5h3V0h2Z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-600",
                    d: "M16 13v4H8v-4H0l3-9h18l3 9h-8Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "m23.72 12 .229.686A.984.984 0 0 1 24 13v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1v-8c0-.107.017-.213.051-.314L.28 12H8v4h8v-4H23.72ZM13 0v7h3l-4 5-4-5h3V0h2Z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.imagePresets.index"),
          title: unref(t)("imagePresets"),
          class: {
            "active-link": _ctx.route().current("admin.imagePresets.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 512 512" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M480 128V96h20c6.627 0 12-5.373 12-12V44c0-6.627-5.373-12-12-12h-40c-6.627 0-12 5.373-12 12v20H64V44c0-6.627-5.373-12-12-12H12C5.373 32 0 37.373 0 44v40c0 6.627 5.373 12 12 12h20v320H12c-6.627 0-12 5.373-12 12v40c0 6.627 5.373 12 12 12h40c6.627 0 12-5.373 12-12v-20h384v20c0 6.627 5.373 12 12 12h40c6.627 0 12-5.373 12-12v-40c0-6.627-5.373-12-12-12h-20V128zM96 276V140c0-6.627 5.373-12 12-12h168c6.627 0 12 5.373 12 12v136c0 6.627-5.373 12-12 12H108c-6.627 0-12-5.373-12-12zm320 96c0 6.627-5.373 12-12 12H236c-6.627 0-12-5.373-12-12v-52h72c13.255 0 24-10.745 24-24v-72h84c6.627 0 12 5.373 12 12v136z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 512 512"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M480 128V96h20c6.627 0 12-5.373 12-12V44c0-6.627-5.373-12-12-12h-40c-6.627 0-12 5.373-12 12v20H64V44c0-6.627-5.373-12-12-12H12C5.373 32 0 37.373 0 44v40c0 6.627 5.373 12 12 12h20v320H12c-6.627 0-12 5.373-12 12v40c0 6.627 5.373 12 12 12h40c6.627 0 12-5.373 12-12v-20h384v20c0 6.627 5.373 12 12 12h40c6.627 0 12-5.373 12-12v-40c0-6.627-5.373-12-12-12h-20V128zM96 276V140c0-6.627 5.373-12 12-12h168c6.627 0 12 5.373 12 12v136c0 6.627-5.373 12-12 12H108c-6.627 0-12-5.373-12-12zm320 96c0 6.627-5.373 12-12 12H236c-6.627 0-12-5.373-12-12v-52h72c13.255 0 24-10.745 24-24v-72h84c6.627 0 12 5.373 12 12v136z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.locations.index"),
          title: unref(t)("locations"),
          class: {
            "active-link": _ctx.route().current("admin.locations.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 576 512" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M288 0c-69.59 0-126 56.41-126 126 0 56.26 82.35 158.8 113.9 196.02 6.39 7.54 17.82 7.54 24.2 0C331.65 284.8 414 182.26 414 126 414 56.41 357.59 0 288 0zm0 168c-23.2 0-42-18.8-42-42s18.8-42 42-42 42 18.8 42 42-18.8 42-42 42zM20.12 215.95A32.006 32.006 0 0 0 0 245.66v250.32c0 11.32 11.43 19.06 21.94 14.86L160 448V214.92c-8.84-15.98-16.07-31.54-21.25-46.42L20.12 215.95zM288 359.67c-14.07 0-27.38-6.18-36.51-16.96-19.66-23.2-40.57-49.62-59.49-76.72v182l192 64V266c-18.92 27.09-39.82 53.52-59.49 76.72-9.13 10.77-22.44 16.95-36.51 16.95zm266.06-198.51L416 224v288l139.88-55.95A31.996 31.996 0 0 0 576 426.34V176.02c0-11.32-11.43-19.06-21.94-14.86z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 576 512"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M288 0c-69.59 0-126 56.41-126 126 0 56.26 82.35 158.8 113.9 196.02 6.39 7.54 17.82 7.54 24.2 0C331.65 284.8 414 182.26 414 126 414 56.41 357.59 0 288 0zm0 168c-23.2 0-42-18.8-42-42s18.8-42 42-42 42 18.8 42 42-18.8 42-42 42zM20.12 215.95A32.006 32.006 0 0 0 0 245.66v250.32c0 11.32 11.43 19.06 21.94 14.86L160 448V214.92c-8.84-15.98-16.07-31.54-21.25-46.42L20.12 215.95zM288 359.67c-14.07 0-27.38-6.18-36.51-16.96-19.66-23.2-40.57-49.62-59.49-76.72v182l192 64V266c-18.92 27.09-39.82 53.52-59.49 76.72-9.13 10.77-22.44 16.95-36.51 16.95zm266.06-198.51L416 224v288l139.88-55.95A31.996 31.996 0 0 0 576 426.34V176.02c0-11.32-11.43-19.06-21.94-14.86z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.localization.index"),
          title: unref(t)("localization"),
          class: {
            "active-link": _ctx.route().current("admin.localization.index"),
            "base-link": true
          }
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24" data-v-e5b6029f${_scopeId}><path class="fill-current text-cyan-400" d="M17,11.94q-.16.626-.922,3.091h1.86q-.719-2.309-.808-2.612C17.072,12.218,17.028,12.058,17,11.94Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="M22,8H12a2,2,0,0,0-2,2v9a2,2,0,0,0,2,2h6l4,3V21a2,2,0,0,0,2-2V10A2,2,0,0,0,22,8ZM18.819,18,18.3,16.3H15.7L15.181,18H13.55l2.52-7.168h1.85L20.45,18Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="M12,0H2A2,2,0,0,0,0,2v9a2,2,0,0,0,2,2v3l4-3H9V10a2.828,2.828,0,0,1,.04-.393A9.23,9.23,0,0,1,7.056,8.529,10.268,10.268,0,0,1,3.874,10a4.633,4.633,0,0,0-.768-1.415A8.7,8.7,0,0,0,5.944,7.537,7,7,0,0,1,4.913,6.074a7.077,7.077,0,0,1-.552-1.367c-.471,0-.743.016-1.143.048V3.308a9.853,9.853,0,0,0,1.159.056H6.145v-.48a2.482,2.482,0,0,0-.048-.5H7.7a2.445,2.445,0,0,0-.048.487v.488h1.9a9.774,9.774,0,0,0,1.159-.056V4.755c-.352-.032-.664-.048-1.135-.048A6.278,6.278,0,0,1,9.039,6.25a5.924,5.924,0,0,1-.888,1.3A6.958,6.958,0,0,0,9.617,8.2,2.987,2.987,0,0,1,12,7h2V2A2,2,0,0,0,12,0Z" data-v-e5b6029f${_scopeId}></path><path class="fill-current text-cyan-400" d="M7.016,6.642a4.51,4.51,0,0,0,1-1.935H5.9A4.562,4.562,0,0,0,7.016,6.642Z" data-v-e5b6029f${_scopeId}></path></svg>`);
            } else {
              return [
                (openBlock(), createBlock("svg", {
                  class: "shrink-0 h-4 w-4",
                  viewBox: "0 0 24 24"
                }, [
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M17,11.94q-.16.626-.922,3.091h1.86q-.719-2.309-.808-2.612C17.072,12.218,17.028,12.058,17,11.94Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M22,8H12a2,2,0,0,0-2,2v9a2,2,0,0,0,2,2h6l4,3V21a2,2,0,0,0,2-2V10A2,2,0,0,0,22,8ZM18.819,18,18.3,16.3H15.7L15.181,18H13.55l2.52-7.168h1.85L20.45,18Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M12,0H2A2,2,0,0,0,0,2v9a2,2,0,0,0,2,2v3l4-3H9V10a2.828,2.828,0,0,1,.04-.393A9.23,9.23,0,0,1,7.056,8.529,10.268,10.268,0,0,1,3.874,10a4.633,4.633,0,0,0-.768-1.415A8.7,8.7,0,0,0,5.944,7.537,7,7,0,0,1,4.913,6.074a7.077,7.077,0,0,1-.552-1.367c-.471,0-.743.016-1.143.048V3.308a9.853,9.853,0,0,0,1.159.056H6.145v-.48a2.482,2.482,0,0,0-.048-.5H7.7a2.445,2.445,0,0,0-.048.487v.488h1.9a9.774,9.774,0,0,0,1.159-.056V4.755c-.352-.032-.664-.048-1.135-.048A6.278,6.278,0,0,1,9.039,6.25a5.924,5.924,0,0,1-.888,1.3A6.958,6.958,0,0,0,9.617,8.2,2.987,2.987,0,0,1,12,7h2V2A2,2,0,0,0,12,0Z"
                  }),
                  createVNode("path", {
                    class: "fill-current text-cyan-400",
                    d: "M7.016,6.642a4.51,4.51,0,0,0,1-1.935H5.9A4.562,4.562,0,0,0,7.016,6.642Z"
                  })
                ]))
              ];
            }
          }),
          _: 1
        }, _parent));
        _push(`</div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$g = _sfc_main$g.setup;
_sfc_main$g.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Partials/Admin/TopPanel.vue");
  return _sfc_setup$g ? _sfc_setup$g(props, ctx) : void 0;
};
const TopPanel = /* @__PURE__ */ _export_sfc(_sfc_main$g, [["__scopeId", "data-v-e5b6029f"]]);
const _sfc_main$f = {
  __name: "Header",
  __ssrInlineRender: true,
  props: {
    title: String,
    currentTime: String,
    showingNavigationDropdown: Boolean
  },
  emits: ["toggleNavigationDropdown"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const switchToTeam = (team) => {
      router.put(
        route("current-team.update"),
        {
          team_id: team.id
        },
        {
          preserveState: false
        }
      );
    };
    const logout = () => {
      router.post(route("logout"));
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[--><div class="sticky top-0 bg-slate-200 dark:bg-slate-800 border-x border-slate-400 dark:border-slate-900 shadow-md shadow-slate-400 dark:shadow-slate-900 z-20">`);
      _push(ssrRenderComponent(TopPanel, null, null, _parent));
      _push(`<nav class="border-b border-gray-200 dark:border-gray-800"><div class="max-w-full mx-auto px-4 sm:px-0"><div class="flex items-center justify-between h-10"><div class="flex items-center justify-center"><div class="shrink-0 flex items-center md:hidden">`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route("dashboard")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(ApplicationMark, { class: "block h-9 w-auto" }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(ApplicationMark, { class: "block h-9 w-auto" })
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div></div><div class="w-full flex justify-between sm:items-center"><div class="hidden sm:block ms-3 relative">`);
      _push(ssrRenderComponent(_sfc_main$j, {
        align: "right",
        width: "60",
        class: "relative z-10"
      }, {
        trigger: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            if (_ctx.$page.props.jetstream.managesProfilePhotos) {
              _push2(`<button class="flex items-center px-2 py-0.5 font-semibold text-sm text-sky-600 dark:text-slate-100 border-2 border-transparent rounded-full focus:outline-none focus:border-gray-400 transition"${_scopeId}><img class="h-8 w-8 mr-2 rounded-full object-cover"${ssrRenderAttr("src", _ctx.$page.props.auth.user.profile_photo_url)}${ssrRenderAttr("alt", _ctx.$page.props.auth.user.name)}${_scopeId}><span${_scopeId}>${ssrInterpolate(_ctx.$page.props.auth.user.email)}</span></button>`);
            } else {
              _push2(`<span class="inline-flex rounded-md"${_scopeId}><button type="button" class="inline-flex items-center bg-white active:bg-gray-50 px-3 py-2 border border-transparent rounded-md text-sm leading-4 font-medium text-slate-500 hover:text-slate-700 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150"${_scopeId}>${ssrInterpolate(_ctx.$page.props.auth.user.name)} <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"${_scopeId}></path></svg></button></span>`);
            }
          } else {
            return [
              _ctx.$page.props.jetstream.managesProfilePhotos ? (openBlock(), createBlock("button", {
                key: 0,
                class: "flex items-center px-2 py-0.5 font-semibold text-sm text-sky-600 dark:text-slate-100 border-2 border-transparent rounded-full focus:outline-none focus:border-gray-400 transition"
              }, [
                createVNode("img", {
                  class: "h-8 w-8 mr-2 rounded-full object-cover",
                  src: _ctx.$page.props.auth.user.profile_photo_url,
                  alt: _ctx.$page.props.auth.user.name
                }, null, 8, ["src", "alt"]),
                createVNode("span", null, toDisplayString(_ctx.$page.props.auth.user.email), 1)
              ])) : (openBlock(), createBlock("span", {
                key: 1,
                class: "inline-flex rounded-md"
              }, [
                createVNode("button", {
                  type: "button",
                  class: "inline-flex items-center bg-white active:bg-gray-50 px-3 py-2 border border-transparent rounded-md text-sm leading-4 font-medium text-slate-500 hover:text-slate-700 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150"
                }, [
                  createTextVNode(toDisplayString(_ctx.$page.props.auth.user.name) + " ", 1),
                  (openBlock(), createBlock("svg", {
                    class: "ms-2 -me-0.5 h-4 w-4",
                    xmlns: "http://www.w3.org/2000/svg",
                    fill: "none",
                    viewBox: "0 0 24 24",
                    "stroke-width": "1.5",
                    stroke: "currentColor"
                  }, [
                    createVNode("path", {
                      "stroke-linecap": "round",
                      "stroke-linejoin": "round",
                      d: "M19.5 8.25l-7.5 7.5-7.5-7.5"
                    })
                  ]))
                ])
              ]))
            ];
          }
        }),
        content: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="block px-4 py-2 text-sm text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("accountManagement"))}</div>`);
            _push2(ssrRenderComponent(_sfc_main$k, {
              href: _ctx.route("profile.show")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("profile"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("profile")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            if (_ctx.$page.props.jetstream.hasApiFeatures) {
              _push2(ssrRenderComponent(_sfc_main$k, {
                href: _ctx.route("api-tokens.index")
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(unref(t)("apiTokens"))}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(unref(t)("apiTokens")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="border-t border-gray-200"${_scopeId}></div><form${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$k, { as: "button" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("logout"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("logout")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</form>`);
          } else {
            return [
              createVNode("div", { class: "block px-4 py-2 text-sm text-slate-400" }, toDisplayString(unref(t)("accountManagement")), 1),
              createVNode(_sfc_main$k, {
                href: _ctx.route("profile.show")
              }, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("profile")), 1)
                ]),
                _: 1
              }, 8, ["href"]),
              _ctx.$page.props.jetstream.hasApiFeatures ? (openBlock(), createBlock(_sfc_main$k, {
                key: 0,
                href: _ctx.route("api-tokens.index")
              }, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("apiTokens")), 1)
                ]),
                _: 1
              }, 8, ["href"])) : createCommentVNode("", true),
              createVNode("div", { class: "border-t border-gray-200" }),
              createVNode("form", {
                onSubmit: withModifiers(logout, ["prevent"])
              }, [
                createVNode(_sfc_main$k, { as: "button" }, {
                  default: withCtx(() => [
                    createTextVNode(toDisplayString(unref(t)("logout")), 1)
                  ]),
                  _: 1
                })
              ], 32)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div><div class="mx-2 flex items-center">`);
      _push(ssrRenderComponent(_sfc_main$l, { class: "relative z-10" }, null, _parent));
      _push(`</div><div class="hidden sm:block sm:me-8 relative">`);
      if (_ctx.$page.props.jetstream.hasTeamFeatures) {
        _push(ssrRenderComponent(_sfc_main$j, {
          align: "right",
          width: "60",
          class: "relative z-10"
        }, {
          trigger: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="inline-flex rounded-md"${_scopeId}><button type="button" class="inline-flex items-center bg-white dark:bg-slate-500 active:bg-gray-50 px-2 py-1 border border-transparent rounded-xs text-sm leading-4 font-medium text-slate-500 dark:text-slate-100 hover:text-slate-700 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150"${_scopeId}>${ssrInterpolate(_ctx.$page.props.auth.user.current_team.name)} <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"${_scopeId}><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"${_scopeId}></path></svg></button></span>`);
            } else {
              return [
                createVNode("span", { class: "inline-flex rounded-md" }, [
                  createVNode("button", {
                    type: "button",
                    class: "inline-flex items-center bg-white dark:bg-slate-500 active:bg-gray-50 px-2 py-1 border border-transparent rounded-xs text-sm leading-4 font-medium text-slate-500 dark:text-slate-100 hover:text-slate-700 focus:outline-none focus:bg-gray-50 transition ease-in-out duration-150"
                  }, [
                    createTextVNode(toDisplayString(_ctx.$page.props.auth.user.current_team.name) + " ", 1),
                    (openBlock(), createBlock("svg", {
                      class: "ms-2 -me-0.5 h-4 w-4",
                      xmlns: "http://www.w3.org/2000/svg",
                      fill: "none",
                      viewBox: "0 0 24 24",
                      "stroke-width": "1.5",
                      stroke: "currentColor"
                    }, [
                      createVNode("path", {
                        "stroke-linecap": "round",
                        "stroke-linejoin": "round",
                        d: "M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"
                      })
                    ]))
                  ])
                ])
              ];
            }
          }),
          content: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<div class="w-60"${_scopeId}><div class="block px-4 py-2 text-sm text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("teamManagement"))}</div>`);
              _push2(ssrRenderComponent(_sfc_main$k, {
                href: _ctx.route("teams.show", _ctx.$page.props.auth.user.current_team)
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(unref(t)("teamSettings"))}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(unref(t)("teamSettings")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              if (_ctx.$page.props.jetstream.canCreateTeams) {
                _push2(ssrRenderComponent(_sfc_main$k, {
                  href: _ctx.route("teams.create")
                }, {
                  default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                    if (_push3) {
                      _push3(`${ssrInterpolate(unref(t)("createNewTeam"))}`);
                    } else {
                      return [
                        createTextVNode(toDisplayString(unref(t)("createNewTeam")), 1)
                      ];
                    }
                  }),
                  _: 1
                }, _parent2, _scopeId));
              } else {
                _push2(`<!---->`);
              }
              if (_ctx.$page.props.auth.user.all_teams.length > 1) {
                _push2(`<!--[--><div class="w-60 border-t border-gray-200"${_scopeId}></div><div class="block px-4 py-2 text-sm text-slate-400"${_scopeId}>${ssrInterpolate(unref(t)("switchTeams"))}</div><!--[-->`);
                ssrRenderList(_ctx.$page.props.auth.user.all_teams, (team) => {
                  _push2(`<form${_scopeId}>`);
                  _push2(ssrRenderComponent(_sfc_main$k, { as: "button" }, {
                    default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                      if (_push3) {
                        _push3(`<div class="flex items-center"${_scopeId2}>`);
                        if (team.id == _ctx.$page.props.auth.user.current_team_id) {
                          _push3(`<svg class="me-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"${_scopeId2}><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"${_scopeId2}></path></svg>`);
                        } else {
                          _push3(`<!---->`);
                        }
                        _push3(`<div${_scopeId2}>${ssrInterpolate(team.name)}</div></div>`);
                      } else {
                        return [
                          createVNode("div", { class: "flex items-center" }, [
                            team.id == _ctx.$page.props.auth.user.current_team_id ? (openBlock(), createBlock("svg", {
                              key: 0,
                              class: "me-2 h-5 w-5 text-green-400",
                              xmlns: "http://www.w3.org/2000/svg",
                              fill: "none",
                              viewBox: "0 0 24 24",
                              "stroke-width": "1.5",
                              stroke: "currentColor"
                            }, [
                              createVNode("path", {
                                "stroke-linecap": "round",
                                "stroke-linejoin": "round",
                                d: "M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                              })
                            ])) : createCommentVNode("", true),
                            createVNode("div", null, toDisplayString(team.name), 1)
                          ])
                        ];
                      }
                    }),
                    _: 2
                  }, _parent2, _scopeId));
                  _push2(`</form>`);
                });
                _push2(`<!--]--><!--]-->`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            } else {
              return [
                createVNode("div", { class: "w-60" }, [
                  createVNode("div", { class: "block px-4 py-2 text-sm text-slate-400" }, toDisplayString(unref(t)("teamManagement")), 1),
                  createVNode(_sfc_main$k, {
                    href: _ctx.route("teams.show", _ctx.$page.props.auth.user.current_team)
                  }, {
                    default: withCtx(() => [
                      createTextVNode(toDisplayString(unref(t)("teamSettings")), 1)
                    ]),
                    _: 1
                  }, 8, ["href"]),
                  _ctx.$page.props.jetstream.canCreateTeams ? (openBlock(), createBlock(_sfc_main$k, {
                    key: 0,
                    href: _ctx.route("teams.create")
                  }, {
                    default: withCtx(() => [
                      createTextVNode(toDisplayString(unref(t)("createNewTeam")), 1)
                    ]),
                    _: 1
                  }, 8, ["href"])) : createCommentVNode("", true),
                  _ctx.$page.props.auth.user.all_teams.length > 1 ? (openBlock(), createBlock(Fragment, { key: 1 }, [
                    createVNode("div", { class: "w-60 border-t border-gray-200" }),
                    createVNode("div", { class: "block px-4 py-2 text-sm text-slate-400" }, toDisplayString(unref(t)("switchTeams")), 1),
                    (openBlock(true), createBlock(Fragment, null, renderList(_ctx.$page.props.auth.user.all_teams, (team) => {
                      return openBlock(), createBlock("form", {
                        key: team.id,
                        onSubmit: withModifiers(($event) => switchToTeam(team), ["prevent"])
                      }, [
                        createVNode(_sfc_main$k, { as: "button" }, {
                          default: withCtx(() => [
                            createVNode("div", { class: "flex items-center" }, [
                              team.id == _ctx.$page.props.auth.user.current_team_id ? (openBlock(), createBlock("svg", {
                                key: 0,
                                class: "me-2 h-5 w-5 text-green-400",
                                xmlns: "http://www.w3.org/2000/svg",
                                fill: "none",
                                viewBox: "0 0 24 24",
                                "stroke-width": "1.5",
                                stroke: "currentColor"
                              }, [
                                createVNode("path", {
                                  "stroke-linecap": "round",
                                  "stroke-linejoin": "round",
                                  d: "M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                })
                              ])) : createCommentVNode("", true),
                              createVNode("div", null, toDisplayString(team.name), 1)
                            ])
                          ]),
                          _: 2
                        }, 1024)
                      ], 40, ["onSubmit"]);
                    }), 128))
                  ], 64)) : createCommentVNode("", true)
                ])
              ];
            }
          }),
          _: 1
        }, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div><div class="-me-2 flex items-center sm:hidden"><button class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-slate-500 transition duration-150 ease-in-out"><svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24"><path class="${ssrRenderClass({ hidden: __props.showingNavigationDropdown, "inline-flex": !__props.showingNavigationDropdown })}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path><path class="${ssrRenderClass({ hidden: !__props.showingNavigationDropdown, "inline-flex": __props.showingNavigationDropdown })}" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div></div></div><div class="${ssrRenderClass([{ block: __props.showingNavigationDropdown, hidden: !__props.showingNavigationDropdown }, "sm:hidden"])}">`);
      _push(ssrRenderComponent(_sfc_main$h, null, null, _parent));
      _push(`</div></nav></div>`);
      if (_ctx.$slots.header) {
        _push(`<header class="bg-slate-100 dark:bg-sky-900 shadow"><div class="max-w-7xl mx-auto py-4 px-4 sm:px-4 lg:px-8">`);
        ssrRenderSlot(_ctx.$slots, "header", {}, null, _push, _parent);
        _push(`</div></header>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup$f = _sfc_main$f.setup;
_sfc_main$f.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Partials/Admin/Header.vue");
  return _sfc_setup$f ? _sfc_setup$f(props, ctx) : void 0;
};
const _sfc_main$e = {
  __name: "DraggableSidebarLink",
  __ssrInlineRender: true,
  props: {
    id: String,
    expanded: Boolean
  },
  setup(__props) {
    const page = usePage();
    const adminSettings = computed(() => {
      var _a;
      return ((_a = page.props) == null ? void 0 : _a.adminSettings) || {};
    });
    const props = __props;
    const isDarkMode = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    onMounted(() => {
      checkDarkMode();
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const colorText = computed(() => {
      return isDarkMode.value ? adminSettings.value.AdminSidebarDarkText || "text-slate-200" : adminSettings.value.AdminSidebarLightText || "text-slate-200";
    });
    const colorTextHover = computed(() => {
      return isDarkMode.value ? adminSettings.value.AdminSidebarDarkHoverText || "text-orange-300" : adminSettings.value.AdminSidebarLightHoverText || "text-orange-300";
    });
    const colorTextActive = computed(() => {
      return isDarkMode.value ? adminSettings.value.adminSidebarDarkActiveText || "text-yellow-200" : adminSettings.value.adminSidebarLightActiveText || "text-yellow-200";
    });
    const { t } = useI18n();
    const { props: pageProps } = usePage();
    const linkInfo = {
      admin: { label: t("adminPanel"), route: "admin.index" },
      home: { label: t("home"), route: "admin.home-page.index" },
      cmsPages: { label: t("pages"), route: "admin.cmsPages.index" },
      currencies: { label: t("currencies"), route: "admin.currencies.index" },
      comments: { label: t("comments"), route: "admin.comments.index" },
      reviews: { label: t("reviews"), route: "admin.reviews.index" },
      marketCompanies: { label: t("marketCompanies"), route: "admin.marketCompanies.index" },
      marketShops: { label: t("marketShops"), route: "admin.marketShops.index" },
      marketCategories: { label: t("marketCategories"), route: "admin.marketCategories.index" },
      marketProducts: { label: t("marketProducts"), route: "admin.marketProducts.index" },
      marketProductBundles: { label: t("marketProductBundles"), route: "admin.marketProductBundles.index" },
      marketProductVariants: { label: t("marketProductVariants"), route: "admin.marketProductVariants.index" },
      marketBrands: { label: t("marketBrands"), route: "admin.marketBrands.index" },
      marketTags: { label: t("marketTags"), route: "admin.marketTags.index" },
      marketAttributeGroups: { label: t("marketAttributeGroups"), route: "admin.marketAttributeGroups.index" },
      marketAttributes: { label: t("marketAttributes"), route: "admin.marketAttributes.index" },
      marketAttributeValues: { label: t("marketAttributeValues"), route: "admin.marketAttributeValues.index" },
      subscriptionPlans: { label: t("subscriptionPlans"), route: "admin.schoolSubscriptionPlans.index" },
      coursePrices: { label: t("coursePrices"), route: "admin.schoolCoursePrices.index" },
      bundlePrices: { label: t("bundlePrices"), route: "admin.schoolBundlePrices.index" },
      orders: { label: t("orders"), route: "admin.schoolOrders.index" },
      apiTokens: { label: t("apiTokens"), route: "api-tokens.index" },
      teamSettings: { label: t("teamSettings"), route: "teams.show", params: { team: pageProps.auth.user.current_team } },
      instructors: { label: t("instructors"), route: "admin.schoolInstructorProfiles.index" },
      hashtags: { label: t("hashtags"), route: "admin.schoolHashtags.index" },
      learningCategories: { label: t("learningCategories"), route: "admin.schoolTracks.index" },
      bundles: { label: t("bundles"), route: "admin.schoolBundles.index" },
      courses: { label: t("courses"), route: "admin.schoolCourses.index" },
      modules: { label: t("modules"), route: "admin.schoolModules.index" },
      lessons: { label: t("lessons"), route: "admin.schoolLessons.index" },
      assignments: { label: t("assignments"), route: "admin.schoolAssignments.index" },
      courseSchedules: { label: t("courseSchedules"), route: "admin.schoolCourseSchedules.index" },
      cohortEnrollments: { label: t("cohortEnrollments"), route: "admin.schoolCohortEnrollments.index" },
      enrollments: { label: t("enrollments"), route: "admin.schoolEnrollments.index" },
      quizzes: { label: t("quizzes"), route: "admin.schoolQuizzes.index" },
      quizQuestions: { label: t("quizQuestions"), route: "admin.schoolQuizQuestions.index" },
      quizAnswers: { label: t("quizAnswers"), route: "admin.schoolQuizAnswers.index" },
      quizAttempts: { label: t("quizAttempts"), route: "admin.schoolQuizAttempts.index" },
      quizAttemptItems: { label: t("quizAttemptItems"), route: "admin.schoolQuizAttemptItems.index" },
      users: { label: t("users"), route: "admin.users.index" },
      roles: { label: t("roles"), route: "admin.roles.index" },
      permissions: { label: t("permissions"), route: "admin.permissions.index" },
      rubrics: { label: t("rubrics"), route: "admin.blogRubrics.index" },
      articles: { label: t("articles"), route: "admin.blogArticles.index" },
      tags: { label: t("tags"), route: "admin.blogTags.index" },
      banners: { label: t("banners"), route: "admin.blogBanners.index" },
      videos: { label: t("videos"), route: "admin.blogVideos.index" },
      charts: { label: t("charts"), route: "admin.charts.index" },
      analyticsVisitorLogs: { label: t("analyticsLogs"), route: "admin.analyticsVisitorLogs.index" },
      settings: { label: t("settings"), route: "admin.settings.index" },
      parameters: { label: t("parameters"), route: "admin.parameters.index" },
      logs: { label: t("logs"), route: "admin.logs.index" },
      phpinfo: { label: "phpinfo", route: "admin.phpinfo.index" },
      composer: { label: "composer", route: "admin.composer.index" },
      package: { label: "package", route: "admin.package.index" },
      env: { label: "env", route: "admin.env.index" },
      backups: { label: t("backups"), route: "admin.backup.index" },
      files: { label: t("archive"), route: "admin.files.index" },
      components: { label: t("components"), route: "admin.components.index" },
      robot: { label: t("robot"), route: "admin.robot.index" },
      sitemap: { label: "sitemap.xml", route: "admin.sitemap.index" },
      reports: { label: t("reports"), route: "admin.reports.index" },
      imagePresets: { label: t("imagePresets"), route: "admin.imagePresets.index" },
      locations: { label: t("locations"), route: "admin.locations.index" },
      localization: { label: t("localization"), route: "admin.localization.index" }
    };
    const link = computed(() => linkInfo[props.id]);
    const svgContent = computed(() => sidebarIcons[props.id]);
    const isActive = computed(() => {
      var _a;
      if (!((_a = link.value) == null ? void 0 : _a.route)) {
        return false;
      }
      const currentRoute = route().current();
      if (!currentRoute) {
        return false;
      }
      if (currentRoute === link.value.route) {
        return true;
      }
      if (link.value.route.endsWith(".index")) {
        const baseRoute = link.value.route.replace(/\.index$/, "");
        return currentRoute.startsWith(`${baseRoute}.`);
      }
      return false;
    });
    const classes = computed(() => {
      if (isActive.value) {
        return `flex items-center px-1 text-xs font-medium leading-3
        ${colorTextActive.value}
        hover:${colorTextActive.value}
        focus:${colorTextActive.value}
        focus:outline-none transition duration-150 ease-in-out`;
      }
      return `flex items-center px-1 text-xs font-medium leading-3
    ${colorText.value}
    hover:${colorTextHover.value}
    focus:${colorTextHover.value}
    focus:outline-none transition duration-150 ease-in-out`;
    });
    const containerClasses = computed(() => {
      return props.expanded ? "mb-1" : "mb-3";
    });
    const textClasses = computed(() => {
      return props.expanded ? "ml-3 opacity-100" : "ml-3 opacity-0 whitespace-nowrap overflow-hidden";
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<li${ssrRenderAttrs(mergeProps({
        class: ["mt-0", containerClasses.value]
      }, _attrs))}>`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route(link.value.route, link.value.params || {}),
        class: classes.value
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<span${_scopeId}>${svgContent.value ?? ""}</span><span class="${ssrRenderClass([textClasses.value, "text-xs font-medium transition-opacity duration-200 max-w-full"])}"${_scopeId}>${ssrInterpolate(link.value.label)}</span>`);
          } else {
            return [
              createVNode("span", { innerHTML: svgContent.value }, null, 8, ["innerHTML"]),
              createVNode("span", {
                class: ["text-xs font-medium transition-opacity duration-200 max-w-full", textClasses.value]
              }, toDisplayString(link.value.label), 3)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</li>`);
    };
  }
};
const _sfc_setup$e = _sfc_main$e.setup;
_sfc_main$e.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Links/DraggableSidebarLink.vue");
  return _sfc_setup$e ? _sfc_setup$e(props, ctx) : void 0;
};
const _sfc_main$d = {
  __name: "SidebarMain",
  __ssrInlineRender: true,
  props: {
    expanded: Boolean
  },
  emits: ["update:pageLinks"],
  setup(__props, { emit: __emit }) {
    const isDarkMode = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    onMounted(() => {
      checkDarkMode();
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const emit = __emit;
    const pageLinks = ref(JSON.parse(localStorage.getItem("pageLinks")) || [
      "admin"
    ]);
    const handleDragEnd = () => {
      localStorage.setItem("pageLinks", JSON.stringify(pageLinks.value));
      emit("update:pageLinks", pageLinks.value);
    };
    watch(pageLinks, (newVal) => {
      localStorage.setItem("pageLinks", JSON.stringify(newVal));
      emit("update:pageLinks", newVal);
    });
    onMounted(() => {
      pageLinks.value = JSON.parse(localStorage.getItem("pageLinks")) || pageLinks.value;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(draggable), mergeProps({
        modelValue: pageLinks.value,
        "onUpdate:modelValue": ($event) => pageLinks.value = $event,
        onEnd: handleDragEnd,
        itemKey: "id",
        group: "links",
        tag: "ul",
        class: "pb-2"
      }, _attrs), {
        item: withCtx(({ element }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: element,
              expanded: __props.expanded
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$e, {
                id: element,
                expanded: __props.expanded
              }, null, 8, ["id", "expanded"])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$d = _sfc_main$d.setup;
_sfc_main$d.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Links/SidebarMain.vue");
  return _sfc_setup$d ? _sfc_setup$d(props, ctx) : void 0;
};
const _sfc_main$c = {
  __name: "SidebarCms",
  __ssrInlineRender: true,
  props: {
    expanded: Boolean
  },
  emits: ["update:pageLinks"],
  setup(__props, { emit: __emit }) {
    const isDarkMode = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    onMounted(() => {
      checkDarkMode();
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const emit = __emit;
    const pageLinks = ref(JSON.parse(localStorage.getItem("pageLinks")) || [
      "cmsPages"
    ]);
    const handleDragEnd = () => {
      localStorage.setItem("pageLinks", JSON.stringify(pageLinks.value));
      emit("update:pageLinks", pageLinks.value);
    };
    watch(pageLinks, (newVal) => {
      localStorage.setItem("pageLinks", JSON.stringify(newVal));
      emit("update:pageLinks", newVal);
    });
    onMounted(() => {
      pageLinks.value = JSON.parse(localStorage.getItem("pageLinks")) || pageLinks.value;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(draggable), mergeProps({
        modelValue: pageLinks.value,
        "onUpdate:modelValue": ($event) => pageLinks.value = $event,
        onEnd: handleDragEnd,
        itemKey: "id",
        group: "links",
        tag: "ul",
        class: "pb-2"
      }, _attrs), {
        item: withCtx(({ element }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: element,
              expanded: __props.expanded
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$e, {
                id: element,
                expanded: __props.expanded
              }, null, 8, ["id", "expanded"])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$c = _sfc_main$c.setup;
_sfc_main$c.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Links/SidebarCms.vue");
  return _sfc_setup$c ? _sfc_setup$c(props, ctx) : void 0;
};
const _sfc_main$b = {
  __name: "SidebarMarket",
  __ssrInlineRender: true,
  props: {
    expanded: Boolean
  },
  emits: ["update:pageLinks"],
  setup(__props, { emit: __emit }) {
    const isDarkMode = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    onMounted(() => {
      checkDarkMode();
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const emit = __emit;
    const pageLinks = ref(JSON.parse(localStorage.getItem("pageLinks")) || [
      "marketCompanies",
      "marketShops",
      "marketCategories",
      "marketProducts",
      "marketProductBundles",
      "marketProductVariants",
      "marketBrands",
      "marketTags",
      "marketAttributeGroups",
      "marketAttributes",
      "marketAttributeValues"
    ]);
    const handleDragEnd = () => {
      localStorage.setItem("pageLinks", JSON.stringify(pageLinks.value));
      emit("update:pageLinks", pageLinks.value);
    };
    watch(pageLinks, (newVal) => {
      localStorage.setItem("pageLinks", JSON.stringify(newVal));
      emit("update:pageLinks", newVal);
    });
    onMounted(() => {
      pageLinks.value = JSON.parse(localStorage.getItem("pageLinks")) || pageLinks.value;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(draggable), mergeProps({
        modelValue: pageLinks.value,
        "onUpdate:modelValue": ($event) => pageLinks.value = $event,
        onEnd: handleDragEnd,
        itemKey: "id",
        group: "links",
        tag: "ul",
        class: "pb-2"
      }, _attrs), {
        item: withCtx(({ element }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: element,
              expanded: __props.expanded
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$e, {
                id: element,
                expanded: __props.expanded
              }, null, 8, ["id", "expanded"])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$b = _sfc_main$b.setup;
_sfc_main$b.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Links/SidebarMarket.vue");
  return _sfc_setup$b ? _sfc_setup$b(props, ctx) : void 0;
};
const _sfc_main$a = {
  __name: "SidebarFinance",
  __ssrInlineRender: true,
  props: {
    expanded: Boolean
  },
  emits: ["update:pageLinks"],
  setup(__props, { emit: __emit }) {
    const isDarkMode = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    onMounted(() => {
      checkDarkMode();
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const emit = __emit;
    const pageLinks = ref(JSON.parse(localStorage.getItem("pageLinks")) || [
      "currencies"
    ]);
    const handleDragEnd = () => {
      localStorage.setItem("pageLinks", JSON.stringify(pageLinks.value));
      emit("update:pageLinks", pageLinks.value);
    };
    watch(pageLinks, (newVal) => {
      localStorage.setItem("pageLinks", JSON.stringify(newVal));
      emit("update:pageLinks", newVal);
    });
    onMounted(() => {
      pageLinks.value = JSON.parse(localStorage.getItem("pageLinks")) || pageLinks.value;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(draggable), mergeProps({
        modelValue: pageLinks.value,
        "onUpdate:modelValue": ($event) => pageLinks.value = $event,
        onEnd: handleDragEnd,
        itemKey: "id",
        group: "links",
        tag: "ul",
        class: "pb-2"
      }, _attrs), {
        item: withCtx(({ element }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: element,
              expanded: __props.expanded
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$e, {
                id: element,
                expanded: __props.expanded
              }, null, 8, ["id", "expanded"])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$a = _sfc_main$a.setup;
_sfc_main$a.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Links/SidebarFinance.vue");
  return _sfc_setup$a ? _sfc_setup$a(props, ctx) : void 0;
};
const _sfc_main$9 = {
  __name: "SidebarSchool",
  __ssrInlineRender: true,
  props: {
    expanded: Boolean
  },
  emits: ["update:pageLinks"],
  setup(__props, { emit: __emit }) {
    const isDarkMode = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    onMounted(() => {
      checkDarkMode();
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const emit = __emit;
    const pageLinks = ref(JSON.parse(localStorage.getItem("pageLinks")) || [
      "instructors",
      "hashtags",
      "learningCategories",
      "bundles",
      "courses",
      "modules",
      "lessons",
      "assignments",
      "courseSchedules",
      "cohortEnrollments",
      "enrollments",
      "quizzes",
      "quizQuestions",
      "quizAnswers",
      "quizAttempts",
      "quizAttemptItems",
      "orders",
      "subscriptionPlans",
      "coursePrices",
      "bundlePrices"
    ]);
    const handleDragEnd = () => {
      localStorage.setItem("pageLinks", JSON.stringify(pageLinks.value));
      emit("update:pageLinks", pageLinks.value);
    };
    watch(pageLinks, (newVal) => {
      localStorage.setItem("pageLinks", JSON.stringify(newVal));
      emit("update:pageLinks", newVal);
    });
    onMounted(() => {
      pageLinks.value = JSON.parse(localStorage.getItem("pageLinks")) || pageLinks.value;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(draggable), mergeProps({
        modelValue: pageLinks.value,
        "onUpdate:modelValue": ($event) => pageLinks.value = $event,
        onEnd: handleDragEnd,
        itemKey: "id",
        group: "links",
        tag: "ul",
        class: "pb-2"
      }, _attrs), {
        item: withCtx(({ element }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: element,
              expanded: __props.expanded
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$e, {
                id: element,
                expanded: __props.expanded
              }, null, 8, ["id", "expanded"])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$9 = _sfc_main$9.setup;
_sfc_main$9.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Links/SidebarSchool.vue");
  return _sfc_setup$9 ? _sfc_setup$9(props, ctx) : void 0;
};
const _sfc_main$8 = {
  __name: "SidebarBlog",
  __ssrInlineRender: true,
  props: {
    expanded: Boolean
  },
  emits: ["update:pageLinks"],
  setup(__props, { emit: __emit }) {
    const isDarkMode = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    onMounted(() => {
      checkDarkMode();
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const emit = __emit;
    const pageLinks = ref(JSON.parse(localStorage.getItem("pageLinks")) || [
      "rubrics",
      "articles",
      "tags",
      "banners",
      "videos"
    ]);
    const handleDragEnd = () => {
      localStorage.setItem("pageLinks", JSON.stringify(pageLinks.value));
      emit("update:pageLinks", pageLinks.value);
    };
    watch(pageLinks, (newVal) => {
      localStorage.setItem("pageLinks", JSON.stringify(newVal));
      emit("update:pageLinks", newVal);
    });
    onMounted(() => {
      pageLinks.value = JSON.parse(localStorage.getItem("pageLinks")) || pageLinks.value;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(draggable), mergeProps({
        modelValue: pageLinks.value,
        "onUpdate:modelValue": ($event) => pageLinks.value = $event,
        onEnd: handleDragEnd,
        itemKey: "id",
        group: "links",
        tag: "ul",
        class: "pb-2"
      }, _attrs), {
        item: withCtx(({ element }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: element,
              expanded: __props.expanded
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$e, {
                id: element,
                expanded: __props.expanded
              }, null, 8, ["id", "expanded"])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Links/SidebarBlog.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const _sfc_main$7 = {
  __name: "SidebarCommunications",
  __ssrInlineRender: true,
  props: {
    expanded: Boolean
  },
  emits: ["update:pageLinks"],
  setup(__props, { emit: __emit }) {
    const isDarkMode = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    onMounted(() => {
      checkDarkMode();
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const emit = __emit;
    const pageLinks = ref(JSON.parse(localStorage.getItem("pageLinks")) || [
      "comments",
      "reviews"
    ]);
    const handleDragEnd = () => {
      localStorage.setItem("pageLinks", JSON.stringify(pageLinks.value));
      emit("update:pageLinks", pageLinks.value);
    };
    watch(pageLinks, (newVal) => {
      localStorage.setItem("pageLinks", JSON.stringify(newVal));
      emit("update:pageLinks", newVal);
    });
    onMounted(() => {
      pageLinks.value = JSON.parse(localStorage.getItem("pageLinks")) || pageLinks.value;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(draggable), mergeProps({
        modelValue: pageLinks.value,
        "onUpdate:modelValue": ($event) => pageLinks.value = $event,
        onEnd: handleDragEnd,
        itemKey: "id",
        group: "links",
        tag: "ul",
        class: "pb-2"
      }, _attrs), {
        item: withCtx(({ element }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: element,
              expanded: __props.expanded
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$e, {
                id: element,
                expanded: __props.expanded
              }, null, 8, ["id", "expanded"])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Links/SidebarCommunications.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const _sfc_main$6 = {
  __name: "SidebarStatistics",
  __ssrInlineRender: true,
  props: {
    expanded: Boolean
  },
  emits: ["update:pageLinks"],
  setup(__props, { emit: __emit }) {
    const isDarkMode = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    onMounted(() => {
      checkDarkMode();
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const emit = __emit;
    const pageLinks = ref(JSON.parse(localStorage.getItem("pageLinks")) || [
      "charts",
      "analyticsVisitorLogs"
    ]);
    const handleDragEnd = () => {
      localStorage.setItem("pageLinks", JSON.stringify(pageLinks.value));
      emit("update:pageLinks", pageLinks.value);
    };
    watch(pageLinks, (newVal) => {
      localStorage.setItem("pageLinks", JSON.stringify(newVal));
      emit("update:pageLinks", newVal);
    });
    onMounted(() => {
      pageLinks.value = JSON.parse(localStorage.getItem("pageLinks")) || pageLinks.value;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(draggable), mergeProps({
        modelValue: pageLinks.value,
        "onUpdate:modelValue": ($event) => pageLinks.value = $event,
        onEnd: handleDragEnd,
        itemKey: "id",
        group: "links",
        tag: "ul",
        class: "pb-2"
      }, _attrs), {
        item: withCtx(({ element }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: element,
              expanded: __props.expanded
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$e, {
                id: element,
                expanded: __props.expanded
              }, null, 8, ["id", "expanded"])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Links/SidebarStatistics.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const _sfc_main$5 = {
  __name: "SidebarSystem",
  __ssrInlineRender: true,
  props: {
    expanded: Boolean
  },
  emits: ["update:pageLinks"],
  setup(__props, { emit: __emit }) {
    const isDarkMode = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    onMounted(() => {
      checkDarkMode();
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const emit = __emit;
    const pageLinks = ref(JSON.parse(localStorage.getItem("pageLinks")) || [
      "logs",
      "phpinfo",
      "composer",
      "package",
      "env",
      "backups",
      "files",
      "parameters",
      "components",
      "robot",
      "sitemap",
      "reports",
      "imagePresets",
      "locations",
      "localization"
    ]);
    const handleDragEnd = () => {
      localStorage.setItem("pageLinks", JSON.stringify(pageLinks.value));
      emit("update:pageLinks", pageLinks.value);
    };
    watch(pageLinks, (newVal) => {
      localStorage.setItem("pageLinks", JSON.stringify(newVal));
      emit("update:pageLinks", newVal);
    });
    onMounted(() => {
      pageLinks.value = JSON.parse(localStorage.getItem("pageLinks")) || pageLinks.value;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(unref(draggable), mergeProps({
        modelValue: pageLinks.value,
        "onUpdate:modelValue": ($event) => pageLinks.value = $event,
        onEnd: handleDragEnd,
        itemKey: "id",
        group: "links",
        tag: "ul",
        class: "pb-2"
      }, _attrs), {
        item: withCtx(({ element }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: element,
              expanded: __props.expanded
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$e, {
                id: element,
                expanded: __props.expanded
              }, null, 8, ["id", "expanded"])
            ];
          }
        }),
        _: 1
      }, _parent));
    };
  }
};
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Links/SidebarSystem.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "SidebarSettings",
  __ssrInlineRender: true,
  props: {
    expanded: Boolean
  },
  emits: ["update:mainLinks", "update:hiddenLinks"],
  setup(__props, { emit: __emit }) {
    const isDarkMode = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    onMounted(() => {
      checkDarkMode();
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const emit = __emit;
    const { t } = useI18n();
    const mainLinks = ref(JSON.parse(localStorage.getItem("mainLinks")) || [
      "users",
      "roles",
      "permissions",
      "settings"
    ]);
    const hiddenLinks = ref(JSON.parse(localStorage.getItem("hiddenLinks")) || [
      "apiTokens",
      "teamSettings"
    ]);
    const showHiddenLinks = ref(false);
    const handleDragEnd = () => {
      localStorage.setItem("mainLinks", JSON.stringify(mainLinks.value));
      localStorage.setItem("hiddenLinks", JSON.stringify(hiddenLinks.value));
      emit("update:mainLinks", mainLinks.value);
      emit("update:hiddenLinks", hiddenLinks.value);
    };
    watch(mainLinks, (newVal) => {
      localStorage.setItem("mainLinks", JSON.stringify(newVal));
      emit("update:mainLinks", newVal);
    });
    watch(hiddenLinks, (newVal) => {
      localStorage.setItem("hiddenLinks", JSON.stringify(newVal));
      emit("update:hiddenLinks", newVal);
    });
    onMounted(() => {
      mainLinks.value = JSON.parse(localStorage.getItem("mainLinks")) || mainLinks.value;
      hiddenLinks.value = JSON.parse(localStorage.getItem("hiddenLinks")) || hiddenLinks.value;
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(draggable), {
        modelValue: mainLinks.value,
        "onUpdate:modelValue": ($event) => mainLinks.value = $event,
        onEnd: handleDragEnd,
        itemKey: "id",
        group: "links",
        tag: "ul",
        class: "pb-2"
      }, {
        item: withCtx(({ element }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$e, {
              id: element,
              expanded: __props.expanded
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$e, {
                id: element,
                expanded: __props.expanded
              }, null, 8, ["id", "expanded"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<button class="w-full"><span class="flex justify-start text-xs uppercase font-semibold pl-1 opacity-95 text-indigo-200 pt-1 border-t border-dotted border-gray-50">${ssrInterpolate(unref(t)("systems"))}</span></button>`);
      if (showHiddenLinks.value) {
        _push(ssrRenderComponent(unref(draggable), {
          modelValue: hiddenLinks.value,
          "onUpdate:modelValue": ($event) => hiddenLinks.value = $event,
          onEnd: handleDragEnd,
          itemKey: "id",
          group: "links",
          tag: "ul",
          class: "my-3"
        }, {
          item: withCtx(({ element }, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(_sfc_main$e, {
                id: element,
                expanded: __props.expanded
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(_sfc_main$e, {
                  id: element,
                  expanded: __props.expanded
                }, null, 8, ["id", "expanded"])
              ];
            }
          }),
          _: 1
        }, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Links/SidebarSettings.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "Sidebar",
  __ssrInlineRender: true,
  props: {
    sidebarOpen: Boolean,
    sidebarTitle: String
  },
  emits: ["close-sidebar"],
  setup(__props, { emit: __emit }) {
    library.add(fas);
    const { t } = useI18n();
    const page = usePage();
    const adminSettings = computed(() => {
      var _a;
      return ((_a = page.props) == null ? void 0 : _a.adminSettings) || {};
    });
    const props = __props;
    const isDarkMode = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    onMounted(() => {
      checkDarkMode();
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const bgColorClass = computed(() => {
      return isDarkMode.value ? adminSettings.value.adminSidebarDarkColor || "bg-gray-700" : adminSettings.value.adminSidebarLightColor || "bg-cyan-800";
    });
    const emit = __emit;
    const trigger = ref(null);
    const sidebar = ref(null);
    const sidebarExpanded = ref(localStorage.getItem("sidebar-expanded") === "true");
    const clickHandler = ({ target }) => {
      if (!sidebar.value || !trigger.value) return;
      if (!props.sidebarOpen || sidebar.value.contains(target) || trigger.value.contains(target)) return;
      emit("close-sidebar");
    };
    const keyHandler = ({ keyCode }) => {
      if (!props.sidebarOpen || keyCode !== 27) return;
      emit("close-sidebar");
    };
    onMounted(async () => {
      document.addEventListener("click", clickHandler);
      document.addEventListener("keydown", keyHandler);
    });
    onUnmounted(() => {
      document.removeEventListener("click", clickHandler);
      document.removeEventListener("keydown", keyHandler);
    });
    watch(sidebarExpanded, (newVal) => {
      localStorage.setItem("sidebar-expanded", newVal.toString());
    });
    const sidebarGroups = ref({
      pages: localStorage.getItem("sidebar-group-pages") !== "false",
      cms: localStorage.getItem("sidebar-group-cms") !== "false",
      finance: localStorage.getItem("sidebar-group-finance") !== "false",
      market: localStorage.getItem("sidebar-group-market") !== "false",
      school: localStorage.getItem("sidebar-group-school") !== "false",
      blog: localStorage.getItem("sidebar-group-blog") !== "false",
      communications: localStorage.getItem("sidebar-group-communications") !== "false",
      statistics: localStorage.getItem("sidebar-group-statistics") !== "false",
      system: localStorage.getItem("sidebar-group-system") !== "false",
      administrator: localStorage.getItem("sidebar-group-administrator") !== "false"
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(_attrs)}><div class="${ssrRenderClass([__props.sidebarOpen ? "opacity-100" : "opacity-0 pointer-events-none", "fixed inset-0 z-20 dark:border-r dark:border-gray-600 bg-opacity-30 md:hidden md:z-auto transition-opacity duration-200"])}" aria-hidden="true"></div><div id="sidebar" class="${ssrRenderClass([[bgColorClass.value, { "translate-x-0": __props.sidebarOpen, "-translate-x-58": !__props.sidebarOpen, "hidden md:flex": true, "md:w-16": !sidebarExpanded.value, "md:!w-58 2xl:!w-58": sidebarExpanded.value }], "h-screen absolute z-40 w-58 left-0 top-0 pb-16 p-2 flex flex-col dark:border-r dark:border-gray-600 md:static md:left-auto md:top-auto md:translate-x-0 md:overflow-y-auto overflow-y-scroll no-scrollbar transition-all duration-200 ease-in-out"])}"><div class="flex justify-around items-center mb-2 pr-3 md:px-0"><button${ssrRenderAttr("title", unref(t)("toggleSidebar"))}><svg class="${ssrRenderClass([{ "rotate-180": sidebarExpanded.value }, "mx-1 w-6 h-6 py-1 fill-current transition-transform duration-200 border border-gray-400 hover:border-red-400"])}" viewBox="0 0 24 24"><path class="text-slate-400 hover:text-red-400" d="M19.586 11l-5-5L16 4.586 23.414 12 16 19.414 14.586 18l5-5H7v-2z"></path><path class="text-slate-600" d="M3 23H1V1h2z"></path></svg></button>`);
      if (sidebarExpanded.value) {
        _push(ssrRenderComponent(unref(Link), {
          href: _ctx.route("admin.index")
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(ssrRenderComponent(ApplicationMark, { class: "h-6 w-auto 2xl:block" }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(ApplicationMark, { class: "h-6 w-auto 2xl:block" })
              ];
            }
          }),
          _: 1
        }, _parent));
      } else {
        _push(`<!---->`);
      }
      if (sidebarExpanded.value) {
        _push(ssrRenderComponent(unref(FontAwesomeIcon), {
          icon: ["fas", "sliders"],
          class: "text-white ml-1"
        }, null, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (sidebarExpanded.value) {
        _push(ssrRenderComponent(DigitalClock, { class: "mb-2 relative z-10" }, null, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`<div class="space-y-1">`);
      if (sidebarExpanded.value) {
        _push(`<span class="flex justify-between items-center cursor-pointer select-none text-xs uppercase font-semibold pl-1 pr-1 opacity-95 text-indigo-200 pt-1 border-t border-dotted border-gray-50">${ssrInterpolate(unref(t)("pages"))} <svg class="${ssrRenderClass([{ "rotate-180": sidebarGroups.value.pages }, "w-3 h-3 fill-current transition-transform duration-200"])}" viewBox="0 0 20 20"><path d="M5.25 7.5L10 12.25L14.75 7.5H5.25Z"></path></svg></span>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$d, {
        style: !sidebarExpanded.value || sidebarGroups.value.pages ? null : { display: "none" },
        expanded: sidebarExpanded.value
      }, null, _parent));
      if (sidebarExpanded.value) {
        _push(`<span class="flex justify-between items-center cursor-pointer select-none text-xs uppercase font-semibold pl-1 pr-1 opacity-95 text-indigo-200 pt-1 border-t border-dotted border-gray-50"> CMS <svg class="${ssrRenderClass([{ "rotate-180": sidebarGroups.value.market }, "w-3 h-3 fill-current transition-transform duration-200"])}" viewBox="0 0 20 20"><path d="M5.25 7.5L10 12.25L14.75 7.5H5.25Z"></path></svg></span>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$c, {
        style: !sidebarExpanded.value || sidebarGroups.value.cms ? null : { display: "none" },
        expanded: sidebarExpanded.value
      }, null, _parent));
      if (sidebarExpanded.value) {
        _push(`<span class="flex justify-between items-center cursor-pointer select-none text-xs uppercase font-semibold pl-1 pr-1 opacity-95 text-indigo-200 pt-1 border-t border-dotted border-gray-50">${ssrInterpolate(unref(t)("store"))} <svg class="${ssrRenderClass([{ "rotate-180": sidebarGroups.value.market }, "w-3 h-3 fill-current transition-transform duration-200"])}" viewBox="0 0 20 20"><path d="M5.25 7.5L10 12.25L14.75 7.5H5.25Z"></path></svg></span>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$b, {
        style: !sidebarExpanded.value || sidebarGroups.value.market ? null : { display: "none" },
        expanded: sidebarExpanded.value
      }, null, _parent));
      if (sidebarExpanded.value) {
        _push(`<span class="flex justify-between items-center cursor-pointer select-none text-xs uppercase font-semibold pl-1 pr-1 opacity-95 text-indigo-200 pt-1 border-t border-dotted border-gray-50">${ssrInterpolate(unref(t)("finance"))} <svg class="${ssrRenderClass([{ "rotate-180": sidebarGroups.value.finance }, "w-3 h-3 fill-current transition-transform duration-200"])}" viewBox="0 0 20 20"><path d="M5.25 7.5L10 12.25L14.75 7.5H5.25Z"></path></svg></span>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$a, {
        style: !sidebarExpanded.value || sidebarGroups.value.finance ? null : { display: "none" },
        expanded: sidebarExpanded.value
      }, null, _parent));
      if (sidebarExpanded.value) {
        _push(`<span class="flex justify-between items-center cursor-pointer select-none text-xs uppercase font-semibold pl-1 pr-1 opacity-95 text-indigo-200 pt-1 border-t border-dotted border-gray-50">${ssrInterpolate(unref(t)("school"))} <svg class="${ssrRenderClass([{ "rotate-180": sidebarGroups.value.finance }, "w-3 h-3 fill-current transition-transform duration-200"])}" viewBox="0 0 20 20"><path d="M5.25 7.5L10 12.25L14.75 7.5H5.25Z"></path></svg></span>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$9, {
        style: !sidebarExpanded.value || sidebarGroups.value.school ? null : { display: "none" },
        expanded: sidebarExpanded.value
      }, null, _parent));
      if (sidebarExpanded.value) {
        _push(`<span class="flex justify-between items-center cursor-pointer select-none text-xs uppercase font-semibold pl-1 pr-1 opacity-95 text-indigo-200 pt-1 border-t border-dotted border-gray-50">${ssrInterpolate(unref(t)("blog"))} <svg class="${ssrRenderClass([{ "rotate-180": sidebarGroups.value.finance }, "w-3 h-3 fill-current transition-transform duration-200"])}" viewBox="0 0 20 20"><path d="M5.25 7.5L10 12.25L14.75 7.5H5.25Z"></path></svg></span>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$8, {
        style: !sidebarExpanded.value || sidebarGroups.value.blog ? null : { display: "none" },
        expanded: sidebarExpanded.value
      }, null, _parent));
      if (sidebarExpanded.value) {
        _push(`<span class="flex justify-between items-center cursor-pointer select-none text-xs uppercase font-semibold pl-1 pr-1 opacity-95 text-indigo-200 pt-1 border-t border-dotted border-gray-50">${ssrInterpolate(unref(t)("communications"))} <svg class="${ssrRenderClass([{ "rotate-180": sidebarGroups.value.communications }, "w-3 h-3 fill-current transition-transform duration-200"])}" viewBox="0 0 20 20"><path d="M5.25 7.5L10 12.25L14.75 7.5H5.25Z"></path></svg></span>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$7, {
        style: !sidebarExpanded.value || sidebarGroups.value.communications ? null : { display: "none" },
        expanded: sidebarExpanded.value
      }, null, _parent));
      if (sidebarExpanded.value) {
        _push(`<span class="flex justify-between items-center cursor-pointer select-none text-xs uppercase font-semibold pl-1 pr-1 opacity-95 text-indigo-200 pt-1 border-t border-dotted border-gray-50">${ssrInterpolate(unref(t)("statistics"))} <svg class="${ssrRenderClass([{ "rotate-180": sidebarGroups.value.finance }, "w-3 h-3 fill-current transition-transform duration-200"])}" viewBox="0 0 20 20"><path d="M5.25 7.5L10 12.25L14.75 7.5H5.25Z"></path></svg></span>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$6, {
        style: !sidebarExpanded.value || sidebarGroups.value.statistics ? null : { display: "none" },
        expanded: sidebarExpanded.value
      }, null, _parent));
      if (sidebarExpanded.value) {
        _push(`<span class="flex justify-between items-center cursor-pointer select-none text-xs uppercase font-semibold pl-1 pr-1 opacity-95 text-indigo-200 pt-1 border-t border-dotted border-gray-50">${ssrInterpolate(unref(t)("system"))} <svg class="${ssrRenderClass([{ "rotate-180": sidebarGroups.value.system }, "w-3 h-3 fill-current transition-transform duration-200"])}" viewBox="0 0 20 20"><path d="M5.25 7.5L10 12.25L14.75 7.5H5.25Z"></path></svg></span>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$5, {
        style: !sidebarExpanded.value || sidebarGroups.value.system ? null : { display: "none" },
        expanded: sidebarExpanded.value
      }, null, _parent));
      if (sidebarExpanded.value) {
        _push(`<span class="flex justify-between items-center cursor-pointer select-none text-xs uppercase font-semibold pl-1 pr-1 opacity-95 text-indigo-200 pt-1 border-t border-dotted border-gray-50">${ssrInterpolate(unref(t)("administrator"))}</span>`);
      } else {
        _push(`<!---->`);
      }
      _push(ssrRenderComponent(_sfc_main$4, { expanded: sidebarExpanded.value }, null, _parent));
      _push(`</div></div></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Partials/Admin/Sidebar.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "Footer",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const { t, locale } = useI18n();
    const availableLocales = computed(() => {
      var _a;
      return Array.isArray((_a = page.props) == null ? void 0 : _a.availableLocales) ? page.props.availableLocales.map((item) => String(item).toLowerCase()) : [];
    });
    const selectedLocale = ref(locale.value);
    watch(
      () => locale.value,
      (newLocale) => {
        if (selectedLocale.value !== newLocale) {
          selectedLocale.value = newLocale;
        }
      }
    );
    watch(selectedLocale, (newLocale) => {
      const targetLocale = String(newLocale || "").toLowerCase();
      const currentLocale = String(locale.value || "").toLowerCase();
      if (!targetLocale || targetLocale === currentLocale) {
        return;
      }
      if (!availableLocales.value.includes(targetLocale)) {
        return;
      }
      locale.value = targetLocale;
      const pathSegments = window.location.pathname.split("/").filter(Boolean);
      if (pathSegments.length > 0 && availableLocales.value.includes(pathSegments[0].toLowerCase())) {
        pathSegments[0] = targetLocale;
      } else {
        pathSegments.unshift(targetLocale);
      }
      const newPath = `/${pathSegments.join("/")}${window.location.search}`;
      Inertia.visit(newPath, {
        preserveState: false,
        preserveScroll: true,
        replace: true
      });
    });
    const buildingSnapshots = ref(false);
    const clearing = ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<footer${ssrRenderAttrs(mergeProps({ class: "sticky px-3 py-1 bottom-0 bg-gradient-to-b from-slate-100 to-slate-300 dark:bg-gradient-to-b dark:from-slate-700 dark:to-slate-900 border-t border-slate-200 dark:border-slate-700 z-20" }, _attrs))}><div class="flex items-center justify-center sm:justify-between flex-wrap"><div class="flex flex-row items-center justify-start gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400"><a href="https://t.me/k_a_v_www" target="_blank"${ssrRenderAttr("title", unref(t)("supportService"))} class="flex items-center space-x-2 text-blue-500 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-500"><svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5 sm:w-6 sm:h-6"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.175 8.89l-1.4 6.63c-.105.467-.405.578-.82.36l-2.27-1.67-1.093 1.054c-.12.12-.222.222-.45.222l.168-2.39 4.35-3.923c.19-.168-.04-.263-.29-.095L8.78 11.167l-2.42-.76c-.464-.14-.474-.464.096-.684l9.452-3.65c.44-.16.82.108.66.717z"></path></svg></a> © ${ssrInterpolate((/* @__PURE__ */ new Date()).getFullYear())} <a href="/admin" target="_blank" class="font-semibold text-red-400 hover:text-rose-300"> Pulsar CMS </a></div><div class="flex flex-row items-center justify-end gap-1"><button type="button"${ssrIncludeBooleanAttr(buildingSnapshots.value) ? " disabled" : ""} title="Создать снимок настроек" class="flex items-center btn px-1 py-0.5 text-slate-900 dark:text-slate-100 rounded-sm border-2 border-slate-400 disabled:opacity-50"><svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24"><path class="fill-current text-blue-400" d="M12,10C8.2,10,4.3,9.3,2,7.6V12c0,2.7,5.2,4,10,4s10-1.3,10-4V7.6C19.7,9.3,15.8,10,12,10z"></path><path class="fill-current text-blue-400" d="M12,18c-3.8,0-7.7-0.7-10-2.4V20c0,2.7,5.2,4,10,4s10-1.3,10-4v-4.4C19.7,17.3,15.8,18,12,18z"></path><path class="fill-current text-blue-600" d="M12,0C7.2,0,2,1.3,2,4s5.2,4,10,4s10-1.3,10-4S16.8,0,12,0z"></path></svg></button><button type="button"${ssrIncludeBooleanAttr(clearing.value) ? " disabled" : ""}${ssrRenderAttr("title", unref(t)("clearCache"))} class="flex items-center btn px-1 py-0.5 text-slate-900 dark:text-slate-100 rounded-sm border-2 border-slate-400 disabled:opacity-50"><svg class="w-4 h-4 fill-current text-red-400 shrink-0" viewBox="0 0 16 16"><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"></path></svg></button>`);
      _push(ssrRenderComponent(_sfc_main$m, {
        modelValue: selectedLocale.value,
        "onUpdate:modelValue": ($event) => selectedLocale.value = $event,
        placement: "top-end"
      }, null, _parent));
      _push(`</div></div></footer>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Partials/Admin/Footer.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "WidgetPanel",
  __ssrInlineRender: true,
  setup(__props) {
    const { t } = useI18n();
    const page = usePage();
    const adminSettings = computed(() => {
      var _a;
      return ((_a = page.props) == null ? void 0 : _a.adminSettings) || {};
    });
    const isDarkMode = ref(false);
    const isTranslatorOpen = ref(false);
    let observer;
    const checkDarkMode = () => {
      isDarkMode.value = document.documentElement.classList.contains("dark");
    };
    const closeTranslator = () => {
      isTranslatorOpen.value = false;
      localStorage.setItem("adminTranslatorOpen", "false");
    };
    onMounted(() => {
      checkDarkMode();
      isTranslatorOpen.value = localStorage.getItem("adminTranslatorOpen") === "true";
      observer = new MutationObserver(checkDarkMode);
      observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ["class"]
      });
    });
    onUnmounted(() => {
      if (observer) observer.disconnect();
    });
    const bgColorClass = computed(() => {
      return isDarkMode.value ? adminSettings.value.adminSidebarDarkColor || "bg-gray-700" : adminSettings.value.adminSidebarLightColor || "bg-cyan-800";
    });
    const colorText = computed(() => {
      return isDarkMode.value ? adminSettings.value.adminSidebarDarkText || "text-slate-200" : adminSettings.value.adminSidebarLightText || "text-slate-200";
    });
    const colorTextHover = computed(() => {
      return isDarkMode.value ? adminSettings.value.adminSidebarDarkHoverText || "text-orange-300" : adminSettings.value.adminSidebarLightHoverText || "text-orange-300";
    });
    const hoveredIcon = ref(null);
    const iconClass = computed(() => {
      return `w-4 h-4 shrink-0 mr-2 transition duration-150 ease-in-out`;
    });
    const iconPathClass = (key) => {
      return hoveredIcon.value === key ? colorTextHover.value : colorText.value;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[--><div class="row-span-full" data-v-90560c81><div id="widgetPanel" class="${ssrRenderClass([[bgColorClass.value], "flex-col items-center h-full w-4 z-50 dark:border-l dark:border-gray-600 overflow-y-scroll hidden md:flex md:z-50 no-scrollbar transition-all duration-200 ease-in-out"])}" data-v-90560c81><a href="/" target="_blank" class="mt-16 ml-1"${ssrRenderAttr("title", unref(t)("website"))} data-v-90560c81><svg class="${ssrRenderClass(iconClass.value)}" viewBox="0 0 16 16" data-v-90560c81><path class="${ssrRenderClass(["fill-current", iconPathClass("site")])}" d="M10 16h4c.6 0 1-.4 1-.998V6.016c0-.3-.1-.6-.4-.8L8.6.226c-.4-.3-.9-.3-1.3 0l-6 4.992c-.2.2-.3.5-.3.799v8.986C1 15.6 1.4 16 2 16h4c.6 0 1-.4 1-.998v-2.996h2v2.996c0 .599.4.998 1 .998Zm-4-5.99c-.6 0-1 .399-1 .998v2.995H3V6.515L8 2.32l5 4.194v7.488h-2v-2.995c0-.6-.4-.999-1-.999H6Z" data-v-90560c81></path></svg></a><a href="/dashboard" target="_blank" class="mt-3 ml-1"${ssrRenderAttr("title", unref(t)("dashboard"))} data-v-90560c81><svg class="${ssrRenderClass(iconClass.value)}" viewBox="0 0 16 16" data-v-90560c81><path class="${ssrRenderClass(["fill-current", iconPathClass("dashboard")])}" d="M12.311 9.527c-1.161-.393-1.85-.825-2.143-1.175A3.991 3.991 0 0012 5V4c0-2.206-1.794-4-4-4S4 1.794 4 4v1c0 1.406.732 2.639 1.832 3.352-.292.35-.981.782-2.142 1.175A3.942 3.942 0 001 13.26V16h14v-2.74c0-1.69-1.081-3.19-2.689-3.733zM6 4c0-1.103.897-2 2-2s2 .897 2 2v1c0 1.103-.897 2-2 2s-2-.897-2-2V4zm7 10H3v-.74c0-.831.534-1.569 1.33-1.838 1.845-.624 3-1.436 3.452-2.422h.436c.452.986 1.607 1.798 3.453 2.422A1.943 1.943 0 0113 13.26V14z" data-v-90560c81></path></svg></a><button type="button" class="mt-3 ml-1"${ssrRenderAttr("title", unref(t)("translator"))} data-v-90560c81><svg class="${ssrRenderClass(iconClass.value)}" viewBox="0 0 24 24" data-v-90560c81><path class="${ssrRenderClass(["fill-current", iconPathClass("translator")])}" d="M12.87 15.07l-2.54-2.51.03-.03A17.52 17.52 0 0014.07 6H17V4h-7V2H8v2H1v2h11.17A15.7 15.7 0 019 11.17 15.16 15.16 0 016.91 8H4.91a17.39 17.39 0 002.77 4.36l-5.09 5.02L4 18.8l5-5 3.11 3.11.76-1.84zM18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12zm-2.62 7l1.62-4.33L19.12 17h-3.24z" data-v-90560c81></path></svg></button></div></div>`);
      _push(ssrRenderComponent(_sfc_main$n, {
        "is-open": isTranslatorOpen.value,
        onClose: closeTranslator
      }, null, _parent));
      _push(`<!--]-->`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Partials/Admin/WidgetPanel.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const WidgetPanel = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-90560c81"]]);
const _sfc_main = {
  __name: "AdminLayout",
  __ssrInlineRender: true,
  props: {
    title: String
  },
  setup(__props) {
    const sidebarOpen = ref(false);
    const showingNavigationDropdown = ref(false);
    const toast = useToast();
    watch(() => Container.props.flash, (flashMessages) => {
      if (flashMessages) {
        if (flashMessages.success) {
          toast.success(flashMessages.success);
        }
        if (flashMessages.error) {
          toast.error(flashMessages.error);
        }
        if (flashMessages.warning) {
          toast.warning(flashMessages.warning);
        }
        if (flashMessages.info) {
          toast.info(flashMessages.info);
        }
      }
    }, {
      deep: true
      // Наблюдаем за изменениями внутри объекта flash
      // immediate: true // Раскомментируйте, если нужно проверить flash при первой загрузке лэйаута
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), { title: __props.title }, null, _parent));
      _push(`<div class="flex flex-row h-screen overflow-hidden" data-v-e024fb85>`);
      _push(ssrRenderComponent(_sfc_main$3, {
        "sidebar-open": sidebarOpen.value,
        onCloseSidebar: ($event) => sidebarOpen.value = false
      }, null, _parent));
      _push(`<div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden" data-v-e024fb85>`);
      _push(ssrRenderComponent(_sfc_main$f, {
        "showing-navigation-dropdown": showingNavigationDropdown.value,
        onToggleNavigationDropdown: ($event) => showingNavigationDropdown.value = !showingNavigationDropdown.value
      }, null, _parent));
      if (_ctx.$slots.header) {
        _push(`<header class="dark:bg-slate-700 bg-slate-50 shadow" data-v-e024fb85><div class="max-w-7xl mx-auto py-2 px-1 sm:px-6 lg:px-8 border-b border-slate-300 dark:border-slate-700" data-v-e024fb85>`);
        ssrRenderSlot(_ctx.$slots, "header", {}, null, _push, _parent);
        _push(`</div></header>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<main class="flex-grow bg-center border-l border-r border-slate-400" data-v-e024fb85>`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(`</main>`);
      _push(ssrRenderComponent(_sfc_main$2, null, null, _parent));
      _push(ssrRenderComponent(ScrollButtons, null, null, _parent));
      _push(`</div>`);
      _push(ssrRenderComponent(WidgetPanel, null, null, _parent));
      _push(`</div><!--]-->`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Layouts/AdminLayout.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const AdminLayout = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-e024fb85"]]);
export {
  AdminLayout as A
};
