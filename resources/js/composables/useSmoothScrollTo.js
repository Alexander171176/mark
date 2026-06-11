import { ref } from 'vue'

export function useSmoothScrollTo(options = {}) {
    const targetRef = ref(null)

    const scrollToTarget = () => {
        if (!targetRef.value) {
            return
        }

        const offset = options.offset ?? 80
        const duration = options.duration ?? 1200

        const target =
            targetRef.value.getBoundingClientRect().top +
            window.scrollY -
            offset

        const start = window.scrollY
        const distance = target - start

        let startTime = null

        const easeOutCubic = (t) => {
            return 1 - Math.pow(1 - t, 3)
        }

        const animate = (timestamp) => {
            if (!startTime) {
                startTime = timestamp
            }

            const progress = Math.min(
                (timestamp - startTime) / duration,
                1
            )

            window.scrollTo(
                0,
                start + distance * easeOutCubic(progress)
            )

            if (progress < 1) {
                requestAnimationFrame(animate)
            }
        }

        requestAnimationFrame(animate)
    }

    return {
        targetRef,
        scrollToTarget,
    }
}
