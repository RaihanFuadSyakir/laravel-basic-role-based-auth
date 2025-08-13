
import { h } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';
import { User } from '@/types';

export const userColumns: ColumnDef<User>[] = [
  {
    accessorKey: 'name',
    header: () => h('div', { class: 'font-medium' }, 'Name'),
    cell: ({ row }) => h('div', {}, row.getValue('name')),
  },
  {
    accessorKey: 'email',
    header: () => h('div', { class: 'font-medium' }, 'Email'),
    cell: ({ row }) => h('div', {}, row.getValue('email')),
  },
  {
    accessorKey: 'roles',
    header: () => h('div', { class: 'font-medium' }, 'Roles'),
    cell: ({ row }) => {
    const roles = row.getValue('roles') as string[] | null | undefined;

    // If roles is nullish or an empty array, show fallback.
    if (!roles || roles.length === 0) {
      return h('div', { class: 'text-sm text-muted-foreground' }, '—');
    }

    return h('div', {}, roles.join(', '));
    },
  },
];
