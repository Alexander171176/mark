import { ref, onMounted, onUnmounted, watch, mergeProps, useSSRContext, computed, unref, withCtx, createVNode, createBlock, withDirectives, openBlock, Fragment, renderList, toDisplayString, vModelSelect, createCommentVNode, createTextVNode } from "vue";
import { ssrRenderAttrs, ssrRenderAttr, ssrRenderComponent, ssrRenderClass, ssrRenderList, ssrInterpolate, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { Chart, Tooltip, BarController, BarElement, LinearScale, CategoryScale, Legend, LineController, LineElement, Filler, PointElement } from "chart.js";
import resolveConfig from "tailwindcss/resolveConfig.js";
import defaultTheme from "tailwindcss/defaultTheme.js";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
import "vue-toastification";
import "./LocaleSelectOption-BeLdazeX.js";
import "./ResponsiveNavLink-gtte0z5g.js";
import "./ThemeToggle-DA16u1ft.js";
import "@vueuse/core";
import "vuedraggable";
import "./ScrollButtons-2xyFJfJ4.js";
import "@fortawesome/vue-fontawesome";
import "@fortawesome/fontawesome-svg-core";
import "@fortawesome/free-solid-svg-icons";
import "@inertiajs/inertia";
import "vue-smooth-dnd";
const tailwindConfigFile = {
  content: [
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./vendor/laravel/jetstream/**/*.blade.php",
    "./storage/framework/views/*.php",
    "./resources/views/**/*.blade.php",
    "./resources/js/**/*.vue"
  ],
  darkMode: "class",
  theme: {
    extend: {
      fontFamily: {
        sans: ["Figtree", ...defaultTheme.fontFamily.sans],
        inter: ["Inter", "sans-serif"]
      },
      boxShadow: {
        DEFAULT: "0 1px 3px 0 rgba(0, 0, 0, 0.08), 0 1px 2px 0 rgba(0, 0, 0, 0.02)",
        md: "0 4px 6px -1px rgba(0, 0, 0, 0.08), 0 2px 4px -1px rgba(0, 0, 0, 0.02)",
        lg: "0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.01)",
        xl: "0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.01)"
      },
      outline: {
        blue: "2px solid rgba(0, 112, 244, 0.5)"
      },
      fontSize: {
        xs: ["0.75rem", { lineHeight: "1.5" }],
        sm: ["0.875rem", { lineHeight: "1.5715" }],
        base: ["1rem", { lineHeight: "1.5", letterSpacing: "-0.01em" }],
        lg: ["1.125rem", { lineHeight: "1.5", letterSpacing: "-0.01em" }],
        xl: ["1.25rem", { lineHeight: "1.5", letterSpacing: "-0.01em" }],
        "2xl": ["1.5rem", { lineHeight: "1.33", letterSpacing: "-0.01em" }],
        "3xl": ["1.88rem", { lineHeight: "1.33", letterSpacing: "-0.01em" }],
        "4xl": ["2.25rem", { lineHeight: "1.25", letterSpacing: "-0.02em" }],
        "5xl": ["3rem", { lineHeight: "1.25", letterSpacing: "-0.02em" }],
        "6xl": ["3.75rem", { lineHeight: "1.2", letterSpacing: "-0.02em" }]
      },
      screens: {
        xs: "480px"
      },
      borderWidth: {
        3: "3px"
      },
      minWidth: {
        36: "9rem",
        44: "11rem",
        56: "14rem",
        60: "15rem",
        72: "18rem",
        80: "20rem"
      },
      maxWidth: {
        "8xl": "88rem",
        "9xl": "96rem"
      },
      zIndex: {
        60: "60"
      }
    }
  },
  plugins: [forms, typography]
};
const tailwindConfig = () => {
  return resolveConfig(tailwindConfigFile);
};
Chart.register(Tooltip);
const getLabelColor = () => {
  return document.documentElement.classList.contains("dark") ? tailwindConfig().theme.colors.slate[100] : tailwindConfig().theme.colors.slate[400];
};
const updateChartColors = () => {
  Chart.defaults.color = getLabelColor();
  Chart.defaults.scale.grid.color = document.documentElement.classList.contains("dark") ? tailwindConfig().theme.colors.slate[700] : tailwindConfig().theme.colors.slate[100];
};
updateChartColors();
const observer = new MutationObserver(() => {
  updateChartColors();
  Object.values(Chart.instances).forEach((instance) => {
    if (instance.options.scales) {
      if (instance.options.scales.x && instance.options.scales.x.ticks) {
        instance.options.scales.x.ticks.color = getLabelColor();
      }
      if (instance.options.scales.y && instance.options.scales.y.ticks) {
        instance.options.scales.y.ticks.color = getLabelColor();
      }
    }
    instance.update();
  });
});
observer.observe(document.documentElement, { attributes: true, attributeFilter: ["class"] });
Chart.defaults.font.family = '"Inter", sans-serif';
Chart.defaults.font.weight = "500";
Chart.defaults.plugins.tooltip.titleColor = tailwindConfig().theme.colors.slate[800];
Chart.defaults.plugins.tooltip.bodyColor = tailwindConfig().theme.colors.slate[800];
Chart.defaults.plugins.tooltip.backgroundColor = tailwindConfig().theme.colors.white;
Chart.defaults.plugins.tooltip.borderWidth = 1;
Chart.defaults.plugins.tooltip.borderColor = tailwindConfig().theme.colors.slate[200];
Chart.defaults.plugins.tooltip.displayColors = false;
Chart.defaults.plugins.tooltip.mode = "nearest";
Chart.defaults.plugins.tooltip.intersect = false;
Chart.defaults.plugins.tooltip.position = "nearest";
Chart.defaults.plugins.tooltip.caretSize = 0;
Chart.defaults.plugins.tooltip.caretPadding = 20;
Chart.defaults.plugins.tooltip.cornerRadius = 4;
Chart.defaults.plugins.tooltip.padding = 8;
Chart.register({
  id: "chartAreaPlugin",
  beforeDraw: (chart) => {
    if (chart.config.options.chartArea && chart.config.options.chartArea.backgroundColor) {
      const ctx = chart.canvas.getContext("2d");
      const { chartArea } = chart;
      if (chartArea) {
        ctx.save();
        ctx.fillStyle = chart.config.options.chartArea.backgroundColor;
        ctx.fillRect(chartArea.left, chartArea.top, chartArea.right - chartArea.left, chartArea.bottom - chartArea.top);
        ctx.restore();
      }
    }
  }
});
const _sfc_main$2 = {
  __name: "EntityBarChart001",
  __ssrInlineRender: true,
  props: {
    items: {
      type: Array,
      default: () => []
    },
    title: {
      type: String,
      default: ""
    },
    width: {
      type: [Number, String],
      default: 600
    },
    height: {
      type: [Number, String],
      default: 400
    }
  },
  setup(__props) {
    Chart.register(
      BarController,
      BarElement,
      LinearScale,
      CategoryScale,
      Tooltip,
      Legend
    );
    const { t, locale } = useI18n();
    const props = __props;
    const canvas = ref(null);
    let chart = null;
    const translatedLabel = (item) => {
      if (item.translate) {
        return t(item.label);
      }
      return item.label;
    };
    const itemLabel = (item) => {
      const label = translatedLabel(item);
      if (item.id !== null && item.id !== void 0) {
        return `ID ${item.id}: ${label}`;
      }
      return label;
    };
    const chartData = () => {
      return {
        labels: props.items.map(
          (item) => itemLabel(item)
        ),
        datasets: [
          {
            label: props.title,
            data: props.items.map(
              (item) => Number(item.value) || 0
            ),
            backgroundColor: tailwindConfig().theme.colors.blue[400],
            borderRadius: 6,
            barThickness: 28
          }
        ]
      };
    };
    const destroyChart = () => {
      if (!chart) {
        return;
      }
      chart.destroy();
      chart = null;
    };
    const createChart = () => {
      destroyChart();
      if (!canvas.value || !props.items.length) {
        return;
      }
      chart = new Chart(
        canvas.value,
        {
          type: "bar",
          data: chartData(),
          options: {
            responsive: true,
            maintainAspectRatio: false,
            resizeDelay: 200,
            layout: {
              padding: 20
            },
            scales: {
              y: {
                beginAtZero: true,
                ticks: {
                  stepSize: 1
                },
                title: {
                  display: true,
                  text: t("value")
                }
              },
              x: {
                title: {
                  display: true,
                  text: t("entities")
                }
              }
            },
            plugins: {
              legend: {
                display: false
              },
              tooltip: {
                callbacks: {
                  title: (context) => {
                    var _a;
                    return ((_a = context[0]) == null ? void 0 : _a.label) || "";
                  },
                  label: (context) => `${t("value")}: ${context.raw}`
                }
              }
            }
          }
        }
      );
    };
    onMounted(
      createChart
    );
    onUnmounted(
      destroyChart
    );
    watch(
      [
        () => props.items,
        () => props.title,
        locale
      ],
      createChart,
      {
        deep: true
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "grow" }, _attrs))} data-v-45a0460e><canvas${ssrRenderAttr("width", __props.width)}${ssrRenderAttr("height", __props.height)} data-v-45a0460e></canvas></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Statistic/Chart/Entity/EntityBarChart001.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const EntityBarChart = /* @__PURE__ */ _export_sfc(_sfc_main$2, [["__scopeId", "data-v-45a0460e"]]);
const _sfc_main$1 = {
  __name: "EntityLineChart001",
  __ssrInlineRender: true,
  props: {
    items: {
      type: Array,
      default: () => []
    },
    title: {
      type: String,
      default: ""
    },
    width: {
      type: [Number, String],
      default: 600
    },
    height: {
      type: [Number, String],
      default: 400
    }
  },
  setup(__props) {
    Chart.register(
      LineController,
      LineElement,
      Filler,
      PointElement,
      LinearScale,
      CategoryScale,
      Tooltip,
      Legend
    );
    const { t, locale } = useI18n();
    const props = __props;
    const canvas = ref(null);
    let chart = null;
    const translatedLabel = (item) => {
      if (item.translate) {
        return t(item.label);
      }
      return item.label;
    };
    const itemLabel = (item) => {
      const label = translatedLabel(item);
      if (item.id !== null && item.id !== void 0) {
        return `ID ${item.id}: ${label}`;
      }
      return label;
    };
    const sortedItems = () => {
      return [...props.items].sort(
        (a, b) => {
          if (a.id !== null && a.id !== void 0 && b.id !== null && b.id !== void 0) {
            return a.id - b.id;
          }
          return String(
            translatedLabel(a)
          ).localeCompare(
            String(
              translatedLabel(b)
            ),
            locale.value
          );
        }
      );
    };
    const chartData = () => {
      const items = sortedItems();
      return {
        labels: items.map(
          (item) => itemLabel(item)
        ),
        datasets: [
          {
            label: props.title,
            data: items.map(
              (item) => Number(item.value) || 0
            ),
            fill: false,
            borderColor: tailwindConfig().theme.colors.blue[500],
            backgroundColor: tailwindConfig().theme.colors.blue[100],
            tension: 0.4,
            pointRadius: 4,
            pointBackgroundColor: tailwindConfig().theme.colors.blue[500]
          }
        ]
      };
    };
    const destroyChart = () => {
      if (!chart) {
        return;
      }
      chart.destroy();
      chart = null;
    };
    const createChart = () => {
      destroyChart();
      if (!canvas.value || !props.items.length) {
        return;
      }
      chart = new Chart(
        canvas.value,
        {
          type: "line",
          data: chartData(),
          options: {
            responsive: true,
            maintainAspectRatio: false,
            layout: {
              padding: 20
            },
            scales: {
              y: {
                beginAtZero: true,
                title: {
                  display: true,
                  text: t("value")
                }
              },
              x: {
                title: {
                  display: true,
                  text: t("entities")
                }
              }
            },
            plugins: {
              legend: {
                display: true
              },
              tooltip: {
                callbacks: {
                  title: (context) => {
                    var _a;
                    return ((_a = context[0]) == null ? void 0 : _a.label) || "";
                  },
                  label: (context) => `${context.dataset.label}: ${context.parsed.y}`
                }
              }
            }
          }
        }
      );
    };
    onMounted(
      createChart
    );
    onUnmounted(
      destroyChart
    );
    watch(
      [
        () => props.items,
        () => props.title,
        locale
      ],
      createChart,
      {
        deep: true
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "grow" }, _attrs))} data-v-63bd8138><canvas${ssrRenderAttr("width", __props.width)}${ssrRenderAttr("height", __props.height)} data-v-63bd8138></canvas></div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/Statistic/Chart/Entity/EntityLineChart001.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const EntityLineChart = /* @__PURE__ */ _export_sfc(_sfc_main$1, [["__scopeId", "data-v-63bd8138"]]);
const selectClass = "w-56 px-3 py-1 border border-slate-500 font-semibold text-sm rounded-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-300 dark:bg-cyan-800 dark:text-slate-100";
const paginationButtonClass = "px-3 py-1 border border-slate-400 font-semibold text-sm rounded-sm shadow-sm bg-white text-slate-700 hover:bg-slate-100 disabled:opacity-40 disabled:cursor-not-allowed dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700";
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    entities: {
      type: Array,
      default: () => []
    },
    metrics: {
      type: Array,
      default: () => []
    },
    filters: {
      type: Object,
      default: () => ({})
    },
    chart: {
      type: Object,
      default: () => ({
        entity_label_key: "",
        metric_label_key: "",
        entity: "",
        metric: "",
        data: [],
        pagination: null
      })
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const selectedEntity = ref(
      props.filters.entity || ""
    );
    const selectedMetric = ref(
      props.filters.metric || ""
    );
    const chartTitle = computed(() => {
      const entity = props.chart.entity_label_key ? t(props.chart.entity_label_key) : "";
      const metric = props.chart.metric_label_key ? t(props.chart.metric_label_key) : "";
      if (!entity) {
        return metric;
      }
      if (!metric) {
        return entity;
      }
      return `${entity} — ${metric}`;
    });
    const pagination = computed(() => {
      return props.chart.pagination || null;
    });
    const changePage = (page) => {
      if (!pagination.value || page < 1 || page > pagination.value.last_page || page === pagination.value.current_page) {
        return;
      }
      router.get(
        route("admin.charts.index"),
        {
          entity: selectedEntity.value,
          metric: selectedMetric.value,
          page
        },
        {
          preserveScroll: true,
          preserveState: false
        }
      );
    };
    const changeEntity = () => {
      router.get(
        route("admin.charts.index"),
        {
          entity: selectedEntity.value
        },
        {
          preserveScroll: true,
          preserveState: false
        }
      );
    };
    const changeMetric = () => {
      router.get(
        route("admin.charts.index"),
        {
          entity: selectedEntity.value,
          metric: selectedMetric.value
        },
        {
          preserveScroll: true,
          preserveState: false
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("charts")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("charts"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("charts")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("charts")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="flex flex-wrap gap-2 items-center justify-between mb-3"${_scopeId}><select class="${ssrRenderClass(selectClass)}"${_scopeId}><!--[-->`);
            ssrRenderList(props.entities, (entity) => {
              _push2(`<option${ssrRenderAttr("value", entity.value)}${ssrIncludeBooleanAttr(Array.isArray(selectedEntity.value) ? ssrLooseContain(selectedEntity.value, entity.value) : ssrLooseEqual(selectedEntity.value, entity.value)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)(entity.label_key))}</option>`);
            });
            _push2(`<!--]--></select><select class="${ssrRenderClass(selectClass)}"${_scopeId}><!--[-->`);
            ssrRenderList(props.metrics, (metric) => {
              _push2(`<option${ssrRenderAttr("value", metric.value)}${ssrIncludeBooleanAttr(Array.isArray(selectedMetric.value) ? ssrLooseContain(selectedMetric.value, metric.value) : ssrLooseEqual(selectedMetric.value, metric.value)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(unref(t)(metric.label_key))}</option>`);
            });
            _push2(`<!--]--></select></div><h2 class="text-center text-lg font-semibold text-slate-700 dark:text-slate-100 mb-3"${_scopeId}>${ssrInterpolate(chartTitle.value)}</h2>`);
            if (!props.chart.data || !props.chart.data.length) {
              _push2(`<div class="text-center text-gray-500 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<div class="space-y-3"${_scopeId}><div class="p-3 bg-white dark:bg-slate-800 border border-slate-400 rounded shadow"${_scopeId}>`);
              _push2(ssrRenderComponent(EntityBarChart, {
                items: props.chart.data,
                title: chartTitle.value,
                height: 400
              }, null, _parent2, _scopeId));
              _push2(`</div><div class="p-3 bg-white dark:bg-slate-800 border border-slate-400 rounded shadow"${_scopeId}>`);
              _push2(ssrRenderComponent(EntityLineChart, {
                items: props.chart.data,
                title: chartTitle.value,
                height: 400
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
              if (pagination.value && pagination.value.last_page > 1) {
                _push2(`<div class="flex flex-wrap gap-2 items-center justify-center pt-2"${_scopeId}><button type="button" class="${ssrRenderClass(paginationButtonClass)}"${ssrIncludeBooleanAttr(
                  pagination.value.current_page <= 1
                ) ? " disabled" : ""}${_scopeId}> ← </button><div class="text-sm font-semibold text-slate-700 dark:text-slate-100"${_scopeId}>${ssrInterpolate(pagination.value.from)} – ${ssrInterpolate(pagination.value.to)} / ${ssrInterpolate(pagination.value.total)} <span class="mx-2"${_scopeId}> · </span> ${ssrInterpolate(pagination.value.current_page)} / ${ssrInterpolate(pagination.value.last_page)}</div><button type="button" class="${ssrRenderClass(paginationButtonClass)}"${ssrIncludeBooleanAttr(
                  pagination.value.current_page >= pagination.value.last_page
                ) ? " disabled" : ""}${_scopeId}> → </button></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
            }
            _push2(`</div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "flex flex-wrap gap-2 items-center justify-between mb-3" }, [
                    withDirectives(createVNode("select", {
                      "onUpdate:modelValue": ($event) => selectedEntity.value = $event,
                      class: selectClass,
                      onChange: changeEntity
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(props.entities, (entity) => {
                        return openBlock(), createBlock("option", {
                          key: entity.value,
                          value: entity.value
                        }, toDisplayString(unref(t)(entity.label_key)), 9, ["value"]);
                      }), 128))
                    ], 40, ["onUpdate:modelValue"]), [
                      [vModelSelect, selectedEntity.value]
                    ]),
                    withDirectives(createVNode("select", {
                      "onUpdate:modelValue": ($event) => selectedMetric.value = $event,
                      class: selectClass,
                      onChange: changeMetric
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(props.metrics, (metric) => {
                        return openBlock(), createBlock("option", {
                          key: metric.value,
                          value: metric.value
                        }, toDisplayString(unref(t)(metric.label_key)), 9, ["value"]);
                      }), 128))
                    ], 40, ["onUpdate:modelValue"]), [
                      [vModelSelect, selectedMetric.value]
                    ])
                  ]),
                  createVNode("h2", { class: "text-center text-lg font-semibold text-slate-700 dark:text-slate-100 mb-3" }, toDisplayString(chartTitle.value), 1),
                  !props.chart.data || !props.chart.data.length ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "text-center text-gray-500 dark:text-gray-300"
                  }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", {
                    key: 1,
                    class: "space-y-3"
                  }, [
                    createVNode("div", { class: "p-3 bg-white dark:bg-slate-800 border border-slate-400 rounded shadow" }, [
                      createVNode(EntityBarChart, {
                        items: props.chart.data,
                        title: chartTitle.value,
                        height: 400
                      }, null, 8, ["items", "title"])
                    ]),
                    createVNode("div", { class: "p-3 bg-white dark:bg-slate-800 border border-slate-400 rounded shadow" }, [
                      createVNode(EntityLineChart, {
                        items: props.chart.data,
                        title: chartTitle.value,
                        height: 400
                      }, null, 8, ["items", "title"])
                    ]),
                    pagination.value && pagination.value.last_page > 1 ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "flex flex-wrap gap-2 items-center justify-center pt-2"
                    }, [
                      createVNode("button", {
                        type: "button",
                        class: paginationButtonClass,
                        disabled: pagination.value.current_page <= 1,
                        onClick: ($event) => changePage(
                          pagination.value.current_page - 1
                        )
                      }, " ← ", 8, ["disabled", "onClick"]),
                      createVNode("div", { class: "text-sm font-semibold text-slate-700 dark:text-slate-100" }, [
                        createTextVNode(toDisplayString(pagination.value.from) + " – " + toDisplayString(pagination.value.to) + " / " + toDisplayString(pagination.value.total) + " ", 1),
                        createVNode("span", { class: "mx-2" }, " · "),
                        createTextVNode(" " + toDisplayString(pagination.value.current_page) + " / " + toDisplayString(pagination.value.last_page), 1)
                      ]),
                      createVNode("button", {
                        type: "button",
                        class: paginationButtonClass,
                        disabled: pagination.value.current_page >= pagination.value.last_page,
                        onClick: ($event) => changePage(
                          pagination.value.current_page + 1
                        )
                      }, " → ", 8, ["disabled", "onClick"])
                    ])) : createCommentVNode("", true)
                  ]))
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Statistics/Charts/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
