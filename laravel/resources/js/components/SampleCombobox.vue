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

// --- Props ---
const props = defineProps<{
  options: string[]
  modelValue: string[]
}>()

// --- Emits ---
const emit = defineEmits<{
  (e: "update:modelValue", value: string[]): void
}>()

// --- Local state ---
const open = ref(false)
const searchTerm = ref("")
const selected = ref<string[]>([...props.modelValue]) // local copy

// Sync prop → local when parent changes
watch(
  () => props.modelValue,
  (val) => {
    selected.value = [...val]
  }
)

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
    selected.value.push(val)
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
      <TagsInput v-model="selected" class="px-2 gap-2 w-80">
        <div class="flex gap-2 flex-wrap items-center">
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

        <ComboboxInput v-model="searchTerm" as-child>
          <TagsInputInput
            placeholder="Select..."
            class="min-w-[200px] w-full p-0 border-none focus-visible:ring-0 h-auto"
            @keydown.enter.prevent
          />
        </ComboboxInput>
      </TagsInput>

      <ComboboxList class="w-[--reka-popper-anchor-width]">
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
