import { ref, withCtx, unref, createTextVNode, toDisplayString, createVNode, createBlock, openBlock, Fragment, withModifiers, nextTick, useSSRContext } from "vue";
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
  __name: "TwoFactorChallenge",
  __ssrInlineRender: true,
  setup(__props) {
    const recovery = ref(false);
    const form = useForm({
      code: "",
      recovery_code: ""
    });
    const recoveryCodeInput = ref(null);
    const codeInput = ref(null);
    const toggleRecovery = async () => {
      recovery.value ^= true;
      await nextTick();
      if (recovery.value) {
        recoveryCodeInput.value.focus();
        form.code = "";
      } else {
        codeInput.value.focus();
        form.recovery_code = "";
      }
    };
    const submit = () => {
      form.post(route("two-factor.login"));
    };
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(_sfc_main$1, _attrs, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(unref(Head), {
              title: unref(t)("twoFactorConfirmationTitle")
            }, null, _parent2, _scopeId));
            _push2(`<div class="relative sm:flex sm:justify-center sm:items-center min-h-screen w-full bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-slate-900 selection:bg-red-500 selection:text-white"${_scopeId}><div class="flex flex-row flex-wrap w-full"${_scopeId}><div class="w-full md:w-1/2"${_scopeId}><div class="min-h-screen h-full flex flex-col justify-center items-center"${_scopeId}><div class="flex flex-col justify-center items-center max-w-sm mx-auto px-4 py-8"${_scopeId}><div class="mb-4 flex flex-col justify-center items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, null, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(HeadingAuth, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("twoFactorConfirmation"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("twoFactorConfirmation")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<div class="mb-4 font-semibold text-md text-gray-900"${_scopeId}>`);
            if (!recovery.value) {
              _push2(`<!--[-->${ssrInterpolate(unref(t)("authCodeDescription"))}<!--]-->`);
            } else {
              _push2(`<!--[-->${ssrInterpolate(unref(t)("recoveryCodeDescription"))}<!--]-->`);
            }
            _push2(`</div><form${_scopeId}>`);
            if (!recovery.value) {
              _push2(`<div${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$3, {
                for: "code",
                value: unref(t)("authCode")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, {
                id: "code",
                ref_key: "codeInput",
                ref: codeInput,
                modelValue: unref(form).code,
                "onUpdate:modelValue": ($event) => unref(form).code = $event,
                type: "text",
                inputmode: "numeric",
                class: "mt-1 block w-full",
                autofocus: "",
                autocomplete: "one-time-code"
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$5, {
                class: "mt-2",
                message: unref(form).errors.code
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<div${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$3, {
                for: "recovery_code",
                value: unref(t)("recoveryCode")
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$4, {
                id: "recovery_code",
                ref_key: "recoveryCodeInput",
                ref: recoveryCodeInput,
                modelValue: unref(form).recovery_code,
                "onUpdate:modelValue": ($event) => unref(form).recovery_code = $event,
                type: "text",
                class: "mt-1 block w-full",
                autocomplete: "one-time-code"
              }, null, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$5, {
                class: "mt-2",
                message: unref(form).errors.recovery_code
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            }
            _push2(`<div class="flex items-center justify-end mt-4"${_scopeId}><button type="button" class="text-md text-gray-600 hover:text-gray-900 underline cursor-pointer"${_scopeId}>`);
            if (!recovery.value) {
              _push2(`<!--[-->${ssrInterpolate(unref(t)("useRecoveryCode"))}<!--]-->`);
            } else {
              _push2(`<!--[-->${ssrInterpolate(unref(t)("useAuthCode"))}<!--]-->`);
            }
            _push2(`</button>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: ["ms-4", { "opacity-25": unref(form).processing }],
              disabled: unref(form).processing
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("login"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("login")), 1)
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
                title: unref(t)("twoFactorConfirmationTitle")
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
                              createTextVNode(toDisplayString(unref(t)("twoFactorConfirmation")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode("div", { class: "mb-4 font-semibold text-md text-gray-900" }, [
                            !recovery.value ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                              createTextVNode(toDisplayString(unref(t)("authCodeDescription")), 1)
                            ], 64)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                              createTextVNode(toDisplayString(unref(t)("recoveryCodeDescription")), 1)
                            ], 64))
                          ]),
                          createVNode("form", {
                            onSubmit: withModifiers(submit, ["prevent"])
                          }, [
                            !recovery.value ? (openBlock(), createBlock("div", { key: 0 }, [
                              createVNode(_sfc_main$3, {
                                for: "code",
                                value: unref(t)("authCode")
                              }, null, 8, ["value"]),
                              createVNode(_sfc_main$4, {
                                id: "code",
                                ref_key: "codeInput",
                                ref: codeInput,
                                modelValue: unref(form).code,
                                "onUpdate:modelValue": ($event) => unref(form).code = $event,
                                type: "text",
                                inputmode: "numeric",
                                class: "mt-1 block w-full",
                                autofocus: "",
                                autocomplete: "one-time-code"
                              }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                              createVNode(_sfc_main$5, {
                                class: "mt-2",
                                message: unref(form).errors.code
                              }, null, 8, ["message"])
                            ])) : (openBlock(), createBlock("div", { key: 1 }, [
                              createVNode(_sfc_main$3, {
                                for: "recovery_code",
                                value: unref(t)("recoveryCode")
                              }, null, 8, ["value"]),
                              createVNode(_sfc_main$4, {
                                id: "recovery_code",
                                ref_key: "recoveryCodeInput",
                                ref: recoveryCodeInput,
                                modelValue: unref(form).recovery_code,
                                "onUpdate:modelValue": ($event) => unref(form).recovery_code = $event,
                                type: "text",
                                class: "mt-1 block w-full",
                                autocomplete: "one-time-code"
                              }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                              createVNode(_sfc_main$5, {
                                class: "mt-2",
                                message: unref(form).errors.recovery_code
                              }, null, 8, ["message"])
                            ])),
                            createVNode("div", { class: "flex items-center justify-end mt-4" }, [
                              createVNode("button", {
                                type: "button",
                                class: "text-md text-gray-600 hover:text-gray-900 underline cursor-pointer",
                                onClick: withModifiers(toggleRecovery, ["prevent"])
                              }, [
                                !recovery.value ? (openBlock(), createBlock(Fragment, { key: 0 }, [
                                  createTextVNode(toDisplayString(unref(t)("useRecoveryCode")), 1)
                                ], 64)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                                  createTextVNode(toDisplayString(unref(t)("useAuthCode")), 1)
                                ], 64))
                              ]),
                              createVNode(_sfc_main$6, {
                                class: ["ms-4", { "opacity-25": unref(form).processing }],
                                disabled: unref(form).processing
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(toDisplayString(unref(t)("login")), 1)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Auth/TwoFactorChallenge.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
