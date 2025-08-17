<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { User } from '@/types';
import { userColumns } from '@/components/datatables/users_column';
import DataTable from '@/components/datatables/DataTable.vue';
import { ref } from 'vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manage Users',
        href: '/manage_users',
    },
];
function editUser(user: User) {
    editUserTarget.value = user;
    isEditOpen.value = true;
}

function deleteUser(id: number) {
  console.log("Deleting user:", id)
  // e.g. router.delete(`/users/${id}`)
}
const props = defineProps({users : Array});
const users = props.users as User[];
const isEditOpen = ref(false);
const editUserTarget = ref<User|null>(null);
const columns = userColumns({ onEdit: editUser, onDelete: deleteUser })
</script>
<template>
    <Head title="Manage Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <DataTable :columns="columns" :data="users" />
        </div>
        <Dialog v-model:open="isEditOpen">
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Edit User</DialogTitle>
              </DialogHeader>
              <div>Test</div>
              <DialogFooter>
                Save changes
              </DialogFooter>
            </DialogContent>
          </Dialog>
    </AppLayout>
</template>
