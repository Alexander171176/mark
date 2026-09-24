import { computed, ref, onMounted, mergeProps, useSSRContext, onBeforeUnmount, inject, unref } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrIncludeBooleanAttr, ssrRenderSlot, ssrRenderComponent } from "vue/server-renderer";
import { usePage } from "@inertiajs/vue3";
import axios from "axios";
const CONSENT_ACCEPTED_KEY$1 = "privacy_user_consent_accepted";
const CONSENT_VERSION_KEY$1 = "privacy_user_consent_version";
const POLICY_VERSION$1 = "1";
const _sfc_main$2 = {
  __name: "CookieConsentPopup",
  __ssrInlineRender: true,
  setup(__props) {
    const page = usePage();
    const privacy = computed(() => {
      var _a, _b, _c;
      return ((_c = (_b = (_a = page.props.laravelLang) == null ? void 0 : _a.public) == null ? void 0 : _b.privacy) == null ? void 0 : _c.popup) ?? {};
    });
    const isVisible = ref(false);
    const isLoading = ref(false);
    const hasLocalConsent = () => {
      return localStorage.getItem(CONSENT_ACCEPTED_KEY$1) === "true" && localStorage.getItem(CONSENT_VERSION_KEY$1) === POLICY_VERSION$1;
    };
    onMounted(() => {
      isVisible.value = !hasLocalConsent();
    });
    return (_ctx, _push, _parent, _attrs) => {
      if (isVisible.value) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "fixed inset-x-0 bottom-0 z-50 px-4 pb-4 sm:px-6 sm:pb-6" }, _attrs))}><div class="mx-auto max-w-5xl rounded-2xl border-2 border-gray-200 bg-white p-5 shadow-2xl dark:border-gray-700 dark:bg-gray-900"><div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between"><div class="text-sm text-gray-700 dark:text-gray-200"><h3 class="mb-1 text-base font-semibold text-gray-900 dark:text-white">${ssrInterpolate(privacy.value.title)}</h3><p>${ssrInterpolate(privacy.value.description)} <a href="/privacy" target="_blank" class="font-medium text-blue-600 underline hover:text-blue-700 dark:text-blue-400">${ssrInterpolate(privacy.value.policy)}</a>. </p></div><button type="button"${ssrIncludeBooleanAttr(isLoading.value) ? " disabled" : ""} class="shrink-0 rounded-md bg-blue-600 px-3 py-1 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">${ssrInterpolate(isLoading.value ? privacy.value.saving : privacy.value.accept)}</button></div></div></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Privacy/CookieConsentPopup.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const CONSENT_ACCEPTED_KEY = "privacy_user_consent_accepted";
const CONSENT_VERSION_KEY = "privacy_user_consent_version";
const VISITOR_UUID_KEY = "analytics_visitor_uuid";
const POLICY_VERSION = "1";
const _sfc_main$1 = {
  __name: "AnalyticsVisitorTracker",
  __ssrInlineRender: true,
  setup(__props) {
    let startedAt = Date.now();
    let maxScrollDepth = 0;
    let clicksCount = 0;
    const getVisitorUuid = () => localStorage.getItem(VISITOR_UUID_KEY);
    const setVisitorUuid = (uuid) => {
      if (uuid) {
        localStorage.setItem(VISITOR_UUID_KEY, uuid);
      }
    };
    const hasLocalConsent = () => {
      return localStorage.getItem(CONSENT_ACCEPTED_KEY) === "true" && localStorage.getItem(CONSENT_VERSION_KEY) === POLICY_VERSION;
    };
    const getScreenData = () => {
      var _a, _b;
      return {
        screen_width: ((_a = window.screen) == null ? void 0 : _a.width) ?? null,
        screen_height: ((_b = window.screen) == null ? void 0 : _b.height) ?? null,
        browser_language: navigator.language ?? null,
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone ?? null
      };
    };
    const updateScrollDepth = () => {
      const scrollTop = window.scrollY || document.documentElement.scrollTop;
      const documentHeight = document.documentElement.scrollHeight - window.innerHeight;
      if (documentHeight <= 0) {
        maxScrollDepth = 100;
        return;
      }
      const currentDepth = Math.round(scrollTop / documentHeight * 100);
      if (currentDepth > maxScrollDepth) {
        maxScrollDepth = Math.min(currentDepth, 100);
      }
    };
    const sendAnalytics = async () => {
      var _a;
      if (!hasLocalConsent()) {
        return;
      }
      updateScrollDepth();
      try {
        const response = await axios.post("/api/analytics/visitor-logs", {
          visitor_uuid: getVisitorUuid(),
          method: "GET",
          url: window.location.href,
          page_title: document.title || null,
          route_name: null,
          module: null,
          entity_type: null,
          entity_id: null,
          event_type: "page_view",
          request_type: "web",
          referer: document.referrer || null,
          time_on_page: Math.round((Date.now() - startedAt) / 1e3),
          scroll_depth: maxScrollDepth,
          clicks_count: clicksCount,
          locale: document.documentElement.lang || null,
          ...getScreenData()
        });
        setVisitorUuid((_a = response.data) == null ? void 0 : _a.visitor_uuid);
      } catch (error) {
        console.error("Ошибка сохранения аналитики:", error);
      }
    };
    const handleClick = () => {
      clicksCount++;
    };
    onMounted(() => {
      startedAt = Date.now();
      window.addEventListener("scroll", updateScrollDepth, { passive: true });
      window.addEventListener("click", handleClick);
      window.addEventListener("beforeunload", sendAnalytics);
      document.addEventListener("visibilitychange", () => {
        if (document.visibilityState === "hidden") {
          sendAnalytics();
        }
      });
    });
    onBeforeUnmount(() => {
      sendAnalytics();
      window.removeEventListener("scroll", updateScrollDepth);
      window.removeEventListener("click", handleClick);
      window.removeEventListener("beforeunload", sendAnalytics);
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<span${ssrRenderAttrs(mergeProps({
        class: "hidden",
        "aria-hidden": "true"
      }, _attrs))}></span>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Privacy/AnalyticsVisitorTracker.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "DefaultLayout",
  __ssrInlineRender: true,
  setup(__props) {
    const isDark = inject("isDark");
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        id: "app",
        class: [unref(isDark) ? "is-dark" : ""]
      }, _attrs))}>`);
      ssrRenderSlot(_ctx.$slots, "default", {}, null, _push, _parent);
      _push(ssrRenderComponent(_sfc_main$2, null, null, _parent));
      _push(ssrRenderComponent(_sfc_main$1, null, null, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Layouts/DefaultLayout.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
