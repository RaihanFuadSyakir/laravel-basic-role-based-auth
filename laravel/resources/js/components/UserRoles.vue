<script setup lang="ts">
import { Plus } from "lucide-vue-next"
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"
defineProps<{
  roles: string[] // required, always array
}>();
</script>

<template>
  <!-- If no roles -->
  <div v-if="!roles || roles.length === 0" class="text-sm text-muted-foreground">—</div>

  <!-- If roles exist -->
  <div v-else class="truncate text-xs">
    <span>{{ roles[0] }}</span>

    <!-- Show popover only if more than 1 role -->
    <span v-if="roles.length > 1" class="h-full p-1">
      <Popover>
        <PopoverTrigger as-child>
          <Plus class="w-3 h-3 cursor-pointer" />
        </PopoverTrigger>
        <PopoverContent align="center" side="right" class="p-2">
          <h4 class="font-medium leading-none">Roles</h4>
          <div class="h-[1px] bg-black my-2"></div>
          <div class="flex flex-col gap-1 text-sm">
            <span v-for="(role, role_index) in roles" :key="role_index">
              {{ role }}
            </span>
          </div>
        </PopoverContent>
      </Popover>
    </span>
  </div>
</template>
