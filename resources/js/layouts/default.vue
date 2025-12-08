<script setup>
import { RouterView } from 'vue-router';
import { Toaster } from 'vue-sonner';
</script>
<template>
    <Toaster position="top-right" richColors closeButton />
    <div class="min-h-screen bg-slate-950 text-white">
        <AppNavbar />
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <RouterView />
        </main>
    </div>
</template>


