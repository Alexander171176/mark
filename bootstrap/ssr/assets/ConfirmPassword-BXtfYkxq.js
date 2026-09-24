import { ref, withCtx, unref, createTextVNode, toDisplayString, createVNode, withModifiers, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { useForm, Head } from "@inertiajs/vue3";
import { _ as _sfc_main$2, I as ImageAuthentication } from "./AuthenticationCardLogo-BDy7i6k4.js";
import { H as HeadingAuth } from "./HeadingAuth-BF-kTGxB.js";
import { _ as _sfc_main$4, a as _sfc_main$5 } from "./TextInput-CCxUFX3K.js";
import { _ as _sfc_main$3 } from "./InputLabel-Ds0Eo91B.js";
import { _ as _sfc_main$6 } from "./PrimaryButton-D7EZDGT_.js";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import "./auth-image-CfsIGyOn.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
import "axios";
const _sfc_main = {
  __name: "ConfirmPassword",
  __ssrInlineRender: true,
  setup(__props) {
    const form = useForm({
      password: ""
    });
    const passwordInput = ref(null);
    const submit = () => {
      form.post(route("password.confirm"), {
        onFinish: () => {
          form.reset();
          passwordInput.value.focus();
        }
      });
    };
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(_sfc_main$1, _attrs, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(unref(Head), {
              title: unref(t)("passwordConfirmation")
            }, null, _parent2, _scopeId));
            _push2(`<div class="relative sm:flex sm:justify-center sm:items-center min-h-screen w-full bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-slate-900 selection:bg-red-500 selection:text-white"${_scopeId}><div class="flex flex-row flex-wrap w-full"${_scopeId}><div class="w-full md:w-1/2"${_scopeId}><div class="min-h-screen h-full flex flex-col justify-center items-center"${_scopeId}><div class="flex flex-col justify-center items-center max-w-sm mx-auto px-4 py-8"${_scopeId}><div class="mb-4 flex flex-col justify-center items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, null, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(HeadingAuth, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("passwordConfirmation"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("passwordConfirmation")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<div class="mb-4 font-semibold text-md text-gray-900"${_scopeId}>${ssrInterpolate(unref(t)("passwordConfirmationMessage"))}</div><form${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$3, {
              for: "password",
              value: unref(t)("password")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              id: "password",
              ref_key: "passwordInput",
              ref: passwordInput,
              modelValue: unref(form).password,
              "onUpdate:modelValue": ($event) => unref(form).password = $event,
              type: "password",
              class: "mt-1 block w-full",
              required: "",
              autocomplete: "current-password",
              autofocus: ""
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              class: "mt-2",
              message: unref(form).errors.password
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex justify-end mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: ["ms-4", { "opacity-25": unref(form).processing }],
              disabled: unref(form).processing
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("confirm"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("confirm")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></form></div></div></div></div>`);
            _push2(ssrRenderComponent(ImageAuthentication, null, null, _parent2, _scopeId));
            _push2(`</div></div>`);
          } else {
            return [
              createVNode(unref(Head), {
                title: unref(t)("passwordConfirmation")
              }, null, 8, ["title"]),
              createVNode("div", { class: "relative sm:flex sm:justify-center sm:items-center min-h-screen w-full bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-slate-900 selection:bg-red-500 selection:text-white" }, [
                createVNode("div", { class: "flex flex-row flex-wrap w-full" }, [
                  createVNode("div", { class: "w-full md:w-1/2" }, [
                    createVNode("div", { class: "min-h-screen h-full flex flex-col justify-center items-center" }, [
                      createVNode("div", { class: "flex flex-col justify-center items-center max-w-sm mx-auto px-4 py-8" }, [
                        createVNode("div", { class: "mb-4 flex flex-col justify-center items-center" }, [
                          createVNode(_sfc_main$2)
                        ]),
                        createVNode("div", null, [
                          createVNode(HeadingAuth, null, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("passwordConfirmation")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode("div", { class: "mb-4 font-semibold text-md text-gray-900" }, toDisplayString(unref(t)("passwordConfirmationMessage")), 1),
                          createVNode("form", {
                            onSubmit: withModifiers(submit, ["prevent"])
                          }, [
                            createVNode("div", null, [
                              createVNode(_sfc_main$3, {
                                for: "password",
                                value: unref(t)("password")
                              }, null, 8, ["value"]),
                              createVNode(_sfc_main$4, {
                                id: "password",
                                ref_key: "passwordInput",
                                ref: passwordInput,
                                modelValue: unref(form).password,
                                "onUpdate:modelValue": ($event) => unref(form).password = $event,
                                type: "password",
                                class: "mt-1 block w-full",
                                required: "",
                                autocomplete: "current-password",
                                autofocus: ""
                              }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                              createVNode(_sfc_main$5, {
                                class: "mt-2",
                                message: unref(form).errors.password
                              }, null, 8, ["message"])
                            ]),
                            createVNode("div", { class: "flex justify-end mt-4" }, [
                              createVNode(_sfc_main$6, {
                                class: ["ms-4", { "opacity-25": unref(form).processing }],
                                disabled: unref(form).processing
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(toDisplayString(unref(t)("confirm")), 1)
                                ]),
                                _: 1
                              }, 8, ["class", "disabled"])
                            ])
                          ], 32)
                        ])
                      ])
                    ])
                  ]),
                  createVNode(ImageAuthentication)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Auth/ConfirmPassword.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
