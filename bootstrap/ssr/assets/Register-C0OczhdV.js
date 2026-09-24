import { withCtx, unref, createTextVNode, toDisplayString, createVNode, withModifiers, createBlock, createCommentVNode, openBlock, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr } from "vue/server-renderer";
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
  __name: "Register",
  __ssrInlineRender: true,
  setup(__props) {
    const form = useForm({
      name: "",
      email: "",
      password: "",
      password_confirmation: "",
      terms: false
    });
    const submit = () => {
      form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation")
      });
    };
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(_sfc_main$1, _attrs, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(unref(Head), {
              title: unref(t)("register")
            }, null, _parent2, _scopeId));
            _push2(`<div class="relative w-full h-screen overflow-hidden bg-slate-900 text-white" data-v-56dc0223${_scopeId}><div class="absolute top-0 left-0 w-full h-full z-0 animate-parallax" data-v-56dc0223${_scopeId}>`);
            _push2(ssrRenderComponent(CustomImageAuthentication, null, null, _parent2, _scopeId));
            _push2(`</div><div class="relative z-10 flex items-center justify-center h-full px-4" data-v-56dc0223${_scopeId}><div class="w-full max-w-sm bg-slate-700/80 px-8 py-4 rounded-lg shadow-lg text-slate-900 dark:text-white" data-v-56dc0223${_scopeId}>`);
            _push2(ssrRenderComponent(HeadingAuth, null, {
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
            _push2(`<form data-v-56dc0223${_scopeId}><div data-v-56dc0223${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              for: "name",
              value: unref(t)("name")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              id: "name",
              modelValue: unref(form).name,
              "onUpdate:modelValue": ($event) => unref(form).name = $event,
              type: "text",
              class: "mt-1 block w-full",
              required: "",
              autofocus: "",
              autocomplete: "name"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.name
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mt-2" data-v-56dc0223${_scopeId}>`);
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
              autocomplete: "username"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.email
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mt-2" data-v-56dc0223${_scopeId}>`);
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
              autocomplete: "new-password"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.password
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mt-2" data-v-56dc0223${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              for: "password_confirmation",
              value: unref(t)("confirmPassword")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              id: "password_confirmation",
              modelValue: unref(form).password_confirmation,
              "onUpdate:modelValue": ($event) => unref(form).password_confirmation = $event,
              type: "password",
              class: "mt-1 block w-full",
              required: "",
              autocomplete: "new-password"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              class: "mt-2",
              message: unref(form).errors.password_confirmation
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (_ctx.$page.props.jetstream.hasTermsAndPrivacyPolicyFeature) {
              _push2(`<div class="mt-2" data-v-56dc0223${_scopeId}><label class="flex items-start text-sm text-white space-x-2" data-v-56dc0223${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$5, {
                id: "terms",
                checked: unref(form).terms,
                "onUpdate:checked": ($event) => unref(form).terms = $event,
                name: "terms",
                required: ""
              }, null, _parent2, _scopeId));
              _push2(`<span data-v-56dc0223${_scopeId}>${ssrInterpolate(unref(t)("agreeTerms1"))} <a${ssrRenderAttr("href", _ctx.route("terms.show"))} class="underline hover:text-sky-300" target="_blank" data-v-56dc0223${_scopeId}>${ssrInterpolate(unref(t)("termsOfService"))}</a> ${ssrInterpolate(unref(t)("and"))} <a${ssrRenderAttr("href", _ctx.route("policy.show"))} class="underline hover:text-sky-300" target="_blank" data-v-56dc0223${_scopeId}>${ssrInterpolate(unref(t)("privacyPolicy"))}</a></span></label>`);
              _push2(ssrRenderComponent(_sfc_main$4, {
                class: "mt-2",
                message: unref(form).errors.terms
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="flex items-center justify-end mt-2" data-v-56dc0223${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              class: { "opacity-25": unref(form).processing },
              disabled: unref(form).processing
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
            _push2(`</div></form><div class="pt-2 mt-2 border-t border-white/20 text-center" data-v-56dc0223${_scopeId}><p class="text-sm text-white" data-v-56dc0223${_scopeId}>${ssrInterpolate(unref(t)("alreadyRegistered"))} `);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("login"),
              class: "text-sky-200 hover:underline ml-1"
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
            _push2(`</p></div></div></div></div>`);
          } else {
            return [
              createVNode(unref(Head), {
                title: unref(t)("register")
              }, null, 8, ["title"]),
              createVNode("div", { class: "relative w-full h-screen overflow-hidden bg-slate-900 text-white" }, [
                createVNode("div", { class: "absolute top-0 left-0 w-full h-full z-0 animate-parallax" }, [
                  createVNode(CustomImageAuthentication)
                ]),
                createVNode("div", { class: "relative z-10 flex items-center justify-center h-full px-4" }, [
                  createVNode("div", { class: "w-full max-w-sm bg-slate-700/80 px-8 py-4 rounded-lg shadow-lg text-slate-900 dark:text-white" }, [
                    createVNode(HeadingAuth, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(unref(t)("register")), 1)
                      ]),
                      _: 1
                    }),
                    createVNode("form", {
                      onSubmit: withModifiers(submit, ["prevent"])
                    }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$2, {
                          for: "name",
                          value: unref(t)("name")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$3, {
                          id: "name",
                          modelValue: unref(form).name,
                          "onUpdate:modelValue": ($event) => unref(form).name = $event,
                          type: "text",
                          class: "mt-1 block w-full",
                          required: "",
                          autofocus: "",
                          autocomplete: "name"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.name
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mt-2" }, [
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
                          autocomplete: "new-password"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.password
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "mt-2" }, [
                        createVNode(_sfc_main$2, {
                          for: "password_confirmation",
                          value: unref(t)("confirmPassword")
                        }, null, 8, ["value"]),
                        createVNode(_sfc_main$3, {
                          id: "password_confirmation",
                          modelValue: unref(form).password_confirmation,
                          "onUpdate:modelValue": ($event) => unref(form).password_confirmation = $event,
                          type: "password",
                          class: "mt-1 block w-full",
                          required: "",
                          autocomplete: "new-password"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.password_confirmation
                        }, null, 8, ["message"])
                      ]),
                      _ctx.$page.props.jetstream.hasTermsAndPrivacyPolicyFeature ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "mt-2"
                      }, [
                        createVNode("label", { class: "flex items-start text-sm text-white space-x-2" }, [
                          createVNode(_sfc_main$5, {
                            id: "terms",
                            checked: unref(form).terms,
                            "onUpdate:checked": ($event) => unref(form).terms = $event,
                            name: "terms",
                            required: ""
                          }, null, 8, ["checked", "onUpdate:checked"]),
                          createVNode("span", null, [
                            createTextVNode(toDisplayString(unref(t)("agreeTerms1")) + " ", 1),
                            createVNode("a", {
                              href: _ctx.route("terms.show"),
                              class: "underline hover:text-sky-300",
                              target: "_blank"
                            }, toDisplayString(unref(t)("termsOfService")), 9, ["href"]),
                            createTextVNode(" " + toDisplayString(unref(t)("and")) + " ", 1),
                            createVNode("a", {
                              href: _ctx.route("policy.show"),
                              class: "underline hover:text-sky-300",
                              target: "_blank"
                            }, toDisplayString(unref(t)("privacyPolicy")), 9, ["href"])
                          ])
                        ]),
                        createVNode(_sfc_main$4, {
                          class: "mt-2",
                          message: unref(form).errors.terms
                        }, null, 8, ["message"])
                      ])) : createCommentVNode("", true),
                      createVNode("div", { class: "flex items-center justify-end mt-2" }, [
                        createVNode(_sfc_main$6, {
                          class: { "opacity-25": unref(form).processing },
                          disabled: unref(form).processing
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("register")), 1)
                          ]),
                          _: 1
                        }, 8, ["class", "disabled"])
                      ])
                    ], 32),
                    createVNode("div", { class: "pt-2 mt-2 border-t border-white/20 text-center" }, [
                      createVNode("p", { class: "text-sm text-white" }, [
                        createTextVNode(toDisplayString(unref(t)("alreadyRegistered")) + " ", 1),
                        createVNode(unref(Link), {
                          href: _ctx.route("login"),
                          class: "text-sky-200 hover:underline ml-1"
                        }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("login")), 1)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Auth/Register.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Register = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-56dc0223"]]);
export {
  Register as default
};
