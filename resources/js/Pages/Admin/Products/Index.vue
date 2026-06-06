<template>
    <AdminLayout>

        <!-- HEADER -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Products</h1>

            <Link 
                href="/admin/products/create"
                class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 transition"
            >
                + Add Product
            </Link>
        </div>

        <!-- FILTER BAR -->
        <div class="flex flex-col md:flex-row gap-4 mb-4 justify-between">

            <!-- Search -->
           <div class="relative w-full md:w-64">

    <!-- Icon -->
    <Search 
        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" 
        size="18"
        color="black"
    />

    <!-- Input -->
    <input
        v-model="search"
        @input="applyFilters"
        type="text"
        placeholder="Search products..."
        class="border pl-10 pr-3 py-2 rounded-lg w-full"
    />

</div>

            <!-- Category Filter -->
            <select
                v-model="category"
                @change="applyFilters"
                class="border px-3 py-2 rounded-lg w-full md:w-48"
            >
                <option value="">All Categories</option>
                <option 
                    v-for="cat in categories" 
                    :key="cat.id" 
                    :value="cat.id"
                >
                    {{ cat.name }}
                </option>
            </select>

        </div>

        <!-- TABLE -->
        <div class="bg-white p-6 rounded-xl shadow">

            <table class="w-full text-sm text-gray-700">

                <!-- HEAD -->
                <thead class="bg-gray-100 text-xs uppercase text-gray-600">
                    <tr>
                        <th class="p-3 text-left">Product</th>
                        <th class="p-3 text-left">Price</th>
                        <th class="p-3 text-left">Stock</th>
                        <th class="p-3 text-left">Category</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Actions</th>
                    </tr>
                </thead>

                <!-- BODY -->
                <tbody>
                    <tr 
                        v-for="product in products.data" 
                        :key="product.id"
                        class="border-b hover:bg-gray-50 transition"
                    >

                        <!-- Product -->
                        <td class="p-3 flex items-center gap-3">
                            <img 
                                v-if="product.images.length"
                                :src="`/storage/${product.images[0].image_path}`"
                                class="w-10 h-10 object-cover rounded"
                            />
                            <span>{{ product.name }}</span>
                        </td>

                        <!-- Price -->
                        <td class="p-3">
                            Ksh {{ product.price }}
                        </td>

                        <!-- Stock -->
                        <td class="p-3">
                            <span 
                                :class="product.stock_quantity > 0 ? 'text-green-600' : 'text-red-500'"
                            >
                                {{ product.stock_quantity }}
                            </span>
                        </td>

                        <!-- Category -->
                        <td class="p-3">
                            {{ product.category?.name || '-' }}
                        </td>

                        <!-- Status -->
                        <td class="p-3">
                            <span 
                                :class="product.is_active 
                                    ? 'bg-green-100 text-green-600' 
                                    : 'bg-gray-200 text-gray-600'"
                                class="px-2 py-1 rounded text-xs"
                            >
                                {{ product.is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="p-3 flex gap-3 justify-center items-center">
                            <Link :href="route('admin.products.edit', product.id)" class="text-blue-500 hover:underline">
                                <Pencil size="20" />
                            </Link>

                            
                            <button 
                             @click="openDelete(product.id)"
                             class="text-red-500 hover:underline">
                                <Trash2 size="20" />
                            </button>
                        </td>

                    </tr>
                </tbody>

            </table>

            <!-- PAGINATION -->
            <div class="flex justify-center mt-6 space-x-1">

                <button
                    v-for="(link, index) in products.links"
                    :key="index"
                    v-html="link.label"
                    @click="goTo(link.url)"
                    :disabled="!link.url"
                    class="px-3 py-1 border rounded"
                    :class="{
                        'bg-blue-600 text-white': link.active,
                        'text-gray-400': !link.url
                    }"
                ></button>

            </div>




<!-- DELETE MODAL -->
<div v-if="showDeleteModal" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white p-6 rounded-xl w-full max-w-md">

        <h2 class="text-lg font-bold mb-4 text-red-600">
            Delete Product
        </h2>

        <p class="mb-6 text-gray-600">
            Are you sure you want to delete this product?
        </p>

        <div class="flex justify-end gap-3">

            <button 
                @click="showDeleteModal = false"
                class="px-4 py-2 bg-gray-300 rounded">
                Cancel
            </button>

            <button 
                @click="deleteProduct"
                class="px-4 py-2 bg-red-600 text-white rounded">
                Delete
            </button>

        </div>

    </div>
</div>







        </div>

    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import { Search } from 'lucide-vue-next'
import { Pencil } from 'lucide-vue-next'
import { Trash2 } from 'lucide-vue-next'
import { useForm } from '@inertiajs/vue3'

const deleteId = ref(null)
const showDeleteModal = ref(false)
const form = useForm({})

// Props
const props = defineProps({
    products: Object,
    filters: Object,
    categories: Array
})

// Filters state
const search = ref(props.filters.search || '')
const category = ref(props.filters.category || '')

// Apply filters
const applyFilters = () => {
    router.get('/admin/products', {
        search: search.value,
        category: category.value
    }, {
        preserveState: true,
        replace: true
    })
}

// Pagination navigation
const goTo = (url) => {
    if (!url) return

    router.visit(url, {
        preserveState: true
    })
}


// Open modal
const openDelete = (id) => {
    deleteId.value = id
    showDeleteModal.value = true
}



// Confirm delete
const deleteProduct = () => {
    form.delete(route('admin.products.destroy', deleteId.value), {
        onSuccess: () => {
            showDeleteModal.value = false
        }
    })
}


</script>