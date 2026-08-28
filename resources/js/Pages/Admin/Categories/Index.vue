<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import {
    Pencil,
    Trash2,
    Image as ImageIcon,
} from 'lucide-vue-next'

import AdminLayout from '@/Layouts/AdminLayout.vue'


/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
})


/*
|--------------------------------------------------------------------------
| Modal State
|--------------------------------------------------------------------------
*/

const showModal = ref(false)
const showDeleteModal = ref(false)

const isEdit = ref(false)
const selectedId = ref(null)
const deleteId = ref(null)

const imagePreview = ref(null)


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = useForm({
    name: '',
    image: null,
    is_featured: false,
})


/*
|--------------------------------------------------------------------------
| Create
|--------------------------------------------------------------------------
*/

const openCreate = () => {
    isEdit.value = false
    selectedId.value = null
    imagePreview.value = null

    form.reset()
    form.clearErrors()

    showModal.value = true
}


/*
|--------------------------------------------------------------------------
| Edit
|--------------------------------------------------------------------------
*/

const openEdit = (cat) => {
    isEdit.value = true
    selectedId.value = cat.id

    form.clearErrors()

    form.name = cat.name
    form.image = null
    form.is_featured = Boolean(cat.is_featured)

    imagePreview.value = cat.image
        ? `/storage/${cat.image}`
        : null

    showModal.value = true
}


/*
|--------------------------------------------------------------------------
| Image
|--------------------------------------------------------------------------
*/

const handleImage = (event) => {
    const file = event.target.files?.[0]

    if (!file) {
        form.image = null
        return
    }

    form.image = file

    imagePreview.value = URL.createObjectURL(file)
}


/*
|--------------------------------------------------------------------------
| Submit
|--------------------------------------------------------------------------
*/

const submit = () => {
    if (isEdit.value) {

        form
            .transform((data) => ({
                ...data,
                _method: 'PUT',
            }))
            .post(
                `/admin/categories/${selectedId.value}`,
                {
                    forceFormData: true,

                    onSuccess: () => {
                        closeModal()
                    },

                    onFinish: () => {
                        form.transform((data) => data)
                    },
                }
            )

        return
    }


    form.post('/admin/categories', {
        forceFormData: true,

        onSuccess: () => {
            closeModal()
        },
    })
}


/*
|--------------------------------------------------------------------------
| Close Modal
|--------------------------------------------------------------------------
*/

const closeModal = () => {
    form.reset()
    form.clearErrors()

    imagePreview.value = null
    showModal.value = false
    isEdit.value = false
    selectedId.value = null
}


/*
|--------------------------------------------------------------------------
| Delete
|--------------------------------------------------------------------------
*/

const openDelete = (id) => {
    deleteId.value = id
    showDeleteModal.value = true
}


const deleteCategory = () => {
    form.delete(
        `/admin/categories/${deleteId.value}`,
        {
            onSuccess: () => {
                showDeleteModal.value = false
                deleteId.value = null
            },
        }
    )
}
</script>




<template>
    <AdminLayout
        title="Categories"
        subtitle="Manage all categories in your store"
    >
        <!-- Header -->
        <div class="flex justify-between mb-6">
            <h1 class="text-2xl font-bold">
                Categories
            </h1>

            <button
                @click="openCreate"
                class="bg-blue-600 text-white px-4 py-2 rounded"
            >
                + Add Category
            </button>
        </div>


        <!-- ========================================================= -->
        <!-- TABLE -->
        <!-- ========================================================= -->

        <div class="bg-white p-6 rounded-xl shadow overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b">
                        <th class="pb-3">
                            Image
                        </th>

                        <th class="pb-3">
                            Name
                        </th>

                        <th class="pb-3">
                            Homepage
                        </th>

                        <th class="pb-3">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <tr
                        v-for="cat in categories"
                        :key="cat.id"
                        class="border-b"
                    >
                        <!-- Image -->
                        <td class="py-3">
                            <div
                                class="w-14 h-14 rounded-lg border bg-gray-50
                                       flex items-center justify-center
                                       overflow-hidden"
                            >
                                <img
                                    v-if="cat.image"
                                    :src="`/storage/${cat.image}`"
                                    :alt="cat.name"
                                    class="w-full h-full object-contain"
                                />

                                <ImageIcon
                                    v-else
                                    class="w-5 h-5 text-gray-400"
                                />
                            </div>
                        </td>


                        <!-- Name -->
                        <td class="py-3 font-medium text-gray-700">
                            {{ cat.name }}
                        </td>


                        <!-- Featured -->
                        <td class="py-3">
                            <span
                                v-if="cat.is_featured"
                                class="inline-flex px-2.5 py-1
                                       rounded-full
                                       bg-green-100
                                       text-green-700
                                       text-xs font-medium"
                            >
                                Featured
                            </span>

                            <span
                                v-else
                                class="inline-flex px-2.5 py-1
                                       rounded-full
                                       bg-gray-100
                                       text-gray-500
                                       text-xs font-medium"
                            >
                                Not featured
                            </span>
                        </td>


                        <!-- Actions -->
                        <td class="py-3">
                            <div class="flex gap-3">
                                <button
                                    @click="openEdit(cat)"
                                    type="button"
                                    class="text-blue-500 hover:text-blue-700"
                                >
                                    <Pencil :size="20" />
                                </button>

                                <button
                                    @click="openDelete(cat.id)"
                                    type="button"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <Trash2 :size="20" />
                                </button>
                            </div>
                        </td>
                    </tr>


                    <!-- Empty state -->
                    <tr v-if="categories.length === 0">
                        <td
                            colspan="4"
                            class="py-10 text-center text-gray-500"
                        >
                            No categories found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>


    <!-- ========================================================= -->
    <!-- CREATE / EDIT MODAL -->
    <!-- ========================================================= -->

    <div
        v-if="showModal"
        class="fixed inset-0
               bg-black bg-opacity-50
               flex items-center justify-center
               z-50
               p-4"
    >
        <div
            class="bg-white
                   w-full max-w-md
                   p-6
                   rounded-xl
                   shadow-lg"
        >
            <!-- Title -->
            <h2 class="text-xl font-bold mb-5">
                {{ isEdit ? 'Edit Category' : 'Add Category' }}
            </h2>


            <!-- Form -->
            <form @submit.prevent="submit">

                <!-- Name -->
                <div class="mb-5">
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Category Name
                    </label>

                    <input
                        v-model="form.name"
                        type="text"
                        class="w-full border
                               px-3 py-2
                               rounded-lg
                               focus:outline-none
                               focus:ring-2
                               focus:ring-blue-500"
                        placeholder="e.g. Phones"
                    />

                    <div
                        v-if="form.errors.name"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.name }}
                    </div>
                </div>


                <!-- Image -->
                <div class="mb-5">
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1"
                    >
                        Category Image
                    </label>

                    <!-- Existing / selected image preview -->
                    <div
                        v-if="imagePreview"
                        class="mb-3
                               w-full h-36
                               border rounded-lg
                               bg-gray-50
                               flex items-center justify-center
                               overflow-hidden"
                    >
                        <img
                            :src="imagePreview"
                            alt="Category preview"
                            class="w-full h-full object-contain"
                        />
                    </div>

                    <input
                        type="file"
                        accept="image/png,image/jpeg,image/webp"
                        @change="handleImage"
                        class="block w-full
                               text-sm text-gray-600
                               border rounded-lg
                               p-2"
                    />

                    <p
                        v-if="isEdit"
                        class="text-xs text-gray-500 mt-1"
                    >
                        Leave empty to keep the current image.
                    </p>

                    <div
                        v-if="form.errors.image"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.image }}
                    </div>
                </div>


                <!-- Featured -->
                <div class="mb-6">
                    <label
                        class="flex items-center gap-3 cursor-pointer"
                    >
                        <input
                            v-model="form.is_featured"
                            type="checkbox"
                            class="w-4 h-4
                                   rounded
                                   border-gray-300
                                   text-blue-600
                                   focus:ring-blue-500"
                        />

                        <div>
                            <p class="text-sm font-medium text-gray-700">
                                Show on homepage
                            </p>

                            <p class="text-xs text-gray-500">
                                Display this category in Popular Categories.
                            </p>
                        </div>
                    </label>

                    <div
                        v-if="form.errors.is_featured"
                        class="text-red-500 text-sm mt-1"
                    >
                        {{ form.errors.is_featured }}
                    </div>
                </div>


                <!-- Buttons -->
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="closeModal"
                        class="px-4 py-2
                               bg-gray-200
                               text-gray-700
                               rounded-lg"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-4 py-2
                               bg-blue-600
                               text-white
                               rounded-lg
                               disabled:opacity-50"
                    >
                        {{
                            form.processing
                                ? 'Saving...'
                                : isEdit
                                    ? 'Update'
                                    : 'Create'
                        }}
                    </button>
                </div>

            </form>
        </div>
    </div>


    <!-- ========================================================= -->
    <!-- DELETE MODAL -->
    <!-- ========================================================= -->

    <div
        v-if="showDeleteModal"
        class="fixed inset-0
               bg-black bg-opacity-50
               flex items-center justify-center
               z-50
               p-4"
    >
        <div
            class="bg-white
                   p-6
                   rounded-xl
                   w-full max-w-md"
        >
            <h2 class="text-lg font-bold mb-4 text-red-600">
                Delete Category
            </h2>

            <p class="mb-6 text-gray-600">
                Are you sure you want to delete this category?
                This action cannot be undone.
            </p>

            <div class="flex justify-end gap-3">
                <button
                    type="button"
                    @click="showDeleteModal = false"
                    class="px-4 py-2 bg-gray-200 rounded-lg"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    @click="deleteCategory"
                    :disabled="form.processing"
                    class="px-4 py-2
                           bg-red-600
                           text-white
                           rounded-lg
                           disabled:opacity-50"
                >
                    Delete
                </button>
            </div>
        </div>
    </div>
</template>


