<script setup>
defineProps({
    title: {
        type: String,
        default: "",
    },
    subtitle: {
        type: String,
        default: "",
    },
    padding: {
        type: [String, Number],
        default: "8",
    },
});
</script>

<template>
    <div
        class="bg-slate-900/50 backdrop-blur-xl border border-slate-800 rounded-2xl shadow-2xl"
        :class="[padding ? `p-${padding}` : 'p-8']"
    >
        <div v-if="$slots.title || title" class="mb-6">
            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                <slot name="icon" />
                <slot name="title">
                    {{ title }}
                </slot>
            </h2>
            <p v-if="subtitle" class="text-slate-500 mt-1">{{ subtitle }}</p>
        </div>
        <slot />
    </div>
</template>
