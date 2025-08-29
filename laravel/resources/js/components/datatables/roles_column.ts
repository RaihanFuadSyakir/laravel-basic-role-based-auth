
import { h } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';
import { Role} from '@/types';
import { Button } from '@/components/ui/button';
import { ArrowUpDown, Pencil, Trash2 } from 'lucide-vue-next';

export const roleColumns = ({hasEditPermission, onEdit, onDelete}:
    {
        hasEditPermission : Boolean;
        onEdit : (role : Role) => void;
        onDelete : (role : Role) => void;
    }) : ColumnDef<Role>[] => [
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
    accessorKey : 'permissions',
    header: ({column}) => h('div', { class: 'font-medium' }, [
        h('span','Total Permissions'),
        h(Button, {
            variant: 'ghost',
            class: 'ml-2',
            onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
        }, () => h(ArrowUpDown, { class: 'h-4 w-4' }))
    ]),
    cell: ({ row }) => {
    const permissions = row.getValue('permissions') as string[] | null | undefined;
    return h('div', {}, permissions?.length);
    },
  },
  ...(hasEditPermission ? [{
    id: "actions", // no accessorKey since it's not from data
    header: () => h("div", { class: "font-medium" }, "Actions"),
    cell: ({ row }) => {
      const role = row.original as Role

      return h("div", { class: "gap-2" }, [
        h(
          Button,
          {
            variant: "outline",
            size: "sm",
            class : "bg-blue-500 hover:bg-blue-400",
            onClick: () => onEdit(role),
          },
            { default: () => h(Pencil, { class: "text-white w-4 h-4" }) }
        ),
        h(
          Button,
          {
            variant: "destructive",
            size: "sm",
            class : "bg-red-500 hover:bg-red-400",
            onClick: () => onDelete(role),
          },
            { default: () => h(Trash2, { class: "w-4 h-4" }) }
        ),
      ])
    },
  }] : []),
];
