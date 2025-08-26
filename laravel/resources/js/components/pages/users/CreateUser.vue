<script setup lang="ts">
import { useForm, configure } from 'vee-validate';
import { formCreateSchema} from '@/lib/users/users_schema';
import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import Input from '@/components/ui/input/Input.vue';
import {
  FormControl,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@/components/ui/form/index';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Eye,EyeOff } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { ref, watch } from 'vue';
const props = defineProps<{ isFormOpen: boolean }>();
const emit = defineEmits<{ (e: 'update:isFormOpen', value: boolean): void }>()
const open = ref(props.isFormOpen)
// sync prop -> local
watch(
  () => props.isFormOpen,
  (val) => { open.value = val }
)
watch(open, (val) => { emit('update:isFormOpen', val) })
configure({
  validateOnBlur: true, // controls if `blur` events should trigger validation with `handleChange` handler
  validateOnModelUpdate: true, // controls if `update:modelValue` events should trigger validation with `handleChange` handler
});
const formCreate = useForm({
  validationSchema: formCreateSchema,
  initialValues: {
    name: '',
    email: '',
    password : '',
    confirmPassword : ''
  },
});
const handleCreateSubmit = formCreate.handleSubmit((values) => {
    router.post(`/users`,values,{
        onSuccess : () => {
            toast.success('User created successfully');
            router.reload({ only: ['users'] });
            emit('update:isFormOpen', false);
            formCreate.resetForm();
        },
        onError : (e) => {
            toast.error('Update unexpected error');
        }
    })

  });
const showPassword = ref(false);
const showConfirm = ref(false);
</script>
<template>
        <Dialog v-model:open="open">
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Create User</DialogTitle>
              </DialogHeader>
              <form @submit.prevent="handleCreateSubmit">
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
                <DialogFooter>
                    <Button variant="outline" class="bg-green-700 hover:bf-green-600 text-white" type="submit">Save changes</Button>
                </DialogFooter>
              </form>

            </DialogContent>
          </Dialog>
</template>
