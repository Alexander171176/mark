<script setup>
import { onBeforeUnmount, onMounted } from 'vue'
import axios from 'axios'

const CONSENT_ACCEPTED_KEY = 'privacy_user_consent_accepted'
const CONSENT_VERSION_KEY = 'privacy_user_consent_version'
const VISITOR_UUID_KEY = 'analytics_visitor_uuid'
const POLICY_VERSION = '1'

let startedAt = Date.now()
let maxScrollDepth = 0
let clicksCount = 0

const getVisitorUuid = () => localStorage.getItem(VISITOR_UUID_KEY)

const setVisitorUuid = (uuid) => {
    if (uuid) {
        localStorage.setItem(VISITOR_UUID_KEY, uuid)
    }
}

const hasLocalConsent = () => {
    return localStorage.getItem(CONSENT_ACCEPTED_KEY) === 'true'
        && localStorage.getItem(CONSENT_VERSION_KEY) === POLICY_VERSION
}

const getScreenData = () => ({
    screen_width: window.screen?.width ?? null,
    screen_height: window.screen?.height ?? null,
    browser_language: navigator.language ?? null,
    timezone: Intl.DateTimeFormat().resolvedOptions().timeZone ?? null,
})

const updateScrollDepth = () => {
    const scrollTop = window.scrollY || document.documentElement.scrollTop
    const documentHeight = document.documentElement.scrollHeight - window.innerHeight

    if (documentHeight <= 0) {
        maxScrollDepth = 100
        return
    }

    const currentDepth = Math.round((scrollTop / documentHeight) * 100)

    if (currentDepth > maxScrollDepth) {
        maxScrollDepth = Math.min(currentDepth, 100)
    }
}

const sendAnalytics = async () => {
    if (!hasLocalConsent()) {
        return
    }

    updateScrollDepth()

    try {
        const response = await axios.post('/api/analytics/visitor-logs', {
            visitor_uuid: getVisitorUuid(),

            method: 'GET',
            url: window.location.href,
            page_title: document.title || null,
            route_name: null,

            module: null,
            entity_type: null,
            entity_id: null,
            event_type: 'page_view',

            request_type: 'web',

            referer: document.referrer || null,

            time_on_page: Math.round((Date.now() - startedAt) / 1000),
            scroll_depth: maxScrollDepth,
            clicks_count: clicksCount,

            locale: document.documentElement.lang || null,

            ...getScreenData(),
        })

        setVisitorUuid(response.data?.visitor_uuid)
    } catch (error) {
        console.error('Ошибка сохранения аналитики:', error)
    }
}

const handleClick = () => {
    clicksCount++
}

onMounted(() => {
    startedAt = Date.now()

    window.addEventListener('scroll', updateScrollDepth, { passive: true })
    window.addEventListener('click', handleClick)
    window.addEventListener('beforeunload', sendAnalytics)

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'hidden') {
            sendAnalytics()
        }
    })
})

onBeforeUnmount(() => {
    sendAnalytics()

    window.removeEventListener('scroll', updateScrollDepth)
    window.removeEventListener('click', handleClick)
    window.removeEventListener('beforeunload', sendAnalytics)
})
</script>

<template>
    <span class="hidden" aria-hidden="true"></span>
</template>
