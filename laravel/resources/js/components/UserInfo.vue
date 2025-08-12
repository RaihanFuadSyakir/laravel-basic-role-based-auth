<script setup lang="ts">
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { useInitials } from '@/composables/useInitials';
import type { User } from '@/types';
import { computed } from 'vue';
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from "@/components/ui/popover"
interface Props {
    user: User;
    showEmail?: boolean;
    roles: string[];
    showRoles? : boolean;
}
import { Plus } from 'lucide-vue-next';
const props = withDefaults(defineProps<Props>(), {
    showEmail: false,
    showRoles: false,
});

const { getInitials } = useInitials();

// Compute whether we should show the avatar image
const showAvatar = computed(() => props.user.avatar && props.user.avatar !== '');
</script>

<template>
    <Avatar class="h-8 w-8 overflow-hidden rounded-lg">
        <AvatarImage v-if="showAvatar" :src="user.avatar!" :alt="user.name" />
        <AvatarFallback class="rounded-lg text-black dark:text-white">
            {{ getInitials(user.name) }}
        </AvatarFallback>
    </Avatar>

    <div class="grid flex-1 text-left text-sm leading-tight">
        <div class="truncate font-medium">{{ user.name }}</div>
        <div v-if="showRoles" class="flex truncate text-xs items-center">
            <span>{{ roles[0] }}</span>
            <span class="h-full p-1">
            <Popover>
                <PopoverTrigger as-child>
                    <Plus class="w-3 h-3"/>
                </PopoverTrigger>
                <PopoverContent align="center" side="right">
                    <h4 class="font-medium leading-none">
                       Roles
                    </h4>
                    <div class="h-[1px] bg-black my-2"></div>
                    <span v-for="(role,role_index) in roles" :key="role_index">{{role}}</span>
                </PopoverContent>
              </Popover>
            </span>
        </div>
        <div v-if="showEmail" class="truncate text-xs text-muted-foreground">{{ user.email }}</div>
    </div>
</template>
