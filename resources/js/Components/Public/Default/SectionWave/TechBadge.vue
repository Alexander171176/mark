<script setup>
const props = defineProps({
    img: { type: String, required: true },
    alt: { type: String, default: '' },
    title: { type: String, required: true },
    subtitle: { type: String, required: true },
    description: { type: String, required: true },
    popperPlacement: { type: String, default: 'top' },
})

const d = {
    wrap: { 'data-v-f048bcd4': '' },
    inline: { 'data-v-5784ed69': '', 'data-v-f048bcd4': '' },
    avatarLarge: { 'data-v-805a4e10': '', 'data-v-f048bcd4': '', 'data-v-5784ed69-s': '' },
    avatarSmall: { 'data-v-805a4e10': '', 'data-v-f048bcd4': '', 'data-v-5784ed69-s': '' },
    popper: { 'data-v-5784ed69': '' },
    popoverHead: { 'data-v-f048bcd4': '', 'data-v-5784ed69-s': '' },
    meta: { 'data-v-f048bcd4': '', 'data-v-5784ed69-s': '' },
    body: { 'data-v-f048bcd4': '', 'data-v-5784ed69-s': '' },
    arrow: { 'data-v-20b7fd4a': '', 'data-v-5784ed69': '' },
}
</script>

<template>
    <div
        v-bind="d.wrap"
        class="image-wrap badge bg-gray-100 dark:bg-gray-950"
        tabindex="0"
        role="button"
        aria-haspopup="dialog"
    >
        <div
            v-bind="d.inline"
            class="inline-block"
            style="border:20px solid transparent; margin:-20px; --c81fc0a4: 9999;"
        >
            <div>
                <div v-bind="d.avatarLarge" class="avatar is-large">
                    <img
                        :src="img"
                        :alt="alt || title"
                        class="avatar-image"
                        height="38"
                        width="38"
                    />
                </div>
            </div>

            <div
                v-bind="d.popper"
                class="popper"
                role="dialog"
                aria-hidden="true"
                :data-popper-placement="popperPlacement"
            >
                <div v-bind="d.popoverHead" class="popover-head">
                    <div v-bind="d.avatarSmall" class="avatar is-small">
                        <img
                            :src="img"
                            :alt="alt || title"
                            class="avatar-image"
                            height="38"
                            width="38"
                        />
                    </div>
                    <div v-bind="d.meta" class="meta">
                        <span class="meta-title">{{ title }}</span>
                        <span class="meta-subtitle">{{ subtitle }}</span>
                    </div>
                </div>

                <div v-bind="d.body" class="popover-body">
                    <p class="paragraph rem-75">{{ description }}</p>
                </div>

                <div v-bind="d.arrow" class="arrow" data-popper-arrow></div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* ====== БАЗА: размеры и выравнивание ====== */
.badge {
    --badge-size: 60px;
    --ring-size-1: calc(var(--badge-size) + 8px);
    --ring-size-2: calc(var(--badge-size) + 18px);

    position: relative;
    display: inline-flex;
    vertical-align: top;
    width: var(--badge-size);
    height: var(--badge-size);
    border-radius: 50%;
    align-items: center;
    justify-content: center;
    cursor: pointer;

    //background: var(--widget-grey, rgba(0,0,0,0.04));
    transition: background 0.2s ease;
    outline: none;
}

/* внутренний holder */
.badge .inline-block {
    margin: 0 !important;
    border: 0 !important;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* иконка внутри круга */
.badge .avatar.is-large .avatar-image {
    width: calc(var(--badge-size) * 0.7);   /* ~40px при круге 50px */
    height: calc(var(--badge-size) * 0.7);
    display: block;
}

/* ====== КОЛЬЦА (hover) ====== */
.badge::before,
.badge::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    opacity: 0;
    transition: transform 0.25s ease, opacity 0.25s ease;
    z-index: 0;
}

/* внутреннее кольцо */
.badge::before {
    width: var(--ring-size-1);
    height: var(--ring-size-1);
    box-shadow: 0 0 0 2px rgba(0,0,0,0.06);
    transform: translate(-50%, -50%) scale(0.92);
    top: 50%;
    left: 50%;
}

/* внешнее кольцо */
.badge::after {
    width: var(--ring-size-2);
    height: var(--ring-size-2);
    box-shadow: 0 0 0 2px rgba(0,0,0,0.04);
    transform: translate(-50%, -50%) scale(0.92);
    top: 30px;
    left: 30px;
}

/* hover/focus — фон и кольца */
.badge:hover,
.badge:focus-visible,
.badge:focus-within {
    background: var(--widget-grey-6, rgba(0,0,0,0.06));
}
.badge:hover::before,
.badge:focus-within::before,
.badge:hover::after,
.badge:focus-within::after {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
}

/* ====== POPPER ====== */
.popper {
    position: absolute;
    bottom: calc(100% + 16px);
    left: 50%;
    transform: translateX(-50%);
    min-width: 220px;
    max-width: 280px;
    padding: 0.75rem;
    border-radius: 0.75rem;
    background: var(--card-bg-color, #fff);
    border: 1px solid var(--card-border-color, rgba(0, 0, 0, .08));
    box-shadow: var(--light-box-shadow, 0 10px 20px rgba(0, 0, 0, .08));
    display: none;
    z-index: 20;
}

.badge:hover .popper,
.badge:focus-within .popper {
    display: block;
}

/* popper header */
.popover-head {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: .5rem;
}
.meta-title { font-weight: 600; display: block; }
.meta-subtitle { font-size: .85rem; opacity: .8; }

/* стрелочка */
.arrow {
    position: absolute;
    top: 95%;
    left: 50%;
    width: 12px;
    height: 12px;
    background: var(--card-bg-color, #fff);
    border-left: 1px solid var(--card-border-color, rgba(0, 0, 0, .08));
    border-bottom: 1px solid var(--card-border-color, rgba(0, 0, 0, .08));
    transform: translateX(-50%) rotate(45deg);
    transform-origin: center;
}

/* ====== РЯД ====== */
.tech-badges-row {
    gap: 1.25rem;
    flex-wrap: wrap;
}

@media (max-width: 480px) {
    .popper { left: 0; transform: none; }
    .arrow  { left: 24px; transform: rotate(45deg); }
}
</style>
