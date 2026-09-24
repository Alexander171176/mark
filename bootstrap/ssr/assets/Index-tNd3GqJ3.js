import { mergeProps, unref, useSSRContext, ref, watch, computed, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, createCommentVNode } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderAttr, ssrRenderList, ssrRenderComponent } from "vue/server-renderer";
import { useToast } from "vue-toastification";
import { useI18n } from "vue-i18n";
import { router } from "@inertiajs/vue3";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$g } from "./DangerModal-DjIe5VDg.js";
import { _ as _sfc_main$a, a as _sfc_main$d, b as _sfc_main$e } from "./ItemsPerPageSelect-DOO-E4Z0.js";
import { _ as _sfc_main$9, a as _sfc_main$f } from "./ServerSearchInput-CxQUCmzV.js";
import { _ as _sfc_main$8 } from "./SearchInput-xZSYbbms.js";
import { _ as _sfc_main$c } from "./CountTable-p8tyXGUL.js";
import { _ as _sfc_main$6 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$b } from "./ServerItemsPerPageSelect-B_wSkKlU.js";
import { _ as _sfc_main$7 } from "./ProcessingModeSwitcher-BJvzFf6_.js";
import { _ as _sfc_main$4 } from "./IconEdit-Bw90OQvk.js";
import { _ as _sfc_main$5 } from "./DeleteIconButton-DLv2Mr1x.js";
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
const _sfc_main$3 = {
  __name: "SortSelect",
  __ssrInlineRender: true,
  props: {
    sortParam: { type: String, default: "nameAsc" }
  },
  emits: ["update:sortParam"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex justify-center items-center h-fit my-2" }, _attrs))}><label for="sortParam" class="hidden lg:block sm:mr-2 tracking-wider text-sm font-semibold text-slate-600 dark:text-slate-100">${ssrInterpolate(unref(t)("sort"))}</label><select id="sortParam"${ssrRenderAttr("value", __props.sortParam)} class="w-50 px-3 py-0.5 form-select bg-white dark:bg-gray-200 text-gray-600 dark:text-gray-900 border border-slate-400 dark:border-slate-600 rounded-sm shadow-sm"><option value="idDesc">${ssrInterpolate(unref(t)("idDesc"))}</option><option value="idAsc">${ssrInterpolate(unref(t)("idAsc"))}</option><option disabled>─────────────</option><option value="nameAsc">${ssrInterpolate(unref(t)("name"))} A→Z</option><option value="nameDesc">${ssrInterpolate(unref(t)("name"))} Z→A</option><option disabled>─────────────</option><option value="guardNameAsc">Guard A→Z</option><option value="guardNameDesc">Guard Z→A</option><option disabled>─────────────</option><option value="permissionsDesc">${ssrInterpolate(unref(t)("permissions"))} 9→0</option><option value="permissionsAsc">${ssrInterpolate(unref(t)("permissions"))} 0→9</option><option disabled>─────────────</option><option value="createdAtDesc">${ssrInterpolate(unref(t)("createdAt"))} ↓</option><option value="createdAtAsc">${ssrInterpolate(unref(t)("createdAt"))} ↑</option><option value="updatedAtDesc">${ssrInterpolate(unref(t)("updatedAt"))} ↓</option><option value="updatedAtAsc">${ssrInterpolate(unref(t)("updatedAt"))} ↑</option><option disabled>─────────────</option></select></div>`);
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Role/Sort/SortSelect.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const _sfc_main$2 = {
  __name: "RoleTable",
  __ssrInlineRender: true,
  props: {
    roles: { type: Array, default: () => [] }
  },
  emits: ["delete"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const emit = __emit;
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-200 dark:border-slate-600 relative" }, _attrs))}><div class="overflow-x-auto">`);
      if (__props.roles.length > 0) {
        _push(`<table class="table-auto w-full text-slate-700 dark:text-slate-100"><thead class="text-sm font-semibold uppercase bg-slate-200 dark:bg-cyan-900 border border-solid border-gray-300 dark:border-gray-700"><tr><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("name"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">${ssrInterpolate(unref(t)("permissions"))}</div></th><th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-end">${ssrInterpolate(unref(t)("actions"))}</div></th></tr></thead><tbody><!--[-->`);
        ssrRenderList(__props.roles, (role) => {
          _push(`<tr class="text-sm font-semibold border-b-2 hover:bg-slate-100 dark:hover:bg-cyan-800"><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"><div class="text-left text-teal-600 dark:text-violet-200">${ssrInterpolate(role.name)}</div></td><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"><div class="flex flex-wrap gap-1"><!--[-->`);
          ssrRenderList(role.permissions || [], (perm) => {
            _push(`<span class="px-1 py-0 bg-slate-100 dark:bg-slate-800 border border-dashed border-gray-400 text-orange-500 dark:text-orange-200 text-xs">${ssrInterpolate(perm.name)}</span>`);
          });
          _push(`<!--]-->`);
          if (!role.permissions || role.permissions.length === 0) {
            _push(`<span class="text-xs italic text-gray-500"> — </span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></td><td class="px-2 first:pl-5 last:pr-5 py-1 whitespace-nowrap"><div class="flex justify-end space-x-2">`);
          _push(ssrRenderComponent(_sfc_main$4, {
            href: _ctx.route("admin.roles.edit", role.id)
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$5, {
            onClick: ($event) => emit("delete", role.id, role.name)
          }, null, _parent));
          _push(`</div></td></tr>`);
        });
        _push(`<!--]--></tbody></table>`);
      } else {
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Role/Table/RoleTable.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "RoleCardGrid",
  __ssrInlineRender: true,
  props: {
    roles: { type: Array, default: () => [] }
  },
  emits: ["delete"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const emit = __emit;
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "bg-white dark:bg-slate-700 shadow-lg rounded-sm border border-slate-400 dark:border-slate-500 relative" }, _attrs))}><div class="flex items-center justify-between px-3 py-2 border-b border-slate-400 dark:border-slate-500"><div class="text-xs text-slate-600 dark:text-slate-200">${ssrInterpolate(unref(t)("roles"))}: ${ssrInterpolate(__props.roles.length)}</div></div>`);
      if (__props.roles.length) {
        _push(`<div class="p-3"><div class="grid gap-3 grid-cols-1 sm:grid-cols-2 lg:grid-cols-4"><!--[-->`);
        ssrRenderList(__props.roles, (role) => {
          _push(`<article class="relative flex flex-col h-full rounded-md border border-slate-400 dark:border-slate-500 bg-slate-50/70 dark:bg-slate-800/80 shadow-sm hover:shadow-md transition-shadow duration-150"><header class="flex items-center justify-between px-2 py-1 border-b border-dashed border-slate-400 dark:border-slate-500"><div class="w-full flex flex-row items-center justify-start"><span class="w-fit text-[10px] font-semibold px-1.5 py-0.5 rounded-sm border border-gray-400 bg-slate-200 dark:bg-slate-700 text-slate-800 dark:text-blue-100 whitespace-nowrap"> ID: ${ssrInterpolate(role.id)}</span><span class="w-full text-[14px] font-semibold text-center text-teal-700 dark:text-teal-200 line-clamp-2"${ssrRenderAttr("title", role.name)}>${ssrInterpolate(role.name)}</span></div></header><div class="flex-1 px-3 py-2"><div class="text-[14px] text-slate-700 dark:text-slate-100 mb-1 text-center">${ssrInterpolate(unref(t)("permissions"))}</div><div class="flex flex-wrap justify-center gap-1"><!--[-->`);
          ssrRenderList(role.permissions || [], (perm) => {
            _push(`<span class="px-1.5 py-0.5 bg-slate-100 dark:bg-slate-800 border border-dashed border-gray-400 text-orange-500 dark:text-orange-200 text-[12px] font-semibold rounded-sm">${ssrInterpolate(perm.name)}</span>`);
          });
          _push(`<!--]-->`);
          if (!role.permissions || role.permissions.length === 0) {
            _push(`<span class="text-[10px] italic text-gray-500 dark:text-gray-400"> — </span>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div></div><footer class="px-3 py-2 border-t border-dashed border-slate-400 dark:border-slate-500"><div class="flex items-center justify-center space-x-2">`);
          _push(ssrRenderComponent(_sfc_main$4, {
            href: _ctx.route("admin.roles.edit", role.id)
          }, null, _parent));
          _push(ssrRenderComponent(_sfc_main$5, {
            onClick: ($event) => emit("delete", role.id, role.name)
          }, null, _parent));
          _push(`</div></footer></article>`);
        });
        _push(`<!--]--></div></div>`);
      } else {
        _push(`<div class="p-5 text-center text-slate-700 dark:text-slate-100">${ssrInterpolate(unref(t)("noData"))}</div>`);
      }
      _push(`</div>`);
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Admin/System/Role/View/RoleCardGrid.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "Index",
  __ssrInlineRender: true,
  props: {
    adminSystemRolesProcessingMode: { type: String, default: "frontend" },
    useServerProcessing: { type: Boolean, default: false },
    roles: { type: [Array, Object], default: () => [] },
    rolesCount: { type: Number, default: 0 },
    adminSystemRolesPerPage: { type: Number, default: 20 },
    adminSystemRolesDefaultSort: { type: String, default: "nameAsc" },
    adminCountRoles: { type: Number, default: 20 },
    adminSortRoles: { type: String, default: "nameAsc" },
    sortParam: { type: String, default: "" },
    search: { type: String, default: "" }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const viewMode = ref(localStorage.getItem("admin_view_mode_roles") || "table");
    watch(viewMode, (val) => {
      localStorage.setItem("admin_view_mode_roles", val);
    });
    const rolesList = computed(() => {
      var _a;
      if (Array.isArray(props.roles)) return props.roles;
      if (Array.isArray((_a = props.roles) == null ? void 0 : _a.data)) return props.roles.data;
      return [];
    });
    const localRoles = ref([]);
    watch(
      rolesList,
      (newVal) => {
        localRoles.value = JSON.parse(JSON.stringify(newVal || []));
      },
      { immediate: true, deep: true }
    );
    const itemsPerPage = ref(props.adminSystemRolesPerPage || props.adminCountRoles || 20);
    watch(itemsPerPage, (newVal) => {
      router.put(route("admin.settings.updateAdminCountRoles"), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => toast.info(`Показ ${newVal} элементов на странице.`),
        onError: (errors) => toast.error(errors.value || "Ошибка обновления кол-ва элементов.")
      });
    });
    const sortParam = ref(
      props.sortParam || props.adminSystemRolesDefaultSort || props.adminSortRoles || "nameAsc"
    );
    const currentPage = ref(1);
    const searchQuery = ref(props.search || "");
    watch(sortParam, (newVal) => {
      currentPage.value = 1;
      router.put(route("admin.settings.updateAdminSortRoles"), { value: newVal }, {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          if (props.useServerProcessing) {
            router.get(
              window.location.pathname,
              {
                ...Object.fromEntries(new URLSearchParams(window.location.search)),
                sort: newVal || void 0,
                page: void 0
              },
              {
                preserveScroll: true,
                preserveState: false,
                replace: true
              }
            );
          }
          toast.info("Сортировка успешно изменена");
        },
        onError: (errors) => {
          toast.error(errors.value || "Ошибка обновления сортировки.");
        }
      });
    });
    const normalize = (value) => (value ?? "").toString().trim().toLowerCase();
    const safeNumber = (value) => {
      const number = Number(value);
      return Number.isFinite(number) ? number : 0;
    };
    const safeDate = (value) => {
      const time = new Date(value || 0).getTime();
      return Number.isFinite(time) ? time : 0;
    };
    const getPermissionsText = (role) => {
      const permissions = Array.isArray(role == null ? void 0 : role.permissions) ? role.permissions : [];
      return permissions.map((permission) => (permission == null ? void 0 : permission.name) || "").filter(Boolean).join(" ");
    };
    const byNumberAsc = (field) => (a, b) => safeNumber(a == null ? void 0 : a[field]) - safeNumber(b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byNumberDesc = (field) => (a, b) => safeNumber(b == null ? void 0 : b[field]) - safeNumber(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringAsc = (field) => (a, b) => normalize(a == null ? void 0 : a[field]).localeCompare(normalize(b == null ? void 0 : b[field])) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byStringDesc = (field) => (a, b) => normalize(b == null ? void 0 : b[field]).localeCompare(normalize(a == null ? void 0 : a[field])) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateAsc = (field) => (a, b) => safeDate(a == null ? void 0 : a[field]) - safeDate(b == null ? void 0 : b[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const byDateDesc = (field) => (a, b) => safeDate(b == null ? void 0 : b[field]) - safeDate(a == null ? void 0 : a[field]) || safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id);
    const sortRoles = (items) => {
      const list = (items || []).slice();
      const sortMap = {
        idAsc: (a, b) => safeNumber(a == null ? void 0 : a.id) - safeNumber(b == null ? void 0 : b.id),
        idDesc: (a, b) => safeNumber(b == null ? void 0 : b.id) - safeNumber(a == null ? void 0 : a.id),
        name: byStringAsc("name"),
        nameAsc: byStringAsc("name"),
        nameDesc: byStringDesc("name"),
        guardNameAsc: byStringAsc("guard_name"),
        guardNameDesc: byStringDesc("guard_name"),
        permissionsAsc: byNumberAsc("permissions_count"),
        permissionsDesc: byNumberDesc("permissions_count"),
        createdAtAsc: byDateAsc("created_at"),
        createdAtDesc: byDateDesc("created_at"),
        updatedAtAsc: byDateAsc("updated_at"),
        updatedAtDesc: byDateDesc("updated_at")
      };
      return sortMap[sortParam.value] ? list.sort(sortMap[sortParam.value]) : list;
    };
    const searchWords = computed(
      () => normalize(searchQuery.value).split(/[\s:#№,"'«»(){}\[\].!?/\\|;+=*&^%$@<>`~_-]+/u).map((word) => word.trim()).filter(Boolean)
    );
    const filteredRoles = computed(() => {
      let filtered = localRoles.value || [];
      if (!searchWords.value.length) {
        return sortRoles(filtered);
      }
      filtered = filtered.filter((role) => {
        const searchableValues = [
          normalize(role == null ? void 0 : role.name),
          normalize(role == null ? void 0 : role.guard_name),
          normalize(getPermissionsText(role))
        ];
        return searchWords.value.every((word) => {
          if (/^\d+$/.test(word) && safeNumber(role == null ? void 0 : role.id) === Number(word)) {
            return true;
          }
          return searchableValues.some((value) => value.includes(word));
        });
      });
      return sortRoles(filtered);
    });
    const paginatedRoles = computed(() => {
      const per = Number(itemsPerPage.value || 20);
      const start = (currentPage.value - 1) * per;
      return filteredRoles.value.slice(start, start + per);
    });
    const displayedRoles = computed(() => {
      return props.useServerProcessing ? rolesList.value : paginatedRoles.value;
    });
    watch([itemsPerPage, searchQuery], () => {
      currentPage.value = 1;
    });
    const showConfirmDeleteModal = ref(false);
    const roleToDeleteId = ref(null);
    const roleToDeleteName = ref("");
    const confirmDelete = (id, name = "") => {
      roleToDeleteId.value = id;
      roleToDeleteName.value = name;
      showConfirmDeleteModal.value = true;
    };
    const closeModal = () => {
      showConfirmDeleteModal.value = false;
      roleToDeleteId.value = null;
      roleToDeleteName.value = "";
    };
    const deleteRole = () => {
      if (roleToDeleteId.value === null) return;
      const idToDelete = roleToDeleteId.value;
      const nameToDelete = roleToDeleteName.value;
      router.delete(route("admin.roles.destroy", { role: idToDelete }), {
        preserveScroll: true,
        preserveState: false,
        onSuccess: () => {
          toast.success(`Роль "${nameToDelete || "ID: " + idToDelete}" удалена.`);
        },
        onError: (errors) => {
          const errorKey = Object.keys(errors || {})[0];
          const errorMsg = errors.general || errors[errorKey] || "Произошла ошибка при удалении.";
          toast.error(`${errorMsg} (Роль: ${nameToDelete || "ID: " + idToDelete})`);
        },
        onFinish: () => closeModal()
      });
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("roles")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("roles"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("roles")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("roles")), 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-2 py-2 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-3 gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              href: _ctx.route("admin.roles.create")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16"${_scopeId2}><path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current opacity-50 shrink-0",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" })
                    ]))
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("addRole"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("addRole")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              "setting-key": "adminSystemRolesProcessingMode",
              mode: __props.adminSystemRolesProcessingMode,
              "use-server-processing": __props.useServerProcessing,
              total: __props.rolesCount
            }, null, _parent2, _scopeId));
            _push2(`</div>`);
            if (__props.rolesCount && !__props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$8, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                placeholder: unref(t)("searchByName")
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.rolesCount && __props.useServerProcessing) {
              _push2(ssrRenderComponent(_sfc_main$9, {
                modelValue: searchQuery.value,
                "onUpdate:modelValue": ($event) => searchQuery.value = $event
              }, null, _parent2, _scopeId));
            } else {
              _push2(`<!---->`);
            }
            if (__props.rolesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$a, {
                  "items-per-page": itemsPerPage.value,
                  "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$b, {
                  "items-per-page": itemsPerPage.value,
                  "update-route": "admin.settings.updateAdminCountRoles"
                }, null, _parent2, _scopeId));
              }
              _push2(ssrRenderComponent(_sfc_main$3, {
                sortParam: sortParam.value,
                "onUpdate:sortParam": (val) => sortParam.value = val
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.rolesCount) {
              _push2(`<div class="flex justify-between items-center flex-col md:flex-row my-3"${_scopeId}>`);
              _push2(ssrRenderComponent(_sfc_main$c, null, {
                default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                  if (_push3) {
                    _push3(`${ssrInterpolate(__props.rolesCount)}`);
                  } else {
                    return [
                      createTextVNode(toDisplayString(__props.rolesCount), 1)
                    ];
                  }
                }),
                _: 1
              }, _parent2, _scopeId));
              _push2(ssrRenderComponent(_sfc_main$d, {
                viewMode: viewMode.value,
                "onUpdate:viewMode": ($event) => viewMode.value = $event
              }, null, _parent2, _scopeId));
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (__props.rolesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredRoles.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$f, { pagination: __props.roles }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            if (viewMode.value === "table") {
              _push2(ssrRenderComponent(_sfc_main$2, {
                roles: displayedRoles.value,
                onDelete: confirmDelete
              }, null, _parent2, _scopeId));
            } else {
              _push2(ssrRenderComponent(_sfc_main$1, {
                roles: displayedRoles.value,
                onDelete: confirmDelete
              }, null, _parent2, _scopeId));
            }
            if (__props.rolesCount) {
              _push2(`<div class="flex justify-center items-center flex-col md:flex-row mt-3"${_scopeId}>`);
              if (!__props.useServerProcessing) {
                _push2(ssrRenderComponent(_sfc_main$e, {
                  "current-page": currentPage.value,
                  "items-per-page": itemsPerPage.value,
                  "total-items": filteredRoles.value.length,
                  "onUpdate:currentPage": ($event) => currentPage.value = $event
                }, null, _parent2, _scopeId));
              } else {
                _push2(ssrRenderComponent(_sfc_main$f, { pagination: __props.roles }, null, _parent2, _scopeId));
              }
              _push2(`</div>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$g, {
              show: showConfirmDeleteModal.value,
              onCancel: closeModal,
              onConfirm: deleteRole,
              cancelText: unref(t)("cancel"),
              confirmText: unref(t)("yesDelete"),
              onClose: closeModal
            }, null, _parent2, _scopeId));
          } else {
            return [
              createVNode("div", { class: "px-2 py-2 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 overflow-hidden shadow-md shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-3 gap-3" }, [
                    createVNode(_sfc_main$6, {
                      href: _ctx.route("admin.roles.create")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current opacity-50 shrink-0",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" })
                        ]))
                      ]),
                      default: withCtx(() => [
                        createTextVNode(" " + toDisplayString(unref(t)("addRole")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"]),
                    createVNode(_sfc_main$7, {
                      "setting-key": "adminSystemRolesProcessingMode",
                      mode: __props.adminSystemRolesProcessingMode,
                      "use-server-processing": __props.useServerProcessing,
                      total: __props.rolesCount
                    }, null, 8, ["mode", "use-server-processing", "total"])
                  ]),
                  __props.rolesCount && !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$8, {
                    key: 0,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event,
                    placeholder: unref(t)("searchByName")
                  }, null, 8, ["modelValue", "onUpdate:modelValue", "placeholder"])) : createCommentVNode("", true),
                  __props.rolesCount && __props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$9, {
                    key: 1,
                    modelValue: searchQuery.value,
                    "onUpdate:modelValue": ($event) => searchQuery.value = $event
                  }, null, 8, ["modelValue", "onUpdate:modelValue"])) : createCommentVNode("", true),
                  __props.rolesCount ? (openBlock(), createBlock("div", {
                    key: 2,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$a, {
                      key: 0,
                      "items-per-page": itemsPerPage.value,
                      "onUpdate:itemsPerPage": ($event) => itemsPerPage.value = $event
                    }, null, 8, ["items-per-page", "onUpdate:itemsPerPage"])) : (openBlock(), createBlock(_sfc_main$b, {
                      key: 1,
                      "items-per-page": itemsPerPage.value,
                      "update-route": "admin.settings.updateAdminCountRoles"
                    }, null, 8, ["items-per-page"])),
                    createVNode(_sfc_main$3, {
                      sortParam: sortParam.value,
                      "onUpdate:sortParam": (val) => sortParam.value = val
                    }, null, 8, ["sortParam", "onUpdate:sortParam"])
                  ])) : createCommentVNode("", true),
                  __props.rolesCount ? (openBlock(), createBlock("div", {
                    key: 3,
                    class: "flex justify-between items-center flex-col md:flex-row my-3"
                  }, [
                    createVNode(_sfc_main$c, null, {
                      default: withCtx(() => [
                        createTextVNode(toDisplayString(__props.rolesCount), 1)
                      ]),
                      _: 1
                    }),
                    createVNode(_sfc_main$d, {
                      viewMode: viewMode.value,
                      "onUpdate:viewMode": ($event) => viewMode.value = $event
                    }, null, 8, ["viewMode", "onUpdate:viewMode"])
                  ])) : createCommentVNode("", true),
                  __props.rolesCount ? (openBlock(), createBlock("div", {
                    key: 4,
                    class: "flex justify-center items-center flex-col md:flex-row"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredRoles.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$f, {
                      key: 1,
                      pagination: __props.roles
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true),
                  viewMode.value === "table" ? (openBlock(), createBlock(_sfc_main$2, {
                    key: 5,
                    roles: displayedRoles.value,
                    onDelete: confirmDelete
                  }, null, 8, ["roles"])) : (openBlock(), createBlock(_sfc_main$1, {
                    key: 6,
                    roles: displayedRoles.value,
                    onDelete: confirmDelete
                  }, null, 8, ["roles"])),
                  __props.rolesCount ? (openBlock(), createBlock("div", {
                    key: 7,
                    class: "flex justify-center items-center flex-col md:flex-row mt-3"
                  }, [
                    !__props.useServerProcessing ? (openBlock(), createBlock(_sfc_main$e, {
                      key: 0,
                      "current-page": currentPage.value,
                      "items-per-page": itemsPerPage.value,
                      "total-items": filteredRoles.value.length,
                      "onUpdate:currentPage": ($event) => currentPage.value = $event
                    }, null, 8, ["current-page", "items-per-page", "total-items", "onUpdate:currentPage"])) : (openBlock(), createBlock(_sfc_main$f, {
                      key: 1,
                      pagination: __props.roles
                    }, null, 8, ["pagination"]))
                  ])) : createCommentVNode("", true)
                ])
              ]),
              createVNode(_sfc_main$g, {
                show: showConfirmDeleteModal.value,
                onCancel: closeModal,
                onConfirm: deleteRole,
                cancelText: unref(t)("cancel"),
                confirmText: unref(t)("yesDelete"),
                onClose: closeModal
              }, null, 8, ["show", "cancelText", "confirmText"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/System/Roles/Index.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
