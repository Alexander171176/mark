import { shallowRef, onMounted, watch, defineAsyncComponent, useSSRContext } from "vue";
const _sfc_main = {
  __name: "Index",
  props: {
    canLogin: Boolean,
    canRegister: Boolean,
    template: String,
    laravelVersion: String,
    phpVersion: String
  },
  setup(__props) {
    const props = __props;
    const importTemplates = () => {
      const context = /* @__PURE__ */ Object.assign({});
      const components2 = {};
      for (const path in context) {
        const templateName = path.split("/")[5];
        components2[templateName] = defineAsyncComponent(
          context[path]
        );
      }
      return components2;
    };
    const components = importTemplates();
    const currentComponent = shallowRef(null);
    const updateComponent = () => {
      const template = props.template || "Default";
      currentComponent.value = components[template] || components["Default"];
    };
    onMounted(() => {
      updateComponent();
    });
    watch(
      () => props.template,
      () => {
        updateComponent();
      }
    );
    return () => {
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
