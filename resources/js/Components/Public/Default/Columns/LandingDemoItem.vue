<script setup>
defineProps({
    href: String,
    imgLight: String,
    imgDark: String,
    alt: String,
    category: String,
    title: String
})
</script>

<template>
    <div class="column is-4 has-text-centered">
        <a :href="href"
           class="vulk-demo-wrapper loaded is-block">

            <img class="is-image is-image-light"
                 :src="imgLight" :alt="alt" loading="lazy" />
            <img class="is-image is-image-dark"
                 :src="imgDark" :alt="alt" loading="lazy" />

            <div class="circle-overlay"></div>
            <div class="demo-info has-text-centered">
                <div class="wrapper">
                    <div class="demo-title">
                        <span>{{ category }}</span>
                        <span>{{ title }}</span>
                    </div>
                    <div class="demo-link">
                    <span class="go-to-demo flex flex-row items-center">
                      Go to demo
                      <svg
                          xmlns="http://www.w3.org/2000/svg" aria-hidden="true" role="img"
                          width="1em" height="1em" viewBox="0 0 24 24"
                          data-icon="feather:arrow-right"
                          class="iconify iconify--feather">
                        <path fill="none" stroke="currentColor" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2"
                              d="M5 12h14m-7-7l7 7l-7 7" />
                      </svg>
                    </span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</template>

<style scoped>
.column {
    margin-bottom: 2rem;
}
</style>
