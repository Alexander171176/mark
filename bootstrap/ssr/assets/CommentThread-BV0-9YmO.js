import { ref, onMounted, watch, mergeProps, unref, useSSRContext } from "vue";
import { ssrRenderAttrs, ssrInterpolate, ssrRenderStyle, ssrRenderList, ssrRenderAttr, ssrIncludeBooleanAttr } from "vue/server-renderer";
import { useI18n } from "vue-i18n";
import axios from "axios";
const _sfc_main = {
  __name: "CommentThread",
  __ssrInlineRender: true,
  props: {
    /**
     * Полный class модели:
     *
     * App\Models\Admin\Blog\BlogArticle\BlogArticle
     * или
     * App\Models\Admin\Blog\BlogVideo\BlogVideo
     */
    commentableType: {
      type: String,
      required: true
    },
    commentableId: {
      type: Number,
      required: true
    },
    /**
     * null для гостя.
     */
    authUser: {
      type: Object,
      default: null
    }
  },
  setup(__props) {
    const { t } = useI18n();
    const props = __props;
    const isVisible = ref(true);
    const comments = ref([]);
    const loading = ref(false);
    const error = ref(null);
    const editingId = ref(null);
    const editText = ref("");
    const replyingId = ref(null);
    const replyText = ref("");
    const newText = ref("");
    const submitting = ref(false);
    const normalizeUser = (user) => {
      if (!user) {
        return {
          id: null,
          name: t("unknownAuthor"),
          profile_photo_url: null
        };
      }
      return {
        id: user.id ?? null,
        name: user.name || t("unknownAuthor"),
        profile_photo_url: user.profile_photo_url || null
      };
    };
    const normalizeReply = (reply) => {
      return {
        id: (reply == null ? void 0 : reply.id) ?? null,
        parent_id: (reply == null ? void 0 : reply.parent_id) ?? null,
        content: (reply == null ? void 0 : reply.content) ?? "",
        user: normalizeUser(
          reply == null ? void 0 : reply.user
        ),
        created_at: (reply == null ? void 0 : reply.created_at) ?? null,
        updated_at: (reply == null ? void 0 : reply.updated_at) ?? null
      };
    };
    const normalizeComment = (comment) => {
      var _a;
      const replies = Array.isArray(
        comment == null ? void 0 : comment.replies
      ) ? comment.replies : Array.isArray(
        (_a = comment == null ? void 0 : comment.replies) == null ? void 0 : _a.data
      ) ? comment.replies.data : [];
      return {
        id: (comment == null ? void 0 : comment.id) ?? null,
        parent_id: (comment == null ? void 0 : comment.parent_id) ?? null,
        content: (comment == null ? void 0 : comment.content) ?? "",
        user: normalizeUser(
          comment == null ? void 0 : comment.user
        ),
        replies_count: Number(
          (comment == null ? void 0 : comment.replies_count) ?? replies.length ?? 0
        ),
        replies: replies.map(
          normalizeReply
        ),
        created_at: (comment == null ? void 0 : comment.created_at) ?? null,
        updated_at: (comment == null ? void 0 : comment.updated_at) ?? null
      };
    };
    const normalizeComments = (payload) => {
      const list = Array.isArray(payload) ? payload : Array.isArray(payload == null ? void 0 : payload.data) ? payload.data : [];
      return list.map(
        normalizeComment
      );
    };
    const fetchComments = async () => {
      var _a, _b;
      loading.value = true;
      error.value = null;
      try {
        const { data } = await axios.get(
          "/api/comments",
          {
            params: {
              commentable_type: String(
                props.commentableType
              ),
              commentable_id: Number(
                props.commentableId
              )
            }
          }
        );
        comments.value = normalizeComments(data);
      } catch (err) {
        console.error(
          t("commentErrorLoading"),
          err.response || err
        );
        if ((_b = (_a = err.response) == null ? void 0 : _a.data) == null ? void 0 : _b.message) {
          error.value = `${t("error")}: ${err.response.data.message}`;
        } else {
          error.value = t("commentErrorLoading");
        }
        comments.value = [];
      } finally {
        loading.value = false;
      }
    };
    const cancelEdit = () => {
      editingId.value = null;
      editText.value = "";
    };
    const cancelReply = () => {
      replyingId.value = null;
      replyText.value = "";
    };
    onMounted(() => {
      fetchComments();
    });
    watch(
      () => [
        props.commentableType,
        props.commentableId
      ],
      () => {
        cancelEdit();
        cancelReply();
        fetchComments();
      }
    );
    return (_ctx, _push, _parent, _attrs) => {
      _push(`<div${ssrRenderAttrs(mergeProps({ class: "mt-4" }, _attrs))}><h3 class="mb-4 flex items-center justify-center text-center cursor-pointer select-none text-indigo-700 dark:text-indigo-300"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 512 512"><path d="M448 0H64C28.7 0 0 28.7 0 64v288c0 35.3 28.7 64 64 64h96v84c0 9.8 11.2 15.5 19.1 9.7L304 416h144c35.3 0 64-28.7 64-64V64c0-35.3-28.7-64-64-64z"></path></svg><span class="mx-2 border-b dashed border-gray-400 font-semibold text-md">${ssrInterpolate(unref(t)("comments"))}</span><span class="text-sm text-red-500 dark:text-yellow-300">${ssrInterpolate(isVisible.value ? "▲" : "▼")}</span></h3><div style="${ssrRenderStyle(isVisible.value ? null : { display: "none" })}">`);
      if (loading.value) {
        _push(`<div class="text-sm text-center text-gray-500">${ssrInterpolate(unref(t)("uploadingComments"))}</div>`);
      } else if (error.value) {
        _push(`<div class="text-sm text-center text-red-500">${ssrInterpolate(error.value)}</div>`);
      } else {
        _push(`<!--[-->`);
        if (!comments.value.length) {
          _push(`<div class="text-sm text-center text-gray-500 dark:text-gray-400">${ssrInterpolate(unref(t)("noCommentsYet"))}</div>`);
        } else {
          _push(`<!---->`);
        }
        _push(`<!--[-->`);
        ssrRenderList(comments.value, (comment) => {
          var _a, _b, _c, _d, _e;
          _push(`<div itemprop="comment" itemscope itemtype="https://schema.org/Comment" class="mb-3 rounded border p-3 bg-gray-50 dark:bg-slate-800">`);
          if (comment.created_at) {
            _push(`<meta itemprop="dateCreated"${ssrRenderAttr("content", comment.created_at)}>`);
          } else {
            _push(`<!---->`);
          }
          if (comment.updated_at) {
            _push(`<meta itemprop="dateModified"${ssrRenderAttr("content", comment.updated_at)}>`);
          } else {
            _push(`<!---->`);
          }
          _push(`<div class="flex items-center justify-between gap-3 text-sm"><div itemprop="author" itemscope itemtype="https://schema.org/Person" class="flex min-w-0 items-center gap-2">`);
          if ((_a = comment.user) == null ? void 0 : _a.profile_photo_url) {
            _push(`<img${ssrRenderAttr("src", comment.user.profile_photo_url)}${ssrRenderAttr("alt", ((_b = comment.user) == null ? void 0 : _b.name) || unref(t)("unknownAuthor"))} itemprop="image" loading="lazy" class="h-6 w-6 rounded-full object-cover">`);
          } else {
            _push(`<!---->`);
          }
          _push(`<span itemprop="name" class="truncate font-semibold text-blue-600 dark:text-blue-300">${ssrInterpolate(((_c = comment.user) == null ? void 0 : _c.name) || unref(t)("unknownAuthor"))}</span></div>`);
          if (__props.authUser) {
            _push(`<div class="flex flex-wrap items-center gap-1 text-xs"><button type="button" class="flex items-center justify-center gap-1 px-3 py-1 text-slate-600 transition hover:text-blue-700 dark:text-slate-400 dark:hover:text-blue-300"><span>${ssrInterpolate(unref(t)("reply"))}</span></button>`);
            if (__props.authUser.id === ((_d = comment.user) == null ? void 0 : _d.id)) {
              _push(`<!--[--><button type="button" class="px-3 py-1 text-green-600 transition hover:text-teal-700 dark:text-green-400 dark:hover:text-teal-300">${ssrInterpolate(unref(t)("edit"))}</button><button type="button" class="px-3 py-1 text-red-600 transition hover:text-rose-700 dark:text-red-400 dark:hover:text-rose-300">${ssrInterpolate(unref(t)("delete"))}</button><!--]-->`);
            } else {
              _push(`<!---->`);
            }
            _push(`</div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
          if (editingId.value === comment.id) {
            _push(`<div class="mt-2 space-y-2"><textarea maxlength="500" class="w-full rounded border p-2 text-sm bg-white text-black dark:bg-slate-700 dark:text-white">${ssrInterpolate(editText.value)}</textarea><div class="flex justify-end gap-2"><button type="button"${ssrIncludeBooleanAttr(submitting.value) ? " disabled" : ""} class="rounded-sm px-3 py-1 btn-default disabled:opacity-50">${ssrInterpolate(unref(t)("save"))}</button><button type="button"${ssrIncludeBooleanAttr(submitting.value) ? " disabled" : ""} class="rounded-sm border border-gray-400 px-3 py-1">${ssrInterpolate(unref(t)("cancel"))}</button></div></div>`);
          } else {
            _push(`<div itemprop="text" class="mt-2 text-sm text-gray-800 dark:text-gray-200">${ssrInterpolate(comment.content)}</div>`);
          }
          if (__props.authUser && replyingId.value === comment.id) {
            _push(`<div class="mt-3"><textarea maxlength="500" class="w-full rounded border p-2 text-sm bg-white text-black dark:bg-slate-700 dark:text-white"${ssrRenderAttr(
              "placeholder",
              unref(t)("commentYourAnswer")
            )}>${ssrInterpolate(replyText.value)}</textarea><div class="mt-2 flex justify-end gap-2"><button type="button"${ssrIncludeBooleanAttr(
              submitting.value || !replyText.value.trim()
            ) ? " disabled" : ""} class="rounded-sm px-3 py-1 btn-default disabled:opacity-50">${ssrInterpolate(unref(t)("send"))}</button><button type="button"${ssrIncludeBooleanAttr(submitting.value) ? " disabled" : ""} class="rounded-sm border border-gray-400 px-3 py-1">${ssrInterpolate(unref(t)("cancel"))}</button></div></div>`);
          } else {
            _push(`<!---->`);
          }
          if ((_e = comment.replies) == null ? void 0 : _e.length) {
            _push(`<div class="mt-3 space-y-2 border-l-2 border-gray-400 pl-4"><!--[-->`);
            ssrRenderList(comment.replies, (reply) => {
              var _a2, _b2;
              _push(`<div itemprop="comment" itemscope itemtype="https://schema.org/Comment">`);
              if (reply.created_at) {
                _push(`<meta itemprop="dateCreated"${ssrRenderAttr("content", reply.created_at)}>`);
              } else {
                _push(`<!---->`);
              }
              if (reply.updated_at) {
                _push(`<meta itemprop="dateModified"${ssrRenderAttr("content", reply.updated_at)}>`);
              } else {
                _push(`<!---->`);
              }
              _push(`<div class="flex items-center justify-between gap-2"><span itemprop="author" itemscope itemtype="https://schema.org/Person"><span itemprop="name" class="text-xs font-semibold text-purple-600 dark:text-purple-300">${ssrInterpolate(((_a2 = reply.user) == null ? void 0 : _a2.name) || unref(t)("unknownAuthor"))}</span></span>`);
              if (__props.authUser && __props.authUser.id === ((_b2 = reply.user) == null ? void 0 : _b2.id)) {
                _push(`<div class="flex gap-2"><button type="button" class="text-xs font-semibold text-teal-700 hover:opacity-75 dark:text-teal-300">${ssrInterpolate(unref(t)("edit"))}</button><button type="button" class="text-xs font-semibold text-red-600 hover:opacity-75 dark:text-red-300">${ssrInterpolate(unref(t)("delete"))}</button></div>`);
              } else {
                _push(`<!---->`);
              }
              _push(`</div>`);
              if (editingId.value === reply.id) {
                _push(`<div class="mt-1"><textarea maxlength="500" class="w-full rounded border p-2 text-sm bg-white text-black dark:bg-slate-700 dark:text-white">${ssrInterpolate(editText.value)}</textarea><div class="mt-1 flex justify-end gap-2"><button type="button"${ssrIncludeBooleanAttr(
                  submitting.value
                ) ? " disabled" : ""} class="text-xs font-semibold">${ssrInterpolate(unref(t)("save"))}</button><button type="button"${ssrIncludeBooleanAttr(
                  submitting.value
                ) ? " disabled" : ""} class="text-xs font-semibold">${ssrInterpolate(unref(t)("cancel"))}</button></div></div>`);
              } else {
                _push(`<div itemprop="text" class="mt-1 text-sm text-gray-700 dark:text-gray-200">${ssrInterpolate(reply.content)}</div>`);
              }
              _push(`</div>`);
            });
            _push(`<!--]--></div>`);
          } else {
            _push(`<!---->`);
          }
          _push(`</div>`);
        });
        _push(`<!--]-->`);
        if (__props.authUser) {
          _push(`<div class="mt-4 border-t pt-3"><h4 class="mb-2 text-sm font-semibold text-gray-700 dark:text-gray-300">${ssrInterpolate(unref(t)("commentLeave"))}</h4><textarea maxlength="500" class="w-full rounded border border-gray-400 px-2 py-1 text-sm bg-white text-black dark:bg-slate-700 dark:text-white"${ssrRenderAttr(
            "placeholder",
            unref(t)("commentYour")
          )}>${ssrInterpolate(newText.value)}</textarea><div class="mt-2 flex justify-end"><button type="button"${ssrIncludeBooleanAttr(
            submitting.value || !newText.value.trim()
          ) ? " disabled" : ""} class="flex w-full items-center justify-center rounded-sm px-3 py-2 btn-default disabled:opacity-50">${ssrInterpolate(unref(t)("send"))}</button></div></div>`);
        } else {
          _push(`<div class="mt-4 border-t pt-3 text-center text-sm text-gray-500 dark:text-gray-400">${ssrInterpolate(unref(t)("commentLogin"))}</div>`);
        }
        _push(`<!--]-->`);
      }
      _push(`</div></div>`);
    };
  }
};
const _sfc_setup = _sfc_main.setup;
_sfc_main.setup = (props, ctx) => {
  const ssrContext = useSSRContext();
  (ssrContext.modules || (ssrContext.modules = /* @__PURE__ */ new Set())).add("resources/js/Components/Public/Default/Blog/Comment/CommentThread.vue");
  return _sfc_setup ? _sfc_setup(props, ctx) : void 0;
};
export {
  _sfc_main as _
};
