import { mergeProps, withCtx, unref, createTextVNode, toDisplayString, useSSRContext, ref, createVNode, createBlock, createCommentVNode, openBlock, withModifiers, Fragment, renderList, computed, onMounted, onUnmounted, watch } from "vue";
import { ssrRenderAttrs, ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderList, ssrRenderClass, ssrRenderSlot, ssrRenderStyle } from "vue/server-renderer";
import { Link, router, usePage, Head } from "@inertiajs/vue3";
import { _ as _sfc_main$a, a as _sfc_main$b, b as _sfc_main$d } from "./LocaleSelectOption-BeLdazeX.js";
import { _ as _sfc_main$9, A as ApplicationMark } from "./ResponsiveNavLink-gtte0z5g.js";
import { _ as _sfc_main$c } from "./ThemeToggle-DA16u1ft.js";
import { useI18n } from "vue-i18n";
import draggable from "vuedraggable";
import { s as sidebarIcons, D as DigitalClock, _ as _sfc_main$e, S as ScrollButtons } from "./ScrollButtons-2xyFJfJ4.js";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { library } from "@fortawesome/fontawesome-svg-core";
import { fas } from "@fortawesome/free-solid-svg-icons";
import { Inertia } from "@inertiajs/inertia";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
import { a as authImage } from "./auth-image-CfsIGyOn.js";
const _sfc_main$8 = {
  __name: "ResponsiveNavLinks",
  __ssrInlineRender: true,
  setup(__props) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "pt-2 pb-3 space-y-1" }, _attrs))}>`);
      _push(ssrRenderComponent(_sfc_main$9, {
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
      _push(ssrRenderComponent(_sfc_main$9, {
        href: _ctx.route("profile.show"),
        active: _ctx.route().current("profile.show")
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`${ssrInterpolate(unref(t)("profile"))}`);
          } else {
            return [
              createTextVNode(toDisplayString(unref(t)("profile")), 1)
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`</div>`);
    };
  }
};
const _sfc_setup$8 = _sfc_main$8.setup;
_sfc_main$8.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/User/Links/ResponsiveNavLinks.vue");
  return _sfc_setup$8 ? _sfc_setup$8(props, ctx) : void 0;
};
const _sfc_main$7 = {
  __name: "TopPanel",
  __ssrInlineRender: true,
  setup(__props) {
    const isPanelOpen = ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(_attrs)}><button class="fixed top-0 right-3 z-50 hidden md:inline-block px-3 py-3 cursor-pointer"><svg class="w-6 h-6" viewBox="0 0 20 20"><circle fill="none" class="stroke-violet-500" cx="9.997" cy="10" r="3.31"></circle><path fill="none" class="stroke-violet-500" d="M18.488,12.285 L16.205,16.237 C15.322,15.496 14.185,15.281 13.303,15.791 C12.428,16.289 12.047,17.373 12.246,18.5 L7.735,18.5 C7.938,17.374 7.553,16.299 6.684,15.791 C5.801,15.27 4.655,15.492 3.773,16.237 L1.5,12.285 C2.573,11.871 3.317,10.999 3.317,9.991 C3.305,8.98 2.573,8.121 1.5,7.716 L3.765,3.784 C4.645,4.516 5.794,4.738 6.687,4.232 C7.555,3.722 7.939,2.637 7.735,1.5 L12.263,1.5 C12.072,2.637 12.441,3.71 13.314,4.22 C14.206,4.73 15.343,4.516 16.225,3.794 L18.487,7.714 C17.404,8.117 16.661,8.988 16.67,10.009 C16.672,11.018 17.415,11.88 18.488,12.285 L18.488,12.285 Z"></path></svg></button>`);
      if (isPanelOpen.value) {
        _push(`<div class="fixed top-0 left-0 right-0 border-b-2 border-slate-200 dark:border-slate-800 bg-slate-700 bg-opacity-90 dark:bg-opacity-90 shadow-md font-semibold text-center text-lg z-40 h-12 py-2 overflow-y-auto flex items-center justify-center space-x-4"></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$7 = _sfc_main$7.setup;
_sfc_main$7.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Partials/User/TopPanel.vue");
  return _sfc_setup$7 ? _sfc_setup$7(props, ctx) : void 0;
};
const _sfc_main$6 = {
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
      _push(ssrRenderComponent(_sfc_main$7, null, null, _parent));
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
      _push(ssrRenderComponent(_sfc_main$a, {
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
            _push2(ssrRenderComponent(_sfc_main$b, {
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
              _push2(ssrRenderComponent(_sfc_main$b, {
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
            _push2(ssrRenderComponent(_sfc_main$b, { as: "button" }, {
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
              createVNode(_sfc_main$b, {
                href: _ctx.route("profile.show")
              }, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("profile")), 1)
                ]),
                _: 1
              }, 8, ["href"]),
              _ctx.$page.props.jetstream.hasApiFeatures ? (openBlock(), createBlock(_sfc_main$b, {
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
                createVNode(_sfc_main$b, { as: "button" }, {
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
      _push(ssrRenderComponent(_sfc_main$c, { class: "relative z-10" }, null, _parent));
      _push(`</div><div class="hidden sm:block sm:me-8 relative">`);
      if (_ctx.$page.props.jetstream.hasTeamFeatures) {
        _push(ssrRenderComponent(_sfc_main$a, {
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
              _push2(ssrRenderComponent(_sfc_main$b, {
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
                _push2(ssrRenderComponent(_sfc_main$b, {
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
                  _push2(ssrRenderComponent(_sfc_main$b, { as: "button" }, {
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
                  createVNode(_sfc_main$b, {
                    href: _ctx.route("teams.show", _ctx.$page.props.auth.user.current_team)
                  }, {
                    default: withCtx(() => [
                      createTextVNode(toDisplayString(unref(t)("teamSettings")), 1)
                    ]),
                    _: 1
                  }, 8, ["href"]),
                  _ctx.$page.props.jetstream.canCreateTeams ? (openBlock(), createBlock(_sfc_main$b, {
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
                        createVNode(_sfc_main$b, { as: "button" }, {
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
      _push(ssrRenderComponent(_sfc_main$8, null, null, _parent));
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
const _sfc_setup$6 = _sfc_main$6.setup;
_sfc_main$6.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Partials/User/Header.vue");
  return _sfc_setup$6 ? _sfc_setup$6(props, ctx) : void 0;
};
const _sfc_main$5 = {
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
      return isDarkMode.value ? adminSettings.value.AdminSidebarDarkActiveText || "text-yellow-200" : adminSettings.value.AdminSidebarLightActiveText || "text-yellow-200";
    });
    const { t } = useI18n();
    const { props: pageProps } = usePage();
    const linkInfo = {
      dashboard: { label: t("dashboard"), route: "dashboard" },
      // Уже исправлено
      // Для API токенов и команд используем стандартные имена Jetstream/Fortify
      apiTokens: { label: t("apiTokens"), route: "api-tokens.index" },
      teamSettings: { label: t("teamSettings"), route: "teams.show", params: { team: pageProps.auth.user.current_team } },
      profile: { label: t("profile"), route: "profile.show" }
    };
    const link = computed(() => linkInfo[props.id]);
    const svgContent = computed(() => sidebarIcons[props.id]);
    const classes = computed(() => {
      if (link.value.route === route().current()) {
        return `flex items-center px-1 pt-1 text-sm font-medium leading-3 ${colorTextActive.value} hover:${colorTextHover.value} focus:${colorTextHover.value} focus:outline-none transition duration-150 ease-in-out`;
      } else {
        return `flex items-center px-1 pt-1 text-sm font-medium leading-3 ${colorText.value} hover:${colorTextActive.value} focus:${colorTextActive.value} focus:outline-none transition duration-150 ease-in-out`;
      }
    });
    const containerClasses = computed(() => {
      return props.expanded ? "mb-1" : "mb-3";
    });
    const textClasses = computed(() => {
      return props.expanded ? "ml-3 opacity-100" : "ml-3 opacity-0 whitespace-nowrap overflow-hidden";
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<li${ssrRenderAttrs(mergeProps({ class: containerClasses.value }, _attrs))}>`);
      _push(ssrRenderComponent(unref(Link), {
        href: _ctx.route(link.value.route, link.value.params || {}),
        class: classes.value
      }, {
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 24 24"${_scopeId}>${svgContent.value ?? ""}</svg><span class="${ssrRenderClass([textClasses.value, "text-sm font-medium transition-opacity duration-200 max-w-full"])}"${_scopeId}>${ssrInterpolate(link.value.label)}</span>`);
          } else {
            return [
              (openBlock(), createBlock("svg", {
                class: "shrink-0 h-4 w-4",
                viewBox: "0 0 24 24",
                innerHTML: svgContent.value
              }, null, 8, ["innerHTML"])),
              createVNode("span", {
                class: ["text-sm font-medium transition-opacity duration-200 max-w-full", textClasses.value]
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
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/User/Links/DraggableSidebarLink.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "DraggableSidebarGroupLink",
  __ssrInlineRender: true,
  props: {
    expanded: Boolean
  },
  emits: ["update:mainLinks", "update:hiddenLinks"],
  setup(__props, { emit: __emit }) {
    const page = usePage();
    const adminSettings = computed(() => {
      var _a;
      return ((_a = page.props) == null ? void 0 : _a.adminSettings) || {};
    });
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
    const colorTextActive = computed(() => {
      return isDarkMode.value ? adminSettings.value.AdminSidebarDarkActiveText || "text-yellow-200" : adminSettings.value.AdminSidebarLightActiveText || "text-yellow-200";
    });
    const emit = __emit;
    const { t } = useI18n();
    const mainLinks = ref(JSON.parse(localStorage.getItem("mainLinks")) || [
      "dashboard",
      "profile"
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
        tag: "ul"
      }, {
        item: withCtx(({ element }, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: element,
              expanded: __props.expanded
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode(_sfc_main$5, {
                id: element,
                expanded: __props.expanded
              }, null, 8, ["id", "expanded"])
            ];
          }
        }),
        _: 1
      }, _parent));
      _push(`<button class="flex justify-center items-center w-full pt-3 mb-0"><span class="${ssrRenderClass([[colorTextActive.value], "text-xs uppercase font-semibold"])}">${ssrInterpolate(unref(t)("more"))}</span></button>`);
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
              _push2(ssrRenderComponent(_sfc_main$5, {
                id: element,
                expanded: __props.expanded
              }, null, _parent2, _scopeId));
            } else {
              return [
                createVNode(_sfc_main$5, {
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
      _push(`<br><!--]-->`);
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/User/Links/DraggableSidebarGroupLink.vue");
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
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(_attrs)}><div class="${ssrRenderClass([__props.sidebarOpen ? "opacity-100" : "opacity-0 pointer-events-none", "fixed inset-0 z-20 dark:border-r dark:border-gray-600 bg-opacity-30 md:hidden md:z-auto transition-opacity duration-200"])}" aria-hidden="true"></div><div id="sidebar" class="${ssrRenderClass([[bgColorClass.value, { "translate-x-0": __props.sidebarOpen, "-translate-x-58": !__props.sidebarOpen, "hidden md:flex": true, "md:w-16": !sidebarExpanded.value, "md:!w-58 2xl:!w-58": sidebarExpanded.value }], "h-screen absolute z-40 w-58 left-0 top-0 pb-16 p-2 flex flex-col dark:border-r dark:border-gray-600 md:static md:left-auto md:top-auto md:translate-x-0 md:overflow-y-auto overflow-y-scroll no-scrollbar transition-all duration-200 ease-in-out"])}"><div class="flex justify-around items-center mb-2 pr-3 md:px-0"><button title="t(&#39;toggleSidebar&#39;)"><svg class="${ssrRenderClass([{ "rotate-180": sidebarExpanded.value }, "mx-1 w-6 h-6 py-1 fill-current transition-transform duration-200 border border-gray-400 hover:border-red-400"])}" viewBox="0 0 24 24"><path class="text-slate-400 hover:text-red-400" d="M19.586 11l-5-5L16 4.586 23.414 12 16 19.414 14.586 18l5-5H7v-2z"></path><path class="text-slate-600" d="M3 23H1V1h2z"></path></svg></button>`);
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
          class: "text-white"
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
        _push(`<span class="flex justify-start text-xs uppercase font-semibold pl-1 opacity-95 text-indigo-200 pt-1 border-t border-dotted border-gray-50">${ssrInterpolate(unref(t)("pages"))}</span>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Partials/User/Sidebar.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "Footer",
  __ssrInlineRender: true,
  setup(__props) {
    const { t, locale } = useI18n();
    const selectedLocale = ref(locale.value);
    watch(selectedLocale, (newLocale) => {
      if (newLocale !== locale.value) {
        locale.value = newLocale;
        const segments = window.location.pathname.split("/");
        segments[1] = newLocale;
        const newPath = segments.join("/") + window.location.search;
        Inertia.visit(newPath, { preserveState: false, preserveScroll: true });
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<footer${ssrRenderAttrs(mergeProps({ class: "sticky px-3 py-1 bottom-0 bg-gradient-to-b from-slate-100 to-slate-300 dark:bg-gradient-to-b dark:from-slate-700 dark:to-slate-900 border-t border-slate-200 dark:border-slate-700 z-20" }, _attrs))}><div class="flex items-center justify-center sm:justify-between flex-wrap"><div class="flex flex-row items-center justify-start gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400"><a href="https://t.me/k_a_v_www" target="_blank"${ssrRenderAttr("title", unref(t)("supportService"))} class="flex items-center space-x-2 text-blue-500 hover:text-blue-700 dark:text-blue-300 dark:hover:text-blue-500"><svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-5 h-5 sm:w-6 sm:h-6"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.175 8.89l-1.4 6.63c-.105.467-.405.578-.82.36l-2.27-1.67-1.093 1.054c-.12.12-.222.222-.45.222l.168-2.39 4.35-3.923c.19-.168-.04-.263-.29-.095L8.78 11.167l-2.42-.76c-.464-.14-.474-.464.096-.684l9.452-3.65c.44-.16.82.108.66.717z"></path></svg></a> © ${ssrInterpolate((/* @__PURE__ */ new Date()).getFullYear())} <a href="/admin" target="_blank" class="font-semibold text-red-400 hover:text-rose-300"> Pulsar CMS </a></div><div class="space-x-2"></div><div class="flex flex-row items-center justify-end gap-2"><button type="button"${ssrRenderAttr("title", unref(t)("clearCache"))} class="flex items-center btn px-1 py-0.5 text-slate-900 dark:text-slate-100 rounded-sm border-2 border-slate-400"><svg class="w-4 h-4 fill-current text-red-400 shrink-0" viewBox="0 0 16 16"><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2.8-6.4z"></path></svg></button>`);
      _push(ssrRenderComponent(_sfc_main$d, {
        modelValue: selectedLocale.value,
        "onUpdate:modelValue": ($event) => selectedLocale.value = $event
      }, null, _parent));
      _push(`</div></div></footer>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Partials/User/Footer.vue");
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
      _push(`<!--[--><div class="row-span-full" data-v-a9fa62cf><div id="widgetPanel" class="${ssrRenderClass([[bgColorClass.value], "flex-col items-center h-full w-4 z-50 dark:border-l dark:border-gray-600 overflow-y-scroll hidden md:flex md:z-50 no-scrollbar transition-all duration-200 ease-in-out"])}" data-v-a9fa62cf><a href="/" target="_blank" class="mt-16 ml-1"${ssrRenderAttr("title", unref(t)("website"))} data-v-a9fa62cf><svg class="${ssrRenderClass(iconClass.value)}" viewBox="0 0 16 16" data-v-a9fa62cf><path class="${ssrRenderClass(["fill-current", iconPathClass("site")])}" d="M10 16h4c.6 0 1-.4 1-.998V6.016c0-.3-.1-.6-.4-.8L8.6.226c-.4-.3-.9-.3-1.3 0l-6 4.992c-.2.2-.3.5-.3.799v8.986C1 15.6 1.4 16 2 16h4c.6 0 1-.4 1-.998v-2.996h2v2.996c0 .599.4.998 1 .998Zm-4-5.99c-.6 0-1 .399-1 .998v2.995H3V6.515L8 2.32l5 4.194v7.488h-2v-2.995c0-.6-.4-.999-1-.999H6Z" data-v-a9fa62cf></path></svg></a><a href="/dashboard" target="_blank" class="mt-3 ml-1"${ssrRenderAttr("title", unref(t)("dashboard"))} data-v-a9fa62cf><svg class="${ssrRenderClass(iconClass.value)}" viewBox="0 0 16 16" data-v-a9fa62cf><path class="${ssrRenderClass(["fill-current", iconPathClass("dashboard")])}" d="M12.311 9.527c-1.161-.393-1.85-.825-2.143-1.175A3.991 3.991 0 0012 5V4c0-2.206-1.794-4-4-4S4 1.794 4 4v1c0 1.406.732 2.639 1.832 3.352-.292.35-.981.782-2.142 1.175A3.942 3.942 0 001 13.26V16h14v-2.74c0-1.69-1.081-3.19-2.689-3.733zM6 4c0-1.103.897-2 2-2s2 .897 2 2v1c0 1.103-.897 2-2 2s-2-.897-2-2V4zm7 10H3v-.74c0-.831.534-1.569 1.33-1.838 1.845-.624 3-1.436 3.452-2.422h.436c.452.986 1.607 1.798 3.453 2.422A1.943 1.943 0 0113 13.26V14z" data-v-a9fa62cf></path></svg></a><button type="button" class="mt-3 ml-1"${ssrRenderAttr("title", unref(t)("translator"))} data-v-a9fa62cf><svg class="${ssrRenderClass(iconClass.value)}" viewBox="0 0 24 24" data-v-a9fa62cf><path class="${ssrRenderClass(["fill-current", iconPathClass("translator")])}" d="M12.87 15.07l-2.54-2.51.03-.03A17.52 17.52 0 0014.07 6H17V4h-7V2H8v2H1v2h11.17A15.7 15.7 0 019 11.17 15.16 15.16 0 016.91 8H4.91a17.39 17.39 0 002.77 4.36l-5.09 5.02L4 18.8l5-5 3.11 3.11.76-1.84zM18.5 10h-2L12 22h2l1.12-3h4.75L21 22h2l-4.5-12zm-2.62 7l1.62-4.33L19.12 17h-3.24z" data-v-a9fa62cf></path></svg></button></div></div>`);
      _push(ssrRenderComponent(_sfc_main$e, {
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Partials/User/WidgetPanel.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const WidgetPanel = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-a9fa62cf"]]);
const _sfc_main = {
  __name: "AppLayout",
  __ssrInlineRender: true,
  props: {
    title: String
  },
  setup(__props) {
    const sidebarOpen = ref(false);
    const showingNavigationDropdown = ref(false);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<!--[-->`);
      _push(ssrRenderComponent(unref(Head), { title: __props.title }, null, _parent));
      _push(`<div class="flex flex-row h-screen overflow-hidden" data-v-ad3fdb73>`);
      _push(ssrRenderComponent(_sfc_main$3, {
        "sidebar-open": sidebarOpen.value,
        onCloseSidebar: ($event) => sidebarOpen.value = false
      }, null, _parent));
      _push(`<div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden" data-v-ad3fdb73>`);
      _push(ssrRenderComponent(_sfc_main$6, {
        "showing-navigation-dropdown": showingNavigationDropdown.value,
        onToggleNavigationDropdown: ($event) => showingNavigationDropdown.value = !showingNavigationDropdown.value
      }, null, _parent));
      if (_ctx.$slots.header) {
        _push(`<header class="dark:bg-slate-700 bg-slate-50 shadow" data-v-ad3fdb73><div class="max-w-7xl mx-auto py-2 px-1 sm:px-6 lg:px-8" data-v-ad3fdb73>`);
        ssrRenderSlot(_ctx.$slots, "header", {}, null, _push, _parent);
        _push(`</div></header>`);
      } else {
        _push(`<!---->`);
      }
      _push(`<main class="flex-grow bg-center" style="${ssrRenderStyle({ backgroundImage: `url(${unref(authImage)})`, backgroundAttachment: "fixed" })}" data-v-ad3fdb73>`);
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Layouts/AppLayout.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const AppLayout = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-ad3fdb73"]]);
export {
  AppLayout as A
};
