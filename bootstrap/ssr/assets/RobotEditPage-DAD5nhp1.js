import { computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createVNode, withModifiers, createBlock, createCommentVNode, openBlock, withDirectives, vModelRadio, Fragment, renderList, vModelText, useSSRContext } from "vue";
import { ssrRenderComponent, ssrRenderClass, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseEqual, ssrRenderList, ssrInterpolate } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";
import { useI18n } from "vue-i18n";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./PrimaryButton-B3InEAXg.js";
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
const _sfc_main = {
  __name: "RobotEditPage",
  __ssrInlineRender: true,
  props: {
    content: {
      type: String,
      default: ""
    },
    groups: {
      type: Array,
      default: () => []
    },
    sitemaps: {
      type: Array,
      default: () => []
    },
    blockAll: {
      type: Boolean,
      default: false
    }
  },
  setup(__props) {
    var _a, _b;
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const createDefaultGroup = () => ({
      user_agents: ["*"],
      allow: [],
      disallow: [],
      clean_params: []
    });
    const form = useForm({
      editor_mode: "builder",
      content: props.content,
      block_all: props.blockAll,
      groups: ((_a = props.groups) == null ? void 0 : _a.length) ? JSON.parse(JSON.stringify(props.groups)) : [createDefaultGroup()],
      sitemaps: ((_b = props.sitemaps) == null ? void 0 : _b.length) ? [...props.sitemaps] : []
    });
    const cleanArray = (items) => {
      return [
        ...new Set(
          (items || []).map((item) => String(item || "").trim()).filter(Boolean)
        )
      ];
    };
    const preview = computed(() => {
      const lines = [
        "# robots.txt",
        "# Generated from admin panel",
        ""
      ];
      if (form.block_all) {
        lines.push("User-agent: *");
        lines.push("Disallow: /");
      } else {
        form.groups.forEach((group) => {
          let userAgents = cleanArray(group.user_agents || []);
          if (!userAgents.length) {
            userAgents = ["*"];
          }
          userAgents.forEach((userAgent) => {
            lines.push(`User-agent: ${userAgent}`);
          });
          cleanArray(group.disallow || []).forEach((path) => {
            lines.push(`Disallow: ${path}`);
          });
          cleanArray(group.allow || []).forEach((path) => {
            lines.push(`Allow: ${path}`);
          });
          (group.clean_params || []).forEach((item) => {
            const params = String(item.params || "").trim();
            const path = String(item.path || "").trim();
            if (!params) {
              return;
            }
            let value = `Clean-param: ${params}`;
            if (path) {
              value += ` ${path}`;
            }
            lines.push(value);
          });
          lines.push("");
        });
      }
      const sitemaps = cleanArray(form.sitemaps || []);
      if (sitemaps.length) {
        if (lines.length && lines[lines.length - 1] !== "") {
          lines.push("");
        }
        sitemaps.forEach((sitemap) => {
          lines.push(`Sitemap: ${sitemap}`);
        });
      }
      while (lines.length && lines[lines.length - 1] === "") {
        lines.pop();
      }
      return lines.join("\n");
    });
    const setEditorMode = (mode) => {
      if (mode === "manual" && form.editor_mode === "builder") {
        form.content = preview.value;
      }
      form.editor_mode = mode;
    };
    const addGroup = () => {
      form.groups.push(createDefaultGroup());
    };
    const removeGroup = (groupIndex) => {
      if (form.groups.length <= 1) {
        return;
      }
      form.groups.splice(groupIndex, 1);
    };
    const addUserAgent = (group) => {
      group.user_agents.push("");
    };
    const removeUserAgent = (group, index) => {
      if (group.user_agents.length <= 1) {
        return;
      }
      group.user_agents.splice(index, 1);
    };
    const addDisallow = (group) => {
      group.disallow.push("");
    };
    const removeDisallow = (group, index) => {
      group.disallow.splice(index, 1);
    };
    const addAllow = (group) => {
      group.allow.push("");
    };
    const removeAllow = (group, index) => {
      group.allow.splice(index, 1);
    };
    const addCleanParam = (group) => {
      group.clean_params.push({
        params: "",
        path: ""
      });
    };
    const removeCleanParam = (group, index) => {
      group.clean_params.splice(index, 1);
    };
    const addSitemap = () => {
      form.sitemaps.push("");
    };
    const removeSitemap = (index) => {
      form.sitemaps.splice(index, 1);
    };
    const submit = () => {
      form.put(route("admin.robot.update"), {
        preserveScroll: true,
        onSuccess: () => toast.success(t("robotSuccess")),
        onError: () => toast.error(t("robotError"))
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("robotTitle")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("robotTitle"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("robotTitle")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("robotTitle")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="mb-4 px-4 py-3 bg-sky-50 dark:bg-sky-900/20 border border-sky-300 dark:border-sky-700 rounded"${_scopeId}><div class="text-sm font-semibold text-sky-800 dark:text-sky-200"${_scopeId}> Генератор robots.txt </div><div class="mt-1 text-xs text-sky-700 dark:text-sky-300"${_scopeId}> Используйте конструктор для стандартных правил или ручной редактор для полного контроля над содержимым файла. </div></div><form class="space-y-4"${_scopeId}><div class="flex justify-end"${_scopeId}><div class="inline-flex p-1 bg-slate-200 dark:bg-slate-800 border border-slate-400 dark:border-slate-500 rounded"${_scopeId}><button type="button" class="${ssrRenderClass([unref(form).editor_mode === "builder" ? "bg-white dark:bg-slate-600 text-sky-700 dark:text-sky-200 shadow" : "text-slate-600 dark:text-slate-300", "px-4 py-1.5 text-sm rounded transition"])}"${_scopeId}> Конструктор </button><button type="button" class="${ssrRenderClass([unref(form).editor_mode === "manual" ? "bg-white dark:bg-slate-600 text-sky-700 dark:text-sky-200 shadow" : "text-slate-600 dark:text-slate-300", "px-4 py-1.5 text-sm rounded transition"])}"${_scopeId}> Редактор </button></div></div>`);
            if (unref(form).editor_mode === "builder") {
              _push2(`<div class="space-y-4"${_scopeId}><div class="p-4 bg-white dark:bg-slate-800 border border-slate-400 dark:border-slate-500 rounded"${_scopeId}><div class="text-center text-sm font-semibold text-slate-800 dark:text-slate-100"${_scopeId}> Индексация сайта </div><div class="mt-3 flex justify-center items-center gap-6"${_scopeId}><label class="inline-flex items-center gap-2 cursor-pointer"${_scopeId}><input${ssrIncludeBooleanAttr(ssrLooseEqual(unref(form).block_all, false)) ? " checked" : ""}${ssrRenderAttr("value", false)} type="radio"${_scopeId}><span class="text-sm text-green-700 dark:text-green-400"${_scopeId}> Разрешена </span></label><label class="inline-flex items-center gap-2 cursor-pointer"${_scopeId}><input${ssrIncludeBooleanAttr(ssrLooseEqual(unref(form).block_all, true)) ? " checked" : ""}${ssrRenderAttr("value", true)} type="radio"${_scopeId}><span class="text-sm text-red-600 dark:text-red-400"${_scopeId}> Запрещена полностью </span></label></div>`);
              if (unref(form).block_all) {
                _push2(`<div class="mt-3 px-3 py-2 text-center text-xs bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 rounded"${_scopeId}> Будет сформировано правило <span class="font-mono font-semibold"${_scopeId}>Disallow: /</span>. Поисковым роботам будет запрещено сканирование всего сайта. </div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`</div>`);
              if (!unref(form).block_all) {
                _push2(`<div class="space-y-3"${_scopeId}><div class="flex items-center justify-between gap-3"${_scopeId}><div${_scopeId}><div class="text-sm font-semibold text-slate-800 dark:text-slate-100"${_scopeId}> Группы роботов </div><div class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}> Для каждой группы можно задать отдельные правила. </div></div><button type="button" class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-white bg-sky-600 hover:bg-sky-700 border border-sky-700 dark:border-sky-500 rounded-sm shadow-sm transition-colors"${_scopeId}><span class="text-sm leading-none"${_scopeId}>+</span> Добавить группу </button></div><!--[-->`);
                ssrRenderList(unref(form).groups, (group, groupIndex) => {
                  _push2(`<div class="p-4 bg-white dark:bg-slate-800 border border-slate-400 dark:border-slate-500 rounded"${_scopeId}><div class="flex items-center justify-between gap-3 mb-4"${_scopeId}><div class="text-sm font-semibold text-slate-800 dark:text-slate-100"${_scopeId}> Группа ${ssrInterpolate(groupIndex + 1)}</div>`);
                  if (unref(form).groups.length > 1) {
                    _push2(`<button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-300 dark:border-amber-700 rounded-sm shadow-sm transition-colors"${_scopeId}><span class="text-sm leading-none"${_scopeId}>×</span> Удалить группу </button>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  _push2(`</div><div class="mb-4"${_scopeId}><div class="flex items-center justify-between mb-2"${_scopeId}><label class="text-sm font-medium text-slate-700 dark:text-slate-200"${_scopeId}> User-agent </label><button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-sky-700 dark:text-sky-200 bg-sky-50 dark:bg-sky-900/40 hover:bg-sky-100 dark:hover:bg-sky-800/60 border border-sky-300 dark:border-sky-600 rounded-sm shadow-sm transition-colors"${_scopeId}><span class="text-sm leading-none"${_scopeId}>+</span> Добавить </button></div><div class="space-y-2"${_scopeId}><!--[-->`);
                  ssrRenderList(group.user_agents, (userAgent, index) => {
                    _push2(`<div class="flex items-center gap-2"${_scopeId}><input${ssrRenderAttr("value", group.user_agents[index])} type="text" placeholder="*" class="w-full px-3 py-1 text-sm font-mono border border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"${_scopeId}>`);
                    if (group.user_agents.length > 1) {
                      _push2(`<button type="button" title="Удалить" class="flex-none w-8 h-8 inline-flex items-center justify-center text-base font-semibold text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-300 dark:border-amber-700 rounded-sm shadow-sm transition-colors"${_scopeId}> × </button>`);
                    } else {
                      _push2(`<!---->`);
                    }
                    _push2(`</div>`);
                  });
                  _push2(`<!--]--></div></div><div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4"${_scopeId}><div${_scopeId}><div class="flex items-center justify-between mb-2"${_scopeId}><label class="text-sm font-medium text-slate-700 dark:text-slate-200"${_scopeId}> Disallow </label><button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-sky-700 dark:text-sky-200 bg-sky-50 dark:bg-sky-900/40 hover:bg-sky-100 dark:hover:bg-sky-800/60 border border-sky-300 dark:border-sky-600 rounded-sm shadow-sm transition-colors"${_scopeId}><span class="text-sm leading-none"${_scopeId}>+</span> Добавить </button></div>`);
                  if (!group.disallow.length) {
                    _push2(`<div class="text-xs text-slate-400"${_scopeId}> Запрещённых путей нет. </div>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  _push2(`<div class="space-y-2"${_scopeId}><!--[-->`);
                  ssrRenderList(group.disallow, (value, index) => {
                    _push2(`<div class="flex items-center gap-2"${_scopeId}><input${ssrRenderAttr("value", group.disallow[index])} type="text" placeholder="/admin/" class="w-full px-3 py-1 text-sm font-mono border border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"${_scopeId}><button type="button" title="Удалить" class="flex-none w-8 h-8 inline-flex items-center justify-center text-base font-semibold text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-300 dark:border-amber-700 rounded-sm shadow-sm transition-colors"${_scopeId}> × </button></div>`);
                  });
                  _push2(`<!--]--></div></div><div${_scopeId}><div class="flex items-center justify-between mb-2"${_scopeId}><label class="text-sm font-medium text-slate-700 dark:text-slate-200"${_scopeId}> Allow </label><button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-sky-700 dark:text-sky-200 bg-sky-50 dark:bg-sky-900/40 hover:bg-sky-100 dark:hover:bg-sky-800/60 border border-sky-300 dark:border-sky-600 rounded-sm shadow-sm transition-colors"${_scopeId}><span class="text-sm leading-none"${_scopeId}>+</span> Добавить </button></div>`);
                  if (!group.allow.length) {
                    _push2(`<div class="text-xs text-slate-400"${_scopeId}> Явных разрешений нет. </div>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  _push2(`<div class="space-y-2"${_scopeId}><!--[-->`);
                  ssrRenderList(group.allow, (value, index) => {
                    _push2(`<div class="flex items-center gap-2"${_scopeId}><input${ssrRenderAttr("value", group.allow[index])} type="text" placeholder="/public/" class="w-full px-3 py-1 text-sm font-mono border border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"${_scopeId}><button type="button" title="Удалить" class="flex-none w-8 h-8 inline-flex items-center justify-center text-base font-semibold text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-300 dark:border-amber-700 rounded-sm shadow-sm transition-colors"${_scopeId}> × </button></div>`);
                  });
                  _push2(`<!--]--></div></div></div><div${_scopeId}><div class="flex items-center justify-between mb-2"${_scopeId}><div${_scopeId}><label class="text-sm font-medium text-slate-700 dark:text-slate-200"${_scopeId}> Clean-param </label><div class="text-xs text-slate-400"${_scopeId}> Дополнительная директива Yandex. </div></div><button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-sky-700 dark:text-sky-200 bg-sky-50 dark:bg-sky-900/40 hover:bg-sky-100 dark:hover:bg-sky-800/60 border border-sky-300 dark:border-sky-600 rounded-sm shadow-sm transition-colors"${_scopeId}><span class="text-sm leading-none"${_scopeId}>+</span> Добавить </button></div><div class="space-y-2"${_scopeId}><!--[-->`);
                  ssrRenderList(group.clean_params, (item, index) => {
                    _push2(`<div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-2"${_scopeId}><input${ssrRenderAttr("value", item.params)} type="text" placeholder="utm_source&amp;utm_medium" class="px-3 py-1 text-sm font-mono border border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"${_scopeId}><input${ssrRenderAttr("value", item.path)} type="text" placeholder="/" class="px-3 py-1 text-sm font-mono border border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"${_scopeId}><button type="button" title="Удалить" class="w-8 h-8 inline-flex items-center justify-center text-base font-semibold text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-300 dark:border-amber-700 rounded-sm shadow-sm transition-colors"${_scopeId}> × </button></div>`);
                  });
                  _push2(`<!--]--></div></div></div>`);
                });
                _push2(`<!--]--></div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="p-4 bg-white dark:bg-slate-800 border border-slate-400 dark:border-slate-500 rounded"${_scopeId}><div class="flex items-center justify-between gap-3 mb-3"${_scopeId}><div${_scopeId}><div class="text-sm font-semibold text-slate-800 dark:text-slate-100"${_scopeId}> Sitemap </div><div class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}> Используйте абсолютный URL. </div></div><button type="button" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-sky-700 dark:text-sky-200 bg-sky-50 dark:bg-sky-900/40 hover:bg-sky-100 dark:hover:bg-sky-800/60 border border-sky-300 dark:border-sky-600 rounded-sm shadow-sm transition-colors"${_scopeId}><span class="text-sm leading-none"${_scopeId}>+</span> Добавить </button></div>`);
              if (!unref(form).sitemaps.length) {
                _push2(`<div class="text-xs text-slate-400"${_scopeId}> Sitemap пока не указан. </div>`);
              } else {
                _push2(`<!---->`);
              }
              _push2(`<div class="space-y-2"${_scopeId}><!--[-->`);
              ssrRenderList(unref(form).sitemaps, (sitemap, index) => {
                _push2(`<div class="flex items-center gap-2"${_scopeId}><input${ssrRenderAttr("value", unref(form).sitemaps[index])} type="url" placeholder="https://example.com/sitemap.xml" class="w-full px-3 py-1 text-sm font-mono border border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"${_scopeId}><button type="button" title="Удалить" class="flex-none w-8 h-8 inline-flex items-center justify-center text-base font-semibold text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-300 dark:border-amber-700 rounded-sm shadow-sm transition-colors"${_scopeId}> × </button></div>`);
              });
              _push2(`<!--]--></div></div><div${_scopeId}><div class="mb-2 flex items-center justify-between"${_scopeId}><label class="text-sm font-semibold text-slate-800 dark:text-slate-100"${_scopeId}> Предпросмотр robots.txt </label><span class="text-xs text-slate-500 dark:text-slate-400"${_scopeId}> Обновляется автоматически </span></div><pre class="w-full min-h-52 p-3 overflow-x-auto text-xs leading-5 font-mono bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 border border-slate-400 dark:border-slate-500 rounded"${_scopeId}>${ssrInterpolate(preview.value)}</pre></div></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (unref(form).editor_mode === "manual") {
              _push2(`<div class="space-y-2"${_scopeId}><div class="flex items-end justify-between gap-4"${_scopeId}><div${_scopeId}><div class="text-sm font-semibold text-slate-800 dark:text-slate-100"${_scopeId}> Ручное редактирование robots.txt </div><div class="mt-1 text-xs text-slate-500 dark:text-slate-400"${_scopeId}> Содержимое сохраняется без автоматического изменения генератором. </div></div><div class="text-xs text-slate-400"${_scopeId}> Полный контроль файла </div></div><textarea rows="24" spellcheck="false" class="w-full min-h-[500px] p-3 font-mono text-sm leading-6 border border-slate-400 dark:border-slate-500 rounded bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500"${_scopeId}>${ssrInterpolate(unref(form).content)}</textarea></div>`);
            } else {
              _push2(`<!---->`);
            }
            if (Object.keys(unref(form).errors).length) {
              _push2(`<div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 rounded"${_scopeId}><div class="text-sm font-semibold text-red-700 dark:text-red-300"${_scopeId}> Ошибка сохранения </div><!--[-->`);
              ssrRenderList(unref(form).errors, (error, key) => {
                _push2(`<div class="mt-1 text-xs text-red-600 dark:text-red-400"${_scopeId}>${ssrInterpolate(error)}</div>`);
              });
              _push2(`<!--]--></div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`<div class="flex justify-end"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              disabled: unref(form).processing,
              class: { "opacity-50 cursor-not-allowed": unref(form).processing }
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(form).processing ? "Сохранение..." : unref(t)("save"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(form).processing ? "Сохранение..." : unref(t)("save")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></form></div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "mb-4 px-4 py-3 bg-sky-50 dark:bg-sky-900/20 border border-sky-300 dark:border-sky-700 rounded" }, [
                    createVNode("div", { class: "text-sm font-semibold text-sky-800 dark:text-sky-200" }, " Генератор robots.txt "),
                    createVNode("div", { class: "mt-1 text-xs text-sky-700 dark:text-sky-300" }, " Используйте конструктор для стандартных правил или ручной редактор для полного контроля над содержимым файла. ")
                  ]),
                  createVNode("form", {
                    class: "space-y-4",
                    onSubmit: withModifiers(submit, ["prevent"])
                  }, [
                    createVNode("div", { class: "flex justify-end" }, [
                      createVNode("div", { class: "inline-flex p-1 bg-slate-200 dark:bg-slate-800 border border-slate-400 dark:border-slate-500 rounded" }, [
                        createVNode("button", {
                          type: "button",
                          class: ["px-4 py-1.5 text-sm rounded transition", unref(form).editor_mode === "builder" ? "bg-white dark:bg-slate-600 text-sky-700 dark:text-sky-200 shadow" : "text-slate-600 dark:text-slate-300"],
                          onClick: ($event) => setEditorMode("builder")
                        }, " Конструктор ", 10, ["onClick"]),
                        createVNode("button", {
                          type: "button",
                          class: ["px-4 py-1.5 text-sm rounded transition", unref(form).editor_mode === "manual" ? "bg-white dark:bg-slate-600 text-sky-700 dark:text-sky-200 shadow" : "text-slate-600 dark:text-slate-300"],
                          onClick: ($event) => setEditorMode("manual")
                        }, " Редактор ", 10, ["onClick"])
                      ])
                    ]),
                    unref(form).editor_mode === "builder" ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "space-y-4"
                    }, [
                      createVNode("div", { class: "p-4 bg-white dark:bg-slate-800 border border-slate-400 dark:border-slate-500 rounded" }, [
                        createVNode("div", { class: "text-center text-sm font-semibold text-slate-800 dark:text-slate-100" }, " Индексация сайта "),
                        createVNode("div", { class: "mt-3 flex justify-center items-center gap-6" }, [
                          createVNode("label", { class: "inline-flex items-center gap-2 cursor-pointer" }, [
                            withDirectives(createVNode("input", {
                              "onUpdate:modelValue": ($event) => unref(form).block_all = $event,
                              value: false,
                              type: "radio"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelRadio, unref(form).block_all]
                            ]),
                            createVNode("span", { class: "text-sm text-green-700 dark:text-green-400" }, " Разрешена ")
                          ]),
                          createVNode("label", { class: "inline-flex items-center gap-2 cursor-pointer" }, [
                            withDirectives(createVNode("input", {
                              "onUpdate:modelValue": ($event) => unref(form).block_all = $event,
                              value: true,
                              type: "radio"
                            }, null, 8, ["onUpdate:modelValue"]), [
                              [vModelRadio, unref(form).block_all]
                            ]),
                            createVNode("span", { class: "text-sm text-red-600 dark:text-red-400" }, " Запрещена полностью ")
                          ])
                        ]),
                        unref(form).block_all ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "mt-3 px-3 py-2 text-center text-xs bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 text-red-700 dark:text-red-300 rounded"
                        }, [
                          createTextVNode(" Будет сформировано правило "),
                          createVNode("span", { class: "font-mono font-semibold" }, "Disallow: /"),
                          createTextVNode(". Поисковым роботам будет запрещено сканирование всего сайта. ")
                        ])) : createCommentVNode("", true)
                      ]),
                      !unref(form).block_all ? (openBlock(), createBlock("div", {
                        key: 0,
                        class: "space-y-3"
                      }, [
                        createVNode("div", { class: "flex items-center justify-between gap-3" }, [
                          createVNode("div", null, [
                            createVNode("div", { class: "text-sm font-semibold text-slate-800 dark:text-slate-100" }, " Группы роботов "),
                            createVNode("div", { class: "text-xs text-slate-500 dark:text-slate-400" }, " Для каждой группы можно задать отдельные правила. ")
                          ]),
                          createVNode("button", {
                            type: "button",
                            class: "inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-white bg-sky-600 hover:bg-sky-700 border border-sky-700 dark:border-sky-500 rounded-sm shadow-sm transition-colors",
                            onClick: addGroup
                          }, [
                            createVNode("span", { class: "text-sm leading-none" }, "+"),
                            createTextVNode(" Добавить группу ")
                          ])
                        ]),
                        (openBlock(true), createBlock(Fragment, null, renderList(unref(form).groups, (group, groupIndex) => {
                          return openBlock(), createBlock("div", {
                            key: groupIndex,
                            class: "p-4 bg-white dark:bg-slate-800 border border-slate-400 dark:border-slate-500 rounded"
                          }, [
                            createVNode("div", { class: "flex items-center justify-between gap-3 mb-4" }, [
                              createVNode("div", { class: "text-sm font-semibold text-slate-800 dark:text-slate-100" }, " Группа " + toDisplayString(groupIndex + 1), 1),
                              unref(form).groups.length > 1 ? (openBlock(), createBlock("button", {
                                key: 0,
                                type: "button",
                                class: "inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-300 dark:border-amber-700 rounded-sm shadow-sm transition-colors",
                                onClick: ($event) => removeGroup(groupIndex)
                              }, [
                                createVNode("span", { class: "text-sm leading-none" }, "×"),
                                createTextVNode(" Удалить группу ")
                              ], 8, ["onClick"])) : createCommentVNode("", true)
                            ]),
                            createVNode("div", { class: "mb-4" }, [
                              createVNode("div", { class: "flex items-center justify-between mb-2" }, [
                                createVNode("label", { class: "text-sm font-medium text-slate-700 dark:text-slate-200" }, " User-agent "),
                                createVNode("button", {
                                  type: "button",
                                  class: "inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-sky-700 dark:text-sky-200 bg-sky-50 dark:bg-sky-900/40 hover:bg-sky-100 dark:hover:bg-sky-800/60 border border-sky-300 dark:border-sky-600 rounded-sm shadow-sm transition-colors",
                                  onClick: ($event) => addUserAgent(group)
                                }, [
                                  createVNode("span", { class: "text-sm leading-none" }, "+"),
                                  createTextVNode(" Добавить ")
                                ], 8, ["onClick"])
                              ]),
                              createVNode("div", { class: "space-y-2" }, [
                                (openBlock(true), createBlock(Fragment, null, renderList(group.user_agents, (userAgent, index) => {
                                  return openBlock(), createBlock("div", {
                                    key: index,
                                    class: "flex items-center gap-2"
                                  }, [
                                    withDirectives(createVNode("input", {
                                      "onUpdate:modelValue": ($event) => group.user_agents[index] = $event,
                                      type: "text",
                                      placeholder: "*",
                                      class: "w-full px-3 py-1 text-sm font-mono border border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                                    }, null, 8, ["onUpdate:modelValue"]), [
                                      [vModelText, group.user_agents[index]]
                                    ]),
                                    group.user_agents.length > 1 ? (openBlock(), createBlock("button", {
                                      key: 0,
                                      type: "button",
                                      title: "Удалить",
                                      class: "flex-none w-8 h-8 inline-flex items-center justify-center text-base font-semibold text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-300 dark:border-amber-700 rounded-sm shadow-sm transition-colors",
                                      onClick: ($event) => removeUserAgent(group, index)
                                    }, " × ", 8, ["onClick"])) : createCommentVNode("", true)
                                  ]);
                                }), 128))
                              ])
                            ]),
                            createVNode("div", { class: "grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4" }, [
                              createVNode("div", null, [
                                createVNode("div", { class: "flex items-center justify-between mb-2" }, [
                                  createVNode("label", { class: "text-sm font-medium text-slate-700 dark:text-slate-200" }, " Disallow "),
                                  createVNode("button", {
                                    type: "button",
                                    class: "inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-sky-700 dark:text-sky-200 bg-sky-50 dark:bg-sky-900/40 hover:bg-sky-100 dark:hover:bg-sky-800/60 border border-sky-300 dark:border-sky-600 rounded-sm shadow-sm transition-colors",
                                    onClick: ($event) => addDisallow(group)
                                  }, [
                                    createVNode("span", { class: "text-sm leading-none" }, "+"),
                                    createTextVNode(" Добавить ")
                                  ], 8, ["onClick"])
                                ]),
                                !group.disallow.length ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-xs text-slate-400"
                                }, " Запрещённых путей нет. ")) : createCommentVNode("", true),
                                createVNode("div", { class: "space-y-2" }, [
                                  (openBlock(true), createBlock(Fragment, null, renderList(group.disallow, (value, index) => {
                                    return openBlock(), createBlock("div", {
                                      key: index,
                                      class: "flex items-center gap-2"
                                    }, [
                                      withDirectives(createVNode("input", {
                                        "onUpdate:modelValue": ($event) => group.disallow[index] = $event,
                                        type: "text",
                                        placeholder: "/admin/",
                                        class: "w-full px-3 py-1 text-sm font-mono border border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                                      }, null, 8, ["onUpdate:modelValue"]), [
                                        [vModelText, group.disallow[index]]
                                      ]),
                                      createVNode("button", {
                                        type: "button",
                                        title: "Удалить",
                                        class: "flex-none w-8 h-8 inline-flex items-center justify-center text-base font-semibold text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-300 dark:border-amber-700 rounded-sm shadow-sm transition-colors",
                                        onClick: ($event) => removeDisallow(group, index)
                                      }, " × ", 8, ["onClick"])
                                    ]);
                                  }), 128))
                                ])
                              ]),
                              createVNode("div", null, [
                                createVNode("div", { class: "flex items-center justify-between mb-2" }, [
                                  createVNode("label", { class: "text-sm font-medium text-slate-700 dark:text-slate-200" }, " Allow "),
                                  createVNode("button", {
                                    type: "button",
                                    class: "inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-sky-700 dark:text-sky-200 bg-sky-50 dark:bg-sky-900/40 hover:bg-sky-100 dark:hover:bg-sky-800/60 border border-sky-300 dark:border-sky-600 rounded-sm shadow-sm transition-colors",
                                    onClick: ($event) => addAllow(group)
                                  }, [
                                    createVNode("span", { class: "text-sm leading-none" }, "+"),
                                    createTextVNode(" Добавить ")
                                  ], 8, ["onClick"])
                                ]),
                                !group.allow.length ? (openBlock(), createBlock("div", {
                                  key: 0,
                                  class: "text-xs text-slate-400"
                                }, " Явных разрешений нет. ")) : createCommentVNode("", true),
                                createVNode("div", { class: "space-y-2" }, [
                                  (openBlock(true), createBlock(Fragment, null, renderList(group.allow, (value, index) => {
                                    return openBlock(), createBlock("div", {
                                      key: index,
                                      class: "flex items-center gap-2"
                                    }, [
                                      withDirectives(createVNode("input", {
                                        "onUpdate:modelValue": ($event) => group.allow[index] = $event,
                                        type: "text",
                                        placeholder: "/public/",
                                        class: "w-full px-3 py-1 text-sm font-mono border border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                                      }, null, 8, ["onUpdate:modelValue"]), [
                                        [vModelText, group.allow[index]]
                                      ]),
                                      createVNode("button", {
                                        type: "button",
                                        title: "Удалить",
                                        class: "flex-none w-8 h-8 inline-flex items-center justify-center text-base font-semibold text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-300 dark:border-amber-700 rounded-sm shadow-sm transition-colors",
                                        onClick: ($event) => removeAllow(group, index)
                                      }, " × ", 8, ["onClick"])
                                    ]);
                                  }), 128))
                                ])
                              ])
                            ]),
                            createVNode("div", null, [
                              createVNode("div", { class: "flex items-center justify-between mb-2" }, [
                                createVNode("div", null, [
                                  createVNode("label", { class: "text-sm font-medium text-slate-700 dark:text-slate-200" }, " Clean-param "),
                                  createVNode("div", { class: "text-xs text-slate-400" }, " Дополнительная директива Yandex. ")
                                ]),
                                createVNode("button", {
                                  type: "button",
                                  class: "inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-sky-700 dark:text-sky-200 bg-sky-50 dark:bg-sky-900/40 hover:bg-sky-100 dark:hover:bg-sky-800/60 border border-sky-300 dark:border-sky-600 rounded-sm shadow-sm transition-colors",
                                  onClick: ($event) => addCleanParam(group)
                                }, [
                                  createVNode("span", { class: "text-sm leading-none" }, "+"),
                                  createTextVNode(" Добавить ")
                                ], 8, ["onClick"])
                              ]),
                              createVNode("div", { class: "space-y-2" }, [
                                (openBlock(true), createBlock(Fragment, null, renderList(group.clean_params, (item, index) => {
                                  return openBlock(), createBlock("div", {
                                    key: index,
                                    class: "grid grid-cols-1 md:grid-cols-[1fr_1fr_auto] gap-2"
                                  }, [
                                    withDirectives(createVNode("input", {
                                      "onUpdate:modelValue": ($event) => item.params = $event,
                                      type: "text",
                                      placeholder: "utm_source&utm_medium",
                                      class: "px-3 py-1 text-sm font-mono border border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                                    }, null, 8, ["onUpdate:modelValue"]), [
                                      [vModelText, item.params]
                                    ]),
                                    withDirectives(createVNode("input", {
                                      "onUpdate:modelValue": ($event) => item.path = $event,
                                      type: "text",
                                      placeholder: "/",
                                      class: "px-3 py-1 text-sm font-mono border border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                                    }, null, 8, ["onUpdate:modelValue"]), [
                                      [vModelText, item.path]
                                    ]),
                                    createVNode("button", {
                                      type: "button",
                                      title: "Удалить",
                                      class: "w-8 h-8 inline-flex items-center justify-center text-base font-semibold text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-300 dark:border-amber-700 rounded-sm shadow-sm transition-colors",
                                      onClick: ($event) => removeCleanParam(group, index)
                                    }, " × ", 8, ["onClick"])
                                  ]);
                                }), 128))
                              ])
                            ])
                          ]);
                        }), 128))
                      ])) : createCommentVNode("", true),
                      createVNode("div", { class: "p-4 bg-white dark:bg-slate-800 border border-slate-400 dark:border-slate-500 rounded" }, [
                        createVNode("div", { class: "flex items-center justify-between gap-3 mb-3" }, [
                          createVNode("div", null, [
                            createVNode("div", { class: "text-sm font-semibold text-slate-800 dark:text-slate-100" }, " Sitemap "),
                            createVNode("div", { class: "text-xs text-slate-500 dark:text-slate-400" }, " Используйте абсолютный URL. ")
                          ]),
                          createVNode("button", {
                            type: "button",
                            class: "inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium text-sky-700 dark:text-sky-200 bg-sky-50 dark:bg-sky-900/40 hover:bg-sky-100 dark:hover:bg-sky-800/60 border border-sky-300 dark:border-sky-600 rounded-sm shadow-sm transition-colors",
                            onClick: addSitemap
                          }, [
                            createVNode("span", { class: "text-sm leading-none" }, "+"),
                            createTextVNode(" Добавить ")
                          ])
                        ]),
                        !unref(form).sitemaps.length ? (openBlock(), createBlock("div", {
                          key: 0,
                          class: "text-xs text-slate-400"
                        }, " Sitemap пока не указан. ")) : createCommentVNode("", true),
                        createVNode("div", { class: "space-y-2" }, [
                          (openBlock(true), createBlock(Fragment, null, renderList(unref(form).sitemaps, (sitemap, index) => {
                            return openBlock(), createBlock("div", {
                              key: index,
                              class: "flex items-center gap-2"
                            }, [
                              withDirectives(createVNode("input", {
                                "onUpdate:modelValue": ($event) => unref(form).sitemaps[index] = $event,
                                type: "url",
                                placeholder: "https://example.com/sitemap.xml",
                                class: "w-full px-3 py-1 text-sm font-mono border border-slate-400 dark:border-slate-500 rounded-sm bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100"
                              }, null, 8, ["onUpdate:modelValue"]), [
                                [vModelText, unref(form).sitemaps[index]]
                              ]),
                              createVNode("button", {
                                type: "button",
                                title: "Удалить",
                                class: "flex-none w-8 h-8 inline-flex items-center justify-center text-base font-semibold text-amber-600 dark:text-amber-300 bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 border border-amber-300 dark:border-amber-700 rounded-sm shadow-sm transition-colors",
                                onClick: ($event) => removeSitemap(index)
                              }, " × ", 8, ["onClick"])
                            ]);
                          }), 128))
                        ])
                      ]),
                      createVNode("div", null, [
                        createVNode("div", { class: "mb-2 flex items-center justify-between" }, [
                          createVNode("label", { class: "text-sm font-semibold text-slate-800 dark:text-slate-100" }, " Предпросмотр robots.txt "),
                          createVNode("span", { class: "text-xs text-slate-500 dark:text-slate-400" }, " Обновляется автоматически ")
                        ]),
                        createVNode("pre", { class: "w-full min-h-52 p-3 overflow-x-auto text-xs leading-5 font-mono bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 border border-slate-400 dark:border-slate-500 rounded" }, toDisplayString(preview.value), 1)
                      ])
                    ])) : createCommentVNode("", true),
                    unref(form).editor_mode === "manual" ? (openBlock(), createBlock("div", {
                      key: 1,
                      class: "space-y-2"
                    }, [
                      createVNode("div", { class: "flex items-end justify-between gap-4" }, [
                        createVNode("div", null, [
                          createVNode("div", { class: "text-sm font-semibold text-slate-800 dark:text-slate-100" }, " Ручное редактирование robots.txt "),
                          createVNode("div", { class: "mt-1 text-xs text-slate-500 dark:text-slate-400" }, " Содержимое сохраняется без автоматического изменения генератором. ")
                        ]),
                        createVNode("div", { class: "text-xs text-slate-400" }, " Полный контроль файла ")
                      ]),
                      withDirectives(createVNode("textarea", {
                        "onUpdate:modelValue": ($event) => unref(form).content = $event,
                        rows: "24",
                        spellcheck: "false",
                        class: "w-full min-h-[500px] p-3 font-mono text-sm leading-6 border border-slate-400 dark:border-slate-500 rounded bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder:text-slate-400 dark:placeholder:text-slate-500 focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500"
                      }, null, 8, ["onUpdate:modelValue"]), [
                        [vModelText, unref(form).content]
                      ])
                    ])) : createCommentVNode("", true),
                    Object.keys(unref(form).errors).length ? (openBlock(), createBlock("div", {
                      key: 2,
                      class: "p-3 bg-red-50 dark:bg-red-900/20 border border-red-300 dark:border-red-700 rounded"
                    }, [
                      createVNode("div", { class: "text-sm font-semibold text-red-700 dark:text-red-300" }, " Ошибка сохранения "),
                      (openBlock(true), createBlock(Fragment, null, renderList(unref(form).errors, (error, key) => {
                        return openBlock(), createBlock("div", {
                          key,
                          class: "mt-1 text-xs text-red-600 dark:text-red-400"
                        }, toDisplayString(error), 1);
                      }), 128))
                    ])) : createCommentVNode("", true),
                    createVNode("div", { class: "flex justify-end" }, [
                      createVNode(_sfc_main$1, {
                        disabled: unref(form).processing,
                        class: { "opacity-50 cursor-not-allowed": unref(form).processing }
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(unref(form).processing ? "Сохранение..." : unref(t)("save")), 1)
                        ]),
                        _: 1
                      }, 8, ["disabled", "class"])
                    ])
                  ], 32)
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/RobotEditPage.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
