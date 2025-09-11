<script setup lang="ts">
import { useForm, configure } from 'vee-validate';
import { formCreateSchema} from '@/lib/roles/roles_schema';
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
import TagsCombobox from '@/components/TagsCombobox.vue';
import { toTypedSchema } from '@vee-validate/zod';
import Slider from '@/components/ui/slider/Slider.vue';
import FormDescription from '@/components/ui/form/FormDescription.vue';
import { h } from 'vue';
const props = defineProps<{ isFormOpen: boolean, permissionOptions : string[] }>();
const emit = defineEmits<{ (e: 'update:isFormOpen', value: boolean): void }>()
const open = ref(props.isFormOpen)
// sync prop -> local
watch(
  () => props.isFormOpen,
  (val) => {
    open.value = val
    if(val){
      formCreate.resetForm();
      formCreate.setFieldValue("level",[5]);
    }
   }
)
watch(open, (val) => {
    emit('update:isFormOpen', val);
    formCreate.setFieldValue("level",[5]);
})
configure({
  validateOnBlur: true, // controls if `blur` events should trigger validation with `handleChange` handler
  validateOnModelUpdate: true, // controls if `update:modelValue` events should trigger validation with `handleChange` handler
});
const formCreate = useForm({
  validationSchema: toTypedSchema(formCreateSchema),
  initialValues: {
    name: '',
    permissions : [],
    level : [5]
  },
});
const handleCreateSubmit = formCreate.handleSubmit((values) => {
    router.post(`/roles`,{...values,level : values.level[0]},{
        onSuccess : () => {
            toast.success('Role created successfully');
            router.reload({ only: ['roles'] });
            emit('update:isFormOpen', false);
            formCreate.resetForm();
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
  });
const permissionsSelected = ref<string[]>([]);
watch(permissionsSelected, (newVal) => {
    formCreate.setFieldValue("permissions",newVal);
})
</script>
<template>
        <Dialog v-model:open="open">
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Create Role</DialogTitle>
              </DialogHeader>
              <form @submit.prevent="handleCreateSubmit">
                <FormField v-slot="{ componentField }" name="name">
                  <FormItem class="mb-2">
                    <FormLabel>Role's Name</FormLabel>
                    <FormControl>
                      <Input type="text" placeholder="Name (ex : Staff)" v-bind="componentField" :name="componentField.name"/>
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
                <FormField v-slot="{ componentField }" name="permissions">
                  <FormItem class="mb-2">
                    <FormLabel>Role's Permissions</FormLabel>
                    <FormControl>
                    <TagsCombobox
                        :options="props.permissionOptions"
                        variant="search"
                        placeholder="List Permissions"
                        v-model="permissionsSelected"
                        />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
                <FormField v-slot="{ componentField, value }" name="level">
                  <FormItem class="mb-2">
                  <FormLabel>Role's Level : {{Array.isArray(value) ? value[0] : value}}</FormLabel>
                    <FormDescription>
                      Choose a role level from <b>1</b> (highest priority) to <b>10</b> (lowest priority).
                    </FormDescription>
                    <FormControl>
                    <Slider
                        :default-value="[5]"
                        :min="1"
                        :max="10"
                        :step="1"
                        @update:model-value="componentField['onUpdate:modelValue']"
                        />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
                <DialogFooter>
                    <Button variant="outline" class="bg-green-700 hover:bf-green-600 text-white" type="submit">Save</Button>
                </DialogFooter>
              </form>
            </DialogContent>
          </Dialog>
</template>
