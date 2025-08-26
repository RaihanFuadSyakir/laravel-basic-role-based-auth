
<script setup lang="ts">
import { ref, computed, watch } from "vue"
import { Combobox, ComboboxAnchor, ComboboxEmpty, ComboboxGroup, ComboboxInput, ComboboxItem, ComboboxList } from "@/components/ui/combobox"
import { TagsInput, TagsInputInput, TagsInputItem, TagsInputItemDelete, TagsInputItemText } from "@/components/ui/tags-input"

interface Option {
  label: string
  value: string
}

const props = defineProps<{
  data: Option[]
  placeholder?: string
  modelValue?: string[]
}>()

const emit = defineEmits<{
  (e: "update:modelValue", value: string[]): void
}>()

const selected = ref<string[]>([]);
const open = ref(false)
const searchTerm = ref("")

const filteredOptions = computed(() => {
  const options = props.data.filter(i => !selected.value.includes(i.label))
  return searchTerm.value
    ? options.filter(option => option.label.toLowerCase().includes(searchTerm.value.toLowerCase()))
    : options
})

// watch selected and emit update
watch(selected, (val) => {
  emit("update:modelValue", val)
})
</script>

<template>
  <Combobox v-model="selected" v-model:open="open" :ignore-filter="true">
    <ComboboxAnchor as-child>
      <TagsInput v-model="selected" class="px-2 gap-2 w-full">
        <div class="flex gap-2 flex-wrap items-center">
          <TagsInputItem v-for="item in selected" :key="item" :value="item">
            <TagsInputItemText />
            <TagsInputItemDelete />
          </TagsInputItem>
        </div>

        <ComboboxInput v-model="searchTerm" as-child>
          <TagsInputInput
            :placeholder="props.placeholder ?? 'Select...'"
            class="min-w-[200px] w-full p-0 border-none focus-visible:ring-0 h-auto"
            @keydown.enter.prevent
          />
        </ComboboxInput>
      </TagsInput>

      <ComboboxList class="w-[--reka-popper-anchor-width]">
        <ComboboxEmpty>No results found</ComboboxEmpty>
        <ComboboxGroup>
          <ComboboxItem
            v-for="option in filteredOptions"
            :key="option.value"
            :value="option.label"
            @select.prevent="(ev : any) => {
              if (typeof ev.detail.value === 'string') {
                searchTerm = '';
                selected = [...selected,ev.detail.value];
                emit('update:modelValue', selected);
              }
              if (filteredOptions.length === 0) open.value = false
            }"
          >
            {{ option.label }}
          </ComboboxItem>
        </ComboboxGroup>
      </ComboboxList>
    </ComboboxAnchor>
  </Combobox>
</template>
