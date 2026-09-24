import { computed, ref, onMounted, resolveComponent, mergeProps, unref, withCtx, createVNode, toDisplayString, createTextVNode, useSSRContext, onBeforeUnmount, watch, createBlock, createCommentVNode, openBlock, Transition, Fragment, renderList, withModifiers } from "vue";
import { ssrRenderAttrs, ssrRenderStyle, ssrRenderClass, ssrInterpolate, ssrRenderComponent, ssrRenderList, ssrRenderAttr } from "vue/server-renderer";
import { Link, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { u as unwrapList, a as unwrap, B as BannersSidebar, b as _sfc_main$6 } from "./SectionBanners-Cdgmr0Bw.js";
import { _ as _export_sfc } from "./_plugin-vue_export-helper-1tPrXgE0.js";
const STORAGE_KEY = "trackAccordionOpen";
const MAX_DEPTH = 2;
const _sfc_main$5 = /* @__PURE__ */ Object.assign({
  name: "TrackTreeItem"
}, {
  __name: "TrackTreeItem",
  __ssrInlineRender: true,
  props: {
    item: {
      type: Object,
      required: true
    },
    /**
     * 0 = первый уровень
     * 1 = второй уровень
     * 2 = третий уровень
     */
    depth: {
      type: Number,
      default: 0
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const trackName = computed(() => {
      var _a, _b;
      return ((_b = (_a = props.item) == null ? void 0 : _a.translation) == null ? void 0 : _b.name) || "";
    });
    const trackSlug = computed(() => {
      var _a;
      return ((_a = props.item) == null ? void 0 : _a.slug) || "";
    });
    const trackUrl = computed(() => {
      return trackSlug.value ? route(
        "public.schoolTracks.show",
        {
          slug: trackSlug.value
        }
      ) : "#";
    });
    const children = computed(() => {
      var _a;
      if (props.depth >= MAX_DEPTH) {
        return [];
      }
      return Array.isArray(
        (_a = props.item) == null ? void 0 : _a.children
      ) ? props.item.children : [];
    });
    const hasChildren = computed(() => {
      return children.value.length > 0;
    });
    const isOpen = ref(false);
    const loadState = () => {
      try {
        const raw = localStorage.getItem(
          STORAGE_KEY
        );
        const openIds = raw ? JSON.parse(raw) : [];
        if (Array.isArray(openIds)) {
          isOpen.value = openIds.includes(
            props.item.id
          );
        }
      } catch {
        isOpen.value = false;
      }
    };
    onMounted(() => {
      if (hasChildren.value) {
        loadState();
      }
    });
    return (_ctx, _push, _parent, _attrs) => {
      const _component_TrackTreeItem = resolveComponent("TrackTreeItem", true);
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-full" }, _attrs))}><div class="my-0.5 flex items-center justify-between gap-0.5 rounded-sm transition hover:bg-slate-200 dark:hover:bg-slate-800" style="${ssrRenderStyle({
        paddingLeft: `${__props.depth * 16}px`
      })}"><div class="flex min-w-0 flex-1 items-center gap-2 py-1 pr-2">`);
      if (hasChildren.value) {
        _push(`<button type="button" class="flex min-w-0 flex-1 items-center justify-start gap-1 text-left"><svg class="${ssrRenderClass([{
          "rotate-90": isOpen.value
        }, "h-3.5 w-3.5 shrink-0 text-gray-500 transition-transform duration-200 dark:text-gray-400"])}" viewBox="0 0 320 512" fill="currentColor"><path d="M96 96l128 160L96 416z"></path></svg><span class="truncate text-xs font-semibold text-gray-700 dark:text-gray-300">${ssrInterpolate(trackName.value)}</span></button>`);
      } else {
        _push(ssrRenderComponent(unref(Link), {
          href: trackUrl.value,
          class: "min-w-0 flex-1"
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(`<span class="truncate text-xs font-semibold text-gray-700 dark:text-gray-300"${_scopeId}>${ssrInterpolate(trackName.value)}</span>`);
            } else {
              return [
                createVNode("span", { class: "truncate text-xs font-semibold text-gray-700 dark:text-gray-300" }, toDisplayString(trackName.value), 1)
              ];
            }
          }),
          _: 1
        }, _parent));
      }
      _push(`</div>`);
      if (hasChildren.value) {
        _push(ssrRenderComponent(unref(Link), {
          href: trackUrl.value,
          class: "mr-2 shrink-0 rounded-sm px-2 py-1 text-[10px] font-semibold text-indigo-700 hover:bg-indigo-500 hover:text-slate-100 dark:text-indigo-300 dark:hover:bg-indigo-500",
          title: unref(t)("openLink")
        }, {
          default: withCtx((_, _push2, _parent2, _scopeId) => {
            if (_push2) {
              _push2(` → `);
            } else {
              return [
                createTextVNode(" → ")
              ];
            }
          }),
          _: 1
        }, _parent));
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
      if (hasChildren.value && isOpen.value) {
        _push(`<div class="overflow-hidden"><!--[-->`);
        ssrRenderList(children.value, (child) => {
          _push(ssrRenderComponent(_component_TrackTreeItem, {
            key: child.id,
            item: child,
            depth: __props.depth + 1
          }, null, _parent));
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
      _push(`</div>`);
    };
  }
});
const _sfc_setup$5 = _sfc_main$5.setup;
_sfc_main$5.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolTrack/TrackTreeItem.vue");
  return _sfc_setup$5 ? _sfc_setup$5(props, ctx) : void 0;
};
const _sfc_main$4 = {
  __name: "TracksSidebar",
  __ssrInlineRender: true,
  props: {
    tracks: {
      type: Array,
      default: () => []
    }
  },
  setup(__props) {
    const props = __props;
    const trackList = computed(() => {
      return Array.isArray(props.tracks) ? props.tracks : [];
    });
    return (_ctx, _push, _parent, _attrs) => {
      if (trackList.value.length) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-full space-y-1" }, _attrs))}><!--[-->`);
        ssrRenderList(trackList.value, (track) => {
          _push(ssrRenderComponent(_sfc_main$5, {
            key: track.id,
            item: track,
            depth: 0
          }, null, _parent));
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$4 = _sfc_main$4.setup;
_sfc_main$4.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolTrack/TracksSidebar.vue");
  return _sfc_setup$4 ? _sfc_setup$4(props, ctx) : void 0;
};
const _sfc_main$3 = {
  __name: "CoursesSidebar",
  __ssrInlineRender: true,
  props: {
    titleKey: {
      type: String,
      default: "courses"
    },
    courses: {
      type: [Array, Object],
      default: () => []
    },
    intervalMs: {
      type: Number,
      default: 4200
    },
    pauseOnHover: {
      type: Boolean,
      default: true
    },
    pauseOnHidden: {
      type: Boolean,
      default: true
    }
  },
  setup(__props) {
    const props = __props;
    const list = computed(
      () => unwrapList(
        props.courses
      )
    );
    const getCourse = (course) => {
      return unwrap(course) ?? {};
    };
    const getCourseTitle = (course) => {
      var _a;
      const item = getCourse(course);
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.title) || (item == null ? void 0 : item.slug) || `ID: ${(item == null ? void 0 : item.id) ?? ""}`;
    };
    const getCourseLink = (course) => {
      const item = getCourse(course);
      if (!(item == null ? void 0 : item.slug)) {
        return "#";
      }
      return route(
        "public.schoolCourses.show",
        {
          slug: item.slug
        }
      );
    };
    const courseImages = (course) => {
      var _a;
      const item = getCourse(course);
      const images = Array.isArray(item == null ? void 0 : item.images) ? item.images : Array.isArray((_a = item == null ? void 0 : item.images) == null ? void 0 : _a.data) ? item.images.data : [];
      return images.slice().sort(
        (a, b) => Number(
          (a == null ? void 0 : a.order) ?? 0
        ) - Number(
          (b == null ? void 0 : b.order) ?? 0
        )
      ).map(
        (image, index) => {
          const src = (image == null ? void 0 : image.webp_url) || (image == null ? void 0 : image.image_url) || (image == null ? void 0 : image.url) || (image == null ? void 0 : image.thumb_url) || "";
          return {
            id: (image == null ? void 0 : image.id) ?? `course-image-${index}`,
            src,
            alt: (image == null ? void 0 : image.alt) || getCourseTitle(
              course
            ),
            title: (image == null ? void 0 : image.title) || (image == null ? void 0 : image.caption) || (image == null ? void 0 : image.alt) || getCourseTitle(
              course
            ),
            order: Number(
              (image == null ? void 0 : image.order) ?? 0
            )
          };
        }
      ).filter(
        (image) => Boolean(
          image.src
        )
      );
    };
    const currentByCourse = ref({});
    const hoveredByCourse = ref({});
    let timer = null;
    const getCurrent = (courseId) => {
      var _a;
      return Number(
        ((_a = currentByCourse.value) == null ? void 0 : _a[courseId]) ?? 0
      );
    };
    const setCurrent = (courseId, index, total) => {
      const count = Number(total) || 0;
      if (count <= 1) {
        currentByCourse.value = {
          ...currentByCourse.value,
          [courseId]: 0
        };
        return;
      }
      const value = Number(index);
      const safeIndex = Number.isFinite(value) ? Math.min(
        Math.max(
          0,
          value
        ),
        count - 1
      ) : 0;
      currentByCourse.value = {
        ...currentByCourse.value,
        [courseId]: safeIndex
      };
    };
    const canRun = () => {
      if (props.pauseOnHidden && typeof document !== "undefined" && document.hidden) {
        return false;
      }
      return true;
    };
    const tick = () => {
      var _a;
      if (!canRun()) {
        return;
      }
      const nextState = {
        ...currentByCourse.value
      };
      for (const course of list.value) {
        const item = getCourse(course);
        const id = item == null ? void 0 : item.id;
        if (!id) {
          continue;
        }
        const images = courseImages(
          course
        );
        if (images.length <= 1) {
          continue;
        }
        if (props.pauseOnHover && ((_a = hoveredByCourse.value) == null ? void 0 : _a[id])) {
          continue;
        }
        const current = Number(
          nextState[id] ?? 0
        );
        nextState[id] = (current + 1) % images.length;
      }
      currentByCourse.value = nextState;
    };
    const stop = () => {
      if (!timer) {
        return;
      }
      clearInterval(
        timer
      );
      timer = null;
    };
    const start = () => {
      stop();
      const hasAnySlider = list.value.some(
        (course) => courseImages(
          course
        ).length > 1
      );
      if (!hasAnySlider) {
        return;
      }
      timer = setInterval(
        tick,
        Math.max(
          1500,
          Number(
            props.intervalMs
          ) || 4200
        )
      );
    };
    const onVisibilityChange = () => {
      start();
    };
    const resetCurrentState = () => {
      var _a;
      const state = {};
      for (const course of list.value) {
        const id = (_a = getCourse(
          course
        )) == null ? void 0 : _a.id;
        if (id) {
          state[id] = 0;
        }
      }
      currentByCourse.value = state;
    };
    onMounted(() => {
      resetCurrentState();
      start();
      if (props.pauseOnHidden && typeof document !== "undefined") {
        document.addEventListener(
          "visibilitychange",
          onVisibilityChange
        );
      }
    });
    onBeforeUnmount(() => {
      stop();
      if (props.pauseOnHidden && typeof document !== "undefined") {
        document.removeEventListener(
          "visibilitychange",
          onVisibilityChange
        );
      }
    });
    watch(
      () => [
        list.value.length,
        props.intervalMs
      ],
      () => {
        resetCurrentState();
        start();
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      if (list.value.length) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "w-full" }, _attrs))} data-v-797d5b0c><!--[-->`);
        ssrRenderList(list.value, (course) => {
          _push(`<div data-v-797d5b0c><div class="mb-4" data-v-797d5b0c>`);
          _push(ssrRenderComponent(unref(Link), {
            href: getCourseLink(
              course
            ),
            class: "flex gap-2"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              var _a, _b, _c;
              if (_push2) {
                if (courseImages(
                  course
                ).length > 0) {
                  _push2(`<div class="post-image relative overflow-hidden rounded-md bg-slate-100 dark:bg-slate-900 w-auto h-[64px] shrink-0" data-v-797d5b0c${_scopeId}><img class="w-full h-full object-cover"${ssrRenderAttr(
                    "src",
                    (_a = courseImages(
                      course
                    )[getCurrent(
                      getCourse(
                        course
                      ).id
                    )]) == null ? void 0 : _a.src
                  )}${ssrRenderAttr(
                    "alt",
                    (_b = courseImages(
                      course
                    )[getCurrent(
                      getCourse(
                        course
                      ).id
                    )]) == null ? void 0 : _b.alt
                  )}${ssrRenderAttr(
                    "title",
                    (_c = courseImages(
                      course
                    )[getCurrent(
                      getCourse(
                        course
                      ).id
                    )]) == null ? void 0 : _c.title
                  )} loading="lazy" data-v-797d5b0c${_scopeId}>`);
                  if (courseImages(
                    course
                  ).length > 1) {
                    _push2(`<div class="absolute left-0 right-0 bottom-0 px-1 pb-1" data-v-797d5b0c${_scopeId}><div class="flex items-center justify-center gap-1" data-v-797d5b0c${_scopeId}><!--[-->`);
                    ssrRenderList(courseImages(
                      course
                    ), (image, index) => {
                      _push2(`<button type="button" class="${ssrRenderClass([
                        index === getCurrent(
                          getCourse(
                            course
                          ).id
                        ) ? "bg-orange-400 shadow ring-1 ring-black/40" : "bg-white/60 hover:bg-orange-400/80",
                        "h-1.5 w-1.5 rounded-full transition-all"
                      ])}"${ssrRenderAttr(
                        "aria-label",
                        `image ${index + 1}`
                      )}${ssrRenderAttr(
                        "title",
                        image.title
                      )} data-v-797d5b0c${_scopeId}></button>`);
                    });
                    _push2(`<!--]--></div></div>`);
                  } else {
                    _push2(`<!---->`);
                  }
                  _push2(`</div>`);
                } else {
                  _push2(`<!---->`);
                }
                _push2(`<h3 class="title" data-v-797d5b0c${_scopeId}><span class="font-semibold text-xs text-gray-700 dark:text-gray-300 hover:text-indigo-600" data-v-797d5b0c${_scopeId}>${ssrInterpolate(getCourseTitle(
                  course
                ))}</span></h3>`);
              } else {
                return [
                  courseImages(
                    course
                  ).length > 0 ? (openBlock(), createBlock("div", {
                    key: 0,
                    class: "post-image relative overflow-hidden rounded-md bg-slate-100 dark:bg-slate-900 w-auto h-[64px] shrink-0",
                    onMouseenter: ($event) => hoveredByCourse.value = {
                      ...hoveredByCourse.value,
                      [getCourse(course).id]: true
                    },
                    onMouseleave: ($event) => hoveredByCourse.value = {
                      ...hoveredByCourse.value,
                      [getCourse(course).id]: false
                    }
                  }, [
                    createVNode(Transition, {
                      name: "imgfx",
                      mode: "out-in"
                    }, {
                      default: withCtx(() => {
                        var _a2, _b2, _c2, _d;
                        return [
                          (openBlock(), createBlock("img", {
                            key: (_a2 = courseImages(
                              course
                            )[getCurrent(
                              getCourse(
                                course
                              ).id
                            )]) == null ? void 0 : _a2.id,
                            class: "w-full h-full object-cover",
                            src: (_b2 = courseImages(
                              course
                            )[getCurrent(
                              getCourse(
                                course
                              ).id
                            )]) == null ? void 0 : _b2.src,
                            alt: (_c2 = courseImages(
                              course
                            )[getCurrent(
                              getCourse(
                                course
                              ).id
                            )]) == null ? void 0 : _c2.alt,
                            title: (_d = courseImages(
                              course
                            )[getCurrent(
                              getCourse(
                                course
                              ).id
                            )]) == null ? void 0 : _d.title,
                            loading: "lazy"
                          }, null, 8, ["src", "alt", "title"]))
                        ];
                      }),
                      _: 2
                    }, 1024),
                    courseImages(
                      course
                    ).length > 1 ? (openBlock(), createBlock("div", {
                      key: 0,
                      class: "absolute left-0 right-0 bottom-0 px-1 pb-1"
                    }, [
                      createVNode("div", { class: "flex items-center justify-center gap-1" }, [
                        (openBlock(true), createBlock(Fragment, null, renderList(courseImages(
                          course
                        ), (image, index) => {
                          return openBlock(), createBlock("button", {
                            key: image.id,
                            type: "button",
                            class: [
                              "h-1.5 w-1.5 rounded-full transition-all",
                              index === getCurrent(
                                getCourse(
                                  course
                                ).id
                              ) ? "bg-orange-400 shadow ring-1 ring-black/40" : "bg-white/60 hover:bg-orange-400/80"
                            ],
                            onClick: withModifiers(($event) => setCurrent(
                              getCourse(
                                course
                              ).id,
                              index,
                              courseImages(
                                course
                              ).length
                            ), ["prevent", "stop"]),
                            "aria-label": `image ${index + 1}`,
                            title: image.title
                          }, null, 10, ["onClick", "aria-label", "title"]);
                        }), 128))
                      ])
                    ])) : createCommentVNode("", true)
                  ], 40, ["onMouseenter", "onMouseleave"])) : createCommentVNode("", true),
                  createVNode("h3", { class: "title" }, [
                    createVNode("span", { class: "font-semibold text-xs text-gray-700 dark:text-gray-300 hover:text-indigo-600" }, toDisplayString(getCourseTitle(
                      course
                    )), 1)
                  ])
                ];
              }
            }),
            _: 2
          }, _parent));
          _push(`</div></div>`);
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$3 = _sfc_main$3.setup;
_sfc_main$3.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolCourse/CoursesSidebar.vue");
  return _sfc_setup$3 ? _sfc_setup$3(props, ctx) : void 0;
};
const CoursesSidebar = /* @__PURE__ */ _export_sfc(_sfc_main$3, [["__scopeId", "data-v-797d5b0c"]]);
const _sfc_main$2 = {
  __name: "LeftSidebarSchool",
  __ssrInlineRender: true,
  props: {
    trackTree: { type: Array, default: () => [] },
    collapsed: { type: Boolean, default: false }
  },
  emits: ["collapsed"],
  setup(__props, { emit: __emit }) {
    const props = __props;
    const { t } = useI18n();
    const page = usePage();
    const leftCourses = computed(() => page.props.leftCourses ?? []);
    const leftBanners = computed(() => page.props.leftBanners ?? []);
    const leftVideos = computed(() => page.props.leftVideos ?? []);
    const isCollapsed = computed(() => props.collapsed);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative" }, _attrs))}><div class="flex items-center justify-center mb-2"><button type="button" class="focus:outline-none"${ssrRenderAttr("title", unref(t)("toggleSidebar"))}>`);
      if (isCollapsed.value) {
        _push(`<svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="currentColor" viewBox="0 0 448 512"><path d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"></path></svg>`);
      } else {
        _push(`<svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="currentColor" viewBox="0 0 448 512"><path d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"></path></svg>`);
      }
      _push(`</button></div><div style="${ssrRenderStyle(!isCollapsed.value ? null : { display: "none" })}" class="flex flex-col gap-4">`);
      _push(ssrRenderComponent(_sfc_main$4, {
        tracks: props.trackTree
      }, null, _parent));
      _push(ssrRenderComponent(CoursesSidebar, { courses: leftCourses.value }, null, _parent));
      _push(ssrRenderComponent(BannersSidebar, { banners: leftBanners.value }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$6, { videos: leftVideos.value }, null, _parent));
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup$2 = _sfc_main$2.setup;
_sfc_main$2.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Partials/LeftSidebarSchool.vue");
  return _sfc_setup$2 ? _sfc_setup$2(props, ctx) : void 0;
};
const _sfc_main$1 = {
  __name: "HashtagsSidebar",
  __ssrInlineRender: true,
  props: {
    hashtags: { type: [Array, Object], default: () => [] }
  },
  setup(__props) {
    const props = __props;
    const list = computed(() => unwrapList(props.hashtags));
    const hashtagName = (hashtag) => {
      var _a;
      const item = unwrap(hashtag);
      return ((_a = item == null ? void 0 : item.translation) == null ? void 0 : _a.name) || "";
    };
    return (_ctx, _push, _parent, _attrs) => {
      if (list.value.length) {
        _push(`<div${ssrRenderAttrs(mergeProps({ class: "flex flex-wrap items-center gap-2" }, _attrs))}><!--[-->`);
        ssrRenderList(list.value, (hashtag) => {
          _push(ssrRenderComponent(unref(Link), {
            key: unref(unwrap)(hashtag).id,
            href: _ctx.route("public.schoolHashtags.show", { slug: unref(unwrap)(hashtag).slug }),
            class: "flex items-center justify-start gap-2 rounded-md border border-gray-400 px-3 py-1 text-xs font-semibold text-gray-700 transition hover:bg-slate-200 dark:border-gray-400 dark:text-gray-300 dark:hover:bg-slate-800 dark:hover:text-slate-300"
          }, {
            default: withCtx((_, _push2, _parent2, _scopeId) => {
              if (_push2) {
                _push2(`${ssrInterpolate(hashtagName(hashtag))}`);
              } else {
                return [
                  createTextVNode(toDisplayString(hashtagName(hashtag)), 1)
                ];
              }
            }),
            _: 2
          }, _parent));
        });
        _push(`<!--]--></div>`);
      } else {
        _push(`<!---->`);
      }
    };
  }
};
const _sfc_setup$1 = _sfc_main$1.setup;
_sfc_main$1.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/School/SchoolHashtag/HashtagsSidebar.vue");
  return _sfc_setup$1 ? _sfc_setup$1(props, ctx) : void 0;
};
const _sfc_main = {
  __name: "RightSidebarSchool",
  __ssrInlineRender: true,
  props: {
    collapsed: { type: Boolean, default: false }
  },
  emits: ["collapsed"],
  setup(__props, { emit: __emit }) {
    const { t } = useI18n();
    const page = usePage();
    const props = __props;
    const hashtags = computed(() => page.props.hashtags ?? []);
    const rightCourses = computed(() => page.props.rightCourses ?? []);
    const rightBanners = computed(() => page.props.rightBanners ?? []);
    const rightVideos = computed(() => page.props.rightVideos ?? []);
    const isCollapsed = computed(() => props.collapsed);
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "relative" }, _attrs))}><div class="flex items-center justify-center mb-2"><button type="button" class="focus:outline-none"${ssrRenderAttr("title", unref(t)("toggleSidebar"))}>`);
      if (isCollapsed.value) {
        _push(`<svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="currentColor" viewBox="0 0 448 512"><path d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"></path></svg>`);
      } else {
        _push(`<svg class="w-6 h-6 text-indigo-500 dark:text-indigo-400" fill="currentColor" viewBox="0 0 448 512"><path d="M313.941 216H12c-6.627 0-12 5.373-12 12v56c0 6.627 5.373 12 12 12h301.941v46.059c0 21.382 25.851 32.09 40.971 16.971l86.059-86.059c9.373-9.373 9.373-24.569 0-33.941l-86.059-86.059c-15.119-15.119-40.971-4.411-40.971 16.971V216z"></path></svg>`);
      }
      _push(`</button></div><div style="${ssrRenderStyle(!isCollapsed.value ? null : { display: "none" })}" class="flex flex-col gap-4">`);
      _push(ssrRenderComponent(_sfc_main$1, { hashtags: hashtags.value }, null, _parent));
      _push(ssrRenderComponent(CoursesSidebar, { courses: rightCourses.value }, null, _parent));
      _push(ssrRenderComponent(BannersSidebar, { banners: rightBanners.value }, null, _parent));
      _push(ssrRenderComponent(_sfc_main$6, { videos: rightVideos.value }, null, _parent));
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Partials/RightSidebarSchool.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main$2 as _,
  _sfc_main as a
};
