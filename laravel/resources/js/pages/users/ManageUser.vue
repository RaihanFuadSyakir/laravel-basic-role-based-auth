<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { AppPageProps, type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/vue3';
import { User } from '@/types';
import { userColumns } from '@/components/datatables/users_column';
import DataTable from '@/components/datatables/DataTable.vue';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form/index';
import { toast } from 'vue-sonner';
import { Input } from '@/components/ui/input'
import { toTypedSchema } from '@vee-validate/zod';
import * as z from 'zod';
import { useForm, configure } from 'vee-validate';
import { UserPlus } from 'lucide-vue-next';
import { router } from '@inertiajs/vue3';
const formSchema = toTypedSchema(
  z.object({
    id : z.number(),
    name: z
      .string()
      .min(2, { message: 'Name must be at least 2 characters' })
      .max(50, { message: 'Name must be less than 50 characters' }),
    email: z
      .string()
      .email({ message: 'Please enter a valid email address' }),
  })
)
configure({
  validateOnBlur: true, // controls if `blur` events should trigger validation with `handleChange` handler
  validateOnModelUpdate: true, // controls if `update:modelValue` events should trigger validation with `handleChange` handler
});
const form = useForm({
  validationSchema: formSchema,
  initialValues: {
    id : 0,
    name: '',
    email: '',
  },
})
const handleSubmit = (mode: 'create' | 'update', e: Event) => {
  form.handleSubmit((values) => {
    router.post(`/update_users`,values,{
        onSuccess : () => {
            toast.success('User updated successfully');
            router.reload({ only: ['users'] });
            isEditOpen.value = false;
        },
        onError : (e) => {
            console.log(e);
        }
    })

  })(e)
}
function editUser(user: User) {
    editUserTarget.value = user;
    form.setValues({
        id : editUserTarget.value.id,
        name : editUserTarget.value.name,
        email : editUserTarget.value.email
    });
    isEditOpen.value = true;
}

function deleteUser(id: number) {
  console.log("Deleting user:", id)
  // e.g. router.delete(`/users/${id}`)
}
const page = usePage<AppPageProps & { users: User[] }>()
const users = computed(() => page.props.users)
const isEditOpen = ref(false);
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
                    <Button variant="outline" size="icon" class="bg-green-700 hover:bg-green-600">
                        <UserPlus class="text-white w-4 h-4"/>
                    </Button>
                </div>
            </div>
            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl overflow-x-auto">
                <DataTable :columns="columns" :data="users" />
            </div>
        </div>

        <Dialog v-model:open="isEditOpen">
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Edit User</DialogTitle>
              </DialogHeader>
              <form @submit.prevent="(e : Event) => handleSubmit('update', e)">
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
                <DialogFooter>
                    <Button variant="outline" class="bg-green-700 hover:bf-green-600 text-white" type="submit">Save changes</Button>
                </DialogFooter>
              </form>

            </DialogContent>
          </Dialog>
    </AppLayout>
</template>
