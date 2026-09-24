import { computed, withCtx, unref, createTextVNode, toDisplayString, createVNode, createBlock, createCommentVNode, openBlock, withModifiers, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { _ as _sfc_main$2, I as ImageAuthentication } from "./AuthenticationCardLogo-BDy7i6k4.js";
import { H as HeadingAuth } from "./HeadingAuth-BF-kTGxB.js";
import { _ as _sfc_main$3 } from "./PrimaryButton-D7EZDGT_.js";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import "./auth-image-CfsIGyOn.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
import "axios";
const _sfc_main = {
  __name: "VerifyEmail",
  __ssrInlineRender: true,
  props: {
    status: String
  },
  setup(__props) {
    const props = __props;
    const form = useForm({});
    const submit = () => {
      form.post(route("verification.send"));
    };
    const verificationLinkSent2 = computed(() => props.status === "verification-link-sent");
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(_sfc_main$1, _attrs, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(unref(Head), {
              title: unref(t)("emailVerificationTitle")
            }, null, _parent2, _scopeId));
            _push2(`<div class="relative sm:flex sm:justify-center sm:items-center min-h-screen w-full bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-slate-900 selection:bg-red-500 selection:text-white"${_scopeId}><div class="flex flex-row flex-wrap w-full"${_scopeId}><div class="w-full md:w-1/2"${_scopeId}><div class="min-h-screen h-full flex flex-col justify-center items-center"${_scopeId}>`);
            if (verificationLinkSent2.value) {
              _push2(`<div class="mb-4 font-medium text-md text-green-600"${_scopeId}>${ssrInterpolate(unref(t)("verificationLinkSent2"))}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="flex flex-col justify-center items-center max-w-sm mx-auto px-4 py-8"${_scopeId}><div class="mb-4 flex flex-col justify-center items-center"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, null, null, _parent2, _scopeId));
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(HeadingAuth, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("resetPassword"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("resetPassword")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<div class="mb-4 font-semibold text-md text-gray-900"${_scopeId}>${ssrInterpolate(unref(t)("confirmEmailPrompt"))}</div><form${_scopeId}><div class="mt-4 flex items-center justify-between"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: { "opacity-25": unref(form).processing },
              disabled: unref(form).processing
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("resendVerification"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("resendVerification")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<div${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("profile.show"),
              class: "underline text-md text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editProfile"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editProfile")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("logout"),
              method: "post",
              as: "button",
              class: "underline text-md text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-2"
            }, {
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
            _push2(`</div></div></form></div></div></div></div>`);
            _push2(ssrRenderComponent(ImageAuthentication, null, null, _parent2, _scopeId));
            _push2(`</div></div>`);
          } else {
            return [
              createVNode(unref(Head), {
                title: unref(t)("emailVerificationTitle")
              }, null, 8, ["title"]),
              createVNode("div", { class: "relative sm:flex sm:justify-center sm:items-center min-h-screen w-full bg-dots-darker bg-center bg-gray-100 dark:bg-dots-lighter dark:bg-slate-900 selection:bg-red-500 selection:text-white" }, [
                createVNode("div", { class: "flex flex-row flex-wrap w-full" }, [
                  createVNode("div", { class: "w-full md:w-1/2" }, [
                    createVNode("div", { class: "min-h-screen h-full flex flex-col justify-center items-center" }, [
                      verificationLinkSent2.value ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "mb-4 font-medium text-md text-green-600"
                      }, toDisplayString(unref(t)("verificationLinkSent2")), 1)) : createCommentVNode("", true),
                      createVNode("div", { class: "flex flex-col justify-center items-center max-w-sm mx-auto px-4 py-8" }, [
                        createVNode("div", { class: "mb-4 flex flex-col justify-center items-center" }, [
                          createVNode(_sfc_main$2)
                        ]),
                        createVNode("div", null, [
                          createVNode(HeadingAuth, null, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("resetPassword")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode("div", { class: "mb-4 font-semibold text-md text-gray-900" }, toDisplayString(unref(t)("confirmEmailPrompt")), 1),
                          createVNode("form", {
                            onSubmit: withModifiers(submit, ["prevent"])
                          }, [
                            createVNode("div", { class: "mt-4 flex items-center justify-between" }, [
                              createVNode(_sfc_main$3, {
                                class: { "opacity-25": unref(form).processing },
                                disabled: unref(form).processing
                              }, {
                                default: withCtx(() => [
                                  createTextVNode(toDisplayString(unref(t)("resendVerification")), 1)
                                ]),
                                _: 1
                              }, 8, ["class", "disabled"]),
                              createVNode("div", null, [
                                createVNode(unref(Link), {
                                  href: _ctx.route("profile.show"),
                                  class: "underline text-md text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(unref(t)("editProfile")), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"]),
                                createVNode(unref(Link), {
                                  href: _ctx.route("logout"),
                                  method: "post",
                                  as: "button",
                                  class: "underline text-md text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 ms-2"
                                }, {
                                  default: withCtx(() => [
                                    createTextVNode(toDisplayString(unref(t)("logout")), 1)
                                  ]),
                                  _: 1
                                }, 8, ["href"])
                              ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Auth/VerifyEmail.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
