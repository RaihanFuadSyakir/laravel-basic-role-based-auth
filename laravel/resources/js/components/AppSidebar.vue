<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type NavGroup } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, LockKeyhole,UserCheck, Users} from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavGroup[] = [
    {
        title : 'Assets',
        childs : [
            {
                title: 'Dashboard',
                href: '/dashboard',
                icon: LayoutGrid,
            },
            ],
        },
    {
        title : 'Manage',
        childs : [
            {
                title: 'Manage Users',
                href: '/manage_users',
                icon: Users,
                permission : 'view_users'
            },
            {
                title: 'Manage Roles',
                href: '/manage_roles',
                icon: UserCheck,
                permission : 'view_roles'
            },
            {
                title: 'Manage Permissions',
                href: '/manage_permissions',
                icon: LockKeyhole,
                permission : 'view_permissions'
            },
        ]
    }
];
const page = usePage();
const permissions = page.props.auth.permissions;
const filteredNavItems = mainNavItems
  .map(group => {
    const visibleChildren = group.childs?.filter(
      child =>
        !child.permission || permissions.includes(child.permission)
    )

    return {
      ...group,
      childs: visibleChildren,
    }
  })
  .filter(group => (group.childs?.length ?? 0) > 0);
const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem size="lg" as-child>
                    <NavUser/>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="filteredNavItems" />
        </SidebarContent>

        <!-- <SidebarFooter> -->
        <!-- <NavFooter :items="footerNavItems" /> -->
        <!-- </SidebarFooter> -->
    </Sidebar>
    <slot />
</template>
