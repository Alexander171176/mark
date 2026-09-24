import { computed, watch, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, withDirectives, Fragment, renderList, vModelSelect, createCommentVNode, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$8 } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$2 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$5 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$4 } from "./InputProgress-BlxWms-V.js";
import { _ as _sfc_main$7 } from "./MetaDescTextarea-D59BR8J3.js";
import { _ as _sfc_main$3 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$6 } from "./SelectEntity-DUeUgJcQ.js";
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
  __name: "Edit",
  __ssrInlineRender: true,
  props: {
    enrollment: {
      type: Object,
      required: true
    },
    users: {
      type: Array,
      default: () => []
    },
    courses: {
      type: Array,
      default: () => []
    },
    schedules: {
      type: Array,
      default: () => []
    },
    orders: {
      type: Array,
      default: () => []
    },
    currentLocale: {
      type: String,
      default: "ru"
    },
    availableLocales: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const normalizeDateTimeLocal = (value) => {
      if (!value) {
        return "";
      }
      const str = String(value);
      const match = str.match(
        /^(\d{4}-\d{2}-\d{2}T\d{2}:\d{2})/
      );
      return match ? match[1] : str;
    };
    const form = useForm({
      _method: "PUT",
      user_id: props.enrollment.user_id ?? null,
      school_course_id: props.enrollment.school_course_id ?? null,
      school_course_schedule_id: props.enrollment.school_course_schedule_id ?? null,
      school_order_id: props.enrollment.school_order_id ?? null,
      status: props.enrollment.status ?? "active",
      started_at: normalizeDateTimeLocal(
        props.enrollment.started_at
      ),
      expires_at: normalizeDateTimeLocal(
        props.enrollment.expires_at
      ),
      completed_at: normalizeDateTimeLocal(
        props.enrollment.completed_at
      ),
      progress_percent: props.enrollment.progress_percent ?? 0,
      notes: props.enrollment.notes ?? "",
      meta: props.enrollment.meta ?? null
    });
    const startedAtModel = computed({
      get: () => normalizeDateTimeLocal(
        form.started_at
      ),
      set: (value) => {
        form.started_at = value;
      }
    });
    const expiresAtModel = computed({
      get: () => normalizeDateTimeLocal(
        form.expires_at
      ),
      set: (value) => {
        form.expires_at = value;
      }
    });
    const completedAtModel = computed({
      get: () => normalizeDateTimeLocal(
        form.completed_at
      ),
      set: (value) => {
        form.completed_at = value;
      }
    });
    const dateLocale = computed(
      () => props.currentLocale || "ru"
    );
    const formatDateTime = (value) => {
      if (!value) {
        return "";
      }
      try {
        return new Intl.DateTimeFormat(
          dateLocale.value,
          {
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit"
          }
        ).format(
          new Date(value)
        );
      } catch {
        return String(value);
      }
    };
    const statusOptions = [
      "active",
      "completed",
      "cancelled",
      "expired",
      "paused"
    ];
    const statusLabelKeyMap = {
      active: "statusEnrollmentActive",
      completed: "statusEnrollmentCompleted",
      cancelled: "statusEnrollmentCancelled",
      expired: "statusEnrollmentExpired",
      paused: "statusEnrollmentPaused"
    };
    const getStatusLabel = (status) => {
      if (!status) {
        return "—";
      }
      const key = statusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getCourseTitle = (course) => {
      var _a;
      return ((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.title) || (course == null ? void 0 : course.slug) || `${t("course")} #${(course == null ? void 0 : course.id) ?? "—"}`;
    };
    const getScheduleTitle = (schedule) => {
      var _a;
      return ((_a = schedule == null ? void 0 : schedule.translation) == null ? void 0 : _a.title) || (schedule == null ? void 0 : schedule.slug) || `${t("schedule")} #${(schedule == null ? void 0 : schedule.id) ?? "—"}`;
    };
    const userOptions = computed(
      () => props.users.map(
        (user) => ({
          id: user.id,
          label: user.name ? `[ID: ${user.id}] ${user.name}${user.email ? ` (${user.email})` : ""}` : `[ID: ${user.id}]`
        })
      )
    );
    const courseOptions = computed(
      () => props.courses.map(
        (course) => ({
          id: course.id,
          label: `[ID: ${course.id}] ${getCourseTitle(course)}`
        })
      )
    );
    const scheduleOptions = computed(
      () => props.schedules.map(
        (schedule) => {
          const starts = formatDateTime(
            schedule.starts_at
          );
          const enrollStart = formatDateTime(
            schedule.enroll_starts_at
          );
          const enrollEnd = formatDateTime(
            schedule.enroll_ends_at
          );
          const courseTitle = schedule.course ? getCourseTitle(
            schedule.course
          ) : null;
          const parts = [
            `[ID: ${schedule.id}] ${getScheduleTitle(schedule)}`,
            courseTitle ? `${t("course")}: ${courseTitle}` : null,
            starts ? `${t("start")}: ${starts}` : null,
            enrollStart || enrollEnd ? `${t("enrollmentPeriod")}: ${t("from")} ${enrollStart || "—"} ${t("to")} ${enrollEnd || "—"}` : null
          ].filter(Boolean);
          return {
            id: schedule.id,
            label: parts.join("\n")
          };
        }
      )
    );
    const orderOptions = computed(
      () => props.orders.map(
        (order) => {
          var _a, _b;
          const date = formatDateTime(
            order.created_at
          );
          const numberPart = order.number ? `[ID: ${order.id}] №${order.number}` : `[ID: ${order.id}]`;
          const amountPart = order.total != null ? `${order.total} ${order.currency || ""}`.trim() : null;
          const userPart = order.buyer_name || ((_a = order.user) == null ? void 0 : _a.name);
          const emailPart = order.buyer_email || ((_b = order.user) == null ? void 0 : _b.email);
          const parts = [
            numberPart,
            date ? `${t("date")}: ${date}` : null,
            amountPart ? `${t("amount")}: ${amountPart}` : null,
            userPart ? `${t("buyer")}: ${userPart}${emailPart ? ` (${emailPart})` : ""}` : null
          ].filter(Boolean);
          return {
            id: order.id,
            label: parts.join("\n")
          };
        }
      )
    );
    const selectedSchedule = computed(
      () => props.schedules.find(
        (schedule) => Number(schedule.id) === Number(
          form.school_course_schedule_id
        )
      )
    );
    const selectedScheduleDetails = computed(() => {
      const schedule = selectedSchedule.value;
      if (!schedule) {
        return "";
      }
      const starts = formatDateTime(
        schedule.starts_at
      );
      const enrollStart = formatDateTime(
        schedule.enroll_starts_at
      );
      const enrollEnd = formatDateTime(
        schedule.enroll_ends_at
      );
      const courseTitle = schedule.course ? getCourseTitle(
        schedule.course
      ) : null;
      const parts = [
        `#${schedule.id} — ${getScheduleTitle(schedule)}`,
        courseTitle ? `${t("course")}: ${courseTitle}` : null,
        starts ? `${t("start")}: ${starts}` : null,
        enrollStart || enrollEnd ? `${t("enrollmentPeriod")}: ${t("from")} ${enrollStart || "—"} ${t("to")} ${enrollEnd || "—"}` : null
      ].filter(Boolean);
      return parts.join("\n");
    });
    const selectedOrder = computed(
      () => props.orders.find(
        (order) => Number(order.id) === Number(
          form.school_order_id
        )
      )
    );
    const selectedOrderDetails = computed(() => {
      var _a, _b;
      const order = selectedOrder.value;
      if (!order) {
        return "";
      }
      const date = formatDateTime(
        order.created_at
      );
      const numberPart = order.number ? `#${order.id} — №${order.number}` : `#${order.id}`;
      const amountPart = order.total != null ? `${order.total} ${order.currency || ""}`.trim() : null;
      const userPart = order.buyer_name || ((_a = order.user) == null ? void 0 : _a.name);
      const emailPart = order.buyer_email || ((_b = order.user) == null ? void 0 : _b.email);
      const parts = [
        numberPart,
        date ? `${t("date")}: ${date}` : null,
        amountPart ? `${t("amount")}: ${amountPart}` : null,
        userPart ? `${t("buyer")}: ${userPart}${emailPart ? ` (${emailPart})` : ""}` : null
      ].filter(Boolean);
      return parts.join("\n");
    });
    watch(
      () => form.school_course_schedule_id,
      () => {
        var _a;
        const schedule = selectedSchedule.value;
        if (!schedule) {
          return;
        }
        const courseId = schedule.school_course_id || ((_a = schedule.course) == null ? void 0 : _a.id);
        if (!courseId) {
          return;
        }
        const newCourseId = Number(courseId);
        if (!form.school_course_id) {
          form.school_course_id = newCourseId;
          return;
        }
        if (Number(
          form.school_course_id
        ) !== newCourseId) {
          form.school_course_id = newCourseId;
          toast.info(
            t("courseAutoSelectedFromSchedule")
          );
        }
      }
    );
    watch(
      () => form.school_order_id,
      () => {
        const order = selectedOrder.value;
        if (!order) {
          return;
        }
        if (order.user_id) {
          const newUserId = Number(
            order.user_id
          );
          if (!form.user_id) {
            form.user_id = newUserId;
          } else if (Number(form.user_id) !== newUserId) {
            form.user_id = newUserId;
            toast.info(
              t("userAutoSelectedFromOrder")
            );
          }
        }
        if (order.school_course_id && !form.school_course_id) {
          form.school_course_id = Number(
            order.school_course_id
          );
        }
        if (order.school_course_schedule_id && !form.school_course_schedule_id) {
          form.school_course_schedule_id = Number(
            order.school_course_schedule_id
          );
        }
      }
    );
    const submitForm = () => {
      form.transform(
        (data) => ({
          ...data,
          user_id: data.user_id || null,
          school_course_id: data.school_course_id || null,
          school_course_schedule_id: data.school_course_schedule_id || null,
          school_order_id: data.school_order_id || null,
          progress_percent: Number(
            data.progress_percent || 0
          )
        })
      );
      form.post(
        route(
          "admin.schoolEnrollments.update",
          {
            schoolEnrollment: props.enrollment.id
          }
        ),
        {
          preserveScroll: true,
          onSuccess: () => {
            toast.success(
              t("enrollmentUpdatedSuccessfully")
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(
              errors || {}
            )[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || t("checkFormFields")
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editEnrollment")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editEnrollment"))} [ID: ${ssrInterpolate(__props.enrollment.id)}] `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editEnrollment")) + " [ID: " + toDisplayString(__props.enrollment.id) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editEnrollment")) + " [ID: " + toDisplayString(__props.enrollment.id) + "] ", 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-4 py-8 w-full max-w-12xl mx-auto sm:px-6 lg:px-8"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolEnrollments.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7
                                       1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4
                                       5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6
                                       8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2
                                       .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0
                                       3.6-.7 4.9-2l2.2 2.2.8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7\n                                       1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4\n                                       5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6\n                                       8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2\n                                       .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0\n                                       3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                    ]))
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("back"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div><form class="p-3 w-full space-y-3"${_scopeId}><div class="grid grid-cols-1 md:grid-cols-2 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "status" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<span class="text-red-500 dark:text-red-300 font-semibold"${_scopeId2}> * </span> ${ssrInterpolate(unref(t)("status"))}`);
                } else {
                  return [
                    createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                    createTextVNode(" " + toDisplayString(unref(t)("status")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<select id="status" class="w-full px-3 py-0.5 form-select bg-white dark:bg-cyan-800 text-gray-600 dark:text-slate-100 rounded-sm shadow-sm border border-slate-400 dark:border-slate-600"${_scopeId}><!--[-->`);
            ssrRenderList(statusOptions, (status) => {
              _push2(`<option${ssrRenderAttr("value", status)}${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, status) : ssrLooseEqual(unref(form).status, status)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(getStatusLabel(status))}</option>`);
            });
            _push2(`<!--]--></select>`);
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.status
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-end"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "progress_percent" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("progress"))}, % `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("progress")) + ", % ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$4, {
              id: "progress_percent",
              modelValue: unref(form).progress_percent,
              "onUpdate:modelValue": ($event) => unref(form).progress_percent = $event,
              class: "w-24"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.progress_percent
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 md:grid-cols-3 gap-4"${_scopeId}><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "started_at" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("accessStartDate"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("accessStartDate")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "started_at",
              modelValue: startedAtModel.value,
              "onUpdate:modelValue": ($event) => startedAtModel.value = $event,
              type: "datetime-local",
              autocomplete: "off",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.started_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "expires_at" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("accessEndDate"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("accessEndDate")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "expires_at",
              modelValue: expiresAtModel.value,
              "onUpdate:modelValue": ($event) => expiresAtModel.value = $event,
              type: "datetime-local",
              autocomplete: "off",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.expires_at
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "completed_at" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("shortCompleted"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("shortCompleted")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "completed_at",
              modelValue: completedAtModel.value,
              "onUpdate:modelValue": ($event) => completedAtModel.value = $event,
              type: "datetime-local",
              autocomplete: "off",
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.completed_at
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="grid grid-cols-1 gap-4"${_scopeId}><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "school_order_id",
              modelValue: unref(form).school_order_id,
              "onUpdate:modelValue": ($event) => unref(form).school_order_id = $event,
              label: unref(t)("order"),
              options: orderOptions.value,
              "error-message": unref(form).errors.school_order_id,
              nullable: true,
              placeholder: unref(t)("notSelected")
            }, null, _parent2, _scopeId));
            if (selectedOrderDetails.value) {
              _push2(`<p class="mt-1 text-xs text-fuchsia-500 dark:text-fuchsia-200 font-semibold whitespace-pre-line"${_scopeId}>${ssrInterpolate(selectedOrderDetails.value)}</p>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div><div${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "school_course_schedule_id",
              modelValue: unref(form).school_course_schedule_id,
              "onUpdate:modelValue": ($event) => unref(form).school_course_schedule_id = $event,
              label: unref(t)("schedule"),
              options: scheduleOptions.value,
              "error-message": unref(form).errors.school_course_schedule_id,
              nullable: true,
              placeholder: unref(t)("notSelected")
            }, null, _parent2, _scopeId));
            if (selectedScheduleDetails.value) {
              _push2(`<p class="mt-1 text-xs text-fuchsia-500 dark:text-fuchsia-200 font-semibold whitespace-pre-line"${_scopeId}>${ssrInterpolate(selectedScheduleDetails.value)}</p>`);
            } else {
              _push2(`<!---->`);
            }
            _push2(`</div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "user_id",
              modelValue: unref(form).user_id,
              "onUpdate:modelValue": ($event) => unref(form).user_id = $event,
              label: `${unref(t)("user")} (${unref(t)("autoCorrect")})`,
              required: true,
              options: userOptions.value,
              "error-message": unref(form).errors.user_id,
              placeholder: unref(t)("select")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "school_course_id",
              modelValue: unref(form).school_course_id,
              "onUpdate:modelValue": ($event) => unref(form).school_course_id = $event,
              label: `${unref(t)("course")} (${unref(t)("autoCorrect")})`,
              required: true,
              options: courseOptions.value,
              "error-message": unref(form).errors.school_course_id,
              placeholder: unref(t)("select")
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col items-start"${_scopeId}><div class="flex justify-between w-full"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, { for: "notes" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("notes"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("notes")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<div class="text-md text-gray-900 dark:text-gray-400 mt-1"${_scopeId}>${ssrInterpolate(unref(form).notes.length)} / 255 ${ssrInterpolate(unref(t)("characters"))}</div></div>`);
            _push2(ssrRenderComponent(_sfc_main$7, {
              id: "notes",
              modelValue: unref(form).notes,
              "onUpdate:modelValue": ($event) => unref(form).notes = $event,
              class: "w-full"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              class: "mt-2",
              message: unref(form).errors.notes
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex items-center justify-center gap-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolEnrollments.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7
                                       1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4
                                       5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6
                                       8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2
                                       .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0
                                       3.6-.7 4.9-2l2.2 2.2.8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7\n                                       1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4\n                                       5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6\n                                       8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2\n                                       .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0\n                                       3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                    ]))
                  ];
                }
              }),
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(` ${ssrInterpolate(unref(t)("back"))}`);
                } else {
                  return [
                    createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$8, {
              class: ["mb-0", {
                "opacity-25": unref(form).processing
              }],
              disabled: unref(form).processing
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("save"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("save")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`</div></form></div></div>`);
          } else {
            return [
              createVNode("div", { class: "px-4 py-8 w-full max-w-12xl mx-auto sm:px-6 lg:px-8" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400 bg-opacity-95 dark:bg-opacity-95" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-2" }, [
                    createVNode(_sfc_main$1, {
                      href: _ctx.route("admin.schoolEnrollments.index")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7\n                                       1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4\n                                       5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6\n                                       8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2\n                                       .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0\n                                       3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                        ]))
                      ]),
                      default: withCtx(() => [
                        createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ]),
                  createVNode("form", {
                    class: "p-3 w-full space-y-3",
                    onSubmit: withModifiers(submitForm, ["prevent"])
                  }, [
                    createVNode("div", { class: "grid grid-cols-1 md:grid-cols-2 gap-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "status" }, {
                          default: withCtx(() => [
                            createVNode("span", { class: "text-red-500 dark:text-red-300 font-semibold" }, " * "),
                            createTextVNode(" " + toDisplayString(unref(t)("status")), 1)
                          ]),
                          _: 1
                        }),
                        withDirectives(createVNode("select", {
                          id: "status",
                          "onUpdate:modelValue": ($event) => unref(form).status = $event,
                          class: "w-full px-3 py-0.5 form-select bg-white dark:bg-cyan-800 text-gray-600 dark:text-slate-100 rounded-sm shadow-sm border border-slate-400 dark:border-slate-600"
                        }, [
                          (openBlock(), createBlock(Fragment, null, renderList(statusOptions, (status) => {
                            return createVNode("option", {
                              key: status,
                              value: status
                            }, toDisplayString(getStatusLabel(status)), 9, ["value"]);
                          }), 64))
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).status]
                        ]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.status
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-end" }, [
                        createVNode(_sfc_main$2, { for: "progress_percent" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("progress")) + ", % ", 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$4, {
                          id: "progress_percent",
                          modelValue: unref(form).progress_percent,
                          "onUpdate:modelValue": ($event) => unref(form).progress_percent = $event,
                          class: "w-24"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.progress_percent
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 md:grid-cols-3 gap-4" }, [
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "started_at" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("accessStartDate")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$5, {
                          id: "started_at",
                          modelValue: startedAtModel.value,
                          "onUpdate:modelValue": ($event) => startedAtModel.value = $event,
                          type: "datetime-local",
                          autocomplete: "off",
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.started_at
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "expires_at" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("accessEndDate")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$5, {
                          id: "expires_at",
                          modelValue: expiresAtModel.value,
                          "onUpdate:modelValue": ($event) => expiresAtModel.value = $event,
                          type: "datetime-local",
                          autocomplete: "off",
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.expires_at
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col items-start" }, [
                        createVNode(_sfc_main$2, { for: "completed_at" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("shortCompleted")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$5, {
                          id: "completed_at",
                          modelValue: completedAtModel.value,
                          "onUpdate:modelValue": ($event) => completedAtModel.value = $event,
                          type: "datetime-local",
                          autocomplete: "off",
                          class: "w-full"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          class: "mt-2",
                          message: unref(form).errors.completed_at
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 gap-4" }, [
                      createVNode("div", null, [
                        createVNode(_sfc_main$6, {
                          id: "school_order_id",
                          modelValue: unref(form).school_order_id,
                          "onUpdate:modelValue": ($event) => unref(form).school_order_id = $event,
                          label: unref(t)("order"),
                          options: orderOptions.value,
                          "error-message": unref(form).errors.school_order_id,
                          nullable: true,
                          placeholder: unref(t)("notSelected")
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "label", "options", "error-message", "placeholder"]),
                        selectedOrderDetails.value ? (openBlock(), createBlock("p", {
                          key: 0,
                          class: "mt-1 text-xs text-fuchsia-500 dark:text-fuchsia-200 font-semibold whitespace-pre-line"
                        }, toDisplayString(selectedOrderDetails.value), 1)) : createCommentVNode("", true)
                      ]),
                      createVNode("div", null, [
                        createVNode(_sfc_main$6, {
                          id: "school_course_schedule_id",
                          modelValue: unref(form).school_course_schedule_id,
                          "onUpdate:modelValue": ($event) => unref(form).school_course_schedule_id = $event,
                          label: unref(t)("schedule"),
                          options: scheduleOptions.value,
                          "error-message": unref(form).errors.school_course_schedule_id,
                          nullable: true,
                          placeholder: unref(t)("notSelected")
                        }, null, 8, ["modelValue", "onUpdate:modelValue", "label", "options", "error-message", "placeholder"]),
                        selectedScheduleDetails.value ? (openBlock(), createBlock("p", {
                          key: 0,
                          class: "mt-1 text-xs text-fuchsia-500 dark:text-fuchsia-200 font-semibold whitespace-pre-line"
                        }, toDisplayString(selectedScheduleDetails.value), 1)) : createCommentVNode("", true)
                      ])
                    ]),
                    createVNode("div", { class: "grid grid-cols-1 md:grid-cols-2 gap-4" }, [
                      createVNode(_sfc_main$6, {
                        id: "user_id",
                        modelValue: unref(form).user_id,
                        "onUpdate:modelValue": ($event) => unref(form).user_id = $event,
                        label: `${unref(t)("user")} (${unref(t)("autoCorrect")})`,
                        required: true,
                        options: userOptions.value,
                        "error-message": unref(form).errors.user_id,
                        placeholder: unref(t)("select")
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "label", "options", "error-message", "placeholder"]),
                      createVNode(_sfc_main$6, {
                        id: "school_course_id",
                        modelValue: unref(form).school_course_id,
                        "onUpdate:modelValue": ($event) => unref(form).school_course_id = $event,
                        label: `${unref(t)("course")} (${unref(t)("autoCorrect")})`,
                        required: true,
                        options: courseOptions.value,
                        "error-message": unref(form).errors.school_course_id,
                        placeholder: unref(t)("select")
                      }, null, 8, ["modelValue", "onUpdate:modelValue", "label", "options", "error-message", "placeholder"])
                    ]),
                    createVNode("div", { class: "flex flex-col items-start" }, [
                      createVNode("div", { class: "flex justify-between w-full" }, [
                        createVNode(_sfc_main$2, { for: "notes" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("notes")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode("div", { class: "text-md text-gray-900 dark:text-gray-400 mt-1" }, toDisplayString(unref(form).notes.length) + " / 255 " + toDisplayString(unref(t)("characters")), 1)
                      ]),
                      createVNode(_sfc_main$7, {
                        id: "notes",
                        modelValue: unref(form).notes,
                        "onUpdate:modelValue": ($event) => unref(form).notes = $event,
                        class: "w-full"
                      }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                      createVNode(_sfc_main$3, {
                        class: "mt-2",
                        message: unref(form).errors.notes
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center gap-3" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.schoolEnrollments.index")
                      }, {
                        icon: withCtx(() => [
                          (openBlock(), createBlock("svg", {
                            class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                            viewBox: "0 0 16 16"
                          }, [
                            createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7\n                                       1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4\n                                       5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6\n                                       8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2\n                                       .3c.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0\n                                       3.6-.7 4.9-2l2.2 2.2.8-6.4z" })
                          ]))
                        ]),
                        default: withCtx(() => [
                          createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                        ]),
                        _: 1
                      }, 8, ["href"]),
                      createVNode(_sfc_main$8, {
                        class: ["mb-0", {
                          "opacity-25": unref(form).processing
                        }],
                        disabled: unref(form).processing
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(unref(t)("save")), 1)
                        ]),
                        _: 1
                      }, 8, ["class", "disabled"])
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolEnrollments/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
