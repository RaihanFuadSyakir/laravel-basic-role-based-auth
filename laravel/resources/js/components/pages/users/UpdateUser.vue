
<script setup lang="ts">
import { computed, ref } from "vue"
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
import { Eye, EyeOff } from "lucide-vue-next"
import { FormContext } from "vee-validate"

interface Props {
  modelValue: boolean
  formUpdate: FormContext<Record<string, any>>
  onSubmit: () => void
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void
}>()
const showPassword = ref(false)
const showConfirm = ref(false)
const isOpen = computed({
  get: () => props.modelValue,
  set: (val: boolean) => emit("update:modelValue", val),
})
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Edit User</DialogTitle>
      </DialogHeader>

      <form @submit.prevent="props.onSubmit">
        <!-- Name -->
        <FormField v-slot="{ componentField }" name="name">
          <FormItem class="mb-2">
            <FormLabel>Name</FormLabel>
            <FormControl>
              <Input type="text" placeholder="Name" v-bind="componentField" />
            </FormControl>
            <FormMessage />
          </FormItem>
        </FormField>

        <!-- Email -->
        <FormField v-slot="{ componentField }" name="email">
          <FormItem class="mb-2">
            <FormLabel>Email</FormLabel>
            <FormControl>
              <Input
                type="email"
                placeholder="you@example.com"
                v-bind="componentField"
              />
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

        <!-- Password & Confirm -->
        <div v-if="props.formUpdate.values.change_password">
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
                    type="button"
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
                    type="button"
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
