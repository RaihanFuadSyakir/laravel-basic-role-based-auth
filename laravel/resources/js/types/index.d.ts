import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
    roles : string[];
    permissions : string[];
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavGroup {
    title: string;
    icon?: LucideIcon;
    isActive?: boolean;
    childs? : NavItem[];
}
export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<T extends Record<string, unknown> = Record<string, unknown>> = T & {
    auth: Auth;
    sidebarOpen: boolean;
};

export interface Role {
    name : string;
    permissions : string[];
};
export interface User {
    id: number;
    name: string;
    email: string;
    roles? : Role[];
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;
