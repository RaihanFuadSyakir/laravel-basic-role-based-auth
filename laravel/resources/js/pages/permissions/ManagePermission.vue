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
} from "@/components/ui/pagination/index"
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
                <div class="flex justify-center">
                    <Input type="text" class="flex-1 m-2" placeholder="filter by name" @input="(e : any) => addFilter('name',e.target.value)"/>
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
