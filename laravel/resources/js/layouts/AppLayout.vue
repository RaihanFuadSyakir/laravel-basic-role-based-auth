<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
import type { BreadcrumbItemType } from '@/types';
interface Props {
    breadcrumbs?: BreadcrumbItemType[];
}
import { Toaster } from '@/components/ui/sonner';
import 'vue-sonner/style.css'
withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
});
</script>

<template>
    <Toaster class="pointer-events-auto"/>
    <AppLayout :breadcrumbs="breadcrumbs">
        <slot />
    </AppLayout>
</template>
