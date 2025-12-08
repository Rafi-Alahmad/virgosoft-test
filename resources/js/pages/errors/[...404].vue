<script setup>
import Err404 from '@/components/errors/404.vue';

definePage({
    alias: '/:404(.*)',
    meta: {
        layout: 'blank',
        public: true,
    },
});
</script>

<template>
<Err404 />
</template>

<style lang="scss"></style>
