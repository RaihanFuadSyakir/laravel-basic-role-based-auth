<script setup lang="ts">
import {
    Sidebar,
    SidebarHeader,
    SidebarContent,
    SidebarGroup,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem
    } from '@/components/ui/sidebar';
import { type NavItem, type NavGroup} from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import Collapsible from './ui/collapsible/Collapsible.vue';
import CollapsibleTrigger from './ui/collapsible/CollapsibleTrigger.vue';
import CollapsibleContent from './ui/collapsible/CollapsibleContent.vue';
defineProps<{
    items: NavGroup[];
}>();

const page = usePage();
</script>

<template>
    <Collapsible defaultOpen v-for="item in items" :key="item.title">
        <SidebarGroup class="px-2 py-0">
            <SidebarGroupLabel>
                <CollapsibleTrigger>{{item.title}}</CollapsibleTrigger>
            </SidebarGroupLabel>
            <CollapsibleContent>
                <SidebarMenu>
                    <SidebarMenuItem v-for="child in item.childs" :key="child.title">
                        <SidebarMenuButton as-child :is-active="child.href === page.url" :tooltip="child.title">
                            <Link :href="child.href">
                                <component :is="child.icon" />
                                <span>{{ child.title }}</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </CollapsibleContent>
        </SidebarGroup>
    </Collapsible>
</template>
