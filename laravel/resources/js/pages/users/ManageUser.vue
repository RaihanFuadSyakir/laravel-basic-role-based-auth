<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { AppPageProps, type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { User } from '@/types';
import { userColumns } from '@/components/datatables/users_column';
import DataTable from '@/components/datatables/DataTable.vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
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
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog/index"
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from "@/components/ui/tags-input"
import { toast } from 'vue-sonner';
import { useForm, configure } from 'vee-validate';
import { UserPlus } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import {formUpdateSchema } from '@/lib/users/users_schema';
import { Input } from '@/components/ui/input';
import CreateUser from '@/components/pages/users/CreateUser.vue';
import UpdateUser from '@/components/pages/users/UpdateUser.vue';
import TagsCombobox from '@/components/TagsCombobox.vue';
configure({
  validateOnBlur: true, // controls if `blur` events should trigger validation with `handleChange` handler
  validateOnModelUpdate: true, // controls if `update:modelValue` events should trigger validation with `handleChange` handler
});
const formUpdate = useForm({
  validationSchema: formUpdateSchema,
  initialValues: {
    id : 0,
    name: '',
    email: '',
    change_password : false,
    password : '',
    confirmPassword : ''
  },
});
const handleUpdateSubmit = formUpdate.handleSubmit((values) => {
    router.put(`/users/${values.id}`,values,{
        onSuccess : () => {
            toast.success('User updated successfully');
            router.reload({ only: ['users'] });
            isEditOpen.value = false;
            userTarget.value = null;
            formUpdate.resetForm();
        },
        onError : () => {
            toast.error('Update unexpected error');
        }
    })
  })
function editUser(user: User) {
    userTarget.value = user;
    formUpdate.setValues({
        id : userTarget.value.id,
        name : userTarget.value.name,
        email : userTarget.value.email,
    });
    isEditOpen.value = true;
}

function deleteUser(user: User) {
    userTarget.value = user;
    confirmDelete.value = true;
  // e.g. router.delete(`/users/${id}`)
}
const handleDeleteSubmit = () =>{
    if(userTarget.value){
        router.delete(`/users/${userTarget.value.id}`,{
            onSuccess : () => {
                toast.success('User deleted successfully');
                router.reload({ only: ['users'] });
                confirmDelete.value = false;
                userTarget.value = null;
            },
            onError : () => {
                toast.error('Update unexpected error');
            }
        });
    }
}
const page = usePage<AppPageProps & {
  users: User[],
  roles : Array<string>,
  pagination: {
    total: number,
    currentPage: number,
    lastPage: number
  },
  permissions : string[]
}>();
const pagination = computed(() => page.props.pagination);
const users = computed(() => page.props.users);
const rolesFilter = ref<string[]>([]);
const roleOptions = page.props.roles.map(val => ({label : val, value : val}));
const filters = ref<Record<string, string | string[]>>({});
const hasEditPermission = page.props.auth.permissions.some(
  (perm: string) => perm === "edit_users"
)
const goToPage = (page: number) => {
  if (page < 1 || page > pagination.value.lastPage) return
  router.get('/manage_users', { page : pagination.value.currentPage, ...filters.value }, { preserveState: true, preserveScroll: true })
}
function addFilter(key : string,value : string | Array<string>){
    console.log(key);
    console.log(value);
   filters.value = {...filters.value,[key] : value};
   console.log(filters.value);
   router.get('/manage_users', { page : pagination.value.currentPage, ...filters.value }, { preserveState: true, preserveScroll: true })
}

const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const confirmDelete = ref(false);
const userTarget = ref<User|null>(null);
const columns = userColumns({
    hasEditPermission : hasEditPermission,
    onEdit: editUser,
    onDelete: deleteUser
});
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manage Users',
        href: '/manage_users',
    },
];

</script>
<template>
    <Head title="Manage Users" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="px-2 flex justify-end">
                <Button
                    variant="outline"
                    size="icon"
                    class="bg-green-700 hover:bg-green-600"
                    @click="isCreateOpen = true"
                    >
                    <UserPlus class="text-white w-4 h-4"/>
                </Button>
            </div>
            <div class="grid md:grid-cols-4">
                <div class="flex justify-center">
                    <Input type="text" class="m-2" placeholder="Filter By Name" @input="(e : any) => addFilter('name',e.target.value)"/>
                </div>
                <div class="flex justify-center">
                    <Input type="text" class="m-2" placeholder="Filter By Email" @input="(e : any) => addFilter('email',e.target.value)"/>
                </div>
                <div class="flex justify-center">
                    <!-- <TagsCombobox v-model="rolesFilter" @update:modelValue="(val) => console.log(rolesFilter)" :data="roleOptions" placeholder="Filter By Roles"/> -->
                </div>
            </div>
            <div class="flex flex-1 flex-col gap-4 rounded-xl overflow-x-auto mb-2">
                <DataTable :columns="columns" :data="users" />
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
        <AlertDialog v-model:open="confirmDelete">
            <AlertDialogContent>
              <AlertDialogHeader>
                <AlertDialogTitle>Are you absolutely sure?</AlertDialogTitle>
                <AlertDialogDescription>
                Please confirm <span class="text-red-400">delete</span> user with name <span class="text-black">{{userTarget.name}}</span>
                </AlertDialogDescription>
              </AlertDialogHeader>
              <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction @click="handleDeleteSubmit">Continue</AlertDialogAction>
              </AlertDialogFooter>
            </AlertDialogContent>
          </AlertDialog>
        <CreateUser v-model:isFormOpen="isCreateOpen" />
        <UpdateUser v-model="isEditOpen" :form-update="formUpdate" :on-submit="handleUpdateSubmit"/>
    </AppLayout>
</template>
