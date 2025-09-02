
<script setup lang="ts">
import { computed} from "vue"
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogFooter,
} from "@/components/ui/dialog"
import {
  FormField,
  FormItem,
  FormLabel,
  FormControl,
  FormMessage,
} from "@/components/ui/form"
import { Input } from "@/components/ui/input"
import { Button } from "@/components/ui/button"
import { FormContext } from "vee-validate"
import TagsCombobox from "@/components/TagsCombobox.vue"
import Slider from "@/components/ui/slider/Slider.vue"
import FormDescription from "@/components/ui/form/FormDescription.vue"

interface Props {
  modelValue: boolean
  formUpdate: FormContext<Record<string, any>>
  onSubmit: () => void,
  permissionOptions : string[]
}
const props = defineProps<Props>()
const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void
}>()
const isOpen = computed({
  get: () => props.modelValue,
  set: (val: boolean) => emit("update:modelValue", val),
})
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Edit Role</DialogTitle>
      </DialogHeader>
      <form @submit.prevent="props.onSubmit">
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
                v-bind="componentField"
                />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <FormField v-slot="{ componentField,value }" name="level">
          <FormItem class="mb-2">
          <FormLabel>Role's Level : {{value[0]}}</FormLabel>
            <FormDescription>
              Choose a role level from <b>1</b> (highest priority) to <b>10</b> (lowest priority).
            </FormDescription>
            <FormControl>
            <Slider
                :default-value="props.formUpdate.values.level"
                :min="1"
                :max="10"
                :step="1"
                :name="componentField.name"
                @update:model-value="componentField['onUpdate:modelValue']"
                />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>
        <!-- Footer -->
        <DialogFooter>
          <Button
            variant="outline"
            class="bg-green-700 hover:bg-green-600 text-white"
            type="submit"
          >
            Save changes
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
