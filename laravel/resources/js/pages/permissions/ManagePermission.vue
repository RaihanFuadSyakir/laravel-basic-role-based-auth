<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { permissionColumns } from '@/components/datatables/permissions_column';
import DataTable from '@/components/datatables/DataTable.vue';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manage Permissions',
        href: '/manage_permissions',
    },
];
    const props = defineProps({permissions : Array});
    const permissions = props.permissions as string[];
</script>
<template>
    <Head title="Manage Permissions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <DataTable :columns="permissionColumns" :data="permissions" />
        </div>
    </AppLayout>
</template>
