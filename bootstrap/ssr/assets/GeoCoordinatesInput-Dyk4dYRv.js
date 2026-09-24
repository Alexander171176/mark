import { computed, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrRenderClass, ssrRenderComponent, ssrRenderAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$2 } from "./InputError-CLVdJ1nk.js";
const _sfc_main = {
  __name: "GeoCoordinatesInput",
  __ssrInlineRender: true,
  props: {
    latitude: {
      type: [String, Number],
      default: ""
    },
    longitude: {
      type: [String, Number],
      default: ""
    },
    latitudeError: {
      type: String,
      default: ""
    },
    longitudeError: {
      type: String,
      default: ""
    },
    wrapperClass: {
      type: String,
      default: "lg:col-span-3 flex justify-center"
    },
    innerClass: {
      type: String,
      default: "w-full max-w-2xl"
    }
  },
  emits: [
    "update:latitude",
    "update:longitude"
  ],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const props = __props;
    const emit = __emit;
    const latitudeValue = computed({
      get: () => props.latitude ?? "",
      set: (value) => emit("update:latitude", value)
    });
    const longitudeValue = computed({
      get: () => props.longitude ?? "",
      set: (value) => emit("update:longitude", value)
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: __props.wrapperClass }, _attrs))}><div class="${ssrRenderClass(__props.innerClass)}"><div class="grid grid-cols-1 md:grid-cols-2 gap-6"><div class="flex flex-col">`);
      _push(ssrRenderComponent(_sfc_main$1, {
        for: "latitude",
        value: unref(t)("latitude")
      }, null, _parent));
      _push(`<input id="latitude" type="number" step="0.0000001" min="-90" max="90"${ssrRenderAttr("value", latitudeValue.value)} class="w-full px-2 py-0.5 font-semibold border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600">`);
      _push(ssrRenderComponent(_sfc_main$2, {
        class: "mt-2",
        message: __props.latitudeError
      }, null, _parent));
      _push(`</div><div class="flex flex-col">`);
      _push(ssrRenderComponent(_sfc_main$1, {
        for: "longitude",
        value: unref(t)("longitude")
      }, null, _parent));
      _push(`<input id="longitude" type="number" step="0.0000001" min="-180" max="180"${ssrRenderAttr("value", longitudeValue.value)} class="w-full px-2 py-0.5 font-semibold border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm bg-white dark:bg-cyan-800 dark:text-slate-100 text-gray-600">`);
      _push(ssrRenderComponent(_sfc_main$2, {
        class: "mt-2",
        message: __props.longitudeError
      }, null, _parent));
      _push(`</div></div></div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/UI/Geo/GeoCoordinatesInput.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
