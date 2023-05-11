<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    bills: {
        type: Object,
        default: () => ({}),
    }
});

</script>

<template>
    <AppLayout title="House Bills">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                House Bills
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-200">
                            <tr >
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-bold text-left text-gray-700 uppercase border"
                                >Title</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-bold text-left text-gray-700 uppercase border"
                                >Description</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-bold text-left text-gray-700 uppercase border"
                                >URL</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-bold text-left text-gray-700 uppercase border"
                                >Status Update Date</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-bold text-left text-gray-700 uppercase border"
                                >Status Body</th>
                                <th scope="col"
                                    class="px-6 py-3 text-xs font-bold text-left text-gray-700 uppercase border"
                                >Status Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="bills.data.length" v-for="bill in bills.data" :key="bills.id">
                                <td class="px-6 py-4 text-sm font-medium text-gray-800 whitespace border">{{ bill.title }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800 whitespace border">{{ bill.description }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800 whitespace border">{{ bill.url }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800 whitespace-nowrap border">{{ bill.status_date }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800 whitespace border">{{ bill.status_body }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-800 whitespace border">{{ bill.status_detail }}</td>
                            </tr>
                            <tr v-else>
                                <td>
                                    No data to display
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="mt-4 ml-4 mb-4">
                        <Pagination :links="bills.links"></Pagination>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
