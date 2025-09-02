<script setup lang="ts">
import { Expand} from "lucide-vue-next"
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"
defineProps<{
  title : string,
  data: string[]
}>();
</script>

<template>
  <div class="truncate text-xs flex justify-center items-center">
    <span>{{ title }}</span>
    <span class="p-2">
      <Popover>
        <PopoverTrigger as-child>
          <Expand class="w-3 h-3 cursor-pointer text-gray-400 hover:text-gray-700" />
        </PopoverTrigger>
        <PopoverContent align="center" side="right" class="p-2">
          <h4 class="font-medium leading-none">Roles</h4>
          <div class="h-[1px] bg-black my-2"></div>
          <div class="flex flex-col gap-1 text-sm">
            <span v-for="(value, index) in data" :key="index">
              {{ value }}
            </span>
          </div>
        </PopoverContent>
      </Popover>
    </span>
  </div>
</template>
