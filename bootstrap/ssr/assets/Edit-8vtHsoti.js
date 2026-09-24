import { computed, mergeProps, unref, withCtx, createTextVNode, toDisplayString, createBlock, openBlock, createVNode, withModifiers, withDirectives, Fragment, renderList, vModelSelect, useSSRContext } from "vue";
import { ssrRenderComponent, ssrInterpolate, ssrRenderList, ssrRenderAttr, ssrIncludeBooleanAttr, ssrLooseContain, ssrLooseEqual } from "vue/server-renderer";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import VueMultiselect from "vue-multiselect";
import { A as AdminLayout } from "./AdminLayout-03yd3nxC.js";
import { T as TitlePage } from "./TitlePage-CBZZ8YZB.js";
import { _ as _sfc_main$1 } from "./DefaultButton-DMDyGacf.js";
import { _ as _sfc_main$7 } from "./PrimaryButton-B3InEAXg.js";
import { _ as _sfc_main$4 } from "./LabelInput-C61CQHdu.js";
import { _ as _sfc_main$5 } from "./InputText-BA43M9f1.js";
import { _ as _sfc_main$3 } from "./InputError-CLVdJ1nk.js";
import { _ as _sfc_main$2 } from "./ActivityCheckbox-BiU-rq6S.js";
import { _ as _sfc_main$6 } from "./MetaDescTextarea-D59BR8J3.js";
/* empty css                                                                      */
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
    currentLocale: {
      type: String,
      default: ""
    },
    availableLocales: {
      type: Array,
      default: () => []
    },
    order: {
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
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const toast = useToast();
    const props = __props;
    const form = useForm({
      _method: "PUT",
      user_id: props.order.user_id ?? null,
      school_course_id: props.order.school_course_id ?? null,
      school_course_schedule_id: props.order.school_course_schedule_id ?? null,
      number: props.order.number ?? "",
      buyer_name: props.order.buyer_name ?? "",
      buyer_email: props.order.buyer_email ?? "",
      buyer_phone: props.order.buyer_phone ?? "",
      billing_company: props.order.billing_company ?? "",
      billing_tax_id: props.order.billing_tax_id ?? "",
      billing_address: props.order.billing_address ?? "",
      is_paid: Boolean(props.order.is_paid),
      paid_at: props.order.paid_at ?? "",
      payment_method_id: props.order.payment_method_id ?? null,
      payment_method: props.order.payment_method ?? "",
      payment_provider: props.order.payment_provider ?? "",
      payment_reference: props.order.payment_reference ?? "",
      confirmation_code: props.order.confirmation_code ?? "",
      confirmation_status: props.order.confirmation_status ?? "",
      failure_reason: props.order.failure_reason ?? "",
      currency: props.order.currency ?? "USD",
      subtotal: props.order.subtotal ?? "0.00",
      discount_total: props.order.discount_total ?? "0.00",
      tax_total: props.order.tax_total ?? "0.00",
      total: props.order.total ?? "0.00",
      status: props.order.status ?? "new",
      payment_status: props.order.payment_status ?? "pending",
      items: props.order.items_snapshot ?? null,
      meta: props.order.meta ?? null,
      user_comment: props.order.user_comment ?? "",
      manager_comment: props.order.manager_comment ?? "",
      external_id: props.order.external_id ?? "",
      exported_at: props.order.exported_at ?? "",
      client_ip: props.order.client_ip ?? "",
      user_agent: props.order.user_agent ?? "",
      public_hash: props.order.public_hash ?? ""
    });
    const pageTitle = computed(
      () => props.order.number || `ID: ${props.order.id}`
    );
    const dynamicOptionsLimit = (items) => {
      return ((items == null ? void 0 : items.length) || 0) + 10;
    };
    const userOptions = computed(
      () => props.users.map((user) => ({
        ...user,
        label: `[ID: ${user.id}] ${user.name || user.email || `#${user.id}`}`
      }))
    );
    const courseOptions = computed(
      () => props.courses.map((course) => {
        var _a;
        return {
          ...course,
          label: `[ID: ${course.id}] ${((_a = course == null ? void 0 : course.translation) == null ? void 0 : _a.title) || (course == null ? void 0 : course.slug) || `#${course.id}`}`
        };
      })
    );
    const scheduleOptions = computed(
      () => props.schedules.map((schedule) => {
        var _a, _b, _c, _d;
        const scheduleTitle = ((_a = schedule == null ? void 0 : schedule.translation) == null ? void 0 : _a.title) || `#${schedule.id}`;
        const courseTitle = ((_c = (_b = schedule == null ? void 0 : schedule.course) == null ? void 0 : _b.translation) == null ? void 0 : _c.title) || ((_d = schedule == null ? void 0 : schedule.course) == null ? void 0 : _d.slug) || "";
        return {
          ...schedule,
          label: courseTitle ? `[ID: ${schedule.id}] [${courseTitle}] ${scheduleTitle}` : `[ID: ${schedule.id}] ${scheduleTitle}`
        };
      })
    );
    const selectedUser = computed({
      get: () => userOptions.value.find(
        (user) => Number(user.id) === Number(form.user_id)
      ) || null,
      set: (user) => {
        form.user_id = (user == null ? void 0 : user.id) ?? null;
      }
    });
    const selectedCourse = computed({
      get: () => courseOptions.value.find(
        (course) => Number(course.id) === Number(form.school_course_id)
      ) || null,
      set: (course) => {
        form.school_course_id = (course == null ? void 0 : course.id) ?? null;
      }
    });
    const selectedSchedule = computed({
      get: () => scheduleOptions.value.find(
        (schedule) => Number(schedule.id) === Number(form.school_course_schedule_id)
      ) || null,
      set: (schedule) => {
        form.school_course_schedule_id = (schedule == null ? void 0 : schedule.id) ?? null;
      }
    });
    const orderStatusLabelKeyMap = {
      new: "statusOrderNew",
      processing: "statusOrderProcessing",
      completed: "statusOrderCompleted",
      cancelled: "statusOrderCancelled",
      refunded: "statusPaidRefunded",
      archived: "statusArchived"
    };
    const paymentStatusLabelKeyMap = {
      pending: "statusPaidPending",
      paid: "statusPaid",
      succeeded: "statusPaid",
      failed: "statusPaidError",
      partial: "statusPaidPartial",
      partially_refunded: "statusPaidPartial",
      refunded: "statusPaidRefunded"
    };
    const orderStatusOptions = [
      "new",
      "processing",
      "completed",
      "cancelled",
      "refunded",
      "archived"
    ];
    const paymentStatusOptions = [
      "pending",
      "paid",
      "succeeded",
      "failed",
      "partial",
      "partially_refunded",
      "refunded"
    ];
    const getOrderStatusLabel = (status) => {
      if (!status) return "—";
      const key = orderStatusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const getPaymentStatusLabel = (status) => {
      if (!status) return "—";
      const key = paymentStatusLabelKeyMap[status];
      return key ? t(key) : status;
    };
    const formatDateTimeHuman = (value) => {
      if (!value) return "—";
      const date = new Date(value);
      if (Number.isNaN(date.getTime())) {
        return String(value);
      }
      return date.toLocaleString("ru-RU");
    };
    const formatMoney = (value, currency = "USD") => {
      if (value === null || value === void 0) return "—";
      const number = Number(value);
      if (Number.isNaN(number)) {
        return String(value);
      }
      return `${number.toFixed(2)} ${currency}`;
    };
    const formattedPaidAt = computed(
      () => formatDateTimeHuman(
        props.order.paid_at
      )
    );
    const formattedExportedAt = computed(
      () => formatDateTimeHuman(
        props.order.exported_at
      )
    );
    const submitForm = () => {
      form.transform((data) => ({
        ...data,
        user_id: data.user_id || null,
        school_course_id: data.school_course_id || null,
        school_course_schedule_id: data.school_course_schedule_id || null,
        payment_method_id: data.payment_method_id || null,
        is_paid: data.is_paid ? 1 : 0,
        subtotal: Number(data.subtotal || 0),
        discount_total: Number(data.discount_total || 0),
        tax_total: Number(data.tax_total || 0),
        total: Number(data.total || 0)
      }));
      form.post(
        route("admin.schoolOrders.update", {
          schoolOrder: props.order.id
        }),
        {
          preserveScroll: true,
          onSuccess: () => {
            toast.success(
              "Заказ успешно обновлён."
            );
          },
          onError: (errors) => {
            const firstKey = Object.keys(errors || {})[0];
            toast.error(
              (errors == null ? void 0 : errors[firstKey]) || "Проверьте правильность заполнения полей заказа."
            );
          }
        }
      );
    };
    return (_ctx, _push, _parent, _attrs) => {
      _push(ssrRenderComponent(AdminLayout, mergeProps({
        title: unref(t)("editOrder")
      }, _attrs), {
        header: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(ssrRenderComponent(TitlePage, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("editOrder"))} - ${ssrInterpolate(pageTitle.value)} [ID: ${ssrInterpolate(__props.order.id)}] `);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("editOrder")) + " - " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(__props.order.id) + "] ", 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
          } else {
            return [
              createVNode(TitlePage, null, {
                default: withCtx(() => [
                  createTextVNode(toDisplayString(unref(t)("editOrder")) + " - " + toDisplayString(pageTitle.value) + " [ID: " + toDisplayString(__props.order.id) + "] ", 1)
                ]),
                _: 1
              })
            ];
          }
        }),
        default: withCtx((_, _push2, _parent2, _scopeId) => {
          if (_push2) {
            _push2(`<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto"${_scopeId}><div class="p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400"${_scopeId}><div class="sm:flex sm:justify-between sm:items-center mb-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolOrders.index")
            }, {
              icon: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`<svg class="w-4 h-4 fill-current text-slate-100 shrink-0 mr-2" viewBox="0 0 16 16"${_scopeId2}><path d="M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z"${_scopeId2}></path></svg>`);
                } else {
                  return [
                    (openBlock(), createBlock("svg", {
                      class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                      viewBox: "0 0 16 16"
                    }, [
                      createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z" })
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
            _push2(`</div><form class="w-full"${_scopeId}><div class="mb-4 flex items-center gap-2"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$2, {
              modelValue: unref(form).is_paid,
              "onUpdate:modelValue": ($event) => unref(form).is_paid = $event
            }, null, _parent2, _scopeId));
            _push2(`<span class="text-sm text-slate-800 dark:text-slate-100"${_scopeId}>${ssrInterpolate(unref(t)("isPaid"))}</span>`);
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.is_paid
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="mb-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3"${_scopeId}><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "number" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("orderNumber"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("orderNumber")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "number",
              modelValue: unref(form).number,
              "onUpdate:modelValue": ($event) => unref(form).number = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.number
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "status" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("statusOrder"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("statusOrder")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<select id="status" class="py-0.5 rounded-sm dark:bg-slate-800"${_scopeId}><!--[-->`);
            ssrRenderList(orderStatusOptions, (status) => {
              _push2(`<option${ssrRenderAttr("value", status)}${ssrIncludeBooleanAttr(Array.isArray(unref(form).status) ? ssrLooseContain(unref(form).status, status) : ssrLooseEqual(unref(form).status, status)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(getOrderStatusLabel(status))}</option>`);
            });
            _push2(`<!--]--></select>`);
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.status
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "payment_status" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("paymentStatus"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("paymentStatus")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<select id="payment_status" class="py-0.5 rounded-sm dark:bg-slate-800"${_scopeId}><!--[-->`);
            ssrRenderList(paymentStatusOptions, (status) => {
              _push2(`<option${ssrRenderAttr("value", status)}${ssrIncludeBooleanAttr(Array.isArray(unref(form).payment_status) ? ssrLooseContain(unref(form).payment_status, status) : ssrLooseEqual(unref(form).payment_status, status)) ? " selected" : ""}${_scopeId}>${ssrInterpolate(getPaymentStatusLabel(status))}</option>`);
            });
            _push2(`<!--]--></select>`);
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.payment_status
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mb-4 border-t border-dashed border-slate-500 pt-4"${_scopeId}><h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span class="w-fit px-2 border border-gray-400 rounded-sm"${_scopeId}>${ssrInterpolate(unref(t)("relations"))}</span></h3><div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3"${_scopeId}><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("user"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("user")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              modelValue: selectedUser.value,
              "onUpdate:modelValue": ($event) => selectedUser.value = $event,
              options: userOptions.value,
              "options-limit": dynamicOptionsLimit(userOptions.value),
              "track-by": "id",
              label: "label",
              searchable: true,
              "allow-empty": true,
              "show-labels": false,
              placeholder: unref(t)("select")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.user_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("course"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("course")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              modelValue: selectedCourse.value,
              "onUpdate:modelValue": ($event) => selectedCourse.value = $event,
              options: courseOptions.value,
              "options-limit": dynamicOptionsLimit(courseOptions.value),
              "track-by": "id",
              label: "label",
              searchable: true,
              "allow-empty": true,
              "show-labels": false,
              placeholder: unref(t)("select")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.school_course_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("schedule"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("schedule")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(unref(VueMultiselect), {
              modelValue: selectedSchedule.value,
              "onUpdate:modelValue": ($event) => selectedSchedule.value = $event,
              options: scheduleOptions.value,
              "options-limit": dynamicOptionsLimit(scheduleOptions.value),
              "track-by": "id",
              label: "label",
              searchable: true,
              "allow-empty": true,
              "show-labels": false,
              placeholder: unref(t)("select")
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.school_course_schedule_id
            }, null, _parent2, _scopeId));
            _push2(`</div></div></div><div class="mb-4 border-t border-dashed border-slate-500 pt-4"${_scopeId}><h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span class="w-fit px-2 border border-gray-400 rounded-sm"${_scopeId}>${ssrInterpolate(unref(t)("buyerData"))}</span></h3><div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3"${_scopeId}><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "buyer_name" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("buyerName"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("buyerName")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "buyer_name",
              modelValue: unref(form).buyer_name,
              "onUpdate:modelValue": ($event) => unref(form).buyer_name = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.buyer_name
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "buyer_email" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("buyerEmail"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("buyerEmail")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "buyer_email",
              modelValue: unref(form).buyer_email,
              "onUpdate:modelValue": ($event) => unref(form).buyer_email = $event,
              type: "email"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.buyer_email
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "buyer_phone" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("buyerPhone"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("buyerPhone")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "buyer_phone",
              modelValue: unref(form).buyer_phone,
              "onUpdate:modelValue": ($event) => unref(form).buyer_phone = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.buyer_phone
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "billing_company" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("billingCompany"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("billingCompany")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "billing_company",
              modelValue: unref(form).billing_company,
              "onUpdate:modelValue": ($event) => unref(form).billing_company = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.billing_company
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "billing_tax_id" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("billingTaxId"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("billingTaxId")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "billing_tax_id",
              modelValue: unref(form).billing_tax_id,
              "onUpdate:modelValue": ($event) => unref(form).billing_tax_id = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.billing_tax_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col md:col-span-2 lg:col-span-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "billing_address" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("billingAddress"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("billingAddress")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "billing_address",
              modelValue: unref(form).billing_address,
              "onUpdate:modelValue": ($event) => unref(form).billing_address = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.billing_address
            }, null, _parent2, _scopeId));
            _push2(`</div></div></div><div class="mb-4 border-t border-dashed border-slate-500 pt-4"${_scopeId}><h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span class="w-fit px-2 border border-gray-400 rounded-sm"${_scopeId}>${ssrInterpolate(unref(t)("payment"))}</span></h3><div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3"${_scopeId}><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "payment_provider" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("paymentProvider"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("paymentProvider")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "payment_provider",
              modelValue: unref(form).payment_provider,
              "onUpdate:modelValue": ($event) => unref(form).payment_provider = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.payment_provider
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "payment_method" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("paymentMethod"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("paymentMethod")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "payment_method",
              modelValue: unref(form).payment_method,
              "onUpdate:modelValue": ($event) => unref(form).payment_method = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.payment_method
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "payment_reference" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("paymentReference"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("paymentReference")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "payment_reference",
              modelValue: unref(form).payment_reference,
              "onUpdate:modelValue": ($event) => unref(form).payment_reference = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.payment_reference
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("paidAt"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("paidAt")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<div class="px-2 py-0.5 rounded-sm border border-gray-400 text-sm bg-slate-200 dark:bg-slate-600"${_scopeId}>${ssrInterpolate(formattedPaidAt.value)}</div></div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "confirmation_code" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("confirmationCode"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("confirmationCode")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "confirmation_code",
              modelValue: unref(form).confirmation_code,
              "onUpdate:modelValue": ($event) => unref(form).confirmation_code = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.confirmation_code
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "confirmation_status" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("confirmationStatus"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("confirmationStatus")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "confirmation_status",
              modelValue: unref(form).confirmation_status,
              "onUpdate:modelValue": ($event) => unref(form).confirmation_status = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.confirmation_status
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col md:col-span-2 lg:col-span-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "failure_reason" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("failureReason"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("failureReason")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "failure_reason",
              modelValue: unref(form).failure_reason,
              "onUpdate:modelValue": ($event) => unref(form).failure_reason = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.failure_reason
            }, null, _parent2, _scopeId));
            _push2(`</div></div></div><div class="mb-4 border-t border-dashed border-slate-500 pt-4"${_scopeId}><h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span class="w-fit px-2 border border-gray-400 rounded-sm"${_scopeId}>${ssrInterpolate(unref(t)("totals"))}</span></h3><div class="grid gap-4 md:grid-cols-2 lg:grid-cols-5"${_scopeId}><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "currency" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("currency"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("currency")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "currency",
              modelValue: unref(form).currency,
              "onUpdate:modelValue": ($event) => unref(form).currency = $event,
              type: "text",
              maxlength: "3"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.currency
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "subtotal" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("subtotal"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("subtotal")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "subtotal",
              modelValue: unref(form).subtotal,
              "onUpdate:modelValue": ($event) => unref(form).subtotal = $event,
              type: "number",
              step: "0.01"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.subtotal
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "discount_total" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("discountTotal"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("discountTotal")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "discount_total",
              modelValue: unref(form).discount_total,
              "onUpdate:modelValue": ($event) => unref(form).discount_total = $event,
              type: "number",
              step: "0.01"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.discount_total
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "tax_total" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("taxTotal"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("taxTotal")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "tax_total",
              modelValue: unref(form).tax_total,
              "onUpdate:modelValue": ($event) => unref(form).tax_total = $event,
              type: "number",
              step: "0.01"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.tax_total
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "total" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("total"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("total")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "total",
              modelValue: unref(form).total,
              "onUpdate:modelValue": ($event) => unref(form).total = $event,
              type: "number",
              step: "0.01"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.total
            }, null, _parent2, _scopeId));
            _push2(`</div></div><div class="mt-2 text-center font-semibold text-sm text-rose-600 dark:text-rose-200"${_scopeId}>${ssrInterpolate(unref(t)("total"))}: ${ssrInterpolate(formatMoney(unref(form).total, unref(form).currency))}</div></div><div class="mb-4 border-t border-dashed border-slate-500 pt-4"${_scopeId}><h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span class="w-fit px-2 border border-gray-400 rounded-sm"${_scopeId}>${ssrInterpolate(unref(t)("comments"))}</span></h3><div class="grid gap-4 md:grid-cols-2"${_scopeId}><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "user_comment" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("userComment"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("userComment")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "user_comment",
              modelValue: unref(form).user_comment,
              "onUpdate:modelValue": ($event) => unref(form).user_comment = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.user_comment
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "manager_comment" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("managerComment"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("managerComment")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "manager_comment",
              modelValue: unref(form).manager_comment,
              "onUpdate:modelValue": ($event) => unref(form).manager_comment = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.manager_comment
            }, null, _parent2, _scopeId));
            _push2(`</div></div></div><div class="mb-4 border-t border-dashed border-slate-500 pt-4"${_scopeId}><h3 class="flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300"${_scopeId}><span class="w-fit px-2 border border-gray-400 rounded-sm"${_scopeId}>${ssrInterpolate(unref(t)("technicalData"))}</span></h3><div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3"${_scopeId}><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "external_id" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("externalId"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("externalId")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "external_id",
              modelValue: unref(form).external_id,
              "onUpdate:modelValue": ($event) => unref(form).external_id = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.external_id
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, null, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("exportedAt"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("exportedAt")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(`<div class="px-2 py-0.5 rounded-sm border border-gray-400 text-sm bg-slate-200 dark:bg-slate-600"${_scopeId}>${ssrInterpolate(formattedExportedAt.value)}</div></div><div class="flex flex-col"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "client_ip" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("clientIp"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("clientIp")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "client_ip",
              modelValue: unref(form).client_ip,
              "onUpdate:modelValue": ($event) => unref(form).client_ip = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.client_ip
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col md:col-span-2 lg:col-span-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "user_agent" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("userAgent"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("userAgent")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$6, {
              id: "user_agent",
              modelValue: unref(form).user_agent,
              "onUpdate:modelValue": ($event) => unref(form).user_agent = $event
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.user_agent
            }, null, _parent2, _scopeId));
            _push2(`</div><div class="flex flex-col md:col-span-2 lg:col-span-3"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$4, { for: "public_hash" }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("publicHash"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("publicHash")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$5, {
              id: "public_hash",
              modelValue: unref(form).public_hash,
              "onUpdate:modelValue": ($event) => unref(form).public_hash = $event,
              type: "text"
            }, null, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$3, {
              message: unref(form).errors.public_hash
            }, null, _parent2, _scopeId));
            _push2(`</div></div></div><div class="flex items-center justify-center mt-4"${_scopeId}>`);
            _push2(ssrRenderComponent(_sfc_main$1, {
              href: _ctx.route("admin.schoolOrders.index")
            }, {
              default: withCtx((_2, _push3, _parent3, _scopeId2) => {
                if (_push3) {
                  _push3(`${ssrInterpolate(unref(t)("back"))}`);
                } else {
                  return [
                    createTextVNode(toDisplayString(unref(t)("back")), 1)
                  ];
                }
              }),
              _: 1
            }, _parent2, _scopeId));
            _push2(ssrRenderComponent(_sfc_main$7, {
              class: ["ms-4 mb-0", { "opacity-25": unref(form).processing }],
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
              createVNode("div", { class: "px-4 sm:px-6 lg:px-8 py-8 w-full max-w-12xl mx-auto" }, [
                createVNode("div", { class: "p-4 bg-slate-50 dark:bg-slate-700 border border-blue-400 dark:border-blue-200 shadow-lg shadow-gray-500 dark:shadow-slate-400" }, [
                  createVNode("div", { class: "sm:flex sm:justify-between sm:items-center mb-4" }, [
                    createVNode(_sfc_main$1, {
                      href: _ctx.route("admin.schoolOrders.index")
                    }, {
                      icon: withCtx(() => [
                        (openBlock(), createBlock("svg", {
                          class: "w-4 h-4 fill-current text-slate-100 shrink-0 mr-2",
                          viewBox: "0 0 16 16"
                        }, [
                          createVNode("path", { d: "M4.3 4.5c1.9-1.9 5.1-1.9 7 0 .7.7 1.2 1.7 1.4 2.7l2-.3c-.2-1.5-.9-2.8-1.9-3.8C10.1.4 5.7.4 2.9 3.1L.7.9 0 7.3l6.4-.7-2.1-2.1zM15.6 8.7l-6.4.7 2.1 2.1c-1.9 1.9-5.1 1.9-7 0-.7-.7-1.2-1.7-1.4-2.7l-2 .3c-.2 1.5.9 2.8 1.9 3.8 1.4 1.4 3.1 2 4.9 2 1.8 0 3.6-.7 4.9-2l2.2 2.2 .8-6.4z" })
                        ]))
                      ]),
                      default: withCtx(() => [
                        createTextVNode(" " + toDisplayString(unref(t)("back")), 1)
                      ]),
                      _: 1
                    }, 8, ["href"])
                  ]),
                  createVNode("form", {
                    class: "w-full",
                    onSubmit: withModifiers(submitForm, ["prevent"])
                  }, [
                    createVNode("div", { class: "mb-4 flex items-center gap-2" }, [
                      createVNode(_sfc_main$2, {
                        modelValue: unref(form).is_paid,
                        "onUpdate:modelValue": ($event) => unref(form).is_paid = $event
                      }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                      createVNode("span", { class: "text-sm text-slate-800 dark:text-slate-100" }, toDisplayString(unref(t)("isPaid")), 1),
                      createVNode(_sfc_main$3, {
                        message: unref(form).errors.is_paid
                      }, null, 8, ["message"])
                    ]),
                    createVNode("div", { class: "mb-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3" }, [
                      createVNode("div", { class: "flex flex-col" }, [
                        createVNode(_sfc_main$4, { for: "number" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("orderNumber")), 1)
                          ]),
                          _: 1
                        }),
                        createVNode(_sfc_main$5, {
                          id: "number",
                          modelValue: unref(form).number,
                          "onUpdate:modelValue": ($event) => unref(form).number = $event,
                          type: "text"
                        }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                        createVNode(_sfc_main$3, {
                          message: unref(form).errors.number
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col" }, [
                        createVNode(_sfc_main$4, { for: "status" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("statusOrder")), 1)
                          ]),
                          _: 1
                        }),
                        withDirectives(createVNode("select", {
                          id: "status",
                          "onUpdate:modelValue": ($event) => unref(form).status = $event,
                          class: "py-0.5 rounded-sm dark:bg-slate-800"
                        }, [
                          (openBlock(), createBlock(Fragment, null, renderList(orderStatusOptions, (status) => {
                            return createVNode("option", {
                              key: status,
                              value: status
                            }, toDisplayString(getOrderStatusLabel(status)), 9, ["value"]);
                          }), 64))
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).status]
                        ]),
                        createVNode(_sfc_main$3, {
                          message: unref(form).errors.status
                        }, null, 8, ["message"])
                      ]),
                      createVNode("div", { class: "flex flex-col" }, [
                        createVNode(_sfc_main$4, { for: "payment_status" }, {
                          default: withCtx(() => [
                            createTextVNode(toDisplayString(unref(t)("paymentStatus")), 1)
                          ]),
                          _: 1
                        }),
                        withDirectives(createVNode("select", {
                          id: "payment_status",
                          "onUpdate:modelValue": ($event) => unref(form).payment_status = $event,
                          class: "py-0.5 rounded-sm dark:bg-slate-800"
                        }, [
                          (openBlock(), createBlock(Fragment, null, renderList(paymentStatusOptions, (status) => {
                            return createVNode("option", {
                              key: status,
                              value: status
                            }, toDisplayString(getPaymentStatusLabel(status)), 9, ["value"]);
                          }), 64))
                        ], 8, ["onUpdate:modelValue"]), [
                          [vModelSelect, unref(form).payment_status]
                        ]),
                        createVNode(_sfc_main$3, {
                          message: unref(form).errors.payment_status
                        }, null, 8, ["message"])
                      ])
                    ]),
                    createVNode("div", { class: "mb-4 border-t border-dashed border-slate-500 pt-4" }, [
                      createVNode("h3", { class: "flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300" }, [
                        createVNode("span", { class: "w-fit px-2 border border-gray-400 rounded-sm" }, toDisplayString(unref(t)("relations")), 1)
                      ]),
                      createVNode("div", { class: "grid gap-4 md:grid-cols-2 lg:grid-cols-3" }, [
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, null, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("user")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(unref(VueMultiselect), {
                            modelValue: selectedUser.value,
                            "onUpdate:modelValue": ($event) => selectedUser.value = $event,
                            options: userOptions.value,
                            "options-limit": dynamicOptionsLimit(userOptions.value),
                            "track-by": "id",
                            label: "label",
                            searchable: true,
                            "allow-empty": true,
                            "show-labels": false,
                            placeholder: unref(t)("select")
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.user_id
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, null, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("course")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(unref(VueMultiselect), {
                            modelValue: selectedCourse.value,
                            "onUpdate:modelValue": ($event) => selectedCourse.value = $event,
                            options: courseOptions.value,
                            "options-limit": dynamicOptionsLimit(courseOptions.value),
                            "track-by": "id",
                            label: "label",
                            searchable: true,
                            "allow-empty": true,
                            "show-labels": false,
                            placeholder: unref(t)("select")
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.school_course_id
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, null, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("schedule")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(unref(VueMultiselect), {
                            modelValue: selectedSchedule.value,
                            "onUpdate:modelValue": ($event) => selectedSchedule.value = $event,
                            options: scheduleOptions.value,
                            "options-limit": dynamicOptionsLimit(scheduleOptions.value),
                            "track-by": "id",
                            label: "label",
                            searchable: true,
                            "allow-empty": true,
                            "show-labels": false,
                            placeholder: unref(t)("select")
                          }, null, 8, ["modelValue", "onUpdate:modelValue", "options", "options-limit", "placeholder"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.school_course_schedule_id
                          }, null, 8, ["message"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "mb-4 border-t border-dashed border-slate-500 pt-4" }, [
                      createVNode("h3", { class: "flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300" }, [
                        createVNode("span", { class: "w-fit px-2 border border-gray-400 rounded-sm" }, toDisplayString(unref(t)("buyerData")), 1)
                      ]),
                      createVNode("div", { class: "grid gap-4 md:grid-cols-2 lg:grid-cols-3" }, [
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "buyer_name" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("buyerName")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "buyer_name",
                            modelValue: unref(form).buyer_name,
                            "onUpdate:modelValue": ($event) => unref(form).buyer_name = $event,
                            type: "text"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.buyer_name
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "buyer_email" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("buyerEmail")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "buyer_email",
                            modelValue: unref(form).buyer_email,
                            "onUpdate:modelValue": ($event) => unref(form).buyer_email = $event,
                            type: "email"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.buyer_email
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "buyer_phone" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("buyerPhone")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "buyer_phone",
                            modelValue: unref(form).buyer_phone,
                            "onUpdate:modelValue": ($event) => unref(form).buyer_phone = $event,
                            type: "text"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.buyer_phone
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "billing_company" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("billingCompany")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "billing_company",
                            modelValue: unref(form).billing_company,
                            "onUpdate:modelValue": ($event) => unref(form).billing_company = $event,
                            type: "text"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.billing_company
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "billing_tax_id" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("billingTaxId")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "billing_tax_id",
                            modelValue: unref(form).billing_tax_id,
                            "onUpdate:modelValue": ($event) => unref(form).billing_tax_id = $event,
                            type: "text"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.billing_tax_id
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col md:col-span-2 lg:col-span-3" }, [
                          createVNode(_sfc_main$4, { for: "billing_address" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("billingAddress")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$6, {
                            id: "billing_address",
                            modelValue: unref(form).billing_address,
                            "onUpdate:modelValue": ($event) => unref(form).billing_address = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.billing_address
                          }, null, 8, ["message"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "mb-4 border-t border-dashed border-slate-500 pt-4" }, [
                      createVNode("h3", { class: "flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300" }, [
                        createVNode("span", { class: "w-fit px-2 border border-gray-400 rounded-sm" }, toDisplayString(unref(t)("payment")), 1)
                      ]),
                      createVNode("div", { class: "grid gap-4 md:grid-cols-2 lg:grid-cols-3" }, [
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "payment_provider" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("paymentProvider")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "payment_provider",
                            modelValue: unref(form).payment_provider,
                            "onUpdate:modelValue": ($event) => unref(form).payment_provider = $event,
                            type: "text"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.payment_provider
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "payment_method" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("paymentMethod")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "payment_method",
                            modelValue: unref(form).payment_method,
                            "onUpdate:modelValue": ($event) => unref(form).payment_method = $event,
                            type: "text"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.payment_method
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "payment_reference" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("paymentReference")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "payment_reference",
                            modelValue: unref(form).payment_reference,
                            "onUpdate:modelValue": ($event) => unref(form).payment_reference = $event,
                            type: "text"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.payment_reference
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, null, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("paidAt")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode("div", { class: "px-2 py-0.5 rounded-sm border border-gray-400 text-sm bg-slate-200 dark:bg-slate-600" }, toDisplayString(formattedPaidAt.value), 1)
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "confirmation_code" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("confirmationCode")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "confirmation_code",
                            modelValue: unref(form).confirmation_code,
                            "onUpdate:modelValue": ($event) => unref(form).confirmation_code = $event,
                            type: "text"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.confirmation_code
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "confirmation_status" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("confirmationStatus")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "confirmation_status",
                            modelValue: unref(form).confirmation_status,
                            "onUpdate:modelValue": ($event) => unref(form).confirmation_status = $event,
                            type: "text"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.confirmation_status
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col md:col-span-2 lg:col-span-3" }, [
                          createVNode(_sfc_main$4, { for: "failure_reason" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("failureReason")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$6, {
                            id: "failure_reason",
                            modelValue: unref(form).failure_reason,
                            "onUpdate:modelValue": ($event) => unref(form).failure_reason = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.failure_reason
                          }, null, 8, ["message"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "mb-4 border-t border-dashed border-slate-500 pt-4" }, [
                      createVNode("h3", { class: "flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300" }, [
                        createVNode("span", { class: "w-fit px-2 border border-gray-400 rounded-sm" }, toDisplayString(unref(t)("totals")), 1)
                      ]),
                      createVNode("div", { class: "grid gap-4 md:grid-cols-2 lg:grid-cols-5" }, [
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "currency" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("currency")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "currency",
                            modelValue: unref(form).currency,
                            "onUpdate:modelValue": ($event) => unref(form).currency = $event,
                            type: "text",
                            maxlength: "3"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.currency
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "subtotal" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("subtotal")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "subtotal",
                            modelValue: unref(form).subtotal,
                            "onUpdate:modelValue": ($event) => unref(form).subtotal = $event,
                            type: "number",
                            step: "0.01"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.subtotal
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "discount_total" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("discountTotal")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "discount_total",
                            modelValue: unref(form).discount_total,
                            "onUpdate:modelValue": ($event) => unref(form).discount_total = $event,
                            type: "number",
                            step: "0.01"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.discount_total
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "tax_total" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("taxTotal")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "tax_total",
                            modelValue: unref(form).tax_total,
                            "onUpdate:modelValue": ($event) => unref(form).tax_total = $event,
                            type: "number",
                            step: "0.01"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.tax_total
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "total" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("total")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "total",
                            modelValue: unref(form).total,
                            "onUpdate:modelValue": ($event) => unref(form).total = $event,
                            type: "number",
                            step: "0.01"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.total
                          }, null, 8, ["message"])
                        ])
                      ]),
                      createVNode("div", { class: "mt-2 text-center font-semibold text-sm text-rose-600 dark:text-rose-200" }, toDisplayString(unref(t)("total")) + ": " + toDisplayString(formatMoney(unref(form).total, unref(form).currency)), 1)
                    ]),
                    createVNode("div", { class: "mb-4 border-t border-dashed border-slate-500 pt-4" }, [
                      createVNode("h3", { class: "flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300" }, [
                        createVNode("span", { class: "w-fit px-2 border border-gray-400 rounded-sm" }, toDisplayString(unref(t)("comments")), 1)
                      ]),
                      createVNode("div", { class: "grid gap-4 md:grid-cols-2" }, [
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "user_comment" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("userComment")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$6, {
                            id: "user_comment",
                            modelValue: unref(form).user_comment,
                            "onUpdate:modelValue": ($event) => unref(form).user_comment = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.user_comment
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "manager_comment" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("managerComment")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$6, {
                            id: "manager_comment",
                            modelValue: unref(form).manager_comment,
                            "onUpdate:modelValue": ($event) => unref(form).manager_comment = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.manager_comment
                          }, null, 8, ["message"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "mb-4 border-t border-dashed border-slate-500 pt-4" }, [
                      createVNode("h3", { class: "flex justify-center text-md mb-3 text-fuchsia-700 dark:text-fuchsia-300" }, [
                        createVNode("span", { class: "w-fit px-2 border border-gray-400 rounded-sm" }, toDisplayString(unref(t)("technicalData")), 1)
                      ]),
                      createVNode("div", { class: "grid gap-4 md:grid-cols-2 lg:grid-cols-3" }, [
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "external_id" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("externalId")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "external_id",
                            modelValue: unref(form).external_id,
                            "onUpdate:modelValue": ($event) => unref(form).external_id = $event,
                            type: "text"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.external_id
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, null, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("exportedAt")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode("div", { class: "px-2 py-0.5 rounded-sm border border-gray-400 text-sm bg-slate-200 dark:bg-slate-600" }, toDisplayString(formattedExportedAt.value), 1)
                        ]),
                        createVNode("div", { class: "flex flex-col" }, [
                          createVNode(_sfc_main$4, { for: "client_ip" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("clientIp")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "client_ip",
                            modelValue: unref(form).client_ip,
                            "onUpdate:modelValue": ($event) => unref(form).client_ip = $event,
                            type: "text"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.client_ip
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col md:col-span-2 lg:col-span-3" }, [
                          createVNode(_sfc_main$4, { for: "user_agent" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("userAgent")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$6, {
                            id: "user_agent",
                            modelValue: unref(form).user_agent,
                            "onUpdate:modelValue": ($event) => unref(form).user_agent = $event
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.user_agent
                          }, null, 8, ["message"])
                        ]),
                        createVNode("div", { class: "flex flex-col md:col-span-2 lg:col-span-3" }, [
                          createVNode(_sfc_main$4, { for: "public_hash" }, {
                            default: withCtx(() => [
                              createTextVNode(toDisplayString(unref(t)("publicHash")), 1)
                            ]),
                            _: 1
                          }),
                          createVNode(_sfc_main$5, {
                            id: "public_hash",
                            modelValue: unref(form).public_hash,
                            "onUpdate:modelValue": ($event) => unref(form).public_hash = $event,
                            type: "text"
                          }, null, 8, ["modelValue", "onUpdate:modelValue"]),
                          createVNode(_sfc_main$3, {
                            message: unref(form).errors.public_hash
                          }, null, 8, ["message"])
                        ])
                      ])
                    ]),
                    createVNode("div", { class: "flex items-center justify-center mt-4" }, [
                      createVNode(_sfc_main$1, {
                        href: _ctx.route("admin.schoolOrders.index")
                      }, {
                        default: withCtx(() => [
                          createTextVNode(toDisplayString(unref(t)("back")), 1)
                        ]),
                        _: 1
                      }, 8, ["href"]),
                      createVNode(_sfc_main$7, {
                        class: ["ms-4 mb-0", { "opacity-25": unref(form).processing }],
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
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Pages/Admin/School/SchoolOrders/Edit.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as default
};
