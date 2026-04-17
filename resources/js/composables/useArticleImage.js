import { usePage } from '@inertiajs/vue3'
import { unwrap, unwrapList } from '@/Composables/useUnwrap'

export default function useArticleImage() {
    const { appUrl } = usePage().props

    const getImgSrc = (imgPath) => {
        if (!imgPath) return ''
        const base = appUrl?.endsWith('/') ? appUrl.slice(0, -1) : (appUrl || '')
        const path = imgPath.startsWith('/') ? imgPath.slice(1) : imgPath
        return `${base}/storage/${path}`
    }

    const getDefaultImg = () => '/storage/article_images/default-image.png'

    const onImgError = (e) => {
        e.target.src = getDefaultImg()
    }

    const getArticleImage = (article) => {
        const a = unwrap(article)

        if (a.img) return getImgSrc(a.img)

        const imgs = unwrapList(a.images)
        const first = imgs?.[0]
        const raw =
            first?.image_url ||
            first?.url ||
            first?.src ||
            first?.path ||
            first?.image ||
            null

        if (raw && /^https?:\/\//i.test(raw)) return raw
        if (raw) return getImgSrc(raw)

        return getDefaultImg()
    }

    return { getArticleImage, onImgError }
}
