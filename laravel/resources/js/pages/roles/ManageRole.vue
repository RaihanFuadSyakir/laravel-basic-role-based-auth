<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { AppPageProps, Role, type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { roleColumns } from '@/components/datatables/roles_column';
import DataTable from '@/components/datatables/DataTable.vue';
import { computed } from 'vue';
import { ref } from 'vue';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination/index"
import Input from '@/components/ui/input/Input.vue';
import { Filter } from 'lucide-vue-next';
import { useForm } from 'vee-validate';
import { formUpdateSchema } from '@/lib/roles/roles_schema';
import { toTypedSchema } from '@vee-validate/zod';
import { toast } from 'vue-sonner';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manage Roles',
        href: '/manage_roles',
    },
];
import TagsCombobox from '@/components/TagsCombobox.vue';
const page = usePage< AppPageProps & {
 data : {
  roles : Role[],
  permissions : string[]
 },
  pagination: {
    total: number,
    currentPage: number,
    lastPage: number
  },
}>();

const pagination = computed(() => page.props.pagination);
const roles = computed(() => page.props.data.roles);
const hasEditPermission = page.props.auth.permissions.some(
  (perm: string) => perm === "edit_roles"
)
const formUpdate = useForm({
    validationSchema : toTypedSchema(formUpdateSchema),
    initialValues : {
        id : 0,
        name : '',
        permissions : []
    }
});
const roleTarget = ref<Role | null>(null);
const isEditOpen = ref(false);
const confirmDelete = ref(false);
function onEdit(role: Role) {
    roleTarget.value = role;
    formUpdate.setValues({
        id : roleTarget.value.id,
        name : roleTarget.value.name,
        permissions : roleTarget.value.permissions,
    });
    isEditOpen.value = true;
}

function onDelete(role : Role) {
    roleTarget.value = role;
    confirmDelete.value = true;
  // e.g. router.delete(`/users/${id}`)
}
const handleUpdateSubmit = formUpdate.handleSubmit((values) => {
    router.put(`/roles/${values.id}`,values,{
        onSuccess : () => {
            toast.success('Role updated successfully');
            fetchData();
            isEditOpen.value = false;
            roleTarget.value = null;
            formUpdate.resetForm();
        },
        onError : () => {
            toast.error('Update unexpected error');
        }
    })
  })
const handleDeleteSubmit = () =>{
    if(roleTarget.value){
        router.delete(`/roles/${roleTarget.value.id}`,{
            onSuccess : () => {
                toast.success('Role deleted successfully');
                fetchData();
                confirmDelete.value = false;
                roleTarget.value = null;
            },
            onError : () => {
                toast.error('Update unexpected error');
            }
        });
    }
}
function fetchData(){
  router.get('/manage_roles', { page : pagination.value.currentPage, ...filters.value }, { preserveState: true, preserveScroll: true })
}
function addFilter(key: string, value: string | string[]) {
  if (Array.isArray(value)) {
    filters.value = { ...filters.value, [key]: value.join(',') };
  } else {
    filters.value = { ...filters.value, [key]: value };
  }
  fetchData();
}
const filters = ref<Record<string, string | string[]>>({});
const permissionsFilter = ref<string[]>([]);
const permissions = page.props.data.permissions;
const goToPage = (page: number) => {
  if (page < 1 || page > pagination.value.lastPage) return
  fetchData();
}
const column = roleColumns({
    hasEditPermission : hasEditPermission,
    onEdit : onEdit,
    onDelete : onDelete
    });
</script>
<template>
    <Head title="Manage Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="grid md:grid-cols-3 gap-4">
                <div class="md:ml-4 m-2 flex items-center">
                    <div class="relative flex items-center flex-1">
                      <!-- Icon inside input -->
                      <Filter class="absolute left-2 size-4 opacity-50 pointer-events-none" />

                      <!-- Input with padding so text doesn't overlap icon -->
                      <Input
                        type="text"
                        placeholder="Filter by name"
                        class="pl-8"
                        @input="(e: any) => addFilter('name', e.target.value)"
                      />
                    </div>
                </div>
                <div class="flex justify-center m-2">
                    <TagsCombobox class="flex-1" v-model="permissionsFilter" :options="permissions" placeholder="Filter By Permissions"/>
                </div>
            </div>
            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                <DataTable :columns="column" :data="roles" />
            </div>
            <div v-if="pagination" class="flex justify-end">
                <Pagination
                  v-slot="{ page }"
                  :items-per-page="10"
                  :total="pagination.total"
                  :default-page="pagination.currentPage"
                >
                  <PaginationContent v-slot="{ items }">
                    <PaginationPrevious
                      :disabled="pagination.currentPage === 1"
                      @click="goToPage(pagination.currentPage - 1)"
                    />

                    <template v-for="(item, index) in items" :key="index">
                      <PaginationItem
                        v-if="item.type === 'page'"
                        :value="item.value"
                        :is-active="item.value === pagination.currentPage"
                        @click="goToPage(item.value)"
                      >
                        {{ item.value }}
                      </PaginationItem>
                    </template>

                    <PaginationEllipsis v-if="pagination.lastPage > 5" />

                    <PaginationNext
                      :disabled="pagination.currentPage === pagination.lastPage"
                      @click="goToPage(pagination.currentPage + 1)"
                    />
                  </PaginationContent>
                </Pagination>
            </div>
        </div>
    </AppLayout>
</template>
