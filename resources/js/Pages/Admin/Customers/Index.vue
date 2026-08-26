<template>
    <AdminLayout
        title="Customers"
        subtitle="Manage your customers and monitor their activity."
    >

        <!-- ================================= -->
        <!-- Statistics -->
        <!-- ================================= -->

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mt-4">

            <StatCard
                title="Total Customers"
                :value="stats.total"
                :icon="Users"
                color="blue"
            />

            <StatCard
                title="Verified"
                :value="stats.verified"
                :icon="BadgeCheck"
                color="green"
            />

            <StatCard
                title="New This Month"
                :value="stats.newThisMonth"
                :icon="UserPlus"
                color="purple"
            />

            <StatCard
                title="Unverified"
                :value="stats.unverified"
                :icon="UserX"
                color="red"
            />

        </div>



        <!-- ================================= -->
        <!-- Filters -->
        <!-- ================================= -->

        <div class="flex flex-col md:flex-row justify-between gap-4 mt-6">

            <!-- Search -->

            <div class="relative w-full md:w-96">

                <Search
                    class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"
                    :size="18"
                />

                <input
                    v-model="search"
                    @input="applyFilters"
                    type="text"
                    placeholder="Search customers..."
                    class="w-full
                           pl-10 pr-4 py-2.5
                           rounded-xl
                           border border-gray-200
                           bg-white
                           shadow-sm
                           focus:ring-2
                           focus:ring-blue-500
                           focus:border-blue-500"
                />

            </div>

        </div>



        <!-- ================================= -->
        <!-- Table -->
        <!-- ================================= -->

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">

            <table class="w-full">

                <thead class="bg-gray-50">

                    <tr class="text-left text-gray-500 text-sm">

                        <th class="px-6 py-4">Customer</th>

                        <th>Email</th>

                        <th>Verified</th>

                        <th>Joined</th>

                        <th class="text-right pr-6">Action</th>

                    </tr>

                </thead>

                <tbody>

                    <tr
                        v-for="customer in customers.data"
                        :key="customer.id"
                        class="border-t hover:bg-gray-50 transition"
                    >

                        <!-- Avatar -->

                        <td class="px-6 py-4">

                            <div class="flex items-center gap-4">

                                <div
                                    class="w-10 h-10 rounded-full
                                           bg-gradient-to-br
                                           from-blue-500
                                           to-indigo-600
                                           flex items-center justify-center
                                           text-white font-semibold"
                                >
                                    {{ customer.name.charAt(0).toUpperCase() }}
                                </div>

                                <div>

                                    <p class="font-semibold text-gray-800">
                                        {{ customer.name }}
                                    </p>

                                </div>

                            </div>

                        </td>

                        <!-- Email -->

                        <td>

                            <span class="text-gray-600">
                                {{ customer.email }}
                            </span>

                        </td>

                        <!-- Verified -->

                        <td>

                            <span
                                v-if="customer.email_verified_at"
                                class="px-3 py-1 rounded-full text-xs
                                       bg-green-100 text-green-700"
                            >
                                Verified
                            </span>

                            <span
                                v-else
                                class="px-3 py-1 rounded-full text-xs
                                       bg-red-100 text-red-600"
                            >
                                Unverified
                            </span>

                        </td>

                        <!-- Joined -->

                        <td class="text-gray-500">

                           {{ new Date(customer.created_at).toLocaleDateString('en-GB', {
                                day: '2-digit',
                                month: 'short',
                                year: 'numeric'
                            }) }}

                        </td>

                        <!-- Action -->

                        <td class="text-right pr-6">

                            <Link
                                :href="route('admin.users.show', customer.id)"
                                class="inline-flex
                                       items-center
                                       justify-center
                                       w-10
                                       h-10
                                       rounded-xl
                                       border
                                       border-gray-200
                                       text-blue-600
                                       hover:bg-blue-50
                                       transition"
                            >

                                <Eye class="w-5 h-5"/>

                            </Link>

                        </td>

                    </tr>

                </tbody>

            </table>



            <!-- Pagination -->

            <div class="flex justify-center p-6">

                <button
                    v-for="(link,index) in customers.links"
                    :key="index"
                    v-html="link.label"
                    @click="goTo(link.url)"
                    :disabled="!link.url"
                    class="px-3 py-2 border rounded-lg mx-1"
                    :class="{
                        'bg-blue-600 text-white':link.active,
                        'text-gray-400':!link.url
                    }"
                />

            </div>

        </div>

    </AdminLayout>

</template>

<script setup>

import AdminLayout from '@/Layouts/AdminLayout.vue'
import StatCard from '@/Components/Dashboard/StatCard.vue'

import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

import {
    Users,
    BadgeCheck,
    UserPlus,
    UserX,
    Search,
    Eye
} from 'lucide-vue-next'

const props = defineProps({
    customers: Object,
    stats: Object,
    filters: Object,
})

const search = ref(props.filters.search || '')

const applyFilters = () => {

    router.get(route('admin.users.index'), {
        search: search.value,
    }, {
        preserveState: true,
        replace: true,
    })

}

const goTo = (url) => {

    if (!url) return

    router.visit(url, {
        preserveState: true,
    })

}

</script>