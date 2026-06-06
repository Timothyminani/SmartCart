<template>
    <AdminLayout>

        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">Categories</h1>

            <button 
                 @click="openCreate"
                class="bg-blue-600 text-white px-4 py-2 rounded">
                + Add Category
            </button>
        </div>

        <!-- Table -->
        <div class="bg-white p-6 rounded-xl shadow">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-for="cat in categories" :key="cat.id" class="border-b">
                        <td class="py-2">{{ cat.name }}</td>
                                    <td class="py-2 flex gap-3">
                    <button 
                        @click="openEdit(cat)"
                        class="text-blue-500">
                        <Pencil size="20" />
                    </button>

                    <button 
                         @click="openDelete(cat.id)"
                        class="text-red-500">
                         <Trash2 size="20" />
                    </button>
                </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </AdminLayout>


<!-- Modal -->
<div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white w-full max-w-md p-6 rounded-xl shadow-lg">

        <!-- Title -->
        <h2 class="text-xl font-bold mb-4">
             {{ isEdit ? 'Edit Category' : 'Add Category' }}
        </h2>

        <!-- Form -->
        <form @submit.prevent="submit">

            <div class="mb-4">
                <label class="block text-sm mb-1">Name</label>
                <input 
                    v-model="form.name"
                    type="text"
                    class="w-full border p-2 rounded"
                />

                <div v-if="form.errors.name" class="text-red-500 text-sm">
                    {{ form.errors.name }}
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-2">

                <button 
                    type="button"
                    @click="showModal = false"
                    class="px-4 py-2 bg-gray-300 rounded">
                    Cancel
                </button>

                <button 
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded">
                    {{ isEdit ? 'Update' : 'Create' }}
                </button>

            </div>

        </form>

    </div>
</div>




 <!-- Delete Modal -->

<div v-if="showDeleteModal" 
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

    <div class="bg-white p-6 rounded-xl w-full max-w-md">

        <h2 class="text-lg font-bold mb-4 text-red-600">
            Delete Category
        </h2>

        <p class="mb-6 text-gray-600">
            Are you sure you want to delete this category? This action cannot be undone.
        </p>

        <div class="flex justify-end gap-3">

            <button 
                @click="showDeleteModal = false"
                class="px-4 py-2 bg-gray-300 rounded">
                Cancel
            </button>

            <button 
                @click="deleteCategory"
                class="px-4 py-2 bg-red-600 text-white rounded">
                Delete
            </button>

        </div>

    </div>
</div>



</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Pencil } from 'lucide-vue-next'
import { Trash2 } from 'lucide-vue-next'

defineProps({
    categories: Array
})



// Modal control
const showModal = ref(false)
const isEdit = ref(false)
const selectedId = ref(null)
const showDeleteModal = ref(false)
const deleteId = ref(null)

// Form (Inertia)
const form = useForm({
    name: ''
})


const openCreate = () => {
    isEdit.value = false
    form.reset()
    showModal.value = true
}


const openEdit = (cat) => {
    isEdit.value = true
    selectedId.value = cat.id

    form.name = cat.name

    showModal.value = true
}




// Submit function
const submit = () => {
    if (isEdit.value) {
        form.put(`/admin/categories/${selectedId.value}`, {
            onSuccess: () => {
                closeModal()
            }
        })
    } else {
        form.post('/admin/categories', {
            onSuccess: () => {
                closeModal()
            }
        })
    }
}


const closeModal = () => {
    form.reset()
    showModal.value = false
    isEdit.value = false
    selectedId.value = null
}

const openDelete = (id) => {
    deleteId.value = id
    showDeleteModal.value = true
}

const deleteCategory = () => {
    form.delete(`/admin/categories/${deleteId.value}`, {
        onSuccess: () => {
            showDeleteModal.value = false
        }
    })
}




</script>