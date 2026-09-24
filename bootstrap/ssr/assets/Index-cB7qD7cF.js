import { ref, computed, watch, mergeProps, unref, withCtx, createVNode, withDirectives, toDisplayString, createBlock, openBlock, Fragment, renderList, vModelSelect, vModelCheckbox, createTextVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderClass } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import * as XLSX from "xlsx";
import html2pdf from "html2pdf.js";
import JSZip from "jszip";
import { saveAs } from "file-saver";
import { Packer, TableRow, TableCell, Paragraph, TextRun, Document, Table, PageOrientation, WidthType } from "docx";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
import "@inertiajs/vue3";
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
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    tables: { type: Array, default: () => [] },
    selectedTable: { type: String, default: "" },
    columns: { type: Array, default: () => [] },
    items: { type: Array, default: () => [] }
  },
  setup(__props) {
    var _a;
    const { t } = useI18n();
    const props = __props;
    const selectedTable = ref(props.selectedTable || ((_a = props.tables) == null ? void 0 : _a[0]) || "");
    const items = ref(props.items || []);
    const columns = ref(props.columns || []);
    const selectedFields = ref(props.columns || []);
    const loading = ref(false);
    const errorMessage = ref("");
    const currentDate = (/* @__PURE__ */ new Date()).toLocaleDateString("ru-RU");
    const currentDateTime = (/* @__PURE__ */ new Date()).toISOString().slice(0, 19).replace(/:/g, "-");
    const hasData = computed(() => items.value.length > 0);
    const reportTitle = computed(() => `Отчёт: ${selectedTable.value} - ${currentDate}`);
    const fetchData = async () => {
      if (!selectedTable.value) return;
      loading.value = true;
      errorMessage.value = "";
      try {
        const response = await fetch(`/admin/reports?table=${selectedTable.value}`, {
          headers: { Accept: "application/json" }
        });
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        items.value = data.data || [];
        columns.value = data.columns || [];
        selectedFields.value = data.columns || [];
      } catch (error) {
        console.error("Не удалось получить данные:", error);
        errorMessage.value = "Не удалось получить данные таблицы.";
        items.value = [];
        columns.value = [];
        selectedFields.value = [];
      } finally {
        loading.value = false;
      }
    };
    watch(selectedTable, fetchData);
    const formatValue = (value) => {
      if (value === null || value === void 0) return "";
      if (typeof value === "boolean") {
        return value ? "true" : "false";
      }
      if (Array.isArray(value)) {
        return value.join(", ");
      }
      if (typeof value === "object") {
        return JSON.stringify(value);
      }
      return String(value);
    };
    const formatData = (data) => {
      return data.map((item) => {
        const row = {};
        selectedFields.value.forEach((field) => {
          row[field] = formatValue(item[field]);
        });
        return row;
      });
    };
    const downloadReport = (format) => {
      if (!hasData.value || !selectedFields.value.length) {
        alert("Нет данных для экспорта.");
        return;
      }
      switch (format) {
        case "csv":
          downloadCSV();
          break;
        case "xls":
          downloadXLS();
          break;
        case "pdf":
          downloadPDF();
          break;
        case "zip":
          downloadZIP();
          break;
        case "docx":
          downloadDOCX();
          break;
      }
    };
    const downloadCSV = () => {
      const worksheet = XLSX.utils.json_to_sheet(formatData(items.value));
      const csvOutput = XLSX.utils.sheet_to_csv(worksheet);
      const blob = new Blob(["\uFEFF" + csvOutput], { type: "text/csv;charset=utf-8;" });
      saveAs(blob, `reports_${selectedTable.value}_${currentDateTime}.csv`);
    };
    const downloadXLS = () => {
      const worksheet = XLSX.utils.json_to_sheet(formatData(items.value));
      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, "Report");
      const xlsOutput = XLSX.write(workbook, { bookType: "xlsx", type: "array" });
      const blob = new Blob([xlsOutput], { type: "application/octet-stream" });
      saveAs(blob, `reports_${selectedTable.value}_${currentDateTime}.xlsx`);
    };
    const downloadPDF = () => {
      const formattedData = formatData(items.value);
      const tableHeaders = selectedFields.value.map((field) => `<th>${field}</th>`).join("");
      const tableRows = formattedData.map((item) => {
        const row = selectedFields.value.map((field) => `<td>${formatValue(item[field])}</td>`).join("");
        return `<tr>${row}</tr>`;
      }).join("");
      const wrapper = document.createElement("div");
      wrapper.innerHTML = `
        <div style="font-family: Arial, sans-serif; font-size: 9px; color: #111; background: #fff;">
            <h1 style="font-size: 18px; margin-bottom: 12px;">${reportTitle.value}</h1>

            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                    table-layout: fixed;
                }

                th, td {
                    border: 1px solid #999;
                    padding: 4px 6px;
                    vertical-align: top;
                    word-break: break-word;
                    overflow-wrap: anywhere;
                }

                th {
                    background: #f0f0f0;
                    font-weight: bold;
                }

                tr {
                    page-break-inside: avoid;
                }
            </style>

            <table>
                <thead>
                    <tr>${tableHeaders}</tr>
                </thead>
                <tbody>
                    ${tableRows}
                </tbody>
            </table>
        </div>
    `;
      const options = {
        margin: 0.35,
        filename: `reports_${selectedTable.value}_${currentDateTime}.pdf`,
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: {
          scale: 1,
          logging: false,
          useCORS: true,
          backgroundColor: "#ffffff"
        },
        jsPDF: {
          unit: "in",
          format: "a4",
          orientation: "landscape"
        },
        pagebreak: {
          mode: ["css", "legacy"],
          avoid: ["tr"]
        }
      };
      html2pdf().set(options).from(wrapper).save();
    };
    const downloadZIP = async () => {
      const zip = new JSZip();
      const formattedData = formatData(items.value);
      const worksheet = XLSX.utils.json_to_sheet(formattedData);
      const csvOutput = XLSX.utils.sheet_to_csv(worksheet);
      const csvBlob = new Blob(["\uFEFF" + csvOutput], { type: "text/csv;charset=utf-8;" });
      zip.file(`reports_${selectedTable.value}_${currentDateTime}.csv`, csvBlob);
      const workbook = XLSX.utils.book_new();
      XLSX.utils.book_append_sheet(workbook, worksheet, "Report");
      const xlsOutput = XLSX.write(workbook, { bookType: "xlsx", type: "array" });
      const xlsBlob = new Blob([xlsOutput], { type: "application/octet-stream" });
      zip.file(`reports_${selectedTable.value}_${currentDateTime}.xlsx`, xlsBlob);
      const doc = createDOCX();
      const docBlob = await Packer.toBlob(doc);
      zip.file(`reports_${selectedTable.value}_${currentDateTime}.docx`, docBlob);
      const content = await zip.generateAsync({ type: "blob" });
      saveAs(content, `reports_${selectedTable.value}_${currentDateTime}.zip`);
    };
    const downloadDOCX = () => {
      const doc = createDOCX();
      Packer.toBlob(doc).then((blob) => {
        saveAs(blob, `reports_${selectedTable.value}_${currentDateTime}.docx`);
      });
    };
    const createDOCX = () => {
      const formattedData = formatData(items.value);
      const tableRows = [
        new TableRow({
          children: selectedFields.value.map((key) => new TableCell({
            children: [
              new Paragraph({
                children: [new TextRun(key)],
                spacing: { after: 200 }
              })
            ]
          }))
        })
      ];
      formattedData.forEach((item) => {
        tableRows.push(
          new TableRow({
            children: selectedFields.value.map((key) => new TableCell({
              children: [
                new Paragraph({
                  children: [new TextRun(String(item[key] ?? ""))],
                  spacing: { after: 200 }
                })
              ]
            }))
          })
        );
      });
      return new Document({
        sections: [
          {
            properties: {
              page: {
                size: {
                  orientation: PageOrientation.LANDSCAPE
                }
              }
            },
            children: [
              new Paragraph({
                text: reportTitle.value,
                heading: "Heading1",
                spacing: { after: 400 }
              }),
              new Table({
                rows: tableRows,
                width: { size: 100, type: WidthType.PERCENTAGE }
              })
            ]
          }
        ]
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("reports")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("reports"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("reports")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("reports")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto" data-v-262f6e13${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-4 p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" data-v-262f6e13${_scopeId}><div data-v-262f6e13${_scopeId}><label for="reportTable" class="block text-sm font-medium text-gray-700 dark:text-gray-300" data-v-262f6e13${_scopeId}>${ssrInterpolate(unref(t)("selectReportType"))}</label><select id="reportTable" class="mt-1 block w-full py-1 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" data-v-262f6e13${_scopeId}><!--[-->`);
            ssrRenderList(__props.tables, (table) => {
              _push2(`<option${ssrRenderAttr("value", table)} data-v-262f6e13${ssrIncludeBooleanAttr(Array.isArray(selectedTable.value) ? ssrLooseContain(selectedTable.value, table) : ssrLooseEqual(selectedTable.value, table)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(table)}</option>`);
            });
            _push2(`<!--]--></select></div><div class="mt-4 sm:mt-0 sm:ml-4" data-v-262f6e13${_scopeId}><button class="inline-flex items-center px-2 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700" data-v-262f6e13${_scopeId}> CSV </button><button class="ml-2 inline-flex items-center px-2 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700" data-v-262f6e13${_scopeId}> Excel </button><button class="ml-2 inline-flex items-center px-2 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700" data-v-262f6e13${_scopeId}> Word </button><button class="ml-2 inline-flex items-center px-2 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700" data-v-262f6e13${_scopeId}> PDF </button><button class="ml-2 inline-flex items-center px-2 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700" data-v-262f6e13${_scopeId}> ZIP </button></div></div><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" data-v-262f6e13${_scopeId}><label class="block text-sm font-medium text-gray-700 dark:text-gray-300" data-v-262f6e13${_scopeId}>${ssrInterpolate(unref(t)("selectFieldsPrint"))}</label>`);
            if (columns.value.length) {
              _push2(`<div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-2" data-v-262f6e13${_scopeId}><!--[-->`);
              ssrRenderList(columns.value, (column) => {
                _push2(`<div data-v-262f6e13${_scopeId}><input type="checkbox"${ssrIncludeBooleanAttr(Array.isArray(selectedFields.value) ? ssrLooseContain(selectedFields.value, column) : selectedFields.value) ? " checked" : ""}${ssrRenderAttr("value", column)} class="form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out" data-v-262f6e13${_scopeId}><span class="ml-2 text-gray-700 dark:text-gray-300" data-v-262f6e13${_scopeId}>${ssrInterpolate(column)}</span></div>`);
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<div class="mt-2 text-sm text-gray-700 dark:text-gray-300" data-v-262f6e13${_scopeId}> Нет доступных колонок. </div>`);
            }
            _push2(`</div><div id="reportContent" class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95 text-xs" data-v-262f6e13${_scopeId}><h1 class="text-md font-semibold mb-4 dark:text-white" data-v-262f6e13${_scopeId}>${ssrInterpolate(reportTitle.value)}</h1>`);
            if (loading.value) {
              _push2(`<div class="text-slate-700 dark:text-slate-100" data-v-262f6e13${_scopeId}> Загрузка... </div>`);
            } else if (errorMessage.value) {
              _push2(`<div class="text-red-600 dark:text-red-300 font-semibold" data-v-262f6e13${_scopeId}>${ssrInterpolate(errorMessage.value)}</div>`);
            } else if (!hasData.value) {
              _push2(`<div class="text-slate-700 dark:text-slate-100" data-v-262f6e13${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</div>`);
            } else {
              _push2(`<div class="overflow-x-auto" data-v-262f6e13${_scopeId}><table class="min-w-full bg-white dark:bg-gray-800 dark:text-white text-xxs" data-v-262f6e13${_scopeId}><thead data-v-262f6e13${_scopeId}><tr class="dark:text-slate-700" data-v-262f6e13${_scopeId}><!--[-->`);
              ssrRenderList(selectedFields.value, (key) => {
                _push2(`<th class="px-4 py-1 dark:bg-gray-900 dark:text-white" data-v-262f6e13${_scopeId}>${ssrInterpolate(key)}</th>`);
              });
              _push2(`<!--]--></tr></thead><tbody data-v-262f6e13${_scopeId}><!--[-->`);
              ssrRenderList(items.value, (item, index) => {
                _push2(`<tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700" data-v-262f6e13${_scopeId}><!--[-->`);
                ssrRenderList(selectedFields.value, (key) => {
                  _push2(`<td class="${ssrRenderClass([{ truncate: String(item[key] || "").length > 80 }, "px-4 py-1"])}" data-v-262f6e13${_scopeId}>${ssrInterpolate(formatValue(item[key]))}</td>`);
                });
                _push2(`<!--]--></tr>`);
              });
              _push2(`<!--]--></tbody></table></div>`);
            }
            _push2(`</div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-4 p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", null, [
                    createVNode("label", {
                      for: "reportTable",
                      class: "block text-sm font-medium text-gray-700 dark:text-gray-300"
                    }, toDisplayString(unref(t)("selectReportType")), 1),
                    withDirectives(createVNode("select", {
                      id: "reportTable",
                      "onUpdate:modelValue": ($event) => selectedTable.value = $event,
                      class: "mt-1 block w-full py-1 px-3 border border-gray-300 bg-white dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    }, [
                      (openBlock(true), createBlock(Fragment, null, renderList(__props.tables, (table) => {
                        return openBlock(), createBlock("option", {
                          key: table,
                          value: table
                        }, toDisplayString(table), 9, ["value"]);
                      }), 128))
                    ], 8, ["onUpdate:modelValue"]), [
                      [vModelSelect, selectedTable.value]
                    ])
                  ]),
                  createVNode("div", { class: "mt-4 sm:mt-0 sm:ml-4" }, [
                    createVNode("button", {
                      onClick: ($event) => downloadReport("csv"),
                      class: "inline-flex items-center px-2 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700"
                    }, " CSV ", 8, ["onClick"]),
                    createVNode("button", {
                      onClick: ($event) => downloadReport("xls"),
                      class: "ml-2 inline-flex items-center px-2 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                    }, " Excel ", 8, ["onClick"]),
                    createVNode("button", {
                      onClick: ($event) => downloadReport("docx"),
                      class: "ml-2 inline-flex items-center px-2 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
                    }, " Word ", 8, ["onClick"]),
                    createVNode("button", {
                      onClick: ($event) => downloadReport("pdf"),
                      class: "ml-2 inline-flex items-center px-2 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700"
                    }, " PDF ", 8, ["onClick"]),
                    createVNode("button", {
                      onClick: ($event) => downloadReport("zip"),
                      class: "ml-2 inline-flex items-center px-2 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-slate-600 hover:bg-slate-700"
                    }, " ZIP ", 8, ["onClick"])
                  ])
                ]),
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("label", { class: "block text-sm font-medium text-gray-700 dark:text-gray-300" }, toDisplayString(unref(t)("selectFieldsPrint")), 1),
                  columns.value.length ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "mt-2 grid grid-cols-2 md:grid-cols-4 gap-2"
                  }, [
                    (openBlock(true), createBlock(Fragment, null, renderList(columns.value, (column) => {
                      return openBlock(), createBlock("div", { key: column }, [
                        withDirectives(createVNode("input", {
                          type: "checkbox",
                          "onUpdate:modelValue": ($event) => selectedFields.value = $event,
                          value: column,
                          class: "form-checkbox h-4 w-4 text-indigo-600 transition duration-150 ease-in-out"
                        }, null, 8, ["onUpdate:modelValue", "value"]), [
                          [vModelCheckbox, selectedFields.value]
                        ]),
                        createVNode("span", { class: "ml-2 text-gray-700 dark:text-gray-300" }, toDisplayString(column), 1)
                      ]);
                    }), 128))
                  ])) : (openBlock(), createBlock("div", {
                    key: 1,
                    class: "mt-2 text-sm text-gray-700 dark:text-gray-300"
                  }, " Нет доступных колонок. "))
                ]),
                createVNode("div", {
                  id: "reportContent",
                  class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95 text-xs"
                }, [
                  createVNode("h1", { class: "text-md font-semibold mb-4 dark:text-white" }, toDisplayString(reportTitle.value), 1),
                  loading.value ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "text-slate-700 dark:text-slate-100"
                  }, " Загрузка... ")) : errorMessage.value ? (openBlock(), createBlock("div", {
                    key: 1,
                    class: "text-red-600 dark:text-red-300 font-semibold"
                  }, toDisplayString(errorMessage.value), 1)) : !hasData.value ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "text-slate-700 dark:text-slate-100"
                  }, toDisplayString(unref(t)("noData")), 1)) : (openBlock(), createBlock("div", {
                    key: 3,
                    class: "overflow-x-auto"
                  }, [
                    createVNode("table", { class: "min-w-full bg-white dark:bg-gray-800 dark:text-white text-xxs" }, [
                      createVNode("thead", null, [
                        createVNode("tr", { class: "dark:text-slate-700" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(selectedFields.value, (key) => {
                            return openBlock(), createBlock("th", {
                              key,
                              class: "px-4 py-1 dark:bg-gray-900 dark:text-white"
                            }, toDisplayString(key), 1);
                          }), 128))
                        ])
                      ]),
                      createVNode("tbody", null, [
                        (openBlock(true), createBlock(Fragment, null, renderList(items.value, (item, index) => {
                          return openBlock(), createBlock("tr", {
                            key: item.id || index,
                            class: "bg-white dark:bg-gray-800 border-b dark:border-gray-700"
                          }, [
                            (openBlock(true), createBlock(Fragment, null, renderList(selectedFields.value, (key) => {
                              return openBlock(), createBlock("td", {
                                key,
                                class: [{ truncate: String(item[key] || "").length > 80 }, "px-4 py-1"]
                              }, toDisplayString(formatValue(item[key])), 3);
                            }), 128))
                          ]);
                        }), 128))
                      ])
                    ])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/Reports/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
const Index = /* @__PURE__ */ _export_sfc(_sfc_main, [["__scopeId", "data-v-262f6e13"]]);
export {
  Index as default
};
