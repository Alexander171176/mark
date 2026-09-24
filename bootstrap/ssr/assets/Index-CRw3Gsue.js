import { ref, computed, watch, onMounted, onBeforeUnmount, mergeProps, withCtx, unref, createBlock, openBlock, createVNode, createCommentVNode, toDisplayString, withDirectives, vModelText, vModelSelect, Fragment, renderList, withKeys, createTextVNode, nextTick, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderClass, ssrRenderStyle, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual, ssrRenderList } from "vue/server-renderer";
import { Link, router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import * as XLSX from "xlsx";
import html2pdf from "html2pdf.js";
import JSZip from "jszip";
import { saveAs } from "file-saver";
import { Packer, TableRow, TableCell, Paragraph, TextRun, Document, Table, PageOrientation, WidthType } from "docx";
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
function useTableExport() {
  const currentDateTime = () => {
    return (/* @__PURE__ */ new Date()).toISOString().slice(0, 19).replace(/:/g, "-");
  };
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
  const formatData = (items, columns) => {
    return items.map((item) => {
      const row = {};
      columns.forEach((column) => {
        row[column] = formatValue(item[column]);
      });
      return row;
    });
  };
  const downloadCSV = (items, columns, filename) => {
    const worksheet = XLSX.utils.json_to_sheet(formatData(items, columns));
    const csvOutput = XLSX.utils.sheet_to_csv(worksheet);
    const blob = new Blob(["\uFEFF" + csvOutput], {
      type: "text/csv;charset=utf-8;"
    });
    saveAs(blob, `${filename}_${currentDateTime()}.csv`);
  };
  const downloadXLS = (items, columns, filename) => {
    const worksheet = XLSX.utils.json_to_sheet(formatData(items, columns));
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Export");
    const xlsOutput = XLSX.write(workbook, {
      bookType: "xlsx",
      type: "array"
    });
    const blob = new Blob([xlsOutput], {
      type: "application/octet-stream"
    });
    saveAs(blob, `${filename}_${currentDateTime()}.xlsx`);
  };
  const createDOCX = (items, columns, title) => {
    const formattedData = formatData(items, columns);
    const rows = [
      new TableRow({
        children: columns.map((column) => new TableCell({
          children: [
            new Paragraph({
              children: [new TextRun(column)]
            })
          ]
        }))
      })
    ];
    formattedData.forEach((item) => {
      rows.push(
        new TableRow({
          children: columns.map((column) => new TableCell({
            children: [
              new Paragraph({
                children: [new TextRun(String(item[column] ?? ""))]
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
              text: title,
              heading: "Heading1"
            }),
            new Table({
              rows,
              width: {
                size: 100,
                type: WidthType.PERCENTAGE
              }
            })
          ]
        }
      ]
    });
  };
  const downloadDOCX = async (items, columns, filename, title) => {
    const doc = createDOCX(items, columns, title);
    const blob = await Packer.toBlob(doc);
    saveAs(blob, `${filename}_${currentDateTime()}.docx`);
  };
  const downloadPDF = (items, columns, filename, title) => {
    const formattedData = formatData(items, columns);
    const headers = columns.map((column) => `<th>${column}</th>`).join("");
    const rows = formattedData.map((item) => {
      const row = columns.map((column) => `<td>${formatValue(item[column])}</td>`).join("");
      return `<tr>${row}</tr>`;
    }).join("");
    const wrapper = document.createElement("div");
    wrapper.innerHTML = `
            <div style="font-family: Arial, sans-serif; font-size: 9px; color: #111; background: #fff;">
                <h1 style="font-size: 18px; margin-bottom: 12px;">${title}</h1>

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
                        <tr>${headers}</tr>
                    </thead>
                    <tbody>
                        ${rows}
                    </tbody>
                </table>
            </div>
        `;
    html2pdf().set({
      margin: 0.35,
      filename: `${filename}_${currentDateTime()}.pdf`,
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
    }).from(wrapper).save();
  };
  const downloadZIP = async (items, columns, filename, title) => {
    const zip = new JSZip();
    const timestamp = currentDateTime();
    const formattedData = formatData(items, columns);
    const worksheet = XLSX.utils.json_to_sheet(formattedData);
    const csvOutput = XLSX.utils.sheet_to_csv(worksheet);
    zip.file(`${filename}_${timestamp}.csv`, "\uFEFF" + csvOutput);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Export");
    const xlsOutput = XLSX.write(workbook, {
      bookType: "xlsx",
      type: "array"
    });
    zip.file(`${filename}_${timestamp}.xlsx`, xlsOutput);
    const doc = createDOCX(items, columns, title);
    const docBlob = await Packer.toBlob(doc);
    zip.file(`${filename}_${timestamp}.docx`, docBlob);
    const content = await zip.generateAsync({ type: "blob" });
    saveAs(content, `${filename}_${timestamp}.zip`);
  };
  const download = async (format, items, columns, filename, title) => {
    if (!(items == null ? void 0 : items.length) || !(columns == null ? void 0 : columns.length)) {
      alert("Нет данных для экспорта.");
      return;
    }
    switch (format) {
      case "csv":
        downloadCSV(items, columns, filename);
        break;
      case "xls":
        downloadXLS(items, columns, filename);
        break;
      case "docx":
        await downloadDOCX(items, columns, filename, title);
        break;
      case "pdf":
        downloadPDF(items, columns, filename, title);
        break;
      case "zip":
        await downloadZIP(items, columns, filename, title);
        break;
    }
  };
  return {
    download
  };
}
const inputClass = "w-full min-w-0 px-3 py-1 border border-slate-500 font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100";
const selectClass = "w-16 px-2 py-1 border border-slate-500 font-semibold text-sm focus:border-indigo-500 focus:ring-indigo-300 rounded-sm shadow-sm dark:bg-cyan-800 dark:text-slate-100";
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    visitorLogs: {
      type: Object,
      default: () => ({})
    },
    filters: {
      type: Object,
      default: () => ({})
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const filters = ref({
      date_from: props.filters.date_from || "",
      date_to: props.filters.date_to || "",
      module: props.filters.module || "",
      event_type: props.filters.event_type || "",
      user_id: props.filters.user_id || "",
      visitor_uuid: props.filters.visitor_uuid || "",
      url: props.filters.url || "",
      per_page: props.filters.per_page || "50",
      sort_direction: props.filters.sort_direction || "desc"
    });
    const applyFilters = () => {
      router.get(
        route("admin.analyticsVisitorLogs.index"),
        {
          ...filters.value,
          page: 1
        },
        {
          preserveScroll: true,
          preserveState: true
        }
      );
    };
    const resetFilters = () => {
      router.get(
        route("admin.analyticsVisitorLogs.index"),
        {},
        {
          preserveScroll: true,
          preserveState: false
        }
      );
    };
    const currentPage = computed(() => {
      var _a;
      return Number(((_a = props.visitorLogs.meta) == null ? void 0 : _a.current_page) || 1);
    });
    const lastPage = computed(() => {
      var _a;
      return Number(((_a = props.visitorLogs.meta) == null ? void 0 : _a.last_page) || 1);
    });
    const pageInput = ref(currentPage.value);
    watch(currentPage, (value) => {
      pageInput.value = value;
    });
    const goToPage = (value = pageInput.value) => {
      let page = Number.parseInt(value, 10);
      if (!Number.isInteger(page)) {
        page = currentPage.value;
      }
      page = Math.max(1, page);
      page = Math.min(page, lastPage.value);
      pageInput.value = page;
      if (page === currentPage.value) {
        return;
      }
      router.get(
        route("admin.analyticsVisitorLogs.index"),
        {
          ...filters.value,
          page
        },
        {
          preserveScroll: true,
          preserveState: true
        }
      );
    };
    const previousPage = () => {
      if (currentPage.value <= 1) {
        return;
      }
      goToPage(currentPage.value - 1);
    };
    const nextPage = () => {
      if (currentPage.value >= lastPage.value) {
        return;
      }
      goToPage(currentPage.value + 1);
    };
    const formatDate = (value) => {
      if (!value) {
        return "—";
      }
      return new Date(value).toLocaleString();
    };
    const importAnalytics = () => {
      router.post(
        route("admin.analytics.import"),
        {},
        {
          preserveScroll: true,
          preserveState: false
        }
      );
    };
    const { download } = useTableExport();
    const exportColumns = [
      "id",
      "visited_at",
      "user_id",
      "visitor_uuid",
      "module",
      "entity_type",
      "entity_id",
      "event_type",
      "page_title",
      "url",
      "referer",
      "time_on_page",
      "scroll_depth",
      "clicks_count",
      "locale"
    ];
    const exportItems = computed(() => {
      return props.visitorLogs.data || [];
    });
    const downloadAnalytics = (format) => {
      download(
        format,
        exportItems.value,
        exportColumns,
        "analytics_visitor_logs",
        "Analytics Visitor Logs"
      );
    };
    const cleanupAnalytics = () => {
      if (!filters.value.date_from || !filters.value.date_to) {
        alert("Выберите период очистки.");
        return;
      }
      if (!confirm("Удалить аналитику за выбранный период?")) {
        return;
      }
      router.delete(
        route("admin.analytics.cleanup.destroy"),
        {
          data: {
            date_from: filters.value.date_from,
            date_to: filters.value.date_to
          },
          preserveScroll: true,
          preserveState: false
        }
      );
    };
    const topScroll = ref(null);
    const tableScroll = ref(null);
    const tableContent = ref(null);
    const tableScrollWidth = ref(0);
    let resizeObserver = null;
    const updateTableScrollWidth = async () => {
      var _a;
      await nextTick();
      tableScrollWidth.value = ((_a = tableContent.value) == null ? void 0 : _a.scrollWidth) || 0;
    };
    const syncTopScroll = () => {
      if (!topScroll.value || !tableScroll.value) {
        return;
      }
      topScroll.value.scrollLeft = tableScroll.value.scrollLeft;
    };
    const syncTableScroll = () => {
      if (!topScroll.value || !tableScroll.value) {
        return;
      }
      tableScroll.value.scrollLeft = topScroll.value.scrollLeft;
    };
    onMounted(async () => {
      await updateTableScrollWidth();
      if (tableContent.value) {
        resizeObserver = new ResizeObserver(() => {
          updateTableScrollWidth();
        });
        resizeObserver.observe(tableContent.value);
      }
    });
    onBeforeUnmount(() => {
      resizeObserver == null ? void 0 : resizeObserver.disconnect();
    });
    watch(
      () => props.visitorLogs.data,
      () => {
        updateTableScrollWidth();
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({ title: "Analytics Visitor Logs" }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("analyticsLogs"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("analyticsLogs")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("analyticsLogs")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="flex flex-col lg:flex-row items-center justify-between gap-3 pb-4"${_scopeId}><div class="flex items-center justify-start flex-wrap gap-3"${_scopeId}><button type="button" class="px-3 py-1 rounded flex items-center justify-center gap-2 bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-700 dark:hover:bg-cyan-800"${_scopeId}><svg class="shrink-0 h-3 w-3" viewBox="0 0 24 24"${_scopeId}><path class="fill-current text-white" d="M14,0H3A1,1,0,0,0,2,1V23a1,1,0,0,0,1,1H21a1,1,0,0,0,1-1V8H15a1,1,0,0,1-1-1ZM5.5,17h13a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,5.5,17Zm0-5h13a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,5.5,12Zm5-3h-5A.5.5,0,0,1,5,8.5v-1A.5.5,0,0,1,5.5,7h5a.5.5,0,0,1,.5.5v1A.5.5,0,0,1,10.5,9Z"${_scopeId}></path><polygon class="fill-current text-white" points="21.414 6 16 6 16 0.586 21.414 6"${_scopeId}></polygon></svg><span class="text-sm font-semibold text-white"${_scopeId}>${ssrInterpolate(unref(t)("import"))}</span></button><button type="button" class="px-3 py-1 rounded flex items-center justify-center gap-2 bg-rose-500 hover:bg-rose-800"${_scopeId}><svg class="shrink-0 h-3 w-3" viewBox="0 0 512 512"${_scopeId}><path class="fill-current text-white" d="M504 255.531c.253 136.64-111.18 248.372-247.82 248.468-59.015.042-113.223-20.53-155.822-54.911-11.077-8.94-11.905-25.541-1.839-35.607l11.267-11.267c8.609-8.609 22.353-9.551 31.891-1.984C173.062 425.135 212.781 440 256 440c101.705 0 184-82.311 184-184 0-101.705-82.311-184-184-184-48.814 0-93.149 18.969-126.068 49.932l50.754 50.754c10.08 10.08 2.941 27.314-11.313 27.314H24c-8.837 0-16-7.163-16-16V38.627c0-14.254 17.234-21.393 27.314-11.314l49.372 49.372C129.209 34.136 189.552 8 256 8c136.81 0 247.747 110.78 248 247.531zm-180.912 78.784l9.823-12.63c8.138-10.463 6.253-25.542-4.21-33.679L288 256.349V152c0-13.255-10.745-24-24-24h-16c-13.255 0-24 10.745-24 24v135.651l65.409 50.874c10.463 8.137 25.541 6.253 33.679-4.21z"${_scopeId}></path></svg><span class="text-sm font-semibold text-white"${_scopeId}>${ssrInterpolate(unref(t)("clear"))} ${ssrInterpolate(unref(t)("period"))}</span></button></div><div class="flex items-center flex-wrap gap-2"${_scopeId}><button type="button" class="px-3 py-1 rounded text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700"${_scopeId}> CSV </button><button type="button" class="px-3 py-1 rounded text-sm font-semibold text-white bg-green-600 hover:bg-green-700"${_scopeId}> Excel </button><button type="button" class="px-3 py-1 rounded text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700"${_scopeId}> Word </button><button type="button" class="px-3 py-1 rounded text-sm font-semibold text-white bg-red-600 hover:bg-red-700"${_scopeId}> PDF </button><button type="button" class="px-3 py-1 rounded text-sm font-semibold text-white bg-slate-600 hover:bg-slate-700"${_scopeId}> ZIP </button></div></div><div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-10 ] gap-2 mb-3"${_scopeId}><input${ssrRenderAttr("value", filters.value.date_from)} type="date" class="${ssrRenderClass([inputClass, "xl:col-span-2"])}"${_scopeId}><input${ssrRenderAttr("value", filters.value.date_to)} type="date" class="${ssrRenderClass([inputClass, "xl:col-span-2"])}"${_scopeId}><input${ssrRenderAttr("value", filters.value.module)} type="text" placeholder="module" class="${ssrRenderClass(inputClass)}"${_scopeId}><input${ssrRenderAttr("value", filters.value.event_type)} type="text" placeholder="event_type" class="${ssrRenderClass(inputClass)}"${_scopeId}><input${ssrRenderAttr("value", filters.value.user_id)} type="number" min="1" placeholder="user_id" class="${ssrRenderClass(inputClass)}"${_scopeId}><input${ssrRenderAttr("value", filters.value.visitor_uuid)} type="text" placeholder="visitor_uuid" class="${ssrRenderClass([inputClass, "xl:col-span-2"])}"${_scopeId}><input${ssrRenderAttr("value", filters.value.url)} type="text" placeholder="url" class="${ssrRenderClass(inputClass)}"${_scopeId}></div><div class="flex flex-col md:flex-row items-center justify-between gap-2 mb-3"${_scopeId}><select class="${ssrRenderClass(selectClass)}"${_scopeId}><option value="10"${ssrIncludeBooleanAttr(Array.isArray(filters.value.per_page) ? ssrLooseContain(filters.value.per_page, "10") : ssrLooseEqual(filters.value.per_page, "10")) ? " selected" : ""}${_scopeId}>10</option><option value="25"${ssrIncludeBooleanAttr(Array.isArray(filters.value.per_page) ? ssrLooseContain(filters.value.per_page, "25") : ssrLooseEqual(filters.value.per_page, "25")) ? " selected" : ""}${_scopeId}>25</option><option value="50"${ssrIncludeBooleanAttr(Array.isArray(filters.value.per_page) ? ssrLooseContain(filters.value.per_page, "50") : ssrLooseEqual(filters.value.per_page, "50")) ? " selected" : ""}${_scopeId}>50</option><option value="100"${ssrIncludeBooleanAttr(Array.isArray(filters.value.per_page) ? ssrLooseContain(filters.value.per_page, "100") : ssrLooseEqual(filters.value.per_page, "100")) ? " selected" : ""}${_scopeId}>100</option><option value="250"${ssrIncludeBooleanAttr(Array.isArray(filters.value.per_page) ? ssrLooseContain(filters.value.per_page, "250") : ssrLooseEqual(filters.value.per_page, "250")) ? " selected" : ""}${_scopeId}>250</option></select><div class="flex items-center justify-center gap-2"${_scopeId}><button type="button" class="px-3 py-1 rounded flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700"${_scopeId}><svg class="shrink-0 h-3 w-3" viewBox="0 0 512 512"${_scopeId}><path class="fill-current text-white" d="M487.976 0H24.028C2.71 0-8.047 25.866 7.058 40.971L192 225.941V432c0 7.831 3.821 15.17 10.237 19.662l80 55.98C298.02 518.69 320 507.493 320 487.98V225.941l184.947-184.97C520.021 25.896 509.338 0 487.976 0z"${_scopeId}></path></svg><span class="text-sm font-semibold text-white"${_scopeId}>${ssrInterpolate(unref(t)("filter"))}</span></button><button type="button" class="px-3 py-1 rounded flex items-center justify-center gap-2 border border-gray-400 dark:border-gray-500 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-900"${_scopeId}><svg class="shrink-0 h-4 w-4" viewBox="0 0 352 512"${_scopeId}><path class="fill-current text-slate-700 dark:text-slate-300" d="M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"${_scopeId}></path></svg><span class="text-sm font-semibold text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(unref(t)("resetFilter"))}</span></button></div><select class="${ssrRenderClass(selectClass)}"${_scopeId}><option value="desc"${ssrIncludeBooleanAttr(Array.isArray(filters.value.sort_direction) ? ssrLooseContain(filters.value.sort_direction, "desc") : ssrLooseEqual(filters.value.sort_direction, "desc")) ? " selected" : ""}${_scopeId}>ID ↓</option><option value="asc"${ssrIncludeBooleanAttr(Array.isArray(filters.value.sort_direction) ? ssrLooseContain(filters.value.sort_direction, "asc") : ssrLooseEqual(filters.value.sort_direction, "asc")) ? " selected" : ""}${_scopeId}>ID ↑</option></select></div><div class="overflow-x-auto mb-1"${_scopeId}><div style="${ssrRenderStyle({
              width: `${tableScrollWidth.value}px`,
              height: "1px"
            })}"${_scopeId}></div></div><div class="overflow-x-auto bg-white dark:bg-slate-800 border rounded"${_scopeId}><table class="min-w-full text-xs border border-gray-400 whitespace-nowrap"${_scopeId}><thead class="bg-gray-100 dark:bg-slate-900 text-gray-700 dark:text-gray-200"${_scopeId}><tr${_scopeId}><th class="px-2 py-2 text-left"${_scopeId}>ID</th><th class="px-2 py-2 text-left"${_scopeId}>Дата</th><th class="px-2 py-2 text-left"${_scopeId}>User</th><th class="px-2 py-2 text-left"${_scopeId}>Visitor UUID</th><th class="px-2 py-2 text-left"${_scopeId}>Module</th><th class="px-2 py-2 text-left"${_scopeId}>Event</th><th class="px-2 py-2 text-left"${_scopeId}>Title</th><th class="px-2 py-2 text-left"${_scopeId}>URL</th><th class="px-2 py-2 text-left"${_scopeId}>Time</th><th class="px-2 py-2 text-left"${_scopeId}>Scroll</th><th class="px-2 py-2 text-left"${_scopeId}>Clicks</th><th class="px-2 py-2 text-center"${_scopeId}>Show</th></tr></thead><tbody${_scopeId}><!--[-->`);
            ssrRenderList(props.visitorLogs.data, (log) => {
              _push2(`<tr class="border-t dark:border-slate-700 text-gray-700 dark:text-gray-200"${_scopeId}><td class="px-2 py-1.5"${_scopeId}>${ssrInterpolate(log.id)}</td><td class="px-2 py-1.5"${_scopeId}>${ssrInterpolate(formatDate(log.visited_at))}</td><td class="px-2 py-1.5"${_scopeId}>${ssrInterpolate(log.user_id || "guest")}</td><td class="px-2 py-1.5 max-w-40 truncate"${ssrRenderAttr("title", log.visitor_uuid || "")}${_scopeId}>${ssrInterpolate(log.visitor_uuid || "—")}</td><td class="px-2 py-1.5"${_scopeId}>${ssrInterpolate(log.module || "—")}</td><td class="px-2 py-1.5"${_scopeId}>${ssrInterpolate(log.event_type || "—")}</td><td class="px-2 py-1.5 max-w-xs truncate"${ssrRenderAttr("title", log.page_title || "")}${_scopeId}>${ssrInterpolate(log.page_title || "—")}</td><td class="px-2 py-1.5 max-w-md truncate"${ssrRenderAttr("title", log.url || "")}${_scopeId}>${ssrInterpolate(log.url || "—")}</td><td class="px-2 py-1.5"${_scopeId}>${ssrInterpolate(log.time_on_page ?? "—")}</td><td class="px-2 py-1.5"${_scopeId}>${ssrInterpolate(log.scroll_depth ?? "—")}</td><td class="px-2 py-1.5"${_scopeId}>${ssrInterpolate(log.clicks_count ?? 0)}</td><td class="px-2 py-1.5"${_scopeId}>`);
              _push2(ssrRenderComponent(unref(Link), {
                href: _ctx.route(
                  "admin.analyticsVisitorLogs.show",
                  log.id
                ),
                title: unref(t)("openLink"),
                class: "flex items-center justify-center"
              }, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`<svg class="shrink-0 h-4 w-4" viewBox="0 0 576 512"${_scopeId2}><path class="fill-current text-blue-600 dark:text-blue-400" d="M576 24v127.984c0 21.461-25.96 31.98-40.971 16.971l-35.707-35.709-243.523 243.523c-9.373 9.373-24.568 9.373-33.941 0l-22.627-22.627c-9.373-9.373-9.373-24.569 0-33.941L442.756 76.676l-35.703-35.705C391.982 25.9 402.656 0 424.024 0H552c13.255 0 24 10.745 24 24zM407.029 270.794l-16 16A23.999 23.999 0 0 0 384 303.765V448H64V128h264a24.003 24.003 0 0 0 16.97-7.029l16-16C376.089 89.851 365.381 64 344 64H48C21.49 64 0 85.49 0 112v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V287.764c0-21.382-25.852-32.09-40.971-16.97z"${_scopeId2}></path></svg>`);
                  } else {
                    return [
                      (openBlock(), createBlock("svg", {
                        class: "shrink-0 h-4 w-4",
                        viewBox: "0 0 576 512"
                      }, [
                        createVNode("path", {
                          class: "fill-current text-blue-600 dark:text-blue-400",
                          d: "M576 24v127.984c0 21.461-25.96 31.98-40.971 16.971l-35.707-35.709-243.523 243.523c-9.373 9.373-24.568 9.373-33.941 0l-22.627-22.627c-9.373-9.373-9.373-24.569 0-33.941L442.756 76.676l-35.703-35.705C391.982 25.9 402.656 0 424.024 0H552c13.255 0 24 10.745 24 24zM407.029 270.794l-16 16A23.999 23.999 0 0 0 384 303.765V448H64V128h264a24.003 24.003 0 0 0 16.97-7.029l16-16C376.089 89.851 365.381 64 344 64H48C21.49 64 0 85.49 0 112v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V287.764c0-21.382-25.852-32.09-40.971-16.97z"
                        })
                      ]))
                    ];
                  }
                }),
                _: 2
              }, _parent2, _scopeId));
              _push2(`</td></tr>`);
            });
            _push2(`<!--]-->`);
            if (!props.visitorLogs.data || !props.visitorLogs.data.length) {
              _push2(`<tr${_scopeId}><td colspan="12" class="px-2 py-6 text-center text-sm text-gray-600 dark:text-gray-400"${_scopeId}>${ssrInterpolate(unref(t)("noData"))}</td></tr>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</tbody></table></div>`);
            if (props.visitorLogs.meta) {
              _push2(`<div class="flex flex-col xl:flex-row items-center justify-between gap-2 mt-3"${_scopeId}><div class="text-sm font-semibold text-gray-600 dark:text-gray-300"${_scopeId}>${ssrInterpolate(props.visitorLogs.meta.from || 0)} – ${ssrInterpolate(props.visitorLogs.meta.to || 0)} из ${ssrInterpolate(props.visitorLogs.meta.total || 0)}</div><div class="flex items-center justify-center flex-wrap gap-2"${_scopeId}><button type="button"${ssrIncludeBooleanAttr(currentPage.value <= 1) ? " disabled" : ""} class="${ssrRenderClass([
                currentPage.value <= 1 ? "text-slate-400 dark:text-slate-500 cursor-not-allowed" : "bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-800 dark:hover:bg-cyan-900 text-white",
                "px-3 py-1 text-sm font-semibold border rounded"
              ])}"${_scopeId}> ← ${ssrInterpolate(unref(t)("previous"))}</button><div class="flex items-center gap-2"${_scopeId}><input${ssrRenderAttr("value", pageInput.value)} type="number" min="1"${ssrRenderAttr("max", lastPage.value)} class="w-20 px-2 py-1 text-center border border-slate-500 font-semibold text-sm rounded-sm focus:border-indigo-500 focus:ring-indigo-300 dark:bg-cyan-800 dark:text-slate-100"${_scopeId}><span class="text-sm font-semibold text-gray-700 dark:text-gray-200"${_scopeId}>${ssrInterpolate(unref(t)("of"))} ${ssrInterpolate(lastPage.value)}</span></div><button type="button"${ssrIncludeBooleanAttr(currentPage.value >= lastPage.value) ? " disabled" : ""} class="${ssrRenderClass([
                currentPage.value >= lastPage.value ? "text-slate-400 dark:text-slate-500 cursor-not-allowed" : "bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-800 dark:hover:bg-cyan-900 text-white",
                "px-3 py-1 text-sm font-semibold border rounded"
              ])}"${_scopeId}>${ssrInterpolate(unref(t)("next"))} → </button></div><div class="text-sm font-semibold text-gray-600 dark:text-gray-300"${_scopeId}>${ssrInterpolate(currentPage.value)} / ${ssrInterpolate(lastPage.value)}</div></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "flex flex-col lg:flex-row items-center justify-between gap-3 pb-4" }, [
                    createVNode("div", { class: "flex items-center justify-start flex-wrap gap-3" }, [
                      createVNode("button", {
                        type: "button",
                        onClick: importAnalytics,
                        class: "px-3 py-1 rounded flex items-center justify-center gap-2 bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-700 dark:hover:bg-cyan-800"
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "shrink-0 h-3 w-3",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("path", {
                            class: "fill-current text-white",
                            d: "M14,0H3A1,1,0,0,0,2,1V23a1,1,0,0,0,1,1H21a1,1,0,0,0,1-1V8H15a1,1,0,0,1-1-1ZM5.5,17h13a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,5.5,17Zm0-5h13a.5.5,0,0,1,.5.5v1a.5.5,0,0,1-.5.5H5.5a.5.5,0,0,1-.5-.5v-1A.5.5,0,0,1,5.5,12Zm5-3h-5A.5.5,0,0,1,5,8.5v-1A.5.5,0,0,1,5.5,7h5a.5.5,0,0,1,.5.5v1A.5.5,0,0,1,10.5,9Z"
                          }),
                          createVNode("polygon", {
                            class: "fill-current text-white",
                            points: "21.414 6 16 6 16 0.586 21.414 6"
                          })
                        ])),
                        createVNode("span", { class: "text-sm font-semibold text-white" }, toDisplayString(unref(t)("import")), 1)
                      ]),
                      createVNode("button", {
                        type: "button",
                        onClick: cleanupAnalytics,
                        class: "px-3 py-1 rounded flex items-center justify-center gap-2 bg-rose-500 hover:bg-rose-800"
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "shrink-0 h-3 w-3",
                          viewBox: "0 0 512 512"
                        }, [
                          createVNode("path", {
                            class: "fill-current text-white",
                            d: "M504 255.531c.253 136.64-111.18 248.372-247.82 248.468-59.015.042-113.223-20.53-155.822-54.911-11.077-8.94-11.905-25.541-1.839-35.607l11.267-11.267c8.609-8.609 22.353-9.551 31.891-1.984C173.062 425.135 212.781 440 256 440c101.705 0 184-82.311 184-184 0-101.705-82.311-184-184-184-48.814 0-93.149 18.969-126.068 49.932l50.754 50.754c10.08 10.08 2.941 27.314-11.313 27.314H24c-8.837 0-16-7.163-16-16V38.627c0-14.254 17.234-21.393 27.314-11.314l49.372 49.372C129.209 34.136 189.552 8 256 8c136.81 0 247.747 110.78 248 247.531zm-180.912 78.784l9.823-12.63c8.138-10.463 6.253-25.542-4.21-33.679L288 256.349V152c0-13.255-10.745-24-24-24h-16c-13.255 0-24 10.745-24 24v135.651l65.409 50.874c10.463 8.137 25.541 6.253 33.679-4.21z"
                          })
                        ])),
                        createVNode("span", { class: "text-sm font-semibold text-white" }, toDisplayString(unref(t)("clear")) + " " + toDisplayString(unref(t)("period")), 1)
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center flex-wrap gap-2" }, [
                      createVNode("button", {
                        type: "button",
                        onClick: ($event) => downloadAnalytics("csv"),
                        class: "px-3 py-1 rounded text-sm font-semibold text-white bg-purple-600 hover:bg-purple-700"
                      }, " CSV ", 8, ["onClick"]),
                      createVNode("button", {
                        type: "button",
                        onClick: ($event) => downloadAnalytics("xls"),
                        class: "px-3 py-1 rounded text-sm font-semibold text-white bg-green-600 hover:bg-green-700"
                      }, " Excel ", 8, ["onClick"]),
                      createVNode("button", {
                        type: "button",
                        onClick: ($event) => downloadAnalytics("docx"),
                        class: "px-3 py-1 rounded text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700"
                      }, " Word ", 8, ["onClick"]),
                      createVNode("button", {
                        type: "button",
                        onClick: ($event) => downloadAnalytics("pdf"),
                        class: "px-3 py-1 rounded text-sm font-semibold text-white bg-red-600 hover:bg-red-700"
                      }, " PDF ", 8, ["onClick"]),
                      createVNode("button", {
                        type: "button",
                        onClick: ($event) => downloadAnalytics("zip"),
                        class: "px-3 py-1 rounded text-sm font-semibold text-white bg-slate-600 hover:bg-slate-700"
                      }, " ZIP ", 8, ["onClick"])
                    ])
                  ]),
                  createVNode("div", { class: "grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-10 ] gap-2 mb-3" }, [
                    withDirectives(createVNode("input", {
                      "onUpdate:modelValue": ($event) => filters.value.date_from = $event,
                      type: "date",
                      class: [inputClass, "xl:col-span-2"]
                    }, null, 10, ["onUpdate:modelValue"]), [
                      [vModelText, filters.value.date_from]
                    ]),
                    withDirectives(createVNode("input", {
                      "onUpdate:modelValue": ($event) => filters.value.date_to = $event,
                      type: "date",
                      class: [inputClass, "xl:col-span-2"]
                    }, null, 10, ["onUpdate:modelValue"]), [
                      [vModelText, filters.value.date_to]
                    ]),
                    withDirectives(createVNode("input", {
                      "onUpdate:modelValue": ($event) => filters.value.module = $event,
                      type: "text",
                      placeholder: "module",
                      class: inputClass
                    }, null, 8, ["onUpdate:modelValue"]), [
                      [vModelText, filters.value.module]
                    ]),
                    withDirectives(createVNode("input", {
                      "onUpdate:modelValue": ($event) => filters.value.event_type = $event,
                      type: "text",
                      placeholder: "event_type",
                      class: inputClass
                    }, null, 8, ["onUpdate:modelValue"]), [
                      [vModelText, filters.value.event_type]
                    ]),
                    withDirectives(createVNode("input", {
                      "onUpdate:modelValue": ($event) => filters.value.user_id = $event,
                      type: "number",
                      min: "1",
                      placeholder: "user_id",
                      class: inputClass
                    }, null, 8, ["onUpdate:modelValue"]), [
                      [vModelText, filters.value.user_id]
                    ]),
                    withDirectives(createVNode("input", {
                      "onUpdate:modelValue": ($event) => filters.value.visitor_uuid = $event,
                      type: "text",
                      placeholder: "visitor_uuid",
                      class: [inputClass, "xl:col-span-2"]
                    }, null, 10, ["onUpdate:modelValue"]), [
                      [vModelText, filters.value.visitor_uuid]
                    ]),
                    withDirectives(createVNode("input", {
                      "onUpdate:modelValue": ($event) => filters.value.url = $event,
                      type: "text",
                      placeholder: "url",
                      class: inputClass
                    }, null, 8, ["onUpdate:modelValue"]), [
                      [vModelText, filters.value.url]
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col md:flex-row items-center justify-between gap-2 mb-3" }, [
                    withDirectives(createVNode("select", {
                      "onUpdate:modelValue": ($event) => filters.value.per_page = $event,
                      onChange: applyFilters,
                      class: selectClass
                    }, [
                      createVNode("option", { value: "10" }, "10"),
                      createVNode("option", { value: "25" }, "25"),
                      createVNode("option", { value: "50" }, "50"),
                      createVNode("option", { value: "100" }, "100"),
                      createVNode("option", { value: "250" }, "250")
                    ], 40, ["onUpdate:modelValue"]), [
                      [vModelSelect, filters.value.per_page]
                    ]),
                    createVNode("div", { class: "flex items-center justify-center gap-2" }, [
                      createVNode("button", {
                        type: "button",
                        onClick: applyFilters,
                        class: "px-3 py-1 rounded flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700"
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "shrink-0 h-3 w-3",
                          viewBox: "0 0 512 512"
                        }, [
                          createVNode("path", {
                            class: "fill-current text-white",
                            d: "M487.976 0H24.028C2.71 0-8.047 25.866 7.058 40.971L192 225.941V432c0 7.831 3.821 15.17 10.237 19.662l80 55.98C298.02 518.69 320 507.493 320 487.98V225.941l184.947-184.97C520.021 25.896 509.338 0 487.976 0z"
                          })
                        ])),
                        createVNode("span", { class: "text-sm font-semibold text-white" }, toDisplayString(unref(t)("filter")), 1)
                      ]),
                      createVNode("button", {
                        type: "button",
                        onClick: resetFilters,
                        class: "px-3 py-1 rounded flex items-center justify-center gap-2 border border-gray-400 dark:border-gray-500 bg-slate-200 hover:bg-slate-300 dark:bg-slate-800 dark:hover:bg-slate-900"
                      }, [
                        (openBlock(), createBlock("svg", {
                          class: "shrink-0 h-4 w-4",
                          viewBox: "0 0 352 512"
                        }, [
                          createVNode("path", {
                            class: "fill-current text-slate-700 dark:text-slate-300",
                            d: "M242.72 256l100.07-100.07c12.28-12.28 12.28-32.19 0-44.48l-22.24-22.24c-12.28-12.28-32.19-12.28-44.48 0L176 189.28 75.93 89.21c-12.28-12.28-32.19-12.28-44.48 0L9.21 111.45c-12.28 12.28-12.28 32.19 0 44.48L109.28 256 9.21 356.07c-12.28 12.28-12.28 32.19 0 44.48l22.24 22.24c12.28 12.28 32.2 12.28 44.48 0L176 322.72l100.07 100.07c12.28 12.28 32.2 12.28 44.48 0l22.24-22.24c12.28-12.28 12.28-32.19 0-44.48L242.72 256z"
                          })
                        ])),
                        createVNode("span", { class: "text-sm font-semibold text-gray-700 dark:text-gray-300" }, toDisplayString(unref(t)("resetFilter")), 1)
                      ])
                    ]),
                    withDirectives(createVNode("select", {
                      "onUpdate:modelValue": ($event) => filters.value.sort_direction = $event,
                      onChange: applyFilters,
                      class: selectClass
                    }, [
                      createVNode("option", { value: "desc" }, "ID ↓"),
                      createVNode("option", { value: "asc" }, "ID ↑")
                    ], 40, ["onUpdate:modelValue"]), [
                      [vModelSelect, filters.value.sort_direction]
                    ])
                  ]),
                  createVNode("div", {
                    ref_key: "topScroll",
                    ref: topScroll,
                    class: "overflow-x-auto mb-1",
                    onScroll: syncTableScroll
                  }, [
                    createVNode("div", {
                      style: {
                        width: `${tableScrollWidth.value}px`,
                        height: "1px"
                      }
                    }, null, 4)
                  ], 544),
                  createVNode("div", {
                    ref_key: "tableScroll",
                    ref: tableScroll,
                    class: "overflow-x-auto bg-white dark:bg-slate-800 border rounded",
                    onScroll: syncTopScroll
                  }, [
                    createVNode("table", {
                      ref_key: "tableContent",
                      ref: tableContent,
                      class: "min-w-full text-xs border border-gray-400 whitespace-nowrap"
                    }, [
                      createVNode("thead", { class: "bg-gray-100 dark:bg-slate-900 text-gray-700 dark:text-gray-200" }, [
                        createVNode("tr", null, [
                          createVNode("th", { class: "px-2 py-2 text-left" }, "ID"),
                          createVNode("th", { class: "px-2 py-2 text-left" }, "Дата"),
                          createVNode("th", { class: "px-2 py-2 text-left" }, "User"),
                          createVNode("th", { class: "px-2 py-2 text-left" }, "Visitor UUID"),
                          createVNode("th", { class: "px-2 py-2 text-left" }, "Module"),
                          createVNode("th", { class: "px-2 py-2 text-left" }, "Event"),
                          createVNode("th", { class: "px-2 py-2 text-left" }, "Title"),
                          createVNode("th", { class: "px-2 py-2 text-left" }, "URL"),
                          createVNode("th", { class: "px-2 py-2 text-left" }, "Time"),
                          createVNode("th", { class: "px-2 py-2 text-left" }, "Scroll"),
                          createVNode("th", { class: "px-2 py-2 text-left" }, "Clicks"),
                          createVNode("th", { class: "px-2 py-2 text-center" }, "Show")
                        ])
                      ]),
                      createVNode("tbody", null, [
                        (openBlock(true), createBlock(Fragment, null, renderList(props.visitorLogs.data, (log) => {
                          return openBlock(), createBlock("tr", {
                            key: log.id,
                            class: "border-t dark:border-slate-700 text-gray-700 dark:text-gray-200"
                          }, [
                            createVNode("td", { class: "px-2 py-1.5" }, toDisplayString(log.id), 1),
                            createVNode("td", { class: "px-2 py-1.5" }, toDisplayString(formatDate(log.visited_at)), 1),
                            createVNode("td", { class: "px-2 py-1.5" }, toDisplayString(log.user_id || "guest"), 1),
                            createVNode("td", {
                              class: "px-2 py-1.5 max-w-40 truncate",
                              title: log.visitor_uuid || ""
                            }, toDisplayString(log.visitor_uuid || "—"), 9, ["title"]),
                            createVNode("td", { class: "px-2 py-1.5" }, toDisplayString(log.module || "—"), 1),
                            createVNode("td", { class: "px-2 py-1.5" }, toDisplayString(log.event_type || "—"), 1),
                            createVNode("td", {
                              class: "px-2 py-1.5 max-w-xs truncate",
                              title: log.page_title || ""
                            }, toDisplayString(log.page_title || "—"), 9, ["title"]),
                            createVNode("td", {
                              class: "px-2 py-1.5 max-w-md truncate",
                              title: log.url || ""
                            }, toDisplayString(log.url || "—"), 9, ["title"]),
                            createVNode("td", { class: "px-2 py-1.5" }, toDisplayString(log.time_on_page ?? "—"), 1),
                            createVNode("td", { class: "px-2 py-1.5" }, toDisplayString(log.scroll_depth ?? "—"), 1),
                            createVNode("td", { class: "px-2 py-1.5" }, toDisplayString(log.clicks_count ?? 0), 1),
                            createVNode("td", { class: "px-2 py-1.5" }, [
                              createVNode(unref(Link), {
                                href: _ctx.route(
                                  "admin.analyticsVisitorLogs.show",
                                  log.id
                                ),
                                title: unref(t)("openLink"),
                                class: "flex items-center justify-center"
                              }, {
                                default: withCtx(() => [
                                  (openBlock(), createBlock("svg", {
                                    class: "shrink-0 h-4 w-4",
                                    viewBox: "0 0 576 512"
                                  }, [
                                    createVNode("path", {
                                      class: "fill-current text-blue-600 dark:text-blue-400",
                                      d: "M576 24v127.984c0 21.461-25.96 31.98-40.971 16.971l-35.707-35.709-243.523 243.523c-9.373 9.373-24.568 9.373-33.941 0l-22.627-22.627c-9.373-9.373-9.373-24.569 0-33.941L442.756 76.676l-35.703-35.705C391.982 25.9 402.656 0 424.024 0H552c13.255 0 24 10.745 24 24zM407.029 270.794l-16 16A23.999 23.999 0 0 0 384 303.765V448H64V128h264a24.003 24.003 0 0 0 16.97-7.029l16-16C376.089 89.851 365.381 64 344 64H48C21.49 64 0 85.49 0 112v352c0 26.51 21.49 48 48 48h352c26.51 0 48-21.49 48-48V287.764c0-21.382-25.852-32.09-40.971-16.97z"
                                    })
                                  ]))
                                ]),
                                _: 2
                              }, 1032, ["href", "title"])
                            ])
                          ]);
                        }), 128)),
                        !props.visitorLogs.data || !props.visitorLogs.data.length ? (openBlock(), createBlock("tr", { key: 0 }, [
                          createVNode("td", {
                            colspan: "12",
                            class: "px-2 py-6 text-center text-sm text-gray-600 dark:text-gray-400"
                          }, toDisplayString(unref(t)("noData")), 1)
                        ])) : createCommentVNode("", true)
                      ])
                    ], 512)
                  ], 544),
                  props.visitorLogs.meta ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "flex flex-col xl:flex-row items-center justify-between gap-2 mt-3"
                  }, [
                    createVNode("div", { class: "text-sm font-semibold text-gray-600 dark:text-gray-300" }, toDisplayString(props.visitorLogs.meta.from || 0) + " – " + toDisplayString(props.visitorLogs.meta.to || 0) + " из " + toDisplayString(props.visitorLogs.meta.total || 0), 1),
                    createVNode("div", { class: "flex items-center justify-center flex-wrap gap-2" }, [
                      createVNode("button", {
                        type: "button",
                        disabled: currentPage.value <= 1,
                        onClick: previousPage,
                        class: [
                          "px-3 py-1 text-sm font-semibold border rounded",
                          currentPage.value <= 1 ? "text-slate-400 dark:text-slate-500 cursor-not-allowed" : "bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-800 dark:hover:bg-cyan-900 text-white"
                        ]
                      }, " ← " + toDisplayString(unref(t)("previous")), 11, ["disabled"]),
                      createVNode("div", { class: "flex items-center gap-2" }, [
                        withDirectives(createVNode("input", {
                          "onUpdate:modelValue": ($event) => pageInput.value = $event,
                          type: "number",
                          min: "1",
                          max: lastPage.value,
                          onChange: ($event) => goToPage(),
                          onKeyup: withKeys(($event) => goToPage(), ["enter"]),
                          class: "w-20 px-2 py-1 text-center border border-slate-500 font-semibold text-sm rounded-sm focus:border-indigo-500 focus:ring-indigo-300 dark:bg-cyan-800 dark:text-slate-100"
                        }, null, 40, ["onUpdate:modelValue", "max", "onChange", "onKeyup"]), [
                          [vModelText, pageInput.value]
                        ]),
                        createVNode("span", { class: "text-sm font-semibold text-gray-700 dark:text-gray-200" }, toDisplayString(unref(t)("of")) + " " + toDisplayString(lastPage.value), 1)
                      ]),
                      createVNode("button", {
                        type: "button",
                        disabled: currentPage.value >= lastPage.value,
                        onClick: nextPage,
                        class: [
                          "px-3 py-1 text-sm font-semibold border rounded",
                          currentPage.value >= lastPage.value ? "text-slate-400 dark:text-slate-500 cursor-not-allowed" : "bg-cyan-600 hover:bg-cyan-700 dark:bg-cyan-800 dark:hover:bg-cyan-900 text-white"
                        ]
                      }, toDisplayString(unref(t)("next")) + " → ", 11, ["disabled"])
                    ]),
                    createVNode("div", { class: "text-sm font-semibold text-gray-600 dark:text-gray-300" }, toDisplayString(currentPage.value) + " / " + toDisplayString(lastPage.value), 1)
                  ])) : createCommentVNode("", true)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/Analytics/AnalyticsVisitorLog/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
