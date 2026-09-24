import { withCtx, unref, createTextVNode, toDisplayString, createVNode, createBlock, createCommentVNode, openBlock, withModifiers, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate } from "vue/server-renderer";
import { useForm, Head, Link } from "@inertiajs/vue3";
import { H as HeadingAuth } from "./HeadingAuth-BF-kTGxB.js";
import { _ as _sfc_main$5 } from "./Checkbox-CgE3PSwb.js";
import { _ as _sfc_main$3, a as _sfc_main$4 } from "./TextInput-CCxUFX3K.js";
import { _ as _sfc_main$2 } from "./InputLabel-Ds0Eo91B.js";
import { _ as _sfc_main$6 } from "./PrimaryButton-D7EZDGT_.js";
import { useI18n } from "vue-i18n";
import { _ as _sfc_main$1 } from "./DefaultLayout-6zxGxPtK.js";
import { C as CustomImageAuthentication } from "./CustomImageAuthentication-1ESSKeFB.js";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
import "axios";
const _sfc_main = {
  __name: "Login",
  __ssrInlineRender: true,
  props: {
    canResetPassword: Boolean,
    status: String
  },
  setup(__props) {
    const form = useForm({
      email: "",
      password: "",
      remember: false
    });
    const submit = () => {
      form.transform((data) => ({
        ...data,
        remember: form.remember ? "on" : ""
      })).post(route("login"), {
        onFinish: () => form.reset("password")
      });
    };
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(_sfc_main$1, _attrs, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(unref(Head), {
              title: unref(t)("loginTitle")
            }, null, _parent2, _scopeId));
            _push2(`<div class="relative w-full h-screen overflow-hidden bg-slate-900 text-white" data-v-00195789${_scopeId}><div class="absolute top-0 left-0 w-full h-full z-0 animate-parallax" data-v-00195789${_scopeId}>`);
            _push2(ssrRenderComponent(CustomImageAuthentication, null, null, _parent2, _scopeId));
            _push2(`</div><div class="relative z-10 flex items-center justify-center h-full px-4" data-v-00195789${_scopeId}><div class="w-full max-w-sm bg-slate-700/80 px-8 py-4 rounded-lg shadow-lg text-slate-900 dark:text-white" data-v-00195789${_scopeId}>`);
            if (__props.status) {
              _push2(`<div class="mb-4 text-green-600 font-medium text-md" data-v-00195789${_scopeId}>${ssrInterpolate(__props.status)}</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(HeadingAuth, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("loginUser"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("loginUser")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<form data-v-00195789${_scopeId}><div data-v-00195789${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              for: "email",
              value: unref(t)("email")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              id: "email",
              modelValue: unref(form).email,
              "onUpdate:modelValue": ($event) => unref(form).email = $event,
              type: "email",
              class: "mt-1 block w-full",
              required: "",
              autofocus: "",
              autocomplete: "username"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.email
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mt-2" data-v-00195789${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              for: "password",
              value: unref(t)("password")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              id: "password",
              modelValue: unref(form).password,
              "onUpdate:modelValue": ($event) => unref(form).password = $event,
              type: "password",
              class: "mt-1 block w-full",
              required: "",
              autocomplete: "current-password"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.password
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="block mt-2" data-v-00195789${_scopeId}><label class="flex items-center" data-v-00195789${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$5, {
              checked: unref(form).remember,
              "onUpdate:checked": ($event) => unref(form).remember = $event,
              name: "remember"
            }, null, _parent2, _scopeId));
            _push2(`<span class="ml-2 text-md text-gray-100" data-v-00195789${_scopeId}>${ssrInterpolate(unref(t)("rememberMe"))}</span></label></div><div class="flex items-center justify-between mt-2" data-v-00195789${_scopeId}>`);
            if (__props.canResetPassword) {
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route("password.request"),
                class: "text-sky-200 hover:underline text-sm"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(unref(t)("forgotPassword"))}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(unref(t)("forgotPassword")), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: { "opacity-25": unref(form).processing },
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
            _push2(`</div></form><div class="pt-2 mt-2 border-t border-white/20 text-center" data-v-00195789${_scopeId}><p class="text-sm text-white" data-v-00195789${_scopeId}>${ssrInterpolate(unref(t)("registerPrompt"))} `);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("register"),
              class: "text-sky-200 hover:underline ml-1"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("register"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("register")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</p></div></div></div></div>`);
          } else {
            return [
              createVNode(unref(Head), {
                title: unref(t)("loginTitle")
              }, null, 8, ["title"]),
              createVNode("div", { class: "relative w-full h-screen overflow-hidden bg-slate-900 text-white" }, [
                createVNode("div", { class: "absolute top-0 left-0 w-full h-full z-0 animate-parallax" }, [
                  createVNode(CustomImageAuthentication)
                ]),
                createVNode("div", { class: "relative z-10 flex items-center justify-center h-full px-4" }, [
                  createVNode("div", { class: "w-full max-w-sm bg-slate-700/80 px-8 py-4 rounded-lg shadow-lg text-slate-900 dark:text-white" }, [
                    __props.status ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "mb-4 text-green-600 font-medium text-md"
                    }, toDisplayString(__props.status), 1)) : createCommentVNode("", true),
                    createVNode(HeadingAuth, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("loginUser")), 1)
                      ]),
                      _: 1
                    }),
                    createVNode("form", {
                      onSubmit: withModifiers(submit, ["prevent"])
                    }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$2, {
                          for: "email",
                          value: unref(t)("email")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$3, {
                          id: "email",
                          modelValue: unref(form).email,
                          "onUpdate:modelValue": ($event) => unref(form).email = $event,
                          type: "email",
                          class: "mt-1 block w-full",
                          required: "",
                          autofocus: "",
                          autocomplete: "username"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.email
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mt-2" }, [
                        createVNode(_sfc_main$2, {
                          for: "password",
                          value: unref(t)("password")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$3, {
                          id: "password",
                          modelValue: unref(form).password,
                          "onUpdate:modelValue": ($event) => unref(form).password = $event,
                          type: "password",
                          class: "mt-1 block w-full",
                          required: "",
                          autocomplete: "current-password"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.password
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "block mt-2" }, [
                        createVNode("label", { class: "flex items-center" }, [
                          createVNode(_sfc_main$5, {
                            checked: unref(form).remember,
                            "onUpdate:checked": ($event) => unref(form).remember = $event,
                            name: "remember"
                          }, null, 8, ["checked", "onUpdate:checked"]),
                          createVNode("span", { class: "ml-2 text-md text-gray-100" }, toDisplayString(unref(t)("rememberMe")), 1)
                        ])
                      ]),
                      createVNode("div", { class: "flex items-center justify-between mt-2" }, [
                        __props.canResetPassword ? (openBlock(), createBlock(unref(Link), {
                          key: 0,
                          href: _ctx.route("password.request"),
                          class: "text-sky-200 hover:underline text-sm"
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("forgotPassword")), 1)
                          ]),
                          _: 1
                        }, 8, ["href"])) : createCommentVNode("", true),
                        createVNode(_sfc_main$6, {
                          class: { "opacity-25": unref(form).processing },
                          disabled: unref(form).processing
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("login")), 1)
                          ]),
                          _: 1
                        }, 8, ["class", "disabled"])
                      ])
                    ], 32),
                    createVNode("div", { class: "pt-2 mt-2 border-t border-white/20 text-center" }, [
                      createVNode("p", { class: "text-sm text-white" }, [
                        createTextVNode(toDisplayString(unref(t)("registerPrompt")) + " ", 1),
                        createVNode(unref(Link), {
                          href: _ctx.route("register"),
                          class: "text-sky-200 hover:underline ml-1"
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("register")), 1)
                          ]),
                          _: 1
                        }, 8, ["href"])
                      ])
                    ])
                  ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Auth/Login.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Login = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-00195789"]]);
export {
  Login as default
};
