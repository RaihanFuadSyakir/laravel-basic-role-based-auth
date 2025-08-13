<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { User } from '@/types';
import { userColumns } from '@/components/datatables/users_column';
import DataTable from '@/components/datatables/DataTable.vue';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manage Users',
        href: '/manage_users',
    },
];
    const props = defineProps({users : Array});
    const users = props.users as User[];
</script>
<template>
    <Head title="Manage Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <DataTable :columns="userColumns" :data="users" />
        </div>
    </AppLayout>
</template>
