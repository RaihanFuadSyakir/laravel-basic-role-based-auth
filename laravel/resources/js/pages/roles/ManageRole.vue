<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { AppPageProps, Role, type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { roleColumns } from '@/components/datatables/roles_column';
import DataTable from '@/components/datatables/DataTable.vue';
import { computed } from 'vue';
import { ref } from 'vue';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination/index"
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manage Roles',
        href: '/manage_roles',
    },
];
const page = usePage< AppPageProps & {
 data : {
  roles : Role[]
 },
  pagination: {
    total: number,
    currentPage: number,
    lastPage: number
  },
}>();

const pagination = computed(() => page.props.pagination);
const roles = computed(() => page.props.data.roles);
const hasEditPermission = page.props.auth.permissions.some(
  (perm: string) => perm === "edit_roles"
)
function onEdit(role : Role){
}
function onDelete(role : Role){
}
const filters = ref<Record<string, string | string[]>>({});
function fetchData(){
  router.get('/manage_roles', { page : pagination.value.currentPage, ...filters.value }, { preserveState: true, preserveScroll: true })
}
const goToPage = (page: number) => {
  if (page < 1 || page > pagination.value.lastPage) return
  fetchData();
}
const column = roleColumns({
    hasEditPermission : hasEditPermission,
    onEdit : onEdit,
    onDelete : onDelete
    });
</script>
<template>
    <Head title="Manage Roles" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
                <DataTable :columns="column" :data="roles" />
            </div>
            <div v-if="pagination" class="flex justify-end">
                <Pagination
                  v-slot="{ page }"
                  :items-per-page="10"
                  :total="pagination.total"
                  :default-page="pagination.currentPage"
                >
                  <PaginationContent v-slot="{ items }">
                    <PaginationPrevious
                      :disabled="pagination.currentPage === 1"
                      @click="goToPage(pagination.currentPage - 1)"
                    />

                    <template v-for="(item, index) in items" :key="index">
                      <PaginationItem
                        v-if="item.type === 'page'"
                        :value="item.value"
                        :is-active="item.value === pagination.currentPage"
                        @click="goToPage(item.value)"
                      >
                        {{ item.value }}
                      </PaginationItem>
                    </template>

                    <PaginationEllipsis v-if="pagination.lastPage > 5" />

                    <PaginationNext
                      :disabled="pagination.currentPage === pagination.lastPage"
                      @click="goToPage(pagination.currentPage + 1)"
                    />
                  </PaginationContent>
                </Pagination>
            </div>
        </div>
    </AppLayout>
</template>
