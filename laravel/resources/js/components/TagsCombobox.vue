<script setup lang="ts">
import { useFilter } from "reka-ui"
import { computed, ref, watch } from "vue"
import {
  Combobox,
  ComboboxAnchor,
  ComboboxEmpty,
  ComboboxGroup,
  ComboboxInput,
  ComboboxItem,
  ComboboxList,
} from "@/components/ui/combobox"
import {
  TagsInput,
  TagsInputInput,
  TagsInputItem,
  TagsInputItemDelete,
  TagsInputItemText,
} from "@/components/ui/tags-input"
import { Filter } from "lucide-vue-next"
// --- Props ---
const props = defineProps<{
  options: string[]
  modelValue: string[]
  placeholder : string
}>()

// --- Emits ---
const emit = defineEmits<{
  (e: "update:modelValue", value: string[]): void
}>()

// --- Local state ---
const open = ref(false)
const searchTerm = ref("")
const selected = ref<string[]>([...props.modelValue]) // local copy

// Sync local → parent when local changes
watch(selected, (val) => {
  emit("update:modelValue", val)
})

// --- Filtered list ---
const { contains } = useFilter({ sensitivity: "base" })
const filteredOptions = computed(() => {
  const options = props.options.filter(opt => !selected.value.includes(opt))
  return searchTerm.value
    ? options.filter(opt => contains(opt, searchTerm.value))
    : options
})

// --- Methods ---
function addValue(val: string) {
  if (!selected.value.includes(val)) {
    selected.value = [...selected.value,val];
  }
  searchTerm.value = ""
  if (filteredOptions.value.length === 0) {
    open.value = false
  }
}
function removeValue(val: string) {
  selected.value = selected.value.filter(item => item !== val)
}
</script>

<template>
  <Combobox v-model="selected" v-model:open="open" :ignore-filter="true">
    <ComboboxAnchor as-child>
      <TagsInput v-model="selected" class="px-2 gap-2 w-full py-0">
      <div v-if="selected.length > 0" class="flex gap-2 mt-2 flex-nowrap items-center">
          <TagsInputItem
            v-for="item in selected"
            :key="item"
            :value="item"
            @delete="removeValue(item)"
          >
            <TagsInputItemText />
            <TagsInputItemDelete />
          </TagsInputItem>
        </div>

        <ComboboxInput v-model="searchTerm" :icon="Filter" as-child>
          <TagsInputInput
            :placeholder="props.placeholder"
            class="w-full p-0 border-none focus-visible:ring-0 h-auto"
            @keydown.enter.prevent
          />
        </ComboboxInput>
      </TagsInput>

      <ComboboxList class="w-full max-w-md">
        <ComboboxEmpty>No results</ComboboxEmpty>
        <ComboboxGroup>
          <ComboboxItem
            v-for="opt in filteredOptions"
            :key="opt"
            :value="opt"
            @select.prevent="addValue(opt)"
          >
            {{ opt }}
          </ComboboxItem>
        </ComboboxGroup>
      </ComboboxList>
    </ComboboxAnchor>
  </Combobox>
</template>
