<script setup lang="ts">
import Navbar from '@/components/Navbar.vue'
</script>
<template>
    <div class="min-h-full">
        <Navbar />

        <div class="py-10">
            <header>
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <h1
                        v-if="$route.name"
                        class="text-3xl font-bold tracking-tight text-gray-900"
                    >
                        {{ $route.name }}
                    </h1>
                </div>
            </header>
            <main>
                <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>
