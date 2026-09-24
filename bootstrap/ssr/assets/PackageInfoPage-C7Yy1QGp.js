import { ref, computed, mergeProps, withCtx, createVNode, createBlock, createCommentVNode, openBlock, createTextVNode, toDisplayString, withDirectives, vModelText, Fragment, renderList, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderAttr, ssrRenderList } from "vue/server-renderer";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import "@inertiajs/vue3";
import "vue-toastification";
import "./LocaleSelectOption-BeLdazeX.js";
import "vue-i18n";
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
const _sfc_main = {
  __name: "PackageInfoPage",
  __ssrInlineRender: true,
  props: {
    packageData: {
      type: Object,
      default: () => ({})
    }
  },
  setup(__props) {
    const props = __props;
    const search = ref("");
    const copiedValue = ref("");
    const mainInfoKeys = [
      "name",
      "version",
      "description",
      "private",
      "type",
      "main",
      "module",
      "license",
      "author",
      "homepage"
    ];
    const reservedSections = [
      ...mainInfoKeys,
      "scripts",
      "dependencies",
      "devDependencies",
      "peerDependencies",
      "optionalDependencies"
    ];
    const normalizedSearch = computed(() => {
      return search.value.trim().toLowerCase();
    });
    const mainInfo = computed(() => {
      return mainInfoKeys.filter((key) => props.packageData[key] !== void 0).map((key) => ({
        key,
        value: props.packageData[key]
      }));
    });
    const scripts = computed(() => {
      return filterEntries(
        props.packageData.scripts || {}
      );
    });
    const dependencies = computed(() => {
      return filterEntries(
        props.packageData.dependencies || {}
      );
    });
    const devDependencies = computed(() => {
      return filterEntries(
        props.packageData.devDependencies || {}
      );
    });
    const peerDependencies = computed(() => {
      return filterEntries(
        props.packageData.peerDependencies || {}
      );
    });
    const optionalDependencies = computed(() => {
      return filterEntries(
        props.packageData.optionalDependencies || {}
      );
    });
    const otherSections = computed(() => {
      return Object.entries(props.packageData).filter(([key]) => !reservedSections.includes(key)).filter(([key, value]) => {
        const query = normalizedSearch.value;
        if (!query) {
          return true;
        }
        return String(key).toLowerCase().includes(query) || JSON.stringify(value).toLowerCase().includes(query);
      }).map(([key, value]) => ({
        key,
        value
      }));
    });
    const totalDependencies = computed(() => {
      return Object.keys(
        props.packageData.dependencies || {}
      ).length;
    });
    const totalDevDependencies = computed(() => {
      return Object.keys(
        props.packageData.devDependencies || {}
      ).length;
    });
    const totalPeerDependencies = computed(() => {
      return Object.keys(
        props.packageData.peerDependencies || {}
      ).length;
    });
    const totalOptionalDependencies = computed(() => {
      return Object.keys(
        props.packageData.optionalDependencies || {}
      ).length;
    });
    const totalPackages = computed(() => {
      return totalDependencies.value + totalDevDependencies.value + totalPeerDependencies.value + totalOptionalDependencies.value;
    });
    const totalScripts = computed(() => {
      return Object.keys(
        props.packageData.scripts || {}
      ).length;
    });
    function filterEntries(entries) {
      const query = normalizedSearch.value;
      return Object.entries(entries).filter(([name, value]) => {
        if (!query) {
          return true;
        }
        return String(name).toLowerCase().includes(query) || String(value).toLowerCase().includes(query);
      }).map(([name, value]) => ({
        name,
        value
      }));
    }
    const isObject = (value) => {
      return value !== null && typeof value === "object";
    };
    const stringifyValue = (value) => {
      return JSON.stringify(
        value,
        null,
        2
      );
    };
    const displayValue = (value) => {
      if (typeof value === "boolean") {
        return value ? "true" : "false";
      }
      if (value === null) {
        return "null";
      }
      return value;
    };
    const copyToClipboard = async (value, key) => {
      try {
        const text = isObject(value) ? stringifyValue(value) : String(value ?? "");
        await navigator.clipboard.writeText(text);
        copiedValue.value = key;
        setTimeout(() => {
          if (copiedValue.value === key) {
            copiedValue.value = "";
          }
        }, 1500);
      } catch (error) {
        console.error(
          "Failed to copy package.json value:",
          error
        );
      }
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({ title: "package.json" }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` package.json `);
                } else {
                  return [
                    createTextVNode(" package.json ")
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(" package.json ")
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="mb-4 px-4 py-3 border border-cyan-300 dark:border-cyan-700 bg-cyan-50 dark:bg-cyan-900/20 rounded-md"${_scopeId}><div class="flex items-start gap-3"${_scopeId}><div class="shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-cyan-100 dark:bg-cyan-800 text-cyan-700 dark:text-cyan-200"${_scopeId}><svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"${_scopeId}><path d="M12 2 2 7v10l10 5 10-5V7L12 2zm0 2.18
                                       7.74 3.87L12 11.92 4.26 8.05 12 4.18zM4
                                       9.62l7 3.5v6.7l-7-3.5v-6.7zm9 10.2v-6.7
                                       l7-3.5v6.7l-7 3.5z"${_scopeId}></path></svg></div><div${_scopeId}><div class="text-sm font-semibold text-cyan-800 dark:text-cyan-200"${_scopeId}> Конфигурация package.json </div><div class="mt-0.5 text-xs text-cyan-700 dark:text-cyan-300"${_scopeId}> Информация загружена непосредственно из <span class="font-mono font-semibold"${_scopeId}> package.json </span>. Страница предназначена только для просмотра. </div></div></div></div><div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-4"${_scopeId}><div class="p-3 border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-900"${_scopeId}><div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400"${_scopeId}> Packages </div><div class="mt-1 text-xl font-semibold text-indigo-700 dark:text-indigo-300"${_scopeId}>${ssrInterpolate(totalPackages.value)}</div></div><div class="p-3 border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-900"${_scopeId}><div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400"${_scopeId}> Scripts </div><div class="mt-1 text-xl font-semibold text-sky-700 dark:text-sky-300"${_scopeId}>${ssrInterpolate(totalScripts.value)}</div></div><div class="p-3 border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-900"${_scopeId}><div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400"${_scopeId}> Dependencies </div><div class="mt-1 text-xl font-semibold text-emerald-700 dark:text-emerald-300"${_scopeId}>${ssrInterpolate(totalDependencies.value)}</div></div><div class="p-3 border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-900"${_scopeId}><div class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400"${_scopeId}> Dev Dependencies </div><div class="mt-1 text-xl font-semibold text-amber-700 dark:text-amber-300"${_scopeId}>${ssrInterpolate(totalDevDependencies.value)}</div></div></div><div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4"${_scopeId}><div class="text-sm text-slate-600 dark:text-slate-300"${_scopeId}> Поиск по пакетам, версиям, scripts и секциям </div><div class="relative w-full sm:w-96"${_scopeId}><svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 fill-current text-slate-400" viewBox="0 0 24 24"${_scopeId}><path d="M9.5 3a6.5 6.5 0 1 0 3.98 11.64L19.85
                                   21 21 19.85l-6.36-6.37A6.5 6.5 0 0 0
                                   9.5 3zm0 2a4.5 4.5 0 1 1 0 9 4.5 4.5
                                   0 0 1 0-9z"${_scopeId}></path></svg><input${ssrRenderAttr("value", search.value)} type="text" placeholder="vue, vite, build, axios..." class="w-full pl-8 pr-8 py-1.5 text-sm font-mono border border-slate-300 dark:border-slate-500 rounded-md bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500"${_scopeId}>`);
            if (search.value) {
              _push2(`<button type="button" title="Очистить поиск" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition"${_scopeId}> × </button>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            if (mainInfo.value.length) {
              _push2(`<div class="mb-4"${_scopeId}><div class="mb-2 text-sm font-semibold text-indigo-700 dark:text-indigo-300"${_scopeId}> Основная информация </div><div class="overflow-hidden border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-gray-900"${_scopeId}><table class="w-full text-left border-collapse text-sm"${_scopeId}><tbody${_scopeId}><!--[-->`);
              ssrRenderList(mainInfo.value, (entry) => {
                _push2(`<tr class="border-b border-slate-200 dark:border-slate-700 last:border-b-0"${_scopeId}><td class="w-1/3 px-3 py-2 font-mono font-semibold text-orange-700 dark:text-orange-300 align-top"${_scopeId}>${ssrInterpolate(entry.key)}</td><td class="px-3 py-2 font-mono text-slate-800 dark:text-slate-100 break-all"${_scopeId}>`);
                if (isObject(entry.value)) {
                  _push2(`<pre class="m-0 whitespace-pre-wrap break-words font-mono text-xs"${_scopeId}>${ssrInterpolate(stringifyValue(entry.value))}</pre>`);
                } else {
                  _push2(`<!--[-->${ssrInterpolate(displayValue(entry.value))}<!--]-->`);
                }
                _push2(`</td></tr>`);
              });
              _push2(`<!--]--></tbody></table></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (scripts.value.length || __props.packageData.scripts) {
              _push2(`<div class="mb-4"${_scopeId}><div class="flex items-center justify-between mb-2"${_scopeId}><div class="text-sm font-semibold text-sky-700 dark:text-sky-300"${_scopeId}> scripts </div><div class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(scripts.value.length)}</div></div><div class="overflow-x-auto border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-gray-900"${_scopeId}><table class="w-full text-left border-collapse text-sm"${_scopeId}><thead class="bg-slate-100 dark:bg-slate-800"${_scopeId}><tr${_scopeId}><th class="w-52 px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-xs font-semibold text-slate-600 dark:text-slate-300"${_scopeId}> Script </th><th class="px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-xs font-semibold text-slate-600 dark:text-slate-300"${_scopeId}> Command </th></tr></thead><tbody${_scopeId}><!--[-->`);
              ssrRenderList(scripts.value, (item) => {
                _push2(`<tr class="border-b border-slate-200 dark:border-slate-700 last:border-b-0 hover:bg-sky-50 dark:hover:bg-slate-800/80"${_scopeId}><td class="px-3 py-1.5 font-mono font-semibold text-sky-700 dark:text-sky-300 align-top"${_scopeId}>${ssrInterpolate(item.name)}</td><td class="px-3 py-1.5 font-mono text-slate-800 dark:text-slate-100 break-all"${_scopeId}>${ssrInterpolate(item.value)}</td></tr>`);
              });
              _push2(`<!--]-->`);
              if (!scripts.value.length) {
                _push2(`<tr${_scopeId}><td colspan="2" class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400"${_scopeId}> Scripts не найдены </td></tr>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</tbody></table></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (dependencies.value.length || __props.packageData.dependencies) {
              _push2(`<div class="mb-4"${_scopeId}><div class="flex items-center justify-between mb-2"${_scopeId}><div class="text-sm font-semibold text-emerald-700 dark:text-emerald-300"${_scopeId}> dependencies </div><div class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(dependencies.value.length)}</div></div><div class="overflow-x-auto border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-gray-900"${_scopeId}><table class="w-full text-left border-collapse text-sm"${_scopeId}><thead class="bg-slate-100 dark:bg-slate-800"${_scopeId}><tr${_scopeId}><th class="px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-xs font-semibold text-slate-600 dark:text-slate-300"${_scopeId}> Package </th><th class="w-52 px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-xs font-semibold text-slate-600 dark:text-slate-300"${_scopeId}> Version </th></tr></thead><tbody${_scopeId}><!--[-->`);
              ssrRenderList(dependencies.value, (item) => {
                _push2(`<tr class="border-b border-slate-200 dark:border-slate-700 last:border-b-0 hover:bg-emerald-50 dark:hover:bg-slate-800/80"${_scopeId}><td class="px-3 py-1.5 font-mono font-semibold text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(item.name)}</td><td class="px-3 py-1.5 font-mono text-emerald-700 dark:text-emerald-300"${_scopeId}>${ssrInterpolate(item.value)}</td></tr>`);
              });
              _push2(`<!--]-->`);
              if (!dependencies.value.length) {
                _push2(`<tr${_scopeId}><td colspan="2" class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400"${_scopeId}> Зависимости не найдены </td></tr>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</tbody></table></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (devDependencies.value.length || __props.packageData.devDependencies) {
              _push2(`<div class="mb-4"${_scopeId}><div class="flex items-center justify-between mb-2"${_scopeId}><div class="text-sm font-semibold text-amber-700 dark:text-amber-300"${_scopeId}> devDependencies </div><div class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(devDependencies.value.length)}</div></div><div class="overflow-x-auto border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-gray-900"${_scopeId}><table class="w-full text-left border-collapse text-sm"${_scopeId}><thead class="bg-slate-100 dark:bg-slate-800"${_scopeId}><tr${_scopeId}><th class="px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-xs font-semibold text-slate-600 dark:text-slate-300"${_scopeId}> Package </th><th class="w-52 px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-xs font-semibold text-slate-600 dark:text-slate-300"${_scopeId}> Version </th></tr></thead><tbody${_scopeId}><!--[-->`);
              ssrRenderList(devDependencies.value, (item) => {
                _push2(`<tr class="border-b border-slate-200 dark:border-slate-700 last:border-b-0 hover:bg-amber-50 dark:hover:bg-slate-800/80"${_scopeId}><td class="px-3 py-1.5 font-mono font-semibold text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(item.name)}</td><td class="px-3 py-1.5 font-mono text-amber-700 dark:text-amber-300"${_scopeId}>${ssrInterpolate(item.value)}</td></tr>`);
              });
              _push2(`<!--]-->`);
              if (!devDependencies.value.length) {
                _push2(`<tr${_scopeId}><td colspan="2" class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400"${_scopeId}> Dev-зависимости не найдены </td></tr>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</tbody></table></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (peerDependencies.value.length || __props.packageData.peerDependencies) {
              _push2(`<div class="mb-4"${_scopeId}><div class="flex items-center justify-between mb-2"${_scopeId}><div class="text-sm font-semibold text-violet-700 dark:text-violet-300"${_scopeId}> peerDependencies </div><div class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(peerDependencies.value.length)}</div></div><div class="overflow-x-auto border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-gray-900"${_scopeId}><table class="w-full text-left border-collapse text-sm"${_scopeId}><tbody${_scopeId}><!--[-->`);
              ssrRenderList(peerDependencies.value, (item) => {
                _push2(`<tr class="border-b border-slate-200 dark:border-slate-700 last:border-b-0"${_scopeId}><td class="px-3 py-1.5 font-mono font-semibold text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(item.name)}</td><td class="w-52 px-3 py-1.5 font-mono text-violet-700 dark:text-violet-300"${_scopeId}>${ssrInterpolate(item.value)}</td></tr>`);
              });
              _push2(`<!--]-->`);
              if (!peerDependencies.value.length) {
                _push2(`<tr${_scopeId}><td colspan="2" class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400"${_scopeId}> Peer-зависимости не найдены </td></tr>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</tbody></table></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (optionalDependencies.value.length || __props.packageData.optionalDependencies) {
              _push2(`<div class="mb-4"${_scopeId}><div class="flex items-center justify-between mb-2"${_scopeId}><div class="text-sm font-semibold text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}> optionalDependencies </div><div class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}>${ssrInterpolate(optionalDependencies.value.length)}</div></div><div class="overflow-x-auto border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-gray-900"${_scopeId}><table class="w-full text-left border-collapse text-sm"${_scopeId}><tbody${_scopeId}><!--[-->`);
              ssrRenderList(optionalDependencies.value, (item) => {
                _push2(`<tr class="border-b border-slate-200 dark:border-slate-700 last:border-b-0"${_scopeId}><td class="px-3 py-1.5 font-mono font-semibold text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(item.name)}</td><td class="w-52 px-3 py-1.5 font-mono text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}>${ssrInterpolate(item.value)}</td></tr>`);
              });
              _push2(`<!--]-->`);
              if (!optionalDependencies.value.length) {
                _push2(`<tr${_scopeId}><td colspan="2" class="px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400"${_scopeId}> Optional-зависимости не найдены </td></tr>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</tbody></table></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (otherSections.value.length) {
              _push2(`<div class="space-y-3"${_scopeId}><!--[-->`);
              ssrRenderList(otherSections.value, (section) => {
                _push2(`<div class="border border-slate-300 dark:border-slate-600 rounded-md overflow-hidden bg-white dark:bg-gray-900"${_scopeId}><div class="flex items-center justify-between px-3 py-2 bg-slate-100 dark:bg-slate-800 border-b border-slate-300 dark:border-slate-600"${_scopeId}><div class="font-mono text-sm font-semibold text-orange-700 dark:text-orange-300"${_scopeId}>${ssrInterpolate(section.key)}</div><button type="button"${ssrRenderAttr(
                  "title",
                  copiedValue.value === section.key ? "Скопировано" : "Копировать"
                )} class="w-7 h-7 inline-flex items-center justify-center rounded text-slate-400 hover:text-orange-600 hover:bg-orange-100 dark:hover:text-orange-300 dark:hover:bg-slate-700 transition"${_scopeId}>`);
                if (copiedValue.value !== section.key) {
                  _push2(`<svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"${_scopeId}><path d="M16 1H4c-1.1 0-2 .9-2
                                           2v14h2V3h12V1zm3 4H8c-1.1
                                           0-2 .9-2 2v14c0 1.1.9 2
                                           2 2h11c1.1 0 2-.9
                                           2-2V7c0-1.1-.9-2-2-2zm0
                                           16H8V7h11v14z"${_scopeId}></path></svg>`);
                } else {
                  _push2(`<svg class="w-4 h-4 fill-current text-green-600 dark:text-green-400" viewBox="0 0 24 24"${_scopeId}><path d="M9 16.17 4.83 12l-1.42
                                           1.41L9 19 21 7l-1.41-1.41z"${_scopeId}></path></svg>`);
                }
                _push2(`</button></div><div class="p-3"${_scopeId}>`);
                if (isObject(section.value)) {
                  _push2(`<pre class="m-0 whitespace-pre-wrap break-words font-mono text-xs leading-5 text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(stringifyValue(section.value))}</pre>`);
                } else {
                  _push2(`<div class="font-mono text-sm text-slate-800 dark:text-slate-100 break-all"${_scopeId}>${ssrInterpolate(displayValue(section.value))}</div>`);
                }
                _push2(`</div></div>`);
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (search.value && !scripts.value.length && !dependencies.value.length && !devDependencies.value.length && !peerDependencies.value.length && !optionalDependencies.value.length && !otherSections.value.length) {
              _push2(`<div class="py-8 text-center text-sm text-slate-500 dark:text-slate-400"${_scopeId}> Ничего не найдено </div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="mt-3 text-xs text-slate-500 dark:text-slate-400"${_scopeId}> Конфигурация package.json доступна только для просмотра. Изменение зависимостей и запуск npm-команд через административную панель отключены. </div></div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "mb-4 px-4 py-3 border border-cyan-300 dark:border-cyan-700 bg-cyan-50 dark:bg-cyan-900/20 rounded-md" }, [
                    createVNode("div", { class: "flex items-start gap-3" }, [
                      createVNode("div", { class: "shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-cyan-100 dark:bg-cyan-800 text-cyan-700 dark:text-cyan-200" }, [
                        (openBlock(), createBlock("svg", {
                          class: "w-5 h-5 fill-current",
                          viewBox: "0 0 24 24"
                        }, [
                          createVNode("path", { d: "M12 2 2 7v10l10 5 10-5V7L12 2zm0 2.18\n                                       7.74 3.87L12 11.92 4.26 8.05 12 4.18zM4\n                                       9.62l7 3.5v6.7l-7-3.5v-6.7zm9 10.2v-6.7\n                                       l7-3.5v6.7l-7 3.5z" })
                        ]))
                      ]),
                      createVNode("div", null, [
                        createVNode("div", { class: "text-sm font-semibold text-cyan-800 dark:text-cyan-200" }, " Конфигурация package.json "),
                        createVNode("div", { class: "mt-0.5 text-xs text-cyan-700 dark:text-cyan-300" }, [
                          createTextVNode(" Информация загружена непосредственно из "),
                          createVNode("span", { class: "font-mono font-semibold" }, " package.json "),
                          createTextVNode(". Страница предназначена только для просмотра. ")
                        ])
                      ])
                    ])
                  ]),
                  createVNode("div", { class: "grid grid-cols-1 sm:grid-cols-4 gap-3 mb-4" }, [
                    createVNode("div", { class: "p-3 border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-900" }, [
                      createVNode("div", { class: "text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400" }, " Packages "),
                      createVNode("div", { class: "mt-1 text-xl font-semibold text-indigo-700 dark:text-indigo-300" }, toDisplayString(totalPackages.value), 1)
                    ]),
                    createVNode("div", { class: "p-3 border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-900" }, [
                      createVNode("div", { class: "text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400" }, " Scripts "),
                      createVNode("div", { class: "mt-1 text-xl font-semibold text-sky-700 dark:text-sky-300" }, toDisplayString(totalScripts.value), 1)
                    ]),
                    createVNode("div", { class: "p-3 border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-900" }, [
                      createVNode("div", { class: "text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400" }, " Dependencies "),
                      createVNode("div", { class: "mt-1 text-xl font-semibold text-emerald-700 dark:text-emerald-300" }, toDisplayString(totalDependencies.value), 1)
                    ]),
                    createVNode("div", { class: "p-3 border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-900" }, [
                      createVNode("div", { class: "text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400" }, " Dev Dependencies "),
                      createVNode("div", { class: "mt-1 text-xl font-semibold text-amber-700 dark:text-amber-300" }, toDisplayString(totalDevDependencies.value), 1)
                    ])
                  ]),
                  createVNode("div", { class: "flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4" }, [
                    createVNode("div", { class: "text-sm text-slate-600 dark:text-slate-300" }, " Поиск по пакетам, версиям, scripts и секциям "),
                    createVNode("div", { class: "relative w-full sm:w-96" }, [
                      (openBlock(), createBlock("svg", {
                        class: "absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 fill-current text-slate-400",
                        viewBox: "0 0 24 24"
                      }, [
                        createVNode("path", { d: "M9.5 3a6.5 6.5 0 1 0 3.98 11.64L19.85\n                                   21 21 19.85l-6.36-6.37A6.5 6.5 0 0 0\n                                   9.5 3zm0 2a4.5 4.5 0 1 1 0 9 4.5 4.5\n                                   0 0 1 0-9z" })
                      ])),
                      withDirectives(createVNode("input", {
                        "onUpdate:modelValue": ($event) => search.value = $event,
                        type: "text",
                        placeholder: "vue, vite, build, axios...",
                        class: "w-full pl-8 pr-8 py-1.5 text-sm font-mono border border-slate-300 dark:border-slate-500 rounded-md bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:outline-none focus:ring-1 focus:ring-orange-500 focus:border-orange-500"
                      }, null, 8, ["onUpdate:modelValue"]), [
                        [vModelText, search.value]
                      ]),
                      search.value ? (openBlock(), createBlock("button", {
                        key: 0,
                        type: "button",
                        title: "Очистить поиск",
                        class: "absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-red-500 transition",
                        onClick: ($event) => search.value = ""
                      }, " × ", 8, ["onClick"])) : createCommentVNode("", true)
                    ])
                  ]),
                  mainInfo.value.length ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "mb-4"
                  }, [
                    createVNode("div", { class: "mb-2 text-sm font-semibold text-indigo-700 dark:text-indigo-300" }, " Основная информация "),
                    createVNode("div", { class: "overflow-hidden border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-gray-900" }, [
                      createVNode("table", { class: "w-full text-left border-collapse text-sm" }, [
                        createVNode("tbody", null, [
                          (openBlock(true), createBlock(Fragment, null, renderList(mainInfo.value, (entry) => {
                            return openBlock(), createBlock("tr", {
                              key: entry.key,
                              class: "border-b border-slate-200 dark:border-slate-700 last:border-b-0"
                            }, [
                              createVNode("td", { class: "w-1/3 px-3 py-2 font-mono font-semibold text-orange-700 dark:text-orange-300 align-top" }, toDisplayString(entry.key), 1),
                              createVNode("td", { class: "px-3 py-2 font-mono text-slate-800 dark:text-slate-100 break-all" }, [
                                isObject(entry.value) ? (openBlock(), createBlock("pre", {
                                  key: 0,
                                  class: "m-0 whitespace-pre-wrap break-words font-mono text-xs"
                                }, toDisplayString(stringifyValue(entry.value)), 1)) : (openBlock(), createBlock(Fragment, { key: 1 }, [
                                  createTextVNode(toDisplayString(displayValue(entry.value)), 1)
                                ], 64))
                              ])
                            ]);
                          }), 128))
                        ])
                      ])
                    ])
                  ])) : createCommentVNode("", true),
                  scripts.value.length || __props.packageData.scripts ? (openBlock(), createBlock("div", {
                    key: 1,
                    class: "mb-4"
                  }, [
                    createVNode("div", { class: "flex items-center justify-between mb-2" }, [
                      createVNode("div", { class: "text-sm font-semibold text-sky-700 dark:text-sky-300" }, " scripts "),
                      createVNode("div", { class: "text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(scripts.value.length), 1)
                    ]),
                    createVNode("div", { class: "overflow-x-auto border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-gray-900" }, [
                      createVNode("table", { class: "w-full text-left border-collapse text-sm" }, [
                        createVNode("thead", { class: "bg-slate-100 dark:bg-slate-800" }, [
                          createVNode("tr", null, [
                            createVNode("th", { class: "w-52 px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-xs font-semibold text-slate-600 dark:text-slate-300" }, " Script "),
                            createVNode("th", { class: "px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-xs font-semibold text-slate-600 dark:text-slate-300" }, " Command ")
                          ])
                        ]),
                        createVNode("tbody", null, [
                          (openBlock(true), createBlock(Fragment, null, renderList(scripts.value, (item) => {
                            return openBlock(), createBlock("tr", {
                              key: item.name,
                              class: "border-b border-slate-200 dark:border-slate-700 last:border-b-0 hover:bg-sky-50 dark:hover:bg-slate-800/80"
                            }, [
                              createVNode("td", { class: "px-3 py-1.5 font-mono font-semibold text-sky-700 dark:text-sky-300 align-top" }, toDisplayString(item.name), 1),
                              createVNode("td", { class: "px-3 py-1.5 font-mono text-slate-800 dark:text-slate-100 break-all" }, toDisplayString(item.value), 1)
                            ]);
                          }), 128)),
                          !scripts.value.length ? (openBlock(), createBlock("tr", { key: 0 }, [
                            createVNode("td", {
                              colspan: "2",
                              class: "px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400"
                            }, " Scripts не найдены ")
                          ])) : createCommentVNode("", true)
                        ])
                      ])
                    ])
                  ])) : createCommentVNode("", true),
                  dependencies.value.length || __props.packageData.dependencies ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "mb-4"
                  }, [
                    createVNode("div", { class: "flex items-center justify-between mb-2" }, [
                      createVNode("div", { class: "text-sm font-semibold text-emerald-700 dark:text-emerald-300" }, " dependencies "),
                      createVNode("div", { class: "text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(dependencies.value.length), 1)
                    ]),
                    createVNode("div", { class: "overflow-x-auto border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-gray-900" }, [
                      createVNode("table", { class: "w-full text-left border-collapse text-sm" }, [
                        createVNode("thead", { class: "bg-slate-100 dark:bg-slate-800" }, [
                          createVNode("tr", null, [
                            createVNode("th", { class: "px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-xs font-semibold text-slate-600 dark:text-slate-300" }, " Package "),
                            createVNode("th", { class: "w-52 px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-xs font-semibold text-slate-600 dark:text-slate-300" }, " Version ")
                          ])
                        ]),
                        createVNode("tbody", null, [
                          (openBlock(true), createBlock(Fragment, null, renderList(dependencies.value, (item) => {
                            return openBlock(), createBlock("tr", {
                              key: item.name,
                              class: "border-b border-slate-200 dark:border-slate-700 last:border-b-0 hover:bg-emerald-50 dark:hover:bg-slate-800/80"
                            }, [
                              createVNode("td", { class: "px-3 py-1.5 font-mono font-semibold text-slate-800 dark:text-slate-100" }, toDisplayString(item.name), 1),
                              createVNode("td", { class: "px-3 py-1.5 font-mono text-emerald-700 dark:text-emerald-300" }, toDisplayString(item.value), 1)
                            ]);
                          }), 128)),
                          !dependencies.value.length ? (openBlock(), createBlock("tr", { key: 0 }, [
                            createVNode("td", {
                              colspan: "2",
                              class: "px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400"
                            }, " Зависимости не найдены ")
                          ])) : createCommentVNode("", true)
                        ])
                      ])
                    ])
                  ])) : createCommentVNode("", true),
                  devDependencies.value.length || __props.packageData.devDependencies ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "mb-4"
                  }, [
                    createVNode("div", { class: "flex items-center justify-between mb-2" }, [
                      createVNode("div", { class: "text-sm font-semibold text-amber-700 dark:text-amber-300" }, " devDependencies "),
                      createVNode("div", { class: "text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(devDependencies.value.length), 1)
                    ]),
                    createVNode("div", { class: "overflow-x-auto border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-gray-900" }, [
                      createVNode("table", { class: "w-full text-left border-collapse text-sm" }, [
                        createVNode("thead", { class: "bg-slate-100 dark:bg-slate-800" }, [
                          createVNode("tr", null, [
                            createVNode("th", { class: "px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-xs font-semibold text-slate-600 dark:text-slate-300" }, " Package "),
                            createVNode("th", { class: "w-52 px-3 py-2 border-b border-slate-300 dark:border-slate-600 text-xs font-semibold text-slate-600 dark:text-slate-300" }, " Version ")
                          ])
                        ]),
                        createVNode("tbody", null, [
                          (openBlock(true), createBlock(Fragment, null, renderList(devDependencies.value, (item) => {
                            return openBlock(), createBlock("tr", {
                              key: item.name,
                              class: "border-b border-slate-200 dark:border-slate-700 last:border-b-0 hover:bg-amber-50 dark:hover:bg-slate-800/80"
                            }, [
                              createVNode("td", { class: "px-3 py-1.5 font-mono font-semibold text-slate-800 dark:text-slate-100" }, toDisplayString(item.name), 1),
                              createVNode("td", { class: "px-3 py-1.5 font-mono text-amber-700 dark:text-amber-300" }, toDisplayString(item.value), 1)
                            ]);
                          }), 128)),
                          !devDependencies.value.length ? (openBlock(), createBlock("tr", { key: 0 }, [
                            createVNode("td", {
                              colspan: "2",
                              class: "px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400"
                            }, " Dev-зависимости не найдены ")
                          ])) : createCommentVNode("", true)
                        ])
                      ])
                    ])
                  ])) : createCommentVNode("", true),
                  peerDependencies.value.length || __props.packageData.peerDependencies ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "mb-4"
                  }, [
                    createVNode("div", { class: "flex items-center justify-between mb-2" }, [
                      createVNode("div", { class: "text-sm font-semibold text-violet-700 dark:text-violet-300" }, " peerDependencies "),
                      createVNode("div", { class: "text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(peerDependencies.value.length), 1)
                    ]),
                    createVNode("div", { class: "overflow-x-auto border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-gray-900" }, [
                      createVNode("table", { class: "w-full text-left border-collapse text-sm" }, [
                        createVNode("tbody", null, [
                          (openBlock(true), createBlock(Fragment, null, renderList(peerDependencies.value, (item) => {
                            return openBlock(), createBlock("tr", {
                              key: item.name,
                              class: "border-b border-slate-200 dark:border-slate-700 last:border-b-0"
                            }, [
                              createVNode("td", { class: "px-3 py-1.5 font-mono font-semibold text-slate-800 dark:text-slate-100" }, toDisplayString(item.name), 1),
                              createVNode("td", { class: "w-52 px-3 py-1.5 font-mono text-violet-700 dark:text-violet-300" }, toDisplayString(item.value), 1)
                            ]);
                          }), 128)),
                          !peerDependencies.value.length ? (openBlock(), createBlock("tr", { key: 0 }, [
                            createVNode("td", {
                              colspan: "2",
                              class: "px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400"
                            }, " Peer-зависимости не найдены ")
                          ])) : createCommentVNode("", true)
                        ])
                      ])
                    ])
                  ])) : createCommentVNode("", true),
                  optionalDependencies.value.length || __props.packageData.optionalDependencies ? (openBlock(), createBlock("div", {
                    key: 5,
                    class: "mb-4"
                  }, [
                    createVNode("div", { class: "flex items-center justify-between mb-2" }, [
                      createVNode("div", { class: "text-sm font-semibold text-fuchsia-700 dark:text-fuchsia-300" }, " optionalDependencies "),
                      createVNode("div", { class: "text-xs text-slate-500 dark:text-slate-400" }, toDisplayString(optionalDependencies.value.length), 1)
                    ]),
                    createVNode("div", { class: "overflow-x-auto border border-slate-300 dark:border-slate-600 rounded-md bg-white dark:bg-gray-900" }, [
                      createVNode("table", { class: "w-full text-left border-collapse text-sm" }, [
                        createVNode("tbody", null, [
                          (openBlock(true), createBlock(Fragment, null, renderList(optionalDependencies.value, (item) => {
                            return openBlock(), createBlock("tr", {
                              key: item.name,
                              class: "border-b border-slate-200 dark:border-slate-700 last:border-b-0"
                            }, [
                              createVNode("td", { class: "px-3 py-1.5 font-mono font-semibold text-slate-800 dark:text-slate-100" }, toDisplayString(item.name), 1),
                              createVNode("td", { class: "w-52 px-3 py-1.5 font-mono text-fuchsia-700 dark:text-fuchsia-300" }, toDisplayString(item.value), 1)
                            ]);
                          }), 128)),
                          !optionalDependencies.value.length ? (openBlock(), createBlock("tr", { key: 0 }, [
                            createVNode("td", {
                              colspan: "2",
                              class: "px-4 py-6 text-center text-sm text-slate-500 dark:text-slate-400"
                            }, " Optional-зависимости не найдены ")
                          ])) : createCommentVNode("", true)
                        ])
                      ])
                    ])
                  ])) : createCommentVNode("", true),
                  otherSections.value.length ? (openBlock(), createBlock("div", {
                    key: 6,
                    class: "space-y-3"
                  }, [
                    (openBlock(true), createBlock(Fragment, null, renderList(otherSections.value, (section) => {
                      return openBlock(), createBlock("div", {
                        key: section.key,
                        class: "border border-slate-300 dark:border-slate-600 rounded-md overflow-hidden bg-white dark:bg-gray-900"
                      }, [
                        createVNode("div", { class: "flex items-center justify-between px-3 py-2 bg-slate-100 dark:bg-slate-800 border-b border-slate-300 dark:border-slate-600" }, [
                          createVNode("div", { class: "font-mono text-sm font-semibold text-orange-700 dark:text-orange-300" }, toDisplayString(section.key), 1),
                          createVNode("button", {
                            type: "button",
                            title: copiedValue.value === section.key ? "Скопировано" : "Копировать",
                            class: "w-7 h-7 inline-flex items-center justify-center rounded text-slate-400 hover:text-orange-600 hover:bg-orange-100 dark:hover:text-orange-300 dark:hover:bg-slate-700 transition",
                            onClick: ($event) => copyToClipboard(
                              section.value,
                              section.key
                            )
                          }, [
                            copiedValue.value !== section.key ? (openBlock(), createBlock("svg", {
                              key: 0,
                              class: "w-4 h-4 fill-current",
                              viewBox: "0 0 24 24"
                            }, [
                              createVNode("path", { d: "M16 1H4c-1.1 0-2 .9-2\n                                           2v14h2V3h12V1zm3 4H8c-1.1\n                                           0-2 .9-2 2v14c0 1.1.9 2\n                                           2 2h11c1.1 0 2-.9\n                                           2-2V7c0-1.1-.9-2-2-2zm0\n                                           16H8V7h11v14z" })
                            ])) : (openBlock(), createBlock("svg", {
                              key: 1,
                              class: "w-4 h-4 fill-current text-green-600 dark:text-green-400",
                              viewBox: "0 0 24 24"
                            }, [
                              createVNode("path", { d: "M9 16.17 4.83 12l-1.42\n                                           1.41L9 19 21 7l-1.41-1.41z" })
                            ]))
                          ], 8, ["title", "onClick"])
                        ]),
                        createVNode("div", { class: "p-3" }, [
                          isObject(section.value) ? (openBlock(), createBlock("pre", {
                            key: 0,
                            class: "m-0 whitespace-pre-wrap break-words font-mono text-xs leading-5 text-slate-800 dark:text-slate-100"
                          }, toDisplayString(stringifyValue(section.value)), 1)) : (openBlock(), createBlock("div", {
                            key: 1,
                            class: "font-mono text-sm text-slate-800 dark:text-slate-100 break-all"
                          }, toDisplayString(displayValue(section.value)), 1))
                        ])
                      ]);
                    }), 128))
                  ])) : createCommentVNode("", true),
                  search.value && !scripts.value.length && !dependencies.value.length && !devDependencies.value.length && !peerDependencies.value.length && !optionalDependencies.value.length && !otherSections.value.length ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "py-8 text-center text-sm text-slate-500 dark:text-slate-400"
                  }, " Ничего не найдено ")) : createCommentVNode("", true),
                  createVNode("div", { class: "mt-3 text-xs text-slate-500 dark:text-slate-400" }, " Конфигурация package.json доступна только для просмотра. Изменение зависимостей и запуск npm-команд через административную панель отключены. ")
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/PackageInfoPage.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
