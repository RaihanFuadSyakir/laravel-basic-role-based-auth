<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { AppPageProps, Role, type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { roleColumns } from '@/components/datatables/roles_column';
import DataTable from '@/components/datatables/DataTable.vue';
import { computed, h } from 'vue';
import { ref } from 'vue';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination/index"
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from "@/components/ui/alert-dialog/index"
import Input from '@/components/ui/input/Input.vue';
import { Filter, Plus } from 'lucide-vue-next';
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
import CreateRole from '@/components/pages/roles/CreateRole.vue';
import UpdateRole from '@/components/pages/roles/UpdateRole.vue';
import TagsCombobox from '@/components/TagsCombobox.vue';
import { Button } from '@/components/ui/button';
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
        permissions : [],
        level : [5]
    }
});
const roleTarget = ref<Role | null>(null);
const isEditOpen = ref(false);
const confirmDelete = ref(false);
const column = roleColumns({
    hasEditPermission : hasEditPermission,
    onEdit : onEdit,
    onDelete : onDelete
    });
const isCreateOpen = ref(false);

const handleUpdateSubmit = formUpdate.handleSubmit((values) => {
    router.put(`/roles/${values.id}`,{...values,level : values.level[0]},{
        onSuccess : () => {
            toast.success('Role updated successfully');
            fetchData();
            isEditOpen.value = false;
            roleTarget.value = null;
            formUpdate.resetForm();
        },
        onError : (errors) => {
          const messages = Object.values(errors).flat()
          toast.error("Error", {
              description: h(
                "div",
                { class: "space-y-1" }, // spacing between lines
                messages.map((msg) =>
                  h("p", { class: "text-red-500 text-sm" }, msg)
                )
              ),
           })
        }
    })
  })
function onEdit(role: Role) {
    roleTarget.value = role;
    formUpdate.setValues({
        id : roleTarget.value.id,
        name : roleTarget.value.name,
        permissions : roleTarget.value.permissions,
        level : [roleTarget.value.level]
    });
    isEditOpen.value = true;
}

function onDelete(role : Role) {
    roleTarget.value = role;
    confirmDelete.value = true;
  // e.g. router.delete(`/users/${id}`)
}
const handleDeleteSubmit = () =>{
    if(roleTarget.value){
        router.delete(`/roles/${roleTarget.value.id}`,{
            onSuccess : () => {
            toast.success('Role deleted successfully');
            router.reload({ only: ['roles'] });
                confirmDelete.value = false;
                roleTarget.value = null;
            },
            onError : (errors) => {
              const messages = Object.values(errors).flat()
              toast.error("Error", {
                  description: h(
                    "div",
                    { class: "space-y-1" }, // spacing between lines
                    messages.map((msg) =>
                      h("p", { class: "text-red-500 text-sm" }, msg)
                    )
                  ),
               })
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
</script>
<template>
    <Head title="Manage Roles" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="grid md:grid-cols-3 gap-4">
                <div class="my-2 flex items-center">
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
                <div class="m-2 flex justify-end items-center">
                    <Button
                        variant="outline"
                        size="icon"
                        class="bg-green-700 hover:bg-green-600"
                        @click="isCreateOpen = true"
                        >
                        <Plus class="text-white w-4 h-4"/>
                    </Button>
                </div>
            </div>
            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl overflow-x-auto">
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
        <AlertDialog v-if="confirmDelete" v-model:open="confirmDelete">
            <AlertDialogContent>
              <AlertDialogHeader>
                <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                <AlertDialogDescription>
                Please confirm <span class="text-red-400">delete</span> role with name <span class="text-black">{{roleTarget.name}}</span>
                </AlertDialogDescription>
              </AlertDialogHeader>
              <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction @click="handleDeleteSubmit">Continue</AlertDialogAction>
              </AlertDialogFooter>
            </AlertDialogContent>
          </AlertDialog>
        <CreateRole v-model:isFormOpen="isCreateOpen" :permission-options="permissions"/>
        <UpdateRole
            v-model="isEditOpen"
            :form-update="formUpdate"
            :permission-options="permissions"
            :on-submit="handleUpdateSubmit"
            />
    </AppLayout>
</template>
