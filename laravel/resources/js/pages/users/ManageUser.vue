<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { AppPageProps, type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { User } from '@/types';
import { userColumns } from '@/components/datatables/users_column';
import DataTable from '@/components/datatables/DataTable.vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Eye,EyeOff } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination/index"
import { toast } from 'vue-sonner';
import { Input } from '@/components/ui/input'
import { useForm, configure } from 'vee-validate';
import { UserPlus } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
import {formUpdateSchema } from './users_schema';
import CreateUser from './CreateUser.vue';
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
    router.post(`/update_users`,values,{
        onSuccess : () => {
            toast.success('User updated successfully');
            router.reload({ only: ['users'] });
            isEditOpen.value = false;
            formUpdate.resetForm();
        },
        onError : (e) => {
            toast.error('Update unexpected error');
        }
    })

  })
function editUser(user: User) {
    editUserTarget.value = user;
    formUpdate.setValues({
        id : editUserTarget.value.id,
        name : editUserTarget.value.name,
        email : editUserTarget.value.email,
    });
    isEditOpen.value = true;
}

function deleteUser(id: number) {
  console.log("Deleting user:", id)
  // e.g. router.delete(`/users/${id}`)
}
const page = usePage<AppPageProps & {
  users: User[],
  pagination: {
    total: number,
    currentPage: number,
    lastPage: number
  }
}>();
const pagination = computed(() => page.props.pagination);
const goToPage = (page: number) => {
  if (page < 1 || page > pagination.value.lastPage) return
  router.get('/manage_users', { page }, { preserveState: true, preserveScroll: true })
}
const users = computed(() => page.props.users);
const isCreateOpen = ref(false);
const isEditOpen = ref(false);
const showPassword = ref(false);
const showConfirm = ref(false);
const editUserTarget = ref<User|null>(null);
const columns = userColumns({ onEdit: editUser, onDelete: deleteUser });
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
            <div class="grid md:grid-cols-2">
                <div class="">a</div>
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
            </div>
            <div class="flex flex-1 flex-col gap-4 rounded-xl overflow-x-auto">
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
        <CreateUser v-model:isFormOpen="isCreateOpen" />
        <Dialog v-model:open="isEditOpen">
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Edit User</DialogTitle>
              </DialogHeader>
              <form @submit.prevent="handleUpdateSubmit">
                <FormField v-slot="{ componentField }" name="name">
                  <FormItem class="mb-2">
                    <FormLabel>Name</FormLabel>
                    <FormControl>
                      <Input type="text" placeholder="Name" v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="email">
                  <FormItem class="mb-2">
                    <FormLabel>Email</FormLabel>
                    <FormControl>
                      <Input type="email" placeholder="you@example.com" v-bind="componentField" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
                  <!-- Change Password Checkbox -->
                <FormField v-slot="{ componentField }" name="change_password" type="checkbox">
                  <FormItem class="flex items-center gap-2 mb-2">
                    <FormControl>
                      <input
                        type="checkbox"
                        class="mr-2"
                        :checked="componentField.value"
                        @change="componentField.onChange($event.target.checked)"
                      />
                    </FormControl>
                    <FormLabel>Change Password</FormLabel>
                  </FormItem>
                </FormField>
                <div v-if="formUpdate.values.change_password">
                    <!-- Password -->
                    <FormField v-slot="{ componentField }" name="password">
                      <FormItem class="mb-2">
                        <FormLabel>Password</FormLabel>
                        <FormControl>
                          <div class="relative">
                            <Input
                              :type="showPassword ? 'text' : 'password'"
                              placeholder="********"
                              v-bind="componentField"
                            />
                            <Button
                              variant="ghost"
                              class="absolute right-2 top-1/2 -translate-y-1/2 text-sm text-gray-500"
                              @click="showPassword = !showPassword"
                            >
                                <Eye v-if="showPassword" class="w-4 h-4" />
                                <EyeOff v-else class="w-4 h-4" />
                            </Button>
                          </div>
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    </FormField>

                    <!-- Confirm Password -->
                    <FormField v-slot="{ componentField }" name="confirmPassword">
                      <FormItem class="mb-2">
                        <FormLabel>Confirm Password</FormLabel>
                        <FormControl>
                          <div class="relative">
                            <Input
                              :type="showConfirm ? 'text' : 'password'"
                              placeholder="********"
                              v-bind="componentField"
                            />
                            <Button
                              variant="ghost"
                              class="absolute right-2 top-1/2 -translate-y-1/2 text-sm text-gray-500"
                              @click="showConfirm = !showConfirm"
                            >
                                <Eye v-if="showConfirm" class="w-4 h-4" />
                                <EyeOff v-else class="w-4 h-4" />
                            </Button>
                          </div>
                        </FormControl>
                        <FormMessage />
                      </FormItem>
                    </FormField>
                </div>
                <DialogFooter>
                    <Button variant="outline" class="bg-green-700 hover:bf-green-600 text-white" type="submit">Save changes</Button>
                </DialogFooter>
              </form>

            </DialogContent>
          </Dialog>
    </AppLayout>
</template>
