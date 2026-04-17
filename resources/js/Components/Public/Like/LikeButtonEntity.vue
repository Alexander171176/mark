<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const props = defineProps({
    likesCount: {
        type: Number,
        default: 0,
    },
    alreadyLiked: {
        type: Boolean,
        default: false,
    },
    routeName: {
        type: String,
        required: true,
    },
    routeParams: {
        type: [Object, Array, String, Number],
        default: () => ({}),
    },
    title: {
        type: String,
        default: '',
    },
    iconClass: {
        type: String,
        default: 'w-3 h-3',
    },
})

const likes = ref(Number(props.likesCount) || 0)
const liked = ref(!!props.alreadyLiked)
const isAnimating = ref(false)
const showBurst = ref(false)
const showPlusOne = ref(false)

watch(
    () => props.likesCount,
    (value) => {
        likes.value = Number(value) || 0
    }
)

watch(
    () => props.alreadyLiked,
    (value) => {
        liked.value = !!value
    }
)

const playLikeAnimation = () => {
    isAnimating.value = false
    showBurst.value = false
    showPlusOne.value = false

    requestAnimationFrame(() => {
        isAnimating.value = true
        showBurst.value = true
        showPlusOne.value = true

        setTimeout(() => {
            showBurst.value = false
        }, 450)

        setTimeout(() => {
            showPlusOne.value = false
        }, 700)

        setTimeout(() => {
            isAnimating.value = false
        }, 500)
    })
}

const likeEntity = async () => {
    if (liked.value) return

    try {
        const response = await axios.post(
            route(props.routeName, props.routeParams)
        )

        if (response.data?.success) {
            likes.value = Number(response.data.likes ?? likes.value + 1)
            liked.value = true
            playLikeAnimation()
            return
        }

        alert(response.data?.message || t('alertLikeAlready'))
    } catch (error) {
        if (error.response?.status === 401) {
            alert(t('alertLikeAuthRequired'))
            return
        }

        console.error('Ошибка лайка:', error)
        alert(t('alertLikeError'))
    }
}
</script>

<template>
    <div
        itemprop="interactionStatistic"
        itemscope
        itemtype="http://schema.org/InteractionCounter"
    >
        <meta itemprop="interactionType" content="http://schema.org/LikeAction">
        <meta itemprop="userInteractionCount" :content="likes">

        <div
            :title="title || t('like')"
            class="relative w-fit flex flex-row items-center justify-center cursor-pointer select-none"
        >
            <div class="relative flex items-center justify-center">
                <!-- burst rings -->
                <span
                    v-if="showBurst"
                    class="pointer-events-none absolute inset-0 rounded-full burst-ring burst-ring-1"
                />
                <span
                    v-if="showBurst"
                    class="pointer-events-none absolute inset-0 rounded-full burst-ring burst-ring-2"
                />

                <!-- floating +1 -->
                <span
                    v-if="showPlusOne"
                    class="pointer-events-none absolute -top-4 left-1/2 -translate-x-1/2 text-[10px] font-bold text-rose-500 dark:text-rose-300 plus-one"
                >
                    +1
                </span>

                <svg
                    @click="likeEntity"
                    :class="[
                        iconClass,
                        'relative z-10 fill-current transition-all duration-200 transform hover:scale-110 active:scale-95',
                        isAnimating ? 'like-bounce' : '',
                        liked
                            ? 'text-red-500 dark:text-red-300'
                            : 'text-amber-500 dark:text-amber-400 hover:text-yellow-600 dark:hover:text-yellow-200 active:text-yellow-300 dark:active:text-yellow-100'
                    ]"
                    viewBox="0 0 24 24"
                >
                    <path d="M3,9H1a1,1,0,0,0-1,1V22a1,1,0,0,0,1,1H4V10A1,1,0,0,0,3,9Z"></path>
                    <path
                        d="M21.882,8.133A2.986,2.986,0,0,0,21,8H15V5c0-3.824-2.589-4.942-3.958-5a1.017,1.017,0,0,0-.734.277A1,1,0,0,0,10,1V5.638l-4,4.8V23H18.23A2.985,2.985,0,0,0,21.1,20.882l2.769-9A3,3,0,0,0,21.882,8.133Z"
                    ></path>
                </svg>
            </div>

            <span
                v-if="likes > 0"
                class="ml-1 font-semibold text-xs dark:text-slate-100"
            >
                {{ likes }}
            </span>
        </div>
    </div>
</template>

<style scoped>
.like-bounce {
    animation: like-bounce 0.45s ease;
}

@keyframes like-bounce {
    0% {
        transform: scale(1);
    }
    30% {
        transform: scale(1.35);
    }
    55% {
        transform: scale(0.92);
    }
    75% {
        transform: scale(1.12);
    }
    100% {
        transform: scale(1);
    }
}

.burst-ring {
    width: 100%;
    height: 100%;
    border: 2px solid rgba(244, 63, 94, 0.55);
    transform: scale(0.6);
    opacity: 0;
}

.burst-ring-1 {
    animation: burst-ring 0.45s ease-out;
}

.burst-ring-2 {
    animation: burst-ring 0.45s ease-out 0.06s;
}

@keyframes burst-ring {
    0% {
        transform: scale(0.55);
        opacity: 0.75;
    }
    100% {
        transform: scale(2.1);
        opacity: 0;
    }
}

.plus-one {
    animation: plus-one-float 0.7s ease-out forwards;
}

@keyframes plus-one-float {
    0% {
        opacity: 0;
        transform: translate(-50%, 4px) scale(0.9);
    }
    20% {
        opacity: 1;
        transform: translate(-50%, -2px) scale(1);
    }
    100% {
        opacity: 0;
        transform: translate(-50%, -16px) scale(1.05);
    }
}
</style>
