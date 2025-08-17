
import { h } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';
import { User } from '@/types';
import { Button } from '@/components/ui/button';

export const userColumns = ({ onEdit, onDelete }:
    { onEdit: (user: User) => void;
      onDelete: (id: number) => void
    }): ColumnDef<User>[] => [
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
  {
    id: "actions", // no accessorKey since it's not from data
    header: () => h("div", { class: "font-medium" }, "Actions"),
    cell: ({ row }) => {
      const user = row.original as User

      return h("div", { class: "gap-2" }, [
        h(
          Button,
          {
            variant: "outline",
            size: "sm",
            onClick: () => onEdit(user),
          },
          { default: () => "Edit" }
        ),
        h(
          Button,
          {
            variant: "destructive",
            size: "sm",
            onClick: () => onDelete(user.id),
          },
          { default: () => "Delete" }
        ),
      ])
    },
  },
];
