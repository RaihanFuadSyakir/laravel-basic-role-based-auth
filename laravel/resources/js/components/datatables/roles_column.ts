
import { h } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';
import { Role} from '@/types';

export const roleColumns: ColumnDef<Role>[] = [
  {
    accessorKey: 'name',
    header: () => h('div', { class: 'font-medium' }, 'Name'),
    cell: ({ row }) => h('div', {}, row.getValue('name')),
  },
  {
    accessorKey: 'permissions',
    header: () => h('div', { class: 'font-medium' }, 'Permissions'),
    cell: ({ row }) => {
    const permissions = row.getValue('permissions') as string[] | null | undefined;

    // If permissions is nullish or an empty array, show fallback.
    if (!permissions || permissions.length === 0) {
      return h('div', { class: 'text-sm text-muted-foreground' }, '—');
    }

    return h('div', {}, permissions.join(', '));
    },
  },
];
