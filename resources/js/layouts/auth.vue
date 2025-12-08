<script setup>
import { RouterView } from 'vue-router';
import { Toaster } from 'vue-sonner';
</script>
<template>
    <Toaster position="top-right" richColors closeButton />
    <div class="min-h-screen bg-slate-950 text-white">
        <RouterView />
    </div>
</template>


