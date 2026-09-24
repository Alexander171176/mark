import { mergeProps, withCtx, unref, createTextVNode, toDisplayString, createVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderClass } from "vue/server-renderer";
import { Link } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import "vue-toastification";
import "./LocaleSelectOption-BeLdazeX.js";
import "./ResponsiveNavLink-gtte0z5g.js";
import "./_plugin-vue_export-helper-1tPrXgE0.js";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "vuedraggable";
import "./ScrollButtons-2xyFJfJ4.js";
import "@fortawesome/vue-fontawesome";
import "@fortawesome/fontawesome-svg-core";
import "@fortawesome/free-solid-svg-icons";
import "@inertiajs/inertia";
import "vue-smooth-dnd";
const cardClass = "p-3 bg-white dark:bg-slate-800 border border-slate-400 rounded";
const titleClass = "mb-2 font-semibold text-gray-800 dark:text-gray-100";
const labelClass = "text-indigo-700 dark:text-indigo-300";
const _sfc_main = {
  __name: "Show",
  __ssrInlineRender: true,
  props: {
    visitorLog: {
      type: Object,
      default: () => ({})
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const log = props.visitorLog.data ?? props.visitorLog;
    const formatDate = (value) => {
      if (!value) {
        return "—";
      }
      return new Date(value).toLocaleString();
    };
    const valueOrDash = (value) => {
      return value === null || value === void 0 || value === "" ? "—" : value;
    };
    const valueWithUnit = (value, unit) => {
      if (value === null || value === void 0 || value === "") {
        return "—";
      }
      return `${value}${unit}`;
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({ title: "Analytics Visitor Log" }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("analyticsLogs"))} #${ssrInterpolate(unref(log).id)}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("analyticsLogs")) + " #" + toDisplayString(unref(log).id), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("analyticsLogs")) + " #" + toDisplayString(unref(log).id), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-7xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="mb-3"${_scopeId}>`);
            _push2(ssrRenderComponent(unref(Link), {
              href: _ctx.route("admin.analyticsVisitorLogs.index"),
              class: "inline-block px-2 py-0.5 text-sm font-semibold text-white bg-blue-600 rounded hover:bg-blue-700"
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ← ${ssrInterpolate(unref(t)("back"))}`);
                } else {
                  return [
                    createTextVNode(" ← " + toDisplayString(unref(t)("back")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div><div class="grid grid-cols-1 lg:grid-cols-2 gap-2 text-sm"${_scopeId}><div class="${ssrRenderClass(cardClass)}"${_scopeId}><h2 class="${ssrRenderClass(titleClass)}"${_scopeId}>${ssrInterpolate(unref(t)("user"))}</h2><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}>${ssrInterpolate(unref(t)("user"))} ID: </b> ${ssrInterpolate(valueOrDash(unref(log).user_id))}</p><p class="break-all"${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}>${ssrInterpolate(unref(t)("session"))} ID: </b> ${ssrInterpolate(valueOrDash(unref(log).session_id))}</p><p class="break-all"${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Visitor UUID: </b> ${ssrInterpolate(valueOrDash(unref(log).visitor_uuid))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}>${ssrInterpolate(unref(t)("gender"))}: </b> ${ssrInterpolate(valueOrDash(unref(log).user_gender))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}>${ssrInterpolate(unref(t)("age"))}: </b> ${ssrInterpolate(valueOrDash(unref(log).user_age))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}>${ssrInterpolate(unref(t)("ageGroup"))}: </b> ${ssrInterpolate(valueOrDash(unref(log).user_age_group))}</p></div><div class="${ssrRenderClass(cardClass)}"${_scopeId}><h2 class="${ssrRenderClass(titleClass)}"${_scopeId}>${ssrInterpolate(unref(t)("page"))}</h2><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}>${ssrInterpolate(unref(t)("date"))}: </b> ${ssrInterpolate(formatDate(unref(log).visited_at))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}>${ssrInterpolate(unref(t)("method"))}: </b> ${ssrInterpolate(valueOrDash(unref(log).method))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}>${ssrInterpolate(unref(t)("status"))}: </b> ${ssrInterpolate(valueOrDash(unref(log).status_code))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}>${ssrInterpolate(unref(t)("title"))}: </b> ${ssrInterpolate(valueOrDash(unref(log).page_title))}</p><p class="break-all"${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> URL: </b> ${ssrInterpolate(valueOrDash(unref(log).url))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Route: </b> ${ssrInterpolate(valueOrDash(unref(log).route_name))}</p></div><div class="${ssrRenderClass(cardClass)}"${_scopeId}><h2 class="${ssrRenderClass(titleClass)}"${_scopeId}>${ssrInterpolate(unref(t)("context"))}</h2><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Module: </b> ${ssrInterpolate(valueOrDash(unref(log).module))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Entity type: </b> ${ssrInterpolate(valueOrDash(unref(log).entity_type))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Entity ID: </b> ${ssrInterpolate(valueOrDash(unref(log).entity_id))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Event type: </b> ${ssrInterpolate(valueOrDash(unref(log).event_type))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Request type: </b> ${ssrInterpolate(valueOrDash(unref(log).request_type))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Response time: </b> ${ssrInterpolate(valueWithUnit(unref(log).response_time, " ms"))}</p></div><div class="${ssrRenderClass(cardClass)}"${_scopeId}><h2 class="${ssrRenderClass(titleClass)}"${_scopeId}>${ssrInterpolate(unref(t)("geography"))}</h2><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> IP: </b> ${ssrInterpolate(valueOrDash(unref(log).ip_address))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}>${ssrInterpolate(unref(t)("country"))}: </b> ${ssrInterpolate(valueOrDash(unref(log).country))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}>${ssrInterpolate(unref(t)("region"))}: </b> ${ssrInterpolate(valueOrDash(unref(log).region))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}>${ssrInterpolate(unref(t)("city"))}: </b> ${ssrInterpolate(valueOrDash(unref(log).city))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Locale: </b> ${ssrInterpolate(valueOrDash(unref(log).locale))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Timezone: </b> ${ssrInterpolate(valueOrDash(unref(log).timezone))}</p></div><div class="${ssrRenderClass(cardClass)}"${_scopeId}><h2 class="${ssrRenderClass(titleClass)}"${_scopeId}>${ssrInterpolate(unref(t)("device"))}</h2><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Device type: </b> ${ssrInterpolate(valueOrDash(unref(log).device_type))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Device name: </b> ${ssrInterpolate(valueOrDash(unref(log).device_name))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Browser: </b> ${ssrInterpolate(valueOrDash(unref(log).browser))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Browser version: </b> ${ssrInterpolate(valueOrDash(unref(log).browser_version))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> OS: </b> ${ssrInterpolate(valueOrDash(unref(log).os))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> OS version: </b> ${ssrInterpolate(valueOrDash(unref(log).os_version))}</p></div><div class="${ssrRenderClass(cardClass)}"${_scopeId}><h2 class="${ssrRenderClass(titleClass)}"${_scopeId}> Frontend ${ssrInterpolate(unref(t)("data"))}</h2><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Screen ${ssrInterpolate(unref(t)("width"))}: </b> ${ssrInterpolate(valueWithUnit(unref(log).screen_width, " px"))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Screen ${ssrInterpolate(unref(t)("height"))}: </b> ${ssrInterpolate(valueWithUnit(unref(log).screen_height, " px"))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Browser language: </b> ${ssrInterpolate(valueOrDash(unref(log).browser_language))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Time on page: </b> ${ssrInterpolate(valueWithUnit(unref(log).time_on_page, " сек."))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Scroll depth: </b> ${ssrInterpolate(valueWithUnit(unref(log).scroll_depth, "%"))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Clicks: </b> ${ssrInterpolate(valueOrDash(unref(log).clicks_count))}</p></div><div class="${ssrRenderClass([
              cardClass,
              "lg:col-span-2"
            ])}"${_scopeId}><h2 class="${ssrRenderClass(titleClass)}"${_scopeId}>${ssrInterpolate(unref(t)("source"))}</h2><p class="break-all"${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Referer: </b> ${ssrInterpolate(valueOrDash(unref(log).referer))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Source type: </b> ${ssrInterpolate(valueOrDash(unref(log).source_type))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Search engine: </b> ${ssrInterpolate(valueOrDash(unref(log).search_engine))}</p><p class="break-all"${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> User-Agent: </b> ${ssrInterpolate(valueOrDash(unref(log).user_agent))}</p></div><div class="${ssrRenderClass([
              cardClass,
              "lg:col-span-2"
            ])}"${_scopeId}><h2 class="${ssrRenderClass(titleClass)}"${_scopeId}>${ssrInterpolate(unref(t)("data"))}</h2><div class="grid grid-cols-1 md:grid-cols-3 gap-1"${_scopeId}><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> ID: </b> ${ssrInterpolate(valueOrDash(unref(log).id))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Created: </b> ${ssrInterpolate(formatDate(unref(log).created_at))}</p><p${_scopeId}><b class="${ssrRenderClass(labelClass)}"${_scopeId}> Updated: </b> ${ssrInterpolate(formatDate(unref(log).updated_at))}</p></div></div></div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-7xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "mb-3" }, [
                    createVNode(unref(Link), {
                      href: _ctx.route("admin.analyticsVisitorLogs.index"),
                      class: "inline-block px-2 py-0.5 text-sm font-semibold text-white bg-blue-600 rounded hover:bg-blue-700"
                    }, {
                      default: withCtx(() => [
                        createTextVNode(" ← " + toDisplayString(unref(t)("back")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ]),
                  createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-2 text-sm" }, [
                    createVNode("div", { class: cardClass }, [
                      createVNode("h2", { class: titleClass }, toDisplayString(unref(t)("user")), 1),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, toDisplayString(unref(t)("user")) + " ID: ", 1),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).user_id)), 1)
                      ]),
                      createVNode("p", { class: "break-all" }, [
                        createVNode("b", { class: labelClass }, toDisplayString(unref(t)("session")) + " ID: ", 1),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).session_id)), 1)
                      ]),
                      createVNode("p", { class: "break-all" }, [
                        createVNode("b", { class: labelClass }, " Visitor UUID: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).visitor_uuid)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, toDisplayString(unref(t)("gender")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).user_gender)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, toDisplayString(unref(t)("age")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).user_age)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, toDisplayString(unref(t)("ageGroup")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).user_age_group)), 1)
                      ])
                    ]),
                    createVNode("div", { class: cardClass }, [
                      createVNode("h2", { class: titleClass }, toDisplayString(unref(t)("page")), 1),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, toDisplayString(unref(t)("date")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(formatDate(unref(log).visited_at)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, toDisplayString(unref(t)("method")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).method)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, toDisplayString(unref(t)("status")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).status_code)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, toDisplayString(unref(t)("title")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).page_title)), 1)
                      ]),
                      createVNode("p", { class: "break-all" }, [
                        createVNode("b", { class: labelClass }, " URL: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).url)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Route: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).route_name)), 1)
                      ])
                    ]),
                    createVNode("div", { class: cardClass }, [
                      createVNode("h2", { class: titleClass }, toDisplayString(unref(t)("context")), 1),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Module: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).module)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Entity type: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).entity_type)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Entity ID: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).entity_id)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Event type: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).event_type)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Request type: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).request_type)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Response time: "),
                        createTextVNode(" " + toDisplayString(valueWithUnit(unref(log).response_time, " ms")), 1)
                      ])
                    ]),
                    createVNode("div", { class: cardClass }, [
                      createVNode("h2", { class: titleClass }, toDisplayString(unref(t)("geography")), 1),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " IP: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).ip_address)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, toDisplayString(unref(t)("country")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).country)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, toDisplayString(unref(t)("region")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).region)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, toDisplayString(unref(t)("city")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).city)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Locale: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).locale)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Timezone: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).timezone)), 1)
                      ])
                    ]),
                    createVNode("div", { class: cardClass }, [
                      createVNode("h2", { class: titleClass }, toDisplayString(unref(t)("device")), 1),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Device type: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).device_type)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Device name: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).device_name)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Browser: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).browser)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Browser version: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).browser_version)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " OS: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).os)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " OS version: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).os_version)), 1)
                      ])
                    ]),
                    createVNode("div", { class: cardClass }, [
                      createVNode("h2", { class: titleClass }, " Frontend " + toDisplayString(unref(t)("data")), 1),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Screen " + toDisplayString(unref(t)("width")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(valueWithUnit(unref(log).screen_width, " px")), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Screen " + toDisplayString(unref(t)("height")) + ": ", 1),
                        createTextVNode(" " + toDisplayString(valueWithUnit(unref(log).screen_height, " px")), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Browser language: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).browser_language)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Time on page: "),
                        createTextVNode(" " + toDisplayString(valueWithUnit(unref(log).time_on_page, " сек.")), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Scroll depth: "),
                        createTextVNode(" " + toDisplayString(valueWithUnit(unref(log).scroll_depth, "%")), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Clicks: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).clicks_count)), 1)
                      ])
                    ]),
                    createVNode("div", {
                      class: [
                        cardClass,
                        "lg:col-span-2"
                      ]
                    }, [
                      createVNode("h2", { class: titleClass }, toDisplayString(unref(t)("source")), 1),
                      createVNode("p", { class: "break-all" }, [
                        createVNode("b", { class: labelClass }, " Referer: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).referer)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Source type: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).source_type)), 1)
                      ]),
                      createVNode("p", null, [
                        createVNode("b", { class: labelClass }, " Search engine: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).search_engine)), 1)
                      ]),
                      createVNode("p", { class: "break-all" }, [
                        createVNode("b", { class: labelClass }, " User-Agent: "),
                        createTextVNode(" " + toDisplayString(valueOrDash(unref(log).user_agent)), 1)
                      ])
                    ], 2),
                    createVNode("div", {
                      class: [
                        cardClass,
                        "lg:col-span-2"
                      ]
                    }, [
                      createVNode("h2", { class: titleClass }, toDisplayString(unref(t)("data")), 1),
                      createVNode("div", { class: "grid grid-cols-1 md:grid-cols-3 gap-1" }, [
                        createVNode("p", null, [
                          createVNode("b", { class: labelClass }, " ID: "),
                          createTextVNode(" " + toDisplayString(valueOrDash(unref(log).id)), 1)
                        ]),
                        createVNode("p", null, [
                          createVNode("b", { class: labelClass }, " Created: "),
                          createTextVNode(" " + toDisplayString(formatDate(unref(log).created_at)), 1)
                        ]),
                        createVNode("p", null, [
                          createVNode("b", { class: labelClass }, " Updated: "),
                          createTextVNode(" " + toDisplayString(formatDate(unref(log).updated_at)), 1)
                        ])
                      ])
                    ], 2)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Analytics/AnalyticsVisitorLog/Show.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
