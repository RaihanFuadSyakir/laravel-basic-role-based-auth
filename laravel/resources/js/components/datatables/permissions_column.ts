
import { h } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';

export const permissionColumns: ColumnDef<string>[] = [
  {
    accessorKey: 'name',
    header: () => h('div', { class: 'font-medium' }, 'Name'),
    cell: ({ row }) => h('div', {}, row.getValue('name')),
  },
  {
    accessorKey: 'created_at',
    header: () => h('div', { class: 'font-medium' }, 'Created At'),
    cell: ({ row }) => {
      const raw = row.getValue('created_at') as string;
      const date = new Date(raw);
      return h('div', {}, date.toLocaleString());
    },
  },
];
