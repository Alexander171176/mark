import { ref, watch, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderClass, ssrInterpolate } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
const _sfc_main = {
  __name: "LikeButtonEntity",
  __ssrInlineRender: true,
  props: {
    likesCount: {
      type: Number,
      default: 0
    },
    alreadyLiked: {
      type: Boolean,
      default: false
    },
    routeName: {
      type: String,
      required: true
    },
    routeParams: {
      type: [Object, Array, String, Number],
      default: () => ({})
    },
    title: {
      type: String,
      default: ""
    },
    iconClass: {
      type: String,
      default: "w-3 h-3"
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const likes = ref(Number(props.likesCount) || 0);
    const liked = ref(!!props.alreadyLiked);
    const isAnimating = ref(false);
    const showBurst = ref(false);
    const showPlusOne = ref(false);
    watch(
      () => props.likesCount,
      (value) => {
        likes.value = Number(value) || 0;
      }
    );
    watch(
      () => props.alreadyLiked,
      (value) => {
        liked.value = !!value;
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({
        itemprop: "interactionStatistic",
        itemscope: "",
        itemtype: "https://schema.org/InteractionCounter"
      }, _attrs))} data-v-1153760d><link itemprop="interactionType" href="https://schema.org/LikeAction" data-v-1153760d><meta itemprop="userInteractionCount"${ssrRenderAttr("content", likes.value)} data-v-1153760d><div${ssrRenderAttr("title", __props.title || unref(t)("like"))} class="relative w-fit flex flex-row items-center justify-center cursor-pointer select-none" data-v-1153760d><div class="relative flex items-center justify-center" data-v-1153760d>`);
      if (showBurst.value) {
        _push(`<span class="pointer-events-none absolute inset-0 rounded-full burst-ring burst-ring-1" data-v-1153760d></span>`);
      } else {
        _push(`<!---->`);
      }
      if (showBurst.value) {
        _push(`<span class="pointer-events-none absolute inset-0 rounded-full burst-ring burst-ring-2" data-v-1153760d></span>`);
      } else {
        _push(`<!---->`);
      }
      if (showPlusOne.value) {
        _push(`<span class="pointer-events-none absolute -top-4 left-1/2 -translate-x-1/2 text-[10px] font-bold text-rose-500 dark:text-rose-300 plus-one" data-v-1153760d> +1 </span>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<svg class="${ssrRenderClass([
        __props.iconClass,
        "relative z-10 fill-current transition-all duration-200 transform hover:scale-110 active:scale-95",
        isAnimating.value ? "like-bounce" : "",
        liked.value ? "text-red-500 dark:text-red-300" : "text-amber-500 dark:text-amber-400 hover:text-yellow-600 dark:hover:text-yellow-200 active:text-yellow-300 dark:active:text-yellow-100"
      ])}" viewBox="0 0 24 24" data-v-1153760d><path d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z" data-v-1153760d></path><path d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z" data-v-1153760d></path></svg></div>`);
      if (likes.value > 0) {
        _push(`<span class="ml-1 font-semibold text-xs dark:text-slate-100" data-v-1153760d>${ssrInterpolate(likes.value)}</span>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Like/LikeButtonEntity.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const LikeButtonEntity = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-1153760d"]]);
export {
  LikeButtonEntity as L
};
