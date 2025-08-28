
import { h } from 'vue';
import type { ColumnDef } from '@tanstack/vue-table';
import { Pencil, Eye, ArrowUpDown } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import Button from '@/components/ui/button/Button.vue';
export const permissionColumns: ColumnDef<string>[] = [
    {
      accessorKey: "name",
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
      cell: ({ row }) => {
        const name = row.getValue("name") as string
        const [action] = name.split("_")

        if (action === "view") {
          return h(
            "div",
            {class : "flex justify-center gap-2"},
            h(
                Badge,
                { class: "flex justify-center gap-2 rounded-full bg-blue-500" },
                [
                  h(Eye, { class: "w-4 h-4 text-white" }),
                  h("span", {}, name),
                ]
            )
          )
        }

        if (action === "edit") {
          return h(
            "div", {class : "flex justify-center gap-2"},
            h(
                Badge,
                { class: "flex justify-center rounded-full bg-green-600" },
                [
                  h(Pencil, { class: "w-4 h-4 text-white" }),
                  h("span", {}, name),
                ]
            )
          )
        }

        return h("div", {}, name)
      },
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
