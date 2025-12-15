<script setup lang="ts">
import Layout from '@/Layout.vue';
</script>

<template>
    <Layout>
        <RouterView :key="$route.path" />
    </Layout>
</template>
