
import { h } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';
import { User } from '@/types';
import { Button } from '@/components/ui/button';
import { Pencil, Trash2, ArrowUpDown, ChevronDown } from "lucide-vue-next";
import ExpandPopover from '@/components/ExpandPopover.vue';
export const userColumns = ({hasEditPermission , onEdit, onDelete }:
    {
      hasEditPermission : Boolean;
      onEdit: (user: User) => void;
      onDelete: (user: User) => void;
    }): ColumnDef<User>[] => [
  {
    accessorKey: 'name',
    header: ({ column }) => {
        return h('div', { class: 'flex justify-center items-center' }, [
          h('span', 'Name'),
          h(Button, {
            variant: 'ghost',
            class: 'ml-2',
            onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
          }, () => h(ArrowUpDown, { class: 'h-4 w-4' }))
        ])
    },
    cell: ({ row }) => h('div', {}, row.getValue('name')),
  },
  {
    accessorKey: 'email',
    header: ({ column }) => {
        return h(Button, {
            variant: 'ghost',
            onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
        }, () => ['Email', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('email')),
  },
  {
    accessorKey: 'roles',
    header: ({ column }) => {
        return h(Button, {
            variant: 'ghost',
            onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
        }, () => ['Roles', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })])
    },
    cell: ({ row }) => {
    const roles = row.getValue('roles') as string[] | null | undefined;
    return h('div', {},
            h(ExpandPopover,{title : roles?.length.toString() ?? "",data: roles ?? []})
    );
    },
  },
  ...(hasEditPermission ? [{
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
            class : "bg-blue-500 hover:bg-blue-400",
            onClick: () => onEdit(user),
          },
            { default: () => h(Pencil, { class: "text-white w-4 h-4" }) }
        ),
        h(
          Button,
          {
            variant: "destructive",
            size: "sm",
            class : "bg-red-500 hover:bg-red-400",
            onClick: () => onDelete(user),
          },
            { default: () => h(Trash2, { class: "w-4 h-4" }) }
        ),
      ])
    },
  }] : []),
];
