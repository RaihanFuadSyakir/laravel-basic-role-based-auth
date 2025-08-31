<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { AppPageProps, type BreadcrumbItem } from '@/types';
import { Head, router, usePage } from '@inertiajs/vue3';
import { permissionColumns } from '@/components/datatables/permissions_column';
import DataTable from '@/components/datatables/DataTable.vue';
import { computed, ref } from 'vue';
import {
  Pagination,
  PaginationContent,
  PaginationEllipsis,
  PaginationItem,
  PaginationNext,
  PaginationPrevious,
} from "@/components/ui/pagination/index";
import { Filter } from 'lucide-vue-next';
import Input from '@/components/ui/input/Input.vue';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Manage Permissions',
        href: '/manage_permissions',
    },
];
const page = usePage<AppPageProps & {
 data : {
  permissions : {
   name : string
   created_at : Date
  }
 },
 pagination: {
  total: number,
  currentPage: number,
  lastPage: number
 }}>();
const permissions = computed(() => page.props.data.permissions);
const pagination = computed(() => page.props.pagination);
const filters = ref<Record<string, string | string[]>>({});
function fetchData(){
   router.get('/manage_permissions',
        { page : pagination.value.currentPage, ...filters.value },
        { preserveState: true, preserveScroll: true }
    )
}
function addFilter(key: string, value: string | string[]) {
  if (Array.isArray(value)) {
    filters.value = { ...filters.value, [key]: value.join(',') };
  } else {
    filters.value = { ...filters.value, [key]: value };
  }
  fetchData();
}
const goToPage = (page: number) => {
  if (page < 1 || page > pagination.value.lastPage) return
  fetchData();
}
</script>
<template>
    <Head title="Manage Permissions" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div class="relative flex items-center m-2">
                  <!-- Icon inside input -->
                  <Filter class="absolute left-2 size-4 opacity-50 pointer-events-none" />

                  <!-- Input with padding so text doesn't overlap icon -->
                  <Input
                    type="text"
                    placeholder="Filter by name"
                    class="pl-8"
                    @input="(e: any) => addFilter('name', e.target.value)"
                  />
                </div>
            </div>
            <div class="flex flex-1 flex-col gap-4 rounded-xl overflow-x-auto mb-2">
                <DataTable :columns="permissionColumns" :data="permissions" />
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
